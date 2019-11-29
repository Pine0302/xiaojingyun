<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");

$destination = "";
$temid =$configutil->splash_new($_POST["temid"]);

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!is_uploaded_file($_FILES["upfile"]["tmp_name"]))
	//是否存在文件
	{
		if($temid<=0){
			echo "<font color='red'>文件不存在！</font>";
			exit;
		}else{
			$destination = $_POST["default_img"];
		}
	}else{
		$file = $_FILES["upfile"];
		require_once ROOT_DIR.'mp/lib/image.php';
		//http://admin.weisanyun.cn/resources/000/override/201709/15045204824161593531978.jpg
		$up_img=new \image();
		$destination=$up_img->upload_image($file,$customer_id,'personal_center');
	}

	$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');

	$save_destination = "/resources/".$destination; 
	
	$save_defaultimg="update ".WSY_SHOP.".personal_center_diy_template set default_img='".$save_destination."' where id='".$temid."' and  customer_id='".$customer_id."' and isvalid=true ";
	$result_save_defaultimg=_mysql_query($save_defaultimg) or die ('save_defaultimg faild' .mysql_error());
	$error =mysql_error();  
	mysql_close($link);  
	echo "<script>location.href='default_img.php?customer_id=".$customer_id_en."&default_img=".$save_destination."&temid=".$temid."';</script>";
}
?>