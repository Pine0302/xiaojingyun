<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$is_web_reg = 0;
$is_bind_chat = 0;
$is_chat_bind_apph5 = 0;
$is_chat_bind_usedphone = 0;
if(!empty($_POST["is_web_reg"])){
	$is_web_reg = $configutil->splash_new($_POST["is_web_reg"]);
}
if(!empty($_POST["is_bind_chat"])){
	$is_bind_chat = $configutil->splash_new($_POST["is_bind_chat"]);
}
//查询商城是否开启同步区块链基因
$block_chain_gene  = 0;
$sql_chain_gene    = "SELECT block_chain_gene FROM ".WSY_SHOP.".block_chain_setting where customer_id=".$customer_id." LIMIT 1 ";
$result_chain_gene = _mysql_query($sql_chain_gene) or die("sql1 query error : ".mysql_error());
if($val_chain_gene = mysql_fetch_object($result_chain_gene))
{
    $block_chain_gene  = $val_chain_gene->block_chain_gene;
}

if($block_chain_gene == 1 && $is_bind_chat == 0)
{
	echo "<script>alert('已开启同步区块链系统用户基因开关，无法关闭强制绑定手机号开关！');location.href='binding.php?customer_id=".$customer_id_en."';</script>";
	exit;
}
if(!empty($_POST["is_chat_bind_apph5"])){
	$is_chat_bind_apph5 = $configutil->splash_new($_POST["is_chat_bind_apph5"]);
}
if(!empty($_POST["is_chat_bind_usedphone"])){
	$is_chat_bind_usedphone = $configutil->splash_new($_POST["is_chat_bind_usedphone"]);
}

$sql="update weixin_commonshops set 
	is_web_reg=".$is_web_reg.",
	is_bind_chat=".$is_bind_chat.",
    is_chat_bind_apph5=".$is_chat_bind_apph5.",
	is_chat_bind_usedphone=".$is_chat_bind_usedphone."
	where isvalid=true and customer_id=".$customer_id; 
$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());
//echo $sql;
$error = "";
$error = mysql_error();
mysql_close($link);
if($error==""){
	echo "<script>location.href='binding.php?customer_id=".$customer_id_en."';</script>";
}else{
	echo $error;
}

?>