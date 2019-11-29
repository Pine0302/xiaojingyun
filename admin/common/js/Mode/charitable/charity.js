function search_condition(url){
	var search_id   = document.getElementById("search_id").value;
	var search_name = document.getElementById("search_name").value;
	if(search_id !=""){
		url = url+"&search_id="+search_id;
	}
	if(search_name !=""){
		url = url+"&search_name="+search_name;
	}
	document.location = url+"&customer_id="+customer_id;
}

function searchForm(){
	var url ="charity.php?";
	search_condition(url); 
}

 $(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
		var url ="charity.php?&pagenum="+p;
		search_condition(url); 
	}
});

function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>count) || isNaN(a)){
		return false;
	}else{
		var url ="charity.php?pagenum="+a;
		search_condition(url); 
	}
}