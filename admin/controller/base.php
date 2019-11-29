<?php
/*
* 套餐相关
* $Author: 痴心绝对 $
* 2015-01-11  $
*/

class control_base{
	var $model;
	public $parmdata;			//统一请求参数数组
	public $system_code;		//api识别码	
	public $customer_id;		//商家id（解密后）	
	public $customer_id_en;		//商家id（加密后）
	public $localdebug = true;	//vue本地开发,正式版本记得屏蔽
	
    public function __construct() {
	
		#开发测试接口用	
		$data 	= $_POST;
		
		#vue获取参数方式
		if (empty($data)) {

			$data = file_get_contents('php://input', true);
			$data = json_decode($data);
			$this->parmdata = object_to_array($data->data);	//对象转数组
			$this->parmsystem_code 	= $data->system_code;
		} else {
			
			$this->parmdata = object_to_array(json_decode($data['data']));	//对象转数组

			$this->parmsystem_code 	= $data['system_code'];

		}
	
		//兼容GET传过来
		if($_GET['data'])
		{
			$this->parmdata = object_to_array(json_decode($_GET['data']));	//对象转数组
		}	
		#获取全局的customer_id

		global $customer_id;
		global $customer_id_en;
		$this->customer_id 		   = $customer_id;
		$this->customer_id_en 	   = $customer_id_en;

		$this->parmdata['cust_id'] = $customer_id;

		//过滤校验
		$this->global_checkout($data);
		//过滤校验 End
		

    }
	
	//全局校验先放这里
	function global_checkout($data = array()){
        
        $data = object_to_array($data);
        
    /*    
         if(!isset($data['system_code']))
    	{
    		json_out(array('errcode' => 600,'errmsg'=>'system_code不存在！'));
    	}
     */   
     //    if(!isset($data['data']))
    	// {
    	// 	json_out(array('errcode' => 600,'errmsg'=>'data不存在！'));
    	// }
        
        
        
		if(empty($this->customer_id ))
    	{
    		json_out(array('errcode' => 600,'errmsg'=>'customer_id参数丢失！'));
    	}
	}
	//全局校验先放这里 End
	
	//登录校验（后台）
	protected function check_login(){                  
		if(empty($_SESSION["C_id"])){
    		json_out(array('errcode' => 100,'errmsg'=>'登录已经超时，请重新登录！'));
    	}

		if($_SESSION["C_id"] != $this->customer_id){
			json_out(array('errcode' => 101,'errmsg'=>'非法登录，登录信息异常！'));
		}		
		
	}	
	//登录校验（后台） End



}
