<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
$cat_id = $configutil->splash_new($_GET["type_id"]);

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
$supply_id = -1;
if(!empty($_GET["supply_id"])){
    $supply_id =$configutil->splash_new($_GET["supply_id"]);
}
$callback = $configutil->splash_new($_GET["callback"]);

$query="select id,name,issnapup,buystart_time,countdown_time from weixin_commonshop_products where isvalid=true and isout=false and customer_id=".$customer_id;
$typeson_id=array();
// if( $cat_id >0 ){
	if( $supply_id > 0 ){
		$query = $query." AND (LOCATE(',".$cat_id.",', brand_type_ids)>0 OR (LOCATE(',".$cat_id.",', type_ids)>0 AND is_supply_id=".$supply_id."))";
		
	} else {
		/* 查找该分类的所有子分类 start */
		$query_child = "SELECT id FROM weixin_commonshop_types WHERE customer_id=".$customer_id." AND isvalid=true AND is_shelves=1 AND LOCATE(',".$cat_id.",', gflag)>0 ";
		$result_child = _mysql_query($query_child) or die('Query_child failed:'.mysql_error());
		while( $row_child = mysql_fetch_object($result_child) ){
			$child_id = $row_child -> id;
			
			$typeson_id[] = $child_id;
		}
		/* 查找该分类的所有子分类 end */
		
		if(empty($typeson_id)){
			$typeson_id = $cat_id; 
		}else{
			array_push($typeson_id,$cat_id);
			$typeson_id = implode(',',$typeson_id);
		}
		
		$query = $query." and (";
		$typeson_id_arr = explode(",",$typeson_id);
		$typeson_id_count = count($typeson_id_arr);
		for( $j=0; $j<$typeson_id_count; $j++ ){
			$o_typeid = $typeson_id_arr[$j];
			if( $j == 0 ){
				$query = $query."( LOCATE(',".$o_typeid.",', type_ids)>0)";
			}else{
				$query = $query." or (LOCATE(',".$o_typeid.",', type_ids)>0)";
			}
		}
		$query = $query.")";
		unset($typeson_id);
	}
	
// }
//echo $query;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  

$str="{pos:".$pos."},{pid:".$pid."},{sort:".$sort."},{detail_id:".$detail_id."}";
while ($row = mysql_fetch_object($result)) {
    $pid = $row->id;
	$pname = mysql_real_escape_string($row->name);
	$issnapup = $row->issnapup;
	$start_time = $row->buystart_time;
	$end_time = $row->countdown_time;
	$str = $str.",{pid:".$pid.",pname:'".$pname."',issnapup:".$issnapup.",start_time:'".$start_time."',end_time:'".$end_time."'}";
	
}


 //echo $error;
mysql_close($link);
  
 
echo $callback."([".$str;
echo "]);";
echo $callback;
 

?>