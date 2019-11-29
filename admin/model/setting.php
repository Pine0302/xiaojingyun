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


class model_setting{
	var $db;

	function __construct() 
	{
        $this->db = DB::getInstance();
    }

     /*
	 * zhoumuwang
	 * 判断商家是已经有配置
    */
	function is_existence($cust_id)
	{
		$sql  = 'select id from '.WSY_SHOP.'.integral_setting where cust_id = '.$cust_id;
		$data = $this->db->getRow($sql);
		return $data['id'];
	}

	/*
	 * zhoumuwang
	 * 插入或修改签到配置
    */
	function change($data)
	{
		$id = $this->is_existence($data['cust_id']);

		if($id)
		{
			$result = $this->db->autoExecute(WSY_SHOP.'.integral_setting', $data, 'update',"id = $id") ;
		}
		else
		{
			$result = $this->db->autoExecute(WSY_SHOP.'.integral_setting',$data, 'insert') ;
		}

		return $result;	
	}

	/*
	 * zhoumuwang
	 * 读取签到默认设置
    */
    function sign_read($data = array())
    {

    	$cust_id             = $data['cust_id'];
    	if(empty($cust_id)) return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');
    	
    	$sql       			 = "select name,sign_agreement,sign_onoff,agreement_onoff,sign_json,suspend_onoff from ".WSY_SHOP.".integral_setting where cust_id = $cust_id";
    	$result   			 = $this->db->getRow ($sql);
    	$result['sign_json'] = json_decode($result['sign_json'],ture);
    	$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取失败');
    	return $res;
    }
    
    /*
	 * 获取积分设置数据
	 * $Author: djy $
	 * 2017-08-28  $
    */
    function integral_setting_details($data = array())
    {
    	$cust_id        = $data['cust_id'];
    	if(empty($cust_id)) return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');
    	
    	$sql       = "select basic_json,shop_onoff,reward_onoff from ".WSY_SHOP.".integral_setting where cust_id = $cust_id";
    	$result    = $this->db->getRow ($sql);
    	$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取失败');
    	return $res;
    }
	
	/*
	 * 判断商家后台邮件提醒设置
	 * $Author: hzq $
	 * 2018-01-13  $
     */
    function select_remind_email_setting($customer_id="")
    {
    	if(empty($customer_id)) return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');
        $email_sql = "select id,isshangcheng,emails from mail_config where C_id='".$customer_id."'";
        $result    = $this->db->getRow($email_sql);
		$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取邮件设置失败');
        return $res;
    }
	
	/*
	 * 判断商家后台手机提醒设置
	 * $Author: hzq $
	 * 2018-01-13  $
     */
    function select_remind_phone_setting($customer_id="")
    {
		if(empty($customer_id)) return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');
        $phone_sql = "select id,isshop,isremainder,phone,acount,isshop_sendtype from sms_settings where isvalid=true and customer_id='".$customer_id."'";
        $result    = $this->db->getRow($phone_sql);
		$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取手机设置失败');
        return $res;
    }
	
	/*
	 * 判断商家后台自动审核设置
	 * $Author: hzq $
	 * 2018-01-13  $
     */
	 function select_is_delay_auto($customer_id="")
    {
		if(empty($customer_id)) return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');
        $delay_sql = "select is_order_delay_check_auto,order_delay_time from weixin_commonshops_extend where isvalid=true and customer_id='".$customer_id."'";
        $result    = $this->db->getRow($delay_sql);
		$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取后台审核设置失败');
        return $res;
    }
	
	/*
	 * 获取供应商后台设置的邮件和短信
	 * $Author: hzq $
	 * 2018-01-16  $
     */
    function select_supply_remind_setting($customer_id="",$supply_id="")
    {
		if(empty($customer_id)) return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');
		if(empty($supply_id))   return array('errcode' => 401,'errmsg'=>'缺少参数supply_id');
        $supply_sql = "select is_phone,phone,is_email,email from weixin_commonshop_supply_orderremind where id='".$supply_id."' and customer_id='".$customer_id."'";
        $result    = $this->db->getRow($supply_sql);
		$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取供应商后台设置失败');
        return $res;
    }
	
	/*
	 * 获取订货系统后台设置的邮件和短信
	 * $Author: hzq $
	 * 2018-01-16  $
     */
	function select_dh_remind_setting($customer_id="",$send_order_id=""){
		if(empty($customer_id)) 	return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');
		if(empty($send_order_id))   return array('errcode' => 401,'errmsg'=>'缺少参数send_order_id');
		$dh_sql = "select op.user_id,omn.mail_json,omn.phone_json,omn.phone_notice,omn.mail_notice from orderingretail_proxy as op left join orderingretail_msg_notice as omn on op.id=omn.proxy_id where omn.customer_id='".$customer_id."' and omn.proxy_id = '".$send_order_id."' and omn.isvalid = true and op.customer_id='".$customer_id."' and op.isvalid = true";
		$result    = $this->db->getRow($dh_sql);
		$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取f2c系统后台设置失败');
        return $res;
	}
	
	/*
	 * 获取f2c系统后台设置的邮件和短信
	 * $Author: hzq $
	 * 2018-01-16  $
     */
	function select_f2c_remind_setting($customer_id="",$send_order_id=""){
		if(empty($customer_id)) 	return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');
		if(empty($send_order_id))   return array('errcode' => 401,'errmsg'=>'缺少参数send_order_id');
		$f2c_sql = "select fop.is_open_phone,fop.is_open_email,fop.phone,fop.email,fa.user_id from ".WSY_F2C.".f2c_accounts as fa LEFT JOIN ".WSY_F2C.".f2c_user AS fu on fa.user_id = fu.account 
		left join ".WSY_F2C.".f2c_order_pc_remind as fop on fop.user_id=fu.id 
		where fop.customer_id='".$customer_id."' and fu.customer_id='".$customer_id."' and fa.id= '".$send_order_id."' and fa.customer_id='".$customer_id."' and fop.isvalid=true and fa.isvalid=true and fu.isvalid = true ";
	//	echo $f2c_sql;
		$result    = $this->db->getRow($f2c_sql);
		$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取f2c系统后台设置失败');
        return $res;
	}
	
	/*
	 * 查询用户信息
	 * $Author: hzq $
	 * 2018-01-18 $
	 */
	function select_user_info($customer_id="",$user_id=""){
		if(empty($customer_id)) 	return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');
		if(empty($user_id))   		return array('errcode' => 401,'errmsg'=>'缺少参数user_id');
		$user_sql = "select weixin_name,weixin_fromuser from weixin_users where isvalid=true and id='".$user_id."' and customer_id='".$customer_id."'";
		$result    = $this->db->getRow($user_sql);
		$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取商家信息成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取商家信息失败');
        return $res;
	}
	
	/*
	 * 查询平台邮箱自定义名称
	 * $Author: hzq $
	 * 2018-01-18 $
	 */
	function select_customer_email_info($customer_id=""){
		if(empty($customer_id)) 	return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');
		$sql = "SELECT name,is_open FROM weixin_commonshops_mail_set where isvalid=1 and customer_id='".$customer_id."'";
		$result    = $this->db->getRow($sql);
		$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取商家信息成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取商家信息失败');
        return $res;
	}

	/*
	 * 查询customer表的商城颜色
	 * $Author: hzq $
	 * 2018-01-18 $
	 */
	function get_shop_skin($customer_id){
		$sql = "select theme from customers where isvalid=true and id=".$customer_id;
		$res    = $this->db->getOne($sql);
		return $res;
	}
}
