<div class="WSY_column_header">
	<div class="WSY_columnnav_currency WSY_columnnav">
	<?php if($INDEX!=100){?>
		<a href="pay_currency.php?customer_id=<?php echo $customer_id_en; ?>"><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>设置</a>
        <a href="pay_currency_log.php?customer_id=<?php echo $customer_id_en; ?>"><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>日志</a>
        <a href="pay_currency_user.php?customer_id=<?php echo $customer_id_en; ?>"><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>充值</a>
	<?php }?>
	<?php if($INDEX==100){?>
	<a class="white1">微信支付</a>	
	<?php }?>
	</div>
</div>
<script>
//var head = <?php echo $head; ?>;
var currency_head = <?php echo $currency_head;?>;
$(".WSY_columnnav_currency").find("a").eq(currency_head).addClass('white1');
</script>