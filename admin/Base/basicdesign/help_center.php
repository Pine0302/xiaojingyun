<?php
  	header("Content-type: text/html; charset=utf-8"); 
  	require('../../../../weixinpl/config.php');
  	require('../../../../weixinpl/back_init.php');
  	require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
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


?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<title>自定义菜单</title>
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
.contentImg img{
width: 65px!important;
}
</style>
</head>
<body>
<!--内容框架开始-->
	<div class="WSY_content">
        <!--列表内容大框开始-->
        <div class="WSY_columnbox">

  			<!--自定义代码开始-->
            <div class="WSY_data" id="div_menucon">
                <!--列表按钮开始-->
                <div class="WSY_list">
                    <li class="WSY_left"><a>帮助中心</a></li>
                    <ul class="WSY_righticon">
                        <li><a href="help_center_add.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&action=add">添加分类</a></li>
                        <!--<li class="WSY_inputicon"><input type="button" value="批量删除"></li>-->
                    </ul>
                </div>
                <!--列表按钮开始-->
                <table width="97%" class="WSY_table" id="custom">
                    <thead class="WSY_table_header">
                        <th width="3%"><input id="s"
                            onclick="$(this).attr(&#39;checked&#39;)?checkAll():uncheckAll()"
                            type="checkbox" name="sex"></th>
                        <th width="3%">ID</th>
                        <th width="20%">分类名</th>
                        <th width="12%">是否有子菜单</th>
                        <th width="44%">文章内容</th>
                        <th width="15%">操作</th>
                    </thead>
				</table>

                <table width="97%" class="WSY_table" cellspacing="1" cellpadding="1" border="0"  class="tb_class table table-bordered"  style="margin-bottom:5px;margin-top:0;" bgcolor="#fff">
                 
				<?php
					

					$query_con="SELECT id,name,superior_id,sort,is_submenu,content FROM pc_help_center_t where isvalid=true and customer_id=".$customer_id." and level='1'   order by sort asc ";
                    if($search_keyword){
						$query_con = $query_con."  and title like '%".$search_keyword."%'";
					}
					$query_count = $query_con;
					$query_con = $query_con." /*group by is_default*/  limit ".$start.",".$end."";
					$result_query_con=_mysql_query($query_con) or die ('query_con faild' .mysql_error());
					$result_count=_mysql_query($query_count) or die ('query_count faild' .mysql_error());
//echo $query_con;
					//分页
					$wcount =0;
					$page   =0;
					$wcount = mysql_num_rows($result_count);
					$page=ceil($wcount/$end);
					
					$id       = -1; //主键
					$name        = ""; //分类名称
					$level       =  0; //级别
					$is_submenu  = "";//有无子菜单
					$superior_id = "";//上级编号
					$sort        = -1;//优先级
					while($row=mysql_fetch_object($result_query_con)){
						$id           = $row->id;
						$name         = $row->name;
						$superior_id  = $row->superior_id;
						$sort         = $row->sort;
						$is_submenu   = $row->is_submenu;
						$p_content 	  = $row->content;
				?>
                
                  
					<tr><td>
					<table width="97%" class="WSY_table WSY_table_add" id="custom">
                        <tr>
                            <td width="3%"><input type="checkbox" name="code_Value" value="1"></td>
                            <td width="3%" class="xuhao"><?php echo $id ?></td>
                            <td width="20%"><?php echo $name; ?></td>
                            <td width="12%"><?php if($is_submenu==1){echo "有子菜单";}else{echo "无子菜单";} ; ?></td>
                            <td width="44%"  class="contentImg"><?php echo $p_content;?></td>
                            <td class="WSY_t4" width="15%">
                            	<?php if($is_submenu==1){ ?>
                                 <a href="article_management_add.php?superior_id=<?php echo $id ?>" title="添加二级标题"><img src="../../../common/images_V6.0/operating_icon/icon34.png"></a>
                                <?php } ?>    
                                <a href="help_center_add.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&op=update&id=<?php echo $id ?>&name=<?php echo $name ?>&is_submenu=<?php echo $is_submenu ?>&sort=<?php echo $sort ?>" title="编辑"><img src="../../../common/images_V6.0/operating_icon/icon05.png"></a>
                                 
                                <button title="删除" style="background: none;border: none;margin-left: -5px;margin-top: -1px;" class="btn-white" onclick="temp_delete(<?php echo $id;?>)"><img width="18" height="18" src="../../../common/images_V6.0/operating_icon/icon04.png"/></button>

                              
                            </td>
                        </tr>
                   
                    <!--二级菜单代码开始-->
					
				<?php
					

					$query_con2="SELECT id,name,sort,is_submenu,content FROM pc_help_center_t where isvalid=true and level='2' and superior_id=".$id." and customer_id=".$customer_id." order by sort asc";
					 // echo $query_con2;
					$result_query_con2=_mysql_query($query_con2) or die ('query_con faild' .mysql_error());

					
					$id       = -1; //主键
					$name_sub        = ""; //二级分类名称
					$level       =  0; //级别
					$is_submenu  = "";//有无子菜单
					$superior_id = "";//上级编号
					$sort        = -1;//优先级
					while($row=mysql_fetch_object($result_query_con2)){
						$id_sub   = $row->id;
						$name_sub = $row->name;
						$sort     = $row->sort;
						$content  = $row->content;
						$is_submenu = $row->is_submenu;		

						preg_match("/(<body>)(.*?)(<\/body>)/is",$content,$match); //截取body部分
				?>
                        <tr>
                            <td><input type="checkbox" name="code_Value" value="1"></td>
                            <td class="xuhao"><?php echo $id_sub ?></td>
                            <td><li class="WSY_lefticon1"><?php echo $name."->".$name_sub; ?></li></td>
                            <td><?php if($is_submenu==1){echo "有子菜单";}else{echo "无子菜单";}?></td>
                            <td class="contentImg"><?php echo $match[2];?></td>
                            <td class="WSY_t4">
                            	<a href="article_management_add.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&op=update&id=<?php echo $id_sub ?>" title="编辑"><img src="../../../common/images_V6.0/operating_icon/icon05.png"></a> 
                                 <button title="删除" style="background: none;border: none;margin-left: -5px;margin-top: -1px;" class="btn-white" onclick="temp_delete(<?php echo $id_sub;?>)"><img width="18" height="18" src="../../../common/images_V6.0/operating_icon/icon04.png"/></button>
                             
                               
                            </td>
                        </tr>
					<?php } ?>
					</table>
					</td></tr>
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
				url : "help_center.class.php",
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
	document.location= "help_center.php?pagenum="+pagenum+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
}
  
function nextPage(){
	pagenum++;
	document.location= "help_center.php?pagenum="+pagenum+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";
}
function search(){
	pagenum = 1;
	var search_keyword = document.getElementById("search_keyword").value;
	document.location= "help_center.php?pagenum="+pagenum
	+"&search_keyword="+search_keyword+"&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>";

}
function gopage(v){
	var a=$(v);
	if(a.hasClass('one')){
		return false;
	}else{
		document.location= "help_center.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a.val();
	}
}
function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		document.location= "help_center.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a;
	}
}

</script>
<!--选择链接的JS结束-->
</body>
</html>