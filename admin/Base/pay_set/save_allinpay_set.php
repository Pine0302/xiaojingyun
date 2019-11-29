<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$allinpay_id=-1;
$allinpay_id =$configutil->splash_new($_POST["allinpay_id"]);//商城id


$version =$configutil->splash_new($_POST["version"]);//版本类型
$vendor_id =$configutil->splash_new($_POST["vendor_id"]);//商户号
$appkey =$configutil->splash_new($_POST["appkey"]);//商户KEY


if($allinpay_id>0){ 
	$sql="update allinpays set version=".$version.",vendor_id='".$vendor_id."',appkey='".$appkey."' where customer_id=".$customer_id;
}else{
	$sql="insert into allinpays(vendor_id,appkey,pwd,customer_id,isvalid,createtime,version) values ('".$vendor_id."','".$appkey."','".$pwd."',".$customer_id.",true,now(),".$version.")";
}

$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());


$error =mysql_error();
mysql_close($link);
echo "<script>location.href='allinpay_set.php?customer_id=".$customer_id_en."';</script>"
?>