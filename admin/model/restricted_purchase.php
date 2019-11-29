<?php 

/*
	数据库操作示例:

	$data = $this->db->getAll ($sql);
	$data = $this->db->getRow ($sql);
	$data = $this->db->getRow ($sql);
	$data = $this->db->getCol ($sql);
	$data = $this->db->autoExecute('users', array('rank'=>1), 'insert') ;
	$data = $this->db->autoExecute('users', array('rank'=>1), 'update',"user_id = '$uid'") ;
	$data = $this->db->query("select * from users where rank='12'") ;
	$this->db->query("delete from users where rank='12'") ;

	//事务处理
	$this->db->tran_begin();
	try{
		//查用户积分加排他锁
		$data = $this->db->getRow ("select points from users where uid=122 for update");
		//查用户日志加排他锁
		$data = $this->db->getRow ("select log_id from users_log where uid=122 for update");
		//插入日志
		$this->db->autoExecute('users_log', array('points'=>-100,'uid'=>122), 'insert') ;
		//更新用户表总积分
		$this->db->autoExecute('users', array('uid'=>122), 'update',"points = points-100") ;
	} catch(Exception $e){
		$this->db->tran_rollback();
		echo '系统错误，请稍后重试'; exit;
	}
	$this->db->tran_commit();
*/
 
/*
版权信息:  秘密信息
功能描述：限购活动数据操作
开 发 者：黄泽钦
开发日期： 2017-08-29
重要说明：无
 */

class model_restricted_purchase{
	var $db;

	function __construct() 
	{
        $this->db = DB::getInstance();
    }
	
	
	/***
	 * 功能描述：查询限购活动列表
	 * @param $data =	array('customer_id'=>'商家编号','page'=>'当前页数','page_size'=>'每页数量','search_key'=>array('title'=>'活动标题','activity_id'=>'活动ID','isout'=>'活动状态'))
	 * @param $where 	customer_id = $customer_id and isvalid = true . (搜索条件)
	 * @return array     活动数组 array('activity_list'=>'活动列表','page'=>'页数','pagenum'=>'当前页')
	 * @author: huangzeqin $
	 * 2017-08-29  $
	 */
	function m_get_activity_list($data = array()){
		//获取参数并分解
		$customer_id    = $data['customer_id'];
		$page     	    = $data['page'];
		$page_size  	= $data['page_size'];
		//解码
		$search_key 	= json_decode($data['search_key'],true);
		
		$title  		= $search_key['title'];
		$activity_id    = $search_key['activity_id'];
		$isout 			= $search_key['isout'];
		
		//拼接查询语句
		$where = ' customer_id='.$customer_id.' and isvalid = 1 ';
		if(!empty($title)){
			$where .= " and title LIKE '%".$title."%' ";
		}
		if(!empty($activity_id)){
			$where .= " and id = ".$activity_id;
		}
		switch($isout){
			case '0':
				$where .= " and isout = 0";			//待发布
				break;
			case '1':
				$where .= " and isout = 1 and time_start>now()";  //已发布
				break;
			case '2':
				$where .= " and isout = 1 and time_end>now() and now()>time_start";    //进行中
				break;
			case '3':
				$where .= " and isout = 1 and time_end<now()";    //已结束
				break;
			case '4':
				$where .= " and isout = 2";  //终止
				break;
		}

		$count_sql = "select count(1) as sum
					  from ".WSY_SHOP.".weixin_commonshop_restricted_purchase 
					  where ".$where;

	    $all_num  = $this->db->getAll($count_sql)[0]['sum'];

	    $count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

        $offset = ($page-1)*$page_size;
		$limit  = ' limit '.$offset.','.$page_size;
		
		//活动列表
		$sql = "select id,title,is_auto,time_start,time_end,isout
				from ".WSY_SHOP.".weixin_commonshop_restricted_purchase 
				where ".$where." order by id desc ".$limit;
	//	echo $sql;
		$list = $this->db->getAll($sql);

		$result['activity_list'] = $this->activity_list_other($list);
		$result['page']			 = $count;
		$result['pagenum']		 = $page;
		return $result;
	}
	
	/***
	 * 功能描述：限购活动拼装活动状态和产品总数
	 * @param $data = array('activity_list'=>'活动列表')
	 * @return array     活动数组 array('activity_list'=>'拼装后活动列表')
	 * @author: huangzeqin $
	 * 2017-08-29  $
	 */
	function activity_list_other($data = array()){
		foreach($data as $key=>$v){
			//活动状态
			switch($v['isout']){
				case '0':
					$data[$key]['out_str'] = '待发布';
					break;
				case '1':
				    if(time()<strtotime($v['time_start'])){
						$data[$key]['out_str'] = '已发布';
					}else if(time()<strtotime($v['time_end']) && time() >= strtotime($v['time_start'])){
						$data[$key]['out_str'] = '进行中';
					}else if(time()>=strtotime($v['time_end'])){
						$data[$key]['out_str'] = '已结束';
					}
					break;
				case '2':
					$data[$key]['out_str'] = '终止';
					break;
			}
			
			//自动发布状态
			switch($v['is_auto']){
				case 0:
					$data[$key]['auto_str'] = "否";
					break;
				case 1:
					$data[$key]['auto_str'] = "是";
					break;
			}
			
			
			//统计产品数量
			$count_sql = "select count(1) as sum from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products where isvalid = 1 and activity_id=".$v['id'];
			$all_num  = $this->db->getAll($count_sql)[0]['sum'];
			$data[$key]['product_count'] = $all_num;
			
		}
		return $data;
		
	}
	
	/***
	 * 功能描述：查询是否开启售后开关
	 * @param $data  customer_id(string) 商家编号
	 * @return bit is_orderActivist 售后开关(1:开,0:关)
     * @author: huangzeqin $
	 * 2017-9-2
	 */
	function check_order_activist($customer_id){
		$query = "select id from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
		$result = $this->db->getAll($query);
		foreach($result as $v){
			$shop_id=$v['id'];
		}
		$is_orderActivist = 0;//订单售后维权开关:1、开；0、关
		$querys = "select is_orderActivist from weixin_commonshops_extend where isvalid=true and customer_id=".$customer_id." and shop_id=".$shop_id;
		$results = $this->db->getAll($querys);
		foreach($results as $k){
			$is_orderActivist=$k['is_orderActivist'];
		}
		return $is_orderActivist;
	}
	
	/***
  	 * 功能描述：编辑活动页面
	 * @param $data = array('activity_id')=>'活动ID'
	 * @return array result 活动详情信息
	 * @author: huangzeqin
	 * 2017-8-31
	 */
	 function m_activity_detail($data){
		 $sql = "select id,title,time_start,time_end,is_auto,is_comission,is_refund,is_return_good,is_exchange,isout,is_display_time,display_time_count_down,display_time_range from ".WSY_SHOP.".weixin_commonshop_restricted_purchase where id = ".$data['activity_id']." and isvalid = true and customer_id =".$data['customer_id'];
		 $result  = $this->db->getAll($sql);
		 if(!empty($result)){
			$result = $this->activity_list_other($result);
		 }
		 return $result;
	 }
	 
	/***
     * 功能描述：修改活动
	 * @param $data = array('activity_id'=>'活动id','customer_id'=>'商家编号','isvalid'=>'有效值','time_start'=>'活动开始时间','time_end'=>'活动结束时间','is_auto'=>'是否自动收货','is_refund'=>'是否开启退款','is_refund_good'=>'是否开启退货','is_exchange'=>'是否开启换货')
	 * @return string $result 更新结果
     * @author: huangzeqin $
     * 2017-8-31
     */
	 function m_update_activity($data){
		 $customer_id = $data['customer_id'];
		 $activity_id = $data['activity_id'];
		 if($data['is_auto'] == '1'){
			$data['isout'] = 1;
		}
		//查找customer_name
		$customer_name = $this->get_customer_name($customer_id);
		
		 //条件
		 $where = "id = ".$data['activity_id']." and isvalid = true and customer_id = ".$data['customer_id'];

		 unset($data['activity_id']);
		 unset($data['customer_id']);
		 
		 $this->db->tran_begin();
		 try{
			$result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase', $data, 'update',$where) ;
			$func  = array(
							'customer_id'	=> $customer_id,
							'isvalid'	 	=> 1,
							'activity_id'	=> $activity_id,
							'type'		 	=> 8,
							'remark' 	 	=> '修改活动详情',
							'createtime' 	=> date('Y-m-d H:i:s',time()),
							'customer_name' => $customer_name
						);
			$this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase_logs',$func, 'insert') ;
		 }catch(Exception $e){
			$this->db->tran_rollback();
			echo '系统错误，请稍后重试'; exit;
		 }
		 $this->db->tran_commit();
		 return $result;
	 }

	/***
	* 功能描述：改变活动状态
	* @param $data = array('customer_id'=>'商家后台','activity_id'=>'活动ID','op'=>'活动操作')
	* 活动操作:发布活动 op=publish 终止活动 op=stop 删除活动 op = del
	* @return string $result 更新结果
	* @author: huangzeqin 
	* 2017-08-29  
	*/
    function m_change_activity($data = array()){ 
		$customer_id = $data['customer_id'];
		$activity_id = $data['activity_id'];
		$op 		 = $data['op'];
		$where = "customer_id = ".$customer_id." and isvalid = 1 and id = ".$activity_id;
		
		//查找customer_name
		$customer_name = $this->get_customer_name($customer_id);
		
		$this->db->tran_begin();
		try{
			switch($op){
				case 'publish':
					$func  = array('isout'=>1);
					break;
				case 'stop':
					$func  = array('isout'=>2);
					break;
				case 'del':
					$func  = array('isvalid'=>false);
					break;
			}
		
			$result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase', $func, 'update',$where) ;
			
			//插入操作日志
			if($result != false){
				switch($op){
					case 'publish':
						$func  = array(
							'customer_id'	=> $customer_id,
							'isvalid'	 	=> 1,
							'activity_id'	=> $activity_id,
							'type'		 	=> 2,
							'remark' 	 	=> '发布活动',
							'createtime' 	=> date('Y-m-d H:i:s',time()),
							'customer_name' => $customer_name
						);
						break;
					case 'stop':
						$func  = array(
							'customer_id'	=> $customer_id,
							'isvalid'	 	=> 1,
							'activity_id'	=> $activity_id,
							'type'		 	=> 3,
							'remark' 	 	=> '终止活动',
							'createtime' 	=> date('Y-m-d H:i:s',time()),
							'customer_name' => $customer_name
						);
						break;
					case 'del':
						//删除相应的数据表
						$this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase_products', $func, 'update','activity_id='.$activity_id." and isvalid=true") ;
						$func  = array(
							'customer_id'	=> $customer_id,
							'isvalid'	 	=> 1,
							'activity_id'	=> $activity_id,
							'type'		 	=> 4,
							'remark' 	 	=> '删除活动',
							'createtime' 	=> date('Y-m-d H:i:s',time()),
							'customer_name' => $customer_name
						);
						break;
				}
			}
			$this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase_logs',$func, 'insert') ;
		} catch(Exception $e){
			$this->db->tran_rollback();
			echo '系统错误，请稍后重试'; exit;
		}
		$this->db->tran_commit();
		
		return $result;
	}
	
	/***
	* 功能描述: 添加活动
	* @param $data = array('customer_id'=>'商家编号','isvalid'=>'有效值','time_start'=>'活动开始时间','time_end'=>'活动结束时间','is_auto'=>'是否自动收货','is_refund'=>'是否开启退款','is_refund_good'=>'是否开启退货','is_exchange'=>'是否开启换货')
	* @return int $activity_id 新建的活动ID
	* @author: huangzeqin 
	* 2017-08-30  
	*/
	function m_create_activity($data = array()){
		$customer_id  = $data['customer_id'];
		if($data['is_auto'] == '1'){
			$data['isout'] = 1;
		}
		//查找customer_name
		$customer_name = $this->get_customer_name($customer_id);
		
		$this->db->tran_begin();
		try{
			$result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase',$data, 'insert') ;
			
			//获取添加的活动id
			$activity_id = $this->db->insert_id();
			
			//如果活动标题为空，则活动标题为活动id
			if($data['title'] == ""){
				$fun = array('title'=>$activity_id);
				$re = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase',$fun, 'update','id='.$activity_id) ;
			}
			
			$func  = array(
				'customer_id'	=> $customer_id,
				'isvalid'	 	=> 1,
				'activity_id'	=> $activity_id,
				'type'		 	=> 1,
				'remark' 	 	=> '添加活动成功，活动ID为'.$activity_id,
				'createtime' 	=> date('Y-m-d H:i:s',time()),
				'customer_name' => $customer_name
			);
		
			$this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase_logs',$func, 'insert') ;
		} catch(Exception $e){
			$this->db->tran_rollback();
			echo '系统错误，请稍后重试'; exit;
		}
		$this->db->tran_commit();
		return $activity_id;
	}
	
	/*
	* 功能描述: 活动产品列表
	* @param $data = array('activity_id'=>'活动ID','page'=>'当前页','page_size'=>'页数')
	* @return array datas('product_list'=>'产品列表','page'=>'页数','pagenum'=>'当前页')
	* @author: huangzeqin 
	* 2017-08-31 
	*/
	function m_activity_product_list($data){
		$activity_id    = $data['activity_id'];
		$page     	    = $data['page'];
		$page_size  	= $data['page_size'];
		
		//拼接查询语句
		$where = ' activity_id = '.$activity_id.' and isvalid = true ';
		$count_sql = "select count(1) as sum
					  from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products 
					  where ".$where;

	    $all_num  = $this->db->getAll($count_sql)[0]['sum'];

	    $count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

        $offset = ($page-1)*$page_size;
		$limit  = ' limit '.$offset.','.$page_size;
		
		//活动列表
		$sql = "select id,product_id,price,purchase_times,quantity_purchased
				from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products 
				where ".$where." order by product_id desc ".$limit;
		$list = $this->db->getAll($sql);
		
		//拼装产品属性 包括
		$list = $this->pink_product_list($list);
		
		$datas['product_list'] = $list;
		$datas['page']		   = $count;
		$datas['pagenum']	   = $page;
		
		return $datas;
	}
	
	/***
	 * 功能描述: 拼装产品属性 包括产品名称、产品分类、原价、现价、库存
	 * @param $data = array('product_list');
	 * @return array $data = array('product_list'=>array('typename'=>'产品分类','name'=>'产品名称','orgin_price'=>'原价','now_price'->'现价','storenum'=>'库存'.原有属性))
	 * $Author: huangzeqin 
	 * 2017-08-31 
	 */
	function pink_product_list($data = array()){
		foreach($data as $key=>$v){
			$typename = "";
			$sql = "select name,type_ids,orgin_price,now_price,storenum
				from weixin_commonshop_products 
				where id=".$v['product_id'];
			$list = $this->db->getAll($sql);
			
			$type_ids = $list[0]['type_ids'];
			if(!empty($type_ids)){
				if(strpos($type_ids,",") === 0){
				   $type_ids = substr($type_ids,1);
			   }
			   if(substr($type_ids,strlen($type_ids)-1) == ","){
				   $type_ids = substr($type_ids,0,strlen($type_ids)-1);
			   }
			   
				if(!empty($type_ids)){
					$type_ids = str_replace(',,',',',$type_ids);
				   $query4="select name from weixin_commonshop_types where isvalid=true and id in (".$type_ids.") ";
				   $result4 = $this->db->getAll($query4) ;
				   foreach($result4 as $k){
					  $typename = $typename."/".$k['name']; 
				   }
			   }
		   }

		   $data[$key]['typename']    = $typename;
		   $data[$key]['name'] 	      = $list[0]['name'];
		   $data[$key]['orgin_price'] = $list[0]['orgin_price'];
		   $data[$key]['now_price']   = $list[0]['now_price'];
		   $data[$key]['storenum']    = $list[0]['storenum'];
		}
		return $data;
	}
	
	
	/***
	 * 功能描述:修改活动产品属性
	 * @param $data = array('customer_id'=>'商家ID','activity_id'=>'活动ID','product_list'=>array('quantity_purchased'=>'限购数量','purchase_times'=>'限购次数','price'=>'活动价格'))
	 * @return string result != false?更新成功:更新失败
	 * @author: huangzeqin 
	 * 2017-08-30 
	 */
	function m_update_activity_product($data){
		$customer_id 			  = $data['customer_id'];
		//查找customer_name
		$customer_name = $this->get_customer_name($customer_id);
		
		foreach($data['product_list'] as $v){
			$func['quantity_purchased']   = $v['quantity_purchased'];	//限购数量
			$func['purchase_times']   	  = $v['purchase_times'];		//限购次数
			$func['price']   	  		  = $v['activity_price'];		//活动价格
			$this->db->tran_begin();
			try{
				$result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase_products',$func,'update','activity_id = '.$data['activity_id'].' and isvalid = true and id='.$v['id'].' and product_id = '.$v['product_id']);
				$fun  = array(
					'customer_id'	=> $customer_id,
					'isvalid'	 	=> 1,
					'activity_id'	=> $data['activity_id'],
					'type'		 	=> 7,
					'remark' 	 	=> '修改活动产品属性: '.'活动价格修改为：'.$func['price'].',限购次数修改为：'.$func['purchase_times'].'次,限购数量修改为：'.$func['quantity_purchased'],
					'createtime' 	=> date('Y-m-d H:i:s',time()),
					'customer_name' => $customer_name,
					'product_id'    => $v['product_id']
				);
				$this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase_logs',$fun, 'insert') ;
			} catch(Exception $e){
				$this->db->tran_rollback();
				echo '系统错误，请稍后重试'; exit;
			}
			$this->db->tran_commit();
		}
		
		return $result;
	}
	
	/***
	 * 功能描述:添加产品
	 * @param $data = array('customer_id'=>'商家ID','activity_id'=>'活动ID','isvalid'=>'有效值','createtime'=>'创建时间','product_ids'=>array('produc t_id'=>'产品ID','now_price'=>'产品现价'))
	 * @return string result != false?添加成功:添加失败
	 * $Author: huangzeqin
     * 2017-08-30
	 */
	function m_add_activity_product($data){
		$customer_id 			  = $data['customer_id'];
		$func['activity_id']	  = $data['activity_id']; 	   					//活动id
		$func['isvalid']		  = $data['isvalid'];
		$func['createtime']       = $data['createtime'];
		//查找customer_name
		$customer_name = $this->get_customer_name($customer_id);
		foreach($data['product_ids'] as $v){
			$func['product_id'] = $v['product_id'];
			$func['price']		= $v['now_price'];
			$this->db->tran_begin();
			try{
				$result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase_products',$func, 'insert') ;
				$fun  = array(
					'customer_id'	=> $customer_id,
					'isvalid'	 	=> 1,
					'activity_id'	=> $data['activity_id'],
					'type'		 	=> 6,
					'remark' 	 	=> '添加关联商品',
					'createtime' 	=> date('Y-m-d H:i:s',time()),
					'customer_name' => $customer_name,
					'product_id'    => $v['product_id']
				);
				$this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase_logs',$fun, 'insert') ;
			} catch(Exception $e){
				$this->db->tran_rollback();
				echo '系统错误，请稍后重试'; exit;
			}
			$this->db->tran_commit();
		}
		return $result;
	}
	
	/***
	 * 功能描述:删除活动产品
	 * @param $data = array('activity_id'=>'活动ID','product_id'=>'产品ID','customer_id'=>'商家ID')
	 * @return string result != false?删除产品成功:删除失败
	 * @author: huangzeqin
     * 2017-08-30
	 */
	function m_del_activity_product($data){ 
		$customer_id = $data['customer_id'];
		//查找customer_name
		$customer_name = $this->get_customer_name($customer_id);
		$this->db->tran_begin();
		try{
			$result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase_products',array('isvalid'=>false), 'update','activity_id = '.$data['activity_id'].' and product_id = '.$data['product_id'].' and isvalid = true') ;
			$fun  = array(
				'customer_id'	=> $customer_id,
				'isvalid'	 	=> 1,
				'activity_id'	=> $data['activity_id'],
				'type'		 	=> 5,
				'remark' 	 	=> '删除的活动id为'.$data['activity_id'].'产品id为'.$data['product_id'],
				'createtime' 	=> date('Y-m-d H:i:s',time()),
				'customer_name' => $customer_name,
				'product_id'    => $data['product_id']
			);
			$this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_restricted_purchase_logs',$fun, 'insert') ;
		} catch(Exception $e){
			$this->db->tran_rollback();
			echo '系统错误，请稍后重试'; exit;
		}
		$this->db->tran_commit();
		return $result;
	}
	
	/***
	 * 功能描述:添加产品列表
	 * @param $data = array('customer_id'=>'商家ID','page'=>'当前页','page_size'=>'单页数量','search_key'=>'array('product_id'=>'产品ID','product_name'=>'产品名称','supply_id'=>'供应商ID','type_id'=>'分类ID','other_id'=>'标签ID','source'=>'产品来源')')
	 * @return array('product_list'=>'产品列表','page'=>'总页数','pagenum'=>'当前页')
	 * @author: huangzeqin
     * 2017-08-30
	 */
	function m_get_product_list($data=array()){
		//获取参数并分解
		$customer_id    = $data['customer_id'];
		$page     	    = $data['page'];
		$page_size  	= $data['page_size'];
		$search_key 	= json_decode($data['search_key'],true);
		
		$product_id  	= $search_key['product_id'];
		$product_name   = $search_key['product_name'];
		$supply_id 		= $search_key['supply_id'];
		$search_type_id = $search_key['type_id'];
		$search_other_id= $search_key['other_id'];
		$search_source 	= $search_key['source'];
		
		$search_type = -1;
		if($search_type_id>0){
			$search_type = $search_type_id;
		}
		//拼接查询语句
		$where = ' customer_id='.$customer_id.' and isvalid = true and isout = 0 and islimit = 0 and yundian_id <= 0';
		
		if(!empty($product_id)){
		   
		   $query3=$query3." AND id = ".$product_id;
		}
		
		if(!empty($product_name)){
		   
		   $query3=$query3." AND name like'%".$product_name."%'";
		}
		
		if($search_type_id>0){
		  
		   
		    //$query3=$query3." AND type_id in (".$search_type.") ";
			$parent_id	=-1;
			$top_id		=-1;
			$level	= 0;
			$type_SQL="select parent_id,level,top_id from weixin_commonshop_types where id='".$search_type_id."'";
			$type_result = $this->db->getAll($type_SQL);
			 foreach($type_result as $z){
				$parent_id	= $z['parent_id'];
				$level		= $z['level'];
				$top_id		= $z['top_id'];
			 }
			 $Str=" type_ids like '%,".$search_type.",%'";
			 if($parent_id>0){
				//$type_ID_SQL="select id from weixin_commonshop_types where parent_id='".$search_type_id."'";
				$type_ID_SQL="select id from weixin_commonshop_types where top_id='".$top_id."' and level>".$level." and gflag like '%,".$search_type_id.",%'" ;
				$type_ID_result = $this->db->getAll($type_ID_SQL);
				foreach($type_ID_result as $j){
					$type_id = $j['id'];
					$Str=$Str."or type_ids like '%,".$type_id.",%'";
				}
 
			 }else{
			 	$type_ID_SQL="select id from weixin_commonshop_types where top_id='".$search_type_id."' and level>".$level ;
				$type_ID_result = $this->db->getAll($type_ID_SQL);
				foreach($type_ID_result as $j){
					$type_id = $j['id'];
					$Str=$Str."or type_ids like '%,".$type_id.",%'";
				}
			 }
			$query3=$query3." AND (".$Str.")";
			 
			
		}
		if($supply_id>0){
		  
		    $query3=$query3." AND is_supply_id = ".$supply_id;
		}
		if($search_source > 0 && $supply_id==0){
			if($search_source == 1){//平台
				$query3=$query3." AND is_supply_id < 0";
			}else if($search_source == 2){
				$query3=$query3." AND is_supply_id > 0";
				
				if($search_supply > 0 ){
					$query3=$query3." AND is_supply_id = ".$search_supply;
				}
			}
		}
		
		if($search_other_id>0){
		   switch($search_other_id){
		      case 1:
			    
			    $query3=$query3." AND isout=true";
			    break;
			  case 2:
			    $query3=$query3." AND isnew=true";
			    break;
			  case 3:
			    $query3=$query3." AND ishot=true";
			    break;
			  case 4:
			    $query3=$query3." AND isvp=true";
			    break;
			  case 5:
			    $query3=$query3." AND issnapup=true";
			  break;
			  case 6:
			    $query3=$query3." AND is_virtual=true";
			  break;
			  case 7:
			    $query3=$query3." AND is_currency=true";
			  break;
			  case 8:
			    $query3=$query3." AND is_guess_you_like=true";
			  break;
			  case 9:
			    $query3=$query3." AND is_free_shipping=true";
			  break;
			  case 10:
			    $query3=$query3." AND isscore=true";
			  break;
			  case 11:
			    $query3=$query3." AND islimit=true";
			  break;
			  case 12:
			    $query3=$query3." AND is_first_extend=true";
			  break;	
		   }
		}
		
		//排除活动已有产品
		$not_sql = "select product_id from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products where isvalid=true and activity_id=".$data['activity_id'];
		$not_result = $this->db->getAll($not_sql);
		$ids = "";
		foreach($not_result as $i){
			$ids .= ",".$i['product_id'];
		}
		$ids = substr($ids,1);
		if($ids == ""){
			$ids = -1;
		}
		$query3 = $query3." AND id not in (".$ids.")";
		
		//排除未结束活动的产品
		
		//查找当前活动时间
		$time_sql = "select time_start,time_end from ".WSY_SHOP.".weixin_commonshop_restricted_purchase where customer_id=".$data['customer_id']." and isvalid = true and id=".$data['activity_id'];
		$time_result = $this->db->getRow($time_sql);
		$time_start = $time_result['time_start'];
		$time_end 	= $time_result['time_end'];
		
		//获取所有超时的活动id
		$aid_sql = "select id from ".WSY_SHOP.".weixin_commonshop_restricted_purchase where customer_id=".$data['customer_id']." and isout<2 and isvalid = true and ((time_end>='".$time_end."' and time_start <='".$time_start."') or (time_end>='".$time_end."' and time_start between '".$time_start."' and '".$time_end."') or (time_end between '".$time_start."' and '".$time_end."' and time_start <='".$time_start."') or (time_start between '".$time_start."' and '".$time_end."' and time_end <='".$time_end."'))";

		$activity_result = $this->db->getAll($aid_sql);
		$activity_ids = "";
		foreach($activity_result as $i){
			$activity_ids .= ",".$i['id'];
		}
		$activity_ids = substr($activity_ids,1);
		if($activity_ids == ""){
			$activity_ids = -1;
		}
		
		$product_sql = "select product_id from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products where isvalid=true and activity_id in (".$activity_ids.")";
		$product_result = $this->db->getAll($product_sql);
		
		$not_ids = "";
		foreach($product_result as $i){
			$not_ids .= ",".$i['product_id'];
		}
		$not_ids = substr($not_ids,1);
		if($not_ids == ""){
			$not_ids = -1;
		}
		$query3 = $query3." AND id not in (".$not_ids.")";
		
	
		$where = $where.$query3;
		$count_sql = "select count(1) as sum
					  from weixin_commonshop_products 
					  where ".$where;

	    $all_num  = $this->db->getAll($count_sql)[0]['sum'];
		
	    $count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

        $offset = ($page-1)*$page_size;
		$limit  = ' limit '.$offset.','.$page_size;
		
		//产品列表
		$sql = "select id,name,asort_value,type_id,type_ids,orgin_price,now_price,cost_price,need_score,default_imgurl,isnew,createtime,isout,ishot,issnapup,isvp,is_virtual,is_currency,is_guess_you_like,is_free_shipping,isscore,islimit,good_level,meu_level,bad_level,is_supply_id,create_type,sell_count,is_QR,storenum,tax_type
				from weixin_commonshop_products 
				where ".$where." order by id desc ".$limit;
	//	echo $sql;
		$list = $this->db->getAll($sql);
		
		foreach($list as $key=>$v){
			$p_type_id         = $v['type_id'];
			$p_isout           = $v['isout'];
			$p_isnew           = $v['isnew'];
			$p_ishot           = $v['ishot'];
			$p_issnapup        = $v['issnapup'];
			$p_isvp            = $v['isvp'];
			$is_virtual        = $v['is_virtual'];
			$is_currency       = $v['is_currency'];
			$is_guess_you_like = $v['is_guess_you_like'];
			$is_free_shipping  = $v['is_free_shipping'];
			$isscore           = $v['isscore'];
			$islimit           = $v['islimit'];
			$type_ids          = $v['type_ids'];
			$supply_id         = $v['is_supply_id'];
			$tax_type          = $v['tax_type'];

			$otherstr="";
		   if($p_isout){
			  $otherstr=$otherstr."下架";
		   }
		   if($p_isnew){
			  $otherstr=$otherstr."/新品";
		   }
		   if($p_ishot){
			  $otherstr=$otherstr."/热卖";
		   }
		   if($p_issnapup){
			  $otherstr=$otherstr."/抢购";
		   }
		   if($p_isvp){
			  $otherstr=$otherstr."/vp产品";
		   }
		   if($is_virtual){
			  $otherstr=$otherstr."/虚拟产品";
		   }
		   if($is_currency){
			  $otherstr=$otherstr."/购物币产品";
		   }
		   if($is_guess_you_like){
			  $otherstr=$otherstr."/猜您喜欢产品";
		   }
		   if($is_free_shipping){
			  $otherstr=$otherstr."/包邮";
		   }
		   if($isscore){
			  $otherstr=$otherstr."/积分专区";
		   }
		   if($islimit){
			  $otherstr=$otherstr."/限购";
		   }
		   if($tax_type>1){
			  $otherstr=$otherstr."/税收产品";
		   }
		   
		   $list[$key]['otherstr'] = $otherstr;
		   
		   $typename="";
		   if(!empty($type_ids)){
				if(strpos($type_ids,",") === 0){
				   $type_ids = substr($type_ids,1);
			   }
			   if(substr($type_ids,strlen($type_ids)-1) == ","){
				   $type_ids = substr($type_ids,0,strlen($type_ids)-1);
			   }
			   
				if(!empty($type_ids)){
					$type_ids = str_replace(',,',',',$type_ids);
				   $query4="select name from weixin_commonshop_types where isvalid=true and id in (".$type_ids.") ";
				   $result4 = $this->db->getAll($query4) ;
				   foreach($result4 as $k){
					  $typename = $typename."/".$k['name']; 
				   }
			   }
		   }

		   $list[$key]['typename'] = $typename;
	
		   if($supply_id == -1){
			   $source_name = '平台';
		   }else{
			   $Query2= "SELECT name,phone,weixin_name,weixin_fromuser FROM weixin_users WHERE isvalid=true AND id=".$supply_id; 
				$Result2 = $this->db->getAll($Query2) ;
			    foreach($Result2 as $k){
				  $typename = $k['name']."(".$k['weixin_name'].")"; 
			    }
		   }
		   $list[$key]['source_name'] = $source_name;
		}
		
		$arr['product_list'] = $list;
		$arr['page']		 = $count;
		$arr['pagenum']		 = $page;
		return $arr;
		
	}
	
	/***
	 * 功能描述:获取分类数组
	 * @param $customer_id 商家ID
	 * @return $type_arr = array('type_parent_id'=>array('分类ID'.'_'.'分类名称'));
	 * @author: huangzeqin $
	 * 2017-9-4
	 */
	function get_select_link($customer_id){
		//分类排序
		$sort_str = "";
		$type_sort = "SELECT sort_str FROM weixin_commonshop_type_sort WHERE customer_id=".$customer_id;
		
		$result_sort = $this->db->getAll($type_sort);
		$sort_str    = $result_sort[0]['sort_str'];

		$query_type = "SELECT id,name,parent_id from weixin_commonshop_types WHERE isvalid=true AND is_shelves=1 AND customer_id=".$customer_id;

		if( $sort_str ){
			$query_type .= ' ORDER BY field(id'.$sort_str.')';  
		}
		$type_arr = array();	//产品分类数组
		$result_type = $this->db->getAll($query_type);
	//	$result_type = _mysql_query($query_type) or die('Query_type failed: ' . mysql_error());
		
		foreach($result_type as $v){
			$type_parent_id = $v['parent_id'];
			$type_arr[$type_parent_id][] = $v['id'].'_'.$v['name'];
		}
		
		return $type_arr;
	}
	
	/***
	 * 功能描述:活动销量统计
	 * @param $data = array('page'=>'当前页','page_size'=>'单页数量','activity_id'=>'活动ID','search_key'=>array('product_name'=>'产品名称','product_id'=>'产品ID','type_id'=>'分类ID'))
	 * @return $datas = array('product_list'=>'活动产品列表','page'=>'总页数','pagenum'=>'当前页')
	 * @author: huangzeqin $
	 * 2017-8-31
	 */
    function m_activity_sales_statistics($data){
		//获取参数并分解
		$page     	    = $data['page'];
		$page_size  	= $data['page_size'];
		$search_key 	= json_decode($data['search_key'],true);
		
		$product_name  		= $search_key['product_name'];
		$product_id         = $search_key['product_id'];
		$search_type_id 	= $search_key['type_id'];
		
		$search_type = -1;
		if($search_type_id>0){
			$search_type = $search_type_id;
		}
		//拼接查询语句
		$where = '';
		if(!empty($product_name)){
			$where .= " and p.name LIKE '%".$product_name."%' ";
		}
		if(!empty($product_id)){
			$where .= " and ap.product_id = ".$product_id;
		}
		if($search_type_id>0){
		  
		   
		    //$query3=$query3." AND type_id in (".$search_type.") ";
			$parent_id	=-1;
			$top_id		=-1;
			$level	= 0;
			$type_SQL="select parent_id,level,top_id from weixin_commonshop_types where id='".$search_type_id."'";
			$type_result = $this->db->getRow($type_SQL);
			$parent_id	= $type_result['parent_id'];
			$level		= $type_result['level'];
			$top_id		= $type_result['top_id'];
			 
			 $Str=" p.type_ids like '%,".$search_type.",%'";
			 if($parent_id>0){
				//$type_ID_SQL="select id from weixin_commonshop_types where parent_id='".$search_type_id."'";
				$type_ID_SQL="select id from weixin_commonshop_types where top_id='".$top_id."' and level>".$level." and gflag like '%,".$search_type_id.",%'" ;
				$type_ID_result = $this->db->getAll($type_ID_SQL);
				foreach($type_ID_result as $j){
					$type_id = $j['id'];
					$Str=$Str."or p.type_ids like '%,".$type_id.",%'";
				}
 
			 }else{
			 	$type_ID_SQL="select id from weixin_commonshop_types where top_id='".$search_type_id."' and level>".$level ;
				$type_ID_result = $this->db->getAll($type_ID_SQL);
				foreach($type_ID_result as $j){
					$type_id = $j['id'];
					$Str=$Str."or p.type_ids like '%,".$type_id.",%'";
				}
			 }
			$where = $where." AND (".$Str.")";
			
			
		}
		
		$count_sql = "select count(1) as sum
					  from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products as ap LEFT JOIN weixin_commonshop_products as p on p.id = ap.product_id where ap.activity_id=".$data['activity_id']." and ap.isvalid = true and p.customer_id = ".$data['customer_id'].$where;
		
	    $all_num  = $this->db->getAll($count_sql)[0]['sum'];

	    $count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

        $offset = ($page-1)*$page_size;
		$limit  = ' limit '.$offset.','.$page_size;
		
		
		$sql = "select ap.activity_id,p.name,p.type_ids,ap.product_id,p.now_price,ap.price,ap.purchase_times,ap.quantity_purchased from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products as ap LEFT JOIN weixin_commonshop_products as p on p.id = ap.product_id where ap.activity_id=".$data['activity_id']." and ap.isvalid = true and p.customer_id = ".$data['customer_id'].$where." order by ap.createtime desc ".$limit;
	//	echo $sql;
		$list = $this->db->getAll($sql);
		
		//拼装产品属性 包括
		$list = $this->pink_product_list($list);
		
		//销量
		foreach($list as $key=>$v){
			$result = $this->get_activity_product_sale($v['product_id'],$v['activity_id']);
			$list[$key]['custom_num'] = $result['custom_num'];
			$list[$key]['sale_num']   = $result['sale_num'];
		}
		
		$datas['pagenum']      = $page;
		$datas['page']	       = $count;
		$datas['product_list'] = $list;
		return $datas;
		
	}
	
	/*** 
	 * 功能描述:统计活动产品的销量、用户数量、活动数量
	 * @param product_id	产品ID(int)  
	 * @param activity_id   活动ID(int)
	 * @return $data = array('sale_num'=>'销量','custom_num'=>'用户数量','activity_num'=>'活动数量')
     * @author: huangzeqin $
	 * 2017-8-31
	 */
	 function get_activity_product_sale($product_id,$activity_id=""){
		 if($activity_id == ""){
			 $where = "pid = ".$product_id." and sendstatus<3 and isvalid = true and is_restricted_purchase = true and restricted_purchase_id > 0 ";
			 $wheres = "product_id = ".$product_id." and isvalid = true";
		 }else{
		     $where = "pid = ".$product_id." and sendstatus<3  and isvalid = true and is_restricted_purchase = true and restricted_purchase_id=".$activity_id;
			 $wheres = "product_id = ".$product_id." and activity_id = ".$activity_id." and isvalid = true";
		 }
		 
		 //销量
		 $sale_sql = "select SUM(rcount) as rcount from weixin_commonshop_orders where ".$where;
		 $sale_num = $this->db->getAll($sale_sql)[0]['rcount'];
		 if($sale_num == ""){
			 $sale_num = 0;
		 }
		 
		 //用户数量
		 $custom_sql = "select count(DISTINCT(user_id)) as sum from weixin_commonshop_orders where ".$where;
		 $custom_num = $this->db->getAll($custom_sql)[0]['sum'];
		 if($custom_num == ""){
			 $custom_num = 0;
		 }
		 
		 //活动数量
		 $activity_sql = "select count(DISTINCT(activity_id)) as sum from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products where ".$wheres;
		 $activity_num = $this->db->getAll($activity_sql)[0]['sum'];
		 if($activity_num == ""){
			 $activity_num = 0;
		 }
		 
		 
		 
		 $data['sale_num']     = $sale_num;
		 $data['custom_num']   = $custom_num;
		 $data['activity_num'] = $activity_num;
		 return $data;
	 }
	 
	/*
	 * 功能描述:产品销量统计
	 * @param $data = array('page'=>'当前页','page_size'=>'单页数量','search_key'=>array('product_name'=>'产品名称','product_id'=>'产品ID','type_id'=>'分类ID'))
	 * @return $datas = array('product_list'=>'活动产品列表','page'=>'总页数','pagenum'=>'当前页')
	 * Author: huangzeqin $
	 * 2017-8-31
	 */
	function m_product_sales_statistics($data){
		//获取参数并分解
		$page     	    = $data['page'];
		$page_size  	= $data['page_size'];
		$search_key 	= json_decode($data['search_key'],true);
		
		$product_name  		= $search_key['product_name'];
		$product_id         = $search_key['product_id'];
		$search_type_id 	= $search_key['type_id'];
		//拼接查询语句
		$where = '';
		if(!empty($product_name)){
			$where .= " and p.name LIKE '%".$product_name."%' ";
		}
		if(!empty($product_id)){
			$where .= " and ap.product_id = ".$product_id;
		}
		$search_type = -1;
		if($search_type_id>0){
			$search_type = $search_type_id;
		}
		if($search_type_id>0){
		  
		   
		    //$query3=$query3." AND type_id in (".$search_type.") ";
			$parent_id	=-1;
			$top_id		=-1;
			$level	= 0;
			$type_SQL="select parent_id,level,top_id from weixin_commonshop_types where id='".$search_type_id."'";
			$type_result = $this->db->getAll($type_SQL);
			 foreach($type_result as $z){
				$parent_id	= $z['parent_id'];
				$level		= $z['level'];
				$top_id		= $z['top_id'];
			 }
			 $Str=" p.type_ids like '%,".$search_type.",%'";
			 if($parent_id>0){
				//$type_ID_SQL="select id from weixin_commonshop_types where parent_id='".$search_type_id."'";
				$type_ID_SQL="select id from weixin_commonshop_types where top_id='".$top_id."' and level>".$level." and gflag like '%,".$search_type_id.",%'" ;
				$type_ID_result = $this->db->getAll($type_ID_SQL);
				foreach($type_ID_result as $j){
					$type_id = $j['id'];
					$Str=$Str."or p.type_ids like '%,".$type_id.",%'";
				}
 
			 }else{
			 	$type_ID_SQL="select id from weixin_commonshop_types where top_id='".$search_type_id."' and level>".$level ;
				$type_ID_result = $this->db->getAll($type_ID_SQL);
				foreach($type_ID_result as $j){
					$type_id = $j['id'];
					$Str=$Str."or p.type_ids like '%,".$type_id.",%'";
				}
			 }
			$where = $where." AND (".$Str.")";
			
			
		}

		$count_sql = "select count(DISTINCT(ap.product_id)) as sum
					  from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products as ap LEFT JOIN weixin_commonshop_products as p on p.id = ap.product_id where ap.isvalid = true and p.customer_id = ".$data['customer_id'].$where;
	//	echo $count_sql;
	    $all_num  = $this->db->getAll($count_sql)[0]['sum'];

	    $count   = ceil($all_num/$page_size);
	//	echo $count;
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }
	//	echo $page;
        $offset = ($page-1)*$page_size;
		$limit  = ' limit '.$offset.','.$page_size;
		
		
		$sql = "select ap.activity_id,p.name,p.type_ids,ap.product_id,p.now_price,ap.price,ap.purchase_times,ap.quantity_purchased from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products as ap LEFT JOIN weixin_commonshop_products as p on p.id = ap.product_id where ap.isvalid = true and p.customer_id = ".$data['customer_id'].$where." group by ap.product_id order by ap.product_id desc ".$limit;
	//	echo $sql;
		
		$list = $this->db->getAll($sql);
		
		//拼装产品属性 包括
		$list = $this->pink_product_list($list);
		
		//销量
		foreach($list as $key=>$v){
			$result = $this->get_activity_product_sale($v['product_id']);
			$list[$key]['sale_num']     = $result['sale_num'];
			$list[$key]['activity_num'] = $result['activity_num'];
		}
		$datas['pagenum']      = $page;
		$datas['page']	       = $count;
		$datas['product_list'] = $list;
		return $datas;
	}
	
	/***
	 * 功能描述:用户管理
	 * @param $data = array('customer_id'=>'商家ID','page'=>'当前页','page_szie'=>'单页数量','search_key'=>array('user_name'=>'用户名','user_id'=>'用户ID','phone'=>'用户手机号','begin_time'=>'开始时间','end_time'=>'结束时间'))
	 * @return $datas = array('user_list'=>'用户列表','page'=>'总页数','pagenum'=>'当前页')
	 * 获取变量：用户编号、头像、用户名、微信号、手机、推荐人、注册时间、限购订单总额
	 * Author: huangzeqin $
	 * 2017-9-1
	 */
	function m_activity_user_list($data){
		$customer_id = $data['customer_id'];
		//获取参数并分解
		$page     	   		= $data['page'];
		$page_size  		= $data['page_size'];
		$search_key 		= json_decode($data['search_key'],true);
		
		$user_name  		= $search_key['user_name'];
		$user_id            = $search_key['user_id'];
		$phone 		     	= $search_key['phone'];
		$search_begintime 	= $search_key['begin_time'];
		$search_endtime 	= $search_key['end_time'];
		
		//拼接查询语句
		$where = ' u.customer_id = '.$customer_id." and wco.customer_id = ".$customer_id." and wco.isvalid = true and wco.is_restricted_purchase = true and wco.restricted_purchase_id > 0";
		if(!empty($user_name)){
			$where .= " and (u.name LIKE '%".$user_name."%' or u.weixin_name LIKE '%".$user_name."%' )";
		}
		if(!empty($user_id)){
			$where .= " and u.id = ".$user_id;
		}
		if(!empty($phone)){
			$where .= " and u.phone = ".$phone;
		}
		if ($search_begintime != "") {
			$where = $where . " and UNIX_TIMESTAMP(u.createtime)>" . strtotime($search_begintime);
		}

		if ($search_endtime != "") {
			$where = $where . " and UNIX_TIMESTAMP(u.createtime)<" . strtotime($search_endtime);
		}
		
		$count_sql = "select count(1) as sum
					  from weixin_users as u INNER JOIN weixin_commonshop_orders as wco on wco.user_id = u.id where ".$where." group by wco.user_id";

	    $all_num  = $this->db->getAll($count_sql)[0]['sum'];

	    $count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

        $offset = ($page-1)*$page_size;
		$limit  = ' limit '.$offset.','.$page_size;
		
		
		$sql = "select u.name,u.id,u.weixin_name,u.weixin_headimgurl,u.phone,u.parent_id,u.createtime,u.customer_id from weixin_users as u INNER JOIN weixin_commonshop_orders as wco on wco.user_id = u.id where ".$where." group by wco.user_id order by u.id desc ".$limit;
		$list = $this->db->getAll($sql);
		
		if(!empty($list)){
			$list = $this->pink_user_list($list);
		}
		
		$datas['pagenum']      = $page;
		$datas['page']	       = $count;
		$datas['user_list'] = $list;
		return $datas;
	}
	
	/***
	 * 功能描述:拼装用户属性 包括推荐人和限购订单数量
	 * @param $data = array('0'=>array('parent_id'=>'推荐人ID','id'=>'用户ID','customer_id'=>'商家ID',......),......)
	 * @return $data = array('0'=>array('parent_id'=>'推荐人ID','id'=>'用户ID','customer_id'=>'商家ID','parent_name'=>'推荐人','order_num'=>'订单总额',......),......)
	 * @author: huangzeqin $
	 * 2017-9-1
	 */
	function pink_user_list($data){
		foreach($data as $key=>$v){
			//推荐人
			$user_sql = "select name,weixin_name from weixin_users where id=".$v['parent_id']." and customer_id = ".$v['customer_id'];
			$user_result = $this->db->getAll($user_sql);
			
			foreach($user_result as $k){
				$parent_name 	    = $k['name'];
				$parent_weixin_name = $k['weixin_name'];
			}
			
			$data[$key]['parent_name'] = $parent_name."(".$parent_weixin_name.")";
			
			//限购订单数量
			$order_sql = "select sum(totalprice) as sum from weixin_commonshop_orders where user_id =".$v['id']." and isvalid = true and is_restricted_purchase = true ";
			$order_num = $this->db->getAll($order_sql)[0]['sum'];
			$data[$key]['order_num'] = $order_num;
		}
		return $data;
	}
	
	/***
	 * 功能描述:查看后台主题颜色
	 * @param customer_id  商家ID(int)
	 * @return $theme 	   颜色(string)
	 * @author:  huangzeqin
	 * 2017-9-6 
	 */
	function find_theme($customer_id){
		//获取主题颜色
		$query = 'SELECT theme FROM customers where isvalid=true and id='.$customer_id;
		//$result = _mysql_query($query) or die('Query failed: ' . mysql_error());	
		$result = $this->db->getAll($query);
		$theme="blue";
		foreach($result as $v){
			$theme = $v['theme'];
		}
		return $theme;
	}
	
	/***
	 * 功能描述:查看商家名称
	 * @param customer_id  商家ID(int)
	 * @return $name 	   商家姓名(string)
	 * @author:  huangzeqin
	 * 2017-9-6 
	 */
	function get_customer_name($customer_id){
		$query = 'SELECT name FROM customers where isvalid=true and id='.$customer_id;
		//$result = _mysql_query($query) or die('Query failed: ' . mysql_error());	
		$result = $this->db->getAll($query);
		$name="";
		foreach($result as $v){
			$name = $v['name'];
		}
		return $name;
	}
}

?>