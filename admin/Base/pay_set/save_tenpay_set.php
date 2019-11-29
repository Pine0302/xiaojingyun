<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$tenpay_id=-1;
$tenpay_id =$configutil->splash_new($_POST["tenpay_id"]);//财付通id


$bussinessid =$configutil->splash_new($_POST["bussinessid"]);//财付通商户号
$bussinesskey =$configutil->splash_new($_POST["bussinesskey"]);//财付通密钥
$type =$configutil->splash_new($_POST["type"]);//接口服务类型


if($tenpay_id>0){ 
	$sql="update tenpays set bussinessid='".$bussinessid."',bussinesskey='".$bussinesskey."',type=".$type." where customer_id=".$customer_id;
}else{
	 $sql="insert into tenpays(bussinessid,bussinesskey,type,customer_id,isvalid,createtime) values ('".$bussinessid."','".$bussinesskey."',".$type.",".$customer_id.",true,now())";
}

$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());


$error =mysql_error();
mysql_close($link);
echo "<script>location.href='tenpay_set.php?customer_id=".$customer_id_en."';</script>"
?>