<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
require('../../../../weixinpl/function_model/public_function.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$shop_id=-1;
$wce_id=-1;
$shop_id =$configutil->splash_new($_POST["shop_id"]);//商城id
$pro_card_level =$configutil->splash_new($_POST["pro_card_level"]);//购买产品需要会员卡级别限制
$is_godefault =$configutil->splash_new($_POST["is_godefault"]);//先进单，再购买
$sell_discount =$configutil->splash_new($_POST["sell_discount"]);//购买折扣率
$auto_cus_time =$configutil->splash_new($_POST["auto_cus_time"]);//自动确定收货
$need_customermessage =$configutil->splash_new($_POST["need_customermessage"]);//顾客是否短信通知
$isprint =$configutil->splash_new($_POST["isprint"]);//是否开启小票打印
$is_ban_use_coupon_currency = $configutil->splash_new($_POST["is_ban_use_coupon_currency"]);//是否禁止同时使用购物币和优惠券
$is_identity =$configutil->splash_new($_POST["is_identity"]);//是否开启身份验证
$is_uploadidentity = $configutil->splash_new($_POST["is_uploadidentity"]);//是否开启身份附件上传
$per_identity_num =$configutil->splash_new($_POST["per_identity_num"]);//每个身份证号每天可下单数量
$is_cost_limit =$configutil->splash_new($_POST["is_cost_limit"]);//是否开启购买金额设置
$per_cost_limit =$configutil->splash_new($_POST["per_cost_limit"]);//每人每天不高于的总额
$is_weight_limit =$configutil->splash_new($_POST["is_weight_limit"]);//是否开启购买重量限制 
$per_weight_limit =$configutil->splash_new($_POST["per_weight_limit"]);//每人每天不高于的KG 
$is_number_limit =$configutil->splash_new($_POST["is_number_limit"]);//是否开启购买数量限制 
$per_number_limit =$configutil->splash_new($_POST["per_number_limit"]);//是否开启数量限制 
$recovery_time =$configutil->splash_new($_POST["recovery_time"]);//没支付订单失效时间

/*weixin_commonshops_extend是否存在记录start*/
$query = "select id from weixin_commonshops_extend where isvalid=true and shop_id=".$shop_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$wce_id = $row->id;
}
/*weixin_commonshops_extend是否存在记录end*/
if($shop_id>0){
	$sql="update weixin_commonshops set pro_card_level=".$pro_card_level.",is_godefault=".$is_godefault.",auto_cus_time=".$auto_cus_time.",need_customermessage=".$need_customermessage.",isprint=".$isprint.",is_ban_use_coupon_currency=".$is_ban_use_coupon_currency.",is_identity=".$is_identity.",is_uploadidentity=".$is_uploadidentity.",per_identity_num=".$per_identity_num.",is_cost_limit=".$is_cost_limit.",per_cost_limit=".$per_cost_limit.",is_weight_limit=".$is_weight_limit.",per_weight_limit=".$per_weight_limit.",is_number_limit=".$is_number_limit.",per_number_limit=".$per_number_limit." where isvalid=true and id=".$shop_id." and customer_id=".$customer_id;
	if($wce_id>0){

		$query = "update weixin_commonshops_extend set recovery_time=".$recovery_time." where isvalid=true and shop_id=".$shop_id." and customer_id=".$customer_id;
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());

	}else{

		$data = array(

			'shop_id'=>$shop_id,
			'isvalid'=>1,
			'createtime'=>'now()',
			'customer_id'=>$customer_id,
			'recovery_time'=>$recovery_time
			
		);
		$table = 'weixin_commonshops_extend';
		insert($data,$table);

		//$query = "insert into weixin_commonshops_extend(shop_id,createtime,isvalid,customer_id,is_Pinformation,is_stockOut,is_division,is_promoter,recovery_time) values(".$shop_id.",now(),true,".$customer_id.",0,0,0,0,".$recovery_time.")";

	}
	
}/* else{
	$sql="insert into weixin_commonshops(customer_id,isvalid,createtime,pro_card_level,is_godefault,sell_discount,auto_cus_time,need_customermessage,isprint,is_identity,per_identity_num,is_cost_limit,per_cost_limit,is_weight_limit,per_weight_limit,is_number_limit,per_number_limit) values(".$customer_id.",true,now(),".$pro_card_level.",".$is_godefault.",".$sell_discount.",".$auto_cus_time.",".$need_customermessage.",".$isprint.",".$is_identity.",".$per_identity_num.",".$is_cost_limit.",".$per_cost_limit.",".$is_weight_limit.",".$per_weight_limit.",".$is_number_limit.",".$per_number_limit.")";
} */
$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());
$error1 =mysql_error();
//$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
//$error2 =mysql_error();
mysql_close($link);
echo "<script>location.href='shop_set.php?customer_id=".$customer_id_en."';</script>"
?>