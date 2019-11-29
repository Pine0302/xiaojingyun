<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$alipay_id=-1;
$alipay_id =$configutil->splash_new($_POST["alipay_id"]);//支付宝id


$account =$configutil->splash_new($_POST["account"]);//支付宝账户
$pid =$configutil->splash_new($_POST["pid"]);//支付宝PID
$akey =$configutil->splash_new($_POST["akey"]);//支付宝KEY

if($alipay_id>0){
	$sql="update alipays set account='".$account."',akey='".$akey."',pid='".$pid."' where customer_id=".$customer_id;
}else{
	$sql="insert into alipays(account,pid,akey,customer_id,isvalid,createtime) values ('".$account."','".$pid."','".$akey."',".$customer_id.",true,now())";
}

$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());


$error =mysql_error();
mysql_close($link);
echo "<script>location.href='alipay_set.php?customer_id=".$customer_id_en."';</script>"
?>