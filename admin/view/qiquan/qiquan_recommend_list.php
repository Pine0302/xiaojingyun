<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>期权交易－推荐列表</title>
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

		/*推荐弹出框*/
		.add_qiquan_recommend_box,.edit_qiquan_recommend_box{border-radius:3px;border:1px solid #333;width:470px;height:360px;background: #fff;position: absolute;transform: translate(-50%,-50%);position: fixed;top:50%;left:50%;display: none;}
		.add_qiquan_recommend_box p,.edit_qiquan_recommend_box p{font-size:15px;height:30px;line-height:30px;text-align:right;width:90%;margin:9px auto;}
		.add_qiquan_recommend_box p input,.edit_qiquan_recommend_box p input{width: 75%;height:30px;border:solid 1px rgb(153, 153, 153);}
	</style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
	    <!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
				<?php $keyContent = '推荐管理'; ?>
                <?php include 'qiquan_switching.php'; ?>
			</div>
		    <!--店主列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
		    		<button type="button" class="WSY_button" onclick="add_qiquan_recommend_box()" style="float: right;border-radius: 5px;border:none;margin: 20px 30px 20px 0px;font-size: 13px;    width: 75px;">添加推荐</button>
		    		<!-- 添加推荐框  start-->
		    		<div class="add_qiquan_recommend_box">
		    			<h2 style="margin:20px;text-align: center;font-size: 23px;font-weight: normal;">添加推荐</h2>
		    			<p>股票代码：<input type="text" class="add_stock_code"></p>
		    			<p>收益率(%)：<input type="text" class="add_yield_rate"></p>
		    			<p>买价：<input type="text" class="add_buy_price"></p>
		    			<p>卖价：<input type="text" class="add_sale_price"></p>
		    			<p>盈亏：<input type="text" class="add_profit_loss"></p>
		    			<p>数量：<input type="text" class="add_num"></p>
		    			<p style="margin-top:10px;text-align: center;"><button type="button" style="background:#fff;border-radius: 5px;border-radius: 5px;border:#666 solid 1px;padding:10px 30px;font-size: 16px;color:#333;" onclick="add_cancel()">取消</button>
		    			<button type="button" style="background-color: rgba(22, 155, 213, 1);border-radius: 5px;border:none;padding:11px 30px;color:#fff;font-size: 16px;margin-left:30px;" onclick="add_qiquan_recommend_data()">保存</button></p>
		    		</div>
		    		<!-- 添加推荐框  end-->
		    		
		    		<!-- 编辑推荐框  start-->
		    		<div class="edit_qiquan_recommend_box">
		    			<h2 style="margin:20px;text-align: center;font-size: 23px;font-weight: normal;">编辑推荐</h2>
		    			<p>股票代码：<input type="text" class="edit_stock_code"></p>
		    			<p>收益率(%)：<input type="text" class="edit_yield_rate"></p>
		    			<p>买价：<input type="text" class="edit_buy_price"></p>
		    			<p>卖价：<input type="text" class="edit_sale_price"></p>
		    			<p>盈亏：<input type="text" class="edit_profit_loss"></p>
		    			<p>数量：<input type="text" class="edit_num"></p>
		    			<input type="hidden" class="qiquan_recommend_id">
		    			<p style="margin-top:10px;text-align: center;"><button type="button" style="background:#fff;border-radius: 5px;border-radius: 5px;border:#666 solid 1px;padding:10px 30px;font-size: 16px;color:#333;" onclick="edit_cancel()">取消</button>
		    			<button type="button" style="background-color: rgba(22, 155, 213, 1);border-radius: 5px;border:none;padding:11px 30px;color:#fff;font-size: 16px;margin-left:30px;" onclick="edit_qiquan_recommend_data()">保存</button></p>
		    		</div>
		    		<!-- 编辑推荐框  end-->
		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="10%" nowrap="nowrap"align="center">ID</th>
							<th width="10%" nowrap="nowrap"align="center">股票代码</th>
							<th width="10%" nowrap="nowrap"align="center">股票名称</th>
							<th width="10%" nowrap="nowrap"align="center">收益率</th>
							<th width="10%" nowrap="nowrap"align="center">买价</th>
							<th width="10%" nowrap="nowrap"align="center">卖价</th>
							<th width="10%" nowrap="nowrap"align="center">盈亏</th>
							<th width="10%" nowrap="nowrap"align="center">数量</th>
							<th width="6%" nowrap="nowrap"align="center">添加时间</th>
							<th width="8%" nowrap="nowrap"align="center">操作</th>
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $key => $row) { ?>
								<tr>
									<td style="text-align:center;"><?=$row['id']?></td>
									<td style="text-align:center;"><?=$row['stock_code']?></td>
									<td style="text-align:center;"><?=$row['name']?></td>
									<td style="text-align:center;"><?=$row['yield_rate']?>%</td>
									<td style="text-align:center;"><?=$row['buy_price']?></td>
									<td style="text-align:center;"><?=$row['sale_price']?></td>
									<td style="text-align:center;"><?=$row['profit_loss']?></td>
									<td style="text-align:center;"><?=$row['num']?></td>
									<td style="text-align:center;"><?=$row['createtime']?></td>
									<td style="text-align:center;">
										<button class="table-btn WSY-skin-bg" onclick="edit_qiquan_recommend_box(<?=$row['id']?>)">编辑</button>
										<button class="table-btn WSY-skin-bg" onclick="delete_qiquan_recommend(<?=$row['id']?>)">删除</button>
									</td>
								</tr>
							
							<?php }?>					
						</tbody>
						
					</table>
				</div>

				
		        <!--翻页开始-->
		        <div class="WSY_page">
		        	
		        </div>
		        <!--翻页结束-->
		    </div>
		    <!--产品管理代码结束-->
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	<!--内容框架结束-->
</body>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script><!--添加时间插件-->
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript">
//验证规则
var stock_code_rule = /^\b\d{6}\b$/; //股票编码
var int_rule    = /^([1-9][0-9]*)$/; //数量
var float_rule  = /^(0|(0|[1-9][0-9]*)+(\.\d{2})?)$/;//买价卖价
var yield_rule  = /^([+-]?(0|([1-9]\d*))+(\.\d{2})?)$/;//收益率
//前端正则验证
function check_rule(stock_code,yield_rate,buy_price,sale_price,profit_loss,num)
{
	if( stock_code != "" && yield_rate != "" && buy_price != "" && sale_price != "" && profit_loss != "" && num != "" )
	{
		if(!stock_code_rule.test(stock_code)){ 
			alert("对不起，您输入的股票编码格式不正确！请重新输入");
			return false;
		}
		if(!yield_rule.test(yield_rate)){ 
			alert("对不起，您输入的收益率格式不正确！请重新输入");
			return false;
		}
		if(!float_rule.test(buy_price)){ 
			alert("对不起，您输入的买价格式不正确！请重新输入");
			return false;
		}
		if(!float_rule.test(sale_price)){ 
			alert("对不起，您输入的卖价格式不正确！请重新输入");
			return false;
		}
		if(!yield_rule.test(profit_loss) ){ 
			alert("对不起，您输入的盈亏格式不正确！请重新输入"); 
			return false;
		}
		
		if( !int_rule.test(num) ){ 
			alert("对不起，您输入的数量格式不正确！请重新输入");
			return false;
		}
		return true;
	}else{
		alert('所有项必选填写！');
		return false;
	}
}

//点击添加推荐按钮
function add_qiquan_recommend_box()
{
	$(".add_qiquan_recommend_box").find("input").attr("value","");
	$(".add_qiquan_recommend_box").show();
}
//添加框 点击取消按钮
function add_cancel(){
	$(".add_qiquan_recommend_box").hide();
}

//点击编辑推荐按钮
function edit_qiquan_recommend_box(id)
{
	$.ajax({
			type: "post",
			url:'/mshop/admin/index.php?m=qiquan&a=getone_qiquan_recommend',
			data:{'id':id},
			dataType:"json",
			success:function(res){
					$(".edit_stock_code").attr('value',res.stock_code);
					$(".edit_yield_rate").attr('value',res.yield_rate);
					$(".edit_buy_price").attr('value',res.buy_price);
					$(".edit_sale_price").attr('value',res.sale_price);
					$(".edit_profit_loss").attr('value',res.profit_loss);
					$(".edit_num").attr('value',res.num);
					$('.qiquan_recommend_id').attr('value',id);
				}
		});
	$(".edit_qiquan_recommend_box").show();
}
//编辑框 点击取消按钮
function edit_cancel(){
	$(".edit_qiquan_recommend_box").hide();
}
//点击添加保存
function add_qiquan_recommend_data()
{
	var stock_code  = trim($(".add_stock_code").val());
	var yield_rate  = trim($(".add_yield_rate").val());
	var buy_price   = trim($(".add_buy_price").val());
	var sale_price  = trim($(".add_sale_price").val());
	var profit_loss = trim($(".add_profit_loss").val());
	var num         = trim($(".add_num").val());

	var res = check_rule(stock_code,yield_rate,buy_price,sale_price,profit_loss,num);
	if(res)
	{
		$.ajax({
			type: "post",
			url:'/mshop/admin/index.php?m=qiquan&a=add_qiquan_recommend',
			data:{
				'stock_code':stock_code,
				'yield_rate':yield_rate,
				'buy_price':buy_price,
				'sale_price':sale_price,
				'profit_loss':profit_loss,
				'num':num
			},
			dataType:"json",
			success:function(res){
				console.log(res);
				if(res.code == 1)
				{
					$(".add_qiquan_recommend_box").hide();
					alert(res.msg);
					window.location.reload();
				}
				if(res.code == 0)
				{
					alert(res.msg);
				}
			}
		});
	}
}

//点击编辑保存
function edit_qiquan_recommend_data()
{
	var stock_code  = trim($(".edit_stock_code").val());
	var yield_rate  = trim($(".edit_yield_rate").val());
	var buy_price   = trim($(".edit_buy_price").val());
	var sale_price  = trim($(".edit_sale_price").val());
	var profit_loss = trim($(".edit_profit_loss").val());
	var num         = trim($(".edit_num").val());
	var qiquan_recommend_id = $('.qiquan_recommend_id').val();

	var res = check_rule(stock_code,yield_rate,buy_price,sale_price,profit_loss,num)
	if(res)
	{
		$.ajax({
			type: "post",
			url:'/mshop/admin/index.php?m=qiquan&a=edit_qiquan_recommend',
			data:{
				'id':qiquan_recommend_id,
				'stock_code':stock_code,
				'yield_rate':yield_rate,
				'buy_price':buy_price,
				'sale_price':sale_price,
				'profit_loss':profit_loss,
				'num':num
			},
			dataType:"json",
			success:function(res){
				console.log(res);
				if(res.code == 1)
				{
					$(".edit_qiquan_recommend_box").hide();
					alert(res.msg);
					window.location.reload();
				}
				if(res.code == 0)
				{
					alert(res.msg);
				}
			}
		});
	}
	
}

//去空格
function trim(str)
{ 
  return str.replace(/(^\s*)|(\s*$)/g, ""); 
}

function delete_qiquan_recommend(id){
	
	
	remark = "删除推荐后不可恢复，继续吗？";

	layer.confirm(remark, {
		title:'警告',
		btn: ['确认','取消']
	}, function(confirm){
		layer.close(confirm);
		$.ajax({
			url: '/mshop/admin/index.php?m=qiquan&a=delete_qiquan_recommend',
			dataType: 'json',
			type: 'post',
			data: {id:id},
			success: function(res){		
				console.log(res);
				if( res.code == 1 ){
					alert(res.msg);
					document.location.reload();
				}else{
					alert(res.msg);
				}
			}
		});
	});

	
}



</script>
</html>