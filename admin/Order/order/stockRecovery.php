<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');   //配置
require('../../../../weixinpl/customer_id_decrypt.php');   //解密参数
$link = mysql_connect(DB_HOST, DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$stringtime = date("Y-m-d H:i:s", time());//当前时间
$op = $configutil->splash_new($_POST["op"]);
$num = 0;
if( $op == "count" ){
	$sql ="select count(1) as num from stockrecovery_t where customer_id=".$customer_id." and UNIX_TIMESTAMP(recovery_time)<".strtotime($stringtime);
	$result = _mysql_query($sql) or die("stockrecovery Query error : ".mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$num = $row->num;
	}
	$json["status"] = 1;
	$json["num"] 	= $num;
	$jsons=json_encode($json);
	die($jsons);	
}

$del_id = -1;//要删除的id
$pid 	= -1;//商品id
$pos_id = -1;//属性id
$stock 	= 0;//要加的库存
$sql = "select id,pid,pos_id,stock from stockrecovery_t where customer_id=".$customer_id." and UNIX_TIMESTAMP(recovery_time)<".strtotime($stringtime)." and is_collageActivities=0 limit 0,20";
$result = _mysql_query($sql) or die("stockrecovery Query error : ".mysql_error());
while ($row = mysql_fetch_object($result)) {
	$del_id = $row->id ;
	$pid 	= $row->pid ;
	$pos_id = $row->pos_id ;
	$stock 	= $row->stock ;
	if( $pos_id > 0 ){
		$query_num_up="update weixin_commonshop_product_prices set storenum = storenum+".$stock." where product_id=".$pid." and id='".$pos_id."'";		
	}else{
		$query_num_up="update weixin_commonshop_products set storenum= storenum+".$stock." where id=".$pid;
	}
	_mysql_query($query_num_up) or die('Query_product_prices failed: ' . mysql_error()); 
	$query = "delete from stockrecovery_t where id =".$del_id;
	_mysql_query($query) or die('delete failed: ' . mysql_error()); 
}

//处理订货系统订单库存   2017-11-18 加 lj
$query_time = "select batchcode,recovery_time,is_sendorder,user_id,o_shop_id,or_shop_type,or_code from weixin_commonshop_order_prices where isvalid=true and o_shop_id>0 and status = 0 and recovery_time<'".$stringtime."' AND paystatus = 0";
$result_time = _mysql_query($query_time) or die('Query_time failed:'.mysql_error());
while($row_time = mysql_fetch_object($result_time)){
    $batchcode     = $row_time->batchcode;
    $recovery_time  = $row_time->recovery_time;
    $o_shop_id 	   = $row_time->o_shop_id;
    $or_shop_type   = $row_time->or_shop_type;
    $user_id        = $row_time->user_id;
    $or_code        = $row_time->or_code;
    if ($or_code){  //物码解除冻结状态
        $sql = "UPDATE ".WSY_DH.".orderingretail_code SET is_freeze = 0 WHERE code = '{$or_code}'";
        _mysql_query($sql);
    }
    //回收库存
    if($o_shop_id>0 && $or_shop_type>0){
        $url = Protocol . "" . $_SERVER['HTTP_HOST'] . "/addons/index.php/ordering_retail/Ordering_Service/recover_product_store?customer_id=" . $customer_id . "&user_id=" . $user_id . "&batchcode=" . $batchcode."&o_shop_id=".$o_shop_id."&or_shop_type=".$or_shop_type;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if (Protocol == "https://") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $output = curl_exec($ch);
    }
    //把订单更新为已失效
    if(!empty($output)){
        $update_order = "update weixin_commonshop_orders set status=-1 where batchcode='$batchcode'";
        _mysql_query($update_order);
        $update_order_prices = "update weixin_commonshop_order_prices set status=-1 where batchcode='$batchcode'";
        _mysql_query($update_order_prices);
    }
}

$error =mysql_error();
if(!empty($error)){
	$json["status"] = 10002;
	$json["msg"] = $error;	
}else{
	$json["status"] = 1;
}

mysql_close($link);


$jsons=json_encode($json);
die($jsons);	

?>