<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php');
require('../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');		
_mysql_query("SET NAMES UTF8");	
require('../../../../weixinpl/proxy_info.php');
$keyid        = -1;
$is_open      = 0;//是否上架
$title        = '';//分类名称
$sort         = 0;//排序
$categorie_id = array();//该类别的产品分类ID
if(!empty($_POST["keyid"])){
	$keyid = $configutil->splash_new($_POST["keyid"]);
}
if(!empty($_POST["is_open"])){
	$is_open = $configutil->splash_new($_POST["is_open"]);
}
if(!empty($_POST["title"])){
	$title = $configutil->splash_new($_POST["title"]);
}
if(!empty($_POST["sort"])){
	$sort = $configutil->splash_new($_POST["sort"]);
}
if(!empty($_POST["categorie_id"])){
	$first_id  = -1;//一级分类ID
	foreach($_POST["categorie_id"] as $k => $v){
		$first_id = $v;
		$first_name = '';//一级分类名字
		$query = "select name,is_shelves from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and id=".$first_id;
		$result = _mysql_query($query) or die("Query pri_id fail：" .mysql_error());
		while($row = mysql_fetch_object($result)){
			$first_name = $row->name;
			$first_is_shelves = $row->is_shelves;
		}
		$query = "select id,name,is_shelves from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and parent_id=".$first_id;
		$result = _mysql_query($query) or die("Query pri_id fail：" .mysql_error());
		$second_id = array();//二级分类ID
		while($row = mysql_fetch_object($result)){
			$second_id[$row->id] = array('id'=>$row->id,'name'=>$row->name,'status'=>$row->is_shelves,'parent'=>$first_id);			
		}
		$categorie_id[$first_id] = array('id'=>$first_id,'name'=>$first_name,'status'=>$first_is_shelves,'child'=>$second_id);
		
	}
	$categorie_id = json_encode($categorie_id,JSON_UNESCAPED_UNICODE);//转化成json
}
$ad_pro_id = '';//产品ID
$destination = array();//图片数组
for($i=1;$i<=7;$i++){
	
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
	$destination_folder="../../../../weixinpl/".Base_Upload."pc_category/".$customer_id.'/'; 									//上传文件路径

	$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
	$imgpreviewsize=1/1; //缩略图比例
	$website_default= "http://".CLIENT_HOST."/weixin/plat/app/html/";

	 if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if (!is_uploaded_file($_FILES["image".$i]["tmp_name"]))	//判断是否上传文件，是则不上传文件，使用旧文件
		{	
		  $destination[$i] = $configutil->splash_new($_POST['img'.$i]);
		  // echo  $destination;
		}else{
			$file = $_FILES["image".$i];
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
		$foreign_id = 0;
		$detail_id = 0;
		if(!empty($destination[$i])){
			$foreign_id = $configutil->splash_new($_POST["foreign_id".$i]);
			$foreign_id = explode("_",$foreign_id);
			$foreign_id = $foreign_id[0];
			if(!empty($foreign_id)){
				$detail_id = $configutil->splash_new($_POST["detail_id".$i]);
			}
		}
		$ad_pro_id .= $foreign_id."_".$detail_id.'|';
	}  
	/*图片上传end*/	
}
$ad_pro_id = rtrim($ad_pro_id,"|");

$top_img  		  = '';//顶部广告图
$right_img 		  = '';//右侧广告图
$abbreviation_img = '';//分类缩略图
for($n=1;$n<=3;$n++){
	$top_img .= $destination[$n]."|";
}
$top_img = rtrim($top_img,"|");
for($n=4;$n<=6;$n++){
	$right_img .= $destination[$n]."|";
}
$right_img = rtrim($right_img,"|");
$abbreviation_img = $destination[7];

//循环热门推荐
$popular = array();
for($h=1;$h<=5;$h++){
	$type_id = -1;
	$type_id = $configutil->splash_new($_POST["search_type_id".$h]);

	if($type_id > 0){
		$type_sql = "select name from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and id=".$type_id;
		$result = _mysql_query($type_sql) or die("Query pri_id fail：" .mysql_error());
		while($row = mysql_fetch_object($result)){
			$name = $row->name;
		}
		$popular[$type_id] = array('id'=>$type_id,'name'=>$name);
	}
	
}
$popular = json_encode($popular,JSON_UNESCAPED_UNICODE);//转化成json
if($keyid>0){
	$sql = "update pcshop_home_categories set 
							is_open=".$is_open.",
							sort=".$sort.",
							title='".$title."',
							popular='".$popular."',
							top_img='".$top_img."',
							right_img='".$right_img."',
							categorie_id='".$categorie_id."',
							abbreviation_img='".$abbreviation_img."',
							ad_pro_id='".$ad_pro_id."'
							where isvalid=true and id=".$keyid."
							";
}else{
	$sql = "insert into pcshop_home_categories(
											customer_id,
											isvalid,
											is_open,
											sort,
											title,
											top_img,
											popular,
											right_img,
											categorie_id,
											abbreviation_img,
											ad_pro_id,
											createtime
										) values(
											".$customer_id.",
											true,
											".$is_open.",
											".$sort.",
											'".$title."',
											'".$top_img."',
											'".$popular."',
											'".$right_img."',
											'".$categorie_id."',
											'".$abbreviation_img."',
											'".$ad_pro_id."',
											now()
										)";
}
$data = _mysql_query($sql) or die("SQL fail：".mysql_error()."<br>query：".$sql);
mysql_close($link);
echo "<script>location.href='all_categories.php?customer_id=".$customer_id_en."';</script>"
?>