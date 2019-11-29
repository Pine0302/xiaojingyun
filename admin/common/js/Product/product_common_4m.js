/*
4M商家权限
is_change_pros_price : 厂家控制商家修改产品价格

*/
$(function(){
	//隐藏修改价格按钮
	var is_change_pros_price = $("input[name='4m_is_change_pros_price']").val();			//
	
	if(0 == is_change_pros_price){
		//$('.WSY_pricebox').next().hide();
	}
	
	
	
});

//隐藏框架
function hide_div_out5(){
	$('#iframe').hide();
}

//4M修改产品权限
$(function(){
	
	$(".pro_4m_pro").click(function(){
		
		var tleft = event.pageX;
		var ttop = event.pageY; 
		$("#div_out4").css("left",tleft-700+"px");
		$("#div_out4").css("top",ttop+10+"px");
		var do4_height = $("#div_out4").contents().find('body').css('height');
		console.log(do4_height);
		$("#div_out4").css("height",do4_height);
		var pid = $(this).data("pro-id");	
		var url = './public/4m_control.php?customer_id='+customer_id+'&product_id='+pid;
		$.ajax({
			url:url,
			dataType:'json',
			data:{},
			success:function(result){
				var html = '';
				html += '<div id="div_inner4">';
				html += '<img src="../../Common/images/Product/shanchuicon.png" style="width:25px;height:25px;border-radius: 5px;"/>';
				html += '</div>';
				html += '<div class="div_out_title" style=" margin-top: 1%;">';
				html += '<font style="font-weight: bold;">厂家控制产品显示权限：</font><span id="span_stock_title"></span>';
				html += '</div>';
				html += '<div class="div_out_item2" style="    overflow: inherit;overflow-x: inherit;" >';
				html += '<ul style="width: 95%;" class="WSY_table tblcls ul4" >';

				$.each(result.data,function(i,val){
					html += '<dd>';
					html += '<input type="checkbox" name="links[]" data-cid="'+val.lowlevel_customer_id+'"   data-adid="'+val.pro_adminuser_id+'"  data-pid="'+val.lowlevel_pros_id+'"  value="'+result.SubCustomers_id+'" ';
					if(val.iis_allow){
						html += 'checked';
					}
					html += ' class="link"  /><label>'+val.pro_customer_name+'</label>';
					html += '</dd>';
				});
				
				
				
				
				html += '</ul>';
				html += '</div>';
				html += '<input type="hidden"  id="product_id" value="'+pid+'"/>';
				html += '<input type="hidden" id="adminuser_parent_id" value="'+result.orgin_adminuser_id+'"/>';//上级就是自己的渠道号
				html += '<input type="button" value="确定修改" id="btn_changestock" class="div_out_btn" onclick="comfirm()"/>';
				
				$('#div_out4').html(html);
				
				$('#div_out4').show();
				
				$('#div_inner4 img').click(function(){
					$('#div_out4').hide();
					
					parent.window.hide_div_out5(); 
				});
			},
			error:function(e){
				alert('错误');
			}
		})
		// $("#iframe").attr('src',url);		

	});

});

function comfirm(){
	
	var data_array = new Array();		
	
	$("input[name='links[]']").each(function(){
			var arr = new Array();
			var _this = $(this);
			var customer_id = _this.data('cid');
			var adminuser_id = _this.data('adid');
			var lowlevel_pros_pid = _this.data('pid');
			var product_id = $('#product_id').val();
			var adminuser_parent_id = $('#adminuser_parent_id').val();
			var c_box_stu = _this.prop('checked');
			arr[0] =  customer_id;		 		//产品对应的商家ID		
			arr[1] =  adminuser_id;		 		//产品对应的渠道ID		
			arr[2] =  adminuser_parent_id;		//渠道上级ID
			arr[3] =  c_box_stu;				//勾选的结果
			arr[5] =  product_id;				//上级产品ID
			arr[4] =  lowlevel_pros_pid;		//产品对应的ID		
			//console.log(arr);
			data_array.push(arr);		//数组拼接

		
	});			
			console.log(data_array);
			data_array = JSON.stringify(data_array);  //数组转json

			$.ajax({
					url: "public/save_4m.php?customer_id="+customer_id_en+"&stu=save",
					data:{
						data_array:data_array	
						
					},
					type: "POST",
					dataType:'json',
					async: true,     
					success:function(res){

						if(res.code == 10002){
							alert(res.msg);
						}else{
							alert(res.msg);
						}
						setTimeout(function(){
						
						history.go(0);
						
						},500);
						
						
					},
					error:function(er){
					
					}
			});



}
