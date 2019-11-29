<?php

class model_queue{
    var $db;

    function __construct($customer_id)
    {
        $this->customer_id = $customer_id;
        $this->db = DB::getInstance();
    }

    /**
     * [get_queue_activities 获取换购活动列表]
     * @param  [array] $param [搜索条件]
     * @return [array] $activity_arr    [活动列表数组形式]
     */
    public function get_queue_activities($param){
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
        $isout = -1;
        
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
        if($param['createtime_start']){
            $createtime_start = $param['createtime_start'];
        }
        if($param['createtime_end']){
            $createtime_end = $param['createtime_end'];
        }
        if($param['isout']){
            $isout = $param['isout'];
        }
        
        $sql = "SELECT id,name,createtime,start_time,end_time,isout FROM ".WSY_MARK.".weixin_commonshop_queue WHERE customer_id='".$this->customer_id."' and isvalid=1";
        /************** 搜索条件 start ******************/
        if($activity_id!=-1){
            $sql .= " and id=".$activity_id;
        }
        if($activity_name!=""){
            $sql .= " and name like '%".$activity_name."%'";
        }
        if($starttime!=-1){
            $sql .= " and start_time >= '".$starttime."'";
        }
        if($endtime!=-1){
            $sql .= " and end_time <= '".$endtime."'";
        }
        if($createtime_start!=-1){
            $sql .= " and createtime >= '".$createtime_start."'";
        }
        if($createtime_end!=-1){
            $sql .= " and createtime <= '".$createtime_end."'";
        }
        if($isout!=-1){
            if($isout == 3) {
                $sql .= " and isout in (0) ";
            } else {
                $sql .= " and isout in ({$isout}) ";
            }
        }
        /************** 搜索条件 end ******************/
        $arr = $this->db->getAll($sql);
        $activity_count = count($arr);//总共多少条记录
        $pageCount = ceil($activity_count/$pageSize);//总页数

        if( $param['pageNum'] > 0 ){
            $sql .= " order by id desc limit ".$start.",".$end;
        }

        if( $param['order'] ){
            $sql .= " order by ".$param['order'];
        }

        $activity_arr = $this->db->getAll($sql);

        foreach ($activity_arr as $k => $v) {
            $sql = "select count(id) as p from ".WSY_MARK.".weixin_commonshop_queue_product where queue_id=".$v['id']." and isvalid =true";

            $arr = $this->db->getRow($sql);//总共多少条记录

            $sql = "select count(id) as o from ".WSY_MARK.".weixin_commonshop_queue_order where queue_id=".$v['id']." and isvalid =true";

            $res = $this->db->getRow($sql);

            $sql = "select bonus from ".WSY_MARK.".weixin_commonshop_queue_order where queue_id=".$v['id']." AND status = 4";

            $ret = $this->db->getAll($sql);
            $bonus = 0;
            foreach ($ret as $key => $val) {
                $bonus = $bonus + $val['bonus'];
            }

            $activity_arr[$k]['count']  = $arr['p']?$arr['p']:0;
            $activity_arr[$k]['people'] = $res['o']?$res['o']:0;
            $activity_arr[$k]['bonus']  = $bonus;
        }

        $return['pageCount'] = $pageCount;
        $return['activity_arr'] = $activity_arr;
        return $return;
    }

    /**
     * [查询是添加活动或者是修改活动]
     * @param  [array] $data    [各参数：商家id，活动id]
     * @return [array] $result  [数组]
     */
    public function get_queue_ck($data){
        $sql = "SELECT id,name,start_time,end_time,queue_num,queue_expenditure,success_num,bonus,expenditure,promote_num,isvalid,isout,get_impose,rule,is_rule FROM ".WSY_MARK.".weixin_commonshop_queue WHERE customer_id='".$this->customer_id."' and id= ".$data['id']." and isvalid=1";

        $result = $this->db->getRow($sql);
        return $result;
    }

    /**
     * [添加活动操作]
     * @param  [array] $data    [各参数]    
     * @return [array] $return  [执行结果，为0表示失败，1成功]
     */
    public function queue_add($data){
        $return['errcode'] = 0;
        $return['errmsg']  = "保存失败！";

        $res = $this->db->autoExecute(WSY_MARK.".weixin_commonshop_queue", $data, 'insert') ;
        if ($res) {
            $return['id'] = $this->db->insert_id();
            $return['errcode'] = 1;
            $return['errmsg']  = "保存成功！";
            
            //添加日志 start
            $data['operation']  = 0;
            $data['type']       = 1;
            $data['remark']     = "添加活动";
            $data['createtime'] = date('Y-m-d H:i:s',time());
            $this->add_queue_logs($data);
            //添加日志 end
        }
        return $return;
    }

    /**
     * [修改活动操作]
     * @param  [array] $data    [各参数]    
     * @return [array] $return  [执行结果，为0表示失败，1成功]
     */
    public function queue_save($data,$id) {
        $return['errcode'] = 0;
        $return['errmsg'] = "保存失败！";

        $arr = $this->get_queue_ck(['id'=>$id]);

        $where = "isvalid = true and id=".$id." and customer_id = ".$this->customer_id;
        
        $res = $this->db->autoExecute(WSY_MARK.".weixin_commonshop_queue", $data, 'update',$where) ;
        
        if ($res) {
            $return['errcode'] = 1;
            $return['errmsg'] = "保存成功！";
        }

        //添加日志 start
        $str = "编辑：编码为".$id."的";
        $data['operation']  = 4;
        $data['type']       = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
        foreach ($data as $k => $v) {
            if ($v != $arr[$k]) {
                switch ($k) {
                    case 'name':
                        $data['remark']     = $str."活动名称：".$arr[$k]." 改为 ".$v;
                        break;
                    case 'start_time':
                        $data['remark']     = $str."活动开始时间：".$arr[$k]." 改为 ".$v;
                        break;
                    case 'end_time':
                        $data['remark']     = $str."活动结束名称：".$arr[$k]." 改为 ".$v;
                        break;
                    case 'queue_num':
                        $data['remark']     = $str."每个用户每天排队限制：".$arr[$k]." 改为 ".$v;
                        break;
                    case 'queue_expenditure':
                        $data['remark']     = $str."参与排队的个人消费金额限制：".$arr[$k]." 改为 ".$v;
                        break;
                    case 'bonus':
                        $data['remark']     = $str."排队人数达到 ".$arr[$k]." 改为 ".$v;
                        break;
                    case 'expenditure':
                        $data['remark']     = $str."参与活动后总个人消费 ".$arr[$k]." 改为 ".$v;
                        break;
                    case 'promote_num':
                        $data['remark']     = $str."首次分享促使他人付款成功的人数：".$arr[$k]." 改为 ".$v;
                        break;
                    default:
                        $data['remark']     = '';
                        break;
                }
                if($data['remark'] != '') {
                    $this->add_queue_logs($data);
                }
            }
        }
        //添加日志 end
        return $return;
    }

    /**
     * [queue_exec 启用、终止、删除-活动]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    function queue_exec($id,$type){
        
        $return['errcode'] = 0;
        $return['errmsg']  = $str."失败！";

        if ($type == 1) {
            $str   = "启用";
            $op    = ['isout'=>true];

            $sql = "SELECT id FROM ".WSY_MARK.".weixin_commonshop_queue WHERE customer_id='".$this->customer_id."' and isvalid=1 and isout=1";
            $queue = $this->db->getRow($sql);

            $return['errmsg']  = $str."失败,已有启用队列活动";
        } elseif ($type == 2) {
            $str   = "终止";
            $op    = ['isout'=>2];

            $sql = "SELECT id,status from ".WSY_MARK.".weixin_commonshop_queue_order where isvalid=true and status not in(4,5,6) and customer_id='".$this->customer_id."' and queue_id=".$id;
            
            $order = $this->db->getAll($sql);

            foreach ($order as $key => $value) {
                if ($value['status'] == 0 || $value['status'] == 1) {
                    $remark = "id为".$value['id']."的队列活动订单排队失败";
                    $status = 5;
                } else {
                    $remark = "id为".$value['id']."的队列活动订单领取失败";
                    $status = 6;
                }
                
                $s     = ['status'=>$status];
                $where = "queue_id='".$id."' and status=".$value['status'];
                
                $this->db->autoExecute(WSY_MARK.".weixin_commonshop_queue_order",$s,'update',$where);

                $map['type']         = 1;
                $map['after_status'] = $status;
                $map['remark']       = $remark;
                $map['createtime']   = date('Y-m-d H:i:s',time());

                $this->db->autoExecute(WSY_MARK.".weixin_commonshop_queue_order_log", $map, 'insert');
            }

        } elseif ($type == 3) {
            $str   = "删除";
            $op    = ['isvalid'=>false];
        }

        
        if (!$queue) {
            $where = "isvalid = true and id=".$id." and customer_id = ".$this->customer_id;

            $res = $this->db->autoExecute(WSY_MARK.".weixin_commonshop_queue",$op,'update',$where);
        }
        
        if($res){
            $return['errcode'] = 1;
            $return['errmsg']  = $str."成功！";

            //添加日志 start
            $data['operation']  = $type;
            $data['type']       = 1;
            $data['createtime'] = date('Y-m-d H:i:s',time());    
            $data['remark']     = "编码为".$id."的活动".$str;
            $this->add_queue_logs($data);              
            //添加日志 end 
        }
        return $return;
    }

    /**
     * [获取关联产品列表]
     * @param  [array] $data    [各参数]    
     * @return [array] $return  [数组]
     */
    public function get_queue_shop($param) {
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum = $param['pageNum'] ? : 1; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;        
        //分页设置 end
        $sql = "select count(id) from ".WSY_MARK.".weixin_commonshop_queue_product where queue_id=".$param['id']." and isvalid =true";
        
        $arr = $this->db->getRow($sql);//总共多少条记录

        $pageCount = ceil($arr['count(id)']/$pageSize);//总页数

        $sql = "select id,pid,queue_id,isvalid from ".WSY_MARK.".weixin_commonshop_queue_product where queue_id=".$param['id']." and isvalid = 1 ORDER BY createtime desc,pid";

        $sql .= " limit ".$start.",".$end;
        
        $res = $this->db->getAll($sql);

        foreach ($res as $k=>$v) {
            $sql = "SELECT id,name,orgin_price,now_price,default_imgurl,type_ids,storenum from weixin_commonshop_products where id='{$v['pid']}' and isvalid = 1 ";

            $data = $this->db->getRow($sql);
            
            $sql_currency = "SELECT currency_percentage FROM commonshop_product_discount_t WHERE isvalid=true and pid='{$v['pid']}' limit 0,1";

            $data_currency = $this->db->getRow($sql_currency);

            $currency = 0;
            
            if($data_currency['currency_percentage']>0){
                $currency = $data_currency['currency_percentage']*100;
            }

            $imgurl = $data['default_imgurl'];
            if(empty($imgurl) && $data){
                $query_img ="SELECT imgurl from weixin_commonshop_product_imgs where isvalid=true and product_id={$data['id']} limit 0,1";
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

            $res[$k]['name']            = $data['name'];
            $res[$k]['orgin_price']     = $data['orgin_price'];
            $res[$k]['now_price']       = $data['now_price'];
            $res[$k]['default_imgurl']  = $imgurl;
            $res[$k]['typename']        = $typename;
            $res[$k]['storenum']        = $data['storenum'];
            $res[$k]['currency']        = $currency;
        }
        $return['pageCount']    = $pageCount;
        $return['activity_arr'] = $res;
        return $return;
    }

    /**
     * [获取产品列表]
     * @param  [array] $data    [各参数]    
     * @return [array] $return  [数组]
     */
    public function get_queue_product($data) {

        //分页设置 start
        $pageSize = $data['pageSize'] ? : 20;//每页多少条
        $pageNum = $data['pageNum'] ? : 1; //当前页,1开始
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
        $query = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='{$this->customer_id}' AND parent_id=-1 AND is_shelves=1";

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
            $query_c2 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='{$this->customer_id}' AND parent_id=$parent_id AND is_shelves=1";
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
                    $query_c3 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='{$this->customer_id}' AND parent_id={$ch_id2} AND is_shelves=1";
                    $result_c3= $this->db->getAll($query_c3);
                    foreach ($result_c3 as $type_k3 => $row_c3) {
                        $ch_id3 = $row_c3['id'];
                        $ch_name3 = $row_c3['name'];
                        $select3 = '';

                        if($data['product_type'] == $ch_id3){ $select3 = 'selected';}
                        
                        $obj .= '<option value="'.$ch_id3.'" '.$select3.' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name3.'</option>';

                        $ch_id4 = -1;
                        $ch_name4 = '';// 第四级分类
                        $query_c4 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id='{$this->customer_id}' AND parent_id={$ch_id3} AND is_shelves=1";
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

        $sql_arr = "select p.pid from ".WSY_MARK.".weixin_commonshop_queue as q left join ".WSY_MARK.".weixin_commonshop_queue_product as p on q.id = p.queue_id where q.isvalid=1 and q.isout!=2 and customer_id='{$this->customer_id}' and p.isvalid = 1 and p.queue_id='{$data['activity_id']}'";

        $res = $this->db->getAll($sql_arr);
        
        $re_str = '';
        
        if($res) {
            $str = '';

            foreach ($res as $key_r =>$val_r) {
                if ($val_r['pid']) {
                    $str = $str.','.$val_r['pid'];
                }
            }

            $str = substr($str,1);

            if ($str) {
                $re_str = " and id not in ({$str}) ";
            }
        }

        $activity_arr = array();

        $sql_c = "SELECT count(id) from weixin_commonshop_products where customer_id='{$this->customer_id}' and isvalid = 1 and isout = 0 and yundian_id<0 {$re_str}";
        
        $sql = "SELECT id,name,orgin_price,now_price,default_imgurl,type_ids,storenum from weixin_commonshop_products where customer_id='{$this->customer_id}' and isvalid = 1 and isout = 0 and yundian_id<0 {$re_str}";

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
            $sql_currency = "SELECT currency_percentage FROM commonshop_product_discount_t WHERE isvalid=true and pid='{$v['id']}' limit 0,1";
            $data_currency = $this->db->getRow($sql_currency);

            $currency = 0;

            if($data_currency['currency_percentage']>0){
                $currency = $data_currency['currency_percentage']*100;
            }

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
            $res[$k]['currency']        = $currency;
        }

        $return['type'] = $obj;
        $return['pageCount'] = $pageCount;
        $return['activity_arr'] = $res;
        return $return;
    }

    /**
     * [添加关联产品操作]
     * @param  [array] $param   [各参数]    
     * @return [array] $return  [执行结果，为0表示失败，1成功]
     */
    public function product_add($param) {

        $sql = "SELECT id from weixin_commonshop_products where customer_id='{$this->customer_id}' and isvalid = 1 and isout = 0 AND id in ({$param['idsStr']})";
        $res = $this->db->getAll($sql);
        $add_id = '';
        foreach ($res as $k => $v) {
            $data['queue_id']       = $param['activity_id'];
            $data['pid']            = $v['id'];
            $data['isvalid']        = true;
            $data['createtime']     = date('Y-m-d H:i:s',time());

            $this->db->autoExecute(WSY_MARK.".weixin_commonshop_queue_product", $data, 'insert');
            $add_id = $add_id.$this->db->insert_id().',';
        }

        $return['add'] = substr($add_id,0,strlen($add_id)-1);
        $return['errcode'] = 1;
        $return['errmsg'] = "添加成功！";
        return $return;
    }

    /**
     * [删除关联产品操作]
     * @param  [array] $param   [各参数]    
     * @return [array] $return  [执行结果，为0表示失败，1成功]
     */
    public function product_del($param){
        $return = array();
        $return['errcode'] = 0;
        $return['errmsg']  = "删除失败！";

        $where = "isvalid = true and id = ".$param['pid']." and queue_id=".$param['activity_id'];
        $data['isvalid']   = false;

        $res = $this->db->autoExecute(WSY_MARK.".weixin_commonshop_queue_product", $data, 'update',$where);

        if($res){
            $return['errcode'] = 1;
            $return['errmsg']  = "删除成功！";
        }

        return $return;
    }

    /**
     * [添加操作日志]
     * @param  [array] $data    [各参数]   
     */
    public function add_queue_logs($data) {
        $this->db->autoExecute(WSY_MARK.".weixin_commonshop_queue_log", $data, 'insert');
    } 

    /**
     * [删除关联产品操作]
     * @param  [array] $param   [各参数]    
     * @return [array] $return  [执行结果，为0表示失败，1成功]
     */
    public function get_queue_count($param){
        require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/php-emoji/emoji.php');
        //分页设置 start
        $pageSize = $param['pageSize'] ? : 20;//每页多少条
        $pageNum = $param['pageNum'] ? : 1; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;        
        //分页设置 end
        $activity_arr  = array();
        $id            = -1;
        $activity_name = "";
        $activity_id   = -1;
        $isout         = 3;
        $user_name     = "";
        $user_id       = -1;
        $status        = -1;
        $batchcode     = -1;

        if(!empty($param['id'])){            
            $id = (int)$param['id'];
        }

        if($param['activity_name']){            
            $activity_name = mysql_escape_string($param['activity_name']);
        }

        if(!empty($param['activity_id'])){            
            $activity_id = (int)$param['activity_id'];
        }
        
        if($param['isout']){
            $isout = $param['isout'];
        }

        if($param['user_name']){            
            $user_name = mysql_escape_string($param['user_name']);
        }

        if(!empty($param['user_id'])){            
            $user_id = (int)$param['user_id'];
        }
        
        if($param['status']){
            $status = $param['status'];
        }

        if($param['batchcode']){
            $batchcode = $param['batchcode'];
        }

        $sql_c = "SELECT 
                      count(o.id) as num  
                  FROM ".WSY_MARK.".weixin_commonshop_queue_order as o 
                  LEFT JOIN ".WSY_MARK.".weixin_commonshop_queue as q on q.id=o.queue_id
                  LEFT JOIN ".WSY_USER.".weixin_users as u on u.id=o.user_id
                  LEFT JOIN weixin_commonshop_queue_order_link as l on o.id = queue_order_id 
                  WHERE 
                      o.isvalid=true 
                  and q.isvalid=true 
                  and u.isvalid=true
                  and o.customer_id=".$this->customer_id." 
                  and q.customer_id=".$this->customer_id." 
                  and u.customer_id=".$this->customer_id;

        $sql = "SELECT 
                    o.id,
                    o.queue_id,
                    o.user_id,
                    o.queue_code,
                    o.status,
                    o.expenditure,
                    o.promote_num,
                    o.queue_time,
                    q.expenditure as q_expenditure,
                    q.promote_num as q_promote_num,
                    q.queue_expenditure,
                    q.success_num,
                    q.bonus,
                    q.isout,
                    q.get_impose,
                    q.id as qid,
                    q.name,
                    u.id as weixin_id,
                    u.weixin_name,
                    l.batchcode 
                FROM ".WSY_MARK.".weixin_commonshop_queue_order as o 
                LEFT JOIN ".WSY_MARK.".weixin_commonshop_queue as q on q.id=o.queue_id 
                LEFT JOIN ".WSY_USER.".weixin_users as u on u.id=o.user_id
                LEFT JOIN weixin_commonshop_queue_order_link as l on o.id = queue_order_id 
                WHERE 
                    o.isvalid=true 
                and q.isvalid=true 
                and u.isvalid=true
                and o.customer_id=".$this->customer_id." 
                and q.customer_id=".$this->customer_id." 
                and u.customer_id=".$this->customer_id;

        /************** 队列搜索条件 start ******************/
        if($status!=-1){
            if($status == 7) {
                $sql_c .= " and o.status in (7) ";
                $sql   .= " and o.status in (7) ";
            } elseif($status == 0) {
                $sql_c .= " and o.status = 0 ";
                $sql   .= " and o.status = 0 ";
            } else {
                $sql_c .= " and o.status in ({$status}) ";
                $sql   .= " and o.status in ({$status}) ";
            }
        }
        if($activity_id!=-1){
            $sql_c .= " and o.queue_id=".$activity_id;
            $sql   .= " and o.queue_id=".$activity_id;
        } 
        if($id!=-1){
            $sql_c .= " and o.id=".$id;
            $sql   .= " and o.id=".$id;
        }

        if($activity_name!=""){
            $sql_c .= " and q.name like '%".$activity_name."%'";
            $sql   .= " and q.name like '%".$activity_name."%'";
        }

        switch ($isout) {
            case 1:
                $sql_c .= " and q.isout=1";
                $sql   .= " and q.isout=1";
                break;
            case 2:
                $sql_c .= " and q.isout=2";
                $sql   .= " and q.isout=2";
                break;
            default:
                $sql_c .= "";
                $sql   .= "";
                break;
        }
        
        if($user_name!=""){
            $sql_c .= " and u.weixin_name like '%".$user_name."%'";
            $sql   .= " and u.weixin_name like '%".$user_name."%'";
        }

        if($user_id!=-1){
            $sql_c .= " and u.id=".$user_id;
            $sql   .= " and u.id=".$user_id;
        }

        if($batchcode!=-1){
            $sql_c .= " and l.batchcode='".$batchcode."'";
            $sql   .= " and l.batchcode='".$batchcode."'";
        }
        /************** 队列搜索条件 end ******************/
        
        $activity_count = $this->db->getRow($sql_c);

        $pageCount = ceil($activity_count['num']/$pageSize);//总页数
        if( $param['pageNum'] > 0 ){
            $sql .= " order by o.queue_code desc limit ".$start.",".$end;
        }
        // var_dump($sql);
        $arr = $this->db->getAll($sql);
        foreach ($arr as $k => $v) {

            $e_num = $v['q_expenditure'] - $v['expenditure'];
            $e_num = $e_num > 0 ? $e_num : 0;

            $p_num = $v['q_promote_num'] - $v['promote_num'];
            $p_num = $p_num > 0 ? $p_num : 0;

            $sql = "SELECT count(id) as code FROM ".WSY_MARK.".weixin_commonshop_queue_order 
                    WHERE queue_time>'0000-00-00 00:00:00' 
                      AND queue_id=".$v['queue_id']."
                      AND queue_code<=".$v['queue_code']." 
                      AND isvalid=true 
                      AND customer_id=".$this->customer_id;

            $count_code = $this->db->getRow($sql);//排队号

            $sql = "SELECT count(id) as num FROM ".WSY_MARK.".weixin_commonshop_queue_order 
                    WHERE queue_time>'0000-00-00 00:00:00' 
                      AND queue_time<'".$v['queue_time']."' 
                      AND queue_id=".$v['queue_id']." 
                      AND isvalid=true 
                      AND status=1 
                      AND customer_id=".$this->customer_id;

            $count = $this->db->getRow($sql);//排队人数

            $c_num = $count['num'];

            $queue_code = '-';
            if ($v['status'] != 0){
                $queue_code = $count_code['code'];
                if ($count_code['code'] == 0) {
                    $queue_code = '-';
                }
            }

            if ( $c_num == 0 || $c_num < 0) {
                $c_num = '-';
            } else {
                $c_num.= '人';
            }

            if ($v['status'] == 2 || $v['status'] == 0) {
                $c_num = '-';
            }

            $res[$k]['sid']                = $v['id'];
            $res[$k]['id']                 = $v['queue_code'] > 0 ? $v['queue_code'] : 0;
            $res[$k]['queue_code']         = $queue_code;
            $res[$k]['weixin_name']        = emoji_html_to_unified($v['weixin_name']);
            $res[$k]['weixin_id']          = $v['weixin_id'];
            $res[$k]['status']             = $v['status'];
            $res[$k]['count_num']          = $c_num;
            $res[$k]['o_expenditure']      = floatval($v['expenditure']) != -1 ? '￥'.$v['expenditure'] : '-';
            $res[$k]['e_num']              = floatval($e_num) != -1 ? '￥'.number_format($e_num, 2) : floatval($e_num);
            $res[$k]['o_promote_num']      = floatval($v['promote_num']) != -1 ? floatval($v['promote_num']) : '-';
            $res[$k]['p_num']              = floatval($p_num);
            $res[$k]['queue_name']         = $v['name'];
            $res[$k]['queue_id']           = $v['qid'];
            $res[$k]['queue_expenditure']  = floatval($v['queue_expenditure']) != -1 ? '￥'.$v['queue_expenditure'] : '无限制';
            $res[$k]['success_num']        = floatval($v['success_num']) != -1 ? $v['success_num'].'人' : '无限制';
            $res[$k]['q_expenditure']      = floatval($v['q_expenditure']) != 0 ? '￥'.$v['q_expenditure'] : '无限制';
            $res[$k]['promote_num']        = floatval($v['q_promote_num']) != -1 ? $v['q_promote_num'] : '0';
            $res[$k]['bonus']              = floatval($v['bonus']) != -1 ? '￥'.$v['bonus'] : '-';
            $res[$k]['isout']              = $v['isout'];
            $res[$k]['get_impose']         = $v['get_impose'];
            $res[$k]['user_id']            = $v['user_id'];
            $res[$k]['batchcode']          = $v['batchcode'];
        }
        $result['pageCount'] = $pageCount;
        $result['arr']       = $res;
        return $result;
    }

    /**
     * [修改状态为已删除]
     * @param  [array] $data    [各参数]   
     */
    public function update_status($data,$id) {
        $where = "isvalid = true and id=".$id." and customer_id = ".$this->customer_id;
        $res = $this->db->autoExecute(WSY_MARK.".weixin_commonshop_queue_order", $data, 'update',$where) ;
    } 
    /**
     * [查询用户微信id]
     * @param  [array] $data    [各参数：商家id，活动id]
     * @return [array] $result  [数组]
     */
    public function get_weixin_fromuser($user_id){
        $sql ="SELECT weixin_fromuser from ".WSY_USER.".weixin_users where customer_id='".$this->$customer_id."' and id = '".$user_id."' and isvalid = true";
        $result = $this->db->getRow($sql);
        return $result;
    }
    /**
     * [删除数据]
     * @param  [array] $data    [各参数]   
     */
    public function data_del($data,$id) {
        $where = "id=".$id." and customer_id = ".$this->customer_id;
        $res = $this->db->autoExecute(WSY_MARK.".weixin_commonshop_queue_order", $data, 'update',$where) ;
        var_dump($res);
    } 
}
