<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
require_once($_SERVER['DOCUMENT_ROOT']."/wsy_pay/web/function/investment_reward.php");

	$inverst_id   = $configutil->splash_new($_POST["inverst_id"]);
	$category  = $configutil->splash_new($_POST["category"]);
	$p_parent_id = $configutil->splash_new($_POST["p_parent_id"]);
	$investment_reward = new investment_reward($customer_id);
	$condition=array('category'=>$category,'inverst_id'=>$inverst_id,'parent_id'=>$p_parent_id);
	$investment_reward->save_parpeople($condition);

	header("location:supply.php?customer_id=$customer_id_en");
?>
