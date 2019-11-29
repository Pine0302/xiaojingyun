<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); 
//导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');
$subscribe_id =-1;
$subscribe_id =$configutil->splash_new($_POST["subscribe_id"]);
$need_score =$configutil->splash_new($_POST["need_score"]);

$is_needmember =$configutil->splash_new($_POST["is_needmember"]);

$commonshop_subscribe_id = $configutil->splash_new($_POST["keyid"]);
$uptypes=array('image/jpg', //上传文件类型列表
'image/jpeg',
'image/png',
'image/pjpeg',
'image/gif',
'image/bmp',
'image/x-png');	
$max_file_size=1000000; //上传文件大小限制, 单位BYTE
$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
$destination_folder='../../../../'.Base_Upload.'Base/personalization/personal_center/'; //上传文件路径
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
	   if($commonshop_subscribe_id<=0){
		   //echo "<font color='red'>文件不存在！</font>";
		   //exit;
		   $destination = $configutil->splash_new($_POST["imgurl"]);
	   }else{
	       $destination = $configutil->splash_new($_POST["imgurl"]);
	   }
        $save_destination = $destination;
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
		 
		   $pinfo=pathinfo($destination);
			
		  $fname=$pinfo["basename"];	
	//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
	  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
		  //echo " 宽度:".$image_size[0];
		  //echo " 长度:".$image_size[1];
		  //添加到图片库	
		  $category_id=11; //不限行业
		  $mapsize_id =1; //360*200
		  $flag_id = 3; //优惠卷
		  
		$save_destination = str_replace("../","",$destination);
		// $save_destination = "/weixinpl/".$save_destination;

		$save_destination = "/mshop/".$save_destination;

		// var_dump($save_destination);die();
		 $query="insert into media_library_maps(category_id,mapsize_id,flag_id,owner_type,owner_id,imgurl,isvalid) values(".$category_id.",".$mapsize_id.",".$flag_id.",2,".$customer_id.",'".$save_destination."',true)";
		  _mysql_query($query);
		  $error = mysql_error();
		  echo $error;
	}
  }

 if($commonshop_subscribe_id>0){
    $sql="update weixin_commonshop_subscribes set is_needmember=".$is_needmember.", imgurl='".$save_destination ."', subscribe_id=".$subscribe_id.",need_score=".$need_score." where id=".$commonshop_subscribe_id;
	//echo $sql;
    _mysql_query($sql);
 }else{
    _mysql_query("insert into weixin_commonshop_subscribes(subscribe_id,need_score,isvalid,customer_id,imgurl,is_needmember) values (".$subscribe_id.",".$need_score.",true,".$customer_id.",'".$save_destination."',".$is_needmember.")");
 }
 $error =mysql_error();	
 mysql_close($link);
 echo $error; 
 echo "<script>location.href='shop_subscribes.php?customer_id=".$customer_id_en."';</script>"
?>