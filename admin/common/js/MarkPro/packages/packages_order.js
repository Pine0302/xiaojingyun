//搜索订单条件---start
function search_condition(url){
	var pay_begintime = document.getElementById("pay_begintime").value;//订单支付时间开始
	var pay_endtime = document.getElementById("pay_endtime").value;//订单支付时间结束
	var begintime = document.getElementById("begintime").value;//下单时间开始
	var endtime = document.getElementById("endtime").value;//下单时间结束
	var search_batchcode = document.getElementById("search_batchcode").value;//订单号
	var search_name = document.getElementById("search_name").value;//搜索姓名
	var search_name_type = document.getElementById("search_name_type").value;//微信名还是收货名
	var search_order_type = document.getElementById("search_order_type").value;//订单状态
	var search_package_type = document.getElementById("search_package_type").value;//订单礼包类型 
	var search_packages_name = document.getElementById("search_packages_name").value;//礼包名称
	var mark = document.getElementById("mark").value;//礼包标签
	var url2="&search_package_type="+search_package_type+"&search_name_type="+search_name_type+"&search_order_type="+search_order_type;
	if(begintime !=""){
		url2=url2+"&begintime="+begintime;
	}
	if(endtime !=""){
		url2=url2+"&endtime="+endtime;
	}
	if(pay_begintime !=""){
		url2=url2+"&pay_begintime="+pay_begintime;
	}
	if(pay_endtime !=""){
		url2=url2+"&pay_endtime="+pay_endtime;
	}
	if(search_batchcode !=""){
		url2=url2+"&search_batchcode="+search_batchcode;
	}
	if(search_name !=""){
		url2=url2+"&search_name="+search_name;
	}
	if(search_packages_name!=-1){
		url2=url2+"&search_packages_name="+search_packages_name;
	}
	if(mark!=-1){
		url2=url2+"&mark="+mark;
	}

	document.location=url+url2;
}
//搜索订单条件---end

//搜索订单---start
function searchForm(){
	var url="packages_order.php?customer_id="+customer_id+"&pagenum=1";
	search_condition(url); 
}
//搜索订单---end

//订单导出---start
function order_Excel(){
	var pay_begintime = document.getElementById("pay_begintime").value;//订单支付时间开始
	var pay_endtime = document.getElementById("pay_endtime").value;//订单支付时间结束
	var begintime = document.getElementById("begintime").value;//下单时间开始
	var endtime = document.getElementById("endtime").value;//下单时间结束
	var search_batchcode = document.getElementById("search_batchcode").value;//订单号
	var search_name = document.getElementById("search_name").value;//搜索姓名
	var search_name_type = document.getElementById("search_name_type").value;//微信名还是收货名
	var search_order_type = document.getElementById("search_order_type").value;//订单状态
	var search_package_type = document.getElementById("search_package_type").value;//订单礼包类型
	
	if(search_batchcode==""){ 
		search_batchcode = -1;
	}
	if(search_name==""){
		search_name = -1;
	}
	if(begintime==""){
		begintime = 0;
	}
	if(endtime==""){
		endtime = 0;  
	}
	if(pay_begintime==""){
		pay_begintime = 0;
	}
	if(pay_endtime==""){ 
		pay_endtime = 0;
	}
	var url='/weixin/plat/app/index.php/Excel/package_order_Excel/customer_id/'+customer_id+'/begintime/'+begintime+'/endtime/'+endtime+'/pay_begintime/'+pay_begintime+'/pay_endtime/'+pay_endtime+'/search_batchcode/'+search_batchcode+'/search_name/'+search_name+'/search_name_type/'+search_name_type+'/search_order_type/'+search_order_type+'/search_package_type/'+search_package_type+'/';
	
	document.location=url;
}
//订单导出---end

//快递100
function KuaiDi100(obj){
	KDNum = $("#express_num2_"+obj).val();
	KDName = $("#express_id2_"+obj).val();
	console.log(KDNum);
	kd_href = "//"+document.domain+"/weixinpl/back_newshops/Distribution/settings/kuaidi_head.php?customer_id="+customer_id_en+"&batchcode="+obj+"&postid="+KDNum+"&type="+KDName;
	
	layer.open({
		type: 2,
		title: '快递查询',
		shadeClose: true,
		shade: 0.5,
		area: ['450px', '70%'],
		content: kd_href 
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

/* 显示修改地址发货  */
function showAddress(batchcode){	
	var div = $("#address_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
/* 显示订单发货 --end*/

/* 显示订单发货  */
function showDelivery(batchcode){
	var div = $("#delivery_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
/* 显示订单发货 --end*/

/* 确认订单  */
function confirmOrder(obj){
	var batchcode = $(obj).data('batchcode');	
	var totalprice = $(obj).data('totalprice');	
	var user_id = $(obj).data('user_id');	
	layer.confirm('您确定要确认 订单:'+batchcode+' 交易完成吗？<br/>确认后，表示订单已经完成，并且无法撤销！', {
		btn: ['确认','取消'] 
	}, function(confirm){
		
		layer.close(confirm);	  
		layer_open();			
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'totalprice':totalprice,'op':"confirm"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					layer.alert(res.msg,function() {
						window.location.reload();
					});
					
					// $("#table_five_"+batchcode).html('<span class="btn btn-success">已完成</span>');
					// if( res.is_return == 1 ){
					// 	$(obj).replaceWith('<a id="reject_'+batchcode+'" title="驳回身份" data-user_id="'+user_id+'" onclick="reject_identity(this)" data-batchcode="'+batchcode+'"><img src="../../../common/images_V6.0/operating_icon/icon25.png"></a>'); 
					// }else{
					// 	$(obj).remove();
					// }
				}else{
					layer.alert(res.msg);
				}
				
			},	
			error:function(res){
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
					// $("#order_pay_"+batchcode).html('<img src="../../../common/images_V6.0/contenticon/pay-icon.png" /><span class="CP_table_bianhaof">已支付<span style="color:red;">(后台支付)</span></span>');					
					// $(obj).prev("a").remove();
					// $(obj).next("a").replaceWith('<a title="返佣记录" href="../../Order/order/order_rebate_log.php?batchcode='+batchcode+'&customer_id='+customer_id_en+'==&class=2"><img src="../../../common/images_V6.0/operating_icon/icon51.png"></a>');
					// $(obj).replaceWith('<a id="button_delivery_'+batchcode+'" title="发货" onclick="showDelivery(\''+batchcode+'\')"><img src="../../../common/images_V6.0/operating_icon/icon42.png"></a>');
				
				}
				layer.alert(res.msg,function(){
					window.location.reload();
				});
			},	
			error:function(res){
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
/*- 后台支付 --end*/

/* 发货 */
$(".order_delivery").click(function(){	
	var batchcode = $(this).data('batchcode');
	var $button = $(this);
	layer.confirm('您确认要发货吗', {
		btn: ['发货','取消'] 
	}, function(confirm){

		var expressID = $("#express_id_"+batchcode).val();
		var expressName = $("#express_id_"+batchcode).find("option:selected").text();	
		var expressRemark = $("#express_remark_"+batchcode).val();
		var expressNum = $("#express_num_"+batchcode).val();
		
		if(expressNum=="" && expressID!=0){ 
			layer.alert("请输入快递单号", function(index){layer.close(index);}); 
			return;  
		}
		layer.close(confirm);	  
		layer_open();			
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'expressID':expressID,'expressRemark':expressRemark,'expressNum':expressNum,'expressName':expressName,'op':"send"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					$button.parent("dd").next("dd").remove(); 
					$button.parent("dd").remove(); 
					var sendstr = "";
					$("#button_delivery_"+batchcode).prev("a").remove();														
					$("#express_id2_"+batchcode).text(expressName);
					$("#express_remark2_"+batchcode).text(expressRemark);
					$("#express_num2_"+batchcode).val(expressNum);
					$("#express_num2_"+batchcode).parent('span').parent('dd').append('<span onclick="KuaiDi100('+batchcode+')" class="order_kuaidi">(点击查看物流)</span>');
					if(expressID!=0){
						sendstr = '<p class="CP_table_chanpina_fourp"><img src="../../../common/images_V6.0/contenticon/affirm-icon.png"><b style="color:#31B0D5"> 已发货</b></p><p>发货时间:'+res.time+'</p>';
						$("#button_delivery_"+batchcode).remove();
					}else if(res.line==41){						
						sendstr = '<p class="CP_table_chanpina_fourp"><img src="../../../common/images_V6.0/contenticon/confirm_delivery.png"><b style="color:#337AB7"> 顾客已收货</b></p><p>发货时间:'+res.time+'</p><p>收货时间:'+res.time+'</p>'; 
						$("#button_delivery_"+batchcode).replaceWith('<a title="确认完成" data-batchcode="'+batchcode+'" data-totalprice="1" onclick="confirmOrder(this)"><img src="../../../common/images_V6.0/operating_icon/icon23.png"></a>');						
					}
					$("#table_four_"+batchcode).html(sendstr);
					$(".order_hide").fadeOut("slow"); 					
				}
				if(res.line==42){
					layer.alert(res.msg,function() {
						window.location.reload();
					});
				}else{
					layer.alert(res.msg);
				}
				
				
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
			
});
/*发货 --end*/

/*- 修改地址 -*/
$(".order_add").click(function(){	
	var batchcode = $(this).data('batchcode');
	layer.confirm('您确认要修改 订单:'+batchcode+' 的收货地址信息吗', {
		btn: ['修改收货信息','取消'] 
	}, function(confirm){

		var addressName = $("#address_name_"+batchcode).val();
		var addressPhone = $("#address_phone_"+batchcode).val();
		var addressP = $("#address_p_"+batchcode).val();
		var addressC = $("#address_c_"+batchcode).val();
		var addressA = $("#address_a_"+batchcode).val();
		var addressAdd = $("#address_add_"+batchcode).val(); 
		
		if(addressName=="" && addressPhone=="" && addressP=="" && addressC=="" && addressA=="" && addressAdd=="" ){ 
			layer.alert("请输入完整的收件人信息", function(index){layer.close(index);}); 
			return;  
		}
		layer.close(confirm);	  
		layer_open();			
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'addressName':addressName,'addressPhone':addressPhone,'addressP':addressP,'addressC':addressC,'addressA':addressA,'addressAdd':addressAdd,'op':"changeAdd"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					$("span[data-add='"+batchcode+"']").text(addressP+addressC+addressA+addressAdd);			 		 
					$("span[data-name='"+batchcode+"']").text(addressName);			 		 
					$("span[data-phone='"+batchcode+"']").text(addressPhone);			 		 
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
			
});
/* 修改地址 End */
var index_layer;
function layer_open(){
	index_layer= layer.load(0, {
		shade: [0.1,'#000'], //0.1透明度的白色背景
		content: '<div style="position:relative;top:30px;width:200px;color:red">数据处理中</div>'
	});	
}

/*提交excel快递单号开始*/

function importMember(){
	layer_open();	
	var f_content = document.getElementById("excelfile").value;
	var fileext=f_content.substring(f_content.lastIndexOf("."),f_content.length)

	fileext=fileext.toLowerCase()
	if (fileext!='.xls'){
		layer.close(index_layer);
		layer.alert("对不起，导入数据格式必须是xls格式文件哦，请您调整格式后重新上传，谢谢 ！");
		return false;
	}
	document.getElementById("frm_import").submit();
/* 	$.ajax({
		url: "save_order_excel.php",
		type:"POST",
		data:{'customer_id':customer_id,'f_content':f_content},
		dataType:"json",
		success: function(res){
			layer.close(index_layer);
			layer.alert(res.msg);
		},	
		error:function(res){
			layer.close(index_layer);
			layer.alert("网络错误请检查网络");
		}						
	});
 */
}
/*提交excel快递单号结束*/

/*全选*/
$(function() {  
    $("#all_checkbox").click(function() { 
		var all_box =  $("#all_checkbox").is(':checked');
        if ( all_box ) {  
            $("input[name=input_checkbox]").each(function() {  
                $(this).prop("checked", true);  
            });  
        } else {  
            $("input[name=input_checkbox]").each(function() {  
                $(this).prop("checked", false);  
            });  
        }  
    });  
});
/*批量确认完成*/
function batchFinish(){
	var box = $(':checkbox[name="input_checkbox"]:checked');
	if( 1 > box.length ){
		layer.alert("请选择订单！");
		return;
	}
	$(".batchFinish").show();
	$(".wait_div").show();
	var box_arr = []; 	//创建数组
	for( var i=0; i < box.length; i++){
		box_arr[i] = []; 
		var box_val = $(':checkbox[name="input_checkbox"]:checked').eq(i).attr("b_id");
		var totalprice_val = $(':checkbox[name="input_checkbox"]:checked').eq(i).attr("b_totalprice");
		box_arr[i][0] = box_val;
		box_arr[i][1] = totalprice_val;
	}
	box_arr = JSON.stringify(box_arr);  //数组转json
	$.ajax({
		type: "post",
		url:'order.class.php',
		data:{'box_arr':box_arr,'op':'batchFinish'},  
		dataType:"json",
		success:function(res){
			if(res.status==0){
				
				for( var i=0; i < box.length; i++){
					var box_val = $(':checkbox[name="input_checkbox"]:checked').eq(i).attr("b_id");
					$('[data-batchcode='+box_val+']').remove();
					$("#table_five_"+box_val).html('<span class="btn btn-success">已完成</span>');
				}
			}
			$(".batchFinish").hide();
			$(".wait_div").hide();
			layer.alert(res.msg);
		},
		error:function(e){
			layer.alert("网络错误请检查网络");
		}
	});
}

/*批量结算*/
function batchSet(){
	var box = $(':checkbox[name="input_checkbox"]:checked');
	if( 1 > box.length ){
		layer.alert("请选择订单！");
		return;
	}
	layer.confirm('您确定要结算选中的'+box.length+'个订单？<br/>确认后，表示订单已经结算，并且无法撤销！', {
		btn: ['确认','取消'] 
	},function(confirm){
	layer.close(confirm);

	$(".batchFinish").show();
	$(".wait_div").show();
	var box_arr = []; 	//创建数组
	for( var i=0; i < box.length; i++){
		box_arr[i] = []; 
		var box_val = $(':checkbox[name="input_checkbox"]:checked').eq(i).attr("b_id");
		var totalprice_val = $(':checkbox[name="input_checkbox"]:checked').eq(i).attr("b_totalprice");
		box_arr[i][0] = box_val;
		box_arr[i][1] = totalprice_val;
	}
	box_arr = JSON.stringify(box_arr);  //数组转json
	$.ajax({
		type: "post",
		url:'order.class.php',
		data:{'box_arr':box_arr,'op':'batchSet'},  
		dataType:"json",
		success:function(res){
			if(res.status==0){				
 				for( var i=0; i < res.code.length; i++){
 					if(res.code[i].status==0){
					var box_val = $(':checkbox[name="input_checkbox"]:checked').eq(i).attr("b_id");
					$("#table_five_"+box_val).html('<span class="btn btn-success">已结算</span>');
				}
				}
			}
			$(".batchFinish").hide();
			$(".wait_div").hide();
			layer.alert(res.msg);
		},
		error:function(e){
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

function backOrder(obj){
	var batchcode = $(obj).data('batchcode');	
	
	$.ajax({
		type: "post",
		url:'order.class.php',
		data:{'batchcode':batchcode,'customer_id':customer_id_en,'op':'backOrder'},  
		dataType:"json",
		success:function(res){

		},
		error:function(e){
			layer.alert("网络错误请检查网络");
		}
	});
	
}

//退款管理
function returnMoney(obj){	
	var batchcode = $(obj).data('batchcode');
	var sendstatus = $(obj).data('sendstatus');

	layer.confirm('请选择 订单:'+batchcode+' 的申请退款操作', {
		btn: ['同意退款','拒绝','取消'] 
	}, function(confirm){		
		layer.close(confirm);
		layer.prompt({
			formType: 0,
			title: '同意退款备注',			
			value: '同意退款申请'
		},function(reason, prompt, elem){
			layer.close(prompt);			
			layer_open();			
			$.ajax({
				url: "order.class.php",
				type:"POST",
				data:{'batchcode':batchcode,'reason':reason,'return_status':1,'op':"confirmReturnMoney"},
				dataType:"json",
				success: function(res){
					 layer.close(index_layer);
					if(res.status==0){ 
						$(obj).replaceWith('<a title="确定退款" data-refund-batchcode="'+batchcode+'" onclick="showGoodRefund(\''+batchcode+'\',0)"><img src="../../../common/images_V6.0/operating_icon/icon57.png"></a>'); 
						$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/return-money.png"> <b style="color:#C9302C">顾客申请退款[已同意]</b>');
					}
					if(res.errcode>0){
						// layer.alert(res.errmsg);
						layer.alert(res.errmsg,function() {
						window.location.reload();
					});
					}else{
						// layer.alert(res.msg);
						layer.alert(res.msg,function() {
						window.location.reload();
					});
					}
				},	
				error:function(){
					layer.close(index_layer);
					layer.alert("网络错误请检查网络");
				}						
			});							
		});  			 
	}, function(confirm2){
		layer.close(confirm2);
		layer.prompt({
			formType: 0,
			title: '拒绝退款备注',			
			value: '拒绝退款申请'
		},function(reason, prompt, elem){
			layer.close(prompt);
			if(!reason || reason  == ""){
				layer.alert("驳回请输入理由！");
				return;
			}			
			layer_open();			
			$.ajax({
				url: "order.class.php",
				type:"POST",
				data:{'batchcode':batchcode,'reason':reason,'return_status':2,'op':"confirmReturnMoney"},
				dataType:"json",
				success: function(res){
					 layer.close(index_layer);
					if(res.status==0){ 
						$(obj).remove();
						if(sendstatus==0){  //在没有发货之前申请退款
							$(obj).replaceWith('<a title="修改收件地址" onclick="showAddress('+batchcode+')"><img src="../../../common/images_V6.0/operating_icon/icon52.png"></a><a title="发货" id="button_delivery_'+batchcode+'" onclick="showDelivery('+batchcode+')"><img src="../../../common/images_V6.0/operating_icon/icon42.png"></a>'); 
							$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/notaffirm-icon.png"> <b>未发货</b>');
						} 						
					}
					if(res.errcode>0){
						// layer.alert(res.errmsg);
						layer.alert(res.errmsg,function() {
						window.location.reload();
					});
					}else{
						// layer.alert(res.msg);
						layer.alert(res.msg,function() {
						window.location.reload();
					});
					}
				},	
				error:function(){
					layer.close(index_layer);
					layer.alert("网络错误请检查网络");
				}						
			});							
		}); 	
	}, function(confirm3){
		layer.close(confirm2);
	});			
}
//退款管理 end

//显示退款
var returntype=-1;
function showGoodRefund(batchcode,retype){
	returntype = retype;
	var div = $("#refund_"+batchcode);
	$(".div_show").not(div).hide();
	if(div.is(":hidden")){
		div.fadeIn("slow");
	}else{
		div.fadeOut("slow");
	}
}
//显示退款 end

//退款
$(".good_refund").click(function(){	
	var batchcode 			= $(this).data('batchcode');
	var refundMoney_old     = $(this).data('money');
	var user_id             = $(this).data('user_id');
	var refundMoney         = $("#good_refund_"+batchcode).val();//退的零钱
	var currencyMoney       = $("#currency_refund_"+batchcode).val(); //退的购物币
	var paytype             = $(this).data('paytype');//支付类型
	var o_block_chain_price = $(this).data('blockchainprice');//区块链支付金额
	var unit                = $(this).data('unit');//单位
	var paycurrency         = $(this).data('paycurrency');


	if( refundMoney == undefined){
		refundMoney = 0;
	}
	if( currencyMoney == undefined){
		currencyMoney = 0;
	}
	
	var total_refundMoney	= Number(refundMoney)+Number(currencyMoney);
	if(paytype == "区块链积分支付"){
		total_refundMoney = parseFloat(refundMoney).toFixed(2);
	}
	layer.confirm('订单:'+batchcode+'</br>将退款 <b style="color:red">'+total_refundMoney+'</b> '+unit+'</br><b style="color:red">微信支付订单请先到微信支付详情界面进行手动退款！</b>', {
		btn: ['退款','取消'] 
	}, function(confirm){
		if(paytype != "区块链积分支付"){
			if( parseFloat(refundMoney) > (parseFloat(refundMoney_old)*100 - parseFloat(paycurrency)*100)/100){
				layer.alert("退款金额不能大于订单使用金额！");
				return;
			}

			if( parseFloat(currencyMoney) > parseFloat(paycurrency)){
				layer.alert("退款购物币不能大于订单支付的购物币总额！");
				return;
			}

			if(isNaN(total_refundMoney) || parseFloat(total_refundMoney) > parseFloat(refundMoney_old)){
				layer.alert("请输入正确的金额！");
				return;
			}
	    }else{
	    	if( parseFloat(refundMoney) > parseFloat(o_block_chain_price) ){
				layer.alert("退款金额不能大于订单区块链积分支付总额！");
				return;
			}
			if(isNaN(total_refundMoney)){
				layer.alert("请输入正确的金额！");
				return;
			}
			if(parseFloat(refundMoney) <= 0){
				layer.alert("退款金额需大于0！");
				return;
			}
			if( parseFloat(currencyMoney) > parseFloat(paycurrency)){
				layer.alert("退款购物币不能大于订单支付的购物币总额！");
				return;
			}
			if(isNaN(currencyMoney)){
				layer.alert("请输入正确的购物币！");
				return;
			}
	    }
		layer.close(confirm);
		layer_open();			
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'totalprice':refundMoney,'currencyMoney':currencyMoney,'retype':returntype,'op':"goodRefund"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					if(returntype==0){
						$('a[data-refund-batchcode='+batchcode+']').replaceWith('<a onclick="confirmOrder(this)" data-totalprice="'+refundMoney_old+'" data-batchcode="'+batchcode+'" data-user_id="'+user_id+'" title="确认完成"><img src="../../../common/images_V6.0/operating_icon/icon23.png"></a>'); 
						$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/refund-success.png"> <b style="color:#1eaf4e">退款[已处理]</b>');		
					}else{
						$('a[data-refund-batchcode='+batchcode+']').replaceWith('<a onclick="confirmOrder(this)" data-totalprice="'+refundMoney_old+'" data-batchcode="'+batchcode+'" data-user_id="'+user_id+'" title="确认完成"><img src="../../../common/images_V6.0/operating_icon/icon23.png"></a>'); 
						$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/confirm-return.png"> <b style="color:#1eaf4e">退货(换货)已确认</b>');								
					}
					$(".order_hide").fadeOut("slow"); 
				}
				if(res.errcode>0){
					// layer.alert(res.errmsg);
					layer.alert(res.errmsg,function() {
						window.location.reload();
					});
				}else{
					// layer.alert(res.msg);
					layer.alert(res.msg,function() {
						window.location.reload();
					});
				}
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
			
});
//退款 End

//退货管理
function returnGood(obj,return_type){	
	var batchcode = $(obj).data('batchcode');
	var reason = $(obj).data('reason');
	layer.confirm('顾客申请退货理由:'+reason+'<br/>请选择 订单:'+batchcode+' 的申请退货操作', {
		btn: ['同意退货','拒绝'] 
	}, function(confirm){
		
		layer.close(confirm);
		layer.prompt({
			formType: 0,
			title: '同意退货备注',			
			value: '同意退货申请'
		},function(reason, prompt, elem){
			layer.close(prompt);			
			layer_open();			
			$.ajax({
				url: "order.class.php",
				type:"POST",
				data:{'batchcode':batchcode,'reason':reason,'return_status':1,'op':"confirmReturnGood"},
				dataType:"json",
				success: function(res){
					 layer.close(index_layer);
					if(res.status==0){ 
						$(obj).remove(); 					
						$("#table_four_"+batchcode+" p:first-child").append('<b style="color:#C9302C"> [已同意]</b>');
					}
					if(res.errcode>0){
						// layer.alert(res.errmsg);
						layer.alert(res.errmsg,function() {
						window.location.reload();
					});
					}else{
						layer.alert(res.msg,function() {
						window.location.reload();
					});
					}
				},	
				error:function(){
					layer.close(index_layer);
					layer.alert("网络错误请检查网络");
				}						
			});				
			
		});  
			 
	}, function(confirm2){
		layer.close(confirm2);
		layer.prompt({
			formType: 0,
			title: '拒绝退货备注',			
			value: '拒绝退货申请'
		},function(reason, prompt, elem){
			layer.close(prompt);
			if(!reason || reason  == ""){
				layer.alert("驳回请输入理由！");
				return;
			}			
			layer_open();			
			$.ajax({
				url: "order.class.php",
				type:"POST",
				data:{'batchcode':batchcode,'reason':reason,'return_status':2,'op':"confirmReturnGood"},
				dataType:"json",
				success: function(res){
					 layer.close(index_layer);
					if(res.status==0){ 
						$(obj).remove(); 
						$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/affirm-icon.png"><b style="color:#31B0D5"> 已发货</b>');
					}
					if(res.errcode>0){
						// layer.alert(res.errmsg);
						layer.alert(res.errmsg,function() {
						window.location.reload();
					});
					}else{
						// layer.alert(res.msg);
						layer.alert(res.msg,function() {
						window.location.reload();
					});
					}
				},	
				error:function(){
					layer.close(index_layer);
					layer.alert("网络错误请检查网络");
				}						
			});				
			
		}); 
	
	});
			
}
//退货管理 end

//确认已退货
function confirmGoodRefund(obj){
	var batchcode = $(obj).data('batchcode');		
	layer.confirm('确定 订单:'+batchcode+' 已收到退货，确定后不可更改！', {
		btn: ['确认','取消'] 
	}, function(confirm){
		
		layer.close(confirm);	  
		layer_open();			
		$.ajax({
			url: "order.class.php",
			type:"POST",
			data:{'batchcode':batchcode,'op':"confirmGoodRefund"},
			dataType:"json",
			success: function(res){
				 layer.close(index_layer);
				if(res.status==0){
					$(obj).remove();
					$("#table_four_"+batchcode+" p:first-child").html('<img src="../../../common/images_V6.0/contenticon/return-goods.png"> <b style="color:#C9302C">申请退货(换货)[已收到退货]</b>');
				}
				if(res.errcode>0){
					layer.alert(res.errmsg,function() {
						window.location.reload();
					});
				}else{
					layer.alert(res.msg,function() {
						window.location.reload();
					});
				}
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
//确认已退货 end

//驳回身份
function reject_identity(obj){
	var user_id = $(obj).data('user_id');
	var batchcode = $(obj).data('batchcode');
	url='get_identity.php?callback=jsonpCallback_getidentity&user_id='+user_id+'&customer_id='+customer_id+'&batchcode='+batchcode;
	$.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_getidentity'
	});
}

function jsonpCallback_getidentity(results){
	var is_promoter = results[0].is_promoter;
	var is_consume = results[0].is_consume;
	var commision_level = results[0].commision_level;
	var user_id = results[0].user_id;
	var batchcode = results[0].batchcode;
	$('.reject_identity').show();
	$('#comission').hide();
	$('#shareholder').hide();
	var before_identity = $('#reject_'+batchcode).data('before_identity');
	var after_identity = $('#reject_'+batchcode).data('after_identity');
	var before_identity_promoter_num = $('#reject_'+batchcode).data('before_identity_promoter_num');
	var before_identity_shareholder_num = $('#reject_'+batchcode).data('before_identity_shareholder_num');
	content_ncomission = '';
	content_shareholder = '';
	if( is_promoter ){	//该用户是推广员就显示其他身份
		if( is_ncomission ){
			for(var i=0;i<commision_arr.length;i++){
				content_ncomission += '<option value="'+commision_arr[i]['level']+'">'+commision_arr[i]['exp_name']+'</option>';
			}
			$('#comission').show();
		}
		if( is_shareholder ){
			content_shareholder = '<option value="0">非股东身份</option>';
			for(var i=0;i<shareholder_arr.length;i++){
				content_shareholder += '<option value="'+shareholder_arr[i]['level']+'">'+shareholder_arr[i]['name']+'</option>';
			}
			$('#shareholder').show();
		}
	}
	$('.confirm_reject').data('user_id',user_id);
	$('.confirm_reject').data('batchcode',batchcode);
	$("#promoter option").each(function(){
		if($(this).val() == is_promoter){
			$(this).attr("selected", true);
		}
	});
	if (before_identity != '' && after_identity != ''){
		var identity_str = '购买前身份：'+before_identity+'<br/>'+'当前身份：'+after_identity;
		$('#identity_div').html(identity_str);
	}
	$('#comission').html(content_ncomission);
	select_promoter = before_identity_promoter_num<0?commision_level:before_identity_promoter_num;
	$("#comission option").each(function(){
		if($(this).val() == select_promoter){
			$(this).attr("selected", true);
		}
	});
	$('#shareholder').html(content_shareholder);
	select_shareholder = before_identity_shareholder_num<0?is_consume:before_identity_shareholder_num;
	$("#shareholder option").each(function(){
		if($(this).val() == select_shareholder){
			$(this).attr("selected", true);
		}
	});
	if (before_identity_promoter_num == 0){
		$("#promoter").find("option[value='-1']").attr("selected",true);
		$('#promoter').change();
	}
}
//确认驳回身份
function confirm_reject(obj){
	var user_id = $(obj).data('user_id');
	var batchcode = $(obj).data('batchcode');
	var promoter = $('#promoter').val();
	var comission = $('#comission').val();
	var shareholder = $('#shareholder').val();
	var reason = '大礼包退货驳回身份';
	layer.confirm('确定 驳回 id '+user_id+'的身份？', {
		btn: ['确认','取消'] 
	}, function(confirm){
		
		layer.close(confirm);	  
		layer_open();			
		$.ajax({
			url: "order.class.php?customer_id="+customer_id_en,
			type:"POST",
			data:{'user_id':user_id,'op':"rejectIdentity",'reason':reason,'promoter':promoter,'comission':comission,'shareholder':shareholder,'batchcode':batchcode},
			dataType:"json",
			success: function(res){
				layer.close(index_layer);
				if(res.status==0){
					$('#reject_'+batchcode).remove();
				}
				if(res.errcode>0){
					// layer.alert(res.errmsg);
					layer.alert(res.errmsg,function() {
						window.location.reload();
					});
				}else{
					// layer.alert(res.msg);
					layer.alert(res.msg,function() {
						window.location.reload();
					});
				}
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
	$('.reject_identity').hide();
}
function close_reject(){
	$('.reject_identity').hide();
}
$('#promoter').change(function(){
	var val = $(this).val();
	if( val > 0 ){	//选择粉丝则隐藏其他身份
		if ( is_ncomission ){
			$('#comission').show();
		}
		if ( is_shareholder ){
			$('#shareholder').show();
		}
	}else{
		$('#comission').hide();
		$('#shareholder').hide();
	}
})
//驳回身份 end

