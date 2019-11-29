<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$shop_id=-1;
$wce_id=-1;
$shop_id =$configutil->splash_new($_POST["shop_id"]);//没支付订单失效时间
$is_orderActivist 	= $configutil->splash_new($_POST["is_orderActivist"]);//没支付订单失效时间
$is_receipt         = $configutil->splash_new($_POST["is_receipt"]);//没支付订单失效时间
$is_deliverySettlement 		= $configutil->splash_new($_POST["is_deliverySettlement"]);//虚拟发货自动收货结算
/*陈仕煌*/
$is_order_delay_check_auto = $configutil->splash_new($_POST["is_order_delay_check_auto"]);//延迟收货自动审核
$order_delay_time = $configutil->splash_new($_POST["order_delay_time"]);//自动延迟收货时间
if($order_delay_time<0||!is_numeric($order_delay_time))
{
    $order_delay_time = 0;
}
/*陈仕煌*/

$is_auto_aftersale = (int)$configutil->splash_new($_POST["is_auto_aftersale"]);//售后订单申请自动同意开关
$auto_aftersale_time = (int)$configutil->splash_new($_POST["auto_aftersale_time"]);//售后订单申请自动同意时间(天)
$is_auto_safeguard = (int)$configutil->splash_new($_POST["is_auto_safeguard"]);//维权订单申请自动同意开关
$auto_safeguard_time = (int)$configutil->splash_new($_POST["auto_safeguard_time"]);//售后订单申请自动同意时间(天)
$is_auto_end = (int)$configutil->splash_new($_POST["is_auto_end"]);//订单流程自动恢复开关
$auto_end_time = (int)$configutil->splash_new($_POST["auto_end_time"]);//订单流程自动恢复时间(天)

/*陶晋*/
$is_blessing  = $configutil->splash_new($_POST["is_blessing"]);//祝福语开关 0-关闭，1-开启
if(!$is_blessing) $is_blessing = 0;
/*陶晋*/
//echo $is_orderActivist."===".$is_receipt;die;
/*郑培强*/
$productend_tips    = $configutil->splash_new($_POST["productend_tips"]);//产品到期前多少天，该产品会被提示为即将到期
if($productend_tips == '')
{
	$productend_tips = 0;
}
/*郑培强*/
/*weixin_commonshops_extend是否存在记录start*/

$query = "select id from weixin_commonshops_extend where isvalid=true and shop_id=".$shop_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$wce_id = $row->id;
}

/*weixin_commonshops_extend是否存在记录end*/
if($shop_id>0){
	if($wce_id>0){
		$query = "UPDATE weixin_commonshops_extend set is_orderActivist=".$is_orderActivist.",is_receipt=".$is_receipt.",is_deliverySettlement=".$is_deliverySettlement.",productend_tips=".$productend_tips.",is_order_delay_check_auto=b'".$is_order_delay_check_auto."',order_delay_time=".$order_delay_time.",is_auto_aftersale=b'{$is_auto_aftersale}',is_auto_safeguard=b'{$is_auto_safeguard}',is_auto_end=b'{$is_auto_end}',auto_aftersale_time='{$auto_aftersale_time}',auto_safeguard_time='{$auto_safeguard_time}',auto_end_time='{$auto_end_time}',is_blessing = {$is_blessing} where isvalid=true and shop_id=".$shop_id." and customer_id=".$customer_id;
		// echo $query;die;
	}else{
		$query = "insert into weixin_commonshops_extend(shop_id,createtime,isvalid,customer_id,is_Pinformation,is_stockOut,is_division,is_promoter,recovery_time,is_orderActivist,is_receipt,is_deliverySettlement,is_order_delay_check_auto,order_delay_time,is_blessing,is_auto_aftersale,is_auto_safeguard,auto_aftersale_time,auto_safeguard_time,is_auto_end,auto_end_time) values(".$shop_id.",now(),true,".$customer_id.",0,0,0,0,".$is_orderActivist.",".$is_receipt.",".$is_deliverySettlement.',b"'.$is_order_delay_check_auto.'",'.$order_delay_time.','.$is_blessing.",'{$is_auto_aftersale}','{$is_auto_safeguard}','{$auto_aftersale_time}','{$auto_safeguard_time}','{$is_auto_end}','{$auto_end_time}')";
	}	
}

$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$error2 =mysql_error();
mysql_close($link);
echo "<script>location.href='order_base.php?customer_id=".$customer_id_en."';</script>"
?>