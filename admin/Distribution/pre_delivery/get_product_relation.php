<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

/*$delivery_id = $configutil->splash_new($_POST["delivery_id"]);

$pid_arr = [];
$query_product_relation = "SELECT pid FROM weixin_commonshop_pre_delivery_product_relation WHERE delivery_id=".$keyid." AND isvalid=true";
$result_product_relation = _mysql_query($query_product_relation) or die('Query_product_relation failed:'.mysql_error());
while( $row_product_relation = mysql_fetch_object($result_product_relation) ){
	$pid_arr[] = $row_product_relation -> pid;
}
$pid_str = implode(',',$pid_arr);
if( $pid_str == '' ){
	$pid_str = -1;
}*/
$customer_id = passport_decrypt($customer_id);
$delivery_id = $configutil->splash_new($_POST["delivery_id"]);
$pid_str = $configutil->splash_new($_POST["pid_str"]);
$search_name = $configutil->splash_new($_POST["search_name"]);
$limitstart = $configutil->splash_new($_POST["limitstart"]);
$limitend = $configutil->splash_new($_POST["limitend"]);
$supply_id = -1;
if( !empty($_POST["supply_id"]) ){
	$supply_id = $configutil->splash_new($_POST["supply_id"]);
}

$data = [];
$i = 0;

$pcount = 0;
$query_count = "SELECT count(1) as pcount FROM weixin_commonshop_products WHERE isvalid=true AND isout=false AND isout_status=true AND is_QR=false AND is_virtual=false AND customer_id=".$customer_id." AND is_supply_id=".$supply_id;

$query_product = "SELECT id,storenum,name,type_ids,default_imgurl,now_price,is_supply_id,brand_type_ids FROM weixin_commonshop_products WHERE isvalid=true AND isout=false AND isout_status=true AND is_QR=false AND is_virtual=false AND customer_id=".$customer_id." AND is_supply_id=".$supply_id;

if( $pid_str != '' ){
	$query_product .= " AND id in(".$pid_str.") ";
	$query_count .= " AND id in(".$pid_str.") ";
} else {
	$query_not_in = "SELECT pid FROM weixin_commonshop_pre_delivery_product_relation WHERE delivery_id!=".$delivery_id." AND customer_id=".$customer_id." AND isvalid=true";
	$query_product .= " AND id not in(".$query_not_in.") ";
	$query_count .= " AND id not in(".$query_not_in.") ";
}
if( $search_name != '' ){
	$query_product .= " AND name like '%".$search_name."%' ";
	$query_count .= " AND name like '%".$search_name."%' ";
}

$result_count = _mysql_query($query_count) or die('Query_count failed:'.mysql_error());
while( $row_count = mysql_fetch_object($result_count) ){
	$pcount = $row_count -> pcount;
}

$query_product .= " LIMIT ".$limitstart.",".$limitend;
// echo $query_product;die;
$result_product = _mysql_query($query_product) or die('Query_product failed:'.mysql_error());
while( $row_product = mysql_fetch_object($result_product) ){
	$pid 			= $row_product -> id;
	$storenum 		= $row_product -> storenum;
	$pname 			= $row_product -> name;
	$type_ids 		= $row_product -> type_ids;
	$default_imgurl = $row_product -> default_imgurl;
	$now_price 		= $row_product -> now_price;
	$supply_id 		= $row_product -> is_supply_id;
	$brand_type_ids = $row_product -> brand_type_ids;
	
	$type_id = -1;
	$type_name = '';
	if( $brand_type_ids != '' ){
		$brand_type_ids_arr = explode(',',$brand_type_ids);
		if( !empty($brand_type_ids_arr[1]) ){
			$type_id = $brand_type_ids_arr[1];
		}
		
		$query_type = "SELECT type_name FROM weixin_commonshop_supply_type WHERE id=".$type_id." AND isvalid=true";
		$result_type = _mysql_query($query_type) or die('Query_type failed:'.mysql_error());
		while( $row_type = mysql_fetch_object($result_type) ){
			$type_name = $row_type -> type_name;
		}
	} else {
		$type_ids_arr = explode(',',$type_ids);
		if( !empty($type_ids_arr[0]) ){
			$type_id = $type_ids_arr[0];
		} else if( !empty($type_ids_arr[1]) ){
			$type_id = $type_ids_arr[1];
		}
		
		$query_type = "SELECT name FROM weixin_commonshop_types WHERE id=".$type_id." AND isvalid=true";
		$result_type = _mysql_query($query_type) or die('Query_type failed:'.mysql_error());
		while( $row_type = mysql_fetch_object($result_type) ){
			$type_name = $row_type -> name;
		}
	}
	
	//防伪二维码
	$qrcount = 0;
	if( $supply_id == -1 ){
		$query_qr = "SELECT count(1) as qrcount FROM product_security_code_t WHERE p_id=".$pid." AND isvalid=true";
		$result_qr = _mysql_query($query_qr) or die('Query_qr failed:'.mysql_error());
		while( $row_qr = mysql_fetch_object($result_qr) ){
			$qrcount = $row_qr -> qrcount;
		}
	}
	
	$data[$i]['pid'] = $pid;
	$data[$i]['storenum'] = $storenum;
	$data[$i]['pname'] = $pname;
	$data[$i]['default_imgurl'] = $default_imgurl;
	$data[$i]['now_price'] = $now_price;
	$data[$i]['type_name'] = $type_name;
	$data[$i]['qrcount'] = $qrcount;
	$data[$i]['pcount'] = $pcount;
	
	$i++;
}
echo json_encode($data);
?>