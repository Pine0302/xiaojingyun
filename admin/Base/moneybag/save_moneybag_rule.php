<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
require_once('../../../../weixinpl/common/utility_common.php');

$isopen_poundage 			= trim($configutil->splash_new($_POST["isopen_poundage"]));	//是否开启零钱收取手续费
$old_isopen_poundage 		= trim($configutil->splash_new($_POST["old_isopen_poundage"]));	//旧的收取手续费
$old_poundage_percentage 	= trim($configutil->splash_new($_POST["old_poundage_percentage"]));	//零钱手续费比例
$poundage_percentage 		= trim($configutil->splash_new($_POST["poundage_percentage"]));	//零钱手续费比例
$poundage_percentage  		=sprintf("%.4f", $poundage_percentage);
$isOpen_callback 			= trim($configutil->splash_new($_POST["isOpen_callback"]));	//是否开启零钱提现
$isOpen_alipay 				= trim($configutil->splash_new($_POST["isOpen_alipay"]));	//是否开启支付宝提现
$isOpen_wechat 				= trim($configutil->splash_new($_POST["isOpen_wechat"]));	//是否开启微信零钱提现
$isOpen_financial 			= trim($configutil->splash_new($_POST["isOpen_financial"]));	//是否开启财付通提现
$isOpen_bank 				= trim($configutil->splash_new($_POST["isOpen_bank"]));	//是否开启银行卡提现
$isOpen_ips 				= trim($configutil->splash_new($_POST["isOpen_ips"]));	//是否开启环迅账户提现
$isOpen_agreement 			= trim($configutil->splash_new($_POST["isOpen_agreement"]));	//是否开启提现协议
$isOpen_massage 			= trim($configutil->splash_new($_POST["isOpen_massage"]));	//是否开启提现协议
$islogin_app 	    		= trim($configutil->splash_new($_POST["islogin_app"]));	//是否仅登录过app才能提现
$isin_app 	   				= trim($configutil->splash_new($_POST["isin_app"]));	//是否仅使用app才能提现
$start_time 				= trim($configutil->splash_new($_POST["start_time"]));		//每月提现开始日期
$end_time 					= trim($configutil->splash_new($_POST["end_time"]));			//每月提现结束日期
$week_time 					= trim($configutil->splash_new($_POST["week_time"]));			//提现可设置按每周几提现
$mini_callback 				= trim($configutil->splash_new($_POST["mini_callback"]));		//最低提现金额
$max_callback 				= trim($configutil->splash_new($_POST["max_callback"]));		//不可提现金额
$full_vpscore 				= trim($configutil->splash_new($_POST["full_vpscore"]));		//提现vp值限制
$is_fee 					= trim($configutil->splash_new($_POST["is_fee"]));				//提现手续费开关
$callback_fee 				= trim($configutil->splash_new($_POST["callback_fee"]));		//提现手续费比例
$callback_fee_flxed 		= trim($configutil->splash_new($_POST["callback_fee_flxed"]));	//提现手续费固定金额
$is_fee_money 				= trim($configutil->splash_new($_POST["is_fee_money"]));		//按金额收取手续费开关
$is_currency 				= trim($configutil->splash_new($_POST["is_currency"]));			//提现返送购物币开关
$callback_currency 			= trim($configutil->splash_new($_POST["callback_currency"]));	//提现返送购物币
$fee_type					= trim($configutil->splash_new($_POST["fee_type"]));			//手续费类型，1：固定金额，2：比例
$cash_coefficient			= trim($configutil->splash_new($_POST["cash_coefficient"]));	//提现系数
$is_withdraw_send_currency	= trim($configutil->splash_new($_POST["is_withdraw_send_currency"]));	//是否提现送购物币，0：关，1：开
$withdraw_send_currency		= trim($configutil->splash_new($_POST["withdraw_send_currency"]));	//提现送购物币比例
//$remark 			= mysql_real_escape_string(trim($configutil->splash_new($_POST["remark"])));
$remark 					= trim($configutil->splash_new($_POST["remark"]));
$id 						= -1;
//echo '<pre>';var_dump($_POST);
if( $is_currency == 'on' ){
	$is_currency  = 1;
}else{
	$is_currency = 0;
}
if( $is_fee_money == 'on' ){
	$is_fee_money  = 1;
}else{
	$is_fee_money = 0;
}
$withdraw_send_currency = round($withdraw_send_currency,3);
//echo $customer_id;
// echo "isOpen_callback==".$isOpen_callback."start_time==".$start_time."end_time==".$end_time."week_time==".$week_time."mini_callback==".$mini_callback."mini_callback==".$mini_callback."max_callback==".$max_callback."full_vpscore==".$full_vpscore."is_fee==".$is_fee."is_currency==".$is_currency;

$query = "SELECT id FROM moneybag_rule where isvalid=true and customer_id=".$customer_id." LIMIT 1";
//echo $query;die;
$result= _mysql_query($query);
while($row=mysql_fetch_object($result)){
	$id = $row->id;
}
if($id<0){
		$sql = "INSERT INTO moneybag_rule(isvalid,
										  customer_id,
										  isOpen_callback,
										  start_time,
										  end_time,
										  week_time,
										  mini_callback,
										  max_callback,
										  callback_currency,
										  callback_fee,
										  full_vpscore,
										  createtime,
										  remark,
										  isOpen_alipay,
										  isOpen_wechat,
										  isOpen_financial,
										  isOpen_bank,
										  isOpen_ips,
										  isOpen_agreement,
										  isOpen_massage,
										  callback_fee_flxed,
										  is_fee,
										  is_currency,
										  fee_type,
										  cash_coefficient,
										  islogin_app,
										  isin_app,
										  is_fee_money,
										  is_withdraw_send_currency,
										  withdraw_send_currency,
										  isopen_poundage,
										  poundage_percentage
										  ) 
									VALUES(true,
										   $customer_id,
										   $isOpen_callback,
										   $start_time,
										   $end_time,
										   $week_time,
										   $mini_callback,
										   $max_callback,
										   $is_currency,
										   $is_fee,
										   $full_vpscore,
										   now(),
										   '{$remark}',
										   $isOpen_alipay,
										   $isOpen_wechat,
										   $isOpen_financial,
										   $isOpen_bank,
										   $isOpen_ips,
										   $isOpen_agreement,
										   '{$isOpen_massage}',
										   $callback_fee_flxed,
										   $is_fee,
										   $is_currency,
										   '{$fee_type}',
										   $cash_coefficient,
										   $islogin_app,
										   $isin_app,
										   $is_fee_money,
										   $is_withdraw_send_currency,
										   $withdraw_send_currency,
										   '{$isopen_poundage}',
										   $poundage_percentage
										   )";
//		echo $sql;
		_mysql_query($sql) or die('Query failed56: ' . mysql_error().$sql);
}else{
		$sql = "UPDATE moneybag_rule SET isOpen_callback=$isOpen_callback,
										 start_time=$start_time,
										 end_time=$end_time,
										 week_time=$week_time,
										 mini_callback=$mini_callback,
										 max_callback=$max_callback,
										 callback_currency=$callback_currency,
										 callback_fee=$callback_fee,
										 full_vpscore=$full_vpscore,
										 createtime=now(),
										 remark='$remark',
										 isOpen_alipay=$isOpen_alipay,
										 isOpen_wechat=$isOpen_wechat,
										 isOpen_financial=$isOpen_financial,
										 isOpen_bank=$isOpen_bank,
										 isOpen_ips=$isOpen_ips,
										 isOpen_agreement=$isOpen_agreement,
										 callback_fee_flxed=$callback_fee_flxed,
										 is_fee=$is_fee,
										 is_currency=$is_currency,
										 fee_type=$fee_type,
										 cash_coefficient=$cash_coefficient,
										 islogin_app=$islogin_app,
										 isin_app=$isin_app,
										 is_fee_money=$is_fee_money,
										 is_withdraw_send_currency=$is_withdraw_send_currency,
										 withdraw_send_currency=$withdraw_send_currency
									  WHERE id=$id";
		_mysql_query($sql)or die('Query failed67: ' . mysql_error());
    // echo $sql;
}

$operation_user = '';	//操作人
if ( $_SESSION['curr_login'] != '' ){
	$operation_user = $_SESSION['curr_login'];
}
//零钱手续费的每次开启或关闭或变更比例都需要记录日志
$is_change = 0 ;
if( $old_isopen_poundage != $isopen_poundage ){
	$is_change = 1;
	if( $isopen_poundage == 1 ){
		$remark = '开启零钱支付需手续费开关，设置比例为：'.$poundage_percentage.'%';
	}else{
		$remark = '关闭零钱支付需手续费开关';
	}

}else if( $old_isopen_poundage == 1 && $isopen_poundage == 1 && $old_poundage_percentage != $poundage_percentage ){
	$is_change = 1;
	$remark = '修改零钱支付手续费比例为：'.$poundage_percentage.'%';
}
if( $is_change == 1 ){
	$query_log = "INSERT INTO weixin_moneybag_poundage_setting_log(
								setting_id,
								industry_name,
								remark,
								customer_id,
								isvalid,
								createtime
							) VALUES (
								1,
								'线上商城',
								'".$remark."',
								".$customer_id.",
								true,
								now()
								)";
	_mysql_query($query_log) or die('Query_log failed:'.mysql_error());
}

//插入日志
$operation_log = new shopOperationLog_Utlity();
$operation_log -> operationLog($customer_id,$sql,$operation_user);
echo "<script>window.location.href='./moneybag.php?customer_id=".$customer_id_en."'</script>";

?>