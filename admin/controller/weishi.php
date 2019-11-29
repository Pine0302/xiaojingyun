<?php


class control_weishi extends control_base {
    var $model;
    var $model_common;
    private $log_ope;

    function __construct() {
        parent::__construct();
        require_once('model/weishi.php');
        require_once('model/common.php');

        require_once (ROOT_DIR.'mp/lib/http.php');
        require_once (ROOT_DIR.'mp/lib/LogOpe.php');

        $this->model = new model_weishi();
        $this->model_common = new model_common();
        // require_once('/weixinpl/common/common_ext.php');
        parent::check_login();
        $data['data'] = file_get_contents('php://input', true);
        //$data = $_REQUEST['data'];
        $this->parmdata = json_decode($data['data'], true);

    }

    /**
     * 调用接口curl
     * @param $url
     * @param $data
     * @param $is_array
     * @return array|bool|string
     * @throws Exception
     */
    public function request($url, $data,$is_array = 0) {
        $http = new http();
        if ($is_array == 0){
            $content = $http->send($url, json_encode($data));
        }else{
            $content = $http->send($url, $data);
        }

        return $content;
    }


    /*
    版权信息:  秘密信息
    功能描述：知识付费——绑定页面
    开 发 者：mzj
    开发日期： 2018-05-15
    重要说明：无
     */
    public function weishi_link() {
    	$ws_url = $_GET['url'];
    	$server_id = $_GET['server_id'];
        $customer_id = $this->customer_id;
        $res=$this->model->suc_information($customer_id);
        // print_r($res);exit;
        if($customer_id==$res['custid']&&empty($ws_url)&&empty($server_id)){
        	$res['ext_info'] = json_decode($res['ext_info'],TRUE);
    		include('view/weishi/suc_link.html');
        }else{
        	include('view/weishi/weishi_link.html');
        }
    }

    /*
    版权信息:  秘密信息
    功能描述：知识付费——绑定ajax
    开 发 者：mzj
    开发日期： 2018-05-15
    重要说明：无
     */
    public function add_link() {
        header("Content-type: text/html; charset=utf-8");
        $data['token'] = md5($_POST['weishi_token']);
        $data['server_id'] = $_POST['weishi_server'];
        $data['shop_url'] = Protocol."$_SERVER[HTTP_HOST]";
        $weishi_url = $_POST['weishi_url'];
        $data['custid'] = $this->customer_id;
        $data['is_lock'] = '1';
        if(!empty($data['custid'])) {
            $res   = $this->model->shop_information($data['custid']);
            if(!empty($res['logourl']) && !empty($res['name'])) {
                // array('errcode' => 0,'errormsg'=>'获取数据成功！','res'=>$res);
                $data['shop_name'] = $res['name'];
                $data['shop_logo'] = $res['logourl'];
            } else if(empty($res['logourl'])){
                json_out(array('errcode' => 400,'errormsg'=>'获取logo失败'));
            } else {
                json_out(array('errcode' => 400,'errormsg'=>'获取名称失败'));
            }
        }
        $res_edit = $this->model->suc_information($data['custid']);		//判断是否已经绑定
        if($res_edit){
        	$res_edit['ext_info'] = json_decode($res_edit['ext_info'],true);
        	if(md5($res_edit['appsecret'])==$data['token']&&$res_edit['custid']==$data['custid']&&$res_edit['api_domain']==$weishi_url&&$res_edit['ext_info']['server_id']==$data['server_id']){
        		json_out(array('errcode' => 0,'errormsg'=>'已经绑定'));
        	}
        }
        $content = $this->request($weishi_url.'/train/index.php/Home/Api2/shop_account', $data,1);//绑定微云视
        $content = json_decode($content,TRUE);
        if($content['status']!=1){
            json_out(array('errcode' => 400,'errormsg'=>'绑定失败'));
        }else{
        	$data['appsecret'] = $_POST['weishi_token'];
        	$data['ws_url'] = $weishi_url;
        	$data['add_time'] = date("Y-m-d H:i:s");

        	$ext_info['ws_name'] = $content['ws_name'];
            $ext_info['ws_logo'] = $content['ws_logo'];
            $ext_info['weixin_pubqr'] = $content['weixin_pubqr'];
            $ext_info['server_id'] = $data['server_id'];

            $data["ext_info"] = json_encode($ext_info,JSON_UNESCAPED_UNICODE);
            if($res_edit){
            	$res = $this->model->edit_information($data);
            }else{
            	$res = $this->model->link_information($data);
            }

        	if($res){
				json_out(array('errcode' => 0,'errormsg'=>'绑定成功'));
        	}else{
        		json_out(array('errcode' => 400,'errormsg'=>'数据错误'));
        	}
        }
    }

    /*
    版权信息:  秘密信息
    功能描述：知识付费——解绑ajax
    开 发 者：mzj
    开发日期： 2018-05-17
    重要说明：无
     */
    public function del_link(){
    	$customer_id = $this->customer_id;
    	$data['token'] = md5($_POST['token']);
        $data['server_id'] = $_POST['server_id'];
        // $data['weishi_url'] = $_POST['ws_url'];
        $data['is_lock'] = '2';
        $weishi_url = $_POST['ws_url'];
    	if(!empty($customer_id)){
    		$res  = $this->model->suc_information($customer_id);
    		if(!empty($res)){
    			$content = $this->request($weishi_url.'/train/index.php/Home/Api2/shop_account', $data,1);
		        $content = json_decode($content,TRUE);
                $res  = $this->model->del_information($customer_id);
                if($res){
                    json_out(array('errcode' => 0,'errormsg'=>'获取信息成功'));
                }
            }
        }else{
            json_out(array('errcode' => 400,'errormsg'=>'没有绑定'));
        }
    }


    /*
    版权信息:  秘密信息
    功能描述：绑定账号页面判断是否绑定微视
    开 发 者：wzj
    开发日期： 2018-06-06
    重要说明：无
    */
    public function ajax_is_link() {
        /*判断是否绑定微视 s*/
        require_once (ROOT_DIR."/wsy_user/public/weishi_common.php");
        $ws_common = new weishi_common($this->customer_id);
        //判断是否绑定
        if ($ws_common->check_ws()) {
            //绑定
            json_out(array('errcode' => 0,'errormsg'=>'已绑定微视', 'data'=>$ws_common->weishi_data));
        } else {
            //未绑定
            json_out(array('errcode' => 400,'errormsg'=>'未绑定微视'));
        }
    }


    /*
        版权信息:  秘密信息
        功能描述：微视信息页面
        开 发 者：wzj
        开发日期： 2018-06-06
        重要说明：无
        */
    public function weishi_page() {
        $url = Protocol . $_SERVER['HTTP_HOST'] .'/mshop/admin/index.php?m=weishi&a=weishi_link&customer_id='.$this->customer_id;
        /*获取微视信息微视 s*/
        require_once (ROOT_DIR."/wsy_user/public/weishi_common.php");
        $ws_common = new weishi_common($this->customer_id);
        $ws_common->check_ws();
        $weishi_data = $ws_common->weishi_data;
        include('view/weishi/weishi_page.html');
    }


}
