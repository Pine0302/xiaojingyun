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


class model_block_chain{
	var $db;

	function __construct()
	{
        $this->db = DB::getInstance();
    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分日志页面
    开 发 者：wuzepeng
    开发日期： 2018-08-03
    @param $param['customer_id']//商家id
    	   $param['batchcode']订单号
    	   $param['user_name']用户名
    	   $param['user_id']用户id
    	   $param['status']类型 
    	   $param['pagenum']
    重要说明：无
     */
    public function get_integral_log($param=array())
    {
    	extract($param);
    	/***分页start******/
    	$pageSize = 20;
    	$start = ($pagenum-1)*$pageSize;
    	$end   = $pageSize;
    	/***分页end******/
    	
    	$sql_count = "SELECT count(b.id) as total FROM ".WSY_SHOP.".block_chain_log b INNER JOIN ".WSY_USER.".weixin_users u ON b.user_id = u.id where b.customer_id = '{$customer_id}' ";
    	$sql_all       = "SELECT b.user_id,b.status,b.batchcode,b.remark,b.createtime,b.reward,b.block_origin,u.weixin_name FROM ".WSY_SHOP.".block_chain_log b INNER JOIN ".WSY_USER.".weixin_users u ON b.user_id = u.id where b.customer_id = '{$customer_id}' ";
    	
    	/***************搜索条件start**********************/
    	$sql = '';
    	if( $batchcode !=-1 )
    	{
    		$sql .= " AND b.batchcode = '{$batchcode}' ";
    	}
    	if( $status > 0 )
    	{
    		$sql .= " AND b.status = '{$status}' ";
    	}
		if( $block_origin !=-1 )
    	{
    		$sql .= " AND b.block_origin = '{$block_origin}' ";
    	}
    	if( $user_name !=-1 )
    	{
    		$sql .= " AND u.weixin_name like '%{$user_name}%' ";
    	}
    	if( $user_id != -1 )
    	{
    		$sql .= " AND b.user_id = '{$user_id}' ";
    	}
		/***************搜索条件end************************/
         
		$order               = " ORDER BY b.createtime desc ";
		$limit               = "  LIMIT {$start} , {$end} ";
		$sql_count           = $sql_count.$sql.$order;//总数据
		$sql_page            = $sql_all.$sql.$order.$limit;//分页后每页数据
		
		$count               = $this->db->getRow($sql_count);
		$return['data']      = $this->db->getAll($sql_page);
		$return['pageCount'] = ceil($count['total']/$pageSize);
		
		return $return;
    }
	/*
	版权信息:  秘密信息
	功能描述：区块链积分发放页面
	开 发 者：zhaipeibin
	开发日期： 2018-08-03
	@param 
	重要说明：无
	*/
	public function integral_grant($customer_id){

		$sql = "SELECT id,on_off,name,appid,screet,url,valid_day,block_chain_type,block_chain_bfb,block_chain_gene FROM ".WSY_SHOP.".block_chain_setting where customer_id=".$customer_id." LIMIT 1 ";

		$result= $this->db->getRow($sql);

		return $result;
	}

	/*
	版权信息:  秘密信息
	功能描述：区块链积分发放页面
	开 发 者：zhaipeibin
	开发日期： 2018-08-03
	@param 
	重要说明：无
	*/

	public function get_integral_grant_update($customer_id,$data){
        $time =date("Y-m-d H:i:s");//时间
        if($data['block_chain_type'] == '')
        {
        	$data['block_chain_type'] = 1;
        }
		if($data['id']==''){
		$sql = "INSERT INTO ".WSY_SHOP.".block_chain_setting (customer_id,on_off,name,appid,screet,url,valid_day,create_time,block_chain_type,block_chain_bfb,block_chain_gene) VALUES ('".$customer_id."','".$data['on_off']."','".$data['name']."','".$data['appid']."','".$data['appsecret']."','".$data['url']."','".$data['valid_day']."','".$time."','".$data['block_chain_type']."','".$data['block_chain_bfb']."','".$data['block_chain_gene']."')";
		              
		$result =$this->db->query($sql);

        }else{
		$sql = "UPDATE ".WSY_SHOP.".block_chain_setting SET appid='".$data['appid']."',screet='".$data['appsecret']."',url='".$data['url']."',on_off='".$data['on_off']."',name='".$data['name']."',valid_day='".$data['valid_day']."',create_time='".$time."',block_chain_type='".$data['block_chain_type']."',block_chain_bfb='".$data['block_chain_bfb']."',block_chain_gene='".$data['block_chain_gene']."' WHERE customer_id=".$customer_id  ;
		              
	    }
	    $result =$this->db->query($sql);
	    //是否强制绑定手机号
	    if ($data['block_chain_gene'] == 1) 
	    {
	    	$sql="update weixin_commonshops set is_bind_chat = 1 where isvalid = true and customer_id=".$customer_id; 
	    }
	    $result =$this->db->query($sql);
		return $result;

	}
	/*
	版权信息:  秘密信息
	功能描述：区块链积分明细页面
	开 发 者：wuzepeng
	开发日期： 2018-08-03
	@param @param $param['customer_id']//商家id
    	   $param['batchcode']订单号
    	   $param['user_name']用户名
    	   $param['user_id']用户id
    	   $param['status']领取状态 
    	   $param['pagenum']
	重要说明：无
	*/
	public function get_integral_details($param=array())
	{
		extract($param);
    	/***分页start******/
    	$pageSize = 20;
    	$start = ($pagenum-1)*$pageSize;
    	$end   = $pageSize;
    	/***分页end******/
    	
    	$sql_count = "SELECT count(d.id) as total FROM ".WSY_SHOP.".block_chain_order_detail d INNER JOIN ".WSY_USER.".weixin_users u ON d.user_id = u.id where d.customer_id = '{$customer_id}' and d.status != 2 and d.status != 3 and d.status !=4 ";
    	$sql_reward_off = "SELECT sum(reward) as reward_off FROM ".WSY_SHOP.".block_chain_order_detail WHERE (status = 0 OR status = 2) AND customer_id = '{$customer_id}' ";//待发放
    	$sql_reward_on = "SELECT sum(reward) as reward_on FROM ".WSY_SHOP.".block_chain_order_detail WHERE status = 1 AND customer_id = '{$customer_id}' ";//已领取
    	$sql_all       = "SELECT d.user_id,d.status,d.batchcode,d.score_receivetime,d.createtime,d.reward,u.weixin_name FROM ".WSY_SHOP.".block_chain_order_detail d INNER JOIN ".WSY_USER.".weixin_users u ON d.user_id = u.id where d.order_status = 1 and  d.customer_id = '{$customer_id}' and d.status != 3 and d.status !=4 ";
    	
    	/***************搜索条件start**********************/
    	$sql = '';
    	if( $batchcode !=-1 )
    	{
    		$sql .= " AND d.batchcode = '{$batchcode}' ";
    	}
    	if( $status != -1 )
    	{
    		if($status == 0)
    		{
    			$sql .= " AND (d.status = '0' OR d.status = '2') ";
    		}
    		else
    		{
    			$sql .= " AND d.status = '{$status}' ";
    		}
    	}
    	if( $user_name !=-1 )
    	{
    		$sql .= " AND u.weixin_name like '%{$user_name}%' ";
    	}
    	if( $user_id != -1 )
    	{
    		$sql .= " AND d.user_id = '{$user_id}' ";
    	}
		/***************搜索条件end************************/

		$order               = " ORDER BY d.createtime desc ";
		$limit               = "  LIMIT {$start} , {$end} ";
		$sql_count           = $sql_count.$sql.$order;//总数据
		$sql_page            = $sql_all.$sql.$order.$limit;//分页后每页数据
		$count               = $this->db->getRow($sql_count);
		$return['data']      = $this->db->getAll($sql_page);
		$reward_off          = $this->db->getOne($sql_reward_off);
		$reward_on           = $this->db->getOne($sql_reward_on);
		if(!$reward_off)//如果没有数据，则为0
		{
			$reward_off = 0;
		}
		if(!$reward_on)
		{
			$reward_on = 0;
		}
		$return['reward_total'] = $reward_on+$reward_off;//已发放 = 已领取+待领取
		$return['reward_off'] = $reward_off;
		$return['reward_on'] = $reward_on;
		$return['pageCount'] = ceil($count['total']/$pageSize);
		
		return $return;

	}
	/*
	版权信息:  秘密信息
	功能描述：区块链积分奖励基本设置信息
	开发日期： 2018-09-04
	@param $customer_id//商家id

	重要说明：无
	*/
	public function integral_reward_setting($customer_id)
	{
		$sql = "SELECT on_off,proportion FROM ".WSY_REBATE.".weixin_block_chain_reward_setting WHERE customer_id = '{$customer_id}' LIMIT 1 ";
		$result = $this->db->getRow($sql);
		return $result;
	}
	/*
	版权信息:  秘密信息
	功能描述：区块链积奖励更新
	开 发 者：wuzepeng
	开发日期： 2018-09-04
	@param 
		$param['customer_id']//商家id
		$param['proportion']奖励金额比例
		$param['on_off']开关
		$param['op']模式：insert/update
	重要说明：无
	*/
	public function integral_reward_setting_update($param=array())
	{
		extract($param);
		$return = array();
		$time =date("Y-m-d H:i:s");//时间
		$sql = '';

		switch ($op) {
			case 'insert':
				$sql = "INSERT INTO ".WSY_REBATE.".weixin_block_chain_reward_setting (on_off,proportion,createtime,customer_id) VALUES ('{$on_off}','{$proportion}','{$time}','{$customer_id}') ";
				break;
			case 'update':
				$sql = "UPDATE ".WSY_REBATE.".weixin_block_chain_reward_setting SET on_off = '{$on_off}' , proportion = '{$proportion}' WHERE customer_id = '{$customer_id}' ";
				break;
		}

		$result = $this->db->query($sql);
		if($result)
		{
			$return['errcode'] = 0;
			$return['errmsg']  ='操作成功';
		}
		else
		{
			$return['errcode'] = 400;
			$return['errmsg']  = $sql;
		}
		return $return;
	}
	/*
	版权信息:  秘密信息
	功能描述：区块链积分奖励奖金池
	开 发 者：wuzepeng
	开发日期： 2018-09-04
	@param 
		$param['customer_id']//商家id
		$param['pagenum']当前页数
		$param['year']年份
		$param['month']月份
	重要说明：无
	*/
	public function get_integral_reward_list($param=array())
	{
		extract($param);
    	/***分页start******/
    	$pageSize = 20;
    	$start = ($pagenum-1)*$pageSize;
    	$end   = $pageSize;
    	/***分页end******/
    	$sql_count = "SELECT count(id) as total FROM ".WSY_SHOP.".block_chain_bonus where customer_id = '{$customer_id}' ";
    	$sql_user_block_chain = "SELECT sum(block_chain) as user_block_chain FROM ".WSY_USER.".moneybag_t where customer_id = '{$customer_id}' and isvalid = true ";//区块链流通发行总量
    	$sql_total_bonus_money = "SELECT total_money FROM ".WSY_REBATE.".weixin_block_chain_reward_pool where customer_id = '{$customer_id}' LIMIT 1 ";//奖金池总额
    	$sql_exchange_jf = "SELECT sum(exchange_jf) as exchange_jf FROM ".WSY_SHOP.".block_chain_bonus_activity where customer_id = '{$customer_id}' ";// 已发放总量
    	$sql_all   = "SELECT id,reward_money,total_money,value_money,createtime,year_months FROM ".WSY_SHOP.".block_chain_bonus where customer_id = '{$customer_id}' ";
    	
    	/***************搜索条件start**********************/
    	$sql = '';
    	if( $year != -1 && $month == -1)
    	{
    		$temp_year = $year.'-00-00 00:00:00';
    		$temp_next_year = ($year+1).'-00-00 00:00:00';
    		$sql .= " AND createtime >= '".$temp_year."' AND createtime < '".$temp_next_year."' ";
    	}else if( $year != -1 && $month != -1 )
    	{
    		$temp_time = $year.'-'.$month;//将年月串起来
    		$sql .= " AND year_months = '".$temp_time."' ";
    	}else if( $year == -1 && $month != -1 )
    	{
    		$sql .= " AND MONTH(createtime) = '".$month."' ";
    	}

		/***************搜索条件end************************/

		$order               = " ORDER BY id desc ";
		$limit               = "  LIMIT {$start} , {$end} ";

		$sql_count           = $sql_count.$sql.$order;//总数据
		$sql_page            = $sql_all.$sql.$order.$limit;//分页后每页数据

		$count                        = $this->db->getRow($sql_count);//
		$data                         = $this->db->getAll($sql_page);//数据
		$user_block_chain             = $this->db->getOne($sql_user_block_chain);//区块链流通发行总量
		$return['total_bonus_money']  = $this->db->getOne($sql_total_bonus_money);//奖金池总额
		$return['total_exchange_jf']  = $this->db->getOne($sql_exchange_jf)+$user_block_chain;//已发放总量 = 区块链流通发行总量 + 已兑换区块链积分
		$return['user_block_chain']   = $user_block_chain;//区块链流通发行总量
		$return['pageCount']          = ceil($count['total']/$pageSize);

		foreach ($data as $key => $value) {
			$sql_data = "SELECT sum(exchange_money) as total_exchange_money FROM ".WSY_SHOP.".block_chain_bonus_activity WHERE bonus_id ='".$value['id']."' AND customer_id = '{$customer_id}' and status = 1 ";
			$value['exchange_money'] = $this->db->getOne($sql_data);
			if($value['exchange_money'] == NULL)
			{
				$value['exchange_money'] = 0;
			}
			$data[$key] = $value;
		}
		$return['data']               = $data;
		return $return;
	}
	/*
	版权信息:  秘密信息
	功能描述：区块链积分奖励产品活动添加
	开 发 者：wuzepeng
	开发日期： 2018-09-04
	@param 
		$param['product_name']  产品名称
        $param['product_num']   产品数量
        $param['product_price'] 产品金额
        $param['begin_time']    开始时间
        $param['end_time']      结束时间
        $param['bonus_id']      奖金池id
        $param['customer_id']   商家id
	重要说明：无
	*/
	public function integral_reward_activity_insert($param=array())
	{
		$return = array();
		extract($param);
		$time  = date('Y-m-d H:i:s');
		$sql = "INSERT INTO ".WSY_SHOP.".block_chain_bonus_activity (product_name,product_num,product_price,begin_time,end_time,customer_id,status,bonus_id,createtime,value_money) VALUES ('{$product_name}','{$product_num}','{$product_price}','{$begin_time}','{$end_time}','{$customer_id}','1','{$bonus_id}','{$time}','{$value_money}') ";
		$result = $this->db->query($sql);
		if($result)
		{
			$return['errcode'] = 0;
			$return['errmsg']  = '操作成功';
		}
		else
		{
			$return['errcode'] = 400;
			$return['errmsg']  = '操作失败';
		}
		return $return;
	}
	/*
	版权信息:  秘密信息
	功能描述：区块链积分奖励活动管理列表
	开 发 者：wuzepeng
	开发日期： 2018-09-04
	@param 
		$param['customer_id']//商家id
		$param['pagenum']当前页数
		$param['bonus_id']奖金池id
		$param['status']活动状态
		$param['product_name']产品名称
	重要说明：无
	*/
	public function get_integral_reward_activity($param=array())
	{
		extract($param);
		/***分页start******/
    	$pageSize = 20;
    	$start = ($pagenum-1)*$pageSize;
    	$end   = $pageSize;
    	/***分页end******/
    	$time = time();
    	$sql_count = "SELECT count(id) as total FROM ".WSY_SHOP.".block_chain_bonus_activity WHERE bonus_id = '{$bonus_id}' AND customer_id = '{$customer_id}' ";
		$sql_all = "SELECT id,product_name,status,product_num,product_price,begin_time,end_time,exchange_num,exchange_money,exchange_jf,createtime FROM ".WSY_SHOP.".block_chain_bonus_activity WHERE bonus_id = '{$bonus_id}' AND customer_id = '{$customer_id}' ";
		
		/***************搜索条件start**********************/
    	$sql = '';
    	if( $product_name != -1 )
    	{
    		$sql .= " AND product_name like '%{$product_name}%' ";
    	}
    	switch ($status) {
    		case 0:
    			$sql .= " AND status = {$status} ";//已删除
    			break;
    		case 1:
    			$sql .= " AND status = 1 AND UNIX_TIMESTAMP(begin_time) > '{$time}' ";//未开始
    			break;
    		case 2:
    			$sql .= " AND status = 1 AND UNIX_TIMESTAMP(begin_time) <= '{$time}' AND UNIX_TIMESTAMP(end_time)>= '{$time}' ";//进行中
    			break;
    		case 3:
    			$sql .= " AND status = 1 AND UNIX_TIMESTAMP(end_time) < '{$time}' ";//已结束
    			break;
    	}
		/***************搜索条件end************************/

		$order               = " ORDER BY id desc ";
		$limit               = "  LIMIT {$start} , {$end} ";
		$sql_count           = $sql_count.$sql.$order;//总数据
		$sql_page            = $sql_all.$sql.$order.$limit;//分页后每页数据
		$count               = $this->db->getRow($sql_count);
		$data                = $this->db->getAll($sql_page);
		
		foreach ($data as $key => $value) {
			if($value['status'] == 0)
			{
				$value['status'] = '已删除';
			}
			else
			{
				if($time >= strtotime($value['begin_time']) && $time <= strtotime($value['end_time']) )
				{
					$value['status'] = '进行中';
				}elseif($time < strtotime($value['begin_time']))
				{
					$value['status'] = '未开始';
				}elseif($time > strtotime($value['end_time']))
				{
					$value['status'] = '已结束';
				}
			}
			$data[$key] = $value;
		}
		$return['data']       = $data; 
		$return['pageCount']  = ceil($count['total']/$pageSize);
		
		return $return;
	}
	/*
	版权信息:  秘密信息
	功能描述：区块链积分奖励删除产品管理
	开 发 者：wuzepeng
	开发日期： 2018-09-04
	@param 
		$param['id']//产品活动id  如果删除多个的话则为数组
		$param['op']模式：del_one/del_many
	重要说明：无
	*/
	public function integral_reward_del($id,$op='')
	{
		$sql= '';
		switch ($op) {
			case 'del_one':
				$sql = "UPDATE ".WSY_SHOP.".block_chain_bonus_activity SET status = 0 WHERE id = '{$id}' ";
				$result = $this->db->query($sql);
				break;
			case 'del_many':
			foreach ($id as $value) {
				$sql = "UPDATE ".WSY_SHOP.".block_chain_bonus_activity SET status = 0 WHERE id = '".$value."' ; ";
				$result = $this->db->query($sql);
			}
				break;
		}
		if($result)
		{
			if($return == '')
			{
				$return['errcode'] = 0;
				$return['errmsg']  = '删除成功';
			}
		}
		else
		{
			if($return == '')
			{
				$return['errcode'] = 400;
				$return['errmsg']  = '删除失败';
			}
		}		
		return $return;
	}
	/*
	版权信息:  秘密信息
	功能描述：区块链积分奖励兑换日志
	开 发 者：wuzepeng
	开发日期： 2018-09-04
	@param 
		$param['customer_id']//商家id
		$param['pagenum']当前页数
		$param['bonus_id']奖金池id
		$param['user_id']用户id
		$param['product_name']产品名称
		$param['user_name']用户名
		$param['activity_id']活动id
	重要说明：无
	*/
	public function integral_reward_exchange_log($param=array())
	{
		extract($param);
		/***分页start******/
    	$pageSize = 20;
    	$start = ($pagenum-1)*$pageSize;
    	$end   = $pageSize;
    	/***分页end******/
    	$sql_count = "SELECT count(id) as total FROM ".WSY_SHOP.".block_chain_exchange_log WHERE customer_id = '{$customer_id}' ";
		$sql_all = "SELECT user_id,user_name,product_name,product_num,product_price,product_money,proportion,jifeng,createtime FROM ".WSY_SHOP.".block_chain_exchange_log WHERE customer_id = '{$customer_id}' ";
		/***************搜索条件start**********************/
    	$sql = '';
    	if( $product_name != -1 )
    	{
    		$sql .= " AND product_name like '%{$product_name}%' ";
    	}
    	if( $user_name != -1 )
    	{
    		$sql .= " AND user_name like '%{$user_name}%' ";
    	}
    	if( $user_id != -1 )
    	{
    		$sql .= " AND user_id = '{$user_id}' ";
    	}
    	if( $activity_id != -1 )
    	{
    		$sql .= " AND activity_id = '{$activity_id}' ";
    	}
    	if( $bonus_id != -1 )
    	{
    		$sql .= " AND bonus_id = '{$bonus_id}' ";
    	}
		/***************搜索条件end************************/

		$order               = " ORDER BY id desc ";
		$limit               = "  LIMIT {$start} , {$end} ";
		$sql_count           = $sql_count.$sql.$order;//总数据
		$sql_page            = $sql_all.$sql.$order.$limit;//分页后每页数据
		$count               = $this->db->getRow($sql_count);
		$data                = $this->db->getAll($sql_page);

		$return['data'] = $data;
		$return['pageCount'] = ceil($count['total']/$pageSize);
		
		return $return;
	}
	/*
	版权信息:  秘密信息
	功能描述：区块链积分奖励获取活动页面与添加活动页面的 流通发行总量，价值 可兑换奖金总额
	开 发 者：wuzepeng
	开发日期： 2018-09-04
	@param 
		$param['customer_id']//商家id
		$param['bonus_id']奖金池id
	重要说明：无
	*/
	public function common_bonus_data($bonus_id,$customer_id)
	{
		$time = time();//当前时间
		$sql_bonus_data = "SELECT reward_money,total_money,value_money,createtime,year_months FROM ".WSY_SHOP.".block_chain_bonus WHERE customer_id = '{$customer_id}' and id = '{$bonus_id}' limit 1 "; //bonus_id 的月份 流通发行总量，价值 奖金池，时间
		$bonus_data  = $this->db->getRow($sql_bonus_data);

		// start 各个活动的产品数量*产品价格
		$sql = "SELECT product_num,product_price FROM ".WSY_SHOP.".block_chain_bonus_activity WHERE bonus_id = '{$bonus_id}' AND customer_id = '{$customer_id}' AND status = 1 AND UNIX_TIMESTAMP(end_time) > '{$time}' ";
		$temp_bonus_arr = $this->db->getAll($sql);
		$sum_bonus_money = 0;
		foreach ($temp_bonus_arr as $key => $value) {
			$sum_bonus_money += ( $value['product_num'] * $value['product_price'] );
		}
		// end 各个活动的产品数量*产品价格
		
		//被删除的已兑换的金额 start
		$sql_del = "SELECT sum(exchange_money) as exchange_money FROM ".WSY_SHOP.".block_chain_bonus_activity WHERE bonus_id = '{$bonus_id}' AND customer_id = '{$customer_id}' AND status = 0 ";//被删除已兑换的活动金额
		$temp_bonus_del = $this->db->getOne($sql_del);
		//被删除的已兑换的金额 end
		
		//已结束的活动的已兑换的金额 start
		
		$sql_end = "SELECT sum(exchange_money) FROM ".WSY_SHOP.".block_chain_bonus_activity WHERE bonus_id = '{$bonus_id}' AND customer_id = '{$customer_id}' AND UNIX_TIMESTAMP(end_time) < '{$time}' ";//已结束的活动
		$activity_end = $this->db->getOne($sql_end);
		//已结束的活动的已兑换的金额 end

		// 可兑换奖金总额 = 当月奖金池数-(各个活动的产品数量*产品价格)-被删除的已兑换的金额-已结束的已兑换的金额
		$bonus_data['exchange_bonus_money'] = $bonus_data['total_money']-$sum_bonus_money-$temp_bonus_del-$activity_end;
		return $bonus_data; //bonus_id 的月份 流通发行总量，价值 可兑换奖金总
	}


	/*
	版权信息:  秘密信息
	功能描述：区块链积分奖励活动管理列表(全部)
	开 发 者：wuzepeng
	开发日期： 2018-09-04
	@param 
		$param['customer_id']//商家id
		$param['pagenum']当前页数
		$param['status']活动状态
		$param['product_name']产品名称
	重要说明：无
	*/
	public function get_integral_reward_all_activity($param=array())
	{
		extract($param);
		/***分页start******/
    	$pageSize = 20;
    	$start = ($pagenum-1)*$pageSize;
    	$end   = $pageSize;
    	/***分页end******/
    	$time = time();
    	$sql_count = "SELECT count(id) as total FROM ".WSY_SHOP.".block_chain_bonus_activity WHERE customer_id = '{$customer_id}' ";
		$sql_all = "SELECT id,product_name,status,product_num,product_price,begin_time,end_time,exchange_num,exchange_money,exchange_jf,createtime,bonus_id FROM ".WSY_SHOP.".block_chain_bonus_activity WHERE customer_id = '{$customer_id}' ";
		
		/***************搜索条件start**********************/
    	$sql = '';
    	if( $product_name != -1 )
    	{
    		$sql .= " AND product_name like '%{$product_name}%' ";
    	}
    	switch ($status) {
    		case 0:
    			$sql .= " AND status = {$status} ";//已删除
    			break;
    		case 1:
    			$sql .= " AND status = 1 AND UNIX_TIMESTAMP(begin_time) > '{$time}' ";//未开始
    			break;
    		case 2:
    			$sql .= " AND status = 1 AND UNIX_TIMESTAMP(begin_time) <= '{$time}' AND UNIX_TIMESTAMP(end_time)>= '{$time}' ";//进行中
    			break;
    		case 3:
    			$sql .= " AND status = 1 AND UNIX_TIMESTAMP(end_time) < '{$time}' ";//已结束
    			break;
    	}
		/***************搜索条件end************************/

		$order               = " ORDER BY id desc ";
		$limit               = "  LIMIT {$start} , {$end} ";
		$sql_count           = $sql_count.$sql.$order;//总数据
		$sql_page            = $sql_all.$sql.$order.$limit;//分页后每页数据
		$count               = $this->db->getRow($sql_count);
		$data                = $this->db->getAll($sql_page);
		
		foreach ($data as $key => $value) {
			if($value['status'] == 0)
			{
				$value['status'] = '已删除';
			}
			else
			{
				if($time >= strtotime($value['begin_time']) && $time <= strtotime($value['end_time']) )
				{
					$value['status'] = '进行中';
				}elseif($time < strtotime($value['begin_time']))
				{
					$value['status'] = '未开始';
				}elseif($time > strtotime($value['end_time']))
				{
					$value['status'] = '已结束';
				}
			}
			$data[$key] = $value;
		}
		$return['data']       = $data; 
		$return['pageCount']  = ceil($count['total']/$pageSize);
		
		return $return;
	}
    public function http_url($customer_id)
    {
    	$query = "select url from ".WSY_SHOP.".block_chain_setting where customer_id='" . $customer_id . "'";
    	$res = $this->db->getRow($query);
    	return $res['url'];
    }
    /*
    * 区块链APP登陆用户是否绑定商城
    * $Author: hjw$
    * $2018-10-8  $
    * 参数：
    */
    public function is_bind_shop($data = array()){
    	extract($data);
    	$query = "select user_id from ".WSY_USER.".system_user_t where customer_id='" . $customer_id . "' and account = '".$mobile."' and isvalid = true";
    	$res = $this->db->getRow($query);
    	return $res;
    }
    /*
    * 区块链APP创建新用户
    * $Author: hjw$
    * $2018-10-8  $
    * 参数：
    */
    public function create_account($data = array()){
		extract($data);
		$select = "select id from ".WSY_USER.".weixin_users where customer_id = '".$customer_id."' and isvalid = true and block_chain_openid = '".$openid."'";
		$res = $this->db->getRow($select);
		if(isset($res['id'])){
			return $res['id'];
		}else{

			$sql = "INSERT INTO ".WSY_USER.".weixin_users (name,phone,isvalid,createtime,customer_id,weixin_name,weixin_headimgurl,fromw,block_chain_openid) VALUES ('{$nickname}','{$mobile}',true,now(),'{$customer_id}','{$nickname}','{$head_img}',7,'{$openid}') ";
			$result = $this->db->query($sql);
			$user_id =  $this->db->insert_id();
			$sql1 = "INSERT INTO ".WSY_USER.".system_user_t (user_id,customer_id,isvalid,createtime,account,password) VALUES ('{$user_id}','{$customer_id}',true,now(),'{$mobile}','".md5('888888')."') ";
			$result1 = $this->db->query($sql1);
			return $user_id;

		}

    }
}//类结束
