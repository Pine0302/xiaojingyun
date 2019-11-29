<?php	

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——基本设置列表
    开 发 者：zhangqiusong
    开发日期： 2018-04-04
    重要说明：无
     */
    function shop_setting_list(){
        $customer_id = $this->customer_id;
        //查询主题
        $theme     = $this->model_common->find_theme($customer_id);
        $res       = $this->model->setting_list_select($customer_id);
        //查询是否存在云店身份的店主
        $is_keeper = $this->model->keeper_select($customer_id);
        // var_dump($res);
        if ($res['errcode'] ==0) {
        	$yundian_onoff          = $res['res1']['yundian_onoff'];
        	$yundian_apply_onoff    = $res['res1']['yundian_apply_onoff'];
        	$yundian_choucheng      = $res['res1']['yundian_choucheng'];
        	$receipt_onoff          = $res['res1']['receipt_onoff'];
        	$receipt_time           = $res['res1']['receipt_time'];
        	$invalid_onoff			= $res['res1']['invalid_onoff'];
        	$invalid_time			= $res['res1']['invalid_time'];
        	$clearing_onoff			= $res['res1']['clearing_onoff'];
        	$playmoney_onoff		= $res['res1']['playmoney_onoff'];
        	$complete_onoff			= $res['res1']['complete_onoff'];
        	$shop_valid_time		= $res['res1']['shop_valid_time'];
        	$shop_notice_time		= $res['res1']['shop_notice_time'];
        	$yundian_reward			= $res['res1']['yundian_reward'];
        	$remark                 = $res['res1']['remark'];
        	$res                    = $res['res2'];
        	// var_dump($res);
        }else{
            $data_ini['customer_id']            = $customer_id;//商家id
            $data_ini['yundian_onoff']          = false;//云店开关
            $data_ini['yundian_apply_onoff']    = false;//云店申请开关
            $data_ini['yundian_choucheng']      = '0';//自营产品抽成
            $data_ini['receipt_onoff']          = false;//默认收货时间开关
            $data_ini['receipt_time']           = '7';//默认收货时间
            $data_ini['invalid_onoff']          = false;//订单失效开关
            $data_ini['invalid_time']           = '30';//订单失效时间
            $data_ini['clearing_onoff']         = false;//自营产品订单收货自动结算开关
            $data_ini['playmoney_onoff']        = false;//售后平台打款开关
            $data_ini['complete_onoff']         = false;//完成退款之后自动完成订单
            $data_ini['shop_valid_time']        = '365';//默认店主有效天数
            $data_ini['shop_notice_time']       = '15';//提前通知天数
            $data_ini['yundian_reward']         = '0';//云店奖励比例
            $data_ini['remark']                 = '云店店主申请协议';//云店店主申请协议
            $data_ini['createtime']             = date('Y-m-d H:i:s');//创建时间
            $data_ini2['is_identity']           = false;//身份开关
            $data_ini2['customer_id']           = $customer_id;//商家id
            $data_ini2['name']                  = '云店店主';//身份名称
            $data_ini2['reward']                = '0';//比例
            $data_ini2['apply_money']           = '0';//申请条件金额
            $data_ini2['tequan']                = '1_1_1_1_1';//各特权是否开启(店铺推广_个性化店标_收益实时查询_店铺自营订单管理_个性化产品管理)
            $data_ini2['remark']                = '特权描述';//特权描述
            $data_ini2['createtime']            = date('Y-m-d H:i:s');//创建时间
            $init = $this->model->initialize_setting($data_ini,$data_ini2);
            echo '<script type="text/javascript">
                    location.href="/mshop/admin/index.php?m=yundian&a=shop_setting_list";
                    </script>';
            exit;
        }

        //获取所有奖励比例
        $reward_data = $this->model->reward_selcet($customer_id);
        //计算出推广奖励比例
        $all = $reward_data['team']+$reward_data['shareholder']+$reward_data['globalbonus']+$reward_data['investmen'];
        $gts = $all+$yundian_reward;
        $tg_reward = 1-($gts.'');
        include('view/yundian/shop_setting_list.html');

    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——基本设置保存
    开 发 者：zhangqiusong
    开发日期： 2018-04-04
    重要说明：无
     */
    function shop_setting_sava(){
    	$customer_id = $this->customer_id;
    	$data['customer_id']            = $customer_id;
    	$data['isvalid']				= true;
    	$data['yundian_onoff']   		= $_REQUEST['yundian_onoff']?$_REQUEST['yundian_onoff']:flase;  //云店开关
    	if ($data['yundian_onoff'] == 0) 
        {
    		$data['yundian_apply_onoff'] = 0;
    	}
        else
        {   
            //云店申请开关        
    		$data['yundian_apply_onoff'] = $_REQUEST['yundian_apply_onoff']?$_REQUEST['yundian_apply_onoff']:flase; 
    	}

        $data['yundian_choucheng']      = $_REQUEST['yundian_choucheng']?$_REQUEST['yundian_choucheng']:'0';  //自营产品总抽成
        $data['receipt_onoff']    		= $_REQUEST['receipt_onoff']?$_REQUEST['receipt_onoff']:flase;  //默认收货时间开关
        $data['receipt_time']    		= $_REQUEST['receipt_time']?$_REQUEST['receipt_time']:'7';  //默认收货时间
        $data['invalid_onoff']    		= $_REQUEST['invalid_onoff']?$_REQUEST['invalid_onoff']:flase;  //订单失效开关
        $data['invalid_time']    		= $_REQUEST['invalid_time']?$_REQUEST['invalid_time']:'30';  //订单失效时间
        $data['clearing_onoff']    		= $_REQUEST['clearing_onoff']?$_REQUEST['clearing_onoff']:flase;  //自营产品订单收货自动结算开关
        $data['playmoney_onoff']    	= $_REQUEST['playmoney_onoff']?$_REQUEST['playmoney_onoff']:flase;  //售后平台打款开关
        $data['complete_onoff']    		= $_REQUEST['complete_onoff']?$_REQUEST['complete_onoff']:flase;  //完成退款之后自动完成订单
        $data['shop_valid_time']    	= $_REQUEST['shop_valid_time']?$_REQUEST['shop_valid_time']:'365';  //初次有效时长
        $data['shop_notice_time']    	= $_REQUEST['shop_notice_time']?$_REQUEST['shop_notice_time']:'15';  //提前通知天数
        $data['yundian_reward']    		= $_REQUEST['yundian_reward']?$_REQUEST['yundian_reward']:'0';  //云店奖励比例
        $data['remark']    				= addslashes($_REQUEST['remark']);  //云店店主申请协议
        $data['createtime']             = date("Y-m-d H:i:s",time());
        $result                         = $_REQUEST['result'];

        if($data['receipt_time'] < 0 || $data['receipt_time'] > 15){    //默认最大收货时间为15天
            $data['receipt_time'] = 15;
        }
        if($data['invalid_time'] < 0 || $data['invalid_time'] > 30){    //默认订单最大失效时间为30分钟
            $data['invalid_time'] = 30;
        }

         $log_remark = $this->model->compare_yundian_setting($data,$result,$customer_id);

        $res = $this->model->sava_setting($data);
        foreach ($result as $k => $v) 
        {
        	$data2['id']           = $result[$k]['id'];
        	$data2['is_identity']  = $result[$k]['is_identity'];
        	$data2['customer_id']  = $customer_id;
        	$data2['name']         = $result[$k]['name'];
        	$data2['reward']       = $result[$k]['reward']?$result[$k]['reward']:0;
        	$data2['apply_money']  = $result[$k]['apply_money'];
        	$data2['createtime']   = date("Y-m-d H:i:s",time());

        	$arr = $result[$k]['tequan'];
        	$a   = $arr[1]?$arr[1]:0;
        	$b   = $arr[2]?$arr[2]:0;
        	$c   = $arr[3]?$arr[3]:0;
        	$d   = $arr[4]?$arr[4]:0;
        	$e   = $arr[5]?$arr[5]:0;
        	$data2['tequan'] = $a."_".$b."_".$c."_".$d."_".$e;
        	$res2 = $this->model->sava_tequan($data2);
        }

        //插入日志
        $log_data['customer_id'] = $customer_id;
        $log_data['remark']      = $log_remark['remark'];
        $log_data['title']      = $log_remark['title'];
        $log = $this->model->save_admin_yundian_log($log_data);

    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——店主特权描述编辑
    开 发 者：zhangqiusong
    开发日期： 2018-04-04
    重要说明：无
     */
    function identity_edit()
    {
    	$data['customer_id'] = $this->customer_id;
    	$theme               = $this->model_common->find_theme($data['customer_id']);
    	$data['id']          = $_REQUEST['id'];
    	$data['type']        = $_REQUEST['type'];
    	if ($data['type']    == 'edit') 
        {
    		$data['remark']  =  $_REQUEST['remark'];
    	}
        $res =  $this->model->identity_edit($data);

    	include('view/yundian/identity_edit.html');
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——删除店主
    开 发 者：zhangqiusong
    开发日期： 2018-04-09
    重要说明：无
     */
    function identity_del(){
    	$data = $_POST;
    	extract($data);
    	$data['customer_id']  = $this->customer_id;
    	if ($is_ajax == 1) 
        {
    		$result = $this->model->identity_del($data);
    		json_out($result);
    	}
    }

    /*
    版权信息:  秘密信息
    功能描述：云店奖励——后台添加店主特权身份
    开 发 者：zhangqiusong
    开发日期： 2018-04-09
    重要说明：$data里的数据说明： customer_id //商家ID   is_ajax //是否ajax请求
     */
    function identity_add(){
    	$data = $_POST;
    	extract($data);
    	$data['customer_id']  = $this->customer_id;
    	if ($is_ajax == 1) {
    		$result  = $this->model->identity_add($data);
    		json_out($result);
    	}
    }

/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主商品列表
    开 发 者：zqs
    开发日期： 2018-04-09
    重要说明：无
     */
    function shopkeeper_order_list(){
    	$customer_id = $this->customer_id;
        //查询主题
        $theme       = $this->model_common->find_theme($customer_id);
    	$data['customer_id']    = $customer_id;
    	$data['realname']     	= $_GET['realname']?$_GET['realname']:'';  //店主昵称
        $data['user_id']        = $_GET['user_id']?$_GET['user_id']:'';	   //店主id
        $data['name']           = $_GET['name']?$_GET['name']:'';          //商品名称
        $data['type']           = $_GET['type']?$_GET['type']:'1';         //商品状态 1.全部商品 2.上架中 3.下架
        $data['pageNum']        = $_GET['pagenum']?$_GET['pagenum']:1;     //当前页
        $data['page_size']      = 20;                                      //每页显示数    	
        $result = $this->model->shopkeeper_order_select($data);

        //获取所有商品数量，上架商品数量，下架商品数量
        $result2 = $this->model->get_shopkeeper_order_num($customer_id);
        $pageNum                = $data['pageNum'];                        //当前页
        if ($data['type'] == 1) {
        	$Count                  = $result2['all'];           //数据总条数
        	$pageCount              = ceil($Count/20);           //数据总页数
        }else if($data['type'] == 2){
        	$Count                  = $result2['on'];           //数据总条数
        	$pageCount              = ceil($Count/20);           //数据总页数
        }else if($data['type'] == 3){
        	$Count                  = $result2['out'];           //数据总条数
        	$pageCount              = ceil($Count/20);           //数据总页数
        }
    	include('view/yundian/shopkeeper_order_list.php');
    }

/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主商品上下架及残忍删除店主商品
    开 发 者：zqs
    开发日期： 2018-04-09
    重要说明：无
     */
     function change_isout_get(){
     	$data = $_POST;
     	$data['customer_id'] = $this->customer_id;
     	$res = $this->model->change_isout_get($data);
     	json_out($res);
     }

/*
    版权信息:  秘密信息
    功能描述：云店奖励——店主产品详情
    开 发 者：zqs
    开发日期： 2018-04-10
    重要说明：无
     */
     function shopkeeper_order(){
     	$customer_id = $this->customer_id;
     	$theme  = $this->model_common->find_theme($customer_id);
     	$id     = $_REQUEST['id']?$_REQUEST['id']:"";
     	$res = $this->model->description_select($id);
     	include('view/yundian/shopkeeper_order.html');
     }