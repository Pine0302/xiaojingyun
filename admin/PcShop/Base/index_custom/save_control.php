<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');  /*fenxiao下链接出错 11.13 by cdr*/
$new_baseurl = Protocol.$http_host; 

$diy_temid			=-1; //保存diy_template表的ID
$name				=""; //模块的ID
$option				=""; //操作
$temidarr			="";
$supply_id			=-1; //供应商id


if($_POST["diy_temid"]){
	$diy_temid	=	$configutil->splash_new($_POST["diy_temid"]);	
}
if($_POST["name"]){
	$name		=	$configutil->splash_new($_POST["name"]);	
}
if($_POST["temidarr"]){
	$temidarr	=	$configutil->splash_new($_POST["temidarr"]);	
}
if($_POST["option"]){
	$option		=	$configutil->splash_new($_POST["option"]);	
}
if($_POST["supply_id"]){
	$supply_id	=	$configutil->splash_new($_POST["supply_id"]);	
}
if($_POST["custom_type"]){
	$custom_type	=	$configutil->splash_new($_POST["custom_type"]);	
}


if($option=="changename"){
	//改变名称
	$changename="update pcshop_diy_template set name='".$name."' where id='".$diy_temid."' and  customer_id='".$customer_id."' and isvalid=true ";
	$result_changename=_mysql_query($changename) or die ('changename faild' .mysql_error());
}
if($option=="temp_delete"){
	$isused = false;
	//是否首页模板
	$query_isused = "SELECT isused FROM pcshop_diy_template WHERE id=".$diy_temid;
	$result_isused = _mysql_query($query_isused) or die('Query_isused failed'.mysql_error());
	while( $row_isused = mysql_fetch_object($result_isused) ){
		$isused = $row_isused -> isused;
	}
	//不能删除首页模板
	if( $isused ){
		$str->code = 0;
		die(json_encode($str));
	}
	
	//删除模板
	$temp_delete="update pcshop_diy_template set isvalid=false where id='".$diy_temid."' and  customer_id='".$customer_id."'";
	$result_temp_delete=_mysql_query($temp_delete) or die ('temp_delete faild' .mysql_error());
	$str->code=1;
	echo json_encode($str);
}
if($option=="deleteall"){
	
	//批量删除
	
	$arr=substr($temidarr,0,strlen($temidarr)-1);
	$deleteall="update pcshop_diy_template set isvalid=false where id in(".$arr.") and  customer_id='".$customer_id."'";
	$result_deleteall=_mysql_query($deleteall) or die ('deleteall faild' .mysql_error());
	$str->msg=1;
	echo json_encode($str);
}
if($option=="temp_check"){
	//选择模板
	$temp_check="update pcshop_diy_template set isused=false where id!='".$diy_temid."' and  customer_id='".$customer_id."' and isvalid=true and supply_id=".$supply_id." and custom_type=".$custom_type;
	$temp_check1="update pcshop_diy_template set isused=true where id='".$diy_temid."' and  customer_id='".$customer_id."' and isvalid=true and supply_id=".$supply_id." and custom_type=".$custom_type;
	$result_temp_check=_mysql_query($temp_check) or die ('temp_check faild' .mysql_error());
	$result_temp_check1=_mysql_query($temp_check1) or die ('temp_check1 faild' .mysql_error());
	$str->code=1;
	echo json_encode($str);
}
if($option=="temp_cancel"){
	//停用模板
	$temp_cancel="update pcshop_diy_template set isused=false where id='".$diy_temid."' and  customer_id='".$customer_id."' and isvalid=true";
	$result_temp_cancel=_mysql_query($temp_cancel) or die ('temp_cancel faild' .mysql_error());
	$str->code=1;
	echo json_encode($str);
}
mysql_close($link);
?>