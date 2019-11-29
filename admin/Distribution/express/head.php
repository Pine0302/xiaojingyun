<?php 
header("Content-type: text/html; charset=utf-8");     
?>
<!--列表头部切换开始-->
<div class="WSY_column_header">
		<div class="WSY_columnnav">
			<a href="express_template.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>"  >运费模板设置</a>   
			<a href="express.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>"  >运费设置</a>   
			<a href="sf_import.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>">顺丰进口物流设置</a>
			<a href="express_company.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>">物流公司设置</a>
		</div>
</div> 

<script>
var header = <?php echo $header; ?>;
$(".WSY_columnnav").find("a").eq(header).addClass('white1');
</script> 
<!--列表头部切换结束-->
