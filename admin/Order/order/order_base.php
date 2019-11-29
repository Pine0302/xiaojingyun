<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=0;//头部文件0基本设置
$query = "select id from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$shop_id=$row->id;
}
$is_receipt		  = 0;//收货结算开关（默认关）
$is_orderActivist = 0;//订单售后维权开关:1、开；0、关
$is_deliverySettlement = 0;//虚拟发货自动收货结算开关：1.开 0.关（默认关）
$is_blessing   =0;//贺卡祝福语开关：1.开   0.关(默认关)
$query = "SELECT is_orderActivist,is_receipt,is_deliverySettlement,productend_tips,is_blessing,is_order_delay_check_auto,order_delay_time,is_auto_aftersale,is_auto_safeguard,auto_aftersale_time,auto_safeguard_time,is_auto_end,auto_end_time from weixin_commonshops_extend where isvalid=true and customer_id=".$customer_id." and shop_id=".$shop_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$is_receipt		  			= $row->is_receipt;
	$is_orderActivist 			= $row->is_orderActivist;
	$is_deliverySettlement 		= $row->is_deliverySettlement;
	$productend_tips  			= $row->productend_tips;
	$is_blessing      			= $row->is_blessing;
	$is_order_delay_check_auto 	= $row->is_order_delay_check_auto;
	$order_delay_time 			= $row->order_delay_time;
	$is_auto_aftersale 			= $row->is_auto_aftersale;
	$is_auto_safeguard 			= $row->is_auto_safeguard;
	$auto_aftersale_time 		= $row->auto_aftersale_time;
	$auto_safeguard_time 		= $row->auto_safeguard_time;
	$is_auto_end 				= $row->is_auto_end;
	$auto_end_time 				= $row->auto_end_time;
}
//echo $is_orderActivist."===".$is_receipt;
?>
<html>
<head>
<style type="text/css">
.WSY_remind_main{overflow:hidden;}
.divfloat{display:block;float:left;}
</style>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/shop_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>
<script charset="utf-8" src="../../../common/js/layer/V2_1/layer.js"></script>
<style>
	.HX_border{border:dashed 2px #797979;max-width:1000px;margin:24px 0 24px 32px;padding:10px;position:relative;overflow: inherit;}
    .HX_border .HX_title{position:absolute;height:30px;line-height:30px;background:#fbfbfb;font-weight:normal;font-size:20px;top:-15px;color:#333;}
    .w-auto{width: auto !important}
</style>

<title>基本设置</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			// include("../../../../weixinpl/back_newshops/Order/order/basic_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Order/order/basic_head.php");
		?>
		<form action="save_order_base.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
			<div class="WSY_remind_main">
				<div class="divfloat" style="width:100%;">
				<dl class="WSY_remind_dl02">
					<dt>开启订单售后维权：</dt>
					<dd>
						<?php if($is_orderActivist==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_orderActivist(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_orderActivist(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_orderActivist(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_orderActivist(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_orderActivist" id="is_orderActivist" value="<?php echo $is_orderActivist; ?>" />
				</dl>

				<dl class="WSY_remind_dl02"> 
					<dt>收货结算开关：<img style="width:12px;" id="package_mode" src="../../Common/images/Base/help.png"></dt>
					<dd>
						<?php if($is_receipt==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_receipt(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_receipt(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_receipt(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_receipt(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_receipt" id="is_receipt" value="<?php echo $is_receipt; ?>" />
				</dl>
				<!--郑培强-->
				<dl class="WSY_remind_dl02"> 
					<dt style="margin-left: -13px;">即将到期时间：</dt> 
					<dd style="width: 500px;">产品到期前<input type="text" style="width:50px;" id="productend_tips" name="productend_tips" value="<?php echo $productend_tips; ?>" >天  <img style="width:12px;" id="daoqi_mode" src="../../Common/images/Base/help.png"></dd>
				</dl>
				<!--郑培强-->
				<dl class="WSY_remind_dl02"  style="width:100%;">
					<dt>虚拟发货自动收货结算：<img style="width:12px;" id="delivery_tip" src="../../Common/images/Base/help.png"></dt>
					<dd>
						<?php if($is_deliverySettlement==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_deliverySettlement(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_deliverySettlement(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_deliverySettlement(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_deliverySettlement(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_deliverySettlement" id="is_deliverySettlement" value="<?php echo $is_deliverySettlement; ?>" />
				</dl>
                    <dl class="WSY_remind_dl02"  style="width:100%;">
                        <dt>商城贺卡：</dt>
                        <dd>
                            <?php if($is_blessing==1){ ?>
                                <ul style="background-color: rgb(255, 113, 112);">
                                    <p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
                                    <li onclick="change_is_blessing(0)" class="WSY_bot" style="left: 0px;"></li>
                                    <span onclick="change_is_blessing(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
                                </ul>
                            <?php }else{ ?>
                                <ul style="background-color: rgb(203, 210, 216);">
                                    <p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
                                    <li onclick="change_is_blessing(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
                                    <span onclick="change_is_blessing(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
                                </ul>
                            <?php } ?>
                        </dd>
                        <input type="hidden" name="is_blessing" id="is_blessing" value="<?php echo $is_blessing; ?>" />
                    </dl>
                    <dl class="WSY_remind_dl02"  style="width:100%;">
					<dt>延迟收货自动审核：</dt>
					<dd>
						<?php if($is_order_delay_check_auto==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_order_delay_check_auto(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_order_delay_check_auto(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_order_delay_check_auto(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_order_delay_check_auto(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_order_delay_check_auto" id="is_order_delay_check_auto" value="<?php echo $is_order_delay_check_auto; ?>" />
				</dl>
				<dl class="WSY_remind_dl02" id="orderDelayTime" style="<?php if($is_order_delay_check_auto==0){echo 'display:none;';} ?>"> 
					<dt style="margin-left: -13px;">自动延迟收货时间：</dt> 
					<dd style="width: 500px;"><input type="text" style="width:50px;" id="order_delay_time" name="order_delay_time" value="<?php echo $order_delay_time; ?>" >天</dd>
				</dl>
				<div class="WSY_remind_main HX_border">
					<h2 class="HX_title">合作商订单设置</h2>
					<dl class="WSY_remind_dl02"  style="width:100%;">
						<dt class='w-auto'>合作商售后订单申请是否自动同意：</dt>
						<dd>
							<?php if($is_auto_aftersale==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_auto_aftersale(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_auto_aftersale(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_is_auto_aftersale(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_auto_aftersale(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<dd id="auto_aftersale_time" style="<?php if($is_auto_aftersale==0){echo 'display:none;';} ?>">&nbsp;&nbsp;默认<input type="text" style="width:50px;" name="auto_aftersale_time" onkeyup="clearNoNums(this)" value="<?php echo $is_auto_aftersale ? $auto_aftersale_time : '7'; ?>" >天不处理，则会自动同意申请</dd>
						<input type="hidden" name="is_auto_aftersale" id="is_auto_aftersale" value="<?php echo $is_auto_aftersale; ?>" />
					</dl>
					<dl class="WSY_remind_dl02"  style="width:100%;">
						<dt class='w-auto'>买家填入退货单号，合作商是否自动确认收货：</dt>
						<dd>
							<?php if($is_auto_safeguard==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_auto_safeguard(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_auto_safeguard(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_is_auto_safeguard(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_auto_safeguard(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<dd id="auto_safeguard_time" style="<?php if($is_auto_safeguard==0){echo 'display:none;';} ?>">&nbsp;&nbsp;默认<input type="text" style="width:50px;" name="auto_safeguard_time" onkeyup="clearNoNums(this)" value="<?php echo $is_auto_safeguard ? $auto_safeguard_time : '30'; ?>" >天不处理，则会自动确认收货</dd>
						<input type="hidden" name="is_auto_safeguard" id="is_auto_safeguard" value="<?php echo $is_auto_safeguard; ?>" />
					</dl>
					<dl class="WSY_remind_dl02"  style="width:100%;">
						<dt class='w-auto'>合作商同意退货/换货申请，买家没有上传退货单号是否自动回到待收货状态：</dt>
						<dd>
							<?php if($is_auto_end==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_auto_end(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_auto_end(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_is_auto_end(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_auto_end(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<dd id="auto_end_time" style="<?php if($is_auto_end==0){echo 'display:none;';} ?>">&nbsp;&nbsp;默认<input type="text" style="width:50px;" name="auto_end_time" onkeyup="clearNoNums(this)" value="<?php echo $is_auto_end ? $auto_end_time : '10'; ?>" >天不上传退货单号，则会自动恢复到已发货状态</dd>
						<input type="hidden" name="is_auto_end" id="is_auto_end" value="<?php echo $is_auto_end; ?>" />
					</dl>
				</div>
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
	var is_auto_aftersale = $('#is_auto_aftersale').val();
	var is_auto_safeguard = $('[name="is_auto_safeguard"]').val();
	var is_auto_end = $('#is_auto_end').val();

	var auto_aftersale_time = parseInt($('[name="auto_aftersale_time"]').val());
	var auto_safeguard_time = parseInt($('[name="auto_safeguard_time"]').val());
	var auto_end_time = parseInt($('[name="auto_end_time"]').val());

	if( is_auto_aftersale && ( auto_aftersale_time==0 || auto_aftersale_time==NaN ) ){
		alert('合作商售后订单申请自动同意天数不能为空或0');
		return false;
	}
	if( is_auto_safeguard && ( auto_safeguard_time==0 || auto_safeguard_time==NaN ) ){
		alert('合作商自动确认收货天数不能为空或0');
		return false;
	}
	if( is_auto_end && ( auto_end_time==0 || auto_end_time==NaN ) ){
		alert('合作商自动恢复到已发货状态天数不能为空或0');
		return false;
	}

	document.getElementById("upform").submit();
}
function change_is_orderActivist(obj){
	$('#is_orderActivist').val(obj);
}
function change_is_receipt(obj){
	$('#is_receipt').val(obj);
}
function change_is_deliverySettlement(obj){
	$('#is_deliverySettlement').val(obj);
}
function change_is_blessing(obj){
    $('#is_blessing').val(obj);
}
function change_is_order_delay_check_auto(obj){
	$('#is_order_delay_check_auto').val(obj);
	if(obj==0){
		$('#orderDelayTime').hide();
	}
	if(obj==1){
		$('#orderDelayTime').show();
	}
}
function change_is_auto_aftersale(obj){
	$('#is_auto_aftersale').val(obj);
	if(obj==0){
		$('#auto_aftersale_time').hide();
	}
	if(obj==1){
		$('#auto_aftersale_time').show();
	}
}
function change_is_auto_safeguard(obj){
	$('#is_auto_safeguard').val(obj);
	if(obj==0){
		$('#auto_safeguard_time').hide();
	}
	if(obj==1){
		$('#auto_safeguard_time').show();
	}
}
function change_is_auto_end(obj){
	$('#is_auto_end').val(obj);
	if(obj==0){
		$('#auto_end_time').hide();
	}
	if(obj==1){
		$('#auto_end_time').show();
	}
}
function clearNoNums(obj){
    obj.value = obj.value.replace(/[^0-9]/g,""); //清除"数字"和"."以外的字符
}
/*郑培强*/
var productend_tips=$("#productend_tips").val();
if(!(/^(\+|-)?\d+$/.test( productend_tips )) || productend_tips<0){
	$("#productend_tips").val(0);
}
/*郑培强*/
/*郑培强*/
$('#daoqi_mode').on('click', function(){
	layer.tips('产品到期前多少天，该产品会被提示为即将到期','#daoqi_mode');
});
/*郑培强*/
/* 抢购产品提示 */
$('#package_mode').on('click', function(){
	layer.tips('开启：用户确认收货后，则自动进行确认完成订单流程，在此模式下完成订单则不给予用户提供维权服务,此状态以用户下单时为准!','#package_mode');
});

/* 虚拟发货提示 */
$('#delivery_tip').on('click', function(){
	layer.tips('开启：用户确认收货后，则自动进行确认完成订单流程，在此模式下完成订单则不给予用户提供维权服务,此状态以用户下单时为准!','#delivery_tip');
});

</script>
<script type="text/javascript" src="/weixinpl/back_newshops/Common/js/Base/check_shop.js"></script>
</body>
</html>