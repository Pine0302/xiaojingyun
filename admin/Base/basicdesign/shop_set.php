<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=2;//头部文件0商城资料，1分享设置,2购物设计
$query = "select id,name,pro_card_level,is_godefault,sell_discount,is_number_limit,auto_cus_time,need_customermessage,isprint,per_identity_num,is_identity,is_uploadidentity,is_cost_limit,per_cost_limit,is_weight_limit,per_weight_limit,per_number_limit,is_ban_use_coupon_currency from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed1: ' . mysql_error());
$name="";//商城名称
$pro_card_level = 0;//购买产品需要会员卡级别开关
$is_godefault=0;   //先进单，再购买
$sell_discount = 0;//购买折扣率
$auto_cus_time = 7;//自动确认收货时间
$need_customermessage= 0;//短信通知开关
$isprint= 0;//小票打印机开关
$is_identity = 0;       //是否开启身份证验证
$is_uploadidentity = 0;       //是否开启上传身份证附件
$per_identity_num = 0;   //每个身份证号每天可下单数量
$is_cost_limit = 0;       //是否开启购买限制
$per_cost_limit = 0;   //每人每天不高于的总额 
$is_weight_limit = 0;   //是否开启重量限制
$per_weight_limit = 0;   //每人每天不高于的KG 
$per_number_limit = 0;   //每人每天购买产品数量不多于
$is_number_limit = 0;   //是否开启数量限制
$recovery_time = 0;   //没支付订单失效时间
$is_orderActivist = -1;   //订单售后维权开关
$shop_id = -1;
$is_ban_use_coupon_currency = 0; //是否禁止购物币和优惠券同时使用
while ($row = mysql_fetch_object($result)) {
	$shop_id=$row->id;
	$name=$row->name;
	$pro_card_level=$row->pro_card_level;
	$is_godefault=$row->is_godefault;
	$sell_discount=$row->sell_discount;
	$is_number_limit=$row->is_number_limit;
	$auto_cus_time=$row->auto_cus_time;
	$need_customermessage=$row->need_customermessage;
	$isprint=$row->isprint;
	$is_ban_use_coupon_currency=$row->is_ban_use_coupon_currency;
	$is_identity=$row->is_identity;
	$is_uploadidentity=$row->is_uploadidentity;
	$per_identity_num=$row->per_identity_num;
	$is_cost_limit=$row->is_cost_limit;
	$per_cost_limit=$row->per_cost_limit;
	$is_weight_limit=$row->is_weight_limit;
	$per_weight_limit=$row->per_weight_limit;
	$per_number_limit=$row->per_number_limit;
}
if($shop_id<0){
	$query = "insert into weixin_commonshops set customer_id=".$customer_id.",isvalid=true";
	_mysql_query($query);
	$shop_id = mysql_insert_id();
}
$query = "select recovery_time,is_orderActivist from weixin_commonshops_extend where isvalid=true and customer_id=".$customer_id." and shop_id=".$shop_id;
$result = _mysql_query($query) or die('Query failed2: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$recovery_time=$row->recovery_time;
	$is_orderActivist=$row->is_orderActivist;
}
?>
<html>
<head>
<style type="text/css">
.WSY_remind_main{overflow:hidden;}
.divfloat{display:block;float:left;width:600px;}
</style>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/shop_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>


<title>购物设计</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/basicdesign/basic_head.php");
		?>
		<form action="save_shop_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
			<div class="WSY_remind_main">
				<div class="divfloat" style="width:430px;">
				<dl class="WSY_remind_dl02"> 
					<dt>开启会员卡级别购买限制：</dt>
					<dd>
						<?php if($pro_card_level==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_pro_card_level(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_pro_card_level(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_pro_card_level(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_pro_card_level(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="pro_card_level" id="pro_card_level" value="<?php echo $pro_card_level; ?>" />
				</dl>	
				<dl class="WSY_remind_dl02"> 
					<dt>先进单品页，再进购买页：</dt>
					<dd>
						<?php if($is_godefault==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_godefault(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_godefault(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_godefault(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_godefault(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_godefault" id="is_godefault" value="<?php echo $is_godefault; ?>" />
				</dl>
				<!--
				<dl class="WSY_remind_dl02"> 
					<dt>购买折扣率：</dt> 
					<dd>
						<input style="float:left" type="text" class="sell_discount no_left" name="sell_discount" value="<?php echo $sell_discount; ?>">
						<span class="no_left">%<span style="margin-left:10px;display: inline-block;"><img src="../../Common/images/Base/help.png" onMouseOver="toolTip('0表示无折扣')" onMouseOut="toolTip()"></span> </span> 
					</dd>
				</dl>
				-->
				<dl class="WSY_remind_dl02"> 
					<dt>系统默认收货时间：</dt>
					<dd>
						<input style="float:left" type="text" class="auto_cus_time no_left" name="auto_cus_time" value="<?php echo $auto_cus_time; ?>">
						<span class="no_left">天<span style="margin-left:10px;display: inline-block;"><img src="../../Common/images/Base/help.png" onMouseOver="toolTip('商家确认发货后，自动确认收货')" onMouseOut="toolTip()"></span></span>
					</dd>
				</dl> 
				<dl class="WSY_remind_dl02"> 
					<dt>没支付订单失效时间：</dt>
					<dd>
						<input style="float:left" type="text" class="recovery_time no_left" name="recovery_time" value="<?php echo $recovery_time; ?>" autocomplete="off" onkeyup="clearInt(this)">
						<span class="no_left">分钟<span style="margin-left:10px;display: inline-block;"><img src="../../Common/images/Base/help.png" onMouseOver="toolTip('当下单后在设置的时间内没支付，订单将自动失效，必须大于0')" onMouseOut="toolTip()"></span></span>
					</dd>
				</dl> 
				<dl class="WSY_remind_dl02"> 
					<dt>开启对顾客短信通知：</dt>
					<dd>
						<?php if($need_customermessage==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_need_customermessage(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_need_customermessage(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_need_customermessage(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_need_customermessage(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
						<span style="margin-left:10px;display: inline-block;"><img src="../../Common/images/Base/help.png" onMouseOver="toolTip('开启后，将按每条收取短信费用')" onMouseOut="toolTip()"></span>
					</dd>
					<input type="hidden" name="need_customermessage" id="need_customermessage" value="<?php echo $need_customermessage; ?>" />
				</dl>	
				<dl class="WSY_remind_dl02"> 
					<dt>开启小票打印：</dt>
					<dd>
						<?php if($isprint==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_isprint(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_isprint(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_isprint(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_isprint(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
						<span style="margin-left:10px;">(<a style="color:#14ACE3;" href="/weixin/plat/app/index.php/Printer_cd/printer_list/type/5/shop_id/<?=$shop_id?>/shop_name/<?=$name?>/C_id/<?=$customer_id?>">设置小票打印机</a>)</span>
					</dd>
					<input type="hidden" name="isprint" id="isprint" value="<?php echo $isprint; ?>" />
				</dl>	
				<dl class="WSY_remind_dl02"> 
					<dt>优惠券和<?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>不能同时使用：</dt>
					<dd>	
						<?php if($is_ban_use_coupon_currency==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_both_currency_coupon(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_both_currency_coupon(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_both_currency_coupon(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_both_currency_coupon(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_ban_use_coupon_currency" id="is_ban_use_coupon_currency" value="<?php echo $is_ban_use_coupon_currency; ?>" />
				</dl>	
				</div>
				<div class="divfloat">
				<dl class="WSY_remind_dl02 Identity">  
					<dt>开启身份证验证：</dt>
					<dd>
						<?php if($is_identity==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_identity(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_identity(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_identity(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_identity(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
						<span>每个身份证号每天可下<input type="text" name="per_identity_num" onkeyup="clearInt(this)"  value="<?php echo $per_identity_num ;?>">单</span>
					</dd>
					<input type="hidden" name="is_identity" id="is_identity" value="<?php echo $is_identity; ?>" />
				</dl>
				<dl class="WSY_remind_dl02 Identity_annex">
					<dt>开启身份证附件上传：</dt>
					<dd>
						<?php if($is_uploadidentity==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_uploadidentity(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_uploadidentity(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
						<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_is_uploadidentity(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_uploadidentity(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>
						<?php } ?>
					</dd>
					<input type="hidden" name="is_uploadidentity" id="is_uploadidentity" value="<?php echo $is_uploadidentity; ?>" />
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>开启金额限购：</dt>
					<dd>
						<?php if($is_cost_limit==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_cost_limit(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_cost_limit(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_cost_limit(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_cost_limit(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
						<span>每人每天不高于<input type="text" value="<?php echo $per_cost_limit; ?>" onkeyup="clearNoNum(this)" name="per_cost_limit">元</span>
					</dd>
					<input type="hidden" name="is_cost_limit" id="is_cost_limit" value="<?php echo $is_cost_limit; ?>" />
				</dl>	
				<dl class="WSY_remind_dl02"> 
					<dt>开启重量限购：</dt>
					<dd>
						<?php if($is_weight_limit==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_weight_limit(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_weight_limit(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_weight_limit(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_weight_limit(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
						<span>每人每天不高于<input type="text" value="<?php echo $per_weight_limit; ?>" onkeyup="clearNoNum(this)" name="per_weight_limit">KG</span>
					</dd>
					<input type="hidden" name="is_weight_limit" id="is_weight_limit" value="<?php echo $is_weight_limit; ?>" />
				</dl>	
				<dl class="WSY_remind_dl02"> 
					<dt>开启数量限购：</dt>
					<dd>
						<?php if($is_number_limit==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_number_limit(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_number_limit(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?> 
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_number_limit(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_number_limit(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
						<span>每人每天不多于<input type="text" name="per_number_limit" onkeyup="clearInt(this)" value="<?php echo $per_number_limit; ?>">件产品</span>
					</dd>
					<input type="hidden" name="is_number_limit" id="is_number_limit" value="<?php echo $is_number_limit; ?>" /> 
				</dl>	
				</div>
			</div> 
		</form>
		<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;">
		</div>
		
	</div>
</div> 
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../Common/js/Base/basicdesign/shop_set.js"></script>
<script type="text/javascript" src="../../Common/js/Base/basicdesign/ToolTip.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script>
var shop_id=<?php echo $shop_id?>;
var customer_id='<?php echo $customer_id_en?>';
function submitV(a){
	var recovery_time = $('.recovery_time').val();
	if(recovery_time<=0){
		layer.alert('没支付订单失效时间必须大于0');
		return;
	}
	document.getElementById("upform").submit();
}


//正整数
function clearInt(obj){
	if(obj.value.length==1){obj.value=obj.value.replace(/[^1-9]/g,'')}else{obj.value=obj.value.replace(/\D/g,'')}
}
</script>
<script type="text/javascript" src="/weixinpl/back_newshops/Common/js/Base/check_shop.js"></script>
</body>
</html>