<?php
header("Content-type: text/html; charset=utf-8");  
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require_once("../../../../weixinpl/common/common_ext.php");
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$supplys_id = i2post("supplys_id"); 
$op 	    = i2post("op");

$query="select id from ".WSY_SHOP.".suning_supplys_setting where isvalid=true and customer_id=".$customer_id." and supply_id=".$supplys_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $suning_id 	   = $row->id;
}
switch($op){
    case 'open':
    	$export_suning_product = 1;
    	$create_suning_order = 1;
	   break;
	case 'close':
		$export_suning_product = 0;
    	$create_suning_order = 0;
	   break;
}
if($suning_id>0){	
	$sql="update ".WSY_SHOP.".suning_supplys_setting set export_suning_product=".$export_suning_product.",create_suning_order=".$create_suning_order." where id=".$suning_id;
}else{
	$sql = "insert into ".WSY_SHOP.".suning_supplys_setting(supply_id,customer_id,export_suning_product,create_suning_order,isvalid,createtime) values (".$supplys_id.",".$customer_id.",".$export_suning_product.",".$create_suning_order.",true,now())";
}
// echo $sql;
_mysql_query($sql);
?>