function submitV(a){
	var  bkey=$("#bussinesskey").val();
	var  is_bkey=bkey.indexOf('*');
	if(is_bkey>0){
		alert("请重新输入财付通密钥");
		return;
	}		
	document.getElementById("upform").submit();
}

