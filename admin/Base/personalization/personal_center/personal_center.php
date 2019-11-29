<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
require('../../../../../weixinpl/common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');
require('../../../../../weixinpl/auth_user.php');
require('../../../../../weixinpl/common/utility_4m.php');
$head=6;  
_mysql_query("SET NAMES UTF8");
// $new_baseurl = Protocol.$http_host; 
$new_baseurl = $protocol_http_host;

// 获取礼包列表
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
$database = new \Key\DB();
$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);

$sql = "SELECT package_name,id from package_list_t where customer_id='{$customer_id}' and isvalid=true ";
$package_list = $database->getData($sql);
foreach ($package_list as $key => $value) {
	$package_lists[] = "{$value['id']}_{$value['package_name']}";
}
// var_dump($package_lists);

$diy_temid=-1;
/*
//判断用户是否有自定义模板，没有就创建
$ccount=-1;
$check_custom="select count(1) as ccount from weixin_commonshop_diy_template where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result_check=_mysql_query($check_custom) or die ('check_custom faild' .mysql_error());
while($row=mysql_fetch_object($result_check)){
	$ccount=$row->ccount;
	if($ccount==0){
		$inser_custom="insert into weixin_commonshop_diy_template (customer_id,content,isused,isvalid,creatime) values ('".$customer_id."','-1',true,true,now())";
		$result_insert=_mysql_query($inser_custom) or die ('inser_custom faild' .mysql_error());
	
	}
}

$query_temid="select id,content from weixin_commonshop_diy_template where isvalid=true and isused=true and customer_id=".$customer_id." limit 0,1";
$result_query_temid=_mysql_query($query_temid) or die ('query_temid faild' .mysql_error());
while($row=mysql_fetch_object($result_query_temid)){
	$diy_temid=$row->id;
	$content=$row->content;
}

$k=0;
$customarr[]="";
$custom_query="select diy_tem_contid,title,imgurl,foreign_id,detail_id,mod_padding,mod_img_padding,css_type,pro_title_show,pro_title_twoline,pro_numshow,foot_position,placeholder,show_sale,type,link_type from weixin_commonshop_diy_template_content where isvalid=true and customer_id=".$customer_id." and LOCATE(diy_tem_contid,'".$content."') ORDER  BY FIND_IN_SET(diy_tem_contid,'".$content."')";

$result_custom=_mysql_query($custom_query) or die ('custom_query faild' .mysql_error());
while($row=mysql_fetch_object($result_custom)){
	$customarr[$k]['diy_tem_contid']=$row->diy_tem_contid;
	$customarr[$k]['title']=$row->title;
	$customarr[$k]['imgurl']=$row->imgurl;
	$customarr[$k]['foreign_id']=$row->foreign_id;
	$customarr[$k]['detail_id']=$row->detail_id;
	$customarr[$k]['mod_padding']=$row->mod_padding;
	$customarr[$k]['mod_img_padding']=$row->mod_img_padding;
	$customarr[$k]['css_type']=$row->css_type;
	$customarr[$k]['pro_title_show']=$row->pro_title_show;
	$customarr[$k]['pro_title_twoline']=$row->pro_title_twoline;
	$customarr[$k]['pro_numshow']=$row->pro_numshow;
	$customarr[$k]['foot_position']=$row->foot_position;
	$customarr[$k]['placeholder']=$row->placeholder;
	$customarr[$k]['show_sale']=$row->show_sale;
	$customarr[$k]['type']=$row->type;
	$customarr[$k]['link_type']=$row->link_type;
	$k++;
}

*/
$action="";
$temid="";
$customarr[]="";
// $customarr_city[]="";
$action=$configutil->splash_new($_GET["action"]);
if(isset($_GET["temid"])){
	$temid=$configutil->splash_new($_GET["temid"]);
}
switch($action){
	
	case "add":
		$inser_custom="insert into weixin_commonshop_diy_template (customer_id,content,isused,isvalid,creatime,name) values ('".$customer_id."','-1',false,true,now(),'自定义模板')";
		$result_insert=_mysql_query($inser_custom) or die ('inser_custom faild' .mysql_error());
		$diy_temid=mysql_insert_id();
		$query_temid="select name from weixin_commonshop_diy_template where id=".$diy_temid." and isvalid=true and customer_id=".$customer_id." limit 0,1";
		$result_query_temid=_mysql_query($query_temid) or die ('query_temid faild' .mysql_error());
		while($row=mysql_fetch_object($result_query_temid)){
			$name=$row->name;
		}
		$temid=$diy_temid;
	break;
	case "edit":
		$query_temid="select id,content,name,bgcolor from weixin_commonshop_diy_template where id=".$temid." and isvalid=true and customer_id=".$customer_id." limit 0,1";
		$result_query_temid=_mysql_query($query_temid) or die ('query_temid faild' .mysql_error());
		while($row=mysql_fetch_object($result_query_temid)){
			$diy_temid=$row->id;
			$content=$row->content;
			$name=$row->name;
			$bgcolor=$row->bgcolor;
		}
		$k=0;
		$custom_query="select diy_tem_contid,title,imgurl,foreign_id,detail_id,mod_padding,mod_img_padding,css_type,pro_title_show,pro_title_twoline,pro_numshow,foot_position,placeholder,show_sale,type,link_type,select_value,detail_value,detail_name,search_color,color,video_link,rolling_direction,rolling_speed,show_time_limit,city_name,start_time,end_time,province,sel_link_type,link,shop_type,sort_type,divide_type from weixin_commonshop_diy_template_content where isvalid=true and customer_id=".$customer_id." and LOCATE(diy_tem_contid,'".$content."') ORDER  BY FIND_IN_SET(diy_tem_contid,'".$content."')";
		$result_custom=_mysql_query($custom_query) or die ('custom_query faild' .mysql_error());
		while($row=mysql_fetch_object($result_custom)){
			$customarr[$k]['diy_tem_contid']=$row->diy_tem_contid;
			$customarr[$k]['title']=$row->title;
			$customarr[$k]['imgurl']=$row->imgurl;
			$customarr[$k]['foreign_id']=$row->foreign_id;	//固定链接ID
			$customarr[$k]['detail_id']=$row->detail_id;	//产品ID
			$customarr[$k]['mod_padding']=$row->mod_padding;	//模块间间距
			$customarr[$k]['mod_img_padding']=$row->mod_img_padding; //模块内间距
			$customarr[$k]['css_type']=$row->css_type;	//样式类型
			$customarr[$k]['pro_title_show']=$row->pro_title_show;	//显示产品名字
			$customarr[$k]['pro_title_twoline']=$row->pro_title_twoline;	//产品显示两行名字
			$customarr[$k]['pro_numshow']=$row->pro_numshow;	//分类产品显示的数量
			$customarr[$k]['foot_position']=$row->foot_position;	//底部菜单是否固定
			$customarr[$k]['placeholder']=$row->placeholder;	//搜索框提示语
			$customarr[$k]['show_sale']=$row->show_sale;	//显示销量
			$customarr[$k]['type']=$row->type;	//模板类型
			$customarr[$k]['link_type']=$row->link_type;	//链接类型
			$customarr[$k]['select_value']=$row->select_value;	//固定链接名字
			$customarr[$k]['detail_value']=$row->detail_value;	//产品的ID
			$customarr[$k]['detail_name']=$row->detail_name;	//产品名字
			$customarr[$k]['bg_color']=$row->search_color;  //搜索框背景颜色
			$customarr[$k]['color']=$row->color;	//文字颜色
			$customarr[$k]['video_link']=$row->video_link;	//文字颜色
			$customarr[$k]['rolling_direction']=$row->rolling_direction;	//滚动方向
			$customarr[$k]['rolling_speed']=$row->rolling_speed;	//滚动速度
			$customarr[$k]['show_time_limit']=$row->show_time_limit;	//显示时间限制
			$customarr[$k]['city_name']=$row->city_name;	//城市广告绑定的城市
			$customarr[$k]['start_time']=$row->start_time;	//展示开始时间
			$customarr[$k]['end_time']=$row->end_time;	//展示结束时间
			$customarr[$k]['province']=$row->province;	//城市广告绑定的省份
			$customarr[$k]['sel_link_type']=$row->sel_link_type;	//链接类型
			$customarr[$k]['link']=$row->link;	//链接
			$customarr[$k]['shop_type']=$row->shop_type;	//商城类型
			$customarr[$k]['sort_type']=$row->sort_type;	//排序类型
			$customarr[$k]['divide_type']=$row->divide_type;	//划分类型
			$k++;
		}

	break;
}


/*$typeLst = new ArrayList();
$query="select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $pt_id = $row->id;
   $pt_name = $row->name;
    
   $pstr = $pt_id."_".$pt_name;
   $catarr[]=$pt_id."_".$pt_name;
   $typeLst->add($pstr);
   
}

$typesize = $typeLst->size();*/

//图文信息
$imginfoLst = new ArrayList();
$query = 'SELECT id,title FROM weixin_subscribes where isvalid=true and parent_id=-1 and is_message=0 and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	  $sub_id =  $row->id ;
	  $title = $row->title;
	  
	  $pstr = $sub_id."_".$title;
	  $imginfo[]=$sub_id."_".$title;
      $imginfoLst->add($pstr);
}

$imginfosize = $imginfoLst->size();

//优惠券 start
$couponLst = new ArrayList();
//只有普通优惠券
$query = 'select id,is_open,title,NeedMoney,CanGetNum,Days,DaysType,class_type,user_scene from weixin_commonshop_coupons where isvalid=true and is_open=1  and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	  $coupon_id =  $row->id ;
	  $title = $row->title;
	  
	  $cstr = $coupon_id."_".$title;
	  $couponinfo[] = $coupon_id."_".$title;
      $couponLst->add($cstr);
}
$couponsize = $couponLst->size();


//优惠券 end
/*$cityarea_industry=[];
//城市商圈-美食
$cityareaCatererLst = new ArrayList();
$query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=2 and customer_id=".$customer_id;
$result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
while($supply_row = mysql_fetch_object($result)){
	$cityarea_id = $supply_row -> id;
	$cityarea_shop_name = $supply_row -> shop_name;		
	
	$pstr = $cityarea_id."_".$cityarea_shop_name;
	$cityfood[]=$cityarea_id."_".$cityarea_shop_name;
	$cityareaCatererLst->add($pstr);
}
$cityareaCaterersize = $cityareaCatererLst->size();*/
///城市商圈，渠道开关
$is_cityarea=0;
$is_cityarea_count=0;
$query="select count(1) as is_cityarea_count from customer_funs cf inner join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and (c.sys_name='商圈-美食' or c.sys_name='商圈-外卖' or c.sys_name='商圈-金融保险' or c.sys_name='商圈-酒店' or c.sys_name='商圈-ktv' or c.sys_name='商圈-线下商城' or c.sys_name='商圈-金融管理' or c.sys_name='商圈-教练服务')";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result)) {
   $is_cityarea_count = $row->is_cityarea_count;
}
if($is_cityarea_count>0){
   $is_cityarea=1;
}

$is_cityarea_caterer = 0;
$is_cityarea_ktv     = 0;
$is_cityarea_hotel   = 0;
$is_cityarea_shop    = 0;
$is_cityarea_finance = 0;
$is_cityarea_coach = 0;
if($is_cityarea){
	//城市商圈（美食），渠道开关	
	$query="select count(1) as is_cityarea_caterer from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-美食'";
	$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
	   $is_cityarea_caterer = $row->is_cityarea_caterer;
	}
	
	if($is_cityarea_caterer){
		//店铺数据
		$cityareaCatererLst = new ArrayList();
		$query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=2 and customer_id=".$customer_id;
		$result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
		while($supply_row = mysql_fetch_object($result)){
			$cityarea_id = $supply_row -> id;
			$cityarea_shop_name = $supply_row -> shop_name;		
			
			$pstr = $cityarea_id."_".$cityarea_shop_name;
			$cityfood[]=$cityarea_id."_".$cityarea_shop_name;
			$cityareaCatererLst->add($pstr);
		}
		$cityareaCaterersize = $cityareaCatererLst->size();
		//店铺数据 End
		$cityarea_industry[]="2_美食_0";
	}	
	//城市商圈（美食），渠道开关 End
	
	//城市商圈（KTV），渠道开关	
	$query="select count(1) as is_cityarea_ktv from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-ktv'";
	$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
	   $is_cityarea_ktv = $row->is_cityarea_ktv;
	}
	
	if($is_cityarea_ktv){
		//店铺数据
		$cityareaKTVLst = new ArrayList();
		$query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=30 and customer_id=".$customer_id;
		$result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
		while($supply_row = mysql_fetch_object($result)){
			$cityarea_id = $supply_row -> id;
			$cityarea_shop_name = $supply_row -> shop_name;		
			
			$pstr = $cityarea_id."_".$cityarea_shop_name;
			$cityktv[] = $cityarea_id."_".$cityarea_shop_name;
			$cityareaKTVLst->add($pstr);
		}
		$cityareaKTVsize = $cityareaKTVLst->size();
		//店铺数据 End
		$cityarea_industry[]="2_KTV_1";
	}	
	//城市商圈（KTV），渠道开关 End
	//
// echo  $is_cityarea_ktv."===<br>";
// var_dump($cityktv);
// die;

	//城市商圈（酒店），渠道开关
	$query="select count(1) as is_cityarea_hotel from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-酒店'";
	$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
	   $is_cityarea_hotel = $row->is_cityarea_hotel;
	}
	
	if($is_cityarea_hotel){
		//店铺数据
		$cityareaHotelLst = new ArrayList();
		$query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=60 and customer_id=".$customer_id;
		$result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
		while($supply_row = mysql_fetch_object($result)){
			$cityarea_id = $supply_row -> id;
			$cityarea_shop_name = $supply_row -> shop_name;		
			
			$pstr = $cityarea_id."_".$cityarea_shop_name;
			$cityhotel[] = $cityarea_id."_".$cityarea_shop_name;
			$cityareaHotelLst->add($pstr);
		}
		$cityareaHotelsize = $cityareaHotelLst->size();
		//店铺数据 End
		$cityarea_industry[]="2_酒店_2";
	}	
	//城市商圈（酒店），渠道开关 End

	//城市商圈（线下商城），渠道开关
	$query="select count(1) as is_cityarea_shop from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-线下商城'";
	$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
	   $is_cityarea_shop = $row->is_cityarea_shop;
	}
	
	if($is_cityarea_shop){
		//店铺数据
		$cityareaShopLst = new ArrayList();
		$query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=20 and customer_id=".$customer_id;
		$result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
		while($supply_row = mysql_fetch_object($result)){
			$cityarea_id = $supply_row -> id;
			$cityarea_shop_name = $supply_row -> shop_name;		
			
			$pstr = $cityarea_id."_".$cityarea_shop_name;
			$cityshop[] = $cityarea_id."_".$cityarea_shop_name;
			$cityareaShopLst->add($pstr);
		}
		$cityareaShopsize = $cityareaShopLst->size();
		//店铺数据 End
		$cityarea_industry[]="2_线下商城-首页_3";
		$cityarea_industry[]="2_线下商城-商家列表_4";
	}		
	//城市商圈（线下商城），渠道开关 End
	
    //城市商圈（金融管理），渠道开关
    $query="select count(1) as is_cityarea_finance from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-金融管理'";
    $result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
    while ($row = mysql_fetch_object($result)) {
       $is_cityarea_finance = $row->is_cityarea_finance;
    }

    if($is_cityarea_finance){
        //城市商圈（金融）
        $cityarea_industry[]="2_金融-贷款_5";
        $cityarea_industry[]="2_金融-信用卡_6";
        $cityarea_industry[]="2_金融-保险_7";        
        //城市商圈（金融） End
		$cityarea_industry[]="2_艺人服务_9";
    }

    //城市商圈（教练服务），渠道开关
	$query="select count(1) as is_cityarea_coach from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-教练服务'";
	$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
	   $is_cityarea_coach = $row->is_cityarea_coach;
	}				
	//城市商圈（教练服务），渠道开关 End
	
	if($is_cityarea_coach){
		$cityarea_industry[]="2_教练系统服务_8";
	}
   
	
}

//城市商圈（美食），渠道开关
/*$is_cityarea_caterer = 0;
$is_cityarea_ktv     = 0;
$is_cityarea_hotel   = 0;
$is_cityarea_shop    = 0;
$is_caterer_count	 = 0;
$query="select count(1) as is_caterer_count from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-美食' and c.id=cf.column_id";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result)) {
   $is_caterer_count = $row->is_caterer_count;
   break;
}
if($is_caterer_count>0){
   $is_cityarea_caterer=1;
   $cityarea_industry[]="2_美食";  //城市商圈美食
}*/

//品牌供应商店铺
$brandarr=[];//品牌供应商数据
$isOpenBrandSupply=0;//是否开启品牌供应商
$user_id=0;//供应商ID
$is_coupon=0;//是否开启优惠券
$brand_supply_name="";//供应商名称
$check_brand="select isOpenBrandSupply,is_coupon from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
$check_brand_result=_mysql_query($check_brand) or die ('check_brand faild ' .mysql_error());
while($row=mysql_fetch_object($check_brand_result)){
	$isOpenBrandSupply=$row->isOpenBrandSupply;
	$is_coupon=$row->is_coupon;
}
if($isOpenBrandSupply){//开启品牌供应商就查询品牌供应商店铺信息
	$brand="select user_id,brand_supply_name from weixin_commonshop_brand_supplys where isvalid=true and brand_status=1 and customer_id=".$customer_id."";
	$brand_result=_mysql_query($brand) or die ('brand faild' .mysql_error());
	while($row=mysql_fetch_object($brand_result)){
		$user_id=$row->user_id;
		$brand_supply_name=$row->brand_supply_name;
		$brandarr[]=$user_id."_".$brand_supply_name;
		
	}
	
}

$is_f2c = 0;
/* 查看f2c系统渠道开关 create by hzq */
$query="select count(1) as is_f2c from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='F2C系统'";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result)) {
   $is_f2c = $row->is_f2c;
}
/* 查看f2c系统渠道开关 end */

$is_ticket = 0;
/* 查看票务系统渠道开关 start */
$query="select count(1) as is_ticket from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='票务系统'";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result)) {
   $is_ticket = $row->is_ticket;
}
/* 查看票务系统渠道开关 end */

$fixedlink[]="-1_---------------请选择---------------";
$fixedlink[]="-16_首页";
$fixedlink[]="-6_全部产品";
$fixedlink[]="-2_新品上市";
$fixedlink[]="-3_热卖产品";
$fixedlink[]="-4_购物车";
$fixedlink[]="-8_个人中心";
$fixedlink[]="-18_我的订单";
$fixedlink[]="-9_我的微店";
$fixedlink[]="-7_产品分类页1";
$fixedlink[]="-17_产品分类页2";
$fixedlink[]="-37_产品分类页3";
$fixedlink[]="-47_产品分类页4";
$fixedlink[]="-33_区域批发商列表";
//$fixedlink[]="-5_限时抢购";
$fixedlink[]="-10_商城在线客服";
$fixedlink[]="-11_礼包列表";
$fixedlink[]="-12_VP产品";
$fixedlink[]="-15_积分专区";
$fixedlink[]="-20_人气团列表";
$fixedlink[]="-21_续费专区";
$fixedlink[]="-22_电商直播";
$fixedlink[]="-23_语音直播";
if($is_ticket){
    $fixedlink[]="-24_票务特价机票";
    $fixedlink[]="-25_票务特价火车票";
}
if($is_f2c>0){
$fixedlink[]="-26_F2C系统中心";
}
$fixedlink[]="-27_订货系统登录";
$fixedlink[]="-28_订货系统申请";
$fixedlink[]="-29_订货系统中心";
$fixedlink[]="-100_门店申请";
$fixedlink[]="-101_门店商城模式";
$fixedlink[]="-102_门店店铺模式";
$fixedlink[]="-19_拼团商品专区1";
$fixedlink[]="-30_拼团商品专区2";
$fixedlink[]="-31_拼团商品专区3";
$fixedlink[]="-34_积分签到";
$fixedlink[]="-35_积分商城";
$fixedlink[]="-95_砍价活动";
$fixedlink[]="-96_众筹新版";
/* 8.1分类 */ 
// $type_arr[] = '-1_---------------请选择---------------';
//分类排序
/*$sort_str = "";
$type_sort = "SELECT sort_str FROM weixin_commonshop_type_sort WHERE customer_id=".$customer_id;
$result_sort = _mysql_query($type_sort) or die ('type_sort failed:'.mysql_error());
while( $row_sort = mysql_fetch_object($result_sort) ){
   $sort_str = $row_sort -> sort_str;									   
}

$query = "select id, name from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=-1 and customer_id=".$customer_id;

if( $sort_str ){
	$query .= ' order by field(id'.$sort_str.')';  
}
$type_arr = array();
$ctype_arr = array();
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$pt_id = $row->id;
	$pt_name = $row->name;
	$type_str = $pt_id."_".$pt_name;
	$type_arr[] = $type_str;
	
	$query_child = "select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id." and parent_id=".$pt_id;
	$result_child = _mysql_query($query_child) or die("Query child failed:".mysql_error());
	while($row_child = mysql_fetch_object($result_child)){
		$pc_id = $row_child->id;
		$pc_name = $row_child->name;
		$ctype_str = $pc_id.'_'.$pc_name;
		$ctype_arr[$pt_id][] = $ctype_str;
		
		$query_child3 = "select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id." and parent_id=".$pc_id;
		$result_child3 = _mysql_query($query_child3) or die("Query child failed3:".mysql_error());
		while($row_child3 = mysql_fetch_object($result_child3)){
			$pc_id3 = $row_child3->id;
			$pc_name3 = $row_child3->name;
			$ctype_str = $pc_id3.'_'.$pc_name3;
			$ctype_arr[$pc_id][] = $ctype_str;
			
			$query_child4 = "select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id." and parent_id=".$pc_id3;
			$result_child4 = _mysql_query($query_child4) or die("Query child failed4:".mysql_error());
			while($row_child4 = mysql_fetch_object($result_child4)){
				$pc_id4 = $row_child4->id;
				$pc_name4 = $row_child4->name;
				$ctype_str = $pc_id4.'_'.$pc_name4;
				$ctype_arr[$pc_id3][] = $ctype_str;
			}
		}
	}		
}*/
/* 8.1分类 */ 

/* 线下商城产品分类 */
$cityarea_shop_protype_arr = array();
$query = "select id,name from weixin_cityarea_shop_types where is_shelves=1 and isvalid=true and customer_id=".$customer_id." order by asort desc";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$st_id = $row->id;
	$st_name = $row->name;
	$shop_protype_str = $st_id."_".$st_name;
	$cityarea_shop_protype_arr[] = $shop_protype_str;
}
/* 线下商城产品分类 */

/* 线下商城店铺分类 */
$cityarea_shop_type_arr = array();
$query = "select id,shoptype_name from weixin_cityarea_shop_shoptypes where customer_id=".$customer_id." and isvalid=true order by sort desc,id desc";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$pt_id = $row->id;
	$pt_name = $row->shoptype_name;
	$shop_type_str = $pt_id."_".$pt_name;
	$cityarea_shop_type_arr[] = $shop_type_str;
}
/* 线下商城店铺分类 */

/* 线下商城店铺列表 */
$cityarea_shop_arr = array();
$query = "select id,shop_name from weixin_cityarea_supply where customer_id=".$customer_id." and (types=20 or (types=21 and is_freeze = 0)) and isvalid=true";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$cs_id = $row->id;
	$cs_name = $row->shop_name;
	$shop_str = $cs_id."_".$cs_name;
	$cityarea_shop_arr[] = $shop_str;
}
/* 线下商城店铺列表 */

/*获取省、市*/
$areaData		 = [];
$area_id 		 = -1;
$area_name 		 = '';
$area_MergerName = '';
$area_LevelType  = 1;
$n = 0;
$query_address = "select ID,name,MergerName,LevelType,ParentId from address where ID != 100000 and LevelType != 3 order by ID desc";
$result_address = _mysql_query($query_address) or die('Query_address failed:'.mysql_error());
while($row_address = mysql_fetch_object($result_address)){
	$area_id 		 = $row_address->ID;
	$area_name 		 = $row_address->name;
	$area_MergerName = $row_address->MergerName;
	$area_LevelType  = $row_address->LevelType;
	$area_ParentId 	 = $row_address->ParentId;
	
	$areaData[$n]['id'] 		= $area_id;
	$areaData[$n]['name'] 	  	= $area_name;
	$areaData[$n]['MergerName'] = $area_MergerName;
	$areaData[$n]['LevelType']  = $area_LevelType;
	$areaData[$n]['parentId']   = $area_ParentId;
	
	$n++;
}
//var_dump($areaData);
/*获取省、市*/

//微视直播房间
$room_link = array();
$query_weishi = "select r.id,r.title from weixin_os_room r inner join weixin_os_anchor a on r.anchor_id=a.id where r.isvalid=true and a.isvalid=true and a.customer_id=".$customer_id;
$result_weishi = _mysql_query($query_weishi) or die('query_weishi failed:'.mysql_error());
while( $row_weishi = mysql_fetch_object($result_weishi) ){
	$room_id 	     = $row_weishi -> id;		//模板id
	$room_title 	 = $row_weishi -> title;	//模板名称
	$room_link[] = $room_id."_".$room_title;
}

//获取已启用的模板

$query_open_tem = "SELECT id,name FROM weixin_commonshop_diy_template WHERE is_open=true AND isvalid=true AND customer_id=".$customer_id." AND id!=".$temid." ORDER BY id DESC";
$result_open_tem = _mysql_query($query_open_tem) or die('Query_open_tem failed:'.mysql_error());
while( $row_open_tem = mysql_fetch_object($result_open_tem) ){
	$template_link[] = $row_open_tem->id ."_". $row_open_tem->name;
}

//获取选择框链接
require_once($_SERVER['DOCUMENT_ROOT']."/weixinpl/common/utility_common.php");
$shopLink = new shopLink_Utlity($customer_id);
$link_arr = $shopLink->getSelectLink(array(3), 1);	//3：产品分类
$type_arr = $link_arr['type_arr'];
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
 <link rel="stylesheet" type="text/css" href="css/com.css">
<link rel="stylesheet" type="text/css" href="css/mod.css">
<link rel="stylesheet" type="text/css" href="css/custom.css">
<link rel="stylesheet" type="text/css" href="css/custom2.css">
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme;?>.css">
<link href="../../../../back_commonshop/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/style.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/operamasks-ui.css" rel="stylesheet" type="text/css"> 
<link rel="stylesheet" media="screen" type="text/css" href="css/layout.css" />
<link rel="stylesheet" type="text/css" href="css/colorpicker.css">
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentGreen.css">--><!--内容CSS配色·绿色-->
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentOrange.css">--><!--内容CSS配色·橙色-->
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentbgreen.css">--><!--内容CSS配色·蓝绿-->
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentGGreen.css">--><!--内容CSS配色·草绿-->
<script type="text/javascript" src="js/jquery-1.12.1.min.js"></script>
</head>

<body>
<!--内容框架开始-->
<div class="WSY_content" id="WSY_content_height">
<!--微商城统计代码结束-->

<style type="text/css">
/*蓝色*/
.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#06a7e1;border:solid 1px #0b91c2;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#017ca9;cursor:pointer;}
.input_butn01 input{width:268px;}
.leftA01 .leftA01_dl dd .tj{background:#07a7e1;border:solid 1px #0b91c2;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#0b91c2;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#06a7e1;color:#fff;}
.marquee{height:40px;line-height:40px;overflow:hidden;margin:0 auto;}
.marquee ul{height:40px;line-height:40px;}
.marquee ul li{padding:0 10px;line-height:40px;height:40px;overflow:hidden;text-align:left;text-overflow:ellipsis;white-space:nowrap;}
.input.xlarge{width:200px;}
.imgnav-info{width:550px;}
.search-input{width:150px;height: 18px;margin-right:0;}
.search-input-btn{cursor: pointer;padding: 6px 11px;background: #3899eb;color: #FFFFFF;margin-left: 5px;vertical-align: top;border-radius: 3px;-webkit-border-radius:3px;-moz-border-radius:3px;border: none;}
.colorpicker{z-index: 9}
.num-input{width:100px;height:22px;line-height:22px;margin:0 10px;padding-left:5px;}

.top-main01{width: 100%;background-color: #FF6600;position: relative;text-align: center;font-size: 13px;padding: 5px 0;}
    	.top-set{position: absolute;right: 5px;top: 5px;font-size: 13px;}
    	.top-imgBox{text-align:center;width: 60px;height: 60px;line-height: 60px;background-color: #FFF;border: 2px solid gray;border-radius: 50%;display: inline-block;margin-bottom: 5px;}
        .top-userName{font-size: 15px;font-weight: bold;}
        
        .top-main02{background-color: #FF9900;display: flex;padding: 10px;font-size: 13px;align-items: center;}
        .top-imgBox02{display: inline-block;background-color: #FFF;width: 75px;height: 75px;line-height: 75px;border: 2px solid gray;border-radius: 5px;text-align: center;overflow: hidden;margin-right: 10px;flex-shrink: 0;}
        .top-name02{font-size: 15px;font-weight: bold;}
        .top-info02 p{margin: 3px 0;}
        
        .top-main03{background-color: #FF6600;font-size: 13px;}
        .top-mainInner{position: relative;display: flex;padding: 10px;align-items: center;}
        .top-info03{margin-left: 10px;}
        .top-info03 p{margin: 3px 0;}
        .divide{height: 1px;background-color: #FFFFFF;}
        .top-status{padding: 5px 15px;}
        
        .top-main04{width: 100%;background-color: #FF6600;position: relative;text-align: center;font-size: 13px;padding-top: 10px;}
        .top-userName04{background: #FFFFFF;padding: 3px 7px;border-radius: 15px;font-size: 15px;font-weight: bold;display: inline-block;}
        .top-status04{padding: 5px 15px;background-color: #FFB27F;color: #803300;}
        .name04{position: absolute;top: 60px;text-align: center;width: 100%;}
        .top-info04{margin-top: 10px;}
        
        .topimg{width: 100%;height: 110px;}
        .top-set05{text-align: right;padding: 10px 8px 0 0;}
        .top-main05{font-size: 12px;color: #FFF;position: relative;background-size:100% 100%;background-repeat:no-repeat;}
        .status05 {display: flex;align-items: center;justify-content: center;height: 20px;}
        .status05 span{color:#FFF;}
        .status05 img{width: 16;height:15px;margin: 0 3px 0 8px;}
        .head-img05{position: absolute;left: 0;width: 100%;text-align: center;top: 75px;}
        .head-content{width: 65px;height: 65px;background: #FFFFFF;border-radius: 50%;border: 2px solid gray;margin: 0 auto;font-size: 12px;color: #000000;line-height: 65px;}
        .name05{font-size: 14px;color: #000000;padding:35px 0 5px 0;text-align: center;}
        
        .back-img{background-size:100% 100%;}
        .head-delete{font-size:13px;background:rgba(0,0,0,0.6);width:100%;height:20px;line-height:20px;position:absolute;left:0;bottom:0;display:none;color:#FFF;}
        .add-img{position:relative;}
        .add-img:hover .head-delete{display:block;}
</style>
       <!--列表内容大框开始-->
	<div class="WSY_columnbox" style="position:relative">
    	<!--列表头部切换开始-->
    	<?php
			include("../../../../../weixinpl/back_newshops/Base/personalization/basic_head.php"); 
		?>
        <!--列表头部切换结束-->
         
    <!--首页设置代码开始-->
<div class="main">
    <div class="WSY_data">
    	<div class="WSY_homebox">
        	<div class="WSY_homeleft">
            	<li class="WSY_homeleft_top">
                	<p></p>
                </li>
                <li class="WSY_homeleft_middle" style="background:<?php echo $bgcolor;?>">
                <!--模块开始-->

                <!--模块结束-->
                </li>
                <li class="WSY_foot" style="background:<?php echo $bgcolor;?>">
                </li>
            </div>
            <div class="WSY_ctrl">
            <div class="diy-ctrl-item-b" data-origin="pagetitle" style="display:block;">  
                <div class="formitems">  
                    <label class="fi-name">模板名称：</label>  
                    <div class="form-controls">  
                        <input type="text" name="tempname" id="tempname" class="input j-pagetitle-ipt" value="<?php echo $name;?>">
                    </div>
                </div>
                <div class="formitems">
                    <label class="fi-name">页面背景色：</label> 
                    <div class="form-controls">
                        <div class="colorSelector" id="bgColor"><div style="background-color: <?php echo $bgcolor;?>"></div></div>
                        <input type="hidden" value="<?php echo $bgcolor;?>" name="bgColor" id="colorbg">
                    </div>
                </div>
				<p class="imgnav-select">
				<iframe src="default_img.php?customer_id=<?php echo $customer_id_en; ?>&temid=<?php echo $temid; ?>" height=200 width=100% FRAMEBORDER=0 SCROLLING=no></iframe>
            	</p>
            </div>
            </div>
        </div>
        <div class="diy-actions">
                <div class="diy-actions-addModules clearfix">
                    <a data-type="0" class="j-page-addModule" href="javascript:;"><i class="gicon-cog"></i>页面设置</a>
                    <a data-type="7" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>分割线</a>
                    <a data-type="13" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>滚动公告栏</a> 
                    <a data-type="17" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>个人中心头部</a>
                    <a data-type="21" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>订单显示</a>
                    <a data-type="22" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>数据显示</a>
                    <a data-type="23" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>功能模块</a>
                    <div class="clear"></div>
                </div>
                <div class="diy-actions-submit">
                    <a href="javascript:;" class="save-btn diy_btn" id="j-savePage" >保存</a>
                </div>
        </div>
    </div>
</div>
    <!--首页设置代码结束-->
</div>
<!--内容框架结束-->
    <!-- diy common start -->
<!--编辑框-->
<script type="dot-template" id="type_conitem">
    <div class="type-conitem">
       {{= it.html }}
        <div class="type-conitem-action">
            <div class="type-conitem-action-btns">
               
                <a href="javascript:;" class="type-conitem-btn type-del j-del">删除</a>
            </div>
        </div>
    </div>
</script>
<!--编辑框-->
<script type="dot-template" id="type_ctrl">
    <div class="type-ctrl-item" data-origin="item" style ="margin-bottom:50px">
        {{= it.html }}
    </div>
</script>





<!--订单显示-->
<script  type="dot-template" id="type_con_21">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
<div class="floor clearfix">
{{? it.content.css_type==1}}
    <div class="div-row">
    	<div class="img">
    	<img src="{{=it.content.icon_pic}}">
    	</div>
    	<p>{{=it.content.li_title}}</p>
    </div>
    {{?? it.content.css_type==2}}
    	<div class="div-row">
	    	<div class="img">
	    	<img src="{{=it.content.icon_pic}}">
	    	</div>
	    	<p>{{=it.content.li_title}}</p>
    	</div>
		<ul class="order-list">
		{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
		    <li>
		    <div class="img">
		    	<img src="{{=it.content.dataset[i].pic}}">
		    </div>
		    <p>{{=it.content.dataset[i].title}}</p>
		    </li>
		{{ } }}
		</ul>
    {{??}}
{{?}}
</div>
</div>
</script>
<script type="dot-template" id="type_ctrl_21">
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
<div class="formitems">
    <label class="fi-name">样式选择：</label> 
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="css_type" value="1" {{? it.content.css_type==1}} checked{{?}}>样式一</label>
            <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>样式二</label>
        </div>
    </div>
</div>
<div class="formitems">  
    <label class="fi-name">选择分类：</label>  
    <div class="form-controls">
        <div class="droplist">
            <select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
			<option value="商城订单" {{? it.content.select_value=='商城订单'}}selected="selected"{{?}}>商城订单</option>
			<option value="线下商城-自提订单" {{? it.content.select_value=='线下商城-自提订单'}}selected="selected"{{?}}>线下商城-自提订单</option>
			<option value="到店付订单" {{? it.content.select_value=='到店付订单'}}selected="selected"{{?}}>到店付订单</option>

            </select>
        </div>
        <input type="hidden" class="j-verify" name="item_id" value="">
        <span class="fi-help-text j-verify-linkType"></span>
    </div>
</div>

<div class="formitems">  
    <label class="fi-name">自定义订单名称：</label>  
    <div class="form-controls">
        <input type="text" name="title1"  class="input xlarge" value="{{=it.content.li_title}}" maxlength="10">
    </div>
</div>

<div class="formitems">  
    <label class="fi-name">自定义订单名称：</label>  
    <div class="form-controls">
    <div class="formitems">                 
	    <label class="note">建议尺寸：40*40,100k以内</label>
    </div>
		<div class="fl" style="margin-right:20px;">
			<div class="imgnav j-selectimg">
				<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img10" enctype="multipart/form-data" method="post" onsubmit="return saveReport(10);">
					<input type="hidden" name="getImg" id='getImg10' value="{{=it.content.icon_pic}}">
					<p class="imgnav-select">
						<input type="file" size="20" name="upfile2" id="upfile2" class="up" >
						<img src="{{=it.content.icon_pic}}">
					</p>
					<input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
					<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
					<input type="hidden" name="img_sort" value="0">
					<input type="hidden" name="uptypes" value="image/jpg,image/jpeg,image/png,">

				</form>
				</div>
			<span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
		</div>
		
    </div>
</div>

<div class="formitems" style="width:550px;{{? it.content.css_type==1}}display:none;{{?}}">  
    <label class="fi-name">自定义订单名称：</label>  
    <div class="form-controls">
    <div class="formitems">                 
	    <label class="note">建议尺寸：46*46,100k以内</label>
    </div>
    </div>
<ul>
{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
<li class="up-icon">
	<p class="fl" style="width:50px;text-align:right;margin-right:10px;line-height:30px;">{{=it.content.dataset[i].title}}：</p>
    <div class="fl" style="margin-right:20px;">
			<div class="imgnav j-selectimg">
				<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{=i}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{=i}});">
					<input type="hidden" name="getImg" id='getImg{{=i}}' value="{{=it.content.dataset[i].pic}}">
					<p class="imgnav-select">
						<input type="file" size="20" name="upfile2" id="upfile2" class="up" >
						<img src="{{=it.content.dataset[i].pic}}">
					</p>
					<input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
					<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
					<input type="hidden" name="img_sort" value="0">
					<input type="hidden" name="uptypes" value="image/jpg,image/jpeg,image/png,">

				</form>
				</div>
			<span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
		</div>
{{ } }}
</li>
</ul>
</div>
</script>
<!--订单显示-->
<!--数据模块-->
<script  type="dot-template" id="type_con_22">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
	{{? it.content.css_type==2}}
	{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
		<div class="div-row">
	    	<div class="img">
	    	<img src="{{=it.content.dataset[i].pic}}">
	    	</div>
	    	<p style="color:{{=it.content.dataset[i].color}}">{{=it.content.dataset[i].title}} <span style="color:{{=it.content.dataset[i].color1}}">200.00元</span></p>
    	</div>  
	{{ } }}
    
    {{?? it.content.css_type==1}}
    	
    	<ul class="data-list">
    		{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
    		<li>
    			<p style="color:{{=it.content.dataset[i].color}}">{{=it.content.dataset[i].title}}</p>
    			<span style="color:{{=it.content.dataset[i].color1}}">200.00元</span>
    		</li>
    		{{ } }}
    	</ul>
    	
    {{??}}
{{?}}
</div>
</script>
<script type="dot-template" id="type_ctrl_22">
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
<div class="formitems">  
        <label class="fi-name">样式选择：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>样式一</label>
                <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>样式二</label>
            </div>
        </div>
</div>

{{? it.content.css_type==1}}
<div class="formitems">  
        <label class="fi-name">数据显示个数：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="data_num" value="2"{{? it.content.data_num==2}} checked{{?}}>2个</label>
                <label><input type="radio" name="data_num" value="3"{{? it.content.data_num==3}} checked{{?}}>3个</label>
                <label><input type="radio" name="data_num" value="4"{{? it.content.data_num==4}} checked {{?}}>4个</label>
                <label><input type="radio" name="data_num" value="5"{{? it.content.data_num==5}} checked{{?}}>5个</label>
            </div>
        </div>
</div>
{{?}}
<ul class="ctrl-item-list"> 
{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
    <li class="ctrl-item-list-li clearfix">
        <div class="fl" style="{{? it.content.css_type==1}}display:none{{?}}">
            <div class="imgnav j-selectimg">			
			<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{=i}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{=i}});">
				<input type="hidden" name="getImg" id='getImg{{=i}}' value="{{=it.content.dataset[i].pic}}">
                <p class="imgnav-select">
                    <input type="file" size="20" name="upfile2" id="upfile2" class="up" >
                    <img src="{{=it.content.dataset[i].pic}}">
                </p>
				<input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
				<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
				<input type="hidden" name="img_sort" value="{{=i}}">
                
			</form>
            </div>
            <span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
        </div>
		
        <div class="fl imgnav-info">
            <div class="formitems">  
                <label class="fi-name">选择数据类型：</label>  
                <div class="form-controls">
                    <div class="droplist">
                        
                        <select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
                       		<option value="零钱" {{? it.content.dataset[i].select_value=='零钱'}}selected="selected"{{?}}>零钱</option>
						<option value="线下商城-自提订单" {{? it.content.dataset[i].select_value=='线下商城-自提订单'}}selected="selected"{{?}}>线下商城-自提订单</option>
						<option value="到店付订单" {{? it.content.dataset[i].select_value=='到店付订单'}}selected="selected"{{?}}>到店付订单</option>
                        </select>
						
                    </div>
					
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
                </div>
            </div>
			<div class="formitems">
				<label class="fi-name">文字自定义：</label>
				<div class="form-controls">
					<input type="text" name="title" id="title_{{=i}}" value="{{= it.content.dataset[i].title}}" class="input xlarge" maxlength="10"/>
				</div>
			</div>
			<div class="formitems">
                <label class="fi-name">字体颜色：</label> 
                <div class="form-controls">
                    <div class="colorSelector"><div color="color" style="background-color: {{=it.content.dataset[i].color}}"></div></div>
                </div>
            </div>
            <div class="formitems">
                <label class="fi-name">数字颜色：</label> 
                <div class="form-controls">
                    <div class="colorSelector1" style="position: relative;width: 36px;height:36px;background: url(images/select.png);"><div color="color" style="    position: absolute;top: 3px;left: 3px;width: 30px;height: 30px;background: url(images/select.png) center;background-color: {{=it.content.dataset[i].color1}}"></div></div>
                </div>
            </div>
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">1080*540 px 图片大小不超过200K</label>
            </div>
            
            
        </div>
        <div class="ctrl-item-list-actions">
            <a href="javascript:;" title="上移" class="j-moveup"><i class="gicon-arrow-up"></i></a>
            <a href="javascript:;" title="下移" class="j-movedown"><i class="gicon-arrow-down"></i></a>
            <a href="javascript:;" title="删除" class="j-del" style="{{? it.content.css_type==1}}display:none{{?}}"><i class="gicon-remove"></i></a>
        </div>
    </li>
    {{ } }}
    <li style="{{? it.content.css_type==1}}display:none{{?}}" class="ctrl-item-list-add" title="添加">+</li>
</ul>
</script>
<!--数据模块-->
<!--功能模块-->
<script  type="dot-template" id="type_con_23">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
{{? it.content.css_type==1}}
	
	<ul class="fun-list line{{= it.content.data_num}}">
    		{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
    		<li>
    			<img src="{{=it.content.dataset[i].pic}}">
    			<p style="color:{{=it.content.dataset[i].color}}">{{=it.content.dataset[i].title}}</p>
    		</li>
    		{{ } }}
    	</ul>
    
    {{?? it.content.css_type==2}}
    	{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
		<div class="div-row">
	    	<div class="img">
	    	<img src="{{=it.content.dataset[i].pic}}">
	    	</div>
	    	<p style="color:{{=it.content.dataset[i].color}}">{{=it.content.dataset[i].title}}</p>
    	</div>  
	{{ } }}
	
    {{??}}
{{?}}
</div>
</script>
<script type="dot-template" id="type_ctrl_23">
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
<div class="formitems">  
        <label class="fi-name">样式选择：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>样式一</label>
                <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>样式二</label>
            </div>
        </div>
</div>

{{? it.content.css_type==1}}
<div class="formitems">  
        <label class="fi-name">数据显示个数：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="data_num" value="4"{{? it.content.data_num==4}} checked{{?}}>每行4个</label>
                <label><input type="radio" name="data_num" value="3"{{? it.content.data_num==3}} checked{{?}}>每行3个</label>
            </div>
        </div>
</div>
{{?}}
<ul class="ctrl-item-list"> 
{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
    <li class="ctrl-item-list-li clearfix">
        <div class="fl">
            <div class="imgnav j-selectimg">			
			<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{=i}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{=i}});">
				<input type="hidden" name="getImg" id='getImg{{=i}}' value="{{=it.content.dataset[i].pic}}">
                <p class="imgnav-select">
                    <input type="file" size="20" name="upfile2" id="upfile2" class="up" >
                    <img src="{{=it.content.dataset[i].pic}}">
                </p>
				<input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
				<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
				<input type="hidden" name="img_sort" value="{{=i}}">
                
			</form>
            </div>
            <span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
        </div>
		
        <div class="fl imgnav-info">
            <div class="formitems">  
                <label class="fi-name">选择数据类型：</label>  
                <div class="form-controls">
                    <div class="droplist">
                        
                        <select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
                       		<option value="我的资产" {{? it.content.dataset[i].select_value=='我的资产'}}selected="selected"{{?}}>我的资产</option>
						<option value="我的特权" {{? it.content.dataset[i].select_value=='我的特权'}}selected="selected"{{?}}>我的特权</option>
						<option value="我的团队" {{? it.content.dataset[i].select_value=='我的团队'}}selected="selected"{{?}}>我的团队</option>
                        </select>
						
                    </div>
					
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
                </div>
            </div>
			<div class="formitems">
				<label class="fi-name">文字自定义：</label>
				<div class="form-controls">
					<input type="text" name="title" id="title_{{=i}}" value="{{= it.content.dataset[i].title}}" class="input xlarge" maxlength="10"/>
				</div>
			</div>
			<div class="formitems">
                <label class="fi-name">字体颜色：</label> 
                <div class="form-controls">
                    <div class="colorSelector"><div color="color" style="background-color: {{=it.content.dataset[i].color}}"></div></div>
                </div>
            </div>
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">65*65 px 图片大小不超过200K</label>
            </div>
            
            
        </div>
        <div class="ctrl-item-list-actions">
            <a href="javascript:;" title="上移" class="j-moveup"><i class="gicon-arrow-up"></i></a>
            <a href="javascript:;" title="下移" class="j-movedown"><i class="gicon-arrow-down"></i></a>
            <a href="javascript:;" title="删除" class="j-del"><i class="gicon-remove"></i></a>
        </div>
    </li>
    {{ } }}
    <li class="ctrl-item-list-add" title="添加">+</li>
</ul>
</script>
<!--功能模块-->

<!--分割线-->
<script type="dot-template" id="type_con_7">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
    <div class="members_con">
        <a title="{{=it.content.dataset[0].title}}" href="{{=it.content.dataset[0].link}}" ><img src="{{=it.content.dataset[0].pic}}" width="100%" /></a>
    </div>
</div>
</script>
<script type="dot-template" id="type_ctrl_7">
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
<ul class="ctrl-item-list"> 
{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
    <li class="ctrl-item-list-li clearfix">
        <div class="fl">
            <div class="imgnav j-selectimg">
            <form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{=i}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{=i}});">
                <input type="hidden" name="getImg" id='getImg{{=i}}' value="{{=it.content.dataset[i].pic}}">
                <p class="imgnav-select">
                    <input type="file" size="20" name="upfile2" id="upfile2" class="up" >
                    <img src="{{=it.content.dataset[i].pic}}">
                </p>
                <input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
                <input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
                <input type="hidden" name="img_sort" value="{{=i}}">
                
            </form>
            </div>
            <span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
        </div>
        <div class="fl imgnav-info">
            <div class="formitems">  
                <label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="1" {{? it.content.dataset[i].sel_link_type == 1}}checked{{?}}/>链接到：</label> 
                <div class="form-controls">
                    <div class="droplist">
                        <!--<a href="javascript:;" class="droplist-title j-droplist-toggle">
                            <span>请选择</span>
                            <i class="gicon-chevron-down mgl5"></i>
                        </a>
                        <ul class="droplist-menu" style="display: none;">
                            <li data-val="1"><a href="javascript:;">选择商品</a></li>
                            <li data-val="2"><a href="javascript:;">商品分组</a></li>
                        </ul>
                        -->
                        <select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
                        {{? it.fixed_link}}
                        	{{? it.content.dataset[i].select_value}}
                        	{{
                        		selv=it.content.dataset[i].select_value.split("_");
                        		select_value=selv[0];
                        	}}
                        	{{??}}
                        	{{ select_value=it.content.dataset[i].select_value; }}
                        	{{?}}
                            {{for (k=0,m=it.fixed_link.length;k<m;k++) { 
                                fl=it.fixed_link[k].split("_");
                            }}
                        <option value="{{=fl[0]}}" {{? fl[0]==select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.template_link}}
							<optgroup label="---------------自定义模板---------------"></optgroup>
							{{for (k=0,m=it.template_link.length;k<m;k++){
								fl = it.template_link[k].split("_");
							}}
							<option value="{{=fl[0]}}_10" {{? fl[0]+'_10'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
							{{ } }}
						{{?}}
                        {{? it.type_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
							{{
								product_type_linktype = -1;
								product_type_val = -1;
							}}
							{{? it.content.dataset[i].select_value}}
							{{	selv=it.content.dataset[i].select_value.split("_");
								product_type_linktype=selv[1];
								product_type_val=selv[0];
                            }}
							{{?}}
                        <option value="-40"{{? product_type_linktype==1 && product_type_val>0}} selected="selected"{{?}}>多级分类</option>
                        {{?}}
                        {{? it.img_info}}
                        <optgroup label="---------------图文消息---------------"></optgroup>
                            {{for (k=0,m=it.img_info.length;k<m;k++) { 
                                fl=it.img_info[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_2"{{? fl[0]+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.coupon_info && it.is_coupon>0}}
                        <optgroup label="---------------优惠券---------------"></optgroup>
						 <option value="0_60"{{? '0_60'==it.content.dataset[i].select_value}} selected="selected"{{?}} >优惠券全部</option>
                            {{for (k=0,m=it.coupon_info.length;k<m;k++) { 
                                fl=it.coupon_info[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_60"{{? fl[0]+'_60'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
                        {{? it.is_cityarea_caterer>0  && it.city_food}}
                        <optgroup label="-----------城市商圈（美食）-----------"></optgroup>
                            {{for (k=0,m=it.city_food.length;k<m;k++) { 
                                fl=it.city_food[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_3" {{? fl[0]+'_3'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.is_cityarea_ktv>0  && it.city_ktv}}
                        <optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
                            {{for (k=0,m=it.city_ktv.length;k<m;k++) { 
                                fl=it.city_ktv[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_6" {{? fl[0]+'_6'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.is_cityarea_hotel>0  && it.city_hotel}}
                        <optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
                            {{for (k=0,m=it.city_hotel.length;k<m;k++) { 
                                fl=it.city_hotel[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_7" {{? fl[0]+'_7'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.is_cityarea_shop>0  && it.city_shop}}
                        <optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
                            {{for (k=0,m=it.city_shop.length;k<m;k++) { 
                                fl=it.city_shop[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_8" {{? fl[0]+'_8'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.is_cityarea>0}}
                        <optgroup label="-----------商圈行业列表-----------"></optgroup>
                            {{for (k=0,m=it.cityarea_industry.length;k<m;k++) { 
                                fl=it.cityarea_industry[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_4_{{=fl[2]}}" {{? fl[0]+'_4_'+fl[2]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.brand_arr}}
                        <optgroup label="-------------品牌供应店铺-------------"></optgroup>
                            {{for (k=0,m=it.brand_arr.length;k<m;k++) { 
                                fl=it.brand_arr[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_5"{{? fl[0]+'_5'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.room_link}}
                        <optgroup label="-------------商城直播系统-------------"></optgroup>
							{{
								product_type_linktype = -1;
								product_type_val = -1;
							}}
							{{? it.content.dataset[i].select_value}}
								{{	selv=it.content.dataset[i].select_value.split("_");
									product_type_linktype=selv[1];
									product_type_val=selv[0];
								}}
							{{?}}
						<option value="weishi"{{? product_type_linktype==9 && product_type_val>0}} selected="selected"{{?}} >直播房间</option>
                        {{?}}	
                        </select>
						<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_{{=i}}"  class="input xlarge" style="{{? product_type_linktype!=1 || product_type_val<=0}}display:none;{{?}}height:28px;">
							{{for (k=0,m=it.type_arr[-1].length;k<m;k++) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_1"{{? type_id+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_1"{{? ctype_id+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_1"{{? ctype_id3+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}_1"{{? ctype_id4+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
						</select>
						{{?}}
						{{? it.room_link}}
						<select name="room_link" id="room_link_{{=i}}" class="input xlarge" style="{{? product_type_linktype!=9 || product_type_val<=0}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.room_link.length; k<m; k++ ) {
								room_id_name = it.room_link[k].split("_");
							}}
							<option value="{{= room_id_name[0]}}_9" {{? room_id_name[0]+'_9'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{= room_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}	
                        <div id="div_products_2_{{=i}}" style="display:none;" >
                            <select name="product_detail_id_2" id="product_detail_id_2_{{=i}}" class="input xlarge" style="height:28px;">
                                <option value="1"></option>
                            </select>
                        </div>
                    </div>
					
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
                </div>
            </div>
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
				<div class="form-controls">
					<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
			</div>
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">宽度1080 px，大小不超过100K</label>
            </div>
            <!--<div class="formitems">  
                <label class="fi-name">标题：</label>
                <div class="form-controls">
                    <input type="text" name="title" class="input xlarge" value="{{=it.content.dataset[i].title}}" maxlength="10">
                    <span class="fi-help-text"></span>
                </div>
            </div>
			-->
        </div>
    </li>
    {{ } }}
</ul>
</script>  
<!--分割线-->


<!--滚动公告栏-->
<script type="text/j-template" id="type_con_13">
<div class="con_display" {{? it.content.padding}}style="padding:{{= it.content.padding}}px 0;"{{?}}>
<div style="height:40px;line-height:40px;background-color:#fef7ee;border:1px solid #ffcc74;padding-left:34px;background-image:url({{? it.content.icon_pic== undefined}}images/laba01.png {{??}}{{=it.content.icon_pic}}{{?}});background-repeat:no-repeat;background-position:10px 9px;background-size:20px 20px;">
	<div id="marquee{{= it.id}}" class="marquee">
		<ul>
		{{for (var j=0,k=it.content.dataset.length;j<k;j++) { }}
			<li style="{{? it.content.rolling_direction==1}}display:inline-block;width:{{= it.content.dataset[j].title.length*16+314-34}}px;{{?}}color:#ffaf74;"><a href="javascript:#;" style="color:#ffaf74;font-size:16px;">{{= it.content.dataset[j].title}}</a></li>
		{{ } }}
		</ul>
	</div>
</div>

</div>
</script>
<script type="dot-template" id="type_ctrl_13">
<div class="formitems">
    <label class="fi-name">模块上下边距：</label> 
    <div class="form-controls">
        <div id='slider' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
<div class="formitems">  
	<label class="fi-name">滚动方向：</label>   
    <div class="form-controls">
		<div class="radio-group">
			<label><input type="radio" name="rolling_direction" value="1"{{? it.content.rolling_direction==1}} checked{{?}}>从右往左</label>
            <label><input type="radio" name="rolling_direction" value="2"{{? it.content.rolling_direction==2}} checked{{?}}>从下往上</label>
        </div>
	</div>
</div>
{{? it.content.rolling_direction==1}}
<div class="formitems">
    <label class="fi-name">滚动速度：</label>
    <div class="form-controls">
        <div  id='slider_speed' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" style="width:250px;"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2-speed">{{? it.content.rolling_speed}}{{=it.content.rolling_speed}}{{??}}1{{?}}（数值越大速度越慢）</span>
    </div>
</div>
{{?? it.content.rolling_direction==2}}
<div class="formitems">  
	<label class="fi-name">每条公告显示时间：</label>   
    <div class="form-controls">
			<input type="text" name="show_time_limit" value="{{= it.content.show_time_limit}}" size="5" style="text-align:center;">（秒）
	</div>
</div>
{{?}}
<!--滚动栏上传图标-->
<div class="formitems">  
	<label class="fi-name">上传图标：</label>   
    <div class="form-controls">
		<div class="fl" style="margin-right:20px;">
			<div class="imgnav j-selectimg">
				<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img10" enctype="multipart/form-data" method="post" onsubmit="return saveReport(10);">
					<input type="hidden" name="getImg" id='getImg10' value="{{=it.content.icon_pic}}">
					<p class="imgnav-select">
						<input type="file" size="20" name="upfile2" id="upfile2" class="up" >
                        {{? it.content.icon_pic== undefined}}
                        <img src="images/laba01.png" />
                        {{??}}
						<img src="{{=it.content.icon_pic}}">
                            {{?}}
					</p>
					<input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
					<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
					<input type="hidden" name="img_sort" value="0">
					<input type="hidden" name="uptypes" value="image/jpg,image/jpeg,image/png,">

				</form>
				</div>
			<span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
		</div>		
    </div>
    <div class="form-controls">
      <p>建议尺寸：36px*36px</p>
      <p>格式：jpg、png、bmp</p>
      <p>大小：24K以下</p>
    </div>
</div>

<ul class="ctrl-item-list"> 
{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
    <li class="ctrl-item-list-li clearfix">
        <div class="fl imgnav-info">
			<div class="formitems">  
                <label class="fi-name">公告内容：</label>
                <div class="form-controls">
                    <input type="text" name="title" class="input xlarge" autocomplete="off" value="{{= it.content.dataset[i].title}}">{{=it.content.dataset[i].text_length}}/50                  
                    <span class="fi-help-text"></span>
                </div>
            </div>
            <div class="formitems">  
                <label class="fi-name">链接到：</label>  
                <div class="form-controls">
                    <div class="droplist">
                        <select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
                        {{? it.fixed_link}}
                        	{{? it.content.dataset[i].select_value}}
                        	{{
                        		selv=it.content.dataset[i].select_value.split("_");
                        		select_value=selv[0];
                        	}}
                        	{{??}}
                        	{{ select_value=it.content.dataset[i].select_value; }}
                        	{{?}}
                            {{for (k=0,m=it.fixed_link.length;k<m;k++) { 
                                fl=it.fixed_link[k].split("_");
                            }}
                        <option value="{{=fl[0]}}" {{? fl[0]==select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.template_link}}
							<optgroup label="---------------自定义模板---------------"></optgroup>
							{{for (k=0,m=it.template_link.length;k<m;k++){
								fl = it.template_link[k].split("_");
							}}
			 				<option value="{{=fl[0]}}_10" {{? fl[0]+'_10'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
							{{ } }}
						{{?}}
                        {{? it.type_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
							{{
								product_type_linktype = -1;
								product_type_val = -1;
							}}
							{{? it.content.dataset[i].select_value}}
							{{	selv=it.content.dataset[i].select_value.split("_");
								product_type_linktype=selv[1];
								product_type_val=selv[0];
                            }}
							{{?}}
                        <option value="-40"{{? product_type_linktype==1 && product_type_val>0}} selected="selected"{{?}}>多级分类</option>
                        {{?}}
                        {{? it.img_info}}
                        <optgroup label="---------------图文消息---------------"></optgroup>
                            {{for (k=0,m=it.img_info.length;k<m;k++) { 
                                fl=it.img_info[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_2"{{? fl[0]+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.coupon_info && it.is_coupon>0}}
                        <optgroup label="---------------优惠券---------------"></optgroup>
						 <option value="0_60"{{? '0_60'==it.content.dataset[i].select_value}} selected="selected"{{?}} >优惠券全部</option>
                            {{for (k=0,m=it.coupon_info.length;k<m;k++) { 
                                fl=it.coupon_info[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_60"{{? fl[0]+'_60'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
                        {{? it.is_cityarea_caterer>0  && it.city_food}}
                        <optgroup label="-----------城市商圈（美食）-----------"></optgroup>
                            {{for (k=0,m=it.city_food.length;k<m;k++) { 
                                fl=it.city_food[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_3" {{? fl[0]+'_3'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.is_cityarea_ktv>0  && it.city_ktv}}
                        <optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
                            {{for (k=0,m=it.city_ktv.length;k<m;k++) { 
                                fl=it.city_ktv[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_6" {{? fl[0]+'_6'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.is_cityarea_hotel>0  && it.city_hotel}}
                        <optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
                            {{for (k=0,m=it.city_hotel.length;k<m;k++) { 
                                fl=it.city_hotel[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_7" {{? fl[0]+'_7'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.is_cityarea_shop>0  && it.city_shop}}
                        <optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
                            {{for (k=0,m=it.city_shop.length;k<m;k++) { 
                                fl=it.city_shop[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_8" {{? fl[0]+'_8'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.is_cityarea>0}}
                        <optgroup label="-----------商圈行业列表-----------"></optgroup>
                            {{for (k=0,m=it.cityarea_industry.length;k<m;k++) { 
                                fl=it.cityarea_industry[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_4_{{=fl[2]}}" {{? fl[0]+'_4_'+fl[2]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.brand_arr}}
                        <optgroup label="-------------品牌供应店铺-------------"></optgroup>
                            {{for (k=0,m=it.brand_arr.length;k<m;k++) { 
                                fl=it.brand_arr[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_5"{{? fl[0]+'_5'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.room_link}}
                        <optgroup label="-------------商城直播系统-------------"></optgroup>
							{{
								product_type_linktype = -1;
								product_type_val = -1;
							}}
							{{? it.content.dataset[i].select_value}}
								{{	selv=it.content.dataset[i].select_value.split("_");
									product_type_linktype=selv[1];
									product_type_val=selv[0];
								}}
							{{?}}
						<option value="weishi"{{? product_type_linktype==9 && product_type_val>0}} selected="selected"{{?}} >直播房间</option>
                        {{?}}	
                        </select>
						<!--<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>-->
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_{{=i}}"  class="input xlarge" style="{{? product_type_linktype!=1 || product_type_val<=0}}display:none;{{?}}height:28px;">
							{{for (k=0,m=it.type_arr[-1].length;k<m;k++) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_1"{{? type_id+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_1"{{? ctype_id+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_1"{{? ctype_id3+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}_1"{{? ctype_id4+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
						</select>
						{{?}}
						{{? it.room_link}}
						<select name="room_link" id="room_link_{{=i}}" class="input xlarge" style="{{? product_type_linktype!=9 || product_type_val<=0}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.room_link.length; k<m; k++ ) {
								room_id_name = it.room_link[k].split("_");
							}}
							<option value="{{= room_id_name[0]}}_9" {{? room_id_name[0]+'_9'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{= room_id_name[1]}}</option> 
							{{ } }}
						</select>
						{{?}}	
                        <div id="div_products_2_{{=i}}" style="display:none;" >
                            <select name="product_detail_id_2" id="product_detail_id_2_{{=i}}" class="input xlarge" style="height:28px;">
                                <option value="1"></option>
                            </select>
                        </div>
                    </div>
					
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
                </div>
            </div>
            {{? it.content.dataset[i].select_value}}
			{{	selv=it.content.dataset[i].select_value.split("_");
				select_val=selv[0];
            }}
			{{? select_val==-11}}
			<div class="formitems">  
			    <label class="fi-name"></label>  
			    <div class="form-controls">
			        <div class="droplist">
			            <select  name="type_id_3"  id="type_id_3"  class="input xlarge" style="height:28px;">
			                <option value="-11">全部</option>
			                {{? it.package_lists}}
			                {{for (k=0,m=it.package_lists.length;k<m;k++) { 
			                fl=it.package_lists[k].split("_");
			            }}
			            <option value="-11_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{?}}
			
        </div>
		<div class="ctrl-item-list-actions">
            <a href="javascript:;" title="上移" class="j-moveup"><i class="gicon-arrow-up"></i></a>
            <a href="javascript:;" title="下移" class="j-movedown"><i class="gicon-arrow-down"></i></a>
            <a href="javascript:;" title="删除" class="j-del"><i class="gicon-remove"></i></a>
        </div>
    </li>
    {{ } }}
	<li class="ctrl-item-list-add" title="添加">+</li>
</ul>
</script>
<!--滚动公告栏-->

<!-- 个人中心头部 -->
 <script type="text/j-template" id="type_con_17">
<div class="con_display" {{? it.content.padding}}style="padding:{{= it.content.padding}}px 0;"{{?}}> 
      {{? it.content.css_type==1}}
      <div class="top-main01 back-img" style="{{? it.content.icon_pic!=''}}background-image:url({{=it.content.icon_pic}}){{?}}">
			<span class="top-set">设置</span>
			<div class="top-imgBox">
				头像
			</div>
			<p class="top-userName">微信昵称</p>
			<div class="top-info">
				<p>用户ID</p>
				<p>推荐人</p>
				<p>个人身份显示区域</p>
			</div>
           </div> 
          {{?? it.content.css_type==2}}
            <div class="top-main02 back-img" style="background-image:url({{=it.content.icon_pic}}) ">
			<div class="top-imgBox02">
				头像
			</div>
			<div class="top-info02">
				<p class="top-name02">小白很爱白</p>
				<p>ID：12365     推荐人：小白</p>
				<p>推广员 | 店铺</p>
			</div>
		</div>
          {{?? it.content.css_type==3}}
          <div class="top-main03 back-img" style="background-image:url({{=it.content.icon_pic}})">
			<div class="top-mainInner">
				<span class="top-set">设置</span>
			<div class="top-imgBox">
				头像
			</div>
			<div class="top-info03">
				<p class="top-name02">微信昵称</p>
				<p>用户ID</p>
				<p>推荐人</p>
		    </div>
			</div>
		    <div class="divide"></div>
		    <div class="top-status">
		    	我的身份：推广员 | 店铺 | 合作商 | 社区代理
		    </div>
		</div>
          {{?? it.content.css_type==4}}
          <div class="top-main04 back-img" style="background-image:url({{=it.content.icon_pic}})">
			<span class="top-set">设置</span>
			<div class="top-imgBox">
				头像
			</div>
			<div class="name04">
				<p class="top-userName04">微信昵称</p>
			</div>
			<div class="top-info04">
				<p>用户ID</p>
				<p>推荐人</p>
			</div>
			<div class="top-status04">
		    	我的身份：推广员 | 店铺 | 合作商 | 社区代理
		    </div>
		</div>
        {{?? it.content.css_type==5}}
        <div class="top-main05" style="background-image:url({{=it.content.icon_pic}})">
			<div class="topimg" style="background: {{? it.content.icon_pic == undefined || it.content.icon_pic == ''}}url(images/back05.png);background-size: 100% 100%;{{?}}">
				<div class="top-set05">
					<img src="images/er_icon01.png"/>
				</div>
				<div class="status05">
					<img src="images/er_icon13.png"/>
					<span>推广员</span>
					<img src="images/er_icon13.png"/>
					<span>店铺</span>
					<img src="images/er_icon13.png"/>
					<span>合作商</span>
					<img src="images/er_icon13.png"/>
					<span>社区代理</span>
				</div>
				<div class="status05">
					<span>ID:15202</span>
					<span style="margin-left: 8px;">推荐人:小一想</span>
				</div>
			</div>
			<div class="head-img05">
				<div class="head-content">
					头像11
				</div>
			</div>
			<div class="name05">
				大姐大大姐大
			</div>
		</div>
          {{?}}
          
		</div>
</script>
<script type="dot-template" id="type_ctrl_17">
<div class="formitems" style="margin-top:10px;">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>         
</div>
<div class="formitems">
       <label class="fi-name">显示方式：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>样式一</label>
                <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>样式二</label>
                <label><input type="radio" name="css_type" value="3"{{? it.content.css_type==3}} checked{{?}}>样式三</label>
                <label><input type="radio" name="css_type" value="4"{{? it.content.css_type==4}} checked{{?}}>样式四</label>
                <label><input type="radio" name="css_type" value="5"{{? it.content.css_type==5}} checked{{?}}>样式五</label>
                
            </div>
        </div>
    </div>
<div class="formitems">  
    <label class="fi-name">更换背景：</label>  
    <div class="form-controls">
    <div class="formitems">                 
	    <label class="note">上传背景图</label>
    </div>
		<div class="fl" style="margin-right:20px;">
			<div class="imgnav j-selectimg">
				<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img10" enctype="multipart/form-data" method="post" onsubmit="return saveReport(10);">
					<input type="hidden" name="getImg" id='getImg10' value="{{=it.content.icon_pic}}">
					<p class="imgnav-select add-img">
						<input type="file" size="20" name="upfile2" id="upfile2" class="up" >
                        {{? it.content.icon_pic == undefined || it.content.icon_pic == ''}}
                        <div style="font-size:45px;line-height:83px;color:gray;">+
                        </div>
                        {{??}}
						<img src="{{=it.content.icon_pic}}">
                        <span class="head-delete">删除</span>
                        {{? }}
					</p>
					<input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
					<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
					<input type="hidden" name="img_sort" value="0">
					<input type="hidden" name="uptypes" value="image/jpg,image/jpeg,image/png,">

				</form>
				</div>
			<span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
		</div>	
        
        
    </div>
	<div class="formitems">                 
		<label class="fi-name">建议尺寸：</label> <label class="note">750*368px，大小不超过200K</label> 
	</div>
</div>

</script>
 

<!-- 个人中心头部-->
<script type="text/javascript" src="js/doT.min.js"></script>
<script type="text/javascript" src="js/colorpicker.js"></script>
<script type="text/javascript" src="js/custom.events.js"></script>
<script type="text/javascript" src="js/custom.core.js"></script>
<script type="text/javascript" src="js/custom.init.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/layer/layer.js"></script>
<script type="text/javascript" src="js/jquery.touchSlider.js"></script>
<script type="text/javascript" src="js/slider.js"></script>
<script type="text/javascript" src="js/custom.display.js"></script>
<script type="text/javascript" src="js/Marquee.js"></script>
<script charset="utf-8" src="js/region_select.js"></script>
<script charset="utf-8" src="../../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/global.js"></script>
<script type="text/javascript" src="../../../Common/js/Base/personalization/shop.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/lean-modal.min.js"></script>
<script type="text/javascript" src="../../../Common/js/Product/product/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script><!--ajaxform 插件-->
<script type="text/javascript" src="js/WdatePicker.js"></script><!--添加时间插件-->
<!--<script type="text/javascript" src="js/region_select.js"></script>--><!--选择地区插件-->
<script type="text/javascript" src="js/select_area.js"></script><!--选择地区插件-->
<!--<script src="//malsup.github.io/jquery.form.js"></script>-->
<script>
    var action ="<?php echo $action;?>";
    var customer_id =<?php echo $customer_id;?>;
    var diy_temid   =<?php echo $diy_temid;?>;
	var is_cityarea_caterer =<?php echo $is_cityarea_caterer;?>  //城市商圈（美食），渠道开关
	var is_cityarea_ktv =<?php echo $is_cityarea_ktv;?>  //城市商圈（ktv），渠道开关
	var is_cityarea_hotel =<?php echo $is_cityarea_hotel;?>  //城市商圈（酒店），渠道开关
	var is_cityarea_shop =<?php echo $is_cityarea_shop;?>  //城市商圈（线下商城），渠道开关
	var is_cityarea =<?php echo $is_cityarea;?>  //城市商圈，渠道开关
	
$(function() {  
    var customarr   =<?php echo json_encode($customarr);?>;//模块内容
    typearr     =<?php echo json_encode($type_arr);?>; //产品分类  
    imginfo  =<?php echo json_encode($imginfo);?>;//图文消息数组
    couponinfo  =<?php echo json_encode($couponinfo);?>;//优惠券数组
    cityfood       =<?php echo json_encode($cityfood);?>;//城市商圈美食
    is_coupon       =<?php echo json_encode($is_coupon);?>;//城市商圈美食
    cityktv       =<?php echo json_encode($cityktv);?>;//城市商圈ktv
    cityhotel       =<?php echo json_encode($cityhotel);?>;//城市商圈酒店
    cityshop       =<?php echo json_encode($cityshop);?>;//城市商圈线下商城
    fixedlink   =<?php echo json_encode($fixedlink);?>;//固定连接数组  
	cityarea_industry       =<?php echo json_encode($cityarea_industry);?>;//商圈-行业
	brandarr     =<?php echo json_encode($brandarr);?>; //品牌供应商
	areaData = <?php echo json_encode($areaData);?>; //省、市
	room_link = <?php echo json_encode($room_link);?>; //微视直播系统
	template_link = <?php echo json_encode($template_link);?>; //已启用的模板
	cityarea_shop_protype_arr = <?php echo json_encode($cityarea_shop_protype_arr);?>; //线下商城产品分类
	cityarea_shop_type_arr = <?php echo json_encode($cityarea_shop_type_arr);?>; //线下商城店铺分类
	cityarea_shop_arr = <?php echo json_encode($cityarea_shop_arr);?>; //线下商城店铺
	oldstr = $('#con_15_placeholder').val(); //已启用的模板
	 //console.log(customarr);
    package_lists   =<?php echo json_encode($package_lists);?>;//固定连接数组  
	
    var new_baseurl   ="<?php echo $new_baseurl;?>";//拼接链接
    var test = eval(customarr);//   JSON转化为数组 
    
    var titleArr=new Array();
    var imgArr=new Array();
    var select_value_arr=new Array();
    var detail_value_arr=new Array();
    var detail_name_arr=new Array();
	var color_arr=new Array();
	var sel_link_type_arr=new Array();
	var link_arr=new Array();
 //读取数据库数组生成页面
    for(i=0;i<test.length;i++)
    {   
		if ( test[i] == '' ) {
			continue;
		}
        var module={
                id:null,//模块ID 
                type:null,//模块类型
                sort:null, //排序
                content:null,//模块内容
                fixed_link:null,//固定
                type_arr:null,//产品分类
                img_info:null,//图文
                coupon_info:null,//优惠券
                city_food:null,//城市商圈美食
                is_coupon:null,//优惠券
                city_ktv:null,//城市商圈ktv
                city_hotel:null,//城市商圈酒店
                city_shop:null,//城市商圈线下商城
				is_cityarea_caterer:null,//城市商圈（美食）
				is_cityarea_ktv:null,//城市商圈（ktv）
				is_cityarea_hotel:null,//城市商圈（酒店）
				is_cityarea_shop:null,//城市商圈（线下商城）
				is_cityarea:null,//城市商圈，渠道开关
				cityarea_industry:null,//商圈-行业
				brand_arr:null,//品牌供应商
				areaData:null,//省、市
				room_link:null,//微视直播系统
				template_link:null,//已启用的模板
				cityarea_shop_protype_arr:null,//线下商城产品分类
				cityarea_shop_type_arr:null,//线下商城店铺分类
				cityarea_shop_arr:null//线下商城店铺

          };
          module.content={
                css_type:null,
                placeholder:null,
                padding:null,
                margin:null,
                pro_title_show:null,
                pro_title_twoline:null,
                pro_numshow:null,
                show_sale:null,
                foot_position:null,
                video_link:null,
				bg_color:null,
				color:null,
				rolling_direction:null,
				rolling_speed:null,
				show_time_limit:null,
				city_name:null,
				location_p:null,
                dataset:[],
                shop_type:null,//商城类型
				sort_type:null,//排序类型
				divide_type:null//划分类型
                };

            if(test[i].title){
                 titleArr=test[i].title.split("|");
            }
           if(test[i].imgurl){
                 imgArr=test[i].imgurl.split("|");
            }
            if(test[i].select_value){
                 select_value_arr=test[i].select_value.split("|");
            }
            if(test[i].detail_value){
                 detail_value_arr=test[i].detail_value.split("|");
            }
            if(test[i].detail_name){
                 detail_name_arr=test[i].detail_name.split("|");
            }
			if(test[i].start_time){
                 start_time_arr=test[i].start_time.split("|");
            }
			if(test[i].end_time){
                 end_time_arr=test[i].end_time.split("|");
            }
			if(test[i].color){
                 color_arr=test[i].color.split("|");
            }
			if(test[i].link){
                 link_arr=test[i].link.split("|");
            }
			if(test[i].sel_link_type){
                 sel_link_type_arr=test[i].sel_link_type.split("|");
            }
			
         module.id=test[i].diy_tem_contid;
         module.type=test[i].type;
         module.fixed_link=fixedlink;
         module.package_lists=package_lists;
         module.room_link=room_link;
         module.template_link=template_link;
         module.type_arr=typearr;
         module.img_info=imginfo;
         module.coupon_info=couponinfo;
         module.city_food=cityfood;
         module.is_coupon=is_coupon;
         module.city_ktv=cityktv;
         module.city_hotel=cityhotel;
         module.city_shop=cityshop;
		 module.is_cityarea_caterer=is_cityarea_caterer;    
		 module.is_cityarea_ktv=is_cityarea_ktv;    
		 module.is_cityarea_hotel=is_cityarea_hotel;    
		 module.is_cityarea_shop=is_cityarea_shop;    
		 module.is_cityarea=is_cityarea;
		 module.cityarea_industry=cityarea_industry;
		 module.brand_arr=brandarr;
		 module.areaData=areaData;
		 module.cityarea_shop_protype_arr=cityarea_shop_protype_arr;
		 module.cityarea_shop_type_arr=cityarea_shop_type_arr;
		 module.cityarea_shop_arr=cityarea_shop_arr;
         module.content.css_type=test[i].css_type;
         module.content.placeholder=test[i].placeholder;
         module.content.padding=test[i].mod_padding;
         module.content.margin=test[i].mod_img_padding;
         module.content.pro_title_show=test[i].pro_title_show;
         module.content.pro_title_twoline=test[i].pro_title_twoline;
         module.content.pro_numshow=test[i].pro_numshow;
         module.content.show_sale=test[i].show_sale;
         module.content.foot_position=test[i].foot_position;
         module.content.video_link=test[i].video_link;
		 module.content.bg_color=test[i].bg_color;
		 module.content.color=test[i].color;
		 module.content.rolling_direction=test[i].rolling_direction;
		 module.content.rolling_speed=test[i].rolling_speed;
		 module.content.show_time_limit=test[i].show_time_limit;
		 module.content.city_name=test[i].city_name;
		 module.content.location_p=test[i].province;
		 module.content.shop_type=test[i].shop_type;
		 module.content.sort_type=test[i].sort_type;
		 module.content.divide_type=test[i].divide_type;
		 // console.log(titleArr.length-1,0<titleArr.length-1)
		
         for(j=0;j<titleArr.length-1;j++)
           {      
            var newdata={
                    mod_sort:null,
                    link:"",
                    title:"",
                    color:"",
                    pic:"",
                    foreign_id:'-1',
                    detail_id:'',
                    link_type:'',
                    select_value:"",
                    detail_value:'',
                    detail_name:"",
					start_time:"",
					end_time:'',
					sel_link_type:1
                };
            module.content.dataset.push(newdata);
            module.content.dataset[j].title=titleArr[j];
            module.content.dataset[j].select_value=select_value_arr[j];
            module.content.dataset[j].detail_value=detail_value_arr[j];
            module.content.dataset[j].detail_name=detail_name_arr[j];
			module.content.dataset[j].color=color_arr[j];
			module.content.dataset[j].sel_link_type=sel_link_type_arr[j];
			if( sel_link_type_arr[j] == 2 ){
				if( link_arr[j] == 'javascript:' || link_arr[j] == undefined ){
					module.content.dataset[j].link='';
				} else {
					module.content.dataset[j].link=link_arr[j];
				}
			}
			if(module.type == 12){
				module.content.dataset[j].start_time=start_time_arr[j];
				module.content.dataset[j].end_time=end_time_arr[j];
			}

            var picUrl=new_baseurl+imgArr[j];
            if(picUrl.indexOf("weixinpl")>0){ //判断图片路径
                module.content.dataset[j].pic=picUrl;
            }
            else{
            	if(imgArr[j]!=""){
            		var defUrl=new_baseurl+"/weixinpl/common_shop/common/custom_temp/"+imgArr[j];
                	module.content.dataset[j].pic=defUrl;
            	}
                
            }
        }		
        custom_query(module);
		var select_value_arr=[];
		var detail_value_arr=[];
		var detail_name_arr=[];
    }

});
function num_check(obj){
	var val = $(obj).val();
	if(isNaN(val) || val < 0){
		$(obj).val(1);
	}
}

function clearNoNum(obj){
	//先把非数字的都替换掉，除了数字和.
	obj.value = obj.value.replace(/[^\d.]/g,"");
	//必须保证第一个为数字而不是.
	obj.value = obj.value.replace(/^\./g,"");
	//保证只有出现一个.而没有多个.
	obj.value = obj.value.replace(/\.{2,}/g,".");
	//保证.只出现一次，而不能出现两次以上
	obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
	//只能输入两个小数
	obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); 
}
</script>

<!--选择链接的JS开始-->
<script>
//var p_detail_id = -1;
function changeProductType(selv,sort,detail_id){
  document.getElementById("div_products_2_"+sort).style.display="none";
  if(selv.indexOf("_1")!=-1){
     //是产品分类
	 var pro_typeid= selv.substring(0,selv.indexOf("_1"));
	 // console.log(pro_typeid);
	 if(pro_typeid>0){
		 document.getElementById("div_products_2_"+sort).style.display="block";
		 
		 if( detail_id != '' ){
			 url='get_product_list.php?callback=jsonpCallback_get_product_list&type_id='+pro_typeid+'&sort='+sort+'&detail_id='+detail_id;
		 }else{
			 url='get_product_list.php?callback=jsonpCallback_get_product_list&type_id='+pro_typeid+'&sort='+sort;
		 }
		 $.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_get_product_list'
		});
	 }
  }
}
//var detail_id=<?php echo $detail_id; ?>;
// var detail_id=-1;
function jsonpCallback_get_product_list(results){
	var len = results.length;
	var sort=results[2].sort;
	var detail_id = results[3].detail_id;
	
	var sel_pro = document.getElementById("product_detail_id_2_"+sort);
	
	sel_pro.options.length = 0;
   
    var new_option = new Option("---请选择一个产品---",-1);
    sel_pro.options.add(new_option);
	
    for(i=4;i<len;i++){
      var pid = results[i].pid;
      var pname = results[i].pname;

      var new_option = new Option(pname,pid);
       sel_pro.options.add(new_option);
      if(pid==detail_id){
         new_option.selected=true;
      }
   }
   
}

function saveReport(o) {   
        $("#frm_img"+o).ajaxSubmit(function(msg) {   
          // 对于表单提交成功后处理，message为提交页面saveReport.htm的返回内容 
          var imgurl=msg;
          if( imgurl.substr(imgurl.length-1, 1) == '！' ){
          	alert(imgurl)
          }else{
          	$('#getImg'+o).val(imgurl);
          	$('#getImg'+o).change();
          }
       });

    return false; // 必须返回false，否则表单会自己再做一次提交操作，并且页面跳转   
}

// 检测字节数
function checktext(obj){
	var str = $(obj).val();
	var trim_str = $.trim(str);
	bytesCount = 0;
	for (var i = 0; i < str.length; i++){
		var c = str.charAt(i);
		if (/^[\u0000-\u00ff]$/.test(c)){
			bytesCount += 1;
		}else{
			bytesCount += 3;
		}
	}
	if(bytesCount>36){
		$(obj).val(oldstr);
	}else if( trim_str != str ){
		$(obj).val(trim_str);
	}
	oldstr = $.trim($(obj).val());
}

</script>

<!--选择链接的JS结束-->
</body>
</html>  
<?php 

mysql_close($link);
?>