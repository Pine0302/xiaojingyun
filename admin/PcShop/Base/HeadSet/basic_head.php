<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<a href="../../categorie/all_categories.php?customer_id=<?php echo $customer_id_en; ?>">首页分类设置</a>
		<a href="../index_custom/custom_control.php?customer_id=<?php echo $customer_id_en; ?>">自定义模板</a>
		<a href="../FootSet/foot_support.php?customer_id=<?php echo $customer_id_en; ?>">尾部设置</a>
		<a href="head_support.php?customer_id=<?php echo $customer_id_en; ?>">头部设置</a>
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>