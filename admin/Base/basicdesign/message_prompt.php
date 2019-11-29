<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=3;//头部文件0商城资料，1分享设置,2购物设计,3消息提示
$query = "select id from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$shop_id=$row->id;
}
$is_qrMessage 			= -1;//分享或者扫二维码提示消息开关
$is_memberBuyMessage 	= -1;//下级会员购物消息开关
$is_buyContentMessage 	= -1;//下级会员购物消息消息（关闭购物内容）开关
$is_commission_message  = -1;//佣金消息提示开关，0关，1开
$is_commission_scope    = -1;//佣金消息提示范围，0所有人，1推广员提示
$is_openOrderMessage    = -1;//商城显示下单提示开关，0关，1开
$query = "select is_qrMessage,is_memberBuyMessage,is_buyContentMessage,is_commission_message,is_commission_scope,is_openOrderMessage from weixin_commonshops_extend where isvalid=true and customer_id=".$customer_id." and shop_id=".$shop_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$is_qrMessage=$row->is_qrMessage;
	$is_memberBuyMessage=$row->is_memberBuyMessage;
	$is_buyContentMessage=$row->is_buyContentMessage;
    $is_commission_message=$row->is_commission_message;
    $is_commission_scope=$row->is_commission_scope;
	$is_openOrderMessage=$row->is_openOrderMessage;
}
?>
<html>
<head>
<style type="text/css">
.WSY_remind_main{overflow:hidden;}
.divfloat{display:block;float:left;width:600px;}
.WSY_remind_dl02 dd input{width:13px;height:13px;}
</style>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/shop_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>


<title>消息提示</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/basicdesign/basic_head.php"); 
		?>
		<form action="save_message_prompt.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
			<div class="WSY_remind_main">
				<div class="divfloat" style="width:430px;">
				<dl class="WSY_remind_dl02"> 
					<dt>建立关系消息提示：</dt>
					<dd>
						<?php if($is_qrMessage==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_qrMessage(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_qrMessage(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_qrMessage(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_qrMessage(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_qrMessage" id="is_qrMessage" value="<?php echo $is_qrMessage; ?>" />
				</dl>	
				<dl class="WSY_remind_dl02" id="memberBuyMessage"> 
					<dt>下级会员购物消息：</dt>
					<dd>
						<?php if($is_memberBuyMessage==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_memberBuyMessage(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_memberBuyMessage(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_memberBuyMessage(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_memberBuyMessage(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_memberBuyMessage" id="is_memberBuyMessage" value="<?php echo $is_memberBuyMessage; ?>" />
				</dl>
				<dl class="WSY_remind_dl02" id="buyContentMessage" style="display:<?php if($is_memberBuyMessage==0){echo "none";} ?>"> 
					<dt>下级会员购物消息（关闭购物内容）：</dt>
					<dd>
						<?php if($is_buyContentMessage==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_buyContentMessage(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_buyContentMessage(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_buyContentMessage(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_buyContentMessage(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_buyContentMessage" id="is_buyContentMessage" value="<?php echo $is_buyContentMessage; ?>" />
				</dl>
				<dl class="WSY_remind_dl02" style="width:800px;">
						<dt>佣金消息提示:</dt>
						<dd>
							<?php if($is_commission_message==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="change_is_commission_message(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="change_is_commission_message(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="change_is_commission_message(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="change_is_commission_message(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>
							<?php } ?>
						</dd>
						<div id="commission_scope"  style="<?php if($is_commission_message==0){echo "display:none;";}?>" >
						<dd >
							<span >所有人都提示</span><input type="radio" name="is_commission_scope" radiogroup="is_commission_scope" id="is_commission_scope" <?php if( $is_commission_scope == 0 ){?>checked<?php }?> value=0 >
							<span >推广员才提示</span><input type="radio" name="is_commission_scope" radiogroup="is_commission_scope" id="is_commission_scope" <?php if( $is_commission_scope == 1 ){?>checked<?php }?> value=1 >
						</dd>
						</div>
						<input type="hidden" name="is_commission_message" id="is_commission_message" value="<?php echo $is_commission_message; ?>" />
				</dl>
					
				<dl class="WSY_remind_dl02"> 
					<dt>商城显示下单提示</dt>
					<dd>
						<?php if($is_openOrderMessage==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_openOrderMessage(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_openOrderMessage(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_openOrderMessage(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_openOrderMessage(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_openOrderMessage" id="is_openOrderMessage" value="<?php echo $is_openOrderMessage; ?>" />
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
	document.getElementById("upform").submit();
}
</script>
<script>
function change_is_qrMessage(obj){
	$("#is_qrMessage").val(obj);
}
function change_is_openOrderMessage(obj){
	$("#is_openOrderMessage").val(obj);
}
function change_is_memberBuyMessage(obj){
	$("#is_memberBuyMessage").val(obj);
	hide(obj);
}
function change_is_buyContentMessage(obj){
	$("#is_buyContentMessage").val(obj);
}
function change_is_orderCommissionMessage(obj){
	$("#is_orderCommissionMessage").val(obj);
}
function change_is_commission_message(a){
	if (a==1) {
		$('#commission_scope').css("display","block");
	}else{
		$('#commission_scope').css("display","none");
	}
	$('#is_commission_message').val(a);
}
function hide(obj){//手续费、返购物币开关只能开一个动画
	if(obj==0){
		 change_is_buyContentMessage(0);
		 $('#buyContentMessage .WSY_bot').trigger('click');
		$('#buyContentMessage').hide();
	}else{
		$('#buyContentMessage').show();
	}		
}
</script>
</body>
</html>