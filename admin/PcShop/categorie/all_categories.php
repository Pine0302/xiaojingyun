<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");
$head=0;

$op = '';
$c_id = -1;
if(!empty($_GET['op'])){
	if(!empty($_GET['business_id'])){
		$c_id = $_GET['business_id'];
	}
	$op = $_GET['op'];
	switch($op){
		case 'delect':
			$sql = "update pcshop_home_categories set isvalid=0 where isvalid=true and customer_id=".$customer_id." and id=".$c_id;
		break;		
	}
	
		_mysql_query($sql) or die('SQL failed: ' . mysql_error());
}

//分页---start
$pagenum = 1;
$pagesize = 20;
$begintime="";
$endtime ="";
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * $pagesize;
$end = $pagesize;

$query = "select id,is_open,sort,title from pcshop_home_categories where isvalid=true and customer_id=".$customer_id." order by sort desc limit ".$start.",".$end."";
$query_num = "select count(1) as pcount from pcshop_home_categories where isvalid=true and customer_id=".$customer_id;
$pcount = 0;
$result_num = _mysql_query($query_num) or die('Query failed: ' . mysql_error());
while($row = mysql_fetch_object($result_num)){
	$pcount = $row->pcount;
}
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());

$page = ceil($pcount/$pagesize);

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>PC商城首页分类</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script src="../../Common/js/Data/js/echarts/echarts.js"></script>
<script type="text/javascript" src="../../Common/js/Data/js/ichartjs/ichart.1.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<style>

table th{color: #FFF;line-height: 30px;text-align: center;font-size: 12px; }
table td{height: 40px;line-height: 20px;font-size: 12px;color: #323232;padding: 0px 1em;text-align: center;border: 1px solid #D8D8D8; }
.display{display:none}
table td img{width: 20px;height: 20px;margin-left: 5px;}
.mlt12{margin-left: 15px;margin-top: 22px;}
.WSY_position_date select {
    width: 130px;
    height: 24px;
    background: #fefefe;
    border: 1px solid #ccc;
    color: #333;
    border-radius: 2px;
    padding-left: 5px;
}
</style>

</head>

<body id="bod" style="min-height: 580px;">
	<!--内容框架-->
	<div class="WSY_content" style="height: 100%;">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			
				<?php
			// include("../../../../weixinpl/back_newshops/PcShop/Base/basic_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/PcShop/Base/basic_head.php");
			?>
		
			<!--列表头部切换结束-->
<!--门店列表开始-->
  <div  class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list">


        </div> 
         <!--列表按钮开始-->
                <div class="WSY_list">
                    <ul class="WSY_righticon">
                        <li><a href="category_add.php?action=add">添加分类</a></li>
                        <!--<li class="WSY_inputicon"><input type="button" value="批量删除"></li>-->
                    </ul>
                </div>
                <!--列表按钮开始-->
		
        <!--表格开始-->
		<div class="WSY_data1" id="type" style="margin-left: 1.5%;display:block;">
		
		<table class="WSY_t2"  width="97%"  style="border: 1px solid #D8D8D8;border-collapse: collapse;">
			<thead class="WSY_table_header">
				<tr style="border:none">
					<th width="2%" >ID</th>
					<th width="6%">类别标题</th>
					<th width="6%">排序</th>
					<th width="6%">上架状态</th>
					<th width="6%">操作</th>
				</tr>
			</thead>
			<tbody>	
				<?php 
					$id 		= -1;
					$is_open  	= -1;//是否上架：1：上架；0：下架 默认上架
					$title		= '';//分类标题
					$sort		= '';//排序

					while ($row = mysql_fetch_object($result)) { 
						$id 		= $row->id;
						$is_open 	= $row->is_open;
						$open_str = "下架";
						if($is_open){
							$open_str = "上架";
						}
						$title 		= $row->title;
						$sort 		= $row->sort;
				?>
				<tr>
					<td><?php echo $id;?></td>
					<td><?php echo $title;?></td>
					<td><?php echo $sort;?></td>
					<td id="open_str_<?php echo $id; ?>"><?php echo $open_str;?></td>			
					<td class="images WSY_t4 WSY_remind_main">
					
						<a href="./category_add.php?keyid=<?php echo $id;?>&customer_id=<?php echo $customer_id_en;?>&pagenum=<?php echo $pagenum; ?>">
							<img src="../../../common/images_V6.0/operating_icon/icon05.png" align="absmiddle" alt="编辑" title="编辑">
						</a>
						<a href="./all_categories.php?business_id=<?php echo $id;?>&customer_id=<?php echo $customer_id_en;?>&op=delect&pagenum=<?php echo $pagenum; ?>">
							<img src="../../../common/images_V6.0/operating_icon/icon04.png" align="absmiddle" alt="删除" title="删除">
						</a>
					<dl class="WSY_remind_dl02" style="margin-top:0px;" keyid='<?php echo $id; ?>'>
						<?php if($is_open==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="statusSwith(this,0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="statusSwith(this,1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="statusSwith(this,0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="statusSwith(this,1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>					
					<?php } ?>
					</dl>
					
					</td>
				</tr>
					<?php } ?>
			</tbody>
			
			</table>
			
			<!--翻页开始-->
			<div class="WSY_page">
				
			</div>
			<!--翻页结束-->
		</div>
		<script src="../../../js/fenye/jquery.page1.js"></script>
		<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
		<script type="text/javascript">
			var customer_id_en = '<?php echo $customer_id_en ?>';
			var pagenum = <?php echo $pagenum ?>;
			var count =<?php echo $page ?>;//总页数
			//pageCount：总页数
			//current：当前页

			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
				 document.location= "all_categories.php?customer_id="+customer_id_en+"&pagenum="+p;
			   }
			});

		  var page = <?php echo $page ?>;
		  
		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());

			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
			document.location= "all_categories.php?customer_id="+customer_id_en+"&pagenum="+a;
			}
		  }	
		  
		</script>
		<script>
		function statusSwith(obj,val){
			var keyid = $(obj).parents('dl').attr('keyid');
			 $.get("ajax_data.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>",
			 {
				 keyid:keyid,
				 val:val
			 },
			 function(result){
				 if(result==10001){
					 alert("更新状态失败");
				}else if(result==10000){
					 if(val==0){
						 $('#open_str_'+keyid).html('下架');
					 }else{
						  $('#open_str_'+keyid).html('上架');
					}
				}
	 });
}
		</script>

	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">


<?php 

mysql_close($link);
?>

</body>
</html>
