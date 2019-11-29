<?php

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主订单
    开 发 者：taojin
    开发日期： 2018-04-08
    重要说明：参数type: 0平台订单 1自营订单
    方法  yundian_order_list  返回errcode(int) errmsg(string) data(array(0=>$data,1=>$count_data))
     */
    function yundian_order_list(){

            $customer_id            = $this->customer_id;
            $customer_id_en         = $this->customer_id_en;
            $theme  = $this->model_common->find_theme($customer_id);
            $data['status']         = $_GET['status']?$_GET['status']:0;            //订单状态-搜索条件
            $data['batchcode']      = $_GET['batchcode']?$_GET['batchcode']:0;      //订单号-搜索条件
            $data['type']           = $_GET['type']?$_GET['type']:0;                //平台订单-0  自营订单-1
            $data['customer_id']    = $this->customer_id;                           //商户号
            $data['yundian_id']     = $_GET['yundian_id']?$_GET['yundian_id']:'-1'; //云店id
            $data['user_id']        = intval($_GET['user_id']?$_GET['user_id']:false);      //店主id
            $data['name']           = $_GET['name']?$_GET['name']:'';               //店主昵称
            $data['pageNum']        = $_GET['pagenum']?$_GET['pagenum']:1;          //当前页
            $data['page_size']      = 20;                                           //每页显示数
            if ($data['type'] == 0){
                unset($data['name']);
                unset($data['user_id']);
            }
            //获取订单列表
            $order_list             = $this->model->yundian_order_list($data);

            $pageNum                = $data['pageNum'];                             //当前页
            $Count                  = $order_list['data'][1];                       //数据总条数
            $pageCount              = ceil($Count/20);                        //数据总页数
            if($data['type']  == 0){
                //获取平台订单数量
                $platform_num       = $Count;
                //获取自营订单数量
                $my_num             = $this->model->get_order_num($data,1);
            }elseif($data['type']  == 1){
                unset($data['name']);
                unset($data['user_id']);
                //获取平台订单数量
                $platform_num       = $this->model->get_order_num($data,0);
                //获取自营订单数量
                $my_num             = $Count;
            }
            include('view/yundian/yundian_order_list.php');
    }

    /*
     版权信息:  秘密信息
     功能描述：云店奖励——订单日志
     开 发 者：taojin
     开发日期： 2018-04-09
     重要说明：参数type: 0平台订单 1自营订单
     方法  yundian_order_log  返回errcode(int) errmsg(string) data(array('res23'=>$data1,'res2'=>$data2,'res24'=>$data3))
      */
    function yundian_order_log(){
        $customer_id            = $this->customer_id;
        $batchcode              = $_GET['batchcode']?$_GET['batchcode']:'';     //订单号
        $user_id                = $_GET['user_id']?$_GET['user_id']:'';         //用户id
        $log                    = $this->model->yundian_order_log($batchcode,$user_id);
        $log_list               = $log['data'];
        $res23                  = $log_list['res23'];
        $res2                   = $log_list['res2'];
        $res24                  = $log_list['res24'];
        $o_batchcode            = $batchcode;
        include('view/yundian/yundian_order_log.php');
    }
