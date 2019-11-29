<?php
header("Content-type: text/html; charset=utf-8"); 
set_time_limit(0); 
require('../../../../../weixinpl/back_newshops/Distribution/express/setup/config.php');
$link = mysql_connect(DB_HOST, DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");


//木有搞

//create_tabel('print_temp');



mysql_close($link);  


function del_tabel($table_name){

	$exits_sql_print_temp = "SHOW TABLES LIKE '$table_name'";
	$obj_print_temp = _mysql_query($exits_sql_print_temp);
	$row_print_temp = mysql_fetch_object($obj_print_temp);
	if($row_print_temp === false){
		try{
			$file_sql_print_temp = file_get_contents($table_name.'.sql');
			$arr_print_temp = explode(';', $file_sql_print_temp);
			foreach ($arr_print_temp as $value) {
				_mysql_query($value.';');
			}
			echo '创建【'.$table_name.'】表完成<br>';
		}catch(Exception $e){
			echo $e->getMessage().'<br><br>';
		}
	}else{
		//print_r($row_print_temp);die();
		echo '【'.$table_name.'】表己经存在<br>';
	}		
}


?>