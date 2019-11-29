<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
require_once('../../../../weixinpl/common/utility_common.php');

$read_time 		= $configutil->splash_new($_POST["finish_time"]);	//阅读完成确认时间限制
$notice 		= $configutil->splash_new($_POST["remark"]);	//app使用须知
$app_url 		= $configutil->splash_new($_POST["app_url"]);	//app下载地址
$isopen_guide 	= $configutil->splash_new($_POST["isopen_guide"]);	//app引导开关

$id = -1;
$query = "SELECT id FROM weixin_app_guide where isvalid=true and customer_id=".$customer_id." LIMIT 1";
$result= _mysql_query($query);
while($row=mysql_fetch_object($result)){
	$id = $row->id;
}
if($id<0){
		$sql = "INSERT INTO weixin_app_guide(customer_id,
											  read_time,
											  notice,
											  isvalid,
											  createtime,
											  app_url,
											  isopen_guide
											  ) 
										VALUES(".$customer_id.",
											   ".$read_time.",
											   '".$notice."',
											   true,
											   now(),
											   '".$app_url."',
											   ".$isopen_guide."
											   )";
		//echo $sql;
		_mysql_query($sql) or die('Query failed56: ' . mysql_error().$sql);  
		
}else{
		$sql = "UPDATE weixin_app_guide SET read_time=".$read_time.",
											 notice='".$notice."' ,
											 app_url='".$app_url."',
											 isopen_guide=".$isopen_guide."
									  WHERE id=$id";
		_mysql_query($sql)or die('Query failed67: ' . mysql_error());
		
}

echo "<script>window.history.go(-1)</script>";

?>