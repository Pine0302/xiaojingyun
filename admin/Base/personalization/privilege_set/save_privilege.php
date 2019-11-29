<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
/* 参数获取 */
$shop_id = $configutil->splash_new($_POST["shop_id"]);

$upgrade_mode = 0;
if(!empty($_POST["upgrade_mode"])){//产品列表模板选择
   $upgrade_mode = $configutil->splash_new($_POST["upgrade_mode"]);	
}

/*weixin_commonshops_extend是否存在记录start*/
$wce_id = -1;
$query = "select id from weixin_commonshops_extend where isvalid=true and shop_id=".$shop_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$wce_id = $row->id;
}
/*weixin_commonshops_extend是否存在记录end*/
		
 if($shop_id>0){
	 if($wce_id>0){
		$sql="update weixin_commonshops_extend set upgrade_mode=".$upgrade_mode." where customer_id=".$customer_id." and isvalid=true and shop_id=".$shop_id;	
	 }else{
		$sql="insert into weixin_commonshops_extend(shop_id,createtime,isvalid,customer_id,is_Pinformation,is_stockOut,is_division,is_promoter,upgrade_mode) values(".$shop_id.",now(),true,".$customer_id.",0,0,0,0,".$upgrade_mode.")";
	 }
	
	_mysql_query($sql)or die(' Query failed1: ' . mysql_error()); 
	
 }

$error = mysql_error();	
mysql_close($link);
echo $error; 
echo "<script>location.href='privilege_set.php?customer_id=".$customer_id_en."';</script>"
?>