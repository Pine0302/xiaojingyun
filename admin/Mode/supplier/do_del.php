<?php
/*
图片删除
*/
require('../../../../weixinpl/config.php');


//连接数据库
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

// //设置上传目录
// $path = "../uploads/";
$data = $_POST['img_id'];
$data = json_decode($data,false);
$result = array();
$result['msg'] = "false";
if (!empty($data)) {
	$img_id = $data;
	$sql = "UPDATE weixin_commonshop_supply_album SET isvalid=false WHERE id=".$img_id;
	$result['msg'] = $sql;
	if(_mysql_query($sql)){
		$result['msg'] = "true";
	}	
}
echo json_encode($result);
?>