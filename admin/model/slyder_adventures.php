<?php

/*
	数据库操作示例:

	$data = $this->db->getAll ($sql);
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

/*
版权信息:  秘密信息
功能描述：营销大转盘工具数据操作
开 发 者：zhangqiusong
开发日期： 2017-11-01
重要说明：无
 */

class model_slyder_adventures{
    public $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

     /*
      * 大转盘营销工具列表页面
      * 传入参数customer_id
      * Author: zhangqiusong $
      * 2017-11-01
      */
     public function action_list_select($data){
     	//分页设置 start
        $pageSize = $data['pageSize'] ? : 20;//每页多少条
        $pageNum = $data['pageNum'] ? : 1; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;        
        //分页设置 end
        $activity_arr = array();
        $title = "";
        $begin_time = "";
        $end_time = "";
        $customer_id = $data['customer_id'];
        $datetime   =date('Y-m-d H:i:s');

        if($data['title']){
            $title = mysql_escape_string($data['title']);
        }
        if($data['begin_time']){
            $begin_time = $data['begin_time'];
        }
        if($data['end_time']){
            $end_time = $data['end_time'];
        }
        if($data['status']!=-1){
            $status = $data['status'];
        }
        if($data['auto_receive_rewards']!=-1){
            $auto_receive_rewards = $data['auto_receive_rewards'];
        }
        $sql="SELECT id,title,auto_receive_rewards,begin_time,end_time,status FROM ".WSY_SHOP.".slyder_adventures_config WHERE customer_id=".$customer_id." and isvalid=true";
                /************** 搜索条件 start ******************/
        if($title!=""){
            $sql .= " and title like '%".$title."%'";
        }
        if($begin_time!=""){
            $sql .= " and begin_time >= '".$begin_time."'";
        }
        if($end_time!=""){
            $sql .= " and end_time <= '".$end_time."'";
        }
        if($auto_receive_rewards!="" && $auto_receive_rewards!=-1 ){
            $sql .= " and auto_receive_rewards in ({$auto_receive_rewards})";
        }
        if($status=='0'){
            $sql .= " and status in ({$status})";
        }else if ($status=='1') {
        	$sql .= " and status=4";
        }else if($status=='2' ){
        	$sql .=" and end_time>'".$datetime."' and status='1' ";
	    }else if($status=='3' ){
	        $sql .=" and end_time<'".$datetime."' and status='1' ";
	    }
        /************** 搜索条件 end ******************/

        $result = $this->db->getAll ($sql);
        $activity_count = count($result);//总共多少条记录
        $pageCount = ceil($activity_count/$pageSize);//总页数

        if( $data['pageNum'] > 0 ){
            $sql .= " order by id desc limit ".$start.",".$end;
        }

        if( $data['order'] ){
            $sql .= " order by ".$data['order'];
        }    
        $activity_arr = $this->db->getAll($sql);

        $result2['pageCount'] = $pageCount;
        $result2['activity_arr'] = $activity_arr;
        return $result2;
     }

     /*
      * 大转盘删除接口
      * 传入参数customer_id
      * Author: zhangqiusong $
      * 2017-11-01
      */
     public function action_del($data){
     	$result = array();
        $result['errcode'] = 0;
        $result['errmsg'] = "删除失败！";

        $customer_id = $data['customer_id'];
        $create_time = $data['create_time'];
        $id = $data['id'];
        $isvalid = false;
        $where = "customer_id=".$customer_id." and id=".$id." and isvalid=true";
        $res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_config',array('isvalid' => $isvalid,'create_time' => $create_time ), 'update',$where);

        if($res){
            $result['errcode'] = 0;
            $result['errmsg'] = "删除成功！";
        
        }
        return $result;
     } 

	 
	 
	 
     /*
      * 检测大转盘是否符合启动条件
      * 传入参数customer_id
      * Author: zpd
      * 2017-11-01
      */
    public function action_enable_check($data){
     	$result = array();
        $result['errcode'] = 0;
        $result['errmsg'] = "符合条件";

        $customer_id = $data['customer_id'];
        $id          = $data['id'];        

        //查询当前是否有活动在进行中
        $query = "SELECT begin_time,end_time FROM ".WSY_SHOP.".slyder_adventures_config WHERE isvalid=true and customer_id=".$customer_id." and id=".$id;
        $result_action = $this->db->getRow($query); 
		if(!$result_action){
			$result['errcode'] = 41803;
			$result['errmsg'] = "活动不存在";			
		}
		$begin = $result_action['begin_time'];
		$end   = $result_action['end_time'];
		
		$now_time = date('Y-m-d H:i:s');//当前时间
		
		$query2 = "select id,title   
		FROM ".WSY_SHOP.".slyder_adventures_config  
		WHERE ((begin_time > '".$begin."' AND begin_time < '".$end."') OR  
			   (begin_time < '".$begin."' AND end_time > '".$end."') OR  
			   (end_time > '".$begin."' AND end_time < '".$end."')) 
			   AND customer_id=".$customer_id." AND `status`=1 AND isvalid=true AND end_time > '".$now_time."'";
		$result_data = $this->db->getAll ($query2);
		if($result_data){
			$result['errcode'] = 41802;
			$result['errmsg'] = "条件不符合";			
			$result['action'] = $result_data;			
		}
        return $result;
    }	 
	 
	 
     /*
      * 大转盘启用接口
      * 传入参数customer_id
      * Author: zhangqiusong $
      * 2017-11-01
      */
    public function action_enable($data){
     	$result = array();
        $result['errcode'] = 0;
        $result['errmsg'] = "启用成功！";

        $customer_id = $data['customer_id'];
        $id          = $data['id'];        

        
		$where = "customer_id=".$customer_id." and isvalid=true and id=".$id;
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_config',array('status' => '1'), 'update',$where);
		if(!$res){
			$result['errcode'] = 41901;
			$result['errmsg'] = "启用失败！";
		}
      
        return $result;
    }

     /*
      * 大转盘停用接口
      * 传入参数customer_id
      * Author: zhangqiusong $
      * 2017-11-01
      */
     public function action_disable($data){
     	$result = array();
        $result['errcode'] = 1;
        $result['errmsg'] = "停用失败！";
        $create_time = date('Y-m-d H:i:s');//当前时间
        $customer_id = $data['customer_id'];
        $id = $data['id'];
        $where = "customer_id=".$customer_id." and isvalid=true and id=".$id;
        $res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_config',array('status' => "0",'create_time' => $create_time ), 'update',$where);
            if($res){
                $result['errcode'] = 0;
                $result['errmsg'] = "停用成功！";

            }     
        return $result;
     }
	 
	 /*
      * 查询单个活动设置详情
      * @param array data
			['id']			int 活动编号
			['customer_id'] int 商家编号
      * Author: liquanhui $
      * 2017-11-04
      */
	 public function action_select($data){
		 $result = array();
		 $result["errcode"] = 0;
         $result["errmsg"]  = "success";
		 $result["data"]    = array();
		 
		 $wheres= "";
		 foreach( $data as $k => $v ){
			
			$wheres .= ' AND '. $k . '=' .$v;

		}
		$wheres = substr($wheres,4);
		
		$query = "SELECT id,title,begin_time,end_time,type,limit_every_day,limit_order,award_expiry_date,limit_of_participation,display_list_of_winner,auto_receive_rewards,cumulative_frequency_type,create_time,status,is_fact_pay FROM ".WSY_SHOP.".slyder_adventures_config WHERE ";
		
		$query = $query.$wheres." LIMIT 1";
		$res   = $this->db->getRow($query);
		if(empty($res)){
			$result["errcode"] = 40009;
			$result["errmsg"]  = "活动查询为空！";
			return $result;
		}else{
			$result["data"]	= $res;
		}
		
		return $result;
	 }
	 
	 
	  /*
      * 更新活动设置
      * @param array $condition 条件
      * @param array $value    更新值
      * Author: liquanhui $
      * 2017-11-04
      */
	 public function action_update($condition=array(),$value=array()){
		 $result = array();
		 $result["errcode"] = 0;
         $result["errmsg"]  = "success";
		 
		 if( empty($condition) ){
			$result["errcode"] = 40002;
			$result["errmsg"]  = "condition参数错误！";
			return $result;
		 }
		 if( empty($value) ){
			$result["errcode"] = 40002;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		 }
		 
		 $wheres= "";
		 foreach( $condition as $k => $v ){
			
			$wheres .= ' AND '. $k . '=' .$v;

		}
		$wheres = substr($wheres,4);
		
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_config',$value, 'update',$wheres);
		if( !$res ){
			$result["errcode"] = 40004;
			$result["errmsg"]  = "slyder_adventures_config更新失败！";
			return $result;
		}
		
		return $result;
	 }
	 
	 /*
      * 新增活动设置
      * @param array $value 插入值
      * Author: liquanhui $
      * 2017-11-04
      */
	 public function action_insert($value=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";

		if(empty($value)){
			$result["errcode"] = 40002;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		}
		 
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_config',$value, 'insert');
		if(!$res){
			$result["errcode"] = 40004;
			$result["errmsg"]  = "slyder_adventures_config插入失败！";
			return $result;
		}
		$act_id = $this->db->insert_id();
		$result["slyder_id"] = $act_id;
		
		return $result;
	 }

	 /*
      * 查询转盘奖品详情
      * @param array data
			['id']			int 活动编号
			['customer_id'] int 商家编号
      * Author: liquanhui $
      * 2017-11-04
      */
	 public function award_select($data){
		 $result = array();
		 $result["errcode"] = 0;
         $result["errmsg"]  = "success";
		 $result["data"]    = array();
		 
		 $wheres= "";
		 foreach( $data as $k => $v ){
			
			$wheres .= ' AND '. $k . '=' .$v;

		}
		$wheres = substr($wheres,4);
		
		$query = "SELECT id,slyder_id,award_type,award_level,name,num,probability,img,coupon_id,num_limit_day,express_price FROM ".WSY_SHOP.".slyder_adventures_award WHERE ";
		
		$query = $query.$wheres." ORDER BY id ASC";
		$res   = $this->db->getAll($query);
		if(empty($res)){
			$result["errcode"] = 40009;
			$result["errmsg"]  = "奖项查询为空！";
			return $result;
		}else{
			$result["data"]	= $res;
		}
		
		return $result;
	 }
	 
	 
	 /*
      * 新增活动奖项
      * @param array $value 插入值
      * Author: liquanhui $
      * 2017-11-04
      */
	 public function award_insert($value=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";

		if( empty($value) ){
			$result["errcode"] = 40002;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		}
		 
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_award',$value, 'insert');
		if( !$res ){
			$result["errcode"] = 40004;
			$result["errmsg"]  = "slyder_adventures_award插入失败！";
			return $result;
		}
		$act_id = $this->db->insert_id();
		$result["slyder_id"] = $act_id;
		
		return $result;
	 }
	 
	 /*
      * 更新活动奖项
      * @param array $condition 条件
      * @param array $value    更新值
      * Author: liquanhui $
      * 2017-11-04
      */
	 public function award_update($condition=array(),$value=array()){
		 $result = array();
		 $result["errcode"] = 0;
         $result["errmsg"]  = "success";
		 
		 if( empty($condition) ){
			$result["errcode"] = 40002;
			$result["errmsg"]  = "condition参数错误！";
			return $result;
		 }
		 if( empty($value) ){
			$result["errcode"] = 40002;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		 }
		 
		 $wheres= "";
		 foreach( $condition as $k => $v ){
			
			$wheres .= ' AND '. $k . '=' .$v;

		}
		$wheres = substr($wheres,4);
		
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_award',$value, 'update',$wheres);
		if( !$res ){
			$result["errcode"] = 40004;
			$result["errmsg"]  = "slyder_adventures_award更新失败！";
			return $result;
		}
	
		return $result;
	 }
	 
	 
	 /*
      * 批量保存活动奖项
      * @param array $data 表单值
      * Author: liquanhui $
      * 2017-11-04
      */
	 public function award_save_all($customer_id,$slyder_id,$data=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";
		 
		//事务处理
		$this->db->tran_begin();
		try{ 
			$all_probability = 0;	//所有概率之和
			foreach ( $data as $key => $vo ) {
				$save = array ();
				foreach ( $vo as $k => $v ) {
					$save [$v ['name']] = $v ['value'];
				}
				if ($save ['probability'] == '' || $save ['probability'] < 0 || $save ['probability']>10000) {
					$result["errcode"] = 40002;
					$result["errmsg"]  = '请正确填写奖项' . $this->select_num($key+1) . '的中奖概率，概率范围0‱ ~10000‱ ';
					return $result;
				}else{
					$all_probability += $save ['probability'];
				}
			}
			if( $all_probability != 10000 ){
				$result["errcode"] = 40002;
				$result["errmsg"]  = '所有奖项的概率之和必须等于10000‱';
				return $result;
			}
			foreach ( $data as $key => $vo ) {
				$save = array ();
				foreach ( $vo as $k => $v ) {
					if( $v['name'] == 'name' ){
						$save [$v ['name']] = mysql_escape_string( $v ['value'] );
					}else{
						$save [$v ['name']] = $v ['value'];
					}
				}
				if ($save ['coupon_id'] < 0 && $save ['award_type'] == 1) {
					$result["errcode"] = 40002;
					$result["errmsg"]  = '请先关联奖项' . $this->select_num($key+1) . '的优惠券';
					return $result;
				}
				if (empty ( $save ['name'] )) {
					$result["errcode"] = 40002;
					$result["errmsg"]  = '请正确填写奖项' . $this->select_num($key+1) . '的奖品名';
					return $result;
				}else if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$save ['name'])){
					//  /[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/
					$result["errcode"] = 40002;
					$result["errmsg"]  = '奖项' . $this->select_num($key+1) . '的奖品名出现特殊字符';
					return $result;
				}
				if ($save ['num'] == '' || $save ['num'] <= 0) {
					$result["errcode"] = 40002;
					$result["errmsg"]  = '请正确填写奖项' . $this->select_num($key+1) . '的奖品数量';
					return $result;
				}
				if (($save ['express_price'] == '' || $save ['express_price'] < 0) && $save ['award_type'] == 2) {
					$result["errcode"] = 40002;
					$result["errmsg"]  = '请正确填写奖项' . $this->select_num($key+1) . '的运费';
					return $result;
				}
				if ($save ['num_limit_day'] == '' || $save ['num_limit_day'] < -1) {
					$result["errcode"] = 40002;
					$result["errmsg"]  = '请正确填写奖项' . $this->select_num($key+1) . '的每天发奖量';
					return $result;
				}
				if ( $save ['num_limit_day'] > $save ['num'] ) {
					$result["errcode"] = 40002;
					$result["errmsg"]  = '奖项' . $this->select_num($key+1) . '的每天发奖量不得大于奖品数量';
					return $result;
				}
				
				if (! empty ( $save ['id'] )) { // 更新数据
					$where = "id=".$save ['id'];
					$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_award',$save, 'update',$where);
				} else { // 新增加
					$save ['isvalid']    = true;
					$save ['slyder_id']  = $slyder_id;
					$save ['create_time']= date("Y-m-d H:i:s",time());
					$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_award',$save, 'insert');
					$newId = $this->db->insert_id();
					if ($newId) {
					} else {
						$this->db->tran_rollback();
						$result["errcode"] = 40004;
						$result["errmsg"]  = '增加奖项' . $this->select_num($key+1) . '失败，请检查数据后重试';
						return $result;
					}
				}
			}
		}catch(Exception $e){
			$this->db->tran_rollback();
			echo '系统错误，请稍后重试'; exit;
		}
		$this->db->tran_commit();

		return $result;
	 }
	 
	 
	public function select_num($n){
		$result = "";
		switch($n){
			case 1:
				$result = "一";
			break;
			case 2:
				$result = "二";
			break;
			case 3:
				$result = "三";
			break;
			case 4:
				$result = "四";
			break;
			case 5:
				$result = "五";
			break;
			case 6:
				$result = "六";
			break;
			case 7:
				$result = "七";
			break;
			case 8:
				$result = "八";
			break;
			case 9:
				$result = "九";
			break;
			case 10:
				$result = "十";
			break;
			case 11:
				$result = "十一";
			break;
			case 12:
				$result = "十二";
			break;
			case 13:
				$result = "十三";
			break;
			case 14:
				$result = "十四";
			break;
			case 15:
				$result = "十五";
			break;
			case 16:
				$result = "十六";
			break;
			case 17:
				$result = "十七";
			break;
			case 18:
				$result = "十八";
			break;
			case 19:
				$result = "十九";
			break;
			case 20:
				$result = "二十";
			break;
			default:
				$result = "未知";
			break;
		}
		return $result;
	}
	 
   /*
      * 查看有无中奖名单
      * @param array $value 插入值
      * Author: zhangqiusong $
      * 2017-11-06
      */
   public function name_list_select($data){
        //分页设置 start
        $pageSize = $data['pageSize'] ? : 20;//每页多少条
        $pageNum  = $data['pageNum'] ? : 1; //当前页,1开始
        $start    = ($pageNum-1)*$pageSize;
        $end      = $pageSize;        
        //分页设置 end
		
        $activity_arr = array();
        $weixin_name = "";
        $name = "";
        $title="";
        $user_id = "";
        $name = "";
        $phone = "";
        $customer_id = $data['customer_id'];
        $award_id    = $data['award_id'];

        if($data['weixin_name']){
            $weixin_name = $data['weixin_name'];
        }
        if($data['user_id']){
            $user_id = $data['user_id'];
        }
        if($data['name']){
            $name = $data['name'];
        }
        if($data['phone']){
            $phone = $data['phone'];
        }
        if($data['status'] !=-1){
            $status = $data['status'];
        }
        if ($data['slyder_id']) {
        	$slyder_id=$data['slyder_id'];
        }else{
        	echo "系统错误40002，请稍后重试";
        	exit;
        }

		
        //查询用户的相关信息
        if ($award_id==">-1") {
        	$userinfo = "SELECT * FROM (SELECT u.id,u.name,u.phone,u.address,u.weixin_name,r.award_level,r.award_name,r.customer_id,r.status,r.type,r.order_batchcode FROM ".WSY_USER.".weixin_users as u,".WSY_SHOP.".slyder_adventures_reward as r WHERE u.id=r.user_id and slyder_id=".$slyder_id." and r.isvalid=true and r.award_id".$award_id." order by u.id desc)userinfo_table WHERE customer_id=".$customer_id;
        }else{
        	$userinfo="SELECT * FROM (SELECT r.user_id as id,r.create_time,r.token,u.name,u.weixin_name,u.phone,u.address FROM ".WSY_SHOP.".slyder_adventures_record AS r JOIN ".WSY_USER.".weixin_users AS u ON u.isvalid=true AND r.isvalid=TRUE and r.customer_id=".$customer_id." and r.user_id=u.id and slyder_id=".$slyder_id." and r.award_id=-1 order by id desc)a where id>0";
        }
        /************** 搜索条件 start ******************/
			if($weixin_name!=""){
				$userinfo .= " and weixin_name like '%".$weixin_name."%'";
			}
			if($user_id!=""){
				$userinfo .= " and id like '%".$user_id."%'";
			}
			if($name!=""){
				$userinfo .= " and name like '%".$name."%'";
			}
			if($phone!=""){
				$userinfo.= " and phone like '%".$phone."%'";
			}
			if($status!=""){
				$userinfo.= " and status ='".$status."'";
			}
		 /************** 搜索条件 end ******************/

        /************** 筛选出对应的slyder_id的获奖用户 start ******************/
        if ($award_id==">-1") {
			$slyuser="SELECT user_id FROM ".WSY_SHOP.".slyder_adventures_record WHERE isvalid=true and customer_id=".$customer_id." and award_id".$award_id." and slyder_id=".$slyder_id;
			$users = $this->db->getAll($slyuser);
			$where = "";
			$count = count($users);
			for ($i=0; $i < $count; $i++) { 
				$where .=$users[$i]["user_id"].",";
			}
			$where=rtrim($where,',');
			if ($where!="") {
				$userinfo.=" and id in ({$where})";
			}else{
				$result2['pageCount']    = 0;
				$result2['activity_arr'] = array();
				return $result2;
				exit;
			}
        }
        /************** 筛选出对应的slyder_id获奖用户 end ******************/
        $result = $this->db->getAll($userinfo);
        $activity_count = count($result);//总共多少条记录
        $pageCount = ceil($activity_count/$pageSize);//总页数
        if( $data['pageNum'] > 0 ){
            $userinfo .= " order by id desc limit ".$start.",".$end;
        }
        // if( $data['order'] ){
        //     $userinfo .= " order by ".$data['order'];
        // }    
        $activity_arr = $this->db->getAll($userinfo);

		//查询出用户对应的订单号
		if ($award_id==">-1") {
			for ($i=0; $i < count($activity_arr); $i++) { 
				$batchcode_user_id=$activity_arr[$i]['id'];
				$token_sql="SELECT token from ".WSY_SHOP.".slyder_adventures_record where isvalid=true and slyder_id=".$slyder_id." and customer_id=".$customer_id." and user_id=".$batchcode_user_id." limit 1";
				$activity_arr[$i]['token']=$this->db->getOne($token_sql);
				$batchcode_sql="SELECT batchcode from ".WSY_SHOP.".slyder_adventures_chance_extend where isvalid=true and slyder_id=".$slyder_id." and customer_id=".$customer_id." and user_id=".$batchcode_user_id." and token='".$activity_arr[$i]['token']."' limit 1";
				$activity_arr[$i]['batchcode_shop']=$this->db->getOne($batchcode_sql);//对应商城订单
			}
		}else{
			for ($i=0; $i < count($activity_arr); $i++) { 
				$batchcode_user_id=$activity_arr[$i]['id'];
				$token    =   $activity_arr[$i]['token'];
				$batchcode_sql="SELECT batchcode from ".WSY_SHOP.".slyder_adventures_chance_extend where isvalid=true and slyder_id=".$slyder_id." and customer_id=".$customer_id." and user_id=".$batchcode_user_id." and token='".$token."' limit 1";
				$activity_arr[$i]['batchcode_shop']=$this->db->getOne($batchcode_sql);//对应商城订单
			}
		}


        $result2['pageCount']    = $pageCount;
        $result2['activity_arr'] = $activity_arr;
        return $result2;

   }

   
   /*
      * 查询优惠券列表
      * @param int   $pageNum 第几页
      * @param array $condition 查询条件
      * Author: liquanhui $
      * 2017-11-07
      */
   public function coupon_sel($pageNum=1,$condition=array()){
	    $return_msg["errcode"] = 0;
	    $return_msg["errmsg"]  = "success";

		$start = ($pageNum-1) * 20 ;
		$end = 20;
		$pageCount = 1;	//总页数
		$wheres= "";
		foreach( $condition as $k => $v ){
			if($k=="title"){
				$wheres .= ' AND '. $k . ' like "%' .$v. '%"';
				continue;
			}
			$wheres .= ' AND '. $k . '=' .$v;
		}
		$wheres = substr($wheres,4);
		
		$query = 'select id,is_open,title,NeedMoney,CanGetNum,Days,DaysType,class_type,MinMoney,MaxMoney,user_scene,couponNum,MoneyType,personNum,getStartTime,getEndTime,createtime,startline,storenum,use_roles,get_roles,is_showcouponlist from '.WSY_SHOP.'.weixin_commonshop_coupons where ';
		$query .= $wheres;
		
		$rcount_num = 0;	//总数据量
		$query_num  = 'select count(1) as rcount from '.WSY_SHOP.'.weixin_commonshop_coupons  where '.$wheres; 
		$rcount_num = $this->db->getOne($query_num);
		
		$pageCount=ceil($rcount_num/$end);

		$query .= " order by id desc limit ".$start.",".$end; 
		//echo $query;
		$result = $this->db->getAll($query);
		
		$return_msg["pageCount"] = $pageCount;
		$return_msg["couponList"]= $result;
		
		return $return_msg;
   }
   
   /**
     * 查询产品分类
     * @param int   $customer_id 商家编号
     * Author: liquanhui $
     * 2017-11-07
     **/
   public function select_product_type($customer_id){

		$parent_id = -1;
        $parent_name = ''; // 顶级分类
        $query = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='".$customer_id."' AND parent_id=-1 AND is_shelves=1";
        $result = $this->db->getAll($query);
        $type_str = '';
        foreach ($result as $type_k1 => $row) {
            $parent_id = $row['id'];
            $parent_name = $row['name'];
            $select = '';
            if($data['product_type'] == $parent_id){ $select = 'selected';} 

            $type_str .= '<option value="'.$parent_id.'" '.$select.' >'.$parent_name.'</option>';

            $ch_id2 = -1;
            $ch_name2 = '';// 第二级分类
            $query_c2 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='".$customer_id."' AND parent_id={$parent_id} AND is_shelves=1";
            $result_c2= $this->db->getAll($query_c2);
            foreach ($result_c2 as $type_k2 => $row_c2) {
                $ch_id2 = $row_c2['id'];
                $ch_name2 = $row_c2['name'];
                if($ch_id2 != -1){
                    $select2 = '';
                    
                    if($data['product_type'] == $ch_id2){ $select2 =  'selected';}

                    $type_str .= '<option value="'.$ch_id2.'" '.$select2.' >'.'&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name2.'</option>';

                    $ch_id3 = -1;
                    $ch_name3 = '';// 第三级分类
                    $query_c3 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='".$customer_id."' AND parent_id={$ch_id2} AND is_shelves=1";
                    $result_c3= $this->db->getAll($query_c3);
                    foreach ($result_c3 as $type_k3 => $row_c3) {
                        $ch_id3 = $row_c3['id'];
                        $ch_name3 = $row_c3['name'];
                        $select3 = '';

                        if($data['product_type'] == $ch_id3){ $select3 = 'selected';}
                        
                        $type_str .= '<option value="'.$ch_id3.'" '.$select3.' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name3.'</option>';

                        $ch_id4 = -1;
                        $ch_name4 = '';// 第四级分类
                        $query_c4 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='".$customer_id."' AND parent_id={$ch_id3} AND is_shelves=1";
                        $result_c4= $this->db->getAll($query_c4);
                        foreach ($result_c4 as $type_k4 => $row_c4) {
                            $ch_id4 = $row_c4['id'];
                            $ch_name4 = $row_c4['name'];
                            $select4 = '';

                            if($data['product_type'] == $ch_id4){ $select4 = 'selected';}
                        
                            $type_str .= '<option value="'.$ch_id4.'" '.$select4.' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name4.'</option>';
                        }
                    }
                }
            }
        }
		
		return $type_str;
   }

   /**
     * 查询产品列表
     * @param int   $customer_id 商家编号
     * Author: liquanhui $
     * 2017-11-07
     **/
   public function select_products($pageNum=1,$condition=array()){
	    $return_msg["errcode"] = 0;
	    $return_msg["errmsg"]  = "success";

		$start = ($pageNum-1) * 20 ;
		$end = 20;
		$pageCount = 1;	//总页数
		$wheres= "";
		foreach( $condition as $k => $v ){
			if($k=="name"){
				$wheres .= ' AND '. $k . ' like "%' .$v. '%"';
				continue;
			}
			if($k=="type_ids"){
				$wheres .= ' AND '. $k . ' like "%,' .$v. ',%"';
				continue;
			}
			$wheres .= ' AND '. $k . '=' .$v;
		}
		$wheres = substr($wheres,4);
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) as rcount from weixin_commonshop_products where isvalid = 1 and isout = 0 and is_virtual=0 and ".$wheres;
        $rcount_num = $this->db->getOne($query_num);
        $query = "SELECT id,name,orgin_price,now_price,default_imgurl,type_ids,storenum from weixin_commonshop_products where isvalid = 1 and isout = 0 and is_virtual=0 and ".$wheres;

        $pageCount=ceil($rcount_num/$end);

        $query .= " order by id desc limit ".$start.",".$end; 
        $res    = $this->db->getAll($query);
		
		foreach ($res as $k=>$v) {
  
            $imgurl = $v['default_imgurl'];

            if(empty($imgurl)){
                $query_img ="SELECT imgurl from weixin_commonshop_product_imgs where isvalid=true and product_id={$v['id']} limit 0,1";
                $data_imgurl = $this->db->getRow($query_img);
                $imgurl = $data_imgurl['imgurl'];
            }

            $type_ids = $v['type_ids'];
            
            if(!empty($type_ids)){
                if(strpos($type_ids,",") === 0){
                    $type_ids = substr($type_ids,1);
                }
                if(substr($type_ids,strlen($type_ids)-1) == ","){
                    $type_ids = substr($type_ids,0,strlen($type_ids)-1);
                }
               
                if(!empty($type_ids)){
                    $type_ids = str_replace(',,',',',$type_ids);
                    
                    $query = "SELECT name from weixin_commonshop_types where isvalid=true and id in ({$type_ids})  ORDER BY create_parent_id asc ";
                    
                    $data_type = $this->db->getAll($query);
                    foreach ($data_type as $key=>$val) {
                        $typename = $typename."/".$val['name'];
                    }
                }
            }

            $res[$k]['default_imgurl']  = $imgurl;
            $res[$k]['typename']        = $typename;
        }
		
		$return_msg["pageCount"]   = $pageCount;
		$return_msg["productList"] = $res;
		
		
		return $return_msg;
   }
   
   	 /*
      * 查看活动统计列表方法
      * @param array $value 插入值
      * Author: zhangqiusong $
      * 2017-11-08
      */
   	public function statis_action_list($data){
		//分页设置 start
        $pageSize = $data['pageSize'] ? : 20;//每页多少条
        $pageNum = $data['pageNum'] ? : 1; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;        
        //分页设置 end

        $activity_arr = array();
        $id           = "";
        $title        = "";
        $begin_time   = "";
        $end_time     = "";
        $status       = "";
        $customer_id  = $data['customer_id'];
        $users        = "";
        $num          = "";
        $win_users    = "";
        $datetime   =date('Y-m-d H:i:s');

        if ($data['id']) {
        	$id = $data['id'];
        }
        if ($data['title']) {
        	$title = $data['title'];
        }
        if($data['begin_time']){
            $begin_time = $data['begin_time'];
        }
        if($data['end_time']){
            $end_time = $data['end_time'];
        }
        if($data['status']!=-1){
            $status = $data['status'];
        }
        $sql = "SELECT id,title,type,begin_time,end_time,status from ".WSY_SHOP.".slyder_adventures_config where isvalid=true and customer_id=".$customer_id;
        /************** 搜索条件 start ******************/
        if($id!=""){
            $sql .= " and id like '%".$id."%'";
        }
        if($title!=""){
            $sql .= " and title like '%".$title."%'";
        }
        if($begin_time!=""){
            $sql .= " and begin_time >= '".$begin_time."'";
        }
        if($end_time!=""){
            $sql .= " and end_time <= '".$end_time."'";
        }
        if($status=='0'){
            $sql .= " and status in ({$status})";
        }else if ($status=='1') {
        	$sql .= " and status=4";
        }else if($status=='2'){
        	$sql .=" and end_time>'".$datetime."' and status='1' ";
        }else if($status=='3'){
        	$sql .=" and end_time<'".$datetime."' and status='1' ";
        }
        /************** 搜索条件 end ******************/

        $result = $this->db->getAll ($sql);
        $activity_count = count($result);//总共多少条记录
        $pageCount = ceil($activity_count/$pageSize);//总页数

        if( $data['pageNum'] > 0 ){
            $sql .= " order by id desc limit ".$start.",".$end;
        }

        if( $data['order'] ){
            $sql .= " order by ".$data['order'];
        }   
        $activity_arr = $this->db->getAll ($sql);
        if ($activity_arr) {
        	foreach ($activity_arr as $k => $v) {
        	$users="SELECT count(distinct user_id) from ".WSY_SHOP.".slyder_adventures_record where isvalid=true and slyder_id=".$v['id'];
        	$activity_arr[$k]['users'] =$this->db->getOne ($users);
        	$num="SELECT sum(num) from ".WSY_SHOP.".slyder_adventures_award where isvalid=true and slyder_id=".$v['id'];
        	$activity_arr[$k]['num'] =$this->db->getOne ($num);
        	$win_users="SELECT count(id) from ".WSY_SHOP.".slyder_adventures_reward where isvalid=true and slyder_id=".$v['id'];
        	$activity_arr[$k]['win_users'] =$this->db->getOne ($win_users);
        	}
        }
        $result2['pageCount'] = $pageCount;
        $result2['activity_arr'] = $activity_arr;
        return $result2;
   	}

   	 /*
      * 活动统计 用户明细方法
      * @param array $value 插入值
      * Author: zhangqiusong $
      * 2017-11-08
      */
   	public function statis_action_user_list($data){
   		//分页设置 start
        $pageSize = $data['pageSize'] ? : 20;//每页多少条
        $pageNum = $data['pageNum'] ? : 1; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;        
        //分页设置 end

        $activity_arr = array();
        $slyder_id    = "";
        $weixin_name  = "";
        $user_id      = "";
        $createtime   = "";
        $customer_id  = $data['customer_id'];
        $user_statistics= $data['user_statistics'];

        if ($data['slyder_id']) {
        	$slyder_id = $data['slyder_id'];
        }
        if ($data['weixin_name']) {
        	$weixin_name = $data['weixin_name'];
        }
        if($data['user_id']){
            $user_id = $data['user_id'];
        }
        if($data['createtime']){
            $createtime = $data['createtime'];
        }
        if($data['createtime_end']){
            $createtime_end = $data['createtime_end'];
        }
        if ($user_statistics!=1) {
        	$where = " slyder_id='".$slyder_id."' and ";
        }else if($user_statistics==1){
        	$where =""; 
        }
        $sql="SELECT a.user_id,a.num,users.weixin_headimgurl,users.weixin_name,users.createtime from (select user_id,COUNT(*) as num from ".WSY_SHOP.".slyder_adventures_record where ".$where." customer_id=".$customer_id." and isvalid=true GROUP BY user_id)a join weixin_users as users on users.isvalid=true and a.user_id=users.id";
                /************** 搜索条件 start ******************/	
        if($weixin_name!=""){
            $sql .= " and users.weixin_name like '%".$weixin_name."%'";
        }
        if($user_id!=""){
            $sql .= " and a.user_id like '%".$user_id."%'";
        }
        if($createtime!=""){	
            $sql .= " and users.createtime>='".$createtime."'";
        }
        if($createtime_end!=""){	
            $sql .= " and users.createtime<='".$createtime_end."'";
        }
        /************** 搜索条件 end ******************/

        $result = $this->db->getAll ($sql);
        $activity_count = count($result);//总共多少条记录
        $pageCount = ceil($activity_count/$pageSize);//总页数

        if( $data['pageNum'] > 0 ){
            $sql .= " order by user_id desc limit ".$start.",".$end;
        }

        if( $data['order'] ){
            $sql .= " order by ".$data['order'];
        }    

        $activity_arr = $this->db->getAll($sql);


        $result2['pageCount'] = $pageCount;
        $result2['activity_arr'] = $activity_arr;
        return $result2;
   	}

	
	 /*
      * 查询轮盘抽奖额外抽奖次数
      * @param array $data 插入值
				boolean ['isvalid'] 有效性
				int     ['customer_id'] 商家编号
				int     ['user_id']     用户编号
				int     ['slyder_id']   活动编号
				int     ['type']   机会类型 1.当天有效 2.当前活动有效 3.临时有效
				string  ['token']  token：当机会类型为临时有效时，需要匹配token
      * Author: liquanhui $
      * 2017-11-08
      */
	public function action_chance_extend_select($data){

		$scount = 0;		//额外抽奖次数
		$wheres = "";
		foreach( $data as $k => $v ){
			
			if( $k == "token" ){
				continue;
			}
			
			$wheres .= ' AND '. $k . '=' .$v;

		}
		$wheres  = substr($wheres,4);
		
		if( $data['type'] == 1 ){
			//1.当天有效
			$wheres .= " AND date = '".date('Y-m-d',time())."' ";
			
		}else if( $data['type'] == 2 ){
			//2.当前活动有效
			
		}else if( $data['type'] == 3 ){
			//3.临时有效
			$wheres .= " AND token = '".$data['token']."' ";
			
		}
		
		$ccount = 0;	//查看是否有无限次数机会
		$query  = "SELECT count(id) AS ccount FROM ".WSY_SHOP.".slyder_adventures_chance_extend WHERE ".$wheres." AND num=-1 AND is_pay=1";
		$result = $this->db->getOne($query);
		if($result) $ccount = $result;
		
		if($ccount > 0){
			$scount = -1;
		}else{
			$query  = "SELECT SUM(num) AS scount FROM ".WSY_SHOP.".slyder_adventures_chance_extend WHERE ".$wheres." AND is_pay=1";
			//echo $query;
			$result = $this->db->getOne($query);
			if($result) $scount = $result;
			if( $scount < 0 ) $scount = 0;
		}

		return $scount;
	}
	
 	 /*
      * 大转盘营销工具 用户统计查看明细查询
      * 传入参数customer_id
      * Author: zhangqiusong
      * 2017-11-24
      */
	public function action_user_statis_select($data){
		//分页设置 start
        $pageSize = $data['pageSize'] ? : 20;//每页多少条
        $pageNum = $data['pageNum'] ? : 1; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;        
        //分页设置 end

        $activity_arr = array();
        $slyder_id    = "";
        $user_id      = "";
        $title        = "";
        $award_level  = "";
        $create_time  = "";
        $status       = "";
        $customer_id  = $data['customer_id'];
        $name         = "";
        $phone        = "";
        $datetime   =date('Y-m-d H:i:s');

        if ($data['slyder_id']) {
        	$slyder_id = $data['slyder_id'];
        }
        if ($data['user_id']) {
        	$user_id = $data['user_id'];
        }
        if ($data['title']) {
        	$title = $data['title'];
        }
        if($data['award_level'] > 0){
            $award_level = $data['award_level'];
        }
        if($data['create_time']){
            $create_time = $data['create_time'];
        }
		if($data['create_time_end']){
            $create_time_end = $data['create_time_end'];
        }
        if($data['name']){
            $name = $data['name'];
        }
        if($data['phone']){
            $phone = $data['phone'];
        }
        if($data['status']!=-1 && $data['status']!=""){
            $status = $data['status'];
        }
        //查询出对应的活动slyder_id,title
        $title_sql = "SELECT id,title FROM ".WSY_SHOP.".slyder_adventures_config where isvalid=true and customer_id=".$customer_id;
        if($title!=""){
            $title_sql.= " and title like '%".$title."%'";
        }
        if($slyder_id!=""){
            $title_sql .= " and id =".$slyder_id;
        }
        $title_slyder=$this->db->getAll ($title_sql); 
        // var_dump($title_slyder);
        foreach ($title_slyder as $k => $v) {
        	$res1.=$title_slyder[$k]['id'].",";
        	$res2.=$title_slyder[$k]['title'].",";
        }
        $where_id = rtrim($res1,',');
        $where_title    = rtrim($res2,',');
        // var_dump($where_title);
        // var_dump($where_id);
        if ($where_id!="") {
        	$where=" and rc.slyder_id in (".$where_id.")";
        }else{
        	$where="";
        }
		
		$result2['pageCount'] = 1;
		$result2['activity_arr'] = [];
		$result2['user_arr']     = [];
		
		if( !empty($title_slyder) ){
			$sql = "SELECT rc.user_id,rc.slyder_id,rc.create_time,rw.award_level,rw.award_name,rw.status,rw.order_batchcode,rw.type FROM ".WSY_SHOP.".slyder_adventures_record as rc left join ".WSY_SHOP.".slyder_adventures_reward as rw on rc.reward_id=rw.id  where rc.isvalid=true and rc.user_id=".$user_id." and rc.customer_id=".$customer_id.$where;
			/************** 搜索条件 start ******************/
			// if($slyder_id!=""){
			//     $sql .= " and r.slyder_id =".$slyder_id;
			// }
			if($award_level!=""){
				$sql.= " and rw.award_level ='".$award_level."'";
			}
			if($status!=""){
				$sql.= " and rw.status ='".$status."'";
			}
			if($create_time!=""){
				$sql .= " and rc.create_time >='".$create_time."'";
			}
			if($create_time_end!=""){
				$sql .= " and rc.create_time <='".$create_time_end."'";
			}
			if($name!=""){
				$sql .= " and wu.name like '%".$name."%'";
			}
			if($phone!=""){
				$sql .= " and wu.phone like '%".$phone."%'";
			}
			/************** 搜索条件 end ******************/

			$result = $this->db->getAll ($sql);
			$activity_count = count($result);//总共多少条记录
			$pageCount = ceil($activity_count/$pageSize);//总页数

			if( $data['pageNum'] > 0 ){
				$sql .= " order by slyder_id desc limit ".$start.",".$end;
			}
			$activity_arr = $this->db->getAll ($sql);
			if ($activity_arr) {
					foreach ($activity_arr as $k => $v) {
						if ($v['order_batchcode']!="") {
							$user_address = "SELECT a.location_p,a.location_c,a.location_a,a.address FROM ".WSY_SHOP.".slyder_adventures_reward_order_address AS a 
							INNER JOIN ".WSY_SHOP.".slyder_adventures_reward_orders AS o ON o.id=a.order_id 
							WHERE a.isvalid=true AND o.isvalid=true AND o.customer_id=".$customer_id." AND o.batchcode='".$v['order_batchcode']."' LIMIT 1";
							$activity_arr[$k]['user_address'] =$this->db->getRow ($user_address); 
						}
			
					foreach ($title_slyder as $k2 => $v2) {
						if ($v['slyder_id']==$v2['id']) {
							$activity_arr[$k]['title']=$v2['title'];
						}
					}
				}
			}
		
		
			//查用户信息
			$sql_user = "SELECT name,phone FROM ".WSY_USER.".weixin_users WHERE isvalid=true and id='".$user_id."' limit 1";
			$user_arr = $this->db->getRow ($sql_user);
		
			$result2['pageCount'] = $pageCount;
			$result2['activity_arr'] = $activity_arr;
			$result2['user_arr']     = $user_arr;
		}
        //var_dump($result2);
         //var_dump($user_address);
         //var_dump($user_arr);
        return $result2;

	 }
	
	/*
      * 查询轮盘抽奖 用户已抽奖次数
	  * @param int   $pageNum   第几页
	  * @param array $condition 参数 	
				int     ['r.customer_id'] 商家编号
				int     ['r.user_id']     用户编号
				int     ['r.slyder_id']   活动编号
				int     ['wu.weixin_name']   微信名
      * Author: liquanhui $
      * 2017-11-08
      */
	public function statis_user_list_select($pageNum=1,$condition=array()){
		$return_msg["errcode"] = 0;
	    $return_msg["errmsg"]  = "success";
		
		if(empty($condition)){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "condition参数错误！";
			return $return_msg;
		}
		
		$start = ($pageNum-1) * 20 ;
		$end   = 20;
		$pageCount = 1;	//总页数
		//print_r($condition);
		$wheres= "";
		foreach( $condition as $k => $v ){
			if($k=="wu.weixin_name"){
				$wheres .= ' AND '. $k . ' like "%' .$v. '%"';
				continue;
			}
			if( $k == "r.begin_time" ){
				if(!empty($v) ){
					$wheres .= " AND r.create_time >='" .$v. "'";
				}
				continue;
			}
			if( $k == "r.end_time" ){
				if(!empty($v) ){
					$wheres .= " AND r.create_time <='" .$v. "'";
				}
				continue;
			}
			
			$wheres .= " AND ". $k . "='" .$v. "'";
		}
		//$wheres = substr($wheres,4);
		
		$query = "SELECT wu.id,wu.name,wu.weixin_name,wu.createtime,wu.weixin_headimgurl 
					FROM ".WSY_SHOP.".slyder_adventures_record AS r 
					INNER JOIN ".WSY_USER.".weixin_users AS wu ON wu.id=r.user_id WHERE r.isvalid=true AND wu.isvalid=true ";
		$query = $query . $wheres . " GROUP BY r.user_id ";
		
		$scount_num = 0;	//总数据量
		$query_num  = "SELECT COUNT( DISTINCT r.`user_id` ) AS scount FROM ".WSY_SHOP.".slyder_adventures_record AS r 
						INNER JOIN ".WSY_USER.".weixin_users AS wu ON wu.id=r.user_id WHERE r.isvalid=true AND wu.isvalid=true ";
		$query_num  = $query_num . $wheres;
		//echo $query_num;
		$scount_num = $this->db->getOne($query_num);
		
		$pageCount=ceil($scount_num/$end);

		$query .= " ORDER BY wu.createtime DESC LIMIT ".$start.",".$end;
		$result = $this->db->getAll($query);
		
		if( !empty($result) ){
			
			foreach( $result as $k=>$v ){
				$where2 = "";
				if(empty($condition["r.user_id"])){
					$where2 = " AND r.user_id=".$v["id"];
				}
				//统计每个用户已抽奖次数
				$rcount = 0;
				$query  = "SELECT count(r.id) AS rcount FROM ".WSY_SHOP.".slyder_adventures_record AS r WHERE r.isvalid=true ". $wheres . $where2;
				$res    = $this->db->getOne($query);
				if($res) $rcount = $res;
				$result[$k]["rcount"] = $rcount;
				//统计每个用户最近抽奖时间
				$last_time = "";
				$query  = "SELECT r.create_time AS last_time FROM ".WSY_SHOP.".slyder_adventures_record AS r WHERE r.isvalid=true ". $wheres . $where2 ." ORDER BY last_time DESC LIMIT 1";
				$res    = $this->db->getOne($query);
				if($res) $last_time = $res;
				$result[$k]["last_time"] = $last_time;
			}
		}
		
		$return_msg["scount_num"]= $scount_num;
		$return_msg["pageCount"] = $pageCount;
		$return_msg["userList"]  = $result;
		
		return $return_msg;
	}
	
	
	/*
      * 查询轮盘抽奖 统计奖项已中奖数量
      * @param  array  condition 条件
				int   ['award_id']   奖项编号
				int   ['slyder_id']  活动编号
				string[begin_time']  生成时间
				string[end_time']   生成时间
      * Author: liquanhui $
      * 2017-11-08
      */
	public function statis_award_count($condition=array()){
		$return_msg["errcode"] = 0;
		$return_msg["errmsg"]  = "success";
		$return_msg["data"]    = array();
		
		$wheres = " isvalid=true ";
		foreach( $condition as $k => $v ){
			if( $k == "begin_time" ){
				if( !empty($v) ){
					$wheres .= " AND create_time >= '" .$v. "'";
				}
				continue;
			}
			if( $k == "end_time" ){
				if( !empty($v) ){
					$wheres .= " AND create_time <= '" .$v. "'";
				}
				continue;
			}
			
			$wheres .= " AND ". $k . "=" .$v;
		}
		
		$query  = "SELECT award_id,count(id) AS arount FROM ".WSY_SHOP.".slyder_adventures_reward WHERE ";
		$query  = $query . $wheres;
		$query .= " GROUP BY award_id";
		$result = $this->db->getAll($query);
		$return_msg["data"] = $result;
		
		return $return_msg;
	}
	
	
	/*
      * 查询轮盘抽奖 保存参与记录方法
      * @param  array value 插入值
      * Author: liquanhui $
      * 2017-11-10
      */
	public function name_list_add($value=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";

		if(empty($value)){
			$result["errcode"] = 40002;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		}
		 
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_record',$value, 'insert');
		if(!$res){
			$result["errcode"] = 40004;
			$result["errmsg"]  = "slyder_adventures_record插入失败！";
			return $result;
		}
		$result["newId"] = $this->db->insert_id();
		
		return $result;
	}
	
	
	/*
      * 查询轮盘抽奖 保存中奖记录方法
      * @param  array value 插入值
      * Author: liquanhui $
      * 2017-11-10
      */
	public function reward_list_add($value=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";

		if(empty($value)){
			$result["errcode"] = 40002;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		}
		 
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_reward',$value, 'insert');
		if(!$res){
			$result["errcode"] = 40004;
			$result["errmsg"]  = "slyder_adventures_reward插入失败！";
			return $result;
		}
		$result["newId"] = $this->db->insert_id();
		
		return $result;
	}

	/*
      * 我的中奖
      * @param  int   $data['customer_id'] 商家编号
      * @param  int   $data['user_id']     用户编号
      * @param  int   $data['page']        页数
      * @param  int   $data['limit']    每页条数
      * @param  int   $data['type']    中奖订单类型:0:全部;1:待领取;2:已领取;3:待发货;4:待收货;5;已完成;
      * Author: chenjunjie $
      * 2017-11-13
      */
	public function my_reward_select($data){
		$result = array();
		$result['errcode'] = 0;
		$result['errmsg'] = '';

		if(empty($data['customer_id'])){
			$result['errcode'] = 40002;
			$result['errmsg']  = 'customer_id丢失!';
			return $result;
		}
		if(empty($data['user_id'])){
			$result['errcode'] = 40002;
			$result['errmsg']  = 'user_id丢失!';
			return $result;
		}
		if(empty($data['page'])){
			$result['errcode'] = 40002;
			$result['errmsg']  = 'page丢失!';
			return $result;
		}

		$customer_id = $data['customer_id'];
		$user_id     = $data['user_id'];
		$type        = $data['type'];
		$page        = $data['page'];
		$limit       = $data['limit'];
		$now         = date('Y-m-d H:i:s',time());

		if(empty($page)||$page == 1){
			$begin = 0;
		}else{
			$begin = $limit*($page-1);
		}

		switch($type){
			case 0:
				$condition = "";
				break;
			case 1:
				$condition = " sar.isvalid = 1 and sar.status=0 and sar.award_expiry_date>'".$now."' and ";
				break;
			case 2:
				//$condition = " sar.isvalid = 1 and saro.isvalid = 1 and sar.status=1 and saro.status=3 and ";
				$condition = " sar.isvalid = 1 and sar.status=1 and ";
				break;
			case 3:
				$condition = " sar.isvalid = 1 and saro.isvalid = 1 and sar.status=1 and  saro.status=1 and ";
				break;
			case 4:
				$condition = " sar.isvalid = 1 and saro.isvalid = 1 and sar.status=1 and  saro.status=2 and ";
				break;
			case 5:
				$condition = " sar.isvalid = 1 and saro.isvalid = 1 and sar.status=1 and  saro.status=3 and ";
				break;
			default:
				$condition = "";
		}

		$where = $condition." sar.customer_id=".$customer_id." and sar.user_id=".$user_id;

//		$order = " sar.isvalid desc,sar.status desc,sar.award_expiry_date desc ";
		$order = " sar.isvalid desc,sar.status desc,sar.create_time desc ";

		$limit_query = " limit ".$begin.",".$limit;

		$query_all = "SELECT sar.id,sar.user_id,sar.customer_id,sar.isvalid,sar.award_level,sar.award_name,sar.award_expiry_date,sar.status as sar_status,sar.order_batchcode,sar.type,saa.img,saro.status as saro_status 
		FROM ".WSY_SHOP.".slyder_adventures_reward AS sar 
		INNER JOIN ".WSY_SHOP.".slyder_adventures_award AS saa ON sar.award_id = saa.id
		LEFT JOIN ".WSY_SHOP.".slyder_adventures_reward_orders AS saro ON sar.order_batchcode = saro.batchcode 
		where ".$where." order by ".$order.$limit_query;

		$query_total = "SELECT count(sar.id) as all_total FROM ".WSY_SHOP.".slyder_adventures_reward AS sar 
		INNER JOIN ".WSY_SHOP.".slyder_adventures_award AS saa ON sar.award_id = saa.id
		LEFT JOIN ".WSY_SHOP.".slyder_adventures_reward_orders AS saro ON sar.order_batchcode = saro.batchcode where ".$where;

		$result['data'] = $this->db->getAll($query_all);
		$total = $this->db->getRow($query_total);

		if(!empty($result['data']))
		{
			foreach ($result['data'] as &$value)
			{
				$value['img'] = addslashes($value['img']);
			}
		}

		$result['page']    = $data['page'];
		$result['limit']   = $data['limit'];
		$result['total']   = $total['all_total'];
		$result['sql']     = $query_all;

		return $result;
	}

	
	/*
      * 查询轮盘抽奖 基础设置
      * @param  int   $data['customer_id'] 商家编号
      * Author: zpd 
      * 2017-11-13
      */
	public function basic_setting_select($data){
		$result = array();
		$result['errcode'] = 0;
		$result['errmsg'] = '';

		if(empty($data['customer_id'])){
			$result['errcode'] = 40502;
			$result['errmsg']  = 'customer_id丢失!';
			return $result;
		}


		$customer_id = $data['customer_id'];


		$where = "and customer_id=".$customer_id;

		$query = "select id,is_open,is_display_person_center,is_display_my_records,description from ".WSY_SHOP.".slyder_adventures_basic_setting where isvalid=true ".$where;
		
		$msg = $this->db->getRow($query);
		if(empty($msg)){
			$result['errcode']    = 40503;
			$result['data']['id'] = -1;
		}else{
			$result['data'] = $msg;
		}
		return $result;
	}	
	
	
	 /*
      * 新增轮盘抽奖 基础设置
      * @param array $value 插入值
      * Author: zpq
      * 2017-11-13
      */
	public function basic_setting_insert($value=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";

		if( empty($value) ){
			$result["errcode"] = 40602;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		}
		
		$value['create_time'] = date('Y-m-d H:i:s',time());
		$value['isvalid']     = true;
		//var_dump($value);
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_basic_setting',$value, 'insert');
		if( !$res ){
			$result["errcode"] = 40604;
			$result["errmsg"]  = "slyder_adventures_basic_setting表 插入失败！";
			return $result;
		}
		$base_id = $this->db->insert_id();
		$result["base_id"] = $base_id;
		
		return $result;
	}


	 /*
      * 更新轮盘抽奖 基础设置
      * @param array $condition 条件
      * @param array $value    更新值
      * Author: zpd
      * 2017-11-04
      */
	public function basic_setting_update($condition=array(),$value=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";
		 
		if( empty($condition) ){
			$result["errcode"] = 40702;
			$result["errmsg"]  = "condition参数错误！";
			return $result;
		}
		if( empty($value) ){
			$result["errcode"] = 40703;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		}
		
		$condition['isvalid']  = true;
		 
		$wheres= "";
		foreach( $condition as $k => $v ){			
			$wheres .= ' AND '. $k . '=' .$v;
		}
		$wheres = substr($wheres,4);
		
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_basic_setting',$value, 'update',$wheres);
		if( !$res ){
			$result["errcode"] = 40704;
			$result["errmsg"]  = "slyder_adventures_basic_setting表 更新失败！";
			return $result;
		}
	
		return $result;
	}
	
	
	 /*
      * 查询查看中奖名单方法
      * @param int   pageNum 页数
      * @param array data
			['id']			int 活动编号
			['customer_id'] int 商家编号
      * Author: liquanhui $
      * 2017-11-04
      */
	 public function reward_list_select($pageNum=1,$data){
		$return_msg = array();
		$return_msg["errcode"] = 0;
        $return_msg["errmsg"]  = "success";
	

		$start = ($pageNum-1) * 20 ;
		$end = 20;
		$pageCount = 1;	//总页数
		$wheres= " wu.isvalid=true AND r.isvalid=true ";
		foreach( $data as $k => $v ){
			if( $k == "r.begin_time" ){
				if(!empty($v) ){
					$wheres .= " AND r.create_time >='" .$v. "'";
				}
				continue;
			}
			if( $k == "r.end_time" ){
				if(!empty($v) ){
					$wheres .= " AND r.create_time <='" .$v. "'";
				}
				continue;
			}
			
			$wheres .= " AND ". $k . "=" .$v;

		}
		
		$query = "SELECT r.id,r.user_id,r.award_id,r.award_level,r.award_name,r.create_time,wu.weixin_name FROM ".WSY_SHOP.".slyder_adventures_reward AS r INNER JOIN ".WSY_USER.".weixin_users AS wu ON wu.id=r.user_id  WHERE ";
		$query .= $wheres;
		
		$rcount_num = 0;	//总数据量
		$query_num  = "select count(r.id) as rcount from ".WSY_SHOP.".slyder_adventures_reward as r INNER JOIN ".WSY_USER.".weixin_users AS wu ON wu.id=r.user_id where ".$wheres; 
		$rcount_num = $this->db->getOne($query_num);
		
		$pageCount=ceil($rcount_num/$end);

		$query .= " ORDER BY r.create_time DESC limit ".$start.",".$end; 
		//echo $query;
		$result = $this->db->getAll($query);
		
		$return_msg["rcount_num"]= $rcount_num;
		$return_msg["pageCount"] = $pageCount;
		$return_msg["rewardList"]= $result;
		
		return $return_msg;

	 }

	 /*
      * 大转盘营销工具 订单详情（前端）
      * Author: zhangqiusong
      * 2017-11-14
      */
	function order_details_select($data){
		if($data['customer_id']){
			$customer_id = $data['customer_id'];
		  }
		if ($data['batchcode']) {
			$batchcode = $data['batchcode'];
		  }

		//查询用户ID user_id，订单状态 status,数量product_num,产品名称product_name,产品图片product_img,创建时间create_time
		$sql = "SELECT id,create_time,product_img,product_name,product_num,status,user_id,express_pirce 
		FROM ".WSY_SHOP.".slyder_adventures_reward_orders WHERE isvalid=true and customer_id=".$customer_id." and batchcode='".$batchcode."' LIMIT 1";
		$orders=$this->db->getAll ($sql);

		//查询发货信息
		$sql2 = "SELECT name,phone,location_p,location_c,location_a,address,create_time FROM ".WSY_SHOP.".slyder_adventures_reward_order_delivery WHERE isvalid=true and order_id='".$orders['0']['id']."' LIMIT 1";
		$delivery=$this->db->getAll ($sql2);

		//查询收货信息记录时间
		$sql3= "SELECT create_time FROM ".WSY_SHOP.".slyder_adventures_reward_order_address WHERE isvalid=true and order_id='".$batchcode."' LIMIT 1";
		$time=$this->db->getAll ($sql3);

		//查询是否领取，奖品编号号
		$sql4="SELECT status,award_id FROM ".WSY_SHOP.".slyder_adventures_reward where isvalid=true and order_batchcode='".$batchcode."' LIMIT 1";
		$award=$this->db->getAll ($sql4);

		//查询店铺名称
		$sql5 = "SELECT name FROM weixin_commonshops Where isvalid=true and customer_id=".$customer_id." LIMIT 1";
		$customer_name=$this->db->getAll ($sql5);

		//查询快递单号
		$sql6 = "SELECT express_num FROM ".WSY_SHOP.".slyder_adventures_reward_order_delivery Where isvalid=true and order_id='".$orders['0']['id']."' LIMIT 1";
		$express=$this->db->getAll ($sql6);

		//查询出对应的reward_id
		// var_dump($award['0']['award_id']);
		$sql7 = "SELECT id FROM ".WSY_SHOP.".slyder_adventures_reward where isvalid=true and award_id='".$award['0']['award_id']."' LIMIT 1";
		$reward_id= $this->db->getAll($sql7);
		
		//查询收货地址
		$sql8 = "SELECT name,phone,location_p,location_c,location_a,address,create_time FROM ".WSY_SHOP.".slyder_adventures_reward_order_address WHERE isvalid=true and order_id='".$orders['0']['id']."' LIMIT 1";
		$order_address=$this->db->getAll ($sql8);

		$result['orders']            = $orders;
		$result['delivery']          = $delivery;
		$result['time']              = $time;
		$result['award']             = $award;
		$result['reward_id']         = $reward_id;
		$result['express_num']       = $express;
		$result['brand_supply_name'] = $customer_name;
		$result['order_address']     = $order_address;
		return $result;
	}
	
	
	 /*
      * 大转盘营销工具 订单管理列表
      * Author: zpd
      * 2017-11-14
      */
	function order_lists_select($condition=array(),$pageNum=1,$pageSize=20,$condition_ext=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";
		$result["data"]    = array();
		
		$customer_id = $condition['orders.customer_id'];
		if(empty($customer_id)){
			$result['errcode'] = 40202;
			$result['errmsg']  = 'customer_id丢失!';
			return $result;
		}		
		
		/*搜索条件*/
		$wheres= " orders.isvalid=true ";
		foreach( $condition as $k => $v ){
			if( $k == "pay_begintime" ){
				if(!empty($v) ){
					$wheres .= " AND UNIX_TIMESTAMP(orders.pay_time)>=".strtotime($v);
				}
				continue;
			}
			if( $k == "pay_endtime" ){
				if(!empty($v) ){
					$wheres .= " AND UNIX_TIMESTAMP(orders.pay_time)<=".strtotime($v);
				}
				continue;
			}
			
			if( $k == "search_begintime" ){
				if(!empty($v) ){
					$wheres .= " AND UNIX_TIMESTAMP(orders.create_time)>=".strtotime($v);
				}
				continue;
			}
			if( $k == "search_endtime" ){
				if(!empty($v) ){
					$wheres .= " AND UNIX_TIMESTAMP(orders.create_time)<=".strtotime($v);
				}
				continue;
			}				
			
			if( $k == "orders.batchcode" ){
				if(!empty($v) ){
					$wheres .= " AND orders.batchcode like '%".$v."%'";
				}
				continue;
			}		

			if( $k == "address.phone" ){
				if(!empty($v) ){
					$wheres .= " AND address.phone like '%".$v."%'";
				}
				continue;
			}				
			
			$wheres .= " AND ". $k . "=" .$v;

		}
		
			//查询用户
		if(!empty($condition_ext['search_name'])){
			switch($condition_ext['search_name_type']){
				case 1:
					$query_name = 'SELECT id FROM '.WSY_USER.'.weixin_users WHERE weixin_name like "%'.$condition_ext['search_name'].'%" AND customer_id='.$customer_id;
					$wheres_2 .= " AND orders.user_id IN (".$query_name.")";				
				break;
				case 2:
					$query_name = "SELECT order_id FROM ".WSY_SHOP.".slyder_adventures_reward_order_address WHERE name like '%".$condition_ext['search_name']."%' AND customer_id=".$customer_id;
					$wheres_2 .= " AND orders.id IN (".$query_name.")";				
				break;
			}
		}
			//查询用户 End
			
			//产品名称
		if(!empty($condition_ext['orders.product_name'])){
			$wheres_2 .= " AND orders.product_name like '%".$condition_ext['orders.product_name']."%'";
		}
			//产品名称 End
		
		/*搜索条件 End*/	
		
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize; 		

		
		$query = "SELECT orders.id,orders.user_id,orders.batchcode,orders.product_name,orders.product_num,orders.create_time,orders.status,orders.send_status,orders.express_pirce,orders.product_img,orders.pay_status,orders.pay_time,orders.pay_type,orders.remark,
		address.location_p,address.location_c,address.location_a,address.address,address.name as a_name,address.phone as a_phone,
		users.weixin_name,users.weixin_fromuser	,users.name	
		FROM ".WSY_SHOP.".slyder_adventures_reward_orders AS orders 
		LEFT JOIN ".WSY_USER.".weixin_users AS users ON users.id=orders.user_id  
		LEFT JOIN ".WSY_SHOP.".slyder_adventures_reward_order_address AS address ON address.order_id=orders.id  and address.isvalid=true
		WHERE ";
		
		$query  = $query . $wheres . $wheres_2." ORDER BY orders.id DESC";		
		$query .=  " limit ".$start.",".$end;
		//return;
		$result["data"] = $this->db->getAll($query);

		return $result;
	}	
	

	 /*
      * 大转盘营销工具 订单管理列表
      * Author: zpd
      * 2017-11-14
      */
	function order_lists_num($condition=array(),$condition_ext=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";
		$result["data"]    = array();
		
		$customer_id = $condition['orders.customer_id'];
		if(empty($customer_id)){
			$result['errcode'] = 40202;
			$result['errmsg']  = 'customer_id丢失!';
			return $result;
		}		
		
		
		/*搜索条件*/
		$wheres= " orders.isvalid=true ";
		foreach( $condition as $k => $v ){
			if( $k == "pay_begintime" ){
				if(!empty($v) ){
					$wheres .= " AND UNIX_TIMESTAMP(orders.pay_time)>=".strtotime($v);
				}
				continue;
			}
			if( $k == "pay_endtime" ){
				if(!empty($v) ){
					$wheres .= " AND UNIX_TIMESTAMP(orders.pay_time)<=".strtotime($v);
				}
				continue;
			}
			
			if( $k == "search_begintime" ){
				if(!empty($v) ){
					$wheres .= " AND UNIX_TIMESTAMP(orders.create_time)>=".strtotime($v);
				}
				continue;
			}
			if( $k == "search_endtime" ){
				if(!empty($v) ){
					$wheres .= " AND UNIX_TIMESTAMP(orders.create_time)<=".strtotime($v);
				}
				continue;
			}				
			
			if( $k == "orders.batchcode" ){
				if(!empty($v) ){
					$wheres .= " AND orders.batchcode like '%".$v."%'";
				}
				continue;
			}		

			if( $k == "address.phone" ){
				if(!empty($v) ){
					$wheres .= " AND address.phone like '%".$v."%'";
				}
				continue;
			}				
			
			$wheres .= " AND ". $k . "=" .$v;

		}
		
			//查询用户
		if(!empty($condition_ext['search_name'])){
			switch($condition_ext['search_name_type']){
				case 1:
					$query_name = 'SELECT id FROM '.WSY_USER.'.weixin_users WHERE weixin_name like "%'.$condition_ext['search_name'].'%" AND customer_id='.$customer_id;
					$wheres_2 .= " AND orders.user_id IN (".$query_name.")";				
				break;
				case 2:
					$query_name = "SELECT order_id FROM ".WSY_SHOP.".slyder_adventures_reward_order_address WHERE name like '%".$condition_ext['search_name']."%' AND customer_id=".$customer_id;
					$wheres_2 .= " AND orders.id IN (".$query_name.")";				
				break;
			}
		}
			//查询用户 End
			
			//产品名称
		if(!empty($condition_ext['orders.product_name'])){
			$wheres_2 .= " AND orders.product_name like '%".$condition_ext['orders.product_name']."%'";
		}
			//产品名称 End
		
		/*搜索条件 End*/

		$query = "SELECT count(orders.id) as num	
		FROM ".WSY_SHOP.".slyder_adventures_reward_orders AS orders
		LEFT JOIN ".WSY_SHOP.".slyder_adventures_reward_order_address AS address ON address.order_id=orders.id  and address.isvalid=true	
		WHERE ";
		
		$query  = $query . $wheres . $wheres_2;
		$result["data"] = $this->db->getRow($query);

		return $result;
	}	
	


	 /*
      * 查询订单操作日志
      * @param int   order_id 订单编号
      * Author: zpd 
      * 2017-11-15
      */
	public function order_log_select($order_id){
		$return_log = array();
		$return_log["errcode"] = 0;
        $return_log["errmsg"]  = "success";
	

		$query = "select operation,descript,operation_user,create_time from ".WSY_SHOP.".slyder_adventures_reward_order_logs where isvalid = true and order_id='".(int)$order_id."'";
		//echo $query;
		$result = $this->db->getAll($query);
		if(empty($result)){
			$return_log['errcode']  = 41003;
			$return_log["errmsg"]  = "暂无日志";
		}else{
			$return_log["log"]= $result;
		}
		
		return $return_log;

	}


	 /*
      * 订单日志插入
      * @param array $value 插入值
      * Author: zpd
      * 2017-11-04
      */
	public function order_log_insert($value=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";

		if(empty($value)){
			$result["errcode"] = 41202;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		}
		
		$value['create_time'] = date("Y-m-d H:i:s",time()); //日志创建时间
		 
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_reward_order_logs',$value, 'insert');
		if(!$res){
			$result["errcode"] = 41203;
			$result["errmsg"]  = "slyder_adventures_reward_order_logs 插入失败！";
			return $result;
		}
		$log_id = $this->db->insert_id();
		$result["log_id"] = $log_id;
		
		return $result;
	}
	

	 /*
      * 查询订单状态
      * @param int   order_id 订单编号
      * Author: zpd 
      * 2017-11-15
      */
	public function order_status_select($data){
		$return_s = array();
		$return_s["errcode"] = 0;
        $return_s["errmsg"]  = "success";
	
		$customer_id  = $data['customer_id'];
		$order_id     = $data['order_id'];
	
		$query = "select status,user_id,product_name from ".WSY_SHOP.".slyder_adventures_reward_orders where isvalid = true and id='".(int)$order_id."' and customer_id=".$customer_id." limit 1";
		//echo $query;
		$result = $this->db->getRow($query);
		if(empty($result)){
			$return_s['errcode']  = 47002;
			$return_s["errmsg"]  = "查询不到订单";
		}else{
			$return_s["status"]       = $result['status'];
			$return_s["user_id"]      = $result['user_id'];
			$return_s["product_name"] = $result['product_name'];
		}
		
		return $return_s;

	}	
	


	 /*
      * 订单状态更新
      * @param array $condition 条件
      * @param array $value    更新值
      * Author: zpd
      * 2017-11-04
      */
	public function order_status_update($condition=array(),$value=array()){
		$result = array();
		$result["errcode"] = 0;
		$result["errmsg"]  = "success";
		 
		if( empty($condition) ){
			$result["errcode"] = 51002;
			$result["errmsg"]  = "condition参数错误！";
			return $result;
		}
		if( empty($value) ){
			$result["errcode"] = 51003;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		}
		 
		$condition['isvalid'] = true; 
		$wheres= "";
		foreach( $condition as $k => $v ){

			$wheres .= ' AND '. $k . '=' .$v;

		}
		$wheres = substr($wheres,4);
		
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_reward_orders',$value, 'update',$wheres);
		if( !$res ){
			$result["errcode"] = 51004;
			$result["errmsg"]  = "slyder_adventures_reward_orders 更新失败！";
			return $result;
		}
	
		return $result;
	}	
	

	 /*
      * 订单发货记录插入
      * @param array $value 插入值
      * Author: zpd
      * 2017-11-04
      */
	public function order_delivery_insert($value=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";

		if(empty($value)){
			$result["errcode"] = 51202;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		}
		
		$value['create_time'] = date("Y-m-d H:i:s",time()); //日志创建时间
		$value['isvalid']     = true;
		 
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_reward_order_delivery',$value, 'insert');
		if(!$res){
			$result["errcode"] = 51203;
			$result["errmsg"]  = "slyder_adventures_reward_order_delivery 插入失败！";
			return $result;
		}
		$delivery_id = $this->db->insert_id();
		$result["delivery_id"] = $delivery_id;
		
		return $result;
	}	
	
	
	
	 /*
      * 查询订单发货快递方式
	  * 传入参数customer_id
      * Author: zpd 
      * 2017-11-15
      */
	public function order_delivery_select($order_id){
		$return_express = array();
		$return_express["errcode"] = 0;
        $return_express["errmsg"]  = "success";
	
		$query = "SELECT name,phone,location_p,location_c,location_a,address,remark,express_num,express_id,receive_time,create_time FROM ".WSY_SHOP.".slyder_adventures_reward_order_delivery where isvalid=true  AND order_id=" . $order_id;
		//echo $query;
		$result = $this->db->getRow($query);
		if(empty($result)){
			$return_express['errcode']  = 43004;
			$return_express["errmsg"]  = "发货地址查询失败";
		}else{
			$return_express["adress"]= $result;
		}
		
		return $return_express;

	}	
	


	 /*
      * 更新活动奖项
      * @param array $condition 条件
      * @param array $value    更新值
      * Author: liquanhui $
      * 2017-11-04
      */
	public function order_delivery_auto_receivetime_update($condition,$day){
		$result = array();
		$result["errcode"] = 0;
		$result["errmsg"]  = "success";

		if( empty($condition) ){
			$result["errcode"] = 51402;
			$result["errmsg"]  = "condition 参数错误！";
			return $result;
		}
		
		if( empty($day) && $day !==0){
			$result["errcode"] = 51403;
			$result["errmsg"]  = "day 参数错误！";
			return $result;
		}
		
		$wheres= "";
		foreach( $condition as $k => $v ){			
			$wheres .= ' AND '. $k . '=' .$v;
		}
		$wheres = substr($wheres,4);		

		$query = "update ".WSY_SHOP.".slyder_adventures_reward_order_delivery set auto_receive_time = DATE_ADD( now(), INTERVAL ".$day." DAY ) where ".$wheres;
		$res = $this->db->query($query);
		if( !$res ){
			$result["errcode"] = 40004;
			$result["errmsg"]  = "slyder_adventures_award更新失败！";
			return $result;
		}
	
		return $result;
	}	
	
	
	
	 /*
      * 查询订单快递方式
	  * 传入参数customer_id
      * Author: zpd 
      * 2017-11-15
      */
	public function order_expresses_company_select($condition){
		$return_express = array();
		$return_express["errcode"] = 0;
        $return_express["errmsg"]  = "success";
	
		$customer_id = $condition['customer_id'];
		$where = " AND customer_id=" . $customer_id;
		if(!empty($condition['express_id'])){
			$where .= " AND id=" . $condition['express_id'];
		}
	
		$query = 'SELECT id,expresses_name FROM '.WSY_SHOP.'.weixin_expresses_company where isvalid=true '.$where." AND supply_id=-1 order by is_default desc";
		//echo $query;
		$result = $this->db->getAll($query);
		if(empty($result)){
			$return_express['errcode']  = 42003;
			$return_express["errmsg"]  = "暂无快递方式";
		}else{
			$return_express["expresses_company"]= $result;
		}
		
		return $return_express;

	}	
	
	
	 /*
      * 更新订单收货地址
      * @param array $condition 条件
      * @param array $value    更新值
      * Author: zpd
      * 2017-11-04
      */
	public function order_address_update($condition=array(),$value=array()){
		$result = array();
		$result["errcode"] = 0;
		$result["errmsg"]  = "success";
		 
		if( empty($condition) ){
			$result["errcode"] = 50002;
			$result["errmsg"]  = "condition参数错误！";
			return $result;
		}
		if( empty($value) ){
			$result["errcode"] = 50003;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		}
		 
		$wheres= "";
		foreach( $condition as $k => $v ){

			$wheres .= ' AND '. $k . '=' .$v;

		}
		$wheres = substr($wheres,4);
		
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_reward_order_address',$value, 'update',$wheres);
		if( !$res ){
			$result["errcode"] = 50004;
			$result["errmsg"]  = "slyder_adventures_reward_order_address 更新失败！";
			return $result;
		}
	
		return $result;
	}	
	
	
	
	 /*
      * 查询订单快递方式
	  * 传入参数customer_id
      * Author: zpd 
      * 2017-11-15
      */
	public function order_address_select($order_id){
		$return_express = array();
		$return_express["errcode"] = 0;
        $return_express["errmsg"]  = "success";
	
		$query = "SELECT name,phone,location_p,location_c,location_a,address FROM ".WSY_SHOP.".slyder_adventures_reward_order_address where isvalid=true  AND order_id=" . $order_id;
		//echo $query;
		$result = $this->db->getRow($query);
		if(empty($result)){
			$return_express['errcode']  = 43003;
			$return_express["errmsg"]  = "发货地址查询失败";
		}else{
			$return_express["adress"]= $result;
		}
		
		return $return_express;

	}		
	



	 /*
      * 查询订单快递方式
	  * 传入参数customer_id
      * Author: zpd 
      * 2017-11-15
      */
	public function order_wx_pay_select($out_trade_no){
		$return_wx = array();
		$return_wx["errcode"] = 0;
        $return_wx["errmsg"]  = "success";
	
		$query = "select transaction_id from ".WSY_PAY.".weixin_weipay_notifys where isvalid=true and out_trade_no='".$out_trade_no."'";
		//echo $query;
		$result = $this->db->getRow($query);
		if(empty($result)){
			$return_wx['errcode']  = 44003;
			$return_wx["errmsg"]  = "支付订单号查询失败";
		}else{
			$return_wx["wx_pay"]= $result;
		}
		
		return $return_wx;

	}	
	
	

	/*
	  * 中奖领取订单详情
      * Author: chenjunjie
      * 2017-11-14
	 */
	function reward_detail($data){
		$result['errcode'] = 0;
		$result['errmsg']  = '';


		$customer_id = $data['customer_id'];
		$user_id     = $data['user_id'];
		$aid         = $data['aid'];
		$id          = $data['id'];

		if(empty($id)){
			$result['errcode'] = 40002;
			$result['errmsg']  = 'id丢失!';
			return $result;
		}
		if(empty($user_id)){
			$result['errcode'] = 40002;
			$result['errmsg']  = 'user_id丢失!';
			return $result;
		}
		if(empty($customer_id)){
			$result['errcode'] = 40002;
			$result['errmsg']  = 'customer_id丢失!';
			return $result;
		}

		//获取商城style
		$result['skin_data'] = $this->get_style($customer_id);

		//获取商城名称
		$result['shop_name'] = $this->db->getOne("select DISTINCT name from weixin_commonshops where customer_id=".$customer_id);

		//获取地址
		if($aid>0){
			$query_address="select id,name,phone,address,location_p,location_c,location_a,is_default,identity,identityimgt,identityimgf from ".WSY_SHOP.".weixin_commonshop_addresses where isvalid=true and user_id=".$user_id."  and id =".$aid;
		}else{
			$query_address="select id,name,phone,address,location_p,location_c,location_a,is_default,identity,identityimgt,identityimgf from ".WSY_SHOP.".weixin_commonshop_addresses where isvalid=true and user_id=".$user_id." and is_default = 1";
		}

		$res_address            = $this->db->getRow($query_address);
		$address['aid']   = $res_address['id'];
		$address['add_name']   = $res_address['name'];
		$address['add_phone']  = $res_address['phone'];
		$address['location_p'] = $res_address['location_p'];
		$address['location_c'] = $res_address['location_c'];
		$address['location_a'] = $res_address['location_a'];
		$address['location_add'] = $res_address['address'];
		$address['address']   = $res_address['location_p']." ".$res_address['location_c']." ".$res_address['location_a']." ".$res_address['address'];
		$result['address']    = $address;


		//获取中奖的产品参数
		$query_reward = "SELECT sar.id,sar.award_id,sar.user_id,sar.customer_id,sar.award_name,sar.award_expiry_date,sar.order_batchcode,sar.slyder_id,saa.img,saa.express_price FROM ".WSY_SHOP.".slyder_adventures_reward AS sar INNER JOIN ".WSY_SHOP.".slyder_adventures_award AS saa ON sar.award_id = saa.id where sar.id=".$id." and sar.user_id=".$user_id." and sar.status=0 and sar.isvalid=1";

		$res_reward      = $this->db->getRow($query_reward);
		$result['reward'] = $res_reward;


		return $result;
//		return $res_address;
	}

	/*
     * 获取商城styles
	 * Author: chenjunjie
      * 2017-11-14
     */
	function get_style($cust_id)
	{
		$sql = "select id,list_type from weixin_commonshops where isvalid=true and customer_id=".$cust_id." limit 0,1";
		$res = $this->db->getRow($sql);

		$now_skin = !empty($res['list_type'])?$res['list_type']:1;

		switch ($now_skin) {
			case '1':
				$images_skin='images';
				$skin = "orange";
				break;
			case '2':
				$images_skin='images_red';
				$skin = "red";
				break;
			case '3':
				$images_skin='images_blue';
				$skin = "blue";
				break;
			case '4':
				$images_skin='images_green';
				$skin = "green";
				break;
			case '5':
				$images_skin='images_black';
				$skin = "black";
				break;
			case '6':
				$images_skin='images_pruple';
				$skin = "pruple";  //pruple虽然拼错了，但是将错就错。没必要改了----
				break;
			default:
				$images_skin='images';
				$skin = "orange";
				break;
		}

		$data['images_skin'] = $images_skin;
		$data['skin']         = $skin;
		return $data;
	}


	/*
	  * 获取奖项信息，并领取奖励
      * Author: liquanhui
      * 2017-11-14
	 */
	function find_act_reward($data){
		$return_msg["errcode"] = 0;
		$return_msg["errmsg"]  = "success";
		
		$date_now = date("Y-m-d H:i:s",time());
		
		if(empty($data["user_id"]) || $data["user_id"]<1){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "user_id参数错误！";
			return $return_msg;
		}
		if(empty($data["reward_id"]) || $data["reward_id"]<1){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "reward_id参数错误！";
			return $return_msg;
		}
		if(empty($data["customer_id"]) || $data["customer_id"]<1){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "customer_id参数错误！";
			return $return_msg;
		}
		
		//查询奖项信息
		$query = "SELECT a.coupon_id,a.award_type,r.status,r.award_expiry_date FROM ".WSY_SHOP.".slyder_adventures_reward AS r 
				  INNER JOIN ".WSY_SHOP.".slyder_adventures_award AS a ON a.id = r.award_id 
				  WHERE a.isvalid=true AND r.isvalid=true AND r.user_id=".$data["user_id"]." AND r.id=".$data["reward_id"]." LIMIT 1";
		$result= $this->db->getRow($query);
		
		if(!$result){
			$return_msg["errcode"] = 40003;
			$return_msg["errmsg"]  = "奖项不存在！";
			return $return_msg;
		}
		if($result["status"] == 1){
			$return_msg["errcode"] = 40004;
			$return_msg["errmsg"]  = "请勿重复领取！";
			return $return_msg;
		}
		if($result["award_expiry_date"] < $date_now){
			$return_msg["errcode"] = 40004;
			$return_msg["errmsg"]  = "奖项已过期，无法领取！";
			return $return_msg;
		}
		
		if($result["award_type"] == 1){
			
			//优惠券
			if($result['coupon_id']<1){
				$return_msg["errcode"] = 40004;
				$return_msg["errmsg"]  = "coupon_id参数错误！";
				return $return_msg;
			}else{
				//领取优惠券
				$data['coupon_id']=$result['coupon_id'];
				$result = $this->get_coupon($data);
				if($result["errcode"] != 0){
					//领取失败
					return $result;
				}else{
					//领取成功，更新奖项状态已领取
					$this->db->autoExecute(WSY_SHOP.'.slyder_adventures_reward', array('status'=>1), 'update',"id = ".$data["reward_id"]) ;
				}
			}
			
		}else if($result["award_type"] == 2){
			
			$return_msg["errcode"] = 40004;
			$return_msg["errmsg"]  = "领取奖品不在这里！";
			return $return_msg;
			
		}

		return $return_msg;
	}
	
	
	/*
	  * 领取优惠券
      * Author: liquanhui
      * 2017-11-14
	 */
	function get_coupon($data){
		$return_msg["errcode"] = 0;
		$return_msg["errmsg"]  = "success";		
		
		if(empty($data["user_id"]) || $data["user_id"]<1){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "user_id参数错误！";
			return $return_msg;
		}
		if(empty($data["customer_id"]) || $data["customer_id"]<1){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "customer_id参数错误！";
			return $return_msg;
		}
		if($data['coupon_id'] < 1 || empty($data['coupon_id'])){
			$return_msg["errcode"] = 40004;
			$return_msg["errmsg"]  = "coupon_id参数错误！";
			return $return_msg;
		}
		
		//查询优惠券信息
		$query = "select id,MinMoney,MaxMoney,NeedMoney,Days,DaysType,get_roles,use_roles,MoneyType,startline,class_type,couponNum,personNum,CanGetNum,getStartTime,getEndTime,storenum from ".WSY_SHOP.".weixin_commonshop_coupons where isvalid=true and id=".$data['coupon_id']." limit 1";
		$coupon = $this->db->getRow($query);
		if(!$coupon){
			$return_msg["errcode"] = 40004;
			$return_msg["errmsg"]  = "暂无优惠券！";
			return $return_msg;
		}
		
		$code = "d".$data['user_id'].strtotime(date('Y-m-d H:i:s'));
		
		$deadline = "1970-01-01 00:00";
		$startline = $coupon['startline'];
		if($coupon['DaysType']==1){
			$deadline = $coupon['Days'];
			$end_day  = strtotime($coupon['Days']);
		}else{
			$deadline  = date('Y-m-d H:i:s',strtotime("+".$coupon['Days']." day"));
			$startline = date("Y-m-d H:i:s",time());
			$end_day   = strtotime("+".$coupon['Days']." day");
		}
		
		if($coupon['MoneyType']==0){
			$CouponMoney = $coupon['MaxMoney'];//固定金额
		}else{
			$CouponMoney = rand($coupon['MinMoney'],$coupon['MaxMoney']);//获取随机金额
		}
		
		$value = array(
			"code"			=>	$code,
			"user_id"		=>	$data['user_id'],
			"customer_id"	=>	$data['customer_id'],
			"Money"			=>	$CouponMoney,
			"deadline"		=>	$deadline,
			"NeedMoney"		=>	$coupon['NeedMoney'],
			"type"			=>	1,
			"is_used"		=>	0,
			"isvalid"		=>	true,
			"createtime"	=>	date("Y-m-d H:i:s"),
			"class_type"	=>	$coupon['class_type'],
			"coupon_id"		=>	$data['coupon_id'],
			"use_roles"		=>	$coupon['use_roles'],
			"coupon_use_inentity"	=>	'1_-1',
			"is_receive"	=>	1,
			"startline"		=>	$startline,
			"source"		=>	7,
			"pid"			=>	-1,
		);
		$res = $this->db->autoExecute(WSY_USER.'.weixin_commonshop_couponusers',$value, 'insert');
		if( !$res ){
			$return_msg["errcode"] = 40006;
			$return_msg["errmsg"]  = "优惠券领取失败！";
			return $return_msg;
		}
		
		return $return_msg;
	}
	
	 /*
	  * 该确认收货了
      * Author: zhangqiusong
      * 2017-11-15
	 */
	function order_confirm($data){
		$return_msg["errcode"] = 0;
		$return_msg["errmsg"]  = "success";
		
		if(empty($data["batchcode"])){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "batchcode参数错误！";
			return $return_msg;
		}
		if(empty($data["customer_id"])){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "customer_id参数错误！";
			return $return_msg;
		}
		$customer_id    =  $data['customer_id'];
		$batchcode      =  $data['batchcode'];
		$where = "isvalid=true and customer_id=".$customer_id." and batchcode='".$batchcode."'";
		$res = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_reward_orders', array('status'=>3), 'update',$where);
			if( !$res ){
			$return_msg["errcode"] = 40006;
			$return_msg["errmsg"]  = "gggg";
			return $return_msg;
		}
	}
	
	
	/*
	  * 保存奖品订单
      * Author: liquanhui
      * 2017-11-15
	 */
	function save_reward_order($data){
		$return_msg["errcode"] = 0;
		$return_msg["errmsg"]  = "success";
		
		$date_now = date("Y-m-d H:i:s",time());
		
		if(empty($data["user_id"])){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "user_id参数错误！";
			return $return_msg;
		}
		if(empty($data["customer_id"])){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "customer_id参数错误！";
			return $return_msg;
		}
		if(empty($data["aid"])){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "aid参数错误！";
			return $return_msg;
		}
		if(empty($data["award_id"])){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "award_id参数错误！";
			return $return_msg;
		}
		if(empty($data["reward_id"])){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "reward_id参数错误！";
			return $return_msg;
		}
		
		
		//事务处理
		$this->db->tran_begin();
		try{
			//查询奖项信息
			$query = "SELECT r.award_name,r.slyder_id,r.award_expiry_date,a.img,a.express_price FROM ".WSY_SHOP.".slyder_adventures_reward AS r 
					  INNER JOIN ".WSY_SHOP.".slyder_adventures_award AS a ON a.id = r.award_id 
					  WHERE a.isvalid=true AND r.isvalid=true AND r.user_id=".$data["user_id"]." AND r.id=".$data["reward_id"]." AND a.id=".$data["award_id"]." LIMIT 1";
			$reward_res = $this->db->getRow($query);
			
			if(!$reward_res){
				$return_msg["errcode"] = 40003;
				$return_msg["errmsg"]  = "奖项信息有误！";
				return $return_msg;
			}

			if($reward_res["award_expiry_date"] < $date_now){
				$return_msg["errcode"] = 40004;
				$return_msg["errmsg"]  = "奖项已过期，无法领取！";
				return $return_msg;
			}
			
			//生成订单号
			$batchcode = $this->create_batchcode($data["user_id"]);
		
			//保存订单
			$value1 = array(
				"batchcode"		=>	$batchcode,
				"create_time"	=>	date("Y-m-d H:i:s",time()),
				"product_img"	=>	$reward_res["img"],
				"product_name"	=>	mysql_escape_string($reward_res["award_name"]),
				"product_num"	=>	1,
				"status"		=>	4,
				"pay_status"	=>	0,
				"send_status"	=>	0,
				"customer_id"	=>	$data["customer_id"],
				"user_id"		=>	$data["user_id"],
				"isvalid"		=>	true,
				"express_pirce"	=>	$reward_res["express_price"],
				"slyder_id"		=>	$reward_res["slyder_id"],
				"remark"		=>	mysql_escape_string($data["remark"]),
			);	
			_mysql_query("BEGIN");
			$result  = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_reward_orders', $value1, 'insert') ;
			$order_id= $this->db->insert_id();
			
			//查询地址信息
			$query_add ="select id,name,phone,address,location_p,location_c,location_a,is_default,identity,identityimgt,identityimgf from ".WSY_SHOP.".weixin_commonshop_addresses where isvalid=true and user_id=".$data['user_id']."  and id =".$data['aid'];
			$reward_add = $this->db->getRow($query_add);

			if(!$reward_add){
				
				$this->db->tran_rollback();
				
				$return_msg["errcode"] = 40003;
				$return_msg["errmsg"]  = "收获地址有误！";
				return $return_msg;
			}
			
			//保存订单地址
			$value2 = array(
				"order_id"		=>$order_id,
				"name"			=>mysql_escape_string($reward_add["name"]),
				"phone"			=>mysql_escape_string($reward_add["phone"]),
				"location_p"	=>mysql_escape_string($reward_add["location_p"]),
				"location_c"	=>mysql_escape_string($reward_add["location_c"]),
				"location_a"	=>mysql_escape_string($reward_add["location_a"]),
				"address"		=>mysql_escape_string($reward_add["address"]),
				"create_time"	=>date("Y-m-d H:i:s",time()),
				"isvalid"		=>true,
				"customer_id"	=>$data["customer_id"],
			);
			$result  = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_reward_order_address', $value2, 'insert') ;
			
			
			//关联奖项订单号
			$result = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_reward', array('order_batchcode'=>$batchcode), 'update',"user_id = '".$data["user_id"]."' and id = ".$data["reward_id"]) ;
			_mysql_query("COMMIT");
			
			
			//插入订单日志
			$date_log["order_id"] = $order_id;
			$date_log["operation"] = 0;
			$date_log["operation_user"] = $data["user_id"];
			$this->insert_order_log($date_log);
			
			$return_msg["batchcode"] = $batchcode; 
					
		}catch(Exception $e){
			$this->db->tran_rollback();
			echo '系统错误，请稍后重试'; exit;
		}
		$this->db->tran_commit();
		
		return $return_msg;
		
	}
	
	
	/*
	  * 写入订单日志
      * Author: liquanhui
      * 2017-11-15
	 */
	function insert_order_log($data){
		$return_msg["errcode"] = 0;
		$return_msg["errmsg"]  = "success";
		
		if(empty($data["order_id"])){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "order_id参数错误！";
			return $return_msg;
		}
		if($data["operation"]<0){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "operation参数错误！";
			return $return_msg;
		}
		if(empty($data["operation_user"])){
			$return_msg["errcode"] = 40002;
			$return_msg["errmsg"]  = "operation_user参数错误！";
			return $return_msg;
		}
		
		//订单操作；0：下单；1：取消；2：支付；3：修改价格；4：发货：5：申请延期；6：确认延期；7：确认收货；8：退货；9：退货审批；10：未发货退款；11：审批退款；12：退款；13：用户退货填单；14：商家确认退货；15：退货完成；16：确认完成；17：订单评价；18：申请维权；19：维权审批；20：维权处理；21：微信退款操作；23：维权扣除供应商款项；30:系统派单；31:运费修改；32：威富通退款；33：健康钱包退款；34：修改快递单号，快递公司
		switch($data["operation"]){
			case 0:
				$descript = "下单";
			break;
			case 1:
				$descript = "取消";
			break;
			case 2:
				$descript = "支付";
			break;
			case 3:
				$descript = "修改价格";
			break;
			case 4:
				$descript = "发货";
			break;
			case 5:
				$descript = "申请延期";
			break;
			case 6:
				$descript = "确认延期";
			break;
			case 7:
				$descript = "确认收货";
			break;
			case 8:
				$descript = "退货";
			break;
			case 9:
				$descript = "退货审批";
			break;
			case 10:
				$descript = "未发货退款";
			break;
			case 11:
				$descript = "审批退款";
			break;
			case 12:
				$descript = "退款";
			break;
			case 13:
				$descript = "用户退货填单";
			break;
			case 14:
				$descript = "商家确认退货";
			break;
			case 15:
				$descript = "退货完成";
			break;
			case 16:
				$descript = "确认完成";
			break;
			case 17:
				$descript = "订单评价";
			break;
			case 18:
				$descript = "申请维权";
			break;
			case 19:
				$descript = "维权审批";
			break;
			case 20:
				$descript = "维权处理";
			break;
			case 21:
				$descript = "微信退款操作";
			break;
			case 23:
				$descript = "维权扣除供应商款项";
			break;
			case 30:
				$descript = "系统派单";
			break;
			case 31:
				$descript = "运费修改";
			break;
			case 32:
				$descript = "威富通退款";
			break;
			case 33:
				$descript = "健康钱包退款";
			break;
			case 34:
				$descript = "修改快递单号，快递公司";
			break;
			default:
				$descript = "";
			break;

		}
		
		if( !empty($data["descript"]) ){
			$descript = $data["descript"];
		}
		
		$value = [];
		$value["order_id"]  = $data["order_id"];
		$value["operation"] = $data["operation"];
		$value["descript"]  = $descript;
		$value["operation_user"]  = $data["operation_user"];
		$value["create_time"]     = date("Y-m-d H:i:s",time());
		$value["isvalid"]   = true;
	
		$result  = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_reward_order_logs', $value, 'insert') ;
		
		return $return_msg;
	}
	
	//生成订单号
	//key_param 标识号
	function create_batchcode($key_param){
		$batchcode = "";
		
		/* 订单号随机3位数*/
		$arr_rand=array();
		while(count($arr_rand)<3)
		{
		  $arr_rand[]=rand(0,9);
		  $arr_rand=array_unique($arr_rand);
		}
		$str_rand = implode("",$arr_rand);
		/* 订单号随机3位数*/


		/*生成订单号开始*/
		$stringtime = date("Y-m-d H:i:s", time());
		$batchcode_time  = strtotime($stringtime);
		$batchcode  = $key_param . $batchcode_time . $str_rand;
		/*生成订单号结束*/
		
		return $batchcode;
	}
	
	//生成token随机数
	function create_token($key_param){
		$token = "";	//两个随机字母+时间戳+随机3位数
		
		/* 随机3个字母*/
		$code = "";          
		$Bchars = "abcdefghijklmnopqrstuvwxyz";
		for($i=0;$i<3;$i++){
			$code .= substr($Bchars, mt_rand(0, strlen($Bchars) - 1), 1);
		}
		/* 随机3个字母*/
		
		/* 随机3位数*/
		$arr_rand=array();
		while(count($arr_rand)<3)
		{
		  $arr_rand[]=rand(0,9);
		  $arr_rand=array_unique($arr_rand);
		}
		$str_rand = implode("",$arr_rand);
		/* 随机3位数*/


		/*生成token开始*/
		$stringtime = date("Y-m-d H:i:s", time());
		$strto_time  = strtotime($stringtime);
		$token  = $code . $strto_time . $str_rand;
		/*生成token结束*/

		return $token;
	}
	
	
	/*
	  * 用户下单增加轮盘抽奖机会
	  * 备注：/weixinpl/mshop/save_order_new.php使用了此方法
      * @param int    customer_id 商家编号
      * @param String batchcode   商城订单号
      * Author: liquanhui
      * 2017-11-17
	 */
	function add_slyder_adventures_order_chance($data){
		$return_msg["errcode"] = 0;
		$return_msg["errmsg"]  = "success";
		$return_msg["data"]["slyder_id"] = -1;  //活动编号
		$return_msg["data"]["chance"]    = 0;	//增加的机会
		$return_msg["data"]["token"]     = "";	//随机token
		$return_msg["data"]["batchcode"] = "";	//商城订单

		//查询正在进行中的活动
		$result_act = $this->select_now_action($data['customer_id']);
		if( $result_act["errcode"] != 0 ){
			$return_msg["errcode"] = $result_act["errcode"];
			$return_msg["errmsg"]  = $result_act["errmsg"];
			return $return_msg;
		}else{
			$actionList = $result_act["data"];
		}
		
		if( $actionList["type"] == 2 ){
			
			//查询订单信息
			$pay_price   = 0;	//订单付款金额
			$query_order = "SELECT price,paystatus,batchcode,user_id FROM weixin_commonshop_order_prices WHERE isvalid=true AND batchcode='".$data['batchcode']."' LIMIT 1";
			$order_info  = $this->db->getRow($query_order);
			if( !$order_info ){
				$return_msg["errcode"] = 40003;
				$return_msg["errmsg"]  = "不存在的订单！";
				return $return_msg;
			}
			// if( $order_info["paystatus"] != 1 ){
				// $return_msg["errcode"] = 40003;
				// $return_msg["errmsg"]  = "订单未付款！";
				// return $return_msg;
			// }
			if( $order_info["price"] > 0 ){
				$pay_price = $order_info["price"];
				$total_price = $pay_price;
			}
			
			//查询可增加的次数
			$chance = 0;
			$limit_order = $actionList["limit_order"];	//每人每单次数限制 -1为不限制 门槛存储方式为 (20_1,40_2,60_3)
			$limit_order_arr = explode(",",$limit_order);
			$limit_order_arr = array_reverse($limit_order_arr);	//倒序从大到小

			//订单实付款累计开启和关闭逻辑
			if ($actionList['is_fact_pay']  == 0) {
				$is_accumulation = 0;
				foreach($limit_order_arr AS $k=>$v){
					$money_chance_arr = explode("_",$v);
					if( $pay_price >= $money_chance_arr[0] ){
						$chance = $money_chance_arr[1];
						break;
					}
				}
				if( $chance <= 0 && $chance != -1 ){
					$return_msg["errcode"] = 40003;
					$return_msg["errmsg"]  = "没有次数可以增加！";
					return $return_msg;
				}
			} else {
				$sql  = "SELECT SUM(this_price) price FROM ".WSY_SHOP.".slyder_adventures_chance_extend WHERE isvalid=true AND customer_id=".$data['customer_id']." AND user_id=".$order_info['user_id']." AND is_accumulation=1";
				$num = $this->db->getOne($sql);

				if( $num > 0 ) {
					$total_price = $pay_price + $num;
				}

				$is_accumulation = 1;
				foreach($limit_order_arr AS $k=>$v){
					$money_chance_arr = explode("_",$v);
					if( $total_price >= $money_chance_arr[0] ){
						$chance = $money_chance_arr[1];
						$is_accumulation = 0;

						//满足条件的修改
						$map['is_accumulation'] = $is_accumulation;
						$map['isvalid'] 		= false;

						$where = "isvalid=true AND customer_id=".$data['customer_id']." AND user_id=".$order_info['user_id']." AND is_accumulation=1";
						$this->db->autoExecute(WSY_SHOP.'.slyder_adventures_chance_extend',$map, 'update',$where);

						break;
					}
				}
			}
			
			//查询该订单是否已新增次数
			$rount = 0;
			$query_num  = "SELECT count(id) rount FROM ".WSY_SHOP.".slyder_adventures_chance_extend WHERE isvalid=true AND customer_id=".$data['customer_id']." AND user_id=".$order_info['user_id']." AND batchcode='".$order_info['batchcode']."'";
			$rount = $this->db->getOne($query_num);
			if($rount > 0){
				$return_msg["errcode"] = 40003;
				$return_msg["errmsg"] = "此订单已经增加过机会了！";
				return $return_msg;
			}
			
			
			//如果是抽奖次数不可累计，生成token验证
			$token = "";
			if($actionList['cumulative_frequency_type'] == 3){
				$token = $this->create_token();
			}
			
			
			//增加抽奖次数
			$value = array(
				"customer_id"	  =>	$data['customer_id'],
				"user_id"		  =>	$order_info['user_id'],
				"isvalid"		  =>	true,
				"date"			  =>	date('Y-m-d'),
				"create_time"	  =>	date('Y-m-d H:i:s',time()),
				"batchcode"		  =>	$order_info['batchcode'],
				"price"			  =>	$total_price,//累计次数订金额
				"num"			  =>	$chance,
				"slyder_id"		  =>	$actionList['id'],
				"source"		  =>	1,
				"type"			  =>	$actionList['cumulative_frequency_type'],
				"token"			  =>	$token,
				'is_accumulation' => 	$is_accumulation,
				'this_price'      =>    $pay_price,//单笔订单金额
			);
			$result  = $this->db->autoExecute(WSY_SHOP.'.slyder_adventures_chance_extend', $value, 'insert') ;
			$extend_id = $this->db->insert_id();
			
			$return_msg["data"]["slyder_extend_id"]= $extend_id;
			$return_msg["data"]["slyder_id"]= $actionList['id'];
			$return_msg["data"]["chance"]   = $chance;
			$return_msg["data"]["token"]    = $token;			
			$return_msg["data"]["batchcode"]= $order_info['batchcode'];

		}else{
			$return_msg["errcode"] = 40003;
			$return_msg["errmsg"]  = "活动类型不正确，不增加次数！";
			return $return_msg;
		}
		
		return $return_msg;
	}
	
	/*
      * 订单详情页获取抽奖次数
      * @param int   order_id 订单编号
      * Author: zpd 
      * 2017-11-15
      */
	public function get_chance_extend_shop_order($extend_id,$user_id){
		$return_msg["errcode"] = 0;
        $return_msg["errmsg"]  = "success";
        $return_msg["data"]    = array();
		
		$datetime = date('Y-m-d H:i:s');
		
		$query = "SELECT batchcode,slyder_id,num,token,date,type FROM ".WSY_SHOP.".slyder_adventures_chance_extend WHERE isvalid=true AND user_id=".$user_id." AND id=".$extend_id." LIMIT 1";
		$extend_info = $this->db->getRow($query);
		if( !$extend_info ){
			$return_msg["errcode"] = 40009;
			$return_msg["errmsg"]  = "不存在抽奖次数！";
			return $return_msg;
		}
		
		switch($extend_info["type"]){
			case 1:
				if($extend_info["date"] != date("Y-m-d")){
					$return_msg["errcode"] = 40003;
					$return_msg["errmsg"]  = "额外抽奖次数已过期！";
					return $return_msg;
				}
			break;
			
			case 2:
			break;
			
			case 3:
				if(empty($extend_info["token"])){
					$return_msg["errcode"] = 40003;
					$return_msg["errmsg"]  = "token为空！";
					return $return_msg;
				}
			break;
			
			default:
			break;
			
		}

		$query = "SELECT begin_time,end_time,status FROM ".WSY_SHOP.".slyder_adventures_config WHERE isvalid=true AND id=".$extend_info['slyder_id']." LIMIT 1";
		$act_info = $this->db->getRow($query);
		if( !$act_info ){
			$return_msg["errcode"] = 40009;
			$return_msg["errmsg"]  = "不存在的活动！";
			return $return_msg;
		}
		if($act_info["status"] == 0){
			$return_msg["errcode"] = 40003;
			$return_msg["errmsg"]  = "活动未启用！";
			return $return_msg;
		}
		if($act_info["begin_time"] > $datetime || $act_info["end_time"] < $datetime){
			$return_msg["errcode"] = 40003;
			$return_msg["errmsg"]  = "活动不是进行中！";
			return $return_msg;
		}
		
		
		$return_msg["data"] = $extend_info;

		return $return_msg;
	}
	
	
	 /*
      * 查询订单状态
      * @param int   order_id 订单编号
      * Author: zpd 
      * 2017-11-15
      */
	public function user_openID_select($data){
		$return_user = array();
		$return_user["errcode"] = 0;
        $return_user["errmsg"]  = "success";
	
		$customer_id  = $data['customer_id'];
		$user_id      = $data['user_id'];
	
		$query = "select weixin_fromuser from ".WSY_USER.".weixin_users where isvalid=true and id=".$user_id." and customer_id=".$customer_id." limit 0,1";
		//echo $query;
		$result = $this->db->getRow($query);
		if(empty($result)){
			$return_user['errcode']  = 47002;
			$return_user["errmsg"]  = "查询不到订单";
		}else{
			$return_user["open_id"]  = $result['weixin_fromuser'];;
		}
		
		return $return_user;
	}		 
	
	
	/*
      * 查询当前进行的活动
      * @param int   customer_id 商家编号
      * Author: zpd 
      * 2017-11-15
      */
	public function select_now_action($customer_id){
		$return_msg["errcode"] = 0;
		$return_msg["errmg"]   = "success";
		$return_msg["data"]    = array();
		
		//查询活动是否开启
		$query = "SELECT is_open FROM ".WSY_SHOP.".slyder_adventures_basic_setting WHERE isvalid=true AND customer_id=".$customer_id." ORDER BY id DESC LIMIT 1";
		$is_open = $this->db->getOne($query);
		if( !$is_open ){
			$return_msg["errcode"] = 40003;
			$return_msg["errmsg"]  = "商家没有开启大转盘活动！";
			return $return_msg;
		}
		
		//查询当前活动信息
		$where = "";
		// $data['slyder_id'] = 37;
		// if($data['slyder_id'] > 0){
			// $where = " AND id = ".$data['slyder_id'];
		// }
		$query = "SELECT id,title,begin_time,end_time,type,limit_every_day,limit_order,award_expiry_date,limit_of_participation,display_list_of_winner,auto_receive_rewards,cumulative_frequency_type,create_time,status,is_fact_pay FROM ".WSY_SHOP.".slyder_adventures_config WHERE 
		          isvalid=true AND status=1 AND customer_id=".$customer_id." AND begin_time <= now() AND end_time >= now() ".$where." ORDER BY id DESC LIMIT 1";
		$actionList = $this->db->getRow($query);

		if(empty($actionList)){
			$return_msg["errcode"] = 40003;
			$return_msg["errmsg"]  = "没有进行中的活动！";
			return $return_msg;
		}
		
		$return_msg["data"] = $actionList;
		
		return $return_msg;
	}
	
	
	/*
      * 查询自动收货时间
      * @param int   order_id 订单编号
      * Author: zpd 
      * 2017-11-15
      */
	public function shop_auto_cus_time_select($customer_id){
		$return_time = array();
		$return_time["errcode"] = 0;
        $return_time["errmsg"]  = "success";
	
	
		$query = "select auto_cus_time from weixin_commonshops where isvalid = true and customer_id = ".$customer_id." limit 1";
		//echo $query;
		$result = $this->db->getRow($query);		
		if(empty($result)){
			$return_time["auto_cus_time"]  = 7;
		}else{
			$return_time["auto_cus_time"]  = $result['auto_cus_time'];
		}
		return $return_time;
	}		
	
	
	/*
      * 查询用户微信名
      * @param int   user_id 用户编号
      * Author: lqh 
      * 2017-11-20
      */
	public function find_weixin_name($user_id){
		$weixin_name = "";
		$query = "select weixin_name from ".WSY_USER.".weixin_users where isvalid = true and id = '".$user_id."' limit 1";
		$weixin_name = $this->db->getOne($query);
		return $weixin_name;
	}
	
	
	 /*
      * 微信消息定时任务表插入
      * @param array $value 插入值
      * Author: zpd
      * 2017-11-04
      */
	public function weixin_msg_insert($value=array()){
		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "success";

		if(empty($value)){
			$result["errcode"] = 51302;
			$result["errmsg"]  = "value参数错误！";
			return $result;
		}
		
		$value['createtime'] = date("Y-m-d H:i:s",time()); //日志创建时间
		$value['type']       = 4; 
		$value['status']     = 0; 
		$value['send_limit'] = 0; 
		$value['is_dealing'] = 0; 
		$value['remark']     = ''; 
		 
		$res = $this->db->autoExecute('send_weixinmsg_log',$value, 'insert');
		if(!$res){
			$result["errcode"] = 51303;
			$result["errmsg"]  = "slyder_adventures_reward_order_delivery 插入失败！";
			return $result;
		}
		$delivery_id = $this->db->insert_id();
		$result["delivery_id"] = $delivery_id;
		
		return $result;
	}		

	/**
	 * [导入订单]
	 * @author liupeixin
	 * @date   2019-01-19
	 * @param  [array]     $file        [excel文件]
	 * @param  [int]       $customer_id [商家号]
	 * @return [array]                  [导入是否成功]
	 */
	public function import_excel($file, $customer_id){
		require_once '../../weixinpl/common/excel/phpExcelReader/Excel/reader.php';
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('utf-8');
        $Import_TmpFile = $file['excelfile']['tmp_name'];
        $data->read($Import_TmpFile);

		$result = array();
		$result["errcode"] = 0;
        $result["errmsg"]  = "导入成功！";

        $err_row1 = '';
        $err_row2 = '';
        $err_row3 = '';

        $numRows = $data->sheets[0]['numRows'];
        if ($numRows > 501) {
			$result["errcode"] = 40002;
	        $result["errmsg"]  = "每次导入数量不能超过500条！";
	        return $result;
        }

        for($i = 2; $i <= $numRows; $i++) {
            $batchcode      = $data->sheets[0]['cells'][$i][1];
            $expressname    = $data->sheets[0]['cells'][$i][2];
            $expressnum     = $data->sheets[0]['cells'][$i][3];
            $express_remark = $data->sheets[0]['cells'][$i][4];

            //去掉特殊符号
            $batchcode      = str_replace("'",'',$batchcode);
            $batchcode      = str_replace("＇",'',$batchcode);
            $expressnum     = str_replace("'",'',$expressnum);
            $expressnum     = str_replace("＇",'',$expressnum);

            if (!empty($batchcode)) {
                $o_query = "SELECT id,status,user_id,product_name FROM ".WSY_SHOP.".slyder_adventures_reward_orders where isvalid = true and batchcode = '{$batchcode}' and customer_id = '{$customer_id}' limit 1";
                $o_result = $this->db->getRow($o_query);
                //没有查到订单号记录行数
                if (empty($o_result)) {
                	$err_row2 .= $i.',';
					continue;
                }

                //订单不是待发货状态直接跳过
                if ($o_result['status'] != 1) {
                	continue;
                }
                $order_id = $o_result['id'];
                $user_id = $o_result['user_id'];
                $pro_name = $o_result['product_name'];

                if($expressnum==false || $expressname==false){
                    //快递单号或者快递公司为空的时候,记录行数
                    $err_row1 .= $i.',';
                    continue;
                }

                $express_id = -1;

				$e_query = "SELECT id FROM ".WSY_SHOP.".weixin_expresses_company WHERE isvalid = true AND customer_id = '{$customer_id}' AND supply_id = -1 AND expresses_name = '{$expressname}'";
				$e_result = $this->db->getOne($e_query);

				//快递公司没查到记录行数
				if (empty($e_result)) {
					$err_row3 .= $i.',';
					continue;
				}
				$express_id = $e_result;

				/* 查询OpenID */
				$order_fromuser = "";
				$data_user['customer_id'] = $customer_id;
				$data_user['user_id']     = $user_id;
				$result_user  = $this->user_openID_select($data_user);

				$order_fromuser  = $result_user['open_id'];
				/* 查询OpenID End */				
				
				/* 自动收货时间 */
				$auto_cus_time = 7;
				$return_time  = $this->shop_auto_cus_time_select($customer_id);
				$auto_cus_time = $return_time['auto_cus_time'];
				if ($return_time['auto_cus_time'] == 0){
					$auto_cus_time = 7;
				}	
				/* 自动收货时间 End */

				/* 日志 */
				$log_username = $_SESSION['curr_login'];
				$data_log['order_id']       = $order_id;
				$data_log['operation']      = 4;
				$data_log['descript']       = "平台发货[物流：".$expressname.",单号：".$expressnum."]";
				$data_log['operation_user'] = $log_username;
				$data_log['isvalid']        = true;
				$return_log  = $this->order_log_insert($data_log);
				/* 日志 end */

				/* 修改订单状态 */
				$condition_order['id']     = $order_id;
				
				$data_order['status']      = 2;
				$data_order['send_status'] = 1;
				$return_order  = $this->order_status_update($condition_order,$data_order);
				/* 修改订单状态 end */
				
				/*插入发货记录*/
				$result_add  = $this->order_address_select($order_id);

				$adress    = $result_add['adress'];
				$data_delivery['name']          = $adress['name'];
				$data_delivery['phone']         = $adress['phone'];
				$data_delivery['location_p']    = $adress['location_p'];
				$data_delivery['location_c']    = $adress['location_c'];
				$data_delivery['location_a']    = $adress['location_a'];
				$data_delivery['address']       = $adress['address'];	
				$data_delivery['order_id']      = $order_id;
				$data_delivery['express_num']   = $expressnum;
				$data_delivery['express_id']    = $express_id;
				$data_delivery['remark']        = $express_remark;
				$return_delivery  = $this->order_delivery_insert($data_delivery);
				$delivery_id = $return_delivery['delivery_id'];
				/*插入发货记录 end  */
				
				/*更新自动确认收货时间*/
				$condition_de['id'] = $delivery_id;
				$return_delivery_ar  = $this->order_delivery_auto_receivetime_update($condition_de,$auto_cus_time);
				/*更新自动确认收货时间 End*/
				
				/* 发送消息 */
				$content = "亲，您有一笔大转盘奖品订单【已发货】\n\n奖品：".$pro_name."\n时间：".date( "Y-m-d H:i:s")."\n快递：".$expressname."";
				if(!empty($express_remark)){
					$content=$content."\n备注：".$express_remark;
				}
				$content=$content."\n\n<a href='https://m.kuaidi100.com/result.jsp?nu=".$expressnum."'>【查看物流进度】</a>\n<a href='".Protocol. $_SERVER['HTTP_HOST']."/mshop/web/index.php?m=slyder_adventures&a=order_details&customer_id=" . $customer_id . "&batchcode=".$batchcode."'>【查看订单详情】</a>";
				
				$wx_msg['openid']      = $order_fromuser;
				$wx_msg['content']     = mysql_escape_string($content);
				$wx_msg['customer_id'] = $customer_id;
				$wx_msg['user_id']     = $user_id;
				$return_msg  = $this->weixin_msg_insert($wx_msg);
				/* 发送消息 */
            }
        }
		if($err_row1 || $err_row2 || $err_row3){
		    $alert_str='';
		    if($err_row1){
		        $err_row1 = trim($err_row1,',');
		        $alert_str = "第{$err_row1}行，快递名称或快递单号为空，导入失败！";
		    }
		    if($err_row2){
		        $err_row2 = trim($err_row2,',');
		        $alert_str = "第{$err_row2}行，不存在该订单号，导入失败！";
		    }
		    if ($err_row3) {
		        $err_row3 = trim($err_row3,',');
		        $alert_str = "第{$err_row3}行，不存在该快递公司，请先添加对应的快递公司，导入失败！";
		    }
		    $result["errcode"] = 40001;
		    $result["errmsg"] = $alert_str.'其他正常导入！';
		}
		return $result;
	}
	
}
?>