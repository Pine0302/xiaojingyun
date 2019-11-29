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
 * Explain: 商城设置模型类
 */

class model_weixin_commonshops{
    public $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    /***
     * 功能描述：获取商城配置
     * @param $customer_id  商户id
     * @return array array()
     * author: chy
     * time：2018-5-12
     */
    public function get_setting($customer_id,$filed=null){
        $f = ' * ';
        if($filed){
            $f = $filed;
        }
        if($filed)
        $sql = "SELECT ".$f."  FROM weixin_commonshops WHERE customer_id='{$customer_id}' and isvalid = 1 ";
        $result = $this->db->getRow($sql);
        return $result;
    }




}

?>