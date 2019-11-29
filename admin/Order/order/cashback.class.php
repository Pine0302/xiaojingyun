<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');

$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');


$batchcode = $configutil->splash_new($_POST["batchcode"]);
$op = $configutil->splash_new($_POST["op"]);
$type = $configutil->splash_new($_POST["type"]);
 
 switch($op){
	case 'del_s':
		// $batchcode_status = 0;
		// switch ($type) {
		// 	case 0: //商城
		// 		$query = "select status from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."' and customer_id=".$customer_id; 
		// 		$result = _mysql_query($query) or die('L18: '.mysql_error());
		// 		while($row = mysql_fetch_object($result)){
		// 			$batchcode_status = $row->status;
		// 		}
		// 		break;
			
  //           case 2:   //餐饮
  //           case 20:  //线下商城
  //           case 30:  //KTV
  //           case 60:  //酒店
		// 		$query = "select status from weixin_cityarea_orders where isvalid=true and batchcode='".$batchcode."' and customer_id=".$customer_id; 
		// 		$result = _mysql_query($query) or die('L18: '.mysql_error());
		// 		while($row = mysql_fetch_object($result)){
		// 			$batchcode_status = $row->status;
		// 		}
		// 		break;
		// 	case 100:
		// 		$query = "select order_status from now_pay_orders where isvalid=true and order_platform='".$batchcode."' and custid=".$customer_id; 
		// 		$result = _mysql_query($query) or die('L18: '.mysql_error());
		// 		while($row = mysql_fetch_object($result)){
		// 			$batchcode_status = $row->order_status;
		// 		}
		// 		break;
		// 	case 101:
		// 		$query = "select order_state from now_pay_product_orders where isvalid=true and batchcode='".$batchcode."' and customer_id=".$customer_id; 
		// 		$result = _mysql_query($query) or die('L18: '.mysql_error());
		// 		while($row = mysql_fetch_object($result)){
		// 			$batchcode_status = $row->order_state;
		// 		}
		// 		break;
		// 	case 110:
		// 		$query = "select status from package_order_t where isvalid=true and batchcode='".$batchcode."' and customer_id=".$customer_id; 
		// 		$result = _mysql_query($query) or die('L18: '.mysql_error());
		// 		while($row = mysql_fetch_object($result)){
		// 			$batchcode_status = $row->status;
		// 		}
		// 	break;
		// }

		// if($batchcode_status){
		// 	$query = "select id from cashback where batchcode='".$batchcode."' and customer_id='".$customer_id."'"; 
		// 	$result = _mysql_query($query) or die('L18: '.mysql_error());
		// 	$obj = mysql_fetch_assoc($result);
		// 	$row = mysql_fetch_object($result);

		// 	if(!$obj) {
		// 		$sql = "update cashback_t set isvalid=false where isvalid=true and batchcode='".$batchcode."'";
		// 		_mysql_query($sql);
		// 	}

		// 	$sql = "update cashback set isvalid=false where isvalid=true and batchcode='".$batchcode."'";
		// 	_mysql_query($sql);
		// }else{
		// 	$sql = "update cashback_t set isvalid=false where isvalid=true and batchcode='".$batchcode."'";
		// 	_mysql_query($sql);
		// }
		
		$sql = "update cashback_t set isvalid=false where isvalid=true and batchcode='".$batchcode."'";
		_mysql_query($sql);
		$sql = "update cashback set isvalid=false where isvalid=true and batchcode='".$batchcode."'";
		_mysql_query($sql);
		
		$sql1 = "select login_name from customers where id=".$customer_id;
		$result1= _mysql_query($sql1) or die('Query failed: ' . mysql_error());
		while($row=mysql_fetch_object($result1)){
		    $operator  = $row->login_name;
		}
		$remark = "删除消费奖励订单:{$batchcode}";
		$sql2 = "insert into ".WSY_REBATE.".cashback_op_log(customer_id,isvalid,createtime,operator,type,remark) values('".$customer_id."',true,now(),'".$operator."',1,'".$remark."')";
		_mysql_query($sql2);
		
		$json["status"] = 0;
		$json["msg"] = "删除成功！";

		break;  
 }





$error =mysql_error();
if(!empty($error)){
	$json["status"] = 10002;
	$json["msg"] = $error;	
}

if($link){mysql_close($link);} 

$jsons=json_encode($json);
die($jsons);
?>