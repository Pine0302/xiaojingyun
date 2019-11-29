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


class model_cailing{
    var $db;
    var $model_common;
    var $shopmessage;
    function __construct()
    {
        $this->db = DB::getInstance();
        require_once('model/common.php');
        $this->model_common = new model_common();
    } 

    /*
    版权信息:  秘密信息
    功能描述：彩铃订购——添加彩铃及修改
    开 发 者：Zoujunjie -- V397
    开发日期： 2018-05-16
    重要说明：无
     */   
    public function insert_color_bell($data){
        if ($data['op'] == 'edit') {
            $sql = "UPDATE ".WSY_SHOP.".colortone_prod set name='{$data['name']}',tip='{$data['tip']}',price={$data['price']},img_url='{$data['img_url']}',issale='{$data['issale']}',sort={$data['sort']},music='{$data['music']}' WHERE id = {$data['cailing_id']} AND customer_id=".$data['customer_id'];
            $res_insert = $this->db->query($sql);
            return $res_insert;
        }else{
            $time = date("Y-m-d H:i:s",time());
            $sql = "INSERT INTO ".WSY_SHOP.".colortone_prod (customer_id,name,tip,price,img_url,issale,sort,music,createtime) VALUES ({$data['customer_id']},'{$data['name']}','{$data['tip']}',{$data['price']},'{$data['img_url']}',0,{$data['sort']},'{$data['music']}','{$time}')";
            $res_insert = $this->db->query($sql);
            return $res_insert;
        }
       
    }

 /*
    版权信息:  秘密信息
    功能描述：彩铃管理————单个删除
    开 发 者：linrongdie-V409
    开发日期： 2018-05-18
    重要说明：无
     */
    function del_cailing_shopkeeper($param){
        $return            = array();
        $return['errcode'] = 0;
        $return['errmsg']  = "删除失败！";
        $customer_id = $param['customer_id'];
        $id          = $param['id'];
        $sql = "update ".WSY_SHOP.".colortone_prod set isvalid = 0 where customer_id='".$customer_id."' and id='".$id."' and isvalid = 1";
        $res = $this->db->query($sql);
          // print_r($res);
        if($res){
            $return['errcode'] = 1;
            $return['errmsg'] = "删除成功！";
            $return['id'] = $param['id'];

        }
        return $return;
    }

/*
    版权信息:  秘密信息
    功能描述：彩铃管理————批量删除
    开 发 者：linrongdie-V409
    开发日期： 2018-05-18
    重要说明：无
     */
    public function del_cailing($del_arr){
         
        $return = array('errcode'=>0,'errmsg'=>'删除成功！！');
        $query = "UPDATE ".WSY_SHOP.".colortone_prod set isvalid=0 where id IN(".$del_arr.")";
        $result = _mysql_query($query)or die("L132 : query error : ".mysql_error());
        return $return;
    }


     /*
    版权信息:  秘密信息
    功能描述：彩铃订购——彩铃管理-搜索
    开 发 者：linrongdie -- V409     $where .= " AND a.status = '".$status."'";
    开发日期： 2018-05-17
    重要说明：无
     */  
    public function get_ell_management($data){
        $page_size = 20; //每页多少条
        $page = $data['page'];
        $total = $data['total'];
        if ($page > $total) $page = $total;
        if ($page != '') {
            $pageNum   = $page > 1 ? $page : 1;//当前页,1开始
            $start     = ($pageNum-1)*$page_size;
        }
       
        
        $sql = "SELECT id,name,price,tip,img_url,issale,sort,createtime,isvalid FROM ".WSY_SHOP.".colortone_prod where customer_id ='".$data['customer_id']."' and isvalid =1  ";
        $sql_count = "SELECT count(id) FROM ".WSY_SHOP.".colortone_prod WHERE customer_id =".$data['customer_id']." AND isvalid = 1";

        if ($data['name'] != '') {
            $sql .= "  and name='".$data['name']."'";
            $sql_count .= "  and name='".$data['name']."'";
        }
        if ($data['issale'] != '' && $data['issale'] != 2) {
            $sql .= "  and issale='".$data['issale']."'";
            $sql_count .= "  and issale='".$data['issale']."'";
        }

        if ($page != '') {
            $sql .= " ORDER BY id desc LIMIT ".$start.",".$page_size;
        }else{
            $sql .= " ORDER BY id desc LIMIT 0,".$page_size;
        }

        $res_count = $this->db->getRow($sql_count);
        $res_select = $this->db->getAll($sql);
        return array('res_select' => $res_select,'res_count' => $res_count);
    }

 /*
    版权信息:  秘密信息
    功能描述：彩铃管理——商品上下架
    开 发 者：linrongdie-V409
    开发日期： 2018-05-21
    重要说明：无
     */
     public function change_isout_get($data){
        extract($data);
        $return = array();
        $return['errcode'] = 0;
        $return['errmsg'] = "修改失败";
        $sql = "update ".WSY_SHOP.".colortone_prod";
        $data2['id'] = $id;
        if($type_out == 1) {
            $sql.=" set issale=0 where id='".$id."'";
            $res =$this->db->query($sql);

            //插入商城商品日志表weixin_commonshop_product_log
            // $data2['log_type'] = 2;
            // $log =$this->set_product_log($data2);

            $return['errcode'] = 1;
            $return['errmsg'] = "下架商品成功！";
        }else if($type_out == 2){
            $sql.=" set issale=1 where id='".$id."'";
            $res =$this->db->query($sql);

            //插入商城商品日志表weixin_commonshop_product_log
            // $data2['log_type'] = 1;
            // $log =$this->set_product_log($data2);

            $return['errcode'] = 1;
            $return['errmsg'] = "上架商品成功！";
        }

        return $return;
     }






    /*
    版权信息:  秘密信息
    功能描述：记录文件上传纪录
    开 发 者：Zoujunjie -- V397
    开发日期： 2018-05-17
    重要说明：无
     */ 
    public function save_music_tmp($file_name,$chunk,$customer_id){
        $encode = mb_detect_encoding($file_name, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')); //1 获取当前字符串的编码
        $file_name = mb_convert_encoding($file_name, 'UTF-8', $encode);//2 将字符编码改为utf-8
        $data['customer_id'] = $customer_id;
        $data['chunk']       = $chunk;
        $data['file_name']   = $file_name;
        $data['isvalid']     = true;
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_order_blessing_video_tmp', $data, 'insert');
        return $result;
    }

    /*
    版权信息:  秘密信息
    功能描述：清除文件分片上传记录
    开 发 者：Zoujunjie -- V397
    开发日期： 2018-05-17
    重要说明：无
     */
    public function clear_music_tmp($file_name,$customer_id){
        $encode = mb_detect_encoding($file_name, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')); //1 获取当前字符串的编码
        $file_name = mb_convert_encoding($file_name, 'UTF-8', $encode);//2 将字符编码改为utf-8
                    
        $data['isvalid']     = false;
        
        $result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_order_blessing_video_tmp', $data, 'update',"customer_id='{$customer_id}' and file_name='{$file_name}'");  
    }

    /*
    版权信息:  秘密信息
    功能描述：清除文件分片上传记录
    开 发 者：Zoujunjie -- V397
    开发日期： 2018-05-17
    重要说明：无
     */
    public function update_success_music($file_name,$customer_id){
        $encode = mb_detect_encoding($file_name, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')); //1 获取当前字符串的编码
        $file_name = mb_convert_encoding($file_name, 'UTF-8', $encode);//2 将字符编码改为utf-8
        
        $con['customer_id'] = $customer_id;
        $con['file_name']   = $file_name;       
        
        $data['isvalid']     = false;
        $data['status']      = true;
        
        $result = $this->db->autoExecute(WSY_SHOP.'.weixin_commonshop_order_blessing_video_tmp', $data, 'update',"customer_id='{$customer_id}' and file_name='{$file_name}'");
    }

    /*
    版权信息:  秘密信息
    功能描述：检查文件分片上传记录数量
    开 发 者：Zoujunjie -- V397
    开发日期： 2018-05-17
    重要说明：无
     */
    public function check_music_tmp_exist($file_name,$chunk,$customer_id){
        $encode = mb_detect_encoding($file_name, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')); //1 获取当前字符串的编码
        $file_name = mb_convert_encoding($file_name, 'UTF-8', $encode);//2 将字符编码改为utf-8     
        
        $sql = "SELECT 1 FROM ".WSY_SHOP.".weixin_commonshop_order_blessing_video_tmp where file_name='{$file_name}' and customer_id='{$customer_id}' and chunk='{$chunk}' and isvalid=true limit 1";
        $result = $this->db->getRow($sql);
        return $result;     
    }

    /*
    版权信息: 秘密信息
    功能描述：获取彩铃订购基本设置
    开 发 者：liupeixin
    开发日期：2018-05-18
    重要说明：无
     */
    public function get_basic_settings($customer_id){
        $sql = "SELECT phone_check_but,card_show_but,card_position 
        FROM ".WSY_SHOP.".`colortone_setting` 
        WHERE customer_id = '{$customer_id}' AND isvalid = 1";
        $result = $this->db->getRow($sql);
        if(!$result){
            $data['customer_id'] = $customer_id;
            $data['phone_check_but']       = 0;
            $data['card_show_but']   = 0;
            $data['card_position']   = 1;
            $data['isvalid']     = true;
            $data['createtime'] = date('Y-m-d H:i:s',time());
            $result = $this->db->autoExecute(WSY_SHOP.'.colortone_setting', $data, 'insert');
            return $data;
        }
        return $result;
    }

    /*
    版权信息: 秘密信息
    功能描述：保存彩铃订购基本设置
    开 发 者：liupeixin
    开发日期：2018-05-18
    重要说明：无
     */
    public function setting_save($param){
        $sql = "UPDATE ".WSY_SHOP.".`colortone_setting` 
        SET phone_check_but = '".$param['phone_check_but']."',card_show_but = '".$param['card_show_but']."',card_position = '".$param['card_position']."' WHERE customer_id = '".$param['customer_id']."'";
        $res = $this->db->query($sql);
        if ($res) {
            $return['errcode'] = 1;
            $return['errmsg'] = "保存成功！";
        }else{
            $return['errcode'] = 400030;
            $return['errmsg'] = "保存失败！";        
        }

        //推广员名片初始化 start crm 15200
            if ($param['card_show_but'] == 1) {
                //查询是否初始化了名片
                $user_card_setting_sql = "select id from weixin_commonshop_user_contact_setting where customer_id='{$param['customer_id']}' and isvalid=true";
                $user_card_setting     = $this->db->getOne($user_card_setting_sql);

                //若没有数据，则初始化数据
                if (empty($user_card_setting)) {
                    $insert_card_set_sql = "insert into weixin_commonshop_user_contact_setting (customer_id,pass_level,jump_url,name_onoff,level_onoff,address_onoff,weixin_onoff,qq_onoff,phone_onoff,tip_onoff,introduce_onoff,follow_onoff,isvalid,jump_title,jump_linktype) values ('{$param['customer_id']}','-1_1_2_3_4_5','/addons/index.php/micro_broadcast/User/index?customer_id={$param['customer_id']}',1,1,1,1,1,1,1,1,1,true,'首页','-2-1-首页')";
                    $this->db->query($insert_card_set_sql);
                }
            }
        //推广员名片初始化 end

        return $return;
    }   

    /*
    版权信息: 秘密信息
    功能描述：获取彩铃订购订单详情
    开 发 者：liupeixin
    开发日期：2018-05-21
    重要说明：无
     */
    public function select_order_details($param){
        $sql = "SELECT cot.batchcode,cot.money,cot.createtime AS cot_createtime,cot.confirmtime,cot.dabletime,cot.paystyle,cot.paytime,cot.status,cot.paystatus,cot.recoverytime,cot.use_phone,
        cp.name AS cp_name,cp.price,cp.tip,cp.img_url,
        wu.name AS wu_name,wu.id,
        col.operator,col.createtime AS col_createtime,col.type,col.content 
        FROM ".WSY_SHOP.".`colortone_order_t` AS cot 
        LEFT JOIN ".WSY_SHOP.".`colortone_prod` AS cp ON cp.id = cot.colortone_id 
        LEFT JOIN ".WSY_USER.".`weixin_users` AS wu ON wu.id = cot.user_id 
        LEFT JOIN ".WSY_SHOP.".`colortone_order_log` AS col ON col.batchcode = cot.batchcode AND col.isvalid = 1 
        WHERE cot.batchcode = '".$param['batchcode']."' AND cot.customer_id = '".$param['customer_id']."' AND cot.customer_del = 0 AND cot.isvalid = 1 
        ORDER BY col.id DESC";
        $res = $this->db->getAll($sql);
        if(!$res){
            $res = array('errcode' => 400 ,'errmsg' => '订单错误');
        }
        return $res;
    }

 /*
    版权信息:  秘密信息
    功能描述： 彩铃管理——分页
    开 发 者：linrongdie-V409
    开发日期： 2018-05-21
    重要说明：无
 */
  
   public function get_identity($customer_id)
    {
        $result['errcode'] = 0;
        $result['errmsg']  = "";

        if(empty($customer_id) || $customer_id < 0)
        {
            $result['errcode'] = 400;
            $result['errmsg']  = "customer_id参数错误";
            return $result;
        }

        $sql = "select id,name,price,tip,img_url,issale,sort,createtime,isvalid from ".WSY_SHOP.".colortone_prod where customer_id ='".$customer_id."' and isvalid =1";
       

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
    版权信息: 秘密信息
    功能描述：获取彩铃商品详情
    开 发 者：Zoujunjie  v397
    开发日期：2018-05-22
    重要说明：无
     */
    function color_ring_editor($cailing_id,$customer_id){
        $sql = "SELECT id,name,price,tip,img_url,music,sort,issale FROM ".WSY_SHOP.".colortone_prod WHERE customer_id ='".$customer_id."' AND isvalid =1 AND id='".$cailing_id."' ";
        $res_select = $this->db->getRow($sql);
        return $res_select;
    }

    /**彩铃订单管理查询
    * @author  HMJ-V384
    * @param  
    * @version  2018-05-25
    * @return  
    * @var  
    */   
    function order_list_management($param){
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum  = $param['pageNum'] ? : 1; //当前页,1开始
        $start    = ($pageNum-1)*$pageSize;
        $end      = $pageSize;

        //分页设置 end
        $time_now = date('Y-m-d H:i:s');
        $order_arr      = array();
        $batchcode      = -1;
        $use_phone      = -1;
        $paystyle       = -1;
        $status         = -1;
        $customer_id    = $param['customer_id'];

        if(!empty($param['batchcode'])){
            $batchcode     = (int)$param['batchcode'];
        }
        if($param['use_phone']){
            $use_phone  = mysql_escape_string($param['use_phone']);
        }
        if($param['paystyle']){
            $paystyle = mysql_escape_string($param['paystyle']);
        }
        if($param['status']){
            $status   = (int)$param['status'];              
        }

        $sql = "SELECT k.id,k.batchcode,k.use_phone,k.colortone_name,k.money,k.user_id,k.createtime,k.paystyle,k.status,k.paytime,k.paystatus,u.weixin_name,k.recoverytime 
         FROM ".WSY_SHOP.".`colortone_order_t` k
         LEFT JOIN ".WSY_USER.".`weixin_users` u ON k.user_id=u.id AND u.isvalid=true 
         WHERE k.customer_id='".$customer_id."' AND k.isvalid=true AND k.customer_del=false ";
        /************** 搜索条件 start ******************/
        if($batchcode!=-1){
            $sql .= " AND k.batchcode like '%".$batchcode."%'";
        }
        if($use_phone!=-1){
            $sql .= " AND k.use_phone like '%".$use_phone."%'";
        }
        if($paystyle!=-1){
            if($paystyle == '提单不支付'){
                $sql .= " AND k.paystatus = 0 AND k.recoverytime>='{$time_now}' AND k.status != 4 AND k.status != -1"; 
            } else {
                $sql .= " AND k.paystyle like '%".$paystyle."%'";
            }
        }
        if($status!=-1){
            if($status == 1) {//未支付
                $sql .= " AND k.paystatus = 0 AND k.recoverytime>='{$time_now}' AND k.status != 4 AND k.status != -1"; 
            } else if($status == 2) {//待完成
                $sql .= " AND k.paystatus = 1 AND k.status = 2 "; 
            } else if($status == 3) {//已完成
                $sql .= " AND k.paystatus = 1 AND k.status = 1 "; 
            } else if($status == 4) {//已退款
                $sql .= " AND k.status = 3 ";                
            }
        }
        /************** 搜索条件 end ******************/
        $order_total = $this->db->getAll($sql);
        $order_count = count($order_total);//总共多少条记录

        if( $param['pageNum'] > 0 ){
            $sql .= " order by k.id desc limit ".$start.",".$end;
        }

        $order_arr = $this->db->getAll($sql);
        $pageCount = ceil($order_count/$pageSize);//总页数

        $return['pageCount'] = $pageCount;
        $return['order_arr'] = $order_arr;
        return $return;
    }

    /**彩铃订单管理列表--订单处理---
    * @author  HMJ-V384
    * @param  type：0确认支付1确认完成2退款3删除4详情5备注 POST
    * @version  2018-05-25
    * @return  
    * @var  
    */    
    function order_deal($customer_id,$type,$id,$batchcode,$content){
        //读操作人昵称
        if($_SESSION['is_auth_user'] == 'yes') { //权限用户--子账户
            $login_name = $_SESSION['curr_login'];
        } else {
            $sql = "SELECT login_name FROM customers WHERE id = '{$customer_id}' AND isvalid=true ";
            $login_name = $this->db->getOne($sql);            
        }

        $data = [];
        $log_insert_arr = [];
        $log_insert_arr['isvalid']      = 1;
        $log_insert_arr['batchcode']    = $batchcode;
        $log_insert_arr['customer_id']  = $customer_id;
        $log_insert_arr['createtime']   = date('Y-m-d H:i:s');
        $log_insert_arr['operator']     = $login_name;
        $log_insert_arr['content']      = $content;
        switch ($type) {
            case '0':
                $ord_update_arr = [];
                $ord_update_arr['paystatus']    = 1;
                $ord_update_arr['paystyle']     = '后台支付';
                $data['paytime']                = $ord_update_arr['paytime']      = date('Y-m-d H:i:s');
                
                $log_insert_arr['type']         = 2;
                $res1 = $this->db->autoExecute(WSY_SHOP.'.colortone_order_t', $ord_update_arr, 'update',"customer_id = '{$customer_id}' AND id = '{$id}' AND isvalid=true ");
                $res2 = $this->db->autoExecute(WSY_SHOP.'.colortone_order_log', $log_insert_arr, 'insert');
                $return = array('errcode' => 0, 'errmsg' => '支付操作成功！', 'data' => $data);
                break;
            case '1':
                $ord_update_arr = [];
                $ord_update_arr['status']       = 1;
                $ord_update_arr['confirmtime']  = date('Y-m-d H:i:s');

                $log_insert_arr['type']         = 3;
                $res1 = $this->db->autoExecute(WSY_SHOP.'.colortone_order_t', $ord_update_arr, 'update',"customer_id = '{$customer_id}' AND id = '{$id}' AND isvalid=true ");
                $res2 = $this->db->autoExecute(WSY_SHOP.'.colortone_order_log', $log_insert_arr, 'insert');
                $return = array('errcode' => 0, 'errmsg' => '确认操作成功！', 'data' => '');
                break;
            case '2':
                $ord_update_arr = [];
                $ord_update_arr['status']       = 3;
                $ord_update_arr['confirmtime']  = date('Y-m-d H:i:s');
                $ord_update_arr['dabletime']    = date('Y-m-d H:i:s');
                $log_insert_arr['type']         = 4;                
                $res1 = $this->db->autoExecute(WSY_SHOP.'.colortone_order_t', $ord_update_arr, 'update',"customer_id = '{$customer_id}' AND id = '{$id}' AND isvalid=true ");
                $res2 = $this->db->autoExecute(WSY_SHOP.'.colortone_order_log', $log_insert_arr, 'insert');
                $return = array('errcode' => 0, 'errmsg' => '退款操作成功！', 'data' => '');          
                break;
            case '3':
                $ord_update_arr = [];
                $ord_update_arr['customer_del'] = 1;

                $log_insert_arr['type']         = 5;
                $res1 = $this->db->autoExecute(WSY_SHOP.'.colortone_order_t', $ord_update_arr, 'update',"customer_id = '{$customer_id}' AND id = '{$id}' AND isvalid=true ");
                $res2 = $this->db->autoExecute(WSY_SHOP.'.colortone_order_log', $log_insert_arr, 'insert');
                $return = array('errcode' => 0, 'errmsg' => '删除成功！', 'data' => '');    
                break;
            case '5':
                $log_insert_arr['type']         = 1;
                $res2 = $this->db->autoExecute(WSY_SHOP.'.colortone_order_log', $log_insert_arr, 'insert');
                $return = array('errcode' => 0, 'errmsg' => '备注成功！', 'data' => '');
                break;                
            default:
                $return = array('errcode' => 0, 'errmsg' => '操作异常！', 'data' => '');
                break;  
        }

        return $return;  
    }

    /*
    版权信息: 秘密信息
    功能描述：彩铃订购——排序
    开 发 者：zoujunjie v397
    开发日期：2018-05-29
    重要说明：无
     */
    public function setting_sort($data){
        $sql = "SELECT id FROM ".WSY_SHOP.".colortone_prod WHERE customer_id ='".$data['customer_id']."' AND sort='".$data['sort']."' ";
        $res_select = $this->db->getOne($sql);
        if ($res_select != '') {
            $return = array('errcode' => 1, 'errmsg' => '已存在', 'data' => $res_select);
        }else{
            $return = array('errcode' => 0, 'errmsg' => '可以使用', 'data' => $res_select);
        }
        return $return;
    }


}//类结束
