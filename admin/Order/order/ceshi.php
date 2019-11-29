<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]		


$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$box_arr = $_POST["box_arr"];
//$box_arr =  '{"foo": 12345}';
//var_dump($box_arr); 
$box_arr = json_decode($box_arr,true);
var_dump($box_arr);
echo $box_arr[0][0];

?>