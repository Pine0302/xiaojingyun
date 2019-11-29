<?php  
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');   //配置
require('../../../../weixinpl/customer_id_decrypt.php');   //解密参数

require('../../../../weixinpl/back_init.php');

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require('../../../../weixinpl/proxy_info.php');
//$_GET['batchcode'] = '1959591460711698';
$array_print_orde = array();
if(isset($_GET['batchcode']) && (is_numeric($_GET['batchcode']))){
	$batchcode = $_GET['batchcode'];
	$json_print_orde[0] = print_order($batchcode);
	echo json_encode($json_print_orde);
}else if(isset($_GET['print_temp_id']) && (is_numeric($_GET['print_temp_id']))){
	$print_temp_id = $_GET['print_temp_id']; $customer_id = $_GET['customer_id'];
	$json_print_orde = array();
	$array_print_temp_orders = print_temp_orders($print_temp_id,$customer_id);
	//print_r($array_print_temp_orders);die();
	foreach($array_print_temp_orders as $val){
		$json_print_orde[] = print_order($val['batchcode']);
	}
	echo json_encode($json_print_orde);
	
}else if(isset($_GET['customer_id']) && (is_numeric($_GET['customer_id']))){
	$array_echo = array(); $customer_id = $_GET['customer_id'];
	$print_temp_array = print_temp_list($customer_id);$i_key=0;//print_r($print_temp_array);die();
	foreach($print_temp_array as $key=>$val){
		$int_print_temp_count = print_temp_count($val['id'],$customer_id);
		if($int_print_temp_count>0){
			$array_echo[$i_key]['print_count'] = $int_print_temp_count;
			$array_echo[$i_key]['print_id'] = $val['id'];
			$array_echo[$i_key]['print_name'] = $val['print_name']; $i_key++;			
		}		
	}
	//print_r($array_echo);die();
	echo json_encode($array_echo);
}
 


mysql_close($link); 


function print_order($batchcode){
	$json_print_orde = array(); $sql_field = 'id,express_id,send_express_id,batchcode,customer_id,supply_id,pid,prvalues';
	$sql_commonshop_orders = "select $sql_field from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."' limit 0,1";
	$obj_commonshop_orders = _mysql_query($sql_commonshop_orders) or die('Query_is_remind5 failed: ' . mysql_error());
	$row_commonshop_orders = mysql_fetch_array($obj_commonshop_orders,MYSQL_ASSOC);
	if($row_commonshop_orders !== false){
		//$array_print_orde['order_info'] = $row_commonshop_orders;
		$array_product_count = product_count($row_commonshop_orders['batchcode']);
		$array_order_address = order_address($row_commonshop_orders['batchcode']);
		// print_r($array_order_address);
		$array_returnaddress = returnaddress($row_commonshop_orders['customer_id']);
		$array_product_count['goods_mark'] = product_mark($row_commonshop_orders['customer_id'],$row_commonshop_orders['pid'],$row_commonshop_orders['prvalues']);
		
		$array_print_orde['order_info']['order_number'] 	= $row_commonshop_orders['batchcode']; //订单号
		$array_print_orde['order_info']['goods_spec'] 		= ''; //商品规格
		$array_print_orde['order_info']['goods_number'] 	= ''; //商品编码
		$array_print_orde['order_info']['goods_name'] 		= $array_product_count['goods_name']; //商品名称
		$array_print_orde['order_info']['goods_no'] 		= ''; //商品货号
		$array_print_orde['order_info']['goods_quantity'] 	= $array_product_count['goods_quantity']; //商品数量
		$array_print_orde['order_info']['goods_mark'] 		= $array_product_count['goods_mark']; //商品外部标识
		$array_print_orde['order_info']['receiver'] 		= $array_order_address['name']; //收货人
		$array_print_orde['order_info']['receive_province']     = $array_order_address['location_p']; //收货-省
		$array_print_orde['order_info']['receive_city'] 	= $array_order_address['location_c']; //收货-市
		$array_print_orde['order_info']['receive_area'] 	= $array_order_address['location_a']; //收货-区
		$array_print_orde['order_info']['receive_address'] 	= $array_order_address['location_p']. $array_order_address['location_c'].$array_order_address['location_a'].$array_order_address['address']; //收货-地址
		$array_print_orde['order_info']['receive_zipcode'] 	= ''; //收货-邮编
		$array_print_orde['order_info']['receive_phone'] 	= $array_order_address['phone']; //收货-电话
		$array_print_orde['order_info']['shop_name'] 		= $array_returnaddress['name']; //发货人
		$array_print_orde['order_info']['ship_address'] 	= $array_returnaddress['re_address']; //发货-地址
		$array_print_orde['order_info']['ship_phone'] 		= $array_returnaddress['re_phone']; //发货-电话
		$array_print_orde['order_info']['ship_year'] 		= $array_returnaddress['re_year']; //发货-年
		$array_print_orde['order_info']['ship_month'] 		= $array_returnaddress['re_month']; //发货-月
		$array_print_orde['order_info']['ship_day'] 		= $array_returnaddress['re_day']; //发货-日

		//$array_print_orde['print_info']				 		= print_info($row_commonshop_orders['express_id']);
        $array_print_orde['print_info']				 		= print_info($row_commonshop_orders['send_express_id'],$row_commonshop_orders['supply_id'],$row_commonshop_orders['customer_id']);
		
		$json_print_orde = $array_print_orde;
		
		//print_r($array_print_orde);die();
	}
	return $json_print_orde;
}




function product_count($batchcode){
	$result = array(); $products_name = ''; $products_rcount = 0;
	$sql_commonshop_orders = "SELECT ord.id, ord.rcount, pro.name, pro.foreign_mark FROM weixin_commonshop_orders as ord INNER JOIN weixin_commonshop_products as pro ON ord.pid=pro.id WHERE ord.batchcode='".$batchcode."'";
	$obj_commonshop_orders = _mysql_query($sql_commonshop_orders) or die('Query_is_remind6 failed: ' . mysql_error());	
	while($row_commonshop_orders = mysql_fetch_array($obj_commonshop_orders,MYSQL_ASSOC)){		
		$products_name .= $row_commonshop_orders['name'].'<br>';
		$products_foreign_mark .= $row_commonshop_orders['foreign_mark'].'<br>';
		$products_rcount += $row_commonshop_orders['rcount'];
	}
	if($products_rcount>0){
		$result = array('goods_name'=>$products_name,'goods_quantity'=>$products_rcount,'goods_mark'=>$products_foreign_mark,);
	}
	return $result;
}

function product_mark($customer_id,$pid,$prvalues){
	$mark = '';
	$sql_commonshop_product = "SELECT foreign_mark from weixin_commonshop_products where id=".$pid."";
	if( !empty($prvalues) ){
		$sql_commonshop_product = "SELECT foreign_mark from weixin_commonshop_product_prices where  product_id=".$pid." and  proids='".$prvalues."'";
	}
	//echo $sql_commonshop_product;
	$sql_commonshop_product = _mysql_query($sql_commonshop_product) or die('Query_is_remind7 failed: ' . mysql_error());	
	while($row_commonshop_product = mysql_fetch_array($sql_commonshop_product,MYSQL_ASSOC)){		
		$mark .= $row_commonshop_product['foreign_mark'];
	}
	return $mark;
}

function order_address($batchcode){
	$reslut = array();
	$sql_commonshop_orders = "SELECT name,location_p,location_c,location_a,address,phone FROM `weixin_commonshop_order_addresses` WHERE batchcode='".$batchcode."' ";
	$obj_commonshop_orders = _mysql_query($sql_commonshop_orders) or die('Query_is_remind8 failed: ' . mysql_error());	
	$row_commonshop_orders = mysql_fetch_array($obj_commonshop_orders,MYSQL_ASSOC);
	if($row_commonshop_orders !== false){ $reslut = $row_commonshop_orders; }
	return $reslut;
}


function returnaddress($customer_id){
	$reslut = array();	
	$sql_commonshop_orders = "SELECT phone,tel,location_p,location_c,location_a,address,name FROM `weixin_commonshop_returnaddress` WHERE customer_id=".$customer_id." AND supplier_id='-1' AND isvalid=1 ";
	$obj_commonshop_orders = _mysql_query($sql_commonshop_orders) or die('Query_is_remind8 failed: ' . mysql_error());	
	$row_commonshop_orders = mysql_fetch_array($obj_commonshop_orders,MYSQL_ASSOC);
	if($row_commonshop_orders !== false){ $reslut = $row_commonshop_orders; }
	$reslut['re_address'] 	= $reslut['location_p'].$reslut['location_c'].$reslut['location_a'].$reslut['address'];	
	$reslut['re_phone'] 	= ''; if($reslut['phone'] == ''){$reslut['re_phone'] = $reslut['tel'];}else{$reslut['re_phone'] = $reslut['phone'];}
	$reslut['re_year'] 		= date('Y'); $reslut['re_month'] = date('m'); $reslut['re_day'] = date('d');
	return $reslut;
}


function print_info($express_id,$supply_id,$customer_id){
	$reslut = array();	
	if($express_id>0){
		$sql_commonshop_orders = "SELECT exp.id, prt.paper_width, prt.paper_height, prt.items_params FROM weixin_expresses_company as exp INNER JOIN weixin_print_temp as prt ON exp.print_temp_id=prt.id WHERE exp.id=".$express_id." AND prt.isvalid=1"; //echo $sql_commonshop_orders; die();
	}else{
		$sql_commonshop_orders = "SELECT exp.id, prt.paper_width, prt.paper_height, prt.items_params FROM weixin_expresses_company as exp INNER JOIN weixin_print_temp as prt ON exp.print_temp_id=prt.id" ;
		$sql_commonshop_orders .=" WHERE  exp.customer_id=".$customer_id." AND prt.isvalid=1 AND exp.is_default=1"; 
		if($supply_id>0){
			$sql_commonshop_orders .=" AND exp.supply_id=".$supply_id." "; 	
		}
		//echo $sql_commonshop_orders; die(); 
	}
	
	$obj_commonshop_orders = _mysql_query($sql_commonshop_orders) or die('Query_is_remind3 failed: ' . mysql_error());	
	$row_commonshop_orders = mysql_fetch_array($obj_commonshop_orders,MYSQL_ASSOC);
	if($row_commonshop_orders !== false){ $reslut = $row_commonshop_orders; }
	return $reslut;
}

function print_temp_list($ord_customer_id){
	$result = array();
	$sql_commonshop_orders = "SELECT id,print_name FROM `weixin_print_temp` WHERE isvalid=1 AND is_supply=0 AND customer_id=".$ord_customer_id;
	$obj_commonshop_orders = _mysql_query($sql_commonshop_orders) or die('Query_is_remind4 failed: ' . mysql_error());	
	while($row_commonshop_orders = mysql_fetch_array($obj_commonshop_orders,MYSQL_ASSOC)){ $result[] = $row_commonshop_orders; }
	return $result;
}


function print_temp_count($exp_print_temp_id,$ord_customer_id){

	//$sql_commonshop_orders = "SELECT count(distinct(ord.batchcode)) AS batchcode_count FROM weixin_expresses_company as exp INNER JOIN weixin_commonshop_orders as ord ON ord.express_id=exp.id WHERE exp.print_temp_id=".$exp_print_temp_id." AND exp.customer_id=".$ord_customer_id." AND ord.sendstatus=0 AND (ord.paystatus=1 OR ord.paystyle='货到付款') AND ord.isvalid=1 AND ord.supply_id<0 AND ord.sendway=0"; //echo($sql_commonshop_orders); echo '<br>';
        //$sql_commonshop_orders = "SELECT count(distinct(ord.batchcode)) AS batchcode_count FROM weixin_expresses_company as exp INNER JOIN weixin_commonshop_orders as ord ON ord.send_express_id=exp.id WHERE exp.print_temp_id=".$exp_print_temp_id." AND exp.customer_id=".$ord_customer_id." AND ord.sendstatus=true AND ord.isvalid=1 AND ord.supply_id<0";
        $sql_commonshop_orders = "SELECT count(distinct(ord.batchcode)) AS batchcode_count FROM weixin_expresses_company as exp INNER JOIN weixin_commonshop_orders as ord ON ord.send_express_id=exp.id WHERE exp.print_temp_id=".$exp_print_temp_id." AND exp.customer_id=".$ord_customer_id." AND  ord.isvalid=1 AND ord.supply_id<0 AND ord.sendway=0";
	$obj_commonshop_orders = _mysql_query($sql_commonshop_orders) or die('Query_is_remind1 failed: ' . mysql_error());	
	$row_commonshop_orders = mysql_fetch_object($obj_commonshop_orders);
	return $row_commonshop_orders->batchcode_count;//print_r($row_commonshop_orders->batchcode_count);die();
}


function print_temp_orders($exp_print_temp_id,$ord_customer_id){
	$result = array();
	//$sql_commonshop_orders = "SELECT distinct(ord.batchcode) AS batchcode FROM weixin_expresses_company as exp INNER JOIN weixin_commonshop_orders as ord ON ord.express_id=exp.id WHERE exp.print_temp_id=".$exp_print_temp_id." AND exp.customer_id=".$ord_customer_id." AND ord.sendstatus=0 AND (ord.paystatus=1 OR ord.paystyle='货到付款') AND ord.isvalid=1 AND ord.supply_id<0 AND ord.sendway=0";
        //$sql_commonshop_orders = "SELECT distinct(ord.batchcode) AS batchcode FROM weixin_expresses_company as exp INNER JOIN weixin_commonshop_orders as ord ON ord.send_express_id=exp.id WHERE exp.print_temp_id=".$exp_print_temp_id." AND exp.customer_id=".$ord_customer_id." AND ord.sendstatus=true AND ord.isvalid=1 AND ord.supply_id<0";
        $sql_commonshop_orders = "SELECT distinct(ord.batchcode) AS batchcode FROM weixin_expresses_company as exp INNER JOIN weixin_commonshop_orders as ord ON ord.send_express_id=exp.id WHERE exp.print_temp_id=".$exp_print_temp_id." AND exp.customer_id=".$ord_customer_id." AND ord.isvalid=1 AND ord.supply_id<0 AND ord.sendway=0";

	$obj_commonshop_orders = _mysql_query($sql_commonshop_orders) or die('Query_is_remind2 failed: ' . mysql_error());	
	while($row_commonshop_orders = mysql_fetch_array($obj_commonshop_orders,MYSQL_ASSOC)){
		$result[] = $row_commonshop_orders;
	}
	//print_r($result);die();
	return $result;
}


?>