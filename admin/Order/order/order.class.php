<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');   //配置
require('../../../../weixinpl/customer_id_decrypt.php');   //解密参数
require_once ($_SERVER['DOCUMENT_ROOT']."/wsy_pay/web/healthpay/healthpay_functions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/wsy_pay/web/healthpay/healthpay_api.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/wsy_pay/web/healthpay/healthpay_submit.class.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/mp/lib/LogOpe.php');//日志文件
require_once(ROOT_DIR."wsy_pay/web/function/queue_order.php");//队列活动


require_once($_SERVER['DOCUMENT_ROOT']."/mshop/web/model/integral.php");
require_once (ROOT_DIR . "/wsy_pub/admin/model/sys_plat_log.php");  //全局日志
//require_once('../../../../weixinpl/function_model/currency.php');
//$batchcode =$configutil->splash_new($_POST["batchcode"]);
$op =$configutil->splash_new($_POST["op"]);
if(!empty($_POST["batchcode"])){
   $batchcode = $configutil->splash_new($_POST["batchcode"]);

   if(!isset($customer_id)){
		$json["status"] = 10001;
		$json["line"] = 15;
		$json["msg"] = "登录超时，请重新登录！$customer_id";
		$jsons=json_encode($json);
		die($jsons);
	}

	if(!is_numeric($batchcode)){
		$json["status"] = 10002;
		$json["line"] = 24;
		$json["msg"] = "订单号不正确！";
		$jsons=json_encode($json);
		die($jsons);
	}
}

$today = date("Ymd");

file_put_contents("log/order_api_" . $today . ".txt", "\r\nbatchcode=======".var_export($batchcode,true)."\r\n",FILE_APPEND);
file_put_contents("log/order_api_" . $today . ".txt", "op=======".var_export($op,true)."\r\n",FILE_APPEND);

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");


$log_username = $_SESSION['curr_login'];

require('../../../../weixinpl/proxy_info.php');       //OEM域名
require('../../../../weixinpl/common/utility.php');
require('../../../../weixinpl/common/utility_shop.php');  //商城方法
include_once('../../../../weixinpl/common/utility_promoter.php');
$shopmessage= new shopMessage_Utlity();
/*4M方法 start*/
require('../../../../weixinpl/common/utility_4m.php');  //商城方法
$shop_4m = new Utiliy_4m_new();

$res_4m = $shop_4m->is_4M_new($customer_id);
$is_4m = $res_4m[0];
/*4M方法 end*/

$Promoter= new Promoter\PromoterUtlity($customer_id);

$http_host = $_SERVER['HTTP_HOST'];

switch($op){
	case "del":  //删除订单
        $sendstatus = -1;   //未发货订单不能删除
        $query_select = "SELECT sendstatus,paystatus FROM weixin_commonshop_orders WHERE isvalid = TRUE AND batchcode = '$batchcode'";
        $result = _mysql_query($query_select) or die("Query failed -58: " . mysql_error() . " || $query_select");;
        while ($row = mysql_fetch_object($result)) {
            $sendstatus = $row->sendstatus;
            $paystatus = $row->paystatus;
            break;
        }
        if ($sendstatus == 0 && $paystatus == 1){
            $json["status"] = 10088;
            $json["line"] = 41;
            $json["msg"] = "编号：".$batchcode."，未发货订单不能删除";
            $jsons = json_encode($json);
            die($jsons);
        }
		$query_del= "update weixin_commonshop_orders set isvalid=false where batchcode='".$batchcode."' and customer_id=".$customer_id;
		_mysql_query($query_del);

		$query_order_del= "update weixin_commonshop_order_prices set isvalid=false where batchcode='".$batchcode."'";
		_mysql_query($query_order_del);

		//插入日志
		$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) values('".$batchcode."',22,'平台删除订单','".$log_username."',now(),1)";
		_mysql_query($query_log);

		$json["status"] = 0;
		$json["line"] = 41;
		$json["msg"] = "编号：".$batchcode."，删除成功";
	break;

	case 'sign_yes' ://确认签收，修改支付状态， 修改签收状态，修改发货状态为已收货,插入收货时间插入日记,修改佣金paytype=0

		$is_sign    = $configutil->splash_new($_POST["is_sign"]);
		$paystatus  = 1;
		$sendstatus = 2;
		$status     = '';
		if($is_sign == 2)
		{
			$paystatus  = 0;
			$sendstatus = 1;
			$status = ",status=-1";
		}
		$confirm_receivetime = '';
		if($sendstatus == 2)
		{
			$time   = date('Y-m-d H:i:s',time());
			$confirm_receivetime = ",confirm_receivetime ='".$time."'";
		}

		//支付时间
		$paytime = '';
		if($paystatus  == 1)
		{
			$now     = date('Y-m-d H:i:s',time());
			$paytime = ",paytime='".$now."'";
		}

		$query_sign = "update weixin_commonshop_orders set is_sign=$is_sign,paystatus=$paystatus,sendstatus=$sendstatus $confirm_receivetime $status $paytime where batchcode='".$batchcode."' and customer_id=".$customer_id;
		_mysql_query($query_sign);


		//修改佣金paytype=0
		$query_promotes = "update weixin_commonshop_order_promoters set paytype = 0 where batchcode='".$batchcode."' and paytype =-1";
		_mysql_query($query_promotes);

		//发送拒绝签收消息
		if($is_sign == 2)
		{
			//查上级id
			// $exp_user_id_sql  = "select exp_user_id from weixin_commonshop_orders where batchcode='".$batchcode."' and customer_id=".$customer_id;
			// $exp_user_id_res  = _mysql_query($exp_user_id_sql) or die('Query_apply failed: ' . mysql_error());
			// while($row 	  = mysql_fetch_object($exp_user_id_res))
			// {
			// 	$exp_user_id  = $row->exp_user_id;
			// }

			$sel_promotes = "select user_id,remark,level_name from weixin_commonshop_order_promoters where batchcode='".$batchcode."' and paytype =0";
			$result_pro   = _mysql_query($sel_promotes) or die('Query_apply failed: ' . mysql_error());
			while($row 	  = mysql_fetch_object($result_pro))
			{
				$user_id  = $row->user_id;
				$usermon  = $row->remark;
				$level    = $row->level_name;
				$userid[] = $user_id;
				$level_name[]    = $level;
				if(mb_strpos($usermon,'元') == false)
				{
					$usermon_str = $usermon.'元';
				}
				$usermoney[]     = $usermon_str;
			}

			foreach($userid as $k=>$v)
			{
				/*读取顾客资料开始*/
				$query_user = "SELECT
									weixin_name,
									province,
									city,
									sex
								FROM
									weixin_users
								WHERE
									customer_id = ".$customer_id."
								AND id = ".$v."
								AND isvalid = TRUE
								LIMIT 0,1";
				$result_user  = _mysql_query($query_user) or errorResult($result,14007);
				$consume_name = "佚名";	//微信名
				$province     = "";		//省份
				$city         = "";		//城市
				$sexstr 	  = "保密";	//性别
				$sex		  = 0;		//性别
				while ($row = mysql_fetch_object($result_user)) {
				  $consume_name = $row->weixin_name;
				  $province     = $row->province;
				  $city 		= $row->city;
				  $sex 			= $row->sex;
				}
				$consume_name = mysql_real_escape_string($consume_name);
				switch($sex){
					case 1:
						$sexstr = "男";
						break;
					case 2:
						$sexstr = "女";
						break;
					default:
						$sexstr = "保密";
				}
	/*读取顾客资料结束*/

				$open     = $shopmessage->query_openid($customer_id,$v);
				$openid   = $open['openid'];

				$content_head2 = "亲，您流失了".$usermoney[$k]."的佣金"."\r\n";
				$content = $content_head2.
							"来源：【货到付款订单拒支付】\n".
							"身份：【".$level_name[$k]."】\n".

							"顾客：".$consume_name."\n".
							"定位：".$province.$city."\n".
							"性别：".$sexstr."\n".
							"时间：".date( "Y-m-d H:i:s")."";
				$send_object[$k]["content"]	= $content;
				$send_object[$k]["openid"]	= $openid;

			}

			//发送消息
			$is_commission_message  = 1;//佣金消息提示开关，0关，1开
			$is_commission_scope    = 0;//佣金消息提示范围，0所有人，1推广员提示
			$send_len = count( $send_object );
			for ($i = 0; $i < $send_len; $i++) {
				$send_con 		= $send_object[$i]["content"];
				$send_openid	= $send_object[$i]["openid"];
				$query_is_extension_status   = $shopmessage->query_is_extension_status($send_openid);//获取is_extension_status
                $is_extension_status       = $query_is_extension_status["is_extension_status"];

				if ($is_commission_message==1) {
                    if ($is_commission_scope==0) {
                         //发送消息
                        $shopmessage->SendMessage($send_con, $send_openid, $customer_id);
                     }else{
                        if ($is_extension_status>0) {
                            //发送消息
                            $shopmessage->SendMessage($send_con, $send_openid, $customer_id);
                         }
                     }
                }
			}

		}

		//插入日志
		$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) values('".$batchcode."',31,'平台确认签收订单','".$log_username."',now(),1)";
		_mysql_query($query_log);

		$json["status"] = 0;
		$json["line"] = 41;
		$is_sign == 1 ? $json["msg"] = "编号：".$batchcode."，签收成功" : $json["msg"] = "编号：".$batchcode."，拒绝签收成功";
		break;

	case "send":   //发货
		$express_remark = $configutil->splash_new($_POST["expressRemark"]);
		$express_id     = $configutil->splash_new($_POST["expressID"]);
		$express_num    = $configutil->splash_new($_POST["expressNum"]);

		$sendMessageContent = [];	//推送消息内容

		$user_id        = -1;
		$agent_id       = -1;
		$totalprice     = 0;
		$sendstatus     = 0;
		$address_id     = 0;
		$rcount         = 0;
		$paystatus      = 0;
		$pro_name       = ""; //产品名称
		$is_QR          = 0;  //是否发送二维码
		$pro_name_one   = ""; //产品名称
		$agentcont_type = 0;
		$is_sendorder   = 0;
		$yundian_self   = 0;
		$exp_user_id    = -1;		 //订单上级
		$card_member_id = -1;
		$paystyle       = "";
		$sendstyle      = "";

		$query_order  = "select user_id,agent_id,totalprice,agentcont_type,sendstatus,pid,rcount,address_id,is_QR,return_status,return_type,paystatus,is_sendorder,is_pay_on_delivery,yundian_self,camilo_ids,exp_user_id,card_member_id,paystyle,sendstyle from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
		$result_order = _mysql_query($query_order) or die('Query_order failed: ' . mysql_error());
		while ($row_order = mysql_fetch_object($result_order)) {
			$user_id          = $row_order->user_id;        //用户ID
			$agent_id         = $row_order->agent_id; 	//代理商user_id
			$totalprice       = $row_order->totalprice; 	 //订单总金额
			$agentcont_type   = $row_order->agentcont_type; 	//分佣路线
			$sendstatus       = $row_order->sendstatus;   //发送状态
			$pid              = $row_order->pid;    //产品ID
			$address_id       = $row_order->address_id;   //地址
			$is_QR            = $row_order->is_QR;   //二维码
			$rcount           = $row_order->rcount;   //数量
			$paystatus        = $row_order->paystatus;   //付款状态
			$return_status    = $row_order->return_status;   //退货状态 2：同意退货
			$return_type      = $row_order->return_type;   //退货类型：0：退款；1：退货；2：换货
			$is_sendorder     = $row_order->is_sendorder;  //派单状态 0.非派单，1派单，2，f2c拒单
			$is_payondelivery = $row_order->is_pay_on_delivery;
			$yundian_self     = $row_order->yundian_self;
			$camilo_ids       = $row_order->camilo_ids;  //卡密相关
			$exp_user_id      = $row_order->exp_user_id;
			$card_member_id   = $row_order->card_member_id;
			$paystyle         = $row_order->paystyle;
			$sendstyle        = $row_order->sendstyle;

			if( $paystatus == 0 && $is_payondelivery != 1){
				$json["status"] = 10014;
				$json["line"] = 81;
				$json["msg"] = "订单未支付，无法发货！";
				$jsons=json_encode($json);
				mysql_close($link);
				die($jsons);
			}

			//判断是否是货到付款订单
	        $order_con = '';
	        if($is_payondelivery == 1)
	        {
	        	$order_con = ',is_sign=0';
	        }

			if( !($sendstatus == 0 or ($sendstatus == 3 and $return_type == 2)) ){
				$json["status"] = 10013;
				$json["line"] = 90;
				$json["msg"] = "订单已经发货，请勿重复提交！";
				$jsons=json_encode($json);
				mysql_close($link);
				die($jsons);
			}

			$agent_discount=0;
			$query_product = "select agent_discount,name,foreign_mark,now_price from weixin_commonshop_products where isvalid=true and id='".$pid."'";
			$result_product = _mysql_query($query_product) or die('Query_product failed: ' . mysql_error());
			while ($row_product = mysql_fetch_object($result_product)) {
				$agent_discount    = $row_product->agent_discount;
				$product_name      = $row_product->name;
				$pro_name_one      = "".$product_name."";
				$product_now_price = $row_product->now_price;
				$foreign_mark      = $row_product->foreign_mark;
			}
			$pro_name .= $pro_name_one;

			/* 代理商扣除库存 */
			if($agent_id>0 and $agentcont_type==1 and $sendstatus==0){
				//购买支付后,扣除代理库存余额 和 代理得到的金额 start
				$agent_inventory = 0;
				$query_promote="select agent_inventory from promoters where status=1 and isvalid=true and user_id=".$agent_id;	//查找代理商代理剩余库存金额
				$result_promote = _mysql_query($query_promote) or die('Query_promote failed: ' . mysql_error());
				while ($row_promote = mysql_fetch_object($result_promote)) {
					$agent_inventory = $row_promote->agent_inventory;
				}
				if($agent_discount==0){
					$query_apply="select agent_discount from weixin_commonshop_applyagents where status=1 and isvalid=true and user_id=".$agent_id;	//查找代理商代理剩余库存金额
					$result_apply = _mysql_query($query_apply) or die('Query_apply failed: ' . mysql_error());
					$agent_discount =0;
					while ($row_apply = mysql_fetch_object($result_apply)) {
					 $agent_discount = $row_apply->agent_discount;
					}
				}

				$agent_discount =  $agent_discount/100;
				$agent_cost_inventorymoney = $totalprice * $agent_discount;	//从代理金额扣除成本价
				$agent_cost_inventorymoney = round($agent_cost_inventorymoney,2);
				$agent_inventory = $agent_inventory - $agent_cost_inventorymoney;

			/* 	if($agent_inventory<0){
					$json["status"] = 10010;
					$json["line"] = 118;
					$json["msg"] = "代理商库存不足,无法发货！";
					$jsons=json_encode($json);
					mysql_close($link);
					die($jsons);

				}	 */

				$agent_cost_inventorymoney = 0-$agent_cost_inventorymoney;

				$query_Srecord = "select id from weixin_commonshop_agentfee_records where batchcode='".$batchcode."' and detail='发货(出库)' and isvalid = true and user_id = '".$agent_id."' ";
				$result_Srecord = _mysql_query($query_Srecord) or die('Query_apply failed: '.mysql_error());
				while ($row_Srecord = mysql_fetch_object($result_Srecord)) {
				    $id = $row_Srecord->id;
				}

				if (!$id) {
				    $query_Irecord = "insert into weixin_commonshop_agentfee_records(user_id,batchcode,price,detail,type,isvalid,createtime,after_inventory) values(".$agent_id.",'".$batchcode."',".$agent_cost_inventorymoney.",'发货(出库)',1,true,now(),".$agent_inventory.")";
					_mysql_query($query_Irecord);		//插入扣除成本价
					$query_Upromote = "update promoters set agent_inventory=".$agent_inventory." where user_id=".$agent_id;
					_mysql_query($query_Upromote);  //更新库存
				}

			}
			/* 代理商扣除库存 End */
		}

		/* 查询OpenID */
		$order_fromuser = "";
		$query_user="select weixin_fromuser from weixin_users where isvalid=true and id=".$user_id." limit 0,1";
		$result_user = _mysql_query($query_user) or die('Query_user failed: ' . mysql_error());
		while ($row_user = mysql_fetch_object($result_user)) {
			$order_fromuser = $row_user->weixin_fromuser;
		}
		/* 查询OpenID End */

		/* 查询快递 */
		$expressname = "";
        $deliverySetting = "2"; //除了0和1，随便设置一个数值都行
		switch($express_id){
			case  0:
			        $expressname="虚拟发货";
			        //判断是订单设置中是否开启了虚拟发货自动结算的开关
                    $query_setting = 'SELECT is_deliverySettlement FROM weixin_commonshops_extend where customer_id='.$customer_id;
                    $result_setting = _mysql_query($query_setting) or die('Query_express failed: ' . mysql_error());
                    while ($result_setting = mysql_fetch_object($result_setting)) {
                        $deliverySetting = $result_setting->is_deliverySettlement;
                    }
                break;
			case -2:  $expressname="顺丰进口业务";  break;
			case -3:  $expressname=$express_remark; 
					  $express_id = 1;
					  break;//云店店主发货
			default:
					$query_express = 'SELECT id,expresses_name FROM weixin_expresses_company where id='.$express_id;
					//_file_put_contents("log/order_send_" . $today . ".txt", "query_express=======".var_export($query_express,true)."\r\n",FILE_APPEND);
					$result_express = _mysql_query($query_express) or die('Query_express failed: ' . mysql_error());
					while ($row_express = mysql_fetch_object($result_express)) {
						$expressname = $row_express->expresses_name;
					}
		}
		/* 查询快递End */

		/* 顺风进口接口  */
		if($express_id== -2 ){
			require_once('../sf/lib/orderService.php'); // 顺丰接口
			$query3="select name,phone,address,location_p,location_c,location_a,identity from weixin_commonshop_order_addresses where batchcode='".$batchcode."'";
			//_file_put_contents("log/order_send_" . $today . ".txt", "query3_167=======".var_export($query3,true)."\r\n",FILE_APPEND);
			$result3 = _mysql_query($query3) or die('Query_168 failed: ' . mysql_error());
			$order_username  = "";
			$order_userphone = "";
			$order_address   = "";
			while ($row3 = mysql_fetch_object($result3)) {
				$order_username  = $row3->name;
				$order_userphone = $row3->phone;
				$identity        = $row3->identity;
				$order_address   = $row3->address;
				$location_p      = $row3->location_p;
				$location_c      = $row3->location_c;
				$location_a      = $row3->location_a;
			}
			if(empty($order_username)){
			   $query3="select name,phone,address,location_p,location_c,location_a from weixin_commonshop_addresses where  id=".$address_id;
			   //_file_put_contents("log/order_send_" . $today . ".txt", "query3_183=======".var_export($query3,true)."\r\n",FILE_APPEND);
				$result3 = _mysql_query($query3) or die('Query failed48: ' . mysql_error());
				$order_username = "";
				$order_userphone ="";
				$order_address="";
				while ($row3 = mysql_fetch_object($result3)) {
					$order_username  = $row3->name;
					$order_userphone = $row3->phone;
					$order_address   = $row3->address;
					$location_p      = $row3->location_p;
					$location_c      = $row3->location_c;
					$location_a      = $row3->location_a;
				}
			}

			$query_re_sf="select * from sf_import where customer_id=$customer_id and ison=1";
			//_file_put_contents("log/order_send_" . $today . ".txt", "query_re_sf=======".var_export($query_re_sf,true)."\r\n",FILE_APPEND);
			$re_sf=_mysql_query($query_re_sf) or die("查询顺丰进口业数据表务表失败!");
			$l_sf=mysql_num_rows($re_sf);
			if(!$l_sf){
				die("没有配置顺丰进口参数!");
			}else{
				$row_sf=mysql_fetch_object($re_sf);
				$head=$row_sf->head;
				$token=$row_sf->token;
				$authToken=$row_sf->authToken;
				$businessLogo=$row_sf->businessLogo;
				$Sendcompany=$row_sf->Sendcompany;
				$Sendconcact=$row_sf->Sendconcact;
				$Sendtelphone=$row_sf->Sendtelphone;
				$Sendmobile=$row_sf->Sendmobile;
				$Sendcountry=$row_sf->Sendcountry;
				$Sendprovinoce=$row_sf->Sendprovinoce;
				$Sendcitycode=$row_sf->Sendcitycode;
				$Sendcity=$row_sf->Sendcity;
				$Sendzipcode=$row_sf->Sendzipcode;
				$Sendaddress=$row_sf->Sendaddress;
				$Sendcounty=$row_sf->Sendcounty;
				$monthlyAccount=$row_sf->monthlyAccount;
				$customsBatchNumber=$row_sf->customsBatchNumber;
				$taxSetAccounts=$row_sf->taxSetAccounts;
				$checkWord=$row_sf->checkWord;

					$xmlArray = array(
					'@attributes' => array(
						'service' => 'otherOrderService',
						'lang' => 'zh_cn',
						'printType'=>'2',
					),
					'Head' => "$head",
					   'Body' => array(
							"Order" => array(
								'@attributes' => array(
									'orderSourceSystem'=>'3',
									 'businessLogo'=>'Wsy',
									 'customerOrderNo'=>$batchcode,
									 'Sendcompany'=>$Sendcompany,
									 'Sendconcact'=>$Sendconcact,
									 'Sendtelphone'=>$Sendtelphone,
									 'Sendmobile'=>$Sendmobile,
									 'Sendcountry'=>$Sendcountry,
									 'Sendprovinoce'=>$Sendprovinoce,
									 'Sendcitycode'=>$Sendcitycode,
									 'Sendcity'=>$Sendcity,
									 'Sendcounty'=>$Sendcounty,
									 'Sendzipcode'=>$Sendzipcode,
									 'Sendaddress'=>$Sendaddress,
									 'monthlyAccount'=>$monthlyAccount,
									 'customsBatchNumber'=>$customsBatchNumber,
									 'taxSetAccounts'=>$taxSetAccounts,
									 'expressType'=>'全球顺',
									 'taxPayType'=>'寄付',
									 'payType'=>'寄方付',
									 'recCompany'=>'*',
									 'recConcact'=>$order_username,
									 'recTelphone'=>$order_userphone,
									 'recMobile'=>$order_userphone,
									 'recCountry'=>'中国',
									 'recProvinoce'=>$location_p,
									 'recCityCode'=>'cn',
									 'recCity'=>$location_c,
									 'recCounty'=>$location_a,
									 'recZipcode'=>'100000',
									 'recAddress'=>$order_address,
									 'turnover'=>$totalprice,
									 'freight'=>0,
									 'freightCurrency'=>'CNY',
									 'buyersNickname'=>$order_username,
									 'ordersName'=>$order_username,
									 'ordersDocumentType'=>'身份证',
									 'orderDocumentNumber'=>strtoupper($identity)
								),
								'Goods' =>array(
									array(
										'@attributes' => array(
											'code'=>$product_name,
											'name'=>$product_name,
											'unit'=>'个',
											'model'=>$product_name,
											'brand'=>$product_name,
											'unitPrice'=>$product_now_price,
											'count'=>$rcount,
											'currencyType'=>'CNY',
											'sourceArea'=>'芬兰'
									))
								)
							))
					);

					$xml = array2xml($xmlArray,"Request"); // 生成XML
					//echo $xml;	exit;
					$arr = array(
						"verifyCode"=>$checkWord, // verifyCode
						'Servicefun' => 'issuedOrder', // webserver 方法
						'server' => 'http://cbti.sfb2c.com:8003/CBTA/ws/orderService?wsdl', // webserver 服务
						'authtoken' =>$token, // authtoken
						'headerNamespace' => 'http://cbti.sfb2c.com:8003/CBTA/' // SoapHeader命名空间
					);

					$Api = new orderService;
					$re_xml=$Api->getOrderData($xml,$arr);
					$xml_obj = simplexml_load_string($re_xml,'SimpleXMLElement', LIBXML_NOCDATA);
					$errorCode = $xml_obj->errorCode;
					$errorDesc = $xml_obj->errorDesc;
					$mailNo = $xml_obj->mailNo;
					$printUrl = $xml_obj->printUrl;
					if($errorCode=="001"){
						$express_num=$mailNo;
					}else{
						if($errorCode=="009"){
							$json["status"] = 10011;
							$json["line"] = 315;
							$json["msg"] = "此订单已经发货，不能重复提交！";
						}else{
							$json["status"] = 10012;
							$json["line"] = 319;
							$json["msg"] = "顺接口返回错误,错误码:$errorCode,错误信息:$errorDesc！";
						}
							mysql_close($link);
							$jsons=json_encode($json);
							die($jsons);
							exit;
					}
			}
		}
		/* 顺风进口接口 End */

		$query = "select auto_cus_time,is_kuaidi from weixin_commonshops where isvalid = true and customer_id = ".$customer_id;
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		$is_kuaidi = 0; //快递查询方式：0免费查询，1付费查询 默认0
		while ($row = mysql_fetch_object($result)) {
		    $auto_cus_time = $row->auto_cus_time;
		    $is_kuaidi = $row->is_kuaidi;
		}

		if(empty($auto_cus_time) || $auto_cus_time <= 0){ //如没有设置时间默认为7天
			$auto_cus_time = 7;
		}
		
		/*查询云店开关以及自动收货开关和默认收货时间*/
		$sql_yundian_receipt = "select yundian_onoff,receipt_onoff,receipt_time from ".WSY_REBATE.".weixin_yundian_setting where customer_id='{$customer_id}' and isvalid = true ";
        $result_yundian_receipt = _mysql_query($sql_yundian_receipt) or die("Query_yundian_switch error : ".mysql_error());
        $res_yundian_receipt = mysql_fetch_assoc($result_yundian_receipt);
        /*查询云店开关以及自动收货开关和收货时间*/
        if($res_yundian_receipt['yundian_onoff'] == 1 && $res_yundian_receipt['receipt_onoff'] == 1 && $yundian_self == 1){
        	if(!empty($res_yundian_receipt['receipt_time']) && $res_yundian_receipt['receipt_time'] > 0){
        		$auto_cus_time = $res_yundian_receipt['receipt_time'];        		
        	}
        }

		if($express_id==-2 && $errorCode==001){ //修改默认的自动收货时间
			//如果是顺丰进口业务
			$query_Uorder = "update weixin_commonshop_orders set sendway=1,sendstatus = 1,confirm_sendtime=now(),auto_receivetime = DATE_ADD( now(), INTERVAL ".$auto_cus_time." DAY ),send_express_id=".$express_id.",expressnum='".$express_num."',send_remarks='".$express_remark."',printUrl='".$printUrl."'$order_con where batchcode='".$batchcode."'";

			$sql = "update weixin_commonshop_order_prices set sendway=1,sendstatus=1,confirm_sendtime=now(),send_express_id=".$express_id.",expressnum='".$express_num."',send_remarks='".$express_remark."',printUrl='".$printUrl."' where isvalid=true and batchcode='" . $batchcode . "'";
			_mysql_query($sql) or die('7_1 Query failed: ' . mysql_error());
		}else if($express_id>0){
			/* 发送Message */

			$content = "亲，您有一笔订单【已发货】\n\n商品：".$pro_name."\n时间：".date( "Y-m-d H:i:s")."\n快递：".$expressname."";
			if(!empty($express_remark)){
				$content=$content."\n备注：".$express_remark;
			}

			if ($is_kuaidi == 1) {
				$kd_href = Protocol.$http_host."/weixinpl/back_newshops/Distribution/settings/kuaidi_ck.php?is_web=1&customer_id=".passport_encrypt((string)$customer_id)."&batchcode=".$batchcode."&postid=".trim($express_num)."&type=".$expressname;
			} elseif ($is_kuaidi == 2) {
				$kd_href = Protocol.$http_host."/weixinpl/back_newshops/Distribution/settings/kuaidi100.php?is_web=1&customer_id=".passport_encrypt((string)$customer_id)."&batchcode=".$batchcode."&postid=".trim($express_num);
			} else {
		    	$kd_href = Protocol."m.kuaidi100.com/index_all.html?type=".$expressname."&postid=".trim($express_num)."#result";
			}

			$content=$content."\n\n<a href='".$kd_href."'>【查看物流进度】</a>\n<a href='".Protocol.$http_host."/weixinpl/mshop/orderlist_detail.php?batchcode=".$batchcode."&customer_id=".passport_encrypt((string)$customer_id)."&fromuser=".$order_fromuser."'>【查看订单详情】</a>";
			//_file_put_contents("log/order_send_" . $today . ".txt", "SendMessage=======".var_export($content,true)."\r\n",FILE_APPEND);
			// $shopmessage->SendMessage($content,$order_fromuser,$customer_id);
			$sendMessageContent[] = array(
								'openid' => $order_fromuser,
								'content'=> $content
							);

			/* 发送Message End */

			$query_Uorder = "update weixin_commonshop_orders set sendway=1,sendstatus = 1,confirm_sendtime=now(),auto_receivetime = DATE_ADD( now(), INTERVAL ".$auto_cus_time." DAY ),expressname='".$expressname."',send_express_id=".$express_id.",expressnum='".$express_num."',send_remarks='".$express_remark."'$order_con where batchcode='".$batchcode."'";

			$sql = "update weixin_commonshop_order_prices set sendway=1,sendstatus=1,confirm_sendtime=now(),send_express_id=".$express_id.",expressnum='".$express_num."',send_remarks='".$express_remark."' where isvalid=true and batchcode='" . $batchcode . "'";
			_mysql_query($sql) or die('389 Query failed: ' . mysql_error());

		}else if($express_id==0){
			$query_Uorder = "update weixin_commonshop_orders set sendway=1,sendstatus = 2,confirm_sendtime=now(),confirm_receivetime=now(),send_express_id=".$express_id.",expressnum='".$express_num."',send_remarks='".$express_remark."'$order_con where batchcode='".$batchcode."'";

			$sql = "update weixin_commonshop_order_prices set sendway=1,sendstatus=2,confirm_sendtime=now(),confirm_receivetime=now(),send_express_id=".$express_id.",expressnum='".$express_num."',send_remarks='".$express_remark."' where isvalid=true and batchcode='" . $batchcode . "'";
			//echo $sql.'====2';
			_mysql_query($sql) or die('395 Query failed: ' . mysql_error());

		}

		//货到付款发放佣金
		if($paystyle =="货到付款" || $sendstyle == "货到付款")
		{
			$reward_money		= 0;
			$needScore			= 0;
			$reward_currency	= 0;
			$payCurrency		= 0;
			$query_reward="select reward_money,needScore,currency,pay_currency from weixin_commonshop_order_prices where isvalid=true and batchcode='".$batchcode."' limit 0,1";
			$result_reward = _mysql_query($query_reward) or die('W376 Query failed: ' . mysql_error());
			while ($row_r = mysql_fetch_object($result_reward)) {
				$reward_money		= $row_r->reward_money;
				$needScore			= $row_r->needScore;
				$reward_currency	= $row_r->currency;
				$payCurrency		= $row_r->pay_currency;
			}

			$reuslt_getmoney_common = $shopmessage->GetMoney_Common($batchcode,$customer_id,$reward_money,$user_id,$exp_user_id,0,-1,$needScore,$card_member_id,$reward_currency,$payCurrency);

		}

		//_file_put_contents("log/order_send_" . $today . ".txt", "query_Uorder=======".var_export($query_Uorder,true)."\r\n",FILE_APPEND);
		_mysql_query($query_Uorder) or die("Query_Uorder error : ".mysql_error());

        //判断云店开关是否开启
        $sql_yundian = "select yundian_onoff from ".WSY_REBATE.".weixin_yundian_setting where customer_id='{$customer_id}' and isvalid = true ";
        $result_yundian_switch = _mysql_query($sql_yundian) or die("Query_yundian_switch error : ".mysql_error());
        $res = mysql_fetch_assoc($result_yundian_switch);
        $sql_yuandian_order = "SELECT yundian_id,yundian_self from weixin_commonshop_orders where batchcode='{$batchcode}' and customer_id='{$customer_id}' and isvalid = true ";
        $result_yundian_order = _mysql_query($sql_yuandian_order) or die("Query_yuandian_order error : ".mysql_error());
        $res_o = mysql_fetch_assoc($result_yundian_order);

        $str = '平台';
        if($res['yundian_onoff'] == true && $res_o['yundian_id']>0 && $res_o['yundian_self'] ==1)
        {
            $str = '云店店主';
            //查询云店店主昵称
            $sql_user = "SELECT realname from ".WSY_USER.".weixin_yundian_keeper where id='{$res_o['yundian_id']}'";
            $result_user = _mysql_query($sql_user) or die("Query_sql_user error : ".mysql_error());
            $res_u = mysql_fetch_assoc($result_user);
            $log_username = $res_u['realname'];
        }

        //判断是否有关联的卡密id
        if (!empty($camilo_ids)) {
            $camilo_log_str = "卡密操作\n订单号：{$batchcode}";
            //有则开启卡密相关操作
            $sql = 'UPDATE '.WSY_PROD.".weixin_commonshop_camilo SET status = 3 WHERE customer_id='{$customer_id}'
                    AND product_id='{$pid}' AND isvalid=1 AND batchcode='{$batchcode}' AND id in({$camilo_ids})";
            $res = _mysql_query($sql) or die("CA Counter1 Query failed :".__LINE__.mysql_error());
            if ($res) {
                $camilo_log_str .= "\n卡密修改成功(已使用)";
                //查询卡密号
                $sql = 'SELECT camilo FROM '.WSY_PROD.".weixin_commonshop_camilo WHERE customer_id='{$customer_id}' AND product_id='{$pid}' AND status=3 AND isvalid=1 AND batchcode='{$batchcode}' AND id in({$camilo_ids})";
                $camilo_log_str .= "\n卡密查询sql:{$sql}";
                $res = _mysql_query($sql) or die("CA Counter1 Query failed :".__LINE__.mysql_error());
                //获取卡密信息转数组
                $shop_camilo = array();
                while($row=mysql_fetch_object($res)){
                    $shop_camilo[] = $row->camilo;
                }
                $shop_camilo_str = '';
                foreach ($shop_camilo as $v) {
                    $shop_camilo_str .= "\n$v;";
                }
                $c_id = passport_encrypt($customer_id);
                $msg_content = "亲，您的订单已发货：\n点击蓝色区域可以跳转订单详情查看\n您的订单<a href='/weixinpl/mshop/orderlist_detail.php?customer_id={$c_id}&batchcode={$batchcode}'>{$batchcode}</a>已发货\n产品卡密是：{$shop_camilo_str}\n可点击订单号查看订单详情";
                $camilo_log_str .= "\n推送信息:\n{$msg_content}";
                //查询订单用户的微信id
                $sql = 'SELECT weixin_fromuser FROM '.WSY_USER.".weixin_users WHERE customer_id='{$customer_id}' AND
        id='{$user_id}' LIMIT 1";
                $res = _mysql_query($sql) or die("CA Counter1 Query failed :".__LINE__.mysql_error());
                //获取产品信息转数组
                $user_res=mysql_fetch_array($res);
                $camilo_log_str .= "\n用户信息：".json_encode($user_res);
                //推送消息
                $shopmessage->SendMessage($msg_content, $user_res['weixin_fromuser'], $customer_id);
                $time = date('Y-m-d H:i:s', time());
                //切割字符串为数组
                $camilo_ids = explode(',', $camilo_ids);
                //插入卡密记录日志
                foreach ($camilo_ids as $v) {
                    $sql = 'INSERT INTO '.WSY_PROD.".weixin_commonshop_camilo_log(customer_id,camilo_id,createtime,operation,comment) VALUES('{$customer_id}', '{$v}', '{$time}', '发货', '修改状态为（已使用）')";
                    _mysql_query($sql)or die("CA Counter1 Query failed :".__LINE__.mysql_error());
                }
            }
        } else {
            $camilo_log_str = "卡密操作\n订单号：{$batchcode}, 该订单没有卡密";
        }
        $LogOpe = new LogOpe('order.class.php');
        $LogOpe->log_insert($camilo_log_str);

		//添加发货日志
		$query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
		values('".$batchcode."',4,'".$str."发货[物流：".$expressname.",单号：".$express_num."]','".$log_username."',now(),1)";
		_mysql_query($query_logs) or die("L365 query error  : ".mysql_error());

		/* 查询发货时间 */
		$query_send_time = " select confirm_sendtime from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 1";
		//_file_put_contents("log/order_send_" . $today . ".txt", "query_send_time=======".var_export($query_send_time,true)."\r\n",FILE_APPEND);
		$result_send_time = _mysql_query($query_send_time);
		$send_time = mysql_result($result_send_time,0,0);
		/* 查询发货时间 End */

		//券类订单发放二维码
		if($is_QR==1){
			//_file_put_contents("log/order_send_" . $today . ".txt", "is_QR=======".var_export($is_QR,true)."\r\n",FILE_APPEND);
			$shopmessage->GetQR($batchcode,$order_fromuser,$customer_id);
		}


		//拒单分担运费
		if( 2 == $is_sendorder ){

			$express_url = Protocol . $_SERVER['HTTP_HOST'] . "/addons/index.php/f2c/Ordering_Service/order_reject_freight?customer_id=" . $customer_id . "&batchcode=" . $batchcode;
			_file_put_contents("log/order_send_" . $today . ".txt", "express_url=======".var_export($express_url,true)."\r\n",FILE_APPEND);
			$param = null;


			$oCurl = curl_init();
			if(stripos($express_url,"https://")!==FALSE){
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
			}
			if (is_string($param)) {
				$strPOST = $param;
			} else {
				$aPOST = array();
				foreach($param as $key=>$val){
					$aPOST[] = $key."=".urlencode($val);
				}
				$strPOST =  join("&", $aPOST);
			}
			curl_setopt($oCurl, CURLOPT_URL, $express_url);
			curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt($oCurl, CURLOPT_POST,true);
			curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
			$sContent = curl_exec($oCurl);
			$aStatus = curl_getinfo($oCurl);
			curl_close($oCurl);
			if(intval($aStatus["http_code"])==200){
				//return $sContent;
			}else{
				//return false;
			}

			_file_put_contents("log/order_send_" . $today . ".txt", "is_sendorder=======".var_export($sContent,true)."\r\n",FILE_APPEND);
		}
		//拒单分担运费 End

		//插入发送消息记录，定时计划发送
		if (!empty($sendMessageContent)) {
			$query = "INSERT INTO send_weixinmsg_log (customer_id, createtime, type, status, send_limit, content, openid, is_dealing, remark) VALUES ";
			$query_v = "";
			foreach ($sendMessageContent as $val) {
				$query_v .= "(".$customer_id.", now(), 2, 0, 0, '".mysql_escape_string($val['content'])."', '".$val['openid']."', false, ''),";
			}

			$query_v = trim($query_v, ',');

			if (!empty($query_v)) {
				$query .= $query_v;
				_mysql_query($query) or die('Query msg failed:'.mysql_error());
			}
		}

		//status状态信息   1发货成功  2发货失败，顺丰进口返回客户单号已存在
		//$error =mysql_error();
		mysql_close($link);

		$json["status"] = 0;
		$json["msg"] = "编号：".$batchcode."，发货成功";
		$json["time"] = $send_time;
		$json["deliverySetting"] = $deliverySetting;

        $sys_plat_log = new \model_sys_plat_log($customer_id);
        $inser_log = $sys_plat_log->add_log("shop_system_order_management","订单号：".$batchcode." 1笔待发货，已发货；");

	break;

	case "pay":   //确认支付

        require_once (ROOT_DIR . "/wsy_pub/admin/model/security_sms.php");  //短信验证
        $security_sms = new \model_security_sms($customer_id);
        $check_result = $security_sms->sms_verification_check('plat_pay');
        if ($check_result["errcode"] != 0){
            $check_result = json_encode($check_result,JSON_UNESCAPED_UNICODE);
            die($check_result);
        }

		$card_member_id = -1;
		$totalprice = $configutil->splash_new($_POST["totalprice"]);
		_file_put_contents("log/order_pay_" . $today . ".txt", "\r\ntotalprice=======".var_export($totalprice,true)."\r\n",FILE_APPEND);
		//查询订单支付状态
		$orgin_paystatus	= -1;
		$sendstyle			= "";
		$paystyle 			= "";
		$user_id 			= -1;
		$pay_batchcode 			= -1;
		$supply_id 			= -1;
		$exp_user_id 		= -1;		 //订单上级
		$share_user_id		= '';
		$query_stat = "select paystatus,pay_batchcode,sendstyle,user_id,paystyle,exp_user_id,supply_id,card_member_id,pid,rcount,share_user_id from weixin_commonshop_orders where  isvalid=true and customer_id= ".$customer_id." and batchcode='".$batchcode."'";
		_file_put_contents("log/order_pay_" . $today . ".txt", "query_stat=======".var_export($query_stat,true)."\r\n",FILE_APPEND);
		$result_stat = _mysql_query($query_stat) or die("Query_stat error : ".mysql_error());
		while ($row_stat = mysql_fetch_object($result_stat)) {
			$orgin_paystatus 	= $row_stat->paystatus;
			$sendstyle 			= $row_stat->sendstyle;
			$paystyle 			= $row_stat->paystyle;
			$user_id 			= $row_stat->user_id;
			$exp_user_id 		= $row_stat->exp_user_id;
			$card_member_id		= $row_stat->card_member_id;
			$supply_id 			= $row_stat->supply_id;
			$pay_batchcode 		= $row_stat->pay_batchcode;
			$pid                = $row_stat->pid;
			$rcount             = $row_stat->rcount;
			$share_user_id		= $row_stat->share_user_id; //分享产品用户ID
		}
		/*$payCurrency = 0;
		$sql = "select currency from order_currencyandcoupon_t where pay_batchcode='".$pay_batchcode."'";
		$result = _mysql_query($sql) or die("Query_stat error : ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$payCurrency = $row->currency;
		}*/

		if($orgin_paystatus == 0){

			//队列活动
	        $is_queue_goods = 0;
	        $queue_order = new queue_order($customer_id);
	        $is_queue_goods = $queue_order->check_queue_goods($pay_batchcode);
	        if($is_queue_goods==1)
	        {
	            $queue_order->create_queue_order($user_id, '', $pay_batchcode); 
	        }

			//查询此订单返佣总金额
			$reward_money		= 0;
			$needScore			= 0;
			$reward_currency	= 0;
			$payCurrency		= 0;
			$query_reward="select reward_money,needScore,currency,pay_currency from weixin_commonshop_order_prices where isvalid=true and batchcode='".$batchcode."' limit 0,1";
			$result_reward = _mysql_query($query_reward) or die('W376 Query failed: ' . mysql_error());
			while ($row_r = mysql_fetch_object($result_reward)) {
				$reward_money		= $row_r->reward_money;
				$needScore			= $row_r->needScore;
				$reward_currency	= $row_r->currency;
				$payCurrency		= $row_r->pay_currency;
			}

			//货到付款是下单的时候打印小票的
			if($paystyle !="货到付款" && $sendstyle != "货到付款"){
				_file_put_contents("log/order_pay_" . $today . ".txt", "paystyle=======".var_export($paystyle,true)."\r\n",FILE_APPEND);
				_file_put_contents("log/order_pay_" . $today . ".txt", "http_host=======".var_export($http_host,true)."\r\n",FILE_APPEND);
				$shopmessage->GetTicket($http_host,$batchcode);
			}

			//更新支付状态
			$query_pay="update weixin_commonshop_orders set Pay_Method=1,paystatus=1,paytime=now() where isvalid=true and customer_id= ".$customer_id." and batchcode='".$batchcode."'";
			_file_put_contents("log/order_pay_" . $today . ".txt", "query_pay=======".var_export($query_pay,true)."\r\n",FILE_APPEND);
			_mysql_query($query_pay);

			$sql = "update weixin_commonshop_order_prices set Pay_Method=1,paystatus=1,paytime=now() where isvalid=true and batchcode='" . $batchcode . "'";
			_mysql_query($sql) or die('8_1 Query failed: ' . mysql_error());

			//插入支付记录
			$query_paystyle="insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)values('".$batchcode."',2,'订单支付 － 后台支付','".$log_username."',now(),1)";
			_file_put_contents("log/order_pay_" . $today . ".txt", "query_paystyle=======".var_export($query_paystyle,true)."\r\n",FILE_APPEND);
			_mysql_query($query_paystyle);


			/*卡密相关 START*/
            //日志记录
            $str = "卡密操作\n支付操作：支付订单：{$pay_batchcode}";
            //根据pid（产品id）查询出相应的产品
            $sql = 'SELECT is_virtual, is_camilo FROM '.WSY_PROD.".weixin_commonshop_products WHERE customer_id='{$customer_id}' AND
        id='{$pid}' LIMIT 1";
            $res = _mysql_query($sql) or die("CA Counter1 Query failed :".__LINE__.mysql_error());
            //获取产品信息转数组
            $products_res=mysql_fetch_array($res);
            //日志记录
            $str .= "\n产品信息：".json_encode($products_res);
            //判断是否是虚拟产品且开启了卡密功能
            if ($products_res['is_virtual']==1 && $products_res['is_camilo']==1) {
                $str .= "\n此产品是虚拟产品且开启了卡密";
                //开启则查询可用的卡密
                $sql = 'SELECT id FROM '.WSY_PROD.".weixin_commonshop_camilo WHERE customer_id='{$customer_id}' AND product_id='{$pid}' AND status=1 AND isvalid=1 limit {$rcount}";
                $res = _mysql_query($sql) or die("CA Counter1 Query failed :".__LINE__.mysql_error());
                //获取卡密信息转数组
                $camilo_res = [];
                while($row=mysql_fetch_object($res)){
                    $camilo_res[] = $row->id;
                }
                //记录日志
                $str .= "\n可用卡密：".json_encode($camilo_res).',数量为:'.count($camilo_res).'产品数量为:'.$rcount;
                //判断卡密的数量是否满足订单的数量
                if (count($camilo_res) >= $rcount) {
                    //记录日志
                    $str .= ',数量满足';
                    //满足修改卡密信息
                    $camilo_res_str= implode(',', $camilo_res);
                    $sql = 'UPDATE '.WSY_PROD.".weixin_commonshop_camilo SET batchcode = {$batchcode}, status = 2 WHERE id in({$camilo_res_str})";
                    $res = _mysql_query($sql) or die("CA Counter1 Query failed :".__LINE__.mysql_error());
                    //成功则插入日志
                    if ($res) {
                        $str .= "\n卡密修改成功(已占用)";
                        $time = date('Y-m-d H:i:s', time());
                        //插入卡密记录日志
                        foreach ($camilo_res as $v) {
                            $sql = 'INSERT INTO '.WSY_PROD.".weixin_commonshop_camilo_log(customer_id,camilo_id,createtime,operation,comment) VALUES('{$customer_id}', '{$v}', '{$time}', '支付', '修改状态为（已占用）')";
                            _mysql_query($sql)or die("CA Counter1 Query failed :".__LINE__.mysql_error());
                        }

                        //修改相应的订单信息
                        $sql = "UPDATE weixin_commonshop_orders SET camilo_ids='{$camilo_res_str}' WHERE batchcode='{$batchcode}'";
                        $res = _mysql_query($sql)or die("CA Counter1 Query failed :".__LINE__.mysql_error());
                        if ($res) {
                            $str .= "\n订单修改成功";
                        }
                    }
                } else {
                    $str .= ",数量不满足，默认卡密库存不足";
                }
            } else {
                $str .= "\n此产品不是虚拟产品或没有开启卡密";
            }
            $LogOpe = new LogOpe('order.class.php');
            $LogOpe->log_insert($str);
            /*卡密相关 END*/


			/* 商城设置 推广员发展模式 */
			$issell = false;
			$reward_type = 1;
			$init_reward= 1;
			$is_autoupgrade=0;
			$auto_upgrade_money_2 = 0;
			$Distr_type = 0;//会员锁定关系模式
			$query_shop = "select is_autoupgrade,auto_upgrade_money_2,reward_type,issell,init_reward,distr_type from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
			_file_put_contents("log/order_pay_" . $today . ".txt", "query_shop=======".var_export($query_shop,true)."\r\n",FILE_APPEND);
		    $result_shop = _mysql_query($query_shop) or die('Query_shop failed: ' . mysql_error());
		    while ($row_shop = mysql_fetch_object($result_shop)) {
				$issell = $row_shop->issell;
				$reward_type = $row_shop->reward_type;
				$init_reward = $row_shop->init_reward;
				$is_autoupgrade = $row_shop->is_autoupgrade;
				$auto_upgrade_money_2 = $row_shop->auto_upgrade_money_2;
				$Distr_type = $row_shop->distr_type;
		    }
			if ($Distr_type == 1) //判断是否设置支付后锁定
			{
			    //查询当前关系是否锁定
				$Is_lock = 0;
				$query  = "SELECT is_lock FROM weixin_users WHERE isvalid = true AND id = '".$user_id."' limit 0,1";
				$result = _mysql_query($query) or die('Query failed9: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) 
				{
					$Is_lock = $row->is_lock;
				}
				if ($Is_lock == 0) //修改关系更改为锁定
				{
					if ($share_user_id == '' || $share_user_id == -1 || $share_user_id == $user_id) 
					{
						$share_user_id = -1;
					}
					$Sql = "UPDATE weixin_users SET parent_id = '{$share_user_id}',is_lock = 1 WHERE isvalid = true AND id = '{$user_id}'";
					_mysql_query($Sql);
				}
			}

		    // var_dump($issell);
		    // var_dump($is_autoupgrade);

		    if($issell and 2 == $is_autoupgrade){
			    //自动更新为推广员
			    if($totalprice>=$auto_upgrade_money_2){
				    //条件满足

					//推广员有效期设置
					$is_promoter_permanent 				= 1;	//推广员是否永久有效 1.是 0.否
					$promoter_term_of_validity_initial 	= 0;	//推广员初始有效期（天）
					$query_shop_set = "SELECT is_promoter_permanent,promoter_term_of_validity_initial FROM weixin_commonshops_extend WHERE customer_id=".$customer_id." AND isvalid=true";
					$result_shop_set = _mysql_query($query_shop_set) or die('Query_shop_set failed:'.mysql_error());
					while( $row_shop_set = mysql_fetch_object($result_shop_set) ){
						$is_promoter_permanent 				= $row_shop_set -> is_promoter_permanent;
						$promoter_term_of_validity_initial 	= $row_shop_set -> promoter_term_of_validity_initial;
					}

					if( $is_promoter_permanent == 1 ){
						$term_of_validity = '3000-01-01 00:00:00';
					} else {
						$term_of_validity = date('Y-m-d',strtotime('+'.$promoter_term_of_validity_initial.' day'));
					}

					$qr_info_id =-1;
					$query_qrin = "select id,scene_id from weixin_qr_infos where type=1 and isvalid=true and customer_id=".$customer_id." and foreign_id=".$user_id;
					_file_put_contents("log/order_pay_" . $today . ".txt", "query_qrin=======".var_export($query_qrin,true)."\r\n",FILE_APPEND);
					$result_qrin = _mysql_query($query_qrin) or die('Query_qrin failed: ' . mysql_error());
					while ($row_qrin = mysql_fetch_object($result_qrin)) {
						$scene_id = $row_qrin->scene_id;
						$qr_info_id=$row_qrin->id;
					}
					if($qr_info_id<0){
						$scene_id=1;
						$query_qrin2="select max(scene_id) as scene_id from weixin_qr_infos where isvalid=true and customer_id=".$customer_id." limit 1";
						_file_put_contents("log/order_pay_" . $today . ".txt", "query_qrin2=======".var_export($query_qrin2,true)."\r\n",FILE_APPEND);
						$result_qrin2 = _mysql_query($query_qrin2) or die('Query_qrin2 failed: ' . mysql_error());
						while ($row = mysql_fetch_object($result_qrin2)) {
							$scene_id = $row->scene_id;
						}
						$scene_id++;
						$query_qrin3 = "insert into weixin_qr_infos(foreign_id,type,scene_id,isvalid,customer_id) values(".$user_id.",1,".$scene_id.",true,".$customer_id.")";
						_file_put_contents("log/order_pay_" . $today . ".txt", "query_qrin3=======".var_export($query_qrin3,true)."\r\n",FILE_APPEND);
						_mysql_query($query_qrin3);
						$qr_info_id = mysql_insert_id();
					}

					$qr_id=-1;
					$status = 0;
					$query_qr = "select id,ticket,status from weixin_qrs where customer_id=".$customer_id." and isvalid=true and type=1 and qr_info_id=".$qr_info_id;
					_file_put_contents("log/order_pay_" . $today . ".txt", "query_qr=======".var_export($query_qr,true)."\r\n",FILE_APPEND);
					$result_qr = _mysql_query($query_qr) or die('Query_qr failed: ' . mysql_error());
					while ($row_qr = mysql_fetch_object($result_qr)) {
						$qr_id = $row_qr->id;
						$status= $row_qr->status;
					}

					//查询上级
					$parent_id=-1;
					$query="select parent_id from weixin_users where isvalid=true and id=".$user_id." limit 0,1";
					_file_put_contents("log/order_pay_" . $today . ".txt", "query457=======".var_export($query,true)."\r\n",FILE_APPEND);
					$result = _mysql_query($query) or die('Query failed9: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
						$parent_id = $row->parent_id;
					}

				    if($qr_id<0){
						$action_name ="QR_LIMIT_SCENE";
						$query_qrs = "insert into weixin_qrs(action_name,expire_seconds,qr_info_id,customer_id,isvalid,createtime,type,status) values('".$action_name."',-1,".$qr_info_id.",".$customer_id.",true,now(),1,1)";
						_file_put_contents("log/order_pay_" . $today . ".txt", "query_qrs=======".var_export($query_qrs,true)."\r\n",FILE_APPEND);
						_mysql_query($query_qrs);
						$qr_id = mysql_insert_id();
					}else{
						//通过推广员
						$query_up_qrs = "update weixin_qrs set status=1 where id=".$qr_id;
						_file_put_contents("log/order_pay_" . $today . ".txt", "query_up_qrs=======".var_export($query_up_qrs,true)."\r\n",FILE_APPEND);
						_mysql_query($query_up_qrs);
						$query_up_promoter = "update promoters set status=1,parent_id=".$parent_id." where user_id=".$user_id;
						_file_put_contents("log/order_pay_" . $today . ".txt", "query_up_promoter=======".var_export($query_up_promoter,true)."\r\n",FILE_APPEND);
						_mysql_query($query_up_promoter);
					}

					$pwd = "";
					$before_customer_id=-1;
					$promoter_id = -1;
					$query_promot = "select id,pwd,customer_id from promoters where  isvalid=true  and user_id=".$user_id;
					_file_put_contents("log/order_pay_" . $today . ".txt", "query_promot=======".var_export($query_promot,true)."\r\n",FILE_APPEND);
					$result_promot = _mysql_query($query_promot) or die('Query_promot failed: ' . mysql_error());
					while ($row_promot = mysql_fetch_object($result_promot)) {
						$promoter_id = $row_promot->id;
						$pwd=$row_promot->pwd;
						$before_customer_id = $row_promot->customer_id;
				    }
					$generation=1;
					if($parent_id>0){
						$query_promot2 = "select generation from promoters where isvalid=true  and user_id=".$parent_id;
						_file_put_contents("log/order_pay_" . $today . ".txt", "query_promot2=======".var_export($query_promot2,true)."\r\n",FILE_APPEND);
						$result_promot2 = _mysql_query($query_promot2) or die('Query_promot2 failed: ' . mysql_error());
						while ($row_promot2 = mysql_fetch_object($result_promot2)) {
							$generation = $row_promot2->generation;
						}
						$generation=$generation+1;
					}

					if($promoter_id<0){
						$pwd="888888";
						$query_promot3 ="insert into promoters(user_id,pwd,isvalid,customer_id,parent_id,createtime,status,generation,term_of_validity) values(".$user_id.",'888888',true,".$customer_id.",".$parent_id.",now(),1,".$generation.",'".$term_of_validity."')";
						_file_put_contents("log/order_pay_" . $today . ".txt", "query_promot3=======".var_export($query_promot3,true)."\r\n",FILE_APPEND);
						_mysql_query($query_promot3);
						//$error=mysql_error();
						//echo $error;
						//增加推广员数量
						if($parent_id>0){
							$query_promot4 = "update promoters set promoter_count= promoter_count+1 where isvalid=true and status=1 and user_id=".$parent_id;
							_file_put_contents("log/order_pay_" . $today . ".txt", "query_promot4=======".var_export($query_promot4,true)."\r\n",FILE_APPEND);
							_mysql_query($query_promot4);
						}
					}else{
						$query_promot5="update promoters set parent_id=".$parent_id.",status=1,term_of_validity='".$term_of_validity."',createtime=now() where id=".$promoter_id;
						_file_put_contents("log/order_pay_" . $today . ".txt", "query_promot5=======".var_export($query_promot5,true)."\r\n",FILE_APPEND);
						_mysql_query($query_promot5);
					}
			   }
		    }

			//产品更新库存
			/* $pid=-1;
			$rcount = 0;
			$prvalues="";
			$query_orders="select pid,rcount,prvalues from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
			_file_put_contents("log/order_pay_" . $today . ".txt", "query_orders=======".var_export($query_orders,true)."\r\n",FILE_APPEND);
			$result_orders = _mysql_query($query_orders) or die('Query_orders failed: ' . mysql_error());
			while ($row_orders = mysql_fetch_object($result_orders)) {
				$pid = $row_orders->pid;
				$rcount = $row_orders->rcount;
				$prvalues= $row_orders->prvalues;

				$prvalues= rtrim($prvalues,"_");
				if(!empty($prvalues)){
					$query_num_sub="update weixin_commonshop_product_prices set storenum= storenum-".$rcount." where product_id=".$pid." and proids='".$prvalues."'";
				}else{
					$query_num_sub="update weixin_commonshop_products set storenum= storenum-".$rcount." where id=".$pid;
				}
				_mysql_query($query_num_sub);
				_file_put_contents("log/order_pay_" . $today . ".txt", "query_num_sub=======".var_export($query_num_sub,true)."\r\n",FILE_APPEND);
			} */
			//_file_put_contents("log/order_pay_" . $today . ".txt", "1.GetMoney_Common=======".var_export($batchcode,true)."\r\n",FILE_APPEND);
			//_file_put_contents("log/order_pay_" . $today . ".txt", "2.GetMoney_Common=======".var_export($customer_id,true)."\r\n",FILE_APPEND);
			//_file_put_contents("log/order_pay_" . $today . ".txt", "3.GetMoney_Common=======".var_export($reward_money,true)."\r\n",FILE_APPEND);
			//_file_put_contents("log/order_pay_" . $today . ".txt", "4.GetMoney_Common=======".var_export($user_id,true)."\r\n",FILE_APPEND);
			//_file_put_contents("log/order_pay_" . $today . ".txt", "5.GetMoney_Common=======".var_export($exp_user_id,true)."\r\n",FILE_APPEND);
			//查询购物币是否参与分佣
			//
			//
			/* if($paystyle=="购物币支付"){
				$sql_curr = "SELECT isOpenCurrency FROM weixin_commonshop_currency WHERE customer_id=".$customer_id;
				$curr_res = _mysql_query($sql_curr) or die('Query failed sc43: ' . mysql_error());
				while($row=mysql_fetch_object($curr_res)){
					$isOpenCurrency = $row->isOpenCurrency;
				}

				if($isOpenCurrency == 1 ){
					$sql_rew = "SELECT p.reward_money,o.exp_user_id,o.user_id FROM weixin_commonshop_orders o LEFT JOIN weixin_commonshop_order_prices p ON o.batchcode=p.batchcode WHERE o.batchcode = ".$batchcode;
					$res_rew = _mysql_query($sql_rew) or die('Query failed sc51: ' . mysql_error());
					while($row=mysql_fetch_object($res_rew)){
						$reward_money = $row->reward_money;
						$exp_user_id  = $row->exp_user_id;
						$user_id = $row->user_id;
					}
					$reuslt_getmoney_common = $shopmessage->GetMoney_Common($batchcode,$customer_id,$reward_money,$user_id,$exp_user_id,0,-1,$needScore,$card_member_id,$reward_currency);
				}
			}else{
				$reuslt_getmoney_common = $shopmessage->GetMoney_Common($batchcode,$customer_id,$reward_money,$user_id,$exp_user_id,0,-1,$needScore,$card_member_id,$reward_currency);
			} */

			$reuslt_getmoney_common = $shopmessage->GetMoney_Common($batchcode,$customer_id,$reward_money,$user_id,$exp_user_id,0,-1,$needScore,$card_member_id,$reward_currency,$payCurrency);

			//提醒供应商发货
			if( $supply_id > 0 ){
				$supply_fromuser_arr = $shopmessage->query_openid($customer_id,$supply_id);
				$supply_fromuser = $supply_fromuser_arr["query_openid"];
				$weixin_name	= $fromuser_arr["name"]."(".$fromuser_arr["weixin_name"].")";

				$content = "亲，您有一笔新订单，请及时发货\n订单：".$batchcode."\n顾客：".$weixin_name."\n时间：".date( "Y-m-d H:i:s")."";

                $shopmessage->SendMessage($content,$supply_fromuser_arr['openid'],$customer_id,1,$batchcode,2,$supply_id);
			}else{
				$supply_fromuser_arr = $shopmessage->query_openid($customer_id,$supply_id);
				$shopmessage->SendMessage("",$supply_fromuser_arr['openid'],$customer_id,1,$batchcode,2,$supply_id);
			}
			//提醒供应商发货 END

			$shopmessage->GetMoney_FirstExtend($customer_id,$batchcode);	//首次推广奖励

			_file_put_contents("log/order_pay_" . $today . ".txt", "reuslt_getmoney_common=======".var_export($reuslt_getmoney_common,true)."\r\n",FILE_APPEND);

			//代言人团队消费增票
			$reuslt_check_daiyanren = $shopmessage->check_daiyanren($user_id,$customer_id,$totalprice,$batchcode);
			_file_put_contents("log/order_pay_" . $today . ".txt", "reuslt_check_daiyanren=======".var_export($reuslt_check_daiyanren,true)."\r\n",FILE_APPEND);

			$json["status"] = 0;
			$json["supply"] = $supply_id;
			$json["msg"] = "编号：".$batchcode."，确认支付成功";
		}else{
			$json["status"] = 0;
			$json["msg"] = "编号：".$batchcode."，已确认支付，请勿重复";
		}

	break;

    case "changeFreightPrice": //改运费
        $from_page =$configutil->splash_new($_POST["from_page"]);	//页面来源 ； 0 ：商城 ； 1：订货系统 ； 2 ： f2c
        $batchcode =$configutil->splash_new($_POST["batchcode"]);
        $changePrice =$configutil->splash_new($_POST["changePrice"]);
        $now       = date('Y-m-d H:i:s',time());
        if($from_page != 0){
            $json["status"] = 20001;
            $json["line"] = 1111;
            $json["msg"] = "编号：".$batchcode."，无法更改运费价格！";
        }else{
            //查询f2c_order_freight表 是否有改过运费
            $query = "SELECT * from f2c_order_freight where customer_id = {$customer_id} and batchcode = '{$batchcode}' and isvalid = true";
            $result = _mysql_query($query) or die('query_supply failed: ' . mysql_error());
            $row= mysql_fetch_assoc($result);
            if($row){
                $query_freight = "update f2c_order_freight set price = '{$changePrice}' , remark = '订单{$batchcode}的运费修改为{$changePrice}' where customer_id = {$customer_id} and batchcode = '{$batchcode}' and isvalid = true";
            }else{
                $query_freight = "insert into f2c_order_freight (customer_id,batchcode,isvalid,price,createtime,remark) values('{$customer_id}','{$batchcode}',true,'{$changePrice}','{$now}','订单{$batchcode}的运费修改为{$changePrice}' )";
            }
            $result_freight = _mysql_query($query_freight) or die('query_supply failed: ' . mysql_error());
            $row_freight = mysql_fetch_assoc($result_freight);
            if($row_freight !== false){
//                $send_order_query = "SELECT current_proxy_id,final_proxy_id FROM system_send_order where customer_id = {$customer_id} and batchcode = {$batchcode} and isvalid = true and send_type = 2";
                //获取拒单对象信息
                $user_query = "select us.name, ac.phone from f2c_accounts as ac left join weixin_users as us on us.id=ac.user_id left join system_send_order as sso on sso.current_proxy_id = ac.id where sso.order_id = '{$batchcode}' and sso.customer_id = {$customer_id} and sso.send_type = 2 and sso.isvalid = true";
                $result_user = _mysql_query($user_query) or die('query_supply failed: ' . mysql_error());
                $arr = array();
                $row_user = mysql_fetch_assoc($result_user);
                //获取拒单对象仓库信息
                $f2c_query = "select a.grade,a.areaname,a.all_areaname from f2c_area_set s inner join weixin_commonshop_team_area a on s.area_id = a.id left join system_send_order as sso on sso.store_id = s.id where sso.order_id = '{$batchcode}' and sso.customer_id = {$customer_id} and sso.send_type = 2 and sso.isvalid = true ";
                $result_f2c = _mysql_query($f2c_query) or die('query_supply failed: ' . mysql_error());
                $arr2 = array();
                $row_f2c = mysql_fetch_assoc($result_f2c);
                //添加订单日志
                $query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
				values('{$batchcode}',31,'".$row_f2c['areaname']."F2C,".$row_user['name']."拒单,由平台发货','{$log_username}','{$now}',1)";

                $result_log = _mysql_query($query_log) or die('query_supply failed: ' . mysql_error());
                $log = mysql_fetch_assoc($result_log);

                if($log !== false){
                    $json["status"] = 0;
                    $json["line"]   = 1111;
                    $json["msg"]    = "订单编号：".$batchcode."，成功修改运费价格！";
                }else{
                    $json["status"] = 20002;
                    $json["line"]   = 1111;
                    $json["msg"]    = "编号：".$batchcode."，更改运费价格失败！";
                }
            }else{
                //添加失败
                $json["status"] = 20003;
                $json["line"]   = 1111;
                $json["msg"]    = "编号：".$batchcode."，更改运费价格失败！";
            }
        }
        break;

    case 'change_courier_number':
        $batchcode = $configutil->splash_new($_POST["batchcode"]);
        $logistics_company = $configutil->splash_new($_POST["logistics_company"]);
        $courier_number = $configutil->splash_new($_POST["courier_number"]);
        $now       = date('Y-m-d H:i:s',time());
        $query1 = "SELECT expressname,expressnum FROM weixin_commonshop_orders where batchcode='{$batcode}'";
        $result1 = _mysql_query($query1) or die('Query_num failed: ' . mysql_error());
        while($row = mysql_fetch_object($result1)){
            $expressname = $row -> expressname;
            $expressnum  = $row -> expressnum;
        }
        if($expressname == $logistics_company && $courier_number == $expressnum){
            $json['status'] = 0;
            $json['line']   = 1113;
            $json['msg']    = "订单编号：".$batchcode."，成功修改快递公司与快递单号成功！";
            $json['time']   = $now;
            $json['customer'] = $log_username;
        }else{
            //修改快递公司，快递单号
            $query = "update weixin_commonshop_orders set expressname = '{$logistics_company}',expressnum = '{$courier_number}' where batchcode = '{$batchcode}'";
            $result = _mysql_query($query) or die('Query_num failed: ' . mysql_error());
            if($result){
                $json['status'] = 0;
                $json['line']   = 1113;
                $json['msg']    = "订单编号：".$batchcode."，修改快递公司与快递单号成功！";
                $json['time']   = $now;
                $json['customer'] = $log_username;
            }else{
                $json['status'] = 20004;
                $json['line']   = 1113;
                $json['msg']    = "订单编号：".$batchcode."，修改快递公司与快递单号失败！";
            }
        }
        if($json['status'] == 0 ){
            //添加订单操作日志
            $query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
				values('{$batchcode}',34,'修改快递公司:{$logistics_company}，修改快递单号:{$courier_number}','{$log_username}','{$now}',1)";

            $result_log = _mysql_query($query_log) or die('query_supply failed: ' . mysql_error());
            $log = mysql_fetch_assoc($result_log);
        }
        break;

	case "changeAdd":  //改地址
		$addressA =$configutil->splash_new($_POST["addressA"]);
		$addressC =$configutil->splash_new($_POST["addressC"]);
		$addressP =$configutil->splash_new($_POST["addressP"]);
		$addressAdd =$configutil->splash_new($_POST["addressAdd"]);
		$addressName =$configutil->splash_new($_POST["addressName"]);
		$addressPhone =$configutil->splash_new($_POST["addressPhone"]);
		$from_page =$configutil->splash_new($_POST["from_page"]);	//页面来源 ； 0 ：商城 ； 1：订货系统 ； 2 ： f2c

		$query_num = "select count(1) from weixin_commonshop_order_addresses where batchcode='".$batchcode."'";
		//_file_put_contents("log/order_Add_" . $today . ".txt", "query_num=======".var_export($query_num,true)."\r\n",FILE_APPEND);
		$result_num = _mysql_query($query_num) or die('Query_num failed: ' . mysql_error());
		$add_num = mysql_result($result_num,0,0);
		if($add_num==0){
			$query_address = "insert into weixin_commonshop_order_addresses(batchcode,name,phone,address,location_p,location_c,location_a)values ('".$batchcode."','".$addressName."','".$addressPhone."','".$addressAdd."','".$addressP."','".$addressC."','".$addressA."')";
		}else{
			$query_address = "update weixin_commonshop_order_addresses set name='".$addressName."',phone='".$addressPhone."',address='".$addressAdd."',location_p='".$addressP."',location_c='".$addressC."',location_a='".$addressA."' where batchcode='".$batchcode."'";
		}
		//_file_put_contents("log/order_Add_" . $today . ".txt", "query_address=======".var_export($query_address,true)."\r\n",FILE_APPEND);
		_mysql_query($query_address);


		if($from_page == 2){
			//F2C订单
			$current_proxy_id = -1;	//当前派单用户id
			$query  = "SELECT current_proxy_id FROM system_send_order WHERE send_type=2 AND isvalid=true AND order_id='".$batchcode."' ORDER BY id DESC LIMIT 1";
			$result = _mysql_query($query) or die("L911 Query failed: ".mysql_error());
			while($row = mysql_fetch_object($result)){
				$current_proxy_id = $row -> current_proxy_id;
			}

			if($current_proxy_id > 0){
				$query = "SELECT wu.weixin_fromuser FROM weixin_users AS wu INNER JOIN f2c_accounts AS a ON a.user_id=wu.id WHERE wu.isvalid=true and a.isvalid=true and a.id=".$current_proxy_id;
				$result = _mysql_query($query) or die("L919 Query failed: ".mysql_error());
				while($row = mysql_fetch_object($result)){
					$weixin_fromuser = $row -> weixin_fromuser;
				}

				$msg_content = "【F2C系统】亲，您的代发订单号【".$batchcode."】收货地址修改为：\r\n".
										$addressP.$addressC.$addressA.$addressAdd."\r\n".
										"收件人：".$addressName."\n".
										"电话：".$addressPhone;

				$shopmessage->SendMessage($msg_content,$weixin_fromuser,$customer_id);	//发送信息
			}
		}


		$json["status"] = 0;
		$json["line"] = 594;
		$json["msg"] = "编号：".$batchcode."，地址更改成功";
	break;

	case "delayDate":  //延期收货
		$delayDate = $configutil->splash_new($_POST["Date"]);
		$is_delay = $configutil->splash_new($_POST["is_delay"]);
		if(empty($delayDate) || $delayDate <= 0){
			$delayDate = 3; //默认延后3天
		}

		if(!empty($batchcode) && !empty($delayDate)){
			if($is_delay == 1){
				$query_delay = "update weixin_commonshop_orders set auto_receivetime = DATE_ADD(auto_receivetime, INTERVAL ".$delayDate." DAY ),is_delay = 2 where isvalid = true and sendstatus = 1 and batchcode = '".$batchcode."'";
			}else{
				$query_delay = "update weixin_commonshop_orders set auto_receivetime = DATE_ADD(auto_receivetime, INTERVAL ".$delayDate." DAY ) where isvalid = true and sendstatus = 1 and batchcode = '".$batchcode."'";
			}
			//_file_put_contents("log/order_delay_" . $today . ".txt", "query_delay=======".var_export($query_delay,true)."\r\n",FILE_APPEND);
			_mysql_query($query_delay); //更改自动收货时间

			$query_time = "select auto_receivetime from weixin_commonshop_orders where isvalid = true and sendstatus = 1 and batchcode = '".$batchcode."'";
			//_file_put_contents("log/order_delay_" . $today . ".txt", "query_time=======".var_export($query_time,true)."\r\n",FILE_APPEND);
			$result_time = _mysql_query($query_time);
			$receivetime = mysql_result($result_time,0,0);   //新自动收货时间

			$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
				values('".$batchcode."',6,'平台更新订单的自动收货日期为".$receivetime."','".$log_username."',now(),1)";
			//_file_put_contents("log/order_delay_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
			_mysql_query($query_log);

			if($is_delay == 1){
				$query_user_id = "select weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
				//_file_put_contents("log/order_delay_" . $today . ".txt", "query_user_id=======".var_export($query_user_id,true)."\r\n",FILE_APPEND);
				$result_user_id = _mysql_query($query_user_id);
				$fromuser = mysql_result($result_user_id,0,0);
				$content = "编号：".$batchcode.",商家已处理了您的延迟收货申请，当前自动收货时间为".$receivetime;
				$shopmessage->SendMessage($content,$fromuser,$customer_id);
				//_file_put_contents("log/order_delay_" . $today . ".txt", "SendMessage=======".var_export($content,true)."\r\n",FILE_APPEND);
			}
		}

		$json["status"] = 0;
		$json["line"] = 632;
		$json["time"] = $receivetime;
		$json["msg"] = "编号：".$batchcode."，延期成功";
	break;

	case "confirm":  //确认订单
		/*事务开始*/
		_tran_start();

		$shopmessage->write_file_log(date('Y_m_d H:i:s',time()).'确认订单!订单号:'.$batchcode);
		$is_receipt = 0;
		if(!empty($_POST["is_receipt"])){
			$is_receipt = $configutil->splash_new($_POST["is_receipt"]);//是否收货自动结算
		}
		$totalprice = $configutil->splash_new($_POST["totalprice"]);
		/* 订单属性 */
		$agentcont_type = 0;

		$sendstatus     = 0;
		$order_status   = 0;
		$paystyle       = "";
		$user_id        = -1;
		$card_member_id = -1;

		$exp_user_id    = -1;
		$paytime        = "";
        $is_sendorder = false;
        $pay_batchcode = "";
        $orderTime = "";
        $customer_id = "";
		$query_status   = "select sendstatus,status,card_member_id,paystyle,user_id,paytime,exp_user_id,is_sendorder,is_pay_on_delivery,is_sign,pay_batchcode,createtime,customer_id from weixin_commonshop_orders where batchcode='".$batchcode."' limit 1";
		//_file_put_contents("log/order_confirm_" . $today . ".txt", "\r\nquery_status=======".var_export($query_status,true)."\r\n",FILE_APPEND);
		$result_status = _mysql_query($query_status) or die('Query_status failed: ' . mysql_error());
		while ($row_status = mysql_fetch_object($result_status)) {

			$sendstatus     = $row_status->sendstatus;  //0:未发货；1：已发货;2:已收货;3.申请退货；4.已退货;5申请退款；6：已经退款
			$order_status   = $row_status->status;		//1:确认完成
			$card_member_id = $row_status->card_member_id;		//会员卡号

			$paystyle       = $row_status->paystyle;		//支付方式,中文
			$user_id        = $row_status->user_id;		//用户编号
			$paytime        = $row_status->paytime;
			$exp_user_id    = $row_status->exp_user_id;
            $is_sendorder   = $row_status -> is_sendorder;
            $is_payondelivery = $row_status->is_pay_on_delivery;
            $is_sign        = $row_status->is_sign;
            $pay_batchcode = $row_status->pay_batchcode;
            $orderTime = $row_status->createtime;
            $customer_id = $row_status->customer_id;
            break;
		}
		/* 订单属性 End */
		if($sendstatus != 2 and $sendstatus != 4 and $sendstatus != 6){
			$json["status"] = 20001;
			$json["line"] = 673;
			$json["msg"] = "编号：".$batchcode."，无法确认订单，请检查订单状态！";
		}elseif($order_status == 1){
			$json["status"] = 20002;
			$json["line"] = 677;
			$json["msg"] = "编号：".$batchcode."，已确认完成，请勿重复提交！";
		}else{

			/* 商城设置 */
			$isOpenPublicWelfare	=  0;
			$is_cashback			=  0;
			$is_shareholder			=  0;
			$is_team				=  0;
			$query_set ="select isOpenPublicWelfare,is_cashback,is_shareholder,is_team from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
			//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_set=======".var_export($query_set,true)."\r\n",FILE_APPEND);
			$result_set = _mysql_query($query_set) or die('Query_set failed: ' . mysql_error());
			while ($row_set = mysql_fetch_object($result_set)) {
				$isOpenPublicWelfare = $row_set->isOpenPublicWelfare;
				$is_cashback         = $row_set->is_cashback;
				$is_shareholder      = $row_set->is_shareholder;
				$is_team             = $row_set->is_team;
			}
			/* 商城设置 End */

			$ordersta = 1;
			if($is_payondelivery == 1 && $is_sign == 0)
			{
				$ordersta = 0;
			}
			
			//健康钱包确认支付后收款方才能收到钱，在订单确认完成增加发起确认支付 20170809
            if($paystyle == "健康钱包支付" && $sendstatus != 4 && $sendstatus != 6){ //不是已退款或已退货
                //获取参数
                $orderTime = datetimeToNewFormat($orderTime);
                $api_obj = new HealthpayApi($customer_id);
                $sourceId = $api_obj->sourceId;//sourceId收款平台账号
                $md5Key = $api_obj->md5Key; //收款平台密钥
                $parameter = array(
					"orderId"     => $pay_batchcode,
					"orderTime"   => $orderTime,
				    "sourceId"    => $sourceId
				);
                $healthPaySubmit = new HealthPaySubmit();
                $sign = $healthPaySubmit->getSign($parameter, $md5Key); //获取签名
                $parameter["sign"] = $sign;

                //发起确认支付
                $res = $api_obj->do_confirm_pay($parameter);
                //var_dump($res);

                //根据结果进行处理 {"retCode":"0000","retMessage":"支付订单确认成功"}
                if($res["retCode"] == "0000"){ //支付订单确认成功,使用原来的业务
                    $shopmessage->write_file_log('1、更改订单状态',2);
                    /* 更改订单状态 */
                    $query_up_status="update weixin_commonshop_orders set status=$ordersta,is_receipt=".$is_receipt.", confirm_order_time=now() where batchcode='".$batchcode."'";

                    //_file_put_contents("log/order_confirm_" . $today . ".txt", "query_up_status=======".var_export($query_up_status,true)."\r\n",FILE_APPEND);
                    _mysql_query($query_up_status);

                    //货到付款前端确认收货状态不能修改

                    $sql = "update weixin_commonshop_order_prices set status=1 where isvalid=true and batchcode='" . $batchcode . "'";
                    _mysql_query($sql) or die('9_1 Query failed: ' . mysql_error());

                    /* 更改订单状态 End */
                } else{ //支付订单确认失败
                    $json["status"] = 30002;
                    $json["line"] = $res["retCode"];
                    $json["msg"] = "订单号：".$batchcode."确认支付失败！";
                    $jsons=json_encode($json);
                    die($jsons);
                }
            } else{ //使用原来的业务
                $shopmessage->write_file_log('1、更改订单状态',2);
                /* 更改订单状态 */
                $query_up_status="update weixin_commonshop_orders set status=$ordersta,is_receipt=".$is_receipt.", confirm_order_time=now() where batchcode='".$batchcode."'";

                //_file_put_contents("log/order_confirm_" . $today . ".txt", "query_up_status=======".var_export($query_up_status,true)."\r\n",FILE_APPEND);
                _mysql_query($query_up_status);

                //货到付款前端确认收货状态不能修改

                $sql = "update weixin_commonshop_order_prices set status=1 where isvalid=true and batchcode='" . $batchcode . "'";
                _mysql_query($sql) or die('9_1 Query failed: ' . mysql_error());

                /* 更改订单状态 End */
            }

			if($sendstatus != 4 and $sendstatus != 6){

				//旧股东分红、区域团队版本结算方法调用
				if(false){
					if(strtotime($paytime) < strtotime(shareholder_team_bug_time)){
						if($is_shareholder==1){
							//_file_put_contents("log/order_confirm_" . $today . ".txt", "Confirm_GetMoney_shareholder_old=======".var_export($exp_user_id,true)."\r\n",FILE_APPEND);
							$shopmessage->Confirm_GetMoney_shareholder_old($batchcode,$customer_id,$exp_user_id);
						}
						if($is_team==1){
							//_file_put_contents("log/order_confirm_" . $today . ".txt", "Confirm_GetMoney_team_old=======".var_export($exp_user_id,true)."\r\n",FILE_APPEND);
							$shopmessage->Confirm_GetMoney_team_old($batchcode,$customer_id,$exp_user_id);
						}
					}
				}

				/* 修复旧版本已支付未完成订单，没有正常返现问题  2016.7.27 */
				/* $cashback_id = -1;
				$sql_cashback_t = "select id from cashback_t where customer_id=".$customer_id." and batchcode='".$batchcode."'";
				$res_cashback_t = _mysql_query($sql_cashback_t) or die('sql_cashback_t failed:'.mysql_error());
				while($row_cashback_t = mysql_fetch_object($res_cashback_t)){
					$cashback_id      = $row_cashback_t->id;
				}
				if($is_cashback==1 && $cashback_id<0){   //开了返现但没有返现记录的，先重新执行新的返现方法
					$res_id = $shopmessage->cashBack($customer_id,$user_id,$batchcode);
					_file_put_contents("log/order_cashback_repair" . $today . ".txt", "order_cashback_repair=======".var_export($res_id,true)."\r\n",FILE_APPEND);
				} */
				/* 修复旧版本已支付未完成订单，没有正常返现问题  2016.7.27 */

				$shopmessage->write_file_log("20、货到付款?{$is_payondelivery}==签收?{$is_sign}",2);
				if($is_payondelivery != 1 || $is_sign == 1)
				{	//echo 123456789;
					//推广员有效期续费
					$shopmessage->write_file_log('10、推广员有效期续费',2);
					$Promoter->settlementRenewOrderPromoter($user_id,$batchcode);

					$shopmessage->write_file_log('2、确认消费返现',2);
					/*** 确认消费返现 start***/
					$shopmessage->confirm_cashBack($customer_id,$batchcode);
					/*** 确认消费返现 end***/

					$shopmessage->write_file_log('3、分佣',2);
					$shopmessage->Confirm_GetMoney_Agent($batchcode,$card_member_id,$totalprice,$customer_id,$paystyle);
					// // 区块链奖金池start
					// $order_chain = $shopmessage->Block_chain_goods($customer_id,$user_id,$batchcode,1); //调用区块链奖励
					// // 区块链奖金池end
					//增加团队订单数
					$shopmessage->write_file_log('4、增加团队订单数',2);
					$shopmessage->Confirm_Team_order($batchcode);

					//慈善金额确认
					$shopmessage->write_file_log('5、慈善金额确认',2);
					$shopmessage->Confirm_charitable($batchcode,$customer_id,$user_id);

					//全球分红分佣进入资金池
					$shopmessage->write_file_log('6、全球分红分佣进入资金池',2);
					$shopmessage->Confirm_Global($customer_id,$batchcode);

					$model_integral = new model_integral();
					//积分确认
					$shopmessage->write_file_log('7、积分确认',2);
					$model_integral->m_confirm_Integral(array('cust_id'=>$customer_id,'batchcode'=>$batchcode));

                    $shopmessage->write_file_log('7.1、积分确认',2);
				}
				// $global_reward=0;
				// $id =-1;
				// $global_sql = "SELECT reward FROM weixin_commonshop_order_promoters where customer_id=".$customer_id." AND batchcode='".$batchcode."' AND type=9";
				// $global_res = _mysql_query($global_sql) or die('orderclass-938 Query failed: ' . mysql_error());
				// while( $row = mysql_fetch_object($global_res) ){
				// 	$global_reward = $row->reward;//查出多少钱要进入资金池
				// 	if( $global_reward > 0 ){
				// 		$sel_sql = "SELECT id FROM weixin_globalbonus_pool WHERE customer_id=".$customer_id;
				// 		$sql_res = _mysql_query($sel_sql);
				// 		while($info = mysql_fetch_object($sql_res)){
				// 			$id = $info->id;
				// 			if($id == -1){
				// 				$ins_sql = "INSERT INTO weixin_globalbonus_pool(customer_id,isvalid,total_money) VALUES(".$customer_id.",true,".$global_reward.")";
				// 				_mysql_query($ins_sql) or die('orderclass-960 Query failed: ' . mysql_error());
				// 				//添加备注
				// 				$remark = "订单号：".$batchcode."佣金：".$global_reward."元 进入资金池";
				// 				$ins_log_sql = "INSERT INTO weixin_globalbonus_pool_log(customer_id,isvalid,batchcode,type,style,money,after_money,createtime,remark) VALUES(".$customer_id.",true,".$batchcode.",1,2,".$global_reward.",".$global_reward.",now(),'".$remark."')";
				// 				_mysql_query($ins_log_sql)or die('orderclass-954 Query failed: ' . mysql_error());
				// 			}else{
				// 				$sel_sql = "SELECT total_money FROM weixin_globalbonus_pool where customer_id=".$customer_id;
				// 				$res_sql = _mysql_query($sel_sql)or die('Query failed2: ' . mysql_error());
				// 				while( $row = mysql_fetch_object($res_sql) ){
				// 					$total_money = $row->total_money;//资金池当前金额
				// 				}
				// 				$after_money = $total_money+$global_reward;
				// 				//添加备注
				// 				$remark = "订单号：".$batchcode."佣金：".$global_reward."元 进入资金池";
				// 				$ins_log_sql = "INSERT INTO weixin_globalbonus_pool_log(customer_id,isvalid,batchcode,type,style,money,after_money,createtime,remark) VALUES(".$customer_id.",true,".$batchcode.",1,2,".$global_reward.",".$after_money.",now(),'".$remark."')";
				// 				_mysql_query($ins_log_sql)or die('orderclass-954 Query failed: ' . mysql_error());
				// 				//将金额加入到资金池中
				// 				$up_pool_sql = "UPDATE weixin_globalbonus_pool SET total_money=total_money+".$global_reward." WHERE customer_id=".$customer_id;
				// 				_mysql_query($up_pool_sql)or die('orderclass-957 Query failed: ' . mysql_error());
				// 			}
				// 		}
				// 	}
				// }

				/* 更改推广员总vp值 start */
					// 查询商城VP设置
                $shopmessage->write_file_log('更改推广员总vp值',2);
					$isvp_switch = 0; //vp开关 1:开 0:关
					$query_s="select isvp_switch from weixin_commonshop_vp_bases where isvalid=true and customer_id=".$customer_id." limit 0,1";
					$result_s = _mysql_query($query_s) or die('W758 Query failed: ' . mysql_error());
					while ($row_s = mysql_fetch_object($result_s)) {
						$isvp_switch = $row_s->isvp_switch;
					}
					if( $isvp_switch == 1 ){
						// 查询此订单总vp值
						$total_vpscore=0;
						$query_reward="select total_vpscore from weixin_commonshop_order_prices where isvalid=true and batchcode='".$batchcode."' limit 0,1";
						$result_reward = _mysql_query($query_reward) or die('W758 Query failed: ' . mysql_error());
						while ($row_r = mysql_fetch_object($result_reward)) {
							$total_vpscore = $row_r->total_vpscore;
						}
						if( 0 < $total_vpscore ){
							$shopmessage->write_file_log('7、更改推广员总vp值',2);
							/*vp值日志开始 更改日志状态已打入个人vp总账户*/
							$shopmessage->Confirm_vp($batchcode,$customer_id,$user_id,$total_vpscore);
							/* 更新个人VP总值 end */

						}
					}
				/* 更改推广员总vp值  end */
                $shopmessage->write_file_log('公益基金',2);
				/* 公益基金 */
				if($isOpenPublicWelfare==1){
					$valuepercent = 0;
					$publicwelfare=0;
					$query_pub = "select valuepercent,publicwelfare from weixin_commonshop_publicwelfare where isvalid=true and customer_id=".$customer_id." limit 1";
					//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_pub=======".var_export($query_pub,true)."\r\n",FILE_APPEND);
					$result_pub = _mysql_query($query_pub);
					while ($row_pub = mysql_fetch_object($result_pub)) {
						$valuepercent = $row_pub->valuepercent;    //比率
						$publicwelfare=$row_pub->publicwelfare;    //奖金池累计金额
					}


					/* 运费 */
					$express_price = 0;
					$query_express = "select price from weixin_commonshop_order_express_prices where isvalid=true and batchcode='".$batchcode."' limit 1";
					//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_express=======".var_export($query_express,true)."\r\n",FILE_APPEND);
					$result_express = _mysql_query($query_express);
					while ($row_express = mysql_fetch_object($result_express)) {
						$express_price = $row_express->price;
					}
					/* 运费 End */

					if($express_price>0){$totalprice=$totalprice-$express_price;}  //减去运费
					$welfare=round($totalprice*$valuepercent,2);


					$welfare_id = -1;
					$query_welfare="select id,before_score,add_score from weixin_commonshop_publicwelfare_log where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." order by id desc limit 0,1";
					//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_welfare=======".var_export($query_welfare,true)."\r\n",FILE_APPEND);
					$result_welfare = _mysql_query($query_welfare);
					while ($row_welfare = mysql_fetch_object($result_welfare)) {
						$welfare_id=$row_welfare->id;
						$before_score=$row_welfare->before_score;
						$add_score=$row_welfare->add_score;
					}

					$shopmessage->write_file_log('8、更新公益基金',2);
					//判断此用户是否曾经捐助过
					if($welfare_id>0){
						$new_before_score=$before_score+$add_score;
						$shopmessage->write_file_log('A、添加公益基金记录',3);
						$query_insert_welfare="insert into weixin_commonshop_publicwelfare_log(user_id,createtime,isvalid,customer_id,before_score,add_score,batchcode) values(".$user_id.",now(),true,".$customer_id.",".$new_before_score.",".$welfare.",".$batchcode.")";
					}else{
						$shopmessage->write_file_log('A、添加公益基金记录',3);
						$query_insert_welfare="insert into weixin_commonshop_publicwelfare_log(user_id,createtime,isvalid,customer_id,before_score,add_score,batchcode) values(".$user_id.",now(),true,".$customer_id.",0,".$welfare.",".$batchcode.")";
					}

					_mysql_query($query_insert_welfare);
					//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_insert_welfare=======".var_export($query_insert_welfare,true)."\r\n",FILE_APPEND);


					//累加至奖金池
					$shopmessage->write_file_log('B、累加至奖金池',3);
					$new_publicwelfare=round($publicwelfare+$welfare,2);
					$query_up_public = "update weixin_commonshop_publicwelfare set publicwelfare=".$new_publicwelfare." where customer_id=".$customer_id;
					//_file_put_contents("log/order_confirm_" . $today . ".txt", "new_publicwelfare=======".var_export($new_publicwelfare,true)."\r\n",FILE_APPEND);
					_mysql_query($query_up_public);
				}



				//消费返现开关
				/*if($is_cashback==1){
					$sum_cashback=0;
					$query_cash_order="select pid,rcount,totalprice from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and batchcode=".$batchcode;
					//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_cash_order=======".var_export($query_cash_order,true)."\r\n",FILE_APPEND);
					$result_cash_order = _mysql_query($query_cash_order);
					while ($row_cash_order = mysql_fetch_object($result_cash_order)) {
						$pid=$row_cash_order->pid;
						$rcount=$row_cash_order->rcount;
						$p_totalprice=$row_cash_order->totalprice;*/

						/* 查询返现 */
						/*$query="select cb_condition,cashback,cashback_r from weixin_commonshop_cashback where isvalid=true and customer_id=".$customer_id." limit 0,1";
						$result=_mysql_query($query);
						while($row = mysql_fetch_object($result)){
							$cb_condition = $row->cb_condition;
							$cashback = $row->cashback;
							$cashback_r = $row->cashback_r;
						}

						$query_cashback="select cashback,cashback_r from weixin_commonshop_products where isvalid=true and id=".$pid;
						//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_cashback=======".var_export($query_cashback,true)."\r\n",FILE_APPEND);
						$result_cashback = _mysql_query($query_cashback);
						while ($row_cashback = mysql_fetch_object($result_cashback)) {
							$p_cashback=$row_cashback->cashback;
							$p_cashback_r=$row_cashback->cashback_r;
						}
						if($p_cashback!=''){
							$cashback = $p_cashback;   //如果产品编辑页设置了返现金额，则以产品设置为准
						}
						if($p_cashback_r!=''){
							$cashback_r = $p_cashback_r;  //如果产品编辑页设置了返现金额，则以产品设置为准
						}

						if($cb_condition==0){
							$sum = $cashback*$rcount;
							$sum_cashback += $sum;
						}else{

							$sum = $p_totalprice*$cashback_r;
							$sum_cashback += $sum;
						}
					}


					if($sum_cashback>0){
						/* 插入返现记录 */
						/*$query_cash_insert="insert into cashback(customer_id,user_id,isvalid,createtime,batchcode,cashback,rest_cashback) values(".$customer_id.",".$user_id.",true,now(),".$batchcode.",".$sum_cashback.",".$sum_cashback.")";
						//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_cash_insert=======".var_export($query_cash_insert,true)."\r\n",FILE_APPEND);
						_mysql_query($query_cash_insert);
					}
				}*/
			}else{
				$extend_id 	  = -1;	//
				$extend_money = 0;	//推广奖励金额
				$query_money = "select id,money from weixin_commonshop_extend_logs where batchcode='".$batchcode."' and isvalid=true";
				$result_money = _mysql_query($query_money) or die('Query_money failed:'.mysql_error());
				while( $row_money = mysql_fetch_object($result_money) ){
					$extend_id 	  = $row_money->id;
					$extend_money = $row_money->money;
				}
				if( $extend_id > 0 ){	//有推广奖励记录
					//退货订单则删除推广奖励记录
					$shopmessage->write_file_log('9、推广奖励',2);
					$query_extend = "update weixin_commonshop_extend_logs set isvalid=false where batchcode='".$batchcode."' and isvalid=true";
					_mysql_query($query_extend) or die('Query_extend failed:'.mysql_error());

					$weixin_name 	 = '佚名';	//微信名
					$weixin_fromuser = '';		//微信唯一标识符
					$query_exp_user_id = "select weixin_name,weixin_fromuser from weixin_users where id=".$exp_user_id." and isvalid=true";
					$result_exp_user_id = _mysql_query($query_exp_user_id) or die('Query_exp_user_id failed:'.mysql_error());
					while( $row_exp_user_id = mysql_fetch_object($result_exp_user_id) ){
						$weixin_name 	 = mysql_real_escape_string($row_exp_user_id->weixin_name);
						$weixin_fromuser = $row_exp_user_id->weixin_fromuser;
					}

					$msg_extend = "亲，您的佣金 -".$extend_money."\r\n".
									"来源：【订单退货】\n".
									"顾客：".$weixin_name."\n".
									"备注：【推广奖励】\n".
									"时间：".date( "Y-m-d H:i:s")."";

					$shopmessage->SendMessage($msg_extend,$weixin_fromuser,$customer_id);	//发送信息
				}

				//撤销推广员有效期续费订单
				$query_renewal_order = "UPDATE promoter_renewal_orders SET status=2 WHERE batchcode='".$batchcode."' AND customer_id=".$customer_id." AND status=0";
				_mysql_query($query_renewal_order) or die('Query_renewal_order failed:'.mysql_error());
			}


			//给顾客增加消费积分奖励.1 表示为商城消费
			/*if( $sendstatus == 2 ){
				$shopmessage->AddScore_level($card_member_id,$totalprice,1,$paystyle,$batchcode);
			}	 */

			//添加订单操作日志
			$shopmessage->write_file_log('10、添加订单操作日志',2);
			$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
				values('".$batchcode."',16,'平台已确认订单完成','".$log_username."',now(),1)";
			//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
			_mysql_query($query_log);

			$json["status"] = 0;
			$json["line"] = 820;
			$json["msg"] = "编号：".$batchcode."，确认完成";
		}

        /*事务提交*/
        _tran_commit();



        /* 订货系统结算方法 */
		$shopmessage->write_file_log('is_sendorder====='.$is_sendorder,2);
        if($is_sendorder == true){
            $sql_query = "select id , send_type from system_send_order where order_id = '".$batchcode."' and isvalid = true and is_accept = true  limit 1";
            $sso_id = 0;
            $send_type = 0;
            $sso_res = _mysql_query($sql_query);
            if($row_sso = mysql_fetch_object($sso_res)){
                $id = $row_sso -> id;
                $send_type = $row_sso -> send_type;
            }
			$shopmessage->write_file_log('send_type====='.$send_type,2);
            if($send_type == 1){ // 订货系统
                $shopmessage->write_file_log('11、分仓派单结算',2);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, Protocol . "" . $_SERVER['HTTP_HOST'] . "/addons/index.php/ordering_retail/Ordering_Service/settle_shop_order?customer_id=" . $customer_id . "&user_id=" . $user_id . "&batchcode=" . $batchcode);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                if (Protocol == "https://") {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                }
                $output = curl_exec($ch);
                file_put_contents($zlog_name,"$batchcode 结果：".$output,FILE_APPEND);
                $or_result = json_decode($output);
                if($or_result -> status != 1){
                    $shopmessage->write_file_log('11、分仓派单结算 - 异常 ： '.var_export($output,true),2);
                }
                curl_close($ch);

            }else if($send_type == 2){ // f2c

                $shopmessage->write_file_log('12、F2C派单结算',2);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, Protocol . "" . $_SERVER['HTTP_HOST'] . "/addons/index.php/f2c/Ordering_Service/settle_shop_order?customer_id=" . $customer_id . "&user_id=" . $user_id . "&batchcode=" . $batchcode);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                if (Protocol == "https://") {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                }
                $output = curl_exec($ch);
                $or_result = json_decode($output);
				$shopmessage->write_file_log('or_result====='.var_export($or_result,true),2);
                if($or_result -> status != 1){
                    $shopmessage->write_file_log('11、分仓派单结算 - 异常 ： '.var_export($output,true),2);
                }
                curl_close($ch);

            }
            /* */
        }

	break;

	case "confirmReturnGood":  //退货申请管理

		(int)$status = $configutil->splash_new($_POST["status"]);
		$reason = $configutil->splash_new($_POST["reason"]);
		if(!empty($batchcode) && !empty($status)){

			//查询退货类型
			$return_type = 0;
			$sendstatus = 0;
			$pid = -1;
			$pro_name = "";
			$send_express_id = 0;
			$yundian_self       = 0;//是否云店自营订单
			$query_orders = "select return_type,sendstatus,pid,send_express_id,yundian_self from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
			//_file_put_contents("log/order_confirmReturnGood_" . $today . ".txt", "\r\nquery_orders=======".var_export($query_orders,true)."\r\n",FILE_APPEND);
			$result_orders = _mysql_query($query_orders) or die('Query_orders failed: ' . mysql_error());
			while ($row_orders = mysql_fetch_object($result_orders)) {
				$return_type = $row_orders->return_type;
				$sendstatus = $row_orders->sendstatus;
				$pid = $row_orders->pid;
				$send_express_id = $row_orders->send_express_id;
				$yundian_self = $row_orders->yundian_self;

				//查询产品名称
				$query_product = "select name from weixin_commonshop_products where id='".$pid."'";
				//_file_put_contents("log/order_confirmReturnGood_" . $today . ".txt", "query_product=======".var_export($query_product,true)."\r\n",FILE_APPEND);
				$result_product = _mysql_query($query_product) or die('Query_product failed: ' . mysql_error());
				while ($row_product = mysql_fetch_object($result_product)) {
					$product_name = $row_product->name;
					$pro_name_one = "".$product_name."";
				}
				$pro_name .= $pro_name_one;

			}



			if($sendstatus!=3 and $sendstatus!=5){
				$json["status"] = 30001;
				$json["line"] =$sendstatus;
				$json["msg"] = "编号：".$batchcode."，处于非售后状态！";
				$jsons=json_encode($json);
				die($jsons);
			}

			$status_str = $status == 1 ? "同意" : "驳回";

			$st = $status == 1 ? 2 : 3;
			//修改订单表状态
			if($status == 1){ //同意
				$query_up_status = "update weixin_commonshop_orders set return_status = ".$st." where isvalid = true and return_status = 0 and batchcode = '".$batchcode."'";
			}else{ //驳回 , 驳回退货申请后，将订单状态重新设置为已发货状态
				$query_up_status = "update weixin_commonshop_orders set return_status = ".$st." , sendstatus = 1 where isvalid = true and sendstatus = 3 and return_status = 0 and batchcode = '".$batchcode."'";
				//云店订单
                if(!empty($yundian_self)){		//云店自营订单退款时修改了aftersale_type和aftersale_state字段，驳回时修改回来
                
                	$query_up_status = "update weixin_commonshop_orders set return_status = ".$st." , sendstatus = 1 , aftersale_type = 0 , aftersale_state = 0 where isvalid = true and sendstatus = 3 and return_status = 0 and batchcode = '".$batchcode."'";
                } 
			}
			//_file_put_contents("log/order_confirmReturnGood_" . $today . ".txt", "\r\nquery_up_status=======".var_export($query_up_status,true)."\r\n",FILE_APPEND);
			// echo $query_up_status;die();
			_mysql_query($query_up_status);

			//添加订单日志
			if($yundian_self == 1) {
				$yundian_user_id_re = $_SESSION['user_id_'.$customer_id];

				$yundian_user_sql = "select weixin_fromuser from weixin_users where id='".$yundian_user_id_re."' ";
				$result_yundian_user = _mysql_query($yundian_user_sql)or die("Query_user error  : ".mysql_error());
				$yundian_fromuser = mysql_result($result_yundian_user,0,0);

				$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
				values('".$batchcode."',9,'云店店主".$status_str."用户的退换货申请".($status == 2 ? ",原因:".$reason.";订单更新为已发货状态。" : "")."','".$yundian_fromuser."',now(),1)";		
			} else {
				$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
				values('".$batchcode."',9,'平台".$status_str."用户的退换货申请".($status == 2 ? ",原因:".$reason.";订单更新为已发货状态。" : "")."','".$log_username."',now(),1)";				
			}

			//_file_put_contents("log/order_confirmReturnGood_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
			_mysql_query($query_log);

			//查询OpenID
			$query_user = "select weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
			//_file_put_contents("log/order_confirmReturnGood_" . $today . ".txt", "query_user=======".var_export($query_user,true)."\r\n",FILE_APPEND);
			$result_user = _mysql_query($query_user);
			$fromuser = mysql_result($result_user,0,0);


			$type_str = "";
			$sendstatus_str = "退货申请";
			switch($sendstatus){
				case 3:
					$sendstatus_str = "退货申请";

					switch($return_type){
						case 0:
							$type_str = "[仅退款]";
							break;
						case 1:
							$type_str = "[退货]";
							break;
						case 2:
							$type_str = "[换货]";
							break;
					}

					break;
				case 5:
					$sendstatus_str = "退款申请";
					break;
			}
			$sendstatus_str .= $type_str;
			$query_address="select location_p,location_c,location_a,address,zipcode,name,phone,tel,comment from weixin_commonshop_returnaddress where customer_id='".$customer_id."' and supplier_id=-1 and isvalid=true";
			$result_address = _mysql_query($query_address) or die('Query failed: ' . mysql_error());
			$location_p="";
			$location_c="";
			$location_a="";
			$address="";
			$zipcode="";
			$name="";
			$phone="";
			$tel="";
			$comment="";
			while ($row = mysql_fetch_object($result_address)) {
				$location_p = $row->location_p;
				$location_c	= $row->location_c;
				$location_a	= $row->location_a;
				$address	= $row->address;
				$zipcode	= $row->zipcode;
				$name	= $row->name;
				$phone	= $row->phone;
				$tel	= $row->tel;
				$comment	= $row->comment;
			}
			$content_address="退货地址:\n".$location_p."".$location_c."".$location_a."".$address."\n收件人:".$name;
			if(strlen($zipcode."")>0){
				$content_address=$content_address."\n邮编:".$zipcode;
			}
			if(strlen($phone."")>0){
				$content_address=$content_address."\n手机:".$phone;
			}
			if(strlen($tel."")>0){
				$content_address=$content_address."\n座机:".$tel;
			}
			if(strlen($comment."")>0){
				$content_address=$content_address."\n备注:".$comment;
			}
			$content = "亲，您有一笔订单\n\n编号：".$batchcode."\n商品：".$pro_name."\n商家已".$status_str."您的".$sendstatus_str.($status == 2 ? "\n原因：".$reason : "\n\n备注：".$reason."");

			$send_express_id = 0;
			$query = "select send_express_id from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
			$result= _mysql_query($query);
			while( $rows2 = mysql_fetch_object($result)){
				$send_express_id = $rows2->send_express_id;
			}
			// if($status==1 && ($send_express_id == -1|| $send_express_id == -1)){
			// 	$content=$content;
			// }elseif($status==1){
			// 	$content=$content."\n".$content_address;
			// }
			if( $send_express_id <=0 ){
				$content=$content;
			}elseif( $send_express_id > 0 ){
				$content=$content."\n".$content_address;
			}
			//_file_put_contents("log/order_confirmReturnGood_" . $today . ".txt", "content=======".var_export($content,true)."\r\n",FILE_APPEND);

			if($yundian_self == 1) {
				$yundian_url = "../../../mshop/web/index?m=yundian&a=return_address_user_in&batchcode=".$batchcode."&customer_id=".$customer_id_en."&yundian=1";
				$content = "您的订单：".$batchcode."的".$sendstatus_str."，店主已经".$status_str.($status == 2 ? "，有疑问直接联系店主" : "，请您及时退货并且上传退货的物流单号。\n<a href='".$yundian_url."'>立即上传物流单号＞</a>");
			}

			//if($status == 1){			//驳回也发送消息通知
				$shopmessage->SendMessage($content,$fromuser,$customer_id);
			//}


			$json["status"] = 0;
			$json["line"] =922;
			$json["msg"] = "编号：".$batchcode."，退货操作提交成功";
		}else{
			$json["status"] = 0;
			$json["line"] = 926;
			$json["msg"] = "订单信息不完成，请重新刷新页面";
		}

	break;

	case "confirmReturnMoney":  //退款申请管理

		(int)$status = $configutil->splash_new($_POST["status"]);
		$reason = $configutil->splash_new($_POST["reason"]);
		if(!empty($batchcode) && !empty($status)){

			$status_str = $status == 1 ? "同意" : "驳回";

			$st = $status == 1 ? 8 : 9;
			//修改订单表状态
			if($status == 1){ //同意


				/*  加回库存 */

				//$shopmessage->addStores($batchcode,$customer_id);  //by yehecong 2017-1-11
				/*  加回库存 End */
				$yundian_self       = 0;//是否云店自营订单
				$query_up_status = "update weixin_commonshop_orders set return_status = ".$st." where isvalid = true and sendstatus = 5  and batchcode = '".$batchcode."'";
                $query_update_status = "update weixin_commonshop_order_prices set sendstatus = 5 where isvalid = true and batchcode = '".$batchcode."'";

				/*4M start*/
				$query ="select pid,rcount,prvalues,is_exchange,exchange_id,yundian_self,camilo_ids from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."' and customer_id=".$customer_id;
				$result = _mysql_query($query) or die('Query_orders failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$pid 		= $row->pid;	//产品ID
					$rcount 	= $row->rcount;	//购买产品数量
					$prvalues	= $row->prvalues;	//产品属性
                    $is_exchange = $row->is_exchange; //是否为换购产品
                    $exchange_id = $row->exchange_id; //换购活动的id
                    $yundian_self = $row->yundian_self; //是否为云店自营订单
                    $camilo_ids = $row->camilo_ids;   //对应虚拟卡密id


                    /*卡密退款START*/
                    //判断卡密是否为空
                    if (!empty($camilo_id)) {
                        $camilo_log_str = "卡密操作：退款，订单号：{$batchcode}";
                        //更新卡密的状态
                        $query = "UPDATE ".WSY_PROD.".weixin_commonshop_camilo SET status=1,batchcode = '' WHERE id in ({$camilo_ids}) AND isvalid = TRUE";
                        $camilo_log_str .= "\n卡密执行sql:{$query}";
                        $result_camilo = _mysql_query($query) OR die('Query_failed'.__LINE__.':'.mysql_error());
                        if ($result_camilo) {
                            $camilo_log_str .= "\n执行成功";
                            //判断成功的情况下清空订单的卡密
                            $query = "UPDATE weixin_commonshop_orders SET camilo_ids = '' WHERE batchcode = '{$batchcode}' AND isvalid = TRUE";
                            $camilo_log_str .= "\n修改订单sql:{$query}";
                            $result_camilo = _mysql_query($query) OR die('Query_failed'.__LINE__.':'.mysql_error());
                            if ($result_camilo) {
                                $camilo_log_str .= "\n执行成功";
                                //插入卡密日志
                                $time = date('Y-m-d H:i:s', time());
                                //切割字符串为数组
                                $camilo_arr = explode(',', $camilo_ids);
                                //插入卡密记录日志
                                foreach ($camilo_arr as $v) {
                                    $sql = 'INSERT INTO '.WSY_PROD.".weixin_commonshop_camilo_log(customer_id,camilo_id,createtime,operation,comment) VALUES('{$customer_id}', '{$v}', '{$time}', '退货', '修改状态为（正常）')";
                                    _mysql_query($sql)or die("CA Counter1 Query failed :".__LINE__.mysql_error());
                                }
                            } else {
                                $camilo_log_str .= "\n执行失败";
                            }
                        } else {
                            $camilo_log_str .= "\n执行失败";
                        }
                    } else {
                        $camilo_log_str = "卡密操作：退款，订单号：{$batchcode}，该订单号无关联卡密";
                    }
                    $LogOpe = new LogOpe('order.class.php');
                    $LogOpe->log_insert($camilo_log_str);
                    /*卡密退款END*/


					if(!empty($prvalues)){
							$sql_4m = "select create_type from weixin_commonshop_product_prices where product_id=".$pid." and proids='".$prvalues."'";
							$result_4m = _mysql_query($sql_4m) or die("stockrecovery Query error : ".mysql_error());
							while ($row_4m = mysql_fetch_object($result_4m)) {
								$create_type = $row_4m->create_type;
							}
							//4M同步库存
							if($create_type !=3 ){
								//$is_4m = true; //ces
								//$create_type = 1; //ces
								$shop_4m->sync_4M_product_storenum($is_4m,1,1,$rcount,$pid,$prvalues,-1,$create_type);
							}
					}else{
							$sql_4m = "select create_type from weixin_commonshop_products where id=".$pid."";
							$result_4m = _mysql_query($sql_4m) or die("stockrecovery Query error : ".mysql_error());
							while ($row_4m = mysql_fetch_object($result_4m)) {
								$create_type = $row_4m->create_type;
							}
							if($create_type !=3 ){
								//4M同步库存
								//$is_4m = true; //ces
								//$create_type = 1; //ces
								$shop_4m->sync_4M_product_storenum($is_4m,2,1,$rcount,$pid,'',-1,$create_type);

							}
					}

				}

					if($create_type !=3 ){	//4M产品库存同步执行语句
						if($is_4m){
							$shop_4m->update_sql_sync_4M_product_storenum(4);
						}
					}else{ //防止4M父级产品扣2次库存，特意分开
						$shopmessage->addStores($batchcode,$customer_id);  //普通商品
					}

				/*4M end*/
			}else{  //驳回 , 驳回退款后，将订单状态重新设置为未发货
				$yundian_self       = 0;//是否云店自营订单
				$query_up_status = "update weixin_commonshop_orders set return_status = ".$st." , sendstatus = 0 where isvalid = true  and sendstatus = 5 and batchcode = '".$batchcode."'";
                $is_collageActivities = 0;//是否拼团订单；0：不是，1：拼团有效订单
                $query_ord = "select is_collageActivities,yundian_self from weixin_commonshop_orders where batchcode='".$batchcode."' limit 1";
                $result_ord         = _mysql_query($query_ord) or die('Query_ord failed: ' . mysql_error());
                while ($row_ord     = mysql_fetch_object($result_ord)) {
                    $is_collageActivities  = $row_ord->is_collageActivities;
                    $yundian_self = $row_ord->yundian_self; //是否为云店自营订单
                }                
                
                //拼团订单
                if($is_collageActivities == 1){
                    $queryc = "SELECT cgot.type,cgot.pid,ccot.rcount,ccot.group_id,ccot.activitie_id FROM collage_crew_order_t AS ccot 
                    LEFT JOIN collage_group_order_t AS cgot ON cgot.id=ccot.group_id 
                    WHERE ccot.batchcode='".$batchcode."'";
                    $resultc = _mysql_query($queryc) or die('queryc failed:'.mysql_error());
                    $collage_type = 1;
                    while( $rowc = mysql_fetch_object($resultc) ){
                        $collage_type = $rowc -> type; 
                        $pid = $rowc -> pid; 
                        $rcount = $rowc -> rcount;
                        $group_id = $rowc -> group_id;                      
                        $activitie_id = $rowc -> activitie_id;                      
                    }
                }
                
                if($is_collageActivities == 1 && $collage_type == 6 ){
                    $query_status_up = "UPDATE collage_crew_order_t SET status=2,is_refund=false WHERE batchcode='".$batchcode."'";
                    _mysql_query($query_status_up) or die('Query_status_up failed:'.mysql_error());
                    
                    $price = 0;
                    $query_order_price = "SELECT price FROM weixin_commonshop_order_prices WHERE batchcode='".$batchcode."'";
                    $result_order_price = _mysql_query($query_order_price) or die('Query_order_price failed:'.mysql_error());
                    while( $row_order_price = mysql_fetch_object($result_order_price) ){
                        $price = $row_order_price -> price;
                    }
                    
                    $query_group_up = "UPDATE collage_group_order_t SET join_num=join_num+1,total_price=total_price+".$price." WHERE id=".$group_id;
                    _mysql_query($query_group_up) or die('Query_group_up failed:'.mysql_error());
                    
                    $query_count_up = "UPDATE collage_group_products_t SET stock=stock-".$rcount." WHERE activitie_id=".$activitie_id." AND pid=".$pid." AND isvalid=true";
                    _mysql_query($query_count_up) or die('Query_count_up failed:'.mysql_error());
                    
                }


                //云店订单
                if(!empty($yundian_self)){		//云店自营订单退款时修改了aftersale_type和aftersale_state字段，驳回时修改回来
                	$query_up_status = "update weixin_commonshop_orders set return_status = ".$st." , sendstatus = 0 , aftersale_type = 0 , aftersale_state = 0 where isvalid = true  and sendstatus = 5 and batchcode = '".$batchcode."'";
                }               
			}
			//_file_put_contents("log/order_confirmReturnMoney_" . $today . ".txt", "\r\nquery_up_status=======".var_export($query_up_status,true)."\r\n",FILE_APPEND);
			_mysql_query($query_up_status);
			_mysql_query($query_update_status);

			//添加订单日志
			if($yundian_self ==1) {
				$yundian_user_id_re = $_SESSION['user_id_'.$customer_id];

				$yundian_user_sql = "select weixin_fromuser from weixin_users where id='".$yundian_user_id_re."' ";
				$result_yundian_user = _mysql_query($yundian_user_sql)or die("Query_user error  : ".mysql_error());
				$yundian_fromuser = mysql_result($result_yundian_user,0,0);

				$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
			values('".$batchcode."',11,'云店店主".$status_str."用户的退款申请".($status == 2 ? ",商家回复：".$reason.";订单更新为未发货状态。" : ",商家回复：".$reason."")."','".$yundian_fromuser."',now(),1)";
			} else {
				$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
			values('".$batchcode."',11,'平台".$status_str."用户的退款申请".($status == 2 ? ",商家回复：".$reason.";订单更新为未发货状态。" : ",商家回复：".$reason."")."','".$log_username."',now(),1)";
			} 

			//_file_put_contents("log/order_confirmReturnMoney_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
			_mysql_query($query_log);

			$query_user = "select weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
			//_file_put_contents("log/order_confirmReturnMoney_" . $today . ".txt", "query_user=======".var_export($query_user,true)."\r\n",FILE_APPEND);
			$result_user = _mysql_query($query_user);
			$fromuser = mysql_result($result_user,0,0);

			$content = "亲，您有一笔订单\n编号：".$batchcode."\n商家已".$status_str."您的退款申请".($status == 2 ? "\n原因：".$reason : "\n原因：".$reason."\n退款正在处理中 ... ");

			if($yundian_self == 1) {
				$content = "您的订单：".$batchcode."的退款申请，店主已经".$status_str.($status == 2 ? "。" : "n退款正在处理中 ... ");
			}
			//if($status ==1 ){		//驳回不发送消息
				$shopmessage->SendMessage($content,$fromuser,$customer_id);
			//}


			$json["status"] = 0;
			$json["line"] =860;
			$json["msg"] = "编号：".$batchcode."，退款操作提交成功";
		}else{
			$json["status"] = 0;
			$json["line"] = 864;
			$json["msg"] = "订单信息不完成，请重新刷新页面";
		}

	break;

	case "confirmReturnAftersale":  //维权申请管理

		(int)$status = $configutil->splash_new($_POST["status"]);
		$reason = $configutil->splash_new($_POST["reason"]);
		if(!empty($batchcode) && !empty($status)){

			$status_str = $status == 1 ? "同意" : "驳回";

			$st = $status == 1 ? 2 : 3;
			//修改订单表状态
			if($status == 1){ //同意
				$query_up_status = "update weixin_commonshop_orders set aftersale_state = ".$st." where isvalid = true and batchcode = '".$batchcode."'";
			}else{  //驳回 , 驳回维权后，将订单状态重新设置为拒绝维权状态
				$query_up_status = "update weixin_commonshop_orders set aftersale_state = ".$st.",aftersale_reason= '".$reason."' where isvalid = true and batchcode = '".$batchcode."'";
			}
			//_file_put_contents("log/order_confirmReturnAftersale_" . $today . ".txt", "\r\nquery_up_status=======".var_export($query_up_status,true)."\r\n",FILE_APPEND);
			_mysql_query($query_up_status);

			//添加订单日志
			$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
			values('".$batchcode."',19,'平台".$status_str."用户的维权申请".($status == 2 ? ",原因：".$reason : "")."','".$log_username."',now(),1)";
			//_file_put_contents("log/order_confirmReturnAftersale_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
			_mysql_query($query_log);

			$query_user = "select weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
			//_file_put_contents("log/order_confirmReturnAftersale_" . $today . ".txt", "query_user=======".var_export($query_user,true)."\r\n",FILE_APPEND);
			$result_user = _mysql_query($query_user);
			$fromuser = mysql_result($result_user,0,0);


			$content = "编号：".$batchcode.";商家已".$status_str."您的售后申请".($status == 2 ? ",原因：".$reason.",正在处理中 ... " : "");


			//if($status == 1){				//驳回不发送消息
				$shopmessage->SendMessage($content,$fromuser,$customer_id);
			//}


			$json["status"] = 0;
			$json["line"] =947;
			$json["msg"] = "编号：".$batchcode."，维权操作提交成功";
		}else{
			$json["status"] = 0;
			$json["line"] = 950;
			$json["msg"] = "订单信息不完成，请重新刷新页面";
		}

	break;

	case "goodRefund":  //退货管理

		(float)$refund 				= $configutil->splash_new($_POST["totalprice"]);//退款的金额
		(float)$currency			= $configutil->splash_new($_POST["currencyMoney"]);//退款的购物币
		(float)$integral			= $configutil->splash_new($_POST["totalintegral"]);//退还的积分
		(float)$refundSupplyMoney	= $configutil->splash_new($_POST["refundSupplyMoney"]);//退给供应商的金额
		$retype 					= $configutil->splash_new($_POST["retype"]);
		(float)$need_score_sum 		= $configutil->splash_new($_POST["need_score"]);
		(float)$block_task 		    = $configutil->splash_new($_POST["block_task"]);//来值区块链退款对账定时任务，为1时部分代码不执行,其他地方的代码不用传该参数或者传0
		/*处理空值 start 2018-3-13*/
		$refund 			  = empty($refund)?0:$refund;
		$currency 			  = empty($currency)?0:$currency;
		$integral             = empty($integral)?0:$integral;
		$refundSupplyMoney    = empty($refundSupplyMoney)?0:$refundSupplyMoney;
		$retype               = empty($retype)?0:$retype;
		$need_score_sum       = empty($need_score_sum)?0:$need_score_sum;
		$block_task           = empty($block_task)?0:$block_task;
		/*处理空值 end*/
		if($retype == 2){  //退货
			$retype = 1;
		}
		$o_card_member_id 	= -1;
		$agent_id			= 0;//代理商id
		$agentcont_type		= 0;//代理结算: 1、代理结算 0、推广员结算
		$buyer_user_id 		= -1;
		$supply_id			= -1;//供应商ID
		$Pay_Method			= 0;//0：真实支付；1：后台支付; 2:奖品赠送
        $is_collageActivities = 0;//是否拼团订单；0：不是，1：拼团有效订单
        $yundian_self       = 0;//是否云店自营订单
        $block_chain_price  = 0;//区块链支付金额
		$query_ord = "select sendstatus,paystyle,card_member_id,agent_id,agentcont_type,user_id,supply_id,Pay_Method,is_collageActivities,yundian_self,block_chain_price from weixin_commonshop_orders where batchcode='".$batchcode."' limit 1";
		//_file_put_contents("log/order_goodRefund_" . $today . ".txt", "\r\nquery_ord=======".var_export($query_ord,true)."\r\n",FILE_APPEND);
		$result_ord         = _mysql_query($query_ord) or die('Query_ord failed: ' . mysql_error());
		while ($row_ord     = mysql_fetch_object($result_ord)) {
			$sendstatus     = $row_ord->sendstatus;
			$paystyle       = $row_ord->paystyle;
			$o_card_member_id = $row_ord->card_member_id;

			$agent_id       = $row_ord->agent_id;
			$supply_id      = $row_ord->supply_id;
			$Pay_Method      = $row_ord->Pay_Method;
			$agentcont_type = $row_ord->agentcont_type;

			$buyer_user_id  = $row_ord->user_id;
            $is_collageActivities  = $row_ord->is_collageActivities;
            $yundian_self  = $row_ord->yundian_self;
            $block_chain_price = $row_ord->block_chain_price;
		}
		$playmoney_onoff = 1; //退款是否通过平台打款
		if($yundian_self == 1) { //如果为云店自营产品，查询最后是否通过平台退款
			$is_pingtai_sql = "SELECT playmoney_onoff FROM ".WSY_REBATE.".weixin_yundian_setting WHERE customer_id=".$customer_id." AND isvalid='1' ";
			$is_pingtai_re  = _mysql_query($is_pingtai_sql) or die('Query_is_yundian failed: ' . mysql_error());
			while ($row_is_pingtai_re     = mysql_fetch_object($is_pingtai_re)) {
				$playmoney_onoff = $row_is_pingtai_re->playmoney_onoff;
			}
		}
		/* 区块链积分 自定义名称start*/
		$block_chain_score = '区块链积分';
		if( $paystyle == '区块链积分支付' and $block_chain_price>0 ){
			$query_block_chain = "select name from ".WSY_SHOP.".block_chain_setting where customer_id='".$customer_id."'";
			$result_block_chain = _mysql_query($query_block_chain) or die('Query_block_chain failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result_block_chain)) {
			   $block_chain_score = $row->name;
			}
		}
		/* 区块链积分 自定义名称end*/
		if( ($sendstatus != 4 and $sendstatus != 6) || $block_task == 1)
		{ //如果收货状态 不是 已退货和已退款
            if( $currency > 0 && $block_task == 0){
                $user_currency = 0;	//用户的购物币数量
                $query_currency = "SELECT currency FROM weixin_commonshop_user_currency WHERE user_id=".$buyer_user_id." AND isvalid=true";
                $result_currency = _mysql_query($query_currency) or die('Query_currency failed:'.mysql_error());
                while( $row_currency = mysql_fetch_object($result_currency) ){
                    $user_currency = $row_currency -> currency;
                }
                $user_currency += $currency;
                //插入购物币日志
                $sql = "insert into weixin_commonshop_currency_log(isvalid,customer_id,user_id,cost_money,cost_currency,after_currency,batchcode,status,type,class,remark,createtime) values (true,".$customer_id.",".$buyer_user_id.",0,".$currency.",".$user_currency.",".$batchcode.",1,1,4,'商城退款',now())";
                _mysql_query($sql) or die('购物币 Query failed: ' . mysql_error());

                //退购物币
                $sql = "update weixin_commonshop_user_currency set currency=currency+".$currency." where isvalid=true and user_id=" . $buyer_user_id;
                _mysql_query($sql) or die('购物币6 Query failed: ' . mysql_error());
            }
            //退还积分
            if( $integral > 0 && $block_task == 0){
				$model_integral = new model_integral();

				$re_integral['cust_id']	     = $customer_id;
				$re_integral['batchcode']    = $batchcode;
				$re_integral['integral_num'] = $integral;
				$re_integral['user_id']		 = $buyer_user_id;

				$refund_data = $model_integral->m_refund_Integral($re_integral);


            }
            //调用通联分期退款接口
			if( $paystyle == '通联分期支付' ){
				// 实例化db类
				require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
				$db = new \Key\DB(true);

				// 通联退款接口
				$industry_type = 'shop';
				require_once($_SERVER['DOCUMENT_ROOT'].'/wsy_pay/web/allinpay/installment/refund.php');
				$allinpay = new Allinpay();
				$refund_result = $allinpay -> refund($customer_id,$batchcode,$industry_type);
				$refund_results = json_decode($refund_result);
                file_put_contents($_SERVER['DOCUMENT_ROOT']."/weixinpl/log/order_goodRefund_" . $today . ".txt", "refunds=======".var_export($refund_results,true)."\r\n",FILE_APPEND);
				if( $refund_results->sucess_business_response->code === 0 ){
					// 获取订单资料
					$sql = "SELECT pay_batchcode,real_pay_price,pay_time,allinpay_client from system_order_pay_log where customer_id='{$customer_id}' and batchcode_str='{$batchcode}'";
					$order_log = $db->getFields($sql);
					$pay_batchcode = $order_log['pay_batchcode'];
					$res_timestamp = $refund_results->sucess_business_response->res_timestamp;

					switch ($industry_type) {
						case 'package':
							$sql = "SELECT return_account from package_return_t where batchcode='{$batchcode}'";
							break;
						
						default:
							$sql = "SELECT return_account from weixin_commonshop_orders where customer_id='{$customer_id}' and batchcode='{$batchcode}'";
							break;
					}
					$amount = bcadd($db->getField($sql),0,2);

					// 第三方退款操作
					require_once($_SERVER["DOCUMENT_ROOT"]."/wsy_pay/web/function/handle_order_refund.php");
					$url = handle_order_refund($amount,$industry_type,$batchcode,$pay_batchcode,$res_timestamp,"通联分期支付",25);
				}else{
					$json["status"] = 10002;
					$json["msg"] = '退款失败';
					$jsons=json_encode($json);
					die($jsons);
				}
			}
			if( $paystyle != '微信支付' && $paystyle != '支付宝支付'){
                $sql_e = "select id from weixin_commonshop_refunds where isvalid =true and batchcode='".$batchcode."'";
                $result_e = _mysql_query($sql_e) or die("Query_stat error : ".mysql_error());
                $refund_id_e = -1;
                while ($row_e = mysql_fetch_object($result_e)) {
                    $refund_id_e = $row_e->id;
                }
                if($refund_id_e<0 && $block_task == 0){
                    $refunds = "insert into weixin_commonshop_refunds (customer_id,batchcode,refund,currency,isvalid,createtime) values(".$customer_id.",'".$batchcode."',".$refund.",".$currency.",true,now())";
                    _mysql_query($refunds) or die ("Query_refunds2 ERROR : ".mysql_error());
                }

				if( $paystyle == '零钱支付'  || $Pay_Method == 1 ){	//后台支付退零钱

                //拼团订单
                if($is_collageActivities == 1){
                    $queryc = "SELECT ccot.group_id,cgot.type,cgot.status,cgot.head_id FROM collage_crew_order_t AS ccot 
                    LEFT JOIN collage_group_order_t AS cgot ON cgot.id=ccot.group_id 
                    WHERE ccot.batchcode='".$batchcode."'";
                    $resultc = _mysql_query($queryc) or die('queryc failed:'.mysql_error());
                    $collage_type = 1;
                    while( $rowc = mysql_fetch_object($resultc) ){
                        $collage_type = $rowc -> type;
                        $collage_status = $rowc -> status;
                        $head_id = $rowc -> head_id;
                        $group_id = $rowc -> group_id;                        
                    }
                }

                if($is_collageActivities == 1 && $collage_type == 6 && $collage_status == 3 && $head_id == $buyer_user_id){
                    //团长免单团  拼团成功 的团长
                    //先扣回返给他的全部金额
                    $queryml = "SELECT money FROM moneybag_log WHERE batchcode='".$batchcode."'";
                    $resultml = _mysql_query($queryml) or die('queryml failed:'.mysql_error());
                    $returen_money = 0;
                    while( $rowml = mysql_fetch_object($resultml) ){
                        $returen_money = $rowml -> money;//团长免单团返回的金额
                    }
                    
                    $queryblc = "SELECT balance FROM moneybag_t where isvalid=true and user_id=" . $buyer_user_id;
                    $resulblc = _mysql_query($queryblc) or die('queryml failed:'.mysql_error());
                    $before_balance = 0;
                    while( $rowblc = mysql_fetch_object($resulblc) ){
                        $before_balance = $rowblc -> balance;//变动前的余额
                    }

                    //扣钱
                    $sql = "update moneybag_t set balance=balance-".$returen_money.",createtime=now() where isvalid=true and user_id=" . $buyer_user_id;
					_mysql_query($sql) or die('零钱支付3 Query failed: ' . mysql_error());

					if( $returen_money>0 ){ //金额大于0才插入日志
						$after_balance = $before_balance - $returen_money;//变动后的余额
						$sql ="insert into moneybag_log (isvalid,customer_id,user_id,before_money,money,after_money,type,batchcode,pay_style,remark,createtime)values(true,".$customer_id.",".$buyer_user_id.",".$before_balance.",-".$returen_money.",".$after_balance.",1,".$batchcode.",0,'商城退款，扣回团长免单团已返金额',now())";
						_mysql_query($sql) or die('零钱支付33 Query failed: ' . mysql_error());
					}


                }
                
                    $queryblc2 = "SELECT balance FROM moneybag_t where isvalid=true and user_id=" . $buyer_user_id;
                    $resulblc2 = _mysql_query($queryblc2) or die('queryml failed:'.mysql_error());
                    $before_balance2 = 0;
                    while( $rowblc2 = mysql_fetch_object($resulblc2) ){
                        $before_balance2 = $rowblc2 -> balance;//变动前的余额
                    }

					$sql = "update moneybag_t set balance=balance+".$refund.",createtime=now() where isvalid=true and user_id=" . $buyer_user_id;
					_mysql_query($sql) or die('零钱支付2 Query failed: ' . mysql_error());

					if( $refund>0 ){ //金额大于0才插入日志
						$after_balance2 = $before_balance2 + $refund;//变动后的余额
						if($yundian_self == 1) {
							$sql ="insert into moneybag_log (isvalid,customer_id,user_id,before_money,money,after_money,type,batchcode,pay_style,remark,createtime)values(true,".$customer_id.",".$buyer_user_id.",".$before_balance2.",".$refund.",".$after_balance2.",0,".$batchcode.",0,'云店自营退款',now())";
						} else {
							$sql ="insert into moneybag_log (isvalid,customer_id,user_id,before_money,money,after_money,type,batchcode,pay_style,remark,createtime)values(true,".$customer_id.",".$buyer_user_id.",".$before_balance2.",".$refund.",".$after_balance2.",0,".$batchcode.",0,'商城退款',now())";
						}
						
						_mysql_query($sql) or die('零钱支付22 Query failed: ' . mysql_error());
					}



				}else if( $paystyle == '会员卡余额支付' and $o_card_member_id>0 ){
					/*$before_cost = 0;
					$consume_before="select remain_consume from weixin_card_member_consumes where card_member_id=".$o_card_member_id." limit 1";
					//_file_put_contents("log/order_goodRefund_" . $today . ".txt", "consume_before=======".var_export($consume_before,true)."\r\n",FILE_APPEND);
					$result_before = _mysql_query($consume_before) or die('Query_consume_before ERROR: ' . mysql_error());
					while ($row_before = mysql_fetch_object($result_before)) {
						$before_cost = $row_before->remain_consume;
					}
					$after_cost = $before_cost + $refund;

					//会员卡返回金额
					$consume = "update weixin_card_member_consumes set total_consume= total_consume-" . $refund . ",remain_consume=remain_consume+".$refund."  where card_member_id=".$o_card_member_id;
					//_file_put_contents("log/order_goodRefund_" . $today . ".txt", "consume=======".var_export($consume,true)."\r\n",FILE_APPEND);
					_mysql_query($consume) or die ("Query_consume ERROR : ".mysql_error());

					//会员卡金额改动日志
					if( $refund>0 ){ //金额大于0才插入日志
						$consume_log = "insert into weixin_card_recharge_records(new_record,before_cost,cost,after_cost,card_member_id,isvalid,createtime,remark) values(1,".$before_cost.",".$refund.",".$after_cost.",".$o_card_member_id.",true,now(),'订单取消，会员卡余额返回')";
						//_file_put_contents("log/order_goodRefund_" . $today . ".txt", "consume_log=======".var_export($consume_log,true)."\r\n",FILE_APPEND);
						_mysql_query($consume_log);
					}*/

					//退款减会员卡消费总额
					$before_money = 0;	//返退款前余额
					$query = "select remain_consume from weixin_card_member_consumes where isvalid=true and card_member_id=" . $o_card_member_id ." limit 0,1";
					$result = _mysql_query($query) or die('会员卡余额2 Query failed: ' . mysql_error());

					while ($row = mysql_fetch_object($result)) {
						$before_money = $row->remain_consume;
					}

					$after_money = $before_money + $refund;

					$query_card_consume = "UPDATE weixin_card_member_consumes SET total_consume=total_consume-".$refund.",remain_consume=remain_consume+".$refund." WHERE card_member_id=".$o_card_member_id." AND isvalid=true";
					_mysql_query($query_card_consume) or die('Query_card_consume failed:'.mysql_error());

					if ( $retype == 0 ){
						$type_remark = '退款';
					} else if ( $retype == 1 ){
						$type_remark = '退货';
					}

					$remark = '订单'.$type_remark.",会员卡余额增加".$refund.",商城订单号:".$batchcode;
					//退款金额大于查入日志
					if( $refund > 0 ){
						$query_record="INSERT INTO weixin_card_recharge_records (
									card_member_id,
									before_cost,
									cost,
									after_cost,
									remark,
									new_record,
									isvalid,
									createtime
								)
								VALUES
									(
										".$o_card_member_id.",
										".$before_money.",
										".$refund.",
										".$after_money.",
										'".$remark."',
										1,
										TRUE,
										now()
									)";
						_mysql_query($query_record) or die('Query_record failed:'.mysql_error());
					}
				}else if( $paystyle == '区块链积分支付' and $block_chain_price>0 ){

					//先将订单改成退款中状态，减少定时任务压力
					$update_block_chain = "UPDATE ".WSY_SHOP.".block_chain_order_detail SET status=3,run_num=0 WHERE batchcode='".$batchcode."'";
					_mysql_query($update_block_chain) or die('Update_block_chain failed:'.mysql_error());
					// 区块链退款接口
					require_once($_SERVER['DOCUMENT_ROOT'].'/wsy_pay/web/blockchain_pay/refund.php');

					$block_chain_extra = array(
						'refund' => $refund,
						'currency' => $currency,
						'integral' => $integral,
						'refundSupplyMoney' => $refundSupplyMoney,
						'retype' => $retype,
						'need_score_sum' => $need_score_sum,
					);
					$extra_json = json_encode($block_chain_extra);//将退款参数记录方便退款定时任务使用
					if($block_task == 0){
						$blockchain = new blockchain_refund();
						$refund_result = $blockchain -> refund($customer_id,$batchcode,$refund,$extra_json);
		                file_put_contents($_SERVER['DOCUMENT_ROOT']."/weixinpl/log/order_goodRefund_" . $today . ".txt", "refunds=======".var_export($refund_result,true)."\r\n",FILE_APPEND);
		                if(empty($refund_result) || $refund_result['errcode'] == 20018){
			                $json["status"] = 10003;
							$json["msg"] = '请上传区块链支付证书或检查证书是否正确';
							$jsons=json_encode($json);
							die($jsons);
		                }elseif(empty($refund_result) || $refund_result['errcode'] != 0){
			                $json["status"] = 10002;
							$json["msg"] = '退款失败';
							$jsons=json_encode($json);
							die($jsons);
		                }else{
		                	$update_block_chain_sec = "UPDATE ".WSY_SHOP.".block_chain_order_detail SET status=4,run_num=3 WHERE batchcode='".$batchcode."'";
							_mysql_query($update_block_chain_sec) or die('Update_block_chain failed:'.mysql_error());
							$insert_block_chain_sec = "INSERT INTO  ".WSY_SHOP.".block_chain_log (customer_id,user_id,status,batchcode,reward,remark,createtime) values(".$customer_id.",'".$buyer_user_id."',1,".$batchcode.",".$refund.",'用户申请退款',now())";
							_mysql_query($insert_block_chain_sec) or die('Insert_block_chain_sec failed:'.mysql_error());
		                }
	                }

				}


			}else{
                $sql0 = "select id from weixin_commonshop_refunds where isvalid =true and batchcode='".$batchcode."'";
                $result0 = _mysql_query($sql0) or die("Query_stat error : ".mysql_error());
                $refund_id = -1;
                while ($row0 = mysql_fetch_object($result0)) {
                    $refund_id = $row0->id;
                }
                if($refund_id<0 && $refund > 0){
                    //$refunds = "insert into weixin_commonshop_refunds (customer_id,batchcode,refund,currency,isvalid,createtime) values(".$customer_id.",'".$batchcode."',".$refund.",".$currency.",true,now())";
                    //_file_put_contents("log/order_goodRefund_" . $today . ".txt", "refunds=======".var_export($refunds,true)."\r\n",FILE_APPEND);
                    //_mysql_query($refunds) or die ("Query_refunds2 ERROR : ".mysql_error());
                }else{
                    $refunds = "update weixin_commonshop_refunds set currency=".$currency." where id=".$refund_id;
					_mysql_query($refunds) or die('Query_refunds2 failed: ' . mysql_error());
                }

            }


			if($retype == 0 && $sendstatus !=6){ //退款


				if($sendstatus == 5){ //未发货的申请退款 更新为 6
					$orders="update weixin_commonshop_orders set sendstatus=6 where batchcode='".$batchcode."'";
				}

				_file_put_contents("log/order_goodRefund_" . $today . ".txt", "orders=======".var_export($orders,true)."\r\n",FILE_APPEND);
				_mysql_query($orders) or die (" Query_orders ERROR : ".mysql_error());
                
                if($is_collageActivities == 1 && $collage_type == 6 ){
                    $query_status_up = "UPDATE collage_crew_order_t SET status=6 WHERE batchcode='".$batchcode."'";
                    _mysql_query($query_status_up) or die('Query_status_up failed:'.mysql_error());
                    
                    $query_order_up = "UPDATE weixin_commonshop_orders SET status=-1 WHERE batchcode='".$batchcode."'";
                    _mysql_query($query_order_up) or die('Query_order_up failed:'.mysql_error());
                    
                    $query_orderp_up = "UPDATE weixin_commonshop_order_prices SET status=-1 WHERE batchcode='".$batchcode."'";
                    _mysql_query($query_orderp_up) or die('Query_orderp_up failed:'.mysql_error());
                    
                    $query = "SELECT batchcode,paystyle FROM collage_crew_order_t WHERE customer_id=".$customer_id." AND group_id=".$group_id." AND isvalid=true AND is_refund=false AND status=3";
                    $result = _mysql_query($query) or die('Query failed:'.mysql_error());
                    while ( $row = mysql_fetch_object($result) ) {
                        $batchcode_arr[] = array(
                            'batchcode' => $row -> batchcode,
                            'paystyle'	=> $row -> paystyle
                        );
                    }
                    if ( empty($batchcode_arr) ) {	//该团已全部退款
                        //更改团退款状态
                        $query_gstatus_up = "UPDATE collage_group_order_t SET refund_status=2 WHERE id='".$group_id."'";
                        _mysql_query($query_gstatus_up) or die('Query_gstatus_up failed:'.mysql_error());
                    }   
                      
                }

				$custom = "购物币";
				$query = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
				$result = _mysql_query($query)or die('Query_ord_pro failed 1467: ' . mysql_error());
				while( $rowc = mysql_fetch_object($result) ){
					$custom = $rowc->custom;
				}
				//退款扣佣金
				$query_ord_pro = "select remark,reward,user_id,card_member_id,level_name,own_user_name,id_new,type,commission_type,commission_score from weixin_commonshop_order_promoters where isvalid=true and paytype=0 and batchcode='".$batchcode."'";
				_file_put_contents("log/order_goodRefund_" . $today . ".txt", "query_ord_pro=======".var_export($query_ord_pro,true)."\r\n",FILE_APPEND);
				$result_ord_pro = _mysql_query($query_ord_pro) or die('Query_ord_pro failed: ' . mysql_error());
				while ($row_ord_pro = mysql_fetch_object($result_ord_pro)) {
					$money = $row_ord_pro->reward;
					$user_id = $row_ord_pro->user_id;
					$card_member_id = $row_ord_pro->card_member_id;
					$level_name = $row_ord_pro->level_name;
					$own_user_name = $row_ord_pro->own_user_name;
					$id_new = $row_ord_pro->id_new;
					$protype = $row_ord_pro->type;
					$commission_type = $row_ord_pro->commission_type;
					$commission_score = $row_ord_pro->commission_score;
					//$remark = $level_name.$own_user_name."退款扣除:".$money;
					if($protype == 10){
						$tis = "扣除".$custom."：￥";
						$com = "个";
					}else{
						$tis = "退款扣除：￥";
						$com = "元";
					}
					$remark = "身份：【".$level_name."】\n".
							  "用户：【".$own_user_name."】\n";
					if( $commission_type == 1 && $money > 0 ){	//扣除零钱
						$remark .= $tis.$money.$com;
					}
					if( $commission_type == 2 && $commission_score > 0 ){	//扣除积分
						$remark .= "退积分扣除：".$commission_score;
					}
					//扣佣金
					$qr_info_id=-1;
					$query_qr_in="select id from weixin_qr_infos where type=1 and foreign_id=".$user_id." and user_type=1";
					//_file_put_contents("log/order_goodRefund_" . $today . ".txt", "query_qr_in=======".var_export($query_qr_in,true)."\r\n",FILE_APPEND);
					$result_qr_in = _mysql_query($query_qr_in) or die('Query_qr_in failed: ' . mysql_error());
					while ($row_qr_in = mysql_fetch_object($result_qr_in)) {
						$qr_info_id = $row_qr_in->id;
					}
					if($qr_info_id>0){
						$query_up_qr="update weixin_qrs set reward_money= reward_money-".$money." where qr_info_id=".$qr_info_id;
						//_file_put_contents("log/order_goodRefund_" . $today . ".txt", "query_up_qr=======".var_export($query_up_qr,true)."\r\n",FILE_APPEND);
						_mysql_query($query_up_qr);
					}
					//更改佣金表状态
					$query_up_pro = "update weixin_commonshop_order_promoters set do_time=now(),paytype=4 where id_new=".$id_new;
					//_file_put_contents("log/order_goodRefund_" . $today . ".txt", "query_up_pro=======".var_export($query_up_pro,true)."\r\n",FILE_APPEND);
					_mysql_query($query_up_pro);
					 //添加信息提醒
					$query5="select weixin_fromuser from weixin_users where id=".$user_id." limit 1";
					//_file_put_contents("log/order_goodRefund_" . $today . ".txt", "query5=======".var_export($query5,true)."\r\n",FILE_APPEND);
					$result5 = _mysql_query($query5) or die('Query5 failed: ' . mysql_error());
					$parent_fromuser="";
					while ($row5 = mysql_fetch_object($result5)) {
						 $parent_fromuser= $row5->weixin_fromuser;
					}
					$remark=addslashes($remark);
					$content = "买家退货\n时间：".date( "Y-m-d H:i:s")."\n".$remark;
					if($money>0){
						$shopmessage->SendMessage($content,$parent_fromuser,$customer_id);
					}

				}

				//添加日志记录，给退款用户发送消息
				if( $refund > 0 or $currency > 0 ){
					if($playmoney_onoff == 0 ) {
						$yundian_user_id_re = $_SESSION['user_id_'.$customer_id];
						$descript = "云店店主退款，退款";

						$yundian_user_sql = "select weixin_fromuser from weixin_users where id='".$yundian_user_id_re."' ";
						$result_yundian_user = _mysql_query($yundian_user_sql)or die("Query_user error  : ".mysql_error());
						$yundian_fromuser = mysql_result($result_yundian_user,0,0);
					} else {
						$descript = "平台退款，退款";
					}
					
					$content  = "订单：".$batchcode."\n商家已退款，\n退款";
					$descript2 = "";
					if( $refund > 0 ){
						$descript2 .= "金额：".$refund.'元，';
					}
					if( $currency > 0 ){
						$descript2 .= $custom.$currency.'，';
					}
					if( $refund_data['errcode']  == 0 && !empty($refund_data['data']['integral_num']) ){

						$descript2 .= $refund_data['data']['name'].$refund_data['data']['integral_num'].'，';
					}
					$descript .= $descript.$descript2;
					if($playmoney_onoff == 0) {
						$query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)values('".$batchcode."',12,'".$descript."','".$yundian_fromuser."',now(),1)";
					} else {
						$query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)values('".$batchcode."',12,'".$descript."','".$log_username."',now(),1)";						
					}

					//_file_put_contents("log/order_goodRefund_" . $today . ".txt", "query_logs=======".var_export($query_logs,true)."\r\n",FILE_APPEND);
					_mysql_query($query_logs) or die("Query_logs error  : ".mysql_error());
				//	$content = "订单：".$batchcode."\n商家已退款，\n退款金额：".$refund."元，\n请注意查收。";
					$content .= $descript2."\n请注意查收。";

					$query_user = "select weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
					$result_user = _mysql_query($query_user)or die("Query_user error  : ".mysql_error());
					$fromuser = mysql_result($result_user,0,0);

					if($yundian_self == 1) {
						$content = "您的订单：".$batchcode."的退款申请，店主已经同意，退款金额会原路退回。";
					}

					$shopmessage->SendMessage($content,$fromuser,$customer_id);
				}

			}else if($retype == 1){ //退货后退款

				$pid		= -1;
				$rcount 	=  0;
				$prvalues	= "";
				$query_ord = "select pid,rcount,prvalues,sendstatus,is_exchange,exchange_id from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
				$result_ord = _mysql_query($query_ord) or die('Query_ord failed: ' . mysql_error());
				 while ($row_ord = mysql_fetch_object($result_ord)) {
					$pid 		= $row_ord->pid;
					$rcount 	= $row_ord->rcount;
					$sendstatus	= $row_ord->sendstatus;
					$prvalues	= $row_ord->prvalues;
					$is_exchange = $row_ord->is_exchange;
					$exchange_id = $row_ord->exchange_id;

					$prvalues= rtrim($prvalues,"_"); //将添加产品库存的操作加到循环里，防止订单中有两件商品时加库存的问题
                     if($is_exchange != 1) {
                         if (!empty($prvalues)) {
                             $query_num_up = "update weixin_commonshop_product_prices set storenum= storenum+" . $rcount . " where product_id=" . $pid . " and proids='" . $prvalues . "'";
                             /*4M start*/
                             $sql_4m = "select create_type from weixin_commonshop_product_prices where product_id=" . $pid . " and proids='" . $prvalues . "'";
                             $result_4m = _mysql_query($sql_4m) or die("stockrecovery Query error : " . mysql_error());
                             while ($row_4m = mysql_fetch_object($result_4m)) {
                                 $create_type = $row_4m->create_type;
                             }
                             //4M同步库存
                             if ($create_type != 3) {
                                 //$is_4m = true; //ces
                                 //$create_type = 1; //ces
                                 $shop_4m->sync_4M_product_storenum($is_4m, 1, 1, $rcount, $pid, $prvalues, -1, $create_type);
                             }
                             /*4M end*/
                         } else {
                             $query_num_up = "update weixin_commonshop_products set storenum= storenum+" . $rcount . " where id=" . $pid;
                             /*4M start*/
                             $sql_4m = "select create_type from weixin_commonshop_products where id=" . $pid . "";
                             $result_4m = _mysql_query($sql_4m) or die("stockrecovery Query error : " . mysql_error());
                             while ($row_4m = mysql_fetch_object($result_4m)) {
                                 $create_type = $row_4m->create_type;
                             }
                             if ($create_type != 3) {
                                 //4M同步库存
                                 //$is_4m = true; //ces
                                 //$create_type = 1; //ces
                                 $shop_4m->sync_4M_product_storenum($is_4m, 2, 1, $rcount, $pid, '', -1, $create_type);

                             }
                             /*4M end*/
                         }
                     }else{
                         //换购活动对应的产品库存加回去
                         $ex_storenum = $ex_storenum + $rcount;
                         $query_num_up = "UPDATE weixin_commonshop_exchange_products SET storenum=".$ex_storenum." WHERE exchange_id='".$exchange_id."' and pid=".$pid;
                     }
					_mysql_query($query_num_up);
				 }
					/*4M库存同步执行语句 start */
					if($create_type !=3 ){
						if($is_4m){
							$shop_4m->update_sql_sync_4M_product_storenum(5);
						}
					}
					/*4M库存同步执行语句 end */
				if( $sendstatus != 4 ){
					//退货完成
					$query_ord_up = "update weixin_commonshop_orders set sendstatus=4 where batchcode='".$batchcode."'";
					_mysql_query($query_ord_up);

					$shopmessage->Back_GetMoney($batchcode,$o_card_member_id,$refund,$customer_id,$paystyle);
				}



				if($playmoney_onoff == 0 ) {
					$yundian_user_id_re = $_SESSION['user_id_'.$customer_id];

					$yundian_user_sql = "select weixin_fromuser from weixin_users where id='".$yundian_user_id_re."' ";
					$result_yundian_user = _mysql_query($yundian_user_sql)or die("Query_user error  : ".mysql_error());
					$yundian_fromuser = mysql_result($result_yundian_user,0,0);

					$query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
					values('".$batchcode."',12,'云店店主退货退款，退款金额：".$refund."','".$yundian_fromuser."',now(),1)";
				} else {
					$unit = '';
					if( $paystyle == '区块链积分支付' and $block_chain_price>0 ){
						$unit = $block_chain_score;
					}
					$query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
					values('".$batchcode."',12,'平台退货退款，退款金额：".$refund.$unit."','".$log_username."',now(),1)";
				}

				_mysql_query($query_logs) or die("Query_logs error  : ".mysql_error());

				$content = "订单：".$batchcode."\n商家已完成退货，\n退款金额：".$refund."元，\n请注意查收。";
				$query_user2 = "select weixin_fromuser from weixin_users where id= ".$buyer_user_id." limit 0,1";
				$result_user2 = _mysql_query($query_user2) or die("Query_user2 error  : ".$buyer_user_id.mysql_error());
				$fromuser = "";
				if($row_user2 = mysql_fetch_object($result_user2)){
					$fromuser = $row_user2->weixin_fromuser;
				}

				if($yundian_self == 1) {
					$content = "您的订单：".$batchcode."的退货申请，店主已经完成退款，退款金额会原路退回。";
				}
				$shopmessage->SendMessage($content,$fromuser,$customer_id);
			}

			if($supply_id>0 && $refundSupplyMoney>0){
				if($shopmessage->GetRefundSupply_Money($batchcode,$refundSupplyMoney,$supply_id)){//调用供应商退款方法
				//$shopmessage->GetRefundSupply_Money($batchcode,$refundSupplyMoney,$supply_id);
					//如果返回真，就发送消息给供应商
					$query_supply = "select weixin_fromuser from weixin_users where id= ".$supply_id." limit 0,1";
					$result_supply = _mysql_query($query_supply) or die("Query_query_supply error  : ".$supply_id.mysql_error());
					$fromuser_supply = "";
					if($row_supply = mysql_fetch_object($result_supply)){
						$fromuser_supply = $row_supply->weixin_fromuser;
					}
					$content = "订单：".$batchcode."\n商家已完成退货，\n账户余额增加：".$refundSupplyMoney."元，\n请注意查收。";
					$shopmessage->SendMessage($content,$fromuser_supply,$customer_id);
				}
			}
			if($need_score_sum>0){
				//返还会员积分
				$consume_score = 0;
				$before_score  = 0;
				$after_score   = 0;
				$before_score1  = 0;
				$after_score1   = 0;
				$remark = "退款返还积分";
				$query_score = "select remain_score from weixin_card_member_scores where isvalid=true and card_member_id= '" . $o_card_member_id ."' limit 1";
				$result_score = _mysql_query($query_score) or die("Query_query_mbScore error  : ".$o_card_member_id.mysql_error());
				while($row = mysql_fetch_object($result_score)){
					$before_score = $row->remain_score;
				}
				$after_score  = $before_score+$need_score_sum;
				$query_logs = "insert into weixin_card_score_records(card_member_id,before_score,after_score,score,createtime,remark,type,new_record,isvalid) values('".$o_card_member_id."','".$before_score."','".$after_score."','".$need_score_sum."',now(),'".$remark."',12,1,true)";
				$result_log = _mysql_query($query_logs) or die("Insert_query_logs error  : ".$o_card_member_id.mysql_error());

				$c_sql = "select score from weixin_card_score_records where isvalid=true and type=2 and score > 0 and batchcode = '".$batchcode."' and before_score = '".$after_score."'"; 
				$result_c_sql = _mysql_query($c_sql); 
				while($row_c = mysql_fetch_object($result_c_sql)){
					$score = $row_c->score;
				}
				if($score > 0){
					$remark1 = "扣除消费奖励积分";
					$consume_score = $score;
/*				$query_consume_score = "select price from weixin_commonshop_order_prices where paystatus = 1 and batchcode = '".$batchcode."'";
				$result_consume_score = _mysql_query($query_consume_score);
				while($row2 = mysql_fetch_object($result_consume_score)){
					$consume_score = $row2->price * 10;//消费奖励积分
				}*/
					$after_score1 =  $after_score - $consume_score;
					$before_score1 =  $after_score;
					$consume_score = 0 - $consume_score;
					$query_logs1 = "insert into weixin_card_score_records(card_member_id,before_score,after_score,score,createtime,remark,type,new_record,isvalid) values('".$o_card_member_id."','".$before_score1."','".$after_score1."','".$consume_score."',now(),'".$remark1."',12,1,true)";
					$result_log1 = _mysql_query($query_logs1) or die("Insert_query_logs error  : ".$o_card_member_id.mysql_error());
				}
				$puls_score = $need_score_sum + $consume_score;
				$update_card_mbScore = "update ".WSY_USER.".weixin_card_member_scores set total_score = total_score+".$puls_score.",consume_score = consume_score-".$need_score_sum.",remain_score = remain_score+".$puls_score." where isvalid=true and card_member_id = ".$o_card_member_id;
				$result_card_mbScore = _mysql_query($update_card_mbScore) or die("Update_update_card_mbScore error  : ".$o_card_member_id.mysql_error());
			}
   			/*云店自营订单 start*/
			if($yundian_self == 1){
				$yundian_reward = 0;//云店自营产品分佣 = 订单金额*（1-店主抽佣比例）
				$new_yundian_reward = 0;//退款后：云店自营产品分佣 = （订单金额-退款金额）*（1-店主抽佣比例）
				$yundian_order_prices = 0;//订单金额
				//$yundian_award_percentage = 0;//抽佣比例 防止后台改变
				$yun_commission = 0;
				$new_yun_reward = 0;//退款后：平台对云店的抽成
				$query_yundian_reward = "select yundian_reward,price,yun_commission from weixin_commonshop_order_prices where batchcode='".$batchcode."'";
                $result_yundian_reward = _mysql_query($query_yundian_reward) or die('Query_yundian_award failed:'.mysql_error());
                while( $row_yundian_reward = mysql_fetch_object($result_yundian_reward) ){
                    $yundian_reward = $row_yundian_reward -> yundian_reward;
                    $yundian_order_prices = $row_yundian_reward -> price;
                    $yun_commission  = $row_yundian_reward -> yun_commission;
                }
                $new_yundian_reward = ($yundian_order_prices - $refund)*(1-$yun_commission);
                $new_yun_reward = ($yundian_order_prices - $refund)*$yun_commission;
                if($new_yundian_reward < 0){$new_yundian_reward = 0;}
                $update_new_yundian_reward = "UPDATE weixin_commonshop_order_prices SET yundian_reward = '$new_yundian_reward' WHERE batchcode='".$batchcode."'";
                _mysql_query($update_new_yundian_reward) or die('Update_new_yundian_reward failed:'.mysql_error());
                $new_user_id = 0;
                $new_yundian_user_id = 0;
                $new_card_member_id = 0;
                $new_remark = '';
                $new_customer_id = 0;
                $new_level_name = '';
                $new_own_user_name = '';
                $new_paytype = 0;
                $new_red_pack_id = 0;
                $new_cityarea_id = 0;
                $new_commission_type = 0;
                $new_commission_score = 0;
                $new_level = 0;
                //云店自营产品奖励
                $select_promoters = "select user_id,yundian_user_id,reward,card_member_id,remark,customer_id,level_name,own_user_name,paytype,red_pack_id,cityarea_id,commission_type,commission_score,level from weixin_commonshop_order_promoters where batchcode='".$batchcode."' and isvalid = true and type=26 and class = 91";
                $result_promoters = _mysql_query($select_promoters) or die("query_logs error  : ".mysql_error());
                while($row_promoters = mysql_fetch_object($result_promoters)){
					$new_user_id = $row_promoters->user_id;
					$new_yundian_user_id = $row_promoters->yundian_user_id;
					$new_card_member_id = $row_promoters->card_member_id;
					//$new_remark = mysql_real_escape_string($row_promoters->remark);
					$new_customer_id = $row_promoters->customer_id;
					$new_level_name = mysql_real_escape_string($row_promoters->level_name);
					$new_own_user_name = mysql_real_escape_string($row_promoters->own_user_name);
					$new_paytype = $row_promoters->paytype;
					$new_red_pack_id = $row_promoters->red_pack_id;
					$new_cityarea_id = $row_promoters->cityarea_id;
					$new_commission_type = $row_promoters->commission_type;
					$new_commission_score = $row_promoters->commission_score;
					$new_level = $row_promoters->level;
				}
					$new_remark = "退款后:(".$new_own_user_name.")云店自营订单最终返还:".$new_yundian_reward."元";
					$update_yundian_self_pro = "UPDATE weixin_commonshop_order_promoters SET isvalid = false WHERE batchcode='".$batchcode."' and type=26 and class = 91";
					_mysql_query($update_yundian_self_pro) or die('Update_yundian_self_pro failed:'.mysql_error());
				if( $new_yundian_reward > 0 ){
					$insert_yundian_self_pro = "insert into weixin_commonshop_order_promoters(user_id,reward,card_member_id,isvalid,createtime,remark,customer_id,level_name,own_user_name,batchcode,paytype,red_pack_id,level,type,class,cityarea_id,commission_type,commission_score,yundian_user_id) values(".$new_user_id.",".$new_yundian_reward.",".$new_card_member_id.",true,now(),'".$new_remark."',".$new_customer_id.",'".$new_level_name."','".$new_own_user_name."',".$batchcode.",0,'".$new_red_pack_id."',".$new_level.",26,91,".$new_cityarea_id.",".$new_commission_type.",".$new_commission_score.",".$new_yundian_user_id.")";
					_mysql_query($insert_yundian_self_pro);
				}

				//平台对云店的抽成
				$new_remark = '';
				$select_yun_promoters = "select user_id,yundian_user_id,reward,card_member_id,remark,customer_id,level_name,own_user_name,paytype,red_pack_id,cityarea_id,commission_type,commission_score,level from weixin_commonshop_order_promoters where batchcode='".$batchcode."' and isvalid = true and type=27 and class = 91";
                $result_yun_promoters = _mysql_query($select_yun_promoters) or die("query_logs error  : ".mysql_error());
                while($row_yun_promoters = mysql_fetch_object($result_yun_promoters)){
					$new_user_id = $row_yun_promoters->user_id;
					$new_yundian_user_id = $row_yun_promoters->yundian_user_id;
					$new_card_member_id = $row_yun_promoters->card_member_id;
					//$new_remark = mysql_real_escape_string($row_yun_promoters->remark);
					$new_customer_id = $row_yun_promoters->customer_id;
					$new_level_name = mysql_real_escape_string($row_yun_promoters->level_name);
					//$new_own_user_name = mysql_real_escape_string($row_yun_promoters->own_user_name);
					$new_paytype = $row_yun_promoters->paytype;
					$new_red_pack_id = $row_yun_promoters->red_pack_id;
					$new_cityarea_id = $row_yun_promoters->cityarea_id;
					$new_commission_type = $row_yun_promoters->commission_type;
					$new_commission_score = $row_yun_promoters->commission_score;
					$new_level = $row_yun_promoters->level;
				}
					$new_remark = "退款后:商城抽取(".$new_own_user_name.")云店金额".$new_yun_reward."元,仅记录";
					$update_yundian_pro = "UPDATE weixin_commonshop_order_promoters SET isvalid = false WHERE batchcode='".$batchcode."' and type=27 and class = 91";
					_mysql_query($update_yundian_pro) or die('Update_yundian_self_pro failed:'.mysql_error());
				if( $new_yun_reward > 0 ){	
					$insert_yundian_pro = "insert into weixin_commonshop_order_promoters(user_id,reward,card_member_id,isvalid,createtime,remark,customer_id,level_name,own_user_name,batchcode,paytype,red_pack_id,level,type,class,cityarea_id,commission_type,commission_score,yundian_user_id) values(".$new_user_id.",".$new_yun_reward.",".$new_card_member_id.",true,now(),'".$new_remark."',".$new_customer_id.",'".$new_level_name."','商城',".$batchcode.",7,'".$new_red_pack_id."',".$new_level.",27,91,".$new_cityarea_id.",".$new_commission_type.",".$new_commission_score.",".$new_yundian_user_id.")";
					_mysql_query($insert_yundian_pro);
				}
			}
			/*云店自营订单 end*/
            $json["is_collageActivities"] = $is_collageActivities;
			$json["status"] = 0;
			$json["line"] = 1032;
			$json["msg"] = "编号：".$batchcode."，退款成功";
		}else{
			$json["status"] = 20002;
			$json["line"] = 677;
			$json["msg"] = "编号：".$batchcode."，已确认完成，请勿重复提交！";
		}
	break;

	case "confirmGoodRefund":  //确定收到退货(申请退货[退货/换货])

        $yundian_self       = 0;//是否云店自营订单
        $return_type       = 1;//1退货2换货
		$query_ord = "select yundian_self,return_type from weixin_commonshop_orders where batchcode='".$batchcode."' limit 1";
		//_file_put_contents("log/order_goodRefund_" . $today . ".txt", "\r\nquery_ord=======".var_export($query_ord,true)."\r\n",FILE_APPEND);
		$result_ord         = _mysql_query($query_ord) or die('Query_ord failed: ' . mysql_error());
		while ($row_ord     = mysql_fetch_object($result_ord)) {
			$return_type     = $row_ord->return_type;
            $yundian_self  = $row_ord->yundian_self;
		}

		$query_up_ord = "update weixin_commonshop_orders set return_status = 6 where isvalid = true and batchcode = '".$batchcode."'";
		//_file_put_contents("log/order_confirmGoodRefund_" . $today . ".txt", "\r\nquery_up_ord=======".var_export($query_up_ord,true)."\r\n",FILE_APPEND);
		_mysql_query($query_up_ord);

		if($yundian_self == 1) {
			$yundian_user_id_re = $_SESSION['user_id_'.$customer_id];

			$yundian_user_sql = "select weixin_fromuser from weixin_users where id='".$yundian_user_id_re."' ";
			$result_yundian_user = _mysql_query($yundian_user_sql)or die("Query_user error  : ".mysql_error());
			$yundian_fromuser = mysql_result($result_yundian_user,0,0);			
			$log_content = "云店店主已确认收到退货.";
			//添加订单日志
			$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
			values('".$batchcode."',14,'".$log_content."','".$yundian_fromuser."',now(),1)";			
		} else {
			$log_content = "平台已确认收到退货.";
			//添加订单日志
			$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
			values('".$batchcode."',14,'".$log_content."','".$log_username."',now(),1)";			
		}


		//_file_put_contents("log/order_confirmGoodRefund_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
		_mysql_query($query_log);

		$query_user = "select weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
		//_file_put_contents("log/order_confirmGoodRefund_" . $today . ".txt", "query_user=======".var_export($query_user,true)."\r\n",FILE_APPEND);
		$result_user = _mysql_query($query_user);
		$fromuser = mysql_result($result_user,0,0);

		$content = "编号：".$batchcode.",商家已确认收到您的退货!";

		if($yundian_self == 1) {
			if($return_type == 1) {
				$content = "您的订单：".$batchcode."的退货申请，店主已经确认收货了。。";
			} else {
				$content = "您的订单：".$batchcode."的换货申请，店主已经确认收货了。。";
			}
			
		}

		$shopmessage->SendMessage($content,$fromuser,$customer_id);

		$json["status"] = 0;
		$json["line"] = 1060;
		$json["msg"] = "编号：".$batchcode."，确认[收到退货]成功";
	break;

	case "confirmGoodAllRefund":  //确定 已退货且退款


		$account      				= $configutil->splash_new($_POST["totalprice"]);
		$remark      				= $configutil->splash_new($_POST["remark"]);
		(float)$refundSupplyMoney	= $configutil->splash_new($_POST["refundSupplyMoney"]);//退给供应商的金额
		(float)$refundGoodMoney_old	= $configutil->splash_new($_POST["refundGoodMoney_old"]);//订单金额

		$paystyle = '';//支付类型
		$block_chain_price = '';//区块链支付金额
		$return_account = 0;//退款金额
		$query_order = "select paystyle,block_chain_price,return_account from weixin_commonshop_orders where batchcode='".$batchcode."' limit 1";
		//_file_put_contents("log/order_goodRefund_" . $today . ".txt", "\r\nquery_ord=======".var_export($query_ord,true)."\r\n",FILE_APPEND);
		$result_order         = _mysql_query($query_order) or die('Query_order failed: ' . mysql_error());
		while ($row_order     = mysql_fetch_object($result_order)) {
			$paystyle          = $row_order->paystyle;
			$block_chain_price = $row_order->block_chain_price;
			$return_account = $row_order->return_account;
		}
		$block_chain_score = '区块链积分';
		$query_block_chain = "select name from ".WSY_SHOP.".block_chain_setting where customer_id='".$customer_id."'";
		$result_block_chain = _mysql_query($query_block_chain) or die('Query_block_chain failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result_block_chain)) {
		   $block_chain_score = $row->name;
		}
		$unit = '';//区块链积分自定义名称
		if($paystyle != '区块链积分支付'){
			$query_up_ord = "update weixin_commonshop_orders set return_status = 6,return_account = ".$account." where isvalid = true and batchcode = '".$batchcode."'";
	    }else{
	    	$unit = $block_chain_score;
	    	$query_up_ord = "update weixin_commonshop_orders set return_status = 6,return_account = ".$return_account." where isvalid = true and batchcode = '".$batchcode."'";
	    }
		//_file_put_contents("log/order_confirmGoodAllRefund_" . $today . ".txt", "\r\nquery_up_ord=======".var_export($query_up_ord,true)."\r\n",FILE_APPEND);
		_mysql_query($query_up_ord);

		$log_content = "平台已确认收到退货.";

		$log_content = $log_content. ",确认可退款金额为：".$account.$unit;
		if(!empty($remark)){
			$log_content = $log_content.",备注：".$remark;
		}

		//添加订单日志
		$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
			values('".$batchcode."',14,'".$log_content."','".$log_username."',now(),1)";
		//_file_put_contents("log/order_confirmGoodRefund_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
		_mysql_query($query_log);

		$query_user = "select weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
		//_file_put_contents("log/order_confirmGoodRefund_" . $today . ".txt", "query_user=======".var_export($query_user,true)."\r\n",FILE_APPEND);
		$result_user = _mysql_query($query_user);
		$fromuser = mysql_result($result_user,0,0);

		$content = "编号：".$batchcode.",商家已确认收到您的退货!";
		$content = $content."确认可退款金额为：".$account.$unit;
		if(!empty($remark)){
			$content = $content.",商家留言：".$remark;
		}

		$shopmessage->SendMessage($content,$fromuser,$customer_id);

		$supply_id			= -1;//供应商ID
		$query_ord = "select supply_id from weixin_commonshop_orders where batchcode='".$batchcode."' limit 1";
		$result_ord         = _mysql_query($query_ord) or die('Query_ord failed: ' . mysql_error());
		while ($row_ord     = mysql_fetch_object($result_ord)) {
			$supply_id      = $row_ord->supply_id;
		}


		if($supply_id>0 && $refundSupplyMoney>0){
			if($shopmessage->GetRefundSupply_Money($batchcode,$refundSupplyMoney,$supply_id)){//调用供应商退款方法
				//如果返回真，就发送消息给供应商
				$query_supply = "select weixin_fromuser from weixin_users where id= ".$supply_id." limit 0,1";
				$result_supply = _mysql_query($query_supply) or die("Query_query_supply error  : ".$supply_id.mysql_error());
				$fromuser_supply = "";
				if($row_supply = mysql_fetch_object($result_supply)){
					$fromuser_supply = $row_supply->weixin_fromuser;
				}
				$content = "订单：".$batchcode."\n商家已完成退货，\n账户余额增加：".$refundSupplyMoney."元，\n请注意查收。";
				$shopmessage->SendMessage($content,$fromuser_supply,$customer_id);
			}
		}


		$json["status"] = 0;
		$json["line"] = 1099;
		$json["msg"] = "编号：".$batchcode."，确认[收到退货且退款]成功";
	break;

	case "confirmAftersale":  //确认维权完毕
		//修改订单维权状态
		$query_up_status="update weixin_commonshop_orders set aftersale_state=4 where isvalid=true and aftersale_state=2 and batchcode='".$batchcode."' and customer_id=".$customer_id;
		//_file_put_contents("log/order_confirmAftersale_" . $today . ".txt", "\r\nquery_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
		_mysql_query($query_up_status);

		//添加订单日志
		$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
		values('".$batchcode."',20,'平台处理了用户的维权申请。','".$log_username."',now(),1)";
		//_file_put_contents("log/order_confirmAftersale_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
		_mysql_query($query_log);

		$query_user = "select weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
		//_file_put_contents("log/order_confirmGoodRefund_" . $today . ".txt", "query_user=======".var_export($query_user,true)."\r\n",FILE_APPEND);
		$result_user = _mysql_query($query_user);
		$fromuser = mysql_result($result_user,0,0);

		$content = "编号：".$batchcode.",商家已处理了您的售后申请!";

		$shopmessage->SendMessage($content,$fromuser,$customer_id);

		$json["status"] = 0;
		$json["line"] = 1187;
		$json["msg"] = "编号：".$batchcode."，确认维权操作提交成功";
	break;

	case "changPirce":  //改价
		(float)$totalprice =$configutil->splash_new($_POST["changePrice"]);

		//插入改价记录
		$query_up_cp = "update weixin_commonshop_changeprices set status=0 where isvalid=true and batchcode='".$batchcode."'";
		//_file_put_contents("log/order_ChangPrice_" . $today . ".txt", "\r\nquery_up_cp=======".var_export($query_up_cp,true)."\r\n",FILE_APPEND);
		_mysql_query($query_up_cp) or die('Query_up_cp failed: ' . mysql_error());

		$query_in_cp="insert into weixin_commonshop_changeprices(batchcode,totalprice,status,isvalid,createtime) values('".$batchcode."',".$totalprice.",1,true,now())";
		_mysql_query($query_in_cp) or die('Query_in_cp failed: ' . mysql_error());
		//_file_put_contents("log/order_ChangPrice_" . $today . ".txt", "query_in_cp=======".var_export($query_in_cp,true)."\r\n",FILE_APPEND);

		//添加订单日志
		$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
	values('".$batchcode."',3,'平台修改了订单的价格为：".$totalprice."元','".$log_username."',now(),1)";
		//_file_put_contents("log/order_ChangPrice_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
		_mysql_query($query_log);

		$query_user = "select weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
		//_file_put_contents("log/order_ChangPrice_" . $today . ".txt", "query_user=======".var_export($query_user,true)."\r\n",FILE_APPEND);
		$result_user = _mysql_query($query_user);
		$fromuser = mysql_result($result_user,0,0);

		$pid = -1;
		$pro_name = "";
		$query_orders = "select pid from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
		//_file_put_contents("log/order_ChangPrice_" . $today . ".txt", "\r\nquery_orders=======".var_export($query_orders,true)."\r\n",FILE_APPEND);
		$result_orders = _mysql_query($query_orders) or die('Query_orders failed: ' . mysql_error());
		while ($row_orders = mysql_fetch_object($result_orders)) {
			$pid = $row_orders->pid;

			//查询产品名称
			$query_product = "select name from weixin_commonshop_products where id='".$pid."'";
			//_file_put_contents("log/order_ChangPrice_" . $today . ".txt", "query_product=======".var_export($query_product,true)."\r\n",FILE_APPEND);
			$result_product = _mysql_query($query_product) or die('Query_product failed: ' . mysql_error());
			while ($row_product = mysql_fetch_object($result_product)) {
				$product_name = $row_product->name;
				$pro_name_one = "".$product_name."";
			}
			$pro_name .= $pro_name_one;

		}

		$content = "亲，您有一笔订单\n\n编号：".$batchcode."\n商品：".$pro_name."\n商家已修改了您的订单价格为：".$totalprice."元";

		$shopmessage->SendMessage($content,$fromuser,$customer_id);

		$json["status"] = 0;
		$json["line"] = 594;
		$json["msg"] = "编号：".$batchcode."，价格更改成功";
	break;

	case "callPay":  //催单
		(float)$totalprice =$configutil->splash_new($_POST["price"]);

		$query_user = "select id,weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
		//_file_put_contents("log/order_callPay_" . $today . ".txt", "\r\nquery_user=======".var_export($query_user,true)."\r\n",FILE_APPEND);
		$result_user = _mysql_query($query_user);
		$user_id = mysql_result($result_user,0,0);
		$fromuser = mysql_result($result_user,0,1);

		$pid = -1;
		$pro_name = "";
		$query_orders = "select pid from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
		//_file_put_contents("log/order_callPay_" . $today . ".txt", "\r\nquery_orders=======".var_export($query_orders,true)."\r\n",FILE_APPEND);
		$result_orders = _mysql_query($query_orders) or die('Query_orders failed: ' . mysql_error());
		while ($row_orders = mysql_fetch_object($result_orders)) {
			$pid = $row_orders->pid;

			//查询产品名称
			$query_product = "select name from weixin_commonshop_products where id='".$pid."'";
			//_file_put_contents("log/order_callPay_" . $today . ".txt", "query_product=======".var_export($query_product,true)."\r\n",FILE_APPEND);
			$result_product = _mysql_query($query_product) or die('Query_product failed: ' . mysql_error());
			while ($row_product = mysql_fetch_object($result_product)) {
				$product_name = $row_product->name;
				$pro_name_one = "".$product_name."";
			}
			$pro_name .= $pro_name_one;

		}

		$content = '亲，您有一笔订单【未支付】\n商品：'.$pro_name.'\n金额：'.$totalprice.'元\n时间：'.date( 'Y-m-d H:i:s').'\n\n<a href=\"http://'.$http_host.'/weixinpl/common_shop/jiushop/order_list.php?customer_id='.$customer_id_en.'&user_id='.$user_id.'&islist=1\">【立即支付】</a>';

		$shopmessage->SendMessage($content,$fromuser,$customer_id);

		$json["status"] = 0;
		$json["line"] = 594;
		$json["msg"] = "编号：".$batchcode."，催单成功";
	break;

	case "merchant_remark":
		$merchant_remark =$configutil->splash_new($_POST["content"]);

		$sql="update weixin_commonshop_orders set merchant_remark= '".$merchant_remark."' where isvalid=true and batchcode='".$batchcode."' and customer_id=".$customer_id;
		//echo sql;
		$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());

		$json["status"] = 0;
		$json["line"] = 594;
		$json["msg"] = "编号：".$batchcode."，备注成功";
	break;

	case "batchFinish": //批量完成
		$box_arr = $_POST["box_arr"];
		$box_arr = json_decode($box_arr,true);//json转数组

		for( $i=0; $i < count($box_arr); $i++ ){
			$batchcode_arr = $box_arr[$i];
			$f_batchcode   = $batchcode_arr[0];
			$f_totalprice  = $batchcode_arr[0];
			/* 订单属性 */
			//$agentcont_type = 0;

			$sendstatus     = 0;
			$order_status   = 0;
			$paystyle       = "";
			$user_id        = -1;
			$card_member_id = -1;

			$exp_user_id    = -1;
			$paytime        = "";
			$query_status   = "select sendstatus,status,card_member_id,paystyle,user_id,paytime,exp_user_id from weixin_commonshop_orders where batchcode='".$f_batchcode."' limit 1";
			$result_status = _mysql_query($query_status) or die('Query_status failed: ' . mysql_error());
			while ($row_status = mysql_fetch_object($result_status)) {
				$sendstatus     = $row_status->sendstatus;  //0:未发货；1：已发货;2:已收货;3.申请退货；4.已退货;5申请退款；6：已经退款
				$order_status   = $row_status->status;		//1:确认完成
				$card_member_id = $row_status->card_member_id;		//会员卡号

				$paystyle       = $row_status->paystyle;		//支付方式
				$user_id        = $row_status->user_id;		//用户编号
				$paytime        = $row_status->paytime;
				$exp_user_id    = $row_status->exp_user_id;
			}
			/* 订单属性 End */
			//echo $order_status."++++".$sendstatus;
			if( $order_status != 1 and ( $sendstatus == 2 or $sendstatus == 4 or $sendstatus == 6 ) ){
				//echo "++";
				/* 商城设置 */
				$isOpenPublicWelfare	=  0;
				$is_cashback			=  0;
				$is_shareholder			=  0;
				$is_team				=  0;
				$query_set ="select isOpenPublicWelfare,is_cashback,is_shareholder,is_team from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
				//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_set=======".var_export($query_set,true)."\r\n",FILE_APPEND);
				$result_set = _mysql_query($query_set) or die('Query_set failed: ' . mysql_error());
				while ($row_set = mysql_fetch_object($result_set)) {
					$isOpenPublicWelfare = $row_set->isOpenPublicWelfare;
					$is_cashback         = $row_set->is_cashback;
					$is_shareholder      = $row_set->is_shareholder;
					$is_team             = $row_set->is_team;
				}
				/* 商城设置 End */

				/* 更改推广员总vp值 start */

				// 查询此订单总vp值
				$total_vpscore=0;
				$query_reward="select total_vpscore from weixin_commonshop_order_prices where isvalid=true and batchcode='".$f_batchcode."' limit 0,1";
				$result_reward = _mysql_query($query_reward) or die('W758 Query failed: ' . mysql_error());
				while ($row_r = mysql_fetch_object($result_reward)) {
					$total_vpscore = $row_r->total_vpscore;
				}
				if( 0 < $total_vpscore ){

					/*vp值日志开始 更改日志状态已打入个人vp总账户*/
					$query_vp = "update weixin_commonshop_vp_logs set status=1 where batchcode='".$f_batchcode."'";
					_mysql_query($query_vp) or die('W766 Query failed: ' . mysql_error());
					/*vp值日志结束*/

					/* 更新个人VP总值 start */
					$vp_id       =  -1; //个人vp值id
					$query = "SELECT id from weixin_user_vp where isvalid=true and customer_id=" . $customer_id . " and user_id=" . $user_id . " limit 0,1";
					$result = _mysql_query($query) or die('W772 Query failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
						$vp_id  	 = $row->id;
					}
					if( 0 > $vp_id ){
						$query_vp="insert into weixin_user_vp(user_id,customer_id,my_vpscore,createtime,isvalid) values(".$user_id.",".$customer_id.",".$total_vpscore.",now(),true)";

						_mysql_query($query_vp) or die('W780 Query failed: ' . mysql_error());
					}else{
						$query_vp="update weixin_user_vp set my_vpscore=my_vpscore+".$total_vpscore." where isvalid=true and user_id=".$user_id." and customer_id=".$customer_id;

						_mysql_query($query_vp) or die('W780 Query failed: ' . mysql_error());
					}
					/* 更新个人VP总值 end */

				}
				/* 更改推广员总vp值  end */

				/* 更改订单状态 */
				$query_up_status="update weixin_commonshop_orders set status=1 where batchcode='".$f_batchcode."'";
				//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_up_status=======".var_export($query_up_status,true)."\r\n",FILE_APPEND);
				_mysql_query($query_up_status);

				$query_order__status="update weixin_commonshop_order_prices set status=1 where batchcode='".$f_batchcode."'";
				_mysql_query($query_order__status);
				/* 更改订单状态 End */

				if($sendstatus != 4 and $sendstatus != 6){

					//旧股东分红、区域团队版本结算方法调用
					if(strtotime($paytime) < strtotime(shareholder_team_bug_time)){
						if($is_shareholder==1){
							//_file_put_contents("log/order_confirm_" . $today . ".txt", "Confirm_GetMoney_shareholder_old=======".var_export($exp_user_id,true)."\r\n",FILE_APPEND);
							$shopmessage->Confirm_GetMoney_shareholder_old($f_batchcode,$customer_id,$exp_user_id);
						}
						if($is_team==1){
							//_file_put_contents("log/order_confirm_" . $today . ".txt", "Confirm_GetMoney_team_old=======".var_export($exp_user_id,true)."\r\n",FILE_APPEND);
							$shopmessage->Confirm_GetMoney_team_old($f_batchcode,$customer_id,$exp_user_id);
						}
					}
					//_file_put_contents("log/order_confirm_" . $today . ".txt", "1.f_batchcode=======".var_export($f_batchcode,true)."\r\n",FILE_APPEND);
					//_file_put_contents("log/order_confirm_" . $today . ".txt", "2.card_member_id=======".var_export($card_member_id,true)."\r\n",FILE_APPEND);
					//_file_put_contents("log/order_confirm_" . $today . ".txt", "3.totalprice=======".var_export($f_totalprice,true)."\r\n",FILE_APPEND);
					//_file_put_contents("log/order_confirm_" . $today . ".txt", "4.customer_id=======".var_export($customer_id,true)."\r\n",FILE_APPEND);
					//_file_put_contents("log/order_confirm_" . $today . ".txt", "5.paystyle=======".var_export($paystyle,true)."\r\n",FILE_APPEND);

					//推广员有效期续费
					$shopmessage->write_file_log('10、推广员有效期续费',2);
					$Promoter->settlementRenewOrderPromoter($user_id,$f_batchcode);

					/*** 确认消费返现 start***/
					$shopmessage->confirm_cashBack($customer_id,$f_batchcode);
					/*** 确认消费返现 end***/

					$shopmessage->Confirm_GetMoney_Agent($f_batchcode,$card_member_id,$f_totalprice,$customer_id,$paystyle,1);

					//增加团队订单数
					$shopmessage->Confirm_Team_order($f_batchcode);

					//慈善金额确认
					$shopmessage->Confirm_charitable($f_batchcode,$customer_id,$user_id);

					//全球分红分佣进入资金池
					$shopmessage->write_file_log('6、全球分红分佣进入资金池',2);
					$shopmessage->Confirm_Global($customer_id,$f_batchcode);

					//积分确认
					$shopmessage->write_file_log('7、积分确认',2);
					$model_integral->m_confirm_Integral(array('cust_id'=>$customer_id,'batchcode'=>$batchcode));


					//全球分红分佣进入资金池
					$global_reward=0;
					$id =-1;
					$global_sql = "SELECT reward FROM weixin_commonshop_order_promoters where customer_id=".$customer_id." AND batchcode='".$batchcode."' AND type=9";
					$global_res = _mysql_query($global_sql) or die('orderclass-938 Query failed: ' . mysql_error());
					while( $row = mysql_fetch_object($global_res) ){
						$global_reward = $row->reward;//查出多少钱要进入资金池
						if( $global_reward > 0 ){
							$sel_sql = "SELECT id FROM weixin_globalbonus_pool WHERE customer_id=".$customer_id;
							$sql_res = _mysql_query($sel_sql);
							while($info = mysql_fetch_object($sql_res)){
								$id = $info->id;
								if($id == -1){
									$ins_sql = "INSERT INTO weixin_globalbonus_pool(customer_id,isvalid,total_money) VALUES(".$customer_id.",true,".$global_reward.")";
									_mysql_query($ins_sql) or die('orderclass-960 Query failed: ' . mysql_error());
									//添加备注
									$remark = "订单号：".$batchcode."佣金：".$global_reward."元 进入资金池";
									$ins_log_sql = "INSERT INTO weixin_globalbonus_pool_log(customer_id,isvalid,batchcode,type,style,money,after_money,createtime,remark) VALUES(".$customer_id.",true,".$batchcode.",1,2,".$global_reward.",".$global_reward.",now(),'".$remark."')";
									_mysql_query($ins_log_sql)or die('orderclass-954 Query failed: ' . mysql_error());
								}else{
									$sel_sql = "SELECT total_money FROM weixin_globalbonus_pool where customer_id=".$customer_id;
									$res_sql = _mysql_query($sel_sql)or die('Query failed2: ' . mysql_error());
									while( $row = mysql_fetch_object($res_sql) ){
										$total_money = $row->total_money;//资金池当前金额
									}
									$after_money = $total_money+$global_reward;
									//添加备注
									$remark = "订单号：".$batchcode."佣金：".$global_reward."元 进入资金池";
									$ins_log_sql = "INSERT INTO weixin_globalbonus_pool_log(customer_id,isvalid,batchcode,type,style,money,after_money,createtime,remark) VALUES(".$customer_id.",true,".$batchcode.",1,2,".$global_reward.",".$after_money.",now(),'".$remark."')";
									_mysql_query($ins_log_sql)or die('orderclass-954 Query failed: ' . mysql_error());
									//将金额加入到资金池中
									$up_pool_sql = "UPDATE weixin_globalbonus_pool SET total_money=total_money+".$global_reward." WHERE customer_id=".$customer_id;
									_mysql_query($up_pool_sql)or die('orderclass-957 Query failed: ' . mysql_error());
								}
							}
						}
					}//--------------全球分红分佣进入资金池end

						/* 公益基金 */
					if($isOpenPublicWelfare==1){
						$valuepercent = 0;
						$publicwelfare=0;
						$query_pub = "select valuepercent,publicwelfare from weixin_commonshop_publicwelfare where isvalid=true and customer_id=".$customer_id." limit 1";
						//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_pub=======".var_export($query_pub,true)."\r\n",FILE_APPEND);
						$result_pub = _mysql_query($query_pub);
						while ($row_pub = mysql_fetch_object($result_pub)) {
							$valuepercent = $row_pub->valuepercent;    //比率
							$publicwelfare=$row_pub->publicwelfare;    //奖金池累计金额
						}

						/* 运费 */
						$express_price = 0;
						$query_express = "select price from weixin_commonshop_order_express_prices where isvalid=true and batchcode='".$f_batchcode."' limit 1";
						//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_express=======".var_export($query_express,true)."\r\n",FILE_APPEND);
						$result_express = _mysql_query($query_express);
						while ($row_express = mysql_fetch_object($result_express)) {
							$express_price = $row_express->price;
						}
						/* 运费 End */

						if($express_price>0){$f_totalprice=$f_totalprice-$express_price;}  //减去运费
						$welfare=round($f_totalprice*$valuepercent,2);

						$welfare_id = -1;
						$query_welfare="select id,before_score,add_score from weixin_commonshop_publicwelfare_log where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." order by id desc limit 0,1";
						//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_welfare=======".var_export($query_welfare,true)."\r\n",FILE_APPEND);
						$result_welfare = _mysql_query($query_welfare);
						while ($row_welfare = mysql_fetch_object($result_welfare)) {
							$welfare_id=$row_welfare->id;
							$before_score=$row_welfare->before_score;
							$add_score=$row_welfare->add_score;
						}

						//判断此用户是否曾经捐助过
						if($welfare_id>0){
							$new_before_score=$before_score+$add_score;
							$query_insert_welfare="insert into weixin_commonshop_publicwelfare_log(user_id,createtime,isvalid,customer_id,before_score,add_score,batchcode) values(".$user_id.",now(),true,".$customer_id.",".$new_before_score.",".$welfare.",".$f_batchcode.")";
						}else{
							$query_insert_welfare="insert into weixin_commonshop_publicwelfare_log(user_id,createtime,isvalid,customer_id,before_score,add_score,batchcode) values(".$user_id.",now(),true,".$customer_id.",0,".$welfare.",".$f_batchcode.")";
						}
						_mysql_query($query_insert_welfare);
						//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_insert_welfare=======".var_export($query_insert_welfare,true)."\r\n",FILE_APPEND);

						//累加至奖金池
						$new_publicwelfare=round($publicwelfare+$welfare,2);
						$query_up_public = "update weixin_commonshop_publicwelfare set publicwelfare=".$new_publicwelfare." where customer_id=".$customer_id;
						//_file_put_contents("log/order_confirm_" . $today . ".txt", "new_publicwelfare=======".var_export($new_publicwelfare,true)."\r\n",FILE_APPEND);
						_mysql_query($query_up_public);
					}

					//消费返现开关  /***** 改为支付成功后就消费返现，方法放在utility.php *****/
					/*if($is_cashback==1){
						$sum_cashback=0;
						$query_cash_order="select pid,rcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and batchcode=".$f_batchcode;
						//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_cash_order=======".var_export($query_cash_order,true)."\r\n",FILE_APPEND);
						$result_cash_order = _mysql_query($query_cash_order);
						while ($row_cash_order = mysql_fetch_object($result_cash_order)) {
							$pid=$row_cash_order->pid;
							$rcount=$row_cash_order->rcount;
							/* 查询返现 */
							/*$query_cashback="select cashback from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id." and id=".$pid;
							//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_cashback=======".var_export($query_cashback,true)."\r\n",FILE_APPEND);
							$result_cashback = _mysql_query($query_cashback);
							while ($row_cashback = mysql_fetch_object($result_cashback)) {
								$cashback=$row_cashback->cashback;
							}
							$sum = $cashback*$rcount;
							$sum_cashback += $sum;
						}

						if($sum_cashback>0){
							/* 插入返现记录 */
							/*$query_cash_insert="insert into cashback(customer_id,user_id,isvalid,createtime,batchcode,cashback,rest_cashback) values(".$customer_id.",".$user_id.",true,now(),".$f_batchcode.",".$sum_cashback.",".$sum_cashback.")";
							//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_cash_insert=======".var_export($query_cash_insert,true)."\r\n",FILE_APPEND);
							_mysql_query($query_cash_insert);
						}
					}*/
				} else {
					$extend_id 	  = -1;	//
					$extend_money = 0;	//推广奖励金额
					$query_money = "select id,money from weixin_commonshop_extend_logs where batchcode='".$f_batchcode."' and isvalid=true";
					$result_money = _mysql_query($query_money) or die('Query_money failed:'.mysql_error());
					while( $row_money = mysql_fetch_object($result_money) ){
						$extend_id 	  = $row_money->id;
						$extend_money = $row_money->money;
					}
					if( $extend_id > 0 ){	//有推广奖励记录
						//退货订单则删除推广奖励记录
						$shopmessage->write_file_log('9、推广奖励',2);
						$query_extend = "update weixin_commonshop_extend_logs set isvalid=false where batchcode='".$f_batchcode."' and isvalid=true";
						_mysql_query($query_extend) or die('Query_extend failed:'.mysql_error());

						$weixin_name 	 = '佚名';	//微信名
						$weixin_fromuser = '';		//微信唯一标识符
						$query_exp_user_id = "select weixin_name,weixin_fromuser from weixin_users where id=".$exp_user_id." and isvalid=true";
						$result_exp_user_id = _mysql_query($query_exp_user_id) or die('Query_exp_user_id failed:'.mysql_error());
						while( $row_exp_user_id = mysql_fetch_object($result_exp_user_id) ){
							$weixin_name 	 = mysql_real_escape_string($row_exp_user_id->weixin_name);
							$weixin_fromuser = $row_exp_user_id->weixin_fromuser;
						}

						$msg_extend = "亲，您的佣金 -".$extend_money."\r\n".
										"来源：【订单退货】\n".
										"顾客：".$weixin_name."\n".
										"备注：【推广奖励】\n".
										"时间：".date( "Y-m-d H:i:s")."";

						$shopmessage->SendMessage($msg_extend,$weixin_fromuser,$customer_id);	//发送信息
					}

					//撤销推广员有效期续费订单
					$query_renewal_order = "UPDATE promoter_renewal_orders SET status=2 WHERE batchcode='".$f_batchcode."' AND customer_id=".$customer_id." AND status=0";
					_mysql_query($query_renewal_order) or die('Query_renewal_order failed:'.mysql_error());
				}

				//统计每笔订单实际支付的金额和购物币
				$sql = "select batchcode,price,pay_currency,origin_price,paystyle,cardDiscount from weixin_commonshop_order_prices where isvalid=true and batchcode='".$batchcode."'";
		        $result_str = _mysql_query($sql) or die('Query failed_opr_198: ' . mysql_error());
		        while($row = mysql_fetch_object($result_str)){
		            $order_prices_arr[$k]['currency']   = $row->pay_currency;
		            $order_prices_arr[$k]['origin_price']   = $row->origin_price;
		            $order_prices_arr[$k]['cardDiscount']   = $row->cardDiscount;   //仅判断有无打折扣      0：无，1：有

		            $sql_cprice = "select totalprice from weixin_commonshop_changeprices where status=1 and isvalid=1 and batchcode='".$batchcode."' order by id desc limit 1";
		            $result_cp = _mysql_query($sql_cprice) or die('Query sql_changeprice failed: ' . mysql_error());
		            if ($row_cp = mysql_fetch_object($result_cp)) {
		                $order_prices_arr[$k]['totalprice'] = $row_cp->totalprice;
		            }else{
		                $query2 = "select price from weixin_commonshop_order_prices where isvalid=true and batchcode='".$batchcode."'";
		                $result2 = _mysql_query($query2) or die('Query failed_opr_188: ' . mysql_error());
		                while ($row2 = mysql_fetch_object($result2)) {
		                    //获取订单的真实价格（可能是折扣总价）
		                    $order_prices_arr[$k]['totalprice']   = $row2->price;
		                }
		            }
		            $k++;
		        }
		        //统计每笔订单实际支付的金额和购物币

				//给顾客增加消费积分奖励.1 表示为商城消费
				if( $sendstatus == 2 ){
					$shopmessage->AddScore_level($card_member_id,$f_totalprice,1,$paystyle,$f_batchcode,0,$order_prices_arr);
				}

				//添加订单操作日志
				$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
					values('".$f_batchcode."',16,'平台已确认订单完成','".$log_username."',now(),1)";
				//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
				_mysql_query($query_log);
						//判断此用户是否曾经捐助过
						if($welfare_id>0){
							$new_before_score=$before_score+$add_score;
							$query_insert_welfare="insert into weixin_commonshop_publicwelfare_log(user_id,createtime,isvalid,customer_id,before_score,add_score,batchcode) values(".$user_id.",now(),true,".$customer_id.",".$new_before_score.",".$welfare.",".$f_batchcode.")";
						}else{
							$query_insert_welfare="insert into weixin_commonshop_publicwelfare_log(user_id,createtime,isvalid,customer_id,before_score,add_score,batchcode) values(".$user_id.",now(),true,".$customer_id.",0,".$welfare.",".$f_batchcode.")";
						}
						_mysql_query($query_insert_welfare);
						//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_insert_welfare=======".var_export($query_insert_welfare,true)."\r\n",FILE_APPEND);

						//累加至奖金池
						$new_publicwelfare=round($publicwelfare+$welfare,2);
						$query_up_public = "update weixin_commonshop_publicwelfare set publicwelfare=".$new_publicwelfare." where customer_id=".$customer_id;
						//_file_put_contents("log/order_confirm_" . $today . ".txt", "new_publicwelfare=======".var_export($new_publicwelfare,true)."\r\n",FILE_APPEND);
						_mysql_query($query_up_public);
					}

					//消费返现开关
					/* if($is_cashback==1){
						$sum_cashback=0;
						$query_cash_order="select pid,rcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and batchcode=".$f_batchcode;
						//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_cash_order=======".var_export($query_cash_order,true)."\r\n",FILE_APPEND);
						$result_cash_order = _mysql_query($query_cash_order);
						while ($row_cash_order = mysql_fetch_object($result_cash_order)) {
							$pid=$row_cash_order->pid;
							$rcount=$row_cash_order->rcount;
							//查询返现
							$query_cashback="select cashback from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id." and id=".$pid;
							//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_cashback=======".var_export($query_cashback,true)."\r\n",FILE_APPEND);
							$result_cashback = _mysql_query($query_cashback);
							while ($row_cashback = mysql_fetch_object($result_cashback)) {
								$cashback=$row_cashback->cashback;
							}
							$sum = $cashback*$rcount;
							$sum_cashback += $sum;
						}

						if($sum_cashback>0){
							//插入返现记录
							$query_cash_insert="insert into cashback(customer_id,user_id,isvalid,createtime,batchcode,cashback,rest_cashback) values(".$customer_id.",".$user_id.",true,now(),".$f_batchcode.",".$sum_cashback.",".$sum_cashback.")";
							//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_cash_insert=======".var_export($query_cash_insert,true)."\r\n",FILE_APPEND);
							_mysql_query($query_cash_insert);
						}
					} */
				}

				//给顾客增加消费积分奖励.1 表示为商城消费
				if( $sendstatus == 2 ){
					$shopmessage->AddScore_level($card_member_id,$f_totalprice,1,$paystyle,$f_batchcode,0,$order_prices_arr);
				}

				//添加订单操作日志
				$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
					values('".$f_batchcode."',16,'平台已确认订单完成','".$log_username."',now(),1)";
				//_file_put_contents("log/order_confirm_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
				_mysql_query($query_log);

				$json["status"] = 0;
				$json["line"] = 820;
				$json["msg"] = "批量确认完成";
	break;

	case "reducesupplymoney":  //扣除供应商款项

		(float)$reducemoney	= $configutil->splash_new($_POST["reducemoney"]);//退给供应商的金额

		$supply_id			= -1;//供应商ID
		$isreducesupply		= true;//判断是否完成维权扣除供应商款项
		$query_ord = "select supply_id,isreducesupply from weixin_commonshop_orders where batchcode='".$batchcode."' limit 1";
		$result_ord         = _mysql_query($query_ord) or die('Query_ord failed: ' . mysql_error());
		while ($row_ord     = mysql_fetch_object($result_ord)) {
			$supply_id      = $row_ord->supply_id;
			$isreducesupply = $row_ord->isreducesupply;
		}


		if($supply_id>0 && $isreducesupply==false){
			if($shopmessage->GetReduceSupply_Money($batchcode,$reducemoney,$supply_id)){//调用供应商退款方法

				$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) values('".$batchcode."',23,'维权扣除合作商款项','".$log_username."',now(),1)";
				_mysql_query($query_log);

				$query_reduce = "update weixin_commonshop_orders set isreducesupply=true where isvalid=true and isreducesupply=false  and batchcode='".$batchcode."' and supply_id=".$supply_id."";
				_mysql_query($query_reduce);

				//如果返回真，就发送消息给供应商
				$query_supply = "select weixin_fromuser from weixin_users where id= ".$supply_id." limit 0,1";
				$result_supply = _mysql_query($query_supply) or die("Query_query_supply error  : ".$supply_id.mysql_error());
				$fromuser_supply = "";
				if($row_supply = mysql_fetch_object($result_supply)){
					$fromuser_supply = $row_supply->weixin_fromuser;
				}
				$content = "订单：".$batchcode."\n已完成维权商家扣除您账户余额，\n账户余额减少：".$reducemoney."元，\n请注意查看。";
				$shopmessage->SendMessage($content,$fromuser_supply,$customer_id);
			}
			$json["status"] = 0;
			$json["line"] = 1099;
			$json["msg"] = "编号：".$batchcode."，扣除合作商款项成功";
		}else{
			$json["msg"] = "编号：".$batchcode."，操作失败";
		}



	break;

	case "attribution":
		$type = -1;
		$isAgent = -1;
		if(!empty($_POST['type'])){
			$type = $configutil->splash_new($_POST["type"]);
		}
		switch($type){
			case 1:
				$isAgent = 3;
			break;
			case 2:
				$isAgent = 1;
			break;
		}
		$tmp   = array();
		$sup_user_id     ="";
		$sup_name        ="";
		$sup_weixin_name ="";
		$sup_userName    ="";
		$query_prom = "
		SELECT pro.user_id,users.name,users.weixin_name
		FROM promoters as pro
		LEFT JOIN weixin_users as users on pro.user_id = users.id
		WHERE pro.isAgent = ".$isAgent." AND pro.customer_id = ".$customer_id;
		$result_prom = _mysql_query($query_prom) or die('Query_prom failed: ' . mysql_error());
		while ($row_prom = mysql_fetch_object($result_prom)) {

			$sup_user_id     = $row_prom->user_id;
			$sup_name        = $row_prom->name;
			$sup_weixin_name = mysql_real_escape_string($row_prom->weixin_name);

			$sup_userName    =	$sup_name;

			if(!empty($sup_weixin_name)){ $sup_userName .= "(". $sup_weixin_name . ")"; }
			$array = array(
				"sup_user_id"=>$sup_user_id,
				"sup_name"=>$sup_name,
				"sup_weixin_name"=>$sup_weixin_name,
				"sup_userName"=>$sup_userName
			);
			array_push($tmp,$array);
		}
		echo json_encode($tmp);
		die;
		break;

	case 'deletePromoter':	//删除推广员身份
		$user_id = $configutil->splash_new($_POST["user_id"]);

        $json = $Promoter->deletePromoter($user_id,0);

	break;

	case 'again_comfirm':
		$batchcode = $configutil->splash_new($_POST["batchcode"]);
		$id_new = $configutil->splash_new($_POST["id_new"]);
		$class = $configutil->splash_new($_POST["class"]);
		$type = $configutil->splash_new($_POST["type"]);

		/*事务开始*/
		_tran_start();
		$json = $shopmessage->Again_Confirm_GetMoney_Agent($batchcode,$id_new,$customer_id,$class,$type);
		/*事务提交*/
		_tran_commit();
	break;
    
    case 'yundian_confirm_all':
        //查询所有待完成云店自营订单
        $sql = "SELECT batchcode,totalprice FROM weixin_commonshop_orders where sendstatus = 2 and paystatus and status = 0 and aftersale_state = 0 and yundian_id >0 and yundian_self = 1";

        $res_all = _mysql_query($sql) or die('Query_all failed: ' . mysql_error());
        $yundian_order = array();
        while($row = mysql_fetch_assoc($res_all)){
            $yundian_order[] = $row;
        }
        if($yundian_order) {
            foreach ($yundian_order as $v) {
                /*事务开始*/
                $batchcode = $v['batchcode'];

                _tran_start();
                $shopmessage->write_file_log(date('Y_m_d H:i:s', time()) . '确认订单!订单号:' . $batchcode);
                $totalprice = $v["totalprice"];
                /* 订单属性 */
                $agentcont_type = 0;

                $sendstatus = 0;
                $order_status = 0;
                $paystyle = "";
                $user_id = -1;
                $card_member_id = -1;

                $exp_user_id = -1;
                $paytime = "";
                $is_sendorder = false;
                $pay_batchcode = "";
                $orderTime = "";
                $customer_id = "";
                $query_status = "select sendstatus,status,card_member_id,paystyle,user_id,paytime,exp_user_id,is_sendorder,is_pay_on_delivery,is_sign,pay_batchcode,createtime,customer_id from weixin_commonshop_orders where batchcode='" . $batchcode . "' limit 1";
                //_file_put_contents("log/order_confirm_" . $today . ".txt", "\r\nquery_status=======".var_export($query_status,true)."\r\n",FILE_APPEND);
                $result_status = _mysql_query($query_status) or die('Query_status failed: ' . mysql_error());
                while ($row_status = mysql_fetch_object($result_status)) {

                    $sendstatus = $row_status->sendstatus;  //0:未发货；1：已发货;2:已收货;3.申请退货；4.已退货;5申请退款；6：已经退款
                    $order_status = $row_status->status;        //1:确认完成
                    $card_member_id = $row_status->card_member_id;        //会员卡号

                    $paystyle = $row_status->paystyle;        //支付方式,中文
                    $user_id = $row_status->user_id;        //用户编号
                    $paytime = $row_status->paytime;
                    $exp_user_id = $row_status->exp_user_id;
                    $is_sendorder = $row_status->is_sendorder;
                    $is_payondelivery = $row_status->is_pay_on_delivery;
                    $is_sign = $row_status->is_sign;
                    $pay_batchcode = $row_status->pay_batchcode;
                    $orderTime = $row_status->createtime;
                    $customer_id = $row_status->customer_id;
                    break;
                }
                /* 订单属性 End */
                if ($sendstatus != 2 and $sendstatus != 4 and $sendstatus != 6) {
                    $json["status"] = 20001;
                    $json["line"] = 673;
                    $json["msg"] = "编号：" . $batchcode . "，无法确认订单，请检查订单状态！";
                } elseif ($order_status == 1) {
                    $json["status"] = 20002;
                    $json["line"] = 677;
                    $json["msg"] = "编号：" . $batchcode . "，已确认完成，请勿重复提交！";
                } else {

                    /* 商城设置 */
                    $isOpenPublicWelfare = 0;
                    $is_cashback = 0;
                    $is_shareholder = 0;
                    $is_team = 0;
                    $query_set = "select isOpenPublicWelfare,is_cashback,is_shareholder,is_team from weixin_commonshops where isvalid=true and customer_id=" . $customer_id;
                    //_file_put_contents("log/order_confirm_" . $today . ".txt", "query_set=======".var_export($query_set,true)."\r\n",FILE_APPEND);
                    $result_set = _mysql_query($query_set) or die('Query_set failed: ' . mysql_error());
                    while ($row_set = mysql_fetch_object($result_set)) {
                        $isOpenPublicWelfare = $row_set->isOpenPublicWelfare;
                        $is_cashback = $row_set->is_cashback;
                        $is_shareholder = $row_set->is_shareholder;
                        $is_team = $row_set->is_team;
                    }
                    /* 商城设置 End */

                    $ordersta = 1;
                    if ($is_payondelivery == 1 && $is_sign == 0) {
                        $ordersta = 0;
                    }

                    //健康钱包确认支付后收款方才能收到钱，在订单确认完成增加发起确认支付 20170809
                    if ($paystyle == "健康钱包支付" && $sendstatus != 4 && $sendstatus != 6) { //不是已退款或已退货
                        //获取参数
                        $orderTime = datetimeToNewFormat($orderTime);
                        $api_obj = new HealthpayApi($customer_id);
                        $sourceId = $api_obj->sourceId;//sourceId收款平台账号
                        $md5Key = $api_obj->md5Key; //收款平台密钥
                        $parameter = array(
                            "orderId" => $pay_batchcode,
                            "orderTime" => $orderTime,
                            "sourceId" => $sourceId
                        );
                        $healthPaySubmit = new HealthPaySubmit();
                        $sign = $healthPaySubmit->getSign($parameter, $md5Key); //获取签名
                        $parameter["sign"] = $sign;

                        //发起确认支付
                        $res = $api_obj->do_confirm_pay($parameter);
                        //var_dump($res);

                        //根据结果进行处理 {"retCode":"0000","retMessage":"支付订单确认成功"}
                        if ($res["retCode"] == "0000") { //支付订单确认成功,使用原来的业务
                            $shopmessage->write_file_log('1、更改订单状态', 2);
                            /* 更改订单状态 */
                            $query_up_status = "update weixin_commonshop_orders set status=$ordersta,is_receipt=" . $is_receipt . ", confirm_order_time=now() where batchcode='" . $batchcode . "'";

                            //_file_put_contents("log/order_confirm_" . $today . ".txt", "query_up_status=======".var_export($query_up_status,true)."\r\n",FILE_APPEND);
                            _mysql_query($query_up_status);

                            //货到付款前端确认收货状态不能修改

                            $sql = "update weixin_commonshop_order_prices set status=1 where isvalid=true and batchcode='" . $batchcode . "'";
                            _mysql_query($sql) or die('9_1 Query failed: ' . mysql_error());

                            /* 更改订单状态 End */
                        } else { //支付订单确认失败
                            $json["status"] = 30002;
                            $json["line"] = $res["retCode"];
                            $json["msg"] = "订单号：" . $batchcode . "确认支付失败！";
                            $jsons = json_encode($json);
                            die($jsons);
                        }
                    } else { //使用原来的业务
                        $shopmessage->write_file_log('1、更改订单状态', 2);
                        /* 更改订单状态 */
                        $query_up_status = "update weixin_commonshop_orders set status=$ordersta,is_receipt=0, confirm_order_time=now() where batchcode='" . $batchcode . "'";

                        //_file_put_contents("log/order_confirm_" . $today . ".txt", "query_up_status=======".var_export($query_up_status,true)."\r\n",FILE_APPEND);
                        _mysql_query($query_up_status);

                        //货到付款前端确认收货状态不能修改

                        $sql = "update weixin_commonshop_order_prices set status=1 where isvalid=true and batchcode='" . $batchcode . "'";
                        _mysql_query($sql) or die('9_1 Query failed: ' . mysql_error());

                        /* 更改订单状态 End */
                    }

                    if ($sendstatus != 4 and $sendstatus != 6) {

                        //旧股东分红、区域团队版本结算方法调用
                        if (false) {
                            if (strtotime($paytime) < strtotime(shareholder_team_bug_time)) {
                                if ($is_shareholder == 1) {
                                    //_file_put_contents("log/order_confirm_" . $today . ".txt", "Confirm_GetMoney_shareholder_old=======".var_export($exp_user_id,true)."\r\n",FILE_APPEND);
                                    $shopmessage->Confirm_GetMoney_shareholder_old($batchcode, $customer_id, $exp_user_id);
                                }
                                if ($is_team == 1) {
                                    //_file_put_contents("log/order_confirm_" . $today . ".txt", "Confirm_GetMoney_team_old=======".var_export($exp_user_id,true)."\r\n",FILE_APPEND);
                                    $shopmessage->Confirm_GetMoney_team_old($batchcode, $customer_id, $exp_user_id);
                                }
                            }
                        }

                        $shopmessage->write_file_log("20、货到付款?{$is_payondelivery}==签收?{$is_sign}", 2);
                        if ($is_payondelivery != 1 || $is_sign == 1) {    //echo 123456789;
                            //推广员有效期续费
                            $shopmessage->write_file_log('10、推广员有效期续费', 2);
                            $Promoter->settlementRenewOrderPromoter($user_id, $batchcode);

                            $shopmessage->write_file_log('2、确认消费返现', 2);
                            /*** 确认消费返现 start***/
                            $shopmessage->confirm_cashBack($customer_id, $batchcode);
                            /*** 确认消费返现 end***/

                            $shopmessage->write_file_log('3、分佣', 2);
                            $shopmessage->Confirm_GetMoney_Agent($batchcode, $card_member_id, $totalprice, $customer_id, $paystyle);

                            //增加团队订单数
                            $shopmessage->write_file_log('4、增加团队订单数', 2);
                            $shopmessage->Confirm_Team_order($batchcode);

                            //慈善金额确认
                            $shopmessage->write_file_log('5、慈善金额确认', 2);
                            $shopmessage->Confirm_charitable($batchcode, $customer_id, $user_id);

                            //全球分红分佣进入资金池
                            $shopmessage->write_file_log('6、全球分红分佣进入资金池', 2);
                            $shopmessage->Confirm_Global($customer_id, $batchcode);

                            $model_integral = new model_integral();
                            //积分确认
                            $shopmessage->write_file_log('7、积分确认', 2);
                            $model_integral->m_confirm_Integral(array('cust_id' => $customer_id, 'batchcode' => $batchcode));

                            $shopmessage->write_file_log('7.1、积分确认', 2);
                        }

                        /* 更改推广员总vp值 start */
                        // 查询商城VP设置
                        $shopmessage->write_file_log('更改推广员总vp值', 2);
                        $isvp_switch = 0; //vp开关 1:开 0:关
                        $query_s = "select isvp_switch from weixin_commonshop_vp_bases where isvalid=true and customer_id=" . $customer_id . " limit 0,1";
                        $result_s = _mysql_query($query_s) or die('W758 Query failed: ' . mysql_error());
                        while ($row_s = mysql_fetch_object($result_s)) {
                            $isvp_switch = $row_s->isvp_switch;
                        }
                        if ($isvp_switch == 1) {
                            // 查询此订单总vp值
                            $total_vpscore = 0;
                            $query_reward = "select total_vpscore from weixin_commonshop_order_prices where isvalid=true and batchcode='" . $batchcode . "' limit 0,1";
                            $result_reward = _mysql_query($query_reward) or die('W758 Query failed: ' . mysql_error());
                            while ($row_r = mysql_fetch_object($result_reward)) {
                                $total_vpscore = $row_r->total_vpscore;
                            }
                            if (0 < $total_vpscore) {
                                $shopmessage->write_file_log('7、更改推广员总vp值', 2);
                                /*vp值日志开始 更改日志状态已打入个人vp总账户*/
                                $shopmessage->Confirm_vp($batchcode, $customer_id, $user_id, $total_vpscore);
                                /* 更新个人VP总值 end */

                            }
                        }
                        /* 更改推广员总vp值  end */
                        $shopmessage->write_file_log('公益基金', 2);
                        /* 公益基金 */
                        if ($isOpenPublicWelfare == 1) {
                            $valuepercent = 0;
                            $publicwelfare = 0;
                            $query_pub = "select valuepercent,publicwelfare from weixin_commonshop_publicwelfare where isvalid=true and customer_id=" . $customer_id . " limit 1";
                            //_file_put_contents("log/order_confirm_" . $today . ".txt", "query_pub=======".var_export($query_pub,true)."\r\n",FILE_APPEND);
                            $result_pub = _mysql_query($query_pub);
                            while ($row_pub = mysql_fetch_object($result_pub)) {
                                $valuepercent = $row_pub->valuepercent;    //比率
                                $publicwelfare = $row_pub->publicwelfare;    //奖金池累计金额
                            }


                            /* 运费 */
                            $express_price = 0;
                            $query_express = "select price from weixin_commonshop_order_express_prices where isvalid=true and batchcode='" . $batchcode . "' limit 1";
                            //_file_put_contents("log/order_confirm_" . $today . ".txt", "query_express=======".var_export($query_express,true)."\r\n",FILE_APPEND);
                            $result_express = _mysql_query($query_express);
                            while ($row_express = mysql_fetch_object($result_express)) {
                                $express_price = $row_express->price;
                            }
                            /* 运费 End */

                            if ($express_price > 0) {
                                $totalprice = $totalprice - $express_price;
                            }  //减去运费
                            $welfare = round($totalprice * $valuepercent, 2);


                            $welfare_id = -1;
                            $query_welfare = "select id,before_score,add_score from weixin_commonshop_publicwelfare_log where isvalid=true and customer_id=" . $customer_id . " and user_id=" . $user_id . " order by id desc limit 0,1";
                            //_file_put_contents("log/order_confirm_" . $today . ".txt", "query_welfare=======".var_export($query_welfare,true)."\r\n",FILE_APPEND);
                            $result_welfare = _mysql_query($query_welfare);
                            while ($row_welfare = mysql_fetch_object($result_welfare)) {
                                $welfare_id = $row_welfare->id;
                                $before_score = $row_welfare->before_score;
                                $add_score = $row_welfare->add_score;
                            }

                            $shopmessage->write_file_log('8、更新公益基金', 2);
                            //判断此用户是否曾经捐助过
                            if ($welfare_id > 0) {
                                $new_before_score = $before_score + $add_score;
                                $shopmessage->write_file_log('A、添加公益基金记录', 3);
                                $query_insert_welfare = "insert into weixin_commonshop_publicwelfare_log(user_id,createtime,isvalid,customer_id,before_score,add_score,batchcode) values(" . $user_id . ",now(),true," . $customer_id . "," . $new_before_score . "," . $welfare . "," . $batchcode . ")";
                            } else {
                                $shopmessage->write_file_log('A、添加公益基金记录', 3);
                                $query_insert_welfare = "insert into weixin_commonshop_publicwelfare_log(user_id,createtime,isvalid,customer_id,before_score,add_score,batchcode) values(" . $user_id . ",now(),true," . $customer_id . ",0," . $welfare . "," . $batchcode . ")";
                            }

                            _mysql_query($query_insert_welfare);
                            //_file_put_contents("log/order_confirm_" . $today . ".txt", "query_insert_welfare=======".var_export($query_insert_welfare,true)."\r\n",FILE_APPEND);


                            //累加至奖金池
                            $shopmessage->write_file_log('B、累加至奖金池', 3);
                            $new_publicwelfare = round($publicwelfare + $welfare, 2);
                            $query_up_public = "update weixin_commonshop_publicwelfare set publicwelfare=" . $new_publicwelfare . " where customer_id=" . $customer_id;
                            //_file_put_contents("log/order_confirm_" . $today . ".txt", "new_publicwelfare=======".var_export($new_publicwelfare,true)."\r\n",FILE_APPEND);
                            _mysql_query($query_up_public);
                        }

                    } else {
                        $extend_id = -1;    //
                        $extend_money = 0;    //推广奖励金额
                        $query_money = "select id,money from weixin_commonshop_extend_logs where batchcode='" . $batchcode . "' and isvalid=true";
                        $result_money = _mysql_query($query_money) or die('Query_money failed:' . mysql_error());
                        while ($row_money = mysql_fetch_object($result_money)) {
                            $extend_id = $row_money->id;
                            $extend_money = $row_money->money;
                        }
                        if ($extend_id > 0) {    //有推广奖励记录
                            //退货订单则删除推广奖励记录
                            $shopmessage->write_file_log('9、推广奖励', 2);
                            $query_extend = "update weixin_commonshop_extend_logs set isvalid=false where batchcode='" . $batchcode . "' and isvalid=true";
                            _mysql_query($query_extend) or die('Query_extend failed:' . mysql_error());

                            $weixin_name = '佚名';    //微信名
                            $weixin_fromuser = '';        //微信唯一标识符
                            $query_exp_user_id = "select weixin_name,weixin_fromuser from weixin_users where id=" . $exp_user_id . " and isvalid=true";
                            $result_exp_user_id = _mysql_query($query_exp_user_id) or die('Query_exp_user_id failed:' . mysql_error());
                            while ($row_exp_user_id = mysql_fetch_object($result_exp_user_id)) {
                                $weixin_name = mysql_real_escape_string($row_exp_user_id->weixin_name);
                                $weixin_fromuser = $row_exp_user_id->weixin_fromuser;
                            }

                            $msg_extend = "亲，您的佣金 -" . $extend_money . "\r\n" .
                                "来源：【订单退货】\n" .
                                "顾客：" . $weixin_name . "\n" .
                                "备注：【推广奖励】\n" .
                                "时间：" . date("Y-m-d H:i:s") . "";

                            $shopmessage->SendMessage($msg_extend, $weixin_fromuser, $customer_id);    //发送信息
                        }

                        //撤销推广员有效期续费订单
                        $query_renewal_order = "UPDATE promoter_renewal_orders SET status=2 WHERE batchcode='" . $batchcode . "' AND customer_id=" . $customer_id . " AND status=0";
                        _mysql_query($query_renewal_order) or die('Query_renewal_order failed:' . mysql_error());
                    }

					

                    //给顾客增加消费积分奖励.1 表示为商城消费
                    /*if( $sendstatus == 2 ){
                        $shopmessage->AddScore_level($card_member_id,$totalprice,1,$paystyle,$batchcode,0,$order_prices_arr);
                    }*/

                    //添加订单操作日志
                    $shopmessage->write_file_log('10、添加订单操作日志', 2);
                    $query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
                    values('" . $batchcode . "',16,'平台已确认订单完成','" . $log_username . "',now(),1)";
                    //_file_put_contents("log/order_confirm_" . $today . ".txt", "query_log=======".var_export($query_log,true)."\r\n",FILE_APPEND);
                    _mysql_query($query_log);

                    $json["status"] = 0;
                    $json["line"] = 820;
                    $json["msg"] .= "编号：" . $batchcode . "，确认完成";
                }

                /*事务提交*/
                _tran_commit();

                /* 订货系统结算方法 */
                $shopmessage->write_file_log('is_sendorder=====' . $is_sendorder, 2);
                if ($is_sendorder == true) {
                    $sql_query = "select id , send_type from system_send_order where order_id = '" . $batchcode . "' and isvalid = true and is_accept = true  limit 1";
                    $sso_id = 0;
                    $send_type = 0;
                    $sso_res = _mysql_query($sql_query);
                    if ($row_sso = mysql_fetch_object($sso_res)) {
                        $id = $row_sso->id;
                        $send_type = $row_sso->send_type;
                    }
                    $shopmessage->write_file_log('send_type=====' . $send_type, 2);
                    if ($send_type == 1) { // 订货系统
                        $shopmessage->write_file_log('11、分仓派单结算', 2);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, Protocol . "" . $_SERVER['HTTP_HOST'] . "/addons/index.php/ordering_retail/Ordering_Service/settle_shop_order?customer_id=" . $customer_id . "&user_id=" . $user_id . "&batchcode=" . $batchcode);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        if (Protocol == "https://") {
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                        }
                        $output = curl_exec($ch);
                        file_put_contents($zlog_name, "$batchcode 结果：" . $output, FILE_APPEND);
                        $or_result = json_decode($output);
                        if ($or_result->status != 1) {
                            $shopmessage->write_file_log('11、分仓派单结算 - 异常 ： ' . var_export($output, true), 2);
                        }
                        curl_close($ch);

                    } else if ($send_type == 2) { // f2c

                        $shopmessage->write_file_log('12、F2C派单结算', 2);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, Protocol . "" . $_SERVER['HTTP_HOST'] . "/addons/index.php/f2c/Ordering_Service/settle_shop_order?customer_id=" . $customer_id . "&user_id=" . $user_id . "&batchcode=" . $batchcode);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        if (Protocol == "https://") {
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                        }
                        $output = curl_exec($ch);
                        $or_result = json_decode($output);
                        $shopmessage->write_file_log('or_result=====' . var_export($or_result, true), 2);
                        if ($or_result->status != 1) {
                            $shopmessage->write_file_log('11、分仓派单结算 - 异常 ： ' . var_export($output, true), 2);
                        }
                        curl_close($ch);

                    }
                    /* */
                }

            }
        }else{
            $json["status"] = 10004;
            $json["line"] = 999;
            $json["msg"] = "已完成";
        }
        break;

    case 'yundian_return_money_all':
        $box_arr = $configutil->splash_new($_POST["box_arr"]);
        $box_arr = trim($box_arr,'[');
        $box_arr = trim($box_arr,']');
        $id_arr = explode(',',$box_arr);
        foreach($id_arr as $k => $v){
            $id = $v;
            //查询订单状态
            $sql = "SELECT batchcode,totalprice,paytime,paystatus,aftersale_state,aftersale_type,prvalues,prvalues_name,pid,rcount,sendstatus,status,aftersale_state,return_type,return_status,expressnum,is_QR,customer_id,yundian_id,paystyle FROM weixin_commonshop_orders where id = '{$id}'";
            $res = _mysql_query($sql);
            $row = mysql_fetch_assoc($res);

            if($row){
                $batchcode = $row['batchcode'];
                //判断状态是否是退款前状态
                if(($row['aftersale_type'] == 1 && $row['aftersale_state'] == 1 && $row['status'] == 0 && $row['sendstatus'] != 6) || ($row['aftersale_type'] == 2 && $row['paystatus'] == 1 && $row['return_type'] == 0 && $row['sendstatus'] == 3) || ($row['aftersale_type'] == 2 && $row['return_type'] == 1 && $row['paystatus'] == 1 && $row['return_status'] == 6)){
                    //是否是退款订单，查询退款金额
                    $sql_return = 'select * from '.WSY_SHOP.".weixin_commonshop_order_rejects where batchcode = '{$batchcode}'";
                    $res_return = _mysql_query($sql_return);
                    $row_return = mysql_fetch_assoc($res_return);
                    $account = isset($row_return['account'])?$row_return['account']:$row['totalprice'];
                    var_dump($account);die();
                    $retype =0;
                    if($row['aftersale_type'] == 2 && $row['return_type'] == 1 && $row['paystatus'] == 1 && $row['return_status'] == 6) $retype = 1;
                    if($row['aftersale_type'] == 2 && $row['return_type'] == 0 && $row['paystatus'] == 1 && $row['return_status'] == 2) $retype = 1;
                    //打款 退款
                    (float)$refund 				= $configutil->splash_new($account);// 退款的金额
                    (float)$currency			= $configutil->splash_new($_POST["currencyMoney"]);//退款的购物币
                    (float)$integral			= $configutil->splash_new($_POST["totalintegral"]);//退还的积分
                    (float)$refundSupplyMoney	= $configutil->splash_new($_POST["refundSupplyMoney"]);//退给供应商的金额
                    $retype 					= $configutil->splash_new($retype);
                    (float)$need_score_sum 		= $configutil->splash_new($_POST["need_score"]);
                    /*处理空值 start 2018-3-13*/
                    $refund 			  = empty($account)?0:$account;
                    $currency 			  = empty($currency)?0:$currency;
                    $integral             = empty($integral)?0:$integral;
                    $refundSupplyMoney    = empty($refundSupplyMoney)?0:$refundSupplyMoney;
                    $retype               = empty($retype)?0:$retype;
                    $need_score_sum       = empty($need_score_sum)?0:$need_score_sum;
                    /*处理空值 end*/
                    if($retype == 2){  //退货
                        $retype = 1;
                    }
                    $o_card_member_id 	= -1;
                    $agent_id			= 0;//代理商id
                    $agentcont_type		= 0;//代理结算: 1、代理结算 0、推广员结算
                    $buyer_user_id 		= -1;
                    $supply_id			= -1;//供应商ID
                    $Pay_Method			= 0;//0：真实支付；1：后台支付; 2:奖品赠送
                    $is_collageActivities = 0;//是否拼团订单；0：不是，1：拼团有效订单
                    $yundian_self       = 0;//是否云店自营订单
                    $query_ord = "select sendstatus,paystyle,card_member_id,agent_id,agentcont_type,user_id,supply_id,Pay_Method,is_collageActivities,yundian_self from weixin_commonshop_orders where batchcode='".$batchcode."' limit 1";
                    //_file_put_contents("log/order_goodRefund_" . $today . ".txt", "\r\nquery_ord=======".var_export($query_ord,true)."\r\n",FILE_APPEND);
                    $result_ord         = _mysql_query($query_ord) or die('Query_ord failed: ' . mysql_error());
                    while ($row_ord     = mysql_fetch_object($result_ord)) {
                        $sendstatus     = $row_ord->sendstatus;
                        $paystyle       = $row_ord->paystyle;
                        $o_card_member_id = $row_ord->card_member_id;

                        $agent_id       = $row_ord->agent_id;
                        $supply_id      = $row_ord->supply_id;
                        $Pay_Method      = $row_ord->Pay_Method;
                        $agentcont_type = $row_ord->agentcont_type;

                        $buyer_user_id  = $row_ord->user_id;
                        $is_collageActivities  = $row_ord->is_collageActivities;
                        $yundian_self  = $row_ord->yundian_self;
                    }
                    $playmoney_onoff = 1; //退款是否通过平台打款
                    if($yundian_self == 1) { //如果为云店自营产品，查询最后是否通过平台退款
                        $is_pingtai_sql = "SELECT playmoney_onoff FROM ".WSY_REBATE.".weixin_yundian_setting WHERE customer_id=".$customer_id." AND isvalid='1' ";
                        $is_pingtai_re  = _mysql_query($is_pingtai_sql) or die('Query_is_yundian failed: ' . mysql_error());
                        while ($row_is_pingtai_re     = mysql_fetch_object($is_pingtai_re)) {
                            $playmoney_onoff = $row_is_pingtai_re->playmoney_onoff;
                        }
                    }

                    if( $sendstatus != 4 and $sendstatus != 6 ){ //如果收货状态 不是 已退货和已退款
                        if( $currency > 0 ){
                            $user_currency = 0;	//用户的购物币数量
                            $query_currency = "SELECT currency FROM weixin_commonshop_user_currency WHERE user_id=".$buyer_user_id." AND isvalid=true";
                            $result_currency = _mysql_query($query_currency) or die('Query_currency failed:'.mysql_error());
                            while( $row_currency = mysql_fetch_object($result_currency) ){
                                $user_currency = $row_currency -> currency;
                            }
                            $user_currency += $currency;
                            //插入购物币日志
                            $sql = "insert into weixin_commonshop_currency_log(isvalid,customer_id,user_id,cost_money,cost_currency,after_currency,batchcode,status,type,class,remark,createtime) values (true,".$customer_id.",".$buyer_user_id.",0,".$currency.",".$user_currency.",".$batchcode.",1,1,4,'商城退款',now())";
                            _mysql_query($sql) or die('购物币 Query failed: ' . mysql_error());

                            //退购物币
                            $sql = "update weixin_commonshop_user_currency set currency=currency+".$currency." where isvalid=true and user_id=" . $buyer_user_id;
                            _mysql_query($sql) or die('购物币6 Query failed: ' . mysql_error());
                        }
                        //退还积分
                        if( $integral > 0 ){
                            $model_integral = new model_integral();

                            $re_integral['cust_id']	     = $customer_id;
                            $re_integral['batchcode']    = $batchcode;
                            $re_integral['integral_num'] = $integral;
                            $re_integral['user_id']		 = $buyer_user_id;

                            $refund_data = $model_integral->m_refund_Integral($re_integral);


                        }
                        //调用通联分期退款接口
                        if( $paystyle == '通联分期支付' ){
                            // 实例化db类
                            require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
                            $db = new \Key\DB(true);

                            // 通联退款接口
                            $industry_type = 'shop';
                            require_once($_SERVER['DOCUMENT_ROOT'].'/wsy_pay/web/allinpay/installment/refund.php');
                            $allinpay = new Allinpay();
                            $refund_result = $allinpay -> refund($customer_id,$batchcode,$industry_type);
                            $refund_results = json_decode($refund_result);
                            file_put_contents($_SERVER['DOCUMENT_ROOT']."/weixinpl/log/order_goodRefund_" . $today . ".txt", "refunds=======".var_export($refund_results,true)."\r\n",FILE_APPEND);
                            if( $refund_results->sucess_business_response->code === 0 ){
                                // 获取订单资料
                                $sql = "SELECT pay_batchcode,real_pay_price,pay_time,allinpay_client from system_order_pay_log where customer_id='{$customer_id}' and batchcode_str='{$batchcode}'";
                                $order_log = $db->getFields($sql);
                                $pay_batchcode = $order_log['pay_batchcode'];
                                $res_timestamp = $refund_results->sucess_business_response->res_timestamp;

                                switch ($industry_type) {
                                    case 'package':
                                        $sql = "SELECT return_account from package_return_t where batchcode='{$batchcode}'";
                                        break;

                                    default:
                                        $sql = "SELECT return_account from weixin_commonshop_orders where customer_id='{$customer_id}' and batchcode='{$batchcode}'";
                                        break;
                                }
                                $amount = bcadd($db->getField($sql),0,2);

                                // 第三方退款操作
                                require_once($_SERVER["DOCUMENT_ROOT"]."/wsy_pay/web/function/handle_order_refund.php");
                                $url = handle_order_refund($amount,$industry_type,$batchcode,$pay_batchcode,$res_timestamp,"通联分期支付",25);
                            }else{
                                $json["status"] = 10002;
                                $json["msg"] = '退款失败';
                                $jsons=json_encode($json);
                                die($jsons);
                            }
                        }
                        if( $paystyle != '微信支付' && $paystyle != '支付宝支付'){
                            $sql_e = "select id from weixin_commonshop_refunds where isvalid =true and batchcode='".$batchcode."'";
                            $result_e = _mysql_query($sql_e) or die("Query_stat error : ".mysql_error());
                            $refund_id_e = -1;
                            while ($row_e = mysql_fetch_object($result_e)) {
                                $refund_id_e = $row_e->id;
                            }
                            if($refund_id_e<0){
                                $refunds = "insert into weixin_commonshop_refunds (customer_id,batchcode,refund,currency,isvalid,createtime) values(".$customer_id.",'".$batchcode."',".$refund.",".$currency.",true,now())";
                                _mysql_query($refunds) or die ("Query_refunds2 ERROR : ".mysql_error());
                            }

                            if( $paystyle == '零钱支付'  || $Pay_Method == 1 ){	//后台支付退零钱

                                //拼团订单
                                if($is_collageActivities == 1){
                                    $queryc = "SELECT ccot.group_id,cgot.type,cgot.status,cgot.head_id FROM collage_crew_order_t AS ccot 
                    LEFT JOIN collage_group_order_t AS cgot ON cgot.id=ccot.group_id 
                    WHERE ccot.batchcode='".$batchcode."'";
                                    $resultc = _mysql_query($queryc) or die('queryc failed:'.mysql_error());
                                    $collage_type = 1;
                                    while( $rowc = mysql_fetch_object($resultc) ){
                                        $collage_type = $rowc -> type;
                                        $collage_status = $rowc -> status;
                                        $head_id = $rowc -> head_id;
                                        $group_id = $rowc -> group_id;
                                    }
                                }

                                if($is_collageActivities == 1 && $collage_type == 6 && $collage_status == 3 && $head_id == $buyer_user_id){
                                    //团长免单团  拼团成功 的团长
                                    //先扣回返给他的全部金额
                                    $queryml = "SELECT money FROM moneybag_log WHERE batchcode='".$batchcode."'";
                                    $resultml = _mysql_query($queryml) or die('queryml failed:'.mysql_error());
                                    $returen_money = 0;
                                    while( $rowml = mysql_fetch_object($resultml) ){
                                        $returen_money = $rowml -> money;//团长免单团返回的金额
                                    }

                                    $queryblc = "SELECT balance FROM moneybag_t where isvalid=true and user_id=" . $buyer_user_id;
                                    $resulblc = _mysql_query($queryblc) or die('queryml failed:'.mysql_error());
                                    $before_balance = 0;
                                    while( $rowblc = mysql_fetch_object($resulblc) ){
                                        $before_balance = $rowblc -> balance;//变动前的余额
                                    }

                                    //扣钱
                                    $sql = "update moneybag_t set balance=balance-".$returen_money.",createtime=now() where isvalid=true and user_id=" . $buyer_user_id;
                                    _mysql_query($sql) or die('零钱支付3 Query failed: ' . mysql_error());

                                    if( $returen_money>0 ){ //金额大于0才插入日志
                                        $after_balance = $before_balance - $returen_money;//变动后的余额
                                        $sql ="insert into moneybag_log (isvalid,customer_id,user_id,before_money,money,after_money,type,batchcode,pay_style,remark,createtime)values(true,".$customer_id.",".$buyer_user_id.",".$before_balance.",-".$returen_money.",".$after_balance.",1,".$batchcode.",0,'商城退款，扣回团长免单团已返金额',now())";
                                        _mysql_query($sql) or die('零钱支付33 Query failed: ' . mysql_error());
                                    }


                                }

                                $queryblc2 = "SELECT balance FROM moneybag_t where isvalid=true and user_id=" . $buyer_user_id;
                                $resulblc2 = _mysql_query($queryblc2) or die('queryml failed:'.mysql_error());
                                $before_balance2 = 0;
                                while( $rowblc2 = mysql_fetch_object($resulblc2) ){
                                    $before_balance2 = $rowblc2 -> balance;//变动前的余额
                                }

                                $sql = "update moneybag_t set balance=balance+".$refund.",createtime=now() where isvalid=true and user_id=" . $buyer_user_id;
                                _mysql_query($sql) or die('零钱支付2 Query failed: ' . mysql_error());

                                if( $refund>0 ){ //金额大于0才插入日志
                                    $after_balance2 = $before_balance2 + $refund;//变动后的余额
                                    if($yundian_self == 1) {
                                        $sql ="insert into moneybag_log (isvalid,customer_id,user_id,before_money,money,after_money,type,batchcode,pay_style,remark,createtime)values(true,".$customer_id.",".$buyer_user_id.",".$before_balance2.",".$refund.",".$after_balance2.",0,".$batchcode.",0,'云店自营退款',now())";
                                    } else {
                                        $sql ="insert into moneybag_log (isvalid,customer_id,user_id,before_money,money,after_money,type,batchcode,pay_style,remark,createtime)values(true,".$customer_id.",".$buyer_user_id.",".$before_balance2.",".$refund.",".$after_balance2.",0,".$batchcode.",0,'商城退款',now())";
                                    }

                                    _mysql_query($sql) or die('零钱支付22 Query failed: ' . mysql_error());
                                }



                            }else if( $paystyle == '会员卡余额支付' and $o_card_member_id>0 ){
                                /*$before_cost = 0;
                                $consume_before="select remain_consume from weixin_card_member_consumes where card_member_id=".$o_card_member_id." limit 1";
                                //_file_put_contents("log/order_goodRefund_" . $today . ".txt", "consume_before=======".var_export($consume_before,true)."\r\n",FILE_APPEND);
                                $result_before = _mysql_query($consume_before) or die('Query_consume_before ERROR: ' . mysql_error());
                                while ($row_before = mysql_fetch_object($result_before)) {
                                    $before_cost = $row_before->remain_consume;
                                }
                                $after_cost = $before_cost + $refund;

                                //会员卡返回金额
                                $consume = "update weixin_card_member_consumes set total_consume= total_consume-" . $refund . ",remain_consume=remain_consume+".$refund."  where card_member_id=".$o_card_member_id;
                                //_file_put_contents("log/order_goodRefund_" . $today . ".txt", "consume=======".var_export($consume,true)."\r\n",FILE_APPEND);
                                _mysql_query($consume) or die ("Query_consume ERROR : ".mysql_error());

                                //会员卡金额改动日志
                                if( $refund>0 ){ //金额大于0才插入日志
                                    $consume_log = "insert into weixin_card_recharge_records(new_record,before_cost,cost,after_cost,card_member_id,isvalid,createtime,remark) values(1,".$before_cost.",".$refund.",".$after_cost.",".$o_card_member_id.",true,now(),'订单取消，会员卡余额返回')";
                                    //_file_put_contents("log/order_goodRefund_" . $today . ".txt", "consume_log=======".var_export($consume_log,true)."\r\n",FILE_APPEND);
                                    _mysql_query($consume_log);
                                }*/

                                //退款减会员卡消费总额
                                $before_money = 0;	//返退款前余额
                                $query = "select remain_consume from weixin_card_member_consumes where isvalid=true and card_member_id=" . $o_card_member_id ." limit 0,1";
                                $result = _mysql_query($query) or die('会员卡余额2 Query failed: ' . mysql_error());

                                while ($row = mysql_fetch_object($result)) {
                                    $before_money = $row->remain_consume;
                                }

                                $after_money = $before_money + $refund;

                                $query_card_consume = "UPDATE weixin_card_member_consumes SET total_consume=total_consume-".$refund.",remain_consume=remain_consume+".$refund." WHERE card_member_id=".$o_card_member_id." AND isvalid=true";
                                _mysql_query($query_card_consume) or die('Query_card_consume failed:'.mysql_error());

                                if ( $retype == 0 ){
                                    $type_remark = '退款';
                                } else if ( $retype == 1 ){
                                    $type_remark = '退货';
                                }

                                $remark = '订单'.$type_remark.",会员卡余额增加".$refund.",商城订单号:".$batchcode;
                                //退款金额大于查入日志
                                if( $refund > 0 ){
                                    $query_record="INSERT INTO weixin_card_recharge_records (
									card_member_id,
									before_cost,
									cost,
									after_cost,
									remark,
									new_record,
									isvalid,
									createtime
								)
								VALUES
									(
										".$o_card_member_id.",
										".$before_money.",
										".$refund.",
										".$after_money.",
										'".$remark."',
										1,
										TRUE,
										now()
									)";
                                    _mysql_query($query_record) or die('Query_record failed:'.mysql_error());
                                }
                            }


                        }else{
                            $sql0 = "select id from weixin_commonshop_refunds where isvalid =true and batchcode='".$batchcode."'";
                            $result0 = _mysql_query($sql0) or die("Query_stat error : ".mysql_error());
                            $refund_id = -1;
                            while ($row0 = mysql_fetch_object($result0)) {
                                $refund_id = $row0->id;
                            }
                            if($refund_id<0 && $refund > 0){
                                //$refunds = "insert into weixin_commonshop_refunds (customer_id,batchcode,refund,currency,isvalid,createtime) values(".$customer_id.",'".$batchcode."',".$refund.",".$currency.",true,now())";
                                //_file_put_contents("log/order_goodRefund_" . $today . ".txt", "refunds=======".var_export($refunds,true)."\r\n",FILE_APPEND);
                                //_mysql_query($refunds) or die ("Query_refunds2 ERROR : ".mysql_error());
                            }else{
                                $refunds = "update weixin_commonshop_refunds set currency=".$currency." where id=".$refund_id;
                                _mysql_query($refunds) or die('Query_refunds2 failed: ' . mysql_error());
                            }

                        }


                        if($retype == 0 && $sendstatus !=6){ //退款


                            if($sendstatus == 5){ //未发货的申请退款 更新为 6
                                $orders="update weixin_commonshop_orders set sendstatus=6 where batchcode='".$batchcode."'";
                            }

                            _file_put_contents("log/order_goodRefund_" . $today . ".txt", "orders=======".var_export($orders,true)."\r\n",FILE_APPEND);
                            _mysql_query($orders) or die (" Query_orders ERROR : ".mysql_error());

                            if($is_collageActivities == 1 && $collage_type == 6 ){
                                $query_status_up = "UPDATE collage_crew_order_t SET status=6 WHERE batchcode='".$batchcode."'";
                                _mysql_query($query_status_up) or die('Query_status_up failed:'.mysql_error());

                                $query_order_up = "UPDATE weixin_commonshop_orders SET status=-1 WHERE batchcode='".$batchcode."'";
                                _mysql_query($query_order_up) or die('Query_order_up failed:'.mysql_error());

                                $query_orderp_up = "UPDATE weixin_commonshop_order_prices SET status=-1 WHERE batchcode='".$batchcode."'";
                                _mysql_query($query_orderp_up) or die('Query_orderp_up failed:'.mysql_error());

                                $query = "SELECT batchcode,paystyle FROM collage_crew_order_t WHERE customer_id=".$customer_id." AND group_id=".$group_id." AND isvalid=true AND is_refund=false AND status=3";
                                $result = _mysql_query($query) or die('Query failed:'.mysql_error());
                                while ( $row = mysql_fetch_object($result) ) {
                                    $batchcode_arr[] = array(
                                        'batchcode' => $row -> batchcode,
                                        'paystyle'	=> $row -> paystyle
                                    );
                                }
                                if ( empty($batchcode_arr) ) {	//该团已全部退款
                                    //更改团退款状态
                                    $query_gstatus_up = "UPDATE collage_group_order_t SET refund_status=2 WHERE id='".$group_id."'";
                                    _mysql_query($query_gstatus_up) or die('Query_gstatus_up failed:'.mysql_error());
                                }

                            }

                            $custom = "购物币";
                            $query = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
                            $result = _mysql_query($query)or die('Query_ord_pro failed 1467: ' . mysql_error());
                            while( $rowc = mysql_fetch_object($result) ){
                                $custom = $rowc->custom;
                            }
                            //退款扣佣金
                            $query_ord_pro = "select remark,reward,user_id,card_member_id,level_name,own_user_name,id_new,type,commission_type,commission_score from weixin_commonshop_order_promoters where isvalid=true and paytype=0 and batchcode='".$batchcode."'";
                            _file_put_contents("log/order_goodRefund_" . $today . ".txt", "query_ord_pro=======".var_export($query_ord_pro,true)."\r\n",FILE_APPEND);
                            $result_ord_pro = _mysql_query($query_ord_pro) or die('Query_ord_pro failed: ' . mysql_error());
                            while ($row_ord_pro = mysql_fetch_object($result_ord_pro)) {
                                $money = $row_ord_pro->reward;
                                $user_id = $row_ord_pro->user_id;
                                $card_member_id = $row_ord_pro->card_member_id;
                                $level_name = $row_ord_pro->level_name;
                                $own_user_name = $row_ord_pro->own_user_name;
                                $id_new = $row_ord_pro->id_new;
                                $protype = $row_ord_pro->type;
                                $commission_type = $row_ord_pro->commission_type;
                                $commission_score = $row_ord_pro->commission_score;
                                //$remark = $level_name.$own_user_name."退款扣除:".$money;
                                if($protype == 10){
                                    $tis = "扣除".$custom."：￥";
                                    $com = "个";
                                }else{
                                    $tis = "退款扣除：￥";
                                    $com = "元";
                                }
                                $remark = "身份：【".$level_name."】\n".
                                    "用户：【".$own_user_name."】\n";
                                if( $commission_type == 1 && $money > 0 ){	//扣除零钱
                                    $remark .= $tis.$money.$com;
                                }
                                if( $commission_type == 2 && $commission_score > 0 ){	//扣除积分
                                    $remark .= "退积分扣除：".$commission_score;
                                }
                                //扣佣金
                                $qr_info_id=-1;
                                $query_qr_in="select id from weixin_qr_infos where type=1 and foreign_id=".$user_id." and user_type=1";
                                //_file_put_contents("log/order_goodRefund_" . $today . ".txt", "query_qr_in=======".var_export($query_qr_in,true)."\r\n",FILE_APPEND);
                                $result_qr_in = _mysql_query($query_qr_in) or die('Query_qr_in failed: ' . mysql_error());
                                while ($row_qr_in = mysql_fetch_object($result_qr_in)) {
                                    $qr_info_id = $row_qr_in->id;
                                }
                                if($qr_info_id>0){
                                    $query_up_qr="update weixin_qrs set reward_money= reward_money-".$money." where qr_info_id=".$qr_info_id;
                                    //_file_put_contents("log/order_goodRefund_" . $today . ".txt", "query_up_qr=======".var_export($query_up_qr,true)."\r\n",FILE_APPEND);
                                    _mysql_query($query_up_qr);
                                }
                                //更改佣金表状态
                                $query_up_pro = "update weixin_commonshop_order_promoters set do_time=now(),paytype=4 where id_new=".$id_new;
                                //_file_put_contents("log/order_goodRefund_" . $today . ".txt", "query_up_pro=======".var_export($query_up_pro,true)."\r\n",FILE_APPEND);
                                _mysql_query($query_up_pro);
                                //添加信息提醒
                                $query5="select weixin_fromuser from weixin_users where id=".$user_id." limit 1";
                                //_file_put_contents("log/order_goodRefund_" . $today . ".txt", "query5=======".var_export($query5,true)."\r\n",FILE_APPEND);
                                $result5 = _mysql_query($query5) or die('Query5 failed: ' . mysql_error());
                                $parent_fromuser="";
                                while ($row5 = mysql_fetch_object($result5)) {
                                    $parent_fromuser= $row5->weixin_fromuser;
                                }
                                $remark=addslashes($remark);
                                $content = "买家退货\n时间：".date( "Y-m-d H:i:s")."\n".$remark;
                                if($money>0){
                                    $shopmessage->SendMessage($content,$parent_fromuser,$customer_id);
                                }

                            }

                            //添加日志记录，给退款用户发送消息
                            if( $refund > 0 or $currency > 0 ){
                                if($playmoney_onoff == 0 ) {
                                    $yundian_user_id_re = $_SESSION['user_id_'.$customer_id];
                                    $descript = "云店店主退款，退款";

                                    $yundian_user_sql = "select weixin_fromuser from weixin_users where id='".$yundian_user_id_re."' ";
									$result_yundian_user = _mysql_query($yundian_user_sql)or die("Query_user error  : ".mysql_error());
									$yundian_fromuser = mysql_result($result_yundian_user,0,0);
                                } else {
                                    $descript = "平台退款，退款";
                                }

                                $content  = "订单：".$batchcode."\n商家已退款，\n退款";
                                $descript2 = "";
                                if( $refund > 0 ){
                                    $descript2 .= "金额".$refund.'，';
                                }
                                if( $currency > 0 ){
                                    $descript2 .= $custom.$currency.'，';
                                }
                                if( $refund_data['errcode']  == 0  ){

                                    $descript2 .= $refund_data['data']['name'].$refund_data['data']['integral_num'].'，';
                                }
                                $descript .= $descript.$descript2;
                                if($playmoney_onoff == 0) {
                                    $query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)values('".$batchcode."',12,'".$descript."','".$yundian_fromuser."',now(),1)";
                                } else {
                                    $query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)values('".$batchcode."',12,'".$descript."','".$log_username."',now(),1)";
                                }

                                //_file_put_contents("log/order_goodRefund_" . $today . ".txt", "query_logs=======".var_export($query_logs,true)."\r\n",FILE_APPEND);
                                _mysql_query($query_logs) or die("Query_logs error  : ".mysql_error());
                                //	$content = "订单：".$batchcode."\n商家已退款，\n退款金额：".$refund."元，\n请注意查收。";
                                $content .= $descript2."\n请注意查收。";

                                $query_user = "select weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
                                $result_user = _mysql_query($query_user)or die("Query_user error  : ".mysql_error());
                                $fromuser = mysql_result($result_user,0,0);

                                if($yundian_self == 1) {
                                    $content = "您的订单：".$batchcode."的退款申请，店主已经同意，退款金额会原路退回。";
                                }

                                $shopmessage->SendMessage($content,$fromuser,$customer_id);
                            }

                        }else if($retype == 1){ //退货后退款

                            $pid		= -1;
                            $rcount 	=  0;
                            $prvalues	= "";
                            $query_ord = "select pid,rcount,prvalues,sendstatus,is_exchange,exchange_id from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
                            $result_ord = _mysql_query($query_ord) or die('Query_ord failed: ' . mysql_error());
                            while ($row_ord = mysql_fetch_object($result_ord)) {
                                $pid 		= $row_ord->pid;
                                $rcount 	= $row_ord->rcount;
                                $sendstatus	= $row_ord->sendstatus;
                                $prvalues	= $row_ord->prvalues;
                                $is_exchange = $row_ord->is_exchange;
                                $exchange_id = $row_ord->exchange_id;

                                $prvalues= rtrim($prvalues,"_"); //将添加产品库存的操作加到循环里，防止订单中有两件商品时加库存的问题
                                if($is_exchange != 1) {
                                    if (!empty($prvalues)) {
                                        $query_num_up = "update weixin_commonshop_product_prices set storenum= storenum+" . $rcount . " where product_id=" . $pid . " and proids='" . $prvalues . "'";
                                        /*4M start*/
                                        $sql_4m = "select create_type from weixin_commonshop_product_prices where product_id=" . $pid . " and proids='" . $prvalues . "'";
                                        $result_4m = _mysql_query($sql_4m) or die("stockrecovery Query error : " . mysql_error());
                                        while ($row_4m = mysql_fetch_object($result_4m)) {
                                            $create_type = $row_4m->create_type;
                                        }
                                        //4M同步库存
                                        if ($create_type != 3) {
                                            //$is_4m = true; //ces
                                            //$create_type = 1; //ces
                                            $shop_4m->sync_4M_product_storenum($is_4m, 1, 1, $rcount, $pid, $prvalues, -1, $create_type);
                                        }
                                        /*4M end*/
                                    } else {
                                        $query_num_up = "update weixin_commonshop_products set storenum= storenum+" . $rcount . " where id=" . $pid;
                                        /*4M start*/
                                        $sql_4m = "select create_type from weixin_commonshop_products where id=" . $pid . "";
                                        $result_4m = _mysql_query($sql_4m) or die("stockrecovery Query error : " . mysql_error());
                                        while ($row_4m = mysql_fetch_object($result_4m)) {
                                            $create_type = $row_4m->create_type;
                                        }
                                        if ($create_type != 3) {
                                            //4M同步库存
                                            //$is_4m = true; //ces
                                            //$create_type = 1; //ces
                                            $shop_4m->sync_4M_product_storenum($is_4m, 2, 1, $rcount, $pid, '', -1, $create_type);

                                        }
                                        /*4M end*/
                                    }
                                }else{
                                    //换购活动对应的产品库存加回去
                                    $ex_storenum = $ex_storenum + $rcount;
                                    $query_num_up = "UPDATE weixin_commonshop_exchange_products SET storenum=".$ex_storenum." WHERE exchange_id='".$exchange_id."' and pid=".$pid;
                                }
                                _mysql_query($query_num_up);
                            }
                            /*4M库存同步执行语句 start */
                            if($create_type !=3 ){
                                if($is_4m){
                                    $shop_4m->update_sql_sync_4M_product_storenum(5);
                                }
                            }
                            /*4M库存同步执行语句 end */
                            if( $sendstatus != 4 ){
                                //退货完成
                                $query_ord_up = "update weixin_commonshop_orders set sendstatus=4 where batchcode='".$batchcode."'";
                                _mysql_query($query_ord_up);

                                $shopmessage->Back_GetMoney($batchcode,$o_card_member_id,$refund,$customer_id,$paystyle);
                            }



                            if($playmoney_onoff == 0 ) {
                                $yundian_user_id_re = $_SESSION['user_id_'.$customer_id];

                                $yundian_user_sql = "select weixin_fromuser from weixin_users where id='".$yundian_user_id_re."' ";
								$result_yundian_user = _mysql_query($yundian_user_sql)or die("Query_user error  : ".mysql_error());
								$yundian_fromuser = mysql_result($result_yundian_user,0,0);

                                $query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
					values('".$batchcode."',12,'云店店主退货退款，退款金额：".$refund."','".$yundian_fromuser."',now(),1)";
                            } else {
                                $query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid)
					values('".$batchcode."',12,'平台退货退款，退款金额：".$refund."','".$log_username."',now(),1)";
                            }

                            _mysql_query($query_logs) or die("Query_logs error  : ".mysql_error());

                            $content = "订单：".$batchcode."\n商家已完成退货，\n退款金额：".$refund."元，\n请注意查收。";
                            $query_user2 = "select weixin_fromuser from weixin_users where id= ".$buyer_user_id." limit 0,1";
                            $result_user2 = _mysql_query($query_user2) or die("Query_user2 error  : ".$buyer_user_id.mysql_error());
                            $fromuser = "";
                            if($row_user2 = mysql_fetch_object($result_user2)){
                                $fromuser = $row_user2->weixin_fromuser;
                            }

                            if($yundian_self == 1) {
                                $content = "您的订单：".$batchcode."的退货申请，店主已经完成退款，退款金额会原路退回。";
                            }
                            $shopmessage->SendMessage($content,$fromuser,$customer_id);
                        }

                        if($supply_id>0 && $refundSupplyMoney>0){
                            if($shopmessage->GetRefundSupply_Money($batchcode,$refundSupplyMoney,$supply_id)){//调用供应商退款方法
                                //$shopmessage->GetRefundSupply_Money($batchcode,$refundSupplyMoney,$supply_id);
                                //如果返回真，就发送消息给供应商
                                $query_supply = "select weixin_fromuser from weixin_users where id= ".$supply_id." limit 0,1";
                                $result_supply = _mysql_query($query_supply) or die("Query_query_supply error  : ".$supply_id.mysql_error());
                                $fromuser_supply = "";
                                if($row_supply = mysql_fetch_object($result_supply)){
                                    $fromuser_supply = $row_supply->weixin_fromuser;
                                }
                                $content = "订单：".$batchcode."\n商家已完成退货，\n账户余额增加：".$refundSupplyMoney."元，\n请注意查收。";
                                $shopmessage->SendMessage($content,$fromuser_supply,$customer_id);
                            }
                        }
                        if($need_score_sum>0){
                            //返还会员积分
                            $consume_score = 0;
                            $before_score  = 0;
                            $after_score   = 0;
                            $before_score1  = 0;
                            $after_score1   = 0;
                            $remark = "退款返还积分";
                            $query_score = "select remain_score from weixin_card_member_scores where isvalid=true and card_member_id= '" . $o_card_member_id ."' limit 1";
                            $result_score = _mysql_query($query_score) or die("Query_query_mbScore error  : ".$o_card_member_id.mysql_error());
                            while($row = mysql_fetch_object($result_score)){
                                $before_score = $row->remain_score;
                            }
                            $after_score  = $before_score+$need_score_sum;
                            $query_logs = "insert into weixin_card_score_records(card_member_id,before_score,after_score,score,createtime,remark,type,new_record,isvalid) values('".$o_card_member_id."','".$before_score."','".$after_score."','".$need_score_sum."',now(),'".$remark."',12,1,true)";
                            $result_log = _mysql_query($query_logs) or die("Insert_query_logs error  : ".$o_card_member_id.mysql_error());

                            $c_sql = "select score from weixin_card_score_records where isvalid=true and type=2 and score > 0 and before_score = '".$after_score."'";
                            $result_c_sql = _mysql_query($c_sql);
                            while($row_c = mysql_fetch_object($result_c_sql)){
                                $score = $row_c->score;
                            }
                            if($score > 0){
                                $remark1 = "扣除消费奖励积分";
                                $consume_score = $score;
                                /*				$query_consume_score = "select price from weixin_commonshop_order_prices where paystatus = 1 and batchcode = '".$batchcode."'";
                                                $result_consume_score = _mysql_query($query_consume_score);
                                                while($row2 = mysql_fetch_object($result_consume_score)){
                                                    $consume_score = $row2->price * 10;//消费奖励积分
                                                }*/
                                $after_score1 =  $after_score - $consume_score;
                                $before_score1 =  $after_score;
                                $consume_score = 0 - $consume_score;
                                $query_logs1 = "insert into weixin_card_score_records(card_member_id,before_score,after_score,score,createtime,remark,type,new_record,isvalid) values('".$o_card_member_id."','".$before_score1."','".$after_score1."','".$consume_score."',now(),'".$remark1."',12,1,true)";
                                $result_log1 = _mysql_query($query_logs1) or die("Insert_query_logs error  : ".$o_card_member_id.mysql_error());
                            }
                            $puls_score = $need_score_sum + $consume_score;
                            $update_card_mbScore = "update ".WSY_USER.".weixin_card_member_scores set total_score = total_score+".$puls_score.",consume_score = consume_score-".$need_score_sum.",remain_score = remain_score+".$puls_score." where isvalid=true and card_member_id = ".$o_card_member_id;
                            $result_card_mbScore = _mysql_query($update_card_mbScore) or die("Update_update_card_mbScore error  : ".$o_card_member_id.mysql_error());
                        }
                        /*云店自营订单 start*/
                        if($yundian_self == 1){
                            $yundian_reward = 0;//云店自营产品分佣 = 订单金额*（1-店主抽佣比例）
                            $new_yundian_reward = 0;//退款后：云店自营产品分佣 = （订单金额-退款金额）*（1-店主抽佣比例）
                            $yundian_order_prices = 0;//订单金额
                            //$yundian_award_percentage = 0;//抽佣比例 防止后台改变
                            $yun_commission = 0;
                            $new_yun_reward = 0;//退款后：平台对云店的抽成
                            $query_yundian_reward = "select yundian_reward,price,yun_commission from weixin_commonshop_order_prices where batchcode='".$batchcode."'";
                            $result_yundian_reward = _mysql_query($query_yundian_reward) or die('Query_yundian_award failed:'.mysql_error());
                            while( $row_yundian_reward = mysql_fetch_object($result_yundian_reward) ){
                                $yundian_reward = $row_yundian_reward -> yundian_reward;
                                $yundian_order_prices = $row_yundian_reward -> price;
                                $yun_commission  = $row_yundian_reward -> yun_commission;
                            }
                            $new_yundian_reward = ($yundian_order_prices - $refund)*(1-$yun_commission);
                            $new_yun_reward = ($yundian_order_prices - $refund)*$yun_commission;
                            if($new_yundian_reward < 0){$new_yundian_reward = 0;}
                            $update_new_yundian_reward = "UPDATE weixin_commonshop_order_prices SET yundian_reward = '$new_yundian_reward' WHERE batchcode='".$batchcode."'";
                            _mysql_query($update_new_yundian_reward) or die('Update_new_yundian_reward failed:'.mysql_error());
                            $new_user_id = 0;
                            $new_yundian_user_id = 0;
                            $new_card_member_id = 0;
                            $new_remark = '';
                            $new_customer_id = 0;
                            $new_level_name = '';
                            $new_own_user_name = '';
                            $new_paytype = 0;
                            $new_red_pack_id = 0;
                            $new_cityarea_id = 0;
                            $new_commission_type = 0;
                            $new_commission_score = 0;
                            $new_level = 0;
                            //云店自营产品奖励
                            $select_promoters = "select user_id,yundian_user_id,reward,card_member_id,remark,customer_id,level_name,own_user_name,paytype,red_pack_id,cityarea_id,commission_type,commission_score,level from weixin_commonshop_order_promoters where batchcode='".$batchcode."' and isvalid = true and type=26 and class = 91";
                            $result_promoters = _mysql_query($select_promoters) or die("query_logs error  : ".mysql_error());
                            while($row_promoters = mysql_fetch_object($result_promoters)){
                                $new_user_id = $row_promoters->user_id;
                                $new_yundian_user_id = $row_promoters->yundian_user_id;
                                $new_card_member_id = $row_promoters->card_member_id;
                                //$new_remark = mysql_real_escape_string($row_promoters->remark);
                                $new_customer_id = $row_promoters->customer_id;
                                $new_level_name = mysql_real_escape_string($row_promoters->level_name);
                                $new_own_user_name = mysql_real_escape_string($row_promoters->own_user_name);
                                $new_paytype = $row_promoters->paytype;
                                $new_red_pack_id = $row_promoters->red_pack_id;
                                $new_cityarea_id = $row_promoters->cityarea_id;
                                $new_commission_type = $row_promoters->commission_type;
                                $new_commission_score = $row_promoters->commission_score;
                                $new_level = $row_promoters->level;
                            }
                            $new_remark = "退款后:(".$new_own_user_name.")云店自营订单最终返还:".$new_yundian_reward."元";
                            $update_yundian_self_pro = "UPDATE weixin_commonshop_order_promoters SET isvalid = false WHERE batchcode='".$batchcode."' and type=26 and class = 91";
                            _mysql_query($update_yundian_self_pro) or die('Update_yundian_self_pro failed:'.mysql_error());
                            if( $new_yundian_reward > 0 ){
                                $insert_yundian_self_pro = "insert into weixin_commonshop_order_promoters(user_id,reward,card_member_id,isvalid,createtime,remark,customer_id,level_name,own_user_name,batchcode,paytype,red_pack_id,level,type,class,cityarea_id,commission_type,commission_score,yundian_user_id) values(".$new_user_id.",".$new_yundian_reward.",".$new_card_member_id.",true,now(),'".$new_remark."',".$new_customer_id.",'".$new_level_name."','".$new_own_user_name."',".$batchcode.",0,'".$new_red_pack_id."',".$new_level.",26,91,".$new_cityarea_id.",".$new_commission_type.",".$new_commission_score.",".$new_yundian_user_id.")";
                                _mysql_query($insert_yundian_self_pro);
                            }

                            //平台对云店的抽成
                            $new_remark = '';
                            $select_yun_promoters = "select user_id,yundian_user_id,reward,card_member_id,remark,customer_id,level_name,own_user_name,paytype,red_pack_id,cityarea_id,commission_type,commission_score,level from weixin_commonshop_order_promoters where batchcode='".$batchcode."' and isvalid = true and type=27 and class = 91";
                            $result_yun_promoters = _mysql_query($select_yun_promoters) or die("query_logs error  : ".mysql_error());
                            while($row_yun_promoters = mysql_fetch_object($result_yun_promoters)){
                                $new_user_id = $row_yun_promoters->user_id;
                                $new_yundian_user_id = $row_yun_promoters->yundian_user_id;
                                $new_card_member_id = $row_yun_promoters->card_member_id;
                                //$new_remark = mysql_real_escape_string($row_yun_promoters->remark);
                                $new_customer_id = $row_yun_promoters->customer_id;
                                $new_level_name = mysql_real_escape_string($row_yun_promoters->level_name);
                                //$new_own_user_name = mysql_real_escape_string($row_yun_promoters->own_user_name);
                                $new_paytype = $row_yun_promoters->paytype;
                                $new_red_pack_id = $row_yun_promoters->red_pack_id;
                                $new_cityarea_id = $row_yun_promoters->cityarea_id;
                                $new_commission_type = $row_yun_promoters->commission_type;
                                $new_commission_score = $row_yun_promoters->commission_score;
                                $new_level = $row_yun_promoters->level;
                            }
                            $new_remark = "退款后:商城抽取(".$new_own_user_name.")云店金额".$new_yun_reward."元,仅记录";
                            $update_yundian_pro = "UPDATE weixin_commonshop_order_promoters SET isvalid = false WHERE batchcode='".$batchcode."' and type=27 and class = 91";
                            _mysql_query($update_yundian_pro) or die('Update_yundian_self_pro failed:'.mysql_error());
                            if( $new_yun_reward > 0 ){
                                $insert_yundian_pro = "insert into weixin_commonshop_order_promoters(user_id,reward,card_member_id,isvalid,createtime,remark,customer_id,level_name,own_user_name,batchcode,paytype,red_pack_id,level,type,class,cityarea_id,commission_type,commission_score,yundian_user_id) values(".$new_user_id.",".$new_yun_reward.",".$new_card_member_id.",true,now(),'".$new_remark."',".$new_customer_id.",'".$new_level_name."','商城',".$batchcode.",7,'".$new_red_pack_id."',".$new_level.",27,91,".$new_cityarea_id.",".$new_commission_type.",".$new_commission_score.",".$new_yundian_user_id.")";
                                _mysql_query($insert_yundian_pro);
                            }
                        }
                        /*云店自营订单 end*/
                        $json["is_collageActivities"] = $is_collageActivities;
                        $json["status"] = 0;
                        $json["line"] = 1032;
                        $json["msg"] .= " 编号：".$batchcode."，退款成功! ";
                    }else{
                        $json["status"] = 20002;
                        $json["line"] = 677;
                        $json["msg"] .= " 编号：".$batchcode."，已确认完成，请勿重复提交！";
                    }
                }
            }

        }

        $json["status"] = 0;
        $json["line"] = 999;
        $json["msg"] = "批量打款完成。".$json["msg"];
        break;

	default:
		$json["status"] = 10003;
		$json["line"] = 999;
		$json["msg"] = "未知方法";
	break;
}


file_put_contents("log/order_api_" . $today . ".txt", "json=======".var_export($json,true)."\r\n",FILE_APPEND);

$error =mysql_error();
if(!empty($error)){
	$json["status"] = 10002;
	$json["msg"] = $error;
}

/* if($link){mysql_close($link);}  */
mysql_close($link);
file_put_contents("log/order_api_" . $today . ".txt", "json2=======".var_export($json,true)."\r\n",FILE_APPEND);

$jsons=json_encode($json);
die($jsons);

?>