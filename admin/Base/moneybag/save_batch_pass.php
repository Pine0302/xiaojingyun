<?php
header("Content-type: text/html; charset=utf-8");
require_once('../../../../wsy_pay/web/IPSpay/IPSfun.php');
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require_once('../../../../weixinpl/proxy_info.php');

require('../../../../weixinpl/common/utility_shop.php');
_mysql_query("SET NAMES UTF8");

require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
$database = new \Key\DB();

// 连接数据库
$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);
$ips=new IPS($customer_id);
mb_internal_encoding('utf-8');

$op        = $_POST['op'];
$box_arr   = $_POST["box_arr"];
$gathering_name   = $_POST["gathering_name"];
$box_arr   = json_decode($box_arr,true);//json转数组

$success_batch_pass = 0; //批量审批通过总数
$error_batch_pass = 0;  //批量审批失败总数
for( $i=0; $i < count($box_arr); $i++ )
{
    if( $box_arr[$i]['cash_type'] == "微信零钱" )
    {
        $w_user_id          = $box_arr[$i]['user_id'];
        $w_id               = $box_arr[$i]['id'];
        $w_batchcode        = $box_arr[$i]['batchcode'];
        $w_real_cash        = $box_arr[$i]['real_cash'];
        $w_AccTime_E        = $box_arr[$i]['AccTime_E'];
        $w_AccTime_B        = $box_arr[$i]['AccTime_B'];
        $w_pagenum          = $box_arr[$i]['pagenum'];
        $w_search_cashtype  = $box_arr[$i]['search_cashtype'];
        $w_search_status    = $box_arr[$i]['search_status'];

        //调用微信零钱提现退款接口
        $url = $_SERVER['HTTP_HOST']."/weixinpl/mshop/WeChatPay/WeChat_ToPay.php?customer_id=$customer_id_en&uid=$w_user_id&kid=$w_id&b=$w_batchcode&AccTime_E=$w_AccTime_E&AccTime_B=$w_AccTime_B&pagenum=$w_pagenum&search_cashtype=$w_search_cashtype&search_status=$w_search_status&batch_operate=1";
        _get_curl($url);

        $check_query = "select id,status from weixin_cash_being_log where id=".$w_id." and batchcode='".$w_batchcode."'";            //判断当前微信零钱提现是否成功
        $check_res = _mysql_query($check_query);
        while($row=mysql_fetch_object($check_res)) {
            $check_status = $row->status;
        }

        if($check_status == 1){                            //判断审批是否成功，增加总数
            $success_batch_pass++;
        }else{
            $error_batch_pass++;
        }

    }
    else if( $box_arr[$i]['cash_type'] == "环迅账户" )
    {
        $query  = "select id,user_id,batchcode,getmoney,person_information from weixin_cash_being_log where isvalid=true AND batchcode='".$box_arr[$i]['batchcode']."'";
        $result = _mysql_query($query);

        while($row=mysql_fetch_object($result)) {
            $person_information    = (array)json_decode($row->person_information);
            $box_arr[$i]['price'] = $row->getmoney;
            $box_arr[$i]['name']  = $person_information['real_name'];
            $box_arr[$i]['user_id'] = $row->user_id;
        }

//        $box_arr[$i]['gathering_name'] = explode(',',$box_arr[$i]['gathering_name']);

        $id = $box_arr[$i]['id'];                                                                                                   //配置参数
        $type = 'user';
        $data['batchcode']  = $box_arr[$i]['batchcode'];
        $data['account']    = $box_arr[$i]['person_bind_account'];
        $data['price']      = cut_num($box_arr[$i]['real_cash'],2);
        $data['name']       = $gathering_name;

        $xml = $ips->setmessage(7,$data);
        $url = 'https://ebp.ips.com.cn/fpms-access/action/trade/transfer.do';
        $box_arr[$i]['result'] = transfer($account,$xml,$url,$id,$data['batchcode'],$type,$customer_id,$data['price'],$box_arr[$i]['user_id']);            //调用环迅的接口提现

        if($box_arr[$i]['result']['code'] == 1){                            //判断审批是否成功，增加总数
            $error_batch_pass++;
        }else if($box_arr[$i]['result']['code'] == 0){
            $success_batch_pass++;
        }
    }
    else
    {
        $w_batchcode            = $box_arr[$i]['batchcode'];
        $save_pay_result = save_pay($w_batchcode, $customer_id);

        if($save_pay_result['status'] == 401){                              //判断审批是否成功，增加总数
            $success_batch_pass++;
        }else{
            $error_batch_pass++;
        }

        $json['curl_result'] = $save_pay_result;
    }
}

/*这里是get方式的curl*/
function _get_curl($url)
{

    if(!$url)
    {
        $result['code'] = 40000;
        $result['msg']  = '参数丢失';
    }
    else
    {
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); //设置超时时间为15秒
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $info = curl_exec($ch);
        $info = json_decode($info,true);
        if($info === false)
        {
            $result['code'] = 40000;
            $result['msg']  = '获取失败';
            if(curl_errno($ch) == CURLE_OPERATION_TIMEDOUT)
            {
                //超时的处理代码
                $result['code'] = 40000;
                $result['msg']  = '网络超时';
            }
        }
        else
        {
            $result['code'] = 20000;
            $result['msg']  = '获取成功';
            $result['data'] = $info;

            if($info['result'] != 1)
            {
                $result['code'] = 40000;
                $result['msg']  = $info['msg'];
                unset($result['data']);
            }

        }

        return $result;

    }
}

/*环迅支付提现*/
function transfer($account,$xml,$url,$id,$batchcode,$type,$customer_id,$price,$user_id){
    global $ips;
    global $database;
    $post_data = array('ipsRequest' => $xml );
    $post_data = http_build_query($post_data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);         // 要访问的地址
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1 );
    curl_setopt($ch, CURLOPT_HEADER, 0);                         // 显示返回的Header区域内容
    curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);    // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');      // 模拟用户使用的浏览器
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);     // 使用自动跳转
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);             // 自动设置Referer
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);                  // Post提交的数据包
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);                      // 设置超时限制防止死循环
    $curl_error = curl_error($ch);
    $data = curl_exec($ch);
    curl_close($ch);

    $xml = new DOMDocument();
    $xml->loadXML($data);
    $argMerCode = $xml->getElementsByTagName('argMerCode')->item(0)->nodeValue;
    $rspCode = $xml->getElementsByTagName('rspCode')->item(0)->nodeValue;
    $rspMsg = $xml->getElementsByTagName('rspMsg')->item(0)->nodeValue; // 错误原因
    $p3DesXmlPara = $xml->getElementsByTagName('p3DesXmlPara')->item(0)->nodeValue;
    $p3DesXmlPara = $ips->decrypt($p3DesXmlPara);

    _file_put_contents('ips_operation.log', "时间:{$time}\n\r回调返回信息解析后p3DesXmlPara:{$p3DesXmlPara};\n\r",FILE_APPEND);
    // $p3DesXmlPara = '<transferRespXml><head><reqDate>2017-05-17 10:24:37</reqDate><respDate>2017-05-17 10:24:38</respDate><signature>bf9b8c2c6265fc98f90202b60aebc5cb</signature></head><body><ipsBillNo>FPDD201705170000000828</ipsBillNo><tradeId>FPFK201705170000000828</tradeId><ipsFee>0.00</ipsFee><tradeState>9</tradeState></body></transferRespXml>';
    // xml->json->多维数组
    libxml_disable_entity_loader(true);
    $xmlstring = simplexml_load_string($p3DesXmlPara, 'SimpleXMLElement', LIBXML_NOCDATA);
    $data = json_decode(json_encode($xmlstring),true);
    $body = $data['body'];
    $head = $data['head'];

    $ipsFee     = $body['ipsFee']; // 手续费;
    $tradeId    = $body['tradeId']; // 交易编号;
    $ipsBillNo  = $body['ipsBillNo']; // Ips订单号;
    // $ipsBillNo  = '123456789'; // Ips订单号;

    $shopmessage= new shopMessage_Utlity();
    $weixin_fromuser = '';
    $query_weixinfromuser = "SELECT weixin_fromuser FROM weixin_users WHERE isvalid=true AND id = ".$user_id." LIMIT 1";            //获取用户的微信openid，之后发送信息到微信账户上
    $result_weixinfromuser= _mysql_query($query_weixinfromuser) or die('Query failed 82: ' . mysql_error());
    while( $row = mysql_fetch_object($result_weixinfromuser) ){
        $weixin_fromuser = $row->weixin_fromuser;
    }

    if( $type=='user' ){
        $sql_true = "UPDATE weixin_cash_being_log SET status = 1,callback_fee_flxed='{$ipsFee}', processing_time=now(),serial_number='{$ipsBillNo}' WHERE isvalid=true AND id='{$id}'";
    }else{
        $sql_true = "UPDATE weixin_commonshop_withdrawals SET status = 2,confirmtime=now(),serial_number='{$ipsBillNo}' WHERE isvalid=true AND id='{$id}'";
    }
    _file_put_contents('ips_operation.log', "\n\r时间:{$time}===sql_true:{$sql_true};\n\r",FILE_APPEND);

    if( $rspCode == 'M999999'){
        $result["return_code"]  = $rspCode;
        $result["return_msg"]   = $rspMsg;
        $result["err_code_des"] = $rspMsg;
        $error_message = json_encode($result);
        $error_message = addslashes($error_message);
        if( $type=='user' ){
            $sql = "UPDATE weixin_cash_being_log SET return_result='{$error_message}' WHERE isvalid=true AND id=".$id;                  //获取错误信息，写入数据库
            $database->query($sql);
        }

        $result['msg'] = $rspMsg;
        $result['code'] = 1;

        $msg_content = 	"亲，您申请环迅账号提现".$price."元\r\n".
            "状态：请求失败\n".
            "原因：".$rspMsg."\n".
            "时间：".date( "Y-m-d H:i:s")."";
        $shopmessage->SendMessage($msg_content,$weixin_fromuser,$customer_id);                                      //返回提现消息给用户
        return $result;
    }

    if( $body['tradeState'] == 10 ){
        $result["return_code"]  = $body['tradeState'];
        $result["return_msg"]   = $rspMsg;
        $result["err_code_des"] = $rspMsg;
        $error_message = json_encode($result);
        $error_message = addslashes($error_message);
        if( $type=='user' ){
            $sql = "UPDATE weixin_cash_being_log SET return_result='{$error_message}' WHERE isvalid=true AND id=".$id;
            $database->query($sql);
        }

        $result['msg'] = '请求失败';
        $result['code'] = 1;
    }else if( $body['tradeState'] == 9 ){
        /*$ipsFee     = $body['ipsFee']; // 手续费;
        $tradeId    = $body['tradeId']; // 交易编号;
        $ipsBillNo  = $body['ipsBillNo']; // Ips订单号;
        $sql = "UPDATE weixin_cash_being_log SET status = 1,callback_fee_flxed='{$ipsFee}', processing_time=now() WHERE isvalid=true AND batchcode='{$batchcode}'";*/
        $database->query($sql_true);
        _file_put_contents('ips_operation.log', "时间:{$time}\n\rsql_true:{$sql_true};\n\r",FILE_APPEND);
        $result['code'] = 0;
        $result['serial_number'] = $ipsBillNo;
        $result['msg'] = '请求成功';
    }else{
        $result['msg'] = '请求失败';
        $result['code'] = 1;
    }

    if( $result['code'] == 0){                                                                                    //判断接口回调的参数，发送信息到微信账户上
        $msg_content = 	"亲，您申请环迅账号提现".$price."元\r\n".
            "状态：已同意申请\n".
            "时间：".date( "Y-m-d H:i:s")."";
    }else{
        $msg_content = 	"亲，您申请环迅账号提现".$price."元\r\n".
            "状态：请求失败\n".
            "原因：".$result['msg']."\n".
            "时间：".date( "Y-m-d H:i:s")."";
    }
    $shopmessage->SendMessage($msg_content,$weixin_fromuser,$customer_id);
    return $result;
}

function cut_num($menber,$places){
    $places = $places+1;
    $num = substr(sprintf("%.".$places."f", $menber),0,-1);
    return $num;
}

/*除了微信零钱、环迅支付之外的第三种情况*/
function save_pay($batchcode, $customer_id){

    $shopmessage= new shopMessage_Utlity();
    $currency_function = new Currency();

    $user_id 		= -1;

    $json = array();
    $json['customer_id'] = $customer_id;
    $json['batchcode'] = $batchcode;

    $getmoney 		= 0;
    $customer_id 	= passport_decrypt((string)$customer_id);
    $getmoney = 0;			//申请提现的金额
    $surplus_type = 0;		//0:全额提现 1：直接扣取 2：返购物币
    $percentage = 0;		//折现率（千分比）
    $arrival_money = 0; 	//到账的金额
    $counter_money = 0;		//需要扣取的金额 不用收手续费就 0
    $fee = 0;				//手续费
    $currency = 0;			//购物币

    $query = "SELECT id,getmoney,user_id,surplus_type,percentage,callback_fee,callback_fee_flxed,callback_currency,give_currency,cash_type FROM weixin_cash_being_log WHERE isvalid=true AND status = 0 AND batchcode ='".$batchcode."' LIMIT 1";
    $result= _mysql_query($query) or die('Query failed 27: ' . mysql_error());
    while( $row = mysql_fetch_object($result) ){
        $being_log_id       = $row->id;
        $getmoney 			= $row->getmoney;
        $user_id 			= $row->user_id;
        $surplus_type 		= $row->surplus_type;
        $percentage 		= $row->percentage;
        $callback_fee 		= $row->callback_fee;
        $callback_fee_flxed = $row->callback_fee_flxed;
        $callback_currency 	= $row->callback_currency;
        $give_currency	 	= $row->give_currency;
        $cash_type	 		= $row->cash_type;
    }

    $CRusult = $currency_function->insert_currency($user_id,$customer_id);

    $weixin_fromuser = '';
    $query = "SELECT weixin_fromuser FROM weixin_users WHERE isvalid=true AND id = $user_id LIMIT 1";
    $result= _mysql_query($query) or die('Query failed 82: ' . mysql_error());
    while( $row = mysql_fetch_object($result) ){
        $weixin_fromuser = $row->weixin_fromuser;
    }

    if( $surplus_type != 0 ){
        $fee 		= 0;	//手续费
        $currency 	= 0;	//购物币
        if( $callback_fee > 0 ){
            $fee = $getmoney * $callback_fee / 100;	//需要扣取的手续费
        } else if( $callback_fee_flxed > 0 ){
            $fee = $callback_fee_flxed;	//需要扣取的手续费
        }
        if( $callback_currency > 0 ){
            $currency = $getmoney * $callback_currency / 100;	//需要返的购物币
        }

        $to_cash = round(($getmoney-$fee-$currency),2);		//实际到账的钱

        if( $to_cash < 0 ){	//不能为负数
            $to_cash = 0;
        }

        $msg_content = 	"亲，您申请零钱提现".$to_cash."元\r\n".
            "状态：已同意申请\n".
            "时间：".date( "Y-m-d H:i:s")."";
        $shopmessage->SendMessage($msg_content,$weixin_fromuser,$customer_id);

        if( $currency > 0 ){	//返购物币

            $custom = "购物币";
            $query = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid = true AND customer_id = $customer_id LIMIT 1";
            $result= _mysql_query($query) or die('Query failed 59: ' . mysql_error());
            while( $row = mysql_fetch_object($result) ){
                $custom = $row->custom;
            }
            $remark = "零钱提现返".$currency.$custom;

            $currency_function->update_currency($user_id,$customer_id,$currency,1,$being_log_id,$remark,3,0);

            $msg_content = 	"亲，您的".$custom."增加了".$currency."\r\n".
                "来源：零钱提现返".$custom."\n".
                "时间：".date( "Y-m-d H:i:s")."";
            $shopmessage->SendMessage($msg_content,$weixin_fromuser,$customer_id);
        }
        $user_give_currency = $getmoney * $give_currency / 100;	//赠送的的购物币
        if( $user_give_currency >= 0.01 ){
            $custom = "购物币";
            $query = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid = true AND customer_id = $customer_id LIMIT 1";
            $result= _mysql_query($query) or die('Query failed 59: ' . mysql_error());
            while( $row = mysql_fetch_object($result) ){
                $custom = $row->custom;
            }
            $remark = "零钱提现赠送".$user_give_currency.$custom;
            $currency_function->update_currency($user_id,$customer_id,$user_give_currency,1,$being_log_id,$remark,16,0);
        }
        if($cash_type==4){

        }else{
            //改变提现状态
            $query = "UPDATE weixin_cash_being_log SET status = 1,processing_time=now() WHERE isvalid=true AND batchcode='".$batchcode."'";
            _mysql_query($query) or die('Query failed 70: ' . mysql_error());
        }



    }else{
        if($cash_type==4){

        }else{
            //改变提现状态
            $query = "UPDATE weixin_cash_being_log SET status = 1,processing_time=now() WHERE isvalid=true AND batchcode='".$batchcode."'";
            _mysql_query($query) or die('Query failed 75: ' . mysql_error());

            $msg_content = 	"亲，您申请零钱提现".$getmoney."元\r\n".
                "状态：已同意申请\n".
                "时间：".date( "Y-m-d H:i:s")."";
            $shopmessage->SendMessage($msg_content,$weixin_fromuser,$customer_id);
        }


    }

    $user_give_currency = $getmoney * $give_currency / 100;	//赠送的的购物币
    if( $user_give_currency >= 0.01 ){
        $custom = "购物币";
        $query = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid = true AND customer_id = $customer_id LIMIT 1";
        $result= _mysql_query($query) or die('Query failed 59: ' . mysql_error());
        while( $row = mysql_fetch_object($result) ){
            $custom = $row->custom;
        }
        $remark = "零钱提现赠送".$user_give_currency.$custom;
        $currency_function->update_currency($user_id,$customer_id,$user_give_currency,1,$being_log_id,$remark,16,0);
    }

    $json['status'] = 401;
    $json['msg']	= "提现状态更改成功";
    $json['datetime'] = date( "Y-m-d H:i:s");
    return json_encode($json);


}


$json["status"] = 0;
$json["msg"] = "批量通过提现完成,共 ".$success_batch_pass." 单批量审核通过,共 ".$error_batch_pass." 单批量审核失败";
$jsons=json_encode($json);
die($jsons);

?>