<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
$tid = $configutil->splash_new($_GET["type_id"]);

$supplier_id=0;
if(!empty($_GET["supplier_id"])){
    $supplier_id =$configutil->splash_new($_GET["supplier_id"]);
}
$pos=0;
if(!empty($_GET["pos"])){
    $pos =$configutil->splash_new($_GET["pos"]);
}
if(isset($_GET["sort"])){
    $sort =$configutil->splash_new($_GET["sort"]);
}
$pid=0;
if(!empty($_GET["pid"])){
    $pid =$configutil->splash_new($_GET["pid"]);
}
$detail_id = -1;
if(!empty($_GET["detail_id"])){
    $detail_id =$configutil->splash_new($_GET["detail_id"]);
}
$callback = $configutil->splash_new($_GET["callback"]);

$query="select id,name from weixin_commonshop_products where isvalid=true ";
if($tid>0){
   $query=$query." and (LOCATE(',".$tid.",', brand_type_ids)>0 OR (LOCATE(',".$tid.",', type_ids)>0 and is_supply_id=".$supplier_id."))"; 
}else{
   $query=$query." and is_supply_id=".$supplier_id; 
}

$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  

$str="{pos:".$pos."},{pid:".$pid."},{sort:".$sort."},{detail_id:".$detail_id."}";
while ($row = mysql_fetch_object($result)) {

    $pid = $row->id;
	$pname = mysql_real_escape_string($row->name);
	//$str = $str.",{pid:".$pid.",pname:'".$pname."'}";
	$str = $str.",{pid:".$pid.",pname:'".$pname."'}";
	
}


 //echo $error;
 mysql_close($link);
  
 
 echo $callback."([".$str;
echo "]);";
echo $callback;
 

?>