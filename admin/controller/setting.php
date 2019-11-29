<?php


class control_setting extends control_base 
{
	var $model;

	function __construct() 
	{	
        parent::__construct();
		require_once('model/setting.php');
        require_once(ROOT_DIR . 'mshop/admin/model/slyder_adventures.php');
		$this->model = new model_setting();
        $this->model_slyder = new model_slyder_adventures();
    }

	/* 
     * zhoumuwang
     * 签到设置
	*/
    function sign_setting()
    {
    	extract( $this->parmdata);
    	$data['cust_id']    	 = $cust_id;   
    	$data['sign_onoff'] 	 = $sign_onoff;       //签到开关
    	$data['agreement_onoff'] = $agreement_onoff;  //协议开关
    	$data['sign_agreement']  = $sign_agreement;   //协议描述
        $data['suspend_onoff']   = $suspend_onoff;    //悬浮开关
    	$data['sign_json']  	 = json_encode($sign_json);        //配置json

    	if(empty($data['cust_id']))
    	{
    		json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
    	}

    	$result = $this->model->change($data);
    	$result != false ? $res = array('errcode' => 0,'errmsg'=>'提交成功') : $res = array('errcode' => 400,'errmsg'=>'提交失败');
    	json_out($res);
    }


    /* 
     * zhoumuwang
     * 读取签到设置
    */
    function sign_read()
    {
        
        $result  = $this->model->sign_read($this->parmdata);
        
        json_out($result);
    }

	/* 
     * djy
     * 积分设置
	*/
    function integral_setting()
    {
    	

    	$data['cust_id']    	 = $_POST['cust_id'];   
    	$data['shop_onoff'] 	 = $_POST['shop_onoff'];       //购物积分开关
    	$data['reward_onoff'] = $_POST['reward_onoff'];  //奖励积分开关
    	$data['basic_json']  	 = $_POST['basic_json'];        //积分配置json

    	//模拟数据
    	$data['cust_id']    	 = 3243;
    	$data['shop_onoff'] 	 = 1;       //购物积分开关
    	$data['reward_onoff'] = 1;  //奖励积分开关
    	$basic_json = array(
                            'integral_name'       => '商城积分', //积分命名
                            'gift_set_type'       => '1', //赠送设置类型：1、按产品现价比例，2、按固定积分
                            'gift_set_value'      => '50', //赠送设置的值：类型为1则为比例，类型为2则为积分
    						'conversion_ratio_integral'       => '1000', //积分兑换比例，积分
                            'conversion_ratio_price'       => '1', //积分兑换比例，金额
                            'is_commission'       => '1', //是否参与分佣，0否，1是
                            'clear_integral_time'       => '2017-8-23 15:30', //清除积分时间
                            'clear_integral_notice'             =>array(
        						'time1'            => array(//时间段1配置
        												'ahead_days'    => '10',	//开始时间
        												'notice_time'      => '2017-8-23 18:30' //结束时间
        											 ),
        						'time1'            => array(//时间段1配置
        												'ahead_days'    => '10',	//开始时间
        												'notice_time'      => '2017-8-23 18:30' //结束时间
        											 )
                            ),
                            'focus_reward'       => '5', //新用户关注公众号奖励
                            'referrer_focus_reward'       => '3', //新用户关注公众号 推荐人奖励
                            'bind_phone_reward'       => '5', //新用户绑定手机奖励
                            'referrer_bind_phone_reward'       => '3', //新用户绑定手机 推荐人奖励
                            'first_order_reward_type'       => '1', //首单奖励积分类型：1、按产品现价比例，2、按固定积分
                            'first_order_reward'       => '1', //首单奖励积分：类型为1则为比例，类型为2则为积分
                            'referrer_first_order_reward'       => '1' //首单奖励积分：类型为1则为比例，类型为2则为积分 推荐人奖励

    					  );

    	$data['basic_json']  = json_encode($basic_json, JSON_UNESCAPED_UNICODE);   //积分配置json
    	if(empty($data['cust_id']))
    	{
    		json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
    	}

    	$result = $this->model->change($data);
    	$result != false ? $res = array('errcode' => 0,'errmsg'=>'提交成功') : $res = array('errcode' => 400,'errmsg'=>'提交失败');
    	json_out($res);
    }    
    
    /*
	* 获取积分设置数据
	* $Author: djy $
	* 2017-08-28  $
    */
    function integral_setting_details()
    {
        $data                 = $this->parmdata;
        $data['cust_id']      = 3243;      //商家ID
        
    	$result = $this->model->integral_setting_details($data);
        if($errcode == 0){
            $result['data']['basic_json'] = json_decode($result['data']['basic_json'],TRUE); 
        }
    	json_out($result);
    }


    /*获取商城皮肤*/
    function get_shop_skin(){
        $customer_id =  $this->customer_id;
        $skin   = $this->model->get_shop_skin($customer_id);
        // var_dump($skin);
        if(!empty($skin)){
            $result['skin'] = $skin;
        }else{
            $result['skin'] = 'Green';
        }

        json_out($result);
    }





}
