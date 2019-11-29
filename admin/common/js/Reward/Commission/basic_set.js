function submitV(a){
	var stu = save_commission_setting();
	var is_submit = true;
	if($("#calc").val()===1){
		var calcval=$(".WSY_calcbox .moshi input:radio[name='calculation']:checked").val();
		if(val==null){
            alert("计算模式必选!");
            return false;
        }
	}
	if(stu){
		var is_promoter_permanent = $('#is_promoter_permanent').val();	//推广员有效期开关
		if( is_promoter_permanent == 0 ){	//开启推广员有效期
			var promoter_term_of_validity_initial = $('input[name=promoter_term_of_validity_initial]').val();
			if( promoter_term_of_validity_initial == '' ){
				alert('请设置初次有效时长！');
				is_submit = false;
			}

			var prmmoter_remark = $('input[name=prmmoter_remark]').val()
			if( prmmoter_remark == '' ){
				alert('请设置过期提示时间！');
				is_submit = false;
			}
		}
		if( is_submit ){
			document.getElementById("upform").submit();
		}

	}

}
function change_issell(obj){
	$("#issell").val(obj);
}
function change_issell_model(obj){
	$("#issell_model").val(obj);
	if(obj == 2){
		$("#dl_is_shop_deductible").show();
		$("#dl_is_extension_deductible").show();
	}else{
		$("#dl_is_shop_deductible").hide();
		$("#dl_is_extension_deductible").hide();
	}
}
function change_is_shop_deductible(obj){
	$("#is_shop_deductible").val(obj);
}
function change_is_extension_deductible(obj){
	$("#is_extension_deductible").val(obj);
}
function change_calc(obj){
	$("#calc").val(obj);
}
if(pid_d>0){
	var link = $("select[name=parent_types_select]");
	link.change();
}
function parentTypesSelect(parent_class){
	if(parent_class>0){
		url='../../../back_commonshop/get_shop_list.php?callback=jsonpCallback_getcolumnlst&parent_class='+parent_class+'&customer_id='+customer_id;
		$.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_getcolumnlst'
		});
	}else{
		$("#parent_pid_select").hide();

	}
}
function jsonpCallback_getcolumnlst(results){
	var len = results.length;
	$("#parent_pid_select").show();
	var strs = "<option value=-1 >--请选择--</option>"
	if(len>0){
		for(var i=0;i<len;i++){
			var obj_id_t = results[i].obj_id;
			var obj_title = results[i].obj_title;
			strs = strs+"<option value="+obj_id_t+" >"+obj_title+"</option>";
		}
	}
	//parentTypesSelect(<?php echo $parent_class; ?>);
	$("#parent_pid_select").html(strs);
	$("#parent_pid_select option").each(function(){
		if($(this).val() == pid_d){
			$(this).attr("selected", true);
		}
	});

}
  function change_autoupgrade(type){
     switch(type){
	    case 0:
	    case 3:
	    case 5:
		case 7:
			$(".div_label").hide();
		break;
	    case 1:
			$(".div_label").hide();
			$("#div_autoupgrade_money").show();
		break;
	    case 4:
			$(".div_label").hide();
			$("#div_autoupgrade_money_4").show();
		break;
	    case 6:
			$(".div_label").hide();
			$("#div_autoupgrade_money_6").show();
		break;
	    case 2:
			$(".div_label").hide();
			$("#div_autoupgrade_money_2").show();
		break;
	 }

  }