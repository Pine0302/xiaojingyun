<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$type = $_GET['type'];
if(empty($type)){
	$type == "";
}
if($type == "city"){
		//$isshop 		= 0;//到店支付
		$isdelivery 	= 0;//货到付款
		$iscard 		= $configutil->splash_new($_POST["iscard"]);//会员卡余额
		$is_payother 	= 0;//找人代付
		$is_weipay 		= $configutil->splash_new($_POST["is_weipay"]);//微信支付
		$is_pay 		= 0;//暂不支付
		$is_payChange 	= $configutil->splash_new($_POST["is_payChange"]);//零钱支付
		$is_alipay 		= $configutil->splash_new($_POST["is_alipay"]);//支付宝
		$is_tenpay 		= 0;//财务通
		$is_allinpay 	= 0;//通联支付
		$is_paypal 		= 0;//PayPal支付
		//$is_unionpay 	= $configutil->splash_new($_POST["is_unionpay"]);//银联支付
		$is_yeepay 		= $configutil->splash_new($_POST["is_yeepay"]);//易宝支付
		$is_jdpay 		= 0;//京东支付
		$is_unionpay    = 0;
		$isOpen			= $configutil->splash_new($_POST["is_currency"]);//购物币支付
		$isOpenCurrency = 0;//购物币是否参与分佣
		$custom 		= '购物币';//购物币自定义
}else{
	//$isshop 		= $configutil->splash_new($_POST["isshop"]);//到店支付
	$isdelivery 	= $configutil->splash_new($_POST["isdelivery"]);//货到付款
	$iscard 		= $configutil->splash_new($_POST["iscard"]);//会员卡余额
	$is_payother 	= $configutil->splash_new($_POST["is_payother"]);//找人代付
	$is_weipay 		= $configutil->splash_new($_POST["is_weipay"]);//微信支付
	$is_pay 		= $configutil->splash_new($_POST["is_pay"]);//暂不支付
	$is_payChange 	= $configutil->splash_new($_POST["is_payChange"]);//零钱支付
	$is_alipay 		= $configutil->splash_new($_POST["is_alipay"]);//支付宝
	$is_tenpay 		= $configutil->splash_new($_POST["is_tenpay"]);//财务通
	$is_allinpay 	= $configutil->splash_new($_POST["is_allinpay"]);//通联支付
	$is_paypal 		= $configutil->splash_new($_POST["is_paypal"]);//PayPal支付
	//$is_unionpay 	= $configutil->splash_new($_POST["is_unionpay"]);//银联支付
	$is_yeepay 		= $configutil->splash_new($_POST["is_yeepay"]);//易宝支付
	$is_jdpay 		= $configutil->splash_new($_POST["is_jdpay"]);//京东支付
	$is_unionpay    = 0;
	$isOpen			= $configutil->splash_new($_POST["is_currency"]);//购物币支付
	$isOpenCurrency = 0;//购物币是否参与分佣
	$custom 		= '购物币';//购物币自定义
}


/*8.0暂不支持 一下支付方式*/
$is_tenpay 		= 0;//财务通
$is_allinpay 	= 0;//通联支付
$is_paypal 		= 0;//PayPal支付

//echo $isOpen."==".$isOpenCurrency."==".$custom;die;
$sel = "SELECT count(id) as num FROM weixin_commonshop_currency WHERE customer_id=".$customer_id;
$res = _mysql_query($sel) or die('Query failed26: ' . mysql_error());
while($row=mysql_fetch_object($res)){
	$num = $row->num;
}

if($num==0){
	$ins_sql = "INSERT INTO weixin_commonshop_currency(isvalid,isOpen,isOpenCurrency,customer_id,custom,createtime) VALUES(true,".$isOpen.",".$isOpenCurrency.",".$customer_id.",'".$custom."',now())";
	//echo $ins_sql;
	_mysql_query($ins_sql) or die('Query failed32: ' . mysql_error());
}else{
	$update_sql = "UPDATE weixin_commonshop_currency SET isOpen=$isOpen,createtime=now() WHERE customer_id=$customer_id";
	//echo $update_sql;die;
	_mysql_query($update_sql);
}



//$sql="update customers set is_pay=".$is_pay.",is_payChange=".$is_payChange.",is_payother=".$is_payother.",iscard=".$iscard.",isshop=".$isshop.",is_weipay=".$is_weipay.",is_alipay=".$is_alipay.",is_tenpay=".$is_tenpay.",is_allinpay=".$is_allinpay.",is_paypal=".$is_paypal.",is_unionpay=".$is_unionpay.",is_yeepay=".$is_yeepay.",is_jdpay=".$is_jdpay." where id=".$customer_id;//die($sql);
$sql="update customers set is_pay=".$is_pay.",is_payChange=".$is_payChange.",is_payother=".$is_payother.",iscard=".$iscard.",is_weipay=".$is_weipay.",is_alipay=".$is_alipay.",is_tenpay=".$is_tenpay.",is_allinpay=".$is_allinpay.",is_paypal=".$is_paypal.",is_unionpay=".$is_unionpay.",is_yeepay=".$is_yeepay." where id=".$customer_id;
// echo "<script>console.log('{$sql}')</script>";
$result = _mysql_query($sql) or die('Query failed40: ' . mysql_error());
//echo $sql;return;

$error =mysql_error();
mysql_close($link);
echo "<script>location.href='pay_switch.php?customer_id=".$customer_id_en."&type=".$type."';</script>"
?>