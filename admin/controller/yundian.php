<?php


class control_yundian extends control_base 
{
    var $model;
    var $model_common;
    function __construct() 
    {
        parent::__construct();
        require_once('model/yundian.php');
        $this->model = new model_yundian();
        require_once('model/common.php');
        $this->model_common = new model_common();
        
        parent::check_login();
        $data['data']=file_get_contents('php://input', true);
        //$data = $_REQUEST['data'];
        $this->parmdata  = json_decode($data['data'],true);
        $customer_id = $this->customer_id;
        require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/proxy_info.php');
//        var_dump($theme);

        
    }
    

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——基本设置列表
    开 发 者：zhangqiusong
    开发日期： 2018-04-04
    重要说明：无
     */
    public function shop_setting_list(){
        $customer_id = $this->customer_id;
        //查询主题
        $theme     = $this->model_common->find_theme($customer_id);
        $res       = $this->model->setting_list_select($customer_id);
        //查询是否存在云店身份的店主
        $is_keeper = $this->model->keeper_select($customer_id);
        // var_dump($res);
        if ($res['errcode'] ==0) {
            $yundian_onoff          = $res['res1']['yundian_onoff'];
            $yundian_apply_onoff    = $res['res1']['yundian_apply_onoff'];
            $yundian_choucheng      = $res['res1']['yundian_choucheng'];
            $receipt_onoff          = $res['res1']['receipt_onoff'];
            $receipt_time           = $res['res1']['receipt_time'];
            $invalid_onoff          = $res['res1']['invalid_onoff'];
            $invalid_time           = $res['res1']['invalid_time'];
            $clearing_onoff         = $res['res1']['clearing_onoff'];
            $playmoney_onoff        = $res['res1']['playmoney_onoff'];
            $complete_onoff         = $res['res1']['complete_onoff'];
            $shop_valid_time        = $res['res1']['shop_valid_time'];
            $shop_notice_time       = $res['res1']['shop_notice_time'];
            $yundian_reward         = $res['res1']['yundian_reward'];
            $remark                 = $res['res1']['remark'];
            $res                    = $res['res2'];
            // var_dump($res);
        }else{
            $data_ini['customer_id']            = $customer_id;//商家id
            $data_ini['yundian_onoff']          = false;//云店开关
            $data_ini['yundian_apply_onoff']    = false;//云店申请开关
            $data_ini['yundian_choucheng']      = '0';//自营产品抽成
            $data_ini['receipt_onoff']          = false;//默认收货时间开关
            $data_ini['receipt_time']           = '7';//默认收货时间
            $data_ini['invalid_onoff']          = false;//订单失效开关
            $data_ini['invalid_time']           = '30';//订单失效时间
            $data_ini['clearing_onoff']         = false;//自营产品订单收货自动结算开关
            $data_ini['playmoney_onoff']        = true;//售后平台打款开关
            $data_ini['complete_onoff']         = false;//完成退款之后自动完成订单
            $data_ini['shop_valid_time']        = '365';//默认店主有效天数
            $data_ini['shop_notice_time']       = '15';//提前通知天数
            $data_ini['yundian_reward']         = '0';//云店奖励比例
            $data_ini['remark']                 = '云店店主申请协议';//云店店主申请协议
            $data_ini['createtime']             = date('Y-m-d H:i:s');//创建时间
            $data_ini2['is_identity']           = true;//身份开关
            $data_ini2['customer_id']           = $customer_id;//商家id
            $data_ini2['name']                  = '云店店主';//身份名称
            $data_ini2['reward']                = '0';//比例
            $data_ini2['apply_money']           = '0';//申请条件金额
            $data_ini2['tequan']                = '1_1_1_1_1_1_1_1_1_1_1_1_1';//各特权是否开启(店铺推广_个性化店标_收益实时查询_店铺自营订单管理_个性化产品管理)
            $data_ini2['remark']                = '特权描述';//特权描述
            $data_ini2['createtime']            = date('Y-m-d H:i:s');//创建时间
            $init = $this->model->initialize_setting($data_ini,$data_ini2);
            echo '<script type="text/javascript">
                    location.href="/mshop/admin/index.php?m=yundian&a=shop_setting_list";
                    </script>';
            exit;
        }

        //获取所有奖励比例
        $reward_data = $this->model->reward_selcet($customer_id);
        //计算出推广奖励比例
        $all = $reward_data['team']+$reward_data['shareholder']+$reward_data['globalbonus']+$reward_data['investmen']+$reward_data['block_chain_reward'];
        //云店奖励开关关闭的话不算入计算
        if ($yundian_onoff == 0) {
            $gts = $all;
        }else{
            $gts = $all+$yundian_reward;
        }
        //$tg_reward = 1-($gts.'');
        $tg_reward =  bcsub(1,$gts,20);
        $tg_reward = rtrim(rtrim($tg_reward, '0'), '.'); 
        include('view/yundian/shop_setting_list.html');

    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——基本设置保存
    开 发 者：zhangqiusong
    开发日期： 2018-04-04
    重要说明：无
     */
    public function shop_setting_sava(){
        $customer_id = $this->customer_id;
        $data['customer_id']            = $customer_id;
        $data['isvalid']                = true;
        $data['yundian_onoff']          = $_REQUEST['yundian_onoff']?$_REQUEST['yundian_onoff']:0;  //云店开关
        if ($data['yundian_onoff'] == 0) 
        {
            $data['yundian_apply_onoff'] = 0;
        }
        else
        {   
            //云店申请开关        
            $data['yundian_apply_onoff'] = $_REQUEST['yundian_apply_onoff']?$_REQUEST['yundian_apply_onoff']:0; 
        }

        $data['yundian_choucheng']      = $_REQUEST['yundian_choucheng']?$_REQUEST['yundian_choucheng']:'0';  //自营产品总抽成
        $data['receipt_onoff']          = $_REQUEST['receipt_onoff']?$_REQUEST['receipt_onoff']:0;  //默认收货时间开关
        $data['receipt_time']           = $_REQUEST['receipt_time']?$_REQUEST['receipt_time']:'7';  //默认收货时间
        $data['invalid_onoff']          = $_REQUEST['invalid_onoff']?$_REQUEST['invalid_onoff']:0;  //订单失效开关
        $data['invalid_time']           = $_REQUEST['invalid_time']?$_REQUEST['invalid_time']:'30';  //订单失效时间
        $data['clearing_onoff']         = $_REQUEST['clearing_onoff']?$_REQUEST['clearing_onoff']:0;  //自营产品订单收货自动结算开关
        $data['playmoney_onoff']        = $_REQUEST['playmoney_onoff']?$_REQUEST['playmoney_onoff']:0;  //售后平台打款开关
        $data['complete_onoff']         = $_REQUEST['complete_onoff']?$_REQUEST['complete_onoff']:0;  //完成退款之后自动完成订单
        $data['shop_valid_time']        = $_REQUEST['shop_valid_time']?$_REQUEST['shop_valid_time']:'365';  //初次有效时长
        $data['shop_notice_time']       = $_REQUEST['shop_notice_time']?$_REQUEST['shop_notice_time']:'15';  //提前通知天数
        $data['yundian_reward']         = $_REQUEST['yundian_reward']?$_REQUEST['yundian_reward']:'0';  //云店奖励比例
        $data['remark']                 = addslashes($_REQUEST['remark']);  //云店店主申请协议
        $data['createtime']             = date("Y-m-d H:i:s",time());
        $result                         = $_REQUEST['result'];
        if($data['receipt_time'] < 0 || $data['receipt_time'] > 15){    //默认最大收货时间为15天
            $data['receipt_time'] = 15;
        }
        if($data['invalid_time'] < 0 || $data['invalid_time'] > 30){    //默认订单最大失效时间为30分钟
            $data['invalid_time'] = 30;
        }

         $log_remark = $this->model->compare_yundian_setting($data,$result,$customer_id);

        $res = $this->model->sava_setting($data);
        foreach ($result as $k => $v) 
        {
            $data2['id']           = $result[$k]['id'];
            $data2['is_identity']  = $result[$k]['is_identity'];
            $data2['customer_id']  = $customer_id;
            if (!empty(trim($result[$k]['name']," "))) {
                $data2['name']     = $result[$k]['name'];
            }else{
                $data2['name']     = "云店店主";
            }
            $data2['reward']       = $result[$k]['reward']?$result[$k]['reward']:0;
            $data2['apply_money']  = $result[$k]['apply_money'];
            $data2['createtime']   = date("Y-m-d H:i:s",time());

            // $arr = $result[$k]['tequan'];
            // $a   = $arr[1]?$arr[1]:0;
            // $b   = $arr[2]?$arr[2]:0;
            // $c   = $arr[3]?$arr[3]:0;
            // $d   = $arr[4]?$arr[4]:0;
            // $e   = $arr[5]?$arr[5]:0;
            // $data2['tequan'] = $a."_".$b."_".$c."_".$d."_".$e; //ZOUJUNJIE - CRM-17147 
            $res2 = $this->model->sava_tequan($data2);
        }

        //插入日志
        $log_data['customer_id'] = $customer_id;
        $log_data['remark']      = $log_remark['remark'];
        $log_data['title']      = $log_remark['title'];
        $log = $this->model->save_admin_yundian_log($log_data);

    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主特权描述编辑
    开 发 者：zhangqiusong
    开发日期： 2018-04-04
    重要说明：无
     */
    public function identity_edit()
    {
        $data['customer_id'] = $this->customer_id;
        $theme               = $this->model_common->find_theme($data['customer_id']);
        $data['id']          = $_REQUEST['id'];
        $data['type']        = $_REQUEST['type'];
        if ($data['type']    == 'edit') 
        {
            $data['remark']  =  addslashes($_REQUEST['remark']);
        }
        $res =  $this->model->identity_edit($data);

        include('view/yundian/identity_edit.html');
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——删除店主
    开 发 者：zhangqiusong
    开发日期： 2018-04-09
    重要说明：无
     */
    public function identity_del(){
        $data = $_POST;
        extract($data);
        $data['customer_id']  = $this->customer_id;
        if ($is_ajax == 1) 
        {
            $result = $this->model->identity_del($data);
            json_out($result);
        }
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——后台添加店主特权身份
    开 发 者：zhangqiusong
    开发日期： 2018-04-09
    重要说明：$data里的数据说明： customer_id //商家ID   is_ajax //是否ajax请求
     */
    public function identity_add(){
        $data = $_POST;
        extract($data);
        $data['customer_id']  = $this->customer_id;
        if ($is_ajax == 1) {
            $result  = $this->model->identity_add($data);
            json_out($result);
        }
    }

   /*
    * 店主审核列表
    * $Author: hjw$
    * $2018-04-04  $
    * 参数： array('search_key'=>['user_id','user_name','identity_id','status','begin_time','end_time'],'page','page_size','is_ajax')
    *        is_ajax 1 ajax请求 0 页面请求,search_key 搜索条件 ，page 当前页数 ，page_size 每页条数
    *
    */
    public function shopkeeper_review_list()
    {
        $post = $_POST;
        extract($post);
        $customer_id_en      = $this->customer_id_en;
        $customer_id         = $this->customer_id;
        $data['customer_id'] = $customer_id;
        $data['page']        = $page;
        $data['page_size']   = $page_size;
        $theme               = $this->model_common->find_theme($data['customer_id']);
        //判断是否为AJAX请求
        if($is_ajax != 1)
        {
           $identity_arr = $this->model->get_identity($data['customer_id']);
           include("view/yundian/shopkeeper_review_list.html");
        }
        else
        {
            //判断数据是否安全
            if(empty($data['customer_id']))
            {
                json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
            }
            if(empty($search_key))
            {
                json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
            }
            if(empty($data['page']) || $data['page'] < 1)
            {
                json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
            }
            if(empty($data['page_size']) || $data['page_size'] < 1){
                $data['page_size'] = 20;//每页数量
            }
            $data = array_merge($data,$search_key);
            $result = $this->model->shopkeeper_review_list($data);
            json_out($result);
        }
    }

/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表
    开 发 者：HMJ-V384
    开发日期： 2018-04-04
    重要说明：无
     */
    function yundian_shopkeeper_list(){             
        $customer_id = $this->customer_id;
        $theme       = $this->model_common->find_theme($customer_id);

        $param['customer_id']     = $this->customer_id;
        $param['user_id']         = $_REQUEST['user_id']?$_REQUEST['user_id']:-1;        
        $param['tequan_id']       = $_REQUEST['tequan_id']?$_REQUEST['tequan_id']:-1;
        $param['store_name']      = $_REQUEST['store_name']?$_REQUEST['store_name']:'';
        $param['verify_time']     = $_REQUEST['verify_time']?$_REQUEST['verify_time']:-1;
        $param['expire_time']     = $_REQUEST['expire_time']?$_REQUEST['expire_time']:-1;
        $param['name']            = $_REQUEST['name']?$_REQUEST['name']:'';       
        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页

        $res       = $this->model->get_yundian_shopkeeper_list($param);//获取店主列表
        $res2      = $this->model->get_yundian_identity($this->customer_id);//获取所有特权，搜索用
        $data      = $res['shopkeeper_arr'];
        $pageCount = $res['pageCount'];        
        include("view/yundian/yundian_shopkeeper_list.php");
    }

    /*
    * 云店后台操作日志
    * $Author: cjj
    * $2018-04-08  $
    * $data=['customer_id','remark'];  //需要传入的数据,配置参数json的从数据库中查找    customer_id //商家ID   remark //备注
    */
    function admin_yundian_log($data = array()){
        $param['customer_id']     = $this->customer_id;
//        $param['remark']   = '修改配置';
        $res = $this->model->save_admin_yundian_log($param);

        if(!empty($res)){
            json_out(array('errcode' => 0,'errmsg'=>'操作成功！'));
        }else{
            json_out(array('errcode' => 400,'errmsg'=>'操作失败，请重新操作！'));
        }
    }
/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——删除店主
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
    返回：  $return['errcode'] = 1/0 成功/失败
            $return['errmsg'] = "删除成功！/删除失败";
     */
    function del_yundian_shopkeepers(){
        $param['customer_id'] = $this->customer_id;
        $param['user_id']     = $_POST['user_id']?$_POST['user_id']:-1;
        $res = $this->model->del_yundian_shopkeeper($param);        
        json_out($res);
    }
/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——编辑店主跳转
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
     */
    public function edit_yundian_shopkeepers(){
        $customer_id         = $this->customer_id;
        $temp['customer_id'] = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);
        $temp['user_id']     = $_REQUEST['user_id']?$_REQUEST['user_id']:"";
        $is_ajax             = $_REQUEST['is_ajax']?$_REQUEST['is_ajax']:"";
        if(!$is_ajax) {
            include("view/yundian/edit_yundian_shopkeeper.php");
        } else {
            if(!empty($temp['user_id'])) {
                $data  = $this->model->get_yundian_shopkeeper($temp);
                if(!empty($data['keeper_msg']) && !empty($data['yundian_identity'])) {
                    json_out(array('errcode' => 1,'errmsg'=>'获取数据成功！','data'=>$data));
                } else if(empty($data['keeper_msg'])){
                    json_out(array('errcode' => 0,'errmsg'=>'获取店主数据失败'));
                } else {
                    json_out(array('errcode' => 0,'errmsg'=>'权限获取失败'));
                }
            }            
        }
    }
/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——编辑店主信息
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
    返回：  $return['errcode'] = 1/0 成功/失败
            $return['errmsg'] = "编辑成功！/编辑失败";
     */
    public function save_shopkeeper_datas(){
        $user_id                    = $_REQUEST['user_id']?$_REQUEST['user_id']:"";
        $data['tequan_id']          = $data2['tequan_id'] = $_REQUEST['tequan_id']?$_REQUEST['tequan_id']:"";
        $data['isvalid']            = true;
        $data['expire_time']        = $_REQUEST['expire_time']?$_REQUEST['expire_time']:"";
        $data['store_name']         = $_REQUEST['store_name']?addslashes($_REQUEST['store_name']):"";
        $data['profit_shop']        = $_REQUEST['profit_shop']?$_REQUEST['profit_shop']:"";
        $data['self_reware']        = $_REQUEST['self_reware']?$_REQUEST['self_reware']:"";

        if(strtotime($data['expire_time']) > strtotime(date("Y-m-d H:i:s"))){   //当修改的过期时间大于现在当前时间，则将首次过期提醒 和 即将过期提醒改为0
            $data['first_warn'] = 0;
            $data['expire_warn'] = 0;
            $data['send_first_warn'] = 0;
            $data['send_expire_warn'] = 0;
        }

        $obj['customer_id']         = $this->customer_id;
        $obj['user_id']             = $user_id;
        $res = $this->model->save_shopkeeper_data($data,$data2,$obj);

        json_out($res);
    }

/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主商品列表
    开 发 者：zqs
    开发日期： 2018-04-09
    重要说明：无
     */
    public function shopkeeper_order_list(){
        $customer_id = $this->customer_id;
        //查询主题
        $theme       = $this->model_common->find_theme($customer_id);
        $data['customer_id']    = $customer_id;
        $data['realname']       = $_GET['realname']?$_GET['realname']:'';      //店主昵称
        $data['user_id']        = $_GET['user_id']?$_GET['user_id']:'';        //店主id
        $data['store_name']     = $_GET['store_name']?$_GET['store_name']:'';  //店主店铺名称
        $data['name']           = $_GET['name']?$_GET['name']:'';              //商品名称
        $data['type']           = $_GET['type']?$_GET['type']:'1';             //商品状态 1.全部商品 2.上架中 3.下架
        $data['pageNum']        = $_GET['pagenum']?$_GET['pagenum']:1;         //当前页
        $data['page_size']      = 20;                                          //每页显示数      
        $result = $this->model->shopkeeper_order_select($data);

        //获取所有商品数量，上架商品数量，下架商品数量
        $result2 = $this->model->get_shopkeeper_order_num($data);
        $pageNum                = $data['pageNum'];                        //当前页
        if ($data['type'] == 1) {
            $Count                  = $result2['all'];           //数据总条数
            $pageCount              = ceil($Count/20);           //数据总页数
        }else if($data['type'] == 2){
            $Count                  = $result2['on'];           //数据总条数
            $pageCount              = ceil($Count/20);           //数据总页数
        }else if($data['type'] == 3){
            $Count                  = $result2['out'];           //数据总条数
            $pageCount              = ceil($Count/20);           //数据总页数
        }
        include('view/yundian/shopkeeper_order_list.php');
    }

/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主商品上下架及残忍删除店主商品
    开 发 者：zqs
    开发日期： 2018-04-09
    重要说明：无
     */
     public function change_isout_get(){
        $data = $_POST;
        $data['customer_id'] = $this->customer_id;
        $res = $this->model->change_isout_get($data);
        json_out($res);
     }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主订单
    开 发 者：taojin
    开发日期： 2018-04-08
    重要说明：参数type: 0平台订单 1自营订单
    方法  yundian_order_list  返回errcode(int) errmsg(string) data(array(0=>$data,1=>$count_data))
     */
    public function yundian_order_list(){

            $customer_id            = $this->customer_id;
            $customer_id_en         = $this->customer_id_en;
            $theme  = $this->model_common->find_theme($customer_id);
            $data['status']         = $_GET['status']?$_GET['status']:0;            //订单状态-搜索条件
            $data['batchcode']      = $_GET['batchcode']?$_GET['batchcode']:0;      //订单号-搜索条件
            $data['type']           = $_GET['type']?$_GET['type']:0;                //平台订单-0  自营订单-1
            $data['customer_id']    = $this->customer_id;                           //商户号
            $data['yundian_id']     = $_GET['yundian_id']?$_GET['yundian_id']:'-1'; //云店id
            $data['yun_user_id']        = intval($_GET['yun_user_id']?$_GET['yun_user_id']:false);      //店主id
            if($data['type'] == 1){
                $data['user_id']        = intval($_GET['user_id']?$_GET['user_id']:false);      //店主id
                $data['name']           = $_GET['name']?$_GET['name']:'';               //店主昵称
            }
            $data['pageNum']        = $_GET['pagenum']?$_GET['pagenum']:1;          //当前页
            $data['page_size']      = 20;                                           //每页显示数

            //获取订单列表
            $order_list             = $this->model->yundian_order_list($data);
            
            $pageNum                = $data['pageNum'];                             //当前页
            $Count                  = $order_list['data'][1];                       //数据总条数
            $pageCount              = ceil($Count/20);                        //数据总页数
            if($data['type']  == 0){
                //获取平台订单数量
                $platform_num       = $Count;
                //获取自营订单数量
                $my_num             = $this->model->get_order_num($data,1);
            }elseif($data['type']  == 1){
                //获取平台订单数量
                $platform_num       = $this->model->get_order_num($data,0);
                //获取自营订单数量
                $my_num             = $Count;
            }
            include('view/yundian/yundian_order_list.php');
    }

    /*
     版权信息:  秘密信息
     功能描述：云店奖励——订单日志
     开 发 者：taojin
     开发日期： 2018-04-09
     重要说明：参数type: 0平台订单 1自营订单
     方法  yundian_order_log  返回errcode(int) errmsg(string) data(array('res23'=>$data1,'res2'=>$data2,'res24'=>$data3))
      */
    public function yundian_order_log(){
        $customer_id            = $this->customer_id;
        $batchcode              = $_GET['batchcode']?$_GET['batchcode']:'';     //订单号
        $user_id                = $_GET['user_id']?$_GET['user_id']:'';         //用户id
        $log                    = $this->model->yundian_order_log($batchcode,$user_id);
        $log_list               = $log['data'];
        $res23                  = $log_list['res23'];
        $res2                   = $log_list['res2'];
        $res24                  = $log_list['res24'];
        $o_batchcode            = $batchcode;

        include('view/yundian/yundian_order_log.php');
    }

  /*
    * 店主审核通过
    * $Author: hjw$
    * $2018-04-08  $
    * 参数：more： 0 单独审核 1 批量审核
    *       more = 0 时:array('more','tequan_id','user_id','profit_shop','self_reware','expire_time') 
    *       more = 0 时（中文备注）: array（'单独','申请的特权ID','用户ID','身份奖励','自营收入','默认到期时间'）
    *       more = 1 时：array('id') //id字符串 格式 '1,2,3'
    */
    public function review_pass(){
        $data  = $_POST;
        $data['customer_id']       = $this->customer_id;
        $data['customer_id_en']    = $this->customer_id_en;
        if(empty($data['customer_id'])){
            json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
        }
        $result = $this->model->review_pass($data);
        json_out($result);
    }

   /*
    * 店主审核驳回
    * $Author: hjw$
    * $2018-04-08  $
    * 参数：more 1批量 0单独
    *       more = 0 时:array('id','reason')
    *       more = 0 时（中文备注）: array（'驳回的ID','驳回原因'）
    *       more = 1 时：array('id','k_id') //id字符串 格式 '1,2,3' ,k_id:weixin_yundian_keeper 的ID组
    */
    public function reject_review(){
        $data  = $_POST;
        $data['customer_id'] = $this->customer_id;
        if(empty($data['customer_id'])){
            json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
        }
        $result = $this->model->reject_review($data);
        json_out($result);

    }  

    /*
     * $explain  : 前台获取单一店主信息
     * $Author   : whl
     * $time     : 2018-04-08  
     * $message  : 公开
     */
    public function shopUserData(){
        $shop_id     = $_POST['yun_user_id'];
        $customer_id = $_POST['customer_id'];
    }

    /*
     * $explain  : 店头背景默认及修改
     * $Author   : zjj-v397
     * $time     : 2018-04-09 
     * $message  : 公开
     */
    public function Background_submission(){
        $data = $_POST;
        if(empty($data['pathArray'])){
            json_out(array('errcode' => 400,'errmsg'=>'pathArray参数丢失！'));
        }
        $result = $this->model->setting_of_store($data);
        json_out($result);

    }

    /*
     * $explain  : 店头背景编辑页面
     * $Author   : zjj-v397
     * $time     : 2018-04-09 
     * $message  : 公开
     */
    public function background_of_store(){
        parent::check_login(); 
        $customer_id = $this->customer_id;

        $theme  = $this->model_common->find_theme($customer_id);
        $upfileUrl = $this->model->select_setting_of_store($customer_id);
       // var_dump($upfileUrl);exit;
        include('view/yundian/background_of_store.php');
    }   

/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主产品详情
    开 发 者：zqs
    开发日期： 2018-04-10
    重要说明：无
     */
     public function shopkeeper_order(){
        $customer_id = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);
        $id     = $_REQUEST['id']?$_REQUEST['id']:"";
        $res = $this->model->description_select($id);
        include('view/yundian/shopkeeper_order.html');
     }

    /*  云店用户退款，换货，退货接口-----卖家端审核--同意
     *  $Author:HMJ-V384
     *  2018-4-10
     *  status:todo
     *  return:
     **/
    public function yundian_pay_return_agree()
    {
        $data['customer_id']    = $this->customer_id;       //商家id
        $data['user_id']        = $_POST['user_id'];        //当前用户ID
        $data['batchcode']      = $_POST['batchcode'];      //订单号
        
        //校验数据
        if (empty($data['customer_id']) || empty($data['batchcode']) || empty($data['user_id'])) {
            $return = array('errcode'=>400, 'errmsg'=>'参数异常！', 'data'=>$data);
            json_out($return);
        }

        $order_msg = $this->model->get_yundian_order_msg($data['batchcode'],$data['customer_id']);
        if(!$order_msg) {
            $return = array('errcode'=>402, 'errmsg'=>'订单数据读取失败！');
            json_out($return);
        }
        if($order_msg['aftersale_type'] == 1) { //申请退款
            if($order_msg['aftersale_state'] != 2) {
                $return = array('errcode'=>401, 'errmsg'=>'申请售后状态异常');
                json_out($return);                  
            }
        } else if($order_msg['aftersale_type'] == 2) { //申请退货
            if($order_msg['aftersale_state'] != 2 || $order_msg['return_status'] != 8) {
                $return = array('errcode'=>401, 'errmsg'=>'申请售后状态异常');
                json_out($return);                  
            }
        }

        $return = $this->model->yundian_money_return($data['user_id'],$data['customer_id'],$data['batchcode']);
            $descript = $return['errmsg'];
            $operation = 16;
            $data_logs = array(
                'batchcode'         =>$data['batchcode'],
                'operation'         =>$operation,
                'descript'          =>'云店退货/退款后台打钱审批：'.$descript,
                'operation_user'    =>$data['user_id'],
                'createtime'        =>date('Y-m-d H:i:s'),
                'isvalid'           =>'1'
            );
        $ret = $this->model->order_logs($data_logs);        
        json_out($return);          

    }

/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主提成收益明细列表
    开 发 者：HMJ-V384
    开发日期： 2018-04-04
    重要说明：无
     */
    function yundian_shopkeeper_reward_detail(){             
        $customer_id  = $this->customer_id;
        $theme        = $this->model_common->find_theme($customer_id);

        $param['customer_id']     = $this->customer_id;
        $param['user_id']         = $_REQUEST['user_id']?$_REQUEST['user_id']:'';
        $param['start_time']      = $_REQUEST['start_time']?$_REQUEST['start_time']:-1;
        $param['end_time']        = $_REQUEST['end_time']?$_REQUEST['end_time']:-1;     
        $param['from_id']         = $_REQUEST['from_id']?$_REQUEST['from_id']:-1;
        $param['name']            = $_REQUEST['name']?$_REQUEST['name']:-1;
        $param['batchcode']       = $_REQUEST['batchcode']?$_REQUEST['batchcode']:-1;
        $param['pay_style']       = $_REQUEST['pay_style']?$_REQUEST['pay_style']:0;
        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页

        $res = $this->model->yundian_shopkeeper_reward_detail($param);//获取店主提成收益明细列表
        $data      = $res['shopkeeper_reward_detail_arr'];
        $pageCount = $res['pageCount'];        
        include("view/yundian/yundian_shopkeeper_reward_detail.php");
    }

    /*
     * 后台操作日志
     * author：cjj
     */
    function yundian_setting_log(){
        $customer_id = $this->customer_id;
        $theme       = $this->model_common->find_theme($customer_id);

        $param['customer_id']     = $this->customer_id;
        $param['start_time']      = $_REQUEST['start_time']?$_REQUEST['start_time']:-1;
        $param['end_time']        = $_REQUEST['end_time']?$_REQUEST['end_time']:-1;
        $param['word']             = $_REQUEST['word']?$_REQUEST['word']:'';
        $pageNum                    = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页

        $res       = $this->model->yundian_setting_log($param);//获取店主列表
        $data      = $res['res'];
        $pageCount = $res['pageCount'];
        include("view/yundian/yundian_setting_log.php");
    }
    /*
     * 权限设置
     * author：hjw
     */
    public function auth_edit()
    {
        $data['customer_id'] = $this->customer_id;
        $theme               = $this->model_common->find_theme($data['customer_id']);
        $data['id']          = $_REQUEST['id'];
        $data['type']        = $_REQUEST['type'];
        $links2      = $_REQUEST['links2'];
        if ($data['type']    == 'edit') 
        {
            $data['remark']  =  $_REQUEST['remark'];
            //数组初始化
            $arr = array();
            for($i=0;$i<13;$i++) {
                $arr[$i] = 0;
            }
            //数组设值
            for($i=0;$i<13;$i++) {
                if(in_array($i,$links2)){
                    $arr[$i] = 1;
                }
            }
            $data['tequan'] = implode("_",$arr);
        }
        $res =  $this->model->auth_edit($data);
        //var_dump($res);

        include('view/yundian/auth_edit.html');
    }


}
