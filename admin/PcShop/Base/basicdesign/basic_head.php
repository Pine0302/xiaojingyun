<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<a href="base.php?customer_id=<?php echo $customer_id_en; ?>">基本设置</a>
		<a href="../../register_setting/register_set.php?customer_id=<?php echo $customer_id_en; ?>">注册设置</a>
		<!--<a href="cid4domain.php?customer_id=<?php echo $customer_id_en; ?>">域名绑定</a>-->
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>
