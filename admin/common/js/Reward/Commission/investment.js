
/*招商奖励校验是否推广员*/
function check_promoter(parent_id,customer_id,obj){
	var op  = 'change';
	var url = '/weixinpl/back_newshops/Mode/supplier/edit_invest.php?customer_id='+customer_id;
	
	$('.tui').remove();
	$.post(url,{op:op,parent_id:parent_id},function(da){
		if(da.status == 0)
		{
            alert(da.msg);
			$(obj).val('');
			return false;
		}
		else
		{
		var html = da.name+"("+da.weixin_name+")";
		var html2 =  '<span class="tui">';
		if(da.name!= null){
			html2 += da.name;
		}
		if(da.weixin_name != null){
			html2 += "("+da.weixin_name+")";
		}
		html+='</span> ';
		$(obj).after(html2);
		}
	},'json');
}

function init(obj,name,weixin_name){
	
	var html = name+"("+weixin_name+")";
	var html2 =  '<span class="tui">';
	if(name!= ''){
		html2 += name;
	}
	if(weixin_name != ''){
		html2 += "("+weixin_name+")";
	}
	html+='</span> ';
	$(obj).after(html2);
	
}




