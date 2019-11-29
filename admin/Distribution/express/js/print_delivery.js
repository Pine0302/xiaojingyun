
function print2order(val,customer_id,single){
	print_dv(val,customer_id,single);	
	if(single==1){LODOP.PRINT();}	
}	
function print_view(val,customer_id,single){
	print_dv(val,customer_id,single);
	if(single==1){LODOP.PREVIEW();}
}
function print_manag(val,customer_id,single){
	print_dv(val,customer_id,single);
	if(single==1){LODOP.PRINT_SETUP();}
}

function print_dv(val,customer_id,single){
	var url_para = '';
	if(single==1){url_para = "batchcode="+val;}else{url_para = "print_temp_id="+val+"&customer_id="+customer_id;}
	$.ajax({url:"/weixinpl/back_newshops/Order/order/ax_print_order.php?"+url_para, async:false, success:function(json){

		var data = $.parseJSON(json);//console.log(data);return;
		$.each(data, function(i_data) {
			var order_info = data[i_data].order_info;
			var print_info = data[i_data].print_info;
			
			if(print_info.length == 0){alert("您要打印的订单还没有绑定快递模板！");return;}
			//console.log(print_info);return;
					 
			var p_direction = 1; //1---纵向打印，固定纸张；2---横向打印，固定纸张；
			var p_page_w = parseInt(print_info.paper_width); //纸张宽度
			var p_page_h = parseInt(print_info.paper_height); //纸张高度
			//console.log(arithmetic2px(p_page_w));
			LODOP=getLodop();
			var init_name = print_info.print_name + '-' + order_info.id;
			//p_page_w = arithmetic2px(p_page_w); p_page_h = arithmetic2px(p_page_h);
			//LODOP.PRINT_INIT(0,0,p_page_w,p_page_h,init_name);  
			var actual_pager_with = arithmetic2px(p_page_w);
			var actual_pager_height = arithmetic2px(p_page_h);
			LODOP.PRINT_INITA(0,0,actual_pager_with,actual_pager_height, init_name);
			LODOP.SET_PRINT_PAGESIZE(p_direction,p_page_w+'mm',p_page_h+'mm','');
			
			
			var obj_items_params = $.parseJSON(print_info.items_params);
			console.log(obj_items_params);
			console.log(order_info);
			$.each(obj_items_params, function(i_items_params){
				
				setup_items(obj_items_params[i_items_params], 'ordersn', order_info.order_number, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'realname2', order_info.receiver, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'province2', order_info.receive_province, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'address2', order_info.receive_address, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'zipcode2', order_info.receive_zipcode, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'mobile2', order_info.receive_phone, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'sitename', order_info.shop_name, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'address', order_info.ship_address, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'tel', order_info.ship_phone, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'year', order_info.ship_year, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'month', order_info.ship_month, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'day', order_info.ship_day, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'city2', order_info.receive_city, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'district2', order_info.receive_area, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'itemattr', order_info.goods_spec, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'itemcode', order_info.goods_number, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'itemname', order_info.goods_name, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'itemsn', order_info.goods_no, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'itemnum', order_info.goods_quantity, actual_pager_with);
				setup_items(obj_items_params[i_items_params], 'itemmark', order_info.goods_mark, actual_pager_with);
				

				
			});   
			if(single==0){LODOP.PRINT();}
		});
	}});
	 
}
function setup_items(obj_items_params, str_items_id, str_order_name, actual_pager_with){
	if(obj_items_params.id == str_items_id){
		var print_html = "<span style='font-size:"+obj_items_params.fontSize+";letter-spacing:"+obj_items_params.letterSpacing+";font-weight:"+obj_items_params.bold+";font-style:"+obj_items_params.italic+";'>"+str_order_name+"</span>";
		var actual_top = html_px2print_px(obj_items_params.top, actual_pager_with);
		var actual_left = html_px2print_px(obj_items_params.left, actual_pager_with);
		var actual_width = html_px2print_px(obj_items_params.width, actual_pager_with);
		var actual_height = html_px2print_px(obj_items_params.height, actual_pager_with);
		LODOP.ADD_PRINT_HTM(actual_top, actual_left, actual_width, actual_height, print_html);
	}
}
function arithmetic2px(mm){
	var result = 0.0;
	result = mm/25.4;			
	result = Math.round(result*96);			
	return result;
}
function html_px2print_px(val, actual_pager_with){
	var reg = /[1-9][0-9]*/g;  
	var number = val.match(reg);  
	var html_px = parseInt(number[0]);
	var parameter = 740/actual_pager_with;
	var result_var = html_px/parameter;
	return Math.round(result_var);
}