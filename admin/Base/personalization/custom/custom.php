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
		$custom_query="select diy_tem_contid,title,imgurl,foreign_id,detail_id,mod_padding,mod_img_padding,css_type,pro_title_show,pro_title_twoline,pro_numshow,foot_position,placeholder,show_sale,type,link_type,select_value,detail_value,detail_name,search_color,color,video_link,rolling_direction,rolling_speed,show_time_limit,city_name,start_time,end_time,province,sel_link_type,link,shop_type,sort_type,divide_type,3d_link as threed_link,pic_type,show_cost,show_cost,show_activity,show_backwards,backwards_day,show_carry,show_carry_type,text_color,bg_color,round,round_color,round_pic,production_num,nav,activity_id,activity_title,show_num,pro_pic_show,all_switch,fix_top,yun_consult_show,yun_phone_show,yun_phone,o2o_grade,o2o_price from weixin_commonshop_diy_template_content where isvalid=true and customer_id=".$customer_id." and LOCATE(diy_tem_contid,'".$content."') ORDER  BY FIND_IN_SET(diy_tem_contid,'".$content."')";
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
			$customarr[$k]['search_color']=$row->search_color;  //搜索框背景颜色
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
			$customarr[$k]['threed_link']=$row->threed_link;
			//活动橱窗 字段
			$customarr[$k]['pic_type']=$row->pic_type;	//商品图片 0:封面图  1：产品图
			$customarr[$k]['show_cost']=$row->show_cost;	//是否显示原价
			$customarr[$k]['show_activity']=$row->show_activity;	//是否显示活动价
			$customarr[$k]['show_backwards']=$row->show_backwards;//是否显示开始时间倒数
			$customarr[$k]['backwards_day']=$row->backwards_day;	//提前倒数天数
			$customarr[$k]['show_carry']=$row->show_carry;	//是否显示活动进行时间
			$customarr[$k]['show_carry_type']=$row->show_carry_type;	//活动时间样式
			$customarr[$k]['text_color']=$row->text_color;	//时间文字颜色
			$customarr[$k]['bg_color']=$row->bg_color;	//时间背景颜色
			$customarr[$k]['round']=$row->round;	//标签设置
			$customarr[$k]['round_color']=$row->round_color;	//标签颜色
			$customarr[$k]['round_pic']=$row->round_pic;	//图片标签
			$customarr[$k]['nav']=$row->nav;				//显示活动模块	
			$customarr[$k]['production_num']=$row->production_num;	//显示活动产品数
			$customarr[$k]['activity_id']=$row->activity_id;	//显示活动ID
			$customarr[$k]['activity_title']=$row->activity_title;	//显示活动标题
			$customarr[$k]['show_num']=$row->show_num;	//滑动首屏显示图标个数
			$customarr[$k]['fix_top']=$row->fix_top;	//放置位置，1固定顶部，0随页面移动
			$customarr[$k]['all_switch']=$row->all_switch;	//查看全部开关
			$customarr[$k]['pro_pic_show']=$row->pro_pic_show;	//是否显示图标
			$customarr[$k]['yun_consult_show']=$row->yun_consult_show;	//云店是否显示咨询
			$customarr[$k]['yun_phone_show']=$row->yun_phone_show;	//云店是否显示电话
			$customarr[$k]['yun_phone']=$row->yun_phone;	//云店电话
			$customarr[$k]['o2o_grade']=$row->o2o_grade;	//o2o是否显示评分
			$customarr[$k]['o2o_price']=$row->o2o_price;	//o2o是否显示价钱
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

$is_exchange = 0;
/* 查看满赠活动系统渠道开关 start */
$query="select count(1) as is_exchange from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='满赠活动'";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result)) {
   $is_exchange = $row->is_exchange;
}
/* 查看满赠活动渠道开关 end */

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
$fixedlink[]="-5_限时抢购";
$fixedlink[]="-10_商城在线客服";
$fixedlink[]="-11_礼包列表";
$fixedlink[]="-12_VP产品";
$fixedlink[]="-15_兑换专区";
$fixedlink[]="-20_人气团列表";
$fixedlink[]="-21_续费专区";
$fixedlink[]="-22_电商直播";
$fixedlink[]="-23_语音直播";
if($is_ticket){
    $fixedlink[]="-24_票务特价机票";
    $fixedlink[]="-25_票务特价火车票";
}
if($is_exchange){
    $fixedlink[]="-32_满赠专区";
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
$fixedlink[]="-36_限时专区";
$fixedlink[]="-361_积分专区";
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

//是否开启3d
$threed_open=0;
$sql_threed = "SELECT is_open FROM ".WSY_PROD.".3d_model_setting WHERE customer_id={$customer_id}";
$res_threed  = _mysql_query($sql_threed);
while ($row = mysql_fetch_object($res_threed) ){
	$threed_open = $row->is_open;
}

//渠道控制d开关
$is_travelcard = 0;
$query="select count(1) as is_travelcard from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='3D素材'";
$funs = _mysql_query($query) or die('L274 is_travelcard Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($funs)) {
    $is_travelcard = $row->is_travelcard;
}

//o2o一级行业列表
$o2o_list_arr = array();
$query_o2o = "SELECT id,trade_name FROM now_pay_trade WHERE custid = ".$customer_id." AND isvalid = TRUE AND level = 0";
$result_o2o = _mysql_query($query_o2o) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result_o2o)) {
    $o2o_id = $row->id;
    $o2o_name = $row->trade_name;
    $o2o_list_arr[] = $o2o_id."_".$o2o_name;
}
//o2o二级行业列表
$o2o_lv_list_arr = array();
$query_o2o_lv = "SELECT id,trade_name,level FROM now_pay_trade WHERE custid = ".$customer_id." AND isvalid = TRUE AND level > 0";
$result_o2o = _mysql_query($query_o2o_lv) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result_o2o)) {
    $o2o_id = $row->id;
    $o2o_level = $row->level;
    $o2o_name = $row->trade_name;
    $o2o_lv_list_arr[] = $o2o_level."_".$o2o_id."_".$o2o_name;
}

//获取选择框链接
require_once($_SERVER['DOCUMENT_ROOT']."/weixinpl/common/utility_common.php");
$shopLink = new shopLink_Utlity($customer_id);
$link_arr = $shopLink->getSelectLink(array(3), 1);	//3：产品分类
$type_arr = $link_arr['type_arr'];

//商城客服电话
$kefu_phone = '';
$query_shop_setting = "select advisory_flag,advisory_telephone from weixin_commonshops where isvalid=true and customer_id='".$customer_id."' limit 1";
$result_shop_setting=_mysql_query($query_shop_setting) or die ('query_shop_setting faild' .mysql_error());
while ($row = mysql_fetch_object($result_shop_setting)) {
	$advisory_flag = $row->advisory_flag;
	$advisory_telephone = $row->advisory_telephone;
}

if($advisory_flag == 1){
	$kefu_phone = $advisory_telephone;
}
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
.droplist button{
	align-items: flex-start;
    text-align: center;
    cursor: default;
    color: buttontext;
    background-color: buttonface;
    box-sizing: border-box;
    border-width: 2px;
    border-style: outset;
    border-color: buttonface;
    border-image: initial;
}
.link-choose{color:#fff!important;font-size:12px;border:0!important;height:22px;padding:0 10px 2px 10px;border-radius:3px;vertical-align:middle;}
/*3D素材---开始*/
    .mask_3d{
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        display: none;
        background: rgba(0,0,0,.4);
        z-index: 9999;
    }
    .mask_3d th{
        text-align: center;
    }
    .mask_3d td{
        word-wrap: break-word;
        border: 1px solid #d8d8d8;
        text-align:center !important;
    }
    .box_3D{
        width: 80%;
        background: #FFF;
        margin:0 auto;
        border-radius: 5px;
        height: 730px;
        margin-top: 40px;
    }
    .box_box{
        height: 600px;
        overflow: scroll;
        margin-top: 20px;
    }
    .title_3D{
        font-size: 17px;
        font-weight: 900;
        margin-left: 2%;
        color: black;
        padding-top: 35px;
    }
/*3D素材---结束*/
	.padding-top-0{
		padding-top: 0 !important;
	}
	.chose-bottom{
		display: inline-block;
	    background-color: #06a7e1;
	    font-size: 14px;
	    color: #FFF;
	    padding: 0 10px;
	    line-height: 30px;
	    border-radius: 2px;
	    cursor: pointer;
	    margin: 0 12px;
	    border:none;
	}
</style>
       <!--列表内容大框开始-->
	<div class="WSY_columnbox" style="position:relative;padding-bottom: 50px">
    	<!--列表头部切换开始-->
    	<?php
			include("../basic_head.php"); 
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
                <li class="WSY_top" style="background:<?php echo $bgcolor;?>">
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
                    <a data-type="1" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>搜索栏</a>
                    <a data-type="2" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>图片广告</a>
                    <a data-type="3" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>分类图标</a>
                    <a data-type="9" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>橱窗二图</a>
                    <a data-type="4" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>橱窗三图</a>
                    <a data-type="8" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>橱窗四图</a>
                    <a data-type="5" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>分类产品</a>
                    <a data-type="6" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>底部菜单</a>
                    <a data-type="7" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>分割线</a>
                    <a data-type="10" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>视频</a>
                    <a data-type="11" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>LBS定位</a>
                    <a data-type="12" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>LBS城市广告</a>
                    <a data-type="13" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>滚动公告栏</a>
                    <a data-type="14" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>推广员名片</a>
                    <a data-type="15" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>头像插件</a>
                    <a data-type="16" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>线下店铺</a>
                    <a data-type="17" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>活动橱窗</a>
                    <a data-type="18" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>天气插件</a>
                    <a data-type="19" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>社区帖子</a>
                    <a data-type="20" data-kefu="<?php echo $kefu_phone;?>" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>云店店头</a>
                    <a data-type="21" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>云店店主产品</a>
                    <a data-type="22" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>O2O店铺列表</a>
                    <div class="clear"></div>
                </div>
                <div class="diy-actions-submit">
                    <a href="javascript:;" class="save-btn diy_btn" id="j-savePage" >保存</a>
                </div>
        </div>
    </div>
</div>
</div>
<div class="mask_3d" style="display: none;">
    <!---->
    <div class="box_3D">
        <div>
            <img src="../../../Common/images/Product/3D_close.png" style="float: right;margin:20px;cursor: pointer;" id="3d_close">
        </div>
        <div class="columnbox_table" style="width: 98%;margin: 0 auto;margin-top: 20px;">
            <p class="title_3D">选择3D素材</p>
            <!--表格开始-->
            <div class="box_box">
                <table width="96%" class="WSY_table" id="WSY_t1_3d" style="background: #FFF">
                </table>
            </div>
        </div>
        <!--翻页开始-->
        <div class="WSY_page" style=""><ul class="WSY_pageleft" style="width: 70%;"><li class="one">1</li><li class="tcdNumber">2</li><li class="tcdNumber">3</li><div class="WSY_searchbox"><input class="WSY_page_search" name="WSY_jump_page" id="WSY_jump_page" value=""><input class="WSY_jump" type="button" value="跳转" onclick="jumppage()"></div></ul><ul class="WSY_pageright"><li class="WSY_next">下一页</li></ul></div>
        <!--翻页结束-->
        <input type="hidden" id="pagenum" value="1">
        <input type="hidden" id="pageCount" value="1">
        <input type="hidden" id="type_num" value="-1">
    </div>
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
<!--搜索框-->
<script  type="dot-template" id="type_con_1">
<div class="con_display" style="{{? it.content.padding}}padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px;{{?}}background-color:{{= it.content.bg_color}}">
{{? it.content.css_type==1}}
<div class="search-box-1">
<div class="search-layer-1">
    <input type="text" class="search-input-1"  placeholder="{{= it.content.placeholder }}" >
    <button type="submit" class="search-icon-1"></button>
</div>
</div>
{{?? it.content.css_type==2}}
<div class="search-box-2">
<a title="{{=it.content.dataset[0].title}}" href="{{=it.content.dataset[0].link}}" ><img src="{{=it.content.dataset[0].pic}}" width="100%" class="search-logo"/></a>
<div class="search-layer-2">
    <input type="text" class="search-input-2"  placeholder="{{= it.content.placeholder }}" >
    <button type="submit" class="search-icon-2"></button>
</div>
</div>
{{?? it.content.css_type==3}}
<div class="search-box-3">
<a title="{{=it.content.dataset[0].title}}" href="{{=it.content.dataset[0].link}}" ><img src="{{=it.content.dataset[0].pic}}" width="100%" class="search-logo"/></a>
<div class="search-layer-3">
    <input type="text" class="search-input-3"  placeholder="{{= it.content.placeholder }}" >
    <button type="submit" class="search-icon-3"></button>
</div>
</div>
{{?? it.content.css_type==4}}
<div class="bg_style4" style="{{? it.content.bg_color=="#fff"}}background-color:#fd4000{{?}}">
    <div class="search-box-4">
    <div class="search-layer-4">
        <input type="text" class="search-input-4"  placeholder="{{= it.content.placeholder }}" >
        <button type="submit" class="search-icon-4"></button>
    </div>
    </div>
</div>
{{?? it.content.css_type==5}}
<div class="bg_style5" style="{{? it.content.bg_color=="#fff"}}background-color:#fd4000{{?}}">
    <div class="search-box-5">
    <a title="{{=it.content.dataset[0].title}}" href="{{=it.content.dataset[0].link}}" ><img src="{{=it.content.dataset[0].pic}}" width="100%" class="search-logo5"/></a>
    <div class="search-layer-5" >
        <input type="text" class="search-input-5"  placeholder="{{= it.content.placeholder }}" >
        <button type="submit" class="search-icon-5"></button>
    </div>
    </div>
</div>
{{?? it.content.css_type==6}}
<div class="search-box-6" style="{{? it.content.bg_color=="#fff"}}background-color:#fd4000{{?}}">
    <div class="search-box">
        <input type="text" class="search-m"  placeholder="{{= it.content.placeholder }}" >
        <div class="search-r">
            <div class="icon-rili"></div>
            <p>签到</p>
        </div>
    </div>
</div>
{{?}}
</div>
</script>
<script type="dot-template" id="type_ctrl_1">
<div class="formitems">  
        <label class="fi-name">显示方式：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>样式一</label>
                <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>样式二</label>
                <label><input type="radio" name="css_type" value="3"{{? it.content.css_type==3}} checked{{?}}>样式三</label>
                <label><input type="radio" name="css_type" value="4"{{? it.content.css_type==4}} checked{{?}}>样式四</label>
                <label><input type="radio" name="css_type" value="5"{{? it.content.css_type==5}} checked{{?}}>样式五</label>
                <label><input type="radio" name="css_type" value="6"{{? it.content.css_type==6}} checked{{?}}>样式六(仅社区可用)</label>
            </div>
        </div>
</div>
<div class="formitems">
        <label class="fi-name">模块上下边距：</label>
        <div class="form-controls">
            <div id='slider' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
            <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
        </div>
</div>
<div class="formitems">  
        <label class="fi-name">搜索提示：</label>   
        <input type="text" name="placeholder" class="input" value="{{= it.content.placeholder }}" maxlength="20">
</div>
<div class="formitems">
    <label class="fi-name">背景色：</label> 
        <div class="form-controls">
            <div class="colorSelector"><div attr="{{=it.content.dataset.length}}" style="background-color: {{= it.content.bg_color}}"></div></div>
        </div>
</div>
{{? it.content.css_type==2||it.content.css_type==3||it.content.css_type==5}}
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
						<input type="text" name="selector_title" id="selector_title" value="{{=  it.content.dataset[i].detail_value}}" disabled />
                        <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id" id="selector_id" value="<?php echo $value['linktype'];?>" />
                        <!--<a href="javascript:;" class="droplist-title j-droplist-toggle">
                            <span>请选择</span>
                            <i class="gicon-chevron-down mgl5"></i>
                        </a>
                        <ul class="droplist-menu" style="display: none;">
                            <li data-val="1"><a href="javascript:;">选择商品</a></li>
                            <li data-val="2"><a href="javascript:;">商品分组</a></li>
                        </ul>
                        -->
						
                        <select style="display:none;"  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
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

                        </select>
						<input style="display:none;" type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button style="display:none;" class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_{{=i}}"  class="input xlarge" style="{{? product_type_linktype!=1 || product_type_val<=0}}display:none;{{?}}height:28px;">
							{{for (k=0,m=it.type_arr[-1].length;k<m;k++) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_1"{{? type_id+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=type_name}}==={{=it.content.dataset[i].select_value}}</option>
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
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
				<div class="form-controls">
					<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
			</div>
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
				{{? it.content.css_type==5}}
                <label class="note">180*84 px(图片位于搜索栏左侧）</label>
				{{??}}
                <label class="note">300*84 px(图片位于搜索栏左侧）</label>
				{{?}}
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
{{?}}
</script>
<!--搜索框-->
<!--图片广告-->
<script  type="dot-template" id="type_con_2">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
{{? it.content.css_type==1}}
<div id="banner_tabs" class="flexslider">
    <ul class="slides">
    {{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
        <li><a title="{{=it.content.dataset[i].title}}" href="{{=it.content.dataset[i].link}}" ><img src="{{=it.content.dataset[i].pic}}" width="100%" /></a></li>
    {{}}}
    </ul>
    <ul class="flex-direction-nav" style="display:none;">
        <li><a class="flex-prev" id="btn_prev" href="javascript:;"></a></li>
        <li><a class="flex-next" id="btn_next" href="javascript:;"></a></li>
    </ul>
    <ol class="flicking_con">
    {{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
        <li><a {{? i==0}} class="on" {{?}}>{{=i+1}}</a></li>
    {{ } }}
    </ol>
</div>
{{?? it.content.css_type==2}}
<section class="members_imgad">
<ul class="img-box clearfix">
 {{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
<li {{? it.content.margin}} style="margin-bottom:{{= it.content.margin}}px;"{{?}} ><a title="{{=it.content.dataset[i].title}}" href="{{=it.content.dataset[i].link}}" ><img src="{{=it.content.dataset[i].pic}}" width="100%" /></a></li>
{{}}}
</ul>
{{??}}
{{?}}
</section>
</div>
</script>
<script type="dot-template" id="type_ctrl_2">
<div class="formitems">  
        <label class="fi-name">显示方式：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>折叠轮播</label>
                <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>分开显示</label>
            </div>
        </div>
</div>
<div class="formitems">
        <label class="fi-name">模块上下边距：</label> 
        <div class="form-controls">
            <div id='slider' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
            <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
        </div>
</div>
{{? it.content.css_type==2}}
<div class="formitems">
        <label class="fi-name">图片边距：</label>
        <div class="form-controls">
            <div id='slider-i' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
            <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight-i">{{? it.content.margin}}{{=it.content.margin+'px'}}{{??}}0px{{?}}</span>
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
                <label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="1" {{? it.content.dataset[i].sel_link_type == 1}}checked{{?}}/>链接到：</label>  
                <div class="form-controls">
                    <div class="droplist">
						<input type="text" name="selector_title" id="selector_title" value="{{= it.content.dataset[i].detail_value}}" disabled />
                        <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id" id="selector_id" value="{{= it.content.dataset[i].select_value}}" />
                        <!--<a href="javascript:;" class="droplist-title j-droplist-toggle">
                            <span>请选择</span>
                            <i class="gicon-chevron-down mgl5"></i>
                        </a>
                        <ul class="droplist-menu" style="display: none;">
                            <li data-val="1"><a href="javascript:;">选择商品</a></li>
                            <li data-val="2"><a href="javascript:;">商品分组</a></li>
                        </ul>
                        -->
                        <select style="display:none;"  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
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
						{{? it.coupon_info  && it.is_coupon>0}}
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

                        </select>
						<input style="display:none;"   type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button style="display:none;"  class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
				<div class="form-controls">
					<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
			</div>
            {{? it.content.css_type==1}}
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">1080*540 px 图片大小不超过200K</label>
            </div>
            {{?? it.content.css_type==2}}
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">宽度1080 px 图片大小不超过200K</label>
            </div>
            {{?}}
            <!--<div class="formitems">  
                <label class="fi-name">标题：</label>
                <div class="form-controls">
                    <input type="text" name="title" class="input xlarge" value="{{=it.content.dataset[i].title}}" maxlength="10">
                    <span class="fi-help-text"></span>
                </div>
            </div>
			-->
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
<!--图片广告-->
<!--分类图标-->
<script  type="dot-template" id="type_con_3">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>

	{{? it.content.css_type==1}}
		<div class="content-box {{? it.content.dataset.length>=5}}col5{{?}} {{? it.content.dataset.length<4}}col{{= it.content.dataset.length}} {{?}} ">
		{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
		    <div class="icon-box"><a href="#">{{? it.content.pro_pic_show==1}}<img src="{{=it.content.dataset[i].pic}}">{{?}}{{? it.content.pro_title_show==1}}<p style="color:{{=it.content.dataset[i].color}}">{{=it.content.dataset[i].title}}</p>{{?}}</a></div>
		{{ } }}
			<div class="clear"></div>
		</div>
	{{?}}

	{{? it.content.css_type==0}}
	<div class="{{? it.content.pro_pic_show==0}}content-flex{{?}}">
		<div class="content-box col{{= it.content.show_num }}" style="{{? it.content.pro_pic_show==1&&it.content.pro_title_show==1}}height:87px;{{?}} {{? it.content.pro_pic_show==1&&it.content.pro_title_show==0}}height:60px;{{?}} {{? it.content.pro_pic_show==0&&it.content.pro_title_show==1}}height:27px;{{?}}" >
		{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
		    <div class="icon-box"><a href="#">{{? it.content.pro_pic_show==1}}<img src="{{=it.content.dataset[i].pic}}">{{?}}{{? it.content.pro_title_show==1}}<p style="color:{{=it.content.dataset[i].color}}">{{=it.content.dataset[i].title}}</p>{{?}}</a></div>
		{{ } }}
		    <div class="clear"></div>
		</div>
		{{? it.content.all_switch==1}}
		<img class="all-switch" style="{{? it.content.pro_pic_show==1}}margin:0 auto 10px;display:block;{{?}}"  src="images/modal_all_swich.png"/>
		{{?}}
	</div>
	{{?}}
</div>
</script>
<script type="dot-template" id="type_ctrl_3">
<ul class="ctrl-nav">
	<li class="{{? it.content.nav==0}}active{{?}}">默认模板</li>
	{{? it.content.nav >= 1}}
	<li class="{{? it.content.nav==1}}active{{?}}">格式设置</li>
	<li class="{{? it.content.nav==2}}active{{?}}">链接设置</li>
	{{?}}
</ul>
<div class="ctrl-tab-box">
	<div class="ctrl-tab" {{? it.content.nav==0}}style="display:block"{{?}}>
		<div class="formitems" style="text-align:right;margin-bottom:10px;">
			<div class="modal-btn">自定义模版</div>
		</div>
		<div class="formitems">
			<img class="modal-img" src="images/fl_modal_1_1.png"/>
			<img class="modal-img" src="images/fl_modal_1_2.png"/>
			<div class="modal-text">
				<p>文字分类</p>
				<div class="modal-btn" data-modal="1">编辑模版</div>
			</div>
		</div>
		<div class="formitems">
			<img class="modal-img" src="images/fl_modal_2_1.png"/>
			<img class="modal-img" src="images/fl_modal_2_2.png"/>
			<div class="modal-text">
				<p>图片分类</p>
				<div class="modal-btn" data-modal="2">编辑模版</div>
			</div>
		</div>

	</div>
	<div class="ctrl-tab" {{? it.content.nav==1}}style="display:block"{{?}}>
		
		<div class="formitems">
		    <label class="fi-name">是否显示文字：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="pro_title_show" value="1"{{? it.content.pro_title_show==1}} checked{{?}}>显示</label>
		            <label><input type="radio" name="pro_title_show" value="0"{{? it.content.pro_title_show==0}} checked{{?}}>隐藏</label>
		        </div>
		    </div>
		</div>
		<div class="formitems">
		    <label class="fi-name">是否显示图标：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="pro_pic_show" value="1"{{? it.content.pro_pic_show==1}} checked{{?}}>显示</label>
		            <label><input type="radio" name="pro_pic_show" value="0"{{? it.content.pro_pic_show==0}} checked{{?}}>隐藏</label>
		        </div>
		    </div>
		</div>
		<div class="formitems">
		    <label class="fi-name">显示样式：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>平铺浏览</label>
		            <label><input type="radio" name="css_type" value="0"{{? it.content.css_type==0}} checked{{?}}>左右滑动</label>
		        </div>
		    </div>
		</div>
		{{? it.content.css_type==0}}
		<div class="formitems">
		    <label class="fi-name">查看全部开关：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="all_switch" value="1"{{? it.content.all_switch==1}} checked{{?}}>开启</label>
		            <label><input type="radio" name="all_switch" value="0"{{? it.content.all_switch==0}} checked{{?}}>关闭</label>
		        </div>
		    </div>
		</div>
		<div class="formitems">
		    <label class="fi-name">滑动首屏显示个数：</label> 
		    <div class="form-controls">
		        <input type="text" name="show_num" value="{{=it.content.show_num}}" style="width:100px"/>个
		    </div>
		</div>
		{{?}}
		<div class="formitems">
		    <label class="fi-name">放置位置：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="fix_top" value="1"{{? it.content.fix_top==1}} checked{{?}}>固定顶部</label>
		            <label><input type="radio" name="fix_top" value="0"{{? it.content.fix_top==0}} checked{{?}}>随页面移动</label>
		        </div>
		    </div>
		</div>
		<div class="formitems">
	        <label class="fi-name">模块上下边距：</label>
	        <div class="form-controls">
	            <div id="slider" class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
	            <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
	        </div>
		</div>
		<div class="formitems modal-text">
			<div class="modal-btn prev-step">上一步</div>
			<div class="modal-btn next-step">下一步</div>
		</div>
	</div>
	<div class="ctrl-tab" {{? it.content.nav==2}}style="display:block"{{?}}>
		<ul class="ctrl-item-list">
		{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
		    <li class="ctrl-item-list-li clearfix">
				{{? it.content.pro_pic_show==1}}
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
				{{?}}
		        <div class="fl imgnav-info">
		            <div class="formitems">  
		                <label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="1" {{? it.content.dataset[i].sel_link_type == 1}}checked{{?}}/>链接到：</label>  
		                <div class="form-controls">
		                    <div class="droplist">
								<input type="text" name="selector_title" id="selector_title" value="{{= it.content.dataset[i].detail_value}}" disabled />
		                        <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
								<input type=hidden name="selector_id" id="selector_id" value="{{= it.content.dataset[i].select_value}}" />
		                        <!--<a href="javascript:;" class="droplist-title j-droplist-toggle">
		                            <span>请选择</span>
		                            <i class="gicon-chevron-down mgl5"></i>
		                        </a>
		                        <ul class="droplist-menu" style="display: none;">
		                            <li data-val="1"><a href="javascript:;">选择商品</a></li>
		                            <li data-val="2"><a href="javascript:;">商品分组</a></li>
		                        </ul>
		                        -->
		                        <select  name="type_id_2" style="display:none;"  id="type_id_2"  class="input xlarge" style="height:28px;">
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

		                        </select>
								<input type="text" style="display:none;"  id="search_input_{{=i}}" value="" class="input search-input" /><button style="display:none;"  class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
					<div class="formitems">
						<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
						<div class="form-controls">
							<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
						</div>
					</div>
		            <div class="formitems">  
		                <label class="fi-name">标题：</label>
		                <div class="form-controls">
		                    <input type="text" name="title" class="input xlarge" value="{{=it.content.dataset[i].title}}" maxlength="5">
		                    <span class="fi-help-text"></span>
		                </div>
		            </div>
		            <div class="formitems">
		                <label class="fi-name">字体颜色：</label> 
		                <div class="form-controls">
		                    <div class="colorSelector"><div style="background-color: {{=it.content.dataset[i].color}}"></div></div>
		                </div>
		            </div>
		            <div class="formitems">  
		                <label class="fi-name">建议尺寸：</label>
		                <label class="note">110*110 px</label>
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
		<div class="formitems modal-text">
			<div class="modal-btn prev-step">上一步</div>
		</div>
	</div>
</div>


</script>
<!--分类图标-->
<!--橱窗二图-->
<script  type="dot-template" id="type_con_9">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
{{? it.content.css_type==1}}
<div class="hf-box-1">
    <a href="{{=it.content.dataset[0].link}}"><img  class="hf-img"  src="{{=it.content.dataset[0].pic}}"></a>
    <a href="{{=it.content.dataset[1].link}}"><img  class="hf-img"  src="{{=it.content.dataset[1].pic}}"></a>
    <div class="clear"></div>
</div>
{{?}}
</div>
</script>
<script type="dot-template" id="type_ctrl_9">
<div class="formitems">  
        <label class="fi-name">布局方式：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>两列</label>
            </div>
        </div>
</div>
<div class="formitems">
        <label class="fi-name">模块上下边距：</label>
        <div class="form-controls">
            <div id='slider' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
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
						<input type="text" name="selector_title" id="selector_title" value="{{= it.content.dataset[i].detail_value}}" disabled />
                        <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id" id="selector_id" value="{{= it.content.dataset[i].select_value}}" />
                        <!--<a href="javascript:;" class="droplist-title j-droplist-toggle">
                            <span>请选择</span>
                            <i class="gicon-chevron-down mgl5"></i>
                        </a>
                        <ul class="droplist-menu" style="display: none;">
                            <li data-val="1"><a href="javascript:;">选择商品</a></li>
                            <li data-val="2"><a href="javascript:;">商品分组</a></li>
                        </ul>
                        -->
						
                        <select style="display:none;"   name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
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

                        </select>
						<input style="display:none;"  type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button style="display:none;"  class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
				<div class="form-controls">
					<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
			</div>
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">宽度540 px ，图片大小不超过200K</label>
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
<!--橱窗二图-->
<!--橱窗三图-->
<script  type="dot-template" id="type_con_4">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
{{? it.content.css_type==1}}
<div class="zt-box">
    <a  class="img1" href="#" style="background-image:url({{=it.content.dataset[0].pic}})"></a>
    <a  class="img2" href="#" style="background-image:url({{=it.content.dataset[1].pic}})"></a>
    <a  class="img3" href="#" style="background-image:url({{=it.content.dataset[2].pic}})"></a>
    <div class="clear"></div>
</div>
{{?? it.content.css_type==2}}
<div class="fl-box clearfix">
    <a href="{{=it.content.dataset[0].link}}"><img  class="f-img"  src="{{=it.content.dataset[0].pic}}"></a>
    <a href="{{=it.content.dataset[1].link}}"><img  class="f-img"  src="{{=it.content.dataset[1].pic}}"></a>
    <a href="{{=it.content.dataset[2].link}}"><img  class="f-img"  src="{{=it.content.dataset[2].pic}}"></a>
</div>
{{?}}
</div>
</script>
<script type="dot-template" id="type_ctrl_4">
<div class="formitems">  
        <label class="fi-name">布局方式：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>两列</label>
                <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>三列</label>
            </div>
        </div>
</div>
<div class="formitems">
        <label class="fi-name">模块上下边距：</label>
        <div class="form-controls">
            <div id='slider' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
            <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
        </div>
</div>
{{? it.content.css_type==1}}
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
						<input type="text" name="selector_title" id="selector_title" value="{{= it.content.dataset[i].detail_value}}" disabled />
                        <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id" id="selector_id" value="{{= it.content.dataset[i].select_value}}" />
                        <!--<a href="javascript:;" class="droplist-title j-droplist-toggle">
                            <span>请选择</span>
                            <i class="gicon-chevron-down mgl5"></i>
                        </a>
                        <ul class="droplist-menu" style="display: none;">
                            <li data-val="1"><a href="javascript:;">选择商品</a></li>
                            <li data-val="2"><a href="javascript:;">商品分组</a></li>
                        </ul>
                        -->
                        <select style="display:none;" name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
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

                        </select>
						<input style="display:none;" type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button style="display:none;" class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
				<div class="form-controls">
					<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
			</div>
            {{? i==0}}
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">540*540 px ，图片大小不超过200K</label>
            </div>
            {{?? i==1||i==2}}
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">540*270 px ，图片大小不超过200K</label>
            </div>
            {{?}}
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
{{?? it.content.css_type==2}}
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
						<input type="text" name="selector_title" id="selector_title" value="{{= it.content.dataset[i].detail_value}}" disabled />
                        <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id" id="selector_id" value="{{= it.content.dataset[i].select_value}}" />
                        <!--<a href="javascript:;" class="droplist-title j-droplist-toggle">
                            <span>请选择</span>
                            <i class="gicon-chevron-down mgl5"></i>
                        </a>
                        <ul class="droplist-menu" style="display: none;">
                            <li data-val="1"><a href="javascript:;">选择商品</a></li>
                            <li data-val="2"><a href="javascript:;">商品分组</a></li>
                        </ul>
                        -->
                        <select style="display:none;"  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
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

                        </select>
						<input style="display:none;" type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button style="display:none;" class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
				<div class="form-controls">
					<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
			</div>
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">360*360 px ，图片大小不超过200K</label>
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
{{?}}
</script>
<!--橱窗三图-->
<!--橱窗（四图）-->
<script  type="dot-template" id="type_con_8">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
{{? it.content.css_type==1}}
<div class="window-box-1 clearfix">
    <a  class="img1" href="#" style="background-image:url({{=it.content.dataset[0].pic}})"></a>
    <a  class="img2" href="#" style="background-image:url({{=it.content.dataset[1].pic}})"></a>
    <a  class="img3" href="#" style="background-image:url({{=it.content.dataset[2].pic}})"></a>
    <a  class="img4" href="#" style="background-image:url({{=it.content.dataset[3].pic}})"></a>
</div>
{{?? it.content.css_type==2}}
<div class="window-box-2 clearfix">
    <a href="{{=it.content.dataset[0].link}}"><img src="{{=it.content.dataset[0].pic}}" class="img1"></a>
    <a href="{{=it.content.dataset[1].link}}"><img src="{{=it.content.dataset[1].pic}}"  class="img1"></a>
    <a href="{{=it.content.dataset[2].link}}"><img src="{{=it.content.dataset[2].pic}}"  class="img1"></a>
    <a href="{{=it.content.dataset[3].link}}"><img src="{{=it.content.dataset[3].pic}}"  class="img1"></a>
</div>
{{?}}
</div>
</script>
<script type="dot-template" id="type_ctrl_8">
<div class="formitems">  
        <label class="fi-name">布局方式：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>样式一</label>
                <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>样式二</label>
            </div>
        </div>
</div>
<div class="formitems">
        <label class="fi-name">模块上下边距：</label>
        <div class="form-controls">
            <div id='slider' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
            <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
        </div>
</div>
{{? it.content.css_type==1}}
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
						<input type="text" name="selector_title" id="selector_title" value="{{= it.content.dataset[i].detail_value}}" disabled />
                        <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id" id="selector_id" value="{{= it.content.dataset[i].select_value}}" />
                        <!--<a href="javascript:;" class="droplist-title j-droplist-toggle">
                            <span>请选择</span>
                            <i class="gicon-chevron-down mgl5"></i>
                        </a>
                        <ul class="droplist-menu" style="display: none;">
                            <li data-val="1"><a href="javascript:;">选择商品</a></li>
                            <li data-val="2"><a href="javascript:;">商品分组</a></li>
                        </ul>
                        -->
                        <select style="display:none;"  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
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

                        </select>
						<input style="display:none;" type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button style="display:none;" class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
				<div class="form-controls">
					<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
			</div>
            {{? i==0}}
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">432*540 px ，图片大小不超过100K</label>
            </div>
            {{?? i==1}}
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">648*216 px ，图片大小不超过100K</label>
            </div>
            {{?? i==2||i==3}}
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">324*324 px ，图片大小不超过100K</label>
            </div>
            {{?}}
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
{{?? it.content.css_type==2}}
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
						<input type="text" name="selector_title" id="selector_title" value="{{= it.content.dataset[i].detail_value}}" disabled />
                        <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id" id="selector_id" value="{{= it.content.dataset[i].select_value}}" />
                        <!--<a href="javascript:;" class="droplist-title j-droplist-toggle">
                            <span>请选择</span>
                            <i class="gicon-chevron-down mgl5"></i>
                        </a>
                        <ul class="droplist-menu" style="display: none;">
                            <li data-val="1"><a href="javascript:;">选择商品</a></li>
                            <li data-val="2"><a href="javascript:;">商品分组</a></li>
                        </ul>
                        -->
                        <select style="display:none;"  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
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

                        </select>
						<input style="display:none;"  type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button style="display:none;"  class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
				<div class="form-controls">
					<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
			</div>
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">270*270 px ，图片大小不超过100K</label>
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
{{?}}
</script>
<!--橱窗（四图）-->
<!--分类产品-->
<script  type="dot-template" id="type_con_5">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
<div class="floor clearfix">
{{? it.content.css_type==1}}
    <div class="pro-box">
        <img src="images/img-product.jpg">
        {{? it.content.pro_title_show==1}}<p {{? it.content.pro_title_twoline==0}}class="goods-title"{{?? it.content.pro_title_twoline==1}}class="goods-title-2"{{?}}>产品名称</p>{{?}}
        <span class="goods-price"><span class="yen">¥</span>149.00</span>
        <span class="old-price"><span class="yen">¥</span>249.00</span>
        {{? it.content.show_sale==1}}<span class="sale">已售 500</span>{{?}}
    </div>
    <div class="pro-box">
        <img src="images/img-product.jpg">
        {{? it.content.pro_title_show==1}}<p {{? it.content.pro_title_twoline==0}}class="goods-title"{{?? it.content.pro_title_twoline==1}}class="goods-title-2"{{?}}>产品名称</p>{{?}}
        <span class="goods-price"><span class="yen">¥</span>149.00</span>
        <span class="old-price"><span class="yen">¥</span>249.00</span>
        {{? it.content.show_sale==1}}<span class="sale">已售 500</span>{{?}}
    </div>
    {{?? it.content.css_type==2}}
    <div class="pro-box-solo">
        <img src="images/img-product.jpg">
        {{? it.content.pro_title_show==1}}<p {{? it.content.pro_title_twoline==0}}class="goods-title"{{?? it.content.pro_title_twoline==1}}class="goods-title-2"{{?}}>产品名称</p>{{?}}
        {{? it.content.show_sale==1}}<span class="sale-solo">已售 500</span>{{?}}
        <p class="price-box">
            <span class="goods-price-solo"><span class="yen">¥</span>149.00</span>
            <span class="old-price-solo"><span class="yen">¥</span>249.00</span>
        </p>
    </div>
    <div class="pro-box-solo">
        <img src="images/img-product.jpg">
        {{? it.content.pro_title_show==1}}<p {{? it.content.pro_title_twoline==0}}class="goods-title"{{?? it.content.pro_title_twoline==1}}class="goods-title-2"{{?}}>产品名称</p>{{?}}
        {{? it.content.show_sale==1}}<span class="sale-solo">已售 500</span>{{?}}
        <p class="price-box">
            <span class="goods-price-solo"><span class="yen">¥</span>149.00</span>
            <span class="old-price-solo"><span class="yen">¥</span>249.00</span>
        </p>
    </div>
    {{??}}
{{?}}
</div>
</div>
</script>
<script type="dot-template" id="type_ctrl_5">
<div class="formitems">
    <label class="fi-name">布局方式：</label>
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="css_type" value="1" {{? it.content.css_type==1}} checked{{?}}>双列商品</label>
            <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>单列商品</label>
        </div>
    </div>
</div>
<div class="formitems">
    <label class="fi-name">是否显示标题：</label> 
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="pro_title_show" value="1" {{? it.content.pro_title_show==1}} checked{{?}}>显示</label>
            <label><input type="radio" name="pro_title_show" value="0"{{? it.content.pro_title_show==0}} checked{{?}}>隐藏</label>
            {{? it.content.pro_title_show==1}}<label><input type="checkbox" name="pro_title_twoline"{{? it.content.pro_title_twoline==1}} checked{{?}}>两行显示{{?}}
            </label>
        </div>
    </div>
</div>
<div class="formitems">
    <label class="fi-name">是否显示销量：</label> 
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="show_sale" value="1"{{? it.content.show_sale==1}} checked{{?}}>显示</label>
            <label><input type="radio" name="show_sale" value="0"{{? it.content.show_sale==0}} checked{{?}}>隐藏</label>
        </div>
    </div>
</div>
<div class="formitems">
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="shop_type" value="0"{{? it.content.shop_type==0}} checked{{?}}>线上商城</label>
            <label><input type="radio" name="shop_type" value="1"{{? it.content.shop_type==1}} checked{{?}}>线下商城</label>
        </div>
    </div>
</div>
<div class="formitems" style="{{? it.content.shop_type==0}}display:none;{{?}}">
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="divide_type" value="0"{{? it.content.divide_type==0}} checked{{?}}>选择产品分类</label>
            <label><input type="radio" name="divide_type" value="1"{{? it.content.divide_type==1}} checked{{?}}>选择店铺</label>
        </div>
    </div>
</div>
            <div class="formitems">  
                <label class="fi-name">{{? it.content.divide_type==1}}选择店铺：{{??}}选择分类：{{?}}</label>  
                <div class="form-controls">
                    <div class="droplist">
                        <select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;{{? it.content.shop_type==1}}display:none;{{?}}">
                        {{? it.type_arr[-1]}}                        
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value="-1">所有分类</option>	
                            {{for (k=0,m=it.type_arr[-1].length;k<m;k++) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_1"{{? type_id+'_1'==it.content.dataset[0].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_1"{{? ctype_id+'_1'==it.content.dataset[0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_1"{{? ctype_id3+'_1'==it.content.dataset[0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}_1"{{? ctype_id4+'_1'==it.content.dataset[0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
                        {{?}}
                        </select>
                        <select  name="type_id_4"  id="type_id_4"  class="input xlarge" style="height:28px;{{? (it.content.shop_type==0) || (it.content.shop_type==1 && it.content.divide_type==1)}}display:none;{{?}}">
                        {{? it.cityarea_shop_protype_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value="-1">请选择分类</option>
							{{for (k=0,m=it.cityarea_shop_protype_arr.length;k<m;k++) {
								type_id_name=it.cityarea_shop_protype_arr[k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_1"{{? type_id+'_1'==it.content.dataset[0].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{ } }}
                        {{?}}
                        </select>
                        <select  name="type_id_5"  id="type_id_5"  class="input xlarge" style="height:28px;{{? (it.content.shop_type==0) || (it.content.shop_type==1 && it.content.divide_type==0)}}display:none;{{?}}">
                        {{? it.cityarea_shop_arr}}
                        <optgroup label="---------------店铺名---------------"></optgroup>
						<option value="-1">请选择店铺</option>
							{{for (k=0,m=it.cityarea_shop_arr.length;k<m;k++) {
								type_id_name=it.cityarea_shop_arr[k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_1"{{? type_id+'_1'==it.content.dataset[0].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{ } }}
                        {{?}}
                        </select>
                    </div>
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
                </div>
            </div>
<div class="formitems">
    <label class="fi-name">显示商品数量：</label> 
    <div class="form-controls">
        <input type="number"  name="pro_numshow" class="input xlarge" value="{{= it.content.pro_numshow}}"> <span class="fi-help-text"></span> 
    </div> 
</div>
<input type="hidden" name="goods_ids" value="">
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
</script>
<!--分类产品-->
<!--底部菜单-->
<script  type="dot-template" id="type_con_6">
<div class="con_display" style="min-height:55px;{{? it.content.padding}}padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px{{?}}">
    <div style="background-color:{{=it.content.bg_color}}" class="foot-box col{{= it.content.dataset.length }} clearfix" >
    {{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
        <div class="icon-box">
            <a href="#"><img src="{{=it.content.dataset[i].pic}}" {{? it.content.pro_title_show==0}} class="big-img"{{?}}>{{? it.content.pro_title_show==1}}<p style="color:{{=it.content.dataset[i].color}}">{{=it.content.dataset[i].title}}</p>{{?}}</a>
        </div>
    {{ } }}
    </div>
</div>
</script>
<script type="dot-template" id="type_ctrl_6">
<div class="formitems" style="text-align:right;margin-bottom:10px;">
	<input type="hidden" name="bottom_id" id="bottom_id" value="{{=  it.content.bottom_id}}" />
	<button class="chose-bottom" onclick="showBottomLabel(this)">选择底部模板</button>
</div>
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
/*<div class="formitems">  
        <label class="fi-name">放置位置：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="foot_position" value="1" {{? it.content.foot_position==1}} checked{{?}}>固定跟随页面移动</label>
                <label><input type="radio" name="foot_position" value="2"{{? it.content.foot_position==2}} checked{{?}}>不跟随页面移动</label>
            </div>
        </div>
</div>*/
<div class="formitems">
    <label class="fi-name">是否显示文字：</label> 
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="pro_title_show" value="1"{{? it.content.pro_title_show==1}} checked{{?}}>显示</label>
            <label><input type="radio" name="pro_title_show" value="0"{{? it.content.pro_title_show==0}} checked{{?}}>隐藏</label>
        </div>
    </div>
</div>
<div class="formitems">
    <label class="fi-name">背景颜色：</label> 
    <div class="form-controls">
        <div class="colorSelector"><div color="bg_color" style="background-color: {{=it.content.bg_color}}"></div></div>
    </div>
</div>
<ul class="ctrl-item-list"> 
{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
    <li class="ctrl-item-list-li clearfix" style="opacity:0.6;">
        <div class="fl">
            <div class="imgnav j-selectimg">
            <form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{=i}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{=i}});">
                /*<input type="hidden" name="getImg" id='getImg{{=i}}' value="{{=it.content.dataset[i].pic}}">*/
                <p class="imgnav-select">
                    <input type="file" disabled size="20" name="upfile2" id="upfile2" class="up" >
                    <img src="{{=it.content.dataset[i].pic}}">
                </p>
                /*<input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
                <input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
                <input type="hidden" name="img_sort" value="{{=i}}">*/
                
            </form>
            </div>
            <span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
        </div>
        <div class="fl imgnav-info">
            <div class="formitems">  
                <label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" /*{{? it.content.dataset[i].sel_link_type == 1}}checked{{?}}*/ value="1"/>链接到：</label> 
                <div class="form-controls">
                    <div class="droplist">
						<input type="text" name="selector_title" id="selector_title" value="{{=  it.content.dataset[i].column_title}}" disabled />
                        <button type="button" class="link-choose WSY-skin-bg">请选择</button>
						/*<input type=hidden name="selector_id" id="selector_id" value="<?php echo $value['linktype'];?>" />*/
                        /*<!--<a href="javascript:;" class="droplist-title j-droplist-toggle">
                            <span>请选择</span>
                            <i class="gicon-chevron-down mgl5"></i>
                        </a>
                        <ul class="droplist-menu" style="display: none;">
                            <li data-val="1"><a href="javascript:;">选择商品</a></li>
                            <li data-val="2"><a href="javascript:;">商品分组</a></li>
                        </ul>
                        -->
                        <select style="display:none;"   name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
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

                        </select>
						<input style="display:none;" type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button style="display:none;" class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
                    <span class="fi-help-text j-verify-linkType"></span>*/
                </div>
            </div>
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" /*{{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}*//>填写链接：</label>
				<div class="form-controls">
					<input type="text" disabled name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
			</div>
            <div class="formitems">  
                <label class="fi-name">标题：</label>
                <div class="form-controls">
                    <input disabled type="text" name="title" class="input xlarge" value="{{=it.content.dataset[i].title}}" maxlength="10">
                    <span class="fi-help-text"></span>
                </div>
            </div>
            <div class="formitems">
                <label class="fi-name">字体颜色：</label> 
                <div class="form-controls">
                    <div class="disabledColorSelector"><div color="color" style="background-color: #{{=it.content.dataset[i].color}}"></div></div>
                </div>
            </div>
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">110*110 px</label>
            </div>
        </div>
   <div class="ctrl-item-list-actions">
            /*<a href="javascript:;" title="上移" class="j-moveup"><i class="gicon-arrow-up"></i></a>
            <a href="javascript:;" title="下移" class="j-movedown"><i class="gicon-arrow-down"></i></a>
            <a href="javascript:;" title="删除" class="j-del"><i class="gicon-remove"></i></a>*/
        </div>
    </li>
    {{ } }}
    /*<li class="ctrl-item-list-add" title="添加">+</li>*/
</ul>
</script>
<!--底部菜单-->
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
						<input type="text" name="selector_title" id="selector_title" value="{{=  it.content.dataset[i].detail_value}}" disabled />
                        <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id" id="selector_id" value="<?php echo $value['linktype'];?>" />
                        <!--<a href="javascript:;" class="droplist-title j-droplist-toggle">
                            <span>请选择</span>
                            <i class="gicon-chevron-down mgl5"></i>
                        </a>
                        <ul class="droplist-menu" style="display: none;">
                            <li data-val="1"><a href="javascript:;">选择商品</a></li>
                            <li data-val="2"><a href="javascript:;">商品分组</a></li>
                        </ul>
                        -->
                        <select style="display:none;"  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
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

                        </select>
						<input style="display:none;" type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button style="display:none;" class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
                    <div style="clear:both"></div>
                    {{? it.content.dataset[i].select_value}}
					{{	selv=it.content.dataset[i].select_value.split("_");
						select_val=selv[0];
		            }}
					{{? select_val==-11}}
					
					    <label class="fi-name"></label>  
					    <div class="formitems">
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
					
					{{?}}
					{{?}}
					
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
<!--视频-->


<script type="dot-template" id="type_con_10">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
	{{?it.content.video_link}}
		{{? it.content.threed_link==0 || it.content.threed_link==null}}<img src="images/video_link.jpg" style="width=100%">
		{{??}}<img src="images/video_link_threed.jpg" style="width=100%">
		{{?}}
	{{??}}<img src="images/video.jpg" style="width=100%">
	{{?}}
</div>
</script>
<script type="dot-template" id="type_ctrl_10">
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
<div class="formitems" style="width:550px;">  
                <label class="fi-name"><?php if($threed_open && $is_travelcard){?><input type="radio" style="float: left;margin-top: 7px;margin-left: 25px;" name="3d_link_{{=it.id}}" value="1" {{? it.content.threed_link==0 || it.content.threed_link==null }}checked{{??}}{{?}} /><?php }?>视频地址：</label>
                <div class="form-controls">
                    <input type="text" name="video_link" class="input xlarge" value="{{? it.content.threed_link==0 || it.content.threed_link==null }}{{? it.content.video_link==null}}{{??}}{{=it.content.video_link}}{{?}}{{??}}{{?}}" >
					<span class="videotips">
						目前只支持腾讯视频，请添加(通用代码)处以http开头的视频地址<br>
						如下 http://v.qq.com/iframe/player.html?vid=f01980mc610&tiny=0&auto=0
					</span>
                    <span class="fi-help-text"></span>
                </div>
            </div>
<?php 
if($threed_open && $is_travelcard){
?>
<div class="formitems">
    <label class="fi-name"><input type="radio" style="float: left;margin-top: 7px;margin-left: 25px;" name="3d_link_{{=it.id}}" value="2" {{? it.content.threed_link==0 || it.content.threed_link==null}}{{??}}checked{{?}} />3D素材库：</label>
    <div class="form-controls">
    	<input type="text" name="video_link" class="input xlarge" value="{{? it.content.threed_link==0 || it.content.threed_link==null || it.content.threed_link==1}}{{??}}{{=it.content.video_link}}{{?}}" >
	    <button style="height: 35px;width: 120px;background: white;border-radius: 10px;" onclick="show_three_d(this)" data-id="{{=it.id}}" >选择3D素材</button>
	    <span class="videotips">
			已选：<input type="text" name="threed_content_{{=it.id}}" value="{{? it.content.threed_link==0 || it.content.threed_link==null || it.content.threed_link==1 }}{{??}}{{=it.content.threed_link}}{{?}}" style="border: none;"><br/>
		</span>
    </div>
</div>
<?php
}
?>	
</script>
<!--视频-->
<!--LBS定位-->
<script  type="dot-template" id="type_con_11">
<div class="con_display" style="background-color:{{= it.content.bg_color}};{{? it.content.dataset[0].pic}}background-image:url({{=it.content.dataset[0].pic}});background-size:100% 100%;background-repeat:no-repeat;{{?}}{{? it.content.padding}}padding:{{= it.content.padding}}px 0;{{?}}">
{{? it.content.css_type==1}}
 	<div style="margin-left:10px;height:50px;line-height:50px;">
		<img src="images/lbs.png" style="vertical-align: middle;"/><span style="color:{{= it.content.color}};font-size:16px;margin-left:5px;">东莞</span>
		<img src="images/xiala.png" style="display: inline-block;margin: 21px 5px;">
	</div> 
{{?? it.content.css_type==2}}
	<div style="margin-left:10px;">
		<div class="lbs_search2">
			<div class="f_box first">
				<p class="f_box" style="color:{{= it.content.color}};font-size:16px;">东莞</p>
				<img class="f_box" src="images/xiala.png">
			</div>
			<div class="f_box second" style="width:72%">
				<input type="text" placeholder="{{= it.content.placeholder}}">
			</div>
		</div>
	</div> 
{{?? it.content.css_type==3}}
	<div style="margin-left:10px;">
		<div class="lbs_search3">
			<div class="f_box first">
				<p class="f_box" style="color:{{= it.content.color}};font-size:16px;">东莞</p>
				<img class="f_box" src="images/xiala.png">
			</div>
			<div class="f_box second">
				<input type="text" placeholder="{{= it.content.placeholder}}">
			</div>
			<div class="f_box third">
				<img src="images/fdj.png">
			</div>			
		</div>
	</div> 
{{?}}
</div>
</script>
<script type="dot-template" id="type_ctrl_11">
<div class="formitems">  
	<label class="fi-name">显示方式：</label>   
    <div class="form-controls">
		<div class="radio-group">
			<label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>样式一</label>
            <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>样式二</label>
            <label><input type="radio" name="css_type" value="3"{{? it.content.css_type==3}} checked{{?}}>样式三</label>
        </div>
	</div>
</div>
{{? it.content.css_type!=1}}
<div class="formitems">  
        <label class="fi-name">搜索提示：</label>   
        <input type="text" name="placeholder" class="input" value="{{= it.content.placeholder }}" maxlength="20">
</div>
{{?}}
<div class="formitems">
    <label class="fi-name">背景颜色：</label> 
        <div class="form-controls">
            <div class="colorSelector"><div color="bg_color" style="background-color: {{= it.content.bg_color}}"></div></div>
        </div>
</div>
<div class="formitems">
    <label class="fi-name">字体颜色：</label> 
	<div class="form-controls">
		<div class="colorSelector"><div color="color" style="background-color: {{=it.content.color}}"></div></div>
	</div>
</div>
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
<ul class="ctrl-item-list"> 
    <li class="ctrl-item-list-li clearfix">
		<a href="javascript:;" title="删除" class="j-del" style="position:relative;right:28px;z-index:1000;bottom:5px;"><i class="gicon-remove"></i></a>
		<div class="fl" style="margin-right:20px;">
            <div class="imgnav j-selectimg">
            <form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img0" enctype="multipart/form-data" method="post" onsubmit="return saveReport(0);">
                <input type="hidden" name="getImg" id='getImg0' value="{{=it.content.dataset[0].pic}}">
                <p class="imgnav-select">
                    <input type="file" size="20" name="upfile2" id="upfile2" class="up" >
                    <img src="{{=it.content.dataset[0].pic}}">
                </p>
                <input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
                <input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
                <input type="hidden" name="img_sort" value="0">
                
            </form>
            </div>
            <span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
        </div>
		<div class="formitems">  
			<label class="fi-name">背景图片建议尺寸：</label>
			<label class="note">320*35 px ，图片大小不超过100K</label>
        </div>
	</li>
</ul>
</script>
<!--LBS定位-->
<!--LBS城市广告-->
<script  type="dot-template" id="type_con_12">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
{{? it.content.css_type==1}}
<div id="banner_tabs" class="flexslider">
    <ul class="slides">
    {{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
		<div style="display:inline-block;position:absolute;right:5px;top:5px;">
			<span style="font-size:16px;color:#fff;">{{= it.content.city_name}}</span>
		</div>
        <li><a title="{{=it.content.dataset[i].title}}" href="{{=it.content.dataset[i].link}}" ><img src="{{=it.content.dataset[i].pic}}" width="100%" /></a></li>
    {{}}}
    </ul>
    <ul class="flex-direction-nav" style="display:none;">
        <li><a class="flex-prev" id="btn_prev" href="javascript:;"></a></li>
        <li><a class="flex-next" id="btn_next" href="javascript:;"></a></li>
    </ul>
    <ol class="flicking_con">
    {{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
        <li><a {{? i==0}} class="on" {{?}}>{{=i+1}}</a></li>
    {{ } }}
    </ol>
</div>
{{?? it.content.css_type==2}}
<section class="members_imgad">
<ul class="img-box clearfix">
{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
<div style="display:inline-block;position:absolute;right:5px;top:5px;">
	<span style="font-size:16px;color:#fff;">{{= it.content.city_name}}</span>
</div>
<li {{? it.content.margin}} style="margin-bottom:{{= it.content.margin}}px;"{{?}} ><a title="{{=it.content.dataset[i].title}}" href="{{=it.content.dataset[i].link}}" ><img src="{{=it.content.dataset[i].pic}}" width="100%" /></a></li>
{{}}}
</ul>
{{?}}
</section>
</div>
</script>
<script type="dot-template" id="type_ctrl_12">
<div class="formitems">  
        <label class="fi-name">显示方式：</label>   
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>折叠轮播</label>
                <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>分开显示</label>
            </div>
        </div>
</div>
<div class="formitems">
        <label class="fi-name">模块上下边距：</label> 
        <div class="form-controls">
            <div id='slider' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
            <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
        </div>
</div>
{{? it.content.css_type==2}}
<div class="formitems">
        <label class="fi-name">图片边距：</label>
        <div class="form-controls">
            <div id='slider-i' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
            <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight-i">{{? it.content.margin}}{{=it.content.margin+'px'}}{{??}}0px{{?}}</span>
        </div>
</div>
{{?}}
<select class = "select-address" id = "location_p{{=it.id}}" name="location_p">
</select>

<select class = "select-address" style="margin-left:5px;" id = "location_c{{=it.id}}" name="city_name">
</select>

<ul class="ctrl-item-list" style="margin-top:5px;"> 
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
						<input type="text" name="selector_title" id="selector_title" value="{{= it.content.dataset[i].detail_value}}" disabled />
                        <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id" id="selector_id" value="{{= it.content.dataset[i].select_value}}" />
                        <select style="display:none;"  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
                        {{? it.fixed_link}}
                        	{{? it.content.dataset[i].select_value }}
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

                        </select>
						<input style='display:none;' type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button style="display:none;" class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
            {{? it.content.css_type==1}}
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">1080*540 px 图片大小不超过200K</label>
            </div>
            {{?? it.content.css_type==2}}
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">宽度1080 px 图片大小不超过200K</label>
            </div>
            {{?}}
			<div class="formitems">  
                <label class="fi-name">开始时间：</label>
                <label class="note"><input type="text" style="width:150px" id="starttime{{= i}}" name="start_time" value="{{= it.content.dataset[i].start_time}}" ></label>
            </div>
			<div class="formitems">  
                <label class="fi-name">结束时间：</label>
                <label class="note"><input type="text" style="width:150px" id="endtime{{= i}}" name="end_time" value="{{= it.content.dataset[i].end_time}}" ></label>
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
<!--LBS城市广告-->
<!--滚动公告栏-->
<script type="text/j-template" id="type_con_13">
<div class="con_display" {{? it.content.padding}}style="padding:{{= it.content.padding}}px 0;"{{?}}>
{{? it.content.css_type==1}}
<div style="height:40px;line-height:40px;background-color:#fef7ee;border:1px solid #ffcc74;padding-left:34px;background-image:url(/weixinpl/common_shop/common/custom_temp/images/laba01.png);background-repeat:no-repeat;background-position:10px 9px;background-size:20px 20px;">
	<div id="marquee{{= it.id}}" class="marquee">
		<ul>
		{{for (var j=0,k=it.content.dataset.length;j<k;j++) { }}
			<li style="{{? it.content.rolling_direction==1}}display:inline-block;width:{{= it.content.dataset[j].title.length*16+314-34}}px;{{?}}color:#ffaf74;"><a href="javascript:#;" style="color:#ffaf74;font-size:16px;">{{= it.content.dataset[j].title}}</a></li>
		{{ } }}
		</ul>
	</div>
</div>
{{?? it.content.css_type==2}}
<div style="height:40px;line-height:40px;background-color:#ffebed;border:1px solid #f7929c;padding-left:34px;background-image:url(/weixinpl/common_shop/common/custom_temp/images/laba02.png);background-repeat:no-repeat;background-position:10px 9px;background-size:20px 20px;">
	<div id="marquee{{= it.id}}" class="marquee">
		<ul>
		{{for (var j=0,k=it.content.dataset.length;j<k;j++) { }}
			<li style="{{? it.content.rolling_direction==1}}display:inline-block;width:{{= it.content.dataset[j].title.length*16+314-34}}px;{{?}}color:#db8089;"><a href="javascript:#;" style="color:#db8089;font-size:16px;">{{= it.content.dataset[j].title}}</a></li>
		{{ } }}
		</ul>
	</div>
</div>
{{?? it.content.css_type==3}}
<div style="height:40px;line-height:40px;background-color:#f6fee7;border:1px solid #b4e14d;padding-left:34px;background-image:url(/weixinpl/common_shop/common/custom_temp/images/laba03.png);background-repeat:no-repeat;background-position:10px 9px;background-size:20px 20px;">
	<div id="marquee{{= it.id}}" class="marquee">
		<ul>
		{{for (var j=0,k=it.content.dataset.length;j<k;j++) { }}
			<li style="{{? it.content.rolling_direction==1}}display:inline-block;width:{{= it.content.dataset[j].title.length*16+314-34}}px;{{?}}color:#aec181;"><a href="javascript:#;" style="color:#aec181;font-size:16px;">{{= it.content.dataset[j].title}}</a></li>
		{{ } }}
		</ul>
	</div>
</div>
{{?? it.content.css_type==4}}
<div style="height:40px;line-height:40px;background-color:#dff9ff;border:1px solid #78c3d4;padding-left:34px;background-image:url(/weixinpl/common_shop/common/custom_temp/images/laba04.png);background-repeat:no-repeat;background-position:10px 9px;background-size:20px 20px;">
	<div id="marquee{{= it.id}}" class="marquee">
		<ul>
		{{for (var j=0,k=it.content.dataset.length;j<k;j++) { }}
			<li style="{{? it.content.rolling_direction==1}}display:inline-block;width:{{= it.content.dataset[j].title.length*16+314-34}}px;{{?}}color:#8ac4d1;"><a href="javascript:#;" style="color:#8ac4d1;font-size:16px;">{{= it.content.dataset[j].title}}</a></li>
		{{ } }}
		</ul>
	</div>
</div>
{{?? it.content.css_type==5}}
<div style="height:40px;line-height:40px;background-color:#fcecff;border:1px solid #efabff;padding-left:34px;background-image:url(/weixinpl/common_shop/common/custom_temp/images/laba05.png);background-repeat:no-repeat;background-position:10px 9px;background-size:20px 20px;">
	<div id="marquee{{= it.id}}" class="marquee">
		<ul>
		{{for (var j=0,k=it.content.dataset.length;j<k;j++) { }}
			<li style="{{? it.content.rolling_direction==1}}display:inline-block;width:{{= it.content.dataset[j].title.length*16+314-34}}px;{{?}}color:#d69be4;"><a href="javascript:#;" style="color:#d69be4;font-size:16px;">{{= it.content.dataset[j].title}}</a></li>
		{{ } }}
		</ul>
	</div>
</div>
{{?}}
</div>
</script>
<script type="dot-template" id="type_ctrl_13">
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
<ul class="ctrl-item-list"> 
{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
    <li class="ctrl-item-list-li clearfix">
        <div class="fl imgnav-info">
			<div class="formitems">  
                <label class="fi-name">公告内容：</label>
                <div class="form-controls">
                    <input type="text" name="title" class="input xlarge" value="{{=it.content.dataset[i].title}}">
                    <span class="fi-help-text"></span>
                </div>
            </div>
            <div class="formitems">  
                <label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="1" {{? it.content.dataset[i].sel_link_type == 1}}checked{{?}}/>链接到：</label>  
                <div class="form-controls">
                    <div class="droplist">
						<input type="text" name="selector_title" id="selector_title" value="{{= it.content.dataset[i].detail_value}}" disabled />
                        <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id" id="selector_id" value="{{= it.content.dataset[i].select_value}}" />
						
                        <select style='display:none;'  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
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

                        </select>
						<input style='display:none;' type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button style='display:none;' class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
				<div class="form-controls">
					<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
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
<!--滚动公告栏-->
<!--头部引导页-->
<script type="text/j-template" id="type_con_14">
<div class="con_display" {{? it.content.padding}}style="padding:{{= it.content.padding}}px 0;"{{?}}>
	<div class="page-main">
		<div class="page-top">
			<a href="javascript:;">编辑</a>
			<!-- <a href="javascript:;">预览</a> -->
		</div>
		<div class="page-center">
			<div class="head-img"><img src="images/headportrait.png"></div>
			<div class="text-box">
				<p class="name">欧阳啦啦<span class="label">型男</span></p>
				<p class="weixin">微信：Luo-xia@en</p>
				<div class="group">
					<label>简介：</label>
					<div class="brief">跟着我 左手右手 一个慢动作，右手左手慢动作重播, 你有没... </div>
				</div>
				<!-- <div class="group">
					<label>地址：</label>
					<div class="address">广东省深圳市龙岗区上塘龙塘新...</div>
				</div> -->
			</div>
		</div>
		<div class="control-main">
			<div class="left"><button type="button" class="skin-bg">+ 关注</button></div>
			<div class="right">
				<div class="list"><img src="images/icon-qq.png" width="20"></div>
				<div class="list"><img src="images/icon-weixin.png" width="26"></div>
				<div class="list"><img src="images/icon-tel.png" width="21"></div>
			</div>
		</div>
	</div>
	<!-- <nav class="headguide_nav">
		<div class="navright">
			<div class="checkbg">+关注</div>
			<img src="images/edit_banner.png">
		</div>
		<div class="navportrait">
			<div class="headportrait">
				<div class="navimg">
					<img src="images/headportrait.png" alt="">
				</div>
			</div>
			<div class="navdescribe">
				<span style="color: #ffffff;">瞪圆妹只</span>
				<div class="navtag" style="color: #ffffff;">工作室</div>
				<p class="navintroduce" style="color: #ffffff;">简单介绍下...</p>
			</div>
		</div>
		<div class="bian"></div>
		<div class="contacttypediv">
			<div class="qtypebtu">
				<img src="images/qqw.png" alt=""><span style="color: #ffffff;">QQ</span>
			</div>
			<div class="wtypebtu">
				<img src="images/weixinw.png" alt=""><span style="color: #ffffff;">微信</span>
			</div>
			<div class="ptypebtu">
				<img src="images/dianhuaw.png" alt=""><span style="color: #ffffff;">电话</span>
			</div>
		</div>
	</nav> -->
</div>
</script>
<script type="dot-template" id="type_ctrl_14">
	<span style="font-weight: bolder;">Tips：用户可以在商城首页点击编辑或在个人中心点击头部引导修改头部引导内容</span>
	<div class="formitems" style="margin-top:10px;">
	    <label class="fi-name">模块上下边距：</label>
	    <div class="form-controls">
	        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
	        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
	    </div>
	</div>
	<p style="margin-left:26px;">
		<a href="#" style="color:#1c58d5;font-size:14px;" onclick="showCard()">设置名片规则</a>
		<img onmouseenter="img_tip1()" onmouseleave="img_tip2()" style="width:15px;margin-top:-1px;margin-left:6px;" id="rebate" src="/mshop/admin/Common/images/Base/help.png">
	</p>
</script>
<!--头部引导页-->
<!-- 头像插件 -->
<script  type="dot-template" id="type_con_15">	
	<div id='con_15' style='height:42px;display:none'></div>
	<div class="con_display" style="{{? it.content.padding}}padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px;{{?}}">
		<div class="search-box-1" style='background-color:{{= it.content.bg_color}}'>

			<div class="search-layer-1" style='height:50px;border:none;width: 100%;margin-bottom:2px'>
				<div style='border:1px solid black;width:100%;width: calc(100% - 2px);height:100%;'>
					<div style='box-shadow: 0px 1px 1px #F7F7F7;width:100%;height:100%;background-color:{{= it.content.bg_color}}'>
						<div style='width:80px;height:80px;top:-40px;position:absolute;left:20px;filter:alpha(Opacity=30);-moz-opacity:0.3;opacity: 0.3;background-color: #ECE7E7;{{? it.content.pro_title_show==1 }} border-radius: 50%;{{??}}border-radius: 5px;{{?}}'>
						</div>
						<img src="{{=it.content.dataset[0].pic}}" alt="" style='width:80px;height:80px;top:-40px;position:absolute;left:20px;{{? it.content.pro_title_show==1 }} border-radius: 50%;{{??}}border-radius: 5px;{{?}}'>
						<div style='margin-left:10px;display:inline-block;position: absolute;left: 100px;color:{{= it.content.color}};top:17.5px;font-size:15px;white-space:nowrap;{{? !it.content.placeholder}}color:gray{{?}}' class='search-input'>{{? it.content.placeholder}}{{= it.content.placeholder}}{{??}}请输入名称{{?}}</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</script>
<script type="dot-template" id="type_ctrl_15">
	<div class="formitems">
	        <label class="fi-name">模块上下边距：</label>
	        <div class="form-controls">
	            <div id='slider' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
	            <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
	        </div>
	</div>
	<div class="formitems">
	    <label class="fi-name">图片样式：</label> 
	    <div class="form-controls">
	        <div class="radio-group">
	            <label><input type="radio" name="pro_title_show" value="0"{{? it.content.pro_title_show==0}} checked{{?}}>矩形</label>
	            <label><input type="radio" name="pro_title_show" value="1"{{? it.content.pro_title_show==1}} checked{{?}}>圆形</label>
	        </div>
	    </div>
	</div>
	<div class="formitems" style='position: relative;'>  
		<label class="fi-name">编辑文字：</label>   
		<input type="text" name="placeholder" id='con_15_placeholder' class="input" value="{{= it.content.placeholder }}" placeholder='请输入介绍,字数不能超出12个字' oninput='checktext(this)' >
		<label class="fi-name" style='float:none;display:inline-block;width:auto;position: absolute;'>字体颜色：</label>   
		<div style='display: inline-block;position: absolute;left: 360px;'>
			<div class="colorSelector color" id='text_color' style='display:inline-block'><div attr="{{=it.content.dataset.length}}" style="background-color: {{= it.content.color}}"></div></div>
		</div>
	</div>
	<div class="formitems">
	    <label class="fi-name">背景颜色：</label> 
	        <div class="form-controls"> 
	            <div class="colorSelector bg_color" id='bg_color'><div attr="{{=it.content.dataset.length}}" style="background-color: {{= it.content.bg_color}}"></div></div>
	        </div>
	</div>
	<div class="formitems">
		<label class="fi-name">背景图片：</label> 
		<div class="form-controls">
			<ul class="ctrl-item-list"> 
				<li class="ctrl-item-list-li clearfix">
					<div class="fl" style="margin-right:20px;">
						<div class="imgnav j-selectimg">
							<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img0" enctype="multipart/form-data" method="post" onsubmit="return saveReport(0);">
								<input type="hidden" name="getImg" id='getImg0' value="{{=it.content.dataset[0].pic}}">
								<p class="imgnav-select">
									<input type="file" size="20" name="upfile2" id="upfile2" class="up" >
									<img src="{{=it.content.dataset[0].pic}}">
								</p>
								<input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
								<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
								<input type="hidden" name="img_sort" value="0">
								<input type="hidden" name="uptypes" value="image/jpg,image/jpeg,image/png,">

							</form>
						</div>
						<span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
					</div>
					<div class="formitems">  
					</div>
				</li>
			</ul>
			<label class="fi-name" style='display:block;width: auto;'>建议尺寸：123px*123px ；支持格式：JPG、JPEG、PNG</label>
		</div>
	</div>
	
</script>
<!-- 头像插件 -->
<!-- 线下商城店铺显示 -->
 <script type="text/j-template" id="type_con_16">
<div class="con_display" {{? it.content.padding}}style="padding:{{= it.content.padding}}px 0;"{{?}}>
	<nav class="headguide_nav">
		<img src="../images/user-defined16.jpg" style="width:100%"/>
		
		<div class="bian"></div>
		
	</nav>
</div>
</script>
<script type="dot-template" id="type_ctrl_16">
	<span style="font-weight: bolder;"></span>
<div class="formitems" style="margin-top:10px;">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>      
</div>
<div class="formitems">  
    <label class="fi-name">选择分类：</label>  
    <div class="form-controls">
        <div class="droplist">
            <select  name="type_id_4"  id="type_id_4"  class="input xlarge" style="height:28px;">
            {{? it.cityarea_shop_type_arr}}
            <optgroup label="---------------店铺分类---------------"></optgroup>
            <option value="1_16"{{? '1_16'==it.content.dataset[0].select_value}} selected="selected"{{?}} > ---------------全部--------------- </option>
				
				{{for (k=0,m=it.cityarea_shop_type_arr.length;k<m;k++) {
					type_id_name=it.cityarea_shop_type_arr[k].split("_");
					type_id=type_id_name[0];
					type_name=type_id_name[1];
				}}
			<option value="{{=type_id}}_1"{{? type_id+'_1'==it.content.dataset[0].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
				{{ } }}
            {{?}}
            </select>
        </div>
        <input type="hidden" class="j-verify" name="item_id" value="">
        <span class="fi-help-text j-verify-linkType"></span>
    </div>
</div>
<div class="formitems">
    <label class="fi-name">展示店铺数量：</label> 
    <div class="form-controls">
        <input type="number"  name="pro_numshow" class="input xlarge" value="{{= it.content.pro_numshow}}"> <span class="fi-help-text"></span> 
    </div> 
</div>
<div class="formitems">
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="sort_type" value="0"{{? it.content.sort_type==0}} checked{{?}}>按用户距离商家距离从近到远排序</label>
            <label><input type="radio" name="sort_type" value="1"{{? it.content.sort_type==1}} checked{{?}}>按商家销量从多到少排序</label>
        </div>
    </div>
</div>
</script>
<!-- 线下商城店铺显示 -->

<!--活动橱窗-->
<script  type="dot-template" id="type_con_17">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
<div class="floor clearfix" style="padding:0 5px;">
{{? it.content.css_type==1}}
	{{? it.content.show_backwards==1||it.content.show_carry==1}}
	<div class="time-round time-round-ab" style="color:{{= it.content.text_color}};background-color:rgba({{=it.content.bg_color}},0.6)">距开始：20<span></span>天<span></span>20<span></span>小时<span></span>20<span></span>分<span></span>20<span></span>秒</div>
    {{?}}
    <div class="pro-box pro-box-sec">
    	<div class="img-box">
    		<img src="images/img-product.jpg">
    		{{? it.content.dataset.length>=1&&it.content.dataset[0].round_pic!=""}}<img class="round_pic" src="{{=it.content.dataset[0].round_pic}}">{{?}}
    	</div>
        {{? it.content.pro_title_show==1}}<p {{? it.content.pro_title_twoline==0}}class="goods-title"{{?? it.content.pro_title_twoline==1}}class="goods-title-2"{{?}}>产品名称</p>{{?}}
        <div class="text-round-box">
        {{? it.content.dataset.length>=1}}<span class="text-round" style="color:{{=it.content.dataset[0].round_color}}">{{=it.content.dataset[0].round}}</span>{{?}}
	    {{? it.content.dataset.length<1}}<span class="text-round">{{?}}
	        	
        </div>
        {{? it.content.show_activity==1}}<span class="goods-price">¥149.00</span>{{?}}
        {{? it.content.show_cost==1}}<span class="old-price">¥249.00</span>{{?}}
        {{? it.content.show_sale==1}}<span class="sale">已售 500</span>{{?}}
    </div>
    <div class="pro-box pro-box-sec">
    	<div class="img-box">
    		<img src="images/img-product.jpg">
    		{{? it.content.dataset.length>=2&&it.content.dataset[1].round_pic!=""}}<img class="round_pic" src="{{=it.content.dataset[1].round_pic}}">{{?}}
    	</div>
        {{? it.content.pro_title_show==1}}<p {{? it.content.pro_title_twoline==0}}class="goods-title"{{?? it.content.pro_title_twoline==1}}class="goods-title-2"{{?}}>产品名称</p>{{?}}
        <div class="text-round-box">
        {{? it.content.dataset.length>=2}}<span class="text-round" style="color:{{=it.content.dataset[1].round_color}}">{{=it.content.dataset[1].round}}</span>
        {{?}}
	        {{? it.content.dataset.length<2}}<span class="text-round">{{?}}
	        	
        </div>
        {{? it.content.show_activity==1}}<span class="goods-price">¥149.00</span>{{?}}
        {{? it.content.show_cost==1}}<span class="old-price">¥249.00</span>{{?}}
        {{? it.content.show_sale==1}}<span class="sale">已售 500</span>{{?}}
    </div>
    
{{?}}
{{? it.content.css_type==2}}
    <div class="pro-box-solo solo-box">
    	<div class="img-box">
    		{{? it.content.show_backwards==1|| it.content.show_carry==1}}
    		<div class="time-round time-round-ab" style="color:{{= it.content.text_color}};background-color:rgba({{=it.content.bg_color}},0.6)">距开始：20<span></span>天<span></span>20<span></span>小时<span></span>20<span></span>分<span></span>20<span></span>秒</div>
    		{{?}}
    		<img src="images/img-product.jpg">
    		{{? it.content.dataset.length>=1&&it.content.dataset[0].round_pic!=""}}<img class="round_pic" src="{{=it.content.dataset[0].round_pic}}">{{?}}
    	</div>
        {{? it.content.pro_title_show==1}}<p {{? it.content.pro_title_twoline==0}}class="goods-title"{{?? it.content.pro_title_twoline==1}}class="goods-title-2"{{?}} style="padding:8px 10px 0;">产品名称</p>{{?}}
       <div class="flex">
	        <p class="price-box flex-auto" style="margin-top:0">
	        	{{? it.content.dataset.length>=1}}<span class="text-round" style="color:{{=it.content.dataset[0].round_color}}">{{=it.content.dataset[0].round}}</span>
	        	{{?}}
	        		{{? it.content.dataset.length<1}}<span class="text-round">{{?}}
	        	
	            {{? it.content.show_activity==1}}<span class="goods-price-solo">¥149.00</span>{{?}}
	            {{? it.content.show_cost==1}}<span class="old-price-solo">¥249.00</span>{{?}}
	        </p>
	        {{? it.content.show_sale==1}}<span class="sale-solo flex-none" style="margin:0;line-height:24px">已售 500</span>{{?}}
        </div>
    </div>
    <div class="pro-box-solo solo-box">
    	<div class="img-box">
    		{{? it.content.show_backwards==1|| it.content.show_carry==1}}
    		 <div class="time-round time-round-ab" style="color:{{= it.content.text_color}};background-color:rgba({{=it.content.bg_color}},0.6)" >距开始：20<span></span>天<span></span>20<span></span>小时<span></span>20<span></span>分<span></span>20<span></span>秒</div>
    		{{?}}
    		<img src="images/img-product.jpg">
    		{{? it.content.dataset.length>=2&&it.content.dataset[1].round_pic!=""}}<img class="round_pic" src="{{=it.content.dataset[1].round_pic}}">{{?}}
    	</div>
        {{? it.content.pro_title_show==1}}<p {{? it.content.pro_title_twoline==0}}class="goods-title"{{?? it.content.pro_title_twoline==1}}class="goods-title-2"{{?}} style="padding:8px 10px 0;">产品名称</p>{{?}}
        <div class="flex">
	        <p class="price-box flex-auto" style="margin-top:0">
	        	{{? it.content.dataset.length>=2}}<span class="text-round" style="color:{{=it.content.dataset[1].round_color}}">{{=it.content.dataset[1].round}}</span>
	        	{{?}}
	        	{{? it.content.dataset.length<2}}<span class="text-round"></span>{{?}}
	        	
	            {{? it.content.show_activity==1}}<span class="goods-price-solo">¥149.00</span>{{?}}
	            {{? it.content.show_cost==1}}<span class="old-price-solo">¥249.00</span>{{?}}
	        </p>
	        {{? it.content.show_sale==1}}<span class="sale-solo flex-none" style="margin:0;line-height:24px">已售 500</span>{{?}}
        </div>
        
    </div>
{{?}}
</div>
</div>
</script>
<script type="dot-template" id="type_ctrl_17">
<ul class="ctrl-nav">
	<li class="{{? it.content.nav==0}}active{{?}}">默认模板</li>
	{{? it.content.nav >= 1}}
	<li class="{{? it.content.nav==1}}active{{?}}">格式设置</li>
	<li class="{{? it.content.nav==2}}active{{?}}">商品设置</li>
	{{?}}
</ul>
<div class="ctrl-tab-box">
	<div class="ctrl-tab" {{? it.content.nav==0}}style="display:block;"{{?}}>
		<div class="formitems" style="text-align:right;margin-bottom:10px;">
			<div class="modal-btn">自定义模版</div>
		</div>
		<div class="formitems">
		    <label class="fi-name">距离开始时间：</label>
		    <div class="form-controls">
		        <div class="time-round" style="color:{{= it.content.text_color}};background-color:rgba({{=it.content.bg_color}},0.6)">20<span></span>天<span></span>20<span></span>小时<span></span>20<span></span>分<span></span>20<span></span>秒</div>
		    </div>
		</div>
		<div class="formitems">
		    <label class="fi-name"><span></span>距离结束时间：</label>
		    <div class="form-controls">
		        <div class="time-round" style="color:{{= it.content.text_color}};background-color:rgba({{=it.content.bg_color}},0.6)">20<span></span>天<span></span>20<span></span>小时<span></span>20<span></span>分<span></span>20<span></span>秒</div>
		    </div>
		</div>
		<div class="formitems">
		    <label class="fi-name">活动时间：</label>
		    <div class="form-controls">
		        <div class="time-round" style="color:{{= it.content.text_color}};background-color:rgba({{=it.content.bg_color}},0.6)">2018-02-06<span></span>-<span></span>2018-03-06</div>
		    </div>
		</div>
		<div class="formitems clearfix">
			<div class="modal-box">
				<img class="modal-img" src="images/hd_modal_1.jpg"/>
				<div class="modal-text">
					<p>双列模版-产品图</p>
					<div class="modal-btn" data-modal="1">编辑模版</div>
				</div>
			</div>
			<div class="modal-box">
				<img class="modal-img" src="images/hd_modal_2.jpg"/>
				<div class="modal-text">
					<p>单列模版-封面图</p>
					<div class="modal-btn" data-modal="2">编辑模版</div>
				</div>
			</div>
			
		</div>
	</div>
	<div class="ctrl-tab" {{? it.content.nav==1}}style="display:block;"{{?}}>
		<div class="formitems">
		    <label class="fi-name">显示布局方式：</label>
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="css_type" value="1" {{? it.content.css_type==1}} checked{{?}}>双列</label>
		            <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>单列</label>
		        </div>
		    </div>
		</div>
		<div class="formitems">
		    <label class="fi-name">是否显示标题：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="pro_title_show" value="1" {{? it.content.pro_title_show==1}} checked{{?}}>显示</label>
		            <label><input type="radio" name="pro_title_show" value="0"{{? it.content.pro_title_show==0}} checked{{?}}>隐藏</label>
		            </label>
		        </div>
		    </div>
		</div>
		{{? it.content.pro_title_show==1}}
		<div class="formitems">
		    <label class="fi-name">显示标题行数：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="pro_title_twoline" value="1"{{? it.content.pro_title_twoline==1}} checked{{?}}>两行</label>
		            <label><input type="radio" name="pro_title_twoline" value="0"{{? it.content.pro_title_twoline==0}} checked{{?}}>一行</label>
		        </div>
		    </div>
		</div>
		{{?}}
		<div class="formitems">
		    <label class="fi-name">商品图片样式：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="pic_type" value="1"{{? it.content.pic_type==1}} checked{{?}}>产品图</label>
		            <label><input type="radio" name="pic_type" value="0"{{? it.content.pic_type==0}} checked{{?}}>封面图</label>
		        </div>
		    </div>
		</div>
		<div class="formitems">
		    <label class="fi-name">是否显示销量：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="show_sale" value="1"{{? it.content.show_sale==1}} checked{{?}}>显示</label>
		            <label><input type="radio" name="show_sale" value="0"{{? it.content.show_sale==0}} checked{{?}}>隐藏</label>
		        </div>
		    </div>
		</div>
		<div class="formitems">
		    <label class="fi-name">是否显示原现价：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="show_cost" value="1"{{? it.content.show_cost==1}} checked{{?}}>显示</label>
		            <label><input type="radio" name="show_cost" value="0"{{? it.content.show_cost==0}} checked{{?}}>隐藏</label>
		        </div>
		    </div>
		</div>
		<div class="formitems">
		    <label class="fi-name">是否显示活动价：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="show_activity" value="1"{{? it.content.show_activity==1}} checked{{?}}>显示</label>
		            <label><input type="radio" name="show_activity" value="0"{{? it.content.show_activity==0}} checked{{?}}>隐藏</label>
		        </div>
		    </div>
		</div>
		<div class="formitems">
		    <label class="fi-name">商品显示数量：</label> 
		    <div class="form-controls">
		        <input type="text" name="production_num" value="{{= it.content.production_num}}" style="width:100px"/>
		    </div>
		</div>
		<div class="formitems">
		    <label class="fi-name">显示开始时间倒数：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="show_backwards" value="1"{{? it.content.show_backwards==1}} checked{{?}}>显示</label>
		            <label><input type="radio" name="show_backwards" value="0"{{? it.content.show_backwards==0}} checked{{?}}>隐藏</label>
		        </div>
		    </div>
		</div> 
		{{? it.content.show_carry==1}}
		<div class="formitems">
		    <label class="fi-name">提前：</label> 
		    <div class="form-controls">
		        <input type="text" name="backwards_day" value="{{= it.content.backwards_day}}" style="width:100px"/>天，开始时间倒数
		    </div>
		</div>
		{{?}}

		<div class="formitems">
		    <label class="fi-name">显示活动进行时间：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="show_carry" value="1"{{? it.content.show_carry==1}} checked{{?}}>显示</label>
		            <label><input type="radio" name="show_carry" value="0"{{? it.content.show_carry==0}} checked{{?}}>隐藏</label>
		        </div>
		    </div>
		</div>  

		{{? it.content.show_carry==1||it.content.show_backwards==1 }}
		<div class="formitems">
		    <label class="fi-name">活动时间样式：</label> 
		    <div class="form-controls">
		        <div class="radio-group padding-top-0">
		            <label><input type="radio" name="show_carry_type" value="1"{{? it.content.show_carry_type==1}} checked{{?}}>范围</label>
		            <label><input type="radio" name="show_carry_type" value="2"{{? it.content.show_carry_type==2}} checked{{?}}>倒数</label>
		        </div>
		    </div>
		</div>  
		{{?}}   

		<div class="formitems">
		    <label class="fi-name">时间文字颜色：</label> 
		        <div class="form-controls">
		            <div class="colorSelector text_color"><div  style="background-color: {{= it.content.text_color}}"></div></div>
		        </div>
		</div>   
		<div class="formitems">
		    <label class="fi-name">时间底图颜色：</label> 
		        <div class="form-controls">
		            <div class="colorSelector bg_color"><div style="background-color:rgb({{= it.content.bg_color}})"></div></div>
		        </div>
		</div>  
		
		
		<div class="formitems">
		    <label class="fi-name">模块上下边距：</label>
		    <div class="form-controls">
		        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
		        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
		    </div>
		</div>

		<div class="formitems modal-text">
			<div class="modal-btn prev-step">上一步</div>
			<div class="modal-btn next-step">下一步</div>
		</div>

	</div>

	<div class="ctrl-tab" {{? it.content.nav==2}}style="display:block;"{{?}}>
		<div class="formitems">
			<label class="fi-name" style="width:auto">活动选择：</label>
			<p class="act-adress">{{=it.content.last_title }}</p>
			<button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this,1)" >请选择</button>
			<input type=hidden name="activity_id" id="selector_id" value="{{=it.content.activity_id }}" />
			<input type=hidden name="activity_title" id="selector_title" value="{{=it.content.activity_title }}" />
			<input type=hidden id="production_num" value="{{= it.content.production_num}}" />
			<input type=hidden id="change_num" value="{{= it.content.change_num}}" />
			<select style="display:none;"  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
			</select>
		</div>
		<div class="formitems">
			<ul class="ctrl-item-list">
				{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
				    <li class="ctrl-item-list-li clearfix">
				    	<div class="fl">
				            <div class="imgnav j-selectimg">
				                <p class="imgnav-select">
				                    <img src="{{=it.content.dataset[i].pic}}">
				                </p>

				            </div>
				            <span class="fi-help-text txtCenter mgt5 j-verify-pic" style="color:#ff0000">{{? it.content.dataset[i].money}}￥{{?}}{{=it.content.dataset[i].money}}</span>
				        </div>
				        <div class="fl imgnav-info">
				        	<div class="formitems">
							    <label class="fi-name">产品名称：</label>
							    <div class="form-controls">
							     	<p>{{=it.content.dataset[i].title}}</p>
							    </div>
							</div>
							<div class="formitems">
							    <label class="fi-name">产品编号：</label>
							    <div class="form-controls">
							     	<p>{{=it.content.dataset[i].num}}</p>
							    </div>
							</div>
							<div class="formitems">
							    <label class="fi-name">标签设置：</label>
							    <div class="form-controls">
							     	<input type="text" name="round" style="width:130px;padding:0 5px;font-size:12px;line-height:20px;" value="{{=it.content.dataset[i].round}}" placeholder="限2个中文字，默认为空" maxlength="2" />
							     	<div class="colorSelector li_round" style="display:inline-block;vertical-align: middle;margin-left: 20px;"><div  style="background-color: {{=it.content.dataset[i].round_color}}"></div></div>
							    </div>
							</div>
							

							<div class="formitems">
							    <label class="fi-name">图片标签：</label> 
							    <div class="form-controls clearfix">
							        <div class="fl">
							            <div class="imgnav j-selectimg">
							            <form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{=i}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{=i}});">
							                <input type="hidden" name="getImg" id='getImg{{=i}}' value="{{=it.content.dataset[i].round_pic}}">
							                <p class="imgnav-select">
							                    <input type="file" size="20" name="upfile2" id="upfile2" class="up" >
							                    <img src="{{? it.content.dataset[i].round_pic==""}}images/add_img.jpg {{??}} {{=it.content.dataset[i].round_pic}}{{?}}">
							                </p>
							                <input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
							                <input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
							                <input type="hidden" name="img_sort" value="{{=i}}">
							                
							            </form>
							            </div>
							            <p style="color:#ff0000">图片建议尺寸：30px*30px</p>
							        </div>
							    </div>
							</div>


				    </li>
				{{ } }}
			</ul>
		</div>
		<div class="formitems modal-text">
			<div class="modal-btn prev-step">上一步</div>
		</div>
	</div>
</div>

</script>
<!--活动橱窗-->

<!-- 天气插件显示  -->
 <script type="dot-template" id="type_con_18">
<div class="con_display" {{? it.content.padding}}style="padding:{{= it.content.padding}}px 0;"{{?}}>
    <div class="headguide_nav" style="position: relative;">
        <img src="../images/tq1.png" style="width:100%"/>
        {{? it.content.all_switch==1}}
        <img src="../images/tq2.png" style="width: 34%;position: absolute;top: 0;right: 0;"/>
        {{?}}
        
        <div class="bian"></div>
        
    </div>
</div>
</script>
<script type="dot-template" id="type_ctrl_18">
    <span style="font-weight: bolder;"></span>
<div class="formitems" style="margin-top:10px;">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>      
</div>

<div class="formitems">
    <label class="fi-name">帖子数据：</label> 
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="all_switch" value="1"{{? it.content.all_switch==1}} checked{{?}}>开启（仅社区功能有效）</label>
            <label><input type="radio" name="all_switch" value="0"{{? it.content.all_switch==0}} checked{{?}}>关闭</label>
        </div>
    </div> 
</div>
</script>
<!-- 天气插件end -->

<!-- 社区帖子  -->
 <script type="dot-template" id="type_con_19">
<div class="con_display" {{? it.content.padding}}style="padding:{{= it.content.padding}}px 0;"{{?}}>
    <div style="position: relative;">
        <img src="../images/shequ1.png" style="width: 100%;"/>
        <img src="../images/shequ2.png" style="width: 100%;"/>
        <img src="../images/shequ3.png" style="width: 100%;"/>
        <img src="../images/shequ4.png" style="width: 100%;"/>
        {{? it.content.all_switch==1}} 
        <img src="../images/fabu.png" style="width: 13%;position: absolute;top:100px;right: 0;"/>
        {{?}}
        <div class="bian"></div>
        
    </div>
</div>
</script>
<script type="dot-template" id="type_ctrl_19">
    <span style="font-weight: bolder;"></span>
<div class="formitems" style="margin-top:10px;">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>      
</div>

<div class="formitems">
    <label class="fi-name">悬浮发帖功能：</label> 
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="all_switch" value="1"{{? it.content.all_switch==1}} checked{{?}}>开启</label>
            <label><input type="radio" name="all_switch" value="0"{{? it.content.all_switch==0}} checked{{?}}>关闭</label>
        </div>
    </div> 
</div>
</script>
<!-- 社区帖子end -->

<!-- 云店店头  -->
 <script type="dot-template" id="type_con_20">
	<div class="con_display" style="{{? it.content.padding}}padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px;{{?}}">
		<div class="cloud-head " style="background-image:url('/mshop/admin/Base/personalization/custom/images/cloud_head_bg.jpg');">
			<div class="cloud-hear-infor {{? it.content.css_type==1}}type1{{?}}{{?it.content.css_type==2}}type2{{?}}">
				<div class="head-img"><img src="/mshop/admin/Common/images/Base/personal_center/gift.png" alt=""></div>
				<div class="head-infor">
					<p class="tt">精品旗舰店</p>
					<div class="mark">
						{{? it.content.yun_consult_show==1}}<span>
							<img src="/mshop/admin/Base/personalization/custom/images/cloud_mes.png" />
							咨询
						</span>{{?}}
						{{? it.content.yun_phone_show==1}}
						<span>
							<img src="/mshop/admin/Base/personalization/custom/images/cloud_mes.png" />
							电话
						</span>
						{{?}}
					</div>
				</div>
			</div>
		</div>		
	</div>
</script>
<script type="dot-template" id="type_ctrl_20">
    <span style="font-weight: bolder;"></span>
<div class="formitems">  
	<label class="fi-name">显示样式：</label>   
    <div class="form-controls">
		<div class="radio-group">
			<label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>样式一</label>
            <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>样式二</label>
        </div>
	</div>
</div>
<div class="formitems" style="margin-top:10px;">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>      
</div>

<div class="formitems">
    <label class="fi-name">是否开启咨询：</label> 
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="yun_consult_show" value="1"{{? it.content.yun_consult_show==1}} checked{{?}}>开启</label>
            <label><input type="radio" name="yun_consult_show" value="0"{{? it.content.yun_consult_show==0}} checked{{?}}>关闭</label>
        </div>
    </div> 
</div>
<div class="formitems">
    <label class="fi-name">是否开启电话：</label> 
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="yun_phone_show" value="1"{{? it.content.yun_phone_show==1}} checked{{?}}>开启</label>
            <label><input type="radio" name="yun_phone_show" value="0"{{? it.content.yun_phone_show==0}} checked{{?}}>关闭</label>
            <label><input type="text" name="yun_phone" style="width:150px;padding:0 5px;font-size:12px;line-height:20px;" value="{{=it.content.yun_phone}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" onafterpaste="this.value=this.value.replace(/[^\d]/g,'')" placeholder="输入电话号码" maxlength="11" /></label>
        </div>
    </div> 
</div>
</script>
<!-- 云店店头end -->

<!-- 云店店主产品  -->
 <script type="dot-template" id="type_con_21">
<div class="con_display" {{? it.content.padding}} style="padding:{{= it.content.padding}}px 0;"{{?}}>
	<div class="cloud-product" >
		<div class="product-tt" style="background-image:url({{=it.content.dataset[0].pic}})">
			<p class="text" style="background-image:url(/weixinpl/common_shop/common/custom_temp/images/pro_tt_bg.png)">{{=it.content.dataset[0].title}}</p>
			<p class="more">查看更多&nbsp;&nbsp;></p>
		</div>
		<div class="product-scroll">
			<ul class="cloud-product-list {{? it.content.css_type==1}}type1{{?}}{{? it.content.css_type==2}}type2{{?}}{{? it.content.css_type==3}}type3{{?}}">
				<li>
				    <div class="img">
				    	<img src="images/img1.jpg" />
				    </div>
				    <div class="product-infor">
				        <p class="tt">居家精选 物美价廉</p>
				        <p class="product-money">￥188</p>
				        {{? it.content.css_type==3}}<p class="product-date"><span>已销22546</span><span>库存22546</span></p>{{?}}
				    </div>
				</li>
				<li>
				    <div class="img">
				    	<img src="images/img1.jpg" />
				    </div>
				    <div class="product-infor">
				        <p class="tt">居家精选 物美价廉</p>
				        <p class="product-money">￥188</p>
				        {{? it.content.css_type==3}}<p class="product-date"><span>已销22546</span><span>库存22546</span></p>{{?}}
				    </div>
				</li>
				<li>
				    <div class="img">
				    	<img src="images/img1.jpg" />
				    </div>
				    <div class="product-infor">
				        <p class="tt">居家精选 物美价廉</p>
				        <p class="product-money">￥188</p>
				        {{? it.content.css_type==3}}<p class="product-date"><span>已销22546</span><span>库存22546</span></p>{{?}}
				    </div>
				</li>
				<li>
				    <div class="img">
				    	<img src="images/img1.jpg" />
				    </div>
				    <div class="product-infor">
				        <p class="tt">居家精选 物美价廉</p>
				        <p class="product-money">￥188</p>
				        {{? it.content.css_type==3}}<p class="product-date"><span>已销22546</span><span>库存22546</span></p>{{?}}
				    </div>
				</li>
			<ul>
		</div>
		<div class="product-list-more">更多推荐&nbsp;&nbsp;></div> 
	</div>
</div>
</script>
<script type="dot-template" id="type_ctrl_21">
    <span style="font-weight: bolder;"></span>
<div class="formitems">  
	<label class="fi-name">显示样式：</label>   
    <div class="form-controls">
		<div class="radio-group">
			<label><input type="radio" name="css_type" value="1"{{? it.content.css_type==1}} checked{{?}}>样式一</label>
            <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>样式二</label>
            <label><input type="radio" name="css_type" value="3"{{? it.content.css_type==3}} checked{{?}}>样式三</label>
        </div>
	</div>
</div>
<div class="formitems" style="margin-top:10px;">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>      
</div>
<div class="formitems">
    <label class="fi-name">“店主精选”自定义名称：</label> 
    <div class="form-controls">
        <input type="text" name="title"   class="input xlarge" maxlength="8" value="{{=it.content.dataset[0].title}}" style="width:100px"/>
    </div>
</div>

<div class="formitems">
    <label class="fi-name">上传背景图：</label> 
    <div class="form-controls clearfix">
        <div class="fl">
            <div class="imgnav j-selectimg">
            <form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>" id="frm_img0" enctype="multipart/form-data" method="post" onsubmit="return saveReport(0);">
                <input type="hidden" name="getImg" id='getImg0' value="{{=it.content.dataset[0].pic}}">
                <p class="imgnav-select">
                    <input type="file" size="20" name="upfile2" id="upfile2" class="up" >
                    <img src="{{=it.content.dataset[0].pic}}">
                </p>
                <input type="hidden" name="diy_tem_contid" value="{{=it.id}}">
                <input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
                <input type="hidden" name="img_sort" value="0">
                
            </form>
            </div>
            <p style="color:#ff0000">建议尺寸：2M以内</p>
        </div>
    </div>
</div>

</script>
<!-- 云店店主产品end -->

<!-- O2O店铺列表 -->
<script type="dot-template" id="type_con_22">
<div class="con_display" {{? it.content.padding}}style="padding:{{= it.content.padding}}px 0;"{{?}}>
	<nav class="headguide_nav">
        {{
            value_level_id=it.content.dataset[0].select_value.split("_");
            value_level=value_level_id[0];
            value_id=value_level_id[1];
        }}
        {{? it.content.dataset[0].select_value == '0_22' || it.content.dataset[0].select_value > 0 || it.content.dataset[0].select_value == 0}}
        {{? it.content.o2o_grade==1 && it.content.o2o_price==1}}
		<img src="../images/user-defined22.jpg" style="width:100%"/>
        {{?}}
        {{? it.content.o2o_grade==0 && it.content.o2o_price==1}}
		<img src="../images/user-defined-g22.jpg" style="width:100%"/>
        {{?}}
        {{? it.content.o2o_grade==1 && it.content.o2o_price==0}}
		<img src="../images/user-defined-p22.jpg" style="width:100%"/>
        {{?}}
        {{? it.content.o2o_grade==0 && it.content.o2o_price==0}}
		<img src="../images/user-defined-g-p22.jpg" style="width:100%"/>
        {{?}}
        {{?}}
        {{? it.content.dataset[0].select_value != '0_22' && value_id > 0}}
        {{? it.content.o2o_grade==1 && it.content.o2o_price==1}}
        <img src="../images/user-set22.jpg" style="width:100%"/>
        {{?}}
        {{? it.content.o2o_grade==0 && it.content.o2o_price==1}}
        <img src="../images/user-nograde22.jpg" style="width:100%"/>
        {{?}}
        {{? it.content.o2o_grade==1 && it.content.o2o_price==0}}
        <img src="../images/user-noprice22.jpg" style="width:100%"/>
        {{?}}
        {{? it.content.o2o_grade==0 && it.content.o2o_price==0}}
        <img src="../images/user-noprice-nograde22.jpg" style="width:100%"/>
        {{?}}
        {{?}}

		<div class="bian"></div>
		
	</nav>
</div>
</script>
<script type="dot-template" id="type_ctrl_22">
<span style="font-weight: bolder;"></span>
<div class="formitems">  
    <label class="fi-name">一级行业分类：</label>  
    <div class="form-controls">
        <div class="droplist">
            <select  name="type_id_6"  id="type_id_6"  class="input xlarge" style="height:28px;" onchange="bindTypeId6Change(this);">
                {{? it.o2o_list_arr}}
                <optgroup label="---------------一级行业分类---------------"></optgroup>
                <option value="0_22"{{? '0_22'==it.content.dataset[0].select_value}} selected="selected"{{?}} > ---------------全部--------------- </option>

				{{for (k=0,m=it.o2o_list_arr.length;k<m;k++) {
					type_id_name=it.o2o_list_arr[k].split("_");
					type_id=type_id_name[0];
					type_name=type_id_name[1];
                    value_level_id=it.content.dataset[0].select_value.split("_");
                    value_level=value_level_id[0];
                    value_id=value_level_id[1];
				}}
			<option value="{{=type_id}}"{{? type_id == value_level}} selected="selected"{{?}} >{{=type_name}}</option>
				{{ } }}
                {{?}}
            </select>
        </div>
        <input type="hidden" class="j-verify" name="item_id" value="">
        <span class="fi-help-text j-verify-linkType"></span>
    </div>
</div>
<div class="formitems">
    <label class="fi-name">二级行业分类：</label>
    <div class="form-controls">
        <div class="droplist">
            <select name="type_id_7"  id="type_id_7" class="input xlarge" style="height:28px;">
                
                <optgroup label="---------------二级行业分类---------------"></optgroup>
                <option value="0"{{? '0' == it.content.dataset[0].select_value}} selected="selected"{{?}} > ---------------全部--------------- </option>
                {{? it.o2o_lv_list_arr}}
                {{for (k=0,m=it.o2o_lv_list_arr.length;k<m;k++) {
					type_id_lv_name=it.o2o_lv_list_arr[k].split("_");
                    type_level=type_id_lv_name[0];
					type_id=type_id_lv_name[1];
					type_name=type_id_lv_name[2];
                    value_level_id=it.content.dataset[0].select_value.split("_");
                    value_level=value_level_id[0];
                    value_id=value_level_id[1];
				}}
                {{? value_level == type_level}}
			     <option value="{{=value_level}}_{{=type_id}}"{{? type_level+'_'+type_id == it.content.dataset[0].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
                {{?}}
				{{ } }}
                {{?}}
            </select>
        </div>
        <input type="hidden" class="j-verify" name="item_id" value="">
        <span class="fi-help-text j-verify-linkType"></span>
    </div>
</div>
<div class="formitems">
    <label class="fi-name">显示列表数量：</label> 
    <div class="form-controls">
        <input type="number" name="pro_numshow" class="input xlarge" value="{{= it.content.pro_numshow}}"> <span class="fi-help-text"></span> 
    </div> 
</div>
<div class="formitems">
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="sort_type" value="0" {{? it.content.sort_type==0}}checked{{?}}>按用户距离商家距离从近到远排序</label>
            <label><input type="radio" name="sort_type" value="1" {{? it.content.sort_type==1}}checked{{?}}>按商家销量从多到少排序</label>
        </div>
    </div>
</div>
<div class="formitems">
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="checkbox" name="o2o_grade" {{? it.content.o2o_grade==1}}checked{{?}} />显示评分</label>
            <label><input type="checkbox" name="o2o_price" {{? it.content.o2o_price==1}}checked{{?}} />显示价格</label>
        </div>
    </div>
</div>
</script>
<!-- O2O店铺列表 end-->

<script type="text/javascript">
	var bottomArr=new Array();//底部模板数据
</script>

<script type="text/javascript" src="js/doT.min.js"></script>
<script type="text/javascript" src="js/colorpicker.js"></script>
<script type="text/javascript" src="js/custom.init.js"></script>
<script type="text/javascript" src="js/custom.core.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/layer/layer.js"></script>
<script type="text/javascript" src="js/custom.events.js"></script>
<script type="text/javascript" src="js/jquery.touchSlider.js"></script>
<script type="text/javascript" src="js/slider.js"></script>
<script type="text/javascript" src="js/custom.display.js"></script>
<script type="text/javascript" src="js/Marquee.js"></script>
<!-- <script charset="utf-8" src="js/region_select.js"></script> -->
<script charset="utf-8" src="../../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/global.js"></script>
<script type="text/javascript" src="../../../Common/js/Base/personalization/shop.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/lean-modal.min.js"></script>
<script type="text/javascript" src="../../../Common/js/Product/product/jquery.uploadify-3.1.min.js?ver=<?php echo rand(0,9999);?>"></script>
<script type="text/javascript" src="js/jquery.form.js"></script><!--ajaxform 插件-->
<script type="text/javascript" src="js/WdatePicker.js"></script><!--添加时间插件-->
<!--<script type="text/javascript" src="js/region_select.js"></script>--><!--选择地区插件-->
<script type="text/javascript" src="js/select_area.js"></script>
<!--选择地区插件-->
<!--<script src="//malsup.github.io/jquery.form.js"></script>-->
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript">
	function img_tip1(){
		layer.tips('提示：名片规则是整个微商城通用。','#rebate',{
		area: '215px',
		time: 0
	});
	};
	function img_tip2(){
		layer.tips('提示：名片规则是整个微商城通用。','#rebate',{
		area: '215px',
		time: 1
	});
	};
</script>



<script>
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
    o2o_list_arr = <?php echo json_encode($o2o_list_arr);?>;//o2o一级行业列表
    o2o_lv_list_arr = <?php echo json_encode($o2o_lv_list_arr);?>;//o2o二级行业列表
	
	var kefu_phone    ="<?php echo $kefu_phone;?>";//客服电话
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
				cityarea_shop_arr:null,//线下商城店铺
                o2o_list_arr:null,//o2o一级行业列表
                o2o_lv_list_arr:null//020二级行业列表
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
				divide_type:null,//划分类型
				threed_link:null,
				pic_type:null,
				show_cost:null,
				show_activity:null,
				show_backwards:null,
				backwards_day:null,
				show_carry:null,
				show_carry_type:null,
				text_color:null,
				bg_color:null,
				round:null,
				round_color:null,
				round_pic:null,
				production_num:null,
				activity_id:null,
				activity_title:null,
				show_num:null,
				fix_top:null,
				all_switch:null,
				pro_pic_show:null,
				bottom_id:null,
				yun_phone:null,
				yun_phone_show:null,
				yun_consult_show:null,
                o2o_grade:null,//o2o显示评分
                o2o_price:null//o2o显示价钱
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
			if(test[i].round){
				round_arr = test[i].round.split("|");
			}
			if(test[i].round_color){
				round_color_arr = test[i].round_color.split("|");
			}
			if(test[i].round_pic){
				round_pic_arr = test[i].round_pic.split("|");
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
		 module.content.search_color=test[i].search_color;
		 module.content.color=test[i].color;
		 module.content.rolling_direction=test[i].rolling_direction;
		 module.content.rolling_speed=test[i].rolling_speed;
		 module.content.show_time_limit=test[i].show_time_limit;
		 module.content.city_name=test[i].city_name;
		 module.content.location_p=test[i].province;
		 module.content.shop_type=test[i].shop_type;
		 module.content.sort_type=test[i].sort_type;
		 module.content.divide_type=test[i].divide_type;
		 module.content.threed_link=test[i].threed_link;
		 module.content.nav=test[i].nav;
		 module.content.pic_type=test[i].pic_type;
		 module.content.show_cost=test[i].show_cost;
		 module.content.show_activity=test[i].show_activity;
		 module.content.show_backwards=test[i].show_backwards;
		 module.content.backwards_day=test[i].backwards_day;
		 module.content.show_carry=test[i].show_carry;
		 module.content.show_carry_type=test[i].show_carry_type;
		 module.content.text_color=test[i].text_color;
		 module.content.bg_color=test[i].bg_color;
		 module.content.round=test[i].round;
		 module.content.round_color=test[i].round_color;
		 module.content.round_pic=test[i].round_pic;
		 module.content.production_num=test[i].production_num;
		 module.content.activity_id=test[i].activity_id;
		 module.content.activity_title=test[i].activity_title;
		 module.content.show_num=test[i].show_num;
		 module.content.all_switch=test[i].all_switch;
		 module.content.fix_top=test[i].fix_top;
		 module.content.pro_pic_show=test[i].pro_pic_show;
		 module.content.change_num=0;					//活动橱窗特殊字段
		 module.content.last_title="";					//活动橱窗特殊字段
		 module.content.yun_phone=test[i].yun_phone;
		 module.content.yun_phone_show=test[i].yun_phone_show;
		 module.content.yun_consult_show=test[i].yun_consult_show;
		 module.content.o2o_grade=test[i].o2o_grade;
		 module.content.o2o_price=test[i].o2o_price;
         module.o2o_list_arr=o2o_list_arr;
         module.o2o_lv_list_arr=o2o_lv_list_arr;
		 // console.log(titleArr.length-1,0<titleArr.length-1)
		if(module.type == 6){
			//根据自定义模板id获取关联的模板id
			$.ajax({
				url:'/mshop/admin/index.php?m=bottom_label&a=icon_list_by_diy',
				async: false,
				data:{'diy_temid':diy_temid},
				type:'POST',
				success:function(res){
					var bottom=JSON.parse(res);
					for(var i=0;i<bottom.length;i++){
		                var newdata={
		                    pic:bottom[i]['noimgUrl']!=null?bottom[i]['noimgUrl']:'',
		                    column_title:bottom[i]['column_title']!=null?bottom[i]['column_title']:'',
		                    link:bottom[i]['url']!=null?bottom[i]['url']:'',
		                    title:bottom[i]['name']!=null?bottom[i]['name']:'',
		                    color:bottom[i]['nocolor']!=null?bottom[i]['nocolor']:''
		                };console.log(newdata);
		                module.content.dataset.push(newdata);
		            }
				}
			})
		}
		else if(module.type == 17){	//活动橱窗专用
			console.log(module.content.round);
			if(module.content.round != ""){
				for(j=0;j<round_arr.length-1;j++){
					var newdata={
						round:'',
						round_pic:'',
						round_color:'',
						title:'商品',
						pic:'images/img-product.jpg',
						num:'111111111',
						money:'10',
					};
					module.content.dataset.push(newdata);
					module.content.dataset[j].round=round_arr[j];
					module.content.dataset[j].round_pic=round_pic_arr[j];
					module.content.dataset[j].round_color=round_color_arr[j];
					module.content.dataset[j].title='标签';
					module.content.dataset[j].pic='images/img-product.jpg';
					module.content.dataset[j].num='111111111';
					module.content.dataset[j].money='130';
				} 
			}else{
				module.content.dataset = [];
			}
		} else {
		
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
				if(detail_value_arr[j] == undefined ){
					module.content.dataset[j].detail_value = "";
				}
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

				var picUrl=imgArr[j];
				//var picUrl=new_baseurl+imgArr[j];
				if(picUrl != undefined && (picUrl.indexOf("weixinpl")>0 || picUrl.indexOf("resources")>0)){ //判断图片路径
					module.content.dataset[j].pic=picUrl;
				}
				else{
					if(imgArr[j]!=""){
						//var defUrl="/weixinpl/common_shop/common/custom_temp/"+imgArr[j];
						//var defUrl=new_baseurl+"/weixinpl/common_shop/common/custom_temp/"+imgArr[j];
						//module.content.dataset[j].pic=defUrl;
						module.content.dataset[j].pic=picUrl;
					}
					
				}
			}		
		}
		console.log(module);
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

/*zpq*/
	var customer_id =<?php echo $customer_id;?>;
    var wsy_page = $('.WSY_page');
    var pagenum=1;
    var search_val='';
    var threed_contents='';
    function go_page(p,search_val){
        $.ajax({
            url:'/wsy_prod/admin/3dmodel/three_d_api.php',
            data:{'data_op':'get_model_more','customer_id':customer_id,'pagenum':p,'search_val':search_val},
            type:'GET',
            async: false,
            success:function(res){
                html = '<thead class="WSY_table_header">';
                html += '<th width="10%">编号</th>';
                html += '<th width="20%">素材名称</th>';
                html += '<th width="20%">素材图片</th>';
                html += '<th width="20%">类型</th>';
                html += '<th width="20%">素材链接</th>';
                html += '<th width="10%">操作</th>';
                html += '</thead>';
                var json_return = $.parseJSON(res);
                var data = json_return['data']['data'];
                for(var i in data){
                    html += '<tr >';
                    html += '<td style="text-align:center;">'+data[i].id+'</td>';
                    html += '<td style="text-align:center;"> '+data[i].title+'</td>';
                    html += '<td style="text-align:center;"> <img width="100" height="100" src="'+data[i].cover_img+'"></td>';
                    html += '<td style="text-align:center;">3D模型</td>';
                    html += '<td style="text-align:center;"><a target="_blank" href="'+data[i].embedLink+'">'+data[i].embedLink+' </a></td>';
                    html += '<td style="text-align:center;"><span class="WSY_buttontj" style="padding:3px 15px;border-radius:3px;margin-left:20px;cursor:pointer;display:inline-block;margin-top:5px;" onclick="WSY_buttontj_tj(this)">添加</span></td>';
                    html += '</tr>';
                }
            }

        });
        $('#WSY_t1_3d').html(html);
    }

    function jumppage(){
        var p=$("input[name='WSY_jump_page']").val()
        $.ajax({
            url:'/wsy_prod/admin/3dmodel/three_d_api.php',
            data:{'data_op':'get_model_more','customer_id':customer_id,'pagenum':p,'search_val':search_val},
            type:'GET',
            async: false,
            success:function(res){
                var json_return = $.parseJSON(res);
                var data = json_return['data']['data'];
                html = '<thead class="WSY_table_header">';
                html += '<th width="10%">编号</th>';
                html += '<th width="20%">素材名称</th>';
                html += '<th width="20%">素材图片</th>';
                html += '<th width="20%">类型</th>';
                html += '<th width="20%">素材链接</th>';
                html += '<th width="10%">操作</th>';
                html += '</thead>';
                for(var i in data){
                    html += '<tr >';
                    html += '<td>'+data[i].id+'</td>';
                    html += '<td style="text-align:center;"> '+data[i].title+'</td>';
                    html += '<td style="text-align:center;"> <img width="100" height="100" src="'+data[i].cover_img+'"></td>';
                    html += '<td style="text-align:center;">3D模型</td>';
                    html += '<td style="text-align:center;"><a target="_blank" href="'+data[i].embedLink+'">'+data[i].embedLink+' </a></td>';
                    html += '<td style="text-align:center;"><span class="WSY_buttontj" style="padding:3px 15px;border-radius:3px;margin-left:20px;cursor:pointer;display:inline-block;margin-top:5px;" onclick="WSY_buttontj_tj(this)">添加</span></td>';
                    html += '</tr>';
                }
                wsy_page.createPage({
                    pageCount:  Math.ceil(json_return['data']['total']/20),
                    current:json_return['data']['current_page'],
                    backFn:function(p){
                        go_page(p,search_val);
                    }
                });
            }

        });
        $('#WSY_t1_3d').html(html);
    }

    function show_three_d(obj){
    	threed_contents=$(obj).attr("data-id")
        html = '<thead class="WSY_table_header">';
        html += '<th width="10%">编号</th>';
        html += '<th width="20%">素材名称</th>';
        html += '<th width="20%">素材图片</th>';
        html += '<th width="20%">类型</th>';
        html += '<th width="20%">素材链接</th>';
        html += '<th width="10%">操作</th>';
        html += '</thead>';
        $.ajax({
            url:'/wsy_prod/admin/3dmodel/three_d_api.php',
            data:{'data_op':'get_model_more','customer_id':customer_id,'pagenum':pagenum,'search_val':search_val},
            type:'GET',
            async: false,
            success:function(res){
                var json_return = $.parseJSON(res);
                var data = json_return['data']['data'];
                for(var i in data){
                    html += '<tr >';
                    html += '<td>'+data[i].id+'</td>';
                    html += '<td style="text-align:center;"> '+data[i].title+'</td>';
                    html += '<td style="text-align:center;"> <img width="100" height="100" src="'+data[i].cover_img+'"></td>';
                    html += '<td style="text-align:center;">3D模型</td>';
                    html += '<td style="text-align:center;"><a target="_blank" href="'+data[i].embedLink+'">'+data[i].embedLink+' </a></td>';
                    html += '<td style="text-align:center;"><span class="WSY_buttontj" style="padding:3px 15px;border-radius:3px;margin-left:20px;cursor:pointer;display:inline-block;margin-top:5px;" onclick="WSY_buttontj_tj(this)">添加</span></td>';
                    html += '</tr>';
                }
                wsy_page.createPage({
                    pageCount:  Math.ceil(json_return['data']['total']/20),
                    current:json_return['data']['current_page'],
                    backFn:function(p){
                        go_page(p,search_val);
                    }
                });
            }

        });
        $('#WSY_t1_3d').html(html);
        $('.mask_3d').show()
        wsy_page.show();
    }
    $('#3d_close').click(function () {
       	threed_insert_ck=0;
       	$('.mask_3d').hide()
   	})
	function WSY_buttontj_tj(obj){
		var newstr=$(obj).parent().parent().children(4).children("a").html().replace('http','https');
		$("input[name='threed_content_"+threed_contents+"']").val($(obj).parent().parent().children("td").eq(1).html())
	    $("input[name='threed_content_"+threed_contents+"']").change()
		$("input[name='video_link']").val(newstr)
	    $("input[name='video_link']").change()
	    $.ajax({
			url:'/wsy_prod/admin/3dmodel/three_d_api.php',
			data:{'data_op':'save_custom_diy','customer_id':customer_id,},
			type:'GET',
			async: false,  
			success:function(res){
				
			}
		});
	    $('.mask_3d').hide()
	}
/*zpq*/

</script>

<!--选择链接的JS结束-->
<!-- 新选择链接 -->
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script>
    var that;//标签选择
    var customer_id_en = '<?php echo $customer_id_en; ?>';
    //选择优惠劵
	function showSelector(obj,is_activity=0){
		that = obj;
        var selector_id = $(obj).parent().find('#selector_id').val();
		layer.open({
			  type: 2,
			  area: ['1500px', '720px'],
			  fixed: false, //不固定
			  maxmin: true,
			  resize:true,
			  title: '选择链接页面',
			  content: '/mshop/admin/index.php?m=plug_link_selector&a=selector_list&customer_id='+customer_id_en+'&selector_id='+selector_id+'&is_activity='+is_activity,
		});
	}
    //选择链接回调函数
    //[int] selector_id 链接组成ID [string] selector_title 链接名称
    function showSelectorCallback(selector_id,selector_title,is_activity=0){
		if(is_activity == 1){
			console.log(selector_id);
			console.log(selector_title);
			var production_num = $(that).parents().find('#production_num').val();
			if(production_num == null){
				production_num = 4;
			}
		//	$(that).parent().find('.act-adress').text(selector_id);
			$.ajax({
				url:'/mshop/admin/index.php?m=plug_link_selector&a=common_activity_product',
				data:{'selector_id':selector_id,'customer_id':customer_id,'selector_title':selector_title,'show_num':production_num},
				type:'POST',
				async: false,
				success:function(res){
					var json_return = eval('(' + res + ')');
					list_value = json_return.result;
					last_title = json_return.title;
					$(that).parent().find("input[name='activity_id']").val(selector_id);
					$(that).parent().find("input[name='activity_title']").val(selector_title);
					$(that).parent().find("select[name='type_id_2']").append("<option value='"+selector_id+"'>"+selector_id+"</option>");  //添加一项option
					$(that).parent().find("#type_id_2").val(selector_id);
					$(that).parent().find(".act-adress").text(json_return.title);
					$(that).parent().find("select[name='type_id_2']").trigger('change');
				}
			});
		}else{
			$(that).parent().find("#selector_title").val(selector_title);
		//	console.log($(that).parent().find("#selector_title").val());
		//	console.log($(that).parent().find("input[name='selector_title']").val());
			$(that).parent().find("#selector_id").val(selector_id);
			$(that).parent().find("select[name='type_id_2']").append("<option value='"+selector_id+"'>"+selector_id+"</option>");  //添加一项option
			$(that).parent().find("#type_id_2").val(selector_id);
			$(that).parent().find("select[name='type_id_2']").trigger('change');
		}
		
    }

    //选择底部模板
    function showBottomLabel(obj){
    	var bottom_id = $(obj).parent().find('#bottom_id').val();
    	layer.open({
			  type: 2,
			  area: ['1500px', '720px'],
			  fixed: false, //不固定
			  maxmin: true,
			  resize:true,
			  title: '选择底部模板',
			  content: '/mshop/admin/index.php?m=bottom_label&a=bottom_selector_list&customer_id='+customer_id_en+'&bottom_id='+bottom_id,
		});
    }
    //选择底部模板回调
    function showBottomSelectorCallback(id){
    	$("#bottom_id").val(id);
    	$.ajax({
			url:'/mshop/admin/index.php?m=bottom_label&a=icon_list_get',
			data:{'tmp_id':id},
			type:'POST',
			async: false,
			success:function(res){
				bottomArr=JSON.parse(res);
				console.log(bottomArr);
    			$("#bottom_id").change();
			}
		});
    }
    //名片规则
    function showCard(){
		layer.open({
			  type: 2,
			  area: ['1400px', '770px'],
			  fixed: false, //不固定
			  maxmin: true,
			  resize:true,
			  title: '名片规则',
			  content: '/mshop/admin/index.php?m=promoter_card&a=get_card_setting&customer_id='+customer_id_en,

		});
	}
</script>

<script>
    function bindTypeId6Change(e) {
        var url = 'get_o2o_list.php?level=' + e.value; //获取o2o二级行业列表

        $.ajax({
            type:'GET',
            url:url,
            dataType:'JSON',
            success:function(data){
                console.log(data);
                var option = '';
                option = '<option value="0"> ---------------全部--------------- </option>';
                if(data != null){
                    for (var k=0,m=data.length;k<m;k++) {
                        var type_level_id_name=data[k].split("_");
                        var type_level=type_level_id_name[0];
                        var type_id=type_level_id_name[1];
                        var type_name=type_level_id_name[2];
                        option +="<option value=" +type_level+"_"+type_id+ ">" +type_name+ "</option>";
                    }
                }
                $("#type_id_7").html(option);  //js刷新第二个下拉框的值
            },
        });
        
    }
</script>

</body>
</html>  
<?php 

mysql_close($link);
?>