<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/common/utility_shop.php');
require('../../../../weixinpl/proxy_info.php');
require('../../../../weixinpl/auth_user.php');
_mysql_query("SET NAMES UTF8");
function cut_num($menber,$places){
	$places = $places+1;
	$num = substr(sprintf("%.".$places."f", $menber),0,-1); 
	return $num;	
}

$user_id	 = 0;
$currency 	 = 0;
$remark 	 = '';
$customer_id = passport_decrypt($customer_id);
$user_id 	 = $configutil->splash_new($_GET["user_id"]);
$currency 	 = $configutil->splash_new($_POST["currency"]);			//充值的购物币
$remark 	 = $configutil->splash_new($_POST["remark"]);
$batchcode   = $user_id.time();
$before_currency = 0;
$id = -1;
$custom = '';
$query = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid=true AND customer_id=".$customer_id." LIMIT 1";
$result= _mysql_query($query) or die( 'Query failed in 30: ' . mysql_error() );
while( $row = mysql_fetch_object($result) ){
	$custom = $row->custom;
	if($custom == NULL){
		$custom = '';
	}
}

$sql = "SELECT id,currency FROM weixin_commonshop_user_currency WHERE isvalid=true AND user_id=".$user_id." AND customer_id=".$customer_id." LIMIT 1";
$res = _mysql_query($sql) or die( 'Query failed in 22: ' . mysql_error() );
while( $row = mysql_fetch_object($res) ){
	$id 				= $row->id;
	$before_currency 	= $row->currency;
}
//echo "充值前购物币==".$before_currency."充值购物币===".$currency."</br>";
$after_currency = cut_num($before_currency+$currency,2);
//echo "充值后购物币".$after_currency;die;
if($after_currency<0){
		echo '<script>history.go(-2);</script>';
		return false;
	}
if( $currency < 0 ){
    if($remark == '') {
        $remark = "商家后台扣取您 " . abs($currency) . $custom;
    }
    $msg_content = 	"亲，商家后台操作扣取".$custom." \n".
                    "扣取金额：【".$currency."元】\n".
                    "当前账户余额为：【".$after_currency.$custom."】\n".
                    "时间：".date( "Y-m-d H:i:s")."";

}elseif( $currency > 0 ){
    if($remark == '') {
        $remark = "后台充值 " . $currency . " 购物币 | 购物币订单号：" . $batchcode;
    }
    $msg_content = 	"亲，商家后台操作充值".$custom." \n".
                    "充值金额：【".$currency."元】\n".
                    "当前账户余额为：【".$after_currency.$custom."】\n".
                    "时间：".date( "Y-m-d H:i:s")."";
}

if( $currency < 0 ){
		$type = 0;
	}elseif( $currency > 0 ){
		$type = 1;
	}

if( $id < 0 ){

	$query = "INSERT INTO weixin_commonshop_user_currency(isvalid,customer_id,user_id,currency,createtime) VALUES(true,".$customer_id.",".$user_id.",".$currency.",now())";
	_mysql_query($query)or die( 'Query failed in 28: ' . mysql_error() );

	$sql = "INSERT INTO weixin_commonshop_currency_log(isvalid,customer_id,user_id,cost_money,cost_currency,after_currency,batchcode,status,type,class,remark,createtime)
	VALUES(true,".$customer_id.",".$user_id.",".$currency.",".abs($currency).",".$after_currency.",'".$batchcode."',1,$type,0,'".$remark."',now())";
	_mysql_query($sql)or die( 'Query failed in 34: ' . mysql_error() );

}elseif( $id > 0 ){

	$query = "UPDATE weixin_commonshop_user_currency SET currency=currency+".$currency." WHERE user_id=".$user_id." AND customer_id=".$customer_id;
	_mysql_query($query)or die( 'Query failed in 40: ' . mysql_error() );

	$sql = "INSERT INTO weixin_commonshop_currency_log(isvalid,customer_id,user_id,cost_money,cost_currency,after_currency,batchcode,status,type,class,remark,createtime)
	VALUES(true,".$customer_id.",".$user_id.",".$currency.",".abs($currency).",".$after_currency.",'".$batchcode."',1,$type,0,'".$remark."',now())";
	_mysql_query($sql)or die( 'Query failed in 50: ' . mysql_error() );
}	


$shopMessage_Utlity = new shopMessage_Utlity;
$weixin_fromuser = '';
$query = "SELECT weixin_fromuser FROM weixin_users WHERE isvalid=true AND customer_id=$customer_id AND id = $user_id LIMIT 1";
$result= _mysql_query($query)or die( 'Query failed in 86: ' . mysql_error() );
while($row = mysql_fetch_object($result)){
	$weixin_fromuser = $row->weixin_fromuser;
}
$shopMessage_Utlity->SendMessage($msg_content,$weixin_fromuser,$customer_id);



echo '<script>history.go(-2);</script>';







?>