function change_printer(cid){
	layer.confirm('您确定要标记已打印吗？<br/>提交后无法修改！', {
		btn: ['确认','取消'] 
	}, function(confirm){	
		$.ajax({
			url: "change_printer.php",
			type:"POST",
			data:{'customer_id':customer_id,'cid':cid},
			dataType:"json",
			success: function(res){
				if(res.status==0){
					$(".printer_"+cid).html("已打印");
					$(".change_printer_"+cid).hide();
				}
				//layer.alert(res.msg);
				layer.close(confirm);
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
function change_used(cid){
	layer.confirm('您确定要标记已使用吗？<br/>提交后无法修改！', {
		btn: ['确认','取消'] 
	}, function(confirm){	
		$.ajax({
			url: "change_used.php",
			type:"POST",
			data:{'customer_id':customer_id,'cid':cid},
			dataType:"json",
			success: function(res){
				if(res.status==0){
					$(".used_"+cid).html("已使用");
					$(".change_used_"+cid).hide();
				}
				layer.close(confirm);
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