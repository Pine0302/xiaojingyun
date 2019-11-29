
$(function(){
	$("#ck_all").click(function(){
		$("input[name='pro_ids']").attr("checked",this.checked);
	});
	$("input[name='pro_ids']").click(function(){
		if(!this.checked){
			$("#ck_all").attr("checked",this.checked);
		}
	});
});