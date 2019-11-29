<?php
 header("Content-type: text/html; charset=utf-8"); 
  require('../../../../weixinpl/config.php');

$uptypes=array('image/jpg', //上传文件类型列表
'image/jpeg',
'image/png',
'image/pjpeg',
'image/gif',
'image/bmp',
'image/x-png');
$max_file_size=1000000; //上传文件大小限制, 单位BYTE


$article_id = $_GET["article_id"];


$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
$destination_folder="up/install_article/"; //上传文件路径
if(!file_exists($destination_folder))
  mkdir($destination_folder,0777,true);
$destination_folder = $destination_folder.$customer_id."/";  
$destination_folder = "../../../".Base_Upload."Mode/instal/";  
//echo $destination_folder;return;
if(!file_exists($destination_folder))
  mkdir($destination_folder,0777,true);
//	$configutil->mkdirs($destination_folder);

//$watermark=1; //是否附加水印(1为加水印,0为不加水印);
//$watertype=1; //水印类型(1为文字,2为图片)
//$waterposition=2; //水印位置(1为左下角,2为右下角,3为左上角,4为右上角,5为居中);
//$waterstring="www.tt365.org"; //水印字符串
//$waterimg="xplore.gif"; //水印图片
$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
$imgpreviewsize=1/1; //缩略图比例
$destination = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!is_uploaded_file($_FILES["upfile"]["tmp_name"]))
	//是否存在文件
	{
	   if($article_id<=0){
		   echo "<font color='red'>文件不存在！</font>";
		   exit;
	   }else{
	       $destination = $_POST["icon"];
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
		  // echo "folder=====".$destination_folder;
		   mkdir($destination_folder);
		  
		 }

		  $filename=$file["tmp_name"];
		//echo "exists : ".mkdir($destination_folder)."<br/>";
		//echo "<br/>filename: ".$filename."<br/>";
			
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
		  if(!_move_uploaded_file ($filename, $destination))
		  {
		     echo $filename."<br/>";
			 echo $destination."<br/>";
			 echo "<font color='red'>移动文件出错！</a>";
			 exit;
		  }
  }
 $save_destination = str_replace("../","",$destination);
// $save_destination = "/weixinpl/".$save_destination;
 $save_destination = "/mshop/".$save_destination;
 echo "<script>location.href='iframe_images_articleicon.php?customer_id=".$customer_id."&icon=".$save_destination."';</script>";
 }
?>