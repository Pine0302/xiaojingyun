<?php 
require_once('shoproom.php');
$pcshop = new Pcshop();
$customer_id_en = $pcshop->customer_id_en;
?>
<div class="WSY_column_header">
	<div class="WSY_columnnav">
	    <a href="banner.php?customer_id=<?php echo $customer_id_en; ?>">导航管理</a>
		<a href="category_management.php?customer_id=<?php echo $customer_id_en; ?>">分类管理</a>
		<a href="goods_add.php?customer_id=<?php echo $customer_id_en; ?>">产品管理</a>
		<!-- <a href="navigation_management.php?customer_id=<?php //echo $customer_id_en; ?>">导航管理</a> -->
		<a href="order_management.php?customer_id=<?php echo $customer_id_en; ?>">订单管理</a>
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>