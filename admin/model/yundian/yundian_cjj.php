<?php


    /*
    * 云店日志公用方法
    * $Author: cjj
    * $2018-04-08  $
    * $data=['customer_id','remark'];  //需要传入的数据,配置参数json的从数据库中查找    customer_id //商家ID   remark //备注
    */
    function save_admin_yundian_log($data){
        $customer_id            = $data['customer_id'];
        $data['operationuser']  = $_SESSION['curr_login'];
        $data['createtime']     = date('Y-m-d H:i:s',time());

        $query = "select customer_id,yundian_onoff,yundian_apply_onoff,yundian_choucheng,receipt_onoff,receipt_time,invalid_onoff,invalid_time,clearing_onoff,playmoney_onoff,complete_onoff,shop_valid_time,shop_notice_time,yundian_reward,yundian_bg from ".WSY_REBATE.".weixin_yundian_setting where customer_id=" . $customer_id . " ";

        $res_basic     = $this->db->getRow($query);     //获取操作后的配置参数
        $data['json']  = json_encode($res_basic, JSON_UNESCAPED_UNICODE);

        $res = $this->db->autoExecute(WSY_REBATE.'.weixin_yundian_setting_log', $data, 'insert');//插入integral_log表

        return $res;
    }

    /*
    * 后台操作日志
    * author：cjj
    */
    function yundian_setting_log($param = array()){
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum  = $param['pageNum'] ? : 1; //当前页,1开始
        $start    = ($pageNum-1)*$pageSize;
        $end      = $pageSize;

        //分页设置 end
        $word           = -1;
        $start_time     = -1;
        $end_time    = -1;
        $customer_id    = $param['customer_id'];

        if($param['word']){
            $word        = mysql_escape_string($param['word']);
        }
        if($param['start_time']){
            $start_time  = $param['start_time'];
        }
        if($param['end_time']){
            $end_time = $param['end_time'];
        }

        $sql = " select id,operationuser,title,remark,createtime  from ".WSY_REBATE.".weixin_yundian_setting_log where customer_id=".$customer_id."  ";
        /************** 搜索条件 start ******************/
        if($word!=-1){
            $sql .= " AND remark like '%".$word."%'";
        }
        if($start_time!=-1){
            $sql .= " AND createtime >= '".$start_time."'";
        }
        if($end_time!=-1){
            $sql .= " AND createtime <= '".$end_time."'";
        }
        /************** 搜索条件 end ******************/
        $res_total  = $this->db->getAll($sql);
        $res_count  = count($res_total);//总共多少条记录

        if( $param['pageNum'] > 0 ){
            $sql .= " order by id desc limit ".$start.",".$end;
        }

        $res        = $this->db->getAll($sql);
        $pageCount  = ceil($res_count/$pageSize);//总页数
        $return['pageCount'] = $pageCount;
        $return['res'] = $res;
        return $return;
    }

    /*
    * 云店配置比较，方便插入操作日志
    * $Author: cjj
    * $2018-04-08  $
    * $data=[];  //旧云店配置数据     $result =[];
    */
    function compare_yundian_setting($data,$result,$customer_id){
        $remark_return = '修改配置：';
        $remark        = '';
        $remark_title  = '';

        $query = "select customer_id,yundian_onoff,yundian_apply_onoff,yundian_choucheng,receipt_onoff,receipt_time,invalid_onoff,invalid_time,clearing_onoff,playmoney_onoff,complete_onoff,shop_valid_time,shop_notice_time,yundian_reward,yundian_bg from ".WSY_REBATE.".weixin_yundian_setting where customer_id=" . $customer_id . " ";
        $res_basic_old     = $this->db->getRow($query);     //获取操作后的配置参数

        if($data['yundian_onoff'] != $res_basic_old['yundian_onoff'] &&  $data['yundian_onoff'] == 1){
            $remark .= '打开云店开关，';
            $remark_title .= '修改云店开关，';
        }else if($data['yundian_onoff'] != $res_basic_old['yundian_onoff'] &&  $data['yundian_onoff'] == 0){
            $remark .= '关闭云店开关，';
            $remark_title .= '修改云店开关，';
        }

        if($data['yundian_apply_onoff'] != $res_basic_old['yundian_apply_onoff'] &&  $data['yundian_apply_onoff'] == 1){
            $remark .= '打开云店申请开关，';
            $remark_title .= '修改云店申请开关，';
        }else if($data['yundian_apply_onoff'] != $res_basic_old['yundian_apply_onoff'] &&  $data['yundian_apply_onoff'] == 0){
            $remark .= '关闭云店申请开关，';
            $remark_title .= '修改云店申请开关，';
        }

        if($data['yundian_choucheng'] != $res_basic_old['yundian_choucheng']){
            $remark .= '修改自营产品总抽成为'.$data['yundian_choucheng'].'，';
        }

        if($data['receipt_onoff'] != $res_basic_old['receipt_onoff'] && $data['receipt_onoff'] == 1){
            $remark .= '打开默认收货时间开关，';
            $remark_title .= '修改默认收货时间开关，';
        }else if($data['receipt_onoff'] != $res_basic_old['receipt_onoff'] &&  $data['receipt_onoff'] == 0){
            $remark .= '关闭默认收货时间开关，';
            $remark_title .= '修改默认收货时间开关，';
        }

        if($data['receipt_time'] != $res_basic_old['receipt_time']){
            $remark .= '修改默认收货时间为'.$data['receipt_time'].'，';
        }

        if($data['invalid_onoff'] != $res_basic_old['invalid_onoff'] && $data['invalid_onoff'] == 1){
            $remark .= '打开订单失效开关，';
            $remark_title .= '修改订单失效开关，';
        }else if($data['invalid_onoff'] != $res_basic_old['invalid_onoff'] &&  $data['invalid_onoff'] == 0){
            $remark .= '关闭订单失效开关，';
            $remark_title .= '修改订单失效开关，';
        }

        if($data['invalid_time'] != $res_basic_old['invalid_time']){
            $remark .= '修改订单失效时间为'.$data['invalid_time'].'，';
        }

        if($data['clearing_onoff'] != $res_basic_old['clearing_onoff'] && $data['clearing_onoff'] == 1){
            $remark .= '打开自营产品订单收货自动结算开关，';
            $remark_title .= '修改收货自动结算开关，';
        }else if($data['clearing_onoff'] != $res_basic_old['clearing_onoff'] &&  $data['clearing_onoff'] == 0){
            $remark .= '关闭自营产品订单收货自动结算开关，';
            $remark_title .= '修改收货自动结算开关，';
        }

        if($data['playmoney_onoff'] != $res_basic_old['playmoney_onoff'] && $data['playmoney_onoff'] == 1){
            $remark .= '打开售后平台打款开关，';
            $remark_title .= '修改售后平台打款开关，';
        }else if($data['playmoney_onoff'] != $res_basic_old['playmoney_onoff'] &&  $data['playmoney_onoff'] == 0){
            $remark .= '关闭售后平台打款开关，';
            $remark_title .= '修改售后平台打款开关，';
        }

        if($data['complete_onoff'] != $res_basic_old['complete_onoff'] && $data['complete_onoff'] == 1){
            $remark .= '打开退款之后自动完成订单开关，';
            $remark_title .= '修改自动完成订单开关，';
        }else if($data['complete_onoff'] != $res_basic_old['complete_onoff'] &&  $data['complete_onoff'] == 0){
            $remark .= '关闭退款之后自动完成订单开关，';
            $remark_title .= '修改自动完成订单开关，';
        }

        if($data['shop_valid_time'] != $res_basic_old['shop_valid_time']){
            $remark .= '修改默认店主有效天数为'.$data['shop_valid_time'].'，';
        }

        if($data['shop_notice_time'] != $res_basic_old['shop_notice_time']){
            $remark .= '修改提前通知天数为'.$data['shop_notice_time'].'，';
        }

        if($data['yundian_reward'] != $res_basic_old['yundian_reward']){
            $remark .= '修改云店奖励比例为'.$data['yundian_reward'].'，';
        }

        $remark_keeper = '';
        foreach ($result as $k => $v)
        {
            $data2['id']           = $result[$k]['id'];
            $data2['is_identity']  = $result[$k]['is_identity'];
            $data2['name']         = $result[$k]['name'];
            $data2['reward']       = $result[$k]['reward'];
            $data2['apply_money']  = $result[$k]['apply_money'];
            $data2['createtime']   = date("Y-m-d H:i:s",time());

            $arr[$k] = $result[$k]['tequan'];
            $a   = $arr[$k][1]?$arr[$k][1]:0;
            $b   = $arr[$k][2]?$arr[$k][2]:0;
            $c   = $arr[$k][3]?$arr[$k][3]:0;
            $d   = $arr[$k][4]?$arr[$k][4]:0;
            $e   = $arr[$k][5]?$arr[$k][5]:0;

            $query_keeper = "select is_identity,name,reward,apply_money,tequan from ".WSY_REBATE.".weixin_yundian_identity where customer_id=" . $customer_id . " and id=".$data2['id']." and isvalid=1 ";
            $res_keeper_old[$k]     = $this->db->getRow($query_keeper);     //获取操作后的配置参数

            if($res_keeper_old[$k]['is_identity'] != $data2['is_identity'] && $data2['is_identity'] == 1){
                $remark_keeper .= '打开'.$res_keeper_old[$k]['name'].'的身份开关，';
                $remark_title .= '修改'.$res_keeper_old[$k]['name'].'的身份开关，';
            }else if($res_keeper_old[$k]['is_identity'] != $data2['is_identity'] && $data2['is_identity'] == 0){
                $remark_keeper .= '关闭'.$res_keeper_old[$k]['name'].'的身份开关，';
                $remark_title .= '修改'.$res_keeper_old[$k]['name'].'的身份开关，';
            }

            if($res_keeper_old[$k]['name'] != $data2['name']){
                $remark_keeper .= '修改‘'.$res_keeper_old[$k]['name'].'’名字为‘'.$data2['name'].'’，';
            }

            if($res_keeper_old[$k]['reward'] != floatval($data2['reward'])){
                $remark_keeper .= '修改‘'.$data2['name'].'’的比例为'.$data2['reward'].'，';
            }

            if($res_keeper_old[$k]['apply_money'] != $data2['apply_money']){
                $remark_keeper .= '修改‘'.$data2['name'].'’的申请金额为'.$data2['apply_money'].'，';
            }

            $res_keeprt_tequan_arr[$k] = explode('_',$res_keeper_old[$k]['tequan']);

            if($res_keeprt_tequan_arr[$k][0] != $a && $a == 1){
                $remark_keeper .= '打开'.$data2['name'].'的店铺推广开关，';
            }else if($res_keeprt_tequan_arr[$k][0] != $a && $a == 0){
                $remark_keeper .= '关闭'.$data2['name'].'的店铺推广开关，';
            }

            if($res_keeprt_tequan_arr[$k][1] != $b && $b == 1){
                $remark_keeper .= '打开'.$data2['name'].'的个性化店标开关，';
            }else if($res_keeprt_tequan_arr[$k][1] != $b && $b == 0){
                $remark_keeper .= '关闭'.$data2['name'].'的个性化店标开关，';
            }

            if($res_keeprt_tequan_arr[$k][2] != $c && $c == 1){
                $remark_keeper .= '打开'.$data2['name'].'的收益实时查询开关，';
            }else if($res_keeprt_tequan_arr[$k][2] != $c && $c == 0){
                $remark_keeper .= '关闭'.$data2['name'].'的收益实时查询开关，';
            }

            if($res_keeprt_tequan_arr[$k][3] != $d && $d == 1){
                $remark_keeper .= '打开'.$data2['name'].'的店铺自营订单管理开关，';
            }else if($res_keeprt_tequan_arr[$k][3] != $d && $d == 0){
                $remark_keeper .= '关闭'.$data2['name'].'的店铺自营订单管理开关，';
            }

            if($res_keeprt_tequan_arr[$k][4] != $e && $e == 1){
                $remark_keeper .= '打开'.$data2['name'].'的店铺自营订单管理开关，';
            }else if($res_keeprt_tequan_arr[$k][4] != $e && $e == 0){
                $remark_keeper .= '关闭'.$data2['name'].'的店铺自营订单管理开关，';
            }
        }

        $remark .= $remark_keeper;

        if(!empty($remark)){
            $remark_return .= $remark;
        }else{
            $remark_return .= '无';
        }

        if(empty($remark_title)){
            $remark_title = '修改配置';
        }

        $res_remark['remark'] = $remark_return;
        $res_remark['title'] = $remark_title;

        return $res_remark;
    }
