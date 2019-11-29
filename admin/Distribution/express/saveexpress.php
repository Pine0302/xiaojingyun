<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$name			 = $configutil->splash_new($_POST["name"]);			//快递名称
$type 			 = $configutil->splash_new($_POST["type"]);			//计价方式
$FirstNum 		 = $configutil->splash_new($_POST["FirstNum"]);		//首件件数/首重重量
$ContinueNum 	 = $configutil->splash_new($_POST["ContinueNum"]);	//续件件数/续重重量
$price 			 = $configutil->splash_new($_POST["price"]);		//首件费用/首重费用
$ContinuePrice   = $configutil->splash_new($_POST["ContinuePrice"]);//续件费用/续重费用
$keyid 			 = $configutil->splash_new($_POST["keyid"]);	
$cost 			 = $configutil->splash_new($_POST["cost"]);		 	//快递所需商品总费用
$expressCode 	 = $configutil->splash_new(i2e($_POST,'kuaiDiName'));	//快递100代码
$FreeNum 		 = $configutil->splash_new($_POST["FreeNum"]);	    //免邮设置。按件免邮/按重免邮
$include 		 = $configutil->splash_new($_POST["include"]);		//运送范围:0.范围之内 1.范围之外
// $region 		 = $_POST["region"];								//是否允许快递的地区
$region 		 = $configutil->splash_new($_POST["region"]);		//是否允许快递的省份
$city_area 		 = $configutil->splash_new($_POST["city_area"]);		//是否允许快递的市和区
$print_temp_id   = -1;
if(!empty($_POST["print_temp_id"])){
	$print_temp_id	 = $configutil->splash_new($_POST["print_temp_id"]);//绑定的打印快递模板ID
}
$print_temp_id = 1;
// $regions = implode(",",$region);

$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

 if($keyid>0){
    _mysql_query("update weixin_expresses set name='".$name."' ,type=".$type.", FirstNum=".$FirstNum.", ContinueNum=".$ContinueNum.", price=".$price.",ContinuePrice=".$ContinuePrice.",cost=".$cost.",is_include=".$include.",region='".$region."',expressCode='".$expressCode."',FreeNum=".$FreeNum.", print_temp_id=".$print_temp_id.",city_area='".$city_area."' where id=".$keyid);
 }else{
    if($name!=""){
	   //*是默认回复
	   $query="insert into weixin_expresses(name,type,FirstNum,ContinueNum,price,ContinuePrice,customer_id,isvalid,createtime,is_include,region,cost,expressCode,FreeNum,print_temp_id,city_area) values ('".$name."',".$type.",".$FirstNum.",".$ContinueNum.",".$price.",".$ContinuePrice.",".$customer_id.",true,now(),".$include.",'".$region."',".$cost.",'".$expressCode."',".$FreeNum.",".$print_temp_id.",'".$city_area."')";
	   
      _mysql_query($query);
	}
 }
 
 $error =mysql_error();
 mysql_close($link);
 //echo $error; 
 echo "<script>location.href='express.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
 
//isset to empty 如果数组不存在就返回空值
function i2e($array, $array_key){ if(isset($array[$array_key])){ return $array[$array_key]; }else{return '';} }
?>