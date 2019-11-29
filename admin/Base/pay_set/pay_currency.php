<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=7;//头部文件0支付方式，1微信支付,2支付宝,3财务通,4通联支付
$currency_head = 0;
$sql  = "SELECT isOpen,isvalid,isOpenCurrency,custom,rule,mini_limit,isOpenGiven FROM weixin_commonshop_currency WHERE customer_id=".$customer_id;
$res  = _mysql_query($sql);
$isOpen 		= 0;
$custom 		= '';
$isOpenCurrency = 1;
$isOpenGiven    = 0;
$mini_limit     = '';
while ($row = mysql_fetch_object($res) ){
	$isOpen 		= $row->isOpen;
	$custom 		= $row->custom;
	$isOpenCurrency	= $row->isOpenCurrency;
	$rule 			= $row->rule;
	$mini_limit 	= $row->mini_limit;
	$isOpenGiven 	= $row->isOpenGiven;
}


?>
<html>
<head>
<style type="text/css">
.WSY_remind_main{overflow:hidden;}
.divfloat{display:block;float:left;width:250px;}
.submit_div{float:left;margin-left:10%;}
</style>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/pay_set/pay_switch.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>

<style type="text/css">
	dd{
		float: left;
	}
	dt{
		float: left;
	}
	dl{
		float: left;
		margin-left: 20px;
	}
	#set_currency{
		width:500px;
	}

</style>
<title>支付方式</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			//include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php"); 
			include("../../../../weixinpl/back_newshops/Base/pay_set/currency_head.php"); 
		?>
		<form action="save_currency.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
			<div class="WSY_remind_main">
				<div class="divfloat">
				
				<dl class="WSY_remind_dl02" id="set_currency" > 
					<dt>使用<?php echo $custom;?>支付参与奖励：</dt>
					<dd>
						<?php if($isOpenCurrency==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_currency(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_currency(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_currency(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_currency(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<img style="width:12px;position: absolute;margin-top: 5px;" id="openCurrency" src="../../Common/images/Base/help.png">
					<input type="hidden" name="currency" id="currency" value="<?php echo $isOpenCurrency; ?>" />
				</dl>

				<dl class="WSY_remind_dl02" id="set_currency" > 
					<dt><?php echo $custom;?>转赠开关：</dt>
					<dd>
						<?php if($isOpenGiven==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_currency_given(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_currency_given(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_currency(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_currency_given(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="currency_given" id="currency_given" value="<?php echo $isOpenGiven; ?>" />
				</dl>

				<dl class="WSY_remind_dl02" id="set_currency" style="height:auto;" > 
					<dt><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>转赠规则说明：</dt>
					<dd>
						<textarea style="border-radius: 5px; border:1px solid #dadada; " rows="6" cols="40" name="rule" value="<?php echo $rule?>"><?php echo $rule?></textarea>
					</dd>
					<!-- <input type="hidden" name="custom" id="custom" value="<?php echo $custom; ?>" /> -->
				</dl>

				<dl class="WSY_remind_dl02" id="set_currency" > 
					<dt>购物币自定义名：</dt>
					<dd>
						<input type="text" id="custom_name" name="custom" value="<?php echo $custom; ?>" style="width:60px;height:20px;border:1px solid #ccc;margin-top:2px;">
					</dd>
					<!-- <input type="hidden" name="custom" id="custom" value="<?php echo $custom; ?>" /> -->
				</dl>

				<dl class="WSY_remind_dl02" id="set_currency" > 
					<dt><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>转赠限制：</dt>
					<dd>
						<input type="text" name="limit_currency" value="<?php echo $mini_limit; ?>" style="width:60px;height:20px;border:1px solid #ccc;margin-top:2px;">
						(<span style="color:red;">即转赠后余额不得少于此限制</span>)
					</dd>
					<!-- <input type="hidden" name="custom" id="custom" value="<?php echo $custom; ?>" /> -->
				</dl>

				</div>
				
				
			</div> 
		</form>
		<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;">
			<input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);"/>
		</div>
	</div>
</div> 
<script type="text/javascript" src="../../Common/js/Base/basicdesign/ToolTip.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../Common/js/Base/pay_set/pay_switch.js"></script>
<script>
/* 抢购产品提示 */
$('#openCurrency').on('click', function(){
	layer.tips('开启：有使用购物币支付，订单正常奖励；关闭：有使用购物币支付订单不参与奖励','#openCurrency');
});
</script>
</body>
</html>