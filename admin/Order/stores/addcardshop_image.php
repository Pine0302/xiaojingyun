<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);//导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');  
require('../../../../weixinpl/common/utility.php');  
$keyid = 0;
$len = count($_GET);
$del = "";
$card_id = $configutil->splash_new($_GET["card_id"]);
$shop_id =-1;

if(!empty($_GET["shop_id"])){
   $shop_id = $configutil->splash_new($_GET["shop_id"]);
}
$shop_imgurl="";
if(!empty($_GET["shop_imgurl"])){
    $shop_imgurl=$configutil->splash_new($_GET["shop_imgurl"]); 
}else{
	 if($shop_id>0){
	   $query = 'SELECT imgurl FROM weixin_card_shops where id='.$shop_id;
		$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
		   $shop_imgurl=$row2->imgurl;
		}
	}
}

/*
$op="";
if(!empty($_GET["op"])){
   $op = $configutil->splash_new($_GET["op"]);
   if($op=="del"){
      $i_id = $configutil->splash_new($_GET["i_id"]);
	  if($product_id>0){
		  $query="update weixin_commonshop_products set class_imgurl='' where id=".$product_id;
		  _mysql_query($query);
	  }
	  $class_imgurl="";
   }
}*/
$imgUrl = $shop_imgurl; 
  $link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
  mysql_select_db(DB_NAME) or die('Could not select database');
  _mysql_query("SET NAMES UTF8");
 require('../../../../weixinpl/proxy_info.php');
?>

<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Product/product.css">
<link href="../../Common/css/Product/product/global.css" rel="stylesheet" type="text/css">
<link href="../../Common/css/Product/product/main.css" rel="stylesheet" type="text/css">
<link href="../../Common/css/Product/product/operamasks-ui.css" rel="stylesheet" type="text/css">
<link href="../../Common/css/Product/product/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../../js/ajaxfileupload.js"></script>
<script type="text/javascript" src="../../Common/js/Product/product/jquery.uploadify-3.1.min.js?ver=<?php echo rand(0,9999);?>"></script>
<link href="../../Common/css/Product/product/uploadify.css" rel="stylesheet" type="text/css" />

</head>
<body style="font-size:12px;background-color:inherit!important;margin:auto">

<form action="save_addcardshop_image.php?customer_id=<?php echo $customer_id_en; ?>&shop_id=<?php echo $shop_id; ?>" id="frm_img" enctype="multipart/form-data" method="post">
	<div class="WSY_memberimg" id="WSY_memberimg" >
                <dl><?php if($shop_imgurl!=""){?>
					<img src="<?php echo $imgUrl; ?>" id="img_v"/>
                        <span>(尺寸要求：宽度480，高度320 ，大小30K以内）</span>
                        <div class="uploader white">
                            <input type="text" class="filename" readonly />
                            <input type="button"  class="button" value="上传..." />
                            <input type="file" name="upfile" id="upfile" size="30"  onchange="uploadImage2()"/>
							<span id="help-block2" style="display: none;">图片上传中...</span>
                        </div> 
						<p class="WSY_imgkup WSY_public" >
							<a  onclick="parent.showMediaMap(<?php echo $customer_id; ?>);" >图片库</a>
						</p>
					<?php 
						}else{ 
					?>
					<img src="pic/shop.jpg" id="img_v"/>
					
						<span>(尺寸要求：宽度480，高度320 ，大小30K以内）</span>
						<!--上传文件代码开始-->
						<div class="uploader white">
							<input type="text" class="filename" readonly />
                            <input type="button"  class="button" value="上传..." />
                            <input type="file" name="upfile" id="upfile" size="30" onchange="uploadImage2()"/>
							<span id="help-block2" style="display: none;">图片上传中...</span>
                        </div>
                        <!--上传文件代码结束-->
						<p class="WSY_imgkup WSY_public" >
						
							<a  onclick="parent.showMediaMap(<?php echo $customer_id; ?>);" >图片库</a>
						</p>
					<?php } ?>
					<span id="help-block" style="display: none;">图片上传中...</span>
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
	parent.setParentShopImage('<?php echo $shop_imgurl; ?>');
	
function uploadImage2(){
	$('#help-block2').hide();
	var imgPath = $("#upfile").val();  
	if (imgPath == "") {  
		$('#help-block2').show();
		$('#help-block2').text('请选择上传图片！');
		
		return;  
	}  
	//判断上传文件的后缀名  
	var strExtension = imgPath.substr(imgPath.lastIndexOf('.') + 1);  
	if (strExtension != 'jpg' && strExtension != 'gif' && strExtension != 'png' && strExtension != 'bmp') {  
		$('#help-block2').show();
		$('#help-block2').text('上传图片的格式不正确，请上传jpg、gif、png或者bmp的格式的图片！');
		
		return;  
	}
	$('#help-block2').show();
	$('#help-block2').text('图片上传中...');
    
	$.ajaxFileUpload({
		url: 'save_addcardshop_image.php', //用于文件上传的服务器端请求地址
		secureuri: false, //是否需要安全协议，一般设置为false
		fileElementId: 'upfile', //文件上传域的ID
		dataType: 'json', //返回值类型 一般设置为json
		success: function (data, status)  //服务器成功响应处理函数
		{	
			if(data.status=='ok'){
				$('#help-block2').hide();
				$("#WSY_memberimg dl img").attr("src", data.info);
					
				parent.setParentShopImage(data.info);	
			}else{
				$('#help-block2').text('上传图片失败，请重新上传！');
                   
					
			}
		
		},
		error: function (data, status, e)//服务器响应失败处理函数
		{
			$('#help-block2').text('上传图片失败，请重新上传！'+e+data.info);
                   
		}
	})
	return false;
}	

	function delImg(id){
	   url = "iframe_class_images_defaultproduct.php?op=del&i_id="+id+"&customer_id=<?php echo $customer_id_en; ?>&product_id=<?php echo $product_id; ?>";
	   document.location= url;
	}
  
</script>  
  
<script type="text/javascript">  
    //upload();  
</script>  
</body>
</html>