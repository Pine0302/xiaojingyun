<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

_mysql_query("SET NAMES UTF8");
require('../../../../../weixinpl/proxy_info.php');
require('../../../../../weixinpl/auth_user.php');
$head = 4;
$pagenum = 1;

if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * 20;
$end = 20;

$query = 'select count(1) as wcount from pcshop_package_banners where isvalid=true and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$wcount =0;
$page=0;
while ($row = mysql_fetch_object($result)) {
	$wcount =  $row->wcount ;
}			
$page=ceil($wcount/$end);

$query = 'select id,banner_imgurl,banner_url,sort from pcshop_package_banners where isvalid=true and customer_id='.$customer_id." order by sort desc,id desc limit ".$start.",".$end;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
?>
<?php 
  require_once ('../shoproom.php');
  $pc = new Pcshop ();
  $theme = $pc->get_theme();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>导航管理</title>
<link rel="stylesheet" type="text/css" href="../../../../../weixinpl/common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../../weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../../weixinpl/common/css/inside.css" media="all">
<script type="text/javascript" src="../../../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>

</head>

<body>
	<!--内容框架开始-->
	<div class="WSY_content">

		<!--列表内容大框开始-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php 
          //include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/PcShop/Base/basic_head.php"); 
          include ($_SERVER ['DOCUMENT_ROOT'] . "/mshop/admin/PcShop/information/basic_head.php");
      ?>
			<!--列表头部切换结束-->

		<div class="WSY_data">
            <!--列表按钮开始-->
            <div class="WSY_list" id="WSY_list">
                <div class="WSY_left" >
					<a>轮播广告图<span style="color:red;">（礼包专区只显示前10张广告图）</span></a>
				</div>
                   
                <ul class="WSY_righticon">
                    <li class="WSY_inputicon"><a href="add_banner.php?customer_id=<?php echo $customer_id_en ?>&pagenum=<?php echo $pagenum ?>">添加图片</a></li>
                </ul>
                <br class="WSY_clearfloat">
            </div>
            <!--列表按钮结束-->
              
              
        <!--表格开始-->
        <table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
          <thead class="WSY_table_header">
            <!--<th width="3%"><input id="s" onclick="$(this).attr(&#39;checked&#39;)?checkAll():uncheckAll()" type="checkbox"></th>-->
            <th width="3%">ID</th>
            <th width="3%">排序（降序）</th>
            <th width="15%">图片</th>
            <th width="15%">链接</th>
            <th width="5%">操作</th>
          </thead>
		<?php
            while ($row = mysql_fetch_object($result)) {
                $keyid 	= $row->id ;
                $imgurl = $row->banner_imgurl;
                $url = $row->banner_url;
                $sort 	= $row->sort;
			
		?>
          <tr class="WSY_q1">
            <!--<td><input type="checkbox" name="code_Value" value="<?php echo $keyid ?>"></td>-->
            <td align="center"><?php echo $keyid ?></td>
            <td align="center"><?php echo $sort;?></td>
            <td align="center"><a class="WSY_fig WSY_fig2"><img src="<?php echo $imgurl; ?>" style="width:350px;height:100px;"></a></td>
			<td align="center"><?php echo $url;?></td>
            <td class="WSY_t4">
			  <a href="add_banner.php?keyid=<?php echo passport_encrypt((string)$keyid) ?>&customer_id=<?php echo $customer_id_en; ?>&pagenum=<?php echo $pagenum; ?>" style="cursor:pointer;" title="编辑"><img src="../../../../common/images_V6.0/operating_icon/icon05.png"></a>
              <a href="javascript: G.ui.tips.confirm('您确定删除吗？','add_banner.php?keyid=<?php echo passport_encrypt((string)$keyid) ?>&op=del&customer_id=<?php echo $customer_id_en; ?>&pagenum=<?php echo $pagenum; ?>');" title="删除"><img src="../../../../common/images_V6.0/operating_icon/icon04.png"></a>
            </td>
          </tr>
          <?php
  
			}

			mysql_close($link);
			?>
        </table>
        <!--表格结束-->
        
        <!--翻页开始-->
		<div class="WSY_page"></div>
        <!--翻页结束-->      
		</div>
	</div>
	<div style="width:100%;height:20px;"></div>
</div>

<script src="../../../../js/fenye/jquery.page1.js"></script>
<script>
  var pagenum = <?php echo $pagenum ?>;
  var count =<?php echo $page ?>;//总页数
  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		 document.location= "package_banner.php?pagenum="+p+"&customer_id=<?php echo $customer_id_en; ?>";
	   }
    });
</script>	
<script>
  var pagenum = <?php echo $pagenum ?>;
   var page = <?php echo $page ?>;
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
	//var search_title = document.getElementById("search_title").value;
	document.location= "package_banner.php?customer_id=<?php echo $customer_id_en; ?>&pagenum="+a;
	}
  }

</script>
</body>
</html>
