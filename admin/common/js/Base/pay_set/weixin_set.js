function submitV(a){
	var  appsecret=$("#appsecret").val();
	var  paysignkey=$("#paysignkey").val();
	var  is_app=appsecret.indexOf('*');
	var  is_pay=paysignkey.indexOf('*');
	if(is_app>0){
		alert("请重新输入AppSecret");
		return;
	}
	if(is_pay>0){
		alert("请重新输入PaySignKey");
		return;
	}	
	document.getElementById("upform").submit();
}
 function selPayType(v){
 
    switch(parseInt(v,10)){
	   case 1:
		  document.getElementById("div_refund").style.display="none";
		  document.getElementById("div_partnerkey").style.display="block";
		  
	      break;
	   case 2:
		  document.getElementById("div_refund").style.display="block";
		  document.getElementById("div_partnerkey").style.display="none";
	      break;
	}
 }
