<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");

/* 产品ID */
if(!empty($_GET["setting_id"])){
   $setting_id = $configutil->splash_new($_GET["setting_id"]);
}
?>
<!DOCTYPE html>
<!-- saved from url=(0047)//www.ptweixin.com/member/?m=shop&a=orders -->
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>零钱支付手续费修改日志</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">	
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
</head>

<body>
<div id="WSY_content">
	<div class="WSY_columnbox" style="min-height: 300px;">
		<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a class="white1">修改日志</a>
			</div>
		</div>
		<div  class="WSY_data">
		<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			<thead class="WSY_table_header">
				<tr>
					<th width="10%" nowrap="nowrap">id</th>  
					<th width="10%" nowrap="nowrap">行业</th>  
					<th width="18%" nowrap="nowrap">修改备注</th>
					<th width="10%" nowrap="nowrap">时间</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$pagenum = 1;

				if(!empty($_GET["pagenum"])){
				   $pagenum = $configutil->splash_new($_GET["pagenum"]);
				}
				
				$start = ($pagenum-1) * 20;
				$end = 20;
				$query="select id,remark,industry_name,createtime from weixin_moneybag_poundage_setting_log where isvalid=true and customer_id=".$customer_id." and setting_id=".$setting_id;
				$result_q = _mysql_query($query) or die('W82 Query failed5: ' . mysql_error());
				$rcount_q = mysql_num_rows($result_q);
				$query = $query." order by id desc limit ".$start.",".$end;
				//echo $query;
				$log_id	        = -1; //日志ID
				$remark 		= ""; //修改备注
				$createtime 	= ""; //日志创建时间	
				$industry_name 	= ""; //行业名称	
				$result = _mysql_query($query) or die('W87 Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
						$log_id             	= $row->id;
						$remark                	= $row->remark;
						$industry_name         	= $row->industry_name;
						$createtime         	= $row->createtime;
						
						
					?>
					<tr>
					   <td><?php echo $log_id; ?></td>
					   <td><?php echo $industry_name; ?></td>
					   <td><?php echo $remark; ?></td>
					   <td><?php echo $createtime; ?></td>
					</tr>				
				<?php } ?>
			</tbody>
		</table>
		<div class="blank20"></div>
		<div id="turn_page"></div>
		</div>
		<!--翻页开始-->
        <div class="WSY_page">
        	
        </div>
        <!--翻页结束-->		
	</div>
</div>

<?php 

mysql_close($link);
?>

<script src="../../../js/fenye/jquery.page1.js"></script>
<script>

var customer_id   = '<?php echo $customer_id_en ?>';
var pagenum       = <?php echo $pagenum ?>;
var rcount_q2     = <?php echo $rcount_q ?>;
var end           = <?php echo $end ?>;
var setting_id    = <?php echo $setting_id ?>;
var count         = Math.ceil(rcount_q2/end);//总页数
var page          = count>0?count:1;
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
	document.location= "poundage_set_log.php?pagenum="+p+"&customer_id="+customer_id+"&setting_id="+setting_id;
   }
});

function jumppage(){
var a=parseInt($("#WSY_jump_page").val()); 
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		document.location= "poundage_set_log.php?pagenum="+a+"&customer_id="+customer_id+"&setting_id="+setting_id;
		
	}
}



</script>
</body></html>