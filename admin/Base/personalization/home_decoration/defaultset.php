<?php
header("Content-type: text/html; charset=utf-8"); //test
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
require('../../../../../weixinpl/common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
$head=1; /*关于头部文件的定位*/
require('../../../../../weixinpl/proxy_info.php');
require('../../../../../weixinpl/auth_user.php');
require('../../../../../weixinpl/common/utility_4m.php');
_mysql_query("SET NAMES UTF8");

require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
$database = new \Key\DB();
$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);

$sql = "SELECT package_name,id from package_list_t where is_card_recharge=true and customer_id='{$customer_id}' and isvalid=true ";
$package_list = $database->getData($sql);


if(!empty($_SESSION['auth_style'])){
    $auth_style=explode(',',$_SESSION['auth_style']);
	if($_GET['default_set']==""){
	switch($auth_style[0]){
			case 1:
				header("Location: base.php?customer_id=".$customer_id_en."");exit;
			break;
			case 2:
				header("Location: fengge.php?customer_id=".$customer_id_en."");exit;
			break;
			case 4:
				header("Location: product.php?customer_id=".$customer_id_en."");exit;
			break;
			case 5:
				header("Location: order.php?customer_id=".$customer_id_en."");exit;
			break;
			case 6:
				header("Location: supply.php?customer_id=".$customer_id_en."");exit;
			break;
			case 7:
				header("Location: agent.php?customer_id=".$customer_id_en."");exit;
			break;
			case 8:
				header("Location: qrsell.php?customer_id=".$customer_id_en."");exit;
			break;
			case 9:
				header("Location: customers.php?customer_id=".$customer_id_en."");exit;
			break;
			case 10:
				header("Location: publicwelfare.php?customer_id=".$customer_id_en."");exit;
			break;
		}
	}
}
$query = "select id,template_id,index_bg,stock_remind,isOpenPublicWelfare from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$template_id=-1;
$index_bg = "";
$stock_remind = 1;
$isOpenPublicWelfare = 0;
while ($row = mysql_fetch_object($result)) {
	$template_id = $row->template_id;
	$index_bg = $row->index_bg;
	$stock_remind = $row->stock_remind;
	$isOpenPublicWelfare = $row->isOpenPublicWelfare;
}
if($template_id<0){
   $query ="insert weixin_commonshops(name,email,need_express,need_email,template_id,isvalid,customer_id,createtime) values('','',false,false,1,true,".$customer_id.",now())";
   _mysql_query($query);
   $template_id=1;
}

//产品分类
$typeLst = new ArrayList();
$query="select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $pt_id = $row->id;
   $pt_name = $row->name;

   $pstr = $pt_id."_".$pt_name;
   $typeLst->add($pstr);
}
$typesize = $typeLst->size();
//产品分类 End


//图文信息
$imginfoLst = new ArrayList();
$query = 'SELECT id,title FROM weixin_subscribes where isvalid=true and parent_id=-1 and is_message=0 and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	  $sub_id =  $row->id ;
	  $title = $row->title;

	  $pstr = $sub_id."_".$title;
      $imginfoLst->add($pstr);
}
$imginfosize = $imginfoLst->size();
//图文信息 End

//优惠券 start
$couponLst = new ArrayList();
//只有普通优惠券
$query = 'select id,is_open,title,NeedMoney,CanGetNum,Days,DaysType,class_type,user_scene from weixin_commonshop_coupons where isvalid=true and is_open=1  and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	  $coupon_id =  $row->id ;
	  $title = $row->title;

	  $cstr = $coupon_id."_".$title;
      $couponLst->add($cstr);
}
$couponsize = $couponLst->size();
//优惠券 end

/***********4M START ***********/
$u4m = new Utiliy_4m_new();//新方法

if($u4m->check_allow_4M_template($template_id)){

	$rearr = $u4m->is_4M_new($customer_id);
	//是4m分销
	$is_shopgeneral = $rearr[0]  ;
	//厂家编号
	$adminuser_id = $rearr[1] ;
	//是否是厂家总店
	$is_samelevel = $rearr[2] ;
	//总店模板编号
	$general_template_id = $rearr[3] ;
	//总店商家编号
	$general_customer_id = $rearr[4] ;

	//1：厂家总店； 2：代理商总店
	$owner_general = $rearr[5] ;
	//直属代理商编号
	$orgin_adminuser_id = $rearr[6] ;

	//var_dump($rearr);

	$getAllSubCustomers = $u4m->getAllSubCustomers_new($customer_id,2);//获取下级商家

	if($orgin_adminuser_id>0 and $is_shopgeneral and $general_template_id>0){ //厂家同步下级模板

	   $query="update weixin_commonshops set template_id=".$general_template_id." where customer_id in (".$getAllSubCustomers.")";
	   _mysql_query($query);
	   $arr = explode(',',$getAllSubCustomers);
	   foreach($arr as $k=>$v){
		   //每次操作都清空模板缓存 xj
		   clear_template_cache("/tmp/weixin_platform/$v");
	   }
	   //$template_id = $general_template_id;

	}
}

/***********4M END***********/



$op = "";
if(!empty($_GET["op"])){
   $op = $configutil->splash_new($_GET["op"]);
   if($op=="del"){
       //删除banner
	   $position = $configutil->splash_new($_GET["position"]);
	   $b_imgurl = $configutil->splash_new($_GET["b_imgurl"]);
       $query="select id,imgurl from weixin_commonshop_template_item_imgs where isvalid=true and template_id=".$template_id." and position=".$position." and customer_id=".$customer_id;
	   $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	   $ti_id=-1;
	   $imgurl_tmp = "";
	   while ($row = mysql_fetch_object($result)) {
		  $ti_id = $row->id;
		  $imgurl = $row->imgurl;
		  break;
	   }
	   $imgurlarr = explode("|*|",$imgurl);
	   $len = count($imgurlarr);
	   for($i=0;$i<$len;$i++){
	       $imgurl = $imgurlarr[$i];
		   if($imgurl!=$b_imgurl){
		       $imgurl_tmp = $imgurl_tmp.$imgurl;
		   }
		   if($i<$len-1){
		      $imgurl_tmp = $imgurl_tmp."|*|";
		   }
	   }
	   $query="update weixin_commonshop_template_item_imgs set imgurl='".$imgurl_tmp."' where id=".$ti_id;
	   _mysql_query($query);
	   //厂家删除属性，释放下级属性
	   if( $is_shopgeneral == 1 and $is_samelevel == 1  and $general_template_id>0){
		   $u4m->release_sub_template_item($customer_id,$ti_id,$template_id);
	   }

   }else if($op=="del_2"){
       $position = $configutil->splash_new($_GET["position"]);
	   $position++;
	   $query="select id,imgurl from weixin_commonshop_template_item_imgs where isvalid=true and template_id=".$template_id." and position=".$position." and customer_id=".$customer_id;
	   $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	   $ti_id=-1;
	   $imgurl_tmp = "";
	   while ($row = mysql_fetch_object($result)) {
		  $ti_id = $row->id;
		  $imgurl = $row->imgurl;
		  break;
	   }
	   $query="update weixin_commonshop_template_item_imgs set isvalid=false where id=".$ti_id;
	   _mysql_query($query);
	    //厂家删除属性，释放下级属性
	   if($is_shopgeneral == 1 and $is_samelevel == 1  and $general_template_id>0){
		     $u4m->release_sub_template_item($customer_id,$ti_id,$template_id);
	   }
   }
}


//新增客户
$new_customer_count =0;
//今日销售
$today_totalprice=0;
//新增订单
$new_order_count =0;
//新增推广员
$new_qr_count =0;

$nowtime = time();
$year = date('Y',$nowtime);
$month = date('m',$nowtime);
$day = date('d',$nowtime);

$cur_date = date('Y-m-d');
$cur_date_begin = $cur_date." 00:00:00";
$cur_date_end = $cur_date." 23:59:59";

//$query="select count(distinct batchcode) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$query="select count(distinct batchcode) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_order_count = $row->new_order_count;
   break;
}

// $query="select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=".$customer_id." and year(paytime)=".$year." and month(paytime)=".$month." and day(paytime)=".$day;
$query="select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=".$customer_id." and paytime>='".$cur_date_begin."' and paytime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $today_totalprice = $row->today_totalprice;
   break;
}
$today_totalprice = round($today_totalprice,2);

// $query="select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$query="select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=".$customer_id." and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_customer_count = $row->new_customer_count;
   break;
}

$query="select count(1) as new_qr_count from promoters where status=1 and  isvalid=true and customer_id=".$customer_id." and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_qr_count = $row->new_qr_count;
   break;
}

$is_distribution=0;//渠道取消代理商功能
//代理模式,分销商城的功能项是 266
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scdl' and c.id=cf.column_id";
$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_distribution=1;
}
$is_supplierstr=0;//渠道取消供应商功能
//供应商模式,渠道开通与不开通
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scgys' and c.id=cf.column_id";
$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_supplierstr=1;
}

//城市商圈，渠道开关
$is_cityarea       = 0;
$is_cityarea_count = 0;
$query="select count(1) as is_cityarea_count from customer_funs cf inner join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and (c.sys_name='商圈-美食' or c.sys_name='商圈-金融保险' or c.sys_name='商圈-酒店' or c.sys_name='商圈-ktv' or c.sys_name='商圈-线下商城' or c.sys_name='商圈-金融管理' or c.sys_name='商圈-教练服务')";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $is_cityarea_count = $row->is_cityarea_count;
}
if($is_cityarea_count>0){
   $is_cityarea = 1;
}

$is_cityarea_caterer = 0;
$is_cityarea_ktv     = 0;
$is_cityarea_hotel   = 0;
$is_cityarea_shop    = 0;
$is_cityarea_coach   = 0;
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
			$cityareaCatererLst->add($pstr);
		}
		$cityareaCaterersize = $cityareaCatererLst->size();
		//店铺数据 End
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
			$cityareaKTVLst->add($pstr);
		}
		$cityareaKTVsize = $cityareaKTVLst->size();
		//店铺数据 End
	}
	//城市商圈（KTV），渠道开关 End

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
			$cityareaHotelLst->add($pstr);
		}
		$cityareaHotelsize = $cityareaHotelLst->size();
		//店铺数据 End
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
			$cityareaShopLst->add($pstr);
		}
		$cityareaShopsize = $cityareaShopLst->size();
		//店铺数据 End
	}
	//城市商圈（线下商城），渠道开关 End
	//
	//城市商圈（金融管理），渠道开关
	$is_cityarea_finance = 0;
	$query="select count(1) as is_cityarea_finance from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-金融管理'";
	$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $is_cityarea_finance = $row->is_cityarea_finance;
	}
	//城市商圈（金融管理），渠道开关  END

	//城市商圈（教练服务），渠道开关
	$query="select count(1) as is_cityarea_coach from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-教练服务'";
	$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $is_cityarea_coach = $row->is_cityarea_coach;
	}
	//城市商圈（教练服务），渠道开关 End

	//艺人服务，渠道开关
	$is_yiren       = 0;
	$query="select count(1) as is_yiren from customer_funs cf inner join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-艺人服务'";
	$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $is_yiren = $row->is_yiren;
	}

	if($is_yiren>0){
	   $is_yiren= 1;
	}
	//艺人服务，渠道开关 END

}


/* 8.1分类 */
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
//已启用的自定义模板
$diy_template_link = array();
$query_open_tem = "SELECT id,name FROM weixin_commonshop_diy_template WHERE is_open=true AND isvalid=true AND customer_id=".$customer_id." ORDER BY id DESC";
$result_open_tem = _mysql_query($query_open_tem) or die('Query_open_tem failed:'.mysql_error());
while( $row_open_tem = mysql_fetch_object($result_open_tem) ){
	$diy_template_link[] = $row_open_tem->id ."_". $row_open_tem->name;
}
$diy_template_count = count($diy_template_link);	//模板数量

$is_f2c = 0;
/* 查看f2c系统渠道开关 create by hzq */
$query="select count(1) as is_f2c from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='F2C系统'";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $is_f2c = $row->is_f2c;
}
/* 查看f2c系统渠道开关 end */

$is_ticket = 0;  //票务系统开关
/* 查看票务系统开关 start */
$query="select count(1) as is_ticket from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='票务系统'";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $is_ticket = $row->is_ticket;
}
/* 查看票务系统开关 end */

$is_exchange = 0;  //满赠专区开关
/* 查看满赠专区开关 start */
$query="select count(1) as is_exchange from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='满赠活动'";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $is_exchange = $row->is_exchange;
}
/* 查看满赠专区开关 end */

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
<script type="text/javascript" src="../../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/m-style.css">
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">

<link href="../../../../back_commonshop/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/style.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/operamasks-ui.css" rel="stylesheet" type="text/css">
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentGreen.css">--><!--内容CSS配色·绿色-->
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentOrange.css">--><!--内容CSS配色·橙色-->
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentbgreen.css">--><!--内容CSS配色·蓝绿-->
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentGGreen.css">--><!--内容CSS配色·草绿-->

<script>var template_id = <?php echo $template_id; ?>;</script>

<script type="text/javascript" src="../../../Common/js/Base/personalization/jscolor/jscolor.js"></script><!--拾色器js-->
<script charset="utf-8" src="../../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/global.js"></script>
<script type="text/javascript" src="../../../Common/js/Base/personalization/shop.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/lean-modal.min.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/operamasks-ui.min.js"></script>
</head>
<script language="javascript">

  $(document).ready(shop_obj.home_init);
</script>

<script>



function changeProductType(selv){
  //var selv =  sel.value;
  //alert(selv);
//  alert('==selv='+selv);
  document.getElementById("div_products_2").style.display="none";
  var flag = selv.substring(selv.length-2,selv.length);
  if(flag == '_1'){
     //是产品分类
	 document.getElementById("div_products_2").style.display="block";
	 var pro_typeid= selv.substring(0,selv.indexOf("_1"));
	 url='get_product_list.php?callback=jsonpCallback_get_product_list&type_id='+pro_typeid;
     $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list'
	});
  }
}


function changeProductType_cat(selv){  //新增分类广告图链接
  //var selv =  sel.value;
  //alert(selv);
//  alert('==selv='+selv);

  document.getElementById("div_products_cat").style.display="none";
  var flag = selv.substring(selv.length-2,selv.length);
  if(flag == '_1'){
     //是产品分类
	 document.getElementById("div_products_cat").style.display="block";
	 var pro_typeid= selv.substring(0,selv.indexOf("_1"));
	 url='get_product_list.php?callback=jsonpCallback_get_product_list_cat&type_id='+pro_typeid;
     $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list_cat'
	});
  }
}

function changeProductType_cat_index(selv){  //新增分类首页显示图链接  11.26
  //var selv =  sel.value;
  //alert(selv);
//  alert('==selv='+selv);

  document.getElementById("div_products_cat_index").style.display="none";
  var flag = selv.substring(selv.length-2,selv.length);
  if(flag == '_1'){
     //是产品分类
	 document.getElementById("div_products_cat_index").style.display="block";
	 var pro_typeid= selv.substring(0,selv.indexOf("_1"));
	 url='get_product_list.php?callback=jsonpCallback_get_product_list_cat_index&type_id='+pro_typeid;
     $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list_cat_index'
	});
  }
}



function changeProductType_txt_orgi(selv){
  //var selv =  sel.value;
  //alert(selv);
    document.getElementById("div_products_3").style.display="none";
    var flag = selv.substring(selv.length-2,selv.length);
    if(flag == '_1'){
	 //console.log(selv);
     //是产品分类
	 document.getElementById("div_products_3").style.display="block";
	 var pro_typeid= selv.substring(0,selv.indexOf("_1"));
	 url='get_product_list.php?callback=jsonpCallback_get_product_list_txt&type_id='+pro_typeid;
     $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list_txt'
	});
  }
}




var p_detail_id = -1;
var p_detail_pos= -1;

function changeProductType2(pro_typeid,d_id){

	 p_detail_id = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=jsonpCallback_get_product_list2&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list2'
	});

 // }
}


 /*11.3增加*/
//window.onload=function(){
	/*$(document).ready(function (){

<?php
		$producttype_id=-1;
		if($_GET['producttype_id']){
			$producttype_id=$_GET['producttype_id'];
			$typeid_query="select cat_foreign_id,cat_detail_id from weixin_commonshop_types where id =".$producttype_id." and customer_id=".$customer_id."";
			$result_typeid=_mysql_query($typeid_query) or die ('typeid_query faild' .mysql_error());
			while($row=mysql_fetch_object($result_typeid)){
				$p_detail_id=$row->cat_detail_id;//产品ID
				$pro_typeid=$row->cat_foreign_id;//分类ID
				$pro_typeid_arr = explode('_',$pro_typeid);
				$pro_typeid = $pro_typeid_arr[0];
				if($pro_typeid){
					$pro_typeid_query="select count(1) as protypeid from weixin_commonshop_types where id=".$pro_typeid." and customer_id=".$customer_id."";
					$result_pro_typeid=_mysql_query($pro_typeid_query) or die ('pro_typeid_query faild' .mysql_error());
					while($row=mysql_fetch_object($result_pro_typeid)){

						$protypeid=$row->protypeid;
					}
				}

			}
		}
	if($p_detail_id && $pro_typeid){

	?>


		p_detail_id =<?php echo $p_detail_id?>;
		pro_typeid=<?php echo $pro_typeid;?>;
		 //是产品分类
		 url='get_product_list.php?callback=jsonpCallback_get_product_list_catindex&type_id='+pro_typeid;
		//未完成
		 $.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_get_product_list_catindex'
		});
		<?php }?>
});*/

$(document).ready(function(){  //分类首页图广告链接

		<?php
		$producttype_id=-1;
		if($_GET['producttype_id']){
			$producttype_id=$_GET['producttype_id'];
			$typeid_query="select cat_foreign_id,cat_detail_id,parent_id from weixin_commonshop_types where id =".$producttype_id." and customer_id=".$customer_id."";
			$result_typeid=_mysql_query($typeid_query) or die ('typeid_query faild' .mysql_error());
			while($row=mysql_fetch_object($result_typeid)){
				$p_detail_id=$row->cat_detail_id;//产品ID
				$pro_typeid=$row->cat_foreign_id;//分类ID
				$parent_id=$row->parent_id;
				$pro_typeid_arr = explode('_',$pro_typeid);
				$pro_typeid = $pro_typeid_arr[0];
				if($pro_typeid){
					$pro_typeid_query="select count(1) as protypeid from weixin_commonshop_types where id=".$pro_typeid." and customer_id=".$customer_id."";
					$result_pro_typeid=_mysql_query($pro_typeid_query) or die ('pro_typeid_query faild' .mysql_error());
					while($row=mysql_fetch_object($result_pro_typeid)){

						$protypeid=$row->protypeid;
					}
				}

			}
		}

	if($p_detail_id && $pro_typeid && $parent_id <0){

	?>


		p_detail_id =<?php echo $p_detail_id?>;
		pro_typeid=<?php echo $pro_typeid;?>;
		//alert(p_detail_id);
		//alert(pro_typeid);
		 //是产品分类
		 url='get_product_list.php?callback=jsonpCallback_get_product_list_catindex&type_id='+pro_typeid;
		//未完成
		 $.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_get_product_list_catindex'
		});
		<?php }?>

});

$(document).ready(function(){  //分类首页显示图

		<?php
			$producttype_id=-1;
			if($_GET['producttype_id']){
				$producttype_id=$_GET['producttype_id'];
				$typeid_query="select cat_foreign_id,cat_detail_id,parent_id from weixin_commonshop_types where id =".$producttype_id." and customer_id=".$customer_id."";
				$result_typeid=_mysql_query($typeid_query) or die ('typeid_query faild' .mysql_error());
				while($row=mysql_fetch_object($result_typeid)){
					$p_detail_id=$row->cat_detail_id;//产品ID
					$pro_typeid=$row->cat_foreign_id;//分类ID
					$parent_id=$row->parent_id;
					$pro_typeid_arr = explode('_',$pro_typeid);
					$pro_typeid = $pro_typeid_arr[0];
					if($pro_typeid){
						$pro_typeid_query="select count(1) as protypeid from weixin_commonshop_types where id=".$pro_typeid." and customer_id=".$customer_id."";
						$result_pro_typeid=_mysql_query($pro_typeid_query) or die ('pro_typeid_query faild' .mysql_error());
						while($row=mysql_fetch_object($result_pro_typeid)){

							$protypeid=$row->protypeid;
						}
					}

				}
			}
		if($p_detail_id && $pro_typeid && $parent_id > 0){

		?>


			p_detail_id =<?php echo $p_detail_id?>;
			pro_typeid=<?php echo $pro_typeid;?>;

			//alert(p_detail_id);
			//alert(pro_typeid);
			 //是产品分类
			 url='get_product_list.php?callback=jsonpCallback_get_product_list_catindex_index&type_id='+pro_typeid;
			//未完成
			 $.jsonp({
				url:url,
				callbackParameter: 'jsonpCallback_get_product_list_catindex_index'
			});
			<?php }?>

});


function changeProductType_txt(pro_typeid,d_id){

	 p_detail_id = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=jsonpCallback_get_product_list_txt&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list_txt'
	});

 // }
}

function changeProductType3(pro_typeid,d_id,m){

     if(m>0){

		 p_detail_id = d_id;
		 p_detail_pos = m;
		 //是产品分类
		 url='get_product_list.php?callback=jsonpCallback_get_product_list3&type_id='+pro_typeid+'&pos='+p_detail_pos+'&pid='+p_detail_id;
		 $.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_get_product_list3'
		});
	}
 // }
}


function jsonpCallback_get_product_list2(results){
   var len = results.length;

   var sel_pro = document.getElementById("product_detail_id_2");
   sel_pro.options.length=0;

    var new_option = new Option("---请选择一个产品---",-1);
    sel_pro.options.add(new_option);
   for(i=2;i<len;i++){
      var pid = results[i].pid;
	  var pname = results[i].pname;

	  var new_option = new Option(pname,pid);
       sel_pro.options.add(new_option);
	  if(pid==p_detail_id){
	     new_option.selected=true;
	  }
   }

}


/*11.3增加分类产品图片链接*/
function jsonpCallback_get_product_list_catindex(results){
   var len = results.length;

   if(template_id==31 || template_id==38 || template_id==41 || template_id==43 || template_id==45 || template_id==46 || template_id==49 || template_id==56){
	   var sel_pro = document.getElementById("product_detail_id_cat");
   }else if(template_id==47 || template_id==48 || template_id==50 || template_id==55){
	   var sel_pro = document.getElementById("product_detail_id_cat_index");
   }

   sel_pro.options.length=0;

    var new_option = new Option("---请选择一个产品---",-1);
    sel_pro.options.add(new_option);
   for(i=2;i<len;i++){
      var pid = results[i].pid;
	  var pname = results[i].pname;
	  // console.log(pid);
	  var new_option = new Option(pname,pid);
       sel_pro.options.add(new_option);
	  if(pid==p_detail_id){
	     new_option.selected=true;
	  }
   }

}

/*11.26增加分类首页图片链接*/
function jsonpCallback_get_product_list_catindex_index(results){
   var len = results.length;

   if(template_id==31 || template_id==38 || template_id==41 || template_id==43 || template_id==45 || template_id==46 || template_id==49 || template_id==56){
	   var sel_pro = document.getElementById("product_detail_id_cat");
   }else if(template_id==47 || template_id==48 || template_id==50 || template_id==55){
	   var sel_pro = document.getElementById("product_detail_id_cat_index");
   }
   sel_pro.options.length=0;

    var new_option = new Option("---请选择一个产品---",-1);
    sel_pro.options.add(new_option);
   for(i=2;i<len;i++){
      var pid = results[i].pid;
	  var pname = results[i].pname;

	  var new_option = new Option(pname,pid);
       sel_pro.options.add(new_option);
	  if(pid==p_detail_id){
	     new_option.selected=true;
	  }
   }

}


function jsonpCallback_get_product_list3(results){
   var len = results.length;
   // alert('p_detail_pos======'+p_detail_pos);
   var pos = results[0].pos;
   var did = results[1].pid;
   var sel_pro = document.getElementById("product_detail_id_1_"+pos);
   sel_pro.options.length=0;

    var new_option = new Option("---请选择一个产品---",-1);
    sel_pro.options.add(new_option);
   for(i=2;i<len;i++){
      var pid = results[i].pid;
	  var pname = results[i].pname;

	  var new_option = new Option(pname,pid);
       sel_pro.options.add(new_option);
	  if(pid==did){
	     new_option.selected=true;
	  }
   }

}

</script>
<script language="javascript">

  <?php
	$general_slider_num=5;
	$logo_url="";
	$img_1="";
	$img_2="";
	$img_3="";
	$img_4="";
	$img_5="";
	$img_6="";
	$img_7="";
	$img_8="";
	$img_9="";
	$img_10="";
	$img_11="";
	$img_12="";
	$img_13="";
	$img_14="";
	$img_15="";

	$id				=	-1;
	$imgurl			=	'';
	$url			=	'';
	$linktype 		= 	'';
	$foreign_id 	= 	'';
	$title 			= 	'';
	$detail_id 		= 	'';
	$video_link		=	'';
	$general_imgurl	=	'';


  //先取默认图片
  echo "var shop_skin_data=[";
  $query="select id,imgurl,default_imgurl,contenttype,title,width,height,position,needlink,url,linktype,foreign_id from weixin_commonshop_template_imgs where isvalid=true  and template_id=".$template_id;

  $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
  while ($row = mysql_fetch_object($result)) {
	  $id=$row->id;
	  $imgurl=$row->imgurl;
	  file_put_contents($_SERVER['DOCUMENT_ROOT']."/DEFAULTSET.txt",'1111'.var_export($imgurl,true).PHP_EOL,FILE_APPEND);
	  $position 		= 	$row->position;
	  $contenttype		=	$row->contenttype;
	  $title			= 	$row->title;
	  $width 			= 	$row->width;
	  $height 			=	$row->height;
	  $needlink 		= 	$row->needlink;
	  $url				=	$row->url;
	  $linktype 		= 	$row->linktype;
	  $foreign_id 		= 	$row->foreign_id;
	  $default_imgurl	=	$row->default_imgurl;
	  $detail_id=-1;
	  $imgurl = $default_imgurl;
	  file_put_contents($_SERVER['DOCUMENT_ROOT']."/DEFAULTSET.txt",'11222'.var_export($imgurl,true).PHP_EOL,FILE_APPEND);
	  //如果客户已经替换了图片，则用客户的图片

	  /* 4m属性 start*/
	 $is_4m				=	0;
	 $template_4m_id	=	-1;
	 /* 4m属性 end*/
	  $query2="select id,imgurl,position,url,linktype,foreign_id,title,detail_id,video_link,is_4m,template_4m_id,sel_link_type  from weixin_commonshop_template_item_imgs where isvalid=true  and template_id=".$template_id." and customer_id=".$customer_id." and position=".$position." order by id desc limit 1";
	  $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());

	  while ($row2 = mysql_fetch_object($result2)) {

		  $id				=	$row2->id;
		  $imgurl			=	$row2->imgurl;
		  file_put_contents($_SERVER['DOCUMENT_ROOT']."/DEFAULTSET.txt",'33333'.var_export($imgurl,true).PHP_EOL,FILE_APPEND);
		  $url				=	$row2->url;
		  $linktype 		= 	$row2->linktype;
		  $foreign_id 		= 	$row2->foreign_id;
		  $title 			= 	$row2->title;
		  $detail_id 		= 	$row2->detail_id;
		  $video_link		=	$row2->video_link;
		  $sel_link_type	=	$row2->sel_link_type;
		   /* 4m属性 start*/
		  $is_4m			=	$row2->is_4m;
		  $template_4m_id	=	$row2->template_4m_id;
		   /* 4m属性 end*/
	  }

	  //如果客户已经加了字体颜色
	  $query3="select font_color from weixin_commonshop_type_font where isvalid=true and template_id=".$template_id." and font_id=".$id;
	  $result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
	  $font_color="000000";
	  while ($row3 = mysql_fetch_object($result3)) {
		   $font_color=$row3->font_color;
	  }

	  switch($position){
		  case 1:
			$img_1 = $imgurl;
			break;
		  case 2:
			$img_2 = $imgurl;
			break;
		  case 3:
			$img_3 = $imgurl;
			break;
		  case 4:
		   $img_4 = $imgurl;
			break;
		  case 5:
			$img_5 = $imgurl;
			break;
		  case 6:
			$img_6 = $imgurl;
			break;
		 case 7:
			$img_7 = $imgurl;
			break;
		  case 8:
			$img_8 = $imgurl;
			break;
		  case 9:
			$img_9 = $imgurl;
			break;
		  case 10:
			$img_10 = $imgurl;
			break;
		  case 11:
			$img_11 = $imgurl;
			break;
		  case 12:
			$img_12 = $imgurl;
			break;
		  case 13:
			$img_13 = $imgurl;
			break;
		  case 14:
			$img_14 = $imgurl;
			break;
		  case 15:
			$img_15 = $imgurl;
			break;

	  }




	  echo "{\"PId\":\"".$id."\"";
	  echo ",\"SId\":\"".$template_id."\"";
	  echo ",\"is_4m\":\"".$is_4m."\"";
	  echo ",\"MemberId\":\"".$customer_id."\"";
	  echo ",\"ContentsType\":\"".$contenttype."\"";
	  echo ",\"Title\":\"".$title."\"";
	  echo ",\"Video_link\":\"".$video_link."\"";
	  //输出文字颜色
	  echo ",\"font_color\":\"".$font_color."\"";


	  /******* 4M START *******/

	if($u4m->check_allow_4M_template($template_id)){

		if($is_shopgeneral == 1  and $general_customer_id>0 and $is_samelevel==0){	//非厂家总店



			//查找厂家总店的幻灯片
			/*$is_4m = 0;
			$template_4m_id = -1;
			$query2="select id,imgurl,is_4m,template_4m_id from weixin_commonshop_template_item_imgs where isvalid=true  and template_id=".$template_id." and customer_id=".$customer_id." and position=".$position."  limit 1";
			//echo $query2;
			$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
			while ($row2 = mysql_fetch_object($result2)) {
				  $general_imgurl	=	$row2->imgurl;
				  $is_4m			=	$row2->is_4m;
				  $template_4m_id	=	$row2->template_4m_id;
			}*/

			if($is_4m){
				if($template_4m_id>0){
					$query3 = "select content,imgurl,url,foreign_id,linktype,sel_link_type from weixin_commonshop_4m_template where isvalid=true and id=".$template_4m_id."";
				   // echo $query3;
					$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
					while ($row3 = mysql_fetch_object($result3)) {
						  $Fac_imgurl		 =	$row3->imgurl;			//产家图片、轮播图
						  $Fac_url			 =	$row3->url;				//产家轮播图链接设置
						  $Fac_foreign_id	 =	$row3->foreign_id;		//产家轮播图链接设置
						  $Fac_linktype		 =	$row3->linktype;		//
						  $Fac_sel_link_type =	$row3->sel_link_type;	//链接类型，1：选择的链接，2:填写的链接
					}
				}
				if($contenttype==1){	//轮播图
					 if(!empty($Fac_imgurl)){
						 $imgurl 		= $u4m->auto_fill_str($imgurl,4,'|*|').'|*|'.$Fac_imgurl;
						 $url 			= $u4m->auto_fill_str($url,4,'|*|').'|*|'.$Fac_url;
						 $foreign_id 	= $u4m->auto_fill_str($foreign_id,4,'|*|').'|*|'.$Fac_foreign_id;
						 $linktype 		= $u4m->auto_fill_str($linktype,4,'|*|').'|*|'.$Fac_linktype;
						 $sel_link_type = $u4m->auto_fill_str($sel_link_type,4,'|*|').'|*|'.$Fac_sel_link_type;
					 }
					 file_put_contents($_SERVER['DOCUMENT_ROOT']."/DEFAULTSET.txt",'222113'.var_export($imgurl,true).PHP_EOL,FILE_APPEND);
				}else{					//普通图片
				   if(!empty($Fac_imgurl)){
						 $imgurl = $Fac_imgurl;
						 file_put_contents($_SERVER['DOCUMENT_ROOT']."/DEFAULTSET.txt",'334243'.var_export($imgurl,true).PHP_EOL,FILE_APPEND);
					 }
				}
				echo ",\"Fac_imgurl\":\"".$Fac_imgurl."\"";
				echo ",\"Fac_url\":\"".$Fac_url."\"";
				echo ",\"Fac_foreign_id\":\"".$Fac_foreign_id."\"";
				echo ",\"Fac_sel_link_type\":\"".$Fac_sel_link_type."\"";
			}
		}
	}

	/******* 4M END *******/

	  echo ",\"ImgPath\":\"".$imgurl."\""; 
	  echo ",\"Url\":\"".$url."\"";
	  echo ",\"linktype\":\"".$linktype."\"";
	  echo ",\"foreign_id\":\"".$foreign_id."\"";
	  echo ",\"detail_id\":\"".$detail_id."\"";
	  echo ",\"Postion\":\"".$position."\"";
	  echo ",\"Width\":\"".$width."\"";
	  echo ",\"Height\":\"".$height."\"";
	  echo ",\"NeedLink\":\"".$needlink."\"";
	  echo ",\"sel_link_type\":\"".$sel_link_type."\"";
	  echo "},";
  }
  echo "];";

//   echo "console.log(shop_skin_data);";
//V7.0分类新排序
/*$sort_str="";
$type_sort="select sort_str from weixin_commonshop_type_sort where  customer_id=".$customer_id."";
$result_type=_mysql_query($type_sort) or die ('type_sort faild' .mysql_error());
while($row=mysql_fetch_object($result_type)){
   $sort_str=$row->sort_str;
}*/

  ?>

</script>


<body>
<!--内容框架开始-->
<div class="WSY_content">

<!--微商城统计代码开始-->
<!--<div class="WSY_statisticsbox">
	<li><a>今日订单：</a><span>13</span></li>
    <li><a>今日销售：</a><span style="color:#F00">￥13000</span></li>
    <li><a>新增客户：</a><span>0</span></li>
    <li><a>新增推广员：</a><span>0</span></li>
</div>-->
<!--微商城统计代码结束-->

<style type="text/css">
<?php
	switch($theme){

		case blue:
		echo "		.input_butn{margin-top:30%}
		.input_butn input{display:block;width:192px;background:#06a7e1;border:solid 1px #0b91c2;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
		.input_butn input:hover{background:#017ca9;cursor:pointer;}
		.input_butn01 input{width:220px;}
		.leftA01 .leftA01_dl dd .tj{background:#07a7e1;border:solid 1px #0b91c2;color:#fff;}
		.leftA01 .leftA01_dl dd .tj:hover{background:#0b91c2;}
		.WSY_homeright .WSY_homeright_nav li .blueAA{background:#06a7e1;color:#fff;}";
		break;

		case Green:
		echo ".input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#32b16c;border:solid 1px #0e9f50;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#0e9f50;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#32b16c;border:solid 1px #0e9f50;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#0e9f50;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#0e9f50;color:#fff;}";
		break;

		case Orange:
		echo ".input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#e74f31;border:solid 1px #d43d1f;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#d43d1f;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#e74f31;border:solid 1px #d43d1f;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#d43d1f;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#e74f31;color:#fff;}";
		break;

		case bgreen:
		echo ".input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#0faa9a;border:solid 1px #20b3a4;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#20b3a4;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#0faa9a;border:solid 1px #20b3a4;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#20b3a4;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#20b3a4;color:#fff;}";
		break;

		case GGreen:
		echo ".input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#096733;border:solid 1px #146e3c;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#146e3c;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#096733;border:solid 1px #146e3c;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#146e3c;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#096733;color:#fff;}";
		break;



	}

?>
/*蓝色*/
/*.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#06a7e1;border:solid 1px #0b91c2;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#017ca9;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#07a7e1;border:solid 1px #0b91c2;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#0b91c2;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#06a7e1;color:#fff;}
*/
/*绿色*/
/*.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#32b16c;border:solid 1px #0e9f50;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#0e9f50;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#32b16c;border:solid 1px #0e9f50;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#0e9f50;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#0e9f50;color:#fff;}*/

/*橙色*/
/*.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#e74f31;border:solid 1px #d43d1f;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#d43d1f;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#e74f31;border:solid 1px #d43d1f;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#d43d1f;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#e74f31;color:#fff;}*/

/*蓝绿色*/
/*.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#0faa9a;border:solid 1px #20b3a4;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#20b3a4;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#0faa9a;border:solid 1px #20b3a4;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#20b3a4;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#20b3a4;color:#fff;}*/

/*草绿色*/
/*.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#096733;border:solid 1px #146e3c;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#146e3c;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#096733;border:solid 1px #146e3c;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#146e3c;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#096733;color:#fff;}*/

/*其他css*/
.moveLeft{margin-left: 18px;}
</style>
       <!--列表内容大框开始-->
	<div class="WSY_columnbox">

       <!--弹窗开始-->
    <style>
     .tips-pop{width:100%;height:100%;top:0;left:0;position:fixed;z-index:11;display:flex;justify-content: center;align-items:center;background:rgba(0,0,0,0.5);display:none;}
     .tips-content{position:absolute;width:880px;background:#FFF;padding:0 30px;display:inline-block;}
     .tips-title{height:63px;line-height:63px;text-align:center;border-bottom:1px solid #d1d1d1;font-size:16px;margin-bottom:13px;position:relative;}
     .tips-title img{position:absolute;right:0;top:20px;}
     .WSY_search_div{overflow:hidden;}
     .WSY-skin-bg{border:none;}
     .WSY_search_div li{float:left;margin-top:15px;margin-right:10px;}
     .WSY_search_div input{width:200px;height:26px;line-height:20px;border:1px solid #BBBBBB;border-radius:2px;margin-left:8px;margin-right:12px;}
     .WSY_search_div select{width:200px;height:26px;line-height:20px;border:1px solid #BBBBBB;border-radius:2px;color:#646464;margin-left:8px;margin-right:12px;}
     .WSY_bottonliss{width: 100px;}
     .WSY_search_div .WSY-skin-bg{width: 100px;height: 30px;font-family: 'Arial Normal',Arial;font-weight: 400;font-style: normal;font-size: 14px;text-decoration: none;color: #fff;text-align: center;border-radius: 3px;line-height: 30px;cursor: pointer;}
     .WSY_table{margin-bottom:15px;margin-left:0;margin-top:20px;}
     .WSY_table td{color:#646464;}
     .WSY_button{margin-bottom:30px;float:left;margin-left:10px;margin-top:20px;}
     </style>






    <!--弹窗结束-->


    	<!--列表头部切换开始-->
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/personalization/basic_head.php");
		?>
        <!--列表头部切换结束-->

		<!--首页设置代码开始-->
		<div class="WSY_data" id="home">
			<div class="WSY_homebox">
				<div class="WSY_homeleft">
					<li class="WSY_homeleft_top">
						<p></p>
					</li>
					<li class="WSY_homeleft_middle">
					<!-- 开发区域 -->
   <script type="text/javascript">
    var skin_index_init=function(){
	   $('#shop_skin_index .menu .nav a.category').click(function(){
		if($('#category').height()>$(window).height()){
			$('html, body, #cover_layer').css({
				height:$('#category').height(),
				width:$(window).width(),
				overflow:'hidden'
			});
		}else{
			$('#category, #cover_layer').css('height', $(window).height());
			$('html, body').css({
				height:$(window).height(),
				overflow:'hidden'
			});
		}
		$('#cover_layer').show();
		$('#category').animate({left:'0%'}, 500);
		$('#shop_page_contents').animate({margin:'0 -70% 0 70%'}, 500);
		window.scrollTo(0);
		return false;
	});
}
</script>

<?php
  if($template_id==1){
?>
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/index.css" rel="stylesheet" type="text/css">


 <div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="header">
    	<div class="shop_skin_index_list logo" rel="edit-t01" no="0">
        	<div class="img">
			  <img src="<?php echo $img_1; ?>">
			</div>
			<div class="mod" style="display: none;">&nbsp;</div>
        </div>
        <div class="login"><a href="#" style="cursor: default; text-decoration: none;">个人中心</a></div>
        <div class="clear"></div>
        <div class="search">
            <form action="#" method="get">
                <input type="text" name="Keyword" class="input" value="" placeholder="输入商品名称...">
                <input type="submit" class="submit" value=" ">
            </form>
        </div>
    </div>
    <div class="shop_skin_index_list banner" rel="edit-t02" no="1">
        <div class="img"><img src="<?php echo $img_2; ?>"></div>
		<div class="mod" style="display: none;">&nbsp;</div>
    </div>
    <div class="menu">

    	<ul class="nav">
        	<li>
			 	<a href="#" class="category" style="cursor: default; text-decoration: none;">

					<div class="shop_skin_index_list" rel="edit-t07" no="6" iscate="1">
					<div class="img"></div>
					</div>
                    <div class="name shop_skin_index_list" rel="edit-t11" no="10">
						<div class="div_typename"></div>
					</div>
                </a>
            </li>
        	<li>
            	<a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t08" no="7"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t12" no="11">
						<div class="div_typename"></div>
					</div>
                </a>
            </li>
        	<li>
            	<a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t09" no="8"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t13" no="12">
						<div class="div_typename"></div>
					</div>
                </a>
            </li>
        	<li>
            	<a href="#" style="cursor: default; text-decoration: none;">
                	<div class="shop_skin_index_list" rel="edit-t10" no="9"  iscate="1">
					  <div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t14" no="13">
						<div class="div_typename"></div>
					</div>
                </a>
            </li>
        </ul>
        <div class="blank9"></div>
        <ul class="ad">
        	<li><div class="shop_skin_index_list" rel="edit-t03" no="2">
			<div class="img"><img src="<?php echo $img_3; ?>"></div>
			<div class="mod" style="display: none;">&nbsp;</div>
			</div>
			</li>
        	<li><div class="shop_skin_index_list" rel="edit-t04" no="3"><div class="img"><img src="<?php echo $img_4; ?>"></div><div class="mod" style="display: none;">&nbsp;</div></div></li>
        	<li><div class="shop_skin_index_list" rel="edit-t05" no="4"><div class="img"><img src="<?php echo $img_5; ?>"></div><div class="mod" style="display: none;">&nbsp;</div></div></li>
        </ul>
        <div class="clear"></div>
    </div>
    <div class="line"></div>
    <div class="box">
        <div class="shop_skin_index_list ad" rel="edit-t06" no="5">
			<div class="img"><img src="<?php echo $img_6; ?>"></div>
			<div class="mod" style="display: none;">&nbsp;</div>
			<div id="SetHomeCurrentBox" style="height: 190px; width: 193px;"></div>
		</div>
        <div class="ad_r">
        	<div class="item">
                <a href="#" style="cursor: default; text-decoration: none;">
					<div class="img" style="heihgt:90px;width:90px;background:#999;">
						<span style="text-align:center;line-height:80px;height:50px;">产品图片</span>
					</div>
					<div class="name">产品名称</div>
                </a>

            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>

<?php }else if($template_id==2){?>
  <link href="../../../Common/css/Base/home_decoration/lingshi/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/lingshi/css/index.css" rel="stylesheet" type="text/css">
  <div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="header">
        <div class="shop_skin_index_list logo" rel="edit-t01" no="0">
            <div class="img"><img src="logo.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
            <div id="SetHomeCurrentBox" style="height: 40px; width: 120px;"></div>
		</div>
        <div class="search">
            <form action="" method="get">
                <input type="text" name="Keyword" class="input" value="" placeholder="输入商品名称...">
                <input type="submit" class="submit" value=" ">
            </form>
        </div>
    </div>
    <div class="menu">
    	<ul>
        	<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t04" no="3"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t08" no="7">
						<div class="div_typename"></div>
					</div>
                </a>
			</li>
        	<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t05" no="4"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t09" no="8">
						<div class="div_typename"></div>
					</div>
                </a>
			</li>
			<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t06" no="5"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t10" no="9">
						<div class="div_typename"></div>
					</div>
                </a>
			</li>

        	<li>
			 <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t07" no="6"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t11" no="10">
						<div class="div_typename"></div>
					</div>
                </a>
			</li>

        </ul>
        <div class="clear"></div>
    </div>
    <div class="box">
        <div class="shop_skin_index_list banner" rel="edit-t02" no="1">
            <div class="img"><img src="banner.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
        </div>
        <div class="blank3"></div>
		            <div class="item">
                <a href="#" style="cursor: default; text-decoration: none;">
                    <div class="img"><img src=""></div>
					<strong>aa</strong>
					<span>￥0.00</span>
                </a>
            </div>
                <div class="clear"></div>
        <div class="shop_skin_index_list a0" rel="edit-t03" no="2">
            <div class="img"><img src="a0.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
        </div>
        <div class="a1">
                </div>
        <div class="clear"></div>
                <div class="clear"></div>
      </div>
   </div>


<?php }else if($template_id==3){?>
  <link href="../../../Common/css/Base/home_decoration/bao/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/bao/css/index.css" rel="stylesheet" type="text/css">
	<div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="header">
			<div class="search">
				 <form action="" method="get">
					<input type="text" name="Keyword" class="input" value="" placeholder="输入商品名称...">
					<input type="submit" class="submit" value=" ">
				</form>
			</div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
			<div class="img"><img src="banner.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		<div id="SetHomeCurrentBox" style="height: 130px; width: 302px;"></div></div>
		<div class="shop_skin_index_list a0" rel="edit-t02" no="1">
			<div class="img"><img src="ad-0.jpg"></div><div class="mod">&nbsp;</div>
		</div>
		<div class="box">
			<ul>
				<li>
					<div class="shop_skin_index_list" rel="edit-t03" no="2">
						<div class="img"><img src="ad-1.jpg"></div><div class="mod">&nbsp;</div>
					</div>
				</li>
				<li>
					<div class="shop_skin_index_list" rel="edit-t04" no="3">
						<div class="img"><img src="ad-2.jpg"></div><div class="mod">&nbsp;</div>
					</div>
				</li>
				<li>
					<div class="shop_skin_index_list" rel="edit-t05" no="4">
						<div class="img"><img src="ad-3.jpg"></div><div class="mod">&nbsp;</div>
					</div>
				</li>
				<li>
					<div class="shop_skin_index_list" rel="edit-t06" no="5">
						<div class="img"><img src="ad-4.jpg"></div><div class="mod">&nbsp;</div>
					</div>
				</li>
			</ul>
			<div class="clear"></div>
		</div>
		<div class="shop_skin_index_list a0" rel="edit-t07" no="6">
			<div class="img"><img src="ad-0.jpg"></div><div class="mod">&nbsp;</div>
		</div>
	</div>
<?php }else if($template_id==4){?>
  <link href="../../../Common/css/Base/home_decoration/fushi/css/shop.css" rel="stylesheet" type="text/css">
    <link href="../../../Common/css/Base/home_decoration/fushi/css/index.css" rel="stylesheet" type="text/css">
	<link href="../../../Common/css/Base/home_decoration/fushi/css/products.css" rel="stylesheet" type="text/css">

	<link href="../../../../back_commonshop/fushi/css/products_media.css" rel="stylesheet" type="text/css">
	<div id="shop_skin_index">
	<div  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
			<div class="img"><img src="e1e3dac757.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		<div id="SetHomeCurrentBox" style="height: 150px; width: 310px;"></div></div>

	</div>
	<div id="index_prolist">
		<div class="shop_skin_index_list" rel="edit-t02" no="1">
			<h1 class="div_typename">新品上市</h1>
			<div class="mod">&nbsp;</div>
		</div>

		<div id="products">
			<div class="list-1 category">
							<div class="item m_lefter">
					<ul>
						<li class="img"><a href="#" style="cursor: default; text-decoration: none;"><img src=""></a></li>
						<li class="name"><a href="#" style="cursor: default; text-decoration: none;">aa</a><span>￥0</span></li>
					</ul>
				</div>
									</div>
		</div>
	</div>
	</div>
<?php }else if($template_id==5){?>
    <link href="../../../Common/css/Base/home_decoration/huazhuang/css/shop.css" rel="stylesheet" type="text/css">
    <link href="../../../Common/css/Base/home_decoration/huazhuang/css/index.css" rel="stylesheet" type="text/css">
	<link href="../../../Common/css/Base/home_decoration/huazhuang/css/products.css" rel="stylesheet" type="text/css">


	<div id="shop_skin_index"   <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="header">
			<div class="search">
				 <form action="" method="get">
					<input type="text" name="keyword" class="input" value="" placeholder="输入商品名称...">
					<input type="submit" class="submit" value=" ">
				</form>
			</div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
			<div class="img"><img src="banner.jpg"></div><div class="mod">&nbsp;</div>
		<div id="SetHomeCurrentBox" style="height: 150px; width: 310px;"></div></div>
		<div class="clear"></div>
		<div class="index_h">
			<div class="l">热销推荐</div>
			<div class="r"><a href="##" style="cursor: default; text-decoration: none;"><img src="../../../Common/css/Base/home_decoration/huazhuang/images/r.jpg"></a></div>
		</div>
		<div class="shop_skin_index_list i0" rel="edit-t02" no="1">
			<div class="img"><img src="i1.jpg"></div><div class="mod">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list i1" rel="edit-t03" no="2">
			<div class="img"><img src="i2.jpg"></div><div class="mod">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list i0" rel="edit-t04" no="3">
			<div class="img"><img src="i3.jpg"></div><div class="mod">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list i2" rel="edit-t05" no="4">
			<div class="img"><img src="i4.jpg"></div><div class="mod">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list i2" rel="edit-t06" no="5">
			<div class="img"><img src="i5.jpg"></div><div class="mod">&nbsp;</div>
		</div>
	</div>
	<div id="index_prolist">
		<div class="index_h">
			<div class="l">最新产品</div>
			<div class="r"><a href="#" style="cursor: default; text-decoration: none;">
			<img src="../../../Common/css/Base/home_decoration/huazhuang/images/r.jpg"></a>
			</div>
		</div>
		<div id="products">
			<div class="list-0">
								<a href="##" style="cursor: default; text-decoration: none;">
						<div class="item">
							<div class="img"><img src=""></div>
							<div class="info">
								<h1>aa</h1>
								<h2>￥0</h2>
								<h3></h3>
							</div>
							<div class="detail"><span></span></div>
						</div>
					</a>
									</div>
		</div>
	</div>
<?php }else if($template_id==6){?>
     <link href="../../../Common/css/Base/home_decoration/huazhuang2/css/shop.css" rel="stylesheet" type="text/css">
    <link href="../../../Common/css/Base/home_decoration/huazhuang2/css/index.css" rel="stylesheet" type="text/css">
	<style>
		#shop_skin_index .menu{height:80px;width:100%;float:left; border-top:1px solid #d1d1c9; border-bottom:2px solid #dedbd2; background:#fcfbf9; overflow:hidden;}
		#shop_skin_index .menu li{width:33%; height:80px; overflow:hidden; float:left; box-sizing:border-box; border-left:1px solid #e2e1df;}
		#shop_skin_index .menu li a{display:block; width:100%; height:80px; line-height:135px; overflow:hidden; text-align:center;}
		#shop_skin_index .menu  li  .name{height:20px; line-height:20px; text-align:center;}
		#shop_skin_index .menu  li  .img{height:60px;}
		#shop_skin_index .imgs img{width:38px;height:38px;}
	</style>
	   <div id="shop_skin_index"   <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
			<div class="img"><img src="huazhuang2/fengge47/images/banner.jpg"></div>
			<div class="mod" style="display: none;">&nbsp;</div>
		   <div id="SetHomeCurrentBox" style="height: 150px; width: 310px;">
		</div></div>
		 <div class="menu">
				<ul>
					<li>
					   <a href="#" style="cursor: default; text-decoration: none;" >
							<div class="shop_skin_index_list" rel="edit-t07" no="6" style="float:none;">
							<div class="img imgs"></div>
							</div>
							<div class="name shop_skin_index_list" rel="edit-t10" no="9" style="float:none;">
								<div class="div_typename"></div>
							</div>
						</a>
					</li>
					<li>
					   <a href="#" style="cursor: default; text-decoration: none;" >
							<div class="shop_skin_index_list" rel="edit-t08" no="7" style="float:none;">
							<div class="img imgs"></div>
							</div>
							 <div class="name shop_skin_index_list" rel="edit-t11" no="10" style="float:none;">
								<div class="div_typename"></div>
							</div>
						</a>
					</li>
					<li>
					   <a href="#" style="cursor: default; text-decoration: none;" >
							<div class="shop_skin_index_list" rel="edit-t09" no="8" style="float:none;">
							<div class="img imgs"></div>
							</div>
							 <div class="name shop_skin_index_list" rel="edit-t12" no="11" style="float:none;">
								<div class="div_typename"></div>
							</div>
						</a>
					</li>
				</ul>
				<div class="clear"></div>
			</div>
		<div class="shop_skin_index_list i0" rel="edit-t02" no="1">
			<div class="img"><img src="huazhuang2/images/i1.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list i1" rel="edit-t03" no="2">
			<div class="img"><img src="huazhuang2/images/68655b4c9d.png"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list i0" rel="edit-t04" no="3">
			<div class="img"><img src="huazhuang2/images/3684d6c0d3.png"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list i2" rel="edit-t05" no="4">
			<div class="img"><img src="huazhuang2/images/i4.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list i2" rel="edit-t06" no="5">
			<div class="img"><img src="huazhuang2/images/i5.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
	</div>
<?php }else if($template_id==7){?>
    <link href="../../../Common/css/Base/home_decoration/huazhuang3/css/shop.css" rel="stylesheet" type="text/css">
    <link href="../../../Common/css/Base/home_decoration/huazhuang3/css/index.css" rel="stylesheet" type="text/css">
	<div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
			<div class="img"><img src="huazhuang3/fengge47/images/banner.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		<div id="SetHomeCurrentBox" style="height: 150px; width: 310px;"></div></div>
		<div class="box">
			<div>
				<div class="search">
					<form>
						<input type="text" name="Keyword" class="input" value="" placeholder="输入商品名称...">
						<input type="button" class="submit" value="搜索">
					</form>
				</div>
				<!--<a href="#" class="category" style="cursor: default; text-decoration: none;"></a>-->
			</div>
		</div>
		<div class="shop_skin_index_list list" rel="edit-t02" no="1">
			<div class="img"><img src="huazhuang3/images/a0.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list list" rel="edit-t03" no="2">
			<div class="img"><img src="huazhuang3/images/a1.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list list" rel="edit-t04" no="3">
			<div class="img"><img src="huazhuang3/images/a2.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list list" rel="edit-t05" no="4">
			<div class="img"><img src="huazhuang3/images/a3.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list list" rel="edit-t06" no="5">
			<div class="img"><img src="huazhuang3/images/a4.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list list" rel="edit-t07" no="6">
			<div class="img"><img src="huazhuang3/images/a5.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
	</div>
<?php }else if($template_id==8){?>
   <script type="text/javascript">
	var skin_index_init=function(){
		$('#web_skin_index .banner *').not('img').height($(window).height());
		$('#index_m').show();
	};
</script>
   <link href="../../../Common/css/Base/home_decoration/lvyou/css/shop.css" rel="stylesheet" type="text/css">
    <link href="../../../Common/css/Base/home_decoration/lvyou/css/index.css" rel="stylesheet" type="text/css">

   <div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="shop_skin_index_list banner" rel="edit-t01" no="0">
        <div class="img"><img src="lvyou/fengge47/images/banner.jpg"></div>
		<div class="mod" style="display: none;">&nbsp;</div>
       <div id="SetHomeCurrentBox" style="height: 10px; width: 310px;"></div>
	</div>


    <div id="index_m">

	  <div  class="shop_skin_index_list"  rel="edit-t02"  no="1"><div  class="text"><a  href="#" class="div_typename" style="cursor: default; text-decoration: none;">热卖产品</a></div><div  class="mod">&nbsp;</div></div>
	  <div  class="shop_skin_index_list"  rel="edit-t03"  no="2"><div  class="text"><a  href="#" class="div_typename" style="cursor: default; text-decoration: none;">新品上市</a></div><div  class="mod">&nbsp;</div></div>

   </div>
</div>
<?php }else if($template_id==9){?>
    <link href="../../../Common/css/Base/home_decoration/tupian/css/shop.css" rel="stylesheet" type="text/css">
    <link href="../../../Common/css/Base/home_decoration/tupian/css/index.css" rel="stylesheet" type="text/css">

	<div  id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
			<div  class="img"><img  src="tupian/fengge47/images/banner.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
		</div>
		<div  class="shop_skin_index_list list"  rel="edit-t02"  no="1">
			<div  class="img"><img  src="tupian/images/01.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
		<div  id="SetHomeCurrentBox"  style="height: 108px; width: 308px;"></div></div>
		<div  class="shop_skin_index_list list"  rel="edit-t03"  no="2">
			<div  class="img"><img  src="tupian/images/02.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
		</div>
		<div  class="shop_skin_index_list list"  rel="edit-t04"  no="3">
			<div  class="img"><img  src="tupian/images/03.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
		</div>
		<div  class="shop_skin_index_list list"  rel="edit-t05"  no="4">
			<div  class="img"><img  src="tupian/images/04.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
		</div>
		<div  class="shop_skin_index_list list"  rel="edit-t06"  no="5">
			<div  class="img"><img  src="tupian/images/04.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
		</div>
		<div  class="shop_skin_index_list list"  rel="edit-t07"  no="6">
			<div  class="img"><img  src="tupian/images/04.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
		</div>
		<div  class="shop_skin_index_list list"  rel="edit-t08"  no="7">
			<div  class="img"><img  src="tupian/images/04.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
		</div>
		<div  class="shop_skin_index_list list"  rel="edit-t09"  no="8">
			<div  class="img"><img  src="tupian/images/04.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
		</div>
		<div  class="shop_skin_index_list list"  rel="edit-t10"  no="9">
			<div  class="img"><img  src="tupian/images/04.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
		</div>
	</div>
<?php }else if($template_id==10){?>
<link href="../../../Common/css/Base/home_decoration/fushi2/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fushi2/css/index.css" rel="stylesheet" type="text/css">
<div  id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
        <div  class="img"><img  src="fushi2/fengge47/images/banner.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
    <div  id="SetHomeCurrentBox"  style="height: 445px; width: 310px;"></div></div>
    <div  id="index_m">
    	<div  class="bg"></div>
        <div  class="cont">
        	<div  class="shop_skin_index_list"  rel="edit-t02"  no="1">
			<div  class="text">
			<a  href="#" class="div_typename">栏目1</a>
			</div>
			<div  class="mod">&nbsp;</div>
			</div>
            <div>|</div>
            <div  class="shop_skin_index_list"  rel="edit-t03"  no="2"><div  class="text"><a  href="#" class="div_typename">栏目</a></div><div  class="mod">&nbsp;</div></div>
            <div>|</div>
            <div  class="shop_skin_index_list"  rel="edit-t04"  no="3"><div  class="text"><a  href="#" class="div_typename">栏目</a></div><div  class="mod">&nbsp;</div></div>
            <div>|</div>
            <div  class="shop_skin_index_list"  rel="edit-t05"  no="4"><div  class="text"><a  href="#" class="div_typename">栏目</a></div><div  class="mod"  style="display: none;">&nbsp;</div></div>
        </div>
	</div>
</div>

<?php }else if($template_id==11){?>
<link href="../../../Common/css/Base/home_decoration/fushi5/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fushi5/css/index.css" rel="stylesheet" type="text/css">
<div  id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
        <div  class="img"><img  src="banner.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
    <div  id="SetHomeCurrentBox"  style="height: 500px; width: 310px;"></div></div>
 	<div  class="box">
    	<ul>
        	<li>
            	<div  class="shop_skin_index_list"  rel="edit-t02"  no="1">
                	<div  class="img"><img  src="01.png"></div><div  class="mod"  style="display: none;">&nbsp;</div>
                    <div  class="text"><a  href="#"></a></div><div  class="mod"  style="display: none;">&nbsp;</div>
                </div>
            </li>
        	<li>
            	<div  class="shop_skin_index_list"  rel="edit-t03"  no="2">
                	<div  class="img"><img  src="02.png"></div><div  class="mod"  style="display: none;">&nbsp;</div>
                    <div  class="text"><a  href="#"></a></div><div  class="mod"  style="display: none;">&nbsp;</div>
                </div>
            </li>
          </ul><ul>
        	<li>
            	<div  class="shop_skin_index_list"  rel="edit-t04"  no="3">
                	<div  class="img"><img  src="03.png"></div><div  class="mod"  style="display: none;">&nbsp;</div>
                    <div  class="text"><a  href="#"></a></div><div  class="mod"  style="display: none;">&nbsp;</div>
                </div>
            </li>
        	<li>
            	<div  class="shop_skin_index_list"  rel="edit-t05"  no="4">
                	<div  class="img"><img  src="04.png"></div><div  class="mod"  style="display: none;">&nbsp;</div>
                    <div  class="text"><a  href="#"></a></div><div  class="mod"  style="display: none;">&nbsp;</div>
                </div>
            </li>
        </ul>
        <div  class="clear"></div>
</div>
</div>

<?php }else if($template_id==12){?>
  <link href="../../../Common/css/Base/home_decoration/small/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/small/css/index.css" rel="stylesheet" type="text/css">
  <div  id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div  class="shop_skin_index_list top_column"  rel="edit-t02"  no="1">
		<div  class="text"><a href="#" class="div_typename">标题</a></div><div  class="mod">&nbsp;</div>
    <div  id="SetHomeCurrentBox"  style="height: 40px; width: 310px;"></div></div>
	<div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
		<div  class="img"><img  src="small/fengge47/images/banner.jpg"></div><div  class="mod">&nbsp;</div>
    </div>
    <div  class="shop_skin_index_list top_column"  rel="edit-t03"  no="2">
		<div  class="text"><a href="#" class="div_typename">标题</a></div><div  class="mod">&nbsp;</div>
    </div>
    <div  id="index_prolist">
        <div  id="products">
                    </div>
	</div>
  </div>


<?php }else if($template_id==13){?>

  <link href="../../../Common/css/Base/home_decoration/jiazhuang/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/jiazhuang/css/index.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/jiazhuang/css/style.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/fushi5/css/index.css" rel="stylesheet" type="text/css">
  <div  id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div  class="index_header">
    	<div  class="lbar fl">
        	<div  class="search">
             <form  action="#"  method="get">
            	<input  type="text"  name="Keyword"  class="input"  value=""  placeholder="输入商品名称...">
                <input  type="submit"  class="submit"  value=" ">
            </form>
            </div>
         </div>
        <div  class="rbar fl"><a  href="#"  class="cart_icon"  style="cursor: default; text-decoration: none;"></a></div>
        <div  class="clear"></div>
    </div>
	<div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
		<div  class="img"><img  src="jiazhuang/fengge47/images/banner.jpg"></div><div  class="mod">&nbsp;</div>
    <div  id="SetHomeCurrentBox"  style="height: 150px; width: 310px;"></div></div>
    <div  class="shop_skin_index_list top_column"  rel="edit-t02"  no="1"><div  class="text"><a href="#" class="div_typename">精选推荐</a></div><div  class="mod">&nbsp;</div></div>
    <div  id="index_prolist">
					<div  class="items">
								<div  class="cont">
					<div  class="lbar fl"><a  href="#"  class="name"  style="cursor: default; text-decoration: none;">test2</a></div>
					<div  class="rbar fr"><span  class="price">￥21</span></div>
					<div  class="blank3"></div>
					<div  class="brief">222</div>
				</div>
				<div  class="more"><a  href="#"  style="cursor: default; text-decoration: none;">更多</a></div>
			</div>
			</div>
</div>

<?php }else if($template_id==14){?>
  <link href="../../../Common/css/Base/home_decoration/fushi6/css/global.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/fushi6/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/fushi6/css/index.css" rel="stylesheet" type="text/css">

  <div  id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
		<div  class="img"><img  src="fushi6/fengge47/images/banner.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
    </div>
    <div  class="ind_one_box">
    	<div  class="lbar fl">
        	<div  class="shop_skin_index_list"  rel="edit-t02"  no="1">
        		<div  class="img"><img  src="fushi6/images/t0.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
        	</div>
        </div>
    	<div  class="rbar fr">
        	<div  class="shop_skin_index_list"  rel="edit-t03"  no="2">
        	<div  class="img"><img  src="fushi6/images/t1.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
        	</div>
        </div>
        <div  class="clear"></div>
    </div>

    <div  class="shop_skin_index_list ind_two_box"  rel="edit-t04"  no="3">
		<div  class="img"><img  src="fushi6/images/t2.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
    </div>
    <div  class="ind_th_box">
    	<div  class="lbar fl">
        	<div  class="shop_skin_index_list"  rel="edit-t05"  no="4">
        		<div  class="img"><img  src="fushi6/images/t3.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
        	</div>
        </div>
    	<div  class="rbar fr">
        	<ul>
            	            	<li  class="fl mar_r mar_b">
                	<div  class="shop_skin_index_list"  rel="edit-t06"  no="5">
                        <div  class="img"><img  src="fushi6/images/t4.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
                    </div>
                </li>
                            	<li  class="fl mar_r mar_b">
                	<div  class="shop_skin_index_list"  rel="edit-t07"  no="6">
                        <div  class="img"><img  src="fushi6/images/t5.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
                    </div>
                </li>
                            	<li  class="fl mar_b">
                	<div  class="shop_skin_index_list"  rel="edit-t08"  no="7">
                        <div  class="img"><img  src="fushi6/images/t6.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
                    </div>
                </li>
                            	<li  class="fl mar_r">
                	<div  class="shop_skin_index_list"  rel="edit-t09"  no="8">
                        <div  class="img"><img  src="fushi6/images/t7.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
                    <div  id="SetHomeCurrentBox"  style="height: 73px; width: 69px;"></div></div>
                </li>
                            	<li  class="fl mar_r">
                	<div  class="shop_skin_index_list"  rel="edit-t10"  no="9">
                        <div  class="img"><img  src="fushi6/images/t8.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
                    </div>
                </li>
                            	<li  class="fl">
                	<div  class="shop_skin_index_list"  rel="edit-t11"  no="10">
                        <div  class="img"><img  src="fushi6/images/t9.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
                    </div>
                </li>
                            </ul>
        </div>
        <div  class="clear"></div>
    </div>
</div>

<?php }else if($template_id==15){?>
  <link href="../../../Common/css/Base/home_decoration/xie2/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/xie2/css/index.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/xie2/css/global.css" rel="stylesheet" type="text/css">
  <div  id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div  id="index_header">
		<div  class="lbar fl">
			<div  class="shop_skin_index_list logo"  rel="edit-t01"  no="0">
				<div  class="img"><img  src="xie2/images/logo.jpg"></div><div  class="mod">&nbsp;</div>
   			 <div  id="SetHomeCurrentBox"  style="height: 23px; width: 115px;"></div></div>
		</div>
		<div  class="rbar fr">
			<div  class="head_menu">
				<a  href="#"  class="cart"  style="cursor: default; text-decoration: none;"><img  src="../../../Common/css/Base/home_decoration/xie2/images/cart_icon.png"></a>
				<a  href="#"  class="cate"  name="show_cate"  style="cursor: default; text-decoration: none;"><img  src="../../../Common/css/Base/home_decoration/xie2/images/cate_list.png"></a>
				<a  href="#"  class="search"  style="cursor: default; text-decoration: none;"><img  src="../../../Common/css/Base/home_decoration/xie2/images/search_btn.png"></a>
			</div>
			<div  class="search_box">
             <form  action="#"  method="get">
            	<input  type="text"  name="Keyword"  class="input"  value=""  placeholder="输入商品名称...">
                <input  type="submit"  class="submit"  value=" ">
            </form>
            </div>
		</div>
		<div  class="clear"></div>
	</div>
	<div  class="shop_skin_index_list banner"  rel="edit-t02"  no="1">
		<div  class="img"><img  src="xie2/fengge47/images/banner.jpg"></div><div  class="mod">&nbsp;</div>
    </div>
			<div  class="products_cont">
			<ul>
				<li  class="column bg_blue"><a  href="#"  style="cursor: default; text-decoration: none;">sss</a></li>

			</ul>
			<div  class="clear"></div>
		</div>
			<div  class="products_cont">
			<ul>
				<li  class="column bg_ff8b3e"><a  href="#"  style="cursor: default; text-decoration: none;">饰品</a></li>

			</ul>
			<div  class="clear"></div>
		</div>
			<div  class="products_cont">
			<ul>
				<li  class="column bg_78c92e"><a  href="#"  style="cursor: default; text-decoration: none;">包包</a></li>

			</ul>
			<div  class="clear"></div>
		</div>
	</div>

<?php }else if($template_id==16){?>

     <link href="../../../Common/css/Base/home_decoration/fushi7/css/shop.css" rel="stylesheet" type="text/css">
     <link href="../../../Common/css/Base/home_decoration/fushi7/css/index.css" rel="stylesheet" type="text/css">
	 <link href="../../../Common/css/Base/home_decoration/fushi7/css/global.css" rel="stylesheet" type="text/css">

    <div  id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
		<div  class="img"><img  src="fushi7/fengge47/images/banner.jpg"></div><div  class="mod">&nbsp;</div>
    <div  id="SetHomeCurrentBox"  style="height: 148px; width: 310px;"></div></div>
	<div  class="index-h">
				<div  class="items">
            <div class="shop_skin_index_list" rel="edit-t02" no="1">
            	<div class="img"></div>
            </div>
            <div class="name shop_skin_index_list" rel="edit-t06" no="5">
				<div class="div_typename div_font" ></div>
            </div>
        </div>

		<div  class="items">
        	<div class="shop_skin_index_list" rel="edit-t03" no="2">
            	<div class="img"></div>
            </div>
            <div class="name shop_skin_index_list" rel="edit-t07" no="6">
				<div class="div_typename div_font" ></div>
            </div>
        </div>

		<div  class="items">
        	<div class="shop_skin_index_list" rel="edit-t04" no="3">
            	<div class="img"></div>
            </div>
            <div class="name shop_skin_index_list" rel="edit-t08" no="7">
				<div class="div_typename div_font" ></div>
            </div>
        </div>

        <div  class="items">
        	<div class="shop_skin_index_list" rel="edit-t05" no="4">
            	<div class="img"></div>
            </div>
            <div class="name shop_skin_index_list" rel="edit-t09" no="8">
				<div class="div_typename div_font" ></div>
            </div>
        </div>
	</div>
			<div  class="products_cont">
			<div  class="title bg_blue"><a  href="#"  class="more"  style="cursor: default; text-decoration: none;">更多</a>sss</div>
			<div  class="cont">
				<ul  class="products_list">
									</ul>
			   <div  class="clear"></div>
			</div>
		</div>
    		<div  class="products_cont">
			<div  class="title bg_f8ca5a"><a  href="#"  class="more"  style="cursor: default; text-decoration: none;">更多</a>饰品</div>
			<div  class="cont">
				<ul  class="products_list">
									</ul>
			   <div  class="clear"></div>
			</div>
		</div>
    		<div  class="products_cont">
			<div  class="title bg_ee7884"><a  href="#"  class="more"  style="cursor: default; text-decoration: none;">更多</a>包包</div>
			<div  class="cont">
				<ul  class="products_list">
									</ul>
			   <div  class="clear"></div>
			</div>
		</div>
    </div>

<?php }else if($template_id==17){?>

 <link href="../../../Common/css/Base/home_decoration/huazhuang5/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/huazhuang5/css/index.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/huazhuang5/css/global.css" rel="stylesheet" type="text/css">
  <div  id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div  class="shop_skin_index_list logo"  rel="edit-t01"  no="0">
		<div  class="img"><img  src="huazhuang5/images/logo.jpg"></div><div  class="mod">&nbsp;</div>
    <div  id="SetHomeCurrentBox"  style="height: 11px; width: 150px;"></div></div>
    <div  class="search_box">
         <form  action="#"  method="get">
            <input  type="text"  name="Keyword"  class="input"  value=""  placeholder="输入商品名称...">
            <input  type="submit"  class="submit"  value=" ">
        </form>
     </div>
	<div  class="shop_skin_index_list banner"  rel="edit-t02"  no="1">
		<div  class="img"><img  src="huazhuang5/fengge47/images/banner.jpg"></div><div  class="mod">&nbsp;</div>
    </div>

	<div  class="index-h">
		<div  class="shop_skin_index_list items"  rel="edit-t08"  no="7"><div  class="img"  style="width:60px; height:80px;text-align:center;margin:0 auto;"><img  src="huazhuang5/images/gift_icon.png" /></div></div>
		<div  class="shop_skin_index_list items"  rel="edit-t11"  no="10"><div  class="img"  style="width:60px; height:80px;text-align:center;margin:0 auto;"><img  src="huazhuang5/images/gift_icon.png" /></div></div>
		<div  class="shop_skin_index_list items"  rel="edit-t10"  no="9"><div  class="img"  style="width:60px; height:80px;text-align:center;margin:0 auto;"><img  src="huazhuang5/images/gift_icon.png" /></div></div>
		<div  class="shop_skin_index_list items"  rel="edit-t09"  no="8"><div  class="img"  style="width:60px; height:80px;text-align:center;margin:0 auto;"><img  src="huazhuang5/images/gift_icon.png" /></div></div>
	</div>
	<div  class="ind_wrap">
    	<div  class="ind_one_box">
            <div  class="lbar fl">
                <div  class="shop_skin_index_list"  rel="edit-t03"  no="2"><div  class="img"><img  src="huazhuang5/images/t3.jpg"></div><div  class="mod">&nbsp;</div></div>
            </div>
            <div  class="rbar fr">
                <div  class="shop_skin_index_list"  rel="edit-t04"  no="3"><div  class="img"><img  src="huazhuang5/images/t4.jpg"></div><div  class="mod">&nbsp;</div></div>
            </div>
            <div  class="clear"></div>
   		</div>
        <div  class="ad_items"><div  class="shop_skin_index_list"  rel="edit-t05"  no="4"><div  class="img"><img  src="huazhuang5/images/t5.jpg"></div><div  class="mod">&nbsp;</div></div></div>

        <div  class="ad_items"><div  class="shop_skin_index_list"  rel="edit-t06"  no="5"><div  class="img"><img  src="huazhuang5/images/t6.jpg"></div><div  class="mod">&nbsp;</div></div></div>
        <div  class="ad_items"><div  class="shop_skin_index_list"  rel="edit-t07"  no="6"><div  class="img"><img  src="huazhuang5/images/t7.jpg"></div><div  class="mod">&nbsp;</div></div></div>
    </div>
</div>

<?php }else if($template_id==18){?>

     <link href="../../../Common/css/Base/home_decoration/fruit/css/shop.css" rel="stylesheet" type="text/css">
     <link href="../../../Common/css/Base/home_decoration/fruit/css/index.css" rel="stylesheet" type="text/css">
	 <link href="../../../Common/css/Base/home_decoration/fruit/css/global.css" rel="stylesheet" type="text/css">

   <div  id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
		<div  class="img"><img  src="../../../Common/css/Base/home_decoration/fruit/fengge47/images/banner.jpg"></div>
		<div  class="mod"  style="display: none;">&nbsp;</div>
    <div  id="SetHomeCurrentBox"  style="height: 190px; width: 310px;"></div></div>
	<div  class="ind_wrap">
    	    	<div  class="category">
        	<h3><a  href="#"  class="more"  style="cursor: default; text-decoration: none;">查看更多</a>sss</h3>
            <div  class="products">
            	                <div  class="clear"></div>
            </div>
        </div>
            	<div  class="category">
        	<h3><a  href="#"  class="more"  style="cursor: default; text-decoration: none;">查看更多</a>饰品</h3>
            <div  class="products">
            	                <div  class="clear"></div>
            </div>
        </div>
            	<div  class="category">
        	<h3><a  href="#"  class="more"  style="cursor: default; text-decoration: none;">查看更多</a>包包</h3>
            <div  class="products">
            	                <div  class="clear"></div>
            </div>
        </div>
            	<div  class="category">
        	<h3><a  href="#"  class="more"  style="cursor: default; text-decoration: none;">查看更多</a>鞋子</h3>
            <div  class="products">
            	                <div  class="clear"></div>
            </div>
        </div>
            	<div  class="category">
        	<h3><a  href="#"  class="more"  style="cursor: default; text-decoration: none;">查看更多</a>衣服</h3>
            <div  class="products">
            	            	<div  class="items fl">
                	<div  class="pro_img"><a  href="#"  style="cursor: default; text-decoration: none;"><img  src="../../../Common/css/Base/home_decoration/fruit/images/8f529fe94b.jpg"></a></div>
                    <div  class="name"><a  href="#"  style="cursor: default; text-decoration: none;">test2</a></div>
                 </div>
                                <div  class="clear"></div>
            </div>
        </div>
            </div>
   </div>
<?php }else if($template_id==19){?>

    <link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/beijing/css/common.css">
    <link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/beijing/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/beijing/css/mall.css">
    <link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/fruit/css/PreFoot2.css">
    <link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/fruit/css/style_common.css">
	<link href="../../../Common/css/Base/home_decoration/fruit/css/shop.css" rel="stylesheet" type="text/css">
     <link href="../../../Common/css/Base/home_decoration/fruit/css/index.css" rel="stylesheet" type="text/css">
	 <link href="../../../Common/css/Base/home_decoration/fruit/css/global.css" rel="stylesheet" type="text/css">
    <div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <!--topbar begin-->
    <section class="box" id="myorder">
        <div class="user_index">
            <div class="user_header" id="actionBar">
                <span  class="shop_skin_index_list top_column"  rel="edit-t02"  no="1">
		            <span  class="text"><a href="#" class="div_typename">标题</a></span><div  class="mod">&nbsp;</div>
                 </span>
                <span  class="shop_skin_index_list top_column"  rel="edit-t03"  no="2">
		            <span  class="text"><a href="#" class="div_typename">标题</a></span><div  class="mod">&nbsp;</div>
                 </span>
                <span class="shop_skin_index_list top_column"><a href="javascript:;" class="shopping-cart">
                     <i class="fa fa-shopping-cart"></i>
                </a></span>
                <span  class="shop_skin_index_list top_column"  rel="edit-t13"  no="12">
		            <span  class="text"><a href="#" class="div_typename">标题</a></span><div  class="mod">&nbsp;</div>
                 </span>
            </div>
        </div>
    </section>

    <section class="box" id="banner">

      <div class="pfhlkd_frame1">
			<div class="pfhlkd_mode0  pfhlkd_mf10001000"></div>
			<div class="pfhlkd_mode0  pfhlkd_mf10001005"></div>
			<div>

					<div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
						<div  class="img"><img  src="fruit/images/banner.jpg" style="width:100px;height:100px;"></div>
						<div  class="mod"  style="display: none;">&nbsp;</div>
						<div  id="SetHomeCurrentBox"  style="height: 190px; width: 310px;"></div>
					</div>

			</div>
		</div>

    </section>
     <style>
    .user_nav .user_nav_list li span{width:35px;margin:0 auto;height:30px;}
	.div_font{color:#fff;margin-top:6px;}
    </style>
    <section class="box" id="module">
        <div>
            <div class="user_nav clearfix">
                <ul class="user_nav_list">
                    <!--<li class="pro-class">
                        <a href="javascript:void(0)" id="menu" class="icon-s1"><span class="fa fa-th" style="height:36px!important"></span>所有商品</a>
                    </li>
					-->
                    <li>
                        <a href="javascript:void(0)" class="icon-s1">
                        	<span class="fa ">
                            	<div class="shop_skin_index_list" rel="edit-t04" no="3">
                        			<div class="img"></div>
                        		</div>
                            </span>
                            <div class="name shop_skin_index_list" rel="edit-t08" no="7">
								<div class="div_typename div_font" ></div>
							</div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="icon-s1">
                        	<span class="fa ">
                            	<div class="shop_skin_index_list" rel="edit-t05" no="4">
                        			<div class="img"></div>
                        		</div>
                            </span>
                            <div class="name shop_skin_index_list" rel="edit-t09" no="8">
								<div class="div_typename div_font" ></div>
							</div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="icon-s1">
                        	<span class="fa">
                            	<div class="shop_skin_index_list" rel="edit-t06" no="5">
                        			<div class="img"></div>
                        		</div>
                            </span>
                            <div class="name shop_skin_index_list" rel="edit-t10" no="9">
								<div class="div_typename div_font" ></div>
							</div>
                    	</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="icon-s1">
                        	<span class="fa">
                            	<div class="shop_skin_index_list" rel="edit-t07" no="6">
                        			<div class="img"></div>
                        		</div>
                            </span>
                            <div class="name shop_skin_index_list" rel="edit-t11" no="10">
								<div class="div_typename div_font" ></div>
							</div>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </section>
    <div style="clear: both;"></div>
    <div class="user_itlist_nb">
        <div class="name shop_skin_index_list title" rel="edit-t12" no="11">
            <div style="color: #f15a5f;font-size: 18px;font-weight: bold;" class="div_typename div_font r_color"></div>
        </div>
    </div>

    <section class="main_title" style="display: none" id="top2">

        <h2 id="topname"></h2>
        <a href="javascript:;" data-type="back" class="go-back" id="backurl"><span class="icons fa fa-angle-left" data-icon=""></span></a>

    </section>
    <div class="h30" id="h30" style="display: none"></div>

    <div class="WX_con" id="J_main">
        <div class="jx">
            <div class="jx_list">

            </div>
            <div class="jx_map">

                <div class="jx_map_bd WX_cat_list">
                    <a href="javascript:;" class="J_ytag WX_cat_sp">防晒丝巾</a><!--00101-00199 -->
                </div>
            </div>

        </div>
    </div>

    <style>
    	.fixed .img img{width:22px!important;}
		.fixed{position:relative!important;}
		.sub-nav.nav-b5{width:320px;}
		.sub-nav.nav-b5 dd{margin-top:4px;}
		.jx_map{margin-bottom:5px;}
		.fixed .div_typename{width:80px;height:20px;}
    </style>

    <div  class="fixed bottom">
        <dl  class="sub-nav nav-b5">

            <dd class="active">
            	<div class="nav-b5-relative shop_skin_index_list" rel="edit-t14" no="13"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t18" no="17">
            		<div class="div_typename div_font" ></div>
            	</div>
            </dd>
            <dd>
                <div class="nav-b5-relative shop_skin_index_list" rel="edit-t15" no="14"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t19" no="18">
                    <div class="div_typename div_font" ></div>
                </div>
            </dd>
            <dd>
                <div class="nav-b5-relative shop_skin_index_list" rel="edit-t16" no="15"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t20" no="19">
                    <div class="div_typename div_font" ></div>
                </div>
            </dd>
            <dd>
                <div class="nav-b5-relative shop_skin_index_list" rel="edit-t17" no="16"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t21" no="20">
                    <div class="div_typename div_font" ></div>
                </div>
            </dd>

        </dl>
    </div>


</div>
<?php }else if($template_id==20){?>

<link  rel="stylesheet"  href="fushi_20/css/style.css">

	<link  rel="stylesheet"  type="text/css"  href="../../../Common/css/Base/home_decoration/fushi_20/css/idangerous.swiper.css">
	<link  rel="stylesheet"  href="../../../Common/css/Base/home_decoration/fushi_20/css/header_style8.css">

 <link href="../../../Common/css/Base/home_decoration/fushi2/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fushi2/css/index.css" rel="stylesheet" type="text/css">
<div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
        <div  class="img"><img  src="fushi2/fengge47/images/banner.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
    <div  id="SetHomeCurrentBox"  style="height: 445px; width: 310px;"></div></div>
    <div  id="index_m" style="bottom:-10px">
    	<div  class="membersbox pad50">

<div  class="membersbox">
	<div  class="mobile8_nav">

              <ul>
			        <li>

						<span class="shop_skin_index_list" rel="edit-t02"  no="1" style="margin-top:8px;"><span class="img" style="margin-top:-15px;"><img  src="fushi_20/images/ind1-1.png"  width="32"  height="25"></span><div  class="mod">&nbsp;</div></span>
						<span class="shop_skin_index_list" rel="edit-t06"  no="5" style="margin-top:-15px;"><span class="text" style="margin-top:-12px;height:6px;"><a  href="#" class="div_typename">栏目</a></span><div  class="mod">&nbsp;</div></span>
						<span class="shop_skin_index_list" rel="edit-t10"  no="9" style="margin-top:-15px;"><span class="text" style="margin-top:-12px;height:56px;"><b  href="#" class="div_typename">栏目</b></span><div  class="mod">&nbsp;</div></span>
                    </li><li>

                         <span class="shop_skin_index_list" rel="edit-t03"  no="2"  style="margin-top:8px;"><span class="img" style="margin-top:-15px;"><img  src="fushi_20/images/ind1-2.png"  width="32"  height="25"></span><div  class="mod">&nbsp;</div></span>
                         <span class="shop_skin_index_list" rel="edit-t07"  no="6" style="margin-top:-15px;"><span class="text" style="margin-top:-12px;height:6px;"><a  href="#" class="div_typename">栏目</a></span><div  class="mod">&nbsp;</div></span>
						 <span class="shop_skin_index_list" rel="edit-t11"  no="10" style="margin-top:-15px;"><span class="text" style="margin-top:-12px;height:56px;"><b  href="#" class="div_typename">栏目</b></span><div  class="mod">&nbsp;</div></span>

                    </li><li>
                            <span class="shop_skin_index_list" rel="edit-t04"  no="3"  style="margin-top:8px;"><span class="img" style="margin-top:-15px;"><img  src="fushi_20/images/ind1-3.png"  width="32"  height="25"></span><div  class="mod">&nbsp;</div></span>
                            <span class="shop_skin_index_list" rel="edit-t08"  no="7" style="margin-top:-15px;"><span class="text" style="margin-top:-12px;height:6px;"><a  href="#" class="div_typename">栏目</a></span><div  class="mod">&nbsp;</div></span>
							<span class="shop_skin_index_list" rel="edit-t12"  no="11" style="margin-top:-15px;"><span class="text" style="margin-top:-12px;height:56px;"><b  href="#" class="div_typename">栏目</b></span><div  class="mod">&nbsp;</div></span>
                    </li><li>
                          <span class="shop_skin_index_list" rel="edit-t05"  no="4"  style="margin-top:8px;"><span class="img" style="margin-top:-15px;"><img  src="fushi_20/images/ind1-4.png"  width="32"  height="25"></span><div  class="mod">&nbsp;</div></span>
                            <span class="shop_skin_index_list" rel="edit-t09"  no="8" style="margin-top:-15px;"><span class="text" style="margin-top:-12px;height:6px;"><a  href="#" class="div_typename">栏目</a></span><div  class="mod">&nbsp;</div></span>
							<span class="shop_skin_index_list" rel="edit-t13"  no="12" style="margin-top:-15px;"><span class="text" style="margin-top:-12px;height:56px;"><b  href="#" class="div_typename">栏目</b></span><div  class="mod">&nbsp;</div></span>

                    </li>
		</ul>
    </div></div></div>

</div>
</div>
<?php }else if($template_id==21){?>

<link  rel="stylesheet"  href="../../../Common/css/Base/home_decoration/fushi_21/css/style.css">


  <link href="../../../Common/css/Base/home_decoration/fushi2/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/fushi2/css/index.css" rel="stylesheet" type="text/css">

<div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
        <div  class="img"><img  src="fushi2/fengge47/images/banner.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
       <div  id="SetHomeCurrentBox"  style="height: 445px; width: 310px;"></div>
	</div>
    <div  id="index_m" style="top:50px;">
	   <div  class="membersbox pad50">

			<div  class="homeBbox">
						<span class="shop_skin_index_list" rel="edit-t02"  no="1"><span class="text"><a  href="#" class="div_typename" style="padding-top:20px;">栏目</a></span><div  class="mod" style="margin-top:-50px;heigth:100%;">&nbsp;</div></span>
						<span class="shop_skin_index_list" rel="edit-t03"  no="2"><span class="text"><a  href="#" class="div_typename" style="padding-top:20px;">栏目</a></span><div  class="mod" style="margin-top:-50px">&nbsp;</div></span>
						<span class="shop_skin_index_list" rel="edit-t04"  no="3"><span class="text"><a  href="#" class="div_typename" style="padding-top:20px;">栏目</a></span><div  class="mod" style="margin-top:-50px">&nbsp;</div></span>
						<span class="shop_skin_index_list" rel="edit-t05"  no="4"><span class="text"><a  href="#" class="div_typename" style="padding-top:20px;">栏目</a></span><div  class="mod" style="margin-top:-50px">&nbsp;</div></span>

			</div>

        </div>
	</div>




</div>

 <?php }else if($template_id==22){?>

<link  rel="stylesheet"  href="../../../Common/css/Base/home_decoration/fushi_22/css/style.css">
<link  rel="stylesheet"  type="text/css"  href="../../../Common/css/Base/home_decoration/fushi_22/css/idangerous.swiper.css">

  <link href="../../../Common/css/Base/home_decoration/fushi2/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/fushi2/css/index.css" rel="stylesheet" type="text/css">

<div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
        <div  class="img"><img  src="fushi2/fengge47/images/banner.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
       <div  id="SetHomeCurrentBox"  style="height: 445px; width: 310px;"></div>
	</div>

    <style>
    	.membersbox .shop_skin_index_list #SetHomeCurrentBox{width:100%;height:20px;}
    </style>

    <div  id="index_m" style="top:10px;">
	   <div  class="membersbox pad50">




			<div  class="homeCcon shop_skin_index_list">
				<span class="shop_skin_index_list"  rel="edit-t02"  no="1"><span class="text" style="font-size:20px;"><a  href="#" class="div_typename">大标题</a></span><div  class="mod">&nbsp;</div></span>

			</div>

            <div class="shop_skin_index_list" >
            	<span class="shop_skin_index_list" rel="edit-t03"  no="2"><span class="text"><a  href="#" class="div_typename">小标题</a></span><div  class="mod">&nbsp;</div></span>
            </div>

            <div  class="homeCpay"  style="top:130px;">
		     <span class="shop_skin_index_list" rel="edit-t04"  no="3"><span class="text"><a  href="#" class="div_typename">所有商品</a></span><div  class="mod">&nbsp;</div></span>
		   </div>

			<div  class="homeCnav" style="height:100%;top:390px;bottom:10px;">
				<div  class="homeCnavbox swiper-container">
					<ul  class="swiper-wrapper"  style="width: 100%; height: 80px;">
						<li  class="swiper-slide"  style="width: 25%; height: 80px;">
							<span    class="homeCnavbox_a_colblue"  style="background-color: #07a0e7">
								<h2><span class="shop_skin_index_list" rel="edit-t05"  no="4"><span class="text"><span href="#" class="div_typename" style="color:#fff;">大标题</span></span><div  class="mod">&nbsp;</div></span></h2>
								<div  class="shop_skin_index_list items"  rel="edit-t09"  no="8" style="width:25px;text-align:center;margin:0 auto;"><div  class="img"></div></div>
							</span>
						</li>
						<li  class="swiper-slide"  style="width: 25%; height: 80px;">
							<span   class="homeCnavbox_a_colgreen"  style="background-color: #72c201">
								<h2><span class="shop_skin_index_list" rel="edit-t06"  no="5"><span class="text"><a  href="#" class="div_typename" style="color:#fff;">大标题</a></span><div  class="mod">&nbsp;</div></span></h2>
								<div  class="shop_skin_index_list items"  rel="edit-t10"  no="9" style="width:25px;text-align:center;margin:0 auto;"><div  class="img"></div></div>
							</span>
						</li>
						<li  class="swiper-slide"  style="width: 25%; height: 80px;">
							<span   class="homeCnavbox_a_colyellow"  style="background-color: #ffa800">
								<h2><span class="shop_skin_index_list" rel="edit-t07"  no="6"><span class="text"><a  href="#" class="div_typename" style="color:#fff;">大标题</a></span><div  class="mod">&nbsp;</div></span></h2>
								<div  class="shop_skin_index_list items"  rel="edit-t11"  no="10" style="width:25px;text-align:center;margin:0 auto;"><div  class="img"></div></div>
							</span>
						</li>
						<li  class="swiper-slide"  style="width: 25%; height: 80px;">
							<span   class="homeCnavbox_a_colred"  style="background-color: #d50303">
								<h2><span class="shop_skin_index_list" rel="edit-t08"  no="7"><span class="text"><a  href="#" class="div_typename" style="color:#fff;">大标题</a></span><div  class="mod">&nbsp;</div></span></h2>
								<div  class="shop_skin_index_list items"  rel="edit-t12"  no="11" style="width:25px;text-align:center;margin:0 auto;"><div  class="img"></div></div>
							</span>
						</li>                </ul>
				</div>
			</div>



		</div>
	</div>




</div>

<?php }else if($template_id==23){?>

<link  rel="stylesheet"  href="../../../Common/css/Base/home_decoration/fushi_20/css/style.css">
<link  rel="stylesheet"  type="text/css"  href="../../../Common/css/Base/home_decoration/fushi_20/css/idangerous.swiper.css">
<link href="../../../Common/css/Base/home_decoration/fushi2/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fushi2/css/index.css" rel="stylesheet" type="text/css">
<style>
.name_block{height:100%;display:block;}
.homeA_span{margin:4px auto!important;height:33px;}
.swiper-wrapper img {width:35px!important;height:35px!important;}
</style>
<div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
        <div  class="img"><img  src="fushi2/fengge47/images/banner.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
       <div  id="SetHomeCurrentBox"  style="height: 445px; width: 310px;"></div>
	</div>
    <div  id="index_m" style="bottom:0px;">
      <div  class="membersbox pad50">
	    <div  class="homeAbox">
		  <div  class="homeA swiper-container">
			<ul  class="swiper-wrapper"  style="width: 100%;height: 235px;">
                <li  class="swiper-slide"  style="width: 25%; height: 135px;">
					<h2><span class="shop_skin_index_list name_block" rel="edit-t02"  no="1"><span class="text"><a  href="#" class="div_typename" style="color:#fff;">栏目</a></span><div  class="mod">&nbsp;</div></span></h2>
					<span class="shop_skin_index_list" rel="edit-t06"  no="5"><span class="img" ><img  src="fushi_23/images/ind1-1.png"></span><div  class="mod">&nbsp;</div></span>
					<div class="homeA_span" style="width:100%;"><span class="shop_skin_index_list name_block" rel="edit-t10"  no="9"><span class="text"><a  href="#" class="div_typename" style="color: #0680ad;">栏目</a></span><div  class="mod">&nbsp;</div></span></div>
					<b></b>
				</li>
				<li  class="swiper-slide"  style="width: 25%; height: 135px;">

						<h2><span class="shop_skin_index_list name_block" rel="edit-t03"  no="2"><span class="text"><a  href="#" class="div_typename"  style="color:#fff;">栏目</a></span><div  class="mod">&nbsp;</div></span></h2>
						<span class="shop_skin_index_list" rel="edit-t07"  no="6" ><span class="img" ><img  src="fushi_23/images/ind1-2.png"  width="32"  height="25"></span><div  class="mod">&nbsp;</div></span>
						<div class="homeA_span" style="width:100%;"><span class="shop_skin_index_list name_block" rel="edit-t11"  no="10"><span class="text"><a  href="#" class="div_typename" style="color: #0680ad;">栏目</a></span><div  class="mod">&nbsp;</div></span></div>
						<b></b>

				</li>
				<li  class="swiper-slide"  style="width: 25%; height: 135px;">

						<h2><span class="shop_skin_index_list name_block" rel="edit-t04"  no="3"><span class="text"><a  href="#" class="div_typename"  style="color:#fff;">栏目</a></span><div  class="mod">&nbsp;</div></span></h2>
						<span class="shop_skin_index_list" rel="edit-t08"  no="7" ><span class="img" style="margin-top:-15px;"><img  src="fushi_23/images/ind1-3.png"  width="32"  height="25"></span><div  class="mod">&nbsp;</div></span>
						<div class="homeA_span" style="width:100%;"><span class="shop_skin_index_list name_block" rel="edit-t12"  no="11"><span class="text"><a  href="#" class="div_typename" style="color: #0680ad;">栏目</a></span><div  class="mod">&nbsp;</div></span></div>
						<b></b>

				</li>
				<li  class="swiper-slide"  style="width: 25%; height: 135px;">

						<h2><span class="shop_skin_index_list name_block" rel="edit-t05"  no="4"><span class="text"><a  href="#" class="div_typename"  style="color:#fff;">栏目</a></span><div  class="mod">&nbsp;</div></span></h2>
						<span class="shop_skin_index_list" rel="edit-t09"  no="8" style="margin-top:8px;"><span class="img" style="margin-top:-15px;"><img  src="fushi_23/images/ind1-4.png"  width="32"  height="25"></span><div  class="mod">&nbsp;</div></span>
						<div class="homeA_span" style="width:100%;"><span class="shop_skin_index_list name_block" rel="edit-t13"  no="12"><span class="text"><a  href="#" class="div_typename" style="color: #0680ad;">栏目</a></span><div  class="mod">&nbsp;</div></span></div>
						<b></b>

				</li>

                			</ul>
		</div>
	   </div>




    </div>


</div>
</div>

<?php }else if($template_id==24){?>
<style>
	.homeCtitle #SetHomeCurrentBox{height:30px!important}
	.homeCcon #SetHomeCurrentBox{top:15px!important;height:17px!important;}

</style>
<link  rel="stylesheet"  href="../../../Common/css/Base/home_decoration/fushi_24/css/style.css">
<link  rel="stylesheet"  type="text/css"  href="../../../Common/css/Base/home_decoration/fushi_24/css/idangerous.swiper.css">
<link  rel="stylesheet"  href="../../../Common/css/Base/home_decoration/fushi_24/css/header_style5.css">

<link href="../../../Common/css/Base/home_decoration/fushi2/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fushi2/css/index.css" rel="stylesheet" type="text/css">
<div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
        <div  class="img"><img  src="fushi2/fengge47/images/banner.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
       <div  id="SetHomeCurrentBox"  style="height: 445px; width: 310px;"></div>
	</div>
    <div  id="index_m" style="top:120px">
      <div  class="membersbox pad50">

    	    <div  class="homeCpay" style="top:150px;"><span class="shop_skin_index_list" rel="edit-t04"  no="3"><span class="text"><a  href="#" class="div_typename" style="color:#fff;">栏目</a></span><div  class="mod">&nbsp;</div></span></div>
			<div  class="homeCmargin"></div>
			<div  class="homeCtitle shop_skin_index_list"  rel="edit-t02"  no="1" style="color:#ffffff;height:20px;display:block;"><a  href="#" class="div_typename" style="color:#fff;">栏目</a><div  class="mod" style="height:20px;">&nbsp;</div></div>

			<div  class="homeCcon shop_skin_index_list"  style="color:#ffffff; height:22px;display:block;padding-top:20px;" rel="edit-t03"  no="2"><a  href="#" class="div_typename" style="color:#fff;height:20px;display:block;">栏目</a><div  class="mod" style="height:20px;">&nbsp;</div></div>
			<div  class="homeCnav" style="position:absolute;top:365px">
				<div  class="homeCnavbox swiper-container">
					<ul  class="swiper-wrapper"  style="width: 100%; height: 65px;">
						<li  class="swiper-slide"  style="width: 25%; height: 65px;">

								<div  class="shop_skin_index_list items"  rel="edit-t05"  no="4" style="width:25px;text-align:center;margin:0 auto;padding-top:5px;"><div  class="img"><img  src="fushi_24/images/index5-2.png"  width="31"  height="26"></div></div>
								<div class="name shop_skin_index_list" rel="edit-t09" no="8" style="margin-top:12px;"><div class="div_typename r_color" style="color:#fff;height:20px;"></div></div>



						</li><li  class="swiper-slide"  style="width: 25%; height: 65px;">

								<div  class="shop_skin_index_list items"  rel="edit-t06"  no="5" style="width:25px;text-align:center;margin:0 auto;padding-top:5px;"><div  class="img"><img  src="fushi_24/images/index5-3.png"  width="31"  height="26"></div></div>
								<div class="name shop_skin_index_list" rel="edit-t10" no="9" style="margin-top:12px;"><div class="div_typename r_color" style="color:#fff;height:20px;"></div></div>

						</li><li  class="swiper-slide"  style="width: 25%;height: 65px;">

								<div  class="shop_skin_index_list items"  rel="edit-t07"  no="6" style="width:25px;text-align:center;margin:0 auto;padding-top:5px;"><div  class="img"><img  src="fushi_24/images/index5-4.png"  width="31"  height="26"></div></div>
								<div class="name shop_skin_index_list" rel="edit-t11" no="10" style="margin-top:12px;"><div class="div_typename r_color" style="color:#fff;height:20px;"></div></div>

						</li><li  class="swiper-slide"  style="width: 25%; height: 65px;">

								<div  class="shop_skin_index_list items"  rel="edit-t08"  no="7" style="width:25px;text-align:center;margin:0 auto;padding-top:5px;"><div  class="img"><img  src="fushi_24/images/index5-5.png"  width="31"  height="26"></div></div>
								<div class="name shop_skin_index_list" rel="edit-t12" no="11" style="margin-top:12px;"><div class="div_typename r_color" style="color:#fff;height:20px;"></div></div>

						</li>
					</ul>
				</div>
			</div>
		</div>


    </div>
</div>


<?php }else if($template_id==25){?>

<link  rel="stylesheet"  href="../../../Common/css/Base/home_decoration/fushi_25/css/style.css">
<link  rel="stylesheet"  type="text/css"  href="../../../Common/css/Base/home_decoration/fushi_25/css/idangerous.swiper.css">
<link  rel="stylesheet"  href="../../../Common/css/Base/home_decoration/fushi_25/css/header_style6.css">

<link href="../../../Common/css/Base/home_decoration/fushi2/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fushi2/css/index.css" rel="stylesheet" type="text/css">
<style>
#shop_skin_index .banner *{
	height:100%;
}
.mod_25{height:26px;width: 60px;margin-top: 23px;display:block;margin-left: 5px;line-height:28px;}
.mobile6_navbox ul li a{height:72px;}
</style>
<div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
        <div  class="img"><img  src="fushi2/fengge47/images/banner.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
       <div  id="SetHomeCurrentBox"  style="height: 445px; width: 310px;"></div>
	</div>
    <div  id="index_m" style="top:20px">
      <div  class="membersbox pad50">

		<div  class="mobile6_title"><a  href="#"><span class="shop_skin_index_list" rel="edit-t02"  no="1" style="height:30px;display:block;"><span style="font-size:24px;" class="div_typename">大标题</span><div  class="mod">&nbsp;</div></span></div></a>
		<div  class="mobile6_con"><a  href="#"><span class="shop_skin_index_list" rel="edit-t03"  no="2" style="height:20px;display:block;"><span class="div_typename">小标题</span><div  class="mod">&nbsp;</div></span></div></a>
		<div  class="mobile6_pay" style="top:140px;color:#fff"><a  href="#"><span class="shop_skin_index_list"  rel="edit-t04"  no="3" style="height:30px;display:block;"><span class="div_typename">立即购买</span><div  class="mod">&nbsp;</div></span></div></a>
		<div  class="mobile6_margin"></div>

		<div  class="mobile6_nav" style="top:450px;">
			<div  class="mobile6_navbox swiper-container">
				<ul  class="swiper-wrapper">
					<li  class="swiper-slide " style="width:25%;">
						<a href="#"><span class="shop_skin_index_list mod_25"  rel="edit-t05"  no="4"><span class="div_typename">首页</span><div  class="mod">&nbsp;</div></span></a>
					</li>
					<li  class="swiper-slide " style="width:25%;">
						<a href="#"><span class="shop_skin_index_list mod_25"  rel="edit-t06"  no="5"><span class="div_typename">新品</span><div  class="mod">&nbsp;</div></span></a>
					</li>
					<li  class="swiper-slide " style="width:25%;">
						<a href="#"><span class="shop_skin_index_list mod_25"  rel="edit-t07"  no="6"><span class="div_typename">热卖</span><div  class="mod">&nbsp;</div></span></a>
					</li>
					<li  class="swiper-slide " style="width:25%;">
						<a href="#"><span class="shop_skin_index_list mod_25"  rel="edit-t08"  no="7"><span class="div_typename">促销</span><div  class="mod">&nbsp;</div></span></a>
					</li>
				</ul>
			</div>
		</div>
    </div>


    </div>
</div>

<script>
$("#shop_skin_index").css("height","550px");

</script>
<?php }else if($template_id==26){?>

 <link href="../../../Common/css/Base/home_decoration/huazhuang5_1/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/huazhuang5_1/css/index.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/huazhuang5_1/css/global.css" rel="stylesheet" type="text/css">
  <div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div  class="shop_skin_index_list logo"  rel="edit-t01"  no="0">
		<div  class="img"><img  src="huazhuang5_1/images/logo.jpg"></div><div  class="mod">&nbsp;</div>
    <div  id="SetHomeCurrentBox"  style="height: 11px; width: 150px;"></div></div>
    <div  class="search_box">
         <form  action="#"  method="get">
            <input  type="text"  name="Keyword"  class="input"  value=""  placeholder="输入商品名称...">
            <input  type="submit"  class="submit"  value=" ">
        </form>
     </div>
	<div  class="shop_skin_index_list banner"  rel="edit-t02"  no="1">
		<div  class="img"><img  src="huazhuang5_1/fengge47/images/banner.jpg"></div><div  class="mod">&nbsp;</div>
    </div>
	<div  class="index-h">
		<div  class="shop_skin_index_list items"  rel="edit-t08"  no="7"><div  class="img"  style="width:60px; height:80px;text-align:center;padding-left:5px;"><img  src="huazhuang5/images/gift_icon.png" /></div></div>
		<div  class="shop_skin_index_list items"  rel="edit-t11"  no="10"><div  class="img"  style="width:60px; height:80px;text-align:center;padding-left:5px;"><img  src="huazhuang5/images/gift_icon.png" /></div></div>
		<div  class="shop_skin_index_list items"  rel="edit-t10"  no="9"><div  class="img"  style="width:60px; height:80px;text-align:center;padding-left:5px;"><img  src="huazhuang5/images/gift_icon.png" /></div></div>
		<div  class="shop_skin_index_list items"  rel="edit-t09"  no="8"><div  class="img"  style="width:60px; height:80px;text-align:center;padding-left:5px;"><img  src="huazhuang5/images/gift_icon.png" /></div></div>
	</div>
	<div  class="ind_wrap">
    	<div  class="ind_one_box">
            <div  class="lbar fl">
                <div  class="shop_skin_index_list"  rel="edit-t03"  no="2"><div  class="img"><img  src="huazhuang5/images/t3.jpg"></div><div  class="mod">&nbsp;</div></div>
            </div>
            <div  class="rbar fr">
                <div  class="shop_skin_index_list"  rel="edit-t04"  no="3"><div  class="img"><img  src="huazhuang5/images/t4.jpg"></div><div  class="mod">&nbsp;</div></div>
            </div>
            <div  class="clear"></div>
   		</div>
        <div  class="ad_items"><div  class="shop_skin_index_list"  rel="edit-t05"  no="4"><div  class="img"><img  src="huazhuang5/images/t5.jpg"></div><div  class="mod">&nbsp;</div></div></div>
		<div  class="products_list">
				<div  class="items"><a  href="#"  style="cursor: default; text-decoration: none;">DIORISSIMO</a></div>
		  </div>
        <div  class="ad_items"><div  class="shop_skin_index_list"  rel="edit-t06"  no="5"><div  class="img"><img  src="huazhuang5/images/t6.jpg"></div><div  class="mod">&nbsp;</div></div></div>
        <div  class="ad_items"><div  class="shop_skin_index_list"  rel="edit-t07"  no="6"><div  class="img"><img  src="huazhuang5/images/t7.jpg"></div><div  class="mod">&nbsp;</div></div></div>
    </div>
</div>

<?php }else if($template_id==27){?>
<link href="../../../Common/css/Base/home_decoration/fushi_27/css/jquery.bxslider.css" rel="stylesheet" />
<link  rel="stylesheet"  href="../../../Common/css/Base/home_decoration/fushi_27/css1/style.css">
<!-- <link  rel="stylesheet"  type="text/css"  href="../../../Common/css					html += '<td align="center">语音直播首页</td>';
						html += '<td align="center">语音直播</td>';
						decoration/fushi_27/css/header_style5.css"> -->
<link  rel="stylesheet"  type="text/css"  href="../../../Common/css/Base/home_decoration/fushi_27/css1/header_style5.css">
<link href="../../../Common/css/Base/home_decoration/fushi2/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fushi2/css/index.css" rel="stylesheet" type="text/css">

<style>
.wenzi img{
	width: 40px;
	height: 40px;
	z-index: 999;
}

</style>
<div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div  class="shop_skin_index_list banner"  rel="edit-t01"  no="0">
        <div  class="img"><img  src="fushi_27/fengge47/images/banner.jpg"></div><div  class="mod"  style="display: none;">&nbsp;</div>
       <div  id="SetHomeCurrentBox"  style="height: 445px; width: 310px;"></div>
	</div>

<div class="music_div" style="position:absolute;">
    <table>
     <tbody>
      <tr>
		<td class="img_td">
			<div class="music_img" >
				<img id="img1" src="../../../Common/css/Base/home_decoration/fushi_27/images/gh.png" alt="" class="gh" style="z-index:333;"/>
				<a href="#" style="cursor: default; text-decoration: none;" >
					<div class="shop_skin_index_list" rel="edit-t02" no="1" style="float:none;height: 70px;">
					<div class="img wenzi" style="position: relative;top: 17px;left: 16px;"><img src="fushi_27/images/wz1.png" /></div>
					</div>
				</a>

			</div>
		</td>

       <td class="img_td">
			<div class="music_img">
				<img id="img2" src="../../../Common/css/Base/home_decoration/fushi_27/images/gh.png" alt="" class="gh" style="z-index:333"/>
				 <a href="#" style="cursor: default; text-decoration: none;" >
					<div class="shop_skin_index_list" rel="edit-t03" no="2" style="float:none;height: 70px;">
					<div class="img wenzi" style="position: relative;top: 17px;left: 16px;"><img src="fushi_27/images/wz2.png" /></div>
					</div>
				</a>

			</div>
		</td>
       <td class="img_td">
        <div class="music_img">
         <img id="img3" src="../../../Common/css/Base/home_decoration/fushi_27/images/gh.png" alt="" class="gh" style="z-index:333"/>
			 <a href="#" style="cursor: default; text-decoration: none;" >
				<div class="shop_skin_index_list" rel="edit-t04" no="3" style="float:none;height: 70px;">
				<div class="img wenzi" style="position: relative;top: 17px;left: 16px;"><img src="fushi_27/images/wz3.png" /></div>
				</div>
			</a>
        </div> </td>
      </tr>
     </tbody>
    </table>
   </div>
   <div class="clear"></div>


</div>





<?php }else if($template_id==28){?>
  <link href="../../../Common/css/Base/home_decoration/bao/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/bao/css/index.css" rel="stylesheet" type="text/css">
	<div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="header">
			<div class="search">
				 <form action="" method="get">
					<input type="text" name="Keyword" class="input" value="" placeholder="输入商品名称...">
					<input type="submit" class="submit" value=" ">
				</form>
			</div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t01" no="0" style="height:230px;">
			<div class="img"><img src="banner.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
		<div id="SetHomeCurrentBox" style="height: 130px; width: 302px;"></div></div>
		<div class="box">
			<ul>
				<li>
					<div class="shop_skin_index_list" rel="edit-t02" no="1">
						<div class="img"><img src="ad-1.jpg"></div><div class="mod">&nbsp;</div>
					</div>
				</li>
				<li>
					<div class="shop_skin_index_list" rel="edit-t03" no="2">
						<div class="img"><img src="ad-2.jpg"></div><div class="mod">&nbsp;</div>
					</div>
				</li>
				<li>
					<div class="shop_skin_index_list" rel="edit-t04" no="3">
						<div class="img"><img src="ad-3.jpg"></div><div class="mod">&nbsp;</div>
					</div>
				</li>
				<li>
					<div class="shop_skin_index_list" rel="edit-t05" no="4">
						<div class="img"><img src="ad-4.jpg"></div><div class="mod">&nbsp;</div>
					</div>
				</li>
			</ul>
			<div class="clear"></div>
		</div>
		<div class="shop_skin_index_list a0" rel="edit-t06" no="5">
			<div class="img"><img src="ad-0.jpg"></div><div class="mod">&nbsp;</div>
		</div>
	</div>




	<?php }else if($template_id==29){?>

	<link href="../../../Common/css/Base/home_decoration/yzzj/css/shop.css" rel="stylesheet" type="text/css">
    <link href="../../../Common/css/Base/home_decoration/yzzj/css/index.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="js/shop.js"></script>

 <div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="header" style="padding:0px;">
    	<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
        	<div class="img">
			  <img src="./yzzj/photo.jpg">
			</div>
			<div class="mod" style="display: none;">&nbsp;</div>
        </div>
    </div>

<style>
.div_font {
	font-size:10px;
	color:#F64004;
}
.uili li {
	float:left;
	text-align:center;
	line-height: 24px;
	height: 24px;
	width: 20%;
}
.sd_color {
	background-color:#F74A11;
}

</style>
    <div class="menu" style="padding:0px;background:#F9F9F9;">
    	<ul class="nav" style="float:right;">
        	<li style="width:49px;">
            	<a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t02" no="1"  iscate="1">
					<div class="img " style="background-size:30px 28px;margin:5px 10px 5px 10px;background-size:cover;height:30px;"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t07" no="6">
						<div class="div_typename div_font" ></div>
					</div>
                </a>
            </li>
        	<li style="width:49px;">
            	<a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t03" no="2"  iscate="1">
					<div class="img " style="background-size:30px 28px;margin:5px 10px 5px 10px;background-size:cover;height:30px;"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t08" no="7">
						<div class="div_typename div_font" ></div>
					</div>
                </a>
            </li>
        	<li style="width:49px;">
            	<a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t04" no="3"  iscate="1">
					<div class="img " style="background-size:31px 31px;margin:5px 10px 5px 10px;background-size:cover;height:30px;"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t09" no="8">
						<div class="div_typename div_font" ></div>
					</div>
                </a>
            </li>
        	<li style="width:49px;">
            	<a href="#" style="cursor: default; text-decoration: none;">
                	<div class="shop_skin_index_list" rel="edit-t05" no="4"  iscate="1">
					  <div class="img " style="background-size:31px 31px;margin:5px 10px 5px 10px;background-size:cover;height:30px;"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t10" no="9">
						<div class="div_typename div_font" ></div>
					</div>
                </a>
            </li>
			<li style="width:49px;" id="site_nav">
			 	<a href="#" style="cursor: default; text-decoration: none;">
					<div class="shop_skin_index_list"  rel="edit-t06" no="5" iscate="1">
						<div class="img "  style="background-size:31px 31px;overflow:hidden;backgroung:center center no-repeat no-repeat;margin:5px 10px 5px 10px;background-size:cover;height:30px;"></div>
					</div>
                    <div class="name shop_skin_index_list"  rel="edit-t11" no="10">
						<div class="div_typename div_font"></div>
					</div>
                </a>
            </li>
        </ul>
		<div  class="clear"></div>


    </div>


		<div  class="ind_wrap">
			<div  class="category" id="site_nav1">
				<div  class="" style="background-color:#303537;height:30px;">

					<a  href="#" class="more" style="cursor: default; text-decoration: none;">查看更多</a>
					<div class="div_typename" style="color:white;margin:5px;">太阳眼镜</div>

				</div>
				<div  class="products" >
							<div  class="items" style="float:left;">
								<div  class="pro_img" style="width:141px;height:186px;" rel="edit-t16" no="15"><div class="img "></div><div >产品介绍</div><div style="float:right;background-color: #000;
										color: #fff;margin:0 3px 0 0;">1.5折</div><div ><span style="color: #ff0000;font-size:15px;">￥60</span> <span style="text-decoration: line-through;">￥70</span></div></div>
							</div>
							<div  class="items" style="float:left;">
								<div  class="pro_img" style="width:141px;height:186px;" rel="edit-t17" no="16"><div class="img "></div><div >产品介绍</div><div style="float:right;background-color: #000;
										color: #fff;margin:0 3px 0 0;">1.5折</div><div ><span style="color: #ff0000;font-size:15px;">￥60</span> <span style="text-decoration: line-through;">￥70</span></div></div>
							</div>

							<div  class="clear"></div>
				</div>


			</div>



			<div  class="category">
				<div  class="" style="background-color:#303537;height:30px;">

					<a  href="#" class="more" style="cursor: default; text-decoration: none;">查看更多</a>
					<div class="div_typename" style="color:white;margin:5px;">婚纱</div>
						<div  class="products">
							<div  class="clear"></div>
						</div>
				</div>
			</div>

			<div  class="category">
				<div  class="" style="background-color:#303537;height:30px;">

					<a  href="#" class="more" style="cursor: default; text-decoration: none;">查看更多</a>
					<div class="div_typename" style="color:white;margin:5px;">短外套</div>
						<div  class="products">
							<div  class="clear"></div>
						</div>
				</div>
			</div>

			<div  class="category">
				<div  class="" style="background-color:#303537;height:30px;">

					<a  href="#" class="more" style="cursor: default; text-decoration: none;">查看更多</a>
					<div class="div_typename" style="color:white;margin:5px;">短裙</div>
						<div  class="products">
							<div  class="clear"></div>
						</div>
				</div>
			</div>
		</div>
    </div>

	<?php }else if($template_id==30){?>
  <link href="../../../Common/css/Base/home_decoration/lingshi/css/shop.css" rel="stylesheet" type="text/css">
  <link href="../../../Common/css/Base/home_decoration/lingshi/css/index1.css" rel="stylesheet" type="text/css">
  <!--<link href="lingshi/css/main.css" rel="stylesheet" type="text/css">-->
  <div id="shop_skin_index"  <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="header">
        <div class="shop_skin_index_list logo" rel="edit-t01" no="0">
            <div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
            <div id="SetHomeCurrentBox" style="height: 40px; width: 120px;"></div>
		</div>
        <div class="search">
            <form action="" method="get">
                <input type="text" name="Keyword" class="input" value="" placeholder="输入商品名称...">
                <input type="submit" class="submit" value=" ">
            </form>
        </div>
    </div>
    <div class="menu">
    	<ul>
        	<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t04" no="3"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t12" no="11">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
        	<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t05" no="4"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t13" no="12">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
			<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t06" no="5"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t14" no="13">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>

        	<li>
			 <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t07" no="6"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t15" no="14">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>



			<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t08" no="7"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t16" no="15">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
        	<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t09" no="8"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t17" no="16">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
			<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t10" no="9"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t18" no="17">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>

        	<li>
			 <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t11" no="10"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t19" no="18">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>

        </ul>
        <div class="clear"></div>
    </div>
    <div class="box">
        <div class="shop_skin_index_list banner" rel="edit-t02" no="1">
            <div class="img"><img src="banner.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
        </div>
        <div class="blank3"></div>
		            <div class="item">
                <a href="#" style="cursor: default; text-decoration: none;">
                    <div class="img"><img src=""></div>
					<strong>aa</strong>
					<span>￥0.00</span>
                </a>
            </div>
                <div class="clear"></div>
        <div class="shop_skin_index_list a0" rel="edit-t03" no="2">
            <div class="img"><img src="a0.jpg"></div><div class="mod" style="display: none;">&nbsp;</div>
        </div>
        <div class="a1">
                </div>
        <div class="clear"></div>
                <div class="clear"></div>
      </div>
   </div>

	<?php }else if($template_id==31){?>
	<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
    <link href="../../../Common/css/Base/home_decoration/fengge31/css/index.css" rel="stylesheet" type="text/css">
	<link href="../../../Common/css/Base/home_decoration/fengge31/css/style.css" rel="stylesheet" type="text/css">
	   <div id="shop_skin_index"   <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="shop_skin_index_list" rel="edit-t01" no="0" style="height:117px;">
			<div class="img"></div>
			<div class="mod" style="display: none;">&nbsp;</div>

		</div>
		<div class="title shop_skin_index_list" rel="edit-t02" no="1" >
			<div class="div_typename"></div>
		</div>

		<style>
		.coupic{
			height:36px !important;
		}

		</style>
		<div class="menu coupon">
			<ul>
				<li>
				   <a href="#" style="cursor: default; text-decoration: none;" >
					<div class="shop_skin_index_list coupon_price" rel="edit-t03" no="2" style="float:none;">
					<div class="div_typename" style="color:#fff;font-size:20px;"></div>
					</div>
					<div class="coupon_desc shop_skin_index_list" rel="edit-t04" no="3" style="float:none;">
						<div class="div_typename" style="color:#D53A49;"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t19" no="18">
						<div class="img coupic"></div>
						<div class="mod" style="display: none;">&nbsp;</div>
					</div>
					</a>
				</li>
				<li>
				   <a href="#" style="cursor: default; text-decoration: none;" >
					<div class="shop_skin_index_list coupon_price" rel="edit-t06" no="5" style="float:none;">
					<div class="div_typename" style="color:#fff;font-size:20px;"></div>
					</div>
					<div class="coupon_desc shop_skin_index_list" rel="edit-t07" no="6" style="float:none;">
						<div class="div_typename" style="color:#FF9900;"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t20" no="19">
						<div class="img coupic"></div>
						<div class="mod" style="display: none;">&nbsp;</div>
					</div>
					</a>
				</li>
				<li>
				   <a href="#" style="cursor: default; text-decoration: none;" >
					<div class="shop_skin_index_list coupon_price" rel="edit-t09" no="8" style="float:none;">
					<div class="div_typename" style="color:#fff;font-size:20px;"></div>
					</div>
					<div class="coupon_desc shop_skin_index_list" rel="edit-t10" no="9" style="float:none;">
						<div class="div_typename" style="color:#79A003;"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t21" no="20">
						<div class="img coupic"></div>
						<div class="mod" style="display: none;">&nbsp;</div>
					</div>
					</a>
				</li>
			</ul>
			<div class="clear"></div>
		</div>
		<div class="notice shop_skin_index_list" rel="edit-t12" no="11" >
			<div class="div_typename notice" style="width: 300px;"></div>
		</div>
		<div>
			<div class="shop_skin_index_list i0" rel="edit-t13" no="12">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
			<div class="shop_skin_index_list i0" rel="edit-t14" no="13">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
			<div class="shop_skin_index_list i0" rel="edit-t15" no="14">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
			<div class="shop_skin_index_list i0" rel="edit-t16" no="15">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t17" no="16" style="height:160px;">
			<div class="img"></div><div class="mod">&nbsp;</div>
		<div id="SetHomeCurrentBox" style="height: 150px; width: 310px;"></div></div>
		<div class="" style="float:left;">
			<div class="img" style="height:230px;"><img src="../../../Common/css/Base/home_decoration/fengge31/img/pic2.png"></div>
		</div>
	</div>


<?php }else if($template_id==32){?>
  <link href="../../../Common/css/Base/home_decoration/yzzj2/css/shop.css" rel="stylesheet" type="text/css">
    <link href="../../../Common/css/Base/home_decoration/yzzj2/css/index.css" rel="stylesheet" type="text/css">


 <div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="header" style="padding:0px;">
    	<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
        	<div class="img">
			  <img src="../../../Common/css/Base/home_decoration/yzzj2/photo.jpg">
			</div>
			<div class="mod" style="display: none;">&nbsp;</div>
        </div>
    </div>

<style>
.div_font {
	font-size:10px;
	color:#F64004;
}
.uili li {
	float:left;
	text-align:center;
	line-height: 24px;
	height: 24px;
	width: 20%;
}
.sd_color {
	background-color:#F74A11;
}


</style>
    <div class="menu" style="padding:0px;background:#F9F9F9;">
    	<ul class="nav" style="float:right;">
        	<li style="width:49px;">
            	<a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t02" no="1"  iscate="1">
					<div class="img " style="background-size:80% 80%;"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t07" no="6">
						<div class="div_typename div_font" ></div>
					</div>
                </a>
            </li>
        	<li style="width:49px;">
            	<a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t03" no="2"  iscate="1">
					<div class="img " style="background-size:80% 80%;"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t08" no="7">
						<div class="div_typename div_font" ></div>
					</div>
                </a>
            </li>
        	<li style="width:49px;">
            	<a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t04" no="3"  iscate="1">
					<div class="img " style="background-size:80% 80%;"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t09" no="8">
						<div class="div_typename div_font" ></div>
					</div>
                </a>
            </li>
        	<li style="width:49px;">
            	<a href="#" style="cursor: default; text-decoration: none;">
                	<div class="shop_skin_index_list" rel="edit-t05" no="4"  iscate="1">
					  <div class="img " style="background-size:80% 80%;"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t10" no="9">
						<div class="div_typename div_font" ></div>
					</div>
                </a>
            </li>
			<li style="width:49px;" id="site_nav">
			 	<a href="#" style="cursor: default; text-decoration: none;">
					<div class="shop_skin_index_list"  rel="edit-t06" no="5" iscate="1">
						<div class="img "  style="background-size:80% 80%;"></div>
					</div>
                    <div class="name shop_skin_index_list"  rel="edit-t11" no="10">
						<div class="div_typename div_font"></div>
					</div>
                </a>
            </li>
        </ul>
		<div  class="clear"></div>


    </div>


		<div  class="ind_wrap">
			<div  class="category" id="site_nav1">
				<div  class="shop_skin_index_list" style="background-color:#303537;" rel="edit-t12"  no="11">

					<a  href="#" class="more" style="cursor: default; text-decoration: none;">查看更多</a>
					<div class="div_typename" style="color:white;margin:5px;"></div>

				</div>
				<div  class="products" >
					<div  class="items" style="float:left;">
						<div  class="pro_img" rel="edit-t16" no="15">
							<div class="img ">
								<img class="auto_img" src="../../../Common/css/Base/home_decoration/yzzj2/images/1.jpg">
							</div>
						</div>
					</div>
					<div  class="items" style="float:left;">
						<div  class="pro_img"  rel="edit-t17" no="16">
							<div class="img ">
								<img class="auto_img" src="../../../Common/css/Base/home_decoration/yzzj2/images/2.jpg">
							</div>
						</div>
					</div>
					<div  class="items" style="float:left;">
						<div  class="pro_img"  rel="edit-t17" no="16">
							<div class="img ">
								<img class="auto_img" src="../../../Common/css/Base/home_decoration/yzzj2/images/3.jpg">
							</div>
						</div>
					</div>
					<div  class="clear"></div>
				</div>
			</div>
		</div>
    </div>
<?php }else if($template_id==33){?>
<link href="../../../Common/css/Base/home_decoration/yzzj3/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/yzzj3/css/index.css" rel="stylesheet" type="text/css">


 <div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="header" style="padding:0px;">
    	<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
        	<div class="img">
			  <img src="../../../Common/css/Base/home_decoration/yzzj2/photo.jpg">
			</div>
			<div class="mod" style="display: none;">&nbsp;</div>
        </div>
    </div>

<style>
.div_font {
	font-size:10px;
	color:#F64004;
}
.uili li {
	float:left;
	text-align:center;
	line-height: 24px;
	height: 24px;
	width: 20%;
}
.sd_color {
	background-color:#F74A11;
}
.marquee {
	height:30px;
	line-height:30px;
}

</style>
	<div class="marquee shop_skin_index_list"  rel="edit-t18" no="17">
		<div class="div_typename r_color"></div>
	</div>
    <div class="menu" style="padding:0px;background:#F9F9F9;">
    	<ul class="nav">
        	<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t02" no="1"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t10" no="9">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
        	<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t03" no="2"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t11" no="10">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
			<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t04" no="3"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t12" no="11">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>

        	<li>
			 <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t05" no="4"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t13" no="12">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>



			<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t06" no="5"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t14" no="13">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
        	<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t07" no="6"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t15" no="14">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
			<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t08" no="7"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t16" no="15">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>

        	<li>
			 <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t09" no="8"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t17" no="16">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>

        </ul>
		<div  class="clear"></div>


    </div>



		<div  class="ind_wrap" style="margin-top: 5px;">
			<div  class="category" id="site_nav1">
				<div style="background-color:#303537;">

					<a  href="#" class="more" style="cursor: default; text-decoration: none;">查看更多</a>
					<div class="div_typename" style="color:white;margin:5px;">一级分类</div>

				</div>
				<div  class="products" >
					<div  class="items" style="float:left;">
						<div  class="pro_img" rel="edit-t16" no="15">
							<div class="img ">
								<img class="auto_img" src="../../../Common/css/Base/home_decoration/yzzj2/images/1.jpg">
							</div>
						</div>
					</div>
					<div  class="items" style="float:left;">
						<div  class="pro_img"  rel="edit-t17" no="16">
							<div class="img ">
								<img class="auto_img" src="../../../Common/css/Base/home_decoration/yzzj2/images/2.jpg">
							</div>
						</div>
					</div>
					<div  class="items" style="float:left;">
						<div  class="pro_img"  rel="edit-t17" no="16">
							<div class="img ">
								<img class="auto_img" src="../../../Common/css/Base/home_decoration/yzzj2/images/3.jpg">
							</div>
						</div>
					</div>
					<div  class="clear"></div>
				</div>
			</div>
		</div>
    </div>
<?php }else if($template_id==34){?>
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
	<link href="../../../Common/css/Base/home_decoration/fengge34/css/style.css" rel="stylesheet" type="text/css">
    <!--<link href="fengge34/css/scroll.css" rel="stylesheet" type="text/css">-->
    <link href="../../../Common/css/Base/home_decoration/fengge34/css/PreFoot.css" rel="stylesheet" type="text/css">

    <!--<script src="fengge34/js/PreFoot.js"></script> -->
<style>
.bg_img img{max-width:1280px;height:160px;}
.members_head_nav_ri .div_font{font-size:12px;}
.iconjh-cart img{width:20px;height:20px;}
.iconjh-brand .shop_skin_index_list{height:25px;}
</style>

 <div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="header bg_img" style="padding:0px;">
            <div class="shop_skin_index_list banner"  rel="edit-t01" no="0">
                <div class="img">
                    <img src="fengge34/css/images/20150521072033344276.jpg" class="">
                </div>
                <div class="mod" style="display: none;">&nbsp;</div>
            </div>
        </div>

            <section class="members_head_nav">
              <section class="members_head_nav_le" style="z-index:999;"><img src="../../../Common/css/Base/home_decoration/fengge34/css/images/20141015093446677334.png" width="60" height="60"></section>
              <section class="members_head_nav_ri">
                <ul>
                  <li style="width:50px;"><span>30</span>
              		<div class="name shop_skin_index_list" rel="edit-t06" no="5">
						<div class="div_typename div_font" ></div>
					</div>
                  </li>
                  <li style="width:50px;">
	                 <span class="iconjh-brand" >
                      	<div class="shop_skin_index_list" rel="edit-t02" no="1" >
                        	<div class="img"></div>
                      	</div>
                     </span>
                     <div class="name shop_skin_index_list" rel="edit-t07" no="6">
						<div class="div_typename div_font" ></div>
					</div>
                  </li>
                  <li style="width:50px;">
                  	<span class="iconjh-cart" >
                    	<div class="shop_skin_index_list" rel="edit-t03" no="2">
                        	<div class="img"></div>
                        </div>
                    </span>
                    <div class="name shop_skin_index_list" rel="edit-t08" no="7">
						<div class="div_typename div_font" ></div>
					</div>
                  </li>
                  <li style="width:50px;">
                  	<span class="iconjh-member" >
                    	<div class="shop_skin_index_list" rel="edit-t04" no="3">
                        	<div class="img"></div>
                        </div>
                    </span>
                   <div class="name shop_skin_index_list" rel="edit-t09" no="8">
						<div class="div_typename div_font" ></div>
					</div>
                  </li>
                  <li style="width:51px;">
                  	<span class="iconjh-member" >
                    	<div class="shop_skin_index_list" rel="edit-t05" no="4">
                        	<div class="img"></div>
                        </div>
                    </span>
                   <div class="name shop_skin_index_list" rel="edit-t10" no="9">
						<div class="div_typename div_font" ></div>
					</div>
                  </li>

                </ul>
              </section>
            </section>

        <div class="members_con">
            <section class="members_goodspic">
                <div class="pro_list_title">
                    <div class="left">防晒/沙滩/空调披肩系列</div>
                    <div class="right">更多&gt;&gt;</div>
                </div>
            <ul>
                <li class="mingoods">

                    <img class="lazy" src="../../../Common/css/Base/home_decoration/fengge34/css/images/201552918540387.jpg" data-original="" width="100%" style="display: inline;">

                    <span class="goods-title">素色防晒披肩</span>
                    <span class="price">￥68.00</span>
                    <em style="padding-left:10px; font-size:10px; color:#999">已售：11156笔</em>
                </li>
                <li class="mingoods">

                    <img class="lazy" src="../../../Common/css/Base/home_decoration/fengge34/css/images/2015520124711501.jpg" data-original="" width="100%" style="display: inline;">

                    <span class="goods-title">素色防晒披肩</span>
                    <span class="price">￥68.00</span>
                    <em style="padding-left:10px; font-size:10px; color:#999">已售：11156笔</em>
                </li>
                <li class="mingoods">

                    <img class="lazy" src="../../../Common/css/Base/home_decoration/fengge34/css/images/2015520124711501.jpg" data-original="" width="100%" style="display: inline;">

                    <span class="goods-title">素色防晒披肩</span>
                    <span class="price">￥68.00</span>
                    <em style="padding-left:10px; font-size:10px; color:#999">已售：11156笔</em>
                </li>
                <li class="mingoods">

                    <img class="lazy" src="../../../Common/css/Base/home_decoration/fengge34/css/images/2015520124711501.jpg" data-original="" width="100%" style="display: inline;">

                    <span class="goods-title">素色防晒披肩</span>
                    <span class="price">￥68.00</span>
                    <em style="padding-left:10px; font-size:10px; color:#999">已售：11156笔</em>
                </li>
            </ul>
            </section>
        </div>


                <!--distribution contact us end-->
                    <dl class="sub-nav nav-b5">
                        <dd class="active">
                            <div class="nav-b5-relative shop_skin_index_list" rel="edit-t11" no="10"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t15" no="14">
						<div class="div_typename div_font" ></div>
					</div>
                        </dd>
                        <dd>
                              <div class="nav-b5-relative shop_skin_index_list" rel="edit-t12" no="11"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t16" no="15">
						<div class="div_typename div_font" ></div>
					</div>
                        </dd>
                        <dd>
                              <div class="nav-b5-relative shop_skin_index_list" rel="edit-t13" no="12"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t17" no="16">
						<div class="div_typename div_font" ></div>
					</div>
                        </dd>
                        <dd>
                              <div class="nav-b5-relative shop_skin_index_list" rel="edit-t14" no="13"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t18" no="17">
						<div class="div_typename div_font" ></div>
					</div>
                        </dd>
                    </dl>



    </div>
<?php }else if($template_id==35){?>
<link href="../../../Common/css/Base/home_decoration/yzzj4/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/yzzj4/css/index.css?ver=<?php echo time();?>" rel="stylesheet" type="text/css">


 <div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="header" style="padding:0px;height:160px">
    	<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
        	<div class="img">
			  <img src="../../../Common/css/Base/home_decoration/yzzj2/photo.jpg">
			</div>
			<div class="mod" style="display: none;height:160px">&nbsp;</div>
        </div>
    </div>

 	<div class="marquee shop_skin_index_list"  rel="edit-t18" no="17">
		<div class="div_typename r_color"></div>
	</div>
    <div class="menu" style="padding:0px;background:#F9F9F9;">
    	<ul class="nav">
        	<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t02" no="1"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t10" no="9">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
        	<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t03" no="2"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t11" no="10">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
			<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t04" no="3"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t12" no="11">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>

        	<li>
			 <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t05" no="4"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t13" no="12">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>



			<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t06" no="5"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t14" no="13">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
        	<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t07" no="6"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t15" no="14">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>
			<li>
			   <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t08" no="7"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t16" no="15">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>

        	<li>
			 <a href="#" style="cursor: default; text-decoration: none;" >
                	<div class="shop_skin_index_list" rel="edit-t09" no="8"  iscate="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t17" no="16">
						<div class="div_typename r_color"></div>
					</div>
                </a>
			</li>

        </ul>
		<div  class="clear"></div>


    </div>

		<div class="button_tab_menu">
			<table>
				<tbody>
					<tr>
						<td align="center" valign="middle" style="width: 19%;">
							<div class="footer_div">
								<div class="shop_skin_index_list" rel="edit-t19" no="18"  iscate="1">
									<div class="footer_tab_index_0 footer_icon footer_icon_0 img"></div>
								</div>
								<div class="name shop_skin_index_list" rel="edit-t23" no="22">
									<div class="div_typename r_color"></div>
								</div>
							</div>
						</td>
						<td align="center" valign="middle" style="width: 19%;">
							<div class="footer_div">
								<div class="shop_skin_index_list" rel="edit-t20" no="19"  iscate="1">
									<div class="footer_tab_index_1 footer_icon footer_icon_1 img"></div>
								</div>
								<div class="name shop_skin_index_list" rel="edit-t24" no="23">
									<div class="div_typename r_color"></div>
								</div>
							</div>
						</td>
						<td align="center" valign="middle" style="width: 24%;">
							<a style="width: 100%">
								<div id="logo" class="shop_skin_index_list" style="position:absolute;" rel="edit-t27" no="26">

									<div id="divuserheader" class="img"></div>

								</div>
							</a>
						</td>
						<td align="center" valign="middle" style="width: 19%;">
							<div class="footer_div">
								<div class="shop_skin_index_list" rel="edit-t21" no="20"  iscate="1">
									<div class="footer_tab_index_2 footer_icon footer_icon_2 img"></div>
								</div>
								<div class="name shop_skin_index_list" rel="edit-t25" no="24">
									<div class="div_typename r_color"></div>
								</div>
							</div>
						</td>
						<td align="center" valign="middle" style="width: 19%;">
							<div class="footer_div">
								<div class="shop_skin_index_list" rel="edit-t22" no="21"  iscate="1">
									<div class="footer_tab_index_3 footer_icon footer_icon_3 img"></div>
								</div>
								<div class="name shop_skin_index_list" rel="edit-t26" no="25">
									<div class="div_typename r_color"></div>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<!-- 	 <div  class="ind_wrap" style="margin-top: 5px;">
			<div  class="category" id="site_nav1">
				<div  class="shop_skin_index_list" style="background-color:#303537;" rel="edit-t12"  no="11">

					<a  href="#" class="more" style="cursor: default; text-decoration: none;">查看更多</a>
					<div class="div_typename" style="color:white;margin:5px;"></div>

				</div>
				<div  class="products" >
					<div  class="items" style="float:left;">
						<div  class="pro_img" rel="edit-t16" no="15">
							<div class="img ">
								<img class="auto_img" src="yzzj2/images/1.jpg">
							</div>
						</div>
					</div>
					<div  class="items" style="float:left;">
						<div  class="pro_img"  rel="edit-t17" no="16">
							<div class="img ">
								<img class="auto_img" src="yzzj2/images/2.jpg">
							</div>
						</div>
					</div>
					<div  class="items" style="float:left;">
						<div  class="pro_img"  rel="edit-t17" no="16">
							<div class="img ">
								<img class="auto_img" src="yzzj2/images/3.jpg">
							</div>
						</div>
					</div>
					<div  class="clear"></div>
				</div>
			</div>
		</div> -->
    </div>
    <?php }else if($template_id==36){?>
<link href="../../../Common/css/Base/home_decoration/fengge36/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge36/index.css?ver=<?php echo time();?>" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge36/base_index.css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge36/showcase_index.css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge36/index_36.css">

<script src="../../../Common/css/Base/home_decoration/fengge36/1_files/Swipe.js"></script>
<script src="../../../Common/css/Base/home_decoration/fengge36/1_files/index.js"></script>


 <div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="header" style="padding:0px;height:160px">
    	<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
        	<div class="img">
			  <img src="../../../Common/css/Base/home_decoration/yzzj2/photo.jpg">
			</div>
			<div class="mod" style="display: none;height:160px">&nbsp;</div>
        </div>
    </div>

 	<div class="marquee shop_skin_index_list"  rel="edit-t18" no="17">
		<div class="div_typename r_color"></div>
	</div>
    	<style>
        .app-preview-anmin .img{width:40px;margin:0 auto;}

		.name{height:18px;display:block;}
		.icon_1 img{height:45px!important;}
        </style>
     <div id="app-field-model-page-1" style="width:100%">
        <div class="app-field clearfix clearfix_list b_white app-preview-anmin"><!--icon开始-->
            <div style="height: 10px;"></div>


            <div style="width:25%;float:left;text-align:center;">
					<div class="shop_skin_index_list icon_1" rel="edit-t02" no="1">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t10" no="9">
						<div class="div_typename r_color"></div>
					</div>


            </div>
            <div style="width:25%;float:left;text-align:center;">

                <div class="shop_skin_index_list icon_1" rel="edit-t03" no="2" >
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t11" no="10">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">

                <div class="shop_skin_index_list icon_1" rel="edit-t04" no="3">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t12" no="11">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">

               <div class="shop_skin_index_list icon_1" rel="edit-t05" no="4">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t13" no="12">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">

                <div class="shop_skin_index_list icon_1" rel="edit-t06" no="5">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t14" no="13">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">

                <div class="shop_skin_index_list icon_1" rel="edit-t07" no="6">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t15" no="14">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">

                <div class="shop_skin_index_list icon_1" rel="edit-t08" no="7">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t16" no="15">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">

                <div class="shop_skin_index_list icon_1" rel="edit-t09" no="8" >
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t17" no="16">
						<div class="div_typename r_color"></div>
					</div>
            </div>
    	</div>
    </div>


            <div class="shop_skin_index_list banner" rel="edit-t19" no="18" style="height:100px;">
                <div class="img" style="height:100px;">

                </div>
                <div class="mod" style="display: none;height:100px">&nbsp;</div>
            </div>

        	<div style="height:10px;display:block;"></div>
        	<div class="shop_skin_index_list banner" rel="edit-t20" no="19" style="height:100px;">
                <div class="img" style="height:100px;">

                </div>
                <div class="mod" style="display: none;height:100px">&nbsp;</div>
            </div>

			<div  class="clear"></div>
            <div class="button_tab_menu">
            <style>
            .footer_div .img{height:25px;width:25px;}
            </style>

			<table>
				<tbody>
					<tr>
						<td align="center" valign="middle" style="width: 19%;">
							<div class="footer_div">
								<div class="shop_skin_index_list" rel="edit-t21" no="20"  iscate="1">
									<div class="footer_tab_index_0 footer_icon footer_icon_0 img"></div>
								</div>
								<div class="name shop_skin_index_list" rel="edit-t25" no="24">
									<div class="div_typename r_color"></div>
								</div>
							</div>
						</td>
						<td align="center" valign="middle" style="width: 19%;">
							<div class="footer_div">
								<div class="shop_skin_index_list" rel="edit-t22" no="21"  iscate="1">
									<div class="footer_tab_index_1 footer_icon footer_icon_1 img"></div>
								</div>
								<div class="name shop_skin_index_list" rel="edit-t26" no="25">
									<div class="div_typename r_color"></div>
								</div>
							</div>
						</td>
						<td align="center" valign="middle" style="width: 24%;">
							<a style="width: 100%">
								<div id="logo" class="shop_skin_index_list" style="position:relative;width:50px;height:50px;left:0px;top:7px;" rel="edit-t29" no="28">

									<div id="divuserheader" class="img"></div>

								</div>
							</a>
						</td>
						<td align="center" valign="middle" style="width: 19%;">
							<div class="footer_div">
								<div class="shop_skin_index_list" rel="edit-t23" no="22"  iscate="1">
									<div class="footer_tab_index_2 footer_icon footer_icon_2 img"></div>
								</div>
								<div class="name shop_skin_index_list" rel="edit-t27" no="26">
									<div class="div_typename r_color"></div>
								</div>
							</div>
						</td>
						<td align="center" valign="middle" style="width: 19%;">
							<div class="footer_div">
								<div class="shop_skin_index_list" rel="edit-t24" no="23"  iscate="1">
									<div class="footer_tab_index_3 footer_icon footer_icon_3 img"></div>
								</div>
								<div class="name shop_skin_index_list" rel="edit-t28" no="27">
									<div class="div_typename r_color"></div>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

    </div>

<?php }else if($template_id==37){?>
<link href="../../../Common/css/Base/home_decoration/fengge37/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge37/index.css?ver=<?php echo time();?>" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge37/base_index.css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge37/showcase_index.css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge37/index_36.css">
<link href="../../../Common/css/Base/home_decoration/fengge37/PreFoot.css" rel="stylesheet" type="text/css">


 <div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="header" style="padding:0px;height:160px">
    	<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
        	<div class="img">
			  <img src="../../../Common/css/Base/home_decoration/yzzj2/photo.jpg">
			</div>
			<div class="mod" style="display: none;height:160px">&nbsp;</div>
        </div>
    </div>

 	<div class="marquee shop_skin_index_list"  rel="edit-t18" no="17">
		<div class="div_typename r_color" style="color:#e60014"></div>
	</div>
    	<style>
        .app-preview-anmin .img{width:40px;height:40px;margin:0 auto;}
        </style>
     <div id="app-field-model-page-1" style="width:100%">
        <div class="app-field clearfix clearfix_list b_white app-preview-anmin"><!--icon开始-->
            <div style="height: 10px;"></div>

            <div style="width:25%;float:left;text-align:center;">
					<div class="shop_skin_index_list" rel="edit-t02" no="1" >
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t10" no="9">
						<div class="div_typename r_color"></div>
					</div>


            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t03" no="2" >
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t11" no="10">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t04" no="3">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t12" no="11">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
               <div class="shop_skin_index_list" rel="edit-t05" no="4">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t13" no="12">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t06" no="5">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t14" no="13">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t07" no="6">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t15" no="14">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t08" no="7">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t16" no="15">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t09" no="8" >
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t17" no="16">
						<div class="div_typename r_color"></div>
					</div>
            </div>
    	</div>
    </div>
    <style>
    	.video_class{width:100%;height:250px;}
    </style>
	<div class="shop_skin_index_list" rel="edit-t31" no="30"><!--添加视频-->
    	<div class="div_typevideo"></div>
	</div>

            <div class="shop_skin_index_list banner" rel="edit-t19" no="18" style="height:100px;">
                <div class="img" style="height:100px;">

                </div>
                <div class="mod" style="display: none;height:100px">&nbsp;</div>
            </div>

        	<div style="height:10px;display:block;"></div>
        	<div class="shop_skin_index_list banner" rel="edit-t20" no="19" style="height:100px;">
                <div class="img" style="height:100px;">

                </div>
                <div class="mod" style="display: none;height:100px">&nbsp;</div>
            </div>

            <style>
            .brand_list ul li{list-style:none;}
			.brand_list li{float:left;}
			.brand_list li img{width:106px;height:71px;}
			.brand_head{height:20px;line-height:20px;position:relative}
			.brand_head .cat_name{float:left;font-size:14px;font-weight:bold;color:#000;margin-left:15px;}
			.brand_head .more{float:right;font-size:12px;color:#000;margin-right:5px;}
            </style>

            <div class="app-field clearfix clearfix_list  b_white app-preview-anmin" style="border-bottom:1px dashed #999;padding-bottom:4px;margin-bottom:5px;"><!--分类楼层开始-->
              <div style="height:10px"></div>
              <div style="width:100%;background-color:#fff">
                  <div style="height:5px"></div>
                      <div class="brand_head">
                          <span class="cat_name">名品专区</span>
                          <span class="more">更多>></span>
                      </div>
                      <div style="height:5px"></div>
                      <div class="brand_list">
                      	<ul>
                        	<li>
                            	<img src="../../../Common/css/Base/home_decoration/fengge37/images/brand1.png" >
                            </li>
                            <li>
                            	<img src="../../../Common/css/Base/home_decoration/fengge37/images/brand2.png" >
                            </li>
                            <li>
                            	<img src="../../../Common/css/Base/home_decoration/fengge37/images/brand3.png" >
                            </li>
                            <li>
                            	<img src="../../../Common/css/Base/home_decoration/fengge37/images/brand4.png" >
                            </li>
                            <li>
                            	<img src="../../../Common/css/Base/home_decoration/fengge37/images/brand5.png" >
                            </li>
                            <li>
                            	<img src="../../../Common/css/Base/home_decoration/fengge37/images/brand6.png" >
                            </li>
                        </ul>
                      </div>

               </div>
            </div>

            <div class="shop_skin_index_list banner" rel="edit-t22" no="21" style="height:100px;">
                <div class="img" style="height:100px;">

                </div>
                <div class="mod" style="display: none;height:100px">&nbsp;</div>
            </div>


            <div class="app-field clearfix clearfix_list  b_white app-preview-anmin"><!--搜索开始-->
                <div class="custom-search">
                  <form>
                    <input type="search" class="custom-search-input" placeholder="商品搜索：请输入商品关键字" name="searchname"  id="searchname" value="" onkeydown="javascript:if(event.keyCode==13){search_name();return false;}">
                    <button type="button" onclick="search_name()" class="custom-search-button">搜索</button>
                  </form>
            	</div>
         	</div>

			<div  class="clear"></div>
            <div class="button_tab_menu">
                <!--distribution contact us end-->
                <dl class="sub-nav nav-b5">
                    <dd class="active">
                        <div class="nav-b5-relative shop_skin_index_list" rel="edit-t23" no="22"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t27" no="26">
                    <div class="div_typename div_font" style="color:#2b9939"></div>
                </div>
                    </dd>
                    <dd>
                          <div class="nav-b5-relative shop_skin_index_list" rel="edit-t24" no="23"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t28" no="27">
                    <div class="div_typename div_font" ></div>
                </div>
                    </dd>
                    <dd>
                          <div class="nav-b5-relative shop_skin_index_list" rel="edit-t25" no="24"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t29" no="28">
                    <div class="div_typename div_font" ></div>
                </div>
                    </dd>
                    <dd>
                          <div class="nav-b5-relative shop_skin_index_list" rel="edit-t26" no="25"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t30" no="29">
                    <div class="div_typename div_font" ></div>
                </div>
                    </dd>
                </dl>
		</div>

    </div>
<?php }else if($template_id==38){?>
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
	<link href="../../../Common/css/Base/home_decoration/fengge38/css/style.css" rel="stylesheet" type="text/css">
    <!--<link href="fengge34/css/scroll.css" rel="stylesheet" type="text/css">-->
    <link href="../../../Common/css/Base/home_decoration/fengge38/css/PreFoot.css" rel="stylesheet" type="text/css">

    <!--<script src="fengge34/js/PreFoot.js"></script> -->


 <div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="header bg_img" style="padding:0px;">
            <div class="shop_skin_index_list banner"  rel="edit-t01" no="0">
                <div class="img">
                    <img src="../../../Common/css/Base/home_decoration/fengge34/css/images/20150521072033344276.jpg" class="">
                </div>
                <div class="mod" style="display: none;">&nbsp;</div>
            </div>
        </div>
        <style>
         .shop_skin_index_list .div_typename{display:block;height:20px!important;}

        </style>
            <section class="members_head_nav">
              <section class="members_head_nav_le" style="z-index:999;"><img src="../../../Common/css/Base/home_decoration/fengge38/css/images/fengge38logo.png" width="60" height="60"></section>
              <section class="members_head_nav_ri">
                <ul>
                  <li style="width:20%;">
	                 <span class="iconjh" >
                      	<div class="shop_skin_index_list" rel="edit-t02" no="1" >
                        	<div class="img"></div>
                      	</div>
                     </span>
                     <div class="name shop_skin_index_list" rel="edit-t07" no="6">
						<div class="div_typename div_font" ></div>
					</div>
                  </li>
                  <li style="width:20%;">
                  	<span class="iconjh" >
                    	<div class="shop_skin_index_list" rel="edit-t03" no="2">
                        	<div class="img"></div>
                        </div>
                    </span>
                    <div class="name shop_skin_index_list" rel="edit-t08" no="7">
						<div class="div_typename div_font" ></div>
					</div>
                  </li>
                  <li style="width:20%;">
                  	<span class="iconjh" >
                    	<div class="shop_skin_index_list" rel="edit-t04" no="3">
                        	<div class="img"></div>
                        </div>
                    </span>
                   <div class="name shop_skin_index_list" rel="edit-t09" no="8">
						<div class="div_typename div_font" ></div>
					</div>
                  </li>
                  <li style="width:20%;">
                  	<span class="iconjh" >
                    	<div class="shop_skin_index_list" rel="edit-t05" no="4">
                        	<div class="img"></div>
                        </div>
                    </span>
                   <div class="name shop_skin_index_list" rel="edit-t10" no="9">
						<div class="div_typename div_font" ></div>
					</div>
                  </li>

                  <li style="width:20%;">
                  	<span class="iconjh" >
                    	<div class="shop_skin_index_list" rel="edit-t06" no="5">
                        	<div class="img"></div>
                        </div>
                    </span>
                   <div class="name shop_skin_index_list" rel="edit-t11" no="10">
						<div class="div_typename div_font" ></div>
					</div>
                  </li>

                </ul>
              </section>
            </section>

            <!--<div style="width:100%;border-bottom:1px solid #ccc;display:block;margin-top:2px;float:left;"></div>
            <div class="ad_title" style="float:left;display:block;margin:15px 0px;width:100%;text-align:center;">
            	<span style="margin-left:2%;width:32%;border-top:2px solid #ccc;margin-top:12px;display:block;float:left;"></span>
                <div class="name shop_skin_index_list" rel="edit-t16" no="15" style="width:32%;float:left;height:26px;overflow:hidden;">
                    <div class="div_typename div_font" style="font-size:18px!important;"></div>
                </div>
                <span style="didplay:block;margin-right:2%;width:32%;border-top:2px solid #ccc;margin-top:12px;display:block;float:right;"></span>
            </div>
            -->
			<div class="members_con members_co">
                <div class="shop_skin_index_list"  rel="edit-t12" no="11">
                	<div class="img"></div>
                </div>
            </div>

            <div class="members_con members_co1">

                	<div class="img">
                    	<!--<img src="fengge38/css/images/indexcatimg.png">-->
						<div style="display:block;background:#9d9d9d;width:320px;height:55px;text-align:center;line-height:26px;">
							<span style="font-size:16px;color:#fff;line-heihgt:32px;">分类楼层广告上传图片尺寸<br> 640px*110px</span>
						</div>
                    </div>

            </div>

        <div class="members_con margin" >
                <section class="members_goodspic">
                    <ul>
                        <li class="mingoods">

                            <img class="lazy" src="../../../Common/css/Base/home_decoration/fengge38/css/images/product.png" data-original="" width="100%" style="display: inline;">

                            <span class="goods-title">素色防晒披肩</span>
                            <span class="price">￥68.00</span>
                          	<div class="buy_btn"><img src="../../../Common/css/Base/home_decoration/fengge38/css/images/buy_now.png"></div>

                        </li>
                        <li class="mingoods">

                            <img class="lazy" src="../../../Common/css/Base/home_decoration/fengge38/css/images/product.png" data-original="" width="100%" style="display: inline;">

                            <span class="goods-title">素色防晒披肩</span>
                            <span class="price">￥68.00</span>
							<div class="buy_btn"><img src="../../../Common/css/Base/home_decoration/fengge38/css/images/buy_now.png"></div>

                        </li>
                        <li class="mingoods">

                            <img class="lazy" src="../../../Common/css/Base/home_decoration/fengge38/css/images/product.png" data-original="" width="100%" style="display: inline;">

                            <span class="goods-title">素色防晒披肩</span>
                            <span class="price">￥68.00</span>
                           	<div class="buy_btn"><img src="../../../Common/css/Base/home_decoration/fengge38/css/images/buy_now.png"></div>

                        </li>
                        <li class="mingoods">

                            <img class="lazy" src="../../../Common/css/Base/home_decoration/fengge38/css/images/product.png" data-original="" width="100%" style="display: inline;">

                            <span class="goods-title">素色防晒披肩</span>
                            <span class="price">￥68.00</span>
                            <div class="buy_btn"><img src="../../../Common/css/Base/home_decoration/fengge38/css/images/buy_now.png"></div>

                        </li>
                    </ul>
                </section>
            </div>

           <!--<div class="members_con" style="width:100%;display:block;border-top:1px solid #8b8b8b;margin-top:20px;margin-left:0px!important;padding-top:5px;">
         		<div class="shop_skin_index_list bottom_menu_img"  rel="edit-t17" no="16" >
                	<div class="img"></div>
                </div>
                <div class="name shop_skin_index_list bottom_menu" rel="edit-t18" no="17">
						<div class="div_typename div_font bottom_font" ></div>
                </div>
                <div class="name shop_skin_index_list bottom_menu" rel="edit-t19" no="18">
                    <div class="div_typename div_font bottom_font" ></div>
                </div>
                <div class="name shop_skin_index_list bottom_menu" rel="edit-t20" no="19">
                    <div class="div_typename div_font bottom_font" ></div>
                </div>
         	</div>
            -->
    </div>
<?php }else if($template_id==39){?>
	<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
	<link href="../../../Common/css/Base/home_decoration/fengge39/css/style.css" rel="stylesheet" type="text/css">
   <link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge39/css/index.css" />



 <div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="header bg_img" style="padding:0px;">
            <div class="shop_skin_index_list banner"  rel="edit-t01" no="0">
                <div class="img">
                    <img src="../../../Common/css/Base/home_decoration/fengge34/css/images/20150521072033344276.jpg" class="">
                </div>
                <div class="mod" style="display: none;">&nbsp;</div>
            </div>
        </div>
        <style>
         .shop_skin_index_list .div_typename{display:block;height:20px!important;}
		 .members_head_nav .div_typename{color:#fff!important;width:56px;display:block;font-size:14px!important;line-height:26px;}

        </style>
            <section class="members_head_nav">
             <div class="phone-homepage ">
			<ul>
				<div class="phone-homepage-first" style="z-index:999"><img src="../../../Common/css/Base/home_decoration/fengge39/img/phone-img3.png" class="img-responsive"/></div>
				<li>
                	<div class="name shop_skin_index_list" rel="edit-t02" no="1">
                    	<div class="div_typename div_font" ></div>
                	</div>
                </li>
				<li>
                	<div class="name shop_skin_index_list" rel="edit-t03" no="2">
                    	<div class="div_typename div_font" ></div>
                	</div>
                </li>
                <li>
                	<div class="name shop_skin_index_list" rel="edit-t04" no="3">
                    	<div class="div_typename div_font" ></div>
                	</div>
                </li>
                <li>
                	<div class="name shop_skin_index_list" rel="edit-t05" no="4">
                    	<div class="div_typename div_font" ></div>
                	</div>
                </li>
			</ul>
		</div>
            </section>

			<div class="members_con members_co">
                <div class="shop_skin_index_list"  rel="edit-t06" no="5">
                	<div class="img"></div>
                </div>
            </div>
            <div class="members_con members_co">
                <div class="shop_skin_index_list"  rel="edit-t07" no="6">
                	<div class="img"></div>
                </div>
            </div>
			<div class="members_con members_co">
                <div class="shop_skin_index_list"  rel="edit-t11" no="10">
                	<div class="img"></div>
                </div>
            </div>
			<div class="members_con members_co">
                <div class="shop_skin_index_list"  rel="edit-t12" no="11">
                	<div class="img"></div>
                </div>
            </div>
			<div class="members_con members_co">
                <div class="shop_skin_index_list"  rel="edit-t13" no="12">
                	<div class="img"></div>
                </div>
            </div>
			<div class="members_con members_co">
                <div class="shop_skin_index_list"  rel="edit-t14" no="13">
                	<div class="img"></div>
                </div>
            </div>
			<div class="members_con members_co">
                <div class="shop_skin_index_list"  rel="edit-t15" no="14">
                	<div class="img"></div>
                </div>
            </div>
			<div class="members_con members_co">
                <div class="shop_skin_index_list"  rel="edit-t16" no="15">
                	<div class="img"></div>
                </div>
            </div>

            <div class="members_con" style="width:100%;display:block;margin-top:5px;margin-left:0px!important;padding-top:5px;background:#ecffff;">

                <div class="name shop_skin_index_list bottom_menu" rel="edit-t08" no="7">
						<div class="div_typename div_font bottom_font" ></div>
                </div>
                <div class="name shop_skin_index_list bottom_menu" rel="edit-t09" no="8">
                    <div class="div_typename div_font bottom_font" ></div>
                </div>
                <div class="name shop_skin_index_list bottom_menu" rel="edit-t10" no="9">
                    <div class="div_typename div_font bottom_font" ></div>
                </div>
         	</div>
    </div>
<?php }else if($template_id==40){?>
	<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
	<link href="../../../Common/css/Base/home_decoration/fengge40/css/style.css" rel="stylesheet" type="text/css">
  <!-- <link rel="stylesheet" href="fengge39/css/index.css" />-->



<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="main">
            <div class="header">
                <div class="bg-green">
                	<div class="shop_skin_index_list banner"  rel="edit-t01" no="0">
                		<div class="img"></div>
                		<div class="mod" style="display: none;">&nbsp;</div>
                    </div>
                </div>
                <style>
                	.div_typename{display:block;height:20px;overflow:hidden;}
                </style>
                <img src="../../../Common/css/Base/home_decoration/fengge40/images/pic.png" class="head-photo">
                <div class="bg-white">
                    <div class="op">
                    	<div class="shop_skin_index_list" rel="edit-t05" no="4">
                        	<div class="img"></div>
                        </div>
                        <p class="text-grey">
                        	<div class="name shop_skin_index_list" rel="edit-t06" no="5">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </div>
                    <div class="op br-right">
                    	<div class="shop_skin_index_list" rel="edit-t03" no="2">
                        	<div class="img"></div>
                        </div>
                        <p class="text-grey">
                        	<div class="name shop_skin_index_list" rel="edit-t04" no="3">
								<div class="div_typename div_font" ></div>
							</div>
                       	</p>
                    </div>
                    <div class="op br-right">
                    	<p class="text-green">62</p>
                    	<p class="text-grey">
                        	<div class="name shop_skin_index_list" rel="edit-t02" no="1">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </div>



                </div>
            </div>
            <div class="search-box">
                <input type="text" class="search-text" placeholder="商品搜索：请输入商品关键字">
                <button class="search-button">搜索</button>
            </div>
			<style>
				.content-box .icon-box img{width:55px;height:55px;}
			</style>
            <div class="item-box">
            <div class="content-box">
                <div class="icon-box">
                	<a href="#">
                    	<div class="shop_skin_index_list" rel="edit-t07" no="6">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t19" no="18">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>
                <div class="icon-box">
                	<a href="#">
                    	<div class="shop_skin_index_list" rel="edit-t08" no="7">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t20" no="19">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>
                <div class="icon-box">
                	<a href="#">
                    	<div class="shop_skin_index_list" rel="edit-t09" no="8">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t21" no="20">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>

                <div class="icon-box">
                	<a href="#">
                    	<div class="shop_skin_index_list" rel="edit-t10" no="9">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t22" no="21">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>
                <div class="icon-box">
                	<a href="#">
                    	<div class="shop_skin_index_list" rel="edit-t11" no="10">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t23" no="22">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>
                <div class="icon-box">
                	<a href="#">
                    	<div class="shop_skin_index_list" rel="edit-t12" no="11">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t24" no="23">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>
                <div class="icon-box">
                	<a href="#">
                    	<div class="shop_skin_index_list" rel="edit-t13" no="12">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t25" no="24">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>
                <div class="icon-box">
                	<a href="#">
                    	<div class="shop_skin_index_list" rel="edit-t14" no="13">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t57" no="56">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>
                <div class="icon-box">
                	<a href="#">
                    	<div class="shop_skin_index_list" rel="edit-t15" no="14">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t26" no="25">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>
                <div class="icon-box">
                	<a href="#">
                    	<div class="shop_skin_index_list" rel="edit-t16" no="15">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t27" no="26">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>
                <div class="icon-box">
                	<a href="#">

                    	<div class="shop_skin_index_list" rel="edit-t17" no="16">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t28" no="27">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>
                <div class="icon-box">
                	<a href="#">
                    	<div class="shop_skin_index_list" rel="edit-t18" no="17">
                        	<div class="img"></div>
                        </div>
                        <p>
                        	<div class="name shop_skin_index_list" rel="edit-t29" no="28">
								<div class="div_typename div_font" ></div>
							</div>
                        </p>
                    </a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="bg-grey"></div>
            </div>
            <div class="hot-market">
                <div class="title-box">
                    <div class="title">
                    	<div class="name shop_skin_index_list" rel="edit-t47" no="46">
								<div class="div_typename div_font" ></div>
						</div>
                    </div>
                </div>
                <div class="market-box">
                    <div class="half-box border-r">
                        <div class="shop_skin_index_list " rel="edit-t30" no="29">
                        	<div class="img hot-market hotimg"></div>
                        </div>

                    </div>
                    <div class="half-box">
                        <div class="shop_skin_index_list" rel="edit-t31" no="30">
                        	<div class="img hot-market hotimg"></div>
                        </div>
                    </div>
                    <div class="four-box border-r">
                        <div class="shop_skin_index_list" rel="edit-t32" no="31">
                        	<div class="img hot-market hotimg2"></div>
                        </div>
                    </div>
                    <div class="four-box border-r">
                        <div class="shop_skin_index_list" rel="edit-t33" no="32">
                        	<div class="img hot-market hotimg2"></div>
                        </div>
                    </div>
                    <div class="four-box border-r">
                        <div class="shop_skin_index_list" rel="edit-t34" no="33">
                        	<div class="img hot-market hotimg2"></div>
                        </div>
                    </div>
                    <div class="four-box">
                        <div class="shop_skin_index_list" rel="edit-t35" no="34">
                        	<div class="img hot-market hotimg2"></div>
                        </div>
                    </div>
                </div>

                <div class="shop_skin_index_list" rel="edit-t42" no="41" style="margin:3px 0px;">
                    <div class="img"></div>
                </div>


            </div></div>

            <div class="like-box">
                 <div class="title-box">
                    <div class="title">
                    	<div class="name shop_skin_index_list" rel="edit-t48" no="47">
								<div class="div_typename div_font"></div>
						</div>
                    </div>
                 </div>
                 <div class="product-box">
                    <div class="shop_skin_index_list" rel="edit-t43" no="42">
                    	<div class="img like-market"></div>
                    </div>
                 </div>
                 <div class="product-box">
                     <div class="shop_skin_index_list" rel="edit-t44" no="43">
                    	<div class="img like-market"></div>
                    </div>

                 </div>

                 <div class="product-box">
                    <div class="shop_skin_index_list" rel="edit-t45" no="44">
                    	<div class="img like-market"></div>
                    </div>
                 </div>
                 <div class="product-box">
                    <div class="shop_skin_index_list" rel="edit-t46" no="45">
                    	<div class="img like-market"></div>
                    </div>
                 </div>
            </div>

            <div class="footer">
            <div class="footer-box">
                <div class="weidian active">

                    	<div class="shop_skin_index_list" rel="edit-t49" no="48">
                    		<div class="img"></div>
                    	</div>
                    	<p>
                        	<div class="name shop_skin_index_list" rel="edit-t53" no="52">
                        		<div class="div_typename div_font" ></div>
                        	</div>
                        </p>

                </div>
                <div class="weidian">

                    	<div class="shop_skin_index_list" rel="edit-t50" no="49">
                    		<div class="img"></div>
                    	</div>
                    	<p>
                        	<div class="name shop_skin_index_list" rel="edit-t54" no="53">
                        		<div class="div_typename div_font" ></div>
                        	</div>
                        </p>

                </div>
                <div class="weidian">

                    	<div class="shop_skin_index_list" rel="edit-t51" no="50">
                    		<div class="img"></div>
                    	</div>
                    	<p>
                        	<div class="name shop_skin_index_list" rel="edit-t55" no="54">
                        		<div class="div_typename div_font" ></div>
                        	</div>
                        </p>

                </div>
                <div class="weidian">

                    	<div class="shop_skin_index_list" rel="edit-t52" no="51">
                    		<div class="img"></div>
                    	</div>
                    	<p>
                        	<div class="name shop_skin_index_list" rel="edit-t56" no="55">
                        		<div class="div_typename div_font" ></div>
                        	</div>
                        </p>

                </div>
            </div>
            </div>
           <!--<div class="clear-fix"></div>-->
        </div>



<?php }else if($template_id==41){?>
<link href="../../../Common/css/Base/home_decoration/fengge41/css/style.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
<div class="main">
    <div class="header"><!--头部开始-->
    	<div class="shop_skin_index_list banner"  rel="edit-t01" no="0" style="height:160px;">
            <div class="img"></div>
            <div class="mod" style="display: none;">&nbsp;</div>
        </div>

        <img src="../../../Common/css/Base/home_decoration/fengge41/images/logo.png" class="picture">
        <div class="detail-info">
            <div class="one">
                <p class="num">1846</p>
                <p class="name">
                	<div class="name shop_skin_index_list" rel="edit-t07" no="6">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
            <div class="one">
                <p class="num">1625</p>
                <p class="name">
                	<div class="name shop_skin_index_list" rel="edit-t08" no="7">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
            <div class="one">
                <div class="shop_skin_index_list" rel="edit-t12" no="11">
                    <div class="img"></div>
                </div>
                <p class="name">
                	<div class="name shop_skin_index_list" rel="edit-t09" no="8">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
            <div class="one">
                <div class="shop_skin_index_list" rel="edit-t13" no="12">
                    <div class="img"></div>
                </div>
                <p class="name">
                	<div class="name shop_skin_index_list" rel="edit-t10" no="9">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
            <div class="one">
                <div class="shop_skin_index_list" rel="edit-t14" no="13">
                    <div class="img"></div>
                </div>
                <p class="name">
                	<div class="name shop_skin_index_list" rel="edit-t11" no="10">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
        </div>
    </div><!--头部结束-->

    <div class="shop_skin_index_list" rel="edit-t02" no="1">
        <div class="img"></div>
    </div>

	<div class="shop_skin_index_list newad" rel="edit-t15" no="14">
        <div class="img"></div>
    </div>
	<div class="shop_skin_index_list newad" rel="edit-t16" no="15">
        <div class="img"></div>
    </div>
	<div class="shop_skin_index_list newad" rel="edit-t17" no="16">
        <div class="img"></div>
    </div>



    <div class="shop_skin_index_list" rel="edit-t03" no="2">
        <div class="img"></div>
    </div>


    <div class="three-shop"><!--分类楼层开始-->
        <div class="left-box">
            <img src="" class="title">
            <div class="big-box">

            	<span  class="floor_tips">三行分类<br>建议尺寸<br>200*520<br><br><br>两行分类<br>建议尺寸<br>200*312</span>
                <img src="">
            </div>
        </div>
        <div class="right-box">
            <div class="st-box">


                <span class="floor_tips1">建议尺寸<br>500*500</span>
                <img src="">
            </div>
            <div class="st-box">


                <span class="floor_tips1">建议尺寸<br>500*500</span>
                <img src="">
            </div>
            <div class="st-box">


                <span class="floor_tips1">建议尺寸<br>500*500</span>
                <img src="">
            </div>
            <div class="st-box">


                <span class="floor_tips1">建议尺寸<br>500*500</span>
                <img src="">
            </div>
            <div class="st-box">


                <span class="floor_tips1">建议尺寸<br>500*500</span>
                <img src="">
            </div>
            <div class="st-box">


                <span class="floor_tips1">建议尺寸<br>500*500</span>
                <img src="">
            </div>


        </div>
        <div class="clear"></div>
    </div><!--分类楼层结束-->

    <div class="div-box"><!--全部商品开始-->

		<div class="shop_skin_index_list" rel="edit-t05" no="4">
			<div class="img"></div>
		</div>

        <div class="shop_skin_index_list" rel="edit-t06" no="5">
            <div class="img"></div>
        </div>
        <div class="small-box">
            <img src="">

            <span class="span1">原价:￥659</span><span class="span2">￥215</span>
            <button class="red-button">抢购</button>
        </div>
        <div class="small-box">
            <img src="">

            <span class="span1">原价:￥659</span><span class="span2">￥215</span>
            <button class="red-button">抢购</button>
        </div>
        <div class="clear"></div>
    </div><!--全部商品结束-->

</div>
</div>

<?php }else if($template_id==42){?>
<link href="../../../Common/css/Base/home_decoration/fengge42/css/style.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">


<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div class="main">
        <div class="shop_skin_index_list" rel="edit-t01" no="0">
        	<div class="img"></div>
    	</div>
        <div class="search-box">
            <input type="text" class="search-input"  placeholder="查找宝贝">
        </div>

        <div class="shop_skin_index_list banner"  rel="edit-t02" no="1" style="height:160px;">
            <div class="img"></div>

        </div>
        <div class="content-box">
            <div class="icon-box">
                <div class="shop_skin_index_list" rel="edit-t03" no="2">
                    <div class="img"></div>
                </div>
                <p>
                    <div class="name shop_skin_index_list" rel="edit-t11" no="10">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
            <div class="icon-box">
                <div class="shop_skin_index_list" rel="edit-t04" no="3">
                    <div class="img"></div>
                </div>
                <p>
                    <div class="name shop_skin_index_list" rel="edit-t12" no="11">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
            <div class="icon-box">
                <div class="shop_skin_index_list" rel="edit-t05" no="4">
                    <div class="img"></div>
                </div>
                <p>
                    <div class="name shop_skin_index_list" rel="edit-t13" no="12">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
            <div class="icon-box">
                <div class="shop_skin_index_list" rel="edit-t06" no="5">
                    <div class="img"></div>
                </div>
                <p>
                    <div class="name shop_skin_index_list" rel="edit-t14" no="13">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
            <div class="icon-box">
                <div class="shop_skin_index_list" rel="edit-t07" no="6">
                    <div class="img"></div>
                </div>
                <p>
                    <div class="name shop_skin_index_list" rel="edit-t15" no="14">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
            <div class="icon-box">
                <div class="shop_skin_index_list" rel="edit-t08" no="7">
                    <div class="img"></div>
                </div>
                <p>
                    <div class="name shop_skin_index_list" rel="edit-t16" no="15">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
            <div class="icon-box">
                <div class="shop_skin_index_list" rel="edit-t09" no="8">
                    <div class="img"></div>
                </div>
                <p>
                    <div class="name shop_skin_index_list" rel="edit-t17" no="16">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>
            <div class="icon-box">
                <div class="shop_skin_index_list" rel="edit-t10" no="9">
                    <div class="img"></div>
                </div>
                <p>
                    <div class="name shop_skin_index_list" rel="edit-t18" no="17">
                        <div class="div_typename div_font" ></div>
                    </div>
                </p>
            </div>

            <div class="clear"></div>
        </div>
        <div class="dapai">
            <div class="left-box">
                <div class="shop_skin_index_list"  rel="edit-t19" no="18">
            		<div class="img"></div>
            	</div>
            </div>
            <div class="right-box">
                <div class="first-box">
                    <div class="shop_skin_index_list"  rel="edit-t20" no="19">
                        <div class="img"></div>
                    </div>
                </div>
                <div class="second-box">
                    <div class="shop_skin_index_list"  rel="edit-t21" no="20">
                        <div class="img"></div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
           <!-- <div class="skin-care">
                <div style="width:320px;height:27px;background:#999999;margin-bottom:5px;text-align:center;color:#fff;line-height:26px;">分类楼层顶部图1024*88</div>
                <div class="content">
                    <div class="left-box"><div style="width:150px;height:118px;background:#999999;text-align:center;color:#fff;line-height:42px;padding-top:30px;">第一张分类图<br>481*504</div></div>
                    <div class="right-box">
                        <div style="width:150px;height:49px;background:#999999;margin-bottom:10px;text-align:center;color:#fff;line-height:16px;padding-top:20px;">第二张分类图<br>481*238</div>
                        <div style="width:150px;height:49px;background:#999999;text-align:center;color:#fff;line-height:16px;padding-top:20px;">第三张分类图<br>481*238</div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            -->
            <div class="makeup">
                    <div class="shop_skin_index_list"  rel="edit-t29" no="28" style="margin-top:5px;">
                        <div class="img"></div>
                    </div>
                    <div class="content-bg">
                    <div class="content">
                        <div class="left-box">
                        	<div class="top_box" >
                            	<div class="shop_skin_index_list"  rel="edit-t35" no="34">
                                    <div class="img"></div>
                                </div>
                            </div>
                            <div class="first-box">
                                <div class="shop_skin_index_list"  rel="edit-t30" no="29">
                                    <div class="img"></div>
                                </div>
                            </div>
                            <div class="second-box">
                                <div class="shop_skin_index_list"  rel="edit-t31" no="30">
                                        <div class="img"></div>
                                    </div>
                                </div>
                            <div class="third-box">
                                <div class="shop_skin_index_list"  rel="edit-t32" no="31">
                                    <div class="img"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="right-box">
                            <div class="first-box">
                                <div class="shop_skin_index_list"  rel="edit-t33" no="32">
                                        <div class="img"></div>
                                    </div>
                                </div>
                            <div class="second-box">
                                <div class="shop_skin_index_list"  rel="edit-t34" no="33">
                                        <div class="img"></div>
                                    </div>
                                </div>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    </div>
                </div>


            <div class="new-product" >
                <div class="title">
                    <div class="name">近日新品</div>
                    <div class="look">查看全部>></div>
                </div>
                <div class="product-box">
                    <div class="one-box">
                        <img src="../../../Common/css/Base/home_decoration/fengge42/images/new1.jpg">
                        <div class="name">新佰草集新玉润保湿化妆水200ml爽肤水柔肤水补水</div>
                        <div class="price">￥58</div>
                        <div class="sale">已售：128笔</div>
                    </div>
                    <div class="one-box">
                        <img src="../../../Common/css/Base/home_decoration/fengge42/images/new1.jpg">
                        <div class="name">新佰草集新玉润保湿化妆水200ml爽肤水柔肤水补水</div>
                        <div class="price">￥58</div>
                        <div class="sale">已售：128笔</div>
                    </div>

                    <div class="clear"></div>
                </div>
            </div>
            <div class="new-product" >
                <div class="title">
                    <div class="name">热销爆款</div>
                    <div class="look">查看全部>></div>
                </div>
                <div class="product-box">
                    <div class="one-box">
                        <img src="../../../Common/css/Base/home_decoration/fengge42/images/new1.jpg">
                        <div class="name">新佰草集新玉润保湿化妆水200ml爽肤水柔肤水补水</div>
                        <div class="price">￥58</div>
                        <div class="sale">已售：128笔</div>
                    </div>
                    <div class="one-box">
                        <img src="../../../Common/css/Base/home_decoration/fengge42/images/new1.jpg">
                        <div class="name">新佰草集新玉润保湿化妆水200ml爽肤水柔肤水补水</div>
                        <div class="price">￥58</div>
                        <div class="sale">已售：128笔</div>
                    </div>

                    <div class="clear"></div>
                </div>
            </div>

            <style>
            	.weidian .first img{width:65px!important;height:35px!important;}
            </style>
            <div class="footer">
            <div class="footer-box">
                <div class="weidian active">

                    	<div class="shop_skin_index_list" rel="edit-t22" no="21">
                    		<div class="img first" ></div>
                    	</div>


                </div>
                <div class="weidian">

                    	<div class="shop_skin_index_list" rel="edit-t23" no="22">
                    		<div class="img"></div>
                    	</div>
                    	<p>
                        	<div class="name shop_skin_index_list" rel="edit-t26" no="25">
                        		<div class="div_typename div_font" ></div>
                        	</div>
                        </p>

                </div>
                <div class="weidian">

                    	<div class="shop_skin_index_list" rel="edit-t24" no="23">
                    		<div class="img"></div>
                    	</div>
                    	<p>
                        	<div class="name shop_skin_index_list" rel="edit-t27" no="26">
                        		<div class="div_typename div_font" ></div>
                        	</div>
                        </p>

                </div>
                <div class="weidian">

                    	<div class="shop_skin_index_list" rel="edit-t25" no="24">
                    		<div class="img"></div>
                    	</div>
                    	<p>
                        	<div class="name shop_skin_index_list" rel="edit-t28" no="27">
                        		<div class="div_typename div_font" ></div>
                        	</div>
                        </p>

                </div>
            </div>
            </div>



        </div>
</div>

<?php }else if($template_id==43){?>

<link href="../../../Common/css/Base/home_decoration/fengge43/css/style.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge43/css/common.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge43/css/detail3.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge43/css/font-awesome.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge43/css/idangerous.swiper.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge43/css/reset.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge43/css/weimobfont2.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge43/css/wicons.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge43/css/widget_menu.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge43/css/widget_public.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">


<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div class="main">
			<div class="header_3" style="background:#8D4E00;">
					<label style="color:#FEFFFC;">
						<i>
						<div class="shop_skin_index_list" rel="edit-t01" no="0">
							<div class="img" style=" border-radius:100px;"></div>
						</div>
						</i>
						<div class="name shop_skin_index_list title" rel="edit-t24" no="23">
							<div class="div_typename div_font" ></div>
						</div>
					</label>
			</div>
			<div class="shop_skin_index_list banner"  rel="edit-t02" no="1" style="height:160px;">
				<div class="img"></div>
			</div>
			<div class="widget_wrap icon_1">
				<ul>
						<li style="background:#F7F7F5" loop="1">
							<div class="shop_skin_index_list" rel="edit-t03" no="2">
								<div class="img"></div>
							</div>
						<div class="name shop_skin_index_list" rel="edit-t07" no="6">
							<div class="div_typename div_font" ></div>
						</div>
						</li>
						<li style="background:#F7F7F5" loop="1">
							<div class="shop_skin_index_list" rel="edit-t04" no="3">
								<div class="img"></div>
							</div>
							<div class="name shop_skin_index_list" rel="edit-t08" no="7">
								<div class="div_typename div_font" ></div>
							</div>
						</li>
						<li style="background:#F7F7F5" loop="1">
							<div class="shop_skin_index_list" rel="edit-t05" no="4">
								<div class="img"></div>
							</div>
							<div class="name shop_skin_index_list" rel="edit-t09" no="8">
								<div class="div_typename div_font" ></div>
							</div>
						</li>
						<li style="background:#F7F7F5" loop="1">
							<div class="shop_skin_index_list" rel="edit-t06" no="5">
								<div class="img"></div>
							</div>
							<div class="name shop_skin_index_list" rel="edit-t10" no="9">
								<div class="div_typename div_font" ></div>
							</div>
						</li>
				</ul>
			</div>
            <div Type="2" data-role="widget" data-widget="search_2" class="search_2">
				<div class="widget_wrap">
					<form action="#">
						<div>
							<input type="search" value="输关键词找宝贝" name="search" placeholder="输关键词找宝贝" />
						</div>
						<div>
							<img src="../../../Common/css/Base/home_decoration/fengge43/images/widget_search_pic.png" />
						</div>
					</form>
				</div>
			</div>
            <div class="shop_skin_index_list" rel="edit-t11" no="10" style="height:320px;">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t12" no="11" style="height:160px;">
				<div class="img"></div>
			</div>
			<div Type="1" data-role="widget" data-widget="line_4" class="line_4">
			  <div class="widget_wrap" style="height:5px;padding:0; position:relative;"></div>
			</div>
			<div Type="1" data-role="widget" data-widget="pic_1" class="pic_1">
			  <div class="widget_wrap">
				  <img src="../../../Common/css/Base/home_decoration/fengge43/images/1508251103576708.jpg" />
			  </div>
			</div>
			<div Type="1" data-role="widget" data-widget="goodsList_1" class="goodsList_1">
				<div class="goods">
				<ul>
					<li loop="1">
						<div class="goodlists">
							<div class="img_wrap">
								<img style="background-image:url(../../../Common/css/Base/home_decoration/fengge43/images/1507241549100744.jpg);" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQIW2NkAAIAAAoAAggA9GkAAAAASUVORK5CYII=" />
								<!-- <span name="goodsdetailspan" class="tag">团购促销</span> -->
							</div>
							<div>
								<p class="title">
									<a href="#" title="【团购】瑞士凯琳斯蒂手工皂七件套男女美白保湿去黑头粉刺祛痘消印全身美白洁面正品">【团购】瑞士凯琳斯蒂手工皂七件套男女美白保湿去黑头粉刺祛痘消印全身美白洁面正品</a>
								</p>
								<label class="price">￥139</label>
								<div class="">
									<a href="javascript:;" vid="76227" vPrice="139" vMember="False" class="goods_Buy"></a>
								</div>
							</div>
						</div>
					</li>
					<li loop="1">
						<div class="goodlists">
							<div class="img_wrap">
								<img style="background-image:url(../../../Common/css/Base/home_decoration/fengge43/images/1507241549100744.jpg);" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQIW2NkAAIAAAoAAggA9GkAAAAASUVORK5CYII=" />
								<!-- <span name="goodsdetailspan" class="tag">团购促销</span> -->
							</div>
							<div>
								<p class="title">
									<a href="#" title="【团购】瑞士凯琳斯蒂手工皂七件套男女美白保湿去黑头粉刺祛痘消印全身美白洁面正品">【团购】瑞士凯琳斯蒂手工皂七件套男女美白保湿去黑头粉刺祛痘消印全身美白洁面正品</a>
								</p>
								<label class="price">￥139</label>
								<div class="">
									<a href="javascript:;" vid="76227" vPrice="139" vMember="False" class="goods_Buy"></a>
								</div>
							</div>
						</div>
					</li>
				</ul>
				</div>
			</div>

			<div class="shop_skin_index_list" rel="edit-t13" no="12">
				<div class="img"></div>
			</div>
			<div data-role="widget" data-widget="menu_2" class="menu_2">
				<div class="widget_wrap icon_1">
					<ul>

						<li style="background: #F7F7F5">
							<div class="shop_skin_index_list" rel="edit-t14" no="13">
								<div class="img foot"></div>
							</div>
							<div class="name shop_skin_index_list" rel="edit-t19" no="18">
								<div class="div_typename div_font" ></div>
							</div>
						</li>

						<li style="background: #F7F7F5">
							<div class="shop_skin_index_list" rel="edit-t15" no="14">
								<div class="img foot"></div>
							</div>
							<div class="name shop_skin_index_list" rel="edit-t20" no="19">
								<div class="div_typename div_font" ></div>
							</div>
						</li>

						<li style="background: #F7F7F5">
							<div class="shop_skin_index_list" rel="edit-t16" no="15">
								<div class="img foot"></div>
							</div>
							<div class="name shop_skin_index_list" rel="edit-t21" no="20">
								<div class="div_typename div_font" ></div>
							</div>
						</li>

						<li style="background: #F7F7F5">
							<div class="shop_skin_index_list" rel="edit-t17" no="16">
								<div class="img foot"></div>
							</div>
							<div class="name shop_skin_index_list" rel="edit-t22" no="21">
								<div class="div_typename div_font" ></div>
							</div>
						</li>

						<li style="background: #F7F7F5">
							<div class="shop_skin_index_list" rel="edit-t18" no="17">
								<div class="img foot"></div>
							</div>
							<div class="name shop_skin_index_list" rel="edit-t23" no="22">
								<div class="div_typename div_font" ></div>
							</div>
						</li>

					</ul>
				</div>
			</div>



    </div>
</div>

<?php }else if($template_id==44){?>
<link href="../../../Common/css/Base/home_decoration/fengge44/shop.css" rel="stylesheet" type="text/css">
<link href="../../../Common/css/Base/home_decoration/fengge44/index.css?ver=<?php echo time();?>" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge44/base_index.css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge44/showcase_index.css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge44/index_36.css">
<link href="../../../Common/css/Base/home_decoration/fengge44/PreFoot.css" rel="stylesheet" type="text/css">


 <div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
    <div class="header" style="padding:0px;height:160px">
    	<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
        	<div class="img">
			  <img src="../../../Common/css/Base/home_decoration/yzzj2/photo.jpg">
			</div>
			<div class="mod" style="display: none;height:160px">&nbsp;</div>
        </div>
    </div>

 	<div class="marquee shop_skin_index_list"  rel="edit-t18" no="17">
		<div class="div_typename r_color" style="color:#e60014"></div>
	</div>
    	<style>
        .app-preview-anmin .img{width:40px;height:40px;margin:0 auto;}
        </style>
     <div id="app-field-model-page-1" style="width:100%">
        <div class="app-field clearfix clearfix_list b_white app-preview-anmin"><!--icon开始-->
            <div style="height: 10px;"></div>

            <div style="width:25%;float:left;text-align:center;">
					<div class="shop_skin_index_list" rel="edit-t02" no="1" >
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t10" no="9">
						<div class="div_typename r_color"></div>
					</div>


            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t03" no="2" >
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t11" no="10">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t04" no="3">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t12" no="11">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
               <div class="shop_skin_index_list" rel="edit-t05" no="4">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t13" no="12">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t06" no="5">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t14" no="13">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t07" no="6">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t15" no="14">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t08" no="7">
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t16" no="15">
						<div class="div_typename r_color"></div>
					</div>
            </div>
            <div style="width:25%;float:left;text-align:center;">
                <div class="shop_skin_index_list" rel="edit-t09" no="8" >
					<div class="img"></div>
					</div>
                     <div class="name shop_skin_index_list" rel="edit-t17" no="16">
						<div class="div_typename r_color"></div>
					</div>
            </div>
    	</div>
    </div>
    <style>
    	.video_class{width:100%;height:250px;}
    </style>
	<div class="shop_skin_index_list" rel="edit-t31" no="30"><!--添加视频-->
    	<div class="div_typevideo"></div>
	</div>

            <div class="shop_skin_index_list banner" rel="edit-t19" no="18" style="height:100px;">
                <div class="img" style="height:100px;">

                </div>
                <div class="mod" style="display: none;height:100px">&nbsp;</div>
            </div>

        	<div style="height:10px;display:block;"></div>
        	<div class="shop_skin_index_list banner" rel="edit-t20" no="19" style="height:100px;">
                <div class="img" style="height:100px;">

                </div>
                <div class="mod" style="display: none;height:100px">&nbsp;</div>
            </div>

            <style>
            .brand_list ul li{list-style:none;}
			.brand_list li{float:left;}
			.brand_list li img{width:160px;height:107px;}
			.brand_head{height:20px;line-height:20px;position:relative}
			.brand_head .cat_name{float:left;font-size:14px;font-weight:bold;color:#000;margin-left:15px;}
			.brand_head .more{float:right;font-size:12px;color:#000;margin-right:5px;}
            </style>

            <div class="app-field clearfix clearfix_list  b_white app-preview-anmin" style="border-bottom:1px dashed #999;padding-bottom:4px;margin-bottom:5px;"><!--分类楼层开始-->
              <div style="height:10px"></div>
              <div style="width:100%;background-color:#fff">
                  <div style="height:5px"></div>
                      <div class="brand_head">
                          <span class="cat_name">名品专区</span>
                          <span class="more">更多>></span>
                      </div>
                      <div style="height:5px"></div>
                      <div class="brand_list">
                      	<ul>
                        	<li>
                            	<img src="../../../Common/css/Base/home_decoration/fengge37/images/brand1.png" >
                            </li>
                            <li>
                            	<img src="../../../Common/css/Base/home_decoration/fengge37/images/brand2.png" >
                            </li>
                            <li>
                            	<img src="../../../Common/css/Base/home_decoration/fengge37/images/brand3.png" >
                            </li>
                            <li>
                            	<img src="../../../Common/css/Base/home_decoration/fengge37/images/brand4.png" >
                            </li>

                        </ul>
                      </div>

               </div>
            </div>

            <div class="shop_skin_index_list banner" rel="edit-t22" no="21" style="height:100px;">
                <div class="img" style="height:100px;">

                </div>
                <div class="mod" style="display: none;height:100px">&nbsp;</div>
            </div>


            <div class="app-field clearfix clearfix_list  b_white app-preview-anmin"><!--搜索开始-->
                <div class="custom-search">
                  <form>
                    <input type="search" class="custom-search-input" placeholder="商品搜索：请输入商品关键字" name="searchname"  id="searchname" value="" onkeydown="javascript:if(event.keyCode==13){search_name();return false;}">
                    <button type="button" onclick="search_name()" class="custom-search-button">搜索</button>
                  </form>
            	</div>
         	</div>

			<div  class="clear"></div>
            <div class="button_tab_menu">
                <!--distribution contact us end-->
                <dl class="sub-nav nav-b5">
                    <dd class="active">
                        <div class="nav-b5-relative shop_skin_index_list" rel="edit-t23" no="22"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t27" no="26">
                    <div class="div_typename div_font" style="color:#2b9939"></div>
                </div>
                    </dd>
                    <dd>
                          <div class="nav-b5-relative shop_skin_index_list" rel="edit-t24" no="23"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t28" no="27">
                    <div class="div_typename div_font" ></div>
                </div>
                    </dd>
                    <dd>
                          <div class="nav-b5-relative shop_skin_index_list" rel="edit-t25" no="24"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t29" no="28">
                    <div class="div_typename div_font" ></div>
                </div>
                    </dd>
                    <dd>
                          <div class="nav-b5-relative shop_skin_index_list" rel="edit-t26" no="25"><div class="img"></div></div><div class="name shop_skin_index_list" rel="edit-t30" no="29">
                    <div class="div_typename div_font" ></div>
                </div>
                    </dd>
                </dl>
		</div>

    </div>
<?php }else if($template_id==45){?>
	<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
    <link href="../../../Common/css/Base/home_decoration/fengge45/css/index.css" rel="stylesheet" type="text/css">
	<link href="../../../Common/css/Base/home_decoration/fengge45/css/style.css" rel="stylesheet" type="text/css">
	<div id="shop_skin_index"   <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="shop_skin_index_list" rel="edit-t01" no="0" style="height:117px;">
			<div class="img"></div>
			<div class="mod" style="display: none;">&nbsp;</div>

		</div>
		<div class="title shop_skin_index_list" rel="edit-t02" no="1" >
			<div class="div_typename"></div>
		</div>

		<ul class="custom-coupon" style="float:right;">
            <li>
                <a href="##" class="js-select-coupon" style="text-decoration:none;">
                        <div class="shop_skin_index_list custom-coupon-price" rel="edit-t03" no="2" style="float:none;">
							<div class="div_typename" style="color:#fa5262 !important;"></div>
						</div>
                        <div class="shop_skin_index_list custom-coupon-desc" rel="edit-t04" no="3" style="float:none;">
							<div class="div_typename" style="color:#fa5262 !important;"></div>
						</div>
                </a>
            </li>
            <li>
                <a href="##" class="js-select-coupon" style="text-decoration:none;">
                        <div class="shop_skin_index_list custom-coupon-price" rel="edit-t05" no="4" style="float:none;">
							<div class="div_typename" style="color:#7acf8d !important;"></div>
						</div>
                        <div class="shop_skin_index_list custom-coupon-desc" rel="edit-t06" no="5" style="float:none;">
							<div class="div_typename" style="color:#7acf8d !important;"></div>
						</div>
                </a>
            </li>
            <li>
                <a href="##" class="js-select-coupon" style="text-decoration:none;">
                        <div class="shop_skin_index_list custom-coupon-price" rel="edit-t07" no="6" style="float:none;">
							<div class="div_typename" style="color:#ff9664 !important;"></div>
						</div>
                        <div class="shop_skin_index_list custom-coupon-desc" rel="edit-t08" no="7" style="float:none;">
							<div class="div_typename" style="color:#ff9664 !important;"></div>
						</div>
                </a>
            </li>
        </ul>
		<div class="notice shop_skin_index_list" rel="edit-t09" no="8" >
			<div class="div_typename notice" style="width: 300px;"></div>
		</div>
		<div>
			<div class="shop_skin_index_list i0" rel="edit-t10" no="9">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
			<div class="shop_skin_index_list i0" rel="edit-t11" no="10">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
			<div class="shop_skin_index_list i0" rel="edit-t12" no="11">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
			<div class="shop_skin_index_list i0" rel="edit-t13" no="12">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
		</div>
		<div class="shop_skin_index_list" rel="edit-t14" no="13">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t15" no="14" style="height:160px;">
			<div class="img"></div><div class="mod">&nbsp;</div>
			<div id="SetHomeCurrentBox" style="height: 150px; width: 310px;"></div>
		</div>
		<div class="shop_skin_index_list" style="width: 100%;">
			<hr style="border-top: 1px;border-top: 1px dashed #bbb;">
		</div>
		<div class="shop_skin_index_list" rel="edit-t16" no="15">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list" rel="edit-t17" no="16">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div>
			<div class="shop_skin_index_list i2" rel="edit-t18" no="17">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
			<div class="shop_skin_index_list i2" rel="edit-t19" no="18">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
		</div>
		<div class="shop_skin_index_list" style="width: 100%;">
			<hr style="border-top: 1px;border-top: 1px dashed #bbb;">
		</div>
		<div class="img" style="height:230px;float:left;"><img src="../../../Common/css/Base/home_decoration/fengge31/img/pic2.png"></div>


	</div>


<?php }else if($template_id==46){?>
	<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/fengge46/css/global.css">
	<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/fengge46/css/style.css">
	<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div class="mp-page1" id="main-page1">
    <header class="mpg-navbar">
        <div class="mpg-nav-wrap-left">
            <div class="mpg-nav-wrap-left">
                <a class="mpg-header-back text-icon"  id="msgoback"></a>
            </div>
        </div>
        <div class="mpg-box-search">
            <a class="mpg-linkarea">
                <i class="icon-search text-icon"></i>
				<span class="single-line">
				<div class="shop_skin_index_list" rel="edit-t20" no="19">
					<div class="div_typename" ></div>
				</div>
				</span>
            </a>
        </div>
        <div class="mpg-nav-wrap-right">
            <a class="mpg-linkarea">
                <span class="nav-city">北京<i class="text-icon"></i></span>
            </a>
        </div>
    </header>
	<style>
		.banner img{width:320px;height:100px;}

	</style>
   <div class="mp-main" id="mp-main">

        <div class="mp-image-container"><div id="img-slider" class="mpw-swipe" data--swipe="[object Object]" style="visibility: visible;">
			<div class="mpw-swipe-wrap">
				<div class="shop_skin_index_list banner" style="min-height:100px;" rel="edit-t01" no="0">
				<div class="img"></div>
				<div class="mod" style="display: none;">&nbsp;</div>
			</div>
			</div>
		</div>
	</div>
	<div class="mp-category-container mpw-swipe" id="category-container" data--swipe="[object Object]" style="visibility: visible;">
		<div class="mpw-swipe-wrap">
			<div class="mpw-swipe-item">

				<div class="mp-category-item">
					<div class="shop_skin_index_list" rel="edit-t02" no="1">
						<div class="img"></div>
					</div>
					<div class="name shop_skin_index_list" rel="edit-t10" no="9">
                        <p class="keywords"><div class="div_typename div_font" ></div></p>
                    </div>

				</div>
				<div class="mp-category-item">
					<div class="shop_skin_index_list" rel="edit-t03" no="2">
						<div class="img"></div>
					</div>
					<div class="name shop_skin_index_list" rel="edit-t11" no="10">
                       <p class="keywords"><div class="div_typename div_font" ></div></p>
                    </div>

				</div>
				<div class="mp-category-item">
					<div class="shop_skin_index_list" rel="edit-t04" no="3">
						<div class="img"></div>
					</div>
					<div class="name shop_skin_index_list" rel="edit-t12" no="11">
                        <p class="keywords"><div class="div_typename div_font" ></div></p>
                    </div>

				</div>
				<div class="mp-category-item">
					<div class="shop_skin_index_list" rel="edit-t05" no="4">
						<div class="img"></div>
					</div>
					<div class="name shop_skin_index_list" rel="edit-t13" no="12">
                        <p class="keywords"><div class="div_typename div_font" ></div></p>
                    </div>

				</div>
				<div class="mp-category-item">
					<div class="shop_skin_index_list" rel="edit-t06" no="5">
						<div class="img"></div>
					</div>
					<div class="name shop_skin_index_list" rel="edit-t14" no="13">
                        <p class="keywords"><div class="div_typename div_font" ></div></p>
                    </div>

				</div>
				<div class="mp-category-item">
					<div class="shop_skin_index_list" rel="edit-t07" no="6">
						<div class="img"></div>
					</div>
					<div class="name shop_skin_index_list" rel="edit-t15" no="14">
                        <p class="keywords"><div class="div_typename div_font" ></div></p>
                    </div>

				</div>
				<div class="mp-category-item">
					<div class="shop_skin_index_list" rel="edit-t08" no="7">
						<div class="img"></div>
					</div>
					<div class="name shop_skin_index_list" rel="edit-t16" no="15">
                       <p class="keywords"><div class="div_typename div_font" ></div></p>
                    </div>

				</div>
				<div class="mp-category-item">
					<div class="shop_skin_index_list" rel="edit-t09" no="8">
						<div class="img"></div>
					</div>
					<div class="name shop_skin_index_list" rel="edit-t17" no="16">
                        <p class="keywords"><div class="div_typename div_font" ></div></p>
                    </div>

				</div>


			</div>
		</div>
	</div>
<div class="mp-hot">
    <h2 class="mp-modtitle">
		<div class="name shop_skin_index_list" rel="edit-t18" no="17">
            <div class="div_typename div_font" ></div>
        </div>
	</h2>
    <div class="mp-hot-con">
        <ul>
            <li class="mp-flexbox mp-hot-prod mp-border-bottom">
                <div class="mp-hotlist-img">
                    <img src="../../../Common/css/Base/home_decoration/fengge46/img/default.png">
                </div>
                <div class="mp-flexbox-item mp-hotlist-infos">
                    <div class="mp-hotlist-title">富国海底222世界2222</div>
                    <div class="mp-hotlist-desc">你来或不来，精彩就在这里</div>
                </div>
                <div class="mp-hotlist-price">¥<em>65</em><span class="mp-price-text">起</span></div>

            </li>
            <li class="mp-flexbox mp-hot-prod mp-border-bottom">
				<div style="display:block;width:100%;height:50px;">
					<span style="display:block;text-align:center;margin-top:15px;font-size:20px;font-weight:bold;">显示用户所在地区的热卖产品</span>
				</div>
            </li>
        </ul>
        <div class="mp-modmore">查看所有产品</div>
    </div>
</div><div class="mp-hot">
    <h2 class="mp-modtitle">
		<div class="name shop_skin_index_list" rel="edit-t19" no="18">
            <div class="div_typename div_font" ></div>
        </div>
	</h2>
    <div>
        <div class="mp-product-item">
                <div style="display:block;width:320px;height:180px;background:#d9d9d9;">
					<span style="text-align:center;line-height:80px;font-size:16px;display:block;">一级分类广告上传区域</span><br>
					<span style="text-align:center;line-height:30px;font-size:16px;display:block;">分类广告图上传尺寸：1024px*576px</span>
				</div>
        </div>
    </div>
</div></div>

	</div>
	</div>

<?php }else if($template_id==47){?>
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/
fengge47/css/style.css">
<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div class="main">
		<div class="search-box">
			<div class="shop_skin_index_list"  rel="edit-t01" no="0">
				<div class="img"></div>

			</div>
			<div class="search-layer">
				<input type="text" class="search-input" placeholder="正品保障，私密发货" style="border-radius:3px;">
				<button class="search-button"></button>
			</div>
		</div>

		<div class="shop_skin_index_list banner"  rel="edit-t02" no="1" style="height: 160px;">
			<div class="img"></div>
			<div class="mod" style="display: none;">&nbsp;</div>
		</div>

		<div class="content-box">
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t03" no="2">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t11" no="10">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t04" no="3">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t12" no="11">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t05" no="4">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t13" no="12">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t06" no="5">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t14" no="13">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t07" no="6">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t15" no="14">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t08" no="7">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t16" no="15">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t09" no="8">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t17" no="16">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t10" no="9">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t18" no="17">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="clear"></div>





		</div>
		<div class="img-box">
			<div class="shop_skin_index_list left-img" rel="edit-t19" no="18">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list right-top-img" rel="edit-t20" no="19">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list right-bottom-img" rel="edit-t21" no="20">
				<div class="img"></div>
			</div>


			<div class="clear"></div>
		</div>



		<div class="product-box">
			<img src="../../../Common/css/Base/home_decoration/fengge47/images/title.png" class="title">
			<span class="more">更多>></span>
			<!--<img src="fengge47/images/banner.jpg" class="banner">-->
			<div style="display:block;background-color:#999;color:#fff;height:108px;width:320px;float:left;border-bottom:1px solid #fff;">
				<span style="text-align:center;display:block;width:100%;margin-top:38px;">一级分类上传图片尺寸：1080*340</span>
			</div>
			<div class="left-product" style="display:block;background-color:#999;color:#fff;height:160px;width:160px;float:left;">
				<span style="text-align:center;display:block;width:100%;margin-top:60px;">二级分类上传图片尺寸：450*450</span>
			</div>
			<div class="right-product" style="display:block;background-color:#999;color:#fff;height:160px;width:160px;float:left;">
				<span style="text-align:center;display:block;width:100%;margin-top:60px;">二级分类上传图片尺寸：450*450</span>
			</div>
			<div class="three-product border-right" style="display:block;background-color:#999;color:#fff;height:106px;width:106px;float:left;">
				<span style="text-align:center;display:block;width:100%;margin-top:16px;">二级分类<br>上传图片尺寸：450*450</span>
			</div>
			<div class="three-product border-right" style="display:block;background-color:#999;color:#fff;height:106px;width:106px;float:left;">
				<span style="text-align:center;display:block;width:100%;margin-top:16px;">二级分类<br>上传图片尺寸：450*450</span>
			</div>
			<div class="three-product" style="display:block;background-color:#999;color:#fff;height:106px;width:106px;float:left;">
				<span style="text-align:center;display:block;width:100%;margin-top:16px;">二级分类<br>上传图片尺寸：450*450</span>
			</div>
			<!--
			<img src="fengge47/images/product-1.png" class="left-product">
			<img src="fengge47/images/product-1.png" class="right-product">
			<img src="fengge47/images/product-2.png" class="three-product border-right">
			<img src="fengge47/images/product-2.png" class="three-product border-right">
			<img src="fengge47/images/product-2.png" class="three-product">
			-->
		</div>
		<div style="display:block;overflow:hidden;width:320px;">
			<img src="../../../Common/css/Base/home_decoration/fengge47/images/ad-1.jpg" class="w50-img">
			<img src="../../../Common/css/Base/home_decoration/fengge47/images/ad-1.jpg" class="w50-img">
		</div>
		<div class="shop_skin_index_list" rel="edit-t30" no="29">
			<div class="img w100-img"></div>
		</div>
		<div class="footer">
			<div class="footer-box">
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t22" no="21">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t26" no="25">
					   <p><div class="div_typename" ></div></p>
					</div>
				</div>
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t23" no="22">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t27" no="26">
					   <p><div class="div_typename" ></div></p>
					</div>
				</div>
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t24" no="23">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t28" no="27">
					   <p><div class="div_typename" ></div></p>
					</div>
				</div>
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t25" no="24">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t29" no="28">
					   <p><div class="div_typename" ></div></p>
					</div>
				</div>
			</div>
		</div>
	</div>

</div><!--shop_skin_index end-->

<?php }else if($template_id==48){?>
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge48/shop.css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge48/index.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/fengge48/amazeui.css">
<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>;width:320px;"<?php } ?> >
	<div class="am-g am-g-collapse" style="height:45px;width:100%;">
		<section class="mix_new_header">

				<form method="post"  name="searchForm" id="searchForm_id">
					<div class="search">
						<div class="text_box">
							<input id="keyword" name="keywords" type="text" value="" placeholder="搜索商品、品牌、种类" class="keyword text" onkeydown="this.style.color=&#39;#404040&#39;;" maxlength="70" autocomplete="off">
						</div>
						<span class="mix_submit"></span>
						<a href="javascript:return check('keywordfoot');" class="clear_input" id="clear_input" style="display: block;"></a>
					</div>

				</form>
		</section>
	</div>

	<style>
		.div_font{height:20px;}
	</style>
	<div class="shop_skin_index_list banner"  rel="edit-t01" no="0" style="height:160px;">
		<div class="img"></div>
	</div>
	<div class="am-g ct bf ade mt8">
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t02" no="1">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list div_font" rel="edit-t10" no="9">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t03" no="2">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list div_font" rel="edit-t11" no="10">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t04" no="3">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list div_font" rel="edit-t12" no="11">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t05" no="4">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list div_font" rel="edit-t13" no="12">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t06" no="5">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list div_font" rel="edit-t14" no="13">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t07" no="6">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list div_font" rel="edit-t15" no="14">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t08" no="7">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list div_font" rel="edit-t16" no="15">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t09" no="8">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list div_font" rel="edit-t17" no="16">
				<div class="div_typename" ></div>
			</div>
		</div>
	</div>
	<div class="shu3 mt8">
		<ul>
			<li>
				<div class="shop_skin_index_list" rel="edit-t18" no="17">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t19" no="18">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t20" no="19">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t21" no="20">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t22" no="21">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t23" no="22">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t24" no="23">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t25" no="24">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t26" no="25">
					<div class="img"></div>
				</div>
			</li>


		</ul>
	</div>
	<div class="clear"></div>

	<div class="floor hot-floor" style="margin-top:10px;">
		<div class="shop_skin_index_list" rel="edit-t27" no="26">
			<div class="img"></div>
		</div>
	</div>
	<div class="floor hot-floor">
		<div class="shop_skin_index_list" rel="edit-t28" no="27">
			<div class="img"></div>
		</div>
	</div>
	<div class="floor hot-floor">
		<div class="shop_skin_index_list" rel="edit-t29" no="28">
			<div class="img"></div>
		</div>
	</div>
	<div class="floor hot-floor">
		<div class="shop_skin_index_list" rel="edit-t30" no="29">
			<div class="img"></div>
		</div>
	</div>
	<div class="floor hot-floor">
		<div class="shop_skin_index_list" rel="edit-t31" no="30">
			<div class="img"></div>
		</div>
	</div>

	<div class="yuangg">


		<div style="display:block;background:#9d9d9d;width:313px;height:133px;border-bottom:1px solid #fff;">
			<span style="color:#fff;line-height:120px;width:100%;display:block;text-align:center;">一级分类图片上传尺寸：720px*306px</span>
		</div>

		<div style="display:block;background:#9d9d9d;width:103px;height:125px;float:left;border-right:1px solid #fff;">
			<span style="color:#fff;line-height:30px;width:100%;display:block;text-align:center;">二级分类图片<br>上传尺寸：350px*417px</span>
		</div>

		<div style="display:block;background:#9d9d9d;width:103px;height:125px;float:left;border-right:1px solid #fff;">
			<span style="color:#fff;line-height:30px;width:100%;display:block;text-align:center;">二级分类图片<br>上传尺寸：350px*417px</span>
		</div>

		<div style="display:block;background:#9d9d9d;width:104px;height:125px;float:left;">
			<span style="color:#fff;line-height:30px;width:100%;display:block;text-align:center;">二级分类图片<br>上传尺寸：350px*417px</span>
		</div>


	   <div class="clear"></div>
	</div>

	<!--<div style="height:10px;width:100%;"></div>-->
	<div class="sstfo">
		<ul>
			<li>
				<div class="shop_skin_index_list" rel="edit-t32" no="31">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list div_font" rel="edit-t36" no="35">
					<div class="div_typename" ></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t33" no="32">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list div_font" rel="edit-t37" no="36">
					<div class="div_typename" ></div>
				</div>
			</li>
		   <li>
				<div class="shop_skin_index_list" rel="edit-t34" no="33">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list div_font" rel="edit-t38" no="37">
					<div class="div_typename" ></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t35" no="34">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list div_font" rel="edit-t39" no="38">
					<div class="div_typename" ></div>
				</div>
			</li>

		</ul>
	</div>


</div>

<?php }else if($template_id==49){?>
	<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
    <link href="../../../Common/css/Base/home_decoration/fengge45/css/index.css" rel="stylesheet" type="text/css">
	<link href="../../../Common/css/Base/home_decoration/fengge45/css/style.css" rel="stylesheet" type="text/css">
	<div id="shop_skin_index"   <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
		<div class="shop_skin_index_list" rel="edit-t01" no="0" style="height:325px;">
			<div class="img"></div>
			<div class="mod" style="display: none;">&nbsp;</div>

		</div>
		<div class="title shop_skin_index_list" rel="edit-t02" no="1" >
			<div class="div_typename"></div>
		</div>

		<ul class="custom-coupon" style="float:right;">
            <li>
                <a href="##" class="js-select-coupon" style="text-decoration:none;">
                        <div class="shop_skin_index_list custom-coupon-price" rel="edit-t03" no="2" style="float:none;">
							<div class="div_typename" style="color:#fa5262 !important;"></div>
						</div>
                        <div class="shop_skin_index_list custom-coupon-desc" rel="edit-t04" no="3" style="float:none;">
							<div class="div_typename" style="color:#fa5262 !important;"></div>
						</div>
                </a>
            </li>
            <li>
                <a href="##" class="js-select-coupon" style="text-decoration:none;">
                        <div class="shop_skin_index_list custom-coupon-price" rel="edit-t05" no="4" style="float:none;">
							<div class="div_typename" style="color:#7acf8d !important;"></div>
						</div>
                        <div class="shop_skin_index_list custom-coupon-desc" rel="edit-t06" no="5" style="float:none;">
							<div class="div_typename" style="color:#7acf8d !important;"></div>
						</div>
                </a>
            </li>
            <li>
                <a href="##" class="js-select-coupon" style="text-decoration:none;">
                        <div class="shop_skin_index_list custom-coupon-price" rel="edit-t07" no="6" style="float:none;">
							<div class="div_typename" style="color:#ff9664 !important;"></div>
						</div>
                        <div class="shop_skin_index_list custom-coupon-desc" rel="edit-t08" no="7" style="float:none;">
							<div class="div_typename" style="color:#ff9664 !important;"></div>
						</div>
                </a>
            </li>
        </ul>
		<div class="notice shop_skin_index_list" rel="edit-t09" no="8" >
			<div class="div_typename notice" style="width: 300px;"></div>
		</div>
		<div>
			<div class="shop_skin_index_list i0" rel="edit-t10" no="9">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
			<div class="shop_skin_index_list i0" rel="edit-t11" no="10">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
			<div class="shop_skin_index_list i0" rel="edit-t12" no="11">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
			<div class="shop_skin_index_list i0" rel="edit-t13" no="12">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
		</div>
		<div class="shop_skin_index_list" rel="edit-t14" no="13">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t15" no="14" style="height:160px;">
			<div class="img"></div><div class="mod">&nbsp;</div>
			<div id="SetHomeCurrentBox" style="height: 150px; width: 310px;"></div>
		</div>
		<div class="shop_skin_index_list" style="width: 100%;">
			<hr style="border-top: 1px;border-top: 1px dashed #bbb;">
		</div>
		<div class="shop_skin_index_list" rel="edit-t16" no="15">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div class="shop_skin_index_list" rel="edit-t17" no="16">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<div>
			<div class="shop_skin_index_list i2" rel="edit-t18" no="17">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
			<div class="shop_skin_index_list i2" rel="edit-t19" no="18">
				<div class="img"></div><div class="mod" style="display: none;">&nbsp;</div>
			</div>
		</div>
		<div class="shop_skin_index_list" style="width: 100%;">
			<hr style="border-top: 1px;border-top: 1px dashed #bbb;">
		</div>
		<div class="img" style="height:230px;float:left;"><img src="../../../Common/css/Base/home_decoration/fengge31/img/pic2.png"></div>


	</div>
<?php }else if($template_id==50){?>
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge48/shop.css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge48/index.css">
<link href="../../../Common/css/Base/home_decoration/fengge38/css/style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/fengge48/amazeui.css">
<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>;width:320px;"<?php } ?> >
	<div class="am-g am-g-collapse" style="height:45px;width:100%;">
		<section class="mix_new_header">

				<form method="post"  name="searchForm" id="searchForm_id">
					<div class="search">
						<div class="text_box">
							<input id="keyword" name="keywords" type="text" value="" placeholder="搜索商品、品牌、种类" class="keyword text" onkeydown="this.style.color=&#39;#404040&#39;;" maxlength="70" autocomplete="off">
						</div>
						<span class="mix_submit"></span>
						<a href="javascript:return check('keywordfoot');" class="clear_input" id="clear_input" style="display: block;"></a>
					</div>

				</form>
		</section>
	</div>


	<div class="shop_skin_index_list banner"  rel="edit-t01" no="0">
		<div class="img"></div>
	</div>
	<style>
	.marquee{height: 30px;line-height: 30px;font-size: 100%;border-top:1px solid #999;border-bottom:1px solid #999;margin-top:3px;}
	.marquee *{font-size:14px;color:#CE8D15;}
	</style>
	<div class="marquee shop_skin_index_list"  rel="edit-t02" no="1">
		<div class="div_typename r_color" style="color:#e60014"></div>
	</div>
	<div class="am-g ct bf ade mt8">
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t03" no="2">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t11" no="10">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t04" no="3">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t12" no="11">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t05" no="4">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t13" no="12">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t06" no="5">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t14" no="13">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t07" no="6">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t15" no="14">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t08" no="7">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t16" no="15">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t09" no="8">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t17" no="16">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="am-u-sm-3">
			<div class="shop_skin_index_list" rel="edit-t10" no="9">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t42" no="41">
				<div class="div_typename" ></div>
			</div>
		</div>
	</div>

	<style>
    	.video_class{width:100%;height:250px;}
    </style>
	<div class="shop_skin_index_list" rel="edit-t19" no="18"><!--添加视频-->
    	<div class="div_typevideo"></div>
	</div>
	<div class="shu3 mt8">
		<ul>
			<li>
				<div class="shop_skin_index_list" rel="edit-t20" no="19">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t21" no="20">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t22" no="21">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t23" no="22">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t24" no="23">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t25" no="24">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t26" no="25">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t27" no="26">
					<div class="img"></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t28" no="27">
					<div class="img"></div>
				</div>
			</li>

		</ul>
	</div>
	<div class="clear"></div>

	<div class="floor hot-floor" style="margin-top:10px;">
		<div class="shop_skin_index_list" rel="edit-t29" no="28">
			<div class="img"></div>
		</div>
	</div>
	<div class="floor hot-floor">
		<div class="shop_skin_index_list" rel="edit-t30" no="29">
			<div class="img"></div>
		</div>
	</div>
	<div class="floor hot-floor">
		<div class="shop_skin_index_list" rel="edit-t31" no="30">
			<div class="img"></div>
		</div>
	</div>
	<div class="floor hot-floor">
		<div class="shop_skin_index_list" rel="edit-t32" no="31">
			<div class="img"></div>
		</div>
	</div>
	<div class="floor hot-floor">
		<div class="shop_skin_index_list" rel="edit-t33" no="32">
			<div class="img"></div>
		</div>
	</div>
	<div class="yuangg">


		<div style="display:block;background:#9d9d9d;width:313px;height:133px;border-bottom:1px solid #fff;">
			<span style="color:#fff;line-height:120px;width:100%;display:block;text-align:center;">一级分类图片上传尺寸：720px*306px</span>
		</div>

		<div style="display:block;background:#9d9d9d;width:103px;height:125px;float:left;border-right:1px solid #fff;">
			<span style="color:#fff;line-height:30px;width:100%;display:block;text-align:center;">二级分类图片<br>上传尺寸：350px*417px</span>
		</div>

		<div style="display:block;background:#9d9d9d;width:103px;height:125px;float:left;border-right:1px solid #fff;">
			<span style="color:#fff;line-height:30px;width:100%;display:block;text-align:center;">二级分类图片<br>上传尺寸：350px*417px</span>
		</div>

		<div style="display:block;background:#9d9d9d;width:104px;height:125px;float:left;">
			<span style="color:#fff;line-height:30px;width:100%;display:block;text-align:center;">二级分类图片<br>上传尺寸：350px*417px</span>
		</div>


	   <div class="clear"></div>
	</div>
	<!--<div style="height:10px;width:100%;"></div>-->
	<div class="sstfo">
		<ul>
		   <li>
				<div class="shop_skin_index_list" rel="edit-t34" no="33">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t38" no="37">
					<div class="div_typename" ></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t35" no="34">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t39" no="38">
					<div class="div_typename" ></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t36" no="35">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t40" no="39">
					<div class="div_typename" ></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t37" no="36">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t41" no="40">
					<div class="div_typename" ></div>
				</div>
			</li>
		</ul>
	</div>


</div>

<?php }else if($template_id==51){?>
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge51/css/style.css">
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>;width:320px;"<?php } ?> >
	<div class="main">
		<div class="search-box">
			<!--<div class="shop_skin_index_list  left-img" rel="edit-t01" no="0">
				<div class="img" ></div>
			</div>
			-->
			<div class="search-layer">
				<input type="text" class="search-input" placeholder="" >
				<button class="search-button"></button>
			</div>
			<!--
			<div class="shop_skin_index_list right-img" rel="edit-t02" no="1">
				<div class="img"></div>
			</div>
			-->
			<div style="position:absolute;right:0px;margin-right:60px;color:#9d9d9d;margin-top:19px;">用户昵称</div>
			<div style="position:absolute;right:0px;margin-top:5px;margin-right:5px;width:50px;z-index:99;"><img src="../../../Common/css/Base/home_decoration/fengge51/images/logo.jpg"></div>
		</div>
		<style>
			.f1{float:left;text-align:center;width:100%;display:block;line-height:40px;height:40px;}
		</style>
		<div class="left-side">
			<div class="title-1">
			<div class="tbox-1">

				<span style="">
					<div class="shop_skin_index_list f1" rel="edit-t04" no="3">
						<div class="div_typename" ></div>
					</div>
					<div class="shop_skin_index_list f1" rel="edit-t16" no="15">
						<div class="div_typename" ></div>
					</div>

					<div class="shop_skin_index_list f1" rel="edit-t17" no="16">
						<div class="div_typename" ></div>
					</div>

					<div class="shop_skin_index_list f1" rel="edit-t18" no="17">
						<div class="div_typename" ></div>
					</div>
				</span>



			</div>
			</div>
			<div class="title-2">
				<div class="t-box-2">
					<span style="">
						<div class="shop_skin_index_list f1" rel="edit-t19" no="18">
							<div class="div_typename" ></div>
						</div>
						<div class="shop_skin_index_list f1" rel="edit-t20" no="19">
							<div class="div_typename" ></div>
						</div>


					</span>



					<!--<span class="icon-up"></span>-->
				</div>
				<div class="con">优选水果</div>
				<div class="con">优选水果2</div>
				<div class="con">优选水果3</div>
				<div class="con">优选水果4</div>
				<div class="con">优选水果5</div>
				<div class="con">优选水果6</div>
				<div class="con">优选水果7</div>
				<div class="con">优选水果8</div>
				<div class="con">优选水果9</div>
				<div class="con">优选水果00</div>
				<div class="con">优选水果10</div>
				<div class="con">优选水果11</div>
				<div class="con">优选水果12</div>
				<div class="con">优选水果13</div>
				<div class="con">优选水果14</div>
				<div class="con">优选水果15</div>
				<div class="clear-fix50"></div>
			</div>
		</div>
		<div class="right-side">
			<div id="part1">
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t06" no="5">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t07" no="6">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t08" no="7">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t09" no="8">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t10" no="9">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t11" no="10">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t12" no="11">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t13" no="12">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t14" no="13">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t15" no="14">
						<div class="img"></div>
					</div>
				</div>
				<!--
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t21" no="20">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t22" no="21">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t23" no="22">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t24" no="23">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t25" no="24">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t26" no="25">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t27" no="26">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t28" no="27">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t29" no="28">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t30" no="29">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t31" no="30">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t32" no="31">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t33" no="32">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t34" no="33">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t35" no="34">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t36" no="35">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t37" no="36">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t38" no="37">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t39" no="38">
						<div class="img"></div>
					</div>
				</div>
				<div class="d-box">
					<div class="shop_skin_index_list" rel="edit-t40" no="39">
						<div class="img"></div>
					</div>
				</div>
				-->
				<div class="clear-fix50"></div>
			</div>
			<div id="part2">
				<div class="pro-box">
					<img src="../../../Common/css/Base/home_decoration/fengge51/images/apple.jpg" class="i1">
					<div class="add"><img src="../../../Common/css/Base/home_decoration/fengge51/images/add.png"></div>
				</div>
				<div class="pro-box">
					<img src="../../../Common/css/Base/home_decoration/fengge51/images/apple.jpg" class="i1">
					<div class="add"><img src="../../../Common/css/Base/home_decoration/fengge51/images/add.png"></div>
				</div>
				<div class="pro-box">
					<img src="../../../Common/css/Base/home_decoration/fengge51/images/apple.jpg" class="i1">
					<div class="add"><img src="../../../Common/css/Base/home_decoration/fengge51/images/add.png"></div>
				</div>
				<div class="pro-box">
					<img src="../../../Common/css/Base/home_decoration/fengge51/images/apple.jpg" class="i1">
					<div class="add"><img src="../../../Common/css/Base/home_decoration/fengge51/images/add.png"></div>
				</div>
				<div class="pro-box">
					<img src="../../../Common/css/Base/home_decoration/fengge51/images/apple.jpg" class="i1">
					<div class="add"><img src="../../../Common/css/Base/home_decoration/fengge51/images/add.png"></div>
				</div>
				<div class="pro-box">
					<img src="../../../Common/css/Base/home_decoration/fengge51/images/apple.jpg" class="i1">
					<div class="add"><img src="../../../Common/css/Base/home_decoration/fengge51/images/add.png"></div>
				</div>
				<div class="pro-box">
					<img src="../../../Common/css/Base/home_decoration/fengge51/images/apple.jpg" class="i1">
					<div class="add"><img src="../../../Common/css/Base/home_decoration/fengge51/images/add.png"></div>
				</div>
				<div class="clear-fix50"></div>
			</div>
		</div>
		<div class="bottom-side">
			<span class="cart">
				<div class="shop_skin_index_list" rel="edit-t03" no="2">
					<div class="img"></div>
				</div>
		   </span>

		   <p class="cart-text">购物车是空的</br></p>
		   <!--<p class="right-text">满￥0起送</p>-->
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$(window).resize(function(){
		var wh=$(window).height()-43;
		$('.left-side').height(480);
		$('.right-side').height(480);
	});

	$(window).resize();
        $(".con").click(function(){
            $(this).addClass('foc').siblings('.con').removeClass('foc');
            $('.tbox-1').addClass('grey-bg');
            $('#part1').hide();
            $('#part2').show();
            $('.title-2').addClass('br0');
            $('#part2').show();
            $('#part1').hide();
        });
        $('.tbox-1').click(function(){
            $('.tbox-1').removeClass('grey-bg');
            $('.title-2').removeClass('br0');
            $('.con').removeClass('foc');
            $('#part1').show();
            $('#part2').hide();
        });
        $('.tbox-2').click(function(){
            $('.icon-up').toggleClass('up-down');
            $('.con-box').slideToggle();
        })
})
</script>

<?php }else if($template_id==52){?>
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge52/css/style.css">
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>;width:320px;"<?php } ?> >
<style>
	.banner,.scroll{height:160px;}
	.banner-b{height:60px;}
	.cl-box img{height:64px;display: inline-block;}
	.de-r,.dh-img{height:75px;}
	.de100,.de-l{height:150px}
	.de25{height:79px;}
	.con4,.con3{height:96px;}
	.con2{height:66px;}
	.con1{height:163px;}
</style>
<div class="main">
	<div class="title" id="title"><!--搜索开始-->
		<div class="shop_skin_index_list  img1" rel="edit-t01" no="0">
			<div class="img" ></div>
		</div>
		<div class="search-layer">
			<input type="text" class="search-input" placeholder="快时尚社交电商平台" >
			<button class="search-button"></button>
		</div>
		<div class="shop_skin_index_list  img3" rel="edit-t02" no="1">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list  img2" rel="edit-t03" no="2">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list  img-bg" rel="edit-t04" no="3">
			<div class="img" ></div>
		</div>

	</div><!--搜索结束-->
	<div class="shop_skin_index_list  scroll" rel="edit-t05" no="4" style="height:160px;">
		<div class="img" ></div>
	</div>

	<div class="cl-box"><!--图标开始-->
		<div class="cl">
			<div class="shop_skin_index_list" rel="edit-t06" no="5">
				<div class="img" ></div>
			</div>
			<!--<p>
				<div class="shop_skin_index_list" rel="edit-t11" no="10">
					<div class="div_typename" ></div>
				</div>
			</p>
			-->
		</div>
		<div class="cl">
			<div class="shop_skin_index_list" rel="edit-t07" no="6">
				<div class="img" ></div>
			</div>
			<!--
			<p>
				<div class="shop_skin_index_list" rel="edit-t12" no="11">
					<div class="div_typename" ></div>
				</div>
			</p>
			-->
		</div>
		<div class="cl">
			<div class="shop_skin_index_list" rel="edit-t08" no="7">
				<div class="img" ></div>
			</div>
			<!--
			<p>
				<div class="shop_skin_index_list" rel="edit-t13" no="12">
					<div class="div_typename" ></div>
				</div>
			</p>
			-->
		</div>
		<div class="cl">
			<div class="shop_skin_index_list" rel="edit-t09" no="8">
				<div class="img" ></div>
			</div>
			<!--
			<p>
				<div class="shop_skin_index_list" rel="edit-t14" no="13">
					<div class="div_typename" ></div>
				</div>
			</p>
			-->
		</div>
		<div class="cl">
			<div class="shop_skin_index_list" rel="edit-t10" no="9">
				<div class="img" ></div>
			</div>
			<!--
			<p>
				<div class="shop_skin_index_list" rel="edit-t15" no="14">
					<div class="div_typename" ></div>
				</div>
			</p>
			-->
		</div>
		<div class="clear"></div>
	</div><!--图标结束-->

	<div class="con-box"><!--广告1开始-->
		<div class="shop_skin_index_list con1" rel="edit-t16" no="15">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list con2" rel="edit-t17" no="16">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list con3" rel="edit-t18" no="17">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list con4" rel="edit-t19" no="18">
			<div class="img" ></div>
		</div>

		<div class="clear"></div>
	</div><!--广告1结束-->
	<!--10张广告图开始-->
	<div class="shop_skin_index_list banner" rel="edit-t20" no="19">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t21" no="20">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t22" no="21">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t23" no="22">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t24" no="23">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t25" no="24">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t26" no="25">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t27" no="26">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t28" no="27">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t29" no="28">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t69" no="68">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t70" no="69">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t71" no="70">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t72" no="71">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t73" no="72">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t74" no="73">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t75" no="74">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t76" no="75">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t77" no="76">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t78" no="77">
		<div class="img" ></div>
	</div>

	<!--20个广告-->
	<!--
	<div class="shop_skin_index_list banner" rel="edit-t79" no="78">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t80" no="79">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t81" no="80">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t82" no="81">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t83" no="82">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t84" no="83">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t85" no="84">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t86" no="85">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t87" no="86">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t88" no="87">
		<div class="img" ></div>
	</div>
	-->
	<!--60个广告-->
	<!--
	<div class="shop_skin_index_list banner" rel="edit-t89" no="88">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t90" no="89">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t91" no="90">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t92" no="91">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t93" no="92">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t94" no="93">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t95" no="94">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t96" no="95">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t97" no="96">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t98" no="97">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t99" no="98">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t100" no="99">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t101" no="100">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t102" no="101">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t103" no="102">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t104" no="103">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t105" no="104">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t106" no="105">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t107" no="106">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t108" no="107">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t109" no="108">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t110" no="109">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t111" no="110">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t112" no="111">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t113" no="112">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t114" no="113">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t115" no="114">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t116" no="115">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t117" no="116">
		<div class="img" ></div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t118" no="117">
		<div class="img" ></div>
	</div>
	-->
	<!--只打包10个广告图！！！！！！！！！!!!!!-->
	<!--10张广告图结束-->
	<!--广告坑1开始-->
	<div class="shop_skin_index_list img-title" rel="edit-t30" no="29">
		<div class="img" ></div>
	</div>
	<div class="de-box">
		<div class="shop_skin_index_list de100" rel="edit-t31" no="30">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-l" rel="edit-t32" no="31">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t33" no="32">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t34" no="33">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t35" no="34">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t36" no="35">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t37" no="36">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t38" no="37">
			<div class="img" ></div>
		</div>
		<div class="clear"></div>
	</div>
	<!--广告坑1结束-->
	<!--广告坑2开始-->
	<div class="shop_skin_index_list img-title" rel="edit-t39" no="38">
		<div class="img" ></div>
	</div>
	<div class="de-box">
		<div class="shop_skin_index_list de100" rel="edit-t40" no="39">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-l" rel="edit-t41" no="40">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t42" no="41">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t43" no="42" >
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t44" no="43">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t45" no="44" >
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t46" no="45">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t47" no="46">
			<div class="img" ></div>
		</div>
		<div class="clear"></div>
	</div>
	<!--广告坑2结束-->
	<!--广告坑3开始-->
	<div class="shop_skin_index_list img-title" rel="edit-t48" no="47">
		<div class="img" ></div>
	</div>
	<div class="de-box">
		<div class="shop_skin_index_list de100" rel="edit-t49" no="48">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-l" rel="edit-t50" no="49">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t51" no="50">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t52" no="51">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t53" no="52">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t54" no="53">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t55" no="54">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t56" no="55">
			<div class="img" ></div>
		</div>
		<div class="clear"></div>
	</div>
	<!--广告坑3结束-->

	<!--广告坑6开始-->  <!--权客户要求加，请勿打包进去！！！！！1-->
	<!--
	<div class="shop_skin_index_list img-title" rel="edit-t119" no="118">
		<div class="img" ></div>
	</div>
	<div class="de-box">
		<div class="shop_skin_index_list de100" rel="edit-t120" no="119">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-l" rel="edit-t121" no="120">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t122" no="121">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t123" no="122">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t124" no="123">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t125" no="124">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t126" no="125">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t127" no="126">
			<div class="img" ></div>
		</div>
		<div class="clear"></div>
	</div>
	-->
	<!--广告坑6结束-->	 <!--权客户要求加，请勿打包进去！！！！！1-->
	<!--广告坑7开始-->  <!--权客户要求加，请勿打包进去！！！！！1-->
	<!--
	<div class="shop_skin_index_list img-title" rel="edit-t128" no="127">
		<div class="img" ></div>
	</div>
	<div class="de-box">
		<div class="shop_skin_index_list de100" rel="edit-t129" no="128">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-l" rel="edit-t130" no="129">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t131" no="130">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t132" no="131">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t133" no="132">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t134" no="133">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t135" no="134">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t136" no="135">
			<div class="img" ></div>
		</div>
		<div class="clear"></div>
	</div>
	-->
	<!--广告坑7结束-->	 <!--权客户要求加，请勿打包进去！！！！！1-->
	<!--广告坑8开始-->  <!--权客户要求加，请勿打包进去！！！！！1-->
	<!--
	<div class="shop_skin_index_list img-title" rel="edit-t137" no="136">
		<div class="img" ></div>
	</div>
	<div class="de-box">
		<div class="shop_skin_index_list de100" rel="edit-t138" no="137">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-l" rel="edit-t139" no="138">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t140" no="139">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de-r" rel="edit-t141" no="140">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t142" no="141">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t143" no="142">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t144" no="143">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list de25" rel="edit-t145" no="144">
			<div class="img" ></div>
		</div>
		<div class="clear"></div>
	</div>
	-->
	<!--广告坑8结束-->  <!--权客户要求加，请勿打包进去！！！！！1-->
	<!--广告坑4开始-->
	<div class="shop_skin_index_list img-title" rel="edit-t58" no="57">
		<div class="img" ></div>
	</div>
	<div class="dh-box">
		<div class="shop_skin_index_list dh-img" rel="edit-t59" no="58">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list dh-img" rel="edit-t60" no="59">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list dh-img" rel="edit-t61" no="60" >
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list dh-img" rel="edit-t62" no="61">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list dh-img" rel="edit-t63" no="62">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list dh-img" rel="edit-t64" no="63">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list dh-img" rel="edit-t65" no="64">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list dh-img" rel="edit-t66" no="65">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list dh-img" rel="edit-t67" no="66">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list dh-img" rel="edit-t68" no="67">
			<div class="img" ></div>
		</div>
		<div class="clear"></div>
	</div>
	<!--广告坑4结束-->
	<!--底部广告图开始-->
	<div class="shop_skin_index_list banner-b" rel="edit-t57" no="56">
		<div class="img" ></div>
	</div>
	<!--底部广告图结束-->
</div>

</div>



<script type="text/javascript">
	$(function(){
		function func(){
			var ww=320-140;
			$('.search-layer').width(ww);
			$('.con1 img').height($('.con2 img').height()+$('.con3 img').height()+2+"px");
			$('.de-l img').height($('.de-r img').height()+$('.de-r img').height()+1+"px");
		}
		window.onload=func;
	});
</script>
<?php }else if($template_id==53){?>
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge53/css/style.css">
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
$(function(){
	$('.type').on('click',function(){
		$(this).addClass('foc').siblings('.type').removeClass('foc');
	});
	SetWidth();      //自动切换搜索栏宽度
	b_SetHeight();   //自动切换幻灯片高度
	ad_SetHeight();  //自动切换广告图高度


});

<!-- 自动切换长宽 start -->
function SetWidth(){
	var head_width = $(".header").width();
	var l_width = $(".head_left").width();
	var r_width = $(".head_right").width();
	var search_width = head_width - l_width - r_width - 61;
	$(".search").width(search_width);
}

function s_SetWidth(){
	var s_head_width = $(".s_header").width();
	var s_r_width = $(".searchForm").width();
	var s_search_width = s_head_width - s_r_width - 46;
	$("#s_search_shop").width(s_search_width);
}

function b_SetHeight(){
	var b_width = $(".homebanner").width();
	var b_height = b_width/2;
	$(".ban img").height(b_height);
}

function ad_SetHeight(){
	var ad_width = $(".ad img").width();
	var ad_height = ad_width/2;
	$(".ad img").height(ad_height);
	$(".ad").height(ad_height);
}
<!-- 自动切换长宽 end -->

<!-- 轮播图 start -->
/* function loadother(){
	setTimeout(function(){
	init_swipe();
	},1000);
}
<!-- 右下方小圆点切换 start -->
function init_swipe(){
	window.mySwipe = new Swipe(document.getElementById('slider'), {
		startSlide: 0,
		speed: 400,
		auto: 3000,
		continuous: true,
		disableScroll: false,
		stopPropagation: false,
		callback: function(index, elem) {
			document.querySelector('#nav > div.active').className = 'off';
			document.querySelector('#nav > div:nth-child(' + (index+1) + ')').className = 'active';
		},
		transitionEnd: function(index, elem) {}
	});
} */
<!-- 右下方小圆点切换 end -->
<!-- 轮播图 end -->

</script>
<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>;width:320px;"<?php } ?> >
	<div class="main">
		<div class="zhezhao" id="zhezhao"></div>
		<div class="header">
		<div class="head_left">
			<input type="button" name="location" id="location" value="北京" />
			<img class="arr" src="../../../Common/css/Base/home_decoration/fengge53/images/arr.png"/>
		</div>
		<div class="search">
			<input type="text" id="search_shop" placeholder="输入关键词" />
			<img class="se" src="../../../Common/css/Base/home_decoration/fengge53/images/se.png"/>
		</div>
		<div class="head_right"><img class="loc" src="../../../Common/css/Base/home_decoration/fengge53/images/loc.png"/></div>
		</div>

		<!-- 点击搜索框时激活 start -->
		<div class="s_header" style="display:none;">
		<div class="s_search">
			<input type="text" name="s_search_shop" id="s_search_shop" placeholder="输入关键词" />
			<img class="s_se" src="../../../Common/css/Base/home_decoration/fengge53/images/se.png"/>
		</div>
		<input type="button" class="searchForm" onclick="searchForm();" value="搜索">
		</div>
		<!-- 点击搜索框时激活 end -->

		<!-- 轮播图 start -->
		<div class="homebanner" id="homebanner">
			<div class="shop_skin_index_list banner" rel="edit-t01" no="0">
				<div class="img" ></div>
			</div>
		</div>
		<!-- 轮播图 end -->

		<div class="type_box">
		<div class="box">
			<div class="shop_skin_index_list" rel="edit-t02" no="1">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t10" no="9">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="box">
			<div class="shop_skin_index_list" rel="edit-t03" no="2">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t11" no="10">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="box">
			<div class="shop_skin_index_list" rel="edit-t04" no="3">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t12" no="11">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="box">
			<div class="shop_skin_index_list" rel="edit-t05" no="4">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t13" no="12">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="box">
			<div class="shop_skin_index_list" rel="edit-t06" no="5">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t14" no="13">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="box">
			<div class="shop_skin_index_list" rel="edit-t07" no="6">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t15" no="14">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="box">
			<div class="shop_skin_index_list" rel="edit-t08" no="7">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t16" no="15">
				<div class="div_typename" ></div>
			</div>
		</div>
		<div class="box">
			<div class="shop_skin_index_list" rel="edit-t09" no="8">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t17" no="16">
				<div class="div_typename" ></div>
			</div>
		</div>
		</div>

		<!-- 页面中部广告位 start -->
		<div class="ad">
			<div class="shop_skin_index_list ad1" rel="edit-t18" no="17">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list ad1" rel="edit-t19" no="18">
				<div class="img" ></div>
			</div>
		</div>
		<!-- 页面中部广告位 end -->

		<div class="main_type">
		<div class="type foc">
			<span>附近商家</span>
		</div>
		<!--<div class="type">
			<span>线上商家</span>
		</div>
		<div class="type">
			<span>附近白给</span>
		</div>
		<div class="type">
			<span>附近活动</span>
		</div>-->
		</div>

		<div class="main">
		<div class="main_box">
			<img src="../../../Common/css/Base/home_decoration/fengge53/images/shop.png"/>
			<div class="s_name"><span>必胜客欢乐餐厅</span></div>
			<div class="s_intro"><span>【五人超值套餐】，免费WIFI</span></div>
			<div class="s_price"><span>￥168</span></div>
			<div class="s_loc"><img src="../../../Common/css/Base/home_decoration/fengge53/images/loc1.png"/><span>500m</span></div>
			<div class="s_sold"><span>已售100</span></div>
		</div>
		<div class="main_box">
			<img src="../../../Common/css/Base/home_decoration/fengge53/images/shop.png"/>
			<div class="s_name"><span>必胜客欢乐餐厅</span></div>
			<div class="s_intro"><span>【五人超值套餐】，免费WIFI</span></div>
			<div class="s_price"><span>￥168</span></div>
			<div class="s_loc"><img src="../../../Common/css/Base/home_decoration/fengge53/images/loc1.png"/><span>12.68km</span></div>
			<div class="s_sold"><span>已售100</span></div>
		</div>
		<div class="main_box">
			<img src="../../../Common/css/Base/home_decoration/fengge53/images/shop.png"/>
			<div class="s_name"><span>必胜客欢乐餐厅</span></div>
			<div class="s_intro"><span>【五人超值套餐】，免费WIFI</span></div>
			<div class="s_price"><span>￥168</span></div>
			<div class="s_loc"><img src="../../../Common/css/Base/home_decoration/fengge53/images/loc1.png"/><span>500m</span></div>
			<div class="s_sold"><span>已售100</span></div>
		</div>
		</div>
	</div>
</div>
<?php }else if($template_id==54){?> <!--浪莎专用模板-->
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge54/css/style.css">
<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>;width:320px;"<?php } ?> >
	<div class="main">
		<div class="shop_skin_index_list  scroll" rel="edit-t01" no="0" style="height:133px">
			<div class="img" ></div>
		</div>
		<div class="cl-box">
			<div class="cl">
				<div class="shop_skin_index_list" rel="edit-t02" no="1">
					<div class="img" ></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t07" no="6">
					<div class="div_typename" ></div>
				</div>
			</div>
			<div class="cl">
				<div class="shop_skin_index_list" rel="edit-t03" no="2">
					<div class="img" ></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t08" no="7">
					<div class="div_typename" ></div>
				</div>
			</div>
			<div class="cl">
				<div class="shop_skin_index_list" rel="edit-t04" no="3">
					<div class="img" ></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t09" no="8">
					<div class="div_typename" ></div>
				</div>
			</div>
			<div class="cl">
				<div class="shop_skin_index_list" rel="edit-t05" no="4">
					<div class="img" ></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t10" no="9">
					<div class="div_typename" ></div>
				</div>
			</div>
			<div class="cl">
				<div class="shop_skin_index_list" rel="edit-t06" no="5">
					<div class="img" ></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t11" no="10">
					<div class="div_typename" ></div>
				</div>
			</div>

			<div class="clear"></div>
		</div>
		<div class="search-layer">
			<img src="../../../Common/css/Base/home_decoration/fengge54/images/search.jpg" class="search-icon">
			<input type="text" class="search-input" placeholder="搜索你想要的商品" >
			<!--<button class="search-button"></button>-->
		</div>
		<div class="zt-box">
			<div class="shop_skin_index_list img1" rel="edit-t12" no="11">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list left-img" rel="edit-t13" no="12">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list right-top-img" rel="edit-t14" no="13">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list right-bottom-img" rel="edit-t15" no="14">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list img1" rel="edit-t16" no="15">
				<div class="img" ></div>
			</div>

			<!--6个自定义产品位开始-->
			<div class="pro-box">
				<div class="shop_skin_index_list pro" rel="edit-t17" no="16">
					<div class="img" ></div>
				</div>
				<div class="shop_skin_index_list pro" rel="edit-t18" no="17">
					<div class="img" ></div>
				</div>
				<div class="shop_skin_index_list pro" rel="edit-t19" no="18">
					<div class="img" ></div>
				</div>
				<div class="shop_skin_index_list pro" rel="edit-t20" no="19">
					<div class="img" ></div>
				</div>
				<div class="shop_skin_index_list pro" rel="edit-t21" no="20">
					<div class="img" ></div>
				</div>
				<div class="shop_skin_index_list pro" rel="edit-t22" no="21">
					<div class="img" ></div>
				</div>
				<div class="clear"></div>
			</div>
			<!--6个自定义产品位结束-->
		</div>
		<!--8个广告位开始-->
		<div class="shop_skin_index_list banner" rel="edit-t23" no="22">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t24" no="23">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t25" no="24">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t26" no="25">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t27" no="26">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t28" no="27">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t29" no="28">
			<div class="img" ></div>
		</div>
		<div class="shop_skin_index_list banner" rel="edit-t30" no="29">
			<div class="img" ></div>
		</div>
		<!--8个广告位结束-->

		<!--三个广告位开始-->
		<div class="p-box">
			<div class="shop_skin_index_list p1" rel="edit-t31" no="30">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list p2" rel="edit-t32" no="31">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list p2" rel="edit-t33" no="32">
				<div class="img" ></div>
			</div>
		</div>
		<!--三个广告位结束-->

		<div class="br-box">
			<img src="../../../Common/css/Base/home_decoration/fengge54/images/br1.jpg">
			<img src="../../../Common/css/Base/home_decoration/fengge54/images/br2.jpg">
			<img src="../../../Common/css/Base/home_decoration/fengge54/images/br1.jpg">
			<span>
				<div class="shop_skin_index_list" rel="edit-t76" no="75" >
					<div class="div_typename" ></div>
				</div>
			</span>

		</div>
		<div class="c-box">
			<div class="shop_skin_index_list c-box-img" rel="edit-t44" no="43"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t45" no="44"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t46" no="45"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t47" no="46"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t48" no="47"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t49" no="48"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t50" no="49"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t51" no="50"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t52" no="51"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t53" no="52"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t54" no="53"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t55" no="54"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t56" no="55"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t57" no="56"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t58" no="57"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t59" no="58"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t60" no="59"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t61" no="60"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t62" no="61"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t63" no="62"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t64" no="63"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t65" no="64"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t66" no="65"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t67" no="66"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t68" no="67"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t69" no="68"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t70" no="69"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t71" no="70"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t72" no="71"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t73" no="72"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t74" no="73"><div class="img" ></div></div>
			<div class="shop_skin_index_list c-box-img" rel="edit-t75" no="74"><div class="img" ></div></div>
		</div>
		<!--<div class="c-box">
			<img src="../../../Common/css/Base/home_decoration/fengge54/images/c1.jpg">
			<img src="../../../Common/css/Base/home_decoration/fengge54/images/c2.jpg">
			<img src="../../../Common/css/Base/home_decoration/fengge54/images/c1.jpg">
			<img src="../../../Common/css/Base/home_decoration/fengge54/images/c2.jpg">
		</div>
		-->
		<div class="footer">
			<div class="footer-box">
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t34" no="33">
						<div class="img" ></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t39" no="38">
						<div class="div_typename" ></div>
					</div>
				</div>
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t35" no="34">
						<div class="img" ></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t40" no="39">
						<div class="div_typename" ></div>
					</div>
				</div>
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t36" no="35">
						<div class="img" ></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t41" no="40">
						<div class="div_typename" ></div>
					</div>
				</div>
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t37" no="36">
						<div class="img" ></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t42" no="41">
						<div class="div_typename" ></div>
					</div>
				</div>
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t38" no="37">
						<div class="img" ></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t43" no="42">
						<div class="div_typename" ></div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

$(function(){
		function func(){
			var two_height=$('.right-top-img').find('img').height()+$('.right-bottom-img').find('img').height();
		$('.left-img').find('img').height(two_height);
		var two_h=$('.p2').find('img').height()
		$('.p1').find('img').height(two_h);
		}
		window.onload=func;
	});


</script>

<?php }else if($template_id==55){?>
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/fengge55/css/style.css">
<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>"<?php } ?>>
	<div class="main">
		<div class="search-box">
			<div class="shop_skin_index_list"  rel="edit-t01" no="0">
				<div class="img"></div>

			</div>
			<div class="search-layer">
				<input type="text" class="search-input" placeholder="正品保障，私密发货" style="border-radius:3px;">
				<button class="search-button"></button>
			</div>
		</div>

		<div class="shop_skin_index_list banner"  rel="edit-t02" no="1" style="height: 160px;">
			<div class="img"></div>
			<div class="mod" style="display: none;">&nbsp;</div>
		</div>
		<style>
			.fenlei_ad img {width:106px;height:145px;}
			.fenlei_ad .fenlei_img{float:left;width:33%;overflow:hidden;}
			.fenlei_bottom_ad .fenlei_ad_img{float:left;width:50%;}
			.fenlei_bottom_ad{margin-bottom:5px;}
			.fenlei_bottom_ad img{width:165px;height:95px;}
		</style>
		<div style="width:100%;margin:0 auto;margin-top:10px;overflow:hidden;display:block;" class="fenlei_ad">
			<div class="fenlei_img">
				<div class="shop_skin_index_list"  rel="edit-t31" no="30">
					<div class="img"></div>
				</div>
			</div>
			<div class="fenlei_img">
				<div class="shop_skin_index_list"  rel="edit-t32" no="31">
					<div class="img"></div>
				</div>
			</div>
			<div class="fenlei_img">
				<div class="shop_skin_index_list"  rel="edit-t33" no="32">
					<div class="img"></div>
				</div>
			</div>
		</div>
		<div class="fenlei_bottom_ad">
			<div class="shop_skin_index_list fenlei_ad_img"  rel="edit-t34" no="33">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list fenlei_ad_img"  rel="edit-t35" no="34">
				<div class="img"></div>
			</div>
		</div>
		<!--<div class="content-box">
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t03" no="2">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t11" no="10">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t04" no="3">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t12" no="11">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t05" no="4">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t13" no="12">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t06" no="5">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t14" no="13">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t07" no="6">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t15" no="14">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t08" no="7">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t16" no="15">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t09" no="8">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t17" no="16">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="icon-box">
				<div class="shop_skin_index_list" rel="edit-t10" no="9">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t18" no="17">
				   <p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		-->
		<div class="img-box">
			<div class="shop_skin_index_list left-img" rel="edit-t19" no="18">
				<div class="img" ></div>
			</div>
			<div class="shop_skin_index_list right-top-img" rel="edit-t20" no="19">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list right-bottom-img" rel="edit-t21" no="20">
				<div class="img"></div>
			</div>


			<div class="clear"></div>
		</div>



		<div class="product-box">
			<img src="../../../Common/css/Base/home_decoration/fengge55/images/title.png" class="title">
			<span class="more">更多>></span>
			<!--<img src="fengge47/images/banner.jpg" class="banner">-->
			<div style="display:block;background-color:#999;color:#fff;height:108px;width:320px;float:left;border-bottom:1px solid #fff;">
				<span style="text-align:center;display:block;width:100%;margin-top:38px;">一级分类上传图片尺寸：1080*340</span>
			</div>
			<div class="left-product" style="display:block;background-color:#999;color:#fff;height:160px;width:160px;float:left;">
				<span style="text-align:center;display:block;width:100%;margin-top:60px;">二级分类上传图片尺寸：450*450</span>
			</div>
			<div class="right-product" style="display:block;background-color:#999;color:#fff;height:160px;width:160px;float:left;">
				<span style="text-align:center;display:block;width:100%;margin-top:60px;">二级分类上传图片尺寸：450*450</span>
			</div>
			<div class="three-product border-right" style="display:block;background-color:#999;color:#fff;height:106px;width:106px;float:left;">
				<span style="text-align:center;display:block;width:100%;margin-top:16px;">二级分类<br>上传图片尺寸：450*450</span>
			</div>
			<div class="three-product border-right" style="display:block;background-color:#999;color:#fff;height:106px;width:106px;float:left;">
				<span style="text-align:center;display:block;width:100%;margin-top:16px;">二级分类<br>上传图片尺寸：450*450</span>
			</div>
			<div class="three-product" style="display:block;background-color:#999;color:#fff;height:106px;width:106px;float:left;">
				<span style="text-align:center;display:block;width:100%;margin-top:16px;">二级分类<br>上传图片尺寸：450*450</span>
			</div>
			<!--
			<img src="fengge47/images/product-1.png" class="left-product">
			<img src="fengge47/images/product-1.png" class="right-product">
			<img src="fengge47/images/product-2.png" class="three-product border-right">
			<img src="fengge47/images/product-2.png" class="three-product border-right">
			<img src="fengge47/images/product-2.png" class="three-product">
			-->
		</div>
		<div style="display:block;overflow:hidden;width:320px;margin-bottom:10px;">
			<div class="goods_box">
				<img src="../../../Common/css/Base/home_decoration/fengge55/images/ad-1.jpg" class="w50-img">
				<p class="good_name">樱花浪漫唇膏樱花浪漫唇膏樱花浪漫唇膏</p>
				<span class="good_price">￥69</span><span class="good_sale">已售：4笔</span>
			</div>
			<div class="goods_box">
				<img src="../../../Common/css/Base/home_decoration/fengge55/images/ad-1.jpg" class="w50-img">
				<p class="good_name">樱花浪漫唇膏樱花浪漫唇膏樱花浪漫唇膏</p>
				<span class="good_price">￥69</span><span class="good_sale">已售：4笔</span>
			</div>
			<div class="goods_box">
				<img src="../../../Common/css/Base/home_decoration/fengge55/images/ad-1.jpg" class="w50-img">
				<p class="good_name">樱花浪漫唇膏樱花浪漫唇膏樱花浪漫唇膏</p>
				<span class="good_price">￥69</span><span class="good_sale">已售：4笔</span>
			</div>
			<div class="goods_box">
				<img src="../../../Common/css/Base/home_decoration/fengge55/images/ad-1.jpg" class="w50-img">
				<p class="good_name">樱花浪漫唇膏樱花浪漫唇膏樱花浪漫唇膏</p>
				<span class="good_price">￥69</span><span class="good_sale">已售：4笔</span>
			</div>
		</div>
		<div class="shop_skin_index_list" rel="edit-t30" no="29">
			<div class="img w100-img"></div>
		</div>
		<div class="footer">
			<div class="footer-box">
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t22" no="21">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t26" no="25">
					   <p><div class="div_typename" ></div></p>
					</div>
				</div>
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t23" no="22">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t27" no="26">
					   <p><div class="div_typename" ></div></p>
					</div>
				</div>
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t24" no="23">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t28" no="27">
					   <p><div class="div_typename" ></div></p>
					</div>
				</div>
				<div class="weidian">
					<div class="shop_skin_index_list" rel="edit-t25" no="24">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t29" no="28">
					   <p><div class="div_typename" ></div></p>
					</div>
				</div>
			</div>
		</div>
	</div>

</div><!--shop_skin_index end-->

<?php }else if($template_id==56){?>
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge56/css/style.css">
<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>;width:320px;"<?php } ?> >
<div class="main">
	<div class="shop_skin_index_list w100" rel="edit-t01" no="0">
		<div class="img"></div>
	</div>
	<div class="t-box">
		<div class="shop_skin_index_list t-box-img" rel="edit-t02" no="1">
			<div class="img"></div>
		</div>
		<div class="shop_skin_index_list t-box-img" rel="edit-t03" no="2">
			<div class="img"></div>
		</div>
	</div>
	<div class="search-layer">
		<input type="text" name="Keyword" class="search-input" placeholder="搜索你想要的商品" style="border:none!important;color:black;">
		<button type="submit" class="search-button"></button>
	</div>
	<div class="shop_skin_index_list w100" rel="edit-t04" no="3">
		<div class="img"></div>
	</div>
	<div class="shop_skin_index_list w100" rel="edit-t05" no="4">
		<div class="img"></div>
	</div>
	<div class="f-box">
		<div class="shop_skin_index_list f-box-img" rel="edit-t06" no="5">
			<div class="img"></div>
		</div>
		<div class="shop_skin_index_list f-box-img" rel="edit-t07" no="6">
			<div class="img"></div>
		</div>
		<div class="shop_skin_index_list f-box-img" rel="edit-t08" no="7">
			<div class="img"></div>
		</div>
		<div class="shop_skin_index_list f-box-img" rel="edit-t09" no="8">
			<div class="img"></div>
		</div>
	</div>
	<div class="shop_skin_index_list w100" rel="edit-t10" no="9">
		<div class="img"></div>
	</div>
	<div class="k-box">
		<div class="shop_skin_index_list k-box-img" rel="edit-t11" no="10">
			<div class="img"></div>
		</div>
		<div class="shop_skin_index_list k-box-img" rel="edit-t12" no="11">
			<div class="img"></div>
		</div>

	</div>
	<div class="shop_skin_index_list scroll banner" rel="edit-t13" no="12" style="height:160px;">
		<div class="img"></div>
	</div>

	<section class="custom-line-wrap"><hr class="custom-line"></section>
	<div class="floor">
		<div style="background:#999;width:320px;height:111px;display:block;line-height:111px;text-align:center;margin-bottom:10px;">
			<span style="color:#fff;">
				分类楼层首页广告图：640px*222px
			</span>
		</div>
		<!--<img src="../../../Common/css/Base/home_decoration/fengge56/images/floor.jpg" class="img-floor">-->
		<div class="pro-box">
			<img src="../../../Common/css/Base/home_decoration/fengge56/images/product1.jpg">
			<p class="goods-title ">澳洲White Glo 惠宝 钻石系列牙齿美白套装 牙齿美白精华和美白牙膏</p>
			<span class="goods-pirce"><span class="yen">¥</span>149.00</span>
			<a href="#" class="addcart"><i></i></a>
		</div>
		<div class="pro-box">
			<img src="../../../Common/css/Base/home_decoration/fengge56/images/product1.jpg">
			<p class="goods-title ">澳洲White Glo 惠宝 钻石系列牙齿美白套装 牙齿美白精华和美白牙膏</p>
			<span class="goods-pirce"><span class="yen">¥</span>149.00</span>
			<a href="#" class="addcart"><i></i></a>
		</div>
		<div class="pro-box">
			<img src="../../../Common/css/Base/home_decoration/fengge56/images/product1.jpg">
			<p class="goods-title ">澳洲White Glo 惠宝 钻石系列牙齿美白套装 牙齿美白精华和美白牙膏</p>
			<span class="goods-pirce"><span class="yen">¥</span>149.00</span>
			<a href="#" class="addcart"><i></i></a>
		</div>
		<div class="pro-box">
			<img src="../../../Common/css/Base/home_decoration/fengge56/images/product1.jpg">
			<p class="goods-title ">澳洲White Glo 惠宝 钻石系列牙齿美白套装 牙齿美白精华和美白牙膏</p>
			<span class="goods-pirce"><span class="yen">¥</span>149.00</span>
			<a href="#" class="addcart"><i></i></a>
		</div>
		<div class="clear"></div>
	</div>

	<div class="footer">
		<div class="footer-box">
			<div class="weidian">
				<a href="#">
					<div class="shop_skin_index_list" rel="edit-t14" no="13">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t18" no="17">
					   <p><div class="div_typename" ></div></p>
					</div>
				</a>
			</div>
			<div class="weidian">
				<a href="#">
					<div class="shop_skin_index_list" rel="edit-t15" no="14">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t19" no="18">
					   <p><div class="div_typename" > </div></p>
					</div>
				</a>
			</div>
			<div class="weidian">
				<a href="#">
					<div class="shop_skin_index_list" rel="edit-t16" no="15">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t20" no="19">
					   <p><div class="div_typename" ></div></p>
					</div>
				</a>
			</div>
			<div class="weidian">
				<a href="#">
					<div class="shop_skin_index_list" rel="edit-t17" no="16">
						<div class="img"></div>
					</div>
					<div class="shop_skin_index_list" rel="edit-t21" no="20">
					   <p><div class="div_typename" ></div></p>
					</div>
				</a>
			</div>
		</div>
	</div>
</div>
</div>

<?php }else if($template_id==57){?>
<link href="../../../../back_commonshop/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../../Common/css/Base/home_decoration/fengge57/css/style.css">
<div id="shop_skin_index" <?php if(!empty($index_bg)){ ?>style="background:#<?php echo $index_bg; ?>;width:320px;"<?php } ?> >
<div class="main">
	<div class="search-box">
		<div class="shop_skin_index_list" rel="edit-t01" no="0">
			<div class="img"></div>
		</div>
		<div class="search-layer">
			<input type="text" class="search-input" placeholder="寻找身边最安全的美食" style="border-radius:10px;">
			<button class="search-button"></button>
		</div>
	</div>
	<!--<img src="images/scroll.jpg" class="scroll-img">-->
	<div class="shop_skin_index_list scroll-img" rel="edit-t02" no="1" style="height:160px;">
		<div class="img"></div>
	</div>

	<div class="content-box">

		<div class="icon-box">
			<div class="shop_skin_index_list" rel="edit-t03" no="2">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t11" no="10">
			   <p><div class="div_typename" ></div></p>
			</div>
		</div>
		<div class="icon-box">
			<div class="shop_skin_index_list" rel="edit-t04" no="3">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t12" no="11">
			   <p><div class="div_typename" ></div></p>
			</div>
		</div>
		<div class="icon-box">
			<div class="shop_skin_index_list" rel="edit-t05" no="4">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t13" no="12">
			   <p><div class="div_typename" ></div></p>
			</div>
		</div>
		<div class="icon-box">
			<div class="shop_skin_index_list" rel="edit-t06" no="5">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t14" no="13">
			   <p><div class="div_typename" ></div></p>
			</div>
		</div>
		<div class="icon-box">
			<div class="shop_skin_index_list" rel="edit-t07" no="6">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t15" no="14">
			   <p><div class="div_typename" ></div></p>
			</div>
		</div>
		<div class="icon-box">
			<div class="shop_skin_index_list" rel="edit-t08" no="7">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t16" no="15">
			   <p><div class="div_typename" ></div></p>
			</div>
		</div>
		<div class="icon-box">
			<div class="shop_skin_index_list" rel="edit-t09" no="8">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t17" no="16">
			   <p><div class="div_typename" ></div></p>
			</div>
		</div>
		<div class="icon-box">
			<div class="shop_skin_index_list" rel="edit-t10" no="9">
				<div class="img"></div>
			</div>
			<div class="shop_skin_index_list" rel="edit-t18" no="17">
			   <p><div class="div_typename" ></div></p>
			</div>
		</div>

		<!--<div class="icon-box"><a href="#"><img src="images/icon1.jpg"><p>港式点心</p></a></div>
		<div class="icon-box"><a href="#"><img src="images/icon2.jpg"><p>西点</p></a></div>
		<div class="icon-box"><a href="#"><img src="images/icon3.jpg"><p>冰激凌</p></a></div>
		<div class="icon-box"><a href="#"><img src="images/icon4.jpg"><p>咖啡饮品</p></a></div>
		<div class="icon-box"><a href="#"><img src="images/icon5.jpg"><p>日韩便当</p></a></div>
		<div class="icon-box"><a href="#"><img src="images/icon6.jpg"><p>孩童最爱</p></a></div>
		<div class="icon-box"><a href="#"><img src="images/icon7.jpg"><p>社区超市</p></a></div>
		<div class="icon-box"><a href="#"><img src="images/icon8.jpg"><p>全部</p></a></div>-->
		<div class="clear"></div>
	</div>
	<div class="info">
		<div class="shop_skin_index_list" rel="edit-t19" no="18" style="float:left;">
			<div class="img"></div>
		</div>
		<ul>
			<li>
				<div class="shop_skin_index_list" rel="edit-t30" no="29">
				   <div class="div_typename" ></div>
				</div>
			</li>
			<li>
				<div class="shop_skin_index_list" rel="edit-t31" no="30">
					<div class="div_typename" ></div>
				</div>
			</li>
		</ul>
	</div>
	<div class="w94-box">
		<div class="shop_skin_index_list left-img" rel="edit-t32" no="31">
			<div class="img"></div>
		</div>
		<div class="shop_skin_index_list right-top-img" rel="edit-t33" no="32">
			<div class="img"></div>
		</div>
		<div class="shop_skin_index_list right-bottom-img" rel="edit-t34" no="33">
			<div class="img"></div>
		</div>

		<!--
		<img src="images/img1.jpg" class="left-img">
		<img src="images/img2.jpg" class="right-top-img">
		<img src="images/img3.jpg" class="right-bottom-img">
		-->
		<div class="clear"></div>
	</div>
	<div class="classify-box">
		<div class="c-title"><span class="icon-title"></span>港式点心<span class="more">更多>></span></div>
		<div class="product">
			<img src="../../../Common/css/Base/home_decoration/fengge57/images/product.jpg">
			<span class="price">单价：28.99元</span>
			<span class="addcart"></span>
		</div>
		<div class="product">
			<img src="../../../Common/css/Base/home_decoration/fengge57/images/product.jpg">
			<span class="price">单价：28.99元</span>
			<span class="addcart"></span>
		</div>
		<div class="clear"></div>
	</div>
	<div class="classify-box">
		<div class="c-title"><span class="icon-title"></span>港式点心<span class="more">更多>></span></div>
		<div class="product">
			<img src="../../../Common/css/Base/home_decoration/fengge57/images/product.jpg">
			<span class="price">单价：28.99元</span>
			<span class="addcart"></span>
		</div>
		<div class="product">
			<img src="../../../Common/css/Base/home_decoration/fengge57/images/product.jpg">
			<span class="price">单价：28.99元</span>
			<span class="addcart"></span>
		</div>
		<div class="clear"></div>
	</div>
	<div class="footer">
		<div class="footer-box">
			<div class="weidian">
				<div class="shop_skin_index_list" rel="edit-t20" no="19">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t25" no="24">
					<p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="weidian">
				<div class="shop_skin_index_list" rel="edit-t21" no="20">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t26" no="25">
					<p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="weidian">
				<div class="shop_skin_index_list" rel="edit-t22" no="21">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t27" no="26">
					<p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="weidian">
				<div class="shop_skin_index_list" rel="edit-t23" no="22">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t28" no="27">
					<p><div class="div_typename" ></div></p>
				</div>
			</div>
			<div class="weidian">
				<div class="shop_skin_index_list" rel="edit-t24" no="23">
					<div class="img"></div>
				</div>
				<div class="shop_skin_index_list" rel="edit-t29" no="28">
					<p><div class="div_typename" ></div></p>
				</div>
			</div>
		</div>
	</div>

	</div>
</div>
<script type="text/javascript">
	/*$(function(){
		$(window).resize(function(){
		var two_height=$('.right-top-img').innerHeight()+$('.right-bottom-img').innerHeight();
		$('.left-img').height(two_height);
		var two_h=$('.p2').innerHeight()
		$('.p1').height(two_h);
	});
		 $(window).resize();
	})
	*/
	$(function(){
		function func(){
			var two_height=$('.right-top-img').find('img').height()+$('.right-bottom-img').find('img').height()+3;
		$('.left-img').find('img').height(two_height);
		var two_h=$('.p2').find('img').height()
		$('.p1').find('img').height(two_h);
		}
		window.onload=func;
	});



</script>

<?php }?>

					<!-- 开发区域结束 -->
					</li>
					<li class="WSY_homeleft_bottom">
						<p></p>
					</li>
				</div>
				<div class="WSY_homeright">
				<ul class="WSY_homeright_nav">
					<li id="tab_1" onclick=setCookie(1);><a class="blueAA" href="#"  >首页装修</a></li>

					<li id="tab_2" onclick=setCookie(2);><a href="#"  style="width:110px;">分类首页显示图</a></li>


					<li id="tab_3" onclick=setCookie(3);><a href="#"  style="width:110px;">自定义底部菜单</a></li>

				</ul>
				<div class="homerightbox" id="tab1">
				<form id="frm_uploadimg" action="<?php //$is_shopgeneral=TRUE; if($is_shopgeneral){echo 'save_templateimg_4M';}else{echo 'save_templateimg';}?>save_templateimg.php?customer_id=<?php echo $customer_id_en; ?>&template_id=<?php echo $template_id; ?>" method="post" enctype="multipart/form-data" >
				<div class="homeright_left">
				<!--拾色器开始-->
				<div class="WSY_jscolor">
					<p>背景颜色：</p>
					<input class="color" value="<?php echo $index_bg; ?>" name="index_bg" id="colorPicker" style="float:none">
					<a class="WSY_jscolor_p01" onclick="document.getElementById('colorPicker').value='';">清除颜色</a>
				</div>
			<div class="input_butn"><input type="submit" value="提交保存" name="submit_button" ></div>
			<input type="hidden" name="contenttype" id="contenttype" value="2">
			<input type="hidden" name="position" id="position" value="1">
				<!--拾色器结束-->
				</div>

				<div class="homeright_right">
				<!--修改图片单个功能操作代码开始-->
				<div class="WSY_microbox WSY_single1 " id="setimages">
					<div value="title" style="display: none;">
						<span class="fc_red">*</span> 标题<br>
						<div class="input"><input name="Title" value="" type="text"></div>
						<div class="blank20"></div>
					</div>
					<p><span class="tips">大图建议尺寸：<label>400*400</label>px</span></p>
					<dl class="WSY_micro" style="margin-top:35px!important">

						<dt style="text-align:center;"><img src="../../../Common/images/Base/home_decoration/contenticon/p-img.jpg" id="temp_img"><span><a href="#" id="a_banner_2_1"><img src="../../../Common/images/Base/home_decoration/operating_icon/guanbi.png"></a></span></dt>
						<!--上传文件代码开始-->
						<div class="uploader white" id="WSY_whitebox">
							<input type="text" class="filename" readonly/>
							<input type="button" name="file" class="button" value="上传..."/>
							<input type="file" size="20" name="upfile2" id="upfile2"/>
							<div id="HomeFileUploadQueue" class="om-fileupload-queue"></div>
					   </div>
					   <!--上传文件代码结束-->
					    <div class="url_select" style="display: block;">
						<dd><input type="text" style="display:none;" id="search_link" /><span style="display:none;" class="search-btn">搜索</span></dd>
					   <dd><input  type="radio" name="sel_link_type" id="sel_link_type_2_1" value="1" /> <span>链接页面：</span>

						<select  name="type_id_2"  id="type_id_2" onchange="change_type(this);" style="display:none;">
						<option value="-1" selected="selected">--------请选择--------</option>
						<option value="-16" >首页</option>
						<option value="-6" >全部产品</option>
						<option value="-2" >新品上市</option>
						<option value="-3" >热卖产品</option>
						<option value="-4" >购物车</option>
						<option value="-8" >个人中心</option>
						<option value="-18" >我的订单</option>
						<option value="-7" >产品分类页1</option>
						<option value="-17" >产品分类页2</option>
						<option value="-37" >产品分类页3</option>
						<option value="-47" >产品分类页4</option>
						<option value="-9" >我的微店</option>
						<option value="-5" >限时抢购</option>
						<option value=-33 >区域批发商列表</option>
						<option value="-10" >商城在线客服</option>
						<option value="-11" >礼包列表</option>
						<option value="-12" >VP产品</option>
						<option value="-15" >兑换专区</option>
						<!--option value="-19" >拼团列表</option-->
						<option value="-20" >人气团列表</option>
						<option value="-21" >续费专区</option>
						<option value="-22" >电商直播</option>
						<option value="-23" >语音直播</option>
						<?php if($is_ticket){?>
						<option value="-24" >票务特价机票</option>
						<option value="-25" >票务特价火车票</option>
						<?php }?>
						<?php if($is_exchange){?>
						<option value="-32" >满赠专区</option>
						<?php }?>
						<option value="-95" >砍价活动</option>
						<option value="-96" >众筹新版</option>
						<?php if($is_f2c>0){ ?>
						<option value="-26" >F2C系统</option>
						<?php } ?>
                        <option value="-27" >订货系统登录</option>
                        <option value="-28" >订货系统申请</option>
                        <option value="-29" >订货系统中心</option>
                        <option value="-100" >门店申请</option>
                        <option value="-101" >门店商城模式</option>
                        <option value="-102" >门店店铺模式</option>
                        <option value="-103" >子门店申请</option>
                        <option value="-104" >子门店中心</option>
                        <option value="-19" >拼团商品专区1</option>
                        <option value="-30" >拼团商品专区2</option>
                        <option value="-31" >拼团商品专区3</option>
                        <option value="-34" >积分签到</option>
                        <option value="-35" >积分兑换</option>
                        <option value="-36" >限时专区</option>
                        <option value="-361" >积分专区</option>
						<?php
							if( !empty($diy_template_link) ){
						?>
						<optgroup label="---------------自定义模板---------------"></optgroup>
						<?php

							for( $i = 0; $i < $diy_template_count; $i++ ){
								$diy_template_arr = explode('_',$diy_template_link[$i]);
								$diy_template_id = $diy_template_arr[0];
								$diy_template_name = $diy_template_arr[1];
						?>
						<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
						<?php }}?>
						<optgroup label="---------------产品分类---------------"></optgroup>
						<!--<?php
						  if($typesize>0){
						     for($i=0;$i<$typesize; $i++){
							    $typestr= $typeLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
						<?php  }

						} ?>-->
						<option value="-40">多级分类</option>
						<optgroup label="---------------图文消息---------------"></optgroup>
						<?php
						  if($imginfosize>0){
						     for($i=0;$i<$imginfosize; $i++){
							    $typestr= $imginfoLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
						<?php  }

						} ?>

						<optgroup label="---------------优惠券---------------"></optgroup>
						<option value="1all_60" >优惠券全部</option>
						<?php
						  if($couponsize>0){
						     for($i=0;$i<$couponsize; $i++){
							    $couponstr= $couponLst->Get($i);

							    $couponarr = explode("_",$couponstr);
								$coupon_id = $couponarr[0];
								$coupon_name = $couponarr[1];


						?>
						  <option value="<?php echo $coupon_id; ?>_60" ><?php echo $coupon_name; ?></option>
						<?php  }

						} ?>

						<?php if($is_cityarea_caterer){?>
						<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
						<?php if($cityareaCaterersize>0){
								for($i=0;$i<$cityareaCaterersize; $i++){
								$typestr= $cityareaCatererLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_ktv){?>
						<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
						<?php if($cityareaKTVsize>0){
								for($i=0;$i<$cityareaKTVsize; $i++){
								$typestr= $cityareaKTVLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_hotel){?>
						<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
						<?php if($cityareaHotelsize>0){
								for($i=0;$i<$cityareaHotelsize; $i++){
								$typestr= $cityareaHotelLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_shop){?>
						<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
						<?php if($cityareaShopsize>0){
								for($i=0;$i<$cityareaShopsize; $i++){
								$typestr= $cityareaShopLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea){?>
						<optgroup label="-----------商圈行业列表-----------"></optgroup>
						<?php }
						if($is_cityarea_caterer){?>
						<option value="2_50" >美食</option>
						<?php }
						if($is_cityarea_ktv){?>
						<option value="2_51" >KTV</option>
						<?php }
						if($is_cityarea_hotel){?>
						<option value="2_52" >酒店</option>
						<?php }
						if($is_cityarea_shop){?>
						<option value="2_53" >线下商城-首页</option>
                        <option value="2_54" >线下商城-商家列表</option>
						<?php }
						?>
						<?php if($is_cityarea_finance){?>
                        <option value="2_63" >金融-贷款</option>
                        <option value="2_61" >金融-信用卡</option>
                        <option value="2_62" >金融-保险</option>
                        <?php }?>
                        <?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
						</select>
						<select name="product_type_2" id="product_type_2" style="display:none;margin:5px 0 0 81px;" onchange="changeProductType(this.value);"><!--分类-->
						<!--<option value=-1>--------请选择--------</option>-->
						<?php
							$type_len = count($type_arr[-1]);
							for($i = 0;$i<$type_len;$i++){
								$typearr = explode("_",$type_arr[-1][$i]);
								$type_id = $typearr[0];
								$type_name = $typearr[1];

						?>
						<option value="<?php echo $type_id; ?>_1"><?php echo $type_name; ?></option>
						<?php
							$type_len2 = count($type_arr[$type_id]);
							for($ii = 0;$ii<$type_len2;$ii++){
								$typearr2 = explode("_",$type_arr[$type_id][$ii]);
								$type_id2 = $typearr2[0];
								$type_name2 = $typearr2[1];
						?>
						<option value="<?php echo $type_id2; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
						<?php
							$type_len3 = count($type_arr[$type_id2]);
							for($iii = 0;$iii<$type_len3;$iii++){
								$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
								$type_id3 = $typearr3[0];
								$type_name3 = $typearr3[1];
						?>
						<option value="<?php echo $type_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
						<?php
							$type_len4 = count($type_arr[$type_id3]);
							for($iiii = 0;$iiii<$type_len4;$iiii++){
								$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
								$type_id4 = $typearr4[0];
								$type_name4 = $typearr4[1];
						?>
						<option value="<?php echo $type_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
						<?php
							}
							}
							}
						}
						?>
						</select>
						<div id="div_products_2" style="display:none;padding:5px 0 0 81px">
							<select name="product_detail_id_2" id="product_detail_id_2">
								<option value=-1>---请选择一款产品---</option>
							</select>
						</div>
						<div id="package_product_id" style="display:none;padding:5px 0 0 81px">
							<select name="package_product_id" id="package_product_id">
								<option value=-1>---全部---</option>
								<?php foreach ($package_list as $key => $value) { ?>
								<option value='<?php echo $value['id'] ?>'><?php echo $value['package_name'] ?></option>
								<?php } ?>
							</select>
						</div>

						</dd>
						<dd>
						<input type="text" name="selector_title" id="selector_title" value="" disabled />
                        <button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id" id="selector_id" value="<?php echo $value['linktype'];?>" />
						</dd>
						<dd>
							<input type="radio" name="sel_link_type" id="sel_link_type_2_2" value="2" /> <span>填写链接：</span>
							<input type="text" name="custom_link_2" id="custom_link" onchange="check_url(this)" value="" style="width:175px;" />
						</dd>
						</div>

						<input type=hidden name="imgurl2" id="imgurl2" value="" />

						</dl>
					</div>
				<!--修改图片单个功能操作代码结束-->
			<script>
				function check_value(){

					var title=document.getElementById("title_3").value;
					var title_3=title.replace(/(^\s*)|(\s*$)/g, "");
					var old_title =document.getElementById("old_title").value;

					result = title_3.indexOf("\"");
					//alert(result);
					if(result >=0){
						alert("含有非法字符");
						document.getElementById("title_3").value=old_title;
						return false;

					}else if(title_3.length == 0 ||title_3 == ''){
						alert("标题不能为空");
						document.getElementById("title_3").value=old_title;
						return false;
					}
				}
			</script>
				<!--修改标题单个功能操作代码开始-->
				<div class="WSY_microbox WSY_single2" id="set_title">
					<p>修改标题</p>
					<dl class="WSY_micro">
					   <dd><div class="input">
					   			<input name="title" value="" id="title_3" type="text" onchange="check_value()">

					   		</div>
					   		<input name="old_title" value="" id="old_title" type="text" hidden>
					   	</dd>
					   <dd class="WSY_jscolor">
							<b>字体颜色：</b>
							<input class="color" value="<?php echo $font_color;?>" name="font_bg" id="font_bg">
							<span style="cursor:pointer" onclick="document.getElementById('font_bg').value='';" >清除颜色</span>
					   </dd>

					   <dd>
					   <div class="url_select" style="display: block;">
						<input type="text" style="display:none;" id="search_link" /><span style="display:none;" class="search-btn">搜索</span>
					   <input type="radio" name="sel_link_type" id="sel_link_type_3_1" value="1" /> <span class="show_typebox">链接页面：</span>
						<select  name="type_id_3"  id="type_id_3"  onchange="change_type(this);" style="display:none;" >
						<option value="-1" selected="selected">--------请选择--------</option>
						<option value="-16" >首页</option>
						<option value="-6" >全部产品</option>
						<option value="-2" >新品上市</option>
						<option value="-3" >热卖产品</option>
						<option value="-4" >购物车</option>
						<option value="-8" >个人中心</option>
						<option value="-18" >我的订单</option>
						<option value="-7" >产品分类页1</option>
						<option value="-17" >产品分类页2</option>
						<option value="-37" >产品分类页3</option>
						<option value="-47" >产品分类页4</option>
						<option value="-9" >我的微店</option>
						<option value="-5" >限时抢购</option>
						<option value='-33' >区域批发商列表</option>
						<option value="-10" >商城在线客服</option>
						<option value="-11" >礼包列表</option>
						<option value="-12" >VP产品</option>
						<option value="-15" >兑换专区</option>
						<!--option value="-19" >拼团列表</option-->
						<option value="-20" >人气团列表</option>
						<option value="-21" >续费专区</option>
						<option value="-22" >电商直播</option>
						<option value="-23" >语音直播</option>
						<?php if($is_ticket){?>
						<option value="-24" >票务特价机票</option>
						<option value="-25" >票务特价火车票</option>
						<?php }?>
						<?php if($is_exchange){?>
						<option value="-32" >满赠专区</option>
						<?php }?>
						<option value="-95" >砍价活动</option>
						<option value="-96" >众筹新版</option>
						<?php if($is_f2c>0){ ?>
						<option value="-26" >F2C系统</option>
						<?php } ?>
                        <option value="-27" >订货系统登录</option>
                        <option value="-28" >订货系统申请</option>
                        <option value="-29" >订货系统中心</option>
                        <option value="-19" >拼团商品专区1</option>
                        <option value="-30" >拼团商品专区2</option>
                        <option value="-31" >拼团商品专区3</option>
                        <option value="-34" >积分签到</option>
                        <option value="-35" >积分兑换</option>
						<option value="-36" >限时专区</option>
						<option value="-361" >积分专区</option>
						<?php
							if( !empty($diy_template_link) ){
						?>
						<optgroup label="---------------自定义模板---------------"></optgroup>
						<?php

							for( $i = 0; $i < $diy_template_count; $i++ ){
								$diy_template_arr = explode('_',$diy_template_link[$i]);
								$diy_template_id = $diy_template_arr[0];
								$diy_template_name = $diy_template_arr[1];
						?>
						<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
						<?php }}?>
						<optgroup label="---------------产品分类---------------"></optgroup>
						<!--<?php
						  if($typesize>0){
						     for($i=0;$i<$typesize; $i++){
							    $typestr= $typeLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
						<?php  }

						} ?>-->
						<option value="-40">多级分类</option>
						<optgroup label="---------------图文消息---------------"></optgroup>
						<?php
						  if($imginfosize>0){
						     for($i=0;$i<$imginfosize; $i++){
							    $typestr= $imginfoLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
						<?php  }

						} ?>

						<?php if($is_cityarea_caterer){?>
						<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
						<?php if($cityareaCaterersize>0){
								for($i=0;$i<$cityareaCaterersize; $i++){
								$typestr= $cityareaCatererLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_ktv){?>
						<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
						<?php if($cityareaKTVsize>0){
								for($i=0;$i<$cityareaKTVsize; $i++){
								$typestr= $cityareaKTVLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_hotel){?>
						<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
						<?php if($cityareaHotelsize>0){
								for($i=0;$i<$cityareaHotelsize; $i++){
								$typestr= $cityareaHotelLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_shop){?>
						<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
						<?php if($cityareaShopsize>0){
								for($i=0;$i<$cityareaShopsize; $i++){
								$typestr= $cityareaShopLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea){?>
						<optgroup label="-----------商圈行业列表-----------"></optgroup>
						<?php }
						if($is_cityarea_caterer){?>
						<option value="2_50" >美食</option>
						<?php }
						if($is_cityarea_ktv){?>
						<option value="2_51" >KTV</option>
						<?php }
						if($is_cityarea_hotel){?>
						<option value="2_52" >酒店</option>
						<?php }
						if($is_cityarea_shop){?>
						<option value="2_53" >线下商城-首页</option>
                        <option value="2_54" >线下商城-商家列表</option>
						<?php }
						?>
						<?php if($is_cityarea_finance){?>
                        <option value="2_63" >金融-贷款</option>
                        <option value="2_61" >金融-信用卡</option>
                        <option value="2_62" >金融-保险</option>
                        <?php }?>
						<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
						</select>
						<select name="product_type_3" id="product_type_3" style="display:none;margin:5px 0 0 81px;" onchange="changeProductType_txt_orgi(this.value);"><!--分类-->
						<!--<option value=-1>--------请选择--------</option>-->
						<?php
							$type_len = count($type_arr[-1]);
							for($i = 0;$i<$type_len;$i++){
								$typearr = explode("_",$type_arr[-1][$i]);
								$type_id = $typearr[0];
								$type_name = $typearr[1];

						?>
						<option value="<?php echo $type_id; ?>_1"><?php echo $type_name; ?></option>
						<?php
							$type_len2 = count($type_arr[$type_id]);
							for($ii = 0;$ii<$type_len2;$ii++){
								$typearr2 = explode("_",$type_arr[$type_id][$ii]);
								$type_id2 = $typearr2[0];
								$type_name2 = $typearr2[1];
						?>
						<option value="<?php echo $type_id2; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
						<?php
							$type_len3 = count($type_arr[$type_id2]);
							for($iii = 0;$iii<$type_len3;$iii++){
								$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
								$type_id3 = $typearr3[0];
								$type_name3 = $typearr3[1];
						?>
						<option value="<?php echo $type_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
						<?php
							$type_len4 = count($type_arr[$type_id3]);
							for($iiii = 0;$iiii<$type_len4;$iiii++){
								$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
								$type_id4 = $typearr4[0];
								$type_name4 = $typearr4[1];
						?>
						<option value="<?php echo $type_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
						<?php
							}
							}
							}
						}
						?>
						</select>
						<div id="div_products_3" style="display:none;padding:5px 0 0 81px">
							<select name="product_detail_id_3" id="product_detail_id_3">
								<option value=-1>---请选择一款产品---</option>
							</select>

						</div>
						<div id="package_product_id" style="display:none;padding:5px 0 0 81px">
							<select name="package_product_id" id="package_product_id">
								<option value=-1>---全部---</option>
								<?php foreach ($package_list as $key => $value) { ?>
								<option value='<?php echo $value['id'] ?>'><?php echo $value['package_name'] ?></option>
								<?php } ?>
							</select>
						</div>

						<input type="text" name="selector_title_font" id="selector_title" value="" disabled />
                        <button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id_font" id="selector_id" value="-1" />

						<input type="radio" name="sel_link_type" id="sel_link_type_3_2" value="2" /> <span>填写链接：</span>
						<input type="text" name="custom_link_3" id="custom_link" onchange="check_url(this)" value="" style="width:175px;" />
						</div>

						</dd>

					</dl>
				</div>
				<!--修改标题单个功能操作代码结束-->
				<!--视频链接开始-->

				<div id="set_video_link" style="display: none;">
					<div class="item">
						<div value="title">
							<span class="fc_red">*</span>视频地址<br>


							<div class="input"><input name="video_link" value="" id="title_4" type="text" style="width:300px;" onchange="check_video()"></div>
							<div class="blank20"></div>
							<span class="fc_red">



								用户进入视频网站，点击视频，在分享一栏将"http://"或者"https://"开头的视频地址复制到此处即可。<br/>
								例如 http://player.youku.com/embed/XMTMyMzYzMTUzMg==
							</span>

						</div>
					</div>


					<script>
						function check_video(){

							var video_link=document.getElementById("title_4").value;

							result = video_link.indexOf("http");
							result2 = video_link.indexOf("iframe");
							result3 = video_link.indexOf("\"");
							result4 = video_link.indexOf("https");

							if((result!=0 && result4!=0) || result2>=0 || result3>=0){
								alert("请输入正确的视频地址");
								document.getElementById("title_4").value="";
								return false;
							}

						}
							
					</script>
				</div>
				<!--视频链接over-->

				<!--修改幻灯片功能操作代码开始-->
				<div class="WSY_slide WSY_single3"  id="setbanner">
					<div class="WSY_microbox WSY_microslide">
						<p>图片1：大图建议尺寸：<label  id="label_slide_1">640*320</label>px</p>
						<dl class="WSY_micro">
							<dt><div class="b_r" id="banner_img_1"><img src="../../../Common/images/Base/home_decoration/contenticon/20140611053000_0.jpg"></div><input type=hidden name="imgids_1_1" id="imgids_1_1"  /> <span><a href="#" value="0" id="a_banner_1"><img src="../../../Common/images/Base/home_decoration/operating_icon/guanbi.png"></a></span></dt>
							<!--上传文件代码开始-->
							<div class="uploader white" id="WSY_whitebox">
								<input type="text" class="filename" readonly/>
								<input type="button" name="file" class="button" value="上传..."/>
								<input type="file" size="20"  name="upfile1_1" id="upfile1_1"/>
						   </div>
						   <!--上传文件代码结束-->
						   <dd>
						<input type="text" id="search_link" style="display:none;" /><span style="display:none;" class="search-btn">搜索</span>
						<input type="radio" name="sel_link_type_1" id="sel_link_type_1_1_1" value="1" />
						<span class="show_typebox">链接页面：</span>
						<select name="type_id_1_1" id="type_id_1_1" onchange="change_type(this); " style="display:none;">

                        <option value="-1" selected="selected">--------请选择--------</option>
						<option value="-16" >首页</option>
						<option value="-6" >全部产品</option>
						<option value="-2" >新品上市</option>
						<option value="-3" >热卖产品</option>
						<option value="-4" >购物车</option>
						<option value="-8" >个人中心</option>
						<option value="-18" >我的订单</option>
						<option value="-7" >产品分类页1</option>
						<option value="-17" >产品分类页2</option>
						<option value="-37" >产品分类页3</option>
						<option value="-47" >产品分类页4</option>
						<option value="-9" >我的微店</option>
						<option value="-5" >限时抢购</option>
						<option value='-33' >区域批发商列表</option>
						<option value="-10" >商城在线客服</option>
						<option value="-11" >礼包列表</option>
						<option value="-12" >VP产品</option>
						<option value="-15" >兑换专区</option>
						<!--option value="-19" >拼团列表</option-->
						<option value="-20" >人气团列表</option>
						<option value="-21" >续费专区</option>
						<option value="-22" >电商直播</option>
						<option value="-23" >语音直播</option>
						<?php if($is_ticket){?>
						<option value="-24" >票务特价机票</option>
						<option value="-25" >票务特价火车票</option>
						<?php }?>
						<?php if($is_exchange){?>
						<option value="-32" >满赠专区</option>
						<?php }?>
						<option value="-95" >砍价活动</option>
						<option value="-96" >众筹新版</option>
						<?php if($is_f2c>0){ ?>
						<option value="-26" >F2C系统</option>
						<?php } ?>
                        <option value="-27" >订货系统登录</option>
                        <option value="-28" >订货系统申请</option>
                        <option value="-29" >订货系统中心</option>
                        <option value="-100" >门店申请</option>
                        <option value="-101" >门店商城模式</option>
                        <option value="-102" >门店店铺模式</option>
                        <option value="-103" >子门店申请</option>
                        <option value="-104" >子门店中心</option>
                        <option value="-19" >拼团商品专区1</option>
                        <option value="-30" >拼团商品专区2</option>
                        <option value="-31" >拼团商品专区3</option>
                        <option value="-34" >积分签到</option>
                        <option value="-35" >积分兑换</option>
						<option value="-36" >限时专区</option>
						<option value="-361" >积分专区</option>
						<?php
							if( !empty($diy_template_link) ){
						?>
						<optgroup label="---------------自定义模板---------------"></optgroup>
						<?php

							for( $i = 0; $i < $diy_template_count; $i++ ){
								$diy_template_arr = explode('_',$diy_template_link[$i]);
								$diy_template_id = $diy_template_arr[0];
								$diy_template_name = $diy_template_arr[1];
						?>
						<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
						<?php }}?>
						<optgroup label="---------------产品分类---------------"></optgroup>
						<!--<?php
						  if($typesize>0){
						     for($i=0;$i<$typesize; $i++){
							    $typestr= $typeLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
						<?php  }
						}
						?>-->
						<option value="-40">多级分类</option>
						<optgroup label="---------------图文消息---------------"></optgroup>
						<?php
						  if($imginfosize>0){
						     for($i=0;$i<$imginfosize; $i++){
							    $typestr= $imginfoLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
						<?php  }

						} ?>

						<optgroup label="---------------优惠券---------------"></optgroup>
						<option value="1all_60" >优惠券全部</option>
						<?php
						  if($couponsize>0){
						     for($i=0;$i<$couponsize; $i++){
							    $couponstr= $couponLst->Get($i);

							    $couponarr = explode("_",$couponstr);
								$coupon_id = $couponarr[0];
								$coupon_name = $couponarr[1];


						?>
						  <option value="<?php echo $coupon_id; ?>_60" ><?php echo $coupon_name; ?></option>
						<?php  }

						} ?>

						<?php if($is_cityarea_caterer){?>
						<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
						<?php if($cityareaCaterersize>0){
								for($i=0;$i<$cityareaCaterersize; $i++){
								$typestr= $cityareaCatererLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_ktv){?>
						<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
						<?php if($cityareaKTVsize>0){
								for($i=0;$i<$cityareaKTVsize; $i++){
								$typestr= $cityareaKTVLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_hotel){?>
						<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
						<?php if($cityareaHotelsize>0){
								for($i=0;$i<$cityareaHotelsize; $i++){
								$typestr= $cityareaHotelLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_shop){?>
						<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
						<?php if($cityareaShopsize>0){
								for($i=0;$i<$cityareaShopsize; $i++){
								$typestr= $cityareaShopLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea){?>
						<optgroup label="-----------商圈行业列表-----------"></optgroup>
						<?php }
						if($is_cityarea_caterer){?>
						<option value="2_50" >美食</option>
						<?php }
						if($is_cityarea_ktv){?>
						<option value="2_51" >KTV</option>
						<?php }
						if($is_cityarea_hotel){?>
						<option value="2_52" >酒店</option>
						<?php }
						if($is_cityarea_shop){?>
						<option value="2_53" >线下商城-首页</option>
                        <option value="2_54" >线下商城-商家列表</option>
						<?php }
						?>
						<?php if($is_cityarea_finance){?>
                        <option value="2_63" >金融-贷款</option>
                        <option value="2_61" >金融-信用卡</option>
                        <option value="2_62" >金融-保险</option>
                        <?php }?>
						<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
						</select>
						<select name="product_type_1_1" id="product_type_1_1" style="display:none;margin:5px 0 0 81px;" onchange="changeSliderType(1,this.value);"><!--分类-->
						<!--<option value=-1>--------请选择--------</option>-->
						<?php
							$type_len = count($type_arr[-1]);
							for($i = 0;$i<$type_len;$i++){
								$typearr = explode("_",$type_arr[-1][$i]);
								$type_id = $typearr[0];
								$type_name = $typearr[1];

						?>
						<option value="<?php echo $type_id; ?>_1"><?php echo $type_name; ?></option>
						<?php
							$type_len2 = count($type_arr[$type_id]);
							for($ii = 0;$ii<$type_len2;$ii++){
								$typearr2 = explode("_",$type_arr[$type_id][$ii]);
								$type_id2 = $typearr2[0];
								$type_name2 = $typearr2[1];
						?>
						<option value="<?php echo $type_id2; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
						<?php
							$type_len3 = count($type_arr[$type_id2]);
							for($iii = 0;$iii<$type_len3;$iii++){
								$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
								$type_id3 = $typearr3[0];
								$type_name3 = $typearr3[1];
						?>
						<option value="<?php echo $type_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
						<?php
							$type_len4 = count($type_arr[$type_id3]);
							for($iiii = 0;$iiii<$type_len4;$iiii++){
								$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
								$type_id4 = $typearr4[0];
								$type_name4 = $typearr4[1];
						?>
						<option value="<?php echo $type_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
						<?php
							}
							}
							}
						}
						?>
						</select>
						<div id="div_products_1_1" style="display:none;padding:5px 0 0 64px">
							<select name="product_detail_id_1_1" id="product_detail_id_1_1" class="moveLeft">
								<option value=-1>---请选择一款产品---</option>
							</select>
						</div>
						<div id="package_product_id" style="display:none;padding:5px 0 0 64px">
							<select name="package_product_id" id="package_product_id">
								<option value=-1>---全部---</option>
								<?php foreach ($package_list as $key => $value) { ?>
								<option value='<?php echo $value['id'] ?>'><?php echo $value['package_name'] ?></option>
								<?php } ?>
							</select>
						</div>

						<input type="text" name="selector_title_1" id="selector_title" value="" disabled />
                        <button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id_1" id="selector_id" value="<?php echo $value['linktype']?>" />

						<input type="radio" name="sel_link_type_1" id="sel_link_type_1_1_2" value="2" /> <span>填写链接：</span>
						<input type="text" name="custom_link_1_1" id="custom_link" onchange="check_url(this)" value="" style="width:175px;" />
						<div class="clear"></div>
								</dd>
							</dl>
						</div>

					<div class="WSY_microbox WSY_microslide">
						<p>图片2：大图建议尺寸：<label  id="label_slide_2">640*320</label>px</p>
						<dl class="WSY_micro">
							<dt><div class="b_r" id="banner_img_2"><img src="../../../Common/images/Base/home_decoration/contenticon/20140611053000_0.jpg"></div><input type=hidden name="imgids_1_2" id="imgids_1_2"  /> <span><a href="#" value="0" id="a_banner_2"><img src="../../../Common/images/Base/home_decoration/operating_icon/guanbi.png"></a></span></dt>
							<!--上传文件代码开始-->
							<div class="uploader white" id="WSY_whitebox">
								<input type="text" class="filename" readonly/>
								<input type="button" name="file" class="button" value="上传..."/>
								<input type="file" size="20"  name="upfile1_2" id="upfile1_2"/>
						   </div>
						   <!--上传文件代码结束-->
						   <dd>
						   <input type="text" id="search_link" style="display:none;" /><span class="search-btn" style="display:none;">搜索</span>
							<input type="radio" name="sel_link_type_2" id="sel_link_type_1_2_1" value="1" />
						   <span class="show_typebox">链接页面：</span>
						<select name="type_id_1_2" id="type_id_1_2" onchange="change_type(this);" style="display:none;" >

                        <option value="-1" selected="selected">--------请选择--------</option>
						<option value="-16" >首页</option>
						<option value="-6" >全部产品</option>
						<option value="-2" >新品上市</option>
						<option value="-3" >热卖产品</option>
						<option value="-4" >购物车</option>
						<option value="-8" >个人中心</option>
						<option value="-18" >我的订单</option>
						<option value="-7" >产品分类页1</option>
						<option value="-17" >产品分类页2</option>
						<option value="-37" >产品分类页3</option>
						<option value="-47" >产品分类页4</option>
						<option value="-9" >我的微店</option>
						<option value="-5" >限时抢购</option>
						<option value='-33' >区域批发商列表</option>
						<option value="-10" >商城在线客服</option>
						<option value="-11" >礼包列表</option>
						<option value="-12" >VP产品</option>
						<option value="-15" >兑换专区</option>
						<!--option value="-19" >拼团列表</option-->
						<option value="-20" >人气团列表</option>
						<option value="-21" >续费专区</option>
						<option value="-22" >电商直播</option>
						<option value="-23" >语音直播</option>
						<?php if($is_ticket){?>
						<option value="-24" >票务特价机票</option>
						<option value="-25" >票务特价火车票</option>
						<?php }?>
						<?php if($is_exchange){?>
						<option value="-32" >满赠专区</option>
						<?php }?>
						<option value="-95" >砍价活动</option>
						<option value="-96" >众筹新版</option>
						<?php if($is_f2c>0){ ?>
						<option value="-26" >F2C系统</option>
						<?php } ?>
                        <option value="-27" >订货系统登录</option>
                        <option value="-28" >订货系统申请</option>
                        <option value="-29" >订货系统中心</option>
                        <option value="-100" >门店申请</option>
                        <option value="-101" >门店商城模式</option>
                        <option value="-102" >门店店铺模式</option>
                        <option value="-103" >子门店申请</option>
                        <option value="-104" >子门店中心</option>
                        <option value="-19" >拼团商品专区1</option>
                        <option value="-30" >拼团商品专区2</option>
                        <option value="-31" >拼团商品专区3</option>
                        <option value="-34" >积分签到</option>
                        <option value="-35" >积分兑换</option>
						<option value="-36" >限时专区</option>
						<option value="-361" >积分专区</option>
						<?php
							if( !empty($diy_template_link) ){
						?>
						<optgroup label="---------------自定义模板---------------"></optgroup>
						<?php

							for( $i = 0; $i < $diy_template_count; $i++ ){
								$diy_template_arr = explode('_',$diy_template_link[$i]);
								$diy_template_id = $diy_template_arr[0];
								$diy_template_name = $diy_template_arr[1];
						?>
						<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
						<?php }}?>
						<optgroup label="---------------产品分类---------------"></optgroup>
						<!--<?php
						  if($typesize>0){
						     for($i=0;$i<$typesize; $i++){
							    $typestr= $typeLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
						<?php  }
						}
						?>-->
						<option value="-40">多级分类</option>
						<optgroup label="---------------图文消息---------------"></optgroup>
						<?php
						  if($imginfosize>0){
						     for($i=0;$i<$imginfosize; $i++){
							    $typestr= $imginfoLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
						<?php  }

						} ?>

						<optgroup label="---------------优惠券---------------"></optgroup>
						<option value="1all_60" >优惠券全部</option>
						<?php
						  if($couponsize>0){
						     for($i=0;$i<$couponsize; $i++){
							    $couponstr= $couponLst->Get($i);

							    $couponarr = explode("_",$couponstr);
								$coupon_id = $couponarr[0];
								$coupon_name = $couponarr[1];


						?>
						  <option value="<?php echo $coupon_id; ?>_60" ><?php echo $coupon_name; ?></option>
						<?php  }

						} ?>

						<?php if($is_cityarea_caterer){?>
						<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
						<?php if($cityareaCaterersize>0){
								for($i=0;$i<$cityareaCaterersize; $i++){
								$typestr= $cityareaCatererLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_ktv){?>
						<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
						<?php if($cityareaKTVsize>0){
								for($i=0;$i<$cityareaKTVsize; $i++){
								$typestr= $cityareaKTVLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_hotel){?>
						<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
						<?php if($cityareaHotelsize>0){
								for($i=0;$i<$cityareaHotelsize; $i++){
								$typestr= $cityareaHotelLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_shop){?>
						<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
						<?php if($cityareaShopsize>0){
								for($i=0;$i<$cityareaShopsize; $i++){
								$typestr= $cityareaShopLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea){?>
						<optgroup label="-----------商圈行业列表-----------"></optgroup>
						<?php }
						if($is_cityarea_caterer){?>
						<option value="2_50" >美食</option>
						<?php }
						if($is_cityarea_ktv){?>
						<option value="2_51" >KTV</option>
						<?php }
						if($is_cityarea_hotel){?>
						<option value="2_52" >酒店</option>
						<?php }
						if($is_cityarea_shop){?>
						<option value="2_53" >线下商城-首页</option>
                        <option value="2_54" >线下商城-商家列表</option>
						<?php }
						?>
						<?php if($is_cityarea_finance){?>
                        <option value="2_63" >金融-贷款</option>
                        <option value="2_61" >金融-信用卡</option>
                        <option value="2_62" >金融-保险</option>
                        <?php }?>
						<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
						</select>
						<select name="product_type_1_2" id="product_type_1_2" style="display:none;margin:5px 0 0 81px;" onchange="changeSliderType(2,this.value);"><!--分类-->
						<!--<option value=-1>--------请选择--------</option>-->
						<?php
							$type_len = count($type_arr[-1]);
							for($i = 0;$i<$type_len;$i++){
								$typearr = explode("_",$type_arr[-1][$i]);
								$type_id = $typearr[0];
								$type_name = $typearr[1];

						?>
						<option value="<?php echo $type_id; ?>_1"><?php echo $type_name; ?></option>
						<?php
							$type_len2 = count($type_arr[$type_id]);
							for($ii = 0;$ii<$type_len2;$ii++){
								$typearr2 = explode("_",$type_arr[$type_id][$ii]);
								$type_id2 = $typearr2[0];
								$type_name2 = $typearr2[1];
						?>
						<option value="<?php echo $type_id2; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
						<?php
							$type_len3 = count($type_arr[$type_id2]);
							for($iii = 0;$iii<$type_len3;$iii++){
								$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
								$type_id3 = $typearr3[0];
								$type_name3 = $typearr3[1];
						?>
						<option value="<?php echo $type_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
						<?php
							$type_len4 = count($type_arr[$type_id3]);
							for($iiii = 0;$iiii<$type_len4;$iiii++){
								$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
								$type_id4 = $typearr4[0];
								$type_name4 = $typearr4[1];
						?>
						<option value="<?php echo $type_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
						<?php
							}
							}
							}
						}
						?>
						</select>
						<div id="div_products_1_2" style="display:none;padding:5px 0 0 64px">
							<select name="product_detail_id_1_2" id="product_detail_id_1_2" class="moveLeft">
								<option value=-1>---请选择一款产品---</option>
							</select>
						</div>
						<div id="package_product_id" style="display:none;padding:5px 0 0 64px">
							<select name="package_product_id" id="package_product_id">
								<option value=-1>---全部---</option>
								<?php foreach ($package_list as $key => $value) { ?>
								<option value='<?php echo $value['id'] ?>'><?php echo $value['package_name'] ?></option>
								<?php } ?>
							</select>
						</div>
						<input type="text" name="selector_title_2" id="selector_title" value="" disabled />
                        <button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id_2" id="selector_id" value="-1" />
						<input type="radio" name="sel_link_type_2" id="sel_link_type_1_2_2" value="2" /> <span>填写链接：</span>
						<input type="text" name="custom_link_1_2" id="custom_link" onchange="check_url(this)" value="" style="width:175px;" />
						<div class="clear"></div>
								</dd>
							</dl>
						</div>

					<div class="WSY_microbox WSY_microslide">
						<p>图片3：大图建议尺寸：<label  id="label_slide_3">640*320</label>px</p>
						<dl class="WSY_micro">
							<dt><div class="b_r" id="banner_img_3"><img src="../../../Common/images/Base/home_decoration/contenticon/20140611053000_0.jpg"></div><input type=hidden name="imgids_1_3" id="imgids_1_3"  /> <span><a href="#" value="0" id="a_banner_3"><img src="../../../Common/images/Base/home_decoration/operating_icon/guanbi.png"></a></span></dt>
							<!--上传文件代码开始-->
							<div class="uploader white" id="WSY_whitebox">
								<input type="text" class="filename" readonly/>
								<input type="button" name="file" class="button" value="上传..."/>
								<input type="file" size="20"  name="upfile1_3" id="upfile1_3"/>
						   </div>
						   <!--上传文件代码结束-->
						   <dd>
						   <input type="text" id="search_link" style="display:none;" /><span style="display:none;" class="search-btn">搜索</span>
							<input type="radio" name="sel_link_type_3" id="sel_link_type_1_3_1" value="1" />
						   <span class="show_typebox">链接页面：</span>
						<select name="type_id_1_3" id="type_id_1_3" onchange="change_type(this);" style="display:none;">

                        <option value="-1" selected="selected">--------请选择--------</option>
						<option value="-16" >首页</option>
						<option value="-6" >全部产品</option>
						<option value="-2" >新品上市</option>
						<option value="-3" >热卖产品</option>
						<option value="-4" >购物车</option>
						<option value="-8" >个人中心</option>
						<option value="-18" >我的订单</option>
						<option value="-7" >产品分类页</option>
						<option value="-17" >产品分类页2</option>
						<option value="-37" >产品分类页3</option>
						<option value="-47" >产品分类页4</option>
						<option value="-9" >我的微店</option>
						<option value="-5" >限时抢购</option>
						<option value='-33' >区域批发商列表</option>
						<option value="-10" >商城在线客服</option>
						<option value="-11" >礼包列表</option>
						<option value="-12" >VP产品</option>
						<option value="-15" >兑换专区</option>
						<!--option value="-19" >拼团列表</option-->
						<option value="-20" >人气团列表</option>
						<option value="-21" >续费专区</option>
						<option value="-22" >电商直播</option>
						<option value="-23" >语音直播</option>
						<?php if($is_ticket){?>
						<option value="-24" >票务特价机票</option>
						<option value="-25" >票务特价火车票</option>
						<?php }?>
						<?php if($is_exchange){?>
						<option value="-32" >满赠专区</option>
						<?php }?>
						<option value="-95" >砍价活动</option>
						<option value="-96" >众筹新版</option>
						<?php if($is_f2c>0){ ?>
						<option value="-26" >F2C系统</option>
						<?php } ?>
                        <option value="-27" >订货系统登录</option>
                        <option value="-28" >订货系统申请</option>
                        <option value="-29" >订货系统中心</option>
                        <option value="-100" >门店申请</option>
                        <option value="-101" >门店商城模式</option>
                        <option value="-102" >门店店铺模式</option>
                        <option value="-103" >子门店申请</option>
                        <option value="-104" >子门店中心</option>
                        <option value="-19" >拼团商品专区1</option>
                        <option value="-30" >拼团商品专区2</option>
                        <option value="-31" >拼团商品专区3</option>
                        <option value="-34" >积分签到</option>
                        <option value="-35" >积分兑换</option>
						<option value="-36" >限时专区</option>
						<option value="-361" >积分专区</option>
						<?php
							if( !empty($diy_template_link) ){
						?>
						<optgroup label="---------------自定义模板---------------"></optgroup>
						<?php

							for( $i = 0; $i < $diy_template_count; $i++ ){
								$diy_template_arr = explode('_',$diy_template_link[$i]);
								$diy_template_id = $diy_template_arr[0];
								$diy_template_name = $diy_template_arr[1];
						?>
						<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
						<?php }}?>
						<optgroup label="---------------产品分类---------------"></optgroup>
						<!--<?php
						  if($typesize>0){
						     for($i=0;$i<$typesize; $i++){
							    $typestr= $typeLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
						<?php  }
						}
						?>-->
						<option value="-40">多级分类</option>
						<optgroup label="---------------图文消息---------------"></optgroup>
						<?php
						  if($imginfosize>0){
						     for($i=0;$i<$imginfosize; $i++){
							    $typestr= $imginfoLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
						<?php  }

						} ?>

						<optgroup label="---------------优惠券---------------"></optgroup>
						<option value="1all_60" >优惠券全部</option>
						<?php
						  if($couponsize>0){
						     for($i=0;$i<$couponsize; $i++){
							    $couponstr= $couponLst->Get($i);

							    $couponarr = explode("_",$couponstr);
								$coupon_id = $couponarr[0];
								$coupon_name = $couponarr[1];


						?>
						  <option value="<?php echo $coupon_id; ?>_60" ><?php echo $coupon_name; ?></option>
						<?php  }

						} ?>

						<?php if($is_cityarea_caterer){?>
						<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
						<?php if($cityareaCaterersize>0){
								for($i=0;$i<$cityareaCaterersize; $i++){
								$typestr= $cityareaCatererLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_ktv){?>
						<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
						<?php if($cityareaKTVsize>0){
								for($i=0;$i<$cityareaKTVsize; $i++){
								$typestr= $cityareaKTVLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_hotel){?>
						<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
						<?php if($cityareaHotelsize>0){
								for($i=0;$i<$cityareaHotelsize; $i++){
								$typestr= $cityareaHotelLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_shop){?>
						<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
						<?php if($cityareaShopsize>0){
								for($i=0;$i<$cityareaShopsize; $i++){
								$typestr= $cityareaShopLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea){?>
						<optgroup label="-----------商圈行业列表-----------"></optgroup>
						<?php }
						if($is_cityarea_caterer){?>
						<option value="2_50" >美食</option>
						<?php }
						if($is_cityarea_ktv){?>
						<option value="2_51" >KTV</option>
						<?php }
						if($is_cityarea_hotel){?>
						<option value="2_52" >酒店</option>
						<?php }
						if($is_cityarea_shop){?>
						<option value="2_53" >线下商城-首页</option>
                        <option value="2_54" >线下商城-商家列表</option>
						<?php }
						?>
						<?php if($is_cityarea_finance){?>
                        <option value="2_63" >金融-贷款</option>
                        <option value="2_61" >金融-信用卡</option>
                        <option value="2_62" >金融-保险</option>
                        <?php }?>
						<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
						</select>
						<select name="product_type_1_3" id="product_type_1_3" style="display:none;margin:5px 0 0 81px;" onchange="changeSliderType(3,this.value);"><!--分类-->
						<!--<option value=-1>--------请选择--------</option>-->
						<?php
							$type_len = count($type_arr[-1]);
							for($i = 0;$i<$type_len;$i++){
								$typearr = explode("_",$type_arr[-1][$i]);
								$type_id = $typearr[0];
								$type_name = $typearr[1];

						?>
						<option value="<?php echo $type_id; ?>_1"><?php echo $type_name; ?></option>
						<?php
							$type_len2 = count($type_arr[$type_id]);
							for($ii = 0;$ii<$type_len2;$ii++){
								$typearr2 = explode("_",$type_arr[$type_id][$ii]);
								$type_id2 = $typearr2[0];
								$type_name2 = $typearr2[1];
						?>
						<option value="<?php echo $type_id2; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
						<?php
							$type_len3 = count($type_arr[$type_id2]);
							for($iii = 0;$iii<$type_len3;$iii++){
								$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
								$type_id3 = $typearr3[0];
								$type_name3 = $typearr3[1];
						?>
						<option value="<?php echo $type_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
						<?php
							$type_len4 = count($type_arr[$type_id3]);
							for($iiii = 0;$iiii<$type_len4;$iiii++){
								$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
								$type_id4 = $typearr4[0];
								$type_name4 = $typearr4[1];
						?>
						<option value="<?php echo $type_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
						<?php
							}
							}
							}
						}
						?>
						</select>
						<div id="div_products_1_3" style="display:none;padding:5px 0 0 64px">
							<select name="product_detail_id_1_3" id="product_detail_id_1_3" class="moveLeft">
								<option value=-1>---请选择一款产品---</option>
							</select>
						</div>
						<div id="package_product_id" style="display:none;padding:5px 0 0 64px">
							<select name="package_product_id" id="package_product_id">
								<option value=-1>---全部---</option>
								<?php foreach ($package_list as $key => $value) { ?>
								<option value='<?php echo $value['id'] ?>'><?php echo $value['package_name'] ?></option>
								<?php } ?>
							</select>
						</div>
						<input type="text" name="selector_title_3" id="selector_title" value="" disabled />
                        <button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id_3" id="selector_id" value="-1" />
						<input type="radio" name="sel_link_type_3" id="sel_link_type_1_3_2" value="2" /> <span>填写链接：</span>
						<input type="text" name="custom_link_1_3" id="custom_link" onchange="check_url(this)" value="" style="width:175px;" />
						<div class="clear"></div>
								</dd>
							</dl>
						</div>

					<div class="WSY_microbox WSY_microslide">
						<p>图片4：大图建议尺寸：<label  id="label_slide_4">640*320</label>px</p>
						<dl class="WSY_micro">
							<dt><div class="b_r" id="banner_img_4"><img src="../../../Common/images/Base/home_decoration/contenticon/20140611053000_0.jpg"></div><input type=hidden name="imgids_1_4" id="imgids_1_4"  /> <span><a href="#" value="0" id="a_banner_4"><img src="../../../Common/images/Base/home_decoration/operating_icon/guanbi.png"></a></span></dt>
							<!--上传文件代码开始-->
							<div class="uploader white" id="WSY_whitebox">
								<input type="text" class="filename" readonly/>
								<input type="button" name="file" class="button" value="上传..."/>
								<input type="file" size="20"  name="upfile1_4" id="upfile1_4"/>
						   </div>
						   <!--上传文件代码结束-->
						   <dd>
						   <input type="text" id="search_link" style="display:none;" /><span class="search-btn" style="display:none;">搜索</span>
							<input type="radio" name="sel_link_type_4" id="sel_link_type_1_4_1" value="1" />
						   <span class="show_typebox">链接页面：</span>
						<select name="type_id_1_4" id="type_id_1_4" onchange="change_type(this);" style="display:none;">

                        <option value="-1" selected="selected">--------请选择--------</option>
						<option value="-16" >首页</option>
						<option value="-6" >全部产品</option>
						<option value="-2" >新品上市</option>
						<option value="-3" >热卖产品</option>
						<option value="-4" >购物车</option>
						<option value="-8" >个人中心</option>
						<option value="-18" >我的订单</option>
						<option value="-7" >产品分类页1</option>
						<option value="-17" >产品分类页2</option>
						<option value="-37" >产品分类页3</option>
						<option value="-47" >产品分类页4</option>
						<option value="-9" >我的微店</option>
						<option value="-5" >限时抢购</option>
						<option value='-33' >区域批发商列表</option>
						<option value="-10" >商城在线客服</option>
						<option value="-11" >礼包列表</option>
						<option value="-12" >VP产品</option>
						<option value="-15" >兑换专区</option>
						<!--option value="-19" >拼团列表</option-->
						<option value="-20" >人气团列表</option>
						<option value="-21" >续费专区</option>
						<option value="-22" >电商直播</option>
						<option value="-23" >语音直播</option>
						<?php if($is_ticket){?>
						<option value="-24" >票务特价机票</option>
						<option value="-25" >票务特价火车票</option>
						<?php }?>
						<?php if($is_exchange){?>
						<option value="-32" >满赠专区</option>
						<?php }?>
						<option value="-95" >砍价活动</option>
						<option value="-96" >众筹新版</option>
						<?php if($is_f2c>0){ ?>
						<option value="-26" >F2C系统</option>
						<?php } ?>
						<option value="-27" >订货系统登录</option>
						<option value="-28" >订货系统申请</option>
						<option value="-29" >订货系统中心</option>
						<option value="-100" >门店申请</option>
                        <option value="-101" >门店商城模式</option>
                        <option value="-102" >门店店铺模式</option>
                        <option value="-103" >子门店申请</option>
                        <option value="-104" >子门店中心</option>
						<option value="-19" >拼团商品专区1</option>
                        <option value="-30" >拼团商品专区2</option>
                        <option value="-31" >拼团商品专区3</option>
                        <option value="-34" >积分签到</option>
                        <option value="-35" >积分兑换</option>
						<option value="-36" >限时专区</option>
						<option value="-361" >积分专区</option>
						<?php
							if( !empty($diy_template_link) ){
						?>
						<optgroup label="---------------自定义模板---------------"></optgroup>
						<?php

							for( $i = 0; $i < $diy_template_count; $i++ ){
								$diy_template_arr = explode('_',$diy_template_link[$i]);
								$diy_template_id = $diy_template_arr[0];
								$diy_template_name = $diy_template_arr[1];
						?>
						<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
						<?php }}?>
						<optgroup label="---------------产品分类---------------"></optgroup>
						<!--<?php
						  if($typesize>0){
						     for($i=0;$i<$typesize; $i++){
							    $typestr= $typeLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
						<?php  }
						}
						?>-->
						<option value="-40">多级分类</option>
						<optgroup label="---------------图文消息---------------"></optgroup>
						<?php
						  if($imginfosize>0){
						     for($i=0;$i<$imginfosize; $i++){
							    $typestr= $imginfoLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
						<?php  }

						} ?>

						<optgroup label="---------------优惠券---------------"></optgroup>
						<option value="1all_60" >优惠券全部</option>
						<?php
						  if($couponsize>0){
						     for($i=0;$i<$couponsize; $i++){
							    $couponstr= $couponLst->Get($i);

							    $couponarr = explode("_",$couponstr);
								$coupon_id = $couponarr[0];
								$coupon_name = $couponarr[1];


						?>
						  <option value="<?php echo $coupon_id; ?>_60" ><?php echo $coupon_name; ?></option>
						<?php  }

						} ?>

						<?php if($is_cityarea_caterer){?>
						<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
						<?php if($cityareaCaterersize>0){
								for($i=0;$i<$cityareaCaterersize; $i++){
								$typestr= $cityareaCatererLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_ktv){?>
						<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
						<?php if($cityareaKTVsize>0){
								for($i=0;$i<$cityareaKTVsize; $i++){
								$typestr= $cityareaKTVLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_hotel){?>
						<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
						<?php if($cityareaHotelsize>0){
								for($i=0;$i<$cityareaHotelsize; $i++){
								$typestr= $cityareaHotelLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_shop){?>
						<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
						<?php if($cityareaShopsize>0){
								for($i=0;$i<$cityareaShopsize; $i++){
								$typestr= $cityareaShopLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea){?>
						<optgroup label="-----------商圈行业列表-----------"></optgroup>
						<?php }
						if($is_cityarea_caterer){?>
						<option value="2_50" >美食</option>
						<?php }
						if($is_cityarea_ktv){?>
						<option value="2_51" >KTV</option>
						<?php }
						if($is_cityarea_hotel){?>
						<option value="2_52" >酒店</option>
						<?php }
						if($is_cityarea_shop){?>
						<option value="2_53" >线下商城-首页</option>
                        <option value="2_54" >线下商城-商家列表</option>
						<?php }
						?>
						<?php if($is_cityarea_finance){?>
                        <option value="2_63" >金融-贷款</option>
                        <option value="2_61" >金融-信用卡</option>
                        <option value="2_62" >金融-保险</option>
                        <?php }?>
						<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
						</select>
						<select name="product_type_1_4" id="product_type_1_4" style="display:none;margin:5px 0 0 81px;" onchange="changeSliderType(4,this.value);"><!--分类-->
						<!--<option value=-1>--------请选择--------</option>-->
						<?php
							$type_len = count($type_arr[-1]);
							for($i = 0;$i<$type_len;$i++){
								$typearr = explode("_",$type_arr[-1][$i]);
								$type_id = $typearr[0];
								$type_name = $typearr[1];

						?>
						<option value="<?php echo $type_id; ?>_1"><?php echo $type_name; ?></option>
						<?php
							$type_len2 = count($type_arr[$type_id]);
							for($ii = 0;$ii<$type_len2;$ii++){
								$typearr2 = explode("_",$type_arr[$type_id][$ii]);
								$type_id2 = $typearr2[0];
								$type_name2 = $typearr2[1];
						?>
						<option value="<?php echo $type_id2; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
						<?php
							$type_len3 = count($type_arr[$type_id2]);
							for($iii = 0;$iii<$type_len3;$iii++){
								$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
								$type_id3 = $typearr3[0];
								$type_name3 = $typearr3[1];
						?>
						<option value="<?php echo $type_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
						<?php
							$type_len4 = count($type_arr[$type_id3]);
							for($iiii = 0;$iiii<$type_len4;$iiii++){
								$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
								$type_id4 = $typearr4[0];
								$type_name4 = $typearr4[1];
						?>
						<option value="<?php echo $type_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
						<?php
							}
							}
							}
						}
						?>
						</select>
						<div id="div_products_1_4" style="display:none;padding:5px 0 0 64px;">
							<select name="product_detail_id_1_4" id="product_detail_id_1_4" class="moveLeft">
								<option value=-1>---请选择一款产品---</option>
							</select>
						</div>
						<div id="package_product_id" style="display:none;padding:5px 0 0 64px">
							<select name="package_product_id" id="package_product_id">
								<option value=-1>---全部---</option>
								<?php foreach ($package_list as $key => $value) { ?>
								<option value='<?php echo $value['id'] ?>'><?php echo $value['package_name'] ?></option>
								<?php } ?>
							</select>
						</div>
						<input type="text" name="selector_title_4" id="selector_title" value="" disabled />
                        <button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id_4" id="selector_id" value="-1" />
						<input type="radio" name="sel_link_type_4" id="sel_link_type_1_4_2" value="2" /> <span>填写链接：</span>
						<input type="text" name="custom_link_1_4" id="custom_link" onchange="check_url(this)" value="" style="width:175px;" />
						<div class="clear"></div>
								</dd>
							</dl>
						</div>

					<div class="WSY_microbox WSY_microslide">
						<p>图片5：大图建议尺寸：<label  id="label_slide_5">640*320</label>px</p>
						<dl class="WSY_micro">
							<dt><div class="b_r" id="banner_img_5"><img src="../../../Common/images/Base/home_decoration/contenticon/20140611053000_0.jpg"></div><input type=hidden name="imgids_1_5" id="imgids_1_5"  /> <span><a href="#" value="0" id="a_banner_5"><img src="../../../Common/images/Base/home_decoration/operating_icon/guanbi.png"></a></span></dt>
							<!--上传文件代码开始-->
							<div class="uploader white" id="WSY_whitebox">
								<input type="text" class="filename" readonly/>
								<input type="button" name="file" class="button" value="上传..."/>
								<input type="file" size="20"  name="upfile1_5" id="upfile1_5"/>
						   </div>
						   <!--上传文件代码结束-->
						   <dd>
						   <input type="text" id="search_link" style="display:none;" /><span style="display:none;" class="search-btn">搜索</span>
							<input type="radio" name="sel_link_type_5" id="sel_link_type_1_5_1" value="1" />
						   <span class="show_typebox">链接页面：</span>
						<select name="type_id_1_5" id="type_id_1_5" onchange="change_type(this);" style="display:none;">

                        <option value="-1" selected="selected">--------请选择--------</option>
						<option value="-16" >首页</option>
						<option value="-6" >全部产品</option>
						<option value="-2" >新品上市</option>
						<option value="-3" >热卖产品</option>
						<option value="-4" >购物车</option>
						<option value="-8" >个人中心</option>
						<option value="-18" >我的订单</option>
						<option value="-7" >产品分类页1</option>
						<option value="-17" >产品分类页2</option>
						<option value="-37" >产品分类页3</option>
						<option value="-47" >产品分类页4</option>
						<option value="-9" >我的微店</option>
						<option value="-5" >限时抢购</option>
						<option value='-33' >区域批发商列表</option>
						<option value="-10" >商城在线客服</option>
						<option value="-11" >礼包列表</option>
						<option value="-12" >VP产品</option>
						<option value="-15" >兑换专区</option>
						<!--option value="-19" >拼团列表</option-->
						<option value="-20" >人气团列表</option>
						<option value="-21" >续费专区</option>
						<option value="-22" >电商直播</option>
						<option value="-23" >语音直播</option>
						<?php if($is_ticket){?>
						<option value="-24" >票务特价机票</option>
						<option value="-25" >票务特价火车票</option>
						<?php }?>
						<?php if($is_exchange){?>
						<option value="-32" >满赠专区</option>
						<?php }?>
						<option value="-95" >砍价活动</option>
						<option value="-96" >众筹新版</option>
						<?php if($is_f2c>0){ ?>
						<option value="-26" >F2C系统</option>
						<?php } ?>
                        <option value="-27" >订货系统登录</option>
                        <option value="-28" >订货系统申请</option>
                        <option value="-29" >订货系统中心</option>
                        <option value="-100" >门店申请</option>
                        <option value="-101" >门店商城模式</option>
                        <option value="-102" >门店店铺模式</option>
                        <option value="-103" >子门店申请</option>
                        <option value="-104" >子门店中心</option>
                        <option value="-19" >拼团商品专区1</option>
                        <option value="-30" >拼团商品专区2</option>
                        <option value="-31" >拼团商品专区3</option>
                        <option value="-34" >积分签到</option>
                        <option value="-35" >积分兑换</option>
						<option value="-36" >限时专区</option>
						<option value="-361" >积分专区</option>
						<?php
							if( !empty($diy_template_link) ){
						?>
						<optgroup label="---------------自定义模板---------------"></optgroup>
						<?php

							for( $i = 0; $i < $diy_template_count; $i++ ){
								$diy_template_arr = explode('_',$diy_template_link[$i]);
								$diy_template_id = $diy_template_arr[0];
								$diy_template_name = $diy_template_arr[1];
						?>
						<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
						<?php }}?>
						<optgroup label="---------------产品分类---------------"></optgroup>
						<!--<?php
						  if($typesize>0){
						     for($i=0;$i<$typesize; $i++){
							    $typestr= $typeLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						<option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
						<?php  }
						}
						?>-->
						<option value="-40">多级分类</option>
						<optgroup label="---------------图文消息---------------"></optgroup>
						<?php
						  if($imginfosize>0){
						     for($i=0;$i<$imginfosize; $i++){
							    $typestr= $imginfoLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						<option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
						<?php  }

						} ?>

						<optgroup label="---------------优惠券---------------"></optgroup>
						<option value="1all_60" >优惠券全部</option>
						<?php
						  if($couponsize>0){
						     for($i=0;$i<$couponsize; $i++){
							    $couponstr= $couponLst->Get($i);

							    $couponarr = explode("_",$couponstr);
								$coupon_id = $couponarr[0];
								$coupon_name = $couponarr[1];


						?>
						  <option value="<?php echo $coupon_id; ?>_60" ><?php echo $coupon_name; ?></option>
						<?php  }

						} ?>

						<?php if($is_cityarea_caterer){?>
						<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
						<?php if($cityareaCaterersize>0){
								for($i=0;$i<$cityareaCaterersize; $i++){
								$typestr= $cityareaCatererLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_ktv){?>
						<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
						<?php if($cityareaKTVsize>0){
								for($i=0;$i<$cityareaKTVsize; $i++){
								$typestr= $cityareaKTVLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_hotel){?>
						<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
						<?php if($cityareaHotelsize>0){
								for($i=0;$i<$cityareaHotelsize; $i++){
								$typestr= $cityareaHotelLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_shop){?>
						<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
						<?php if($cityareaShopsize>0){
								for($i=0;$i<$cityareaShopsize; $i++){
								$typestr= $cityareaShopLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea){?>
						<optgroup label="-----------商圈行业列表-----------"></optgroup>
						<?php }
						if($is_cityarea_caterer){?>
						<option value="2_50" >美食</option>
						<?php }
						if($is_cityarea_ktv){?>
						<option value="2_51" >KTV</option>
						<?php }
						if($is_cityarea_hotel){?>
						<option value="2_52" >酒店</option>
						<?php }
						if($is_cityarea_shop){?>
						<option value="2_53" >线下商城-首页</option>
                        <option value="2_54" >线下商城-商家列表</option>
						<?php }
						?>
						<?php if($is_cityarea_finance){?>
                        <option value="2_63" >金融-贷款</option>
                        <option value="2_61" >金融-信用卡</option>
                        <option value="2_62" >金融-保险</option>
                        <?php }?>
						<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
						</select>
						<select name="product_type_1_5" id="product_type_1_5" style="display:none;margin:5px 0 0 81px;" onchange="changeSliderType(5,this.value);"><!--分类-->
						<!--<option value=-1>--------请选择--------</option>-->
						<?php
							$type_len = count($type_arr[-1]);
							for($i = 0;$i<$type_len;$i++){
								$typearr = explode("_",$type_arr[-1][$i]);
								$type_id = $typearr[0];
								$type_name = $typearr[1];

						?>
						<option value="<?php echo $type_id; ?>_1"><?php echo $type_name; ?></option>
						<?php
							$type_len2 = count($type_arr[$type_id]);
							for($ii = 0;$ii<$type_len2;$ii++){
								$typearr2 = explode("_",$type_arr[$type_id][$ii]);
								$type_id2 = $typearr2[0];
								$type_name2 = $typearr2[1];
						?>
						<option value="<?php echo $type_id2; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
						<?php
							$type_len3 = count($type_arr[$type_id2]);
							for($iii = 0;$iii<$type_len3;$iii++){
								$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
								$type_id3 = $typearr3[0];
								$type_name3 = $typearr3[1];
						?>
						<option value="<?php echo $type_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
						<?php
							$type_len4 = count($type_arr[$type_id3]);
							for($iiii = 0;$iiii<$type_len4;$iiii++){
								$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
								$type_id4 = $typearr4[0];
								$type_name4 = $typearr4[1];
						?>
						<option value="<?php echo $type_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
						<?php
							}
							}
							}
						}
						?>
						</select>
						<div id="div_products_1_5" style="display:none;padding:5px 0 0 64px;">
							<select name="product_detail_id_1_5" id="product_detail_id_1_5" class="moveLeft">
								<option value=-1>---请选择一款产品---</option>
							</select>
						</div>
						<div id="package_product_id" style="display:none;padding:5px 0 0 64px">
							<select name="package_product_id" id="package_product_id">
								<option value=-1>---全部---</option>
								<?php foreach ($package_list as $key => $value) { ?>
								<option value='<?php echo $value['id'] ?>'><?php echo $value['package_name'] ?></option>
								<?php } ?>
							</select>
						</div>

						<input type="text" name="selector_title_5" id="selector_title" value="" disabled />
                        <button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id_5" id="selector_id" value="-1" />

						<input type="radio" name="sel_link_type_5" id="sel_link_type_1_5_2" value="2" /> <span>填写链接：</span>
						<input type="text" name="custom_link_1_5" id="custom_link" onchange="check_url(this)" value="" style="width:175px;" />
						<div class="clear"></div>
								</dd>
							</dl>
						</div>
					<div style="clear:both;"></div>
				<?php

				/**********4M  start**********/

               if($is_shopgeneral and $general_template_id=37){
				   if($is_samelevel==0){	//不是厂家总店
				?>
				<div class="u4M">
				 <p>---总部轮播图---</p>
				<?php
				  for($m=0;$m<$general_slider_num; $m++){
				     $num = $m+6;

				?>

				<div class="item">
					<div class="rows">
						<div class="b_l">
							<strong>图片(<?php echo $num; ?>)</strong><span class="tips">大图建议尺寸：<label>640*320</label>px</span>
							<div class="blank6"></div>
							<div>
							</div>
						</div>
						<div class="b_r" id="banner_img_<?php echo $num; ?>">
						   <a href="fengge47/images/banner.jpg" target="_blank"><img src="fengge47/images/banner.jpg"></a>
						</div>
						<input type=hidden name="imgids_1_<?php echo $num; ?>" id="imgids_1_<?php echo $num; ?>"  />
					</div>
					<div class="blank9"></div>
					<div class="rows url_select" style="display: block;">
						<!--<div class="u_l">
							<input type="radio" name="sel_link_type_<?php echo $num;?>" id="sel_link_type_1_<?php echo $num; ?>_1" value="1" disabled />
							链接页面：
						</div>-->
						<div class="u_r">
						<input type="radio" name="sel_link_type_<?php echo $num;?>" id="sel_link_type_1_<?php echo $num; ?>_1" value="1" disabled />
						链接页面：
						<select  name="type_id_1_<?php echo $num; ?>" disabled="disabled" id="type_id_1_<?php echo $num; ?>"  onchange="change_type_4m(this);">
						<option value="-1" selected="selected">--------请选择--------</option>
						<option value="-16" >首页</option>
						<option value="-6" >全部产品</option>
						<option value="-2" >新品上市</option>
						<option value="-3" >热卖产品</option>
						<option value="-4" >购物车</option>
						<option value="-8" >个人中心</option>
						<option value="-18" >我的订单</option>
						<option value="-7" >产品分类页1</option>
						<option value="-17" >产品分类页2</option>
						<option value="-37" >产品分类页3</option>
						<option value="-47" >产品分类页4</option>
						<option value="-9" >我的微店</option>
						<option value="-5" >限时抢购</option>
						<option value='-33' >区域批发商列表</option>
						<option value="-10" >商城在线客服</option>
						<option value="-11" >礼包列表</option>
						<option value="-12" >VP产品</option>
						<option value="-13" >趣味测试</option>
						<option value="-14" >软文发布</option>
						<option value="-15" >兑换专区</option>
						<!--option value="-19" >拼团列表</option-->
						<option value="-20" >人气团列表</option>
						<option value="-21" >续费专区</option>
						<option value="-22" >电商直播</option>
						<option value="-23" >语音直播</option>
						<?php if($is_ticket){?>
						<option value="-24" >票务特价机票</option>
						<option value="-25" >票务特价火车票</option>
						<?php }?>
						<?php if($is_exchange){?>
						<option value="-32" >满赠专区</option>
						<?php }?>
						<option value="-95" >砍价活动</option>
						<option value="-96" >众筹新版</option>
						<?php if($is_f2c>0){ ?>
						<option value="-26" >F2C系统</option>
						<?php } ?>
                        <option value="-27" >订货系统登录</option>
                        <option value="-28" >订货系统申请</option>
                        <option value="-29" >订货系统中心</option>
                        <option value="-19" >拼团商品专区1</option>
                        <option value="-30" >拼团商品专区2</option>
                        <option value="-31" >拼团商品专区3</option>
                        <option value="-34" >积分签到</option>
                        <option value="-35" >积分兑换</option>
						<option value="-36" >限时专区</option>
						<option value="-361" >积分专区</option>
						<?php
							if( !empty($diy_template_link) ){
						?>
						<optgroup label="---------------自定义模板---------------"></optgroup>
						<?php

							for( $i = 0; $i < $diy_template_count; $i++ ){
								$diy_template_arr = explode('_',$diy_template_link[$i]);
								$diy_template_id = $diy_template_arr[0];
								$diy_template_name = $diy_template_arr[1];
						?>
						<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
						<?php }}?>
						<optgroup label="---------------产品分类---------------"></optgroup>
						<!--<?php
						  if($typesize>0){
						     for($i=0;$i<$typesize; $i++){
							    $typestr= $typeLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
						<?php  }

						} ?>-->
						<option value="-40">多级分类</option>
						<optgroup label="---------------图文消息---------------"></optgroup>
						<?php
						  if($imginfosize>0){
						     for($i=0;$i<$imginfosize; $i++){
							    $typestr= $imginfoLst->Get($i);

							    $typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];


						?>
						  <option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
						<?php  }

						} ?>

						<optgroup label="---------------优惠券---------------"></optgroup>
						<option value="1all_60" >优惠券全部</option>
						<?php
						  if($couponsize>0){
						     for($i=0;$i<$couponsize; $i++){
							    $couponstr= $couponLst->Get($i);

							    $couponarr = explode("_",$couponstr);
								$coupon_id = $couponarr[0];
								$coupon_name = $couponarr[1];


						?>
						  <option value="<?php echo $coupon_id; ?>_60" ><?php echo $coupon_name; ?></option>
						<?php  }

						} ?>

						<?php if($is_cityarea_caterer){?>
						<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
						<?php if($cityareaCaterersize>0){
								for($i=0;$i<$cityareaCaterersize; $i++){
								$typestr= $cityareaCatererLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_ktv){?>
						<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
						<?php if($cityareaKTVsize>0){
								for($i=0;$i<$cityareaKTVsize; $i++){
								$typestr= $cityareaKTVLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_hotel){?>
						<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
						<?php if($cityareaHotelsize>0){
								for($i=0;$i<$cityareaHotelsize; $i++){
								$typestr= $cityareaHotelLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea_shop){?>
						<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
						<?php if($cityareaShopsize>0){
								for($i=0;$i<$cityareaShopsize; $i++){
								$typestr= $cityareaShopLst->Get($i);

								$typearr = explode("_",$typestr);
								$type_id = $typearr[0];
								$type_name = $typearr[1];
						?>
						<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
						<?php  }

						}} ?>

						<?php if($is_cityarea){?>
						<optgroup label="-----------商圈行业列表-----------"></optgroup>
						<?php }
						if($is_cityarea_caterer){?>
						<option value="2_50" >美食</option>
						<?php }
						if($is_cityarea_ktv){?>
						<option value="2_51" >KTV</option>
						<?php }
						if($is_cityarea_hotel){?>
						<option value="2_52" >酒店</option>
						<?php }
						if($is_cityarea_shop){?>
						<option value="2_53" >线下商城-首页</option>
                        <option value="2_54" >线下商城-商家列表</option>
						<?php }
						?>
						<?php if($is_cityarea_finance){?>
                        <option value="2_63" >金融-贷款</option>
                        <option value="2_61" >金融-信用卡</option>
                        <option value="2_62" >金融-保险</option>
                        <?php }?>
						<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
						</select>
						<select name="product_type_1_<?php echo $num; ?>" id="product_type_1_<?php echo $num; ?>" style="display:none;margin:5px 0 0 81px;" onchange="changeSliderType(<?php echo $num; ?>,this.value);"><!--分类-->
						<!--<option value=-1>--------请选择--------</option>-->
						<?php
							$type_len = count($type_arr[-1]);
							for($i = 0;$i<$type_len;$i++){
								$typearr = explode("_",$type_arr[-1][$i]);
								$type_id = $typearr[0];
								$type_name = $typearr[1];

						?>
						<option value="<?php echo $type_id; ?>_1"><?php echo $type_name; ?></option>
						<?php
							$type_len2 = count($type_arr[$type_id]);
							for($ii = 0;$ii<$type_len2;$ii++){
								$typearr2 = explode("_",$type_arr[$type_id][$ii]);
								$type_id2 = $typearr2[0];
								$type_name2 = $typearr2[1];
						?>
						<option value="<?php echo $type_id2; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
						<?php
							$type_len3 = count($type_arr[$type_id2]);
							for($iii = 0;$iii<$type_len3;$iii++){
								$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
								$type_id3 = $typearr3[0];
								$type_name3 = $typearr3[1];
						?>
						<option value="<?php echo $type_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
						<?php
							$type_len4 = count($type_arr[$type_id3]);
							for($iiii = 0;$iiii<$type_len4;$iiii++){
								$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
								$type_id4 = $typearr4[0];
								$type_name4 = $typearr4[1];
						?>
						<option value="<?php echo $type_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
						<?php
							}
							}
							}
						}
						?>
						</select>
						</div>
						<div id="div_products_1_<?php echo $num; ?>" style="display:none;padding:5px 0 0 64px;">
							<select name="product_detail_id_1_<?php echo $num; ?>" id="product_detail_id_1_<?php echo $num; ?>">
								<option value=-1>---请选择一款产品---</option>
							</select>
					   </div>
					   <div id="package_product_id" style="display:none;padding:5px 0 0 64px">
					   	<select name="package_product_id" id="package_product_id">
					   		<option value=-1>---全部---</option>
					   		<?php foreach ($package_list as $key => $value) { ?>
					   		<option value='<?php echo $value['id'] ?>'><?php echo $value['package_name'] ?></option>
					   		<?php } ?>
					   	</select>
					   </div>

					   <input type="text" name="selector_title_<?php echo $num; ?>" id="selector_title" value="" disabled />
                        <button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
						<input type=hidden name="selector_id_<?php echo $num; ?>" id="selector_id" value="-1" />

					   <input type="radio" name="sel_link_type_<?php echo $num; ?>" id="sel_link_type_1_<?php echo $num; ?>_2" value="2" disabled /> <span>填写链接：</span>
						<input type="text" name="custom_link_1_<?php echo $num; ?>" id="custom_link" onchange="check_url(this)" value="" style="width:175px;" readonly />
					</div>
					<div class="clear"></div>
				</div>

				<?php }

					?>
				</div>
				<?php
					}
                  }
				 /**********4M  end**********/
				?>
					</div>
					<!--修改幻灯片功能操作代码结束-->
					</div>

					</form>
					</div>

				<script type="text/javascript" src="../../../Common/js/Base/personalization/jquery-cookie.js"></script>
				<div class="homerightbox" id="tab2">
					<!--模板分类广告图开始-->

					<script>
						$(document).ready(function(){
							var tabvalue=$.cookie('tabname');
							$("#tab"+tabvalue+"").css("display","block");
							$("#tab_"+tabvalue+" a").addClass("blueAA").parents("li").siblings("li").find("a").removeClass("blueAA");
							$("#tab"+tabvalue+"").css("display","block").siblings("div").css("display","none");;

						});
						function setCookie(tab){

							document.cookie="tabname="+tab;

						}

					</script>
					<?php if($template_id==31 || $template_id==38 || $template_id==41 || $template_id==43 || $template_id==45 || $template_id==46 || $template_id==49 || $template_id==56){?>
					<div style="overflow:hidden;border-bottom:solid 1px #ccc;margin:0 10px 0 10px">


					<?php
						$producttype_id=-1;
						$btn="添加分类";
						if(!empty($_GET["producttype_id"])){
						   $producttype_id=$configutil->splash_new($_GET["producttype_id"]);
						   $btn="保存修改";
						}

						$type_imgurl="";
						if($producttype_id>0 and empty($_GET["op"])){
						   //编辑属性的才读取数据，删除不需要读取数据

						   $query="select name,parent_id,sendstyle,index_imgurl from weixin_commonshop_types where isvalid=true and is_shelves=1 and  id=".$producttype_id;
						   $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
						   while ($row = mysql_fetch_object($result)) {
							   $producttype_name = $row->name;
							   $producttype_parent_id = $row->parent_id;
							   $producttype_sendstyle= $row->sendstyle;
							   $type_imgurl= $row->index_imgurl;
						   }
						}
					?>
						<div class="homeright_right rightA01"><!--左边分类显示开始-->
							<dl>
								<dt>添加分类楼层广告图片</dt>
								<?php
								   $query= "select id,name,parent_id,sendstyle,is_shelves,index_catnum,create_type,asort from weixin_commonshop_types where isvalid=true and is_shelves=1  and parent_id=-1 and customer_id=".$customer_id;
								   if($sort_str){
										$query =$query.' order by field(id'.$sort_str.')';
									}
								   $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
								   while ($row = mysql_fetch_object($result)) {
									   $pt_id = $row->id;
									   $pt_name = $row->name;
									   $pt_parent_id = $row->parent_id;
									   $pt_sendstyle= $row->sendstyle;
									   $pt_is_shelves= $row->is_shelves;
									   $create_type = $row->create_type;
									   $asort = $row->asort;
									   $index_catnum = $row->index_catnum;

								?>
								<dd cateid="<?php echo $pt_id; ?>" style="cursor: pointer;float:left">
									  <div class="category no_ext">
										<?php if((($owner_general==1 and $create_type==1) or ($owner_general==2 and $create_type==2)) or ($create_type==3)){ ?>
											<span style="width:100%;text-align:center;margin:0 auto;">
												<a class="fenlei_a" href="defaultset.php?customer_id=<?php echo $customer_id_en; ?>&producttype_id=<?php echo $pt_id;?>&default_set=1" title="<?php echo $pt_name; ?>" style="font-size:14px;"><i class="fa fa-gear"></i><?php echo $pt_name; ?></a>
										  </span>
										<?php } ?>
									 </div>
								   </dd>
								  <?php
								   $str = $u4m->getSubProductTypes($pt_id,$customer_id,1,$owner_general);
							   } ?>


								<!--<dd>
									<a href="#">测试名称多长还未知</a>

								</dd>
								-->
							</dl>
						</div><!--左边分类显示结束-->

						<!--右边图片上传开始-->
						<form id="frm_producttype" class="" action="save_producttype_img.php?customer_id=<?php echo $customer_id_en; ?>&adminuser_id=<?php echo $adminuser_id; ?>&owner_general=<?php echo $owner_general; ?>&orgin_adminuser_id=<?php echo $orgin_adminuser_id; ?>" method="post" enctype="multipart/form-data" style="margin:5px 10px;">
							<div class="homeright_left leftA01">
								<div class="WSY_microbox WSY_microslide">
									<p>上传：</p>
									<br/>
									<?php if($template_id==38 || $template_id==45){
										$query_catnum="select index_catnum from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id." and id=".$producttype_id."";
										$result_catnum=_mysql_query($query_catnum) or die ('catnym faild' .mysql_error());
										while($row=mysql_fetch_object($result_catnum)){
											 $index_catnum=$row->index_catnum;

										}
									?>
									<span style="margin-left:10px;font-size:14px;">分类楼层显示数量:<input type="text" name="index_catnum" id="index_catnum" style="width:30px;border:1px solid #999;height:18px;text-align:center;" value="<?php echo $index_catnum;?>"></span>
									<?php }?>

									<dl class="WSY_micro">
										<dt>
											<!--<img src="../../../Common/images/Base/home_decoration/contenticon/20140611053000_0.jpg" alt="">-->
											<iframe src="product_type_catimg.php?customer_id=<?php echo $customer_id_en; ?>&type_imgurl=<?php echo $type_imgurl; ?>&keyid=<?php echo $producttype_id; ?>" height=200 width=100% FRAMEBORDER=0 SCROLLING=no></iframe>

										</dt>
										<!--<dd style="display:block;text-align:center;padding-top:5px;">上传1张图片，作为首页楼层分类图片</dd>-->
										<!--<div class="uploader white">
											<input type="text" class="filename" readonly/>
											<input type="button" name="file" class="button" value="上传..."/>
											<input type="file" name="upfile" id="upfile" size="20"/>
										</div>
										-->
									</dl>
									<input type="hidden" id="keyid" name="keyid" value="<?php echo $producttype_id;?>">
									<input type=hidden name="type_imgurl" id="type_imgurl" value="<?php echo $type_imgurl ; ?>" />
									<input type="hidden" name="temp41" id="temp41" value="1" /><!--作为47模板的标识-->


									<!--========下面为添加图片链接======-->
									<?php

										$cat_url="select id,cat_foreign_id,cat_detail_id,cat_adurl,parent_id from weixin_commonshop_types where id=".$producttype_id."";
										$result_caturl=_mysql_query($cat_url) or die ('cat_url faild' .mysql_error());
										while($row=mysql_fetch_object($result_caturl)){
											$caturl_id=$row->id;
											$caturl_cat_foreign_id=$row->cat_foreign_id;
											$caturl_cat_detail_id=$row->cat_detail_id;
											$cat_adurl=$row->cat_adurl;
											$parent_id_cat=$row->parent_id;
										}
										$type_id_5_linktype = -1;
										$caturl_cat_foreign_id_arr = explode('_',$caturl_cat_foreign_id);
										$caturl_cat_foreign_id = $caturl_cat_foreign_id_arr[0];
										$type_id_5_linktype = $caturl_cat_foreign_id_arr[1];
									?>

									<div class="url_select1" style="">
										<div class="input">
											<?php
												if($caturl_cat_foreign_id<0 ){
													switch ($caturl_cat_foreign_id)
													{

														case -2:
														$opname="新品上市";
														break;
														case -3:
														$opname="热卖产品";
														break;
														case -4:
														$opname="购物车";
														break;
														case -6:
														$opname="全部产品";
														break;
														case -7:
														$opname="产品分类页1";
														break;
														case -8:
														$opname="个人中心";
														break;
														case -9:
														$opname="我的微店";
														break;
														case -5:
														$opname="限时抢购";
														break;
														case -33:
														$opname="区域批发商列表";
														break;
														case -10:
														$opname="商城在线客服";
														break;
														case -11:
														$opname="礼包列表";
														break;
														case -12:
														$opname="VP产品";
														break;
														case -15:
														$opname="兑换专区";
														break;
														case -16:
														$opname="首页";
														break;
														case -17:
														$opname="产品分类页2";
														break;
														case -37:
														$opname="产品分类页3";
														break;
														case -47:
														$opname="产品分类页4";
														break;
														case -18:
														$opname="我的订单";
														break;
														/*case -19:
														$opname="拼团列表";
														break;*/
														case -20:
														$opname="人气团列表";
														break;
														case -21:
														$opname="续费专区";
														break;
														case -22:
														$opname = "电商直播";
														break;
														case -23:
														$opname = '语音直播';
														break;
														case -24:
														$opname="票务特价机票";
														break;
														case -25:
														$opname = '票务特价火车票';
														break;
														case -32:
														$opname = '满赠专区';
														break;
														case -26:
														$opname = 'F2C系统';
														break;

														case -19:
														$opname = '拼团商品专区1';
														break;
														case -30:
														$opname = '拼团商品专区2';
														break;
														case -31:
														$opname = '拼团商品专区3';
														break;
                                                        case -34:
														$opname = '积分签到';
														break;
                                                        case -35:
														$opname = '积分兑换';
														break;
														case -36:
														$opname = '限时专区';
														break;
														case -361:
														$opname = '积分专区';
														break;
														case -1:
														$opname="--------请选择--------";
													}
												}
												elseif($caturl_cat_foreign_id>0){
													if($type_id_5_linktype == 1){
														$fenlei ="SELECT id,name,parent_id from weixin_commonshop_types where isvalid=true and id=".$caturl_cat_foreign_id." and customer_id=".$customer_id."";
														$result_fenlei=_mysql_query($fenlei) or die ('fenlei faild' .mysql_error());
														while($row=mysql_fetch_object($result_fenlei)){
															$fenlei_id=$row->id;
															$fenlei_name=$row->name;
														}
													}else if($type_id_5_linktype == 2){
														$tuwen ="SELECT id,title FROM weixin_subscribes where isvalid=true and id=".$caturl_cat_foreign_id." and customer_id=".$customer_id."";
														$result_tuwen=_mysql_query($tuwen) or die ('tuwen faild' .mysql_error());

														while($row=mysql_fetch_object($result_tuwen)){
															$tuwen_id=$row->id;
															$tuwen_title=$row->title;
														}
													}
													/*$tuwen ="SELECT id,title FROM weixin_subscribes where isvalid=true and id=".$caturl_cat_foreign_id." and customer_id=".$customer_id."";
													$result_tuwen=_mysql_query($tuwen) or die ('tuwen faild' .mysql_error());

													while($row=mysql_fetch_object($result_tuwen)){
														$tuwen_id=$row->id;
														$tuwen_title=$row->title;
														}

													if(!$tuwen_id){
															$fenlei ="SELECT id,name,parent_id from weixin_commonshop_types where isvalid=true and id=".$caturl_cat_foreign_id." and customer_id=".$customer_id."";
															$result_fenlei=_mysql_query($fenlei) or die ('fenlei faild' .mysql_error());
															while($row=mysql_fetch_object($result_fenlei)){
																$fenlei_id=$row->id;
																$fenlei_name=$row->name;


															}

														}*/


													}

											?>

											<select  name="type_id_5" class="home_select" id="type_id_5" onchange="change_type(this);"><?php echo $opname;?>

												<?php if($caturl_cat_foreign_id<0){?><option value="<?php echo $caturl_cat_foreign_id; ?>" selected="selected"><?php echo $opname;?></option>
												<?php }elseif($type_id_5_linktype == 1){?>
												 <option value="-40" selected="selected">多级分类</option>
												<?php }elseif($tuwen_id){?>
												<option value="<?php echo $tuwen_id; ?>_2" selected="selected"><?php echo $tuwen_title; ?></option>
												<?php }?>



											<!--<option value="-1" selected="selected">--------请选择--------</option>-->
											<option value="-16" >首页</option>
											<option value="-6" >全部产品</option>
											<option value="-2" >新品上市</option>
											<option value="-3" >热卖产品</option>
											<option value="-4" >购物车</option>
											<option value="-8" >个人中心</option>
											<option value="-18" >我的订单</option>
											<option value="-7" >产品分类页</option>
											<option value="-17" >产品分类页2</option>
											<option value="-37" >产品分类页3</option>
											<option value="-47" >产品分类页4</option>
											<option value="-9" >我的微店</option>
											<option value="-5" >限时抢购</option>
											<option value='-33' >区域批发商列表</option>
											<option value="-10" >商城在线客服</option>
											<option value="-11" >礼包列表</option>
											<option value="-12" >VP产品</option>
											<option value="-15" >兑换专区</option>
											<!--option value="-19" >拼团列表</option-->
											<option value="-20" >人气团列表</option>
											<option value="-21" >续费专区</option>、
											<option value="-22" >电商直播</option>
											<option value="-23" >语音直播</option>
											<?php if($is_ticket){?>
											<option value="-24" >票务特价机票</option>
											<option value="-25" >票务特价火车票</option>
											<?php }?>
											<?php if($is_exchange){?>
											<option value="-32" >满赠专区</option>
											<?php }?>
											<option value="-95" >砍价活动</option>
											<option value="-96" >众筹新版</option>
											<?php if($is_f2c>0){ ?>
											<option value="-26" >F2C系统</option>
											<?php } ?>
                                            <option value="-27" >订货系统登录</option>
                                            <option value="-28" >订货系统申请</option>
                                            <option value="-29" >订货系统中心</option>
                                            <option value="-19" >拼团商品专区1</option>
					                        <option value="-30" >拼团商品专区2</option>
					                        <option value="-31" >拼团商品专区3</option>
                                            <option value="-34" >积分签到</option>
                                            <option value="-35" >积分兑换</option>
											<option value="-36" >限时专区</option>
											<option value="-361" >积分专区</option>

											<?php
												if( !empty($diy_template_link) ){
											?>
											<optgroup label="---------------自定义模板---------------"></optgroup>
											<?php

												for( $i = 0; $i < $diy_template_count; $i++ ){
													$diy_template_arr = explode('_',$diy_template_link[$i]);
													$diy_template_id = $diy_template_arr[0];
													$diy_template_name = $diy_template_arr[1];
											?>
											<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
											<?php }}?>
											<optgroup label="---------------产品分类---------------"></optgroup>
											<!--<?php
											  if($typesize>0){
												 for($i=0;$i<$typesize; $i++){
													$typestr= $typeLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];


											?>
											  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
											<?php  }

											} ?>-->
											<option value="-40">多级分类</option>
											<optgroup label="---------------图文消息---------------"></optgroup>
											<?php
											  if($imginfosize>0){
												 for($i=0;$i<$imginfosize; $i++){
													$typestr= $imginfoLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];


											?>

											  <option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
											<?php  }

											} ?>

											<?php if($is_cityarea_caterer){?>
											<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
											<?php if($cityareaCaterersize>0){
													for($i=0;$i<$cityareaCaterersize; $i++){
													$typestr= $cityareaCatererLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];
											?>
											<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
											<?php  }

											}} ?>

											<?php if($is_cityarea_ktv){?>
											<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
											<?php if($cityareaKTVsize>0){
													for($i=0;$i<$cityareaKTVsize; $i++){
													$typestr= $cityareaKTVLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];
											?>
											<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
											<?php  }

											}} ?>

											<?php if($is_cityarea_hotel){?>
											<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
											<?php if($cityareaHotelsize>0){
													for($i=0;$i<$cityareaHotelsize; $i++){
													$typestr= $cityareaHotelLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];
											?>
											<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
											<?php  }

											}} ?>

											<?php if($is_cityarea_shop){?>
											<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
											<?php if($cityareaShopsize>0){
													for($i=0;$i<$cityareaShopsize; $i++){
													$typestr= $cityareaShopLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];
											?>
											<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
											<?php  }

											}} ?>

											<?php if($is_cityarea){?>
											<optgroup label="-----------商圈行业列表-----------"></optgroup>
											<?php }
											if($is_cityarea_caterer){?>
											<option value="2_50" >美食</option>
											<?php }
											if($is_cityarea_ktv){?>
											<option value="2_51" >KTV</option>
											<?php }
											if($is_cityarea_hotel){?>
											<option value="2_52" >酒店</option>
											<?php }
											if($is_cityarea_shop){?>
											<option value="2_53" >线下商城-首页</option>
                                            <option value="2_54" >线下商城-商家列表</option>
											<?php }
											?>
											<?php if($is_cityarea_finance){?>
					                        <option value="2_63" >金融-贷款</option>
					                        <option value="2_61" >金融-信用卡</option>
					                        <option value="2_62" >金融-保险</option>
					                        <?php }?>
											<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
											</select>
											<select name="product_type_5" id="product_type_5" style="<?php if($type_id_5_linktype==1 && $caturl_cat_foreign_id>0){?>display:block;<?php }else{?>display:none;<?php }?>margin:5px 0 0 10px;width: 240px;" onchange="changeProductType_cat(this.value);"><!--分类-->
											<!--<option value=-1>--------请选择--------</option>-->
											<?php
												$type_len = count($type_arr[-1]);
												for($i = 0;$i<$type_len;$i++){
													$typearr = explode("_",$type_arr[-1][$i]);
													$type_id = $typearr[0];
													$type_name = $typearr[1];

											?>
											<option value="<?php echo $type_id; ?>_1" <?php if($type_id_5_linktype==1 && $caturl_cat_foreign_id==$type_id){?>selected="selected"<?php }?>><?php echo $type_name; ?></option>
											<?php
												$type_len2 = count($type_arr[$type_id]);
												for($ii = 0;$ii<$type_len2;$ii++){
													$typearr2 = explode("_",$type_arr[$type_id][$ii]);
													$type_id2 = $typearr2[0];
													$type_name2 = $typearr2[1];
											?>
											<option value="<?php echo $type_id2; ?>_1" <?php if($type_id_5_linktype==1 && $caturl_cat_foreign_id==$type_id2){?>selected="selected"<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
											<?php
												$type_len3 = count($type_arr[$type_id2]);
												for($iii = 0;$iii<$type_len3;$iii++){
													$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
													$type_id3 = $typearr3[0];
													$type_name3 = $typearr3[1];
											?>
											<option value="<?php echo $type_id3; ?>_1" <?php if($type_id_5_linktype==1 && $caturl_cat_foreign_id==$type_id3){?>selected="selected"<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
											<?php
												$type_len4 = count($type_arr[$type_id3]);
												for($iiii = 0;$iiii<$type_len4;$iiii++){
													$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
													$type_id4 = $typearr4[0];
													$type_name4 = $typearr4[1];
											?>
											<option value="<?php echo $type_id4; ?>_1" <?php if($type_id_5_linktype==1 && $caturl_cat_foreign_id==$type_id4){?>selected="selected"<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
											<?php
												}
												}
												}
											}
											?>
											</select>
											<div id="div_products_cat" style="<?php if(!$protypeid){echo "display:none;";}?> margin-top:20px;">

												<select name="product_detail_id_cat" id="product_detail_id_cat" class="home_select">
													<option value=-1>---请选择一款产品---</option>
												</select>
											</div>
											<div id="package_product_id" style="display:none;margin-top:20px;">
												<select name="package_product_id" id="package_product_id">
													<option value=-1>---全部---</option>
													<?php foreach ($package_list as $key => $value) { ?>
													<option value='<?php echo $value['id'] ?>'><?php echo $value['package_name'] ?></option>
													<?php } ?>
												</select>
											</div>

										</div>

									</div>
									<div class="input_butn input_butn01"><input type="submit" value="提交"></div>
								</div>
							</div>
						</form>
						<!--右边图片上传结束-->

					</div>
					<?php }?>

				<!--模板分类广告图结束-->

				<!--模板分类首页显示图开始-->

				<?php if($template_id==41 || $template_id==47 || $template_id==48 || $template_id==50 || $template_id==55){?>  <!--41模板暂定-->
					<div style="overflow:hidden;margin:10px 10px 0 10px">


						<?php
							$producttype_id=-1;
							$btn="添加分类";
							if(!empty($_GET["producttype_id"])){
							   $producttype_id=$configutil->splash_new($_GET["producttype_id"]);
							   $btn="保存修改";
							}

							$type_imgurl="";
							if($producttype_id>0 and empty($_GET["op"])){
							   //编辑属性的才读取数据，删除不需要读取数据

							   $query="select name,parent_id,sendstyle,cat_index_imgurl  from weixin_commonshop_types where isvalid=true and is_shelves=1 and  id=".$producttype_id;
							   $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
							   while ($row = mysql_fetch_object($result)) {
								   $producttype_name = $row->name;
								   $producttype_parent_id = $row->parent_id;
								   $producttype_sendstyle= $row->sendstyle;
								   $cat_index_imgurl= $row->cat_index_imgurl;
							   }
							}
						?>
						<!--左边分类显示开始-->
						<div class="homeright_right rightA01">
							<dl>
								<dt>分类首页显示图</dt>
								<?php
								   $query= "select id,name,parent_id,sendstyle,is_shelves,index_catnum,create_type,asort from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=-1 and customer_id=".$customer_id;
								   if($sort_str){
										$query =$query.' order by field(id'.$sort_str.')';
									}
								   $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
								   while ($row = mysql_fetch_object($result)) {
									   $pt_id = $row->id;
									   $pt_name = $row->name;
									   $pt_parent_id = $row->parent_id;
									   $pt_sendstyle= $row->sendstyle;
									   $pt_is_shelves= $row->is_shelves;
									   $create_type = $row->create_type;
									   $asort = $row->asort;
								?>
									<dd class="flei" style="float:left;">
										<?php if($pt_id){?>
											<em>
												<?php if((($owner_general==1 and $create_type==1) or ($owner_general==2 and $create_type==2)) or ($create_type==3)){ ?>
													<a class="fenlei_a" href="defaultset.php?customer_id=<?php echo $customer_id_en; ?>&producttype_id=<?php echo $pt_id;?>&default_set=1&cat_op=catad" title="<?php echo $pt_name; ?>"><i class="fa fa-gear"></i><?php echo $pt_name; ?></a><!--一级分类-->
												<?php } ?>
												<?php
													$query_son= "select id,name,parent_id,sendstyle,is_shelves,index_catnum,create_type,asort from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=".$pt_id." and customer_id=".$customer_id." order by id asc" ;


											   $result_son = _mysql_query($query_son) or die('Query failed: ' . mysql_error());
											   while ($row = mysql_fetch_object($result_son)) {
												   $son_id = $row->id;
												   $son_name = $row->name;
												   $son_parent_id = $row->parent_id;
												   $son_sendstyle= $row->sendstyle;
												   $son_is_shelves= $row->is_shelves;
												   $create_type = $row->create_type;
												   $asort = $row->asort;
												 ?>
													<?php if((($owner_general==1 and $create_type==1) or ($owner_general==2 and $create_type==2)) or ($create_type==3)){ ?>
														<a class="fenlei_a soncat" href="defaultset.php?customer_id=<?php echo $customer_id_en; ?>&producttype_id=<?php echo $son_id;?>&default_set=1&cat_op=catindex" title="<?php echo $son_name; ?>" style="background:none;color:#000;height:10px;line-height:10px;">
															<span style="padding-left:0px;"><?php echo $son_name; ?></span><!--二级分类-->
														</a>
													<?php } ?>
												<?php }?>
											</em>
										<?php }?>
									</dd>
								<?php
								   $str = $u4m->getSubProductTypes($pt_id,$customer_id,1,$owner_general);
							   } ?>


							</dl>
						</div>
						<!--左边分类显示结束-->

						<!--右边图片上传开始-->
						<form id="frm_producttype" class="" action="save_producttype_img.php?customer_id=<?php echo $customer_id_en; ?>&adminuser_id=<?php echo $adminuser_id; ?>&owner_general=<?php echo $owner_general; ?>&orgin_adminuser_id=<?php echo $orgin_adminuser_id; ?>" method="post" enctype="multipart/form-data" style="margin:5px 10px;">
							<div class="homeright_left leftA01">
								<div class="WSY_microbox WSY_microslide">
									<p>上传：</p>

									<dl class="WSY_micro">
										<dt>
											<iframe src="product_cat_indeximg.php?customer_id=<?php echo $customer_id_en; ?>&type_imgurl=<?php echo $cat_index_imgurl; ?>&keyid=<?php echo $producttype_id; ?>" height=200 width=100% FRAMEBORDER=0 SCROLLING=no></iframe>
										</dt>

									</dl>
									<input type="hidden" id="keyid" name="keyid" value="<?php echo $producttype_id;?>">
									<input type=hidden name="type_imgurl" id="type_imgurl_cat" value="<?php echo $cat_index_imgurl ; ?>" />
									<input type="hidden" name="temp48" id="temp48" value="1" /><!--作为47模板的标识-->


									<!--========下面为添加图片链接======-->
									<?php

										$cat_url="select id,cat_foreign_id,cat_detail_id,cat_adurl from weixin_commonshop_types where id=".$producttype_id."";
										$result_caturl=_mysql_query($cat_url) or die ('cat_url faild' .mysql_error());
										while($row=mysql_fetch_object($result_caturl)){
											$caturl_id=$row->id;
											$caturl_cat_foreign_id=$row->cat_foreign_id;
											$caturl_cat_detail_id=$row->cat_detail_id;
											$cat_adurl=$row->cat_adurl;
										}
										$type_id_5_index_linktype = -1;
										$caturl_cat_foreign_id_arr = explode('_',$caturl_cat_foreign_id);
										$caturl_cat_foreign_id = $caturl_cat_foreign_id_arr[0];
										$type_id_5_index_linktype = $caturl_cat_foreign_id_arr[1];
									?>

									<div class="url_select1" style="<?php if($template_id==41){echo 'display:none;';}?>">
										<div class="input">
											<?php
												if($caturl_cat_foreign_id<0){
													switch ($caturl_cat_foreign_id)
													{

														case -2:
														$opname="新品上市";
														break;
														case -3:
														$opname="热卖产品";
														break;
														case -4:
														$opname="购物车";
														break;
														case -6:
														$opname="全部产品";
														break;
														case -7:
														$opname="产品分类页1";
														break;
														case -8:
														$opname="个人中心";
														break;
														case -9:
														$opname="我的微店";
														break;
														case -5:
														$opname="限时抢购";
														break;
														case -33:
														$opname="区域批发商列表";
														break;
														case -10:
														$opname="商城在线客服";
														break;
														case -11:
														$opname="礼包列表";
														break;
														case -12:
														$opname="VP产品";
														break;
														case -15:
														$opname="兑换专区";
														break;
														case -16:
														$opname="首页";
														break;
														case -17:
														$opname="产品分类页2";
														break;
														case -37:
														$opname="产品分类页3";
														break;
														case -47:
														$opname="产品分类页4";
														break;
														case -18:
														$opname="我的订单";
														break;
														/*case -19:
														$opname="拼团列表";
														break;*/
														case -20:
														$opname="人气团列表";
														break;
														case -21:
														$opname="续费专区";
														break;
														case -22:
														$opname = "电商直播";
														break;
														case -23:
														$opname = '语音直播';
														break;
														case -19:
														$opname = '拼团商品专区1';
														break;
														case -30:
														$opname = '拼团商品专区2';
														break;
														case -31:
														$opname = '拼团商品专区3';
														break;
                                                        case -34:
														$opname = '积分签到';
														break;
                                                        case -35:
														$opname = '积分兑换';
														break;
														case -36:
														$opname = '限时专区';
														break;
														case -361:
														$opname = '积分专区';
														break;
														default:
														$opname="--------请选择--------";
													}
												}
												elseif($caturl_cat_foreign_id>0 ){
													if($type_id_5_index_linktype == 1){
														$fenlei ="SELECT id,name,parent_id from weixin_commonshop_types where isvalid=true and id=".$caturl_cat_foreign_id." and customer_id=".$customer_id."";
														$result_fenlei=_mysql_query($fenlei) or die ('fenlei faild' .mysql_error());
														while($row=mysql_fetch_object($result_fenlei)){
															$fenlei_id=$row->id;
															$fenlei_name=$row->name;
														}
													}else if($type_id_5_index_linktype == 2){
														$tuwen ="SELECT id,title FROM weixin_subscribes where isvalid=true and id=".$caturl_cat_foreign_id." and customer_id=".$customer_id."";
														$result_tuwen=_mysql_query($tuwen) or die ('tuwen faild' .mysql_error());

														while($row=mysql_fetch_object($result_tuwen)){
															$tuwen_id=$row->id;
															$tuwen_title=$row->title;
														}
													}
													/*$tuwen ="SELECT id,title FROM weixin_subscribes where isvalid=true and id=".$caturl_cat_foreign_id." and customer_id=".$customer_id."";
													$result_tuwen=_mysql_query($tuwen) or die ('tuwen faild' .mysql_error());

													while($row=mysql_fetch_object($result_tuwen)){
														$tuwen_id=$row->id;
														$tuwen_title=$row->title;
														}

													if(!$tuwen_id){
															$fenlei ="SELECT id,name from weixin_commonshop_types where isvalid=true and id=".$caturl_cat_foreign_id." and customer_id=".$customer_id."";
															$result_fenlei=_mysql_query($fenlei) or die ('fenlei faild' .mysql_error());
															while($row=mysql_fetch_object($result_fenlei)){
																$fenlei_id=$row->id;
																$fenlei_name=$row->name;

															}

														}*/


													}

											?>

											<select  name="type_id_5_index" class="home_select" id="type_id_5_index" onchange="change_type(this);"><?php echo $opname;?>
											<?php if($caturl_cat_foreign_id<0){?><option value="<?php echo $caturl_cat_foreign_id; ?>" selected="selected"><?php echo $opname;?></option>
											<?php }elseif($type_id_5_index_linktype == 1){?>
											<option value="-40" selected="selected">多级分类</option>
											<?php }elseif($tuwen_id){?>
											<option value="<?php echo $tuwen_id; ?>_2" selected="selected"><?php echo $tuwen_title; ?></option>
											<?php }?>



											<!--<option value="-1" selected="selected">--------请选择--------</option>-->
											<!--<option value="-1" selected="selected">--------请选择--------</option>-->
											<option value="-16" >首页</option>
											<option value="-6" >全部产品</option>
											<option value="-2" >新品上市</option>
											<option value="-3" >热卖产品</option>
											<option value="-4" >购物车</option>
											<option value="-8" >个人中心</option>
											<option value="-18" >我的订单</option>
											<option value="-7" >产品分类页1</option>
											<option value="-17" >产品分类页2</option>
											<option value="-37" >产品分类页3</option>
											<option value="-47" >产品分类页4</option>
											<option value="-9" >我的微店</option>
											<option value="-5" >限时抢购</option>
											<option value='-33' >区域批发商列表</option>
											<option value="-10" >商城在线客服</option>
											<option value="-11" >礼包列表</option>
											<option value="-12" >VP产品</option>
											<option value="-15" >兑换专区</option>
											<!--option value="-19" >拼团列表</option-->
											<option value="-20" >人气团列表</option>
											<option value="-21" >续费专区</option>
											<option value="-22" >电商直播</option>
											<option value="-23" >语音直播</option>
											<?php if($is_ticket){?>
											<option value="-24" >票务特价机票</option>
											<option value="-25" >票务特价火车票</option>
											<?php }?>
											<?php if($is_exchange){?>
											<option value="-32" >满赠专区</option>
											<?php }?>
											<option value="-95" >砍价活动</option>
											<option value="-96" >众筹新版</option>
											<?php if($is_f2c>0){ ?>
											<option value="-26" >F2C系统</option>
											<?php } ?>
                                            <option value="-27" >订货系统登录</option>
                                            <option value="-28" >订货系统申请</option>
                                            <option value="-29" >订货系统中心</option>
                                            <option value="-19" >拼团商品专区1</option>
					                        <option value="-30" >拼团商品专区2</option>
					                        <option value="-31" >拼团商品专区3</option>
                                            <option value="-34" >积分签到</option>
                                            <option value="-35" >积分兑换</option>
											<option value="-36" >限时专区</option>
											<option value="-361" >积分专区</option>

											<?php
												if( !empty($diy_template_link) ){
											?>
											<optgroup label="---------------自定义模板---------------"></optgroup>
											<?php

												for( $i = 0; $i < $diy_template_count; $i++ ){
													$diy_template_arr = explode('_',$diy_template_link[$i]);
													$diy_template_id = $diy_template_arr[0];
													$diy_template_name = $diy_template_arr[1];
											?>
											<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
											<?php }}?>
											<optgroup label="---------------产品分类---------------"></optgroup>
											<!--<?php
											  if($typesize>0){
												 for($i=0;$i<$typesize; $i++){
													$typestr= $typeLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];


											?>
											  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
											<?php  }

											} ?>-->
											<option value="-40">多级分类</option>
											<optgroup label="---------------图文消息---------------"></optgroup>
											<?php
											  if($imginfosize>0){
												 for($i=0;$i<$imginfosize; $i++){
													$typestr= $imginfoLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];


											?>

											<option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
											<?php  }

											} ?>

											<?php if($is_cityarea_caterer){?>
											<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
											<?php if($cityareaCaterersize>0){
													for($i=0;$i<$cityareaCaterersize; $i++){
													$typestr= $cityareaCatererLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];
											?>
											<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
											<?php  }

											}} ?>

											<?php if($is_cityarea_ktv){?>
											<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
											<?php if($cityareaKTVsize>0){
													for($i=0;$i<$cityareaKTVsize; $i++){
													$typestr= $cityareaKTVLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];
											?>
											<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
											<?php  }

											}} ?>

											<?php if($is_cityarea_hotel){?>
											<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
											<?php if($cityareaHotelsize>0){
													for($i=0;$i<$cityareaHotelsize; $i++){
													$typestr= $cityareaHotelLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];
											?>
											<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
											<?php  }

											}} ?>

											<?php if($is_cityarea_shop){?>
											<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
											<?php if($cityareaShopsize>0){
													for($i=0;$i<$cityareaShopsize; $i++){
													$typestr= $cityareaShopLst->Get($i);

													$typearr = explode("_",$typestr);
													$type_id = $typearr[0];
													$type_name = $typearr[1];
											?>
											<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
											<?php  }

											}} ?>

											<?php if($is_cityarea){?>
											<optgroup label="-----------商圈行业列表-----------"></optgroup>
											<?php }
											if($is_cityarea_caterer){?>
											<option value="2_50" >美食</option>
											<?php }
											if($is_cityarea_ktv){?>
											<option value="2_51" >KTV</option>
											<?php }
											if($is_cityarea_hotel){?>
											<option value="2_52" >酒店</option>
											<?php }
											if($is_cityarea_shop){?>
											<option value="2_53" >线下商城-首页</option>
                                            <option value="2_54" >线下商城-商家列表</option>
											<?php }
											?>
											<?php if($is_cityarea_finance){?>
					                        <option value="2_63" >金融-贷款</option>
					                        <option value="2_61" >金融-信用卡</option>
					                        <option value="2_62" >金融-保险</option>
					                        <?php }?>
											<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
											</select>
											<select name="product_type_5_index" id="product_type_5_index" style="<?php if($type_id_5_index_linktype==1 && $caturl_cat_foreign_id>0){?>display:block;<?php }else{?>display:none;<?php }?>margin:5px 0 0 10px;width: 240px;" onchange="changeProductType_cat_index(this.value);"><!--分类-->
											<!--<option value=-1>--------请选择--------</option>-->
											<?php
												$type_len = count($type_arr[-1]);
												for($i = 0;$i<$type_len;$i++){
													$typearr = explode("_",$type_arr[-1][$i]);
													$type_id = $typearr[0];
													$type_name = $typearr[1];

											?>
											<option value="<?php echo $type_id; ?>_1" <?php if($type_id_5_index_linktype==1 && $caturl_cat_foreign_id==$type_id){?>selected="selected"<?php }?>><?php echo $type_name; ?></option>
											<?php
												$type_len2 = count($type_arr[$type_id]);
												for($ii = 0;$ii<$type_len2;$ii++){
													$typearr2 = explode("_",$type_arr[$type_id][$ii]);
													$type_id2 = $typearr2[0];
													$type_name2 = $typearr2[1];
											?>
											<option value="<?php echo $type_id2; ?>_1" <?php if($type_id_5_index_linktype==1 && $caturl_cat_foreign_id==$type_id2){?>selected="selected"<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
											<?php
												$type_len3 = count($type_arr[$type_id2]);
												for($iii = 0;$iii<$type_len3;$iii++){
													$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
													$type_id3 = $typearr3[0];
													$type_name3 = $typearr3[1];
											?>
											<option value="<?php echo $type_id3; ?>_1" <?php if($type_id_5_index_linktype==1 && $caturl_cat_foreign_id==$type_id3){?>selected="selected"<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
											<?php
												$type_len4 = count($type_arr[$type_id3]);
												for($iiii = 0;$iiii<$type_len4;$iiii++){
													$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
													$type_id4 = $typearr4[0];
													$type_name4 = $typearr4[1];
											?>
											<option value="<?php echo $type_id4; ?>_1" <?php if($type_id_5_index_linktype==1 && $caturl_cat_foreign_id==$type_id4){?>selected="selected"<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
											<?php
												}
												}
												}
											}
											?>
											</select>
											<div id="div_products_cat_index" style="<?php if(!$protypeid){echo "display:none;";}?> margin-top:20px;">

												<select name="product_detail_id_cat_index" id="product_detail_id_cat_index" class="home_select">
													<option value=-1>---请选择一款产品---</option>
												</select>
											</div>
											<div id="package_product_id" style="display:none;margin-top:20px;">
												<select name="package_product_id" id="package_product_id">
													<option value=-1>---全部---</option>
													<?php foreach ($package_list as $key => $value) { ?>
													<option value='<?php echo $value['id'] ?>'><?php echo $value['package_name'] ?></option>
													<?php } ?>
												</select>
											</div>

											</div>
									</div>
									<div class="input_butn input_butn01"><input type="submit" value="提交"></div>
								</div>
							</div>
						</form>
						<!--右边图片上传结束-->


					<!--模板分类首页显示图结束-->

				</div>
				<?php }?>

				<?php if($template_id!=31 && $template_id!=38 && $template_id!=41 && $template_id!=43 && $template_id!=45 && $template_id!=46 && $template_id!=47 && $template_id!=48 && $template_id!=49 && $template_id!=51 && $template_id!=55 && $template_id!=56){?>
					<div style="width:100%;">
						<span style="text-align:center;font-size:18px;color:#999;display:block;">此模板不需要编辑分类首页显示图</span>
					</div>
				<?php }?>
				</div> <!--TAB2结束-->
				<!--WSY_homeright end-->
				<div class="homerightbox" id="tab3">
				<!--底部菜单已换成后台自定义添加，这里先屏蔽掉，不能编辑-->
				<?php //if($template_id==31 || $template_id==45 || $template_id==49){?>
				<?php if(false){?>
					<?php
						$key_id="";
						if(!empty($_GET["key_id"])){
							$key_id=$configutil->splash_new($_GET["key_id"]);
							$result=_mysql_query("select name,chk_submenu,navigation_id,subpros,sublinks_id from weixin_commonshop_userdefined_nav where isvalid=true and id=".$key_id) or die ("Query failed_key_id:".$mysql_error());
							while($row=mysql_fetch_object($result)){
								$pre_name=$row->name;
								$pre_chk_submenu=$row->chk_submenu;
								$pre_navigation_id=$row->navigation_id;
								$pre_subpros=$row->subpros;
								$pre_sublinks=$row->sublinks_id;
							}

						}

					?>

					<div class="homeright_right rightA01 rightA02">
						<?php
							$query="select id,name,chk_submenu,subpros from weixin_commonshop_userdefined_nav where isvalid=true and customer_id=".$customer_id;
							$chk_submenu=0;
							$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
							while ($row = mysql_fetch_object($result)) {
								$nav_id = $row->id;
								$name = $row->name;
								$chk_submenu = $row->chk_submenu;
								$subpros=$row->subpros;
						?>

						<dl class="rightA01_dl">
							<dt>
								<?php if($name!=""){ ?>
									<a href="defaultset.php?customer_id=<?php echo $customer_id_en; ?>&template_id=<?php echo $template_id; ?>&key_id=<?php echo $nav_id;?>" title="修改">
									<img src="../../../Common/images/Base/home_decoration/operating_icon/icon05.png" alt="修改">
									</a>
									<a href="save_navigation.php?customer_id=<?php echo $customer_id_en; ?>&op=del&id=<?php echo $nav_id; ?>&template_id=<?php echo $template_id;?>" title="删除" onclick="if(!confirm(&#39;删除后不可恢复，继续吗？&#39;)){return false};">
									<img src="../../../Common/images/Base/home_decoration/operating_icon/icon04.png" alt="删除">
									</a>
								<?php } ?>
								<em>导航名：<?php echo $name; ?></em>
							</dt>
							<dd>
								<?php
									if($chk_submenu==1){
										$submenu=explode("#",$subpros);
										$count_sub=count($submenu);
										for($i=0;$i<$count_sub;$i++){
								?>
								<em><b href="#">子菜单名：<?php echo $submenu[$i]; ?></b></em>
								<?php }}?>
							</dd>
						</dl>
						<?php } ?>
					</div>
					<style>
						.navigation_left{float:left;background:#f7f7f7;width: 345px;border: 1px solid #ddd;min-height: 200px;}
						.navigation_left #frm_pro h1{text-align:center;}
						.navigation_left #frm_pro .opt_item label{float:left;width:84px;height:28px;line-height:28px;text-align:right;}
						.navigation_left #frm_pro .opt_item .input{float: left;width: 220px;display: block;line-height: 28px;}
						.navigation_left #frm_pro .opt_item .input img{cursor:pointer;vertical-align:middle;}
						.navigation_right{float:left;background:#f7f7f7;width: 345px;border: 1px solid #ddd;min-height: 200px;margin-left:6px;padding:10px;}
						.navigation_right dl dd{border-bottom:1px solid #ddd;background:#f7f7f7;}
						.navigation_right ul li{width:50%;float:left;}
					</style>
					<div class="homeright_left leftA01 navigation_left">
						<form id="frm_pro" name="frm_pro" method="post" action="save_navigation.php?customer_id=<?php echo $customer_id_en; ?>&template_id=<?php echo $template_id;?>">
							<dl class="leftA01_dl">
								<dt>添加自定义底部导航栏</dt>
								<dd>
									<em>导航栏名称：</em>
									<input type="text" name="name" id="name" value="<?php echo $pre_name; ?>" class="form_input" size="15" maxlength="30" notnull="">
								</dd>
								<dd>
									<em>是否有子菜单：</em>
									<i><input type="radio" name="is_submenu" class="is_submenu" value="0" <?php if($pre_chk_submenu==0){?>checked="checked" <?php } ?>>否</i>
									<i><input type="radio" name="is_submenu" value="1" <?php if($pre_chk_submenu==1){?>checked="checked" <?php } ?> class="is_submenu">是</i>
									<input type="hidden" id="chk_submenu" name="chk_submenu" value="<?php echo $pre_chk_submenu;?>">
								</dd>
								<dd>
									<?php if($key_id>0){?>
										<div class="opt_item navigation_link" <?php if($pre_chk_submenu==0){ ?> style="display:block;" <?php }else{ ?> style="display:none;"<?php } ?> >
											<em style="float:left;">导航栏链接：</em>
												<div class="input" style="width:167px;float:left;">
													<select  name="navigation_id"  id="navigation_id" onchange="change_type(this)">
														<option value="-1" <?php if($pre_navigation_id==-1){ ?> selected="selected" <?php } ?> >--------请选择--------</option>
														<option value="-6" <?php if($pre_navigation_id==-6){ ?> selected="selected" <?php } ?> >全部产品</option>
														<option value="-2" <?php if($pre_navigation_id==-2){ ?> selected="selected" <?php } ?> >新品上市</option>
														<option value="-3" <?php if($pre_navigation_id==-3){ ?> selected="selected" <?php } ?> >热卖产品</option>
														<option value="-4" <?php if($pre_navigation_id==-4){ ?> selected="selected" <?php } ?> >购物车</option>
														<option value="-8" <?php if($pre_navigation_id==-8){ ?> selected="selected" <?php } ?> >个人中心</option>
														<option value="-18" <?php if($pre_navigation_id==-18){ ?> selected="selected" <?php } ?>>我的订单</option>
														<option value="-7" <?php if($pre_navigation_id==-7){ ?> selected="selected" <?php } ?> >产品分类页</option>
														<option value="-9" <?php if($pre_navigation_id==-9){ ?> selected="selected" <?php } ?> >我的微店</option>
														<option value="-5" <?php if($pre_navigation_id==-5){ ?> selected="selected" <?php } ?> >限时抢购</option>
														<option value="-33" <?php if($pre_navigation_id==-33){ ?> selected="selected" <?php } ?> >区域批发商列表</option>
														<option value="-10" <?php if($pre_navigation_id==-10){ ?> selected="selected" <?php } ?> >商城在线客服</option>
														<option value="-11" <?php if($pre_navigation_id==-11){ ?> selected="selected" <?php } ?> >礼包列表</option>
														<option value="-12" <?php if($pre_navigation_id==-12){ ?> selected="selected" <?php } ?> >VP产品</option>
														<!--option value="-19"  selected="selected"  >拼团列表</option-->
														<option value="-20" <?php if($pre_navigation_id==-20){ ?> selected="selected" <?php } ?> >人气团列表</option>
														<option value="-21" <?php if($pre_navigation_id==-21){ ?> selected="selected" <?php } ?> >续费专区</option>
														<option value="-22" <?php if($pre_navigation_id==-22){ ?> selected="selected" <?php } ?> >电商直播</option>
														<option value="-23" <?php if($pre_navigation_id==-23){ ?> selected="selected" <?php } ?> >语音直播</option>
														<?php if($is_ticket){?>
														<option value="-24" <?php if($pre_navigation_id==-24){ ?> selected="selected" <?php } ?> >票务特价机票</option>
														<option value="-25" <?php if($pre_navigation_id==-25){ ?> selected="selected" <?php } ?> >票务特价火车票</option>
														<?php }?>
														<?php if($is_exchange){?>
														<option value="-32" <?php if($pre_navigation_id==-32){ ?> selected="selected" <?php } ?> >满赠专区</option>
														<?php }?>
														<option value="-95" <?php if($pre_navigation_id==-95){ ?> selected="selected" <?php } ?> >砍价活动</option>
														<option value="-96" <?php if($pre_navigation_id==-96){ ?> selected="selected" <?php } ?> >众筹新版</option>
														<?php if($is_f2c>0){ ?>
														<option value="-26" <?php if($pre_navigation_id==-26){ ?> selected="selected" <?php } ?> >F2C系统</option><?php } ?>

														<option value="-19" <?php if($pre_navigation_id==-19){ ?> selected="selected" <?php } ?> >拼团商品专区1</option>
														<option value="-30" <?php if($pre_navigation_id==-30){ ?> selected="selected" <?php } ?> >拼团商品专区2</option>
														<option value="-31" <?php if($pre_navigation_id==-31){ ?> selected="selected" <?php } ?> >拼团商品专区3</option>
														<option value="-34" <?php if($pre_navigation_id==-34){ ?> selected="selected" <?php } ?> >积分签到</option>
														<option value="-35" <?php if($pre_navigation_id==-35){ ?> selected="selected" <?php } ?> >积分兑换</option>
														<option value="-36" <?php if($pre_navigation_id==-36){ ?> selected="selected" <?php } ?> >限时专区</option>
														<option value="-361" <?php if($pre_navigation_id==-361){ ?> selected="selected" <?php } ?> >积分专区</option>

														<!--注意-33已有-->


														<?php
															if( !empty($diy_template_link) ){
														?>
														<optgroup label="---------------自定义模板---------------"></optgroup>
														<?php

															for( $i = 0; $i < $diy_template_count; $i++ ){
																$diy_template_arr = explode('_',$diy_template_link[$i]);
																$diy_template_id = $diy_template_arr[0];
																$diy_template_name = $diy_template_arr[1];
														?>
														<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
														<?php }}?>
														<optgroup label="---------------产品分类---------------"></optgroup>
														<!--<?php
														  if($typesize>0){
															 for($i=0;$i<$typesize; $i++){
																$typestr= $typeLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];


														?>
														  <option value="<?php echo $type_id; ?>_1" <?php if($pre_navigation_id==$type_id){ ?> selected="selected" <?php } ?> ><?php echo $type_name; ?></option>
														<?php  }

														} ?>-->
														<?php
															$navigation_cache = '';
															$navigation_cache_id = -1;
															$navigation_cache_linktype = -1;
															$navigation_cache = explode("_",$pre_navigation_id);
															$navigation_cache_id = $navigation_cache[0];
															$navigation_cache_linktype = $navigation_cache[1];
														?>
														<option value="-40" <?php if($navigation_cache_linktype==1 && $navigation_cache_id>0){?>selected="selected"<?php }?>>多级分类</option>
														<optgroup label="---------------图文消息---------------"></optgroup>
														<?php
														  if($imginfosize>0){
															 for($i=0;$i<$imginfosize; $i++){
																$typestr= $imginfoLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];


														?>
														  <option value="<?php echo $type_id; ?>_2" <?php if($pre_navigation_id==$type_id){ ?> selected="selected" <?php } ?> ><?php echo $type_name; ?></option>
														<?php  }

														} ?>

														<?php if($is_cityarea_caterer){?>
														<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
														<?php if($cityareaCaterersize>0){
																for($i=0;$i<$cityareaCaterersize; $i++){
																$typestr= $cityareaCatererLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_ktv){?>
														<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
														<?php if($cityareaKTVsize>0){
																for($i=0;$i<$cityareaKTVsize; $i++){
																$typestr= $cityareaKTVLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_hotel){?>
														<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
														<?php if($cityareaHotelsize>0){
																for($i=0;$i<$cityareaHotelsize; $i++){
																$typestr= $cityareaHotelLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_shop){?>
														<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
														<?php if($cityareaShopsize>0){
																for($i=0;$i<$cityareaShopsize; $i++){
																$typestr= $cityareaShopLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea){?>
														<optgroup label="-----------商圈行业列表-----------"></optgroup>
														<?php }
														if($is_cityarea_caterer){?>
														<option value="2_50" >美食</option>
														<?php }
														if($is_cityarea_ktv){?>
														<option value="2_51" >KTV</option>
														<?php }
														if($is_cityarea_hotel){?>
														<option value="2_52" >酒店</option>
														<?php }
														if($is_cityarea_shop){?>
														<option value="2_53" >线下商城-首页</option>
                                                        <option value="2_54" >线下商城-商家列表</option>
														<?php }
														?>
														<?php if($is_cityarea_finance){?>
								                        <option value="2_63" >金融-贷款</option>
								                        <option value="2_61" >金融-信用卡</option>
								                        <option value="2_62" >金融-保险</option>
								                        <?php }?>
														<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
													</select>
													<select name="product_type" style="<?php if($navigation_cache_linktype==1 && $navigation_cache_id>0){?>display:block;<?php }else{?>display:none;<?php }?>" class="product_type"><!--分类-->
															<!--<option value=-1>--------请选择--------</option>-->
															<?php
																$type_len = count($type_arr[-1]);
																for($i = 0;$i<$type_len;$i++){
																	$typearr = explode("_",$type_arr[-1][$i]);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];

															 ?>
															<option value="<?php echo $type_id; ?>_1" <?php if($navigation_cache_linktype==1 && $navigation_cache_id==$type_id){ ?> selected="selected" <?php } ?>><?php echo $type_name; ?></option>
															<?php
																$type_len2 = count($type_arr[$type_id]);
																for($ii = 0;$ii<$type_len2;$ii++){
																	$typearr2 = explode("_",$type_arr[$type_id][$ii]);
																	$type_id2 = $typearr2[0];
																	$type_name2 = $typearr2[1];
															?>
															<option value="<?php echo $type_id2; ?>_1" <?php if($navigation_cache_linktype==1 && $navigation_cache_id==$type_id2){ ?> selected="selected" <?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
															<?php
																$type_len3 = count($type_arr[$type_id2]);
																for($iii = 0;$iii<$type_len3;$iii++){
																	$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
																	$type_id3 = $typearr3[0];
																	$type_name3 = $typearr3[1];
															?>
															<option value="<?php echo $type_id3; ?>_1" <?php if($navigation_cache_linktype==1 && $navigation_cache_id==$type_id3){ ?> selected="selected" <?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
															<?php
																$type_len4 = count($type_arr[$type_id3]);
																for($iiii = 0;$iiii<$type_len4;$iiii++){
																	$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
																	$type_id4 = $typearr4[0];
																	$type_name4 = $typearr4[1];
															?>
															<option value="<?php echo $type_id4; ?>_1" <?php if($navigation_cache_linktype==1 && $navigation_cache_id==$type_id4){ ?> selected="selected" <?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
															<?php
																}
																}
																}
															}
															?>
														</select>
												</div>
											<div class="clear"></div>
										</div>
										<?php
											$pros_separate=explode("#",$pre_subpros);
											$links_separate=explode("#",$pre_sublinks);
											$count=count($pros_separate);
											?>

										<div class="opt_item submenu" <?php if($pre_chk_submenu==1){ ?> style="display:block;" <?php }else{ ?> style="display:none;"<?php } ?> >
											<label>子菜单：</label>
											<span class="input">
												<ul>
												<?php $k=1;
													for($j=0;$j<$count;$j++){
												?>
													<li>
														<input type="text" name="SubmenuName[]" value="<?php echo $pros_separate[$j];?>" class="form_input" size="15" maxlength="30">
														<input type="hidden" name="NId[]" value="">
														<img src="../../../Common/images/Base/home_decoration/del.gif" width="18px;">

														<?php if($k==$count){ ?><img src="../../../Common/images/Base/home_decoration/add.gif" width="18px"> <?php } ?>
													</li>
													<li>
														<select  name="Submenulink[]" onchange="change_type(this)">
															<option value="-1" <?php if($links_separate[$j]==-1){?> selected="selected"<?php } ?> >--------请选择--------</option>
															<option value="-6" <?php if($links_separate[$j]==-6){?> selected="selected"<?php } ?> >全部产品</option>
															<option value="-2" <?php if($links_separate[$j]==-2){?> selected="selected"<?php } ?> >新品上市</option>
															<option value="-3" <?php if($links_separate[$j]==-3){?> selected="selected"<?php } ?> >热卖产品</option>
															<option value="-4" <?php if($links_separate[$i]==-4){?> selected="selected"<?php } ?> >购物车</option>
															<option value="-8" <?php if($links_separate[$j]==-8){?> selected="selected"<?php } ?> >个人中心</option>
															<option value="-18" <?php if($links_separate[$j]==-18){ ?> selected="selected" <?php } ?>>我的订单</option>
															<option value="-7" <?php if($links_separate[$j]==-7){?> selected="selected"<?php } ?> >产品分类页</option>
															<option value="-9" <?php if($links_separate[$j]==-9){?> selected="selected"<?php } ?> >我的微店</option>
															<option value="-5" <?php if($links_separate[$j]==-5){?> selected="selected"<?php } ?> >限时抢购</option>
															<option value="-33" <?php if($links_separate[$j]==-33){?> selected="selected"<?php } ?> >区域批发商列表</option>
															<option value="-10" <?php if($links_separate[$j]==-10){?> selected="selected"<?php } ?> >商城在线客服</option>
															<option value="-11" <?php if($links_separate[$j]==-11){?> selected="selected"<?php } ?> >礼包列表</option>
															<option value="-12" <?php if($links_separate[$j]==-12){?> selected="selected"<?php } ?> >VP产品</option>
															<!--option value="-19"  selected="selected"  >拼团列表</option-->
															<option value="-20" <?php if($links_separate[$j]==-20){ ?> selected="selected" <?php } ?> >人气团列表</option>
															<option value="-21" <?php if($links_separate[$j]==-21){ ?> selected="selected" <?php } ?> >续费专区</option>
															<option value="-22" <?php if($links_separate[$j]==-22){ ?> selected="selected" <?php } ?> >电商直播</option>
															<option value="-23" <?php if($links_separate[$j]==-23){ ?> selected="selected" <?php } ?> >语音直播</option>
															<?php if($is_ticket){?>
															<option value="-24" <?php if($links_separate[$j]==-24){ ?> selected="selected" <?php } ?> >票务特价机票</option>
															<option value="-25" <?php if($links_separate[$j]==-25){ ?> selected="selected" <?php } ?> >票务特价火车票</option>
															<?php }?>
															<?php if($is_exchange){?>
															<option value="-32" <?php if($links_separate[$j]==-32){ ?> selected="selected" <?php } ?> >满赠专区</option>
															<?php }?>
															<option value="-95" <?php if($pre_navigation_id==-95){ ?> selected="selected" <?php } ?> >砍价活动</option>
															<option value="-96" <?php if($pre_navigation_id==-96){ ?> selected="selected" <?php } ?> >众筹新版</option>
															<?php if($is_f2c>0){ ?>
															<option value="-26" <?php if($pre_navigation_id==-26){ ?> selected="selected" <?php } ?> >F2C系统</option><?php } ?>

															<option value="-19" <?php if($pre_navigation_id==-19){ ?> selected="selected" <?php } ?> >拼团商品专区1</option>
															<option value="-30" <?php if($pre_navigation_id==-30){ ?> selected="selected" <?php } ?> >拼团商品专区2</option>
															<option value="-31" <?php if($pre_navigation_id==-31){ ?> selected="selected" <?php } ?> >拼团商品专区3</option>
															<option value="-34" <?php if($pre_navigation_id==-34){ ?> selected="selected" <?php } ?> >积分签到</option>
															<option value="-35" <?php if($pre_navigation_id==-35){ ?> selected="selected" <?php } ?> >积分兑换</option>
															<option value="-36" <?php if($pre_navigation_id==-36){ ?> selected="selected" <?php } ?> >限时专区</option>
															<option value="-361" <?php if($pre_navigation_id==-361){ ?> selected="selected" <?php } ?> >积分专区</option>
															<?php
																if( !empty($diy_template_link) ){
															?>
															<optgroup label="---------------自定义模板---------------"></optgroup>
															<?php

																for( $i = 0; $i < $diy_template_count; $i++ ){
																	$diy_template_arr = explode('_',$diy_template_link[$i]);
																	$diy_template_id = $diy_template_arr[0];
																	$diy_template_name = $diy_template_arr[1];
															?>
															<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
															<?php }}?>
															<optgroup label="---------------产品分类---------------"></optgroup>
															<!--<?php
															  if($typesize>0){
																 for($i=0;$i<$typesize; $i++){
																	$typestr= $typeLst->Get($i);

																	$typearr = explode("_",$typestr);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];


															?>
															  <option value="<?php echo $type_id; ?>_1" <?php if($links_separate[$j]==$type_id."_1"){?> selected="selected"<?php } ?> ><?php echo $type_name; ?></option>
															<?php  }

															} ?>-->
															<?php
															$links_separate_cache = '';
															$links_separate_cache_id = -1;
															$links_separate_cache_linktype = -1;
															$links_separate_cache = explode("_",$links_separate[$j]);
															$links_separate_cache_id = $links_separate_cache[0];
															$links_separate_cache_linktype = $links_separate_cache[1];
															?>
															<option value="-40" <?php if($links_separate_cache_id>0 && $links_separate_cache_linktype==1){?>selected="selected"<?php }?>>多级分类</option>
															<optgroup label="---------------图文消息---------------"></optgroup>
															<?php
															  if($imginfosize>0){
																 for($i=0;$i<$imginfosize; $i++){
																	$typestr= $imginfoLst->Get($i);

																	$typearr = explode("_",$typestr);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];


															?>
															  <option value="<?php echo $type_id; ?>_2" <?php if($links_separate[$j]==$type_id."_2"){?> selected="selected"<?php } ?> ><?php echo $type_name; ?></option>
															<?php  }

															} ?>

															<?php if($is_cityarea_caterer){?>
															<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
															<?php if($cityareaCaterersize>0){
																	for($i=0;$i<$cityareaCaterersize; $i++){
																	$typestr= $cityareaCatererLst->Get($i);

																	$typearr = explode("_",$typestr);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];
															?>
															<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
															<?php  }

															}} ?>

															<?php if($is_cityarea_ktv){?>
															<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
															<?php if($cityareaKTVsize>0){
																	for($i=0;$i<$cityareaKTVsize; $i++){
																	$typestr= $cityareaKTVLst->Get($i);

																	$typearr = explode("_",$typestr);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];
															?>
															<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
															<?php  }

															}} ?>

															<?php if($is_cityarea_hotel){?>
															<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
															<?php if($cityareaHotelsize>0){
																	for($i=0;$i<$cityareaHotelsize; $i++){
																	$typestr= $cityareaHotelLst->Get($i);

																	$typearr = explode("_",$typestr);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];
															?>
															<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
															<?php  }

															}} ?>

															<?php if($is_cityarea_shop){?>
															<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
															<?php if($cityareaShopsize>0){
																	for($i=0;$i<$cityareaShopsize; $i++){
																	$typestr= $cityareaShopLst->Get($i);

																	$typearr = explode("_",$typestr);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];
															?>
															<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
															<?php  }

															}} ?>

															<?php if($is_cityarea){?>
															<optgroup label="-----------商圈行业列表-----------"></optgroup>
															<?php }
															if($is_cityarea_caterer){?>
															<option value="2_50" >美食</option>
															<?php }
															if($is_cityarea_ktv){?>
															<option value="2_51" >KTV</option>
															<?php }
															if($is_cityarea_hotel){?>
															<option value="2_52" >酒店</option>
															<?php }
															if($is_cityarea_shop){?>
															<option value="2_53" >线下商城-首页</option>
                                                            <option value="2_54" >线下商城-商家列表</option>
															<?php }
															?>
															<?php if($is_cityarea_finance){?>
									                        <option value="2_63" >金融-贷款</option>
									                        <option value="2_61" >金融-信用卡</option>
									                        <option value="2_62" >金融-保险</option>
									                        <?php }?>
															<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
														</select>
														<select name="product_types[]" style="<?php if($links_separate_cache_id>0 && $links_separate_cache_linktype==1){?>display:block;<?php }else{?>display:none;<?php }?>" class="product_type"><!--分类-->
															<!--<option value=-1>--------请选择--------</option>-->
															<?php
																$type_len = count($type_arr[-1]);
																for($i = 0;$i<$type_len;$i++){
																	$typearr = explode("_",$type_arr[-1][$i]);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];

															 ?>
															<option value="<?php echo $type_id; ?>_1" <?php if($links_separate_cache_linktype==1 &&$links_separate_cache_id==$type_id){?> selected="selected"<?php } ?>><?php echo $type_name; ?></option>
															<?php
																$type_len2 = count($type_arr[$type_id]);
																for($ii = 0;$ii<$type_len2;$ii++){
																	$typearr2 = explode("_",$type_arr[$type_id][$ii]);
																	$type_id2 = $typearr2[0];
																	$type_name2 = $typearr2[1];
															?>
															<option value="<?php echo $type_id2; ?>_1" <?php if($links_separate_cache_linktype==1 &&$links_separate_cache_id==$type_id2){?> selected="selected"<?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
															<?php
																$type_len3 = count($type_arr[$type_id2]);
																for($iii = 0;$iii<$type_len3;$iii++){
																	$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
																	$type_id3 = $typearr3[0];
																	$type_name3 = $typearr3[1];
															?>
															<option value="<?php echo $type_id3; ?>_1" <?php if($links_separate_cache_linktype==1 &&$links_separate_cache_id==$type_id3){?> selected="selected"<?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
															<?php
																$type_len4 = count($type_arr[$type_id3]);
																for($iiii = 0;$iiii<$type_len4;$iiii++){
																	$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
																	$type_id4 = $typearr4[0];
																	$type_name4 = $typearr4[1];
															?>
															<option value="<?php echo $type_id4; ?>_1" <?php if($links_separate_cache_linktype==1 &&$links_separate_cache_id==$type_id4){?> selected="selected"<?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
															<?php
																}
																}
																}
															}
															?>
														</select>
													</li>
												<?php  $k++;} ?>
												</ul>
											</span>
											<input type="hidden" id="keyid" name="keyid" value="<?php echo $key_id;?>">
											<input type="hidden" id="subpro" name="subpro" value="">
											<input type="hidden" id="sublinks_id" name="sublinks_id" value="">
											<div class="clear"></div>
										</div>


									<?php }else{ ?>
										<div class="opt_item navigation_link">
											<em style="float:left;">导航栏链接：</em>
												<div class="input" style="width:167px;float:left;">
													<select  name="navigation_id"  id="navigation_id" onchange="change_type(this)">
														<option value="-1" selected="selected">--------请选择--------</option>
														<option value="-16" >首页</option>
														<option value="-6" >全部产品</option>
														<option value="-2" >新品上市</option>
														<option value="-3" >热卖产品</option>
														<option value="-4" >购物车</option>
														<option value="-8" >个人中心</option>
														<option value="-18" >我的订单</option>
														<option value="-7" >产品分类页1</option>
														<option value="-17" >产品分类页2</option>
														<option value="-37" >产品分类页3</option>
														<option value="-47" >产品分类页4</option>
														<option value="-9" >我的微店</option>
														<option value="-5" >限时抢购</option>
														<option value='-33' >区域批发商列表</option>
														<option value="-10" >商城在线客服</option>
														<option value="-11" >礼包列表</option>
														<option value="-12" >VP产品</option>
														<option value="-15" >兑换专区</option>
														<!--option value="-19" >拼团列表</option-->
														<option value="-20" >人气团列表</option>
														<option value="-21" >续费专区</option>
														<option value="-22" >电商直播</option>
														<option value="-23" >语音直播</option>
														<?php if($is_ticket){?>
														<option value="-24" >票务特价机票</option>
														<option value="-25" >票务特价火车票</option>
														<?php }?>
														<?php if($is_exchange){?>
														<option value="-32" >满赠专区</option>
														<?php }?>
														<option value="-95" >砍价活动</option>
														<option value="-96" >众筹新版</option>
														<?php if($is_f2c>0){ ?>
														<option value="-26" >F2C系统</option>
														<?php } ?>
                                                        <option value="-27" >订货系统登录</option>
                                                        <option value="-28" >订货系统申请</option>
                                                        <option value="-29" >订货系统中心</option>
                                                        <option value="-19" >拼团商品专区1</option>
								                        <option value="-30" >拼团商品专区2</option>
								                        <option value="-31" >拼团商品专区3</option>
                                                        <option value="-34" >积分签到</option>
                                                        <option value="-35" >积分兑换</option>
                                                        <option value="-36" >限时专区</option>
                                                        <option value="-361" >积分专区</option>
														<?php
															if( !empty($diy_template_link) ){
														?>
														<optgroup label="---------------自定义模板---------------"></optgroup>
														<?php

															for( $i = 0; $i < $diy_template_count; $i++ ){
																$diy_template_arr = explode('_',$diy_template_link[$i]);
																$diy_template_id = $diy_template_arr[0];
																$diy_template_name = $diy_template_arr[1];
														?>
														<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
														<?php }}?>
														<optgroup label="---------------产品分类---------------"></optgroup>
														<!--<?php
														  if($typesize>0){
															 for($i=0;$i<$typesize; $i++){
																$typestr= $typeLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];


														?>
														  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
														<?php  }

														} ?>-->
														<option value="-40" >多级分类</option>
														<optgroup label="---------------图文消息---------------"></optgroup>
														<?php
														  if($imginfosize>0){
															 for($i=0;$i<$imginfosize; $i++){
																$typestr= $imginfoLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];


														?>
														  <option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
														<?php  }

														} ?>

														<?php if($is_cityarea_caterer){?>
														<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
														<?php if($cityareaCaterersize>0){
																for($i=0;$i<$cityareaCaterersize; $i++){
																$typestr= $cityareaCatererLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_ktv){?>
														<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
														<?php if($cityareaKTVsize>0){
																for($i=0;$i<$cityareaKTVsize; $i++){
																$typestr= $cityareaKTVLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_hotel){?>
														<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
														<?php if($cityareaHotelsize>0){
																for($i=0;$i<$cityareaHotelsize; $i++){
																$typestr= $cityareaHotelLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_shop){?>
														<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
														<?php if($cityareaShopsize>0){
																for($i=0;$i<$cityareaShopsize; $i++){
																$typestr= $cityareaShopLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea){?>
														<optgroup label="-----------商圈行业列表-----------"></optgroup>
														<?php }
														if($is_cityarea_caterer){?>
														<option value="2_50" >美食</option>
														<?php }
														if($is_cityarea_ktv){?>
														<option value="2_51" >KTV</option>
														<?php }
														if($is_cityarea_hotel){?>
														<option value="2_52" >酒店</option>
														<?php }
														if($is_cityarea_shop){?>
														<option value="2_53" >线下商城-首页</option>
                                                        <option value="2_54" >线下商城-商家列表</option>
														<?php }
														?>
														<?php if($is_cityarea_finance){?>
								                        <option value="2_63" >金融-贷款</option>
								                        <option value="2_61" >金融-信用卡</option>
								                        <option value="2_62" >金融-保险</option>
								                        <?php }?>
														<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
													</select>
													<select name="product_types" style="display:none;" class="product_type"><!--分类-->
															<!--<option value=-1>--------请选择--------</option>-->
															<?php
															  $type_len = count($type_arr[-1]);
																for($i = 0;$i<$type_len;$i++){
																	$typearr = explode("_",$type_arr[-1][$i]);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];

															 ?>
															<option value="<?php echo $type_id; ?>_1"><?php echo $type_name; ?></option>
															<?php
																$type_len2 = count($type_arr[$type_id]);
																for($ii = 0;$ii<$type_len2;$ii++){
																	$typearr2 = explode("_",$type_arr[$type_id][$ii]);
																	$type_id2 = $typearr2[0];
																	$type_name2 = $typearr2[1];
															?>
															<option value="<?php echo $type_id2; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
															<?php
																$type_len3 = count($type_arr[$type_id2]);
																for($iii = 0;$iii<$type_len3;$iii++){
																	$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
																	$type_id3 = $typearr3[0];
																	$type_name3 = $typearr3[1];
															?>
															<option value="<?php echo $type_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
															<?php
																$type_len4 = count($type_arr[$type_id3]);
																for($iiii = 0;$iiii<$type_len4;$iiii++){
																	$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
																	$type_id4 = $typearr4[0];
																	$type_name4 = $typearr4[1];
															?>
															<option value="<?php echo $type_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
															<?php
																}
																}
																}
															}
															?>
														</select>
												</div>
											<div class="clear"></div>
										</div>
										<div class="opt_item submenu" style="display:none;">
											<label>子菜单：</label>
											<span class="input">
												<ul>
													<li>
														<input type="text" name="SubmenuName[]" value="" class="form_input" size="15" maxlength="30">
														<input type="hidden" name="NId[]" value="">
														<img src="../../../Common/images/Base/home_decoration/del.gif" width="18px;">

													</li>
													<li>
														<select  name="Submenulink[]" onchange="change_type(this)">
															<option value="-1" selected="selected">--------请选择--------</option>
															<option value="-16" >首页</option>
															<option value="-6" >全部产品</option>
															<option value="-2" >新品上市</option>
															<option value="-3" >热卖产品</option>
															<option value="-4" >购物车</option>
															<option value="-8" >个人中心</option>
															<option value="-18" >我的订单</option>
															<option value="-7" >产品分类页1</option>
															<option value="-17" >产品分类页2</option>
															<option value="-37" >产品分类页3</option>
															<option value="-47" >产品分类页4</option>
															<option value="-9" >我的微店</option>
															<option value="-5" >限时抢购</option>
															<option value='-33' >区域批发商列表</option>
															<option value="-10" >商城在线客服</option>
															<option value="-11" >礼包列表</option>
															<option value="-12" >VP产品</option>
															<option value="-15" >兑换专区</option>
															<!--option value="-19" >拼团列表</option-->
															<option value="-20" >人气团列表</option>
															<option value="-21" >续费专区</option>
															<option value="-22" >电商直播</option>
															<option value="-23" >语音直播</option>
															<?php if($is_ticket){?>
															<option value="-24" >票务特价机票</option>
															<option value="-25" >票务特价火车票</option>
															<?php }?>
															<?php if($is_exchange){?>
															<option value="-32" >满赠专区</option>
															<?php }?>
															<option value="-95" >砍价活动</option>
															<option value="-96" >众筹新版</option>
															<?php if($is_f2c>0){ ?>
															<option value="-26" >F2C系统</option>
															<?php } ?>
                                                            <option value="-27" >订货系统登录</option>
                                                            <option value="-28" >订货系统申请</option>
                                                            <option value="-29" >订货系统中心</option>
                                                            <option value="-19" >拼团商品专区1</option>
									                        <option value="-30" >拼团商品专区2</option>
									                        <option value="-31" >拼团商品专区3</option>
                                                            <option value="-34" >积分签到</option>
                                                            <option value="-35" >积分兑换</option>
                                                            <option value="-36" >限时专区</option>
                                                            <option value="-361" >积分专区</option>
															<?php
																if( !empty($diy_template_link) ){
															?>
															<optgroup label="---------------自定义模板---------------"></optgroup>
															<?php

																for( $i = 0; $i < $diy_template_count; $i++ ){
																	$diy_template_arr = explode('_',$diy_template_link[$i]);
																	$diy_template_id = $diy_template_arr[0];
																	$diy_template_name = $diy_template_arr[1];
															?>
															<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
															<?php }}?>
															<optgroup label="---------------产品分类---------------"></optgroup>
															<!--<?php
															  if($typesize>0){
																 for($i=0;$i<$typesize; $i++){
																	$typestr= $typeLst->Get($i);

																	$typearr = explode("_",$typestr);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];


															?>
															  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
															<?php  }

															} ?>-->
															<option value="-40" >多级分类</option>
															<optgroup label="---------------图文消息---------------"></optgroup>
															<?php
															  if($imginfosize>0){
																 for($i=0;$i<$imginfosize; $i++){
																	$typestr= $imginfoLst->Get($i);

																	$typearr = explode("_",$typestr);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];


															?>
															  <option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
															<?php  }

															} ?>

														<?php if($is_cityarea_caterer){?>
														<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
														<?php if($cityareaCaterersize>0){
																for($i=0;$i<$cityareaCaterersize; $i++){
																$typestr= $cityareaCatererLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_ktv){?>
														<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
														<?php if($cityareaKTVsize>0){
																for($i=0;$i<$cityareaKTVsize; $i++){
																$typestr= $cityareaKTVLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_hotel){?>
														<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
														<?php if($cityareaHotelsize>0){
																for($i=0;$i<$cityareaHotelsize; $i++){
																$typestr= $cityareaHotelLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_shop){?>
														<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
														<?php if($cityareaShopsize>0){
																for($i=0;$i<$cityareaShopsize; $i++){
																$typestr= $cityareaShopLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea){?>
														<optgroup label="-----------商圈行业列表-----------"></optgroup>
														<?php }
														if($is_cityarea_caterer){?>
														<option value="2_50" >美食</option>
														<?php }
														if($is_cityarea_ktv){?>
														<option value="2_51" >KTV</option>
														<?php }
														if($is_cityarea_hotel){?>
														<option value="2_52" >酒店</option>
														<?php }
														if($is_cityarea_shop){?>
														<option value="2_53" >线下商城-首页</option>
                                                        <option value="2_54" >线下商城-商家列表</option>
														<?php }
														?>
														<?php if($is_cityarea_finance){?>
								                        <option value="2_63" >金融-贷款</option>
								                        <option value="2_61" >金融-信用卡</option>
								                        <option value="2_62" >金融-保险</option>
								                        <?php }?>
														<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
														</select>
														<select name="product_types[]" style="display:none;" class="product_type"><!--分类-->
															<!--<option value=-1>--------请选择--------</option>-->
															<?php
															  $type_len = count($type_arr[-1]);
																for($i = 0;$i<$type_len;$i++){
																	$typearr = explode("_",$type_arr[-1][$i]);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];

															 ?>
															<option value="<?php echo $type_id; ?>_1"><?php echo $type_name; ?></option>
															<?php
																$type_len2 = count($type_arr[$type_id]);
																for($ii = 0;$ii<$type_len2;$ii++){
																	$typearr2 = explode("_",$type_arr[$type_id][$ii]);
																	$type_id2 = $typearr2[0];
																	$type_name2 = $typearr2[1];
															?>
															<option value="<?php echo $type_id2; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
															<?php
																$type_len3 = count($type_arr[$type_id2]);
																for($iii = 0;$iii<$type_len3;$iii++){
																	$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
																	$type_id3 = $typearr3[0];
																	$type_name3 = $typearr3[1];
															?>
															<option value="<?php echo $type_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
															<?php
																$type_len4 = count($type_arr[$type_id3]);
																for($iiii = 0;$iiii<$type_len4;$iiii++){
																	$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
																	$type_id4 = $typearr4[0];
																	$type_name4 = $typearr4[1];
															?>
															<option value="<?php echo $type_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
															<?php
																}
																}
																}
															}
															?>
														</select>
													</li>
													<li>
														<input type="text" name="SubmenuName[]" value="" class="form_input" size="15" maxlength="30">
														<input type="hidden" name="NId[]" value="">
														<img src="../../../Common/images/Base/home_decoration/del.gif" width="18px;">
														<img src="../../../Common/images/Base/home_decoration/add.gif" width="18px;">
													</li>
													<li>
														<select  name="Submenulink[]" onchange="change_type(this)">
															<option value="-1" selected="selected">--------请选择--------</option>
															<option value="-16" >首页</option>
															<option value="-6" >全部产品</option>
															<option value="-2" >新品上市</option>
															<option value="-3" >热卖产品</option>
															<option value="-4" >购物车</option>
															<option value="-8" >个人中心</option>
															<option value="-18" >我的订单</option>
															<option value="-7" >产品分类页1</option>
															<option value="-17" >产品分类页2</option>
															<option value="-37" >产品分类页3</option>
															<option value="-47" >产品分类页4</option>
															<option value="-9" >我的微店</option>
															<option value="-5" >限时抢购</option>
															<option value='-33' >区域批发商列表</option>
															<option value="-10" >商城在线客服</option>
															<option value="-11" >礼包列表</option>
															<option value="-12" >VP产品</option>
															<option value="-15" >兑换专区</option>
															<!--option value="-19" >拼团列表</option-->
															<option value="-20" >人气团列表</option>
															<option value="-21" >续费专区</option>
															<option value="-22" >电商直播</option>
															<option value="-23" >语音直播</option>
															<?php if($is_ticket){?>
															<option value="-24" >票务特价机票</option>
															<option value="-25" >票务特价火车票</option>
															<?php }?>
															<?php if($is_exchange){?>
															<option value="-32" >满赠专区</option>
															<?php }?>
															<option value="-95" >砍价活动</option>
															<option value="-96" >众筹新版</option>
															<?php if($is_f2c>0){ ?>
															<option value="-26" >F2C系统</option>
															<?php } ?>
                                                            <option value="-27" >订货系统登录</option>
                                                            <option value="-28" >订货系统申请</option>
                                                            <option value="-29" >订货系统中心</option>
                                                            <option value="-19" >拼团商品专区1</option>
									                        <option value="-30" >拼团商品专区2</option>
									                        <option value="-31" >拼团商品专区3</option>
                                                            <option value="-34" >积分签到</option>
                                                            <option value="-35" >积分兑换</option>
                                                            <option value="-36" >限时专区</option>
                                                            <option value="-361" >积分专区</option>
															<?php
																if( !empty($diy_template_link) ){
															?>
															<optgroup label="---------------自定义模板---------------"></optgroup>
															<?php

																for( $i = 0; $i < $diy_template_count; $i++ ){
																	$diy_template_arr = explode('_',$diy_template_link[$i]);
																	$diy_template_id = $diy_template_arr[0];
																	$diy_template_name = $diy_template_arr[1];
															?>
															<option value="<?php echo $diy_template_id;?>_3"><?php echo $diy_template_name;?></option>
															<?php }}?>
															<optgroup label="---------------产品分类---------------"></optgroup>
															<!--<?php
															  if($typesize>0){
																 for($i=0;$i<$typesize; $i++){
																	$typestr= $typeLst->Get($i);

																	$typearr = explode("_",$typestr);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];


															?>
															  <option value="<?php echo $type_id; ?>_1" ><?php echo $type_name; ?></option>
															<?php  }

															} ?>-->
															<option value="-40" >多级分类</option>
															<optgroup label="---------------图文消息---------------"></optgroup>
															<?php
															  if($imginfosize>0){
																 for($i=0;$i<$imginfosize; $i++){
																	$typestr= $imginfoLst->Get($i);

																	$typearr = explode("_",$typestr);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];


															?>
															  <option value="<?php echo $type_id; ?>_2" ><?php echo $type_name; ?></option>
															<?php  }

															} ?>

														<?php if($is_cityarea_caterer){?>
														<optgroup label="-----------城市商圈（美食）-----------"></optgroup>
														<?php if($cityareaCaterersize>0){
																for($i=0;$i<$cityareaCaterersize; $i++){
																$typestr= $cityareaCatererLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_10" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_ktv){?>
														<optgroup label="-----------城市商圈（KTV）-----------"></optgroup>
														<?php if($cityareaKTVsize>0){
																for($i=0;$i<$cityareaKTVsize; $i++){
																$typestr= $cityareaKTVLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_11" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_hotel){?>
														<optgroup label="-----------城市商圈（酒店）-----------"></optgroup>
														<?php if($cityareaHotelsize>0){
																for($i=0;$i<$cityareaHotelsize; $i++){
																$typestr= $cityareaHotelLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_12" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea_shop){?>
														<optgroup label="-----------城市商圈（线下商城）-----------"></optgroup>
														<?php if($cityareaShopsize>0){
																for($i=0;$i<$cityareaShopsize; $i++){
																$typestr= $cityareaShopLst->Get($i);

																$typearr = explode("_",$typestr);
																$type_id = $typearr[0];
																$type_name = $typearr[1];
														?>
														<option value="<?php echo $type_id; ?>_13" ><?php echo $type_name; ?></option>
														<?php  }

														}} ?>

														<?php if($is_cityarea){?>
														<optgroup label="-----------商圈行业列表-----------"></optgroup>
														<?php }
														if($is_cityarea_caterer){?>
														<option value="2_50" >美食</option>
														<?php }
														if($is_cityarea_ktv){?>
														<option value="2_51" >KTV</option>
														<?php }
														if($is_cityarea_hotel){?>
														<option value="2_52" >酒店</option>
														<?php }
														if($is_cityarea_shop){?>
														<option value="2_53" >线下商城-首页</option>
                                                        <option value="2_54" >线下商城-商家列表</option>
														<?php }
														?>
														<?php if($is_cityarea_finance){?>
								                        <option value="2_63" >金融-贷款</option>
								                        <option value="2_61" >金融-信用卡</option>
								                        <option value="2_62" >金融-保险</option>
								                        <?php }?>
														<?php if($is_cityarea_coach){?>
                        <option value="2_64" >教练服务首页</option>
                        <?php }?>
						<?php if($is_yiren){ ?><option value="2_65" >艺人服务</option> <?php } ?>
														</select>
														<select name="product_types[]" style="display:none;" class="product_type"><!--分类-->
															<!--<option value=-1>--------请选择--------</option>-->
															<?php
															  $type_len = count($type_arr[-1]);
																for($i = 0;$i<$type_len;$i++){
																	$typearr = explode("_",$type_arr[-1][$i]);
																	$type_id = $typearr[0];
																	$type_name = $typearr[1];

															 ?>
															<option value="<?php echo $type_id; ?>_1"><?php echo $type_name; ?></option>
															<?php
																$type_len2 = count($type_arr[$type_id]);
																for($ii = 0;$ii<$type_len2;$ii++){
																	$typearr2 = explode("_",$type_arr[$type_id][$ii]);
																	$type_id2 = $typearr2[0];
																	$type_name2 = $typearr2[1];
															?>
															<option value="<?php echo $type_id2; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name2; ?></option>
															<?php
																$type_len3 = count($type_arr[$type_id2]);
																for($iii = 0;$iii<$type_len3;$iii++){
																	$typearr3 = explode("_",$type_arr[$type_id2][$iii]);
																	$type_id3 = $typearr3[0];
																	$type_name3 = $typearr3[1];
															?>
															<option value="<?php echo $type_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name3; ?></option>
															<?php
																$type_len4 = count($type_arr[$type_id3]);
																for($iiii = 0;$iiii<$type_len4;$iiii++){
																	$typearr4 = explode("_",$type_arr[$type_id3][$iiii]);
																	$type_id4 = $typearr4[0];
																	$type_name4 = $typearr4[1];
															?>
															<option value="<?php echo $type_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $type_name4; ?></option>
															<?php
																}
																}
																}
															}
															?>
														</select>
													</li>
												</ul>
											</span>
											<input type="hidden" id="keyid" name="keyid" value="">
											<input type="hidden" id="subpro" name="subpro" value="">
											<input type="hidden" id="sublinks_id" name="sublinks_id" value="">
											<div class="clear"></div>
										</div>



									<?php } ?>
								</dd>
								<dd>
									<input class="tj" type="button" value="添加导航" name="submit_button" onclick="subNav();" >
									<a href="defaultset.php"><input class="fh" type="button" value="返回"></a>
								</dd>
							</dl>
						</form>
					</div>
					<script>
						$(document).ready(function(){
							var chk_submenu='<?php echo $pre_chk_submenu;?>';
							if(chk_submenu==0){
								document.getElementById("chk_submenu").value=0;
							}else{
								document.getElementById("chk_submenu").value=chk_submenu;
								$(".submenu").css('display','block');
								$(".navigation_link").css('display','none');
							}


						})


							function subNav(){
								var name = $("#name").attr("value");
							   if($.trim(name)==""){
								  alert('请输入导航名称');
								  return;
							   }
								var chk_submenu = document.getElementById("chk_submenu").value;
								var navigation_id = document.getElementsByName("navigation_id")[0].value;
								if(chk_submenu == 0 && navigation_id == -40){
									var product_type = document.getElementsByName("product_type")[0].value;
									if(product_type<0){
										alert('请选择分类！');
										return;
									}
								}

							   //子菜单名称
								var SubmenuName = document.getElementsByName("SubmenuName[]");
								var SNlen = SubmenuName.length;
								var subpros="";
								for(i=0;i<SNlen;i++){
									   sub = SubmenuName[i];
									   var sv = sub.value;
									   if($.trim(sv)!=""){
										  //sv = sv.replace("_","");
										  subpros = subpros+sv+"#";
									   }
								}
								if(subpros.length>0){
									  subpros= subpros.substring(0,subpros.length-1);
								}

								//子菜单链接
								var Submenulink = document.getElementsByName("Submenulink[]");
								var SLlen = Submenulink.length;
								var sublinks_id="";
								for(i=0;i<SLlen;i++){
									   sublink = Submenulink[i];
									   var slv = sublink.value;
									   if(slv == -40){
										   var product_type = sublink.nextElementSibling.value;
										   if(product_type<0){
											   if(chk_submenu == 1){
												   i = SLlen;
												   alert('请选择分类！');
												   return;
											   }
										   }
										   slv = product_type;
									   }
									   if($.trim(slv)!=""){
										  //slv = slv.replace("_","");
										  sublinks_id = sublinks_id+slv+"#";
									   }
								}
								if(sublinks_id.length>0){
									  sublinks_id= sublinks_id.substring(0,sublinks_id.length-1);
								}
								document.getElementById("subpro").value=subpros;
								document.getElementById("sublinks_id").value=sublinks_id;
								$("#frm_pro").submit();
							}

							$('.is_submenu').click(function(){
								if($(this).val()==1){
									document.getElementById("chk_submenu").value=1;
									$(".submenu").css('display','block');
									$(".navigation_link").css('display','none');
								}else{
									document.getElementById("chk_submenu").value=0;
									$(".submenu").css('display','none');
									$(".navigation_link").css('display','block');
								}
							})

							var ul=$('.navigation_left #frm_pro .submenu span ul');
							var add_btn="<img src='../../../Common/images/Base/home_decoration/add.gif' width='18px;'>";
							$('.navigation_left #frm_pro .submenu span ul li img').live('click',function(){
								var img_btn=$(this).attr('src');
								img_btn=img_btn.slice(-7,-4);
								if(img_btn=='add'){
									ul.append(ul.children('li').eq(-2).clone(true));
									ul.append(ul.children('li').eq(-2).clone(true));
									$(this).remove();
								}else if(img_btn=='del'){
									if(ul.children('li').size()==2){
										alert("再删就没了，不需要子菜单请勾选【否】");
										return;
									}else{
										$(this).parent().next().remove();
										$(this).parent().remove();
										if(ul.find('img[src*=add]').size()==0){
											ul.children('li').eq(-2).append(add_btn);
										}
									}
								}
							})
					</script>

				<?php }else{?>
					<div style="width:100%;">
						<span style="text-align:center;font-size:18px;color:#999;display:block;">此模板不需要编辑底部菜单</span>
					</div>
				<?php }?>
				</div><!--tab 3结束-->
				</div>
			</div>
		</div>
		<!--首页设置代码结束-->

        <!--分类图片-->


</div>
</div>
<!--内容框架结束-->
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<script>


function setParentDefaultimgurl(type_imgurl){
    document.getElementById("type_imgurl").value=type_imgurl;
}

function up2(pt_id){
	document.location ="product_type.php?op=up&producttype_id="+pt_id;

}

function down2(pt_id){
	document.location ="product_type.php?op=down&producttype_id="+pt_id;
}

function setParentDefaultimgurl_cat(type_imgurl_cat){
    document.getElementById("type_imgurl_cat").value=type_imgurl_cat;
}

function up2_cat(pt_id){
	document.location ="product_type.php?op=up&producttype_id="+pt_id;

}

function down2_cat(pt_id){
	document.location ="product_type.php?op=down&producttype_id="+pt_id;
}

</script>
<script>
var detail_id='<?php echo $detail_id; ?>';
function jsonpCallback_get_product_list(results){
   var len = results.length;
   var sel_pro = document.getElementById("product_detail_id_2");
   sel_pro.options.length=0;

    var new_option = new Option("---请选择一个产品---",-1);
    sel_pro.options.add(new_option);
   for(i=2;i<len;i++){
      var pid = results[i].pid;
	  var pname = results[i].pname;

	  var new_option = new Option(pname,pid);
       sel_pro.options.add(new_option);
	  if(pid==detail_id){
	     new_option.selected=true;
	  }
   }

}

function jsonpCallback_get_product_list_cat(results){ //分类楼层广告
   var len = results.length;
   var sel_pro = document.getElementById("product_detail_id_cat");
   sel_pro.options.length=0;

    var new_option = new Option("---请选择一个产品---",-1);
    sel_pro.options.add(new_option);
   for(i=2;i<len;i++){
      var pid = results[i].pid;
	  var pname = results[i].pname;

	  var new_option = new Option(pname,pid);
       sel_pro.options.add(new_option);
	  if(pid==detail_id){
	     new_option.selected=true;
	  }
   }

}

function jsonpCallback_get_product_list_cat_index(results){ //分类首页显示  11.26
   var len = results.length;
   var sel_pro = document.getElementById("product_detail_id_cat_index");
   sel_pro.options.length=0;

    var new_option = new Option("---请选择一个产品---",-1);
    sel_pro.options.add(new_option);
   for(i=2;i<len;i++){
      var pid = results[i].pid;
	  var pname = results[i].pname;

	  var new_option = new Option(pname,pid);
       sel_pro.options.add(new_option);
	  if(pid==detail_id){
	     new_option.selected=true;
	  }
   }

}


function jsonpCallback_get_product_list_txt(results){
   var len = results.length;
   var sel_pro = document.getElementById("product_detail_id_3");
   sel_pro.options.length=0;

    var new_option = new Option("---请选择一个产品---",-1);
    sel_pro.options.add(new_option);
   for(i=2;i<len;i++){
      var pid = results[i].pid;
	  var pname = results[i].pname;

	  var new_option = new Option(pname,pid);
       sel_pro.options.add(new_option);
	  if(pid==p_detail_id){
	     new_option.selected=true;
	  }
   }

}

var slide_type=1;

function changeSliderType(type,selv){
   slide_type = type;
   document.getElementById("div_products_1_"+slide_type).style.display="none";
   var flag = selv.substring(selv.length-2,selv.length);
   if(flag == '_1'){
     //是产品分类
	 document.getElementById("div_products_1_"+slide_type).style.display="block";
	 var pro_typeid= selv.substring(0,selv.indexOf("_1"));
	 url='get_product_list.php?callback=jsonpCallback_get_product_list_slider&type_id='+pro_typeid;
     $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list_slider'
	});
  }
}

function jsonpCallback_get_product_list_slider(results){
   var len = results.length;
   var sel_pro = document.getElementById("product_detail_id_1_"+slide_type);
   sel_pro.options.length=0;

    var new_option = new Option("---请选择一个产品---",-1);
    sel_pro.options.add(new_option);
   for(i=2;i<len;i++){
      var pid = results[i].pid;
	  var pname = results[i].pname;

	  var new_option = new Option(pname,pid);
       sel_pro.options.add(new_option);
	  if(pid==detail_id){
	     new_option.selected=true;
	  }
   }
}
function change_type(obj){	//选择多级分类后，显示分类
	var val = obj.value;
	if(val == -40){
		var options = obj.nextElementSibling.options;
		options[0].selected = 'selected';
		obj.nextElementSibling.style.display='block';
		var type_val = obj.nextElementSibling.value;
		var type_val_bak = type_val;
		var type_val_arr = type_val.split('_');
		type_val = type_val_arr[0];
		if(type_val > 0){
			obj.nextElementSibling.nextElementSibling.style.display='block';
			changeProductType(type_val_bak);
		}
		obj.nextElementSibling.nextElementSibling.nextElementSibling.style.display='none';
	}else if(val == -11){
		// $(obj).siblings("#package_product_id").show()
		// obj.nextElementSibling.nextElementSibling.nextElementSibling.style.display='block';
	}else{
		obj.nextElementSibling.style.display='none';
		obj.nextElementSibling.nextElementSibling.style.display='none';
		obj.nextElementSibling.nextElementSibling.nextElementSibling.style.display='none';
	}
}

function change_type_4m(obj){	//选择多级分类后，显示分类
	var val = obj.value;
	if(val == -40){
		var options = obj.nextElementSibling.options;
		options[0].selected = 'selected';
		obj.nextElementSibling.style.display='block';
		var type_val = obj.nextElementSibling.value;
		var type_val_arr = type_val.split('_');
		type_val = type_val_arr[0];
		if(type_val > 0){
			obj.parentNode.nextElementSibling.style.display='block';
		}
	}else{
		obj.nextElementSibling.style.display='none';
		obj.parentNode.nextElementSibling.style.display='none';
	}
}
$('.search-btn').click(function(){
	var search_val = $(this).prev().val();
	var options = $(this).parents('dl').find('select').eq(0).find('option');
	options.each(function(i){
		if( options.eq(i).text() == search_val ){
			options.eq(i).attr('selected','selected');
			$(this).parents('dl').find('select').eq(0).change();
			return false;
		}
	});
});
</script>
<script type="text/javascript">
var n = 0
$(".homerightbox").hide();
$(".homerightbox:first").show();
$(".WSY_homeright_nav li a").click(function(){
	$(".WSY_homeright_nav li a").removeClass("blueAA");
	$(this).addClass("blueAA");
	n=$(".WSY_homeright_nav li a").index(this);
	$(".homerightbox").hide();
	$(".homerightbox:eq("+n+")").show();
})
window.onload = function() {
	$('.div_typename').each(function(){
		var a=$(this).html();
		//alert(a);
		if(a==null||a.length==0){
		   $(this).text('未设置');
		}

	});
}

function check_url(obj){
	var reg=/^(http:\/\/|https:\/\/|HTTP:\/\/|HTTPS:\/\/).*$/;
	if(!reg.test($(obj).val())){
		alert('提示信息：请输入以http://或https://开头正确的URL！');
		return false;
	}
}

</script>
<!-- 新选择链接 -->
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script>
   var that;//标签选择
   var customer_id_en = '<?php echo $customer_id_en; ?>';

	//选择优惠劵
	function showSelector(obj){
		that = obj;
        var selector_id = $(obj).parent().find('#selector_id').val();
		layer.open({
			  type: 2,
			  area: ['95%', '720px'],
			  fixed: false, //不固定
			  maxmin: true,
			  resize:true,
			  title: '选择链接页面',
			  content: '/mshop/admin/index.php?m=plug_link_selector&a=selector_list&customer_id='+customer_id_en+'&selector_id='+selector_id,
		});
	}
	//选择链接回调函数
	//[int] selector_id 链接组成ID [string] selector_title 链接名称
	function showSelectorCallback(selector_id,selector_title){
		console.log(selector_id);
		console.log(selector_title);
		$(that).parent().find("#selector_title").val(selector_title);
		$(that).parent().find("#selector_id").val(selector_id);
	}

	$(function(){
		var template_id = '<?php echo $template_id; ?>';
		if (template_id == '27') 
		{
			for (var i = 1; i <= 5; i++) 
			{
				$('#label_slide_'+i).text('750*1200');
			}
		}
	})

</script>
</body>
</html>
<?php

mysql_close($link);

?>