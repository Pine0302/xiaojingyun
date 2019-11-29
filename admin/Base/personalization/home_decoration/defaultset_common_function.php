<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

if(!empty($_POST['op'])){
    $op =$configutil->splash_new($_POST['op']);
}
if(!empty($_POST['customer_id'])){
    $customer_id =$configutil->splash_new($_POST['customer_id']);
}
if(!empty($_POST['pagenum'])){
    $pagenum =$configutil->splash_new($_POST['pagenum']);
}
$customer_id = passport_decrypt((string)$customer_id);
$res = array();
$pagesize = 10;
$limit = " limit ".($pagenum-1)*$pagesize.",".$pagesize;
switch($op){
	case 'photo_text_message':				//图文消息
		$query = 'SELECT id,title FROM weixin_subscribes where isvalid=true and parent_id=-1 and is_message=0 and customer_id='.$customer_id.$limit;
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$temp = array();
		    $sub_id  =  $row->id ;
		    $title   = $row->title;  
			$temp['id']    = $sub_id;
			$temp['title'] = $title;
			array_push($res,$temp);
		}
		break;
	case 'video_message':					//视频消息
		$query = 'SELECT id,title,type,iframe,video_url FROM weixin_videos where isvalid=true  and  customer_id='.$customer_id.$limit;
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$temp = array();
		    $keyid =  $row->id ;
		    $title = $row->title;
			$temp['id']    = $keyid;
			$temp['title'] = $title;
			array_push($res,$temp);
		}
		break;
	case 'wei_singlepage':
		$query = 'SELECT id,name FROM site_singlepage where  c_id='.$customer_id." and isvalid = 1".$limit;
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$temp = array();
			$s_id =  $row->id ;
			$name = $row->name;
			$temp['id']    = $s_id;
			$temp['title'] = $name;
			array_push($res,$temp);
		}
}
//print_r($res);
echo json_encode($res);
?>