<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');


$yz_type 	= $configutil->splash_new($_POST["yz_type"]);
$code_type 	= $configutil->splash_new($_POST["code_type"]);
$agreement 	= $configutil->splash_new($_POST["agreement"]);
$is_agreement 	= $configutil->splash_new($_POST["is_agreement"]);

// echo "yz_type=".$yz_type."</br>";
// echo "code_type=".$code_type."</br>";
// echo "agreement=".$agreement."</br>";
$id = -1;
$query = "SELECT id FROM register_set WHERE isvalid=true AND customer_id = $customer_id LIMIT 1";
$result= _mysql_query($query) or die('Query failed 21: ' . mysql_error()." query ==".$query);  
while( $row = mysql_fetch_object($result) ){
	$id = $row->id;
}
if( $id < 0 ){
	$query = "INSERT INTO register_set(isvalid,customer_id,yz_type,code_type,agreement,is_agreement) VALUES(true,$customer_id,$yz_type,$code_type,'$agreement','$is_agreement')";
}else{
	$query = "UPDATE register_set SET is_agreement=$is_agreement,yz_type=$yz_type,code_type=$code_type,agreement='$agreement' WHERE isvalid=true AND customer_id=$customer_id";
}
//echo $query;die;
_mysql_query($query)or die('Query failed 21: ' . mysql_error()." query ==".$query);  

	echo '<script>history.go(-1);</script>';



?>