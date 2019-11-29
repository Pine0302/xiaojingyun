<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);//导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');  
require('../../../../../weixinpl/common/utility.php');  
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
$keyid = 0;
$len = count($_GET);
$del = "";
$article_id =-1;

if(!empty($_GET["article_id"])){
   $article_id = $configutil->splash_new($_GET["article_id"]);
}
$article_imgurl="";
if(!empty($_GET["article_imgurl"])){
    $article_imgurl=$configutil->splash_new($_GET["article_imgurl"]); 
}else{
	 if($article_id>0){
		$query='select share_img from weixin_commonshop_distributor_article where id='.$article_id;
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
		   $article_imgurl=$row->share_img;
		}
	}
}

$imgUrl = $article_imgurl; 
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../../weixinpl/proxy_info.php');
?>

<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Product/product.css">
<link href="../../../Common/css/Product/product/global.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Product/product/main.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Product/product/operamasks-ui.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Product/product/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../../Common/js/Product/product/jquery.uploadify-3.1.min.js?ver=<?php echo rand(0,9999);?>"></script>
<link href="../../../Common/css/Product/product/uploadify.css" rel="stylesheet" type="text/css" />

</head>
<body style="font-size:12px;background-color:inherit!important;margin:auto">

<form action="save_distributor_article_image.php?customer_id=<?php echo $customer_id_en; ?>&article_id=<?php echo $article_id; ?>" id="frm_img" enctype="multipart/form-data" method="post">
	<div class="WSY_memberimg" id="WSY_memberimg" >
                <dl><?php if($article_imgurl!=""){?>
					<img src="<?php echo $imgUrl; ?>" id="img_v"/>
                        <span>(尺寸要求：宽度480，高度320 ，大小1M以内）</span>
                        <div class="uploader white">
                            <input type="text" class="filename" readonly />
                            <input type="button"  class="button" value="上传..." />
                            <input type="file" name="upfile" id="upfile" size="30"/>
                        </div> 
					<?php 
						}else{ 
					?>
					<img src="/weixinpl/pic/button_bg.png" id="img_v"/>
						<span>(尺寸要求：宽度480，高度320 ，大小1M以内）</span>
						<!--上传文件代码开始-->
						<div class="uploader white">
							<input type="text" class="filename" readonly />
                            <input type="button"  class="button" value="上传..." />
                            <input type="file" name="upfile" id="upfile" size="30"/>
                        </div>
                        <!--上传文件代码结束-->
					<?php } ?>
                </dl>	
	</div>	
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
	parent.setParentShopImage('<?php echo $article_imgurl; ?>');
</script>  
  
<script type="text/javascript">  
    upload();  
</script>  
</body>
</html>