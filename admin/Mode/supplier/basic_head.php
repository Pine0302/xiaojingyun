<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<a href="set.php?customer_id=<?php echo $customer_id_en; ?>">基本设置</a>
		<a href="cash.php?customer_id=<?php echo $customer_id_en; ?>">提现记录</a>					
		<a href="supply.php?customer_id=<?php echo $customer_id_en; ?>">合作商管理</a>
		<a href="brand_supply.php?customer_id=<?php echo $customer_id_en; ?>">品牌合作商管理</a>
		<a href="shop_supply_user.php?customer_id=<?php echo $customer_id_en; ?>">手动添加合作商</a>
		<a href="album_manage.php?customer_id=<?php echo $customer_id_en; ?>">幻灯片管理</a>
		<?php if($_GET['detail']){?>
		<a href="supplycost_detail.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $_GET['user_id'];?>&istype=<?php echo $_GET['istype'];?>&detail=1">账目明细</a>
		<?php }?>
		<a href="area_set.php?customer_id=<?php echo $customer_id_en; ?>">区域批发商基本设置</a>
		<a href="category.php?customer_id=<?php echo $customer_id_en; ?>">区域批发商经营类目管理</a>
		<a href="area_supply.php?customer_id=<?php echo $customer_id_en; ?>">区域批发商管理</a>
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>