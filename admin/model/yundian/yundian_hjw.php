<?php


    /*
     * 店主审核列表
     * $Author: hjw$
     * $2018-04-04  $
     */
    function shopkeeper_review_list($data){
        extract($data);
        $pageNum   = $page > 1 ? $page : 1;//当前页,1开始
        $page_size = $page_size; //每页多少条
        $start     = ($pageNum-1)*$page_size;
        $sql       = "select a.id,a.user_id,a.tequan_id,a.phone,a.realname,a.identity_num,
                a.apply_time,a.verify_time,a.status,a.reject_desc,i.name AS identity_name,
                u.name,i.reward FROM ".WSY_REBATE.".weixin_yundian_identity_applylog a 
                left JOIN ".WSY_USER.".weixin_users u ON a.user_id = u.id 
                left JOIN ".WSY_REBATE.".weixin_yundian_identity i ON i.id = a.tequan_id";

        $sql_count = "select COUNT(a.id) as total FROM ".WSY_REBATE.".weixin_yundian_identity_applylog a 
                left JOIN ".WSY_USER.".weixin_users u ON a.user_id = u.id 
                left JOIN ".WSY_REBATE.".weixin_yundian_identity i ON i.id = a.tequan_id";

        $where     = "";
        $where    .= " WHERE a.customer_id = '".$customer_id."' and a.isvalid = true ";
        if(!empty($user_id)){
            $where .= " AND a.user_id LIKE '%".$user_id."%'";
        }
        if(!empty($user_name)){
            $where .= " AND u.name LIKE '%".$user_name."%'";
        }
        if(!empty($identity_id)){
            $where .= " AND i.id = '".$identity_id."'";
        }
        if($status != ''){
            $where .= " AND a.status = '".$status."'";
        }
        if(!empty($begin_time)){
            $where .= " AND a.apply_time >= '".$begin_time."'";
        }
        if(!empty($end_time)){
            $where .= " AND a.apply_time <= '".$end_time."'";
        }
        $limit            = " ORDER BY a.apply_time desc LIMIT ".$start.",".$page_size;
        $sql_choucheng    = "SELECT yundian_choucheng,shop_valid_time FROM ".WSY_REBATE.".weixin_yundian_setting WHERE customer_id = '".$customer_id."' and isvalid =true ";
        $result           = Array();
        $page_arr         = Array();
        $all              = $this->db->getRow($sql_count.$where);
        $result           = $this->db->getAll ($sql.$where.$limit);
        $result_choucheng = $this->db->getRow($sql_choucheng);
        $list_num         = count($result);

        if(empty($list_num) || $list_num <= 0){ return array("errcode"=>0,"errmsg"=>"获取数据失败","data"=>$result);}

        $page_arr['total']      = $all['total'];
        $page_arr['page']       = $page;
        $page_arr['page_size']  = $page_size;
        $page_arr['list_num']   = $list_num;
        $res = array('errcode'  => 0,'errmsg'=>'获取成功','data'=>['result'=>$result,'page_arr'=>$page_arr,'choucheng'=>$result_choucheng]);
        return $res;

    }

    /*
     * 获取云店特权身份配置
     * $Author: hjw$
     * $2018-04-04  $
     * 传入customer_id
     */
    function get_identity($customer_id)
    {
        $result['errcode'] = 0;
        $result['errmsg']  = "";

        if(empty($customer_id) || $customer_id < 0)
        {
            $result['errcode'] = 400;
            $result['errmsg']  = "customer_id参数错误";
            return $result;
        }

        $sql = "select id,name,reward,apply_money,tequan,remark,createtime from ".WSY_REBATE.".weixin_yundian_identity where isvalid=true and customer_id = '".$customer_id."'";
        $identity_arr = $this->db->getAll($sql);

        if($identity_arr)
        {
            $result['errcode'] = 0;
            $result['errmsg']  = "获取成功";
            $result['data']    = $identity_arr;
        }
        else
        {
            $result['errcode'] = 400;
            $result['errmsg']  = "查询失败";
        }
        return $result;
    }

    /*
     * 批量和单独审核店主
     * $Author: hjw$
     * $2018-04-08  $
     */
    function review_pass($param){
    	$result = Array();
    	extract($param);
        $content = '';
        $yundian_id = 0;//云店门店ID
    	if($self_reware > 1 || $self_reware <0 || empty($self_reware)){
    		$self_reware = 0;
    	}
    	if($profit_shop > 1 || $profit_shop <0 || empty($profit_shop)){
    		$profit_shop = 0;
    	}
    	$time = date("Y-m-d H:i:s",time());
    	switch ($more) {
    		case '':
    			$result = array('errcode' => 400 ,'errmsg' => 'more参数只能为0或1');
    			break;
            case 0:
                //单独审核
                if($default_expire <= 0 || !$default_expire){
                    $default_expire = 365;
                }
                $expire_time     = strtotime($time)+$default_expire*24*3600;
                //first steps: 查询之前是否有店主信息 有：更新，无：插入
                $query_keeper = "select id from ".WSY_USER.".weixin_yundian_keeper where isvalid = true and customer_id = '".$customer_id."' and user_id = '".$user_id."' and status = 1";
                $result_keeper = $this->db->getAll($query_keeper)[0];
                if($result_keeper['id']){
                    //更新
                    $sql           = "update ".WSY_USER.".weixin_yundian_keeper set status = 1,tequan_id = '".$tequan_id."',verify_time = '".$time."',self_reware = '".$self_reware."', profit_shop = '".$profit_shop."',expire_time = '".date("Y-m-d H:i:s", $expire_time)."' where user_id = '".$user_id."' and isvalid = true and customer_id = '".$customer_id."'";
                    $res          = $this->db->query($sql);
                    $yundian_id = $result_keeper['id'];
                    if($res){
                        $result = array('errcode' => 0 ,'errmsg' => '审核成功');
                    }else{
                        $result = array('errcode' => 400 ,'errmsg' => '审核失败');
                    }
                    
                }else{
                    //插入
                    $arr_apply  = "select phone,realname,identity_num,apply_time from ".WSY_REBATE.".weixin_yundian_identity_applylog where id ='".$id."' and isvalid = true";
                    $result_arr = $this->db->getAll($arr_apply)[0]; 
                    $sql_insert = "insert into ".WSY_USER.".weixin_yundian_keeper (customer_id,isvalid,user_id,tequan_id,phone,realname,identity_num,status,apply_time,verify_time,expire_time,profit_shop,self_reware,createtime) values({$customer_id},true,".$user_id.",".$tequan_id.",".$result_arr['phone'].",'".$result_arr['realname']."',".$result_arr['identity_num'].",1,'".$result_arr['apply_time']."',now(),'".date("Y-m-d H:i:s", $expire_time)."',".$profit_shop.",".$self_reware.",now())";
                    $res_insert = $this->db->query($sql_insert);
                    $query_last_id = "select id from ".WSY_USER.".weixin_yundian_keeper where isvalid = true and customer_id = '".$customer_id."' and user_id = '".$user_id."' and status = 1";
                    $result_last_id = $this->db->getRow($query_last_id); 
                    $yundian_id = $result_last_id['id'];
                    if($res_insert){
                        $result = array('errcode' => 0 ,'errmsg' => '审核成功' );
                     }else{
                        $result = array('errcode' => 400 ,'errmsg' => '审核失败');
                    }
                    
                }
                //判断是否之前有申请记录，有：修改之前的当前身份状态为0
                $count_old_apply = "select id from ".WSY_REBATE.".weixin_yundian_identity_applylog where isvalid = true and customer_id = '".$customer_id."' and user_id = '".$user_id."'";
                $result_old_apply = $this->db->getAll($count_old_apply);
                if(count($result_old_apply) > 0){
                    $sql_apply_log_old = "update ".WSY_REBATE.".weixin_yundian_identity_applylog set is_default = 0 where user_id = '".$user_id."' and isvalid = true and customer_id = '".$customer_id."'";
                    $res_apply_log_old = $this->db->query($sql_apply_log_old);
                }
                //更新当前申请记录的相关状态
                $sql_apply_log = "update ".WSY_REBATE.".weixin_yundian_identity_applylog set is_default = 1,status = 1,verify_time ='".$time."' where id ='".$id."' and isvalid = true and customer_id = '".$customer_id."'";
                $res_apply = $this->db->query($sql_apply_log);
                //查询申请身份的名称
                $sql_identity = "select name from ".WSY_REBATE.".weixin_yundian_identity where isvalid = true and id ='".$tequan_id."' and customer_id = '".$customer_id."'";
                $res_identity = $this->db->getAll($sql_identity)[0];
                //插入推送消息
                $url = Protocol. $_SERVER["HTTP_HOST"] . "/weixinpl/mshop/personal_center.php?customer_id=".$customer_id_en."&yundian=".$yundian_id; 
                $content = "恭喜您，您的云店".$res_identity['name']."申请已经审核通过！\\n<a href='".$url."'>赶紧去管理您的云店吧！</a>";
                $openid = $this->shopmessage->query_openid($customer_id,$user_id);
                $query = "INSERT INTO send_weixinmsg_log (
                                customer_id, createtime, type, content, openid
                            ) VALUES (
                                {$customer_id}, now(), 0, '".mysql_real_escape_string($content)."', '{$openid['openid']}'
                            )";
                 $res_query = $this->db->query($query);
                break;
    		case 1:
                //批量审核
                 $user_id_arr = array();
                 $tequan_id_arr = array();
                 $update_ids = array();
                 $insert_userIds = array();
                 $default_expire = 0;
                 $profit_shop = 0;
                 $self_reware = 0;
                 $user_id_string = '';
                 //first steps: 查询平台设置的抽成比例和默认到期天数
                 $sql_choucheng = "SELECT yundian_choucheng,shop_valid_time FROM ".WSY_REBATE.".weixin_yundian_setting WHERE customer_id = '".$customer_id."' and isvalid =true ";
                 $res_choucheng = $this->db->getRow($sql_choucheng);
                 $default_expire =  $res_choucheng['shop_valid_time'];
                 $self_reware = $res_choucheng['yundian_choucheng'];
                 if($default_expire <= 0 || !$default_expire){
                    $default_expire = 365;
                 }
                 $expire_time = strtotime($time)+$default_expire*24*3600;
                 //second steps: 批量查询申请店主身份及店主个人的相关信息
                 $query_apply_ids = "select distinct(a.user_id),a.tequan_id,i.reward,u.weixin_fromuser,i.name,a.phone,a.realname,a.identity_num,a.apply_time from ".WSY_REBATE.".weixin_yundian_identity_applylog a inner join ".WSY_REBATE.".weixin_yundian_identity i on a.tequan_id = i.id inner join ".WSY_USER.".weixin_users u on u.id = a.user_id where a.id in(".$id.") and a.isvalid = true and i.isvalid and a.customer_id = '".$customer_id."'"; 
                 $res_apply_ids = $this->db->getAll($query_apply_ids);


                 foreach ($res_apply_ids as $k => $v) {
                    $user_id_arr[$k] = $v['user_id'];
                 }
                 $user_id_string  = implode(",", $user_id_arr);
                 //next steps:批量更新店主申请表的状态
                 $update_old_ids = "update ".WSY_REBATE.".weixin_yundian_identity_applylog set is_default = 0,verify_time = '".$time."' where user_id in(".$user_id_string.") and isvalid = true and status = 1 and customer_id = '".$customer_id."'";
                 $res_old_ids  = $this->db->query($update_old_ids);
                 $update_new_ids = "update ".WSY_REBATE.".weixin_yundian_identity_applylog set is_default = 1,status = 1,verify_time = '".$time."' where id in(".$id.") and isvalid = true";
                 $res_new_ids  = $this->db->query($update_new_ids);

                 //批量查询keeper表，是否存在记录 有则更新，无则插入
                 $select_keeper_ids = "select id as keeper_id,user_id from ".WSY_USER.".weixin_yundian_keeper where user_id in(".$user_id_string.") and isvalid = true and status = 1 and customer_id = '".$customer_id."'";
                 $res_keeper_ids = $this->db->getAll($select_keeper_ids);

                 foreach ($res_apply_ids as $k2 => $v2) {
                     $yundian_id = 0;//云店门店ID
                     $select_k = "select id from ".WSY_USER.".weixin_yundian_keeper where user_id ='".$v2['user_id']."' and isvalid = true and status = 1 and customer_id = '".$customer_id."'";
                     $result_k = $this->db->getAll($select_k)[0];
                     if($result_k['id']){
                        $sql_update  = "update ".WSY_USER.".weixin_yundian_keeper set status = 1,tequan_id = '".$v2['tequan_id']."',verify_time = '".$time."',self_reware = '".$self_reware."', profit_shop = '".$v2['reward']."',expire_time = '".date("Y-m-d H:i:s", $expire_time)."' where user_id = '".$v2['user_id']."' and isvalid = true and customer_id = '".$customer_id."'";
                        $res_update  = $this->db->query($sql_update);
                        $yundian_id = $result_k['id'];
                        if(!$res_update){
                             return array('errcode' => 400 ,'errmsg' => '批量审核失败');
                        }
                     }else{
                         $sql_insert = "insert into ".WSY_USER.".weixin_yundian_keeper (customer_id,isvalid,user_id,tequan_id,phone,realname,identity_num,status,apply_time,verify_time,expire_time,profit_shop,self_reware,createtime) values({$customer_id},true,".$v2['user_id'].",".$v2['tequan_id'].",".$v2['phone'].",'".$v2['realname']."',".$v2['identity_num'].",1,'".$v2['apply_time']."',now(),'".date("Y-m-d H:i:s", $expire_time)."',".$v2['reward'].",".$self_reware.",now())";
                         $res_insert = $this->db->query($sql_insert);
                         $query_last_id = "select id from ".WSY_USER.".weixin_yundian_keeper where isvalid = true and customer_id = '".$customer_id."' and user_id = '".$v2['user_id']."' and status = 1";
                         $result_last_id = $this->db->getRow($query_last_id); 
                         $yundian_id = $result_last_id['id'];
                         if(!$res_insert){
                             return array('errcode' => 400 ,'errmsg' => '批量审核失败');
                         }
                     }
                     //插入推送消息
                    $url = Protocol. $_SERVER["HTTP_HOST"] . "/weixinpl/mshop/personal_center.php?customer_id=".$customer_id_en."&yundian=".$yundian_id; 
                    $content = "恭喜您，您的云店".$v2['name']."申请已经审核通过！\\n<a href='".$url."'>赶紧去管理您的云店吧！</a>";
                    $query = "INSERT INTO send_weixinmsg_log (
                                    customer_id, createtime, type, content, openid
                                ) VALUES (
                                    {$customer_id}, now(), 0, '".mysql_escape_string($content)."', '{$v2['weixin_fromuser']}'
                                )";
                     $res_query = $this->db->query($query);
                 }
                 $result = array('errcode' => 0 ,'errmsg' => '批量审核成功');
    			break;
    		default:
    			$result = array('errcode' => 400 ,'errmsg' => 'more参数丢失');
    			break;
    	}
    	return $result;
    }
    /*
     * 批量和单独驳回店主
     * $Author: hjw$
     * $2018-04-08  $
     */
    function reject_review($param){
    	$result = Array();
    	extract($param);
    	$time = date("Y-m-d H:i:s",time());
        $content = '';
    	switch ($more) {
    		case '':
    			$result = array('errcode' => 400 ,'errmsg' => 'more参数只能为0或1');
    			break;
    		case 0:
    			 //first steps: 查询申请店主身份的相关信息
                 $sql_applylog = "select a.customer_id,a.user_id,a.tequan_id,i.name from ".WSY_REBATE.".weixin_yundian_identity_applylog a inner join ".WSY_REBATE.".weixin_yundian_identity i on a.tequan_id = i.id where a.id='".$id."' and a.isvalid = true and i.isvalid = true";
    			 $res_applylog = $this->db->getAll($sql_applylog)[0];
/*                 //second steps：查看申请人的相关信息 
    			 $sql_keeper = "select k.id,u.weixin_fromuser,k.status from ".WSY_USER.".weixin_yundian_keeper k inner join ".WSY_USER.".weixin_users u on u.id = k.user_id where k.customer_id='".$res_applylog['customer_id']."' and k.user_id='".$res_applylog['user_id']."' and k.isvalid = true and u.isvalid = true";
    			 $res_keeper = $this->db->getAll($sql_keeper)[0];*/
                 //next steps：更新店主申请表的状态
    			 $sql = "update ".WSY_REBATE.".weixin_yundian_identity_applylog set status = 2,verify_time = '".$time."',reject_desc = '".$reason."' where id = '".$id."' and isvalid = true";
       			 $res = $this->db->query($sql);
/*                 //next steps：判断店主是否第一次申请，是：修改店主信息表状态
                 if($res_keeper['status'] == 0){
                    $update_keeper = "update ".WSY_USER.".weixin_yundian_keeper set status = 2,verify_time = '".$time."',reject_desc = '".$reason."' where id = '".$res_keeper['id']."' and isvalid = true ";
                    $res_update_keeper = $this->db->query($update_keeper);
                 }*/
                  //last steps: 插入推送消息
                 $content = "您的云店".$res_applylog['name']."申请已经被驳回了！驳回原因：".(empty($reason)?'不符合资格。':$reason)."";
                 $query = "INSERT INTO send_weixinmsg_log (
                                customer_id, createtime, type, content, openid
                            ) VALUES (
                                {$res_applylog['customer_id']}, now(), 0, '".$content."', '{$res_keeper['weixin_fromuser']}'
                            )";
                 $res_query = $this->db->query($query);
       			 if($res){
       			 	$result = array('errcode' => 0 ,'errmsg' => '驳回成功');
       			 }else{
       			 	$result = array('errcode' => 400 ,'errmsg' => '驳回失败');
       			 }
    			break;
    		case 1:
                 $update_keeper_ids = Array();
                 //first steps: 批量查询申请店主身份及店主个人的相关信息
                 $query_msg = "select distinct(a.user_id),u.weixin_fromuser,i.name from ".WSY_REBATE.".weixin_yundian_identity_applylog a inner join ".WSY_REBATE.".weixin_yundian_identity i on a.tequan_id = i.id inner join ".WSY_USER.".weixin_users u on u.id = a.user_id where a.id in(".$id.") and a.isvalid = true and i.isvalid and a.customer_id = '".$customer_id."'";
                 $result_msg = $this->db->getAll($query_msg);
                 //second steps：批量更新店主申请表的状态
    			 $sql = "update ".WSY_REBATE.".weixin_yundian_identity_applylog set status = 2,verify_time = '".$time."' where id in(".$id.") and isvalid = true";
       			 $res = $this->db->query($sql);
   /*              //next steps：判断店主是否第一次申请，是：批量修改店主信息表状态
                 $query_keeper = "select id,status from ".WSY_USER.".weixin_yundian_keeper where id in(".$k_id.") and isvalid = true";
                 $result_query_keeper = $this->db->getAll($query_keeper);
                 foreach ($result_query_keeper as $k1 => $v1) { 
                        if($v1['status'] == 0){
                            $update_keeper_ids[] = $v1['id'];
                        }
                 }
                 $update_keeper_ids = array_filter($update_keeper_ids);
                 if(!empty($update_keeper_ids)){
                     $sql_keeper = "update ".WSY_USER.".weixin_yundian_keeper set status = 2,verify_time = '".$time."' where id in(".implode(",", $update_keeper_ids).") and isvalid = true";
                     $res_keeper = $this->db->query($sql_keeper);
                 } */ 
                 //last steps: 批量插入推送消息
                 $content = "您的云店%s申请已经被驳回了！驳回原因：不符合资格。";
                 $sql_send_msg = "INSERT INTO send_weixinmsg_log (customer_id, createtime, type, content, openid) VALUES ";
                 foreach ($result_msg as $k => $v) { 
                    $sql_send_msg .= " ({$customer_id}, now(), 0, '".mysql_escape_string(sprintf($content, $v['name']))."', '{$v['weixin_fromuser']}'),";
                 }  
                  $sql_send_msg  = trim($sql_send_msg,',');
                  $res_send_msg = $this->db->query($sql_send_msg); 
       			 if($res){
       			 	$result = array('errcode' => 0 ,'errmsg' => '驳回成功');
       			 }else{
       			 	$result = array('errcode' => 400 ,'errmsg' => '驳回失败');
       			 }
    			break;
    		default:
    			$result = array('errcode' => 400 ,'errmsg' => 'more参数丢失');
    			break;
    	}
    	return $result;
    } 