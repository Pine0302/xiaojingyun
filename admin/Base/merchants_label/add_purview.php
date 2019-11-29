<?php 
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php'); 
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
  
	$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');
	_mysql_query("SET NAMES UTF8");
	require('../../../../weixinpl/proxy_info.php');

	if (!empty($_GET['keyid'])) {
		$keyid = $_GET['keyid'];
		$keyid =passport_decrypt($keyid);
	}
    if (!empty($_GET['op'])) {
		$op = $_GET['op'];
	}
	/*if (!empty($_GET['level_name'])) {
	    $level_name =$_GET['level_name'];
    }
    if (!empty($_GET['label_level'])) {
	    $label_level =$_GET['label_level'];
    }
    if (!empty($_GET['type'])) {
	$type =$_GET['type'];
    }*/

    $sql = "SELECT * from weixin_cityarea_supply_label where id='{$keyid}' and customer_id='{$customer_id}' ";
    $result = _mysql_query($sql) or die(mysql_error().$sql);
    if($result){
        $data = mysql_fetch_assoc($result);
    }
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<title>合作商设置</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style type="text/css">
.WSY_bulkboximg dt img{
	display: block;
}
.WSY_bulkboximg dt{
	text-align: center;
	margin-right: 6px;
}	
</style>
</head>
<script>
 function check(num){
	var check_num=/^[0-9]*$/.test(num);
	return check_num;
}	

 function submitV(a){
	// alert();
	document.getElementById("upform").submit();	   
 } 

</script>
<style type="text/css">
.WSY_member textarea {
width: 350px;
height: 150px;
}
dt{
	margin-top:6px;
}
.WSY_member dt {
    width: 50px;
}
.WSY_bulkboximg dt img {
    height: 50px;
}
.label_image{width: 100px; height: 100px}
</style>
<body>
<div class="div_new_content">
<form action="save_purview.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
	<input type="hidden" name="keyid" value="<?php echo $keyid ?>" />
	<input type="hidden" name="type" value="<?php echo $type ?>" />
	<?php if($op=='add'){?>
	<input type="hidden" name="op" value="add" />
	<?php }elseif($op=='detail'){?>
	<input type="hidden" name="op" value="detail" />
    <?php }?>
    <div class="WSY_content">
		<div class="WSY_columnbox">
	
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1"><?php if($keyid>0){echo "添加";}else{echo "修改";} ?>商家等级</a>
				</div>
			</div>

			<div class="WSY_data">
			<?php if($keyid>0){?>
				<dl class="WSY_member">
					<dt>ID:</dt>
					<dt><?php echo $keyid;?></dt>
				</dl>
				<?php }?>
				<dl class="WSY_member">
					<dt>等级：</dt>
					<dd >
					<select name="label_level" id="label_level" style="margin-top:5px;">
						<option value="1" <?php if($data['label_level']==1){echo 'selected="selected"';} ?> >一级</option>
						<option value="2" <?php if($data['label_level']==2){echo 'selected="selected"';} ?> >二级</option>
						<option value="3" <?php if($data['label_level']==3){echo 'selected="selected"';} ?> >三级</option>
						<option value="4" <?php if($data['label_level']==4){echo 'selected="selected"';} ?> >四级</option>
						<option value="5" <?php if($data['label_level']==5){echo 'selected="selected"';} ?> >五级</option>
					</select>
					</dd>
				</dl>	

				<dl class="WSY_member">
					<dt>名称：</dt>
					<dd><input type="text" value="<?php echo $data['level_name'] ?>" name="level_name" id="level_name" style="width:250px;margin-top:5px;"></dd>
				</dl>					
				<div class="WSY_bulkboximgbox">
				<div class="WSY_bulkboximg" style="width:500px;height:260px;">
					<p>标签图标</p>
					<dl>
						<dd><label><input data-id="customImg" name="selectIcon" value="0" type="radio" <?php if( $data['label_image_type'] === 0 ) echo 'checked' ?> onclick='changeIcon(this)' />使用我上传的图标</label></dd>
						<dd><label><input data-id="defaultImg" name="selectIcon" value="1" type="radio" <?php if( $data['label_image_type'] === 1 ) echo 'checked' ?> onclick='changeIcon(this)' />使用默认图标</label></dd>
						<dd><label><input data-id="noImg" name="selectIcon" value="2" type="radio" <?php if( $data['label_image_type'] === 2 ) echo 'checked' ?> onclick='changeIcon(this)' />不使用图标</label></dd>
					</dl>
					<dl class="defaultImg Icon" >
						<dt>
							<img src="../../Common/images/Base/label/label-1.png" />
							<input name="defaultImg" value="/weixinpl/back_newshops/Common/images/Base/label/label-1.png" type="radio" checked/>
						</dt>
						<dt>
							<img src="../../Common/images/Base/label/label-2.png" />
							<input name="defaultImg" value="/weixinpl/back_newshops/Common/images/Base/label/label-2.png" type="radio" checked/>
						</dt>
						<dt>
							<img src="../../Common/images/Base/label/label-3.png" />
							<input name="defaultImg" value="/weixinpl/back_newshops/Common/images/Base/label/label-3.png" type="radio" checked/>
						</dt>
						<dt>
							<img src="../../Common/images/Base/label/label-4.png" />
							<input name="defaultImg" value="/weixinpl/back_newshops/Common/images/Base/label/label-4.png" type="radio" checked/>
						</dt>
						<dt>
							<img src="../../Common/images/Base/label/label-5.png" />
							<input name="defaultImg" value="/weixinpl/back_newshops/Common/images/Base/label/label-5.png" type="radio" checked/>
						</dt>

					</dl>
					<dl class="customImg Icon" style="display:none; display: inline-block">
						<dd class="WSY_bulkboxdd03">
							<a>请根据您选择的模板上传合适的图片,一般图片建议尺寸宽20px,高20px;</a>
							<!--上传文件代码开始-->
							<div class="uploader white">
								<input type="text" class="filename" readonly/>
								<input type="button" name="file" class="button" value="上传..."/>
								<input size="17" name="upfile1" id="upfile1" type=file value="<?php echo $data['label_image'];?>" onchange='img_browse(this)' data-name='label_image11'>
								<input type=hidden value="<?php echo $data['label_image'] ?>" name="label_image" id="label_image"  />
							</div>
								
							<!--上传文件代码结束-->
						</dd>
						<img class='label_image' id='label_image11' src="<?php echo $data['label_image']; ?>" alt="">

					</dl>
				</div>
				<div class="WSY_bulkboximg" style="width:500px;height:260px;">
					<p>标签图片</p>
					<dl style="display: inline-block">
						<dd><label><input name="selectImg" data-id="Img" value="0" type="radio" <?php if( $data['label_image2'] ) echo 'checked' ?> onclick='changeImg(this)' />使用我上传的图片</label></dd>
						<dd><label><input name="selectImg" value="1" type="radio" <?php if( !$data['label_image2'] ) echo 'checked' ?> onclick='changeImg(this)' />不使用图片</label></dd>
					</dl>
					<dl class="Img" <?php if( !$data['label_image2'] ) echo "style='display:none'" ?> >
						<dd class="WSY_bulkboxdd03">
							<a>请根据您选择的模板上传合适的图片,一般图片建议尺寸宽120px,高15px;</a>
							<!--上传文件代码开始-->
							<div class="uploader white">
								<input type="text" class="filename" readonly/>
								<input type="button" name="file" class="button" value="上传..."/>
								<input size="17" name="upfile2" id="upfile2" type=file value="<?php echo $data['label_image2'];?>" onchange='img_browse(this)' data-name='label_image22'>
								<input type=hidden value="<?php echo $data['label_image2'] ?>" name="label_image2" id="label_image2" /> 
							</div>
							<!--上传文件代码结束-->
						</dd>
						<img style='width: 120px; height: 15px;' class='label_image' id='label_image22' src="<?php echo $data['label_image2'] ?>" alt="">
 
					</dl>
				</div>
				</div>
				<div class="WSY_text_input01" style="margin-top:100px;margin-right:45%;">
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
				</div>
			</div>
	
		</div>
		
	</div>
 </form>

<div style="width:100%;height:20px;">
</div>
</div>	
<!--内容框架结束-->
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript">
var label_image_type = '<?php echo $data['label_image_type']?$data['label_image_type']:0 ?>';
var label_image = '<?php echo $data['label_image'] ?>';
// alert(label_image_type)
$("[name='selectIcon'][value="+label_image_type+"]").click();
$("[name='defaultImg'][value='"+label_image+"']").click();
	/*$("#customImg").click(function(){
		$(".customImg").show();
		$(".defaultImg").hide();
	})
	$("#defaultImg").click(function(){
		$(".customImg").hide();
		$(".defaultImg").show();
	})*/
	function changeIcon(obj){
		var id = $(obj).data('id');
		$('.Icon').hide();
		console.log(id)
		$('.'+id).show();
	}

	function changeImg(obj){
		var id = $(obj).data('id');
		$('.Img').hide();
		$('.'+id).show();
	}

	function img_browse(obj){
	    var objUrl = getObjectURL(obj.files[0]) ;
	    if (objUrl) {
	        var img = $(obj).data('name'); 
	    console.log("objUrl = "+img) ;
	        $("#"+img).attr("src", objUrl) ;
	    }
	}

	function getObjectURL(file) {
	    var url = null ; 
	    if (window.createObjectURL!=undefined) { // basic
	        url = window.createObjectURL(file) ;
	    } else if (window.URL!=undefined) { // mozilla(firefox)
	        url = window.URL.createObjectURL(file) ;
	    } else if (window.webkitURL!=undefined) { // webkit or chrome
	        url = window.webkitURL.createObjectURL(file) ;
	    }
	    return url ;
	}
</script>
</body>

<?php mysql_close($link);?>	
</html>