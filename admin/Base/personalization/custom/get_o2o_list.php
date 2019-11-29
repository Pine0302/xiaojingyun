<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$level = 0;
if(!empty($_GET["level"])) {
    $level = $configutil->splash_new($_GET["level"]);
}

if ($level == 0) {
    exit;
}

$query = "SELECT id,trade_name,level FROM now_pay_trade WHERE custid = ".$customer_id." AND isvalid = TRUE AND level = ".$level;
$result_o2o = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result_o2o)) {
    $o2o_id         = $row->id;
    $o2o_name       = $row->trade_name;
    $o2o_level      = $row->level;
    $o2o_list_arr[] = $row->level."_".$o2o_id."_".$o2o_name;
}

mysql_close($link);

echo json_encode($o2o_list_arr);
exit;
 

?>