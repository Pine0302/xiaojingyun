<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php');
require('../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');		
require('../../../../weixinpl/proxy_info.php');

$isOpenMicroshopAd=0; //是否开启微店广告图
$microshop_adimg="";//微店广告图
$foreign_id=-1; //固定链接ID
$detail_id=-1; //产品ID
$link_type="";//链接类型
$id=-1;
$link="#";
$is_microshopData=0;
$is_mandatoryAD=0;
$is_microshop=0;

$ids = array();
if(!empty($_POST["id1"])){
	$ids[0] = $configutil->splash_new($_POST["id1"]);
}
if(!empty($_POST["id2"])){
	$ids[1] = $configutil->splash_new($_POST["id2"]);
}
if(!empty($_POST["id3"])){
	$ids[2] = $configutil->splash_new($_POST["id3"]);
}
if(!empty($_POST["id4"])){
	$ids[3] = $configutil->splash_new($_POST["id4"]);
}
if(!empty($_POST["id5"])){
	$ids[4] = $configutil->splash_new($_POST["id5"]);
}

// if(!empty($_POST["id"])){
// 	$id = $configutil->splash_new($_POST["id"]);
// }

if(!empty($_POST["isOpenMicroshopAd"])){
	$isOpenMicroshopAd = $configutil->splash_new($_POST["isOpenMicroshopAd"]);
}
if(!empty($_POST["microshop_adimg"])){
	$microshop_adimg = $configutil->splash_new($_POST["microshop_adimg"]);
}
if(!empty($_POST["link_type"])){
	$link_type = $configutil->splash_new($_POST["link_type"]);
}
if(!empty($_POST["is_microshopData"])){
	$is_microshopData = $configutil->splash_new($_POST["is_microshopData"]);
}
if(!empty($_POST["is_mandatoryAD"])){
	$is_mandatoryAD = $configutil->splash_new($_POST["is_mandatoryAD"]);
}
if(!empty($_POST["is_microshop"])){
	$is_microshop = $configutil->splash_new($_POST["is_microshop"]);
}
$foreign_id_array = "";
$detail_id_array = "";
$link_type_array = "";
$link_array = "";
//产品ID 链接
if(!empty($_POST["detail_id1"])){
	$detail_id1 = $configutil->splash_new($_POST["detail_id1"]);
	$detail_id_array = $detail_id_array.$detail_id1;
}
if(!empty($_POST["foreign_id1"])){
	$foreign_id1 = $configutil->splash_new($_POST["foreign_id1"]);
	$typestrarr= explode("_",$foreign_id1);
	$foreign_id1 = $typestrarr[0];
	$link_type1=$typestrarr[1];
	$foreign_id_array = $foreign_id_array.$foreign_id1;
	//$link_type_array = $link_type_array.$link_type;
	if($foreign_id1 > 0){
		if($link_type1==1){
			if($detail_id1>0){			
				$link1="product_detail.php?customer_id=".$customer_id_en."&pid=".$detail_id1;				 
			}else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$foreign_id1;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$link1="list.php?customer_id=".$customer_id_en."&tid=".$foreign_id1."&tname=".$typename;
			}
		//$link_array = $link_array.$link;
		}else if($link_type1==2){
		   //图文
			$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$foreign_id1;
			$result = _mysql_query($query) or die('Query failed 2: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $website_url = $row->website_url;
			}
			$pos = strpos($website_url,"?"); 
			if($pos>0){
			   $website_url = $website_url."&C_id=".$customer_id_en;
			}else{
			   $website_url = $website_url."?C_id=".$customer_id_en;
			}
			$link1 = $website_url;
		}
	}else{
	    switch($foreign_id1){	    	
			case -6:
				$link1="list.php?customer_id=".$customer_id_en;
				break;
			case -2:
				$link1="list.php?isnew=1&customer_id=".$customer_id_en;
				break;
			case -3:
				$link1="list.php?ishot=1&customer_id=".$customer_id_en;
				break;
			case -4:
				$link1="order_cart.php?customer_id=".$customer_id_en;
				break;
			case -7:
				$link1="class_page.php?customer_id=".$customer_id_en;
				break;
			case -8:
				$link1="personal_center.php?customer_id=".$customer_id_en;
				break;
			case -9:
				$link1="index.php?customer_id=".$customer_id_en;
				break;
			case -5:
				$link1="snap_up.php?customer_id=".$customer_id_en;
				break;
			case -10:
				$link1="../online/show_online.php?customer_id=".$customer_id_en; 	  
				break;	
			case -11:
				$link1="package_list.php?customer_id=".$customer_id_en; 	  
				break;
			case -12:
				$link1="list.php?isvp=1&customer_id=".$customer_id_en; 	  
				break;
		}
	}

}


if(!empty($_POST["detail_id2"])){
	$detail_id2 = $configutil->splash_new($_POST["detail_id2"]);
	$detail_id_array = $detail_id_array."|".$detail_id2;
}
if(!empty($_POST["foreign_id2"])){
	$foreign_id2 = $configutil->splash_new($_POST["foreign_id2"]);
	$typestrarr= explode("_",$foreign_id2);
	$foreign_id2 = $typestrarr[0];
	$link_type2=$typestrarr[1];
	$foreign_id_array = $foreign_id_array."|".$foreign_id2;
	//$link_type_array = $link_type_array."|".$link_type;
	if($foreign_id2>0){
		if($link_type2==1){
			 if($detail_id2>0){
				
				 $link2="product_detail.php?customer_id=".$customer_id_en."&pid=".$detail_id2;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$foreign_id;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$link2="list.php?customer_id=".$customer_id_en."&tid=".$foreign_id2."&tname=".$typename;
			}
			//$link_array = $link_array."|".$link;
		}else if($link_type2==2){
		   //图文
			$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$foreign_id2;
			$result = _mysql_query($query) or die('Query failed 2: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $website_url = $row->website_url;
			}
			$pos = strpos($website_url,"?"); 
			if($pos>0){
			   $website_url = $website_url."&C_id=".$customer_id_en;
			}else{
			   $website_url = $website_url."?C_id=".$customer_id_en;
			}
			$link2 = $website_url;
		}
	}else{
		switch($foreign_id2){	    	
			case -6:
				$link2="list.php?customer_id=".$customer_id_en;
				break;
			case -2:
				$link2="list.php?isnew=1&customer_id=".$customer_id_en;
				break;
			case -3:
				$link2="list.php?ishot=1&customer_id=".$customer_id_en;
				break;
			case -4:
				$link2="order_cart.php?customer_id=".$customer_id_en;
				break;
			case -7:
				$link2="class_page.php?customer_id=".$customer_id_en;
				break;
			case -8:
				$link2="personal_center.php?customer_id=".$customer_id_en;
				break;
			case -9:
				$link2="index.php?customer_id=".$customer_id_en;
				break;
			case -5:
				$link2="snap_up.php?customer_id=".$customer_id_en;
				break;
			case -10:
				$link2="../online/show_online.php?customer_id=".$customer_id_en; 	  
				break;	
			case -11:
				$link2="package_list.php?customer_id=".$customer_id_en; 	  
				break;
			case -12:
				$link2="list.php?isvp=1&customer_id=".$customer_id_en; 	  
				break;
		}
	}
}
		

if(!empty($_POST["detail_id3"])){
	$detail_id3 = $configutil->splash_new($_POST["detail_id3"]);
	$detail_id_array = $detail_id_array."|".$detail_id3;
}
if(!empty($_POST["foreign_id3"])){
	$foreign_id3 = $configutil->splash_new($_POST["foreign_id3"]);
	$typestrarr= explode("_",$foreign_id3);
	$foreign_id3 = $typestrarr[0];
	$link_type3=$typestrarr[1];
	//$link_type_array = $link_type_array."|".$link_type;
	$foreign_id_array = $foreign_id_array."|".$foreign_id3;

	if($foreign_id3>0){
		if($link_type3==1){
			 if($detail_id3>0){
				
				 $link3="product_detail.php?customer_id=".$customer_id_en."&pid=".$detail_id3;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$foreign_id3;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$link3="list.php?customer_id=".$customer_id_en."&tid=".$foreign_id3."&tname=".$typename;
			}
			//$link_array = $link_array."|".$link;
		}else if($link_type3==2){
		   //图文
			$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$foreign_id3;
			$result = _mysql_query($query) or die('Query failed 2: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $website_url = $row->website_url;
			}
			$pos = strpos($website_url,"?"); 
			if($pos>0){
			   $website_url = $website_url."&C_id=".$customer_id_en;
			}else{
			   $website_url = $website_url."?C_id=".$customer_id_en;
			}
			$link3 = $website_url;
		}
	}else{
		switch($foreign_id3){	    	
			case -6:
				$link3="list.php?customer_id=".$customer_id_en;
				break;
			case -2:
				$link3="list.php?isnew=1&customer_id=".$customer_id_en;
				break;
			case -3:
				$link3="list.php?ishot=1&customer_id=".$customer_id_en;
				break;
			case -4:
				$link3="order_cart.php?customer_id=".$customer_id_en;
				break;
			case -7:
				$link3="class_page.php?customer_id=".$customer_id_en;
				break;
			case -8:
				$link3="personal_center.php?customer_id=".$customer_id_en;
				break;
			case -9:
				$link3="index.php?customer_id=".$customer_id_en;
				break;
			case -5:
				$link3="snap_up.php?customer_id=".$customer_id_en;
				break;
			case -10:
				$link3="../online/show_online.php?customer_id=".$customer_id_en; 	  
				break;	
			case -11:
				$link3="package_list.php?customer_id=".$customer_id_en; 	  
				break;
			case -12:
				$link3="list.php?isvp=1&customer_id=".$customer_id_en; 	  
				break;
		}
	}
		
}

if(!empty($_POST["detail_id4"])){
	$detail_id4 = $configutil->splash_new($_POST["detail_id4"]);
	$detail_id_array = $detail_id_array."|".$detail_id4;
}
if(!empty($_POST["foreign_id4"])){
	$foreign_id4 = $configutil->splash_new($_POST["foreign_id4"]);
	$typestrarr= explode("_",$foreign_id4);
	$foreign_id4 = $typestrarr[0];
	$link_type4=$typestrarr[1];
	$foreign_id_array = $foreign_id_array."|".$foreign_id4;
	//$link_type_array = $link_type_array."|".$link_type;
	if($foreign_id4>0){
		if($link_type4==1){
			 if($detail_id4>0){
				
				 $link4="product_detail.php?customer_id=".$customer_id_en."&pid=".$detail_id4;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$foreign_id4;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$link4="list.php?customer_id=".$customer_id_en."&tid=".$foreign_id4."&tname=".$typename;
			}
			//$link_array = $link_array."|".$link;
		}else if($link_type4==2){
		   //图文
			$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$foreign_id4;
			$result = _mysql_query($query) or die('Query failed 2: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $website_url = $row->website_url;
			}
			$pos = strpos($website_url,"?"); 
			if($pos>0){
			   $website_url = $website_url."&C_id=".$customer_id_en;
			}else{
			   $website_url = $website_url."?C_id=".$customer_id_en;
			}
			$link4 = $website_url;
		}
	}else{
		switch($foreign_id4){	    	
			case -6:
				$link4="list.php?customer_id=".$customer_id_en;
				break;
			case -2:
				$link4="list.php?isnew=1&customer_id=".$customer_id_en;
				break;
			case -3:
				$link4="list.php?ishot=1&customer_id=".$customer_id_en;
				break;
			case -4:
				$link4="order_cart.php?customer_id=".$customer_id_en;
				break;
			case -7:
				$link4="class_page.php?customer_id=".$customer_id_en;
				break;
			case -8:
				$link4="personal_center.php?customer_id=".$customer_id_en;
				break;
			case -9:
				$link4="index.php?customer_id=".$customer_id_en;
				break;
			case -5:
				$link4="snap_up.php?customer_id=".$customer_id_en;
				break;
			case -10:
				$link4="../online/show_online.php?customer_id=".$customer_id_en; 	  
				break;	
			case -11:
				$link4="package_list.php?customer_id=".$customer_id_en; 	  
				break;
			case -12:
				$link4="list.php?isvp=1&customer_id=".$customer_id_en; 	  
				break;
		}
	}
		
}
if(!empty($_POST["detail_id5"])){
	$detail_id5 = $configutil->splash_new($_POST["detail_id5"]);
	$detail_id_array = $detail_id_array."|".$detail_id5;
}
if(!empty($_POST["foreign_id5"])){
	$foreign_id5 = $configutil->splash_new($_POST["foreign_id5"]);
	$typestrarr= explode("_",$foreign_id5);
	$foreign_id5 = $typestrarr[0];
	$link_type5=$typestrarr[1];
	$foreign_id_array = $foreign_id_array."|".$foreign_id5;
	//$link_type_array = $link_type_array."|".$link_type;
	if($foreign_id5>0){
		if($link_type5==1){
			if($detail_id5>0){
				
				 $link5="product_detail.php?customer_id=".$customer_id_en."&pid=".$detail_id5;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$foreign_id5;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$link5="list.php?customer_id=".$customer_id_en."&tid=".$foreign_id5."&tname=".$typename;
			}
			//$link_array = $link_array."|".$link;
		}else if($link_type5==2){
		   //图文
			$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$foreign_id5;
			$result = _mysql_query($query) or die('Query failed 2: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $website_url = $row->website_url;
			}
			$pos = strpos($website_url,"?"); 
			if($pos>0){
			   $website_url = $website_url."&C_id=".$customer_id_en;
			}else{
			   $website_url = $website_url."?C_id=".$customer_id_en;
			}
			$link5 = $website_url;
		}
	}else{
		switch($foreign_id5){	    	
			case -6:
				$link5="list.php?customer_id=".$customer_id_en;
				break;
			case -2:
				$link5="list.php?isnew=1&customer_id=".$customer_id_en;
				break;
			case -3:
				$link5="list.php?ishot=1&customer_id=".$customer_id_en;
				break;
			case -4:
				$link5="order_cart.php?customer_id=".$customer_id_en;
				break;
			case -7:
				$link5="class_page.php?customer_id=".$customer_id_en;
				break;
			case -8:
				$link5="personal_center.php?customer_id=".$customer_id_en;
				break;
			case -9:
				$link5="index.php?customer_id=".$customer_id_en;
				break;
			case -5:
				$link5="snap_up.php?customer_id=".$customer_id_en;
				break;
			case -10:
				$link5="../online/show_online.php?customer_id=".$customer_id_en; 	  
				break;	
			case -11:
				$link5="package_list.php?customer_id=".$customer_id_en; 	  
				break;
			case -12:
				$link5="list.php?isvp=1&customer_id=".$customer_id_en; 	  
				break;
		}
	}
		
}
//产品ID 链接

//广告图
$adimg = "";
if(!empty($_POST["adimg1"])){
	$adimg1 = $configutil->splash_new($_POST["adimg1"]);	
	$adimg = $adimg.$adimg1;
}if(!empty($_POST["adimg2"])){
	$adimg2 = $configutil->splash_new($_POST["adimg2"]);
	$adimg = $adimg."|".$adimg2;
}if(!empty($_POST["adimg3"])){
	$adimg3 = $configutil->splash_new($_POST["adimg3"]);
	$adimg = $adimg."|".$adimg3;
}if(!empty($_POST["adimg4"])){
	$adimg4 = $configutil->splash_new($_POST["adimg4"]);
	$adimg = $adimg."|".$adimg4;
}if(!empty($_POST["adimg5"])){
	$adimg5 = $configutil->splash_new($_POST["adimg5"]);
	$adimg = $adimg."|".$adimg5;
}

//广告图链接
// if($foreign_id){ //创建连接
	// if($foreign_id>0){
		// $typestrarr= explode("_",$foreign_id);
		// $foreign_id = $typestrarr[0];
		// $link_type=$typestrarr[1];
		// if($link_type==1){
			 // if($detail_id>0){
				
				 // $link="detail.php?customer_id=".$customer_id_en."&pid=".$detail_id;
			 
			 // }else{					
				// $query3="select name from weixin_commonshop_types where isvalid=true and id=".$foreign_id;
				// $result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				// $typename="";
				// while ($row3 = mysql_fetch_object($result3)) {
				   // $typename = $row3->name;
				// }
				// $link="list.php?customer_id=".$customer_id_en."&tid=".$foreign_id."&tname=".$typename;
			// }
		// }
	// }
// }

$count = count($ids);

for($i=1;$i<=5;$i++){
	switch($i){
		case 1:
			$img 		= "adimg1";
			$url 		= "link1";
			$foreign 	= "foreign_id1";
			$detail  	= "detail_id1";
			$linktype   = "link_type1";
			break;
		case 2:
			$img 		= "adimg2";
			$url 		= "link2";
			$foreign 	= "foreign_id2";
			$detail  	= "detail_id2";
			$linktype   = "link_type2";
			break;
		case 3:
			$img 		= "adimg3";
			$url 		= "link3";
			$foreign 	= "foreign_id3";
			$detail  	= "detail_id3";
			$linktype   = "link_type3";
			break;
		case 4:
			$img 		= "adimg4";
			$url 		= "link4";
			$foreign 	= "foreign_id4";
			$detail  	= "detail_id4";
			$linktype   = "link_type4";
			break;
		case 5:
			$img 		= "adimg5";
			$url 		= "link5";
			$foreign 	= "foreign_id5";
			$detail  	= "detail_id5";
			$linktype   = "link_type5";
			break;
	}

	if($ids[$i-1] > 0){		
		$query = "update weixin_commonshop_supply_album set brand_adimg='".$$img."',brand_adurl='".$$url."',brand_ad_foreign_id='".$$foreign."',brand_ad_detail_id='".$$detail."',brand_linktype='".$$linktype."',createtime=now() where customer_id=".$customer_id." and supply_id=-1 and isvalid=true and types=".$i." and id=".$ids[$i-1];
		//echo $query;
		//echo "<br>";

	}else{

		$query = "insert into weixin_commonshop_supply_album (customer_id,supply_id,brand_adimg,brand_adurl,brand_ad_foreign_id,brand_ad_detail_id,brand_linktype,types,isvalid,createtime) values (".$customer_id.",-1,'".$$img."','".$$url."','".$$foreign."','".$$detail."','".$$linktype."',".$i.",true,now())";
		
	}
	
	_mysql_query($query);
}
//return;
mysql_close($link);
echo "<script>location.href='album_manage.php?customer_id=".$customer_id_en."';</script>"
?>