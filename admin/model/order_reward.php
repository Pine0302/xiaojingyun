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

/**
 * User: chy
 * Date: 2018/5/12
 * Time: 14:32
 * Explain: 订单奖励模型类
 */

class model_order_reward{
    public $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    /***
     * 功能描述：获取订单奖励配置
     * @param $customer_id  商户id
     * @return array array()
     * author: chy
     * time：2018-5-12
     */
    public function get_setting($customer_id){
        $sql = "SELECT *  FROM ".WSY_SHOP.".order_reward_order_setting WHERE customer_id='{$customer_id}' and isvalid = 1 ";
        $result = $this->db->getRow($sql);
        return $result;
    }

    /***
     * 功能描述：修改订单奖励配置
     * @param $customer_id  商户id
     * @param $array  各等级配置 [[等级,比例/固定金额],[1,10],[2,10],[3,10]]
     * @return true/false
     * author: chy
     * time：2018-5-12
     */
    public function add_setting($customer_id,$array=[]){

            var_dump($array);
            #新增记录
            $this->db->autoExecute(WSY_SHOP.'.order_reward_order_setting',
                array(
                 'customer_id'=>$customer_id,
                 'isvalid'=>true,
                 'json'=>$array['json'],
                 'type'=>$array['type'],
                 'isopen'=>$array['isopen'],
                ),
                'insert') ;

        return true;
    }

    /***
     * 功能描述：修改订单奖励配置
     * @param $customer_id  商户id
     * @param $array  各等级配置 [[等级,比例/固定金额],[1,10],[2,10],[3,10]]
     * @return true/false
     * author: chy
     * time：2018-5-12
     */
    public function update_setting($customer_id,$id,$array=[]){


        #新增记录
        $this->db->autoExecute(WSY_SHOP.'.order_reward_order_setting',
            array(
                'json'=>$array['json'],
                'type'=>$array['type'],
                'isopen'=>$array['isopen'],
            ),
            'update',"customer_id = {$customer_id} and isvalid = true and id={$id}") ;

        return true;
    }


    public function order_poll($customer_id,$condition = [],$page,$pageNum){


         $sql = "SELECT poll.* ,log.money,u.weixin_name,u.weixin_headimgurl FROM ".WSY_SHOP.".order_reward_order_poll poll
         left join ".WSY_SHOP." .order_reward_order_log log on poll.batchcode = log.batchcode
         left join weixin_users u on poll.user_id = u.id 
         WHERE poll.customer_id='{$customer_id}' and poll.isvalid = 1 and poll.status=".$condition['status'];
        if( !empty($condition['search_batchcode']) ){
            $sql .=  ' and poll.batchcode ="'.$condition['search_batchcode'].'"';
        }
        if( !empty($condition['search_starttime']) ){
            $sql .=  ' and poll.createtime >"'.$condition['search_starttime'].'"';
        }
        if( !empty($condition['search_endtime']) ){
            $sql .=  ' and poll.createtime <"'.$condition['search_endtime'].'"';
        }
        $sql .=  ' order by  poll.createtime desc ';
        $page_num = ($page-1) * $pageNum;
        $sql .= " limit {$page_num}, {$pageNum}";
        //echo $sql;
        $result = $this->db->getAll($sql);
        //var_dump($result);
        return $result;


    }

	public function order_poll_all($customer_id,$condition = []){


		$sql = "SELECT poll.* ,log.money,u.weixin_name FROM ".WSY_SHOP.".order_reward_order_poll poll
         left join ".WSY_SHOP." .order_reward_order_log log on poll.batchcode = log.batchcode
         left join weixin_users u on poll.user_id = u.id 
         WHERE poll.customer_id='{$customer_id}' and poll.isvalid = 1 and poll.status=".$condition['status'];
		if( !empty($condition['search_batchcode']) ){
			$sql .=  ' and poll.batchcode ="'.$condition['search_batchcode'].'"';
		}
		if( !empty($condition['search_starttime']) ){
			$sql .=  ' and poll.createtime >"'.$condition['search_starttime'].'"';
		}
		if( !empty($condition['search_endtime']) ){
			$sql .=  ' and poll.createtime <"'.$condition['search_endtime'].'"';
		}
		$sql .=  ' order by  poll.createtime desc ';
		//echo $sql;
		$result = $this->db->getAll($sql);
		//var_dump($result);
		return $result;


	}


    public function order_poll_count($customer_id,$condition = []){


        $sql = "SELECT count(1) as count FROM ".WSY_SHOP.".order_reward_order_poll poll
         left join ".WSY_SHOP." .order_reward_order_log log on poll.batchcode = log.batchcode 
         left join weixin_users u on poll.user_id = u.id 
         WHERE poll.customer_id='{$customer_id}' and poll.isvalid = 1  and  poll.status=".$condition['status'];

        if( !empty($condition['search_batchcode']) ){
            $sql .=  ' and poll.batchcode ="'.$condition['search_batchcode'].'"';
        }
        if( !empty($condition['search_starttime']) ){
            $sql .=  ' and poll.createtime >'.$condition['search_starttime'];
        }
        if( !empty($condition['search_endtime']) ){
            $sql .=  ' and poll.createtime <'.$condition['search_endtime'];
        }

        //echo $sql;
        $result = $this->db->getRow($sql);

        return $result['count'];


    }


    public function order_poll_log($customer_id,$condition = [],$page,$pageNum){

        if(empty($condition['search_batchcode'])){
            return [];
        }
        $sql = "SELECT *  FROM ".WSY_SHOP.".order_reward_order_poll_log WHERE customer_id='{$customer_id}' and isvalid = 1 ";
        $sql .=  ' and batchcode ="'.$condition['search_batchcode'].'"';
        $page_num = ($page-1) * $pageNum;
        $sql .= " limit {$page_num}, {$pageNum}";
        //echo $sql;
        $result = $this->db->getAll($sql);
        //var_dump($result);
        return $result;


    }

    public function order_poll_log_count($customer_id,$condition = []){

        if(empty($condition['search_batchcode'])){
            return 0;
        }
        $sql = "SELECT count(1) as count  FROM ".WSY_SHOP.".order_reward_order_poll_log WHERE customer_id='{$customer_id}' and isvalid = 1 ";

        $sql .=  ' and batchcode ="'.$condition['search_batchcode'].'"';


        //echo $sql;
        $result = $this->db->getRow($sql);
        return $result['count'];


    }


    public function order_poll_order($customer_id,$condition = [],$page,$pageNum){

        if(empty($condition['search_batchcode'])){
            return [];
        }
        $sql = "SELECT user.*,u.weixin_name  FROM ".WSY_SHOP.".order_reward_order_user user
        left join weixin_users u on user.user_id = u.id 
        WHERE user.customer_id='{$customer_id}' and user.isvalid = 1 ";

        $sql .=  ' and batchcode ="'.$condition['search_batchcode'].'"';

        $page_num = ($page-1) * $pageNum;
        $sql .= " limit {$page_num}, {$pageNum}";
        //echo $sql;
        $result = $this->db->getAll($sql);
        //var_dump($result);
        return $result;


    }


    public function order_poll_order_count($customer_id,$condition = []){

        if(empty($condition['search_batchcode'])){
            return 0;
        }
        $sql = "SELECT count(1) as count  FROM ".WSY_SHOP.".order_reward_order_user user
        left join weixin_users u on user.user_id = u.id 
        WHERE user.customer_id='{$customer_id}' and user.isvalid = 1 ";

        $sql .=  ' and batchcode ="'.$condition['search_batchcode'].'"';

        //echo $sql;
        $result = $this->db->getRow($sql);
        return $result['count'];

    }






}

?>