<?php

class control_exchange extends control_base
{
    var $model;
    
    function __construct()
    {
        parent::__construct();  
        
        // parent::check_login();

        require_once('model/exchange.php');
        $this->model = new model_exchange($this->customer_id);
        
        require_once('model/common.php');
        $this->model_common = new model_common();

        $this->user_id = $_SESSION['user_id_'.$this->customer_id];
    }

    //活动列表
    function exchange_activity_list(){         
        parent::check_login();      
        $customer_id       = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);

        /************  自动终止过期活动以及将开始时间大于等于当前时间的已发布活动状态改为进行中 start************/
        $this->in_process_activities();
        $this->end_activities();        
        /************  自动终止过期活动以及将开始时间大于等于当前时间的已发布活动状态改为进行中 end************/

        $param['customer_id']     = $this->customer_id;
        $param['activity_id']     = $_REQUEST['activity_id']?$_REQUEST['activity_id']:-1;        
        $param['activity_name']   = $_REQUEST['activity_name']?$_REQUEST['activity_name']:"";
        $param['starttime']       = $_REQUEST['starttime']?$_REQUEST['starttime']:-1;
        $param['endtime']         = $_REQUEST['endtime']?$_REQUEST['endtime']:-1;
        $param['activity_status'] = $_REQUEST['activity_status']?$_REQUEST['activity_status']:-1;       
        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页

        $res = $this->model->get_exchange_activities($param);
        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];        
        include("view/exchange/activity.php");
    } 

    //发布活动
    function launch_activities(){
        parent::check_login(); 
        $param['customer_id']     = $this->customer_id;
        $param['activity_id']     = $_POST['activity_id']?$_POST['activity_id']:-1;
        $res = $this->model->launch_activities($param);
        echo json_encode($res);
        exit;
    }

    //终止活动
    function end_activities(){
        parent::check_login(); 
        $param['customer_id']     = $this->customer_id;        
        $type            = $_POST['type']?$_POST['type']:-1;//终止活动方式 1手动 -1自动
        if($type==1){
            $param['activity_id']     = $_POST['activity_id'];
        }else{
            $param['activity_id'] = -1;
        }
        $res = $this->model->end_activities($param);
        if($type==1){//手动
            echo json_encode($res);
            exit;
        }        
    }

    //删除活动
    function del_activities(){
        parent::check_login(); 
        $param['customer_id']     = $this->customer_id;
        $param['activity_id']     = $_POST['activity_id']?$_POST['activity_id']:-1;
        $res = $this->model->del_activity($param);        
        echo json_encode($res);
        exit;
    }

    //活动自动切换为进行中状态
    function in_process_activities(){
        parent::check_login(); 
        $param['customer_id']     = $this->customer_id;
        $this->model->in_process_activities($param);        
    }
    
    //添加活动
    public function ex_exchange(){
        parent::check_login(); 
        $customer_id         = $this->customer_id;
        $data['customer_id'] = $this->customer_id;
        $data['id']          = $_REQUEST['id']?$_REQUEST['id']:"";

        if(!empty($data['id'])) {
            $res    = $this->model->get_ex_exchange($data);
        }

        $theme  = $this->model_common->find_theme($customer_id);
        
        include("view/exchange/add_activity.php");
    }

    //添加&修改活动操作
    public function save_exchange(){
        parent::check_login(); 
        $id                         = $_REQUEST['id']?$_REQUEST['id']:"";
        $data['isvalid']            = true;
        $data['title']              = $_REQUEST['title']?$_REQUEST['title']:"";
        $data['title']              = addslashes($data['title']);
        $data['threshold']          = $_REQUEST['threshold']?$_REQUEST['threshold']:"";
        $data['exchange_num']       = $_REQUEST['exchange_num']?$_REQUEST['exchange_num']:"";
        $data['is_superposition']   = true;
        $data['starttime']          = $_REQUEST['starttime']?$_REQUEST['starttime']:"";
        $data['endtime']            = $_REQUEST['endtime']?$_REQUEST['endtime']:"";
        $data['status']             = $_REQUEST['status']?$_REQUEST['status']:"";
        if($_POST['superposition'] != 1) {
            $data['is_superposition'] = false;
        }

        // if( strtotime($data['starttime']) < time() && strtotime($data['endtime']) > time() ) {
        //     $data['status']             = 3;
        // }

        if(empty($id)) {
            $data['customer_id']    = $this->customer_id;
            $data['createtime']     = date('Y-m-d H:i:s',time());

            $res = $this->model->add_ex($data);

            $datas['exchange_id']    = $res['id'];
            $action                 = 1;
        } else {
            $obj['customer_id']     = $this->customer_id;
            $obj['id']              = $id;

            $arr = $this->model->get_ex_exchange($obj);

            $res = $this->model->save_ex($data,$obj);

            $datas['exchange_id']    = $id;
            $action                 = 2;
        }
        
        $datas['customer_id']      = $this->customer_id;
        $datas['threshold']        = $arr['threshold'];
        $datas['exchange_num']     = $arr['exchange_num'];
        $datas['is_superposition'] = $arr['is_superposition'];
        $this->model->add_exchange_logs($datas,$action);

        json_out($res);
    }

    //关联产品列表
    public function exchange_activity_relation(){
        parent::check_login(); 
        $customer_id       = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);

        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页
        $param['activity_id']     = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"";
        $activity_id              = $param['activity_id'];

        // 数据校验
        if( $param['activity_id'] == ''){
            json_out(['errmsg'=>'活动编码不能为空']);
        }

        $data['id']               = $param['activity_id'];
        $data['customer_id']      = $customer_id;

        $arr = $this->model->get_ex_exchange($data);
        
        $res = $this->model->get_relation($param);

        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];
        
        include("view/exchange/relation.php");
    } 

    //添加关联产品列表
    public function get_add_relation(){
        parent::check_login(); 
        $customer_id       = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);

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

        $res = $this->model->get_add_relation($param);

        $type      = $res['type'];
        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];

        include("view/exchange/add_relation.php");
    } 

    //添加关联产品
    public function add_relation(){
        parent::check_login(); 
        $param['idsStr']           = $_REQUEST['idsStr']?$_REQUEST['idsStr']:'';        
        $param['activity_id']      = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"";
        $param['customer_id']      = $this->customer_id;
        $str                       = substr($param['idsStr'],strlen($param['idsStr'])-1,strlen($param['idsStr']));
        if ($str == ',') {
            $param['idsStr']       = substr($param['idsStr'],0,strlen($param['idsStr'])-1);
        }

        $res = $this->model->add_relation($param);
      
        json_out($res);
    }

    //删除关联产品
    public function del_relation(){      
        parent::check_login(); 
        $param['activity_id']      = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"";
        $param['pid']              = $_REQUEST['pid']?$_REQUEST['pid']:"";
        $param['customer_id']      = $this->customer_id;
        
        $res = $this->model->del_relation($param);

        json_out($res);
    }

    //修改关联产品
    public function save_relation(){
        parent::check_login(); 
        $param['activity_id']      = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"";       
        $param['obj']              = $_REQUEST['obj']?$_REQUEST['obj']:"";
        $param['id']               = $_REQUEST['id']?$_REQUEST['id']:"";
        $param['str']              = $_REQUEST['str']?$_REQUEST['str']:"";
        $param['customer_id']      = $this->customer_id;

        $res = $this->model->save_relation($param);

        json_out($res);
    }

    // 获取满赠活动商品列表
    public function get_exchange_products(){
        $exchange_id    = (int)$_REQUEST['exchange_id'];
        $search    = $_REQUEST['search'];

        // 数据校验
        if( $exchange_id <= 0 ){
            $this->error_ajax('请输入正确的满赠活动编号');
        }

        $data = $this->model->get_exchange_products($exchange_id,$search);
        // var_dump($data,$exchange_id);
        $this->success_ajax($data);
    }

    // 满赠专区
    public function full_give(){
        $is_diy_menu = $_REQUEST['is_diy_menu'] ? : 0; //是否从自定义链接进入
        $customer_id = $_REQUEST['customer_id']; 
        $search      = addslashes(trim($_REQUEST['search']));
        $price = isset($_REQUEST['price']) ? (float)$_REQUEST['price'] : $_SESSION['exchange_price'] ; 
        $_SESSION['exchange_price'] = $price;
        $price = $_SESSION['exchange_price'] ? $_SESSION['exchange_price'] : $price;
        $price = $is_diy_menu || $_REQUEST['price'] == -1 ? -1 : $price ; 
        $exchange_activities = $this->model->get_order_exchange_activities($price);
        // var_dump($exchange_activities);
        $exchange_count = count($exchange_activities);
        if($exchange_count==0){//暂无活动信息
            $url = "/weixinpl/mshop/errors.php?customer_id=".$customer_id."&msg=暂无活动信息";
            echo "<script>location.href = '$url'</script>";
            die;
        }
        if($_REQUEST['keyid']){
            $keyid = $_REQUEST['keyid'];
        }else{
            $keyid = $exchange_activities[0]['id'];
        }

        $exchange_products = $this->model->get_exchange_products($keyid,$search);
        include("view/exchange/full_give.php");
    }

    // 获取产品属性
    public function get_product_pro(){
        $propertyids = $_REQUEST['propertyids'] ? : 0;
        $product_id = (int)$_REQUEST['pid'] ? : 0;
        $ex_id = (int)$_REQUEST['eid'] ? : 0;

        $propertyids = str_replace('_', ',', $propertyids);
        $param = array('customer_id' => $this->customer_id ); 
        $products = $this->model('commonshop_products',$param);
        $product_pro = $products->get_product_pro($propertyids,$product_id);

        $product_detail = $this->model->get_exchange_product_detail($ex_id,$product_id);
        $product_detail['pro'] = $product_pro;
        $this->success_ajax($product_detail);
    }

    public function get_product_pro_html(){
        $propertyids = $_REQUEST['propertyids'] ? : 0;
        $product_id = (int)$_REQUEST['pid'] ? : 0;
        $ex_id = (int)$_REQUEST['eid'] ? : 0;

        $propertyids = str_replace('_', ',', $propertyids);
        $param = array('customer_id' => $this->customer_id ); 
        $products = $this->model('commonshop_products',$param);
        $product_pro = $products->get_product_pro($propertyids,$product_id);

        $product_detail = $this->model->get_exchange_product_detail($ex_id,$product_id);
        $product_detail['pro'] = $product_pro;
        $html .= '<div class="attr-bg" onclick="off_attr();"></div>';
        $html .= '<div class="attr-main">';

        $html .= '<div class="attr-head flex">';
        $html .= '<div class="img-box"><img src="'.$product_detail['default_imgurl'].'" class="img"/></div>';
        $html .= '<div class="details">';
        $html .= '<p class="title ellipsis">' . $product_detail['name'] . '</p>';
        $html .= '<span class="price">';
        if(OOF_P != 2) $html .= OOF_S;
        $html .= $product_detail['exchange_price'];
        if(OOF_P == 2) $html .= OOF_S;
        $html .= '</span>';
        $html .= '</div>';
        $html .= '<div class="off-btn" onclick="off_attr();"><img src="/weixinpl/mshop/images/close.png"/></div>';
        $html .= '</div>';

        $html .= '<div class="attr-list" >';
        foreach ($product_pro as $key => $value) {
            $html .= '<div class="list" >';
                $html .= '<p class="name">'.$value['name'].'：</p>';
                $html .= '<ul class="column" >';
                    foreach ($value['chi'] as $key => $val) {
                        $html .= '<li data-id=' . $val['id'] . '>' . $val['name'] . '<i class="skin-bg"><img src="/weixinpl/mshop/images/checked.png"></i></li>';
                    }
                $html .= '</ul>';
            $html .= '</div>';
        }
        $html .= '</div>';

        $html .= '<div class="num-box flex">';
        $html .= '<span class="name">数量：</span>';
        $html .= '<div class="change-num">';
        $html .= '<button class="reduce">-</button>';
        $html .= '<input onblur="modify(this);" id="pd-num" type="text" value="1" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)">';
        $html .= '<button class="add">+</button>';
        $html .= '</div>';
        $html .= '<span class="stock">库存:' .$product_detail['storenum']. '</span>';
        $html .= '</div>';

        $html .= '<button class="attr-submit skin-bg" onclick="confirms()">确认</button>';

        $html .= '</div>';
        echo $html;
        // $this->success_ajax($product_detail);
    }

    // 我的换购
    public function my_traded(){
        $exchange_list = $_SESSION['exchange_'.$this->user_id];
        $search      = addslashes(trim($_REQUEST['search']));
        foreach ($exchange_list as $key => $value) {
            $pid = $value[1][0];
            $ex_id = $value[1][8];
            $count = $value[1][2];
            $exchange_products[$key] = $this->model->get_exchange_products($ex_id,$search,$pid)[0];
            $exchange_products[$key]['count'] = $value[1][2];
            $price = $exchange_products[$key]['exchange_price'];
            $total_price += (float)$price * (int)$count;
            $total_count += (int)$count;
        }
        $exchange_activities = $this->model->get_order_exchange_activities($_SESSION['exchange_price']);
        include("view/exchange/my_traded.php");
    }

    // ajax获取已选换购产品列表
    function get_traded(){
        $exchange_list = $_REQUEST['exchange_list'];
        $search      = addslashes(trim($_REQUEST['search']));

        $param = array('customer_id' => $this->customer_id ); 
        $products = $this->model('commonshop_products',$param);
        foreach ($exchange_list as $key => $value) {
            $pid = $value[1][0];
            $ex_id = $value[1][8];
            $count = $value[1][2];
            $exchange_product = $this->model->get_exchange_products($ex_id,$search,$pid);
            $price = $exchange_product[0]['exchange_price'];
            $exchange_product[0]['count'] = $value[1][2];
            $exchange_product[0]['pros'] = $value[1][1];
            $pro = str_replace('_', ',', $value[1][1]);
            if( $pro ){
                $exchange_product[0]['pro'] = $products->get_pro_name($pro);
            }
            if( count($exchange_product[0]) > 5 ){
                $exchange_products[] = $exchange_product[0];
            }
            $total_price += (float)$price * (int)$count;
            $total_count += (int)$count;
        }
        $data['exchange_list'] = $exchange_products;
        $data['total_price'] = $total_price ? : 0;
        $this->success_ajax($data);
    }

    function save_exchange_list(){
        $exchange = $_REQUEST['exchange'];
        $_SESSION['exchange_'.$this->user_id] = $exchange;
    }

    function get_fist_exchange_detail(){
        $price = $_REQUEST['price'];
		if (! preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $price)) {
			$this->error_ajax("价格参数有误!");
		}
        $exchange_activities = $this->model->get_order_exchange_activities($price,1);
        if( $exchange_activities ) {
            foreach ($exchange_activities as $key => $value) {
                if( $value['exchange_num'] > $count_ex || $value['exchange_num'] == -1 ){
                    $count_ex = $value['exchange_num'];
                }
            }
            $count_ex_str = $exchange_activities[0]['exchange_num']==-1 ? '可换购件数不限制' : '可换购' . $exchange_activities[0]['exchange_num'] . '件';
        }
        $exchange['threshold'] = $exchange_activities[0]['threshold'];
        $exchange['count_ex_str'] = $count_ex_str;
        $this->success_ajax($exchange);
    }

    function text(){
        $param = array('customer_id' => $this->customer_id ); 
        $products = $this->model('commonshop_products',$param);
        $product_pro = $products->get_pro_name('1252,1907');
        var_dump($product_pro);
        return $product_pro;     
    }

    public function error_ajax($msg,$code=-1){
        $res['msg'] = $msg;
        $res['cod'] = $code;
        echo json_encode($res);
        die;
    }

    public function success_ajax($data,$code=0){
        if( !$data ){
            $this->error_ajax('没有相关数据!');
        }else{
            $res['data'] = $data;
            $res['cod'] = $code;
            echo json_encode($res);
            die;
        }
    }

    public function model($name,$arr){
        // 引入对应model类并实例化
        $className  = 'model_'.$name;
        $fileName   = 'model/'.$name.'.php';

        if( file_exists($fileName) ){
            include_once($fileName);
            if (count($arr) > 0){
                $class = new ReflectionClass($className);
                $model = $class->newInstanceArgs($arr);
            }else{
                $model = new $className();
            }
        }
        return $model;
    }

    //操作日志列表
    public function operation_log(){
        $theme  = $this->model_common->find_theme($this->customer_id);
        $pageNum = 1;
        if($_REQUEST['pagenum']){
            $pageNum = $_REQUEST['pagenum'];
        }
        $param['pageNum'] = $pageNum;
        $param['customer_id'] = $this->customer_id;
        $res = $this->model->get_operation_log($param);
        $pageCount = $res['pageCount'];
        $data = $res['log_arr'];
        include_once("view/exchange/operation_log.php");
    }
}
