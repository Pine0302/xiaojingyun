<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
date_default_timezone_set(PRC);
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require_once("../../../../weixinpl/common/common_ext.php");
require('../../../../weixinpl/common/utility_shop.php');
$callback = $configutil->splash_new($_GET["callback"]);
$user_id =$configutil->splash_new($_GET["user_id"]);
$id =$configutil->splash_new($_GET["id"]);
$money = 0;
if(!empty($_GET["money"])){
	$money =$configutil->splash_new($_GET["money"]);
}
$errcode = 0;			//返回码
$errmsg  = "succee";	//返回信息

$stringtime = date("Y-m-d H:i:s",time());  
$batchcode=strtotime($stringtime); 
$batchcode=$user_id.$batchcode;
$query2="select agent_inventory from promoters where status=1 and isvalid=true and user_id=".$user_id;	//查找代理商代理入账总金额
$agent_inventory = 0;
$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
while ($row2 = mysql_fetch_object($result2)) {
	$agent_inventory = $row2->agent_inventory;
}

_tran_start(); //开始事务

$agent_inventory = $agent_inventory + $money;	//充值后金额
$sql1 = "insert into weixin_commonshop_agentfee_records(user_id,batchcode,price,detail,type,isvalid,createtime,after_inventory,withdrawal_id) values(".$user_id.",'".$batchcode."',".$money.",'商家充值',3,true,now(),".$agent_inventory.",-1)";
_mysql_query($sql1);		//插入 充值记录
$sql = "update promoters set agent_inventory=".$agent_inventory." where user_id=".$user_id;
_mysql_query($sql);

				


$error =mysql_error();
if( $error != ""){
	$errcode = 40004;
	$errmsg  = "充值失败：".$error;
	
	_tran_rollback();//事务回滚
}else{
	_tran_commit(); //事务提交
}

mysql_close($link);
echo $callback."([{errcode:".$errcode.",errmsg:'".urlencode($errmsg)."',id:".$id.",agent_inventory:".$agent_inventory."}";
echo "]);";
echo $callback;
?>