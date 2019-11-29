$(function(){
	customer_id = $("#customer_id").val();
	var chart_width = $('.statistics_ul04').width();
	$('.chart').css('width',chart_width+"px");
	qrsell_num();
	stay_qrsell_num();
	sell_pro_num();
	isout_pro_num();
	sell_out_pro_num();
	post_expense();
	stay_expense_num();
	stay_payment_order_num();
	stay_send_order_num();
	stock_num();
	return_order_num();
	complete_order_num();
	total_order_num();
	total_consumption();
	this_month_order_num();
	this_month_consumption();
	total_order_amplitude();
	total_consumption_amplitude();
	this_month_order_amplitude();
	this_month_consumption_amplitude();
	container_charts();
	yes_total_order_num();
	pay_total_order_num();
	yes_pay_total_order_num();
	pay_charts();
	total_consumption_charts();
	consumption_cake_charts();
}); 

function qrsell_num(){	//分销商数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:1},
        success: function (result) {
			 $(".qrsell_num").html(result);
        }
    })
}
function stay_qrsell_num(){  //待审核分销商数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:2},
        success: function (result) {
			$(".stay_qrsell_num").html(result);
        }
    })
}
function sell_pro_num(){	//出售中的商品数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:3},
        success: function (result) {
			$(".sell_pro_num").html(result);
        }
    })
}
function isout_pro_num(){	//仓库中商品数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:4},
        success: function (result) {
			$(".isout_pro_num").html(result);
        }
    })
}
function sell_out_pro_num(){	//已售罄的商品数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:5},
        success: function (result) {
			$(".sell_out_pro_num").html(result);
        }
    })
}
function post_expense(){	//已支出佣金
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:6},
        success: function (result) {
			$(".post_expense").html(result);
        }
    })
}
function stay_expense_num(){	//待提现佣金笔数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:7},
        success: function (result) {
			$(".stay_expense_num").html(result);
        }
    })
}
function stay_payment_order_num(){	//待付款订单数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:8},
        success: function (result) {
			$(".stay_payment_order_num").html(result);
        }
    })
}
function stay_send_order_num(){	//待发货订单数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:9},
        success: function (result) {
			$(".stay_send_order_num").html(result);
        }
    })
}
function stock_num(){	//库存提醒
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:10},
        success: function (result) {
			$(".stock_num").html(result);
        }
    })
}
function return_order_num(){	//退货订单数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:11},
        success: function (result) {
			$(".return_order_num").html(result);
        }
    })
}
function complete_order_num(){	//已完成订单数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:12},
        success: function (result) {
			$(".complete_order_num").html(result);
        }
    })
}
function total_order_num(){	//当天总订单数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:13},
        success: function (result) {
			$(".total_order_num").html(result);
        }
    })
}
function total_consumption(){	//当天总消费金额
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:14},
        success: function (result) {
			$(".total_consumption").html(result);
        }
    })
}
function this_month_order_num(){	//本月订单
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:15},
        success: function (result) {
			$(".this_month_order_num").html(result);
        }
    })
}
function this_month_consumption(){	//本月总消费金额
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:16},
        success: function (result) {
			$(".this_month_consumption").html(result);
        }
    })
}
function total_order_amplitude(){	//当天订单增幅
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:17},
        success: function (result) {
			if(result>=0){
				$(".total_order_amplitude").css("color","red");
				result=(result*100);
				result=result.toFixed(2)+"%";
			}else if(result=="--"){
				$(".total_order_amplitude").css("background","url() no-repeat right center");
			}else{
				result=Math.abs(result);
				result=(result*100);
				result=result.toFixed(2)+"%";
				$(".total_order_amplitude").css("color","green");
				$(".total_order_amplitude").css("background","url(images/qushiicon/dor_icon02.png) no-repeat right center");
			}
			$(".total_order_amplitude").html(result);
        }
    })
}
function total_consumption_amplitude(){	//当天消费增幅
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:18},
        success: function (result) {
			if(result>=0){
				$(".total_consumption_amplitude").css("color","red");
				result=(result*100);
				result=result.toFixed(2)+"%";
			}else if(result=="--"){
				$(".total_consumption_amplitude").css("background","url() no-repeat right center");
			}else{
				result=Math.abs(result);
				result=(result*100);
				result=result.toFixed(2)+"%";
				$(".total_consumption_amplitude").css("color","green");
				$(".total_consumption_amplitude").css("background","url(images/qushiicon/dor_icon02.png) no-repeat right center");
			}
			$(".total_consumption_amplitude").html(result);
        }
    })
}
function this_month_order_amplitude(){	//本月订单增幅
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:19},
        success: function (result) {
			if(result>=0){
				$(".this_month_order_amplitude").css("color","red");
				result=(result*100);
				result=result.toFixed(2)+"%";
			}else if(result=="--"){
				$(".this_month_order_amplitude").css("background","url() no-repeat right center");
			}else{
				result=Math.abs(result);
				result=(result*100);
				result=result.toFixed(2)+"%";
				$(".this_month_order_amplitude").css("color","green");
				$(".this_month_order_amplitude").css("background","url(images/qushiicon/dor_icon02.png) no-repeat right center");
			}
			$(".this_month_order_amplitude").html(result);
        }
    })
}
function this_month_consumption_amplitude(){	//本月消费增幅
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:20},
        success: function (result) {
			if(result>=0){
				$(".this_month_consumption_amplitude").css("color","red");
				result=(result*100);
				result=result.toFixed(2)+"%";
			}else if(result=="--"){
				$(".this_month_consumption_amplitude").css("background","url() no-repeat right center");
			}else{
				result=Math.abs(result);
				result=(result*100);
				result=result.toFixed(2)+"%";
				$(".this_month_consumption_amplitude").css("color","green");
				$(".this_month_consumption_amplitude").css("background","url(images/qushiicon/dor_icon02.png) no-repeat right center"); 
			}
			$(".this_month_consumption_amplitude").html(result);
        }
    })
}

function container_charts(){	//当天总订单报表
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:21},
		dataType: "json",
        success: function (result) {
			//console.log(result);
			var counts = new Array();
			 for(var i=0;i<result.length;i++){
				// console.log(result[i]);
				counts.push(parseInt(result[i])); 
			}  
			require.config({
				paths: {
					echarts: '../../Common/js/Data/js/echarts'
				}
			});
			require(
				[
					'echarts',
					'echarts/chart/line'
				],
				function (ec) {
					var myChart = ec.init(document.getElementById('container_charts'));
					myChart.setOption({
		    tooltip : {
								trigger: 'axis'
							},
							grid:{
								x:20,
								y:10,
								x2:40,
								y2:20
						    },
							calculable : true,
							xAxis : [
								{
									type : 'category',
									boundaryGap : false,
									data : [seven,six,five,four,there,two,ome]
								}
							],
							yAxis : [
								{
									type : 'value'
								}
							],
							series : [
								{
									name:'订单数',
									type:'line',
									smooth:true,
									itemStyle: {
										normal: {
											color:'#06A7E1',
											areaStyle: {
												color : 'rgba(205,237,249,0.6)'
											},
											lineStyle:{
												color:'#06A7E1'
											}
										}
									},
									data:counts
								}
							]
							
							
					});
					window.onresize = myChart.resize;
				}
			);
        }
    })
}

function yes_total_order_num(){	//昨天总订单数
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:22},
        success: function (result) {
			$(".yes_total_order_num").html(result);
        }
    })
}
function pay_total_order_num(){	//今日付款订单数（笔）
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:23},
        success: function (result) {
			$(".pay_total_order_num").html(result);
        }
    })
}
function yes_pay_total_order_num(){	//昨日付款订单数（笔）
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:24},
        success: function (result) {
			$(".yes_pay_total_order_num").html(result); 
        }
    })
}
function pay_charts(){	//付款订单笔数趋势图
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:25},
		dataType: "json",
        success: function (result) {
			//console.log(result);
			var counts = new Array();
			 for(var i=0;i<result.length;i++){
				counts.push(parseInt(result[i])); 
			}  
			require.config({
				paths: {
					echarts: '../../Common/js/Data/js/echarts'
				}
			});
			require(
				[
					'echarts',
					'echarts/chart/line'
				],
				function (ec) {
					var myChart = ec.init(document.getElementById('pay_charts'));
					myChart.setOption({
						    tooltip : {
								trigger: 'axis'
							},
							grid:{
								x:20,
								y:10,
								x2:50,
								y2:20
						    },
							calculable : true,
							xAxis : [
								{
									type : 'category',
									boundaryGap : false,
									data : [seven,six,five,four,there,two,ome]
								}
							],
							yAxis : [
								{
									type : 'value'
								}
							],
							series : [
								{
									name:'订单数',
									type:'line',
									smooth:true,
									itemStyle: {
										normal: {
											color:'#06A7E1',
											areaStyle: {
												color : 'rgba(205,237,249,0.6)'
											},
											lineStyle:{
												color:'#06A7E1'
											}
										}
									},
									data:counts
								}
							]
							
					});
				}
			);
        }
    })
}
function total_consumption_charts(){	//订单金额统计
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:26},
		dataType: "json",
        success: function (result) {
		//	console.log(result);
			var counts1 = new Array();
			var counts2 = new Array();
			for(var i=0;i<result[0].length;i++){
				counts1.push(parseInt(result[0][i])); 				
			}  
			for(var i=0;i<result[1].length;i++){
				counts2.push(parseInt(result[1][i])); 				
			} 
			//console.log(counts1);
		//	console.log(counts2);
			require.config({
				paths: {
					echarts: '../../Common/js/Data/js/echarts'
				}
			});
			require(
				[
					'echarts',
					'echarts/chart/bar'
				],
				function (ec) {
					var myChart = ec.init(document.getElementById('total_consumption_charts'));
					myChart.setOption({
						tooltip : {
							trigger: 'axis'
						},
						grid:{   
							x:50,
							y:20,
							x2:30,
							y2:20
						},
						legend: {
							data:['总订单金额','已付款金额']
						},
						calculable : true,
						xAxis : [
							{
								type : 'category',
								data : [seven,six,five,four,there,two,ome]
							}
						],
						yAxis : [
							{
								type : 'value',
								splitArea : {show : true}
							}
						],
						series : [
							{
								name:'总订单金额',
								type:'bar',
								itemStyle: {
									normal: {										
										color : '#06A7E1'										
									}
								},
								data:counts2
							},
							{
								name:'已付款金额',
								type:'bar',
								itemStyle: {
									normal: {
										color : '#E4214A'										
									}
								},
								data:counts1
							}
						]
					});
				}
			);
        }
    })
}
function consumption_cake_charts(){	//订单统计
	$.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:27},
		dataType: "json",
        success: function (result) {
			console.log(result);
			var counts1 = parseInt(result[0]);
			var counts2 = parseInt(result[1]);
			/* for(var i=0;i<result[0].length;i++){
				counts1.push(parseInt(result[0][i])); 				
			} */
			console.log(counts1);
			console.log(counts2);
			require.config({
				paths: {
					echarts: '../../Common/js/Data/js/echarts'
				}
			});
			require(
				[
					'echarts',
					'echarts/chart/pie'
				],
				function (ec) {
					var myChart = ec.init(document.getElementById('consumption_cake_charts'));
					myChart.setOption({
						tooltip : {
							trigger: 'item',
							formatter: "{a} <br/>{b} : {c} ({d}%)"
						},
						legend: {
							orient : 'vertical',
							x : 'left',
							data:['未支付订单','已支付订单']
						},
						calculable : true,
						series : [
							{
								name:'今日订单',
								type:'pie',
								radius : '55%',
								center: ['50%', '60%'],
								data:[
									{
										value:counts2, 
										name:'未支付订单',
										itemStyle: {
											normal: {										
												color : '#06A7E1'										
											}
										},
									}, 
									{
										value:counts1, 
										name:'已支付订单',
										itemStyle: {
											normal: {										
												color : '#E4214A'										
											}
										},
									}
								]
							}
						]
					});
				}
			);
        }
    })
}
