<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<a href="foot_support.php?customer_id=<?php echo $customer_id_en; ?>">A区设置</a>
		<!-- <a href="cash_being.php?customer_id=<?php echo $customer_id_en; ?>">待提现列表</a>		 -->			
<!-- 		<a href="shop_set.php?customer_id=<?php echo $customer_id_en; ?>">零钱后台充值</a>
		<a href="recharge_log.php?customer_id=<?php echo $customer_id_en; ?>">零钱日志</a> -->
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>


