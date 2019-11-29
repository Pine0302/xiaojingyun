<?php
header("Content-type: text/html; charset=utf-8");

class control_change_of_change extends control_base
{
    var $model;

    function __construct()
    {

        parent::__construct();
        require_once('model/change_of_change.php');
        $this->model = new model_change_of_change();
        require_once('model/common.php');
        $this->model_common = new model_common(); 
        if(empty($_SESSION["C_id"])){
           echo "<script>alert('登录已经超时，请重新登录！');</script>";
           exit;
        }
    }

    /*
    版权信息:  秘密信息
    功能描述：零钱转赠页面
    开 发 者：wuzepeng
    开发日期：2018-09-20
    @param 
    重要说明：无
     */
    public function money_bag_change_setting()
    {
    	$customer_id    = $this->customer_id;
    	$customer_id_en = $this->customer_id_en;
    	$theme          = $this->model_common->find_theme($customer_id);
    	$data           = $this->model->money_bag_change_setting($customer_id);
    	extract($data);
    	$type = explode('_',$type);
    	include_once('view/change_of_change/money_bag_change_setting.php');
    }
    /*
    版权信息:  秘密信息
    功能描述：零钱转区块链积分页面
    开 发 者：wuzepeng
   	开发日期：2018-09-20
    @param 
    重要说明：无
     */
    public function money_bag_change_block_chain_setting()
    {
    	$customer_id    = $this->customer_id;
    	$customer_id_en = $this->customer_id_en;
    	$theme          = $this->model_common->find_theme($customer_id);
    	$data           = $this->model->money_bag_change_block_chain_setting($customer_id);
    	extract($data);
    	include_once('view/change_of_change/money_bag_change_block_chain_setting.php');
    }
    /*
    版权信息:  秘密信息
    功能描述：保存转赠数据
    开 发 者：wuzepeng
  	开发日期：2018-09-20
    @param 
    重要说明：无
     */
    public function save_money_bag_change_setting()
    {
    	$param['customer_id']    = $this->customer_id;
    	$param['type']           = implode('_',$_POST['type']);
    	$param['transfer_onoff'] = addslashes($_POST['transfer_onoff']);
    	$param['min_money']      = addslashes($_POST['min_money']);
    	$param['remark']         = addslashes($_POST['remark']);
    	$param['op']             = addslashes($_POST['op']);
    	$result = $this->model->save_money_bag_change_setting($param);
    	json_out($result);
    }

    /*
    版权信息:  秘密信息
    功能描述：保存零钱转区块链设置数据
    开 发 者：wuzepeng
  	开发日期：2018-09-20
    @param 
    重要说明：无
     */
    public function save_change_block_chian_setting()
    {
        $param['customer_id']    = $this->customer_id;
        $param['type']           = addslashes($_POST['type']);
    	$param['block_onoff']    = addslashes($_POST['block_onoff']);
    	$param['proportion']     = addslashes($_POST['proportion']);
    	$param['min_money']      = addslashes($_POST['min_money']);
    	$param['remark']         = addslashes($_POST['remark']);
    	$param['op']             = addslashes($_POST['op']);
    	$result = $this->model->save_change_block_chian_setting($param);
    	json_out($result);
    }
}//类结束