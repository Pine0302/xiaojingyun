<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<a href="order_base.php?customer_id=<?php echo $customer_id_en; ?>">基本设置</a>
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>