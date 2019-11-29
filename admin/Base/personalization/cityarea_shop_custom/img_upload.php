<?php
 
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
// require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');


require('../../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");

$type_id =-1;

if(!empty($_GET["type_id"])){
   $product_id = $configutil->splash_new($_GET["type_id"]);
}
$type_imgurl="";
if(!empty($_GET["type_imgurl"])){
    $type_imgurl=$configutil->splash_new($_GET["type_imgurl"]);
}else{
	 if($customer_id>0){
	   $query2="select imgurl from weixin_commonshop_types where isvalid=true and id=".$type_id." and customer_id=".$customer_id;
	   //echo $query2;
		$result2 = _mysql_query($query2) or die('Query failed2: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
		   $type_imgurl=$row2->imgurl;
		}
	}
}
$op="";
if(!empty($_GET["op"])){
   $op = $configutil->splash_new($_GET["op"]);
   if($op=="del"){  
   $keyid = $configutil->splash_new($_GET["keyid"]);
		  $query="update weixin_commonshop_types set imgurl='' where id=".$keyid;	
//echo $query;  
		  _mysql_query($query);
	    
	  $type_imgurl="";
   }
}


$new_baseurl = $protocol_http_host.;  
$n_width=450;
$n_height=300;

?>

<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentblue.css"><!--内容CSS配色·蓝色-->
<link rel="stylesheet" type="text/css" href="../../../Common/css/Product/product.css">
<link href="../../../Common/css/Product/product/global.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Product/product/main.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Product/product/operamasks-ui.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Product/product/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../../Common/js/Product/product/jquery.uploadify-3.1.min.js?ver=<?php echo rand(0,9999);?>"></script>
<link href="../../../Common/css/Product/product/uploadify.css" rel="stylesheet" type="text/css" />
<style>

.classify_text{font-size:16px;display:block;margin-top:-10px;margin-left:30px;background:#fff;width:110px;text-align:center;}
.classify_name{float:left;display:block;margin-top:28px;margin-left:20px;font-size:14px;}
.classify_name input{width:212px;height:24px;border:solid 1px #dadada;margin-left:12px;border-radius:2px;}
.classify_span select{width:212px;height:24px;padding:3px;border-radius:3px;display:inline-block;border:solid 1px #dadada;}
.classify_span{float:left;margin-left:18px;margin-top:10px;font-size:14px;}  
.classify_span input{margin-left:-10px;}
.classify_content{}
.white{margin-left:10px;}
.classify_content p{width:240px;font-size:14px;margin-top:16px;margin-left:10px;}
.classify_content_img{border:0;margin-left:5px;margin-top:10px;display:block;}
.classify_content-input{position:absolute; bottom:10px;text-align:center;display:block;left:30px;}
.classify_input{background:#07a7e1;border:1px solid #056f9f;width:110px;height:30px;font-size:16px;color:#fff; font-family:"微软雅黑";border-radius:3px; cursor:pointer;}
.classify_input2{background:#e3e3e3;border:1px solid #c7c7c7;width:110px;height:30px;font-size:16px;color:#6d6d6d; font-family:"微软雅黑";border-radius:3px; cursor:pointer;margin-left:20px;}
.show_text{font-size:16px;background:#fff;margin-left:30px;margin-top:-10px;display:block;width:140px;}
.show_img{overflow:hidden;margin-left:26px;margin-top:24px;}
.show_img a{float:left;margin-right:15px;margin-bottom:30px;}
.show_button{display:block;text-align:center;}
.show_button2{width:110px;height:30px;background:#07a7e1;border:1px solid #056f9f;border-radius:3px;cursor:pointer;font-size:16px;font-family:"微软雅黑";color:#fff;}
.classify_name_text input{width:150px;}
</style>
</head>
<body style="font-size:12px;background-color:inherit!important;margin:auto">
<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img" enctype="multipart/form-data" method="post">
			
			<!--上传文件-->
			<div class="uploader white">
				<input type="text" class="filename" readonly/>
				<input type="button" name="file" class="button" value="上传..."/>
				<input type="file" name="upfile" id="upfile" size="30"/>

			</div>
			
			<?php if(empty($type_imgurl)){?>
				<a href="" class="classify_content_img"><img id="show_img" src="../../../Common/images/Product/contenticon/classify_content.png" style="width:278px;height:120px" ></a>
			<?php }else{?>
			<a href="" class="classify_content_img"><img id="show_img" src="<?php echo $type_imgurl;?>" style="width:278px;height:120px" ></a>
			<?php } ?>
</form>

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
	//parent.setTypeLogo('<?php echo $type_imgurl; ?>');
	
  
</script>  
  
<script type="text/javascript">  
    upload();  
</script>  
</body>
</html>