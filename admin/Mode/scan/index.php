<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$link =    mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require('../../../../weixinpl/proxy_info.php');
  
$keyid = -1;
$content = "您的订单二维码为：{orderNumber}";
$deliver_template_type = 0;

$query = 'SELECT id,markedWords,deliver_template_type,share_title,share_desc,share_img FROM weixin_commonshop_order_qrset where isvalid=true and customer_id='.$customer_id." limit 0,1";
$result = _mysql_query($query) or die('L16 Query failed: ' . mysql_error());

while ($row = mysql_fetch_object($result)) {
   $keyid = $row->id;
   $content = $row->markedWords;
   $deliver_template_type = $row->deliver_template_type; //核销二维码转发页面模板
   $share_title = $row->share_title;  
   $share_desc = $row->share_desc;
   $share_img = $row->share_img;
}
?>
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>二维码发送设置</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../../js/tis.js"></script>


<script>  
function saveQRTip(){
	//文字
	var content = "";
	content = document.getElementById("content").value;
	if(content==""){
		alert('请输入内容!');
		return;
	}  
document.getElementById("frm").submit();
}
</script>
<style>
#div_text p{	
	display: block;
	float: none;
	overflow: hidden;
	margin-top: 5px;
	margin-bottom: 10px;
}
#div_text p input{	
	width: 200px;
	height: 24px;
	border: solid 1px #ccc;
	border-radius: 2px;
	padding-left: 5px;
}
.con {
    width: 100%;
	font-size: 13px;
	margin-bottom: 5px;
}
label {
    margin-left: 60px;
}
.share_title {
    width: 200px;
    height: 24px;
    border: solid 1px #ccc;
    border-radius: 2px;
    padding-left: 5px;
}
.share_desc {
    width: 345px;
    height: 75px;
    padding: 5px;
    border: solid 1px #ccc;
}
.url_login {
    width: 320px;
    height: 24px;
    border: solid 1px #ccc;
    border-radius: 2px;
    padding-left: 5px;
}

</style>
</head>
<body>
<!--内容框架开始-->
	<div class="WSY_content">

		<!--列表内容大框开始-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a href="index.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" class="white1">二维码发送设置</a>
					<a href="QR_user.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>">兑票员设置</a>
					<a href="QR_user_login.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" >兑票员登录日志</a>
					<a href="QR_user_check.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" >兑票员扫码日志</a> 
				</div>
			</div>
			<!--列表头部切换结束-->
            
            <!--订单二维码代码开始-->
            <form action="index.class.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" id="frm" method="post" enctype="multipart/form-data">
				<div class="WSY_remind_main">
				
					<dl class="WSY_remind_dl02">
						<dt style="line-height:20px;font-weight:normal;margin-left:8px" class="WSY_left">核销后台登陆链接：</dt>
						<dd>
							<input type="text" class="url_login" name="url_login" value="<?php echo "//".$_SERVER['SERVER_NAME']."/weixinpl/shopQR/index.php"; ?>">
						</dd>
					</dl>				
				
					<dt style="line-height:20px;font-weight:normal;margin-left:28px;margin-top: 22px;" class="WSY_left">二维码发放提示语：</dt>		
					<dd>
						<div class="WSY_type_scdyipdiv" id="div_text">
							<div class="WSY_type_scdyip04box">                  	
								<ul class="WSY_type_scdyip04" id="div_exptype_0" style="display:block">
									<dt><textarea name="content" id="content" maxlength="680" ><?php echo $content ?></textarea></dt>
									<dd><button class="button_icon01" onClick="insertOrderNum(event);">插入订单号</button></dd>
									<dd><button class="button_icon01" onClick="insertLink(event);">插入链接</button></dd>                                
									<dd>
										<p><i>链接名称：</i><input type="text" id="txt_linkname1" value=""></p>
										<p><i>链接地址：</i><input style="width:300px" type="text" id="txt_linkurl1" value=""></p>
									</dd>
							   </ul>							                                                                         
							</div>
						</div>
					</dd>
					
					<div class="WSY_remind_div000">
						<h1>核销二维码转发页面模板</h1>
						<dl class="WSY_remind_dl02">
						<!--<dt style="line-height:20px;font-weight:normal;" class="WSY_left">核销二维码转发页面模板：</dt>-->
						<dd>
							<label style="float:left;"><div class="con"><input type="radio" name="deliver_template_type" <?php if($deliver_template_type==0){ ?>checked<?php } ?>  value=0>风格一</div> <img src='../../Common/images/Mode/scan/mb_1.png' style="margin: 0 auto;width:200px;height: 310px;    box-shadow: 0px 3px 6px 0px rgb( 193, 193, 193 );"  onMouseOver="toolTip('<img src=../../Common/images/Mode/scan/mb_1.png>')" onMouseOut="toolTip()" ></label>
							  
							<label style="float:left;"><div class="con"><input type="radio" name="deliver_template_type" <?php if($deliver_template_type==1){ ?>checked<?php } ?>  value=1>风格二 </div><img src='../../Common/images/Mode/scan/mb_2.png' style="margin: 0 auto;width:200px;height: 310px;    box-shadow: 0px 3px 6px 0px rgb( 193, 193, 193 );"  onMouseOver="toolTip('<img src=../../Common/images/Mode/scan/mb_2.png>')" onMouseOut="toolTip()" ></label>
							  
							<label style="float:left;"><div class="con"><input type="radio" name="deliver_template_type" <?php if($deliver_template_type==2){ ?>checked<?php } ?>  value=2>风格三</div> <img src='../../Common/images/Mode/scan/mb_3.png' style="margin: 0 auto;width:200px;height: 310px;    box-shadow: 0px 3px 6px 0px rgb( 193, 193, 193 );"  onMouseOver="toolTip('<img src=../../Common/images/Mode/scan/mb_3.png>')" onMouseOut="toolTip()" ></label>

							<label style="float:left;"><div class="con"><input type="radio" name="deliver_template_type" <?php if($deliver_template_type==3){ ?>checked<?php } ?>  value=3>风格四</div> <img src='../../Common/images/Mode/scan/mb_4.png' style="margin: 0 auto;width:200px;height: 310px;    box-shadow: 0px 3px 6px 0px rgb( 193, 193, 193 );"  onMouseOver="toolTip('<img src=../../Common/images/Mode/scan/mb_4.png>')" onMouseOut="toolTip()" ></label>
						</dd>
						</dl>
					</div>
					<style>
						.WSY_remind_div000{position:relative;border:solid 1px #ccc;margin-left:100px;padding-bottom:20px;width:70%;}
						.WSY_remind_div000 h1{display:block;position:absolute;left:20px;top:-10px;font-size:14px;background:#fbfbfb;padding:0 10px;}
					</style>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:20px;font-weight:normal;margin-left:8px" class="WSY_left">微信分享标题：</dt>
						<dd>
							<input type="text" class="share_title" name="share_title" value="<?php echo $share_title; ?>">
						</dd>
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:20px;font-weight:normal;margin-left:8px" class="WSY_left">微信分享描述：</dt>
						<dd>
							<textarea cols="50" rows="15" class="share_desc" name="share_desc"><?php echo $share_desc;?></textarea>
						</dd>
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:20px;font-weight:normal;margin-left:8px" class="WSY_left">微信分享图片：</dt>
						<dd>
							<div class="WSY_memberimg">
								<?php if($share_img!=""){?>
								<img src="<?php echo $share_img; ?>" style="width:100px;height:100px;">
								<?php }else{ ?>
								<img src="../../../pic/uniqlo.png" style="width:100px;height:100px;">
								<?php } ?>
								<span>(图片尺寸：100px*100px）</span>
								<!--上传文件代码开始-->
								<div class="uploader white">
									<input type="text" class="filename" readonly/>
									<input type="button" name="file" class="button" value="上传..."/>
									<input size="17" name="new_share_img" id="new_share_img" type=file value="<?php echo $share_img; ?>">
									<input type=hidden value="<?php echo $share_img; ?>" name="share_img" id="share_img" /> 
								</div>
								<!--上传文件代码结束-->
							</div>
						</dd>
					</dl>
					<div style="clear: both;"></div>						
					<input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
									
					<div class="WSY_text_input"><input type=button class="WSY_button" onClick="saveQRTip();" value="提交保存" /><br class="WSY_clearfloat"></div>
				</div>
            <!--订单二维码回复代码结束-->
             <input type="hidden" name="keyid" value="<?php echo $keyid ?>" />
    	</form>
		</div>
	</div>
	<div style="height:30px;width:100%;"></div>
</body>        


<?php


mysql_close($link);
?>



<script>
function insertLink(e){
	e.preventDefault();
	var linkname=document.getElementById("txt_linkname1").value;
	var linkurl=document.getElementById("txt_linkurl1").value;
	var content = document.getElementById("content").value;
	content = content+"<a href=\'"+linkurl+"\'>"+linkname+"</a>";
	document.getElementById("content").value = content;   
} 

function insertOrderNum(e){
	e.preventDefault();
	var title = document.getElementById("content").value;
	title = title+"{orderNumber}";
	document.getElementById("content").value = title; 
}

</script>
<script type="text/javascript" src="../../Common/js/Base/mall_setting/ToolTip.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</html>