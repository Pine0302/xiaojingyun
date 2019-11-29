<?php


class control_promoter_card extends control_base 
{
	public $model;
	public $model_common;
    public $theme;
	function __construct() 
	{
		parent::__construct();
		require_once('model/promoter_card.php');
		$this->model = new model_promoter_card();
		require_once('model/common.php');
        $this->model_common = new model_common();
		
		parent::check_login();
        $data['data']=file_get_contents('php://input', true);
		//$data = $_REQUEST['data'];
		$this->parmdata  = json_decode($data['data'],true);
        $customer_id = $this->customer_id;
        require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/proxy_info.php');
        //查询主题
        $this->theme     = $this->model_common->find_theme($this->customer_id);
//        var_dump($theme);

		
    }

   /*
    * 获取名片规则页面
    * $Author: hjw$
    * $2018-05-16  $
    * 参数：
    */
    public function get_card_setting(){
    	$data  = $_POST;
        $data['customer_id']       = $this->customer_id;
        $data['customer_id_en']    = $this->customer_id_en;
        $shareholder_name = $this->model->get_shareholder_name($data);
        $result = $this->model->get_card_setting($data);
        if($result){
        	$result['pass_level'] = explode('_',$result['pass_level']);
        }
        include('view/promoter_card/card_rule.html');
    }
   /*
    * 保存名片规则
    * $Author: hjw$
    * $2018-05-17  $
    * 参数：
    */
    public function save_card_setting(){
        $data  = $_POST;
        if(!isset($data['pass_level']) || empty($data['pass_level'])){
            $data['pass_level'] = 0;
        }
        if(!isset($data['selector_id']) || empty($data['selector_id'])){
            $data['selector_id'] = -1;
        }        
        if(!isset($data['name_onoff']) || empty($data['name_onoff'])){
            $data['name_onoff'] = 0;
        }       
        if(!isset($data['level_onoff']) || empty($data['level_onoff'])){
            $data['level_onoff'] = 0;
        }       
        if(!isset($data['address_onoff']) || empty($data['address_onoff'])){
            $data['address_onoff'] = 0;
        }        
        if(!isset($data['weixin_onoff']) || empty($data['weixin_onoff'])){
            $data['weixin_onoff'] = 0;
        }
        if(!isset($data['qq_onoff']) || empty($data['qq_onoff'])){
            $data['qq_onoff'] = 0;
        }
        if(!isset($data['phone_onoff']) || empty($data['phone_onoff'])){
            $data['phone_onoff'] = 0;
        }
        if(!isset($data['tip_onoff']) || empty($data['tip_onoff'])){
            $data['tip_onoff'] = 0;
        }
        if(!isset($data['introduce_onoff']) || empty($data['introduce_onoff'])){
            $data['introduce_onoff'] = 0;
        }
        if(!isset($data['follow_onoff']) || empty($data['follow_onoff'])){
            $data['follow_onoff'] = 0;
        }
        $data['customer_id']       = $this->customer_id;
        $data['customer_id_en']    = $this->customer_id_en;
        $result = $this->model->save_card_setting($data);
        json_out($result);
    }
}