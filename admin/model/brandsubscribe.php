<?php

/*
	数据库操作示例:

	$data = $this->db->getAll ($sql);
	$data = $this->db->getOne ($sql);
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


class model_brandsubscribe{
    var $db;
    var $model_common;
    function __construct()
    {
        $this->db = DB::getInstance();
        require_once('model/common.php');
        $this->model_common = new model_common();
    }  

    /*
     * 品牌订阅 获取添加产品列表
     * $Author: zqs
     * $2018-05-07  
     */
     public function get_add_relation($data){
        //分页设置 start
        $pageSize = 20;//每页多少条
        $pageNum = $data['pageNum']; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;        
        //分页设置 end

        if(!empty($data['product_id'])){            
            $product_id   = (int)$data['product_id'];
        }
        if($data['product_name']){            
            $product_name = mysql_escape_string($data['product_name']);
        }
        if($data['product_type']){
            $product_type = (int)$data['product_type'];
        }

        $parent_id = -1;
        $parent_name = ''; // 顶级分类
        $query = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='{$data['customer_id']}' AND parent_id=-1 AND is_shelves=1";

        $result = $this->db->getAll($query);
        $obj = '';

        foreach ($result as $type_k1 => $row) {
            $parent_id = $row['id'];
            $parent_name = $row['name'];
            $select = '';
            if($data['product_type'] == $parent_id){ $select = 'selected';} 

            $obj .= '<option value="'.$parent_id.'" '.$select.' >'.$parent_name.'</option>';

            $ch_id2 = -1;
            $ch_name2 = '';// 第二级分类
            $query_c2 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='{$data['customer_id']}' AND parent_id=$parent_id AND is_shelves=1";
            $result_c2= $this->db->getAll($query_c2);
            foreach ($result_c2 as $type_k2 => $row_c2) {
                $ch_id2 = $row_c2['id'];
                $ch_name2 = $row_c2['name'];
                if($ch_id2 != -1){
                    $select2 = '';
                    
                    if($data['product_type'] == $ch_id2){ $select2 =  'selected';}

                    $obj .= '<option value="'.$ch_id2.'" '.$select2.' >'.'&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name2.'</option>';

                    $ch_id3 = -1;
                    $ch_name3 = '';// 第三级分类
                    $query_c3 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='{$data['customer_id']}' AND parent_id={$ch_id2} AND is_shelves=1";
                    $result_c3= $this->db->getAll($query_c3);
                    foreach ($result_c3 as $type_k3 => $row_c3) {
                        $ch_id3 = $row_c3['id'];
                        $ch_name3 = $row_c3['name'];
                        $select3 = '';

                        if($data['product_type'] == $ch_id3){ $select3 = 'selected';}
                        
                        $obj .= '<option value="'.$ch_id3.'" '.$select3.' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name3.'</option>';

                        $ch_id4 = -1;
                        $ch_name4 = '';// 第四级分类
                        $query_c4 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='{$data['customer_id']}' AND parent_id={$ch_id3} AND is_shelves=1";
                        $result_c4= $this->db->getAll($query_c4);
                        foreach ($result_c4 as $type_k4 => $row_c4) {
                            $ch_id4 = $row_c4['id'];
                            $ch_name4 = $row_c4['name'];
                            $select4 = '';

                            if($data['product_type'] == $ch_id4){ $select4 = 'selected';}
                        
                            $obj .= '<option value="'.$ch_id4.'" '.$select4.' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name4.'</option>';
                        }
                    }
                }
            }
        }

        //查询未结束的活动id
        $time_now = date('Y-m-d H:i:s',time());
        $select_activity_sql = "select id from ".WSY_MARK.".brandsubscribe_activity where customer_id='{$data['customer_id']}' and isvalid=true and status=2 and ((end_time > '{$time_now}' and time_type = 1) or time_type = 2)";
        $select_activity = $this->db->getAll($select_activity_sql);

        $re_str2 = '';
        if($select_activity) {
            $re_s2 = '';

            foreach ($select_activity as $re_k2 =>$re_v2) {
                $re_s2 = $re_s2.','.$re_v2['id'];
            }

            $re_s2 = substr($re_s2,1);

            $re_str2 = " and activity_id in ({$re_s2}) ";
        }

        $sql_t = "select product_id from ".WSY_MARK.".brandsubscribe_relate_prod where customer_id='".$data['customer_id']."' and isvalid = 1 {$re_str2}";

        $re = $this->db->getAll($sql_t);
        
        $re_str = '';
        if($re) {
            $re_s = '';

            foreach ($re as $re_k =>$re_v) {
                $re_s = $re_s.','.$re_v['product_id'];
            }

            $re_s = substr($re_s,1);

            $re_str = " and id not in ({$re_s}) ";
        }

        $activity_arr = array();

        $sql_c = "SELECT count(id) from weixin_commonshop_products where customer_id='{$data['customer_id']}' and isvalid = 1 and isout = 0 {$re_str}";
        
        $sql = "SELECT id,name,orgin_price,now_price,default_imgurl,type_ids,storenum from weixin_commonshop_products where customer_id='{$data['customer_id']}' and isvalid = 1 and isout = 0 {$re_str}";

        /************** 搜索条件 start ******************/
        if($product_id!=-1){
            $sql_c .= " and id=".$product_id;
            $sql   .= " and id=".$product_id;
        }
        if($product_name!=""){
            $sql_c .= " and name like '%".$product_name."%'";
            $sql   .= " and name like '%".$product_name."%'";
        }
        if($product_type!=-1){
            $sql_c .= " and type_ids like '%".$product_type."%'";
            $sql   .= " and type_ids like '%".$product_type."%'";
        }
        /************** 搜索条件 end ******************/

        $sql_c .= " order by id desc ";
        $sql   .= " order by id desc ";
        
        $arr = $this->db->getRow($sql_c);//总共多少条记录

        $pageCount = ceil($arr['count(id)']/$pageSize);//总页数

        $sql .= " limit ".$start.",".$end;
        
        $res = $this->db->getAll($sql);
        
        foreach ($res as $k=>$v) {
  
            $imgurl = $v['default_imgurl'];

            if(empty($imgurl)){
                $query_img ="SELECT imgurl from weixin_commonshop_product_imgs where isvalid=true and product_id={$v['id']} limit 0,1";

                $data_imgurl = $this->db->getRow($query_img);

                $imgurl = $data_imgurl['imgurl'];
            }

            $type_ids = $v['type_ids'];
            
            if(!empty($type_ids)){
                if(strpos($type_ids,",") === 0){
                    $type_ids = substr($type_ids,1);
                }
                if(substr($type_ids,strlen($type_ids)-1) == ","){
                    $type_ids = substr($type_ids,0,strlen($type_ids)-1);
                }
               
                if(!empty($type_ids)){
                    $type_ids = str_replace(',,',',',$type_ids);
                    
                    $query = "SELECT name from weixin_commonshop_types where isvalid=true and id in ({$type_ids})  ORDER BY create_parent_id asc ";
                    
                    $data_type = $this->db->getAll($query);
                    foreach ($data_type as $key=>$val) {
                        $typename = $typename."/".$val['name'];
                    }
                }
            }

            $res[$k]['default_imgurl']  = $imgurl;
            $res[$k]['typename']        = $typename;
        }

        $return['type'] = $obj;
        $return['pageCount'] = $pageCount;
        $return['activity_arr'] = $res;
        return $return;
     }

    /*
     * 品牌订阅 获取活动详情配置
     * $Author: zqs
     * $2018-05-07  
     */
    public function get_activity($data){
    	$sql    = "select name,start_time,end_time,time_type,package_type,publish,status,limit_time from ".WSY_MARK.".brandsubscribe_activity where customer_id='".$data['customer_id']."' and id='".$data['id']."' and isvalid='1'";
    	$result = $this->db->getRow($sql);
    	return $result;
    }

    /*
     * 品牌订阅 获取活动产品配置
     * $Author: zqs
     * $2018-05-07  
     */
    public function get_relate_prod($param){

        
        //分页设置 start
        $pageSize = 20;//每页多少条
        $pageNum = $param['pageNum']; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;
        //分页设置 end

        $activity_arr = array();

        $sql = "select count(id) from ".WSY_MARK.".brandsubscribe_relate_prod where activity_id=".$param['activity_id']." and isvalid = 1 ";
        
        $arr = $this->db->getRow($sql);//总共多少条记录

        $pageCount = ceil($arr['count(id)']/$pageSize);//总页数

        $sql = "select id,product_id,activity_price,total_limit_num,day_limit_num,month_limit_num,is_confirm from ".WSY_MARK.".brandsubscribe_relate_prod where activity_id=".$param['activity_id']." and isvalid = 1 ORDER BY createtime desc";

        $sql .= " limit ".$start.",".$end;
       
        $res = $this->db->getAll($sql);

        // //查询出活动的礼包时限类型 1自然月2自然年3自定义天数
        // $type_sql = "select package_type from ".WSY_MARK.".brandsubscribe_activity where id='".$param['activity_id']."' and isvalid=true";
        // $package_type = $this->db->getOne($type_sql);

        foreach ($res as $k=>$v) {
            $sql = "SELECT id,name,now_price,default_imgurl,type_ids from ".WSY_PROD.".weixin_commonshop_products where id='{$v['product_id']}' and isvalid = 1 ";

            $data = $this->db->getRow($sql);

            $imgurl = $data['default_imgurl'];

            if(empty($imgurl)){
                $query_img ="SELECT imgurl from ".WSY_PROD.".weixin_commonshop_product_imgs where isvalid=true and product_id='{$data['id']}' limit 0,1";

                $data_imgurl = $this->db->getRow($query_img);

                $imgurl = $data_imgurl['imgurl'];
            }

            $type_ids = $data['type_ids'];
            
            if(!empty($type_ids)){
                if(strpos($type_ids,",") === 0){
                    $type_ids = substr($type_ids,1);
                }
                if(substr($type_ids,strlen($type_ids)-1) == ","){
                    $type_ids = substr($type_ids,0,strlen($type_ids)-1);
                }
               
                if(!empty($type_ids)){
                    $type_ids = str_replace(',,',',',$type_ids);
                    
                    $query = "SELECT name from weixin_commonshop_types where isvalid=true and id in ({$type_ids})  ORDER BY create_parent_id asc ";
                    
                    $data_type = $this->db->getAll($query);
                    foreach ($data_type as $key=>$val) {
                        $typename = $typename."/".$val['name'];
                    }
                }
            }

            // //查询出商城大礼包
            // $sql_pack  = "select id,package_name from ".WSY_SHOP.".package_list_t where customer_id='".$param['customer_id']."' and isvalid=true and isout=0";
            // $res_pack  = $this->db->getAll($sql_pack);

            // //查询出该活动被关联的大礼包id
            // $pack_prod_sql = "select package_id from ".WSY_MARK.".brandsubscribe_relate_prod where customer_id='".$param['customer_id']."' and activity_id='".$param['activity_id']."' and package_id!=-1 and isvalid=true";
            // $pack_prod_res = $this->db->getAll($pack_prod_sql);
            // $pack_prod     =  array_column($pack_prod_res,'package_id');

            // //循环出大礼包列表
            // foreach ($res_pack as $pack_k => $pack_v) {
            // 	$select = '';
            // 	$is_in = in_array($pack_v['id'],$pack_prod);
            // 	if ($is_in == false || $v['package_id'] == $pack_v['id']){
            // 	if ($v['package_id'] == $pack_v['id']) {
            // 		$select = 'selected';
            // 	}
            // 	$res[$k]['type']  .= '<option value="'.$pack_v['id'].'" '.$select.' >'.$pack_v['package_name'].'</option>';
           	// }
            // }

            // if ($package_type != 3) {
            // 	$res[$k]['limit_time']  = $package_type;
            // }
            $res[$k]['name']            = $data['name'];
            $res[$k]['now_price']       = $data['now_price'];
            $res[$k]['default_imgurl']  = $imgurl;
            $res[$k]['typename']        = $typename;
        }
        $return['pageCount'] = $pageCount;
        $return['activity_arr'] = $res;
        return $return;
    }

    /*
     * 品牌订阅 添加活动产品关联
     * $Author: zqs
     * $2018-05-07  
     */
     public function add_related($param){
     	$product_id = explode(",",$param['idsStr']);
     	foreach ($product_id as $k => $v) {
     		$activity_price_sql = "select now_price from ".WSY_PROD.".weixin_commonshop_products where customer_id='{$param['customer_id']}' and id='{$product_id[$k]}' and isvalid=true";
     		$activity_price = $this->db->getOne($activity_price_sql);
     		$data['customer_id'] = $param['customer_id'];//商家号
     		$data['activity_id'] = $param['activity_id'];//品牌订阅活动id
     		$data['product_id']  = $product_id[$k];//产品id
     		$data['package_id'] = -1;//关联大礼包id
     		$data['limit_time'] = '30';//礼包时限，自然月年为-1
     		$data['activity_price'] = $activity_price;//活动价格
     		$data['total_limit_num'] = '1';//限购数量
     		$data['day_limit_num'] = '-1';//每人每天限制数量，-1不限制
     		$data['day_limit_num'] = '-1';//每人每月限制数量，-1不限制
     		$data['is_confirm'] = '0';//第一次添加产品时为0，保存后为1
     		$data['createtime'] = date('Y-m-d H:i:s',time());//创建时间
     		$res = $this->db->autoExecute(WSY_MARK.'.brandsubscribe_relate_prod',$data, 'insert');
     	}
	        $return['errcode'] = 1;
	        $return['errmsg'] = "添加成功！";
     		return $return;
     }

    /*
     * 品牌订阅 删除活动产品关联
     * $Author: zqs
     * $2018-05-07  
     */
     public function del_related($param){
     	$sql = "update ".WSY_MARK.".brandsubscribe_relate_prod set isvalid=false where customer_id='".$param['customer_id']."' and id='".$param['id']."'";
     	$res = $this->db->query($sql);
     	if ($res) {
	        $return['errcode'] = 1;
	        $return['errmsg'] = "删除成功！";
     	}else{
	        $return['errcode'] = 400030;
	        $return['errmsg'] = "删除失败！"; 		
     	}
     		return $return;
     }


    /*
     * 品牌订阅 修改关联产品属性
     * $Author: zqs
     * $2018-05-07  
     */
     public function update_related($param){
     	$sql = "update ".WSY_MARK.".brandsubscribe_relate_prod set ".$param['str']."='".$param['obj']."' where customer_id='".$param['customer_id']."' and id='".$param['id']."'";
     	$res = $this->db->query($sql);
     	if ($res) {
	        $return['errcode'] = 1;
	        $return['errmsg'] = "修改成功！";
     	}else{
	        $return['errcode'] = 400030;
	        $return['errmsg'] = "修改失败！"; 		
     	}
     		return $return;
     }

    // /*
    //  * 品牌订阅 修改关联产品关联礼包
    //  * $Author: zqs
    //  * $2018-05-08  
    //  */
    //  public function upd_pack($param){
    //  	//查询出已被关联的礼包
    //  	$sql = "select package_id from ".WSY_MARK.".brandsubscribe_relate_prod where customer_id='".$param['customer_id']."' and isvalid=true and id!='".$param['id']."' and package_id!=-1 and activity_id='".$param['activity_id']."'";
    //  	$pack_id_res = $this->db->getAll($sql);
    //  	$pack_id = array_column($pack_id_res, 'package_id');
    //  	$package_id = $param['package_id'];
    //  	$is_pack =  in_array($package_id,$pack_id);
    //  	if ($is_pack) {
	   //      $return['errcode'] = 40031;
	   //      $return['errmsg'] = "该礼包已被其他产品关联！";
    //  	}else{
    //  		$sql2 = "update ".WSY_MARK.".brandsubscribe_relate_prod set package_id='".$package_id."' where customer_id='".$param['customer_id']."' and id='".$param['id']."'";
    //  		$res = $this->db->query($sql2);
	   //   	if ($res) {
		  //       $return['errcode'] = 1;
		  //       $return['errmsg'] = "修改成功！";
		  //       $return['package_id'] = $package_id;
		  //       $return['pack_id'] = $pack_id;
	   //   	}else{
		  //       $return['errcode'] = 400030;
		  //       $return['errmsg'] = "修改失败！"; 		
	   //   	}
    //  	}
    //  	return $return;
    //  }

	/**添加/编辑活动保存
	* @author  HMJ-V384
	* @param  
	* @version  2018-05-07
	* @return  
	* @var  
	*/    
	public function activity_save($data = array()){
		$res = array('errcode' => 0,'errmsg' => '保存成功！','data' =>'' );
  //       $now_time = date('Y-m-d H:i:s');-------------------------------------------------------------------------不做限制
		// if($data['publish'] == 1) { //如果设定为发布则查询是否有活动在发布
		// 	$sql = "select id,name from ".WSY_MARK.".brandsubscribe_activity where customer_id='".$data['customer_id']."' and status='2' AND end_time>'{$now_time}' and isvalid='1'";
		// 	$res1 = $this->db->getRow($sql);
		// 	if($res1 && $res1['id'] != $data['activity_id']) {
		// 		$res = array('errcode' => 400,'errmsg' => '已有活动正在发布中，不能再发布！','data' =>$res1 );
		// 		return $res;
		// 	}
		// }


		if(!empty($data['activity_id']) && $data['activity_id'] != -1) {
			$id = $data['activity_id'];
			unset($data['activity_id']);
			$res2 = $this->db->autoExecute(WSY_MARK.".`brandsubscribe_activity`", $data, 'update',"id = '{$id}' and isvalid='1'") ;			

		} else {
			unset($data['activity_id']);
			$data['create_time'] = date('Y-m-d H:i:s');
			$res2 = $this->db->autoExecute(WSY_MARK.".`brandsubscribe_activity`", $data, 'insert') ;
			$sql = "select id from ".WSY_MARK.".brandsubscribe_activity where customer_id='".$data['customer_id']."' and isvalid='1' order by id desc"; //取出刚插入信息ID	
			$res2 = $this->db->getRow($sql);	
		}
		$res['data'] = $res2;
		return $res;
	}

    /*
     * 品牌订阅 获取活动概况列表
     * $Author: lpx
     * $2018-05-07  
     */
    function get_activities($param){
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum = $param['pageNum'] ? : 1; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;        
        //分页设置 end
        $activity_arr = array();
        $activity_id = -1;
        $activity_name = "";
        $starttime = -1;
        $endtime = -1;
        $activity_status = -1;
        $customer_id = $param['customer_id'];
        
        if(!empty($param['activity_id'])){            
            $activity_id = (int)$param['activity_id'];
        }
        if($param['activity_name']){            
            $activity_name = mysql_escape_string($param['activity_name']);
        }
        if($param['starttime']){
            $starttime = $param['starttime'];
        }
        if($param['endtime']){
            $endtime = $param['endtime'];
        }
        if($param['activity_status']){
            $activity_status = $param['activity_status'];
        }

        $sql = "SELECT bsa.id,bsa.name,bsa.start_time,bsa.end_time,bsa.status,bsa.time_type,count(distinct(bsrp.product_id)) AS productnum,count(distinct(bsat.user_id)) AS usernum 
        FROM ".WSY_MARK.".`brandsubscribe_activity` bsa 
        LEFT JOIN ".WSY_MARK.".`brandsubscribe_relate_prod` bsrp ON bsa.id=bsrp.activity_id AND bsrp.isvalid=1 
        LEFT JOIN ".WSY_MARK.".`brandsubscribe_authorize` bsat ON bsa.id=bsat.activity_id AND bsat.isvalid=1 
        WHERE bsa.customer_id=".$customer_id." AND bsa.isvalid=1";

        /************** 搜索条件 start ******************/
        if($activity_id!=-1){
            $sql .= " AND bsa.id like '%".$activity_id."%'";
        }
        if($activity_name!=""){
            $sql .= " AND bsa.name like '%".$activity_name."%'";
        }
        if($starttime!=-1){
            $sql .= " AND bsa.start_time >= '".$starttime."'";
        }
        if($endtime!=-1){
            $sql .= " AND bsa.end_time <= '".$endtime."'";
            $sql .= " AND bsa.time_type = '1'";
        }
        
        if($activity_status!=-1){
            if($activity_status == 'inprocessing') {//活动状态为进行中
                $sql .= " AND bsa.status = '2' ";       
                $nowtime = time();
                if ($starttime==-1) {
                    $sql .= " AND bsa.start_time <= from_unixtime({$nowtime})"; 
                }
                if ($endtime==-1) {
                    $sql .= " AND ((bsa.end_time >= from_unixtime({$nowtime})) OR bsa.time_type = 2)";
                }
            } else if($activity_status == 2) {//活动状态为已发布
                $nowtime = time();
                $sql .= " AND (bsa.end_time > from_unixtime({$nowtime}) AND bsa.time_type = 1 OR bsa.time_type = 2) ";                 
                $sql .= " AND bsa.status = '{$activity_status}' ";
            } else if($activity_status == 3){//活动状态为结束
                $nowtime = time();
                $sql .= " AND ((bsa.end_time <= from_unixtime({$nowtime}) AND bsa.status = '2' AND bsa.time_type = '1') OR bsa.status='3')"; 
            } else {//活动状态为待发布或者手动结束
                $sql .= " AND bsa.status = '{$activity_status}'";
            }
        }

        /************** 搜索条件 end ******************/

        $sql .= " GROUP BY bsa.id";

        $arr = $this->db->getAll($sql);//查出所有有效的活动概况
        $activity_count = count($arr);//总共多少条记录
        $pageCount = ceil($activity_count/$pageSize);//总页数

        if( $param['pageNum'] > 0 ){
            $sql .= " order by bsa.id desc limit ".$start.",".$end;
        }

        $activity_arr = $this->db->getAll($sql);

        $return['pageCount'] = $pageCount;
        $return['activity_arr'] = $activity_arr;
        return $return;
    }

	/**活动管理查询
	* @author  HMJ-V384
	* @param  
	* @version  2018-05-07
	* @return  
	* @var  
	*/   
    function activity_management($param){
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum  = $param['pageNum'] ? : 1; //当前页,1开始
        $start    = ($pageNum-1)*$pageSize;
        $end      = $pageSize;

        //分页设置 end
        $activity_arr = array();
        $id        = -1;
        $name           = -1;
        $start_time     = -1;
        $end_time    = -1;
        $status      = -1;
        $customer_id    = $param['customer_id'];

        if(!empty($param['id'])){
            $id     = (int)$param['id'];
        }
        if($param['name']){
            $name        = mysql_escape_string($param['name']);
        }
        if($param['start_time']){
            $start_time  = mysql_escape_string($param['start_time']);
        }
        if($param['end_time']){
            $end_time = mysql_escape_string($param['end_time']);
        }
        if($param['status']){
        	if($param['status'] == 'ongoing') {
        		$status   = $param['status']; 
        	} else {
             	$status   = (int)$param['status'];       		
        	}
        }

        $sql = "select a.id,a.name,a.start_time,a.end_time,a.status,a.time_type,pacl.package_name 
         FROM ".WSY_MARK.".`brandsubscribe_activity` AS a 
         INNER JOIN ".WSY_SHOP.".`package_list_t` AS pacl ON pacl.id=a.package_id AND pacl.isvalid=true 
         WHERE a.customer_id='".$customer_id."' AND a.isvalid=true ";
        /************** 搜索条件 start ******************/
        if($id!=-1){
            $sql .= " AND a.id like '%".$id."%'";
        }
        if($name!=-1){
            $sql .= " AND a.name like '%".$name."%'";
        }
        if($start_time!=-1){
            $sql .= " AND a.start_time >= '".$start_time."'";
        }
        if($end_time!=-1){
            $sql .= " AND a.end_time <= '".$end_time."'";
            $sql .= " AND a.time_type = '1'"; 
        }
        if($status!=-1){
			if($status == 'ongoing') {
				$nowtime = date('Y-m-d H:i:s');
	            $sql .= " AND (a.end_time > '{$nowtime}' AND a.time_type = 1 OR a.time_type = 2)";
	            $sql .= " AND a.start_time <= '{$nowtime}' ";	
	            $sql .= " AND a.status = '2' ";		
			} else if($status == 2) {
                $nowtime = date('Y-m-d H:i:s');
                $sql .= " AND (a.end_time > '{$nowtime}' AND a.time_type = 1 OR a.time_type = 2) ";                 
                $sql .= " AND a.status = '{$status}' ";
            }  else if($status == 3) {
                $nowtime = date('Y-m-d H:i:s');
                $sql .= " AND ((a.end_time <= '{$nowtime}' "; 
                $sql .= " AND a.status = '2' AND a.time_type = 1) OR a.status='3')";
			} else {
                $sql .= " AND a.status = '{$status}' ";
            }
        }
        /************** 搜索条件 end ******************/
        $activity_total = $this->db->getAll($sql);
        $activity_count = count($activity_total);//总共多少条记录

        if( $param['pageNum'] > 0 ){
            $sql .= " order by a.id desc limit ".$start.",".$end;
        }

        $activity_arr = $this->db->getAll($sql);
        $pageCount = ceil($activity_count/$pageSize);//总页数

        $return['pageCount'] = $pageCount;
        $return['activity_arr'] = $activity_arr;
        return $return;
    }

	/**活动发布/终止/删除处理
	* @author  HMJ-V384
	* @param  
	* @version  2018-05-08
	* @return  
	* @var  type: 0发布5终止2删除 | id | customer_id
	*/    
    function activity_deal($data){

    	$res = $this->get_activity($data);
    	if(!$res) {
    		$ret = array('errcode' => 400,'errmsg' => '读取活动信息失败！','data' => $data);
    		return $ret;
    	}
        $now_time = date('Y-m-d H:i:s');
        switch ($data['type']) {
        	case '0':

				// $sql = "select id,name from ".WSY_MARK.".brandsubscribe_activity where customer_id='".$data['customer_id']."' and status='2' AND end_time>'{$now_time}' and isvalid='1'";
				// $res1 = $this->db->getRow($sql);
				// if($res1 && $res1['id'] != $data['id']) {
				// 	$ret = array('errcode' => 400,'errmsg' => '已有活动正在发布中，不能再发布！','data' =>$data );
				// 	return $ret;
				// }
        		if($res['status'] != 1){
					$ret = array('errcode' => 400,'errmsg' => '该活动不在待发布状态，无法发布！','data' =>$data );
					return $ret;
        		}
        		$sql_deal = "update ".WSY_MARK.".brandsubscribe_activity set status='2' where customer_id='".$data['customer_id']."' and id='".$data['id']."'";
        		$mark = '发布';
        		break;
        	case '5':
        		if($res['status'] != 2){
					$ret = array('errcode' => 400,'errmsg' => '该活动不在已发布状态，终止异常！','data' =>$data );
					return $ret;
        		}        	
        	    $sql_deal = "update ".WSY_MARK.".brandsubscribe_activity set status='4' where customer_id='".$data['customer_id']."' and id='".$data['id']."'";
        	    $mark = '终止'; 
                $this->activity_end_sendmessage(array("customer_id"=>$data['customer_id'],"activity_id"=>$data['id']));       	
        		break;
        	case '2':
        	    $sql_deal = "update ".WSY_MARK.".brandsubscribe_activity set isvalid=false where customer_id='".$data['customer_id']."' and id='".$data['id']."'";
        	    $mark = '删除';
        		break;        		        	
        	default:
        		 $ret = array('errcode' => 400,'errmsg' => '活动编码异常！','data' => $data);
        		 return $ret;
        		break;
        }
        $res_fin = $this->db->query($sql_deal);
        if(!$res_fin){
        	$ret = array('errcode' => 400,'errmsg' => '系统异常！','data' => '');
        	return $ret;
        }
    	$ret = array('errcode' => 0,'errmsg' => $mark.'成功！','data' => $res);
    	return $ret;

    }     

    /*
     * 品牌订阅 获取活动明细列表
     * $Author: lpx
     * $2018-05-09  
     */
    function get_activity_details($param){
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum = $param['pageNum'] ? : 1; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;        
        //分页设置 end
        $activity_arr = array();
        $activity_id = -1;
        $user_name = "";
        $user_id = -1;
        $customer_id = $param['customer_id'];
        
        if(!empty($param['activity_id'])){            
            $activity_id = (int)$param['activity_id'];
        }
        if($param['user_name']){            
            $user_name = mysql_escape_string($param['user_name']);
        }
        if($param['user_id']){
            $user_id = $param['user_id'];
        }

        /************** 搜索条件 end ******************/

        $sql1 = "SELECT user_id,wu.name 
        FROM ".WSY_MARK.".`brandsubscribe_authorize` AS bsat 
        INNER JOIN ".WSY_USER.".`weixin_users` AS wu ON wu.id = bsat.user_id AND wu.isvalid = 1 
        WHERE bsat.customer_id = ".$customer_id." AND bsat.isvalid = 1";

        /************** 搜索条件 start ******************/
        if($activity_id!=-1){
            $sql1 .= " AND bsat.activity_id = ".$activity_id;
        }
        if($user_name!=""){
            $sql1 .= " AND wu.name like '%".$user_name."%'";
        }
        if($user_id!=-1){
            $sql1 .= " AND bsat.user_id like '%".$user_id."%'";
        }
        /************** 搜索条件 end ******************/
        $sql1 .= " GROUP BY bsat.user_id";
        $activity_arr = $this->db->getAll($sql1);//查出该活动下的用户ID和用户名
        $activity_count = count($activity_arr);//总共多少条记录
        $pageCount = ceil($activity_count/$pageSize);//总页数

        if( $param['pageNum'] > 0 ){
            $sql1 .= " order by user_id desc limit ".$start.",".$end;
        }

        $activity_arr = $this->db->getAll($sql1);

        foreach ($activity_arr as $k1 => $v1) {
            $sql2 = "SELECT bsat.id,bsat.user_id,bsrp.product_id,bsat.end_time AS bsat_end_time,bs_relate_prod_id,bsrp.total_limit_num,bsa.end_time AS bsa_end_time,bsa.time_type,bsa.status 
            FROM weixin_commonshop_orders AS ord 
            INNER JOIN ".WSY_MARK.".`brandsubscribe_relate_prod` AS bsrp ON bsrp.id = ord.bs_relate_prod_id AND bsrp.isvalid = '1' 
            INNER JOIN ".WSY_MARK.".`brandsubscribe_authorize` AS bsat ON bsat.activity_id = bsrp.activity_id AND bsat.isvalid = '1' 
            INNER JOIN ".WSY_MARK.".`brandsubscribe_activity` AS bsa ON bsa.id = bsat.activity_id AND bsa.isvalid = '1' 
            WHERE bsat.activity_id = '".$activity_id."' AND ord.customer_id = '".$customer_id."' AND ord.isvalid = '1' AND ord.user_id = '".$v1['user_id']."' AND ord.paystatus = '1' 
            GROUP BY bs_relate_prod_id 
            ORDER BY ord.id DESC";
            $res2 = $this->db->getAll($sql2);//查出该活动下该用户的订阅产品详情

            $activity_arr[$k1]['products_subscriber_num'] = count($res2);//初始化该活动下该用户的订阅产品的总数
            $activity_arr[$k1]['insubscription'] = 0;//初始化该活动下该用户的订阅中的产品数
            $activity_arr[$k1]['be_continued'] = 0;//初始化该活动下该用户的已失效的产品数
            foreach ($res2 as $k2 => $v2) {
                //计算剩余可购数量-先查询已购数量
                $sql3 = "SELECT IFNULL(sum(ord.rcount), 0) buy_sum 
                FROM weixin_commonshop_orders AS ord 
                INNER JOIN ".WSY_MARK.".`brandsubscribe_relate_prod` AS bsrp ON bsrp.id = ord.bs_relate_prod_id AND bsrp.isvalid = '1' 
                INNER JOIN ".WSY_MARK.".`brandsubscribe_authorize` AS bsat ON bsat.activity_id = bsrp.activity_id AND bsat.isvalid = '1' 
                INNER JOIN ".WSY_MARK.".`brandsubscribe_activity` AS bsa ON bsa.id = bsat.activity_id AND bsa.isvalid = '1' 
                WHERE bsat.activity_id = '".$activity_id."' AND bsat.customer_id = '".$customer_id."' AND ord.isvalid = '1' AND ord.user_id = '".$v1['user_id']."' AND ord.paystatus = '1' 
                GROUP BY bs_relate_prod_id";  

                $my_subscribe_order = $this->db->getRow($sql3);//查出该活动下该用户每个产品的已购数量
                
                if($my_subscribe_order['buy_sum'] >= $v2['total_limit_num']) {
                    $v2['num_left'] = 0;
                } else {
                    $v2['num_left'] = $v2['total_limit_num'] - $my_subscribe_order['buy_sum'];
                }

                $bsat_end_time = $v2['bsat_end_time'];
                $bsa_end_time = $v2['bsa_end_time'];
                $time_type = $v2['time_type'];
                $status = $v2['status'];
                $num_left = $v2['num_left'];
               
                if ($time_type == 1) {//活动时间为自定义时间
                    if (strtotime($bsa_end_time) >= time() && $status == 2 ) {
                        if (strtotime($bsat_end_time) < time() || $num_left == 0) {
                            $activity_arr[$k1]['be_continued'] ++;//已失效的产品数量加一
                        } else {
                            $activity_arr[$k1]['insubscription'] ++;//订阅中产品数量加一
                        }
                    } 
                } else {//活动时间为永久
                    if ($status == 2) {
                        if (strtotime($bsat_end_time) < time() || $num_left == 0) {
                            $activity_arr[$k1]['be_continued'] ++;//已失效的产品数量加一
                        } else {
                            $activity_arr[$k1]['insubscription'] ++;//订阅中产品数量加一
                        }
                    }
                }
            }
        }

        $return['pageCount'] = $pageCount;
        $return['activity_arr'] = $activity_arr;
        return $return;
    }

    /*
     * 品牌订阅 获取用户订阅产品明细列表
     * $Author: lpx
     * $2018-05-11  
     */
    function get_user_product($param){
        $pageSize = 20;//每页多少条
        $pageNum = $param['pageNum']; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;
        //分页设置 end

        $activity_arr = array();
        $activity_id = $param['activity_id'];
        $user_id = $param['user_id'];
        $product_name = "";
        $product_id = -1;
        $product_status = -1;
        $customer_id = $param['customer_id'];
        
        if($param['product_name']){            
            $product_name = mysql_escape_string($param['product_name']);
        }
        if($param['product_id']){
            $product_id = $param['product_id'];
        }
        if($param['product_status']){
            $product_status = $param['product_status'];
        }

        $sql = "SELECT bsat.id AS authorize_id,bsat.end_time AS aut_end_time,bsrp.day_limit_num,bsa.limit_time,bsrp.activity_price,bsrp.total_limit_num,bsrp.id AS relate_prod_id,bsa.end_time AS bsa_end_time,bsa.status,bsa.time_type,bsa.package_type,pro.name,pro.id AS product_id,pro.type_ids,plt.package_name 
        FROM weixin_commonshop_orders AS ord 
        INNER JOIN ".WSY_MARK.".`brandsubscribe_relate_prod` AS bsrp ON bsrp.id = ord.bs_relate_prod_id AND bsrp.isvalid = '1' 
        INNER JOIN ".WSY_MARK.".`brandsubscribe_authorize` AS bsat ON bsat.activity_id = bsrp.activity_id AND bsat.isvalid = '1' 
        INNER JOIN ".WSY_MARK.".`brandsubscribe_activity` AS bsa ON bsa.id = bsat.activity_id AND bsa.isvalid = '1' 
        INNER JOIN ".WSY_PROD.".`weixin_commonshop_products AS pro ON pro.id = ord.pid AND pro.isvalid = '1' 
        INNER JOIN ".WSY_SHOP.".`package_list_t`` AS plt ON plt.id = bsa.package_id AND plt.isvalid = '1' 
        WHERE bsat.activity_id = '".$activity_id."' AND ord.customer_id = '".$customer_id."' AND ord.isvalid = '1' AND ord.user_id = '".$user_id."' AND ord.paystatus = '1'"; 

        /************** 搜索条件 start ******************/
        if($product_name!=""){
            $sql .= " AND pro.name like '%".$product_name."%'";
        }
        if($product_id!=-1){
            $sql .= " AND bsrp.product_id like '%".$product_id."%'";
        }
        if($product_status!=-1){
            $nowtime = time();

            $sql2 = "SELECT bsrp.id AS relate_prod_id,bsrp.total_limit_num 
            FROM weixin_commonshop_orders AS ord 
            INNER JOIN ".WSY_MARK.".`brandsubscribe_relate_prod` AS bsrp ON bsrp.id = ord.bs_relate_prod_id AND bsrp.isvalid = '1' 
            INNER JOIN ".WSY_MARK.".`brandsubscribe_authorize` AS bsat ON bsat.activity_id = bsrp.activity_id AND bsat.isvalid = '1' 
            INNER JOIN ".WSY_MARK.".`brandsubscribe_activity` AS bsa ON bsa.id = bsat.activity_id AND bsa.isvalid = '1' 
            INNER JOIN ".WSY_PROD.".`weixin_commonshop_products AS pro ON pro.id = ord.pid AND pro.isvalid = '1' 
            INNER JOIN ".WSY_SHOP.".`package_list_t`` AS plt ON plt.id = bsa.package_id AND plt.isvalid = '1' 
            WHERE bsat.activity_id = '".$activity_id."' AND ord.customer_id = '".$customer_id."' AND ord.isvalid = '1' AND ord.user_id = '".$user_id."' AND ord.paystatus = '1' 
            GROUP BY bs_relate_prod_id DESC LIMIT ".$start.",".$end;

            $res2 = $this->db->getAll($sql2);//查出该活动下该用户已订阅的产品关联ID和产品限购数量

            $relate_prod_id1 = [];
            $relate_prod_id2 = [];

            foreach ($res2 as $k => $v) {
                //计算剩余可购数量-先查询已购数量

            $sql3 = "SELECT IFNULL(sum(ord.rcount), 0) buy_sum 
            FROM weixin_commonshop_orders AS ord 
            INNER JOIN ".WSY_MARK.".`brandsubscribe_relate_prod` AS bsrp ON bsrp.id = ord.bs_relate_prod_id AND bsrp.isvalid = '1' 
            INNER JOIN ".WSY_MARK.".`brandsubscribe_authorize` AS bsat ON bsat.activity_id = bsrp.activity_id AND bsat.isvalid = '1' 
            INNER JOIN ".WSY_MARK.".`brandsubscribe_activity` AS bsa ON bsa.id = bsat.activity_id AND bsa.isvalid = '1' 
            WHERE bsat.activity_id = '".$activity_id."' AND bsat.customer_id = '".$customer_id."' AND ord.isvalid = '1' AND ord.user_id = '".$user_id."' AND ord.paystatus = '1' 
            GROUP BY bs_relate_prod_id";
            $my_subscribe_order = $this->db->getRow($sql3);//查出该活动下该用户每个产品的已购数量

            if($my_subscribe_order['buy_sum'] >= $v['total_limit_num']) {
                $relate_prod_id1[] = $v['relate_prod_id'];
            } else {
                $relate_prod_id2[] = $v['relate_prod_id'];
            }
            }
            $str3 = "('".implode('\',\'', $relate_prod_id1)."')";
            $str4 = "('".implode('\',\'', $relate_prod_id2)."')";

            if ($product_status == 1) {
                $sql .= "AND bsa.status = 2 AND bsat.end_time >= from_unixtime({$nowtime}) AND ( ( bsa.time_type = 1 AND bsa.end_time >= from_unixtime({$nowtime}) AND bsrp.id IN ".$str4." ) OR ( bsa.time_type = 2 AND bsrp.id IN ".$str4." ) )";//搜索订阅中的产品
            }else if ($product_status == 2) {
                $sql .= "AND bsa.status = 2 AND ( ( bsa.time_type = 1 AND bsa.end_time >= from_unixtime({$nowtime}) AND (bsat.end_time < from_unixtime({$nowtime}) OR bsrp.id IN ".$str3.") ) OR ( bsa.time_type = 2 AND (bsat.end_time < from_unixtime({$nowtime}) OR bsrp.id IN ".$str3.") ) )";//搜索已失效的产品
            }else{
                $sql .= "AND ( (bsa.status = 3) OR (bsa.status = 4) OR (bsa.time_type = 1 AND bsa.end_time < from_unixtime({$nowtime})) )";//搜索已结束的产品
            }
        }
        /************** 搜索条件 end ******************/
        $sql .= "GROUP BY bs_relate_prod_id ORDER BY bsrp.product_id DESC,ord.id DESC LIMIT ".$start.",".$end;
       
        $res = $this->db->getAll($sql);//查出该活动下该用户的订阅明细

        for ($i=0; $i < count($res); $i++) {

            $type_ids = $res[$i]['type_ids'];//每个产品的所有分类
            $typename = '';
            if(!empty($type_ids)){
                if(strpos($type_ids,",") === 0){
                    $type_ids = substr($type_ids,1);
                }
                if(substr($type_ids,strlen($type_ids)-1) == ","){
                    $type_ids = substr($type_ids,0,strlen($type_ids)-1);
                }
               
                if(!empty($type_ids)){
                    $type_ids = str_replace(',,',',',$type_ids);

                    $sql3 = "SELECT name FROM ".WSY_PROD.".`weixin_commonshop_types` where isvalid='1' and id in ({$type_ids})  ORDER BY create_parent_id ASC ";
                    
                    $data_type = $this->db->getAll($sql3);//获取每个分类ID对应的分类名

                    foreach ($data_type as $key=>$val) {
                        $typename = $typename."/".$val['name'];//拼接每个分类名
                    }
                }
            }

            $res[$i]['typename'] = $typename;

            //计算剩余可购买天数
            $day_left = strtotime($res[$i]['aut_end_time']) - time();
            if($day_left < 0) {
                $res[$i]['day_left'] = 0;
            } else {
                $res[$i]['day_left'] = floor($day_left/(3600*24))+1;
            }
            //计算礼包时限

            if($res[$i]['package_type'] == 1){//自然月
                $month_lastday = strtotime(date('Y-m-01', strtotime($res[$i]['createtime'])) . ' +1 month -1 day');
                $temp = $month_lastday - strtotime($res[$i]['createtime']);
                $res[$i]['time_limit'] = floor($temp/(3600*24))+1;

            } else if($res[$i]['package_type'] == 2) {//自然年
                $year_lastday = strtotime(date('Y',strtotime($res[$i]['createtime']))."-12-31");
                $temp = $year_lastday - strtotime($res[$i]['createtime']);
                $res[$i]['time_limit'] = floor($temp/(3600*24))+1;

            } else if($res[$i]['package_type'] == 3) {//自定义
                $res[$i]['time_limit'] = $res[$i]['limit_time'];
            }
            //计算剩余可购数量-先查询已购数量
            $sql4 = "SELECT IFNULL(sum(ord.rcount), 0) buy_sum 
                FROM weixin_commonshop_orders AS ord 
                INNER JOIN ".WSY_MARK.".`brandsubscribe_relate_prod` AS bsrp ON bsrp.id = ord.bs_relate_prod_id AND bsrp.isvalid = '1' 
                INNER JOIN ".WSY_MARK.".`brandsubscribe_authorize` AS bsat ON bsat.activity_id = bsrp.activity_id AND bsat.isvalid = '1' 
                INNER JOIN ".WSY_MARK.".`brandsubscribe_activity` AS bsa ON bsa.id = bsat.activity_id AND bsa.isvalid = '1' 
                WHERE bsat.activity_id = '".$activity_id."' AND bsat.customer_id = '".$customer_id."' AND ord.isvalid = '1' AND ord.user_id = '".$user_id."' AND ord.paystatus = '1' 
                GROUP BY bs_relate_prod_id"; 
            $my_subscribe_order = $this->db->getRow($sql4);//查出该活动下该用户每个产品的已购数量

            if($my_subscribe_order['buy_sum'] >= $res[$i]['total_limit_num']) {
                $res[$i]['num_left'] = 0;
            } else {
                $res[$i]['num_left'] = $res[$i]['total_limit_num'] - $my_subscribe_order['buy_sum'];
            }
        }

        $product_count = count($res);//总共多少条记录
        $pageCount = ceil($product_count/$pageSize);//总页数

        $return['pageCount'] = $pageCount;
        $return['activity_arr'] = $res;
        return $return;
    }  

    /*
     * 品牌订阅 确认保存关联产品 5.28新加需求
     * $Author: zqs
     * $2018-05-28  
     */
    public function sava_relate_prod($data){
		$return['errcode'] = 4002;
		$return['errmsg'] = '保存失败！';
    	$sql = "update ".WSY_MARK.".brandsubscribe_relate_prod set is_confirm=1 where customer_id='{$data['customer_id']}' and isvalid=true and activity_id='{$data['activity_id']}'";
    	$result = $this->db->query($sql);
    	if ($result) {
	    	$return['errcode'] = 1;
			$return['errmsg'] = '保存成功！';
    	}
    	return $return;
    }


    /**
    *读活动可添加礼包信息
    * @author  HMJ-V384
    * @param  
    * @version  2018-05-29
    * @return  
    * @var  
    */  
    public function get_packages($data){
        $now       = date('Y-m-d H:i:s',time());
        //查询出已被关联的礼包
        $sql = "select package_id from ".WSY_MARK.".brandsubscribe_activity where customer_id='".$data['customer_id']."' AND isvalid=true AND status!=3 AND status!=4 AND (((end_time >= '{$now}' AND time_type=1) OR time_type=2) AND status=2 ) OR (status=1 AND customer_id='".$data['customer_id']."' AND isvalid=true) ";
        $pack_used = $this->db->getAll($sql);

        $arr = [];
        if($pack_used) {
            foreach ($pack_used as $key => $value) {
                $arr[] = $value['package_id'];
            }            
        }
        $str = "('".implode('\',\'', $arr)."')";

        //查询出商城大礼包
        $sql_pack  = "SELECT id,package_name FROM ".WSY_SHOP.".package_list_t where customer_id='".$data['customer_id']."' AND isvalid=true AND isout=0 AND id NOT IN ".$str." ";
        $all_pack  = $this->db->getAll($sql_pack);

        return $all_pack;

    }

    /**
    *读活动当前礼包信息
    * @author  HMJ-V384
    * @param  
    * @version  2018-05-29
    * @return  
    * @var  
    */  
    public function get_package_act($data){

        //查询出该活动被关联的大礼包id
        $pack_sql = "SELECT package_id FROM ".WSY_MARK.".brandsubscribe_activity where customer_id='".$data['customer_id']."' and id='".$data['id']."' and isvalid=true";
        $pack_id = $this->db->getOne($pack_sql);

        //查询出商城大礼包
        $sql_pack  = "SELECT id,package_name FROM ".WSY_SHOP.".package_list_t where customer_id='".$data['customer_id']."' AND isvalid=true AND isout=0 AND id='".$pack_id."' ";
        $package  = $this->db->getRow($sql_pack);

        if(!$package) {
            $ret = array('errcode' => 400,'errmsg' => '获取礼包信息失败！','data' => '');
            return $ret;
        }
        $ret = array('errcode' => 0,'errmsg' => '成功！','data' => $package); 
        return $ret;

    }

    /**
    * 品牌订阅活动手动结束推送消息
    * @author  zqs
    * @param  activity_id 活动id customer_id 商家id
    * @version  2018-06-19
    * @return  
    * @var  
    */
   public function activity_end_sendmessage($param){
        //查询出活动名称
        $act_name_sql = "select name from ".WSY_MARK.".brandsubscribe_activity where id={$param['activity_id']} and customer_id={$param['customer_id']} and isvalid=true";
        $act_name = $this->db->getOne($act_name_sql);
        
        //获取对应活动的用户信息
        $sql = "select a.user_id,u.weixin_fromuser from ".WSY_MARK.".brandsubscribe_authorize a inner join ".WSY_USER.".weixin_users u on a.user_id=u.id where a.activity_id={$param['activity_id']} and a.customer_id={$param['customer_id']} and a.isvalid=true and u.isvalid=true";
        $res = $this->db->getAll($sql);

        if (empty($res)) {
            return;
        }

        //组装发送信息
        $content  = "亲，您订阅的【".$act_name."】品牌活动已结束！";
        $createtime = date('Y-m-d H:i:s',time());//创建时间
        $sql2     = "INSERT INTO send_weixinmsg_log (
                    customer_id, createtime, type, content, openid
                ) VALUES ";
        foreach ($res as $k => $v) {
            $sql2 .= "({$param['customer_id']},'{$createtime}',1,'{$content}','{$v['weixin_fromuser']}'),";
        }
        $sql2 = rtrim($sql2,",");
        $res2 = $this->db->query($sql2);
   }

}
?>