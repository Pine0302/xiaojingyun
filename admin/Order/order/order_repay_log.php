<?php
header("Content-type: text/html; charset=utf-8"); //ini_set('display_errors','on');
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');

$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

$batchcode = '';
if(!empty($_GET["batchcode"])){
	$batchcode = $configutil->splash_new($_GET["batchcode"]);
}
 
$query  = "select id,commission_id,createtime,class,type,status from weixin_order_commission_repay_log where isvalid=true and batchcode='".$batchcode."' ORDER BY createtime DESC"; 
?>

<!doctype html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>返佣说明 订单号:<?php echo $batchcode;?></title>
<style>
.operation-btn{padding: 3px 5px;background-color: #06a7e1;color: #fff;border-radius: 2px;cursor:pointer;}
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
					<a class="white1">返佣说明</a>
				</div>
			</div>
			<!--列表头部切换结束-->
			<div class="WSY_remind_main">
				<form class="search" id="search_form" method="post" action="cash.php?customer_id=AzBVZ1UzVGk=">
					<div class="WSY_list" style="margin-top: 18px;">
						<li class="WSY_left"><a>订单号：<?php echo $batchcode; ?></a></li>		
						<ul class="WSY_righticon">
							<li><a style="margin-right:40px;" href="javascript:history.go(-1);"><td valign="bottom" align="right">返回</td></a></li>         
						</ul>
					</div>     
				</form>
  
				<table width="97%" class="WSY_table" id="WSY_t1">
					<thead class="WSY_table_header">
						<th width="20%" nowrap="nowrap">序号</th>
						<th width="10%" nowrap="nowrap">佣金ID</th>
						<th width="10%" nowrap="nowrap">分佣行业</th>
						<th width="10%" nowrap="nowrap">分佣类型</th>
						<th width="20%" nowrap="nowrap">处理结果</th>
						<th width="20%" nowrap="nowrap">操作时间</th>
					</thead>
					<tbody>
					<?php 
						 $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
						
						$id = -1;
						$commission_id = -1;
						$class = -1;
						$status = -1;
						$type = -1;
						$createtime = '';
						 while ($row = mysql_fetch_object($result)) {
							$id = $row->id;
							$createtime = $row->createtime;
							$commission_id = $row->commission_id;
							$class = $row->class;
							$type = $row->type;
							$status = $row->status;
							
							$class_str = '';
							switch($class){
								case 0:
									$class_str = '商城';
								break;
								case 70:
									$class_str = '大礼包';
								break;
							}
							$type_str = '';
							switch($type){
								case 0:
									$type_str = '普通分佣';
								break;
								case 1:
									$type_str = '区域奖励';
								break;
								case 2:
									$type_str = '店铺奖励';
								break;
								case 9:
									$type_str = '全球分红';
								break;
							}
							$status_str = '';
							switch($status){
								case 0:
									$status_str = '未处理';
								break;
								case 1:
									$status_str = '处理成功';
								break;
								case 2:
									$status_str = '处理失败';
								break;
							}
						
					?> 
						<tr>
							<td><?php echo $id; ?></td>
							<td><?php echo $commission_id; ?></td>
							<td><?php echo $class_str; ?></td>
							<td><?php echo $type_str; ?></td>
							<td><?php echo $status_str; ?></td>
							<td><?php echo $createtime; ?></td>
						</tr>					    
					<?php
						 }
					?>
					</tbody>					
				</table>
				<div class="blank20"></div>
				<div id="turn_page"></div>
				<!--翻页开始-->
				<div class="WSY_page">
        	
				</div>
				<!--翻页结束-->
			</div>
		</div>
	</div>

<?php mysql_close($link);?>	


</body>
</html>