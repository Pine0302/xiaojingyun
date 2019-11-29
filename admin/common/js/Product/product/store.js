$(function(){
	
	$("#mul_onsale").click(function(){
		var ckIds = $("input[name='pro_ids']:checked");
		if(ckIds.length == 0){
			alert("请先勾选要上架的商品！");
			return;
		}


		if(confirm("是否确定将选中的"+ckIds.length+"个商品上架？")){

			var idsStr = "";
			// var errorinfo ="";

			ckIds.each(function(i,n){
				//console.log(" i : "+i+" n.value : "+n.value);
				if(i > 0){
					idsStr += ",";
				}
				idsStr = idsStr + n.value;
			});
		var search_type_id = $("#search_type_id").val();
		var search_status = $("#search_status").val();
		var keyword = $("#keyword").val();
		var search_other_id = $("#search_other_id").val();
		var search_status = $("#search_status").val();
		var search_source = $("#search_source").val();
		var search_supply = $("#search_supply").val();
		var supply_id = $("#supply_id").val();
		var foreign_mark = $("#foreign_mark").val();
		// alert(foreign_mark);return;
		$.ajax({
			url: "store_check.php?customer_id="+customer_id_en+"&op=onsale_m",
			dataType: 'json',
			data:{
				    ids:idsStr
		        },
			type: 'post',
			success: function(r) {
				if (r.resultCode == 0) {
					var msg = r.resultMessage+r.errorMessage;
                	alert(msg);
					var url = "store.php?customer_id="+customer_id_en+"&foreign_mark="+foreign_mark+"&pagenum="+pagenum+"&search_type_id="+search_type_id
					+"&search_status="+search_status+"&keyword="+keyword+"&search_other_id="+search_other_id+"&search_status="+search_status
					+"&search_source="+search_source+"&search_supply="+search_supply+"&supply_id="+supply_id;
			     	location.href=url;
            	}
			}
			
		 });
		}
	});



	$("#ck_all").click(function(){
		$("input[name='pro_ids']").attr("checked",this.checked);
	});


	$("input[name='pro_ids']").click(function(){
		if(!this.checked){
			$("#ck_all").attr("checked",this.checked);
		}
	});


});