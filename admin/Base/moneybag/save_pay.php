<?php
header("Content-type: text/html; charset=utf-8");
// require_once('../../../../wsy_pay/web/ipspay/IPSfun.php');
require_once('../../../../weixinpl/config.php');
require_once('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require_once('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require_once('../../../../weixinpl/proxy_info.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/common/utility_shop.php');

_mysql_query("SET NAMES UTF8");
$shopmessage= new shopMessage_Utlity();
$currency_function = new Currency();

$user_id 		= -1;
$customer_id 	= $configutil->splash_new($_POST["customer_id"]);
$batchcode  	= $configutil->splash_new($_POST["batchcode"]);

//var_dump($batchcode);die();

$getmoney 		= 0;
$customer_id 	= passport_decrypt((string)$customer_id);
//echo $customer_id;die;
$getmoney = 0;			//申请提现的金额
$surplus_type = 0;		//0:全额提现 1：直接扣取 2：返购物币
$percentage = 0;		//折现率（千分比）
$arrival_money = 0; 	//到账的金额
$counter_money = 0;		//需要扣取的金额 不用收手续费就 0
$fee = 0;				//手续费
$currency = 0;			//购物币

$query = "SELECT id,getmoney,user_id,surplus_type,percentage,callback_fee,callback_fee_flxed,callback_currency,give_currency,cash_type FROM weixin_cash_being_log WHERE isvalid=true AND batchcode = '$batchcode' LIMIT 1";
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
	//$batchcode 		= $row->batchcode;
}
$CRusult = $currency_function->insert_currency($user_id,$customer_id);

$weixin_fromuser = '';
$query = "SELECT weixin_fromuser FROM weixin_users WHERE isvalid=true AND id = $user_id LIMIT 1";
$result= _mysql_query($query) or die('Query failed 82: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$weixin_fromuser = $row->weixin_fromuser;
}
//
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
		/*
		$query = "SELECT id,currency FROM weixin_commonshop_user_currency WHERE isvalid=true AND user_id = $user_id LIMIT 1";
		$result= _mysql_query($query) or die('Query failed 44: ' . mysql_error());
		while( $row = mysql_fetch_object($result) ){
			$id = $row->id;
			$user_currency = $row->currency;
		}
		$after_currency = $user_currency + $currency;
		if( $id < 0 ){
			$query = "INSERT INTO weixin_commonshop_user_currency(isvalid,customer_id,user_id,currency,createtime) VALUES(true,$customer_id,$user_id,$currency,now())";
			_mysql_query($query) or die('Query failed 52: ' . mysql_error());
		} else {
			$query = "UPDATE weixin_commonshop_user_currency SET currency = currency+$currency WHERE isvalid=true AND user_id = $user_id";
			_mysql_query($query) or die('Query failed 56: ' . mysql_error());
		}
		*/

		$custom = "购物币";
		$query = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid = true AND customer_id = $customer_id LIMIT 1";
		$result= _mysql_query($query) or die('Query failed 59: ' . mysql_error());
		while( $row = mysql_fetch_object($result) ){
			$custom = $row->custom;
		}
		$remark = "零钱提现返".$currency.$custom;

		/*$query = "INSERT INTO weixin_commonshop_currency_log(isvalid,customer_id,user_id,cost_money,cost_currency,after_currency,batchcode,status,type,class,remark,createtime) VALUES(true,$customer_id,$user_id,$currency,$currency,$after_currency,'$batchcode',1,1,3,'$remark',now())";
		_mysql_query($query) or die('Query failed 66: ' . mysql_error());
		*/
        
        /* 防止重复返购物币 */
        $lcount = 0;
        $query1 = "SELECT count(id) as lcount from weixin_commonshop_currency_log where type=1 and class=3 and batchcode = '".$batchcode."'";
        $result1= _mysql_query($query1);
        while($row1=mysql_fetch_object($result1)){
            $lcount = $row1->lcount;
        }
        
        if($lcount==0){//防止重复返购物币
		$currency_function->update_currency($user_id,$customer_id,$currency,1,$batchcode,$remark,3,0);

		$msg_content = 	"亲，您的".$custom."增加了".$currency."\r\n".
					"来源：零钱提现返".$custom."\n".
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
        
        /* 防止重复赠送购物币 */
        $lcount2 = 0;
        $query2 = "SELECT count(id) as lcount from weixin_commonshop_currency_log where type=1 and class=16 and batchcode = '".$batchcode."'";
        $result2= _mysql_query($query2);
        while($row2=mysql_fetch_object($result2)){
            $lcount2 = $row2->lcount;
        }
        
        if($lcount2==0){//防止重复赠送购物币
		$remark = "零钱提现赠送".$user_give_currency.$custom;
		$currency_function->update_currency($user_id,$customer_id,$user_give_currency,1,$batchcode,$remark,16,0);
        }
	}
	if($cash_type==4){
		// $data['batchcode']  = $batchcode;
		// $data['account']    = $_REQUEST['account'];
		// $data['price']      = $to_cash;
		// require_once('../../../../wsy_pay/web/ipspay/IPSfun.php');
		// $ips=new IPS();
		// $xml = $ips->setmessage(7,$data);
		// $url = 'https://ebp.ips.com.cn/fpms-access/action/trade/transfer.do';
		// $result = transfer($data,$xml,$url);
		// if( $data['tradeState'] == 10 ){
		// 	$json = array();
		// 	$json['status'] = 400;
		// 	$json['msg']	= "提现状态更改失败";
		// 	$json['datetime'] = date( "Y-m-d H:i:s");
		// 	echo json_encode($json);
		// 	return fasle;
		// }
	}else{
		//改变提现状态
		$query = "UPDATE weixin_cash_being_log SET status = 1,processing_time=now() WHERE isvalid=true AND batchcode='".$batchcode."'";
		_mysql_query($query) or die('Query failed 70: ' . mysql_error());
	}


	/*$counter_money = $getmoney*$percentage/1000;			//需要扣取的全部或返佣的购物币
	$arrival_money = (($getmoney-$counter_money)*100)/100;	//实际到账的钱

	if( $surplus_type == 2 ){	//返购物币

		$id = -1;
		$currency = 0;
		$query = "SELECT id,currency FROM weixin_commonshop_user_currency WHERE isvalid=true AND user_id = $user_id LIMIT 1";
		$result= _mysql_query($query) or die('Query failed 44: ' . mysql_error());
		while( $row = mysql_fetch_object($result) ){
			$id = $row->id;
			$currency = $row->currency;
		}
		$after_currency = $currency+$counter_money;
		if( $id < 0 ){
			$query = "INSERT INTO weixin_commonshop_user_currency(isvalid,customer_id,user_id,currency,createtime) VALUES(true,$customer_id,$user_id,$counter_money,now())";
			_mysql_query($query) or die('Query failed 52: ' . mysql_error());
		}
		else{
			$query = "UPDATE weixin_commonshop_user_currency SET currency = currency+$counter_money WHERE isvalid=true AND user_id = $user_id";
			_mysql_query($query) or die('Query failed 56: ' . mysql_error());
		}
		$custom = "购物币";
		$query = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid = true AND customer_id = $customer_id LIMIT 1";
		$result= _mysql_query($query) or die('Query failed 59: ' . mysql_error());
		while( $row = mysql_fetch_object($result) ){
			$custom = $row->custom;
		}
		$remark = "提现返".$counter_money.$custom;
		$query = "INSERT INTO weixin_commonshop_currency_log(isvalid,customer_id,user_id,cost_money,cost_currency,after_currency,batchcode,status,type,class,remark,createtime) VALUES(true,$customer_id,$user_id,$counter_money,$counter_money,$after_currency,'$batchcode',1,1,3,'$remark',now())";
		_mysql_query($query) or die('Query failed 66: ' . mysql_error());

		//改变提现状态
		$query = "UPDATE weixin_cash_being_log SET status = 1 WHERE isvalid=true AND batchcode=".$batchcode;
		_mysql_query($query) or die('Query failed 70: ' . mysql_error());
	}else{
		// $counter_money = $getmoney*$percentage/1000;			//需要扣取的全部或返佣的购物币
		// $arrival_money = (($getmoney-$counter_money)*100)/100;	//实际到账的钱
		//改变提现状态
		$query = "UPDATE weixin_cash_being_log SET status = 1 WHERE isvalid=true AND batchcode=".$batchcode;
		_mysql_query($query) or die('Query failed 75: ' . mysql_error());
	}*/

	}else {
	if ($cash_type == 4) {
		// $data['batchcode']  = $batchcode;
		// $data['account']    = $_REQUEST['account'];
		// $data['price']      = $to_cash;
		// require_once('../../../../weixinpl/back_newshops/IPSpay/IPSfun.php');
		// $ips=new IPS();
		// $xml = $ips->setmessage(7,$data);
		// $url = 'https://ebp.ips.com.cn/fpms-access/action/trade/transfer.do';
		// $result = transfer($data,$xml,$url);
		// if( $data['tradeState'] == 10 ){
		// 	$json = array();
		// 	$json['status'] = 400;
		// 	$json['msg']	= "提现状态更改失败";
		// 	$json['datetime'] = date( "Y-m-d H:i:s");
		// 	echo json_encode($json);
		// 	return fasle;
		// }
	} else {
		//改变提现状态
		$query = "UPDATE weixin_cash_being_log SET status = 1,processing_time=now() WHERE isvalid=true AND batchcode='" . $batchcode."'";
		_mysql_query($query) or die('Query failed 75: ' . mysql_error());

		$msg_content = "亲，您申请零钱提现" . $getmoney . "元\r\n" .
				"状态：已同意申请\n" .
				"时间：" . date("Y-m-d H:i:s") . "";
		$shopmessage->SendMessage($msg_content, $weixin_fromuser, $customer_id);
	}
}
/*
	$user_give_currency = $getmoney * $give_currency / 100;    //赠送的的购物币
	if ($user_give_currency >= 0.01) {
		$custom = "购物币";
		$query = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid = true AND customer_id = $customer_id LIMIT 1";
		$result = _mysql_query($query) or die('Query failed 59: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$custom = $row->custom;
		}
		$remark = "零钱提现赠送" . $user_give_currency . $custom;
		$currency_function->update_currency($user_id, $customer_id, $user_give_currency, 1, $being_log_id, $remark, 16, 0);
	}
*/
	/**
	 * [环迅支付提现操作]
	 * @param  [type] $data    [description]
	 * @param  [type] $xml     [description]
	 * @param  [type] $url     [description]
	 * @return [type]          [description]
	 */
	/*function transfer($data,$xml,$url){
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

        // xml->json->多维数组
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($p3DesXmlPara, 'SimpleXMLElement', LIBXML_NOCDATA);
        $data = json_decode(json_encode($xmlstring),true);
        $body = $data['body'];
        $head = $data['head'];

        if( $rspCode == 'M999999'){
            $result['msg'] = $rspMsg;
            return false;
        }

        if( $data['tradeState'] == 10 ){
            $result['msg'] = '请求失败';
            return false;
        }else if( $data['tradeState'] == 9 ){
            $ipsFee     = $data['ipsFee']; // 手续费;
            $tradeId    = $data['tradeId']; // 交易编号;
            $ipsBillNo  = $data['ipsBillNo']; // Ips订单号;
            $sql = "UPDATE weixin_cash_being_log SET status = 1,callback_fee_flxed={$ipsFee}, processing_time=now() WHERE isvalid=true AND batchcode=".$ipsBillNo;
            // _mysql_query($sql) or die('Query failed 75: ' . mysql_error());
            // $database->query($sql);
        }
        // var_dump($rspMsg,$body,$sql);

        return $data;
    }*/
	$json = array();
	$json['status'] = 401;
	$json['msg'] = "提现状态更改成功";
	$json['datetime'] = date("Y-m-d H:i:s");

//	var_dump($json);
	echo json_encode($json);





?>