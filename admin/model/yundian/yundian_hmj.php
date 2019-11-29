<?php


    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表查询
    开 发 者：HMJ-V384
    开发日期： 2018-04-04
    重要说明：无
     */
    function get_yundian_shopkeeper_list($param){
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum  = $param['pageNum'] ? : 1; //当前页,1开始
        $start    = ($pageNum-1)*$pageSize;
        $end      = $pageSize;

        //分页设置 end
        $shopkeeper_arr = array();
        $user_id        = -1;
        $name           = -1;
        $verify_time     = -1;
        $expire_time    = -1;
        $tequan_id      = -1;
        $customer_id    = $param['customer_id'];

        if(!empty($param['user_id'])){
            $user_id     = (int)$param['user_id'];
        }
        if($param['name']){
            $name        = mysql_escape_string($param['name']);
        }
        if($param['verify_time']){
            $verify_time  = $param['verify_time'];
        }
        if($param['expire_time']){
            $expire_time = $param['expire_time'];
        }
        if($param['tequan_id']){
            $tequan_id   = (int)$param['tequan_id'];
        }

        $sql = "select k.user_id,k.realname,k.verify_time,k.expire_time,k.tequan_id,k.status,ifnull(count(p.isout), 0) as isup,p.yundian_id,
         k.profit_shop,k.profit_self,k.product_count,k.order_count,u.name,i.name AS identity_name,k.profit_keeper FROM ".WSY_USER.".`weixin_yundian_keeper` k
         LEFT JOIN ".WSY_USER.".`weixin_users` u ON k.user_id=u.id AND u.isvalid='1'
         LEFT JOIN ".WSY_REBATE.".`weixin_yundian_identity` i ON k.tequan_id=i.id AND i.isvalid='1'
         LEFT JOIN ".WSY_PROD.".`weixin_commonshop_products` p ON p.yundian_id=k.id AND p.isvalid='1' AND p.isout=0 
         WHERE k.customer_id='".$customer_id."' AND k.isvalid='1' AND k.status='1'";
        /************** 搜索条件 start ******************/
        if($user_id!=-1){
            $sql .= " AND k.user_id like '%".$user_id."%'";
        }
        if($name!=-1){
            $sql .= " AND u.name like '%".$name."%'";
        }
        if($verify_time!=-1){
            $sql .= " AND k.verify_time >= '".$verify_time."'";
        }
        if($expire_time!=-1){
            $sql .= " AND k.expire_time <= '".$expire_time."'";
        }
        if($tequan_id!=-1){
            $sql .= " AND k.tequan_id = '".$tequan_id."' ";
        }
        // if( $param['threshold'] || $param['threshold'] === 0 ){
        //     $sql .= " AND threshold<=".$param['threshold'];
        // }
        if( $param['nowtime'] ){
            $sql .= " AND k.expire_time >= '{$param['nowtime']}' ";
            $sql .= " AND k.verify_time <= '{$param['nowtime']}' ";
        }
        /************** 搜索条件 end ******************/
        $sql .= " group by k.id";
        $shopkeeper_total = $this->db->getAll($sql);
        $shopkeeper_count = count($shopkeeper_total);//总共多少条记录

        if( $param['pageNum'] > 0 ){
            $sql .= " order by k.id desc limit ".$start.",".$end;
        }

        if( $param['order'] ){
            $sql .= " order by ".$param['order'];
        }


        $shopkeeper_arr = $this->db->getAll($sql);
        $pageCount = ceil($shopkeeper_count/$pageSize);//总页数

        //总订单查询
        $sql1 = "select ifnull(count(*), 0) as order_sum,yundian_id
         FROM weixin_commonshop_orders
         WHERE customer_id='".$customer_id."' AND isvalid='1' AND paystatus=1 AND yundian_id!='-1' group by yundian_id";        
        //总订单查询end
        $order_sum_arr = $this->db->getAll($sql1);
         foreach ($shopkeeper_arr as $key0 => $value0) {
             foreach ($order_sum_arr as $key1 => $value1) {
                 if($value0['yundian_id'] == $value1['yundian_id']) {
                    $shopkeeper_arr[$key0]['order_sum'] = $value1['order_sum'];
                    unset($order_sum_arr[$key1]);break;
                 }
             }
             if(!isset($shopkeeper_arr[$key0]['order_sum'])) {
                $shopkeeper_arr[$key0]['order_sum'] = 0;
             }
         }
        // $return['sql'] = $sql;
        $return['pageCount'] = $pageCount;
        $return['shopkeeper_arr'] = $shopkeeper_arr;
        return $return;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表查询
    开 发 者：HMJ-V384
    开发日期： 2018-04-04
    重要说明：无
     */
    function setting_list_select($customer_id){

        $result = array();
        $result['errcode'] = 0;
        $result['errmsg'] = '';

        $sql = "select * from ".WSY_REBATE.".weixin_yundian_setting where customer_id='".$customer_id."' and isvalid=true";
        $res = $this->db->getRow($sql);

        if ($res) {
            $sql2 = "select id,is_identity,name,reward,apply_money,tequan,remark from ".WSY_REBATE.".weixin_yundian_identity where customer_id='".$customer_id."' and isvalid=true order by reward DESC,createtime ASC";
            $result2 = $this->db->getAll($sql2);
        }else{
            $result['errcode'] = 400030;
            $result['errmsg']  = '商家还未设置云店';
            return $result;
        }
        $result['res1'] = $res;
        $result['res2'] = $result2;
        return $result;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——删除店主
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
     */
    function del_yundian_shopkeeper($param){
        $return            = array();
        $return['errcode'] = 0;
        $return['errmsg']  = "删除失败！";

        $customer_id = $param['customer_id'];
        $user_id     = $param['user_id'];

        $sql = "update ".WSY_USER.".`weixin_yundian_keeper` set isvalid=false where customer_id='".$customer_id."' and user_id='".$user_id."' and isvalid=true";
        $res = $this->db->query($sql);
        if($res){
            $return['errcode'] = 1;
            $return['errmsg'] = "删除成功！";
        }
        return $return;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——编辑店主，信息读取
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
     */
    public function get_yundian_shopkeeper($data){
        $result = [];
        $sql1   = "SELECT k.user_id,k.profit_shop,k.self_reware,k.expire_time,k.tequan_id,i.name FROM ".WSY_USER.".`weixin_yundian_keeper` k INNER JOIN
         ".WSY_REBATE.".weixin_yundian_identity i ON k.tequan_id=i.id WHERE k.customer_id='".$data['customer_id']."' and k.user_id= '".$data['user_id']."' and k.isvalid = '1' ";

        $result['keeper_msg'] = $this->db->getRow($sql1); //查询用户信息

        $result['yundian_identity'] = $this->get_yundian_identity($data['customer_id']); //查询所有身份

        return $result;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——获取所有特权ID NAME
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
     */
    public function get_yundian_identity($customer_id){
        $sql = "SELECT id,name FROM ".WSY_REBATE.".weixin_yundian_identity WHERE customer_id='".$customer_id."' and isvalid = '1' order by reward DESC,createtime ASC";

        $result = $this->db->getAll($sql); //查询所有身份

        return $result;
    }
    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——编辑店主信息
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
     */
    public function save_shopkeeper_data($data,$data2,$w) {
        $return['errcode'] = 0;
        $return['errmsg']  = "保存失败！";

        //查用用户现有特权ID
        $ret1 = $this->db->getRow ("select tequan_id from ".WSY_USER.".`weixin_yundian_keeper` where status = '1' and isvalid = true and user_id='".$w['user_id']."' and customer_id = '".$w['customer_id']."'");

        if($ret1['tequan_id'] != $data2['tequan_id']) {

            //事务处理
            $this->db->tran_begin();
            try{
                //更新特权表，待审核状态不写入
                $sql = "UPDATE ".WSY_REBATE.".`weixin_yundian_identity_applylog` SET `tequan_id` = '".$data2['tequan_id']."' where status = '1' and isvalid = true and user_id='".$w['user_id']."' and customer_id = '".$w['customer_id']."'";

                $ret2 = $this->db->query($sql,'',1) ;

                if(!$ret2) {
                    $return['errmsg']  = "特权ID处理中，无法修改！";
                } else {
                    $where = "status = '1' and isvalid = true and user_id='".$w['user_id']."' and customer_id = '".$w['customer_id']."'";

                    $res  = $this->db->autoExecute("".WSY_USER.".`weixin_yundian_keeper`", $data, 'update',$where) ;                      
                }

            if ($res) {
                $return['errcode'] = 1;
                $return['errmsg']  = "保存成功！";
                $return['data'] = $ret2;
            }            

            } catch(Exception $e){
                $this->db->tran_rollback();
                return $return;
            }
            $this->db->tran_commit();
            return $return;

        } else {
            if(!$ret1) {
                unset($data['tequan_id']);
            }

            $where = "status = '1' and isvalid = true and user_id='".$w['user_id']."' and customer_id = '".$w['customer_id']."'";

            $res  = $this->db->autoExecute("".WSY_USER.".`weixin_yundian_keeper`", $data, 'update',$where) ;           
            if ($res) {
                $return['errcode'] = 1;
                $return['errmsg']  = "保存成功！";
            } 
        }

        return $return;
    }

    /*  云店用户退款，换货，退货接口--获取订单信息
     *  $Author:HMJ-V384
     *  2018-4-09
     *  return:
     **/
    public function get_yundian_order_msg($batchcode,$customer_id) {
        $select_sql = "SELECT paystyle,yundian_id,is_open_aftersale,yundian_self,pay_batchcode,paystatus,sendstatus,aftersale_type,return_account,user_id,aftersale_state,totalprice 
        FROM weixin_commonshop_orders WHERE batchcode='{$batchcode}' AND customer_id='{$customer_id}' and isvalid = '1' ";
        $result    = $this->db->getRow($select_sql);    
        return $result; 
    }

    /*  云店用户退款，换货，退货----更新订单状态，单订单表
     *  $Author:HMJ-V384
     *  2018-4-10
     *  status:fin
     **/
    public function set_yundian_return_status_confirm($batchcode,$customer_id,$sql_data_order) {
        $result = $this->db->autoExecute('weixin_commonshop_orders', $sql_data_order, 'update', "batchcode='{$batchcode}' AND isvalid=true AND customer_id='{$customer_id}'");
        return $result;
    }

    /*  云店用户退款，换货，退货----店主同意退款，零钱或第三方接口退款
     *  $Author:HMJ-V384
     *  2018-4-12
     *  status:todo
     **/
    public function yundian_money_return($user_id,$customer_id,$batchcode){
        $order_msg = $this->get_yundian_order_msg($batchcode,$customer_id);
        if(!$order_msg) {
            return false;
        } else {
            if($order_msg['paystyle'] == '微信支付'){
                $sql = "SELECT partnerid FROM ".WSY_PAY.".pay_config WHERE customer_id='{$customer_id}' AND pay_type='weipay' AND isvalid=true";
                $sjj = $this->db->getRow($sql);
                $partnerid = $sjj['partnerid'];
        
                if( $partnerid != '' ){
                    //发送的数据
                    $post_data  = array(
                        'batchcode' => $batchcode,
                        'transaction_id' => $partnerid,
                        'total_fee' => $order_msg['totalprice'],
                        'refund_fee' => $order_msg['return_account']
                    );

                    $post_data = http_build_query($post_data);
                    $url = Protocol.$_SERVER["HTTP_HOST"].'/weixinpl/common_shop/jiushop/refund_yundian.php?customer_id='.$customer_id;     //调用拼团微信退款

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);                    // 要访问的地址
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, 1 );
                    curl_setopt($ch, CURLOPT_HEADER, 0);                    // 显示返回的Header区域内容
                    curl_setopt($ch, CURLOPT_NOBODY, 0);                    //只取body头
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 对认证证书来源的检查
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);        // 从证书中检查SSL加密算法是否存在
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)'); // 模拟用户使用的浏览器
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);            // 使用自动跳转
                    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);               // 自动设置Referer
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);       // Post提交的数据包
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);                  // 设置超时限制防止死循环
                    $curl_error = curl_error($ch);
                    $json = curl_exec($ch);

                    curl_close($ch);

                    $jsons = json_decode($json,true);
                    return $jsons;
                    //微信退款失败
                    if( $jsons['return_code'] == 'FAIL' || $jsons['result_code'] == 'FAIL' ){
                        $r['errcode'] = 406;
                        $r['errmsg'] = $jsons['err_code_des'] ? $jsons['err_code_des'] : $jsons['return_msg'];
                        return $r;
                    } else {
                        $sendMessage_content = "亲，您的微信零钱 +".$order_msg['return_account']."元\r\n".
                                                "来源：【云店商品退款】\n".
                                                "状态：【退款到帐】\n".
                                                "时间：".date( "Y-m-d H:i:s")."";
                        $r['errmsg'] = '微信退款成功';
                        $r['errcode'] = '0';
                        return $r;                                                    
                    }
                }

            }else if(!empty($order_msg['paystyle'])){
                $user_id = $order_msg['user_id'];
                $money = $order_msg['return_account'];

                $refund_pay['aftersale_state'] = 4;
                $result = $this->set_yundian_return_status_confirm($batchcode,$customer_id,$refund_pay);
                if(!$result){
                    $r['errmsg'] = '系统零钱退款失败,请联系客服';
                    $r['errcode'] = '401';
                    return $r;
                }else{
                    $ret = $this->editBalance($customer_id, $user_id, $money , $batchcode);
                    if(!$ret) {
                        $r['errmsg'] = '零钱退款失败';
                        $r['errcode'] = '400';
                        return $r;                      
                    }
                        $r['errmsg'] = '零钱退款成功';
                        $r['errcode'] = '0';
                        return $r;
                }
            }else{
                $r['errmsg'] = '未知错误';
                $r['errcode'] = '400';
                return $r;
            }
        }
    }   

    # 查询用户零钱余额 start #
    protected function getBalance($customerId, $userId) {
        $q = "SELECT balance FROM ".WSY_USER.".moneybag_t WHERE customer_id = '{$customerId}' AND isvalid = 1 AND user_id = '{$userId}'";
        $r = $this->db->getRow ($q);
        return $r["balance"] ?: 0;
    }
    # 查询用户零钱余额 end #

    # 修改用户零钱的余额 start #
    protected function editBalance($customerId, $userId, $expense , $pay_batchcode) {
        if (empty($customerId) || empty($userId) || empty($expense)) {
            $r['errmsg'] = '获取商家ID、用户ID或订单金额失败！';
            $r['errcode'] = '400';
            return $r;
        }
        $balance = $this->getBalance($customerId, $userId) + $expense;
        $q = "UPDATE ".WSY_USER.".moneybag_t SET balance = '{$balance}' WHERE customer_id = '{$customerId}' AND isvalid = 1 AND user_id = '{$userId}'";
        $r = $this->db->query($q);
        if ($r) {
            $parameters = [
                "customerId" => $customerId,
                "userId" => $userId,
                "expense" => $expense,
                "orderNumber" => $pay_batchcode//订单号
            ];
            $this->addChangeLog($parameters);
            return true;
        } else {
            return false;
        }
    }
    # 修改用户零钱的数值 end #
    # 更新零钱日志 start #
    protected function addChangeLog($parameters) {

        $customerId = $parameters["customerId"];
        $userId = $parameters["userId"];
        $expense = $parameters["expense"];
        $orderNumber = $parameters["orderNumber"];
        $dateTime = date("Y-m-d H:i:s");
        $q = "SELECT after_money AS balance FROM ".WSY_USER.".moneybag_log WHERE isvalid = 1 AND customer_id = {$customerId} AND user_id = {$userId} ORDER BY createtime DESC LIMIT 1";
        $r = $this->db->getRow ($q);
        $originalBalance = 0;
        if ($r) {
            $originalBalance =  $r["balance"];
        } else {
            $r['errmsg'] = '查询零钱日志失败';
            $r['errcode'] = '400';
            return $r;
        }
        $presentBalance = $originalBalance + $expense;
        $q = "INSERT INTO ".WSY_USER.".moneybag_log (isvalid, customer_id, user_id, before_money, money, after_money, type, batchcode, pay_style, remark, createtime, artificial) VALUES (1, {$customerId}, {$userId}, {$originalBalance}, {$expense}, {$presentBalance}, 1, \"{$orderNumber}\", 0, \"云店商品退款：【{$expense}】元\", \"{$dateTime}\", 0)";
        $r = $this->db->query($q);
        if ($r) {

        } else {
            $r['errmsg'] = '零钱日志更新失败';
            $r['errcode'] = '400';
            return $r;
        }
    }
    # 更新零钱日志 end #    

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主提成收入明细查询
    开 发 者：HMJ-V384
    开发日期： 2018-04-18
    重要说明：无
     */
    function yundian_shopkeeper_reward_detail($param){
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum  = $param['pageNum'] ? : 1; //当前页,1开始
        $start    = ($pageNum-1)*$pageSize;
        $end      = $pageSize;
        //分页设置 end
        $shopkeeper_arr = array();
        $user_id        = -1;
        $start_money    = -1;
        $end_time       = -1;
        $start_time     = -1;
        $end_time       = -1;
        $from_id        = -1;
        $pay_style      = 0;
        $customer_id    = $param['customer_id'];

        if($param['start_money']){
            $start_money = (float)$param['start_money'];
        }
        if($param['end_money']){
            $end_money = (float)$param['end_money'];
        }
        if($param['start_time']){
            $start_time = $param['start_time'];
        }
        if($param['end_time']){
            $end_time = $param['end_time'];
        }
        if($param['from_id']){
            $from_id = (int)$param['from_id'];
        }
        if($param['user_id']){
            $user_id = (int)$param['user_id'];
        } else {
            $user_id = '';
        }
        if($param['pay_style']){
            $pay_style = (int)$param['pay_style'];
        }        

        $sql = "SELECT id,money,createtime,from_id,remark,batchcode FROM ".WSY_USER.".`moneybag_log` WHERE customer_id='".$customer_id."' AND isvalid='1' AND user_id='".$user_id ."'";
        /************** 搜索条件 start ******************/
        if($start_money!=-1){
            $sql .= " and money >= '".$start_money."'";
        }
        if($end_money!=-1){
            $sql .= " and money <= '".$end_money."'";
        }        
        if($start_time!=-1){
            $sql .= " and createtime >= '".$start_time."'";
        }
        if($end_time!=-1){
            $sql .= " and createtime <= '".$end_time."'";
        }
        if($from_id!=-1){
            $sql .= " and from_id like '%".$from_id."%' ";
        }

        if( $param['nowtime'] ){
            $sql .= " and end_time >= '{$param['nowtime']}' ";
            $sql .= " and start_time <= '{$param['nowtime']}' ";
        }
        if($pay_style){ //区分是提成还是自营产品收入 0 提成 1 自营 默认提成0
            //$sql .= " and pay_style = '39' ";
            $sql .= " and commission_type = 26 ";
        } else {
            $sql .= " and commission_type = 25 ";
        }
        /************** 搜索条件 end ******************/
        if( $param['pageNum'] > 0 ){
            $sql .= " order by id desc limit ".$start.",".$end;
        }

        $shopkeeper_reward_detail_arr = $this->db->getAll($sql);
        $shopkeeper_count = count($shopkeeper_reward_detail_arr);//总共多少条记录
        $pageCount = ceil($shopkeeper_count/$pageSize);//总页数


        // $return['sql'] = $sql;
        $return['pageCount'] = $pageCount;
        $return['shopkeeper_reward_detail_arr'] = $shopkeeper_reward_detail_arr;
        return $return;
    }
