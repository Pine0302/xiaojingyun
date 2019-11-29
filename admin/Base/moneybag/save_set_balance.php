<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/common/utility_shop.php');
require('../../../../weixinpl/proxy_info.php');
require('../../../../weixinpl/auth_user.php');
_mysql_query("SET NAMES UTF8");
$shopmessage= new shopMessage_Utlity(); 



$user_id	 = 0;
$balance 	 = 0;
$remark 	 = '';
$real_name 	 = '';
$customer_id = passport_decrypt($customer_id);
$user_id 	 = $configutil->splash_new($_GET["user_id"]);
$balance 	 = $configutil->splash_new($_POST["balance"]);
$real_name 	 = $configutil->splash_new($_POST["real_name"]);
$remark 	 = $configutil->splash_new($_POST["remark"]);
$batchcode   = $user_id.time();
$id=0;

$sql = "SELECT count(id) as id,balance FROM moneybag_t where user_id=".$user_id." AND customer_id=".$customer_id." limit 1";
$res = _mysql_query($sql)or die( 'Query failed in 22: ' . mysql_error() );

while($row=mysql_fetch_object($res)){
	$id = $row->id;
	$before_balance = $row->balance;

	// if($balance<0 && abs($balance)> $before_balance ){
		
	// }

	$after_balance  = $before_balance+$balance;
	if($remark == ''){
		if( $balance > 0 ){			
			$remark = "后台充值 ".$balance." 零钱 | 零钱订单号：".$batchcode;			
		}elseif( $balance < 0 ){		
			$remark = "商家后台扣取您 ".abs($balance)." 零钱";			
		}
		
	}

	if( $balance > 0 ){			
		$msg_content =  "亲,您的零钱增加了".$balance."元\r\n".
							"来源【后台充值】\r\n".	
							"时间：".date( "Y-m-d H:i:s")."";
	}elseif( $balance < 0 ){		
		$msg_content =  "亲,您的零钱扣取了".abs($balance)."元\r\n".
						"来源【后台扣取】\r\n".	
						"时间：".date( "Y-m-d H:i:s")."";
	}
    //插入操作日志
    require_once(ROOT_DIR.'/wsy_pub/admin/model/sys_plat_log.php');
    $sys_plat_log = new \model_sys_plat_log($customer_id);
    $sys_plat_log->add_log('shop_system_moneybag_background_recharge','用户ID'.$user_id.',订单号'.$batchcode.'，后台充值'.$balance.'；');

}
if( $balance > 0 ){
	$type = 0;
}elseif( $balance < 0 ){
	$type = 1;
}
$operation_user = '';	//操作人
if ( $_SESSION['curr_login'] != '' ){
	$operation_user = $_SESSION['curr_login'];
}



$shopmessage->PayToMoneybag($customer_id,$user_id,$balance,$type,$batchcode,4,$remark,$operation_user);

	/* if($id == 0){

		$query = "INSERT INTO moneybag_t(isvalid,customer_id,user_id,balance,createtime,real_name) VALUES(true,".$customer_id.",".$user_id.",".$balance.",now(),'".$real_name."')";
		_mysql_query($query)or die( 'Query failed in 28: ' . mysql_error() );
		
		$sql = "INSERT INTO moneybag_log(isvalid,customer_id,user_id,money,batchcode,remark,createtime,type,pay_style)
		VALUES(true,".$customer_id.",".$user_id.",".abs($balance).",".$batchcode.",'".$remark."',now(),$type,4)";

		_mysql_query($sql)or die( 'Query failed in 34: ' . mysql_error() );
	}else{

		$query = "UPDATE moneybag_t SET balance=balance+".$balance.",createtime=now() WHERE user_id=".$user_id." AND customer_id=".$customer_id." ";
		_mysql_query($query)or die( 'Query failed in 40: ' . mysql_error() );

		$sql = "INSERT INTO moneybag_log(isvalid,customer_id,user_id,money,batchcode,remark,createtime,type,pay_style)
		VALUES(true,".$customer_id.",".$user_id.",".abs($balance).",".$batchcode.",'".$remark."',now(),$type,4)";
		_mysql_query($sql)or die( 'Query failed in 44: ' . mysql_error() );
		
	} */

$shopmessage->SendMessage($msg_content,$weixin_fromuser,$customer_id);

echo '<script>history.go(-2);</script>';


//echo $day; 

?>