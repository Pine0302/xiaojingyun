<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$title =$configutil->splash_new($_POST["title"]);
$keyid =$configutil->splash_new($_POST["keyid"]);
 $link =    mysql_connect(DB_HOST,DB_USER, DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');




 if($keyid>0){
    _mysql_query("update weixin_sendtimes set title='".$title."' where id=".$keyid);
 }else{
    _mysql_query("insert into weixin_sendtimes(title,customer_id,isvalid) values ('".$title."',".$customer_id.",true)");
 }
 
 $error =mysql_error();
 mysql_close($link);
 //echo $error; 
 echo "<script>location.href='sendtimes.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>"
?>