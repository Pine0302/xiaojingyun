<?php
header("Content-type: text/html; charset=utf-8"); 
set_time_limit(0); 
require('../../../../weixinpl/config.php');
$link = mysql_connect(DB_HOST, DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");


if(isset($_GET['print_name'])){
	$select_print_temp_array = select_print_temp($_GET['print_name']);
	echo json_encode($select_print_temp_array);
}




mysql_close($link);  


function select_print_temp($print_name){
	$result = array();
	$sql_print_temp = "SELECT id,print_name from weixin_print_temp WHERE print_name='$print_name'";
	$obj_print_temp = _mysql_query($sql_print_temp); 
	$row_print_temp = mysql_fetch_array($obj_print_temp, MYSQL_ASSOC);
	if($row_print_temp !== false){ $result = $row_print_temp; }else{$result['id']=0;}	
	return $result;
}

?>