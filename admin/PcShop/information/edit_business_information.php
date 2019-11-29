<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

$business_id = -1;
if(!empty($_GET['business_id'])){
	$business_id = $_GET['business_id'];
}

$email 				  = '';//联系人电子邮箱
$phone 				  = '';//联系人电话
$name 				  = '';//联系人姓名
$company_name 		  = '';//公司名称
$company_management   = '';//公司经营
$company_describe 	  = '';//公司描述
$business_licence 	  = '';//营业执照
$business_licence_img = '';//营业执照图片
$corporation_name	  = '';//法人姓名
$identity			  = '';//身份证号码
$identityimgt		  = '';//身份证正面
$identityimgf		  = '';//身份证反面
$query = "select email,phone,name,company_name,company_management,company_describe,business_licence,business_licence_img,corporation_name,identity,identityimgt,identityimgf from pcshop_merchants_settled_member where isvalid=true and customer_id=".$customer_id." and id=".$business_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $email 					= $row->email;
    $phone 					= $row->phone;
    $name 					= $row->name;
    $company_name 			= $row->company_name;	
    $company_management 	= $row->company_management;	
    $company_describe 		= $row->company_describe;	
    $business_licence 		= $row->business_licence;	
    $business_licence_img 	= $row->business_licence_img;	
    $corporation_name		= $row->corporation_name;	
    $identity 				= $row->identity;	
    $identityimgt 			= $row->identityimgt;	
    $identityimgf 			= $row->identityimgf;	
}
$business_licence_img = ltrim($business_licence_img,'..');
$identityimgt = ltrim($identityimgt,'..');
$identityimgf = ltrim($identityimgf,'..');


?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/supplier/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<title>编辑入驻商家</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>
.WSY_button1 {
	cursor: pointer;
    width: 82px;
    height: 30px;
    background-color: #06a7e1;
    border: none;
    font-size: 13px;
    color: #f9fdff;
    border-radius: 2px;
    float: left;
    border: solid 1px #06a7e1;
    text-align: center;
}
</style>
</head>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a href="" class="white1">编辑入驻商家</a>
				</div>
			</div>
			<!--列表头部切换结束-->
			<form action="save_business_information.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
				<div class="WSY_remind_main">
					<dl class="WSY_member">
						<dt style="line-height:28px;" class="WSY_left">公司名称：</dt>
						<dd>
							<input type="text" class="company_name" name="company_name" value="<?php echo $company_name; ?>" type="text" readonly>
						</dd>
					</dl>
					<dl class="WSY_member">
						<dt style="line-height:28px;" class="WSY_left">公司经营：</dt>
						<dd>
							<input type="text" class="company_management" name="company_management" value="<?php echo $company_management; ?>" type="text" readonly>
						</dd>
					</dl>
					<dl class="WSY_member">
						<dt style="line-height:28px;" class="WSY_left">法人姓名：</dt>
						<dd>
							<input type="text" class="corporation_name" name="corporation_name" value="<?php echo $corporation_name; ?>" type="text" readonly>
						</dd>
					</dl>
					<dl class="WSY_member">
						<dt style="line-height:28px;" class="WSY_left">联系人姓名：</dt>
						<dd>
							<input type="text" class="name" name="name" value="<?php echo $name; ?>" type="text" readonly>
						</dd>
					</dl>
					<dl class="WSY_member">
						<dt style="line-height:28px;" class="WSY_left">联系人电话：</dt>
						<dd>
							<input type="text" class="phone" name="phone" value="<?php echo $phone; ?>" type="text" readonly>
						</dd>
					</dl>
					<dl class="WSY_member">
						<dt style="line-height:28px;" class="WSY_left">联系人电子邮箱：</dt>
						<dd>
							<input type="text" class="email" name="email" value="<?php echo $email; ?>" type="text" readonly>
						</dd>
					</dl>
					<dl class="WSY_member">
						<dt style="line-height:28px;" class="WSY_left">营业执照：</dt>
						<dd>
							<input type="text" class="business_licence" name="business_licence" value="<?php echo $business_licence; ?>" type="text" readonly>
						</dd>
					</dl>
					
					<dl class="WSY_member">			
						<div>
							<dt>营业执照图片：</dt>						
							<dd class="spa">
								<img src="<?php echo $business_licence_img; ?>" id="img_v1" style="width:220px;height:200px;" /><br/>
								<!--<input style="width:208;border:1 solid #9a9999; font-size:9pt; background-color:#ffffff; height:18;margin-top: 5px;margin-bottom: 5px;" size="17" name="upfile1" id="upfile1" class="upfile" type=file value=""> (图片尺寸：宽220*高200)-->
								<input type=hidden value="<?php echo $business_licence_img; ?>" name="imgurl1" id="imgurl1" /> 

							</dd>	
							
						</div>
					</dl>

					<dl class="WSY_member">
						<dt style="line-height:28px;" class="WSY_left">身份证号码：</dt>
						<dd>
							<input type="text" class="identity" name="identity" value="<?php echo $identity; ?>" type="text" readonly>
						</dd>
					</dl>
					<dl class="WSY_member">			
						<div>
							<dt>身份证正面：</dt>						
							<dd class="spa">
								<img src="<?php echo $identityimgt; ?>" id="img_v2" style="width:220px;height:200px;" /><br/>
								<!--<input style="width:208;border:1 solid #9a9999; font-size:9pt; background-color:#ffffff; height:18;margin-top: 5px;margin-bottom: 5px;" size="17" name="upfile2" id="upfile2" class="upfile" type=file value=""> (图片尺寸：宽220*高200)-->
								<input type=hidden value="<?php echo $identityimgt; ?>" name="imgurl2" id="imgurl2" /> 

							</dd>	
							
						</div>
					</dl>
					<dl class="WSY_member">			
						<div>
							<dt>身份证反面：</dt>						
							<dd class="spa">
								<img src="<?php echo $identityimgf; ?>" id="img_v3" style="width:220px;height:200px;" /><br/>
								<!--<input style="width:208;border:1 solid #9a9999; font-size:9pt; background-color:#ffffff; height:18;margin-top: 5px;margin-bottom: 5px;" size="17" name="upfile3" id="upfile3" class="upfile" type=file value=""> (图片尺寸：宽220*高200)-->
								<input type=hidden value="<?php echo $identityimgf; ?>" name="imgurl3" id="imgurl3" /> 

							</dd>	
							
						</div>
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;" class="WSY_left">公司描述：</dt>							
					</dl>
					<textarea id="editor1"  name="company_describe" disabled="disabled"><?php echo $company_describe; ?></textarea>
				
					
					<input type=hidden name="business_id" id="business_id" value="<?php echo $business_id; ?>">
					<!--<div class="WSY_text_input"><button class="WSY_button" onclick=" return subBase();">提交保存</button><br class="WSY_clearfloat"></div>-->
					<div class="WSY_text_input"><button class="WSY_button" onclick=" history.go(-1);">返回</button><br class="WSY_clearfloat"></div>
				</div>
			</form>
		</div>
	</div>
<?php mysql_close($link);?>	
<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<script>


 function subBase(){
	var company_name = $('.company_name').val();
	var company_management = $('.company_management').val();
	var corporation_name = $('.corporation_name').val();
	var name = $('.name').val();
	var phone = $('.phone').val();
	var email = $('.email').val();
	var business_licence = $('.business_licence').val();
	var identity = $('.identity').val();
	var cke_editor1 = $('#editor1').val();

	if(company_name==""){
		alert("公司名称不能为空！");
		return false;
	}	
	if(company_management==""){
		alert("公司经营不能为空！");
		return false;
	}	
	if(corporation_name==""){
		alert("法人姓名不能为空！");
		return false;
	}	
	if(name==""){
		alert("联系人名称不能为空！");
		return false;
	}	
	if(phone==""){
		alert("联系人电话不能为空！");
		return false;
	}	
	if(email==""){
		alert("联系人电子邮箱不能为空！");
		return false;
	}	
	if(business_licence==""){
		alert("营业执照不能为空！");
		return false;
	}	
	if(identity==""){
		alert("身份证号码不能为空！");
		return false;
	}	
	if(cke_editor1==""){
		alert("公司描述不能为空！");
		return false;
	}	

	document.getElementById("upform").submit();
 }
</script>
<!--编辑器多图片上传引入开始--->
<script type="text/javascript" src="../../../../weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/handlers.js"></script>
<!--编辑器多图片上传引入结束--->
<script>
CKEDITOR.replace( 'editor1',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Images',
filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Flash',
filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});

$(function(){
	function getObjectURL(file) {
		var url = null ; 
		if (window.createObjectURL!=undefined) {
		url = window.createObjectURL(file) ;
		} else if (window.URL!=undefined) {
		url = window.URL.createObjectURL(file) ;
		} else if (window.webkitURL!=undefined) {
		url = window.webkitURL.createObjectURL(file) ;
			}
		return url ;
		}
		$(".upfile").change(function(){
		var objUrl;
		if(navigator.userAgent.indexOf("MSIE")>0){
		objUrl = this.value;
		}else
		objUrl = getObjectURL(this.files[0]);
		var src_id = $(this).parent().find('img').attr('id');
		$("#"+src_id).attr("src",objUrl);
	}) ;
})

</script>

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>