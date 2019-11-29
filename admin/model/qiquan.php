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


class model_qiquan{
    var $db;
    var $model_common;
    var $shopmessage;
    function __construct()
    {
        $this->db = DB::getInstance();
      
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——查询全部推荐管理数据
    开 发 者：wuzepeng
    开发日期： 2018-06-06
    @param customer_id 商家ID
    重要说明：无
    返回：推荐表中的所有数据（二维数组）
     */
    public function get_ququan_recommend_list($customer_id)
    {
    	$sql = "SELECT a.id,a.stock_code,a.yield_rate,a.buy_price,a.sale_price,a.profit_loss,a.num,a.createtime,b.name FROM ".WSY_SHOP.".optiondeal_recommend a INNER JOIN ".WSY_SHOP.".optiondeal_shares_infos b ON a.stock_code = b.code WHERE customer_id = '{$customer_id}' order by a.yield_rate desc ";
    	$data = $this->db->getAll($sql);
    	return $data;
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——检测推荐是否存在
    开 发 者：wuzepeng
    @param stock_code 股票编码
    开发日期： 2018-06-07
    重要说明：无
     */
    public function check_exists_recommend($stock_code='')
    {
        $sql = "SELECT stock_code FROM ".WSY_SHOP.".optiondeal_recommend WHERE stock_code = {$stock_code} ";
        $result = $this->db->getRow($sql);
        if($result)
        {
            return array('errcode' =>400,'msg' => "股票代码已存在，请在已有的推荐上编辑");
        }else
        {
            return array('errcode' => 200);
        }
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——添加（插入）期权推荐
    开 发 者：wuzepeng
    开发日期： 2018-06-06
    重要说明：无
    参数：$data 需要插入（添加）的数据
    返回：bool (true or false)
     */
   	public function insert_qiquan_recommend($data=array())
   	{
   		$column = implode(',',(array_keys($data)));
   		$value = implode(',',$data);

   		$sql = "INSERT INTO ".WSY_SHOP.".optiondeal_recommend ($column) VALUES ($value)";
   		return $this->db->query($sql);
   		
   	}
    /*
    版权信息:  秘密信息
    功能描述：期权交易——检测是否股票代码是否存在，是否停牌
    开 发 者：wuzepeng
    开发日期： 2018-06-06
    重要说明：无
    参数：$stock_code 股票编码
     */
   	public function check_qiquan_stock_code($stock_code='')
   	{
   		$sql = "SELECT code FROM ".WSY_SHOP.".optiondeal_shares_infos where code = $stock_code";
   		$result = $this->db->getRow($sql);

   		if( !$result )
   		{
   			$tips = "【股票[ {$stock_code} ]不存在！】";
   			return array('errcode'=>400,'msg'=>$tips);
   		}

   		$sql2 = "SELECT state,code,name FROM ".WSY_SHOP.".optiondeal_shares_infos where code = $stock_code";
   		$result2 = $this->db->getRow($sql2);
   		if( $result2['state'] != '1' )
   		{
   			$tips = "【股票 [ {$result2['code']} {$result2['name']} ]处理于停牌中，不可正常交易】";
   			return array('errcode'=>400,'msg'=>$tips);
   		}

        return array('errcode'=>200);
   		
   	}
    /*
    版权信息:  秘密信息
    功能描述：期权交易——编辑推荐 获取单条推荐信息
    开 发 者：wuzepeng
    开发日期： 2018-06-07
    重要说明：无
    参数：$id 推荐ID
     */
   	public function getone_qiquan_recommend_data($id='')
   	{
   		$sql = "SELECT stock_code,yield_rate,buy_price,sale_price,profit_loss,num FROM ".WSY_SHOP.".optiondeal_recommend WHERE id = '{$id}'";
   		return $this->db->getRow($sql);
   	}
    /*
    版权信息:  秘密信息
    功能描述：期权交易——更新推荐数据
    开 发 者：wuzepeng
    开发日期： 2018-06-07
    重要说明：无
    参数：$id 推荐ID $data 需要更新的数据
    返回：bool (true or false)
     */
   	public function update_qiquan_recommend ($id='',$data=array())
   	{
   		$tmp = '';
    	foreach ($data as $key=>$val)
    	{
    		if($tmp == '')
    		{
    			$tmp=$key."='".$val."'";
    		}
    		else
    		{
    			$tmp.=",".$key."='".$val."'";
    		}
    	}
        $sql = "UPDATE ".WSY_SHOP.".optiondeal_recommend SET {$tmp} WHERE id = {$id} ";
   		return $this->db->query($sql);
   	}
    /*
    版权信息:  秘密信息
    功能描述：期权交易——删除推荐
    开 发 者：wuzepeng
    开发日期： 2018-06-07
    重要说明：无
    参数：$id 推荐ID
    返回：bool (true or false)
     */
    public function delete_qiquan_recommend_data($id ='')
    {
        $sql = sprintf("DELETE FROM ".WSY_SHOP.".optiondeal_recommend WHERE id = %d ",$id);
        return $this->db->query($sql);
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——获取订单管理信息
    开 发 者：wuzepeng
    开发日期： 2018-06-07
    参数：$param 需要查询的参数
    重要说明：无
     */
    public function get_qiquan_order_list($param = array())
    {
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum  = $param['pageNum'] ? : 1; //当前页,1开始
        $start    = ($pageNum-1)*$pageSize;
        $end      = $pageSize;
        $customer_id = $param['customer_id'];
        //分页设置 end

        $batchcode             = -1;//订单号
        $phone                 = -1;//电话号码
        $stock_code            = -1;//股票代码
        $capital               = -1;//名义本金
        $status                = -1;//订单状态(-1)为搜索全部
        $search_time_type      = -1;//搜索的时间类型
        $start_time            = -1;//开始时间
        $end_time              = -1;//结束时间

        if(!empty($param['batchcode'])){
            $batchcode = $param['batchcode'];
        }
        if(!empty($param['phone'])){
            $phone = $param['phone'];
        }
        if(!empty($param['stock_code'])){
            $stock_code = $param['stock_code'];
        }
        if(!empty($param['capital'])){
            $capital = $param['capital'];
        }
        if(!empty($param['user_id'])){
            $user_id = $param['user_id'];
        }
        if(is_numeric($param['status'])){
            $status = (int)$param['status'];
        }
        if(!empty($param['search_time_type'])){
            $search_time_type  = $param['search_time_type'];
        }
        if(!empty($param['start_time'])){
            $start_time  = $param['start_time'];
        }
        if(!empty($param['end_time'])){
            $end_time = $param['end_time'];
        }
        $sql_all  = "select count(a.id) as total from ".WSY_SHOP.".optiondeal_order a INNER JOIN ".WSY_USER.".weixin_users b ON a.user_id = b.id INNER JOIN ".WSY_SHOP.".optiondeal_shares_infos c ON a.stock_code = c.code WHERE a.customer_id = {$customer_id} ";  

        $sql_all2 = "SELECT a.id,
                a.user_id,
                a.stock_code,
                a.batchcode,
                a.capital,
                a.capital_ratio,
                a.droit_price,
                a.exercise_stock_price,
                a.exercise_cycle,
                a.profit_percent,
                a.settle_gain,
                a.close_stock_price,
                a.exercise_deadline,
                a.paystyle,
                a.alipay_trade_no,
                a.pay_time,
                a.status,
                a.offer_percent,
                a.invalid_reason,
                a.paystatus,
                a.createtime,
                a.min_exercise_day,
                a.gain_droit_time,
                a.cancel_time,
                a.settle_time,
                a.manual_exercise,
                b.phone,
                b.weixin_name,
                c.name
                FROM ".WSY_SHOP.".optiondeal_order a INNER JOIN ".WSY_USER.".weixin_users b ON a.user_id = b.id INNER JOIN ".WSY_SHOP.".optiondeal_shares_infos c ON a.stock_code = c.code WHERE a.customer_id = {$customer_id} AND a.isvalid = 1 ";

                /*************搜索条件start*************/
            $sql = '';    
            if( $batchcode != -1 )
            {
                $sql .= "AND a.batchcode = '{$batchcode}' ";
            }
            if( $phone != -1 )
            {
                $sql .= "AND b.phone = '{$phone}' ";
            }
            if( $user_id != -1 )
            {
                $sql .= "AND a.user_id = '{$user_id}' ";
            }
            if( $stock_code != -1 )
            {
                $sql .= "AND a.stock_code = '{$stock_code}'";
            }
            if( $capital != -1)
            {
                $sql .= "AND a.capital = '{$capital}' ";
            }
            if( $status != -1)
            {
                if($status == 3 )
                {
                    $sql.= "AND (a.status = 3 OR a.status = 4 ) ";
                }else
                {
                    $sql.= "AND a.status = '{$status}' ";
                }
            	
            }
            if( $search_time_type != -1 && $start_time!=-1 && $end_time!=-1 )//两个时间都填写时
            {
                switch ($search_time_type) {
                    case '1'://下单时间
                        $sql .= " AND a.createtime >= '{$start_time}' AND a.createtime <= '{$end_time}' ";
                        break;
                    case '2'://委托时间
                        $sql .= " AND a.pay_time >= '{$start_time}' AND a.pay_time <= '{$end_time}' ";
                        break;
                    case '3'://认购时间
                        $sql .= " AND a.gain_droit_time >= '{$start_time}' AND a.gain_droit_time <= '{$end_time}' ";
                        break;
                    case '4'://结算时间
                        $sql .= " AND a.settle_time >= '{$start_time}' AND a.settle_time <= '{$end_time}' ";
                        break;
                    case '5'://取消委托时间
                        $sql .= " AND a.cancel_time >= '{$start_time}' AND a.cancel_time <= '{$end_time}' ";
                        break;
                }
                
            }elseif($search_time_type != -1 && $start_time != -1 && $end_time == -1)//只填写了开始时间，结束时间为空空
            {
                switch ($search_time_type) {
                    case '1':
                        $sql .= " AND a.createtime >= '{$start_time}' ";
                        break;
                    case '2':
                        $sql .= " AND a.pay_time >= '{$start_time}' ";
                        break;
                    case '3':
                        $sql .= " AND a.gain_droit_time >= '{$start_time}' ";
                        break;
                    case '4':
                        $sql .= " AND a.settle_time >= '{$start_time}' ";
                        break;
                    case '5':
                        $sql .= " AND a.cancel_time >= '{$start_time}' ";
                        break;
                }
            }
        /*************搜索条件end*************/
        $sql .= " order by a.createtime desc ";

        $sql2 = $sql."limit {$start} , {$end} ";

        $sql_all  = $sql_all.$sql;        
        $sql_all2 = $sql_all2.$sql2;
        $res       = $this->db->getRow($sql_all);  //没有limit 算总条数 做分页用
        $res2      = $this->db->getAll($sql_all2);
        $pageCount = $res['total'];
        $data['pageCount'] = ceil($pageCount/$pageSize);
        $temp = array();
        $user_arr = array();
        $user_str = '';
        foreach ($res2 as $key => $value) {

            if($value['exercise_cycle'] % 7 == 0 && $value['exercise_cycle'] >= 7 )//取余  若结果为0代表能够整除  
            {  
            	
                $value['exercise_cycle'] = ($value['exercise_cycle']/7)."周";//行权周期
                
            }else
            {
                $value['exercise_cycle'] = $value['exercise_cycle']."天";
                
            }
            $value['offer_percent'] = $value['offer_percent']*100;
            $value['profit_percent'] = $value['profit_percent']*100;
            $value['begin_time'] = date('Y-m-d H:i:s',strtotime($value['gain_droit_time'])+($value['min_exercise_day']*24*60*60));
            $temp[] = $value;
            $user_arr[] = $value['user_id'];
        }
        $user_arr = array_unique($user_arr);
        $user_arr = array_values($user_arr);
        $user_str = implode(",",$user_arr);
        $user_str = rtrim($user_str,",");
        $res_blacklist = $this->query_blacklist($user_str,$customer_id);
        foreach ($temp as $k => &$v) {
            if(empty($res_blacklist)){
                $v['is_blacklist'] = 0;
            }
            foreach ($res_blacklist as $k1 => $v1){
                if($v1['user_id'] == $v['user_id']){
                    $v['is_blacklist'] = empty($v1['isvalid']) ? 0 : $v1['isvalid'];
                    break;
                }else{
                    $v['is_blacklist'] = 0;
                }
            }
        }
        $data['data'] = $temp;
        $data['res_blacklist'] =$res_blacklist;
        return $data;
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——获取订单详情数据
    开 发 者：wuzepeng
    开发日期： 2018-06-07
    重要说明：无
     */
    public function get_qiquan_order_details($id = '')
    {
        $sql = "SELECT a.id,
                a.user_id,
                a.stock_code,
                a.batchcode,
                a.capital,
                a.droit_price,
                a.offer_percent,
                a.poundage,
                a.create_stock_price,
                a.settle_gain,
                a.exercise_stock_price,
                a.exercise_cycle,
                a.profit_percent,
                a.close_stock_price,
                a.exercise_deadline,
                a.paystyle,
                a.alipay_trade_no,
                a.total_price,
                a.invalid_reason, 
                a.pay_time,
                a.status,
                a.paystatus,
                a.createtime,
                a.gain_droit_time,
                a.min_exercise_day,
                a.cancel_time,
                a.settle_time,
                a.manual_exercise,
                b.phone,
                b.weixin_name,
                c.name
                FROM ".WSY_SHOP.".optiondeal_order a INNER JOIN ".WSY_USER.".weixin_users b ON a.user_id = b.id INNER JOIN ".WSY_SHOP.".optiondeal_shares_infos c ON a.stock_code = c.code WHERE a.id = {$id} AND a.isvalid = 1 ";
        $data = $this->db->getRow($sql);
        if($data['exercise_cycle'] % 7 == 0 && $data['exercise_cycle'] >= 7 )//取余  若结果为0代表能够整除  
        {  
            $data['exercise_cycle'] = ($data['exercise_cycle'] /7)."周";//行权周期
        }else
        {
            $data['exercise_cycle'] = $data['exercise_cycle']."天";
        }
       	$data['profit_percent'] = $data['profit_percent']*100;
       	$data['offer_percent'] = $data['offer_percent']*100;
        $data['begin_time'] = date('Y-m-d H:i:s',strtotime($data['gain_droit_time'])+($data['min_exercise_day']*24*60*60));
        return $data;
    }

    /*
     *  黑名单操作
     *  $Author:   hjw
     *   2018-6-6
     */
    public function update_blacklist($param = array()){
        extract($param);
        $return  = array('errcode' => 400, 'errmsg' => '操作失败');
        $sql = "select id,isvalid from ".WSY_SHOP.".optiondeal_blacklist where customer_id = '".$customer_id."' and user_id = '".$user_id."'";
        $result = $this->db->getAll($sql)[0];
        if($result['id']){
            $update_sql = "update ".WSY_SHOP.".optiondeal_blacklist set isvalid = '".$status."' where id = '".$result['id']."'";
            $res_update = $this->db->query($update_sql);
            if($res_update){
                $return['errcode'] = 0;
                $return['errmsg'] = '操作成功';
            }else{
                $return['errcode'] = 400;
                $return['errmsg'] = '操作失败';
            }
        }else{
            $insert_sql = "insert into ".WSY_SHOP.".optiondeal_blacklist (user_id,isvalid,createtime,customer_id) VALUES ('".$user_id."','".$status."',now(),'".$customer_id."')";
            $res_insert = $this->db->query($insert_sql);
            if($res_insert){
                $return['errcode'] = 0;
                $return['errmsg'] = '操作成功';
            }else{
                $return['errcode'] = 400;
                $return['errmsg'] = '操作失败';
            }
        }
        return $return;
    }
    /*
     *  黑名单查询
     *  $Author:   hjw
     *   2018-6-6
     */
    public function query_blacklist($user_str = '',$customer_id){
        if(empty($user_str)){
            $user_str = 0;
        }
        $sql = "select id,user_id,isvalid from ".WSY_SHOP.".optiondeal_blacklist where user_id in (".$user_str.") and customer_id = '".$customer_id."'";
        $res = $this->db->getAll($sql);
        return $res;
    }
}//类结束