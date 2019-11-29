<?php 
header("Content-type: text/html; charset=utf-8");     
?>
<!--列表头部切换开始-->
<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<a href="services.php?customer_id=<?php echo $customer_id_en;?>">所有申请</a>
		<a href="exchange.php?customer_id=<?php echo $customer_id_en;?>">换货申请</a>
		<a href="refund.php?customer_id=<?php echo $customer_id_en;?>">退款申请</a>
		<a href="return.php?customer_id=<?php echo $customer_id_en;?>">退货申请</a>				
		<a href="completion.php?customer_id=<?php echo $customer_id_en;?>">处理完成的申请</a>
	</div>
</div><!--列表头部切换结束-->
<?php ?>
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>