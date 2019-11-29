<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>区块链积分奖励－添加活动</title>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
	<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>

	<style type="text/css">
		.WSY_previous{width: 110px!important;}
		.temp_data{margin-top: 20px;margin-left: 30px;padding-left:10px;width: 700px;height:30px;line-height: 30px;border-radius: 5px;border: 1px solid #ccc;font-size: 16px;}
		.temp_data li{float: left;margin-right: 10px;}
		.input_data{margin-left: 20px;width: 100%;height: auto;font-size: 16px;}
		.input_data li{float: left;}
		.input_data li p{height: 30px;line-height: 30px;margin: 10px;}
		.input_data li input[type=text]{border:solid 1px #ccc;padding-left: 5px;width:200px;height:30px;border-radius: 5px;}
		.add_activity{float: none;margin-left: 700px;}
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
					<ul class="temp_data">
						<li>
							流通发行总量：<?php echo $bonus_data['reward_money'];?>
						</li>
						<li>
							可兑换奖金总额：￥<?php echo number_format($bonus_data['exchange_bonus_money'],4);?>
						</li>
						<li>
							当月价值：<?php echo $bonus_data['value_money'];?>元（积分：零钱=1:<?php echo $bonus_data['value_money'];?>）
						</li>
					</ul>
		            <ul class="input_data">
		            	<li style="text-align: right;">
		            		<p>产品名称：</p>
		            		<p>兑换产品总数量：</p>
		            		<p>产品金额：</p>
		            		<p>可兑换时间：</p>
		            	</li>
		            	<li>
		            		<p><input type="text" placeholder="请输入产品名称" id="product_name" maxlength="20"></p>
		            		<p><input type="text" placeholder="请输入产品数量" id="product_num" onkeyup="this.value=this.value.replace(/[^0-9]{1,}/g,'')"></p>
		            		<p><input type="text" placeholder="请输入产品金额" id="product_price" onkeyup="clearNoNum(this)"></p>
		            		<p><input class="date_picker" type="text" class="begin_time" id="begin_time" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});">&nbsp;&nbsp;至&nbsp;&nbsp;<input class="date_picker" type="text" class="end_time" id="end_time" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});"></p>
		            	</li>
						<div style="clear: both;"></div>
		            </ul>
		            <!-- <div class="clear"></div> -->
		            <div style="margin-bottom: 50px;"><button type="button" class="add_activity WSY_button">提交</button></div>
				</div>
		    </div>
		</div>
	</div>
	<!--内容框架结束-->
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script>
<script type="text/javascript">

//时间锁
var lastClick;
function lockClick() {
    var nowClick = new Date();
    if (lastClick == null) {
        lastClick = nowClick;
        return true;
    } else {
        if (Math.round((nowClick.getTime() - lastClick.getTime())) > 2000) {  // 每次点击不能少于一秒
            lastClick = nowClick;
            return true;
        }
        else {
            lastClick = nowClick;
            return false
        }
    }
}

$('.add_activity').on('click',function(){
	if(lockClick())//防止多次重复提交的时间锁
	{
		var rule = /^[0-9]{1,6}([.]{1}[0-9]{1,2})?$/;
		    product_name  = $('#product_name').val();
			product_num   = $('#product_num').val();
			product_price = $('#product_price').val();
			begin_time    = $('#begin_time').val();
		    end_time      = $('#end_time').val();
		    bonus_id      = <?php echo $bonus_id;?>;
		    value_money   = <?php echo $bonus_data['value_money']?$bonus_data['value_money']:0;?>;//奖金池id价值
		    exchange_bonus_money = <?php echo $bonus_data['exchange_bonus_money'] ?>;//可兑换奖金总额
		    createtime_month = <?php echo date('m',strtotime('+1 month',strtotime($bonus_data['year_months'].'-01 00:00:01')));?>;//时间，用来判断开始时间与结束时间不能超过奖金池id的月份
	    if(product_name== '')
	    {
	    	alert('产品名称不能为空');
	    	return;
	    }
	    if(product_num== '')
	    {
	    	alert('产品数量不能为空');
	    	return;
	    }
	    if(product_price== '')
	    {
	    	alert('产品金额不能为空');
	    	return;
	    }

	    if(!rule.test(product_price))
	    {
	    	alert('产品金额格式不正确');
	    	return;
	    }
	    // return;
	    if(begin_time== '')
	    {
	    	alert('兑换开始时间不能为空');
	    	return;
	    }
	    if(end_time== '')
	    {
	    	alert('兑换结束时间不能为空');
	    	return;
	    }
	    if( ( parseFloat(product_price) * parseFloat(product_num) ) > exchange_bonus_money )
	    {
	    	alert('产品价格*产品数量不能大于可兑换奖金总额');
	    	return;
	    }
	    if(begin_time>=end_time)
	    {
	    	alert('结束时间不能小于开始时间');
	    	return;
	    }

	    var new_bengin_time = parseInt(begin_time.substring(5,7));//截取时间的月份
	    	new_end_time    = parseInt(end_time.substring(5,7));//截取时间的月份
	    if(new_bengin_time != createtime_month || new_end_time != createtime_month) //如果开始时间与结束时间的月份 不等于 奖金池 id的月份
	    {
	    	alert('开始时间与结束时间均不能超过该奖金池的月份时间');
	    	return;
	    }
	    $.ajax({
			type:'post',
			url:'/mshop/admin/index.php?m=block_chain&a=integral_reward_activity_insert',
			async:false, 
			dataType:'json',
			data:{
				product_name:product_name,
				product_num:product_num,
				product_price:product_price,
				begin_time:begin_time,
				end_time:end_time,
				bonus_id:bonus_id,
				value_money:value_money
			},
			success:function(res){
				console.log(res);
				if(res.errcode == 0)
				{
					location.href="/mshop/admin/index.php?m=block_chain&a=integral_reward_activity&bonus_id="+bonus_id+"&customer_id=<?php echo $customer_id_en?>";
				}
				if(res.errcode == 400)
				{
					alert(res.errmsg);
				}
			},
		});
	}
});
function clearNoNum(obj)
{
	//先把非数字的都替换掉，除了数字和.
	obj.value = obj.value.replace(/[^0-9.]/g,"");
	//必须保证第一个为数字而不是.
	obj.value = obj.value.replace(/^\./g,"");
	//保证.只出现一次，而不能出现两次以上
	obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
}
</script>
</html>