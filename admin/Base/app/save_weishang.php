<?php
header("Content-type: text/html; charset=utf-8"); //test
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
require('../../../../weixinpl/common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../weixinpl/proxy_info.php'); 
_mysql_query("SET NAMES UTF8");

$op = $configutil->splash_new($_GET["op"]);
if($op == "reg"){
	$password = $configutil->splash_new($_POST["regPass"]);
	$phone = $configutil->splash_new($_POST["safePhone"]);
	
	$query = "select id from appusers where username = '".$phone."' and isvalid = true";
	$result = _mysql_query($query) or die("L19 query error : ".mysql_error());
	$aid = 0;
	if($row = mysql_fetch_object($result)){
		$aid = $row->id;
	}
	if($aid == 0 ){
		$query = "insert into appusers(username,pwd,role,customerid,createtime) values ('".$phone."','".md5($password)."',2,'".$customer_id."',now())";
		_mysql_query($query) or die("L22 query error : ".mysql_error());
		$appid = mysql_insert_id();
		
		$query = "insert into appandcus (appid,customerid,createtime) values ('".$appid."','".$customer_id."',now())";
		_mysql_query($query) or die("L26 query error : ".mysql_error());
	}
	
	
}
?>
<script type="text/javascript">
	location.href='weishang.php?customer_id=<?php echo $customer_id_en ?>';
</script>
<?php
mysql_close($link);
?>
