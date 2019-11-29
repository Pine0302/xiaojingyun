<?php
/*
* 套餐相关
* $Author: 痴心绝对 $
* 2015-01-11  $
*/

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


class model_package{
	var $db;

	function __construct() {
        $this->db = DB::getInstance();
		var_dump($this->db);
    }

	/*¹ºÂò*/
	function buy(){
		return array();
		$sql = "select * from ubt_package where package_id='$package_id'";
		$row = $this->db->getRow($sql);
		if (!$row) {
			die('package not exists');
		}
		unset($row['package_id'], $row['add_time'], $row['status']);
		$row['kuid'] = $_SESSION['kuid'];
		$row['kname'] = $_SESSION['kname'];
		mt_srand(( double )microtime() * 1000000);
		$rand = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
		$row['order_sn'] = date('YmdHis'.$rand);
		$this->db->autoExecute('ubt_package_order', $row, 'insert');
	}

	/*Ê×Ò³*/
	function index(){
		return array();
		$rank = $this->db->getOne("select rank from ubt_user where kuid='$_SESSION[kuid]' ");
		if($rank){
			$next_rank = $this->db->getOne("select rank from ubt_package where rank>'$rank' ");
		}
		//Ì×²ÍÁÐ±í
		$sql = "select * from ubt_package where status=1 order by rank asc";
		$list = $this->db->getAll($sql);
		if($next_rank){
			$sql = "select rank from ubt_package where status=1 and rank>='$next_rank' order by rank asc";
			$next_rank = $this->db->getOne($sql);
		}
		return array('list'=>$list,'next_rank'=>$next_rank);
	}


}
