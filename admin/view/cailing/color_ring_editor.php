<html >
<head>
<meta charset="utf-8">
<title>彩铃编辑</title>
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
    padding: 0;
    height: 44px; 
}  
  
.progress {  
    height: 20px;  
    margin-bottom: 20px;  
    overflow: hidden;  
    background-color: #f5f5f5;  
    border-radius: 4px;  
    -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);  
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1)  
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
.webuploader-pick{width:100%;height:100%;line-height:32px}
</style>
</head>

<body>
<div class="div_new_content">
    <div class="WSY_content">
		<div class="WSY_columnbox WSY_list">
	
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">编辑彩铃</a> 
				</div>
			</div>

			<div class="WSY_data">
					<dl class="WSY_member">					
						<div>
							<dt>彩铃名称：</dt>
							<dd class="spa">
								<input onkeyup="prices_type(this)" type=text value="<?php echo $result['name']; ?>" name="name" id="name" maxlength="10" placeholder="请填写彩铃名称10字以内" style="width:300px;" />
							</dd>
						
						</div>
					</dl>

					<dl class="WSY_member">					
						<div>
							<dt>彩铃标签：</dt>
							<dd class="spa">
								<input onkeyup="prices_type(this)" type=text value="<?php echo $result['tip']; ?>" name="tip" maxlength="4" id="tip" placeholder="请填写彩铃标签4字以内" style="width:300px;" />
							</dd>
						
						</div>
					</dl>

					<dl class="WSY_member">					
						<div>
							<dt>彩铃价格：</dt>
							<dd class="spa">
								<input onkeyup="prices(this)" type=text value="<?php echo $result['price']; ?>" maxlength="12" placeholder="价格" name="price" id="price" style="width:300px;" />
							</dd>
						
						</div>
					</dl>

					<dl class="WSY_member">					
						<div>
							<dt>彩铃图片：</dt>
							<dd class="spa">
								<div style="width: 200px;height: 200px;border: 1px solid #ccc;" id='images'>
									<img src="<?php echo $result['img_url']; ?>" style="width:100%;height:100%;">
									<input type="hidden" name="img_url" id="img_url" value="<?php echo $result['img_url']; ?>">
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
								<div id="uploader" style="margin: auto;margin-top: -9px;">  
								    <div id="thelist" class="uploader-list"></div>  
								    <div class="btns">  
								        <table style="width: 200px;" border="0">  
								            <tr align="right">  
								                <td>  
								                    <!-- <div id="picker" style="float:left">选择文件</div> -->
								                    <div id="picker" class="btn btn-default webuploader-pick" style="padding:0;float:left;width:80px;height:32px">选择文件</div>  
								                </td>  
								                <td>  
								                    <button id="ctlBtn" class="btn btn-default" style="padding:0px;width:80px;height:34px">开始上传</button>  
								                </td>  
								            </tr>  
								        </table>  
								    </div>  
								</div> 
								<span style="color: #666;">(支持格式：mp3,/wmv 文件大小；0M~30M)</span>
								<br />
								<audio id="audio" src="<?php echo $result['music']; ?>" controls="controls"></audio>
							</dd>
							
						</div>
					</dl>

					<dl class="WSY_member">					
						<div>
							<dt>彩铃排序：</dt>
							<dd class="spa">
								<input  onkeyup='this.value=this.value.replace(/\D/gi,"");' onchange="sort(this)" maxlength="9" type=text value="<?php echo $result['sort']; ?>" name="sort" id="sort" style="width:300px;margin-bottom: 10px;margin-right: 32px;" placeholder="不填或为0默认为时间排序" /><span id="_span" style="color: red;"></span><br />
								<span style="color: #666;">排序限制：降序，数值越大排名越前，数值为空为0则看添加时间，时间最早，排名越前</span>
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

	$("#ctlBtn").click(function(){
		console.log($(".webuploader-element-invisible").val())
		console.log($("#info").text())
		if($("#info").text()==""){
			layer.alert('请选择文件');
		}
	})
	var _span = true;
	//排序是否存在
	function sort(obj){
		var val_s = $(obj).val();
		var sort = "<?php echo $result['sort']; ?>";

		if(val_s != '' && val_s != sort && val_s != '0'){
			$.post('/mshop/admin/index?m=cailing&a=setting_sort',{sort:val_s},function(res){
				console.log(res.errcode);
				if (res.errcode == '1') {
					$('#_span').text('提示：'+res.errmsg);
					_span = false;
				}else{
					$('#_span').text('');
					_span = true;
				}

			},'json');
		}else{
			_span = true;
			$('#_span').text('');
		}
		
	}

	//添加彩铃
	function add_color_bell(){
		var name = $('#name').val();//名字
		var tip = $('#tip').val();//标签
		var img_url = $('#img_url').val();//彩铃图片
		var price = $('#price').val(); //价格
		var sort = $('#sort').val(); //排序
		var music = $('#audio').attr('src'); //排序
		var customer_id = '<?php echo $customer_id; ?>';
		console.log(sort);
		// if (name == '' || img_url == '' || price == '') {
		// 	layer.alert('任何一项都不能为空');
		// 	return;
		// }

		if (name == '' || name.length > 10) {
			layer.alert('彩铃名称不能为空,限制10字以内');
			return;
		}

		if (tip.length > 4) {
			layer.alert('彩铃标签限制4字以内');
			return;
		}

		if(price == ''){
			layer.alert('彩铃价格不能为空');
			return;
		}

		if (img_url == '') {
			layer.alert('彩铃图片不能为空');
			return;
		}

		if (music == '') {
			layer.alert('音乐必须上传');
			return;
		}

		if (!_span) {
			layer.alert('排序已存在');
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
				'music':music,
				'op':'edit',
				'issale':"<?php echo $result['issale'] ?>",
				'cailing_id':"<?php echo $result['id'] ?>",
				'customer_id':'<?php echo $customer_id; ?>',
			},
			success: function(res){
				console.log(res);
				if (res.errcode == 1) {
					layer.alert('修改成功');
					setTimeout(function(){
						//window.location.reload();
						history.back(-1);
					},1000);
					
				}else{
					layer.alert('修改失败');
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
		obj.value=obj.value.replace(/^ +| +$/g,'');//禁止输入空格

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
					layer.alert('文件类型错误');
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
		            	console.log(data);
		                var html = '<img src="'+data.info+'" style="width:100%;height:100%;">';
		                html += '<input type="hidden" name="img_url" id="img_url" value="'+data.info+'">';
		                $('#images').html(html);
		                $('.filename').val(my);
		                $(evt).val('');
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

    var uploader = WebUploader.create({  
        // 选完文件后，是否自动上传。  
        auto: false,  

        // swf文件路径  
        swf: '/mshop/web/static/js/Uploader.swf',  

        // 文件接收服务端。  
        server: '/mshop/admin/?m=cailing&a=file_slicing', 

        // 选择文件的按钮。可选。  
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.  
        pick: '#picker',  

        chunked: true,//开启分片上传  
        threads: 1,//上传并发数  

        method:'POST',

        // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
		resize: false,

		//分片大小 1M
		chunkSize: 1 * 1024 * 1024,	
		
		//设定单个文件大小 30M
		fileSingleSizeLimit:30*1024*1024,
		
		//允许重复上传。可选
		duplicate :true,
		
		// 只允许选择音频文件。
		accept: {
			title: '彩铃音频文件',
			extensions: 'mp3,wmv',
		   mimeTypes: 'music/*'
		}
    });

    // 验证大小
    // uploader.on("error",function (type){ 
    //      if(type == "F_DUPLICATE"){
    //           win.alert("系统提示","请不要重复选择文件！");
    //      }else if(type == "Q_EXCEED_SIZE_LIMIT"){
    //           win.alert("系统提示","<span class='C6'>所选附件总大小</span>不可超过<span class='C6'>" + allMaxSize + "M</span>哦！<br>换个小点的文件吧！");
    //      }

    //  });

    // 当有文件添加进来的时候  
    uploader.on( 'fileQueued', function( file ) {  
        // webuploader事件.当选择文件后，文件被加载到文件队列中，触发该事件。等效于 uploader.onFileueued = function(file){...} ，类似js的事件定义。  
        $list.html( '<div id="' + file.id + '" class="item" style="margin-top: 2px;">' +  
            '<h4 id="info" class="info">' + file.name + '</h4>' + 
            '<p class="state">等待上传...</p>' +  
            '</div>' );
        $('#ctlBtn').attr('disabled',false);  
    });  
    // 文件上传过程中创建进度条实时显示。  
    uploader.on( 'uploadProgress', function( file, percentage ) {  
        var $li = $( '#'+file.id ),  
            $percent = $li.find('.progress .progress-bar');  

        // 避免重复创建  
        if ( !$percent.length ) {  
            $percent = $('<div class="progress progress-striped active" style="width:100px;margin-top: 7px;">' +  
                '<div class="progress-bar" role="progressbar" style="width: 0%">' +  
                '</div>' +  
                '</div>').appendTo( $li ).find('.progress-bar');
        }  

        $li.find('p.state').text('上传中：');

        $percent.css( 'width', percentage * 100 + '%' );
        return false;  
    });  

    // 文件上传成功，给item添加成功class, 用样式标记上传成功。  
    uploader.on( 'uploadSuccess', function( file , response) {
    	if( response.errcode != 0 ){
			layer.alert(response.errmsg);
			$list.html("");
			return false;
			
		}else{
			console.log(response.newfileurl);
	    	$('#audio').attr('src',response.newfileurl);
	        $( '#'+file.id ).addClass('upload-state-done');
	        $('#ctlBtn').attr('disabled',true); 
		}
    	
    });  

    // 文件上传失败，显示上传出错。  
    uploader.on( 'uploadError', function( file ) {  
        $( '#'+file.id ).find('p.state').text('上传出错');  
    });  

    // 完成上传完了，成功或者失败，先删除进度条。  
    uploader.on( 'uploadComplete', function( file ) {  
        $( '#'+file.id ).find('.progress').remove();  
        $( '#'+file.id ).find('p.state').text('上传完成');  
    });  
    $btn.on( 'click', function() {  
        if ($(this).hasClass('disabled')) {  
            return false;  
        }  
        uploader.upload();  
    });  
</script>
</body>
</html>

