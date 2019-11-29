<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
// require('../../../../weixinpl/back_newshops/Mode/back_init.php');
// $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
// mysql_select_db(DB_NAME) or die('Could not select database');
// _mysql_query("SET NAMES UTF8");
// require('../../../../weixinpl/back_newshops/Mode/proxy_info.php');slide
$type=$configutil->splash_new($_POST['type']);
	$old_img=$_POST['old_img'];
	/*if($old_img){
		$temp=explode('weixinpl',$old_img);
		$f='../'.$temp[1];
		unlink($f);
	}*/
	$filename=date("YmdHis").rand(1,99).rand(1,99).rand(1,99);
	$filetype=substr($_FILES['up_img']['name'], strrpos($_FILES['up_img']['name'], "."),strlen($_FILES['up_img']['name'])-strrpos($_FILES['up_img']['name'], "."));
	$filetype=strtolower($filetype);
	if(($filetype!='.jpg')&&($filetype!='.png')&&($filetype!='.gif')&&($filetype!='.bmp')){
			$data['state']=0;
			$data['info']='类型错误';
			echo json_encode($data);
			exit ;
		}
	$filename=$filename.$filetype;
	$savedir='../../../up/slbmall/';
	if(!is_dir($savedir)){
		mkdir($savedir,0777);
	}
	$savedir.=$type.'/';
	if(!is_dir($savedir)){
		mkdir($savedir,0777);
	}
	$savedir.=$customer_id.'/';
	if(!is_dir($savedir)){
		mkdir($savedir,0777);
	}
	$savefile=$savedir.$filename;
	if (!_move_uploaded_file($_FILES['up_img']['tmp_name'], $savefile)){
		$data['state']=0;
		$data['info']='移动失败'.$savefile;
		echo json_encode($data);
		exit;
	}
	$data['state']=1;
	$data['info']='图片保存好了'.$savefile;
	$data['savedir']=$savefile;
	echo json_encode($data);
	exit;

?>