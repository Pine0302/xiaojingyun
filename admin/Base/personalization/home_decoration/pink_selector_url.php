<?php
//上传分类首页显示图

function pink_selector_url($selector_id,$protocol_http_host,$customer_id,$customer_id_en,$user_id)
{
    require_once(ROOT_DIR."wsy_user/public/weishi_common.php");
    $weishi_common = new weishi_common($customer_id);

    $temp = explode('-', $selector_id);            //数据组成		分类+ID+标题
    $temp_id_num = count($temp) - 2;
    //转换标题中的 - 
    foreach ( $temp as $k => $v )
    {
        $temp[$k] = str_replace('&henggan&', '-', $temp[$k]);
    }
    
    $url = "";
    $shop_url = $protocol_http_host . "/weixinpl/common_shop/jiushop/";
    $mShop_url = $protocol_http_host . "/weixinpl/mshop/";
	$url_type = 0;
    // echo $selector_id;
    //echo $selector_title;
    switch ($temp[1]) {
        case '1':                    //微网系统
            switch ($temp[2]) {
                case '1':                //图文消息
                    $query = "SELECT id,website_url FROM weixin_subscribes where customer_id=" . $customer_id . " and  id=" . $temp[3];//CRM18463 标题有可能含有'-'字符，直接去取temp[3]
                    $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
                    while ($row = mysql_fetch_object($result)) {
                        $website_url = $row->website_url;
                    }
                    $pos = strpos($website_url, "?");
                    $pos2 = strpos($website_url, "single_id");
                    $pos3 = strpos($website_url, "customer_id");
                    if ($pos2 > 0) {
                        $website_url = $website_url . "&C_id=" . $customer_id_en;
                    } else {
                        if ($pos > 0) {
                            if (!$pos3) {
                                $website_url = $website_url . "&customer_id=" . $customer_id_en;
                            }
                        } else {
                            $website_url = $website_url . "?customer_id=" . $customer_id_en;
                        }
                    }
                    $url_type = 1;            //用来判断是否有前缀 1有 0 无
                    $url = $website_url;
                    break;
                case '2':                //微官网单页
                    $cust_sql = "select minisite_url from customers where isvalid = true and id =" . $customer_id;
                    $cust_result = _mysql_query($cust_sql);
                    while ($row = mysql_fetch_object($cust_result)) {
                        $minisite_url = $row->minisite_url;
                    }
                    $minisite_arr = explode('/', $minisite_url);

                    $single_sql = "select type,link,fileName from site_singlepage where C_id =" . $customer_id . " and isvalid=true and indexShow=0 and id=" . $temp[3];//CRM18463 标题有可能含有'-'字符，直接去取temp[3]
                    $single_result = _mysql_query($single_sql);
                    while ($row = mysql_fetch_object($single_result)) {
                        $single_type = $row->type;
                        $single_link = $row->link;
                        $single_fileName = $row->fileName;
                    }
                    if ($single_type == 0) {
                        $url = "/weixin/plat/app/Html/" . $minisite_arr[0] . "/" . $minisite_arr[1] . "/" . $single_fileName . "?single_id=" . $temp[3] . "&C_id=" . $customer_id;//CRM18463 标题有可能含有'-'字符，直接去取temp[3]
                    } else if ($single_type == 1) {
                        $url = $single_link;
                    }
                    break;
                case '3':                //微官网首页
                    $url = "/weixinpl/weixin_inter/menu_index.php?customer_id=" . $customer_id;
                    break;
            }
            break;
        case '2':                    //商城系统
            switch ($temp[2]) {
                case '1':                //默认页面
                    switch ($temp[3]) {
                        case '首页':
                            $url = "/weixinpl/common_shop/jiushop/index.php?customer_id=" . $customer_id_en;
                            break;
                        case '客服':
							$url = "/weixinpl/mshop/kefu_page_url.php?customer_id=" . $customer_id_en;
                            break;
                        case '购物车':
                            $url = "/weixinpl/mshop/order_cart.php?customer_id=" . $customer_id_en;
                            break;
                        case '个人中心':
                            $url = "/weixinpl/mshop/personal_center.php?customer_id=" . $customer_id_en;
                            break;
                        case '足迹':
                            $url = "/weixinpl/mshop/my_visit.php?customer_id=" . $customer_id_en;
                            break;
                        case '收藏':
                            $url = "/weixinpl/mshop/my_collect.php?customer_id=" . $customer_id_en;
                            break;
                    }
                    break;
                case '2':                //合作模式
                    switch ($temp[3]) {
                        // case '品牌合作商申请页面':
                        //     $url = '/weixinpl/mshop/supply_login.php?customer_id=' . $customer_id_en;
                        //     break;
                        // case '区域批发商列表':
                        //     $url = "/weixinpl/mshop/wholesalers_list.php?customer_id=" . $customer_id_en;
                        //     break;
                            case '1'://品牌合作商申请页面
                                $url = '/weixinpl/mshop/supply_login.php?customer_id=' . $customer_id_en;
                            break;
                            case '2'://区域批发商列表
                                // $url = "/weixinpl/mshop/my_store/my_store.php?supplier_id={$temp[4]}&customer_id={$customer_id_en}";
                                $url = "/weixinpl/mshop/wholesalers_list.php?customer_id=" . $customer_id_en;
                            break;
                            case '3':
                                $url = "/weixinpl/mshop/my_store/my_store.php?supplier_id={$temp[4]}&customer_id={$customer_id_en}";
                            break;
                    }
                    break;
                case '3':                //奖励模式
                    switch ($temp[3]) {
                        case '推广员申请页面':
                            $url = '/weixinpl/mshop/promoter_upgrade.php?customer_id=' . $customer_id_en;
                            break;
                        case '区域商申请页面':
                            $url = '/weixinpl/mshop/area_agent.php?customer_id=' . $customer_id_en;
                            break;
                    }
                    break;
                case '4':                //个人中心
                    switch ($temp[3]) {
                        case '我的订单':
                            $url = "/weixinpl/mshop/orderlist.php?customer_id=" . $customer_id_en . "&currtype=1";
                            break;
                        case '会员卡':
                            $url = '/weixinpl/mshop/vip_card_list.php?customer_id=' . $customer_id_en;
                            break;
                        case '我的特权':
                            $url = "/weixinpl/mshop/my_privilege.php?customer_id=" . $customer_id_en;
                            break;
                        case '我的团队':
                            $url = "/weixinpl/mshop/myteam.php?customer_id=" . $customer_id_en;
                            break;
                        case '排队奖励':
                            $url = "/weixinpl/mshop/queue_order_lists.php?customer_id=" . $customer_id_en;
                            break;
                        case '累积收益':
                            $url = "/weixinpl/mshop/my_profit.php?customer_id=" . $customer_id_en;
                            break;
                        case '收货地址':
                            $url = "/weixinpl/mshop/my_address.php?customer_id=" . $customer_id_en . '&a_type=-1';
                            break;
                        case '社区微店':
                            $url = "/o2o/web/city_area/shop/supply_store.php?customer_id=" . $customer_id_en;
                            break;
                        case '我的店铺':
                            $url = "/weixinpl/mshop/my_store/my_store.php?customer_id=" . $customer_id_en;
                            break;
                        case '推广员排行榜':
                            $url = '/weixinpl/mshop/promoter_ranking.php?customer_id=' . $customer_id_en;
                            break;
                        case '授权证书':
                            $url = '/weixinpl/mshop/authorization/authorization.php?customer_id=' . $customer_id_en;
                            break;
                        case '我的慈善':
                            $url = '/weixinpl/common_shop/jiushop/charitable.php?customer_id=' . $customer_id_en;
                            break;
                        case '邀请人':
                            $url = '/weixinpl/common_shop/jiushop/change_relation_user.php?customer_id=' . $customer_id_en . '&user_id=' . passport_encrypt((string)$user_id);
                            break;
                        case '外卖配送':
                            $url = '/weixinpl/common_shop/jiushop/order_list_takeaway_courier.php?user_id=' . passport_encrypt((string)$user_id) . '&customer_id=' . $customer_id_en . '&currtype=1';
                            break;
                        case '我的赠送':
                            $url = '/weixinpl/common_shop/jiushop/cashback.php?user_id=' . passport_encrypt((string)$user_id) . '&customer_id=' . $customer_id_en;
                            break;
                        case '我的佣金':
                            $url = '/weixinpl/common_shop/jiushop/my_reward.php?user_id=' . passport_encrypt((string)$user_id) . '&customer_id=' . $customer_id_en;
                            break;
                        case '店铺龙虎榜':
                            $url = '/weixinpl/mshop/longhuban.php?customer_id=' . $customer_id_en;
                            break;
                        case '头部引导':
                            $url = '/weixinpl/mshop/headguide.php?customer_id=' . $customer_id_en;
                            break;
                        case 'F2C系统入口':
                            $url = "/addons/index.php/f2c/index/index/customer_id/" . $customer_id_en;
                            break;
                        case '订货系统入口':
                            $url = "/addons/index.php/ordering_retail/Proxy/proxy_login?customer_id=" . $customer_id_en;
                            break;
                        case '子门店入口':
                            $url = "/addons/index.php/ordering_retail/Shopbranch/shop_branch_center/customer_id=" . $customer_id_en;
                            break;
                        case '我的导师':
                            $url = "/weixinpl/mshop/my_teacher.php?customer_id=" . $customer_id_en;
                            break;
                        case '我的微店':
                            $url = "/weixinpl/mshop/my_microshop/my_microshop.php?customer_id=" . $customer_id_en;
                            break;
                    }
                    break;
                case '5':                //我的资产
                    switch ($temp[3]) {
                        case '零钱':
                            $url = "/weixinpl/mshop/my_moneybag.php?customer_id=" . $customer_id_en;
                            break;
                        case '消费明细':
                            $url = "/weixinpl/mshop/my_consumer_money.php?customer_id=" . $customer_id_en;
                            break;
                        case '会员卡积分':
                            $sql = "SELECT show_card_id FROM weixin_commonshops WHERE isvalid=true AND customer_id=" . $customer_id . " LIMIT 1";

                            $res = _mysql_query($sql) or die('Query failed34: ' . mysql_error());
                            while ($row = mysql_fetch_object($res)) {
                                $show_card_id = $row->show_card_id;
                            }
                            $url = "/weixinpl/mshop/my_card_score_log.php?customer_id=" . $customer_id_en;
                            break;
                        case '购物币记录':
                            $url = "/weixinpl/mshop/my_currency.php?customer_id=" . $customer_id_en;
                            break;
                        case '红积分记录':
                            $url = "/weixinpl/mshop/my_red_score.php?customer_id=" . $customer_id_en;
                            break;
                        case '商城积分记录':
                            $url = "/weixinpl/mshop/my_integral.php?customer_id=".$customer_id;
                            break;
                        case '店铺奖励报表':
                            $url = "/weixinpl/mshop/my_shop_reward.php?customer_id=" . $customer_id_en;
                            break;
                        case '优惠券':
                            $url = "/weixinpl/mshop/my_coupon.php?customer_id=" . $customer_id_en;
                            break;
                        case '红包':
                            $url = "/weixinpl/mshop/my_hongbao.php?customer_id=" . $customer_id_en;
                            break;
                    }
                    break;
                case '6':                //自定义模板
                    $url = "/weixinpl/common_shop/jiushop/index.php?customer_id=" . $customer_id_en . "&diy_template_id=" . $temp[3];
                    break;
                case '7':                //产品分类
                    $query3 = "select name from weixin_commonshop_types where isvalid=true and id=" . $temp[$temp_id_num];
                    $result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
                    $typename = "";
                    while ($row3 = mysql_fetch_object($result3)) {
                        $typename = $row3->name;
                    }
                    $tcount = 0;    //子分类数量
                    $query_type = "SELECT count(1) as tcount FROM weixin_commonshop_types WHERE customer_id=" . $customer_id . " AND parent_id=" . $temp[$temp_id_num] . " AND is_shelves=1 AND isvalid=true";
                    $result_type = _mysql_query($query_type) or die('Query_type failed:' . mysql_error());
                    while ($row_type = mysql_fetch_object($result_type)) {
                        $tcount = $row_type->tcount;
                    }
                    if ($tcount > 0) {
                        $url = "/weixinpl/mshop/proclass.php?customer_id=" . $customer_id_en . "&tid=" . $temp[$temp_id_num] . "&tname=" . $typename;
                    } else {
                        $url = "/weixinpl/mshop/list.php?customer_id=" . $customer_id_en . "&tid=" . $temp[$temp_id_num] . "&tname=" . $typename;
                    }
                    if($temp[3] == 1 && $temp[4] == '全部分类'){
                        $url = "/weixinpl/mshop/list.php?customer_id=" . $customer_id_en ;
                    }

                    break;
                case '8':                //产品详情页
                    $url = "/weixinpl/mshop/product_detail.php?customer_id=" . $customer_id_en . "&pid=" . $temp[$temp_id_num];
                    break;
                case '9':                //分类页 
                    switch ($temp[3]) {
                        case '二级分类页':
                            $url = "/weixinpl/mshop/class_page.php?customer_id=" . $customer_id_en;
                            break;
                        case '三级分类页——上下结构':
                            $url = '/weixinpl/mshop/class_page4.php?customer_id=' . $customer_id_en;
                            break;
                        case '三级分类页——左右结构':
                            $url = '/weixinpl/mshop/class_page3_left.php?customer_id=' . $customer_id_en;
                            break;
                        case '四级分类页':
                            $url = "/weixinpl/mshop/proclass.php?customer_id=" . $customer_id_en;
                            break;
                        case '快速购买页':
                            $url = '/weixinpl/mshop/class_page3.php?customer_id=' . $customer_id_en;
                            break;
                    }
                    break;
                case '10':                //订单列表
                    switch ($temp[3]) {
                        case '大礼包订单':
                            $url = '/weixinpl/mshop/order_packages_list.php?customer_id=' . $customer_id_en;
                            break;
                        case '商城订单':
                            $url = "/weixinpl/mshop/orderlist.php?customer_id=" . $customer_id_en . "&currtype=1";
                            break;
                    }
                    break;
                case '11':                //营销工具
                    switch ($temp[3]) {
                        case '1':            //续费活动
                            switch ($temp[4]) {
                                case '1':        //续费专区
                                    $url = "/market/web/promoter_renew/index.php?customer_id=" . $customer_id_en;
                                    break;
                                case '2':        //续费产品详情
                                    $url = "/weixinpl/mshop/product_detail.php?customer_id=" . $customer_id_en . "&pid=" . $temp[$temp_id_num];
                                    break;
                            }
                            break;
                        case '2':            //限时活动
                            switch ($temp[4]) {
                                case '1':        //限时专区
                                    $url = '/mshop/web/index.php?m=integral_zone&a=limit_product&customer_id=' . $customer_id_en;
                                    break;
                                case '2':        //限时产品详情
								$url = "/weixinpl/mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$temp[$temp_id_num];
                                    break;
                            }
                            break;
                        case '4':            //礼包活动
                            switch ($temp[4]) {
                                case '1':        //礼包专区
                                    $url = "/weixinpl/mshop/package_list.php?customer_id=" . $customer_id_en;
                                    break;
                                case '2':        //礼包产品详情
                                $url = "/weixinpl/mshop/product_detail_gift.php?package_id=".$temp[$temp_id_num];
                                    break;
                            }
                            break;
                        case '5':            //特权专区
                            switch ($temp[4]) {
                                case '1':        //特权专区
                                    $url = '/weixinpl/mshop/list.php?is_privilege=1&customer_id=' . $customer_id_en;
                                    break;
                                case '2':        //特权产品详情
                                    $url = "/weixinpl/mshop/product_detail.php?customer_id=" . $customer_id_en . "&pid=" . $temp[$temp_id_num];
                                    break;
                            }
                            break;
                        case '6':            //VP活动
                            switch ($temp[4]) {
                                case '1':        //VP专区
                                    $url = '/weixinpl/mshop/list.php?isvp=1&customer_id=' . $customer_id_en;
                                    break;
                                case '2':        //Vp产品详情
                                    $url = "/weixinpl/mshop/product_detail.php?customer_id=" . $customer_id_en . "&pid=" . $temp[$temp_id_num];
                                    break;
                            }
                            break;
                        case '7':            //热卖专区
                            switch ($temp[4]) {
                                case '1':        //热卖专区
                                    $url = "/weixinpl/mshop/list.php?ishot=1&customer_id=" . $customer_id_en;
                                    break;
                                case '2':        //热卖产品详情
                                    $url = "/weixinpl/mshop/product_detail.php?customer_id=" . $customer_id_en . "&pid=" . $temp[$temp_id_num];
                                    break;
                            }
                            break;
                        case '8':            //新品专区
                            switch ($temp[4]) {
                                case '1':        //新品专区
                                    $url = "/weixinpl/mshop/list.php?isnew=1&customer_id=" . $customer_id_en;
                                    break;
                                case '2':        //新品产品详情
                                    $url = "/weixinpl/mshop/product_detail.php?customer_id=" . $customer_id_en . "&pid=" . $temp[$temp_id_num];
                                    break;
                            }
                            break;
                        case '9':            //票务活动
                            switch ($temp[4]) {
                                case '票务特价机票':
                                    $url = "/weixinpl/ticke_check.php?type=flight";
                                    break;
                                case '票务特价火车票':
                                    $url = "/weixinpl/ticke_check.php?type=train";
                                    break;
                            }
                            break;
                        case '10':            //公益基金
                            $url = "/weixinpl/publicwelfare/index.php?customer_id=" . $customer_id;
                            break;
                        case '11':            //趣味测试
                            $url = "/market/admin/MarkPro/Rec_funnytest/index.php?customer_id=" . $customer_id_en;
                            break;
                        case '12':            //软文发布
                            $url = "/market/admin/MarkPro/ruanwen/index.php?customer_id=" . $customer_id_en;
                            break;
						case '13':            //满赠活动
							switch ($temp[4]){
                                case '1':      //优惠券专区
                                    $url = "/mshop/admin/index.php?m=exchange&a=full_give&is_diy_menu=1&customer_id=" . $customer_id_en;
									break;
							}
                            break;
                        case '14':            //优惠券
                            switch ($temp[4]){
                                case '1':      //优惠券专区
                                    $url = "/weixinpl/mshop/coupons_center.php?customer_id={$customer_id_en}";
                                    break;
                                case '2':      //指定优惠券
                                    $m_array = explode('/',$temp[5]);
                                    $cp_id   = $m_array[0];
                                    $url     = "/weixinpl/mshop/coupons_center.php?customer_id={$customer_id_en}&cp_id={$cp_id}";
                                    break;

                            }
                            break;
                        case '15':            //分享有礼
                            switch ($temp[4]){
                                case '分享有礼首页':      //分享有礼首页
                                    $url = "/mshop/web/index.php?m=share_gifts&a=index&customer_id={$customer_id_en}";
                                    break;
                                case '分享有礼排行榜':      //分享有礼排行榜
                                    $url = "/mshop/web/index.php?m=share_gifts&a=ranking&customer_id={$customer_id_en}";
                                    break;

                            }
                            break;
						case '16':			//大转盘
							switch($temp[4]){
								case '大转盘':
									$url = "/mshop/web/index.php?m=slyder_adventures&a=index&customer_id={$customer_id_en}";
									break;
							}
							break;
						case '17':			//限时抢购
							switch($temp[4]){
								case '限时抢购':
									$url = "/weixinpl/mshop/snap_up.php?customer_id={$customer_id_en}";
									break;
							}
							break;
                        case '18':          //队列活动
                            switch($temp[4]){
                                case '1':
                                    $url = "/weixinpl/mshop/list.php?customer_id={$customer_id_en}&isqueue=1";
                                    break;
                            }
                            break;
                        case '19':          //队列活动
                            switch($temp[4]){
                                case '1':   //我的订阅
                                    $url = "/mshop/web/index.php?m=brandsubscribe&a=my_subscribe_list_in&customer_id={$customer_id_en}";
                                    break;
                                case '2':   //品牌订阅列表
                                    $url = "/weixinpl/mshop/list.php?issubscribe=1&customer_id={$customer_id_en}";
                                    break;
                            }
                            break;
                    }
                    break;
                case '12':                //活动专区
                    switch ($temp[3]) {
                        case '1':            //积分活动
                            require_once($_SERVER['DOCUMENT_ROOT'] . '/mshop/web/model/integral.php');
                            require_once($_SERVER['DOCUMENT_ROOT'] . '/mshop/web/model/integral_zone.php');
                            $model_integral = new model_integral();
                            $model_restricted_zone = new model_integral_zone();
                            switch ($temp[4]) {
                                case '1':        //积分专区
                                    $url = '/mshop/web/index.php?m=integral_zone&a=index&customer_id='. $customer_id_en;
                                    break;
                                case '2':        //积分兑换专区
                                    $url = '/weixinpl/mshop/creditsExchange.php?customer_id='. $customer_id_en;
                                    break;
                                case '3':        //积分签到
                                    $url = '/mshop/web/index.php?m=sign&a=index&customer_id=' . $customer_id_en;
                                    break;
                                case '4':        //积分产品详情
                                    $url = "/weixinpl/mshop/product_detail.php?customer_id={$customer_id_en}&pid=" . $temp[$temp_id_num] . "&pro_act_type=21";
                                    break;
								case '5':        //兑换产品详情
                                    $url = "/weixinpl/mshop/product_detail.php?customer_id={$customer_id_en}&pid=" . $temp[$temp_id_num] . "&pro_act_type=22";
                                    break;
                            }
                            break;
                        case '2':            //赠送活动
                            switch ($temp[4]) {
                                case '1':        //兑换专区
                                    $url = "/weixinpl/mshop/list.php?isscore=1&customer_id=" . $customer_id_en;
                                    break;
                                // case '2':        //兑换产品详情
                                //     $url = "/weixinpl/mshop/package_detail.php?customer_id=" . $customer_id_en . "&package_id=" . $temp[$temp_id_num];
                                //     break;
                            }
                            break;
                        case '3':            //拼团活动
                            switch ($temp[4]) {
                                case '1':        //拼团大图专区
									switch($temp[5]){
										case '拼团大图专区':
											 $url = "/market/web/collageActivities/product_list_view.php?op=ordinary2&customer_id=" . $customer_id_en;
											break;
										case '拼团列表专区':
											  $url = "/market/web/collageActivities/product_list_view.php?op=ordinary&customer_id=" . $customer_id_en;
											break;
										case '拼团平铺专区':
											 $url = "/market/web/collageActivities/product_list_view.php?op=ordinary3&customer_id=" . $customer_id_en;
											break;
										case '人气拼团':
											 $url = "/market/web/collageActivities/product_list_view.php?customer_id=" . $customer_id_en . "&op=popularity";
											break;
										case '我的拼团记录':
											  $url = "/market/web/collageActivities/my_collages_record_list_view.php?customer_id=" . $customer_id_en;
											break;

									}
                                    break;
                                case '6':        //拼团详情
                                    $url = "/market/web/collageActivities/activities_detail_view.php?customer_id=" . $customer_id_en . "&group_id=" . $temp[$temp_id_num];
                                    break;
                                case '7':        //拼团产品详情
                                    $url = "/weixinpl/mshop/product_detail.php?pid=" . $temp[$temp_id_num] . "&customer_id=" . $customer_id_en . "&is_collage_from=1";
                                    break;
                            }
                            break;
                        case '4':            //众筹活动
                            switch ($temp[4]) {
                                case '1':        //众筹首页
                                    $url = "/weixinpl/sustain/front/web/index.html?customer_id_en=" . $customer_id_en . "&activity_id=" . $temp[$temp_id_num];
                                    break;
                                case '2':        //众筹列表
                                    $url = "/weixinpl/sustain/back/index.php/Workroom_admin/crowdfund/index_list.html?customer_id_en=" . $customer_id_en;
                                    break;
                                case '3':        //众筹记录
                                    $url = "/weixinpl/sustain/front/web/index.html?customer_id_en={$customer_id_en}&activity_id={$temp[$temp_id_num]}#/Index/MyCrowdfunding";
                                    break;
                                case '4':        //众筹详情
                                    $url = "/weixinpl/sustain/back/index.php/Workroom_admin/crowdfund/index_list.html?customer_id_en=" . $customer_id_en . "&activity_id=" . $temp[$temp_id_num];
                                    break;
                                case '5':        //众筹产品详情
                                    $url = "/weixinpl/sustain/front/web/index.html?customer_id_en=" . $customer_id_en . "&activity_id=" . $temp[count($temp) - 3] . "#/Index/CrowdfundingDetails?id=" . $temp[$temp_id_num];
                                    break;
                            }
                            break;
                        case '5':            //砍价活动
                            switch ($temp[4]) {
                                case '1':        //砍价首页
                                    $url = "/weixinpl/haggling/front/web/index.html?customer_id_en=" . $customer_id_en . "&activity_id=" . $temp[$temp_id_num];
                                    break;
                                case '2':        //排行榜
                                    $url = "/weixinpl/haggling/front/web/index.html?customer_id_en=" . $customer_id_en . "&activity_id=" . $temp[$temp_id_num];
                                    break;
                                case '3':        //我发起的砍价活动
									$url = "/weixinpl/haggling/front/web/index.html?customer_id_en=" . $customer_id_en . "&activity_id=".$temp[$temp_id_num];
                                    break;
								case '4':        //邀请我的砍价活动
									$url = "/weixinpl/haggling/front/web/index.html?customer_id_en=" . $customer_id_en . "&activity_id=".$temp[$temp_id_num];
                                    break;
                                case '5':        //砍价产品详情
                                    $url = "/weixinpl/haggling/front/web/NoEnroll.html?customer_id_en=" . $customer_id_en . "&id=" . $temp[$temp_id_num] . "&activity_id=" . $temp[count($temp) - 3];
                                    break;
                            }
                            break;
                        case '7':
                            switch ($temp[4]) {
                                case '1':     //兑换产品详情
                                    // $url = "/weixinpl/mshop/package_detail.php?customer_id=" . $customer_id_en . "&package_id=" . $temp[$temp_id_num];
                                $url = "/weixinpl/mshop/product_detail_gift.php?package_id=".$temp[$temp_id_num]."&package_parent_id=";
                                    break;
                            }
                            break;
                    }
                    break;
                case '13': //3D素材

                        $url = "/weixinpl/mshop/product_detail_threed.php?customer_id=" . $customer_id_en . "&id=" . $temp[3] ;
                    break;
				case '14':	//慈善公益
					$url = "/weixinpl/common_shop/jiushop/contribution.php?customer_id=".$customer_id_en;
					break;
            }
            break;
        case '3':                    //订货系统
            switch ($temp[2]) {
                case '1':                //主要功能
                    switch ($temp[3]) {
                        case '订货系统中心':
                            $url = "/addons/index.php/ordering_retail/proxy/personal_center?customer_id=" . $customer_id_en;
                            break;
                        case '我要进货':
                            $url = "/addons/index.php/ordering_retail/Purchasing/purchase_product_list?customer_id=" . $customer_id_en;
                            break;
                        case '我要零售':
                            $url = "/addons/index.php/ordering_retail/Sales/order_retail?customer_id=" . $customer_id_en;
                            break;
                        case '账号管理':
                            $url = "/addons/index.php/ordering_retail/Account/account_manager?customer_id=" . $customer_id_en;
                            break;
                        case '退货地址':
                            $url = "/addons/index.php/ordering_retail/Account/return_address?customer_id=" . $customer_id_en;
                            break;
                        case '门店列表':
                            $url = "/addons/index.php/ordering_retail/Shop/shop_list.html?customer_id=" . $customer_id_en . "&type=0";
                            break;
                        case '附近门店':
                            $url = "/addons/index.php/ordering_retail/Shop/nearby_shop.html?customer_id=" . $customer_id_en . "&type=0";
                            break;
                        case '子门店中心':
                            $url = "/addons/index.php/ordering_retail/Shopbranch/shop_branch_center?customer_id=" . $customer_id_en;
                            break;
                        case '申请门店':
                            $url = "/addons/index.php/ordering_retail/Proxy/apply_shop.html?customer_id=" . $customer_id_en . "&type=0";
                            break;
                        case '申请订货商':
                            $url = "/addons/index.php/ordering_retail/Proxy/proxy_apply.html?customer_id=" . $customer_id_en . "&type=0";
                            break;
                        case '申请仓库':
                            $url = "/addons/index.php/ordering_retail/Proxy/apply_store?customer_id=" . $customer_id_en;
                            break;
                        case '申请子门店':
                            $url = "/addons/index.php/ordering_retail/Shopbranch/shop_branch_apply?customer_id=" . $customer_id_en;
                            break;
                        case '订货商登录':
                            $url = "/addons/index.php/ordering_retail/Proxy/proxy_login.html?customer_id=" . $customer_id_en . "&type=0";
                            break;
                        case '子门店登录':
                            $url = "/addons/index.php/ordering_retail/Shopbranch/shop_branch_login?customer_id=" . $customer_id_en;
                            break;
						case '授权查询':
                            $url = "/addons/index.php/ordering_retail/Proxy/select_authorization.html?customer_id=" . $customer_id_en;
                            break;
						case '防伪查询':
                            $url = "/addons/index.php/ordering_retail/Proxy/anti_fake.html?customer_id=" . $customer_id_en;
                            break;
                    }
                    break;
                case '2':                //订单列表
                    switch ($temp[3]) {
                        case '进货订单':
                            $url = "/addons/index.php/ordering_retail/Purchasing/purchase_order_list?customer_id=" . $customer_id_en;
                            break;
                        case '销货订单':
                            $url = "/addons/index.php/ordering_retail/Purchasing/purchase_sale_order_list?customer_id=" . $customer_id_en . "&type=3";
                            break;
                        case '零售订单':
                            $url = "/addons/index.php/ordering_retail/Sales/order_retail_list?customer_id=" . $customer_id_en;
                            break;
                        case '代发订单':
                            $url = "/addons/index.php/ordering_retail/sales/to_send_order.html?customer_id=" . $customer_id_en . "&state=all&is_shop=1";
                            break;
                        case '子门店订单':
                            $url = "/addons/index.php/ordering_retail/Branch/branch_order_list?customer_id=" . $customer_id_en . "&state=all";
                            break;
                        case '订货商调货单':
                            $url = "/addons/index.php/ordering_retail/Branch/branch_transfer_order?customer_id=" . $customer_id . "&type=1&order_type=2";
                            break;
                        case '订货商补货单':
                            $url = "/addons/index.php/ordering_retail/Branch/branch_transfer_order?customer_id=" . $customer_id . "&type=1&order_type=1";
                            break;
                        case '子门店调货单':
                            $url = "/addons/index.php/ordering_retail/Shopbranch/branch_transfer_order?customer_id=" . $customer_id . "&type=1&order_type=2";
                            break;
                        case '子门店补货单':
                            $url = "/addons/index.php/ordering_retail/Shopbranch/branch_transfer_order?customer_id=" . $customer_id . "&type=1&order_type=1";
                            break;
                    }
                    break;
                case '3':                //数据统计
                    switch ($temp[3]) {
                        case '货款记录':
                            $url = "/addons/index.php/ordering_retail/Account/account_award?customer_id=" . $customer_id . "&type=incharge";
                            break;
                        case '我的奖励':
                            $url = "/addons/index.php/ordering_retail/Account/account_award.html?customer_id=" . $customer_id_en;
                            break;
                        case '库存管理':
                            $url = "/addons/index.php/ordering_retail/Stock/stock_list?customer_id=" . $customer_id_en;
                            break;
                        case '数据统计':
                            $url = "/addons/index.php/ordering_retail/Stock/stock_count?customer_id=" . $customer_id;
                            break;
                        case '子门店库存':
                            $url = "/addons/index.php/ordering_retail/Branch/shop_branch_list?customer_id=" . $customer_id_en;
                            break;
                        case '发货奖励':
                            $url = "/addons/index.php/ordering_retail/Branch/branch_account?customer_id=" . $customer_id_en;
                            break;
                    }
                    break;
                case '4':                //团队列表
                    switch ($temp[3]) {
                        case '我的销货商':
                            $url = "/addons/index.php/ordering_retail/Proxy/proxy_my?customer_id=" . $customer_id_en . "&type=is_supplier";
                            break;
                        case '我的订货商':
                            $url = "/addons/index.php/ordering_retail/Proxy/proxy_my?customer_id=" . $customer_id_en;
                            break;
                    }
                    break;
            }
            break;
        case '4':                    //F2C系统
            switch ($temp[2]) {
                case '1':                //主要功能
                    switch ($temp[3]) {
                        case 'F2C中心':
                            $url = "/addons/index.php/f2c/index/personal_center?customer_id=" . $customer_id_en;
                            break;
                        case 'F2C申请':
                            $url = "/addons/index.php/f2c/index/apply?customer_id=" . $customer_id_en;
                            break;
                        case '我要进货':
                            $url = "/addons/index.php/f2c/Goods/product_list?customer_id=" . $customer_id_en;
                            break;
                        case '我要销货':
                            $url = "/addons/index.php/f2c/Sale/order_retail?customer_id=" . $customer_id_en;
                            break;
                    }
                    break;
                case '2':                //订单列表
                    switch ($temp[3]) {
                        case '进货订单':
                            $url = "/addons/index.php/f2c/Orders/purchase_order_list?customer_id=" . $customer_id_en;
                            break;
                        case '销货订单':
                            $url = "/addons/index.php/f2c/Sale/f2c_sale_log?customer_id=" . $customer_id_en;
                            break;
                        case '代发订单':
                            $url = "/addons/index.php/f2c/Orders/order_substitute_list?customer_id=" . $customer_id_en;
                            break;
                    }
                    break;
                case '3':                //数据统计
                    switch ($temp[3]) {
                        case '数据统计':
                            $url = "/addons/index.php/f2c/Statistics/data_statistics?customer_id=" . $customer_id_en;
                            break;
                        case '我的奖励':
                            $url = "/addons/index.php/f2c/reward/my_reward?customer_id=" . $customer_id_en . "&types=my_reward&paytype=0";
                            break;
                    }
                    break;
            }
            break;
        case '5':                    //电商直播
            $url = "/addons/index.php/micro_broadcast/User/index?customer_id=" . $customer_id;
            break;
        case '6':                    //语音直播
            $url = "/addons/index.php/voice_online/Index/index?customer_id=" . $customer_id_en;
            break;
        case '7':                    //收银O2O
            switch ($temp[2]) {
                case '1':                //收银O20
                    switch ($temp[3]) {
                        case '收银员':
                            $url = "/weixinpl/back_nowpaySystem/cashier_login_link.php?customer_id=" . $customer_id . "&type=0";
                            break;
                        case '商家':
                            $url = "/weixinpl/back_nowpaySystem/cashplatform/T_IndustryDiversionA.php?customer_id=" . $customer_id;
                            break;
                        case '后台登录':
                            $url = "/weixinpl/back_nowpaySystem/cashier_login_link.php?customer_id=" . $customer_id . "&type=0";
                            break;
                    }
                    break;
                case $temp[2]:
                    case $temp[3]:
                        switch ($temp[3]){
                            case '0':
                                $url = "/weixinpl/back_nowpaySystem/cashplatform/T_IndustryDiversionB.php?tradeid=".$temp[4];
                                break;
                            case '1':
                                switch ($temp[4]) {
                                    case $temp[4]:
                                        $url = "/weixinpl/back_nowpaySystem/cashplatform/T_BusinessIntroduction.php?customer_id=" . $customer_id . "&n_p_custid=".$temp[4];
                                        break;
                                }
                                break;
                        }
                    
                // case '2':                //收银台
                //     $url = "/weixinpl/back_paySystem/cashier_login_link.php?customer_id=" . $customer_id_en;
                //     break;
            }
            break;
        case '8':                    //城市商圈
            switch ($temp[2]) {
                case '1':                //城市商圈-美食
                    switch ($temp[3]) {
                        case '1':            //商家列表
                            $url = "/o2o/web/city_area/cater/index.php?customer_id=" . $customer_id_en;
                            break;
                        case '2':            //单个店铺
                            $url = "/o2o/web/city_area/cater/shop_detail.php?customer_id=" . $customer_id_en . "&caterer_id=" . $temp[$temp_id_num];
                            break;
                        case '3':            //订餐订单列表
                            $url = "/weixinpl/mshop/cityarea/orderlist_caterer_package.php?customer_id=" . $customer_id_en . "&currtype=1&cityarea_type=2";
                            break;
                        case '4':            //外卖订单列表
                            $url = "/weixinpl/mshop/cityarea/orderlist_caterer_package.php?customer_id=" . $customer_id_en . "&currtype=1&cityarea_type=1";
                            break;
                    }
                    break;
                case '2':                //城市商圈-KTV
                    switch ($temp[3]) {
                        case '1':            //商家列表
                            $url = "/o2o/web/city_area/ktv/index.php?customer_id=" . $customer_id_en;
                            break;
                        case '2':            //单个店铺
                            $url = "/o2o/web/city_area/ktv/shop_detail.php?customer_id=" . $customer_id_en . "&supply_id=" . $temp[$temp_id_num];
                            break;
                        case '3':            //订单列表
                            $url = "/weixinpl/mshop/cityarea/orderlist_ktv_package.php?customer_id=" . $customer_id_en . "&currtype=1";
                            break;
                    }
                    break;
                case '3':                //城市商圈-酒店
                    switch ($temp[3]) {
                        case '1':            //商家列表
                            $url = "/o2o/web/city_area/hotel/index.php?customer_id=" . $customer_id_en;
                            break;
                        case '2':            //单个店铺
                            $url = "/o2o/web/city_area/hotel/shop.php?customer_id=" . $customer_id_en . "&shop_id=" . $temp[$temp_id_num];
                            break;
                        case '3':            //订单列表
                            $url = "/weixinpl/mshop/cityarea/orderlist_hotel_package.php?customer_id=" . $customer_id_en . "&currtype=1";
                            break;
                    }
                    break;
                case '4':                //线下商城首页
                    switch ($temp[3]) {
                        case '1':            //商家列表
                            $url = "/o2o/web/city_area/shop/shop_list.php?customer_id=" . $customer_id_en;
                            break;
                        case '2':            //单个店铺
                            $temp_id_num = 4;
                            $url = "/o2o/web/city_area/shop/supply_store.php?customer_id=" . $customer_id_en . "&supply_id=" . $temp[$temp_id_num];
                            break;
                        case '3':            //订单列表
                            switch($temp[4]){
								case '自提订单列表':
									$url = "/o2o/web/city_area/shop/order_list.php?customer_id=" . $customer_id_en . "&currtype=1";
									break;
								case '配送订单列表':
									$url = "/o2o/web/city_area/shop/order_list_take.php?customer_id=" . $customer_id_en . "&currtype=11";
									break;
								case '社区订单列表':
									$url = "/o2o/web/city_area/shop/order_list_community.php?customer_id=" . $customer_id_en . "&currtype=11";
									break;
							}
                            break;
                        case '4':            //分享卡
                            $url = "/o2o/web/city_area/shop/myorders.php?customer_id=" . $customer_id_en;
                            break;
						case '5':            //首页
                            $url = "/o2o/web/city_area/shop/index.php?customer_id=" . $customer_id_en;
                            break;
                        case '6':            //商家分类
                            $temp_id_num = 4;
                            $url = "/o2o/web/city_area/shop/shop_list.php?customer_id=" . $customer_id_en . "&type=" . $temp[$temp_id_num];
                            break;
                    }
                    break;
                case '5':                //金融
                    switch ($temp[3]) {
                        case '贷款':
                            $url = "/o2o/web/city_area/finance2/loan/loanList.php?customer_id=" . $customer_id_en;
                            break;
                        case '信用卡':
                            $url = "/o2o/web/city_area/finance2/credit/index.php?customer_id=" . $customer_id_en;
                            break;
                        case '保险':
                            $url = "/o2o/web/city_area/finance2/insurance/insurance_list.php?customer_id=" . $customer_id_en;
                            break;
                    }
                    break;
                case '6':                //教练服务首页
                    $url = "/addons/index.php/coach/Index/coach_index?customer_id=" . $customer_id_en;
                    break;
                case '7':                //艺人服务首页
                    switch ($temp[3]) {
                        case '1':            //艺人服务首页
                            $url = "/weixinpl/yiren/front/web/index.html?customer_id_en=" . $customer_id_en;
                            break;
                        case '2':            //艺人入驻
                            $url = "/weixinpl/yiren/front/web/applyText.html?customer_id_en=" . $customer_id_en;
                            break;
                        case $temp[3]:
                            switch($temp[4]){
                                    case $temp[4]:
                                        $url = "/weixinpl/yiren/front/web/classFilter.html?customer_id_en=" . $customer_id_en . "&server_id=" . $temp[$temp_id_num];
                                        break;
                                   
                                }
                                break;

                        
                    }
                    
                    break;
                case '8':                //音王KTY
                    switch ($temp[3]) {
                        case '点歌台':        //点歌台
                            $url = "/o2o/web/index.php?m=soundking&a=index";
                            break;
                        case '个人中心':      //个人中心
                            $url = "/o2o/web/index.php?m=soundking&a=my_account";
                            break;
                        case '我的作品':      //我的作品
                            $url = "/o2o/web/index.php?m=soundking&a=myPro&customer_id=" . $customer_id_en ;
                            break;
                    }
                    break;
            }
            break;
        case '9':                    //会员系统
            switch ($temp[2]) {
                case '1':                //会员卡列表
                    $url = "/weixinpl/mshop/vip_card_list.php?customer_id=" . $customer_id_en;
                    break;
                case '2':                //单个会员卡
                    $url = "/wsy_user/web/card/show_card.php?customer_id=" . $customer_id_en . "&card_id=" . $temp[$temp_id_num];
                    break;
				case '3':                //微信卡券
                    $url = "/wsy_user/web/index.php?m=wechat_card&a=choose_store_list&customer_id=" . $customer_id_en;
                    break;	
            }
            break;
		case '10':                   //新话费流量充值
            switch ($temp[2])  {
				case '1':   //新话费流量充值
				   switch ($temp[3]){
					   case '新话费流量充值':
					       $url = "/special/web/index.php?m=saiheyi_recharge&a=index&customer_id=" . $customer_id_en;
					       break;
				   }
				   break;
                case '2':   //旅游卡
                    switch ($temp[3]) {
                        case '我的旅游卡':
                            $url = "/o2o/web/view/travel/my_travelCard.html?customer_id=" . $customer_id ;
                            break;
                        case '旅游卡办卡页面':
                            $url = "/o2o/web/view/travel/apply_card.html?customer_id=" . $customer_id ;
                            break;
                    }
                    break;
                case '3':   //期权交易
                   switch ($temp[3]){
                       case '期权交易':
                           $url = "/mshop/web/index.php?m=qiquan&a=index&customer_id=".$customer_id_en;
                           break;
                   }
                   break;
			}
            break;
        case '11':                   //微社区
            switch ($temp[2])  {
                case '1':   //推荐页面
                    $url = "/blend/mobile.php/community/Recommend/index?customer_id=".$customer_id_en;
                    break;
                case '2':   //发布
                    $url = "/blend/mobile.php/community/forum/forum_list?customer_id=".$customer_id_en;
                    break;
                case '3':   //帖子详情
                    $url = "/blend/mobile.php/community/post/index?customer_id=".$customer_id_en."&id=".$temp[3];
                    break;
                case '4':   //版块
                    $url = "/blend/mobile.php/community/Search/index?customer_id=".$customer_id_en."&forum_id=".$temp[3];
                    break;               
            }
            break;
         case '12':                   //便民查询
            switch ($temp[2])  {
                case '1':   //快递查询
                    $url = "/blend/mobile.php/community/post/express?customer_id=".$customer_id_en;
                    break;
                case '2':   //汇率查询
                    $url = "/blend/mobile.php/community/post/rateIndex?customer_id=".$customer_id_en;
                    break;            
            }
            break;
         case '13':                   //彩铃订购
            switch ($temp[2])  {
                case '1':   //彩铃订购
                    $url = "/mshop/web/index.php?m=cailing&a=color_bell_list&customer_id=".$customer_id_en;
                    break;              
            }
            break;
        case '14':                   //知识付费
            switch ($temp[2])  {
                case '1':   //微云视首页
                    //判断是否绑定微视
                    $check_ws = $weishi_common->check_ws();
                    if ($check_ws) {
                        $url = $weishi_common->weishi_data["ws_url"];
                    } else {
                        $url = 'baidu.com';
                    }
                    break;
            }
            break;
    }

    $linktype = $selector_id;
    //CRM18463 增加判断。如果是微网系统，而且不是微官网首页的话。标题就从数组第四位开始截取到最后，因为图文信息和微官网单页标题有可能含有'-'字符
    if ($temp[1] == 1 && $temp[2] != 3) {
        $link_title = implode('-',array_slice($temp, 4));
        return array('url'=>$url,'linktype'=>$linktype,'title'=>$link_title,'url_type'=>$url_type);
    } else {
        return array('url'=>$url,'linktype'=>$linktype,'title'=>$temp[count($temp)-1],'url_type'=>$url_type);
    }
}
function g_curl($url)
{
    $ch = curl_init($url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch,CURLOPT_AUTOREFERER,1);
    $output = curl_exec($ch);
    if(curl_errno($ch)){var_dump(curl_error($ch));}
    curl_close($ch);
    return $output;
}
//echo $url;
//die();
//$product_detail_id_2 = $temp[$temp_id_num];
//$foreign_id = $selector_id;
?>