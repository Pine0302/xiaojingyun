function getCode(){	
	$(".code_list").html('<i class="wx_loading_icon"></i>');
	$.ajax({ 
        type: "post",
        url: "get_code.php",
        data: { cid: cid,num: num},
        success: function (result) {
			location.href='show_code.php?customer_id='+customer_id+'&cid='+cid+'&p_id='+p_id+'&p_class='+p_class;
        }    
    })
}
