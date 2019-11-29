<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php');
require('../../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');		
require('../../../../../weixinpl/proxy_info.php');

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
if(!empty($_POST["id"])){
	$id = $configutil->splash_new($_POST["id"]);
}
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





//产品ID 链接 旧链接
/*
if(!empty($_POST["detail_id1"])){
	$detail_id1 = $configutil->splash_new($_POST["detail_id1"]);
	$detail_id_array = $detail_id_array.$detail_id1;
}
if(!empty($_POST["foreign_id1"])){
	$foreign_id1 = $configutil->splash_new($_POST["foreign_id1"]);
	$typestrarr= explode("_",$foreign_id1);
	$foreign_id1 = $typestrarr[0];
	$link_type=$typestrarr[1];
	$foreign_id_array = $foreign_id_array.$foreign_id1;
	$link_type_array = $link_type_array.$link_type;
			if($link_type==1){
			 if($detail_id1>0){
				
				 $link="../../common_shop/jiushop/detail_default.php?customer_id=".$customer_id_en."&pid=".$detail_id1;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$foreign_id1;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$link="list.php?customer_id=".$customer_id_en."&tid=".$foreign_id1."&tname=".$typename;
			}
			$link_array = $link_array.$link;
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
	$link_type=$typestrarr[1];
	$foreign_id_array = $foreign_id_array."|".$foreign_id2;
	$link_type_array = $link_type_array."|".$link_type;
		if($link_type==1){
			 if($detail_id2>0){
				
				 $link="../../common_shop/jiushop/detail_default.php?customer_id=".$customer_id_en."&pid=".$detail_id2;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$foreign_id2;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$link="list.php?customer_id=".$customer_id_en."&tid=".$foreign_id2."&tname=".$typename;
			}
			$link_array = $link_array."|".$link;
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
	$link_type=$typestrarr[1];
	$link_type_array = $link_type_array."|".$link_type;
	$foreign_id_array = $foreign_id_array."|".$foreign_id3;
		if($link_type==1){
			 if($detail_id3>0){
				
				 $link="../../common_shop/jiushop/detail_default.php?customer_id=".$customer_id_en."&pid=".$detail_id3;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$foreign_id3;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$link="list.php?customer_id=".$customer_id_en."&tid=".$foreign_id3."&tname=".$typename;
			}
			$link_array = $link_array."|".$link;
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
	$link_type=$typestrarr[1];
	$foreign_id_array = $foreign_id_array."|".$foreign_id4;
	$link_type_array = $link_type_array."|".$link_type;
		if($link_type==1){
			 if($detail_id4>0){
				
				 $link="../../common_shop/jiushop/detail_default.php?customer_id=".$customer_id_en."&pid=".$detail_id4;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$foreign_id4;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$link="list.php?customer_id=".$customer_id_en."&tid=".$foreign_id4."&tname=".$typename;
			}
			$link_array = $link_array."|".$link;
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
	$link_type=$typestrarr[1];
	$foreign_id_array = $foreign_id_array."|".$foreign_id5;
	$link_type_array = $link_type_array."|".$link_type;
			if($link_type==1){
			 if($detail_id5>0){
				
				 $link="../../common_shop/jiushop/detail_default.php?customer_id=".$customer_id_en."&pid=".$detail_id5;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$foreign_id5;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$link="list.php?customer_id=".$customer_id_en."&tid=".$foreign_id5."&tname=".$typename;
			}
			$link_array = $link_array."|".$link;
		}
}*/
//产品ID 链接


//新链接
include_once('../home_decoration/pink_selector_url.php');
if(!empty($_POST["selector_id1"])){
	$selector_id1 = $configutil->splash_new($_POST["selector_id1"]);
	$res = pink_selector_url($selector_id1,$protocol_http_host,$customer_id,$customer_id_en);
	$link_array = $link_array.$res['url'];
	$foreign_id_array = $foreign_id_array.$res['linktype'];
}
if(!empty($_POST["selector_id2"])){
	$selector_id2 = $configutil->splash_new($_POST["selector_id2"]);
	$res = pink_selector_url($selector_id2,$protocol_http_host,$customer_id,$customer_id_en);
	$link_array = $link_array."|".$res['url'];
	$foreign_id_array = $foreign_id_array."|".$res['linktype'];
}
if(!empty($_POST["selector_id3"])){
	$selector_id3 = $configutil->splash_new($_POST["selector_id3"]);
	$res = pink_selector_url($selector_id3,$protocol_http_host,$customer_id,$customer_id_en);
	$link_array = $link_array."|".$res['url'];
	$foreign_id_array = $foreign_id_array."|".$res['linktype'];
}
if(!empty($_POST["selector_id4"])){
	$selector_id4 = $configutil->splash_new($_POST["selector_id4"]);
	$res = pink_selector_url($selector_id4,$protocol_http_host,$customer_id,$customer_id_en);
	$link_array = $link_array."|".$res['url'];
	$foreign_id_array = $foreign_id_array."|".$res['linktype'];
}
if(!empty($_POST["selector_id5"])){
	$selector_id5 = $configutil->splash_new($_POST["selector_id5"]);
	$res = pink_selector_url($selector_id5,$protocol_http_host,$customer_id,$customer_id_en);
	$link_array = $link_array."|".$res['url'];
	$foreign_id_array = $foreign_id_array."|".$res['linktype'];
}

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

for($j=1;$j<6;$j++){
  ${"pc_adimg_info".$j}=array();
  ${"pc_adimg_info".$j}['microshop_adimg']='';
  ${"pc_adimg_info".$j}['foreign_id']='';
  ${"pc_adimg_info".$j}['link_type']='';
  ${"pc_adimg_info".$j}['link']='';
  ${"pc_adimg_info".$j}['detail_id']='';
}
$pc_adimg_info1=array();
$pc_adimg_info2=array();
$pc_adimg_info3=array();
$pc_adimg_info4=array();
$pc_adimg_info5=array();

//pc端广告图
$pc_adimg = "";
$pc_shop_adimg="";
if(!empty($_POST["pc_adimg1"])){
	$pc_adimg1 = $configutil->splash_new($_POST["pc_adimg1"]);
	$pc_adimg_info1['microshop_adimg'] = $pc_adimg1;
}if(!empty($_POST["pc_adimg2"])){
	$pc_adimg2 = $configutil->splash_new($_POST["pc_adimg2"]);
	$pc_adimg_info2['microshop_adimg'] = $pc_adimg2;
}if(!empty($_POST["pc_adimg3"])){
	$pc_adimg3 = $configutil->splash_new($_POST["pc_adimg3"]);
	$pc_adimg_info3['microshop_adimg'] = $pc_adimg3;
}if(!empty($_POST["pc_adimg4"])){
	$pc_adimg4 = $configutil->splash_new($_POST["pc_adimg4"]);
	$pc_adimg_info4['microshop_adimg'] = $pc_adimg4;
}if(!empty($_POST["pc_adimg5"])){
	$pc_adimg5 = $configutil->splash_new($_POST["pc_adimg5"]);
	$pc_adimg_info5['microshop_adimg'] = $pc_adimg5;
}

//pc端产品ID　链接


if(!empty($_POST["pc_foreign_id1"])){
	$pc_foreign_id1 = $configutil->splash_new($_POST["pc_foreign_id1"]);
	$typestrarr= explode("_",$pc_foreign_id1);
	$pc_foreign_id1 = $typestrarr[0];
	$pc_link_type=$typestrarr[1];
	$pc_adimg_info1['foreign_id'] = $pc_foreign_id1;
	$pc_adimg_info1['link_type'] =  $pc_link_type;
			if($pc_link_type==1){
			 if($pc_detail_id1>0){
				
				 $pc_link="../../common_shop/jiushop/detail_default.php?customer_id=".$customer_id_en."&pid=".$pc_detail_id1;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$pc_foreign_id1;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$pc_link="list.php?customer_id=".$customer_id_en."&tid=".$pc_foreign_id1."&tname=".$typename;
			}
			$pc_adimg_info1['link'] = $pc_link;
		}
}
if(!empty($_POST["pc_detail_id1"])){
	$pc_detail_id1 = $configutil->splash_new($_POST["pc_detail_id1"]);
	$pc_adimg_info1['detail_id'] = $pc_detail_id1;
}


if(!empty($_POST["pc_foreign_id2"])){
	$pc_foreign_id2 = $configutil->splash_new($_POST["pc_foreign_id2"]);
	$typestrarr= explode("_",$pc_foreign_id2);
	$pc_foreign_id2 = $typestrarr[0];
	$pc_link_type=$typestrarr[1];
	$pc_adimg_info2['foreign_id'] = $pc_foreign_id2;
	$pc_adimg_info2['link_type'] =  $pc_link_type;
		if($pc_link_type==1){
			 if($pc_detail_id2>0){
				
				 $pc_link="../../common_shop/jiushop/detail_default.php?customer_id=".$customer_id_en."&pid=".$pc_detail_id2;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$pc_foreign_id2;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$pc_link="list.php?customer_id=".$customer_id_en."&tid=".$pc_foreign_id2."&tname=".$typename;
			}
			$pc_adimg_info2['link'] = $pc_link;
		}
}
if(!empty($_POST["pc_detail_id2"])){
	$pc_detail_id2 = $configutil->splash_new($_POST["pc_detail_id2"]);
	$pc_adimg_info2['detail_id'] = $pc_detail_id2;
}

if(!empty($_POST["pc_foreign_id3"])){
	$pc_foreign_id3 = $configutil->splash_new($_POST["pc_foreign_id3"]);
	$typestrarr= explode("_",$pc_foreign_id3);
	$pc_foreign_id3 = $typestrarr[0];
	$pc_link_type=$typestrarr[1];
	$pc_adimg_info3['foreign_id'] = $pc_foreign_id3;
	$pc_adimg_info3['link_type'] =  $pc_link_type;
		if($pc_link_type==1){
			 if($pc_detail_id3>0){
				
				 $pc_link="../../common_shop/jiushop/detail_default.php?customer_id=".$customer_id_en."&pid=".$pc_detail_id3;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$pc_foreign_id3;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$pc_link="list.php?customer_id=".$customer_id_en."&tid=".$pc_foreign_id3."&tname=".$typename;
			}
			$pc_adimg_info3['link'] = $pc_link;
		}
}
if(!empty($_POST["pc_detail_id3"])){
	$pc_detail_id3 = $configutil->splash_new($_POST["pc_detail_id3"]);
	$pc_adimg_info3['detail_id'] = $pc_detail_id3;
}

if(!empty($_POST["pc_foreign_id4"])){
	$pc_foreign_id4 = $configutil->splash_new($_POST["pc_foreign_id4"]);
	$typestrarr= explode("_",$pc_foreign_id4);
	$pc_foreign_id4 = $typestrarr[0];
	$pc_link_type=$typestrarr[1];
	$pc_adimg_info4['foreign_id'] = $pc_foreign_id4;
	$pc_adimg_info4['link_type'] =  $pc_link_type;
		if($pc_link_type==1){
			 if($pc_detail_id4>0){
				
				 $pc_link="../../common_shop/jiushop/detail_default.php?customer_id=".$customer_id_en."&pid=".$pc_detail_id4;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$pc_foreign_id4;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$pc_link="list.php?customer_id=".$customer_id_en."&tid=".$pc_foreign_id4."&tname=".$typename;
			}
			$pc_adimg_info4['link'] = $pc_link;
		}
}
if(!empty($_POST["pc_detail_id4"])){
	$pc_detail_id4 = $configutil->splash_new($_POST["pc_detail_id4"]);
	$pc_adimg_info4['detail_id'] = $pc_detail_id4;
}

if(!empty($_POST["pc_foreign_id5"])){
	$pc_foreign_id5 = $configutil->splash_new($_POST["pc_foreign_id5"]);
	$typestrarr= explode("_",$pc_foreign_id5);
	$pc_foreign_id5 = $typestrarr[0];
	$pc_link_type=$typestrarr[1];
	$pc_adimg_info5['foreign_id'] = $pc_foreign_id5;
	$pc_adimg_info5['link_type'] =  $pc_link_type;
			if($pc_link_type==1){
			 if($pc_detail_id5>0){
				
				 $pc_link="../../common_shop/jiushop/detail_default.php?customer_id=".$customer_id_en."&pid=".$pc_detail_id5;
			 
			 }else{					
				$query3="select name from weixin_commonshop_types where isvalid=true and id=".$pc_foreign_id5;
				$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				$typename="";
				while ($row3 = mysql_fetch_object($result3)) {
				   $typename = $row3->name;
				}
				$pc_link="list.php?customer_id=".$customer_id_en."&tid=".$pc_foreign_id5."&tname=".$typename;
			}
			$pc_adimg_info5['link'] = $pc_link;
		}
}
if(!empty($_POST["pc_detail_id5"])){
	$pc_detail_id5 = $configutil->splash_new($_POST["pc_detail_id5"]);
	$pc_adimg_info5['detail_id'] = $pc_detail_id5;
}



$pc_shop_adimg=array($pc_adimg_info1,$pc_adimg_info2,$pc_adimg_info3,$pc_adimg_info4,$pc_adimg_info5);
$pc_shop_adimg_new=array();
foreach($pc_shop_adimg as $key=>$value){
	if((empty($pc_shop_adimg[$key]['detail_id'])&&empty($pc_shop_adimg[$key]['foreign_id'])&&empty($pc_shop_adimg[$key]['microshop_adimg']))||count($pc_shop_adimg[$key])==0){
	unset($pc_shop_adimg[$key]);
	}else{
	$pc_shop_adimg_new[]=$pc_shop_adimg[$key];
    }
}
if(count($pc_shop_adimg_new)==0){
$pc_shop_adimg = "";
}else{
$pc_shop_adimg = json_encode($pc_shop_adimg_new);
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



if($id<0){ //没有记录就执行插入
	$query="insert into weixin_commonshop_customer_microshop (customer_id,isOpenMicroshopAd,microshop_adimg,foreign_id,detail_id,link_type,link,creatime,is_microshopData,is_microshop,is_mandatoryAD,pc_shop_adimg) values (".$customer_id.",".$isOpenMicroshopAd.",'".$adimg."','".$foreign_id_array."','".$detail_id_array."','".$link_type_array."','".$link_array."',now(),".$is_microshopData.",".$is_microshop.",".$is_mandatoryAD.",'".$pc_shop_adimg."')";
	
}else{ //有数据则插入
	$query="update weixin_commonshop_customer_microshop set isOpenMicroshopAd=".$isOpenMicroshopAd.",microshop_adimg='".$adimg."',foreign_id='".$foreign_id_array."',detail_id='".$detail_id_array."',link_type='".$link_type_array."',link='".$link_array."',is_microshopData=".$is_microshopData.",is_mandatoryAD=".$is_mandatoryAD.",is_microshop=".$is_microshop.",pc_shop_adimg='".$pc_shop_adimg."' where customer_id=".$customer_id;
}
//echo $query;
_mysql_query($query);


//微店开启权限
$keyid         	= $configutil->splash_new($_POST["keyid"]);
$wce_id         = $configutil->splash_new($_POST["wce_id"]);
$P_num         	= $configutil->splash_new($_POST["P_num"]);
$is_ncomission	= $configutil->splash_new($_POST["is_ncomission"]);
$is_shareholder	= $configutil->splash_new($_POST["is_shareholder"]);
$is_team 		= $configutil->splash_new($_POST["is_team"]);
$isOpenAgent 	= $configutil->splash_new($_POST["isOpenAgent"]);
$isOpenSupply	= $configutil->splash_new($_POST["isOpenSupply"]);
$p_str 			= "";
$p 				= "";
$g_str 			= "";
$q_str 			= "";
$D_1 			= "";
$Y_1 			= "";
$microshop_open_permissions_code = "";
if( $is_ncomission ){
	for( $i = 1; $i <= $P_num; $i++){
		$p = $configutil->splash_new($_POST["P_".$i]);
		if( $p =='on' ){
			$p_str .= "P_".$i.","; 
		}		
	}
}else{
	$P_1		= $configutil->splash_new($_POST["P_1"]);
	if( $P_1 =='on' ){
		$p_str .= "P_1,";
	}	
}
if( !empty( $p_str ) ){
	$microshop_open_permissions_code .= $p_str;
}
if( $is_shareholder ){
	$G_1	= $configutil->splash_new($_POST["G_1"]);
	$G_2	= $configutil->splash_new($_POST["G_2"]);
	$G_3	= $configutil->splash_new($_POST["G_3"]);
	$G_4	= $configutil->splash_new($_POST["G_4"]);
	if( $G_1 =='on' ){
		$g_str .= "G_1,"; 
	}
	if( $G_2 =='on' ){
		$g_str .= "G_2,"; 
	}
	if( $G_3 =='on' ){
		$g_str .= "G_3,"; 
	}
	if( $G_4 =='on' ){
		$g_str .= "G_4,"; 
	}
}
if( !empty( $g_str ) ){
	$microshop_open_permissions_code .= $g_str;
}
if( $is_team ){
	$is_diy_area	= $configutil->splash_new($_POST["is_diy_area"]);
	$Q_1			= $configutil->splash_new($_POST["Q_1"]);
	$Q_2			= $configutil->splash_new($_POST["Q_2"]);
	$Q_3			= $configutil->splash_new($_POST["Q_3"]);
	$Q_4			= $configutil->splash_new($_POST["Q_4"]);
	if( $Q_1 =='on' ){
		$q_str .= "Q_1,"; 
	}
	if( $Q_2 =='on' ){
		$q_str .= "Q_2,"; 
	}
	if( $Q_3 =='on' ){
		$q_str .= "Q_3,"; 
	}
	if( $Q_4 =='on' and $is_diy_area == 1 ){
		$q_str .= "Q_4,"; 
	}
}
if( !empty( $q_str ) ){
	$microshop_open_permissions_code .= $q_str;
}
if( $isOpenAgent ){
	$D_1 = $configutil->splash_new($_POST["D_1"]);
}
if( !empty( $D_1 ) ){
	$microshop_open_permissions_code .= "D_1,";
}
if( $isOpenSupply ){
	$Y_1 = $configutil->splash_new($_POST["Y_1"]);
}
if( !empty( $Y_1 ) ){
	$microshop_open_permissions_code .= "Y_1,";
}

//微店开启权限
$keyid         	= $configutil->splash_new($_POST["keyid"]);
$wce_id         = $configutil->splash_new($_POST["wce_id"]);
$P_num         	= $configutil->splash_new($_POST["P_num"]);
$is_ncomission	= $configutil->splash_new($_POST["is_ncomission"]);
$is_shareholder	= $configutil->splash_new($_POST["is_shareholder"]);
$is_team 		= $configutil->splash_new($_POST["is_team"]);
$isOpenAgent 	= $configutil->splash_new($_POST["isOpenAgent"]);
$isOpenSupply	= $configutil->splash_new($_POST["isOpenSupply"]);
$p_str 			= "";
$p 				= "";
$g_str 			= "";
$q_str 			= "";
$D_1 			= "";
$Y_1 			= "";
$microshop_open_permissions_code = "";
if( $is_ncomission ){
	for( $i = 1; $i <= $P_num; $i++){
		$p = $configutil->splash_new($_POST["P_".$i]);
		if( $p =='on' ){
			$p_str .= "P_".$i.","; 
		}		
	}
}else{
	$P_1		= $configutil->splash_new($_POST["P_1"]);
	if( $P_1 =='on' ){
		$p_str .= "P_1,";
	}	
}
if( !empty( $p_str ) ){
	$microshop_open_permissions_code .= $p_str;
}
if( $is_shareholder ){
	$G_1	= $configutil->splash_new($_POST["G_1"]);
	$G_2	= $configutil->splash_new($_POST["G_2"]);
	$G_3	= $configutil->splash_new($_POST["G_3"]);
	$G_4	= $configutil->splash_new($_POST["G_4"]);
	if( $G_1 =='on' ){
		$g_str .= "G_1,"; 
	}
	if( $G_2 =='on' ){
		$g_str .= "G_2,"; 
	}
	if( $G_3 =='on' ){
		$g_str .= "G_3,"; 
	}
	if( $G_4 =='on' ){
		$g_str .= "G_4,"; 
	}
}
if( !empty( $g_str ) ){
	$microshop_open_permissions_code .= $g_str;
}
if( $is_team ){
	$is_diy_area	= $configutil->splash_new($_POST["is_diy_area"]);
	$Q_1			= $configutil->splash_new($_POST["Q_1"]);
	$Q_2			= $configutil->splash_new($_POST["Q_2"]);
	$Q_3			= $configutil->splash_new($_POST["Q_3"]);
	$Q_4			= $configutil->splash_new($_POST["Q_4"]);
	if( $Q_1 =='on' ){
		$q_str .= "Q_1,"; 
	}
	if( $Q_2 =='on' ){
		$q_str .= "Q_2,"; 
	}
	if( $Q_3 =='on' ){
		$q_str .= "Q_3,"; 
	}
	if( $Q_4 =='on' and $is_diy_area == 1 ){
		$q_str .= "Q_4,"; 
	}
}
if( !empty( $q_str ) ){
	$microshop_open_permissions_code .= $q_str;
}
if( $isOpenAgent ){
	$D_1 = $configutil->splash_new($_POST["D_1"]);
}
if( !empty( $D_1 ) ){
	$microshop_open_permissions_code .= "D_1,";
}
if( $isOpenSupply ){
	$Y_1 = $configutil->splash_new($_POST["Y_1"]);
}
if( !empty( $Y_1 ) ){
	$microshop_open_permissions_code .= "Y_1,";
}
//赠送身份
$microshop_give_identity = '';
$microshop_give_identity	= $configutil->splash_new($_POST["microshop_give_identity"]);


$query = "select id from weixin_commonshops_extend where isvalid=true and shop_id=".$keyid;
$result = _mysql_query($query) or die('Query failed1: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$wce_id = $row->id;
}
if($wce_id>0){
	$sql="update weixin_commonshops_extend set 
		microshop_open_permissions_code='".$microshop_open_permissions_code."',
		microshop_give_identity='".$microshop_give_identity."'
		where isvalid=true and shop_id=".$keyid; 
}else{
	$sql="insert into weixin_commonshops_extend(shop_id,createtime,isvalid,customer_id,is_Pinformation,is_stockOut,is_division,is_promoter,microshop_open_permissions_code,microshop_give_identity) values(".$keyid.",now(),true,".$customer_id.",0,0,0,0,'".$microshop_open_permissions_code."','".$microshop_give_identity."')";
}
	//echo $sql;
$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());
mysql_close($link);
echo "<script>location.href='microshop_set.php?customer_id=".$customer_id_en."';</script>"
?>