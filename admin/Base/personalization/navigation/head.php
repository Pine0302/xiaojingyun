
<div class="WSY_column_header">  
	<div class="WSY_columnnav">
		<a  href="setting.php?customer_id=<?php echo $customer_id_en; ?>" >导航设置</a>
		<a  href="style_setting.php?customer_id=<?php echo $customer_id_en; ?>" >导航样式设置</a>
        <a  href="/mshop/admin/index.php?m=navigation&a=template_list&customer_id=<?php echo $customer_id_en; ?>" >新导航设置</a>
    </div>
</div>  
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>