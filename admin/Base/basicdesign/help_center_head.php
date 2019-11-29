<?php 
header("Content-type: text/html; charset=utf-8");     
?>
<!--列表头部切换开始-->
<div class="WSY_column_header">
		<div class="WSY_columnnav">
		<a href="help_center.php?customer_id=<?php echo $customer_id_en; ?>">分类管理</a>				
		<a href="article_management.php?customer_id=<?php echo $customer_id_en; ?>">文章管理</a>
		</div>
</div> 

<script>
var header = <?php echo $header; ?>;
$(".WSY_columnnav").find("a").eq(header).addClass('white1');
</script> 
<!--列表头部切换结束-->