function submitV(a){
	var  mail=$("#mail").val();
	if(mail.replace(/(^\s*)|(\s*$)/g,'') == ""){
		alert('请输入邮箱');
		return;
	}
	var mailreg=/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if(!mailreg.test(mail)){
		alert('邮箱错误');
		return;
	}
	document.getElementById("upform").submit();
}

