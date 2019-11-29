<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<a href="documentation.php?customer_id=<?php echo $customer_id_en; ?>">入驻首页管理</a>
		<a href="document_management.php?customer_id=<?php echo $customer_id_en; ?>">入驻文档管理</a>
		<a href="settled_type.php?customer_id=<?php echo $customer_id_en; ?>">入驻分类</a>
		<a href="business_information.php?customer_id=<?php echo $customer_id_en; ?>">入驻商家</a>
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>