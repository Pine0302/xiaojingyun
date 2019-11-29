<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>导航-发布</title>
    <link rel="stylesheet" type="text/css" href="/weixin/plat/Public/css_V6.0/content.css">
    <link rel="stylesheet" type="text/css" href="/weixin/plat/Public/css_V6.0/content<?php echo $theme; ?>.css"><!--内容CSS配色·蓝色-->
    <!--<link rel="stylesheet" type="text/css" href="../css/contentGreen.css">--><!--内容CSS配色·绿色-->
    <!--<link rel="stylesheet" type="text/css" href="../css/contentOrange.css">--><!--内容CSS配色·橙色-->
    <!--<link rel="stylesheet" type="text/css" href="../css/contentOrange1.css">--><!--内容CSS配色·粉色色-->
    <!--<link rel="stylesheet" type="text/css" href="../css/contentbgreen.css">--><!--内容CSS配色·蓝绿-->
    <!--<link rel="stylesheet" type="text/css" href="../css/contentGGreen.css">--><!--内容CSS配色·草绿-->
    <script type="text/javascript" src="/weixin/plat/Public/js_V6.0/assets/js/jquery.min.js"></script>

    <style>
        label input[type="radio"]{
            width: auto;
            height: auto;
        }
        .scdl{
            margin: 20px 10px 10px 45px;
            width: 120px;
            float: none;
            display: inline-block;
            vertical-align: top;
        }
        .WSY_competence_header h4 {
            display: block;
            float: left;
            font-size: 16px;
            padding-left: 10px;
            padding-right: 10px;
            line-height: 28px;
            font-size: 12px;
            font-weight:normal;
        }
    </style>
</head>

<body>
<!--内容框架-->
<div class="WSY_content">

    <!--列表内容大框-->
    <div class="WSY_columnbox">
        <!--列表头部切换开始-->
        <div class="WSY_column_header">
            <div class="WSY_columnnav">
                <a class="white1">导航-发布</a>
            </div>
        </div>
        <!--列表头部切换结束-->
        <!--搜索框-->
        <div id="search" style="right:50px;top:115px;">
            <div style="display: inline-block;width:50px;height:25px;margin-left: 50px;margin-top: 20px;">搜索</div>
            <input type="text" value="" id="searchVal" name="search" style="width:150px;height:25px;border:1px solid #ccc;">
            <input type="submit" value="搜索" onclick="like(); return false;" style="width:40px;border-radius:3px;height:26px;cursor:pointer;">
            <!--                    <input type="submit" value="发布" onclick="release(); return false;" style="margin-left:50px;width:40px;border-radius:3px;height:26px;cursor:pointer;">-->
        </div>
        <!-- </form> -->
        <!--权限管理代码开始-->
        <form action="/mshop/admin/index.php?m=navigation&a=template_release_edit" method="post" >
            <input type="hidden" name="tid" value="<?php echo $_GET['tid']?$_GET['tid']:0; ?>">
            <div class="WSY_data">
                <div class="WSY_competence">
                    <!--列表头部切换开始-->

                    <div class="WSY_competence_header">

                        <h3 id="h3">全选<input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> <?php echo $all_check?'checked':''; ?> id="s" onclick="$(this).attr('checked')?checkAll():uncheckAll()" ></h3>
                        <div id="nav_list">
                            <a><h4><input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> onclick="$(this).attr('checked')?checkmodel(this):uncheckmodel(this)" id="a1" name="links2[]" value="all_shop" >商城系统</h4></a>
                            <a><h4><input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> id="a2" onclick="$(this).attr('checked')?checkmodel(this):uncheckmodel(this)" name="links2[]" value="all_dinghuo" >订货系统</h4></a>
                            <a><h4><input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> id="a3" onclick="$(this).attr('checked')?checkmodel(this):uncheckmodel(this)" name="links2[]" value="all_f2c" >F2C系统</h4></a>
                            <a><h4><input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> id="a4" onclick="$(this).attr('checked')?checkmodel(this):uncheckmodel(this)" name="links2[]" value="all_dianshang" >电商直播</h4></a>
                            <a><h4><input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> id="a5" onclick="$(this).attr('checked')?checkmodel(this):uncheckmodel(this)" name="links2[]" value="all_voice" >语音直播</h4></a>
                            <a><h4><input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> id="a6" onclick="$(this).attr('checked')?checkmodel(this):uncheckmodel(this)" name="links2[]" value="all_o2o" >收银O2O</h4></a>
                            <a><h4><input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> id="a7" onclick="$(this).attr('checked')?checkmodel(this):uncheckmodel(this)" name="links2[]" value="all_cityarea" >城市商圈</h4></a>
                            <a><h4><input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> id="a8" onclick="$(this).attr('checked')?checkmodel(this):uncheckmodel(this)" name="links2[]" value="all_member" >会员系统</h4></a>
                            <a id="search_a">模块搜索</a>
                            <div style="display:none"><!-- 商城系统 -->
                                <ul data-id="0" class="WSY_competenceul shop_system" data-id="a1"  style="border:none;float:left;margin-left: 0px;">
                                    <dl class="scdl">
<!--                                        --><?php //$team1 = array('index,order_cart,personal_center,my_visit,my_collect,mshop_product_detail,mshop_product_list,diy_template,area_wholesaler_list'); ?>
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("default_page",$funs)) echo "style='display:none'"; ?> name="links2[]" data-id="" value="default_page" <?php if(in_array("default_page",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("default_page",$funs)) echo "style='display:none'"; ?>>默认页面</label>
                                        <?php if($show > 0 || !in_array("index",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="index" <?php if(in_array("index",$selected_funs2)){echo 'checked';} ?> /><label>首页</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("order_cart",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="order_cart" <?php if(in_array("order_cart",$selected_funs2)){echo 'checked';} ?> /><label>购物车</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("personal_center",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="personal_center" <?php if(in_array("personal_center",$selected_funs2)){echo 'checked';} ?> /><label>个人中心</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("my_visit",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_visit" <?php if(in_array("my_visit",$selected_funs2)){echo 'checked';} ?> /><label>足迹</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("my_collect",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_collect" <?php if(in_array("my_collect",$selected_funs2)){echo 'checked';} ?> /><label>收藏</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("mshop_product_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="mshop_product_detail" <?php if(in_array("mshop_product_detail",$selected_funs2)){echo 'checked';} ?> /><label>产品详情页</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("mshop_product_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="mshop_product_list" <?php if(in_array("mshop_product_list",$selected_funs2)){echo 'checked';} ?> /><label>产品列表页</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("diy_template",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="diy_template" <?php if(in_array("diy_template",$selected_funs2)){echo 'checked';} ?> /><label>自定义模板</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("area_wholesaler_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="area_wholesaler_list" <?php if(in_array("area_wholesaler_list",$selected_funs2)){echo 'checked';} ?> /><label>区域批发商列表</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("category_page",$funs)) echo "style='display:none'"; ?> name="links2[]" value="category_page" <?php if(in_array("category_page",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("category_page",$funs)) echo "style='display:none'"; ?>>分类页</label>
                                        <?php if($show > 0 || !in_array("category_page_2",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="category_page_2" <?php if(in_array("category_page_2",$selected_funs2)){echo 'checked';} ?> /><label>二级分类页</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("category_page_3",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="category_page_3" <?php if(in_array("category_page_3",$selected_funs2)){echo 'checked';} ?> /><label>三级分类页</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("category_page_4",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="category_page_4" <?php if(in_array("category_page_4",$selected_funs2)){echo 'checked';} ?> /><label>四级分类页</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("quick_purchase_page",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="quick_purchase_page" <?php if(in_array("quick_purchase_page",$selected_funs2)){echo 'checked';} ?> /><label>快速购买页</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("personal_center",$funs)) echo "style='display:none'"; ?> name="links2[]" value="personal_center" <?php if(in_array("personal_center",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("personal_center",$funs)) echo "style='display:none'"; ?>>个人中心</label>
                                        <?php if($show > 0 || !in_array("orderlist",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="orderlist" <?php if(in_array("orderlist",$selected_funs2)){echo 'checked';} ?> /><label>我的订单</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("card",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="card" <?php if(in_array("card",$selected_funs2)){echo 'checked';} ?> /><label>会员卡</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("my_privilege",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_privilege" <?php if(in_array("my_privilege",$selected_funs2)){echo 'checked';} ?> /><label>我的特权</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("myteam",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="myteam" <?php if(in_array("myteam",$selected_funs2)){echo 'checked';} ?> /><label>我的团队</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("queue_order_lists",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="queue_order_lists" <?php if(in_array("queue_order_lists",$selected_funs2)){echo 'checked';} ?> /><label>排队奖励</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("my_profit",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_profit" <?php if(in_array("my_profit",$selected_funs2)){echo 'checked';} ?> /><label>累计收益</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("my_address",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_address" <?php if(in_array("my_address",$selected_funs2)){echo 'checked';} ?> /><label>收货地址</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("my_store",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_store" <?php if(in_array("my_store",$selected_funs2)){echo 'checked';} ?> /><label>我的店铺</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("promoter_ranking",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="promoter_ranking" <?php if(in_array("promoter_ranking",$selected_funs2)){echo 'checked';} ?> /><label>推广员排行榜</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("authorization",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="authorization" <?php if(in_array("authorization",$selected_funs2)){echo 'checked';} ?> /><label>授权证书</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("charitable",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="charitable" <?php if(in_array("charitable",$selected_funs2)){echo 'checked';} ?> /><label>我的慈善</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("change_relation_user",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="change_relation_user" <?php if(in_array("change_relation_user",$selected_funs2)){echo 'checked';} ?> /><label>邀请人</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("order_list_takeaway_courier",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="order_list_takeaway_courier" <?php if(in_array("order_list_takeaway_courier",$selected_funs2)){echo 'checked';} ?> /><label>外卖配送</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("cashback",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cashback" <?php if(in_array("cashback",$selected_funs2)){echo 'checked';} ?> /><label>我的赠送</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("my_reward",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_reward" <?php if(in_array("my_reward",$selected_funs2)){echo 'checked';} ?> /><label>我的佣金</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("longhuban",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="longhuban" <?php if(in_array("longhuban",$selected_funs2)){echo 'checked';} ?> /><label>店铺龙虎榜</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("headguide",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="headguide" <?php if(in_array("headguide",$selected_funs2)){echo 'checked';} ?> /><label>头部引导</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("f2c",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c" <?php if(in_array("f2c",$selected_funs2)){echo 'checked';} ?> /><label>F2C系统入口</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("ordering_retail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="ordering_retail" <?php if(in_array("ordering_retail",$selected_funs2)){echo 'checked';} ?> /><label>订货系统入口</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("sub_store_entrance",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="sub_store_entrance" <?php if(in_array("sub_store_entrance",$selected_funs2)){echo 'checked';} ?> /><label>子门店入口</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("my_teacher",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_teacher" <?php if(in_array("my_teacher",$selected_funs2)){echo 'checked';} ?> /><label>我的导师</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("my_microshop",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_microshop" <?php if(in_array("my_microshop",$selected_funs2)){echo 'checked';} ?> /><label>我的微店</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("privilege_application",$funs)) echo "style='display:none'"; ?> name="links2[]" value="privilege_application" <?php if(in_array("privilege_application",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("privilege_application",$funs)) echo "style='display:none'"; ?>>特权申请</label>
                                        <?php if($show > 0 || !in_array("agent_login",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="agent_login" <?php if(in_array("agent_login",$selected_funs2)){echo 'checked';} ?> /><label>品牌合作商列申请页面</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("promoter_upgrade",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="promoter_upgrade" <?php if(in_array("promoter_upgrade",$selected_funs2)){echo 'checked';} ?> /><label>推广员申请页面</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("area_agent",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="area_agent" <?php if(in_array("area_agent",$selected_funs2)){echo 'checked';} ?> /><label>区域奖励申请页面</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("shareholder",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="shareholder" <?php if(in_array("shareholder",$selected_funs2)){echo 'checked';} ?> /><label>店铺奖励资料填写页</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("my_asset",$funs)) echo "style='display:none'"; ?> name="links2[]" value="my_asset" <?php if(in_array("my_asset",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("my_asset",$funs)) echo "style='display:none'"; ?>>我的资产</label>
                                        <?php if($show > 0 || !in_array("moneybag",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="moneybag" <?php if(in_array("moneybag",$selected_funs2)){echo 'checked';} ?> /><label>零钱</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("consumption_details",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="consumption_details" <?php if(in_array("consumption_details",$selected_funs2)){echo 'checked';} ?> /><label>消费明细</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("card_integral",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="card_integral" <?php if(in_array("card_integral",$selected_funs2)){echo 'checked';} ?> /><label>会员卡积分</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("shopping_currency_record",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="shopping_currency_record" <?php if(in_array("shopping_currency_record",$selected_funs2)){echo 'checked';} ?> /><label><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>记录</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("red_integral_record",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="red_integral_record" <?php if(in_array("red_integral_record",$selected_funs2)){echo 'checked';} ?> /><label>红积分记录</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("shop_integral_record",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="shop_integral_record" <?php if(in_array("shop_integral_record",$selected_funs2)){echo 'checked';} ?> /><label>商城积分记录</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("my_shop_reward",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_shop_reward" <?php if(in_array("my_shop_reward",$selected_funs2)){echo 'checked';} ?> /><label>店铺奖励报表</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("coupon",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="coupon" <?php if(in_array("coupon",$selected_funs2)){echo 'checked';} ?> /><label>优惠券</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("red_packet",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="red_packet" <?php if(in_array("red_packet",$selected_funs2)){echo 'checked';} ?> /><label>红包</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("order_list",$funs)) echo "style='display:none'"; ?> name="links2[]" value="order_list" <?php if(in_array("order_list",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("order_list",$funs)) echo "style='display:none'"; ?>>订单列表</label>
                                        <?php if($show > 0 || !in_array("package_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="package_list" <?php if(in_array("package_list",$selected_funs2)){echo 'checked';} ?> /><label>礼包订单列表</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("goods_order_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="goods_order_list" <?php if(in_array("goods_order_list",$selected_funs2)){echo 'checked';} ?> /><label>商品订单列表</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("marketing_tools",$funs)) echo "style='display:none'"; ?> name="links2[]" value="marketing_tools" <?php if(in_array("marketing_tools",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("marketing_tools",$funs)) echo "style='display:none'"; ?>>营销工具</label>
                                        <?php if($show > 0 || !in_array("renew_area",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="renew_area" <?php if(in_array("renew_area",$selected_funs2)){echo 'checked';} ?> /><label>续费专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("time_limit_area",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="time_limit_area" <?php if(in_array("time_limit_area",$selected_funs2)){echo 'checked';} ?> /><label>限时专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("gift_area",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="gift_area" <?php if(in_array("gift_area",$selected_funs2)){echo 'checked';} ?> /><label>礼品专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("package_area",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="package_area" <?php if(in_array("package_area",$selected_funs2)){echo 'checked';} ?> /><label>礼包专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("privilege_area",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="privilege_area" <?php if(in_array("privilege_area",$selected_funs2)){echo 'checked';} ?> /><label>特权专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("vp_area",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="vp_area" <?php if(in_array("vp_area",$selected_funs2)){echo 'checked';} ?> /><label>VP活动专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("hot",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="hot" <?php if(in_array("hot",$selected_funs2)){echo 'checked';} ?> /><label>热卖专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("new",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="new" <?php if(in_array("new",$selected_funs2)){echo 'checked';} ?> /><label>新品专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("plane_ticket_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="plane_ticket_list" <?php if(in_array("plane_ticket_list",$selected_funs2)){echo 'checked';} ?> /><label>票务特价机票列表</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("train_ticket_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="train_ticket_list" <?php if(in_array("train_ticket_list",$selected_funs2)){echo 'checked';} ?> /><label>票务特价火车票列表</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("activity_area",$funs)) echo "style='display:none'"; ?> name="links2[]" value="activity_area" <?php if(in_array("activity_area",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("activity_area",$funs)) echo "style='display:none'"; ?>>活动专区</label>
                                        <?php if($show > 0 || !in_array("list_score",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="list_score" <?php if(in_array("list_score",$selected_funs2)){echo 'checked';} ?> /><label>积分专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("integral_shop",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="integral_shop" <?php if(in_array("integral_shop",$selected_funs2)){echo 'checked';} ?> /><label>积分商城专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("integral_sign",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="integral_sign" <?php if(in_array("integral_sign",$selected_funs2)){echo 'checked';} ?> /><label>积分签到</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("exchange_area",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="exchange_area" <?php if(in_array("exchange_area",$selected_funs2)){echo 'checked';} ?> /><label>兑换专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("group_bigpic_area",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="group_bigpic_area" <?php if(in_array("group_bigpic_area",$selected_funs2)){echo 'checked';} ?> /><label>拼团大图专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("group_list_area",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="group_list_area" <?php if(in_array("group_list_area",$selected_funs2)){echo 'checked';} ?> /><label>拼团列表专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("group_tile_area",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="group_tile_area" <?php if(in_array("group_tile_area",$selected_funs2)){echo 'checked';} ?> /><label>拼团平铺专区</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("popularity_group",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="popularity_group" <?php if(in_array("popularity_group",$selected_funs2)){echo 'checked';} ?> /><label>人气拼团</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("my_group_record",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_group_record" <?php if(in_array("my_group_record",$selected_funs2)){echo 'checked';} ?> /><label>我的拼团记录</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("group_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="group_detail" <?php if(in_array("group_detail",$selected_funs2)){echo 'checked';} ?> /><label>拼团详情</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("group_goods_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="group_goods_detail" <?php if(in_array("group_goods_detail",$selected_funs2)){echo 'checked';} ?> /><label>拼团产品详情</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("crowd_funding_index",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="crowd_funding_index" <?php if(in_array("crowd_funding_index",$selected_funs2)){echo 'checked';} ?> /><label>众筹首页</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("crowd_funding_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="crowd_funding_list" <?php if(in_array("crowd_funding_list",$selected_funs2)){echo 'checked';} ?> /><label>众筹列表</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("crowd_funding_record",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="crowd_funding_record" <?php if(in_array("crowd_funding_record",$selected_funs2)){echo 'checked';} ?> /><label>众筹记录</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("crowd_funding_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="crowd_funding_detail" <?php if(in_array("crowd_funding_detail",$selected_funs2)){echo 'checked';} ?> /><label>众筹详情</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("crowd_funding_goods_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="crowd_funding_goods_detail" <?php if(in_array("crowd_funding_goods_detail",$selected_funs2)){echo 'checked';} ?> /><label>众筹产品详情</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("bargain_index",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="bargain_index" <?php if(in_array("bargain_index",$selected_funs2)){echo 'checked';} ?> /><label>砍价首页</label>
                                        </dd>
                                        <?php } ?>
<!--                                        <dd>-->
<!--                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="bargain_list" --><?php //if(in_array("bargain_list",$selected_funs2)){echo 'checked';} ?><!-- /><label>砍价列表</label>-->
<!--                                        </dd>-->
<!--                                        <dd>-->
<!--                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="bargain_record" --><?php //if(in_array("bargain_record",$selected_funs2)){echo 'checked';} ?><!-- /><label>砍价记录</label>-->
<!--                                        </dd>-->
                                        <?php if($show > 0 || !in_array("my_launch_bargain",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="my_launch_bargain" <?php if(in_array("my_launch_bargain",$selected_funs2)){echo 'checked';} ?> /><label>我发起的砍价活动</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("invite_me_bargain",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="invite_me_bargain" <?php if(in_array("invite_me_bargain",$selected_funs2)){echo 'checked';} ?> /><label>邀请我的砍价活动</label>
                                        </dd>
                                        <?php } ?>
<!--                                        <dd>-->
<!--                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="bargain_detail" --><?php //if(in_array("bargain_detail",$selected_funs2)){echo 'checked';} ?><!-- /><label>砍价详情</label>-->
<!--                                        </dd>-->
                                        <?php if($show > 0 || !in_array("bargain_goods_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="bargain_goods_detail" <?php if(in_array("bargain_goods_detail",$selected_funs2)){echo 'checked';} ?> /><label>砍价产品详情</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                </ul>
                            </div>

                            <div style="display:none"><!-- 订货系统 -->
                                <ul data-id="a2" class="WSY_competenceul whith1 fenxiao dinghuo" style="border:none;float:left;">
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("retail_main_funs",$funs)) echo "style='display:none'"; ?> name="links2[]" value="retail_main_funs" <?php if(in_array("retail_main_funs",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("retail_main_funs",$funs)) echo "style='display:none'"; ?>>主要功能</label>
                                        <?php if($show > 0 || !in_array("ordering_retail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="ordering_retail" <?php if(in_array("ordering_retail",$selected_funs2)){echo 'checked';} ?> /><label>订货系统中心</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_purchase_product_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_purchase_product_list" <?php if(in_array("proxy_purchase_product_list",$selected_funs2)){echo 'checked';} ?> /><label>我要进货</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_order_retail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_order_retail" <?php if(in_array("proxy_order_retail",$selected_funs2)){echo 'checked';} ?> /><label>我要零售</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_account_manager",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_account_manager" <?php if(in_array("proxy_account_manager",$selected_funs2)){echo 'checked';} ?> /><label>账号管理</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_store_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_store_list" <?php if(in_array("proxy_store_list",$selected_funs2)){echo 'checked';} ?> /><label>门店列表</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_near_store",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_near_store" <?php if(in_array("proxy_near_store",$selected_funs2)){echo 'checked';} ?> /><label>附近门店</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_store_center",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_store_center" <?php if(in_array("proxy_store_center",$selected_funs2)){echo 'checked';} ?> /><label>子门店中心</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_request_store",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_request_store" <?php if(in_array("proxy_request_store",$selected_funs2)){echo 'checked';} ?> /><label>申请门店</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_request_order",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_request_order" <?php if(in_array("proxy_request_order",$selected_funs2)){echo 'checked';} ?> /><label>申请订货商</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_request_warehouse",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_request_warehouse" <?php if(in_array("proxy_request_warehouse",$selected_funs2)){echo 'checked';} ?> /><label>申请仓库</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_request_son_store",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_request_son_store" <?php if(in_array("proxy_request_son_store",$selected_funs2)){echo 'checked';} ?> /><label>申请子门店</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_order_login",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_order_login" <?php if(in_array("proxy_order_login",$selected_funs2)){echo 'checked';} ?> /><label>订货商登录</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_son_store_login",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_son_store_login" <?php if(in_array("proxy_son_store_login",$selected_funs2)){echo 'checked';} ?> /><label>子门店登录</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("proxy_order_list",$funs)) echo "style='display:none'"; ?> name="links2[]" value="proxy_order_list" <?php if(in_array("proxy_order_list",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("proxy_order_list",$funs)) echo "style='display:none'"; ?>>订单列表</label>
                                        <?php if($show > 0 || !in_array("proxy_purchase_order_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_purchase_order_list" <?php if(in_array("proxy_purchase_order_list",$selected_funs2)){echo 'checked';} ?> /><label>进货订单</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_sale_order_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_sale_order_list" <?php if(in_array("proxy_sale_order_list",$selected_funs2)){echo 'checked';} ?> /><label>销货订单</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_retail_order_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_retail_order_list" <?php if(in_array("proxy_retail_order_list",$selected_funs2)){echo 'checked';} ?> /><label>零售订单</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_send_order_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_send_order_list" <?php if(in_array("proxy_send_order_list",$selected_funs2)){echo 'checked';} ?> /><label>代发订单</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_son_order_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_son_order_list" <?php if(in_array("proxy_son_order_list",$selected_funs2)){echo 'checked';} ?> /><label>子门店订单</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_my_deal_order",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_my_deal_order" <?php if(in_array("proxy_my_deal_order",$selected_funs2)){echo 'checked';} ?> /><label>我的调拨单</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_store_deal_order",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_store_deal_order" <?php if(in_array("proxy_store_deal_order",$selected_funs2)){echo 'checked';} ?> /><label>门店调拨单</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_son_deal_order",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_son_deal_order" <?php if(in_array("proxy_son_deal_order",$selected_funs2)){echo 'checked';} ?> /><label>子门店调拨单</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("proxy_stock_count",$funs)) echo "style='display:none'"; ?> name="links2[]" value="proxy_stock_count" <?php if(in_array("proxy_stock_count",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("proxy_stock_count",$funs)) echo "style='display:none'"; ?>>数据统计</label>
                                        <?php if($show > 0 || !in_array("proxy_account_award",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_account_award" <?php if(in_array("proxy_account_award",$selected_funs2)){echo 'checked';} ?> /><label>货款记录</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_my_reward",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_my_reward" <?php if(in_array("proxy_my_reward",$selected_funs2)){echo 'checked';} ?> /><label>我的奖励</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_stock",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_stock" <?php if(in_array("proxy_stock",$selected_funs2)){echo 'checked';} ?> /><label>库存管理</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_stock_count",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_stock_count" <?php if(in_array("proxy_stock_count",$selected_funs2)){echo 'checked';} ?> /><label>数据统计</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_my_team",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_my_team" <?php if(in_array("proxy_my_team",$selected_funs2)){echo 'checked';} ?> /><label>我的团队</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_son_stock",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_son_stock" <?php if(in_array("proxy_son_stock",$selected_funs2)){echo 'checked';} ?> /><label>子门店库存</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_send_reward",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_send_reward" <?php if(in_array("proxy_send_reward",$selected_funs2)){echo 'checked';} ?> /><label>发货奖励</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("proxy_team_list",$funs)) echo "style='display:none'"; ?> name="links2[]" value="proxy_team_list" <?php if(in_array("proxy_team_list",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("proxy_team_list",$funs)) echo "style='display:none'"; ?>>团队列表</label>
                                        <?php if($show > 0 || !in_array("proxy_my_sales",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_my_sales" <?php if(in_array("proxy_my_sales",$selected_funs2)){echo 'checked';} ?> /><label>我的销货商</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_my_orders",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_my_orders" <?php if(in_array("proxy_my_orders",$selected_funs2)){echo 'checked';} ?> /><label>我的订货商</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("proxy_order_detail",$funs)) echo "style='display:none'"; ?> name="links2[]" value="proxy_order_detail" <?php if(in_array("proxy_order_detail",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("proxy_order_detail",$funs)) echo "style='display:none'"; ?>>订单详情</label>
                                        <?php if($show > 0 || !in_array("proxy_purchase_order_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_purchase_order_detail" <?php if(in_array("proxy_purchase_order_detail",$selected_funs2)){echo 'checked';} ?> /><label>进货订单详情</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_sale_order_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_sale_order_detail" <?php if(in_array("proxy_sale_order_detail",$selected_funs2)){echo 'checked';} ?> /><label>销货订单详情</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_retail_order_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_retail_order_detail" <?php if(in_array("proxy_retail_order_detail",$selected_funs2)){echo 'checked';} ?> /><label>零售订单详情</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_send_order_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_send_order_detail" <?php if(in_array("proxy_send_order_detail",$selected_funs2)){echo 'checked';} ?> /><label>代发订单详情</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("proxy_reward_detail",$funs)) echo "style='display:none'"; ?> name="links2[]" value="proxy_reward_detail" <?php if(in_array("proxy_reward_detail",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("proxy_reward_detail",$funs)) echo "style='display:none'"; ?>>奖励详情</label>
                                        <?php if($show > 0 || !in_array("proxy_my_reward_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_my_reward_detail" <?php if(in_array("proxy_my_reward_detail",$selected_funs2)){echo 'checked';} ?> /><label>我的奖励详情</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("proxy_my_team_reward_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="proxy_my_team_reward_detail" <?php if(in_array("proxy_my_team_reward_detail",$selected_funs2)){echo 'checked';} ?> /><label>我的团队业绩详情</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                </ul>
                            </div>

                            <div style="display:none"><!-- F2C系统 -->
                                <ul data-id="a3" class="WSY_competenceul hangye f2c" style="border:none;float:left;">
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("f2c_main_funs",$funs)) echo "style='display:none'"; ?> name="links2[]" value="f2c_main_funs" <?php if(in_array("f2c_main_funs",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("f2c_main_funs",$funs)) echo "style='display:none'"; ?>>主要功能</label>
                                        <?php if($show > 0 || !in_array("f2c",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c" <?php if(in_array("f2c",$selected_funs2)){echo 'checked';} ?> /><label>F2C中心</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("f2c_apply",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_apply" <?php if(in_array("f2c_apply",$selected_funs2)){echo 'checked';} ?> /><label>F2C申请</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("f2c_purchase",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_purchase" <?php if(in_array("f2c_purchase",$selected_funs2)){echo 'checked';} ?> /><label>我要进货</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("f2c_retail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_retail" <?php if(in_array("f2c_retail",$selected_funs2)){echo 'checked';} ?> /><label>我要零售</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("f2c_order_list",$funs)) echo "style='display:none'"; ?> name="links2[]" value="f2c_order_list" <?php if(in_array("f2c_order_list",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("f2c_order_list",$funs)) echo "style='display:none'"; ?>>订单列表</label>
                                        <?php if($show > 0 || !in_array("f2c_purchase_order",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_purchase_order" <?php if(in_array("f2c_purchase_order",$selected_funs2)){echo 'checked';} ?> /><label>进货订单</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("f2c_sale_order",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_sale_order" <?php if(in_array("f2c_sale_order",$selected_funs2)){echo 'checked';} ?> /><label>销货订单</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("f2c_retail_order",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_retail_order" <?php if(in_array("f2c_retail_order",$selected_funs2)){echo 'checked';} ?> /><label>零售订单</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("f2c_agent_order",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_agent_order" <?php if(in_array("f2c_agent_order",$selected_funs2)){echo 'checked';} ?> /><label>代发订单</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("f2c_data_statistics",$funs)) echo "style='display:none'"; ?> name="links2[]" value="f2c_data_statistics" <?php if(in_array("f2c_data_statistics",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("f2c_data_statistics",$funs)) echo "style='display:none'"; ?>>数据统计</label>
                                        <?php if($show > 0 || !in_array("f2c_data_statistics",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_data_statistics" <?php if(in_array("f2c_data_statistics",$selected_funs2)){echo 'checked';} ?> /><label>数据统计</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("f2c_my_reward",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_my_reward" <?php if(in_array("f2c_my_reward",$selected_funs2)){echo 'checked';} ?> /><label>我的奖励</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("f2c_order_detail",$funs)) echo "style='display:none'"; ?> name="links2[]" value="f2c_order_detail" <?php if(in_array("f2c_order_detail",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("f2c_order_detail",$funs)) echo "style='display:none'"; ?>>订单详情</label>
                                        <?php if($show > 0 || !in_array("f2c_purchase_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_purchase_detail" <?php if(in_array("f2c_purchase_detail",$selected_funs2)){echo 'checked';} ?> /><label>进货订单详情</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("f2c_sale_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_sale_detail" <?php if(in_array("f2c_sale_detail",$selected_funs2)){echo 'checked';} ?> /><label>销货订单详情</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("f2c_retail_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_retail_detail" <?php if(in_array("f2c_retail_detail",$selected_funs2)){echo 'checked';} ?> /><label>零售订单详情</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("f2c_agent_order_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_agent_order_detail" <?php if(in_array("f2c_agent_order_detail",$selected_funs2)){echo 'checked';} ?> /><label>代发订单详情</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("f2c_reward_detail",$funs)) echo "style='display:none'"; ?> name="links2[]" value="f2c_reward_detail" <?php if(in_array("f2c_reward_detail",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("f2c_reward_detail",$funs)) echo "style='display:none'"; ?>>奖励详情</label>
                                        <?php if($show > 0 || !in_array("f2c_my_reward_detail",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="f2c_my_reward_detail" <?php if(in_array("f2c_my_reward_detail",$selected_funs2)){echo 'checked';} ?> /><label>我的奖励详情</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                </ul>
                            </div>

                            <div style="display:none"><!-- 电商直播 -->
                                <ul data-id="a4" class="WSY_competenceul zhanshi dianshang" style="border:none;float:left;">
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("micro_broadcast",$funs)) echo "style='display:none'"; ?> name="links2[]" value="micro_broadcast" <?php if(in_array("micro_broadcast",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("micro_broadcast",$funs)) echo "style='display:none'"; ?>>电商直播首页</label>
                                    </dl>
                                </ul>
                            </div>

                            <div style="display:none"><!-- 语音直播 -->
                                <ul data-id="a5" class="WSY_competenceul cuoxiao voice" style="border:none;float:left;">
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("voice_broadcast_index",$funs)) echo "style='display:none'"; ?> name="links2[]" value="voice_broadcast_index" <?php if(in_array("voice_broadcast_index",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("voice_broadcast_index",$funs)) echo "style='display:none'"; ?>>语音直播首页</label>
                                    </dl>
                                </ul>
                            </div>

                            <div style="display:none"><!-- 收银o2o -->
                                <ul data-id="a6" class="WSY_competenceul hudong o2o" style="border:none;float:left;">
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("O2O_check_out",$funs)) echo "style='display:none'"; ?> name="links2[]" value="O2O_check_out" <?php if(in_array("O2O_check_out",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("O2O_check_out",$funs)) echo "style='display:none'"; ?>>收银O20</label>
                                        <?php if($show > 0 || !in_array("O2O_cashier",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="O2O_cashier" <?php if(in_array("O2O_cashier",$selected_funs2)){echo 'checked';} ?> /><label>收银员</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("O2O_business",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="O2O_business" <?php if(in_array("O2O_business",$selected_funs2)){echo 'checked';} ?> /><label>商家</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("O2O_admin_login",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="O2O_admin_login" <?php if(in_array("O2O_admin_login",$selected_funs2)){echo 'checked';} ?> /><label>后台登陆</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("O2O_checkout_counter",$funs)) echo "style='display:none'"; ?> name="links2[]" value="O2O_checkout_counter" <?php if(in_array("O2O_checkout_counter",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("O2O_checkout_counter",$funs)) echo "style='display:none'"; ?>>收银台</label>
                                    </dl>
                                </ul>
                            </div>
                            <div style="display:none"><!-- 城市商圈 -->
                                <ul data-id="a7" class="WSY_competenceul shop cityarea" style="border:none;float:left;">
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("cater",$funs)) echo "style='display:none'"; ?> name="links2[]" value="cater" <?php if(in_array("cater",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("cater",$funs)) echo "style='display:none'"; ?>>城市商圈-美食</label>
                                        <?php if($show > 0 || !in_array("cityarea_cate_business_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cityarea_cate_business_list" <?php if(in_array("cityarea_cate_business_list",$selected_funs2)){echo 'checked';} ?> /><label>商家列表</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("cityarea_cate_order_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cityarea_cate_order_list" <?php if(in_array("cityarea_cate_order_list",$selected_funs2)){echo 'checked';} ?> /><label>订单列表</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("ktv",$funs)) echo "style='display:none'"; ?> name="links2[]" value="ktv" <?php if(in_array("ktv",$selected_funs2)){echo 'checked';} ?>  /><label <?php if(in_array("ktv",$funs)) echo "style='display:none'"; ?>>城市商圈-KTV</label>
                                        <?php if($show > 0 || !in_array("cityarea_ktv_business_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cityarea_ktv_business_list" <?php if(in_array("cityarea_ktv_business_list",$selected_funs2)){echo 'checked';} ?> /><label>商家列表</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("cityarea_ktv_order_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cityarea_ktv_order_list" <?php if(in_array("cityarea_ktv_order_list",$selected_funs2)){echo 'checked';} ?> /><label>订单列表</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("hotel",$funs)) echo "style='display:none'"; ?> name="links2[]" value="hotel" <?php if(in_array("hotel",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("hotel",$funs)) echo "style='display:none'"; ?>>城市商圈-酒店</label>
                                        <?php if($show > 0 || !in_array("cityarea_hotel_business_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cityarea_hotel_business_list" <?php if(in_array("cityarea_hotel_business_list",$selected_funs2)){echo 'checked';} ?> /><label>商家列表</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("cityarea_hotel_order_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cityarea_hotel_order_list" <?php if(in_array("cityarea_hotel_order_list",$selected_funs2)){echo 'checked';} ?> /><label>订单列表</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("shop",$funs)) echo "style='display:none'"; ?> name="links2[]" value="shop" <?php if(in_array("shop",$selected_funs2)){echo 'checked';} ?>  /><label <?php if(in_array("shop",$funs)) echo "style='display:none'"; ?>>线下商城</label>
                                        <?php if($show > 0 || !in_array("shop_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="shop_list" <?php if(in_array("shop_list",$selected_funs2)){echo 'checked';} ?> /><label>商家列表</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("cityarea_local_order_list",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cityarea_local_order_list" <?php if(in_array("cityarea_local_order_list",$selected_funs2)){echo 'checked';} ?> /><label>订单列表</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("cityarea_local_share_card",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cityarea_local_share_card" <?php if(in_array("cityarea_local_share_card",$selected_funs2)){echo 'checked';} ?> /><label>分享卡</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("cityarea_finance",$funs)) echo "style='display:none'"; ?> name="links2[]" value="cityarea_finance" <?php if(in_array("cityarea_finance",$selected_funs2)){echo 'checked';} ?>  /><label <?php if(in_array("cityarea_finance",$funs)) echo "style='display:none'"; ?>>金融</label>
                                        <?php if($show > 0 || !in_array("cityarea_finance_loan",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cityarea_finance_loan" <?php if(in_array("cityarea_finance_loan",$selected_funs2)){echo 'checked';} ?> /><label>金融-贷款</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("cityarea_finance_credit_card",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cityarea_finance_credit_card" <?php if(in_array("cityarea_finance_credit_card",$selected_funs2)){echo 'checked';} ?> /><label>金融-信用卡</label>
                                        </dd>
                                        <?php } ?>
                                        <?php if($show > 0 || !in_array("cityarea_finance_insurance",$funs)){ ?>
                                        <dd>
                                            <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links2" name="links2[]" value="cityarea_finance_insurance" <?php if(in_array("cityarea_finance_insurance",$selected_funs2)){echo 'checked';} ?> /><label>金融-保险</label>
                                        </dd>
                                        <?php } ?>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("cityarea_coach",$funs)) echo "style='display:none'"; ?> name="links2[]" value="cityarea_coach" <?php if(in_array("cityarea_coach",$selected_funs2)){echo 'checked';} ?>  /><label <?php if(in_array("cityarea_coach",$funs)) echo "style='display:none'"; ?>>教练服务首页</label>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("cityarea_artist",$funs)) echo "style='display:none'"; ?> name="links2[]" value="cityarea_artist" <?php if(in_array("cityarea_artist",$selected_funs2)){echo 'checked';} ?>  /><label <?php if(in_array("cityarea_artist",$funs)) echo "style='display:none'"; ?>>艺人服务首页</label>
                                    </dl>
                                </ul>
                            </div>
                            <div style="display:none"><!-- 会员系统 -->
                                <ul data-id="a8" class="WSY_competenceul shouyin member" style="border:none;float:left;">
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("card_list",$funs)) echo "style='display:none'"; ?> name="links2[]" value="card_list" <?php if(in_array("card_list",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("card_list",$funs)) echo "style='display:none'"; ?>>会员卡列表</label>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("card_detail",$funs)) echo "style='display:none'"; ?> name="links2[]" value="card_detail" <?php if(in_array("card_detail",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("card_detail",$funs)) echo "style='display:none'"; ?>>会员卡详情</label>
                                    </dl>
                                    <dl class="scdl">
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> class="links" <?php if(in_array("card_receive",$funs)) echo "style='display:none'"; ?> name="links2[]" value="card_receive" <?php if(in_array("card_receive",$selected_funs2)){echo 'checked';} ?> /><label <?php if(in_array("card_receive",$funs)) echo "style='display:none'"; ?>>会员卡领取</label>
                                    </dl>
                                </ul>
                            </div>
                            <div style="display:none" id="searchShow"><!-- 搜索模块 -->
                                <ul class="WSY_competenceul" id="search_s" style="border:none;float:left;">
                                    <!-- <dd>
                                        <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> name="links[]" value="{$vo.id}" <volist name="selected_funs" id="z"><eq name="z.fun_id" value="$vo.id" >checked</eq></volist>  /><label>{$vo.name}</label>
                                    </dd> -->
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- 搜索模块 -->
                    <!-- <div style="display:none" id="searchShow">
                         <ul class="WSY_competenceul" style="border:none;float:left;">
                            <volist name="search_cols" id="vo">
                                <dd>
                                    <input type="checkbox" <?php if($show==1){echo "disabled='disabled'";}?> name="links[]" value="{$vo.id}" <volist name="selected_funs" id="z"><eq name="z.fun_id" value="$vo.id" >checked</eq></volist>  /><label>{$vo.name}</label>
                                </dd>
                            </volist>
                        </ul>
                    </div> -->

                </div>
                <?php if($show != 1){ ?>
                <div class="WSY_text_input"><button class="WSY_button">提交</button><br class="WSY_clearfloat"></div>
                <?php }?>
            </div>
        </form>
        <!--权限管理代码结束-->
    </div>
</div>
<script type="text/javascript" src="/weixin/plat/Public/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/js_V6.0/content.js"></script>
<script>
    //解决同一个页面存在多个,勾选无效问题
    $("body").on("click",'input', function() {
//    $('input').on('click', function() {
        var _this = $(this);
        var check_name = _this.val();//子集
        if(_this.hasClass('links2')){
            var level = 2;
            var p_check_name = _this.parent().parent().find('.links').val();
        }else if(_this.hasClass('links')){
            var level = 1;
            var p_check_name = check_name;
        }
        $("input").each(function(){
            if ($(this).val() == check_name){
                if (_this.prop('checked')){//选中
//                    console.log($(this).parent().parent().find(".links"));
                    if ($(this).hasClass('links')) {
                        $(this).prop("checked", true);
                        $(this).parent().find(".links2").prop("checked",true);//全选选取商城功能商城模块相应选上
                    }

//                    if ($(this).hasClass('links2')) {
//                        $(this).prop("checked", true);
//                        $(this).parent().parent().find('.links').prop("checked", true);
//                    }
//                        console.log($(this).parents('.WSY_competenceul').length);
                    var model = $(this).parents('.WSY_competenceul').eq(0).attr('data-id');
                    console.log(model);
                    $('#'+model).attr("checked","true");

//                    if(level == 2){
//                        $('input').each(function(){
//                            if($(this).val() == p_check_name){
//                                if ($(this).hasClass('links')) {
//                                    $(this).prop("checked", true);
//                                    $(this).parent().find(".links").prop("checked",true);
//                                }
//
//                                if ($(this).hasClass('links2')) {
//                                    $(this).prop("checked", true);
//                                    $(this).parent().parent().find('.links').prop("checked", true);
//                                }
//                            }
//                        })
//                    }
//
//                    if(level == 1){
//                        $(this).parent().find(".links2").each(function(){//遍历子集
//                            var name = $(this).val();
//                            var _that = $(this);
//                            $('input').each(function(){
//                                if($(this).val() == name ){
//                                    if ($(this).hasClass('links')) {
//                                        $(this).prop("checked", true);
//                                        $(this).parent().find(".links2").prop("checked",true);
//                                        var model2 = $(this).parents('.WSY_competenceul').eq(0).attr('data-id');
//                                        $('#'+model2).attr("checked","true");
//                                    }
//
//                                    if ($(this).hasClass('links2')) {
//                                        $(this).prop("checked", true);
//                                        $(this).parent().parent().find('.links').prop("checked", true);
//                                        var model3 = $(this).parents('.WSY_competenceul').eq(0).attr('data-id');
//                                        $('#'+model3).attr("checked","true");
//                                    }
//                                }
//                            });
//                        })
//                    }
                }else{//取消
                    if ($(this).hasClass('links')) {
                        $(this).prop("checked", false);
                        $(this).parent().find(".links2").prop("checked",false);
                    }

//                    if ($(this).hasClass('links2')) {
//                        $(this).prop("checked", false);
//                        //判断下面是否有已选中的
//                        if( $(this).parent().parent().find('.links2').filter(':checked').length == 0){
//                            $(this).parent().parent().find('.links').prop("checked", false);
//                        }
//                    }

                    if( $(this).parents('.WSY_competenceul').find('input').filter(':checked').length == 0){
                        var model = $(this).parents('.WSY_competenceul').eq(0).attr('data-id');
                        $('#'+model).removeAttr("checked");
                    }

//                    if(level == 2){
//                        $('input').each(function(){
//                            if($(this).val() == p_check_name){
//                                if ($(this).hasClass('links')) {
//                                    if( $(this).parent().find('.links2').filter(':checked').length == 0) {
//                                        $(this).prop("checked", false);
//                                        $(this).parent().find(".links2").prop("checked", false);
//                                        if( $(this).parents('.WSY_competenceul').find('input').filter(':checked').length == 0){
//                                            var model2 = $(this).parents('.WSY_competenceul').eq(0).attr('data-id');
//                                            $('#'+model2).removeAttr("checked");
//                                        }
//                                    }
//                                }
//                                if ($(this).hasClass('links2')) {
//                                    if(_this.parent().parent().find('.links').filter(':checked').length == 0){
//                                        $(this).prop("checked", false);
//                                    }
//                                    if( $(this).parent().parent().find('.links2').filter(':checked').length == 0){
//                                        $(this).parent().parent().find('.links').prop("checked", false);
//                                    }
//                                    if( $(this).parents('.WSY_competenceul').find('input').filter(':checked').length == 0){
//                                        var model2 = $(this).parents('.WSY_competenceul').eq(0).attr('data-id');
//                                        $('#'+model2).removeAttr("checked");
//                                    }
//                                }
//                            }
//                        })
//                    }
//                    if(level == 1){
//                        $(this).parent().find(".links2").each(function(){//遍历子集
//                            var name = $(this).val();
//                            $('input').each(function(){
//                                if($(this).val() == name ){
//                                    if ($(this).hasClass('links')) {
//                                        $(this).prop("checked", false);
//                                        $(this).parent().find(".links2").prop("checked",false);
//                                        if( $(this).parents('.WSY_competenceul').find('input').filter(':checked').length == 0){
//                                            var model2 = $(this).parents('.WSY_competenceul').eq(0).attr('data-id');
//                                            $('#'+model2).removeAttr("checked");
//                                        }
//                                    }
//
//                                    if ($(this).hasClass('links2')) {
//                                        $(this).prop("checked", false);
//                                        if( $(this).parent().parent().find('.links2').filter(':checked').length == 0){
//                                            $(this).parent().parent().find('.links').prop("checked", false);
//                                        }
//                                        if( $(this).parents('.WSY_competenceul').find('input').filter(':checked').length == 0){
//                                            var model2 = $(this).parents('.WSY_competenceul').eq(0).attr('data-id');
//                                            $('#'+model2).removeAttr("checked");
//                                        }
//                                    }
//                                }
//                            });
//                        })
//                    }
                }
            }
        });
    });

    //单个项下面全选，自动勾选全选中相应权限
    $(function(){
        $('.links').click(function(){
            var parentdl = $(this).parent().parent();
            var clinks  = parentdl.find(".links").filter(":checked");
            //商城模块、订货系统 单个项下面全选，非商城模块、订货系统 自动勾选全选中相应权限
            if ($('#nav_list').children('.white1').text() == '商城系统' || $('#nav_list').children('.white1').text() == '订货系统' || $('#nav_list').children('.white1').text() == 'F2C系统' || $('#nav_list').children('.white1').text() == '订货系统' || $('#nav_list').children('.white1').text() == '电商直播' || $('#nav_list').children('.white1').text() == '语音直播' || $('#nav_list').children('.white1').text() == '收银O2O' || $('#nav_list').children('.white1').text() == '城市商圈' || $('#nav_list').children('.white1').text() == '会员系统' ){
                $(this).parent().find(".links2").prop("checked",$(this).prop("checked"));
            }
        });
        var is_show=<?php if($show) echo $show; else echo '0'; ?>;
        $("#nav_list>div:not(#searchShow) .scdl").each(function(){
            //已被其他模板关联的二级菜单，默认隐藏，若有三级菜单可选，则二级菜单显示
            if($(this).find("dd").length>0){
                $(this).children("input").show();
                $(this).children("label").show();
            }
            //没有三级菜单可选，整个scdl删除
            if($(this).find("dd").length==0&&$(this).find(".links").css('display')=='none'&&!is_show){
                $(this).remove();
            }
        })
        //当没有二级菜单可以选，一级菜单删除
        $("#nav_list>div:not(#searchShow)").each(function(){
            if($(this).find(".scdl").length==0){
                $("#nav_list>a").eq($(this).index("#nav_list>div:not(#searchShow)")).remove();
                $(this).remove();
            }
        })
//        $('.links2').click(function(){
//            //console.log($(this));
//            var parentdl = $(this).parent().parent();
//            var clinks  = parentdl.find(".links2").filter(":checked");
//            if ($('#nav_list').children('.white1').text() == '商城系统' || $('#nav_list').children('.white1').text() == '订货系统' || $('#nav_list').children('.white1').text() == 'F2C系统' || $('#nav_list').children('.white1').text() == '订货系统' || $('#nav_list').children('.white1').text() == '电商直播' || $('#nav_list').children('.white1').text() == '语音直播' || $('#nav_list').children('.white1').text() == '收银O2O' || $('#nav_list').children('.white1').text() == '城市商圈' || $('#nav_list').children('.white1').text() == '会员系统'){
//                if(clinks.length > 0){
//                    parentdl.find(".links").prop("checked",true);
//                }else{
//                    parentdl.find(".links").prop("checked",false);
//                }
//            }
//
//        });
    });

    // ---------全选效果
    function checkAll() {
        var code_Values2 = document.all['links2[]'];
        if (code_Values2.length) {
            for (var i = 0; i < code_Values2.length; i++) {
                code_Values2[i].checked = true;
            }
        } else {
            code_Values2.checked = true;
        }

    }
    function uncheckAll() {
        var code_Values2 = document.all['links2[]'];
        if (code_Values2.length) {
            for (var i = 0; i < code_Values2.length; i++) {
                code_Values2[i].checked = false;
            }
        } else {
            code_Values2.checked = false;
        }
    }

    //是否全选被选中
    function check_s(){
        if($('.shop_system').find('input[name="links2[]"]').not("input:checked").length == 0){
            $('#a1').attr("checked","true");
            var a1 = 1;
        }
        if($('.dinghuo').find('input[name="links2[]"]').not("input:checked").length == 0){
            $('#a2').attr("checked","true");
            var a2 = 1;
        }
        if($('.f2c').find('input[name="links2[]"]').not("input:checked").length == 0){
            $('#a3').attr("checked","true");
            var a3 = 1;
        }
        if($('.dianshang').find('input[name="links2[]"]').not("input:checked").length == 0){
            $('#a4').attr("checked","true");
            var a4 = 1;
        }
        if($('.voice').find('input[name="links2[]"]').not("input:checked").length == 0){
            $('#a5').attr("checked","true");
            var a5 = 1;
        }
        if($('.o2o').find('input[name="links2[]"]').not("input:checked").length == 0){
            $('#a6').attr("checked","true");
            var a6 = 1;
        }
        if($('.cityarea').find('input[name="links2[]"]').not("input:checked").length == 0){
            $('#a7').attr("checked","true");
            var a7 = 1;
        }
        if($('.member').find('input[name="links2[]"]').not("input:checked").length == 0){
            $('#a8').attr("checked","true");
            var a8 = 1;
        }
    }

    check_s();

    // ---------全选效果
    function checkmodel(ob) {
        var model = $(ob).val();
        switch (model){
            case 'all_shop':
                $('.shop_system').find('input[name="links2[]"]').attr("checked","true");
                break;
            case 'all_dinghuo':
                $('.dinghuo').find('input[name="links2[]"]').attr("checked","true");
                break;
            case 'all_f2c':
                $('.f2c').find('input[name="links2[]"]').attr("checked","true");
                break;
            case 'all_dianshang':
                $('.dianshang').find('input[name="links2[]"]').attr("checked","true");
                break;
            case 'all_voice':
                $('.voice').find('input[name="links2[]"]').attr("checked","true");
                break;
            case 'all_o2o':
                $('.o2o').find('input[name="links2[]"]').attr("checked","true");
                break;
            case 'all_cityarea':
                $('.cityarea').find('input[name="links2[]"]').attr("checked","true");
                break;
            case 'all_member':
                $('.member').find('input[name="links2[]"]').attr("checked","true");
                break;
        }
    }
    function uncheckmodel(ob) {
        var model = $(ob).val();
        switch (model){
            case 'all_shop':
                $('.shop_system').find('input[name="links2[]"]').removeAttr("checked");
                break;
            case 'all_dinghuo':
                $('.dinghuo').find('input[name="links2[]"]').removeAttr("checked");
                break;
            case 'all_f2c':
                $('.f2c').find('input[name="links2[]"]').removeAttr("checked");
                break;
            case 'all_dianshang':
                $('.dianshang').find('input[name="links2[]"]').removeAttr("checked");
                break;
            case 'all_voice':
                $('.voice').find('input[name="links2[]"]').removeAttr("checked");
                break;
            case 'all_o2o':
                $('.o2o').find('input[name="links2[]"]').removeAttr("checked");
                break;
            case 'all_cityarea':
                $('.cityarea').find('input[name="links2[]"]').removeAttr("checked");
                break;
            case 'all_member':
                $('.member').find('input[name="links2[]"]').removeAttr("checked");
                break;
        }
    }

    //展示各类模块

    window.onload=function(){
        var oDiv=document.getElementById('nav_list');
        var aBtn=oDiv.getElementsByTagName('a');
        var aDiv=oDiv.getElementsByTagName('div');
        var searchShow = document.getElementById('searchShow');
        for(var i=0;i<aBtn.length;i++){			//遍历div1中的按钮
            aBtn[i].index=i;			//给aBth[]添加自定义属性
            aBtn[i].onclick=function (){
                searchShow.style.display='none';
                for(var i=0;i<aBtn.length;i++){	//遍历按钮，将class清除
                    aBtn[i].className='';
                    aDiv[i].style.display='none';
                }
                this.className='white1';
                aDiv[this.index].style.display='block';
            }
        }
        aBtn[0].click();
    }

    //    function like(){   //搜索功能列表
    //
    //        var all = document.getElementById("all");
    //        var searchShow = document.getElementById("searchShow");
    //        var search_s = document.getElementById("search_s");
    //        var search = $('#searchVal').val();
    //        if(search == '') {
    //            alert('搜索信息不能为空！');
    //            return false;
    //        }
    //        var oDiv=document.getElementById('nav_list');
    //        var aBtn=oDiv.getElementsByTagName('a');
    //        var aDiv=oDiv.getElementsByTagName('div');
    ////        var all = document.getElementById('all');
    //        var search_a = document.getElementById('search_a');
    //        for(var i=0;i<aDiv.length;i++){
    //            aDiv[i].style.display='none';
    //        }
    //        $('#nav_list').find('a').removeClass('white1');
    ////        all.style.display='none';
    //        searchShow.style.display='block';
    //
    //        search_a.className='white1';
    //
    //        var i=0;
    //        var arr_val = [];
    //        var arr_text = [];
    //        var p_arr_val = [];
    //        var level = [];
    //        var p_arr_text = [];
    //        var html1 = [];
    //        var plate_id = [];
    //        var plate_name = [];
    //        $("#nav_list").find("label").each(function(){
    //            if ($(this).text().indexOf(search)>=0){
    //                // console.log($(this).text());
    //                arr_val[i] = $(this).siblings("input").val();
    //                if ($(this).siblings("input").hasClass('links')) {
    ////                    p_arr_val[i] = arr_val[i];
    ////                    level[i]     = 1;
    ////                    p_arr_text[i] = $(this).text();
    //                    html1[i] = $(this).parent().html();
    //
    //                }
    //                if ($(this).siblings("input").hasClass('links2')) {
    ////                    p_arr_val[i] = $(this).siblings("input").parent().parent().find('.links').val();
    ////                    level[i]     = 2;
    ////                    p_arr_text[i] = $(this).siblings("input").parent().parent().find('.links').siblings("label").text();
    //                    html1[i] = $(this).parent().parent().html();
    //                }
    //                plate_id[i] = $(this).parents('.WSY_competenceul').data('id');
    //                plate_name[i] = $('#nav_list').find('a').eq(plate_id[i]).text();
    //                arr_text[i] = $(this).text();
    //                i++;
    //            }
    //        });
    //
    //        var html = "";
    //        for (j=0;j<i;j++){
    //            html+='<dl class="scdl"><div style="margin-bottom: 15px;font-size: 14px;" >'+plate_name[j]+'</div>'+html1[j]+'</dl>';
    //        }
    //        if (arr_val == ""){
    //            alert('没有该信息！');
    //        }else{
    //            $('#search_s').html(html);
    //        }
    //
    //        return false;
    //    }

    function like(){   //搜索功能列表

        var all = document.getElementById("all");
        var searchShow = document.getElementById("searchShow");
        var search_s = document.getElementById("search_s");
        var search = $('#searchVal').val();
        if(search == '') {
            alert('搜索信息不能为空！');
            return false;
        }
        var oDiv=document.getElementById('nav_list');
        var aBtn=oDiv.getElementsByTagName('a');
        var aDiv=oDiv.getElementsByTagName('div');
//        var all = document.getElementById('all');
        var search_a = document.getElementById('search_a');
        for(var i=0;i<aDiv.length;i++){
            aDiv[i].style.display='none';
        }
        $('#nav_list').find('a').removeClass('white1');
//        all.style.display='none';
        searchShow.style.display='block';

        search_a.className='white1';

        var i=0;
        var arr_val = [];
        var arr_text = [];
        var p_arr_val = [];
        var level = [];
        var p_arr_text = [];
        var html1 = [];
        var plate_id = [];
        var plate_name = [];
        $("#nav_list").find("label").each(function(){
            if ($(this).text().indexOf(search)>=0){
                // console.log($(this).text());
                arr_val[i] = $(this).siblings("input").val();
                if ($(this).siblings("input").hasClass('links')) {
//                    p_arr_val[i] = arr_val[i];
//                    level[i]     = 1;
//                    p_arr_text[i] = $(this).text();
                    html1[i] = $(this).siblings("input").prop("outerHTML");;
                    html1[i] = html1[i]+$(this).prop("outerHTML");;
                }
                if ($(this).siblings("input").hasClass('links2')) {
//                    p_arr_val[i] = $(this).siblings("input").parent().parent().find('.links').val();
//                    level[i]     = 2;
//                    p_arr_text[i] = $(this).siblings("input").parent().parent().find('.links').siblings("label").text();
                    html1[i] = $(this).parent().html();
                }
                plate_id[i] = $(this).parents('.WSY_competenceul').data('id');
                plate_name[i] = $('#nav_list').find('a').eq(plate_id[i]).text();
                arr_text[i] = $(this).text();
                i++;
            }
        });

        var html = "";
        for (j=0;j<i;j++){
//            html+='<dl class="scdl"><div style="margin-bottom: 15px;font-size: 14px;" >'+plate_name[j]+'</div>'+html1[j]+'</dl>';
            html+='<dl class="scdl">'+html1[j]+'</dl>';
        }
        if (arr_val == ""){
            alert('没有该信息！');
        }else{
            $('#search_s').html(html);
        }

        return false;
    }
</script>
</body>
</html>
