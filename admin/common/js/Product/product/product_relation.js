var pic;
$(function(){	
	pic = $(".pic").width();
	$(".pic").css("height",pic+"px");
});
function add_pro(){
	$("#add_pro_button").attr("disabled", true);
	var tleft = event.pageX;
	var ttop = event.pageY;
	$("#add_pro_div").css("left",tleft+10+"px");
	$("#add_pro_div").css("top",ttop+10+"px");
	$("#add_pro_div").show();
}

/*加载产品开始*/
function parentTypesSelect(parent_class){
	if(parent_class>0){
		url='get_shop_list.php?callback=jsonpCallback_getcolumnlst&parent_class='+parent_class+'&customer_id='+customer_id;
		$.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_getcolumnlst'
		});
	}else{
		$(".sel_pro_p").hide();
 
	}
}
function jsonpCallback_getcolumnlst(results){
	var len = results.length;
	$(".sel_pro_p").show();
	var strs = "<option value=-1 >--请选择--</option>"  
	if(len>0){
		for(var i=0;i<len;i++){
			var obj_id_t = results[i].obj_id;
			var obj_title = results[i].obj_title;
			strs = strs+"<option value="+obj_id_t+" >"+obj_title+"</option>";
		} 
	}
	$("#parent_pid_select").html(strs);

}
/*加载产品结束*/


function cancelBtn2(btn){
	$(btn).parent().hide();
	$("#add_pro_button").attr("disabled", false);
}

/*添加关联产品开始*/
function relation_pro(){
	$("#add_pro_div").hide();
	layer.confirm('是否关联此产品？？', {
		title:'确定发放？',		
		btn: ['确定','取消'] 
	}, function(confirm){
		layer.close(confirm);
		$(".batchFinish").show();
		$(".wait_div").show();
		var pid = $("#parent_pid_select").val();
		$.ajax({
			url: "relation_pro.php",
			type:"POST",
			data:{'pid':pid,'product_id':product_id,'customer_id':customer_id},
			dataType:"json",
			success: function(json){
				if( json.status == 1 ){ 				
					layer.alert("添加成功！");	
					var html = "";
					html += '<dd id="dd_pic" class="dd_'+json.pid+'">';
					html += '<span class="pic" style="height:'+pic+'px" >';
					html += '<img src="'+json.default_imgurl+'" title="">';
					html += '</span>';
					html += '<span class="span_input">';
					html += '<p class="crew_name" id="em_'+json.pid+'">'+json.name+'</p>';
					html += '</span>';
					html += '<div class="div_imga">';
					html += '<a><img src="../../../common/images_V6.0/operating_icon/icon04.png" title="删除" onclick="doDeleteCrew('+json.pid+','+product_id+')"></a>';
					html += '</div>';
					html += '</dd>';
					$(".material_con").prepend(html);
				}else if(json.status == 2){
					layer.alert("关联产品已存在！");
				}else if(json.status == 13){
					$("#add_pro_button").hide();
					layer.alert("关联产品最多只能关联13个！");
				}else{
					layer.alert("添加失败！");
				}	
				$(".batchFinish").hide();
				$(".wait_div").hide();
			},	
			error:function(json){
				layer.alert("网络错误请检查网络");
			}						
		}); 		
		$("#add_pro_button").attr("disabled", false);		
	}, function(json){
		$("#add_pro_button").attr("disabled", false);
		layer.msg('已取消删除', {
			time: 4000,
			btn: ['确认'],  
			icon:1
		});
	});
	

}
/*添加关联产品结束*/

//删除关联产品
function doDeleteCrew(pid,product_id){
	layer.confirm('是否确定删除此产品关联？', {
		title:'删除组员',		
		btn: ['确定删除','取消'] 
	}, function(){
		$.ajax({
			url: "delrelation_pro.php",
			type:"POST",
			data:{'pid':pid,'product_id':product_id,'customer_id':customer_id},
			dataType:"json",
			success: function(json){
				if( json.status == 1 ){ 				
					layer.alert("删除成功！");	
					$(".dd_"+pid).remove();
					$("#add_pro_button").show();
				}else{
					layer.alert("删除失败！");
				}	
				$(".batchFinish").hide();
				$(".wait_div").hide();
			},	
			error:function(json){
				layer.alert("网络错误请检查网络");
			}						
		});	
	}, function(json){
		layer.msg('已取消删除', {
			time: 4000,
			btn: ['确认'],  
			icon:1
		});
	});
}