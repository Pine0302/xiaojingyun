<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");


$user_id = $configutil->splash_new($_POST["user_id"]);
$brand_name = $configutil->splash_new($_POST["brand_name"]);//公司名
$brand_tel = $configutil->splash_new($_POST["brand_tel"]);//公司电话
$brand_address = $configutil->splash_new($_POST["brand_address"]);//公司地址
$brand_intro = $configutil->splash_new(nl2br($_POST["brand_intro"]));//公司简介
$brand_supply_name = $configutil->splash_new($_POST["brand_supply_name"]); //品牌供应商名称
$supply_id = $configutil->splash_new($_GET["supply_id"]); //品牌供应商ID
$is_kefu = $configutil->splash_new($_POST["is_kefu"]); //是否开启客服
$kefu_type = $configutil->splash_new($_POST["kefu_type"]); //客服类型
$supply_qq = $configutil->splash_new($_POST["supply_qq"]); //QQ
$siteid = $configutil->splash_new($_POST["siteid"]); //小能企业号
$xiaoneng = $configutil->splash_new($_POST["xiaoneng"]); //小能客服接待组
	//echo $siteid;return;
if($is_kefu!=1){
	$save_brand="update weixin_commonshop_brand_supplys set brand_name='".$brand_name."',brand_tel='".$brand_tel."',brand_address='".$brand_address."',brand_supply_name='".
	$brand_supply_name."',brand_intro='".$brand_intro."' where user_id='".$user_id."' and customer_id=".$customer_id." ";

	$result=_mysql_query($save_brand) or die('Query1 failed: ' . mysql_error());
}else{
	//echo 's='.$supply_id;return;
	if($supply_id>0){
			$save_brand="update weixin_commonshop_brand_supplys set brand_name='".$brand_name."',brand_tel='".$brand_tel."',brand_address='".$brand_address."',brand_supply_name='".
			$brand_supply_name."',brand_intro='".$brand_intro."' where user_id='".$user_id."' and customer_id=".$customer_id." ";
			$sql="update weixin_commonshop_supply_kefu set is_kefu='".$is_kefu."',kefu_type='".$kefu_type."',supply_qq='".$supply_qq."',siteid='".$siteid."',xiaoneng='".$xiaoneng."' where supply_id='".$supply_id."' and customer_id=".$customer_id." ";
			//echo $sql;return;
			$result=_mysql_query($save_brand) or die('Query3 failed: ' . mysql_error());
			$result2=_mysql_query($sql) or die('Query4 failed: ' . mysql_error());
	}
	else{
		if($kefu_type==1){
			$sql="insert into weixin_commonshop_supply_kefu(customer_id,supply_id,is_kefu,kefu_type,supply_qq,siteid,xiaoneng,isvalid,createtime)values(".$customer_id.",".$user_id.",".$is_kefu.",".$kefu_type.",".$supply_qq.",null,null,true,now())";	
			//echo $sql;return;
			$save_brand="update weixin_commonshop_brand_supplys set brand_name='".$brand_name."',brand_tel='".$brand_tel."',brand_address='".$brand_address."',brand_supply_name='".
			$brand_supply_name."',brand_intro='".$brand_intro."' where user_id='".$user_id."' and customer_id=".$customer_id." ";
			$result=_mysql_query($save_brand) or die('Query5 failed: ' . mysql_error());
			$result2=_mysql_query($sql) or die('Query6 failed: ' . mysql_error());

		}else{
				$sql="insert into weixin_commonshop_supply_kefu(customer_id,supply_id,is_kefu,kefu_type,supply_qq,siteid,xiaoneng,isvalid,createtime)values(".$customer_id.",".$user_id.",".$is_kefu.",".$kefu_type.",null,".$siteid.",".$xiaoneng.",true,now())";	
		echo $sql;return;
				$save_brand="update weixin_commonshop_brand_supplys set brand_name='".$brand_name."',brand_tel='".$brand_tel."',brand_address='".$brand_address."',brand_supply_name='".
				$brand_supply_name."',brand_intro='".$brand_intro."' where user_id='".$user_id."' and customer_id=".$customer_id." ";
				$result=_mysql_query($save_brand) or die('Query7 failed: ' . mysql_error());
				$result2=_mysql_query($sql) or die('Query8 failed: ' . mysql_error());
		}
}

}

mysql_close($link);
echo "<script>location.href='brand_supply.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
 
?>