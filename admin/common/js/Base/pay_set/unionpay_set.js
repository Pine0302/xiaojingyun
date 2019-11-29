function submitV(a){
	var  terminal_no=$("#terminal_no").val();
	var  merchant_key=$("#merchant_key").val();
	var  is_pid=terminal_no.indexOf('*');
	var  is_akey=merchant_key.indexOf('*');
	if(is_pid>0){
		alert("请重新输入终端号");
		return;
	}
	if(is_akey>0){
		alert("请重新输入商户密钥KEY");
		return;
	}	
	document.getElementById("upform").submit();
}
