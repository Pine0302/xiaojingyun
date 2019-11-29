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

_mysql_query("SET NAMES UTF8");
$new_baseurl = Protocol.$http_host; 

$head = 1;  //公共头部文件用到

$diy_temid=-1;
$action="";
$temid="";
$customarr[]="";
$type_arr = array();
$action=$configutil->splash_new($_GET["action"]);
if(isset($_GET["temid"])){
	$temid=$configutil->splash_new($_GET["temid"]);
}

$supply_id = -1;	//供应商id
if( !empty($_GET['supply_id']) && !empty($_SESSION['supplier_Acount']) && empty($_GET['customer_id']) ){
	$supply_id = $_SESSION['supplier_Acount'];
} else if( !empty($_SESSION['supplier_Acount']) && empty($_GET['customer_id']) ) {
	die('操作异常！');
}

switch($action){
	
	case "add":
		$inser_custom = "insert into pcshop_diy_template (customer_id,content,isused,isvalid,createtime,name,supply_id) values ('".$customer_id."','-1',false,true,now(),'自定义模板',".$supply_id.")";
		$result_insert = _mysql_query($inser_custom) or die ('inser_custom faild' .mysql_error());
		$diy_temid = mysql_insert_id();
		
		$query_temid = "select name from pcshop_diy_template where id=".$diy_temid." and isvalid=true and customer_id=".$customer_id." and supply_id=".$supply_id." limit 0,1";
		$result_query_temid = _mysql_query($query_temid) or die ('query_temid faild' .mysql_error());
		while($row = mysql_fetch_object($result_query_temid)){
			$name = $row->name;
		}
		$temid = $diy_temid;
	break;
	case "edit":
		$query_temid = "select id,content,name,bgcolor,floating_floor,custom_type from pcshop_diy_template where id=".$temid." and isvalid=true and customer_id=".$customer_id." and supply_id=".$supply_id." limit 0,1";
		$result_query_temid = _mysql_query($query_temid) or die ('query_temid faild' .mysql_error());
		$custom_type = 1;
		while($row = mysql_fetch_object($result_query_temid)){
			$diy_temid = $row->id;
			$content = $row->content;
			$name = $row->name;
			$bgcolor = $row->bgcolor;
			$custom_type = $row->custom_type;
			$floating_floor = $row->floating_floor;	//浮动楼层插件开关
		}
		$k = 0;
		$floor_num = 0;	//楼层专区数量
		$floor_content = array();
		
		$custom_query = "SELECT diy_tem_contid,type,title,title_en,mod_describe,mod_padding,nav_title,is_show,floor_number,nav_css_type,css_type,pro_name_show,pro_num_show,show_sale FROM pcshop_diy_template_content WHERE isvalid=true AND customer_id=".$customer_id." AND LOCATE(diy_tem_contid,'".$content."') ORDER BY FIND_IN_SET(diy_tem_contid,'".$content."')";
		
		$result_custom = _mysql_query($custom_query) or die ('Custom_query failed' .mysql_error());
		while( $row = mysql_fetch_object($result_custom) ){
			$customarr[$k]['diy_tem_contid'] 	= $row -> diy_tem_contid;	//模块id
			$customarr[$k]['type'] 				= $row -> type;				//模块类型
			$customarr[$k]['title'] 			= $row -> title;			//模块标题
			$customarr[$k]['title_en'] 			= $row -> title_en;			//英文标题
			$customarr[$k]['mod_describe'] 		= $row -> mod_describe;		//模块描述
			$customarr[$k]['mod_padding'] 		= $row -> mod_padding;		//上下内边距
			$customarr[$k]['nav_title'] 		= $row -> nav_title;		//导航标题
			$customarr[$k]['is_show'] 			= $row -> is_show;			//导航显示
			$customarr[$k]['floor_number'] 		= $row -> floor_number;		//模块楼层
			$customarr[$k]['nav_css_type'] 		= $row -> nav_css_type;		//导航样式
			$customarr[$k]['css_type'] 			= $row -> css_type;			//模块样式
			$customarr[$k]['pro_name_show'] 	= $row -> pro_name_show;	//是否显示产品名
			$customarr[$k]['pro_num_show'] 		= $row -> pro_num_show;		//产品显示数量
			$customarr[$k]['show_sale'] 		= $row -> show_sale;		//是否显示销量
			
			if ( $customarr[$k]['type'] == 4 ){	//统计楼层专区数量
				$floor_content[$floor_num]['title'] = $customarr[$k]['title'];
				$floor_content[$floor_num]['floor_number'] = $customarr[$k]['floor_number'];
				$floor_num++;
			}
			
			$content_detail_query = "SELECT position,imgurl,link_type,link,select_value,detail_value,title,start_time,end_time FROM pcshop_diy_template_content_detail WHERE isvalid=true AND content_id=".$customarr[$k]['diy_tem_contid'];
			
			$result_content = _mysql_query($content_detail_query) or die('Content_detail_query failed:'.mysql_error());
			while ( $row_content = mysql_fetch_object($result_content) ){
				$content_position = $row_content -> position;
				$content_detail[$customarr[$k]['diy_tem_contid']][$content_position]['imgurl'] 		 = $row_content -> imgurl;	//图片路径
				$content_detail[$customarr[$k]['diy_tem_contid']][$content_position]['pic_title'] 	 = $row_content -> title;	//标题
				$content_detail[$customarr[$k]['diy_tem_contid']][$content_position]['link_type'] 	 = $row_content -> link_type;	//链接类型
				$content_detail[$customarr[$k]['diy_tem_contid']][$content_position]['link'] 	     = $row_content -> link;	//外部链接网址
				$content_detail[$customarr[$k]['diy_tem_contid']][$content_position]['select_value'] = $row_content -> select_value;	//链接的值
				$content_detail[$customarr[$k]['diy_tem_contid']][$content_position]['detail_value'] = $row_content -> detail_value;	//产品id
				$content_detail[$customarr[$k]['diy_tem_contid']][$content_position]['start_time'] 	 = $row_content -> start_time;	//开始时间
				$content_detail[$customarr[$k]['diy_tem_contid']][$content_position]['end_time'] 	 = $row_content -> end_time;	//结束时间
			}
			
			$k++;
		}

	break;
}

if ( $supply_id > 0 ){
	//固定链接
	$fixedlink[]="-1_---------------请选择---------------";
	$fixedlink[]="-12_店铺首页";
	$fixedlink[]="-4_购物车";
	$fixedlink[]="-5_个人中心";
	$fixedlink[]="-6_我的订单";
	
	$supply_type_arr = array();
	$o_supply_type_arr[] = "-1_-------------请选择------------";
	/* 供应商产品分类 */
	$query = "SELECT id,type_name FROM weixin_commonshop_supply_type WHERE isvalid=true AND customer_id=".$customer_id." AND user_id=".$supply_id." ORDER BY sort_value DESC";
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$pt_id = $row->id;
		$pt_name = $row->type_name;
		
		$pstr = $pt_id."_".$pt_name;
		$supply_type_arr[] = $pt_id."_".$pt_name;
		$o_supply_type_arr[] = $pt_id."_".$pt_name;
	   
	}
	$sql = "select type_ids from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id." and is_supply_id=".$supply_id."";
	$result = _mysql_query($sql) or die('Query failed2: ' . mysql_error());
	$shop_type_id = array();
	while ($row = mysql_fetch_object($result)) {
		 $pt_id = $row->type_ids;
		 $pt_id = trim($pt_id,",");
		 $pt_id = explode(",",$pt_id);
		 foreach($pt_id as $k => $v){
			if(!in_array($v,$shop_type_id) and !empty($v) ){
				$shop_type_id[] = $v;
			}	 
		}
		
	}
	foreach($shop_type_id as $k=>$v){
		$sql_type = "select name from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and id=".$v;
		$result_type = _mysql_query($sql_type) or die('Query failed3: ' . mysql_error());
		while ($row_type = mysql_fetch_object($result_type)) {
			$pt_name = $row_type->name;
		}
		$pstr = $v."_".$pt_name;
		$supply_type_arr[] = $v."_".$pt_name;
	}
	/* 供应商产品分类 */
} else {
	//固定链接
	$fixedlink[]="-1_---------------请选择---------------";
	$fixedlink[]="-2_首页";
	$fixedlink[]="-3_全部产品";
	$fixedlink[]="-4_购物车";
	$fixedlink[]="-5_个人中心";
	$fixedlink[]="-6_我的订单";
	$fixedlink[]="-7_我的微店";
	/* $fixedlink[]="-8_旗舰店产品分类页"; */
	$fixedlink[]="-9_限时抢购";
	$fixedlink[]="-10_礼包列表";
	$fixedlink[]="-11_积分专区";
	
	//品牌供应商店铺
	$brandarr=[];//品牌供应商数据
	$isOpenBrandSupply=0;//是否开启品牌供应商
	$user_id=0;//供应商ID
	$brand_supply_name="";//供应商名称
	$check_brand="select isOpenBrandSupply from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
	$check_brand_result=_mysql_query($check_brand) or die ('check_brand faild ' .mysql_error());
	while($row=mysql_fetch_object($check_brand_result)){
		$isOpenBrandSupply=$row->isOpenBrandSupply;
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

	// 获取礼包列表
	require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
	$database = new \Key\DB();
	$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);

	$sql = "SELECT package_name,id from package_list_t where customer_id='{$customer_id}' and isvalid=true ";
	$package_list = $database->getData($sql);
	foreach ($package_list as $key => $value) {
		$package_lists[] = "{$value['id']}_{$value['package_name']}";
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
	
	//获取选择框链接
	require_once($_SERVER['DOCUMENT_ROOT']."/weixinpl/common/utility_common.php");
	$shopLink = new shopLink_Utlity($customer_id);
	$link_arr = $shopLink->getSelectLink(array(3), 1);	//3：产品分类
	$type_arr = $link_arr['type_arr'];
}

//其他模板
$template_link = array();	//其他模板
$query_template = "SELECT id,name FROM pcshop_diy_template WHERE customer_id=".$customer_id." AND supply_id=".$supply_id." AND isvalid=true AND id != $temid AND custom_type = 3 order by id desc";
$result_template = _mysql_query($query_template) or die('Query_template failed:'.mysql_error());
while( $row_template = mysql_fetch_object($result_template) ){
	$template_id 	 = $row_template -> id;		//模板id
	$template_name 	 = $row_template -> name;	//模板名称
	$template_link[] = $template_id."_".$template_name;
}

//微视直播房间
$room_link = array();
$query_weishi = "select r.id,r.title from weixin_os_room r inner join weixin_os_anchor a on r.anchor_id=a.id where r.isvalid=true and a.isvalid=true and a.customer_id=".$customer_id;
$result_weishi = _mysql_query($query_weishi) or die('query_weishi failed:'.mysql_error());
while( $row_weishi = mysql_fetch_object($result_weishi) ){
	$room_id 	     = $row_weishi -> id;		//模板id
	$room_title 	 = $row_weishi -> title;	//模板名称
	$room_link[] = $room_id."_".$room_title;
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

<link href="../../../../back_commonshop/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/style.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/operamasks-ui.css" rel="stylesheet" type="text/css"> 
<link rel="stylesheet" media="screen" type="text/css" href="css/layout.css" />
<link rel="stylesheet" type="text/css" href="css/colorpicker.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/liebiaoye.css">
<link rel="stylesheet" type="text/css" href="css/pianpeifenlei.css">
<link rel="stylesheet" type="text/css" href="css/other-style.css">
<link rel="stylesheet" href="css/font-awesome.min.css"/>
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
</style>
       <!--列表内容大框开始-->
	<div class="WSY_columnbox" style="position:relative">
    	<!--列表头部切换开始-->
    	<?php
			//$head_type = 'pcshop_custom';
			if( $supply_id<0 && empty($_GET['supply_id'])){
				include("../../../../../weixinpl/back_newshops/PcShop/Base/basic_head.php"); 
			}
		?>
        <!--列表头部切换结束-->
         
    <!--首页设置代码开始-->
<div class="main">
    <div class="WSY_data">
    	<div class="WSY_homebox">
        	<div class="WSY_homeleft">
            	<!--<li class="WSY_homeleft_top">
                	<p></p>
                </li>-->                
				<li class="WSY_homeleft_middle" style="background:<?php echo $bgcolor;?>">
                <!--模块开始-->
					
                <!--模块结束-->
                </li>
                <li class="WSY_foot" style="background:<?php echo $bgcolor;?>">
                </li>
				<!--浮动楼层插件-->
				<div class="nav-left-fix" id="nav-left-floor" <?php if( $floating_floor == 0 ){?>style="display:none;"<?php }?>>
					<?php
						for ( $i = 0; $i < $floor_num; $i++ ){
					?>
					<div class="nav-chd-left"><a href="#floor-<?php echo $floor_content[$i]['floor_number']?>"><span class="normal-span"><?php echo $floor_content[$i]['floor_number']?>F</span></a><span class="focus-span main-theme-bg"><?php echo $floor_content[$i]['floor_number']?>F&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="floor_number_<?php echo $floor_content[$i]['floor_number']?>"><?php echo $floor_content[$i]['title']?></span></span></div>
					<?php
						}
					?>
					<div class="nav-chd-left"><span class="nav-go-top" id="backToTop-up"><i class="fa fa-angle-up" ></i></span></div>
				</div>
				<!--浮动楼层插件-->
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
				<div class="formitems">
                    <label class="fi-name" style="width:125px;">自动浮动楼层插件：</label> 
                    <div class="form-controls">
                        <label style="margin-right:5px;"><input type="radio" name="floating_floor" value="1" <?php if( $floating_floor == 1 ){?>checked<?php }?> >显示</label>
						<label><input type="radio" name="floating_floor" value="0" <?php if( $floating_floor == 0 ){?>checked<?php }?> >隐藏</label>
                    </div>
                </div>
				<?php if($supply_id<0){?>
				<div class="formitems">
                    <label class="fi-name" style="width:125px;">所属页面：</label> 
                    <div class="form-controls">
                        <select  name="custom_type"  id="custom_type"  class="input" style="height:28px;">
							<option value="1" <?php if($custom_type==1){?>selected="selected"<?php }?> >首页</option>
							<option value="2" <?php if($custom_type==2){?>selected="selected"<?php }?> >一级列表页</option>
							<option value="3" <?php if($custom_type==3){?>selected="selected"<?php }?> >活动页</option>
						</select>
                    </div>
                </div>
				<?php }?>
				<!--<p class="imgnav-select">
				<iframe src="default_img.php?customer_id=<?php echo $customer_id_en; ?>&temid=<?php echo $temid; ?>" height=200 width=100% FRAMEBORDER=0 SCROLLING=no></iframe>
            	</p>-->
            </div>
            </div>
        </div>
        <div class="diy-actions">
                <div class="diy-actions-addModules clearfix">
                    <a data-type="0" class="j-page-addModule" href="javascript:;"><i class="gicon-cog"></i>页面设置</a>
                    <a data-type="1" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>品牌推荐</a>
                    <a data-type="2" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>竖型广告</a>
                    <a data-type="3" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>横型广告</a>
                    <a data-type="4" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>楼层专区</a>
					<a data-type="5" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>活动模板块</a>
					<a data-type="6" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>多分类橱窗</a>
					<a data-type="7" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>分类产品</a>
					<a data-type="8" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>轮播图</a>
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
    <div class="type-ctrl-item" data-origin="item">
        {{= it.html }}
    </div>
</script>
<!--品牌推荐-->
<script type="text/j-template" id="type_con_1">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
	<div class="good-top main-border-left3">
		<div class="good-top-title">
            <div class="good-top-maintitle"><h3 class="good-main-title">{{= it.content.title}}</h3><h3 style="color: #bbbbbb;">{{= it.content.title_en}}</h3></div>
            <span style="font-size:12px;-webkit-transform-origin-x: 0;-webkit-transform: scale(0.75);color: #bbbbbb;display:-webkit-inline-box;">{{= it.content.mod_describe}}</span>
        </div>
        <div class="good-top-title nav">
		{{? it.content.is_show[1] == 1}}
            <a {{? it.content.tab == 1}}class="good-menu-select main-theme-bg"{{?}} data-target="brand-box1">{{= it.content.dataset[1][0].title}}</a>
		{{?}}
		{{? it.content.is_show[2] == 1}}
            <a {{? it.content.tab == 2}}class="good-menu-select main-theme-bg"{{?}} data-target="brand-box2">{{= it.content.dataset[2][0].title}}</a>
		{{?}}
		{{? it.content.is_show[3] == 1}}
            <a {{? it.content.tab == 3}}class="good-menu-select main-theme-bg"{{?}} data-target="brand-box1">{{= it.content.dataset[3][0].title}}</a>
		{{?}}
		{{? it.content.is_show[4] == 1}}
            <a {{? it.content.tab == 4}}class="good-menu-select main-theme-bg"{{?}} data-target="brand-box2">{{= it.content.dataset[4][0].title}}</a>
		{{?}}
        </div>
        <input type="button" class="more-view main-theme-bg" value="更多>">
    </div>
    <div class="good-main clearfix">
        <a href="" target="_blank"><img class="good-main-img" src="{{= it.content.dataset[0][0].pic}}"></a>
		{{? it.content.is_show[it.content.tab] == 1}}
        <div class="brand-main brand-box1">
            <div class="brand-one-floor">
			{{ for(var i=0,l=it.content.dataset[it.content.tab].length;i<l;i++) { }}
                <div class="brand-one-cell"><img src="{{= it.content.dataset[it.content.tab][i].pic}}"></div>
			{{ } }}
            </div>
        </div>
		{{?}}
    </div>
</div>
</script>
<script type="dot-template" id="type_ctrl_1">
<div class="formitems">  
    <label class="fi-name pc-fi-name">标题：</label>   
    <input type="text" name="title" class="input pc-title-input" style="float:left;" value="{{= it.content.title}}" maxlength="6">
	<label class="fi-name pc-fi-name">副标题：</label>   
    <input type="text" name="title_en" class="input pc-title-input" value="{{= it.content.title_en}}" maxlength="15">
</div>
<div class="formitems">  
    <label class="fi-name" style="width: 130px;">模块描述：(最多60字)</label>   
	<textarea class="pc-brand-mod-describe" name="mod_describe" rows="2" cols="60" maxlength="60">{{= it.content.mod_describe}}</textarea>
</div>
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
<ul> 
	<li>左侧图片：</li>
    <li class="ctrl-item-list-li clearfix" data-sort="50" data-position="0">
        <div class="fl">
            <div class="imgnav j-selectimg">
            <form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img50" enctype="multipart/form-data" method="post" onsubmit="return saveReport(50);">
                <input type="hidden" name="getImg" id='getImg50' value="{{= it.content.dataset[0][0].pic}}">
                <p class="imgnav-select">
                    <input type="file" size="20" name="upfile2" id="upfile2" class="up">
                    <img src="{{= it.content.dataset[0][0].pic}}">
                </p>
                <input type="hidden" name="diy_tem_contid" value="{{= it.id}}">
                <input type="hidden" name="diy_temid" value="<?php echo $diy_temid;?>">
                <input type="hidden" name="img_sort" value="0">
            </form>
            </div>
            <span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
        </div>
        <div class="fl imgnav-info">
            <div class="formitems">  
                <label class="fi-name">链接到：</label>  
				<div class="radio-group" style="padding-top:0;">
					<label><input type="radio" class="link_type" name="link_type_50" value="" {{? it.content.dataset[0][0].link_type != 1}}checked{{?}}>PC商城</label>
					<label><input type="radio" class="link_type" name="link_type_50" value="1" {{? it.content.dataset[0][0].link_type == 1}}checked{{?}}>链接网址</label>
				</div>
				{{? it.content.dataset[0][0].link_type == 1}}
                <div class="form-controls">
					<input type="text" class="j-verify" name="link_address" value="{{=it.content.dataset[0][0].link}}" placeholder="请输入网址，必须以http://开头">
				</div>
				{{??}}
				<div class="form-controls">
                    <div class="droplist">
						<select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
						{{? it.fixed_link}}
							{{ select_value=0; }}
							{{? it.content.dataset[0][0].select_value}}
		                        {{	selv=it.content.dataset[0][0].select_value.split("_");
									select_value=selv[0];
		                        }}
                        	{{?}}
							{{	for( k=0,m=it.fixed_link.length; k<m; k++ ) { 
                                fl = it.fixed_link[k].split("_");
                            }}
							<option value="2_{{=fl[0]}}" {{? fl[0]==select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
						{{?}}
						{{? it.type_arr}}
							<optgroup label="------------产品分类------------"></optgroup>
							<option value="3" {{? it.content.dataset[0][0].link_type==3}} selected="selected"{{?}}>多级分类</option>
						{{?}}
						{{? it.brand_arr}}
							<optgroup label="------------品牌供应商----------"></optgroup>
							<option value="4" {{? it.content.dataset[0][0].link_type==4}} selected="selected"{{?}}>品牌供应商店铺</option>
						{{?}}
						<?php if($supply_id<0){?>
						{{? it.template_link}}
						<optgroup label="-------------活动页--------------"></optgroup>
							<option value="5" {{? it.content.dataset[0][0].link_type==5}} selected="selected"{{?}}>活动页模板</option>
						{{?}}
						<!--{{? it.room_link}}
						<optgroup label="----------微视直播系统-----------"></optgroup>
							<option value="7" {{? it.content.dataset[0][0].link_type==7}} selected="selected"{{?}}>直播房间</option>
						{{?}}-->
						<?php }?>
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<optgroup label="----------产品分类---------"></optgroup>
							<option value="6" {{? it.content.dataset[0][0].link_type==6}} selected="selected"{{?}}>产品分类</option>
						{{?}}
                        </select>
						<!-- 产品分类 -->
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_50"  class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=3}}display:none;{{?}}height:28px;">
							{{for ( k=0,m=it.type_arr[-1].length; k<m; k++ ) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}"{{? type_id==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}"{{? ctype_id==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}"{{? ctype_id3==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}"{{? ctype_id4==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
						</select>
						{{?}}
						<!-- 品牌供应商店铺 -->
						{{? it.brand_arr}}
						<select name="brand_supply" id="brand_supply_50" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=4}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.brand_arr.length; k<m; k++ ) {
								supply_id_name = it.brand_arr[k].split("_");
							}}
							<option value="{{= supply_id_name[0]}}" {{? supply_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 礼包列表 -->
						{{? it.package_lists}}
						{{	selv=it.content.dataset[0][0].select_value.split("_");
							select_value=selv[0];
						}}
						<select name="brand_supply" id="brand_supply_80" class="input xlarge" style="{{? select_value!=-10}}display:none;{{?}}height:28px;" >
						    <option value="-10" >全部礼包</option>
						    {{for ( k=0,m=it.package_lists.length; k<m; k++ ) {
						        supply_id_name = it.package_lists[k].split("_");
						    }}
						    <option value="-10_{{= supply_id_name[0]}}" {{? "-10_"+supply_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
						    {{ } }}
						</select>
						{{?}}
						<!-- 其他模板 -->
						{{? it.template_link}}
						<select name="template_link" id="template_link_50" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=5}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.template_link.length; k<m; k++ ) {
								template_id_name = it.template_link[k].split("_");
							}}
							<option value="{{= template_id_name[0]}}" {{? template_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= template_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 微视直播房间 -->
						<!--{{? it.room_link}}
						<select name="room_link" id="room_link_50" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=7}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.room_link.length; k<m; k++ ) {
								room_id_name = it.room_link[k].split("_");
							}}
							<option value="{{= room_id_name[0]}}" {{? room_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= room_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}-->
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<select name="supply_type" id="supply_type_50" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=6}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.supply_type_arr.length; k<m; k++ ) {
								supply_type_id_name = it.supply_type_arr[k].split("_");
							}}
							<option value="{{= supply_type_id_name[0]}}" {{? supply_type_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= supply_type_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 产品 -->
                        <div id="div_products_2_50" style="display:none;" >
                            <select name="product_detail_id_2" id="product_detail_id_2_50" class="input xlarge" style="height:28px;">
                                
							</select>
						</div>
                    </div>
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
                </div>
				{{?}}
            </div>
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">390*280 px</label>
            </div>
        </div>
    </li>
</ul>
<div class="pc-line"></div>
<div class="formitems">  
    <ul class="pc-brand-tab">
		<li><span class="pc-brand-tab-data {{? it.content.tab == 1}}pc-brand-tab-selected{{?}}" data-tab="1">{{= it.content.dataset[1][0].title}}</span></li>
		<li><span class="pc-brand-tab-data {{? it.content.tab == 2}}pc-brand-tab-selected{{?}}" data-tab="2">{{= it.content.dataset[2][0].title}}</span></li>
		<li><span class="pc-brand-tab-data {{? it.content.tab == 3}}pc-brand-tab-selected{{?}}" data-tab="3">{{= it.content.dataset[3][0].title}}</span></li>
		<li><span class="pc-brand-tab-data {{? it.content.tab == 4}}pc-brand-tab-selected{{?}}" data-tab="4">{{= it.content.dataset[4][0].title}}</span></li>
	</ul>
</div>
<div class="formitems">  
    <label class="fi-name pc-fi-name">导航标题：</label>   
    <input type="text" name="nav_title" class="input pc-title-input" value="{{= it.content.dataset[it.content.tab][0].title}}" maxlength="6">
</div>
<div class="formitems">  
    <label class="fi-name pc-fi-name">是否显示：</label>   
    <div class="radio-group" style="padding-top:0;">
        <label><input type="radio" name="pc_brand_tab_show" value="1" {{? it.content.is_show[it.content.tab] == 1}}checked{{?}}>显示</label>
        <label><input type="radio" name="pc_brand_tab_show" value="0" {{? it.content.is_show[it.content.tab] == 0}}checked{{?}}>隐藏</label>
    </div>
</div>
<div class="formitems">  
    <label class="fi-name pc-fi-name">楼层：</label>   
    <select class="select-floor" name="floor">
		<option value="1" {{? it.content.floor == 1}}selected{{?}}>楼层一</option>
		{{? it.content.dataset[it.content.tab].length >= 6}}
		<option value="2" {{? it.content.floor == 2}}selected{{?}}>楼层二</option>
		{{?}}
		{{? it.content.dataset[it.content.tab].length >= 12}}
		<option value="3" {{? it.content.floor == 3}}selected{{?}}>楼层三</option>
		{{?}}
	</select>
</div>
<ul class="ctrl-item-list">
<!-- 按楼层显示 -->
{{? it.content.floor == 1}}
	{{
		var i = 0;
		var l = 6;
	}}
{{?? it.content.floor == 2}}
	{{
		var i = 6;
		var l = 12;
	}}
{{?? it.content.floor == 3}}
	{{
		var i = 12;
		var l = 18;
	}}
{{?}}
{{? it.content.dataset[it.content.tab].length < l}}
	{{
		l = it.content.dataset[it.content.tab].length;
	}}
{{?}}
{{ for(  ;i<l;i++) { }}
    <li class="ctrl-item-list-li clearfix" data-sort="{{= i}}" data-position="{{= it.content.tab}}">
        <div class="fl">
            <div class="imgnav j-selectimg">			
			<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{= i}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{= i}});">
				<input type="hidden" name="getImg" id='getImg{{= i}}' value="{{= it.content.dataset[it.content.tab][i].pic}}">
                <p class="imgnav-select">
                    <input type="file" size="20" name="upfile2" id="upfile2" class="up">
                    <img src="{{= it.content.dataset[it.content.tab][i].pic}}">
                </p>
				<input type="hidden" name="diy_tem_contid" value="{{= it.id}}">
				<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
				<input type="hidden" name="img_sort" value="{{= i}}">
                
			</form>
            </div>
            <span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
        </div>
		
        <div class="fl imgnav-info">
            <div class="formitems">  
                <label class="fi-name">链接到：</label>  
				<div class="radio-group" style="padding-top:0;">
					<label><input type="radio" class="link_type" name="link_type_{{= i}}" value="" {{? it.content.dataset[it.content.tab][i].link_type != 1}}checked{{?}}>PC商城</label>
					<label><input type="radio" class="link_type" name="link_type_{{= i}}" value="1" {{? it.content.dataset[it.content.tab][i].link_type == 1}}checked{{?}}>链接网址</label>
				</div>
				{{? it.content.dataset[it.content.tab][i].link_type == 1}}
                <div class="form-controls">
					<input type="text" class="j-verify" name="link_address" value="{{=it.content.dataset[it.content.tab][i].link}}" placeholder="请输入网址，必须以http://开头">
				</div>
				{{??}}
                <div class="form-controls">
                    <div class="droplist">
						<select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
                        {{? it.fixed_link}}
                        	{{ select_value=0; }}
	                        {{? it.content.dataset[it.content.tab][i].select_value}}
		                        {{	selv=it.content.dataset[it.content.tab][i].select_value.split("_");
									select_value=selv[0];
		                        }}
                        	{{?}}
							{{	for( k=0,m=it.fixed_link.length; k<m; k++ ) { 
                                fl = it.fixed_link[k].split("_");
                            }}
							<option value="2_{{=fl[0]}}" {{? fl[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
						{{?}}
						{{? it.type_arr}}
							<optgroup label="------------产品分类------------"></optgroup>
							<option value="3" {{? it.content.dataset[it.content.tab][i].link_type==3}} selected="selected"{{?}}>多级分类</option>
						{{?}}
						{{? it.brand_arr}}
							<optgroup label="------------品牌供应商----------"></optgroup>
							<option value="4" {{? it.content.dataset[it.content.tab][i].link_type==4}} selected="selected"{{?}}>品牌供应商店铺</option>
						{{?}}
						<?php if($supply_id<0){?>
						{{? it.template_link}}
						<optgroup label="-------------活动页--------------"></optgroup>
							<option value="5" {{? it.content.dataset[it.content.tab][i].link_type==5}} selected="selected"{{?}}>活动页模板</option>
						{{?}}
						<!--{{? it.room_link}}
						<optgroup label="----------微视直播系统-----------"></optgroup>
							<option value="7" {{? it.content.dataset[it.content.tab][i].link_type==7}} selected="selected"{{?}}>直播房间</option>
						{{?}}-->
						<?php }?>
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<optgroup label="----------产品分类---------"></optgroup>
							<option value="6" {{? it.content.dataset[it.content.tab][i].link_type==6}} selected="selected"{{?}}>产品分类</option>
						{{?}}
                        </select>
						<!-- 产品分类 -->
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_{{=i}}"  class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=3}}display:none;{{?}}height:28px;">
							{{for ( k=0,m=it.type_arr[-1].length; k<m; k++ ) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}"{{? type_id==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}"{{? ctype_id==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}"{{? ctype_id3==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}"{{? ctype_id4==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
						</select>
						{{?}}
						<!-- 品牌供应商店铺 -->
						{{? it.brand_arr}}
						<select name="brand_supply" id="brand_supply_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=4}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.brand_arr.length; k<m; k++ ) {
								supply_id_name = it.brand_arr[k].split("_");
							}}
							<option value="{{= supply_id_name[0]}}" {{? supply_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 礼包列表 -->
						{{? it.package_lists}}
                        {{ select_value=0; }}
						{{? it.content.dataset[it.content.tab][i].select_value}}
							{{	selv=it.content.dataset[it.content.tab][i].select_value.split("_");
								select_value=selv[0];
							}}
						{{?}}
						<select name="brand_supply" id="brand_supply_80" class="input xlarge" style="{{? select_value!=-10}}display:none;{{?}}height:28px;" >
						    <option value="-10" >全部礼包</option>
						    {{for ( k=0,m=it.package_lists.length; k<m; k++ ) {
						        supply_id_name = it.package_lists[k].split("_");
						    }}
						    <option value="-10_{{= supply_id_name[0]}}" {{? "-10_"+supply_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
						    {{ } }}
						</select>
						{{?}}
						<!-- 其他模板 -->
						{{? it.template_link}}
						<select name="template_link" id="template_link_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=5}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.template_link.length; k<m; k++ ) {
								template_id_name = it.template_link[k].split("_");
							}}
							<option value="{{= template_id_name[0]}}" {{? template_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= template_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 微视直播房间 -->
						<!--{{? it.room_link}}
						<select name="room_link" id="room_link_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=7}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.room_link.length; k<m; k++ ) {
								room_id_name = it.room_link[k].split("_");
							}}
							<option value="{{= room_id_name[0]}}" {{? room_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= room_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}-->
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<select name="supply_type" id="supply_type_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=6}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.supply_type_arr.length; k<m; k++ ) {
								supply_type_id_name = it.supply_type_arr[k].split("_");
							}}
							<option value="{{= supply_type_id_name[0]}}" {{? supply_type_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= supply_type_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 产品 -->
                        <div id="div_products_2_{{=i}}" style="display:none;" >
                            <select name="product_detail_id_2" id="product_detail_id_2_{{=i}}" class="input xlarge" style="height:28px;">
                                
							</select>
						</div>
					</div>
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
                </div>
				{{?}}
			</div>
			<div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">131*92 px</label>
            </div>
        </div>
        <div class="ctrl-item-list-actions">
            <a href="javascript:;" title="上移" class="j-moveup"><i class="gicon-arrow-up"></i></a>
            <a href="javascript:;" title="下移" class="j-movedown"><i class="gicon-arrow-down"></i></a>
            <a href="javascript:;" title="删除" class="j-del"><i class="gicon-remove"></i></a>
        </div>
    </li>
	{{ } }}
	<!-- 每个楼层最多六张图片 -->
	{{? (it.content.dataset[it.content.tab].length < 6 && it.content.floor == 1) || (it.content.dataset[it.content.tab].length < 12 && it.content.floor == 2) || (it.content.dataset[it.content.tab].length < 18 && it.content.floor == 3)}}
    <li class="ctrl-item-list-add" title="添加">+</li>
	{{?}}
</ul>
</script>
<!--品牌推荐-->
<!--竖形广告-->
<script type="text/j-template" id="type_con_2">
{{
	if(it.content.nav_css_type[0]==1){
		var l = 2;
	}else if(it.content.nav_css_type[0]==2){
		var l = 3;
	}else if(it.content.nav_css_type[0]==3){
		var l = 4;
	}else if(it.content.nav_css_type[0]==4){
		var l = 5;
	}
}}
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
	<ul class="pg-1">
		{{for (p=0;p<l;p++) { }}
		<li class="photo1-li photo1-left p-li-{{=l}}">
			<a>
				<img class="photo1-img2" src="{{= it.content.dataset[0][p].pic}}">
			</a>
		</li>
		{{ } }}
		<div style="clear:both"></div>
	</ul>
</div>
</script>
<script type="dot-template" id="type_ctrl_2">
{{
	if(it.content.nav_css_type[0]==1){
		var l = 2;
	}else if(it.content.nav_css_type[0]==2){
		var l = 3;
	}else if(it.content.nav_css_type[0]==3){
		var l = 4;
	}else if(it.content.nav_css_type[0]==4){
		var l = 5;
	}
}}
<div class="formitems">
	<label class="fi-name">模块上下边距：</label> 
	<div class="form-controls">
		<div id='slider' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
		<span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
	</div>
</div>
<div class="formitems">  
	<label class="fi-name pc-fi-name">图片数量：</label>   
	<div class="radio-group" style="padding-top:0;">
		<label><input type="radio" name="nav_css_type" value="1"{{? it.content.nav_css_type[0] == 1}} checked{{?}}>二</label>
        <label><input type="radio" name="nav_css_type" value="2"{{? it.content.nav_css_type[0] == 2}} checked{{?}}>三</label>
        <label><input type="radio" name="nav_css_type" value="3"{{? it.content.nav_css_type[0] == 3}} checked{{?}}>四</label>
		<label><input type="radio" name="nav_css_type" value="4"{{? it.content.nav_css_type[0] == 4}} checked{{?}}>五</label>
	</div>
</div>
<ul class="ctrl-item-list"> 
	{{for (p=0;p<l;p++) { }}
    <li class="ctrl-item-list-li clearfix" data-sort="{{= p}}" data-position="0">		
		<div class="fl">
			<div class="imgnav j-selectimg">			
			<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{=p}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{=p}});">
				<input type="hidden" name="getImg" id='getImg{{=p}}' value="{{= it.content.dataset[0][p].pic}}">
				<p class="imgnav-select">
					<input type="file" size="20" name="upfile2" id="upfile2" class="up" >
					<img src="{{= it.content.dataset[0][p].pic}}">
				</p>
				<input type="hidden" name="diy_tem_contid" value="{{= it.id}}">
				<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
				<input type="hidden" name="img_sort" value="{{= p}}">
				
			</form>
			</div>
			<span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
		</div>
		
		<div class="fl imgnav-info">
			<div class="formitems">  
				<label class="fi-name">链接到：</label> 
				<div class="radio-group" style="padding-top:0;">
					<label><input type="radio" class="link_type" name="link_type_{{=p}}" value="" {{? it.content.dataset[0][p].link_type != 1}}checked{{?}}>PC商城</label>
					<label><input type="radio" class="link_type" name="link_type_{{=p}}" value="1" {{? it.content.dataset[0][p].link_type == 1}}checked{{?}}>链接网址</label>
				</div>
				{{? it.content.dataset[0][p].link_type == 1}}
                <div class="form-controls">
					<input type="text" class="j-verify" name="link_address" value="{{=it.content.dataset[0][p].link}}" placeholder="请输入网址，必须以http://开头">
				</div>
				{{??}}	
				<div class="form-controls">
					<div class="droplist">
						<select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
						{{? it.fixed_link}}
							{{ select_value=0;}}
							{{? it.content.dataset[0][p].select_value}}
								{{ 	selv=it.content.dataset[0][p].select_value.split("_");
									select_value=selv[0];
								}}
							{{?}}
							{{	for( k=0,m=it.fixed_link.length; k<m; k++ ) { 
                                fl = it.fixed_link[k].split("_");
                            }}
							<option value="2_{{=fl[0]}}" {{? fl[0]==select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
						{{?}}
						{{? it.type_arr}}
							<optgroup label="------------产品分类------------"></optgroup>
							<option value="3" {{? it.content.dataset[0][p].link_type==3}} selected="selected"{{?}}>多级分类</option>
						{{?}}
						{{? it.brand_arr}}
							<optgroup label="------------品牌供应商----------"></optgroup>
							<option value="4" {{? it.content.dataset[0][p].link_type==4}} selected="selected"{{?}}>品牌供应商店铺</option>
						{{?}}
						<?php if($supply_id<0){?>
						{{? it.template_link}}
						<optgroup label="-------------活动页--------------"></optgroup>
							<option value="5" {{? it.content.dataset[0][p].link_type==5}} selected="selected"{{?}}>活动页模板</option>
						{{?}}
						<!--{{? it.room_link}}
						<optgroup label="----------微视直播系统-----------"></optgroup>
							<option value="7" {{? it.content.dataset[0][p].link_type==7}} selected="selected"{{?}}>直播房间</option>
						{{?}}-->
						<?php }?>
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<optgroup label="----------产品分类---------"></optgroup>
							<option value="6" {{? it.content.dataset[0][p].link_type==6}} selected="selected"{{?}}>产品分类</option>
						{{?}}
                        </select>
						<!-- 产品分类 -->
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_{{=p}}"  class="input xlarge" style="{{? it.content.dataset[0][p].link_type!=3}}display:none;{{?}}height:28px;">
							{{for ( k=0,m=it.type_arr[-1].length; k<m; k++ ) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}"{{? type_id==it.content.dataset[0][p].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}"{{? ctype_id==it.content.dataset[0][p].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}"{{? ctype_id3==it.content.dataset[0][p].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}"{{? ctype_id4==it.content.dataset[0][p].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
						</select>
						{{?}}
						<!-- 品牌供应商店铺 -->
						{{? it.brand_arr}}
						<select name="brand_supply" id="brand_supply_{{=p}}" class="input xlarge" style="{{? it.content.dataset[0][p].link_type!=4}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.brand_arr.length; k<m; k++ ) {
								supply_id_name = it.brand_arr[k].split("_");
							}}
							<option value="{{= supply_id_name[0]}}" {{? supply_id_name[0]==it.content.dataset[0][p].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 礼包列表 -->
						{{? it.package_lists}}
						{{ select_value=0;}}
						{{? it.content.dataset[0][p].select_value}} 
							{{ 	selv=it.content.dataset[0][p].select_value.split("_");
								select_value=selv[0];
							}}
						{{?}}
						<select name="brand_supply" id="brand_supply_80" class="input xlarge" style="{{? select_value!=-10}}display:none;{{?}}height:28px;" >
						    <option value="-10" >全部礼包</option>
						    {{for ( k=0,m=it.package_lists.length; k<m; k++ ) {
						        supply_id_name = it.package_lists[k].split("_");
						    }}
						    <option value="-10_{{= supply_id_name[0]}}" {{? "-10_"+supply_id_name[0]==it.content.dataset[0][p].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
						    {{ } }}
						</select>
						{{?}}
						<!-- 其他模板 -->
						{{? it.template_link}}
						<select name="template_link" id="template_link_{{=p}}" class="input xlarge" style="{{? it.content.dataset[0][p].link_type!=5}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.template_link.length; k<m; k++ ) {
								template_id_name = it.template_link[k].split("_");
							}}
							<option value="{{= template_id_name[0]}}" {{? template_id_name[0]==it.content.dataset[0][p].select_value}} selected="selected"{{?}}>{{= template_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 微视直播房间 -->
						<!--{{? it.room_link}}
						<select name="room_link" id="room_link_{{=p}}" class="input xlarge" style="{{? it.content.dataset[0][p].link_type!=7}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.room_link.length; k<m; k++ ) {
								room_id_name = it.room_link[k].split("_");
							}}
							<option value="{{= room_id_name[0]}}" {{? room_id_name[0]==it.content.dataset[0][p].select_value}} selected="selected"{{?}}>{{= room_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}-->
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<select name="supply_type" id="supply_type_{{= p}}" class="input xlarge" style="{{? it.content.dataset[0][p].link_type!=6}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.supply_type_arr.length; k<m; k++ ) {
								supply_type_id_name = it.supply_type_arr[k].split("_");
							}}
							<option value="{{= supply_type_id_name[0]}}" {{? supply_type_id_name[0]==it.content.dataset[0][p].select_value}} selected="selected"{{?}}>{{= supply_type_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 产品 -->
						<div id="div_products_2_{{=p}}" style="display:none;" >
							<select name="product_detail_id_2" id="product_detail_id_2_{{=p}}" class="input xlarge" style="height:28px;">
								
							</select>
						</div>
					</div>
					<input type="hidden" class="j-verify" name="item_id" value="">
					<span class="fi-help-text j-verify-linkType"></span>
				</div>
				{{?}}
			</div>
			<div class="formitems">  
				<label class="fi-name">建议尺寸：</label>
				{{? it.content.nav_css_type[0]==1}}
				<label class="note">宽度600px，高度需保持多张图片一致</label>
				{{?? it.content.nav_css_type[0]==2}}
				<label class="note">宽度400px，高度需保持多张图片一致</label>
				{{?? it.content.nav_css_type[0]==3}}
				<label class="note">宽度300px，高度需保持多张图片一致</label>
				{{?? it.content.nav_css_type[0]==4}}
				<label class="note">宽度240px，高度需保持多张图片一致</label>
				{{?}}
			</div>
		</div>

        <div class="ctrl-item-list-actions">
            <a href="javascript:;" title="上移" class="j-moveup"><i class="gicon-arrow-up"></i></a>
            <a href="javascript:;" title="下移" class="j-movedown"><i class="gicon-arrow-down"></i></a>
        </div>
    </li>
    {{ } }}
</ul>
</script>
<!--竖形广告-->
<!--横型广告-->
<script type="text/j-template" id="type_con_3">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
	<div class="nav-mid-bottom">
		<img src="{{= it.content.dataset[0][0].pic}}">
	</div>
</div>
</script>
<script type="dot-template" id="type_ctrl_3">
<div class="formitems">
	<label class="fi-name">模块上下边距：</label> 
	<div class="form-controls">
		<div id='slider' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
		<span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
	</div>
</div>
<li class="ctrl-item-list-li clearfix" data-sort="0" data-position="0"> 
     <div class="fl">
		<div class="imgnav j-selectimg">			
		<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img0" enctype="multipart/form-data" method="post" onsubmit="return saveReport(0);">
			<input type="hidden" name="getImg" id='getImg0' value="{{= it.content.dataset[0][0].pic}}">
			<p class="imgnav-select">
				<input type="file" size="20" name="upfile2" id="upfile2" class="up" >
				<img src="{{= it.content.dataset[0][0].pic}}">
			</p>
			<input type="hidden" name="diy_tem_contid" value="{{= it.id}}">
			<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
			<input type="hidden" name="img_sort" value="0">
			
		</form>
		</div>
		<span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
	</div>
	<div class="fl imgnav-info">
		<div class="formitems">  
			<label class="fi-name">链接到：</label>  
			<div class="radio-group" style="padding-top:0;">
				<label><input type="radio" class="link_type" name="link_type_0" value="" {{? it.content.dataset[0][0].link_type != 1}}checked{{?}}>PC商城</label>
				<label><input type="radio" class="link_type" name="link_type_0" value="1" {{? it.content.dataset[0][0].link_type == 1}}checked{{?}}>链接网址</label>
			</div>
			{{? it.content.dataset[0][0].link_type == 1}}
			<div class="form-controls">
				<input type="text" class="j-verify" name="link_address" value="{{=it.content.dataset[0][0].link}}" placeholder="请输入网址，必须以http://开头">
			</div>
			{{??}}	
			<div class="form-controls">
				<div class="droplist">
					<select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
						{{? it.fixed_link}}
						{{ select_value=0; }}
							{{? it.content.dataset[0][0].select_value}}
		                        {{	selv=it.content.dataset[0][0].select_value.split("_");
									select_value=selv[0];
		                        }}
                        	{{?}}
							{{	for( k=0,m=it.fixed_link.length; k<m; k++ ) { 
                                fl = it.fixed_link[k].split("_");
                            }}
							<option value="2_{{=fl[0]}}" {{? fl[0]==select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
						{{?}}
						{{? it.type_arr}}
							<optgroup label="------------产品分类------------"></optgroup>
							<option value="3" {{? it.content.dataset[0][0].link_type==3}} selected="selected"{{?}}>多级分类</option>
						{{?}}
						{{? it.brand_arr}}
							<optgroup label="------------品牌供应商----------"></optgroup>
							<option value="4" {{? it.content.dataset[0][0].link_type==4}} selected="selected"{{?}}>品牌供应商店铺</option>
						{{?}}
						<?php if($supply_id<0){?>
						{{? it.template_link}}
						<optgroup label="-------------活动页--------------"></optgroup>
							<option value="5" {{? it.content.dataset[0][0].link_type==5}} selected="selected"{{?}}>活动页模板</option>
						{{?}}
						<!--{{? it.room_link}}
						<optgroup label="----------微视直播系统-----------"></optgroup>
							<option value="7" {{? it.content.dataset[0][0].link_type==7}} selected="selected"{{?}}>直播房间</option>
						{{?}}-->
						<?php }?>
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<optgroup label="----------产品分类---------"></optgroup>
							<option value="6" {{? it.content.dataset[0][0].link_type==6}} selected="selected"{{?}}>产品分类</option>
						{{?}}
                        </select>
						<!-- 产品分类 -->
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_0"  class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=3}}display:none;{{?}}height:28px;">
							{{for ( k=0,m=it.type_arr[-1].length; k<m; k++ ) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}"{{? type_id==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}"{{? ctype_id==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}"{{? ctype_id3==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}"{{? ctype_id4==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
						</select>
						{{?}}
						<!-- 品牌供应商店铺 -->
						{{? it.brand_arr}}
						<select name="brand_supply" id="brand_supply_0" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=4}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.brand_arr.length; k<m; k++ ) {
								supply_id_name = it.brand_arr[k].split("_");
							}}
							<option value="{{= supply_id_name[0]}}" {{? supply_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 礼包列表 -->
						{{? it.package_lists}}
						{{? it.content.dataset[0][0].select_value}}
	                        {{	selv=it.content.dataset[0][0].select_value.split("_");
								select_value=selv[0];
	                        }}
                    	{{?}}
						<select name="brand_supply" id="brand_supply_80" class="input xlarge" style="{{? select_value!=-10}}display:none;{{?}}height:28px;" >
						    <option value="-10" >全部礼包</option>
						    {{for ( k=0,m=it.package_lists.length; k<m; k++ ) {
						        supply_id_name = it.package_lists[k].split("_");
						    }}
						    <option value="-10_{{= supply_id_name[0]}}" {{? "-10_"+supply_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
						    {{ } }}
						</select>
						{{?}}
						<!-- 其他模板 -->
						{{? it.template_link}}
						<select name="template_link" id="template_link_0" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=5}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.template_link.length; k<m; k++ ) {
								template_id_name = it.template_link[k].split("_");
							}}
							<option value="{{= template_id_name[0]}}" {{? template_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= template_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 微视直播房间 -->
						<!--{{? it.room_link}}
						<select name="room_link" id="room_link_0" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=7}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.room_link.length; k<m; k++ ) {
								room_id_name = it.room_link[k].split("_");
							}}
							<option value="{{= room_id_name[0]}}" {{? room_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= room_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}-->
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<select name="supply_type" id="supply_type_0" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=6}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.supply_type_arr.length; k<m; k++ ) {
								supply_type_id_name = it.supply_type_arr[k].split("_");
							}}
							<option value="{{= supply_type_id_name[0]}}" {{? supply_type_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= supply_type_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
					<!-- 产品 -->
					<div id="div_products_2_0" style="display:none;" >
						<select name="product_detail_id_2" id="product_detail_id_2_0" class="input xlarge" style="height:28px;">
							
						</select>
					</div>
				</div>
				<input type="hidden" class="j-verify" name="item_id" value="">
				<span class="fi-help-text j-verify-linkType"></span>
			</div>
			{{?}}
		</div>
		<div class="formitems">  
			<label class="fi-name">建议尺寸：</label>
			<label class="note">宽度1200px</label>
		</div>
	</div>
</li>
</script>
<!-- 横型广告 -->
<!-- 楼层专区 -->
<script type="text/j-template" id="type_con_4"> 
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
        <a name="floor-{{= it.id}}" id="floor-{{= it.content.floor_number}}" data-floor_number="{{= it.content.floor_number}}" data-title="{{= it.content.title}}"></a>
        <div class="info-box floor_module">
            <div class="mark">
                <img class="mark-l" src="img/00400.png" />
                <p class="mark-r">{{= it.content.title}}</p>
                <span class="span-other" id="floor_{{= it.id}}">{{= it.content.floor_number}}F</span>
            </div>
            <ul class="tab-nav">
			{{ for ( var i=0,l=it.content.dataset.length; i<l; i++ ) { }}
				{{? it.content.is_show[i] == 1 }}
                <li id="li4-1" class="li-nav {{? it.content.tab == i}}li-nav-hover{{?}}" style="width:75px;">{{= it.content.dataset[i][0].title}}</li>
				{{?}}
			{{ } }}
            </ul>
			{{? it.content.is_show[it.content.tab] == 1}}
				<!-- 样式一 -->
				{{? it.content.nav_css_type[it.content.tab] == 1}}
			<div id="box4-1" style="display: block" class="hiddenBox4">
				<div class="product-box">
                	<a href="#" target="_blank">
                        <div class="inner-left">
                            <img src="{{= it.content.dataset[it.content.tab][0].pic}}" />
                        </div>
                    </a>
                    <div class="inner-right2">
                        <div >
                            <div class="fourth-special-item">
                                <a href="#"><img src="{{= it.content.dataset[it.content.tab][1].pic}}" /></a>
                            </div>
                            <div class="fourth-first-item">
                                <a href="#">
                                    <img src="{{= it.content.dataset[it.content.tab][2].pic}}" />
                                </a>
                            </div>
                            <div class="fourth-first-item">
                                <a href="#">
                                    <img src="{{= it.content.dataset[it.content.tab][3].pic}}" />
                                </a>
                            </div>
                        </div>
                        <div>
                            <div class="fourth-second-item">
                                <a href="#">
                                    <img src="{{= it.content.dataset[it.content.tab][4].pic}}" />
                                </a>
                             </div>
                             <div class="fourth-second-item">
                                <a href="#">
                                    <img src="{{= it.content.dataset[it.content.tab][5].pic}}" />
                                </a>
                             </div>
                            <div class="fourth-second-item">
                                <a href="#">
                                    <img src="{{= it.content.dataset[it.content.tab][6].pic}}" />
                                </a>
                             </div>
                            <div class="fourth-second-item">
                                <a href="#">
                                    <img src="{{= it.content.dataset[it.content.tab][7].pic}}" />
                                </a>
                             </div>
                        </div>
                    </div>
                    <div style="clear:both"></div>				
                </div>
			</div>
			
				{{?? it.content.nav_css_type[it.content.tab] == 2 }}
				<!-- 样式二 -->
				<div id="box1-1" class="tab-inner-left hiddenBox1" style="display: block;" >
					<div class="tab-content content-left2">
                    	<a href="" target="_blank">
                        	<img src="{{= it.content.dataset[it.content.tab][0].pic}}">
                        </a>
                    </div>
                    <div class="tab-content content-center">
                        <div class="div-04">
                            <div class="inner-left2">
                                <img src="{{= it.content.dataset[it.content.tab][1].pic}}">
                            </div>
                            <div class="inner-left2">
                                <img src="{{= it.content.dataset[it.content.tab][2].pic}}">
                            </div>
                            <div class="inner-left2">
								<img src="{{= it.content.dataset[it.content.tab][3].pic}}">
                            </div>
                        </div>
                        <div class="div-04">
                            <div class="div-03" >
                                <img src="{{= it.content.dataset[it.content.tab][4].pic}}">
                            </div>
                            <div class="div-03" ><img src="{{= it.content.dataset[it.content.tab][5].pic}}"></div>
                        </div>
                    </div>
    
                    <div class="tab-content content-right2">
                        <div class="div-01" >
                            <img class="img-03" src="{{= it.content.dataset[it.content.tab][6].pic}}">
                        </div>
                        <div class="div-01" >
                            <img class="img-03" src="{{= it.content.dataset[it.content.tab][7].pic}}">
                        </div>
                        <div class="div-01" >
                        	<img class="img-03" src="{{= it.content.dataset[it.content.tab][8].pic}}">
                        </div>
                    </div>
				</div>
				<div style="clear:both"></div>
				{{?? it.content.nav_css_type[it.content.tab] == 3 }}
				<!-- 样式三 -->
				<div id="box3-1" class=" tab-inner-left hiddenBox3" style="display:block">
                	<a href="#" target="_blank">
                        <div class="tab-content content-left3">
                            <div class="nav-div-img3">
                                <img class="nav-img" src="{{= it.content.dataset[it.content.tab][0].pic}}">
                           </div>
                        </div>
                    </a>
                    <div class="tab-content padding-01">
                        <div class="div-06">
                            <div class="inner-left4">
                                <div class="thumbnail_img"><img src="{{= it.content.dataset[it.content.tab][1].pic}}"></div>
                            </div>
                            <div class="inner-left4">
                                <div class="thumbnail_img"><img src="{{= it.content.dataset[it.content.tab][2].pic}}"></div>
                            </div>
                            <div class="inner-left4">
                                <div class="thumbnail_img"><img src="{{= it.content.dataset[it.content.tab][3].pic}}"></div>
                            </div>
                            <div class="inner-left5">
                                <div class="div-07">
                                    <div class="div-08 div-082" ><img class="img-05" src="{{= it.content.dataset[it.content.tab][4].pic}}"></div>
                                </div>
                                <div class="div-07">
                                    <div class="div-08 div-082"><img class="img-05" src="{{= it.content.dataset[it.content.tab][5].pic}}" /></div>
                                </div>
                            </div>
                        </div>
                        <div class="div-06">
                            <div class="inner-left4">
                                <div class="thumbnail_img"><img src="{{= it.content.dataset[it.content.tab][6].pic}}"></div>
                            </div>
                            <div class="inner-left4">
                                <div class="thumbnail_img"><img src="{{= it.content.dataset[it.content.tab][7].pic}}"></div>
                            </div>
                            <div class="inner-left4">
                                <div class="thumbnail_img"><img src="{{= it.content.dataset[it.content.tab][8].pic}}"></div>
                            </div>
                            <div class="inner-left5">
                                <div class="div-07">
                                    <div class="div-08 div-082" ><img class="img-05" src="{{= it.content.dataset[it.content.tab][9].pic}}"></div>
                                </div>
                                <div class="div-07">
                                    <div class="div-08 div-082"><img class="img-05" src="{{= it.content.dataset[it.content.tab][10].pic}}"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<div style="clear:both"></div>
				{{?? it.content.nav_css_type[it.content.tab] == 4 }}
				<div id="box5-1" style="display: block;" class="hiddenBox5">
					<div class="product-box" style="">
						<div class="inner-left">
							<a href="#" target="_blank"><img src="{{= it.content.dataset[it.content.tab][0].pic}}"></a>
						</div>
						<div class="inner-right">
							<div>
								<div class="fifth-item">
									<a href="#">
										<img src="{{= it.content.dataset[it.content.tab][1].pic}}">				                
									</a>
								</div>
								<div class="fifth-item"> 	  
									<a href="#">
										<img src="{{= it.content.dataset[it.content.tab][2].pic}}">			                
									</a>
								</div>
								<div class="fifth-item">
									<a href="#">
										<img src="{{= it.content.dataset[it.content.tab][3].pic}}">				                
									</a>
								</div>
								<div class="fifth-item">
									<a href="#">
										<img src="{{= it.content.dataset[it.content.tab][4].pic}}">				                
									</a>
								</div>
								<div class="fifth-item">
									<a href="#">					            	
										<img src="{{= it.content.dataset[it.content.tab][5].pic}}">

									</a>
								 </div>
								 <div class="fifth-item">
									<a href="#">					         
										<img src="{{= it.content.dataset[it.content.tab][6].pic}}">
									</a>
								 </div>
								<div class="fifth-item">
									<a href="#">					            	
										<img src="{{= it.content.dataset[it.content.tab][7].pic}}">
									</a>
								 </div>
								<div class="fifth-item">
									<a href="#"> 	
										<img src="{{= it.content.dataset[it.content.tab][8].pic}}">
									</a>
								 </div>
								 <div class="fifth-item">
									<a href="#">					            	
										<img src="{{= it.content.dataset[it.content.tab][9].pic}}">
									</a>
								 </div>
								<div class="fifth-item">
									<a href="#">					        
										<img src="{{= it.content.dataset[it.content.tab][10].pic}}">
									</a>
								 </div>
							</div>
						</div>				
					</div>
				</div>
				<div style="clear:both"></div>
				{{?}}
			{{?}}
        </div>
        <div class="empty-div"></div>
</div>
</script>
<script type="dot-template" id="type_ctrl_4">
<div class="formitems">  
    <label class="fi-name pc-fi-name">楼层标题：</label>   
    <input type="text" name="title" class="input pc-title-input" value="{{=it.content.title}}" maxlength="6">
</div>
<div class="formitems">
	<label class="fi-name">模块上下边距：</label> 
	<div class="form-controls">
		<div id='slider' class="fl diy-slider j-slider2 ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
		<span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
	</div>
</div>
<div class="pc-line"></div>
<div class="formitems">  
    <ul class="pc-brand-tab">
		<li><span class="pc-brand-tab-data {{? it.content.tab == 0}}pc-brand-tab-selected{{?}}" data-tab="0">{{= it.content.dataset[0][0].title}}</span></li>
		<li><span class="pc-brand-tab-data {{? it.content.tab == 1}}pc-brand-tab-selected{{?}}" data-tab="1">{{= it.content.dataset[1][0].title}}</span></li>
		<li><span class="pc-brand-tab-data {{? it.content.tab == 2}}pc-brand-tab-selected{{?}}" data-tab="2">{{= it.content.dataset[2][0].title}}</span></li>
		<li><span class="pc-brand-tab-data {{? it.content.tab == 3}}pc-brand-tab-selected{{?}}" data-tab="3">{{= it.content.dataset[3][0].title}}</span></li>
	</ul>
</div>
<div class="formitems">  
    <label class="fi-name pc-fi-name">导航标题：</label>   
    <input type="text" name="nav_title" class="input pc-title-input" value="{{= it.content.dataset[it.content.tab][0].title}}" maxlength="6">
</div>
<div class="formitems">  
    <label class="fi-name pc-fi-name">是否显示：</label>   
    <div class="radio-group" style="padding-top:0;">
        <label><input type="radio" name="pc_brand_tab_show" value="1" {{? it.content.is_show[it.content.tab] == 1}}checked{{?}}>显示</label>
        <label><input type="radio" name="pc_brand_tab_show" value="0" {{? it.content.is_show[it.content.tab] == 0}}checked{{?}}>隐藏</label>
    </div>
</div>
<div class="formitems">  
	<label class="fi-name pc-fi-name">显示方式：</label>   
	<div class="radio-group" style="padding-top:0;">
		<label><input type="radio" name="nav_css_type" value="1"{{? it.content.nav_css_type[it.content.tab] == 1}} checked{{?}}>样式一</label>
        <label><input type="radio" name="nav_css_type" value="2"{{? it.content.nav_css_type[it.content.tab] == 2}} checked{{?}}>样式二</label>
        <label><input type="radio" name="nav_css_type" value="3"{{? it.content.nav_css_type[it.content.tab] == 3}} checked{{?}}>样式三</label>
		<label><input type="radio" name="nav_css_type" value="4"{{? it.content.nav_css_type[it.content.tab] == 4}} checked{{?}}>样式四</label>
	</div>
</div>
<ul class="ctrl-item-list">
{{
	if ( it.content.nav_css_type[it.content.tab] == 1 ){
		var l = 8;
	} else if ( it.content.nav_css_type[it.content.tab] == 2 ){
		var l = 9;
	} else if ( it.content.nav_css_type[it.content.tab] == 3 ){
		var l = 11;
	} else if ( it.content.nav_css_type[it.content.tab] == 4 ){
		var l = 11;
	}
}}
{{ for( var i = 0; i < l; i++ ) { }}
    <li class="ctrl-item-list-li clearfix" data-sort="{{= i}}" data-position="{{= it.content.tab}}">
        <div class="fl">
            <div class="imgnav j-selectimg">			
			<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{= i}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{= i}});">
				<input type="hidden" name="getImg" id='getImg{{= i}}' value="{{= it.content.dataset[it.content.tab][i].pic}}">
                <p class="imgnav-select">
                    <input type="file" size="20" name="upfile2" id="upfile2" class="up">
                    <img src="{{= it.content.dataset[it.content.tab][i].pic}}">
                </p>
				<input type="hidden" name="diy_tem_contid" value="{{= it.id}}">
				<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
				<input type="hidden" name="img_sort" value="{{= i}}">
                
			</form>
            </div>
            <span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
        </div>
		
        <div class="fl imgnav-info">
            <div class="formitems">  
                <label class="fi-name">链接到：</label>  
				<div class="radio-group" style="padding-top:0;">
					<label><input type="radio" class="link_type" name="link_type_{{= i}}" value="" {{? it.content.dataset[it.content.tab][i].link_type != 1}}checked{{?}}>PC商城</label>
					<label><input type="radio" class="link_type" name="link_type_{{= i}}" value="1" {{? it.content.dataset[it.content.tab][i].link_type == 1}}checked{{?}}>链接网址</label>
				</div>
				{{? it.content.dataset[it.content.tab][i].link_type == 1}}
				<div class="form-controls">
					<input type="text" class="j-verify" name="link_address" value="{{=it.content.dataset[it.content.tab][i].link}}" placeholder="请输入网址，必须以http://开头">
				</div>
				{{??}}
                <div class="form-controls">
                    <div class="droplist">
						<select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
                        {{? it.fixed_link}}
                        	{{ select_value=0; }}
                        	{{? it.content.dataset[it.content.tab][i].select_value}}
                        		{{	selv=it.content.dataset[it.content.tab][i].select_value.split("_");
									select_value=selv[0];
								}}
							{{?}}
							{{	for( k=0,m=it.fixed_link.length; k<m; k++ ) { 
                                fl = it.fixed_link[k].split("_");
                            }}
							<option value="2_{{=fl[0]}}" {{? fl[0]==select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
						{{?}}
						{{? it.type_arr}}
							<optgroup label="------------产品分类------------"></optgroup>
							<option value="3" {{? it.content.dataset[it.content.tab][i].link_type==3}} selected="selected"{{?}}>多级分类</option>
						{{?}}
						{{? it.brand_arr}}
							<optgroup label="------------品牌供应商----------"></optgroup>
							<option value="4" {{? it.content.dataset[it.content.tab][i].link_type==4}} selected="selected"{{?}}>品牌供应商店铺</option>
						{{?}}
						<?php if($supply_id<0){?>
						{{? it.template_link}}
						<optgroup label="-------------活动页--------------"></optgroup>
							<option value="5" {{? it.content.dataset[it.content.tab][i].link_type==5}} selected="selected"{{?}}>活动页模板</option>
						{{?}}
						<!--{{? it.room_link}}
						<optgroup label="----------微视直播系统-----------"></optgroup>
							<option value="7" {{? it.content.dataset[it.content.tab][i].link_type==7}} selected="selected"{{?}}>直播房间</option>
						{{?}}-->
						<?php }?>
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<optgroup label="----------产品分类---------"></optgroup>
							<option value="6" {{? it.content.dataset[it.content.tab][i].link_type==6}} selected="selected"{{?}}>产品分类</option>
						{{?}}
                        </select>
						<!-- 产品分类 -->
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_{{=i}}"  class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=3}}display:none;{{?}}height:28px;">
							{{for ( k=0,m=it.type_arr[-1].length; k<m; k++ ) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}"{{? type_id==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}"{{? ctype_id==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}"{{? ctype_id3==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}"{{? ctype_id4==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
						</select>
						{{?}}
						<!-- 品牌供应商店铺 -->
						{{? it.brand_arr}}
						<select name="brand_supply" id="brand_supply_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=4}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.brand_arr.length; k<m; k++ ) {
								supply_id_name = it.brand_arr[k].split("_");
							}}
							<option value="{{= supply_id_name[0]}}" {{? supply_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 礼包列表 -->
						{{? it.package_lists}}
                        {{ select_value=0; }}
						{{? it.content.dataset[it.content.tab][i].select_value}}
                    		{{	selv=it.content.dataset[it.content.tab][i].select_value.split("_");
								select_value=selv[0];
							}}
						{{?}}
						<select name="brand_supply" id="brand_supply_80" class="input xlarge" style="{{? select_value!=-10}}display:none;{{?}}height:28px;" >
						    <option value="-10" >全部礼包</option>
						    {{for ( k=0,m=it.package_lists.length; k<m; k++ ) {
						        supply_id_name = it.package_lists[k].split("_");
						    }}
						    <option value="-10_{{= supply_id_name[0]}}" {{? "-10_"+supply_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
						    {{ } }}
						</select>
						{{?}}
						<!-- 其他模板 -->
						{{? it.template_link}}
						<select name="template_link" id="template_link_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=5}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.template_link.length; k<m; k++ ) {
								template_id_name = it.template_link[k].split("_");
							}}
							<option value="{{= template_id_name[0]}}" {{? template_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= template_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 微视直播房间 -->
						<!--{{? it.room_link}}
						<select name="room_link" id="room_link_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=7}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.room_link.length; k<m; k++ ) {
								room_id_name = it.room_link[k].split("_");
							}}
							<option value="{{= room_id_name[0]}}" {{? room_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= room_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}-->
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<select name="supply_type" id="supply_type_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=6}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.supply_type_arr.length; k<m; k++ ) {
								supply_type_id_name = it.supply_type_arr[k].split("_");
							}}
							<option value="{{= supply_type_id_name[0]}}" {{? supply_type_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= supply_type_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 产品 -->		
                        <div id="div_products_2_{{=i}}" style="display:none;" >
                            <select name="product_detail_id_2" id="product_detail_id_2_{{=i}}" class="input xlarge" style="height:28px;">
                                
							</select>
						</div>
					</div>
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
                </div>
				{{?}}
			</div>
			<div class="formitems">  
				<!-- 样式一建议尺寸 -->
				{{
					size_str = '';
					if ( it.content.nav_css_type[it.content.tab] == 1 ){
						switch (i){
							case 0: size_str = '300*444';
							break;
							
							case 1: size_str = '439*240';
							break;
							
							case 2:
							case 3: size_str = '219*240';
							break;
							
							case 4:
							case 5:
							case 6:
							case 7: size_str = '219*201';
							break;
						}
					}
				}}
				<!-- 样式二建议尺寸 -->
				{{
					if ( it.content.nav_css_type[it.content.tab] == 2 ) {
						switch (i){
							case 0: size_str = '300*414';
							break;
							
							case 1:
							case 2:
							case 3: size_str = '235*210';
							break;
							
							case 4:
							case 5: size_str = '353*198';
							break;
							
							case 6:
							case 7:
							case 8: size_str = '163*137';
							break;
						}
					}
				}}
				<!-- 样式三建议尺寸 -->
				{{
					if ( it.content.nav_css_type[it.content.tab] == 3 ) {
						switch (i){
							case 0: size_str = '300*416';
							break;
							
							case 1:
							case 2:
							case 3:
							case 6:
							case 7:
							case 8: size_str = '218*204';
							break;
							
							case 4:
							case 5:
							case 9:
							case 10: size_str = '212*101';
							break;
						}
					}
				}}
				<!-- 样式四建议尺寸 -->
				{{
					if ( it.content.nav_css_type[it.content.tab] == 4 ) {
						switch (i){
							case 0: size_str = '300*444';
							break;
							
							case 1:
							case 2:
							case 3:
							case 4:
							case 5:
							case 6:
							case 7:
							case 8:
							case 9:
							case 10: size_str = '174*221';
							break;
						}
					}
				}}
                <label class="fi-name">建议尺寸：</label>
                <label class="note">{{= size_str}}</label><!-- 显示建议尺寸 -->
            </div>
        </div>
    </li>
	{{ } }}
</ul>
</script>
<!--楼层专区-->
<!--活动模板块-->
<script type="text/j-template" id="type_con_5">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
	<div class="child-nav-main">
        <div class="child-nav-left">
            <div class="nav child-nav-top">
			{{? it.content.is_show[1] == 1 }}
                <div class="nav-brand-main {{? it.content.tab == 1 }}main-border-bottom3{{?}}">
                    <a>{{= it.content.dataset[1][0].title}}<img src="img/Home/icon-triangle.png"></a>
                </div>
			{{?}}
			{{? it.content.is_show[2] == 1 }}
                <div class="nav-brand-main {{? it.content.tab == 2 }}main-border-bottom3{{?}}">
					<a>{{= it.content.dataset[2][0].title}}<img src="img/Home/icon-triangle.png"></a>
				</div>
			{{?}}
			{{? it.content.is_show[3] == 1 }}
                <div class="nav-brand-main {{? it.content.tab == 3 }}main-border-bottom3{{?}}">
					<a>{{= it.content.dataset[3][0].title}}<img src="img/Home/icon-triangle.png"></a>
				</div>
			{{?}}
            </div>
            <div class="nav-brand-img">
			{{? it.content.is_show[it.content.tab] == 1 }}
				{{ for ( var i=0,l=it.content.dataset[it.content.tab].length; i < l; i++ ) { }}
                <a href="#" target="_blank"><img src="{{= it.content.dataset[it.content.tab][i].pic}}"></a>
				{{ } }}
			{{?}}
            </div>
        </div>
        <div class="child-nav-right">
            <p>{{= it.content.dataset[0][0].title}}</p>
            <img src="{{= it.content.dataset[0][0].pic}}">
            <div class="child-nav-time child-nav-time-start_{{= it.id}}">
                <span class="child-time-span-tip_{{= it.id}}">距结束仅剩：</span>
				<span class="child-time-span" id="day_{{= it.id}}"></span><span>天</span>
                <span class="child-time-span" id="hour_{{= it.id}}"></span><span>时</span>
                <span class="child-time-span" id="minute_{{= it.id}}"></span><span>分</span>
                <span class="child-time-span" id="second_{{= it.id}}"></span><span>秒</span>
            </div>
			<div class="child-nav-time child-nav-time-end_{{= it.id}}">
                <span class="child-time-span-tip-end">已结束！</span>
            </div>
        </div>
    </div>
</div>
</script>
<script type="dot-template" id="type_ctrl_5">
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
<div class="formitems">  
    <label class="fi-name pc-fi-name">推荐标题：</label>   
    <input type="text" name="title" class="input pc-title-input" style="float:left;" value="{{= it.content.dataset[0][0].title}}" maxlength="6">
</div>
<ul> 
	<li>推荐区域：</li>
    <li class="ctrl-item-list-li clearfix" data-sort="50" data-position="0">
        <div class="fl">
            <div class="imgnav j-selectimg">
            <form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img50" enctype="multipart/form-data" method="post" onsubmit="return saveReport(50);">
                <input type="hidden" name="getImg" id='getImg50' value="{{= it.content.dataset[0][0].pic}}">
                <p class="imgnav-select">
                    <input type="file" size="20" name="upfile2" id="upfile2" class="up">
                    <img src="{{= it.content.dataset[0][0].pic}}">
                </p>
                <input type="hidden" name="diy_tem_contid" value="{{= it.id}}">
                <input type="hidden" name="diy_temid" value="<?php echo $diy_temid;?>">
                <input type="hidden" name="img_sort" value="0">
            </form>
            </div>
            <span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
        </div>
        <div class="fl imgnav-info">
            <div class="formitems">  
                <label class="fi-name">链接到：</label>  
				<div class="radio-group" style="padding-top:0;">
					<label><input type="radio" class="link_type" name="link_type_50" value="" {{? it.content.dataset[0][0].link_type != 1}}checked{{?}}>PC商城</label>
					<label><input type="radio" class="link_type" name="link_type_50" value="1" {{? it.content.dataset[0][0].link_type == 1}}checked{{?}}>链接网址</label>
				</div>
				{{? it.content.dataset[0][0].link_type == 1}}
				<div class="form-controls">
					<input type="text" class="j-verify" name="link_address" value="{{=it.content.dataset[0][0].link}}" placeholder="请输入网址，必须以http://开头">
				</div>
				{{??}}
                <div class="form-controls">
                    <div class="droplist">
						<select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
						{{? it.fixed_link}}
							{{ select_value=0; }}
							{{? it.content.dataset[0][0].select_value}}
		                        {{	selv=it.content.dataset[0][0].select_value.split("_");
									select_value=selv[0];
		                        }}
                        	{{?}}
							{{	for( k=0,m=it.fixed_link.length; k<m; k++ ) { 
                                fl = it.fixed_link[k].split("_");
                            }}
							<option value="2_{{=fl[0]}}" {{? fl[0]==select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
						{{?}}
						{{? it.type_arr}}
							<optgroup label="------------产品分类------------"></optgroup>
							<option value="3" {{? it.content.dataset[0][0].link_type==3}} selected="selected"{{?}}>多级分类</option>
						{{?}}
						{{? it.brand_arr}}
							<optgroup label="--------------品牌供应商------------"></optgroup>
							<option value="4" {{? it.content.dataset[0][0].link_type==4}} selected="selected"{{?}}>品牌供应商店铺</option>
						{{?}}
						<?php if($supply_id<0){?>
						{{? it.template_link}}
						<optgroup label="-----------------活动页------------------"></optgroup>
							<option value="5" {{? it.content.dataset[0][0].link_type==5}} selected="selected"{{?}}>活动页模板</option>
						{{?}}
						<!--{{? it.room_link}}
						<optgroup label="----------微视直播系统-----------"></optgroup>
							<option value="7" {{? it.content.dataset[0][0].link_type==7}} selected="selected"{{?}}>直播房间</option>
						{{?}}-->
						<?php }?>
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<optgroup label="----------产品分类---------"></optgroup>
							<option value="6" {{? it.content.dataset[0][0].link_type==6}} selected="selected"{{?}}>产品分类</option>
						{{?}}
                        </select>
						<!-- 产品分类 -->
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_50"  class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=3}}display:none;{{?}}height:28px;">
							{{for ( k=0,m=it.type_arr[-1].length; k<m; k++ ) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}"{{? type_id==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}"{{? ctype_id==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}"{{? ctype_id3==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}"{{? ctype_id4==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
						</select>
						{{?}}
						<!-- 品牌供应商店铺 -->
						{{? it.brand_arr}}
						<select name="brand_supply" id="brand_supply_50" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=4}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.brand_arr.length; k<m; k++ ) {
								supply_id_name = it.brand_arr[k].split("_");
							}}
							<option value="{{= supply_id_name[0]}}" {{? supply_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 礼包列表 -->
						{{? it.package_lists}}
						{{? it.content.dataset[0][0].select_value}}
	                        {{	selv=it.content.dataset[0][0].select_value.split("_");
								select_value=selv[0];
	                        }}
                    	{{?}}
						<select name="brand_supply" id="brand_supply_80" class="input xlarge" style="{{? select_value!=-10}}display:none;{{?}}height:28px;" >
						    <option value="-10" >全部礼包</option>
						    {{for ( k=0,m=it.package_lists.length; k<m; k++ ) {
						        supply_id_name = it.package_lists[k].split("_");
						    }}
						    <option value="-10_{{= supply_id_name[0]}}" {{? "-10_"+supply_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
						    {{ } }}
						</select>
						{{?}}
						<!-- 其他模板 -->
						{{? it.template_link}}
						<select name="template_link" id="template_link_50" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=5}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.template_link.length; k<m; k++ ) {
								template_id_name = it.template_link[k].split("_");
							}}
							<option value="{{= template_id_name[0]}}" {{? template_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= template_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 微视直播房间 -->
						<!--{{? it.room_link}}
						<select name="room_link" id="room_link_50" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=7}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.room_link.length; k<m; k++ ) {
								room_id_name = it.room_link[k].split("_");
							}}
							<option value="{{= room_id_name[0]}}" {{? room_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= room_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}-->
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<select name="supply_type" id="supply_type_50" class="input xlarge" style="{{? it.content.dataset[0][0].link_type!=6}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.supply_type_arr.length; k<m; k++ ) {
								supply_type_id_name = it.supply_type_arr[k].split("_");
							}}
							<option value="{{= supply_type_id_name[0]}}" {{? supply_type_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= supply_type_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 产品 -->
                        <div id="div_products_2_50" style="display:none;" >
                            <select name="product_detail_id_2" id="product_detail_id_2_50" class="input xlarge" style="height:28px;">
                                
							</select>
						</div>
                    </div>
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
                </div>
				{{?}}
            </div>
            <div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">331*152 px</label>
            </div>
			<label style="color:red;">（如果选择的是抢购产品，则使用产品的抢购时间）</label>
			<div class="formitems">  
                <label class="fi-name">开始时间：</label>
                <label class="note"><input type="text" style="width:150px;border:1px solid #ddd;" id="starttime" name="start_time" value="{{= it.content.dataset[0][0].start_time}}" ></label>
            </div>
			<div class="formitems">  
                <label class="fi-name">结束时间：</label>
                <label class="note"><input type="text" style="width:150px;border:1px solid #ddd;" id="endtime" name="end_time" value="{{= it.content.dataset[0][0].end_time}}" ></label>
            </div>
        </div>
    </li>
</ul>
<div class="pc-line"></div>
<div class="formitems">  
    <ul class="pc-brand-tab pc-brand-tab3">
		<li><span class="pc-brand-tab-data {{? it.content.tab == 1}}pc-brand-tab-selected{{?}}" data-tab="1">{{= it.content.dataset[1][0].title}}</span></li>
		<li><span class="pc-brand-tab-data {{? it.content.tab == 2}}pc-brand-tab-selected{{?}}" data-tab="2">{{= it.content.dataset[2][0].title}}</span></li>
		<li><span class="pc-brand-tab-data {{? it.content.tab == 3}}pc-brand-tab-selected{{?}}" data-tab="3">{{= it.content.dataset[3][0].title}}</span></li>
	</ul>
</div>
<div class="formitems">  
    <label class="fi-name pc-fi-name">导航标题：</label>   
    <input type="text" name="nav_title" class="input pc-title-input" value="{{= it.content.dataset[it.content.tab][0].title}}" maxlength="6">
</div>
<div class="formitems">  
    <label class="fi-name pc-fi-name">是否显示：</label>   
    <div class="radio-group" style="padding-top:0;">
        <label><input type="radio" name="pc_brand_tab_show" value="1" {{? it.content.is_show[it.content.tab] == 1}}checked{{?}}>显示</label>
        <label><input type="radio" name="pc_brand_tab_show" value="0" {{? it.content.is_show[it.content.tab] == 0}}checked{{?}}>隐藏</label>
    </div>
</div>
<ul class="ctrl-item-list">
{{ for( var i = 0,l = it.content.dataset[it.content.tab].length; i < l; i++ ) { }}
    <li class="ctrl-item-list-li clearfix" data-sort="{{= i}}" data-position="{{= it.content.tab}}">
        <div class="fl">
            <div class="imgnav j-selectimg">			
			<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{= i}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{= i}});">
				<input type="hidden" name="getImg" id='getImg{{= i}}' value="{{= it.content.dataset[it.content.tab][i].pic}}">
                <p class="imgnav-select">
                    <input type="file" size="20" name="upfile2" id="upfile2" class="up">
                    <img src="{{= it.content.dataset[it.content.tab][i].pic}}">
                </p>
				<input type="hidden" name="diy_tem_contid" value="{{= it.id}}">
				<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
				<input type="hidden" name="img_sort" value="{{= i}}">
                
			</form>
            </div>
            <span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
        </div>
		
        <div class="fl imgnav-info">
            <div class="formitems">  
                <label class="fi-name">链接到：</label>  
				<div class="radio-group" style="padding-top:0;">
					<label><input type="radio" class="link_type" name="link_type_{{= i}}" value="" {{? it.content.dataset[it.content.tab][i].link_type != 1}}checked{{?}}>PC商城</label>
					<label><input type="radio" class="link_type" name="link_type_{{= i}}" value="1" {{? it.content.dataset[it.content.tab][i].link_type == 1}}checked{{?}}>链接网址</label>
				</div>
				{{? it.content.dataset[it.content.tab][i].link_type == 1}}
				<div class="form-controls">
					<input type="text" class="j-verify" name="link_address" value="{{=it.content.dataset[it.content.tab][i].link}}" placeholder="请输入网址，必须以http://开头">
				</div>
				{{??}}
                <div class="form-controls">
                    <div class="droplist">
						<select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
                        {{? it.fixed_link}}
                        	{{ select_value=0; }}
                       		{{? it.content.dataset[it.content.tab][i].select_value}}
                        		{{	selv=it.content.dataset[it.content.tab][i].select_value.split("_");
									select_value=selv[0];
								}}
							{{?}}
							{{	for( k=0,m=it.fixed_link.length; k<m; k++ ) { 
                                fl = it.fixed_link[k].split("_");
                            }}
							<option value="2_{{=fl[0]}}" {{? fl[0]==select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
						{{?}}
						{{? it.type_arr}}
							<optgroup label="------------产品分类------------"></optgroup>
							<option value="3" {{? it.content.dataset[it.content.tab][i].link_type==3}} selected="selected"{{?}}>多级分类</option>
						{{?}}
						{{? it.brand_arr}}
							<optgroup label="------------品牌供应商----------"></optgroup>
							<option value="4" {{? it.content.dataset[it.content.tab][i].link_type==4}} selected="selected"{{?}}>品牌供应商店铺</option>
						{{?}}
						<?php if($supply_id<0){?>
						{{? it.template_link}}
						<optgroup label="-------------活动页--------------"></optgroup>
							<option value="5" {{? it.content.dataset[it.content.tab][i].link_type==5}} selected="selected"{{?}}>活动页模板</option>
						{{?}}
						<!--{{? it.room_link}}
						<optgroup label="----------微视直播系统-----------"></optgroup>
							<option value="7" {{? it.content.dataset[it.content.tab][i].link_type==7}} selected="selected"{{?}}>直播房间</option>
						{{?}}-->
						<?php }?>
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<optgroup label="----------产品分类---------"></optgroup>
							<option value="6" {{? it.content.dataset[it.content.tab][i].link_type==6}} selected="selected"{{?}}>产品分类</option>
						{{?}}
                        </select>
						<!-- 产品分类 -->
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_{{=i}}"  class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=3}}display:none;{{?}}height:28px;">
							{{for ( k=0,m=it.type_arr[-1].length; k<m; k++ ) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}"{{? type_id==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}"{{? ctype_id==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}"{{? ctype_id3==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}"{{? ctype_id4==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
						</select>
						{{?}}
						<!-- 品牌供应商店铺 -->
						{{? it.brand_arr}}
						<select name="brand_supply" id="brand_supply_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=4}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.brand_arr.length; k<m; k++ ) {
								supply_id_name = it.brand_arr[k].split("_");
							}}
							<option value="{{= supply_id_name[0]}}" {{? supply_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 礼包列表 -->
						{{? it.package_lists}}
                        {{ select_value=0; }}
						{{? it.content.dataset[it.content.tab][i].select_value}}
                    		{{	selv=it.content.dataset[it.content.tab][i].select_value.split("_");
								select_value=selv[0];
							}}
						{{?}}
						<select name="brand_supply" id="brand_supply_80" class="input xlarge" style="{{? select_value!=-10}}display:none;{{?}}height:28px;" >
						    <option value="-10" >全部礼包</option>
						    {{for ( k=0,m=it.package_lists.length; k<m; k++ ) {
						        supply_id_name = it.package_lists[k].split("_");
						    }}
						    <option value="-10_{{= supply_id_name[0]}}" {{? "-10_"+supply_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
						    {{ } }}
						</select>
						{{?}}
						<!-- 其他模板 -->
						{{? it.template_link}}
						<select name="template_link" id="template_link_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=5}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.template_link.length; k<m; k++ ) {
								template_id_name = it.template_link[k].split("_");
							}}
							<option value="{{= template_id_name[0]}}" {{? template_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= template_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 微视直播房间 -->
						<!--{{? it.room_link}}
						<select name="room_link" id="room_link_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=7}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.room_link.length; k<m; k++ ) {
								room_id_name = it.room_link[k].split("_");
							}}
							<option value="{{= room_id_name[0]}}" {{? room_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= room_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}-->
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<select name="supply_type" id="supply_type_{{= i}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][i].link_type!=6}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.supply_type_arr.length; k<m; k++ ) {
								supply_type_id_name = it.supply_type_arr[k].split("_");
							}}
							<option value="{{= supply_type_id_name[0]}}" {{? supply_type_id_name[0]==it.content.dataset[it.content.tab][i].select_value}} selected="selected"{{?}}>{{= supply_type_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 产品 -->
                        <div id="div_products_2_{{=i}}" style="display:none;" >
                            <select name="product_detail_id_2" id="product_detail_id_2_{{=i}}" class="input xlarge" style="height:28px;">
                                
							</select>
						</div>
					</div>
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
                </div>
				{{?}}
			</div>
			<div class="formitems">  
                <label class="fi-name">建议尺寸：</label>
                <label class="note">205*142 px</label>
            </div>
        </div>
        <div class="ctrl-item-list-actions">
            <a href="javascript:;" title="上移" class="j-moveup"><i class="gicon-arrow-up"></i></a>
            <a href="javascript:;" title="下移" class="j-movedown"><i class="gicon-arrow-down"></i></a>
            <a href="javascript:;" title="删除" class="j-del"><i class="gicon-remove"></i></a>
        </div>
    </li>
	{{ } }}
	<!-- 最多四张图片 -->
	{{? it.content.dataset[it.content.tab].length < 4 }}
    <li class="ctrl-item-list-add" title="添加">+</li>
	{{?}}
</ul>
</script>
<!-- 活动模板块 -->
<!-- 多分类橱窗 -->
<script type="text/j-template" id="type_con_6">
{{ 
	var show_count = 0;	
	for(var i=0;i<6;i++){
		if(it.content.is_show[i]==1){
			show_count++;
		}
	}
}}
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
	<div class="horizontalTab-liebiao" >
		<ul class="tab-nav-liebiao height-82">
			{{ for(p=0;p<6;p++){ }}
				{{? it.content.is_show[p]==1}}
				<li id="li1-{{=p+1}}" class="li-nav-liebiao {{? it.content.tab==(p+1)}}li-nav-hover-liebiao{{?}} nav-t-{{=show_count}}">
					<div class="tab-sel-img nav-b-{{=show_count}}" >
						<img class="sel-img-white" src="{{=it.content.dataset[0][p].pic}}" />
					</div>
					<div class="margin-left-40">
						{{=it.content.dataset[0][p].title}}
					</div>
					<div class="tab-tail-img {{? it.content.tab==(p+1)}}tail-show{{?}}">
						<img src="img/liebiaoye/tab-tail.png" />
					</div>					
				</li>
				{{?}}
			{{ } }}
		</ul>
		<div id="box1-1" class="tab-inner-left-liebiao hiddenBox1 tab-box" {{? it.content.nav_css_type[it.content.tab - 1]==1}}style="display: block;"{{?}}>
			<div class="tab-content-left">
				<img class="tab-content-left-img" src="{{=it.content.dataset[it.content.tab][0].pic}}">
			</div>
			<ul class="tab-content-center">
				<li class="photo1-li3">
					<img class="photo1-li3-img" src="{{=it.content.dataset[it.content.tab][1].pic}}">
				</li>
				<li class="photo1-li3-right">
					<img class="photo1-li3-img" src="{{=it.content.dataset[it.content.tab][2].pic}}">
				</li>
				<li class="photo1-li3">
					<img class="photo1-li3-img" src="{{=it.content.dataset[it.content.tab][3].pic}}">
				</li>
				<li class="photo1-li3-right">
					<img class="photo1-li3-img" src="{{=it.content.dataset[it.content.tab][4].pic}}">
				</li>
			</ul>
			<div class="tab-content-right">
				<img src="{{=it.content.dataset[it.content.tab][5].pic}}">
			</div>
			<div class="footer-ad">
				<img src="{{=it.content.dataset[it.content.tab][6].pic}}" />
			</div>
		</div><!-- end of tab-1 -->
		<div id="box1-2" class="hiddenBox1 tab-box" {{? it.content.nav_css_type[it.content.tab - 1]==2}}style="display: block;"{{?}}>
			<div class="tab-content-left tcl-2">
				<img class="photo-left-spec-2" src="{{=it.content.dataset[it.content.tab][0].pic}}">
			</div>
			<div class="tab-content-left2 tcl-3">
				<img src="{{=it.content.dataset[it.content.tab][1].pic}}">
			</div>
			<div class="tab-content-right">
				<ul class="tab-content-right">
					<li class="photo1-li3-2">
						<img class="photo1-li3-img2" src="{{=it.content.dataset[it.content.tab][2].pic}}">
					</li>
					<li class="photo1-li3-2">
						<img class="photo1-li3-img2" src="{{=it.content.dataset[it.content.tab][3].pic}}">
					</li>
				</ul>
			</div>
			<div class="footer-ad">
				<img  src="{{=it.content.dataset[it.content.tab][4].pic}}" />
			</div>
		</div>
		<div id="box1-3" class="hiddenBox1 tab-box" {{? it.content.nav_css_type[it.content.tab - 1]==3}}style="display: block;"{{?}}>
			<div class="photos-box">
				<ul>
					<li>
						<img class="photo9" src="{{=it.content.dataset[it.content.tab][0].pic}}">
					</li>
					<li>
						<img class="photo9" src="{{=it.content.dataset[it.content.tab][1].pic}}">
					</li>
					<li>
						<img class="photo9" src="{{=it.content.dataset[it.content.tab][2].pic}}">
					</li>
					<li>
						<img class="photo9" src="{{=it.content.dataset[it.content.tab][3].pic}}">
					</li>
				</ul>
			</div>
			<div class="footer-ad3">
				<img  src="{{=it.content.dataset[it.content.tab][4].pic}}" />
				<img  src="{{=it.content.dataset[it.content.tab][5].pic}}" />
				<img  src="{{=it.content.dataset[it.content.tab][6].pic}}" />
				<img  src="{{=it.content.dataset[it.content.tab][7].pic}}" />
				<img  src="{{=it.content.dataset[it.content.tab][8].pic}}" />
				<img  src="{{=it.content.dataset[it.content.tab][9].pic}}" />
				<img  src="{{=it.content.dataset[it.content.tab][10].pic}}" />
				<img  src="{{=it.content.dataset[it.content.tab][11].pic}}" />
				<img  src="{{=it.content.dataset[it.content.tab][12].pic}}" />
			</div>
		</div>
		<div id="box1-4" class="hiddenBox1 tab-box">tab-4</div>
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
    <ul class="pc-brand-tab pc-brand-tab6">
		{{ for(p=0;p<6;p++){ }}
		<li><span class="pc-brand-tab-data {{? it.content.tab == p+1}}pc-brand-tab-selected{{?}}" data-tab="{{=p+1}}">{{= it.content.dataset[0][p].title}}</span></li>
		{{ } }}
	</ul>
</div>
<div class="pc-line"></div>
<li class="ctrl-item-list-li clearfix" data-sort="0" data-position="0"> 
	<div class="formitems">
		<label class="fi-name pc-fi-name">是否显示：</label> 
		<div class="radio-group">
			<label><input type="radio" name="is_show" value="1"{{? it.content.is_show[it.content.tab - 1]==1}} checked{{?}}>显示</label>
			<label><input type="radio" name="is_show" value="0"{{? it.content.is_show[it.content.tab - 1]==0}} checked{{?}}>隐藏</label>
		</div>	
	</div>
	<div class="formitems">  
		<label class="fi-name pc-fi-name">分类标题：</label>   
		<input type="text" name="nav_title" class="input pc-title-input" value="{{= it.content.dataset[0][it.content.tab - 1].title}}" maxlength="4">
	</div>
	<div class="formitems">  
		<label class="fi-name pc-fi-name">标题图标：</label>   
	</div>
    <div class="fl">
		<div class="imgnav j-selectimg" data-tab="{{=it.content.tab}}">			
		<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_imgnav{{=it.content.tab}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport('nav{{=it.content.tab}}');">
			<input type="hidden" name="getImg" id='getImgnav{{=it.content.tab}}' value="{{= it.content.dataset[0][it.content.tab - 1].pic}}">
			<p class="imgnav-select">
				<input type="file" size="20" name="upfile2" id="upfile2" class="up" >
				<img src="{{= it.content.dataset[0][it.content.tab - 1].pic}}">
			</p>
			<input type="hidden" name="diy_tem_contid" value="{{= it.id}}">
			<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
			<input type="hidden" name="img_sort" value="0">
			
		</form>
		</div>
		<span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
	</div>
	<div class="fl imgnav-info">
		<div class="formitems">  			
			<label class="fi-name">建议尺寸：</label>
			<label class="note">50*50 px</label>
		</div>
	</div>
</li>
<div class="formitems">  
	<label class="fi-name pc-fi-name">橱窗样式：</label>   
	<div class="radio-group" style="padding-top:0;">
		<label><input type="radio" name="nav_css_type" value="1"{{? it.content.nav_css_type[it.content.tab - 1] == 1}} checked{{?}}>样式一</label>
		<label><input type="radio" name="nav_css_type" value="2"{{? it.content.nav_css_type[it.content.tab - 1] == 2}} checked{{?}}>样式二</label>
		<label><input type="radio" name="nav_css_type" value="3"{{? it.content.nav_css_type[it.content.tab - 1] == 3}} checked{{?}}>样式三</label>
	</div>
</div>
<ul class="ctrl-item-list"> 
{{
	if ( it.content.nav_css_type[it.content.tab - 1] == 1 ){
		var l = 7;
	} else if ( it.content.nav_css_type[it.content.tab - 1] == 2 ){
		var l = 5;
	} else if ( it.content.nav_css_type[it.content.tab - 1] == 3 ){
		var l = 13;
	}
}}
	{{ for(p=0;p<l;p++){ }}
    <li class="ctrl-item-list-li clearfix" data-sort='{{=p}}' data-position="{{= it.content.tab}}">		
		<div class="fl">
			<div class="imgnav j-selectimg" data-tab='0'>			
			<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{=p}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{=p}});">
				<input type="hidden" name="getImg" id='getImg{{=p}}' value="{{= it.content.dataset[it.content.tab][p].pic}}">
				<p class="imgnav-select">
					<input type="file" size="20" name="upfile2" id="upfile2" class="up" >
					<img src="{{= it.content.dataset[it.content.tab][p].pic}}">
				</p>
				<input type="hidden" name="diy_tem_contid" value="{{= it.id}}">
				<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
				<input type="hidden" name="img_sort" value="{{= p}}">
				
			</form>
			</div>
			<span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
		</div>
		
		<div class="fl imgnav-info">
			<div class="formitems">  
				<label class="fi-name">链接到：</label>  
				<div class="radio-group" style="padding-top:0;">
					<label><input type="radio" class="link_type" name="link_type_{{=p}}" value="" {{? it.content.dataset[it.content.tab][p].link_type != 1}}checked{{?}}>PC商城</label>
					<label><input type="radio" class="link_type" name="link_type_{{=p}}" value="1" {{? it.content.dataset[it.content.tab][p].link_type == 1}}checked{{?}}>链接网址</label>
				</div>
				{{? it.content.dataset[it.content.tab][p].link_type == 1}}
				<div class="form-controls">
					<input type="text" class="j-verify" name="link_address" value="{{=it.content.dataset[it.content.tab][p].link}}" placeholder="请输入网址，必须以http://开头">
				</div>
				{{??}}
				<div class="form-controls">
					<div class="droplist">
						<select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
						{{? it.fixed_link}}
							{{select_value=0;}}
							{{? it.content.dataset[it.content.tab][p].select_value}}
								{{	selv=it.content.dataset[it.content.tab][p].select_value.split("_");
									select_value=selv[0];
								}}
							{{?}}
							{{	for( k=0,m=it.fixed_link.length; k<m; k++ ) { 
                                fl = it.fixed_link[k].split("_");
                            }}
							<option value="2_{{=fl[0]}}" {{? fl[0]==select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
						{{?}}
						{{? it.type_arr}}
							<optgroup label="------------产品分类------------"></optgroup>
							<option value="3" {{? it.content.dataset[it.content.tab][p].link_type==3}} selected="selected"{{?}}>多级分类</option>
						{{?}}
						{{? it.brand_arr}}
							<optgroup label="------------品牌供应商----------"></optgroup>
							<option value="4" {{? it.content.dataset[it.content.tab][p].link_type==4}} selected="selected"{{?}}>品牌供应商店铺</option>
						{{?}}
						<?php if($supply_id<0){?>
						{{? it.template_link}}
						<optgroup label="-------------活动页--------------"></optgroup>
							<option value="5" {{? it.content.dataset[it.content.tab][p].link_type==5}} selected="selected"{{?}}>活动页模板</option>
						{{?}}
						<!--{{? it.room_link}}
						<optgroup label="----------微视直播系统-----------"></optgroup>
							<option value="7" {{? it.content.dataset[it.content.tab][p].link_type==7}} selected="selected"{{?}}>直播房间</option>
						{{?}}-->
						<?php }?>
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<optgroup label="----------产品分类---------"></optgroup>
							<option value="6" {{? it.content.dataset[it.content.tab][p].link_type==6}} selected="selected"{{?}}>产品分类</option>
						{{?}}
                        </select>
						<!-- 产品分类 -->
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_{{=p}}"  class="input xlarge" style="{{? it.content.dataset[it.content.tab][p].link_type!=3}}display:none;{{?}}height:28px;">
							{{for ( k=0,m=it.type_arr[-1].length; k<m; k++ ) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}"{{? type_id==it.content.dataset[it.content.tab][p].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}"{{? ctype_id==it.content.dataset[it.content.tab][p].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}"{{? ctype_id3==it.content.dataset[it.content.tab][p].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}"{{? ctype_id4==it.content.dataset[it.content.tab][p].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
						</select>
						{{?}}
						<!-- 品牌供应商店铺 -->
						{{? it.brand_arr}}
						<select name="brand_supply" id="brand_supply_{{=p}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][p].link_type!=4}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.brand_arr.length; k<m; k++ ) {
								supply_id_name = it.brand_arr[k].split("_");
							}}
							<option value="{{= supply_id_name[0]}}" {{? supply_id_name[0]==it.content.dataset[it.content.tab][p].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 礼包列表 -->
						{{? it.package_lists}}
						{{select_value=0;}}
						{{? it.content.dataset[it.content.tab][p].select_value}}
							{{	selv=it.content.dataset[it.content.tab][p].select_value.split("_");
								select_value=selv[0];
							}}
						{{?}}
						<select name="brand_supply" id="brand_supply_80" class="input xlarge" style="{{? select_value!=-10}}display:none;{{?}}height:28px;" >
						    <option value="-10" >全部礼包</option>
						    {{for ( k=0,m=it.package_lists.length; k<m; k++ ) {
						        supply_id_name = it.package_lists[k].split("_");
						    }}
						    <option value="-10_{{= supply_id_name[0]}}" {{? "-10_"+supply_id_name[0]==it.content.dataset[it.content.tab][p].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
						    {{ } }}
						</select>
						{{?}}
						<!-- 其他模板 -->
						{{? it.template_link}}
						<select name="template_link" id="template_link_{{=p}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][p].link_type!=5}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.template_link.length; k<m; k++ ) {
								template_id_name = it.template_link[k].split("_");
							}}
							<option value="{{= template_id_name[0]}}" {{? template_id_name[0]==it.content.dataset[it.content.tab][p].select_value}} selected="selected"{{?}}>{{= template_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 微视直播房间 -->
						<!--{{? it.room_link}}
						<select name="room_link" id="room_link_{{=p}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][p].link_type!=7}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.room_link.length; k<m; k++ ) {
								room_id_name = it.room_link[k].split("_");
							}}
							<option value="{{= room_id_name[0]}}" {{? room_id_name[0]==it.content.dataset[it.content.tab][p].select_value}} selected="selected"{{?}}>{{= room_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}-->
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<select name="supply_type" id="supply_type_{{= p}}" class="input xlarge" style="{{? it.content.dataset[it.content.tab][p].link_type!=6}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.supply_type_arr.length; k<m; k++ ) {
								supply_type_id_name = it.supply_type_arr[k].split("_");
							}}
							<option value="{{= supply_type_id_name[0]}}" {{? supply_type_id_name[0]==it.content.dataset[it.content.tab][p].select_value}} selected="selected"{{?}}>{{= supply_type_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 产品 -->
						<div id="div_products_2_{{=p}}" style="display:none;" >
							<select name="product_detail_id_2" id="product_detail_id_2_{{=p}}" class="input xlarge" style="height:28px;">
								
							</select>
						</div>
					</div>
					<input type="hidden" class="j-verify" name="item_id" value="">
					<span class="fi-help-text j-verify-linkType"></span>
				</div>
				{{?}}
			</div>
			<div class="formitems">  
				<label class="fi-name">建议尺寸：</label>
				{{? it.content.nav_css_type[it.content.tab - 1]==1}}	
					{{? p==0}}
					<label class="note">580*580 px</label>
					{{?? p==1 || p==2 || p==3 || p==4}}
					<label class="note">220*298 px</label>
					{{?? p==5}}
					<label class="note">220*586 px</label>
					{{?? p==6}}
					<label class="note">1200*120 px</label>
					{{?}}
				{{?}}
				{{? it.content.nav_css_type[it.content.tab - 1]==2}}	
					{{? p==0}}
					<label class="note">580*580 px</label>
					{{?? p==1}}
					<label class="note">350*587 px</label>
					{{?? p==2 || p==3}}
					<label class="note">300*286 px</label>
					{{?? p==4}}
					<label class="note">1200*120 px</label>
					{{?}}
				{{?}}
				{{? it.content.nav_css_type[it.content.tab - 1]==3}}	
					{{? p==0 || p==1 || p==2 || p==3}}
					<label class="note">280*560 px</label>
					{{?? p==4 || p==5 || p==6 || p==7 || p==8 || p==9 || p==10 || p==11 || p==12}}
					<label class="note">140*123 px</label>
					{{?}}
				{{?}}
			</div>
		</div>

        <div class="ctrl-item-list-actions">
            <!--<a href="javascript:;" title="上移" class="j-moveup"><i class="gicon-arrow-up"></i></a>
            <a href="javascript:;" title="下移" class="j-movedown"><i class="gicon-arrow-down"></i></a>-->
        </div>
    </li>
    {{ } }}
</ul>
</script>
<!-- 多分类橱窗 -->
<!--分类产品-->
<script  type="dot-template" id="type_con_7">
<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
	{{? it.content.css_type == 1 }}
	<ul class="sx-cont-main">
		<li>
			<img src="images/img-product.jpg">
			<div class="sx-cell-cont">
				<h3>{{? it.content.pro_name_show == 1 }}产品名称{{?}}</h3>
				<div class="bottom-btn">¥17.5</div>
			{{? it.content.show_sale == 1 }}
				<div class="bottom-sale">销量：12</div>
			{{?}}
			</div>
		</li>
		<li>
			<img src="images/img-product.jpg">
			<div class="sx-cell-cont">
				<h3>{{? it.content.pro_name_show == 1 }}产品名称{{?}}</h3>
				<div class="bottom-btn">¥59.2<span>¥99.9</span></div>
			{{? it.content.show_sale == 1 }}
				<div class="bottom-sale">销量：12</div>
			{{?}}
			</div>
		</li>
		<li>
			<img src="images/img-product.jpg">
			<div class="sx-cell-cont">
				<h3>{{? it.content.pro_name_show == 1 }}产品名称{{?}}</h3>
				<div class="bottom-btn">¥9.9<span>¥19.9</span></div>
			{{? it.content.show_sale == 1 }}
				<div class="bottom-sale">销量：12</div>
			{{?}}
			</div>
		</li>
		<li>
			<img src="images/img-product.jpg">
			<div class="sx-cell-cont">
				<h3>{{? it.content.pro_name_show == 1 }}产品名称{{?}}</h3>
				<div class="bottom-btn">¥12.8</div>
			{{? it.content.show_sale == 1 }}
				<div class="bottom-sale">销量：12</div>
			{{?}}
			</div>
		</li>
		<div class="empty-div"></div>
	</ul>
	{{?? it.content.css_type == 2 }}
	<ul class="sx-cont-sub">
        <li>
            <a>
				<img class="pro_img_4" src="images/img-product.jpg">
				{{? it.content.pro_name_show == 1 }}
				<h3>产品名称</h3>
				{{?}}
			</a>
            <p class="pro_price_p"><span class="red" style="font-size: 12px;">¥339.00</span></p>
			{{? it.content.show_sale == 1 }}
			<p class="show_sale_p"><span style="font-size: 12px;">销量：11</span></p>
			{{?}}
        </li>
        <li>
            <a>
				<img class="pro_img_4" src="images/img-product.jpg">
				{{? it.content.pro_name_show == 1 }}
				<h3>产品名称</h3>
				{{?}}
			</a>
            <p class="pro_price_p"><span class="red" style="font-size: 12px;">¥12.80</span><span class="old_price">¥32.80</span></p>
			{{? it.content.show_sale == 1 }}
			<p class="show_sale_p"><span style="font-size: 12px;">销量：11</span></p>
			{{?}}
        </li>
        <li>
            <a>
				<img class="pro_img_4" src="images/img-product.jpg">
				{{? it.content.pro_name_show == 1 }}
				<h3>产品名称</h3>
				{{?}}
			</a>
            <p class="pro_price_p"><span class="red" style="font-size: 12px;">¥28.80</span></p>
			{{? it.content.show_sale == 1 }}
			<p class="show_sale_p"><span style="font-size: 12px;">销量：11</span></p>
			{{?}}
        </li>
        <li class="sx-cell-right">
            <a>
				<img class="pro_img_4" src="images/img-product.jpg">
				{{? it.content.pro_name_show == 1 }}
				<h3>产品名称</h3>
				{{?}}
			</a>
            <p class="pro_price_p"><span class="red" style="font-size: 12px;">¥39.90</span><span class="old_price">¥59.90</span></p>
			{{? it.content.show_sale == 1 }}
			<p class="show_sale_p"><span style="font-size: 12px;">销量：11</span></p>
			{{?}}
        </li>
        <div class="empty-div"></div>
    </ul>
	{{?? it.content.css_type == 3 }}
	<ul class="sx-cont-sub3">
        <li>
            <a>
				<img class="pro_img_5" src="images/img-product.jpg">
				{{? it.content.pro_name_show == 1 }}
				<h3>产品名称</h3>
				{{?}}
			</a>
            <p class="pro_price_p"><span class="red">¥339.00</span></p>
			{{? it.content.show_sale == 1 }}
			<p class="show_sale_p"><span>销量：11</span></p>
			{{?}}
        </li>
        <li>
            <a>
				<img class="pro_img_5" src="images/img-product.jpg">
				{{? it.content.pro_name_show == 1 }}
				<h3>产品名称</h3>
				{{?}}
			</a>
            <p class="pro_price_p"><span class="red">¥12.80</span><span class="old_price">¥32.80</span></p>
			{{? it.content.show_sale == 1 }}
			<p class="show_sale_p"><span>销量：11</span></p>
			{{?}}
        </li>
        <li>
            <a>
				<img class="pro_img_5" src="images/img-product.jpg">
				{{? it.content.pro_name_show == 1 }}
				<h3>产品名称</h3>
				{{?}}
			</a>
            <p class="pro_price_p"><span class="red">¥28.80</span></p>
			{{? it.content.show_sale == 1 }}
			<p class="show_sale_p"><span>销量：11</span></p>
			{{?}}
        </li>
        <li>
            <a>
				<img class="pro_img_5" src="images/img-product.jpg">
				{{? it.content.pro_name_show == 1 }}
				<h3>产品名称</h3>
				{{?}}
			</a>
            <p class="pro_price_p"><span class="red">¥39.90</span><span class="old_price">¥59.90</span></p>
			{{? it.content.show_sale == 1 }}
			<p class="show_sale_p"><span>销量：11</span></p>
			{{?}}
        </li>
		<li class="sx-cell-right">
            <a>
				<img class="pro_img_5" src="images/img-product.jpg">
				{{? it.content.pro_name_show == 1 }}
				<h3>产品名称</h3>
				{{?}}
			</a>
            <p class="pro_price_p"><span class="red">¥39.90</span><span class="old_price">¥59.90</span></p>
			{{? it.content.show_sale == 1 }}
			<p class="show_sale_p"><span>销量：11</span></p>
			{{?}}
        </li>
        <div class="empty-div"></div>
    </ul>
	{{?}}
	<div style="clear:both"></div>	
</div>
</script>
<script type="dot-template" id="type_ctrl_7">
<div class="formitems">
    <label class="fi-name">布局方式：</label> 
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="css_type" value="1" {{? it.content.css_type==1}} checked{{?}}>双列商品</label>
            <label><input type="radio" name="css_type" value="2"{{? it.content.css_type==2}} checked{{?}}>四列商品</label>
			<label><input type="radio" name="css_type" value="3"{{? it.content.css_type==3}} checked{{?}}>五列商品</label>
        </div>
    </div>
</div>
<div class="formitems">
    <label class="fi-name">是否显示产品名：</label> 
    <div class="form-controls">
        <div class="radio-group">
            <label><input type="radio" name="pro_name_show" value="1" {{? it.content.pro_name_show==1}} checked{{?}}>显示</label>
            <label><input type="radio" name="pro_name_show" value="0"{{? it.content.pro_name_show==0}} checked{{?}}>隐藏</label>
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
<?php if($custom_type==1 || $custom_type==3){?>
<div class="formitems">  
	<label class="fi-name">选择分类：</label>  
	<div class="form-controls">
		<div class="droplist">
			<!-- 产品分类 -->
			<select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
			<option value="-1"{{? -1==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >请选择分类</option>
			{{? it.type_arr[-1]}}
				{{for ( k=0,m=it.type_arr[-1].length; k<m; k++ ) {
					type_id_name=it.type_arr[-1][k].split("_");
					type_id=type_id_name[0];
					type_name=type_id_name[1];
				}}
			<option value="{{=type_id}}"{{? type_id==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
				{{? it.type_arr[type_id]}}
					{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
						ctype_id_name=it.type_arr[type_id][j].split("_");
						ctype_id=ctype_id_name[0];
						ctype_name=ctype_id_name[1];
					}}
					<option value="{{=ctype_id}}"{{? ctype_id==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
						{{? it.type_arr[ctype_id]}}
							{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
								ctype_id_name3=it.type_arr[ctype_id][h].split("_");
								ctype_id3=ctype_id_name3[0];
								ctype_name3=ctype_id_name3[1];
							}}
							<option value="{{=ctype_id3}}"{{? ctype_id3==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
							{{? it.type_arr[ctype_id3]}}
								{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
									ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
									ctype_id4=ctype_id_name4[0];
									ctype_name4=ctype_id_name4[1];
								}}
								<option value="{{=ctype_id4}}"{{? ctype_id4==it.content.dataset[0][0].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
								{{ } }}
							{{?}}
							{{ } }}
						{{?}}
					{{ } }}
				{{?}}
				{{ } }}
				
			{{?}}
			<!-- 品牌供应商分类 -->
			{{? it.o_supply_type_arr}}
				{{for ( k=0,m=it.o_supply_type_arr.length; k<m; k++ ) {
					supply_type_id_name = it.o_supply_type_arr[k].split("_");
				}}
				<option value="{{= supply_type_id_name[0]}}" {{? supply_type_id_name[0]==it.content.dataset[0][0].select_value}} selected="selected"{{?}}>{{= supply_type_id_name[1]}}</option>
				{{ } }}
			{{?}}
			</select>
		</div>
		<input type="hidden" class="j-verify" name="item_id" value="">
		<span class="fi-help-text j-verify-linkType"></span>
	</div>
</div>
<?php }else{?>
<div class="formitems">  
	<label class="fi-name">选择分类：</label>  
	<div class="form-controls">
		<p style="color:red">无需选择，将显示当前分类页所属分类的商品</p>
	</div>
</div>	
<?php }?>
<div class="formitems">
    <label class="fi-name">显示商品数量：</label> 
    <div class="form-controls">
        <input type="number"  name="pro_num_show" class="input xlarge" value="{{= it.content.pro_num_show}}"> <span class="fi-help-text"></span> 
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
<!--轮播图-->

<script  type="dot-template" id="type_con_8">
	<div class="con_display" {{? it.content.padding}}style="padding-top:{{= it.content.padding}}px;padding-bottom:{{= it.content.padding}}px"{{?}}>
		<div class="content">
            <ul class="imgBox" id="imgBox1">
                <li><a href="#"><img src="{{=it.content.dataset[0][0].pic}}"></a></li>
            </ul>
            <div class="currentNum" id="pager1">
				{{ for(p=0;p<it.content.dataset[0].length;p++){ }}
				<span class="imgNum {{?p==0}}activeSlide{{?}}"></span>
				{{ } }}
			</div>
            <div class="nav-view">
	            <div class="control to-left " id="cleft1"><i class="fa fa-angle-left"></i></div>
	        	<div class="control to-right " id="cright1"><i class="fa fa-angle-right"></i></div>
	        </div>
        </div>
	</div>
</script>
<script type="dot-template" id="type_ctrl_8">
<div class="formitems">
    <label class="fi-name">模块上下边距：</label>
    <div class="form-controls">
        <div  id='slider' class="fl diy-slider j-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 10%;"></span></div>
        <span class="fl mgl10 mgt5 ftsize14 j-ctrl-showheight2">{{? it.content.padding}}{{=it.content.padding+'px'}}{{??}}0px{{?}}</span>
    </div>
</div>
<div class="formitems">  
	<label class="fi-name">轮播图样式：</label>   
	<div class="radio-group" style="padding-top:0;">
		<label><input type="radio" name="nav_css_type" value="1"{{? it.content.nav_css_type[0] == 1}} checked{{?}}>宽度铺满全屏</label>
		<label><input type="radio" name="nav_css_type" value="2"{{? it.content.nav_css_type[0] == 2}} checked{{?}}>宽度与页面内容主体同宽</label>
	</div>
	<p style="color: red;margin-left: 40px;">注：因本页面布局受限，轮播图实际样式效果请参考预览页面</p>
</div>
<ul class="ctrl-item-list"> 
	{{for (p=0;p<it.content.dataset[0].length;p++) { }}
    <li class="ctrl-item-list-li clearfix" data-sort="{{= p}}" data-position="0">		
		<div class="fl">
			<div class="imgnav j-selectimg">			
			<form action="save_img.php?customer_id=<?php echo $customer_id_en; ?>&type_id=<?php echo $type_id; ?>" id="frm_img{{=p}}" enctype="multipart/form-data" method="post" onsubmit="return saveReport({{=p}});">
				<input type="hidden" name="getImg" id='getImg{{=p}}' value="{{= it.content.dataset[0][p].pic}}">
				<p class="imgnav-select">
					<input type="file" size="20" name="upfile2" id="upfile2" class="up" >
					<img src="{{= it.content.dataset[0][p].pic}}">
				</p>
				<input type="hidden" name="diy_tem_contid" value="{{= it.id}}">
				<input type="hidden" name="diy_temid" value="<?php echo $diy_temid?>">
				<input type="hidden" name="img_sort" value="{{= p}}">
				
			</form>
			</div>
			<span class="fi-help-text txtCenter mgt5 j-verify-pic"></span>
		</div>
		
		<div class="fl imgnav-info">
			<div class="formitems">  
				<label class="fi-name">链接到：</label> 
				<div class="radio-group" style="padding-top:0;">
					<label><input type="radio" class="link_type" name="link_type_{{=p}}" value="" {{? it.content.dataset[0][p].link_type != 1}}checked{{?}}>PC商城</label>
					<label><input type="radio" class="link_type" name="link_type_{{=p}}" value="1" {{? it.content.dataset[0][p].link_type == 1}}checked{{?}}>链接网址</label>
				</div>
				{{? it.content.dataset[0][p].link_type == 1}}
                <div class="form-controls">
					<input type="text" class="j-verify" name="link_address" value="{{=it.content.dataset[0][p].link}}" placeholder="请输入网址，必须以http://开头">
				</div>
				{{??}}	
				<div class="form-controls">
					<div class="droplist">
						<select  name="type_id_2"  id="type_id_2"  class="input xlarge" style="height:28px;">
						{{? it.fixed_link}}
							{{ select_value=0;}}
							{{? it.content.dataset[0][p].select_value}}
								{{ 	selv=it.content.dataset[0][p].select_value.split("_");
									select_value=selv[0];
								}}
							{{?}}
							{{	for( k=0,m=it.fixed_link.length; k<m; k++ ) { 
                                fl = it.fixed_link[k].split("_");
                            }}
							<option value="2_{{=fl[0]}}" {{? fl[0]==select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
						{{?}}
						{{? it.type_arr}}
							<optgroup label="------------产品分类------------"></optgroup>
							<option value="3" {{? it.content.dataset[0][p].link_type==3}} selected="selected"{{?}}>多级分类</option>
						{{?}}
						{{? it.brand_arr}}
							<optgroup label="------------品牌供应商----------"></optgroup>
							<option value="4" {{? it.content.dataset[0][p].link_type==4}} selected="selected"{{?}}>品牌供应商店铺</option>
						{{?}}
						<?php if($supply_id<0){?>
						{{? it.template_link}}
						<optgroup label="-------------活动页--------------"></optgroup>
							<option value="5" {{? it.content.dataset[0][p].link_type==5}} selected="selected"{{?}}>活动页模板</option>
						{{?}}
						<!--{{? it.room_link}}
						<optgroup label="----------微视直播系统-----------"></optgroup>
							<option value="7" {{? it.content.dataset[0][p].link_type==7}} selected="selected"{{?}}>直播房间</option>
						{{?}}-->
						<?php }?>
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<optgroup label="----------产品分类---------"></optgroup>
							<option value="6" {{? it.content.dataset[0][p].link_type==6}} selected="selected"{{?}}>产品分类</option>
						{{?}}
                        </select>
						<!-- 产品分类 -->
						{{? it.type_arr[-1]}}
						<select  name="product_type_2"  id="product_type_2_{{=p}}"  class="input xlarge" style="{{? it.content.dataset[0][p].link_type!=3}}display:none;{{?}}height:28px;">
							{{for ( k=0,m=it.type_arr[-1].length; k<m; k++ ) {
								type_id_name=it.type_arr[-1][k].split("_");
								type_id=type_id_name[0];
								type_name=type_id_name[1];
							}}
						<option value="{{=type_id}}"{{? type_id==it.content.dataset[0][p].select_value}} selected="selected"{{?}} >{{=type_name}}</option>
							{{? it.type_arr[type_id]}}
								{{for (j=0,n=it.type_arr[type_id].length;j<n;j++) {
									ctype_id_name=it.type_arr[type_id][j].split("_");
									ctype_id=ctype_id_name[0];
									ctype_name=ctype_id_name[1];
								}}
								<option value="{{=ctype_id}}"{{? ctype_id==it.content.dataset[0][p].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name}}</option>
									{{? it.type_arr[ctype_id]}}
										{{for (h=0,b=it.type_arr[ctype_id].length;h<b;h++) {
											ctype_id_name3=it.type_arr[ctype_id][h].split("_");
											ctype_id3=ctype_id_name3[0];
											ctype_name3=ctype_id_name3[1];
										}}
										<option value="{{=ctype_id3}}"{{? ctype_id3==it.content.dataset[0][p].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name3}}</option>
										{{? it.type_arr[ctype_id3]}}
											{{for (g=0,v=it.type_arr[ctype_id3].length;g<v;g++) {
												ctype_id_name4=it.type_arr[ctype_id3][g].split("_");
												ctype_id4=ctype_id_name4[0];
												ctype_name4=ctype_id_name4[1];
											}}
											<option value="{{=ctype_id4}}"{{? ctype_id4==it.content.dataset[0][p].select_value}} selected="selected"{{?}} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{=ctype_name4}}</option>
											{{ } }}
										{{?}}
										{{ } }}
									{{?}}
								{{ } }}
							{{?}}
							{{ } }}
						</select>
						{{?}}
						<!-- 品牌供应商店铺 -->
						{{? it.brand_arr}}
						<select name="brand_supply" id="brand_supply_{{=p}}" class="input xlarge" style="{{? it.content.dataset[0][p].link_type!=4}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.brand_arr.length; k<m; k++ ) {
								supply_id_name = it.brand_arr[k].split("_");
							}}
							<option value="{{= supply_id_name[0]}}" {{? supply_id_name[0]==it.content.dataset[0][p].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 礼包列表 -->
						{{? it.package_lists}}
						{{? it.content.dataset[0][0].select_value}}
	                        {{	selv=it.content.dataset[0][0].select_value.split("_");
								select_value=selv[0];
	                        }}
                    	{{?}}
						<select name="brand_supply" id="brand_supply_80" class="input xlarge" style="{{? select_value!=-10}}display:none;{{?}}height:28px;" >
						    <option value="-10" >全部礼包</option>
						    {{for ( k=0,m=it.package_lists.length; k<m; k++ ) {
						        supply_id_name = it.package_lists[k].split("_");
						    }}
						    <option value="-10_{{= supply_id_name[0]}}" {{? "-10_"+supply_id_name[0]==it.content.dataset[0][p].select_value}} selected="selected"{{?}}>{{= supply_id_name[1]}}</option>
						    {{ } }}
						</select>
						{{?}}
						<!-- 其他模板 -->
						{{? it.template_link}}
						<select name="template_link" id="template_link_{{=p}}" class="input xlarge" style="{{? it.content.dataset[0][p].link_type!=5}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.template_link.length; k<m; k++ ) {
								template_id_name = it.template_link[k].split("_");
							}}
							<option value="{{= template_id_name[0]}}" {{? template_id_name[0]==it.content.dataset[0][p].select_value}} selected="selected"{{?}}>{{= template_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 微视直播房间 -->
						<!--{{? it.room_link}}
						<select name="room_link" id="room_link_{{=p}}" class="input xlarge" style="{{? it.content.dataset[0][p].link_type!=7}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.room_link.length; k<m; k++ ) {
								room_id_name = it.room_link[k].split("_");
							}}
							<option value="{{= room_id_name[0]}}" {{? room_id_name[0]==it.content.dataset[0][p].select_value}} selected="selected"{{?}}>{{= room_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}-->
						<!-- 品牌供应商产品分类 -->
						{{? it.supply_type_arr}}
						<select name="supply_type" id="supply_type_{{= p}}" class="input xlarge" style="{{? it.content.dataset[0][p].link_type!=6}}display:none;{{?}}height:28px;" >
							{{for ( k=0,m=it.supply_type_arr.length; k<m; k++ ) {
								supply_type_id_name = it.supply_type_arr[k].split("_");
							}}
							<option value="{{= supply_type_id_name[0]}}" {{? supply_type_id_name[0]==it.content.dataset[0][p].select_value}} selected="selected"{{?}}>{{= supply_type_id_name[1]}}</option>
							{{ } }}
						</select>
						{{?}}
						<!-- 产品 -->
						<div id="div_products_2_{{=p}}" style="display:none;" >
							<select name="product_detail_id_2" id="product_detail_id_2_{{=p}}" class="input xlarge" style="height:28px;">
								
							</select>
						</div>
					</div>
					<input type="hidden" class="j-verify" name="item_id" value="">
					<span class="fi-help-text j-verify-linkType"></span>
				</div>
				{{?}}
			</div>
			<div class="formitems">  
				<label class="fi-name">建议尺寸：</label>
				{{? it.content.nav_css_type[0]==1}}
				<label class="note">1920*500 px</label>
				{{?? it.content.nav_css_type[0]==2}}
				<label class="note">1200*500 px</label>
				{{?}}
			</div>
		</div>

        <div class="ctrl-item-list-actions">
            <a href="javascript:;" title="上移" class="j-moveup"><i class="gicon-arrow-up"></i></a>
            <a href="javascript:;" title="下移" class="j-movedown"><i class="gicon-arrow-down"></i></a>
			<a href="javascript:;" title="删除" class="j-del"><i class="gicon-remove"></i></a>
        </div>
    </li>
    {{ } }}
	<!-- 最多六张图片 -->
	{{? it.content.dataset[0].length < 6 }}
    <li class="ctrl-item-list-add" title="添加">+</li>
	{{?}}
</ul>
</script>
<!--轮播图-->
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
	var supply_id = <?php echo $supply_id;?>	//供应商id
	
$(function() {  
    var customarr   	= <?php echo json_encode($customarr);?>;//模块内容
    console.log(customarr);
	var content_detail  = <?php echo json_encode($content_detail);?>;//模块内容详细信息
	console.log(content_detail);
	typearr     		= <?php echo json_encode($type_arr);?>; //产品分类  
    fixedlink   		= <?php echo json_encode($fixedlink);?>;//固定连接数组  
	brandarr     		= <?php echo json_encode($brandarr);?>; //品牌供应商
	package_lists     	= <?php echo json_encode($package_lists);?>; //礼包列表
	template_link   	= <?php echo json_encode($template_link);?>; //其他模板
	supply_type_arr 	= <?php echo json_encode($supply_type_arr);?>; //品牌供应商产品分类
	o_supply_type_arr 	= <?php echo json_encode($o_supply_type_arr);?>; //品牌供应商分类
	room_link           = <?php echo json_encode($room_link);?>; //微视直播房间
	
    var new_baseurl   	= "<?php echo $new_baseurl;?>";//拼接链接
    var test = eval(customarr);//   JSON转化为数组 
    //console.log(test);
	var content_detail = eval(content_detail);//   JSON转化为数组 
	// console.log(test);
	//读取数据库数组生成页面
	for ( var i = 0; i < test.length; i++ ){
		if ( test[i] == '' ){
			continue;
		}
		var module = {
				id:null,	//模块id
				type:null,	//模块类型
				fixed_link:null,//固定链接
				type_arr:null,//产品分类
                brand_arr:null,//品牌供应商
                template_link:null,//其他模板
                supply_type_arr:null,//品牌供应商产品分类
                o_supply_type_arr:null,//品牌供应商分类
				sort:null, //排序
				supply_id:null,	//供应商id
				content:null,//模块内容
				room_link:null //微视直播房间 
		};
		
		module.content = {
				title: null,
				title_en: null,
				mod_describe: null,
				padding: 0,
				tab: 1,
				nav_title: null,
				is_show: 1,
				nav_css_type: 1,
				floor_number: 1,
				floor: 1,
				css_type: 1,
				pro_name_show: 1,
				pro_num_show: 1,
				show_sale: 1,
				dataset:[]
		};
		
		
		test[i].is_show = test[i].is_show.split('|');
		var is_show_len = test[i].is_show.length;
		
		if ( test[i].type == 1 || test[i].type == 5 ){
			for ( var m = 1; m < is_show_len; m++ ){
				if ( test[i].is_show[m] == 1 ){
					module.content.tab = m;
					break;
				}
			}
		} else if ( test[i].type == 4 ){
			for ( var m = 0; m < is_show_len; m++ ){
				if ( test[i].is_show[m] == 1 ){
					module.content.tab = m;
					break;
				}
			}
		}
		
		if ( test[i].nav_css_type ){
			test[i].nav_css_type = test[i].nav_css_type.split('|');
		}
		
		module.id						= test[i].diy_tem_contid;
        module.type						= test[i].type;
		module.fixed_link				= fixedlink;
		module.type_arr					= typearr;
        module.brand_arr				= brandarr;
        module.package_lists			= package_lists;
        module.template_link			= template_link;
        module.room_link		     	= room_link;
        module.supply_type_arr			= supply_type_arr;
        module.o_supply_type_arr		= o_supply_type_arr;
        module.supply_id				= supply_id;
		module.content.title 			= test[i].title;		//模块标题
		module.content.title_en 		= test[i].title_en;		//模块英文标题
		module.content.mod_describe 	= test[i].mod_describe;	//模块描述
		module.content.padding			= test[i].mod_padding;	//模块间距	
		module.content.nav_title		= test[i].nav_title;	//模块导航栏标题
		module.content.is_show			= test[i].is_show;		//模块导航栏显示
		module.content.nav_css_type		= test[i].nav_css_type;	//模块导航栏样式
		module.content.floor_number		= test[i].floor_number;	//模块楼层
		module.content.css_type			= test[i].css_type;		//模块样式
		module.content.pro_name_show	= test[i].pro_name_show;//是否显示产品名
		module.content.pro_num_show		= test[i].pro_num_show;	//产品显示数量
		module.content.show_sale		= test[i].show_sale;	//是否显示销量
		
		var content_len = content_detail[module.id].length;
		var nav_title = test[i].nav_title.split('|');
		
		for ( var k = 0; k < content_len; k++ ){
			var imgurl_arr = new Array();
			if(content_detail[module.id][k].imgurl){
				imgurl_arr = content_detail[module.id][k].imgurl.split("|");
			}
			
			var pic_title_arr = new Array();
			if(content_detail[module.id][k].pic_title){
				pic_title_arr = content_detail[module.id][k].pic_title.split("|");
			}
			
			var link_type_arr = new Array();
			if(content_detail[module.id][k].link_type){
				link_type_arr = content_detail[module.id][k].link_type.split("|");
			}
			
			var link_arr = new Array();
			if(content_detail[module.id][k].link){
				link_arr = content_detail[module.id][k].link.split("|");
			}
			
			var select_value_arr = new Array();
			if(content_detail[module.id][k].select_value){
				select_value_arr = content_detail[module.id][k].select_value.split("|");
			}
			
			var detail_value_arr = new Array();
			if(content_detail[module.id][k].detail_value){
				detail_value_arr = content_detail[module.id][k].detail_value.split("|");
			}
			
			var start_time_arr = new Array();
			if(content_detail[module.id][k].start_time){
				start_time_arr = content_detail[module.id][k].start_time.split("|");
			}
			
			var end_time_arr = new Array();
			if(content_detail[module.id][k].end_time){
				end_time_arr = content_detail[module.id][k].end_time.split("|");
			}
			
			var newarray = [];
			module.content.dataset.push(newarray);
			
			for ( var j = 0; j < imgurl_arr.length; j++ ){
				var newdata = {
					mod_sort:null,
					title:"",
					pic:"",
					link_type:"",
					link:"",
					select_value:"",
					detail_value:'',
					start_time:'',
					end_time:''
				};
				
				module.content.dataset[k].push(newdata);
				
				module.content.dataset[k][j].title			= pic_title_arr[j];
				module.content.dataset[k][j].pic			= imgurl_arr[j];	
				if ( link_type_arr[j] != undefined ){				
					module.content.dataset[k][j].link_type	= link_type_arr[j];
				}
				if ( link_arr[j] != undefined ){
					module.content.dataset[k][j].link       = link_arr[j];
				}
				if ( select_value_arr[j] != undefined ){
					module.content.dataset[k][j].select_value	= select_value_arr[j];
				}
				if ( detail_value_arr[j] != undefined ){
					module.content.dataset[k][j].detail_value	= detail_value_arr[j];
				}
				if ( start_time_arr[j] != undefined ){
					module.content.dataset[k][j].start_time	= start_time_arr[j];
				}
				if ( end_time_arr[j] != undefined ){
					module.content.dataset[k][j].end_time	= end_time_arr[j];
				}
			}
            
		}
		custom_query(module);
		imgurl_arr			= [];
		link_type_arr    	= [];
		link_arr    	    = [];
		select_value_arr	= [];
		detail_value_arr	= [];
		// console.log(module.content.dataset);
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
function changeProductType(type_id,sort,detail_id){
	document.getElementById("div_products_2_"+sort).style.display="block";
	
	if( detail_id != '' ){
		url='get_product_list.php?callback=jsonpCallback_get_product_list&type_id='+type_id+'&sort='+sort+'&detail_id='+detail_id+'&supply_id='+supply_id;
	}else{
		url='get_product_list.php?callback=jsonpCallback_get_product_list&type_id='+type_id+'&sort='+sort+'&supply_id='+supply_id;
	}
	$.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list'
	});

}

function jsonpCallback_get_product_list(results){
	var len = results.length;
	var sort=results[2].sort;
	var detail_id = results[3].detail_id;
	
	var sel_pro = document.getElementById("product_detail_id_2_"+sort);
	var startTimeInput = $('#starttime');
	var endTimeInput = $('#endtime');
	
	sel_pro.options.length=0;
   
    var new_option = new Option("---请选择一个产品---",-1);
    sel_pro.options.add(new_option);
	
    for(i=4;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var issnapup = results[i].issnapup;
		var start_time = results[i].start_time;
		var end_time = results[i].end_time;
	
		var new_option = new Option(pname,pid);
		sel_pro.options.add(new_option);
		if( pid == detail_id ){
			new_option.selected = true;
			if( startTimeInput && endTimeInput ){
				if( issnapup ){	//如果是抢购产品，则更新时间
					if( start_time != '0000-00-00 00:00:00' && startTimeInput.val() != start_time ){
						startTimeInput.val(start_time);
						startTimeInput.change();
					}
					if( end_time != '0000-00-00 00:00:00' && endTimeInput.val() != end_time ){
						endTimeInput.val(end_time);
						endTimeInput.change();
					}
					
				}
			}
		}
	}
}

function saveReport(o) {   
        $("#frm_img"+o).ajaxSubmit(function(msg) {   
          // 对于表单提交成功后处理，message为提交页面saveReport.htm的返回内容 
          var imgurl=msg;  
          if (imgurl.indexOf('Custom') != -1 && imgurl.indexOf('Custom') != '') 
          {
          	console.log(imgurl);
            $('#getImg'+o).val(imgurl);
            $('#getImg'+o).change();
          }
          else
          {
          	$('#getImg'+o).change();
          	layer.alert(imgurl);   
          }
		  
       });

    return false; // 必须返回false，否则表单会自己再做一次提交操作，并且页面跳转   
}
$(document).ready(function(){
    $(window).scroll( function() {               //滚动时触发
        var top = $(document).scrollTop(),       //获取滚动条到顶部的垂直高度
            height = $(window).height();         //获得可视浏览器的高度
			
		$("#nav-left-floor").css({position: 'fixed', top: (height-180)/2+'px'});
		$(".nav-right-fix").css({position: 'fixed', top: (height-210)/2+'px'});
        
		var floor_num = $('.nav-chd-left').length - 1;	//楼层数量
		var floor_module_top = new Array();
		var floor_module_bottom = new Array();
		$('.nav-chd-left').each(function(index, element) {
            $(this).find("span").eq(1).hide();
			
			if ( index < floor_num ){	//计算模块位置
				floor_module_top[index] = $('.floor_module').eq(index).offset().top;
				var floor_height = $('.floor_module').eq(index).height();
				floor_module_bottom[index] = floor_height + floor_module_top[index];
				// console.log(floor_height);
				// console.log(floor_module_bottom[index]);
			}
			
        });
		for ( var i=0; i < floor_num; i++ ){
			if ( top+1 >= floor_module_top[i] && top+1 < floor_module_bottom[i] ){
				$('.nav-chd-left').eq(i).find("span").eq(1).show();
			}
		}
		
    });
	
	$('input[name="floating_floor"]').change(function(){
		if ( $(this).val() == 1 ){
			$('#nav-left-floor').show();
		} else {
			$('#nav-left-floor').hide();
		}
	
	});
    /*点击回到顶部*/
    $('#backToTop-up').click(function(){
        $('html, body').animate({
            scrollTop: 0
        }, 500);
    });
    $('#backTop-up').click(function(){
        $('html, body').animate({
            scrollTop: 0
        }, 500);
    });
    /*点击到底部*/
    $('#backToTop-down').click(function(){
        $('html, body').animate({
            scrollTop: $(document).height()
        }, 500);
    });
	
});
</script>

<!--选择链接的JS结束-->
</body>
</html>  
<?php 

mysql_close($link);
?>