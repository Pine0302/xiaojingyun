function change_sendstatus(obj){ 
	$("#is_charitable").val(obj);
	//console.log(obj);
}

function subBase(){
	var div_num        = $('.diy_one_two').length;
	var ii = 0;
	$("#diy_num").val(div_num);
	charitable_propotion = document.getElementById("charitable_propotion").value;
	is_charitable        = document.getElementById("is_charitable").value;
	integration_price    = document.getElementById("integration_price").value;
		if( charitable_propotion == "" ){
			alert('请输入慈善公益最低分配率！');
			return false;
		}else if(isNaN(charitable_propotion)){
			alert('慈善公益最低分配率必须为数字！');
			return false;
		}else if( charitable_propotion > 1 || charitable_propotion < 0){
			alert('慈善公益最低分配率必须为0~1之间！');
			return false;
		}
		if( integration_price == "" ){
			alert('请输入捐赠多少钱得1慈善分！');
			return false;
		}else if(isNaN(integration_price)){
			alert('捐赠必须为数字！');
			return false;
		}else if( 0 >= integration_price ){
			alert('捐赠必须大于0！');
			return false;
		}
		$('.singletext_con').each(function(){
			_val = $(this).val();
			if(_val<=0){
				alert('额度限制必须大于0！');
				ii = 1;
				return false;
			}
		});
		if(ii==1){
			return false;
		}
	//document.getElementById("upform").submit();
}


function diy_add(dtype){
	diy_num++;
	var str = "<tr class=\"diy_one_two\" id=\"diy_item_"+diy_num+"\">"
				+"<input type=hidden name=\"name_id"+diy_num+"\" id=\"name_id"+diy_num+"\" value=\"0\">"
				+ "<td><input type=text name=\"singletext_"+diy_num+"\" id=\"singletext_"+diy_num+"\" value=\"\" placeholder=\"请输入等级名称\"  /></td>"
				+ "<td><input type=text class=\"singletext_con\" name=\"singletext_con_"+diy_num+"\" id=\"singletext_con"+diy_num+"\" value=\"\" placeholder=\"请输入额度\"  />-不限</td>"
				+ "<td><a title=\"删除\"  href=\"javascript:diy_del("+diy_num+");\"><img src=\"../../../common/images_V6.0/operating_icon/icon04.png\"></a>&nbsp;&nbsp;<a title=\"添加\" href=\"javascript:diy_add(1,"+diy_num+");\"><img src=\"../../../common/images_V6.0/operating_icon/icon05.png\"></a></td>"
				+ "</tr>";
	$("#WSY_t1").append(str);
}




function diy_del(nu){
	var kid = $("#name_id"+nu).val();
	if( kid > 0 ){
		if(confirm("您确认要删除吗？")){
			$.getJSON("del_level.php", { kid: kid}, function(json){
				console.log("json.result : "+json.result);
				if(json.result == 1){
					document.getElementById("diy_item_"+nu).style.display="none";
					document.getElementById("diy_item_"+nu).innerHTML="";   
				}
			});
		}
	}else{
		document.getElementById("diy_item_"+nu).style.display="none";
		document.getElementById("diy_item_"+nu).innerHTML="";   		
	}
   
}

