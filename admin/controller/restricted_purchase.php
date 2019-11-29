<?php

/*
版权信息:  秘密信息
功能描述：限购活动数据操作
开 发 者：黄泽钦
开发日期： 2017-08-29
重要说明：无
 */
class control_restricted_purchase extends control_base
{
	var $model;

    function __construct()
	{
		parent::__construct();
		require_once('model/restricted_purchase.php');
		$this->model = new model_restricted_purchase();
		
		parent::check_login();

        $data['data']=file_get_contents('php://input', true);
		//$data = $_REQUEST['data'];
		$this->parmdata  = json_decode($data['data'],true);

    }


	function index(){
		$customer_id =  $this->customer_id;
		echo $customer_id;
		echo 123;
		$theme = "blue";
		include('view/activityList.htm');
		//仍未处理步骤 ： 1.没有获取customer_nanme 
	}

	/*
	 * 功能描述：查询限购活动列表
	 * 搜索条件，形式：array('title'=>'','activity_id'=>'','isout'=>'-1');
	 * $Author: huangzeqin $
	 * 2017-08-29  $
	 */
	function get_activity_list(){
	//	echo $this->customer_id;
        $data['customer_id']  = $this->customer_id;      //商家ID
    //   $data['search_key']   = $_POST['search_key'];   //搜索条件，形式：array('title'=>'','activity_id'=>'','isout'=>'-1');
        $data['page']         = $_GET['pagenum']?$_GET['pagenum']:1;
        $data['page_size']    = (int)10;

		$title       = $_GET['search_title']?$_GET['search_title']:"";
		$activity_id = $_GET['search_activity_id']?$_GET['search_activity_id']:"";
		$isout       = $_GET['search_isout'];
		if($isout == ""){
			$isout = -1;
		}
        $search_key = array('title'=>$title,'activity_id'=>$activity_id,'isout'=>$isout);
        $data['search_key'] = json_encode($search_key);
        //判断数据是否安全
        if(empty($data['customer_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
        }
        if(empty($data['search_key']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
        }
        if(empty($data['page_size']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'page_size参数丢失！'));
        }else if($data['page_size'] < 1){
            json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
        }
        $result = $this->model->m_get_activity_list($data);
		
		//查看主题颜色
		$theme = $this->model->find_theme($this->customer_id);

		include('view/restricted_purchase/activityList.htm');
        //json_out($result);
	}

    /*
	 * 改变活动状态
	 * 发布活动 op=publish 终止活动 op=stop 删除活动 op = del
	 * 传入参数($data = array('activity_id'=>'活动编号','customer_id'=>'商家编号','op'=>'活动操作'))
	 * $Author: huangzeqin $
	 * 2017-08-29  $
	 */
	 function change_activity($data = array()){
        $data['customer_id']      = $this->customer_id;    	   //商家ID
		$data['activity_id']      = $_POST['activity_id'];     //活动编号
		$data['op']				  = $_POST['op']; 			   //活动操作
		
		if(empty($data['customer_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
        }
		if(empty($data['activity_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'activity_id参数丢失！'));
        }
		if(empty($data['op']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'op参数丢失！'));
        }
		$result = $this->model->m_change_activity($data);
		switch($data['op']){
			case 'publish':
				$result != false ? $res = array('errcode' => 0,'errmsg'=>'发布成功') : $res = array('errcode' => 400,'errmsg'=>'发布失败');
				break;
			case 'stop':
				$result != false ? $res = array('errcode' => 0,'errmsg'=>'终止成功') : $res = array('errcode' => 400,'errmsg'=>'终止失败');
				break;
			case 'del':
				$result != false ? $res = array('errcode' => 0,'errmsg'=>'删除成功') : $res = array('errcode' => 400,'errmsg'=>'删除失败');
				break;
		}
    	json_out($res);


	 }
	 
	/*
	 * 新建活动
	 * 传入参数$data = 		array('title'=>'活动名称','customer_id'=>'商家编号','isvalid'=>'有效值','time_start'=>'活动开始时间','time_end'=>'活动结束时间','is_auto'=>'是否自动收货','is_commission'=>'是否分佣','is_refund'=>'是否开启退款','is_refund_good'=>'是否开启退货','is_exchange'=>'是否开启换货')
	 * $Author: huangzeqin $
	 * 2017-08-29  $
	 */
	 function create_activity(){
        $data['customer_id']      = $this->customer_id;     	//商家编号
		$data['isvalid']		  = true; 	   					//有效值

		$data['title']			  = $_POST['title']?$_POST['title']:"";  		  	   //活动名称
		$data['time_start']		  = $_POST['time_start']?$_POST['time_start']:"";	   //活动开始时间
		$data['time_end']		  = $_POST['time_end']?$_POST['time_end']:"";		   //活动结束时间
		$data['createtime']		  = date('Y-m-d H:i:s',time());				      	   //创建时间
		if($_POST['is_auto'] == '0' || $_POST['is_auto'] == ""){
			$is_auto = false;
		}else if($_POST['is_auto'] == '1'){
			$is_auto = true;
		}
		if($_POST['if_refund'] == '0' ){
			$if_refund = false;
		}else if($_POST['if_refund'] == '1' || $_POST['if_refund'] == ""){
			$if_refund = true;
		}
		if($_POST['if_return_pro'] == '0' ){
			$if_return_pro = false;
		}else if($_POST['if_return_pro'] == '1' || $_POST['if_return_pro'] == ""){
			$if_return_pro = true;
		}
		if($_POST['change_pro'] == '0' ){
			$change_pro = false;
		}else if($_POST['change_pro'] == '1' || $_POST['change_pro'] == ""){
			$change_pro = true;
		}
		if($_POST['if_display_time'] == '0' ){
			$if_display_time = false;
		}else if($_POST['if_display_time'] == '1' || $_POST['if_display_time'] == ""){
			$if_display_time = true;
		}
		$display_time_count_down = $_POST['display_time_count_down'];
		$display_time_range 	 = $_POST['display_time_range'];
		$data['is_auto']				  = $is_auto;	  	   //是否立即发布
		$data['is_refund']		 		  = $if_refund;		   //是否开启退款
		$data['is_return_good']  	 	  = $if_return_pro;    //是否开启退货	
		$data['is_exchange']     		  = $change_pro;	   //是否开启换货
		$data['is_display_time'] 		  = $if_display_time;  //是否显示时间
		$data['display_time_count_down']  = boolval($display_time_count_down);  //显示倒计时样式
		$data['display_time_range']  	  = boolval($display_time_range);  //显示时间范围
		
		if(empty($data['customer_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
        }
		if(empty($data['time_start']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'time_start不能为空！'));
        } 
		if(empty($data['time_end']))
        {
			json_out(array('errcode' => 400,'errmsg'=>'time_end不能为空！')); 
        }
		if(strtotime($data['time_start'])>strtotime($data['time_end'])){
			json_out(array('errcode' => 400,'errmsg'=>'开始时间不能大于结束时间！')); 	
		}
		
		$result = $this->model->m_create_activity($data);
		$result != false ? $res = array('errcode' => 0,'errmsg'=>'添加活动成功，活动ID为'.$result) : $res = array('errcode' => 400,'errmsg'=>'添加失败');
		json_out($res);
		
	 }
	 
	 /*
	  * 添加活动页面
	  * 传入参数customer_id
	  * Author: huangzeqin $
	  * 2017-9-2
	  */
	  function create_activity_list(){
		  $customer_id 	   = $this->customer_id;
		  $is_open_return  = $this->model->check_order_activist($customer_id);
		  //查看主题颜色
		  $theme = $this->model->find_theme($this->customer_id);
		  include('view/restricted_purchase/addActivity.htm');
	  }
	 
	 /*
	  * 编辑活动页面
	  * 传入参数 $data = array('activity_id'=>'活动id','customer_id'=>'商家id')
	  * Author: huangzeqin $
	  * 2017-8-31
	  */
	  function activity_detail(){
		$customer_id 	   = $this->customer_id;
		$is_open_return    = $this->model->check_order_activist($customer_id);
		$activity_id	   = $_GET['activity_id'];
		
		$data['activity_id']  = $activity_id;
		$data['customer_id']  = $customer_id;
		if(empty($data['activity_id']))
	    {
	     	json_out(array('errcode' => 400,'errmsg'=>'activity_id不能为空'));
	    }
		//查看主题颜色
		$theme = $this->model->find_theme($this->customer_id);
		
		$result = $this->model->m_activity_detail($data);
	//	var_dump($result);
		include('view/restricted_purchase/addActivity.htm');
	  }
	  
	  /*
	   * 修改活动
	   * 传入参数 $data = array('activity_id'=>'活动id','customer_id'=>'商家编号','isvalid'=>'有效值','time_start'=>'活动开始时间','time_end'=>'活动结束时间','is_auto'=>'是否自动收货','is_refund'=>'是否开启退款','is_refund_good'=>'是否开启退货','is_exchange'=>'是否开启换货')
	   * Author: huangzeqin $
	   * 2017-8-31
	   */
	   function update_activity(){
		   $data['activity_id']		  = $_POST['activity_id'];							   //活动ID
		   $data['customer_id'] 	  = $this->customer_id;
		   $data['title']			  = $_POST['title']?$_POST['title']:"";  		  	   //活动名称
		   $data['time_start']		  = $_POST['time_start']?$_POST['time_start']:"";	   //活动开始时间
		   $data['time_end']		  = $_POST['time_end']?$_POST['time_end']:"";		   //活动结束时间
		//   echo $_POST['is_auto'].'_'.$_POST['if_refund']."_".$_POST['if_return_pro']."_".$_POST['change_pro'];
		   if($_POST['is_auto'] == '0' || $_POST['is_auto'] == ""){
				$is_auto = false;
			}else if($_POST['is_auto'] == '1'){
				$is_auto = true;
			}
			if($_POST['if_refund'] == '0' ){
				$if_refund = false;
			}else if($_POST['if_refund'] == '1' || $_POST['if_refund'] == ""){
				$if_refund = true;
			}
			if($_POST['if_return_pro'] == '0' ){
				$if_return_pro = false;
			}else if($_POST['if_return_pro'] == '1' || $_POST['if_return_pro'] == ""){
				$if_return_pro = true;
			}
			if($_POST['change_pro'] == '0' ){
				$change_pro = false;
			}else if($_POST['change_pro'] == '1' || $_POST['change_pro'] == ""){
				$change_pro = true;
			}
			if($_POST['if_display_time'] == '0' ){
				$if_display_time = false;
			}else if($_POST['if_display_time'] == '1' || $_POST['if_display_time'] == ""){
				$if_display_time = true;
			}
			$display_time_count_down = $_POST['display_time_count_down'];
			$display_time_range 	 = $_POST['display_time_range'];
		//	echo $is_auto.'_'.$if_refund."_".$if_return_pro."_".$change_pro;
		//	die();
			$data['is_auto']				  = $is_auto;	  	   //是否立即发布
			$data['is_refund']		 		  = $if_refund;		   //是否开启退款
			$data['is_return_good']  	 	  = $if_return_pro;    //是否开启退货	
			$data['is_exchange']     		  = $change_pro;	   //是否开启换货
			$data['is_display_time'] 		  = $if_display_time;  //是否显示时间
			$data['display_time_count_down']  = boolval($display_time_count_down);  //显示倒计时样式
			$data['display_time_range']  	  = boolval($display_time_range);  //显示时间范围
			
		   if(empty($data['activity_id'])){
			   json_out(array('errcode' => 400,'errmsg'=>'activity_id不能为空'));
		   }
		   $result = $this->model->m_update_activity($data);
		   $result != false ? $res = array('errcode' => 0,'errmsg'=>'修改活动成功') : $res = array('errcode' => 400,'errmsg'=>'修改活动成功');
		   json_out($res);
	   }
	 
	 /*
	  * 活动产品列表
	  * 传入参数 $data = array('activity_id'=>'活动id');
	  * Author: huangzeqin $
	  * 2017-8-31
	  */
	 function activity_product_list(){
		$customer_id          = $this->customer_id;
		
		$activity_id		  = $_GET['activity_id'];
		$data['page']         = $_GET['pagenum']?$_GET['pagenum']:1;
        $data['page_size']    = (int)10;
		$data['activity_id']  = $_GET['activity_id'];
		
	    if(empty($data['activity_id']))
	    {
	     	json_out(array('errcode' => 400,'errmsg'=>'activity_id不能为空'));
	    }
		
		//获取活动信息
		$res = $this->model->m_activity_detail($datas = array('activity_id'=>$activity_id,'customer_id'=>$customer_id));
		
		$result = $this->model->m_activity_product_list($data);
		
		//查看主题颜色
		$theme = $this->model->find_theme($this->customer_id);
		include('view/restricted_purchase/proActivityList.htm');
	//	json_out($result);
	 }
	  
	 /*
	 * 添加产品
	 传入参数 $data = array('activity_id'=>'活动id','product_ids'=>产品列表 array('product_id'=>'产品编号','now_price'=>'现价'))
	 * $Author: huangzeqin $
	 * 2017-08-29  $
	 */
	 function add_activity_product(){
		$customer_id = $this->customer_id;
		
		$activity_id = $_POST['activity_id'];
		$product_ids = $_POST['product_ids'];
		
		$data['activity_id'] = $activity_id;
		$data['customer_id'] = $customer_id;
		$product_list = array();
		$temp         = array();
		foreach($product_ids as $v){
			$temp['product_id'] = $v[0];
			$temp['now_price']  = $v[1];
			array_push($product_list,$temp);
			$temp = array();
		}
		$data['product_ids'] = $product_list;
		$data['isvalid']	 = true;
		$data['createtime']  = date('Y-m-d H:i:s',time());
		if(empty($data['activity_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'activity_id不能为空'));
        }
		if(empty($data['product_ids']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'没有添加产品，添加失败'));
        }
		$result = $this->model->m_add_activity_product($data);
		$result != false ? $res = array('errcode' => 0,'errmsg'=>'产品添加成功') : $res = array('errcode' => 400,'errmsg'=>'产品添加失败');
		json_out($res);
		
	 }
	 
	/*
	 * 修改产品属性 
	 * 传入参数 $data = array('activity_id'=>'活动id','product_list'=>产品列表 array('id'=>'活动产品id','product_id'=>'产品编号','quantity_purchased'=>'限购数量','purchase_times'=>'限购次数','activity_price'=>'活动价格'))
	 * $Author: huangzeqin $
	 * 2017-08-29  $
	 */
	 function update_activity_product($data=array()){
	/*	$product_list = array(
			'0'=>array( 'id'=>68,'product_id' => 233,'quantity_purchased'=>10,'purchase_times'=>9,'activity_price'=>243.5),
			'1'=>array( 'id'=>69,'product_id' => 444,'quantity_purchased'=>1,'purchase_times'=>8,'activity_price'=>3.5)
		); */
		$arr 		  = $_POST['product_list'];
		$temp         = array();
		$product_list = array();
		foreach($arr as $v){
			$temp['id'] 				= $v[0];
			$temp['product_id']		    = $v[1];
			$temp['quantity_purchased'] = $v[2];
			$temp['purchase_times'] 	= $v[3];
			$temp['activity_price'] 	= $v[4];
			array_push($product_list,$temp);
			$temp = array();
		}
		
		$activity_id  = $_POST['activity_id'];
		//测试数据
		$data['customer_id']	  = $this->customer_id;	   				//商家id
		$data['activity_id']	  = $activity_id; 	   					//活动id
		$data['isvalid']		  = true;
		$data['product_list']	  = $product_list;
		if(empty($data['activity_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'activity_id不能为空'));
        }
		if(empty($data['product_list']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'产品不能为空'));
        }
		$result = $this->model->m_update_activity_product($data);
		$result != false ? $res = array('errcode' => 0,'errmsg'=>'编辑成功') : $res = array('errcode' => 400,'errmsg'=>'编辑成功');
		json_out($res);
	 }
	 
	/*
	 * 删除活动产品
	 * 传入参数：data = array('id'=>'产品活动id','activity_id'=>'活动id','product_id'=>'产品id')
	 * author: huangzeqin $
	 * 2017-8-30 $
	 */
	 function del_activity_product(){
		$data['activity_id'] = $_POST['activity_id'];
		$data['product_id']  = $_POST['product_id'];
		$data['customer_id'] = $this->customer_id;
		if(empty($data['customer_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
        }
        if(empty($data['activity_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'activity_id参数丢失！'));
        }
		if(empty($data['product_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'product_id参数丢失！'));
        }
		$result = $this->model->m_del_activity_product($data);
		$result != false ? $res = array('errcode' => 0,'errmsg'=>'删除活动产品成功') : $res = array('errcode' => 400,'errmsg'=>'删除活动产品失败');
		json_out($res);
	 }
	 
	 
	 /*
	 * 查询产品列表
	 * $Author: huangzeqin $
	 * 2017-08-29  $
	 */
	function get_product_list(){
		$activity_id  	      = $_GET['activity_id'];
		$customer_id          = $this->customer_id;
        $data['customer_id']  = $customer_id;      //商家ID
		$data['activity_id']  = $activity_id;
		
		$search_product_id    = trim($_GET['search_product_id'])?trim($_GET['search_product_id']):"";
		$search_product_name  = trim($_GET['search_product_name'])?trim($_GET['search_product_name']):"";
		$search_supply_id     = trim($_GET['search_supply_id'])?trim($_GET['search_supply_id']):"";
		$search_type_id       = $_GET['search_type_id']?$_GET['search_type_id']:"";
		$search_other_id      = $_GET['search_other_id']?$_GET['search_other_id']:-1;
		$search_source	      = $_GET['search_source']?$_GET['search_source']:-1;
		
        //$data['search_key']   = $_POST['search_key'];   //搜索条件，形式：array('product_id'=>'产品编号','product_name'=>'产品名称','supply_id'=>'合作商id','type_id'=>'产品分类','other_id'=>'产品标签','source'=>'商品来源');
        $data['page']         = $_GET['pagenum']?$_GET['pagenum']:1;
        $data['page_size']    = (int)10;

        $search_key = array('product_id'=>$search_product_id,'product_name'=>$search_product_name,'supply_id'=>$search_supply_id,'type_id'=>$search_type_id,'other_id'=>$search_other_id,'source'=>$search_source);

        $data['search_key'] = json_encode($search_key);

        //判断数据是否安全
        if(empty($data['customer_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
        }
		if(empty($data['activity_id']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'activity_id参数丢失！'));
        }
        if(empty($data['search_key']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
        }
        if(empty($data['page_size']))
        {
            json_out(array('errcode' => 400,'errmsg'=>'page_size参数丢失！'));
        }else if($data['page_size'] < 1){
            json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
        }
		
		//查看主题颜色
		$theme = $this->model->find_theme($this->customer_id);
		
		//分类数组
		$type_arr = $this->model->get_select_link($customer_id);
		
        $result = $this->model->m_get_product_list($data);
		include('view/restricted_purchase/addProductList.htm');
    //    json_out($result);
	}
	
	/*
	 * 活动产品销量统计列表
	 * 传入参数：array('activity_id'=>'活动id','customer_id'=>'商家ID','search_key'=>'搜索条件')
	 * 获取变量：活动ID、活动标题、活动状态、活动时间、产品ID、产品名、产品分类、现价、活动价格、限购次数、限购数量、销量、用户数
	 * Author: huangzeqin $
	 * 2017-8-31
	 */
	 function activity_sales_statistics(){
		 $customer_id  		  = $this->customer_id;
		 $activity_id  		  = $_GET['activity_id'];
		 $search_product_name = $_GET['search_product_name']?$_GET['search_product_name']:"";
		 $search_product_id   = $_GET['search_product_id']?$_GET['search_product_id']:"";
		 $search_type_id  	  = $_GET['search_type_id']?$_GET['search_type_id']:"";
		 $data['activity_id']  = $activity_id;
		 $data['customer_id']  = $customer_id;
		 $data['search_key']   = $_POST['search_key'];   //搜索条件，形式：array('product_name'=>'','product_id'=>'','type_id'=>'');
         $data['page']         = $_GET['pagenum']?$_GET['pagenum']:1;
         $data['page_size']    = (int)10;

        //模拟数据
         $search_key = array('product_name'=>$search_product_name,'product_id'=>$search_product_id,'type_id'=>$search_type_id);
         $data['search_key'] = json_encode($search_key);
		 
		 if(empty($data['activity_id']))
         {
            json_out(array('errcode' => 400,'errmsg'=>'activity_id参数丢失！'));
         }
		 if(empty($data['search_key']))
         {
            json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
         }
		 
		 //活动详情 包含：活动ID、活动标题、活动状态、活动时间
		 $res = $this->model->m_activity_detail($data);
		 
		 //查看主题颜色
		$theme = $this->model->find_theme($this->customer_id);
		 
		 //分类数组
		 $type_arr = $this->model->get_select_link($customer_id);
		 
		 $result = $this->model->m_activity_sales_statistics($data);
		 include('view/restricted_purchase/CountsActivityList.htm');
		 
	 }
	 
	 /*
	 * 产品销量统计列表
	 * 传入参数：array('customer_id'=>'商家ID','search_key'=>'搜索条件')
	 * 获取变量：产品ID、产品名、产品分类、现价、原价、销量、活动数量
	 * Author: huangzeqin $
	 * 2017-8-31
	 */
	 function product_sales_statistics(){
		 $customer_id 		   = $this->customer_id;
		 $data['customer_id']  = $customer_id;
	//	 $data['search_key']   = $_POST['search_key'];   //搜索条件，形式：array('product_name'=>'','product_id'=>'','type_id'=>'');
         $data['page']         = $_GET['pagenum']?$_GET['pagenum']:1;
         $data['page_size']    = (int)10;
		
		 $search_class  = 1;
		 $search_product_name  = $_GET['search_product_name']?$_GET['search_product_name']:"";
		 $search_product_id    = $_GET['search_product_id']?$_GET['search_product_id']:"";
		 $search_type_id  	   = $_GET['search_type_id']?$_GET['search_type_id']:"";
        //模拟数据
         $search_key = array('product_name'=>$search_product_name,'product_id'=>$search_product_id,'type_id'=>$search_type_id);
         $data['search_key'] = json_encode($search_key);
		 if(empty($data['search_key']))
         {
            json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
         }
		 
		 //查看主题颜色
		$theme = $this->model->find_theme($this->customer_id);
		 //分类数组
		 $type_arr = $this->model->get_select_link($customer_id);
		 
		 $result = $this->model->m_product_sales_statistics($data);
		 
		 include('view/restricted_purchase/ProductList.htm');
		 //json_out($result);
		 
	 }
	 
	 /*
	 * 用户管理
	 * 传入参数：array('customer_id'=>'商家ID','search_key'=>'搜索条件')
	 * 获取变量：用户编号、头像、用户名、微信号、手机、推荐人、注册时间、限购订单总额
	 * Author: huangzeqin $
	 * 2017-9-1
	 */
	 function activity_user_list(){
		$search_class = 2;
		$customer_id  = $this->customer_id;
		
		$search_user_name     = $_GET['search_user_name']?$_GET['search_user_name']:"";
		$search_user_id       = $_GET['search_user_id']?$_GET['search_user_id']:"";
		$search_phone         = $_GET['search_phone']?$_GET['search_phone']:"";
		$search_begintime     = $_GET['search_begintime']?$_GET['search_begintime']:"";
		$search_endtime       = $_GET['search_endtime']?$_GET['search_endtime']:"";
		$data['customer_id']  =  $customer_id;
	//	$data['search_key']   = $_POST['search_key'];   //搜索条件，形式：array('user_name'=>'','user_id'=>'','phone'=>'','begin_time'=>'','end_time');
		
        $data['page']         = $_GET['pagenum']?$_GET['pagenum']:1;
        $data['page_size']    = (int)10;
		
		$search_key = array('user_name'=>$search_user_name,'user_id'=>$search_user_id,'phone'=>$search_phone,'begin_time'=>$search_begintime,'end_time'=>$search_endtime);
        $data['search_key'] = json_encode($search_key);
		if(empty($data['search_key']))
        {
           json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
        }
		//查看主题颜色
		$theme = $this->model->find_theme($this->customer_id);
		$result = $this->model->m_activity_user_list($data);
		include('view/restricted_purchase/CustomList.htm');
	//	json_out($result);
	 }

//    //限购订单列表 返回数据 array() list:限购订单列表数据, array() pages pagesize:单页显示数量， pagenum：页码 ， pages：总页数
//	 function get_order_list(){
//        session_start();
//        $data['order_status']      = intval($_GET['search_status'])?intval($_GET['search_status']):0;//订单状态
//        $data['search_begintime']  = $_GET['search_begintime'];	//下单开始时间
//        $data['search_endtime']	   = $_GET['search_endtime'];		//下单结束时间
//        $data['search_batchcode']  = trim($_GET['search_batchcode']);	//订单编号
//        $data['s_product_name']	   = trim($_GET['search_product_name']);	//产品名称
//        $data['search_name_type']  = $_GET['search_name_type'];	//用户名称类型 1:微信昵称 2：收货人姓名
//        $data['search_name']	   = trim($_GET['search_name']);			//用户姓名
//        $data['pagenum']		   = $_GET['pagenum']?$_GET['pagenum']:1;   //当前页码
//        $data['pagesize']          = $_GET['pagesize']?$_GET['pagesize']:(int)10;
//        $data['search_phone']	   = trim($_GET['search_phone']);		//用户手机号
//        $data['search_paystyle']   = $_GET['search_paystyle'];    //支付方式
//        $data['search_send_status']= $_GET['search_send_status'];   //配送方式
//        $data['customer_id']       = $_SESSION['customer_id'];     //商户号
//        $data['from_page']         = $_GET['from_page'];            //代发订单类型
//
//        if(empty($data['customer_id']  ))
//        {
//            json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
//        }
//         if(empty($data['pagesize']  ))
//         {
//             json_out(array('errcode' => 400,'errmsg'=>'pagesize参数丢失！'));
//         }
//        $order_list = $this->model->get_restricted_purchase_order_list($data);
////die();
//        json_out($order_list);
//    }
	 
}
