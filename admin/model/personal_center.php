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


class model_personal_center{
	var $db;

	function __construct() 
	{
        $this->db = DB::getInstance();
    }

     /*
	 * 分页查询所有个人中心自定义模板
     * @param   [array]  $data
	            [int]    ['page']  页码
	            [int]    ['customer_id']  商家编号
	 * 作者：lqh
    */
	function select_personal_center_diy_template($data=array())
	{
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = "success";

		$pageSize = 20;				//每页显示数量
		$page     = $data['page'];	//页码
        if($page < 1 || empty($data['page'])){
            $page = 1;
        }
		
		if($data["customer_id"] <=0 || empty($data["customer_id"])){
			$return_msg['errcode'] = 40002;
			$return_msg['errmsg']  = "customer_id参数错误";
			return $return_msg;
		}else{
			$customer_id = $data["customer_id"];
		}
		
		$where    = " isvalid=true and customer_id = ".$customer_id;	//查询条件
		
		//统计一下数据量
		$query_count = "SELECT count(id) as total FROM ".WSY_SHOP.".personal_center_diy_template WHERE {$where}";	//计算数据量
		$count       = $this->db->getRow($query_count);	
		$totalPage   = ceil($count['total']/$pageSize); //总页数 
		$return_msg['totalPage'] = $totalPage;
			
		
		$query    = "SELECT id,is_used,createtime,name,bgcolor,default_img FROM ".WSY_SHOP.".personal_center_diy_template WHERE {$where}";
		$query   .= " ORDER BY is_used DESC,id DESC ";
		
		if( $page != '' ){
			$query .= " LIMIT ".($page-1)*$pageSize.",".$pageSize;
		}else{
            $query .= " LIMIT 0,".$pageSize;
        }
		//echo $query;
    	$result    = $this->db->getAll ($query);
		
		$return_msg['page'] = $page;	
		$return_msg['data'] = $result;
		return $return_msg;
	}


	/*
	 * 查询一行个人中心自定义模板
	 * 作者：lqh
    */
	function find_personal_center_diy_template($id,$customer_id)
	{	
		$sql    = 'SELECT id,is_used,createtime,name,bgcolor,content,default_img FROM '.WSY_SHOP.'.personal_center_diy_template WHERE isvalid=true and id = "'.$id.'" and customer_id = "'.$customer_id.'"';
		$result = $this->db->getRow ($sql);
		return $result;
	}
	
	/*
	 * 更新个人中心自定义模板
	 * 作者：lqh
    */
	function update_personal_center_diy_template($condition=array(),$value=array()){
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = "success";
		
		$where = "";
		if( empty($condition["customer_id"]) ){
			$return_msg['errcode'] = 40002;
			$return_msg['errmsg']  = "customer_id参数错误";
			return $return_msg;
		}else{
			$where .= " customer_id=".$condition["customer_id"];
		}

		if( isset($condition["id"]) ){
			$where .= " and id='".$condition["id"]."'";
		}
		
		if( empty($where) ){
			$return_msg['errcode'] = 40002;
			$return_msg['errmsg']  = "where为空";
			return $return_msg;
		}

		$result = $this->db->autoExecute(WSY_SHOP.'.personal_center_diy_template', $value, 'update', $where) ;
		
		return $return_msg;
	}

	/*
	 * 新增个人中心自定义模板
	 * 作者：lqh
    */
	function insert_personal_center_diy_template($value=array()){
		$result  = $this->db->autoExecute(WSY_SHOP.'.personal_center_diy_template',$value, 'insert') ;
		$temp_id = $this->db->insert_id();
		return $temp_id;
	}
	
	/*
	 * 新增个人中心自定义模板内容
	 * 作者：hzq
    */
	function insert_personal_center_diy_template_content($value=array()){
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = "success";
		
	//	$result = $this->db->autoExecute(WSY_SHOP.'.personal_center_diy_template_content',$value, 'insert') ;
		//事务处理
		$this->db->tran_begin();
		try{
			$result = $this->db->autoExecute(WSY_SHOP.'.personal_center_diy_template_content',$value, 'insert') ;
		} catch(Exception $e){
			$this->db->tran_rollback();
			$return_msg['errcode'] = 40001;
			$return_msg['errmsg']  = $e;
			echo '系统错误，请稍后重试'; exit;
		}
		$this->db->tran_commit();
		//print_r($result);
		return $return_msg;
	}
	
	/*
	 *	查询模块类型
	 *	作者：hzq
	 */
	function select_content_type($customer_id,$diy_tem_contid){
		$sql = "select type from ".WSY_SHOP.".personal_center_diy_template_content where diy_tem_contid = ".$diy_tem_contid." and customer_id = ".$customer_id." and isvalid = true";
		$result = $this->db->getRow($sql);
		return $result;
	}
	
	/*
	 * 更新模块内容
	 * 作者：hzq
	 */
	function update_personal_center_diy_template_content($value=array(),$condition=array()){
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = "success";
		
		$where = "";
		if( empty($condition["customer_id"]) ){
			$return_msg['errcode'] = 40002;
			$return_msg['errmsg']  = "customer_id参数错误";
			return $return_msg;
		}else{
			$where .= " customer_id=".$condition["customer_id"];
		}

		if( isset($condition["isvalid"]) ){
			$where .= " and isvalid='".$condition["isvalid"]."'";
		}
		
		if( isset($condition["diy_tem_contid"]) ){
			$where .= " and diy_tem_contid='".$condition["diy_tem_contid"]."'";
		}
		
		if( empty($where) ){
			$return_msg['errcode'] = 40002;
			$return_msg['errmsg']  = "where为空";
			return $return_msg;
		}
		
		//事务处理
		$this->db->tran_begin();
		try{
			$result = $this->db->autoExecute(WSY_SHOP.'.personal_center_diy_template_content', $value, 'update', $where) ;
			
			//插入更新日志
			$this->insert_log($condition["customer_id"],$value['content']);
			
		} catch(Exception $e){
			$this->db->tran_rollback();
			echo '系统错误，请稍后重试'; exit;
		}
		$this->db->tran_commit();
	//	$result = $this->db->autoExecute(WSY_SHOP.'.personal_center_diy_template_content', $value, 'update', $where) ;
		
		return $return_msg;
	}
	
	/*
	 *	查询模块信息
	 *	作者：lqh
	 */
	function select_template_content($content,$customer_id){
		$where = "isvalid=true and customer_id=".$customer_id." and LOCATE(diy_tem_contid,'".$content."') ";		
		$query = "SELECT id,diy_temid,diy_tem_contid,createtime,isvalid,customer_id,type,content FROM ".WSY_SHOP.".personal_center_diy_template_content WHERE ".$where;	
		$order = " ORDER  BY FIND_IN_SET(diy_tem_contid,'".$content."')";
		$query = $query.$order;
		$result= $this->db->getAll ($query);
		
		return $result;
	}
	
	
	/*
	 * 新增个人中心自定义模板操作日志
	 * @param   [int]     $customer_id	商家编号
	 * @param   [string]  $content   	日志描述
	 * 作者：lqh
    */
	function insert_log($customer_id,$content){
		
		$value = array(
			"customer_id"	=>	$customer_id,
			"operation_name"=>	$_SESSION['curr_login'],
			"content"		=>	$content,
			"createtime"	=>	date("Y-m-d H:i:s",time()),
		);
		$result  = $this->db->autoExecute(WSY_SHOP.'.personal_center_diy_template_content_log',$value, 'insert') ;

		return $result;
	}
	
	/*
	 * 新增预定模板内容SQL
	 * @param   [string]  $sql   	插入语句
	 * 作者：hzq
     */
	function insert_template_content($sql){
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = "success";
		$res = $this->db->query($sql); 
		$contend_id = $this->db->insert_id();
		if($contend_id > 0){
			$query  = "select diy_tem_contid from ".WSY_SHOP.".personal_center_diy_template_content where id='".$contend_id."' and isvalid = true";
			$result = $this->db->getRow($query)['diy_tem_contid'];
			$return_msg['diy_tem_contid'] = $result;
		}else{
			$return_msg['errcode'] = 3614;
			$return_msg['errmsg']  = "插入模板内容失败";
		}
		return $return_msg;
	}
}
