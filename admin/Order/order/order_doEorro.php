<!doctype html>
<?php  
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');   //配置
require('../../../../weixinpl/customer_id_decrypt.php');   //解密参数
require('../../../../weixinpl/back_init.php');
	
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
require('../../../../weixinpl/common/utility.php');
?>
<html>
<head>
<meta charset="utf-8">
<title>订单管理</title>

<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Order/orders/order.css">
<link rel="stylesheet" href="percent/jquery.percentageloader.0.2.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script charset="utf-8" src="../../../common/js/layer/V2_1/layer.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script> 
<script src="../../../common_shop/jiushop/js/region_select.js"></script> 
<script type="text/javascript" src="../../Common/js/Order/order/order.js"></script>

<script type="text/javascript" src="../../Distribution/express/js/LodopFuncs.js"></script>
<script type="text/javascript" src="../../Distribution/express/js/print_delivery.js"></script>


<script src="percent/jquery.percentageloader.0.2.js"></script>


<style>
body{
	    background: #fff
}
.left{
	float:left;
	width:30%;
}
.right{
	float:left;
}
</style>
</head>

<body>
<div class="left">
	<input type="button" value="点击筛选可能有问题的订单" onclick="showbatchcode();">
	<ul class="list">
	</ul>
</div>
<div class="right">
	订单号：<input type="text" name="batchcode" id="batchcode" value="">
	<input type="button" value="提交" onclick="tijiao();">
</div>



</body>
</html>
<script>
var customer_id = "<?php echo passport_encrypt($customer_id);?>";

function showbatchcode(){
	var html ="";
	$.ajax({
		url: "order_doEorro.class.php",
		type:"POST",
		data:{'op':"list","customer_id":customer_id},
		dataType:"json",
		success: function(res){
			if(res.status==1){ 	
				var batchcode_arr = res.msg;
				for(var i =0; i < batchcode_arr.length;i++){
					var dd = batchcode_arr[i];
					html += "<li>"+batchcode_arr[i]+"</li>";
				}
				$(".list").append(html);
			}
		},	
		error:function(){
			alert("网络错误请检查网络");
		}						
	});
}

function tijiao(){
	var batchcode = $("#batchcode").val();
	$.ajax({
		url: "order_doEorro.class.php",
		type:"POST",
		data:{'op':"pay",'batchcode':batchcode,"customer_id":customer_id},
		dataType:"json",
		success: function(res){
			if(res.status==1){ 	
				alert("单号："+batchcode+"支付完成");
			}else{
				alert(res.msg);
			}
			
		},	
		error:function(){
			alert("网络错误请检查网络");
		}						
	});	
}
</script>