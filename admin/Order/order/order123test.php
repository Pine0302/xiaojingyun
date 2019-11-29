<?php

	header("Content-type: text/html; charset=utf-8");
	require('../../../../weixinpl/config.php');   //配置
	require('../../../../weixinpl/customer_id_decrypt.php');   //解密参数

	require('../../../../weixinpl/back_init.php');

	$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');
	_mysql_query("SET NAMES UTF8");

	require('../../../../weixinpl/proxy_info.php');
	require('../../../../weixinpl/common/utility.php');

	require('../../../../weixinpl/mshop/tax_function.php');		 //税收方法文件

	include_once("../../../../weixinpl/log_.php");

    include_once('../../../../weixinpl/common/common_ext.php');

	$log_ = new Log_();
	$log_name="notify_url_order".date("Y-m-d").".log";//log文件路径

	$to_day = new to_day();
	
	$img_utility = new ImgurlUtiliy();

	$isauto = 0;
	if(!empty($_GET["isauto"])){
	   $isauto = $configutil->splash_new($_GET["isauto"]);
	}

    $from_page      = i2get("from_page",0); //页面来源 ； 0 ：商城 ； 1：订货系统 ； 2 ： f2c
    $is_sendorder   = $from_page > 0 ? '=1' : '!=1'; //是否查询派单订单
    $param_store_id = i2get("store_id",0); //订货系统中的仓库编号

    /* 读取F2C运费设置 Start  */
    if($from_page == 0){
        //查询默认运费标准
        $normal_query = "SELECT * from f2c_order_reject_freight where id = {$customer_id}";
        $result = _mysql_query($normal_query) or die('query_supply failed: ' . mysql_error());
        $f2c_freight = array();
        while ($row= mysql_fetch_assoc($result)) {
            $f2c_freight[] = $row;
        }
    }
    /* 读取F2C运费设置 End  */

    //微信支付版本号
	$wxpay_version=1;
	$query_ver = "select version from pay_config where isvalid=true and customer_id=".$customer_id." and pay_type = 'weipay' limit 1";
	$result_ver = _mysql_query($query_ver) or die('Query_ver failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result_ver)) {
		$wxpay_version = $row->version;
	}
	 //微信支付版本号 End


	/* 订单提醒按钮 */
	$is_remind = 0;
	$query_remind = "select order_remind from weixin_commonshop_orderremind where isvalid=true and customer_id=".$customer_id." limit 1";
	$result_remind = _mysql_query($query_remind) or die('Query_is_remind failed: ' . mysql_error());
	while ($row_remind = mysql_fetch_object($result_remind)) {
	   $is_remind = $row_remind->order_remind;
	}
	/* 订单提醒按钮 End	*/


	/* 从右下角提醒图片进订单管理，更新订单支付总数 */

	if(!empty($_GET["up_order"])){
		$up_order = $configutil->splash_new($_GET["up_order"]);
		$ordercount=-1;
		$sql_ordercount="select count(1) as ordercount from weixin_commonshop_orders where customer_id=".$customer_id." and isvalid=true and paystatus=1";
		$re=_mysql_query($sql_ordercount) or die('Query sql_ordercount: '.mysql_error());
		while ($ro = mysql_fetch_object($re)) {
			$ordercount= $ro->ordercount;
		}
		if($ordercount>0){
			$query="update weixin_commonshop_orderremind set order_count=".$ordercount.",last_record=".$ordercount." where isvalid=true and customer_id=".$customer_id;
			_mysql_query($query) or die('Query failed3_weixin_commonshop_orderremind: ' . mysql_error());
		}
	}
	/* 从右下角提醒图片进订单管理，更新订单支付总数  End*/

	/* 4M分销 -- 是否总店  */
	$is_generalcustomer = 1;
	$is_shopgeneral = 0;

	$adminuser_id=-1;  //总部模板才添加
	$query_admin = "select adminuser_id from customers where isvalid=true and id=".$customer_id." limit 1";
	$result_admin = _mysql_query($query_admin) or die('Query_admin failed: ' . mysql_error());
	while ($row_admin = mysql_fetch_object($result_admin)) {
	   $adminuser_id = $row_admin->adminuser_id;
	}
	while($adminuser_id>0){
		$channel_level_id = -1;
		$query_level = "select channel_level_id,parent_id from adminusers where isvalid=true and id=".$adminuser_id;
		$result_level = _mysql_query($query_level) or die('Query_level failed: ' . mysql_error());
		while ($row_level = mysql_fetch_object($result_level)) {
			$channel_level_id = $row_level->channel_level_id;
			$parent_id2 = $row_level->parent_id;
		}
	    if($channel_level_id==5){
			//找到贴牌
			$query_oem = "select is_shopgeneral from oem_infos where isvalid=true and adminuser_id=".$adminuser_id." limit 1";
			$result_oem = _mysql_query($query_oem) or die('Query_oem failed: ' . mysql_error());
			while ($row_oem = mysql_fetch_object($result_oem)) {
				$is_shopgeneral = $row_oem->is_shopgeneral;
			}
			break;
	    }else{
			$adminuser_id = $parent_id2;
			$is_generalcustomer = 0;
	    }
	}
	/*  4M分销 -- 是否总店 End */


	//订单查询


	$o_id             	= -1;	//编号
	$o_batchcode      	= -1;	//订单号
	$o_name           	= "";	//名称
	$o_weixin_name    	= "";	//微信名称
	$o_phone          	= "";	//手机号
	$o_remark         	= "";	//订单备注
	$o_paystyle       	= 0;	//支付方式 0：微信支付；1：支付宝；2：通联支付
	$o_totalprice     	= 0;  //合计总价(包括运费)
	$o_real_pay       	= 0;  //实付金额
	$o_freight        	= 0;	//运费
	$o_is_change      	= 0;	//改价标志
	$o_paystatus      	= 0;	//支付状态 0:未支付 1:已支付 -1:支付失败
	$o_createtime     	= "1970-00-00 00:00:00";
	$o_transaction_id 	= "无支付单号";	 //支付单号

	$o_expressName    	= "";     //下单名称
	$o_expressPhone   	= "";		//下单手机号
	$o_Pay_Method 		= 0;		//系统支付方式  0:默认支付;  1:后台手动支付
	$o_status 			= 0;	//订单状态。-1：取消订单；0：未发货；  1：发货；2：客户收货确定完成；3：商家确定完成订单；4：退货；5：驳回退货；6：退货完成；7：换货完成；8：退款；9：退款完成；10：驳回退款；11：客户发退货物流
	$o_exp_user_id 		= -1;	//推广员
	$o_expressAddress = "";  //收货地址
	$o_identity 		= "";  //收货身份证
	$identityimgt 		= "";  //身份证正面
	$identityimgf 		= "";  //身份证反面
	$o_paytime 			= "";  //支付时间
	$o_pageAmount 		= 0;
	$o_agent_id 		= -1;
	$o_supply_id 		= -1;
    $o_agentcont_type 	= 0;
	$o_aftersale_type 	= 0;
	$o_is_QR 			= 0;
	$o_expressnum 		= "";  //快递单号
	$new_expressname	= "";  //快递公司
	$weipay_style		= 0; // 0:不是微信支付/找人代付 1:微信支付/找人代付
	$pay_batchcode		= "";//支付订单号
	$o_store_id			= "";//门店自提ID
	$o_store_name		= "";//门店自提店铺
	$o_isreducesupply	= 0; //维权是否扣除供应商款项
	$o_sendtime			= '';//送货时间
	$o_send_remarks	    = '';//送货备注
	$o_is_sendorder	    = 0; //是否零售派单订单
	/*$query_order = "SELECT
						orders.pay_batchcode,
						orders.id,
						orders.user_id,
						orders.batchcode,
						orders.createtime,
						orders.paystyle,
						sum(orders.totalprice) as  totalprice,
						orders.paystatus,
						orders.sendstatus,
						orders.status,
						orders.exp_user_id,
						orders.supply_id,
						orders.allipay_orderid,
						orders.is_delay,
						orders.store_id,
						orders.return_type,
						orders.confirm_sendtime,
						orders.confirm_receivetime,
						orders.sendtime,
						orders.printUrl,
						orders.paytime,
						orders.agent_id,
						orders.Pay_Method,
						orders.remark,
						orders.supply_id,
						orders.express_id,
						orders.sendway,
						orders.agentcont_type,
						orders.auto_receivetime,
						orders.aftersale_type,
						orders.aftersale_state,
						orders.return_status,
						orders.return_account,
						orders.is_QR,
						orders.expressnum,
						orders.expressname,
						orders.sendstyle,
						orders.backgoods_reason,
						orders.store_id,
						orders.store_name,
						orders.isreducesupply,
						orders.send_remarks,
						orders.delivery_time_start,
						orders.delivery_time_end,
						orders.is_collageActivities,
						users.name,
						users.weixin_name,
						users.weixin_fromuser,
						users.phone,
						addr.name as expressName,
						addr.phone as expressPhone,
						addr.location_p,
						addr.location_c,
						addr.location_a,
						addr.address as expressAddress,
						addr.identity,
						addr.identityimgt,
						addr.identityimgf
		FROM weixin_commonshop_orders as orders
		LEFT JOIN weixin_users as users ON users.id = orders.user_id
		INNER JOIN weixin_commonshop_order_addresses as addr ON orders.batchcode = addr.batchcode";

	$query_search = " WHERE orders.customer_id = ".$customer_id;*/

	$query_order = "SELECT DISTINCT(orders.batchcode),
						orders.pay_batchcode,
						orders.id,
						orders.user_id,
						orders.batchcode,
						orders.createtime,
						orders.paystyle,
						orders.totalprice,
						orders.paystatus,
						orders.sendstatus,
						orders.status,
						orders.exp_user_id,
						orders.supply_id,
						orders.allipay_orderid,
						orders.is_delay,
						orders.store_id,
						orders.return_type,
						orders.confirm_sendtime,
						orders.confirm_receivetime,
						orders.sendtime,
						orders.printUrl,
						orders.paytime,
						orders.agent_id,
						orders.Pay_Method,
						orders.remark,
						orders.supply_id,
						orders.express_id,
						orders.sendway,
						orders.agentcont_type,
						orders.auto_receivetime,
						orders.aftersale_type,
						orders.aftersale_state,
						orders.return_status,
						orders.return_account,
						orders.is_QR,
						orders.expressnum,
						orders.expressname,
						orders.sendstyle,
						orders.backgoods_reason,
						orders.store_id,
						orders.store_name,
						orders.isreducesupply,
						orders.send_remarks,
						orders.delivery_time_start,
						orders.delivery_time_end,
						orders.is_collageActivities,
						orders.mb_order,
						orders.aftersale_time,
						orders.is_pay_on_delivery,
						orders.is_sendorder,
						orders.is_sign
						";
            if($from_page > 0){
                $query_order .= " , sso.store_id as sso_store_id,sso.current_proxy_id as sso_proxy_id,sso.is_accept as sso_is_accept";
            }
            $query_order .=" FROM weixin_commonshop_orders AS orders";

	$orgin_type = -1;
	if( !empty( $_GET["orgin_type"] ) ){
		$orgin_type = $_GET["orgin_type"];
		if( $orgin_type == 1 ){
			$query_order .= ' INNER JOIN cashback_t AS cash on cash.batchcode=orders.batchcode';
		}
	}
    if($from_page > 0){
        $query_order .= ' INNER JOIN system_send_order AS sso on orders.batchcode = sso.order_id and sso.send_type='.$from_page;
    }
	$query_search = " WHERE orders.customer_id = ".$customer_id." AND is_sendorder ".$is_sendorder;
    if($from_page > 0){
        $query_order .= '  and sso.isvalid = true ';
        if($param_store_id > 0){
            $query_order .= '  and sso.store_id = '.$param_store_id;
        }
    }

	if( $orgin_type > 0 ){
		switch( $orgin_type ){
			case 1:
				$query_search .= " AND cash.isvalid=true  ";
			break;
			case 2:
				$query_search .= ' AND orders.is_collageActivities=1';
			break;
			case 3:
				$query_search .= ' AND orders.mb_order>0';
			break;
			case 4:
				$query_search .= ' AND orders.is_QR=1';
			break;
		}
	}

	$search_product_name = "";
	if(!empty($_GET["search_product_name"])){    //产品名称
		$search_product_name = $_GET["search_product_name"];
		/*$query_order .= " LEFT JOIN weixin_commonshop_products as pro ON orders.pid=pro.id ";
		$query_search .= " AND pro.name like '%".$search_product_name."%'";*/
		$query_spn = "SELECT id FROM weixin_commonshop_products WHERE name like '%".$search_product_name."%'";
		$query_search .= " AND orders.pid in (".$query_spn.")";
	}
	/* 搜索条件 */


	$begintime = "";
	if(!empty($_GET["begintime"])){  //下单时间
	   $begintime = $_GET["begintime"];
	   $query_search = $query_search." and UNIX_TIMESTAMP(orders.createtime)>=".strtotime($begintime);
	}

	$endtime = "";
	if(!empty($_GET["endtime"])){   //下单时间 End
	   $endtime = $_GET["endtime"];
	   $query_search = $query_search." and UNIX_TIMESTAMP(orders.createtime)<=".strtotime($endtime);
	}

	$pay_begintime = "";
	if(!empty($_GET["pay_begintime"])){  //支付时间
	   $pay_begintime = $_GET["pay_begintime"];
	   $query_search = $query_search." and UNIX_TIMESTAMP(orders.paytime)>=".strtotime($pay_begintime);
	}

	$pay_endtime = "";
	if(!empty($_GET["pay_endtime"])){   //支付时间 End
	   $pay_endtime = $_GET["pay_endtime"];
	   $query_search = $query_search." and UNIX_TIMESTAMP(orders.paytime)<=".strtotime($pay_endtime);
	}

	$search_batchcode = "";
	if(!empty($_GET["search_batchcode"])){    //订单号
	   $search_batchcode = $configutil->splash_new($_GET["search_batchcode"]);
	   $query_search = $query_search." and (orders.batchcode = '".$search_batchcode."' or orders.pay_batchcode = '".$search_batchcode."')";
	}

	$search_attribution_type = 0;
	if(!empty($_GET["search_attribution_type"])){    //订单所属分类  1、供应商 2、代理商
		 $search_attribution_type = $configutil->splash_new($_GET["search_attribution_type"]);
	}
	// $search_order_ascription = -1;
	// $search_agent_id = "";
	// if(!empty($_GET["search_agent_id"]) and $search_attribution_type==2){    //代理商ID
	   // $search_agent_id = $configutil->splash_new($_GET["search_agent_id"]);
		// $query_search .= " AND orders.agent_id=".$search_agent_id;
		// $search_order_ascription = $search_agent_id;
	// }

	// $search_supply_id        = "";


	// if(!empty($_GET["search_supply_id"]) and $search_attribution_type==1){ 	//订单供应商ID
	   // $search_supply_id = $configutil->splash_new($_GET["search_supply_id"]);
	   // $query_search = $query_search." and orders.supply_id=".$search_supply_id;
	   // $search_order_ascription = $search_supply_id;
	// }

	if(!empty($_GET["search_order_ascription"])){

	//订单所属
	   $search_order_ascription = $configutil->splash_new($_GET["search_order_ascription"]);
	    switch($search_order_ascription){
			case -1:
				 switch($search_attribution_type){
					case -1://平台订单
						$query_search = $query_search." and orders.supply_id<0 and orders.agent_id<0";
					break;
					case 1://供应商订单
						$query_search = $query_search." and orders.supply_id>0";
					break;
					case 2://代理商订单
						$query_search = $query_search." and orders.agent_id>0";
					break;
					default:
					break;
				 }
				break;
			default:
				 switch($search_attribution_type){
					case -1://平台订单
						$query_search = $query_search." and orders.supply_id<0 and orders.agent_id<0";
					break;
					case 1:
						$query_search = $query_search." and orders.supply_id=".$search_order_ascription;
					break;
					case 2:
						$query_search = $query_search." and orders.agent_id=".$search_order_ascription;
					break;
				 }
		}

	}

	$orgin_from = 0;
	if(!empty($_GET["orgin_from"])){    //订单所属
	   $orgin_from = $configutil->splash_new($_GET["orgin_from"]);
		switch($orgin_from){
			case 1:
				$query_search = $query_search." and orders.exp_user_id<0";
				break;
			case 2:
				$query_search = $query_search." and orders.exp_user_id>0";
				break;
			default:
				break;
		}
	}

	$search_name = "";
	$search_name_type = 1;
	if(!empty($_GET["search_name_type"])){     //名称类型
		(int)$search_name_type = $configutil->splash_new($_GET["search_name_type"]);
	}
	if(!empty($_GET["search_name"])){    //名称
	   $search_name = $configutil->splash_new($_GET["search_name"]);
		switch($search_name_type){
			case 1:
				// $query_search .= " AND users.weixin_name like '%".$search_name."%'";
				$query_sn = 'SELECT id FROM weixin_users WHERE weixin_name like "%'.$search_name.'%" AND customer_id='.$customer_id;
				$query_search .= " AND orders.user_id IN (".$query_sn.")";
			break;
			case 2:
				// $query_search .= " AND addr.name like '%".$search_name."%'";
				//$query_sn = 'SELECT batchcode FROM weixin_commonshop_order_addresses WHERE name like "%'.$search_name."%'";
				$query_sn = "SELECT batchcode FROM weixin_commonshop_order_addresses WHERE name like '%".$search_name."%'";
				$query_search .= " AND orders.batchcode IN (".$query_sn.")";
			break;
		}
	}



	$search_phone = "";
	if(!empty($_GET["search_phone"])){    //收货人电话
	   $search_phone = $configutil->splash_new($_GET["search_phone"]);
		// $query_search .= " AND addr.phone like '%".$search_phone."%'";
		$query_sp = 'SELECT batchcode FROM weixin_commonshop_order_addresses WHERE phone like"%'.$search_phone.'%"';
		$query_search .= " AND orders.batchcode IN (".$query_sp.")";
	}

	$search_paystyle = "";	//支付方式
	if(isset($_GET["search_paystyle"])){
	   $search_paystyle = $configutil->splash_new($_GET["search_paystyle"]);  //支付方式

	   if( $search_paystyle == "后台支付" ){
		    $query_search .= " AND orders.Pay_Method=1 ";
	   }else{
		    $query_search .= " AND orders.paystyle='".$search_paystyle."' AND orders.Pay_Method=0 ";
	   }
	}

	$search_paystatus = "";	//支付状态
	if(isset($_GET["search_paystatus"])){
	   (int)$search_paystatus = $configutil->splash_new($_GET["search_paystatus"]);  //支付状态 0:未支付 1:已支付
	   $query_search .= " AND orders.paystatus=".$search_paystatus;
	}

	$search_shop_id = "";	//门店号
	if(isset($_GET["search_shop_id"])){
	   (int)$search_shop_id = $configutil->splash_new($_GET["search_shop_id"]);
	   $query_search .= " AND orders.store_id='".$search_shop_id."'";
	}

	$search_user_id="";			//顾客编号
	if(!empty($_GET["user_id"])){
		(int)$search_user_id = $configutil->splash_new($_GET["user_id"]);
		$query_search .= " AND orders.user_id='".$search_user_id."'";
	}

	$search_pre_delivery_type = '';	//预配送订单
	if( !empty($_GET['search_pre_delivery_type']) ){
		(int)$search_pre_delivery_type = $configutil->splash_new($_GET["search_pre_delivery_type"]);
		$current_date = date('Y-m-d',time());
		$current_time = strtotime($current_date);
		switch( $search_pre_delivery_type ){
			case 1:
				$target_time = strtotime("+1 day",$current_time);
				$query_search .= " AND UNIX_TIMESTAMP(orders.delivery_time_start) > 0 AND UNIX_TIMESTAMP(orders.delivery_time_start)<=".$target_time." AND UNIX_TIMESTAMP(orders.delivery_time_start)>=".$current_time;
			break;
			case 2:
				$target_time = strtotime("+3 day",$current_time);
				$query_search .= " AND UNIX_TIMESTAMP(orders.delivery_time_start) > 0 AND UNIX_TIMESTAMP(orders.delivery_time_start)<=".$target_time." AND UNIX_TIMESTAMP(orders.delivery_time_start)>=".$current_time;
			break;
			case 3:
				$target_time = strtotime("+7 day",$current_time);
				$query_search .= " AND UNIX_TIMESTAMP(orders.delivery_time_start) > 0 AND UNIX_TIMESTAMP(orders.delivery_time_start)<=".$target_time." AND UNIX_TIMESTAMP(orders.delivery_time_start)>=".$current_time;
			break;
		}
	}
	/* 搜索条件End */

	/* 订单管理状态 */
	$search_class = 0;
	$continued_arr = array();
	if(isset($_GET["search_class"]) and $_GET["search_class"] !="" ){
	   (int)$search_class = $configutil->splash_new($_GET["search_class"]);
	}
	switch($search_class){
		case 0:
			$query_search .= " AND orders.isvalid=true";  //所有订单
			$continued_arr[] = 'orders.paytime';
			$continued_arr[] = 'orders.confirm_receivetime';
			$continued_arr[] = 'orders.confirm_sendtime';
		break;
		case 1:
			$query_search .= " AND orders.paystatus=false AND orders.isvalid=true and is_pay_on_delivery != 1";  //待付款
		break;
		case 2:
			$query_search .= " AND (orders.paystatus=true or orders.is_pay_on_delivery=1) AND orders.sendstatus=0  AND orders.isvalid=true";	  //待发货
			$continued_arr[] = 'orders.paytime';
            if($from_page > 0){
                $query_search.= " and sso.is_accept = true ";
            }
			break;
        case 8 : //派单后未接单的状态[待处理]
            $query_search .= " AND orders.paystatus=true AND orders.sendstatus=0  AND orders.isvalid=true ";	  //待发货
            if($from_page > 0){
				$query_search.= " and sso.is_accept = false ";
			}
            $continued_arr[] = 'orders.paytime';
            break;
		case 3:
			$query_search .= " AND orders.paystatus=true AND orders.status=1  AND orders.isvalid=true";  //交易完成
			break;
		case 4:
			$query_search .= " AND orders.status=-1  AND orders.isvalid=true";  //已关闭
			break;
		case 5:
			$query_search .= " AND orders.paystatus=false  AND orders.isvalid=false";	  //未付款删除
			break;
		case 6:
			$query_search .= " AND orders.paystatus=true  AND orders.isvalid=false";	 //已付款删除
			break;
		case 7:
			$query_search .= " AND orders.paystatus=true  AND orders.status=0 AND orders.sendstatus in(2,4,6)";	 //已付款删除
			$continued_arr[] = 'orders.confirm_receivetime';
			break;
		case 8:
			$query_search .= " AND orders.paystatus=true AND orders.isvalid=true ";	 	//已支付
			break;
		case 0.5:
			$query_search .= " AND orders.sendstatus=true AND orders.isvalid=true ";	 //已发货
			$continued_arr[] = 'orders.confirm_sendtime';
			break;

		case 10:
			$query_search .= " AND orders.sendstatus>2  AND orders.isvalid=true";  //所有售后申请
			$continued_arr[] = 'orders.aftersale_time';
		break;
		case 11:
			$query_search .= " AND orders.sendstatus=3 AND orders.return_type=2 AND orders.isvalid=true";  //换货申请
		break;
		case 12:
			$query_search .= " AND (orders.sendstatus=5 OR (orders.sendstatus=3 AND orders.return_type=0)) AND orders.isvalid=true";	  //退款申请
			break;
		case 13:
			$query_search .= " AND orders.sendstatus=3 AND orders.return_type=1 AND orders.isvalid=true";  //退货申请
			break;
		case 14:
			$query_search .= " AND (orders.sendstatus=4 OR orders.sendstatus=6) AND orders.isvalid=true";  //售后处理完毕
			break;

		case -1:
			$query_search .= " AND orders.aftersale_state>0  AND orders.isvalid=true";
			break;
		case -2:
			$query_search .= " AND orders.aftersale_state>0 AND orders.aftersale_type=3 AND orders.isvalid=true";
			break;
		case -3:
			$query_search .= " AND orders.aftersale_state>0 AND orders.aftersale_type=1 AND orders.isvalid=true";
			break;
		case -4:
			$query_search .= " AND orders.aftersale_state>0 AND orders.aftersale_type=2 AND orders.isvalid=true";
			break;
		case -5:
			$query_search .= " AND orders.aftersale_state=4  AND orders.isvalid=true";
			break;
		case 0.11:
			$query_search .= " AND orders.is_pay_on_delivery=1 AND is_sign=0  AND orders.isvalid=true and orders.status !=2";//待签收
			break;
		default:
		echo "状态异常";
		return;
	}

	//echo $query_order.'<br/>';
	//非拼图无效订单
	$query_search .= " AND orders.is_collageActivities!=2 ";

	/* 订单管理状态 End */
	$live_room_id=0;			//主播房间id
	if(!empty($_GET["live_room_id"])){
		(int)$live_room_id = $configutil->splash_new($_GET["live_room_id"]);
		$query_search .= " AND orders.live_room_id='".$live_room_id."'";
	}

	/* 查看F2C店铺的订单 */
	if(!empty($_GET["f2c_id"])){
		(int)$f2c_id = $configutil->splash_new($_GET["f2c_id"]);
		$f2c_querys  = "select order_id from system_send_order where current_proxy_id=".$f2c_id." and isvalid=true and customer_id=".$customer_id;

		$f2c_ress 	= _mysql_query($f2c_querys);
		while($f2c_rows = mysql_fetch_object($f2c_ress)){
			$store_ides[] = $f2c_rows->order_id;
		}
		$store_ides = array_unique($store_ides);
		$store_ides = implode(',',$store_ides);
		if($store_ides != ""){
			$query_search .= " AND orders.batchcode in (".$store_ides.")";
		}else{
			$query_search .= " AND orders.batchcode = -1 ";
		}

	}
	/* 查看F2C店铺的订单 end*/

	/* 查看区域的F2c订单 */
	if(!empty($_GET["store_id"])){
		(int)$store_id = $configutil->splash_new($_GET["store_id"]);
		$f2c_query  = "select order_id from system_send_order where store_id=".$store_id." and isvalid=true and customer_id=".$customer_id;
		$f2c_res 	= _mysql_query($f2c_query);
		while($f2c_row = mysql_fetch_object($f2c_res)){
			$store_ids[] = $f2c_row->order_id;
		}
		$store_ids = array_unique($store_ids);
		$store_ids = implode(',',$store_ids);
		if($store_ids != ""){
			$query_search .= " AND orders.batchcode in (".$store_ids.")";
		}else{
			$query_search .= " AND orders.batchcode = -1 ";
		}
	}
	/* 查看区域的F2c订单 end*/

	// 分页---start
	$pagenum = 1;
	if(!empty($_GET["pagenum"])){
	   $pagenum = $configutil->splash_new($_GET["pagenum"]);
	}
	$pagesize = 20;
	//$pagesize = 1; //柠 TEST
	if(!empty($_GET["pagesize"])){
	   $pagesize = $configutil->splash_new($_GET["pagesize"]);
	}
	$start = ($pagenum-1) * $pagesize;
	$end = $pagesize;

	/*$query_num = '
	SELECT count(distinct orders.batchcode) as wcount FROM weixin_commonshop_orders as orders
	LEFT JOIN weixin_users as users ON users.id = orders.user_id
	INNER JOIN weixin_commonshop_order_addresses as addr ON orders.batchcode = addr.batchcode';
	if(!empty($_GET["search_product_name"])){    //产品名称
		$query_num .= " LEFT JOIN weixin_commonshop_products as pro ON orders.pid=pro.id ";
	}*/
	$query_num = '
	SELECT count(distinct orders.batchcode) as wcount FROM weixin_commonshop_orders as orders';
	if( $orgin_type == 1 ){
		$query_num .= " INNER JOIN cashback_t AS cash ON cash.batchcode=orders.batchcode";
	}
    if($is_sendorder == 1 || $from_page > 0){
        $query_num .= ' INNER JOIN system_send_order AS sso on orders.batchcode = sso.order_id and sso.send_type='.$from_page;
    }

	$continued_day = 0;
	if(!empty($_GET["continued_day"])){    //持续时间
	   $continued_day = $configutil->splash_new($_GET["continued_day"]);
	   $search_str = '';
	   if( $search_class == 10 ){
			$query_search = $query_search.' and orders.return_status=0';
	   }
		switch($continued_day){
			case 1:
				foreach( $continued_arr as $key => $val ){
					$search_str = $search_str." (to_days(now())-to_days(".$val."))=0 or";
				}
			break;
			case 2:
				foreach( $continued_arr as $key => $val ){
					$search_str = $search_str." (to_days(now())-to_days(".$val."))>=1 AND (to_days(now())-to_days(".$val."))<=3 or";
				}
			break;
			case 3:
				foreach( $continued_arr as $key => $val ){
					$search_str = $search_str." (to_days(now())-to_days(".$val."))>=4 AND (to_days(now())-to_days(".$val."))<=7 or";
				}
			break;
			case 4:
				foreach( $continued_arr as $key => $val ){
					$search_str = $search_str." (to_days(now())-to_days(".$val."))>7 or";
				}
			break;
		}

		$search_str = trim($search_str,'or');
		$query_search = $query_search.' and ('.$search_str.')';

	}

	$query_num .= $query_search;
	// var_dump($query_num);exit;
	// echo "<script>console.log('{$query_num}')</script>";
//	echo $query_num."<br>";

	$result_num = _mysql_query($query_num) or die('Query_num failed: ' . mysql_error());
	$wcount =0;
	$page=0;
	while ($row_num = mysql_fetch_object($result_num)) {
		$wcount =  $row_num->wcount ;
	}
	$page=ceil($wcount/$end);
	// 分页---end

	$query_order .= $query_search;
	//防止DISTINCT失效 2017-4-24
	$query_order .= ' group by orders.batchcode ';
	// $query_order .=  " GROUP BY orders.batchcode ORDER BY id DESC limit ".$start.",".$end;
	$query_order .=  "  ORDER BY id DESC limit ".$start.",".$end;
	//echo $query_order;
	/*订单查询End*/


	//return

	//查询平台是否开启虚拟发货
	$query_virtual = "select open_virtual_cust from weixin_commonshops where customer_id = ".$customer_id;
	$open_virtual_cust = 1;
	$result_virtual = _mysql_query($query_virtual) or die("query_virtual Query error : ".mysql_error());
	if($row_virtual = mysql_fetch_object($result_virtual)){
		$open_virtual_cust = $row_virtual -> open_virtual_cust;
	}

//查询可用物流公司
$company = array();
$sql_company = "select id,expresses_name,print_temp_id from weixin_expresses_company where isvalid=true and supply_id<0 and customer_id=".$customer_id." order by sort_num asc";
$result_company = _mysql_query($sql_company) or die("sql_company Query error : ".mysql_error());
$i = 0;
while($row_company = mysql_fetch_object($result_company)){
	$company[$i]['company_id'] = $row_company->id;
	$company[$i]['expresses_name'] = $row_company->expresses_name;
	$company[$i]['print_temp_id'] = $row_company->print_temp_id;
	$i++;
}
$company = json_encode($company);


//查询店铺等级名称 create by hzq
$store_level_sql = "select d_name,c_name,b_name,a_name from weixin_commonshop_shareholder where isvalid=true and customer_id=".$customer_id;
$store_level_query = _mysql_query($store_level_sql);
while($store_level_row =mysql_fetch_object($store_level_query)){
	$d_name = $store_level_row->d_name;
	$c_name = $store_level_row->c_name;
	$b_name = $store_level_row->b_name;
	$a_name = $store_level_row->a_name;
}
//查询店铺等级名称 End

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>订单管理</title>

<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css?<?php echo time(); ?>">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Order/orders/order.css">
<link rel="stylesheet" href="percent/jquery.percentageloader.0.2.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script charset="utf-8" src="../../../common/js/layer/V2_1/layer.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script src="../../../common_shop/jiushop/js/region_select.js"></script>
<script type="text/javascript" src="../../Common/js/Order/order/order.js?ver=<?php echo time(); ?>"></script>

<script type="text/javascript" src="../../Distribution/express/js/LodopFuncs.js"></script>
<script type="text/javascript" src="../../Distribution/express/js/print_delivery.js"></script>


<script src="percent/jquery.percentageloader.0.2.js"></script>
<script src="../../../common/utility.js"></script>


<script>
layer.config({
    extend: '/extend/layer.ext.js'
});
</script>

<style>
.con-button{  float: left;padding-left: 20px;padding-top: 17px;}
.con-button2{  float: left;padding-left: 5px;padding-top: 17px;}
.textCenter{text-align: center;}
.WSY_position_text a:first-child{margin-left: 0;}
.WSY_bottonli2 input{margin-right: 6px;}

.WSY_righticon_li04 label{margin-top: 3px;display: inline-block;}
.WSY_righticon_li04 input{margin-top: 6px;margin-right: 5px;vertical-align: sub;}
.WSY_righticon_li05 label{margin-top: 3px;display: inline-block;}
.WSY_righticon_li05 input{margin-top: 6px;margin-right: 5px;vertical-align: sub;}
.WSY_position_date input{height:22px;}

#topLoader {
	width: 256px;
	height: 256px;
	margin-bottom: 32px;
	position:absolute;width:400px; left:50%; top:50%; margin-left:-200px; height:auto; z-index:100; padding:1px;
}
#per_container {
	width: 500px;
	padding: 10px;
	margin-left: auto;
	margin-right: auto;
}
#BgDiv{background-color:#e3e3e3; position:absolute; z-index:99; left:0; top:0; display:none; width:100%; height:1000px;opacity:0.5;filter: alpha(opacity=50);-moz-opacity: 0.5;}

#DialogDiv{position:absolute;width:400px; left:50%; top:50%; margin-left:-200px; height:auto; z-index:100;background-color:#fff; border:1px #8FA4F5 solid; padding:1px;}
.order dl dd b{width:85px;}
.shui{width: 15px;height: 15px;color: #ffffff;background: #ec2935;padding: 2px;line-height: 15px;display: inline-block;border-radius: 3px;font-size: 15px;text-align: center;vertical-align: bottom;}
.div_item{float:left;padding:15px;font-size:14px;}
.div_item label{margin-left:5px;font-size:14px;}
.div_item input{border:1px solid #ccc; border-radius: 2px;}
.layui-layer-content button{float: left;margin-top: 56px;margin-bottom: 19px;width: 80px;height: 30px;}
.layui-layer{width:362px;}
.CP_table_bianhaoh + span{margin: 5px 40px 0 0;float: right;}
.zhibo{width: 30px;height: 15px;color: #ffffff;background: #25bf49;padding: 2px;line-height: 15px;display: inline-block;border-radius: 3px;font-size: 14px;text-align: center;vertical-align: bottom;}
a.payon_btn{display: inline-block; height: 20px; color: #fff;line-height:20px;padding: 3px 15px; border-radius: 3px;}
</style>
</head>

<body>
<div id="BgDiv"></div>
<div id="per_container">
<div style="display:none" id="topLoader"></div>
</div>
<!--内容框架开始-->
<div class="WSY_content" id="WSY_content_height">



       <!--列表内容大框开始-->
	<div class="WSY_columnbox">
    	<!--列表头部切换开始-->
    	<div class="WSY_column_header">
        	<div class="WSY_columnnav">
				<?php if($search_class >= 0 and $search_class<10){ ?>
				<a <?php if($search_class == 0){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&from_page=<?php echo $from_page;?>&f2c_id=<?php echo $f2c_id;?>">所有订单</a>

			<?php if($from_page <= 0){?>
				<a <?php if($search_class == 0.11){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=0.11&from_page=<?php echo $from_page;?>"><?php echo $show_menu;?>待签收</a>
            <?php }?>


            <?php if($from_page <= 0){?>
				<a <?php if($search_class == 1){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=1&from_page=<?php echo $from_page;?>"><?php echo $show_menu;?>待付款</a>
            <?php }?>

            <?php if($from_page > 0 ){?>
                    <!-- 订货和f2c的状态  待处理  -->
                 <a <?php if($search_class == 8){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=8&from_page=<?php echo $from_page;?>&f2c_id=<?php echo $f2c_id;?>">待处理</a>
                    <!-- 订货和f2c的状态  待处理  -->
            <?php }?>

				<a <?php if($search_class == 2){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=2&from_page=<?php echo $from_page;?>&f2c_id=<?php echo $f2c_id;?>">待发货</a>
				<a <?php if($search_class == 0.5){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=0.5&from_page=<?php echo $from_page;?>&f2c_id=<?php echo $f2c_id;?>">已发货</a>
				<a <?php if($search_class == 7){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=7&from_page=<?php echo $from_page;?>&f2c_id=<?php echo $f2c_id;?>">待完成</a>
				<a <?php if($search_class == 3){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=3&from_page=<?php echo $from_page;?>&f2c_id=<?php echo $f2c_id;?>">交易完成</a>

            <?php if($from_page <= 0){?>
				<a <?php if($search_class == 4){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=4&from_page=<?php echo $from_page;?>">已关闭</a>
				<a <?php if($search_class == 5){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=5&from_page=<?php echo $from_page;?>">未付款删除</a>
				<a <?php if($search_class == 6){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=6&from_page=<?php echo $from_page;?>">已付款删除</a>
            <?php }?>

				<?php }elseif($search_class>=10 ){ ?>
				<a <?php if($search_class == 10){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=10&from_page=<?php echo $from_page;?>">所有售后申请</a>
				<a <?php if($search_class == 11){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=11&from_page=<?php echo $from_page;?>">换货申请</a>
				<a <?php if($search_class == 12){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=12&from_page=<?php echo $from_page;?>">退款申请</a>
				<a <?php if($search_class == 13){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=13&from_page=<?php echo $from_page;?>">退货申请</a>
				<a <?php if($search_class == 14){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=14&from_page=<?php echo $from_page;?>">售后处理完毕</a>
				<?php }else{ ?>

						<a <?php if($search_class == -1){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=-1&from_page=<?php echo $from_page;?>">所有维权申请</a>

						<a <?php if($search_class == -2){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=-2&from_page=<?php echo $from_page;?>">换货申请</a>

						<a <?php if($search_class == -3){echo 'class="white1"';} ?> style="display:none;" href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=-3&from_page=<?php echo $from_page;?>">退款申请</a>
						<a <?php if($search_class == -4){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=-4&from_page=<?php echo $from_page;?>">退货申请</a>
						<a <?php if($search_class == -5){echo 'class="white1"';} ?> href="order.php?customer_id=<?php echo passport_encrypt($customer_id)?>&search_class=-5&from_page=<?php echo $from_page;?>">维权处理完毕</a>

				<?php }?>
            </div>
        </div>
        <!--列表头部切换结束-->
         <!--放置头部按钮  开始-->
        <div style="width:100%;display:block;">
			<input type="hidden" id="status" value="<?php echo $search_class;?>" >
			<ul>
         		<li class="WSY_bottonli WSY_bottonli2" style="display:block;float:left;margin-left:20px;">
					<input type="button"  value="导出记录" onClick="export_excel(1);" >
					<input type="button"  value="导出飞豆" onClick="export_excel(2);" >
					<input type="button"  value="导出海关头部" onClick="export_excel(3);" >
					<input type="button"  value="导出海关明细" onClick="export_excel(4);" >
					<?php if($from_page < 1){ //F2C系统不要?><input type="button"  value="回收库存" onClick="stock_recovery();" ><?php } ?>
					<?php if($search_class == 2){?><input type="button"  value="批量打印快递单" onClick="print_order();" ><?php } ?>
					<?php if($from_page < 1){ //F2C系统不要?><a href="//admin.weisanyun.cn/weixinpl/back_newshops/Order/order/attach/payondelivery.xls" target="_blank" class="payon_btn WSY-skin-bg"  value="回收库存"  >签收模板</a><?php } ?>
				 </li>
			</ul>
			<div style="clear:both"></div>
		</div>
		<!--放置头部按钮  结束-->
    <!--订单管理代码开始-->
	<form class="search" id="search_form" >
    <div class="WSY_data">

		<div class="WSY_position1" >
			<ul>
				<li class="WSY_position_date tate001">
					<p>支付时间：<input class="date_picker" type="text" name="AccTime_S" id="pay_begintime" value="<?php echo $pay_begintime ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});"></p>
					<p>&nbsp;&nbsp;-&nbsp;&nbsp;<input class="date_picker" type="text" name="AccTime_B" id="pay_endtime" value="<?php echo $pay_endtime ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});"></p>
				</li>
				<li class="WSY_position_date tate001" >
					<p>&nbsp;&nbsp;&nbsp;&nbsp;下单时间：<input class="date_picker" type="text" name="AccTime_E" id="begintime" value="<?php echo $begintime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});"></p>
					<p>&nbsp;&nbsp;-&nbsp;&nbsp;<input class="date_picker" type="text" name="AccTime_B" id="endtime" value="<?php echo $endtime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});"></p>

				</li>

				<li <?php if( $search_class != 2 ){ echo 'style="display:none;"'; } ?> class="WSY_position_text" style="margin-left:10px;margin-top:13px;">
					<span>预配送待发货订单筛选：</span>
					<select name="search_pre_delivery_type" id="search_pre_delivery_type" >
						<option value="-1" <?php if($search_pre_delivery_type==-1){ ?>selected <?php } ?>>全部</option>
						<option value="1" <?php if($search_pre_delivery_type==1){ ?>selected <?php } ?>>当天预配送待发货</option>
						<option value="2" <?php if($search_pre_delivery_type==2){ ?>selected <?php } ?>>三天内预配送待发货</option>
						<option value="3" <?php if($search_pre_delivery_type==3){ ?>selected <?php } ?>>七天内预配送待发货</option>
					</select>
				</li>
				<li class="WSY_position_text" style="margin-top:13px;margin-left:10px;margin-right: 10px;">
					<a>订单号：<input type="text" name="search_batchcode" id="search_batchcode" value="<?php echo $search_batchcode; ?>"></a>
					<a>姓名：<input type="text" name="search_name" id="search_name" value="<?php echo $search_name; ?>"></a>
					<input type="hidden" name="search_class" id="search_class" value="<?php echo $search_class; ?>">
					<select name="search_name_type" id="search_name_type">
						<option value="1" <?php if($search_name_type==1){ ?>selected <?php } ?>>微信昵称</option>
						<option value="2" <?php if($search_name_type==2){ ?>selected <?php } ?>>收货人</option>
					</select>
                    <?php if($from_page <= 0){ //订货系统不要?>
					<span>订单类型：</span>
                    <?php }?>
					<select name="orgin_type" id="orgin_type" style="width:100px;<?php echo $from_page > 0 ? 'display:none':'';?>">
						<option value="" >全部</option>
						<option value="1" <?php if($orgin_type==1){ ?>selected<?php } ?>>消费奖励</option>
						<option value="2" <?php if($orgin_type==2){ ?>selected<?php } ?>>拼团</option>
						<option value="3" <?php if($orgin_type==3){ ?>selected<?php } ?>>直播</option>
						<option value="4" <?php if($orgin_type==4){ ?>selected<?php } ?>>核销</option>
					</select>
                    <?php if($from_page <= 0){ //订货系统不要?>
					<span>订单归属：</span>
                    <?php }?>
					<select name="search_attribution_type" id="search_attribution_type" style="<?php echo $from_page > 0 ? 'display:none':'';?>">
						<option value="0" <?php if($search_attribution_type==0){ ?>selected <?php } ?>>全部</option>
						<option value="-1" <?php if($search_attribution_type==-1){ ?>selected <?php } ?>>平台</option>
						<option value="1" <?php if($search_attribution_type==1){ ?>selected <?php } ?>>合作商</option>
						<option value="2" <?php if($search_attribution_type==2){ ?>selected <?php } ?>>代理商</option>
					</select>

					<select name="search_order_ascription" id="search_order_ascription" style="display:<?php if($search_attribution_type<=0)echo "none"; ?>">
						<option value="-1" >-- 请选择 --</option>
						<?php
						$isAgent = -1;
						switch($search_attribution_type){
							case 1:
								$isAgent = 3;
							break;
							case 2:
								$isAgent = 1;
							break;
						}
						$query_prom = "
						SELECT pro.user_id,users.name,users.weixin_name
						FROM promoters as pro
						LEFT JOIN weixin_users as users on pro.user_id = users.id
						WHERE pro.isvalid=true AND pro.isAgent = ".$isAgent." AND pro.customer_id = ".$customer_id;
						$result_prom = _mysql_query($query_prom) or die('Query_prom failed: ' . mysql_error());
						while ($row_prom = mysql_fetch_object($result_prom)) {

							$sup_user_id     = $row_prom->user_id;
							$sup_name        = $row_prom->name;
							$sup_weixin_name = $row_prom->weixin_name;

							$sup_userName    =	$sup_name;
							if(!empty($sup_weixin_name)){ $sup_userName .= "(". $sup_weixin_name . ")"; }
						?>
						<option value="<?php echo $sup_user_id;  ?>" <?php if($search_order_ascription == $sup_user_id){echo "selected"; } ?>><?php echo $sup_userName; ?></option>
						<?php } ?>

					</select>
					<a style="display:<?php if($search_attribution_type!=1)echo "none"; ?>;" id="search_supply">合作商ID：<input type="text" name="search_supply_id" id="search_supply_id" value="<?php if($search_attribution_type==1 && $search_order_ascription>0 )echo $search_order_ascription; ?>" onkeyup="takeReplace(this)"></a>
					<a style="display:<?php if($search_attribution_type!=2)echo "none"; ?>;" id="search_agent">代理商ID：<input type="text" name="search_agent_id" id="search_agent_id" value="<?php if($search_attribution_type==2 && $search_order_ascription>0 )echo $search_order_ascription; ?>"  onkeyup="takeReplace(this)"></a>

				 </li>
				 <li class="WSY_bottonli WSY_bottonli2" style="display:none;">
					<input type="button"  value="导出记录" onClick="exportRecord(1);" >
					<a href="javascript:export_excel(1);">导出记录</a>
					<input type="button"  value="导出飞豆" onClick="exportRecord(2);" >
					<input type="button"  value="导出海关头部" onClick="exportRecord(3);" >
					<input type="button"  value="导出海关明细" onClick="exportRecord(4);" >
					<!-- <input type="button" value="批量确认完成" onclick="batchFinish();" /> -->
					<!--<input type="button" value="批量删除">-->
				 </li>

				 <!-- <li class="WSY_bottonli WSY_bottonli2">
					<input type="button"  value="导出记录" onClick="export_excel(1);" >
					<input type="button"  value="导出飞豆" onClick="export_excel(2);" >
					<input type="button"  value="导出海关头部" onClick="export_excel(3);" >
					<input type="button"  value="导出海关明细" onClick="export_excel(4);" >
					<input type="button"  value="回收库存" onClick="stock_recovery();" >-->
					<!-- <input type="button" value="批量确认完成" onclick="batchFinish();" /> -->
					<!--<input type="button" value="批量删除">-->
				<!--  </li>	-->
				<li class="WSY_position_text" style="margin-top:13px;">
					<a>产品名称：<input type="text" name="search_product_name" id="search_product_name" value="<?php echo $search_product_name; ?>"></a>
					<a>手机号码：<input type="text" name="search_phone" id="search_phone" value="<?php echo $search_phone; ?>" onkeyup="this.value=this.value.replace(/[^\d]/g,'');"></a>
					<a class="WSY_bottonliss"><input type="button" value="订单搜索" onclick="searchForm();" /></a>
				</li>
			 </ul>
		</div>

    	<div class="WSY_orderformbox" style="margin-top: 12px;" >
			<ul>
            <li class="WSY_righticon_li01">
				<p>支付方式：</p>
                	<select name="search_paystyle" id="search_paystyle" >
                    	<option value="" >-- 请选择 --</option>
							<option value="微信支付" <?php if($search_paystyle=="微信支付"){ ?>selected <?php } ?>>微信支付</option>
							<option value="支付宝支付" <?php if($search_paystyle=="支付宝支付"){ ?>selected <?php } ?>>支付宝支付</option>
							<option value="通联支付" <?php if($search_paystyle=="通联支付"){ ?>selected <?php } ?>>通联支付</option>
							<option value="货到付款" <?php if($search_paystyle=="货到付款"){ ?>selected <?php } ?>>货到付款</option>
							<option value="到店支付" <?php if($search_paystyle=="到店支付"){ ?>selected <?php } ?>>到店支付</option>
							<option value="会员卡余额支付" <?php if($search_paystyle=="会员卡余额支付"){ ?>selected <?php } ?>>会员卡余额支付</option>
							<option value="购物币支付" <?php if($search_paystyle=="购物币支付"){ ?>selected <?php } ?>>购物币支付</option>
							<option value="零钱支付" <?php if($search_paystyle=="零钱支付"){ ?>selected <?php } ?>>零钱支付</option>
							<option value="积分支付" <?php if($search_paystyle=="积分支付"){ ?>selected <?php } ?>>积分支付</option>
							<option value="易宝支付" <?php if($search_paystyle=="易宝支付"){ ?>selected <?php } ?>>易宝支付</option>
							<option value="京东支付" <?php if($search_paystyle=="京东支付"){ ?>selected <?php } ?>>京东支付</option>
							<option value="V咖支付" <?php if($search_paystyle=="V咖支付"){ ?>selected <?php } ?>>V咖支付</option>
							<option value="paypal支付" <?php if($search_paystyle=="paypal支付"){ ?>selected <?php } ?>>paypal支付</option>
							<option value="兴业银行公众号支付" <?php if($search_paystyle=="兴业银行公众号支付"){ ?>selected <?php } ?>>兴业银行公众号支付</option>
							<option value="优惠抵扣" <?php if($search_paystyle=="优惠抵扣"){ ?>selected <?php } ?>>优惠抵扣</option>
							<option value="环迅快捷支付" <?php if($search_paystyle=="环迅快捷支付"){ ?>selected <?php } ?>>环迅快捷支付</option>
							<option value="环迅微信支付" <?php if($search_paystyle=="环迅微信支付"){ ?>selected <?php } ?>>环迅微信支付</option>
							<option value="后台支付" <?php if($search_paystyle=="后台支付"){ ?>selected <?php } ?>>后台支付</option>
							<option value="威富通支付" <?php if($search_paystyle=="威富通支付"){ ?>selected <?php } ?>>威富通支付</option>
							<option value="健康钱包支付" <?php if($search_paystyle=="健康钱包支付"){ ?>selected <?php } ?>>健康钱包支付</option>
                 </select>
            </li>
            <li class="WSY_righticon_li03">
				<p>每页记录数：</p>
					<select name="pagesize" id="pagesize" >
					<option value="20" <?php if($pagesize==20){ ?>selected<?php } ?>>20</option>
					<option value="40" <?php if($pagesize==40){ ?>selected<?php } ?>>40</option>
					<option value="75" <?php if($pagesize==75){ ?>selected<?php } ?>>75</option>
					<option value="100" <?php if($pagesize==100){ ?>selected<?php } ?>>100</option>
					</select>
            </li>

			<li class="WSY_righticon_li03" style="<?php echo $from_page > 0? 'display:none':''; ?>">
				<p>订单来源：</p>
					<select name="orgin_from" id="orgin_from" style="width:100px;">
						<option value="" >所有订单</option>
						<option value="1" <?php if($orgin_from==1){ ?>selected<?php } ?>>非推广订单</option>
						<option value="2" <?php if($orgin_from==2){ ?>selected<?php } ?>>推广订单</option>
					</select>
            </li>
			<?php
			if($from_page <= 0){
				$arr = array('1','3','4','5','6');
				if( !in_array($search_class,$arr) ){

			?>
			<li class="WSY_righticon_li03" >
				<p>持续天数：</p>
					<select name="continued_day" id="continued_day" style="width:100px;">
						<option value="" >全部</option>
						<option value="1" <?php if($continued_day==1){ ?>selected<?php } ?>>今天内</option>
						<option value="2" <?php if($continued_day==2){ ?>selected<?php } ?>>1-3天</option>
						<option value="3" <?php if($continued_day==3){ ?>selected<?php } ?>>4-7天</option>
						<option value="4" <?php if($continued_day==4){ ?>selected<?php } ?>>7天以上</option>
					</select>
            </li>
				<?php }else{ ?>
					<input type="hidden" name='continued_day' id="continued_day" value="">
				<?php }
            ?>
            <li class="WSY_righticon_li04">
			    <input type="checkbox" id="auto_refer" name="auto_refer" value="on" <?php if($isauto){?> checked<?php } ?>><label for="auto_refer">自动刷新订单</label>
			</li>

			<li class="WSY_righticon_li05">
				<input type="checkbox"  id="order_remind" name="order_remind" value="on" onclick="chkremind();" <?php if($is_remind){?> checked<?php } ?>><label for="order_remind">订单提醒功能</label>
			</li>
			<?php } ?>

			<input type="hidden" id="search_shop_id" value="<?php echo $search_shop_id;?>">  <!--门店号-->
			</ul>
        </div>
	</div>
	</form>

	<?php
	if($from_page <= 0){
		$action = "save_order_excel.php?customer_id={$customer_id_en}&from_page={$from_page}";
		// if($_GET['search_class'] == 0.11)
		// {
		// 	$action = "save_payondelivery_excel.php?customer_id=$customer_id_en";
		// }
	?>
	<form  id="frm_import" action="<?php echo $action ?>" enctype="multipart/form-data" method="post">
		<div style="overflow:hidden;margin-left:20px">
			<div class="uploader white">
				<input type="text" class="filename" readonly/>
				<input type="button" name="file" class="button" value="上传..."/>
				<input type="file" name="excelfile" id="excelfile" size="30"/>
			</div>
			<div class="WSY_position_text" style="margin-left: 33px;margin-top:24px">
				<!--<input type=file name="excelfile" style="width:150px;" id="excelfile" />-->
				<span>数据来源：</span>
				<select name="order_way">
					<option value="1">导出记录</option>
					<option value="2">导出飞豆</option>
					<option value="3">货到付款</option>
				</select>
				<a class="WSY_bottonliss"><input type="button" value="导入数据" onclick="importMember();" /></a>
				<span style="color:red">
					(请使用金山Excel)
				</span>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<span id="print_batch_show"> </span>

			</div>
		</div>
	</form>
	<?php } ?>
	<style>
		.uploader,.WSY_position_text{float:left}
	</style>
	<!--订单管理代码开始 End -->

     <!--表格开始-->
	<div style="width:100%;overflow:hidden">
		<table width="97%" class="CP_table " id="order_table">
			 <thead class="CP_table_header">
				<th width="3%">
					<input type="checkbox" name="all_checkbox" onclick="change_box()" class="all_checkbox" >
					全选
				</th>
				<th width="30%">产品</th>
				<th width="10%">收货人</th>
				<th width="10%">实付金额</th>
				<th width="10%">发货状态</th>
				<th width="8%">订单状态</th>
                <?php if($from_page > 0){ ?>
                    <th width="12%">发货仓</th>
				<?php } ?>
				<th width="10%">邀请人</th>
				<th width="15%">操作</th>
			 </thead>

			 <?php
			//echo $query_order;
				$result_order = _mysql_query($query_order) or die('Query_order failed: ' . mysql_error());
				
				while ($row_order = mysql_fetch_object($result_order)) {
					
					$o_batchcode           = $row_order->batchcode;  //订单
					$buy_user_id           = $row_order->user_id;  //购买用户id
					$pay_batchcode		   = $row_order->pay_batchcode;  //支付订单号
					$o_createtime          = $row_order->createtime;  //下单时间
					$o_paystyle            = $row_order->paystyle;  //支付类型
					$o_paystatus           = $row_order->paystatus;  //支付状态
					$o_payondelivery       = $row_order->is_pay_on_delivery;  //是否货到付款
					$o_is_sign             = $row_order->is_sign;  //签收状态
					$o_allipay_orderid     = $row_order->allipay_orderid;	 //通联支付单号
					// $o_expressName         = $row_order->expressName;		//收货人姓名
					// $o_weixin_name         = $row_order->weixin_name;			//微信名称
					// $o_expressPhone        = $row_order->expressPhone;	 //快递-电话
					$o_backgoods_reason    = $row_order->backgoods_reason;	 //(退货/退款)原因
					// $o_totalprice          = $row_order->totalprice;		  //总价
					$o_exp_user_id         = $row_order->exp_user_id;   //推广员ID
					$o_sendstatus          = $row_order->sendstatus;   //发货状态
					$o_status              = $row_order->status;   //订单状态
					$o_return_type         = $row_order->return_type;    //退货/款状态
					$o_confirm_sendtime    = $row_order->confirm_sendtime;   //发货时间
					$o_confirm_receivetime = $row_order->confirm_receivetime;   //收货时间
					$o_sendtime 		   = $row_order->sendtime;   			//送货时间 8.0使用

					$o_sf_printUrl         = $row_order->printUrl;			//顺风进口订单
					$o_paytime             = $row_order->paytime;			//支付时间
					$o_agent_id            = $row_order->agent_id;			//代理商编号
					$o_supply_id           = $row_order->supply_id;			//供应商编号
					$o_is_QR               = $row_order->is_QR;			//二维码
					// $o_weixin_fromuser     = $row_order->weixin_fromuser;			//微信OpneID
					// $o_name                = $row_order->name;			//注册-名称
					// $o_name 			   = htmlspecialchars($o_name);	//转义特殊字符
					// $o_phone               = $row_order->phone;			//注册-电话
					// $o_phone               = $row_order->phone;			//注册-电话
					// $o_location_p          = $row_order->location_p;			//省
					// $o_location_c          = $row_order->location_c;			//市
					// $o_location_a          = $row_order->location_a;			//区
					// $o_expressAddress      = $row_order->expressAddress;			//详细地址
					// $o_expressAddress      = htmlspecialchars($o_expressAddress);	//转义特殊字符
					// $o_identity            = $row_order->identity;			//身份证
			 		// $identityimgt		   = $row_order->identityimgt;
					// $identityimgt          = $img_utility->imgurl_url('',$identityimgt);
					// $identityimgf		   = $row_order->identityimgf;
					// $identityimgf          = $img_utility->imgurl_url('',$identityimgf);
					$o_remark              = $row_order->remark;			//订单备注
					$o_expressnum          = $row_order->expressnum;			//发货-快递单号
					$o_sendstyle           = $row_order->sendstyle;			//收货方式
					$o_express_id          = $row_order->express_id;			//快递方式
					$o_expressname_new     = $row_order->expressname;			//快递名称
					$o_Pay_Method          = $row_order->Pay_Method;			//后台支付
					$o_sendway             = $row_order->sendway;			//代理商发货，0:未指派，1：平台发货，2：代理商发货
					$o_agentcont_type      = $row_order->agentcont_type;		//代理结算: 1、代理结算 0、推广员结算
					$o_is_delay            = $row_order->is_delay;		//申请延期状态：1：已申请；2：已处理
					$o_aftersale_type      = $row_order->aftersale_type;		//售后维权 0：无；1：退款；2：退货；3：换货
					$o_aftersale_state     = $row_order->aftersale_state;  //申请售后状态：0:默认；1：已申请；2：同意；3：驳回；4：已处理
					$o_return_status       = $row_order->return_status;  //退货状态。0. 未退货；1：退货成功；-1：退货失败；2：同意退货；3：驳回请求；4：确认退货；5： 用户已退货；6：商家确认收货；7：商家已发货；8：同意退款；9：驳回退款
					$o_store_id 		   = $row_order->store_id;
					$o_store_name 		   = $row_order->store_name;
					$o_isreducesupply 	   = $row_order->isreducesupply; //0：维权扣除供应商款项未完成 1：维权扣除供应商款项已完成
					$o_send_remarks 	   = $row_order->send_remarks; //发货备注
					$o_delivery_time_start = $row_order->delivery_time_start; //配送时间
					$o_delivery_time_end   = $row_order->delivery_time_end; //配送时间
					$o_is_collageActivities = $row_order->is_collageActivities; //拼团订单标识
					$o_aftersale_time      = $row_order->aftersale_time; //售后申请时间
					$mb_order              = $row_order->mb_order; //主播id
					$o_is_sendorder        = $row_order->is_sendorder; //零售派单订单

                    // 派单仓库和代理商
                    $sso_store_id = $row_order -> sso_store_id;
                    $sso_proxy_id = $row_order -> sso_proxy_id;
                    $sso_is_accept= $row_order -> sso_is_accept;


					//查询单笔订单总价格
					$query_totalprice = "SELECT SUM(totalprice) AS totalprice FROM weixin_commonshop_orders WHERE batchcode='".$o_batchcode."' AND customer_id=".$customer_id;
					$result_totalprice = _mysql_query($query_totalprice) or die('Query_totalprice failed:'.mysql_error());
					$o_totalprice_result = mysql_fetch_assoc($result_totalprice);
			 		$o_totalprice = $o_totalprice_result['totalprice'];

					//获取下单人的信息
					$query_users = "SELECT name,weixin_name,weixin_fromuser,phone FROM weixin_users WHERE id=".$buy_user_id;
					$result_users = _mysql_query($query_users) or die('Query_users failed:'.mysql_error());
					$user_info = mysql_fetch_assoc($result_users);
					$o_name 			= htmlspecialchars($user_info['name']);	//注册-名称
					$o_weixin_name 		= $user_info['weixin_name'];			//微信名称
					$o_weixin_fromuser 	= $user_info['weixin_fromuser'];		//微信OpneID
					$o_phone 			= $user_info['phone'];					//注册-电话

					//获取收货地址信息
					$query_address = "SELECT name,phone,location_p,location_c,location_a,address,identity,identityimgt,identityimgf FROM weixin_commonshop_order_addresses WHERE batchcode='".$o_batchcode."'";
					$result_address = _mysql_query($query_address) or die('Query_address failed:'.mysql_error());
					$address_info = mysql_fetch_assoc($result_address);
					$o_expressName = $address_info['name'];
					$o_expressPhone = $address_info['phone'];
					$o_location_p = $address_info['location_p'];
					$o_location_c = $address_info['location_c'];
					$o_location_a = $address_info['location_a'];
					$o_expressAddress = htmlspecialchars($address_info['address']);
					$o_identity = $address_info['identity'];
					$identityimgt = $img_utility->imgurl_url('',$address_info['identityimgt']);
					$identityimgf = $img_utility->imgurl_url('',$address_info['identityimgf']);

					//旧订单支付订单号转换
					if( empty( $pay_batchcode ) ){
						$pay_batchcode = $o_batchcode;
					}

					if(!empty($o_weixin_name)){
						$o_name .= "(". $o_weixin_name . ")";
					}

					$o_store_name = "";
					if(!empty($o_store_id) && $o_store_id > 0){
						$query_shop = "select name from weixin_card_shops where isvalid = true and id = ".$o_store_id;
						$result_shop = _mysql_query($query_shop) or die('query_shop failed: ' . mysql_error());
						if($row_shop = mysql_fetch_object($result_shop)){
							$o_store_name = $row_shop->name;
						}
					}

					/*发票信息*/
					$sql_in = "select invoice_head from order_invoice_t where batchcode='".$o_batchcode."'";
					$result_in = _mysql_query($sql_in) or die('Query_express failed: ' . mysql_error());
					$invoice_head = "";
					while ($row_in = mysql_fetch_object($result_in)) {
						$invoice_head = $row_in->invoice_head;
					}
					/*发票信息*/




					/*  发货状态 */
					$sendstatusstr = "<img src=\"../../../common/images_V6.0/contenticon/notaffirm-icon.png\" /> <b>未发货</b>";
					switch ($o_sendstatus) {
						case 1:
							$sendstatusstr = "<img src=\"../../../common/images_V6.0/contenticon/affirm-icon.png\" /> <b style=\"color:#31B0D5\">已发货</b>";
							if ($o_is_delay == 1) {
								$sendstatusstr .= "<span style='color:red'> [申请延迟收货]</span>";
							}
							break;
						case 2:
							$sendstatusstr = "<img src=\"../../../common/images_V6.0/contenticon/confirm_delivery.png\" /> <b style=\"color:#337AB7\">顾客已收货</b>";
							break;
						case 3:
							$sendstatusstr = "<img src=\"../../../common/images_V6.0/contenticon/return-goods.png\" /> <b style=\"color:#C9302C\">顾客申请退货</b>";
							if ($o_return_type == 0) {
								$sendstatusstr = "<img src=\"../../../common/images_V6.0/contenticon/return-money-only.png\" /> <b style=\"color:#C9302C\">申请退货(仅退款)</b>";
							} else if ($o_return_type == 2) {
								$sendstatusstr = "<img src=\"../../../common/images_V6.0/contenticon/change-goods.png\" /> <b style=\"color:#C9302C\">申请退货(换货)</b>";
							}
							if ($o_return_status == 2) {
								$sendstatusstr .= "<b style='color:#C9302C'> [已同意]</b>";
							} else if ($o_return_status == 3) {
								$sendstatusstr .= "<b style='color:#C9302C'> [已驳回]</b>";
							} else if ($o_return_status == 5) {
								$sendstatusstr .= "<b style='color:#C9302C'> [用户已退货]</b>";
							} else if ($o_return_status == 6) {
								$sendstatusstr .= "<b style='color:#C9302C'> [已收到退货]</b>";
							}
							break;
						case 4:
							$rt = "退货";
							if ($o_return_type == 0) {

								$rt = "仅退款";
							} else if ($o_return_type == 2) {

								$rt = "换货";
							}
							$sendstatusstr = "<img src=\"../../../common/images_V6.0/contenticon/confirm-return.png\" /> <b style=\"color:#1eaf4e\">退货已确认(" . $rt . ")</b>";
							break;
						case 5:
							$sendstatusstr = "<img src=\"../../../common/images_V6.0/contenticon/return-money.png\" /> <b style=\"color:#C9302C\">顾客申请退款</b>";
							if ($o_return_status == 8) {
								$sendstatusstr .= "<b style='color:#C9302C'> [已同意]</b>";
							} else if ($o_return_status == 9) {
								$sendstatusstr .= "<b style='color:#C9302C'> [已驳回]</b>";
							}
							break;
						case 6:
							$sendstatusstr = "<img src=\"../../../common/images_V6.0/contenticon/refund-success.png\" /> <b style=\"color:#1eaf4e\">退款完成</b>";
							break;
					}
					/*  发货状态 End */



					/*行邮税*/
					//获取订单行邮税总和
					$total_tax = 0;
					$get_tax_result = get_tax_result($o_batchcode);
					$total_tax = $get_tax_result[1];
					$total_tax_type = $get_tax_result[0];
					//获取行邮税类型名称
					$tax_name = get_tax_name($tax_type);
					/*行邮税*/



					/*是否改价 改价则改价价格为全价,非改价则需加上运费*/
					$o_totalprice_last = 0;
					$o_CouponPrice = 0;	//优惠券金额
					$o_SupplyCostMoney = 0;//供应商成本价
					$o_SupplyExpressPrice = 0;//运费
					$pay_currency = 0;//使用购物币
					$needScore = 0;//使用积分
					$last_time = '';//最后修改状态时间
					/* 查询运费 */
					//8.0 2016-7-9 修改 by 叶贺聪 每个产品的运费都会保存到这个表里面，所以用sum方法求和
					$query_express="select sum(price) as price from weixin_commonshop_order_express_prices where isvalid=true and batchcode='".$o_batchcode."'";
					$result_express = _mysql_query($query_express) or die('Query_express failed: ' . mysql_error());
					$o_express_price = 0;
					while ($row_express = mysql_fetch_object($result_express)) {
						$o_express_price = $row_express->price;
					}
					/* 查询运费End */
					$o_totalprice_last = $o_totalprice + $o_express_price;	//非改价加运费
					/*查询快递名*/
					if($o_express_id>0){

						//供应商快递
						if( $o_supply_id > 0 ){
							$query_expressname="select name from weixin_expresses_supply where id=".$o_express_id." and supply_id=".$o_supply_id." and customer_id=".$customer_id;
						}else{
							$query_expressname="select name from weixin_expresses where id=".$o_express_id." and customer_id=".$customer_id;
						}

						$result_expressname=_mysql_query($query_expressname) or die ('query_expressname faild' .mysql_error());
						while($row=mysql_fetch_object($result_expressname)){
							$new_expressname=$row->name;
						}
					}
					$query_change="select price,CouponPrice,SupplyCostMoney,ExpressPrice,pay_currency,needScore from weixin_commonshop_order_prices where isvalid=true and batchcode='".$o_batchcode."' limit 1";
					$result_change = _mysql_query($query_change) or die('Query_change failed: ' . mysql_error());
					while ($row_change = mysql_fetch_object($result_change)) {
					    //获取订单的真实价格（可能是折扣总价）
					    $o_totalprice_last = $row_change->price;
					    $o_CouponPrice = $row_change->CouponPrice;
						$o_SupplyCostMoney = $row_change->SupplyCostMoney;
						$o_SupplyExpressPrice = $row_change->ExpressPrice;
						$pay_currency = $row_change->pay_currency;
						$needScore = $row_change->needScore;
					}

                    if($o_paystyle=="找人代付"){//找人代付没有购物币
                        $pay_currency = 0;
                    }

					$changeprice_str = "";
					$query_change_price= "select totalprice from weixin_commonshop_changeprices where status=1 and isvalid=1 and batchcode='".$o_batchcode."' limit 1";
					$result_change_price = _mysql_query($query_change_price) or die('Query_change_price failed: ' . mysql_error());
					while ($row_change_price = mysql_fetch_object($result_change_price)) {
					    $o_totalprice_last = $row_change_price->totalprice;
						if($o_totalprice_last>0){
							$changeprice_str = '<span style="color:#dd514c;margin-left: 4px;">(改价后)</span>';
						}
					}
					$o_totalprice_last = $o_totalprice_last - $pay_currency;
					/* 是否改价END */



					/* 查询上级 */
					$exp_user_name ="无";
					if($o_exp_user_id>0){
					    $query_exp= "select name,phone,weixin_name,weixin_fromuser from weixin_users where id=" . $o_exp_user_id . " limit 1";
					    $result_exp = _mysql_query($query_exp) or die('Query_exp failed: ' . mysql_error());
						while ($row_exp = mysql_fetch_object($result_exp)) {
							$exp_user_name=$row_exp->name;
							$exp_weixin_name = $row_exp->weixin_name;
							$exp_fromuser = $row_exp->weixin_fromuser;
							if(!empty($exp_weixin_name)){
								$exp_user_name = $exp_user_name."(".$exp_weixin_name.")";
							}
						}
					}
					/* 查询上级End */



					/* 订单收发货日期 */
					$confirm_sendtimestr = "";
					$confirm_receivetimestr="";
					if(!empty($o_confirm_sendtime) and $o_confirm_sendtime!="0000-00-00 00:00:00"){
						$confirm_sendtimestr = "<p>发货时间:".$o_confirm_sendtime;
						if($o_sf_printUrl){
							$confirm_sendtimestr .= "<a  href='$sf_printUrl' target='_blank' class='btn'   title='顺丰运单打印'><i  class='icon-print'></i></a>&nbsp;&nbsp;<a  href='./sf/routeQuery.php?mailorderNo=$expressnum&customer_id=$customer_id' target='_blank' class='btn'   title='运单路由'><i  class='icon-globe'></i></a>";
					  }
					  $confirm_sendtimestr .= "</p>";
				   }
				    if(!empty($o_confirm_receivetime) and $o_confirm_receivetime!="0000-00-00 00:00:00"){

					    if($o_sendstatus==4 or $o_sendstatus==6){
							$confirm_receivetimestr="<p>退货时间:".$o_confirm_receivetime."</p>";
					    }else{
							$confirm_receivetimestr="<p>收货时间:".$o_confirm_receivetime."</p>";
					    }
				    }

					/* 订单收发货日期 End */



					/* 代理商 */
					$agent_name ="";
					$agent_username = "";
					$agent_weixin_fromuser ="";
					if($o_agent_id>0){
						$query_agent = "SELECT name,weixin_name,weixin_fromuser FROM weixin_users WHERE id=".$o_agent_id." limit 1";
						$result_agent = _mysql_query($query_agent) or die('query_agent failed: ' . mysql_error());
						while ($row_agent = mysql_fetch_object($result_agent)) {
							$agent_username=$row_agent->name;
							$agent_weixin_fromuser= $row_agent->weixin_fromuser;
							$agent_weixin_name=$row_agent->weixin_name;
							if(!empty($agent_weixin_fromuser)){
								$agent_username = $agent_username."(".$agent_weixin_name.")";
							}
						}
					}
					/* 代理商 End  */



					/* 查看代理商发货方式 */
					$p_sendway=0;
					$o_open_sendway=0;
					if($o_agent_id>0){
						$query_sendway = "select sendway from promoters where isvalid=true and customer_id=".$customer_id." and user_id=".$o_agent_id;
						$result_sendway = _mysql_query($query_sendway) or die('Query_sendway failed: ' . mysql_error());
						while ($row_sendway = mysql_fetch_object($result_sendway)) {
							$p_sendway = $row_sendway->sendway; //1:代理商自己发货 0:平台发货
						}
						if($p_sendway==1 and $o_sendway==2 and $o_supply_id<0){
							$o_open_sendway=1;
						}
					}
					/* 查看代理商发货方式End */



					/* 供应商 */
					$supply_name ="";
					$supply_username = "";
					$supply_weixin_fromuser ="";
					if($o_supply_id>0){
						$query_supply = "SELECT name,weixin_name,weixin_fromuser FROM weixin_users WHERE id=".$o_supply_id." limit 1";
						$result_supply = _mysql_query($query_supply) or die('query_supply failed: ' . mysql_error());
						while ($row_supply = mysql_fetch_object($result_supply)) {
							$supply_username=$row_supply->name;
							$supply_weixin_fromuser= $row_supply->weixin_fromuser;
							$supply_weixin_name=$row_supply->weixin_name;
							if(!empty($supply_weixin_fromuser)){
								$supply_username = $supply_username."(".$supply_weixin_name.")";
							}
						}
					}
					/* 供应商 End  */

                     /* 计算F2C运费 Start  */
                     if($from_page == 0){
                         //分担运费修改按钮 false不显示 true显示
                         $freight_button = false;
                         //订单总价格$o_totalprice,商城订单邮费$o_express_price
                         $f2c_freight_is_change = 0;
                         //判断F2C是否需要分担邮费
                         if($o_totalprice >= $f2c_freight[0]['order_price']){
                             //需要分担邮费
                             //如果是拒单订单，显示按钮
                             if($o_is_sendorder == 2) $freight_button = true;

                             //查询该订单是否有改过价
                             $freight_query = "SELECT price FROM f2c_order_freight where customer_id = {$customer_id} and batchcode={$o_batchcode} and isvalid = true";
                             $result_freight = _mysql_query($freight_query) or die('query_supply failed: ' . mysql_error());
                             $row_freight= mysql_fetch_assoc($result_freight);
                             if($row_freight == false){
                                 $f2c_freight_price = $f2c_freight[0]['freight'];
                             }else{
                                 $f2c_freight_is_change = 1;
                                 $f2c_freight_price = $row_freight['price'];
                             }
                         }else{
                             $f2c_freight_price = 0;
                         }
                     }

                     /* 读取F2C运费设置 End  */

					if($from_page != 2){
						/* 订单状态  */
						$o_statusstr="<span class='btn btn-grey'>未完成</span><br>";
						if($o_status==1){
							$o_statusstr="<span class='btn btn-success'>已完成</span><br>";
						}else if($o_status==-1 && $o_payondelivery != 1){
							$o_statusstr="<span class='btn btn-danger'>顾客已取消</span><br>";
						}
						else if($o_status==0 && $o_payondelivery == 1 && $o_is_sign ==0)
						{
							$o_statusstr="<span class='btn btn-success'>待签收</span><br>";
						}
						else if($o_status==-1 && $o_payondelivery == 1 )
						{
							$o_statusstr="<span class='btn btn-danger' >已拒签</span><br>";
						}
						else if($o_status==0 && $o_payondelivery == 1 && $o_is_sign ==1)
						{
							$o_statusstr="<span class='btn btn-success' >已签收</span><br>";
						}

						if($o_status==1 && $o_payondelivery == 1 && $o_is_sign ==0)
						{
							$o_statusstr="<span class='btn btn-success'>待签收</span><br>";
						}
					}else{
						$o_statusstr="<span class='btn btn-grey'>待处理</span><br>";
						if($o_status==1){
							$o_statusstr="<span class='btn btn-success'>已完成</span><br>";
						}else if($o_status==-1 && $o_payondelivery != 1){
							$o_statusstr="<span class='btn btn-danger'>顾客已取消</span><br>";
						}
						else if($o_status==0 && $o_payondelivery == 1 && $o_is_sign ==0)
						{
							$o_statusstr="<span class='btn btn-success'>待签收</span><br>";
						}
						else if($o_status==-1 && $o_payondelivery == 1 )
						{
							$o_statusstr="<span class='btn btn-danger' >已拒签</span><br>";
						}
						else if($o_status==0 && $o_payondelivery == 1 && $o_is_sign ==1)
						{
							$o_statusstr="<span class='btn btn-success' >已签收</span><br>";

						}
						else if($o_status==0 && $o_payondelivery != 1 && $o_paystatus == 0)
						{
							$o_statusstr="<span class='btn btn-success' >待支付</span><br>";
						}
						else if($o_status==0 && $o_payondelivery != 1 && $o_paystatus == 1 && $o_sendstatus == 0 && $sso_is_accept == true)
						{
							$o_statusstr="<span class='btn btn-success' >待发货</span><br>";
						}
						else if($o_status==0 && $o_payondelivery != 1 && $o_paystatus == 1 && $o_sendstatus == 1 && $sso_is_accept == true)
						{
							$o_statusstr="<span class='btn btn-success' >已发货</span><br>";
						}
						else if( $o_payondelivery != 1 && $o_paystatus == 1 && ($o_sendstatus == 2 || $o_sendstatus == 4 || $o_sendstatus == 6) && $sso_is_accept == true)
						{
							$o_statusstr="<span class='btn btn-success' >待完成</span><br>";
						}

						if($o_status==1 && $o_payondelivery == 1 && $o_is_sign ==0)
						{
							$o_statusstr="<span class='btn btn-success'>待签收</span><br>";
						}
					}


					/* 新零售接单状态 End  */
					if($from_page > 0){
						if($sso_is_accept == true){
							$o_statusstr .= "</br><span class='btn btn-success'>零售:已接单</span>";
						}else{
							$o_statusstr .= "</br><span class='btn btn-grey'>零售:未接单</span>";
						}
					}
					/* 新零售接单状态 End  */

					if($o_aftersale_state > 0){
							$o_statusstr = $o_statusstr . "<span class='btn btn-warning'>";
						if($o_aftersale_state == 1){
							$o_statusstr = $o_statusstr . "申请售后维权";
						 }else if($o_aftersale_state == 2){
							  $o_statusstr = $o_statusstr . "同意售后维权";
						 }else if($o_aftersale_state == 3){
							  $o_statusstr = $o_statusstr . "驳回售后维权";
						 }else if($o_aftersale_state == 4){
							  $o_statusstr = $o_statusstr . "售后已处理完成";
						 }
						 $o_statusstr = $o_statusstr . "</span><br>";
						 $o_statusstr = $o_statusstr . "<span class='btn btn-success'>".($o_aftersale_type == 2 ? "退货":"换货")."</span><br>";
					}
					/* 订单状态 End  */



					/*  判断是否有返现 */
					$cashback_rows = 0;
					$query_cashback = "select id from cashback where batchcode='".$o_batchcode."'";
					$result_cashback = _mysql_query($query_cashback) or die('query_cashback failed: ' . mysql_error());
					$cashback_rows = mysql_num_rows($result_cashback);
					if($cashback_rows < 1){
						$query_cashback_t = "select id from cashback_t where batchcode='".$o_batchcode."'";
						$result_cashback_t = _mysql_query($query_cashback_t) or die('query_cashback_t failed: ' . mysql_error());
						$cashback_rows = mysql_num_rows($result_cashback_t);
					}

					/*//查询订单使用了多少购物币
					$settlementcurrency=0;//购物币
					$query = "select currency from order_currencyandcoupon_t where pay_batchcode='".$pay_batchcode."'";
					$result = _mysql_query($query) or die('Query_weipay_currency failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
						$settlementcurrency 	= $row->currency;
					}*/

					/*8.1.4需求：
					订单详情里面——订单信息里面增加：总优惠金额、使用购物币、使用积分、使用优惠券张数和总金额，当订单没有使用任何优惠时，为0 */

					if($o_store_id>0){
						$o_discount_price = $o_totalprice - $o_totalprice_last;
						//门店自提：总优惠金额：订单金额-实付金额
						//已付金额包括税款，需加回
						if($total_tax_type>1){
							$o_discount_price=(string)$o_discount_price +(string)$total_tax;
						}
					}else{
						if($o_express_price>0){
							$o_discount_price = $o_totalprice + $o_express_price - $o_totalprice_last;
						}else{
							//免邮
							$o_discount_price = $o_totalprice - $o_totalprice_last;
						}

						//已付金额包括税款，需加回
						if($total_tax_type>1){
							$o_discount_price= (string)$o_discount_price + (string)$total_tax;
						}

					//有运费：总优惠金额：订单金额+运费-实付金额
					}
					$o_discount_price = bcadd($o_discount_price,0,2);
					$o_pay_currency = $pay_currency;//使用购物币
					$o_needScore = $needScore;//使用积分
					//使用优惠券张数
					$coupons_count = 0;
					$query_coupon="select count(id) as coupons_count from weixin_commonshop_order_coupons where isvalid=true and batchcode='".$o_batchcode."'";
					$result_coupon = _mysql_query($query_coupon) or die('Query_change failed: ' . mysql_error());
					while ($row_coupon = mysql_fetch_object($result_coupon)) {
					    //获取订单的真实价格（可能是折扣总价）
					    $coupons_count = $row_coupon->coupons_count;
					}
					$o_coupons_count = $coupons_count;//使用优惠券张数
					//使用优惠券总金额 $o_CouponPrice

					/* 8.1.4需求结束 */

			 ?>
			 <tr class="CP_table_bianhao" >

				<td class="CP_table_bianhaoa" colspan="<?php echo $from_page > 0 ? 9 : 8 ;?>">
					<!--<input type="checkbox" name="code_Value" value="1">-->
					<span class="CP_table_bianhaob" >编号：<b onclick="showDetail('<?php echo $o_batchcode; ?>')"><?php echo $o_batchcode; ?></b>

					<?php
					if($o_agentcont_type==1){?>
					<img style="width:18px;height:18px;margin-left:2px" src="../../../common/images_V6.0/contenticon/dai.png" ondragstart="return false;" title="代理商订单" />
					<?php }
					if($o_supply_id>0){?>
					<img style="width:18px;height:18px;margin-left:2px" src="../../../common/images_V6.0/contenticon/gong.png" ondragstart="return false;" title="合作商订单" />
					<?php }
					if($o_is_QR==1){   ?>
					<img style="width:18px;height:18px;margin-left:2px" src="../../../common/images_V6.0/contenticon/coupon.png" ondragstart="return false;" title="二维码核销订单" />
					<?php }
					if($cashback_rows>0){?>
					<img style="width:18px;height:18px;margin-left:2px" src="../../../common/images_V6.0/contenticon/cashback2.png" ondragstart="return false;" title="赠送订单" />
					<?php }
					if( $o_delivery_time_start > 0 && $o_delivery_time_end > 0 && $o_paystatus == 1 ){?>
					<img style="width:18px;height:18px;margin-left:2px" src="../../../common/images_V6.0/contenticon/pre_delivery.png" ondragstart="return false;" title="预配送订单" />
					<?php }
					if( $o_is_collageActivities > 0 ){?>
					<img style="width:18px;height:18px;margin-left:2px" src="../../../common/images_V6.0/contenticon/is_head.png" ondragstart="return false;" title="拼团订单" />
					<?php }
					if($from_page == 0 and $o_is_sendorder == 2 ){?>
					<img style="width:18px;height:18px;margin-left:2px" src="../../../common/images_V6.0/contenticon/zhuan.png" ondragstart="return false;" title="F2C拒单" />
					<?php } ?>
					<span class="zhibo mb_order_<?php echo $o_batchcode;?>" style="display:none;" ondragstart="return false;">直播</span>
					<!-- <img class="mb_order_<?php echo $o_batchcode; ?>" style="width:20px;height:20px;margin-left:2px;display:none;" src="../../../common/images_V6.0/contenticon/zb_icon.png" ondragstart="return false;" title="直播订单" /> -->
					<?php
					if($total_tax_type>1){?>
					<span class="shui" ondragstart="return false;" title="行邮税">税</span>
					<?php }?>
					<span class="CP_table_bianhaod"><?php echo $o_createtime; ?></span>
					<?php
					if($o_CouponPrice>0){ ?>
					<span><img style="margin-right: -20px;" src="../../../common/images_V6.0/contenticon/pay-discount.png" /></span>
					<?php } ?>
					<span id="order_pay_<?php echo $o_batchcode; ?>" >
					<?php
					if($o_paystatus==0 ){ ?>
					<img src="../../../common/images_V6.0/contenticon/del-icon.png" /><span class="CP_table_bianhaoe">未支付</span>
					<a title="催单" style="cursor:pointer;" onclick="callPay('<?php echo $o_batchcode; ?>',<?php echo $o_totalprice_last; ?>)" ><img style="width:16px;height:18px;" src="../../../common/images_V6.0/contenticon/callback.png" /></a>
					<?php }elseif($o_paystatus==1 ){ ?>
					<img src="../../../common/images_V6.0/contenticon/pay-icon.png" /><span class="CP_table_bianhaof">已支付<?php if($o_Pay_Method==1){?><span style="color:red;">(后台支付)</span><?php }?></span>
					<span class="CP_table_bianhaog">支付订单号：<?php echo $pay_batchcode;?></span>
					<?php } ?>
					</span>

				<span class="CP_table_bianhaog">支付方式：<?php echo $o_paystyle;?></span>
				<span class="CP_table_bianhaoh">
				<?php
				$callBackBatchcode = "";
				if($o_paystatus==1 and $o_Pay_Method==0){
					$transaction_id=-1;
					if($o_paystyle=="通联支付"){
						echo "[<a href=\"allipay_detail.php?allipay_orderid=$o_allipay_orderid\">". $o_allipay_orderid ."(点击查看)</a>]";
					}elseif($o_paystyle=="微信支付" or $o_paystyle=="找人代付" or $o_paystyle=="兴业银行公众号支付" ){
						/* 微信支付 */
						$weipay = "select transaction_id from weixin_weipay_notifys where isvalid=true and out_trade_no='".$pay_batchcode."'";
						$result_weipay = _mysql_query($weipay) or die('Query_weipay failed: ' . mysql_error());
						while ($row_result_weipay = mysql_fetch_object($result_weipay)) {
							$transaction_id = $row_result_weipay->transaction_id;
						}
						if($wxpay_version==2){
							echo "[<a href=\"weipay_detail.php?allipay_orderid=".$transaction_id."&pay_batchcode=".$pay_batchcode."&batchcode=$o_batchcode\">". $transaction_id ."(点击查看)</a>]";
						}else{
							echo "[<a href=\"weipay_detail2.php?pay_batchcode=".$pay_batchcode."&batchcode=$o_batchcode\">". $transaction_id ."(点击查看)</a>]";
						}
						$settlementprice=0; //可以结算的金额
                        $settlementcurrency=0;
						$paySql = "select price,currency from paycallback_t where isvalid=true and pay_batchcode='".$pay_batchcode."' limit 0,1";
						$result_paySql = _mysql_query($paySql) or die('paySql failed: ' . mysql_error());
						while ($row_paySql = mysql_fetch_object($result_paySql)) {
							$settlementprice = $row_paySql->price;
                            $settlementcurrency = $row_paySql->currency;
						}
                        if($o_paystyle=="找人代付"){
                            $settlementprice = $settlementprice + $settlementcurrency;
                        }
						/* 微信支付End */
					}elseif($o_paystyle=="环迅快捷支付" || $o_paystyle=="环迅微信支付"){
						$paySql = "select callBackBatchcode,price from paycallback_t where isvalid=true and pay_batchcode='".$pay_batchcode."' limit 0,1";
						$result_paySql = _mysql_query($paySql) or die('paySql failed: ' . mysql_error());
						while ($row_paySql = mysql_fetch_object($result_paySql)) {
							$callBackBatchcode = $row_paySql->callBackBatchcode;
							$settlementprice = $row_paySql->price;
						}

						echo "[<a href=\"hxpay_detail.php?pay_batchcode=$pay_batchcode&batchcode=$o_batchcode\">". $callBackBatchcode ."(点击查看)</a>]";
					}elseif( $o_paystyle=="威富通支付" ){
						$wftpay = "select transaction_id,wft_type from system_order_pay_log where pay_batchcode='".$pay_batchcode."'";
						$result_wftpay = _mysql_query($wftpay) or die('Query_weipay failed: ' . mysql_error());
						while ($row_result_weipay = mysql_fetch_object($result_wftpay)) {
							$transaction_id = $row_result_weipay->transaction_id;
							$wft_type = $row_result_weipay->wft_type;
						}
						//echo $wft_type;
						echo "[<a href=\"wftpay_detail.php?allipay_orderid=".$transaction_id."&wft_type=".$wft_type."&pay_batchcode=".$pay_batchcode."&batchcode=$o_batchcode\">". $transaction_id ."(点击查看)</a>]";
					}elseif($o_paystyle=="健康钱包支付"){
                        $paySql = "select callBackBatchcode,price from paycallback_t where isvalid=true and pay_batchcode='".$pay_batchcode."' limit 0,1";
                        $result_paySql = _mysql_query($paySql) or die('paySql failed: ' . mysql_error());
                        while ($row_paySql = mysql_fetch_object($result_paySql)) {
                            $callBackBatchcode = $row_paySql->callBackBatchcode;
                            $settlementprice = $row_paySql->price;
                            break;
                        }
                        echo "[<a href=\"healthpay_detail.php?pay_batchcode=$pay_batchcode&batchcode=$o_batchcode\">". $callBackBatchcode ."(点击查看)</a>]";
                    }elseif($o_paystyle=="易宝支付"){
						$paySql = "select callBackBatchcode,price from paycallback_t where isvalid=true and pay_batchcode='".$pay_batchcode."' limit 0,1";
						$result_paySql = _mysql_query($paySql) or die('paySql failed: ' . mysql_error());
						while ($row_paySql = mysql_fetch_object($result_paySql)) {
							$callBackBatchcode = $row_paySql->callBackBatchcode;
                            $settlementprice = $row_paySql->price;
						}
						echo "[<a href=\"yeepay_detail.php?pay_batchcode=$pay_batchcode&batchcode=$o_batchcode\">". $callBackBatchcode ."(点击查看)</a>]";
					}elseif($o_paystyle=="京东支付"){
						$paySql = "select callBackBatchcode from paycallback_t where isvalid=true and pay_batchcode='".$pay_batchcode."' limit 0,1";
						$result_paySql = _mysql_query($paySql) or die('paySql failed: ' . mysql_error());
						while ($row_paySql = mysql_fetch_object($result_paySql)) {
							$callBackBatchcode = $row_paySql->callBackBatchcode;
						}
						echo "[<a href=\"jdpay_detail.php?pay_batchcode=$pay_batchcode&batchcode=$o_batchcode\">". $callBackBatchcode ."(点击查看)</a>]";
					}else{
						$settlementprice=0; //可以结算的金额
						$paySql = "select callBackBatchcode,price from paycallback_t where isvalid=true and pay_batchcode='".$pay_batchcode."' limit 0,1";
						$result_paySql = _mysql_query($paySql) or die('paySql failed: ' . mysql_error());
						while ($row_paySql = mysql_fetch_object($result_paySql)) {
							$callBackBatchcode = $row_paySql->callBackBatchcode;
							$settlementprice = $row_paySql->price;
						}
						echo "[<a href=\"pay_detail.php?pay_batchcode=$pay_batchcode\">". $callBackBatchcode ."(点击查看)</a>]";
					}
				}
				?>
				</span>
				<span style="margin-left: 250px;color: #E90B0B;">
					<?php

					if( $o_status == 0){
						switch($o_sendstatus){
							case 0:
								if( $o_paystatus == 1 ){
									$differ_day = $to_day->get_days($o_paytime);
									echo '已付款'.$differ_day.'天';
								}
							break;
							case 1:
								$differ_day = $to_day->get_days($o_confirm_sendtime);
								if( $differ_day >=0 ){
									echo '已发货'.$differ_day.'天';
								}
							break;
							case 2:
								if($o_aftersale_state == 0){
									$differ_day = $to_day->get_days($o_confirm_receivetime);
									echo '已收货'.$differ_day.'天';
								}
							break;
							case 3:case 4:case 5:
								if( $o_return_status == 0 ){
									$differ_day = $to_day->get_days($o_aftersale_time);
									if( $differ_day >=0 ){
										echo '已申请'.$differ_day.'天';
									}

								}
							break;
						}
					}

					?>
				</span>
				</td>
			 </tr>

			 <?php
					/* 产品信息 */

						$o2_prvalues        = "";
						$o2_prvalues_name	= "";
						$o2_rcount          = 0;
						$o2_prvalue         = "";
						$o2_merchant_remark = "";
						$o2_num             = 0;
						$o2_mb_order        = 0;
						$o2_imgurl          = 0;
						$o2_tax_type 		= 1;
						$o2_cost_price         = 0;//供货价
						$query_order2 = "SELECT orders2.rcount,
												orders2.prvalues,
												orders2.prvalues_name,
												orders2.totalprice,
												orders2.isvalid,
												orders2.merchant_remark,
												orders2.mb_order,
												product.name,
												product.foreign_mark,
												product.default_imgurl,
												product.id,
												product.cost_price,
												product.tax_type
										FROM weixin_commonshop_orders as orders2
										LEFT JOIN weixin_commonshop_products as product on product.id=orders2.pid
										where orders2.batchcode='".$o_batchcode."' and orders2.isvalid=true";
						$result_order2 = _mysql_query($query_order2) or die('Query_order2 failed: ' . mysql_error());
						$o2_rows = mysql_num_rows($result_order2);
						while ($row_order2 = mysql_fetch_object($result_order2)) {

							$o2_pid             = $row_order2->id;
							$o2_rcount          = $row_order2->rcount;
							$o2_isvalid         = $row_order2->isvalid;
							$o2_prvalue         = $row_order2->prvalues;
							$o2_prvalues_name	= $row_order2->prvalues_name;
							$o2_nane            = $row_order2->name;
							$o2_totalprice      = $row_order2->totalprice;
							$o2_foreign_mark    = $row_order2->foreign_mark;
							$o2_imgurl          = $row_order2->default_imgurl;
							$o2_merchant_remark = $row_order2->merchant_remark;
							$o2_totalprice      = $o2_totalprice / $o2_rcount;
							$o2_cost_price 		= $row_order2->cost_price;
							$o2_tax_type 		= $row_order2->tax_type;
							$o2_mb_order 		= $row_order2->mb_order;
							//echo $o2_tax_type;

							/* 产品图片 */
							if(empty($o2_imgurl)){
								$query_imgurl="select imgurl from weixin_commonshop_product_imgs where isvalid=true and  product_id=".$o2_pid." limit 0,1";
								$result_imgurl = _mysql_query($query_imgurl) or die('Query_imgurl failed: ' . mysql_error());
								while ($row_imgurl = mysql_fetch_object($result_imgurl)) {
									$o2_imgurl = $row_imgurl->imgurl;
								}
								if(empty($o2_imgurl)){
									$o2_imgurl = "../../../common/images_V6.0/contenticon/pic_miss.png";
								}
							}
							/* 产品图片 End */

							/*================工单：14323 属性里面有空格分隔会导致分隔问题，现在屏蔽 2017-6-29 by chy=================*/


							/* 产品属性 */
							/*$o2_prvstr="";
							if( !empty($o2_prvalues_name) and !empty($o2_prvalue) ){
								$o2_prvalues_name_arr = explode(" ",$o2_prvalues_name);
								for( $j=0;$j<count($o2_prvalues_name_arr);$j++ ){
									$o2_prvalues_name_arr2 = explode(":",$o2_prvalues_name_arr[$j]);
									$o2_prvstr .= $o2_prvalues_name_arr2[1]."  ";
								}

							} else if( !empty($o2_prvalue) ){

								$o2_prvarr = explode("_",$o2_prvalue);
								for( $i=0;$i<count($o2_prvarr);$i++ ){
									$o2_prvid = $o2_prvarr[$i];
									if( $o2_prvid>0 ){
										$query_pros = "SELECT name from weixin_commonshop_pros where  id=".$o2_prvid;
										$result_pros = _mysql_query($query_pros) or die('Query_pros failed: ' . mysql_error());
										while ($row_pros = mysql_fetch_object($result_pros)) {
										   $prname     = $row_pros->name;
										   $o2_prvstr .= $prname."  ";
										}
									}
								}


								$query_prod2="select foreign_mark from weixin_commonshop_product_prices where product_id=".$o2_pid." and proids='".$o2_prvalue."'";
								$result_prod2 = _mysql_query($query_prod2) or die('Query_prod2 failed: ' . mysql_error());
								while ($row_prod2 = mysql_fetch_object($result_prod2)) {
									 $o2_foreign_mark = $row_prod2->foreign_mark;
								}


							}*/
		 			 		/* 产品属性 End */

							//直接读数据库即可
							$o2_prvstr = $o2_prvalues_name;
							if(!empty($o2_prvalue)){
								$query_prod2="select foreign_mark from weixin_commonshop_product_prices where product_id=".$o2_pid." and proids='".$o2_prvalue."'";
								$result_prod2 = _mysql_query($query_prod2) or die('Query_prod2 failed: ' . mysql_error());
								while ($row_prod2 = mysql_fetch_object($result_prod2)) {
									 $o2_foreign_mark = $row_prod2->foreign_mark;
								}
							}

							/*================工单：14323 属性里面有空格分隔会导致分隔问题，现在屏蔽，直接读数据库即可 2017-6-29 by chy=================*/

						/*必填信息开关*/
					$query_op = "SELECT is_Pinformation from weixin_commonshops_extend where isvalid=true and customer_id=".$customer_id;
						$result_op = _mysql_query($query_op) or die('Query_op failed: ' . mysql_error());
						while ($row_op = mysql_fetch_object($result_op)) {
							$is_Pinformation  = $row_op->is_Pinformation;
						}
						/*必填信息开关end*/

						/*是否推广员身份*/
						$query_promoter = 'SELECT count(1) AS is_promoter FROM promoters WHERE customer_id='.$customer_id.' AND status=1 AND isvalid=true AND user_id='.$buy_user_id;
						$result_promoter = _mysql_query($query_promoter) or die('Query_promoter failed:'.mysql_error());
						$is_promoter = -1;	//是否推广员身份
						while ( $row_promoter = mysql_fetch_object($result_promoter) ){
							$is_promoter = $row_promoter -> is_promoter;
						}
						/*是否推广员身份*/
					/*判断是否为电商直播订单*/
					if( $o2_mb_order ){
			 ?>
			 <script>
				$('.mb_order_<?php echo $o_batchcode; ?>').show();
			 </script>
			 <?php
					}
					/*判断是否为电商直播订单*/
			 ?>
			 <tr class="CP_table_chanpina">
				<td>
					<input type="checkbox" name="input_checkbox" class="checkbox" b_totalprice="<?php echo $o_totalprice_last; ?>" b_id="<?php echo $o_batchcode; ?>">
				</td>
				<td class="CP_table_chanpina_one">
					<img src="<?php echo "//".$http_host.$o2_imgurl; ?>" />
					<span class="CP_table_chanpina_onep">
						<p><b><?php echo $o2_nane; ?></b></p>
						<p><b>¥<?php echo number_format($o2_totalprice,2); ?></b><span class="CP_table_chanpina_onepa"> 数量：<b><?php echo $o2_rcount; ?></b></span></p>
						<p>
							<span class="CP_table_chanpina_onepa">【属 性】：<?php echo $o2_prvstr; ?> </span>
							</br><span class="CP_table_chanpina_onepa">【外部标识】：<?php echo $o2_foreign_mark; ?> </span>
						</p>
						<!-- 必填信息 开始 -->
						<?php
						$query_m = "SELECT count(1) as num from weixin_commonshop_orders_requiredinformation_t where isvalid=true and pid=".$o2_pid." and batchcode=".$o_batchcode." limit 1";
						$result_m = _mysql_query($query_m) or die('Query_mess failed: ' . mysql_error());
						$row_m = mysql_fetch_object($result_m);
						$row_m = $row_m ->num;

						if( 0 < $row_m ){
						?>
						<div>
							<p><b>必填信息</b></p>
							<div class="mess_b" style="max-height: 48px;overflow: hidden;"><!-- 必填信息框架 -->
								<?php
								$query_mess = "SELECT information_head,information_con from weixin_commonshop_orders_requiredinformation_t where isvalid=true and pid=".$o2_pid." and batchcode=".$o_batchcode;
								$result_mess = _mysql_query($query_mess) or die('Query_mess failed: ' . mysql_error());
								while ($row_mess = mysql_fetch_object($result_mess)) {//查询信息
									$information_head   = $row_mess->information_head;
									$information_con    = $row_mess->information_con;
								?>

								<div  style="margin-left:20px;">
									<?php echo $information_head; ?>：<span><?php echo $information_con; ?></span>
								</div>
								<?php } ?>
							</div>
							<?php
							if( 3 < $row_m ){
							?>
							<span id="div1" val='1' onClick="showMore('<?php echo $o_batchcode;?>')" style="cursor:pointer">更多</span>
							<?PHP } ?>
						</div>
						<?php } ?>
					</span>
				</div>
				</td>
				<?php if($o2_num==0){ ?>
				<td class="CP_table_chanpina_two" rowspan="<?php echo $o2_rows; ?>">
					<p><?php echo $o_expressName."(".$o_weixin_name.")"; ?>
					<a title="微信对话" href="../../../weixin_inter/send_to_msg.php?fromuserid=<?php echo $o_weixin_fromuser; ?>&customer_id=<?php echo $customer_id_en; ?>" ><i class="order-comment"></i></a>
					</p>
					<p><?php echo $o_expressPhone; ?></p>
				</td>
				<td class="CP_table_chanpina_three" <?php if($from_page == 2){ echo "style = 'text-align: center;'"; } ?> id="table_three_<?php echo $o_batchcode; ?>" rowspan="<?php echo $o2_rows; ?>">
					<b>¥<?php echo number_format($o_totalprice_last,2)."元".$changeprice_str; ?></b><?php if($o_pay_currency>0){ echo "<br/><span>购物币：". $o_pay_currency ."</span>"; } ?><br/><span><?php if($o_express_price>0){ echo "(含运费 ¥". $o_express_price ."元)"; }else{ echo "免邮"; } ?></span>
					<?php if($o2_tax_type>1){?>
					</br><span>(含<?php echo $tax_name;?>税：<?php echo $total_tax;?>元)</span>
					<?php }?>
				</td>

				<td class="CP_table_chanpina_four " id="table_four_<?php echo $o_batchcode; ?>" rowspan="<?php echo $o2_rows; ?>">
					<p class="CP_table_chanpina_fourp"><?php echo $sendstatusstr; ?></p>
					<?php
							if($o_is_QR == 1){
								$QR_createtime = '';
								$query_QR = "select createtime from weixin_commonshop_order_logs where isvalid = true and batchcode='".$o_batchcode."' and operation=4";
								$result_QR = _mysql_query($query_QR) or die("query_QR failed : ".mysql_error());
								while($row_QR = mysql_fetch_object($result_QR)){
									$QR_createtime = $row_QR->createtime;
								}
								if(!empty($QR_createtime) && $QR_createtime!="0000-00-00 00:00:00"){
									$confirm_sendtimestr = "<p>发货时间:".$QR_createtime."</p>";
								}
							}
					?>
					<?php if(!empty($confirm_sendtimestr)){  echo $confirm_sendtimestr; } ?>

					<?php if(!empty($confirm_receivetimestr)){  echo $confirm_receivetimestr; } ?>
				</td>

				<td class="CP_table_chanpina_five" id="table_five_<?php echo $o_batchcode; ?>" rowspan="<?php echo $o2_rows; ?>">
				<?php echo $o_statusstr; ?>
				</td>
                <?php
                    if($from_page > 0){
                        $sso_proxy_id = empty($sso_proxy_id) ? -1 : $sso_proxy_id;
                        $sso_store_id = empty($sso_store_id) ? -1 : $sso_store_id;
                        if($from_page == 1){ //订货系统
							//查询零售负责人
                            $query_proxy = "select name, phone from ".WSY_DH.".orderingretail_proxy where id = ".$sso_proxy_id;
							//查询零售负责人 End
							//查询区域
                            $query_area = "select a.grade,a.areaname from ".WSY_DH.".orderingretail_area_set s inner join weixin_commonshop_team_area a on s.area_id = a.id  and s.id = ".$sso_store_id;
							//查询区域 End
                        }else{
							//查询零售负责人
                            $query_proxy = "select name, ac.phone from f2c_accounts as ac left join weixin_users as us on us.id=ac.user_id where ac.id = ".$sso_proxy_id;
							//查询零售负责人 End
							//查询区域
                            $query_area = "select a.grade,a.areaname from f2c_area_set s inner join weixin_commonshop_team_area a on s.area_id = a.id  and s.id = ".$sso_store_id;
							//查询区域 End
						}

                        $res_proxy = _mysql_query($query_proxy) or die(" sso query proxy error : ".mysql_error());
                        $proxy_name = "";
                        $proxy_phone = "";
                        if($row_proxy = mysql_fetch_object($res_proxy)){
                            $proxy_name  = $row_proxy -> name;
                            $proxy_phone = $row_proxy -> phone;
                        }
                        $res_store = _mysql_query($query_area) or die(" sso query store error : ".mysql_error());
                        $store_grade    = "";
                        $store_areaname = "";
                        if($row_store = mysql_fetch_object($res_store)){
                            $store_grade = $row_store -> grade;
                            $store_areaname = $row_store -> areaname;
                        }
                        $store_grade_name = "";
                        switch($store_grade){
                            case 0 :
                                $store_grade_name = "区";
                                break;
                            case 1 :
                                $store_grade_name = "市";
                                break;
                            case 2 :
                                $store_grade_name = "省";
                                break;
                            case 3 :
                                $store_grade_name = "自定义区域";
                                break;
                        }


                    ?>
                    <td class="CP_table_chanpina_five" id="table_five_<?php echo $o_batchcode; ?>" rowspan="<?php echo $o2_rows; ?>"><?php echo $proxy_name; ?> - <?php echo $proxy_phone; ?> <br/>
                        <?php echo $store_areaname; ?> - <?php echo $store_grade_name; ?> </td>
                <?php }?>
				<td class="CP_table_chanpina_five" rowspan="<?php echo $o2_rows; ?>">
					<?php



					if($o_exp_user_id>0){ echo '<p>推广员:<a title="推广员" href="../../Users/promoter/promoter.php?search_user_id=' . $o_exp_user_id . '&customer_id=' . $customer_id_en . '">' . $exp_user_name . '</a> <a title="微信对话" href="../../../weixin_inter/send_to_msg.php?fromuserid=' . $exp_fromuser . '&customer_id=' . $customer_id_en . '"><i class="order-comment"></i></a></p>'; }

					if($o_agent_id>0){ echo '<p>代理商:<a title="代理商" href="../../Mode/agent/agent.php?search_user_id=' . $o_agent_id . '&customer_id=' . $customer_id_en . '">' . $agent_username . '</a> <a title="微信对话" href="../../../weixin_inter/send_to_msg.php?fromuserid=' .$agent_weixin_fromuser. '&customer_id=' . $customer_id_en . '"><i class="order-comment"></i></a></p>';}
					if($o_supply_id>0){

					$p_str = '<p>合作商:<a title="合作商" href="../../Mode/supplier/supply.php?search_user_id=' . $o_supply_id . '&customer_id=' . $customer_id_en . '">' . $supply_username.'</a>';
					if(!empty($supply_weixin_fromuser)){
						$p_str .= '<a title="微信对话" href="../../../weixin_inter/send_to_msg.php?fromuserid=' .$supply_weixin_fromuser. '&customer_id=' . $customer_id_en . '"><i class="order-comment"></i></a>';
					}
					$p_str .= '</p>';
					echo $p_str; }
					if($o_supply_id<0 && $o_agent_id<0 && $o_exp_user_id<0){ echo "<p>无</p>";}
					?>
				</td>
				<td class="CP_table_chanpina_six" rowspan="<?php echo $o2_rows; ?>">
					<a title="订单详情" onclick="showDetail('<?php echo $o_batchcode; ?>')" ><img src="../../../common/images_V6.0/operating_icon/icon44.png" /></a>
					<a title="订单日志" onclick="showLog('<?php echo $o_batchcode; ?>')" ><img src="../../../common/images_V6.0/operating_icon/icon11.png" /></a>
					<?php
				if($o2_isvalid==1){
					if($o_sendstatus==1){
					?>
					<a title="延期收货" onclick="showDate('<?php echo $o_batchcode; ?>',<?php echo $o2_rows; ?>)" ><img src="../../../common/images_V6.0/operating_icon/icon53.png" /></a>
					<?php  }   if($o_status==0 and $o_sendstatus==0 and ($from_page == 0 || $from_page == 2) ){ ?>
					<a class="change_add_<?php echo $o_batchcode; ?>" title="修改收件地址" onclick="showAddress('<?php echo $o_batchcode; ?>',<?php echo $o2_rows; ?>)" ><img src="../../../common/images_V6.0/operating_icon/icon52.png" /></a>


					<?php }

                        if($o_status==0 and $o_sendstatus==0 and $from_page == 0 and $o_is_sendorder == 2 and $freight_button == true ){
                            ?>

                        <a title="修改分担运费" class="change_freight_<?php echo $o_batchcode; ?>"  onclick="editFreight('<?php echo $o_batchcode; ?>')" ><img src="../../../common/images_V6.0/operating_icon/icon05.png" /></a>

                    <?php }

					if($o_status==0 and $o_paystatus==0 and $o_payondelivery !=1){
						if($o_agentcont_type==0 and $o_supply_id<0){
					?>

					<a title="修改价格" onclick="showPrice('<?php echo $o_batchcode; ?>',<?php echo $o2_rows; ?>)" ><img src="../../../common/images_V6.0/operating_icon/icon05.png" /></a>

					<?php } ?>

					<a title="确认支付" data-batchcode="<?php echo $o_batchcode;?>" data-totalprice="<?php echo $o_totalprice_last;?>" onclick="payOrder(this)" ><img src="../../../common/images_V6.0/operating_icon/icon39.png" /></a>

					<?php }

					if($o_sendstatus==0 and ($o_paystatus==1 or $o_paystyle=="货到付款") and $o_supply_id<0 and $o_open_sendway==0 and $from_page == 0){ ?>

					<a id="button_print_<?php echo $o_batchcode; ?>" title="打印快递运单" class="print_delivery" style="display:none;"  onclick="print_delivery('<?php echo $o_batchcode; ?>')" ><img src="../../../common/images_V6.0/operating_icon/icon29.png" /></a>

					<a id="button_delivery_<?php echo $o_batchcode; ?>" title="发货"  onclick="showDelivery('<?php echo $o_batchcode; ?>')" ><img src="../../../common/images_V6.0/operating_icon/icon42.png" /></a>

					<?php }

					if($o_status==0 and $o_paystatus==1 and ($o_sendstatus==2 or $o_sendstatus==4 or $o_sendstatus==6) ){ ?>

					<a title="确认完成" class="confir" data-batchcode="<?php echo $o_batchcode;?>" data-totalprice="<?php echo $o_totalprice_last;?>" onclick="confirmOrder(this)" ><img src="../../../common/images_V6.0/operating_icon/icon23.png" /></a>


					<?php if($o_sendstatus!=4 and $o_sendstatus!=6){?>

					<a title="红包确认" class="red_<?php echo $o_batchcode; ?>" href="order_send_redpack.php?customer_id=<?php echo $customer_id_en; ?>&batchcode=<?php echo $o_batchcode; ?>" ><img src="../../../common/images_V6.0/operating_icon/icon55.png" /></a>

					<?php }} ?>

					<?php if($o_aftersale_state == 1){ ?>

						<a title="维权管理" data-batchcode="<?php echo $o_batchcode;?>"  onclick="returnAftersale(this)" ><img src="../../../common/images_V6.0/operating_icon/icon58.png" /></a>

					<?php }else if($o_aftersale_state == 2){ ?>

						<a title="确认维权完毕" data-batchcode="<?php echo $o_batchcode;?>" onclick="confirmAftersale(this)" ><img src="../../../common/images_V6.0/operating_icon/icon59.png" /></a>

					<?php } ?>

					<?php if($o_aftersale_state==4 && $o_supply_id>0 && !$o_isreducesupply){?>
						<a title="扣除合作商款项" class="reducesupply_btn" data-reduce-supply-batchcode="<?php echo $o_batchcode;?>"  onclick="reducesupply('<?php echo $o_batchcode;?>')" ><img src="../../../common/images_V6.0/operating_icon/icon57.png" /></a>
					<?php }?>

					<!--确认签收和拒绝签收-->
					<?php if($o_payondelivery == 1 && $o_is_sign == 0){ ?>
					<a title="确认签收" data-batchcode="<?php echo $o_batchcode;?>" data-totalprice="<?php echo $o_totalprice_last;?>" onclick="sign_yes(this,1)" ><img src="../../../common/images_V6.0/operating_icon/icon80.png" /></a>

					<a title="拒绝签收" data-batchcode="<?php echo $o_batchcode;?>" data-totalprice="<?php echo $o_totalprice_last;?>" onclick="sign_yes(this,2)" ><img src="../../../common/images_V6.0/operating_icon/icon81.png" /></a>
					<?php } ?>

					<?php
					if($o_sendstatus==3 and $o_supply_id<0 and $from_page == 0 and $o_open_sendway==0){

						if($o_return_status == 0){  //申请退货后审批

						?>
						<a title="退货管理" data-batchcode="<?php echo $o_batchcode;?>" data-reason="<?php echo $o_backgoods_reason;?>"  onclick="returnGood(this,<?php echo $o_return_type;?>)" ><img src="../../../common/images_V6.0/operating_icon/icon56.png" /></a>
						<?php

						}else if($o_return_status == 2){ //同意退货
							if($o_return_type == 0 ){ //退货，仅退款

							?>
							<a title="确定退款" data-refund-batchcode="<?php echo $o_batchcode;?>"  onclick="showGoodRefund('<?php echo $o_batchcode; ?>',1,this)" ><img src="../../../common/images_V6.0/operating_icon/icon57.png" /></a>
							<?php

							}else if($o_return_type == 2){ // 申请换货并且商家已同意

							?>
							<a title="确定已退货" data-batchcode="<?php echo $o_batchcode;?>" onclick="confirmGoodRefund(this)" ><img src="../../../common/images_V6.0/operating_icon/icon56.png" /></a>
							<a id="button_delivery_<?php echo $o_batchcode; ?>" title="发货" onclick="showDelivery('<?php echo $o_batchcode; ?>')" ><img src="../../../common/images_V6.0/operating_icon/icon42.png" /></a>
							<?php

							}

						}else if($o_return_status == 5 ){ //退货并且用户已发货
							if($o_return_type == 1){  //申请退货或换货都可以显示确定

							?>
							<a title="确定已退货" data-refund-all-batchcode="<?php echo $o_batchcode;?>" onclick="showGoodAll('<?php echo $o_batchcode;?>')" ><img src="../../../common/images_V6.0/operating_icon/icon56.png" /></a>
							<?php

							}
							if($o_return_type == 2){    //或可以直接发货

							?>
							<a title="确定已收到退货" data-batchcode="<?php echo $o_batchcode;?>" onclick="confirmGoodRefund(this)" ><img src="../../../common/images_V6.0/operating_icon/icon56.png" /></a>
							<a id="button_delivery_<?php echo $o_batchcode; ?>" title="发货" onclick="showDelivery('<?php echo $o_batchcode; ?>')" ><img src="../../../common/images_V6.0/operating_icon/icon42.png" /></a>
							<?php

							}
						}else if($o_return_status == 6){ //退货并且商家已确认收货 , 进行退款操作
							if($o_return_type == 1){

							?>
							<a title="确定退款" data-refund-batchcode="<?php echo $o_batchcode;?>"  onclick="showGoodRefund('<?php echo $o_batchcode; ?>',2,this)" ><img src="../../../common/images_V6.0/operating_icon/icon57.png" /></a>
							<?php

							}else if($o_return_type == 2){

							?>
							<a id="button_delivery_<?php echo $o_batchcode; ?>" title="发货" onclick="showDelivery('<?php echo $o_batchcode; ?>')" ><img src="../../../common/images_V6.0/operating_icon/icon42.png" /></a>
							<?php

							}
						}

					} ?>

					<?php
					// 由供应商/代理商发货  ，申请退货（仅退款） 。 代理商已同意后/
					if($o_sendstatus == 3 and ($o_supply_id > 0 || $o_open_sendway > 0 || $from_page > 0)){
						if($o_return_status == 2){ //同意退货
							if($o_return_type == 0 ){ //仅退款

							?>
							<a title="确定退款" data-refund-batchcode="<?php echo $o_batchcode;?>"  onclick="showGoodRefund('<?php echo $o_batchcode; ?>',1,this)" ><img src="../../../common/images_V6.0/operating_icon/icon57.png" /></a>
							<?php

							}
						}else if($o_return_status == 6){  //供应商已确认收货后
                            if($o_return_type == 1 ) { //退货
                                ?>

                                <a title="确定退款" data-refund-batchcode="<?php echo $o_batchcode;?>"
                                   onclick="showGoodRefund('<?php echo $o_batchcode; ?>',2,this)"><img
                                        src="../../../common/images_V6.0/operating_icon/icon57.png"/></a>
                            <?php
                            }
						}
					}

					if($o_sendstatus==5){
						if($o_return_status == 0 && $from_page <= 0){  //申请退款后审批

						?>
						<a title="退款管理" data-batchcode="<?php echo $o_batchcode;?>"  onclick="returnMoney(this)" ><img src="../../../common/images_V6.0/operating_icon/icon56.png" /></a>
						<?php

						}else if($o_return_status == 8){ //退款

						?>
						<a title="确定退款" data-refund-batchcode="<?php echo $o_batchcode;?>"  onclick="showGoodRefund('<?php echo $o_batchcode; ?>',0,this)" ><img src="../../../common/images_V6.0/operating_icon/icon57.png" /></a>
						<?php

						}

						if ( $is_promoter > 0 ){	//推广员身份显示删除推广员按钮
						?>
						<a title="删除推广员身份" data-refund-batchcode_id="<?php echo $o_batchcode;?>"  onclick="deletePromoter(<?php echo $buy_user_id;?>)" ><img src="../../../common/images_V6.0/operating_icon/icon25.png" /></a>
						<?
						}
					}

					if(($o_status==0 and $o_paystatus==0) or $o_status==1 or $o_status==-1){
						if($is_shopgeneral==0 or $is_generalcustomer==1){
					?>
					<a class="shanchu" title="删除" data-batchcode="<?php echo $o_batchcode;?>"  onclick="delOrder(this)" ><img src="../../../common/images_V6.0/operating_icon/icon04.png" /></a>
					<?php
						}
					}
				}?>

				<?php if($o_paystatus==1 ){?>
				<a title="返佣记录" href="order_rebate_log.php?batchcode=<?php echo $o_batchcode; ?>&customer_id=<?php echo passport_encrypt($customer_id)?>=&class=1&from_page=<?php echo $from_page; ?>" ><img src="../../../common/images_V6.0/operating_icon/icon51.png" /></a>
				<?php }?>

				<?php if($cashback_rows>0){?>
				<a title="赠送记录" href="order_cashback_log.php?batchcode=<?php echo $o_batchcode; ?>&customer_id=<?php echo passport_encrypt($customer_id)?>"><img src="../../../common/images_V6.0/operating_icon/icon72.png" /></a>
				<?php }?>


				</td>

				<?php $o2_num++; } ?>
			 </tr>
			 <?php } ?>


          <!--订单详情开始·定位属性-->
          <tr class="WSY_positiontrhide">
          	<td colspan="11" class="order_td">
            	<div class="order order_hide div_show" id="order_<?php echo $o_batchcode; ?>" >
					<i class="guanbi" onclick="hideDetail()" ><img class="WSY_modifypimg" src="../../../common/images_V6.0/contenticon/gbicon.png" alt=""></i><!--点击关闭信息-->
                	<dl class="order_dl01">
                        <dt><a>订单信息</a></dt>
                        <div class="order_div">
                            <dd><b>订单号：</b><span><?php echo $o_batchcode; ?></span></dd>
                            <dd><b>下单时间：</b><span><?php echo $o_createtime; ?></span></dd>
                            <dd><b>支付时间：</b><span><?php echo $o_paytime; ?></span></dd>
                            <dd><b>支付方式：</b><span><?php echo $o_paystyle; ?></span></dd>
								<?php if(!empty($o_sendstyle)){  ?>
                            <?php if(empty($o_store_name)){
								?>
							<dd><b>收货方式：</b><span><?php echo $o_sendstyle; ?>(<?php echo $new_expressname;?>)</span>
								<?php } ?>
							</dd>
								<?php } if(!empty($agent_username)){   ?>
									<dd><b>代理商：</b><span><?php echo $agent_username; ?></span></dd>
								<?php } if(!empty($supply_username)){  ?>
                           <!-- <dd><b>合作商：</b><span><?php echo $supply_username; ?></span></dd> -->

								<?php } ?>
							<?php
							if( !empty($invoice_head) ){
							?>
							<dd><b>发票抬头：</b><span><?php echo $invoice_head; ?></span></dd>
							<?php } ?>
							<dd><b>买家姓名：</b><span><?php echo $o_name; ?></span></dd>

							<dd><b>微信名称：</b><span><?php echo $o_weixin_name; ?></span></dd>
							<dd><b>买家电话：</b><span><?php echo $o_phone; ?></span></dd>
							<?php if($o_CouponPrice>0){?><dd><b>优惠券：</b><span class="WSY_red"><?php  echo  "￥".$o_CouponPrice."元";?></span></dd><?php }?>
							<?php if($o_store_id>0){ ?>
							<dd><b>门店自提：</b><span class="WSY_red">
							<?php
										echo $o_store_name;

							?></span>
							</dd>
							<?php }else{?>
							<dd><b>邮费：</b><span class="WSY_red">
							<?php
										if($o_express_price>0) echo  "￥".$o_express_price."元";else echo "免邮";

							}?></span>
							</dd>
							<?php if($total_tax_type>1){?>
							<dd><b><?php echo  get_tax_name($total_tax_type).'税'?>：</b><span class="WSY_red">
							<?php
									echo  "￥".$total_tax;

							}?></span>
							</dd>
							<?php if( $o_delivery_time_start > 0 && $o_delivery_time_end > 0 && $o_paystatus == 1 ){ ?>
								<dd><b>预配送时间：</b><span><?php echo $o_delivery_time_start.' 至 '.$o_delivery_time_end; ?></span></dd>
							<?php }?>
							<?php
							if($o_sendtime!=''){
							?>
							<dd><b>送货时间：</b><span><?php echo $o_sendtime; ?></span></dd>
							<?php
							}
															?>
                        </div>
                        <div class="order_div">
								<dd><b>订单金额：</b><span class="WSY_red">￥<?php echo $o_totalprice; ?>元</span></dd>
								<dd><b>总优惠金额：</b><span class="WSY_blue">￥<?php echo $o_discount_price; ?>元</span></dd>
								<dd><b>使用购物币：</b><span><?php echo $o_pay_currency; ?></span></dd>
								<dd><b>使用积分：</b><span><?php echo $o_needScore; ?></span></dd>
								<dd><b style="width: 112px;">使用优惠券张数：</b><span><?php echo $o_coupons_count; ?></span></dd>
								<dd><b style="width: 126px;">使用优惠券总金额：</b><span><?php echo $o_CouponPrice; ?></span></dd>
								<dd><b>实付金额：</b><span class="WSY_red" id="order_price_<?php echo $o_batchcode; ?>">￥<?php echo number_format($o_totalprice_last,2); ?>元</span></dd>
                        </div>
                    </dl>
                    <dl class="order_dl02">
                        <form>
                        <dt><a>收货信息</a></dt>
                        <div class="order_div01">
                            <dd><b>收货人：</b><span data-name="<?php echo $o_batchcode; ?>"><?php echo $o_expressName; ?></span></dd>
                            <dd><b>收货电话：</b><span data-phone="<?php echo $o_batchcode; ?>"><?php echo $o_expressPhone; ?></span></dd>
                            <dd><b>收货地址：</b><span title="<?php echo $o_location_p . $o_location_c . $o_location_a . $o_expressAddress; ?>" class="order_span_break" data-add="<?php echo $o_batchcode; ?>"><?php echo $o_location_p . $o_location_c . $o_location_a . $o_expressAddress; ?></span></dd>
                            <dd><b>订单备注：</b><span class="order_span_break" ><?php echo $o_remark; ?></span></dd>

							<dd class="WSY_bottonli" style="float:none;">
								<b>商家备注：</b>
								<textarea class="merchant_remark_<?php echo $o_batchcode;?>  merchant_remark" name="merchant_remark"  <?php if(!empty($o2_merchant_remark)){?>disabled="disabled" <?php } ?>><?php echo $o2_merchant_remark; ?></textarea>

								<input type="button" style="<?php if(empty($o2_merchant_remark)){?>display:none<?php } ?>;margin-left:0;" class="change_merchant_remark<?php echo $o_batchcode?> change_remark" value="修改" onclick="change_merchant_remark('<?php echo $o_batchcode;?>')">
								<input type="button"  style="<?php if(!empty($o2_merchant_remark)){?>display:none<?php } ?>;margin-left:0;" class="save_merchant_remark_<?php echo $o_batchcode?> change_remark" type="button" value="保存" onclick="save_merchant_remark('<?php echo $o_batchcode;?>')">

							</dd>
                            <dd><b>物流公司：</b><span id="express_id2_<?php echo $o_batchcode; ?>">

									<?php
									if($o_sendstatus==0 && $o_express_id<0){
										echo "未发货";
									}else{
										switch($o_express_id){
											case -2: echo "顺丰进口";break;
											case 0: echo "虚拟发货";break;
											default:
												// if($o_supply_id>0){
												// 	$query_express = 'SELECT expresses_name FROM weixin_expresses_company where isvalid=true and supply_id='.$o_supply_id.' and customer_id='.$customer_id.' and id ='.$o_express_id;
												// }else{
												// 	$query_express = 'SELECT expresses_name FROM weixin_expresses_company where isvalid=true and customer_id='.$customer_id.' and id ='.$o_express_id;
												// }
												// $result_express = _mysql_query($query_express) or die('Query_express_send failed: ' . mysql_error());
												// while ($row_express = mysql_fetch_object($result_express)) {
												// 	$e_name = $row_express->name;
												// 	echo $e_name;
											 //    }
											 echo $o_expressname_new;
										}
									}

									?>
                            </span></dd>
                        </div>
                        <div class="order_div01">
								<?php if(!empty($o_identity)){ ?><dd><b>身份证号：</b><span><?php echo $o_identity; ?></span></dd><?php } ?>
							<?php if(!empty($identityimgt)){ ?><dd>
								<b>身份附件：</b>&nbsp;&nbsp;<a href="<?php echo $identityimgt ?>" target="_blank" title="身份证正面"><img style="width:100px; height:60px;" src="<?php echo $identityimgt ?>"/></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $identityimgf ?>" target="_blank" title="身份证反面"><img style="width:100px; height:60px;" src="<?php echo $identityimgf ?>"/></a>
								</dd><?php } ?>
                           <dd><b>物流单号：</b><span><input type="text" disabled="disabled"  id="express_num2_<?php echo $o_batchcode; ?>" value="<?php echo $o_expressnum;?>" ></span>
								<?php if(!empty($o_expressnum)){?>
								<span class="order_kuaidi" onclick="KuaiDi100('<?php echo $o_batchcode; ?>')" >(点击查看物流)</span><?php }	?>
								</dd>
								<dd class="order_ddleft"><b>物流备注：</b>
								<span><textarea class="textarea01" disabled="disabled" id="express_remark2_<?php echo $o_batchcode; ?>"><?php echo $o_send_remarks; ?></textarea></span>
								</dd>
                        </div>
                        </form>
                    </dl>

						<?php  if($o_supply_id>0){  ?>
                    <dl class="order_dl04">
                        <dt><a>留言信息</a><i></i></dt>

                        <dd class="order_dd_hidden_<?php echo $o_batchcode; ?>" style="max-height: 150px;overflow-y: auto;width: 98%;display: inline-block;">
							<?php
								   $query_msg = "SELECT message,createtime,type,sub_supplier_id FROM weixin_commonshop_supply_message where isvalid=true and batchcode='".$o_batchcode."'";
								   $result_msg = _mysql_query($query_msg) or die('Query_msg failed: ' . mysql_error());
								   while ($row_msg = mysql_fetch_object($result_msg)) {
									  $msg_message= $row_msg->message;
									  $msg_createtime = $row_msg->createtime;
									  $msg_type = $row_msg->type;
									  $sub_supplier_id = $row_msg->sub_supplier_id;
							?>
                            <div class="order_dl04_div">
                                <h3>
										<?php
										if($msg_type==1){
											$msg_user = $supply_username;
										}else if ($msg_type==2){
											$query_subname = 'SELECT username FROM weixin_commonshop_supply_users where id='.$sub_supplier_id;
											$result_subname = _mysql_query($query_subname) or die("L2380 Query_subname error : ".mysql_error());
											$msg_user = $supply_username.":".mysql_result($result_subname,0,0);
										}else{
											$msg_user = "<a>我</a>";
										}
										echo "<a>" . $msg_user ."</a>  留言于  ".$msg_createtime;
										?>

							    </h3>
                                <p style="text-align: center;"><?php echo $msg_message; ?></p>
                            </div>
								  <?php  }  ?>
                        </dd>


						<dd><textarea class="textarea02" name="order_talk_<?php echo $o_batchcode; ?>" id="order_talk_<?php echo $o_batchcode; ?>" placeholder="输入您想留言的信息"></textarea></dd>

						<input type="hidden" name="supply_id_<?php echo $o_batchcode; ?>" id="supply_id_<?php echo $o_batchcode; ?>" value="<?php echo $o_supply_id; ?>" />
						<dd class="WSY_bottonli" style="border:none">
							<input type="button" value="留言" onclick="message('<?php echo $o_batchcode; ?>');">
							<input type="button" value="取消" onclick="hideDetail()">
						</dd>

                    </dl>
						<?php  }  ?>
             </div>
				<!--订单详情End-->

				<?php if($o_sendstatus == 0 or ($o_sendstatus == 3 && $o_return_type == 2) ){ ?>
				<!--订单发货-->
            	<div class="order order_delivery_dl order_hide div_show" id="delivery_<?php echo $o_batchcode; ?>"  >
					<i class="guanbi" onclick="hideDetail()" ><img class="WSY_modifypimg" src="../../../common/images_V6.0/contenticon/gbicon.png" alt=""></i><!--点击关闭信息-->
                    <dl>
                        <form>
                        <dt><a>收货信息</a></dt>
                        <div class="order_div01">
                            <dd><b>收货人：</b><span data-name="<?php echo $o_batchcode; ?>"><?php echo $o_expressName; ?></span></dd>
                            <dd><b>收货电话：</b><span data-phone="<?php echo $o_batchcode; ?>"><?php echo $o_expressPhone; ?></span></dd>
                            <dd><b>收货地址：</b><span title="<?php echo $o_location_p . $o_location_c . $o_location_a . $o_expressAddress; ?>" class="order_span_break" data-add="<?php echo $o_batchcode; ?>"><?php echo $o_location_p . $o_location_c . $o_location_a . $o_expressAddress; ?></span></dd>
                            <dd><b>订单备注：</b><span class="order_span_break" ><?php echo $o_remark; ?></span></dd>
							<dd class="WSY_bottonli" style="float:none;">
								<b>商家备注：</b>
								<textarea class="merchant_remark_<?php echo $o_batchcode;?>  merchant_remark" name="merchant_remark"  disabled="disabled" ><?php echo $o2_merchant_remark; ?></textarea>

							</dd>
                            <dd><b>物流公司：</b><span>
                                <select id="express_id_<?php echo $o_batchcode; ?>">

									<?php if($open_virtual_cust == 1 && $o_payondelivery != 1){ ?>
                                    <option value="0">虚拟发货</option>
									<?php } ?>
										   <?php
										   $sf_id = -1;
										   $query_sf = 'SELECT id FROM sf_import where ison=1 and customer_id='.$customer_id." limit 1";
										   $result_sf = _mysql_query($query_sf) or die('Query sf_import: ' . mysql_error());
										   while ($row_sf = mysql_fetch_object($result_sf)) {
											  $sf_id= $row_sf->id;
										   }
										   if($sf_id>0 ){ ?>
										   <option value="-2"  <?php if($o_express_id == -2){ ?>selected<?php } ?> >顺丰进口</option>
										   <?php }
										   if($o_supply_id>0){
												$query_express = 'SELECT id,expresses_name FROM weixin_expresses_company where isvalid=true and supply_id='.$o_supply_id.' and customer_id='.$customer_id." order by is_default desc";
										   }else{
												$query_express = 'SELECT id,expresses_name FROM weixin_expresses_company where isvalid=true and customer_id='.$customer_id." and supply_id=-1 order by is_default desc";
										   }
										   $result_express = _mysql_query($query_express) or die('Query_express failed: ' . mysql_error());
										   while ($row_express = mysql_fetch_object($result_express)) {
											  $e_id= $row_express->id;
											  $e_name = $row_express->expresses_name;
										   ?>
											<option value="<?php echo $e_id; ?>" <?php if($o_express_id == $e_id){ ?>selected<?php } ?>><?php echo $e_name; ?></option>
										   <?php } ?>
                                </select>
                            </span><span><a style="color:blue" href="../../Distribution/express/express_company.php?customer_id=<?php echo $customer_id_en;?>">点击添加物流公司</a></span></dd>

                        <dd class="WSY_bottonli con-button"><input  type="button" value="确定发货" class="order_delivery" data-totalprice="<?php echo $o_totalprice_last;?>" data-batchcode="<?php echo $o_batchcode;?>" is_pay_on_delivery ="<?php echo $o_payondelivery ?>" ></dd>
                        <dd class="WSY_bottonli con-button2"><input  type="button" value="取消" onclick="hideDetail()"></dd>
                        </div>
                        <div class="order_div01">
								  <?php if(!empty($o_identity)){ ?><dd><b>身份证号：</b><span><?php echo $o_identity; ?></span></dd><?php } ?>
                            <dd><b>物流单号：</b><span><input type="text" placeholder="虚拟发货可不用填" id="express_num_<?php echo $o_batchcode; ?>" ></span></dd>
                            <dd class="order_ddleft"><b>物流备注：</b>
								 <span><textarea class="textarea01" placeholder="写上要留言的信息" id="express_remark_<?php echo $o_batchcode; ?>"></textarea></span>
								 </dd>
                        </div>
                        </form>
                    </dl>
              </div>
				<!--订单发货End-->
				<?php  }
				if($o_status==0 and $o_sendstatus==0 and ($from_page == 0 || $from_page == 2) ){ ?>
				<!--修改收件地址-->
            	<div class="WSY_modifydiv order_hide div_show" id="address_<?php echo $o_batchcode; ?>" >
                    <dl class="order_dl_gaijia">
                        <dt><a>修改收件地址</a></dt>
                        <dd class="order_dl_add"><b>收件人姓名：</b><span><input type="text" id="address_name_<?php echo $o_batchcode; ?>" value="<?php echo $o_expressName; ?>"></span></dd>
                        <dd class="order_dl_add"><b>收件人手机：</b><span><input type="text" id="address_phone_<?php echo $o_batchcode; ?>" value="<?php echo $o_expressPhone; ?>"></span></dd>
                        <dd class="order_dl_add"><b>省级：</b><span><select name="address_p_<?php echo $o_batchcode; ?>" id="address_p_<?php echo $o_batchcode; ?>" ></select></span></dd>
                        <dd class="order_dl_add"><b>市级：</b><span><select name="address_c_<?php echo $o_batchcode; ?>" id="address_c_<?php echo $o_batchcode; ?>" ></select></span></dd>
                        <dd class="order_dl_add"><b>区级：</b><span><select name="address_a_<?php echo $o_batchcode; ?>" id="address_a_<?php echo $o_batchcode; ?>" ></select></span></dd>
                        <dd class="order_dl_add"><b>详细地址：</b><span><input type="text" name="address_add_<?php echo $o_batchcode; ?>" id="address_add_<?php echo $o_batchcode; ?>" value="<?php echo $o_expressAddress; ?>"></span></dd>
                        <dd class="WSY_bottonli con-button"><input  type="button" value="确定" class="order_add" data-batchcode="<?php echo $o_batchcode;?>"></dd>
                        <dd class="WSY_bottonli con-button2"><input  type="button" value="取消" onclick="hideDetail()"></dd>
                    </dl>
             </div>
				<script type="text/javascript">
					new PCAS('address_p_<?php echo $o_batchcode; ?>', 'address_c_<?php echo $o_batchcode; ?>', 'address_a_<?php echo $o_batchcode; ?>', '<?php echo $o_location_p; ?>', '<?php echo $o_location_c; ?>', '<?php echo $o_location_a; ?>');
				</script>
				<!--修改收件地址End-->
				<?php  }

                if($o_status==0 and $o_sendstatus==0 and $from_page == 0 and $o_is_sendorder == 2){ ?>

                    <div class="WSY_modifydiv order_hide div_show" id="freight_<?php echo $o_batchcode; ?>" >
                        <dl class="freight_dl_gaijia">
                            <dt><a>修改分担运费</a></dt>
                            <dd class="order_dl_add"><b>订单运费：</b>
							<span id="freight_p_<?php echo $o_batchcode; ?>">
							<?php
							if($f2c_freight_price>0){ echo "¥". $f2c_freight_price ."元"; }else{ echo "免邮"; }
							if($f2c_freight_is_change == 1){ echo '(改价后)'; } ?>
							</span>
							</dd>
                            <dd class="order_dl_add"><b>更改为：</b><span><input type="text" id="freight_price_<?php echo $o_batchcode; ?>" value=""></span></dd>
                            <dd class="WSY_bottonli con-button"><input  type="button" value="提交" class="change_freight" data-batchcode="<?php echo $o_batchcode;?>"></dd>
                            <dd class="WSY_bottonli con-button2"><input  type="button" value="取消" onclick="hideDetail()"></dd>
                        </dl>
                    </div>

                <?php }

				if($o_sendstatus==1){  ?>
				<!-- 延期收货 -->
            	<div class="WSY_modifydiv order_hide" id="date_<?php echo $o_batchcode; ?>" >
                    <dl class="order_date">
                       <dt><a>延期收货</a></dt>
						   <dd><b style="width:145px;">当前自动收货时间：</b><br/><span style="width:200px;margin-left:14px;" id="date_time_<?php echo $o_batchcode; ?>" ><?php echo $row_order->auto_receivetime;?></span></dd>
                       <dd><b style="width:65px;">延期</b><span>
									<input type="text" id="data_delay_<?php echo $o_batchcode; ?>" value="3"></span>
									<b style="width:40px;">天</b>
							</dd>
                        <dd class="WSY_bottonli con-button"><input  type="button" value="确定" class="order_delay" data-batchcode="<?php echo $o_batchcode;?>" data-is_delay="<?php echo $o_is_delay;?>"></dd>
                        <dd class="WSY_bottonli con-button2"><input  type="button" value="取消" onclick="hideDetail()"></dd>
                    </dl>
				</div>
				<!-- 延期收货End -->
				<?php }
				if( $o_sendstatus==3 || ( $o_sendstatus==5 && ($o_return_status==8 or $o_return_status==0 or $o_return_status==6) )){
				?>
				<!-- 退款 -->
            	<div class="WSY_modifydiv order_hide div_show" id="refund_<?php echo $o_batchcode; ?>" >
                    <dl class="order_date" style="min-width: 280px;">
						<dt>
							<a>退款</a>
						</dt>
						<dd>
							<b style="width:145px;">当前申请金额：</b>
							<span><?php echo $row_order->return_account;?></span>
						</dd>

						<?php if($o_supply_id>0){?>
						<dd>
							<b style="width:145px;">结算合作商金额：</b>
							<span>
								<input type="text"  id="good_supply_refund_<?php echo $o_batchcode;?>" value="0" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" onchange="clearNoNum(this)">
								<input type="hidden" id="good_cost_price_<?php echo $o_batchcode;?>" value="<?php echo $o_SupplyCostMoney;?>">
								<input type="hidden" id="good_expressfee_<?php echo $o_batchcode;?>" value="<?php echo $o_SupplyExpressPrice;?>">
							</span>
							<b style="width:40px;">元</b>
							<span style="font-size:12px;width:100%;text-align:left;color:#f00;">(最多结算給合作商 <?php echo $o_SupplyCostMoney+$o_SupplyExpressPrice;?>元)</span>
						</dd>
						<?php }?>
						<dd>
							<b style="width:145px;">实际退款金额：</b>
							<span>
								<input type="text" onkeyup="clearNoNum(this)" id="apply_good_refund_<?php echo $o_batchcode;?>" value="<?php echo $row_order->return_account;?>" disabled>
							</span>
							<b style="width:40px;">元</b>
						</dd>
						<?php
						if( $o_paystyle != "购物币支付"){
						?>
						<dd>
							<b style="width:145px;"><?php echo $o_paystyle; ?>退款金额：</b>
							<span>
								<input type="text" id="good_refund_<?php echo $o_batchcode;?>" value="0" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" onchange="clearNoNum(this)">
							</span>
							<b style="width:40px;">元</b>
							<span style="font-size:12px;width:100%;color:#f00;">(使用<?php echo $o_paystyle; ?>方式支付了 <?php echo $settlementprice; ?>元)</span>
						</dd>
						<dd
                        <?php
						if( $o_paystyle == "找人代付"){
                            echo 'style="text-align: right;margin-right: 96px;display:none;"';
                        }else{
                            echo 'style="text-align: right;margin-right: 96px;"';
                        } ?>
                        >
						+
						</dd>
						<?php
						}
						?>
						<?php

						?>


						<dd
                        <?php
						if( $o_paystyle == "找人代付"){
                            echo 'style="text-align: right;margin-right: 96px;display:none;"';
                        } ?>
                        >
							<b style="width:145px;">购物币退款金额：</b>
							<span>
								<input type="text" id="currency_refund_<?php echo $o_batchcode;?>" value="0" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" onchange="clearNoNum(this)">
							</span>
							<b style="width:40px;">元</b>
							<span style="font-size:12px;width:100%;color:#f00;">(该订单使用了 <?php echo $pay_currency;?>购物币)</span>
						</dd>
                        <dd class="WSY_bottonli con-button"><input  type="button" value="确定" class="good_refund" data-batchcode="<?php echo $o_batchcode;?>" data-money="<?php echo $row_order->return_account;?>" data-paystyle="<?php echo $o_paystyle; ?>"></dd>
                        <dd class="WSY_bottonli con-button2"><input  type="button" value="取消" onclick="hideDetail()"></dd>
                    </dl>
             </div>
				<!-- 退款 End -->
				<?php }  ?>

				<?php if($o_status==0 and $o_paystatus==0 and $o_agentcont_type==0 and $o_supply_id<0){ ?>
				<!--修改价格·定位属性-->
					<div class="WSY_modifydiv order_hide div_show" id="price_<?php echo $o_batchcode; ?>" >
						<dl class="order_dl_gaijia">
							<dt><a>修改价格</a></dt>
							<dd><b>订单价格：</b><span class="WSY_red">￥<?php echo number_format($o_totalprice_last,2); ?>元</span></dd>
							<dd><b>现价：</b><span><input type="text" value="<?php echo $o_totalprice_last; ?>" id="change_price_<?php echo $o_batchcode; ?>"></span></dd>
							<dd class="WSY_bottonli con-button"><input  type="button" value="确定改价" class="order_price" data-batchcode="<?php echo $o_batchcode;?>" data-price="<?php echo $o_totalprice_last;?>"  ></dd>
							<dd class="WSY_bottonli con-button2"><input  type="button" onclick="hideDetail()" value="取消"></dd>
						</dl>
				 </div>
			  <!--修改价格·定位属性-->
				<?php } ?>

				<!-- 退货(确认收到退货) -->
            	<div class="WSY_modifydiv order_hide div_show" id="refund_all_<?php echo $o_batchcode; ?>" >
                    <dl class="order_refund" style="width:25%;min-width: 280px;">
						<dt><a style="width:120px">退货(已收到退货)</a></dt>
						   <dd><b style="width:145px;">当前申请金额：</b><span><?php echo $row_order->return_account;?></span></dd>
						<dd>
							<b style="width:145px;">实际可退款金额：</b>
							<span>
								<input type="text" id="good_refund_money_<?php echo $o_batchcode;?>" value="<?php echo $row_order->return_account;?>">
							</span>
							<b style="width:40px;">元</b>
						</dd>

						<?php if($o_supply_id){?>
						<dd>
							<b style="width:145px;">结算合作商金额：</b>
							<span>
								<input type="text"  id="good_supply_refund_<?php echo $o_batchcode;?>" value="0" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" onchange="clearNoNum(this)">
								<input type="hidden" id="good_cost_price_<?php echo $o_batchcode;?>" value="<?php echo $o_SupplyCostMoney;?>">
								<input type="hidden" id="good_expressfee_<?php echo $o_batchcode;?>" value="<?php echo $o_SupplyExpressPrice;?>">
							</span>
							<b style="width:40px;">元</b>
							<span style="font-size:12px;width:100%;text-align:left;color:#f00;">(最多结算給合作商 <?php echo $o_SupplyCostMoney+$o_SupplyExpressPrice;?>元)</span>
						</dd>
						<?php }?>

                       <dd><b style="width:145px;">备注：</b><span>
									<textarea class="textarea01" id="good_refund_remark_<?php echo $o_batchcode;?>" ></textarea></span>
							</dd>
                        <dd class="WSY_bottonli con-button"><input  type="button" value="确定" class="good_refund_all" data-batchcode="<?php echo $o_batchcode;?>" data-money="<?php echo $row_order->return_account;?>" ></dd>
                        <dd class="WSY_bottonli con-button2"><input  type="button" value="取消" onclick="hideDetail()"></dd>
                    </dl>
             </div>
				<!-- 退货(确认收到退货) End -->

				<?php if($o_aftersale_state==4){?>
				<!-- 扣除供应商款项 -->
            	<div class="WSY_modifydiv order_hide" id="reduce_<?php echo $o_batchcode; ?>" >
                    <dl class="order_date">
                       <dt><a style="width:110px;">扣除合作商款项</a></dt>

						<dd>
							<b style="width:65px;">扣除</b><span>
								<input type="text" id="data_reduce_<?php echo $o_batchcode; ?>" value="0" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" onchange="clearNoNum(this)"></span>
							<b style="width:40px;">元</b>
							<span style="font-size:12px;width:100%;color:#f00;">(最多扣除合作商 <?php echo $o_SupplyCostMoney+$o_SupplyExpressPrice;?>元)</span>
						</dd>
                        <dd class="WSY_bottonli con-button"><input  type="button" value="确定" class="order_reduce" data-batchcode="<?php echo $o_batchcode;?>" data-money="<?php echo number_format($o_totalprice_last,2);?>" data-supplymoney="<?php echo $o_SupplyCostMoney+$o_SupplyExpressPrice;?>"></dd>
                        <dd class="WSY_bottonli con-button2"><input  type="button" value="取消" onclick="hideDetail()"></dd>
                    </dl>
				</div>
				<!-- 扣除供应商款项end -->
				<?php }?>


				<!-- 订单日志 -->
            	<div class="WSY_modifydiv order_hide div_show" id="log_<?php echo $o_batchcode; ?>" >
                    <dl class="order_log">
                        <dt><a>订单日志</a></dt>
							<?php
								$query_log = "select operation,descript,operation_user,createtime from weixin_commonshop_order_logs where isvalid = true and batchcode='".$o_batchcode."'";
								$result_log = _mysql_query($query_log) or die("Query_log failed : ".mysql_error());
								while($row_log = mysql_fetch_object($result_log)){
							?>
							<dd>
								<b>时间：</b><span><?php echo $row_log->createtime;?></span>
								<b>操作：</b>
								<span>
							<?php
									$op_str = "";
									$op = $row_log->operation;									//0：下单；1：取消；2：支付；3：修改价格；4：发货：5：申请延期；6：确认延期；7：确认收货；8：退货；9：退货审批；10：退款；11：退款审批；12：退款；13：用户退货填单；14：商家确认退货；';
									//获取用户售后上传的图片
									if($op == 8 || $op == 18 ){
										$query_ref_img = "select images from weixin_commonshop_order_rejects where isvalid=true  and batchcode='".$o_batchcode."'";
										$images_ref_img = '';
										$images_ref_img_arr = array();
										$result_ref_img = _mysql_query($query_ref_img) or die("Query_ref_img failed : ".mysql_error());
										while($row_ref_img = mysql_fetch_object($result_ref_img)){
											$images_ref_img = $row_ref_img->images;
										}
										if(!empty($images_ref_img)){
											$images_ref_img_arr = explode('|',$images_ref_img);
											$images_ref_img_count = count($images_ref_img_arr);
										}
									}
									switch($op){
										case 0 :$op_str = "下单";break;
										case 1 :$op_str = "取消";break;
										case 2 :$op_str = "支付";break;
										case 3 :$op_str = "修改价格";break;
										case 4 :$op_str = "发货";break;
										case 5 :$op_str = "申请延期";break;
										case 6 :$op_str = "确认延期";break;
										case 7 :$op_str = "确认收货";break;
										case 8 :$op_str = "退货";break;
										case 9 :$op_str = "退货审批";break;
										case 10 :$op_str = "退款";break;
										case 11 :$op_str = "退款审批";break;
										case 12 : $op_str = "退款操作";break;
										case 13 :$op_str = "用户退货填单";break;
										case 14 :$op_str = "商家确认退货";break;
										case 15 :$op_str = "退货完成";break;
										case 16 :$op_str = "确认完成";break;
										case 17 :$op_str = "订单评价";break;
										case 18 :$op_str = "申请维权";break;
										case 19 :$op_str = "维权审批";break;
										case 20 :$op_str = "维权处理";break;
										case 21 :$op_str = "微信退款";break;
										case 22 :$op_str = "订单删除";break;
										case 23 :$op_str = "维权扣除合作商款项";break;
                                        case 30 :$op_str = "系统派单";break;
									}
									echo $op_str;
								?>
								</span>
								<b>描述：</b><span><?php echo $row_log->descript;?></span>
								<b>操作人：</b><span><?php echo $row_log->operation_user;?></span>
							</dd>
							<?php } ?>
                    </dl>
             </div>
				<!-- 订单日志End -->

            </td>
          </tr>
			<?php  }  ?>

		</table>
	</div>

	<!--翻页开始-->
	<div class="WSY_page">
	</div>
	<!--翻页结束-->

     <!--表格结束-->
    </div>

   </div>
    <!--订单管理代码结束-->


</div>
</div>
<div class="batchFinish"></div>
<div class="wait_div">
	<i class="wx_loading_icon"></i>
	<p class="wait">请等待...</p>
</div>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script src="../../../common/js/floatBox.js"></script>
<script type="text/javascript">
//-------------上传文件效果
	$(function() {

		$("input[type=file]").change(function() {
			$(this).parents(".uploader").find(".filename").val($(this).val());
		});

		$("input[type=file]").each(function() {
			if ($(this).val() == "") {
				$(this).parents(".uploader").find(".filename").val("请选择文件...");
			}
		});

		/*订单归属分类开始*/
		$('#search_attribution_type').change(function(){
			var type = $('#search_attribution_type').val();
			if(type<=0){
				$('#search_order_ascription').hide();
				$('#search_supply').hide();
				$('#search_agent').hide();
				return;
			}else if(type==1){
				$('#search_supply').show();
				$('#search_agent').hide();
				$('#search_order_ascription').show();
			}else if(type==2){
				$('#search_order_ascription').show();
				$('#search_supply').hide();
				$('#search_agent').show();
			}
				$.ajax({
				type: "post",
				url:'order.class.php?customer_id='+customer_id,
				data:{'op':'attribution','type':type},
				dataType:"json",
				success:function(res){
					var html = "";
					html += "<option value='-1' >-- 请选择 --</option>";
					$.each(res,function(i,val){
						html += "<option value='"+val.sup_user_id+"'>"+val.sup_userName+"</option>";
					});
					$('#search_order_ascription').html(html);
				},
				error:function(){
					layer.alert('加载出错');
				}
			});
		});
		$('#search_order_ascription').change(function(){
			var type = $('#search_attribution_type').val();
			if(type<=0){
				$('#search_supply_id').val('');
				$('#search_agent_id').val('');
				return;
			}else if(type==1){
				$('#search_supply_id').val('');
			}else if(type==2){
				$('#search_agent_id').val('');
			}
		});
		/*订单归属分类结束*/

	});

</script>
<!--内容框架结束-->
<script>
<!-- 分页 --start-->
    var from_page = "<?php echo $from_page ?>";
	var f2c_id = "<?php echo $f2c_id ?>";
	var store_id = "<?php echo $param_store_id ?>";
	var customer_id = "<?php echo passport_encrypt($customer_id);?>";
	var pagenum = <?php echo $pagenum ?>;
	var count =<?php echo $page ?>;//总页数
	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
		pageCount:count,
		current:pagenum,
		backFn:function(p){
		var url="order.php?customer_id="+customer_id+"&pagenum="+p+"&from_page="+from_page+"&f2c_id="+f2c_id+"&store_id="+store_id;
		search_condition(url);
	   }
	});

  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="order.php?customer_id="+customer_id+"&pagenum="+a+"&from_page="+from_page+"&f2c_id="+f2c_id+"&store_id="+store_id;
		search_condition(url);

	}
  }
<!-- 分页 --end-->


<!-- 自动刷新 -->
var refer_time=10;
var refer_left_time=0;
var refer_ing=false;
function auto_refer(){
	if($('#auto_refer').is(':checked')){
		if(refer_left_time<refer_time){
			$('#search_form div .WSY_righticon_li04 label').html('<span><strong>'+(refer_time-refer_left_time)+'</strong></span>秒后自动刷新');
			refer_left_time++;
		}else{
				url=window.location.href;
				if(url.indexOf("&isauto=1")==-1)
			　{
			　　url=window.location.href+"&isauto=1";
			　}
				location.href=url;
		}
	}else{
		$('#search_form div .WSY_righticon_li04 label').html('自动刷新订单');
		refer_left_time=0;
		refer_ing=false;
		url=window.location.href;
		if(url.indexOf("&isauto=1")>0)
		{
			url=url.replace("&isauto=1","");
			location.href=url;
		}
	}
	setTimeout(auto_refer, 1000);
};
auto_refer();
<!-- 自动刷新 --end-->

<!-- 订单提醒 -->
function chkremind(){
	var urls='';
	if($('#order_remind').is(':checked')){
		urls='order_save_remind.php?op=1&customer_id='+customer_id+"&order_remind=1";
	}else{
		urls='order_save_remind.php?op=1&customer_id='+customer_id+"&order_remind=0";
	}

	$.ajax({
	type:"GET",
	url:urls,
	dataType:"jsonp",
	success: function(results){
		if(results[0].status==1){
			console.log("open remind");
		}else if(results[0].status==0){
			console.log("close remind");
			//parent.location.reload();
		}
	}
	});
}
<!-- 订单提醒 --end-->



<!-- 关闭订单详情 -->
function hideDetail(){
	$(".order_hide").fadeOut("slow");
}
<!-- 关闭订单详情 --end-->

<!-- 显示订单详情 -->
function showDetail(batchcode){
	var div = $("#order_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
<!-- 显示订单详情 --end-->

<!-- 显示订单日志 -->
function showLog(batchcode){
	var div = $("#log_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
<!-- 显示订单日志 --end-->

<!-- 显示修改运费 -->
function editFreight(batchcode){
    var div = $("#freight_"+batchcode);
    console.log(batchcode);
//    alert(div);
    $(".div_show").not(div).hide();
    if(div.is(":hidden")){
        div.fadeIn("slow");
    }else{
        div.fadeOut("slow");
    }
}
<!-- 显示修改运费 --end-->

<!-- 显示订单发货 -->
function showDelivery(batchcode){
	var div = $("#delivery_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
<!-- 显示订单发货 --end-->

<!-- 显示订单发货 -->
function showPrice(batchcode,num){
	if(num>1){
		layer.alert("单个商品才能修改价格！");
		return;
	}
	var div = $("#price_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
<!-- 显示订单发货 --end-->

<!-- 显示修改地址发货 -->
function showAddress(batchcode){
	var div = $("#address_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
<!-- 显示订单发货 --end-->

<!-- 显示延期发货 -->
function showDate(batchcode){
	var div = $("#date_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
<!-- 显示延期发货 --end-->


<!-- 显示退款 -->
var returntype=-1;
function showGoodRefund(batchcode,retype,obj){
batchcode = $(obj).data('refund-batchcode');
	//console.log(batchcode);
	returntype = retype;
	var div = $("#refund_"+batchcode);
	$(".div_show").not(div).hide();
	var _A = div.is(":hidden");
	console.log(_A);
	if(_A){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
<!-- 显示退款 --end-->

<!-- 显示退货确认 -->
function showGoodAll(batchcode){
	var div = $("#refund_all_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
<!-- 显示退货确认 --end-->

<!-- 显示扣除供应商款项 -->
function reducesupply(batchcode){


	var div = $("#reduce_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
<!-- 显示扣除供应商款项 --end-->

<!-- 修改地址 -->
$(".order_add").click(function(){
	var batchcode = $(this).data('batchcode');
	layer.confirm('您确认要修改 订单:'+batchcode+' 的收货地址信息吗', {
		btn: ['修改收货信息','取消']
	}, function(confirm){

		var addressName = $("#address_name_"+batchcode).val();
		var addressPhone = $("#address_phone_"+batchcode).val();
		var addressP = $("#address_p_"+batchcode).val();
		var addressC = $("#address_c_"+batchcode).val();
		var addressA = $("#address_a_"+batchcode).val();
		var addressAdd = $("#address_add_"+batchcode).val();

		/* var isPhone = /^([0-9]{3,4}-)?[0-9]{7,8}$/;
		var isMob=/^((\+?86)|(\(\+86\)))?(13[0123456789][0-9]{8}|15[012356789][0-9]{8}|18[0123456789][0-9]{8}|14[57][0-9]{8}|17[3678][0-9]{8})$/; */

		if(addressName=="" || addressPhone=="" || addressP=="" || addressC=="" || addressA=="" || addressAdd=="" ){
			layer.alert("请输入完整的收件人信息", function(index){layer.close(index);});
			return;
		}
		if((/^\s+$/g).test(addressName)){
			layer.alert("请输入正确的姓名", function(index){layer.close(index);});
			return;
		}
		/* if(!isMob.test(addressPhone)&&!isPhone.test(addressPhone)){
			layer.alert("请输入正确的电话号码", function(index){layer.close(index);});
			return;
		} */
        if( !chkPhoneNumber(addressPhone) && !chk400(addressPhone) ){
            layer.alert("请输入正确的电话号码", function(index){layer.close(index);});
			return;
        }
		if((/^\s+$/g).test(addressAdd)){
			layer.alert("请输入正确的详细地址", function(index){layer.close(index);});
			return;
		}

		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'addressName':addressName,'addressPhone':addressPhone,'addressP':addressP,'addressC':addressC,'addressA':addressA,'addressAdd':addressAdd,'from_page':from_page,'op':"changeAdd"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					$("span[data-add='"+batchcode+"']").text(addressP+addressC+addressA+addressAdd);
					$("span[data-name='"+batchcode+"']").text(addressName);
					$("span[data-phone='"+batchcode+"']").text(addressPhone);
					showAddress(batchcode);
				}
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

});
<!-- 修改地址 End -->

<!-- 运费改价 START-->
$(".change_freight").click(function(){
    var batchcode = $(this).data('batchcode');
    layer.confirm('您确认要修改 订单:'+batchcode+' 的运费价格吗', {
        btn: ['改价','取消']
    }, function(confirm){

        var changePrice = $("#freight_price_"+batchcode).val();
        if(isNaN(changePrice)){
            layer.alert("请输入正确的金额！");
            return;
        }

        layer.close(confirm);
        layer_open();
        $.ajax({
            url: "order.class.php",
            type:"POST",
            data:{'batchcode':batchcode,'changePrice':changePrice,'op':"changeFreightPrice",'from_page':from_page},
            dataType:"json",
            success: function(res){
                layer.close(index_layer);
                if(res.status==0){
                    changePrice = parseFloat(changePrice).toFixed(2);
                    $('#freight_p_'+batchcode).text('￥'+changePrice+'元(改价后)');
//                    $('#price_'+batchcode).find('dd').find('.WSY_red').text('￥'+changePrice+'元');
//                    $('#table_three_'+batchcode).find('b').html('￥'+changePrice+'元<span style="color:#dd514c;margin-left: 4px;">(改价后)</span>');
//                    $('#order_price_'+batchcode).text('￥'+changePrice+'元');
                    editFreight(batchcode);
                }
                if(res.errcode>0){
                    layer.alert(res.errmsg);
                }else{
                    layer.alert(res.msg);
                }
            },
            error:function(){
                layer.close(index_layer);
                layer.alert("网络错误请检查网络");
            }
        });
    }, function(){
        layer.msg('已取消', {
            time: 4000,
            btn: ['确认'],
            icon:1
        });
    });

});
<!-- 运费改价 End-->

<!-- 改价 -->
$(".order_price").click(function(){
	var batchcode = $(this).data('batchcode');
	layer.confirm('您确认要修改 订单:'+batchcode+' 的价格吗', {
		btn: ['改价','取消']
	}, function(confirm){

		var changePrice = $("#change_price_"+batchcode).val();
		if(isNaN(changePrice)){
			layer.alert("请输入正确的金额！");
			return;
		}

		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'changePrice':changePrice,'op':"changPirce"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					changePrice = parseFloat(changePrice).toFixed(2);
					$('#price_'+batchcode).find('dd').find('.WSY_red').text('￥'+changePrice+'元');
					$('#table_three_'+batchcode).find('b').html('￥'+changePrice+'元<span style="color:#dd514c;margin-left: 4px;">(改价后)</span>');
					$('#order_price_'+batchcode).text('￥'+changePrice+'元');
				}
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

});
<!-- 改价 --end-->

<!-- 催单 -->
function callPay(batchcode,price){
	layer.confirm('是否要对 订单:'+batchcode+' 进行催单？', {
		btn: ['催单','取消']
	}, function(confirm){

		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'price':price,'op':"callPay"},
			dataType:"json",
			success: function(res){
				layer.close(index_layer);
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

}
<!-- 催单 --end-->

<!-- 退货管理 -->
function returnGood(obj,return_type){
	var batchcode = $(obj).data('batchcode');
	var reason = $(obj).data('reason');
	layer.confirm('顾客申请退货理由:'+reason+'<br/>请选择 订单:'+batchcode+' 的申请退货操作', {
		btn: ['同意退货','拒绝']
	}, function(confirm){

		layer.close(confirm);
		layer.prompt({
			formType: 0,
			title: '同意退货备注',
			value: '同意退货申请'
		},function(reason, prompt, elem){
			layer.close(prompt);
			layer_open();
			$.ajax({
				url: "order.class.php",
				type:"POST",
				data:{'batchcode':batchcode,'reason':reason,'status':1,'op':"confirmReturnGood"},
				dataType:"json",
				success: function(res){
					 layer.close(index_layer);
					if(res.status==0){
						if(return_type==1){
							$(obj).remove();
						}else if(return_type==2){
							$(obj).replaceWith('<a title="确定已退货" data-batchcode="'+batchcode+'" onclick="confirmGoodRefund(this)"><img src="../../../common/images_V6.0/operating_icon/icon56.png"></a><a id="button_delivery_19450514967343994280" title="发货" onclick="showDelivery(\''+batchcode+'\')"><img src="../../../common/images_V6.0/operating_icon/icon42.png"></a>');
						}else{
							$(obj).replaceWith('<a onclick="showGoodRefund('+batchcode+',1,this)" data-refund-batchcode="'+batchcode+'" title="确定退款"><img src="../../../common/images_V6.0/operating_icon/icon57.png"></a>');
						}
						$("#table_four_"+batchcode+" p:first-child").append('<b style="color:#C9302C"> [已同意]</b>');
					}
					if(res.errcode>0){
						layer.alert(res.errmsg);
					}else{
						layer.alert(res.msg);
					}
				},
				error:function(){
					layer.close(index_layer);
					layer.alert("网络错误请检查网络");
				}
			});

		});

	}, function(confirm2){
		layer.close(confirm2);
		layer.prompt({
			formType: 0,
			title: '拒绝退货备注',
			value: '拒绝退货申请'
		},function(reason, prompt, elem){
			layer.close(prompt);
			if(!reason || reason  == ""){
				layer.alert("驳回请输入理由！");
				return;
			}
			layer_open();
			$.ajax({
				url: "order.class.php",
				type:"POST",
				data:{'batchcode':batchcode,'reason':reason,'status':2,'op':"confirmReturnGood"},
				dataType:"json",
				success: function(res){
					 layer.close(index_layer);
					if(res.status==0){
						$(obj).remove();
						$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/affirm-icon.png"><b style="color:#31B0D5"> 已发货</b>');
					}
					if(res.errcode>0){
						layer.alert(res.errmsg);
					}else{
						layer.alert(res.msg);
					}
				},
				error:function(){
					layer.close(index_layer);
					layer.alert("网络错误请检查网络");
				}
			});

		});

	});

}
<!-- 退货管理 --end-->

<!-- 退款管理 -->
function returnMoney(obj){
	var batchcode = $(obj).data('batchcode');
	layer.confirm('请选择 订单:'+batchcode+' 的申请退款操作', {
		btn: ['同意退款','拒绝','取消']
	}, function(confirm){
		layer.close(confirm);
		layer.prompt({
			formType: 0,
			title: '同意退款备注',
			value: '同意退款申请'
		},function(reason, prompt, elem){
			layer.close(prompt);
			layer_open();
			$.ajax({
				url: "order.class.php",
				type:"POST",
				data:{'batchcode':batchcode,'reason':reason,'status':1,'op':"confirmReturnMoney"},
				dataType:"json",
				success: function(res){
					 layer.close(index_layer);
					 console.log(res);
					if(res.status==0){
						$(obj).replaceWith('<a title="确定退款" data-refund-batchcode="'+batchcode+'" onclick="showGoodRefund(\''+batchcode+'\',0,this)"><img src="../../../common/images_V6.0/operating_icon/icon57.png"></a>');
						$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/return-money.png"> <b style="color:#C9302C">顾客申请退款</b><b style="color:#C9302C"> [已同意]</b>');
					}
					if(res.errcode>0){
						layer.alert(res.errmsg);
					}else{
						layer.alert(res.msg);
					}
				},
				error:function(){
					layer.close(index_layer);
					layer.alert("网络错误请检查网络");
				}
			});
		});
	}, function(confirm2){
		layer.close(confirm2);
		layer.prompt({
			formType: 0,
			title: '拒绝退款备注',
			value: '拒绝退款申请'
		},function(reason, prompt, elem){
			layer.close(prompt);
			if(!reason || reason  == ""){
				layer.alert("驳回请输入理由！");
				return;
			}
			layer_open();
			$.ajax({
				url: "order.class.php",
				type:"POST",
				data:{'batchcode':batchcode,'reason':reason,'status':2,'op':"confirmReturnMoney"},
				dataType:"json",
				success: function(res){
					 layer.close(index_layer);
					if(res.status==0){
						$(obj).remove();
						$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/notaffirm-icon.png"> <b>未发货</b>');
					}
					if(res.errcode>0){
						layer.alert(res.errmsg);
					}else{
						layer.alert(res.msg);
					}
				},
				error:function(){
					layer.close(index_layer);
					layer.alert("网络错误请检查网络");
				}
			});
		});
	}, function(confirm3){
		layer.close(confirm2);
	});
}
<!-- 退货管理 --end-->

<!-- 维权管理 -->
function returnAftersale(obj){
	var batchcode = $(obj).data('batchcode');
	layer.confirm('请选择 订单:'+batchcode+' 的申请维权操作', {
		btn: ['同意维权','驳回']
	}, function(confirm){
		layer.close(confirm);
		layer.prompt({
			formType: 0,
			title: '同意维权备注',
			value: '同意维权申请'
		},function(reason, prompt, elem){
			layer.close(prompt);
			layer_open();
			$.ajax({
				url: "order.class.php",
				type:"POST",
				data:{'batchcode':batchcode,'reason':reason,'status':1,'op':"confirmReturnAftersale"},
				dataType:"json",
				success: function(res){
					 layer.close(index_layer);
					if(res.status==0){
						$(obj).replaceWith('<a title="确认维权完毕" data-batchcode="'+batchcode+'" onclick="confirmAftersale(this)"><img src="../../../common/images_V6.0/operating_icon/icon59.png"></a>');
						$("#table_five_"+batchcode+" .btn-warning").html('同意售后维权');
					}
					if(res.errcode>0){
						layer.alert(res.errmsg);
					}else{
						layer.alert(res.msg);
					}
				},
				error:function(){
					layer.close(index_layer);
					layer.alert("网络错误请检查网络");
				}
			});
		});
	}, function(confirm2){
		layer.close(confirm2);
		layer.prompt({
			formType: 0,
			title: '驳回维权备注',
			value: '驳回维权申请'
		},function(reason, prompt, elem){
			layer.close(prompt);
			if(!reason || reason  == ""){
				layer.alert("驳回请输入理由！");
				return;
			}
			layer_open();
			$.ajax({
				url: "order.class.php",
				type:"POST",
				data:{'batchcode':batchcode,'reason':reason,'status':2,'op':"confirmReturnAftersale"},
				dataType:"json",
				success: function(res){
					 layer.close(index_layer);
					if(res.status==0){
						$(obj).remove();
						$("#table_five_"+batchcode+" .btn-warning").html('驳回售后维权');
					}
					if(res.errcode>0){
						layer.alert(res.errmsg);
					}else{
						layer.alert(res.msg);
					}
				},
				error:function(){
					layer.close(index_layer);
					layer.alert("网络错误请检查网络");
				}
			});
		});
	});
}
<!-- 维权管理 --end-->

<!-- 退款 -->
$(".good_refund").click(function(){
	var batchcode 			= $(this).data('batchcode');
	var refundMoney_old 	= $(this).data('money');
	var paystyle			= $(this).data('paystyle');
	var refundMoney 		= $("#good_refund_"+batchcode).val();
	if( refundMoney == undefined){
		refundMoney = 0;
	}
	
	var warning_str = '';
	if(paystyle == '健康钱包支付'){
		warning_str = '微信支付、威富通支付、健康钱包支付的订单请先到支付详情界面进行手动退款！';
	}else{
		warning_str = '微信支付和威富通支付的订单请先到支付详情界面进行手动退款！';
	}
	
	var refundSupplyMoney 	= Math.abs($("#good_supply_refund_"+batchcode).val());//退回给供应商的金额
	var currencyMoney		= $("#currency_refund_"+batchcode).val();
	var total_refundMoney	= Number(currencyMoney) + Number(refundMoney);
	var cost_price 			= Math.abs($("#good_cost_price_"+batchcode).val());
	var expressfee 			= Math.abs($("#good_expressfee_"+batchcode).val());

	console.log(cost_price+expressfee);
	layer.confirm('订单:'+batchcode+'</br>将退款 <b style="color:red">'+parseFloat(total_refundMoney).toFixed(2)+'</b> 元</br><b style="color:red">'+warning_str+'</b>', {
		btn: ['退款','取消']
	}, function(confirm){
		if( parseFloat(total_refundMoney) > parseFloat(refundMoney_old) ){
			layer.alert("退款金额不能大于订单总额！");
			return;
		}
		if(isNaN(total_refundMoney) || parseFloat(total_refundMoney) > parseFloat(refundMoney_old)){
			layer.alert("请输入正确的金额！");
			return;
		}

		//console.log(parseFloat(refundSupplyMoney),parseFloat(cost_price+expressfee));
		// if(isNaN(refundSupplyMoney) || parseFloat(refundSupplyMoney) > parseFloat(cost_price+expressfee)){
			// layer.alert("结算合作商金额不能大于供货价加运费！");
			// return;
		// }

		/*if(isNaN(refundSupplyMoney) || parseFloat(refundMoney) > parseFloat(cost_price+expressfee)){
			layer.alert("退款金额不能大于供货价("+parseFloat(cost_price)+"元)加运费("+parseFloat(expressfee)+"元)！");
			return;
		}*/

		//尹志雄修改 2017年2月28日16:57:39 CRM7319
		// if(isNaN(refundSupplyMoney) > parseFloat(refundSupplyMoney-cost_price+expressfee) ||
			// parseFloat(refundMoney) > parseFloat(refundMoney-cost_price+expressfee) ){
			// layer.alert("退款金额不能大于可退金额【总金额减去供货价("+parseFloat(cost_price)+"元)和运费("+parseFloat(expressfee)+"元)】！");
			// return;
		// }

		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'totalprice':refundMoney,'currencyMoney':currencyMoney,'refundSupplyMoney':refundSupplyMoney,'retype':returntype,'op':"goodRefund"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					if(returntype==0){
						$('a[data-refund-batchcode='+batchcode+']').replaceWith('<a onclick="confirmOrder(this)" data-totalprice="'+refundMoney_old+'" data-batchcode="'+batchcode+'" title="确认完成"><img src="../../../common/images_V6.0/operating_icon/icon23.png"></a>');
						//$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/affirm-icon.png"> <b style="color:#1eaf4e">退货已确认(仅退款)</b>');
						$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/refund-success.png"> <b style="color:#1eaf4e">退款完成</b>');
						$('a[data-refund-batchcode_id='+batchcode+']').hide();
					}else if(returntype==1){
						$('a[data-refund-batchcode='+batchcode+']').replaceWith('<a onclick="confirmOrder(this)" data-totalprice="'+refundMoney_old+'" data-batchcode="'+batchcode+'" title="确认完成"><img src="../../../common/images_V6.0/operating_icon/icon23.png"></a>');
						$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/confirm-return.png"> <b style="color:#1eaf4e">退货已确认(仅退款)</b>');
					}else if(returntype==2){
						$('a[data-refund-batchcode='+batchcode+']').replaceWith('<a onclick="confirmOrder(this)" data-totalprice="'+refundMoney_old+'" data-batchcode="'+batchcode+'" title="确认完成"><img src="../../../common/images_V6.0/operating_icon/icon23.png"></a>');
						$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/confirm-return.png"> <b style="color:#1eaf4e">退货已确认(退货)</b>');
					}
					$(".order_hide").fadeOut("slow");
				}
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

});
<!-- 退款 End -->

<!-- 申请退货(且退款) -->
$(".good_refund_all").click(function(){
	var batchcode = $(this).data('batchcode');
	var refundGoodMoney_old = $(this).data('money');
	var refundGoodMoney = $("#good_refund_money_"+batchcode).val();
	var refundRemark = $("#good_refund_remark_"+batchcode).val();
	var refundSupplyMoney 	= Math.abs($("#good_supply_refund_"+batchcode).val());//退回给供应商的金额
	var cost_price = Math.abs($("#good_cost_price_"+batchcode).val());
	var expressfee = Math.abs($("#good_expressfee_"+batchcode).val());

	var msg = "订单:"+batchcode+"</br>已确定收到退货!";
	if(parseFloat(refundGoodMoney_old) != parseFloat(refundGoodMoney)){
		msg += "并将退款金额修改为：<b style='color:red'>"+refundGoodMoney+"<b> 元";
	}else{
		msg += "退款金额：<b style='color:red'>"+refundGoodMoney+"<b> 元";
	}
	layer.confirm(msg, {
		btn: ['确定修改','取消']
	}, function(confirm){
		if( parseFloat(refundGoodMoney) > parseFloat(refundGoodMoney_old) ){
			layer.alert("退款金额不得大于订单总额！");
			return;
		}
		if(isNaN(refundGoodMoney) || parseFloat(refundGoodMoney) > parseFloat(refundGoodMoney_old)){
			layer.alert("请输入正确的金额！");
			return;
		}

		// if(isNaN(refundSupplyMoney) || parseFloat(refundSupplyMoney) > parseFloat(cost_price+expressfee)){
			// layer.alert("结算合作商金额不能大于供货价+运费！");
			// return;
		// }

		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'totalprice':refundGoodMoney,'refundSupplyMoney':refundSupplyMoney,'remark':refundRemark,'op':"confirmGoodAllRefund"},
			dataType:"json",
			success: function(res){
				layer.close(index_layer);
				if(res.status==0){
					var currOp = $('a[data-refund-all-batchcode='+batchcode+']');
					var newOp = '<a title="确定退款" data-refund-batchcode='+batchcode
					+'  onclick="showGoodRefund('+batchcode+',1,this)" ><img src="../../../common/images_V6.0/operating_icon/icon57.png" /></a>';
					currOp.replaceWith(newOp);
					$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/affirm-icon.png"> <b style="color:#EC971F">顾客申请退货</b><span style="color:red"> [已收到退货]</span>');
					$(".order_hide").fadeOut("slow");
				}
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

});
<!-- 申请退货(且退款) End -->

<!-- 延期收货 -->
$(".order_delay").click(function(){
	var batchcode = $(this).data('batchcode');
	var is_delay = $(this).data('is_delay');
	var delayDate = $("#data_delay_"+batchcode).val();
	layer.confirm('您确认要延迟 订单:'+batchcode+' 的</br>收货时间为 '+delayDate+'天 后吗', {
		btn: ['延迟','取消']
	}, function(confirm){
		if(isNaN(delayDate)){
			layer.alert("请输入正确的时间", function(index){layer.close(index);});
			return;
		}
		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'is_delay':is_delay,'Date':delayDate,'op':"delayDate"},
			dataType:"json",
			success: function(res){
				layer.close(index_layer);
				if(res.status==0){
					$("#date_time_"+batchcode).html(res.time);
				}
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

});
<!-- 延期发货 --end-->

<!-- 确认已退货 -->
function confirmGoodRefund(obj){
	var batchcode = $(obj).data('batchcode');
	layer.confirm('确定 订单:'+batchcode+' 已收到退货，确定后不可更改！', {
		btn: ['确认','取消']
	}, function(confirm){

		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'op':"confirmGoodRefund"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					$(obj).remove();
					$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/change-goods.png"> <b style="color:#C9302C">申请退货(换货)</b><b style="color:#C9302C"> [已收到退货]</b>');
				}
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

}
<!-- 确认已退货 --end-->

<!-- 确认订单 -->
function confirmOrder(obj){
	var batchcode = $(obj).data('batchcode');
	var totalprice = $(obj).data('totalprice');
	layer.confirm('您确定要确认 订单:'+batchcode+' 交易完成吗？<br/>确认后，表示订单已经完成，并且无法撤销！', {
		btn: ['确认','取消']
	}, function(confirm){

		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'totalprice':totalprice,'op':"confirm"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){

					$(obj).next('a').remove();
					$(obj).replaceWith('<a title="删除" data-batchcode="'+batchcode+'" onclick="delOrder(this)"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>');
					$("#table_five_"+batchcode).html('<span class="btn btn-success">已完成</span>');
				}
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(res){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

}
<!-- 确认订单 --end-->

<!-- 确认签收  -->
function sign_yes(obj,is_sign){
	var batchcode = $(obj).data('batchcode');
	var totalprice = $(obj).data('totalprice');
	if(is_sign == 1)
	{
		var content = '您确定要确认 订单:'+batchcode+' 签收完成吗？<br/>确认后，表示订单已经签收完成，并且无法撤销！';
	}
	else
	{
		var content = '您确定要拒绝 订单:'+batchcode+' 签收吗？<br/>确认后，表示订单已经拒绝签收，并且无法撤销！';
	}
	layer.confirm(content, {
		btn: ['确认','取消']
	}, function(confirm){

		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'totalprice':totalprice,'op':"sign_yes",is_sign:is_sign},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){

					$("#table_five_"+batchcode).html('<span class="btn btn-success">已完成</span>');
					setTimeout(function(){
						 location.reload();
					},1500);
				}
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(res){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

}
<!-- 确认签收 --end-->

<!-- 确认维权完毕 -->
function confirmAftersale(obj){
	var batchcode = $(obj).data('batchcode');
	var totalprice = $(obj).data('totalprice');
	layer.confirm('您确定要确认 订单:'+batchcode+' 维权完毕吗？', {
		btn: ['确认','取消']
	}, function(confirm){

		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'totalprice':totalprice,'op':"confirmAftersale"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					$(obj).remove();
					$("#table_five_"+batchcode+" .btn-warning").html('售后已处理完成');
				}
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

}
<!-- 确认维权完毕 --end-->

<!-- 后台支付 -->
function payOrder(obj){
	var batchcode = $(obj).data('batchcode');
	var totalprice = $(obj).data('totalprice');

	layer.confirm('您确定要确认支付订单号:'+batchcode+'吗？', {
		title:'后台支付',
		btn: ['确认支付','取消']
	}, function(confirm){
		layer_open();
		layer.close(confirm);
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'totalprice':totalprice,'op':"pay"},
			dataType:"json",
			success: function(res){
				layer.close(index_layer);
				if(res.status==0){
					$("#order_pay_"+batchcode).html('<img src="../../../common/images_V6.0/contenticon/pay-icon.png" /><span class="CP_table_bianhaof">已支付<span style="color:red;">(后台支付)</span></span>');
					$(obj).prev("a").remove();
					$(obj).next("a").replaceWith('<a title="返佣记录" href="order_rebate_log.php?batchcode='+batchcode+'&customer_id='+customer_id+'==&class=1"><img src="../../../common/images_V6.0/operating_icon/icon51.png"></a>');
					if(res.supply==-1){
						$(obj).replaceWith('<a id="button_delivery_'+batchcode+'" title="发货" onclick="showDelivery(\''+batchcode+'\')"><img src="../../../common/images_V6.0/operating_icon/icon42.png"></a>');
					}else{
						$(obj).remove();
					}

				}

				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(res){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

}
<!-- 后台支付 --end-->

<!-- 发货 -->
$(".order_delivery").click(function(){
	var batchcode = $(this).data('batchcode');
	var totalprice = $(this).data('totalprice');
	var is_payondelivery = $(this).attr('is_pay_on_delivery');
	var $button = $(this);
	layer.confirm('您确认要发货吗', {
		btn: ['发货','取消']
	}, function(confirm){

		var expressID = $("#express_id_"+batchcode).val();
		var expressName = $("#express_id_"+batchcode).find("option:selected").text();
		var expressRemark = $("#express_remark_"+batchcode).val();
		var expressNum = $("#express_num_"+batchcode).val();

		if(expressNum=="" && expressID!=0){
			layer.alert("请输入快递单号", function(index){layer.close(index);});
			return;
		}
		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'expressID':expressID,'expressRemark':expressRemark,'expressNum':expressNum,'op':"send"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					$button.parent("dd").next("dd").remove();
					$button.parent("dd").remove();
					var sendstr = "";
					$("#button_delivery_"+batchcode).prev("a").remove();
					$("#express_id2_"+batchcode).text(expressName);
					$("#express_remark2_"+batchcode).text(expressRemark);
					$("#express_num2_"+batchcode).val(expressNum);
					$("#express_num2_"+batchcode).parent('span').parent('dd').append('<span onclick="KuaiDi100('+batchcode+')" class="order_kuaidi">(点击查看物流)</span>');
					$(".change_add_"+batchcode).remove();

					if(is_payondelivery == 1)
					{
						var payondeliveryhtml = "<a title='确认签收' data-batchcode='"+batchcode+"' data-totalprice='"+totalprice+"' onclick='sign_yes(this,1)' ><img src='../../../common/images_V6.0/operating_icon/icon80.png' /></a><a title='拒绝签收' data-batchcode='"+batchcode+"' data-totalprice='"+totalprice+"' onclick='sign_yes(this,2)' ><img src='../../../common/images_V6.0/operating_icon/icon81.png' /></a>";
						$("#button_delivery_"+batchcode).parent().find('.shanchu').before(payondeliveryhtml);
					}

					if(expressID!=0){
						if(from_page != 2){
							sendstr = '<p class="CP_table_chanpina_fourp"><img src="../../../common/images_V6.0/contenticon/affirm-icon.png"><b style="color:#31B0D5"> 已发货</b></p><p>发货时间:'+res.time+'</p>';
							$("#button_delivery_"+batchcode).remove();
						}
					}else{
						if(from_page != 2){
							sendstr = '<p class="CP_table_chanpina_fourp"><img src="../../../common/images_V6.0/contenticon/confirm_delivery.png"><b style="color:#337AB7"> 顾客已收货</b></p><p>发货时间:'+res.time+'</p><p>收货时间:'+res.time+'</p>';

							$("#button_delivery_"+batchcode).replaceWith('<a title="确认完成" data-batchcode="'+batchcode+'" data-totalprice="'+totalprice+'" onclick="confirmOrder(this)"><img src="../../../common/images_V6.0/operating_icon/icon23.png"></a><a title="红包确认" class="red_'+batchcode+'"><img src="../../../common/images_V6.0/operating_icon/icon55.png"></a>');
						}
					}
					$("#table_four_"+batchcode).html(sendstr);
					$(".order_hide").fadeOut("slow");
					if(res.deliverySetting == 1){
                        $.ajax({
                            url: "order.class.php",
                            type: "POST",
                            data:{'batchcode':batchcode,'totalprice':totalprice,'op':"confirm",'is_receipt':"1"},
                            dataType: "json",
                            success: function (res) {
                                layer.close(index_layer);
                                if(res.status==0){
                                    window.location.reload();
                                    //$(obj).next('.confir').remove();
                                    //$(obj).replaceWith('<a title="删除" data-batchcode="'+batchcode+'" onclick="delOrder(this)"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>');
                                    //$("#table_five_"+batchcode).html('<span class="btn btn-success">已完成</span>');
                                }
//                                if(res.errcode>0){
//                                    layer.alert(res.errmsg);
//                                }else{
//                                    layer.alert(res.msg);
//                                }
                            },
                            error:function(res){
                                layer.close(index_layer);
                                layer.alert("网络错误请检查网络");

                            }
                        })
                    }
				}
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(res){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

});
<!-- 发货 --end-->

<!-- 删除订单 -->
function delOrder(obj){
	var batchcode = $(obj).data('batchcode');
	layer.confirm('您确定要删除订单号:'+batchcode+'吗？', {
		title:'订单删除',
		btn: ['确定删除','取消']
	}, function(){
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'op':"del"},
			dataType:"json",
			success: function(res){
				if(res.status==0){
					$(obj).parent("td").html('<a style="color:#C9302C">订单已删除</a>');
				}
				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
			},
			error:function(){
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消删除', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

}
<!-- 删除订单 --end-->

<!--扣除供应商款项 start-->



$(".order_reduce").click(function(){
	var batchcode = $(this).data('batchcode');
	var money = Number($(this).data('money'));  //原来订单金额
	var reducemoney = Math.abs($("#data_reduce_"+batchcode).val());
	var supplymoney = Math.abs($(this).data('supplymoney')); //供货价+运费金额
	layer.confirm('您确认要对 订单:'+batchcode+' </br>的合作商扣除金额 '+reducemoney+'吗', {
		btn: ['扣除','取消']
	}, function(confirm){
		if(isNaN(reducemoney) || reducemoney<0 || reducemoney>supplymoney){
			layer.alert("请输入正确的金额（扣除金额不能大于供货价+运费）", function(index){layer.close(index);});
			return;
		}
		layer.close(confirm);
		layer_open();
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'reducemoney':reducemoney,'op':"reducesupplymoney"},
			dataType:"json",
			success: function(res){
				layer.close(index_layer);

				if(res.errcode>0){
					layer.alert(res.errmsg);
				}else{
					layer.alert(res.msg);
				}
				$("#reduce_"+batchcode).css("display","none");
				$(".reducesupply_btn").remove();
			},
			error:function(){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});

});
<!--扣除供应商款项 end-->



function backOrder(obj){
	var batchcode = $(obj).data('batchcode');
	if(!confirm('您确定要驳回客户退货吗？')){
		return false;
	}
	$.ajax({
		url: "order.class.php",
		type:"POST",
		data:{'batchcode':batchcode,'op':"back"},
		dataType:"json",
		success: function(res){
			if(res.status==0){
				$(obj).prev("a").remove();
				$(obj).next("a").remove();
				$(obj).remove();
				$("#WSY_order_status_"+batchcode).html("<a class=\"WSY_already_red\">驳回退货</a>");
				//$("#WSY_order_pay_"+batchcode).prev("td").children("p").html("微信支付<br>(<a style=\"color:red\">后台支付</a>)");
			}
			alert(res.msg);
		},
		error:function(){
			alert("网络错误请检查网络");
		}
	});

}

function KuaiDi100(obj){
	KDNum = $("#express_num2_"+obj).val();
	KDName = $("#express_id2_"+obj).text().trim();
    console.log(KDNum);

	layer.open({
		type: 2,
		title: '快递查询',
		shadeClose: true,
		shade: 0.5,
		area: ['450px', '70%'],
		content: '//m.kuaidi100.com/index_all.html?type='+KDName+'&postid='+KDNum+'#result'
	});

}


var glo_add;
var glo_per;//完成百份比
var obj_json;
var topLoaderRunning;
var $topLoader;
$(function() {
	inti_per();
});

function inti_per(){
	glo_add = 0.0;
	glo_per = 0.0;
	obj_json = new Array();
	$topLoader = $("#topLoader").percentageLoader({
		width: 256, height: 256, controllable: true, progress: glo_add, onProgressUpdate: function (val) {
		  this.setValue(Math.round(val * 100.0) + '%初始化中，请勿刷新和关闭页面！');
		}
	});
    topLoaderRunning = false;
}

var d_name = '<?php echo $d_name; ?>';
var c_name = '<?php echo $c_name; ?>';
var b_name = '<?php echo $b_name; ?>';
var a_name = '<?php echo $a_name; ?>';
function export_excel(num){

	switch(num){
		case 1: //订单导出
			var name ="commonshop_excel";
			var excelArray = [
						["batchcode","订单号"],
						["expressname","快递"],
						["expressnum","快递单号"],
						["send_remarks","备注信息"],
						["pay_batchcode","支付号"],
						["name","姓名"],
						["identity","身份证号"],
						["phone","手机"],
						["address","地址"],
						["good_name","商品"],
						["foreign_mark","外部标识"],
						["for_price","成本价"],
						["cost_price","供货价"],
                        ["unit_price","现价"],
						["rcount","数量"],
                        ['all_price','合计'],
						["provalue_str","属性"],
						["information","必填信息"],
						["sendstyle","送货方式"],
						["paystyle","付款方式"],
						["paysid","交易单号"],
						["totalprice","总价"],
						["discount_price","总优惠金额"],
						["pay_currency","使用购物币"],
						["needScore","使用积分"],
						["coupons_count","使用优惠券张数"],
						["couponprice","使用优惠券总金额"],
						["pro_reward","分佣比例"],
						["sum_reward","分佣总额"],
						["agent_id","代理商编号"],
						["agent_name","代理商级别"],
						["supply_id","合作商编号"],
						["user_id1","一级推广员编号"],
						["user_id2","二级推广员编号"],
						["user_id3","三级推广员编号"],
						["express_price","运费"],
						["paystatus","支付状态"],
						["sendstatus","发货状态"],
						["status","状态"],
						["createtime","订单时间"],
						["merchant_remark","商家备注"],
						["card_shop_name","所属门店"],
						["card_shop_address","门店地址"],
						["d_name",d_name],
						["c_name",c_name],
						["b_name",b_name],
						["a_name",a_name]
					 ];
		  break;
		case 2: //飞豆
			var name ="commonshop_feidou_excel";
			var excelArray = [
						["batchcode","订单号"],						
						["expressname","快递"],
						["expressnum","快递单号"],
						["send_remarks","订单备注"],						
						["sendstyle","快递类型"],
						["name","姓名"],
						["address","地址"],
						["conpany","单位名称"],
						["phone","手机电话"],
						["Zipcode","邮编"],						
						["pay_batchcode","支付号"],
						["good_name","商品名"],						
						["remark","客户留言"],
						["ocount","商品数量"],
						
					 ];
		  break;
	    case 3: //海关头部
			var name ="commonshop_customs_head_excel";
			var excelArray = [
						["batchcode","订单编号"],
						["expressnum","物流运单号"],
						["name","收件人姓名"],
						["identity","收件人身份证号"],
						["phone","收件人手机"],
						["address2","收件人地址"],
						["rcount","件数"],
						["price","运费"],
						["totalprice2","总费用"],
						["cost_prices","备案价"]
					 ];
		  break;
		case 4: //海关明细
			var name ="commonshop_customs_excel";
		  	var excelArray = [
						["batchcode","订单编号"],
						["foreign_mark","商品编号"],
						["good_name","商品名称"],
						["unit","单位"],
						["price","单价"],
						["rcount","数量"]
					 ];
		  break;
	}

    
    
	

	/*导出订单筛选框*/
	exportBox(excelArray);
	$(".floatbox").show();

	$(".floatinputs").click(function(){

    //组装参数
	var _obj = {
		name: name,
		param: [
            'begintime',
            'endtime',
            'pay_begintime',
            'pay_endtime',
            'orgin_from',
            'continued_day',
            'search_paystyle',
            'search_batchcode',
            'search_name',
            'search_product_name',
            'search_agent_id',
            'search_phone',
            'search_name_type',
            'search_order_ascription',
            'search_supply_id',                                 
            'search_attribution_type',                                 
            'search_pre_delivery_type',                                 
            'search_supply_id',                                 
            'status',                                 
        ]
			
	}

    
    
    
    exportSubmit(_obj);
	
      

	var f2c_id = '<?php echo $_GET['f2c_id']; ?>';//f2c仓库编号
    if(f2c_id > 0){
        url_base = url_base + 'f2c_id/' +f2c_id+'/';
    }
	//console.log(url_base);return;
	
	topLoaderRunning = true;
	

	

		
	});
}





function exportRecord(num){
	switch(num){
		case 1: //订单导出
		  var name ="commonshop_excel";
		  break;
		case 2: //飞豆
		  var name ="commonshop_feidou_excel";
		  break;
	    case 3: //海关头部
		  var name ="commonshop_customs_head_excel";
		  break;
		case 4: //海关明细
		  var name ="commonshop_customs_excel";
		  break;
	}

	var begintime = $("#begintime").val();//下单时间开始
	var endtime = $("#endtime").val();//下单时间结束
	var pay_begintime = $("#pay_begintime").val();//订单支付时间开始
	var pay_endtime = $("#pay_endtime").val();//订单支付时间结束
	var orgin_from = $("#orgin_from").val();//订单来源
	var continued_day = $("#continued_day").val();//订单来源
	var search_paystyle = $("#search_paystyle").val();//支付方式
	var search_batchcode = $("#search_batchcode").val();//订单号
	var search_name = $("#search_name").val();//搜索姓名
	var search_product_name = $("#search_product_name").val();//搜索产品名称
	var search_phone = $("#search_phone").val();//搜索产品名称
	var search_name_type = $("#search_name_type").val();//微信名还是收货名
	var search_order_ascription = $("#search_order_ascription").val();//订单归属
	var search_supply_id = $("#search_supply_id").val();//订单归属
	var status = <?php echo $search_class; ?>;//订单状态
	if(search_batchcode==""){
		search_batchcode = -1;
	}
	if(orgin_from==""){
		orgin_from = -1;
	}
	if(continued_day==""){
		continued_day = -1;
	}
	if(search_name==""){
		search_name = -1;
	}
	if(search_product_name==""){
		search_product_name = -1;
	}
	if(search_paystyle==""){
		search_paystyle = -1;
	}
	if(search_phone==""){
		search_phone = -1;
	}
	if(begintime==""){
		begintime = 0;
	}
	if(endtime==""){
		endtime = 0;
	}
	if(pay_begintime==""){
		pay_begintime = 0;
	}
	if(pay_endtime==""){
		pay_endtime = 0;
	}

	var url='/weixin/plat/app/index.php/Excel/'+name+'/customer_id/<?php echo passport_decrypt($customer_id); ?>/begintime/'+begintime+'/endtime/'+endtime+'/pay_begintime/'+pay_begintime+'/pay_endtime/'+pay_endtime+'/search_batchcode/'+search_batchcode+'/orgin_from/'+orgin_from+'/search_paystyle/'+search_paystyle+'/search_order_ascription/'+search_order_ascription+'/search_supply_id/'+search_supply_id+'/search_name/'+search_name+'/search_product_name/'+search_product_name+'/search_phone/'+search_phone+'/search_name_type/'+search_name_type+'/status/'+status+'/continued_day/'+continued_day+'/';
	//console.log(url);
	document.location=url;
}

var index_layer;
function layer_open(){
	index_layer= layer.load(0, {
		shade: [0.1,'#000'], //0.1透明度的白色背景
		content: '<div style="position:relative;top:30px;width:200px;color:red">数据处理中</div>'
	});
}
function showMore(batchcode){//必填信息更多显示/隐藏
	$('.mess_b').css('max-height',"auto");
	$('#div1').css('display',"none");
}
//passport_decrypt
$(function(){
	// $.ajax({
		// url: "ax_print_order.php?customer_id="+<?php echo passport_decrypt($customer_id); ?>,
		// type:"GET",
		// dataType:"json",
		// success: function(data){
			// //console.log(data);
			// $.each(data, function(i_data) {
				// var s_html = '<span style="color:#32b16c;font-size: smaller;cursor:pointer;" onClick="print_delivery_orders('+data[i_data].print_id+')">批量打印'+data[i_data].print_name+'('+data[i_data].print_count+')</span>&nbsp;&nbsp;';
				// $('#print_batch_show').html($('#print_batch_show').html()+s_html);
			// });
		// },
		// error:function(){
			// alert("网络错误请检查网络");
		// }
	// });
	// $('.print_delivery').show();
});
function print_delivery(batchcode){
	print2order(batchcode,<?php echo passport_decrypt($customer_id); ?>,1);
}

function print_delivery_orders(print_temp_id){
	print2order(print_temp_id,<?php echo passport_decrypt($customer_id); ?>,0);
}

function stock_recovery(){
	var num;
	var page;
	inti_per();
	ShowDIV('topLoader');
	var animateFunc = function () {
		var dd = "fff";
		$.ajax({
			type: "post",
			url: "stockRecovery.php",
			async: false,
			data: {customer_id: '<?php echo $customer_id_en; ?>'},
			success: function (result2) {
				var obj2 = JSON.parse(result2);
				if( obj2.status == 1 ){
					glo_per = 1/page;
					glo_add = glo_add + glo_per;
					$topLoader.percentageLoader({progress: glo_add});
					$topLoader.percentageLoader({value: ('回收中，请勿刷新和关闭页面！')});
					if(glo_add<1){
						setTimeout(animateFunc, 200);
					}else{
						closeDiv('topLoader');
						topLoaderRunning = false;
					}
				}else{
					alert("网络错误请检查网络");
				}
			},
			error:function(result){
				alert("网络错误请检查网络");
			}
		});
	};

	$.ajax({
        type: "post",
        url: "stockRecovery.php",
		async: false,
        data: {op:"count",customer_id: '<?php echo $customer_id_en; ?>'},
        success: function (result) {
			var obj = JSON.parse(result);
			if( obj.status == 1 ){
				num = obj.num;
				page = Math.ceil(num/20);
				setTimeout(animateFunc, 200);
			}else{
				alert("网络错误请检查网络");
			}
        },
		error:function(result){
			alert("网络错误请检查网络");
		}
    });


}
function deletePromoter(user_id){
	layer.confirm('您确认要删除该用户的推广员身份吗？', {
		btn: ['确定','取消']
	}, function(confirm){

		layer.close(confirm);
		layer_open();

		$.ajax({
			type: "post",
			url: "order.class.php",
			async: false,
			data: {op:"deletePromoter",customer_id: '<?php echo $customer_id_en; ?>',user_id:user_id},
			success: function (result) {
			console.log(result);
				if(result.errcode > 0){
					alert("网络错误请检查网络");
					window.location.reload();
				} else {

					window.location.reload();
				}
			},
			error:function(result){
				alert("网络错误请检查网络");
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});
}
/*只能输入数字和两位小数*/
function clearNoNum(obj){
	obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
	obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字而不是
	obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
	obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
	obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
}
function takeReplace(obj){
	obj.value = obj.value.replace(/[^\d]/g,'');
	$('#search_order_ascription').val(obj.value);
}

var company = eval('<?php echo $company; ?>');//物流公司信息
function print_order(){
	var batchcode = '';
	var b_id = '';
	$('input[name="input_checkbox"]').each(function(){
		if($(this).prop("checked")){
			b_id = ($(this).attr('b_id'));
			if(batchcode.indexOf(b_id)<0){
				batchcode += '|'+b_id;
			}
		}
	});
	if(batchcode==''){
		layer.alert('请勾选订单去打印');
		return;
	}
	batchcode = '"'+batchcode+'"';
	var html = '';
		$.each(company,function(key,value){
			html += '<div class="div_item"><input type="checkbox" name="ckp_company" value="'+value.company_id+'" print_temp_id="'+value.print_temp_id+'"><label for="check_out">'+value.expresses_name+'</label></div>';
		});
		html += '<div style="clear:both;">';
		html += "<button style='float:none;margin-top:0;margin-left:95px;' onclick='print_delivery_order("+batchcode+")'>确定</button>";
		html += '<button style="float:none;margin-top:0;margin-left: 20px;" onclick="colse_layerOpen()">取消</button>';
		html += '</div>';
		layer.open({
			  type: 1,
			  title:'选择打印订单的物流公司',
			  skin: 'layui-layer-demo', //样式类名
			  closeBtn: 0, //不显示关闭按钮
			  shift: 2,
			  shadeClose: true, //开启遮罩关闭
			  content: html
			});
		$('input[name="ckp_company"]').click(function(){
			$('input[name="ckp_company"]').removeAttr('checked');
			this.checked=true;
		});
}
function colse_layerOpen(){
	$('.layui-layer').remove();
	$('.layui-layer-shade').remove();
}
var is_Lock = 0;
function print_delivery_order(batchcode){
	var print_temp_id = -1;//快递模板ID
	var company_id = -1;//物流公司ID
	var company_name = '';//物流公司名字
	$('input[name="ckp_company"]').each(function(){
		if($(this).prop("checked")){
			print_temp_id = $(this).attr('print_temp_id');
			company_id = $(this).val();
			company_name = $(this).siblings('label').text();
		}
	});
	if(print_temp_id<0){
		layer.alert('未绑定运单模板');
		return;
	}
	if(!is_Lock){
		is_Lock = 1;
		$.ajax({
			url: "save_order_company.php?customer_id="+<?php echo passport_decrypt($customer_id); ?>,
			type:"POST",
			data:{batchcode:batchcode,company_id:company_id,company_name:company_name},
			dataType:"json",
			success: function(data){
				console.log(data);
				if(data.code==1001){
					//打印快递运单
					$.each(data.batchcode,function(id,val){
						print_delivery(val,<?php echo passport_decrypt($customer_id); ?>,1);
					});
				}else{
					alert(data.msg);
				}
				$('.layui-layer').remove();
				$('.layui-layer-shade').remove();
				is_Lock = 0;
			},
			error:function(){
				alert("网络错误请检查网络");
			}
		});
	}


}
</script>
</body>
</html>
