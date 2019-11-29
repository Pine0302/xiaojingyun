<?php
header("Content-type: text/html; charset=utf-8"); 
//ini_set('display_errors','On');
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

$inverst_id  = $configutil->splash_new($_GET["inverst_id"]);
$category = $configutil->splash_new($_GET["category"]);
if(empty($inverst_id) || empty($category))
{
	header("location:javascript:history.back(-1)");
}

$pagenum  = 1;//页码
$pagesize = 10;//每页数据数量

if(!empty($_GET["pagenum"])) 
{
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * $pagesize;
$sql1  = "select count(id) as count_id from weixin_attrach_investment_user_log where inverst_id=$inverst_id and category=$category";
$res1  = _mysql_query($sql1) or die('Query failed1: ' . mysql_error());
while($row = mysql_fetch_object($res1)) 
{
	$count = $row->count_id;
}
$page  = ceil($count/$pagesize);

$sql2  = "select parent_id,inverst_id,prev_id,remark,createtime from weixin_attrach_investment_user_log where inverst_id=$inverst_id and category=$category order by id desc limit $start,$pagesize";
$res2  = _mysql_query($sql2) or die('Query failed2: ' . mysql_error());
$k     = 0;
while($row = mysql_fetch_object($res2)) 
{
	if($row->parent_id != '')
	{
		$sql3  = "select name,weixin_name from weixin_users where id=".$row->parent_id;
		$res3  = _mysql_query($sql3) or die('Query failed3: ' . mysql_error());
		while($row3 = mysql_fetch_object($res3)) 
		{
			$data[$k]['parent_name']  = $row3->name;
			$data[$k]['parent_wname'] = $row3->weixin_name;
		}
	}	

	if($row->prev_id != '')
	{
		$sql4  = "select name,weixin_name from weixin_users where id=".$row->prev_id;
		$res4  = _mysql_query($sql4) or die('Query failed4: ' . mysql_error());
		while($row4 = mysql_fetch_object($res4)) 
		{
			$data[$k]['prev_name']  = $row4->name;
			$data[$k]['prev_wname'] = $row4->weixin_name;
		}
	}
	$data[$k]['remark']     = $row->remark;
	$data[$k]['createtime'] = $row->createtime;
	$k++;
}

?>  
<!doctype html>
<html><head><meta charset="utf-8">
<title>基本设置</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/charitable/set_up.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/MarkPro/packages/packages.css">
<style type="text/css">
	.fanhui{display: block; width: 100px; height: 30px; font-size: 14px; color: #fff; background:#06a7e1; text-align: center;line-height: 30px; border-radius: 3px; float: right; margin-top: 30px; margin-right: 40px;}
</style>
</head>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a href="javascript:void(0)">招商推荐人</a>
				</div>
			</div>
			<!--列表头部切换结束-->		
			<a class="fanhui" href="javascript:history.back(-1)">返回</a>
			<div style="clear: both;"></div>
			<div class="WSY_remind_main">
				<dl class="WSY_remind_dl02">
					<table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<!-- <th width="25%" class="WSY_table_little">字段类型</th> -->
							<th width="25%" class="WSY_table_little">更改前推荐人</th>
							<th width="25%" class="WSY_table_little">更改后推荐人</th>
							<th width="25%" class="WSY_table_little">更改时间</th>
							<th width="25%" class="WSY_table_little">说明</th>
						</thead>
						<?php foreach($data as $k=>$v){  ?>
							<tr class="diy_one_two" id="diy_item_<?php echo $diy_num; ?>">

								<td><?php echo $v['prev_name'] ?>
								<?php if($v['prev_wname'] != ''){ ?>
									(<?php echo $v['prev_wname'] ?>)
								<?php }else{ ?>
									无推荐人
								<?php } ?>
								</td>
								<td>
								<?php echo $v['praent_name'] ?>
								<?php if($v['parent_wname'] != ''){ ?>
									(<?php echo $v['parent_wname'] ?>)
								<?php }else{ ?>
									无推荐人
								<?php } ?>
								</td>
								<td><?php echo $v['createtime'] ?></td>
								<td><?php echo $v['remark'] ?></td>
							</tr>
						<?php } ?>
					</table>
				</dl>
			
			</div> 
			<!--翻页开始-->
		    <div class="WSY_page">
		    	
		    </div>
		</div>
	</div>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script> 
var customer_id   = '<?php echo $customer_id_en?>';

var pagenum = <?php echo $pagenum ?>;
var page    = <?php echo $page ?>;
var pagenum = <?php echo $pagenum ?>;
var count   = <?php echo $page ?>;//总页数
var user_id = <?php echo $user_id ?>;
var category= <?php echo $category ?>

$(".WSY_page").createPage({
    pageCount:count,
    current:pagenum,
    backFn:function(p){
		var url = "edit_detail.php?pagenum="+p+"&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&user_id="+user_id+"&category="+category;
		document.location= url;
   }
});


</script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../Common/js/Mode/charitable/set_up.js"></script>
</body>
</html>