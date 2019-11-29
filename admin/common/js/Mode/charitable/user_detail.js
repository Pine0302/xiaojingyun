function search_condition(url){
	var search_batchcode = document.getElementById("search_batchcode").value;
	var begintime        = document.getElementById("begintime").value;
    var endtime          = document.getElementById("endtime").value;
	if(search_batchcode !=""){
		url = url+"&search_batchcode="+search_batchcode;
	}
	if(begintime !=""){
		url = url+"&begintime="+begintime;
	}
	if(endtime !=""){
		url = url+"&endtime="+endtime;
	}
	document.location = url+"&customer_id="+customer_id+"&user_id="+user_id;
}

function searchForm(){
	var url ="user_detail.php?";
	search_condition(url); 
}

 $(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
		var url ="user_detail.php?&pagenum="+p;
		search_condition(url); 
	}
});

function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>count) || isNaN(a)){
		return false;
	}else{
		var url ="user_detail.php?pagenum="+a;
		search_condition(url); 
	}
}