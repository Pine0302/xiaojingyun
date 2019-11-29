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
$head =1;
_mysql_query("SET NAMES UTF8");
$new_baseurl = Protocol.$http_host; 
$supply_id = -1;	//供应商id
if( !empty($_GET['supply_id']) && !empty($_SESSION['supplier_Acount']) && empty($_GET['customer_id']) ){
	$supply_id = $_SESSION['supplier_Acount'];
} else if( empty($_GET['customer_id']) ) {
	die('操作异常！');
}

/* $query = "select second_domain from customers where isvalid=true and id=".$customer_id." limit 1";
$result = _mysql_query($query) or die('L24 failed:'.mysql_error());
$second_domain = '';
while( $row = mysql_fetch_object($result) ){
	$second_domain = $row -> second_domain;
} */

function get_pcSecondDomain($customer_id){
	$result = array('code'=>0, 'domain'=>'');
	$filepath = '../../../../../shop/Application/Common/Conf/customerid4domain.php'; 
	if(is_file($filepath)){
		$content = file_get_contents($filepath);
		if(empty($content)){
			$result['code'] = -1;
		}else{ 
			$arr_ss = json_decode($content, true); 
			$arr_aa = array(); 
			foreach($arr_ss as $key => $val){
				foreach($val as $v_key => $v_val){   
					$arr_aa[$v_val] = $v_key;
				}
			}
			//判断是否已设置pc商城二级域名	
			if(empty($arr_aa[$customer_id])){
				$result['code'] = -2;
			}else{
				$result['domain'] = $arr_aa[$customer_id];
			}
		}
	}else{
		$result['code'] = -3;
	}
	return $result;
}

//根据customer_id获取对应的PC商城二级域名
$second_domain = get_pcSecondDomain($customer_id);

$search_keyword="";   
if(!empty($_GET["search_keyword"])){
	$search_keyword = $_GET["search_keyword"];
};
$pagenum = 1;					
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$template_type = -1;
if(!empty($template_type)){
	$template_type = $configutil->splash_new($_GET["template_type"]);
}
$start = ($pagenum-1) * 20;
$end =20;

//查一个一级分类用于一级列表页预览
/*$cate_id = -1;
$categorie_id = '';
$query_cate = "SELECT id,categorie_id FROM pcshop_home_categories WHERE isvalid=true AND customer_id=".$customer_id." AND is_open=true ORDER BY sort DESC LIMIT 1";
$result_cate = _mysql_query($query_cate) or die('Query_cate failed:'.mysql_error());
while( $row_cate = mysql_fetch_object($result_cate) ){
	$cate_id 		= $row_cate -> id;
	$categorie_id 	= $row_cate -> categorie_id;
}
$categorie_id = json_decode($categorie_id,true);
$cate_type = -1;
foreach( $categorie_id as $k => $v ){
	$cate_type = $v['id'];
	break;
}*/
//查一个一级分类用于一级列表页预览
$type_id = -1;
$query_type = "SELECT id FROM weixin_commonshop_types WHERE customer_id=".$customer_id." AND is_shelves=1 AND parent_id=-1 AND isvalid=true ORDER BY asort DESC LIMIT 1";
$result_type = _mysql_query($query_type) or die('Query_type failed:'.mysql_error());
while( $row_type = mysql_fetch_object($result_type) ){
	$type_id = $row_type -> id;
}


?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme;?>.css">
<link rel="stylesheet" type="text/css" href="css/per-style.css">
<script type="text/javascript" src="js/jquery-1.12.1.min.js"></script>
<script type="text/javascript" src="js/layer/layer.js"></script>

</head>

<body>
<!--内容框架开始-->
<div style="overflow-x:hidden;width:368px; height:750px; background:url(images/background.png)  no-repeat top center; margin:0 auto;display:none;position:fixed;left:30%;top:0%;scrolling:no;z-index:99" id="show_preview">
<iframe style=" border:1px solid #ccc; margin-top:128px; margin-left:26px; overflow-x:hidden; overflow-y:auto;" id="iframe" frameBorder="no" height="521" width="317" vspace="0" src="../../../../common_shop/jiushop/index_custom.php" ></iframe>
</div>


<div class="WSY_content" id="WSY_content_height" style="z-index:1">
<!--微商城统计代码结束-->

<style type="text/css">
/*蓝色*/
.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#06a7e1;border:solid 1px #0b91c2;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#017ca9;cursor:pointer;}
.input_butn01 input{width:268px;}
.leftA01 .leftA01_dl dd .tj{background:#07a7e1;border:solid 1px #0b91c2;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#0b91c2;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#06a7e1;color:#fff;};
.btn{width:20px;height:20px;margin-right:8px};
</style>
       <!--列表内容大框开始-->
	<div class="WSY_columnbox">
    	<!--列表头部切换开始-->
    	<?php
		// $head_type = 'pcshop_custom';
		if( $supply_id<0 && empty($_GET['supply_id'])){
			// include("../../../../../weixinpl/back_newshops/PcShop/Base/basic_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/PcShop/Base/basic_head.php");
		}
		?>
        <!--列表头部切换结束-->
         
    <!--首页设置代码开始-->
<div class="main">
	<?php if($supply_id>0){?>
	<a style="float:right" href="custom.php?supply_id=<?php echo $supply_id;?>&action=add"><button class="btn-green mt15 newadd diy_btn" >新建模块</button></a>  
	<?php }else{?>
	<a style="float:right" href="custom.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&action=add"><button class="btn-green mt15 newadd diy_btn" >新建模块</button></a>  
	<?php }?>
	<div style="float:right" class="search-box">
		<input class="search-text" type="text" placeholder="模块名称" value="<?php echo $search_keyword;?>" id="search_keyword">
		<button onClick="search();" class="diy_btn">搜索</button>
	</div>
	<div style="float:right;<?php if( $supply_id > 0 ){echo 'display:none;';}?>" class="search-box" >
		<span>模板类型：</span>
		<select id="template_type" class="search-text" onChange="search();">
			<option value="-1">不限</option>
			<option value="1" <?php if($template_type==1){echo 'selected';}?>>首页</option>
			<option value="2" <?php if($template_type==2){echo 'selected';}?>>一级列表页</option>
			<option value="3" <?php if($template_type==3){echo 'selected';}?>>活动页</option>
		</select>
	</div>
    <div style="clear:both"></div>
    <div style="color: red;font-size: 12px;">所有的预览一级列表页均为统一一个一级分类（排序最大的）,可通过改变排序进行替换</div>
	<div class="content-box">
		<table width="97%" class="WSY_table" id="WSY_t1">
              <thead class="WSY_table_header">
                <!--<th width="3%"><input id="s" onclick="$(this).attr(&#39;checked&#39;)?checkAll():uncheckAll()" type="checkbox"></th>-->
                <th width="17%">模块名称</th>
				<?php if($supply_id<0){?>
                <th width="20%">所属页面</th>
				<?php }?>
                <th width="20%">创建时间</th>
                <th width="20%">操作</th>
             </thead>        
			
				<?php
					$query_temid="select id,content,isused,createtime,name,custom_type from pcshop_diy_template where isvalid=true  and customer_id=".$customer_id." and supply_id=".$supply_id;
					if($search_keyword){
						$query_temid = $query_temid."  and name like '%".$search_keyword."%'";
					}
					if($template_type>0){
						$query_temid .= " and custom_type=".$template_type;
					}
					
					$query_count = $query_temid;
					$query_temid = $query_temid." order by isused desc,id desc limit ".$start.",".$end."";
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
						$createtime=$row->createtime;
						$name=$row->name;
						$custom_type=$row->custom_type;
						
						$custom_type_str = '';
						switch($custom_type){
							case 1:
								$custom_type_str = '首页';
								break;
							case 2:
								$custom_type_str = '一级列表页';
								break;
							case 3:
								$custom_type_str = '活动页';
								break;
						}
				?>
				<tr>
					<!--<td><input type="checkbox" class="temid" value="<?php echo $diy_temid;?>"></td>-->
					<td>
						<span ondblclick="ShowElement(this)" class="custom_name" data-temid="<?php echo $diy_temid;?>"><?php echo $name;?></span>
						<?php if($isused){?>
						<span style="color:red">(已启用)</span>
						<?php }?>
					</td>
					<?php if($supply_id<0){?>
					<td><?php echo $custom_type_str;?></td>
					<?php }?>
					<td><?php echo $createtime;?></td>
					<td style="border-right:none"> 
						<?php
							$preview_url = '';
							if( $supply_id < 1 && $second_domain['code']==0){ 
								if( $custom_type == 1){
									$preview_url = Protocol.$second_domain['domain']."/shop/index.php/Home/Index/index/tem_id/".$diy_temid;
								} else if( $custom_type == 2 ){
									$preview_url = Protocol.$second_domain['domain']."/shop/index.php/Home/Product/ListPage/sid/".$type_id;
								} else if( $custom_type == 3 ){
									$preview_url = Protocol.$second_domain['domain']."/shop/index.php/Home/Product/ActivityPage/tem_id/".$diy_temid;
								} 
							}
						?>
						<?php if( !empty($preview_url) ){?>
						<a href="<?php echo $preview_url;?>" target="_blank"><button title="预览" style="background:none;border:none;margin-right:8px" class="btn preview" onclick="preview(<?php echo $diy_temid;?>)"  value="<?php echo $diy_temid;?>"><img width="20" height="20" src="../../../../common/images_V6.0/operating_icon/icon73.png"></button></a>
						<?php }?>						
						<?php if($supply_id>0){?>
						<a style="margin-right:8px" href="custom.php?supply_id=<?php echo $supply_id;?>&temid=<?php echo $diy_temid;?>&action=edit"><button title="编辑" style="background:none;border:none" class="btn"><img width="20" height="20" src="../../../../common/images_V6.0/operating_icon/icon05.png"></button></a>
						<?php }else{?>
						<a style="margin-right:8px" href="custom.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&temid=<?php echo $diy_temid;?>&action=edit"><button title="编辑" style="background:none;border:none" class="btn"><img width="20" height="20" src="../../../../common/images_V6.0/operating_icon/icon05.png"></button></a>
						<?php }?>
						<button title="删除" style="background:none;border:none;margin-right:8px" class="btn" onclick="temp_delete(<?php echo $diy_temid;?>)"><img width="20" height="20" src="../../../../common/images_V6.0/operating_icon/icon04.png"></button>
						<?php if($custom_type!=3){?>
							<?php if($isused){?>
							<button title="模板已启用" style="background:none;border:none;margin-right:8px" class="btn diy_btn"><img width="20" height="20" src="../../../../common/images_V6.0/operating_icon/icon74.png"></button>					
							<?php }else{?>                        
							<button title="启用此模板" style="background:none;border:none;margin-right:8px" class="btn" onclick="temp_check(<?php echo $diy_temid;?>,<?php echo $supply_id;?>,<?php echo $custom_type?>)"><img width="20" height="20" src="../../../../common/images_V6.0/operating_icon/icon75.png"></button>
							<?php }?>  
						<?php }?>
					</td>
                    
				</tr>
				<?php 
					}  //循环结束
				?>
			
				
		</table>
	</div>
    <!--
	<div class="btn-box">
		<!--<button class="btn-white" id="checkAll">全选</button>
		<button class="btn-white" id="cancleAll">取消全选</button>
		<button class="btn-white">删除</button>
		
		<input type="button" id="checkAll" class="select selectshort diy_btn" value="全选" />
		<input type="button" id="reverse" class="select selectshort diy_btn" value="反选" />
				<input type="button" id="removeAll" class="select selectlong diy_btn" value="取消全部" /> 
		<input type="button" value="批量删除" id="delAll" class="select selectlong diy_btn">
		
		
	</div>
   -->
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
var supply_id = <?php echo $supply_id;?>	//供应商id

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
		}, function(index){
			var option="temp_delete";
			$.ajax({  
				type : "POST",  
				url : "save_control.php",
				data : {"option" : option,"diy_temid" : diy_temid},
				dataType: "json",		
				success : function(result) {
					if(result.code=="1"){
						window.location.reload(); 
					} else if(result.code=="0"){
						alert('不能删除首页模板');
					}
				}
				
			});
			layer.close(index);
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
function temp_check(diy_temid,supply_id,custom_type){ //选择模板
	var option="temp_check";
	$.ajax({  
		type : "POST",  
		url : "save_control.php",
		data : {"option" : option,"diy_temid" : diy_temid,"supply_id" : supply_id,"custom_type":custom_type},
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
		data : {"option" : option,"diy_temid" : diy_temid},
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
	var template_type = document.getElementById("template_type").value;
	var url = "custom_control.php?pagenum="+pagenum;
	if(supply_id>0){
		url = url + "&supply_id="+supply_id;
	}else{
		url = url + "&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&template_type="+template_type;
	}
	document.location= url;
}
  
function nextPage(){
	pagenum++;
	var template_type = document.getElementById("template_type").value;
	var url = "custom_control.php?pagenum="+pagenum;
	if(supply_id>0){
		url = url + "&supply_id="+supply_id;
	}else{
		url = url + "&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&template_type="+template_type;
	}
	document.location= url;
}
function search(){
	pagenum = 1;
	var search_keyword = document.getElementById("search_keyword").value;
	var template_type = document.getElementById("template_type").value;
	var url = "custom_control.php?pagenum="+pagenum+"&search_keyword="+search_keyword;
	if(supply_id>0){
		url = url + "&supply_id="+supply_id;
	}else{
		url = url + "&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&template_type="+template_type;
	}
	document.location= url;

}
function gopage(v){
	var a=$(v);
	if(a.hasClass('one')){
		return false;
	}else{
		var template_type = document.getElementById("template_type").value;
		var url = "custom_control.php?pagenum="+a.val();
		if(supply_id>0){
			url = url + "&supply_id="+supply_id;
		}else{
			url = url + "&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&template_type="+template_type;
		}
		document.location= url;
	}
}
function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		var template_type = document.getElementById("template_type").value;
		var url = "custom_control.php?pagenum="+a;
		if(supply_id>0){
			url = url + "&supply_id="+supply_id;
		}else{
			url = url + "&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&template_type="+template_type;
		}
		document.location= url;
	}
}
function preview(temid){ 

	
	/*$("#show_preview").css("display","block");
	$("#WSY_content_height").css({ filter: "alpha(opacity=50)", "-moz-opacity": "0.5" , "-khtml-opacity": "0.5", opacity: "0.5"});
	document.getElementById("iframe").src='../../../../common_shop/jiushop/index_custom.php?temid='+temid; 
	setTimeout(funcx, 1000);*/ 
	
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