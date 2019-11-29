<html>
<head>
<title>编辑</title>
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content{$theme}.css">
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>
#cke_editor,#cke_editor div{    
	float: none;
    margin-right: 0px;
}
.WSY_member input[type="radio"] 
{
	display:inline;
    float: none;
}
</style>
</head>
<body>


<div style='width:320px;height:360px'>
	<?php  if(empty($default_imgurl)){ ?>
	<img src="../../../up/slb_mall/s05.jpg" id='myimg' style='width:230px;height:260px'/>	
	<input type=hidden name="default_imgurl" id="default_imgurl" value="/weixinpl/up/slb_mall/s05.jpg" />
	<?php }else{ ?>
	<img src="<?php echo $default_imgurl;?>" id='myimg' style='width:230px;height:260px'/>
	<input type=hidden name="default_imgurl" id="default_imgurl" value="<?php echo $default_imgurl;?>" />
	<?php } ?>
	<br/>
	<input type="file" name="up_img" id="up_img"  onchange="setImagePreview()" style="border: 1px solid #CFCBCB;width:320px;margin-top:5px"/>
	<br/>
	<span>230px*310px比例</span>
	


</div>
<script type="text/javascript" src="/weixinpl/common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="/weixinpl/common/js_V6.0/ajaxfileupload.js"></script>
<script>
	function setImagePreview() {
		var imgPath = $("#up_img").val();
		var old_img=$("#default_imgurl").val();
		$("#bt").removeAttr("onclick");

		if (imgPath == "") {
			alert('请选择上传图片！');
			return;
		}
		//判断上传文件的后缀名
		var strExtension = imgPath.substr(imgPath.lastIndexOf('.') + 1);
		if (strExtension != 'jpg' && strExtension != 'gif' && strExtension != 'png' && strExtension != 'bmp') {
			alert('上传图片的格式不正确，请上传jpg、gif、png或者bmp的格式的图片！');
			return;
		}
		// document.getElementById("myimg").src= window.URL.createObjectURL(docObj.files[0]);
		// document.getElementById("default_imgurl").value= window.URL.createObjectURL(docObj.files[0]);

		$.ajaxFileUpload
		(
				{
					url: 'save_uploading.php', //用于文件上传的服务器端请求地址
					secureuri: false, //是否需要安全协议，一般设置为false
					fileElementId: 'up_img', //文件上传域的ID
					data:{type:'slide',customer_id:'{$C_id}',old_img:old_img},
					dataType: 'json', //返回值类型 一般设置为json
					success: function (data)  //服务器成功响应处理函数
					{
						//alert("00:"+data.info);
						if(data.state==1){
							$("#myimg").attr('src',data.savedir);
							$("#default_imgurl").val(data.savedir);
							//alert(data.info);
							//alert('a');
						}
						$("#bt").attr("onclick","submitV(this);");
					},
					error: function (err)//服务器响应失败处理函数
					{
						//$('#help-block').text(err.msg);
					}
				}
		)


	}

</script>
</body>
</html>