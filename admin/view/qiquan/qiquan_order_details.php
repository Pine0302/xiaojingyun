<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>期权交易－订单详情</title>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
	<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>

	<style type="text/css">
		.qiquan_order_info,.qiquan_user_info,.qiquan_trade_info{width:95%;height:auto;margin:10px auto;display: block;}
		.qiquan_order_info h1,.qiquan_user_info h1,.qiquan_trade_info h1{width: 100%;line-height: 30px;font-size: 20px;}
		.qiquan_order_info ul,.qiquan_user_info ul,.qiquan_trade_info ul{width: 410px;height:auto;float: left;margin-right:50px;margin-top:10px;}
		.qiquan_order_info ul li,.qiquan_user_info ul li,.qiquan_trade_info ul li{width:100%;height:30px;line-height: 30px;font-size: 16px;color:#333;}
	</style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
	    <!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
				<h1 style="font-size: 20px;text-align: center;height: 39px;line-height: 39px;color: #333;">订单详情</h1>
			</div>
		    <!--店主列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
		    		<div class="qiquan_order_info">
			    		<h1>订单基本信息&nbsp;&nbsp;&nbsp;&nbsp;
				    		<b style="color:#0000FF;font-size: 20px;">
				    			<?php
									if($data['status'] == 0 && $data['paystatus'] == 0){
										echo '待支付';
									}
									elseif($data['status'] == 1)
									{
										echo '委托中';
									}
									elseif($data['status'] == 2)
									{
										echo '取消委托';
									}
									elseif($data['status'] == 3)
									{
										echo '待行权';
									}
									elseif ($data['status'] == 4) {
										echo '待结算';
									}
									elseif($data['status'] == 5  )
									{
										if($data['manual_exercise'] == 0 )
										{
											echo '已行权（自动）';
										}elseif ($data['manual_exercise'] == 1 )
										{
											echo '已行权（手动）';
										}
									}
									elseif($data['status'] == 6)
									{
										echo '行权失败';
									}
									elseif($data['status'] == 8)
									{
										echo '失效订单';
									}
								?>
							</b>
						</h1>
			    		<hr/>
		    			<ul>
		    				<li>订单号：<?=$data['batchcode']?></li>
		    				<li>支付方式：<?php echo $data['paystatus'] != 0 ? $data['paystyle'] : "— —";?></li>
		    				<li>支付流水号：<?php echo !empty($data['alipay_trade_no']) && $data['alipay_trade_no'] != 0 ?$data['alipay_trade_no']:"— —";?></li>
		    				<li>名义本金：<?php echo !empty($data['capital']) && $data['capital'] != 0 ?$data['capital']:"— —";?></li>
		    				<li>手续费：<?php echo !empty($data['poundage']) && $data['poundage'] != 0 ?$data['poundage']:"— —";?></li>
		    				<li>合约周期：<?php echo !empty($data['exercise_cycle']) && $data['exercise_cycle'] != 0 ?$data['exercise_cycle']:"— —";?></li>
		    				<li>到期日：<?php echo !empty($data['exercise_deadline']) && $data['exercise_deadline'] != 0 ?$data['exercise_deadline']:"— —";?></li>
		    			</ul>
		    			<ul>
		    				<li>下单时间：<?=$data['createtime']?></li>
		    				<li>支付时间：<?php echo $data['paystatus'] != 0 ?$data['pay_time']:"— —";?></li>
		    				<li>报价比：<?php echo !empty($data['offer_percent']) && $data['offer_percent'] != 0 ?$data['offer_percent']."%":"— —";?></li>
		    				<li>权利金：<?php echo !empty($data['droit_price']) && $data['droit_price'] != 0 ?$data['droit_price']:"— —";?></li>
		    				<li>实付：<?php echo !empty($data['total_price']) && $data['total_price'] != 0 ?$data['total_price']:"— —";?></li>
		    				<li>行权周期：<?php echo !empty($data['gain_droit_time']) && $data['gain_droit_time'] != 0 ?$data['begin_time']:"— —";?>&nbsp;至&nbsp;<?php echo !empty($data['exercise_deadline']) && $data['exercise_deadline'] != 0 ?$data['exercise_deadline']:"— —";?></li>
		    			</ul>
		    			<div style="clear: both;"></div>
		            </div>
		            
		            <div class="qiquan_user_info">
			    		<h1>用户信息</h1>
			    		<hr/>
		    			<ul>
		    				<li>用户ID：<?=$data['user_id']?></li>
		    				<li>手机号：<?php echo !empty($data['phone']) && $data['phone'] != 0 ?$data['phone'] : "— —" ?></li>
		    			</ul>
		    			<ul>
		    				<li>用户昵称：<?=$data['weixin_name']?></li>
		    			</ul>
		    			<div style="clear: both;"></div>
		            </div>
		            
		            <div class="qiquan_trade_info">
			    		<h1>交易信息</h1>
			    		<hr/>
		    			<ul>
		    				<li>标的股票：<?=$data['stock_code']?>&nbsp;&nbsp;<?=$data['name']?></li>
		    				<li>盈亏比例：<?php echo $data['status'] == 5 ?$data['profit_percent']."%":"— —";?></li>
		    				<li>盈亏：<?php echo $data['status'] == 5 ?$data['settle_gain']:"— —";?></li>
		    			</ul>
		    			<ul>
		    				<li>下单价：<?=$data['create_stock_price']?></li>
		    				<li>认购价：<?php echo $data['status'] >=3 && $data['status'] <= 6 ?$data['exercise_stock_price']:"— —";?></li>
		    				<li>结算价：<?php echo $data['status'] == 5 ?$data['close_stock_price']:"— —";?></li>
		    			</ul>
		    			<ul>
		    				<li>下单时间：<?=$data['createtime']?></li>
		    				<li>认购时间：<?php echo $data['status'] >=3 && $data['status'] <= 6 ?$data['gain_droit_time']:"— —";?></li>
		    				<li>结算时间：<?php echo $data['status'] == 5 ?$data['settle_time']:"— —";?></li>
		    				<?php if($data['status'] == 2 ) echo "<li>取消时间：".$data['cancel_time']."</li>";?>
		    				<?php if($data['status'] == 8 ) echo "<li>失效原因：".$data['invalid_reason']."</li>";?>
		    			</ul>
		    			<div style="clear: both;"></div>
		            </div>
				</div>
		    <!--产品管理代码结束-->
		</div>
	</div>
	<!--内容框架结束-->
</body>
</html>