function savecode(){
	var name = $("#name").val();
	if(name == ""){
		alert('请输入名称!');
		return;
	}
	var num = $("#num").val();
	if(num == ""){
		alert('请输入数量!');
		return;
	}
	if(isNaN(num)){
		alert('数量必须为数字');
		return false;
	}
	if( num < 1 ){
		alert('数量必须大于0');
		return false;
	}
	
	layer.confirm('您确定要提交吗？<br/>提交后无法修改！', {
		btn: ['确认','取消'] 
	}, function(confirm){	
		$("#btnSave").attr("disabled", true);
		$(".wx_loading_icon").show();
		layer.close(confirm);
		$.ajax({
			url: "save_code.php",
			type:"POST",
			data:{'customer_id':customer_id,'name':name,'num':num,'p_id':p_id,'supplier_id':-1},
			dataType:"json",
			success: function(res){
				if(res.status==0){
					location.href='security_code.php?customer_id='+customer_id+"&product_id="+p_id;
				}
			},	
			error:function(res){
				//layer.close(index_layer);
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

function getPclass(p_class){
	if(p_class>0){
		url='get_shop_list.php?callback=jsonpCallback_getcolumnlst&p_class='+p_class+'&customer_id='+customer_id;
		$.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_getcolumnlst'
		});
	}else{
		$("#p_id").hide();
 
	}
}

function jsonpCallback_getcolumnlst(results){
	var len = results.length;
	$("#p_id").show();
	var strs = "<option value=-1 >--请选择--</option>"  
	if(len>0){
		for(var i=0;i<len;i++){
			var obj_id_t = results[i].obj_id;
			var obj_title = results[i].obj_title;
			strs = strs+"<option value="+obj_id_t+" >"+obj_title+"</option>";
		} 
	}
	//parentTypesSelect(<?php echo $parent_class; ?>);
	$("#p_id").html(strs);
	$("#p_id option").each(function(){
		if($(this).val() == pid_d){
			$(this).attr("selected", true);
		}
	});

}