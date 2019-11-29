<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$maintenance_id = -1;
$is_maintain    = 0;		//是否开启维护：0不维护，1维护
$begintime      = "";
$endtime        = "";
$information    = "";
if(!empty($_POST["maintenance_id"])){
	$maintenance_id = $configutil->splash_new($_POST["maintenance_id"]);
}
if(!empty($_POST["is_maintain"])){
	$is_maintain = $configutil->splash_new($_POST["is_maintain"]);
}
if(!empty($_POST["begintime"])){
	$begintime = $configutil->splash_new($_POST["begintime"]);
}
if(!empty($_POST["endtime"])){
	$endtime = $configutil->splash_new($_POST["endtime"]);
}
if(!empty($_POST["information"])){
	$information = $configutil->splash_new($_POST["information"]);
}

$sql = "update weixin_maintenance_info set 
	is_maintain=".$is_maintain.",
	begintime='".$begintime."',
	endtime='".$endtime."',
	information='".$information."'
	where isvalid=true and customer_id=".$customer_id." and id=".$maintenance_id; 
$result = _mysql_query($sql) or die('Sql failed: ' . mysql_error());
//echo $sql;
$error = "";
$error = mysql_error();
mysql_close($link);
if($error==""){
	echo "<script>location.href='maintenance_info_edit.php?customer_id=".$customer_id_en."';</script>";
}else{
	echo $error;
}

?>