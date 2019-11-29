<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');

$sendstatus=0;
$is_showdiscount=0;
$agent_price =$configutil->splash_new($_POST["agent_price"]);
$agent_price=str_replace(" ","",$agent_price);
//echo $agent_price;return;
$agent_detail =$configutil->splash_new($_POST["agent_detail"]);
$sendstatus =$configutil->splash_new($_POST["sendstatus"]);
$is_showdiscount =$configutil->splash_new($_POST["is_showdiscount"]);
$is_export_order =$configutil->splash_new($_POST["isExportOrder"]);
//echo $sendstatus;return;
$not_agent_tip ="对不起,你仍未成为代理商";
if(!empty($_POST["not_agent_tip"])){
$not_agent_tip =$configutil->splash_new($_POST["not_agent_tip"]);
}


$isOpenAgent =$configutil->splash_new($_POST["isOpenAgent"]);

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
 

$query="select id from weixin_commonshop_agents where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$agent_id=-1;
while ($row = mysql_fetch_object($result)) {
	$agent_id = $row->id;
}
if($agent_id>0){
	$sql="update weixin_commonshop_agents set agent_price='".$agent_price."',not_agent_tip='".$not_agent_tip."',agent_detail='".$agent_detail."',sendstatus=".$sendstatus.",is_showdiscount=".$is_showdiscount.",is_export_order=".$is_export_order." where id=".$agent_id;
	// echo $sql."<br/>";
	_mysql_query($sql);
	
}else{
	$sql = "insert into weixin_commonshop_agents(customer_id,agent_price,agent_detail,isvalid,createtime,not_agent_tip,sendstatus,is_showdiscount,is_export_order) values (".$customer_id.",'".$agent_price."','".$agent_detail."',true,now(),'".$not_agent_tip."',".$sendstatus.",".$is_showdiscount.",".$is_export_order.")";
	// echo $sql."<br/>";
	_mysql_query($sql);
 }
 $sql="update weixin_commonshops set isOpenAgent=".$isOpenAgent." where customer_id=".$customer_id;
_mysql_query($sql);
 $error =mysql_error();
 mysql_close($link);
//echo $error; 
 echo "<script>location.href='set.php?customer_id=".$customer_id_en."';</script>"
?>