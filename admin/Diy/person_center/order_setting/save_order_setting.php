<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
$customer_id = $_GET['customer_id'];
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$op = $_GET['op'];
$result = array(
	'code'=>10000,
	'msg'=>''
);

switch ($op){
	case 'save';
		$check_box_sel = $_POST['check_box_sel'];
		$check_box_sel = json_decode($check_box_sel);
		
		$str = '';
		foreach($check_box_sel as $k =>$values){
			$str .= $values[0].'_'.$values[1].'|*|';
		}		
		$query = "update weixin_commonshop_order_setting_cus set choose='".$str."' where isvalid=true  and  customer_id=".$customer_id."";
		//echo $query;
		$res=_mysql_query($query)or die('Query failed'.mysql_error());
		if($res){
				$result = array(
					'code'=>10001,
					'msg'=>'保存成功！'
				);
		
		}else{
				$result = array(
					'code'=>10004,
					'msg'=>'保存失败，请重试！'
				);
			
		}
	break;
}
$out = json_encode($result);
echo $out;

mysql_close($link);

?>