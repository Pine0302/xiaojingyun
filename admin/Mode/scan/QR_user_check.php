<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);  //解密

require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require('../../../../weixinpl/proxy_info.php');
$pagenum = 1;

if(!empty($_GET["pagenum"])){
   $pagenum = $_GET["pagenum"];
}



$start = ($pagenum-1) * 20;
$end = 20;
$query = 'select tick.createtime,tick.batchcode,clerk.confirm_name from weixin_commonshop_ticketget as tick LEFT JOIN weixin_commonshop_ticketclerk as clerk on clerk.id = tick.clerk_id  where tick.customer_id='.$customer_id." order by tick.createtime desc limit ".$start.",".$end; 

if(!empty($_GET["batchcode"])){
   $batchcode = $configutil->splash_new($_GET["batchcode"]);
   $query = 'select tick.createtime,tick.batchcode,clerk.confirm_name from weixin_commonshop_ticketget as tick LEFT JOIN weixin_commonshop_ticketclerk as clerk on clerk.id = tick.clerk_id  where tick.customer_id='.$customer_id." and tick.batchcode like '%".$batchcode."%' order by tick.createtime desc limit ".$start.",".$end; 
}

$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
 
$rcount_num = 0; 
$query_num = 'select count(1) as rcount from weixin_commonshop_ticketget where customer_id='.$customer_id; 
$result_num = _mysql_query($query_num) or die('Query failed_num: ' . mysql_error());
while ($row = mysql_fetch_object($result_num)) {
	$rcount_num =$row->rcount;
}
 
$page=ceil($rcount_num/$end);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>兑换员登录记录</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<link rel="stylesheet" type="text/css" href="../../../common/css_liuliang/flowblue.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
</head>
<style>
.WSY_sales{margin:0px;}
.WSY_list p{
	margin:0px;
}
</style>
<body>

	<div class="WSY_content">

		<div class="WSY_columnbox">
			
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a href="index.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" >二维码发送设置</a>
					<a href="QR_user.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" >兑票员设置</a>
					<a href="QR_user_login.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" >兑票员登录日志</a>  
					<a href="QR_user_check.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" class="white1">兑票员扫码日志</a>  
				</div>
			</div>			

		<div class="WSY_data">

		<div class="WSY_list" id="WSY_list" style="min-height: 500px;">
			<div class="WSY_left" ><a>日志列表</a>
		</div>

		<br class="WSY_clearfloat">

		<form action="QR_user_check.php" method="get">
			<div class="WSY_sales">
				<!--<dl class="WSY_sales_dl01"> 
					<dt>订单号：</dt>
					<dd>
					<input type="text" class="test-style width150" autocomplete="off" value="<?php echo $batchcode; ?>" id="batchcode" name="batchcode">
					<input type="hidden"  value="<?php echo passport_encrypt((string)$customer_id) ?>" id="customer_id" name="customer_id">
					</dd>
				</dl>   
				 
				<dl class="WSY_sales_dl02">
					<button class="sbtn" type="submit">查询</button>
				</dl>-->
				<li class="WSY_positiondate" id="WSY_positiondate">
					<p>
						订单号：<input type="text" class="test-style width150" autocomplete="off" value="<?php echo $batchcode; ?>" id="batchcode" name="batchcode">
						<input type="hidden"  value="<?php echo passport_encrypt((string)$customer_id) ?>" id="customer_id" name="customer_id">
					</p>
					<p class="WSY_bottonliss"><input type="submit" value="搜索"></p>
				</li>
			</div>
			
		</form>		
		
        <table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			<thead class="WSY_table_header">
				<th width="3%"><input id="s" onclick="$(this).attr('checked')?checkAll():uncheckAll()" type="checkbox"></th>            
				<th width="20%">用户名</th> 
				<th width="20%">订单号</th>
				<th width="20%">时间</th>
			</thead>
		  <?php	
		   while ($row = mysql_fetch_object($result)) {				
				$batchcode= $row->batchcode;
				$createtime=$row->createtime;				
				$confirm_name=$row->confirm_name;				
		?>
			<tr>
				<td><input type="checkbox" name="code_Value" value=""></td>
				<td align="center"><?php echo $confirm_name; ?></td>
				<td align="center"><?php echo $batchcode; ?></td>
				<td align="center"><?php echo $createtime; ?></td>
			</tr>
		  <?php
  
			}

			mysql_close($link);
			?>
        </table>
        <!--表格结束-->
        
        <!--翻页开始-->
        <div class="WSY_page">
        	
        </div>
        <!--翻页结束-->
        </div>
		</div>
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>

<script src="../../../js/fenye/jquery.page1.js"></script>
<script>
  var pagenum = <?php echo $pagenum ?>;
  var count =<?php echo $page ?>;//总页数
  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		 document.location= "QR_user_check.php?pagenum="+p+"&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>"; 
	   }
    });

  var page = <?php echo $page ?>;
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
	document.location= "QR_user_check.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a; 
	}
  }	
</script>	
</body>
</html>
