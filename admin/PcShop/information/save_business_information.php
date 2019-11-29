<?php 
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$company_name       = $_POST['company_name'];//公司名称
$company_management = $_POST['company_management'];//公司经营
$corporation_name   = $_POST['corporation_name'];//法人姓名
$name               = $_POST['name'];//联系人姓名
$phone              = $_POST['phone'];//联系人电话
$email              = $_POST['email'];//联系人电子邮箱
$business_licence   = $_POST['business_licence'];//营业执照
$identity           = $_POST['identity'];//身份证号码
$company_describe   = $_POST['company_describe'];//公司描述
$business_id        = $_POST['business_id'];//ID

$destination = array();
for($i=1;$i<=3;$i++){

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
	$destination_folder="../../../up/merchants_settled/".$customer_id.'/'; 									//上传文件路径

	$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
	$imgpreviewsize=1/1; //缩略图比例
	$website_default= "http://".CLIENT_HOST."/weixin/plat/app/html/";

	 if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if (!is_uploaded_file($_FILES["upfile".$i]["tmp_name"]))	//判断是否上传文件，是则不上传文件，使用旧文件
		{	
		  $destination[$i] = $_POST['imgurl'.$i];
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
			  $destination[$i] = $destination_folder.time().".".$ftype;
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


$business_licence_img  = $destination[1];//营业执照图片
$identityimgt  = $destination[2];//身份证正面
$identityimgf  = $destination[3];//身份证反面

$sql = "update pcshop_merchants_settled_member set
								name='".$name."',
								phone='".$phone."',
								email='".$email."',
								company_name='".$company_name."',
								company_management='".$company_management."',
								company_describe='".$company_describe."',
								business_licence_img='".$business_licence_img."',
								business_licence='".$business_licence."',
								corporation_name='".$corporation_name."',
								identity='".$identity."',
								identityimgt='".$identityimgt."',
								identityimgf='".$identityimgf."'
		where isvalid=true and customer_id=".$customer_id." and id=".$business_id;
$result = _mysql_query($sql) or die('Sql failed: ' . mysql_error());
 echo "<script>location.href='business_information.php?customer_id=".$customer_id_en."';</script>"
?>