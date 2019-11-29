<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

//echo 1;
$head=1;//头部文件1推广二维码设置，0基本设置,2分佣设置

$yz_type 	= 0;//0：短信验证；1：验证码验证
$code_type 	= 0;//0：不显示；1：公众号二维码；2：APP二维码
$agreement 	= "";//购买协议
$is_agreement = 0;//是否开启购买协议
$query = "SELECT yz_type,code_type,agreement,is_agreement FROM register_set WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
$result= _mysql_query($query) or die('Query failed 15: ' . mysql_error()." query ==".$query);  
while ($row = mysql_fetch_object($result)) {
	$yz_type 	= $row->yz_type;
	$code_type 	= $row->code_type;
	$agreement 	= $row->agreement;
	$is_agreement = $row->is_agreement;
}



?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Reward/Commission/basic_set.css">
<title>基本设置</title>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/jquery.jsonp-2.2.0.js" charset="utf-8"></script>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php

			// require("../../../../weixinpl/back_newshops/PcShop/register_setting/basic_head.php"); 
		include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/PcShop/register_setting/basic_head.php");

		?>
		<form action="save_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
			<div class="WSY_remind_main">
				<div style="overflow:hidden">
					

				<div style="overflow:hidden">

				<div class=" remind01">
					<dl class="WSY_remind_dl02" style="margin-top:40px;"> 
						<dt style="line-height:20px;" class="WSY_left">注册是否需要阅读协议：</dt>
						<dd>
							<?php if($is_agreement==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">是</p>
								<li onclick="change_is_agreement(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_agreement(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">否</p>
								<li onclick="change_is_agreement(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_agreement(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
						</dd>						
						<input type="hidden" name="is_agreement" id="is_agreement" value="<?php echo $is_agreement; ?>" />
					</dl>
					
					<dl class="WSY_remind_dl02"> 
						<dt>验证码类型选择：</dt>
						<dd style="overflow:hidden;margin-top:5px;">
																												
							<div class="WSY_remind_labelbox">
								<label>
									<input type="radio" <?php if($yz_type == 0){ ?>checked<?php } ?> value=0 name="yz_type">
									使用短信验证
								</label>
							</div>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="radio" <?php if($yz_type == 1){ ?>checked<?php } ?> value=1 name="yz_type">
									使用普通验证码
								</label>
							</div>
							
						</dd>
					</dl>

					<dl class="WSY_remind_dl02"> 
						<dt>注册成功后显示的二维码：</dt>
						<dd style="overflow:hidden;margin-top:5px;">
																												
							<div class="WSY_remind_labelbox">
								<label>
									<input type="radio" <?php if($code_type == 1){ ?>checked<?php } ?> value=1 name="code_type">
									公众号二维码
								</label>
							</div>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="radio" <?php if($code_type == 2){ ?>checked<?php } ?> value=2 name="code_type">
									APP二维码
								</label>
							</div>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="radio" <?php if($code_type == 0){ ?>checked<?php } ?> value=0 name="code_type">
									不显示
								</label>
							</div>
							
						</dd>
					</dl>

				</div>
				<dl class="WSY_remind_dl02 remind01" style="width:47%;height: auto;"> 
					<dt style="float:none;margin-left:-84px;margin-bottom:5px;">注册协议：</dt>
					<dd style="float:left">
					<textarea id="editor1" style="	visibility: visible;display: initial;"  name="agreement"><?php echo $agreement; ?></textarea>
				</dl>
				</div>
			</div>
		</form>
		<div class="submit_div" style="margin-left:0px;text-align:center">
			<input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;">
		</div>
		
	</div>
</div> 

<script>

var customer_id='<?php echo $customer_id_en; ?>';
</script>

<!--编辑器多图片上传引入开始-->
<script type="text/javascript" src="/weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/handlers.js"></script>
<!--编辑器多图片上传引入结束-->

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>




<script>
var editor = CKEDITOR.replace( 'editor1',{
	width: '80%', 
	extraAllowedContent: 'img iframe[*]',
	filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
	filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Images',
	filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Flash',
	filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
	filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
	filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});

var firstfocus = true;
CKEDITOR.instances["editor1"].on("instanceReady", function () {  
	this.document.on("click", clearContent);  
});  

function clearContent(){
	if(firstfocus == true){
		CKEDITOR.tools.setTimeout(function(){
			//debugger;
			//var dsc = CKEDITOR.instances.editor1.getData();
			var dom_body = editor.document.getBody().$;
			var divs = $(dom_body).find("div");
			divs.each(function(i,n){
				if($(n).html().indexOf("tongji.baidu.com")>-1){
					$(n).remove();
				}
			});
		},0);
		firstfocus = false;
	}
	
}
function change_modify_model(obj){ 
	$("#modify_up").val(obj);
	if(obj){
		$(".modify_type_dl").show();
	}else{
		$(".modify_type_dl").hide();
	}
}

function submitV(){
	$("#upform").submit();
}

function change_is_agreement(o){
	$("#is_agreement").val(o);
}	



</script>
</body>
</html>