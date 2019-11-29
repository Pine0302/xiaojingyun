function submitV(a){
	document.getElementById("upform").submit();
}
function change_pro_card_level(obj){
	$("#pro_card_level").val(obj);
}
function change_is_godefault(obj){
	$("#is_godefault").val(obj);
}
function change_need_customermessage(obj){
	$("#need_customermessage").val(obj);
}
function change_isprint(obj){
	$("#isprint").val(obj);
}
function change_both_currency_coupon(obj){
	$("#is_ban_use_coupon_currency").val(obj);
}
function change_is_identity(obj){
	$("#is_identity").val(obj);
	if(obj==0){
		$('.Identity_annex .WSY_bot').click();
	}
}
function change_is_uploadidentity(obj){
	$("#is_uploadidentity").val(obj);
	if(obj==1){
		$('.Identity .WSY_bot2').click();
	}
}
function change_is_cost_limit(obj){
	$("#is_cost_limit").val(obj);
}
function change_is_weight_limit(obj){
	$("#is_weight_limit").val(obj);
}
function change_is_number_limit(obj){
	$("#is_number_limit").val(obj);
}
function change_is_orderActivist(obj){
	$("#is_orderActivist").val(obj);
}

