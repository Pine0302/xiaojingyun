<?php

    /*
    * 云店后台操作日志
    * $Author: cjj
    * $2018-04-08  $
    * $data=['customer_id','remark'];  //需要传入的数据,配置参数json的从数据库中查找    customer_id //商家ID   remark //备注
    */
    function admin_yundian_log($data = array()){
        $param['customer_id']     = $this->customer_id;
//        $param['remark']   = '修改配置';
        $res = $this->model->save_admin_yundian_log($param);

        if(!empty($res)){
            json_out(array('errcode' => 0,'errmsg'=>'操作成功！'));
        }else{
            json_out(array('errcode' => 400,'errmsg'=>'操作失败，请重新操作！'));
        }
    }

    /*
     * 后台操作日志
     * author：cjj
     */
    function yundian_setting_log(){
        $customer_id = $this->customer_id;
        $theme       = $this->model_common->find_theme($customer_id);

        $param['customer_id']     = $this->customer_id;
        $param['start_time']      = $_REQUEST['start_time']?$_REQUEST['start_time']:-1;
        $param['end_time']        = $_REQUEST['end_time']?$_REQUEST['end_time']:-1;
        $param['word']             = $_REQUEST['word']?$_REQUEST['word']:'';
        $pageNum                    = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页

        $res       = $this->model->yundian_setting_log($param);//获取店主列表
        $data      = $res['res'];
        $pageCount = $res['pageCount'];
        include("view/yundian/yundian_setting_log.php");
    }

