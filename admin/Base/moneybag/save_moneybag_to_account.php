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
require_once ('../../../../weixinpl/common/common_ext.php');

$is_open_change 	= $configutil->splash_new($_POST["is_open_change"]);//零钱转货款开关，默认关闭
$min_change_price 	= $configutil->splash_new($_POST["min_change_price"]);//最低转换金额条件：默认-1不限制
$coefficient 		= $configutil->splash_new($_POST["coefficient"]);//转换系数：默认 -1：不限，10：整10，100：整100，1000：整1000 （目前仅4种选项）
$change_rule 		= $configutil->splash_new($_POST["change_rule"]);//转换规则
$comment 			= $configutil->splash_new($_POST["comment"]);	//说明
if(empty($comment)){
    $comment='';
}

$query = "SELECT customer_id FROM ".WSY_DH.".orderingretail_change_account_setting where customer_id=".$customer_id;
$result= mysql_find($query);
$res=false;
if(!$result){
    $sql = 'INSERT INTO '.WSY_DH.'.orderingretail_change_account_setting(customer_id,is_open_change,min_change_price,coefficient,change_rule,comment) 
									VALUES('.$customer_id.','.$is_open_change.','.$min_change_price.','.$coefficient.','.$change_rule.',"'.$comment.'")';
    $res=_mysql_query($sql)or die('Query failed: ' . mysql_error());
}else{
    $sql = "UPDATE ".WSY_DH.".orderingretail_change_account_setting SET is_open_change=$is_open_change,
										 min_change_price=$min_change_price,
										 coefficient=$coefficient,
										 change_rule=$change_rule,
										 comment='$comment'
									  WHERE customer_id=$customer_id";
    $res=_mysql_query($sql)or die('Query failed: ' . mysql_error());
}
if($res!==false){
    echo "<script>alert('保存成功');</script>";
}else{
    echo "<script>alert('网络异常');</script>";
}
echo "<script>window.location.href='./money_to_account.php?customer_id=".$customer_id_en."'</script>";

?>