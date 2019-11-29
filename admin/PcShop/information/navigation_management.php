<?php
  header("Content-type: text/html; charset=utf-8"); 
  require('../../../../weixinpl/config.php');
  $customer_id = passport_decrypt($customer_id);
  require('../../../../weixinpl/back_init.php');
  
  $link =    mysql_connect(DB_HOST,DB_USER, DB_PWD);
  mysql_select_db(DB_NAME) or die('Could not select database');
  _mysql_query("SET NAMES UTF8");
  require('../../../../weixinpl/proxy_info.php');
require('../../../../weixinpl/common/utility_interface.php');
$head = 1;
$search_keyword="";   
if(!empty($_GET["search_keyword"])){
	$search_keyword = $_GET["search_keyword"];
};

$pagenum = 1;					
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$end =20;
$start = ($pagenum-1) * $end;

?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<title>入驻文档</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style type="text/css">
.table-bordered {
border: 1px solid #ddd;
border-collapse: separate;
-moz-border-radius: 4px;
border-radius: 4px;
}
menu1.phpmedia="all"
.table {
width: 100%;
margin-bottom: 20px;
}
menu1.phpmedia="all"
.tb_class {
font-size: 12px;
text-align: center;
margin: 0 auto;
}
.WSY_table_add{
margin-left: 0;
margin-top: 0;
width: 100%;
}
table td {
    height: 40px;
    font-size: 12px;
    color: #323232;
    padding: 0px 1em;
    text-align: center;
    border: 1px solid #D8D8D8;
}
</style>
</head>
<body>
<!--内容框架开始-->
	<div class="WSY_content">
        <!--列表内容大框开始-->
        <div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/PcShop/information/basic_head.php");
			?>
			<!--列表头部切换结束-->
  			<!--自定义代码开始-->
            <div class="WSY_data" id="div_menucon">
                <!--列表按钮开始-->
                <div class="WSY_list">
                    <li class="WSY_left"><a>入驻文档</a></li>
                    <ul class="WSY_righticon">
                        <li><a href="document_management_add.php?action=add">添加文档</a></li>
                        <!--<li class="WSY_inputicon"><input type="button" value="批量删除"></li>-->
                    </ul>
                </div>
                <!--列表按钮开始-->
                <table width="97%" class="WSY_table" id="custom">
                    <thead class="WSY_table_header">
                       <!--  <th width="3%"><input id="s"
                            onclick="$(this).attr(&#39;checked&#39;)?checkAll():uncheckAll()"
                            type="checkbox" name="sex"></th> -->
                        <th width="12%">ID</th>
                        <th width="30%">入驻文档标题</th>
                        <!--<th width="12%">是否有子菜单</th>-->
                        <th width="25%">文档内容</th>
                        <th width="5%">操作</th>
                    </thead>
                 
				<?php
					

					$query_con="SELECT id,title,content FROM pcshop_merchants_settled_data where isvalid=true and customer_id=".$customer_id." order by sort asc";
					$query_count = $query_con;
					$query_con = $query_con." /*group by is_default*/  limit ".$start.",".$end."";
					$result_query_con=_mysql_query($query_con) or die ('query_con faild' .mysql_error());
					$result_count=_mysql_query($query_count) or die ('query_count faild' .mysql_error());

					//分页
					$wcount =0;
					$page   =0;
					$wcount = mysql_num_rows($result_count);
					$page=ceil($wcount/$end);
					
					$id       = -1; //主键
					$title        = ""; //分类名称
					$level       =  0; //级别
					$content  = "";//有无子菜单
					while($row=mysql_fetch_object($result_query_con)){
						$id  = $row->id;
						$title     = $row->title;
						$content     = $row->content;
				?>

                        <tr>
                           <!--  <td width="3%"><input type="checkbox" name="code_Value" value="1"></td> -->
                            <td width="12%" class="xuhao"><?php echo $id ?></td>
                            <td width="30%"><?php echo $title; ?></td>
                            <td width="25%"><?php echo $content; ?></td>
                            <td class="WSY_t4" width="15%">
							<a href="document_management_add.php?op=update&id=<?php echo $id ?>&name=<?php echo $name ?>&is_submenu=<?php echo $is_submenu ?>&sort=<?php echo $sort ?>" title="编辑"><img src="../../../common/images_V6.0/operating_icon/icon05.png"></a>
						 
							<button title="删除" style="background: none;border: none;margin-right: 8px;" class="btn-white" onclick="temp_delete(<?php echo $id;?>)"><img width="18" height="18" src="../../../common/images_V6.0/operating_icon/icon04.png"/></button>

                              
                            </td>
                        </tr>

                  <?php } ?>
				</table>  
                
                <!--表格结束-->

            </div>
            <!--自定义菜单代码结束-->            
            
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
        <!--列表内容大框开始-->
    </div>    
	<!--内容框架开始-->

<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/V2_1/layer.js"></script>
<?php mysql_close($link); ?>

<script type="text/javascript" > 

function saveXuHao(){
var result="";
$("td .xuhao").each(function(){
 result=result+","+$(this).text();
 }); 
 
 alert(result);
}
</script> 
<script>
function temp_delete(id){ 
		layer.confirm('确定要删除吗？', {
			title: false,
			skin:'red-skin',
			shift:6,
  			btn: ['删除','取消'] //按钮
		}, function(){
			$.ajax({  
				type : "POST",  
				url : "document_management.class.php",
				data : {"id" : id,'op':'del'},
				dataType: "json",		
				success : function(result) {
					if(result.code=="1"){
						window.location.reload(); 
					}
				}
				
			});
		});
}	
var pagenum = <?php echo $pagenum ?>;
var page = <?php echo $page ?>;
function prePage(){
	pagenum--;
	document.location= "help_center.php?pagenum="+pagenum;
}
  
function nextPage(){
	pagenum++;
	document.location= "help_center.php?pagenum="+pagenum;
}

function gopage(v){
	var a=$(v);
	if(a.hasClass('one')){
		return false;
	}else{
		document.location= "help_center.php?pagenum="+a.val();
	}
}
function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		document.location= "help_center.php?pagenum="+a;
	}
}

</script>
<!--选择链接的JS结束-->
</body>
</html>