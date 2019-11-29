<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');

$new_baseurl = Protocol.$http_host; 

$diy_temid			= -1; 		//保存diy_template表的ID
$diy_tem_contid		= -1; 		//模块的ID
$type				= -1; 		//模块类型
$title				= ""; 		//文字标题
$title_en			= ""; 		//英文标题
$mod_describe		= ""; 		//模块描述
$mod_padding		= 0; 		//模块间距
$imgurl				= array(); 	//图片地址
$pic_title			= array(); 	//图片标题
$nav_title			= '';		//导航标题
$is_show			= '';		//是否显示
$link_type			= array();	//链接类型
$select_value		= array();	//链接选择的值
$detail_value		= array();	//选择产品的id
$start_time			= array();	//开始时间
$end_time			= array();	//结束时间
$nav_css_type		= '';		//导航样式
$floor_number		= '';		//楼层专区的楼层号
$floor_id_arr		= array();	//楼层id
$floor_number_arr	= array();	//楼层号
$floating_floor		= 0;		//浮动楼层开关
$css_type			= 1;		//模块样式
$pro_name_show		= 0;		//是否显示产品名
$pro_num_show		= 1;		//产品显示数量
$show_sale			= 0;		//是否显示销量
$custom_type        = 1;        //模板所属页面

if($_POST["diy_temid"]){
	$diy_temid = $configutil->splash_new($_POST["diy_temid"]);	
}
if($_POST["diy_tem_contid"]){
	$diy_tem_contid	= $configutil->splash_new($_POST["diy_tem_contid"]);	
}
if($_POST["type"]){
	$type =	$configutil->splash_new($_POST["type"]);	
}
if($_POST["title"]){
	$title = $configutil->splash_new($_POST["title"]);	
}
if($_POST["title_en"]){
	$title_en = $configutil->splash_new($_POST["title_en"]);	
}
if($_POST["mod_describe"]){
	$mod_describe = $configutil->splash_new($_POST["mod_describe"]);	
}
if($_POST["mod_padding"]){
	$mod_padding = $configutil->splash_new($_POST["mod_padding"]);	
}
if($_POST["imgurl"]){
	$imgurl = $_POST["imgurl"];	//不过滤
	// $imgurl = $configutil->splash_new($_POST["imgurl"]);
	// $imgurl = str_replace($new_baseurl,'',$imgurl); 
}
// var_dump($_POST["imgurl"]);
if($_POST["pic_title"]){
	$pic_title = $_POST["pic_title"];	//不过滤
	// $pic_title = $configutil->splash_new($_POST["pic_title"]);	
}
if($_POST["nav_title"]){
	$nav_title = $configutil->splash_new($_POST["nav_title"]);	
}
if($_POST["is_show"]){
	$is_show = $configutil->splash_new($_POST["is_show"]);	
}
if($_POST["name"]){
	$name = $configutil->splash_new($_POST["name"]);	
}
if($_POST["content"]){
	$content = $configutil->splash_new($_POST["content"]);	
}
if($_POST["bgcolor"]){
	$bgcolor = $configutil->splash_new($_POST["bgcolor"]);	
}
if($_POST["link_type"]){
	$link_type = $_POST["link_type"];	//不过滤
	// $link_type = $configutil->splash_new($_POST["link_type"]);	
}
if($_POST["select_value"]){
	$select_value = $_POST["select_value"];	//不过滤
	// $select_value = $configutil->splash_new($_POST["select_value"]);	
}
if($_POST["detail_value"]){
	$detail_value = $_POST["detail_value"];	//不过滤
	// $detail_value = $configutil->splash_new($_POST["detail_value"]);	
}
if($_POST["start_time"]){
	$start_time = $_POST["start_time"];	//不过滤
	// $detail_value = $configutil->splash_new($_POST["detail_value"]);	
}
if($_POST["end_time"]){
	$end_time = $_POST["end_time"];	//不过滤
	// $detail_value = $configutil->splash_new($_POST["detail_value"]);	
}
if($_POST["nav_css_type"]){
	$nav_css_type = $configutil->splash_new($_POST["nav_css_type"]);	
}
if($_POST["floor_number"]){
	$floor_number = $configutil->splash_new($_POST["floor_number"]);	
}
if($_POST["floor_id_arr"]){
	$floor_id_arr = $_POST["floor_id_arr"];	//不过滤	
}
if($_POST["floor_number_arr"]){
	$floor_number_arr = $_POST["floor_number_arr"];	//不过滤
}
if($_POST["floating_floor"]){
	$floating_floor = $configutil->splash_new($_POST["floating_floor"]);	
}
if($_POST["custom_type"]){
	$custom_type = $configutil->splash_new($_POST["custom_type"]);	
}
if($_POST["css_type"]){
	$css_type = $configutil->splash_new($_POST["css_type"]);	
}
if($_POST["pro_name_show"]){
	$pro_name_show = $configutil->splash_new($_POST["pro_name_show"]);	
}
if($_POST["pro_num_show"]){
	$pro_num_show = $configutil->splash_new($_POST["pro_num_show"]);	
}
if($_POST["show_sale"]){
	$show_sale = $configutil->splash_new($_POST["show_sale"]);	
}
if($_POST["op"]){
	$op	= $configutil->splash_new($_POST["op"]);	
}
if($_POST["link"]){
	$link_url = $_POST["link"];	//不过滤
}
$supply_id = -1;
if($_POST["supply_id"]){
	$supply_id = $configutil->splash_new($_POST["supply_id"]);	
}

//整理链接
$link_len = count($link_url);
for( $i = 0; $i < $link_len; $i++ ){
	$link_arr 			= explode("|",$link_url[$i]);
	$link_type_arr 		= explode("|",$link_type[$i]);
	$select_value_arr 	= explode("|",$select_value[$i]);
	$detail_value_arr 	= explode("|",$detail_value[$i]);
	$link_arr_len 		= count($link_arr);
	

	$link_url_re = '';	//重新整理的链接
	
	for( $j = 0; $j < $link_arr_len; $j++ ){
		$select_value_arr_arr	= explode("_",$select_value_arr[$j]);
		$select_value_arr[$j] 	= $select_value_arr_arr[0];
		switch( $link_type_arr[$j] ){
			//自定义链接
			case 1:
				$link_arr[$j] = mysql_real_escape_string($link_arr[$j]);
				break;
			//固定链接
			case 2:
				switch( $select_value_arr[$j] ){
					case -1:  //无
						$link_arr[$j] = "";
					break;
					case -2:  //首页
						$link_arr[$j] = "/shop/index.php/Home/Index/index";
					break;
					case -3:  //全部产品
						$link_arr[$j] = "/shop/index.php/Home/Product/ProductList";
					break;
					case -4:  //购物车
						$link_arr[$j] = "/shop/index.php/Home/Cart/order_cart";
					break;
					case -5:  //个人中心
						$link_arr[$j] = "/shop/index.php/Home/My/index";
					break;
					case -6:  //我的订单
						$link_arr[$j] = "/shop/index.php/Home/My/orderList";
					break;
					case -7:  //我的微店
						$link_arr[$j] = "/shop/index.php/Home/MyShop/index";
					break;
					case -8:  //旗舰店产品分类页
						$link_arr[$j] = "/shop/index.php/Home/MyStore/myStoreList";
					break;
					case -9:  //限时抢购
						$link_arr[$j] = "/shop/index.php/Home/Qiang/index";
					break;
					case -10:  //礼包列表
						if( $select_value_arr_arr[1] ){
							$link_arr[$j] = "/shop/index.php/Home/Package/detail/package_id/{$select_value_arr_arr[1]}";
						}else{
							$link_arr[$j] = "/shop/index.php/Home/Package/index";
						}
					break;
					case -11:  //积分专区
						$link_arr[$j] = "/shop/index.php/Home/ScoreShop/index";
					break;
					
				}
				break;
			//产品分类链接
			case 3:
				if( $detail_value_arr[$j] > 0 ){
					//产品详情页
					$link_arr[$j] = "/shop/index.php/Home/Detail/index/product_id/".$detail_value_arr[$j];
				} else {
					//产品分类页
					$link_arr[$j] = "/shop/index.php/Home/Product/ProductList/type_id/".$select_value_arr[$j];
				}
				break;
			//品牌供应商链接
			case 4:
				$link_arr[$j] = "/shop/index.php/Home/MyStore/index/supplier_id/".$select_value_arr[$j];
				break;
			//其他模板链接
			case 5:
				//if( $supply_id > 0 ){	//品牌供应商
				//	$link_arr[$j] = "/shop/index.php/Home/MyStore/index/tem_id/".$select_value_arr[$j];
				//} else {
					//平台
					$link_arr[$j] = "/shop/index.php/Home/Product/ActivityPage/tem_id/".$select_value_arr[$j];
				//}
				
				break;
			//品牌供应商产品分类链接
			case 6:
				if( $detail_value_arr[$j] > 0 ){
					//产品详情页
					$link_arr[$j] = "/shop/index.php/Home/Detail/index/product_id/".$detail_value_arr[$j];
				} else {
					//产品分类页
					$link_arr[$j] = "/shop/index.php/Home/Product/ProductList/type_id/".$select_value_arr[$j];
				}
				break;
			//微视直播系统	
			case 7:
				$link_arr[$j] = "/weixin/plat/app/index.php/Mshopzhibo/show_room/customer_id/".$customer_id."/room_id/".$detail_value_arr[$j];
				break;
			default:
				$link_arr[$j] = "";
		}
		
		$link_url_re .= $link_arr[$j]."|";
	}
	
	$link_url[$i] = substr($link_url_re,0,-1);	//获取整理后的链接

}

if( $op == "add_mod" ){
	//插入一个新模块
	$add_mod = "INSERT INTO pcshop_diy_template_content(
						customer_id,
						diy_temid,
						diy_tem_contid,
						type,
						title,
						title_en,
						mod_describe,
						mod_padding,
						nav_title,
						is_show,
						nav_css_type,
						floor_number,
						css_type,
						pro_name_show,
						pro_num_show,
						show_sale,
						isvalid,
						createtime
						) VALUES (
						".$customer_id.",
						".$diy_temid.",
						'".$diy_tem_contid."',
						".$type.",
						'".$title."',
						'".$title_en."',
						'".$mod_describe."',
						".$mod_padding.",
						'".$nav_title."',
						'".$is_show."',
						'".$nav_css_type."',
						'".$floor_number."',
						'".$css_type."',
						'".$pro_name_show."',
						'".$pro_num_show."',
						'".$show_sale."',
						true,
						now()
						)";
	
	$result_add_mod = _mysql_query($add_mod) or die ('Add_mod failed' .mysql_error());

	$add_mod_detail = 'INSERT INTO pcshop_diy_template_content_detail(
								content_id,
								position,
								imgurl,
								link,
								link_type,
								select_value,
								detail_value,
								isvalid,
								createtime,
								title,
								start_time,
								end_time,
								customer_id
								) VALUES ';
		
	$len = count($imgurl);
	$content_detail = '';
	for ( $i = 0; $i < $len; $i++ ){
		$content_detail .= "('".$diy_tem_contid."',
								".$i.",
								'".$imgurl[$i]."',
								'".$link_url[$i]."',
								-1,
								-1,
								-1,
								true,
								now(),
								'".$pic_title[$i]."',
								'".$start_time[$i]."',
								'".$end_time[$i]."',
								".$customer_id."
								),";
	}
	$content_detail = substr($content_detail,0,-1);
	if ( $content_detail != '' ){
		$add_mod_detail .= $content_detail;
		// echo $add_mod_detail;
		_mysql_query($add_mod_detail) or die('Add_mod_detail failed:'.mysql_error());
	}
	
}

if( $op == "del_mod" ){
	//删除一个新模块
	$del_mod = "update pcshop_diy_template_content set isvalid=false where diy_tem_contid='".$diy_tem_contid."' and customer_id=".$customer_id;
	
	$result_del_mod = _mysql_query($del_mod) or die ('del_mod failed' .mysql_error());
	
	//删除模块的详细内容
	$del_mod_detail = "UPDATE pcshop_diy_template_content_detail SET isvalid=false WHERE content_id='".$diy_tem_contid."'";
	
	_mysql_query($del_mod_detail) or die ('Del_mod_detail failed' .mysql_error());
}
if( $op == "update_mod" ){
	//更新模块
	$update_mod = "UPDATE pcshop_diy_template_content SET 
						title='".$title."',
						title_en='".$title_en."',
						mod_describe='".$mod_describe."',
						mod_padding='".$mod_padding."',
						nav_title='".$nav_title."',
						is_show='".$is_show."',
						nav_css_type='".$nav_css_type."',
						css_type=".$css_type.",
						pro_name_show=".$pro_name_show.",
						pro_num_show=".$pro_num_show.",
						show_sale=".$show_sale."
					WHERE diy_tem_contid='".$diy_tem_contid."' AND customer_id=".$customer_id;		
	
	_mysql_query($update_mod) or die ('Update_mod failed' .mysql_error());
	
	//更新模块详细内容
	$len = count($imgurl);
	for ( $i = 0; $i < $len; $i++ ){
		$update_mod_detail = "UPDATE pcshop_diy_template_content_detail SET 
								imgurl='".$imgurl[$i]."',
								link_type='".$link_type[$i]."',
								link='".$link_url[$i]."',
								select_value='".$select_value[$i]."',
								detail_value='".$detail_value[$i]."',
								title='".$pic_title[$i]."',
								start_time='".$start_time[$i]."',
								end_time='".$end_time[$i]."'
							WHERE content_id='".$diy_tem_contid."' AND position=".$i;			
		_mysql_query($update_mod_detail) or die ('Update_mod_detail failed' .mysql_error());
	}
	
}

if( $op == "save_mod" ){
	//保存模块
	
	$save_mod = "update pcshop_diy_template set content='".$content."',name='".$name."',bgcolor='".$bgcolor."',floating_floor=".$floating_floor.",custom_type=".$custom_type." where id='".$diy_temid."' and  customer_id='".$customer_id."' and isvalid=true and supply_id=".$supply_id;
	$result_save_mod = _mysql_query($save_mod) or die ('save_mod failed' .mysql_error());
	
	//更新楼层号
	$floor_id_len = count($floor_id_arr);
	if ( $floor_id_len > 0 ){
		for ( $i = 0; $i < $floor_id_len; $i++ ){
			$update_floor = "UPDATE pcshop_diy_template_content SET floor_number=".$floor_number_arr[$i]." WHERE diy_tem_contid=".$floor_id_arr[$i];
			_mysql_query($update_floor) or die('Update_floor failed:'.mysql_error());
		}
	}
	
	$str->code = 1;
	echo json_encode($str);
}

mysql_close($link);
?>