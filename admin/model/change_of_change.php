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


class model_change_of_change{
	var $db;

	function __construct()
	{
        $this->db = DB::getInstance();
    }
	/*
	版权信息：秘密信息
	功能描述：零钱转赠页面数据
	开 发 者：wuzepeng
	开发日期：2018-09-20
	@param 
	重要说明：无
	*/
	public function money_bag_change_setting($customer_id)
	{

		$sql = "SELECT id,transfer_onoff,min_money,type,remark FROM ".WSY_SHOP.".moneybat_transfer_setting where customer_id='".$customer_id."' LIMIT 1 ";

		$result= $this->db->getRow($sql);

		return $result;
	}
	/*
	版权信息：秘密信息
	功能描述：保存零钱转赠页面数据
	开 发 者：wuzepeng
	开发日期：2018-09-20
	@param 
	重要说明：无
	*/
	public function save_money_bag_change_setting($param=array())
	{
		extract($param);
		$time = date('Y-m-d H:i:s');
		switch ($op) {
			case 'insert':
				$sql = "INSERT INTO ".WSY_SHOP.".moneybat_transfer_setting (customer_id,transfer_onoff,min_money,type,remark,createtime) VALUES ('{$customer_id}','{$transfer_onoff}','{$min_money}','{$type}','{$remark}','{$createtime}') ";
				break;
			case 'update':
				$sql = "UPDATE ".WSY_SHOP.".moneybat_transfer_setting SET transfer_onoff = '{$transfer_onoff}' , min_money = '{$min_money}' , type = '{$type}' , remark = '{$remark}' WHERE customer_id = '{$customer_id}' ";
				break;
			default:
				die('无此操作！');
				break;
		}
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
	功能描述：零钱转区块链积分页面数据
	开 发 者：wuzepeng
	开发日期：2018-09-20
	@param 
	重要说明：无
	*/
	public function money_bag_change_block_chain_setting($customer_id)
	{
		$sql_on_off = "SELECT on_off FROM ".WSY_SHOP.".block_chain_setting where customer_id=".$customer_id." LIMIT 1 ";
		$on_off = $this->db->getOne($sql_on_off);
		$sql = "SELECT id,block_onoff,min_money,type,proportion,remark FROM ".WSY_SHOP.".moneybat_blockchain_setting where customer_id=".$customer_id." LIMIT 1 ";
		$result = $this->db->getRow($sql);
		$result['block_setting_onoff'] = $on_off;
		return $result;
	}
	/*
	版权信息:  秘密信息
	功能描述：保存零钱转区块链积分页面数据
	开 发 者：wuzepeng
	开发日期：2018-09-20
	@param 
	重要说明：无
	*/
	public function save_change_block_chian_setting($param=array())
	{
		extract($param);
		switch ($op) {
			case 'insert':
				$sql = "INSERT INTO ".WSY_SHOP.".moneybat_blockchain_setting (customer_id,block_onoff,min_money,type,remark,proportion) VALUES ('{$customer_id}','{$block_onoff}','{$min_money}','{$type}','{$remark}','{$proportion}') ";
				break;
			case 'update':
				$sql = "UPDATE ".WSY_SHOP.".moneybat_blockchain_setting SET block_onoff = '{$block_onoff}' , min_money = '{$min_money}' , type = '{$type}' , remark = '{$remark}' , proportion = '{$proportion}' WHERE customer_id = '{$customer_id}' ";
				break;
			default:
				die('无此操作！');
				break;
		}
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

}//类结束
