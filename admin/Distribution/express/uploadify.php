<?php
/*
Uploadify 后台处理 Demo
Author:wind
Date:2013-1-4
uploadify 后台处理！
*/
  header("Content-type: text/html; charset=utf-8"); 
  require('../../../../weixinpl/config.php');
  //$customer_id = passport_decrypt($customer_id);
  //require('../../../../weixinpl/back_newshops/Distribution/back_init.php');  
  $customer_id = $_GET["customer_id"];
  //$photo_id = $_GET["photo_id"];
  //$link =    mysql_connect(DB_HOST,DB_USER, DB_PWD);
  //mysql_select_db(DB_NAME) or die('Could not select database');


//设置上传目录

$path = "../../../../weixinpl/up/delivery/".$customer_id."/"; //上传文件存放路径
$sqlpath = "../../../../weixinpl/up/delivery/".$customer_id."/"; //上传文件数据库路径

if (!empty($_FILES)) {
	
	//得到上传的临时文件流
	$tempFile = $_FILES['Filedata']['tmp_name'];
	
	//允许的文件后缀
	$fileTypes = array('jpg','jpeg','gif','png'); 
	
	//得到文件原名
	$fileName = iconv("UTF-8","GB2312",$_FILES["Filedata"]["name"]);
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	$a=$_FILES["Filedata"]["name"];

	//得到文件名字与类型名
	$pinfo = pathinfo($fileName);
	$ftype = $pinfo["extension"];
	$picname = time().rand(100,999);
	$destination = $path.$picname.".".$ftype;//上传文件存放路径
	$destination2 = $sqlpath.$picname.".".$ftype;//上传文件数据库路径	
	
	//接受动态传值
	//$files=$_POST['typeCode'];
	
	//最后保存服务器地址
	if(!is_dir($path))
	    mkdir($path);
	if (_move_uploaded_file($tempFile, $destination)){
		$ary_result = array('webpath'=>$destination2,);
		echo json_encode($ary_result);		
		//echo $fileName."上传成功！";
	}else{
		echo $fileName."上传失败！";
        
        
	}
}
//mysql_close($link);
?>