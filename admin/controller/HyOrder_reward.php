<?php
/**
 * User: chy
 * Date: 2018/5/10
 * Time: 14:32
 * Explain: 订单奖励业务类
 */


require ('HyBase.php');
require ('HyComfun.php');
require ('HyBaseInterface.php');
 
class control_HyOrder_reward extends HyBase implements HyBaseInterface
{
    public  $data;
    public  $model;
    public  $shop;
    #使用HyComfun
    use HyComfun;
    public function __construct()
    {
       parent::__construct();

        //登录校验
        parent::check_login();
        //登录校验 End

        #引入模型类
        require_once('model/order_reward.php');
        $this->model = new model_order_reward();

        require_once('model/weixin_commonshops.php');
        $this->shop = new model_weixin_commonshops();



    }


    public function setting(){
        $customer_id = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        if(empty($customer_id) || $customer_id <0){
            return json_out (['errcode' => 401, 'msg' => '$customer_id 缺失']);
        }
        $res = $this->shop->get_setting($customer_id,'issell,reward_level');
        //var_dump($res);
        /* 查询商城皮肤 */
        //require('../../weixinpl/mshop/select_skin.php');

        include ('view/order_reward/setting.htm');
    }

    public function statistics(){	
        $customer_id = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        include ('view/order_reward/statistics.htm');
    }

    public function poll_log(){
        $customer_id = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        include ('view/order_reward/poll_log.htm');
    }

    public function poll_order(){
        $customer_id = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        include ('view/order_reward/poll_order.htm');
    }

    /*
       * 后台获取配置
       * @xxx：
       * @xxx：
       * @return：返回结果
       */
    public function admin_get(){

        $customer_id = $this->customer_id;
        if(empty($customer_id) || $customer_id <0){
            return json_out (['errcode' => 401, 'msg' => '$customer_id 缺失']);
        }
        $res = $this->model->get_setting($customer_id);
        if (!$res) {
            $result = [
                'errcode' => 400,
                'msg' => '数据为空',
            ];
            return json_out($result);
        }
        $result = [
            'errcode' => 0,
            'msg' => 'success',
            'data' => $res,
        ];

        return json_out($result);

    }

    /*函数说明：
    * 订单奖励统计
    * @xxx：
    * @xxx：
    * @return：
    */
    public function admin_get_poll(){

        $customer_id = $this->customer_id;
        $op_type			        = $this->data['op_type'];
        $page			            = $this->data['page'];
        $pageNum			        = $this->data['pageNum'];
        $search_batchcode			= $this->data['search_batchcode'];
        $search_starttime			= $this->data['search_starttime'];
        $search_endtime			    = $this->data['search_endtime'];
        $type			            = $this->data['type'];
        $data_type			        = $this->data['data_type'];
		
		if(!empty($search_batchcode) || !empty($search_starttime) || !empty($search_endtime)){
			$data_type = null;
			
		}
		
        $condition = [];
        $condition['search_batchcode']  = $search_batchcode;
        $condition['search_starttime']  = $search_starttime;
        $condition['search_endtime']    = $search_endtime;
        $condition['status']               = $type?$type:0;
		
		switch($data_type){
			
			case 'today':	//当天
				$condition['createtime'] = ' and date(poll.createtime) = curdate()';  
			break;			
			case 'yesterday':  //昨日
				$condition['createtime'] = ' and  (to_days(now())-to_days(poll.createtime)) = 1';
			break;
			case 'week':	//近7天	
				$condition['createtime'] = ' and  date_sub(curdate(), INTERVAL 7 DAY) <= date(poll.createtime) ';   //包含今日
			break;
			case 'month':	//近30天
				$condition['createtime'] = ' and  date_sub(curdate(), INTERVAL 30 DAY) <= date(poll.createtime)' ;   //包含今日
			break;
			
			
		}
		
		
        if( $op_type == 1 ) {	//统计数量
            $result = $this->model->order_poll_count($customer_id,$condition);
        } else {
            $result = $this->model->order_poll($customer_id,$condition,$page,$pageNum);
        }

        //var_dump(json_encode($result));
        json_out($result);


    }


    /*函数说明：
    * 订单奖励统计
    * @xxx：
    * @xxx：
    * @return：
    */
    public function admin_get_poll_order(){

        $customer_id = $this->customer_id;
        $op_type			        = $this->data['op_type'];
        $page			            = $this->data['page'];
        $pageNum			        = $this->data['pageNum'];
        $search_batchcode			= $this->data['search_batchcode'];

        $condition = [];
        $condition['search_batchcode']  = $search_batchcode;
        if( $op_type == 1 ) {	//统计数量
            $result = $this->model->order_poll_order_count($customer_id,$condition);
        } else {
            $result = $this->model->order_poll_order($customer_id,$condition,$page,$pageNum);
        }

        //var_dump(json_encode($result));
        json_out($result);


    }


    /*函数说明：
    * 订单奖励执行的记录
    * @xxx：
    * @xxx：
    * @return：
    */
    public function admin_get_poll_log(){

        $customer_id = $this->customer_id;
        $op_type			        = $this->data['op_type'];
        $page			            = $this->data['page'];
        $pageNum			        = $this->data['pageNum'];
        $search_batchcode			        = $this->data['search_batchcode'];


        $condition = [];
        $condition['search_batchcode']  = $search_batchcode;

        if( $op_type == 1 ) {	//统计数量
            $result = $this->model->order_poll_log_count($customer_id,$condition);
        } else {
            $result = $this->model->order_poll_log($customer_id,$condition,$page,$pageNum);
        }

        //var_dump(json_encode($result));
        json_out($result);


    }


    /*
     * 后台保存配置
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function admin_save(){

        $customer_id = $this->customer_id;
        if(empty($customer_id) || $customer_id <0){
            json_out (['errcode' => 401, 'msg' => 'xxx 缺失']);
        }
        $res = $this->model->get_setting($customer_id);
        $condition = [];
        $condition['customer_id'] = $customer_id;
        $condition['isvalid'] = true;
        $condition['json'] = json_encode($this->data['json']) ;
        $condition['type'] = $this->data['type'];
        $condition['isopen'] = $this->data['isopen'];
        $condition['createtime'] = date('Y-m-d H:i:s',time());

        if ($res) {
            $res = $this->model->update_setting($customer_id,$res['id'],$condition);
        } else {
            $res = $this->model->add_setting($customer_id,$condition);
        }
        $result = [];
        $result['errcode'] = 0;
        $result['msg'] = 'success';
        return json_out($result);

    }



    /*
     * 后台删除配置
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function admin_del(){}
    /*
     * 前台获取配置
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_setting(){}
    /*
     * 前台获取额外数据
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_get(){}
    /*
     * 前台算法
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_cal(){}
    /*
     * 前台主要业务
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_main(){}
    /*
     * 前台设计的表操作
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_sql(){}
    /*
      * 前台的表操作
      * @xxx：
      * @xxx：
      * @return：返回结果
      */
    public function busses_execute_sql(){}
    /*
     * 前台设计的日志
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_log($str){}
}


