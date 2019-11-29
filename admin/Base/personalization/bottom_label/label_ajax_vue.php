<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../../weixinpl/config.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require_once('../../../../../weixinpl/common/common_ext.php');
require_once('../../../../../weixinpl/common/utility_setting_function.php');

$fun        = i2post("column",""); //操作

	if($fun == "order_cart"){   // 当为购物车页面时，获取customer_id，异步获取数据
        header("Content-type: text/html; charset=utf-8");
        require_once('../../../../../weixinpl/back_newshops/Base/personalization/config.php');

        $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
        mysql_select_db(DB_NAME) or die('Could not select database');

    }

	if(empty($customer_id_en)){
        $customer_id_en = $customer_id;
    }
    /*查找使用中的底部标签*/
    //$label_sql = "select us.id,us.name,us.icon_url,us.icon_url_selected,us.page_url,us.column_id,page.funs from bottom_label_using as us inner join page_column_t as page on page.id = us.column_id where us.isvalid=true and page.isvalid=true and page.type=2 and us.customer_id=".$customer_id." order by us.`sort` desc";
//    $label_sql = "select id,name,icon_url,icon_url_selected,page_url,column_id,funs from bottom_label_using where isvalid=true and customer_id=".$customer_id." order by `sort` desc";
    $rcount = 0;
    //		$sql = "select count(1) as rcount from ".WSY_SHOP.".publish_page_t where isvalid=true and customer_id=".$customer_id." and funs='".$fun."' and type=".$type;
    $sql = "SELECT n.id as rcount from ".WSY_SHOP.".publish_page_management as p inner join ".WSY_SHOP.".bottom_label_template_setting as n on n.id = p.tmp_id and n.isvalid = true and n.is_shelve = true where p.customer_id = '{$customer_id}' and p.isvalid = true and p.type = 2 and p.funs = '{$fun}'";
    $result = _mysql_query($sql) or die('sql failed: ' . mysql_error());

    while ($row = mysql_fetch_object($result)) {
        $rcount =(int)$row->rcount;
    }

    /*查找发布底部标签的个数*/
    //		$check_num_sql = "select count(1) as wcount from ".WSY_SHOP.".bottom_label_using where isvalid=true and customer_id=".$customer_id;
    $check_num_sql = "SELECT count(*) as wcount from ".WSY_SHOP.".bottom_label_icon_setting where isvalid =true and customer_id = '{$customer_id}' and tmp_id='{$rcount}'";
    $check_result = _mysql_query($check_num_sql) or die('check_num_sql failed: ' . mysql_error());

    while ($check_row = mysql_fetch_object($check_result)) {
        $wcount =(int)$check_row->wcount;
    }

    if($rcount > 0 && $wcount>0){
        $is_publish = $rcount;
    }else{
//        $is_publish = false;
        die(json_encode(array('data'=>'')));
    }

    $sql_position = "SELECT position from ".WSY_SHOP.".bottom_label_template_setting where isvalid =true and id = '{$is_publish}' and is_shelve = true";
    $result_position = _mysql_query($sql_position) or die("position_sql fail:".mysql_error());
    while ($row_position = mysql_fetch_object($result_position)) {
        $position = $row_position->position;
    }
    $label_sql = "select ns.id,ns.icon_url,ns.icon_url_selected,ns.page_url,ns.column_id,ns.name,ns.color,ns.color_selected from ".WSY_SHOP.".bottom_label_icon_setting as ns inner join ".WSY_SHOP.".bottom_label_template_setting as ts on ts.id = ns.tmp_id and ts.isvalid =true and ts.is_shelve = true where ns.customer_id=".$customer_id." and ns.display= 1 and ns.isvalid=true and ts.id = '{$is_publish}' order by ns.sort asc";

	$result = _mysql_query($label_sql) or die("label_sql fail:".mysql_error());

    if($wcount >0) {

        $str1 = '';
        if (!empty($fun)) {
            if ($fun == "ordering_retail" || $fun == "proxy_apply" || $fun == "proxy_login" || $fun == "f2c" || $fun == "promotion_renewal" || $fun == "my_shop_reward" || $fun == 'crowd_funding_list' || $fun == 'bargain_index' || $fun == 'proxy_sale_order_list' || $fun == 'bargain_record' || $fun == 'crowd_funding_record') {
                $str1 = <<<EOF
        <style type="text/css">
    .footer{position: fixed;bottom: 0px;left: 0px;width: 100%;min-height: 0.98rem;background:#fff;z-index: 1000;line-height: 0.48rem;border-top: 0.02rem solid #eeeeee;box-shadow: 0 0 10px 0 rgba(155,143,143,0.6);
        -webkit-box-shadow: 0 0 10px 0 rgba(155,143,143,0.6);    padding: 0px;}
    .footer .footer-box{margin:0 auto;width: 100%;height: 0.98rem;display: -webkit-box;}
    .footer .footer-box .weidian{height: 0.98rem;text-align: center;-webkit-box-flex: 1;
        -moz-box-flex: 1;display:flex;align-items:center;justify-content:center;}
    .footer .footer-box .weidian img{width: 0.98rem;height: 0.98rem;vertical-align: middle;}
    .footer .footer-box .weidian p{font-size: 0.24rem;color: #a1a1a1;margin: 0;}
    .footer .footer-box .weidian.active p{color:#64b83c;white-space:nowrap;text-overflow:clip;overflow: hidden;}
    .footer .footer-box .weidian p.foot_grey{color: #a1a1a1;}
    .paddingBottom{height:0.98rem;}
    .footer.hasname{position:fixed;bottom:0px;left:0px;width:100%;height:49px;background:#fff;z-index:50;line-height:24px;border-top:1px solid #eeeeee;box-shadow:0 0 10px 0 rgba(155,143,143,0.6);-webkit-box-shadow:0 0 10px 0 rgba(155,143,143,0.6);padding:0px}
    .footer.hasname .footer-box{margin:0 auto;width:100%;height:49px;display:-webkit-box}
    .footer.hasname .footer-box .weidian{height:49px;text-align:center;-webkit-box-flex:1;-moz-box-flex:1;display:flex;align-items:center;justify-content:center;float:none}
    .footer.hasname .footer-box .weidian p{font-size:12px;color:#a1a1a1;margin:0;line-height:14px;overflow:hidden}
    .footer.hasname .footer-box .weidian.active p{color:#64b83c;white-space:nowrap;text-overflow:clip;overflow:hidden}
    .footer.hasname .footer-box .weidian p.foot_grey{color:#a1a1a1}
    .paddingBottom{height:49px}
    .footer.hasname .footer-box .weidian img{width:32px;height:32px;margin:0 auto;vertical-align:middle}
    .footer.hasname .footer-box .weidian .foot-text{font-size:10px;line-height:14px;white-space:nowrap;overflow:hidden}
</style>
EOF;

            } else {
                $str1 = <<<EOF
        <style type="text/css">
    .footer{position: fixed;bottom: 0px;left: 0px;width: 100%;min-height: 49px;background:#fff;z-index: 50;line-height: 24px;border-top: 1px solid #eeeeee;box-shadow: 0 0 10px 0 rgba(155,143,143,0.6);
        -webkit-box-shadow: 0 0 10px 0 rgba(155,143,143,0.6);padding: 0px;}
    .footer .footer-box{margin:0 auto;width: 100%;height: 49px;display: -webkit-box;}
    .footer .footer-box .weidian{height: 49px;text-align: center;-webkit-box-flex: 1;
        -moz-box-flex: 1;display:flex;align-items:center;justify-content:center;}
    .footer .footer-box .weidian img{width: 49px;height: 49px;vertical-align: middle;}
    .footer .footer-box .weidian p{font-size: 12px;color: #a1a1a1;margin: 0;}
    .footer .footer-box .weidian.active p{color:#64b83c;white-space:nowrap;text-overflow:clip;overflow: hidden;}
    .footer .footer-box .weidian p.foot_grey{color: #a1a1a1;}
    .paddingBottom{height:49px;}
    .footer.hasname{position:fixed;bottom:0px;left:0px;width:100%;height:49px;background:#fff;z-index:50;line-height:24px;border-top:1px solid #eeeeee;box-shadow:0 0 10px 0 rgba(155,143,143,0.6);-webkit-box-shadow:0 0 10px 0 rgba(155,143,143,0.6);padding:0px}
    .footer.hasname .footer-box{margin:0 auto;width:100%;height:49px;display:-webkit-box}
    .footer.hasname .footer-box .weidian{height:49px;text-align:center;-webkit-box-flex:1;-moz-box-flex:1;display:flex;align-items:center;justify-content:center;float:none}
    .footer.hasname .footer-box .weidian p{font-size:12px;color:#a1a1a1;margin:0;line-height:14px;overflow:hidden}
    .footer.hasname .footer-box .weidian.active p{color:#64b83c;white-space:nowrap;text-overflow:clip;overflow:hidden}
    .footer.hasname .footer-box .weidian p.foot_grey{color:#a1a1a1}
    .paddingBottom{height:49px}
    .footer.hasname .footer-box .weidian img{width:32px;height:32px;margin:0 auto;vertical-align:middle}
    .footer.hasname .footer-box .weidian .foot-text{font-size:10px;line-height:14px;white-space:nowrap;overflow:hidden}
</style>
EOF;
            }
        }

        if($position==1)    $static = 'static';
        $str2 = <<<EOF
    <!--底部按钮-->
<div class="footer hasname" style="position: {$static};">
    <div class="footer-box">
EOF;
        $temp2 = '';
        while ($row = mysql_fetch_object($result)) {
            $keyid = $row->id;
            $name = $row->name;
            $icon_url = $row->icon_url;
            $icon_url_selected = $row->icon_url_selected;
            $page_url = $row->page_url;
            $column_id = (int)$row->column_id;
            $funs = $row->funs;
            $color = $row->color;
            $color_selected = $row->color_selected;

            if(strpos($icon_url,Protocol) == false){
                if($icon_url == '/weixinpl/back_newshops/Common/images/Base/personal_center/gift.png'){
                    $icon_url = Protocol.$_SERVER['HTTP_HOST'].$icon_url;
                }else{
                    $icon_url = Protocol.$_SERVER['HTTP_HOST'].$icon_url."?x-oss-process=image/resize,w_200";
                }
            }
            if(strpos($icon_url_selected,Protocol) == false){
                if($icon_url == '/weixinpl/back_newshops/Common/images/Base/personal_center/gift.png'){
                    $icon_url_selected = Protocol.$_SERVER['HTTP_HOST'].$icon_url_selected;
                }else{
                    $icon_url_selected = Protocol.$_SERVER['HTTP_HOST'].$icon_url_selected."?x-oss-process=image/resize,w_200";
                }
            }

            /*获取跳转链接*/
            if ($column_id > 0) {
                $jump_url = location_link($funs, $customer_id_en);
                $jump_url = "http://" . $_SERVER['HTTP_HOST'] . $jump_url;
            } else {
                $jump_url = $page_url;
            }

            $page_url_array = explode('?',$page_url);
            $page_url1 = $page_url_array[0];
            $page_url2 = $page_url_array[1];
            parse_str($page_url2,$page_url2_1);

            //获取当前url
            $page_url1_2 = $_SERVER['PHP_SELF'];
            parse_str($_SERVER['QUERY_STRING'],$page_url2_2);

            $a = count($page_url2_1);
            $b = count($page_url2_2);

            $show_select = 0;
            if( $page_url1 == $page_url1_2 && $a == $b && $a > 0 && $b >0){
                foreach ($page_url2_2 as $v){
                    if(in_array($v,$page_url2_1)){
                        $show_select = 1;
                    }else{
                        $show_select = 0;break;
                    }
                }
            }

            if ($show_select == 1) {

                $set2 = "<img src=\"<?php echo $icon_url_selected;?>\" alt=\"\">
                        <p style=\"color:#{$color_selected}\" class=\"foot-text\">{$name}</p>";
            } else {
                $set2 = "<img src=\"{$icon_url}\" alt=\"\">
                        <p style=\"color:#{$color};\" class=\"foot-text\">{$name}</p>";
            }

            $temp1 = <<<EOF
    <div class="weidian">
            <a onclick="onloadP('{$jump_url}')">
                {$set2}
            </a>
        </div>
EOF;
            $temp2 .= $temp1;
        }
        $temp3 = <<<EOF
    </div>
</div>
<div class="paddingBottom" style="display: {$static};"></div>
<!--底部按钮-->
<script>
    function onloadP(url){ // Tab Selection
        window.location.href = url;
    }
</script>
EOF;

        $bottom_label = $str1 . $str2 . $temp2 . $temp3;
        die(json_encode(array('data' => $bottom_label)));

    }else{
        die(json_encode(array('data'=>'')));
    }
