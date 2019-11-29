<?php
/**
 * @desc pcshop admin
 * @author Administrator
 */
require_once('pcbase.php');
class Pcshop extends Pcbase{
	//分类信息
	public function comtype(){
		$db = Ycc::getInstance()->connect();
		$strfield = 'id,catename,parent_id,level,listorder';
		$sql = "select $strfield from pc_catetype where isvalid=1 and customer_id='".$this->customer_id."' order by listorder asc,id desc";
		return $db->query($sql);
	}
	//分类列表
	public function typeinfo(){
		return Cc::shl(Cc::lt($this->comtype()));
	}
	//产品类别
	public function catetype(){
		return Cc::sh2(Cc::lt($this->comtype()));
	}
	
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
		return self::ltr($list);
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
	
	//获取主题
	public function get_theme(){
		$style = 'blue';//default
		$db = Ycc::getInstance()->connect();
		$list_theme = $db->get_one("select theme from customers where isvalid=1 and customer_id={$this->customer_id}");
		return $list_theme['theme']? 'content'.$list_theme['theme']:'content'.$style;///
	}
	
	//获取导航信息
	public function nav_info(){
		$result = []; $show = 10;
		$result['errcode'] = 0;
		require_once('lib/Page.class.php');
		$db = Ycc::getInstance()->connect();
		$count_query = "select count(*) as totalRows from pc_navigation where isvalid=1 and customer_id='".$this->customer_id."'";
		$listRows = $db->get_one($count_query);
		$totalRows = $listRows['totalRows'];
		$page = new Page($totalRows,$show);
		$strPage = $page->pageInfo("&customer_id=".$this->customer_id_en);
		$fieldstr = "id,nav_name,logo,link,listorder";
		$_query = "select $fieldstr from pc_navigation where isvalid=1 and customer_id='".$this->customer_id."' order by listorder asc,id desc limit {$page->firstRow},{$show}";  
		$_list = $db->query($_query);
		if(!$_list){///
			$result['errcode'] = 1;
			$result['data'] = null;
		}else{
			foreach($_list as &$ve){
			$ve['logo'] = $this->http.$ve['logo'];}
			$result['data'] = $_list;
			$result['currentPage'] = $page->page;
			$result['pageNums'] = $page->pageNums;
			$result['strPage'] = $strPage;
		} return $result;
	}
	
	//获取最新图片
	public function getLastNewPic(){
		$pic = '';
		$db = Ycc::getInstance()->connect();
		$pic_query = "select pic from pc_goods order by id desc limit 1";
		$pic_list = $db->get_one($pic_query);
		if(!empty($pic_list['pic']))
		$pic = $this->http.$pic_list['pic'];
		return $pic;///
	}
	
	//订单列表
	public function listorder(){
		$result = [];$show = 12;
		$result['errcode'] = 0;
		$result['data'] = '';
		require_once('lib/Page.class.php');
		$db = Ycc::getInstance()->connect();
		$query_order_c = "select count(*) as totalRows from `pc_order` where isvalid=1 and customer_id='{$this->customer_id}'";
		$listRows = $db->get_one($query_order_c);
		$totalRows = $listRows['totalRows'];
		$page = new Page($totalRows,$show);
		$strPage = $page->pageInfo("&customer_id=".$this->customer_id_en);
		$strfield = "o.id,o.gid,o.batchcode,o.user_name,o.tel,o.is_pay,g.title,g.pic,g.cate_id,o.ordertime,c.catename";
		$query_order = "select $strfield from pc_order o inner join pc_goods g on o.gid=g.id and o.isvalid=1 and o.customer_id=$this->customer_id inner join pc_catetype c on c.id=g.cate_id where g.isvalid=1 and g.customer_id=$this->customer_id";   
		$listorder = $db->query($query_order);
		$listorder = array_slice($listorder,$page->firstRow,$page->show);
		if(!$listorder){
			$result['errcode'] = 1;
		}else{
			$listorder = array_map(function($data){
			$data['pic'] = $this->http.$data['pic'];return $data;
			},$listorder); 
			$result['data'] = $listorder;
			$result['currentPage'] = $page->page;
			$result['pageNums'] = $page->pageNums;
			$result['strPage'] = $strPage;
		} return $result;///
	}
	
	//导航名称信息 
	public function singlenavinfo($nav_id){
		if(!$nav_id)return false;
		$db = Ycc::getInstance()->connect();
		$query_nav = "select * from `pc_navigation` where id='{$nav_id}'";
		$listnav = $db->get_one($query_nav);
		if(!$listnav)return false;
		return $listnav;
	}
	
	//导航信息all
	public function navinfoall(){
		$db = Ycc::getInstance()->connect();
		$fieldstr = "id,nav_name,logo,link,listorder";
		$_query = "select $fieldstr from pc_navigation where isvalid=1 and customer_id='".$this->customer_id."' order by listorder asc,id desc";
		$_list = $db->query($_query);
		if(!$_list)return false;
		return $_list;///
	}
	
	


}



