<?php


class control_qiquan extends control_base
{
    var $model;
    var $model_common;
    function __construct() 
    {
        parent::__construct();
        require_once('model/qiquan.php');
        $this->model = new model_qiquan();
        require_once('model/common.php');
        $this->model_common = new model_common();
        
        parent::check_login();
        $data['data']=file_get_contents('php://input', true);
        //$data = $_REQUEST['data'];
        $customer_id = $this->customer_id;       

        if(empty($customer_id)) exit('未知商家！');

        $this->parmdata  = json_decode($data['data'],true);
        
    }
    
    public function index()
    {
        echo 123;
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——推荐管理列表
    开 发 者：wuzepeng
    开发日期： 2018-06-06
    重要说明：无
     */
    public function qiquan_recommend_list(){
        $customer_id = $this->customer_id;
        $theme       = $this->model_common->find_theme($customer_id);
        $data = $this->model->get_ququan_recommend_list($customer_id);
        require_once('view/qiquan/qiquan_recommend_list.php');
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——添加推荐
    开 发 者：wuzepeng
    开发日期： 2018-06-06
    重要说明：无
    返回：  $return['code'] = 1/0 成功/失败
           $return['msg'] = "删除成功！/删除失败/股票代码信息错误";
     */
    public function add_qiquan_recommend()
    {
        if( !empty($_POST['stock_code']) && !empty($_POST['yield_rate']) && !empty($_POST['buy_price']) && !empty($_POST['sale_price']) && !empty($_POST['profit_loss']) && !empty($_POST['num']) )
        {
            $customer_id = $this->customer_id;
            $check_type = $this->model->check_qiquan_stock_code(addslashes($_POST['stock_code']));
            if($check_type['errcode'] == 200)
            {
                $ret = $this->model->check_exists_recommend(addslashes($_POST['stock_code']));
                if($ret['errcode'] == 400)
                {
                    json_out(array('msg' => $ret['msg'],'code' => 0));
                    return;
                }
                $data['stock_code']  = "'".addslashes($_POST['stock_code'])."'";
                $data['yield_rate']  = addslashes($_POST['yield_rate']);
                $data['buy_price']   = addslashes($_POST['buy_price']);
                $data['sale_price']  = addslashes($_POST['sale_price']);
                $data['profit_loss'] = addslashes($_POST['profit_loss']);
                $data['num']         = intval($_POST['num']);
                $data['createtime']  = "'".date('Y-m-d H:i:s')."'";
                $data['customer_id'] = $customer_id;
                $result = $this->model->insert_qiquan_recommend($data);
                
                if( $result )
                {
                    json_out(array('msg'=>"添加成功",'code'=>1));
                }
                else
                {
                    json_out(array('msg'=>"添加失败",'code'=>0));
                }
                
            }
            else
            {
                json_out(array('msg'=>$check_type['msg'],'code'=>0));
                
            }
            
        }
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——编辑推荐数据显示
    开 发 者：wuzepeng
    开发日期： 2018-06-07
    重要说明：无
     */
    public function getone_qiquan_recommend()
    {
        $id = intval($_POST['id']);
        $data = $this->model->getone_qiquan_recommend_data($id);
        json_out($data);
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——编辑推荐数据更新
    开 发 者：wuzepeng
    开发日期： 2018-06-07
    重要说明：无
    返回：  $return['code'] = 1/0 成功/失败
           $return['msg'] = "删除成功！/删除失败/股票代码信息错误";
     */
    public function edit_qiquan_recommend()
    {
        if( !empty($_POST['stock_code']) && !empty($_POST['yield_rate']) && !empty($_POST['buy_price']) && !empty($_POST['sale_price']) && !empty($_POST['profit_loss']) && !empty($_POST['num']) )
        {
            $customer_id = $this->customer_id;
            $check_type = $this->model->check_qiquan_stock_code(addslashes($_POST['stock_code']));
            if($check_type['errcode'] == 200)
            {
                $data['stock_code']  = addslashes($_POST['stock_code']);
                $data['yield_rate']  = addslashes($_POST['yield_rate']);
                $data['buy_price']   = addslashes($_POST['buy_price']);
                $data['sale_price']  = addslashes($_POST['sale_price']);
                $data['profit_loss'] = addslashes($_POST['profit_loss']);
                $data['num']         = intval($_POST['num']);
                $data['customer_id'] = $customer_id;
                $id                  = intval($_POST['id']);
                $result = $this->model->update_qiquan_recommend($id,$data);
                if( $result )
                {
                    json_out(array('msg'=>"修改成功",'code'=>1));
                }
                else
                {
                    json_out(array('msg'=>"修改失败",'code'=>0));
                }
                
            }
            else
            {
                json_out(array('msg'=>$check_type['msg'],'code'=>0));
                
            }
            
        }
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——删除推荐
    开 发 者：wuzepeng
    开发日期： 2018-06-07
    重要说明：无
    返回：  $return['code'] = 1/0 成功/失败
            $return['msg'] = "删除成功！/删除失败";
     */
    public function delete_qiquan_recommend()
    {
        $recommend_id = intval($_POST['id']);
        $result = $this->model->delete_qiquan_recommend_data($recommend_id);
        if(!$result)
        {
            json_out(array('msg'=>'删除失败','code'=>0));
        }else{
            json_out(array('msg'=>'删除成功','code'=>1));
        }
        
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——订单管理页面数据显示
    开 发 者：wuzepeng
    开发日期： 2018-06-07
    重要说明：无
    */
    public function qiquan_order_list()
    {
        
        $theme                      = $this->model_common->find_theme($this->customer_id);
        $param['customer_id']       = addslashes($this->customer_id);
        $param['batchcode']         = empty(trim($_GET['batchcode']))?-1:addslashes($_GET['batchcode']);//订单号
        $param['phone']             = empty(trim($_GET['phone']))?-1:addslashes($_GET['phone']);//手机号
        $param['user_id']             = empty(trim($_GET['user_id']))?-1:addslashes($_GET['user_id']);//手机号
        $param['stock_code']        = empty(trim($_GET['stock_code']))?-1:addslashes($_GET['stock_code']);//股票编码
        $param['capital']           = empty(trim($_GET['capital']))?-1:addslashes($_GET['capital']);//名义本金
        $param['status']            = !is_numeric(trim($_GET['status']))?-1:addslashes($_GET['status']);//订单状态
        $param['search_time_type']  = empty(trim($_GET['search_time_type']))?-1:addslashes($_GET['search_time_type']);//搜索时间类型
        $param['start_time']        = empty(trim($_GET['start_time']))?-1:addslashes($_GET['start_time']);//开始时间
        $param['end_time']          = empty(trim($_GET['end_time']))?-1:addslashes($_GET['end_time']);//结束时间   
        $pageNum                    = !empty(trim($_GET['pagenum']))?intval($_GET['pagenum']):1;//当前页
        $param['pageNum']           = $pageNum;//当前页
        $customer_id_en             = $this->customer_id_en;
        $res = $this->model->get_qiquan_order_list($param);
        $data = $res['data'];
        $pageCount = $res['pageCount'];
        require_once('view/qiquan/qiquan_order_list.php');
    }
    /*
    版权信息:  秘密信息
    功能描述：期权交易——订单详情页面数据显示
    开 发 者：wuzepeng
    开发日期： 2018-06-07
    重要说明：无
    */
    public function qiquan_order_details()
    {
        $id = intval($_GET['id']);
        $theme = $this->model_common->find_theme($this->customer_id);
        $data = $this->model->get_qiquan_order_details($id);
        require_once('view/qiquan/qiquan_order_details.php');
    }
    /*
     *  黑名单
     *  $Author:   hjw
     *  @param status 1 黑名单 0 取消黑名单；user_id 用户ID
     *   2018-6-6
     */
    public function blacklist(){
       $data['customer_id']       = addslashes($this->customer_id);
       $data['status']            = empty($_POST['status'])?0:addslashes($_POST['status']);
       $data['user_id']           = empty($_POST['user_id'])?0:addslashes($_POST['user_id']);
       $res = $this->model->update_blacklist($data);
       json_out($res);
    }
}
