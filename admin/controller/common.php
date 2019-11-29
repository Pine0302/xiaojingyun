<?php
/*
* 套餐相关
* $Author: 痴心绝对 $
* 2015-01-11  $
*/

class control_common extends control_base {
	var $model;
    
	function __construct() {     
		parent::__construct();
        require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/common/utility.php');
		require_once('model/common.php');
		$this->model = new model_common();
    }
    
    
	function get_products(){
        
        $parm   = $this->parmdata;
        $parm['cust_id']  = $this->customer_id;
        //模拟数据
    	/* $parm['customer_id']  = 3243;
        $parm['search_pid']   = $_POST['search_pid'];
		$parm['search_pname'] = $_POST['search_pname'];
		$parm['search_ptype'] = $_POST['search_ptype'];
		$parm['page']   = '1';
        $parm['count']   = '3'; */
        $parm['field']     = 'id,id as product_id,default_imgurl,name,type_ids,orgin_price,now_price,storenum';

		$result = $this->model->get_products($parm);
        json_out($result);
        
	}
    
    function get_product_pros(){
        
        $parm['pid']  = $_POST['pid'];
        $parm['customer_id']  = $_POST['customer_id'];
        
        //模拟数据
    	$parm['pid']  = 10;
        $parm['customer_id']  = 3243;

		$result = $this->model->get_product_pros($parm);
        var_dump($result);
        return $result;
	}

}
