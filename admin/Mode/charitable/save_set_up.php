<?php

header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
//echo "0000";return;
$charitable_id = 0;
if(!empty($_POST["charitable_id"])){
	$charitable_id = $configutil->splash_new($_POST["charitable_id"]); 
	//是否存在
}
$is_charitable = 0;

	//echo '===1====='.$is_charitable."<br/>";
	$is_charitable = $configutil->splash_new($_POST["is_charitable"]); 
	//是否开启慈善公益：0、关闭，1、开启
	//echo '==2====='.$is_charitable."<br/>";

$charitable_propotion = 0;
if(!empty($_POST["charitable_propotion"])){
	$charitable_propotion = $configutil->splash_new($_POST["charitable_propotion"]); //慈善公益最低分配率

}
$integration_price = 0;
if(!empty($_POST["integration_price"])){
	$integration_price = $configutil->splash_new($_POST["integration_price"]); //捐赠多少元得1慈善分	

}
$diy_num = 0;//获取自定义名称个数
if(!empty($_POST["diy_num"])){
	$diy_num = $configutil->splash_new($_POST["diy_num"]); //慈善公益最低分配率

}
 

	
if($charitable_id>0){
	$sql="update charitable_set_t set is_charitable=". $is_charitable .",charitable_propotion='". $charitable_propotion ."',integration_price='". $integration_price ."' where customer_id=".$customer_id;
	//echo $sql;return;
	//echo $sql."<br/>";
	
}else{
	$sql="insert into charitable_set_t(customer_id,isvalid,is_charitable,charitable_propotion,integration_price) values(".$customer_id.",true,". $is_charitable .",". $charitable_propotion .",". $integration_price .")";
//	echo $sql."<br/>";
}
$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());
for( $i=1; $i <= $diy_num; $i++){
	$singletext     = "";
	$singletext_con = "";
	$name_id        = "";
	if(!empty($_POST["name_id".$i])){
		$name_id = $configutil->splash_new($_POST["name_id".$i]); //判断数据是否存在表里
	}
	if(!empty($_POST["singletext_".$i])){
		$singletext = $configutil->splash_new($_POST["singletext_".$i]); //自定义名称
	}
	if(!empty($_POST["singletext_con_".$i])){
		$singletext_con = $configutil->splash_new($_POST["singletext_con_".$i]); //自定义名称
	}
	if( 1 > $name_id ){
		$sql="insert into charitable_name_t(customer_id,isvalid,name,price_limit) values(".$customer_id.",true,'". $singletext ."','". $singletext_con ."')";
	}else{
		$sql="update charitable_name_t set name='". $singletext ."',price_limit='". $singletext_con ."' where id = ".$name_id." and customer_id=".$customer_id;
	}
	//echo $sql;
	$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());
}

 
$error =mysql_error();
mysql_close($link);
//echo $error; 
echo "<script>location.href='set_up.php?customer_id=".$customer_id_en."';</script>"	
?>