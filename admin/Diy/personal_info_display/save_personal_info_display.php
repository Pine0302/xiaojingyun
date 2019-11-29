<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$pidt_id	   = -1;
$is_phone 	   = 0;
$is_qq 		   = 0;
$is_weixin 	   = 0;
$is_weixincode = 0;
if(!empty($_POST["pidt_id"])){
	$pidt_id = $configutil->splash_new($_POST["pidt_id"]);
}
if(!empty($_POST["is_phone"])){
	$is_phone = $configutil->splash_new($_POST["is_phone"]);
}
if(!empty($_POST["is_qq"])){
	$is_qq = $configutil->splash_new($_POST["is_qq"]);
}
if(!empty($_POST["is_weixin"])){
	$is_weixin = $configutil->splash_new($_POST["is_weixin"]);
}
if(!empty($_POST["is_weixincode"])){
	$is_weixincode = $configutil->splash_new($_POST["is_weixincode"]);
}

$sql = "update personal_info_display_t set 
	is_phone=".$is_phone.",
	is_qq=".$is_qq.",
	is_weixin=".$is_weixin.",
	is_weixincode=".$is_weixincode."
	where isvalid=true and customer_id=".$customer_id." and id=".$pidt_id; 
$result = _mysql_query($sql) or die('Sql failed: ' . mysql_error());
//echo $sql;
$error = "";
$error = mysql_error();
mysql_close($link);
if($error==""){
	echo "<script>location.href='personal_info_display.php?customer_id=".$customer_id_en."';</script>";
}else{
	echo $error;
}

?>