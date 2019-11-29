<?php

/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表
    开 发 者：HMJ-V384
    开发日期： 2018-04-04
    重要说明：无
     */
    function yundian_shopkeeper_list(){             
        $customer_id = $this->customer_id;
        $theme       = $this->model_common->find_theme($customer_id);

        $param['customer_id']     = $this->customer_id;
        $param['user_id']         = $_REQUEST['user_id']?$_REQUEST['user_id']:-1;        
        $param['tequan_id']       = $_REQUEST['tequan_id']?$_REQUEST['tequan_id']:-1;
        $param['verify_time']      = $_REQUEST['verify_time']?$_REQUEST['verify_time']:-1;
        $param['expire_time']     = $_REQUEST['expire_time']?$_REQUEST['expire_time']:-1;
        $param['name']            = $_REQUEST['name']?$_REQUEST['name']:'';       
        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页

        $res       = $this->model->get_yundian_shopkeeper_list($param);//获取店主列表
        $res2      = $this->model->get_yundian_identity($this->customer_id);//获取所有特权，搜索用
        $data      = $res['shopkeeper_arr'];
        $pageCount = $res['pageCount'];        
        include("view/yundian/yundian_shopkeeper_list.php");
    }

/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——删除店主
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
    返回：  $return['errcode'] = 1/0 成功/失败
            $return['errmsg'] = "删除成功！/删除失败";
     */
    function del_yundian_shopkeepers(){
        $param['customer_id'] = $this->customer_id;
        $param['user_id']     = $_POST['user_id']?$_POST['user_id']:-1;
        $res = $this->model->del_yundian_shopkeeper($param);        
        json_out($res);
    }
/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——编辑店主跳转
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
     */
    function edit_yundian_shopkeepers(){
        $customer_id         = $this->customer_id;
        $temp['customer_id'] = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);
        $temp['user_id']     = $_REQUEST['user_id']?$_REQUEST['user_id']:"";
        $is_ajax             = $_REQUEST['is_ajax']?$_REQUEST['is_ajax']:"";
        if(!$is_ajax) {
            include("view/yundian/edit_yundian_shopkeeper.php");
        } else {
            if(!empty($temp['user_id'])) {
                $data  = $this->model->get_yundian_shopkeeper($temp);
                if(!empty($data['keeper_msg']) && !empty($data['yundian_identity'])) {
                    json_out(array('errcode' => 1,'errmsg'=>'获取数据成功！','data'=>$data));
                } else if(empty($data['keeper_msg'])){
                    json_out(array('errcode' => 0,'errmsg'=>'获取店主数据失败'));
                } else {
                    json_out(array('errcode' => 0,'errmsg'=>'权限获取失败'));
                }
            }            
        }
    }
/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——编辑店主信息
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
    返回：  $return['errcode'] = 1/0 成功/失败
            $return['errmsg'] = "编辑成功！/编辑失败";
     */
    function save_shopkeeper_datas(){
        $user_id                    = $_REQUEST['user_id']?$_REQUEST['user_id']:"";
        $data['tequan_id']          =$data2['tequan_id']        = $_REQUEST['tequan_id']?$_REQUEST['tequan_id']:"";
        $data['isvalid']            = true;
        $data['expire_time']        = $_REQUEST['expire_time']?$_REQUEST['expire_time']:"";
        $data['profit_shop']        = $_REQUEST['profit_shop']?$_REQUEST['profit_shop']:"";
        $data['self_reware']        = $_REQUEST['self_reware']?$_REQUEST['self_reware']:"";

        if(strtotime($data['expire_time']) > strtotime(date("Y-m-d H:i:s"))){   //当修改的过期时间大于现在当前时间，则将首次过期提醒改为0
            $data['first_warn'] = 0;
        }

        $obj['customer_id']         = $this->customer_id;
        $obj['user_id']             = $user_id;

        $res = $this->model->save_shopkeeper_data($data,$data2,$obj);

        json_out($res);
    }

    /*  云店用户退款，换货，退货接口-----卖家端审核--同意
     *  $Author:HMJ-V384
     *  2018-4-10
     *  status:todo
     *  return:
     **/
    function yundian_pay_return_agree()
    {
        $data['customer_id']    = $this->customer_id;       //商家id
        $data['user_id']        = $_POST['user_id'];        //当前用户ID
        $data['batchcode']      = $_POST['batchcode'];      //订单号
        
        //校验数据
        if (empty($data['customer_id']) || empty($data['batchcode']) || empty($data['user_id'])) {
            $return = array('errcode'=>400, 'errmsg'=>'参数异常！', 'data'=>$data);
            json_out($return);
        }

        $order_msg = $this->model->get_yundian_order_msg($data['batchcode'],$data['customer_id']);
        if(!$order_msg) {
            $return = array('errcode'=>402, 'errmsg'=>'订单数据读取失败！');
            json_out($return);
        }
        if($order_msg['aftersale_type'] == 1) { //申请退款
            if($order_msg['aftersale_state'] != 2) {
                $return = array('errcode'=>401, 'errmsg'=>'申请售后状态异常');
                json_out($return);                  
            }
        } else if($order_msg['aftersale_type'] == 2) { //申请退货
            if($order_msg['aftersale_state'] != 2 || $order_msg['return_status'] != 8) {
                $return = array('errcode'=>401, 'errmsg'=>'申请售后状态异常');
                json_out($return);                  
            }
        }

        $return = $this->model->yundian_money_return($data['user_id'],$data['customer_id'],$data['batchcode']);
            $descript = $return['errmsg'];
            $operation = 16;
            $data_logs = array(
                'batchcode'         =>$data['batchcode'],
                'operation'         =>$operation,
                'descript'          =>'云店退货/退款后台打钱审批：'.$descript,
                'operation_user'    =>$data['user_id'],
                'createtime'        =>date('Y-m-d H:i:s'),
                'isvalid'           =>'1'
            );
        $ret = $this->model->order_logs($data_logs);        
        json_out($return);          

    }

/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主提成收益明细列表
    开 发 者：HMJ-V384
    开发日期： 2018-04-04
    重要说明：无
     */
    function yundian_shopkeeper_reward_detail(){             
        $customer_id  = $this->customer_id;
        $theme        = $this->model_common->find_theme($customer_id);

        $param['customer_id']     = $this->customer_id;
        $param['user_id']         = $_REQUEST['user_id']?$_REQUEST['user_id']:'';
        $param['start_money']     = $_REQUEST['start_money']?$_REQUEST['start_money']:-1;
        $param['end_money']       = $_REQUEST['end_money']?$_REQUEST['end_money']:-1;
        $param['start_time']      = $_REQUEST['start_time']?$_REQUEST['start_time']:-1;
        $param['end_time']        = $_REQUEST['end_time']?$_REQUEST['end_time']:-1;     
        $param['from_id']         = $_REQUEST['from_id']?$_REQUEST['from_id']:-1;
        $param['pay_style']       = $_REQUEST['pay_style']?$_REQUEST['pay_style']:0;
        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页

        $res = $this->model->yundian_shopkeeper_reward_detail($param);//获取店主提成收益明细列表
        $data      = $res['shopkeeper_reward_detail_arr'];
        $pageCount = $res['pageCount'];        
        include("view/yundian/yundian_shopkeeper_reward_detail.php");
    }