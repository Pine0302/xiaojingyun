<?php 
header("Content-type: text/html; charset=utf-8");     
?>
<!--列表头部切换开始-->
<div class="WSY_column_header">
		<div class="WSY_columnnav">
			<a href="system_feedback_list.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>"  >系统反馈列表</a>   
		</div>
</div> 

<script>
var header = <?php echo $header; ?>;
$(".WSY_columnnav").find("a").eq(header).addClass('white1');
</script> 
<!--列表头部切换结束-->
