<?php
	$c_name = "购物币";
	$c_sql  = "SELECT custom FROM weixin_commonshop_currency WHERE customer_id=".$customer_id." limit 1";
	$c_res  = _mysql_query($c_sql);
	while ($c_row = mysql_fetch_object($c_res) ){
		$c_name 		= $c_row->custom;
	}
?>
<div class="WSY_column_header">
	<div class="WSY_columnnav_currency WSY_columnnav">
	<?php if($INDEX!=100){?>
		<a href="pay_currency.php?customer_id=<?php echo $customer_id_en; ?>"><?php echo $c_name ?>设置</a>
        <a href="pay_currency_log.php?customer_id=<?php echo $customer_id_en; ?>"><?php echo $c_name ?>日志</a>
        <a href="pay_currency_user.php?customer_id=<?php echo $customer_id_en; ?>"><?php echo $c_name ?>充值</a>
		<a href="currency_recharge.php?customer_id=<?php echo $customer_id_en; ?>">充值卡</a>
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