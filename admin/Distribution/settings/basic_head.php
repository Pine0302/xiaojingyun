<?php 
if($shop_level == 1){ ?>
	<!-- /weixinpl/config.php定义了shop_level 商城等级 1：默认高级微商城 2：精简微商城  -->
	
	<div class="WSY_column_header">
		<div class="WSY_columnnav">
			<a href="sendstyles.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>">配送方式</a>
			<a href="sendtimes.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>">送货时间</a>
		</div>
	</div>
	<script>
	var head = <?php echo $head; ?>;
	$(".WSY_columnnav").find("a").eq(head).addClass('white1');
	</script>

<?php }else if($shop_level == 2){ ?>

	<div class="WSY_column_header">
		<div class="WSY_columnnav">
			<a href="sendstyles.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>">配送方式</a>
		</div>
	</div>
	<script>
	var head = <?php echo $head; ?>;
	$(".WSY_columnnav").find("a").eq(head).addClass('white1');
	</script>

<?php } ?>