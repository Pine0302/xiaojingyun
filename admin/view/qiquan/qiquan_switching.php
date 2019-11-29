<?php 

$temp = array(
		'订单管理' 		=> '/mshop/admin/index.php?m=qiquan&a=qiquan_order_list&customer_id='.$customer_id,
		'推荐管理' 		=> '/mshop/admin/index.php?m=qiquan&a=qiquan_recommend_list&customer_id='.$customer_id,
		'支付方式'       => '/weixinpl/Base/pay_set/pay_switch.php?customer_id='.$customer_id.'&industry_type=optiondeal'
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