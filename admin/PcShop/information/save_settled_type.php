<?php
header("Content-type:text/html;charset=utf-8"); 
/* require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');;
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8"); */

require_once 'lib/cc.php';
$db = Ycc::getInstance()->connect();








/* $name      = $_POST['name'];//分类名字
$deal_id   = $_POST['deal_id'];//当前处理的分类ID
if(!empty($_POST['parent_id'])){//分类上级ID -1：顶级
	$parent_id = $_POST['parent_id'];
}
$level = 1;
if($parent_id>0){
	$level = 2;
}
$LId = '';
	$PropertyList = '';
	if(!empty($_POST['LId'])){
		$LId = $_POST['LId'];
	}
	if(!empty($_POST['PropertyList'])){
		$PropertyList = $_POST['PropertyList'];
	}
if($deal_id<0){
	$query = "insert into pcshop_merchants_settled_type(name,parent_id,level,customer_id,isvalid,createtime) values('".$name."',".$parent_id.",".$level.",".$customer_id.",true,now())";
	_mysql_query($query) or die('Query failed: ' . mysql_error());
	$insert_id = mysql_insert_id();
	foreach($LId as $key => $value){
		if(!empty($PropertyList[$key])){
			$sql_insert = "insert into pcshop_merchants_settled_type(name,parent_id,level,customer_id,isvalid,createtime) values('".$PropertyList[$key]."',".$insert_id.",2,".$customer_id.",true,now())";
				_mysql_query($sql_insert) or die('sql_insert failed1: ' . mysql_error());
		}				
	}
}else{	
	$name_update = "update pcshop_merchants_settled_type set name='".$name."' where id=".$deal_id;
	_mysql_query($name_update) or die('name_update failed: ' . mysql_error());
	foreach($LId as $key => $value){
		if(!empty($value)){
			$sql_update = "update pcshop_merchants_settled_type set name='".$PropertyList[$key]."' where id=".$value;
			_mysql_query($sql_update) or die('sql_update failed: ' . mysql_error());
			$str .= $value.',';
		}else{
			if(!empty($PropertyList[$key])){
				$sql_insert = "insert into pcshop_merchants_settled_type(name,parent_id,level,customer_id,isvalid,createtime) values('".$PropertyList[$key]."',".$deal_id.",2,".$customer_id.",true,now())";
				_mysql_query($sql_insert) or die('sql_insert failed2: ' . mysql_error());
				$insert_id = mysql_insert_id();
				$str .= $insert_id.',';
			}
		}
	}
	if(!empty($str)){
		$str = trim($str,',');
		$query = "update pcshop_merchants_settled_type set isvalid=false where parent_id=".$deal_id." and id not in(".$str.")";
		_mysql_query($query) or die('query failed: ' . mysql_error());
	}
	
}




mysql_close($link);
 
echo "<script>location.href='settled_type.php?customer_id=".$customer_id_en."';</script>" 
*/
