
$(function(){

/*显示上传图片-----start*/
	listenMouse();//监听鼠标移动
	relative();//定位信息框位置
})
/*显示上传图片-----end*/
function change_is_certificate(obj){//授权书开关
	$("#is_certificate").val(obj);
}


//点击事件-----start
$('#shape div').click(function(){//点击样式字形
	if($(this).hasClass('select')){//是否已被选中
		$(this).removeClass('select');
	}else{
		$(this).addClass('select');
	}
	var shape = $(this).attr('id');
	var _val = mess_id;//获取当前选择加载项的值
	if(shape=="bold"){
		if($(this).hasClass('select')){
			$('.message'+_val).css('font-weight',shape);//改变文字形态
			$('.message'+_val).attr('font-weight',shape);//记录形态
		}else{
			$('.message'+_val).css('font-weight','normal');//改变文字形态
			$('.message'+_val).attr('font-weight','normal');//记录形态
		}
	}
	if(shape=="italic"){
		if($(this).hasClass('select')){
			$('.message'+_val).css('font-style',shape);//改变文字形态
			$('.message'+_val).attr('font-style',shape);//记录形态
		}else{
			$('.message'+_val).css('font-style','normal');//改变文字形态
			$('.message'+_val).attr('font-style','normal');//记录形态
		}	
	}
	
});

//文本框选择项和属性
$('#information').change(function(){//选择加载项
	select_information();//显示当前信息参数数据
});

$('#font').change(function(){//选择字体
	var font = $('#font').val();//获取当前选择的字体
	if(font<0){
		return;
	}
	var _val = mess_id;//获取当前选择加载项的值
	$('.message'+_val).find('span').css('font-family',font);
	$('.message'+_val).attr('font',font);
});

$('#size').change(function(){//选择字号
	var size = $('#size').val();//获取当前选择的字号
	if(size<0){
		return;
	}
	var _val = mess_id;//获取当前选择加载项的值
	$('.message'+_val).find('span').css('font-size',size+'px');//改变文字字号
	$('.message'+_val).attr('size',size);//记录字号
	var word_width = $('.message'+_val).find('span').width();
	$('.message'+_val).find('.message_close').css('margin-left',word_width+'px');
	$('.message'+_val).css('width',size+word_width+'px');
});

$('#spacing').change(function(){//选择间距
	var spacing = $('#spacing').val();//获取当前选择的间距
	if(size<0){
		return;
	}
	var _val = mess_id;//获取当前选择加载项的值
	$('.message'+_val).find('span').css('letter-spacing',spacing+'px');//改变文字间距
	var word_length = $('.message'+_val).find('span').text().length;//文字长度
	$('.message'+_val).attr('spacing',spacing);//记录间距
	var word_width = $('.message'+_val).find('span').width();
	$('.message'+_val).find('.message_close').css('margin-left',word_width+'px');
	$('.message'+_val).css('width',word_length*spacing+word_width+'px');
});

//重置显示内容
$('#reset_messages').click(function(){
	$(".message_single").remove();
	$('#show_picture').attr('src','');
	$("#picture_url").val('');
	$("#picture_id").val('-1');
});

function closeMess(_val){//关闭当前文本框
	$('.message'+_val).remove();
	del_num++;
}

$('#add_messages').click(function(){//点击添加显示内容
	var _val = $('#information').val();//获取选择的值
	select_message(_val);//新建信息框函数
	listenMouse();//监听鼠标移动
});
//点击事件-----end





//底图操作函数---start
 $("#uploadForm").submit(function(e){//上传底片		
	e.preventDefault();
	 var formData = new FormData();		 
	 formData.append("file_button", "submit"); 
	 var formData = new FormData(document.getElementById("uploadForm"));//获取文件file数据
	 $.ajax({  
		  url: 'uploadify.php' ,  
		  type: 'POST',  
		  data: formData,  
		  async: false,  
		  cache: false,  
		  contentType: false,  
		  processData: false,  
		  success: function (returndata) { 
		  switch(returndata){
			  case '10001':
					alert('不能上传此类型文件！');
			  break;
			  case '10002':
					alert('同名文件已经存在了！');
			  break;
			  case '10003':
					alert('移动文件出错！');
			  break;
			  case '10004':
					alert('文件太大！');
			  break;
			  case '10005':
					alert('请选择文件！');
			  break;
			  default:
					del_num = del_num + $('.message_single').length;
					$("#picture_url").val(returndata);
					$("#show_picture").attr("src",returndata);
					$("#picture_id").val('-1');
					
			break;
		  }	 
			return;
		  },  
		  error: function (returndata) {  
			  alert('上传出错');  
		  }  
	 });  
	return;
});
//底图操作函数---end






//公用函数


//信息框数据-----start
function relative(){
	//获取图片的坐标
	var picture_left = document.getElementById('show_picture').offsetLeft;
	var picture_top = document.getElementById('show_picture').offsetTop;
	//遍历元素
	$('.message_single').each(function(){
		//获取数据
		var id            = $(this).data('id');
		var obj_id        = $(this).attr('id');
		var size          = $(this).attr('size');
		var obj_word_id   = 'message_single_word'+id;
		var relative_top  = $(this).attr('top');
		var relative_left = $(this).attr('left');
		var left = picture_left + parseInt(relative_left) - document.getElementById(obj_word_id).offsetLeft;		
		var top = picture_top + parseInt(relative_top) - document.getElementById(obj_word_id).offsetTop - parseInt(size);
		$(this).css('left',left+'px');
		$(this).css('top',top+'px');
	});
	//信息框数据-----end
}

function select_information(v){//显示具体参数数据
	if(v>-1){
		var _val = v;
	}else{
		var _val = $('#information').val();//获取当前选择加载项的值
	}
	//读取该选项的数据
	var font        = $('.message'+_val).attr('font');
	var size        = $('.message'+_val).attr('size');
	var spacing	    = $('.message'+_val).attr('spacing');
	var location    = $('.message'+_val).attr('location');
	var font_weight = $('.message'+_val).attr('font-weight');
	var font_style  = $('.message'+_val).attr('font-style');
	var ctype       = $('.message'+_val).attr('ctype');
	//赋予各个状态,为空则默认值
	if(v>-1){
		if(ctype>-1){
			$('#information').val(ctype);
		}
	}else{
			$('#information').val(_val);
	}
	
	if(font=="" || font==undefined){
		$("#font").val('Microsoft YaHei');
	}else{
		$("#font").val(font);
	}

	if(size=="" || size==undefined){
		$("#size").val(12);
	}else{
		$("#size").val(size);
	}
	if(spacing=="" || spacing==undefined){
		$("#spacing").val(0);
	}else{
		$("#spacing").val(spacing);
	}
	$('.in_format div').removeClass('select');
	if(location=="" || location==undefined){
		$('#left').addClass('select');
	}else{
		$('#'+location).addClass('select');
	}	
	if(font_weight=="bold"){
		$('#bold').addClass('select');
	}
	if(font_style=="italic"){
		$('#italic').addClass('select');
	}
	var word_width = $('.message'+_val).find('span').width();
	$('.message'+_val).find('.message_close').css('margin-left',word_width+'px');
}

function select_message(_val){//显示信息框
	var word = "";
	var i = $('.message_single').length+del_num;
	i++;
	switch(_val){//判断不同的值给予不同的文字
		case '0':
			word = '真实名字';
		break;
		case '1':
			word = '微信名字';
		break;
		case '2':
			word = '推广员编号';
		break;
		case '3':
			word = '推广员等级';
		break;
		case '4':
			word = '成为推广员时间';
		break;
		default:
			return;
		break;
	}
	
	/*加载数据*/
	var html = "";
	html += '<div class="message_single message'+i+'" id="message'+i+'" ctype='+_val+' data-id='+i+' location="left" font-weight="normal" size="12" font="Microsoft YaHei" spacing="0" font-style="normal">';
	//html += '	<img class="message_single_img"  src="images/text.png">';
	html += '	<img class="message_close" src="images/close.png" onclick="closeMess('+i+')">';
	html += '	<div class="message_single_word" id="message_single_word'+i+'" class="text">';
	html += '		 <p><span>'+word+'</span></p>';
	html += '	</div>';
	html += '</div>';
	$('.show_picture').append(html);
	var content = "";
	content = "<option value='"+i+"' style='display:none;'>"+word+"</option>";
	$('#information').append(content);
	var content2 = "<input type=hidden name='message"+i+"' id='mess"+i+"' value='' />";
	$('#upform').append(content2);
}
function listenMouse(){
	//监听拖动信息框-----start
	$(".message_single").mousedown(function(e) {//e鼠标事件
		var id = $(this).data('id');
		mess_id = id;
		select_information(id);
		var obj = '.message'+id;
		$(this).css("cursor","move");//改变鼠标指针的形状 
		$(".message_single_word").find('span').removeClass("messBorder");
		$(this).find('span').addClass("messBorder");
		var offset = $(this).offset();//DIV在页面的位置 
		var x = e.pageX - offset.left;//获得鼠标指针离DIV元素左边界的距离 
		var y = e.pageY - offset.top;//获得鼠标指针离DIV元素上边界的距离 
		
		$(document).bind("mousemove",function(ev){//绑定鼠标的移动事件，因为光标在DIV元素外面也要有效果，所以要用doucment的事件，而不用DIV元素的事件 	 
			$(".message_single").stop();//加上这个之后 

			var _x = ev.pageX - x;//获得X轴方向移动的值 
			var _y = ev.pageY - y;//获得Y轴方向移动的值 

			$(obj).animate({left:_x+"px",top:_y+"px"},10);	 
		}); 
	}); 

	$(document).mouseup(function() { 
		$(".message_single").css("cursor","default"); 
		$(this).unbind("mousemove");		
	});
//拖动信息框-----end
}
//赋值给各个信息框元素
function assignment(_val,size,font,spacing,weight,style,page_x,page_y){
		//赋值给各个信息框元素
		$('.message'+_val).attr('font',font);
		$('.message'+_val).attr('size',size);
		$('.message'+_val).attr('spacing',spacing);
		$('.message'+_val).attr('location');
		weight = (weight==1)?"bold":"normal";
		$('.message'+_val).attr('font-weight',weight);
		style = (style==1)?"italic":"normal";
		$('.message'+_val).attr('font-style',style);
		$('.message'+_val).attr('top',page_y);
		$('.message'+_val).attr('left',page_x);
		//改变信息框各个元素样式
		$('.message'+_val).find('span').css('font-family',font);
		$('.message'+_val).find('span').css('font-size',size+'px');//改变文字字号
		$('.message'+_val).css('letter-spacing',spacing+'px');//改变文字间距
		$('.message'+_val).css('font-weight',weight);//改变文字形态
		$('.message'+_val).css('font-style',style);//改变文字形态		
}