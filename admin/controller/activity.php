<?php


class control_activity extends control_base
{
    var $model;

    function __construct()
    {
        parent::__construct();
        require_once('model/activity.php');
        $this->model = new model_activity();

    }

    /*
     * zhoumuwang
     * 公共活动列表
     * 参数：count-条数、page-页数、act_name-活动名称、start_time-开始时间、end_time-结束时间、add_time-添加时间、act_type-活动类型 0购买产品送积分 1签到送积分 2兑换扣积分'、status-状态0-未开启 1-已开启 2-已结束 3-手动结束、cust_id-商家id
    */
    function activity_index()
    {

        $data   = $this->parmdata;

        $result = $this->model->activity_index($data);

        json_out($result);
    }

    

    /*
     * zhoumuwang
     * 公共读取活动数据
     * 参数：act_id-活动id
    */
    function read()
    {   
        $data   = $this->parmdata;
        $result = $this->model->read($data);

        json_out($result);
    }

    /*
     * zhoumuwang
     * 添加签到活动
     * 添加签到活动时候，先读取签到活动默认时间配置
    */
    function sign_add()
    {
        $result = $this->model->sign_add($this->parmdata);
        json_out($result);
    }


    /*
     * zhoumuwang
     * 定时任务接口
     * 过期的活动状态改为已结束
    */
    function crontab_activity()
    {

        $result = $this->model->crontab_activity();
        json_out($result);
    }


    /*
    * 积分活动保存操作
     * 参数：start_time-开始时间、end_time-结束时间、act_name-活动名称、status-是否发布、cust_id-商家ID、ext_info-配置json、act_type-活动类型 0购买产品送积分 1签到送积分 2兑换扣积分'、act_id-活动id、op-操作del-删除 release发布 end结束 conserve更新保存
    * $Author: 刘仲轩 $
    * 2017-08-24  $
    */
    function save_activity()
    {

        /*$data['op']       = $this->parmdata['op']; //操作类型
        $data['act_id']     = $this->parmdata['act_id']; //活动编号
        $data['cust_id']    = $this->parmdata['cust_id'];    //商家ID
        $data['act_name']   = $this->parmdata['act_name'];   //活动名称
        $data['act_type']   = $this->parmdata['act_type'];   //活动类型 0购买产品送积分 1签到送积分 2兑换扣积分'
        $data['is_commission']      = $this->parmdata['is_commission']; //'是否参与分佣：0不参加 1参加'
        $data['only_type'] = $this->parmdata['only_type']; //-1不限 1-仅商城 2-仅门店
        $data['start_time'] = $this->parmdata['start_time']; //开始时间
        $data['end_time']   = $this->parmdata['end_time'];   //结束时间
        $data['status']     = $this->parmdata['status'];     //是否发布
        $data['suspend_onoff'] = $this->parmdata['suspend_onoff'];   */
        $ext_info   = $this->parmdata['ext_info'];   //配置json


        $data   = $this->parmdata;
        $data['cust_id']  = $this->customer_id;
        $data['add_time']   = date('Y-m-d H:i:s',time());     //创建时间
        $data['isvalid']    = 1;

        //校验数据
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'cust_id参数丢失！'));
        }

        if(empty($data['op'])){
            json_out(array('errcode'=>600,'errmsg'=>'op不能为空'));
        }
        if($data['op'] != 'conserve'){
            if($data['act_id']<0 || $data['act_id']=='')
            {
                json_out(array('errcode' => 600,'errmsg'=>'act_id参数丢失！'));
            }
        }
        if($data['op'] == 'conserve'){
            if($data['act_name']==''){
                json_out(array('errcode'=>600,'errmsg'=>'活动名称不能为空'));
            }
            if($data['act_type']==''){
                json_out(array('errcode'=>600,'errmsg'=>'活动类型不能为空'));
            }
            // if($data['auto_start']==''){
            //     json_out(array('errcode'=>600,'errmsg'=>'是否自动发布不能为空'));
            // }
            if($data['is_commission']=='' && $data['act_type'] != 1){
                json_out(array('errcode'=>600,'errmsg'=>'是否参与分佣'));
            }
            if($data['start_time']==''){
                json_out(array('errcode'=>600,'errmsg'=>'开始时间不能为空'));
            }else if( date('Y',strtotime($data['start_time'])) < 2000){
                json_out(array('errcode'=>600,'errmsg'=>'开始时间格式有误'));
            }

            if($data['end_time']==''){
                json_out(array('errcode'=>600,'errmsg'=>'结束时间不能为空'));
            }else if( strtotime($data['end_time']) < time() ){
                json_out(array('errcode'=>600,'errmsg'=>'结束时间不能早于现在'));
            }
            if($ext_info==''){
                json_out(array('errcode'=>600,'errmsg'=>'ext_info不能为空'));
            }
        }

        $data['ext_info']   = json_encode($ext_info);   //配置json

        $result = $this->model->saveactivity($data);

        json_out($result);
    }



    /*
    * 添加积分活动产品时，获取 常量积分产品 列表，已剔除已添加产品
    * $Author: wuhaoliang $
    * $2017-08-24  $
    */
    function get_integral_product_list(){


        $data['cust_id']      = $this->customer_id;    //商家ID
        $data['search_key']   = $this->parmdata['search_key'];        //搜索条件，形式：array('product_name'=>'','product_id'=>'','type_id'=>'-1');
        $data['act_id']       = $this->parmdata['act_id'];
        $data['page']         = (int)$this->parmdata['page'];         //当前页数
        $data['page_size']    = (int)$this->parmdata['page_size'];    //单页数据条数

        //模拟数据
        //$data['search_key'] = array('product_name'=>'刘芬','product_id'=>'','type_id'=>'-1');


        //判断数据是否安全
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['act_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'act_id参数丢失！'));
        }
        if(empty($data['search_key']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
        }
        if(empty($data['page_size']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'page_size参数丢失！'));
        }else if($data['page_size'] < 1){
            json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
        }

        $result = $this->model->m_get_integral_product_list($data);

        json_out($result);

    }

    /*
     * 添加门店积分产品时 获取 门店产品列表
     * $Author: wuhaoliang $
     * $2017-11-7  $
     */
    function get_store_product_list(){


        $data['cust_id']      = $this->customer_id;    //商家ID
        $data['search_key']   = $this->parmdata['search_key'];        //搜索条件，形式：array('product_name'=>'','product_id'=>'','type_id'=>'-1');
        $data['page']         = (int)$this->parmdata['page'];         //当前页数
        $data['page_size']    = (int)$this->parmdata['page_size'];    //单页数据条数

        //模拟数据
        //$data['search_key'] = array('product_name'=>'刘芬','product_id'=>'','type_id'=>'-1');


        //判断数据是否安全
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['search_key']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
        }
        if(empty($data['page_size']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'page_size参数丢失！'));
        }else if($data['page_size'] < 1){
            json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
        }

        $result = $this->model->m_get_store_product_list($data);

        json_out($result);

    }

    /*
    * 获取 所有类型 列表
    * $Author: wuhaoliang $
    * $2017-09-02  $
    */
    function __get_all_type(){
        $data['cust_id']  = $this->customer_id;//3243;//
        //判断数据是否安全
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }

        $result = $this->model->get_all_product_type($data);

        json_out($result);
    }

    /*
    * 获取 已添加的常量积分产品 列表
    * $Author: wuhaoliang $
    * $2017-09-02  修改2018-1-4 hjw$
    */
     function __get_integral_product(){
        $data['cust_id']      = $this->customer_id;    //商家ID3243;//
        $data['search_key']   = $this->parmdata['search_key'];        
        //搜索条件，形式：array('product_name'=>'','product_id'=>'','type_id'=>'-1');
        $data['page']         = (int)$this->parmdata['page'];         //当前页数3;//
        $data['page_size']    = (int)$this->parmdata['page_size'];    //单页数据条数2;//
        $data['integral_type']= $this->parmdata['integral_type'];     //0商城  1门店

        //模拟数据
        //$data['search_key']  = array('product_name'=>'','product_id'=>'','type_id'=>'-1');

        //判断数据是否安全
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['search_key']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
        }
        if(empty($data['page_size']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'page_size参数丢失！'));
        }else if($data['page_size'] < 1){
            json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
        }
        if($data['integral_type']!= 0 && $data['integral_type'] != 1)
        {
            json_out(array('errcode' => 400,'errmsg'=>'integral_type参数丢失！'));
        }

        $result = $this->model->get_integral_product($data);
        json_out($result);

     }


    /*
     * 获取产品列表,除去积分产品,用于兑换产品
     * $Author: wuhaoliang $
     * $2017-08-24  $
     */
    function get_product_except_inte(){

        $data['cust_id']      = $this->customer_id;//商家ID
        $data['search_key']   = $this->parmdata['search_key'];   //搜索条件，形式：array('product_name'=>'','product_id'=>'','type_id'=>'-1');
        $data['page']         = (int)$this->parmdata['page'];
        $data['page_size']    = (int)$this->parmdata['page_size'];
        $data['act_id']       = $this->parmdata['act_id'];

        //模拟数据
        /*$search_key = array('product_name'=>'刘','product_id'=>'','type_id'=>'-1');
        $data['search_key'] = json_encode($search_key);*/
        //var_dump($data);
        //判断数据是否安全
        if(empty($data['act_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'act_id参数丢失！'));
        }
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['search_key']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
        }
        if(empty($data['page_size']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'page_size参数丢失！'));
        }else if($data['page_size'] < 1){
            json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
        }
        $result = $this->model->m_get_product_except_inte($data);

        json_out($result);

    }
    /*
     * 获取活动产品
     * $Author: wuhaoliang $
     * $2017-08-31  $
     */
    function __get_activity_product(){
        $data['cust_id']      = $this->customer_id;//商家ID
        $data['act_id']       = $this->parmdata['act_id'];
        $data['page']         = (int)$this->parmdata['page'];
        $data['page_size']    = (int)$this->parmdata['page_size'];
        //var_dump($data);
         if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['act_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'act_id参数丢失！'));
        }
        if(empty($data['page_size']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'page_size参数丢失！'));
        }else if($data['page_size'] < 1){
            json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
        }
        //var_dump(202323);
        $result = $this->model->m_get_activity_product($data);

        json_out($result);
    }


    /*
     * 添加常量积分产品
     * $Author: wuhaoliang $
     * $2017-08-25  $
     */
    function add_integral_product(){
        //模拟数据

        $data['p_ids']          = $this->parmdata['p_ids'];         //产品ID链
        $data['integral_type']  = $this->parmdata['integral_type']; //0 商城  1 门店
        $data['cust_id']        = $this->customer_id;

        //初始化
        $data['consume_integral']   = 0;
        $data['consume_type']       = 1;
        $data['recommend_integral'] = 0;
        $data['recommend_type']     = 1;

        //获取默认配置
        $setting_data['cust_id']= $this->customer_id;
        $setting = $this->model->integral_setting_details($setting_data);
        if($setting['errcode'] == 0){
            if( $data['integral_type'] == 0){
                $setting = json_decode($setting['data']['basic_json'],TRUE);
                $data['recommend_integral'] = $setting['gift_set_value'];
                $data['recommend_type']     = $setting['gift_set_type']+1;
            }else{
                $setting = json_decode($setting['data']['store_json'],TRUE);
            }
            $data['consume_integral']   = $setting['gift_set_value'];
            $data['consume_type']       = $setting['gift_set_type']+1;
           
        }

        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'act_id参数丢失！'));
        }
        if(empty($data['p_ids']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'p_ids参数丢失！'));
        }
        if($data['integral_type']!= 0 && $data['integral_type'] != 1)
        {
            json_out(array('errcode' => 400,'errmsg'=>'integral_type参数丢失！'));
        }

        //判断是多个产品还是单个产品
        if(strpos($data['p_ids'],'_') === false){
            $data['p_id'] = $data['p_ids'];
            //var_dump($data['p_id']);
            $result = $this->model->m_add_integral_product($data);
            if($result['errcode'] != 0){
                json_out($result);
            }
        }else{
            $pids = explode('_',$data['p_ids']);
            foreach ($pids as $key => $one_p) {
                $data['p_id'] = $one_p;
                if($data['p_id'] != ''){
                    //var_dump($data['p_id']);
                    $result       = $this->model->m_add_integral_product($data);
                    if($result['errcode'] != 0){
                        json_out($result);
                        exit();
                    }
                }
            }

        }
        json_out($result);
    }
     /*
     * 删除常量积分产品
     * $Author: wuhaoliang $
     * $2017-08-25  $
     */
    function del_integral_product(){
        $data['p_id']           = $this->parmdata['p_id'];          //产品ID链
        $data['integral_type']  = $this->parmdata['integral_type']; //0 商城  1 门店
        $data['cust_id']        = $this->customer_id;
       
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['p_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'p_id参数丢失！'));
        }
        if($data['integral_type']!= 0 && $data['integral_type'] != 1)
        {
            json_out(array('errcode' => 400,'errmsg'=>'integral_type参数丢失！'));
        }

        $result = $this->model->m_del_integral_product($data);
        json_out($result);
    }

    /*
     * 删除活动产品
     * $Author: wuhaoliang $
     * $2017-09-06  $
     */
    function del_activity_product(){
        $data['p_id']    = $this->parmdata['p_id'];     //产品ID链
        $data['act_id']  = $this->parmdata['act_id'];     //产品ID链
        $data['cust_id'] = $this->customer_id;

        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }
         if(empty($data['act_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'act_id参数丢失！'));
        }
        if(empty($data['p_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'p_id参数丢失！'));
        }

        $result = $this->model->m_del_activity_product($data);
        json_out($result);
    }

    //test test tttttttttt
    function  test_fun(){

        $data['p_id']    = 4774;     //产品ID
        $data['cust_id'] = 3243;
        $data['pros_id'] = -1;
        $data['rcount']  = 2;

        $result = $this->model->get_pros_integral($data);
        json_out($result);
    }


    /*
     * 添加活动产品
     * $Author: wuhaoliang $
     * $2017-09-05  $
     */


    function add_activity_product(){

        $data['cust_id'] = $this->customer_id;
        $data['p_ids']   = $this->parmdata['p_ids'];       //产品ID链
        $data['act_id']  = $this->parmdata['act_id'];      //活动ID
        $act_type  = $this->parmdata['act_type'];    //活动类型  1为积分活动  2为兑换活动

        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['act_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'act_id参数丢失！'));
        }
        if(empty($data['p_ids']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'p_ids参数丢失！'));
        }
        if(empty($act_type))
        {
            json_out(array('errcode' => 400,'errmsg'=>'act_type参数丢失！'));
        }
        //插入初始化信息
        //积分
        $data['consume_integral']   = 0;
        $data['recommend_integral'] = 0;
        $data['integral_type']      = 1;       //注意，这里是consume_type 不是区分门店
        //兑换
        $data['store_integral']     = 1;
        $data['integral']           = 1;
        $data['money']              = 0;
        $data['stock']              = 0;

        //判断是多个产品还是单个产品
        if(strpos($data['p_ids'],'_') === false){
            $data['p_id'] = $data['p_ids'];
            if($act_type == 1){
                $result   = $this->model->m_add_activity_integral_product($data);
            }else{
                $result   = $this->model->m_add_integral_exchange_product($data);
            }
            if($result['errcode'] != 0){
                json_out($result);
            }
        }else{
            $pids = explode('_',$data['p_ids']);
            foreach ($pids as $key => $one_p) {
                $data['p_id'] = $one_p;
                if($data['p_id'] != ''){
                    if($act_type == 1){
                        $result   = $this->model->m_add_activity_integral_product($data);
                    }else{
                        $result   = $this->model->m_add_integral_exchange_product($data);
                    }
                    if($result['errcode'] != 0){
                        json_out($result);
                        exit();
                    }
                }
             }

        }
        json_out($result);

    }

    /*
     * 保存积分活动产品
     * $Author: wuhaoliang $
     * $2017-08-25  $
     */
    function add_activity_integral_product(){

        $data['cust_id'] = $this->customer_id;
        $data['act_id']  = $this->parmdata['act_id'];      //活动ID int_save_data
        $data['int_save_data'] = $this->parmdata['int_save_data'];  //array('0'=> '4402_50_60_1','1'=> '4774_40_60_1');//产品id_消费积分_推荐积分_积分类型
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['act_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'act_id参数丢失！'));
        }
        if(empty($data['int_save_data']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'int_save_data参数丢失！'));
        }

        foreach ($data['int_save_data'] as $key => $one) {
            $temp = explode('_',$one);

            $data['p_id']    =  $temp[0];
            $data['consume_integral']   = $temp[1];
            $data['recommend_integral'] = $temp[2];
            $data['integral_type']      = $temp[3]; //注意，这里是consume_type 不是区分门店
            /*
             *检查数据是否安全
             */
            $result = $this->model->m_add_activity_integral_product($data);
            if($result['errcode'] != 0){
                    json_out($result);
                    exit();
            }
        }
        json_out($result);
    }

    /*
     * 保存兑换活动产品
     * $Author: wuhaoliang $
     * $2017-08-25  $
     */
    function add_integral_exchange_product(){
        $data['cust_id'] = $this->customer_id;
        $data['act_id']  = $this->parmdata['act_id'];
        $data['exc_save_data'] = $this->parmdata['exc_save_data'];//array(0=>'4775_50_60_100');//产品id_所需积分_所需金额_库存

        foreach ($data['exc_save_data'] as $key => $one) {
            $temp = explode('_',$one);

            $data['p_id']    = $temp[0];
            $data['integral']= $temp[1];
            $data['store_integral']= $temp[2];
            $data['money']   = $temp[3];
            $data['stock']   = $temp[4];
            if( empty($data['integral'])|| $data['integral'] <= 0){
                $data['integral'] = 1;
            }
            if( empty($data['store_integral']) || $data['store_integral'] <= 0 ){
                $data['store_integral'] = 1;
            }
            if( empty($data['money']) ){
                $data['money'] = 0;
            }
            /*
             *检查数据是否安全
             */
            $result = $this->model->m_add_integral_exchange_product($data);
            if($result['errcode'] != 0){
                json_out($result);
                exit();
            }
        }
        json_out($result);
    }





    /*function checktime(){
        $data['act_type']       = $this->parmdata['act_type']; //操作类型
        $data['cust_id']        = $this->parmdata['cust_id']; //操作类型
        $data['start_time']     = $this->parmdata['start_time']; //操作类型
        $data['end_time']       = $this->parmdata['end_time']; //操作类型
        //判断数据是否安全
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'cust_id参数丢失！'));
        }
         if(empty($data['act_type']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'act_type参数丢失！'));
        }
         if(empty($data['start_time']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'start_time参数丢失！'));
        }
         if(empty($data['end_time']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'end_time参数丢失！'));
        }
        $result = $this->model->checktime($data);
        json_out($result) ;

    }*/




    /*
     * 后台获取单个用户积分明细
     * $Author: wuhaoliang $
     * 2017-9-05  (改)2017-11-1 $
     */
    function user_integral_log(){

        $data['cust_id']      = $this->customer_id;             //  商家ID
        $data['user_id']      = $this->parmdata['user_id'];     //  用户id

        if(empty($_GET['integral_type'])){
            $data['integral_type'] = $this->parmdata['integral_type']; //积分明细类型：-1为全部 0为商城积分  1为门店积分
        }else{
            $data['integral_type'] = $_GET['integral_type'];
        }

        $data['type']         = $this->parmdata['type'];          //明细类型：-1为全部 1为收入  2为支出  3签到收入
        $data['start_time']   = $this->parmdata['start_time'];  //  前时间 可以为空
        $data['end_time']     = $this->parmdata['end_time'];    //  后时间 可以为空
        $data['page']         = (int)$this->parmdata['page'];
        $data['page_size']    = (int)$this->parmdata['page_size'];



        //判断数据是否安全
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['user_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'user_id参数丢失！'));
        }
        if($data['integral_type']==null)
        {
            json_out(array('errcode' => 600,'errmsg'=>'integral_type参数丢失！'));
        }

        if($data['integral_type']==-1 || $data['integral_type']==0 || $data['integral_type']==1)
        {

        }
        else
        {
            json_out(array('errcode' => 600,'errmsg'=>'integral_type参数不正确！'));
        }

        if(empty($data['type']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'type参数丢失！'));
        }
        if(empty($data['page_size'])) {
            json_out(array('errcode' => 400,'errmsg'=>'page_size参数丢失！'));
//            $data['page_size']=10;
        }else if($data['page_size'] < 1){
            json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
        }

        $result = $this->model->m_user_integral_log($data);
        json_out($result);

    }


    /*
    * 获取用户签到积分明细
    * $Author: djy $
    * 2017-08-28  $
    */
    function integral_sign_log()
    {
        $data                 = $this->parmdata;
        $data['cust_id']      = $this->customer_id;      //商家ID

        /* 模拟数据 */
        /* $data['user_id']      = 195217;      //用户ID
        $data['type']      = 1;      //类型：签到
        $data['page']      = '1';
        $data['count']      = '3';
        $data['start_time']      = '2016-08-23 11:29:00';
        $data['end_time']      = '2016-08-23 21:30:59'; */



        $result1 = $this->model->integral_log($data);

        $result2 = $this->model->integral_sign_log_sum($data);

        if($result1['errcode'] == 0 && $result2['errcode'] == 0){
            $result['errcode'] = 0;
            $result['errmsg'] = '获取成功';
            $result['data_count'] = $result2['data'];
            $result['data_list'] = $result1['data'];
        }else{
            $result['errcode'] = 400;
            $result['errmsg'] = '获取失败';
        }

        json_out($result);
    }
    /*
    * 积分产品保存操作
    * 参数：id：'积分id'；
            is_commission：'是否参与分佣：0不参加 1参加'；
            mode：'模式：0全局 1自定义'；
            consume_integral：'消费积分(如果有百分号表示比例，否则表示数字)'；
            recommend_integral：'推荐积分(如果有百分号表示比例，否则表示数字)'；
            consume_type：'赠送类型：1比例2固定值'；
            recommend_type：'推荐类型：1比例2固定值'；
    * $Author: liuzhongxuan $
    * 2017-08-24  $
    */
    function save_integral_setting_product()
    {
        /*$data['id']                   = $this->parmdata['id'];
        $data['is_commission：']         = $this->parmdata['is_commission：'];
        $data['mode']                   = $this->parmdata['mode'];
        $data['consume_integral']       = $this->parmdata['consume_integral'];
        $data['recommend_integral']     = $this->parmdata['recommend_integral'];
        $data['consume_type']           = $this->parmdata['consume_type'];
        $data['recommend_type']         = $this->parmdata['recommend_type'];*/
        $data   = $this->parmdata;
        $data['cust_id']  = $this->customer_id;

        //var_dump($this->parmdata);
        //校验数据
        foreach ($data['save_arr'] as $key => $value ){
            if(empty($value['id'])){
                json_out(array('errcode' => 600,'errmsg'=>'id参数丢失！'));
            }
            if($value['mode']==''){
                json_out(array('errcode' => 600,'errmsg'=>'mode参数丢失！'));
            }
            if($value['consume_integral']==''){
                json_out(array('errcode' => 600,'errmsg'=>'consume_integral参数丢失！'));
            }
            if($value['recommend_integral']==''){
                json_out(array('errcode' => 600,'errmsg'=>'recommend_integral参数丢失！'));
            }
            if($value['consume_type']==''){
                json_out(array('errcode' => 600,'errmsg'=>'consume_type参数丢失！'));
            }
            if($value['recommend_type']==''){
                json_out(array('errcode' => 600,'errmsg'=>'recommend_type参数丢失！'));
            }
        }

        $result_basic = $this->model->integral_setting_details($data);
        if($result_basic['errcode'] == 0){
            $basic_arr = json_decode($result_basic['data']['basic_json'],TRUE);
            $data['gift_set_type']   =  ($basic_arr['gift_set_type']+1);  //配置记录0为比例 1为固定
        }

     
        $result = $this->model->saveintegralproduct($data);
        $result != false ? $res = array('errcode' => 0,'errmsg'=>'提交成功') : $res = array('errcode' => 400,'errmsg'=>'提交失败');
        json_out($res);
    }

    /*
    * 单个积分产品获取操作
    * 参数：cust_id：'商家id'；
            product_id：'产品id'；
    * $Author: liuzhongxuan $
    * 2017-08-24  $
    */
    function get_one_integral_setting_product()
    {
        $data   = $this->parmdata;
        $data['cust_id']  = $this->customer_id;
        //var_dump($data);

        //校验数据
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['product_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'product_id参数丢失！'));
        }
        if($data['integral_type'] != "0" && $data['integral_type'] != "1")
        {
            json_out(array('errcode' => 400,'errmsg'=>'integral_type参数有误！'));
        }

        $result = $this->model->get_one_integral_product($data);
        //var_dump($result);
        json_out($result);
    }

    /*
     * djy
     * 积分设置
    */
    function integral_setting()
    {
        $data                 = $this->parmdata;
        //模拟数据
        $data['cust_id']         = $this->customer_id;

        /* 模拟数据
        $data['shop_onoff']      = 1;       //购物积分开关
        $data['reward_onoff'] = 1;  //奖励积分开关
        $data['aftersale_onoff'] = 1;  //售后开关
        $basic_json = array(
                            'integral_name'       => '商城积分', //积分命名
                            'gift_set_type'       => '1', //赠送设置类型：1、按产品现价比例，2、按固定积分
                            'gift_set_value'      => '50', //赠送设置的值：类型为1则为比例，类型为2则为积分
                            'conversion_ratio_integral'       => '1000', //积分兑换比例，积分
                            'conversion_ratio_price'       => '1', //积分兑换比例，金额
                            'is_commission'       => '1', //是否参与分佣，0否，1是
                            'clear_integral_time'       => '2017-8-23 15:30', //清除积分时间
                            'clear_integral_notice'             =>array(
                                'time1'            => array(//时间段1配置
                                                        'ahead_days'    => '10',    //开始时间
                                                        'notice_time'      => '2017-8-23 18:30' //结束时间
                                                     ),
                                'time2'            => array(//时间段1配置
                                                        'ahead_days'    => '10',    //开始时间
                                                        'notice_time'      => '2017-8-23 18:30' //结束时间
                                                     )
                            ),
                            'focus_reward'       => '5', //新用户关注公众号奖励
                            'referrer_focus_reward'       => '3', //新用户关注公众号 推荐人奖励
                            'bind_phone_reward'       => '5', //新用户绑定手机奖励
                            'referrer_bind_phone_reward'       => '3', //新用户绑定手机 推荐人奖励
                            'first_order_reward_type'       => '1', //首单奖励积分类型：1、按产品现价比例，2、按固定积分
                            'first_order_reward'       => '4', //首单奖励积分：类型为1则为比例，类型为2则为积分
                            'referrer_first_order_reward'       => '8' //首单奖励积分：类型为1则为比例，类型为2则为积分 推荐人奖励

                          );
        */
        $pregTitleSave = "/[\x{4e00}-\x{9fa5}\·\.\",\?|A-Za-z\.\。\d？！!(（)）：:+-{}\[\]【】《》<>-_· “”：，]+/iu";
        preg_match_all($pregTitleSave,$data['basic_json']['integral_name'],$titleArray);   //去掉特殊表情 只保留应该有的字符
        $data['basic_json']['integral_name'] = implode('',$titleArray[0]);
        $data['basic_json']  = json_encode($data['basic_json'], JSON_UNESCAPED_UNICODE);   //积分配置json
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }

        $result = $this->model->save_integral_setting($data);
        $result != false ? $res = array('errcode' => 0,'errmsg'=>'提交成功') : $res = array('errcode' => 400,'errmsg'=>'提交失败');
        json_out($res);
    }

    /*
    * 获取积分设置数据
    * $Author: djy $
    * 2017-08-28  $
    */
    function integral_setting_details()
    {
        $data                 = $this->parmdata;
        $data['cust_id']      = $this->customer_id;      //商家ID

        $result = $this->model->integral_setting_details($data);
        if($result['errcode'] == 0){
            $result['data']['basic_json'] = json_decode($result['data']['basic_json'],TRUE);
        }
        json_out($result);
    }

    /*
     * 获取用户积分
     * $Author: wuhaoliang $
     * 2017-08-28  $
     */
    function __get_user_integral(){
        $data   = $this->parmdata;
        $data['user_id']  = 309497;
        $data['cust_id']  = 3243;

        //校验数据
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['user_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'user_id参数丢失！'));
        }

        $result = $this->model->get_user_integral($data);
        json_out($result);
    }

    /*
     * 获取用户积分明细
     * $Author: wuhaoliang $
     * 2017-08-28  $
     */
    function get_user_integral_detail(){
        $data   = $this->parmdata;
        $data['cust_id']  = $this->customer_id;


        /* 模拟数据  */
        $data['user_id']  = $this->parmdata['user_id'];//;195217;
        $data['type']     = $this->parmdata['type'];//0;  //0为全部 1为收入  2为支出
        $data['month']    = $this->parmdata['month'];//8;  //0为全部 1~12为对应的月份
        $data['page']     = $this->parmdata['page'];//'1';
        $data['count']    = $this->parmdata['count'];//'3';

        //校验数据
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['user_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'user_id参数丢失！'));
        }
        if(empty($data['type'])){
            $data['type'] = 0 ;
        }
        if($data['type']<0 || $data['type'] > 2){
            json_out(array('errcode' => 601,'errmsg'=>'type参数出错'));
        }
        $diyname = _get_diyname($data['cust_id']);              //自定义名称

        $result = $this->model->m_get_user_integral($data);
        $result['shop_integral_name'] = $diyname['shop_integral_name'];
        json_out($result);
    }

    /*

     /*获取用户积分统计
     *参数：cust_id：商家id ;user_name:用户名称；user_id：用户id count：条数；page：页数
     * liuzhongxuan  $
     * 2017-08-28  $
    */
    function integral_stat_user_list()
    {
        header("Content-type:text/html;charset=utf-8");
        $data   = $this->parmdata;
        $data['cust_id']  = $this->customer_id;
        $result = $this->model->integral_stat_user_list($data);

        //导出      
        if($_GET['output'] == 1)
        {

            $title = array('用户头像','用户编号','用户名称','积分余额','累计收入积分','累计签到积分','累计出账积分','清除次数','清除总额');
// dump($_SESSION['export_data']);die();
            $this->exportexcel($_SESSION['export_data'],$title);
            
            unset($_SESSION['export_data']);
            unset($result['page1']);
            die();
        }    

        if($data['export'] == 1)
        {
            
            $result['page1']    = $data['page']+1;
            $result['list_num'] = 20;
            $result['sum1']     = $result['data']['total'];

            unset($result['data']['total']);
            unset($result['data']['total']);
            unset($result['data']['page']);
            foreach ($result['data'] as $k => $v) 
            {
                $output[$k]['weixin_headimgurl'] = $v['weixin_headimgurl'];
                $output[$k]['user_id']           = $v['user_id'];
                $output[$k]['weixin_name']       = $v['weixin_name'];
                $output[$k]['balance']           = $v['balance'];
                $output[$k]['input']             = $v['input'];
                $output[$k]['sign_score']        = $v['sign_score'];
                $output[$k]['output']            = $v['output'];
                $output[$k]['clear_num']         = $v['clear_num'];
                $output[$k]['clear_sum']         = $v['clear_sum'];
            }

            $array_exlce_cache = $_SESSION['export_data'];
            foreach($output as $val){ $array_exlce_cache[] = $val; }
            $_SESSION['export_data'] =  $array_exlce_cache;
            unset($result['data']);

        }

        json_out($result);
    }

        /*

     /*积分活动统计数据
     * djy  $
     * 2017-08-28  $
    */
    function integral_activity_statistics()
    {
        $data   = $this->parmdata;
        $data['cust_id']  = $this->customer_id;

        //校验数据
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'cust_id参数丢失！'));
        }

        /* 模拟数据 */
        /*
        $data['search_pid']   = '';
        $data['search_pname'] = '';
        $data['search_ptype'] = '';
        $data['search_actid'] = '';
        $data['search_actname'] = '';
        $data['search_actstatus'] = '';
        $data['search_acttype'] = '';
        $data['page']      = '1';
        $data['count']      = '3';
         */

        $result = $this->model->integral_activity_statistics($data);

        json_out($result);
    }

    /*
     * 积分兑换列表
     * $Author: djy $
     * 2017-08-28  $
     */
    function integral_exchange_product(){
        $data   = $this->parmdata;
        $data['cust_id']  = $this->customer_id;

        //$data['count'] = $_POST["count"];
        //json_out($data);
        /* 模拟数据  */
        /* $data['orderby']  = array(
                                    'type' => 'time',//sale：按销量排序，integral：按积分排序，time：按时间排序
                                    'value'=>'asc'//asc:顺序，desc:倒序
                                  );
        $data['search_ptype']  = 1466;// 产品类型塞选
        $data['page']      = '1';
        $data['count']      = '3';
        $data['search_name']      = '666'; //搜索产品名   */

        //校验数据
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'cust_id参数丢失！'));
        }

        $result = $this->model->integral_exchange_product($data);
        json_out($result);
    }

    /*
    * 获取操作日志
    * $Author: djy $
    * 2017-09-1  $
    */
    function read_admin_log()
    {
        $data                 = $this->parmdata;
        $data['cust_id']      = $this->customer_id;      //商家ID
        /* 模拟数据 */
        /*
        $data['keyword']      = '积分';      //类型：签到
        $data['page']      = '1';
        $data['count']      = '3';
        $data['start_time']      = '2017-09-01 16:18:43';
        $data['end_time']      = '2016-08-23 21:30:59'; */

        $result = $this->model->read_admin_log($data);

        json_out($result);
    }
    /*
    * 校验自动发布时间
    * 参数：start_time-开始时间、end_time-结束时间、
    * $Author: liuzhongxuan $
    * 2017-08-24  $
    */
    function checktime_auto()
    {
        $data                 = $this->parmdata;
        $data['cust_id']      = $this->customer_id;      //商家ID
        /* 模拟数据 */
        /*
        $data['keyword']      = '积分';      //类型：签到
        $data['page']      = '1';
        $data['count']      = '3';
        $data['start_time']      = '2017-09-01 16:18:43';
        $data['end_time']      = '2016-08-23 21:30:59'; */
        //校验数据
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['start_time']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'start_time参数丢失！'));
        }
        if(empty($data['end_time']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'end_time参数丢失！'));
        }
        if(empty($data['act_type']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'act_type参数丢失！'));
        }

        $result = $this->model->checktime_auto($data);

        json_out($result);
    }


        /**
            * 导出数据为excel表格
            *@param $data    一个二维数组,结构如同从数据库查出来的数组
            *@param $title   excel的第一行标题,一个数组,如果为空则没有标题
            *@param $filename 下载的文件名
            *@examlpe
        */
        function exportexcel($data=array(),$title=array(),$filename='report')
        {
            //header("Content-type:application/octet-stream");
            header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=".$filename.".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            ob_clean();
            //导出xls 开始
            if (!empty($title))
            {
                foreach ($title as $k => $v) 
                {
                   $title[$k]=iconv("UTF-8", "GBK//IGNORE",$v);//识别繁体字、特殊字符
                }
                $title= implode("\t", $title);
                echo "$title\n";
            }
            if (!empty($data))
            {
                foreach($data as $key=>$val)
                {
                    foreach ($val as $ck => $cv)
                     {
                        $data[$key][$ck]=iconv("UTF-8", "GBK//IGNORE", $cv);//识别繁体字、特殊字符
                        if($data[$key][$ck] == '')
                        {
                            $data[$key][$ck]=iconv("UTF-8", "GB2312", $cv);
                        }   
                        if($data[$key][$ck] == '')
                        {
                            $data[$key][$ck]= mb_convert_encoding($cv, "GBK", "UTF-8");
                        }
                    }
                    $data[$key]=implode("\t", $data[$key]);
                }
                echo implode("\n",$data);
            }
        }
        
    /*
    * 获取门店积分设置数据
    * $Author: djy $
    * 2017-11-02  $
    */
    function store_setting_details()
    {
        $data                 = $this->parmdata;
        $data['cust_id']      = $this->customer_id;      //商家ID

        $result = $this->model->store_setting_details($data);
        if($result['errcode'] == 0){
            $result['data']['store_json'] = json_decode($result['data']['store_json'],TRUE);
        }
        json_out($result);
    } 

    /*
     * djy
     * 保存门店积分设置
    */
    function save_store_setting()
    {
        $data                 = $this->parmdata;
        //模拟数据
        $data['cust_id']         = $this->customer_id;

        $data['store_json']  = json_encode($data['store_json'], JSON_UNESCAPED_UNICODE);   //积分配置json
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'cust_id参数丢失！'));
        }

        $result = $this->model->save_store_setting($data);
        $result != false ? $res = array('errcode' => 0,'errmsg'=>'提交成功') : $res = array('errcode' => 400,'errmsg'=>'提交失败');
        json_out($res);
    } 

    /*
     * 获取用户门店积分明细
     * $Author: djy $
     * 2017-11-03  $
     */
    function get_user_store_integral_detail(){
        $data   = $this->parmdata;
        $data['cust_id']  = $this->customer_id;


        /* 模拟数据  */
        $data['user_id']  = $this->parmdata['user_id'];//;195217;
        $data['type']     = $this->parmdata['type'];//0;  //0为全部 1为收入  2为支出
        $data['month']    = $this->parmdata['month'];//8;  //0为全部 1~12为对应的月份
        $data['page']     = $this->parmdata['page'];//'1';
        $data['count']    = $this->parmdata['count'];//'3';

        //校验数据
        if(empty($data['cust_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'cust_id参数丢失！'));
        }
        if(empty($data['user_id']))
        {
            json_out(array('errcode' => 600,'errmsg'=>'user_id参数丢失！'));
        }
        if(empty($data['type'])){
            $data['type'] = 0 ;
        }
        if($data['type']<0 || $data['type'] > 2){
            json_out(array('errcode' => 601,'errmsg'=>'type参数出错'));
        }

        $result = $this->model->get_user_store_integral_detail($data);
        json_out($result);
    }

    /*
     * 获取商城积分，门店积分，购物币自定义名称
     * $Author: chenjunjie $
     * 2018-1-3  $
     */
    public function get_diyname(){
        $customer_id = $this->customer_id;

        $result['errcode'] = 0;
        $result['msg']     = '获取成功';

        if(empty($customer_id)){
            $result['errcode'] = 40003;
            $result['msg']     = 'customer_id丢失';
            json_out($result);
        }

        $result['data'] = _get_diyname($customer_id);
        json_out($result);
    }

    /*
    * 获取兑换活动设置
     * $data = ['activity_id']  //兑换活动ID
    * $Author: chenjunjie $
    * 2018-1-3  $
    */
    public function get_exchange_status(){
        $customer_id = $this->customer_id;
        $activity_id = $this->parmdata['activity_id'];
//       $activity_id = 537;     //测试数据

        $result['errcode'] = 0;
        $result['msg']     = '获取成功';

        if(empty($customer_id)){
            $result['errcode'] = 40003;
            $result['msg']     = 'customer_id参数丢失!';
            json_out($result);
        }
        if(empty($activity_id)){
            $result['errcode'] = 40003;
            $result['msg']     = 'activity_id参数丢失!';
            json_out($result);
        }

        $result['data'] = _get_exchange_status($customer_id,$activity_id);
        json_out($result);
    }
        

    /**
     * 积分转换记录
     * $Author:  hjw$
     * $ 2018-1-3 $
     */
    function integral_transformation_log(){
        require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/php-emoji/emoji.php');
        $data = $this->parmdata;
        $data['customer_id']  = $this->customer_id;
        $result = $this->model->integral_transformation_log($data);
        foreach ($result['datas']  as  &$v) {
            $v['user_name'] = str_replace('“','"',$v['user_name']);
            $v['user_name']= emoji_html_to_unified($v['user_name']); 
        }
        json_out($result);
    }


    /*
    * 获取商城或者门店积分转换设置
    * $Author: liusongheng $
    * $data['type'] 1是商城积分 2是门店积分
    * 2018-1-3  $
    */
   function shopmall_integral_transformation_setting(){
        $data['customer_id']  = $this->customer_id;
        $data['type']=$this->parmdata['type'];
        $res = $this->model->get_shop_stroe_integral_onoff_setting($data);
        $result['turn_on_off']=$res['data'];
        $diyname= _get_diyname($data['customer_id']);
        if ($res['data']['shop_onoff'] == 0) {
            $result['errcode'] = 600;
            $result['msg_shop']     = '请先打开'.$diyname['shop_integral_name'].'开关再进行设置!';
        }else{
            $data['type']=1;
            $ret = $this->model->get_shopmall_integral_transform_setting($data);
            if ($ret['errcode'] == 100) {
                $result['errcode'] = 400;
                $result['msg_shop'] = '获取失败';
            }else{
                $result['errcode'] = 0;
                $result['msg_shop'] = '获取成功';
                $result['data_shop']=$ret;
            }
        }
       if ($res['data']['store_onoff'] == 0) {
            $result['errcode'] = 600;
            $result['msg_store']     = '请先打开'.$diyname['store_integral_name'].'开关再进行设置!';
        }else{
            $data['type']=2;
            $ret = $this->model->get_shopmall_integral_transform_setting($data);
            if ($ret['errcode'] == 100) {
                $result['errcode'] = 400;
                $result['msg_store'] = '获取失败';
            }else{
                $result['errcode'] = 0;
                $result['msg_store'] = '获取成功';
                $result['data_store']=$ret;
            }
        }
        json_out($result);
   }


   /*
    * 保存商城或者门店积分转换设置
    * $Author: liusongheng $
    * 2018-1-3  $
    */
   function save_shopmall_integral_transform_setting(){
      $this->parmdata['customer_id']= $this->customer_id;
      $this->parmdata['on_off']=$this->parmdata['turn_on_off'];
      $this->parmdata['remark'] = addslashes($this->parmdata['remark']);
      $ret = $this->model->check_shopmall_integral_transform_setting($this->parmdata);
      if ($ret['errcode'] == 100) {
          json_out($ret);
      }else{
          $res = $this->model->save_shopmall_integral_transform_setting($ret['data']);
          if ($res==true) {
            $result['errcode'] = 0;
            $result['msg'] = '保存成功';
            json_out($result);
          }else{
            $result['errcode'] = 400;
            $result['msg'] = '保存失败';
            json_out($result);
          }
           
      }
   }

    /*
    * 获取商城和门店积分开关设置接口
    * $Author: liusongheng $
    * 2018-1-5  $
    */
   function get_shopmall_integral_onoff_setting(){
      $data['customer_id']  = $this->customer_id;
      $res = $this->model->get_shop_stroe_integral_onoff_setting($data);
      $result['errcode'] = 0;
      $result['msg'] = '获取成功';
      $result['data']=$res;
      json_out($result);
   }



}//类结束
