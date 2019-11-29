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

 
 
class model_yundian{
    var $db;
    var $model_common;
    var $shopmessage;
    function __construct()
    {
        $this->db = DB::getInstance();
        require_once('model/common.php');
        require_once($_SERVER["DOCUMENT_ROOT"].'/weixinpl/common/utility_shop.php');  //商城方法
        $this->model_common = new model_common();
        $this->shopmessage = new shopMessage_Utlity();
    }  

    /*
     * 店主审核列表
     * $Author: hjw$
     * $2018-04-04  $
     */
    public function shopkeeper_review_list($data){
        extract($data);
        $pageNum   = $page > 1 ? $page : 1;//当前页,1开始
        $page_size = $page_size; //每页多少条
        $start     = ($pageNum-1)*$page_size;
        $sql       = "select a.id,a.user_id,a.tequan_id,a.phone,a.realname,a.identity_num,
                a.apply_time,a.verify_time,a.status,a.reject_desc,a.store_name,i.name AS identity_name,
                u.weixin_name as name,i.reward FROM ".WSY_REBATE.".weixin_yundian_identity_applylog a 
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
            $where .= " AND u.weixin_name LIKE '%".$user_name."%'";
        }
        if(!empty($store_name)){
            $where .= " AND a.store_name LIKE '%".$store_name."%'";
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
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表查询
    开 发 者：HMJ-V384
    开发日期： 2018-04-04
    重要说明：无
     */
    function get_yundian_shopkeeper_list($param){
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum  = $param['pageNum'] ? : 1; //当前页,1开始
        $start    = ($pageNum-1)*$pageSize;
        $end      = $pageSize;

        //分页设置 end
        $shopkeeper_arr = array();
        $user_id        = -1;
        $name           = -1;
        $verify_time     = -1;
        $expire_time    = -1;
        $tequan_id      = -1;
        $customer_id    = $param['customer_id'];

        if(!empty($param['user_id'])){
            $user_id     = (int)$param['user_id'];
        }
        if($param['name']){
            $name        = mysql_escape_string($param['name']);
        }
        if($param['store_name']){
            $store_name  = $param['store_name'];
        }
        if($param['verify_time']){
            $verify_time  = $param['verify_time'];
        }
        if($param['expire_time']){
            $expire_time = $param['expire_time'];
        }
        if($param['tequan_id']){
            $tequan_id   = (int)$param['tequan_id'];
        }

        $sql = "select k.user_id,k.realname,k.verify_time,k.expire_time,k.tequan_id,k.store_name,k.status,k.phone,ifnull(count(p.isout), 0) as isup,p.yundian_id,
         k.profit_shop,k.profit_self,k.product_count,k.order_count,u.weixin_name AS name,i.name AS identity_name,k.profit_keeper FROM ".WSY_USER.".`weixin_yundian_keeper` k
         LEFT JOIN ".WSY_USER.".`weixin_users` u ON k.user_id=u.id AND u.isvalid='1'
         LEFT JOIN ".WSY_REBATE.".`weixin_yundian_identity` i ON k.tequan_id=i.id AND i.isvalid='1'
         LEFT JOIN ".WSY_PROD.".`weixin_commonshop_products` p ON p.yundian_id=k.id AND p.isvalid='1' AND p.isout=0 
         WHERE k.customer_id='".$customer_id."' AND k.isvalid='1' AND k.status='1'";
        /************** 搜索条件 start ******************/
        if($user_id!=-1){
            $sql .= " AND k.user_id like '%".$user_id."%'";
        }
        if($name!=-1){
            $sql .= " AND u.name like '%".$name."%'";
        }
        if($store_name!=-1){
            $sql .= " AND k.store_name like '%".$store_name."%'";
        }
        if($verify_time!=-1){
            $sql .= " AND k.verify_time >= '".$verify_time."'";
        }
        if($expire_time!=-1){
            $sql .= " AND k.expire_time <= '".$expire_time."'";
        }
        if($tequan_id!=-1){
            $sql .= " AND k.tequan_id = '".$tequan_id."' ";
        }
        // if( $param['threshold'] || $param['threshold'] === 0 ){
        //     $sql .= " AND threshold<=".$param['threshold'];
        // }
        if( $param['nowtime'] ){
            $sql .= " AND k.expire_time >= '{$param['nowtime']}' ";
            $sql .= " AND k.verify_time <= '{$param['nowtime']}' ";
        }
        /************** 搜索条件 end ******************/
        $sql .= " group by k.id";
        $shopkeeper_total = $this->db->getAll($sql);
        $shopkeeper_count = count($shopkeeper_total);//总共多少条记录

        if( $param['pageNum'] > 0 ){
            $sql .= " order by k.id desc limit ".$start.",".$end;
        }

        if( $param['order'] ){
            $sql .= " order by ".$param['order'];
        }


        $shopkeeper_arr = $this->db->getAll($sql);
        $pageCount = ceil($shopkeeper_count/$pageSize);//总页数

//        //总订单查询
//        $sql1 = "select ifnull(count(*), 0) as order_sum,yundian_id
//         FROM weixin_commonshop_orders
//         WHERE customer_id='".$customer_id."' AND isvalid='1' AND paystatus=1 AND yundian_id!='-1' group by yundian_id";
//        //总订单查询end
//        $order_sum_arr = $this->db->getAll($sql1);


//        //总订单查询
        $sql1 = "SELECT batchcode,yundian_id from weixin_commonshop_orders where customer_id='".$customer_id."' AND isvalid='1' AND paystatus=1 AND yundian_id!='-1' group by batchcode";
        $order1_arr = $this->db->getAll($sql1);
        foreach($order1_arr as $k => $v){
            $order_sum1_arr[$v['yundian_id']][] = $v;
        }
        $i=0;
        foreach($order_sum1_arr as $k => $v){
            $order_sum_arr[$i] = array('order_sum'=>count($order_sum1_arr[$k]),'yundian_id'=>$k);
            $i++;
        }
        //总订单查询end

         foreach ($shopkeeper_arr as $key0 => $value0) {
             foreach ($order_sum_arr as $key1 => $value1) {
                 if($value0['yundian_id'] == $value1['yundian_id']) {
                    $shopkeeper_arr[$key0]['order_sum'] = $value1['order_sum'];
                    unset($order_sum_arr[$key1]);break;
                 }
             }
             if(!isset($shopkeeper_arr[$key0]['order_sum'])) {
                $shopkeeper_arr[$key0]['order_sum'] = 0;
             }
         }
        // $return['sql'] = $sql;
        $return['pageCount'] = $pageCount;
        $return['shopkeeper_arr'] = $shopkeeper_arr;
        return $return;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表查询
    开 发 者：HMJ-V384
    开发日期： 2018-04-04
    重要说明：无
     */
    function setting_list_select($customer_id){

        $result = array();
        $result['errcode'] = 0;
        $result['errmsg'] = '';

        $sql = "select * from ".WSY_REBATE.".weixin_yundian_setting where customer_id='".$customer_id."' and isvalid=true";
        $res = $this->db->getRow($sql);

        if ($res) {
            $sql2 = "select id,is_identity,name,reward,apply_money,tequan,remark from ".WSY_REBATE.".weixin_yundian_identity where customer_id='".$customer_id."' and isvalid=true order by reward DESC,createtime ASC";
            $result2 = $this->db->getAll($sql2);
        }else{
            $result['errcode'] = 400030;
            $result['errmsg']  = '商家还未设置云店';
            return $result;
        }
        $result['res1'] = $res;
        $result['res2'] = $result2;
        return $result;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——基本配置 初始化
    开 发 者：zqs
    开发日期： 2018-04-04
    重要说明：无
     */
     function initialize_setting($data_ini,$data_ini2){
        //初始化weixin_yundian_setting表数据
        $init_set = $this->db->autoExecute(WSY_REBATE.'.weixin_yundian_setting',$data_ini, 'insert');
        //初始化weixin_yundian_identity表数据
        $init_iden = $this->db->autoExecute(WSY_REBATE.'.weixin_yundian_identity',$data_ini2, 'insert');
     }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——基本配置保存
    开 发 者：zhangqiusong
    开发日期： 2018-04-04
    重要说明：无
     */
    function sava_setting($data){
        $customer_id  = $data['customer_id'];
        $return       = array();
        //判断是否有数据
        $where   = "customer_id='".$customer_id."' and isvalid=true";
        $sql     = "select id from ".WSY_REBATE.".weixin_yundian_setting where ".$where;
        $is_res  = $this->db->getOne($sql);
        // _file_put_contents("log/23456" . $this->today . ".txt", "=====".var_export($data,true)."\r\n",FILE_APPEND);
        if ($is_res) {
            $res = $this->db->autoExecute(WSY_REBATE.'.weixin_yundian_setting',$data,'update',$where);
        }else{
            $res = $this->db->autoExecute(WSY_REBATE.'.weixin_yundian_setting',$data,'insert');
        }
        if ($res) {
            $return['errcode'] = 0;
            $return['errmsg'] = "保存成功！";
        }
        return $return;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——基本特权保存
    开 发 者：zhangqiusong
    开发日期： 2018-04-04
    重要说明：无
     */
    function sava_tequan($data2){
        $customer_id  = $data2['customer_id'];
        $where = "id=".$data2['id'];
        $res = $this->db->autoExecute(WSY_REBATE.'.weixin_yundian_identity',$data2,'update',$where);
        return $res;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——云店身份删除
    开 发 者：zhangqiusong
    开发日期： 2018-04-09
    重要说明：无
     */
    function identity_del($data){
        $result            = array();
        $return['errcode'] = 0;
        $return['errmsg']  = "删除失败";

        $sql = "select user_id from ".WSY_USER.".weixin_yundian_keeper where customer_id='".$data['customer_id']."' and isvalid=true and tequan_id='".$data['id']."' and status=1";
        $res = $this->db->getOne($sql);

        $sql_tequan = "select name from ".WSY_REBATE.".weixin_yundian_identity where id='".$data['id']."' ";
        $res_tequan = $this->db->getAll($sql_tequan);

        if (empty($res)) 
        {
            $sql2 = "update ".WSY_REBATE.".weixin_yundian_identity set isvalid=false where id='".$data['id']."'";
            $res2 = $this->db->query($sql2);
            $return['errcode'] = 1;
            $return['errmsg']  = "删除成功";

            $log_remark['customer_id'] = $data['customer_id'];
            $log_remark['title'] = '删除'.$res_tequan[0]['name'].'店主身份';
            $log_remark['remark'] = '删除'.$res_tequan[0]['name'].'店主身份';
            $log = $this->save_admin_yundian_log($log_remark);
        }
        else
        {
            $return['errcode'] = 40003;
            $return['errmsg'] = "该店主等级存在用户，无法删除！";
        }
        return $return;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——云店特权身份添加
    开 发 者：zhangqiusong
    开发日期： 2018-04-04
    重要说明：无
     */
    function identity_add($data){
        $result            = array();
        $result['errcode'] = 0;
        $result['errmsg']  = "删除失败";

        //查询出有几个云店身份
        $sql = "select id from ".WSY_REBATE.".weixin_yundian_identity where customer_id='".$data['customer_id']."' and isvalid=true";
        $res = $this->db->getAll($sql);
        $num = count($res);

        if ($num >= 5) 
        {
            $result['errcode'] = 40003;
            $result['errmsg'] = "云店店主身份最多只能添加5个！";
        }
        else
        {   
            $data2 = array('is_identity' => false, 
                           'customer_id' => $data['customer_id'],
                           'isvalid'     => true,
                           'name'        => '云店店主',
                           'reward'      => '0',
                           'apply_money' => '0',
                           'tequan'      => '1_1_1_1_1_1_1_1_1_1_1_1_1',
                           'remark'      => '',
                           'createtime'  => date('Y-m-d H:i:s',time())
                           );
            $res2 = $this->db->autoExecute(WSY_REBATE.'.weixin_yundian_identity', $data2, 'insert');
            $result['errcode'] = 1;
            $result['errmsg'] = "云店店主身份添加成功！";

            $log_remark['customer_id'] = $data['customer_id'];
            $log_remark['title'] = '添加云店店主身份';
            $log_remark['remark'] = '添加云店店主身份';
            $log = $this->save_admin_yundian_log($log_remark);
        }
        return $result;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——特权描述编辑
    开 发 者：zhangqiusong
    开发日期： 2018-04-04
    重要说明：无
     */
    function identity_edit($data){
        $customer_id  = $data['customer_id'];
        $id           = $data['id'];
        $type         = $data['type'];
        if ($type == "edit") {
            $remark = $data['remark'];
            $sql = "update ".WSY_REBATE.".weixin_yundian_identity set remark='".$remark."' where id='".$id."'";
            $res = $this->db->query($sql);
        }else{
            $sql = "select remark from ".WSY_REBATE.".weixin_yundian_identity where id='".$id."'";
            $res = $this->db->getOne($sql);
        }
        return $res;
    }

    /*
    * 云店日志公用方法
    * $Author: cjj
    * $2018-04-08  $
    * $data=['customer_id','remark'];  //需要传入的数据,配置参数json的从数据库中查找    customer_id //商家ID   remark //备注
    */
    function save_admin_yundian_log($data){
        $customer_id            = $data['customer_id'];
        $data['operationuser']  = $_SESSION['curr_login'];
        $data['createtime']     = date('Y-m-d H:i:s',time());

        $query = "select customer_id,yundian_onoff,yundian_apply_onoff,yundian_choucheng,receipt_onoff,receipt_time,invalid_onoff,invalid_time,clearing_onoff,playmoney_onoff,complete_onoff,shop_valid_time,shop_notice_time,yundian_reward,yundian_bg from ".WSY_REBATE.".weixin_yundian_setting where customer_id=" . $customer_id . " ";

        $res_basic     = $this->db->getRow($query);     //获取操作后的配置参数
        $data['json']  = json_encode($res_basic, JSON_UNESCAPED_UNICODE);

        $res = $this->db->autoExecute(WSY_REBATE.'.weixin_yundian_setting_log', $data, 'insert');//插入integral_log表

        return $res;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——删除店主
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
     */
    function del_yundian_shopkeeper($param){
        $return            = array();
        $return['errcode'] = 0;
        $return['errmsg']  = "删除失败！";

        $customer_id = $param['customer_id'];
        $user_id     = $param['user_id'];

        $sql = "update ".WSY_USER.".`weixin_yundian_keeper` set isvalid=false where customer_id='".$customer_id."' and user_id='".$user_id."' and isvalid=true";
        $res = $this->db->query($sql);
        if($res){
            $return['errcode'] = 1;
            $return['errmsg'] = "删除成功！";
        }
        return $return;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主商品列表查询
    开 发 者：zqs
    开发日期： 2018-04-10
    重要说明：无
     */
    public function shopkeeper_order_select($data){
        $result = array();
        $sql = "select p.id,k.realname,k.user_id,k.store_name,p.default_imgurl,p.name,t.name as fenlei,p.now_price,p.storenum,p.sell_count,p.isout from ".WSY_PROD.".weixin_commonshop_products as p INNER JOIN ".WSY_PROD.".weixin_commonshop_types as t ON p.type_id= t.id INNER JOIN ".WSY_USER.".weixin_yundian_keeper as k ON k.id=p.yundian_id where";
        $where = " p.customer_id='".$data['customer_id']."' and p.isvalid=true and k.isvalid=true";
        if (!empty($data['realname'])) {
            $data['realname'] = mysql_escape_string($data['realname']);
            $where.=" and k.realname like '%".$data['realname']."%'";
        }
        if (!empty($data['user_id'])) {
            $where.=" and k.user_id like '%".$data['user_id']."%'";
        }
        if (!empty($data['name'])) {
            $data['name'] = mysql_escape_string($data['name']);
            $where.=" and p.name like '%".$data['name']."%'";
        }
        if (!empty($data['store_name'])) {
            $data['store_name'] = mysql_escape_string($data['store_name']);
            $where.=" and k.store_name like '%".$data['store_name']."%'";
        }
        if ($data['type'] == 2) {
            $where.=" and p.isout=false";
        }else if($data['type'] == 3){
            $where.=" and p.isout=true";
        }
        $where.=" order by p.id desc";
        //计算出查询分页数据
        $a = ($data['pageNum']-1)*$data['page_size'];
        $b = $data['page_size'];
        $where.=" limit ".$a.",".$b;
        $sql.=$where;
        // echo $sql;
        $res = $this->db->getAll($sql);
        return $res;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——获取商品列表数量
    开 发 者：zqs
    开发日期： 2018-04-10
    重要说明：无
     */
    public function get_shopkeeper_order_num($data){
        //获取所有商品数量
        $sql = "select count(*) from ".WSY_PROD.".weixin_commonshop_products as p INNER JOIN ".WSY_PROD.".weixin_commonshop_types as t ON p.type_id= t.id INNER JOIN ".WSY_USER.".weixin_yundian_keeper as k ON k.id=p.yundian_id and k.isvalid=1 where";
        //查询所有商品数量
        $sql_all = $sql." p.customer_id='".$data['customer_id']."' and p.isvalid=true";
        
        //查询上架商品数量
        $sql_on = $sql." p.customer_id='".$data['customer_id']."' and p.isvalid=true and p.isout=false";

        //查询下架商品数量
        $sql_out = $sql." p.customer_id='".$data['customer_id']."' and p.isvalid=true and p.isout=true";
        
        if (!empty($data['realname'])) {
            $data['realname'] = mysql_escape_string($data['realname']);
            $sql_all.=" and k.realname like '%".$data['realname']."%'";
            $sql_on.=" and k.realname like '%".$data['realname']."%'";
            $sql_out.=" and k.realname like '%".$data['realname']."%'";
        }
        if (!empty($data['user_id'])) {
            $sql_all.=" and k.user_id like '%".$data['user_id']."%'";
            $sql_on.=" and k.user_id like '%".$data['user_id']."%'";
            $sql_out.=" and k.user_id like '%".$data['user_id']."%'";
        }
        if (!empty($data['name'])) {
            $data['name'] = mysql_escape_string($data['name']);
            $sql_all.=" and p.name like '%".$data['name']."%'";
            $sql_on.=" and p.name like '%".$data['name']."%'";
            $sql_out.=" and p.name like '%".$data['name']."%'";
        }
        if (!empty($data['store_name'])) {
            $data['store_name'] = mysql_escape_string($data['store_name']);
            $sql_all.=" and k.store_name like '%".$data['store_name']."%'";
            $sql_on.=" and k.store_name like '%".$data['store_name']."%'";
            $sql_out.=" and k.store_name like '%".$data['store_name']."%'";
        }
        $result['all'] = $this->db->getOne($sql_all);
        $result['on'] = $this->db->getOne($sql_on);
        $result['out'] = $this->db->getOne($sql_out);
        // $result['all'] = count($res_all);
        // $result['on'] = count($res_on);
        // $result['out'] = count($res_out);
        return $result;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主商品上下架
    开 发 者：zqs
    开发日期： 2018-04-10
    重要说明：无
     */
     public function change_isout_get($data){
        extract($data);
        $return = array();
        $return['errcode'] = 0;
        $return['errmsg'] = "修改失败";
        $sql = "update ".WSY_PROD.".weixin_commonshop_products";
        $data2['product_id'] = $id;
        if($type_out == 1) {
            $sql.=" set isout=1 where id='".$id."'";
            $res =$this->db->query($sql);

            //插入商城商品日志表weixin_commonshop_product_log
            $data2['log_type'] = 2;
            $log =$this->set_product_log($data2);

            $return['errcode'] = 1;
            $return['errmsg'] = "下架商品成功！";
        }else if($type_out == 2){
            $sql.=" set isout=0 where id='".$id."'";
            $res =$this->db->query($sql);

            //插入商城商品日志表weixin_commonshop_product_log
            $data2['log_type'] = 1;
            $log =$this->set_product_log($data2);

            $return['errcode'] = 1;
            $return['errmsg'] = "上架商品成功！";
        }else if($type_out == 4){
            $sql.=" set isvalid=0 where id='".$id."'";
            $res =$this->db->query($sql);

            //插入商城商品日志表weixin_commonshop_product_log
            $data2['log_type'] = 3;
            $log =$this->set_product_log($data2);

            $return['errcode'] = 1;
            $return['errmsg'] = "成功删除店主商品！";
        }else{
            $return['errcode'] = 40003;
            $return['errmsg'] = "参数错误！";
        }

        return $return;
     }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——编辑店主，信息读取
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
     */
    public function get_yundian_shopkeeper($data){
        $result = [];
        $sql1   = "SELECT k.user_id,k.profit_shop,k.self_reware,k.expire_time,k.tequan_id,k.store_name,i.name FROM ".WSY_USER.".`weixin_yundian_keeper` k INNER JOIN
         ".WSY_REBATE.".weixin_yundian_identity i ON k.tequan_id=i.id WHERE k.customer_id='".$data['customer_id']."' and k.user_id= '".$data['user_id']."' and k.isvalid = '1' ";

        $result['keeper_msg'] = $this->db->getRow($sql1); //查询用户信息

        $result['yundian_identity'] = $this->get_yundian_identity($data['customer_id']); //查询所有身份

        return $result;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——获取所有特权ID NAME
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
     */
    public function get_yundian_identity($customer_id){
        $sql = "SELECT id,name FROM ".WSY_REBATE.".weixin_yundian_identity WHERE customer_id='".$customer_id."' and isvalid = '1' order by reward DESC,createtime ASC";

        $result = $this->db->getAll($sql); //查询所有身份

        return $result;
    }
    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主列表——编辑店主信息
    开 发 者：HMJ-V384
    开发日期： 2018-04-08
    重要说明：无
     */
    public function save_shopkeeper_data($data,$data2,$w) {
        $return['errcode'] = 0;
        $return['errmsg']  = "保存失败！";

        //查用用户现有特权ID
        $ret1 = $this->db->getRow ("select tequan_id from ".WSY_USER.".`weixin_yundian_keeper` where status = '1' and isvalid = true and user_id='".$w['user_id']."' and customer_id = '".$w['customer_id']."'");

        if($ret1['tequan_id'] != $data2['tequan_id']) {

            //事务处理
            $this->db->tran_begin();
            try{
                //更新特权表，待审核状态不写入
                $sql = "UPDATE ".WSY_REBATE.".`weixin_yundian_identity_applylog` SET `tequan_id` = '".$data2['tequan_id']."' where status = '1' and isvalid = true and user_id='".$w['user_id']."' and customer_id = '".$w['customer_id']."'";

                $ret2 = $this->db->query($sql,'',1) ;

                if(!$ret2) {
                    $return['errmsg']  = "特权ID处理中，无法修改！";
                } else {
                    $where = "status = '1' and isvalid = true and user_id='".$w['user_id']."' and customer_id = '".$w['customer_id']."'";

                    $res  = $this->db->autoExecute("".WSY_USER.".`weixin_yundian_keeper`", $data, 'update',$where) ;                      
                }

            if ($res) {
                $return['errcode'] = 1;
                $return['errmsg']  = "保存成功！";
                $return['data'] = $ret2;
            }            

            } catch(Exception $e){
                $this->db->tran_rollback();
                return $return;
            }
            $this->db->tran_commit();
            return $return;

        } else {
            if(!$ret1) {
                unset($data['tequan_id']);
            }

            $where = "status = '1' and isvalid = true and user_id='".$w['user_id']."' and customer_id = '".$w['customer_id']."'";

            $res  = $this->db->autoExecute("".WSY_USER.".`weixin_yundian_keeper`", $data, 'update',$where) ;           
            if ($res) {
                $return['errcode'] = 1;
                $return['errmsg']  = "保存成功！";
            } 
        }

        return $return;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主订单
    开 发 者：taojin
    开发日期： 2018-04-08
    重要说明：参数type: 0平台订单 1自营订单
     */
    public function yundian_order_list($data){
        $return['errcode']   = 400;
        $return['errmsg']    = "保存失败！";

        $where = '';
        if($data['type'] == 1){//获取货币单位
            $set = $this->currency_set($data['customer_id']);
        }
        if($data['batchcode'])       $where .= " and o.batchcode = '{$data['batchcode']}'";
        $where .= " and o.yundian_self = '{$data['type']}'";
        if($data['user_id']){
            //查询云店id
            $sql_yun = "SELECT id from ".WSY_USER.".weixin_yundian_keeper where user_id = '{$data['user_id']}' and isvalid =true and customer_id = '{$data['customer_id']}' ";
            $res_yun = $this->db->getAll($sql_yun);
            $ids = '';
            foreach($res_yun as $k => $v){
                $ids = $v['id'].',';
            }
            $ids1 = trim($ids,',');
            if($ids1){
                $where .= " and o.yundian_id in ({$ids1})";
            }else{
                $return['data']      = array(array(),0);
                $return['errcode']   = 0;
                $return['msg']       = 'success';
                return $return;
            }
        }
        if($data['yun_user_id']){
            //查询云店id
            $sql_yun2 = "SELECT id from ".WSY_USER.".weixin_yundian_keeper where user_id = '{$data['yun_user_id']}' and isvalid =true and customer_id = '{$data['customer_id']}' ";
            $res_yun2 = $this->db->getAll($sql_yun2);
            $ids = '';
            foreach($res_yun2 as $k => $v){
                $ids = $v['id'].',';
            }
            $ids = trim($ids,',');

            if($ids){
                $where .= " and o.yundian_id in ({$ids})";
            }else{
                $return['data']      = array(array(),0);
                $return['errcode']   = 0;
                $return['msg']       = 'success';
                return $return;
            }
        }

        if($data['name']){
        	$data['name'] = mysql_escape_string($data['name']);
            $sql_mem = "select k.id from ".WSY_USER.".weixin_users u inner join ".WSY_USER.".weixin_yundian_keeper k on u.id=k.user_id where k.customer_id = '{$data['customer_id']}' and u.name like '%{$data['name']}%'";
            $res_mem = $this->db->getAll($sql_mem);
            $ids = '';
            if($res_mem){
                foreach($res_mem as $k => $v){
                    $ids .= $v['id'].',';
                }
                $ids2 = trim($ids,',');
                if($ids2){
                    $where .= " and o.yundian_id in ({$ids2})";
                }
            }else{
                $return['data']      = array(array(),0);
                $return['errcode']   = 0;
                $return['msg']       = 'success';
                return $return;
            }
        }

        switch ($data['status']){
            case 0;//全部
                break;
            case 1;//代发货
                $where .= " and o.sendstatus = 0 and (o.paystatus = 1 and o.paystyle !='货到付款') and o.status = 0 and aftersale_type=0 ";
                break;
            case 2;//待收货
                $where .= " and o.sendstatus = 1 and (o.paystatus = 1 or o.paystyle ='货到付款')and o.status = 0 and aftersale_type=0 ";
                break;
            case 3;//待完成
                $where .= " and o.sendstatus = 2 and o.paystatus = 1 and o.status = 0 and aftersale_type=0 ";
                break;
            case 4;//交易完成
                $where .= ' and o.sendstatus = 2 and o.paystatus = 1 and o.status = 1 and aftersale_type=0 ';
                break;
            case 5;//退款
                if($data['type'] == 0){
                    $where .= " AND o.sendstatus in ('5','6') ";
                }else{
                    $where .= " and o.aftersale_type = 1 and o.paystatus = 1 ";
                }
                break;
            case 6;//退货
                if($data['type'] == 0){
                    $where .= " and ((o.sendstatus in ('5','6') and o.return_type = 0 ) or (o.sendstatus in ('3','4') and o.return_type = 1)) ";
                }else{
                    $where .= " and o.aftersale_type = 2 and o.paystatus = 1 ";
                }
                break;
            case 7;//换货
                if($data['type'] == 0){
                    $where .= "AND o.sendstatus in ('3','4') AND o.return_type=2 ";
                }else{
                    $where .= " and o.aftersale_type = 3 and o.paystatus = 1 ";
                }
                break;
        }

        $page = ($data['pageNum'] - 1)*$data['page_size'] . ','. $data['page_size'];
        $sql = "SELECT o.*,op.origin_price,op.price,op.recovery_time,u.weixin_name,u.name,u.phone,oa.name as a_name,oa.phone as a_phone,oa.location_p,oa.location_c,oa.location_a,oa.address,oa.identity,oa.identityimgt,oa.identityimgf from weixin_commonshop_orders o left join weixin_commonshop_order_prices op on op.batchcode = o.batchcode left join ".WSY_USER.".weixin_users u on u.id = o.user_id left join ".WSY_SHOP.".weixin_commonshop_order_addresses oa on oa.batchcode = o.batchcode   left join ".WSY_USER.".weixin_yundian_keeper yk on yk.id = o.yundian_id  where o.isvalid = true and o.paystatus = 1 {$where} and yundian_id>0 and o.customer_id = '{$data['customer_id']}' and o.is_sendorder <> 1 and o.is_collageActivities <> 2 and o.shopactivity_mark = 0  group by o.batchcode order by o.id desc limit {$page} ";

        $res_list = $this->db->getAll($sql);

        $res_count = $this->get_order_num($data,$data['type']);
        if( $res_list && $res_count > 0 ){
            foreach($res_list as $k => $res) {
                $sql2 = "SELECT recovery_time from weixin_commonshop_order_prices where batchcode = '{$v['batchcode']}'";
                $res2 = $this->db->getRow($sql2);
                $batchcode = $res['batchcode'];
                $supply_id = $res['upply_id'];
                $yundian_id = $res['yundian_id'];
                $yundian_self = $res['yundian_self'];
                $sendstatus = $res['sendstatus'];
                $paystatus = $res['paystatus'];
                $paystyle = $res ['paystyle'];
                $status = $res['status'];
                $aftersale_type = $res['aftersale_type'];
                $return_type = $res['return_type'];
                $recovery_time = $res2['recovery_time'];
                $aftersale_state = $res['aftersale_state'];
                $return_status = $res['return_status'];

                if($sendstatus == 0 && $paystatus == 0 && $paystyle != '货到付款' && $status == 0 && $aftersale_type == 0 && strtotime($recovery_time)>time()){
                    $status_str = '待付款';
                }else if(strtotime($recovery_time)<time() && $aftersale_type == 0 && $paystatus == 0){
                    $status_str = '订单已失效';
                }else if($sendstatus == 0 && ($paystatus==1 || $paystyle == '货到付款') && $status == 0 && $aftersale_type == 0){
                    $status_str = '待发货';
                }else if ($sendstatus == 1 && $paystatus == 1 && $status == 0 && $aftersale_type == 0){
                    $status_str = '待收货';
                }else if ($sendstatus == 2 && $paystatus == 1 && $status == 0 && $aftersale_type == 0){
                    $status_str = '待完成';
                }else if ($sendstatus == 2 && $paystatus == 1 && $status == 1 && $aftersale_type == 0){
                    $status_str = '交易完成';
                }
                if($data['type'] == 0){
                    $sendstatusstr = '';
                    $o_sendstatus = $res['sendstatus'];
                    $o_return_type = $res['return_type'];
                    $o_is_delay   = $res['is_delay'];
                    $o_return_status = $res['return_status'];
                    if($o_return_type >=0) {
                        switch ($o_sendstatus) {
                            case 1:
                                $sendstatusstr = "已发货";
                                if ($o_is_delay == 1) {
                                    $sendstatusstr .= "[申请延迟收货]";
                                }
                                break;
                            case 2:
                                $sendstatusstr = "顾客已收货";
                                break;
                            case 3:
                                $sendstatusstr = "顾客申请退货";
                                if ($o_return_type == 0) {
                                    $sendstatusstr = "申请退货(仅退款)";
                                } else if ($o_return_type == 2) {
                                    $sendstatusstr = "申请退货(换货)";
                                }
                                if ($o_return_status == 2) {
                                    $sendstatusstr .= "[已同意]";
                                } else if ($o_return_status == 3) {
                                    $sendstatusstr .= "[已驳回]";
                                } else if ($o_return_status == 5) {
                                    $sendstatusstr .= "[用户已退货]";
                                } else if ($o_return_status == 6) {
                                    $sendstatusstr .= "[已收到退货]";
                                }
                                break;
                            case 4:
                                $rt = "退货";
                                if ($o_return_type == 0) {

                                    $rt = "仅退款";
                                } else if ($o_return_type == 2) {

                                    $rt = "换货";
                                }
                                $sendstatusstr = "退货已确认(" . $rt . ")</b>";
                                break;
                            case 5:
                                $sendstatusstr = "顾客申请退款";
                                if ($o_return_status == 8) {
                                    $sendstatusstr .= "[已同意]";
                                } else if ($o_return_status == 9) {
                                    $sendstatusstr .= "[已驳回]";
                                }
                                break;
                            case 6:
                                $sendstatusstr = "退款完成";
                                break;
                        }
                    }
                    $status_str = $sendstatusstr?$sendstatusstr:$status_str;
                }else{
                    if ($aftersale_type == 1 && $paystatus == 1) {
                        $status_str = '退款';
                        if($sendstatus == 6 or $aftersale_state == 4){
                            $status_str = '退款完成';
                        }else{
                            if ($aftersale_state == 1) $status_str .= '申请中';
                        }
                    }else if ($aftersale_type == 2 && $paystatus == 1) {
                        $status_str = '退货';
                        switch ($return_type){
                            case 0;
                                $status_str = "退货(仅退款)";
                                break;
                            case 2;
                                $status_str = "换货";
                                break;
                        }
                        if ($aftersale_state == 1) $status_str .= '申请中';
                        if($return_type == 0){
                            if ($aftersale_state == 2 || $return_status == 2) $status_str = '退货(仅退款)已同意，待退款';
                            if ($aftersale_state == 3 || $return_status== 3) $status_str = '已驳回退货(仅退款)请求';
                            if ($return_status == 8 || $sendstatus == 4) $status_str = '退货(仅退款)完成';
                        }else if($return_type == 1){
                            if ($aftersale_state == 2 || $return_status== 2) $status_str = '已确认，等待退货';
                            if ($aftersale_state == 3 || $return_status== 3) $status_str = '已驳回退货请求';
                            if ($return_status == 5) $status_str = '退货(买家已退货)';
                            if ($return_status == 6) $status_str = '退货已收货，等待退款';
                            // if ($sendstatus == 4) $status_str = '退货完成';
                            if ($sendstatus == 4) $status_str = '退货,退款完成';
                        }
                    }else if ($aftersale_type == 3 && $paystatus == 1) {
                        $status_str = '换货';
                        if ($aftersale_state == 1) $status_str = '换货申请中';
                        if($return_type == 2){
                            if ($aftersale_state == 2 || $return_status== 2) $status_str = '已同意换货，请买家退货';
                            if ($aftersale_state == 3 || $return_status== 3) $status_str = '已驳回换货请求';
                            if ($return_status == 5) $status_str = '买家已退货，等待换货';
                            if ($return_status == 6 && $status != 1 && $sendstatus == 3) $status_str = '换货待发货';
                            if ($return_status == 6 && $status != 1 && $sendstatus == 1) $status_str = '换货待完成';
                            if ($return_status == 6 && $status == 1 && $sendstatus == 2) $status_str = '换货已完成';
                        }
                    }else if($status == -1){
                        $status_str = '已取消';
                    }
                }

                $res_list[$k]['status_str'] = $status_str;
                //获取下单人信息
                if($res['user_id'])        $res_list[$k]['name_str'] = 'ID：'.$res['user_id'].'<br/>';
                if($res['name'])           $res_list[$k]['name_str'] .= "昵称：{$res['name']}";
//                if($res['phone']) $res_list[$k]['name_str'] .= " / {$res['phone']}";
                $res_list[$k]['paystyle'] = $res_list[$k]['paystyle']?$res_list[$k]['paystyle']:'';
                if($res_list[$k]['Pay_Method'] == 1 && $res_list[$k]['paystyle']=='') $res_list[$k]['paystyle'] = '后台支付';
                //获取支付信息
                $pay_res = $this->get_pay_info($res);
                if($pay_res['errcode'] == 0) $res_list[$k]['pay_info'] = $pay_res['data'];
                if($data['type'] == 1){
                    //获取店主信息
                    $sql_M = "select u.weixin_name,u.name,u.phone,k.user_id from ".WSY_USER.".weixin_users u inner join ".WSY_USER.".weixin_yundian_keeper k on u.id=k.user_id where u.customer_id = '{$data['customer_id']}' and k.id = '{$res['yundian_id']}'";
                    $res_M = $this->db->getRow($sql_M);
                    if($res_M['user_id'])       $res_list[$k]['yundian_info'] = 'ID：'.$res_M['user_id'].'<br/>';
                    if($res_M['name'])          $res_list[$k]['yundian_info'] .= "昵称：{$res_M['name']}";
//                    if($res_M['phone'])         $res_list[$k]['yundian_info'] .= " / {$res_M['phone']}";

                    //获取货款金额
                    $sql_payment = "SELECT yundian_reward from weixin_commonshop_order_prices where batchcode = '{$res['batchcode']}' and customer_id = '{$data['customer_id']}'";
                    $res_payment = $this->db->getAll($sql_payment);
                    $payment = 0;
                    if($res_payment){
                        foreach ($res_payment as $v){
                            $payment += $v['yundian_reward'];
                        }
                    }
                    $res_list[$k]['yundian_reward'] = $payment;
                    //订单结算状态与货款状态
                    if($res['sendstatus'] == 2 && $res['paystatus'] == 1 && $res['status'] == 1){
                        $res_list[$k]['balance_str']    = '已完成';
                        $res_list[$k]['payment']        = "已结算：{$set['currency_symbol']}{$payment}";
                    }elseif ($res['aftersale_type'] == 1 && $res['paystatus'] == 1 && $res['sendstatus'] == 6){
                        $res_list[$k]['balance_str']    = '已完成';
                        $res_list[$k]['payment']        = "已结算：{$set['currency_symbol']}{$payment}";
                    }elseif($res['aftersale_type'] == 2 && $res['paystatus'] == 1 && ($res['return_status'] == 8 || $res['return_status'] == 1)){
                        $res_list[$k]['balance_str']    = '已完成';
                        $res_list[$k]['payment']        = "已结算：{$set['currency_symbol']}{$payment}";
                    }elseif($res['aftersale_type'] == 3 && $res['paystatus'] == 1 && ($res['return_status'] == 7 || $res['return_status'] == 1)){
                        $res_list[$k]['balance_str']    = '已完成';
                        $res_list[$k]['payment']        = "已结算：{$set['currency_symbol']}{$payment}";
                    }else{
                        $res_list[$k]['balance_str']    = '未完成';
                        $res_list[$k]['payment']        = "未结算：{$set['currency_symbol']}{$payment}";
                    }
                    if ($sendstatus == 4) {
                        $res_list[$k]['balance_str']    = '已完成';
                        $res_list[$k]['payment']        = "已结算：{$set['currency_symbol']}{$payment}";
                    }

                    //是否是退款订单，查询退款金额
                    $sql_return = 'select * from '.WSY_SHOP.".weixin_commonshop_order_rejects where batchcode = '{$res['batchcode']}'";
                    $res_return = $this->db->getRow($sql_return);
                    $res_list[$k]['account'] = isset($res_return['account'])?$res_return['account']:$res['totalprice'];
                }
            }

            $return['data']      = array($res_list,$res_count);
            $return['errcode']   = 0;
            $return['msg']       = 'success';
        }
        return $return;
    }
    
    /*
    版权信息:  秘密信息
    功能描述：获取货币单位
    开 发 者：taojin
    开发日期： 2018-04-09
    重要说明：
     */
    public function currency_set($customer_id){
        $sql = "SELECT type,currency_symbol,currency_text,symbol_position FROM weixin_currency_symbol_set WHERE customer_id=".$customer_id;
        $res = $this->db->getRow($sql);
        return $res;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——订单日志
    开 发 者：taojin
    开发日期： 2018-04-09
    重要说明：
     */
    public function yundian_order_log($batchcode,$user_id){
        //获取下单人的信息
        $sql2  = "select name,weixin_name,id,weixin_fromuser,phone FROM weixin_users WHERE id ='{$user_id}'";
        $res2  = $this->db->getAll($sql2);

        //订单日志
        $sql23  = "select operation,descript,operation_user,createtime,batchcode from weixin_commonshop_order_logs where isvalid = true and batchcode='{$batchcode}'";
        $res23  = $this->db->getAll($sql23);
        foreach($res23 as $k=>$v)
        {
            if(!empty($v['operation_user'])) $op_user[$k] = $v['operation_user']; //产品id

        }

        foreach($res23 as &$v)
        {
            if(strlen($v['operation_user']) >19){     //log日志有用户姓名，又有用户open_id，这里排除一下
                $sql25   = "select weixin_name,weixin_fromuser from weixin_users where isvalid = true and weixin_fromuser LIKE '%".$v['operation_user']."%' LIMIT 0,1";
                $res25   = $this->db->getAll($sql25);
                if(!empty($res25[0]['weixin_name'])){
                    $v['operation_user'] = $res25[0]['weixin_name'];
                }
            }
        }

        //操作人姓名
        $op_user_str = implode("','",$op_user);
        if(count($op_user) > 0)
        {
            $op_user_str = "'".$op_user_str."'";
            $sql24   = "select weixin_name,weixin_fromuser from weixin_users where isvalid = true and id = '{$user_id}'";
            $res24   = $this->db->getAll($sql24);
        }else{
            $res24   = $res2;
        }

        return array('errcode'=>0,'errmsg'=>'success','data'=>array('res23'=>$res23,'res2'=>$res2,'res24'=>$res24));
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——点击各类支付跳转url
    开 发 者：taojin
    开发日期： 2018-04-09
    重要说明：
     */
    public function get_pay_info($data){
        $url = '';
        $sql27 = "select callBackBatchcode,price,pay_batchcode from paycallback_t where isvalid=true and pay_batchcode = '{$data['pay_batchcode']}'";
        $res27 = $this->db->getAll($sql27);

        if($data['paystyle'] == '通联支付'){
            $url = "[<a href=\"/mshop/admin/Order/order/allipay_detail.php?allipay_orderid={$data['allipay_orderid']}\">". $data['allipay_orderid'] ."(点击查看)</a>]";;
        }elseif ($data['paystyle'] == '支付宝支付'){
            $alipaySql = "select pay_batchcode,transaction_id from system_order_pay_log where   pay_batchcode='".$data['pay_batchcode']."' limit 0,1";
            $res = $this->db->getRow($alipaySql);
            $transaction_id = $res['transaction_id'];
            $pay_batchcode = $res['pay_batchcode'];
            $url = "[<a href=\"/mshop/admin/Order/order/alipay_rsa_detail.php?pay_batchcode={$pay_batchcode}&batchcode={$data['batchcode']}\">". $transaction_id ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '通联分期支付'){
            $alipaySql = "select pay_batchcode,transaction_id from system_order_pay_log where pay_batchcode='".$data['pay_batchcode']."' limit 0,1";
            $res = $this->db->getRow($alipaySql);
            $transaction_id = $res['transaction_id'];
            $pay_batchcode = $res['pay_batchcode'];
            $url = "[<a href=\"/mshop/admin/Order/order/allinpay_rsa_detail.php?pay_batchcode={$pay_batchcode}&batchcode={$data['batchcode']}\">". $transaction_id ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '微信支付' or $data['paystyle'] == '找人代付' or $data['paystyle'] == '兴业银行公众号支付'){
            $weipay = "select transaction_id from weixin_weipay_notifys where isvalid=true and out_trade_no='".$data['pay_batchcode']."'";
            $result = $this->db->getRow($weipay);
            $transaction_id = $result['transaction_id'];

            $wxpay_version=1;
            $query_ver = "select version from pay_config where isvalid=true and customer_id=".$data['customer_id']." and pay_type = 'weipay' limit 1";
            $result_ver = $this->db->getRow($query_ver);
            $wxpay_version = $result_ver['version'];

            if($wxpay_version==2){
                $url = "[<a href=\"/mshop/admin/Order/order/weipay_detail.php?allipay_orderid=".$transaction_id."&pay_batchcode=".$data['pay_batchcode']."&batchcode={$data['batchcode']}\">". $transaction_id ."(点击查看)</a>]";
            }else{
                $url = "[<a href=\"/mshop/admin/Order/order/weipay_detail2.php?pay_batchcode=".$data['pay_batchcode']."&batchcode={$data['batchcode']}\">". $transaction_id ."(点击查看)</a>]";
            }
        }elseif ($data['paystyle'] == '环迅快捷支付' or $data['paystyle'] == '环迅微信支付'){
            foreach ($res27 as $k => $v)
            {
                if($data['pay_batchcode'] == $v['pay_batchcode'])
                {
                    $callBackBatchcode = $v['callBackBatchcode'];
                    $settlementprice   = $v['price'];
                }
            }

            $url = "[<a href=\"/mshop/admin/Order/order/hxpay_detail.php?pay_batchcode={$data['pay_batchcode']}&batchcode={$data['batchcode']}\">". $callBackBatchcode ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '威富通支付'){
            $wftpay = "select transaction_id,wft_type,real_pay_price from system_order_pay_log where pay_batchcode='".$data['pay_batchcode']."'";
            $res = $this->db->getRow($wftpay);
            $transaction_id =$res['transaction_id'];
            $wft_type = $res['wft_type'];
            $settlementprice = $res['real_pay_price'];
            $url = "[<a href=\"/mshop/admin/Order/order/wftpay_detail.php?allipay_orderid=".$transaction_id."&wft_type=".$wft_type."&pay_batchcode=".$data['pay_batchcode']."&batchcode={$data['batchcode']}\">". $transaction_id ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '健康钱包支付'){
            foreach ($res27 as $k => $v)
            {
                if($data['pay_batchcode'] == $v['pay_batchcode'])
                {
                    $callBackBatchcode = $v['callBackBatchcode'];
                    $settlementprice   = $v['price'];
                }
            }
            $url = "[<a href=\"/mshop/admin/Order/order/healthpay_detail.php?pay_batchcode={$data['pay_batchcode']}&batchcode={$data['batchcode']}\">". $callBackBatchcode ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '易宝支付'){
            foreach ($res27 as $k => $v)
            {
                if($data['pay_batchcode'] == $v['pay_batchcode'])
                {
                    $callBackBatchcode = $v['callBackBatchcode'];
                    $settlementprice   = $v['price'];
                }
            }
            $url = "[<a href=\"/mshop/admin/Order/order/yeepay_detail.php?pay_batchcode={$data['pay_batchcode']}&batchcode={$data['pay_batchcode']}\">". $callBackBatchcode ."(点击查看)</a>]";
        }elseif ($data['paystyle'] == '京东支付'){
            $paySql = "select callBackBatchcode from paycallback_t where isvalid=true and pay_batchcode='".$data['pay_batchcode']."' limit 0,1";
            $res = $this->db->getRow($paySql);
            $callBackBatchcode = $res['callBackBatchcode'];
            echo "[<a href=\"/mshop/admin/Order/order/jdpay_detail.php?pay_batchcode={$data['pay_batchcode']}&batchcode={$data['batchcode']}\">". $callBackBatchcode ."(点击查看)</a>]";
        }else{
            foreach ($res27 as $k => $v)
            {
                if($data['pay_batchcode'] == $v['pay_batchcode'])
                {
                    $callBackBatchcode = $v['callBackBatchcode'];
                    $settlementprice   = $v['price'];
                }
            }
            $url = "[<a href=\"/weixinpl/back_newshops/Order/order/pay_detail.php?pay_batchcode={$data['pay_batchcode']}\">". $callBackBatchcode ."(点击查看)</a>]";
        }
        return array('errcode'=>0,'errmsg'=>'success','data'=>$url);
    }

    /*
     * 获取云店特权身份配置
     * $Author: hjw$
     * $2018-04-04  $
     * 传入customer_id
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
    版权信息:  秘密信息
    功能描述：云店奖励——店主订单——平台订单数量及自营订单数量
    开 发 者：taojin
    开发日期： 2018-04-08
    重要说明：参数data 条件数据， type: 0平台订单 1自营订单， where sql条件判断
     */
    public function get_order_num($data,$type){
        $where = '';
        if($data['batchcode'])       $where .= " and o.batchcode = '{$data['batchcode']}'";
        $where .= " and o.yundian_self = '$type'";
        if($data['user_id']){
            //查询云店id
            $sql_yun = "SELECT id from ".WSY_USER.".weixin_yundian_keeper where user_id = '{$data['user_id']}' and isvalid =true and customer_id = '{$data['customer_id']}' ";
            $res_yun = $this->db->getAll($sql_yun);
            $ids = '';
            foreach($res_yun as $k => $v){
                $ids = $v['id'].',';
            }
            $ids1 = trim($ids,',');
            if($ids1){
                $where .= " and o.yundian_id in ({$ids1})";
            }else{
                $return['data']      = array(array(),0);
                $return['errcode']   = 0;
                $return['msg']       = 'success';
                return $return;
            }
        }
        if($data['yun_user_id']){
            //查询云店id
            $sql_yun = "SELECT id from ".WSY_USER.".weixin_yundian_keeper where user_id = '{$data['yun_user_id']}' and isvalid =true and customer_id = '{$data['customer_id']}' ";
            $res_yun = $this->db->getAll($sql_yun);
            $ids = '';
            foreach($res_yun as $k => $v){
                $ids = $v['id'].',';
            }
            $ids3 = trim($ids,',');
            if($ids3){
                $where .= " and o.yundian_id in ({$ids3})";
            }else{
                $return['data']      = array(array(),0);
                $return['errcode']   = 0;
                $return['msg']       = 'success';
                return $return;
            }
        }
        if($data['name']){
        	$data['name'] = mysql_escape_string($data['name']);
            $sql_mem = "select k.id from ".WSY_USER.".weixin_users u inner join ".WSY_USER.".weixin_yundian_keeper k on u.id=k.user_id where k.customer_id = '{$data['customer_id']}' and u.name like '%{$data['name']}%'";
            $res_mem = $this->db->getAll($sql_mem);
            $ids = '';
            foreach($res_mem as $k => $v){
                $ids .= $v['id'].',';
            }
            $ids2 = trim($ids,',');
            if($ids2){
                $where .= " and o.yundian_id in ({$ids2})";
            }else{
                $return['data']      = array(array(),0);
                $return['errcode']   = 0;
                $return['msg']       = 'success';
                return $return;
            }
        }

        switch ($data['status']){
            case 0;//全部
                break;
            case 1;//代发货
                $where .= " and o.sendstatus = 0 and (o.paystatus = 1 and o.paystyle != '货到付款') and o.status = 0 and aftersale_type=0";
                break;
            case 2;//待收货
                $where .= " and o.sendstatus = 1 and (o.paystatus = 1 or o.paystyle = '货到付款') and o.status = 0 and aftersale_type=0";
                break;
            case 3;//待完成
                $where .= " and o.sendstatus = 2 and o.paystatus = 1 and o.status = 0 and aftersale_type=0";
                break;
            case 4;//交易完成
                $where .= ' and ( o.sendstatus = 2 and o.paystatus = 1 and o.status = 1 ) and aftersale_type = 0';
                break;
            case 5;//退款
                if($data['type'] == 0){
                    $where .= " AND o.sendstatus in ('5','6') ";
                }else{
                    $where .= " and o.aftersale_type = 1 and o.paystatus = 1 ";
                }
                break;
            case 6;//退货
                if($data['type'] == 0){
                    $where .= " and ((o.sendstatus in ('5','6') and o.return_type = 0 ) or (o.sendstatus in ('3','4') and o.return_type = 1)) ";
                }else{
                    $where .= " and o.aftersale_type = 2 and o.paystatus = 1 ";
                }
                break;
            case 7;//换货
                if($data['type'] == 0){
                    $where .= "AND o.sendstatus in ('3','4') AND o.return_type=2 ";
                }else{
                    $where .= " and o.aftersale_type = 3 and o.paystatus = 1 ";
                }
        }

        $sql_count = "SELECT count(DISTINCT o.batchcode) as num from weixin_commonshop_orders o  where o.isvalid = true and o.paystatus = 1 {$where} and o.yundian_id>0 and o.customer_id = '{$data['customer_id']}' and o.is_sendorder <> 1 and o.is_collageActivities <> 2 and o.shopactivity_mark = 0 ";
        $res = $this->db->getRow($sql_count);
        return $res['num'];
    }
    /*
     * 批量和单独审核店主
     * $Author: hjw$
     * $2018-04-08  $
     */
    public function review_pass($param){
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
                    $arr_apply  = "select phone,realname,identity_num,apply_time,store_name from ".WSY_REBATE.".weixin_yundian_identity_applylog where id ='".$id."' and isvalid = true";
                    $result_arr = $this->db->getAll($arr_apply)[0]; 
                    $sql_insert = "insert into ".WSY_USER.".weixin_yundian_keeper (customer_id,isvalid,user_id,tequan_id,phone,realname,identity_num,status,apply_time,verify_time,expire_time,profit_shop,self_reware,createtime,store_name) values({$customer_id},true,".$user_id.",".$tequan_id.",".$result_arr['phone'].",'".$result_arr['realname']."','".$result_arr['identity_num']."',1,'".$result_arr['apply_time']."',now(),'".date("Y-m-d H:i:s", $expire_time)."',".$profit_shop.",".$self_reware.",now(),'".$result_arr['store_name']."')";
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
                $content = "恭喜您，您的云店".$res_identity['name']."申请已经审核通过！<a href='".$url."'>赶紧去管理您的云店吧！</a>";
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
                 $query_apply_ids = "select distinct(a.user_id),a.tequan_id,i.reward,u.weixin_fromuser,i.name,a.phone,a.realname,a.identity_num,a.apply_time,a.store_name from ".WSY_REBATE.".weixin_yundian_identity_applylog a inner join ".WSY_REBATE.".weixin_yundian_identity i on a.tequan_id = i.id inner join ".WSY_USER.".weixin_users u on u.id = a.user_id where a.id in(".$id.") and a.isvalid = true and i.isvalid and a.customer_id = '".$customer_id."'"; 
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
                         $sql_insert = "insert into ".WSY_USER.".weixin_yundian_keeper (customer_id,isvalid,user_id,tequan_id,phone,realname,identity_num,status,apply_time,verify_time,expire_time,profit_shop,self_reware,createtime,store_name) values({$customer_id},true,".$v2['user_id'].",".$v2['tequan_id'].",".$v2['phone'].",'".$v2['realname']."','".$v2['identity_num']."',1,'".$v2['apply_time']."',now(),'".date("Y-m-d H:i:s", $expire_time)."',".$v2['reward'].",".$self_reware.",now(),'".$v2['store_name']."')";
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
                    $content = "恭喜您，您的云店".$v2['name']."申请已经审核通过！<a href='".$url."'>赶紧去管理您的云店吧！</a>";
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
    public function reject_review($param){
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
                  $openid = $this->shopmessage->query_openid($res_applylog['customer_id'],$res_applylog['user_id']);
                 $content = "您的云店".$res_applylog['name']."申请已经被驳回了！驳回原因：".(empty($reason)?'不符合资格。':$reason)."";
                 $query = "INSERT INTO send_weixinmsg_log (
                                customer_id, createtime, type, content, openid
                            ) VALUES (
                                {$res_applylog['customer_id']}, now(), 0, '".$content."', '{$openid['openid']}'
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

     /*
    版权信息:  秘密信息
    功能描述：云店奖励——店头背景查询
    开 发 者：zjj-v397
    开发日期： 2018-04-10
    重要说明：
     */
    public function select_setting_of_store($id){
        $upfileUrl = '/resources/yundian/image/cloud_shop_bg1.jpg|/resources/yundian/image/cloud_shop_bg2.jpg|/resources/yundian/image/cloud_shop_bg3.jpg|/resources/yundian/image/cloud_shop_bg4.jpg|/resources/yundian/image/cloud_shop_bg5.jpg';
        $sql = "SELECT id FROM ".WSY_REBATE.".weixin_yundian_setting where customer_id = ".$id."";
        $yun_id = $this->db->getOne($sql);
        if ($yun_id == '') {
            $sql = "INSERT INTO ".WSY_REBATE.".weixin_yundian_setting (customer_id, isvalid, yundian_bg) VALUES (".$id.",1,".$upfileUrl.")";
            $result = $this->db->query($sql);
        }else{
            $sql = "SELECT yundian_bg FROM ".WSY_REBATE.".weixin_yundian_setting where customer_id = ".$id."";
            $upfile = $this->db->getOne($sql);
            if ($upfile == '') {
                $sql = "UPDATE ".WSY_REBATE.".weixin_yundian_setting SET yundian_bg='".$upfileUrl."' where customer_id = ".$id."";
                $res = $this->db->query($sql);
                $upfileUrl = explode('|', $upfileUrl);
                return $upfileUrl;
            }else{
                $upfileUrl = explode('|', $upfile);
                return $upfileUrl;
            }
        }
        $upfileUrl = explode('|', $upfileUrl);
        return $upfileUrl;

       
    } 

     /*
    版权信息:  秘密信息
    功能描述：云店奖励——店头背景
    开 发 者：zjj-v397
    开发日期： 2018-04-10
    重要说明：
     */
    public function setting_of_store($data){
        $upfileUrl = $data['pathArray'];
        $upfileUrl = implode('|', $upfileUrl);

        $sql = "UPDATE ".WSY_REBATE.".weixin_yundian_setting SET yundian_bg='".$upfileUrl."' where customer_id = '{$data['customer_id']}'";
        $res = $this->db->query($sql);
        if($res){
            $result = array('errcode' => 1 ,'errmsg' => '修改成功');
        }else{
            $result = array('errcode' => 400 ,'errmsg' => '修改失败');
        }
        return $result;
    }

     /*
    版权信息:  秘密信息
    功能描述：云店奖励——店头背景
    开 发 者：zjj-v397
    开发日期： 2018-04-10
    重要说明：
     */
    public function description_select($id){
        $sql = "select name,now_price,description,storenum,default_imgurl from ".WSY_PROD.".weixin_commonshop_products where id='".$id."'";
        $result = $this->db->getRow($sql);
        return $result;
    }

     /*
    版权信息:  秘密信息
    功能描述：云店奖励——奖励模式比例查询
    开 发 者：zqs
    开发日期： 2018-04-11
    重要说明：
     */
     public function reward_selcet($customer_id){
        $reward_data = array();
        //查询奖励开关是否开启 is_team区域奖励开关 is_shareholder店铺奖励开关
        $onoff_sql = "select is_team,is_shareholder from weixin_commonshops where isvalid=true and customer_id=".$customer_id."";
        $onoff     = $this->db->getRow($onoff_sql);
        //查询区域奖励比例
        $team_sql    = "select team_all from ".WSY_SHOP.".weixin_commonshop_team where isvalid = true and customer_id = '".$customer_id."'";
        $team        = $this->db->getOne($team_sql);

        //查询股东分红奖励比例
        $shareholder_sql = "select shareholder_all from ".WSY_REBATE.".weixin_commonshop_shareholder where isvalid = true and customer_id = '".$customer_id."'";
        $shareholder     = $this->db->getOne($shareholder_sql);

        //查询绩效奖励
        $globalbonus_sql = "select isOpenGlobal,Global_all from ".WSY_REBATE.".weixin_globalbonus where isvalid=true and customer_id='".$customer_id."'";
        $globalbonus_res = $this->db->getRow($globalbonus_sql);
        $globalbonus     = $globalbonus_res['Global_all'];
        $isOpenGlobal    = $globalbonus_res['isOpenGlobal'];

        //查询招商奖励比例
        $investmen_sql   = "select proportion,isvalid from ".WSY_REBATE.".weixin_attract_investment where category=1 and isvalid = true and customer_id='".$customer_id."'";
        $investmen       = $this->db->getRow($investmen_sql);
        //查询区块链奖励比例
        $block_chain_sql = "SELECT on_off,proportion FROM ".WSY_REBATE.".weixin_block_chain_reward_setting WHERE customer_id = '{$customer_id}' LIMIT 1 ";
        $block_chain_reward = $this->db->getRow($block_chain_sql);
        //将数据封装成数组
        if($onoff['is_team'] == 0){
            $reward_data['team']        = 0;
        }else{
            $reward_data['team']        = $team;
        }
        if ($onoff['is_shareholder'] == 0) {
            $reward_data['shareholder'] = 0;
        }else{
            $reward_data['shareholder'] = $shareholder;
        }
        if ($isOpenGlobal==0) {
            $reward_data['globalbonus'] = 0;
        }else{
            $reward_data['globalbonus'] = $globalbonus;
        }
        if($investmen['isvalid'] == ""){
            $reward_data['investmen']   = 0;  
        }else{
            $reward_data['investmen']   = $investmen['proportion'];  
        }
        if($block_chain_reward['on_off'] == 0)
        {
            $reward_data['block_chain_reward'] = 0;
        }else
        {
            $reward_data['block_chain_reward'] = $block_chain_reward['proportion'];
        }
        $reward_data['is_team'] = $onoff['is_team'];
        $reward_data['is_shareholder'] = $onoff['is_shareholder'];
        $reward_data['isOpenGlobal'] = $isOpenGlobal;
        $reward_data['block_chain_on_off'] = $block_chain_reward['on_off'];
        $reward_data['isvalid'] = $investmen['isvalid']?1:0;
        return $reward_data;
     }

          /*
    版权信息:  秘密信息
    功能描述：云店奖励——查询是否有云店店主身份
    开 发 者：zqs
    开发日期： 2018-04-11
    重要说明：
     */
    public function keeper_select($customer_id){
        $sql = "select id from ".WSY_USER.".weixin_yundian_keeper where customer_id='".$customer_id."' and isvalid=true and status=1";
        $res = $this->db->getOne($sql);
        if ($res == "") {
            $res = -1;
        }
        return $res;
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——平台上下架删除产品日志记录
    开 发 者：zqs
    开发日期： 2018-04-09
    重要说明：
     */
    public function set_product_log($data){
        $data['operation']  = $_SESSION['curr_login'];
        $data['createtime']  = date("Y-m-d H:i:s",time());
        $res = $this->db->autoExecute(WSY_PROD.'.weixin_commonshop_product_log', $data, 'insert');
        return $res;
    }

    /*  云店用户退款，换货，退货接口--获取订单信息
     *  $Author:HMJ-V384
     *  2018-4-09
     *  return:
     **/
    public function get_yundian_order_msg($batchcode,$customer_id) {
        $select_sql = "SELECT paystyle,yundian_id,is_open_aftersale,yundian_self,pay_batchcode,paystatus,sendstatus,aftersale_type,return_account,user_id,aftersale_state,totalprice 
        FROM weixin_commonshop_orders WHERE batchcode='{$batchcode}' AND customer_id='{$customer_id}' and isvalid = '1' ";
        $result    = $this->db->getRow($select_sql);    
        return $result; 
    }

    /*  云店用户退款，换货，退货----更新订单状态，单订单表
     *  $Author:HMJ-V384
     *  2018-4-10
     *  status:fin
     **/
    public function set_yundian_return_status_confirm($batchcode,$customer_id,$sql_data_order) {
        $result = $this->db->autoExecute('weixin_commonshop_orders', $sql_data_order, 'update', "batchcode='{$batchcode}' AND isvalid=true AND customer_id='{$customer_id}'");
        return $result;
    }

    /*  云店用户退款，换货，退货----店主同意退款，零钱或第三方接口退款
     *  $Author:HMJ-V384
     *  2018-4-12
     *  status:todo
     **/
    public function yundian_money_return($user_id,$customer_id,$batchcode){
        $order_msg = $this->get_yundian_order_msg($batchcode,$customer_id);
        if(!$order_msg) {
            return false;
        } else {
            if($order_msg['paystyle'] == '微信支付'){
                $sql = "SELECT partnerid FROM ".WSY_PAY.".pay_config WHERE customer_id='{$customer_id}' AND pay_type='weipay' AND isvalid=true";
                $sjj = $this->db->getRow($sql);
                $partnerid = $sjj['partnerid'];
        
                if( $partnerid != '' ){
                    //发送的数据
                    $post_data  = array(
                        'batchcode' => $batchcode,
                        'transaction_id' => $partnerid,
                        'total_fee' => $order_msg['totalprice'],
                        'refund_fee' => $order_msg['return_account']
                    );

                    $post_data = http_build_query($post_data);
                    $url = Protocol.$_SERVER["HTTP_HOST"].'/weixinpl/common_shop/jiushop/refund_yundian.php?customer_id='.$customer_id;     //调用拼团微信退款

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);                    // 要访问的地址
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, 1 );
                    curl_setopt($ch, CURLOPT_HEADER, 0);                    // 显示返回的Header区域内容
                    curl_setopt($ch, CURLOPT_NOBODY, 0);                    //只取body头
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 对认证证书来源的检查
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);        // 从证书中检查SSL加密算法是否存在
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)'); // 模拟用户使用的浏览器
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);            // 使用自动跳转
                    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);               // 自动设置Referer
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);       // Post提交的数据包
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);                  // 设置超时限制防止死循环
                    $curl_error = curl_error($ch);
                    $json = curl_exec($ch);

                    curl_close($ch);

                    $jsons = json_decode($json,true);
                    return $jsons;
                    //微信退款失败
                    if( $jsons['return_code'] == 'FAIL' || $jsons['result_code'] == 'FAIL' ){
                        $r['errcode'] = 406;
                        $r['errmsg'] = $jsons['err_code_des'] ? $jsons['err_code_des'] : $jsons['return_msg'];
                        return $r;
                    } else {
                        $sendMessage_content = "亲，您的微信零钱 +".$order_msg['return_account']."元\r\n".
                                                "来源：【云店商品退款】\n".
                                                "状态：【退款到帐】\n".
                                                "时间：".date( "Y-m-d H:i:s")."";
                        $r['errmsg'] = '微信退款成功';
                        $r['errcode'] = '0';
                        return $r;                                                    
                    }
                }

            }else if(!empty($order_msg['paystyle'])){
                $user_id = $order_msg['user_id'];
                $money = $order_msg['return_account'];

                $refund_pay['aftersale_state'] = 4;
                $result = $this->set_yundian_return_status_confirm($batchcode,$customer_id,$refund_pay);
                if(!$result){
                    $r['errmsg'] = '系统零钱退款失败,请联系客服';
                    $r['errcode'] = '401';
                    return $r;
                }else{
                    $ret = $this->editBalance($customer_id, $user_id, $money , $batchcode);
                    if(!$ret) {
                        $r['errmsg'] = '零钱退款失败';
                        $r['errcode'] = '400';
                        return $r;                      
                    }
                        $r['errmsg'] = '零钱退款成功';
                        $r['errcode'] = '0';
                        return $r;
                }
            }else{
                $r['errmsg'] = '未知错误';
                $r['errcode'] = '400';
                return $r;
            }
        }
    }   

    # 查询用户零钱余额 start #
    protected function getBalance($customerId, $userId) {
        $q = "SELECT balance FROM ".WSY_USER.".moneybag_t WHERE customer_id = '{$customerId}' AND isvalid = 1 AND user_id = '{$userId}'";
        $r = $this->db->getRow ($q);
        return $r["balance"] ?: 0;
    }
    # 查询用户零钱余额 end #

    # 修改用户零钱的余额 start #
    protected function editBalance($customerId, $userId, $expense , $pay_batchcode) {
        if (empty($customerId) || empty($userId) || empty($expense)) {
            $r['errmsg'] = '获取商家ID、用户ID或订单金额失败！';
            $r['errcode'] = '400';
            return $r;
        }
        $balance = $this->getBalance($customerId, $userId) + $expense;
        $q = "UPDATE ".WSY_USER.".moneybag_t SET balance = '{$balance}' WHERE customer_id = '{$customerId}' AND isvalid = 1 AND user_id = '{$userId}'";
        $r = $this->db->query($q);
        if ($r) {
            $parameters = [
                "customerId" => $customerId,
                "userId" => $userId,
                "expense" => $expense,
                "orderNumber" => $pay_batchcode//订单号
            ];
            $this->addChangeLog($parameters);
            return true;
        } else {
            return false;
        }
    }
    # 修改用户零钱的数值 end #
    # 更新零钱日志 start #
    protected function addChangeLog($parameters) {

        $customerId = $parameters["customerId"];
        $userId = $parameters["userId"];
        $expense = $parameters["expense"];
        $orderNumber = $parameters["orderNumber"];
        $dateTime = date("Y-m-d H:i:s");
        $q = "SELECT after_money AS balance FROM ".WSY_USER.".moneybag_log WHERE isvalid = 1 AND customer_id = {$customerId} AND user_id = {$userId} ORDER BY createtime DESC LIMIT 1";
        $r = $this->db->getRow ($q);
        $originalBalance = 0;
        if ($r) {
            $originalBalance =  $r["balance"];
        } else {
            $r['errmsg'] = '查询零钱日志失败';
            $r['errcode'] = '400';
            return $r;
        }
        $presentBalance = $originalBalance + $expense;
        $q = "INSERT INTO ".WSY_USER.".moneybag_log (isvalid, customer_id, user_id, before_money, money, after_money, type, batchcode, pay_style, remark, createtime, artificial) VALUES (1, {$customerId}, {$userId}, {$originalBalance}, {$expense}, {$presentBalance}, 1, \"{$orderNumber}\", 0, \"云店商品退款：【{$expense}】元\", \"{$dateTime}\", 0)";
        $r = $this->db->query($q);
        if ($r) {

        } else {
            $r['errmsg'] = '零钱日志更新失败';
            $r['errcode'] = '400';
            return $r;
        }
    }
    # 更新零钱日志 end #    

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主提成收入明细查询
    开 发 者：HMJ-V384
    开发日期： 2018-04-18
    重要说明：无
     */
    function yundian_shopkeeper_reward_detail($param){
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum  = $param['pageNum'] ? : 1; //当前页,1开始
        $start    = ($pageNum-1)*$pageSize;
        $end      = $pageSize;
        //分页设置 end
        $shopkeeper_arr = array();
        $user_id        = -1;
        $start_time     = -1;
        $end_time       = -1;
        $from_id        = -1;
        $pay_style      = 0;
        $customer_id    = $param['customer_id'];

        if($param['start_time']){
            $start_time = $param['start_time'];
        }
        if($param['end_time']){
            $end_time = $param['end_time'];
        }
        if($param['from_id']){
            $from_id = (int)$param['from_id'];
        }
        if($param['name']){
            $name = mysql_escape_string($param['name']);
        }
        if($param['batchcode']){
            $batchcode = $param['batchcode'];
        }        
        if($param['user_id']){
            $user_id = (int)$param['user_id'];
        } else {
            $user_id = '';
        }
        if($param['pay_style']){
            $pay_style = (int)$param['pay_style'];
        }        

        $sql = "SELECT m.id,m.money,m.createtime,m.from_id,m.remark,m.batchcode,c.rcount,c.totalprice,c.sendstatus,u.phone,u.name";
        
        if($pay_style){
        	$sql .= ",p.reward ";
        } else {
        	$sql .= ",op.reward_money ";
        }
        
        $sql .= " FROM ".WSY_USER.".`moneybag_log` m
         LEFT JOIN weixin_commonshop_orders c ON c.batchcode = m.batchcode AND c.isvalid='1' 
         LEFT JOIN ".WSY_USER.".`weixin_users` u ON u.id = m.user_id AND u.isvalid='1' ";

		if($pay_style){
			$sql .= "LEFT JOIN ".WSY_SHOP.".`weixin_commonshop_order_promoters` p ON p.batchcode = c.batchcode AND p.isvalid='1' AND p.class='91' AND p.type='27' ";
		} else {
			$sql .= "LEFT JOIN weixin_commonshop_order_prices op ON op.batchcode = c.batchcode AND op.isvalid='1' ";
		}

        $sql .= "WHERE m.customer_id='".$customer_id."' AND m.isvalid='1' AND m.user_id='".$user_id ."'";
        /************** 搜索条件 start ******************/
        if($start_time!=-1){
            $sql .= " and m.createtime >= '".$start_time."'";
        }
        if($end_time!=-1){
            $sql .= " and m.createtime <= '".$end_time."'";
        }
        if($from_id!=-1){
            $sql .= " and m.from_id like '%".$from_id."%' ";
        }
        if($name!=-1){
            $sql .= " and u.name like '%".$name."%' ";
        }
        if($batchcode!=-1){
            $sql .= " and m.batchcode like '%".$batchcode."%' ";
        }                

        if( $param['nowtime'] ){
            $sql .= " and m.end_time >= '{$param['nowtime']}' ";
            $sql .= " and m.start_time <= '{$param['nowtime']}' ";
        }
        if($pay_style){ //区分是提成还是自营产品收入 0 提成 1 自营 默认提成0
            //$sql .= " and pay_style = '39' ";
            $sql .= " and m.commission_type = 26 ";
        } else {
            $sql .= " and m.commission_type = 25 ";
        }
        /************** 搜索条件 end ******************/
        if( $param['pageNum'] > 0 ){
            $sql .= " order by m.id desc limit ".$start.",".$end;
        }

        $shopkeeper_reward_detail_arr = $this->db->getAll($sql);
        $shopkeeper_count = count($shopkeeper_reward_detail_arr);//总共多少条记录
        $pageCount = ceil($shopkeeper_count/$pageSize);//总页数


        // $return['sql'] = $sql;
        $return['pageCount'] = $pageCount;
        $return['shopkeeper_reward_detail_arr'] = $shopkeeper_reward_detail_arr;
        return $return;
    }

    /*
    * 后台操作日志
    * author：cjj
    */
    function yundian_setting_log($param = array()){
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum  = $param['pageNum'] ? : 1; //当前页,1开始
        $start    = ($pageNum-1)*$pageSize;
        $end      = $pageSize;

        //分页设置 end
        $word           = -1;
        $start_time     = -1;
        $end_time    = -1;
        $customer_id    = $param['customer_id'];

        if($param['word']){
            $word        = mysql_escape_string($param['word']);
        }
        if($param['start_time']){
            $start_time  = $param['start_time'];
        }
        if($param['end_time']){
            $end_time = $param['end_time'];
        }

        $sql = " select id,operationuser,title,remark,createtime  from ".WSY_REBATE.".weixin_yundian_setting_log where customer_id=".$customer_id."  ";
        /************** 搜索条件 start ******************/
        if($word!=-1){
            $sql .= " AND remark like '%".$word."%'";
        }
        if($start_time!=-1){
            $sql .= " AND createtime >= '".$start_time."'";
        }
        if($end_time!=-1){
            $sql .= " AND createtime <= '".$end_time."'";
        }
        /************** 搜索条件 end ******************/
        $res_total  = $this->db->getAll($sql);
        $res_count  = count($res_total);//总共多少条记录

        if( $param['pageNum'] > 0 ){
            $sql .= " order by id desc limit ".$start.",".$end;
        }

        $res        = $this->db->getAll($sql);
        $pageCount  = ceil($res_count/$pageSize);//总页数
        $return['pageCount'] = $pageCount;
        $return['res'] = $res;
        return $return;
    }

    /*
    * 云店配置比较，方便插入操作日志
    * $Author: cjj
    * $2018-04-08  $
    * $data=[];  //旧云店配置数据     $result =[];
    */
    function compare_yundian_setting($data,$result,$customer_id){
        $remark_return = '修改配置：';
        $remark        = '';
        $remark_title  = '';

        $query = "select customer_id,yundian_onoff,yundian_apply_onoff,yundian_choucheng,receipt_onoff,receipt_time,invalid_onoff,invalid_time,clearing_onoff,playmoney_onoff,complete_onoff,shop_valid_time,shop_notice_time,yundian_reward,yundian_bg from ".WSY_REBATE.".weixin_yundian_setting where customer_id=" . $customer_id . " ";
        $res_basic_old     = $this->db->getRow($query);     //获取操作后的配置参数

        if($data['yundian_onoff'] != $res_basic_old['yundian_onoff'] &&  $data['yundian_onoff'] == 1){
            $remark .= '打开云店开关，';
            $remark_title .= '修改云店开关，';
        }else if($data['yundian_onoff'] != $res_basic_old['yundian_onoff'] &&  $data['yundian_onoff'] == 0){
            $remark .= '关闭云店开关，';
            $remark_title .= '修改云店开关，';
        }

        if($data['yundian_apply_onoff'] != $res_basic_old['yundian_apply_onoff'] &&  $data['yundian_apply_onoff'] == 1){
            $remark .= '打开云店申请开关，';
            $remark_title .= '修改云店申请开关，';
        }else if($data['yundian_apply_onoff'] != $res_basic_old['yundian_apply_onoff'] &&  $data['yundian_apply_onoff'] == 0){
            $remark .= '关闭云店申请开关，';
            $remark_title .= '修改云店申请开关，';
        }

        if($data['yundian_choucheng'] != $res_basic_old['yundian_choucheng']){
            $remark .= '修改自营产品总抽成为'.$data['yundian_choucheng'].'，';
        }

        if($data['receipt_onoff'] != $res_basic_old['receipt_onoff'] && $data['receipt_onoff'] == 1){
            $remark .= '打开默认收货时间开关，';
            $remark_title .= '修改默认收货时间开关，';
        }else if($data['receipt_onoff'] != $res_basic_old['receipt_onoff'] &&  $data['receipt_onoff'] == 0){
            $remark .= '关闭默认收货时间开关，';
            $remark_title .= '修改默认收货时间开关，';
        }

        if($data['receipt_time'] != $res_basic_old['receipt_time']){
            $remark .= '修改默认收货时间为'.$data['receipt_time'].'，';
        }

        if($data['invalid_onoff'] != $res_basic_old['invalid_onoff'] && $data['invalid_onoff'] == 1){
            $remark .= '打开订单失效开关，';
            $remark_title .= '修改订单失效开关，';
        }else if($data['invalid_onoff'] != $res_basic_old['invalid_onoff'] &&  $data['invalid_onoff'] == 0){
            $remark .= '关闭订单失效开关，';
            $remark_title .= '修改订单失效开关，';
        }

        if($data['invalid_time'] != $res_basic_old['invalid_time']){
            $remark .= '修改订单失效时间为'.$data['invalid_time'].'，';
        }

        if($data['clearing_onoff'] != $res_basic_old['clearing_onoff'] && $data['clearing_onoff'] == 1){
            $remark .= '打开自营产品订单收货自动结算开关，';
            $remark_title .= '修改收货自动结算开关，';
        }else if($data['clearing_onoff'] != $res_basic_old['clearing_onoff'] &&  $data['clearing_onoff'] == 0){
            $remark .= '关闭自营产品订单收货自动结算开关，';
            $remark_title .= '修改收货自动结算开关，';
        }

        if($data['playmoney_onoff'] != $res_basic_old['playmoney_onoff'] && $data['playmoney_onoff'] == 1){
            $remark .= '打开售后平台打款开关，';
            $remark_title .= '修改售后平台打款开关，';
        }else if($data['playmoney_onoff'] != $res_basic_old['playmoney_onoff'] &&  $data['playmoney_onoff'] == 0){
            $remark .= '关闭售后平台打款开关，';
            $remark_title .= '修改售后平台打款开关，';
        }

        if($data['complete_onoff'] != $res_basic_old['complete_onoff'] && $data['complete_onoff'] == 1){
            $remark .= '打开退款之后自动完成订单开关，';
            $remark_title .= '修改自动完成订单开关，';
        }else if($data['complete_onoff'] != $res_basic_old['complete_onoff'] &&  $data['complete_onoff'] == 0){
            $remark .= '关闭退款之后自动完成订单开关，';
            $remark_title .= '修改自动完成订单开关，';
        }

        if($data['shop_valid_time'] != $res_basic_old['shop_valid_time']){
            $remark .= '修改默认店主有效天数为'.$data['shop_valid_time'].'，';
            $remark_title .= '修改默认店主有效天数，';
        }

        if($data['shop_notice_time'] != $res_basic_old['shop_notice_time']){
            $remark .= '修改提前通知天数为'.$data['shop_notice_time'].'，';
            $remark_title .= '修改提前通知天数，';
        }

        if($data['yundian_reward'] != $res_basic_old['yundian_reward']){
            $remark .= '修改云店奖励比例为'.$data['yundian_reward'].'，';
            $remark_title .= '修改云店奖励比例，';
        }

        $remark_keeper = '';
        foreach ($result as $k => $v)
        {
            $data2['id']           = $result[$k]['id'];
            $data2['is_identity']  = $result[$k]['is_identity'];
            $data2['name']         = $result[$k]['name'];
            $data2['reward']       = $result[$k]['reward'];
            $data2['apply_money']  = $result[$k]['apply_money'];
            $data2['createtime']   = date("Y-m-d H:i:s",time());

            $arr[$k] = $result[$k]['tequan'];
            $a   = $arr[$k][1]?$arr[$k][1]:0;
            $b   = $arr[$k][2]?$arr[$k][2]:0;
            $c   = $arr[$k][3]?$arr[$k][3]:0;
            $d   = $arr[$k][4]?$arr[$k][4]:0;
            $e   = $arr[$k][5]?$arr[$k][5]:0;

            $query_keeper = "select is_identity,name,reward,apply_money,tequan from ".WSY_REBATE.".weixin_yundian_identity where customer_id=" . $customer_id . " and id=".$data2['id']." and isvalid=1 ";
            $res_keeper_old[$k]     = $this->db->getRow($query_keeper);     //获取操作后的配置参数

            if($res_keeper_old[$k]['is_identity'] != $data2['is_identity'] && $data2['is_identity'] == 1){
                $remark_keeper .= '打开'.$res_keeper_old[$k]['name'].'的身份开关，';
                $remark_title .= '修改'.$res_keeper_old[$k]['name'].'的身份开关，';
            }else if($res_keeper_old[$k]['is_identity'] != $data2['is_identity'] && $data2['is_identity'] == 0){
                $remark_keeper .= '关闭'.$res_keeper_old[$k]['name'].'的身份开关，';
                $remark_title .= '修改'.$res_keeper_old[$k]['name'].'的身份开关，';
            }

            if($res_keeper_old[$k]['name'] != $data2['name']){
                $remark_keeper .= '修改‘'.$res_keeper_old[$k]['name'].'’名字为‘'.$data2['name'].'’，';
            }

            if($res_keeper_old[$k]['reward'] != floatval($data2['reward'])){
                $remark_keeper .= '修改‘'.$data2['name'].'’的比例为'.$data2['reward'].'，';
            }

            if($res_keeper_old[$k]['apply_money'] != $data2['apply_money']){
                $remark_keeper .= '修改‘'.$data2['name'].'’的申请金额为'.$data2['apply_money'].'，';
            }

            $res_keeprt_tequan_arr[$k] = explode('_',$res_keeper_old[$k]['tequan']);

            if($res_keeprt_tequan_arr[$k][0] != $a && $a == 1){
                $remark_keeper .= '打开'.$data2['name'].'的店铺推广开关，';
            }else if($res_keeprt_tequan_arr[$k][0] != $a && $a == 0){
                $remark_keeper .= '关闭'.$data2['name'].'的店铺推广开关，';
            }

            if($res_keeprt_tequan_arr[$k][1] != $b && $b == 1){
                $remark_keeper .= '打开'.$data2['name'].'的个性化店标开关，';
            }else if($res_keeprt_tequan_arr[$k][1] != $b && $b == 0){
                $remark_keeper .= '关闭'.$data2['name'].'的个性化店标开关，';
            }

            if($res_keeprt_tequan_arr[$k][2] != $c && $c == 1){
                $remark_keeper .= '打开'.$data2['name'].'的收益实时查询开关，';
            }else if($res_keeprt_tequan_arr[$k][2] != $c && $c == 0){
                $remark_keeper .= '关闭'.$data2['name'].'的收益实时查询开关，';
            }

            if($res_keeprt_tequan_arr[$k][3] != $d && $d == 1){
                $remark_keeper .= '打开'.$data2['name'].'的店铺自营订单管理开关，';
            }else if($res_keeprt_tequan_arr[$k][3] != $d && $d == 0){
                $remark_keeper .= '关闭'.$data2['name'].'的店铺自营订单管理开关，';
            }

            if($res_keeprt_tequan_arr[$k][4] != $e && $e == 1){
                $remark_keeper .= '打开'.$data2['name'].'的店铺自营订单管理开关，';
            }else if($res_keeprt_tequan_arr[$k][4] != $e && $e == 0){
                $remark_keeper .= '关闭'.$data2['name'].'的店铺自营订单管理开关，';
            }
        }

        $remark .= $remark_keeper;

        if(!empty($remark)){
            $remark_return .= $remark;
        }else{
            $remark_return .= '无';
        }

        if(empty($remark_title)){
            $remark_title = '修改配置';
        }

        $res_remark['remark'] = trim($remark_return,'，');
        $res_remark['title'] = trim($remark_title,'，');

        return $res_remark;
    }
    /*
     * 权限设置
     * author：hjw
     */
    function auth_edit($data){
        $customer_id  = $data['customer_id'];
        $id           = $data['id'];
        $type         = $data['type'];
        if ($type == "edit") {
            $tequan = $data['tequan'];
            $sql = "update ".WSY_REBATE.".weixin_yundian_identity set tequan='".$tequan."' where id='".$id."'";
            $res = $this->db->query($sql);
        }else{
            $sql = "select tequan from ".WSY_REBATE.".weixin_yundian_identity where id='".$id."'";
            $res = $this->db->getOne($sql);

            $res = explode('_',$res);
        }
        return $res;
    }

}//类结束
