<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');  /*fenxiao下链接出错 11.13 by cdr*/

 require_once($_SERVER['DOCUMENT_ROOT'].'/mshop/web/model/integral.php');
 $model_integral = new model_integral();

$new_baseurl = $protocol_http_host;

$diy_temid			=-1; //保存diy_template表的ID
$diy_tem_contid		=-1; //模块的ID
$type				=-1; //模块类型
$title				=""; //文字标题
$imgurl				=""; //图片地址
$link_str			="#";
$color				=""; //文字颜色
$search_color		=""; //搜素栏背景颜色
$foreign_id			=-1; //图文消息之类ID
$detail_id			=-1; //商品ID
$video_link			=""; //视频链接
$mod_padding		=0; //模块间距
$mod_img_padding	=0; //模块内图片间距
$css_type			=""; //样式ID
$pro_title_show		=0; // 是否显示产品标题
$pro_title_twoline	=0; // 是否两行显示产品
$pro_numshow		=2; // 显示产品数量
$foot_position		=1; // 底部菜单样式，1-固定在底部，2-随页面移动
$placeholder		=""; // 搜索框提示语
$show_sale			=0; // 是否显示销量
$content			=-1; // diy_template表保存的模块顺序
$type_id_2			=-1;
$select_value		=-1;
$detail_value		=-1;
$detail_name		=-1;
$name				="";
$bgcolor			="";
$op					="";  //ajax操作
$link_type			=1; //分类连接类型 ,1 为固定连接，2为分类，3为图文，4为城市商圈
$rolling_direction	=""; //滚动公告栏滚动方向
$rolling_speed		=""; //滚动公告栏滚动速度
$show_time_limit	=""; //滚动公告栏每条公告显示时间
$city_name			=""; //城市名
$start_time			=""; //展示开始时间
$end_time			=""; //展示结束时间
$sel_link_type		=""; //链接类型，1：选择的链接，2：填写的链接
$shop_type		    =0; //商城类型: 0.线上商城 1.线下商城
$sort_type			=0; //排序类型: 0.按用户距商家距离从近到远排序 1.按商家销量从多到少排序
$divide_type		=0; //划分类型: 0.按产品分类 1.按店铺

if($_POST["diy_temid"]){
	$diy_temid	=	$configutil->splash_new($_POST["diy_temid"]);
}
if($_POST["diy_tem_contid"]){
	$diy_tem_contid	=	$configutil->splash_new($_POST["diy_tem_contid"]);
}
if($_POST["type"]){
	$type	=	$configutil->splash_new($_POST["type"]);
}
if($_POST["title"]){
	$title	=	$configutil->splash_new($_POST["title"]);
}
if($_POST["imgurl"]){
	$imgurl	=	$configutil->splash_new($_POST["imgurl"]);
	$imgurl=str_replace($new_baseurl,'',$imgurl);
}
if($_POST["select_value"]){
	$select_value	=	$configutil->splash_new($_POST["select_value"]);
}
if($_POST["detail_value"]){
	$detail_value	=	$configutil->splash_new($_POST["detail_value"]);
}
if($_POST["detail_name"]){
	$detail_name	=	$configutil->splash_new($_POST["detail_name"]);
}
if($_POST["color"]){
	$color	=	$configutil->splash_new($_POST["color"]);
}
if($_POST["search_color"]){
	$search_color	=	$configutil->splash_new($_POST["search_color"]);
}
if($_POST["foreign_id"]){
	$foreign_id	=	$configutil->splash_new($_POST["foreign_id"]);
}

if($_POST["detail_id"]){
	$detail_id	=	$configutil->splash_new($_POST["detail_id"]);
}
if($_POST["video_link"]){
	$video_link	=	$configutil->splash_new($_POST["video_link"]);
}
if($_POST["mod_padding"]){
	$mod_padding	=	$configutil->splash_new($_POST["mod_padding"]);
}
if($_POST["mod_img_padding"]){
	$mod_img_padding	=	$configutil->splash_new($_POST["mod_img_padding"]);
}
if($_POST["css_type"]){
	$css_type	=	$configutil->splash_new($_POST["css_type"]);
}
if(isset($_POST["pro_title_show"])){
	$pro_title_show	=	$configutil->splash_new($_POST["pro_title_show"]);
}
if(isset($_POST["pro_title_twoline"])){
	$pro_title_twoline	=	$configutil->splash_new($_POST["pro_title_twoline"]);
}
if($_POST["pro_numshow"]){
	$pro_numshow	=	$configutil->splash_new($_POST["pro_numshow"]);
}
if($_POST["foot_position"]){
	$foot_position	=	$configutil->splash_new($_POST["foot_position"]);
}
if($_POST["placeholder"]){
	$placeholder	=	$configutil->splash_new($_POST["placeholder"]);
}
if(isset($_POST["show_sale"])){
	$show_sale	=	$configutil->splash_new($_POST["show_sale"]);
}
if($_POST["content"]){
	$content	=	$configutil->splash_new($_POST["content"]);
	$content	=   ",".$content;
}
if($_POST["name"]){
	$name	=	$configutil->splash_new($_POST["name"]);
}
if($_POST["bgcolor"]){
	$bgcolor	=	$configutil->splash_new($_POST["bgcolor"]);
}
if($_POST["rolling_direction"]){
	$rolling_direction	=	$configutil->splash_new($_POST["rolling_direction"]);
}
if($_POST["rolling_speed"]){
	$rolling_speed	=	$configutil->splash_new($_POST["rolling_speed"]);
}
if($_POST["show_time_limit"]){
	$show_time_limit	=	$configutil->splash_new($_POST["show_time_limit"]);
}
if($_POST["city_name"]){
	$city_name	=	$configutil->splash_new($_POST["city_name"]);
}
if($_POST["start_time"]){
	$start_time	=	$configutil->splash_new($_POST["start_time"]);
}
if($_POST["end_time"]){
	$end_time	=	$configutil->splash_new($_POST["end_time"]);
}
if($_POST["province"]){
	$province	=	$configutil->splash_new($_POST["province"]);
}
if($_POST["sel_link_type"]){
	$sel_link_type	=	$configutil->splash_new($_POST["sel_link_type"]);
}
if($_POST["link"]){
	$link_str	=	$configutil->splash_new($_POST["link"]);
}
if($_POST["op"]){
	$op	=	$configutil->splash_new($_POST["op"]);
}

if($_POST["select_package_value"]){
	$select_package_value	=	$configutil->splash_new($_POST["select_package_value"]);
}

if($_POST["shop_type"]){
	$shop_type	=	$configutil->splash_new($_POST["shop_type"]);
}

if($_POST["sort_type"]){
	$sort_type	=	$configutil->splash_new($_POST["sort_type"]);
}

if($_POST["divide_type"]){
	$divide_type	=	$configutil->splash_new($_POST["divide_type"]);
}
// 数据监测
// if( $_REQUEST['model_type']==15 && mb_strlen($placeholder)>36 ){
// 	// $count = mb_strlen($placeholder);
// 	echo json_encode("文字不能超过36个字节");exit;
// }

//sz_zpq
$query_bargain="select id from bargain.kj_activity where isvalid=true and customer_id=".$customer_id." ORDER BY create_time desc limit 1";
$result_bargain = _mysql_query($query_bargain) or die('Query_bargain failed: ' . mysql_error());
while ($row = mysql_fetch_object($result_bargain)) {
	$bargain_id = $row->id;
}
$query_crowdfund="select id from crowdfund.cr_activity where isvalid=true and status=2 and customer_id=".$customer_id." ORDER BY create_time desc limit 1";
$result_crowdfund = _mysql_query($query_crowdfund) or die('Query_crowdfund failed: ' . mysql_error());
while ($row = mysql_fetch_object($result_crowdfund)) {
	$crowdfund_id = $row->id;
}
//sz_zpq

//处理图片链接
if($foreign_id>0)
{
	$select_value=$foreign_id;
	$foreignarr=explode("_",$foreign_id);
	$foreign_id=$foreignarr[0];
	//线下商城如果选择的是'1_16'，即为选择全部，$foreign_id取-1
	if ($foreignarr[1] == 16){
		$foreign_id = -1;
	}
}

$selectarr[]="";
$detailvaluearr[]="";
$detailnamearr[]="";
$sel_link_type_arr[]="";

if($select_value){ //创建连接
	//$type_id_2	=	$configutil->splash_new($_POST["type_id_2"]);
	$link1[]="";
	$selectarr=explode("|",$select_value);
	$packagearr=explode("|",$select_package_value);
	$detailvaluearr=explode("|",$detail_value);
	$sel_link_type_arr=explode("|",$sel_link_type);
	$link_arr=explode("|",$link_str);
	// echo count($selectarr);exit;
	for($i=0;$i<count($selectarr)-1;$i++){
		$link1[$i] = 'javascript:';
		if( $sel_link_type_arr[$i] == 1 ){
			if($selectarr[$i]>=0){
				$typestrarr= explode("_",$selectarr[$i]);
				$type_id_2 = $typestrarr[0];
				$link_type=$typestrarr[1];
				$type_id_3=$typestrarr[2];

				if($link_type==1){
					$product_detail_id_2 = $detailvaluearr[$i];
					if($product_detail_id_2>0){
						$link1[$i]="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_2;
					}else{
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_2;
						$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$tcount = 0;	//子分类数量
						$query_type = "SELECT count(1) as tcount FROM weixin_commonshop_types WHERE customer_id=".$customer_id." AND parent_id=".$type_id_2." AND is_shelves=1 AND isvalid=true";
						$result_type = _mysql_query($query_type) or die('Query_type failed:'.mysql_error());
						while( $row_type = mysql_fetch_object($result_type) ){
							$tcount = $row_type -> tcount;
						}
						if( $tcount > 0 ){
							$link1[$i]="../../mshop/proclass.php?customer_id=".$customer_id_en."&tid=".$type_id_2;
						}else{
							$link1[$i]="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$type_id_2;
						}
					}
				}else if($link_type==2){
				   //图文
					$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_2;
					$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?");
					$pos2 = strpos($website_url,"single_id");
					if( $pos2 > 0 ){	//微官网单页链接
						$website_url = $website_url."&C_id=".$customer_id_en;
					} else {
						// if($pos>0){
						//    $website_url = $website_url."&customer_id=".$customer_id_en;
						// }else{
						//    $website_url = $website_url."?customer_id=".$customer_id_en;
						// }
					}
					$link1[$i] = $website_url;
				}else if($link_type==3){
				   //城市商圈-美食
					$link1[$i] = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_2;
				}else if($link_type==4){
				   //商圈行业列表
					switch($type_id_3){
						case 0:
							$link1[$i] = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
							break;
						case 1:
							$link1[$i] = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
							break;
						case 2:
							$link1[$i] = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
							break;
						case 3:
							$link1[$i] = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
							break;
						case 4:
							$link1[$i] = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
							break;
						case 5:
							$link1[$i] = "../../city_area/finance2/loan/loanList.php?customer_id=".$customer_id_en;
							break;
						case 6:
							$link1[$i] = "../../city_area/finance2/credit/index.php?customer_id=".$customer_id_en;
							break;
						case 7:
							$link1[$i] = "../../city_area/finance2/insurance/insurance_list.php?customer_id=".$customer_id_en;
							break;
						case 8:
							$link1[$i] = "../../../addons/index.php/coach/Index/coach_index?customer_id=".$customer_id_en;
							break;
						case 9:
							$link1[$i] = "/weixinpl/yiren/front/web/index.html?customer_id_en=".$customer_id_en;
							break;
					}
				}else if($link_type==5){
				   //品牌供应商
					$link1[$i] = "../../mshop/my_store/my_store.php?customer_id=".$customer_id_en."&supplier_id=".$type_id_2;
				}else if($link_type==6){
				   //城市商圈-ktv
					$link1[$i] = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_2;
				}else if($link_type==7){
				   //城市商圈-酒店
					$link1[$i] = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_2;
				}else if($link_type==8){
				   //城市商圈-线下商城
					$link1[$i] = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_2;
				}else if($link_type==60){
				   //优惠券
					if($type_id_2==0 or empty($type_id_2)){
						$link1[$i] = "../../mshop/coupons_center.php?customer_id=".$customer_id_en;
					}else{
						$link1[$i] = "../../mshop/coupons_center.php?customer_id=".$customer_id_en."&cp_id=".$type_id_2;
					}
				}else if($link_type==9){
				   //微视直播系统
					$link1[$i] = "../../../weixin/plat/app/index.php/Mshopzhibo/show_room/customer_id/".$customer_id."/room_id/".$type_id_2;
				}else if($link_type==10){
				   //已启用的模板
					$link1[$i] = "index.php?customer_id=".$customer_id_en."&diy_template_id=".$type_id_2;
				}


			}else{
				switch($selectarr[$i]){
					case -6:
						$link1[$i]="../../mshop/list.php?customer_id=".$customer_id_en;
						break;
					case -2:
						$link1[$i]="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
						break;
					case -3:
						$link1[$i]="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
						break;
					case -4:
						$link1[$i]="../../mshop/order_cart.php?customer_id=".$customer_id_en;
						break;
					case -7:
						$link1[$i]="../../mshop/class_page.php?customer_id=".$customer_id_en;
						break;
					case -8:
						$link1[$i]="../../mshop/personal_center.php?customer_id=".$customer_id_en;
						break;
					case -9:
						$link1[$i]="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
						break;
					case -5:
						$link1[$i]="../../mshop/snap_up.php?customer_id=".$customer_id_en;
						break;
					case -33:
						$link1[$i]="../../mshop/wholesalers_list.php?customer_id=".$customer_id_en;
						break;
					case -10:
						$link1[$i]="../../online/show_online.php?customer_id=".$customer_id_en;
						break;
					case -11:
						$typestrarr= explode("_",$selectarr[$i]);
						$type_id_2 = $typestrarr[0];
						$link_type=$typestrarr[1];
						if($link_type){
							$link1[$i]="../../mshop/product_detail_gift.php?package_id={$link_type}";
						}else{
							$link1[$i]="../../mshop/package_list.php?customer_id=".$customer_id_en;
						}
						break;
					case -12:
						$link1[$i]="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en;
						break;
					case -15:
						$link1[$i]="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;
						break;
					case -16:
						$link1[$i]="index.php?customer_id=".$customer_id_en;
						break;
					case -17:
						$link1[$i]="../../mshop/proclass.php?customer_id=".$customer_id_en;
						break;
					case -18:
						$link1[$i]="../../mshop/orderlist.php?customer_id=".$customer_id_en;
						break;
					case -19:
						$link1[$i]="/market/web/collageActivities/product_list_view.php?customer_id=".$customer_id_en."&op=ordinary";
						break;
					case -20:
						$link1[$i]="/market/web/collageActivities/product_list_view.php?customer_id=".$customer_id_en."&op=popularity";
						break;
					case -21:
						$link1[$i]="/market/web/promoter_renew/index.php?customer_id=".$customer_id_en;
						break;
					case -22:
						$link1[$i]="/addons/index.php/micro_broadcast/user/index?customer_id=".$customer_id_en;
						break;
					case -23:
						$link1[$i]="/addons/index.php/voice_online/Index/index?customer_id=".$customer_id_en;
						break;
					case -24:
						$link1[$i]="/weixinpl/ticke_check.php?type=flight";
						break;
					case -25:
						$link1[$i]="/weixinpl/ticke_check.php?type=train";
						break;
					case -26:
						$link1[$i]="/addons/index.php/f2c/index/personal_center?customer_id=".$customer_id_en;
						break;
                    case -27:
                        $link1[$i]="/addons/index.php/ordering_retail/Proxy/proxy_login?customer_id=".$customer_id_en;
                        break;
                    case -28:
                        $link1[$i]="/addons/index.php/ordering_retail/Proxy/proxy_apply?customer_id=".$customer_id_en;
                        break;
                    case -29:
                        $link1[$i]="/addons/index.php/ordering_retail/Proxy/personal_center.html?customer_id=".$customer_id_en;
                        break;
                    case -30:
                        $link1[$i]="/market/web/collageActivities/product_list_view.php?op=ordinary2&customer_id=".$customer_id_en;
                        break;
                    case -31:
                        $link1[$i]="/market/web/collageActivities/product_list_view.php?op=ordinary3&customer_id=".$customer_id_en;
                        break;
                    case -34:
                        $link1[$i]=$model_integral->integral_sign_url.$customer_id_en;
                        break;
                    case -35:
                        $link1[$i]=$model_integral->integral_shop_url.$customer_id_en;
                        break;
					case -37:
						$link1[$i]="../../mshop/class_page3.php?customer_id=".$customer_id_en;
						break;
					case -47:
						$link1[$i]="../../mshop/class_page4.php?customer_id=".$customer_id_en;
						break;
					case -95:
					    $link1[$i]="/market/web/haggling/web/index.html?customer_id_en=".$customer_id_en."&activity_id=".$bargain_id;
					    break;
					case -96:
						//$link1[$i]="/weixinpl/sustain/front/web/index.html?customer_id_en=".$customer_id_en."&activity_id=".$crowdfund_id;
						$link1[$i]="/weixinpl/sustain/back/index.php/Workroom_admin/crowdfund/index_list.html?customer_id_en=".$customer_id_en;
						break;
					case -100:
                        $link1[$i]="/addons/index.php/ordering_retail/Proxy/apply_shop.html?customer_id=".$customer_id_en;
                        break;
                    case -101:
                        $link1[$i]="/addons/index.php/ordering_retail/Shop/nearby_shop.html?customer_id=".$customer_id_en;
                        break;
                    case -102:
                        $link1[$i]="/addons/index.php/ordering_retail/Shop/shop_list.html?customer_id=".$customer_id_en;
                        break;
					default:
						$link1[$i]="javascript:";
						break;
				}
			}
		} else {
			if( $link_arr[$i] == '' ){
				$link1[$i] = "javascript:";
			} else {
				$link1[$i] = $link_arr[$i];
			}
		}
		$link=implode("|",$link1);
		//$link=count($selectarr);



	}

	//$link=$link1;
	//$str->msg=$selectarr;
	//$str->code=$link;
	//echo json_encode($str);
	//return;


}


if($op=="add_mod"){

	//插入一个新模块
	$add_mod="insert into weixin_commonshop_diy_template_content (diy_temid,type,diy_tem_contid,title,imgurl,link,color,foreign_id,detail_id,video_link,mod_padding,mod_img_padding,css_type,pro_title_show,pro_title_twoline,pro_numshow,foot_position,placeholder,show_sale,link_type,creatime,isvalid,customer_id,rolling_direction,rolling_speed,show_time_limit,city_name,start_time,end_time,province,sel_link_type,shop_type,sort_type,divide_type,select_value) values ('".$diy_temid."','".$type."','".$diy_tem_contid."','".$title."','".$imgurl."','".$link_str."','".$color."','".$foreign_id."','".$detail_id."','".$video_link."','".$mod_padding."','".$mod_img_padding."','".$css_type."',true,true,'".$pro_numshow."','".$foot_position."','".$placeholder."',true,'".$link_type."',now(),true,'".$customer_id."','".$rolling_direction."','".$rolling_speed."','".$show_time_limit."','".$city_name."','".$start_time."','".$end_time."','".$province."','".$sel_link_type."',".$shop_type.",".$sort_type.",".$divide_type.",'".$select_value."')";
	//$str->msg=$add_mod;
	//echo json_encode($str);
	$result_add_mod=_mysql_query($add_mod) or die ('add_mod failed' .mysql_error());

}

if($op=="del_mod"){
	//删除一个新模块
	$del_mod="update weixin_commonshop_diy_template_content set isvalid=false where diy_tem_contid=".$diy_tem_contid." and customer_id='".$customer_id."'";
	$result_del_mod=_mysql_query($del_mod) or die ('del_mod failed' .mysql_error());
}
if($op=="update_mod"){
	//更新一个新模块

	$update_mod="update weixin_commonshop_diy_template_content set
				title='".$title."',
				link='".$link."',
				color='".$color."',
				search_color='".$search_color."',
				foreign_id=".$foreign_id.",
				detail_id=".$detail_id.",
				video_link='".$video_link."',
				mod_padding=".$mod_padding.",
				mod_img_padding=".$mod_img_padding.",
				css_type='".$css_type."',
				imgurl='".$imgurl."',
				pro_title_show=".$pro_title_show.",
				pro_title_twoline=".$pro_title_twoline.",
				pro_numshow=".$pro_numshow.",
				foot_position=".$foot_position.",
				placeholder='".$placeholder."',
				show_sale=".$show_sale.",
				link_type='".$link_type."',
				select_value='".$select_value."',
				detail_value='".$detail_value."',
				detail_name='".$detail_name."',
				rolling_direction='".$rolling_direction."',
				rolling_speed='".$rolling_speed."',
				show_time_limit='".$show_time_limit."',
				city_name='".$city_name."',
				start_time='".$start_time."',
				end_time='".$end_time."',
				province='".$province."',
				sel_link_type='".$sel_link_type."',
				shop_type=".$shop_type.",
				sort_type=".$sort_type.",
				divide_type=".$divide_type."
				where diy_tem_contid=".$diy_tem_contid." and customer_id='".$customer_id."'";
				// echo $update_mod;exit;
	// $str->msg=$update_mod;
	// echo json_encode($str);
	$result_update_mod=_mysql_query($update_mod) or die ('update_mod failed' .mysql_error());

	// $update_mod = str_replace(array("\r\n", "\r", "\n"), "", $update_mod);

	$query_log = "INSERT INTO weixin_commonshop_diy_template_content_log (
							customer_id, operation_name, content ,createtime
						) VALUES (
							{$customer_id}, '{$_SESSION['curr_login']}', '".mysql_real_escape_string($update_mod)."', now()
						)";
	_mysql_query($query_log);
}


if($op=="save_mod"){
	//保存模块

	$save_mod="update weixin_commonshop_diy_template set content='".$content."',name='".$name."',bgcolor='".$bgcolor."' where id='".$diy_temid."' and  customer_id='".$customer_id."' and isvalid=true ";
	$result_save_mod=_mysql_query($save_mod) or die ('save_mod failed' .mysql_error());
	$str->code=1;
	echo json_encode($str);
}

mysql_close($link);
?>