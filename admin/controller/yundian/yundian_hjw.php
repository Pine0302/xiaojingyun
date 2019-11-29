<?php

   /*
    * 店主审核列表
    * $Author: hjw$
    * $2018-04-04  $
    * 参数： array('search_key'=>['user_id','user_name','identity_id','status','begin_time','end_time'],'page','page_size','is_ajax')
    *        is_ajax 1 ajax请求 0 页面请求,search_key 搜索条件 ，page 当前页数 ，page_size 每页条数
    *
    */
    function shopkeeper_review_list()
    {
        $post = $_POST;
        extract($post);
        $customer_id_en      = $this->customer_id_en;
        $customer_id         = $this->customer_id;
        $data['customer_id'] = $customer_id;
        $data['page']        = $page;
        $data['page_size']   = $page_size;
        $theme               = $this->model_common->find_theme($data['customer_id']);
        //判断是否为AJAX请求
        if($is_ajax != 1)
        {
           $identity_arr = $this->model->get_identity($data['customer_id']);
           include("view/yundian/shopkeeper_review_list.html");
        }
        else
        {
            //判断数据是否安全
            if(empty($data['customer_id']))
            {
                json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
            }
            if(empty($search_key))
            {
                json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
            }
            if(empty($data['page']) || $data['page'] < 1)
            {
                json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
            }
            if(empty($data['page_size']) || $data['page_size'] < 1){
                $data['page_size'] = 20;//每页数量
            }
            $data = array_merge($data,$search_key);
            $result = $this->model->shopkeeper_review_list($data);
            json_out($result);
        }
    }

  /*
    * 店主审核通过
    * $Author: hjw$
    * $2018-04-08  $
    * 参数：more： 0 单独审核 1 批量审核
    *       more = 0 时:array('more','tequan_id','user_id','profit_shop','self_reware','expire_time') 
    *       more = 0 时（中文备注）: array（'单独','申请的特权ID','用户ID','身份奖励','自营收入','默认到期时间'）
    *       more = 1 时：array('id') //id字符串 格式 '1,2,3'
    */
    function review_pass(){
        $data  = $_POST;
        $data['customer_id']       = $this->customer_id;
        $data['customer_id_en']    = $this->customer_id_en;
        if(empty($data['customer_id'])){
            json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
        }
        $result = $this->model->review_pass($data);
        json_out($result);
    }

   /*
    * 店主审核驳回
    * $Author: hjw$
    * $2018-04-08  $
    * 参数：more 1批量 0单独
    *       more = 0 时:array('id','reason')
    *       more = 0 时（中文备注）: array（'驳回的ID','驳回原因'）
    *       more = 1 时：array('id','k_id') //id字符串 格式 '1,2,3' ,k_id:weixin_yundian_keeper 的ID组
    */
    function reject_review(){
        $data  = $_POST;
        $data['customer_id'] = $this->customer_id;
        if(empty($data['customer_id'])){
            json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
        }
        $result = $this->model->reject_review($data);
        json_out($result);

    }  
