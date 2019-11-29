<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');



//$commission =$configutil->splash_new($_POST["commission"]);
//$commission = round($commission,2);
$brand_adimg 	 = $configutil->splash_new($_POST["brand_adimg"]);  //分类页的品牌分类广告图片
$type_foreign_id = $configutil->splash_new($_POST["type_foreign_id"]);  //分类页品牌供应商广告图链接
$type_detail_id  = $configutil->splash_new($_POST["type_detail_id"]);  //分类页品牌供应商链接


$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
/*
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
*/
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

$createtime = date('Y-m-d H:i:s',time());



$query="select id from weixin_commonshop_supply_album where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$result_stat_num = mysql_num_rows($result);
$album_id=-1;

if($result_stat_num >0){
	while ($row = mysql_fetch_object($result)) {
		$album_id = $row->id;
		
        $sql="update weixin_commonshop_supply_album set brand_adimg='".$brand_adimg."',brand_ad_foreign_id='".$type_foreign_id."',brand_ad_detail_id='".$type_detail_id."',brand_linktype='".$linktype."',brand_adurl='".$type_adurl."' where id=".$album_id;
        _mysql_query($sql) or die('Query failed 39: ' . mysql_error());
	}	
}else{
     $sql="insert into
	weixin_commonshop_supply_album (customer_id,supply_id,isvalid,createtime,brand_adimg,brand_adurl,brand_ad_foreign_id,brand_ad_detail_id,brand_linktype) values (".$customer_id.",-1".",true,'".$createtime."','".$brand_adimg."','".$type_adurl."','".$type_foreign_id."','".$type_detail_id."','".$linktype."')";	

	_mysql_query($sql);

	 //$sql="update weixin_commonshops set isOpenSupply=".$isOpenSupply.",is_supplyset=".$is_supplyset.",isOpenBrandSupply=".$isOpenBrandSupply." where customer_id=".$customer_id;
	//_mysql_query($sql);

	 $error =mysql_error();
} 

$sql_supply="update weixin_commonshop_supplys set brand_adimg='".$brand_adimg."',brand_ad_foreign_id='".$type_foreign_id."',brand_ad_detail_id='".$type_detail_id."',brand_linktype='".$linktype."',brand_adurl='".$type_adurl."' where customer_id=".$customer_id;
	 _mysql_query($sql_supply) or die('Query failed 39: ' . mysql_error());
//$sql="update weixin_commonshop_supplys set brand_adimg='".$brand_adimg."',brand_ad_foreign_id='".$type_foreign_id."',brand_ad_detail_id='".$type_detail_id."',brand_linktype='".$linktype."',brand_adurl='".$type_adurl."' where id=".$supply_id;
//插入数据库

 mysql_close($link);
//echo $error; 
 echo "<script>location.href='album_manage.php?customer_id=".$customer_id_en."';</script>"
?>