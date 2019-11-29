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


class model_promoter_card{
    var $db;
    var $model_common;
    var $shopmessage;
    function __construct()
    {
        $this->db = DB::getInstance();
        require_once('model/common.php');
        require_once($_SERVER["DOCUMENT_ROOT"].'/weixinpl/common/utility_shop.php');  //商城方法
        require_once($_SERVER['DOCUMENT_ROOT'].'/mshop/admin/Base/personalization/home_decoration/pink_selector_url.php');
        $this->model_common = new model_common();
        $this->shopmessage = new shopMessage_Utlity();
    }
   /*
    * 获取商城股东奖励设置
    * $Author: hjw$
    * $2018-05-16  $
    * 参数：
    */  
    public function get_shareholder_name($param = array()){
    	extract($param);
    	$sql = "select a_name,b_name,c_name,d_name from ".WSY_REBATE.".weixin_commonshop_shareholder where isvalid = true and customer_id = '".$customer_id."' limit 0,1";
    	$result = $this->db->getAll($sql)[0];
    	return $result;
    }
   /*
    * 获取名片设置
    * $Author: hjw$
    * $2018-05-16  $
    * 参数：
    */
    public function get_card_setting($param = array()){
    	extract($param);
    	$sql = "select pass_level,jump_url,jump_title,jump_linktype,name_onoff,level_onoff,address_onoff,weixin_onoff,qq_onoff,phone_onoff,tip_onoff,introduce_onoff,follow_onoff from weixin_commonshop_user_contact_setting where isvalid = true and customer_id = '".$customer_id."' limit 0,1";
    	$result = $this->db->getAll($sql)[0];
        if(empty($result)){
            $result = array(
                'pass_level' => '-1_1_2_3_4_5',
                'jump_url' =>'/weixinpl/common_shop/jiushop/index.php?customer_id='.$customer_id_en,
                'jump_title' => '首页',
                'jump_linktype' => '-2-1-首页',
                'name_onoff' => 1,
                'level_onoff' => 1,
                'address_onoff' => 1,
                'weixin_onoff' => 1,
                'qq_onoff' => 1,
                'phone_onoff' => 1,
                'tip_onoff' => 1,
                'introduce_onoff' => 1,
                'follow_onoff' => 1
            );
        }
    	return $result;
    }
   /*
    * 保存名片设置
    * $Author: hjw$
    * $2018-05-18  $
    * 参数：
    */
    public function save_card_setting($param = array()){
        extract($param);
        $res = array();
        $return = array('errcode'=>400,'errmsg'=>'保存失败');
        $param['jump_url'] = '';
        $param['jump_linktype'] = -1;
        $param['jump_title'] = '';
        $where   = "customer_id='".$customer_id."' and isvalid=true ";
        if($selector_id != "-1" && $selector_id != ""){
            $res = pink_selector_url($selector_id,'',$customer_id,$customer_id_en,$user_id);
            $param['jump_url'] = $res['url'];
            $param['jump_linktype'] = $res['linktype'];
            $param['jump_title'] = $res['title'];
        }
        $sql = "select id from weixin_commonshop_user_contact_setting where isvalid = true and customer_id = '".$customer_id."'";
        $result = $this->db->getAll($sql)[0];
        if($result['id']){
            $where .= " and id = '".$result['id']."'";
            $result_setting = $this->db->autoExecute('weixin_commonshop_user_contact_setting',$param,'update',$where);
        }else{
            $param['isvalid'] = 1;
            $result_setting = $this->db->autoExecute('weixin_commonshop_user_contact_setting',$param,'insert');
        }
        if($result_setting){
            $return['errcode'] = 0;
            $return['errmsg'] = "保存成功！";
        }
        return $return;
    }
}