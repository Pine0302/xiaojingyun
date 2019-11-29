function submitV(a){
	var val_img = $('.filenames').val();
	var cal_data = $('.filenames').attr('cal-data');
	console.log(val_img);
	if (cal_data == '1') {
		if (val_img == '') {
	  		layer.alert('自定义图标必须上传');
	  		return false;
  		}
	}
  	
	document.getElementById("upform").submit();
}
function change_showshare_info(obj){
	$("#is_showshare_info").val(obj);
}
function change_photo_div(obj){
	switch(obj){
		case 0:
		  $('.filenames').attr('cal-data','0'); 
		  $('.filenames').val('');
		  $(".WSY_memberimg").css("display","none");
		  $("#now_define_share_image").val("");
		  break;
		case 1:
		  $('.filenames').attr('cal-data','1');
		  var img_url = $('#upfile1').val();
		  $('.filenames').val(img_url);
		  $(".WSY_memberimg").css("display","block");
		  break; 
	}
}