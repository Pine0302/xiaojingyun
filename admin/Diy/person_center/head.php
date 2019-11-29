<?php
header("Content-type: text/html; charset=utf-8"); 
$link2 = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$diy_count=0;//判断渠道是否开启自定义模板
$sp_query="select count(1) as diy_count from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='个人中心自定义模板' and c.id=cf.column_id";
$sp_result = _mysql_query($sp_query) or die('W_is_diy Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($sp_result)) {
   $diy_count = $row->diy_count;
   break;
}
	

?>
<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<a href="/weixinpl/back_newshops/Diy/person_center/order_setting/order_setting.php?customer_id=<?php echo $customer_id_en; ?>">多种订单开关管理</a>
		<a href="/weixinpl/back_newshops/Diy/person_center/order_setting/personal_center.php?customer_id=<?php echo $customer_id_en; ?>">信息显示设置</a>
		<?php if($diy_count>0){?>
		<a href="/mshop/admin/index.php?m=personal_center&a=diy_template_list&customer_id=<?php echo $customer_id_en; ?>&pagenum=1">个人中心自定义</a>
		<?php } ?>
	</div>
</div>
<script>
var pageindex = <?php echo $pageindex; ?>;
$(".WSY_columnnav").find("a").eq(pageindex).addClass('white1');
</script>