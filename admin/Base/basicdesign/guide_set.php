<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

$head   = 5;//头部文件0商城资料，1分享设置,2购物设计,5引导提示
$query  = "select id,is_guide_attention,is_guide_phone,is_guide_app,app_domain_name,app_url_type,app_diy_url from weixin_commonshop_guide where isvalid=true and customer_id=".$customer_id." limit 1";
$result = _mysql_query($query) or die('L13 Query failed: ' . mysql_error());
$guide_id		    = -1;		//引导关注表编号
$is_guide_attention = 0;	//引导关注 0.关 1.开
$is_guide_phone 	= 0;	//引导绑定手机号 0.关 1.开
$is_guide_app       = 0;	//引导下载app 0.关 1.开
$app_domain_name    = "";		//app域名
$app_url_type       = 0;	//app下载链接类型 0：APP系统内置 1：自定义
$app_diy_url        = "";	//自定义app下载链接
while ($row = mysql_fetch_object($result)) {
	$guide_id            = $row->id;
	$is_guide_attention  = $row->is_guide_attention;
	$is_guide_phone		 = $row->is_guide_phone;
	$is_guide_app		 = $row->is_guide_app;
	$app_domain_name	 = $row->app_domain_name;
	$app_url_type	     = $row->app_url_type;
	$app_diy_url	     = $row->app_diy_url;

}
//查询是否开启APP功能
$is_open_app=0;//是否开启APP功能 1:开启 0:关闭
$sql = "select funs.id from columns as col inner join customer_funs as funs where col.sys_name='移动社交App' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
$res = _mysql_query($sql) or die('L31 Query failed: ' . mysql_error());
while( $row = mysql_fetch_object($res) ){
	$is_open_app = $row->id;
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/guide.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>

<title>引导提示</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">

</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/basicdesign/basic_head.php"); 
		?>
		<form action="save_guide_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="guide_id" id="guide_id" value="<?php echo $guide_id; ?>" />
			<div class="WSY_remind_main">
				<dl class="WSY_remind_dl02"> 
					<dt style="line-height:20px;">引导关注公众号：</dt>
					 <dd>
						<?php if($is_guide_attention==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_guide_attention(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_guide_attention(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_guide_attention(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_guide_attention(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_guide_attention" id="is_guide_attention" value="<?php echo $is_guide_attention; ?>" />
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt style="line-height:20px;">引导绑定手机号：</dt>
					 <dd>
						<?php if($is_guide_phone==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_guide_phone(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_guide_phone(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_guide_phone(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_guide_phone(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_guide_phone" id="is_guide_phone" value="<?php echo $is_guide_phone; ?>" />
				</dl>
				<?php if($is_open_app > 0){ ?>
				<dl class="WSY_remind_dl02"> 
					<dt style="line-height:20px;">引导下载APP：</dt>
					 <dd>
						<?php if($is_guide_app==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_guide_app(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_guide_app(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_guide_app(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_guide_app(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>		
					</dd>					
					
					<input type="hidden" name="is_guide_app" id="is_guide_app" value="<?php echo $is_guide_app; ?>" />					
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt style="line-height:20px;">填写APP下载链接：</dt>
					<dd>
						<input type="radio" name="app_url_type" id="app_url_type" value=0 <?php if($app_url_type == 0){ ?>checked<?php } ?> />使用系统内置链接：
						<input type="text" class="app_diy_url"  name="app_domain_name" id="app_domain_name" value="<?php echo $app_domain_name; ?>" placeholder="请输入APP域名" /><a class="frm_tips">（必须以http://或https://开头）</a>
						<input type="radio" name="app_url_type" id="app_url_type" value=1 <?php if($app_url_type == 1){ ?>checked<?php } ?> />使用自定义链接：
						<input type="text" class="app_diy_url"  name="app_diy_url" id="app_diy_url" value="<?php echo $app_diy_url; ?>" placeholder="请输入自定义链接" /><a class="frm_tips">（支持应用宝等类型的下载链接）</a>
					</dd>					
					<dd  style="margin: 10px 230px 10px;">
						
					</dd>			
				</dl>

				<?php } ?>
			</div>
			<div class="submit_div">
				<input type="submit" class="WSY_button" value="提交" style="cursor:pointer;">
			</div>
		</form>
		
	</div>
</div> 
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>

<script>

var customer_id='<?php echo $customer_id_en?>';
</script>

<script>
function change_guide_attention(obj){
	$("#is_guide_attention").val(obj);
}
function change_guide_phone(obj){
	$("#is_guide_phone").val(obj);
}
function change_guide_app(obj){
	$("#is_guide_app").val(obj);
	if( obj == 1 ){
		//$(".app_item").fadeIn();
	}else{
		//$(".app_item").fadeOut();
	}
}

$(document).ready(function(){
	//表单提交 --start
	$('#upform').off('submit').on('submit',function(e){
		e.preventDefault();
		
		var is_guide_app    = $("#is_guide_app").val();
		var app_domain_name = $.trim($("#app_domain_name").val());
		var app_diy_url     = $.trim($("#app_diy_url").val());
		var app_url_type    = $('input[name="app_url_type"]:checked').val(); //获取被选中Radio的Value值
		if(app_url_type == ''){
			layer.msg('请选择APP下载类型！', {icon: 5});
			return false;
		}
		
		if( is_guide_app == 1 ){
			//判断域名正确性开始
			if( app_url_type == 0 ){
				if( app_domain_name == "" ){
					layer.msg('请填写正确的APP域名！', {icon: 5});
					return false;
				}
				if(check_name(app_domain_name) == true){
					layer.msg('域名禁止填入特殊字符！', {icon: 5});
					return false;
				}
				var pos1 = app_domain_name.indexOf("http://");
				var pos2 = app_domain_name.indexOf("https://");
				if(pos1 == 0){		
				}else if(pos2 == 0){		
				}else{
					layer.msg('请填写正确的APP域名！', {icon: 5});
					return false;
				}
			}
			//判断域名正确性结束
			if( app_url_type == 1 ){
				if( app_diy_url == "" ){
					layer.msg('请填写正确的APP下载自定义链接！', {icon: 5});
					return false;
				}
			}
		}
		
		
		document.getElementById("upform").submit();
	});
	//表单提交 --end
	
})


//检查域名是否有单引号双引号
function check_name(name){
	var stu = false;
	var reg = /['"’”‘“]/g;
	stu = reg.test(name);
	return stu;
}
</script>
</body>
</html>