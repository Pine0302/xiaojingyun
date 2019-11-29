function submitV(a){
	var custom_name = $("#custom_name").val();
	if( custom_name == ""){
		alert("购物币自定义名不能为空！");
		return;
	}
	document.getElementById("upform").submit();
}
function change_isshop(obj){
	$("#isshop").val(obj);//到店支付
}
function change_isdelivery(obj){
	$("#isdelivery").val(obj);//货到付款
}
function change_iscard(obj){
	$("#iscard").val(obj);//会员卡余额支付
}
function change_is_payother(obj){
	$("#is_payother").val(obj);//找人代付
}
function change_weipay(obj){
	$("#is_weipay").val(obj);//微信支付
}
function change_is_payChange(obj){
	$("#is_payChange").val(obj);//零钱支付
}
function change_is_pay(obj){
	$("#is_pay").val(obj);//零钱支付
}
function change_alipay(obj){
	$("#is_alipay").val(obj);//支付宝
}
function change_tenpay(obj){
	$("#is_tenpay").val(obj);//财务通
}
function change_allinpay(obj){
	$("#is_allinpay").val(obj);//通联支付
}
function change_paypal(obj){
	$("#is_paypal").val(obj);//PayPal支付
}
function change_yeepay(obj){
	$("#is_yeepay").val(obj);//yeepay支付
}
function change_jdpay(obj){
	$("#is_jdpay").val(obj);//jdpay支付
}
function change_unionpay(obj){
	$("#is_unionpay").val(obj);//高汇通支付
}
function change_is_currency(obj){
	$("#is_currency").val(obj);//购物币支付
	// if(obj==0){
	// 	$("#set_currency").hide();
	// }else{
	// 	$("#set_currency").show();
	// }
}
function change_currency(obj){
	$("#currency").val(obj);//购物币是否参与分佣
}
function change_currency_given(obj){
	$("#currency_given").val(obj);//购物币是否参与分佣
}
function change_currency_open(obj){
	$("#is_currency").val(obj);//购物币是否开启抵扣
}

function change_isdelivery(obj){
	$("#isdelivery").val(obj);//购物币是否参与分佣
}