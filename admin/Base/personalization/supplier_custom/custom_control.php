<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$_SESSION['C_id']=$customer_id;
require('../../../../../weixinpl/back_init.php');
require('../../../../../weixinpl/common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');
require('../../../../../weixinpl/auth_user.php');
require('../../../../../weixinpl/common/utility_4m.php');
$head=6;
_mysql_query("SET NAMES UTF8");
$new_baseurl = Protocol.$http_host; 

$search_keyword="";   
if(!empty($_GET["search_keyword"])){
	$search_keyword = $_GET["search_keyword"];
}
$pagenum = 1;					
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * 20;
$end =20;

$supplier_id_en=$_GET["supplier_id"];
$supplier_id =passport_decrypt($supplier_id_en);

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme;?>.css">
<link rel="stylesheet" type="text/css" href="css/per-style.css">
<script type="text/javascript" src="js/jquery-1.12.1.min.js"></script>
<script type="text/javascript" src="js/layer/layer.js"></script>
<style type="text/css">

</style>
</head>

<body>
<!--内容框架开始-->
<div style="overflow-x:hidden;width:368px; height:750px; background:url(images/background.png)  no-repeat top center; margin:0 auto;display:none;position:fixed;left:30%;top:0%;scrolling:no;z-index:99" id="show_preview">
<iframe style=" border:1px solid #ccc; margin-top:128px; margin-left:26px; overflow-x:hidden; overflow-y:auto;" id="iframe" frameBorder="no" height="521" width="317" vspace="0" src="../../../../common_shop/jiushop/index_custom.php" ></iframe>
</div>


<div class="WSY_content" id="WSY_content_height" style="z-index:1">
<!--微商城统计代码结束-->

<style type="text/css">
html{height:100%;overflow:hidden;}
body{height:100%;background-color:#f3f3f3;}
/*蓝色*/
.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#06a7e1;border:solid 1px #0b91c2;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#017ca9;cursor:pointer;}
.input_butn01 input{width:268px;}
.leftA01 .leftA01_dl dd .tj{background:#07a7e1;border:solid 1px #0b91c2;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#0b91c2;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#06a7e1;color:#fff;}
</style>
       <!--列表内容大框开始-->
	<div class="WSY_columnbox">

         
    <!--首页设置代码开始-->
<div class="main">
	

	

	<a href="custom.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&supplier_id=<?php echo $supplier_id_en;?>&action=add"><button class="btn-green mt15 newadd diy_btn" >新建模块</button></a>
	<div class="search-box">
		<input class="search-text" type="text" placeholder="模块名称" value="<?php echo $search_keyword;?>" id="search_keyword">
		<button onClick="search();" class="diy_btn">搜索</button>
		
	</div>
	<div class="content-box">
		<table>
			<colgroup>
				<col width="2%">
				<col width="30%">
				<col width="13%">
				<col width="25%">
			</colgroup>
			<thead>
				<tr>
					<th style="padding:10px 8px;"><input type="checkbox" id="checkAllChange" /></th>
					<th>模块名称</th>
					<th>创建时间</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$query_temid="select id,content,isused,creatime,name from weixin_commonshop_supply_diy_template where isvalid=true and supplier_id=".$supplier_id." and customer_id=".$customer_id;
					if($search_keyword){
						$query_temid = $query_temid."  and name like '%".$search_keyword."%'";
					}
					$query_count = $query_temid;
					$query_temid = $query_temid." order by id asc limit ".$start.",".$end."";
					
					$result_query_temid=_mysql_query($query_temid) or die ('query_temid faild' .mysql_error());
					$result_count=_mysql_query($query_count) or die ('query_count faild' .mysql_error());
					
					$wcount =0;
					$page=0;
					$wcount = mysql_num_rows($result_count);
					
					$page=ceil($wcount/$end);
					while($row=mysql_fetch_object($result_query_temid)){
						$diy_temid=$row->id;
						$content=$row->content;
						$isused=$row->isused;
						$creatime=$row->creatime;
						$name=$row->name;
					
				?>
				<tr>
					<td><input type="checkbox" class="temid" value="<?php echo $diy_temid;?>"></td>
					<td><span ondblclick="ShowElement(this)" class="custom_name"><?php echo $name;?></span></td>
					<td><?php echo $creatime;?></td>
					<td>
					<!--	<button class="btn-white preview" onclick="preview(<?php echo $diy_temid;?>)"  value="<?php echo $diy_temid;?>">预览</button>-->
						<a href="custom.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&supplier_id=<?php echo $supplier_id_en;?>&temid=<?php echo $diy_temid;?>&action=edit"><button class="btn-white">编辑</button></a>
						<button class="btn-white" onclick="temp_delete(<?php echo $diy_temid;?>)">删除</button>
						<?php if($isused){?>
						<button class="btn-green diy_btn" onclick="temp_cancel(<?php echo $diy_temid;?>)">模板已启用</button>					
						<?php }else{?>
						<button class="btn-white " onclick="temp_check(<?php echo $diy_temid;?>)">启用此模板</button>
						<?php }?>
					</td>
				</tr>
				<?php 
					}  //循环结束
				?>
			</tbody>
				
		</table>
	</div>
	<div class="btn-box">
		<!--<button class="btn-white" id="checkAll">全选</button>
		<button class="btn-white" id="cancleAll">取消全选</button>
		<button class="btn-white">删除</button>
		-->
		<input type="button" id="checkAll" class="select selectshort diy_btn" value="全选" />
		<input type="button" id="reverse" class="select selectshort diy_btn" value="反选" />
				<input type="button" id="removeAll" class="select selectlong diy_btn" value="取消全部" /> 
		<input type="button" value="批量删除" id="delAll" class="select selectlong diy_btn">
		
		
	</div>
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

<script>
var supplier_id=<?php echo $supplier_id;?>;
var customer_id=<?php echo $customer_id;?>;
$(function(){
	$('.custom_name').change(function(){//修改名称
		var name=$(this).children().val();
		var id=$(this).parent().prev().find('.temid').val();
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
				data : {"option" : option,"temidarr" : temidarr,"customer_id" : customer_id,"supplier_id" : supplier_id},
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
		data : {"option" : option,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_temid" : diy_temid,"name" : name},
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
		data : {"option" : option,"diy_temid" : diy_temid,"supplier_id" : supplier_id},
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
	var option="temp_check";
	$.ajax({  
		type : "POST",  
		url : "save_control.php",
		data : {"option" : option,"diy_temid" : diy_temid,"supplier_id" : supplier_id},
		dataType: "json",		
		success : function(result) {
			if(result.code=="1"){
				window.location.reload(); 
			}
		}
		
	});
	
}

function temp_cancel(diy_temid){ //停用模板
	var option="temp_cancel";
	$.ajax({  
		type : "POST",  
		url : "save_control.php",
		data : {"option" : option,"diy_temid" : diy_temid,"supplier_id" : supplier_id},
		dataType: "json",		
		success : function(result) {
			if(result.code=="1"){
				window.location.reload(); 
			}
		}
		
	});
	
}
var pagenum = <?php echo $pagenum ?>;
var page = <?php echo $page ?>;
function prePage(){
	pagenum--;
	document.location= "custom_control.php?pagenum="+pagenum+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&supplier_id=<?php echo $supplier_id_en;?>";
}
  
function nextPage(){
	pagenum++;
	document.location= "custom_control.php?pagenum="+pagenum+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&supplier_id=<?php echo $supplier_id_en;?>";
}
function search(){
	pagenum = 1;
	var search_keyword = document.getElementById("search_keyword").value;
	document.location= "custom_control.php?pagenum="+pagenum
	+"&search_keyword="+search_keyword+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&supplier_id=<?php echo $supplier_id_en;?>";

}
function gopage(v){
	var a=$(v);
	if(a.hasClass('one')){
		return false;
	}else{
		document.location= "custom_control.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&supplier_id=<?php echo $supplier_id_en;?>&pagenum="+a.val();
	}
}
function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		document.location= "custom_control.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&supplier_id=<?php echo $supplier_id_en;?>&pagenum="+a;
	}
}
function preview(temid){ 

	
	$("#show_preview").css("display","block");
	$("#WSY_content_height").css({ filter: "alpha(opacity=50)", "-moz-opacity": "0.5" , "-khtml-opacity": "0.5", opacity: "0.5"});
	document.getElementById("iframe").src='../../common_shop/jiushop/index_custom.php?temid='+temid; 
	setTimeout(funcx, 1000);
	
}
function funcx(){
	$('.WSY_columnbox').click(function(){
        $("#show_preview").hide();
		window.location.reload(); 
    });
}

</script>
<!--选择链接的JS结束-->
</body>
</html>  
<?php 

mysql_close($link);
?>