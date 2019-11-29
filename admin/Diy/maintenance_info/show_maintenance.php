<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");


$is_maintain = 0;//维护开关
$begintime = "";//维护开始时间
$endtime = "";//维护结束时间
$information = "";//维护内容
$query = 'SELECT id,is_maintain,begintime,endtime,information FROM weixin_maintenance_info where isvalid=true and customer_id='.$customer_id.' limit 0,1';
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
  $is_maintain = $row->is_maintain;
  $begintime = $row->begintime;
  $endtime = $row->endtime;
  $information = $row->information;
}
 
?>
<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="cache-control" content="no-cache">
        <meta http-equiv="expires" content="0">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="white">
        <meta name="format-detection" content="telephone=no">
        <meta name="description" content="">
        <meta name="author" content="Administrator">
        <title>维护信息</title>
<style>
.main{
   width:90%;
   margin: 0 auto;
   margin-top:30px;
   height:500px;
}
.one{
   text-align:center;
   
}
.two{

   font-size:20px;
   color:#000;
   text-align:center;
   margin-top:10px;
   font-family: Georgia, serif;
   text-shadow: 0px 2px 3px #666;
}

.three{
	margin:0 auto;
	text-align:center;
	display:block;
	width:80px;
	line-height:40px;
	-webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    margin-top:30px;
	font-size:18px;
	color:#fb6800;
	background:#f5ceba;
}
</style>		
    </head>
    <body>
        <div class="main">
			<?php if($is_maintain ){echo $information; } else{echo "没有维护！";}?>
			<div class="maintaintime">
				<a>维护时间：<?php echo $begintime;?> 至 <?php echo $endtime;?></a>
			</div>
        </div>
		

</body></html>