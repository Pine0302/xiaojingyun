<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');  /*fenxiao下链接出错 11.13 by cdr*/

$op = '';
if($_POST["op"]){
	$op	=	$configutil->splash_new($_POST["op"]);	
}

$expresses_name = '';
if($_POST["expresses_name"]){
	$expresses_name	=	$configutil->splash_new($_POST["expresses_name"]);	
}

$expresses_type = '';
if($_POST["expresses_type"]){
	$expresses_type	=	$configutil->splash_new($_POST["expresses_type"]);	
}

$id = -1;
if($_POST["id"]){
	$id	=	$configutil->splash_new($_POST["id"]);	
}

$res = [];
$error = 1;
if($op=="add"){
	//添加
	$query_add = "insert into ".WSY_SHOP.".weixin_expresses_company_kuaidi(expresses_name,expresses_type,customer_id,isvalid,createtime)values('".$expresses_name."','".$expresses_type."',".$customer_id.",true,now())";
	_mysql_query($query_add)or die('Query failed'.mysql_error());
	$res['kuaidi_id'] = mysql_insert_id();
	$error = mysql_error();
}

if($op=="del"){
	//删除
	$query_del="update ".WSY_SHOP.".weixin_expresses_company_kuaidi set isvalid=false where id=".$id." and customer_id=".$customer_id."";
	_mysql_query($query_del)or die('Query failed'.mysql_error());	
	$error = mysql_error();
}

if($error==0){
	$res['code'] = 1;
	echo json_encode($res);
}else{
	$res['code'] = 0;
	echo json_encode($res);
}
mysql_close($link);

?>