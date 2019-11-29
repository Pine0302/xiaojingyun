<?php

class share_gifts{
    var $db;

    function __construct($customer_id)
    {
        $this->customer_id = $customer_id;
        $this->db = DB::getInstance();
    }

    /**
     * 活动统计查询
     * 作者：刘伟涛
     */
 	function select_statistics($data){
		$pageSize = $data['pageSize'] ? : 20;//每页多少条
        $pageNum = $data['pageNum'] ? : 1; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;   
        $customer_id = $data['customer_id'];
        $sql = "SELECT a.id,a.total_share_num,a.total_share_people,a.distribute_coupon_num,a.activity_id,a.distribute_red_envelopes_value,a.new_fans_num,b.name,b.begin_time,b.end_time,b.status FROM ".WSY_SHOP.".share_with_prize_statistics AS a INNER JOIN ".WSY_SHOP.".share_with_prize_activity AS b ON a.activity_id = b.id  WHERE a.customer_id='".$customer_id."' and a.isvalid=1";
		
		//账号信息数据
		if($data['b.name']!=""){
			$sql .= " and b.name like '%".addslashes($data['b.name'])."%'";
		}
		if($data['id']!=""){
			$sql .= " and a.activity_id = '".addslashes($data['id'])."'";
		}
		if($data['b.status']>"0"){
			// $sql .= " and b.status = '".$data['b.status']."'";
      //CRM18526 
      if($data['b.status'] == 2){
          $sql .= " and b.status = 2 AND b.end_time_int >=".strtotime(date('Y-m-d'.'00:00:00',time()));
      }elseif($data['b.status'] == 3){
          $sql .= " and b.status = 3 OR (b.end_time_int <".strtotime(date('Y-m-d'.'00:00:00',time()))." AND b.status=2)";
      }else{
          $sql .= " and b.status = '".$data['b.status']."'";
      }
		}
		if($data['begin_time_int']!=""){
            $sql .= " and b.begin_time_int >= '".$data['begin_time_int']."'";
        }
        if($data['end_time_int']!=""){
            $sql .= " and b.end_time_int <='".$data['end_time_int']."'";
        }
		$result = $this->db->getAll($sql);
		$activity_count = count($result);//总共多少条记录
        $pageCount = ceil($activity_count/$pageSize);//总页数
		$sql .= " order by id desc";
   
    if( $data['pageNum'] > 0 ){
            $sql .= " limit ".$start.",".$end;
        }
        $activity_arr = $this->db->getAll($sql);
        //CRM18526
        foreach ($activity_arr as $k => $v) {
          if ($v['status'] == 2) {
            if(strtotime($v['begin_time']) < strtotime(date('Y-m-d'.'00:00:00',time())) && strtotime($v['end_time']) >= strtotime(date('Y-m-d'.'00:00:00',time()))){
               $activity_arr[$k]['status'] = 2;
            }elseif(strtotime($v['end_time']) < strtotime(date('Y-m-d'.'00:00:00',time()))){
               $activity_arr[$k]['status'] = 3;
            }else{
               $activity_arr[$k]['status'] = 1;
            }  
          }
        }
        $result2['activity_count'] = $activity_count;
        $result2['pageCount'] = $pageCount;
        $result2['activity_arr'] = $activity_arr;
        return $result2;
	 }    
	     /**
     * 活动统计详情查询
     * 作者：刘伟涛
     */
 	function select_statisticsdetail($data){
		$pageSize = $data['pageSize'] ? : 20;//每页多少条
        $pageNum = $data['pageNum'] ? : 1; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;   
        $customer_id = $data['customer_id'];

		$sql = "SELECT a.id,a.user_total_share_num,a.user_new_fans_num,a.receive_coupon_num,a.activity_id,a.receive_red_envelopes_value,a.user_id,b.weixin_name,b.weixin_headimgurl FROM ".WSY_SHOP.".share_with_prize_statistics_details as a left join ".DB_NAME.".weixin_users as b on a.user_id = b.id  WHERE a.customer_id='".$customer_id."' and a.isvalid=true";

    if($data['activity_id']!=""){
      $sql .= " and a.activity_id='".$data['activity_id']."'";
    }
    
		//账号信息数据
		if($data['weixin_name']!=""){
			$sql .= " and b.weixin_name like '%".addslashes($data['weixin_name'])."%'";
		}
		if($data['user_id']!=""){
			$sql .= " and a.user_id = '".addslashes($data['user_id'])."'";
		}
		if($data['begin_time']!=""){
            $sql .= " and b.createtime >= '".$data['begin_time']."'";
        }
        if($data['end_time']!=""){
            $sql .= " and b.createtime <='".$data['end_time']."'";
        }

 		$result = $this->db->getAll($sql);
		$activity_count = count($result);//总共多少条记录
        $pageCount = ceil($activity_count/$pageSize);//总页数

		$sql .= " order by id desc";
    if( $data['pageNum'] > 0 ){
            $sql .= " limit ".$start.",".$end;
        }
        $activity_arr = $this->db->getAll($sql);
        $result2['activity_count'] = $activity_count;
        $result2['pageCount'] = $pageCount;
        $result2['activity_arr'] = $activity_arr;
        return $result2;
	 }    
	 	     /**
     * 活动统计详情查询
     * 作者：刘伟涛
     */
 	function select_infodetail($data){
		$pageSize = $data['pageSize'] ? : 20;//每页多少条
        $pageNum = $data['pageNum'] ? : 1; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;   
        $customer_id = $data['customer_id'];

		$sql = "SELECT a.id,a.user_total_share_num,a.activity_id,a.user_id,b.weixin_name,b.weixin_headimgurl FROM ".WSY_SHOP.".share_with_prize_statistics_details as a left join ".DB_NAME.".weixin_users as b on a.user_id = b.id  WHERE a.customer_id='".$customer_id."' and a.share_user_id='".$data['share_user_id']."' and a.isvalid=true";
		//账号信息数据
		if($data['weixin_name']!=""){
			$sql .= " and b.weixin_name like '%".$data['weixin_name']."%'";
		}
		if($data['user_id']!=""){
			$sql .= " and a.user_id = '".$data['user_id']."'";
		}

    if($data['activity_id']!=""){
      $sql .= " and a.activity_id = '".$data['activity_id']."'";
    }

 		$result = $this->db->getAll($sql);
		$activity_count = count($result);//总共多少条记录
        $pageCount = ceil($activity_count/$pageSize);//总页数
		
    $sql .= " group by a.user_id order by a.id desc";
    if( $data['pageNum'] > 0 ){
            $sql .= " limit ".$start.",".$end;
        }
        $activity_arr = $this->db->getAll($sql);
        $result2['activity_count'] = $activity_count;
        $result2['pageCount'] = $pageCount;
        $result2['activity_arr'] = $activity_arr;
        return $result2;
	 }    
    /*
    * 获取分享有礼活动
    */
     function get_share_activity($param=array()){
        $pageSize = 20;
        $pageNum  = $param['pageNum'] ? : 1; 
        $start    = ($pageNum-1)*$pageSize;
        $end      = $pageSize;   

        $customer_id = $param['customer_id'];
        $fields      = $param['fields'];
        $activity_id = $param['activity_id'];

        $query = "SELECT ".$fields." FROM ".WSY_SHOP.".share_with_prize_activity AS ac INNER JOIN ".WSY_SHOP.".share_with_prize_activity_prize AS pr ON ac.id=pr.activity_id WHERE ac.customer_id='".$customer_id."' AND pr.customer_id=".$customer_id." AND ac.isvalid=true AND pr.isvalid=true";

        if(!empty($param['user_id']) && !empty($param['user_activity'])){
           $query .= " and ac.id  in(".$param['user_activity'].")";
        }
        if((int)$param['act_id'] > 0){
           $query .= " and ac.id = '".$param['act_id']."'";
        }
        
        if((int)$activity_id >0){
           $query .= " and ac.id = '".$activity_id."'";
        }

        if(!empty($param['name'])){
           $query .= " and ac.name like '%".addslashes($param['name'])."%'";
        }
        
        if((int)$param['status'] > 0){
           if((int)$param['status'] == 2){
              $query .= " and ac.status = 2 AND ac.end_time_int >=".strtotime(date('Y-m-d'.'00:00:00',time()));
           }elseif((int)$param['status'] == 3){
                $query .= " and ac.status = 3 OR (ac.end_time_int <".strtotime(date('Y-m-d'.'00:00:00',time()))." AND ac.status=2)";
           }else{
              $query .= " and ac.status = '".$param['status']."'";
           }
           
        }
        
        if(!empty($param['begin_time'])){
           $query .= " and ac.createtime >= '".$param['begin_time']."'";
        }
        
        if(!empty($param['end_time'])){
           $query .= " and ac.createtime <= '".$param['end_time']."'";
        }        

        $result_all = $this->db->getAll($query);
        $count      = count($result_all);//总共多少条记录
        $pageCount  = ceil($count/$pageSize);//总页数

        if( $param['pageNum'] > 0 && empty($activity_id)){
            $query .= " order by ac.id desc limit ".$start.",".$end;
        }

        $result = $this->db->getAll($query);

        $return = array(
               'act_infos' => $result,
               'pageCount' => $pageCount,
               'allCount'  => $count
        	);
        return $return;
     } 
     /*
    * 获取活动随机红包信息
    */
     function get_random_red_envelopes($param=array()){
        $customer_id = $param['customer_id'];
        $fields      = $param['fields'];
        $activity_id = $param['activity_id'];

        $query = "SELECT ".$fields." FROM ".WSY_SHOP.".share_with_prize_random_red_envelopes WHERE customer_id='".$customer_id."' AND activity_id='".$activity_id."' AND isvalid=true";

        $result = $this->db->getAll($query);

        return $result;
     }
    /*
    * 分享有礼活动的操作
    */
     function operation_act($param=array()){
        $customer_id = $param['customer_id'];
        $act_id      = (int)$param['act_id'];
        $op          = $param['op'];
      
        $return = array(
               "errcode" => -1,
               "errmsg"  => "失败"
        	);
        if(!empty($op) && $act_id > 0){         
           switch($op){
           	 //启动活动
           	 case "start":
           	       $begin_time = "";
		           $end_time   = "";

		           $check_sql = "SELECT begin_time,end_time FROM ".WSY_SHOP.".share_with_prize_activity WHERE id='".$act_id."' and customer_id='".$customer_id."' and isvalid=true";
		           $result_check = $this->db->getRow($check_sql);
		           
		           $begin_time = $result_check['begin_time'];
		           $end_time   = $result_check['end_time'];

                   //判断是否有活动时间重叠，有则不能启动
                   $wcount = 0;
                   $select_sql = "SELECT count('id') as wcount FROM ".WSY_SHOP.".share_with_prize_activity WHERE end_time_int >".strtotime(date('Y-m-d'.'00:00:00',time()))." AND status=2 AND ((begin_time >= '".$begin_time."' AND begin_time <= '".$end_time."') OR  (begin_time <= '".$begin_time."' AND end_time >= '".$end_time."') OR  (end_time >= '".$begin_time."' AND end_time <= '".$end_time."')) and customer_id='".$customer_id."' and isvalid=true";

                   $result_select = $this->db->getRow($select_sql);
                   
                   if($result_select){
                      $wcount = (int)$result_select['wcount'];
                   }

                   if($wcount >= 1){
                   	   $return['errcode'] = 3;
                       $return['errmsg']  = "活动时间重叠，请重新编辑";

                       break;
                   }
                   //活动结束时间小于当前时间，改为已结束状态
                   if(strtotime($end_time) < strtotime(date('Y-m-d'.'00:00:00',time()))){
                      $update_sql    = "UPDATE ".WSY_SHOP.".share_with_prize_activity SET status=3 WHERE id='".$act_id."' and isvalid=true";
                      $result_update = $this->db->query($update_sql);

                      if($result_update){
                         $return['errcode'] = 2;
                         $return['errmsg']  = "活动时间超时";
                      }
                   }else{
                   	  $update_sql    = "UPDATE ".WSY_SHOP.".share_with_prize_activity SET status=2 WHERE id='".$act_id."' and isvalid=true";
                      $result_update = $this->db->query($update_sql);

                      if($result_update){
                         $return['errcode'] = 1;
                         $return['errmsg']  = "成功";
                      }
                   }
           	 break;
           	 //终止活动
           	 case "end":
                  $update_sql    = "UPDATE ".WSY_SHOP.".share_with_prize_activity SET status=3 WHERE id='".$act_id."' and isvalid=true";
                  $result_update = $this->db->query($update_sql);

                  if($result_update){
                     $return['errcode'] = 1;
                     $return['errmsg']  = "成功";
                  }
           	 break;
           	 case "del":
                  $update_sql    = "UPDATE ".WSY_SHOP.".share_with_prize_activity SET isvalid=false WHERE id='".$act_id."' and isvalid=true";
                  $result_update = $this->db->query($update_sql);

                  if($result_update){
                     $return['errcode'] = 1;
                     $return['errmsg']  = "成功";
                  }
           	 break;
           	 default:
           	 break;
           }
        }

        return $return;
     }

    /*
    * 保存活动列表
    */   
    function save_share_activity($param=array()){
        $act_id      = (int)$param['act_id'];
      
        $return = array(
               "errcode" => -1,
               "errmsg"  => "失败"
        );

        if($act_id > 0){  //修改活动
            $update_main_sql = "UPDATE ".WSY_SHOP.".share_with_prize_activity set name='".$param['name']."',begin_time='".$param['begin_time']."',end_time='".$param['end_time']."',begin_time_int='".strtotime($param['begin_time'])."',end_time_int='".strtotime($param['end_time'])."',share_background_img='".$param['share_background_img_path']."',receive_background_img='".$param['receive_background_img_path']."',have_receive_background_img='".$param['have_receive_background_img_path']."',leaderboards_background_img='".$param['leaderboards_background_img_path']."',is_subscription=".(int)$param['is_subscription'].",is_bind_phone=".(int)$param['is_bind_phone'].",is_share_instruction=".$param['is_share_instruction'].",share_instruction='".addslashes($param['share_instruction'])."',is_receive_instruction=".$param['is_receive_instruction'].",receive_instruction='".addslashes($param['receive_instruction'])."',is_leaderboards_instruction=".$param['is_leaderboards_instruction'].",leaderboards_instruction='".addslashes($param['leaderboards_instruction'])."',color='".$param['color']."',receive_color='".$param['receive_color']."',have_receive_color='".$param['have_receive_color']."',leaderboards_color='".$param['leaderboards_color']."' WHERE isvalid=true and customer_id='".$param['customer_id']."' and id='".$act_id."'";

             $result_main_update = $this->db->query($update_main_sql);

            $update_extend_sql = "UPDATE ".WSY_SHOP.".share_with_prize_activity_prize set share_is_coupon=".(int)$param['share_is_coupon'].", share_is_red_envelopes=".(int)$param['share_is_red_envelopes'].",coupon_begin_time_int=".$param['coupon_begin_time']." ,coupon_end_time_int=".$param['coupon_end_time'].",share_coupon_ids = '".$param['share_link_coupons_save']."', share_coupon_guide_url='".$param['share_coupon_guide_url']."',red_envelopes_begin_time_int=".$param['red_envelopes_begin_time'].",red_envelopes_end_time_int=".$param['red_envelopes_end_time'].", share_red_envelopes_type='".$param['share_red_envelopes_type']."',share_red_envelopes_time_limit='".$param['share_red_envelopes_time_limit']."',share_red_envelopes_money_limit='".$param['share_red_envelopes_money_limit']."', share_red_envelopes_people_limit='".$param['share_red_envelopes_people_limit']."',share_red_envelopes_fixed_value='".$param['share_red_envelopes_fixed_value']."',share_invitations='".$param['share_invitations']."',new_coupon_ids='".$param['new_link_coupons_save']."',new_coupon_guide_url='".$param['new_coupon_guide_url']."',time_type=".$param['share_activity_time_type']." WHERE isvalid=true and customer_id='".$param['customer_id']."' and activity_id = '".$act_id."'";
      
            $result_main_update = $this->db->query($update_extend_sql);

            if($result_main_update || $result_extend_update){
		           $return['errcode'] = 1;
		           $return['errmsg']  = "修改成功";
		      }                        
        }else{ //新增活动
            $insert_main_sql = "INSERT INTO ".WSY_SHOP.".share_with_prize_activity (customer_id,name,status,begin_time,end_time,begin_time_int,end_time_int,share_background_img,receive_background_img,have_receive_background_img,leaderboards_background_img,is_subscription,is_bind_phone,is_share_instruction,share_instruction,is_receive_instruction,receive_instruction,is_leaderboards_instruction,leaderboards_instruction,isvalid,createtime,color,receive_color,have_receive_color,leaderboards_color) VALUES(".$param['customer_id'].",'".$param['name']."',1,'".$param['begin_time']."','".$param['end_time']."','".strtotime($param['begin_time'])."','".strtotime($param['end_time'])."','".$param['share_background_img_path']."','".$param['receive_background_img_path']."','".$param['have_receive_background_img_path']."','".$param['leaderboards_background_img_path']."',".(int)$param['is_subscription'].",".(int)$param['is_bind_phone'].",".$param['is_share_instruction'].",'".addslashes($param['share_instruction'])."',".$param['is_receive_instruction'].",'".addslashes($param['receive_instruction'])."',".$param['is_leaderboards_instruction'].",'".addslashes($param['leaderboards_instruction'])."',true,now(),'".$param['color']."','".$param['receive_color']."','".$param['have_receive_color']."','".$param['leaderboards_color']."')";

             $result_main_insert = $this->db->query($insert_main_sql);

            $act_id = $this->db->insert_id();

             $insert_extend_sql =  "INSERT INTO ".WSY_SHOP.".share_with_prize_activity_prize (activity_id,customer_id,share_is_coupon,share_is_red_envelopes,coupon_begin_time_int,coupon_end_time_int,share_coupon_ids,share_coupon_guide_url,red_envelopes_begin_time_int,red_envelopes_end_time_int,share_red_envelopes_type,share_red_envelopes_time_limit,share_red_envelopes_money_limit,share_red_envelopes_people_limit,share_red_envelopes_fixed_value,share_invitations,new_coupon_ids,new_coupon_guide_url,isvalid,createtime,time_type) VALUES(".$act_id.",'".$param['customer_id']."',".(int)$param['share_is_coupon'].",".(int)$param['share_is_red_envelopes'].",'".$param['coupon_begin_time']."','".$param['coupon_end_time']."','".$param['share_link_coupons_save']."','".$param['share_coupon_guide_url']."','".$param['red_envelopes_begin_time']."','".$param['red_envelopes_end_time']."','".$param['share_red_envelopes_type']."','".$param['share_red_envelopes_time_limit']."','".$param['share_red_envelopes_money_limit']."','".$param['share_red_envelopes_people_limit']."','".$param['share_red_envelopes_fixed_value']."','".$param['share_invitations']."','".$param['new_link_coupons_save']."','".$param['new_coupon_guide_url']."',true,now(),".$param['share_activity_time_type'].")";

              $result_extend_insert = $this->db->query($insert_extend_sql);

              if($result_main_insert && $result_extend_insert){
		           $return['errcode'] = 1;
		           $return['errmsg']  = "添加成功";
		      }
        }
       
       //修改随机红包
        if((int)$param['share_is_red_envelopes'] == 1 && (int)$param['share_red_envelopes_type']==2 && !empty($param['bag_id'])) {
        	//先置该活动随机红包为false
        	$rengon_update_sql = "UPDATE ".WSY_SHOP.".share_with_prize_random_red_envelopes SET isvalid=false WHERE activity_id='".$act_id."' AND customer_id='".$param['customer_id']."' AND isvalid=true";
        	$this->db->query($rengon_update_sql);
        	
           for($i=0;$i<count($param['bag_id']);$i++){
               if((int)$param['bag_id'][$i] > 0){
                 //更新
                 $update_random_red_bag_sql = "UPDATE ".WSY_SHOP.".share_with_prize_random_red_envelopes set min_money='".$param['min_money'][$i]."',max_money='".$param['max_money'][$i]."',probability='".$param['probability'][$i]."',isvalid=true WHERE customer_id='".$param['customer_id']."' and id='".$param['bag_id'][$i]."'";

                   $this->db->query($update_random_red_bag_sql);
               }else{//新增                    
                   $insert_random_red_bag_sql = "INSERT INTO ".WSY_SHOP.".share_with_prize_random_red_envelopes (activity_id,customer_id,min_money,max_money,probability,isvalid,createtime)  VALUES(".$act_id.",".$param['customer_id'].",".$param['min_money'][$i].",".$param['max_money'][$i].",".$param['probability'][$i].",true,now())";

                   $this->db->query($insert_random_red_bag_sql);
               }
           }

        } 
       
        return $return;
    }
     /*
    * 获取用户统计列表 --by dino
    */
     function get_user_statistic($data){
        $result      = array(); 
        $pageSize    = 20;
        $pageNum     = $data['pageNum'] ? : 1; 
        $start       = ($pageNum-1)*$pageSize;
        $end         = $pageSize;
        $customer_id = $data['customer_id'];


        /*********搜索条件start***********/
    
        if($data['user_name']){

           $weixin_name = $data['user_name'];   //用户名（微信名）

        }
        if($data['user_number']){

           $user_id     = $data['user_number']; //用户编号（weixin_users用户信息表的id）

        }

        $sql = "SELECT a.relation_activity_num,a.user_total_share_total_num,a.user_new_fans_total_num,a.receive_coupon_total_num,a.receive_red_envelopes_total_value,a.user_id,b.weixin_name,b.weixin_headimgurl FROM ".WSY_SHOP.".share_with_prize_user_info AS a left join ".DB_NAME.".weixin_users AS b on a.user_id = b.id WHERE a.customer_id='".$customer_id."' and a.isvalid=true";


        if($weixin_name!="")//用户微信名
        {
            $sql.=' and b.weixin_name like "%'.addslashes($weixin_name).'%"';
        }
        if($user_id!="")
        {
            $sql.=' and a.user_id = "'.$user_id.'"';
        }
        /*********搜索条件end*************/
        
        $result = $this->db->getAll($sql);
        $activity_count = count($result);//总共多少条记录
        $pageCount = ceil($activity_count/$pageSize);//总页数

        $sql .= " order by a.user_new_fans_total_num desc";
        if( $data['pageNum'] > 0 ){
            $sql .= " limit ".$start.",".$end;
        }  

        $statistic_arr                   = $this->db->getAll($sql);
        $result2['sql']                  = $sql;
        $result2['activity_count']       = $activity_count;
        $result2['pageCount']            = $pageCount;
        $result2['statistic_arr']        = $statistic_arr;
        return $result2;
     }
     
    /*
    * 获取优惠券列表
    */
     function select_coupon_list($param=array()){
        $customer_id = $param['customer_id'];

        $result = array();

        $query_coupons_id='select id from weixin_commonshop_coupons where isvalid=true and customer_id='.$customer_id.' and is_open=1 order by id desc ';
        $result_coupons_id = $this->db->getAll($query_coupons_id);
        $all_coupons_id="";

        if(!empty($result_coupons_id)){
           for($i=0;$i<count($result_coupons_id);$i++){
              $all_coupons_id.=$result_coupons_id[$i]['id'].",";
           }
        }
        $all_coupons_id=rtrim($all_coupons_id, ",");
        
        $query_coupons='select c.id,c.is_open,c.title,c.NeedMoney,c.CanGetNum,c.Days,c.DaysType,c.class_type,c.MinMoney,c.MaxMoney,c.user_scene,c.couponNum,c.MoneyType,c.personNum,c.getStartTime,c.getEndTime,c.createtime,c.startline,p.name from weixin_commonshop_coupons c LEFT JOIN weixin_commonshop_products p on c.connected_id=p.id where c.isvalid=true and c.customer_id='.$customer_id.' and c.is_open=1 order by c.id desc';

        $result_coupons = $this->db->getAll($query_coupons);

        $result['all_coupons_id'] = $all_coupons_id;
        $result['result_coupons'] = $result_coupons;

        return $result;
     }

    /*
    * 获取选取的优惠券
    */
   function select_coupon_one($coupon_array){
      $new_coupon_array = array();

      foreach ($coupon_array as $key => $value) {
          $query_open_coupons="select is_open,id,title from weixin_commonshop_coupons where isvalid=true and id='".$value."' limit 1";

          $resule_open_coupons = $this->db->getAll($query_open_coupons);

          if((int)$resule_open_coupons[0]['is_open'] > 0){
              $new_coupon_array[] = (int)$resule_open_coupons[0]['id'];
          }
      }
      
      return $new_coupon_array;
   }
   /*
    * 获取开启的总的优惠券
    */
   function select_coupon_get_all($param=array()){
          $query="select id from weixin_commonshop_coupons where isvalid=true and customer_id='".$param['customer_id']."' and is_open=1 order by id desc";

          $result = $this->db->getAll($query);

          $all_coupons_id = "";

          if(!empty($result)){
             foreach($result as $k=>$v){
                $all_coupons_id .= $v['id'].",";
             }
             $all_coupons_id=rtrim($all_coupons_id, ",");
          }

          return $all_coupons_id;
   }

    /*
    * 判断活动是否重叠
    */
   function check_activity_is_overlap($param=array()){
          //判断是否有活动时间重叠，有则不能启动
           $wcount = 0;
           $select_sql = "SELECT count('id') as wcount FROM ".WSY_SHOP.".share_with_prize_activity WHERE status=2 AND isvalid=true AND customer_id='".$this->customer_id."' AND ((begin_time >= '".$param['begin_time']."' AND begin_time <= '".$param['end_time']."') OR  (begin_time <= '".$param['begin_time']."' AND end_time >= '".$param['end_time']."') OR  (end_time >= '".$param['begin_time']."' AND end_time <= '".$param['end_time']."'))";

           $result_select = $this->db->getRow($select_sql);
           
           if($result_select){
              $wcount = (int)$result_select['wcount'];
           }

           if($wcount >= 1){
              return true;
           }else{
              return false;
           }
   }

    /*
    * 查找用户参与过的活动id
    */
   function select_user_join_activity($param=array()){
           $sql = "SELECT activity_id FROM ".WSY_SHOP.".share_with_prize_statistics_details WHERE user_id='".$param['user_id']."' and customer_id='".$param['customer_id']."' and isvalid=true";

           $result = $this->db->getALL($sql);
           
           $activity = "";

           if($result){
             foreach($result as $k=>$v){
                $activity .= $v['activity_id'].",";
             }
             $activity = rtrim($activity,",");
           }
           return $activity;
   }
}