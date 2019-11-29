<?php
header("Content-type: text/html; charset=utf-8"); 
set_time_limit(0); 
//require('../../../../../weixinpl/back_newshops/Distribution/express/setup/config.php'); //本地
require('../../../../../../../../../weixinpl/back_newshops/Distribution/express/setup/config.php'); //服务器
$link = mysql_connect(DB_HOST, DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");




create_tabel('weixin_print_temp');
add_alter("ALTER TABLE `weixin_print_temp` ADD `isvalid` BOOLEAN NOT NULL DEFAULT TRUE COMMENT '是否可用'");
add_alter("ALTER TABLE `weixin_expresses` ADD `print_temp_id` INT NOT NULL DEFAULT '0' COMMENT '快递打印模板ID'");
add_alter("ALTER TABLE `weixin_print_temp` ADD `customer_id` INT NOT NULL DEFAULT '0' COMMENT '商城用户ID' AFTER `id`");
add_alter("ALTER TABLE `weixin_print_temp` ADD `is_supply` TINYINT NOT NULL DEFAULT '0' COMMENT '是否属于供应商，0非，1是' AFTER `customer_id`");
add_alter("ALTER TABLE `weixin_expresses_supply` ADD `print_temp_id` INT NOT NULL DEFAULT '0' COMMENT '快递打印模板ID'");
add_alter("ALTER TABLE `weixin_print_temp` ADD `supply_id` INT NOT NULL DEFAULT '0' COMMENT '供应商ID' AFTER `customer_id`");

mysql_close($link);  


function create_tabel($table_name){

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


function add_alter($sql){
	//$sql = "ALTER TABLE `weixin_print_temp` ADD `isvalid` BOOLEAN NOT NULL DEFAULT TRUE COMMENT '是否可用'";
	$obj_print_temp = _mysql_query($sql);
	if($obj_print_temp){
		echo '添加属性成功<br>';
	}else{
		echo '属性添加失败，也许是语法错了，也许是己经存在该属性，复制下列语句到数据库中尝试<br>';
		echo $sql.'<br>';
	}		
}


?>