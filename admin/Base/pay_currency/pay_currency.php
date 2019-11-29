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
$sql  = "SELECT isOpen,isvalid,isOpenCurrency,custom,rule,mini_limit,isOpenGiven,is_rebate_open,rebate_user FROM weixin_commonshop_currency WHERE customer_id=".$customer_id." limit 1";
$res  = _mysql_query($sql);
$isOpen 		= 0;
$custom 		= '';
$isOpenCurrency = 1;
$isOpenGiven    = 0;
$mini_limit     = '';
$is_rebate_open = 1;
$rebate_user    = 2;
while ($row = mysql_fetch_object($res) ){
	$isOpen 		= $row->isOpen;
	$custom 		= $row->custom;
	$isOpenCurrency	= $row->isOpenCurrency;
	$rule 			= $row->rule;
	$mini_limit 	= $row->mini_limit;
	$isOpenGiven 	= $row->isOpenGiven;
	$is_rebate_open = $row->is_rebate_open;
	$rebate_user 	= $row->rebate_user;
}

$percentage = 1;//默认100%
$sql2  = "SELECT percentage FROM currency_percentage_t WHERE isvalid=true and type=1 and customer_id=".$customer_id." limit 0,1";
$res2  = _mysql_query($sql2);
while ($row2 = mysql_fetch_object($res2) ){
	$percentage 		= $row2->percentage;
}

$percentage = $percentage*100;

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
<script src="../../../common/utility.js"></script>

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
			//include("../../../../weixinpl/back_newshops/Base/pay_currency/pay_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/pay_currency/currency_head.php"); 
		?>
		<form action="save_currency.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
			<div class="WSY_remind_main">
				<div class="divfloat">
                
                <dl class="WSY_remind_dl02" id="set_currency" > 
					<dt><?php echo $custom;?>抵扣开关：</dt>
					<dd>
						<?php if($isOpen==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_currency_open(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_currency_open(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_currency_open(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_currency_open(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_currency" id="is_currency" value="<?php echo $isOpen; ?>" />
				</dl>
				
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

				<dl class="WSY_remind_dl02" id="set_currency"> 
					<dt>返赠<?php echo $custom;?>开关：</dt>
					<dd>
 						<?php if($is_rebate_open==1){ ?> 
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_rebate_open(0)" class="WSY_bot"  style="left: 0px;"></li>
							<span onclick="set_rebate_open(1)" class="WSY_bot2"  style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_rebate_open(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_rebate_open(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>					
						<?php } ?> 
					</dd>
					<input type="hidden" name="is_rebate_open" id="is_rebate_open" value="<?php echo $is_rebate_open; ?>" />
				</dl>
				<dl class="WSY_remind_dl02" id="rebate_user_div" style="width: 5000px;<?php if($is_rebate_open=='0'){ echo "display:none"; }?>" > 
					<dt>返赠对象：</dt>
						<dd>
							<input type="radio"  id="rebate_user" name="rebate_user" <?php if($rebate_user==1){ ?>checked<?php } ?> value="1">购买者&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="radio"  id="rebate_user" name="rebate_user" <?php if($rebate_user==2){ ?>checked<?php } ?> value="2">推广员
							<img style="width:12px;margin-top: 5px;" id="rebate" src="../../Common/images/Base/help.png">
						</dd>
				</dl>

				<dl class="WSY_remind_dl02" id="set_currency" style="height:auto;" > 
					<dt><?php echo $custom;?>转赠规则说明：</dt>
					<dd>
						<textarea style="border-radius: 5px; border:1px solid #dadada;resize: none; " rows="6" cols="40" name="rule" value="<?php echo $rule?>"><?php echo $rule?></textarea>
					</dd>
					<!-- <input type="hidden" name="custom" id="custom" value="<?php echo $custom; ?>" /> -->
				</dl>

				<dl class="WSY_remind_dl02" id="set_currency" > 
					<dt>购物币自定义名：</dt>
					<dd>
						<input type="text" id="custom_name" name="custom" value="<?php echo $custom; ?>" maxlength="6" style="width:100px;height:20px;border:1px solid #ccc;margin-top:2px;">
					(<span style="color:red;">最多输入六个字符</span>)
					</dd>
					<!-- <input type="hidden" name="custom" id="custom" value="<?php echo $custom; ?>" /> -->
				</dl>

				<dl class="WSY_remind_dl02" id="set_currency" > 
					<dt><?php echo $custom;?>转赠限制：</dt>
					<dd>
						<input type="text" name="limit_currency" value="<?php echo $mini_limit; ?>" style="width:60px;height:20px;border:1px solid #ccc;margin-top:2px;">
						(<span style="color:red;">即转赠后余额不得少于此限制</span>)
					</dd>
					<!-- <input type="hidden" name="custom" id="custom" value="<?php echo $custom; ?>" /> -->
				</dl>
                
                <dl class="WSY_remind_dl02" id="set_currency" style="width: 1000px;"> 
					<dt><?php echo $custom;?>抵扣比例（线上）：</dt>
					<dd>
						<input type="text" id="percentage" name="percentage" value="<?php echo $percentage; ?>" style="width:60px;height:20px;border:1px solid #ccc;margin-top:2px;" onkeyup="clearNoNum(this,2);">%
						(<span style="color:red;">可填0-100的百分比数，最多可支持小数点后两位，如1.01%</span>)
					</dd>
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

$('#rebate').on('click', function(){
	layer.tips('购买者非推广员身份，则购物币返给其直接上级，若其上级非推广员身份，则两人都不返赠。','#rebate');
});

$("#percentage").on("blur",function(){
    var percentage = $(this).val();
    if(isNaN(percentage) || (parseFloat(percentage) < 0 || parseFloat(percentage) > 100)){
        alert("请输入正确的购物币抵扣比例！");
        $('#percentage').val('0');
        return;
    }
}); 

function set_rebate_open(obj){
	$("#is_rebate_open").val(obj);
	if(obj==0){
		$("#rebate_user_div").hide();
	}else{
		$("#rebate_user_div").show();
	}
}

</script>
</body>
</html>