<?php 

$temp = array(
		'基本信息' 		=> '/mshop/admin/index.php?m=yundian&a=shop_setting_list',
		'店头背景' 		=> '/mshop/admin/index.php?m=yundian&a=background_of_store&customer_id='.$customer_id_en,
		'支付方式' 		=> '/weixinpl/Base/pay_set/pay_switch.php?industry_type=yundian&customer_id='.$customer_id_en,
/*		'店主编辑' 		=> '',*/
		'店主审核'      => '/mshop/admin/index.php?m=yundian&a=shopkeeper_review_list&customer_id='.$customer_id_en,
		'店主列表' 		=> '/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_list',
		'店主商品列表'  => '/mshop/admin/index.php?m=yundian&a=shopkeeper_order_list',
		'订单管理' 		=> '/mshop/admin/index.php?m=yundian&a=yundian_order_list',
		'操作日志' 		=> '/mshop/admin/index.php?m=yundian&a=yundian_setting_log',
);


?>

<div class="WSY_columnnav">
	<?php foreach ($temp as $key => $value) {?>
    	<?php if ($key == $keyContent) {?>
    		<a class="white1" href="<?php echo $value; ?>"><?php echo $key; ?></a>
    	<?php }else{ ?>
    		<a  href="<?php echo $value; ?>"><?php echo $key; ?></a>
    	<?php } ?>
	<?php } ?>
</div>             

</body>
</html>