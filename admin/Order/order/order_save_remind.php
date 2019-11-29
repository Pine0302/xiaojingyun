<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php');   //解密参数
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
$database = new \Key\DB();
$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);
// $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
// mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require_once PATH_REDIS_CLIENT;
ini_set('default_socket_timeout', -1);

require('../../../../weixinpl/common/utility_shop.php');
require('../../../../weixinpl/common/newredis.class.php');

$callback = $configutil->splash_new($_GET["callback"]);


$op =$configutil->splash_new($_GET["op"]);
//1:是更新order_remind，2:是查order_remind的状态，3:查是否有新订单吧(商城+线下商城) 4:查是否有新订单吧(商城)






if($op==1){

	$keyid=-1;
	$query="select id from weixin_commonshop_orderremind where isvalid=true and customer_id=".$customer_id;
	/*$result = _mysql_query($query) or die('Query failed1_weixin_commonshop_orderremind: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$keyid = $row->id;
	}*/

	$result = redis_select($query);
	$keyid = $result[0]['id'];


	if(empty($keyid) || $keyid<0){
		$query="insert into weixin_commonshop_orderremind (customer_id,order_remind,isvalid,order_count,last_record) values(".$customer_id.",0,true,0,0)";
		$result = _mysql_query($query) or die('Query failed2_weixin_commonshop_orderremind: ' . mysql_error());
	}
	$ordercount=-1;
	//$sql_ordercount="select count(1) as ordercount from weixin_commonshop_orders where customer_id=".$customer_id." and isvalid=true and paystatus=1";
	/* $re=_mysql_query($sql_ordercount) or die('Query sql_ordercount: '.mysql_error());
	while ($ro = mysql_fetch_object($re)) {
		$ordercount= $ro->ordercount;
	} */
	//$result = redis_select($sql_ordercount);
	//$ordercount = $result[0]['ordercount'];

	if($ordercount>0){
		$query="update weixin_commonshop_orderremind set order_count=".$ordercount.",last_record=".$ordercount." where isvalid=true and customer_id=".$customer_id;
		$result = _mysql_query($query) or die('Query failed3_weixin_commonshop_orderremind: ' . mysql_error());
	}

	$order_remind =$configutil->splash_new($_GET["order_remind"]);
	$query_orderremind = "update weixin_commonshop_orderremind set order_remind=".$order_remind." where customer_id=".$customer_id;
	$result = _mysql_query($query_orderremind) or die('Query failed_orderremind: ' . mysql_error());

	$error =mysql_error();
	if($order_remind==1){
		echo $callback."([{status:1}";
	}else{
		echo $callback."([{status:0}";
	}

	echo "]);";
	echo $callback;

}else if($op==2){

	$order_remind=-1; //提醒开关
	$query="select order_remind from weixin_commonshop_orderremind where isvalid=true and customer_id=".$customer_id." limit 1";
	$result = _mysql_query($query) or die('Query failed2: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$order_remind = $row->order_remind;
	}
	$result = redis_select($query);
	$order_remind = $result[0]['order_remind'];

	if( $order_remind==0 ){
		$query="select order_remind from weixin_cityarea_orderremind where isvalid=true and types in(120,121,122,123) and customer_id=".$customer_id;
		/*$result = _mysql_query($query) or die('Query failed4: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$order_remind = $row->order_remind;
			if ( $order_remind==1 ) {
				break;
			}
		}*/
		$result = redis_select($query);
		foreach($result as $value){
			$order_remind = $value['order_remind'];
			if ( $order_remind==1 ) {
				break;
			}
		}

	}

	$error =mysql_error();
	if($order_remind>0){
		echo $callback."([{status:1}";
		echo "]);";
		echo $callback;
	}else{
		echo $callback."([{status:0}";
		echo "]);";
		echo $callback;
	}
}else if($op==3){

	// 商城
	//因为查询时有isvalid=true条件所以，删除的查不出来，导致 weixin_commonshop_orders 和 weixin_commonshop_orderremind两张表不匹配，把isvalid=true屏蔽了
	$query="select count(1) as ordercount from ".DB_NAME.".weixin_commonshop_orders where customer_id=".$customer_id." and paystatus=1";//订单前加主库名DB_NAME  用作redis_select()时区分数据库 2018/5/4
	/*$result=_mysql_query($query) or die('Query failed3: '.mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$ordercount= $row->ordercount;
	}*/

	
	$result = redis_select($query);

	$ordercount = $result[0]['ordercount'];
	if(!empty($_GET['update'])){
		$query="update weixin_commonshop_orderremind set order_count=".$ordercount.",last_record=".$ordercount." where isvalid=true and customer_id=".$customer_id;
		$result = _mysql_query($query) or die('Query failed3_weixin_commonshop_orderremind1: ' . mysql_error());
	}
	$query="select last_record,order_remind from weixin_commonshop_orderremind where isvalid=true and customer_id=".$customer_id;
	$result = _mysql_query($query) or die('Query failed4: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$last_record = $row->last_record;
		$order_remind = $row->order_remind;
		$statuss[] = $order_remind;
	}
	$count=$ordercount-$last_record;
	if($count < 0){
		$count=0;
	}

	

	// 线下商城
	// 20.线下商城-自提订单 21.线下商城-当面付 22.线下商城-配送订单 23.线下商城-社区订单
	$types = array('20','21','22','23');
	$texts = array('take','face','distribution','community');

	// 获取开关信息
	$query="select last_record,types,order_remind from weixin_cityarea_orderremind where isvalid=true and types in(120,121,122,123) and customer_id=".$customer_id;
	$switch = $database->getData($query);
	foreach ($switch as $key => $value) {
		$switchs[$value['types']]['last_record'] = $value['last_record'];
		$switchs[$value['types']]['order_remind'] = $value['order_remind'];
	}
	// var_dump($switchs);exit;
	// 获取当前订单数
	$query="select count(1) as ordercount,types from weixin_cityarea_orders where customer_id=".$customer_id." and isvalid=true and pay_status=1 and types in(20,21,22,23) GROUP BY types";
	$ordercounts = $database->getData($query);

	foreach ($ordercounts as $key => $value) {
		$shop_order_count = $value['ordercount'];
		if(!empty($_GET['update'])){
			$query="update weixin_cityarea_orderremind set order_count='".$shop_order_count."',last_record='".$shop_order_count."' where isvalid=true and types=1{$value['types']} and customer_id=".$customer_id;
			$result = _mysql_query($query) or die('Query failed3_weixin_cityarea_orderremind1: ' . mysql_error());
		}
		$type = '1'.$value['types'];
		$shop_last_record = $switchs[$type]['last_record'];
		$shop_count=$shop_order_count-$shop_last_record;
		if($shop_count < 0){
			$shop_count=0;
		}
		$switchs[$type]['order_remind'] = (int)$switchs[$type]['order_remind'];
		// $cityarea_shop_switch .= ",{$texts[$key]}_switch:{$switchs[$type]['order_remind']}";
		// $cityarea_shop_count .= ",{$texts[$key]}_order:{$shop_count}";
		if($value['types']==20){
			$key = 0;
		}elseif($value['types']==21){
			$key = 1;
		}elseif($value['types']==22){
			$key = 2;
		}elseif($value['types']==23){
			$key = 3;
		}
        $cityarea_shop_count_switch .= ",{$texts[$key]}_order:{$shop_count},{$texts[$key]}_switch:{$switchs[$type]['order_remind']}";
		$statuss[] = $switchs[$type]['order_remind'];
	}

	$status = in_array(1,$statuss)==false?0:1;
	$order_remind = $order_remind ? $order_remind : 0; 

	// echo $callback."([{status:{$status},shop_order:{$count}{$cityarea_shop_count},shop_switch:{$order_remind}{$cityarea_shop_switch} }";
	echo $callback."([{status:{$status},shop_order:{$count},shop_switch:{$order_remind}{$cityarea_shop_count_switch}}";
	echo "]);";
	echo $callback;

}else if($op==4){
	$query="select count(1) as ordercount from weixin_commonshop_orders where customer_id=".$customer_id." and isvalid=true and paystatus=1";
	// $result=_mysql_query($query) or die('Query failed3: '.mysql_error());
	// while ($row = mysql_fetch_object($result)) {
	// 	$ordercount= $row->ordercount;
	// }
	$result = redis_select($query);
	$ordercount = $result[0]['ordercount'];    

	if(!empty($_GET['update'])){
		$query="update weixin_commonshop_orderremind set order_count=".$ordercount.",last_record=".$ordercount." where isvalid=true and customer_id=".$customer_id;
		$result = _mysql_query($query) or die('Query failed3_weixin_commonshop_orderremind1: ' . mysql_error());
	}
	$query="select last_record from weixin_commonshop_orderremind where isvalid=true and customer_id=".$customer_id;
	/*$result = _mysql_query($query) or die('Query failed4: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$last_record = $row->last_record;
	}*/
	$result = redis_select($query);
	$last_record = $result[0]['last_record'];
	$count=$ordercount-$last_record;
	if($count < 0){
		$count=0;
	}
	echo $callback."([{status:1,count:".$count."}";
	echo "]);";
	echo $callback;
}




mysql_close($link);



//redis查询
function redis_select($query){
	$redis = RedisClient::getInstance(include PATH_REDIS_CONFIG);
	// $redis = new NewRedis(0);
	if(empty($query)){
		die(array('errcode'=>400,'msg'=>'query为空'));
	}
	$key = md5($query);
	$result = $redis->get($key);

	//$redis->setEXPIRE($key, 3);
	$result = redis_object_to_array(unserialize($result));
	if(empty($result)){
		$res = _mysql_query($query) or die('Query redis_select: ' . mysql_error());
		while ($row = mysql_fetch_object($res)) {
			$new_result[] = $row;
		}
		$redis->set(md5($query),serialize($new_result),180);
		//$redis->setEXPIRE($key, 60);
		$result = redis_object_to_array($new_result);
	}
	return $result;

}

// 对象转数组
function redis_object_to_array($d) {
    if (is_object($d)) {
        $d = get_object_vars($d); //将第一层对象转换为数组
    }

    if (is_array($d)) {
        return array_map(__FUNCTION__, $d); //如果是数组使用array_map递归调用自身处理数组元素
    } else {
        return $d;
    }
}

?>