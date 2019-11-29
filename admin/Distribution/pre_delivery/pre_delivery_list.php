<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");

$supply_id = -1;	//供应商id
$supply_id_en = '';
if( !empty($_GET['supply_id']) && !empty($_SESSION['supplier_Acount']) && empty($_GET['customer_id']) ){
	$supply_id = $_SESSION['supplier_Acount'];
	$supply_id_en = $_GET['supply_id'];
} else if( empty($_GET['customer_id']) ) {
	die('操作异常！');
}

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
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme;?>.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../Base/personalization/custom/css/per-style.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/V2_1/layer.js"></script>

</head>

<body>
<!--内容框架开始-->
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
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#06a7e1;color:#fff;}

body{background: #e4e4e4;}
a:hover{text-decoration: none;}   
.button_blue{margin-left: 17px;font-size: 14px;display: block;line-height: 30px;background-color: #06a7e1;padding-left: 15px;padding-right: 15px;border-radius: 3px 3px 3px 3px;margin-top:15px;color: #fff;}
.button_blue:hover{background:#0e98c9;}
.WSY_righticon .WSY_inputicon input{margin-top:0px}
</style>
       <!--列表内容大框开始-->
	<div class="WSY_columnbox">
    	<!--列表头部切换开始-->
    	<?php
			$header = 0;
			// include("../../../../weixinpl/back_newshops/Distribution/pre_delivery/head.php");
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Distribution/pre_delivery/head.php");
		?>
        <!--列表头部切换结束-->
		
    <!--首页设置代码开始-->
<div class="main">
	<?php if($supply_id>0){?>
	<a style="margin-top: 0px;float: left;" href="add_pre_delivery.php?supply_id=<?php echo $supply_id_en;?>"><button class="btn-green mt15 newadd diy_btn" >设置时间安排</button></a> 
	<?php }else{?>
	<a style="margin-top: 0px;float: left;" href="add_pre_delivery.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>"><button class="btn-green mt15 newadd diy_btn" >设置时间安排</button></a> 
	<?php }?>
	
	<div style="margin-top: 23px;margin-left: 20px;float: left;">
		<input type="checkbox" id="all-checked" /><label for="all-checked">全选</label>
	</div>
	
	<a style="margin-top: 0px;float: left;margin-left: 15px;" href="javascript:;"><button class="btn-green mt15 newadd diy_btn del-btn" >删除</button></a>
	
	<div style="float: right;" class="search-box">
		<input style="line-height: 28px;" class="search-text" type="text" placeholder="请输入关键字" value="<?php echo $search_keyword;?>" id="search_keyword">
		<button onClick="search();" class="diy_btn">搜索</button>	
	</div>
	<div style="clear: both;"></div>
	
	
	<div class="content-box">
		<table>
			<colgroup>
				<col width="5%">
				<col width="10%">
				<col width="20%">
				<col width="7%">
				<col width="13%">
				<col width="10%">
			</colgroup>
			<thead class="WSY_table_header">
				<tr>
					<th>勾选</th>
					<th>序号</th>
					<th>活动名称</th>
					<th>ID</th>
					<th>已选产品总数</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = -1;
					$delivery_name = '';
				    $query = "SELECT id,delivery_name FROM weixin_commonshop_pre_delivery WHERE customer_id=".$customer_id." AND isvalid=true";
					$query_count = "SELECT count(1) as dcount FROM weixin_commonshop_pre_delivery WHERE customer_id=".$customer_id." AND isvalid=true";
					if( $supply_id > 0 ){
						$query .= " AND supply_id=".$supply_id;
						$query_count .= " AND supply_id=".$supply_id;
					} else {
						$query .= " AND supply_id=-1";
						$query_count .= " AND supply_id=-1";
					}
					if( $search_keyword != '' ){
						$query .= " AND delivery_name like '%".$search_keyword."%'";
						$query_count .= " AND delivery_name like '%".$search_keyword."%'";
					}
					$dcount = 0;
					$result_count = _mysql_query($query_count) or die('Query_count failed:'.mysql_error());
					while( $row_count = mysql_fetch_object($result_count) ){
						$dcount = $row_count -> dcount;
					}
					$page = ceil($dcount/$end);
					$query .= " ORDER BY id DESC LIMIT ".$start.",".$end;
					$result = _mysql_query($query) or die('Query failed:'.mysql_error());
					while( $row = mysql_fetch_object($result) ){
						$id = $row -> id;
						$delivery_name = $row -> delivery_name;
						
						$pcount = 0;	//关联产品数量
						$query_pcount = "SELECT COUNT(1) AS pcount 
										FROM weixin_commonshop_pre_delivery_product_relation AS wcpdrr
										INNER JOIN weixin_commonshop_products AS wcp ON wcpdrr.pid=wcp.id
										WHERE wcpdrr.delivery_id=".$id." AND wcpdrr.customer_id=".$customer_id." AND wcpdrr.isvalid=true AND wcp.isvalid=true AND wcp.isout=false AND wcp.isout_status=true AND wcp.is_QR=false AND wcp.is_virtual=false";
						$result_pcount = _mysql_query($query_pcount) or die('Query_pcount failed:'.mysql_error());
						while( $row_pcount = mysql_fetch_object($result_pcount) ){
							$pcount = $row_pcount -> pcount;
						}
						
				?>
				<tr >
					<td><input type="checkbox" class="delivery_id" value="<?php echo $id;?>"></td>
					<td><?php echo $id;?></td>
					<td><?php echo $delivery_name;?></td>
					<td><?php echo $id;?></td>
					<td><?php echo $pcount;?></td>
					
					<td style="border-right: none;">
					<?php
						if( $supply_id > 0 ){
					?>
						<a style="margin-top: 0px;display: inline-block;" href="add_pre_delivery.php?supply_id=<?php echo $supply_id_en;?>&keyid=<?php echo $id;?>"><button class="btn-green mt15 newadd diy_btn" style="margin: 0;" >查看详情</button></a>
					<?php
						} else {
					?>
						<a style="margin-top: 0px;display: inline-block;" href="add_pre_delivery.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&keyid=<?php echo $id;?>"><button class="btn-green mt15 newadd diy_btn" style="margin: 0;" >查看详情</button></a>
					<?php
						}
					?>
					</td>
				</tr>
				<?php 
					}  //循环结束
				?>
			</tbody>
				
		</table>
	</div>
	<!--翻页开始-->
		<div class="WSY_page">
			<ul class="WSY_pageleft" style="width:100%;margin-top:5px;">
				<?php 	if($dcount>0){ 
					for($i=1;$i<=$page;$i++){
				?>
					<li <?php if($i==$pagenum){ ?> class="one" <?php } ?> onClick="gopage(this)" value="<?php echo $i; ?>"><?php echo $i; ?></li>
				<?php }} ?>	
			<?php if($dcount>0){ ?>
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
$(function(){
	$("#all-checked").click(function() { // 全选/取消全部 
		if (this.checked == true) { 
			$(".delivery_id").each(function() { 
				this.checked = true; 
			}); 
		} else { 
			$(".delivery_id").each(function() { 
				this.checked = false; 
			}); 
		} 
	});
	
	$('.del-btn').click(function(){
		
	});
}); 	

var pagenum = <?php echo $pagenum ?>;
var page = '<?php echo $page ?>';
var supply_id = '<?php echo $supply_id ?>';
var supply_id_en = '<?php echo $supply_id_en ?>';
function prePage(){
	pagenum--;
	// document.location= "pre_delivery_list.php?pagenum="+pagenum+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
	var url = "pre_delivery_list.php?pagenum="+pagenum;
	if(supply_id_en != ''){
		url = url + "&supply_id="+supply_id_en;
	}else{
		url = url + "&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
	}
	document.location= url;
}
  
function nextPage(){
	pagenum++;
	// document.location= "pre_delivery_list.php?pagenum="+pagenum+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
	var url = "pre_delivery_list.php?pagenum="+pagenum;
	if(supply_id_en != ''){
		url = url + "&supply_id="+supply_id_en;
	}else{
		url = url + "&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
	}
	document.location= url;
}
function search(){
	pagenum = 1;
	var search_keyword = document.getElementById("search_keyword").value;
	// document.location= "pre_delivery_list.php?pagenum="+pagenum
	// +"&search_keyword="+search_keyword+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
	var url = "pre_delivery_list.php?pagenum="+pagenum+"&search_keyword="+search_keyword;
	if(supply_id_en != ''){
		url = url + "&supply_id="+supply_id_en;
	}else{
		url = url + "&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
	}
	document.location= url;

}
function gopage(v){
	var a=$(v);
	if(a.hasClass('one')){
		return false;
	}else{
		// document.location= "pre_delivery_list.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a.val();
		var url = "pre_delivery_list.php?pagenum="+a.val();
		if(supply_id_en != ''){
			url = url + "&supply_id="+supply_id_en;
		}else{
			url = url + "&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
		}
		document.location= url;
	}
}
function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		// document.location= "pre_delivery_list.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a;
		var url = "pre_delivery_list.php?pagenum="+a;
		if(supply_id_en != ''){
			url = url + "&supply_id="+supply_id_en;
		}else{
			url = url + "&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
		}
		document.location= url;
	}
}

$('.del-btn').click(function(){
	if( confirm('确定删除所选的设置吗？') ){
		var deliveryIdCheckbox = $('.delivery_id:checked'),
			checkedId = '';
		
		deliveryIdCheckbox.each(function(i){
			checkedId += $(this).val()+',';
		});
		checkedId = checkedId.slice(0,-1);
		if( checkedId != null && checkedId != '' ){
			var url = "add_pre_delivery.php?op=del&checkedId="+checkedId;
			if(supply_id_en != ''){
				url = url + "&supply_id="+supply_id_en;
			}else{
				url = url + "&customer_id=<?php echo passport_encrypt((string)$customer_id);?>";
			}
			document.location= url;
		}
	}
	
});
</script>
<!--选择链接的JS结束-->
</body>
</html>  
<?php 

mysql_close($link);
?>