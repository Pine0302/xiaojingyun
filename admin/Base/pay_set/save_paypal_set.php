<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$paypalpay_id =$configutil->splash_new($_POST["paypalpay_id"]);
$mail =$configutil->splash_new($_POST["mail"]);

if($paypalpay_id>0){ 
	$sql=sprintf("update paypalpay set mail='%s' where customer_id=%s",$mail,$customer_id);
}else{
	 $sql=sprintf("insert into paypalpay(mail,customer_id,isvalid)values('%s',%s,true)",$mail,$customer_id);;
}

$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());


$error =mysql_error();
mysql_close($link);
echo "<script>location.href='paypal_set.php?customer_id=".$customer_id_en."';</script>"
?>