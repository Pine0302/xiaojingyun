<?php
ignore_user_abort(true); // 忽略客户端断开 
set_time_limit(0);    // 设置执行不超时

header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require_once '../../../../weixinpl/common/excel/phpExcelReader/Excel/reader.php';

$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('utf-8');
$log_username = $_SESSION['username'];
require('../../../../weixinpl/common/utility_shop.php');  //商城方法
$shopmessage = new shopMessage_Utlity();

$sendMessageContent = [];	//推送消息内容

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (!is_uploaded_file($_FILES["excelfile"]["tmp_name"]))
        //是否存在文件
    {
    }else{
        $Import_TmpFile = $_FILES['excelfile']['tmp_name'];
        $data->read($Import_TmpFile);

        $order_way = $_POST['order_way'];	//订单来源，1：导出记录，2：导出飞豆
        $f2c_id    = $_GET['f2c_id'];//
        $from_page = $_GET['from_page'];
		
		$numRows = $data->sheets[0]['numRows'];  //获取最大的行数
		
		//限制数量
		if($numRows>510){
			echo "<script>alert('导入订单最大数量限制为500条，请重新编辑后导入！');</script>";
			return;			
		}
			
		
		
        if($order_way == 3)//货到付款导入
        {
            $k = 0;
            for($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

                $batchcode      = $data -> sheets[0]['cells'][$i][1];
                $order_time     = $data -> sheets[0]['cells'][$i][2];
                $is_sign        = $data -> sheets[0]['cells'][$i][3];
                //去掉特殊符号
                $batchcode      = str_replace("'",'',$batchcode);
                $batchcode      = str_replace("＇",'',$batchcode);
                $order_time     = str_replace("'",'',$order_time);
                $order_time     = str_replace("＇",'',$order_time);
                $is_sign     	= str_replace("'",'',$is_sign);
                $is_sign     	= str_replace("＇",'',$is_sign);

                $is_sign == "确认" ? $is_sign = 1 : $is_sign = 2;

                $datas[$k]['batchcode']  = $batchcode;
                $datas[$k]['order_time'] = $order_time;
                $datas[$k]['is_sign']    = $is_sign;

                $k++;
            }

            foreach($datas as $k=>$v)
            {
                $is_sign    = $v['is_sign'];
                $batchcode  = $v['batchcode'];
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

                $order_sel  = "select id from weixin_commonshop_orders where is_pay_on_delivery = 1 and is_sign=0 and batchcode='".$batchcode."' and customer_id=".$customer_id;
                $order_rest = _mysql_query($order_sel);
                if($order_rest == false || $batchcode == '')
                {
                    continue;
                }
                $query_sign = "update weixin_commonshop_orders set is_sign=$is_sign,paystatus=$paystatus,sendstatus=$sendstatus $confirm_receivetime $status $paytime where batchcode='".$batchcode."' and customer_id=".$customer_id;
                //echo $query_sign;
                _mysql_query($query_sign);


                //修改佣金paytype=0
                $query_promotes = "update weixin_commonshop_order_promoters set paytype = 0 where batchcode='".$batchcode."'";
                _mysql_query($query_promotes);

                //发送拒绝签收消息
                if($is_sign == 2)
                {

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
                                // $shopmessage->SendMessage($send_con, $send_openid, $customer_id);
								$sendMessageContent[] = array(
														'openid' => $send_openid,
														'content'=> $send_con
													);
                            }else{
                                if ($is_extension_status>0) {
                                    //发送消息
                                    // $shopmessage->SendMessage($send_con, $send_openid, $customer_id);
									$sendMessageContent[] = array(
														'openid' => $send_openid,
														'content'=> $send_con
													);
                                }
                            }
                        }
                    }

                }

                //插入日志
                $query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) values('".$batchcode."',31,'平台确认签收订单','".$log_username."',now(),1)";
                _mysql_query($query_log);

                //$json["status"] = 0;
                //$json["line"] = 41;
                //$is_sign == 1 ? $json["msg"] = "编号：".$batchcode."，签收成功" : $json["msg"] = "编号：".$batchcode."，拒绝签收成功";
            }
        }
        else
        {
            $batchcode_same = false;
            $batchcode_array= array();
            $err_row = '';
            $repeat_arr = array();
            //查询是否有重复的快递单号
            for($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                $expressnum     = $data -> sheets[0]['cells'][$i][3];
                //去掉特殊符号
                $expressnum     = str_replace("'",'',$expressnum);
                $expressnum     = str_replace("＇",'',$expressnum);
                //获取所有快递单号数组
                if($expressnum != false){
                    $expressnum_array[] = $expressnum;
                }
            }
            // 获取去掉重复数据的数组 
            $unique_arr = array_unique ( $expressnum_array );
            // 获取重复数据的数组 
            $repeat_arr = array_diff_assoc ( $expressnum_array, $unique_arr );

            for($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                if($from_page == 2){
                    //F2C代发订单
                    if ( $order_way == 1 ){
                        $batchcode      = $data -> sheets[0]['cells'][$i][1];
                        $package_name   = $data -> sheets[0]['cells'][$i][6];
                        $express_remark = $data -> sheets[0]['cells'][$i][2];
                        $expressname    = $data -> sheets[0]['cells'][$i][4];
                        $expressnum     = $data -> sheets[0]['cells'][$i][3];
                    } else if ( $order_way == 2 ){
                        $batchcode      = $data -> sheets[0]['cells'][$i][1];
                        $package_name   = $data -> sheets[0]['cells'][$i][7];
                        $express_remark = $data -> sheets[0]['cells'][$i][2];
                        $expressname    = $data -> sheets[0]['cells'][$i][4];
                        $expressnum     = $data -> sheets[0]['cells'][$i][3];
                    }
                }else{
                    //非F2C代发订单
                    if ( $order_way == 1 ){
                        $batchcode      = $data -> sheets[0]['cells'][$i][1];
                        $package_name   = $data -> sheets[0]['cells'][$i][6];
                        $express_remark = $data -> sheets[0]['cells'][$i][4];
                        $expressname    = $data -> sheets[0]['cells'][$i][2];
                        $expressnum     = $data -> sheets[0]['cells'][$i][3];
                    } else if ( $order_way == 2 ){
                        $batchcode      = $data -> sheets[0]['cells'][$i][1];
                        $package_name   = $data -> sheets[0]['cells'][$i][7];
                        $express_remark = $data -> sheets[0]['cells'][$i][4];
                        $expressname    = $data -> sheets[0]['cells'][$i][2];
                        $expressnum     = $data -> sheets[0]['cells'][$i][3];
                    }
                }


                //去掉特殊符号
                $batchcode      = str_replace("'",'',$batchcode);
                $batchcode      = str_replace("＇",'',$batchcode);
                $expressnum     = str_replace("'",'',$expressnum);
                $expressnum     = str_replace("＇",'',$expressnum);

                if( !empty( $batchcode ) ){
                    //如果是重复快递单号的不做处理跳出本次循环
                    if($expressnum==false || $expressname==false){
                        //订单号为空的时候,记录行数
                        $err_row .= $i.',';
                        continue;
                    }

                    // if( 3 == $i ){
                    $express_id = -1;
                    //保障编号15815，此处从weixin_expresses表中获取的数据表并不是快递公司表，不能获取到快递公司编号，导致后面的$express_id的值为-1并且在微商城里显示不出物流方式和快递编号
                    //这里已注释旧sql语句，并且从weixin_expresses_company快递公司表里获取数据，测试后发现在前台能正常显示
                    //$query = 'SELECT id FROM weixin_expresses where isvalid=true and customer_id='.$customer_id.' and name="'.$expressname.'"';
                    $query = 'SELECT id FROM weixin_expresses_company where isvalid=true and customer_id='.$customer_id.' and expresses_name="'.$expressname.'"';
                    //echo $query;
                    $result=_mysql_query($query)or die('Query failed'.mysql_error());

                    while($row=mysql_fetch_object($result)){
                        $express_id = $row->id;
                    }
                    // }

                    if($f2c_id>0){
                        $query_sso="select is_accept from system_send_order where isvalid=true and order_id='".$batchcode."' limit 0,1";
                        //echo $i."=====". $query_user."<br>";
                        $result_sso = _mysql_query($query_sso) or die('Query_user1 failed: ' . mysql_error());
                        $sso = array();
                        while($row_sso = mysql_fetch_assoc($result_sso)){
                            $is_accept = $row_sso['is_accept'];//获取f2c接单状态
                        }
                    }

                    //return;
                    $user_id		= -1;//顾客id
                    $pid			= -1;//产品id
                    $agentcont_type = -1;//分佣路线
                    $sendstatus     = -1;//发送状态
                    $agent_id	    = -1;//代理商id
                    $is_QR 		    = 0;//是否发送二维码
                    $address_id 	= 0;//地址
                    $query_user="select paystatus,user_id,pid,totalprice,agent_id,agentcont_type,sendstatus,address_id,is_QR from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."' limit 0,1";
                    //echo $i."=====". $query_user."<br>";
                    $result_user = _mysql_query($query_user) or die('Query_user1 failed: ' . mysql_error());

                    while ($row_user = mysql_fetch_object($result_user)) {
                        $user_id        = $row_user->user_id;
                        $pid            = $row_user->pid;
                        $agentcont_type = $row_user->agentcont_type;
                        $totalprice     = $row_user->totalprice; 	//订单总金额
                        $sendstatus     = $row_user->sendstatus;
                        $agent_id       = $row_user->agent_id;
                        $address_id     = $row_user->address_id;
                        $is_QR    	    = $row_user->is_QR;
                        $paystatus      = $row_user->paystatus;

                        //产品信息
                        $agent_discount = 0;//产品代理商折扣
                        $product_name   = "";//产品名
                        $query_product = "select agent_discount,name from weixin_commonshop_products where isvalid=true and id=".$pid;
                        $result_product = _mysql_query($query_product) or die('Query_product failed: ' . mysql_error());
                        while ($row_product = mysql_fetch_object($result_product)) {
                            $agent_discount = $row_product->agent_discount;
                            $product_name   = $row_product->name;
                            $product_name   = "".$product_name."";
                        }


                        /* 代理商扣除库存 */
                        if($agent_id > 0 and $agentcont_type == 1 and $sendstatus == 0){

                            $agent_inventory = 0;
                            //查找代理商代理剩余库存金额
                            $query_promote = "select agent_inventory from promoters where status=1 and isvalid=true and user_id=".$agent_id;
                            $result_promote = _mysql_query($query_promote) or die('Query_promote failed: ' . mysql_error());
                            while ($row_promote  = mysql_fetch_object($result_promote)) {
                                $agent_inventory = $row_promote->agent_inventory;
                            }
                            if( $agent_discount == 0 ){
                                $query_apply = "select agent_discount from weixin_commonshop_applyagents where status=1 and isvalid=true and user_id=".$agent_id;
                                $result_apply = _mysql_query($query_apply) or die('Query_apply failed: ' . mysql_error());
                                $agent_discount = 0;
                                while ($row_apply   = mysql_fetch_object($result_apply)) {
                                    $agent_discount = $row_apply->agent_discount;//代理商折扣
                                }
                            }
                            $agent_discount 		   =  $agent_discount/100;
                            $agent_cost_inventorymoney = $totalprice * $agent_discount;	//本价
                            //从代理金额扣除成本价
                            $agent_cost_inventorymoney = round($agent_cost_inventorymoney,2);
                            $agent_inventory = $agent_inventory - $agent_cost_inventorymoney;

                            $agent_cost_inventorymoney2 = 0-$agent_cost_inventorymoney;
                            //插入日志
                            $query_Irecord = "insert into weixin_commonshop_agentfee_records(user_id,batchcode,price,detail,type,isvalid,createtime,after_inventory) values(".$agent_id.",'".$batchcode."',".$agent_cost_inventorymoney2.",'发货(出库)',1,true,now(),".$agent_inventory.")";
                            _mysql_query($query_Irecord);
                            //更新库存
                            $query_Upromote = "update promoters set agent_inventory=agent_inventory-".$agent_cost_inventorymoney." where user_id=".$agent_id;
                            _mysql_query($query_Upromote);
                        }

                        /* 代理商扣除库存 End */
                    }

                    if( $sendstatus != 0 ){	//非未发货状态不执行下面的代码
                        continue;
                    }


                    if($f2c_id>0){
                        //判断状态是否是待发货状态
                        if($paystatus == true && $sendstatus == 0 && $is_accept == true){
                            //是发货状态，进行导入
                            //查询仓库管理者的user_id
                            $query_account = "SELECT user_id FROM f2c_accounts WHERE customer_id={$customer_id} and id = {$f2c_id}";
                            $result_account = _mysql_query($query_account) or die('Query_product failed: ' . mysql_error());
                            $account = array();
                            while($row_account = mysql_fetch_assoc($result_account)){
                                $account[] = $row_account;
                            }
                            $f2c_user_id = $account[0]['user_id'];

                            //查询对应仓库的对应库存,如果有库存则发货，如果无库存不发货
                            $query_order1 = "SELECT o.id,o.pid,o.pname,o.user_id,o.rcount,o.prvalues,o.prvalues_name,o.totalprice,p.default_imgurl,p.name,o.return_status,o.return_type FROM weixin_commonshop_orders o INNER JOIN weixin_commonshop_products p on o.pid=p.id WHERE o.batchcode = {$batchcode} and o.isvalid = true and o.customer_id = {$customer_id}";
                            $result_order1 = _mysql_query($query_order1) or die('Query_product failed: ' . mysql_error());
                            $order_items =array();
                            while($row_order1 = mysql_fetch_assoc($result_order1)){
                                $order_items[]    =   $row_order1;
                            }
                            $num = count($order_items);

                            $info_query = "SELECT p.*,o.return_type,o.return_status,o.sendstatus,o.user_id FROM weixin_commonshop_order_prices p LEFT JOIN weixin_commonshop_orders o on p.batchcode = o.batchcode WHERE p.batchcode={$batchcode} and o.is_sendorder=true and p.isvalid=true limit 1";
                            $result_info = _mysql_query($info_query) or die('Query_product failed: ' . mysql_error());
                            $order_info =array();
                            while($row_info = mysql_fetch_assoc($result_info)) {
                                $order_info = $row_info;
                            }

                            $sql_store = "select count(pro.id) pro_row from f2c_warehouse pro , weixin_commonshop_orders o where o.batchcode = '{$batchcode}' and o.prvalues = pro.proids and o.pid = pro.product_id and pro.stock >= o.rcount and pro.user_id = ".$f2c_user_id;
                            $result_store = _mysql_query($sql_store) or die('Query_product failed: ' . mysql_error());
                            $row_count =array();
                            while($row_store = mysql_fetch_assoc($result_store)){
                                $row_count[]    =   $row_store;
                            }
                            if($row_count[0]["pro_row"] >= $num){ //如果获取出的总数和订单中明细的商品项一样的话代表每个商品库存都够
                                //有库存
                            }else{
                                //无库存
                                $is_enough = 1;
                                continue;
                            }
                        }else{
                            //不是发货状态，跳出本次循环
                            $alert = 1;
                            continue;
                        }
                    }

                    /* 查询OpenID */
                    $order_fromuser = "";
                    $query_user="select weixin_fromuser from weixin_users where isvalid=true and id=".$user_id." limit 0,1";
                    $result_user = _mysql_query($query_user) or die('Query_user2 failed: ' . mysql_error());
                    while ($row_user = mysql_fetch_object($result_user)) {
                        $order_fromuser = $row_user->weixin_fromuser;
                    }
                    /* 查询OpenID End */

                    if($express_id== -2 ){
                        require_once('../../../../weixinpl/back_newshops/Order/sf/lib/orderService.php'); // 顺丰接口
                        $query3="select name,phone,address,location_p,location_c,location_a,identity from weixin_commonshop_order_addresses where  batchcode='".$batchcode."'";
                        $result3 = _mysql_query($query3) or die('Query_168 failed: ' . mysql_error());
                        $order_username  = "";
                        $order_userphone = "";
                        $order_address   = "";
                        $identity        = "";
                        $location_p      = "";
                        $location_c      = "";
                        $location_a      = "";
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
                            $result3 = _mysql_query($query3) or die('Query failed48: ' . mysql_error());
                            while ($row3 = mysql_fetch_object($result3)) {
                                $order_username  = $row3->name;
                                $order_userphone = $row3->phone;
                                $order_address   = $row3->address;
                                $location_p      = $row3->location_p;
                                $location_c		 = $row3->location_c;
                                $location_a		 = $row3->location_a;
                            }
                        }
                        $query_re_sf="select * from sf_import where customer_id=$customer_id and ison=1";
                        $re_sf=_mysql_query($query_re_sf) or die("查询顺丰进口业数据表务表失败!");
                        $l_sf=mysql_num_rows($re_sf);
                        if(!$l_sf){
                            die("没有配置顺丰进口参数!");
                        }else{
                            $row_sf		   		= mysql_fetch_object($re_sf);
                            $head			    = $row_sf->head;
                            $token		   		= $row_sf->token;
                            $authToken	   		= $row_sf->authToken;
                            $businessLogo  		= $row_sf->businessLogo;
                            $Sendcompany   		= $row_sf->Sendcompany;
                            $Sendconcact   		= $row_sf->Sendconcact;
                            $Sendtelphone  		= $row_sf->Sendtelphone;
                            $Sendmobile    		= $row_sf->Sendmobile;
                            $Sendcountry   		= $row_sf->Sendcountry;
                            $Sendprovinoce 		= $row_sf->Sendprovinoce;
                            $Sendcitycode  		= $row_sf->Sendcitycode;
                            $Sendcity	   		= $row_sf->Sendcity;
                            $Sendzipcode   		= $row_sf->Sendzipcode;
                            $Sendaddress   		= $row_sf->Sendaddress;
                            $Sendcounty    		= $row_sf->Sendcounty;
                            $monthlyAccount 	= $row_sf->monthlyAccount;
                            $customsBatchNumber = $row_sf->customsBatchNumber;
                            $taxSetAccounts     = $row_sf->taxSetAccounts;
                            $checkWord          = $row_sf->checkWord;

                            $xmlArray = array(
                                '@attributes'   => array(
                                    'service'   => 'otherOrderService',
                                    'lang' 	    => 'zh_cn',
                                    'printType' => '2',
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

                            $Api       = new orderService;
                            $re_xml	   =$Api->getOrderData($xml,$arr);
                            $xml_obj   = simplexml_load_string($re_xml,'SimpleXMLElement', LIBXML_NOCDATA);
                            $errorCode = $xml_obj->errorCode;
                            $errorDesc = $xml_obj->errorDesc;
                            $mailNo    = $xml_obj->mailNo;
                            $printUrl  = $xml_obj->printUrl;
                            if($errorCode=="001"){
                                $express_num=$mailNo;
                            }else{
                                mysql_close($link);
                                $jsons=json_encode($json);
                                die($jsons);
                                exit;
                            }
                        }
                    }

                    /* 顺风进口接口 End */

                    $query_cus = " select auto_cus_time from weixin_commonshops where isvalid = true and customer_id = ".$customer_id;
                    $result_cus = _mysql_query($query_cus);
                    $auto_cus_time = mysql_result($result_cus,0,0);
                    if(empty($auto_cus_time) || $auto_cus_time <= 0){ //如没有设置时间默认为7天
                        $auto_cus_time = 7;
                    }
                    $query_Uorder = '';

                    if($express_id==-2 && $errorCode==001){ //修改默认的自动收货时间
                        //如果是顺丰进口业务
                        $query_Uorder = "update weixin_commonshop_orders set sendway=1,sendstatus = 1, expressname = '".$expressname."',confirm_sendtime=now(),auto_receivetime = DATE_ADD( now(), INTERVAL ".$auto_cus_time." DAY ),send_express_id=".$express_id.",expressnum='".$expressnum."',send_remarks='".$express_remark."',printUrl='".$printUrl."' where batchcode='".$batchcode."'";
                    }else if(!empty($expressname) && !empty($expressnum) && $sendstatus == 0 ){

                        /* 发送Message */
                        $content = "亲，您有一笔订单【已发货】\n\n商品：".$product_name."\n时间：".date( "Y-m-d H:i:s")."\n快递：".$expressnum."";
                        if(!empty($express_remark)){
                            $content=$content."\n备注：".$express_remark;
                        }

                        $kd_href = Protocol.$http_host."/weixinpl/back_newshops/Distribution/settings/kuaidi_head.php?is_web=1&customer_id=".passport_encrypt((string)$customer_id)."&batchcode=".$batchcode."&postid=".trim($expressnum)."&type=".$expressname;

                        $content=$content."\n\n<a href='".$kd_href."'>【查看物流进度】</a>\n<a href='".Protocol.$http_host."/weixinpl/mshop/orderlist_detail.php?batchcode=".$batchcode."&customer_id=".passport_encrypt((string)$customer_id)."&fromuser=".$order_fromuser."'>【查看订单详情】</a>";

                        // $shopmessage->SendMessage($content,$order_fromuser,$customer_id);
                        $sendMessageContent[] = array(
                                                'openid' => $order_fromuser,
                                                'content'=> $content
                                            );
                        /* 发送Message End */
                        $query_Uorder = "update weixin_commonshop_orders set sendway=1,sendstatus = 1, expressname = '".$expressname."',confirm_sendtime=now(),auto_receivetime = DATE_ADD( now(), INTERVAL ".$auto_cus_time." DAY ),send_express_id=".$express_id.",expressnum='".$expressnum."',send_remarks='".$express_remark."' where batchcode='".$batchcode."'";
                    }else if($express_id==0){
                        $query_Uorder = "update weixin_commonshop_orders set sendway=1,sendstatus = 2, expressname = '".$expressname."',confirm_sendtime=now(),confirm_receivetime=now(),send_express_id=".$express_id.",expressnum='".$expressnum."',send_remarks='".$express_remark."' where batchcode='".$batchcode."'";
                    }else if($from_page == 2 && $f2c_id>0 && $express_id==-1 && !empty($express_remark) && !empty($expressnum) && $sendstatus == 0){
                        //F2C店导出特殊情况  不填备注时 $express_name == false
                        /* 发送Message */
                        $content = "亲，您有一笔订单【已发货】\n\n商品：".$product_name."\n时间：".date( "Y-m-d H:i:s")."\n快递：".$expressnum."";
                        if(!empty($express_remark)){
                            $content=$content."\n备注：".$express_remark;
                        }
                        $content=$content."\n\n<a href='http://m.kuaidi100.com/result.jsp?nu=".$expressnum."'>【查看物流进度】</a>\n<a href='".Protocol.$http_host."/weixinpl/common_shop/jiushop/order_detail.php?batchcode=".$batchcode."&customer_id=".passport_encrypt((string)$customer_id)."&fromuser=".$order_fromuser."'>【查看订单详情】</a>";
                        // $shopmessage->SendMessage($content,$order_fromuser,$customer_id);
						$sendMessageContent[] = array(
												'openid' => $order_fromuser,
												'content'=> $content
											);
                        /* 发送Message End */
                        $query_Uorder = "update weixin_commonshop_orders set sendway=1,sendstatus = 0, expressname = '".$expressname."',confirm_sendtime=now(),auto_receivetime = DATE_ADD( now(), INTERVAL ".$auto_cus_time." DAY ),send_express_id=".$express_id.",expressnum='".$expressnum."',send_remarks='".$express_remark."' where batchcode='".$batchcode."'";
                    }

                    if ( $query_Uorder != '' ){
                        //添加发货日志
                        _mysql_query($query_Uorder) or die("Query_Uorder error : ".mysql_error());

                        //添加发货日志
                        if($f2c_id>0){
                            $query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) 
					values('".$batchcode."',4,'F2C店发货[物流：".$express_remark.",单号：".$expressnum."]','F2C店:".$f2c_user_id."',now(),1)";
                            _mysql_query($query_logs) or die("L365 query error  : ".mysql_error());
                            $i = 0;//商品个数

                            if($order_info["sendstatus"] == 0) { //售后状态的换货发货不用扣库存

                                //扣库存,加销售额
                                foreach ($order_items as $item) {
                                    if($i<3){
                                        $name_str .= "、<".$item['name'].">";
                                        $is_over = 0;
                                    }else{
                                        $is_over = 1;
                                    }
                                    $i++;
                                    $rcount = $item["rcount"];
                                    $query_warehouse = "update f2c_warehouse set stock = stock - {$rcount}, sales_online = sales_online + {$rcount} WHERE product_id = {$item["pid"]} and proids = '{$item["prvalues"]}' and isvalid =true and user_id = {$f2c_user_id}";
                                    $result_warehouse = _mysql_query($query_warehouse) or die("L365 query error  : ".mysql_error());

                                    //库存销量修改 End

                                    //库存提醒
                                    $query_stocks = "SELECT stock FROM f2c_warehouse WHERE isvalid = true and product_id={$item['pid']} and proids='{$item['prvalues']}' and user_id = {$f2c_user_id} limit 1";
                                    $result_stocks = _mysql_query($query_stocks) or die("L365 query error  : ".mysql_error());
                                    $stocks = array();
                                    while($row_stocks = mysql_fetch_assoc($result_stocks)){
                                        $stocks = $row_stocks;
                                    }
                                    $now_time = date('Y-m-d H:i:s',time());
                                    if(!empty($stocks)){
                                        if($stocks["stock"] >= 0){
                                            //查询产品名
                                            $query_products = "SELECT name FROM weixin_commonshop_products WHERE id = {$item['pid']} ";
                                            $result_products = _mysql_query($query_products) or die("L365 query error  : ".mysql_error());
                                            $product_info = array();
                                            while($row_products = mysql_fetch_assoc($result_products)){
                                                $product_info[] = $row_products;
                                            }
                                            $product_name = $product_info[0]["name"];
                                            //查询属性名
                                            if($item['prvalues'] != false){
//                                                $Pos_arr         = str_replace("_",",",$item["prvalues"]);
                                                $Pos_arr         = str_replace("_",",",$item['prvalues']);
                                                $Pos_name        = '';
                                                $att_name        = "";//产品属性名
                                                $att_parent_name = "";//产品属性上级名
                                                $query_pros      = "SELECT parent_name,name FROM weixin_commonshop_pros WHERE id in ({$Pos_arr})";
                                                $result_pros = _mysql_query($query_pros) or die("L365 query error  : ".mysql_error());
                                                $pro_info = array();
                                                while($row_pros = mysql_fetch_assoc($result_pros)){
                                                    $pro_info[] = $row_pros;
                                                }
                                                if($pro_info != false){
                                                    foreach($pro_info as $k=>$v){
                                                        $att_name         = $pro_info[$k]['name'];
                                                        $att_parent_name  = $pro_info[$k]['parent_name'];
                                                        $Pos_name .= $att_parent_name.":".$att_name." ";
                                                    }
                                                }else{
                                                    $Pos_name = '';
                                                }
                                            }
                                            if(!empty($Pos_name)){
                                                $product_name = $product_name." 属性：".$Pos_name;
                                            }
                                            //查询用户信息
                                            $query_user1 = "SELECT weixin_fromuser from weixin_users WHERE id = {$f2c_user_id} and isvalid = true";
                                            $result_user1 = _mysql_query($query_user1) or die("L365 query error  : ".mysql_error());
                                            $user_info1 = array();
                                            while($row_user1 = mysql_fetch_assoc($result_user1)){
                                                $user_info1 = $row_user1;
                                            }

                                            $msg = "亲，您的F2C店产品库存已不足，产品剩余0件。\r\n".
                                                "产品：【".$product_name."】\n".
                                                "来源：【F2C系统】\n".
                                                "备注：【F2C店产品库存提醒】\n".
                                                "时间：{$now_time}";

                                            //发送消息
                                            // $shopmessage->SendMessage($msg,$user_info1["weixin_fromuser"],$customer_id);
											$sendMessageContent[] = array(
																	'openid' => $user_info1["weixin_fromuser"],
																	'content'=> $msg
																);
                                        }
                                    }
                                    //库存提醒 End

                                    //库存日志
                                    $query_stock_log = "INSERT INTO f2c_warehouse_log (`user_id` , `customer_id` , `product_id` , `proids` , `num` , `line` , `from` , `type` , `isvalid` , `createtime` , `batchcode`) VALUES ({$f2c_user_id} , {$customer_id} , {$item['pid']} , '{$item['prvalues']}' , {$item['rcount']} , 'on' , 1 , 2 , 1 , '{$now_time}' , '{$batchcode}')";
                                    $res = _mysql_query($query_stock_log) or die("L365 query error  : ".mysql_error());
                                }
                            }else{
                                //无需扣库存
                            }
                        }else{
                            $query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) 
					values('".$batchcode."',4,'平台发货[物流：".$expressname.",单号：".$expressnum."]','".$log_username."',now(),1)";
                            _mysql_query($query_logs) or die("L365 query error  : ".mysql_error());
                        }


                        //券类订单发放二维码
                        if($is_QR==1){
                            $shopmessage->GetQR($batchcode,$order_fromuser,$customer_id);
                        }

                    }
                }
            }

        }
		
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
    }

}



$error =mysql_error();
mysql_close($link);
//echo $error;
//echo $parent_id;
if($f2c_id >0){
    if($is_enough == 1){
        $str1 = "存在库存不足的订单，无法导入库存不足的订单的快递单号,";
    }
    if($alert == 1){
        $str2 = "存在待处理状态的订单，无法导入待处理订单的快递单号,";
    }
    if($is_enough || $alert){
        echo "<script>alert('{$str2}{$str1}待发货的订单已正常导入快递单号');
        window.location.href='/addons/index.php/f2c/anchor/f2c_order.php';</script>";
    }else{
        echo "<script>location.href='/addons/index.php/f2c/anchor/f2c_order.php';</script>";
    }
    die();
}

if($err_row != false || $repeat_arr != false){

    $alert_str1='';
    $alert_str2='';
    if($err_row){
        $err_row = trim($err_row,',');
        $alert_str1 = "第{$err_row}行，快递名称或快递单号为空，导入失败！";
    }
    if($repeat_arr){
        foreach ($repeat_arr as $v){
            $num .= $v.',';
        }
        $num = rtrim($num,',');
        $alert_str2 = "快递单号：{$num}重复，导入失败！";
    }
    $alert_str = $alert_str1.$alert_str2.'其他正常导入！';
}else{
    $alert_str = '导入成功!';
}
echo "<script>alert('{$alert_str}');location.href='order.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
?>