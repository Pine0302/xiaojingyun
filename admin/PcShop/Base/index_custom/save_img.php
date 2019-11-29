<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
require('../../../../../weixinpl/common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');
$uptypes=array('image/jpg', //上传文件类型列表
'image/jpeg',
'image/png',
'image/pjpeg',
'image/gif',
'image/bmp',
'image/x-png');
$max_file_size=1000000; //上传文件大小限制, 单位BYTE
_mysql_query("SET NAMES UTF8");

//$product_id = $configutil->splash_new($_GET["product_id"]);

$diy_tem_contid = $configutil->splash_new($_POST["diy_tem_contid"]);
$diy_temid = $configutil->splash_new($_POST["diy_temid"]);
$img_sort = $configutil->splash_new($_POST["img_sort"]);
$destination_folder = "../../../../".Base_Upload."Custom/";  
if(!file_exists($destination_folder)){
	mkdir($destination_folder,0777,true);
}

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
	if (!is_uploaded_file($_FILES["upfile2"]["tmp_name"]))
	//是否存在文件
	{
	   /*if($product_id<=0){
		   echo "<font color='red'>文件不存在！</font>";
		   exit;
	   }else{*/
	       $destination = $_POST["default_imgurl"];
	  /* }*/
	}else{
		$file = $_FILES["upfile2"];
		if($max_file_size < $file["size"])
		//检查文件大小
		{
			echo "<font>文件太大！</font>";
			exit;
		}
		if(!in_array($file["type"], $uptypes))
		//检查文件类型
		{
		  echo "<font>不能上传此类型文件！</font>";
		  exit;
		}
		if(!file_exists($destination_folder)){
		  // echo "folder=====".$destination_folder;
		  mkdir($destination_folder,0777,true);
		  
		 }

		  $filename=$file["tmp_name"];

		  $image_size = getimagesize($filename);

		  $pinfo=pathinfo($file["name"]);

		  $ftype=$pinfo["extension"];
		  $destination = $destination_folder.time().".".$ftype;
		  $overwrite=true;
		  if (file_exists($destination) && $overwrite != true)
		  {
			 echo "<font>同名文件已经存在了！</a>";
			 exit;
		   }
		  if(!_move_uploaded_file ($filename, $destination))
		  {
		     echo $filename."<br/>";
			 echo $destination."<br/>";
			 echo "<font>移动文件出错！</a>";
			 exit;
		  }
		  $pinfo=pathinfo($destination);
		  $fname=$pinfo["basename"];
	

  }
  
	
 
 $new_baseurl = Protocol.$http_host; 
 $save_destination = str_replace("../","",$destination);
 $save_destination = "/mshop/".$save_destination;
// $save_destination = "/weixinpl/".$save_destination;

 
 /*$imgurl="";
 $imgurl_query="select imgurl from weixin_commonshop_diy_template_content where isvalid=true and diy_temid=".$diy_temid." and diy_tem_contid=".$diy_tem_contid." and customer_id='".$customer_id."'";
 $result_imgurl_query=_mysql_query($imgurl_query) or die ('imgurl_query faild' .mysql_error());
 while($row=mysql_fetch_object($result_imgurl_query)){
	 $imgurl=$row->imgurl;
	
 }*/
// $imgarr=explode("|",$imgurl);
 //$imgarr[$img_sort]=$save_destination;
 /*$len=sizeof($imgarr);
 for ($i=0;$i<$len;$i++){
	//$imgurl1=$imgurl1."|".$imgarr[$i];
	//echo $a+=$imgarr[$i]."|";
	print_r($imgarr[$i]."|");
	implode
	 
 }*/
 // $imgurl=implode("|",$imgarr);
// print_r($imgurl1);
 //echo $a;
// echo "==========";
 //$imgurl=implode($imgarr);
// print_r($imgarr);
 //echo $imgurl;
 //echo $img_sort;
 /*$save_img="update weixin_commonshop_diy_template_content set imgurl='".$imgurl."' where isvalid=true and  diy_temid=".$diy_temid." and diy_tem_contid=".$diy_tem_contid." and customer_id='".$customer_id."'";
 $result_save_img=_mysql_query($save_img) or die ('save_img faild' .mysql_error());
 */
 //echo $parent_id;
 $imgurl_index=$new_baseurl.$save_destination; //给JS的图片地址
 echo $imgurl_index;
 //echo $diy_temid."=====".$diy_tem_contid."===".$customer_id;
 }
 $error =mysql_error();
 mysql_close($link);
?>