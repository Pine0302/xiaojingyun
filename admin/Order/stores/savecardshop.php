<?php
header("Content-type: text/html; charset=utf-8"); 

require('../../../../weixinpl/config.php');
$link =    mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');

$card_id =$configutil->splash_new($_GET["card_id"]);
$name =$configutil->splash_new($_POST["name"]);
$phone =$configutil->splash_new($_POST["phone"]);
$address =$configutil->splash_new($_POST["address"]);
$contactname =$configutil->splash_new($_POST["contactname"]);
$type = $configutil->splash_new($_POST["type"]);
$description =$configutil->splash_new($_POST["description"]);
$mini_websiteurl = $configutil->splash_new($_POST["mini_websiteurl"]);
$category_id = $configutil->splash_new($_POST["category_id"]);
$store_number = $configutil->splash_new($_POST["store_number"]);
$chk_users = $configutil->splash_new($_POST["chk_users"]);
$location_p = $configutil->splash_new($_POST["location_p"]);
$location_c = $configutil->splash_new($_POST["location_c"]);
$location_a = $configutil->splash_new($_POST["location_a"]);
if(empty($location_a)){
	$location_a='NULL';
}
$keyid =$configutil->splash_new($_POST["keyid"]);

$destination = "";

$uptypes=array('image/jpg', //上传文件类型列表
'image/jpeg',
'image/png',
'image/pjpeg',
'image/gif',
'image/bmp',
'image/x-png');
$max_file_size=1000000; //上传文件大小限制, 单位BYTE
$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
$destination_folder="up/cardshop/"; //上传文件路径
//$watermark=1; //是否附加水印(1为加水印,0为不加水印);
//$watertype=1; //水印类型(1为文字,2为图片)
//$waterposition=2; //水印位置(1为左下角,2为右下角,3为左上角,4为右上角,5为居中);
//$waterstring="www.tt365.org"; //水印字符串
//$waterimg="xplore.gif"; //水印图片
$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
$imgpreviewsize=1/1; //缩略图比例


if(!empty($_POST['imgurl'])){
	     $destination = $_POST["imgurl"];
	  
	//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
	  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
		  //echo " 宽度:".$image_size[0];
		  //echo " 长度:".$image_size[1];
		  //添加到图片库
		  $category_id=11; //不限行业
		  $mapsize_id =1; //360*200
		  $flag_id = 3; //优惠卷
		  $query="insert into media_library_maps(category_id,mapsize_id,flag_id,owner_type,owner_id,imgurl,isvalid) values(".$category_id.",".$mapsize_id.",".$flag_id.",2,".$customer_id.",'".$destination."',true)";
		  _mysql_query($query);
		  $error = mysql_error();
		  echo $error;
}



 if($keyid>0){
    _mysql_query("update weixin_card_shops set description='".$description."', type=".$type.",imgurl='".$destination."',contactname='".$contactname."',category_id=".$category_id.",name='".$name."',phone='".$phone."',address='".$address."',store_number='".$store_number."' , card_id=".$card_id.",mini_websiteurl='".$mini_websiteurl."',location_p='".$location_p."',location_c='".$location_c."',location_a='".$location_a."' where id=".$keyid);
 }else{
    $sql = "insert into weixin_card_shops(name,phone,address,card_id,isvalid,createtime,type,category_id,mini_websiteurl,contactname,imgurl,description,store_number,location_p,location_c,location_a) values ('".$name."','".$phone."','".$address."',".$card_id.",true,now(),".$type.",".$category_id.",'".$mini_websiteurl."','".$contactname."','".$destination."','".$description."','".$store_number."','".$location_p."','".$location_c."','".$location_a."')";
    _mysql_query($sql);
	$keyid = mysql_insert_id();
 }
 //echo $chk_users."<br/>";
 if($chk_users!=""){
   $query="update weixin_card_shop_auths set isvalid=false where card_shop_id=".$keyid;
   _mysql_query($query);
   
   $userarr = explode(",",$chk_users);
   for($i=0;$i<count($userarr);$i++){
      $u_id = $userarr[$i];
      $query="insert into weixin_card_shop_auths(customer_user_id,card_shop_id,isvalid,createtime) values(".$u_id.",".$keyid.",true,now())";	  
//	  echo $query."<br/>";
	  _mysql_query($query);
   }
 }
 
 $error =mysql_error();
 mysql_close($link);
// echo $error; 
echo "<script>location.href='shops.php?customer_id=".passport_encrypt((string)$customer_id)."&card_id=".$card_id."';</script>"
?>