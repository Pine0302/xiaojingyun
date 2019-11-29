$(function(){ 
　　add_crew();
});
function add_crew(){
	$(".add").click(function(){
		$(this).html("");
		var user_id = $(this).attr("uid");
		var content = "";
		var name = $(".name_"+user_id).text();
		$.ajax({
			url: "crew.class.php",
			type:"POST",
			data:{'op':"add",'user_id':user_id,"group_id":group_id},
			dataType:"json",
			success: function( res ){
				if( res.status==0 ){
					content += '<tr class="ctr_'+user_id+'"><td class="del" uid="'+user_id+'">-</td><td class="cname_'+user_id+'">'+name+'</td><td>'+user_id+'</td></tr>';
					$(".crew_list_table").append(content);
				}
			},	
			error:function(res){
				layer.alert("网络错误请检查网络");
			}						
		});	
	});
}