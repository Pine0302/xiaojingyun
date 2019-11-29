<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
require_once('../../../../weixinpl/function_model/currency.php');
_mysql_query("SET NAMES UTF8");
$currency = new Currency();

$keyid       =0;
$name	     = "";
$num 	     = 0;
$money 	     = 0;
$begintime 	 = "";
$endtime 	 = "";

$customer_id = passport_decrypt($_GET['customer_id']);
if(!empty($_POST["keyid"])){
	$keyid 	 = $configutil->splash_new($_POST["keyid"]);
}

if(!empty($_POST["name"])){
	$name 	 = $configutil->splash_new($_POST["name"]);
}
if(!empty($_POST["num"])){
	$num 	 = $configutil->splash_new($_POST["num"]);
	if($num<0){
		$num = 0;
	}
}
if(!empty($_POST["money"])){
	$money 	 = $configutil->splash_new($_POST["money"]);			//充值的购物币
	if($money<0.01){
		$money = 0;
	}
}
$begintime 	 = $configutil->splash_new($_POST["begintime"]);
$endtime 	 = $configutil->splash_new($_POST["endtime"]);

$fileds = array();
$fileds['title']      = $name;
$fileds['num']        = $num;
$fileds['money']      = $money;
$fileds['starttime']  = $begintime;
$fileds['endtime']    = $endtime;
$fileds['used']       = 0;
$fileds['not_used']   = $num;
if($keyid>0){//更新数据    
    $conditions['id']     = $keyid;	
	$result = $currency ->update_recharge_card_list($conditions,$fileds);	
	$errcode = $result['errcode'];
	$errmsg  = $result['errmsg'];
	
	if($result['errcode'] == 0){
		$return_errmsg = "修改成功";
        //插入操作日志
        require_once(ROOT_DIR.'/wsy_pub/admin/model/sys_plat_log.php');
        $sys_plat_log = new \model_sys_plat_log($customer_id);
        $sys_plat_log->add_log('shop_system_currency_recharge_card','编辑充值卡名称【'.$name.'】；');
	}else{
		$return_errmsg = $errmsg;
	}
	echo "<script>alert('".$return_errmsg."');location.href='currency_recharge.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
	
}else{  //插入数据
    $result = $currency ->insert_recharge_card_list($fileds,$customer_id);	
	$errcode = $result['errcode'];
	$errmsg  = $result['errmsg'];
	
	if($result['errcode'] == 0){
		$return_errmsg = "添加成功";
        //插入操作日志
        require_once(ROOT_DIR.'/wsy_pub/admin/model/sys_plat_log.php');
        $sys_plat_log = new \model_sys_plat_log($customer_id);
        $sys_plat_log->add_log('shop_system_currency_recharge_card','添加充值卡名称【'.$name.'】；');
	}else{
		$return_errmsg = $errmsg;
	}
	echo "<script>alert('".$return_errmsg."');location.href='currency_recharge.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
}

?>