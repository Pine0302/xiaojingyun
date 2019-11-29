<?php

header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
//echo "0000";return;
$isOpenPublicWelfare = 0;
if(!empty($_POST["isOpenPublicWelfare"])){
	$isOpenPublicWelfare = $configutil->splash_new($_POST["isOpenPublicWelfare"]); 
}
$valuepercent = 0;
if(!empty($_POST["valuepercent"])){
	$valuepercent = $configutil->splash_new($_POST["valuepercent"]); //公益基金分配率
}
$welfare_images = "";
	if(!empty($_FILES['new_welfare_images']['name'])){
		$rand1=rand(0,9);
		$rand2=rand(0,9);
		$rand3=rand(0,9);
		$filename=date("Ymdhis").$rand1.$rand2.$rand3;
		$filetype=substr($_FILES['new_welfare_images']['name'], strrpos($_FILES['new_welfare_images']['name'], "."),strlen($_FILES['new_welfare_images']['name'])-strrpos($_FILES['new_welfare_images']['name'], "."));
		$filetype=strtolower($filetype);
		if(($filetype!='.jpg')&&($filetype!='.png')&&($filetype!='.gif')){
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
		if (!_move_uploaded_file($_FILES['new_welfare_images']['tmp_name'], $savefile)){
			echo "<script>history.back(-1);</script>";
			exit;
		}
		$welfare_images=$savefile;
		$welfare_images = str_replace("../","",$welfare_images);
//		$welfare_images = "/weixinpl/".$welfare_images;
		$welfare_images = "/mshop/".$welfare_images;
	}else{
	$welfare_images=$configutil->splash_new($_POST['welfare_images']);
	} 

	
if($customer_id>0){
	$sql="update weixin_commonshops set isOpenPublicWelfare=".$isOpenPublicWelfare." where customer_id=".$customer_id;
	//echo $sql."<br/>";
	_mysql_query($sql);
			
}else{
	$sql = "insert into weixin_commonshops(isOpenPublicWelfare) values (".$isOpenPublicWelfare.")";
	 //echo $sql."<br/>";
	_mysql_query($sql);
 }

	
	 $query="select id from weixin_commonshop_publicwelfare where isvalid=true and customer_id=".$customer_id;
	 $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	 $PublicWelfare_id=-1;
	  while ($row = mysql_fetch_object($result)) {
	     $PublicWelfare_id= $row->id;
	  }
	  if($PublicWelfare_id>0){
		  
		  $sql="update weixin_commonshop_publicwelfare set valuepercent=".$valuepercent.",backimg='".$welfare_images."' where id=".$PublicWelfare_id;
		  _mysql_query($sql);
	  }else{
		  $sql="insert into weixin_commonshop_publicwelfare(customer_id,valuepercent,backimg,isvalid,createtime) values(".$customer_id.",".$valuepercent.",'".$welfare_images."',true,now())";
		  _mysql_query($sql);
	  }
//echo $sql;return;
 $error =mysql_error();
 mysql_close($link);
	echo $error; 
 echo "<script>location.href='welfare.php?customer_id=".$customer_id_en."';</script>"	
?>