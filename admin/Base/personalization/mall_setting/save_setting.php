<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
/* 参数获取 */
$shop_id = $configutil->splash_new($_POST["shop_id"]);
$is_showbottom_menu = 0;
if(!empty($_POST["is_showbottom_menu"])){//是否显示底部菜单
	$is_showbottom_menu = $configutil->splash_new($_POST["is_showbottom_menu"]); 
}
$is_pic = 0;
if(!empty($_POST["is_pic"])){//是否开启评论上传图片
   $is_pic = $configutil->splash_new($_POST["is_pic"]);	
}
$is_showdiscuss = 0;
if(!empty($_POST["is_showdiscuss"])){//是否显示好评中评差评
   $is_showdiscuss = $configutil->splash_new($_POST["is_showdiscuss"]);	
}
/*$isOpenSales = 0;
if(!empty($_POST["isOpenSales"])){//是否显示微商城销量
   $isOpenSales = $configutil->splash_new($_POST["isOpenSales"]);	
}*/
$isshowdiscount = 0;
if(!empty($_POST["isshowdiscount"])){//是否显示产品折扣
   $isshowdiscount = $configutil->splash_new($_POST["isshowdiscount"]);	
}
$nowprice_title = '';
if(!empty($_POST["nowprice_title"])){//"现金"属性自定义
   $nowprice_title = $configutil->splash_new($_POST["nowprice_title"]);	
}
$detail_template_type = 0;
if(!empty($_POST["detail_template_type"])){//详情页面模板选择
   $detail_template_type = $configutil->splash_new($_POST["detail_template_type"]);	
}

$sendstyle2 = 0;
if(!empty($_POST["sendstyle2"])){//产品列表模板选择
   $sendstyle2 = $configutil->splash_new($_POST["sendstyle2"]);	
}
$footmenu_type =0;
if(!empty($_POST["footmenu_type"])){//底部菜单模板选择
   $footmenu_type = $configutil->splash_new($_POST["footmenu_type"]);	
}
		
$isvp_switch =0;
if(!empty($_POST["isvp_switch"])){//vp值开关
   $isvp_switch = $configutil->splash_new($_POST["isvp_switch"]);	
}
		
			
 if($shop_id>0){
	$sql="update weixin_commonshops set
	is_showbottom_menu=".$is_showbottom_menu.",	
	is_pic=".$is_pic.",	
	is_showdiscuss=".$is_showdiscuss.",
	isshowdiscount=".$isshowdiscount.",
	nowprice_title='".$nowprice_title."',
	detail_template_type=".$detail_template_type.",
	list_type=".$sendstyle2.",
	footmenu_type=".$footmenu_type."
	where customer_id=".$customer_id." and isvalid=true";	
	_mysql_query($sql)or die(' Query failed1: ' . mysql_error()); 
	
	/* 个人中心和产品显示vp值 */
	$vp_id = -1;
	$query_vp = "select id from weixin_commonshop_vp_bases where isvalid=true and customer_id=".$customer_id." limit 0,1";
	$result_vp = _mysql_query($query_vp) or die('Query failed: ' . mysql_error());
	while ($row_vp = mysql_fetch_object($result_vp)) {
		$vp_id = $row_vp->id;
	}
	if(0 > $vp_id){
		$sql_vp 	  = "INSERT INTO weixin_commonshop_vp_bases(customer_id,isvalid,isvp_switch,createtime) VALUES(".$customer_id.",TRUE,".$isvp_switch.",now())";
		_mysql_query($sql_vp) or die('W74 Query failed : '.$user_id . mysql_error());
	}else{
		$sql_vp="update weixin_commonshop_vp_bases set isvp_switch=".$isvp_switch." where customer_id=".$customer_id." and isvalid = true";
		_mysql_query($sql_vp) or die('W78 Query failed : ' . mysql_error());  
	}
 }

$error = mysql_error();	
mysql_close($link);
echo $error; 
echo "<script>location.href='setting.php?customer_id=".$customer_id_en."';</script>"
?>