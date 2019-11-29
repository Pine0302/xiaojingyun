<?php

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主订单
    开 发 者：taojin
    开发日期： 2018-04-08
    重要说明：参数type: 0平台订单 1自营订单
     */
    public function yundian_order_list($data){
        $return['errcode']   = 400;
        $return['errmsg']    = "保存失败！";
        
        $where = '';
        if($data['type'] == 1){//获取货币单位
            $set = $this->currency_set($data['customer_id']);
        }
        if($data['batchcode'])       $where .= " and o.batchcode = '{$data['batchcode']}'";
        $where .= " and o.yundian_self = '{$data['type']}'";
        if($data['user_id']){
            //查询云店id
            $sql_yun = "SELECT id from ".WSY_USER.".weixin_yundian_keeper where user_id = '{$data['user_id']}' and isvalid =true and customer_id = '{$data['customer_id']}' ";
            $res_yun = $this->db->getAll($sql_yun);
            $ids = '';
            foreach($res_yun as $k => $v){
                $ids = $v['id'].',';
            }
            $ids1 = trim($ids,',');
            if($ids1){
                $where .= " and o.yundian_id in ({$ids1})";
            }else{
                $return['data']      = array(array(),0);
                $return['errcode']   = 0;
                $return['msg']       = 'success';
                return $return;
            }
        }
        if($data['name']){
            $sql_mem = "select k.id from ".WSY_USER.".weixin_users u inner join ".WSY_USER.".weixin_yundian_keeper k on u.id=k.user_id where k.customer_id = '{$data['customer_id']}' and u.name like '%{$data['name']}%'";
            $res_mem = $this->db->getAll($sql_mem);
            $ids = '';
            if($res_mem){
                foreach($res_mem as $k => $v){
                    $ids .= $v['id'].',';
                }
                $ids2 = trim($ids,',');
                if($ids2){
                    $where .= " and o.yundian_id in ({$ids2})";
                }
            }else{
                $return['data']      = array(array(),0);
                $return['errcode']   = 0;
                $return['msg']       = 'success';
                return $return;
            }
        }

        switch ($data['status']){
            case 0;//全部
                break;
            case 1;//代发货
                $where .= " and o.sendstatus = 0 and (o.paystatus = 1 and o.paystyle !='货到付款') and o.status = 0 and aftersale_type=0 ";
                break;
            case 2;//待收货
                $where .= " and o.sendstatus = 1 and (o.paystatus = 1 or o.paystyle ='货到付款')and o.status = 0 and aftersale_type=0 ";
                break;
            case 3;//待完成
                $where .= " and o.sendstatus = 2 and o.paystatus = 1 and o.status = 0 and aftersale_type=0 ";
                break;
            case 4;//交易完成
                $where .= ' and o.sendstatus = 2 and o.paystatus = 1 and o.status = 1 and aftersale_type=0 ';
                break;
            case 5;//退款
                $where .= " and o.aftersale_type = 1 and o.paystatus = 1 ";
                break;
            case 6;//退货
                $where .= " and o.aftersale_type = 2 and o.paystatus = 1 ";
                break;
            case 7;//换货
                $where .= " and o.aftersale_type = 3 and o.paystatus = 1 ";
                break;
        }

        $page = ($data['pageNum'] - 1)*$data['page_size'] . ','. $data['page_size'];
        $sql = "SELECT o.*,op.origin_price,op.price,op.recovery_time,u.weixin_name,u.name,u.phone,oa.name as a_name,oa.phone as a_phone,oa.location_p,oa.location_c,oa.location_a,oa.address,oa.identity,oa.identityimgt,oa.identityimgf from weixin_commonshop_orders o left join weixin_commonshop_order_prices op on op.batchcode = o.batchcode left join ".WSY_USER.".weixin_users u on u.id = o.user_id left join ".WSY_SHOP.".weixin_commonshop_order_addresses oa on oa.batchcode = o.batchcode   left join ".WSY_USER.".weixin_yundian_keeper yk on yk.id = o.yundian_id  where o.isvalid = true and o.paystatus = 1 {$where} and yundian_id>0 and o.customer_id = '{$data['customer_id']}' and o.is_sendorder <> 1 and o.is_collageActivities <> 2 and o.shopactivity_mark = 0  group by o.batchcode order by o.id desc limit {$page} ";

        $res_list = $this->db->getAll($sql);

        $res_count = $this->get_order_num($data,$data['type']);
        if( $res_list && $res_count > 0 ){
            foreach($res_list as $k => $res) {

                if ($res['sendstatus'] == 0 && ($res['paystatus']==1 and $res['paystyle']!='货到付款') && $res['status'] >= 0 && $res['aftersale_type']==0) {
                    $res_list[$k]['status_str'] = '待发货';
                } elseif (strtotime($res['recovery_time']) < time() && $res['aftersale_type']==0 && $res['paystatus'] == 0) {
                    $res_list[$k]['status_str'] = '已失效';
                }elseif ($res['sendstatus'] == 1 && ($res['paystatus'] == 1 or $res['paystyle']=='货到付款') && $res['status'] >= 0 && $res['aftersale_type']==0) {
                    $res_list[$k]['status_str'] = '待收货';
                } elseif ($res['sendstatus'] == 2 && $res['paystatus'] == 1 && $res['status'] != 1 && $res['aftersale_type']==0) {
                    $res_list[$k]['status_str'] = '待完成';
                } elseif ($res['sendstatus'] == 2 && $res['paystatus'] == 1 && $res['status'] == 1 && $res['aftersale_type']==0) {
                    $res_list[$k]['status_str'] = '交易完成';
                } elseif ($res['aftersale_type'] == 1 && $res['paystatus'] == 1) {
                    $res_list[$k]['status_str'] = '退款';
                    if ($res['aftersale_state'] == 1) $res_list[$k]['status_str'] .= '(申请中)';
                    if ($res['aftersale_state'] == 2) $res_list[$k]['status_str'] .= '(同意退款)';
                    if ($res['aftersale_state'] == 3) $res_list[$k]['status_str'] .= '(驳回申请)';
                    if ($res['sendstatus'] == 6 && $res['aftersale_state'] == 2) $res_list[$k]['status_str'] .= '完成';
                } elseif ($res['aftersale_type'] == 2 && $res['paystatus'] == 1) {
                    $res_list[$k]['status_str'] = '退货';
                    switch ($res['return_type']){
                        case 0;
                            $res_list[$k]['status_str'] = "退货[仅退款]";
                            break;
                        case 2;
                            $res_list[$k]['status_str'] = "换货";
                            break;
                    }
                    if ($res['return_status'] == -1) $res_list[$k]['status_str'] .= '(退货失败)';
                    if ($res['return_status'] == 0) $res_list[$k]['status_str'] .= '(未退货)';
                    if ($res['return_status'] == 1) $res_list[$k]['status_str'] .= '(退货成功)';
                    if ($res['return_status'] == 2) $res_list[$k]['status_str'] .= '(同意退货)';
                    if ($res['return_status'] == 3) $res_list[$k]['status_str'] .= '(驳回请求)';
                    if ($res['return_status'] == 4) $res_list[$k]['status_str'] .= '(确认退货)';
                    if ($res['return_status'] == 5) $res_list[$k]['status_str'] .= '(用户已退货)';
                    if ($res['return_status'] == 6) $res_list[$k]['status_str'] .= '(商家确认收货)';
                    if ($res['return_status'] == 7) $res_list[$k]['status_str'] .= '(商家已发货)';
                    if ($res['return_status'] == 8) $res_list[$k]['status_str'] .= '(同意退款)';
                    if ($res['return_status'] == 9) $res_list[$k]['status_str'] .= '(驳回退款)';
                }elseif ($res['aftersale_type'] == 3 && $res['paystatus'] == 1) {
                    $res_list[$k]['status_str'] = '换货';
                    if ($res['return_status'] == -1) $res_list[$k]['status_str'] .= '(退货失败)';
                    if ($res['return_status'] == 0) $res_list[$k]['status_str'] .= '(未退货)';
                    if ($res['return_status'] == 1) $res_list[$k]['status_str'] .= '(退货成功)';
                    if ($res['return_status'] == 2) $res_list[$k]['status_str'] .= '(同意退货)';
                    if ($res['return_status'] == 3) $res_list[$k]['status_str'] .= '(驳回请求)';
                    if ($res['return_status'] == 4) $res_list[$k]['status_str'] .= '(确认退货)';
                    if ($res['return_status'] == 5) $res_list[$k]['status_str'] .= '(用户已退货)';
                    if ($res['return_status'] == 6) $res_list[$k]['status_str'] .= '(商家确认收货)';
                    if ($res['return_status'] == 7) $res_list[$k]['status_str'] .= '(商家已发货)';
                    if ($res['return_status'] == 8) $res_list[$k]['status_str'] .= '(同意退款)';
                    if ($res['return_status'] == 9) $res_list[$k]['status_str'] .= '(驳回退款)';
                } elseif( $res['status'] == -1){
                    $res_list[$k]['status_str'] = '已取消';
                }
                //获取下单人信息
                if($res['user_id'])        $res_list[$k]['name_str'] = 'ID：'.$res['user_id'].'<br/>';
                if($res['name'])           $res_list[$k]['name_str'] .= "昵称：{$res['name']}";
//                if($res['phone']) $res_list[$k]['name_str'] .= " / {$res['phone']}";

                //获取支付信息
                $pay_res = $this->get_pay_info($res);
                if($pay_res['errcode'] == 0) $res_list[$k]['pay_info'] = $pay_res['data'];
                if($data['type'] == 1){
                    //获取店主信息
                    $sql_M = "select u.weixin_name,u.name,u.phone,k.user_id from ".WSY_USER.".weixin_users u inner join ".WSY_USER.".weixin_yundian_keeper k on u.id=k.user_id where u.customer_id = '{$data['customer_id']}' and k.id = '{$res['yundian_id']}'";
                    $res_M = $this->db->getRow($sql_M);
                    if($res_M['user_id'])       $res_list[$k]['yundian_info'] = 'ID：'.$res_M['user_id'].'<br/>';
                    if($res_M['name'])          $res_list[$k]['yundian_info'] .= "昵称：{$res_M['name']}";
//                    if($res_M['phone'])         $res_list[$k]['yundian_info'] .= " / {$res_M['phone']}";

                    //获取货款金额
                    $sql_payment = "SELECT yundian_reward from weixin_commonshop_order_prices where batchcode = '{$res['batchcode']}' and customer_id = '{$data['customer_id']}'";
                    $res_payment = $this->db->getAll($sql_payment);
                    $payment = 0;
                    if($res_payment){
                        foreach ($res_payment as $v){
                            $payment += $v['yundian_reward'];
                        }
                    }
                    $res_list[$k]['yundian_reward'] = $payment;
                    //订单结算状态与货款状态
                    if($res['sendstatus'] == 2 && $res['paystatus'] == 1 && $res['status'] == 1){
                        $res_list[$k]['balance_str']    = '已完成';
                        $res_list[$k]['payment']        = "已结算：{$set['currency_symbol']}{$payment}";
                    }elseif ($res['aftersale_type'] == 1 && $res['paystatus'] == 1 && $res['sendstatus'] == 6){
                        $res_list[$k]['balance_str']    = '已完成';
                        $res_list[$k]['payment']        = "已结算：{$set['currency_symbol']}{$payment}";
                    }elseif($res['aftersale_type'] == 2 && $res['paystatus'] == 1 && ($res['return_status'] == 8 || $res['return_status'] == 1)){
                        $res_list[$k]['balance_str']    = '已完成';
                        $res_list[$k]['payment']        = "已结算：{$set['currency_symbol']}{$payment}";
                    }elseif($res['aftersale_type'] == 3 && $res['paystatus'] == 1 && ($res['return_status'] == 7 || $res['return_status'] == 1)){
                        $res_list[$k]['balance_str']    = '已完成';
                        $res_list[$k]['payment']        = "已结算：{$set['currency_symbol']}{$payment}";
                    }else{
                        $res_list[$k]['balance_str']    = '未完成';
                        $res_list[$k]['payment']        = "未结算：{$set['currency_symbol']}{$payment}";
                    }

                }
            }

            $return['data']      = array($res_list,$res_count);
            $return['errcode']   = 0;
            $return['msg']       = 'success';
        }
        return $return;
    }
    
    /*
    版权信息:  秘密信息
    功能描述：获取货币单位
    开 发 者：taojin
    开发日期： 2018-04-09
    重要说明：
     */
    public function currency_set($customer_id){
        $sql = "SELECT type,currency_symbol,currency_text,symbol_position FROM weixin_currency_symbol_set WHERE customer_id=".$customer_id;
        $res = $this->db->getRow($sql);
        return $res;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——订单日志
    开 发 者：taojin
    开发日期： 2018-04-09
    重要说明：
     */
    public function yundian_order_log($batchcode,$user_id){
        //获取下单人的信息
        $sql2  = "select name,weixin_name,id,weixin_fromuser,phone FROM weixin_users WHERE id ='{$user_id}'";
        $res2  = $this->db->getAll($sql2);

        //订单日志
        $sql23  = "select operation,descript,operation_user,createtime,batchcode from weixin_commonshop_order_logs where isvalid = true and batchcode='{$batchcode}'";
        $res23  = $this->db->getAll($sql23);
        foreach($res23 as $k=>$v)
        {
            if(!empty($v['operation_user'])) $op_user[$k] = $v['operation_user']; //产品id
        }

        //操作人姓名
        $op_user_str = implode("','",$op_user);
        if(count($op_user) > 0)
        {
            $op_user_str = "'".$op_user_str."'";
            $sql24   = "select weixin_name,weixin_fromuser from weixin_users where isvalid = true and id = '{$user_id}'";
            $res24   = $this->db->getAll($sql24);
        }else{
            $res24   = $res2;
        }
        return array('errcode'=>0,'errmsg'=>'success','data'=>array('res23'=>$res23,'res2'=>$res2,'res24'=>$res24));
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——点击各类支付跳转url
    开 发 者：taojin
    开发日期： 2018-04-09
    重要说明：
     */
    public function get_pay_info($data){
        $url = '';
        $sql27 = "select callBackBatchcode,price,pay_batchcode from paycallback_t where isvalid=true and pay_batchcode = '{$data['pay_batchcode']}'";
        $res27 = $this->db->getAll($sql27);

        if($data['paystyle'] == '通联支付'){
            $url = "[<a href=\"/mshop/admin/Order/order/allipay_detail.php?allipay_orderid={$data['allipay_orderid']}\">". $data['allipay_orderid'] ."(点击查看)</a>]";;
        }elseif ($data['paystyle'] == '支付宝支付'){
            $alipaySql = "select pay_batchcode,transaction_id from system_order_pay_log where   pay_batchcode='".$data['pay_batchcode']."' limit 0,1";
            $res = $this->db->getRow($alipaySql);
            $transaction_id = $res['transaction_id'];
            $pay_batchcode = $res['pay_batchcode'];
            $url = "[<a href=\"/mshop/admin/Order/order/alipay_rsa_detail.php?pay_batchcode={$pay_batchcode}&batchcode={$data['batchcode']}\">". $transaction_id ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '通联分期支付'){
            $alipaySql = "select pay_batchcode,transaction_id from system_order_pay_log where pay_batchcode='".$data['pay_batchcode']."' limit 0,1";
            $res = $this->db->getRow($alipaySql);
            $transaction_id = $res['transaction_id'];
            $pay_batchcode = $res['pay_batchcode'];
            $url = "[<a href=\"/mshop/admin/Order/order/allinpay_rsa_detail.php?pay_batchcode={$pay_batchcode}&batchcode={$data['batchcode']}\">". $transaction_id ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '微信支付' or $data['paystyle'] == '找人代付' or $data['paystyle'] == '兴业银行公众号支付'){
            $weipay = "select transaction_id from weixin_weipay_notifys where isvalid=true and out_trade_no='".$data['pay_batchcode']."'";
            $result = $this->db->getRow($weipay);
            $transaction_id = $result['transaction_id'];

            $wxpay_version=1;
            $query_ver = "select version from pay_config where isvalid=true and customer_id=".$data['customer_id']." and pay_type = 'weipay' limit 1";
            $result_ver = $this->db->getRow($query_ver);
            $wxpay_version = $result_ver['version'];

            if($wxpay_version==2){
                $url = "[<a href=\"/mshop/admin/Order/order/weipay_detail.php?allipay_orderid=".$transaction_id."&pay_batchcode=".$data['pay_batchcode']."&batchcode={$data['batchcode']}\">". $transaction_id ."(点击查看)</a>]";
            }else{
                $url = "[<a href=\"/mshop/admin/Order/order/weipay_detail2.php?pay_batchcode=".$data['pay_batchcode']."&batchcode={$data['batchcode']}\">". $transaction_id ."(点击查看)</a>]";
            }
        }elseif ($data['paystyle'] == '环迅快捷支付' or $data['paystyle'] == '环迅微信支付'){
            foreach ($res27 as $k => $v)
            {
                if($data['pay_batchcode'] == $v['pay_batchcode'])
                {
                    $callBackBatchcode = $v['callBackBatchcode'];
                    $settlementprice   = $v['price'];
                }
            }

            $url = "[<a href=\"/mshop/admin/Order/order/hxpay_detail.php?pay_batchcode={$data['pay_batchcode']}&batchcode={$data['batchcode']}\">". $callBackBatchcode ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '威富通支付'){
            $wftpay = "select transaction_id,wft_type,real_pay_price from system_order_pay_log where pay_batchcode='".$data['pay_batchcode']."'";
            $res = $this->db->getRow($wftpay);
            $transaction_id =$res['transaction_id'];
            $wft_type = $res['wft_type'];
            $settlementprice = $res['real_pay_price'];
            $url = "[<a href=\"/mshop/admin/Order/order/wftpay_detail.php?allipay_orderid=".$transaction_id."&wft_type=".$wft_type."&pay_batchcode=".$data['pay_batchcode']."&batchcode={$data['batchcode']}\">". $transaction_id ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '健康钱包支付'){
            foreach ($res27 as $k => $v)
            {
                if($data['pay_batchcode'] == $v['pay_batchcode'])
                {
                    $callBackBatchcode = $v['callBackBatchcode'];
                    $settlementprice   = $v['price'];
                }
            }
            $url = "[<a href=\"/mshop/admin/Order/order/healthpay_detail.php?pay_batchcode={$data['pay_batchcode']}&batchcode={$data['batchcode']}\">". $callBackBatchcode ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '易宝支付'){
            foreach ($res27 as $k => $v)
            {
                if($data['pay_batchcode'] == $v['pay_batchcode'])
                {
                    $callBackBatchcode = $v['callBackBatchcode'];
                    $settlementprice   = $v['price'];
                }
            }
            $url = "[<a href=\"/mshop/admin/Order/order/yeepay_detail.php?pay_batchcode={$data['pay_batchcode']}&batchcode={$data['pay_batchcode']}\">". $callBackBatchcode ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '京东支付'){
            $paySql = "select callBackBatchcode from paycallback_t where isvalid=true and pay_batchcode='".$data['pay_batchcode']."' limit 0,1";
            $res = $this->db->getRow($paySql);
            $callBackBatchcode = $res['callBackBatchcode'];
            echo "[<a href=\"/mshop/admin/Order/order/jdpay_detail.php?pay_batchcode={$data['pay_batchcode']}&batchcode={$data['batchcode']}\">". $callBackBatchcode ."(点击查看)</a>]";
        }else{
            foreach ($res27 as $k => $v)
            {
                if($data['pay_batchcode'] == $v['pay_batchcode'])
                {
                    $callBackBatchcode = $v['callBackBatchcode'];
                    $settlementprice   = $v['price'];
                }
            }
            $url = "[<a href=\"/mshop/admin/Order/order/pay_detail.php?pay_batchcode={$data['pay_batchcode']}\">". $callBackBatchcode ."(点击查看)</a>]";
        }
        return array('errcode'=>0,'errmsg'=>'success','data'=>$url);
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主订单——平台订单数量及自营订单数量
    开 发 者：taojin
    开发日期： 2018-04-08
    重要说明：参数data 条件数据， type: 0平台订单 1自营订单， where sql条件判断
     */
    public function get_order_num($data,$type){
        $where = '';
        if($data['batchcode'])       $where .= " and o.batchcode = '{$data['batchcode']}'";
        if($type)                    $where .= " and o.yundian_self = '$type'";
        if($data['user_id']){
            //查询云店id
            $sql_yun = "SELECT id from ".WSY_USER.".weixin_yundian_keeper where user_id = '{$data['user_id']}' and isvalid =true and customer_id = '{$data['customer_id']}' ";
            $res_yun = $this->db->getAll($sql_yun);
            $ids = '';
            foreach($res_yun as $k => $v){
                $ids = $v['id'].',';
            }
            $ids1 = trim($ids,',');
            if($ids1){
                $where .= " and o.yundian_id in ({$ids1})";
            }else{
                $return['data']      = array(array(),0);
                $return['errcode']   = 0;
                $return['msg']       = 'success';
                return $return;
            }
        }
        if($data['name']){
            $sql_mem = "select k.id from ".WSY_USER.".weixin_users u inner join ".WSY_USER.".weixin_yundian_keeper k on u.id=k.user_id where k.customer_id = '{$data['customer_id']}' and u.name like '%{$data['name']}%'";
            $res_mem = $this->db->getAll($sql_mem);
            $ids = '';
            foreach($res_mem as $k => $v){
                $ids .= $v['id'].',';
            }
            $ids2 = trim($ids,',');
            if($ids2){
                $where .= " and o.yundian_id in ({$ids2})";
            }else{
                $return['data']      = array(array(),0);
                $return['errcode']   = 0;
                $return['msg']       = 'success';
                return $return;
            }
        }

        switch ($data['status']){
            case 0;//全部
                break;
            case 1;//代发货
                $where .= " and o.sendstatus = 0 and (o.paystatus = 1 and o.paystyle != '货到付款') and o.status = 0 and aftersale_type=0";
                break;
            case 2;//待收货
                $where .= " and o.sendstatus = 1 and (o.paystatus = 1 or o.paystyle = '货到付款') and o.status = 0 and aftersale_type=0";
                break;
            case 3;//待完成
                $where .= " and o.sendstatus = 2 and o.paystatus = 1 and o.status = 0 and aftersale_type=0";
                break;
            case 4;//交易完成
                $where .= ' and ( o.sendstatus = 2 and o.paystatus = 1 and o.status = 1 ) and aftersale_type = 0';
                break;
            case 5;//退款
                $where .= " and o.aftersale_type = 1 and o.paystatus = 1 ";
                break;
            case 6;//退货
                $where .= " and o.aftersale_type = 2 and o.paystatus = 1 ";
                break;
            case 7;//换货
                $where .= " and o.aftersale_type = 3 and o.paystatus = 1 ";
                break;
        }

        $sql_count = "SELECT count(DISTINCT o.batchcode) as num from weixin_commonshop_orders o  where o.isvalid = true and o.paystatus = 1 {$where} and o.yundian_id>0 and o.customer_id = '{$data['customer_id']}' and o.is_sendorder <> 1 and o.is_collageActivities <> 2 and o.shopactivity_mark = 0 ";
        $res = $this->db->getRow($sql_count);
        return $res['num'];
    }

