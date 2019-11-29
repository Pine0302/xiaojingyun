<?php
	header("Content-type: text/html; charset=utf-8");
	require('../../../../../weixinpl/config.php');
	$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');
	_mysql_query("SET NAMES UTF8");

	require_once('../../../../../weixinpl/common/common_ext.php');

	$op          = i2post("op",""); //操作
	$keyid       = i2post("keyid",-1); //当前操作id
	$key_sort    = i2post("key_sort",-1); //所在顺序
	$switch_id   = i2post("switch_id",-1); //需要交换顺序的id
	$switch_sort = i2post("switch_sort",-1); //需要交换顺序的排序

	$result = Array();  //初始化返回结果
	$result['errcode'] = -1;
	$result['errmsg'] = "未知结果";

	$error = "";

	switch ($op) {
		case 'up':
			$query = "select id,sort from navigation_setting_t where isvalid=true and sort>".$key_sort." and customer_id=".$customer_id." order by sort limit 1";
			$res = _mysql_query($query) or $error=mysql_error();
			while ($row = mysql_fetch_object($res)) {
			    $switch_id = $row->id;
			    $switch_sort = $row->sort;
			}
			if ($switch_id<0){
				$result['errcode'] = 10004;
				$result['errmsg'] = "已经到达导航栏首位";
				break;
			}
			$query1 = "update navigation_setting_t set sort=".$switch_sort." where id=".(int)$keyid;
			$query2 = "update navigation_setting_t set sort=".$key_sort." where id=".(int)$switch_id;
			_mysql_query($query1) or $error=mysql_error();
			_mysql_query($query2) or $error=mysql_error();

			if (empty($error)){
				$result['errcode'] = 0;
				$result['errmsg'] = "上移成功";
			}else{
				$result['errcode'] = 10004;
				$result['errmsg'] = "上移失败：".$error;
			}
			break;

		case 'down':
			$query = "select id,sort from navigation_setting_t where isvalid=true and sort<".$key_sort." and customer_id=".$customer_id." order by sort desc limit 1";
			$res = _mysql_query($query) or $error=mysql_error();
			while ($row = mysql_fetch_object($res)) {
			    $switch_id = $row->id;
			    $switch_sort = $row->sort;
			}
			if ($switch_id<0){
				$result['errcode'] = 10004;
				$result['errmsg'] = "已经到达导航栏末位";
				break;
			}
			$query1 = "update navigation_setting_t set sort=".$switch_sort." where id=".(int)$keyid;
			$query2 = "update navigation_setting_t set sort=".$key_sort." where id=".(int)$switch_id;
			_mysql_query($query1) or $error=mysql_error();
			_mysql_query($query2) or $error=mysql_error();

			if (empty($error)){
				$result['errcode'] = 0;
				$result['errmsg'] = "下移成功";
			}else{
				$result['errcode'] = 10004;
				$result['errmsg'] = "下移失败：".$error;
			}
			break;
		
		default:
			$result['errcode'] = 10004;
			$result['errmsg'] = "未知操作";
			break;
	}

	mysql_close($link);
	echo json_encode($result);
?>