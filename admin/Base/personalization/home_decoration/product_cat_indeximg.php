<?php
//显示首页分类图
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");


//$keyid = $configutil->splash_new($_POST["keyid"]);
$keyid = $configutil->splash_new($_GET["keyid"]);
$cat_op=$_POST["cat_op"];
//echo "keyid=".$keyid;
$type_imgurl="";

if(!empty($_GET["type_imgurl"])){
    $type_imgurl=$configutil->splash_new($_GET["type_imgurl"]);
}else{
	 if($customer_id>0){
	   $query2="select cat_index_imgurl from weixin_commonshop_types where isvalid=true and id=".$keyid." and customer_id=".$customer_id;
	   //echo $query2;
		$result2 = _mysql_query($query2) or die('Query failed2: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
		   $type_imgurl=$row2->cat_index_imgurl;   
		}
	}
}
$op="";
if(!empty($_GET["op"])){
   $op = $configutil->splash_new($_GET["op"]);
   if($op=="del"){  
   $keyid = $configutil->splash_new($_GET["keyid"]);
		  $query="update weixin_commonshop_types set cat_index_imgurl='' where id=".$keyid;	
//echo $query;  
		  _mysql_query($query);
	  
	  $type_imgurl="";
   }  
}
//$new_baseurl = BaseURL."back_commonshop/";
$new_baseurl = Protocol.$http_host; 

$n_width=500;
$n_height=250;
 
?>

<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<!--<link href="css/global.css" rel="stylesheet" type="text/css">
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="operamasks-ui.css" rel="stylesheet" type="text/css">
<link href="css/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery.uploadify-3.1.min.js"></script>
<link href="css/uploadify.css" rel="stylesheet" type="text/css" />
-->
<script type="text/javascript" src="../../../Common/js/Base/personalization/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/assets/js/jquery.min.js"></script>

</head>
<body style="font-size:12px;">
<?php if(!empty($type_imgurl)){ ?>
<span style="position:absolute;right:0px;top:0px;cursor:pointer;" onclick="delImg(<?php echo $keyid; ?>);"><img src="../../../Common/images/Base/home_decoration/operating_icon/guanbi.png" alt="" ></span>
<?php }?>
<div id="products" class="r_con_wrap" style="background:none">
<span class="input">
<span class="upload_file">  
	<div>
	   <form action="save_pro_catimg.php?customer_id=<?php echo $customer_id_en; ?>" id="frm_img" enctype="multipart/form-data" method="post">
			<div class="up_input">
			<!--<input name="upfile" id="upfile" type="file"  width="120" height="30" value="Submit" onchange="uploading()">-->
			<?php if(!empty($type_imgurl)){ ?>
				<div id="type_imgurl" style="display:block;text-align:center">
					<a href="<?php echo $new_baseurl.$type_imgurl; ?>" target="_blank" >		
						<img style="max-width:100%;height:120px;display:inline-block;" src="<?php echo $new_baseurl.$type_imgurl; ?>">
					</a>
				</div>	  
			<?php }else{?>
				<div id="default"><a ><img style="width:100%;height:120px;" src="../../../Common/images/Base/home_decoration/typeindex_default.jpg"></a></div>
				
			<?php }?>		
			 <div id="xianshi" style="display:none;margin:50px 0px 0px 87px;"><img src="../../../Common/images/Base/home_decoration/upload.gif"></div>  
			  
			
			
			<div id="PicUploadQueue" class="om-fileupload-queue"></div>
			</div>
			<input type=hidden name="customer_id" id="customer_id" value="<?php echo $customer_id_en; ?>" />
			<input type=hidden name="keyid" id="keyid" value="<?php echo $keyid; ?>" />
			
			<div class="uploader white">
	<input type="text" class="filename" readonly/>
	<input type="button" name="file" class="button" value="上传..."/>
	<input type="file" name="upfile" id="upfile" value="Submit" size="20" onchange="uploading()"/>
</div>  
			
		</form>
		<div class="tips" style="font-size:12px;"></div>
		<div class="clear"></div>
	</div>  
</span>
<div class="clear"></div>
</div>
<?php 
    
mysql_close($link);
?>
<script type="text/javascript">  
  function uploading(){  
	 $("#default").css("display","none");
	 $("#type_imgurl").css("display","none");
	 $("#xianshi").css("display","block");
		
	  }	  
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
	parent.setParentDefaultimgurl_cat('<?php echo $type_imgurl; ?>');
	
	
	function delImg(id){
	   if(window.confirm('确定删除？')){
                 url = "product_cat_indeximg.php?op=del&keyid="+id+"&customer_id=<?php echo $customer_id_en; ?>";
				document.location= url;
                
              }else{
                
                 return false;
             }
	}
  
</script>
 <script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<style>        
.uploader {
    margin: 10px 0px;
}
.uploader {
    margin-top: 20px;
    position: relative;
    display: inline-block;
    overflow: hidden;
    cursor: default;
    padding: 0;
    -moz-box-shadow: 0px 0px 5px #ddd;
    -webkit-box-shadow: 0px 0px 5px #ddd;
    box-shadow: 0px 0px 5px #ddd;
    -moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    border-radius: 5px;
}
.filename {
    float: left;
    display: inline-block;
    outline: 0 none;
    height: 32px;  
    width: 168px;
    margin: 0;
    padding: 8px 10px;
    overflow: hidden;
    cursor: default;
    border: 1px solid;
    border-right: 0;
    font: 9pt/100% Arial, Helvetica, sans-serif;
    color: #777;
    text-shadow: 1px 1px 0px #fff;
    text-overflow: ellipsis;
    white-space: nowrap;
    -moz-border-radius: 5px 0px 0px 5px;
    -webkit-border-radius: 5px 0px 0px 5px;
    border-radius: 5px 0px 0px 5px;
    background: #f5f5f5;
    background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #fafafa), color-stop(100%, #f5f5f5));
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fafafa', endColorstr='#f5f5f5', GradientType=0);
    border-color: #ccc;
    -moz-box-shadow: 0px 0px 1px #fff inset;
    -webkit-box-shadow: 0px 0px 1px #fff inset;
    box-shadow: 0px 0px 1px #fff inset;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
.white .button {
    color: #555;
    text-shadow: 1px 1px 0px #fff;
    background: #ddd;
    background: -moz-linear-gradient(top, #eeeeee 0%, #dddddd 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #eeeeee), color-stop(100%, #dddddd));
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#eeeeee', endColorstr='#dddddd', GradientType=0);
    border-color: #ccc;
}
.uploader input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    border: 0;
    padding: 0;
    margin: 0;
    height: 30px;
    cursor: pointer;
    filter: alpha(opacity=0);
    -moz-opacity: 0;
    -khtml-opacity: 0;
    opacity: 0;
}
.button {
    float: left;
    height: 32px;
    display: inline-block;
    outline: 0 none;
    padding: 8px 11px;
    margin: 0;
    cursor: pointer;
    border: 1px solid;
    font: bold 9pt/100% Arial, Helvetica, sans-serif;
    -moz-border-radius: 0px 5px 5px 0px;
    -webkit-border-radius: 0px 5px 5px 0px;
    border-radius: 0px 5px 5px 0px;
    -moz-box-shadow: 0px 0px 1px #fff inset;
    -webkit-box-shadow: 0px 0px 1px #fff inset;
    box-shadow: 0px 0px 1px #fff inset;
}
</style>  
<script type="text/javascript">  
    upload();  
</script>  
</body>
</html>