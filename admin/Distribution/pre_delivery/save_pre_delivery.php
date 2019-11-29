<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$keyid 				= $configutil->splash_new($_POST["keyid"]);
$delivery_name 		= $configutil->splash_new($_POST["delivery_name"]);
$delivery_time 		= $configutil->splash_new($_POST["delivery_time"]);
$delivery_limit 	= $configutil->splash_new($_POST["delivery_limit"]);
$earliest_hour 		= 0;
$latest_hour 		= 0;
$custom_date 		= $configutil->splash_new($_POST["custom_date"]);
$product_relation 	= $configutil->splash_new($_POST["product_relation"]);
$supply_id			= $configutil->splash_new($_GET["supply_id"]);
$supply_id_en		= $configutil->splash_new($_GET["supply_id_en"]);
if( $_POST["earliest_hour"] != '' ){
	$earliest_hour = $configutil->splash_new($_POST["earliest_hour"]);
}
if( $_POST["latest_hour"] != '' ){
	$latest_hour = $configutil->splash_new($_POST["latest_hour"]);
}

$query = '';
if( $keyid > 0 ){
	$query = "UPDATE weixin_commonshop_pre_delivery SET 
						delivery_name='".$delivery_name."',
						delivery_time='".$delivery_time."',
						earliest_hour=".$earliest_hour.",
						latest_hour=".$latest_hour.",
						custom_date='".$custom_date."',
						delivery_limit=".$delivery_limit."
					WHERE id=".$keyid;
}else{
	$query = "INSERT INTO weixin_commonshop_pre_delivery(
						delivery_name,
						delivery_time,
						earliest_hour,
						latest_hour,
						custom_date,
						delivery_limit,
						supply_id,
						customer_id,
						isvalid,
						createtime
					) VALUES(
						'".$delivery_name."',
						'".$delivery_time."',
						".$earliest_hour.",
						".$latest_hour.",
						'".$custom_date."',
						".$delivery_limit.",
						".$supply_id.",
						".$customer_id.",
						true,
						now()
					)";
}
_mysql_query($query) or die('Query failed:'.mysql_error());

if( $keyid < 1 ){
	$keyid = mysql_insert_id();
}

$query_relation_del = "UPDATE weixin_commonshop_pre_delivery_product_relation SET isvalid=false WHERE delivery_id=".$keyid." AND isvalid=true";
_mysql_query($query_relation_del) or die('Query_relation_del failed:'.mysql_error());

$product_relation_arr = explode(',',$product_relation);
$p_len = count($product_relation_arr);
$query_relation_ins = "INSERT INTO weixin_commonshop_pre_delivery_product_relation(pid,delivery_id,customer_id,isvalid,createtime) VALUES";
$query_relation_ins_v = '';
for( $i = 0; $i < $p_len; $i++ ){
	$pid = $product_relation_arr[$i];
	if( $pid < 1 ){
		continue;
	}
	$query_relation_ins_v .= "(".$pid.",".$keyid.",".$customer_id.",true,now()),";
}
if( $query_relation_ins_v != '' ){
	$query_relation_ins_v = substr($query_relation_ins_v,0,-1);
	$query_relation_ins .= $query_relation_ins_v;
	_mysql_query($query_relation_ins) or die('Query_relation_ins failed:'.mysql_error());
}

mysql_close($link);

if( $supply_id > 0 ){
	echo "<script>location.href='pre_delivery_list.php?supply_id=".$supply_id_en."';</script>";
} else {
	echo "<script>location.href='pre_delivery_list.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
}
?>