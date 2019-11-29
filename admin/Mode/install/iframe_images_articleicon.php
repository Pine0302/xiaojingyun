<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");
$article_id =-1;

if(!empty($_GET["article_id"])){
   $article_id = $_GET["article_id"];
}
$icon="";
if(!empty($_GET["icon"])){
    $icon=$_GET["icon"];
}else{
	 if($article_id>0){
	   $query2="select icon from weixin_install_article where isvalid=true and  id=".$article_id;
		$result2 = _mysql_query($query2) or die('L22 : Query failed: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
		   $icon=$row2->icon;
		}
	}
}
$op="";
if(!empty($_GET["op"])){
   $op = $_GET["op"];
   if($op=="del"){
      $i_id = $_GET["i_id"];
	  if($article_id>0){
		  $query="update weixin_install_article set icon='' where id=".$article_id;
		  _mysql_query($query);
	  }
	  $icon="";
   }
}
//$new_baseurl = BaseURL."install_platform/";
$new_baseurl = "http://".$http_host;  
$n_width=450;
$n_height=300;
 
?>

<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link href="../../../back_commonshop/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../back_commonshop/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../back_commonshop/operamasks-ui.css" rel="stylesheet" type="text/css">
<link href="../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../back_commonshop/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../back_commonshop/js/jquery.uploadify-3.1.min.js?ver=<?php echo rand(0,9999);?>"></script>
<link href="../../../back_commonshop/css/uploadify.css" rel="stylesheet" type="text/css" />

</head>
<body style="font-size:12px;">
<div id="products" class="r_con_wrap">
<span class="input">
<span class="upload_file">
	<div>
	   <form action="save_images_articleicon.php?customer_id=<?php echo $customer_id; ?>&article_id=<?php echo $article_id; ?>" id="frm_img" enctype="multipart/form-data" method="post">
			<div class="up_input">
			<input name="upfile" id="upfile" type="file"  width="120" height="30" value="Submit">
			
			<div id="PicUploadQueue" class="om-fileupload-queue"></div>
			</div>
			<input type=hidden name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>" />
		</form>
		<div class="tips" style="font-size:12px;">上传1张图片，作为首页的图片。图片大小建议：<?php echo $n_width; ?>*<?php echo $n_height; ?>像素,70k以下</div>
		<div class="clear"></div>
	</div>
</span>


<div class="img" id="PicDetail">
  
        <?php if(!empty($icon)){ ?>
		<div>
			 <a href="<?php echo $new_baseurl.$icon; ?>" target="_blank">
			 <img src="<?php echo $new_baseurl.$icon; ?>"></a>
			 <span onclick="delImg();">删除</span>
		</div>
        <?php } ?>
  
</div>
</span>
<div class="clear"></div>
</div>
<?php 

mysql_close($link);
?>
<script type="text/javascript">  
  
    function upload(){  
        var element = document.getElementById("upfile");  
        if("\v"=="v")  
        {  
            element.onpropertychange = uploadHandle;  
        }  
        else  
        {  
            element.addEventListener("change",uploadHandle,false);  
        }  
  
        function uploadHandle()  
        {  
            if(element.value)  
            {  
              
			  $("#frm_img").submit();
  
            }  
        }  
  
    } 
	parent.setParentDefaultimgurl('<?php echo $icon; ?>');
	
	
	function delImg(id){
	   url = "iframe_images_articleicon.php?op=del&i_id="+id+"&customer_id=<?php echo $customer_id; ?>&article_id=<?php echo $article_id; ?>";
	   document.location= url;
	}
  
</script>  
  
<script type="text/javascript">  
    upload();  
</script>  
</body>
</html>