<?php 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

//是否开启3d
$threed_open=0;
$sql_threed = "SELECT is_open FROM ".WSY_PROD.".3d_model_setting WHERE customer_id='".$customer_id."'";
$res_threed  = _mysql_query($sql_threed);
while ($row = mysql_fetch_object($res_threed) ){
	$threed_open = $row->is_open;
}
//渠道 3d素材
$is_threed_open = 0;
$query="select count(1) as is_threed_open from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='3D素材'";
$result = _mysql_query($query) or die('L274 is_threed_open Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $is_threed_open = $row->is_threed_open;
}

//渠道 新话费流量充值
$is_saiheyi 	= 0;
$is_OpenTrafficFlow = 0;
$query = "SELECT count(1) AS is_saiheyi FROM customer_funs cf INNER JOIN columns c WHERE c.isvalid=true AND cf.isvalid=true AND cf.customer_id=".$customer_id." AND c.sys_name='新话费流量充值' AND c.id=cf.column_id";
$result = _mysql_query($query) or die('W228 is_saiheyi Query failed: ' . mysql_error());  
while ( $row = mysql_fetch_object($result) ) {
	$is_saiheyi = $row->is_saiheyi;
	break;
}
if( $is_saiheyi > 0 ){
	$is_OpenTrafficFlow = 1;
}

//判断渠道是否开启微信卡券功能---start
$is_wercount 	= 0;
$is_OpenWechatCard = 0;
$query = "SELECT count(1) AS is_wercount FROM customer_funs cf INNER JOIN columns c WHERE c.isvalid=true AND cf.isvalid=true AND cf.customer_id=".$customer_id." AND c.sys_name='微信卡券' AND c.id=cf.column_id";
$result = _mysql_query($query) or die('W228 is_OpenShareholder Query failed: ' . mysql_error());  
while ( $row = mysql_fetch_object($result) ) {
	$is_wercount = $row->is_wercount;
	break;
}
if( $is_wercount > 0 ){
	$is_OpenWechatCard = 1;
}
//判断渠道是否开启微信卡券功能---end


/* 查看旅游卡渠道开关 start */
	$is_travelcard = 0;
	$query="select count(1) as is_travelcard from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='旅游卡'";
	$result = _mysql_query($query) or die('L274 is_travelcard Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
	   $is_travelcard = $row->is_travelcard;
	}	
/* 查看旅游卡渠道开关 end */

/* 查看公众号微社区渠道开关 start */
$is_community = 0;
$query="select count(1) as is_community from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='公众号微社区'";
$result = _mysql_query($query) or die('L274 is_travelcard Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $is_community = $row->is_community;
}
/* 查看公众号微社区渠道开关 end */

/* 屏蔽功能优化-自定义屏蔽功能 start */
$sql = "SELECT id,status,menu_name FROM columns_hidden_log WHERE status = 0";
$result_menu = _mysql_query($sql) or die('L274 is_travelcard Query failed: ' . mysql_error()); //查询出所有应需要屏蔽功能
$menu_temp = array();
while ($menu = mysql_fetch_object($result_menu))  //以查出数据自增ID作为屏蔽功能下标
{
    $menu_temp[] = $menu->menu_name;
}
$sql = "SELECT count('id') AS m FROM columns_hidden_log";
$result_menu = _mysql_query($sql) or die('L274 is_travelcard Query failed: ' . mysql_error()); //查询出所有应需要屏蔽功能
$count_menu = 0;
while ($menu = mysql_fetch_object($result_menu))  //以查出数据自增ID作为屏蔽功能下标
{
    $count_menu = $menu->m;
}
/* 屏蔽功能优化-自定义屏蔽功能 end */

?>