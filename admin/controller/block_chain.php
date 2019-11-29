<?php
header("Content-type: text/html; charset=utf-8");

class control_block_chain extends control_base
{
    var $model;

    function __construct()
    {
        parent::__construct();
        require_once('model/block_chain.php');
        $this->model = new model_block_chain();
        require_once('model/common.php');
        $this->model_common = new model_common();
        require_once('model/sign.class.php');
        $this->sign = new sign();
        
    }

    /*
    版权信息:  秘密信息
    功能描述：区块链积分日志页面
    开 发 者：wuzepeng
    开发日期： 2018-08-03
    @param 
    重要说明：无
     */
    public function integral_log()
    {
    	$customer_id = $this->customer_id;
    	$customer_id_en = $this->customer_id_en;
    	$theme       = $this->model_common->find_theme($customer_id);
		
		$param['customer_id'] = $customer_id;
    	$param['batchcode']   = $_GET['batchcode']?addslashes($_GET['batchcode']):-1;
    	$param['user_name']   = $_GET['user_name']?addslashes($_GET['user_name']):-1;
    	$param['user_id']     = $_GET['user_id']?intval($_GET['user_id']):-1;
    	$param['status']      = $_GET['status']!=NULL?addslashes($_GET['status']):-1;
    	$param['block_origin']= $_GET['block_origin']!=NULL?addslashes($_GET['block_origin']):-1;
		$param['pagenum']     = $_GET['pagenum']?intval($_GET['pagenum']):1;
    	$result               = $this->model->get_integral_log($param);
    	$data                 = $result['data'];
    	$pagenum              = $param['pagenum'];
    	$pageCount            = $result['pageCount'];
		
		
    	include_once('view/block_chain/integral_log.php');
    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分明细页面
    开 发 者：wuzepeng
    开发日期： 2018-08-03
    @param 
    重要说明：无
     */
    public function integral_details()
    {
        $customer_id    = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        $theme          = $this->model_common->find_theme($customer_id);
		
		$param['customer_id'] = $customer_id;
    	$param['batchcode']   = $_GET['batchcode']?addslashes($_GET['batchcode']):-1;
    	$param['user_name']   = $_GET['user_name']?addslashes($_GET['user_name']):-1;
    	$param['user_id']     = $_GET['user_id']?intval($_GET['user_id']):-1;
    	$param['status']      = isset($_GET['status'])&&$_GET['status'] != NULL?addslashes($_GET['status']):-1;;
    	$param['pagenum']     = $_GET['pagenum']?intval($_GET['pagenum']):1;
        
    	$result               = $this->model->get_integral_details($param);
    	$data                 = $result['data'];
    	$pagenum              = $param['pagenum'];
    	$pageCount            = $result['pageCount'];
        $reward_off = $result['reward_off'];//待领取
        $reward_on = $result['reward_on'];//已领取
        $reward_total = $result['reward_total'];//已发放
    	include_once('view/block_chain/integral_details.php');
    }
	/*
    版权信息:  秘密信息
    功能描述：区块链积分发放页面
    开 发 者：zhaipeibin
    开发日期： 2018-08-03
    @param 
    重要说明：无
     */
    public function integral_grant()
    {
		$customer_id        = $this->customer_id;
    	$customer_id_en     = $this->customer_id_en;
    	$theme              = $this->model_common->find_theme($customer_id);
        //设置信息
        $setting            = $this->model->integral_grant($customer_id);

        $id                 = $setting['id'];
        $on_off             = $setting['on_off'];
        $name               = $setting['name'];
        $appid              = $setting['appid'];
        $screet             = $setting['screet'];
        $url                = $setting['url'];
        $valid_day          = $setting['valid_day'];
        $block_chain_type   = $setting['block_chain_type'];
        $block_chain_bfb    = $setting['block_chain_bfb'];
        $block_chain_gene   = $setting['block_chain_gene'];
        
    	include_once('view/block_chain/integral_grant.php');
    }
   
    /*     
    版权信息:  秘密信息
    功能描述：区块链积分发放页面
    开 发 者：zhaipeibin
    开发日期： 2018-08-03
    @param 
    重要说明：无
     */
    public function integral_grant_update()
    {
        //var_dump($customer_id);
        $customer_id    = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        $theme          = $this->model_common->find_theme($customer_id);

        $data['id']                = $_POST['id'];
        $data['on_off']            = addslashes($_POST['on_off']);
        $data['name']              = addslashes($_POST['name']);
        $data['appid']             = addslashes($_POST['appid']);
        $data['appsecret']         = addslashes($_POST['appsecret']);
        $data['url']               = addslashes($_POST['url']);
        $data['valid_day']         = addslashes($_POST['valid_day']);
        $data['block_chain_type']  = addslashes($_POST['block_chain_type']);
        // $data['block_chain_money'] = addslashes($_POST['block_chain_money']);
        $data['block_chain_bfb']   = addslashes($_POST['block_chain_bfb']);
        $data['block_chain_gene']  = addslashes($_POST['block_chain_gene']);

        $integral_grant_update = $this->model->get_integral_grant_update($customer_id,$data);

        $result['errcode'] = 0;
        $result['errmsg']  = 'ok';
        json_out($result);
    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分发放页面
    开 发 者：linrongdie
    开发日期： 2018-08-03
    @param 
    重要说明：无
     */
    public function check()
    {
        $appid = addslashes($_POST['appid']); //账号
        $appsecret = addslashes($_POST['appsecret']); //密钥
        $blockchain_address = $_POST['blockchain_address']; //密钥
     
        $customer_id = $this->customer_id;
        $customer_id = $this->customer_id;
        //调用区块链支付接口并判断该配置是否可用
         //示例参数
        $_POST = array(
            'method'     =>'check_config',
            'version'    =>'1.0',
            'timetamp'   =>time()
        );

        //引用文件
        if (!empty($appid) && !empty($appsecret)) {
            // 分配给开发应用者的id
            $data['app_id']     = $appid;
            //商户密钥
            $data['app_secret'] = $appsecret;
            $type = 'check';
             // 接口名称
            $data['method']     = $_POST['method'];
            // 发送请求的时间 时间戳
            $data['timetamp']   = $_POST['timetamp'];
            // 版本号  1.0
            $data['version']    = $_POST['version'];
            $data['customer_id'] = $this->customer_id;
            $access_token = _block_chain_token($this->customer_id,$data,$type); //获取access_token
        if ($access_token['errcode'] != 0) 
        {
            // json_out($access_token);
        }
        $access_token = $access_token['access_token'];
         //请求接口地址
        $url =$blockchain_address."wsy_blockchain/api/index.php?m=openapi_service&a=".$data['method'];
           //获取签名
        $sign = $this->sign;
        $ras = $sign->create_rsa_sign($data); 
        $data['app_secret'] = $appsecret; 
        $ress = $sign ->post_curl($url,$ras);
        redis_set('block_chain_token'.$customer_id,NULL); //清空token缓存
        $ress = json_decode($ress,true);
        json_out(array('data'=>$ress));
    }

      
    }

    /*
    版权信息:  秘密信息
    功能描述：区块链积分发放页面
    开 发 者：zhaipeibin
    开发日期： 2018-08-03
    @param 
    重要说明：无
    */
    public function check_integral()
    {
        $appid       = addslashes($_POST['appid']); //账号
        $appsecret   = addslashes($_POST['appsecret']); //密钥
        $address     = addslashes($_POST['url']); //域名地址
        $customer_id = $this->customer_id;
        //调用区块链支付接口并判断该配置是否可用
         //示例参数
        $_POST = array(
            'method'     =>'check_config',
            'version'    =>'1.0',
            'timetamp'   =>time()
        );

        //引用文件
        if (!empty($appid) && !empty($appsecret)) {
            // 分配给开发应用者的id
            $date['appid']     = $appid;
            //商户密钥
            $date['appsecret'] = $appsecret;
            //测试
            $type ='check';

            $access_token = _block_chain_token($this->customer_id,$date,$type,$address); //获取access_token

            //清除redis缓存
            if($access_token!=null){
                 redis_set('block_chain_token'.$customer_id,NULL);
            }
            //返回access_token
            json_out($access_token);
        }   

    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分奖励基本设置页面
    开 发 者：wuzepeng
    开发日期： 2018-09-03
    @param 
    重要说明：无
     */
    public function integral_reward_setting()
    {
        $customer_id        = $this->customer_id;
        $customer_id_en     = $this->customer_id_en;
        $theme              = $this->model_common->find_theme($customer_id);

        //设置信息
        $setting            = $this->model->integral_reward_setting($customer_id);

        include_once('view/block_chain/integral_reward_setting.php');
    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分奖励基本设置数据更新
    开 发 者：wuzepeng
    开发日期： 2018-09-03
    @param 
    重要说明：无
     */
    public function integral_reward_setting_update()
    {

        $customer_id          = $this->customer_id;
        $param['on_off']      = addslashes($_POST['on_off']);
        $param['proportion']  = addslashes($_POST['proportion']);
        $param['op']          = addslashes($_POST['op']);
        $param['customer_id'] = $customer_id;
        $result = $this->model->integral_reward_setting_update($param);
        json_out($result);
    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分奖励奖金池页面
    开 发 者：wuzepeng
    开发日期： 2018-09-03
    @param 
    重要说明：无
     */
    public function integral_reward_list()
    {
        $customer_id          = $this->customer_id;
        $customer_id_en       = $this->customer_id_en;
        $theme                = $this->model_common->find_theme($customer_id);
        
        $param['customer_id'] = $customer_id;
        $param['year']        = $_GET['year']?addslashes($_GET['year']):-1;
        $param['month']       = $_GET['month']?addslashes($_GET['month']):-1;
        $param['pagenum']     = $_GET['pagenum']?intval($_GET['pagenum']):1;
        $result               = $this->model->get_integral_reward_list($param);
        $data                 = $result['data'];
        $pagenum              = $param['pagenum'];
        $pageCount            = $result['pageCount'];
        $total_bonus_money    = $result['total_bonus_money'];//奖金池总量
        $user_block_chain     = $result['user_block_chain'];//发行总量
        $total_exchange_jf    = $result['total_exchange_jf'];//已发放总量
        include_once('view/block_chain/integral_reward_list.php');
    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分奖励活动管理
    开 发 者：wuzepeng
    开发日期： 2018-09-03
    @param 
    重要说明：无
     */
    public function integral_reward_activity()
    {
        $customer_id          = $this->customer_id;
        $customer_id_en       = $this->customer_id_en;
        $theme                = $this->model_common->find_theme($customer_id);
        if(empty($_GET['bonus_id']) || empty($customer_id) )
        {
            die('参数缺失，非法操作');
        }
        $param['customer_id'] = $customer_id;
        $param['pagenum']     = $_GET['pagenum']?intval($_GET['pagenum']):1;
        $param['bonus_id']    = $bonus_id = $_GET['bonus_id']?intval($_GET['bonus_id']):-1;
        $param['status']      = $_GET['status']!=NULL?addslashes($_GET['status']):-1;
        $param['product_name']= $_GET['product_name']?addslashes($_GET['product_name']):-1;

        $result               = $this->model->get_integral_reward_activity($param);

        $pagenum              = $param['pagenum'];
        $bonus_id             = $param['bonus_id'];

        $data                 = $result['data'];

        $pageCount            = $result['pageCount'];
        $bonus_data           = $this->model->common_bonus_data($bonus_id,$customer_id);//bonus_id 的月份 流通发行总量，价值 可兑换总量
        $bonus_month          = $bonus_data['year_months'];//bonus_id 的月份
        $month = date('Y-m',strtotime('-1 month',strtotime(date('Y-m').'-01 00:00:01')));//上个月的年月
        $on_off      = true;
        if($bonus_month != $month)//如果两个时间不一样，就代表进入的页面不是当月的奖金池活动，则不能添加产品活动
        {
            $on_off = false;
        }
        include_once('view/block_chain/integral_reward_activity.php');
    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分奖励添加产品兑换活动页面
    开 发 者：wuzepeng
    开发日期： 2018-09-03
    @param 
    重要说明：无
     */
    public function integral_reward_activity_add()
    {
        $customer_id    = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        if(empty($_GET['bonus_id']) || empty($customer_id) )
        {
            die('参数缺失，非法操作');
        }
        $bonus_id       = intval($_GET['bonus_id']);
        $theme          = $this->model_common->find_theme($customer_id);
        $bonus_data     = $this->model->common_bonus_data($bonus_id,$customer_id);//bonus_id 的月份 流通发行总量，价值 可兑换总量

        include_once('view/block_chain/integral_reward_activity_add.php');
    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分奖励添加产品兑换活动操作
    开 发 者：wuzepeng
    开发日期： 2018-09-03
    @param 
    重要说明：无
     */
    public function integral_reward_activity_insert()
    {

        $param['product_name']  = addslashes($_POST['product_name']);
        $param['product_num']   = addslashes($_POST['product_num']);
        $param['product_price'] = addslashes($_POST['product_price']);
        $param['begin_time']    = addslashes($_POST['begin_time']);
        $param['end_time']      = addslashes($_POST['end_time']);
        $param['bonus_id']      = intval($_POST['bonus_id']);
        $param['value_money']   = addslashes($_POST['value_money']);
        $param['customer_id']   = $this->customer_id;

        $result = $this->model->integral_reward_activity_insert($param);
        json_out($result);
    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分奖励删除产品兑换活动
    开 发 者：wuzepeng
    开发日期： 2018-09-03
    @param 
    重要说明：无
     */
    public function integral_reward_del()
    {
        $op = addslashes($_POST['op']);
        switch ($op) {
            case 'del_one':
                $activity_id = addslashes($_POST['activity_id']);
                $result = $this->model->integral_reward_del($activity_id,$op);
                break;
            case 'del_many':
                $activity_id = $_POST['activity_id'];//为数组
                $result = $this->model->integral_reward_del($activity_id,$op);
                break;
            default:
                json_out(array('errcode'=>404,'errmsg'=>'无此操作'));
                break;
        }
        json_out($result);
    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分奖励产品兑换日志
    开 发 者：wuzepeng
    开发日期： 2018-09-03
    @param 
    重要说明：无
     */
    public function integral_reward_exchange_log()
    {
        $customer_id          = $this->customer_id;
        $customer_id_en       = $this->customer_id_en;
        $theme                = $this->model_common->find_theme($customer_id);
        
        $param['customer_id'] = $customer_id;
        $param['pagenum']     = $_GET['pagenum']?intval($_GET['pagenum']):1;
        $param['bonus_id']    = trim($_GET['bonus_id'])?intval($_GET['bonus_id']):-1;
        $param['activity_id'] = trim($_GET['activity_id'])?addslashes($_GET['activity_id']):-1;
        $param['product_name']= trim($_GET['product_name'])?addslashes($_GET['product_name']):-1;
        $param['user_id']     = trim($_GET['user_id'])?addslashes($_GET['user_id']):-1;
        $param['user_name']   = trim($_GET['user_name'])?addslashes($_GET['user_name']):-1;

        $result               = $this->model->integral_reward_exchange_log($param);
        $data                 = $result['data'];
        $pagenum              = $param['pagenum'];
        $pageCount            = $result['pageCount'];
        $bonus_id             = $param['bonus_id'];
        $activity_id          = $param['activity_id'];
        
        include_once('view/block_chain/integral_reward_exchange_log.php');
    }
    /*
    版权信息:  秘密信息
    功能描述：区块链积分奖励活动管理（全部）
    开 发 者：wuzepeng
    开发日期： 2018-09-03
    @param 
    重要说明：无
     */
    public function integral_reward_all_activity()
    {
        $customer_id          = $this->customer_id;
        $customer_id_en       = $this->customer_id_en;
        $theme                = $this->model_common->find_theme($customer_id);
        $param['customer_id'] = $customer_id;
        $param['pagenum']     = $_GET['pagenum']?intval($_GET['pagenum']):1;
        $param['status']      = $_GET['status']!=NULL?addslashes($_GET['status']):-1;
        $param['product_name']= $_GET['product_name']?addslashes($_GET['product_name']):-1;

        $result               = $this->model->get_integral_reward_all_activity($param);

        $pagenum              = $param['pagenum'];

        $data                 = $result['data'];
        $pageCount            = $result['pageCount'];
        
        include_once('view/block_chain/integral_reward_all_activity.php');
    }
    /*
    * 区块链APP登陆注册接口
    * $Author: hjw$
    * $2018-10-8  $
    * 参数：
    */
    public function app_login_api($customer_id){
        $data['customer_id']    = $customer_id; //商家ID
        $http_url = $this->model->http_url($customer_id);//获取前缀地址
        //需要参数
        $access_token = _block_chain_token($customer_id); //获取access_token
        if ($access_token['errcode'] != 0) 
        {
            json_out($access_token);
        }
        $access_token = $access_token['access_token'];
        $return_url = Protocol . $_SERVER["HTTP_HOST"] . "/mshop/admin/index.php?m=block_chain&a=app_login_return&customer_id=".$customer_id;
         //请求接口地址
        $url = $http_url."wsy_blockchain/api/index.php?m=openapi_user&a=authorization&access_token=".urlencode($access_token)."&return_url=".urlencode($return_url);
        header("location:".$url);

    }
    /*
    * 区块链APP登陆注册接口
    * $Author: hjw$
    * $2018-10-8  $
    * 参数：
    */
    public function app_login_return(){
        var_dump($_GET);
        $data['customer_id'] = $customer_id = $this->customer_id; //商家ID
        $data['openid']      = addslashes($_GET['openid']);
        $data['mobile']      = addslashes($_GET['mobile']);
        $data['nickname']    = addslashes($_GET['nickname']);
        $data['head_img']    = $_GET['head_img'] == -1 ? '' : addslashes($_GET['head_img']);
        $res_bind = $this->model->is_bind_shop($data);//是否绑定商城
        if(isset($res_bind['user_id'])){
            //已绑定商城，登陆
            $user_id = $res_bind['user_id'];
        }else{
            $user_id = $this->model->create_account($data);//是否绑定商城
        }
        //app消息互通：登陆成功吧需要退出登陆的用户进行软删除 zhou 2017-7-10
        $up_query = "update h5_loginout set isvalid=0 where user_id=".$user_id." and type=1";
        //echo $up_query;exit;
        _mysql_query($up_query) or die("sql h5_loginout faild:".mysql_error());

        $opid_query="select weixin_fromuser,weixin_headimgurl from ".WSY_USER.".weixin_users where isvalid=true and id=".$user_id." limit 0,1";
        //echo $opid_query;exit;
        $opid_result=_mysql_query($opid_query) or die ("opid_query faild" .mysql_error());
        while($row=mysql_fetch_object($opid_result)){
            $weixin_fromuser=$row->weixin_fromuser;
            $weixin_headimgurl=$row->weixin_headimgurl;
        }
        $_SESSION["customer_id"] = $customer_id;   
        $_SESSION["user_id_".$customer_id]      =$user_id;
        $_SESSION["myfromuser_".$customer_id]   =$weixin_fromuser;
        $_SESSION["fromuser_".$customer_id]     =$weixin_fromuser;
        $_SESSION["is_bind_".$customer_id]      =1;//已经注册
        setcookie("login_headimgurl",$weixin_headimgurl, time()+604800,'/');//设置用户头像COOKIE
        $url = $_SESSION["nurl_".$customer_id];
        //header("location:".$url);
        var_dump($url);
    }

}//类结束
