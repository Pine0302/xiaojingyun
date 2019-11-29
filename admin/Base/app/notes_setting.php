<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require('../../../../weixinpl/proxy_info.php');
require('../../../../weixinpl/auth_user.php');

$read_time = 5;
$notice = "";
$app_url = "";
$isopen_guide = 0;
$query = "select read_time,notice,app_url,isopen_guide from weixin_app_guide where customer_id=".$customer_id." and isvalid=true";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) { 
	$read_time 		= $row->read_time;
	$notice    		= $row->notice;
	$app_url  		= $row->app_url;
	$isopen_guide   = $row->isopen_guide;
}

?>

<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>app下载引导设置</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/base_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../Common/js/Base/basicdesign/layer.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>
<style>
.WSY_remind_dl02 dt{
	width: 170px;
}
.submit_div{
	text-align: left;
	margin-left: 200px;
}
#editor1{
	width: 60%;
}
.moren{
	width: 30%;
    position: absolute;
    top: 0px;
    left: 650px;
}
</style>
</head>

<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a href="weishang.php?customer_id=<?php echo $customer_id_en; ?>">操作</a>
				<a href="messenge.php?customer_id=<?php echo $customer_id_en; ?>">信息推送记录</a>
				<a class="white1">app下载引导设置</a>
			</div>
		</div>	
	<form action="save_notes_setting.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="saveFrom" name="saveFrom">
		<div class="WSY_remind_main">
		<dl class="WSY_remind_dl02"> 
			<dt>APP下载引导开关：</dt>
			<dd>
				<?php if($isopen_guide==1){ ?>
					<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
						<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
						<li onclick="set_isopen_guide(0)" class="WSY_bot" style="left: 0px;"></li>
						<span onclick="set_isopen_guide(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
					</ul>																
				<?php }else{ ?>
					<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
						<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
						<li onclick="set_isopen_guide(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
						<span onclick="set_isopen_guide(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
					</ul>						
				<?php } ?>
				<input type="hidden" name="isopen_guide" id="isopen_guide" value="<?php echo $isopen_guide; ?>" />	
			</dd>
		</dl>
		<dl class="WSY_remind_dl02" style="margin-top: 20px;"> 
			<dt>阅读完成确认时间限制：</dt>
			<dd>
				<input type="text" name="finish_time" id="finish_time" oninput="change_num(this)" onafterpaste="change_num(this)" value="<?php echo $read_time; ?>" >
				(默认5秒)
			</dd>
		</dl>
		<dl class="WSY_remind_dl02" style="margin-top: 10px;"> 
			<dt>app下载地址：</dt>
			<dd>
				<input type="text" name="app_url" id="app_url" value="<?php echo $app_url; ?>" style="width: 600px;">
			</dd>
		</dl>
		<dl class="WSY_remind_dl02">
			<dt class="editor edit1" id="edit1" style="background-color:white;">app使用须知：</dt>
			<div class="text_box input remark" style="width: 80%;margin-left: 180px;position: relative;">
            	<textarea id="editor1" name="remark"><?php echo $notice; ?></textarea>
            	<span class="moren">默认：<br/>
            		<div id="difine_notice">
            			在注册APP用户之前，请认真阅读本《用户协议》，请确保您充分同意本协议中各条款。请您审请阅读并现在接受或者不接受本协议。除非您接受本协议所有条款，否则您无权注册、登录、使用等行为将视为对本协议的接受，并同意接受本协议各项条款的约束。
            		</div>
            	</span>
            </div> 
		</dl>

		<div class="submit_div">
				<input type="button" class="WSY_button" value="提交" onclick="saveData()" style="cursor:pointer;">
				<input type="button" class="WSY_button" value="还原默认" onclick="resetData()" style="cursor:pointer;">
		</div>	
		</div>
	</form>
	
</div> 
<!--配置ckeditor和ckfinder-->
<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<!--编辑器多图片上传引入开始-->
<script type="text/javascript" src="../../../../weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/handlers.js"></script> 
<!--编辑器多图片上传引入结束-->
<script type="text/javascript">
	CKEDITOR.replace( 'editor1', //提现规则
	{
	width : 600,
	extraAllowedContent: 'img iframe[*]',
	filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
	filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Images',
	filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Flash',
	filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
	filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
	filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
	
	});

</script>
<script type="text/javascript">
	function set_isopen_guide(obj){
		$("#isopen_guide").val(obj);
	}
	function change_num(num){
		if(num.value.length==1){
			num.value=num.value.replace(/[^1-9]/g,'');
		}else{
			num.value=num.value.replace(/\D/g,'');
		}
	}
	function saveData(){
		var finish_time = $("#finish_time").val();
		var notice = CKEDITOR.instances.editor1;
		var app_url = $("#app_url").val();
		var isopen_guide = $("#isopen_guide").val();
		var define_text = $("#difine_notice").html();

		if (finish_time == ""){
			finish_time = 5;
			$("#finish_time").val("5");
		}

		var notice_text = notice.document.getBody().getText();
		// console.log(notice_text);
		notice_text = notice_text.replace(/[ ]/g,"");
		notice_text = notice_text.replace(/[\r\n]/g,"");
		if (notice_text == ""){
			notice.setData(define_text);
		}
		if (app_url == "" && isopen_guide == 1){
			alert("请输入下载链接");
			return false;
		}
		document.getElementById("saveFrom").submit();	
	}
	function resetData(){
		var define_text = $("#difine_notice").html();
		// console.log(define_text);
		var ckEditor = CKEDITOR.instances.editor1;
		ckEditor.setData(define_text);
	}
</script>
</body>
</html>
