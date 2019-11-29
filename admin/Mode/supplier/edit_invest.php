<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');


$user_id   = $configutil->splash_new($_GET["user_id"]);
$category  = $configutil->splash_new($_GET["category"]);

if($_POST['op'] == 'change')
{
	$parent_id = $configutil->splash_new($_POST["parent_id"]);
	if($parent_id != -1 && $parent_id != '' && $parent_id != '-'){

		$sql0  = "select id from weixin_users where id=$parent_id and customer_id='{$customer_id}' limit 1";
		$res0  = _mysql_query($sql0) or die('Query failed0: ' . mysql_error());
		while ($row = mysql_fetch_object($res0)) 
		{
			$u_id = $row->id;
		}

		if($u_id == '' || $u_id== -1)
		{
			die(json_encode(array('status'=>0,'msg'=>'招商推荐人输入用户不存在，请重新输入！')));
		}	

		$sql1  = "select p.user_id,p.term_of_validity,u.name,u.weixin_name from promoters as p left join weixin_users as u on p.user_id=u.id where p.isvalid=1 and p.status=1 and p.user_id='{$parent_id}' and u.isvalid=1";
		$res1  = _mysql_query($sql1) or die('Query failed1: ' . mysql_error());
		while($row = mysql_fetch_object($res1)) 
		{
			$p_id  = $row->user_id;
			$p_time  = $row->term_of_validity;
			$name		 = $row->name;
			$weixin_name = $row->weixin_name;
		}
		if(!$p_id)
		{
			die(json_encode(array('status'=>0,'msg'=>'招商推荐人必须是推广员，请重新输入！')));
		}
		$nowtime = date('Y-m-d H:i:s');
		
		if($p_time<$nowtime)
		{
			die(json_encode(array('status'=>0,'msg'=>'抱歉，招商推荐人身份已过期，请重新输入！')));
		}		
	}
	mysql_close($link);
	die(json_encode(array('status'=>1,'msg'=>'获取成功','name'=>$name,'weixin_name'=>$weixin_name)));
	
}	

