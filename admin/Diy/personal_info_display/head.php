<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<a href="personal_info_display.php?customer_id=<?php echo $customer_id_en; ?>">团队个人信息显示开关</a>
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>