<?php


    /*
     * $explain  : 店头背景默认及修改
     * $Author   : zjj-v397
     * $time     : 2018-04-09 
     * $message  : 公开
     */
    function Background_submission(){
        $data = $_POST;
        if(empty($data['pathArray'])){
            json_out(array('errcode' => 400,'errmsg'=>'pathArray参数丢失！'));
        }
        $result = $this->model->setting_of_store($data);
        json_out($result);

    }

    /*
     * $explain  : 店头背景编辑页面
     * $Author   : zjj-v397
     * $time     : 2018-04-09 
     * $message  : 公开
     */
    function background_of_store(){
        parent::check_login(); 
        $customer_id = $this->customer_id;

        $theme  = $this->model_common->find_theme($customer_id);
        $upfileUrl = $this->model->select_setting_of_store(1);
        include('view/yundian/background_of_store.php');
    }   
