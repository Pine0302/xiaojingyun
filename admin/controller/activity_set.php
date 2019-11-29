<?php


class control_activity_set extends control_base 
{
	var $model;
	
	function __construct() 
	{
		parent::__construct();
		require_once('model/back_activity_set.php');
		$this->model = new model_back_activity_set();
		
		parent::check_login();
        $data['data']=file_get_contents('php://input', true);
		//$data = $_REQUEST['data'];
		$this->parmdata  = json_decode($data['data'],true);
		
    }
	
	function index(){
		echo 123;
	}
	
	function back_activity_set(){
		$customer_id = $this->customer_id;
		$data['customer_id'] = $customer_id;
		$result = $this->model->show_activity_priority($data);
		$priority_arr = $result['data'];
    	//json_out($result);
		$theme = "blue";
		include('view/restricted_purchase/activitySet.htm');
	}
	
	function update_activity_set(){
		$type = $_GET['type'];
		$id = $_GET['id'];
		
		$condition["customer_id"] = $this->customer_id;
		if($_GET['id']>0){
			$condition["id"] = $_GET['id'];
		}
		$value = array();
		if( $type == 1 ){
			$value["count_down"] = $_GET['val'];
		}else if( $type == 2 ){
			$value["sort"] = $_GET['val'];
		}else{
			$return_msg = array('errcode' => 400,'errmsg'=>'type参数错误');
			json_out($return_msg);
		}
	
		$result = $this->model->update_activity_priority($condition,$value);
		$result != false ? $return_msg = array('errcode' => 0,'errmsg'=>'更新成功') : $return_msg = array('errcode' => 400,'errmsg'=>'更新失败');
		json_out($return_msg);
	}


	
}
