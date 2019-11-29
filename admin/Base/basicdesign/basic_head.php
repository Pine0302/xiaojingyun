<?php 
if($shop_level == 1){ ?>
	<!-- /weixinpl/config.php定义了shop_level 商城等级 1：默认高级微商城 2：精简微商城  -->

	<?php 
	$type = isset($_GET['type'])?$_GET['type']:'';
	if($type=='cityarea') {
		$head = 0;
	?>
	<div class="WSY_column_header">
		<div class="WSY_columnnav">
			<a href="share.php?customer_id=<?php echo $customer_id_en; ?>&type=cityarea">分享设置</a>                 
		</div>
	</div>
	<?php }else{?>
	<div class="WSY_column_header">
		<div class="WSY_columnnav">
			<a href="base.php?customer_id=<?php echo $customer_id_en; ?>">商城资料</a>
			<a href="share.php?customer_id=<?php echo $customer_id_en; ?>">分享设置</a>					
			<a href="shop_set.php?customer_id=<?php echo $customer_id_en; ?>">购物设置</a>
			<a href="message_prompt.php?customer_id=<?php echo $customer_id_en; ?>">消息提示</a>
			<a href="limit_ad.php?customer_id=<?php echo $customer_id_en; ?>">限时广告图管理</a>
			<a href="guide_set.php?customer_id=<?php echo $customer_id_en; ?>">引导提示</a>
		</div>
	</div>
	<?php }?>
	<script>
	var head = <?php echo $head; ?>;
	$(".WSY_columnnav").find("a").eq(head).addClass('white1');
	</script>

<?php }else if($shop_level == 2){ ?>

		<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a href="base.php?customer_id=<?php echo $customer_id_en; ?>">商城资料</a>
				<a href="share.php?customer_id=<?php echo $customer_id_en; ?>">分享设置</a>					
				<a href="shop_set.php?customer_id=<?php echo $customer_id_en; ?>">购物设置</a>
			</div>
		</div>
		<script>
		var head = <?php echo $head; ?>;
		$(".WSY_columnnav").find("a").eq(head).addClass('white1');
		</script>

<?php } ?>