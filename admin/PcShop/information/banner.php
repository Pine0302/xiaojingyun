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

require_once ('shoproom.php');
$pc = new Pcshop ();
$customer_id_en = $pc->customer_id_en;
$sortinfo = $pc->nav_info();
$head=0;//头部文件

//导航删除
$navdel_url = $_SERVER['DOCUMENT_ROOT'].'/weixinpl/back_newshops/PcShop/information/interfaceroom.php?action=delnav&customer_id='.$pc->customer_id_en;
//$banner_url = $pc->http.'/weixinpl/back_newshops/PcShop/information/banner.php?customer_id='.$pc->customer_id_en;

?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<title>导航列表</title>
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
.logo_pic>img{
	width:60px;
	height:60px;
}
.logo_pic{
	padding:3px !important;
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
			include ($_SERVER ['DOCUMENT_ROOT'] . "/mshop/admin/PcShop/information/basic_head.php");
			?> 
			<!--列表头部切换结束-->
  			<!--自定义代码开始-->
            <div class="WSY_data" id="div_menucon">
                <!--列表按钮开始-->
                <div class="WSY_list">
                    <li class="WSY_left"><a>导航列表</a></li>
                    <ul class="WSY_righticon" style="width:100px;">
                        <li><a href="banner_add.php?op=add&customer_id=<?php echo $customer_id_en;?>">添加</a></li>
                        <!--<li class="WSY_inputicon"><input type="button" value="批量删除"></li>-->
                    </ul>
                </div>
                <!--列表按钮开始-->
                <table width="97%" class="WSY_table" id="custom">
                    <thead class="WSY_table_header">
                       <!--  <th width="3%"><input id="s"
                            onclick="$(this).attr(&#39;checked&#39;)?checkAll():uncheckAll()"
                            type="checkbox" name="sex"></th> -->
                        <th width="8%">ID</th>
                        <th width="15%">LOGO</th>
                        <!--<th width="12%">是否有子菜单</th>-->
                        <th width="40%">链接</th>
                        <th width="15%">操作</th>
                    </thead>
                    
                    <?php 
                    
                      if(empty($sortinfo['errcode'])){
                          foreach($sortinfo['data'] as $vq){
                    ?>
                        <tr>
                           <!--  <td width="3%"><input type="checkbox" name="code_Value" value="1"></td> -->
                            <td width="12%" class="xuhao"><?php echo $vq['id']; ?></td>
                            <td width="30%" class="logo_pic"><img src="<?php echo $vq['logo']; ?>"/></td>
                            <td width="25%"><?php echo $vq['link']; ?></td>
                            <td class="WSY_t4" width="15%">
								<a href="banner_add.php?op=update&id=<?php echo $vq['id'];?>&customer_id=<?php print $customer_id_en;?>" title="编辑">
								   <img src="../../../common/images_V6.0/operating_icon/icon05.png">
								</a>
							    <button title="删除" style="background:none;border:none;margin-right:8px;" class="btn-white" onclick="temp_delete(<?php echo $vq['id'];?>)">
							       <img width="18" height="18" src="../../../common/images_V6.0/operating_icon/icon04.png"/>
							    </button>
                            </td>
                        </tr>
                        
                    <?php }} ?>

				</table>  
                
                <!--表格结束-->

            </div>
            <!--自定义菜单代码结束-->            
            
		    <!--翻页开始-->
			<div class="WSY_page">
				<ul class="WSY_pageleft" style="width:100%;margin-top:5px;">
					<?php if(!$sortinfo['errcode'])echo $sortinfo['strPage'];?>
					<form class="WSY_searchbox">
						<input class="WSY_page_search" name="WSY_jump_page" id="WSY_jump_page" value="">
						<input class="WSY_jump" type="button" value="跳转" onclick="jumppage()" style="border:none">
					</form>
				</ul>
			</div>
		    <!--翻页结束-->
		</div>
        <!--列表内容大框开始-->
    </div>    
	<!--内容框架开始-->
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/js/layer/V2_1/layer.js"></script>
<?php mysql_close($link); ?>

<script type="text/javascript"> 

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
	    var delnav_url = "<?php echo $navdel_url;?>";
	    delnav_url += '&del_id='+id;
		layer.confirm('确定要删除吗？', {
			title: false,
			skin:'red-skin',
			shift:6,
  			btn: ['删除','取消'] //按钮
		}, function(){
			$.ajax({  
				url : delnav_url,
				data : {'op':'del'},
				type : "POST", 
				dataType: "json",		
				success : function(result){
					console.log(result);
					if(result.errcode){
						alert(result.data);
					} window.location.reload(); 
				}
			});
		});
}	
/*var pagenum = <?php echo $pagenum ?>;
var page = <?php echo $page ?>;*/
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
		document.location.href = "help_center.php?pagenum="+a.val();
	}
}
function jumppage(){
	var currentPage = <?php echo $sortinfo['currentPage'];?>;
	var pageNums = <?php echo $sortinfo['pageNums'];?>;
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==currentPage) || (a>pageNums) || isNaN(a)){
		return false;
	}else{
		document.location = '?page='+a+'&customer_id='+"<?php echo $pc->customer_id_en;?>";
	}
}

function pagehref(obj){
	var attr_page = $(obj).attr('page');
	var attr_condition = $(obj).attr('condition');
	location.href = '?page='+attr_page+attr_condition;
}

</script>
<!--选择链接的JS结束-->
</body>
</html>