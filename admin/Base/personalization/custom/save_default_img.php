<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");
$uptypes=array('image/jpg', //上传文件类型列表
'image/jpeg',
'image/png',
'image/pjpeg',
'image/gif',
'image/bmp',
'image/x-png');
$max_file_size=1000000; //上传文件大小限制, 单位BYTE

$product_id = $configutil->splash_new($_GET["product_id"]);
$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
$destination_folder = "../../../../".Base_Upload."Custom/";
if(!file_exists($destination_folder)){
	mkdir($destination_folder,0777,true);
}

$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
$imgpreviewsize=1/1; //缩略图比例
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
		if($max_file_size < $file["size"])
		//检查文件大小
		{
			echo "<font color='red'>文件太大！</font>";
			exit;
		}
		if(!in_array($file["type"], $uptypes))
		//检查文件类型
		{
		  echo "<font color='red'>不能上传此类型文件！</font>";
		  exit;
		}
		if(!file_exists($destination_folder)){
		   mkdir($destination_folder);
		  
		}

		$filename=$file["tmp_name"];
		$image_size = getimagesize($filename);
		$pinfo=pathinfo($file["name"]);
		$ftype=$pinfo["extension"];
		$destination = $destination_folder.time().".".$ftype;
		$overwrite=true;
		if (file_exists($destination) && $overwrite != true)
		{
			echo "<font color='red'>同名文件已经存在了！</a>";
			exit;
		}
		// v827路由换了需要更改上传路径 屏蔽以前的写法
		// if(!_move_uploaded_file ($filename, $destination))
		// {
			// echo $filename."<br/>";
			// echo $_FILES['userfile']['error']."<br/>";
			// echo $destination."<br/>";
			// echo "<font color='red'>移动文件出错！</a>";
			// exit;  
		// }
		// $pinfo=pathinfo($destination);
		// $fname=$pinfo["basename"];
		
		// v827路由换了需要更改上传路径 改用新路径
		require_once ROOT_DIR.'mp/lib/image.php';
		//http://admin.weisanyun.cn/resources/3243/custom/201709/15045204824161593531978.jpg
		$up_img=new \image();
		$new_destination=$up_img->upload_image($file,$customer_id,'custom');

	}

	$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');

	// $save_destination = str_replace("../","",$destination);
	// $save_destination = "/weixinpl/".$save_destination;
	// $save_destination = "/mshop/".$save_destination;
	$save_destination = "/resources/".$new_destination;

	$save_defaultimg="update weixin_commonshop_diy_template set default_img='".$save_destination."' where id='".$temid."' and  customer_id='".$customer_id."' and isvalid=true ";
	$result_save_defaultimg=_mysql_query($save_defaultimg) or die ('save_defaultimg faild' .mysql_error());
	$error =mysql_error();  
	mysql_close($link);  
	echo "<script>location.href='default_img.php?customer_id=".$customer_id_en."&default_img=".$save_destination."&temid=".$temid."';</script>";
}
?>