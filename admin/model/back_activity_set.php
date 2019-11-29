<?php

class model_back_activity_set{
	
	var $db;

	public function __construct() 
	{
        $this->db = DB::getInstance();
    }	
	
	/*
	 *	查询活动优先级设置
	 *	$Author:	lqh
	 *	2017-9-4
    */
    public function select_activity_priority($data = array())
    {
		$return_msg = array();
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = 'success';
		$return_msg['data']    = array();
		
    	if(empty($data['customer_id'])) return array('errcode' => 400,'errmsg'=>'缺少参数customer_id');
		
		$where  = "";	//查询条件
		$where  = " isvalid=true AND customer_id=".$data['customer_id'];
		if( !empty($data['activity_type']) ){
			$where .= " AND activity_type='".$data['activity_type']."'";
		}
		$query  = "SELECT id,sort,activity_type,count_down FROM ".WSY_SHOP.".weixin_commonshop_activity_priority WHERE ".$where." ORDER BY id DESC";
		$result = $this->db->getAll($query);
		if($result){
			$return_msg['data'] = $result;
		}else{
			return array('errcode' => 404,'errmsg'=>'活动优先级查询为空！');
		}
		
		return $return_msg;
    }
	
	/*
	 *	查询客户功能开关
	 *	$Author:	lqh
	 *	2017-9-4
    */
    public function select_active_columns($data = array())
    {
		$return_msg = array();
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = 'success';
		$return_msg['data']    = array();
		
    	if(empty($data['customer_id'])) return array('errcode' => 400,'errmsg'=>'缺少参数customer_id');

		$query="select count(1) as rcount from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id='".$data['customer_id']."' and c.sys_name='".$data['activity_name']."'";

		$result = $this->db->getRow($query);
		if($result){
			$return_msg['data'] = $result;
		}else{
			return array('errcode' => 404,'errmsg'=>'用户功能表查询为空！');
		}
		
		return $return_msg;
    }
	
	/*
	 *	初始化并查看优先级设置
	 *  @param int $param["customer_id"]  商家编号
	 *	$Author:	lqh
    */
	public function show_activity_priority($param)
	{
		$return_msg = array();
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = 'success';
		$return_msg['data']    = array();
		
		if(empty($param['customer_id'])) return array('errcode' => 400,'errmsg'=>'缺少参数customer_id');
		
		$condition['customer_id']   = $param['customer_id'];
		
		//活动类型['拼团','积分','限购','砍价','众筹']
		//$activity_type_arr[] = array('activity_type'=>'collage','activity_name'=>'拼团活动');
		$activity_type_arr[] = array('activity_type'=>'integral','activity_name'=>'积分管理');
		$activity_type_arr[] = array('activity_type'=>'restricted','activity_name'=>'限时活动');
		//$activity_type_arr[] = array('activity_type'=>'bargain','activity_name'=>'砍价活动');
		//$activity_type_arr[] = array('activity_type'=>'crowdfund','activity_name'=>'众筹活动');
		$count_type = count($activity_type_arr);
		
		for($i=0;$i<$count_type;$i++){
			
			$condition['activity_type'] = $activity_type_arr[$i]['activity_type'];
			$condition['activity_name'] = $activity_type_arr[$i]['activity_name'];
			//查是否设置了活动优先级
			$activity = $this->select_activity_priority($condition);
			//查用户是否开通了此功能
			$customer_funs = $this->select_active_columns($condition);
			$status = 0;	//用户是否拥有此活动功能 0；隐藏 1：显示
			
			if( $activity['errcode'] != 0 ){
				//初始化插入数据
				$data = array(
					'customer_id'   => $param['customer_id'],
					'activity_type' => $activity_type_arr[$i]['activity_type'],
					'count_down'    => 2,
					'sort'          => 1,
					'createtime'    => date('Y-m-d H:i:s',time()),
					'isvalid'       => true,
				);
				$result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_activity_priority',$data, 'insert') ;
				$act_id = $this->db->insert_id();
				if( $customer_funs['errcode'] == 0 && $customer_funs['data']['rcount']>0 ){
					$status = 1;				
				}
				$data['id']     = $act_id;
				$data['status'] = $status;
				array_push($return_msg['data'],$data);
			}else{
				if( $customer_funs['errcode'] == 0 && $customer_funs['data']['rcount']>0 ){
					$status = 1;					
				}
				$activity['data'][0]['status'] = $status;
				array_push($return_msg['data'],$activity['data'][0]);
			}
			
		}
		
		return $return_msg;	
	}
	
	/*
	 *	更新优先级设置
	 *	$Author:	lqh
	 *	@param array $condtion 条件
	 *	@param array $value    更新值
    */
	public function update_activity_priority($condtion=array(),$value=array()){
		$where = "";
		if( $condtion['customer_id'] > 0 ){
			$where .= "customer_id=".$condtion['customer_id'];
		}
		if( $condtion['id'] > 0 ){
			$where .= " AND id=".$condtion['id'];
		}

		$result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_activity_priority', $value, 'update',$where) ;

   		return $result;
	}
}