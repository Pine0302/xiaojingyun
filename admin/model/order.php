<?php


/*
	数据库操作示例:

	$data = $this->db->getAll ($sql);
	$data = $this->db->getOne ($sql);
	$data = $this->db->getRow ($sql);
	$data = $this->db->getCol ($sql);
	$data = $this->db->autoExecute('users', array('rank'=>1), 'insert') ;
	$data = $this->db->autoExecute('users', array('rank'=>1), 'update',"user_id = '$uid'") ;
	$data = $this->db->query("select * from users where rank='12'") ;
	$this->db->query("delete from users where rank='12'") ;

	//事务处理
	$this->db->tran_begin();
	try{
		//查用户积分加排他锁
		$data = $this->db->getRow ("select points from users where uid=122 for update");
		//查用户日志加排他锁
		$data = $this->db->getRow ("select log_id from users_log where uid=122 for update");
		//插入日志
		$this->db->autoExecute('users_log', array('points'=>-100,'uid'=>122), 'insert') ;
		//更新用户表总积分
		$this->db->autoExecute('users', array('uid'=>122), 'update',"points = points-100") ;
	} catch(Exception $e){
		$this->db->tran_rollback();
		echo '系统错误，请稍后重试'; exit;
	}
	$this->db->tran_commit();
*/


class model_order{
	var $db;

	function __construct() 
	{
        $this->db = DB::getInstance();
    }

    /*
	 * 判断订单是否已发货
	 * $Author: hzq $
	 * 2018-01-13  $
     */
	public function select_order_status($batchcode="",$customer_id=""){
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = "";
		if($batchcode == ""){
			$return_msg['errcode'] = 1;
			$return_msg['errmsg']  = "缺少参数：订单号！";
		}
		if($customer_id == ""){
			$return_msg['errcode'] = 1;
			$return_msg['errmsg']  = "缺少参数：商家号！";
		}
		$sql = "select sendstatus from weixin_commonshop_orders where isvalid = true and batchcode ='".$batchcode."' and customer_id = '".$customer_id."'";
		$data = $this->db->getRow($sql);
		$return_msg['sendstatus'] = $data['sendstatus'];	
		return $return_msg;
		
	}
	
	/*
	 * 判断订单是否供应商订单
	 * $Author: hzq $
	 * 2018-01-16  $
     */
	public function select_order_supply_id($batchcode="",$customer_id=""){
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = "";
		if($batchcode == ""){
			$return_msg['errcode'] = 1;
			$return_msg['errmsg']  = "缺少参数：订单号！";
		}
		if($customer_id == ""){
			$return_msg['errcode'] = 1;
			$return_msg['errmsg']  = "缺少参数：商家号！";
		}
		$sql = "select supply_id from weixin_commonshop_orders where isvalid = true and batchcode ='".$batchcode."' and customer_id = '".$customer_id."'";
		$data = $this->db->getRow($sql);
		$return_msg['supply_id'] = $data['supply_id'];	
		return $return_msg;
	}
	
	/*
	 * 判断订单是否代发订单
	 * $Author: hzq $
	 * 2018-01-16  $
     */
	public function select_order_send_id($batchcode="",$customer_id=""){
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = "";
		if($batchcode == ""){
			$return_msg['errcode'] = 1;
			$return_msg['errmsg']  = "缺少参数：订单号！";
		}
		if($customer_id == ""){
			$return_msg['errcode'] = 1;
			$return_msg['errmsg']  = "缺少参数：商家号！";
		}
		$sql = "select is_sendorder from weixin_commonshop_orders where isvalid = true and batchcode ='".$batchcode."' and customer_id = '".$customer_id."'";
		$data = $this->db->getRow($sql);
		$return_msg['is_sendorder'] = $data['is_sendorder'];	
		return $return_msg;
	}
	
	/*
	 * 判断订单的归属
	 * $Author: hzq $
	 * 2018-01-16  $
     */
	public function select_send_store_id($batchcode="",$customer_id=""){
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = "";
		if($batchcode == ""){
			$return_msg['errcode'] = 1;
			$return_msg['errmsg']  = "缺少参数：订单号！";
		}
		if($customer_id == ""){
			$return_msg['errcode'] = 1;
			$return_msg['errmsg']  = "缺少参数：商家号！";
		}
		$sql = "select final_proxy_id,send_type from system_send_order where isvalid = true and order_id ='".$batchcode."' and customer_id = '".$customer_id."'";
		$data = $this->db->getRow($sql);
		$return_msg['final_proxy_id'] = $data['final_proxy_id'];
		$return_msg['send_type'] = $data['send_type'];		
		return $return_msg;
	}

}
