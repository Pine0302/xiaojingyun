<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/common/common_ext.php'); 
require('../../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");
 
$keyid     = i2post("keyid",-1);
$name      = i2post("name","");
$page_url  = i2post("page_url","");
$column_id = i2post("column_id",-1);
$pagenum   = i2post("pagenum",1);
$selector_id   = i2post('selector_id',-1);
if($selector_id != "-1" && $selector_id != ""){
    include_once('../../../../../mshop/admin/Base/personalization/home_decoration/pink_selector_url.php');
    $url = pink_selector_url($selector_id,$protocol_http_host,$customer_id,$customer_id_en,$user_id);
}
$uptypes=array('image/jpg', //上传文件类型列表
'image/jpeg',
'image/png',
'image/pjpeg',
'image/gif',
'image/bmp',
'image/x-png');	
$max_file1_size=1000000; //上传文件大小限制, 单位BYTE
$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
$destination1_folder='../../../../'.Base_Upload.'Base/personalization/bottom_label/images/'; //上传文件路径

$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
$imgpreviewsize=1/1; //缩略图比例

$destination1 = "";
$destination2 = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!is_uploaded_file($_FILES["upfile1"]["tmp_name"]) && !is_uploaded_file($_FILES["upfile2"]["tmp_name"]))
	//是否存在文件
	{
	   if($keyid<=0){
		   //echo "<font color='red'>文件不存在！</font>";
		   //exit;
		   $destination1 = i2post("icon_url","");
		   $destination2 = i2post("icon_url_selected","");
	   }else{
	       $destination1 = i2post("icon_url","");
		   $destination2 = i2post("icon_url_selected","");
	   }
	   
	   $save_destination1 = $destination1;
	   $save_destination2 = $destination2;
	}else{
		$file1 = $_FILES["upfile1"];
		$file2 = $_FILES["upfile2"];
		if($max_file1_size < $file1["size"] || $max_file1_size < $file2["size"])
		//检查文件大小
		{
			//echo "<font color='red'>文件太大！</font
			if($keyid >0){
				echo "<script>alert('文件太大');location.href='./label_edit.php?keyid=".$keyid."&customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}else{
				echo "<script>alert('文件太大');location.href='./label_edit.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}			
			exit;
		}
		if((!in_array($file1["type"], $uptypes) && !empty($file1["type"])) || (!in_array($file2["type"], $uptypes) && !empty($file2["type"])))
		//检查文件类型
		{		  
		  if($keyid >0){
				echo "<script>alert('不能上传此类型文件！');location.href='./label_edit.php?keyid=".$keyid."&customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}else{
				echo "<script>alert('不能上传此类型文件！');location.href='./label_edit.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}				  		 
		  exit;
		}
		if(!file_exists($destination1_folder))
		  mkdir($destination1_folder,0777,true);
	  
		  $file1name=$file1["tmp_name"];
          $file2name=$file2["tmp_name"];
		  
		  $image_size = getimagesize($file1name);

		  $pinfo  = pathinfo($file1["name"]);
          $pinfo2 = pathinfo($file2["name"]);
		  
		  $ftype  = $pinfo["extension"];
		  $ftype2 = $pinfo2["extension"];
		  
		  $destination1 = $destination1_folder.time().".".$ftype;
		  $destination2 = $destination1_folder.time()."1.".$ftype2;
		  
		  $overwrite=true;
		  if ((file_exists($destination1) || file_exists($destination2)) && $overwrite != true)
		  {			 
			if($keyid >0){
				echo "<script>alert('同名文件已经存在了！');location.href='./label_edit.php?keyid=".$keyid."&customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}else{
				echo "<script>alert('同名文件已经存在了！');location.href='./label_edit.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}	 	
			 exit;
		   }
		  if((!_move_uploaded_file ($file1name, $destination1) && !empty($file1name))|| (!_move_uploaded_file ($file2name, $destination2) && !empty($file2name)))
		  {
			if($keyid >0){
				echo "<script>alert('移动文件出错！');location.href='./label_edit.php?keyid=".$keyid."&customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}else{
				echo "<script>alert('移动文件出错！');location.href='./label_edit.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}	
			 exit;
		  }
		  
		$save_destination1 = str_replace("../","",$destination1);
		// $save_destination1 = "/weixinpl/".$save_destination1;
		$save_destination1 = "/mshop/".$save_destination1;

		$save_destination2 = str_replace("../","",$destination2);
		// $save_destination2 = "/weixinpl/".$save_destination2;
		$save_destination2 = "/mshop/".$save_destination2;
		
		if(empty($file1['name']) && $file1['size'] <=0){
			$destination1 = i2post("icon_url","");
			$save_destination1 = $destination1;
		}
		if(empty($file2['name']) && $file2['size'] <=0){
			 $destination2 = i2post("icon_url_selected","");
			$save_destination2 = $destination2;
		}
	}
  }
if($selector_id && $url) $page_url = $url['url'];
	
 if($keyid>0){
    $sql="update bottom_label_setting_t set name='".$name."', icon_url='".$save_destination1 ."', icon_url_selected='".$save_destination2."',page_url='".$page_url."',column_id=".$column_id." ,selector_id='".$selector_id."' where id=".$keyid." and isvalid=true";
    _mysql_query($sql) or die('Query failed: ' . mysql_error());
 }else{
  $insert_sql = "insert into bottom_label_setting_t(customer_id,name,icon_url,icon_url_selected,page_url,column_id,display,isvalid,createtime,selector_id) 
          values(".$customer_id.",'".$name."','".$save_destination1."','".$save_destination2."','".$page_url."',".$column_id.",1,true,now(),'{$selector_id}')";
  $result = _mysql_query($insert_sql) or die('insert_sql failed: ' . mysql_error());
   
  /*新增数据成功时修改排序*/
  if($result){
	  $new_id = mysql_insert_id();
	  $update_sql = "update bottom_label_setting_t set `sort`=".$new_id." where id =".$new_id." and isvalid=true";
	  
	  _mysql_query($update_sql) or die('update_sql failed: ' . mysql_error());
  }
  
 }	
 mysql_close($link);
 
 if($pagenum>0){
	 echo "<script>location.href='./index.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
 }else{
	 echo "<script>location.href='./index.php?customer_id=".$customer_id_en."';</script>";
 }
 
?>