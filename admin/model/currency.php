<?php

/*
	数据库操作示例:

	$data = $this->db->getAll ($sql);
	$data = $this->db->getRow ($sql);
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

/*
版权信息:  秘密信息
功能描述：零钱转换系统数据操作
开 发 者：陶晋
开发日期： 2017-10-12
重要说明：无
 */

class model_currency{
    public $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    /***
     * 功能描述：查询零钱转换开关状态
     * @param $customer_id  商户id
     * @return array array('is_open'=>1,'switch_type'=>1) 开关状态
     * @author: taojin $
     * 2017-10-12  $
     */
    public function currency_switch($customer_id){
        $sql = "SELECT is_open,switch_type,remark FROM ".WSY_SHOP.".moneybag_exchange_t WHERE customer_id='{$customer_id}' and isvalid = 1 ";
        $result = $this->db->getRow($sql);
        return $result;
    }

/***
     * 功能描述：查询零钱转换开关状态（包括贷款，购物币，区块链）
     * @param $customer_id  商户id
     * @return array array('is_open'=>1,'switch_type'=>1) 开关状态
     * @author: 林荣碟 $
     * 2018-10-12  $
     */
    public function new_currency_switch($customer_id){
         
        $result =[];
        //转购物币的开关is_open_change 转区块链的开关block_onoff 转购物币的开关is_open 开启区块链领取积分on_off 因为几个开关不存在相互影响的关系不可连表查询
        $sql = "SELECT a.is_open_change FROM ".WSY_DH.".orderingretail_change_account_setting a WHERE a.customer_id='{$customer_id}' ";
        $res= _mysql_query($sql) or die('Query failed 76: ' . mysql_error());
        while( $row = mysql_fetch_object($res) ){
            $result['is_open_change'] = $row->is_open_change;
        }
 
        $sql2 = "SELECT c.is_open FROM ".WSY_SHOP.".moneybag_exchange_t c WHERE c.customer_id='{$customer_id}' ";
        $res= _mysql_query($sql2) or die('Query failed 76: ' . mysql_error());
        while( $row = mysql_fetch_object($res) ){
            $result['is_open'] = $row->is_open;
        }

        $sql3 = "SELECT b.block_onoff,d.on_off FROM ".WSY_SHOP.".moneybat_blockchain_setting b INNER join ".WSY_SHOP.".block_chain_setting d on b.customer_id=d.customer_id  WHERE b.customer_id='{$customer_id}' ";
        $res= _mysql_query($sql3) or die('Query failed 76: ' . mysql_error());
        while( $row = mysql_fetch_object($res) ){
            $result['block_onoff'] = $row->block_onoff;
            $result['on_off']      = $row->on_off;
        }

        return $result;
    }
    /***
     * 功能描述：查询零钱转换配置详情
     * @param $customer_id  商户id
     * @return array array('multiple_type'=>1,'multiple_diy'=>1,'minimum'=>1,'conversion_ratio'=>1,'type'=>1) 零钱转换配置详情
     * @author: taojin $
     * 2017-10-12  $
     */
    public function currency_config($customer_id){
        $sql = "SELECT multiple_type,multiple_diy,minimum,conversion_ratio,type,customer_id FROM ".WSY_SHOP.".moneybag_exchange_extend_t WHERE customer_id='{$customer_id}' and isvalid = 1 ";
        $result = $this->db->getRow($sql);
        return $result;
    }

    /***
     * 功能描述：查询零钱转换配置详情
     * @param $customer_id  商户id
     * @return array array('multiple_type'=>1,'multiple_diy'=>1,'minimum'=>1,'conversion_ratio'=>1,'type'=>1) 零钱转换配置详情
     * @author: taojin $
     * 2017-10-12  $
     */
    public function currency_config_for_update($customer_id){
        $sql = "SELECT multiple_type,multiple_diy,minimum,conversion_ratio,type,customer_id FROM ".WSY_SHOP.".moneybag_exchange_extend_t WHERE customer_id='{$customer_id}' and isvalid = 1 FOR UPDATE ";
        $result = $this->db->getRow($sql);
        return $result;
    }

    /***
     * 功能描述：查询订货系统零钱转换配置详情
     * @param $customer_id  商户id
     * @author: lj $
     * 2017-11-30  $
     */
    public function account_config($customer_id){
        $sql = "SELECT is_open_change,min_change_price,coefficient,change_rule,comment from ".DB_NAME.".orderingretail_change_account_setting  WHERE customer_id='{$customer_id}' limit 1";
        $result = $this->db->getRow($sql);
        return $result;
    }

    /***
     * 功能描述：查询订货系统零钱转换配置详情
     * @param $customer_id  商户id
     * @param $user_id
     * @author: lj $
     * 2017-11-30  $
     */
    public function or_proxy_info($customer_id,$user_id){
        $sql = "SELECT id from ".DB_NAME.".orderingretail_proxy  WHERE customer_id='{$customer_id}' and user_id='{$user_id}' and status='checked' and  isvalid = 1 and expiretime>now() limit 1";
        $result = $this->db->getRow($sql);
        return $result;
    }

    /***
     * 功能描述：查询订货系统设置
     * @param $customer_id  商户id
     * @author: lj $
     * 2017-11-30  $
     */
    public function or_setting($customer_id){
        $sql = "SELECT a.receive_mode,a.isopen_proxy,b.isopen_account from ".WSY_DH.".orderingretail_setting a 
        INNER JOIN ".WSY_DH.".orderingretail_account_setting b ON a.customer_id = b.customer_id  WHERE a.customer_id='{$customer_id}' and a.isvalid = 1 limit 1";
        $result = $this->db->getRow($sql);
        return $result;
    }

    /***
     * 功能描述：查询用户零钱
     * @param $user_id      用户id
     * @param $customer_id  商户id
     * @return array array('multiple_type'=>1,'multiple_diy'=>1,'minimum'=>1,'conversion_ratio'=>1,'type'=>1) 零钱转换配置详情
     * @author: taojin $
     * 2017-10-12  $
     */
    public function get_user_currency($user_id,$customer_id = -1){
        $condition = '';
        if($customer_id != -1) $condition = " AND customer_id = '{$customer_id}' ";
        $sql = "SELECT balance FROM ".DB_NAME.".moneybag_t WHERE isvalid = 1 and user_id='{$user_id}' {$condition} ";
        $result = $this->db->getOne($sql);
        return $result;
    }

    /***
     * 功能描述：查询用户零钱+加锁
     * @param $user_id      用户id
     * @param $customer_id  商户id
     * @return array array('multiple_type'=>1,'multiple_diy'=>1,'minimum'=>1,'conversion_ratio'=>1,'type'=>1) 零钱转换配置详情
     * @author: taojin $
     * 2017-10-12  $
     */
    public function get_user_currency_for_update($user_id,$customer_id = -1){
        $condition = '';
        if($customer_id != -1) $condition = " AND customer_id = '{$customer_id}' ";
        $sql = "SELECT balance FROM ".DB_NAME.".moneybag_t WHERE isvalid = 1 and user_id='{$user_id}' {$condition} FOR UPDATE ";
        $result = $this->db->getOne($sql);
        return $result;
    }

    function get_isopen($customer_id)
    {
        $isOpen_callback = 0;
        $query = "SELECT isOpen_callback FROM moneybag_rule WHERE isvalid=true AND customer_id=".$customer_id." LIMIT 1";
        $result= _mysql_query($query) or die('Query failed 76: ' . mysql_error());
        while( $row = mysql_fetch_object($result) ){
            $isOpen_callback = $row->isOpen_callback;
        }

        return $isOpen_callback;
    }

    /***
     * 功能描述：零钱转换
     * @param array $data  转换数据
     * @return array array('multiple_type'=>1,'multiple_diy'=>1,'minimum'=>1,'conversion_ratio'=>1,'type'=>1) 零钱转换配置详情
     * @author: taojin $
     * 2017-10-12  $
     */
    public function transform_currency($data){
        $money_bag = $data['balance'] - $data['price'];
        $batchcode = $this->make_transform_order_batchcode($data['user_id']);    //生成订单号
        $create_time = date('Y-m-d H:i:s',time());
        //查询用户信息
        $user_info = $this->get_user_info($data['user_id']);

        //扣除转换的零钱
       $res =  $this->db->autoExecute(DB_NAME.'.moneybag_t',array('balance' => $money_bag),'update',"customer_id='{$data['customer_id']}' and user_id='{$data['user_id']}' and isvalid=1 ");
        switch ($data['type']){
            case 1://购物币类型
                //转成对应购物币
                $type_name = '购物币';
                $currency = $data['price']*$data['conversion_ratio'];
                $sql = "update ".DB_NAME.".weixin_commonshop_user_currency set currency = currency + $currency  where customer_id='{$data['customer_id']}'  and user_id='{$data['user_id']}'";
                $this->db->query($sql);
                //添加购物币日志
                $this->db->autoExecute(DB_NAME.'.weixin_commonshop_currency_log', array('customer_id' => $data['customer_id'],'user_id' => $data['user_id'],'cost_money' => $data['price'],'cost_currency' => $currency,'after_currency' => $data['after_currency'],'batchcode' => $batchcode,'status' => 1,'type' => 1,'class'=>19,'createtime'=>$create_time,'isvalid' => true,'remark'=>"零钱转换成{$type_name}"), 'insert') ;
                break;
            default:

                break;
        }
        //添加零钱日志
        $this->db->autoExecute(DB_NAME.'.moneybag_log', array('customer_id' => $data['customer_id'],'user_id' => $data['user_id'],'isvalid'=>true,'before_money'=>$data['balance'],'after_money'=>$money_bag,'money'=>$data['price'],'type'=>1,'batchcode'=>$batchcode,'pay_style'=>31,'createtime'=>$create_time,'operation_user'=>mysql_escape_string($user_info['name']),'remark'=>"零钱转换成{$type_name}"), 'insert') ;

        //添加零钱转化日志
        $this->db->autoExecute(WSY_SHOP.'.moneybag_exchange_log_t', array('customer_id' => $data['customer_id'],'user_id' => $data['user_id'],'money' => $data['price'],'conversion_ratio' => $data['conversion_ratio'],'switch_type' => $data['type'],'remark' => $batchcode,'createtime' => $create_time,'operation_user'=>mysql_escape_string($user_info['name']),'isvalid' => true), 'insert') ;

        return true;
    }

    /***
     * 功能描述：生成零钱转化订单号
     * @param array $user_id  用户id
     * @return string 订单号
     * @author: taojin $
     * 2017-10-13  $
     */
    protected function make_transform_order_batchcode($user_id){
        /* 订单号随机3位数*/
        $arr_rand=array();
        while(count($arr_rand)<3)
        {
            $arr_rand[]=rand(0,9);
            $arr_rand=array_unique($arr_rand);
        }
        $str_rand = implode("",$arr_rand);
        $stringtime = date("Y-m-d H:i:s", time());
        $batchcode_time  = strtotime($stringtime);
        $order_batchcode  = $user_id . $batchcode_time . $str_rand;
        return $order_batchcode;
    }

    /***
     * 功能描述：获取用户信息
     * @param array $user_id  用户id
     * @return array 用户信息
     * @author: taojin $
     * 2017-10-13  $
     */
    public function get_user_info($user_id){
        $sql="SELECT name FROM ".DB_NAME.".weixin_users where id = '{$user_id}' and isvalid = true";
        $user_info = $this->db->getRow($sql);
        return $user_info;
    }

    /***
     * 功能描述：获取用户信息
     * @param array $data  array('page'=>1,'page_size'=>1,'user_id'=>1,'customer_id'=>1)
     * @return array 零钱转换记录信息
     * @author: taojin $
     * 2017-10-13  $
     */
    public function moneybag_exchange_log($data){
        $page = ($data['page']-1)*$data['page_size'].','.$data['page_size'];
//        $page = "0,{$data['page_size']}";
        $sql="SELECT money,createtime,switch_type FROM ".WSY_SHOP.".moneybag_exchange_log_t where customer_id = '{$data['customer_id']}' and user_id = '{$data['user_id']}' and isvalid = true ORDER BY id DESC limit $page ";
        $moneybag_exchange_log = $this->db->getAll($sql);
        if($moneybag_exchange_log){
            $ra = array();
            foreach ($moneybag_exchange_log as $k => $v){
                $moneybag_exchange_log[$k]['month']     = date('m',strtotime($v['createtime']));
                $moneybag_exchange_log[$k]['year_month']= date('Y-m',strtotime($v['createtime']));
                if($v['switch_type']==1){
                    $moneybag_exchange_log[$k]['text']      = '转换成购物币';
                }else if($v['switch_type']==2){
                    $moneybag_exchange_log[$k]['text']      = '转换成货款';
                }else if($v['switch_type']==3){
                    $moneybag_exchange_log[$k]['text']      = '转换成区块链积分';
                }
                $ra[$moneybag_exchange_log[$k]['year_month']][] = $moneybag_exchange_log[$k];
            }
            $return_array = array_values($ra);
        }
        return $return_array;
    }

    /***
     * 功能描述：查询是否开启app功能 用于零钱页面
     * @param int $customer_id  商户号
     * @return array 零钱转换记录信息
     * @author: taojin $
     * 2017-10-13  $
     */
    public function check_app_is_open($customer_id){
        $sql = "select funs.id from ".DB_NAME.".columns as col inner join ".DB_NAME.".customer_funs as funs where col.sys_name='微商App' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
        $result = $this->db->getOne($sql);
        return $result;
    }

    /***
     * 功能描述：查询零钱转换配置(exchange_t表)
     * @param $customer_id  商户id
     * @author: zhangqiusong $
     * 2017-10-13  $
     */
    public function get_exchange_t($customer_id){
        $sql = "select id,is_open,switch_type,remark from ".WSY_SHOP.".moneybag_exchange_t where customer_id=".$customer_id." and isvalid = 1 ";
        $result = $this->db->getAll($sql);
        return $result;
    }

    /***
     * 功能描述：查询零钱转换配置(extend_t表)
     * @param $customer_id  商户id
     * @author: zhangqiusong $
     * 2017-10-13  $
     */
    public function get_extend_t($customer_id){
        $sql = "select multiple_type,multiple_diy,minimum,conversion_ratio,type from ".WSY_SHOP.".moneybag_exchange_extend_t where customer_id=".$customer_id." and isvalid = 1 ";
        $result2 = $this->db->getAll($sql);
        return $result2;
    }

    /***
     * 功能描述：修改零钱转换配置(exchange_t表)
     * @param $customer_id  商户id
     * @author: zhangqiusong $
     * 2017-10-13  $
     */
    public function update_exchange_t($data){
        extract($data);
        $sql = "select id from ".WSY_SHOP.".moneybag_exchange_t where isvalid = true and customer_id = ".$customer_id;
        $result_id = $this->db->getOne($sql);
        if ($result_id > 0) 
        {
            $sql = "UPDATE ".WSY_SHOP.".moneybag_exchange_t SET is_open = '{$is_open}',switch_type = '{$switch_type}',remark = '{$remark}',createtime = '{$createtime}' WHERE isvalid = true AND customer_id = ".$customer_id;
            $update = $this->db->query($sql);
        }
        else
        {
            $sql = "INSERT INTO ".WSY_SHOP.".moneybag_exchange_t(is_open,isvalid,customer_id,switch_type,remark,createtime) VALUES ('{$is_open}',true,'{$customer_id}','{$switch_type}','{$remark}','{$createtime}')";
            $update = $this->db->query($sql);
        }

        return $update;
    }

    /***
     * 功能描述：修改零钱转换配置(extend_t表)
     * @param $customer_id  商户id
     * @author: zhangqiusong $
     * 2017-10-13  $
     */
    public function update_extend_t($data2){
        extract($data2);
        $sql = "select id from ".WSY_SHOP.".moneybag_exchange_extend_t where isvalid = true and customer_id = ".$customer_id;
        $result_id = $this->db->getOne($sql);
        if ($result_id > 0) 
        {
            $sql = "UPDATE ".WSY_SHOP.".moneybag_exchange_extend_t SET multiple_type = '{$multiple_type}',multiple_diy = '{$multiple_diy}',minimum = '{$minimum}',conversion_ratio = '{$conversion_ratio}',type = '{$type}',createtime = '{$createtime}' WHERE isvalid = true AND customer_id = ".$customer_id;
            $update2 = $this->db->query($sql);
        }
        else
        {
            $sql = "INSERT INTO ".WSY_SHOP.".moneybag_exchange_extend_t(customer_id,multiple_type,multiple_diy,minimum,conversion_ratio,type,isvalid,createtime) VALUES ('{$customer_id}','{$multiple_type}','{$multiple_diy}','{$minimum}','{$conversion_ratio}','{$type}',true,'{$createtime}')";
            $update2 = $this->db->query($sql);
        }
        
        return $update2;   
    }    


    /***
     * 功能描述：增加零钱转换配置(exchange_t表)
     * @param $customer_id  商户id
     * @author: zhangqiusong $
     * 2017-10-13  $
     */
     public function add_exchange_t($data){
        $add = $this->db->autoExecute(WSY_SHOP.'.moneybag_exchange_t', $data, 'insert') ;
        return $add;
     }

    /***
     * 功能描述：增加零钱转换配置(extend_t表)
     * @param $customer_id  商户id
     * @author: zhangqiusong $
     * 2017-10-13  $
     */
     public function add_extend_t($data2){
        $add2 = $this->db->autoExecute(WSY_SHOP.'.moneybag_exchange_extend_t', $data2, 'insert') ;
        return $add2;
     }     

    /***
     * 功能描述：转换日志列表
     * @param $customer_id  商户id
     * @author: zhangqiusong $
     * 2017-10-16  $
     */
    public function get_conversion_log($data){
        //获取参数并分解
        $customer_id    = $data['customer_id'];
        $page           = $data['page'];
        $page_size      = $data['page_size'];
        //解码
        $search_key     = json_decode($data['search_key'],true);
        
        $user_id          = $search_key['user_id'];
        // $switch_type    = $search_key['switch_type'];
        
        //拼接查询语句
        $where = ' customer_id='.$customer_id.' and isvalid = 1 ';
        if(!empty($user_id)){
            $where .= " and user_id LIKE '%".$user_id."%' ";
        }
        // if(!empty($switch_type)){
        //     $where .= " and id = ".$switch_type;
        // }

        $count_sql = "select count(1) as sum
                      from ".WSY_SHOP.".moneybag_exchange_log_t 
                      where ".$where;

        $all_num  = $this->db->getAll($count_sql)[0]['sum'];

        $count   = ceil($all_num/$page_size);
        if($page > $count){
            $page = $count;
        }
        if($page < 1 || empty($page)){
            $page = 1;
        }

        $offset = ($page-1)*$page_size;
        $limit  = ' limit '.$offset.','.$page_size;
        
        //活动列表
        $sql = "select id,user_id,money,conversion_ratio,switch_type,remark,createtime,operation_user
                from ".WSY_SHOP.".moneybag_exchange_log_t 
                where ".$where." order by id desc ".$limit;
    //  echo $sql;
        $result = $this->db->getAll($sql);
        $data2['list']=$result;
        $data2['page']          = $count;
        $data2['pagenum']       = $page;
        return $data2;
    }

    /***
     * 功能描述：查询零钱转赠开关
     * @param $customer_id  商户id
     * @author: zhaipeibin $
     * 2018-9-22  $
     */
    function get_istransfer($customer_id)
    {
        $transfer_onoff = 0;
        $query = "SELECT transfer_onoff FROM ".WSY_SHOP.".moneybat_transfer_setting WHERE  customer_id=".$customer_id." LIMIT 1";
        $result= _mysql_query($query) or die('Query failed 76: ' . mysql_error());
        while( $row = mysql_fetch_object($result) ){
            $transfer_onoff = $row->transfer_onoff;
        }
        return $transfer_onoff;
    }
     /*
    * 获取转换设置
    * @param : customer_id 商家ID
    * $Author: HJW $
    * 2018-9-22  $
    */
   public function get_exchange_money_integral_setting($data=array()){
        $customer_id = $data['customer_id'];
        $query  = "select block_onoff,min_money,type,proportion,remark from ".WSY_SHOP.".moneybat_blockchain_setting where customer_id='".$customer_id."' limit 1";
        $transformation_setting = $this->db->getRow($query);
        $transformation_setting['old_type'] = $transformation_setting['type'];
        return $transformation_setting;
    }
        /*
    * 区块链发放设置
    * $Author: HJW $
    * @param : customer_id 商家ID 
    * 2018-9-22  $
    */
    public function block_chain_setting($customer_id)
    {
        $query = "select on_off,url,name from ".WSY_SHOP.".block_chain_setting where customer_id='" . $customer_id . "'";
        $res = $this->db->getRow($query);
        return $res;
    }
}

?>