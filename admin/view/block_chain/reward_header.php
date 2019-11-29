<?php 

$temp = array(
        '基本设置' 	=> '/mshop/admin/index.php?m=block_chain&a=integral_reward_setting&customer_id='.$customer_id_en,
		'奖金池' 		=> '/mshop/admin/index.php?m=block_chain&a=integral_reward_list&customer_id='.$customer_id_en,
		'活动管理' 	=> '/mshop/admin/index.php?m=block_chain&a=integral_reward_all_activity&bonus_id=-1&customer_id='.$customer_id_en,
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