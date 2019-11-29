<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<a href="set.php?customer_id=<?php echo $customer_id_en; ?>">基本设置</a>
		<a href="cash.php?customer_id=<?php echo $customer_id_en; ?>">提现记录</a>					
		<a href="agent.php?customer_id=<?php echo $customer_id_en; ?>">代理商管理</a>
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>