<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$yeepay_customernumber = '';
$yeepay_secret = '';

$yeepay_id =$configutil->splash_new($_POST["yeepay_id"]);
$yeepay_customernumber =$configutil->splash_new($_POST["yeepay_customernumber"]);
$yeepay_secret =$configutil->splash_new($_POST["yeepay_secret"]);


// $sql="delete from yeepay";
// _mysql_query($sql);
// die();

if($yeepay_id>0){ 
	$sql=sprintf("update yeepay set customernumber='%s',secret='%s' where customer_id=%d",$yeepay_customernumber,$yeepay_secret,$customer_id);
}else{
	 $sql=sprintf("insert into yeepay(customernumber,secret,customer_id,isvalid)values('%s','%s',%d,true)",$yeepay_customernumber,$yeepay_secret,$customer_id);
}

// echo $sql;
// die();

$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());


$error =mysql_error();
mysql_close($link);
echo "<script>location.href='yeepay_set.php?customer_id=".$customer_id_en."';</script>"
?>