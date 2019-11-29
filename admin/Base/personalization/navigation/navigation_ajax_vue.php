<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../../weixinpl/config.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require_once('../../../../../weixinpl/common/common_ext.php');
require_once('../../../../../weixinpl/common/utility_setting_function.php');

$column        = i2post("column",""); //操作
$nav_is_publish = check_nav_is_publish($column,$customer_id);
$is_publish = check_is_publish(2,$column,$customer_id);

$funs = array();
$have_kf = 0;//是否有插入客服功能
if ($nav_is_publish['nav_in_page']){
//    $nav_sql = "select name,icon_url,page_url,funs from navigation_using where customer_id=".$customer_id." and isvalid=true order by sort desc";
    $nav_sql = "select ns.icon_url,ns.page_url from ".WSY_SHOP.".navigation_icon_setting as ns inner join ".WSY_SHOP.".navigation_template_setting as ts on ts.id = ns.tmp_id and ts.isvalid =true and ts.is_shelve = true where ns.customer_id=".$customer_id." and ns.display= 1 and ns.isvalid=true and ts.id = '{$nav_is_publish['nav_id']}' order by ns.sort asc";
    $nav_result= _mysql_query($nav_sql) or die('nav_sql failed: ' . mysql_error());
    $nav_result_num= mysql_num_rows($nav_result);
}
$fun = $column;
$return_live_room = $_COOKIE['return_live_room'];
$position = $nav_is_publish['nav_position'];
$nav_left = '';
if($position == 2){
    $nav_left = 'floating-left';
}
//$str1 = <<< EOF
//    <style>
///*share_start*/
//#share{position:fixed;bottom:62px;left:90%;width:30px;zoom:1;z-index:49;opacity: 0.8;}
//#share a{
//	background-image:url(/weixinpl/mshop/images/list_image/wa4.png);background-repeat:no-repeat;display:block;width:30px;height:30px;overflow:hidden;text-indent:-999px;
//	background-size: cover;margin:0;
//}
//#share .hideicon{background-position:0 0;}
//#share a.hideicon:hover{background-position:0 0;}
//#share .hideicon.close{transform:rotate(180deg);-webkit-transform:rotate(180deg);opacity:1;}
//#share .homepage{background-position:0 -30px;}
//#share a.homepage:hover{background-position:0 -30px;}
//#share .personal{background-position:0 -60px;}
//#share a.personal:hover{background-position:0 -60px;}
//#share .pingtuan{background-position:0 -232px;height: 30px;}
//#share a.pingtuan:hover{background-position:0 -232px;}
//#share .eye{background-position:0 -90px;}
//#share a.eye:hover{background-position:0 -90px;}
//#share .heart{background-position:0 -120px;}
//#share a.heart:hover{background-position:0 -120px;}
//#share .chat{background-position:0 -182px;}
//#share a.chat:hover{background-position:0 -182px;}
//#share .supply_chat{background-position:0 -182px;}
//#share a.supply_chat:hover{background-position:0 -182px;}
//#share a#totop{background-position:0 -210px;cursor:pointer;display: none;}
//#share a#totop:hover{background-position:0 -210px;}
//</style>
//EOF;
//
//if(!empty($funs)){
//    if($fun =="ordering_retail" || $fun == "proxy_apply" || $fun == "proxy_login" || $fun == "f2c" || $fun == "promotion_renewal" || $fun == "my_shop_reward" || $fun == 'proxy_sale_order_list' || $fun == "bargain_record" ){
//        $str2 = <<<EOF
//<link href="/weixinpl/mshop/css/floatingwindowrem.css" type="text/css" rel="stylesheet">
//EOF;
//    }else{
//        $str2 = <<<EOF
//<link href="/weixinpl/mshop/css/floatingwindow.css" type="text/css" rel="stylesheet">
//EOF;
//    }
//}

$query_online = "select need_online,online_type,online_qq,online_custom,supply_chat,is_applymoney_startdate,is_applymoney_enddate
	,advisory_telephone from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
$online_qq 		= 0;
$online_type    = 1;
$online_qq      = '';
$supply_chat    = '';
$online_url     = '';
$result_online=_mysql_query($query_online);
while($row_online=mysql_fetch_object($result_online)){
    $need_online 	= $row_online->need_online;
    $online_type 	= $row_online->online_type;
    $online_qq		= $row_online->online_qq;
    $online_custom	= $row_online->online_custom;
    $supply_chat	= $row_online->supply_chat;
}
if($need_online){
    $online_url="/weixinpl/online/show_online.php?customer_id=".$customer_id_en;
    if($online_type==2){
        //qq咨询
        if ($from_type == 2) {    //app
            if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'iphone') || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'ipad' )) {
                $online_url="mqq://wpa.qq.com/msgrd?v=3&uin=".$online_qq."&site=qq&menu=yes";
            }else if( strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'android') ){
                $online_url="mqqwpa://wpa.qq.com/msgrd?v=3&uin=".$online_qq."&site=qq&menu=yes";
            }
        } else {
            $online_url="http://wpa.qq.com/msgrd?v=3&uin=".$online_qq."&site=qq&menu=yes";
        }
    }
    if($online_type==4){
        //自定义链接
        $i = strripos($online_custom,'http://');
        $j = strripos($online_custom,'https://');
        if($i>=0 or $j>=0){
            $online_url=$online_custom;
        }else{
            $online_url='//'.$online_custom;
        }
    }
}

if (!$nav_is_publish['is_set_nav']){
    $set1 = ($is_nav==1)?'hideicon':'hideicon ui-link ui-btn-up-undefined close';
    $set2 = ($is_nav==1)?'block':'none';
    $set3 = ($supplier_kefu['is_kefu'] == 1 or $online_type == 5)?'supply_chat':'chat';
    $set4 = ($supplier_kefu['is_kefu'] == 1 or $online_type == 5)?'supply_chat':'chat';
    $str3 = <<<EOF
    	<!--悬浮按钮-->
	<!-- <div id="share">
	    <a class="hideicon {$set1} title="隐藏" >隐藏图标</a>
		<div class="all" style="display:{$set2}" >
			<a class="homepage"  onclick="locatP(1);">homepage</a>
			<a class="personal"  onclick="locatP(5);">personal</a>
			<a class="pingtuan"  onclick="locatP(6);">pingtuan</a>
			<a class="eye"   onclick="locatP(2);">eye</a>
			<a class="heart"  onclick="locatP(3);">heart</a>
		</div>
		<a class="{$set3}"  onclick="locatP(4);">{$set4}</a>
		<a id="totop" title="返回顶部" ></a>
	</div> -->
	<!--悬浮按钮-->
EOF;
}elseif($nav_is_publish['nav_in_page'] && $nav_is_publish['nav_style'] == 1 && $nav_result_num>0){
    //此页有自定义导航，风格1
    $temp1_1 = <<<EOF
	<div id="template7">
		<div class="floating-window-box7 {$nav_left}">
			<div class="add-show7">
				<img class="transform180" src="/weixinpl/mshop/images/icon-arroww.png"/>
			</div>
			<div class="floating-window-abs7">
				<div class="floating-window7">
EOF;
    $i = 0;
    $temp1_5 = '';
    while( $row = mysql_fetch_object($nav_result) ) {
        $name = $row->name;
        $icon_url = $row->icon_url;
        $page_url = $row->page_url;
        $funs = $row->funs;

        if (!empty($funs)) {
            $page_url = location_link($funs, $customer_id);
            $page_url = $protocol_http_host . $page_url;
        }
        $set1 = ($i == 0)?'id="customerService"':'';
        $temp1_2 = <<<EOF
        <div {$set1} class="icon-box7">
EOF;
        if($funs == 'customer_service'){
            $temp1_3 = <<<EOF
<a onclick="locatP(4);"><img src="{$icon_url}">
                </a>
                </div>
EOF;
        }else{
            $temp1_3 = <<<EOF
<a href="{$page_url}"><img src="{$icon_url}">
    </a>
    </div>
EOF;
        }
        $i++;
        $temp1_6 = $temp1_2.$temp1_3;
        $temp1_5 .= $temp1_6;
    }

    $temp1_4 = <<<EOF
				</div>
			</div>
			<div class="pack-up7" id="packUp7">
				<img class="pack-up-icon7" src="/weixinpl/mshop/images/icon-topw.png"/>
			</div>
		</div>
	</div>
EOF;
    $str3 = $temp1_1.$temp1_5.$temp1_4;

}elseif ($nav_is_publish['nav_in_page'] && $nav_is_publish['nav_style'] == 2 && $nav_result_num>0) {
    //此页有自定义导航，风格2
    $temp1_1 = <<<EOF
<div class="shadow"></div>
	<div id="template1">
		<div class="floating-window-box1 $nav_left">
			<div class="pack-up1" id="packUp1">
				<img class="pack-up-icon1" src="/weixinpl/mshop/images/icon-rightw.png"/>
				<div class="pack-up-title1">收起</div>
			</div>
			<div class="floating-window-abs1">
				<div class="floating-window1">
EOF;

    $temp1_3 = '';
    while ($row = mysql_fetch_object($nav_result)) {
        $name = $row->name;
        $icon_url = $row->icon_url;
        $page_url = $row->page_url;
        $funs = $row->funs;

        if (!empty($funs)) {
            $page_url = location_link($funs, $customer_id);
            $page_url = $protocol_http_host . $page_url;
        }
        $set1 = ($funs == 'customer_service') ? 'onclick="locatP(4);"' : "href='{$page_url}'";
        $temp1_2 = <<<EOF
<div class="icon-box1"><a {$set1} ><img src="{$icon_url}"></a></div>
EOF;
        $temp1_3 .= $temp1_2;

    }
    $temp1_4 = <<<EOF
				</div>
			</div>
		</div>
		<div class="back-to-top1 {$nav_left}" id="backTop1">
			<img class="back-to-top-icon1" src="/weixinpl/mshop/images/icon-topw.png"/>
		</div>
	</div>
EOF;
    $str3 = $temp1_1.$temp1_3.$temp1_4;
}elseif($nav_is_publish['nav_in_page'] && $nav_is_publish['nav_style'] == 3 && $nav_result_num>0) {
    //此页有自定义导航，风格3
    if($fun =="ordering_retail" || $fun == "proxy_apply" || $fun == "proxy_login" || $fun == "f2c" || $fun == "promotion_renewal"){
        if($is_publish) $set1 = 'bottom: 0.98rem;';
    }else{
        if($is_publish) $set1 = 'bottom: 50px;';
    }
    $temp1_1 = <<<EOF
<div class="shadow"></div>
	<div id="template2">
		<div class="floating-window-box2 {$nav_left}" style="{$set1}">
			<div class="floating-window-abs2">
				<div class="floating-window2">
EOF;
    $temp1_3 = '';
    while( $row = mysql_fetch_object($nav_result) ){
        $name     = $row->name;
        $icon_url = $row->icon_url;
        $page_url = $row->page_url;
        $funs     = $row->funs;

        if (!empty($funs)){
            $page_url = location_link($funs,$customer_id);
            $page_url = $protocol_http_host.$page_url;
        }

        $set2 = ($funs == 'customer_service')?'onclick="locatP(4);"':"href='{$page_url}'";
        $temp1_2 = <<<EOF
    <div class="icon-box2"><a {$set2} ><img src="{$icon_url}"></a></div>
EOF;
        $temp1_3 .= $temp1_2;
    }

    $temp1_4 = <<<EOF
				</div>
			</div>
			<div class="close2" id="close2">
				<img class="close-icon2" src="/weixinpl/mshop/images/icon-closeg.png"/>
			</div>
		</div>
		<div class="add-show2 {$nav_left}" id="add-show2">
			<img src="/weixinpl/mshop/images/icon-addw.png"/>
		</div>
		<div class="pack-up2 {$nav_left}" id="packUp2">
			<img class="pack-up-icon2" src="/weixinpl/mshop/images/icon-topw.png"/>
		</div>
	</div>
EOF;

    $str3 = $temp1_1.$temp1_3.$temp1_4;
}elseif($nav_is_publish['nav_in_page'] && $nav_is_publish['nav_style'] == 4 && $nav_result_num>0){
    //此页有自定义导航，风格4
    if($fun =="ordering_retail" || $fun == "proxy_apply" || $fun == "proxy_login" || $fun == "f2c" || $fun == "promotion_renewal"){
        if ($is_publish) $set1 = 'bottom: 0.98rem;';
    }else{
        if ($is_publish) $set1 = 'bottom: 50px;';
    }
    $temp1_1 = <<<EOF
	<div id="template3">
		<div class="floating-window-box3 {$nav_left}" style="{$set1}">
			<div class="floating-window-abs3">
				<div class="floating-window3" style="overflow-scrolling: touch;-webkit-overflow-scrolling:touch;">
EOF;
    $temp1_3 = '';
    while( $row = mysql_fetch_object($nav_result) ){
        $name     = $row->name;
        $icon_url = $row->icon_url;
        $page_url = $row->page_url;
        $funs     = $row->funs;

        if (!empty($funs)){
            $page_url = location_link($funs,$customer_id);
            $page_url = $protocol_http_host.$page_url;
        }

        $set2 = ($funs == 'customer_service')?'onclick="locatP(4);"':"href='{$page_url}'";
        $temp1_2 = <<<EOF
    <div class="icon-box3"><a {$set2} ><img src="{$icon_url}"></a></div>
EOF;
        $temp1_3 .= $temp1_2;
    }
    $temp1_4 = <<<EOF
</div>
			</div>
			<div class="close3" id="close3">
				<img class="close-icon3" src="/weixinpl/mshop/images/icon-closeg.png"/>
			</div>
		</div>
		<div class="add-show3 {$nav_left}" id="add-show3">
			<img src="/weixinpl/mshop/images/icon-addw.png"/>
		</div>
		<div class="pack-up3 {$nav_left}" id="packUp3">
			<img class="pack-up-icon3" src="/weixinpl/mshop/images/icon-topw.png"/>
		</div>
	</div>
EOF;
    $str3 = $temp1_1.$temp1_3.$temp1_4;
}elseif($nav_is_publish['nav_in_page'] && $nav_is_publish['nav_style'] == 5 && $nav_result_num>0){
    //此页有自定义导航，风格5
    if($fun =="ordering_retail" || $fun == "proxy_apply" || $fun == "proxy_login" || $fun == "f2c" || $fun == "promotion_renewal"){
        if ($is_publish) $set1 = 'bottom: 0.98rem;';
    }else{
        if ($is_publish) $set1 = 'bottom: 50px;';
    }
    $temp1_1 = <<<EOF
    	<div id="template4">
		<div class="floating-window-box4 {$nav_left}" style="{$set1}">
			<div class="floating-window-abs4">
				<div class="floating-window4">
EOF;
    $temp1_3 = '';
    while( $row = mysql_fetch_object($nav_result) ) {
        $name = $row->name;
        $icon_url = $row->icon_url;
        $page_url = $row->page_url;
        $funs = $row->funs;

        if (!empty($funs)) {
            $page_url = location_link($funs, $customer_id);
            $page_url = $protocol_http_host . $page_url;
        }

        $set2 = ($funs == 'customer_service')?'onclick="locatP(4);"':"href='{$page_url}'";
        $temp1_2 = <<<EOF
						<div class="icon-box4"><a {$set2} ><img src="{$icon_url}"></a></div>
EOF;
        $temp1_3 .= $temp1_2;
    }
    $temp1_4 = <<<EOF
</div>
			</div>
			<div class="close4" id="close4">
				<img class="close-icon4" src="/weixinpl/mshop/images/icon-closew.png"/>
			</div>
		</div>
		<div class="add-show4 {$nav_left}" id="add-show4">
			<img src="/weixinpl/mshop/images/icon-addw.png"/>
		</div>
		<div class="pack-up4 {$nav_left}" id="packUp4">
			<img class="pack-up-icon4" src="/weixinpl/mshop/images/icon-topw.png"/>
		</div>
	</div>
EOF;
    $str3 = $temp1_1.$temp1_3.$temp1_4;
}elseif($nav_is_publish['nav_in_page'] && $nav_is_publish['nav_style'] == 6 && $nav_result_num>0){
//此页有自定义导航，风格6
    $temp1_1 = <<<EOF
<div id="template5">
    <div class="floating-window-box5 {$nav_left}">
        <div class="floating-window-abs5">
            <div class="floating-window5">
EOF;
    $temp1_3 = '';
    while( $row = mysql_fetch_object($nav_result) ) {
        $name = $row->name;
        $icon_url = $row->icon_url;
        $page_url = $row->page_url;
        $funs = $row->funs;

        if (!empty($funs)) {
            $page_url = location_link($funs, $customer_id);
            $page_url = $protocol_http_host . $page_url;
        }

        $set2 = ($funs == 'customer_service')?'onclick="locatP(4);"':"href='{$page_url}'";
        $temp1_2 = <<<EOF
						<div class="icon-box5"><a {$set2} ><img src="{$icon_url}"></a></div>
EOF;
        $temp1_3 .= $temp1_2;
    }
    $temp1_4 = <<<EOF
</div>
			</div>
			<div class="add-show5">
				<img src="/weixinpl/mshop/images/icon-addw.png"/>
			</div>
			<div class="pack-up5" id="packUp5">
				<img class="pack-up-icon5" src="/weixinpl/mshop/images/icon-topw.png"/>
			</div>
		</div>
	</div>
EOF;

    $str3 = $temp1_1.$temp1_3.$temp1_4;
}

if(!empty($fun)){
    if($fun =="ordering_retail" || $fun == "proxy_apply" || $fun == "proxy_login" || $fun == "f2c" || $fun == "promotion_renewal" || $fun == "my_shop_reward" || $fun == 'proxy_request_order' || $fun == 'proxy_sale_order_list' || $fun == 'proxy_son_store_login' || $fun == 'proxy_order_login' || $fun == 'proxy_store_center' || $fun == 'renew_area' || $fun == 'proxy_son_order_list' || $fun == 'proxy_my_reward' || $fun == 'ordering_retail' || $fun == 'proxy_apply' || $fun == 'proxy_login' || $fun == 'retail_main_funs' || $fun == 'proxy_purchase_product_list' || $fun == 'proxy_order_retail' || $fun == 'proxy_account_manager' || $fun == 'proxy_store_list' || $fun == 'proxy_near_store' || $fun == 'proxy_store_center' || $fun == 'proxy_request_store' || $fun =="proxy_request_order" || $fun == "proxy_request_warehouse" || $fun == "proxy_request_son_store" || $fun == "proxy_order_login" || $fun == "proxy_son_store_login" || $fun == "proxy_order_list" || $fun == 'proxy_purchase_order_list' || $fun == 'proxy_sale_order_list' || $fun == 'proxy_retail_order_list' || $fun == 'proxy_send_order_list' || $fun == 'proxy_son_order_list' || $fun == 'proxy_my_deal_order' || $fun == 'proxy_store_deal_order' || $fun == 'proxy_son_deal_order' || $fun == 'proxy_stock_count' || $fun == 'proxy_account_award' || $fun == 'proxy_my_reward' || $fun == 'proxy_my_team' || $fun == 'proxy_son_stock' || $fun == 'proxy_send_reward' || $fun == 'proxy_team_list' || $fun == 'proxy_my_sales' || $fun == 'proxy_my_orders' || $fun == 'proxy_order_detail' || $fun == 'proxy_purchase_order_detail' || $fun == 'proxy_retail_order_detail' || $fun == 'proxy_send_order_detail' || $fun == 'proxy_reward_detail' || $fun == 'proxy_my_reward_detail' || $fun == 'proxy_my_team_reward_detail' || $fun == 'proxy_stock' || $fun == 'proxy_sale_order_detail' || $fun == 'f2c_apply' || $fun == 'f2c' || $fun == 'f2c_main_funs' || $fun == 'f2c_purchase' || $fun == 'f2c_order_list' || $fun == 'f2c_purchase_order' || $fun == 'f2c_sale_order' || $fun == 'f2c_agent_order' || $fun == 'f2c_data_statistics' || $fun == 'f2c_my_reward' || $fun == 'f2c_order_detail' || $fun == 'f2c_purchase_detail' || $fun == 'f2c_sale_detail' || $fun == 'f2c_agent_order_detail' ){
        $str6 = <<<EOF
<script src="/weixinpl/mshop/js/floatingwindowrem.js"></script>
EOF;
    }else{
        $str6 = <<<EOF
<script src="/weixinpl/mshop/js/floatingwindow.js"></script>
EOF;
    }
}

$str4 = <<<EOF
<script src="//res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<script>
var is_kefu = '{$supplier_kefu['is_kefu']}';
var kefu_type = '{$supplier_kefu['kefu_type']}';
var need_online  ='{$need_online}';	//是否开启在线客服
var online_type  ='{$online_type}';	//在线客服类型 1:在线客服 2:QQ 3:多客服 4:自定义链接 5小能客服
var is_publish = '{$is_publish}'; //是否开启底部菜单
$(function(){

	$('.hideicon').click(function(){
		$('.all').slideToggle();
		$('.hideicon').toggleClass('close');
	})
	$("#totop").click(function(){
		$("body,html").animate({scrollTop:0},500);
	})
	$(window).scrollTop();
	$(window).scroll(function(){
		if($(window).scrollTop()>0)
		{
			$('#totop').css('display','block');
		}
		else{
			$('#totop').css('display','none');
		}
	})
})
	    function locatP(type){ // Tab Selection
			if(type == 1){
				window.location.href="/weixinpl/common_shop/jiushop/index.php?customer_id="+'{$customer_id_en}';
			}else if(type == 2){
				window.location.href="/weixinpl/mshop/my_visit.php?customer_id="+'{$customer_id_en}';
			}else if(type == 3){
				window.location.href="/weixinpl/mshop/my_collect.php?customer_id="+'{$customer_id_en}';
			}else if(type == 4){
				if( is_kefu == 1){		//品牌商客服
					if( kefu_type == 1){			//QQ客服
						location.href = "http://wpa.qq.com/msgrd?v=3&uin={$supply_qq}&site=qq&menu=yes";
					}else if(kefu_type == 2){	//小能客服
						NTKF.im_openInPageChat('{$xiaoneng}');
					}
				}else{					//平台，普通供应商
					if( need_online == 1){			//平台开启客服
						if(online_type == 3){		//多客服
							wx.closeWindow();
						}else if(online_type == 5){	//小能客服
							NTKF.im_openInPageChat('{$supply_chat}')
						}else {
							window.location.href="{$online_url}";
						}
					}else{
						alert('商家未开启客服');
						return;
					}
				}

		  }else if(type == 5){
				window.location.href="/weixinpl/mshop/personal_center.php?customer_id="+'{$customer_id_en}';
		  }else if(type == 6){
				window.location.href="/market/web/collageActivities/my_collages_record_list_view.php?customer_id="+'{$customer_id_en}';
		  }
		}
</script>
{$str6}
EOF;
$user_name ='';
$user_id = $_SESSION['user_id_'.$customer_id];
if( $user_id ){
    $user_query = "SELECT weixin_name FROM weixin_users WHERE isvalid=true AND customer_id=$customer_id and id=".$user_id;
    // var_dump($_SESSION);
    // echo $user_query;
    $result= _mysql_query($user_query) or die('user_query failed : ' . mysql_error());
    while( $row = mysql_fetch_object($result) ){
        $user_name = $row->weixin_name;
    }
}
$str5 = '';
if($supplier_kefu['kefu_type'] == 2 || $online_type == 5) {
    $aaa = SITDID;
    $str5 = <<<EOF
<script>
	<!--小能客服系统集成脚本 -->
	 var NTKF_PARAM = {
	  siteid:"{$aaa}",                    //企业ID，为固定值，必填
	 settingid:"{$supply_chat}",   //接待组ID，为固定值，必填
	  uid:"{$user_id}",                         //用户ID
	  uname:"{$user_name}",             //用户名
	  //erpparam:"abc",                      //erpparam为erp功能的扩展字段
	  itemid:"{$pid}"			       //(必填)商品ID
	  }
</script>
<script type="text/javascript" src="//dl.ntalker.com/js/xn6/ntkfstat.js?siteid={$aaa}" charset="utf-8"></script>
EOF;
}

$navagation = $str3.$str4.$str5;
die(json_encode(array('data'=>$navagation)));




