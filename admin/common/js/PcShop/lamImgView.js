/**
 * 图像上传前的预览，只支持 IE6+和Firefox 3+, Chrome
 *
 * @author 
 * @version 4.2 最后修改时间 2012年12月18日
 * @link //www.lamsonphp.com
 * @example
 	<input name="thumb" id="thumb" type="file" onchange="LamImgView(this)" />
	<div id="lamImgBox"><img id="lamThumbImg" src="images/thumbview.jpg"  /></div>
 */
if(typeof(jQuery) != 'function')
{
	alert('jQuery库不存在，请检查该页面是否已成功导入jquery.js');
}

function LamImgView(obj, w, h)
{
	var $lamThumbImg = $('#lamThumbImg');
	w = w || $lamThumbImg.width();
	h = h || $lamThumbImg.height();
	
	if (obj.files && obj.files[0])
	{ 
		var reader = new FileReader(); 
		reader.onload = function (e) { $lamThumbImg.attr('src', e.target.result).width(w).height(h); }; 
		reader.readAsDataURL(obj.files[0]);
	}
	else	//IE下，使用滤镜
	{
		var $lamImgBox = $("#lamImgBox"); 
		$lamImgBox.width(w).height(h); //必须设置初始大小
		
		obj.select();
		
		if(window.top == window)	//非框架嵌入
		{
			obj.blur();	//解决IE9下document.selection拒绝访问的错误，不过如果网页是以框架的形式显示，仍然会报错
		}else
		{
			$lamImgBox.focus();	//如果当前页面被嵌在框架中，则file域.blur()之后，file域中原本被选中的文本将会失去选中的状态，因此，不能使用file域.blur()。可以让当前页面上的其他元素，如div，button等获得焦点即可，如div对象.focus()。注意，如果是div，则要确保div有至少1像素的高和宽，方可获得焦点。
		}
		
		var localsrc = document.selection.createRange().text;
		//图片异常的捕捉，防止用户修改后缀来伪造图片 
		try { 
			$lamImgBox[0].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)"; 
			$lamImgBox[0].filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = localsrc; 
		} catch (e)
		{ 
			alert("您上传的图片格式不正确，请重新选择!");
			return false; 
		} 
		$lamThumbImg.hide(); 
		document.selection.empty(); 
	} 
} 