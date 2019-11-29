<?php

header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');  /*fenxiao下链接出错 11.13 by cdr*/

$op = '';
if($_POST["op"]){
	$op	=	$configutil->splash_new($_POST["op"]);	
}
$sort = 0;
if($_POST["sort"]){
	$sort	=	$configutil->splash_new($_POST["sort"]);	
}
$title = '';
if($_POST["title"]){
	$title	=	$configutil->splash_new($_POST["title"]);	
}

// if($op!='checkTitle'){

// var_dump($_POST);exit();
// }
$id = -1;
if($_POST["id"]){
	$id	=	$configutil->splash_new($_POST["id"]);	
	
}
$content = '';
if($_POST["content"]){
	$content	=	$configutil->splash_new($_POST["content"]);	
	
}
if($op=="add"){
	//添加
	$query_add = "insert into pcshop_merchants_settled_data(title,content,sort,isvalid,customer_id,createtime)values('".$title."','".$content."',".$sort.",true,".$customer_id.",now())";
	// echo $query_add;exit();
    // echo json_encode($link_b);
	_mysql_query($query_add)or die('Query failed'.mysql_error());
    
    echo "<script>location.href='document_management.php';</script>";
	
}
if($op=="update"){//修改

	//更新名称
	$query = "update pcshop_merchants_settled_data set title='".$title."',sort=".$sort.",content='".$content."' where isvalid=true and id=".$id."";
	
	_mysql_query($query)or die('Query failed'.mysql_error());
	echo "<script>location.href='document_management.php';</script>";
	
}
if($op=="del"){
	//删除
	$query_del="update pcshop_merchants_settled_data set isvalid=false where id=".$id."";
	//echo $query_del;
	_mysql_query($query_del)or die('Query failed'.mysql_error());	
	$error = mysql_error();

	if($error==0){
		 $res['code'] = 1;
		echo json_encode($res);
	}else{
		 $res['code'] = 0;
		echo json_encode($res);
	}
}

// if($op=="checkTitle"){
	// //检查模板名称是否重名
	// $tcount = 0;
	// $query = "select count(1) as tcount from pc_help_center_t where isvalid=true and id<>".$id." and name='".$name."'";
	// $result = _mysql_query($query) or die('checkTitle Query failed:'.mysql_error());
	// while($row = mysql_fetch_object($result)){
		// $tcount = $row->tcount;
		// break;
	// }
	// if($tcount>0){
		// $res['status'] = 1;
		// echo json_encode($res);
	// }else{
		// $res['status'] = 0;
		// echo json_encode($res);
	// }
// }
mysql_close($link);

?>