<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

// $isOpen			= isset($_POST["is_currency"])?$configutil->splash_new($_POST["is_currency"]):0;//购物币支付
$isOpenCurrency = isset($_POST["currency"])?$configutil->splash_new($_POST["currency"]):0;				//购物币是否参与分佣
$custom 		= isset($_POST["custom"])?$configutil->splash_new($_POST["custom"]):'购物币';			//购物币自定义名
$rule 			= isset($_POST["rule"])?$configutil->splash_new($_POST["rule"]):'';						//购物币转赠规则说明
$mini_limit 	= isset($_POST["limit_currency"])?$configutil->splash_new($_POST["limit_currency"]):0;	//购物币限制
$isOpenGiven 	= isset($_POST["currency_given"])?$configutil->splash_new($_POST["currency_given"]):0;	//购物币限制


$sel = "SELECT count(id) as num FROM weixin_commonshop_currency WHERE customer_id=".$customer_id;
$res = _mysql_query($sel) or die('Query failed26: ' . mysql_error());
while($row=mysql_fetch_object($res)){
	$num = $row->num;
}

if($num==0){
	$ins_sql = "INSERT INTO weixin_commonshop_currency(isvalid,isOpen,isOpenCurrency,isOpenGiven,customer_id,custom,rule,mini_limit,createtime) VALUES(true,0,'".$isOpenCurrency."','".$isOpenGiven."',".$customer_id.",'".$custom."','".$rule."','".$mini_limit."',now())";
	_mysql_query($ins_sql) or die('Query failed32: ' . mysql_error());
}else{
	$update_sql = "UPDATE weixin_commonshop_currency SET isOpenCurrency=$isOpenCurrency,isOpenGiven=$isOpenGiven,custom='$custom',createtime=now(),rule='$rule',mini_limit='$mini_limit' WHERE customer_id=$customer_id limit 1";
	//echo $update_sql;die;
	_mysql_query($update_sql)or die('Query failed31: ' . mysql_error());
}


$error =mysql_error();
mysql_close($link);
echo "<script>location.href='pay_currency.php?customer_id=".$customer_id_en."';</script>"


?>