<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>块链积分奖励－兑换日志</title>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
	<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>

	<style type="text/css">
		.form-btn{width:auto!important;padding:0 10px!important;cursor:pointer;color:#fff!important;border:0!important;}
		.form-add-btn{display:inline-block;line-height:24px;border-radius:3px;}
		.table-btn{color:#fff;border:0;cursor:pointer;border-radius:3px;height:24px;padding:0 10px;font-size:12px;}


		.div_item{float:left;padding:10px;font-size:14px;}
		.div_item label{margin-left:5px;font-size:14px;}
		.div_item input{border:1px solid #ccc; border-radius: 2px;}
		.layui-layer-content button{float: left;margin-top: 56px;margin-bottom: 19px;width: 80px;height: 30px;}
		.xubox_title{background: none!important;}
		.xubox_title em{left: 0!important;text-align: center!important;width: 100%!important;}
		.WSY_previous{width: 110px!important;}
		.temp_data{margin-left: 20px;padding-left:10px;width: 600px;height:30px;line-height: 30px;border-radius: 5px;border: 1px solid #ccc;font-size: 16px;}
		.temp_data li{float: left;margin-right: 10px;}
		.del_one{height: 24px;border-radius: 3px;}
	</style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
	    <!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
				<?php $keyContent = '奖金池'; ?>
                <?php include_once('reward_header.php'); ?>
			</div>
		    <!--订单列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form style="display:block" method="get" action="/mshop/admin/index.php?m=block_chain&a=integral_reward_exchange_log">
						<input type="hidden" id="m" name="m" value="block_chain">
						<input type="hidden" id="a" name="a" value="integral_reward_exchange_log">
						<input type="hidden" name="bonus_id" id="bonus_id" value="<?php echo $bonus_id;?>">
						<ul class="WSY_search_q">
							<li>用户名：<input type="text" name="user_name" id="user_name" class="form_input" value="<?php echo $_GET['user_name'];?>" /></li>
							<li>用户编码：<input type="text" name="user_id" id="user_id" class="form_input" value="<?php echo $_GET['user_id'];?>"></li>	
							<li>兑换产品名称：<input type="text" name="product_name" id="product_name" class="form_input" value="<?php echo $_GET['product_name'];?>"></li>
							<li>兑换活动编码：<input type="text" name="activity_id" id="activity_id" class="form_input" value="<?php echo $_GET['activity_id'];?>"></li>	 			
							<li><input type="submit" class="WSY-skin-bg form-btn search" value="搜索"></li>
							<li><input type="button" class="WSY-skin-bg form-btn" value="导出" onclick="export_excel()"></li>
						</ul> 
					</form>
		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="8%" nowrap="nowrap" align="center">用户编码</th>
							<th width="8%" nowrap="nowrap" align="center">用户名</th>
							<th width="8%" nowrap="nowrap" align="center">兑换产品名称</th>
							<th width="8%" nowrap="nowrap" align="center">兑换数量</th>
							<th width="8%" nowrap="nowrap" align="center">产品金额</th>
							<th width="8%" nowrap="nowrap" align="center">兑换零钱总额</th>
							<th width="10%" nowrap="nowrap" align="center">兑换比例</th>
							<th width="5%" nowrap="nowrap" align="center">已扣除区块链积分</th>
							<th width="6%" nowrap="nowrap" align="center">兑换时间</th>
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $row){ ?>
								<tr>
									<td style="text-align:center;"><?php echo $row['user_id'];?></td>
									<td style="text-align:center;"><?php echo $row['user_name'];?></td>
									<td style="text-align:center;"><?php echo $row['product_name'];?></td>
									<td style="text-align:center;"><?php echo $row['product_num'];?></td>
									<td style="text-align:center;"><?php echo $row['product_price'];?></td>
									<td style="text-align:center;"><?php echo $row['product_money'];?></td>
									<td style="text-align:center;"><?php echo $row['proportion'];?></td>
									<td style="text-align:center;"><?php echo $row['jifeng'];?></td>
									<td style="text-align:center;"><?php echo $row['createtime'];?></td>
								</tr>
							<?php } ?>
						</tbody>
						
					</table>
				</div>
		        <!--翻页开始-->
		        <div class="WSY_page"></div>
		        <!--翻页结束-->
		    </div>
		</div>
	</div>
	<!--内容框架结束-->
</body>
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript">

var product_name = $("#product_name").val();//订单号
	user_id      = $("#user_id").val();//用户id
	user_name    = $("#user_name").val();//用户姓名
	bonus_id     = $("#bonus_id").val();//奖金池id
	activity_id  = $("#activity_id").val();

var param = "";
if(product_name!=""){
	param += "&product_name="+product_name;
}
if(user_id!=""){
	param += "&user_id="+user_id;
}
if(user_name!=""){
	param += "&user_name="+user_name;
}
if(bonus_id!=""){
	param += "&bonus_id="+bonus_id;
}
if(activity_id!=""){
	param += "&activity_id="+activity_id;
}
//分页 start
var pagenum = <?php echo $pagenum ?>;//当前页
var count =<?php echo $pageCount ?>;//总页数	
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
	var url="/mshop/admin/index.php?m=block_chain&a=integral_reward_exchange_log&pagenum="+p+param;	
	location.href = url;
   }
});



function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=block_chain&a=integral_reward_exchange_log&pagenum="+a+param;

		location.href = url;
	}
}
//分页 end

//限制输入数字START
function num(obj){
obj.value = obj.value.replace(/[^\d]/g,""); //清除"数字"和"."以外的字符
}
//限制输入数字END

//导出
function export_excel(){
	var url          = '/weixin/plat/app/index.php/Excel/block_chain_exchange_log_excel/customer_id/'+'<?php echo $customer_id;?>';
		user_id      = $('#user_id').val();
    	product_name = $('#product_name').val();
    	user_name    = $('#user_name').val();
    	bonus_id     = $('#bonus_id').val();
    	activity_id  = $('#activity_id').val();

	if( user_name != ''){
		url += '/user_name/'+user_name;
	}
	if( user_id != '' ){
		url += '/user_id/'+user_id;
	}
	if( product_name != '' ){
		url += '/product_name/'+product_name;
	}
	if( bonus_id != '' ){
		url += '/bonus_id/'+bonus_id;
	}
	if( activity_id !='' )
	{
		url += '/activity_id/'+activity_id;
	}

	document.location = url;
}
</script>
</html>