<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/common/common_ext.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../../weixinpl/proxy_info.php');


$type = $_GET['type'];
$id   = $_GET['id'];
// 选中导航风格
if ($type > 0) {
	if ($id > 0) {
		$query = "UPDATE navigation_style_setting_t SET type = $type,isvalid = 1 where id =".$id;
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	}else{
		$query = "INSERT INTO navigation_style_setting_t (type,isvalid,createtime,customer_id) VALUES ($type,1,now(),$customer_id)";
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	}
}else{
	die('数据错误！');
}
mysql_close($link);

?>
<script language="javascript" type="text/javascript"> 

	window.location.href="style_setting.php?customer_id=<?php echo $customer_id_en; ?>";

</script>