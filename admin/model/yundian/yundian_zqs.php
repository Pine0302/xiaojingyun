<?php

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

        if ($num > 5) 
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
                           'tequan'      => '1_1_1_1_1',
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
    版权信息:  秘密信息
    功能描述：云店奖励——店主商品列表查询
    开 发 者：zqs
    开发日期： 2018-04-10
    重要说明：无
     */
    public function shopkeeper_order_select($data){
        $result = array();
        $sql = "select p.id,k.realname,k.user_id,p.default_imgurl,p.name,t.name as fenlei,p.now_price,p.storenum,p.sell_count,p.isout from ".WSY_PROD.".weixin_commonshop_products as p INNER JOIN ".WSY_PROD.".weixin_commonshop_types as t ON p.type_id= t.id INNER JOIN ".WSY_USER.".weixin_yundian_keeper as k ON k.id=p.yundian_id where";
        $where = " p.customer_id='".$data['customer_id']."' and p.isvalid=true";
        if (!empty($data['realname'])) {
            $where.=" and k.realname like '%".$data['realname']."%'";
        }
        if (!empty($data['user_id'])) {
            $where.=" and k.user_id like '%".$data['user_id']."%'";
        }
        if (!empty($data['name'])) {
            $where.=" and p.name like '%".$data['name']."%'";
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
    public function get_shopkeeper_order_num($customer_id){
        //获取所有商品数量
        $sql = "select count(*) from ".WSY_PROD.".weixin_commonshop_products as p INNER JOIN ".WSY_PROD.".weixin_commonshop_types as t ON p.type_id= t.id INNER JOIN ".WSY_USER.".weixin_yundian_keeper as k ON k.id=p.yundian_id and k.isvalid=1 where";
        //查询所有商品数量
        $sql_all = $sql." p.customer_id='".$customer_id."' and p.isvalid=true";
        $result['all'] = $this->db->getOne($sql_all);
        // $result['all'] = count($res_all);
        //查询上架商品数量
        $sql_on = $sql." p.customer_id='".$customer_id."' and p.isvalid=true and p.isout=false";
        $result['on'] = $this->db->getOne($sql_on);
        // $result['on'] = count($res_on);
        //查询下架商品数量
        $sql_out = $sql." p.customer_id='".$customer_id."' and p.isvalid=true and p.isout=true";
        $result['out'] = $this->db->getOne($sql_out);
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
        $reward_data['is_team'] = $onoff['is_team'];
        $reward_data['is_shareholder'] = $onoff['is_shareholder'];
        $reward_data['isOpenGlobal'] = $isOpenGlobal;
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