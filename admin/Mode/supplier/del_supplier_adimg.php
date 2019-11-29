<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php');
require('../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');		
require('../../../../weixinpl/proxy_info.php');

$src = "";
if(!empty($_GET['src'])){
	$src = $_GET['src'];
}

$microshop_adimg = "";
$adimg = "";
$adcount = -1;
$new_adimg = "";
$new_foreign_id = "";
$new_detail_id = "";
$new_link_type = "";
$foreign_id = "";
$detail_id = "";
$link_type = "";
$query = "select microshop_adimg,foreign_id,detail_id,link_type from weixin_commonshop_customer_microshop where customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed del: ' . mysql_error());  
while ($row = mysql_fetch_object($result)) {
	$microshop_adimg = $row->microshop_adimg;
	$foreign_id = $row->foreign_id;
	$detail_id = $row->detail_id;
	$link_type = $row->link_type;
}
$adimg = explode("|",$microshop_adimg);
$foreign_id_array = explode("|",$foreign_id);
$detail_id_array = explode("|",$detail_id);
$link_type_array = explode("|",$link_type);
$adcount = count($adimg);
for($i=0;$i<$adcount;$i++){
	if($i==($num-1)){
		continue;
	}
	if($new_adimg==""){
		$new_adimg = $new_adimg.$adimg[$i];
	}else{
		$new_adimg = $new_adimg."|".$adimg[$i];
	}
}
for($i=0;$i<$adcount;$i++){
	if($i==($num-1)){
		continue;
	}
	if($new_foreign_id==""){
		$new_foreign_id = $new_foreign_id.$foreign_id_array[$i];
	}else{
		$new_foreign_id = $new_foreign_id."|".$foreign_id_array[$i];
	}
}
for($i=0;$i<$adcount;$i++){
	if($i==($num-1)){
		continue;
	}
	if($new_detail_id==""){
		$new_detail_id = $new_detail_id.$detail_id_array[$i];
	}else{
		$new_detail_id = $new_detail_id."|".$detail_id_array[$i];
	}
}
for($i=0;$i<$adcount;$i++){
	if($i==($num-1)){
		continue;
	}
	if($new_link_type==""){
		$new_link_type = $new_link_type.$link_type_array[$i];
	}else{
		$new_link_type = $new_link_type."|".$link_type_array[$i];
	}
}

	$query="update weixin_commonshop_supply_album set brand_adimg='' where brand_adimg='".$src."'";
_mysql_query($query) or die('Query failed del2: ' . mysql_error());
 echo "<script>window.parent.location.href='album_manage.php?customer_id=".$customer_id_en."';</script>";	
?>