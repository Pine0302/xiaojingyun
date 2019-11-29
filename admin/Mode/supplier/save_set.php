<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$commission = 0;
$limit_day = -1;
$limit_money = -1;
//$commission =$configutil->splash_new($_POST["commission"]);
//$commission = round($commission,2);
$isOpenSupply		= $configutil->splash_new($_POST["isOpenSupply"]);
$isOpenBrandSupply	= $configutil->splash_new($_POST["isOpenBrandSupply"]);//品牌供应商
$is_supplyset		= $configutil->splash_new($_POST["is_supplyset"]);
$deposit 			= $configutil->splash_new($_POST["deposit"]);
$deposit 			= round($deposit,2);
$supply_detail		= $configutil->splash_new($_POST["supply_detail"]);   
$brandsupply_detail	= $configutil->splash_new($_POST["brandsupply_detail"]);//品牌供应商说明
$limit_day 			= $configutil->splash_new($_POST["limit_day"]);  //限制时间
$limit_money 		= $configutil->splash_new($_POST["limit_money"]);  //限制金额
$is_open_suning 	= $configutil->splash_new($_POST["is_open_suning"]);  //苏宁对接开关

if(empty($is_open_suning)){
	$is_open_suning = 0;
}
if(empty($isOpenBrandSupply)){
	$isOpenBrandSupply = 0;
}
$is_supply_product_off_shelves = $configutil->splash_new($_POST["is_supply_product_off_shelves"]);  //合作商可以下架自己的产品

//$brand_adimg 		= $configutil->splash_new($_POST["brand_adimg"]);  //限制金额
$not_supply_tip 	= "对不起,你仍未成为合作商";
//$type_foreign_id=$configutil->splash_new($_POST["type_foreign_id"]);  //分类页品牌供应商广告图链接
//$type_detail_id=$configutil->splash_new($_POST["type_detail_id"]);  //分类页品牌供应商链接
$is_export_order=$configutil->splash_new($_POST["is_export_order"]);  //订单导出开关
$is_supplyData=$configutil->splash_new($_POST["is_supplyData"]);  //数据统计开关



if(!empty($_POST["not_supply_tip"])){
	$not_supply_tip = $configutil->splash_new($_POST["not_supply_tip"]);
}

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

if(!empty($_POST["op"])){
	if($_POST["op"] == "cha_sort"){
		$sort_id = $configutil->splash_new($_POST["so_id"]);
		$ch_sort = $configutil->splash_new($_POST["ch_sort"]);
		$query = "UPDATE weixin_commonshop_applysupplys SET asort_value = ".$ch_sort." WHERE id=".$sort_id;
		//echo $query;die;
		_mysql_query($query)or die('Query failed 34: ' . mysql_error());
		echo "ok";
		return false;
	}
	if($_POST["op"] == "cha_sort_b"){
		$sort_id = $configutil->splash_new($_POST["so_id"]);
		$ch_sort = $configutil->splash_new($_POST["ch_sort"]);
		$query = "UPDATE weixin_commonshop_brand_supplys SET asort_value = ".$ch_sort." WHERE user_id=".$sort_id;
		_mysql_query($query)or die('Query failed 34: ' . mysql_error());
		echo "ok";
		return false;
	}
}

/*
$type_adurl="#";
//广告图链接
if($type_foreign_id){ //创建连接
	if($type_foreign_id>0){
		$typestrarr= explode("_",$type_foreign_id);
		$type_foreign_id = $typestrarr[0];
		$linktype=$typestrarr[1];
		if($linktype==1){
			 if($type_detail_id>0){
				
				 $type_adurl="product_detail.php?customer_id=".$customer_id_en."&pid=".$type_detail_id;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_foreign_id;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$type_adurl="list.php?customer_id=".$customer_id_en."&tid=".$type_foreign_id;
			}
		}else if($linktype==5){
		   //供应商店铺
			$type_adurl="my_store/my_store.php?customer_id=".$customer_id_en."&supplier_id=".$type_foreign_id."";
			
		}
	}

}
*/




$query="select id from weixin_commonshop_supplys where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$supply_id=-1;
while ($row = mysql_fetch_object($result)) {
	$supply_id = $row->id;
}
if(!empty($deposit)){
	if($supply_id>0){
		
		$sql="update weixin_commonshop_supplys set deposit=".$deposit.",commission=".$commission.",limit_day=".$limit_day.",limit_money=".$limit_money.",supply_detail='".$supply_detail."',not_supply_tip='".$not_supply_tip."',brandsupply_detail='".$brandsupply_detail."',is_export_order=".$is_export_order.",is_supplyData=".$is_supplyData." where id=".$supply_id;
	
	}else{
		
		$sql = "insert into weixin_commonshop_supplys(customer_id,deposit,commission,supply_detail,isvalid,createtime,not_supply_tip,limit_day,limit_money,brandsupply_detail,is_export_order,is_supplyData) values (".$customer_id.",".$deposit.",".$commission.",'".$supply_detail."',true,now(),'".$not_supply_tip."',".$limit_day.",".$limit_money.",'".$brandsupply_detail."',".$is_export_order.",".$is_supplyData.")";
		// echo $sql."<br/>";
		
	}
	_mysql_query($sql);
}

if($is_supply_product_off_shelves == '')
{
	$is_supply_product_off_shelves = 0;
}


$supply_must = implode('_', $_POST['checkboxItem']);

$supply_must_e == '';


if ($isOpenSupply == 1) {
	$supply_must_e = " supply_must='".$supply_must."',";
}else{
	$supply_must_e = "supply_must='1_0_0_0_0_0',";
}
$sql="update weixin_commonshops set ".$supply_must_e."is_supplyset=".$is_supplyset.",isOpenSupply=".$isOpenSupply.",isOpenBrandSupply=".$isOpenBrandSupply.",is_supply_product_off_shelves={$is_supply_product_off_shelves} where customer_id=".$customer_id;
_mysql_query($sql);
$query="select id from ".WSY_SHOP.".suning_setting where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $suning_id 	   = $row->id;
}
if($suning_id>0){	
	$sql="update ".WSY_SHOP.".suning_setting set is_open_suning=".$is_open_suning." where id=".$suning_id;
}else{
	$sql = "insert into ".WSY_SHOP.".suning_setting(customer_id,is_open_suning,isvalid,createtime) values (".$customer_id.",".$is_open_suning.",true,now())";
}
_mysql_query($sql);

 $error =mysql_error();
 mysql_close($link);
//echo $error; 
 echo "<script>location.href='set.php?customer_id=".$customer_id_en."';</script>"
?>