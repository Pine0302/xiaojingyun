<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
// require('../../../../../weixinpl/back_init.php');
require('../../../../../weixinpl/common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');
require('../../../../../weixinpl/auth_user.php');
require('../../../../../weixinpl/common/utility_4m.php');
$head=6;
_mysql_query("SET NAMES UTF8");
$new_baseurl = $protocol_http_host;

// 获取商家信息
$supply_id = $_SESSION['city_shop_AcountID'];
require_once('../../../../../o2o/web/city_area/shop/cityarea_shop_utlity.php');
$shop_utlity = new cityarea_shop_Utlity();
$shop_info = $shop_utlity->supply_informations($customer_id,$supply_id); //店铺信息
$pos = strpos($shop_info['logo'],"//thirdwx.qlogo.cn");
if($pos===0){//商家类型头像包含微信域名即使用微信头像
    $logo = $shop_info['logo'];
}else{
    $logo = $new_baseurl.$shop_info['logo'];
}

//商家类型为社区代理 使用总店产品
$search_supply_id = $supply_id;
if($shop_info['types'] == 22){
    $search_supply_id          = $shop_info['parent_id'];
    $label_result              = $shop_utlity->supply_label_image($customer_id,$search_supply_id);//查询总店标签图片
    $parent_shop_info          = $shop_utlity->supply_informations($customer_id,$search_supply_id);//总店冻结状态
    $shop_info['label_image']  = $label_result['label_image'];
    $shop_info['label_image2'] = $label_result['label_image2'];
    if($parent_shop_info == 1 || $is_freeze == 1){//总店冻结或社区代理冻结
        $is_show = 0;
    }
}

// 等级标签
$label_id = $shop_info['label_id'];
$query_label = "select label_image2,label_image from weixin_cityarea_supply_label where isvalid=true and id=".$label_id." and customer_id=".$customer_id;
$result_label = _mysql_query($query_label) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result_label)) {
    $shop_info['label_image'] = $row->label_image;
    $shop_info['label_image2'] = $row->label_image2;
}

// 获取礼包列表
require_once($_SERVER['DOCUMENT_ROOT']."/weixinpl/namespace_database.php");
$database = new \Key\DB();
$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);

$sql = "SELECT package_name,id from package_list_t where customer_id='{$customer_id}' and isvalid=true ";
$package_list = $database->getData($sql);
foreach ($package_list as $key => $value) {
	$package_lists[] = "{$value['id']}_{$value['package_name']}";
}

// 商品列表
$sql = "SELECT id,name FROM weixin_cityarea_shop_products WHERE isout = 0 AND isvalid = true AND supply_id={$supply_id}";
$product_list = $database->getData($sql);
foreach ($product_list as $key => $value) {
	$product_lists[] = "{$value['id']}_{$value['name']}";
}

// 门店列表
$sql = "SELECT s.id,s.shop_name
		from weixin_cityarea_supply as s
		inner join weixin_cityarea_shop_extends as se
		where s.isvalid=true and ((s.types =21 and s.is_freeze = 0 and s.id={$supply_id}) or (s.types =22 and s.is_freeze = 0 and s.is_confirm = 2 and s.parent_id={$supply_id})) and s.customer_id={$customer_id} and se.isvalid=true and se.customer_id={$customer_id} and se.supply_id=s.id and s.id!={$search_supply_id}";
$store_list = $database->getData($sql);
foreach ($store_list as $key => $value) {
	$store_lists[] = "{$value['id']}_{$value['shop_name']}";
}

$diy_temid=-1;
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
		$inser_custom="insert into weixin_cityarea_diy_template (customer_id,content,isused,isvalid,creatime,name,cityarea_id) values ('".$customer_id."','-1',false,true,now(),'自定义模板','{$supply_id}')";
		$result_insert=_mysql_query($inser_custom) or die ('inser_custom faild' .mysql_error());
		$diy_temid=mysql_insert_id();
		$query_temid="select name from weixin_cityarea_diy_template where id=".$diy_temid." and isvalid=true and cityarea_id='{$supply_id}' and customer_id=".$customer_id." limit 0,1";
		$result_query_temid=_mysql_query($query_temid) or die ('query_temid faild' .mysql_error());
		while($row=mysql_fetch_object($result_query_temid)){
			$name=$row->name;
		}
		$temid=$diy_temid;
	break;
	case "edit":
		$query_temid="select id,content,name,bgcolor from weixin_cityarea_diy_template where id=".$temid." and isvalid=true and cityarea_id='{$supply_id}' and customer_id=".$customer_id." limit 0,1";
		$result_query_temid=_mysql_query($query_temid) or die ('query_temid faild' .mysql_error());
		while($row=mysql_fetch_object($result_query_temid)){
			$diy_temid=$row->id;
			$content=$row->content;
			$name=$row->name;
			$bgcolor=$row->bgcolor;
		}
		$k=0;
		$custom_query="select diy_tem_contid,title,imgurl,foreign_id,detail_id,mod_padding,mod_img_padding,css_type,pro_title_show,pro_title_twoline,pro_numshow,foot_position,placeholder,show_sale,type,link_type,select_value,detail_value,detail_name,search_color,color,video_link,rolling_direction,rolling_speed,show_time_limit,city_name,start_time,end_time,province,sel_link_type,link from weixin_cityarea_diy_template_content where isvalid=true and customer_id=".$customer_id." and LOCATE(diy_tem_contid,'".$content."') ORDER  BY FIND_IN_SET(diy_tem_contid,'".$content."')";
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
			$k++;
		}

	break;
}


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


///城市商圈，渠道开关
$is_cityarea=0;
$is_cityarea_count=0;
$query="select count(1) as is_cityarea_count from customer_funs cf inner join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and (c.sys_name='商圈-美食' or c.sys_name='商圈-外卖' or c.sys_name='商圈-金融保险' or c.sys_name='商圈-酒店' or c.sys_name='商圈-ktv' or c.sys_name='商圈-线下商城')";
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

	//城市商圈（金融）
	$cityarea_industry[]="2_金融-贷款_5";
	$cityarea_industry[]="2_金融-信用卡_6";
	$cityarea_industry[]="2_金融-保险_7";
	//城市商圈（金融） End
}

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

$fixedlink[]="-1_---------------请选择---------------";
$fixedlink[]="-10_店铺首页";
$fixedlink[]="-11_全部商品列表";
$fixedlink[]="-12_店铺详情";
$fixedlink[]="-13_线上微店首页";
if($shop_info['types'] == 21 or $shop_info['types'] == 22){
$fixedlink[]="-14_各门店首页";
}
// $fixedlink[]="-15_商家产品";
$fixedlink[]="-16_购物车";
$fixedlink[]="-17_门店列表";
$fixedlink[]="-20_个人中心";

/* 8.1分类 */
// $type_arr[] = '-1_---------------请选择---------------';
//分类排序
$sort_str = "";
/*$type_sort = "SELECT sort_str FROM weixin_commonshop_type_sort WHERE customer_id=".$customer_id;
$result_sort = _mysql_query($type_sort) or die ('type_sort failed:'.mysql_error());
while( $row_sort = mysql_fetch_object($result_sort) ){
   $sort_str = $row_sort -> sort_str;
}

$query = "select id, name from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=-1 and customer_id=".$customer_id;

if( $sort_str ){
	$query .= ' order by field(id'.$sort_str.')';
}*/
$type_arr = array();
$ctype_arr = array();
/*$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
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
$type_arr = '';
$sql = "SELECT	id,name FROM weixin_cityarea_shop_types WHERE customer_id = $customer_id AND isvalid = 1 AND is_shelves = 1";
$parent_id = $shop_info['parent_id'];
if($parent_id>0){//继承总店分类
    $sql.= " AND (supply_id = $supply_id OR supply_id= $parent_id )";
}else{
    $sql.= " AND supply_id = $supply_id";
}
$type_arrs = $database->getData($sql);
foreach ($type_arrs as $key => $value) {
	$type_arr[] = "{$value['id']}_{$value['name']}";
}
// var_dump($type_arr);
/* 8.1分类 */

/*获取省、市*/
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

$query_open_tem = "SELECT id,name FROM weixin_cityarea_diy_template WHERE is_open=true AND isvalid=true and cityarea_id='{$cityarea_id}' AND customer_id=".$customer_id." AND id!=".$temid." ORDER BY id DESC";
$result_open_tem = _mysql_query($query_open_tem) or die('Query_open_tem failed:'.mysql_error());
while( $row_open_tem = mysql_fetch_object($result_open_tem) ){
	$template_link[] = $row_open_tem->id ."_". $row_open_tem->name;
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
.WSY_homeleft_middle{overflow-x: hidden;}
</style>
       <!--列表内容大框开始-->
	<div class="WSY_columnbox" style="position:relative">

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
                    <a data-type="20" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>联系电话</a>
                    <a data-type="21" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>定位地址</a>
                    <a data-type="22" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>店铺顶部信息</a>
                    <!-- <a data-type="11" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>LBS定位</a> -->
                    <!-- <a data-type="12" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>LBS城市广告</a> -->
                    <!-- <a data-type="13" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>滚动公告栏</a> -->
                    <a data-type="14" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>头部引导页</a>
                    <!-- <a data-type="15" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>头像插件</a> -->
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
                <a href="javascript:;" class="type-conitem-btn type-edit j-edit">编辑</a>
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
                        </select>
						<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			                {{? it.product_lists}}
			                {{	for (k=0,m=it.product_lists.length;k<m;k++) {
			                	fl=it.product_lists[k].split("_");
			            	}}
			            	<option value="-11_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            	{{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{? select_val==-14}}
			<div class="formitems">
			    <label class="fi-name"></label>
			    <div class="form-controls">
			        <div class="droplist">
			            <select  name="type_id_3"  id="type_id_3"  class="input xlarge" style="height:28px;">
			                <option value="-14">全部</option>
			                {{? it.store_lists}}
			                {{	for (k=0,m=it.store_lists.length;k<m;k++) {
			                	fl=it.store_lists[k].split("_");
			            	}}
			            	<option value="-14_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
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
                        </select>
						<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			                {{? it.product_lists}}
			                {{for (k=0,m=it.product_lists.length;k<m;k++) {
			                fl=it.product_lists[k].split("_");
			            }}
			            <option value="-11_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{? select_val==-14}}
			<div class="formitems">
			    <label class="fi-name"></label>
			    <div class="form-controls">
			        <div class="droplist">
			            <select  name="type_id_3"  id="type_id_3"  class="input xlarge" style="height:28px;">
			                <option value="-14">全部</option>
			                {{? it.store_lists}}
			                {{for (k=0,m=it.store_lists.length;k<m;k++) {
			                fl=it.store_lists[k].split("_");
			            }}
			            <option value="-14_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{?}}
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="3" {{? it.content.dataset[i].sel_link_type == 3}}checked{{?}}/>产品分类页：</label>
				<div class="form-controls">
                    <div class="droplist">
                        <select  name="type_id_4"  id="type_id_4"  class="input xlarge" style="height:28px;">
                        {{? it.type_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value="-1">所有分类</option>
                        {{for (k=0,m=it.type_arr.length;k<m;k++) {
								type_id_name=it.type_arr[k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_2"{{? type_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.ctype_arr[type_id]}}
								{{for (j=0,n=it.ctype_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.ctype_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_2"{{? ctype_id+'_2'==it.content.dataset[0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.ctype_arr[ctype_id]}}
										{{for (h=0,b=it.ctype_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.ctype_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_2"{{? ctype_id3+'_2'==it.content.dataset[0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.ctype_arr[ctype_id3]}}
											{{for (g=0,v=it.ctype_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.ctype_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}_2"{{? ctype_id4+'_2'==it.content.dataset[0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
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
<div class="content-box col{{= it.content.dataset.length }}">
{{ for(var i=0,l=it.content.dataset.length;i<l;i++) { }}
    <div class="icon-box"><a href="#"><img src="{{=it.content.dataset[i].pic}}">{{? it.content.pro_title_show==1}}<p style="color:{{=it.content.dataset[i].color}}">{{=it.content.dataset[i].title}}</p>{{?}}</a></div>
{{ } }}
    <div class="clear"></div>
</div>
</div>
</script>
<script type="dot-template" id="type_ctrl_3">
<div class="formitems">
        <label class="fi-name">模块上下边距：</label>
        <div class="form-controls">
            <div id="slider" class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
            <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
        </div>
</div>
<div class="formitems">
    <label class="fi-name">是否显示文字：</label>
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="pro_title_show" value="1"{{? it.content.pro_title_show==1}} checked{{?}}>显示</label>
            <label><input type="radio" name="pro_title_show" value="0"{{? it.content.pro_title_show==0}} checked{{?}}>隐藏</label>
        </div>
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
                        </select>
						<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			                {{? it.product_lists}}
			                {{for (k=0,m=it.product_lists.length;k<m;k++) {
			                fl=it.product_lists[k].split("_");
			            }}
			            <option value="-11_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{? select_val==-14}}
			<div class="formitems">
			    <label class="fi-name"></label>
			    <div class="form-controls">
			        <div class="droplist">
			            <select  name="type_id_3"  id="type_id_3"  class="input xlarge" style="height:28px;">
			                <option value="-14">全部</option>
			                {{? it.store_lists}}
			                {{for (k=0,m=it.store_lists.length;k<m;k++) {
			                fl=it.store_lists[k].split("_");
			            }}
			            <option value="-14_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{?}}
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type ex" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="3" {{? it.content.dataset[i].sel_link_type == 3}}checked{{?}}/>产品分类页：</label>
				<div class="form-controls">
                    <div class="droplist">
                        <select  name="type_id_4"  id="type_id_4"  class="input xlarge" style="height:28px;">
                        {{? it.type_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value="ex_2">所有分类</option>
                        {{for (k=0,m=it.type_arr.length;k<m;k++) {
								type_id_name=it.type_arr[k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_2"{{? type_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.ctype_arr[type_id]}}
								{{for (j=0,n=it.ctype_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.ctype_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_2"{{? ctype_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.ctype_arr[ctype_id]}}
										{{for (h=0,b=it.ctype_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.ctype_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_2"{{? ctype_id3+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.ctype_arr[ctype_id3]}}
											{{for (g=0,v=it.ctype_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.ctype_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}_2"{{? ctype_id4+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
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
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
				<div class="form-controls">
					<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
			</div>
            <div class="formitems">
                <label class="fi-name">标题：</label>
                <div class="form-controls">
                    <input type="text" name="title" class="input xlarge" value="{{=it.content.dataset[i].title}}" maxlength="10">
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
                        </select>
						<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			                {{? it.product_lists}}
			                {{for (k=0,m=it.product_lists.length;k<m;k++) {
			                fl=it.product_lists[k].split("_");
			            }}
			            <option value="-11_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{? select_val==-14}}
			<div class="formitems">
			    <label class="fi-name"></label>
			    <div class="form-controls">
			        <div class="droplist">
			            <select  name="type_id_3"  id="type_id_3"  class="input xlarge" style="height:28px;">
			                <option value="-14">全部</option>
			                {{? it.store_lists}}
			                {{for (k=0,m=it.store_lists.length;k<m;k++) {
			                fl=it.store_lists[k].split("_");
			            }}
			            <option value="-14_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{?}}
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="3" {{? it.content.dataset[i].sel_link_type == 3}}checked{{?}}/>产品分类页：</label>
				<div class="form-controls">
                    <div class="droplist">
                        <select  name="type_id_4"  id="type_id_4"  class="input xlarge" style="height:28px;">
                        {{? it.type_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value="-1">所有分类</option>
                        {{for (k=0,m=it.type_arr.length;k<m;k++) {
								type_id_name=it.type_arr[k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_2"{{? type_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.ctype_arr[type_id]}}
								{{for (j=0,n=it.ctype_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.ctype_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_2"{{? ctype_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.ctype_arr[ctype_id]}}
										{{for (h=0,b=it.ctype_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.ctype_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_2"{{? ctype_id3+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.ctype_arr[ctype_id3]}}
											{{for (g=0,v=it.ctype_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.ctype_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}_2"{{? ctype_id4+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
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
                        </select>
						<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			                {{? it.product_lists}}
			                {{for (k=0,m=it.product_lists.length;k<m;k++) {
			                fl=it.product_lists[k].split("_");
			            }}
			            <option value="-11_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{? select_val==-14}}
			<div class="formitems">
			    <label class="fi-name"></label>
			    <div class="form-controls">
			        <div class="droplist">
			            <select  name="type_id_3"  id="type_id_3"  class="input xlarge" style="height:28px;">
			                <option value="-14">全部</option>
			                {{? it.store_lists}}
			                {{for (k=0,m=it.store_lists.length;k<m;k++) {
			                fl=it.store_lists[k].split("_");
			            }}
			            <option value="-14_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{?}}
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="3" {{? it.content.dataset[i].sel_link_type == 3}}checked{{?}}/>产品分类页：</label>
				<div class="form-controls">
                    <div class="droplist">
                        <select  name="type_id_4"  id="type_id_4"  class="input xlarge" style="height:28px;">
                        {{? it.type_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value="-1">所有分类</option>
                        {{for (k=0,m=it.type_arr.length;k<m;k++) {
								type_id_name=it.type_arr[k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_2"{{? type_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.ctype_arr[type_id]}}
								{{for (j=0,n=it.ctype_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.ctype_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_2"{{? ctype_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.ctype_arr[ctype_id]}}
										{{for (h=0,b=it.ctype_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.ctype_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_2"{{? ctype_id3+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.ctype_arr[ctype_id3]}}
											{{for (g=0,v=it.ctype_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.ctype_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}_2"{{? ctype_id4+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
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
                        </select>
						<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			                {{? it.product_lists}}
			                {{for (k=0,m=it.product_lists.length;k<m;k++) {
			                fl=it.product_lists[k].split("_");
			            }}
			            <option value="-11_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{? select_val==-14}}
			<div class="formitems">
			    <label class="fi-name"></label>
			    <div class="form-controls">
			        <div class="droplist">
			            <select  name="type_id_3"  id="type_id_3"  class="input xlarge" style="height:28px;">
			                <option value="-14">全部</option>
			                {{? it.store_lists}}
			                {{for (k=0,m=it.store_lists.length;k<m;k++) {
			                fl=it.store_lists[k].split("_");
			            }}
			            <option value="-14_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{?}}
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="3" {{? it.content.dataset[i].sel_link_type == 3}}checked{{?}}/>产品分类页：</label>
				<div class="form-controls">
                    <div class="droplist">
                        <select  name="type_id_4"  id="type_id_4"  class="input xlarge" style="height:28px;">
                        {{? it.type_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value="-1">所有分类</option>
                        {{for (k=0,m=it.type_arr.length;k<m;k++) {
								type_id_name=it.type_arr[k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_2"{{? type_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.ctype_arr[type_id]}}
								{{for (j=0,n=it.ctype_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.ctype_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_2"{{? ctype_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.ctype_arr[ctype_id]}}
										{{for (h=0,b=it.ctype_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.ctype_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_2"{{? ctype_id3+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.ctype_arr[ctype_id3]}}
											{{for (g=0,v=it.ctype_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.ctype_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}_2"{{? ctype_id4+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
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
                        </select>
						<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			                {{? it.product_lists}}
			                {{for (k=0,m=it.product_lists.length;k<m;k++) {
			                fl=it.product_lists[k].split("_");
			            }}
			            <option value="-11_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{? select_val==-14}}
			<div class="formitems">
			    <label class="fi-name"></label>
			    <div class="form-controls">
			        <div class="droplist">
			            <select  name="type_id_3"  id="type_id_3"  class="input xlarge" style="height:28px;">
			                <option value="-14">全部</option>
			                {{? it.store_lists}}
			                {{for (k=0,m=it.store_lists.length;k<m;k++) {
			                fl=it.store_lists[k].split("_");
			            }}
			            <option value="-14_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{?}}
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="3" {{? it.content.dataset[i].sel_link_type == 3}}checked{{?}}/>产品分类页：</label>
				<div class="form-controls">
                    <div class="droplist">
                        <select  name="type_id_4"  id="type_id_4"  class="input xlarge" style="height:28px;">
                        {{? it.type_arr}}

                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value="-1">所有分类</option>
                            {{for (k=0,m=it.type_arr.length;k<m;k++) {
								type_id_name=it.type_arr[k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_2"{{? type_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.ctype_arr[type_id]}}
								{{for (j=0,n=it.ctype_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.ctype_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_2"{{? ctype_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.ctype_arr[ctype_id]}}
										{{for (h=0,b=it.ctype_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.ctype_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_2"{{? ctype_id3+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.ctype_arr[ctype_id3]}}
											{{for (g=0,v=it.ctype_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.ctype_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}_2"{{? ctype_id4+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
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
                        </select>
						<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			                {{? it.product_lists}}
			                {{for (k=0,m=it.product_lists.length;k<m;k++) {
			                fl=it.product_lists[k].split("_");
			            }}
			            <option value="-11_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{? select_val==-14}}
			<div class="formitems">
			    <label class="fi-name"></label>
			    <div class="form-controls">
			        <div class="droplist">
			            <select  name="type_id_3"  id="type_id_3"  class="input xlarge" style="height:28px;">
			                <option value="-14">全部</option>
			                {{? it.store_lists}}
			                {{for (k=0,m=it.store_lists.length;k<m;k++) {
			                fl=it.store_lists[k].split("_");
			            }}
			            <option value="-14_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{?}}
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="3" {{? it.content.dataset[i].sel_link_type == 3}}checked{{?}}/>产品分类页：</label>
				<div class="form-controls">
                    <div class="droplist">
                        <select  name="type_id_4"  id="type_id_4"  class="input xlarge" style="height:28px;">
                        {{? it.type_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value="-1">所有分类</option>
                            {{for (k=0,m=it.type_arr.length;k<m;k++) {
								type_id_name=it.type_arr[k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_2"{{? type_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.ctype_arr[type_id]}}
								{{for (j=0,n=it.ctype_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.ctype_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_2"{{? ctype_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.ctype_arr[ctype_id]}}
										{{for (h=0,b=it.ctype_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.ctype_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_2"{{? ctype_id3+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.ctype_arr[ctype_id3]}}
											{{for (g=0,v=it.ctype_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.ctype_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}_2"{{? ctype_id4+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
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
                <label class="fi-name">选择分类：</label>
                <div class="form-controls">
                    <div class="droplist">
                        <select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
                        {{? it.type_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value="-1">所有分类</option>
                        {{for (k=0,m=it.type_arr.length;k<m;k++) {
								type_id_name=it.type_arr[k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_1"{{? type_id+'_1'==it.content.dataset[0].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.ctype_arr[type_id]}}
								{{for (j=0,n=it.ctype_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.ctype_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_1"{{? ctype_id+'_1'==it.content.dataset[0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.ctype_arr[ctype_id]}}
										{{for (h=0,b=it.ctype_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.ctype_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_1"{{? ctype_id3+'_1'==it.content.dataset[0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.ctype_arr[ctype_id3]}}
											{{for (g=0,v=it.ctype_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.ctype_arr[ctype_id3][g].split("_");
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
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
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
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
<div class="formitems">
        <label class="fi-name">放置位置：</label>
        <div class="form-controls">
            <div class="radio-group">
                <label><input type="radio" name="foot_position" value="1" {{? it.content.foot_position==1}} checked{{?}}>固定跟随页面移动</label>
                <label><input type="radio" name="foot_position" value="2"{{? it.content.foot_position==2}} checked{{?}}>不跟随页面移动</label>
            </div>
        </div>
</div>
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
                        </select>
						<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			                {{? it.product_lists}}
			                {{for (k=0,m=it.product_lists.length;k<m;k++) {
			                fl=it.product_lists[k].split("_");
			            }}
			            <option value="-11_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{? select_val==-14}}
			<div class="formitems">
			    <label class="fi-name"></label>
			    <div class="form-controls">
			        <div class="droplist">
			            <select  name="type_id_3"  id="type_id_3"  class="input xlarge" style="height:28px;">
			                <option value="-14">全部</option>
			                {{? it.store_lists}}
			                {{for (k=0,m=it.store_lists.length;k<m;k++) {
			                fl=it.store_lists[k].split("_");
			            }}
			            <option value="-14_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{?}}
			<div class="formitems">
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="3" {{? it.content.dataset[i].sel_link_type == 3}}checked{{?}}/>产品分类页：</label>
				<div class="form-controls">
                    <div class="droplist">
                        <select  name="type_id_4"  id="type_id_4"  class="input xlarge" style="height:28px;">
                        {{? it.type_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value="-1">所有分类</option>
                        {{for (k=0,m=it.type_arr.length;k<m;k++) {
								type_id_name=it.type_arr[k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}_2"{{? type_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.ctype_arr[type_id]}}
								{{for (j=0,n=it.ctype_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.ctype_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}_2"{{? ctype_id+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.ctype_arr[ctype_id]}}
										{{for (h=0,b=it.ctype_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.ctype_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}_2"{{? ctype_id3+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.ctype_arr[ctype_id3]}}
											{{for (g=0,v=it.ctype_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.ctype_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}_2"{{? ctype_id4+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
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
				<label class="fi-name"><input type="radio" class="sel_link_type" name="sel_link_type_{{=i}}" id="sel_link_type_{{=i}}" value="2" {{? it.content.dataset[i].sel_link_type == 2}}checked{{?}}/>填写链接：</label>
				<div class="form-controls">
					<input type="text" name="custom_link" id="custom_link_{{=i}}" value="{{= it.content.dataset[i].link}}" class="input xlarge" />
				</div>
			</div>
            <div class="formitems">
                <label class="fi-name">标题：</label>
                <div class="form-controls">
                    <input type="text" name="title" class="input xlarge" value="{{=it.content.dataset[i].title}}" maxlength="10">
                    <span class="fi-help-text"></span>
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
                        </select>
						<input type="text" id="search_input_{{=i}}" value="" class="input search-input" /><button class="search-input-btn" id="search_btn_{{=i}}">搜索</button>
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
			                {{? it.product_lists}}
			                {{for (k=0,m=it.product_lists.length;k<m;k++) {
			                fl=it.product_lists[k].split("_");
			            }}
			            <option value="-11_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
			            {{ } }}
			            {{?}}
			        </select>
			    </div>
			</div>
			{{?}}
			{{? select_val==-14}}
			<div class="formitems">
			    <label class="fi-name"></label>
			    <div class="form-controls">
			        <div class="droplist">
			            <select  name="type_id_3"  id="type_id_3"  class="input xlarge" style="height:28px;">
			                <option value="-14">全部</option>
			                {{? it.store_lists}}
			                {{for (k=0,m=it.store_lists.length;k<m;k++) {
			                fl=it.store_lists[k].split("_");
			            }}
			            <option value="-14_{{=fl[0]}}" {{? '-11_'+fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
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
<style>
	.icon {width: 7%;display: table-cell;}
	.content {width: 90%;line-height: 20px;display: table-cell;}
	.arrow {width: 7%;text-align: right;display: table-cell;vertical-align: middle;}
	.icon img {width: 15px;height: 15px;}
	.arrow img {width: 12px;height: 12px;}
</style>
<!--联系电话-->
<script type="dot-template" id="type_con_20">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
    <div class="members_con">
        <a title="{{=it.content.dataset[0].title}}" href="{{=it.content.dataset[0].link}}" >
        	<div id="location" class="list-item">
        		<div class="icon"><img src="images/icon_phone_yellow.png"></div>
        		<div class="content"><span class="text-black14"><?php echo $shop_info['phone'] ?></span></div>
        		<div class="arrow"><img src="images/arrow_right_gray2.png"></div>
        		<div class="clear"></div>
        	</div>
        </a>
    </div>
</div>
</script>
<script type="dot-template" id="type_ctrl_20">
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
</script>
<!--联系电话-->
<!--定位地址-->
<script type="dot-template" id="type_con_21">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
    <div class="members_con">
        <a title="{{=it.content.dataset[0].title}}" href="{{=it.content.dataset[0].link}}" >
        	<div id="location" class="list-item">
        	    <div class="icon"><img src="images/icon_loc_yellow.png"></div>
        	    <div class="content"><span class="text-black14"><?php echo $shop_info['add_c'].$shop_info['add_a'].$shop_info['address'] ?><?php if($distance!=''){?><p class="text-yellow14"><?php echo $distance; ?></p><?php }?></span></div>
        	    <div class="arrow"><img src="images/arrow_right_gray2.png"></div>
        	    <div class="clear"></div>
        	</div>
        </a>
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
</script>
<!--定位地址-->
<style>
	.shop-banner{width: 320px;height: 192.8px;}
	.shop-banner img{width:100%;height:100%;}
	.btn-back {position: absolute;left: 5px;top: 0px;padding: 5px;}
	.btn-back img {width: 13px;height: 13px;}
	.btn-menu {position: absolute;right: 8px;top: 2px;padding: 5px;}
	.btn-menu img.menu {width: 18px;height: 14px;}
	.info-area .action-pane{width:100%;display:table;top:50%;padding: 0px 5px 12px 7px;overflow:auto;position: absolute;}
	.info-area .action-pane .leftPane2{width:45%;display:table-cell;padding-left:5px;vertical-align: top;}
	.info-area .action-pane .rightPane2{width:53%;display:table-cell;vertical-align: top;color: #ffffff;}
	.info-area .action-pane .leftPane2 .logo img{width:90%;max-height:82px;}
	.info-area .action-pane .leftPane2 .title{font-size:15px;color:#2a2a2a;margin-top:2px;}
	.info-area .action-pane .leftPane2 .title span{color:#ffffff;}
	.info-area .action-pane .rightPane2 .action-group{width:100%;display:table;color: #ffffff;margin: -12px 0 0 0;}
	.info-area .action-pane .rightPane2 .action-group .col1{width:40%;display:table-cell;text-align:center;float:left;}
	.info-area .action-pane .rightPane2 .action-group .col2{width:35%;display:table-cell;text-align:center;float:left;}
	.info-area .action-pane .rightPane2 .action-group .col3{width:25%;display:table-cell;text-align:center;float:left;}
	.info-area .action-pane .rightPane2 .action-group .icon img{width:14px;height:14px;}
	.action-group .icon{height: 20px;}
	.info-area .action-pane .rightPane2 .action-group .icon span{font-size:9px;}
	.info-area .action-pane .rightPane2 .action-group .title{font-size:9px;color:#ffffff;margin-top:4px;}
	.info-area .action-pane .rightPane2 .action-group .title.collect{font-size:9px;color:#fd7d24;margin-top:4px;}
	.info2 {display: block;}
	.info2 .star{width:80px;height:15px;background-image: url("images/star_normal5.png");background-repeat: no-repeat;background-size:100% 100%;float:left;margin-top:4px;}
	.info2 .star .select{height:100%;overflow-x:hidden;}
	.info2 .star2{width:80px;height:100%;background-image: url("images/star_select5.png");background-repeat: no-repeat;background-size:100% 100%;}
	.info2 .mark{font-size:10px;color:#fff;line-height:26px;margin-left:7px;float:left;}

	.leftPane2 .logo {position: relative;display: inline-block;}
	.leftPane2 .small{width: 20px !important;height: 20px !important;border-radius: 5px;position: absolute;top: -10px;right: 6px;}
	.small {margin-top: -3px !important;width: 15px !important;height: 15px !important;margin-left: -10px !important;-webkit-border-radius: 5px;}
	.span-img {max-width: 90px;width: 45px;height: 15px;display: inline-block;margin-top: 5px;border: 1px solid #c1c1c1;border-radius: 2px;}
</style>
<!--店铺顶部信息-->
<script type="dot-template" id="type_con_22">
<div class="con_display" style="{{? it.content.padding}}padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px{{?}}">
	<!--<div style="padding-bottom:24px;">-->
	<div>
    <div class="members_con">
        <div class="info-area">
            <div class="shop-banner"><img src="<?php echo $new_baseurl.$shop_info['banner']; ?>"></div>
            <div class="btn-back"><img src="images/arrow_left_white.png"></div>
            <div id="dropmenu" class="btn-menu disp-inblock am-dropdown" data-am-dropdown>
                <img class="menu am-dropdown-toggle" src="images/icon_three_white.png">
                <?php include('../../../../../weixinpl/back_newshops/Base/personalization/cityarea_shop_custom/common_menu.php'); ?>
            </div>
            <div class="action-pane">
                <div class="leftPane2">
                    <div class="logo"><img src="<?php echo $logo; ?>"><?php if ($shop_info['label_image']!=""&&$shop_info['label_image']!=null) {?><img class='small' src="<?php echo $shop_info['label_image']; ?>"><?php }?>
                    </div>
                </div>
                <div class="rightPane2">
                	<div class="title"><span style='color: #ffffff;'><?php echo $shop_info['shop_name']; ?></span></div>
                	<div class="info2">
                	    <div class="star">
                	        <div style="height: 100%;overflow-x: hidden;width:<?php echo $shop_info['evaluationAvg']*20?>%;">
                	            <div class="star2"></div>
                	        </div>
                	    </div>
                	    <div class="mark"><?php echo $shop_info['evaluationAvg']?>分</div>
                	</div>
                	<?php if ($shop_info['label_image2']!=""&&$shop_info['label_image2']!=null) {?>
                	    <img src="<?php echo $shop_info['label_image2'] ?>" class="span-img">
                	<?php } ?>
                	<div class="action-group">
                	    <div class="col1 qr" >
                	        <div class="icon">
                	            <img src="images/icon_gray_qr.png">
                	        </div>
                	        <div class="title">商家二维码</div>
                	    </div>
                	    <div class="col2"><div class="icon"><span style='color:white'><?php echo $shop_info['collect_num'];?></span></div><div class="title">粉丝数</div></div>
                	    <div class="col3 fav"><div class="icon"><img src="images/icon_star_off.png"></div><div class="title">收藏</div></div>
                	    <div class="clear"></div>
                	</div>
                </div>
            </div>
        </div>
    </div>
    </div>
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
</script>
<!--店铺顶部信息-->

<!--视频-->
<script type="dot-template" id="type_con_10">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
    {{?it.content.video_link}}<img src="images/video_link.jpg" style="width=100%">
    {{??}}<img src="images/video.jpg" style="width=100%">
    {{?}}
</div>
</script>
<script type="dot-template" id="type_ctrl_10">
<div class="formitems" style="width:550px;">
                <label class="fi-name">视频地址：</label>
                <div class="form-controls">
                    <input type="text" name="video_link" class="input xlarge" value="{{=it.content.video_link}}" >
					<span class="videotips">
						目前只支持腾讯视频，请添加(通用代码)处以http开头的视频地址<br>
						如下 //v.qq.com/iframe/player.html?vid=f01980mc610&tiny=0&auto=0
					</span>
                    <span class="fi-help-text"></span>
                </div>
            </div>
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
</script>
<!--视频-->
<!--头部引导页-->
<script type="text/j-template" id="type_con_14">
<div class="con_display" {{? it.content.padding}}style="padding:{{= it.content.padding}}px 0;"{{?}}>
	<nav class="headguide_nav">
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
	</nav>
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
</script>
<!--头部引导页-->
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
<script charset="utf-8" src="js/region_select.js"></script>
<script charset="utf-8" src="../../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/global.js"></script>
<script type="text/javascript" src="../../../Common/js/Base/personalization/shop.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/lean-modal.min.js"></script>
<script type="text/javascript" src="../../../Common/js/Product/product/jquery.uploadify-3.1.min.js?ver=<?php echo rand(0,9999);?>"></script>
<script type="text/javascript" src="js/jquery.form.js"></script><!--ajaxform 插件-->
<script type="text/javascript" src="js/WdatePicker.js"></script><!--添加时间插件-->
<!--<script type="text/javascript" src="js/region_select.js"></script>--><!--选择地区插件-->
<script type="text/javascript" src="js/select_area.js"></script><!--选择地区插件-->
<!--<script src="//malsup.github.io/jquery.form.js"></script>-->
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
    typearr     =<?php echo json_encode($type_arr);?>; //一级分类
    ctypearr     =<?php echo json_encode($ctype_arr);?>; //子分类
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
	oldstr = $('#con_15_placeholder').val(); //已启用的模板
	// console.log(areaData);
    package_lists   =<?php echo json_encode($package_lists);?>;//固定连接数组
    product_lists   =<?php echo json_encode($product_lists);?>;//产品列表
    store_lists   	=<?php echo json_encode($store_lists);?>;//门店列表

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
        var module={
                id:null,//模块ID
                type:null,//模块类型
                sort:null, //排序
                content:null,//模块内容
                fixed_link:null,//固定
                type_arr:null,//产品一级分类
                ctype_arr:null,//产品子分类
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
				template_link:null//已启用的模板
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
                dataset:[]
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
         module.product_lists=product_lists;
         module.store_lists=store_lists;
         module.room_link=room_link;
         module.template_link=template_link;
         module.type_arr=typearr;
         module.ctype_arr=ctypearr;
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
            if(picUrl.indexOf("weixinpl")>0 || picUrl.indexOf("mshop")>0){ //判断图片路径
                module.content.dataset[j].pic=picUrl;
            }
            else{
                var defUrl=new_baseurl+"/weixinpl/common_shop/common/custom_temp/"+imgArr[j];
                module.content.dataset[j].pic=defUrl;
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