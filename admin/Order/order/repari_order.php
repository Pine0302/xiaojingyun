<?php
header("Content-type: text/html; charset=utf-8"); 
 require('../../../../weixinpl/config.php');   //配置
require('../../../../weixinpl/customer_id_decrypt.php');   //解密参数



$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");



	
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
</head>
<body>
<form id="form" action="repari_order.class.php" method="post" enctype="multipart/form-data">
	customer_id：<input type="text" name="customer_id" />
	
	batchcode：<input type="text" name="batchcode" style="width:250px"/>
	
	执行方法：<select type="text" name="op" >
		<option value="-1">请选择</option>
		<option value="GetSupply_Money">供应商订单重新入账</option>
	</select>
	<input type="button" id="sub" value="提交"/>
</form>
<script>


$(function(){
		$('#sub').click(function(){
		var customer_id 	=  $('input[name="customer_id"]').val();
		var batchcode 		=  $('input[name="batchcode"]').val();
		var op 				=  $('select').val();
		if(customer_id == ''){
			alert('请输入customer_id！');
			return;
		}
		if(batchcode == ''){
			alert('请输入batchcode！');
			return;
		}
		if(op == -1){
			alert('请选择执行方法');
			return;
		}
		var str = '';
		if(op == 'GetSupply_Money'){
			str = '请再次确认该订单已经被确认过！';
		}
		var stu = confirm(str);
		console.log(customer_id);
		console.log(batchcode);
		console.log(op);
		if(stu){
			$('#form').submit();
		}
		
	});
});

</script>
</body>
</html>