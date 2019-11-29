<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link =    mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');



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
$name = "";  //分类名称  
$sort = '';  //优先级
$superior_id = '';  //上级
$is_submenu = 0;  //是否有子菜单
$content ='';//文章内容
$qurey_content = "SELECT name,sort,content,superior_id,is_submenu FROM pc_help_center_t WHERE id =".$id." and customer_id=".$customer_id." and isvalid=1";
$result_content=_mysql_query($qurey_content)or die('Query failed'.mysql_error());
while($row = mysql_fetch_object($result_content)){
		$name = $row->name;
		$sort = $row->sort;
		$content = $row->content;
		$is_submenu = $row->is_submenu;
		$superior_id = $row->superior_id;
	}
?>
<!doctype html>
<html>
<head>
<title>分类添加</title>
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
</style>
<body>
<!--内容框架-->
	<div class="WSY_content">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">分类添加设置</a>
				</div>
			</div>
			<!--列表头部切换结束-->
            <!--关注用户开始-->
            <form action="help_center.class.php?customer_id=<?php echo $customer_id_en;?>"  method="post" id="upform" name="upform">
              <div id="products">
                  <div class="WSY_data" id="WSY_dataddbox">
                  	<input type="hidden" value='<?php echo $op;?>' name="op">
					<input type="hidden" value='<?php echo $id;?>' name="id">
                      <dl class="WSY_bulk WSY_bulk_height WSY_bulk01">
                          <dt>分类名称：</dt>
                          <dd><input type="text" name="name"  id="name" value="<?php echo $name;?>"></dd>
                      </dl>
                      <dl class="WSY_bulk WSY_bulk_height WSY_bulk01">
                          <dt>优先级</dt>
                          <dd><input type="text" name="sort"  id="sort" value="<?php echo $sort;?>"></dd>
                      </dl>
                      <dl class="WSY_bulk">
                          <dt>是否有子菜单：</dt>
                          <dd><label><input type=radio name="is_submenu" value=0 <?php if($is_submenu==0){?>checked<?php } ?>  onClick="chksubmenu(0);" />没有</label></dd>
                          <dd> <label><input type=radio name="is_submenu" value=1 <?php if($is_submenu==1){?>checked<?php } ?>  onClick="chksubmenu(1);" />有</label></dd>
                      </dl>
                      <?php if($is_submenu==1){?>
                      <dl class="WSY_bulk WSY_bulk_height WSY_bulk01" id="div_menucontent"></dl>
                      <?php }else{?>
                      <dl class="WSY_bulk WSY_bulk_height WSY_bulk01" id="div_menucontent">
			              <div>文章内容:</div>
			              <textarea type="text" name="content"  id="content"><?php echo $content;?></textarea>
                      </dl>
                      <?php }?>
                      <div class="WSY_text_input01">
                          <div class="WSY_text_input"><input type="button" class="WSY_button" onClick="submitV();" value="提交"></div>
                      </div>
                  </div>
                </div>

              </form>
              <!--关注用户结束-->
		
        
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
    //是否有子菜单
	var is_submenu = <?php echo $is_submenu; ?>;
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
		var name = document.getElementById('name').value;
		if( name == "" ){
			alert('请输入分类名称');
			return false;
		}
		var sort = document.getElementById('sort').value;
		if( sort == "" ){
			alert('请输入优先级');
			return false;
		}
        var regu_s =/^[0-9]*[1-9][0-9]*$/;
        var re_s = new RegExp(regu_s);
        if(!re_s.test(sort)){
        	alert('优先级请输入非零正整数');
           return false; 
        }
        var id = '<?php echo $id ;?>';
        // var list = document.getElementById('is_submenu').value;
        // win_alert(list);return;

			$.ajax({
				url: 'help_center.class.php',
				data: {'name':name,'id':id,'op':'checkTitle'},
				type: 'post',
				dataType: 'json',
				success:function(res){
					console.log(res.status);
					if(res.status==1){
						alert('分类名称重名，请重新命名！');
						return false;
					}else{
						document.getElementById("upform").submit();
						// console.log(res)
			            // location.href= "help_center.php";
					}
				},
				error:function(er){
					
				}
			})	
	}
		
</script>


</body>
</html>