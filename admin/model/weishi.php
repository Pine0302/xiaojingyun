<?php



class model_weishi{
    var $db;
    var $model_common;
    function __construct()
    {
        $this->db = DB::getInstance();
        require_once('model/common.php');
        $this->model_common = new model_common();
    }

	/*
     * 查询name、logo
     * $Author: mzj$
     * $2018-05-15  $
     */
    public function shop_information($data){
    	$customer_id = $data;
    	if(empty($customer_id)){
    		return false;
    	}
    	$sql = "select oi.name,oi.logourl from customers as c 
    	inner join oem_infos as oi on c.adminuser_id=oi.adminuser_id  
    	where c.id='{$customer_id}'";
    	$res = $this->db->getRow($sql);
    	return $res;
    }

    /*
     * 插入
     * $Author: mzj$
     * $2018-05-15  $
     */
    public function link_information($data){
    	$sql_insert = "insert into ".WSY_PUB.".api_secret_config (appid,appsecret,add_time,custid,api_domain,type,ext_info) 
    	                values('1','{$data['appsecret']}','{$data['add_time']}','{$data['custid']}','{$data['ws_url']}','1','{$data['ext_info']}')";
        $res_insert = $this->db->query($sql_insert);
        return $data;
    }

    /*
     * 修改
     * $Author: mzj$
     * $2018-05-26  $
     */
    public function edit_information($data){
    	if(count($data)==0){
    		return false;
    	}
    	$where   = "custid=".$data['custid']."";
    	$res = $this->db->autoExecute(WSY_PUB.'.api_secret_config',$data,'update',$where);
    	return $res;
    }

    /*
     * 查询绑定信息
     * $Author: mzj$
     * $2018-05-15  
     */
    public function suc_information($data){
    	$customer_id = $data;
    	if(empty($customer_id)){
    		return false;
    	}
    	$sql = "select appsecret,custid,api_domain,ext_info from ".WSY_PUB.".api_secret_config where custid=". $customer_id ."";
    	$res = $this->db->getRow($sql);
    	return $res;
    }

    /*
     * 删除绑定信息
     * $Author: mzj$
     * $2018-05-17  $
     */
    public function del_information($data){
    	if(!empty($data)){
    		$sql = "delete from ".WSY_PUB.".api_secret_config where custid=".$data."";
    		$res = $this->db->query($sql);
    		return $res;
    	}
    }

}