<?php
class control_queue extends control_base
{
    var $model;
    
    public function __construct()
    {
        parent::__construct();  
        
        parent::check_login();

        require_once('model/queue.php');
        $this->model = new model_queue($this->customer_id);
        
        require_once('model/common.php');
        $this->model_common = new model_common();

        $this->user_id = $_SESSION['user_id_'.$this->customer_id];

        $this->queue_end();
    }

    /**
     * 活动列表
     * $Author     :   zhangcanxin
     * $time       :   2018-4-19
     */
    public function queue_activity(){         
        $theme  = $this->model_common->find_theme($this->customer_id);

        $param['activity_name']    = $_REQUEST['activity_name']?$_REQUEST['activity_name']:"";
        $param['activity_id']      = $_REQUEST['activity_id']?$_REQUEST['activity_id']:-1;        
        $param['starttime']        = $_REQUEST['starttime']?$_REQUEST['starttime']:-1;
        $param['endtime']          = $_REQUEST['endtime']?$_REQUEST['endtime']:-1;
        $param['createtime_start'] = $_REQUEST['createtime_start']?$_REQUEST['createtime_start']:-1;       
        $param['createtime_end']   = $_REQUEST['createtime_end']?$_REQUEST['createtime_end']:-1; 
        $param['isout']            = $_REQUEST['isout']?$_REQUEST['isout']:-1;       
        $pageNum                   = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']          = $pageNum;//当前页

        $res = $this->model->get_queue_activities($param);
        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];        
        include("view/queue/activity.php");
    }

    /**
     * 查看活动
     * $Author     :   zhangcanxin
     * $time       :   2018-4-19
     */
    public function queue_ck(){
        $theme  = $this->model_common->find_theme($this->customer_id);
        
        $data['id']          = $_REQUEST['id']?$_REQUEST['id']:"";

        if(!empty($data['id'])) {
            $res    = $this->model->get_queue_ck($data);
        }
        include("view/queue/ck_activity.php");
    }

    /**
     * 活动操作[添加&修改]
     * $Author     :   zhangcanxin
     * $time       :   2018-4-19
     */
    public function queue_save(){
        $array = json_decode($_REQUEST['arr'],true);
        
        $id                         = $array['id']?$array['id']:"";
        $data['name']               = $array['name']?$array['name']:"";
        $data['start_time']         = $array['start_time']?$array['start_time']:"";
        $data['end_time']           = $array['end_time']?$array['end_time']:"";
        $data['queue_num']          = $array['queue_num']?$array['queue_num']:"";
        $data['queue_expenditure']  = $array['queue_expenditure']?$array['queue_expenditure']:"";
        $data['success_num']        = $array['success_num']?$array['success_num']:"";
        $data['bonus']              = $array['bonus']?$array['bonus']:"";
        $data['expenditure']        = $array['expenditure']?$array['expenditure']:"";
        $data['promote_num']        = $array['promote_num']?$array['promote_num']:"";
        $data['isvalid']            = true;
        $data['get_impose']         = $array['get_impose']?$array['get_impose']:"";
        $data['is_rule']            = $array['is_rule']?$array['is_rule']:"";
        $data['rule']               = $array['rule']?htmlspecialchars($array['rule']):"";
        if(empty($id)) {
            $data['isout']          = false;
            $data['customer_id']    = $this->customer_id;
            $data['createtime']     = date('Y-m-d H:i:s',time());

            $res = $this->model->queue_add($data);
        } else {
            $res = $this->model->queue_save($data,$id);
        }
        
        json_out($res);
    }

    /**
     * 活动简单操作[启用、终止、删除]
     * $Author     :   zhangcanxin
     * $time       :   2018-4-19
     */
    public function queue_exec(){
        $id   = $_REQUEST['id']?$_REQUEST['id']:"";
        $type = $_REQUEST['type']?$_REQUEST['type']:"";
        
        $res = $this->model->queue_exec($id,$type);
        
        json_out($res);
    }

    /**
     * 自动终止活动
     * $Author     :   zhangcanxin
     * $time       :   2018-4-19
     */
    public function queue_end(){
        require_once($_SERVER['DOCUMENT_ROOT']."/mshop/common/finish_queue.php");
        $order = new queue_order($this->customer_id);
        $order->finish_queue_ex();
    }

    /**
     * 关联产品列表
     * $Author     :   zhangcanxin
     * $time       :   2018-4-19
     */
    public function queue_shop(){
        $theme  = $this->model_common->find_theme($this->customer_id);

        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页
        $param['id']              = $_REQUEST['id']?$_REQUEST['id']:"";
        $id                       = $param['id'];

        // 数据校验
        if( $param['id'] == ''){
            json_out(['errmsg'=>'活动编码不能为空']);
        }

        $data['id']               = $param['id'];

        $arr = $this->model->get_queue_ck($data);
        
        $res = $this->model->get_queue_shop($param);

        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];
        
        include("view/queue/activity_shop.php");
    }

    /**
     * 产品列表
     * $Author     :   zhangcanxin
     * $time       :   2018-4-19
     */
    public function queue_product(){
        $theme  = $this->model_common->find_theme($this->customer_id);

        $pageNum                   = $_GET['pagenum']?$_GET['pagenum']:1;//当前页
        if ($_POST['pagenum']) {
            $pageNum               = $_POST['pagenum'];
        }
        $param['product_id']       = $_REQUEST['product_id']?$_REQUEST['product_id']:-1;        
        $param['product_name']     = $_REQUEST['product_name']?$_REQUEST['product_name']:"";
        $param['product_type']     = $_REQUEST['product_type']?$_REQUEST['product_type']:-1;
        $param['activity_id']      = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"";
        $param['pageNum']          = $pageNum;//当前页
        $param['customer_id']      = $this->customer_id;

        // 数据校验
        if( $param['activity_id'] == ''){
            json_out(['errmsg'=>'活动编码不能为空']);
        }

        $res = $this->model->get_queue_product($param);

        $type      = $res['type'];
        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];

        include("view/queue/product.php");
    }

    /**
     * 添加关联产品
     * $Author     :   zhangcanxin
     * $time       :   2018-4-19
     */
    public function product_add(){
        $param['idsStr']           = $_REQUEST['idsStr']?$_REQUEST['idsStr']:'';        
        $param['activity_id']      = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"";
        $str                       = substr($param['idsStr'],strlen($param['idsStr'])-1,strlen($param['idsStr']));
        if ($str == ',') {
            $param['idsStr']       = substr($param['idsStr'],0,strlen($param['idsStr'])-1);
        }

        $res = $this->model->product_add($param);
      
        json_out($res);
    }

    /**
     * 删除关联产品
     * $Author     :   zhangcanxin
     * $time       :   2018-4-19
     */
    public function product_del(){      
        $param['activity_id']      = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"";
        $param['pid']              = $_REQUEST['pid']?$_REQUEST['pid']:"";
        
        $res = $this->model->product_del($param);

        json_out($res);
    }

    /**
     * 活动统计
     * $Author     :   zhangcanxin
     * $time       :   2018-4-19
     */
    public function queue_count(){      
        $theme  = $this->model_common->find_theme($this->customer_id);
        
        $param['activity_name']    = $_REQUEST['activity_name']?$_REQUEST['activity_name']:"";
        $param['activity_id']      = $_REQUEST['activity_id']?$_REQUEST['activity_id']:-1;        
        $param['isout']            = $_REQUEST['isout']?$_REQUEST['isout']:-1;
        $param['user_name']        = $_REQUEST['user_name']?$_REQUEST['user_name']:"";
        $param['user_id']          = $_REQUEST['user_id']?$_REQUEST['user_id']:-1;       
        $param['status']           = $_REQUEST['status']?$_REQUEST['status']:-1; 
        $pageNum                   = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']          = $pageNum;//当前页
        $id                        = $_REQUEST['id']?$_REQUEST['id']:-1;
        $param['batchcode']        = $_REQUEST['batchcode']?$_REQUEST['batchcode']:-1;
        $param['id']               = $id;

        $data = $this->model->get_queue_count($param);
        $pageCount = $data['pageCount'];
        $res       = $data['arr'];
        include("view/queue/count.php");
    }
    /**
     * 状态改为已删除
     * $Author     :   yaojinpei
     * $time       :   2018-11-1
     */
    public function status_del(){
        $data['y_status'] = $_POST['status'];
        $user_id = $_POST['user_id'];
        // if( $_POST['status'] == 1 || $_POST['status'] == 2 || $_POST['status'] == 3 ){
            $this->WeChat_news($this->customer_id,$user_id);
        // }
        $data['status'] = 7;
        $id   = $_POST['id'];
        $res = $this->model->update_status($data,$id);
        require_once($_SERVER['DOCUMENT_ROOT'].'/mshop/web/model/queue_activity.php');
        $this->model = new model_queue_activity($this->customer_id);
        $this->model->check_queue_success();//检测是否有排队中的订单已完成排队并改变状态

        $this->model->check_queue_receive();//检测是否有排队完成的订单已达到领取条件并改变状态
        
        json_out($res);
    }

    /**
     * 删除数据
     * $Author     :   yaojinpei
     * $time       :   2018-11-1
     */
    public function data_del(){
        $id   = $_POST['id'];
        $sql  = "UPDATE ".WSY_MARK.".weixin_commonshop_queue_order SET isvalid = 0 where id = '".$id."' ";
        $res  = _mysql_query($sql);
        json_out($res);
    }
        /*
     *  [发送微信消息]
     *  $Author     :   yaojinpei
     *  $time       :   2018-11-1
     *  $user_arr   :   需要发送消息的用户
    **/
    public function WeChat_news($customer_id,$user_id) {
        require_once ROOT_DIR.'weixinpl/common/utility_shop.php';
        $shopMessage_Utlity = new shopMessage_Utlity();
        // $fromuser = $this->model->get_weixin_fromuser($user_id);
        // $sql ="SELECT weixin_fromuser from ".WSY_USER.".weixin_users where customer_id='".$this->$customer_id."' and id = '".$user_id."' and isvalid = true";
        // $fromuser  = _mysql_query($sql);
        // var_dump($fromuser);exit;
        $content = "您的排队资格已被取消，详情可查看您队列详情！";
        $str = $shopMessage_Utlity->sendWeixinMessage($customer_id,'oV4YRtwxNR2yYZFOCo0K4cWU2JnY', $content);
    }
}