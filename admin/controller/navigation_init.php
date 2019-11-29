<?php

class control_navigation_init extends control_base
{
    public $model;
    
    function __construct()
    {
        parent::__construct();  
        
        // parent::check_login();

        require_once('model/navigation_init.php');
        $this->model = new model_navigation_init($this->customer_id);

    }

    //导航模板初始化数据脚本
    public function initialize_template(){
        //查询出平台的所有商家id
        $id = $this->model->select_all_customer();
        for($b='0';$b<count($id);$b++){
            $customer_id = $id[$b]['customer_id'];
            echo "</br>初始化脚本商家id:[".$customer_id."]---------------------------------</br>";
            $data = $this->model->navigaton_initialize($customer_id);
        }
    }

    //底部菜单初始化数据脚本
    public function initialize_bottom(){
        //查询出平台的所有商家id
        $id = $this->model->select_all_customer();
        for($b='0';$b<count($id);$b++){
            $customer_id = $id[$b]['customer_id'];
            echo "</br>初始化脚本商家id:[".$customer_id."]---------------------------------</br>";
            $data = $this->model->bottom_initialize($customer_id);
        } 
    }

    //自定义底部菜单初始化数据脚本
    public function initialize_custom(){
        //查询出平台的所有商家id
        $id = $this->model->select_all_customer();
        for($b='0';$b<count($id);$b++){
            $customer_id = $id[$b]['customer_id'];
            echo "</br>初始化脚本商家id:[".$customer_id."]---------------------------------</br>";
            $data = $this->model->custom_initialize($customer_id);
        } 
        
    }

}
?>