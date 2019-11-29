<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=0;//头部文件0支付方式，1微信支付,2支付宝,3财务通,4通联支付

$query = "select is_payChange,is_yeepay,is_pay,is_payother,iscard,isshop,isdelivery,is_alipay,is_weipay,is_tenpay,is_allinpay,is_paypal,is_unionpay,is_jdpay from customers where isvalid=true and id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$is_payother  =1; //代付开关
$isdelivery   =1; //货到付款支付开关
$isshop       =1; //到店支付开关
$iscard       =1; //会员卡余额支付开关
$is_pay       =1; //暂不支付开关
$is_payChange =1; //零钱支付开关
$is_alipay    =1; //支付宝开关
$is_weipay    =1; //微信支付开关
$is_tenpay    =1; //财务通开关
$is_allinpay  =1; //通联支付开关
$is_unionpay  =1; //银联支付开关
$is_paypal    =0;
$is_yeepay    =0;//易宝支付
$is_jdpay    =0;//京东支付
while ($row = mysql_fetch_object($result)) {
	$is_payother  = $row->is_payother;
	$isdelivery   = $row->isdelivery;
	$isshop       = $row->isshop;
	$is_pay       = $row->is_pay;
	$is_payChange = $row->is_payChange;
	$iscard       = $row->iscard;
	$is_alipay    = $row->is_alipay;
	$is_weipay    = $row->is_weipay;
	$is_tenpay    = $row->is_tenpay;
	$is_allinpay  = $row->is_allinpay;
	$is_paypal    =$row->is_paypal;
	$is_unionpay  =$row->is_unionpay;
	$is_yeepay  =$row->is_yeepay;
	$is_jdpay  =$row->is_jdpay;
}
$sql  = "SELECT isOpen,isvalid,isOpenCurrency,custom FROM weixin_commonshop_currency WHERE customer_id=".$customer_id;
$res  = _mysql_query($sql);
$isOpen 		= 0;
$custom 		= '';
$isOpenCurrency = 0;
while ($row = mysql_fetch_object($res) ){
	$isOpen 		= $row->isOpen;
	$custom 		= $row->custom;
	$isOpenCurrency	= $row->isOpenCurrency;
}
$type = $_GET['type'];


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


<title>支付方式</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php"); 
		?>
		<form action="save_pay_switch.php?customer_id=<?php echo $customer_id_en; ?>&type=<?php echo $type;?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
			<div class="WSY_remind_main">
				<div class="divfloat">
				<dl class="WSY_remind_dl02"> 
					<dt>会员卡余额支付：</dt>
					<dd>
						<?php if($iscard==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_iscard(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_iscard(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_iscard(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_iscard(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="iscard" id="iscard" value="<?php echo $iscard; ?>" />
				</dl>

				<dl class="WSY_remind_dl02"> 
					<dt><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>支付：</dt>
					<dd>
						<?php if($isOpen==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_currency(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_currency(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_currency(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_currency(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_currency" id="is_currency" value="<?php echo $isOpen; ?>" />
				</dl>

				<dl class="WSY_remind_dl02"> 
					<dt>零钱支付：</dt>
					<dd>
						<?php if($is_payChange==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_payChange(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_payChange(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_payChange(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_payChange(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_payChange" id="is_payChange" value="<?php echo $is_payChange; ?>" />
				</dl>
				<?php if($type != "city"){?>
				<!--<dl class="WSY_remind_dl02"> 
					<dt>到店支付：</dt>
					<dd>
						<?php if($isshop==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_isshop(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_isshop(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_isshop(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_isshop(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="isshop" id="isshop" value="<?php echo $isshop; ?>" />
				</dl>	-->
				
				<!--<dl class="WSY_remind_dl02"> 
					<dt>货到付款：</dt>
					<dd>
						<?php if($isdelivery==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_isdelivery(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_isdelivery(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_isdelivery(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_isdelivery(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="isdelivery" id="isdelivery" value="<?php echo $isdelivery; ?>" />
				</dl>-->
				

				<dl class="WSY_remind_dl02"> 
					<dt>找人代付：</dt>
					<dd>
						<?php if($is_payother==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_payother(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_payother(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_payother(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_payother(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_payother" id="is_payother" value="<?php echo $is_payother; ?>" />
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>暂不支付：</dt>
					<dd>
						<?php if($is_pay==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_pay(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_pay(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_pay(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_pay(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_pay" id="is_pay" value="<?php echo $is_pay; ?>" />
				</dl>
				
				<?php }?>

				</div>
				
				<div class="divfloat">
				<dl class="WSY_remind_dl02"> 
					<dt>微信支付：</dt>
					<dd>
						<?php if($is_weipay==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_weipay(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_weipay(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_weipay(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_weipay(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_weipay" id="is_weipay" value="<?php echo $is_weipay; ?>" />
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>支付宝：</dt>
					<dd>
						<?php if($is_alipay==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_alipay(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_alipay(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_alipay(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_alipay(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_alipay" id="is_alipay" value="<?php echo $is_alipay; ?>" />
				</dl>
				
				<dl class="WSY_remind_dl02"> 
                    <dt>易宝支付：</dt>
                    <dd> 
                        <?php if($is_yeepay==1){ ?>
                        <ul style="background-color: rgb(255, 113, 112);">
                            <p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
                            <li onclick="change_yeepay(0)" class="WSY_bot" style="left: 0px;"></li>
                            <span onclick="change_yeepay(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
                        </ul>
                        <?php }else{ ?>
                        <ul style="background-color: rgb(203, 210, 216);">
                            <p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
                            <li onclick="change_yeepay(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
                            <span onclick="change_yeepay(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
                        </ul>                       
                        <?php } ?>
                    </dd>
                    <input type="hidden" name="is_yeepay" id="is_yeepay" value="<?php echo $is_yeepay; ?>" />
                </dl>
				<?php if($type != "city"){?>
				<!--  8.0暂时不支持
				<dl class="WSY_remind_dl02"> 
					<dt>财付通：</dt>
					<dd>
						<?php if($is_tenpay==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_tenpay(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_tenpay(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_tenpay(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_tenpay(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_tenpay" id="is_tenpay" value="<?php echo $is_tenpay; ?>" />
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>通联支付：</dt>
					<dd> 
						<?php if($is_allinpay==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_allinpay(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_allinpay(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_allinpay(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_allinpay(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_allinpay" id="is_allinpay" value="<?php echo $is_allinpay; ?>" />
				</dl>
				<dl class="WSY_remind_dl02"> 
                    <dt>PayPal支付：</dt>
                    <dd> 
                        <?php if($is_paypal==1){ ?>
                        <ul style="background-color: rgb(255, 113, 112);">
                            <p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
                            <li onclick="change_paypal(0)" class="WSY_bot" style="left: 0px;"></li>
                            <span onclick="change_paypal(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
                        </ul>
                        <?php }else{ ?>
                        <ul style="background-color: rgb(203, 210, 216);">
                            <p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
                            <li onclick="change_paypal(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
                            <span onclick="change_paypal(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
                        </ul>                       
                        <?php } ?>
                    </dd>
                    <input type="hidden" name="is_paypal" id="is_paypal" value="<?php echo $is_paypal; ?>" />
                </dl>
				-->
				
				<!--<dl class="WSY_remind_dl02"> 
                    <dt>京东支付：</dt>
                    <dd> 
                        <?php if($is_jdpay==1){ ?>
                        <ul style="background-color: rgb(255, 113, 112);">
                            <p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
                            <li onclick="change_jdpay(0)" class="WSY_bot" style="left: 0px;"></li>
                            <span onclick="change_jdpay(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
                        </ul>
                        <?php }else{ ?>
                        <ul style="background-color: rgb(203, 210, 216);">
                            <p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
                            <li onclick="change_jdpay(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
                            <span onclick="change_jdpay(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
                        </ul>                       
                        <?php } ?>
                    </dd>
                    <input type="hidden" name="is_jdpay" id="is_jdpay" value="<?php echo $is_jdpay; ?>" />
                </dl>-->
                <?php }?>
                <!-- <dl class="WSY_remind_dl02"> 
					<dt>银联支付：</dt>
					<dd> 
						<?php if($is_unionpay==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_unionpay(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_unionpay(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_unionpay(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_unionpay(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_unionpay" id="is_unionpay" value="<?php echo $is_unionpay; ?>" />
				</dl> -->

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
<script type="text/javascript" src="../../Common/js/Base/pay_set/pay_switch.js"></script>
</body>
</html>