<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php'); 

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');		
_mysql_query("SET NAMES UTF8");	
$op = $_REQUEST['op'];
if (!empty($_POST['level_name'])) {
	$level_name =$_POST['level_name'];
}
if (!empty($_REQUEST['type'])) {
	$type =$_REQUEST['type'];
}
if (!empty($_POST['label_level'])) {
	$label_level =$_POST['label_level'];
}
if (!empty($_POST['label_image'])) {
	$label_image =$_POST['label_image'];
}
if (!empty($_REQUEST['keyid'])) {
	$keyid =$_REQUEST['keyid'];
	$keyid =passport_decrypt($keyid);
}
if (!empty($_REQUEST['defaultImg'])) {
	$defaultimg =$_REQUEST['defaultImg'];
}
if (!empty($_POST['selectIcon'])) {
	$selectIcon =$_POST['selectIcon'];
}
if (!empty($_POST['selectImg'])) {
	$selectImg =$_POST['selectImg'];
}
if (!empty($_POST['selectIcon'])) {
	$label_image_type =$_POST['selectIcon'];
}
$label_image = "";
if(!empty($_FILES['upfile1']['name'])){
	$rand1=rand(0,9);
	$rand2=rand(0,9);
	$rand3=rand(0,9);
	$filename=date("Ymdhis").$rand1.$rand2.$rand3;
	$filetype=substr($_FILES['upfile1']['name'], strrpos($_FILES['upfile1']['name'], "."),strlen($_FILES['upfile1']['name'])-strrpos($_FILES['upfile1']['name'], "."));
	$filetype=strtolower($filetype);
	if(($filetype!='.jpg')&&($filetype!='.jpeg')&&($filetype!='.png')&&($filetype!='.gif')){
		echo "<script>alert('文件类型或地址错误');</script>";
		echo "<script>history.back(-1);</script>";
		exit ;
	}
	$filename=$filename.$filetype;
	$savedir='../../../'.Base_Upload.'Mode/welfare/';
	if(!is_dir($savedir)){
		mkdir($savedir,0777,true);
	}
	$savefile=$savedir.$filename;
	if (!_move_uploaded_file($_FILES['upfile1']['tmp_name'], $savefile)){
		echo "<script>history.back(-1);</script>";
		exit;
	}
	$label_image=$savefile;
	$label_image = str_replace("../","",$label_image);
//	$label_image = "/weixinpl/".$label_image;
	$label_image = "/mshop/".$label_image;
}else{
	$label_image=$configutil->splash_new($_POST['label_image']);
} 
// var_dump($label_image,$_REQUEST['defaultImg'],$defaultimg);exit;
$label_image2 = "";
if(!empty($_FILES['upfile2']['name'])){
	$rand1=rand(0,9);
	$rand2=rand(0,9);
	$rand3=rand(0,9);
	$filename=date("Ymdhis").$rand1.$rand2.$rand3;
	$filetype=substr($_FILES['upfile2']['name'], strrpos($_FILES['upfile2']['name'], "."),strlen($_FILES['upfile2']['name'])-strrpos($_FILES['upfile2']['name'], "."));
	$filetype=strtolower($filetype);
	if(($filetype!='.jpg')&&($filetype!='.jpeg')&&($filetype!='.png')&&($filetype!='.gif')){
		echo "<script>alert('文件类型或地址错误');</script>";
		echo "<script>history.back(-1);</script>";
		exit ;
	}
	$filename=$filename.$filetype;
	$savedir='../../../'.Base_Upload.'Mode/welfare/';
	if(!is_dir($savedir)){
		mkdir($savedir,0777,true);
	}
	$savefile=$savedir.$filename;
	if (!_move_uploaded_file($_FILES['upfile2']['tmp_name'], $savefile)){
		echo "<script>history.back(-1);</script>";
		exit;
	}
	$label_image2=$savefile;
	$label_image2 = str_replace("../","",$label_image2);
//	$label_image2 = "/weixinpl/".$label_image2;
	$label_image2 = "/mshop/".$label_image2;
}else{
	$label_image2=$configutil->splash_new($_POST['label_image2']);
} 
if($selectImg==1){
	$label_image2 = '';
}
if( $label_image_type==2 ){
	$label_image = '';
	$defaultimg = '';
}
if ($op=='add') {
	// var_dump($_REQUEST);exit;
	if ($selectIcon==0) {
		$query = "insert into weixin_cityarea_supply_label(level_name,label_level,isvalid,label_image,createtime,customer_id,label_image2,label_image_type)values('".$level_name."',".$label_level.",true,'".$label_image."','".date('y-m-d H:i:s',time())."',".$customer_id.",'{$label_image2}','{$label_image_type}')";
	}else{
    	$query = "insert into weixin_cityarea_supply_label(level_name,label_level,isvalid,label_image,createtime,customer_id,label_image2,label_image_type)values('".$level_name."',".$label_level.",true,'".$defaultimg."','".date('y-m-d H:i:s',time())."',".$customer_id.",'{$label_image2}','{$label_image_type}')";
	}
	// var_dump($query);exit();
	_mysql_query($query)or die('Query failed_add'.mysql_error());
}
if ($op=='detail') {
	if ($selectIcon==0) {
		$query = "update weixin_cityarea_supply_label set label_image2='{$label_image2}',level_name='".$level_name."',label_level='".$label_level."',label_image_type='".$label_image_type."',label_image='".$label_image."' where isvalid=true and id='".$keyid."' and customer_id=".$customer_id;
    }else{
    	$query = "update     weixin_cityarea_supply_label set label_image2='{$label_image2}',level_name='".$level_name."',label_level='".$label_level."',label_image_type='".$label_image_type."',label_image='".$defaultimg."' where isvalid=true and id='".$keyid."' and customer_id=".$customer_id;
    }
    $result=_mysql_query($query)or die('Query failed_update'.mysql_error());
}
if ($op=='del') {

	$query = "update weixin_cityarea_supply_label set isvalid=0 where id='".$keyid."' and customer_id=".$customer_id;
	// var_dump($type);exit();
		$result=_mysql_query($query)or die('Query failed_del'.mysql_error());

}
if ($op=='batchdel') {
	$idsStr = $_POST['idsStr'];
	$shop_name_id = explode(',', $idsStr);
	for ($i=0; $i < count($shop_name_id); $i++) { 
		$query_del_single = "update weixin_cityarea_supply_label set isvalid=false where customer_id =".$customer_id."  and id=".$shop_name_id[$i];
	_mysql_query($query_del_single)or die('Query_del_label_single failed'.mysql_error());
	}
	return;
}

echo "<script>location.href='grade_labeling.php?customer_id=".$customer_id."&type=".$type."';</script>";

// echo "<script>location.href='grade_labeling.php?customer_id=".$customer_id_en."';</script>";
?>