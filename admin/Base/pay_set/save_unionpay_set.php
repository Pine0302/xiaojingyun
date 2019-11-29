<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$unionpay_id = -1;
$unionpay_id = $configutil->splash_new($_POST["unionpay_id"]);//银联支付id


$merchant_no  = $configutil->splash_new($_POST["merchant_no"]);//商户号
$terminal_no  = $configutil->splash_new($_POST["terminal_no"]);//终端号
$merchant_key = $configutil->splash_new($_POST["merchant_key"]);//商户密钥KEY

if($unionpay_id>0){
	$sql="update weixin_china_unionpays set merchant_no='".$merchant_no."',merchant_key='".$merchant_key."',terminal_no='".$terminal_no."' where isvalid=true and id=".$unionpay_id." and customer_id=".$customer_id;
}else{
	$sql="insert into weixin_china_unionpays(merchant_no,terminal_no,merchant_key,customer_id,isvalid,createtime) values ('".$merchant_no."','".$terminal_no."','".$merchant_key."',".$customer_id.",true,now())";
}

$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());


$error =mysql_error();
mysql_close($link);
echo "<script>location.href='unionpay_set.php?customer_id=".$customer_id_en."';</script>"
?>