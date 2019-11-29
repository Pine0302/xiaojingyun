<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$cat_id = $configutil->splash_new($_POST["type_id"]);

$query="select id,name from weixin_commonshop_products where isvalid=true and isout=false and customer_id=".$customer_id;
$typeson_id=array();
// if( $cat_id >0 ){
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
		// unset($typeson_id);
	
// }
// var_dump($typeson_id);
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  

$str = [];
while ($row = mysql_fetch_object($result)) {
    $pid = $row->id;
	$pname = mysql_real_escape_string($row->name);
	// $str = $str."{pid:".$pid.",pname:'".$pname."'},";
	$str[] = array('pid'=>$pid,'pname'=>$pname);
}
// $str = substr($str,0,-1);

mysql_close($link);
  
 
echo json_encode($str);

?>