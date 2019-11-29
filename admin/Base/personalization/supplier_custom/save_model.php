<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../../weixinpl/proxy_info.php');  /*fenxiao下链接出错 11.13 by cdr*/
$new_baseurl = Protocol.$http_host;
$diy_temid			=-1; //保存diy_template表的ID
$diy_tem_contid		=-1; //模块的ID
$type				=-1; //模块类型
$title				=""; //文字标题
$imgurl				=""; //图片地址
$link				="#";
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
$supplier_id		="";//供应商ID


if($_POST["diy_temid"]){
	$diy_temid	=	$configutil->splash_new($_POST["diy_temid"]);	
}
if($_POST["supplier_id"]){
	$supplier_id	=	$configutil->splash_new($_POST["supplier_id"]);	
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
if($_POST["op"]){
	$op	=	$configutil->splash_new($_POST["op"]);	
}

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
if($select_value){ //创建连接
	//$type_id_2	=	$configutil->splash_new($_POST["type_id_2"]);
	$link1[]="";
	$selectarr=explode("|",$select_value);
	$detailvaluearr=explode("|",$detail_value);
	for($i=0;$i<count($selectarr)-1;$i++){
		if($selectarr[$i]>0 || $selectarr[$i] == -1){
			$typestrarr= explode("_",$selectarr[$i]);
			$type_id_2 = $typestrarr[0];
			$link_type=$typestrarr[1];
			if($link_type==1){
				$product_detail_id_2 = $detailvaluearr[$i];        
				if($product_detail_id_2>0){
					$link1[$i]="../product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_2;
				}else{										
					$link1[$i]="../list.php?customer_id=".$customer_id_en."&brand_typeid=".$type_id_2."&supply_id=".$supplier_id."";
				}
			}else if($link_type==2){
                //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_2;
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$link1[$i] = $website_url;
			}else if($link_type==3){
                //城市商圈-美食
				$link1[$i] = "../../city_area/cater/shop.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_2;
			}else if($link_type==4){
                //商圈行业列表
				$link1[$i] = "../../common_shop/jiushop/cityarea_list.php?customer_id=".$customer_id_en."&supply_type=".$type_id_2;
			}	
			
			
		}else{
			switch($selectarr[$i]){
				case -1:
					$product_detail_id_2 = $detailvaluearr[$i];        
					if($product_detail_id_2>0){
						$link1[$i]="../product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_2;
					}else{										
						$link1[$i]="../list.php?customer_id=".$customer_id_en."&brand_typeid=".$selectarr[$i]."&supply_id=".$supplier_id."";
					}
					break;
				case -6:
					$link1[$i]=="../list.php?customer_id=".$customer_id_en;
					break;
				case -2:
					$link1[$i]=="../list.php?isnew=1&customer_id=".$customer_id_en;
					break;
				case -3:
					$link1[$i]=="../list.php?ishot=1&customer_id=".$customer_id_en;
					break;
				case -4:
					$link1[$i]="../order_cart.php?customer_id=".$customer_id_en;
					break;
				case -7:
					$link1[$i]="../class_page.php?customer_id=".$customer_id_en;
					break;
				case -8:
					$link1[$i]="../personal_center.php?customer_id=".$customer_id_en;
					break;
				case -9:
					$link1[$i]="index.php?customer_id=".$customer_id_en;
					break;
				case -5:
					$link1[$i]="../snap_up.php?customer_id=".$customer_id_en; 		
					break;
				case -10:
					$link1[$i]="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					break;	
				case -11:
					$link1[$i]="../packages_list.php?customer_id=".$customer_id_en; 	  
					break;
				case -12:
					$link1[$i]=="../list.php?isvp=1&customer_id=".$customer_id_en; 	  
					break;
				default:
					$link1[$i]="javascript:";
					break;	
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
	$add_mod="insert into weixin_commonshop_supply_diy_template_content (diy_temid,type,diy_tem_contid,title,imgurl,link,color,foreign_id,detail_id,video_link,mod_padding,mod_img_padding,css_type,pro_title_show,pro_title_twoline,pro_numshow,foot_position,placeholder,show_sale,link_type,creatime,isvalid,customer_id,supplier_id) values ('".$diy_temid."','".$type."','".$diy_tem_contid."','".$title."','".$imgurl."','".$link."','".$color."','".$foreign_id."','".$detail_id."','".$video_link."','".$mod_padding."','".$mod_img_padding."','".$css_type."',true,true,'".$pro_numshow."','".$foot_position."','".$placeholder."',true,'".$link_type."',now(),true,'".$customer_id."','".$supplier_id."')";
	//$str->msg=$add_mod;
	//echo json_encode($str);
	$result_add_mod=_mysql_query($add_mod) or die ('add_mod faild' .mysql_error());
	
}

if($op=="del_mod"){
    //删除一个新模块
	$del_mod="update weixin_commonshop_supply_diy_template_content set isvalid=false where diy_tem_contid=".$diy_tem_contid." and customer_id='".$customer_id."' and supplier_id='".$supplier_id."'";
	$result_del_mod=_mysql_query($del_mod) or die ('del_mod faild' .mysql_error());
}
if($op=="update_mod"){
    //更新一个新模块
	$update_mod="update weixin_commonshop_supply_diy_template_content set 
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
				detail_name='".$detail_name."'
				where diy_tem_contid=".$diy_tem_contid." and customer_id='".$customer_id."' and supplier_id='".$supplier_id."'";
	//$str->msg=$update_mod;
	//echo json_encode($str);				
	$result_update_mod=_mysql_query($update_mod) or die ('update_mod faild' .mysql_error());
}
if($op=="save_mod"){
    //保存模块
	
	$save_mod="update weixin_commonshop_supply_diy_template set content='".$content."',name='".$name."',bgcolor='".$bgcolor."' where id='".$diy_temid."' and  customer_id='".$customer_id."' and  supplier_id='".$supplier_id."' and isvalid=true ";
	$result_save_mod=_mysql_query($save_mod) or die ('save_mod faild' .mysql_error());
	$str->code=1;
	echo json_encode($str);
}

mysql_close($link);
?>