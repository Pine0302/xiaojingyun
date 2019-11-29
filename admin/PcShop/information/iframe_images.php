

<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/contentblue.css"><!--内容CSS配色·蓝色-->
<link rel="stylesheet" type="text/css" href="../../Common/css/Product/product.css">
<link href="../../Common/css/Product/product/global.css" rel="stylesheet" type="text/css">
<link href="../../Common/css/Product/product/main.css" rel="stylesheet" type="text/css">
<link href="../../Common/css/Product/product/operamasks-ui.css" rel="stylesheet" type="text/css">
<link href="../../Common/css/Product/product/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../Common/js/Product/product/jquery.uploadify-3.1.min.js?ver=<?php echo rand(0,9999);?>"></script>
<link href="../../Common/css/Product/product/uploadify.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.uploadifive-button {
	float: left;
	margin-top:50px;
	margin-left:10px;
	margin-right:10px;	
}
#queue {
	border: 1px solid #E5E5E5;
	height: 83px;
	overflow: auto;
	margin-bottom: 3px;
	padding: 0 3px 3px;
	width: 350px;
	background:#fff;
	float: left;
}
</style>

<script type="text/javascript">
		var httphost = ''
		var imgids="";
		var img_id_upload=new Array();//初始化数组，存储已经上传的图片名
		var i=0;//初始化数组下标
		
		$(function() {
			$('#file_upload').uploadifive({
				'auto'             : true,
				//'checkScript'      : 'uppic5/check-exists.php',
				'method'   : 'post',//方法，默认为post			
				'buttonText' : '选择图片', //设置按钮文本				
				'formData': {
									'timestamp' : '',
									'token'    : '',
									'product_id' :''											
							 },
				'queueID'          : 'queue',
				// 'fileType'     : 'image/*', //允许类型：图片
				'uploadLimit' : 5, //一次最多只允许上传5张图片
				'uploadScript'     : 'uppic5/uploadifive.php',
				'removeCompleted'  :true, //上传完毕后删除
				'fileSizeLimit' : '1024KB', //限制上传的图片不得超过1M 
				'onUploadComplete' : function(file, data) { 
						console.log(file.name+" == "+data);
						var lastimg = $("#show_imgs .i_dd_100").eq(4);
						var piccount = $("#show_imgs .i_dd_100").length;
						
						if(piccount == 5){
							alert("最多只能5张图片！");
							return;
						}
						
						if(lastimg){
							lastimg.remove();
						}
						$("#show_imgs").append('<dd class="i_dd_100"><img src="'+data+'" data-id="0" class="imgPro"><span class="i_dd_span">删除</span></dd>');
						$('#file_upload').data('uploadifive').uploads.count = piccount;
				},
			   'onQueueComplete' : function(queueData) {  //上传队列全部完成后执行的回调函数
						//alert("上传成功");
						//location.href="iframe_images.php?customer_id=<?php echo $customer_id_en;?>&product_id=<?php echo $product_id?>";				
				}  				
			});
		});
</script>
</head>
<body style="font-size:12px;background-color:inherit!important;margin:auto">
<dl class="i_dl">
	<div id="queue"></div>
	<dd class="WSY_bulkboxdd03 i_dt" style="width:150px">
		<a>共可上传<span id="pic_count">5</span>张图片,图片大小建议：*像素</a>
		<!--上传文件代码开始-->
		<div class="uploader white">
		<!--
			<input type="text" class="filename" readonly/>
			<input type="button" name="file" class="button" value="上传..."/> -->
			<input type="file" size="30" id="file_upload" name="file_upload"/>
		</div>
		<!--上传文件代码结束-->
		<span class="upload_file">
			<div>
				<div class="clear"></div>
			</div>
		</span>
	</dd>
</dl>
<dl class="WSY_bulkbox02img i_dl" id="show_imgs">
	
	
	
	<dd class="i_dd_100">
	<a href="" target="_blank">
	<img class="imgPro" data-id="" src="">
	</a>
		<span class="i_dd_span">删除</span>
	</dd>
</dl>

<script type="text/javascript">  	
	if(imgids.length>0){
	   imgids= imgids.substring(0,imgids.length-1);
	   parent.setParentImgIds(imgids);
	}	
	function delImg(id){
	   url = "iframe_images.php?op=del&i_id="+id+"&customer_id=&product_id=";
	   document.location= url;
	}
  $(function(){
	  $("#show_imgs").on("click",".i_dd_span",function(){
		  var parent = $(this).parent();
		  var img = parent.find("img");
		  var id = img.data("id");
		  if(id > 0){
			$.get("iframe_images.php",{op:"del",i_id:id},function(data){
				parent.remove();
				var piccount = $("#show_imgs .i_dd_100").length;
				$('#file_upload').data('uploadifive').uploads.count = piccount;	//更改已有图片数量
				return;
			});  
		  }  
		  
		  parent.remove();
		  var piccount = $("#show_imgs .i_dd_100").length;
		  $('#file_upload').data('uploadifive').uploads.count = piccount;	//更改已有图片数量
	  });
  });
</script> 
<script src="uppic5/jquery.min.js" type="text/javascript"></script>
<script src="uppic5/jquery.uploadifive.min.js" type="text/javascript"></script> 
</body>
</html>