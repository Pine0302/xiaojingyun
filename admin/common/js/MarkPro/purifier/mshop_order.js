function search_condition(url){
	var pay_begintime = document.getElementById("pay_begintime").value;//订单支付时间开始
	var pay_endtime = document.getElementById("pay_endtime").value;//订单支付时间结束
	var begintime = document.getElementById("begintime").value;//下单时间开始
	var endtime = document.getElementById("endtime").value;//下单时间结束
	var confirm_order_time_l = document.getElementById("confirm_order_time_l").value;//确认订单时间开始
	var confirm_order_time_r = document.getElementById("confirm_order_time_r").value;//确认订单时间结束
	var search_batchcode = document.getElementById("search_batchcode").value;//订单号
	var search_name = document.getElementById("search_name").value;//搜索姓名
	var search_product_name = document.getElementById("search_product_name").value;//搜索姓名
	var search_phone = document.getElementById("search_phone").value;//搜索姓名
	var search_name_type = document.getElementById("search_name_type").value;//微信名还是收货名
	var search_paystyle = document.getElementById("search_paystyle").value;//支付方式
	var search_supply_id = document.getElementById("search_supply_id").value;//订单供应商ID
	var search_attribution_type = document.getElementById("search_attribution_type").value;//订单归属分类
	var search_order_ascription = document.getElementById("search_order_ascription").value;//订单归属
	/*郑培强*/
	var search_status=document.getElementById("search_status").value;
	/*郑培强*/
	var search_class = document.getElementById("search_class").value;//订单管理类型
	var pagesize = document.getElementById("pagesize").value;//每页记录数
	if(from_page==0){
		var continued_day = document.getElementById("continued_day").value;//订单所属
	}
	var orgin_from = document.getElementById("orgin_from").value;//订单所属
	var search_shop_id = document.getElementById("search_shop_id").value;//门店ID
	var search_agent_id = document.getElementById("search_agent_id").value;//代理商ID
	var search_pre_delivery_type = document.getElementById("search_pre_delivery_type").value;//预配送订单
	var orgin_type = document.getElementById("orgin_type").value;//订单类型
	var url2="&search_class="+search_class+"&pagesize="+pagesize+"&search_name_type="+search_name_type;
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
	if(confirm_order_time_l != "" && confirm_order_time_l != undefined){
		url2=url2+"&confirm_order_time_l="+confirm_order_time_l;
	}
	if(confirm_order_time_r != "" && confirm_order_time_r != undefined){
		url2=url2+"&confirm_order_time_r="+confirm_order_time_r;
	}
	if(search_batchcode !=""){
		url2=url2+"&search_batchcode="+search_batchcode;
	}
	if(search_name !=""){
		url2=url2+"&search_name="+search_name;
	}
	if(search_product_name !=""){
		url2=url2+"&search_product_name="+search_product_name;
	}
	if(search_phone !=""){
		url2=url2+"&search_phone="+search_phone;
	}
	if(search_paystyle !=""){
		url2=url2+"&search_paystyle="+search_paystyle;
	}
	if(search_supply_id !=""){
		url2=url2+"&search_supply_id="+search_supply_id;
	}
	if(from_page == 0)
	{
		if(continued_day !=""){
			url2=url2+"&continued_day="+continued_day;
		}
	}
	if(search_order_ascription !=""){
		url2=url2+"&search_order_ascription="+search_order_ascription;
	}else if(search_attribution_type==1){
		alert("没用此合作商！");
		return;
	}else if(search_attribution_type==2){
		alert("没用此代理商！");
		return;
	}
	if(search_attribution_type !=""){
		url2=url2+"&search_attribution_type="+search_attribution_type;
	}
	/*郑培强*/ 
	if(search_status !=""){
		url2=url2+"&search_status="+search_status;
	}
	/*郑培强*/
	if(orgin_from !=""){
		url2=url2+"&orgin_from="+orgin_from;
	}
	if(search_shop_id !=""){
		url2=url2+"&search_shop_id="+search_shop_id;
	}	
	if(search_agent_id !=""){
		url2=url2+"&search_agent_id="+search_agent_id;
	}
	if(search_pre_delivery_type !=""){
		url2=url2+"&search_pre_delivery_type="+search_pre_delivery_type;
	}
	if(orgin_type > 0){
		url2=url2+"&orgin_type="+orgin_type;
	}		
	document.location=url+url2;
}

function searchForm(){
	var search_begintime = $("#begintime").val();
	var search_endtime   = $("#endtime").val();

	 if(search_endtime<search_begintime){
		 alert("申请时间有误，结束时间不能比开始时间早！");
		return;
	 }

	var pay_begintime = $("#pay_begintime").val();
	var pay_endtime   = $("#pay_endtime").val();
	 if(pay_endtime<pay_begintime){
		 alert("申请时间有误，结束时间不能比开始时间早！");
		return;
	 }
	var url="order.php?customer_id="+customer_id+"&pagenum=1&from_page="+from_page;
	search_condition(url); 
}

/*全选*/
function change_box(){
	var all_box = $(".all_checkbox").is(':checked')
	if( all_box ){
		$(".checkbox").prop("checked",true);
	}else{
		$(".checkbox").prop("checked",false);
	}
}

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
					$('[data-batchcode='+box_val+']').replaceWith('<a title="删除" data-batchcode="'+box_val+'" onclick="delOrder(this)"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>');	
					$(".red_"+box_val).hide();
					$("#table_five_"+box_val).html('<span class="btn btn-success">已完成</span>');
				}
				$(".batchFinish").hide();
				$(".wait_div").hide();
			}
			layer.alert(res.msg);
		},
		error:function(e){
			layer.alert("网络错误请检查网络");
		}
	});
	//alert(sed);
}

/*商家备注保存开始*/
function save_merchant_remark(batchcode){
	var content=$(".merchant_remark_"+batchcode).val();
	$.ajax({
		type: "post",
		url:'order.class.php',
		data:{'content':content,'batchcode':batchcode,'op':'merchant_remark'},  
		dataType:"json",
		success:function(res){
			if(res.status==0){
				$('.change_merchant_remark'+batchcode).css("display","inline-block");
				$('.save_merchant_remark_'+batchcode).css("display","none");
				$('.merchant_remark_'+batchcode).attr("disabled",true);
			}
			layer.alert(res.msg);
		},
		error:function(e){
			layer.alert("网络错误请检查网络");
		}
	});
}
/*商家备注保存结束*/
/*商家备注修改开始*/
function change_merchant_remark(batchcode){
	$('.merchant_remark_'+batchcode).attr("disabled",false);
	$('.change_merchant_remark'+batchcode).css("display","none");
	$('.save_merchant_remark_'+batchcode).css("display","inline-block");
}
/*商家备注修改结束*/

/*提交excel快递单号开始*/

function importMember(){
	layer_open();	
	var f_content = document.getElementById("excelfile").value;
	var fileext=f_content.substring(f_content.lastIndexOf("."),f_content.length)

	fileext=fileext.toLowerCase();
	if (fileext!='.xls'){
		layer.close(index_layer);
		layer.alert("对不起，导入数据格式必须是xls格式文件哦，请您调整格式后重新上传，谢谢 ！");
		return false;
	}
	document.getElementById("frm_import").submit();
}
/*提交excel快递单号结束*/

/*留言信息开始*/
function message(batchcode){
	var content   = $("#order_talk_"+batchcode).val();
	var supply_id = $("#supply_id_"+batchcode).val();
	$.ajax({
		type: "post",
		url:'order_talk_do.php',
		data:{'content':content,'batchcode':batchcode,'supply_id':supply_id,'customer_id':customer_id},  
		dataType:"json",
		success:function(res){
			if(res.status==0){
				var cnt = '<div class="order_dl04_div"><h3><a><a>我</a></a>留言于'+res.time+'</h3><p style="text-align: center;">'+content+'</p></div>'; 
				$('.order_dd_hidden_'+batchcode).prepend(cnt);
				$("#order_talk_"+batchcode).html('');
			}
			layer.alert(res.msg);
		},
		error:function(e){
			layer.alert("网络错误请检查网络");
		}
	});
}

/*留言信息结束*/

