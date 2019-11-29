<?php  
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');   //配置
require('../../../../weixinpl/customer_id_decrypt.php');   //解密参数
require('../../../../weixinpl/back_init.php');	
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

$batchcode = '';//订单数组
$company_name = '';//物流公司
$company_id = -1;//物流公司ID
if(!empty($_POST["batchcode"])){
   $batchcode = $configutil->splash_new($_POST["batchcode"]);
}
if(!empty($_POST["company_id"])){
   $company_id = $configutil->splash_new($_POST["company_id"]);
}
if(!empty($_POST["company_name"])){
   $company_name = $configutil->splash_new($_POST["company_name"]);
}
$batchcode = trim($batchcode,"|");
$batchcode_array = explode("|",$batchcode);
$str = '';
$query = "update weixin_commonshop_orders set send_express_id=".$company_id.",express_id=".$company_id.",expressname='".$company_name."' where isvalid=true and sendway=0 and customer_id=".$customer_id." and batchcode in(";
foreach($batchcode_array as $key => $value){
	$str .= $value.",";
}
$str = rtrim($str,",");
$query .= $str.")";
_mysql_query($query) or die('Query failed: ' . mysql_error());

$num = mysql_affected_rows();

if($num>=0){	
	$result = array("code"=>1001,"msg"=>"修改成功",'batchcode'=>$batchcode_array);
}else{
	$result = array("code"=>4001,"msg"=>"修改失败");
}	
echo json_encode($result);
?>