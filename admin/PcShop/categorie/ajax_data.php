<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);  //解密
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

$keyid = -1;
$val = -1;
$keyid = $configutil->splash_new($_GET["keyid"]);
$val   = $configutil->splash_new($_GET["val"]);

$query = "update pcshop_home_categories set is_open=".$val." where isvalid=true and customer_id=".$customer_id." and id=".$keyid;
$error = 0;
_mysql_query($query) or die('W21 Query failed: ' . mysql_error());
$error = mysql_error();
if($error>0){
	$code = "10001";
}else{
	$code = "10000";
}
echo $code;
?>