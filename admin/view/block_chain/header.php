<?php 

$temp = array(
        '区块链积分发放' 		=> '/mshop/admin/index.php?m=block_chain&a=integral_grant&customer_id='.$customer_id_en,
		'区块链积分日志' 		=> '/mshop/admin/index.php?m=block_chain&a=integral_log&customer_id='.$customer_id_en,
		'区块链积分明细' 		=> '/mshop/admin/index.php?m=block_chain&a=integral_details&customer_id='.$customer_id_en,
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