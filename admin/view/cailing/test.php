<html >
<head>
<title>添加彩铃</title>
<link rel="stylesheet" type="text/css" href="../../../../weixinpl/common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script>
<script type="text/javascript" src="/weixinpl/js/ajaxfileupload.js"></script>
<link rel="stylesheet" type="text/css" href="/mshop/web/static/css/webuploader.css" />
<script type="text/javascript" src="/mshop/web/static/js/TouchSlide.1.1.js"></script>
<script type="text/javascript" src="/mshop/web/static/js/global.js"></script>
<meta http-equiv="content-phone" content="text/html;charset=UTF-8">
<style type="text/css">
.WSY_member input[type="radio"] {
	display: inline-block !important;
	float: none !important;
}
.WSY_member dd input[type="button"] {
    display: block;
    width: 55px;
    height: 28px;
    margin-right: 0px;
    border: solid 1px #ccc;
    line-height: 0px; 
    margin-top: -1px;
    margin-left: -6px;
}

button, input {  
    margin: 0;  
    font: inherit;  
    color: inherit  
}  
  
button::-moz-focus-inner, input::-moz-focus-inner {  
    padding: 0;  
    border: 0  
}  
  
table {  
    border-spacing: 0;  
    border-collapse: collapse  
}  
  
td, th {  
    padding: 0  
}  
  
.progress {  
    height: 20px;  
    margin-bottom: 20px;  
    overflow: hidden;  
    background-color: #f5f5f5;  
    border-radius: 4px;  
    -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);  
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
    width:100px;
}  
  
.progress-bar {  
    float: left;  
    width: 0;  
    height: 100%;  
    font-size: 12px;  
    line-height: 20px;  
    color: #fff;  
    text-align: center;  
    background-color: #337ab7;  
    -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);  
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);  
    -webkit-transition: width .6s ease;  
    -o-transition: width .6s ease;  
    transition: width .6s ease  
}  
  
.progress-bar-striped, .progress-striped .progress-bar {  
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);  
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);  
    background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);  
    -webkit-background-size: 40px 40px;  
    background-size: 40px 40px  
}  
  
.progress-bar.active, .progress.active .progress-bar {  
    -webkit-animation: progress-bar-stripes 2s linear infinite;  
    -o-animation: progress-bar-stripes 2s linear infinite;  
    animation: progress-bar-stripes 2s linear infinite  
}  
  
.progress-bar-success {  
    background-color: #5cb85c  
}  
  
.progress-striped .progress-bar-success {  
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);  
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);  
    background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent)  
}  
  
.progress-bar-info {  
    background-color: #5bc0de  
}  
  
.progress-striped .progress-bar-info {  
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);  
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);  
    background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent)  
}  
  
.progress-bar-warning {  
    background-color: #f0ad4e  
}  
  
.progress-striped .progress-bar-warning {  
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);  
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);  
    background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent)  
}  
  
.progress-bar-danger {  
    background-color: #d9534f  
}  
  
.progress-striped .progress-bar-danger {  
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);  
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);  
    background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent)  
}  
</style>
</head>

<body>
<div class="div_new_content">
    <div class="WSY_content">
		<div class="WSY_columnbox WSY_list">
	
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">添加彩铃</a> 
				</div>
			</div>

			<div class="WSY_data">
					<dl class="WSY_member">					
						<div>
							<dt>彩铃名称：</dt>
							<dd class="spa">
								<input onkeyup="prices_type(this)" type=text value="" name="name" id="name" maxlength="10" placeholder="请填写彩铃名称10字以内" style="width:300px;" />
							</dd>
						
						</div>
					</dl>

					<dl class="WSY_member">					
						<div>
							<dt>彩铃标签：</dt>
							<dd class="spa">
								<input onkeyup="prices_type(this)" type=text value="" name="tip" maxlength="4" id="tip" placeholder="请填写彩铃标签4字以内" style="width:300px;" />
							</dd>
						
						</div>
					</dl>

					<dl class="WSY_member">					
						<div>
							<dt>彩铃价格：</dt>
							<dd class="spa">
								<input onkeyup="prices(this)" type=text value="" maxlength="12" placeholder="价格" name="price" id="price" style="width:300px;" />
							</dd>
						
						</div>
					</dl>

					<dl class="WSY_member">					
						<div>
							<dt>彩铃图片：</dt>
							<dd class="spa">
								<div style="width: 200px;height: 200px;border: 1px solid #ccc;" id='images'>
									<input type="hidden" name="img_url" id="img_url" value="">
								</div><br />
								<div style="margin-top: 10px;"><span style="color: #666;">图片尺寸建议：200*200PX,格式支持PNG,JPG,JPEG,GIF</span></div><br />
								<div class="uploader white" style="margin-top: 10px;overflow: initial;">
									<input type="text" class="filename" readonly="" style="width: 150px;height: 25px;" value="">
									<input type="button" name="file" class="button" value="上传...">
									<input name="upfile" id="upfile" type="file" size="30" onchange="fileSelect_banner(this)" value="">
								</div>
							</dd>

						
						</div>
					</dl>

					<dl class="WSY_member">					
						<div>
							<dt>上传音乐：</dt>
							<dd class="spa">
								<!-- <input name="music" id="music" type="file" size="30" multiple="multiple" value="">
								<span style="color: #666;">(支持格式：mp3,/wmv)</span> -->
								<div id="uploader" style="width: 800px;margin: auto">  
								    <h1 style="color: #00a7d0">大文件上传测试</h1>  
								    <div id="thelist" class="uploader-list"></div>  
								    <div class="btns">  
								        <table style="width: 300px;" border="1">  
								            <tr align="right">  
								                <td>  
								                    <div id="picker" style="float:left">选择文件</div>  
								                </td>  
								                <td>  
								                    <button id="ctlBtn" class="btn btn-default" style="padding:8px 15px;">开始上传</button>  
								                </td>  
								            </tr>  
								        </table>  
								    </div>  
								  
								</div> 
							</dd>
						
						</div>
					</dl>

					<dl class="WSY_member">					
						<div>
							<dt>彩铃排序：</dt>
							<dd class="spa">
								<input  onkeyup='this.value=this.value.replace(/\D/gi,"");' maxlength="9" type=text value="1" name="sort" id="sort" style="width:300px;margin-bottom: 10px;" /><br />
								<span style="color: #666;">排序限制：降序，数值越大排名越前，数值相同则看添加时间，时间最早，排名越前</span>
							</dd>

						
						</div>
					</dl>

					<div class="WSY_text_input01" style="margin-left: 18%;">
						<div class="WSY_text_input"><input type="button" class="WSY_button" value="保存" onclick="add_color_bell()" style="cursor:pointer;"/></div>
					</div>
			
			</div>
	
		</div>
	</div>
<div style="width:100%;height:20px;">
</div>
</div>
<script type="text/javascript" src="/mshop/web/static/js/dist/webuploader.js"></script>
<script type="text/javascript">
	var $list=$("#thelist");   //这几个初始化全局的百度文档上没说明，好蛋疼。  
    var $btn =$("#ctlBtn");   //开始上传  
	var customer_id = '<?php echo $customer_id; ?>';
	var uploader = WebUploader.create({
		// 选完文件后，是否自动上传。
    	auto: true,
		// swf文件路径
		swf: '/mshop/web/static/js/Uploader.swf',

		// 文件接收服务端。
		server: '/mshop/admin/index.php?m=test&a=handleVideo&customer_id='+customer_id,

		// 选择文件的按钮。可选。
		// 内部根据当前运行是创建，可能是input元素，也可能是flash.
		pick: '#picker',

		// 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
		resize: false,
		
		//开启分片上传
		chunked: true,
		
		//分片大小 5M
		chunkSize: 2 * 1024 * 1024,		
		
		//设定单个文件大小 100M
		fileSingleSizeLimit:100*1024*1024,
		
		//允许重复上传。可选
		duplicate :true,
		
		// 只允许选择视频文件。
		accept: {
			title: 'HTML5视频文件',
			extensions: 'mov,ogg,mp4,webm,mpeg4,mp3',
		   mimeTypes: 'mp3/*'
		}


	});


	if(WebUploader.os.ios>0){
		$("div").on("click",".webuploader-element-invisible",function(){
			$(".webuploader-element-invisible").removeAttr("capture");
		});
	}

	// 当有文件被添加进队列的时候
	uploader.on( 'fileQueued', function( file ) {
		$list.append( '<div id="' + file.id + '" class="item">' +
			'<h4 class="info">' + file.name + '</h4>' +
			'<p class="state">等待上传...</p>' +
		'</div>' );
	});
	
	// 文件上传过程中创建进度条实时显示。
	uploader.on( 'uploadProgress', function( file, percentage ) {
		var $li = $( '#'+file.id ),
			$percent = $li.find('.progress .progress-bar');

		// 避免重复创建
		if ( !$percent.length ) {
			$percent = $('<div class="progress progress-striped active">' +
			  '<div class="progress-bar" role="progressbar" style="width: 200px">' +
			  '</div>' +
			'</div>').appendTo( $li ).find('.progress-bar');
		}
		$li.find('p.state').text(Math.floor(percentage * 100) + '%');
		$percent.css( 'width', percentage * 100 + '%' );
	});
	
	uploader.on( 'uploadSuccess', function( file , response ) {
		if( response.errcode != 0 ){
			alert(response.errmsg);
			$list.html("");
			return false;
			
		}else{
			$( '#'+file.id ).find('p.state').text('已上传');
			$("#video").val(response.newfileurl);
			$list.html('<div class="videobox"><video width="100%" height="auto" poster="/mshop/web/static/images/bgposter.jpg" style="max-height: 200px;" src="'+response.newfileurl+'" controls>您的浏览器不支持 video 标签。</video><span onclick="closeVideo()" class="video-close-btn">关闭</span>');
		}
	});

	uploader.on( 'uploadError', function( file ) {
		$( '#'+file.id ).find('p.state').text('上传出错');
	});

	uploader.on( 'uploadComplete', function( file ) {
		$( '#'+file.id ).find('.progress').fadeOut();
	});
	
	 /**
     * 验证文件格式以及文件大小
     */
    uploader.on("error",function (type){
        if (type=="Q_TYPE_DENIED"){
			alert("请上传mov,mp4,flv,3gp,wmv格式文件");
            return false;
        }else if(type=="F_EXCEED_SIZE"){
			alert("文件大小不能超过100M");
            return false;
        }
    });
	
	// 改成自动上传
	// $btn.on( 'click', function() {  
	// 	console.log("上传...");  
	// 	uploader.upload();  
	// 	console.log("上传成功");  
	// }); 
	
	
	// 删除视频按钮
	function closeVideo(){
		$("#thelist").html("");
		$("#video").val("");
	}

    </script>
</script>

<script type="text/javascript">
	//添加彩铃
	function add_color_bell(){
		var name = $('#name').val();//名字
		var tip = $('#tip').val();//标签
		var img_url = $('#img_url').val();//彩铃图片
		var price = $('#price').val(); //价格
		var sort = $('#sort').val(); //排序
		var customer_id = '<?php echo $customer_id; ?>';
		console.log(sort);
		if (name == '' || tip == '' || img_url == '' || price == '' || sort == '') {
			layer.alert('任何一项都不能为空');
			return;
		}

		var url = "/mshop/admin/index?m=cailing&a=add_color_bell";
		$.ajax({
			url: url,
			dataType: 'json',
			type: 'post',
			data: {
				'name':name,
				'tip':tip,
				'img_url':img_url,
				'price':price,
				'sort':sort,
			},
			success: function(res){
				console.log(res);
				if (res.errcode == 1) {
					layer.alert('添加成功');
					setTimeout(function(){
						window.location.reload();
						//history.back(-1);
					},1000);
					
				}else{
					layer.alert('添加失败');
				}
				
			}
		});
	}


	//限制输入框输入特殊字符
	function prices_type(obj){
		var s = $(obj).val();
	    var pattern = new RegExp("[~'!>@#$%^&*<()-+_=:]");
	    var rs = ""; 
		for (var i = 0; i < s.length; i++) { 
			rs = rs+s.substr(i, 1).replace(pattern, '');
		} 
		if (rs != s) {
			$(obj).val(rs);
			$(obj).focus();
		}

	}

	//保留两位小数
	function prices(obj){
		obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
		obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字
		obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个, 清除多余的
		obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
		obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
		if (obj.value > 999999999.99){
			var num = obj.value.substr(0,obj.value.length-1);
			$(obj).val(num);
		} 
			

	}
	//图片上传
	function fileSelect_banner(evt) {
		var str = evt.value;
        var arr=str.split('\\');
		var my=arr[arr.length-1];
		if(window.File && window.FileReader && window.FileList && window.Blob) {
			currfile = evt;
			var files = evt.files; //直接传入file对象，evt.target改成evt
			var file;
			for(var i=0; i<files.length; i++) {
				if(!files[i].type.match('image.*')) {
					return;
				}
				if (files[i].size > 10000000) {
					alert('图片大小限制不超过10M');
					return;
				}
				reader = new FileReader();
				reader.onload = (function(tFile) {
					return function(evt) {
						dataURL = evt.target.result;
						// var swiper = $(".swiper-slide").length;
						// $(".swiper-wrapper").prepend('<div class="swiper-slide" id="hide'+arab+'"><div class="swip hide"><img src="/mshop/web/view/yundian/images/goods_edit/delete.png"></div><img src=' + dataURL + '></div>')
						//数据变更后的展示第一张图
						
					}
				}(files[i]));
				reader.readAsDataURL(files[i]);
				sendFile = files[i];
				var formData = new FormData();
		        formData.append("upfile",sendFile);
		        $.ajax({
		            url:'/wsy_prod/admin/Product/product/cailing_background_upload.php?customer_id=<?php echo $customer_id; ?>',
		            type:'POST',
		            data:formData,
		            cache: false,
		            async: true,
		            dataType:"json",
		            contentType: false,    //不可缺
		            processData: false,    //不可缺
		            success:function(data){
		                var html = '<img src="'+data.info+'" style="width:100%;height:100%;">';
		                html += '<input type="hidden" name="img_url" id="img_url" value="'+data.info+'">';
		                $('#images').html(html);
		                $('.filename').val(my);
		            }

		        });
			}


		} else {
			alert('该浏览器不支持文件管理。');
			return;
		}

		
	}

    var $list=$("#thelist");   //这几个初始化全局的百度文档上没说明，好蛋疼。  
    var $btn =$("#ctlBtn");   //开始上传  

  //   var uploader = WebUploader.create({  
  //       // 选完文件后，是否自动上传。  
  //       auto: false,  

  //       // swf文件路径  
  //       swf: '/mshop/web/static/js/Uploader.swf',  

  //       // 文件接收服务端。  
  //       server: '/mshop/admin/?m=cailing&a=file_slicing', 

  //       // 选择文件的按钮。可选。  
  //       // 内部根据当前运行是创建，可能是input元素，也可能是flash.  
  //       pick: '#picker',  

  //       chunked: true,//开启分片上传  
  //       threads: 1,//上传并发数  

  //       method:'POST',

  //       // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
		// resize: false,

		// //分片大小 1M
		// chunkSize: 1 * 1024 * 1024,	
		
		// //设定单个文件大小 10M
		// fileSingleSizeLimit:10*1024*1024,
		
		// //允许重复上传。可选
		// duplicate :true,
		
		// // 只允许选择音频文件。
		// accept: {
		// 	title: '彩铃音频文件',
		// 	extensions: 'mp3,wmv',
		//    mimeTypes: 'music/*'
		// }
  //   });  
  //   // 当有文件添加进来的时候  
  //   uploader.on( 'fileQueued', function( file ) {  
  //       // webuploader事件.当选择文件后，文件被加载到文件队列中，触发该事件。等效于 uploader.onFileueued = function(file){...} ，类似js的事件定义。  
  //       $list.append( '<div id="' + file.id + '" class="item">' +  
  //           '<h4 class="info">' + file.name + '</h4>' + 
  //           '<p class="state">等待上传...</p>' +  
  //           '</div>' );  
  //   });  
  //   // 文件上传过程中创建进度条实时显示。  
  //   uploader.on( 'uploadProgress', function( file, percentage ) {  
  //       var $li = $( '#'+file.id ),  
  //           $percent = $li.find('.progress .progress-bar');  

  //       // 避免重复创建  
  //       if ( !$percent.length ) {  
  //           $percent = $('<div class="progress progress-striped active">' +  
  //               '<div class="progress-bar" role="progressbar" style="width: 0%">' +  
  //               '</div>' +  
  //               '</div>').appendTo( $li ).find('.progress-bar');  
  //       }  

  //       $li.find('p.state').text('上传中');  

  //       $percent.css( 'width', percentage * 100 + '%' );  
  //   });  

  //   // 文件上传成功，给item添加成功class, 用样式标记上传成功。  
  //   uploader.on( 'uploadSuccess', function( file ) {
  //   	console.log(file); 
  //       $( '#'+file.id ).addClass('upload-state-done');  
  //   });  

  //   // 文件上传失败，显示上传出错。  
  //   uploader.on( 'uploadError', function( file ) {  
  //   	console.log(file);
  //       $( '#'+file.id ).find('p.state').text('上传出错');  
  //   });  

  //   // 完成上传完了，成功或者失败，先删除进度条。  
  //   uploader.on( 'uploadComplete', function( file ) {  
  //       $( '#'+file.id ).find('.progress').remove();  
  //       $( '#'+file.id ).find('p.state').text('已上传');  
  //   });  
  //   $btn.on( 'click', function() {  
  //       if ($(this).hasClass('disabled')) {  
  //           return false;  
  //       }  
  //       uploader.upload();  
  //   });  
</script>
</body>
</html>

