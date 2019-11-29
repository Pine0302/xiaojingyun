<?php
	$type = $_GET['type'];//为city则表示城市商圈
?>
<div class="WSY_column_header">
	<div class="WSY_columnnav">
	<?php if($INDEX!=100){?>
		<a href="pay_switch.php?customer_id=<?php echo $customer_id_en; ?>&type=<?php echo $type;?>">支付方式</a>					
		<a href="weixinpay_set.php?customer_id=<?php echo $customer_id_en; ?>&type=<?php echo $type;?>">微信支付</a>	
		<a href="alipay_set.php?customer_id=<?php echo $customer_id_en; ?>&type=<?php echo $type;?>">支付宝</a>
		<?php if($type != "city"){?>				
        <a href="pay_currency.php?customer_id=<?php echo $customer_id_en; ?>"><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>支付</a>
		<a href="tenpay_set.php?customer_id=<?php echo $customer_id_en; ?>" style="display:none;">财付通</a>
		<a href="allinpay_set.php?customer_id=<?php echo $customer_id_en; ?>" style="display:none;">通联支付</a>
		<a href="paypal_set.php?customer_id=<?php echo $customer_id_en; ?>" style="display:none;">PayPal支付</a>
        <a href="unionpay_set.php?customer_id=<?php echo $customer_id_en; ?>" style="display:none;">银联支付</a>
		<a href="yeepay_set.php?customer_id=<?php echo $customer_id_en; ?>">易宝支付</a>
		<!--<a href="jdpay_set.php?customer_id=<?php echo $customer_id_en; ?>">京东支付</a>-->
		<?php }?>
        <!-- <a href="pay_currency_log.php?customer_id=<?php echo $customer_id_en; ?>">购物币日志</a>
        <a href="pay_currency_user.php?customer_id=<?php echo $customer_id_en; ?>">购物币充值</a> -->
	<?php }?>
	<?php if($INDEX==100){?>
	<a class="white1">微信支付</a>	
	<?php }?>
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>