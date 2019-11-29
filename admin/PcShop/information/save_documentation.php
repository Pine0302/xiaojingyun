<?php 
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");


$destination = array();
for($i=1;$i<=6;$i++){

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
	$destination_folder="../../../../weixinpl/".Base_Upload."merchants_settled/".$customer_id.'/'; 									//上传文件路径

	$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
	$imgpreviewsize=1/1; //缩略图比例
	$website_default= "http://".CLIENT_HOST."/weixin/plat/app/html/";

	 if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if (!is_uploaded_file($_FILES["upfile".$i]["tmp_name"]))	//判断是否上传文件，是则不上传文件，使用旧文件
		{	
		  $destination[$i] = $configutil->splash_new($_POST['imgurl'.$i]);
		  // echo  $destination;
		}else{
			$file = $_FILES["upfile".$i];
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
			  $destination[$i] = $destination_folder.time().$i.".".$ftype;
			  $overwrite=true;
			  if (file_exists($destination[$i]) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</a>";
				 exit;
			   }
			  if(!_move_uploaded_file ($filename, $destination[$i]))
			  {
				 echo "<font color='red'>移动文件出错！</a>";
				 exit;
			  }
		}
	}  
	/*图片上传end*/	

}

$left_img  = $destination[1];//左侧广告图片
$destination = array_splice($destination,1);	

$right_img = '';//右侧轮播广告图
$pro_str   = '';//相关产品ID

foreach($destination as $key => $value){
	if(!empty($value)){
		$right_img .= $value.'|';
		
	}	
}

for($n=2;$n<=6;$n++){
	if(!empty($_POST['detail_id'.$n])){
		$parent_id =  $configutil->splash_new($_POST['foreign_id'.$n]);
		$parent_id = explode("_",$parent_id);
		$pro_id =  $configutil->splash_new($_POST['detail_id'.$n]);
		$pro_str .= $parent_id[0].'_'.$pro_id.'|';
	}
}

$right_img = rtrim($right_img,'|');	//右侧轮播广告图
$pro_str = rtrim($pro_str,'|');	//相关产品ID

$keyid = -1;
$query = "select id as keyid from pcshop_merchants_settled_img where isvalid=true and customer_id=".$customer_id."";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$keyid = $row->keyid;
}
if($keyid>0){//存在数据，更新表
	$sql = "update pcshop_merchants_settled_img set
									pro_id='".$pro_str."',
									left_img='".$left_img."',
									right_img='".$right_img."'
			where isvalid=true and customer_id=".$customer_id.";
									";
}else{//不存在数据，插入表
	$sql = "insert into pcshop_merchants_settled_img(left_img,right_img,pro_id,customer_id,isvalid,createtime) values('".$left_img."','".$right_img."','".$pro_str."',".$customer_id.",true,now())";
}	

$result = _mysql_query($sql) or die('Sql failed: ' . mysql_error());
 echo "<script>location.href='documentation.php?customer_id=".$customer_id_en."';</script>"
?>