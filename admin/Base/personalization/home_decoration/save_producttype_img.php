<?php  //保存分类首页显示的图片以及连接
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
require('../../../../../weixinpl/back_newshops/Base/personalization/home_decoration/product_type_utlity.php');
require('../../../../../weixinpl/common/utility_4m.php');
 
$index_catnum=-1;
$name =$configutil->splash_new($_POST["name"]);
$keyid =$configutil->splash_new($_POST["keyid"]);

$parent_id = $configutil->splash_new($_POST["parent_id"]);
$sendstyle=$configutil->splash_new($_POST["sendstyle"]);
$type_imgurl=$configutil->splash_new($_POST["type_imgurl"]);
$index_catnum=$configutil->splash_new($_POST["index_catnum"]);
$adminuser_id = $configutil->splash_new($_GET["adminuser_id"]);
$orgin_adminuser_id = $configutil->splash_new($_GET["orgin_adminuser_id"]);
$owner_general = $configutil->splash_new($_GET["owner_general"]);



if($_POST["temp41"]){
	$temp41 = $configutil->splash_new($_POST["temp41"]);		
}
if($_POST["temp48"]){
	$temp48 = $configutil->splash_new($_POST["temp48"]);		
}


if($temp48){  //47模板专属	
	$product_detail_id_cat_index =-1;
	$type_id_5_index = $configutil->splash_new($_POST["type_id_5_index"]);
	$product_detail_id_cat_index = $configutil->splash_new($_POST["product_detail_id_cat_index"]);
	$f = fopen('out_savetype.txt', 'w'); 
	$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
	 mysql_select_db(DB_NAME) or die('Could not select database');
	$customer_ids="";
			
	$url="-1";
	if($type_id_5_index>0 or $type_id_5_index == -40){
		$typestrarr= explode("_",$type_id_5_index);
		$type_id_5_index = $typestrarr[0];
		$linktype=$typestrarr[1];
		if($type_id_5_index == -40){
			$linktype = 1;
			$product_types_5_index = '';
			if(!empty($_POST["product_type_5_index"])){
				$product_types_5_index = $configutil->splash_new($_POST["product_type_5_index"]);
			}
			$product_types_5_index_arr = explode("_",$product_types_5_index);
			$product_types_5_index = $product_types_5_index_arr[0];
			 $product_detail_id_cat_index = $configutil->splash_new($_POST["product_detail_id_cat_index"]);        
			 if($product_detail_id_cat_index>0){
				
				 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_cat_index;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_5_index;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_5_index."&tname=".$typename;
			}
			$type_id_5_index = $product_types_5_index.'_1';
		}else if($linktype==2){
		   //图文
			$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_5_index;
			$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $website_url = $row->website_url;
			}
			$pos = strpos($website_url,"?"); 
			if($pos>0){
			   $website_url = $website_url."&C_id=".$customer_id_en;
			}else{
			   $website_url = $website_url."?C_id=".$customer_id_en;
			}
			$url = $website_url;
			$type_id_5_index = $type_id_5_index.'_2';
		}
	}else{
	   switch($type_id_5_index){
		   case -6:
			  $url="../../mshop/list.php?customer_id=".$customer_id_en;
			  break;
		   case -2:
			  $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
			  break;
		   case -3:
			  $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
			  break;
		   case -4:
			  $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
			  break;
		   case -7:
			  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
			  break;
		   case -8:
			  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
			  break;
	   }
	}
	$foreign_id=$type_id_5_index;
} //47模板专属	  


if($temp41){  //41模板专属	
	$product_detail_id_cat =-1;
	$type_id_5 = $configutil->splash_new($_POST["type_id_5"]);
	$product_detail_id_cat = $configutil->splash_new($_POST["product_detail_id_cat"]);
	$f = fopen('out_savetype.txt', 'w'); 
	$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
	 mysql_select_db(DB_NAME) or die('Could not select database');
	$customer_ids="";
			
	$url="-1";
	if($type_id_5>0 or $type_id_5 == -40){
		$typestrarr= explode("_",$type_id_5);
		$type_id_5 = $typestrarr[0];
		$linktype=$typestrarr[1];
		if($type_id_5 == -40){
			$linktype = 1;
			$product_types_5 = '';
			if(!empty($_POST["product_type_5"])){
				$product_types_5 = $configutil->splash_new($_POST["product_type_5"]);
			}
			$product_types_5_arr = explode("_",$product_types_5);
			$product_types_5 = $product_types_5_arr[0];
			 $product_detail_id_cat = $configutil->splash_new($_POST["product_detail_id_cat"]);        
			 if($product_detail_id_cat>0){
				
				 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_cat;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_5;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_5."&tname=".$typename;
			}
			$type_id_5 = $product_types_5.'_1';
		}else if($linktype==2){
		   //图文
			$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_5;
			$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $website_url = $row->website_url;
			}
			$pos = strpos($website_url,"?"); 
			if($pos>0){
			   $website_url = $website_url."&C_id=".$customer_id_en;
			}else{
			   $website_url = $website_url."?C_id=".$customer_id_en;
			}
			$url = $website_url;
			$type_id_5 = $type_id_5.'_2';
		}
	}else{
	   switch($type_id_5){
		   case -6:
			  $url="../../mshop/list.php?customer_id=".$customer_id_en;
			  break;
		   case -2:
			  $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
			  break;
		   case -3:
			  $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
			  break;
		   case -4:
			  $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
			  break;
		   case -7:
			  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
			  break;
		   case -8:
			  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
			  break;
	   }
	}
	$foreign_id=$type_id_5;
} //41模板专属	  


	
	
	//echo "UPDATE weixin_commonshop_types set cat_detail_id='".$product_detail_id_cat."',index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."' where id=".$keyid."";
	if($temp48){
		//  47,48 分类首页显示图模板用这个 
		_mysql_query("UPDATE weixin_commonshop_types set cat_index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."',cat_detail_id='".$product_detail_id_cat_index."' where id=".$keyid."");
		//echo "UPDATE weixin_commonshop_types set cat_detail_id='".$product_detail_id_cat."',cat_index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."',cat_detail_id='".$product_detail_id_cat_index."' where id=".$keyid."";return;		
	}elseif($temp41){
		//楼层广告显示
		 _mysql_query("UPDATE weixin_commonshop_types set cat_detail_id='".$product_detail_id_cat."',index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."',cat_detail_id='".$product_detail_id_cat."' where id=".$keyid."");
		_mysql_query("UPDATE weixin_commonshop_types set cat_detail_id='".$product_detail_id_cat."',index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."' where id=".$keyid."");
		//echo "UPDATE weixin_commonshop_types set cat_detail_id='".$product_detail_id_cat."',index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."',cat_detail_id='".$product_detail_id_cat."' where id=".$keyid."";return;
	}
	
 $error =mysql_error();
 mysql_close($link);
 echo $error; 

 echo "<script>location.href='defaultset.php?default_set=1&customer_id=".$customer_id_en."&producttype_id=".$keyid."';</script>"
?>