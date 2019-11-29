//搜索订单条件---start
function search_condition(url){
	var begintime = document.getElementById("begintime").value;//下单时间开始
	var endtime = document.getElementById("endtime").value;//下单时间结束
	var search_batchcode = document.getElementById("search_batchcode").value;//订单号
	var search_name = document.getElementById("search_name").value;//搜索姓名
	var search_name_type = document.getElementById("search_name_type").value;//微信名还是收货名
	var url2="";
	if(begintime !=""){
		url2=url2+"&begintime="+begintime;
	}
	if(endtime !=""){
		url2=url2+"&endtime="+endtime;
	}
	if(search_batchcode !=""){
		url2=url2+"&search_batchcode="+search_batchcode;
	}
	if(search_name_type !=""){
		url2=url2+"&search_name_type="+search_name_type;
	}
	if(search_name !=""){
		url2=url2+"&search_name="+search_name;
	}
	if(pagenum !=""){
		url2=url2+"&pagenum="+pagenum;
	}
	document.location=url+url2;
}
//搜索订单条件---end

//搜索订单---start
function searchForm(){
	var url="order.php?customer_id="+customer_id+"&pagenum="+pagenum;
	search_condition(url); 
}
//搜索订单---end

function KuaiDi100(obj){
	KDNum = $("#express_num2_"+obj).val();
	KDName = $("#express_id2_"+obj).val();
	layer.open({
		type: 2,
		title: '快递查询',
		shadeClose: true,
		shade: 0.5,
		area: ['450px', '70%'],
		content: '//m.kuaidi100.com/index_all.html?type='+KDNum+'&postid='+KDNum+'#result' 
	});  		
}
/* 显示订单详情  */
function showDetail(batchcode){
	var div = $("#order_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
/* 显示订单详情 --end- */

/* 关闭订单详情  */
function hideDetail(){
	$(".order_hide").fadeOut("slow"); 
}
/* 关闭订单详情 --end- */


/* 确认订单  */
function confirmOrder(obj){
	var batchcode = $(obj).data('batchcode');	
	layer.confirm('您确定要确认 订单:'+batchcode+' 交易完成吗？<br/>确认后，表示订单已经完成，并且无法撤销！', {
		btn: ['确认','取消'] 
	}, function(confirm){
		 
		layer.close(confirm);	  
		layer_open();			
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'op':"confirm"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					$(obj).remove();
					$("#table_five_"+batchcode).html('<span class="btn btn-success">已完成</span>');
				}
				layer.alert(res.msg);
			},	
			error:function(){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络");
			}						
		});		 
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});
			
}
/* 确认订单 --end*/

/* 删除订单  */
function delOrder(obj){
	var batchcode = $(obj).data('batchcode');
	layer.confirm('您确定要删除订单号:'+batchcode+'吗？', {
		title:'订单删除',		
		btn: ['确定删除','取消'] 
	}, function(){
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'op':"del"},
			dataType:"json",
			success: function(res){
				if(res.status==0){ 				
					$(obj).parent("td").html('<a style="color:#C9302C">订单已删除</a>');	 	
				}
				layer.alert(res.msg);
			},	
			error:function(){
				layer.alert("网络错误请检查网络");
			}						
		});			
	}, function(){
		layer.msg('已取消删除', {
			time: 4000,
			btn: ['确认'],  
			icon:1
		});
	});
	
}
/* 删除订单 --end*/

/* 后台支付 */
function payOrder(obj){
	var batchcode = $(obj).data('batchcode');	
	var totalprice = $(obj).data('totalprice');	

	layer.confirm('您确定要确认支付订单号:'+batchcode+'吗？', {
		title:'后台支付',		
		btn: ['确认支付','取消'] 
	}, function(confirm){
		layer_open();
		layer.close(confirm);	 
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'totalprice':totalprice,'op':"pay"},
			dataType:"json",
			success: function(res){
				if(res.status==0){ 
					layer.close(index_layer);
					$("#order_pay_"+batchcode).html('<img src="../../../common/images_V6.0/contenticon/pay-icon.png" /><span class="CP_table_bianhaof">已支付<span style="color:red;">(后台支付)</span></span>');					
					$(obj).prev("a").remove();
					$(obj).next("a").replaceWith('<a title="返佣记录" href="order_rebate_log.php?batchcode='+batchcode+'&customer_id='+customer_id+'=="><img src="../../../common/images_V6.0/operating_icon/icon51.png"></a>');
					$(obj).replaceWith('<a id="button_delivery_'+batchcode+'" title="发货" onclick="showDelivery('+batchcode+')"><img src="../../../common/images_V6.0/operating_icon/icon42.png"></a>');
				}
				layer.alert(res.msg);
			},	
			error:function(){
				layer.close(index_layer);
				layer.alert("网络错误请检查网络1");
			}						
		});			
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],  
			icon:1
		});
	});
	
}
/*- 后台支付 --end*/


var index_layer;
function layer_open(){
	index_layer= layer.load(0, {
		shade: [0.1,'#000'], //0.1透明度的白色背景
		content: '<div style="position:relative;top:30px;width:200px;color:red">数据处理中</div>'
	});	
}