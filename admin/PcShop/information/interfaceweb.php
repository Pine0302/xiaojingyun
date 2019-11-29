<?php
/**
 * @desc pcshop frontweb
 * @author Administrator
 */
require_once('pcbase.php');
class Pcinterfaceroom extends Pcbase{
	
	//comsort infomation
	public function comsort(){
		$result = [];
		$result['errcode'] = 0;
		$db = Ycc::getInstance()->connect();
		$fieldstr = "id,catename,parent_id,listorder";
		$_query = "select $fieldstr from pc_catetype where isvalid=1 and customer_id='".$this->customer_id."'";
		return $db->query($_query);
	}
	
	//分类信息
	public function sortinfo(){
		$result = [];
		$result['errcode'] = 0;
		$list = $this->comsort();
		foreach($list as $key=>$ve){
		$field1[$key]  = $ve['id'];
		$field2[$key] = $ve['listorder'];}
		array_multisort($field2,SORT_ASC,$field1,SORT_DESC,$list);
		if(empty($list)){
			$result['errcode'] = 1;
			$result['data'] = null;
		}else{
			$result['data'] = self::ltr($list);
		} echo json_encode($result);///
	}
	
	static public function ltr($list,$id='id',$pid='parent_id',$child='next',$root=0){
		$tree = [];$array = [];
		if(!is_array($list))return null;
		if(count($list)==count($list,1))return null;
		foreach($list as $key=>$v){
		$array[$v[$id]] = &$list[$key];}
		foreach($list as $key=>$v){
		$parent_id = $v[$pid];
		if($root==$parent_id){
		$tree[] = &$list[$key];}else
	    {if(isset($array[$parent_id])){
		$parent = &$array[$parent_id];
		$parent[$child][] = &$list[$key];}}}
		return $tree;///
	}
	
	//导航信息
	public function navinfo(){
		$result = [];
		$result['errcode'] = 0;
		$db = Ycc::getInstance()->connect();
		$fieldstr = "id,nav_name,logo,link";
		$_query = "select $fieldstr from pc_navigation where isvalid=1 and customer_id='".$this->customer_id."' order by listorder asc,id desc";
		$_list = $db->query($_query);
		if(!$_list){///
			$result['errcode'] = 1;
			$result['data'] = null;
		}else{
			foreach($_list as &$ve){
			$ve['logo'] = $this->http.$ve['logo'];}
			$result['data'] = $_list;
		} echo json_encode($result);
	}
	
	/**
	 * @desc 产品列表
	 * @param customer_id,cate_id(分类id)
	 */
	public function goodslist(){
		$result = [];
		$result['errcode'] = 0;
		$cate_id = isset($_GET['cate_id'])? trim($_GET['cate_id']):'';
		if(!$cate_id){
			$result['errcode'] = 1;
			$result['data'] = '缺少参数cate_id';
			echo json_encode($result);return;
		} $db = Ycc::getInstance()->connect();
		$fieldstr = "id,title,pic,goods_desc,cate_id";
		$_query = "select $fieldstr from pc_goods where isvalid=1 and customer_id='".$this->customer_id."'";
		$_list = $db->query($_query);
		$_list = Cc::tch($_list,$cate_id);
		if(!$_list){///
			$result['errcode'] = 1;
			$result['data'] = null;
		}else{
			foreach($_list as &$ve){
			$ve['pic'] = $this->http.$ve['pic'];}
			$result['data'] = $_list;
		} echo json_encode($result);///
	}
	
	/**
	 * @desc 产品详情
	 * @param customer_id,goods_id
	 */
	public function goodsinfo(){
		$result = [];
		$result['errcode'] = 0;
		$goods_id = isset($_GET['goods_id'])? trim($_GET['goods_id']):'';
		if(!$goods_id){
			$result['errcode'] = 1;
			$result['data'] = '缺少参数goods_id';
			echo json_encode($result);return;
		} $db = Ycc::getInstance()->connect();
		$fieldstr = "id,title,pic,goods_desc,cate_id,details";
		$_query = "select $fieldstr from `pc_goods` where id='{$goods_id}'";
		$_list = $db->get_one($_query);
		if(!$_list){
			$result['errcode'] = 2;
			$result['data'] = '不存在该产品';
		}else{
			$_list['pic'] = $this->http.$_list['pic'];
			$result['data'] = $_list;
		} echo json_encode($result);///
	}
	
	/**
	 * @desc 生成订单
	 * @param customer_id,goods_id,username,tel
	 */
	public function createorder(){
		$result = [];
		$data = $_POST;
		$result['errcode'] = 0;
		$result['data'] = '下单成功';
		foreach($data as &$ve){
		$ve = htmlentities(trim($ve));}
		if(!$data){
			$result['errcode'] = 1;
			$result['data'] = '缺少参数请求';
			echo json_encode($result);return;
		}
		if(!isset($data['goods_id'])){
			$result['errcode'] = 2;
			$result['data'] = '缺少参数goods_id';
			echo json_encode($result);return;
		}
		if(!preg_match('/^[1-9]*[1-9][0-9]*$/',$data['goods_id'])){
			$result['errcode'] = 3;
			$result['data'] = 'goods_id参数有误';
			echo json_encode($result);return;
		}
		if(!$data['username']){
			$result['errcode'] = 4;
			$result['data'] = '请输入用户名';
			echo json_encode($result);return;
		}
		if(!isset($data['tel'])){
			$result['errcode'] = 5;
			$result['data'] = '请输入手机号码';
			echo json_encode($result);return;
		}
		if(!preg_match('/^1[34578]\d{9}$/',$data['tel'],$j)){
			$result['errcode'] = 6;
			$result['data'] = '请输入正确手机号';
			echo json_encode($result);return;
		} $money = 0;$is_pay = 0;$isvalid = 1;
		$db = Ycc::getInstance()->connect();
		$order_no = date('ymdHi').rand(10000,99999);
		$gid = $data['goods_id'];
		$tel = $data['tel'];
		$batchcode = $order_no;
		$user_name = $data['username'];
		$ordertime = $this->nowtime;
		$remark = 'pc商城订单';
		$customer_id = $this->customer_id;
		$query_insert = "insert into pc_order(gid,tel,money,user_name,batchcode,ordertime,is_pay,isvalid,remark,customer_id) values($gid,'{$tel}',$money,'{$user_name}','{$batchcode}','{$ordertime}',0,1,'{$remark}',$customer_id)";  
		$res_insert = $db->query($query_insert);
		if(!$res_insert){
			$result['errcode'] = 6;
			$result['info'] = $query_insert;
			$result['errquery'] = mysql_error();
			$result['data'] = '下单失败';
		} echo json_encode($result);///
	}
	
	
	
}

$ifsroom = new Pcinterfaceroom();
$ifsroom->$_GET['action']();



