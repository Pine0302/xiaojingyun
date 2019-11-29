<?php 
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');

$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');

/*----------------------------导航标题区设置-----------------------------*/

$nav_title = $_POST["nav_title"];
$nav_link = $_POST["nav_link"];
$link_detail = $_POST["link_detail"];
$nav_num = $_POST["nav_num"];

$data = array();

for($i=0;$i<$nav_num;$i++){
	$data_detail = array();	
	$data_detail['title'] = $nav_title[$i];
	$data_detail['link'] = $nav_link[$i];
	$data_detail['link_detail'] = $link_detail[$i];
	$link_url = '';			
	if($nav_link[$i]==6 && $link_detail[$i]>0){ //活动页
		$link_url = "/shop/index.php/Home/Product/ActivityPage/tem_id/".$link_detail[$i];
	}
	switch($nav_link[$i]){
		case 1:
			$link_url = "/shop/index.php/Home/Index/index";
			break;
		case 2:
			$link_url = "/shop/index.php/Home/Package/index";
			break;
		case 3:
			$link_url = "/shop/index.php/Home/ScoreShop/index";
			break;
		case 4:
			$link_url = "/shop/index.php/Home/Qiang/index";
			break;
		case 5:
			$link_url = "/shop/index.php/Home/MyShop/index";
			break;
			
	}
	$data_detail['link_url'] = $link_url;
	$data[] = $data_detail;
}
$data_json = json_encode($data,JSON_UNESCAPED_UNICODE);

$id = -1;
$query = "SELECT id FROM pcshop_head_service WHERE isvalid=true AND customer_id=".$customer_id." AND type=0 LIMIT 1";
$result= _mysql_query($query) or die('L29: ' . mysql_error());  
while( $row = mysql_fetch_object($result)){
	$id = $row->id;
}
if( $id < 0){
	$query = "INSERT INTO pcshop_head_service(isvalid,customer_id,type,data_t) VALUES(true,".$customer_id.",0,'".$data_json."')";
}else{
	$query = "UPDATE pcshop_head_service SET data_t='".$data_json."' WHERE isvalid=true AND id=".$id;
}
_mysql_query($query) or die('L38: ' . mysql_error());  

/*----------------------------导航标题区设置 end---------------------------*/


echo '<script>document.location="head_support.php?customer_id='.$customer_id_en.'";</script>';




?>