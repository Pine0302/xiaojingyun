<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$is_open = isset($_POST["is_open"])?$configutil->splash_new($_POST["is_open"]):0;	//购物币提现至洛克云平台开关
$key = isset($_POST["key"])?$configutil->splash_new($_POST["key"]):'';			    //洛克云对接密钥
 


$sel = "SELECT count(id) as num FROM currency_luokeyun_set WHERE isvalid=true and customer_id=".$customer_id;
$res = _mysql_query($sel) or die('Query failed26: ' . mysql_error());
while($row=mysql_fetch_object($res)){
	$num = $row->num;
}

if($num==0){
	$ins_sql = "INSERT INTO currency_luokeyun_set(isvalid,is_open,luokeyun_key,customer_id) VALUES(true,".$is_open.",'".$key."',".$customer_id.")";
	_mysql_query($ins_sql) or die('Query failed32: ' . mysql_error());
}else{
	$update_sql = "UPDATE currency_luokeyun_set SET is_open=".$is_open.",luokeyun_key='".$key."' WHERE isvalid=true and customer_id=".$customer_id." limit 1";
	_mysql_query($update_sql)or die('Query failed31: ' . mysql_error());
}


$error =mysql_error();
mysql_close($link);
echo "<script>location.href='luokeyun_set.php?customer_id=".$customer_id_en."';</script>"


?>