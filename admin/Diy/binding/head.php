<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<a href="binding.php?customer_id=<?php echo $customer_id_en; ?>">网页注册和微信端绑定</a>
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>