<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
require('../../../../../weixinpl/common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');
ini_set('max_input_time', '200');
_mysql_query("SET NAMES UTF8");


$diy_tem_contid = $configutil->splash_new($_POST["diy_tem_contid"]);
$diy_temid = $configutil->splash_new($_POST["diy_temid"]);

$max_file_size=204800; //上传文件大小限制, 单位BYTE
$destination = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!is_uploaded_file($_FILES["upfile2"]["tmp_name"]))
	//是否存在文件
	{
	   if($diy_tem_contid<=0){
			echo "文件不存在！";
			exit;
		}else{
	       $destination = $_POST["default_imgurl"];
	   }
	}else{
		
		$file = $_FILES["upfile2"];
		
		if($max_file_size < $file["size"])
		//检查文件大小
		{
			echo "上传文件大小已超过200K！";
			exit;
		}
		
		require_once ROOT_DIR.'mp/lib/image.php';
		//http://admin.weisanyun.cn/resources/3243/personal_center/201709/15045204824161593531978.jpg
		$up_img=new \image();
		$destination=$up_img->upload_image($file,$customer_id,'personal_center');
	

  }
  
	$save_destination = "/resources/".$destination; 

	echo $save_destination;

}
$error =mysql_error();
mysql_close($link);
?>