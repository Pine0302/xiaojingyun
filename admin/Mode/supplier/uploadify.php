<?php
/*
Uploadify 后台处理 Demo
Author:wind
Date:2013-1-4
uploadify 后台处理！
*/
require('../../../../weixinpl/config.php');


//连接数据库
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

//设置上传目录
$path = "../uploads/";	

if (!empty($_FILES)) {
	
	//得到上传的临时文件流
	$tempFile = $_FILES['Filedata']['tmp_name'];
	
	//允许的文件后缀
	$fileTypes = array('jpg','jpeg','gif','png'); 
	
	//得到文件原名
	$fileName = iconv("UTF-8","GB2312",$_FILES["Filedata"]["name"]);
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	//接受动态传值
	$files=$_POST['typeCode'];
	
	//最后保存服务器地址
	if(!is_dir($path))
	   mkdir($path);

	$sql_count = "select count(*) as count from weixin_commonshop_supply_album where isvalid=true and supply_id=-1 and customer_id=".$customer_id;
	$result_count = _mysql_query($sql_count);
	while($row = mysql_fetch_object($result_count)){
		$count = $row->count;
	}

	if($count<5){
		if (_move_uploaded_file($tempFile, $path.$fileName)){

		$createtime = date('Y-m-d H:i:s',time());

		//插入数据库
		$sql="insert into
	    weixin_commonshop_supply_album (customer_id,supply_id,picture,isvalid,createtime) values (".$customer_id.",-1,'".$path.$fileName."',true,'".$createtime."')";

	    _mysql_query($sql);
		
		$error =mysql_error();		
		echo $fileName."上传成功！";
		//echo $path.$fileName;

		}else{
			echo $fileName."上传失败！";
		}
	}else{
		echo "最多只能上传5张照片";
	}
	
}

?>