<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');   //配置
require('../../../../weixinpl/customer_id_decrypt.php');   //解密参数

$op =$configutil->splash_new($_POST["op"]);
if(!empty($_POST["batchcode"])){
   $batchcode = $configutil->splash_new($_POST["batchcode"]);
   
   if(!isset($customer_id)){
		$json["status"] = 10001;
		$json["line"] = 15;
		$json["msg"] = "登录超时，请重新登录！$customer_id";
		$jsons=json_encode($json);
		die($jsons);	
	}

	if(!is_numeric($batchcode)){
		$json["status"] = 10002;
		$json["line"] = 24;
		$json["msg"] = "订单号不正确！";
		$jsons=json_encode($json);
		die($jsons);		
	}
}

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");


require('../../../../weixinpl/proxy_info.php');       //OEM域名
require('../../../../weixinpl/common/utility.php');
require('../../../../weixinpl/common/utility_shop.php');  //商城方法
$shopmessage= new shopMessage_Utlity(); 


switch($op){
	case "pay": 
		$num = 0;
		$sql = "select count(1) as num from weixin_commonshop_order_promoters where batchcode='".$batchcode."'";
		$result = _mysql_query($sql) or die("Query_stat error : ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$num 	= $row->num;
		}
		if( $num > 0 ){
			$json["status"] = 0;		
			$json["msg"] = "编号：".$batchcode."，已有奖励记录";
			$jsons=json_encode($json);
			die($jsons);				
		}
		$query_pay="update weixin_commonshop_orders set paystatus=0 where isvalid=true and customer_id= ".$customer_id." and batchcode='".$batchcode."'";
		_mysql_query($query_pay)or die("query_pay error : ".mysql_error());

		//查询订单支付状态
		$orgin_paystatus	= -1;	//支付状态
		$sendstyle			= "";
		$paystyle 			= "";
		$user_id 			= -1;		
		$pay_batchcode		= -1;		
		$supply_id 			= -1;		
		$exp_user_id 		= -1;		 //订单上级
		$query_stat = "select paystatus,pay_batchcode,sendstyle,user_id,paystyle,exp_user_id,supply_id,card_member_id from weixin_commonshop_orders where  isvalid=true and customer_id= ".$customer_id." and batchcode='".$batchcode."'";
		$result_stat = _mysql_query($query_stat) or die("Query_stat error : ".mysql_error());
		while ($row_stat = mysql_fetch_object($result_stat)) {
			$orgin_paystatus 	= $row_stat->paystatus;
			$sendstyle 			= $row_stat->sendstyle;
			$paystyle 			= $row_stat->paystyle;
			$user_id 			= $row_stat->user_id;
			$exp_user_id 		= $row_stat->exp_user_id;
			$card_member_id		= $row_stat->card_member_id;
			$supply_id 			= $row_stat->supply_id;
			$supply_id 			= $row_stat->supply_id;
			$pay_batchcode 			= $row_stat->pay_batchcode;
		}
		$payCurrency = 0;
		$sql = "select currency from order_currencyandcoupon_t where pay_batchcode='".$pay_batchcode."'";
		$result = _mysql_query($sql) or die("Query_stat error : ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$payCurrency = $row->currency;
		}

		if($orgin_paystatus == 0){
			//查询此订单返佣总金额
			$reward_money		= 0;
			$needScore			= 0;
			$reward_currency	= 0;
			$query_reward="select reward_money,needScore,currency from weixin_commonshop_order_prices where isvalid=true and batchcode='".$batchcode."' limit 0,1";	
			$result_reward = _mysql_query($query_reward) or die('W376 Query failed: ' . mysql_error());
			while ($row_r = mysql_fetch_object($result_reward)) {
				$reward_money		= $row_r->reward_money;
				$needScore			= $row_r->needScore;
				$reward_currency	= $row_r->currency;
			}
			
			//更新支付状态
			$query_pay="update weixin_commonshop_orders set paystatus=1 where isvalid=true and customer_id= ".$customer_id." and batchcode='".$batchcode."'";
			_mysql_query($query_pay);
			
			$sql = "update weixin_commonshop_order_prices set Pay_Method=0,paystatus=1,paytime=now() where isvalid=true and batchcode='" . $batchcode . "'";
			_mysql_query($sql) or die('8_1 Query failed: ' . mysql_error());
			
			/* 商城设置 推广员发展模式 */
			$issell = false;
			$reward_type = 1;
			$init_reward= 1;
			$is_autoupgrade=0;
			$auto_upgrade_money_2 = 0;			
			$query_shop = "select is_autoupgrade,auto_upgrade_money_2,reward_type,issell,init_reward from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
			$result_shop = _mysql_query($query_shop) or die('Query_shop failed: ' . mysql_error());		   
			while ($row_shop = mysql_fetch_object($result_shop)) {
				$issell = $row_shop->issell;
				$reward_type = $row_shop->reward_type;
				$init_reward = $row_shop->init_reward;
				$is_autoupgrade = $row_shop->is_autoupgrade;
				$auto_upgrade_money_2 = $row_shop->auto_upgrade_money_2;
			}
			$shopmessage->GetMoney_Common($batchcode,$customer_id,$reward_money,$user_id,$exp_user_id,0,-1,$needScore,$card_member_id,$reward_currency,$payCurrency); 
			$json["status"] = 1;		
			$json["supply"] = $supply_id;		
			$json["msg"] = "编号：".$batchcode."，确认支付成功";				
		}else{
			$json["status"] = 0;		
			$json["msg"] = "编号：".$batchcode."，已确认支付，请勿重复";						
		}
	break;
	case "list": 
		$array = array();
		$sql=" select o.batchcode from weixin_commonshop_orders o left join weixin_commonshop_order_promoters
 p on o.batchcode=p.batchcode  where p.batchcode is null and  o.isvalid=true and o.customer_id=".$customer_id." order by o.id desc";
		$result = _mysql_query($sql) or die('Query_shop failed: ' . mysql_error());		   
		while ($row = mysql_fetch_object($result)) {
			$batchcode	= $row->batchcode;
			array_push($array,$batchcode);
		}
		$json["status"] = 1;		
		$json["msg"] 	= $array;		
	break;
}




$error =mysql_error();
if(!empty($error)){
	$json["status"] = 10002;
	$json["msg"] = $error;	
}

/* if($link){mysql_close($link);}  */ 
mysql_close($link);

$jsons=json_encode($json);
die($jsons);	

?>