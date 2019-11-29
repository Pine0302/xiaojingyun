function savePackages(a){
	var bonus_money  		= $("#bonus_money").val();//奖励金额
	var bonus_first_ratio  	= $("#bonus_first_ratio").val();//首次奖励金额
	if( parseFloat(bonus_money) < parseFloat(bonus_first_ratio) ){
		//alert("请输入名称！");
		alert('首次奖励金额不能大于奖励金额!');
		return false;
	}

	var name = $("#package_name").val();
	if(name == ""){
		alert('请输入礼包名称!');
		return;
	}

	var package_type_str=''; 			//用户选择的礼包类型组成的字符串
	var package_type_val=new Array();	//用户选择的礼包类型组成的数组
	$("input[name='package_type[]']:checkbox").each(function(){ 
	  if($(this).attr('checked')){ 
		package_type_str+=$(this).val()+','; 
	  } else{
          package_type_str+='0,';
      }
	}) 
	package_type_val=package_type_str.split(","); 
/* 	if( '' == package_type_str ){
		alert('请选择礼包类型!');
		return;
	} */
	if( 1 == package_type_val[0] ){
		var promoter_level = $("#promoter_level").val();
		if( 1 > promoter_level ){
			alert('请选择推广员礼包等级!');
			return;
		}
	}
	if( 2 == package_type_val[1] ){
		var shareholder_level = $("#shareholder_level").val();
		if( 1 > shareholder_level ){
			alert('请选择股东等级!');
			return;
		}
	}

    // if( 3 == package_type_val[2] ){
    //     var three_id = $("#three_id").val();
    //     if( 1 > three_id ){
    //         alert('请选择3*3等级!');
    //         return;
    //     }
    // }
    if( 4 == package_type_val[2] ){
		// alert(1);return false;
        var dh_proxy_grade = $("#dh_proxy_grade").val();
        if( 1 > dh_proxy_grade ){
            alert('请选择订货商礼包等级!');
            return;
        }
    }
	// console.log(package_type_val);return false;
    var card_id = $("#shop_card_id_select option:selected").val();
    var dh_account_recharge = $("[name='dh_account_recharge']").val();
	var is_card_recharge = $("[name='is_card_recharge']").is(':checked');
	if (is_card_recharge==true ) {
		if(-1 == card_id&&dh_account_recharge <=0){
            alert("请选择会员卡或订货商货款充值金额!");
            return;
		}else if(dh_account_recharge>0&&-1 != card_id){
            // alert("不能会员卡和订货商货款充值金额同时选择!");
            // return;
		}
	}

    //订货商高级奖励判定function
	if(check_ordering_reward()==0){
		return false;
	}

	var price = $("#price").val();
	if(isNaN(price)){
		alert('价格必须为数字');
		return false;
	}
	if( 0 >= parseFloat(price) ){
		alert('价格要大于0!');
		return;
	}
	var init_reward = document.getElementById("init_reward").value;
	if(isNaN(init_reward)){
		alert('推广比例必须为数字');
		return false;
	} 
	if( 0 > parseFloat(init_reward) ){
		alert('推广比例必须在0-1之间');
		return false;
	}
	if( 1 < parseFloat(init_reward) ){
		alert('推广比例必须在0-1之间');
		return false;
	} 
	var reward_level = document.getElementById("reward_level").value;
	if(isNaN(reward_level)){
		alert('推广深度必须为数字');
		return false;
	}
	if( 0 > parseFloat(reward_level) ){
		alert('推广深度必须大于等于0');
		return false;
	}
	if(is_8shopdistr==1){
		
		if( 8 < parseFloat(reward_level) ){
			alert('推广深度必须小于等于8');
			return false;
		}
	}else{
		if( 3 < parseFloat(reward_level) ){
			alert('推广深度必须小于等于3');
			return false;
		}
	}
	
	
	var init_reward_1 = document.getElementById("init_reward_1").value;
	if(isNaN(init_reward_1)){
		alert('推广比例必须为数字');
		return false;
	}
	if( 0 > parseFloat(init_reward_1) ){
		alert('推广比例必须在0-1之间');
		return false;
	}
	if( 1 < parseFloat(init_reward_1) ){
		alert('推广比例必须在0-1之间');
		return false;
	} 
	var init_reward_2 = document.getElementById("init_reward_2").value;
	if(isNaN(init_reward_2)){
		alert('推广比例必须为数字');
		return false;
	}
	if( 0 > parseFloat(init_reward_2) ){
		alert('推广比例必须在0-1之间');
		return false;
	}
	if( 1 < parseFloat(init_reward_2) ){
		alert('推广比例必须在0-1之间');
		return false;
	} 
	var init_reward_3 = document.getElementById("init_reward_3").value;
	if(isNaN(init_reward_3)){
		alert('推广比例必须为数字');
		return false;
	}
	if( 0 > parseFloat(init_reward_3) ){
		alert('推广比例必须在0-1之间');
		return false;
	}
	if( 1 < parseFloat(init_reward_3) ){
		alert('推广比例必须在0-1之间');
		return false;
	} 
	var d = parseFloat(init_reward_1) + parseFloat(init_reward_2)+parseFloat(init_reward_3);
	if(is_8shopdistr==1){
		var init_reward_4 = document.getElementById("init_reward_4").value;
		if(isNaN(init_reward_4)){
			alert('推广比例必须为数字');
			return false;
		}
		if( 0 > parseFloat(init_reward_4) ){
			alert('推广比例必须在0-1之间');
			return false;
		}
		if( 1 < parseFloat(init_reward_4) ){
			alert('推广比例必须在0-1之间');
			return false;
		} 
		var init_reward_5 = document.getElementById("init_reward_5").value;
		if(isNaN(init_reward_5)){
			alert('推广比例必须为数字');
			return false;
		}
		if( 0 > parseFloat(init_reward_5) ){
			alert('推广比例必须在0-1之间');
			return false;
		}
		if( 1 < parseFloat(init_reward_5) ){
			alert('推广比例必须在0-1之间');
			return false;
		} 
		
		var init_reward_6 = document.getElementById("init_reward_6").value;
		if(isNaN(init_reward_6)){
			alert('推广比例必须为数字');
			return false;
		} 
		if( 0 > parseFloat(init_reward_6) ){
			alert('推广比例必须在0-1之间');
			return false;
		}
		if( 1 < parseFloat(init_reward_6) ){
			alert('推广比例必须在0-1之间');
			return false;
		}
		var init_reward_7 = document.getElementById("init_reward_7").value;
		if(isNaN(init_reward_7)){
			alert('推广比例必须为数字');
			return false;
		} 
		if( 0 > parseFloat(init_reward_7) ){
			alert('推广比例必须在0-1之间');
			return false;
		}
		if( 1 < parseFloat(init_reward_7) ){
			alert('推广比例必须在0-1之间');
			return false;
		}
		var init_reward_8 = document.getElementById("init_reward_8").value;
		if(isNaN(init_reward_8)){
			alert('推广比例必须为数字');
			return false;
		}
		if( 0 > parseFloat(init_reward_8) ){
			alert('推广比例必须在0-1之间');
			return false;
		}
		if( 1 < parseFloat(init_reward_8) ){
			alert('推广比例必须在0-1之间');
			return false;
		}
		var d = parseFloat(init_reward_1)*100 + parseFloat(init_reward_2)*100+parseFloat(init_reward_3)*100+parseFloat(init_reward_4)*100+parseFloat(init_reward_5)*100+parseFloat(init_reward_6)*100+parseFloat(init_reward_7)*100+parseFloat(init_reward_8)*100;
		d = d/100;
	}
	if((d.toFixed(4))>1){
		alert('佣金总和不能超过1!');
		return false;
	}
	var shareholder_all = document.getElementById("shareholder_all").value;
	if(isNaN(shareholder_all)){
		alert('股东总比例必须为数字');
		return false;
	}

	/*-----------824新增需求：礼包店铺奖励比例设置可自己编辑，默认为商城公共的店铺奖励比例 start-------*/
	var is_shareholders = document.getElementById("is_shareholders").value;
	if(is_shareholders==1){
		var shareholder_reward_a = document.getElementById("shareholder_reward_a").value;
		var shareholder_reward_b = document.getElementById("shareholder_reward_b").value;
		var shareholder_reward_c = document.getElementById("shareholder_reward_c").value;
		var shareholder_reward_d = document.getElementById("shareholder_reward_d").value;
		if(isNaN(shareholder_reward_a) || isNaN(shareholder_reward_b) || isNaN(shareholder_reward_c) || isNaN(shareholder_reward_d)){
			alert('店铺等级比例必须为数字');
			return false;
		}
		if( (0 > parseFloat(shareholder_reward_a)) || (0 > parseFloat(shareholder_reward_b)) || (0 > parseFloat(shareholder_reward_c)) || (0 > parseFloat(shareholder_reward_d))){
			alert('店铺等级比例必须在0-1之间');
			return false;
		}
		if( (1 < parseFloat(shareholder_reward_a)) || (1 < parseFloat(shareholder_reward_b)) || (1 < parseFloat(shareholder_reward_c)) || (1 < parseFloat(shareholder_reward_d))){
			alert('店铺等级比例必须在0-1之间');
			return false;
		}
		if(shareholder_reward_a>shareholder_reward_b && shareholder_reward_b>shareholder_reward_c && shareholder_reward_c>shareholder_reward_d){
	
		}else if(shareholder_reward_a>shareholder_reward_b && shareholder_reward_b>shareholder_reward_c && shareholder_reward_c==shareholder_reward_d){
			if(shareholder_reward_c==0 && shareholder_reward_d==0){	
			}else{
				alert('各级奖励比例必须逐级递减！');		
				return false;
			}
		}else if(shareholder_reward_a>shareholder_reward_b && shareholder_reward_b==shareholder_reward_c && shareholder_reward_c==shareholder_reward_d){
			if(shareholder_reward_b==0&&shareholder_reward_c==0&&shareholder_reward_d==0){}else{
				alert('各级奖励比例必须逐级递减！');		
				return false;
			}
		}else if(shareholder_reward_a==shareholder_reward_b && shareholder_reward_b==shareholder_reward_c && shareholder_reward_c==shareholder_reward_d){
			if(shareholder_reward_a==0&&shareholder_reward_b==0&&shareholder_reward_c==0&&shareholder_reward_d==0){}else{
				alert('各级奖励比例必须逐级递减！');		
				return false;
			}
		}else{
			alert('各级奖励比例必须逐级递减！');		
			return false;
		}
	 
		// var shareholder_reward_all = parseFloat(shareholder_reward_a) + parseFloat(shareholder_reward_b)+parseFloat(shareholder_reward_c)+parseFloat(shareholder_reward_d);
		// if(shareholder_reward_all>1){
		// 	alert('店铺等级比例总和不能超过1!');
		// 	return false;
		// }
	}
	
	/*-----------824新增需求：礼包店铺奖励比例设置可自己编辑，默认为商城公共的店铺奖励比例 end-------*/
	var team_all = document.getElementById("team_all").value;
	if(isNaN(team_all)){
		alert('团队总比例必须为数字');
		return false;
	}
	var ball_all = document.getElementById("ball_all").value;
	if(isNaN(ball_all)){
		alert('绩效奖励总比例必须为数字');
		return false;
	}
	if(dh_isopen_proxy==1){
        var ordering_proportion_all = document.getElementById("ordering_proportion_all").value;
        if(isNaN(ordering_proportion_all)){
            alert('订货商奖励总比例必须为数字');
            return false;
        }
    }

	var propotion = parseFloat(team_all)+parseFloat(shareholder_all)+parseFloat(ball_all)+parseFloat(ordering_proportion_all);
	if( propotion > 1 ){
		alert('店铺、团队、绩效、订货商奖励总比例之和不能超过1!');
		return false;
	}

	/*
	 var p_id = 0;
	 if ($("#package_id").val()>0){
	 p_id = $("#package_id").val();
	 }
	$.ajax({
		url: 'ajax_cheakpackages.php',
		data:{obj:"cheakname",package_id:p_id,package_name:name,package_type:package_type,},
		dataType: 'json',
		type: 'post',
		success:function(data){
			if(data.status == 1){

			}else if(data.status == -1){
				alert(data.msg);
				return false;
			}
		}
	});
	*/
	$("#btnSave").attr("disabled", true);
	document.getElementById("upform").submit();


}

//订货商高级奖励横纵判定
function check_ordering_reward(){
	var deep = $("input[name='ordering_deep']").val();
	var len = $("input[name='ordering_grade_len']").val();
    var deep_reward_name = '';
    var grade_reward_name = '';
    for(var i=0;i<len;i++){
        var add_deep = 0;
        for(var j=1;j<=deep;j++){
            deep_reward_name = 'ordering_reward_'+i+'_'+j;
            add_deep+= Number($("input[name="+deep_reward_name+"]").val());
        }
        if(add_deep>1){
        	alert('横纵之和不能大于1');
        	return 0;
		}
    }
    for(var j=1;j<=deep;j++){
        var add_grade = 0;
        for(i=0;i<len;i++){
            grade_reward_name = 'ordering_reward_'+i+'_'+j;
            add_grade+= Number($("input[name="+grade_reward_name+"]").val());
        }
        if(add_grade>1){
            alert('横纵之和不能大于1');
            return 0;
        }
    }
}

function changeReward(o){
   if(o.checked){
      document.getElementById("is_reward").value=1;
	  document.getElementById("is_three").value=0;
	  $("#chk_isthree").removeAttr("checked");
	  $(".reward_div1").show();
	  $(".reward_div2").show();
	  $(".three_table").hide();
   }else{
      document.getElementById("is_reward").value=0;
	  $(".reward_div1").hide();
	  $(".reward_div2").hide();
   }
}

//订货商深度变化
function ordering_deep_level(obj){
    obj.value = obj.value.replace(/[^1-8]{1}/g,""); //清除"数字"和"."以外的字符
    var new_deep = obj.value;
    if(new_deep > 0){
        change_ordering_reward(new_deep);
    }
}

function changeShareholder(o){
   if(o.checked){
      document.getElementById("is_shareholders").value=1;      
	  $(".shareholder_div").show();
	  $(".shareholder_div1").show();
   }else{
      document.getElementById("is_shareholders").value=0;
	  $(".shareholder_div").hide();
	  $(".shareholder_div1").hide();
	  $("#shareholder_all").val(0);
	  document.getElementById("shareholder_reward_a").value = 0;
	  document.getElementById("shareholder_reward_b").value = 0;
	  document.getElementById("shareholder_reward_c").value = 0;
	  document.getElementById("shareholder_reward_d").value = 0;
   }
}

function changeTeam(o){
   if(o.checked){
      document.getElementById("is_team").value=1;
	  $(".team_div").show();
   }else{
      document.getElementById("is_team").value=0;
	  $(".team_div").hide();
	  $("#team_all").val(0);
   }
}
function changeBall(o){
   if(o.checked){
      document.getElementById("is_ball").value=1;
	  $(".ball_div").show();
   }else{
      document.getElementById("is_ball").value=0;
	  $(".ball_div").hide();
	  $("#ball_all").val(0);
   }
}

function changeCurrency(o){
   if(o.checked){
      document.getElementById("is_currency").value=1;
	  $(".currency_div").show();
   }else{
      document.getElementById("is_currency").value=0;
	  $(".currency_div").hide();
	  $("#currency_all").val(0);
   }
}

function changeRhree(o){
   if(o.checked){
      document.getElementById("is_three").value=1;
	  document.getElementById("is_reward").value=0;
	  $("#chk_isout").removeAttr("checked");
	  $(".reward_div2").hide();
	  $(".reward_div1").show();
	  $(".three_table").show();
   }else{
      document.getElementById("is_three").value=0;
	  $(".three_table").hide();
	  $(".reward_div1").hide();
   }
}

function changeOrdering(o){
    if(o.checked){
        document.getElementById("is_reward_ordering").value=1;
        $(".ordering_reward_div2").hide();
        $(".ordering_reward_div1").show();
        $(".ordering_three_table").show();
        $(".ordering_proportion_div").show();
        $("#recharge_change_radio").show();
    }else{
        document.getElementById("is_reward_ordering").value=0;
        $(".ordering_three_table").hide();
        $(".ordering_reward_div1").hide();
        $(".ordering_proportion_div").hide();
    }
}
function change_ordering_reward(deep=null){
	if(deep){
        url='get_ordering.php?callback=jsonpCallback_getOrdering_reward&deep='+deep+'&package_value=4&customer_id='+customer_id;
        // dh_reward_info = null;
	}else{
        url='get_ordering.php?callback=jsonpCallback_getOrdering_reward&package_value=4&customer_id='+customer_id;
	}
    $.jsonp({
        url:url,
        callbackParameter: 'jsonpCallback_getOrdering_reward'
    });
}

function changeOut(o){
   if(o.checked){
      document.getElementById("isout").value=1;
   }else{
      document.getElementById("isout").value=0;
   }
}
function changePanicBuy(o){
   if(o.checked){
      document.getElementById("isPanicBuy").value=1;
	  $(".PanicBuy_time").show();
   }else{
      document.getElementById("isPanicBuy").value=0;
	  $(".PanicBuy_time").hide();
   }
}

function get_package_type_1(){
	/* if( package_value == 3 ){
		url='get_area_list.php?callback=jsonpCallback_getcolumnlst&package_value='+package_value+'&customer_id='+customer_id;
		$.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_getcolumnlst'
		});
	}else{
		$("#area_select").hide();
	} */
	/*if( package_value == 3 ){
		url='get_commisions.php?callback=jsonpCallback_getcommisions&package_value='+package_value+'&customer_id='+customer_id;
		$.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_getcommisions'
		});
	}else{
		$("#three_select").hide();
	}
	if( package_value == 2 ){
		url='get_ShareholderBonus.php?callback=jsonpCallback_getShareholderBonus&package_value='+package_value+'&customer_id='+customer_id;
		$.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_getShareholderBonus'
		});
	}else{
		$("#shareholderBonus_select").hide();
	}
	if( package_value == 1 ){
		url='get_commisions.php?callback=jsonpCallback_getShopCommisions&package_value='+package_value+'&customer_id='+customer_id;
		$.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_getShopCommisions'
		});
	}else{
		$("#three_select").hide();
	}*/
	if( $('[id="is_promoter"]:checked').val() == 1 ){
		url='get_commisions.php?callback=jsonpCallback_getShopCommisions&package_value=1&customer_id='+customer_id;
		$.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_getShopCommisions'
		});
	}else{
		$("#three_select").hide();
	}
	
}
function get_package_type_2(){
	if( $('[id="is_shareholder"]:checked').val() == 2 ){
		url='get_ShareholderBonus.php?callback=jsonpCallback_getShareholderBonus&package_value=2&customer_id='+customer_id;
		$.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_getShareholderBonus'
		});
	}else{
		$("#shareholderBonus_select").hide();
	}
}

function get_package_type_4(){
    if( $('[id="is_check_ordering"]:checked').val() == 4 ){
        url='get_ordering.php?callback=jsonpCallback_getOrdering&package_value=4&customer_id='+customer_id;
        $.jsonp({
            url:url,
            callbackParameter: 'jsonpCallback_getOrdering'
        });
        $(".dh_payment_hide").show();
        if($("#dh_payment_checked").attr("checked")=="checked"){
            $("#ordering_recharge").show();
            $("#recharge_change_radio").show();
            $("#rechargeable").attr("checked",true);
            changeIsPanicBuy();
        }
        $("#dh_reward_hide").show();
        if($("#chk_ordering").attr("checked")=="checked"){
            $(".ordering_three_table").show();
			$(".ordering_proportion_div").show();
            $(".ordering_reward_div1").show();
        }
    }else{
        $("#ordering_select").hide();
        $(".dh_payment_hide").hide();
        $("#ordering_recharge").hide();
        $("#dh_reward_hide").hide();
        $(".ordering_three_table").hide();
        $(".ordering_proportion_div").hide();
        $(".ordering_reward_div1").hide();
        if(is_card_recharge != 1){
            $("#rechargeable").attr("checked",false);
        }
        $("#dh_payment_checked").attr("checked",false);
        changeIsPanicBuy();
    }
}
//推广员礼包
function jsonpCallback_getShopCommisions(results){
	// console.log(results)
	var len = results.length;
	$("#three_select").show();
	var strs = "<option value=-1 >--请选择--</option>" ;
	if(len>0){
		for(var i=0;i<len;i++){
			var obj_id_t = results[i].obj_id;
			var obj_level = results[i].level;
			var exp_name = results[i].exp_name;
			// console.log(obj_level)
			strs = strs+"<option value="+obj_level+" >"+exp_name+"</option>";
		}
	}
	//parentTypesSelect(<?php echo $parent_class; ?>);
	$("#three_select").html(strs);
	$("#three_select option").each(function(){
		if($(this).val() == promoter_level){
			// console.log(promoter_level);
			$(this).attr("selected", true);
		}
	});

}

function jsonpCallback_getcommisions(results){
	var len = results.length;
	$("#three_select").show();
	var strs = "<option value=-1 >--请选择--</option>" ;
	var level_arry = new Array("","普通(默认)","黑铁","青铜","黄铜","白银","黄金","白金","钻石");
	if(len>0){
		for(var i=0;i<len;i++){
			var obj_id_t = results[i].obj_id;
			var obj_level = results[i].level;
			strs = strs+"<option value="+obj_level+" >"+level_arry[obj_level]+"</option>";
		} 
	}
	//parentTypesSelect(<?php echo $parent_class; ?>);
	$("#three_select").html(strs);
	$("#three_select option").each(function(){
		if($(this).val() == three_level){
			$(this).attr("selected", true);
		}
	});

}
//店铺奖励礼包
function jsonpCallback_getShareholderBonus(results){
	$("#shareholderBonus_select").show();
	var d_name = results[0].d_name;
	var c_name = results[0].c_name;
	var b_name = results[0].b_name;
	var a_name = results[0].a_name;
	var strs = "<option value=-1 >--请选择--</option>";
	strs = strs+"<option value=4 >"+a_name+"</option>";
	strs = strs+"<option value=3 >"+b_name+"</option>";
	strs = strs+"<option value=2 >"+c_name+"</option>";
	strs = strs+"<option value=1 >"+d_name+"</option>";
	$("#shareholderBonus_select").html(strs);
	$("#shareholderBonus_select option").each(function(){
		if($(this).val() == shareholder_level){
			$(this).attr("selected", true);
		}
	});
}
//订货商奖励礼包
function jsonpCallback_getOrdering(results){
    console.log(results);
    var len = results.length;
    $("#ordering_select").show();
    var strs = "<option value=-1 >--请选择--</option>" ;
    if(len>0){
        for(var i=0;i<len;i++){
            var obj_id_t = results[i].obj_id;
            var obj_id = results[i].obj_id;
            var proxy_level_name = results[i].proxy_level_name;
            console.log(obj_id)
            strs = strs+"<option value="+obj_id+" >"+proxy_level_name+"</option>";
        }
    }
    //parentTypesSelect(<?php echo $parent_class; ?>);
    $("#ordering_select").html(strs);
    $("#ordering_select option").each(function(){
        if($(this).val() == dh_proxy_grade){
            $(this).attr("selected", true);
        }
    });
}
//订货商奖励列表
function jsonpCallback_getOrdering_reward(results){
	console.log(results);
	if(dh_reward_info==null||dh_reward_info==0){
        var len = results.length;
        if(results[1].deep==''){
            var deep = 8;
        }else{
            var deep = results[1].deep;
        }
	}else if(dh_reward_info!=null){
        var len = getJsonLength(dh_reward_info);
        if(results.length > len){
            len = results.length;
        }
        var grade_arr = changeJsonLength(dh_reward_info);
        if(results[1].deep==''){
            var deep = getJsonLength(dh_reward_info[grade_arr[1]]);
            $("input[name='ordering_reward_level']").val(deep);
        }else{
        	var deep = results[1].deep;
		}
	}
    var html = '';
    if(len>0){
        html ='<table width="80%" class="WSY_table" id="WSY_t1">';
        html+='<thead class="WSY_table_header">';
        html+='<th nowrap="nowrap">等级</th>';
        for(var d=1;d<=deep;d++){
        	if(d==1)	html+='<th nowrap="nowrap">深度一</th>';
            if(d==2)	html+='<th nowrap="nowrap">深度二</th>';
            if(d==3)	html+='<th nowrap="nowrap">深度三</th>';
            if(d==4)	html+='<th nowrap="nowrap">深度四</th>';
            if(d==5)	html+='<th nowrap="nowrap">深度五</th>';
            if(d==6)	html+='<th nowrap="nowrap">深度六</th>';
            if(d==7)	html+='<th nowrap="nowrap">深度七</th>';
            if(d==8)	html+='<th nowrap="nowrap">深度八</th>';
		}
        html+='</thead>';
        html+= '<input type=hidden name="ordering_deep" value="'+deep+'"/>';
        html+= '<input type=hidden name="ordering_grade_len" value="'+len+'"/>';
        // console.log(results);return false;
        for(var i=0;i<len;i++){
            var obj_id = results[i].obj_id;
            var proxy_level_name = results[i].proxy_level_name;
        	html+= '<tr>';
            html+= '<input type=hidden name="ordering_reward_len[]" value="'+obj_id+'"/>';
            html+= '<td>'+proxy_level_name+'</td>';
            for(var j=1;j<=deep;j++){
                html+= '<td>';
                if(dh_reward_info==null||dh_reward_info==0){
                    html+= '<input type="text"  name="ordering_reward_'+i+'_'+j+'" id="ordering_reward_'+i+'" value="0.00" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)"/>';
                }else{
                	if(dh_reward_info[grade_arr[i]]){
                        if(j==1) var reward_info = dh_reward_info[grade_arr[i]].deep_1;
                        if(j==2) var reward_info = dh_reward_info[grade_arr[i]].deep_2;
                        if(j==3) var reward_info = dh_reward_info[grade_arr[i]].deep_3;
                        if(j==4) var reward_info = dh_reward_info[grade_arr[i]].deep_4;
                        if(j==5) var reward_info = dh_reward_info[grade_arr[i]].deep_5;
                        if(j==6) var reward_info = dh_reward_info[grade_arr[i]].deep_6;
                        if(j==7) var reward_info = dh_reward_info[grade_arr[i]].deep_7;
                        if(j==8) var reward_info = dh_reward_info[grade_arr[i]].deep_8;
					}else{
                        reward_info = null;
					}
                    if(reward_info==null){
                        html+= '<input type="text"  name="ordering_reward_'+i+'_'+j+'" id="ordering_reward_'+i+'_'+j+'" value="0.00" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)"/>';
                    }else{
                        html+= '<input type="text"  name="ordering_reward_'+i+'_'+j+'" id="ordering_reward_'+i+'_'+j+'" value="'+reward_info+'" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)"/>';
                    }
                }
                html+= '</td>';
			}
            html+= '</tr>';
        }
        html+='</table>';
	$(".ordering_three_table").html(html);
    }
}
//判断json数组长度
function getJsonLength(json){
    var jsonLength=0;
    for (var i in json) {
        jsonLength++;
    }
    return jsonLength;
}

//转化json数组
function changeJsonLength(json){
    var jsonChange = new Array();
    var len = 0;
    for (var i in json) {
        jsonChange[len]=i;
        len++;
    }
    return jsonChange;
}
// 
function jsonpCallback_getcolumnlst(results){
	var len = results.length;
	$("#area_select").show();
	var strs = "<option value=-1 >--请选择--</option>"  
	if(len>0){
		for(var i=0;i<len;i++){
			var obj_id_t = results[i].obj_id;
			var obj_title = results[i].obj_title;
			strs = strs+"<option value="+obj_id_t+" >"+obj_title+"</option>";
		} 
	}
	//parentTypesSelect(<?php echo $parent_class; ?>);
	$("#area_select").html(strs);
	$("#area_select option").each(function(){
		if($(this).val() == area_id){
			$(this).attr("selected", true);
		}
	});

}

function get_three_id(three_value){
	document.getElementById("promoter_level").value=three_value;
}
function get_dh_proxy_grade(ordering_value){
    document.getElementById("dh_proxy_grade").value=ordering_value;
}
function get_area_id(area_value){
	document.getElementById("area_id").value=area_value;
}
function get_shareholder_level(shareholder_level_value){
	document.getElementById("shareholder_level").value=shareholder_level_value;
}
