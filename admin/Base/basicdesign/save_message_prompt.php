<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
require('../../../../weixinpl/function_model/public_function.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$shop_id					= -1;
$wce_id						= -1;
$is_qrMessage				= -1;
$is_memberBuyMessage		= -1;
$is_buyContentMessage		= -1;
$is_orderCommissionMessage	= -1;
$is_commission_message      = -1;
$is_commission_scope        = -1;
$is_openOrderMessage		= -1;
$shop_id 					= $configutil->splash_new($_POST["shop_id"]);//商城id
$is_qrMessage 				= $configutil->splash_new($_POST["is_qrMessage"]);//分享或者扫二维码提示消息开关
$is_memberBuyMessage 		= $configutil->splash_new($_POST["is_memberBuyMessage"]);//下级会员购物消息开关
$is_buyContentMessage 		= $configutil->splash_new($_POST["is_buyContentMessage"]);//下级会员购物消息消息（关闭购物内容）开关
$is_commission_message 		= $configutil->splash_new($_POST["is_commission_message"]);//佣金消息提示开关，0关，1开
$is_commission_scope 		= $configutil->splash_new($_POST["is_commission_scope"]);//佣金消息提示范围，0所有人，1推广员提示
$is_openOrderMessage 		= $configutil->splash_new($_POST["is_openOrderMessage"]);//商城显示下单提示开关，0关，1开

/*weixin_commonshops_extend是否存在记录start*/
$query = "select id from weixin_commonshops_extend where isvalid=true and shop_id=".$shop_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$wce_id = $row->id;
}
/*weixin_commonshops_extend是否存在记录end*/
if($shop_id>0){
	if($wce_id>0){
		$query = "update weixin_commonshops_extend set is_openOrderMessage=".$is_openOrderMessage.",is_qrMessage=".$is_qrMessage.",is_memberBuyMessage=".$is_memberBuyMessage.",is_buyContentMessage=".$is_buyContentMessage.",is_commission_message=".$is_commission_message.",is_commission_scope=".$is_commission_scope." where isvalid=true and shop_id=".$shop_id." and customer_id=".$customer_id;
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	}else{

		$data = array(

			'shop_id'=>$shop_id,
			'isvalid'=>1,
			'createtime'=>'now()',
			'customer_id'=>$customer_id,
			'is_qrMessage'=>$is_qrMessage,
			'is_buyContentMessage'=>$is_buyContentMessage,
			'is_commission_scope'=>$is_commission_scope,
			'is_openOrderMessage'=>$is_openOrderMessage

		);

		$table = 'weixin_commonshops_extend';
		insert($data,$table);

		//$query = "insert into weixin_commonshops_extend(shop_id,createtime,isvalid,customer_id,is_Pinformation,is_stockOut,is_division,is_promoter,is_qrMessage,is_memberBuyMessage,is_buyContentMessage,is_commission_message,is_commission_scope) values(".$shop_id.",now(),true,".$customer_id.",0,0,0,0,".$is_qrMessage.",".$is_buyContentMessage.",".$is_commission_message.",".$is_commission_scope.")";


	}
	
}

$error =mysql_error();
mysql_close($link);
echo "<script>location.href='message_prompt.php?customer_id=".$customer_id_en."';</script>"
?>