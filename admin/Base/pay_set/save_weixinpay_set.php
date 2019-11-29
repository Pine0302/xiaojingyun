<?php
// header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$weixinpay_id=-1;
$weixinpay_id =$configutil->splash_new($_POST["weixinpay_id"]);//商城id


$version =$configutil->splash_new($_POST["version"]);//版本类型
$appid =$configutil->splash_new($_POST["appid"]);//APPID
$appsecret =$configutil->splash_new($_POST["appsecret"]);//APPSeceret
$paysignkey =$configutil->splash_new($_POST["paysignkey"]);
$partnerid =$configutil->splash_new($_POST["partnerid"]);
$partnerkey =$configutil->splash_new($_POST["partnerkey"]);
$sub_mch_id =$configutil->splash_new($_POST["sub_mch_id"]);
$fee_type =$configutil->splash_new($_POST["fee_type"]);

$uptypes=array('﻿application/octet-stream','﻿application/octet-stream');
$max_file_size=1000000; //上传文件大小限制, 单位BYTE
$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
$destination_folder="../../../".Base_Upload."Base/pay_set"; //上传文件路径
//echo $destination_folder;
//$watermark=1; //是否附加水印(1为加水印,0为不加水印);
//$watertype=1; //水印类型(1为文字,2为图片)
//$waterposition=2; //水印位置(1为左下角,2为右下角,3为左上角,4为右上角,5为居中);
//$waterstring="www.tt365.org"; //水印字符串
//$waterimg="xplore.gif"; //水印图片


$destination_cert = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!is_uploaded_file($_FILES["apiclient_cert_path"]["tmp_name"]))
	//是否存在文件
	{
	   $destination_cert = $_POST["apiclient_cert_path_v"];
	}else{
		$file = $_FILES["apiclient_cert_path"];
		if($max_file_size < $file["size"])
		//检查文件大小
		{
			echo "<font color='red'>文件太大！</font>";
			exit;
		}
		$filetype =  $file["type"];
		if($filetype!="﻿application/octet-stream")
		{
		  //echo "<font color='red'>不能上传此类型文件！</font>";
		 // exit;
		}
		
		if(!file_exists($destination_folder))
		   mkdir($destination_folder,0777,true);
		
		$destination_folder = $destination_folder."/";
		
		if(!file_exists($destination_folder))
		   mkdir($destination_folder,0777,true);
		  $filename=$file["tmp_name"];
		  $pinfo=pathinfo($file["name"]);
		  $ftype=$pinfo["extension"];
		  if($ftype!="pem"){
		      echo "<font color='red'>不能上传此类型文件！</font>";
		      exit;
		  }
		  $destination_cert = $destination_folder.time().".".$ftype;
		  $overwrite=true;
		  if (file_exists($destination_cert) && $overwrite != true)
		  {
			 echo "<font color='red'>同名文件已经存在了！</a>";
			 exit;
		   }
		  if(!_move_uploaded_file ($filename, $destination_cert))
		  {
			 echo "<font color='red'>移动文件出错！</a>";
			 exit;
		  }
		  //$pinfo=pathinfo($destination_cert);
		  
		  //$fname=$pinfo["basename"];
		  $destination_cert = str_replace("../","",$destination_cert);
		  $destination_cert = "/weixinpl/".$destination_cert;
	}
}


$destination_key = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!is_uploaded_file($_FILES["apiclient_key_path"]["tmp_name"]))
	//是否存在文件
	{
	   $destination_key = $_POST["apiclient_key_path_v"];
	}else{
		$file = $_FILES["apiclient_key_path"];
		if($max_file_size < $file["size"])
		//检查文件大小
		{
			echo "<font color='red'>文件太大！</font>";
			exit;
		}
		$filetype =  $file["type"];
		if($filetype!="﻿application/octet-stream")
		{
		  //echo "<font color='red'>不能上传此类型文件！</font>";
		 // exit;
		}
		
		if(!file_exists($destination_folder))
		   mkdir($destination_folder,0777,true);
		
		$destination_folder = $destination_folder.$customer_id."/";
		
		if(!file_exists($destination_folder))
		   mkdir($destination_folder,0777,true);  
		  $filename=$file["tmp_name"];
		  $pinfo=pathinfo($file["name"]);
		  $ftype=$pinfo["extension"];
		  if($ftype!="pem"){
		      echo "<font color='red'>不能上传此类型文件！</font>";
		      exit;
		  }
		  $destination_key = $destination_folder.time().".".$ftype;
		  $overwrite=true;
		  if (file_exists($destination_key) && $overwrite != true)
		  {
			 echo "<font color='red'>同名文件已经存在了！</a>";
			 exit;
		   }
		  if(!_move_uploaded_file ($filename, $destination_key))
		  {
			 echo "<font color='red'>移动文件出错！</a>";
			 exit;
		  }
		  //$pinfo=pathinfo($destination_cert);
		  
		  //$fname=$pinfo["basename"];
		  $destination_key = str_replace("../","",$destination_key);
		  $destination_key = "/weixinpl/".$destination_key;
  }

}




if($weixinpay_id>0){ 
	$sql="update weixinpays set apiclient_cert_path='".$destination_cert."',apiclient_key_path='".$destination_key."',version=".$version.",appid='".$appid."',appsecret='".$appsecret."',paysignkey='".$paysignkey."',partnerid='".$partnerid."',partnerkey='".$partnerkey."',sub_mch_id='".$sub_mch_id."',fee_type='".$fee_type."' where customer_id=".$customer_id;
}else{
	$sql="insert into weixinpays(version,appid,appsecret,paysignkey,partnerid,partnerkey,customer_id,isvalid,createtime,apiclient_cert_path,apiclient_key_path,sub_mch_id,fee_type) values(".$version.",'".$appid."','".$appsecret."','".$paysignkey."','".$partnerid."','".$partnerkey."',".$customer_id.",true,now(),'".$destination_cert."','".$destination_key."','".$sub_mch_id."','".$fee_type."')";
}
//echo $sql."==<br/>="; 
$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());


$error =mysql_error();

mysql_close($link);
echo "<script>location.href='weixinpay_set.php?customer_id=".$customer_id_en."';</script>"
?>