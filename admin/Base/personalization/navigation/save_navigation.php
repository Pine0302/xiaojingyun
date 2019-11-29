<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php');
require('../../../../../weixinpl/common/common_ext.php'); 
//导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
 
$navigation_id = i2post("navigation_id",-1);
$name          = i2post("name","");
$page_url      = i2post("page_url","");
$column_id     = i2post("column_id",-1);
$pagenum       = i2post("pagenum",1);
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
$max_file_size=51200; //上传文件大小限制, 单位BYTE
$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
$destination_folder='../../../../'.Base_Upload.'Base/personalization/navigation/'; //上传文件路径

$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
$imgpreviewsize=1/1; //缩略图比例

$destination = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (!is_uploaded_file($_FILES["upfile"]["tmp_name"]))
    //是否存在文件
    {
        if($navigation_id<=0){
            //echo "<font color='red'>文件不存在！</font>";
            //exit;
            $save_destination = i2post("icon_url","");
        }else{
            $save_destination = i2post("icon_url","");
           
        }
    }else{
        $file = $_FILES["upfile"];
        if($max_file_size < $file["size"])
        //检查文件大小
        {
			if($navigation_id >0){
				echo "<script>alert('文件太大！');location.href='./navigation_edit.php?navigation_id=".$navigation_id."&customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}else{
				echo "<script>alert('文件太大！');location.href='./navigation_edit.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}	
            exit;
        }
        if(!in_array($file["type"], $uptypes))
        //检查文件类型
        {
			if($navigation_id >0){
				echo "<script>alert('不能上传此类型文件！');location.href='./navigation_edit.php?navigation_id=".$navigation_id."&customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}else{
				echo "<script>alert('不能上传此类型文件！');location.href='./navigation_edit.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}	
				
            exit;
        }
        if(!file_exists($destination_folder)){
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
            if($navigation_id >0){
				echo "<script>alert('同名文件已经存在了！');location.href='./navigation_edit.php?navigation_id=".$navigation_id."&customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}else{
				echo "<script>alert('同名文件已经存在了！');location.href='./navigation_edit.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}	
						
            exit;
        }
        if(!_move_uploaded_file ($filename, $destination))
        {
            if($navigation_id >0){
				echo "<script>alert('移动文件出错！');location.href='./navigation_edit.php?navigation_id=".$navigation_id."&customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}else{
				echo "<script>alert('移动文件出错！');location.href='./navigation_edit.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
			}	
	
            exit;
        }
     
        $pinfo=pathinfo($destination);
            
          
        $save_destination = str_replace("../","",$destination);
        $save_destination = "/mshop/".$save_destination;
    }
  }

  if($selector_id && $url) $page_url = $url['url'];

 if($navigation_id>0){
    $sql="update navigation_setting_t set name='".$name."', icon_url='".$save_destination ."', page_url='".$page_url."',column_id=".$column_id." ,selector_id='".$selector_id."' where id=".$navigation_id;
    _mysql_query($sql) or die('SQL failed: ' . mysql_error());
 }else{
    $query = "select sort from navigation_setting_t where isvalid=true and customer_id=".$customer_id." order by sort desc limit 1";
    $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
    while ($row = mysql_fetch_object($result)) {
        $sort = $row->sort;
    }
    $new_sort = $sort+1;
    $sql="insert into navigation_setting_t(name,icon_url,page_url,column_id,isvalid,customer_id,sort,display,createtime,selector_id) values ('".$name."','".$save_destination."','".$page_url."',".$column_id.",true,".$customer_id.",".$new_sort.",false,now(),'{$selector_id}')";
    _mysql_query($sql) or die('SQL failed: ' . mysql_error());
 }
$error = mysql_error(); 
mysql_close($link);

if($pagenum > 0){
	echo "<script>location.href='setting.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
}else{
	echo "<script>location.href='setting.php?customer_id=".$customer_id_en."';</script>";
}


?>