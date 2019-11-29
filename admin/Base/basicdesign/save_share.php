<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$shop_id=-1;
$shop_id =$configutil->splash_new($_POST["shop_id"]);
$is_dis_model = $configutil->splash_new($_POST["is_dis_model"]);//是否保存过分销模式
if(empty($is_dis_model)){ 
	   $is_dis_model =-1; 
}
// echo "<pre>";
// var_dump($_POST);die; 
$type = $configutil->splash_new($_GET["type"]);//商城||城市商圈判断
$gz_url =$configutil->splash_new($_POST["gz_url"]);//引导关注链接
$distr_type =$configutil->splash_new($_POST["distr_type"]);//会员锁定关系模式
$is_showshare_info =$configutil->splash_new($_POST["is_showshare_info"]);//分享链接是否显示分享者
$per_share_score =$configutil->splash_new($_POST["per_share_score"]);//每推广增加一名粉丝,奖励的积分分享图背景0默认1自

$define_share_image_flag =$configutil->splash_new($_POST["define_share_image_flag"]);//产品定义
$is_nav = 0;	//商城导航条
$logo = '';	//商城LOGO
$query = "SELECT is_nav,logo FROM weixin_commonshops_extend WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
$result= _mysql_query($query) or die('Query failed 64: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$is_nav = $row->is_nav;
	$logo = $row->logo;
}
$define_share_image=$logo;//产品分享图背景

if($define_share_image_flag==1){
	if(!empty($_FILES['new_define_share_image']['name'])){
		$rand1=rand(0,9);
		$rand2=rand(0,9);
		$rand3=rand(0,9);
		$filename=date("Ymdhis").$rand1.$rand2.$rand3;
		$filetype=substr($_FILES['new_define_share_image']['name'], strrpos($_FILES['new_define_share_image']['name'], "."),strlen($_FILES['new_define_share_image']['name'])-strrpos($_FILES['new_define_share_image']['name'], "."));
		$filetype=strtolower($filetype);
		if(($filetype!='.jpg')&&($filetype!='.png')&&($filetype!='.gif')){
				echo "<script>alert('文件类型或地址错误');</script>";
				echo "<script>history.back(-1);</script>";
				exit ;
			}
		$filename=$filename.$filetype;
		$savedir='../../../../weixinpl/'.Base_Upload.'Base/basicdesign/';
		if(!is_dir($savedir)){
			mkdir($savedir,0777,true);
		}
		$savefile=$savedir.$filename;
		if (!_move_uploaded_file($_FILES['new_define_share_image']['tmp_name'], $savefile)){
			echo "<script>history.back(-1);</script>";
			exit;
		}
	$define_share_image='weixinpl/'.Base_Upload.'Base/basicdesign/'.$filename;	
	}else{
		$define_share_image=$configutil->splash_new($_POST['define_share_image']);
		//echo $define_share_image."--";die;
	} 
// echo $define_share_image."=0";
// $define_share_image=$savefile;
// echo $define_share_image."=1";
// $define_share_image = str_replace("../","",$define_share_image);
// echo $define_share_image."=2"; 
// $define_share_image = "/weixinpl/".$define_share_image;
// echo $define_share_image."=3";die;
}
if($shop_id>0){
	$query = "select distr_type from weixin_commonshops where isvalid=true and id=".$shop_id." and customer_id=".$customer_id; 
	$result = _mysql_query($query);
	while($row = mysql_fetch_object($result)){
		$orgin_distr_type = $row->distr_type;
	}
	if($orgin_distr_type!=$distr_type){    //若修改关系锁定模式，则插入修改日志
		$remark = "通过商家后台修改";
		$query= "insert into weixin_commonshop_lockmode_change_logs(customer_id,orgin_mode,change_mode,isvalid,createtime,remark) values(".$customer_id.",".$orgin_distr_type.",".$distr_type.",true,now(),'".$remark."')";
		_mysql_query($query);
	}
	$sql="update weixin_commonshops set is_dis_model=".$is_dis_model.",gz_url='".$gz_url."',distr_type=".$distr_type.",is_showshare_info=".$is_showshare_info.",per_share_score=".$per_share_score.",define_share_image='".$define_share_image."' where isvalid=true and id=".$shop_id." and customer_id=".$customer_id; 
}
//echo $sql;die;
$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());
$error =mysql_error();
mysql_close($link);
echo "<script>location.href='share.php?customer_id=".$customer_id_en."&type=".$type."';</script>"
?>