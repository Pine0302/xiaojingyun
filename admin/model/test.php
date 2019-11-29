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


class model_test{
    var $db;
    var $model_common;
    var $shopmessage;
    function __construct()
    {
        $this->db = DB::getInstance();
        require_once('model/common.php');
        $this->model_common = new model_common();
    } 

   
    
    //记录文件分片上传记录
    public function save_video_tmp($file_name,$chunk,$customer_id){
        $encode = mb_detect_encoding($file_name, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')); //1 获取当前字符串的编码
        $file_name = mb_convert_encoding($file_name, 'UTF-8', $encode);//2 将字符编码改为utf-8
        $data['customer_id'] = $customer_id;
        $data['chunk']       = $chunk;
        $data['file_name']   = $file_name;
        $data['isvalid']     = true;
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_order_blessing_video_tmp', $data, 'insert');
        return $result;
    }
    







   

}//类结束
