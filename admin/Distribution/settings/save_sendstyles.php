<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$sendstyle_express=$configutil->splash_new($_POST['sendstyle_express']);
$sendstyle_pickup=$configutil->splash_new($_POST['sendstyle_pickup']);
$open_virtual_cust=$configutil->splash_new($_POST['open_virtual_cust']);
$open_virtual_supplier=$configutil->splash_new($_POST['open_virtual_supplier']);
$regional_detection=$configutil->splash_new($_POST['regional_detection']);
$is_kuaidi     = $configutil->splash_new($_POST['is_kuaidi']);
$AppKey     = $configutil->splash_new($_POST['AppKey']);
$AppSecret  = $configutil->splash_new($_POST['AppSecret']);
$AppCode    = $configutil->splash_new($_POST['AppCode']);

$con = '';
if ($is_kuaidi == 1){
	$con = " , appkey = '".$AppKey."' , appsecret = '".$AppSecret."' , appcode = '".$AppCode."' ";
}
//$open_virtual_proxy=$configutil->splash_new($_POST['open_virtual_proxy']);
$sql="update weixin_commonshops set sendstyle_express=".$sendstyle_express.",sendstyle_pickup=".$sendstyle_pickup.
	",open_virtual_cust = ".$open_virtual_cust." , open_virtual_supplier = ".$open_virtual_supplier.
	" , regional_detection = ".$regional_detection.
	" , is_kuaidi = ".$is_kuaidi." ".$con." where isvalid=1 and customer_id=".$customer_id;
_mysql_query($sql) or die('Query failed: ' . mysql_error());
$error =mysql_error();
mysql_close($link); 
echo "<script>location.href='sendstyles.php?customer_id=".$customer_id_en."';</script>";
?>