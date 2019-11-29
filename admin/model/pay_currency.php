<?php

class model_pay_currency{

    public $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    /**
     * 消费送购物币优化
     * @param  int    $customer_id  商家编号id
     * @param  var    $industry     类型
     * @param  var    $extend       大礼包编号/o2o行业编号
     * @param  int    $user_id      顾客id
     * @param  int    $exp_user_id  上级id
     * @param  int    $return_id    返佣对象
     */
    function get_rebate_user($customer_id,$industry,$user_id,$extend="",$exp_user_id,$batchcode){
    	$result=array();
    	$result['errcode']=0;
    	$result['errmsg'] = "成功";
    	$return_id        = -1;
    	//检测类型
    	switch ($industry) {
    		case 'shop':
    			$select_switch = $this->select_shop_switch($customer_id);//查询开关、送购物币对象：1.消费者 2.推广员
    			break;

    		case 'packages':
    			$select_rebate_user = $this->select_packages_switch($customer_id,$extend);//查询开关、送购物币对象：1.消费者 2.推广员
    			$select_switch['is_rebate_open'] = 1 ;
    			$select_switch['rebate_user']    = $select_rebate_user ;
    			// $result['23333']=$select_switch;
    			break;

    		case 'o2o':
            switch ($extend) {
                case 2:
                    $extend = 1;
                    break;
                case 3:
                    $extend = 1;
                    break;
                case 22:
                    $extend = 20;
                    break;
                case 21:
                    $extend = 20;
                    break;
                case 23:
                    $extend = 20;
                    break;
                case 31:
                    $extend = 30;
                    break;
                case 61:
                    $extend = 60;
                    break;
                case 62:
                    $extend = 60;
                    break;
                case 101:
                    $extend = 100;
                    break;
            }
				$select_o2o = $this->select_o2o_system_switch($customer_id,$extend);//查询开关、送购物币对象：1.消费者 2.推广员
				$select_switch['is_rebate_open']=1;
				$select_switch['rebate_user']   = $select_o2o;
    			break;

    	}
    			if($select_switch['is_rebate_open']==1){
    				if ($select_switch['rebate_user']==1) {
    					$return_id=$user_id;
    					$result['errmsg'] = "成功，返购物币模式为购买者";
    				}else if($select_switch['rebate_user']==2){//判断自己是否为推广员，是即为返佣对象，否则判断上级是否为推广员，若两者都不是，则无返佣对象
    					//检测自己是否推广员
                        if($industry == 'packages')
                        {
                            $sql="SELECT is_promoter FROM ".WSY_SHOP.".package_order_t where batchcode='".$batchcode."' and customer_id='".$customer_id."' and isvalid=true";
                            $is_promoter = $this->db->getOne($sql);
                            if ($is_promoter) {
                                $return_id = $user_id;
                                $result['errmsg'] = "成功，返购物币模式为推广员，购买者是推广员，为分俑者";
                            }else{
                            //检测上级是否推广员
                                if ($exp_user_id > 0) {
                                    $sql2 = "SELECT status FROM ".WSY_PUB.".promoters where user_id='".$exp_user_id."' and customer_id='".$customer_id."' and isvalid=true";
                                    $exp_user_is_promoters=$this->db->getOne($sql2);
                                    if ($exp_user_is_promoters==1) {

                                        $return_id = $exp_user_id;
                                        $result['errmsg'] = "成功，返购物币模式为推广员，购买者不是推广员，上级为推广员，为分俑者";
                                    }else{
                                        $return_id = -1;
                                        $result['errcode']= 40003;
                                        $result['errmsg'] = "上级即自身不满足推广员身份";
                                    }
                                }else{
                                    $return_id = -1;
                                    $result['errcode']= 40003;
                                    $result['errmsg'] = "本身不满足推广员身份且无上级";
                                }
                            }
                        }else
                        {
        					$sql="SELECT status FROM ".WSY_PUB.".promoters where user_id='".$user_id."' and customer_id='".$customer_id."' and isvalid=true";
        					$user_is_promoters = $this->db->getOne($sql);
        					if ($user_is_promoters==1) {
        						$return_id = $user_id;
        						$result['errmsg'] = "成功，返购物币模式为推广员，购买者是推广员，为分俑者";
        					}else{
        					//检测上级是否推广员
        						if ($exp_user_id > 0) {
    	    						$sql2 = "SELECT status FROM ".WSY_PUB.".promoters where user_id='".$exp_user_id."' and customer_id='".$customer_id."' and isvalid=true";
    	    						$exp_user_is_promoters=$this->db->getOne($sql2);
    	    						if ($exp_user_is_promoters==1) {
    	    							$return_id = $exp_user_id;
    	    							$result['errmsg'] = "成功，返购物币模式为推广员，购买者不是推广员，上级为推广员，为分俑者";
    	    						}else{
    	    							$return_id = -1;
    	    							$result['errcode']= 40003;
    	    							$result['errmsg'] = "上级即自身不满足推广员身份";
    	    						}
        						}else{
        							$return_id = -1;
        						    $result['errcode']= 40003;
    	    						$result['errmsg'] = "本身不满足推广员身份且无上级";
        						}
        					}
                        }
    				}
    			}else{
	    			$result['errcode']= 40233;
	    			$result['errmsg'] = "未开启消费返购物币开关";
    			}
    		
    	$result['return_id']=$return_id;
    	return $result;
    }

    /**
     * 查询商城分佣开关
     * @param  int    $customer_id  商家编号id
     */
    function select_shop_switch($customer_id){
    	$sql = "SELECT is_rebate_open,rebate_user FROM ".WSY_SHOP.".weixin_commonshop_currency where isvalid=true and customer_id='".$customer_id."'";
    	$result = $this->db->getRow($sql);
    	return $result;
    }

    /**
     * 查询商城大礼包返购物币开关
     * @param  int    $customer_id  商家编号id
     * @param  int    $packages_id  大礼包编号
     */
    function select_packages_switch($customer_id,$packages_id){
    	$sql = "SELECT rebate_user FROM ".WSY_SHOP.".package_list_t where isvalid=true and id='".$packages_id."' and customer_id='".$customer_id."'";
    	$result = $this->db->getOne($sql);
    	return $result;
    }

    /**
     * 查询城市商圈，线下商城返购物币开关
     * @param  int    $customer_id  商家编号id
     * @param  int    $extend       o2o商家编号
     */
    function select_o2o_system_switch($customer_id,$extend){
    	$sql = "SELECT rebate_user FROM ".WSY_O2O.".weixin_cityarea_entend where isvalid=true and cityType=".$extend." and customer_id=".$customer_id;
    	$result = $this->db->getOne($sql);
        _file_put_contents("log/utlilty_returnCurrency_ceshi" . $this->today . ".txt", "sql=======".var_export($sql,true)."\r\n",FILE_APPEND);
    	return $result;
    }

    /***
 * 功能描述：创建群发任务记录  fan_group_pay_currency_task
 * @param array array('currency'=>1,'group_id'=>1,'current_user_id'=>1,'user_id'=>1,'customer_id'=>1,'customer_id_en'=>1,'balance'=>1)
 * @return bool
 * @author: taojin $
 * 2018-1-3  $
 */
    public function insert_group_batch_sending_task($data){
        $return = false;
        $create_time = date('Y-m-d H:i:s',$data['task_id']);
        //添加购物币日志
//        $id = $this->db->autoExecute(WSY_USER.'.fan_group_pay_currency_task', array('customer_id' => $data['customer_id'],'group_id' => $data['group_id'],'task_id' => $data['task_id'],'total_pay_amount' => ($data['user_num']*$data['currency']),'total_pay_people' => $data['user_num'],'status' => 0,'createtime'=>$create_time,'isvalid' => true), 'insert') ;
        $sql = "INSERT INTO ".WSY_USER.".fan_group_pay_currency_task (customer_id,group_id,task_id,total_pay_amount,total_pay_people,status,create_time,isvalid) values('{$data['customer_id']}','{$data['group_id']}','{$data['task_id']}','".$data['user_num']*$data['currency']."','{$data['user_num']}',0,'{$create_time}',true)";
        $res = $this->db->query($sql);
        if($res) $return = true;
        return $return;
    }

    /***
     * 功能描述：更新群发任务记录  fan_group_pay_currency_task
     * @param array array('currency'=>1,'group_id'=>1,'current_user_id'=>1,'user_id'=>1,'customer_id'=>1,'customer_id_en'=>1,'balance'=>1)
     * @return bool
     * @author: taojin $
     * 2018-1-3  $
     */
    public function update_group_batch_sending_task($data){
        $true_send = $data['n'] * $data['currency'];
        $final_time = date('Y-m-d H:i:s',time());
        $sql = "UPDATE ".WSY_USER.".fan_group_pay_currency_task SET status = '{$data['status']}' , final_paying_people = '{$data['n']}' , final_paying_amout = '{$true_send}', final_time = '{$final_time}' where customer_id = '{$data['customer_id']}' AND task_id = '{$data['task_id']}'";
        $res = $this->db->query($sql);
        return $res;
    }

    /***
     * 功能描述：查看群发任务记录  fan_group_pay_currency_task
     * @param array array('currency'=>1,'group_id'=>1,'current_user_id'=>1,'user_id'=>1,'customer_id'=>1,'customer_id_en'=>1,'balance'=>1)
     * @return bool
     * @author: taojin $
     * 2018-1-3  $
     */
    public function get_group_batch_sending_task($data){
        $sql = "SELECT count(id) FROM ".WSY_USER.".fan_group_pay_currency_task where customer_id = '{$data['customer_id']}' AND task_id = '{$data['task_id']}'";
        $res = $this->db->getOne($sql);
        return $res;
    }


    /***
     * 功能描述：增加购物币
     * @param array array('currency'=>1,'group_id'=>1,'current_user_id'=>1,'user_id'=>1,'customer_id'=>1,'customer_id_en'=>1,'balance'=>1)
     * @return bool
     * @author: taojin $
     * 2018-1-3  $
     */
    public function add_user_pay_currency($data){
        $sql = "update ".WSY_SHOP.".weixin_commonshop_user_currency set currency = currency + {$data['currency']}  where customer_id='{$data['customer_id']}'  and user_id='{$data['user_id']}'";
        $res = $this->db->query($sql);
        return $res;
    }

    /***
     * 功能描述：新增购物币日志
     * @param array array('currency'=>1,'group_id'=>1,'current_user_id'=>1,'user_id'=>1,'customer_id'=>1,'customer_id_en'=>1,'balance'=>1)
     * @return bool
     * @author: taojin $
     * 2018-1-3  $
     */
    public function insert_user_pay_currency_log($data){
        $return = false;
        $batchcode = $this->make_transform_order_batchcode($data['user_id']);    //生成订单号
        $create_time = date('Y-m-d H:i:s',time());
        //添加购物币日志
        $id = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_currency_log', array('customer_id' => $data['customer_id'],'user_id' => $data['user_id'],'cost_currency' => $data['currency'],'after_currency' => ($data['balence']+$data['currency']),'batchcode' => $batchcode,'status' => 1,'type' => 1,'class'=>23,'createtime'=>$create_time,'isvalid' => true,'remark'=>"用户组群发{$data['currency_name']}"), 'insert') ;
        if($id>0) $return = true;
        return $return;
    }

    /***
     * 功能描述：查询用户购物币
     * @param array array('currency'=>1,'group_id'=>1,'current_user_id'=>1,'user_id'=>1,'customer_id'=>1,'customer_id_en'=>1,'balance'=>1)
     * @return array array('multiple_type'=>1,'multiple_diy'=>1,'minimum'=>1,'conversion_ratio'=>1,'type'=>1) 零钱转换配置详情
     * @author: taojin $
     * 2018-1-3  $
     */
    public function get_user_currency($user_id){
        $sql = "SELECT currency FROM ".WSY_SHOP.".weixin_commonshop_user_currency WHERE isvalid = 1 and user_id='{$user_id}' FOR UPDATE ";
        $result = $this->db->getOne($sql);
        return $result;
    }

    /***
     * 功能描述：查询自定义购物币名称
     * @param string customer_id
     * @return string custom  购物币名称
     * @author: taojin $
     * 2018-1-3  $
     */
    public function get_diy_curreny_name($customer_id){
        $sql = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid=true AND customer_id= '{$customer_id}' LIMIT 1";
        $result = $this->db->getOne($sql);
        $res = $result?$result:'购物币';
        return $res;
    }

    /***
     * 功能描述：生成订单号
     * @param array $user_id  用户id
     * @return string 订单号
     * @author: taojin $
     * 2018-1-3  $
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

}
?>