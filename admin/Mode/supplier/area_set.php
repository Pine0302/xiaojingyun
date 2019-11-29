<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');

// 数据库操作类
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
$database = new \Key\DB();

// 连接数据库
$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);

_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=6;//头部文件 

$sql = "SELECT * FROM weixin_commonshop_area where customer_id='{$customer_id}' and isvalid = true";
$data = $database->getFields($sql);

$sql = "SELECT a_name,b_name,c_name,d_name from weixin_commonshop_shareholder WHERE isvalid = true and customer_id='{$customer_id}'";
$shareholder = $database->getFields($sql);

if($data['level']){
	$level	= explode('_', $data['level']);
}
// var_dump($data);

// 更新1还是创建2
$op = $data?1:2;

?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>区域批发商-基本设置</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/supplier/set.css">

<style type="text/css" media="screen">
	.WSY_remind_dl02 input {
		 width: auto; 
		 height: auto; 
	}
	#prerogative{
		margin-top: 20px;
	}
</style>
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/supplier/basic_head.php"); 
			?>
			<!--列表头部切换结束-->
			<form action='area_operation.php' enctype="multipart/form-data" method="post" id="upform" name="upform">
				<input type="hidden" name="op" value="<?php echo $op ?>"><!-- 更新1还是创建2 -->
				<div class="WSY_remind_main">
					<dl class="WSY_remind_dl02"  style="margin-top:40px;"> 
						<dt style="line-height:20px;" class="WSY_left">开启区域批发商：</dt>
						<dd>
							<?php if($data['is_area']==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change('is_area',0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change('is_area',1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change('is_area',0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change('is_area',1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
						</dd>						
						<input type="hidden" name="is_area" id="is_area" value="<?php echo $data['is_area']; ?>" />
					</dl>
					
					<div id='area' <?php if($data['is_area']!=1) echo 'style="display:none"' ?>>
					<!-- <dl class="WSY_remind_dl02"  style="margin-top:40px;"> 
						<dt style="line-height:20px;" class="WSY_left">客户端个人中心开启区域批发商申请入口：</dt>
						<dd>
							<?php if($data['is_area_entrance']==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change('is_area_entrance',0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change('is_area_entrance',1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change('is_area_entrance',0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change('is_area_entrance',1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
						</dd>						
						<input type="hidden" name="is_area_entrance" id="is_area_entrance" value="<?php echo $data['is_area_entrance']; ?>" />
					</dl> -->

					<dl class="WSY_remind_dl02"  style="margin-top:40px;"> 
						<dt style="line-height:20px;" class="WSY_left">用户定位：</dt>
						<dd>
							<div>
							<input name="user_location" type="radio" value="1" <?php if( ($data['user_location']==1) or ($data['user_location']==0) ){ echo 'checked';} ?> /><label> 精确到省级 </label>
							<input name="user_location" type="radio" value="2" <?php if($data['user_location']==2){ echo 'checked';} ?> /><label> 精确到市级 </label>
							<input name="user_location" type="radio" value="3" <?php if($data['user_location']==3){ echo 'checked';} ?> /><label> 精确到区域 </label>
							</div>
						</dd>						
					</dl>

					<dl class="WSY_remind_dl02"  style="margin-top:40px;"> 
						<dt style="line-height:20px;" class="WSY_left">特权限制：</dt>

						<dd>
							<?php if($data['is_prerogative']==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change('is_prerogative',0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change('is_prerogative',1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change('is_prerogative',0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change('is_prerogative',1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
							<span style="float: left;margin: -16px 150px;color: #888;"><span style="margin-top: -3px;display: inline-block;"><img id="ncomission_auto" src="../../Common/images/Base/help.png"></span></span>
							<div id='prerogative' <?php if($data['is_prerogative']!=1) echo 'style="display:none"' ?>>
							<input type="checkbox" name='level[]' value="-1" <?php if(in_array(-1, $level)){ echo 'checked'; } ?> /><label> 粉丝 </label> 
							<input type="checkbox" name='level[]' value="0" <?php if(in_array(0, $level)!==false){ echo 'checked'; } ?> /><label> 推广员 </label> 
							<input type="checkbox" name='level[]' value="1" <?php if(in_array(1, $level)){ echo 'checked'; } ?> /><label> <?php echo $shareholder['d_name'] ?> </label> 
							<input type="checkbox" name='level[]' value="2" <?php if(in_array(2, $level)){ echo 'checked'; } ?> /><label> <?php echo $shareholder['c_name'] ?> </label> 
							<input type="checkbox" name='level[]' value="3" <?php if(in_array(3, $level)){ echo 'checked'; } ?> /><label> <?php echo $shareholder['b_name'] ?> </label> 
							<input type="checkbox" name='level[]' value="4" <?php if(in_array(4, $level)){ echo 'checked'; } ?> /><label> <?php echo $shareholder['a_name'] ?> </label> 
							</div>
						</dd>	

						<input type="hidden" name="is_prerogative" id="is_prerogative" value="<?php echo $data['is_prerogative']; ?>" />
					</dl>

					<dl class="WSY_remind_dl02"  style="margin-top:40px;"> 
						<dt style="line-height:20px;" class="WSY_left">申请限制：</dt>
						<dd>
						<div>
							<input name="apply_restrict" type="radio" value="1" <?php if($data['apply_restrict']==1){ echo 'checked';} ?> /><label> 品牌合作商无法申请 </label>
							<input name="apply_restrict" type="radio" value="2" <?php if($data['apply_restrict']==2){ echo 'checked';} ?> /><label> 无限制 </label>
							<input name="apply_restrict" type="radio" value="3" <?php if($data['apply_restrict']==3){ echo 'checked';} ?> /><label> 仅品牌合作商 </label>
						</div>
						</dd>						
					</dl>
					</div>
					<div id="editor2_supply" >
						<dl class="WSY_remind_dl02">
							<dt style="line-height:28px;" class="WSY_left">区域批发商协议：</dt>							
						</dl>
						<textarea id="editor2" name="brandsupply_detail"><?php echo $data['brandsupply_detail'];?></textarea>
					</div>
				</div>
			<div class="WSY_text_input"><button class="WSY_button" onclick="submit();">提交保存</button><br class="WSY_clearfloat"></div>
			</form>

		</div>
	</div>
<?php mysql_close($link);?>	
<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<script>

	$(document).ready(function(){ 

	}); 

	function setTypeImg(imgurl){
		$("#brand_adimg").val(imgurl);
	}
	/*function submit(){
		$.get('area_operation.php',$('#upform').serialize(),function(data){
			window.location.href=location
		})
	}*/
</script>
<!--编辑器多图片上传引入开始-->
<script type="text/javascript" src="../../../../weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/handlers.js"></script>
<!--编辑器多图片上传引入结束-->
<script>

CKEDITOR.replace( 'editor2',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Images',
filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Flash',
filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});
function change(name,obj){
	$('#'+name).val(obj);
	if( name == 'is_area' ){
		if(obj){
			$('#area').show()
		}else{
			$('#area').hide()
		}
	}
	if( name == 'is_prerogative' ){
		if(obj){
			$('#prerogative').show()
		}else{
			$('#prerogative').hide()
		}
	}
}

// function area(obj){
// 	if(obj){
// 		$('#area').show()
// 	}else{
// 		$('#area').hide()
// 	}
// }
$('#ncomission_auto').on('click', function(){
	layer.tips("只有拥有对应身份的用户才能进入区域批发商列表页面",'#ncomission_auto');
});
</script>

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>