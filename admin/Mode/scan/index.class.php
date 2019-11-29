<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$keyid =$configutil->splash_new($_POST["keyid"]);   //ID编号
$content =$configutil->splash_new($_POST["content"]);   //内容
$deliver_template_type=1;
$deliver_template_type =$configutil->splash_new($_POST["deliver_template_type"]);   //核销二维码转发页面模板
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$share_title =$configutil->splash_new($_POST["share_title"]); 
$share_desc =$configutil->splash_new($_POST["share_desc"]); 

$share_img = "";
if(!empty($_FILES['new_share_img']['name'])){
	$rand1=rand(0,9);
	$rand2=rand(0,9);
	$rand3=rand(0,9);
	$filename=date("Ymdhis").$rand1.$rand2.$rand3;
	$filetype=substr($_FILES['new_share_img']['name'], strrpos($_FILES['new_share_img']['name'], "."),strlen($_FILES['new_share_img']['name'])-strrpos($_FILES['new_share_img']['name'], "."));
	$filetype=strtolower($filetype);
	if(($filetype!='.jpg')&&($filetype!='.png')&&($filetype!='.gif')){
			echo "<script>alert('文件类型或地址错误');</script>";
			echo "<script>history.back(-1);</script>";
			exit ;
		}
	$filename=$filename.$filetype;
	$savedir='../../../'.Base_Upload.'Mode/scan/';
	if(!is_dir($savedir)){
		mkdir($savedir,0777,true);
	}
	$savefile=$savedir.$filename;
	if (!_move_uploaded_file($_FILES['new_share_img']['tmp_name'], $savefile)){
		echo "<script>history.back(-1);</script>";
		exit;
	}
	$share_img=$savefile;
	$share_img = str_replace("../","",$share_img);
	$share_img = "/weixinpl/".$share_img;		
}else{
$share_img=$configutil->splash_new($_POST['share_img']);
} 
	
 if($keyid>0){
	$query = "update weixin_commonshop_order_qrset set markedWords='".$content."',deliver_template_type=".$deliver_template_type.",share_title='".$share_title."',share_desc='".$share_desc."',share_img='".$share_img."' where id=".$keyid;
 }else{
    $query = "insert into weixin_commonshop_order_qrset(markedWords,customer_id,isvalid,createtime,deliver_template_type,share_title,share_desc,share_img) values ('".$content."','".$customer_id."',true,now(),".$deliver_template_type.",'".$share_title."','".$share_desc."','".$share_img."')";
 }
 //echo $query;
_mysql_query($query) or die("L17 Query error : ".mysql_error());
mysql_close($link);
echo "<script>location.href='index.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>"
?>