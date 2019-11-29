<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link =    mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

require_once('shoproom.php');
$pcshop = new Pcshop();
$customer_id_en = $pcshop->customer_id_en;
$navinfo = $pcshop->nav_info();

//上传图片
$uploadfile_url_x = $_SERVER['DOCUMENT_ROOT'].'/weixinpl/back_newshops/PcShop/information/interfaceroom.php?action=uploadfile&customer_id='.$customer_id_en;
$banner_add_do_url = $_SERVER['DOCUMENT_ROOT'].'/weixinpl/back_newshops/PcShop/information/interfaceroom.php?action=banner_add_do&customer_id='.$customer_id_en;
$banner_url = $pcshop->http.'/weixinpl/back_newshops/PcShop/information/banner.php?customer_id='.$pcshop->customer_id_en;
$banner_edt_do_url = $_SERVER['DOCUMENT_ROOT'].'/weixinpl/back_newshops/PcShop/information/interfaceroom.php?action=banner_edt_do&customer_id='.$customer_id_en;

$id = -1;  //分类ID
if(!empty($_GET["id"])){
	$id = $configutil->splash_new($_GET["id"]);
}

// echo $is_submenu;return;
$action = "";  //操作 add:新增 edit:修改
if(!empty($_GET["action"])){
	$action = $configutil->splash_new($_GET["action"]);
}
$op = 'add';
if($_GET["op"]){
	$op	=	$configutil->splash_new($_GET["op"]);	
}
$title = ""; //分类名称  
$content ='';//文章内容
$sort  = -1;
$qurey_content = "SELECT title,sort,content FROM pcshop_merchants_settled_data WHERE id =".$id." and isvalid=1";
$result_content=_mysql_query($qurey_content)or die('Query failed'.mysql_error());
while($row = mysql_fetch_object($result_content)){
	$title = $row->title;
	$sort = $row->sort;
	$content = $row->content;
}
$flag = $_GET['op'];
$edt_id = isset($_GET['id'])? trim($_GET['id']):'';
$opt = ($flag=='add')? 1:0;
$edtgoodsinfo = '';
if(!$opt)$edtgoodsinfo = $pcshop->singlenavinfo($edt_id);

//$banner_add_do_url
if(!$opt){//编辑
	$nav_op_id = $edt_id;
	$banner_edt_do_url .= '&edtid='.$edt_id;
}



?>
<!doctype html>
<html>
<head>
<title>文档添加</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">

</head>
<style>
.button_icon01:hover{
	background-color: #20b3a4;
	border: solid 1px #20b3a4;
	color: #fff;
}
.button_icon01{
	display: block;
	cursor: pointer;
	background-color: #f3f3f3;
	border: solid 1px #dadada;
	margin-right: 10px;
	padding-left: 10px;
	padding-right: 10px;
	line-height: 24px;
	margin-right:10px;
}
#div_text p i{	
	display: block;
	height: 24px;
	float: left;
	line-height: 24px;
}
#div_text p input{	
	width: 200px;
	height: 24px;
	border: solid 1px #ccc;
	border-radius: 2px;
	padding-left: 5px;
}
#div_text p{
	display: block;
	float: none;
	overflow: hidden;
	margin-top: 5px;
	margin-bottom: 10px;
}
.WSY_button.xx{
    margin-right: 96%;
    width: 91px;
    height: 32px;
}
.WSY_bulk01.logo dt:nth-child(1){
	position: relative;
    top: 44px;
    left: 1%;
}

.WSY_bulk01.logo dt:nth-child(2){
	position: relative;
    top: 66px;
    left: -6%;
}

.WSY_bulk01.logo dd{
	position: relative;
    left: -6%;
}
input[name=navigation_link]{
	width:480px !important;
}
.confirm{
	margin-left:58px !important;
}

.cancel{
	margin-left:-56px !important;
}
.WSY_bulk_height.WSY_bulk01.link_x dt{
	position: relative;
	left: 2%;
}
.WSY_bulk01.logo img{
	width:105px;
    height:105px;
}

</style>
<body>
<!--内容框架-->
	<div class="WSY_content">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1 banner">导航名称添加</a>
				</div>
			</div>
			<!--列表头部切换结束-->
            <!--关注用户开始-->
            <form action="<?php if(!$opt){echo $banner_edt_do_url;}else{echo $banner_add_do_url;}?>"  method="post" id="upform" name="upform">
              <div id="products">
                  <div class="WSY_data" id="WSY_dataddbox">
                  	<input type="hidden" value='<?php echo $op;?>' name="op">
					<input type="hidden" value='<?php echo $id;?>' name="id">
                      <dl class="WSY_bulk WSY_bulk_height WSY_bulk01">
                          <dt>导航名称：</dt>
                          <dd><input type="text" name="title"  id="title" value="<?php if(!$opt){print $edtgoodsinfo['nav_name'];}else{echo '';}?>"></dd>
                      </dl>
                      <dl class="WSY_bulk WSY_bulk_height WSY_bulk01">
                          <dt>导航排序：</dt>
                          <dd>
                              <select name="listorder" class="nav_sort">
                                    <option value="-1" selected="selected">--请选择排序--</option>
									<option value="first">设定为第一个</option>
									<?php 
								    $navinfoall = $pcshop->navinfoall();
									if(!empty($navinfoall)){
										foreach($navinfoall as $key=>$vel){
									?>
									<option value="<?php echo $vel['listorder'];?>">[于 <?php echo $vel['nav_name'];?> 之后]</option>
									<?php }} ?>
							  </select>
                          </dd>
                      </dl>
                      <dl class="WSY_bulk WSY_bulk_height WSY_bulk01 logo">
                          <dt>LOGO：</dt>
                          <dt>尺寸:105*105</dt>
                          <dd>
                              <img src="<?php $default_picc = 'static/img/u64.png'; if(!$opt){echo $pcshop->http.$edtgoodsinfo['logo'];}else{print $default_picc;}?>" onclick="uploadFile(this,'#logo_pic')"/>
                              <input type="hidden" name="upload_pic" value=""/>
                              <input type="hidden" name="upload_pic_id" value=""/>
                          </dd>
                      </dl>
                      <dl class="WSY_bulk WSY_bulk_height WSY_bulk01 link_x">
                          <dt>链接：</dt>
                          <dd><input type="text" name="navigation_link" value="<?php if(!$opt){print $edtgoodsinfo['link'];}else{echo '';}?>"></dd>
                      </dl>

                      <!--<dl class="WSY_bulk">
                          <dt>是否有子菜单：</dt>
                          <dd><label><input type=radio name="is_submenu" value=0 <?php if($is_submenu==0){?>checked<?php } ?>  onClick="chksubmenu(0);" />没有</label></dd>
                          <dd> <label><input type=radio name="is_submenu" value=1 <?php if($is_submenu==1){?>checked<?php } ?>  onClick="chksubmenu(1);" />有</label></dd>
                      </dl>-->                    
                      <!--<dl class="WSY_bulk WSY_bulk_height WSY_bulk01" id="div_menucontent">
			              <div>文档内容:</div>
			              <textarea type="text" name="content"  id="content"><?php echo $content;?></textarea>
                      </dl>-->
                      <div class="WSY_text_input01">
                          <div class="WSY_text_input"><input type="button" class="WSY_button confirm" onClick="submitV();" value="提交"></div>
                          <div class="WSY_text_input"><input type="button" class="WSY_button cancel" onClick="cancel();" value="返回"></div>
                      </div>
                  </div>
                </div>
              </form>
              <!--关注用户结束-->
              <input type="file" name="logo_pic" id="logo_pic" style="display:none"/>
        </div>
        <!--列表内容大框结束-->
	</div>
	<!--内容框架结束--><?php mysql_close($link);?>

	<!--配置ckeditor和ckfinder-->
<script type="text/javascript" src="../../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<!--编辑器多图片上传引入开始-->
<script type="text/javascript" src="../../../../weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/handlers.js"></script>
<!--编辑器多图片上传引入结束-->	
<script>
CKEDITOR.replace( 'content',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../../../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../../../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Images',
filebrowserFlashBrowseUrl : '../../../../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Flash',
filebrowserUploadUrl : '../../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});

</script>
<script>
    var edtag = "<?php echo $opt? 0:1;?>";
	function chksubmenu(v){
	switch(v){
		case 0:
		document.getElementById("div_menucontent").style.display="block";
		break;
		case 1:
		document.getElementById("div_menucontent").style.display="none";
		break;
	}
		document.getElementById("is_submenu").value=v;
		is_submenu = v;
	}
	// ---------提交------start
	function submitV(){
		var title = document.getElementById('title').value;
		if( title == "" ){
			alert('请输入导航名称');
			return false;
		}
		/*var listorder_val = $("[name=listorder][class=nav_sort]").val();
		if(!listorder_val){
			alert('请选择排序');
			return false;
		}*/
		//var sort = document.getElementById('sort').value;
		var upload_pic = $("[name=upload_pic]").val();
		var upload_pic_id = $("[name=upload_pic_id]").val();
		if(!edtag){
			if(upload_pic.length<=0||upload_pic_id==''){
				alert('请选择LOGO');
				return false;
			}
		}
		var navigation_link = $("[name=navigation_link]").val();
		if(navigation_link==''){
			alert('请输入导航链接');
			return false;
		}

        /*var regu_s =/^[0-9]*[1-9][0-9]*$/;
        var re_s = new RegExp(regu_s);
        if(!re_s.test(sort)){
        	alert('优先级请输入非零正整数');
           return false; 
        }*/
        //var id = '<?php echo $id ;?>';
        // var list = document.getElementById('is_submenu').value;
        // win_alert(list);return;

			// $.ajax({
				// url: 'help_center.class.php',
				// data: {'name':name,'id':id,'op':'checkTitle'},
				// type: 'post',
				// dataType: 'json',
				// success:function(res){
					// console.log(res.status);
					// if(res.status==1){
						// alert('分类名称重名，请重新命名！');
						// return false;
					// }else{
						// document.getElementById("upform").submit();
						// // console.log(res)
			            // // location.href= "help_center.php";
					// }
				// },
				// error:function(er){   
					
				// }
			// })	
		document.getElementById("upform").submit();
	}

	$(function(){
		window.onload = function(){
            var title = '';
			var flag = "<?php echo $flag;?>";
			if(flag=='add')title = '导航名称添加';
			if(flag=='update')title = '导航名称编辑';
			$(".white1.banner").html(title);
		}
	});

	function uploadFile(obj,file_id){
	    $(file_id).click();
	    var uploadfile_url = "<?php echo $uploadfile_url_x;?>";
		$(file_id).unbind().change(function(){
	        var files = this.files[0];
	        var img = new Image();
	        var reader = new FileReader();
	        reader.readAsDataURL(files); 
	        reader.onload = function(e){ 
	            var mb = (e.total/1024)/1024;
	            if(mb>=2){
	                alert('2M');
	                return;
	            }
	            img.src = this.result;
	            $(obj).attr('src',img.src);
	        }
	        var formData = new FormData();
	        formData.append('cover_pic',$(file_id)[0].files[0]);
	        $.ajax({ 
	            url:uploadfile_url,
	            type:'post',
	            cache:false,
	            data:formData,
		        dataType:'json',
	            processData:false,
	            contentType:false,
	            success:function(res){
	                console.log(res);
	                if(!res.errcode){
	                	$(obj).attr('src',res.show_pic);
	                	$("[name=upload_pic_id]").val(res.pic_id);
	                	$("[name=upload_pic]").val(res.file_path);
	                	return;
	                } alert(res.data);return;
	            },
	            error:function(msg){
	                console.log(res);
	            }
	        });
	    });
	}

	function cancel(){
		location.href = "<?php echo $banner_url;?>";
	}
		
</script>


</body>
</html>