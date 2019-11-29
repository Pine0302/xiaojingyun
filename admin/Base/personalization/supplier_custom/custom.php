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
$new_baseurl = Protocol.$http_host; 

$diy_temid=-1;

$action="";
$temid="";
$customarr[]="";
$action=$configutil->splash_new($_GET["action"]);
if(isset($_GET["temid"])){
	$temid=$configutil->splash_new($_GET["temid"]);
}
$supplier_id_en=$_GET["supplier_id"];
$supplier_id =passport_decrypt($supplier_id_en);
switch($action){
	
	case "add":
		$inser_custom="insert into weixin_commonshop_supply_diy_template (customer_id,supplier_id,content,isused,isvalid,creatime,name) values ('".$customer_id."','".$supplier_id."','-1',false,true,now(),'品牌合作商自定义模板')";
		$result_insert=_mysql_query($inser_custom) or die ('inser_custom faild' .mysql_error());
		$diy_temid=mysql_insert_id();
		$query_temid="select name from weixin_commonshop_supply_diy_template where id=".$diy_temid." and isvalid=true and customer_id=".$customer_id." and supplier_id=".$supplier_id." limit 0,1";
		$result_query_temid=_mysql_query($query_temid) or die ('query_temid faild' .mysql_error());
		while($row=mysql_fetch_object($result_query_temid)){
			$name=$row->name;
		}
		$temid=$diy_temid;
	break;
	case "edit":
		$query_temid="select id,content,name,bgcolor from weixin_commonshop_supply_diy_template where id=".$temid." and isvalid=true and customer_id=".$customer_id." and supplier_id=".$supplier_id." limit 0,1";
		$result_query_temid=_mysql_query($query_temid) or die ('query_temid faild' .mysql_error());
		while($row=mysql_fetch_object($result_query_temid)){
			$diy_temid=$row->id;
			$content=$row->content;
			$name=$row->name;
			$bgcolor=$row->bgcolor;
		}
		$k=0;
		$custom_query="select diy_tem_contid,title,imgurl,foreign_id,detail_id,mod_padding,mod_img_padding,css_type,pro_title_show,pro_title_twoline,pro_numshow,foot_position,placeholder,show_sale,type,link_type,select_value,detail_value,detail_name,search_color,color,video_link from weixin_commonshop_supply_diy_template_content where isvalid=true and customer_id=".$customer_id."  and supplier_id=".$supplier_id." and LOCATE(diy_tem_contid,'".$content."') ORDER  BY FIND_IN_SET(diy_tem_contid,'".$content."')";

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
			$k++;
		}
		
	break;
}


$typeLst = new ArrayList();
$query="select id,type_name from weixin_commonshop_supply_type where isvalid=true  and customer_id=".$customer_id." and user_id=".$supplier_id."";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$catarr[] = "-1_全部产品";
while ($row = mysql_fetch_object($result)) {
   $pt_id = $row->id;
   $pt_name = $row->type_name;
    
   $pstr = $pt_id."_".$pt_name;
   $catarr[]=$pt_id."_".$pt_name;
   $typeLst->add($pstr);
   
}
/*
$sql = "select type_ids from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id." and is_supply_id=".$supplier_id."";
$result = _mysql_query($sql) or die('Query failed2: ' . mysql_error());
$type_id = array();
while ($row = mysql_fetch_object($result)) {
	 $pt_id = $row->type_ids;
	 $pt_id = trim($pt_id,",");
	 $pt_id = explode(",",$pt_id);
	 foreach($pt_id as $k => $v){
		  if(!in_array($v,$type_id) and !empty($v) ){
			$type_id[] = $v;
			}	 
	 }
		 
}
foreach($type_id as $k=>$v){
	 $sql_type = "select name from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and id=".$v;
	 $result_type = _mysql_query($sql_type) or die('Query failed3: ' . mysql_error());
	 while ($row_type = mysql_fetch_object($result_type)) {
		  $pt_name = $row_type->name;
	 }
	 $pstr = $v."_".$pt_name;
	 $catarr[]=$v."_".$pt_name;
	 $typeLst->add($pstr);
}
*/
/*
$typesize = $typeLst->size();

//图文信息
$imginfoLst = new ArrayList();
$query = 'SELECT id,title FROM weixin_subscribes where isvalid=true and parent_id=-1 and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	  $sub_id =  $row->id ;
	  $title = $row->title;
	  
	  $pstr = $sub_id."_".$title;
	  $imginfo[]=$sub_id."_".$title;
      $imginfoLst->add($pstr);
}

$imginfosize = $imginfoLst->size();


$cityarea_food=[];
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
$cityareaCaterersize = $cityareaCatererLst->size();
///城市商圈，渠道开关
$is_cityarea=0;
$is_cityarea_count=0;
$query="select count(1) as is_cityarea_count from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and (c.sys_name='商圈-美食' or c.sys_name='商圈-外卖' or c.sys_name='商圈-金融保险' or c.sys_name='商圈-酒店' or c.sys_name='商圈-ktv') and c.id=cf.column_id";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result)) {
   $is_cityarea_count = $row->is_cityarea_count;
   break;
}
if($is_cityarea_count>0){
   $is_cityarea=1;
}

//城市商圈（美食），渠道开关
$is_cityarea_caterer=0;
$is_caterer_count=0;
$query="select count(1) as is_caterer_count from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-美食' and c.id=cf.column_id";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result)) {
   $is_caterer_count = $row->is_caterer_count;
   break;
}
if($is_caterer_count>0){
   $is_cityarea_caterer=1;
   $cityarea_food[]="2_美食";  //城市商圈美食
}


$fixedlink[]="-1_---------------请选择---------------";
$fixedlink[]="-6_全部产品";
$fixedlink[]="-2_新品上市";
$fixedlink[]="-3_热卖产品";
$fixedlink[]="-4_购物车";
$fixedlink[]="-8_个人中心";
$fixedlink[]="-7_产品分类页";
$fixedlink[]="-5_限时抢购";
$fixedlink[]="-10_商城在线客服";
$fixedlink[]="-11_礼包列表";
$fixedlink[]="-12_VP产品";
*/


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
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content<?php echo $theme;?>.css">
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
				<iframe src="default_img.php?customer_id=<?php echo $customer_id_en; ?>&temid=<?php echo $temid; ?>&supplier_id=<?php echo $supplier_id;?>" height=110 width=100% FRAMEBORDER=0 SCROLLING=no></iframe>
            	</p>
				<p class="imgnav-select" style="height:240px;">
				<iframe src="background_img.php?customer_id=<?php echo $customer_id_en; ?>&temid=<?php echo $temid; ?>&supplier_id=<?php echo $supplier_id;?>" height=110 width=100% FRAMEBORDER=0 SCROLLING=no style="height:240px;"></iframe>
            	</p>
            </div>
            </div>
        </div>
        <div class="diy-actions" style="margin-bottom: 20px;">
                <div class="diy-actions-addModules clearfix">
                    <a data-type="0" class="j-page-addModule" href="javascript:;"><i class="gicon-cog"></i>页面设置</a>
                    <a data-type="2" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>图片广告</a>
                    <a data-type="9" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>橱窗二图</a>
                    <a data-type="7" class="j-diy-addModule" href="javascript:;"><span class="icon-plus"></span>单张图片</a>
                    <div class="clear"></div>
                </div>
                <div class="diy-actions-submit">
                    <a href="javascript:;" class="save-btn diy_btn" id="j-savePage" style="margin-bottom:15px;" >保存</a>
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
                <label class="fi-name">链接到：</label>  
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
                        <!--{{? it.fixed_link}}
                            {{for (k=0,m=it.fixed_link.length;k<m;k++) { 
                                fl=it.fixed_link[k].split("_");
                            }}
                        <option value="{{=fl[0]}}" {{? fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						-->
                        {{? it.cat_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value=-1>---请选择产品分类---</option>
                            {{for (k=0,m=it.cat_arr.length;k<m;k++) { 
                                fl=it.cat_arr[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_1"{{? fl[0]+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						<!--
                        {{? it.img_info}}
                        <optgroup label="---------------图文消息---------------"></optgroup>
                            {{for (k=0,m=it.img_info.length;k<m;k++) { 
                                fl=it.img_info[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_2"{{? fl[0]+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
                        {{? it.is_cityarea_caterer>0}}
                        <optgroup label="-----------城市商圈（美食）-----------"></optgroup>
                            {{for (k=0,m=it.city_food.length;k<m;k++) { 
                                fl=it.city_food[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_3" {{? fl[0]+'_3'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.is_cityarea_caterer>0}}
                        <optgroup label="-----------商圈行业列表-----------"></optgroup>
                            {{for (k=0,m=it.cityarea_food.length;k<m;k++) { 
                                fl=it.cityarea_food[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_4" {{? fl[0]+'_4'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						-->
                        </select>
                        <div id="div_products_2_{{=i}}" style="display:none;" >
                            <select name="product_detail_id_2" id="product_detail_id_2_{{=i}}" class="input xlarge" style="height:28px;">
                                
                            </select>
                        </div>
                    </div>
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
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
                <label class="fi-name">链接到：</label>  
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
                        <!--{{? it.fixed_link}}
                            {{for (k=0,m=it.fixed_link.length;k<m;k++) { 
                                fl=it.fixed_link[k].split("_");
                            }}
                        <option value="{{=fl[0]}}" {{? fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						-->
                        {{? it.cat_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value=-1>---请选择产品分类---</option>
                            {{for (k=0,m=it.cat_arr.length;k<m;k++) { 
                                fl=it.cat_arr[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_1"{{? fl[0]+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						<!--
                        {{? it.img_info}}
                        <optgroup label="---------------图文消息---------------"></optgroup>
                            {{for (k=0,m=it.img_info.length;k<m;k++) { 
                                fl=it.img_info[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_2"{{? fl[0]+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
                        {{? it.is_cityarea_caterer>0}}
                        <optgroup label="-----------城市商圈（美食）-----------"></optgroup>
                            {{for (k=0,m=it.city_food.length;k<m;k++) { 
                                fl=it.city_food[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_3" {{? fl[0]+'_3'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.is_cityarea_caterer>0}}
                        <optgroup label="-----------商圈行业列表-----------"></optgroup>
                            {{for (k=0,m=it.cityarea_food.length;k<m;k++) { 
                                fl=it.cityarea_food[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_4" {{? fl[0]+'_4'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						-->
                        </select>
                        <div id="div_products_2_{{=i}}" style="display:none;" >
                            <select name="product_detail_id_2" id="product_detail_id_2_{{=i}}" class="input xlarge" style="height:28px;">
                                
                            </select>
                        </div>
                    </div>
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
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
                <label class="fi-name">链接到：</label>  
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
                        <!--{{? it.fixed_link}}
                            {{for (k=0,m=it.fixed_link.length;k<m;k++) { 
                                fl=it.fixed_link[k].split("_");
                            }}
                        <option value="{{=fl[0]}}" {{? fl[0]==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						-->
                        {{? it.cat_arr}}
                        <optgroup label="---------------产品分类---------------"></optgroup>
						<option value=-1>---请选择产品分类---</option>
                            {{for (k=0,m=it.cat_arr.length;k<m;k++) { 
                                fl=it.cat_arr[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_1"{{? fl[0]+'_1'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						<!--
                        {{? it.img_info}}
                        <optgroup label="---------------图文消息---------------"></optgroup>
                            {{for (k=0,m=it.img_info.length;k<m;k++) { 
                                fl=it.img_info[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_2"{{? fl[0]+'_2'==it.content.dataset[i].select_value}} selected="selected"{{?}} >{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
                        {{? it.is_cityarea_caterer>0}}
                        <optgroup label="-----------城市商圈（美食）-----------"></optgroup>
                            {{for (k=0,m=it.city_food.length;k<m;k++) { 
                                fl=it.city_food[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_3" {{? fl[0]+'_3'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						{{? it.is_cityarea_caterer>0}}
                        <optgroup label="-----------商圈行业列表-----------"></optgroup>
                            {{for (k=0,m=it.cityarea_food.length;k<m;k++) { 
                                fl=it.cityarea_food[k].split("_");
                            }}
                        <option value="{{=fl[0]}}_4" {{? fl[0]+'_4'==it.content.dataset[i].select_value}} selected="selected"{{?}}>{{=fl[1]}}</option>
                            {{ } }}
                        {{?}}
						-->
                        </select>
                        <div id="div_products_2_{{=i}}" style="display:none;" >
                            <select name="product_detail_id_2" id="product_detail_id_2_{{=i}}" class="input xlarge" style="height:28px;">
                                
                            </select>
                        </div>
                    </div>
                    <input type="hidden" class="j-verify" name="item_id" value="">
                    <span class="fi-help-text j-verify-linkType"></span>
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

<script type="text/javascript" src="js/doT.min.js"></script>
<script type="text/javascript" src="js/colorpicker.js"></script>
<script type="text/javascript" src="js/custom.init.js"></script>
<script type="text/javascript" src="js/custom.core.js?ver=<?php echo rand(0,9999);?>"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/layer/layer.js"></script>
<script type="text/javascript" src="js/custom.events.js"></script>
<script type="text/javascript" src="js/jquery.touchSlider.js"></script>
<script type="text/javascript" src="js/slider.js"></script>
<script type="text/javascript" src="js/custom.display.js"></script>
<script charset="utf-8" src="../../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/global.js"></script>
<script type="text/javascript" src="../../../Common/js/Base/personalization/shop.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/lean-modal.min.js"></script>
<script type="text/javascript" src="../../../Common/js/Product/product/jquery.uploadify-3.1.min.js?ver=<?php echo rand(0,9999);?>"></script>
<script type="text/javascript" src="js/jquery.form.js"></script><!--ajaxform 插件-->
<!--<script src="//malsup.github.io/jquery.form.js"></script>-->
<script>
    var customer_id =<?php echo $customer_id;?>; 
	var supplier_id =<?php echo $supplier_id;?>;
    var diy_temid   =<?php echo $diy_temid;?>;
	//var is_cityarea_caterer =<?php echo $is_cityarea_caterer;?>  //城市商圈（美食），渠道开关
	//var is_cityarea =<?php echo $is_cityarea;?>  //城市商圈，渠道开关
$(function() {  
    var customarr   =<?php echo json_encode($customarr);?>;//模块内容
    console.log(customarr);
     catarr     =<?php echo json_encode($catarr);?>; //分类
    // imginfo  =<?php echo json_encode($imginfo);?>;//图文消息数组
    // cityfood       =<?php echo json_encode($cityfood);?>;//城市商圈美食
    // fixedlink   =<?php echo json_encode($fixedlink);?>;//固定连接数组  
	// cityarea_food       =<?php echo json_encode($cityarea_food);?>;//商圈-美食
	 
	 
    var new_baseurl   ="<?php echo $new_baseurl;?>";//拼接链接
    var test = eval(customarr);//   JSON转化为数组 
    var titleArr=new Array();
    var imgArr=new Array();
    var select_value_arr=new Array();
    var detail_value_arr=new Array();
    var detail_name_arr=new Array();
	var color_arr=new Array();
 //读取数据库数组生成页面
    for(i=0;i<test.length;i++)
    {   
        var module={
                id:null,//模块ID 
                type:null,//模块类型
                sort:null, //排序
                content:null,//模块内容
            //    fixed_link:null,//固定
                cat_arr:null//产品分
            //    img_info:null,//图文
            //    city_food:null,//城市商圈
			//	is_cityarea_caterer:null,//城市商圈（美食）
			//	is_cityarea:null,//城市商圈，渠道开关
			//	cityarea_food:null//商圈-美食
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
			if(test[i].color){
                 color_arr=test[i].color.split("|");
            }
         module.id=test[i].diy_tem_contid;
         module.type=test[i].type;
        // module.fixed_link=fixedlink;
         module.cat_arr=catarr;
        // module.img_info=imginfo;
        // module.city_food=cityfood;
		// module.is_cityarea_caterer=is_cityarea_caterer;    
		// module.is_cityarea=is_cityarea;
		 //module.cityarea_food=cityarea_food;
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
					color:""
					
                };
            module.content.dataset.push(newdata);
            module.content.dataset[j].title=titleArr[j];
            module.content.dataset[j].select_value=select_value_arr[j];
            module.content.dataset[j].detail_value=detail_value_arr[j];
            module.content.dataset[j].detail_name=detail_name_arr[j];
			module.content.dataset[j].color=color_arr[j];
            var picUrl=new_baseurl+imgArr[j];
            if(picUrl.indexOf("weixinpl")>0){ //判断图片路径
                module.content.dataset[j].pic=picUrl;
            }
            else{
                var aaa = imgArr[j].indexOf("/");
                if(aaa == 0){
                   var defUrl=new_baseurl+imgArr[j];
                }else{
                   var defUrl=new_baseurl+"/weixinpl/common_shop/common/custom_temp/"+imgArr[j];
                }
                module.content.dataset[j].pic=defUrl;
            }
             }
        custom_query(module);
		var select_value_arr=[];
		var detail_value_arr=[];
		var detail_name_arr=[];
    }

});

</script>

<!--选择链接的JS开始-->
<script>
//var p_detail_id = -1;
function changeProductType(selv,sort,detail_id){
  //var selv =  sel.value;
  //alert(selv);
//  alert('==selv='+selv);

  document.getElementById("div_products_2_"+sort).style.display="none";
  if(selv.indexOf("_1")!=-1){
     //是产品分类
     document.getElementById("div_products_2_"+sort).style.display="block";
     var pro_typeid= selv.substring(0,selv.indexOf("_1"));
	 if( detail_id != '' ){
		 url='get_product_list.php?callback=jsonpCallback_get_product_list&type_id='+pro_typeid+'&sort='+sort+'&detail_id='+detail_id+'&supplier_id='+supplier_id;
	 }else{
		 url='get_product_list.php?callback=jsonpCallback_get_product_list&type_id='+pro_typeid+'&sort='+sort+'&supplier_id='+supplier_id;
	 }
     $.jsonp({
        url:url,
        callbackParameter: 'jsonpCallback_get_product_list'
    });
  }
}
//var detail_id=<?php echo $detail_id; ?>;
// var detail_id=-1;
function jsonpCallback_get_product_list(results){
	var len = results.length;
    var sort=results[2].sort;
	var detail_id = results[3].detail_id;
   
   var sel_pro = document.getElementById("product_detail_id_2_"+sort);
   sel_pro.options.length=0;
   
    var new_option = new Option("---请选择一款产品---",-1);
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
		  console.log(imgurl);
            $('#getImg'+o).val(imgurl);
            $('#getImg'+o).change();
       });

    return false; // 必须返回false，否则表单会自己再做一次提交操作，并且页面跳转   
}

</script>

<!--选择链接的JS结束-->
</body>
</html>  
<?php 

mysql_close($link);
?>