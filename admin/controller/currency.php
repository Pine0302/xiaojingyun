<?php

/*
版权信息:  秘密信息
功能描述：零钱转换系统
开 发 者：陶晋
开发日期： 2017-10-12
重要说明：无
 */
class control_currency extends control_base
{
    public $model;

    function __construct()
    {
        parent::__construct();
		//登录校验
		parent::check_login();		
		//登录校验 End
		
        require_once('model/currency.php');
        $this->model = new model_currency();
        require_once('model/common.php');
        $this->model_common = new model_common();
        $data['data']=file_get_contents('php://input', true);
        //$data = $_REQUEST['data'];
        $this->parmdata  = json_decode($data['data'],true);
    }

     /*
      * 零钱转换配置页面
      * 传入参数customer_id
      * Author: zhangqiusong $
      * 2017-10-13
      */
    function money_conversion(){
        $customer_id       = $this->customer_id;
        $result            = $this->model->get_exchange_t($customer_id);
        $result2           = $this->model->get_extend_t($customer_id);
        //查询模板颜色
//        $theme             = "blue";
        $theme             = $this->model_common->find_theme($customer_id);
        // var_dump($result);
        // var_dump($result2);
        include ('view/currency/conversion.htm');
    }

     /*
      * 修改零钱转换配置
      * 传入参数customer_id
      * Author: zhangqiusong $
      * 2017-10-13
      */
     function update_conversion(){
        $data['is_open']            = $_POST['is_open']?$_POST['is_open']:false;
        $data['isvalid']            = true;
        $data['customer_id']        = $this->customer_id;
        $data['switch_type']        = $_POST['switch_type']?$_POST['switch_type']:"1";
        $data['remark']             = $_POST['remark']?$_POST['remark']:"";
        $data['createtime']         = date('Y-m-d H:i:s',time());
        $update = $this->model->update_exchange_t($data);
        $data2['customer_id']       = $this->customer_id;
        $data2['multiple_type']     = $_POST['multiple_type']?$_POST['multiple_type']:"";
        $data2['multiple_diy']      = $_POST['multiple_diy']?$_POST['multiple_diy']:"";
        $data2['minimum']           = $_POST['minimum']?$_POST['minimum']:"-1";
        $data2['conversion_ratio']  = $_POST['conversion_ratio']?$_POST['conversion_ratio']:"";
        $data2['type']              = $_POST['type']?$_POST['type']:"1";
        $data2['isvalid']           = true;
        $data2['createtime']         = date('Y-m-d H:i:s',time());
        if($data2['conversion_ratio']<0.01) json_out(array('errcode'=>401,'errmsg'=>'转换比例最小为0.01'));
        $update2 = $this->model->update_extend_t($data2);
        if ($update != false && $update2 != false) {
            $result = true;
        }else{
            $result = false ;
        }
        $result != false ? $res = array('errcode' => 0,'errmsg'=>'保存成功') : $res = array('errcode' => 400,'errmsg'=>'保存失败');
        json_out($res);
     }

     /*
      * 新增零钱转换配置
      * 传入参数customer_id
      * Author: zhangqiusong $
      * 2017-10-13
      */
     function add_conversion(){
        $data['is_open']            = $_POST['is_open']?$_POST['is_open']:false;
        $data['isvalid']            = true;
        $data['customer_id']        = $this->customer_id;
        $data['switch_type']        = $_POST['switch_type']?$_POST['switch_type']:"1";
        $data['remark']             = $_POST['remark']?$_POST['remark']:"";
        $data['createtime']         = date('Y-m-d H:i:s',time());
        $add= $this->model->add_exchange_t($data);
        $data2['customer_id']       = $this->customer_id;
        $data2['multiple_type']     = $_POST['multiple_type']?$_POST['multiple_type']:"";
        $data2['multiple_diy']      = $_POST['multiple_diy']?$_POST['multiple_diy']:"";
        $data2['minimum']           = $_POST['minimum']?$_POST['minimum']:"-1";
        $data2['conversion_ratio']  = $_POST['conversion_ratio']?$_POST['conversion_ratio']:"";
        $data2['type']              = $_POST['type']?$_POST['type']:"";
        $data2['isvalid']           = true;
        $data2['createtime']         = date('Y-m-d H:i:s',time());
        $add2= $this->model->add_extend_t($data2);
        if ($add != false && $add2 != false) {
            $result = true;
        }else{
            $result = false ;
        }
        $result != false ? $res = array('errcode' => 0,'errmsg'=>'保存成功') : $res = array('errcode' => 400,'errmsg'=>'保存失败');
        json_out($res);
     }

     /*
      * 零钱转换日志查看
      * 传入参数customer_id
      * Author: zhangqiusong $
      * 2017-10-16
      */
     function conversion_log(){
        $data['customer_id']  = $this->customer_id;      //商家ID

        $data['page']         = $_GET['pagenum']?$_GET['pagenum']:1;
        $data['page_size']    = (int)20;

        $user_id = $_GET['user_id'];
        $switch_type       = "1";

        $search_key = array('user_id'=>$user_id,'type'=>$switch_type);
        $data['search_key'] = json_encode($search_key);
        //判断数据是否安全
        if(empty($data['customer_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
        }
        if(empty($data['page_size']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'page_size参数丢失！'));
        }else if($data['page_size'] < 1){
            json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
        }
        $data2 = $this->model->get_conversion_log($data);
        
//        $theme = "blue";
        //查询模板颜色
         $theme             = $this->model_common->find_theme($this->customer_id);
        include('view/currency/conversion_log.htm');
     }
}
