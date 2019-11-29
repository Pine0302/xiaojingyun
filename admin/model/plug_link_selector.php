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
功能描述：新链接选择
开 发 者：黄泽钦
开发日期： 2017-11-9
重要说明：无
 */

class model_plug_link_selector{
    public $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }
	
	//获取背景颜色
	public function get_theme($customer_id){
		$sql = "select theme from customers where id = '".$customer_id."' and isvalid = true";
		$result = $this->db->getOne($sql);
		return $result;
	}

	//收银O2O-一级分类
	public function select_o2o_one_category($condition,$limit=null){
		$customer_id = $condition['customer_id'];
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) from now_pay_trade where isvalid=true and level=0 ";
        $rcount_num = $this->db->getOne($query_num);
		
		//查询一级分类名字,等级为0
		$sql = "SELECT trade_name,level FROM now_pay_trade where isvalid=true and level=0 ";		
		$sql = $sql;
		$res = $this->db->getAll($sql);
		
		$pageCount=ceil($rcount_num/20);
		$result['result_list'] = $res;
		$result['pageCount'] = $pageCount;
		return $result;
	}

	//收银O2O-一级分类详情和二级分类
	public function select_o2o_category_list($condition,$limit=null){
		$customer_id = $condition['customer_id'];
		$parent_id   = $condition['parent_id'];
		$class   	 = $condition['class'];
		$parent_id   -=2;
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) from now_pay_trade where isvalid=true and level=0 ";
        $rcount_num = $this->db->getOne($query_num);
		
		//查询一级分类名字,等级为0
		//1.利用一个联合查询根据获得的id,查询是否有下级id,如果有继续往下查
		$sql = "SELECT id,trade_name,level FROM now_pay_trade where isvalid=true and level=0 ";
		$data = $this->db->getAll($sql);

		$id = $data[$parent_id]['id'];
		$sid = $data[$parent_id]['id'];
		//查询下级id
		$sql = "SELECT id,trade_name,level FROM now_pay_trade where isvalid=true and level=".$id;
		$data = $this->db->getAll($sql);
		$result['category'] = $data[0]['id'];

		if($data && $class != 0 ){
			//如果有下级查询下级的店铺  循环左查询
			$id = '';
			// $result['category']='';
			$count = count($data);
			foreach( $data as $key => $value ){
				// $result['category'].= $value['id'];
				$id.=$value['id'];
				if ($key != $count-1){
					$id.=',';
					// $result['category'].= ',';
				}
			}
			$sql = "SELECT c.id,c.cust_name,t.trade_name from now_pay_cust as c LEFT JOIN now_pay_trade as t on t.id = c.n_p_trade where c.n_p_trade in(".$id.") and c.isvalid=true ";
			$res= $this->db->getAll($sql);
			$rcount_num = count($res);
			$sql = $sql.$limit;
			$res= $this->db->getAll($sql);
		}else{
			//查询一二级
			// $count = count($data);
			// $id.=',';
			// foreach( $data as $key => $value ){
			// 	$id.=$value['id'];
			// 	if ( $key != $count-1 && $count!=1 ){
			// 		$id.=',';
			// 	}
			// }
			$sql = "SELECT c.id,c.cust_name,t.trade_name from now_pay_cust as c LEFT JOIN now_pay_trade as t on t.id = c.n_p_trade where c.n_p_trade = ".$id." and c.isvalid=true ";
			// var_dump($sql);
			$res= $this->db->getAll($sql);
			$rcount_num = count($res);
			$sql = $sql.$limit;
			$res = $this->db->getAll($sql);
		}

		//如果里面有level有
		
		$pageCount=ceil($rcount_num/20);
		$result['result_list'] = $res;
		$result['rcount'] = strval($rcount_num);
		$result['pageCount'] = $pageCount;
		$result['sid'] = $sid;
		return $result;
	}

	//收银O2O-二级分类
	public function select_o2o_category_next($condition,$limit=null){
		$customer_id = $condition['customer_id'];
		$parent_id   = $condition['parent_id'];
		$parent_id   -=2;
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) from now_pay_trade where isvalid=true and level=0 ";
        $rcount_num = $this->db->getOne($query_num);
		
		$sql = "SELECT id,trade_name,level FROM now_pay_trade where isvalid=true and level=0 ";
		$data = $this->db->getAll($sql);

		$id = $data[$parent_id]['id'];
		//查询下级id
		$sql = "SELECT id,trade_name,level FROM now_pay_trade where isvalid=true and level=".$id;
		$res = $this->db->getAll($sql);


		//如果里面有level有
		
		$pageCount=ceil($rcount_num/30);
		$result['result_list'] = $res;
		$result['rcount'] = strval($rcount_num);
		$result['pageCount'] = $pageCount;
		return $result;
	}

	//收银O2O-二级分类详情
	public function select_o2o_category_next_list($condition,$limit=null){
		$customer_id = $condition['customer_id'];
		$parent_id   = $condition['parent_id'];
		$parent_id_next   = $condition['parent_id_next'];
		$parent_id        -=2;
		$parent_id_next   -=1;
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) from now_pay_trade where isvalid=true and level=0 ";
        $rcount_num = $this->db->getOne($query_num);
		
		$sql = "SELECT id,trade_name,level FROM now_pay_trade where isvalid=true and level=0 ";
		$data = $this->db->getAll($sql);

		$id = $data[$parent_id]['id'];
		//查询下级id
		$sql = "SELECT id,trade_name,level FROM now_pay_trade where isvalid=true and level=".$id;
		$res = $this->db->getAll($sql);

		//根据id搜索
		$id = $res[$parent_id_next]['id'];
		$sql = "SELECT c.id,c.cust_name,t.trade_name from now_pay_cust as c LEFT JOIN now_pay_trade as t on t.id = c.n_p_trade where c.n_p_trade = ".$id." and c.isvalid=true ";
		$res= $this->db->getAll($sql);
		$rcount_num = count($res);
		$sql = $sql.$limit;
		$res = $this->db->getAll($sql);

		//如果里面有level有
		
		$pageCount=ceil($rcount_num/30);
		$result['result_list'] = $res;
		$result['rcount'] = strval($rcount_num);
		$result['pageCount'] = $pageCount;
		return $result;
	}
	
	//查看图文消息
	public function select_photo_text_message($condition,$limit=null,$search_val=null){
		$customer_id = $condition['customer_id'];
		$where = "";
		if($search_val != ""){ 
			$where = " and title like '%".$search_val."%'";
		}

		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) as rcount FROM weixin_subscribes where isvalid=true and parent_id=-1 and is_message=0 and customer_id=".$customer_id.$where;
        $rcount_num = $this->db->getOne($query_num);
		
		$sql = 'SELECT id,title FROM weixin_subscribes where isvalid=true and parent_id=-1 and is_message=0 and customer_id='.$customer_id.$where;
		$sql = $sql.$limit;
		
		$pageCount=ceil($rcount_num/20);
		$result['result_list'] = $this->db->getAll($sql);
		$result['rcount'] = $rcount_num;
		$result['pageCount'] = $pageCount;
		return $result;
	}
	
	//微官网单页
	public function select_wei_singlepage($condition,$limit=null,$search_val=null){
		$customer_id = $condition['customer_id'];
		
		if($search_val != ""){
			$where = " and name like '%".$search_val."%'";
		}
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) as rcount FROM site_singlepage where  c_id=".$customer_id." and isvalid = 1 and indexShow = 0".$where;
        $rcount_num = $this->db->getOne($query_num);
		
		$sql = 'SELECT id,name FROM site_singlepage where  c_id='.$customer_id." and isvalid = 1 and indexShow = 0 ".$where;
		$sql = $sql.$limit;
		
		$pageCount=ceil($rcount_num/20);
		$result['result_list'] = $this->db->getAll($sql);
		$result['rcount'] = $rcount_num;
		$result['pageCount'] = $pageCount;
		
		return $result;
	}
	
	//自定义模板
	public function select_diy_template($condition,$limit=null,$search_val=null){
		$customer_id = $condition['customer_id'];
		if($search_val != ""){
			$where = " AND name like '%".$search_val."%' ";
		}
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) as rcount FROM weixin_commonshop_diy_template WHERE is_open=true AND isvalid=true AND customer_id=".$customer_id.$where;
        $rcount_num = $this->db->getOne($query_num);

		$sql = "SELECT id,name FROM weixin_commonshop_diy_template WHERE is_open=true AND isvalid=true AND customer_id=".$customer_id.$where." ORDER BY id DESC";
		$sql = $sql.$limit;
		
		$pageCount=ceil($rcount_num/20);
		$result['result_list'] = $this->db->getAll($sql);
		$result['rcount'] = $rcount_num;
		$result['pageCount'] = $pageCount;
		return $result;
	}
	
	//全部产品
	public function select_all_product($condition,$limit=null,$search_val=null,$search_type=null,$type = null){
		$customer_id = $condition['customer_id'];  
		$where = "";
		if($search_val != ""){
			$where .= " AND name like '%".$search_val."%' ";
		}
		if($search_type != ""){
			$where .= " AND type_ids like '%,".$search_type.",%' ";
		}
		switch($type){
			case 'product_privilege':
				$where .= " AND is_privilege = true ";
				break;
			case 'product_new':
				$where .= " AND isnew = true ";
				break;
			case 'product_hot':
				$where .= " AND ishot = true ";
				break;
			case 'product_vp':
				$where .= " AND isvp = true ";
				break;
		}
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) as rcount from weixin_commonshop_products where isvalid = 1 and isout = 0 and customer_id=".$customer_id.$where;
        $rcount_num = $this->db->getOne($query_num);
		
		$sql = "SELECT id,name,type_ids from weixin_commonshop_products where isvalid = 1 and isout = 0 and customer_id=".$customer_id.$where;
		
		
		$sql .= " order by id desc".$limit;
		
		$result = $this->db->getAll($sql);
		foreach ($result as $k=>$v) {
			$type_ids = $v['type_ids'];
			$typename = "";
            
            if(!empty($type_ids)){
                if(strpos($type_ids,",") === 0){
                    $type_ids = substr($type_ids,1);
                }
                if(substr($type_ids,strlen($type_ids)-1) == ","){
                    $type_ids = substr($type_ids,0,strlen($type_ids)-1);
                }
               
                if(!empty($type_ids)){
                    $type_ids = str_replace(',,',',',$type_ids);
                    
                    $query = "SELECT name from weixin_commonshop_types where isvalid=true and id in (".$type_ids.")  ORDER BY create_parent_id asc ";
                    
                    $data_type = $this->db->getAll($query);
                    foreach ($data_type as $key=>$val) {
                        $typename = $typename."/".$val['name'];
                    }
                }
            }

            $result[$k]['typename']        = $typename;
		}
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//会员卡列表
	public function select_card_member($condition,$limit=null){
		$customer_id = $condition['customer_id'];  
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) as rcount from weixin_cards  where isvalid=true and customer_id=".$customer_id;
        $rcount_num = $this->db->getOne($query_num);
		
		$sql = "select id,name,imgurl,font_color,shop_name,card_type,num_color,is_show_shopnum,isauto,getmoney_name from weixin_cards  where isvalid=true and customer_id=".$customer_id.$limit;
		$res = $this->db->getAll($sql);
		
		$pageCount=ceil($rcount_num/20);
		$result['result_list'] = $res;
		$result['rcount'] = $rcount_num;
		$result['pageCount'] = $pageCount;
		return $result;
	}
	
	//产品分类
	public function select_product_type($condition,$limit=null,$search_val=null){
		$customer_id =  $condition['customer_id'];
		
		$sort_str = "";
		$type_sort = "SELECT sort_str FROM weixin_commonshop_type_sort WHERE customer_id=".$customer_id;
		$res = $this->db->getRow($type_sort);
		$sort_str = $res['sort_str'];
		
		$where = "";
		if($search_val != ""){
			$where .= " and level = ".$search_val;
		}
		
		$where .= ' ORDER BY id desc ';  
		// 导致其余ID的分类没有按照一定顺序排列而导致分页时重复并丢失 CRM15648
		// if( $sort_str ){
		// 	$where .= ' ORDER BY field(id'.$sort_str.') ';  
		// }
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) as rcount from weixin_commonshop_types WHERE isvalid=true AND is_shelves=1 AND customer_id=".$customer_id.$where;
        $rcount_num = $this->db->getOne($query_num);

		$query_type = "SELECT id,name,parent_id from weixin_commonshop_types WHERE isvalid=true AND is_shelves=1 AND customer_id=".$customer_id.$where;
		
		$query_type = $query_type.$limit;
		
		if($search_val != ""){
			$type_arr = $this->db->getAll($query_type);
		}else{
			$type_arr = array();	//产品分类数组
			$result_type = _mysql_query($query_type) or die('Query_type failed: ' . mysql_error());

			while ($row_type = mysql_fetch_object($result_type)) {
				$type_parent_id = $row_type -> parent_id;
				$type_arr[$type_parent_id][] = $row_type -> id.'_'.$row_type->name;
			}
		}
		$pageCount=ceil($rcount_num/20);
		$result['result_list'] = $type_arr;
		$result['rcount'] = $rcount_num;
		$result['pageCount'] = $pageCount;
		
		return $result;
	
	}

	//线下商城-店铺分类
	public function select_shoptype_name($condition,$limit=null){
		$customer_id = $condition['customer_id'];
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) as pageCount from weixin_cityarea_shop_shoptypes where isvalid=true and customer_id=".$customer_id;
        $rcount_num = $this->db->getOne($query_num);
		
		$sql = "select shoptype_name,id from weixin_cityarea_shop_shoptypes where isvalid=true and customer_id=".$customer_id." order by sort  desc ,id DESC ";		
		$sql = $sql.$limit;
		$res = $this->db->getAll($sql);
		
		$pageCount=ceil($rcount_num/20);
		$result['result_list'] = $res;
		$result['pageCount'] = $pageCount;
		return $result;
	}

	//线下商城-店铺分类-具体分类
	public function select_classify_list($condition,$limit=null){
		$customer_id = $condition['customer_id'];
		$parent_id   = $condition['parent_id'];
		$parent_id-=1;
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT shoptype_name from weixin_cityarea_shop_shoptypes where isvalid=true and customer_id=".$customer_id;
        $rcount_num = $this->db->getOne($query_num);
		
		//查找类型
		$sql = "SELECT id,shoptype_name from weixin_cityarea_shop_shoptypes where isvalid=true and customer_id=".$customer_id;		
		$sql = $sql.$limit;
		$type = $this->db->getAll($sql);		//查询出来的是分类名称
		$res['type'] = $type;

		//查找supply_id
		$sql = "SELECT supply_id,o2o_shop_type FROM weixin_cityarea_shop_extends WHERE customer_id = ".$customer_id." and isvalid=true ";
        $type_result=$this->db->getAll($sql);
        $o2o_shop_type = -1;
        foreach ($type_result as $key => $val) {	//遍历185次
        	$o2o_shop_type = trim($val['o2o_shop_type'],",");
            if(strstr($val['o2o_shop_type'], ',')){
                $o2o_type_arr = explode(",", $val['o2o_shop_type']);
                foreach( $o2o_type_arr as $k => $v ){
                	if( $v == $res['type'][$parent_id]['id'] ) {
                		$res['id'][] = $val['supply_id'];
                		break;
                	}
                }
            }else{
                if ($val['o2o_shop_type'] != '') {
                    $o2o_type_arr = array($val);
                    foreach( $o2o_type_arr as $k => $v ){
	                	if( $v == $res[$parent_id]['id'] ) {
	                		// $res['id'][] = $val['supply_id'];
	                	}
	                }
                }
            }
            if( $val['o2o_shop_type'] == $res['type'][$parent_id]['id'] ) {
            	$res['id'][] = $val['supply_id'];
            }
        }

        //根据id查找商店名称
        foreach ( $res['id'] as $key => $val ){
        	$sql = "SELECT id,shop_name FROM weixin_cityarea_supply WHERE id = ".$val." and isvalid=true and customer_id=".$customer_id;
        	$shop_name = $this->db->getAll($sql);
        	if(!empty($shop_name)){
        		$res['shop_name'][] = $shop_name;
        	}
        }
		$res['val'] = $res['type'][$parent_id]['shoptype_name'];
		foreach( $res['shop_name'] as $key => $val ) {
			$result['result_list'][$key][] = $val[0]['id'];
			$result['result_list'][$key][] = $val[0]['shop_name'];
			$result['result_list'][$key][] = $res['type'][$parent_id]['shoptype_name']; 
		}
		


		$pageCount=ceil($rcount_num/20);
		// $result['result_list'] = $res;
		$result['pageCount'] = $pageCount;
		return $result;
	}
	
	//城市商圈-店铺列表
	public function select_cityarea_shop($condition,$limit=null,$city_type=-1){
		$customer_id = $condition['customer_id'];
		if($city_type > 0){
			$where = " and types =".$city_type;
		}
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) as rcount from weixin_cityarea_supply where isvalid=true and customer_id=".$customer_id.$where;
        $rcount_num = $this->db->getOne($query_num);
		
		$sql = "select id,shop_name from weixin_cityarea_supply where isvalid=true and customer_id=".$customer_id.$where;
		
		$sql = $sql.$limit;
		$res = $this->db->getAll($sql);
		
		$pageCount=ceil($rcount_num/20);
		$result['result_list'] = $res;
		$result['rcount'] = $rcount_num;
		$result['pageCount'] = $pageCount;
		return $result;
	}
	
	//续费专区 上架产品
	public function select_renew_product($condition,$limit=null,$search_val=null,$search_type=null){
		$customer_id = $condition['customer_id'];
		$where = "";
		if($search_val != ""){
			$where .= " and (pro.name like '%".$search_val."%' or pro.id = ".$search_val.")";
		}
		if($search_type != ""){
			$where .= " AND pro.type_ids like '%,".$search_type.",%' ";
		}
		
		$rcount_num = 0;
		$query_num  = "select count(DISTINCT(pro.id)) as rcount from promoter_renewal as pr 
		INNER JOIN promoter_renewal_products as prp on pr.id = prp.renewal_id 
		INNER JOIN weixin_commonshop_products as pro on prp.product_id = pro.id
		WHERE pr.customer_id = ".$customer_id." and pr.isvalid = true and pr.isout = 0 and prp.isvalid = true and pro.isvalid = true and pro.isout=0 and pro.customer_id = ".$customer_id.$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "select pro.id,pro.name,pro.type_ids from promoter_renewal as pr 
		INNER JOIN promoter_renewal_products as prp on pr.id = prp.renewal_id 
		INNER JOIN weixin_commonshop_products as pro on prp.product_id = pro.id
		WHERE pr.customer_id = ".$customer_id." and pr.isvalid = true and pr.isout = 0 and prp.isvalid = true and pro.isvalid = true and pro.isout=0 and pro.customer_id = ".$customer_id.$where;
		
		$sql .= " group by pro.id ".$limit;
		$result = $this->db->getAll($sql);
		foreach ($result as $k=>$v) {
			$type_ids = $v['type_ids'];
			$typename = "";
            
            if(!empty($type_ids)){
                if(strpos($type_ids,",") === 0){
                    $type_ids = substr($type_ids,1);
                }
                if(substr($type_ids,strlen($type_ids)-1) == ","){
                    $type_ids = substr($type_ids,0,strlen($type_ids)-1);
                }
               
                if(!empty($type_ids)){
                    $type_ids = str_replace(',,',',',$type_ids);
                    
                    $query = "SELECT name from weixin_commonshop_types where isvalid=true and id in (".$type_ids.")  ORDER BY create_parent_id asc ";
                    
                    $data_type = $this->db->getAll($query);
                    foreach ($data_type as $key=>$val) {
                        $typename = $typename."/".$val['name'];
                    }
                }
            }

            $result[$k]['typename']        = $typename;
		}
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
		
	}
	
	//续费活动列表
	public function select_renew_activity($condition,$limit,$search_val){
		$customer_id = $condition['customer_id'];
		$where = "";
		if($search_val != ""){
			$where .= " and (title like '%".$search_val."%') ";
		}

		$rcount_num = 0;
		$query_num  = "select count(1) as rcount from promoter_renewal where customer_id = ".$customer_id." and isvalid = true ".$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql ="SELECT id,title,isout FROM promoter_renewal WHERE isvalid=true and customer_id='".$customer_id."'".$where;
		
		$sql = $sql.$limit;
	
		$result = $this->db->getAll($sql);
		foreach($result as $key=>$v){
			$status_str = "未知状态";
			if($v['isout'] == 0){
				$status_str = "已上架";
			}else if($v['isout'] == 1){
				$status_str = "已下架";
			}else if($v['isout'] == 2){
				$status_str = "未使用";
			}
			$result[$key]['status_str'] = $status_str;
			
			$about_num = 0;
			$query_about_num  = "select count(1) as rcount from promoter_renewal_products as prp 
			INNER JOIN weixin_commonshop_products as pro on prp.product_id = pro.id 
			WHERE prp.isvalid = true and pro.isvalid = true and pro.isout=0 and pro.customer_id = ".$customer_id." and prp.renewal_id='".$v['id']."'";
			$about_num = $this->db->getOne($query_about_num);
			$result[$key]['about_num'] = $about_num;
		}
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//限时活动产品
	public function select_product_restricte_time($condition,$limit=null,$search_val=null,$search_type=null){
		$customer_id = $condition['customer_id'];
		$where = "";
		if($search_val != ""){
			$where .= " and (p.name like '%".$search_val."%' or p.id = ".$search_val.")";
		}
		if($search_type != ""){
			$where .= " AND p.type_ids like '%,".$search_type.",%' ";
		}
		
		$rcount_num = 0;
		$query_num  = "select count(DISTINCT(ap.product_id)) as rcount from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products as ap LEFT JOIN weixin_commonshop_products as p on p.id = ap.product_id where ap.isvalid = true and p.customer_id = ".$customer_id.$where." order by ap.product_id desc ";
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "select p.id,p.name,p.type_ids from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products as ap LEFT JOIN weixin_commonshop_products as p on p.id = ap.product_id where ap.isvalid = true and p.customer_id = ".$customer_id.$where." group by ap.product_id order by ap.product_id desc ".$limit;
		$result = $this->db->getAll($sql);
		foreach ($result as $k=>$v) {
			$type_ids = $v['type_ids'];
			$typename = "";
            
            if(!empty($type_ids)){
                if(strpos($type_ids,",") === 0){
                    $type_ids = substr($type_ids,1);
                }
                if(substr($type_ids,strlen($type_ids)-1) == ","){
                    $type_ids = substr($type_ids,0,strlen($type_ids)-1);
                }
               
                if(!empty($type_ids)){
                    $type_ids = str_replace(',,',',',$type_ids);
                    
                    $query = "SELECT name from weixin_commonshop_types where isvalid=true and id in (".$type_ids.")  ORDER BY create_parent_id asc ";
                    
                    $data_type = $this->db->getAll($query);
                    foreach ($data_type as $key=>$val) {
                        $typename = $typename."/".$val['name'];
                    }
                }
            }

            $result[$k]['typename']        = $typename;
		}
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
		
	}
	
	//限时活动列表
	public function select_limit_activity($condition,$limit=null,$search_val=null){
		$customer_id = $condition['customer_id'];
		$where = "";
		if($search_val != ""){
			$where .= " and (title like '%".$search_val."%')";
		}

		$rcount_num = 0;
		$query_num  = "select count(1) as sum from ".WSY_SHOP.".weixin_commonshop_restricted_purchase where customer_id = ".$customer_id." and isvalid = true ".$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql ="select id,title,isout,time_end,time_start from ".WSY_SHOP.".weixin_commonshop_restricted_purchase where customer_id = ".$customer_id." and isvalid = true ".$where." order by id desc ";

		$sql = $sql.$limit;
		$result = $this->db->getAll($sql);
		foreach($result as $key=>$v){
			$status_str = "未知状态";
			switch($v['isout']){
				case '0':
					$status_str = '待发布';
					break;
				case '1':
				    if(time()<strtotime($v['time_start'])){
						$status_str = '已发布';
					}else if(time()<strtotime($v['time_end']) && time() >= strtotime($v['time_start'])){
						$status_str = '进行中';
					}else if(time()>=strtotime($v['time_end'])){
						$status_str = '已结束';
					}
					break;
				case '2':
					$status_str = '终止';
					break;
			}
			$result[$key]['status_str'] = $status_str;
			
			$about_num = 0;
			$query_about_num  = "select count(1) as sum from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products as ap 
			LEFT JOIN weixin_commonshop_products as p on p.id = ap.product_id 
			where ap.isvalid = true and p.customer_id = '".$customer_id."' and ap.activity_id= '".$v['id']."'";
			$about_num = $this->db->getOne($query_about_num);
			$result[$key]['about_num'] = $about_num;
		}
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//大礼包列表
	public function select_package_list($condition,$limit=null,$search_val=null){
		$customer_id = $condition['customer_id'];
		$where = "";
		if($search_val != ""){
			$where .= " and package_name like '%".$search_val."%' ";
		}
		
		$rcount_num = 0;
		$query_num  = "select count(1) as rcount from package_list_t where customer_id = ".$customer_id." and isvalid = true and isout = 0 ".$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "select id,package_name from package_list_t where customer_id = ".$customer_id." and isvalid = true and isout = 0 ".$where;
		
		$sql = $sql.$limit;
		$result = $this->db->getAll($sql);
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
		
	}
	
	//商品满赠活动列表
	public function select_exchange_activity($condition,$limit,$search_val){
		$customer_id = $condition['customer_id'];
		$where = "";
		
		if($search_val != ""){
			$where .= " and (title like '%".$search_val."%')";
		}
		
		$rcount_num = 0;
		$query_num  = "select count(1) as sum from weixin_commonshop_exchange where customer_id = ".$customer_id." and isvalid = true ".$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql ="select id,title,starttime,endtime,status,is_superposition,threshold,exchange_num from weixin_commonshop_exchange where customer_id = ".$customer_id." and isvalid = true ".$where." order by id desc ";
		$sql = $sql.$limit;
		$result = $this->db->getAll($sql);
		foreach ($result as $key => $v) { 								
			$status = $v['status'] ;
			switch($status){
				case "1":
					$status_str = "待发布";
					break;
				case "2":
					$status_str = "已发布";
					break;
				case "3":
					$status_str = "进行中";
					break;
				case "4":
					$status_str = "已结束";
					break;
				default:
					$status_str = "未知状态";
			}
			$result[$key]['status_str'] = $status_str;
			
			$about_num = 0;
			$query_about_num  = "select count(1) as sum from weixin_commonshop_exchange_products as ep 
				inner join weixin_commonshop_products as p on p.id = ep.pid 
				where ep.isvalid = true and p.customer_id = '".$customer_id."' and ep.exchange_id= '".$v['id']."'";
			$about_num = $this->db->getOne($query_about_num);
			$result[$key]['about_num'] = $about_num;
		}
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//积分关联产品列表
	public function select_integral_product($condition,$limit=null,$search_val=null,$search_type=null,$type=null){
		$customer_id = $condition['customer_id'];
		$where = "";
		
		if($search_val != ""){
			$where .= " and (wxp.name like '%".$search_val."%' or wxp.id = ".$search_val.")";
		}			
		if($search_type != ""){
			$where .= " AND wxp.type_ids like '%,".$search_type.",%' ";
		}
		if($type>0){
			$where .= " AND ipi.type = ".$type;			//1.积分 2.兑换
		}
		
		$rcount_num = 0;
		if($type == 1)
		{	
			$query_num  = "select count(DISTINCT(ipi.pid)) as rcount from ".WSY_SHOP.".integral_product_index as ipi 
			inner join ".WSY_SHOP.".integral_activity as ia on ia.act_id = ipi.act_id 
			inner join weixin_commonshop_products as wxp on ipi.pid = wxp.id 
			where ia.isvalid = true and ipi.cust_id = ".$customer_id." and ia.cust_id = ".$customer_id." and wxp.isvalid = true and wxp.customer_id= ".$customer_id.$where;
		}
		else
		{
			$query_num  = "select count(DISTINCT(ipi.pid)) as rcount from ".WSY_SHOP.".integral_product_index as ipi 
			inner join ".WSY_SHOP.".integral_activity as ia on ia.act_id = ipi.act_id 
			inner join weixin_commonshop_products as wxp on ipi.pid = wxp.id 
			inner join ".WSY_SHOP.".integral_exchange_product as exp on exp.product_id=wxp.id
			where ia.isvalid = true and ipi.cust_id = ".$customer_id." and ia.cust_id = ".$customer_id." and wxp.isvalid = true and wxp.customer_id= ".$customer_id.$where.' and exp.isvalid=1 and ia.status = 1';
		}
		$rcount_num = $this->db->getOne($query_num);
		

		if($type == 1)
		{	
			$sql = "select ipi.pid,wxp.id,wxp.name,wxp.type_ids from ".WSY_SHOP.".integral_product_index as ipi 
			inner join ".WSY_SHOP.".integral_activity as ia on ia.act_id = ipi.act_id 
			inner join weixin_commonshop_products as wxp on ipi.pid = wxp.id 
			where ia.isvalid = true and ipi.cust_id = ".$customer_id." and ia.cust_id = ".$customer_id." and wxp.isvalid = true and wxp.customer_id= ".$customer_id.$where;
		}
		else  
		{
			$sql = "select ipi.pid,wxp.id,wxp.name,wxp.type_ids from ".WSY_SHOP.".integral_product_index as ipi 
			inner join ".WSY_SHOP.".integral_activity as ia on ia.act_id = ipi.act_id 
			inner join weixin_commonshop_products as wxp on ipi.pid = wxp.id
			inner join ".WSY_SHOP.".integral_exchange_product as exp on exp.product_id=wxp.id
			where ia.isvalid = true and ipi.cust_id = ".$customer_id." and ia.cust_id = ".$customer_id." and wxp.isvalid = true and wxp.customer_id= ".$customer_id.$where.' and exp.isvalid=1 and ia.status = 1';
		}
		
		$sql .= " group by ipi.pid ".$limit;
		$result = $this->db->getAll($sql);
		foreach ($result as $k=>$v) {
			$type_ids = $v['type_ids'];
			$typename = "";
            
            if(!empty($type_ids)){
                if(strpos($type_ids,",") === 0){
                    $type_ids = substr($type_ids,1);
                }
                if(substr($type_ids,strlen($type_ids)-1) == ","){
                    $type_ids = substr($type_ids,0,strlen($type_ids)-1);
                }
               
                if(!empty($type_ids)){
                    $type_ids = str_replace(',,',',',$type_ids);
                    
                    $query = "SELECT name from weixin_commonshop_types where isvalid=true and id in (".$type_ids.")  ORDER BY create_parent_id asc ";
                    
                    $data_type = $this->db->getAll($query);
                    foreach ($data_type as $key=>$val) {
                        $typename = $typename."/".$val['name'];
                    }
                }
            }

            $result[$k]['typename']        = $typename;
		}
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//积分活动列表
	public function select_intergral_activity($condition,$limit=null,$search_val=null,$type=null){
		$customer_id = $condition['customer_id'];
		$where = "";
		$now 	   = time();
		
		if($search_val != ""){
			$where .= " and (act_name like '%".$search_val."%') ";
		}			

		if($type != ""){
			$where .= " AND act_type = ".$type;			//1.积分 2.兑换
		}
		
		$rcount_num = 0;
		$query_num  = "select count(1) as sum from ".WSY_SHOP.".integral_activity where cust_id = '".$customer_id."' and isvalid = 1".$where; 
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "select act_id,act_name,add_time,status,start_time,end_time,auto_start from ".WSY_SHOP.".integral_activity where cust_id = '".$customer_id."' and isvalid = 1".$where;
		
		$sql .= " order by act_id desc ".$limit;
		$result = $this->db->getAll($sql);
		foreach($result as $k => $val){
			$result[$k]['id'] 	= $val['act_id'];
			$result[$k]['title'] = $val['act_name'];
			
			if($val['status'] == 0){
				$result[$k]['status_str'] = "未启动";
			}elseif($val['status'] == 1){
				if(strtotime($val['start_time']) <= $now){
				    if(strtotime($val['end_time']) < $now){
                        $result[$k]['status_str'] = "结束";
                    }else{
                        $result[$k]['status_str'] = "进行中";
                    }
				}else{
					$result[$k]['status_str'] = "已启用";
				}
			}elseif($val['status'] == 2){
				$result[$k]['status_str'] = "结束";
			}elseif($val['status'] == 3){
				$result[$k]['status_str'] = "手动终止";
			}
			
			$about_num = 0;
			$query_about_num  = "select count(1) as sum from ".WSY_SHOP.".integral_activity_product as iap inner join weixin_commonshop_products as p on iap.product_id = p.id where iap.isvalid = 1 and iap.act_id='".$val['act_id']."' and p.isvalid = true and p.customer_id= '".$customer_id."'";
			$about_num = $this->db->getOne($query_about_num);
			$result[$k]['about_num'] = $about_num;
		}
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//赠送活动产品详情
	public function select_send_package($condition,$limit=null,$search_val=null){
		$customer_id = $condition['customer_id'];
		$where = "";
		if($search_val != ""){
			$where .= " and plt.package_name like '%".$search_val."%' ";
		}
		
		$rcount_num = 0;
		$query_num = "select count(1) as rcount from weixin_commonshop_activity_products as wcap 
		inner join weixin_commonshop_activities as wca on wca.id = wcap.activity_id 
		inner join package_list_t as plt on wcap.product_id = plt.id 
		where wcap.isvalid = true and plt.customer_id=".$customer_id." and plt.isvalid = true and wca.customer_id = ".$customer_id." and wca.isvalid = true".$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "select plt.package_name,plt.id from weixin_commonshop_activity_products as wcap 
		inner join weixin_commonshop_activities as wca on wca.id = wcap.activity_id 
		inner join package_list_t as plt on wcap.product_id = plt.id 
		where wcap.isvalid = true and plt.customer_id=".$customer_id." and plt.isvalid = true and wca.customer_id = ".$customer_id." and wca.isvalid = true".$where;
		$sql = $sql.$limit;
		$result = $this->db->getAll($sql);
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		
		return $res;
	}
	
	//拼团活动列表
	public function select_pink_activity($condition,$limit=null,$search_val=null){
		$customer_id = $condition['customer_id'];
		
		$where = "";
		if($search_val != ""){
			$where .= " and at.name like '%".$search_val."%' ";
		}
		
		$rcount_num = 0;
		$query_num = "select count(1) as rcount FROM collage_activities_t AS at 
				LEFT JOIN collage_group_order_t AS ot ON at.id=ot.activitie_id 
				INNER JOIN weixin_users AS wu ON wu.id=ot.head_id
				INNER JOIN weixin_commonshop_products AS mp ON mp.id=ot.pid   
			 WHERE at.isvalid=true AND ot.isvalid=true AND at.customer_id=".$customer_id.$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "SELECT id,name,type,start_time,end_time,group_size,number,status,createtime FROM collage_activities_t WHERE customer_id=".$customer_id." AND isvalid=true".$where;
		
		
		$sql = "SELECT ot.id,at.name,wu.weixin_name
				FROM collage_activities_t AS at 
				LEFT JOIN collage_group_order_t AS ot ON at.id=ot.activitie_id 
				INNER JOIN weixin_users AS wu ON wu.id=ot.head_id
				INNER JOIN weixin_commonshop_products AS mp ON mp.id=ot.pid   
			 WHERE at.isvalid=true AND ot.isvalid=true AND at.customer_id=".$customer_id.$where;
		$sql = $sql.$limit;
		$result = $this->db->getAll($sql);
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//所有拼团活动
	public function select_all_pink_activity($condition,$limit=null,$search_val=null){
		$customer_id = $condition['customer_id'];
		
		$where = "";
		if($search_val != ""){
			$where .= " and name like '%".$search_val."%' ";
		}
		
		$rcount_num = 0;
		$query_num = "SELECT count(1) as rcount FROM collage_activities_t WHERE customer_id=".$customer_id." AND isvalid=true".$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "SELECT id,name,type,start_time,end_time,group_size,number,status,createtime FROM collage_activities_t WHERE customer_id=".$customer_id." AND isvalid=true".$where;
		$sql = $sql.$limit;
		$result = $this->db->getAll($sql);
		foreach($result as $key=>$v){
			$result[$key]['title'] = $v['name'];
			
			switch( $v['status'] ){
				case 1:
					$status_str = '未发布';
					break;
				case 2:
					$status_str = '进行中';
					break;
				case 3:
					$status_str = '终止';
					break;
				case 4:
					$status_str = '已结束';
					break;
			}
			$result[$key]['status_str'] = $status_str;
			
			$about_num = 0;
			$query_about_num  = "select count(1) as rcount from collage_group_products_t as cgp 
				inner join weixin_commonshop_products as p on p.id = cgp.pid 
				where cgp.isvalid = true and p.customer_id = '".$customer_id."' and cgp.activitie_id= '".$v['id']."'";
			$about_num = $this->db->getOne($query_about_num);
			$result[$key]['about_num'] = $about_num;
		}
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//拼团活动关联产品
	public function select_pink_activity_product($condition,$limit=null,$search_val=null,$search_type=null){
		$customer_id = $condition['customer_id'];
		
		$where = "";
		if($search_val != ""){
			$where .= " and (cp.name like '%".$search_val."%' or cp.id = ".$search_val.")";
		}			
		if($search_type != ""){
			$where .= " AND cp.type_ids like '%,".$search_type.",%' ";
		}

		$rcount_num = 0;
		$query_num = "select count(DISTINCT(cp.id)) as rcount FROM collage_group_products_t AS pt 
			INNER JOIN collage_activities_t AS at ON at.id=pt.activitie_id 
			INNER JOIN weixin_commonshop_products AS cp ON cp.id=pt.pid  
            LEFT JOIN collage_activities_explain_t AS ae ON ae.type=at.type
            INNER JOIN collage_group_products_t AS CGPT ON cp.id = CGPT.pid AND at.id = CGPT.activitie_id
			 WHERE at.customer_id = ".$customer_id." and ae.customer_id = ".$customer_id." and ae.isvalid = true and pt.isvalid = true and at.isvalid = true and CGPT.isvalid = true".$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "SELECT cp.id,cp.name,cp.type_ids 
			FROM collage_group_products_t AS pt 
			INNER JOIN collage_activities_t AS at ON at.id=pt.activitie_id 
			INNER JOIN weixin_commonshop_products AS cp ON cp.id=pt.pid  
            LEFT JOIN collage_activities_explain_t AS ae ON ae.type=at.type
            INNER JOIN collage_group_products_t AS CGPT ON cp.id = CGPT.pid AND at.id = CGPT.activitie_id
			 WHERE at.customer_id = ".$customer_id." and ae.customer_id = ".$customer_id." and ae.isvalid = true and pt.isvalid = true and at.isvalid = true and CGPT.isvalid = true".$where;
		$sql .= " group by cp.id ".$limit;
		$result = $this->db->getAll($sql);
		foreach ($result as $k=>$v) {
			$type_ids = $v['type_ids'];
			$typename = "";
            
            if(!empty($type_ids)){
                if(strpos($type_ids,",") === 0){
                    $type_ids = substr($type_ids,1);
                }
                if(substr($type_ids,strlen($type_ids)-1) == ","){
                    $type_ids = substr($type_ids,0,strlen($type_ids)-1);
                }
               
                if(!empty($type_ids)){
                    $type_ids = str_replace(',,',',',$type_ids);
                    
                    $query = "SELECT name from weixin_commonshop_types where isvalid=true and id in (".$type_ids.")  ORDER BY create_parent_id asc ";
                    
                    $data_type = $this->db->getAll($query);
                    foreach ($data_type as $key=>$val) {
                        $typename = $typename."/".$val['name'];
                    }
                }
            }

            $result[$k]['typename']        = $typename;
		}
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//众筹活动
	public function select_cr_activity($condition,$limit=null,$search_val=null){
		$customer_id = $condition['customer_id'];
		$where = "";
		if($search_val != ""){
			$where .= " and activity_title like '%".$search_val."%' ";
		}
		
		$rcount_num = 0;
		$query_num = "select count(1) as rcount FROM ".WSY_SHOP.".cr_activity where isvalid = true and customer_id = ".$customer_id.$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "select id,activity_title from ".WSY_SHOP.".cr_activity where isvalid = true and customer_id = ".$customer_id.$where;
		$sql = $sql.$limit;
		$result = $this->db->getAll($sql);
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//众筹活动关联产品
	public function select_cr_activity_product($condition,$limit=null,$search_val=null,$search_type=null){
		$customer_id = $condition['customer_id'];
		$where = "";
		if($search_val != ""){
			$where .= " and (cp.name like '%".$search_val."%' or cp.id = ".$search_val.")";
		}			
		if($search_type != ""){
			$where .= " AND cp.type_ids like '%,".$search_type.",%' ";
		}
		
		$rcount_num = 0;
		$query_num = "select count(DISTINCT(cp.id)) as rcount FROM ".WSY_SHOP.".cr_goods AS cg 
			INNER JOIN weixin_commonshop_products AS cp ON cp.id=cg.product_id 
			 WHERE cp.customer_id = ".$customer_id." and cg.customer_id = ".$customer_id." and cp.isvalid = true and cg.isvalid = true".$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "SELECT cg.id,cg.activity_id,cp.name,cp.type_ids 
			FROM ".WSY_SHOP.".cr_goods AS cg 
			INNER JOIN weixin_commonshop_products AS cp ON cp.id=cg.product_id 
			 WHERE cp.customer_id = ".$customer_id." and cg.customer_id = ".$customer_id." and cp.isvalid = true and cg.isvalid = true".$where;
		
		$sql .= " group by cp.id ".$limit;
		$result = $this->db->getAll($sql);
		foreach ($result as $k=>$v) {
			$type_ids = $v['type_ids'];
			$typename = "";
            
            if(!empty($type_ids)){
                if(strpos($type_ids,",") === 0){
                    $type_ids = substr($type_ids,1);
                }
                if(substr($type_ids,strlen($type_ids)-1) == ","){
                    $type_ids = substr($type_ids,0,strlen($type_ids)-1);
                }
               
                if(!empty($type_ids)){
                    $type_ids = str_replace(',,',',',$type_ids);
                    
                    $query = "SELECT name from weixin_commonshop_types where isvalid=true and id in (".$type_ids.")  ORDER BY create_parent_id asc ";
                    
                    $data_type = $this->db->getAll($query);
                    foreach ($data_type as $key=>$val) {
                        $typename = $typename."/".$val['name'];
                    }
                }
            }

            $result[$k]['typename']        = $typename;
		}
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//砍价活动
	public function select_bargain_activity($condition,$limit=null,$search_val=null){
		$customer_id = $condition['customer_id'];
		$where = "";
		if($search_val != ""){
			$where .= " and activity_title like '%".$search_val."%' ";
		}
		
		$rcount_num = 0;
		$query_num = "select count(1) as rcount FROM ".WSY_SHOP.".kj_activity where isvalid = true and customer_id = ".$customer_id.$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "select id,activity_title from ".WSY_SHOP.".kj_activity where isvalid = true and customer_id = ".$customer_id.$where;
		
		$sql = $sql.$limit;
		$result = $this->db->getAll($sql);
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//砍价活动关联产品
	public function select_bargain_activity_product($condition,$limit=null,$search_val=null,$search_type=null){
		$customer_id = $condition['customer_id'];
		
		$where = "";
		if($search_val != ""){
			$where .= " and (cp.name like '%".$search_val."%' or cp.id = ".$search_val.")";
		}			
		if($search_type != ""){
			$where .= " AND cp.type_ids like '%,".$search_type.",%' ";
		}
		
		$rcount_num = 0;
		$query_num = "select count(DISTINCT(cp.id)) as rcount FROM ".WSY_SHOP.".kj_goods AS kg 
			INNER JOIN weixin_commonshop_products AS cp ON cp.id=kg.product_no 
			 WHERE cp.customer_id = ".$customer_id." and kg.customer_id = ".$customer_id." and cp.isvalid = true and kg.isvalid = true".$where;
		$rcount_num = $this->db->getOne($query_num);
		
		$sql = "SELECT max(kg.id) as id,max(kg.activity_id) as activity_id,cp.name,cp.type_ids 
			FROM ".WSY_SHOP.".kj_goods AS kg 
			INNER JOIN weixin_commonshop_products AS cp ON cp.id=kg.product_no 
			 WHERE cp.customer_id = ".$customer_id." and kg.customer_id = ".$customer_id." and cp.isvalid = true and kg.isvalid = true".$where;
		$sql .= " group by cp.id ".$limit;
		
		$result = $this->db->getAll($sql);
		foreach ($result as $k=>$v) {
			$type_ids = $v['type_ids'];
			$typename = "";
            
            if(!empty($type_ids)){
                if(strpos($type_ids,",") === 0){
                    $type_ids = substr($type_ids,1);
                }
                if(substr($type_ids,strlen($type_ids)-1) == ","){
                    $type_ids = substr($type_ids,0,strlen($type_ids)-1);
                }
               
                if(!empty($type_ids)){
                    $type_ids = str_replace(',,',',',$type_ids);
                    
                    $query = "SELECT name from weixin_commonshop_types where isvalid=true and id in (".$type_ids.")  ORDER BY create_parent_id asc ";
                    
                    $data_type = $this->db->getAll($query);
                    foreach ($data_type as $key=>$val) {
                        $typename = $typename."/".$val['name'];
                    }
                }
            }

            $result[$k]['typename']        = $typename;
		}
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}
	
	//礼包满赠活动列表
	public function select_send_package_list($condition,$limit,$search_val){
		$customer_id = $condition['customer_id'];
        $where = "";
        if($search_val != ""){
            $where .= " and (title like '%".$search_val."%')";
        }
		
        $rcount_num = 0;
        $query_num = "SELECT count(1) as sum from weixin_commonshop_activities where isvalid = true and customer_id=".$customer_id.$where;
        $rcount_num = $this->db->getOne($query_num);
		
        $sql ="SELECT id,title,time_start,time_end,activity_types,rule_ids,isout,createtime 
		from weixin_commonshop_activities where isvalid = true and customer_id=".$customer_id.$where;
        $sql .= "  order by id desc ".$limit;
        $result = $this->db->getAll($sql);
		foreach($result as $key=>$v){
			switch($v['isout']){
				case '0':
					$result[$key]['status_str'] = "上架";
					break;
				case '1':
					$result[$key]['status_str'] = "下架";
					break;
				case '2':
					$result[$key]['status_str'] = "未使用";
					break;
			}
			
			$about_num = 0;
			$query_about_num  = "select count(1) as rcount from weixin_commonshop_activity_products as wcap 
				inner join package_list_t as p on p.id = wcap.product_id 
				where wcap.isvalid = true and p.customer_id = '".$customer_id."' and wcap.activity_id= '".$v['id']."'";
			$about_num = $this->db->getOne($query_about_num);
			$result[$key]['about_num'] = $about_num;
		}
		
		$pageCount=ceil($rcount_num/20);
		$res['result_list'] = $result;
		$res['rcount'] = $rcount_num;
		$res['pageCount'] = $pageCount;
		return $res;
	}

	//查询品牌合作商店铺
    public function select_cooperative_shop($condition,$limit=null,$search_val=null,$search_type=null){
        $customer_id = $condition['customer_id'];
        $where = "";
        if($search_val != ""){
            $where .= " and (a.shopName like '%".$search_val."%' or wu.name like '%".$search_val."%' or wu.weixin_name like '%".$search_val."%' or wcbs.user_id = '".$search_val."' or wcbs.brand_tel = '{$search_val}')";
        }
        $rcount_num = 0;
        $query_num = "SELECT count(wcbs.id)
            from ".WSY_SHOP.".weixin_commonshop_brand_supplys wcbs 
            INNER JOIN ".WSY_SHOP.".weixin_commonshop_applysupplys a ON a.user_id = wcbs.user_id and a.isvalid=true
            inner join ".WSY_USER.".weixin_users wu on wcbs.isvalid=true and wcbs.user_id=
                wu.id and wcbs.customer_id={$customer_id} and wcbs.brand_status=1 {$where}";
        $rcount_num = $this->db->getOne($query_num);
        $sql ="SELECT 
                wcbs.user_id as user_id,
                wcbs.brand_logo as brand_logo,
                wcbs.brand_tel as brand_tel,
                wcbs.brand_intro as brand_intro,
                wcbs.brand_name as brand_name,
                wcbs.asort_value as asort_value,
                wcbs.brand_address as brand_address,
                wcbs.brand_business_license as brand_business_license,
                wcbs.addition as addition,
                wcbs.brand_status as brand_status,
                wcbs.reason as reason,
                wcbs.brand_opentime as brand_opentime,
                wcbs.creatime as creatime,
                wu.name as name,
                wu.weixin_name as weixin_name, 
                a.shopName
            from ".WSY_SHOP.".weixin_commonshop_brand_supplys wcbs 
            INNER JOIN ".WSY_SHOP.".weixin_commonshop_applysupplys a ON a.user_id = wcbs.user_id and a.isvalid=true
            inner join ".WSY_USER.".weixin_users wu on wcbs.isvalid=true and wcbs.user_id=
                wu.id and wcbs.customer_id={$customer_id} and wcbs.brand_status=1 {$where}";
        $sql .= "  order by wcbs.asort_value desc,wcbs.creatime desc ".$limit;
        $result = $this->db->getAll($sql);
        if($result){
            foreach ($result as $k => $v){
                if($v['name'] == false) $result[$k]['name'] = '空';
                if($v['weixin_name'] == false) $result[$k]['weixin_name'] = '空';
                $result[$k]['show_name']= $result[$k]['name']."(".$result[$k]['weixin_name'].")";
                if($v['brand_tel'] == false) $result[$k]['brand_tel'] = '(空)';
            }
        }
        $pageCount=ceil($rcount_num/20);
        $res['result_list'] = $result;
        $res['rcount'] = $rcount_num;
        $res['pageCount'] = $pageCount;
        return $res;
    }

    //查询优惠券列表
    public function select_coupon_list($condition,$limit=null,$search_val=null,$search_type=null){
        $customer_id = $condition['customer_id'];
        $where = "";
        if($search_val != ""){
            $where .= " and (title like '%".$search_val."%')";
        }
        $rcount_num = 0;
        $query_num = "SELECT count(id) FROM weixin_commonshop_coupons where is_open = 1 and isvalid = 1 and is_showcouponlist = 1 and customer_id = {$customer_id} {$where}";
        $rcount_num = $this->db->getOne($query_num);
        $sql ="SELECT id,title,get_roles,use_roles FROM weixin_commonshop_coupons where is_open = 1 and isvalid = 1 and is_showcouponlist = 1 and customer_id = {$customer_id} {$where}";
        $sql .= "  order by id desc ".$limit;
        $result = $this->db->getAll($sql);
        $sql_shop = "SELECT a_name,b_name,c_name,d_name from weixin_commonshop_shareholder where customer_id='{$customer_id}'";
        $result_shop = $this->db->getRow($sql_shop);
        if($result){
            foreach ($result as $k=>$v){
                $result[$k]['title']    = htmlspecialchars($result[$k]['title']);
                $result[$k]['get_roles'] = '';
                if(strpos($v['get_roles'],"1")!==false) $result[$k]['get_roles'] .= '粉丝,';
                if(strpos($v['get_roles'],"2")!==false) $result[$k]['get_roles'] .= '推广员,';
                if(strpos($v['get_roles'],"3")!==false) $result[$k]['get_roles'] .= $result_shop['d_name'].',';
                if(strpos($v['get_roles'],"4")!==false) $result[$k]['get_roles'] .= $result_shop['c_name'].',';
                if(strpos($v['get_roles'],"5")!==false) $result[$k]['get_roles'] .= $result_shop['b_name'].',';
                if(strpos($v['get_roles'],"6")!==false) $result[$k]['get_roles'] .= $result_shop['a_name'];
                $result[$k]['get_roles'] = rtrim($result[$k]['get_roles'],',');
                $result[$k]['use_roles'] = '';
                if(strpos($v['use_roles'],"1")!==false) $result[$k]['use_roles'] .= '粉丝,';
                if(strpos($v['use_roles'],"2")!==false) $result[$k]['use_roles'] .= '推广员,';
                if(strpos($v['use_roles'],"3")!==false) $result[$k]['use_roles'] .= $result_shop['d_name'].',';
                if(strpos($v['use_roles'],"4")!==false) $result[$k]['use_roles'] .= $result_shop['c_name'].',';
                if(strpos($v['use_roles'],"5")!==false) $result[$k]['use_roles'] .= $result_shop['b_name'].',';
                if(strpos($v['use_roles'],"6")!==false) $result[$k]['use_roles'] .= $result_shop['a_name'];
                $result[$k]['use_roles'] = rtrim($result[$k]['use_roles'],',');
            }
        }
        $pageCount=ceil($rcount_num/20);
        $res['result_list'] = $result;
        $res['rcount'] = $rcount_num;
        $res['pageCount'] = $pageCount;
        return $res;
    }
	
	//根据活动ID查关联产品
	public function common_activity_product($condition,$limit,$activitie_id=0,$act_type){
		$customer_id = $condition['customer_id'];
		switch($act_type){
			case '1':	//续费产品
				$sql = "select pro.id,pro.name,pro.type_ids,pro.default_imgurl,pro.sell_count,pro.orgin_price,pro.now_price from promoter_renewal_products as prp 
				INNER JOIN ".WSY_PROD.".weixin_commonshop_products as pro on prp.product_id = pro.id 
				WHERE prp.isvalid = true and pro.isvalid = true and pro.isout=0 and pro.customer_id = ".$customer_id." and prp.renewal_id=".$activitie_id." order by prp.createtime desc ".$limit;
				break;
			case '2':	//限时产品
				$sql = "select p.id,p.name,p.default_imgurl,p.sell_count,p.orgin_price,ap.price as now_price from ".WSY_SHOP.".weixin_commonshop_restricted_purchase_products as ap 
				LEFT JOIN ".WSY_PROD.".weixin_commonshop_products as p on p.id = ap.product_id 
				where ap.isvalid = true and p.customer_id = '".$customer_id."' and ap.activity_id= '".$activitie_id."' order by ap.createtime desc ".$limit;
				break;
			case '3':	//商品满赠产品
				$sql = "select p.id,p.name,p.default_imgurl,p.sell_count,p.orgin_price,ep.exchange_price as now_price from weixin_commonshop_exchange_products as ep 
				inner join ".WSY_PROD.".weixin_commonshop_products as p on p.id = ep.pid 
				where ep.isvalid = true and p.customer_id = '".$customer_id."' and ep.exchange_id= '".$activitie_id."' order by ep.createtime desc ".$limit;
				break;
			case '4':	//积分产品
				$sql = "select ipi.pid,wxp.id,wxp.name,wxp.default_imgurl,wxp.sell_count,wxp.orgin_price,wxp.now_price from ".WSY_SHOP.".integral_product_index as ipi 
				inner join ".WSY_SHOP.".integral_activity as ia on ia.act_id = ipi.act_id 
				inner join ".WSY_PROD.".weixin_commonshop_products as wxp on ipi.pid = wxp.id 
				where ia.isvalid = true and ipi.cust_id = ".$customer_id." and ia.cust_id = ".$customer_id." and wxp.isvalid = true and ipi.act_id='".$activitie_id."' and wxp.customer_id= ".$customer_id." order by ia.add_time desc ".$limit;
				break;
			case '5':	//拼团产品
				$sql = "select p.id,p.name,p.default_imgurl,p.sell_count,p.orgin_price,cgp.price as now_price from collage_group_products_t as cgp 
				inner join ".WSY_PROD.".weixin_commonshop_products as p on p.id = cgp.pid 
				where cgp.isvalid = true and p.customer_id = '".$customer_id."' and cgp.activitie_id= '".$activitie_id."' order by cgp.createtime desc ".$limit;
				break;
			case '6':	//众筹产品
				$sql = "SELECT p.name,p.id,p.default_imgurl,p.sell_count,p.orgin_price,convert(cg.price/100,decimal(10,2)) as now_price FROM ".WSY_SHOP.".cr_goods AS cg 
			INNER JOIN ".WSY_PROD.".weixin_commonshop_products AS p ON p.id=cg.product_id 
			 WHERE p.customer_id = ".$customer_id." and cg.customer_id = ".$customer_id." and cg.activity_id='".$activitie_id."' and p.isvalid = true and cg.isvalid = true ".$limit;
				break;
			case '7':	//砍价产品
				$sql = "SELECT p.name,p.id,p.default_imgurl,kj.price as orgin_price,kj.minimum_price as now_price FROM ".WSY_SHOP.".kj_goods AS kj 
			INNER JOIN ".WSY_PROD.".weixin_commonshop_products AS p ON p.id=kj.product_no 
			 WHERE p.customer_id = ".$customer_id." and kj.customer_id = ".$customer_id." and kj.activity_id='".$activitie_id."' and p.isvalid = true and kj.isvalid = true ".$limit;
				break;
			case '8':	//礼包满赠产品
				$sql = "select p.id,p.package_name as name,p.default_head_imgurl as default_imgurl,p.cost_price as orgin_price,p.price as now_price from weixin_commonshop_activity_products as wcap 
				inner join ".WSY_SHOP.".package_list_t as p on p.id = wcap.product_id 
				where wcap.isvalid = true and p.customer_id = '".$customer_id."' and wcap.activity_id= '".$activitie_id."' order by wcap.createtime desc ".$limit;
				break;
		}
		$result = $this->db->getAll($sql);
		foreach($result as $key=>$v){
			$img_sql = "select imgurl from ".WSY_PROD.".weixin_commonshop_product_imgs where isvalid = true and product_id ='".$v['id']."' limit 1";
			$product_img = $this->db->getOne($img_sql);
			$result[$key]['product_img'] = $product_img;
		}
		return $result;
	}
	
	//根据活动ID查活动时间
	public function common_activity_time($condition,$activitie_id=0,$act_type){
		$customer_id = $condition['customer_id'];
		switch($act_type){
			case '2':	//限时产品
				$sql = "select time_start,time_end from ".WSY_SHOP.".weixin_commonshop_restricted_purchase where customer_id=".$customer_id." and isvalid = true and id=".$activitie_id;
				break;
			case '3':	//商品满赠产品
				$sql = "select starttime as time_start,endtime as time_end from  ".WSY_MARK.".weixin_commonshop_exchange where customer_id=".$customer_id." and isvalid = true and id=".$activitie_id;
				break;
			case '4':	//积分产品
				$sql = "select start_time,end_time from ".WSY_SHOP.".integral_activity where cust_id=".$customer_id." and isvalid = true and act_id=".$activitie_id;
				break;
			case '5':	//拼团产品
				$sql = "select start_time as time_start,end_time as time_end from ".WSY_MARK.".collage_activities_t where customer_id=".$customer_id." and isvalid = true and id=".$activitie_id;
				break;
			case '6':	//众筹产品
				$sql = "select activity_start_time as time_start,activity_end_time as time_end from ".WSY_MARK.".cr_activity where customer_id=".$customer_id." and isvalid = true and id=".$activitie_id;
				break;
			case '7':	//砍价产品
				$sql = "select activity_start_time as time_start,activity_end_time as time_end from ".WSY_MARK.".kj_activity where customer_id=".$customer_id." and isvalid = true and id=".$activitie_id;
				break;
			case '8':
				$sql = "select time_start,time_end ".WSY_MARK.".from weixin_commonshop_activities where customer_id=".$customer_id." and isvalid = true and id=".$activitie_id;
				break;
		}
		$result=$this->db->getRow($sql);
		return $result;
	}
	
    
	//艺人服务-二级分类
	public function select_yiren_list($condition,$limit=null,$search_val=null){
		$customer_id = $condition['customer_id'];
		$parent_id = $condition['parent_id'];
        $where = "";
        if($search_val != ""){
            $where .= " and (service_name like '%".$search_val."%')";
        }
		
		$rcount_num = 0;	//总数据量
		$query_num = "SELECT count(1) as rcount from ".WSY_O2O.".yr_service where isvalid=true and depth=3 and custom_id='".$customer_id."' and parent_id='".$parent_id."'".$where;
        $rcount_num = $this->db->getOne($query_num);
		
		$sql = "select id,service_name from ".WSY_O2O.".yr_service where isvalid=true and depth=3 and custom_id='".$customer_id."' and parent_id='".$parent_id."'".$where;
		
		$sql = $sql.$limit;
		$res = $this->db->getAll($sql);
		$pageCount=ceil($rcount_num/20);
		$result['result_list'] = $res;
		$result['rcount'] = $rcount_num;
		$result['pageCount'] = $pageCount;
		return $result;
	}
    //艺人服务-二级分类
    public function select_yiren_list2($condition,$limit=null,$search_val=null){
        $customer_id = $condition['customer_id'];
        $parent_id = $condition['parent_id'];
        $where = "";
        if($search_val != ""){
            $where .= " and (service_name like '%".$search_val."%')";
        }

        $rcount_num = 0;	//总数据量
       	$query_num = "SELECT count(1) as rcount from ".WSY_O2O.".yr_service where isvalid=true and depth=2 and custom_id='".$customer_id."' and parent_id='".$parent_id."'".$where;
        $rcount_num = $this->db->getOne($query_num);
		
		$sql = "select id,service_name from ".WSY_O2O.".yr_service where isvalid=true and depth=2 and custom_id='".$customer_id."' and parent_id='".$parent_id."'".$where;
		

        $sql = $sql.$limit;
        $res = $this->db->getAll($sql);

        $pageCount=ceil($rcount_num/20);
        $result['result_list'] = $res;
        $result['rcount'] = $rcount_num;
        $result['pageCount'] = $pageCount;
        return $result;
    }
    public function select_yiren_list3($condition,$limit=null,$search_val=null){
        $customer_id = $condition['customer_id'];
        $where = "";
        if($search_val != ""){
            $where .= " and (service_name like '%".$search_val."%')";
        }

        $rcount_num = 0;	//总数据量
        $query_num = "SELECT count(1) as rcount from ".WSY_O2O.".yr_service where isvalid=true and depth=1 and custom_id=".$customer_id.$where;
        $rcount_num = $this->db->getOne($query_num);

        $sql = "select id,service_name from ".WSY_O2O.".yr_service where isvalid=true and depth=1 and custom_id=".$customer_id.$where;

        $sql = $sql.$limit;
        $res = $this->db->getAll($sql);

        $pageCount=ceil($rcount_num/20);
        $result['result_list'] = $res;
        $result['rcount'] = $rcount_num;
        $result['pageCount'] = $pageCount;
        return $result;
    }
    


	//品牌订阅列表
    public function select_brandsubscribe_list($condition,$limit=null,$search_val=null){
        $customer_id = $condition['customer_id'];
        $where = "";
        if($search_val != ""){  //活动名称
            $where .= " and (name like '%".$search_val."%')";
        }

        $rcount_num = 0;	//总数据量
        $query_num = "SELECT count(1) as rcount from ".WSY_MARK.".brandsubscribe_activity where isvalid=true and customer_id=".$customer_id.$where;
        $rcount_num = $this->db->getOne($query_num);

        $sql = "select id,name,status from ".WSY_MARK.".brandsubscribe_activity where isvalid=true and customer_id=".$customer_id.$where;

        $sql = $sql.$limit;
        $res = $this->db->getAll($sql);
        if($res != false){
            foreach($res as $k => $v){
                switch($v['status']){
                    case 1;
                        $res[$k]['status_str'] = '待发布';
                        break;
                    case 2;
                        $res[$k]['status_str'] = '已发布';
                        break;
                    case 3;
                        $res[$k]['status_str'] = '结束';
                        break;
                    case 4;
                        $res[$k]['status_str'] = '手动结束';
                        break;
                }
            }
        }
        $pageCount=ceil($rcount_num/20);
        $result['result_list'] = $res;
        $result['rcount'] = $rcount_num;
        $result['pageCount'] = $pageCount;
        return $result;
    }
}

?>