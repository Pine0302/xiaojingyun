<?php 
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');


/*----------------------------先获取A区的设置-----------------------------*/
$is_check_a = false;
//-------先设置开关------//
if(!empty($_POST["is_check_a"])){
	$is_check_a = $_POST["is_check_a"];//A区是否显示开关
	if( $is_check_a == "" || $is_check_a == 0 ){
		$is_check_a == false;
	}elseif( $is_check_a == 1 ){
		$is_check_a == true;
	}
}
//echo $is_check_a;die;
$id = -1;
$query = "SELECT id FROM pcshop_foot WHERE isvalid=true AND customer_id=$customer_id AND type=1 LIMIT 1";
$result= _mysql_query($query) or die('Query failed 15: ' . mysql_error());  
while( $row = mysql_fetch_object($result)){
	$id = $row->id;
}
if( $id < 0){
	$query = "INSERT INTO pcshop_foot(isvalid,customer_id,type,is_check) VALUES(true,$customer_id,1,'$is_check_a')";
}else{
	$query = "UPDATE pcshop_foot SET is_check='$is_check_a' WHERE isvalid=true AND $customer_id AND type=1";
}
//echo $query;die;
_mysql_query($query) or die('Query failed 25: ' . mysql_error()." query ==".$query);  
//-------先设置开关------//

//--------A区上传图片操作----------/
$uploadpath = '../../../Common/images/PcShop/Base/Footset/'.$customer_id;
if(!file_exists($uploadpath)){
	mkdir($uploadpath,0777);
}
if(!empty($_FILES)){

	for($i=0;$i<count($_FILES['image']['name']);$i++){

		if(!empty($_FILES['image']['name'][$i])){

			if($_FILES['image']['error'][$i] == UPLOAD_ERR_OK){

				$exten = extension($_FILES['image']['name'][$i]);
				$file_newname = time().rand(1000,9999).$customer_id.'.'.$exten;
				$imgpath = $uploadpath."/".$file_newname;
				_move_uploaded_file($_FILES['image']['tmp_name'][$i],$imgpath);
				chmod($imgpath, 0777);

				$id = -1;
				$query = "SELECT id FROM pcshop_foot_support WHERE isvalid=true AND customer_id=$customer_id AND type = $i+1 limit 1";
				$result = _mysql_query($query) or die('Query failed 33: ' . mysql_error());  
				while( $row = mysql_fetch_object($result)){
					$id = $row->id;
				}

				//id<0 则需要插入记录
				if( $id < 0){
					$sql = "INSERT INTO pcshop_foot_support(isvalid,customer_id,type,image) VALUES(true,$customer_id,$i+1,'$imgpath')";
				}else{
					$sql = "UPDATE pcshop_foot_support SET image = '$imgpath' WHERE customer_id=$customer_id AND type = $i+1";
				}
				_mysql_query($sql) or die('Query failed 40: ' . mysql_error()); 

				
			}else{
				echo '[上传出错!]';
			}
		 	

		}else{
			$img_num = $configutil->splash_new($_POST["img_num"]);

			if($img_num!=4){
				switch ($img_num) {
					case '1':
						$true_id  = '1';
						$false_id = '2,3,4';
						break;
					case '2':
						$true_id  = '1,2';
						$false_id = '3,4';
						break; 
					case '3':
						$true_id  = '1,2,3';
						$false_id = '4';
						break;
				}
                if($true_id && $false_id) {
                    $query = "UPDATE pcshop_foot_support SET isvalid=false WHERE type in($false_id)";
                    $sql = "UPDATE pcshop_foot_support SET isvalid=true WHERE type in($true_id)";
                    _mysql_query($sql);
                }
				//$query = "DELETE FROM pcshop_foot_support WHERE type in($false_id)";
			}elseif($img_num==4){
				$query = "UPDATE pcshop_foot_support SET isvalid=true WHERE $customer_id=$customer_id";
				//$query = "DELETE FROM pcshop_foot_support WHERE customer_id=$customer_id";
			}
			_mysql_query($query);
		}

	}
				
}
function extension($filename){
	$exten = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );
	return $exten;
}
//--------A区上传图片操作----------/

/*----------------------------先获取A区的设置-----------------------------*/

/*----------------------------B区的设置-----------------------------*/

//-------先设置开关------//
$is_check_b = 0;
if(!empty($_POST['is_check_b'])){
	$is_check_b = $configutil->splash_new($_POST["is_check_b"]);
}
$id = -1;
$query = "SELECT id FROM pcshop_foot WHERE isvalid=true AND customer_id=$customer_id AND type=2 LIMIT 1";
$result= _mysql_query($query) or die('Query failed 110: ' . mysql_error());  
while( $row = mysql_fetch_object($result)){
	$id = $row->id;
}
if( $id < 0){
	$query = "INSERT INTO pcshop_foot(isvalid,customer_id,type,is_check) VALUES(true,$customer_id,2,$is_check_b)";
}else{
	if(count($fb_name) == 0 || empty($fb_name)) {
		for ($a=0;$a<5;$a++) {
			$arr[$a]['title'] = '';
			$arr[$a]['link'] = '';
		}

		for ($i=0;$i<5;$i++) {
			$obj[$i]['title'] = '';
			$obj[$i]['fb_check'] = NULL;
			$obj[$i]['fb'] = $arr;
		}

		$fb_name_arr_ex = json_encode($obj,JSON_UNESCAPED_UNICODE);
	}
	$query = "UPDATE pcshop_foot SET is_check=$is_check_b WHERE isvalid=true AND $customer_id AND type=2";
}
_mysql_query($query) or die('Query failed 119: ' . mysql_error());  
//-------先设置开关------//

//-------保存连接与标题------//

$fb_name = array();
if(!empty($_POST['fb_name'])){
	$fb_name = $_POST["fb_name"];
}

$fb_title = array();
if(!empty($_POST['fb_title'])){
	$fb_title = $_POST["fb_title"];
}

$fb_links = array();
if(!empty($_POST['fb_links'])){
	$fb_links = $_POST["fb_links"];
}

$fb_name_arr = array(); 
foreach ($fb_name as $key => $value) {
	
	$fb_name_arr[$key]['title'] = $fb_name[$key];


	$fb_name_arr[$key]['fb_check'] = $_POST['fb_check_'.$key];

	$key_add++;

	$fb_title = array();
	if(!empty($_POST['fb_title'.$key_add])){
		$fb_title = $_POST["fb_title".$key_add];
	}	

	$fb_links = array();
	if(!empty($_POST['fb_links'.$key_add])){
		$fb_links = $_POST["fb_links".$key_add];
	}

	foreach ($fb_title as $key_fb => $value_fb) {
		$fb_name_arr[$key]['fb'][$key_fb]['title'] = $fb_title[$key_fb];
		$fb_name_arr[$key]['fb'][$key_fb]['link'] = $fb_links[$key_fb];
	}

}

//转成json数组
$fb_name_arr = json_encode($fb_name_arr,JSON_UNESCAPED_UNICODE);
$id = -1;
$query = "SELECT id FROM pcshop_foot_service WHERE isvalid=true AND customer_id=$customer_id AND type=0";
$result= _mysql_query($query)or die('Query Error 170: '.mysql_error()." Error query :".$query);
while( $row = mysql_fetch_object($result)){
	$id = $row->id;
}
if( $id < 0 ){
	$query = "INSERT INTO pcshop_foot_service(isvalid,customer_id,type,data_t) VALUES(true,$customer_id,0,'$fb_name_arr')";
}else{
	if(empty($fb_name)){
		$fb_name_arr = $fb_name_arr_ex;
	}
	$query = "UPDATE pcshop_foot_service SET data_t = '$fb_name_arr' WHERE isvalid=true AND customer_id=$customer_id AND type=0";
}
_mysql_query($query)or die('Query Error 180: '.mysql_error()." Error query :".$query);

//-------保存连接与标题------//

/*----------------------------B区的设置-----------------------------*/

/*----------------------------C区的设置-----------------------------*/
$fc_title = array();
if(!empty($_POST['fc_title'])){
	$fc_title = $_POST["fc_title"];
}
$fc_links = array();
if(!empty($_POST['fc_links'])){
	$fc_links = $_POST["fc_links"];
}
$fc_arr = array();
foreach ($fc_title as $key => $value) {
	$fc_arr[$key]['title'] = $fc_title[$key];
	$fc_arr[$key]['link']  = $fc_links[$key];
}

$fc_arr = json_encode($fc_arr,JSON_UNESCAPED_UNICODE);
$query = "SELECT id FROM pcshop_foot_service WHERE isvalid=true AND customer_id=$customer_id AND type=1";
$result= _mysql_query($query)or die('Query Error 170: '.mysql_error()." Error query :".$query);
while( $row = mysql_fetch_object($result)){
	$id = $row->id;
}
if( $id < 0 ){
	$query = "INSERT INTO pcshop_foot_service(isvalid,customer_id,type,data_t) VALUES(true,$customer_id,1,'$fc_arr')";
}else{
	$query = "UPDATE pcshop_foot_service SET data_t = '$fc_arr' WHERE isvalid=true AND customer_id=$customer_id AND type=1";
}
_mysql_query($query)or die('Query Error 212: '.mysql_error()." Error query :".$query);

if(!empty($_POST['copyright'])){
	$copyright = mysql_real_escape_string($_POST['copyright']);
	$id = -1;
	$query = "SELECT id FROM pcshop_foot_service WHERE isvalid=true AND customer_id=$customer_id AND type=2 LIMIT 1";
	$result= _mysql_query($query)or die('Query Error 217: '.mysql_error()." Error query :".$query);
	while( $row = mysql_fetch_object($result)){
		$id = $row->id;
	}
	if($id<0){
		$query = "INSERT INTO pcshop_foot_service(isvalid,customer_id,type,data_t) VALUES(true,$customer_id,2,'$copyright')";
	}else{
		$query = "UPDATE pcshop_foot_service SET data_t = '$copyright' WHERE isvalid=true AND customer_id=$customer_id AND type=2";
	}
	_mysql_query($query)or die('Query Error 227: '.mysql_error()." Error query :".$query);
}

/*----------底部举报电话等信息-----------*/
$identification = "";
if(!empty($_POST['identification'])){
	$identification = mysql_real_escape_string($_POST['identification']);
}
$telephone = "";
if(!empty($_POST['telephone'])){
	$telephone = mysql_real_escape_string($_POST['telephone']);
}
$data = array();
$data['identification'] = $identification;
$data['telephone']		= $telephone;
$data = json_encode($data,JSON_UNESCAPED_UNICODE);

$id = -1;
$query = "SELECT id FROM pcshop_foot_service WHERE isvalid=true AND customer_id=$customer_id AND type=3";
$result= _mysql_query($query)or die('Query Error 249: '.mysql_error()." Error query :".$query);
while( $row = mysql_fetch_object($result) ){
	$id = $row->id;
}
if( $id < 0 ){
	$query = "INSERT INTO pcshop_foot_service(isvalid,customer_id,type,data_t) VALUES(true,$customer_id,3,'$data');";
}else{
	$query = "UPDATE pcshop_foot_service SET data_t = '$data' WHERE isvalid = true AND customer_id = $customer_id AND type = 3";
}
_mysql_query($query)or die('Query Error 263: '.mysql_error()." Error query :".$query);

//var_dump($fc_arr);
/*----------------------------C区的设置-----------------------------*/
echo '<script>history.go(-1);</script>';




?>