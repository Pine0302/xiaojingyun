<?php 
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

	$material_id = -1;	
	if($_GET['material_id']){
		$material_id = $configutil->splash_new($_GET['material_id']);
	}
	/*图片上传*/	
	 $uptypes=array('image/jpg', //上传文件类型列表
	'image/jpeg',
	'image/png',
	'image/pjpeg',
	'image/gif',
	'image/bmp',
	'image/x-png');
	$max_file_size=1000000; //上传文件大小限制, 单位BYTE
	$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
	$destination_folder="../../../../../weixinpl/".Base_Upload."base/"; //上传文件路径

	$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
	$imgpreviewsize=1/1; //缩略图比例
	$website_default= "http://".CLIENT_HOST."/weixin/plat/app/html/";

	 if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if (!is_uploaded_file($_FILES["upfile"]["tmp_name"]))	//判断是否上传文件，是则不上传文件，使用旧文件
		{	
		  $destination = $configutil->splash_new($_POST['imgurl']);
		  // echo  $destination;
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
			if(!file_exists($destination_folder))
			   mkdir($destination_folder,0777,true);

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
			  if(!_move_uploaded_file ($filename, $destination))
			  {
				 echo "<font color='red'>移动文件出错！</a>";
				 exit;
			  }
		}
	}  
	/*图片上传end*/	
	

$sql = "update weixin_commonshop_material set pc_logo='".$destination."' where id=".$material_id." and isvalid=true ";
_mysql_query($sql)or die('L79 Query failed: ' . mysql_error());

//处理客服设置----start--2016-12-09-qiao

$is_open 		= 0;//是否开启客服
$choose_type 	= -1;//客服类型
$qq_link 		= "";//qq号码
$custom_link 	= "";//自定义链接
$bear_link 		= "";//小熊链接

if(!empty($_POST['need_online'])){
	$is_open = $configutil->splash_new($_POST['need_online']);
}
if(!empty($_POST['online_type'])){
	$choose_type = $configutil->splash_new($_POST['online_type']);
}
if(!empty($_POST['qq_link'])){
	$qq_link = $configutil->splash_new($_POST['qq_link']);
}
if(!empty($_POST['custom_link'])){
	$custom_link = $configutil->splash_new($_POST['custom_link']);
}
if(!empty($_POST['bear_link'])){
	$bear_link = $configutil->splash_new($_POST['bear_link']);
}
//echo 'is_open='.$is_open.";/choose_type=".$choose_type.";/qq_link=".$qq_link.";/custom_link=".$custom_link.";/bear_link=".$bear_link;
$id = -1;
$query = "SELECT id FROM customer_server_center WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
$result= _mysql_query($query)or die('L105 Query failed: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$id = $row->id;
}
if( $id < 0 ){
	$sql = "INSERT INTO customer_server_center(isvalid,customer_id,is_open,choose_type,qq_link,custom_link,bear_link) VALUES(true,$customer_id,$is_open,$choose_type,'$qq_link','$custom_link','$bear_link')";
}else{
	$sql = "UPDATE customer_server_center SET is_open=$is_open,choose_type=$choose_type,qq_link='$qq_link',custom_link='$custom_link',bear_link='$bear_link'";
}
_mysql_query($sql)or die('L115 Query failed: ' . mysql_error());






 echo "<script>location.href='base.php?customer_id=".$customer_id_en."';</script>"
?>