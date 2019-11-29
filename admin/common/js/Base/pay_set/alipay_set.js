function submitV(a){
	var  pid=$("#pid").val();
	var  akey=$("#akey").val();
	var  is_pid=pid.indexOf('*');
	var  is_akey=akey.indexOf('*');
	if(is_pid>0){
		alert("请重新输入支付宝PID");
		return;
	}
	if(is_akey>0){
		alert("请重新输入支付宝KEY");
		return;
	}	
	document.getElementById("upform").submit();
}
