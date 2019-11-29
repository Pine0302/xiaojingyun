<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');   //配置
require('../../../../weixinpl/customer_id_decrypt.php');   //解密参数
require_once('../../../../weixinpl/back_newshops/Order/sf/lib/CryptDes.php'); // 解密
require_once('../../../../weixinpl/back_newshops/Order/sf/lib/routeApi.php'); // 顺丰接口

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$mailorderNo = $configutil->splash_new($_GET["mailorderNo"]);
$customer_id = $configutil->splash_new($_GET["customer_id"]);

if(!$mailorderNo || !$customer_id){
	die("参数错误!");
}


	$sql="select * from sf_import where customer_id=$customer_id and ison=1";
	$re_sf=_mysql_query($sql) or die("查询顺丰进口业数据表务表失败!");
	$l_sf=mysql_num_rows($re_sf);
	if(!$l_sf){
		die("没有配置顺丰进口参数!");
	}else{
		$row_sf=mysql_fetch_object($re_sf);
		$head=$row_sf->head;
		$authToken=$row_sf->authToken;
	}
	
	

$array = Array(
    'server' => '//cbti.sfb2c.com:8005/CBTT/ws/routeQueryService?wsdl', // webserver 服务	
    'authToken' => '+SVLulpUoT993cvqVeO2II0HhZp6NfgkHf8+LWjbhBKo5oC4dkGo1Q==', // authToken	
    'headerNamespace' => '//cbti.sfb2c.com8005/CBTT', // SoapHeader命名空间（与webserver域同步）	
    'customerCode' => $head, //客户代码	
    'mailorderNo' => $mailorderNo, //运单号或电商原始订单号	
    'secretKey' => '7%&y*p#e', //秘钥向量	
    'mix' => '(-&t@p#p' // 混淆向量	
); 


$Api   = new routeApi(); // 实例化
$re=$Api -> getRoute($array);
if(!$re) exit;
$re = simplexml_load_string($re,'SimpleXMLElement', LIBXML_NOCDATA);
$track=$re->track;
if(!$track) die("找不到路由");

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>运单路由查询</title>
<link rel="stylesheet" type="text/css" href="css/base_m.css">
<link rel="stylesheet" type="text/css" href="css/query.css">
<link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body id="body" class="hidden" style="display: block;">

<!--fix-box-bottom-->
<div class="container w960 mt10px">
  <div class="section">
    <div id="queryContext" class="mt10px hidden relative" style="z-index: 4; display: block;">
      <div class="result-top">
	  <span class="col1">时间</span><span class="col2">地点和跟踪进度</span>
	  </div>
      <table id="queryResult2" class="result-info2" cellspacing="0">
	  <tbody>
	  
<?php
$i=0;
while($detail=$track->detail[$i]){
	$mailNo=$detail->attributes()->mailNo;
	$occurcountry=$detail->attributes()->occurcountry;
	$localtime=$detail->attributes()->localtime;
	$eventdescription=$detail->attributes()->eventdescription;
	?>
	<tr><td class="row1"><?php echo $localtime; ?></td><td class="status">&nbsp;</td><td><?php echo $eventdescription; ?></td></tr>
	<?php
	$i++;
	}
	?>
	</tbody>
	</table>
    </div>

  </div>
</div>

</body>
</html>
