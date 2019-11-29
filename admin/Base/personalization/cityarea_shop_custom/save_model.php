<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
// require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');  /*fenxiao下链接出错 11.13 by cdr*/
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

$cityarea_id = $_SESSION['city_shop_AcountID'];
$cityarea_id_en = passport_encrypt($_SESSION['city_shop_AcountID']);

//处理图片链接
if($foreign_id>0)
{
	$select_value=$foreign_id;
	$foreignarr=explode("_",$foreign_id);
	$foreign_id=$foreignarr[0];
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
		$link1[$i] = 'javascript:1';
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
				}else if($link_type==3){
				   //城市商圈-美食
					$link1[$i] = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_2;
				}
			}else{
				switch($selectarr[$i]){
					case -10:
						$link1[$i]=$protocol_http_host."/o2o/web/city_area/shop/supply_store.php?customer_id={$customer_id_en}&supply_id={$cityarea_id_en}";
						break;
					case -11:
						$typestrarr= explode("_",$selectarr[$i]);
						$type_id_2 = $typestrarr[0];
						$link_type=$typestrarr[1];
					 	if($link_type){
					 		$link1[$i]=$protocol_http_host."/o2o/web/city_area/shop/product_detail.php?customer_id={$customer_id_en}&product_id={$link_type}&order_supply_id={$cityarea_id_en}";
					 	}else{
					 		$link1[$i]=$protocol_http_host."/o2o/web/city_area/shop/product_list.php?customer_id={$customer_id_en}&supply_id={$cityarea_id_en}";
					 	}
						// $link1[$i]="product_list.php?customer_id={$customer_id_en}&supply_id={$cityarea_id_en}";
						break;
					case -12:
						$link1[$i]=$protocol_http_host."/o2o/web/city_area/shop/store_detail.php?customer_id={$customer_id_en}&supply_id={$cityarea_id_en}";
						// $link1[$i]="store_detail.php?customer_id={$customer_id_en}&supply_id={$cityarea_id_en}";
						break;
					case -13:
						$link1[$i]=$protocol_http_host."/weixinpl/mshop/my_microshop/my_microshop.php?customer_id={$customer_id_en}&owner_id={$cityarea_id}";
						// $link1[$i]="../../mshop/my_microshop/my_microshop.php?owner_id={$cityarea_id}&customer_id={$customer_id_en}";
						break;
					case -14:
						$typestrarr= explode("_",$selectarr[$i]);
						$type_id_2 = $typestrarr[0];
						$link_type=$typestrarr[1];
						$link_type = passport_encrypt($link_type);
						if($link_type){
							$link1[$i]=$protocol_http_host."/o2o/web/city_area/shop/supply_store.php?customer_id={$customer_id_en}&supply_id={$link_type}";
						}else{
							$link1[$i]=$protocol_http_host."/o2o/web/city_area/shop/supply_store_lists.php?customer_id={$customer_id_en}&supply_id={$cityarea_id_en}";
						}
						// $link1[$i]="../../mshop/my_microshop/my_microshop.php?owner_id={$cityarea_id}&customer_id={$customer_id_en}";
						break;
					case -16:
						$link1[$i]=$protocol_http_host."/o2o/web/city_area/shop/order_cart.php?customer_id={$customer_id_en}";
						// $link1[$i]="order_cart.php.php?customer_id={$customer_id_en}";
						break;
					case -17:
						$link1[$i]=$protocol_http_host."/o2o/web/city_area/shop/shop_list.php?customer_id={$customer_id_en}";
						// $link1[$i]="order_cart.php.php?customer_id={$customer_id_en}";
						break;
					case -20:
						$link1[$i]=$protocol_http_host."/weixinpl/mshop/personal_center.php?customer_id={$customer_id_en}";
						// $link1[$i]="../../mshop/personal_center.php?customer_id={$customer_id_en}";
						break;
					default:
						$link1[$i]="javascript:2";
						break;
				}
			}
		} elseif ($sel_link_type_arr[$i] == 3){
			if($selectarr[$i]>=0){
				$typestrarr= explode("_",$selectarr[$i]);
				$type_id_2 = $typestrarr[0];
				$link_type=$typestrarr[1];
				$type_id_3=$typestrarr[2];
				if($link_type==2){
					//某分类产品列表
					$link1[$i];
					$link1[$i]="../../city_area/shop/product_list.php?customer_id=".$customer_id_en."&supply_id={$cityarea_id_en}&tid=".$type_id_2;
				}
			} else if (empty($selectarr[$i])) {
				$link1[$i];
                $link1[$i]="../../city_area/shop/product_list.php?customer_id=".$customer_id_en."&supply_id={$cityarea_id_en}&tid=-1";
			} else {
                $link1[$i];
                $link1[$i]="../../city_area/shop/product_list.php?customer_id=".$customer_id_en."&supply_id={$cityarea_id_en}&tid=".$selectarr[$i];
            }
		} else {
			if( $link_arr[$i] == '' ){
				$link1[$i] = "javascript:4";
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
	$add_mod="insert into weixin_cityarea_diy_template_content (diy_temid,type,diy_tem_contid,title,imgurl,link,color,foreign_id,detail_id,video_link,mod_padding,mod_img_padding,css_type,pro_title_show,pro_title_twoline,pro_numshow,foot_position,placeholder,show_sale,link_type,creatime,isvalid,customer_id,rolling_direction,rolling_speed,show_time_limit,city_name,start_time,end_time,province,sel_link_type) values ('".$diy_temid."','".$type."','".$diy_tem_contid."','".$title."','".$imgurl."','".$link_str."','".$color."','".$foreign_id."','".$detail_id."','".$video_link."','".$mod_padding."','".$mod_img_padding."','".$css_type."',true,true,'".$pro_numshow."','".$foot_position."','".$placeholder."',true,'".$link_type."',now(),true,'".$customer_id."','".$rolling_direction."','".$rolling_speed."','".$show_time_limit."','".$city_name."','".$start_time."','".$end_time."','".$province."','".$sel_link_type."')";
	//$str->msg=$add_mod;
	//echo json_encode($str);
	$result_add_mod=_mysql_query($add_mod) or die ('add_mod failed' .mysql_error());

}

if($op=="del_mod"){
	//删除一个新模块
	$del_mod="update weixin_cityarea_diy_template_content set isvalid=false where diy_tem_contid=".$diy_tem_contid." and customer_id='".$customer_id."'";
	$result_del_mod=_mysql_query($del_mod) or die ('del_mod failed' .mysql_error());
}
if($op=="update_mod"){
	//更新一个新模块

	$update_mod="update weixin_cityarea_diy_template_content set
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
				sel_link_type='".$sel_link_type."'
				where diy_tem_contid=".$diy_tem_contid." and customer_id='".$customer_id."'";
				// echo $update_mod;exit;
	// $str->msg=$update_mod;
	// echo json_encode($str);
	$result_update_mod=_mysql_query($update_mod) or die ('update_mod failed' .mysql_error());
}
// echo $update_mod;

if($op=="save_mod"){
	//保存模块

	$save_mod="update weixin_cityarea_diy_template set content='".$content."',name='".$name."',bgcolor='".$bgcolor."' where id='".$diy_temid."' and  customer_id='".$customer_id."' and isvalid=true ";
	$result_save_mod=_mysql_query($save_mod) or die ('save_mod failed' .mysql_error());
	$str->code=1;
	echo json_encode($str);
}

mysql_close($link);
?>