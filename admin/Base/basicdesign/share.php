<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$new_baseurl = $http_host;
$head=1;//头部文件0商城资料，1分享设置,2购物设计
$query = "select id,per_share_score,is_showshare_info,gz_url,distr_type,define_share_image,is_dis_model from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$per_share_score=0;//每推广增加一名粉丝,奖励的积分
$is_showshare_info = 0;//分享链接是否显示分享者
$distr_type = 0;//会员锁定关系模式
$gz_url = "";//引导关注链接
$define_share_image = 0;//产品分享图背景模式
$is_dis_model = 0; //是否保存过分销模式
$shop_id=-1;//是否存在商城
$logo = '';	//商城LOGO
$query = "SELECT logo FROM weixin_commonshops_extend WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
$res= _mysql_query($query) or die('Query failed 64: ' . mysql_error());
while( $is_row = mysql_fetch_object($res) ){
	$logo = $is_row->logo;
}
while ($row = mysql_fetch_object($result)) {
	$shop_id=$row->id;
	$per_share_score=$row->per_share_score;
	$is_showshare_info=$row->is_showshare_info;
	$distr_type=$row->distr_type;
	$gz_url=$row->gz_url;
	$is_dis_model=$row->is_dis_model;
	$define_share_image=$row->define_share_image;
	$define_share_image_flag=$define_share_image?1:0;
	if ($define_share_image == $logo) {
		$define_share_image_flag = 0;
	}else{
		$define_share_image_flag = 1;
	}
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/share.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>


<title>分享设置</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>
	<?php if(isset($_GET['type'])&&$_GET['type']=='cityarea') { $displayType = 'none';} else { $displayType = 'block';}?>
	.display{display: <?php echo $displayType; ?>; }
	.filenames {
    float: left;
    display: inline-block;
    outline: 0 none;
    height: 32px;
    width: 180px;
    margin: 0;
    padding: 8px 10px;
    overflow: hidden;
    cursor: default;
    border: 1px solid;
    border-right: 0;
    font: 9pt/100% Arial, Helvetica, sans-serif;
    color: #777;
    text-shadow: 1px 1px 0px #fff;
    text-overflow: ellipsis;
    white-space: nowrap;
    -moz-border-radius: 5px 0px 0px 5px;
    -webkit-border-radius: 5px 0px 0px 5px;
    border-radius: 5px 0px 0px 5px;
    background: #f5f5f5;
    background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #fafafa), color-stop(100%, #f5f5f5));
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fafafa', endColorstr='#f5f5f5', GradientType=0);
    border-color: #ccc;
    -moz-box-shadow: 0px 0px 1px #fff inset;
    -webkit-box-shadow: 0px 0px 1px #fff inset;
    box-shadow: 0px 0px 1px #fff inset;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
</style>
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/basicdesign/basic_head.php");
		?>
		<form action="save_share.php?customer_id=<?php echo $customer_id_en; ?>&type=<?php echo $type; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
			<div class="WSY_remind_main">
				<dl class="WSY_remind_dl02 display" style="display:none"> 
					<dt style="line-height:20px;">分享首页显示分享者：</dt>
					 <dd>
						<?php if($is_showshare_info==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_showshare_info(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_showshare_info(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_showshare_info(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_showshare_info(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_showshare_info" id="is_showshare_info" value="<?php echo $is_showshare_info; ?>" />
				</dl>	
				<dl class="WSY_remind_dl02"> 
					<dt>会员锁定邀请人模式：</dt>
					<dd>
						<div class="distr_type_div">
							<input type="radio" id="distr_type1" name="distr_type" class="distr_type" <?php if($distr_type==1){ ?>checked<?php } ?> value="1" name="distr_type">支付后锁定
						</div>
						<div class="distr_type_div">
							<input type="radio" id="distr_type2" name="distr_type" class="distr_type" <?php if($distr_type==2){ ?>checked<?php } ?> value="2" name="distr_type">第一次邀请人锁定
						</div>
						<input type=hidden name="is_dis_model" id="is_dis_model" />
					</dd>
					<a href="lockmode_change_log.php?customer_id=<?php echo $customer_id_en;?>"><img style="width:20px;" title="查看修改日志" src="../../Common/images/Base/basicdesign/icon-log.png"/></a>
				</dl>
				<dl class="WSY_remind_dl02 display"> 
					<dt>分享奖励积分：</dt>
					<dd>
						奖励
						<input type="text" class="gz_url per_share_score" name="per_share_score" value="<?php echo $per_share_score; ?>">
						积分
					</dd>
				</dl>
				<dl class="WSY_remind_dl02 display" style=""> 
					<dt>链接：</dt>
					<dd>
						<input type="text" class="gz_url" name="gz_url" value="<?php echo $gz_url; ?>">
						<!--span>点击关注链接（<a style="color:#11ABE2;" href="../../../word/guanzhu_operation.doc">点击下载操作文档</a>）</span-->
                        <span class="WSY_preview1" style="position: absolute;margin-left: 21px;">示例图<span style="top: -10px;left: 70px; background-image:none;">
                        <img src="/weixinpl/back_newshops/Common/images/Base/basicdesign/link.png" style="width: 363px;height: 109px;"></span></span>
					</dd>
				</dl>
				<dl class="WSY_remind_dl02 display"> 
					<dt>分享默认图标：</dt>
					<dd>
						<div class="distr_type_div">
							<input type="radio" onclick="change_photo_div(0)" class="distr_type" <?php if($define_share_image_flag==0){ ?>checked<?php } ?> value="0" name="define_share_image_flag">默认
						</div>
						<div class="distr_type_div">
							<input type="radio" onclick="change_photo_div(1)" class="distr_type" <?php if($define_share_image_flag==1){ ?>checked<?php } ?> value="1" name="define_share_image_flag">自定义
						</div>

						<div class="WSY_memberimg">
							<?php if($define_share_image_flag == 1){?>
								<img value="1" src="<?php echo Protocol.$new_baseurl.'/'.$define_share_image; ?>" style="width:64px;height:64px;">
							<?php }else{ ?>
								<img value="0" src="/weixinpl/common/images_V6.0/contenticon/pic_icon.png" style="width:64px;height:64px;">
							<?php } ?>
							<span>(图片尺寸：64px*64px）</span>
							<!--上传文件代码开始-->
							<div class="uploader white">
								<?php if($define_share_image_flag == 1){?>
									<span id="img_url" style="display: none;"><?php echo $define_share_image ?></span>
									<input cal-data="1" type="text" class="filenames" value="<?php echo $define_share_image ?>" readonly/>
								<?php }else{ ?>
									<input cal-data="0" type="text" class="filenames" readonly />
								<?php } ?>
								
								<input type="button" name="file" class="button" value="上传..."/>
								<input size="17" name="new_define_share_image" id="upfile1" type=file value="">
								<input type=hidden value="<?php echo $define_share_image ?>" name="define_share_image" id="define_share_image" /> 
							</div>
							<!--上传文件代码结束-->
						</div>
					</dd>
				</dl>
			</div>
		</form>
		<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="submitV(this,'<?php echo $define_share_image_flag ?>');" style="cursor:pointer;">
		</div>
		
	</div>
</div> 
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script type="text/javascript" src="../../Common/js/Base/basicdesign/share.js"></script>


<script>
change_photo_div(<?php echo $define_share_image_flag;?>);
var shop_id=<?php echo $shop_id?>;
var customer_id='<?php echo $customer_id_en?>';
$('#upfile1').change(function(event) {
	$('.filenames').val($(this).val());
});

$('.filenames').val($('#img_url').text());
</script>
<script type="text/javascript" src="/weixinpl/back_newshops/Common/js/Base/check_shop.js"></script>
</body>
</html>