<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$guide_id           = -1;
$is_guide_attention = 0;
$is_guide_phone     = 0;
$is_guide_app       = 0;
$app_domain_name    = "";
$app_url_type       = 0;
$app_diy_url        = "";
if( !empty($_POST["guide_id"]) ){
	$guide_id = $configutil->splash_new($_POST["guide_id"]);
}
if( !empty($_POST["is_guide_attention"]) ){
	$is_guide_attention = $configutil->splash_new($_POST["is_guide_attention"]);
}
if( !empty($_POST["is_guide_phone"]) ){
	$is_guide_phone     = $configutil->splash_new($_POST["is_guide_phone"]);
}
if( !empty($_POST["is_guide_app"]) ){
	$is_guide_app       = $configutil->splash_new($_POST["is_guide_app"]);
}
if( !empty($_POST["app_domain_name"]) ){
	$app_domain_name    = trim($configutil->splash_new($_POST["app_domain_name"]));
}
if( !empty($_POST["app_url_type"]) ){
	$app_url_type       = $configutil->splash_new($_POST["app_url_type"]);
}
if( !empty($_POST["app_diy_url"]) ){
	$app_diy_url        = $configutil->splash_new($_POST["app_diy_url"]);
}

if($guide_id > 0){
	$query = "UPDATE weixin_commonshop_guide SET 
				is_guide_attention=".$is_guide_attention.", 
				is_guide_phone=".$is_guide_phone.", 
				is_guide_app=".$is_guide_app.", 
				app_domain_name='".$app_domain_name."', 
				app_url_type=".$app_url_type.", 
				app_diy_url='".$app_diy_url."' 
				WHERE id=".$guide_id;
}else{
	$query = "INSERT INTO weixin_commonshop_guide(
					customer_id,
					is_guide_attention,
					is_guide_phone,
					is_guide_app,
					app_domain_name,
					isvalid,
					createtime,
					app_url_type,
					app_diy_url) VALUES (
					".$customer_id.",
					".$is_guide_attention.",
					".$is_guide_phone.",
					".$is_guide_app.",
					'".$app_domain_name."',
					true,
					now(),
					".$app_url_type.",
					'".$app_diy_url."'
					)";
}
//echo $query;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$error =mysql_error();
mysql_close($link);
echo "<script>location.href='guide_set.php?customer_id=".$customer_id_en."';</script>"
?>