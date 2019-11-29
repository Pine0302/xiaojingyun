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


class model_block_chain_api{
	var $db;

	function __construct()
	{
        $this->db = DB::getInstance();
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
        $query = "select user_id from ".WSY_USER.".system_user_t where customer_id='" . $customer_id . "' and account = '".$mobile."' and isvalid = true limit 1";
        $res1 = $this->db->getRow($query);
        if($res1['user_id']){
            $select = "select id,block_chain_openid from ".WSY_USER.".weixin_users where customer_id = '".$customer_id."' and isvalid = true and id = '".$res1['user_id']."'";
            $res = $this->db->getRow($select);
            if($res['block_chain_openid'] != -1 && $res['block_chain_openid']){
                $user_id = $res['id']; 
            }else if($res['id'] && ($res['block_chain_openid'] == -1 || empty($res['block_chain_openid']))){
                $update = "update ".WSY_USER.".weixin_users set block_chain_openid = '{$openid}' where id = '".$res['id']."' and '".$customer_id."' and isvalid = true";
                $result2 = $this->db->query($update);
                $user_id = $res['id']; 
            }else{
                $sql = "INSERT INTO ".WSY_USER.".weixin_users (name,phone,isvalid,createtime,customer_id,weixin_name,weixin_headimgurl,fromw,block_chain_openid) VALUES ('{$nickname}','{$mobile}',true,now(),'{$customer_id}','{$nickname}','{$head_img}',12,'{$openid}') ";
                $result = $this->db->query($sql);
                $user_id =  $this->db->insert_id();
            }
        }else{
            $sql = "INSERT INTO ".WSY_USER.".weixin_users (name,phone,isvalid,createtime,customer_id,weixin_name,weixin_headimgurl,fromw,block_chain_openid) VALUES ('{$nickname}','{$mobile}',true,now(),'{$customer_id}','{$nickname}','{$head_img}',12,'{$openid}') ";
            $result = $this->db->query($sql);
            $user_id =  $this->db->insert_id();
            $sql1 = "INSERT INTO ".WSY_USER.".system_user_t (user_id,customer_id,isvalid,createtime,account,password) VALUES ('{$user_id}','{$customer_id}',true,now(),'{$mobile}','".md5(rand(100000,999999))."') ";
            $result1 = $this->db->query($sql1);
        }
        return $user_id;

    }
    public function http_url($customer_id)
    {
    	$query = "select url from ".WSY_SHOP.".block_chain_setting where customer_id='" . $customer_id . "'";
    	$res = $this->db->getRow($query);
    	return $res['url'];
    }
   /*
    * H5退出
    * $Author: hjw$
    * $2018-10-10  $
    * 参数：
    */
    public function H5_loginout($user_id){
	    //app消息互通：登陆成功吧需要退出登陆的用户进行软删除 
        $up_query = "update h5_loginout set isvalid=0 where user_id=".$user_id." and type=1";
        //echo $up_query;exit;
        $result = $this->db->query($up_query);
        return $result;
    }
    /*
    * 获取用户信息
    * $Author: hjw$
    * $2018-10-8  $
    * 参数：
    */
    public function get_user_msg($data = array()){
    	extract($data);
    	$query = "select weixin_fromuser,weixin_headimgurl from ".WSY_USER.".weixin_users where isvalid=true and id=".$user_id." and customer_id='" . $customer_id . "' limit 0,1";
    	$res = $this->db->getRow($query);
    	return $res;
    }
 }