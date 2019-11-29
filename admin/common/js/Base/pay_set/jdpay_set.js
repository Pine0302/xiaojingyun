function submitV(a){
	var jdpay_customernumber = $('#jdpay_customernumber').val();
	
	if(!jdpay_customernumber || !$.isNumeric(jdpay_customernumber)){
		alert('商户号为空');
		return false;
	}
	
	var jdpay_secret = $('#jdpay_secret').val();
	
	if(!jdpay_secret){
		alert('密钥为空');
		return false;
	}
	
	document.getElementById("upform").submit();
}

