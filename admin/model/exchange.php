<?php

class model_exchange{
    var $db;

    function __construct($customer_id)
    {
        $this->customer_id = $customer_id;
        $this->db = DB::getInstance();
    }

    /**
     * [get_exchange_activities 获取换购活动列表]
     * @param  [array] $param [搜索条件]
     * @return [array] $activity_arr    [活动列表数组形式]
     */
    function get_exchange_activities($param){
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

        $sql = "select id,title,starttime,endtime,status,is_superposition,threshold,exchange_num from weixin_commonshop_exchange where customer_id=".$customer_id." and isvalid=true";
        /************** 搜索条件 start ******************/
        if($activity_id!=-1){
            $sql .= " and id=".$activity_id;
        }
        if($activity_name!=""){
            $sql .= " and title like '%".$activity_name."%'";
        }
        if($starttime!=-1){
            $sql .= " and starttime >= '".$starttime."'";
        }
        if($endtime!=-1){
            $sql .= " and endtime <= '".$endtime."'";
        }
        if($activity_status!=-1){
            $sql .= " and status in ({$activity_status}) ";
        }
        if( $param['threshold'] || $param['threshold'] === 0 ){
            $sql .= " and threshold<=".$param['threshold'];
        }
        if( $param['nowtime'] ){
            $sql .= " and endtime >= '{$param['nowtime']}' ";
            $sql .= " and starttime <= '{$param['nowtime']}' ";
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

        // $return['sql'] = $sql;
        $return['pageCount'] = $pageCount;
        $return['activity_arr'] = $activity_arr;
        return $return;
    }

    /**
     * [launch_activities 发布活动 状态：待发布(1)->已发布(2)或进行中(3) (注：如果发布活动时，正好满足活动时间范围，则活动状态直接变为进行中)]
     * 注：活动状态：状态 1.待发布 2.已发布 3.进行中 4.已结束
     * @param  [array] $param [各参数：商家id，活动id]    
     * @return [int] $err_status  [执行结果，为0表示失败，1成功]
     */
    function launch_activities($param){
        $return = array();
        $return['errcode'] = 0;
        $return['errmsg'] = "发布失败！";

        $customer_id = $param['customer_id'];
        $activity_id = $param['activity_id'];        
        $curtime = date('Y-m-d H:i:s');//当前时间
        $sql_query = "select id from  weixin_commonshop_exchange where starttime <='".$curtime."' and endtime>= '".$curtime."' and id=".$activity_id." and customer_id=".$customer_id." and isvalid=true and status=1";      
        $res_act = $this->db->getAll($sql_query);
        
        if($res_act){            
            $status = 3;//进行中
        }else{
            $status = 2;
        }
       
        $sql = "update weixin_commonshop_exchange set status=".$status." where customer_id=".$customer_id." and isvalid=true and id=".$activity_id." and status=1";//待发布的活动才能发布。
        $res = $this->db->query($sql);
        if($res){
            $return['errcode'] = 1;
            $return['errmsg'] = "发布成功！";

            //添加日志 start
            $log['customer_id'] = $customer_id;
            $log['exchange_id'] = $activity_id;
            $this->add_exchange_logs($log,6);
            //添加日志 end
        }
        return $return;
    }

    /**
     * [end_activities 终止活动（手动or过期活动自动终止）]
     * @param  [array] $param [description]
     * @return [type]        [description] 状态变化：所有状态->已结束（4）
     */
    function end_activities($param){
        $return = array();
        $return['errcode'] = 0;
        $return['errmsg'] = "终止失败！";

        $customer_id = $param['customer_id'];
        $activity_id = $param['activity_id'];
        if($activity_id>0){//手动终止活动
            $sql_update = "update weixin_commonshop_exchange set status=4 where customer_id=".$customer_id." and isvalid=true and id=".$activity_id;
            $res = $this->db->query($sql_update);
            if($res){
                $return['errcode'] = 1;
                $return['errmsg'] = "终止成功！";

                //添加日志 start
                $log_con['customer_id'] = $customer_id; 
                $log_con['exchange_id'] = $activity_id;    
                $this->add_exchange_logs($log_con,8);              
                //添加日志 end 
            }
        }else{//过期活动自动终止
            $curtime = date('Y-m-d H:i:s');
            $sql_query = "select id from weixin_commonshop_exchange where endtime<='".$curtime."' and isvalid=true and customer_id=".$customer_id." and status!=4";
            $res = $this->db->getAll($sql_query);

            $activity_arr = array();
            //添加日志 start
            $log_con['customer_id'] = $customer_id;             
            foreach ($res as $key => $value) {
                $activity_arr[] = $value['id'];
                $log_con['exchange_id'] = $value['id'];    
                $this->add_exchange_logs($log_con,9);
            }  
            //添加日志 end
            
            $activity_id_str = "";
            if($activity_arr){
                $activity_id_str = implode(",", $activity_arr);
            }
            if($activity_id_str != ""){
                $sql_update = "update weixin_commonshop_exchange set status=4 where id in (".$activity_id_str.")";
                $res = $this->db->query($sql_update);
            }            

            $return['errcode'] = 1;
            $return['errmsg'] = "终止成功！";         
        }       
        return $return;
    }

    /**
     * [in_process_activities 当前时间在活动时间范围内 则自动将活动改为进行中状态]
     * @param  [array] $param [各参数：customer_id-商家id]
     * @return 状态变化 已发布（2）->进行中（3）
     */
    function in_process_activities($param){
        $customer_id = $param['customer_id'];
        $curtime = date('Y-m-d H:i:s');
        $sql = "select id from weixin_commonshop_exchange where status=2 and starttime<='".$curtime."' and endtime>'".$curtime."' and customer_id=".$customer_id." and isvalid=true";
        $res = $this->db->getAll($sql);        

        $activity_arr = array();

        //添加日志 start
        $log_con['customer_id'] = $customer_id;             
        foreach ($res as $key => $value) {
            $activity_arr[] = $value['id'];
            $log_con['exchange_id'] = $value['id'];    
            $this->add_exchange_logs($log_con,7);
        }  
        //添加日志 end 
        
        $activity_id_str = "";
        if($activity_arr){
            $activity_id_str = implode(",",$activity_arr);  
        } 

        if($activity_id_str != ""){
            $sql_update = "update weixin_commonshop_exchange set status=3 where id in(".$activity_id_str.")";
            $this->db->query($sql_update);
        }          
        
    }

    /**
     * [del_activity 删除活动]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    function del_activity($param){
        $return = array();
        $return['errcode'] = 0;
        $return['errmsg'] = "删除失败！";

        $customer_id = $param['customer_id'];
        $activity_id = $param['activity_id'];

        $sql = "update weixin_commonshop_exchange set isvalid=false where customer_id=".$customer_id." and id=".$activity_id." and isvalid=true";
        $res = $this->db->query($sql);
        if($res){
            $return['errcode'] = 1;
            $return['errmsg'] = "删除成功！";

            //添加日志 start
            $log_con['customer_id'] = $customer_id; 
            $log_con['exchange_id'] = $activity_id;    
            $this->add_exchange_logs($log_con,10);              
            //添加日志 end 
        
        }
        return $return;
    }

    /**
     * [查询是添加活动或者是修改活动]
     * @param  [array] $data    [各参数：商家id，活动id]
     * @return [array] $result  [数组]
     */
    public function get_ex_exchange($data){
        $sql = "select id,title,threshold,exchange_num,is_superposition,starttime,endtime,status from weixin_commonshop_exchange where customer_id=".$data['customer_id']." and id= ".$data['id']." and isvalid = 1 ";

        $result = $this->db->getRow($sql);
        return $result;
    } 

    /**
     * [添加活动操作]
     * @param  [array] $data    [各参数]    
     * @return [array] $return  [执行结果，为0表示失败，1成功]
     */
    public function add_ex($data){
        $return['errcode'] = 0;
        $return['errmsg']  = "保存失败！";

        $res = $this->db->autoExecute('weixin_commonshop_exchange', $data, 'insert') ;
        if ($res) {
            $return['id'] = $this->db->insert_id();
            $return['errcode'] = 1;
            $return['errmsg']  = "保存成功！";
        }
        return $return;
    }

    /**
     * [修改活动操作]
     * @param  [array] $data    [各参数]    
     * @return [array] $return  [执行结果，为0表示失败，1成功]
     */
    public function save_ex($data,$w) {
        $return['errcode'] = 0;
        $return['errmsg'] = "保存失败！";

        $where = "isvalid = true and id=".$w['id']." and customer_id = ".$w['customer_id'];

        $res = $this->db->autoExecute('weixin_commonshop_exchange', $data, 'update',$where) ;

        if ($res) {
            $return['errcode'] = 1;
            $return['errmsg'] = "保存成功！";
        }

        return $return;
    }

    /**
     * [获取关联产品列表]
     * @param  [array] $data    [各参数]    
     * @return [array] $return  [数组]
     */
    public function get_relation($param) {
        
        //分页设置 start
        $pageSize = 20;//每页多少条
        $pageNum = $param['pageNum']; //当前页,1开始
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;
        //分页设置 end

        $activity_arr = array();

        $sql = "select count(id) from weixin_commonshop_exchange_products where exchange_id=".$param['activity_id']." and isvalid = 1 ";
        
        $arr = $this->db->getRow($sql);//总共多少条记录

        $pageCount = ceil($arr['count(id)']/$pageSize);//总页数

        $sql = "select id,pid,storenum,exchange_price,num_per_person,num_per_time from weixin_commonshop_exchange_products where exchange_id=".$param['activity_id']." and isvalid = 1 ORDER BY createtime desc";

        $sql .= " limit ".$start.",".$end;
       
        $res = $this->db->getAll($sql);

        foreach ($res as $k=>$v) {
            $sql = "SELECT id,name,now_price,default_imgurl,type_ids,storenum from weixin_commonshop_products where id='{$v['pid']}' and isvalid = 1 ";

            $data = $this->db->getRow($sql);

            $imgurl = $data['default_imgurl'];

            if(empty($imgurl)){
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
            $res[$k]['now_price']       = $data['now_price'];
            $res[$k]['default_imgurl']  = $imgurl;
            $res[$k]['typename']        = $typename;
            $res[$k]['storenum']        = $v['storenum'];
        }
        $return['pageCount'] = $pageCount;
        $return['activity_arr'] = $res;
        return $return;
    }

    /**
     * [获取添加关联产品列表]
     * @param  [array] $data    [各参数]    
     * @return [array] $return  [数组]
     */
    public function get_add_relation($data) {

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

        $sql_t = "select pid from weixin_commonshop_exchange_products where exchange_id= ".$data['activity_id']." and isvalid = 1 ";

        $re = $this->db->getAll($sql_t);
        
        $re_str = '';
        if($re) {
            $re_s = '';

            foreach ($re as $re_k =>$re_v) {
                $re_s = $re_s.','.$re_v['pid'];
            }

            $re_s = substr($re_s,1);

            $re_str = " and id not in ({$re_s}) ";
        }

        $activity_arr = array();

        $sql_c = "SELECT count(id) from weixin_commonshop_products where customer_id='{$data['customer_id']}' and isvalid = 1 and is_supply_id = -1 and isout = 0 {$re_str}";
        
        $sql = "SELECT id,name,orgin_price,now_price,default_imgurl,type_ids,storenum,is_QR from weixin_commonshop_products where customer_id='{$data['customer_id']}' and isvalid = 1 and is_supply_id = -1 and isout = 0 {$re_str}";

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

    /**
     * [添加关联产品操作]
     * @param  [array] $param   [各参数]    
     * @return [array] $return  [执行结果，为0表示失败，1成功]
     */
    public function add_relation($param) {

        $sql = "SELECT id,name,orgin_price,now_price,default_imgurl,type_ids,storenum from weixin_commonshop_products where customer_id='{$param['customer_id']}' and isvalid = 1 and isout = 0 AND id in ({$param['idsStr']})";
        $res = $this->db->getAll($sql);
        $add_id = '';
        foreach ($res as $k => $v) {
            $data['exchange_id']    = $param['activity_id'];
            $data['pid']            = $v['id'];
            $data['isvalid']        = true;
            $data['storenum']       = $v['storenum'];
            $data['exchange_price'] = $v['now_price'];
            $data['num_per_person'] = -1;
            $data['num_per_time']   = -1;
            $data['createtime']     = date('Y-m-d H:i:s',time());

            $this->db->autoExecute('weixin_commonshop_exchange_products', $data, 'insert');
            $add_id = $add_id.$this->db->insert_id().',';
        }
        
        $param['exchange_id'] = $param['activity_id'];
        $this->add_exchange_logs($param,5);
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
    public function del_relation($param){
        $return = array();
        $return['errcode'] = 0;
        $return['errmsg'] = "删除失败！";

        $where = "isvalid = true and id = ".$param['pid']." and exchange_id=".$param['activity_id'];
        $data['isvalid']        = false;

        $res = $this->db->autoExecute('weixin_commonshop_exchange_products', $data, 'update',$where);

        if($res){
            $return['errcode'] = 1;
            $return['errmsg'] = "删除成功！";
        }

        $param['exchange_id'] = $param['activity_id'];

        $this->add_exchange_logs($param,4);

        return $return;
    }

    /**
     * [修改关联产品操作]
     * @param  [array] $param   [各参数]    
     * @return [array] $return  [执行结果，为0表示失败，1成功]
     */
    public function save_relation($param){
        switch ($param['str']) {
            case 'storenum'://库存
                $str                    = 'storenum';
                $data['storenum']       = $param['obj'];
                break;
            case 'exchange_price'://换购价
                $str                    = 'exchange_price';
                $data['exchange_price'] = $param['obj'];
                break;
            case 'num_per_person'://每人次数
                $str                    = 'num_per_person';
                $data['num_per_person'] = $param['obj'];
                break;
            case 'num_per_time'://每次数量
                $str                    = 'num_per_time';
                $data['num_per_time']   = $param['obj'];
                break;
        }

        $where = "isvalid = true and id = ".$param['id']." and exchange_id=".$param['activity_id'];

        $sql = "SELECT {$str} from weixin_commonshop_exchange_products where {$where}";

        $arr = $this->db->getRow($sql);

        if($arr == $data) {
            $return['errcode'] = 0;
        } else {
            $res = $this->db->autoExecute('weixin_commonshop_exchange_products', $data, 'update',$where);
        }

        if($res){
            $return['errcode'] = 1;
        }

        $data['storenum']       = $arr['storenum'];
        $data['exchange_price'] = $arr['exchange_price'];
        $data['num_per_person'] = $arr['num_per_person'];
        $data['num_per_time']   = $arr['num_per_time'];
        $data['customer_id']    = $param['customer_id'];
        $data['exchange_id']    = $param['activity_id'];
        $data['id']             = $param['id'];

        $this->add_exchange_logs($data,3);

        return $return;
    }

    /**
     * [添加操作日志]
     * @param  [array] $data    [各参数]    
     * @param  [str]   $action  [操作动作]    
     */
    public function add_exchange_logs($data,$action) {
        
        $param['customer_id']   = $data['customer_id'];
        $param['exchange_id']   = $data['exchange_id'];
        $param['isvalid']       = true;
        $param['customer_name'] = $_SESSION['curr_login'];
        $param['createtime']    = date('Y-m-d H:i:s',time());

        switch ($action) {
            case 1:
                $remark = '添加活动';
                break;
            case 2:
                $sql = "SELECT threshold,exchange_num,is_superposition from weixin_commonshop_exchange where id= ".$data['exchange_id']." and isvalid = 1 ";

                $res = $this->db->getRow($sql);    

                if ($data['threshold'] != $res['threshold']) {
                    $remark = '每笔订单门槛：'.$data['threshold'].' 改为 '.$res['threshold'];
                    
                    $param['remark']        = $remark;

                    $this->db->autoExecute('weixin_commonshop_exchange_logs', $param, 'insert') ;
                }

                if ($data['exchange_num'] != $res['exchange_num']) {
                    $remark = '每笔订单产品总量：'.$data['exchange_num'].' 改为 '.$res['exchange_num'];

                    $param['remark']        = $remark;

                    $this->db->autoExecute('weixin_commonshop_exchange_logs', $param, 'insert') ;
                }

                if ($data['is_superposition'] != $res['is_superposition']) {
                    if ($data['is_superposition'] == 1) {
                        $data['is_superposition'] = '是';
                    } else {
                        $data['is_superposition'] = '否';
                    }

                    if ($res['is_superposition'] == 1) {
                        $res['is_superposition'] = '是';
                    } else {
                        $res['is_superposition'] = '否';
                    }

                    $remark = '叠加活动选项：'.$data['is_superposition'].' 改为 '.$res['is_superposition'];

                    $param['remark']        = $remark;

                    $this->db->autoExecute('weixin_commonshop_exchange_logs', $param, 'insert') ;
                }
                break;
            case 3:
                $sql_p = "SELECT storenum,exchange_price,num_per_person,num_per_time from weixin_commonshop_exchange_products where isvalid = true and id = ".$data['id']." and exchange_id=".$data['exchange_id'];

                $res = $this->db->getRow($sql_p);

                if ($data['storenum'] != $res['storenum']) { 
                    if ($data['storenum'] != '') {
                        $remark = '序号:'.$data['id'].'的关联产品库存：'.$data['storenum'].' 改为 '.$res['storenum'];
                    
                        $param['remark']        = $remark;
                    
                        $this->db->autoExecute('weixin_commonshop_exchange_logs', $param, 'insert') ;
                    }
                }

                if ($data['exchange_price'] != $res['exchange_price']) { 
                    if ($data['exchange_price'] != '') {
                        $remark = '序号:'.$data['id'].'的关联产品换购价：'.$data['exchange_price'].' 改为 '.$res['exchange_price'];
                    
                        $param['remark']        = $remark;
                    
                        $this->db->autoExecute('weixin_commonshop_exchange_logs', $param, 'insert') ;
                    }
                }

                if ($data['num_per_person'] != $res['num_per_person']) { 
                    if ($data['num_per_person'] != '') {
                        $remark = '序号:'.$data['id'].'的关联产品每人次数：'.$data['num_per_person'].' 改为 '.$res['num_per_person'];
                    
                        $param['remark']        = $remark;
                    
                        $this->db->autoExecute('weixin_commonshop_exchange_logs', $param, 'insert') ;
                    }
                }

                if ($data['num_per_time'] != $res['num_per_time']) { 
                    if ($data['num_per_time'] != '') {
                        $remark = '序号:'.$data['id'].'的关联产品每次数量：'.$data['num_per_time'].' 改为 '.$res['num_per_time'];
                    
                        $param['remark']        = $remark;
                    
                        $this->db->autoExecute('weixin_commonshop_exchange_logs', $param, 'insert') ;
                    }
                }
                break;
            case 4:
                $remark = '删除关联产品';
                break;
            case 5:
                $remark = '添加关联产品';
                break;
            case 6:
                $remark = "发布活动";
                break;
            case 7:
                $remark = "进行中活动";
                break;
            case 8:
                $remark = "手动终止活动";
                break;
            case 9:
                $remark = "过期活动自动终止";
                break;
            case 10:
                $remark = "删除活动";
                break;

        }

        $param['remark']        = $remark;

        if ($action != 2 && $action != 3) {
            $this->db->autoExecute('weixin_commonshop_exchange_logs', $param, 'insert') ;
        }
    }
    
    /**
     * [获取满赠活动商品列表]
     * @param  [type] $exchange_id [description]
     * @return [type]              [description]
     */
    public function get_exchange_products($exchange_id='',$search='',$ids=''){
        $this->user_id = $_SESSION['user_id_'.$this->customer_id];
        $sql = "SELECT ep.exchange_id,ep.storenum,ep.exchange_price,ep.num_per_person,ep.num_per_time,p.name,p.default_imgurl,p.id,p.propertyids,p.introduce,e.exchange_num 
                from weixin_commonshop_exchange_products as ep
                left join weixin_commonshop_products as p on p.id = ep.pid
                LEFT JOIN weixin_commonshop_exchange AS e ON ep.exchange_id = e.id
                where ep.isvalid=true and p.isvalid=true ";
        if( $search ){
            $sql .= " and p.name like '%{$search}%'";
        }
        if( $exchange_id ){
            $sql .= " and ep.exchange_id='{$exchange_id}'";
        }
        if( $ids ){
            $sql .= " and ep.pid in ({$ids})";
        }
        $data = $this->db->getAll($sql);
        // $data['sql'] = $sql;
        foreach ($data as $key => &$value) {
            //过滤回车、换行
            $value['introduce'] = str_replace(array("\r\n", "\r", "\n"), "", $value['introduce']);
            if( $value['num_per_person'] != -1 ){
                $count = $this->get_exchange_product_count($value['id'],$this->user_id,$value['exchange_id']);
                $value['count'] = $count;
                if( $count >= $value['num_per_person'] ){
                    $value['can_not_select'] = true;
                }else{
                    $value['can_not_select'] = false;
                }
            }
        }
        return $data;
    }

    /**
     * [获取满赠商品详情]
     * @param  [type] $ex_id   [满赠活动编号]
     * @param  [type] $pid     [商品编号]
     * @param  [type] $user_id [用户编号]
     * @return [type]          [description]
     */
    function get_exchange_product_detail($ex_id,$pid,$user_id){
        $sql = "SELECT ep.storenum,ep.exchange_price,ep.num_per_person,ep.num_per_time,p.name,p.description,p.default_imgurl,p.id,p.propertyids  
                from weixin_commonshop_exchange_products as ep
                left join weixin_commonshop_products as p on p.id = ep.pid
                where ep.pid='{$pid}' and ep.isvalid=true and p.isvalid=true and ep.exchange_id='{$ex_id}'";
        $data = $this->db->getRow($sql);

        /*$rp_data['user_id']     = $user_id;
        $rp_data['customer_id'] = $this->customer_id;
        $rp_data['product_id']  = $pid;
        require_once($_SERVER['DOCUMENT_ROOT'].'/mshop/web/model/restricted_purchase.php');
        $restricted_purchase    = new model_restricted_purchase();
        $restricted_result      = $restricted_purchase->findRestrictedPurchase($rp_data);
        $restricted_isout       = $restricted_result['restricted_isout'];
        $countdown_time         = $restricted_result['countdown_time'];
        $buystart_time          = $restricted_result['buystart_time'];
        $now_time               = date("Y-m-d H:i:s",time());
        if( $restricted_isout == 1 ){
            if( $now_time > $countdown_time ){
                $restricted_isout = 2;
            }
        }
        if($restricted_isout == 1 && ($now_time >= $buystart_time) && ($now_time <= $countdown_time) ){
            //产品正在参与限购活动
            $data['is_restricted'] = 1;
        }*/

        return $data;
    }

    /**
     * [获取订单可用满赠活动数据]
     * @param  [type] $price [订单价格]
     * @return [type]        [description]
     */
    public function get_order_exchange_activities($price,$type=''){
        $where['customer_id'] = $this->customer_id;
        $where['nowtime'] = date('Y-m-d H:i:s');
        if( $price!==-1 ){
            $where['threshold'] = $price;
        }
        $where['activity_status'] = '2,3';
        $where['pageNum'] = -1;
        $where['order'] = 'threshold desc';

        $data = $this->get_exchange_activities($where);
        if( $price==-1 ){
            $datas = $data['activity_arr'];
        }else if( $data['activity_arr'] && $price != -1 ){
            $activities = $data['activity_arr'];
            
            $is_superposition = $activities[0]['is_superposition'];
            if( $is_superposition == false ){
                $datas[0] = $activities[0];
            }else{
                $count = count($activities);
                for ($i=0; $i < $count ; $i++) { 
                    if( $activities[$i]['is_superposition'] == false || $activities[$i]['is_superposition'] == 0 ){
                        // array_splice($activities,$i,1);
                    }else{
                        $activitiess[] = $activities[$i];
                    }
                }
                $datas = $activitiess;
            }
        }
        // 反转数组
        if( !$type  ){
            $datas = array_reverse($datas);
        }
        return $datas;
    }

    /**
     * [获取满赠商品用户已用次数]
     * @param  [type] $pid     [商品编号]
     * @param  [type] $user_id [用户编号]
     * @return [type]          [description]
     */
    function get_exchange_product_count($pid,$user_id,$eid){
        $sql = "SELECT DISTINCT batchcode from weixin_commonshop_orders where pid='{$pid}' and user_id='{$user_id}' and exchange_id='{$eid}' and status>=0 and isvalid=true ";
        $bats = $this->db->getAll($sql);
        $count = count($bats);
        return $count;
    }

    /**
     * [获取活动门槛]
     * @return [type] [description]
     */
    function get_exchange_threshold($eid){
        $sql = "SELECT threshold from weixin_commonshop_exchange where id='{$eid}' ";
        $data = $this->db->getRow($sql);
        return $data['threshold'];
    }

    /**
     * [get_operation_log 获取满赠活动操作日志]
     * @param  [array] $param []
     * @return [array] $log_arr     [操作日志数组形式]
     */
    public function get_operation_log($param){
        $pageNum = $param['pageNum'];//当前页
        $pageSize = 20;//每页大小
        $start = ($pageNum-1)*$pageSize;
        $end = $pageSize;

        if($param['pageNum']>0){
            $pageNum = $param['pageNum'];
        }

        $log_arr = array();
        $sql = "select exchange_id,remark,customer_name,createtime from weixin_commonshop_exchange_logs where customer_id=".$param['customer_id']." and isvalid=true";
        $count_res = $this->db->getAll($sql);
        $count_all = count($count_res);//总共多少条记录
        $pageCount = ceil($count_all/$pageSize);//一共多少页，向上取整

        $sql .= " order by id desc limit ".$start.",".$end;
        $log_arr = $this->db->getAll($sql);

        $return['pageCount'] = $pageCount;
        $return['log_arr'] = $log_arr;
        return $return;
    }
}
