<?php

header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
$customer_id = $_SESSION['city_CustomerID'];
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
// require('../../../../../weixinpl/back_init.php');
require('../../../../../weixinpl/common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');
require('../../../../../weixinpl/auth_user.php');
require('../../../../../weixinpl/common/utility_4m.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
$database = new \Key\DB();
$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);

$head=6;
_mysql_query("SET NAMES UTF8");
$new_baseurl = "http://".$http_host; 

$search_keyword="";   
if(!empty($_GET["search_keyword"])){
	$search_keyword = $_GET["search_keyword"];
};
$pagenum = 1;					
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * 20;
$end =20;

$cityarea_id = $_SESSION['city_shop_AcountID'];
$sql = "SELECT id,content,isused,creatime,name,is_open from weixin_cityarea_diy_template where isvalid=true and customer_id={$customer_id} and cityarea_id={$cityarea_id}";
if($search_keyword){
	$sql .= " and name like '%".$search_keyword."%'";
}
$sql .= " order by isused desc,is_open desc,id desc limit ".$start.",".$end."";
$data = $database->getData($sql);


session_start();

if($_SESSION['city_shop_Power']['power_store_change'] != 1){   //检查权限
	echo '<script>alert("权限不足！");window.history.go(-1)</script>';
	exit;
}   

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme;?>.css">
<link rel="stylesheet" type="text/css" href="css/per-style.css">
<link rel="stylesheet" href="/weixin/plat/Public/assets/css_v2.5/amazeui.min.css">
<link rel="stylesheet" href="/weixin/plat/Public/assets/css_v2.5/admin.css">
<link rel="stylesheet" href="/weixin/plat/Public/assets/css/amazeui.chosen.css">
<link rel="stylesheet" href="/weixin/plat/Public/assets/css/amazeui.datetimepicker.css"> 
<script type="text/javascript" src="js/jquery-1.12.1.min.js"></script>
<script type="text/javascript" src="js/layer/layer.js"></script>

</head>

<body>
<!--内容框架开始-->
<div style="overflow-x:hidden;width:368px; height:750px; background:url(images/background.png)  no-repeat top center; margin:0 auto;display:none;position:fixed;left:30%;top:0%;scrolling:no;z-index:99" id="show_preview">
<iframe style=" border:1px solid #ccc; margin-top:128px; margin-left:26px; overflow-x:hidden; overflow-y:auto;" id="iframe" frameBorder="no" height="521" width="317" vspace="0" src="../../../../common_shop/jiushop/index_custom_cityarea.php" ></iframe>
</div>

<!-- <div class="WSY_content" id="WSY_content_height" style="z-index:1"> -->
<!--微商城统计代码结束-->

<style type="text/css">
body{background-color:#f3f3f3;}
/*蓝色*/
.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#06a7e1;border:solid 1px #0b91c2;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#017ca9;cursor:pointer;}
.input_butn01 input{width:268px;}
.leftA01 .leftA01_dl dd .tj{background:#07a7e1;border:solid 1px #0b91c2;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#0b91c2;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#06a7e1;color:#fff;}
.btn{width:20px;height:20px;margin-right:8px}
.operation_btn{
	border-radius: 7px;
    padding: 2px 11px;
    color: #fff;
    font-size: 12px;
    top: -6px;
    position: relative;
}
.open_color{
	background-color: #078B40;
}
.close_color{
	background-color: #898787;
}
.use_color{
	background-color: #F8922B;
    color: #000;
}
.open_tip{
	display: inline-block;
    background-color: #9A9494;
    color: #fff;
    padding: 2px 10px;
    font-size: 12px;
    position: absolute;
}
.hint{
	background-color: #d7d7d7;display: inline-block;padding: 20px;position: fixed;top: 33%;left: 33%;z-index:222;
}
.closeBtn{
	position: absolute;right: 0;top: 0;font-size: 25px;font-weight: 900;transform: rotate(45deg);-ms-transform: rotate(45deg);-webkit-transform: rotate(45px);height: 25px;width: 25px; line-height: 25px;text-align: center;
}
.hintContent{
	background-color: #fff;width: 400px;display: inline-block;padding-bottom: 50px;position: relative;
}
.hintContent>p:first-child{
	margin-top: 10px;font-weight: bold;width: 95%;
}
.hintContent p{
	width: 90%;margin: auto;margin-bottom: 10px;line-height: 22px;color: #333333;
}
.confirm_btn{
	color: #fff;padding: 5px 30px;border-radius: 5px;bottom: 15px;
}
.confirm_button{
	background-color: #169bd5;position: absolute;left: 40px;
}
.cancel_button{
	background-color: #999999;position: absolute;right: 40px;
}
.shadowbg{
	width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #000;
    opacity: 0.4;
    z-index: 111;
}
.pointer-btn{
	cursor: pointer;
}
.am-table>thead>tr>th{border-bottom:0;}
.am-dropdown-content{text-align:center;}
table > thead > tr > th{ color:black; }
.red{color:red;}
</style>

<div class="am-cf admin-main admin-main-footer">
    <div class="admin-content-auto">
        <div class="am-cf am-padding">
            <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">自定义模板</strong> / <small>Templates</small></div>
        </div>
        <div class="am-g" style="overflow:hidden;padding:0 1.5rem;">
        <p class='red'>注意：自定义模板启用后，优先级是最高的，原有首页模板会停用。</p>
        </div>
        <div class="am-g" style="overflow:hidden;padding:0 1.5rem;margin-bottom:1.5rem;">
            <button type="submit" class="am-btn am-btn-success am-btn-sm" style="float:right;" onclick="location.href='custom.php?customer_id=<?php echo $customer_id_en; ?>&action=add'">+新建模板</button>
            <div class="am-u-sm-12 am-u-md-3" style="float:right;">
            	<form action="" method="get" accept-charset="utf-8">
	                <div class="am-input-group am-input-group-sm">
	                    <input type="text" class="am-form-field" name="search_keyword" placeholder="" value="<?php 	echo $search_keyword ?>">
	                    <span class="am-input-group-btn">
	                        <button class="am-btn am-btn-default">搜索</button>
	                    </span>
	                </div>
            	</form>
            </div>
            
        </div>

        <div class="am-g">
            <div class="am-u-sm-12">
                <table class="am-table am-table-striped table-main am-table-bordered am-table-centered table-bordered">
                    <thead>
                        <tr>
                            <th class="table-td-bordered-first am-text-center">模板名称</th>
                            <th class="am-text-center">创建时间</th>
                            <th class="am-text-center">状态</th>
                            <th class="am-text-center">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $key => $value) { ?>
                        <tr>
                            <td><?php echo $value['name'] ?></td>
                            <td><?php echo $value['creatime'] ?></td>
                            <td><button type="button" class="am-btn am-btn-default am-btn-xs am-round am-text-primary"><?php echo $value['isused']?'已启用':'无' ?></button></td>
                            <td>
                                <div class="am-btn-group-stacked">
                                    <!-- <button type="button" class="am-btn am-btn-default am-btn-xs am-text-warning" data-order-commission=""> 设为默认</button>  -->
                                    <div class="am-dropdown admin-margin-top-1" data-am-dropdown>
                                        <button class="am-btn am-btn-default am-btn-xs am-text-primary am-dropdown-toggle" data-am-dropdown-toggle>&nbsp;&nbsp;&nbsp;&nbsp;操作&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                        <ul class="am-dropdown-content">
                                            <li><a href="#" data-order-toggle="" class="am-text-primary" onclick="preview(<?php echo $value['id'];?>)"  value="<?php echo $value['id'];?>" >预览</a></li>            
                                            <li class="am-divider"></li>
                                            <li><a href="custom.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&temid=<?php echo $value['id'];?>&action=edit" data-order-toggle="" class="am-text-primary">编辑</a></li>
                                            <li class="am-divider"></li>
                                            <li><a href="#" data-order-toggle="" class="am-text-danger" onclick="temp_delete(<?php echo $value['id'];?>)">删除</a></li>
                                            <li class="am-divider"></li>
                                            <li><a href="#" data-order-toggle="" onclick="open_template(<?php echo $value['id'];?>);">启用模板</a></li>
                                            <li class="am-divider"></li>
                                            <li><a href="#" data-order-toggle="" onclick="close_template(<?php echo $value['id'];?>);">关闭模板</a></li>
                                        </ul>
                                    </div>    
                                </div>    
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!--翻页开始-->
		<div class="WSY_page">
			<ul class="WSY_pageleft" style="width:100%;margin-top:5px;">
				<?php 	if($wcount>0){ 
					for($i=1;$i<=$page;$i++){
				?>
					<li <?php if($i==$pagenum){ ?> class="one" <?php } ?> onClick="gopage(this)" value="<?php echo $i; ?>"><?php echo $i; ?></li>
				<?php }} ?>	
			<?php if($wcount>0){ ?>
			<form class="WSY_searchbox">
				<input class="WSY_page_search" name="WSY_jump_page" id="WSY_jump_page" value="">
				<input class="WSY_jump" type="button" value="跳转" onClick="jumppage()" style="border:none">
			</form>
			<?php } ?>
			</ul>
			 
		</div>
	<!--翻页结束-->
            </div>
        </div>
    </div>
</div>
<footer class="admin-footer">
  <hr>
  <p class="am-padding-left">© <?php echo date('Y') ?>  AllMobilize, Inc. Licensed under MIT license.</p> 
</footer>
<!-- </div> -->
<script src="/weixin/plat/Public/assets/js_v2.4.1/jquery.min.js"></script>
<script src="/weixin/plat/Public/assets/js_v2.4.1/amazeui.min.js"></script>
<script src="/weixin/plat/Public/assets/js_v2.4.1/app.js"></script>

<script>
$(function(){
	$('.custom_name').change(function(){//修改名称
		var name=$(this).children().val();
		var id=$(this).data('temid');
		changename(id,name);
	});
	$("#checkAllChange").click(function() { // 全选/取消全部 
		if (this.checked == true) { 
			$(".temid").each(function() { 
			this.checked = true; 
			}); 
		} else { 
			$(".temid").each(function() { 
			this.checked = false; 
			}); 
		} 
	}); 

	$("#checkAll").click(function() { // 全选 
		$(".temid").each(function() { 
			this.checked = true; 
		}); 
	}); 
	 
	$("#removeAll").click(function() { // 取消全部
		$(".temid").each(function() { 
			this.checked = false; 
		}); 
	}); 
		
	$("#reverse").click(function() { // 反选 
		$(".temid").each(function() { 
			if (this.checked == true) { 
				this.checked = false; 
			} else { 
				this.checked = true; 
			} 
		}) 
	}); 
	//批量删除 
	$("#delAll").click(function() {
		layer.confirm('确定要删除吗？', {
			title: false,
			skin:'red-skin',
			shift:6,
  			btn: ['删除','取消'] //按钮
		}, function(index){
	  		var arrtemid = new Array();
			var temidarr="";
			$(".temid").each(function(i) { 
				if (this.checked == true) { 
				//	arrtemid[i] = $(this).val(); 
					temidarr+=$(this).val()+",";
				} 
			});
			var option="deleteall";
			$.ajax({  
				type : "POST",  
				url : "save_control.php",
				data : {"option" : option,"temidarr" : temidarr},
				dataType: "json",		
				success : function(result) {
					if(result.msg=="1"){
						window.location.reload(); 
					}
				}
			});
			layer.close(index);
		}, function(){
  			
		});
	}); 
}); 

function changename(diy_temid,name){  //修改模板名字
	var option="changename";
	var customer_id=<?php echo $customer_id;?>;
	$.ajax({  
		type : "POST",  
		url : "save_control.php",
		data : {"option" : option,"customer_id" : customer_id,"diy_temid" : diy_temid,"name" : name},
		dataType: "json",		
		success : function(result) {
			console.log(result.msg);
		}
		
	});
	
}

function temp_delete(diy_temid){ //删除模板
		layer.confirm('确定要删除吗？', {
			title: false,
			skin:'red-skin',
			shift:6,
  			btn: ['删除','取消'] //按钮
		}, function(){
		var option="temp_delete";
	$.ajax({  
		type : "POST",  
		url : "save_control.php",
		data : {"option" : option,"diy_temid" : diy_temid},
		dataType: "json",		
		success : function(result) {
			if(result.code=="1"){
				window.location.reload(); 
			}
		}
		
	});
		}, function(){
  				
		});
} 
function ShowElement(element) //双击可编辑
{
	var oldhtml = element.innerHTML;
	var newobj = document.createElement('input');
	//创建新的input元素
	newobj.type = 'text';
	newobj.value=oldhtml;
	//为新增元素添加类型
	newobj.onblur = function(){
		element.innerHTML = this.value ? this.value : oldhtml;
	//当触发时判断新增元素值是否为空，为空则不修改，并返回原有值 
}
	element.innerHTML = '';
	element.appendChild(newobj);
	newobj.focus();
}
function temp_check(diy_temid){ //选择模板
	<?php
		if( $isused_tag ){
	?>
	showConfirmBox('提示','如果您确认将该模板设置为首页模板，则已有的自定义首页模板将被替换成该模板。','您确定将该模板设置为首页模板吗？','确定','取消',function(){
		var option="temp_check";
		$.ajax({  
			type : "POST",  
			url : "save_control.php",
			data : {"option" : option,"diy_temid" : diy_temid},
			dataType: "json",		
			success : function(result) {
				if(result.code=="1"){
					window.location.reload(); 
				}
			}
		});
	});
	<?php
		} else {
	?>
	var option="temp_check";
	$.ajax({  
		type : "POST",  
		url : "save_control.php",
		data : {"option" : option,"diy_temid" : diy_temid},
		dataType: "json",		
		success : function(result) {
			if(result.code=="1"){
				window.location.reload(); 
			}
		}
	});
	<?php }?>
}

function temp_cancel(diy_temid){ //停用模板
	var option="temp_cancel";
	$.ajax({  
		type : "POST",  
		url : "save_control.php",
		data : {"option" : option,"diy_temid" : diy_temid},
		dataType: "json",		
		success : function(result) {
			if(result.code=="1"){
				window.location.reload(); 
			}
		}
		
	});
	
}
var pagenum = '<?php echo $pagenum ?>';
var page = '<?php echo $page ?>';
function prePage(){
	pagenum--;
	document.location= "custom_control.php?pagenum="+pagenum+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
}
  
function nextPage(){
	pagenum++;
	document.location= "custom_control.php?pagenum="+pagenum+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
}
function search(){
	pagenum = 1;
	var search_keyword = document.getElementById("search_keyword").value;
	document.location= "custom_control.php?pagenum="+pagenum
	+"&search_keyword="+search_keyword+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";

}
function gopage(v){
	var a=$(v);
	if(a.hasClass('one')){
		return false;
	}else{
		document.location= "custom_control.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a.val();
	}
}
function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		document.location= "custom_control.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a;
	}
}
function preview(temid){ 
	$("#show_preview").css("display","block");
	$("#WSY_content_height").css({ filter: "alpha(opacity=50)", "-moz-opacity": "0.5" , "-khtml-opacity": "0.5", opacity: "0.5"});
	document.getElementById("iframe").src='../../../../common_shop/jiushop/index_custom_cityarea.php?temid='+temid; 
	setTimeout(funcx, 1000);
	

}
function funcx(){
	$('html').click(function(){
		$("#show_preview").css("display","none");
		$('html').off('click')
		$('html').unbind("click");
	})
	$('.WSY_columnbox').click(function(){
        $("#show_preview").hide();
		window.location.reload(); 
    });
}
function show_tip(obj){
	var html = '';
	
	html = '<div class="open_tip">请先启用该模板</div>';
	
	$(obj).after(html);
}
function close_tip(obj){
	$('.open_tip').remove();
}
function open_template(diy_temid){
	var option = "open_template";
	$.ajax({  
		type : "POST",  
		url : "save_control.php",
		data : {"option" : option,"diy_temid" : diy_temid},
		dataType: "json",		
		success : function(result) {
			if(result.code=="1"){
				window.location.reload(); 
			}
		}
		
	});
}
function close_template(diy_temid){
	var option = "close_template";
	$.ajax({  
		type : "POST",  
		url : "save_control.php",
		data : {"option" : option,"diy_temid" : diy_temid},
		dataType: "json",		
		success : function(result) {
			if(result.code=="1"){
				window.location.reload(); 
			}
		}
		
	});
}

function showConfirmBox(title,content1,content2,confirm_btn,cancle_btn,callbackfunc){
	var html = '';
	
	html += '<div class="hint">';
	html += '	<span class="pointer-btn closeBtn close-btn">+</span>';
	html += '	<div class="hintContent">';
	html += '		<p class="confirm_title">'+title+':</p>';
	html += '		<p class="content-one">'+content1+'</p>';
	html += '		<p class="content-tow">'+content2+'</p>';
	html += '		<span class="pointer-btn confirm_btn confirm_button">'+confirm_btn+'</span>';
	html += '		<span class="pointer-btn confirm_btn cancel_button close-btn">'+cancle_btn+'</span>';
	html += '	</div>';
	html += '</div>';
	html += '<div class="shadowbg"></div>';
	
	$('body').append(html);
	
	$('.confirm_button').click(function(){
		if( callbackfunc ){
			callbackfunc();
		}
		closeDialog();
	});
	$('.close-btn').click(function(){
		closeDialog();
	});
}

function closeDialog(){
	$('.hint').remove();
	$('.shadowbg').remove();
}
</script>
<!--选择链接的JS结束-->
</body>
</html>  
<?php 

mysql_close($link);
?>