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
$max_file_size_kb = 1000;
$max_file_size=bcmul($max_file_size_kb,1024,2); //上传文件大小限制, 单位BYTE

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
$supplier_id =$configutil->splash_new($_POST["supplier_id"]);
$background_img = '';
$query_img = "select background_img from weixin_commonshop_supply_diy_template where id='".$temid."' and  customer_id=".$customer_id." and  supplier_id=".$supplier_id." and isvalid=true";
$result_img = _mysql_query($query_img) or die('Query_img failed:'.mysql_error());
while( $row_img = mysql_fetch_object($result_img) ){
	$background_img = $row_img -> background_img;
}
$background_img_arr = explode('|',$background_img);
$result = array('error' => 0, 'message' => '');
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$imgfiles = $_FILES["upfile"]['name'];
	for( $i=0;$i<count($imgfiles);$i++ ){
		if (!is_uploaded_file($_FILES["upfile"]["tmp_name"][$i]))
		//是否存在文件
		{
			if($temid<=0){
				$result['error'] = 1;
				$result['message'] = '文件不存在！';
			}else{
				// $destination = $_POST["background_img"];
			}
		}else{
			$file = $_FILES["upfile"];
			if($max_file_size < $file["size"][$i])
			//检查文件大小
			{
				$result['error'] = 1;
				$result['message'] = "文件大小超过{$max_file_size_kb}kb,请选择合适的图片";
			}
			if(!in_array($file["type"][$i], $uptypes))
			//检查文件类型
			{
				$result['error'] = 1;
				$result['message'] = '不支持上传此类型文件';
			}
			if(!file_exists($destination_folder)){
			   mkdir($destination_folder);
			  
			}

			$filename=$file["tmp_name"][$i];
			$image_size = getimagesize($filename);
			$pinfo=pathinfo($file["name"][$i]);
			$ftype=$pinfo["extension"];
			$destination = $destination_folder.time().$i.".".$ftype;
			$overwrite=true;
			if (file_exists($destination) && $overwrite != true)
			{
				$result['error'] = 1;
				$result['message'] = '同名文件已经存在了！';
			}
			if(!_move_uploaded_file ($filename, $destination))
			{
				echo $filename."<br/>";
				echo $_FILES['userfile']['error']."<br/>";
				echo $destination."<br/>";
				echo "<font color='red'>移动文件出错！</a>";
				exit;  
			}
			$pinfo=pathinfo($destination);
			$fname=$pinfo["basename"];
			
			$destination 	  = str_replace("../","",$destination);
//			$save_destination = "/weixinpl/".$destination;
			$save_destination = "/mshop/".$destination;
			$background_img_arr[$i] = $save_destination;
		}
	}
	if(!$result['error']){
		$background_img = implode('|',$background_img_arr);
		$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
		mysql_select_db(DB_NAME) or die('Could not select database');

		// $save_destination = str_replace("../","",$destination);
		// $save_destination = "/weixinpl/".$save_destination; 

		$save_defaultimg="update weixin_commonshop_supply_diy_template set background_img='".$background_img."' where id='".$temid."' and  customer_id=".$customer_id." and  supplier_id=".$supplier_id." and isvalid=true ";
		$result_save_defaultimg=_mysql_query($save_defaultimg) or die ('save_background_img faild' .mysql_error());
		$error =mysql_error();  
		mysql_close($link);  
	}else{
		echo "<script>alert('{$result['message']}')</script>";
	}
	echo "<script>location.href='background_img.php?customer_id=".$customer_id_en."&background_img=".$background_img."&temid=".$temid."&supplier_id=".$supplier_id."';</script>";
}
?>