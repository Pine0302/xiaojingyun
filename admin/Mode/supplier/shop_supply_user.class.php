<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');

$keyid =$configutil->splash_new($_GET["keyid"]);   //User_ID编号
$op =$configutil->splash_new($_GET["op"]);   //内容

$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

if($op=='reset'){

	$newpassword=888888;
	//weixin_users
	$query_users = "update weixin_users set pwd='".$newpassword."'  where id=".$keyid." and customer_id=".$customer_id;
	_mysql_query($query_users) or die('Query_users failed: ' . mysql_error()); 
	//echo $query_users."<br>";
	
	//promoters
	$query_promoters = "update promoters set pwd='".$newpassword."'  where user_id=".$keyid." and customer_id=".$customer_id;
	_mysql_query($query_promoters) or die('Query_promoters failed: ' . mysql_error()); 
	//echo $query_promoters."<br>";	

	echo "<script>alert('密码重置为888888')</script>";
	
}else if($op=='del'){
	 
	//删除供应商身份
	$sql_promoters="update promoters set isAgent=0 where user_id=".$keyid;
	_mysql_query($sql_promoters) or die('Query_promoters failed: ' . mysql_error()); 
	//删掉供应商申请
	$sql_applysupplys="update weixin_commonshop_applysupplys set isvalid=false where user_id=".$keyid;
	_mysql_query($sql_applysupplys) or die('Query_applysupplys failed: ' . mysql_error()); 
	//将供应商的产品下架  
	$sql_products="update weixin_commonshop_products set isout=1 where is_supply_id=".$keyid;
	_mysql_query($sql_products) or die('Query_products failed: ' . mysql_error()); 	  
	//weixin_commonshop_supply_pc
	$sql_products="update weixin_commonshop_supply_pc set isvalid=false where user_id=".$keyid;
	_mysql_query($sql_products) or die('Query_products failed: ' . mysql_error()); 	  
	  
}

mysql_close($link);
echo "<script>location.href='shop_supply_user.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>"
?>