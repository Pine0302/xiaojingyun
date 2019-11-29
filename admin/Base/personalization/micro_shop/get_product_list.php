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
$pid=0;
if(!empty($_GET["pid"])){
    $pid =$configutil->splash_new($_GET["pid"]);
}
$num=0;
if(!empty($_GET["num"])){
    $num =$configutil->splash_new($_GET["num"]);
}

$callback = $configutil->splash_new($_GET["callback"]);

$query = "select id,name from weixin_commonshop_products where isvalid=true and isout=0 and customer_id=".$customer_id;	
$typeson_id=array();
if($cat_id >0){
		$type_son="select id from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=".$cat_id." and customer_id=".$customer_id."";
		$result_typeson=_mysql_query($type_son) or die ('typeson faild ' .mysql_error());
		while($row=mysql_fetch_object($result_typeson)){

			$typeson_id[]=$row->id;
		}
		if(empty($typeson_id)){
			$typeson_id=$cat_id; 
		}else{
			array_push($typeson_id,$cat_id);
			$typeson_id=implode(',',$typeson_id);
		}
			$query = $query." and (";
			$typeson_id_arr = explode(",",$typeson_id);
			$typeson_id_count = count($typeson_id_arr);
			for($j=0;$j<$typeson_id_count;$j++){
				$o_typeid = $typeson_id_arr[$j];
				if($j==0){
					$query = $query."( LOCATE(',".$o_typeid.",', type_ids)>0)";
					}else{
					$query = $query." or (LOCATE(',".$o_typeid.",', type_ids)>0)";
				}
			}
			$query = $query.")";
			unset($typeson_id);
		
	}

//$query="select id,name from weixin_commonshop_products where isvalid=true ";
//$query=$query." and (type_id=".$tid." or (LOCATE(',".$tid.",', type_ids)>0))";
$result = _mysql_query($query) or die('Query failed1: ' . mysql_error());  


$str="{pos:".$pos."},{pid:".$pid."},{num:".$num."}";
while ($row = mysql_fetch_object($result)) {

    $pid = $row->id;
	$pname = $row->name;
	
	$str = $str.",{pid:".$pid.",pname:'".$pname."'}";
	
}


 //echo $error;
 mysql_close($link);
  
 
 echo $callback."([".$str;
echo "]);";
echo $callback;
 

?>