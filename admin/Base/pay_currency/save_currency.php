<?php
 header("Content-type: text/html; charset=utf-8"); 
require_once('../../../../weixinpl/config.php');
require_once('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require_once('../../../../weixinpl/back_init.php');
require_once("../../../../mp/lib/fileCache.php"); 
require_once('../../../../wsy_pub/admin/model/global_config.php');  //全局变量

$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$isOpen			= isset($_POST["is_currency"])?$configutil->splash_new($_POST["is_currency"]):0;//购物币抵扣
$isOpenCurrency = isset($_POST["currency"])?$configutil->splash_new($_POST["currency"]):0;				//购物币是否参与分佣
$custom 		= isset($_POST["custom"])?$configutil->splash_new($_POST["custom"]):'购物币';			//购物币自定义名
$rule 			= isset($_POST["rule"])?$configutil->splash_new($_POST["rule"]):'';						//购物币转赠规则说明
$mini_limit 	= isset($_POST["limit_currency"])?$configutil->splash_new($_POST["limit_currency"]):0;	//购物币限制
$isOpenGiven 	= isset($_POST["currency_given"])?$configutil->splash_new($_POST["currency_given"]):0;	//购物币限制
$percentage 	= isset($_POST["percentage"])?$configutil->splash_new($_POST["percentage"]):0;	//购物币设置比例
$is_rebate_open = isset($_POST["is_rebate_open"])?$configutil->splash_new($_POST["is_rebate_open"]):1;	//返增购物币开关
$rebate_user 	= isset($_POST["rebate_user"])?$configutil->splash_new($_POST["rebate_user"]):2;	//返增对象

$percentage = $percentage/100;

//查询购物币配置表
$sel = "SELECT count(id) as num FROM weixin_commonshop_currency WHERE customer_id=".$customer_id;
$res = _mysql_query($sel) or die('Query failed26: ' . mysql_error());
while($row=mysql_fetch_object($res)){
	$num = $row->num;
}
//查询购物币配置表 End



//更新购物币配置表
if($num==0){
	$ins_sql = "INSERT INTO weixin_commonshop_currency(isvalid,isOpen,isOpenCurrency,isOpenGiven,customer_id,custom,rule,mini_limit,createtime,is_rebate_open,rebate_user) VALUES(true,'".$isOpen."','".$isOpenCurrency."','".$isOpenGiven."',".$customer_id.",'".$custom."','".$rule."','".$mini_limit."',now(),'".$is_rebate_open."','".$rebate_user."')";
	_mysql_query($ins_sql) or die('Query failed32: ' . mysql_error());
}else{
	$update_sql = "UPDATE weixin_commonshop_currency SET isOpenCurrency=$isOpenCurrency,isOpen=$isOpen,isOpenGiven=$isOpenGiven,custom='$custom',createtime=now(),rule='$rule',mini_limit='$mini_limit',is_rebate_open=$is_rebate_open,rebate_user=$rebate_user WHERE customer_id=$customer_id limit 1";
	//echo $update_sql;die;
	_mysql_query($update_sql)or die('Query failed31: ' . mysql_error());
}
//更新购物币配置表 End



//查询购物币抵扣设置表
$sel2 = "SELECT count(id) as num2 FROM currency_percentage_t WHERE isvalid=true and type=1 and customer_id=".$customer_id;
$res2 = _mysql_query($sel2) or die('Query_sel2 failed26: ' . mysql_error());
while($row2=mysql_fetch_object($res2)){
	$num2 = $row2->num2;
}
//查询购物币抵扣设置表 End



//更新购物币抵扣设置表
if($num2==0){
	$ins_sql2 = "INSERT INTO currency_percentage_t(customer_id,isvalid,createtime,type,percentage) VALUES(".$customer_id.",true,now(),1,".$percentage.")";
	_mysql_query($ins_sql2) or die('Query_ins_sql2 failed32: ' . mysql_error());
}else{
	$update_sql2 = "UPDATE currency_percentage_t SET percentage=".$percentage." WHERE isvalid=true and type=1 and customer_id=".$customer_id." limit 1";
	//echo $update_sql;die;
	_mysql_query($update_sql2)or die('Query_update_sql2 failed31: ' . mysql_error());
}
//更新购物币抵扣设置表 End


//查询全局自定义表-购物币名称
$global_config = new global_config();
$fields = 'count(id) as num3';
$num3   = 0;
$result_g_config = $global_config->select_global_custom_name_config($customer_id,$fields);
if($result_g_config['errcode'] == 0){
	$num3 = $result_g_config['config']['num3'];
}
//查询全局自定义表-购物币名称 End

//更新全局自定义购物币名称
$data['pay_currency_name'] = $custom;
if($num3==0){
	$result_g_config = $global_config->insert_global_custom_name_config($customer_id,$data);
}else{
	$result_g_config = $global_config->update_global_custom_name_config($customer_id,$data);
}

$cacheFile = new Inc_FileCache(array('cacheTime'=>3600,'suffix'=>'.php')); 
$isset = $cacheFile->_unset('define_'.$customer_id);
//更新全局自定义购物币名称 End


$error =mysql_error();
mysql_close($link);
echo "<script>location.href='pay_currency.php?customer_id=".$customer_id_en."';</script>"


?>