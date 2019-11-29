<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php');
require('../../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');		
require('../../../../../weixinpl/proxy_info.php');

$num = -1;
//echo $id;
if(!empty($_GET['num'])){
	$num = $_GET['num'];
}
//echo $num;
if(!empty($_GET['is_pc'])){
	$is_pc = $_GET['is_pc'];
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
$query = "select microshop_adimg,foreign_id,detail_id,link_type,pc_shop_adimg from weixin_commonshop_customer_microshop where customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed del: ' . mysql_error());  
while ($row = mysql_fetch_object($result)) {
	$microshop_adimg = $row->microshop_adimg;
	$foreign_id = $row->foreign_id;
	$detail_id = $row->detail_id;
	$link_type = $row->link_type;
	$pc_shop_adimg = $row->pc_shop_adimg;
}
$adimg = explode("|",$microshop_adimg);
$foreign_id_array = explode("|",$foreign_id);
$detail_id_array = explode("|",$detail_id);
$link_type_array = explode("|",$link_type);
$adcount = count($adimg);


if($is_pc>0){
//pc端广告图把图片，将json格式变成数组
$pc_adimg_info =  json_decode($pc_shop_adimg,true);
// $pc_foreign_id_array 	= explode("|",$pc_adimg_info['foreign_id']);
// $pc_adimg = explode("|",$pc_adimg_info['microshop_adimg']);
// $pc_detail_id_array 	= explode("|",$pc_adimg_info['detail_id']);
// $pc_link_type_array 	= explode("|",$pc_adimg_info['link_type']);	
$new_pc_adimg_info=array();
foreach ($pc_adimg_info as $k => $v){
if($k==$num-1){
    unset($pc_adimg_info[$k]);
}else{
    $new_pc_adimg_info[]=$pc_adimg_info[$k];
}
}
$pc_shop_adimg=json_encode($new_pc_adimg_info);
$query="update weixin_commonshop_customer_microshop set pc_shop_adimg='".$pc_shop_adimg."' where customer_id=".$customer_id."";
}else{//非pc端

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
$query="update weixin_commonshop_customer_microshop set microshop_adimg='".$new_adimg."',foreign_id='".$new_foreign_id."',detail_id='".$new_detail_id."',link_type='".$new_link_type."' where customer_id=".$customer_id."";
}

   
_mysql_query($query) or die('Query failed del2: ' . mysql_error());
 echo "<script>window.parent.location.href='microshop_set.php?customer_id=".$customer_id_en."';</script>";	
?>