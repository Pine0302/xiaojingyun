<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>云店奖励－店头背景</title>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
	<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
    <script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script>
    <script type="text/javascript" src="/weixinpl/js/ajaxfileupload.js"></script>
	<style type="text/css">
		.WSY_button {
		    float: none;
		    margin-bottom: 10px;
		}
		.upfile_box{
			position: absolute;
		    margin-left: -20px;
		    overflow: hidden;
		    width: 160px;
		    height: 20px;
		    margin-top: -20px;
		    opacity: 0;
		}
		.i_dt img {
		    width: 162px;
		    height: 102px;
		}
		.default{
			position: absolute;
			margin-left: 22%;
			height: 27px;
			width: 85px;
			border-radius: 5px;
			color: white;
			margin-top: 4px;
			cursor: pointer;
			font-size: 14px;
		}
		.default:hover{
			background: #06a7e1;
			color: white;
		}

	</style>
</head>
<body style="font-size:12px;background-color:inherit!important;margin:auto">
	 <div class="WSY_content" id="WSY_content_height">
        <!--列表内容大框开始-->
        <div class="WSY_columnbox"> 
            <div class="WSY_column_header">
            	<?php $keyContent = '店头背景'; ?>
                <?php include 'cloud_shop_switching.php'; ?>
            </div>
            <!--店头背景管理代码开始-->
            <div class="WSY_data">
            	<span style="color: red;">&nbsp;&nbsp;&nbsp;&nbsp;提示：图片大小建议：①风格1: 375*150像素 ②风格2：不作要求(100Kb以下)</span>
				<form action="" id="frm_img" enctype="multipart/form-data" method="post">
					<div class="" style="width: 100%;min-height: 300px;">

						<?php for ($i=0; $i < 5; $i++) { ?>
						<?php if ($upfileUrl[$i] == ''){ ?>
							<?php if($i == 0){?>
								<dl class="i_dl" style="margin-top: 30px;width: 11%;float: left;overflow: inherit;">
									<dt class="i_dt">
										<span class="i_dt_defaults" style="position: absolute;padding: 3px 7px;background: #f90;color: white;">默认</span>
										<a>
											<img src="/weixinpl/common/images_V6.0/contenticon/pic_icon.png" >
										</a>
									
										<span onclick="" class="i_dt_span">更换</span>
									</dt>
									<dd class="WSY_bulkboxdd03 i_dd">
										<!--上传文件代码开始-->
										<div class="uploader white upfile_box">
											<input type="text" class="filename" readonly/>
											<input  name="upfile" id="upfile<?php echo $i; ?>" type="file"  onchange="uploadImage2(this)"  size="30" value="Submit"/>
										</div>
										<input type=hidden name="upfile_url[]" class="upfile_url" value="/weixinpl/common/images_V6.0/contenticon/pic_icon.png" />
										<!--上传文件代码结束-->
									</dd>
								</dl>
							<?php }else{ ?>
								<dl class="i_dl" style="margin-top: 30px;width: 11%;float: left;overflow: inherit;">
									<dt class="i_dt">
										<a>
											<img src="/weixinpl/common/images_V6.0/contenticon/pic_icon.png">
										</a>
										<span onclick="" class="i_dt_span">更换</span>
										<button class="default WSY_button" type="button" onclick="defaults(this)">设为默认</button>
									</dt>
									<dd class="WSY_bulkboxdd03 i_dd">
										<!--上传文件代码开始-->
										<div class="uploader white upfile_box">
											<input type="text" class="filename" readonly/>
											<input  name="upfile" id="upfile<?php echo $i; ?>" type="file"  onchange="uploadImage2(this)"  size="30" value="Submit"/>
										</div>
										<input type=hidden name="upfile_url[]" class="upfile_url" value="/weixinpl/common/images_V6.0/contenticon/pic_icon.png" />
										<!--上传文件代码结束-->
									</dd>
								</dl>
							<?php } ?>
							
						<?php }else if ($i == 0 && $upfileUrl[0] != '') {?>
							<dl class="i_dl" style="margin-top: 30px;width: 11%;float: left;overflow: inherit;">
								<dt class="i_dt">
									<span class="i_dt_defaults" style="position: absolute;padding: 3px 7px;background: #f90;color: white;">默认</span>
									<a href="<?php echo $upfileUrl[$i]; ?>" target="_blank">
										<img src="<?php echo $upfileUrl[$i]; ?>" >
									</a>
								
									<span onclick="" class="i_dt_span">更换</span>
								</dt>
								<dd class="WSY_bulkboxdd03 i_dd">
									<!--上传文件代码开始-->
									<div class="uploader white upfile_box">
										<input type="text" class="filename" readonly/>
										<input  name="upfile" id="upfile<?php echo $i; ?>" type="file"  onchange="uploadImage2(this)"  size="30" value="Submit"/>
									</div>
									<input type=hidden name="upfile_url[]" class="upfile_url" value="<?php echo $upfileUrl[$i]; ?>" />
									<!--上传文件代码结束-->
								</dd>
							</dl>

							<?php }else if($upfileUrl[$i] != ''){ ?>

							<dl class="i_dl" style="margin-top: 30px;width: 11%;float: left;overflow: inherit;">
								<dt class="i_dt">
									<a href="<?php echo $upfileUrl[$i]; ?>" target="_blank">
										<img src="<?php echo $upfileUrl[$i]; ?>">
									</a>
								
									<span onclick="" class="i_dt_span">更换</span>
									<button class="default WSY_button" type="button" onclick="defaults(this)">设为默认</button>
								</dt>
								<dd class="WSY_bulkboxdd03 i_dd">
									<!--上传文件代码开始-->
									<div class="uploader white upfile_box">
										<input type="text" class="filename" readonly/>
										<input  name="upfile" id="upfile<?php echo $i; ?>" type="file"  onchange="uploadImage2(this)"  size="30" value="Submit"/>
									</div>
									<input type=hidden name="upfile_url[]" class="upfile_url" value="<?php echo $upfileUrl[$i]; ?>" />
									<!--上传文件代码结束-->
								</dd>
							</dl>	
							<?php } ?>

						<?php } ?>

					</div>
				</form>
            	<div class="at-btn-content" style="text-align: center;">
            		<input type="button" class="WSY_button" value="提交" onclick="BackgroundOfStore()" style="cursor:pointer;">
                </div>
            </div>
            <!--店头背景代码结束-->
        </div>
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
	//parent.setParentClassDefaultimgurl('<?php echo $class_imgurl; ?>');


	function uploadImage2(obj){
		var imgPath = $(obj).val();
		if (imgPath == "") {
			alert('请选择要上传的图片');

			return;
		}
		//判断上传文件的后缀名
		var strExtension = imgPath.substr(imgPath.lastIndexOf('.') + 1);
		if (strExtension != 'jpg' && strExtension != 'gif' && strExtension != 'png' && strExtension != 'bmp') {

			alert("上传图片的格式不正确，请上传jpg、gif、png或者bmp的格式的图片！");

			return;
		}
		var upfileId = $(obj).attr('id'); //获取当前选择图片ID 
		$.ajaxFileUpload({
			url: '/wsy_prod/admin/Product/product/store_head_background_upload.php?customer_id=<?php echo $customer_id_en; ?>&product_id=<?php echo $product_id; ?>', //用于文件上传的服务器端请求地址
			secureuri: false, //是否需要安全协议，一般设置为false
			fileElementId: upfileId, //文件上传域的ID
			dataType: 'json', //返回值类型 一般设置为json
			success: function (data, status)  //服务器成功响应处理函数
			{
				if(data.status=='ok'){
					$('#'+upfileId).parent().parent().siblings('.i_dt').find('img').attr('src',data.info);
					$('#'+upfileId).parent().parent().siblings('.i_dt').find('a').attr('href',data.info);
					$('#'+upfileId).parent().siblings('.upfile_url').val(data.info);

				}else{
					alert('上传图片失败，请重新上传！');
				}

			},
			error: function (data, status, e)//服务器响应失败处理函数
			{
				//$('#help-block2').text('上传图片失败，请重新上传！'+e+data.info);
				alert('上传图片失败，请重新上传！'+e+data.info);
			}
		})
		return false;
	}
	//设为默认图片方法
	function defaults(obj){
		$('.i_dt').find('.i_dt_defaults').parent().append('<button class="default WSY_button" type="button" onclick="defaults(this)">设为默认</button>');
		$('.i_dt').find('.i_dt_defaults').remove();
		var html = '<span class="i_dt_defaults" style="position: absolute;padding: 3px 7px;background: #f90;color: white;">默认</span>';
		$(obj).parent().prepend(html);
		$(obj).remove();
	}
	//ajax传输更改图片
	function BackgroundOfStore(){
		var pathArray = [];
		var defaultsUrl = $('.i_dt_defaults').parent().siblings('.i_dd').children('input').val();
		pathArray.push(defaultsUrl);
		$('.upfile_url').each(function(){
			var upfileUrl = $(this).val();
			if (upfileUrl != defaultsUrl) {
				pathArray.push(upfileUrl);
			}
		})
		$.ajax({
	        url: '/mshop/admin/index.php?m=yundian&a=Background_submission',
	        dataType: 'json',
	        type: 'post',
	         data: {
	        	'pathArray':pathArray,
	        	'customer_id':'<?php echo $customer_id; ?>',
	        },
	        success: function(res){
	            if( res.errcode == '1' ){
	            	layer.alert(res.errmsg);
	            	setTimeout(function(){
	            		window.location.reload();
	            	},800);
	            	
	            }else{
	                layer.alert(res.errmsg);
	            }
	        }
	    });
	}
</script>

<script type="text/javascript">
    //upload();
</script>
</body>
</html>
