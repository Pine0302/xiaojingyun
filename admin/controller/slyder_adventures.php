<?php

/*
版权信息:  秘密信息
功能描述：大转盘营销工具
开 发 者：zhangqiusong
开发日期： 2017-11-01
重要说明：无
 */
class control_slyder_adventures extends control_base
{
    public $model;

    function __construct()
    {
        parent::__construct();
		//登录校验
		parent::check_login();		
		//登录校验 End
		
        require_once('model/slyder_adventures.php');
        $this->model = new model_slyder_adventures();
        require_once('model/common.php');
        $this->model_common = new model_common();
    }

     /*
      * 大转盘营销工具列表页面
      * 传入参数customer_id
      * Author: zhangqiusong $
      * 2017-11-01
      */
    function action_list(){
        $customer_id                     = $this->customer_id;
        //查看当前主题皮肤
        $theme                           = $this->model_common->find_theme($customer_id);
        $pageNum                         = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $data['pageNum']                 = $pageNum;//当前页
        $data['customer_id']             = $customer_id;
        $data['title']                   = $_REQUEST['title']?$_REQUEST['title']:"";//活动名称
        $data['begin_time']              = $_REQUEST['begin_time']?$_REQUEST['begin_time']:"";//开始时间
        $data['end_time']                = $_REQUEST['end_time']?$_REQUEST['end_time']:"";//结束时间
        $data['status']                  = $_REQUEST['status']=="0"?"0":$_REQUEST['status'];//状态
        $data['auto_receive_rewards']    = $_REQUEST['auto_receive_rewards']=="0"?"0":$_REQUEST['auto_receive_rewards'];//发放方式
        $res                          = $this->model->action_list_select($data);
        $data2      = $res['activity_arr'];
        $pageCount = $res['pageCount'];
        // var_dump($data['status']);
        // var_dump($_REQUEST['status']);
        // var_dump($data['auto_receive_rewards'])  ;
        include ('view/slyder_adventures/action_list.html');
    }


     /*
      * 大转盘 启用接口
      * 传入参数customer_id
      * Author: zhangqiusong
      * 2017-11-02
      */
    function action_enable(){
        		
		if(empty($_POST['id']) || !is_numeric($_POST['id']) || $_POST['id']<0){
			json_out(array("errcode"=>41007,"errmsg"=>"参数错误！"));
		}	
		
		$data['customer_id']     = $this->customer_id;
        $data['id']              = $_POST['id'];		
		
        $res_check = $this->model->action_enable_check($data);
		if($res_check['errcode']!=0){
			json_out($res_check);
		}
        $res = $this->model->action_enable($data);
        json_out($res);

    } 


     /*
      * 大转盘 停用接口
      * 传入参数customer_id
      * Author: zhangqiusong
      * 2017-11-02
      */
    function action_disable(){
		
		if(empty($_POST['id']) || !is_numeric($_POST['id']) || $_POST['id']<0){
			json_out(array("errcode"=>41008,"errmsg"=>"参数错误！"));
		}	
		
		$data['customer_id']     = $this->customer_id;
        $data['id']              = $_POST['id'];
        $res = $this->model->action_disable($data);
        json_out($res);
    } 


     /*
      * 大转盘 删除接口
      * 传入参数customer_id
      * Author: zhangqiusong
      * 2017-11-02
      */
    function action_del(){
        $data['customer_id']     = $this->customer_id;
        $data['id']     = $_POST['id']?$_POST['id']:-1;
        $data['create_time'] = date('Y-m-d H:i:s');
        $res = $this->model->action_del($data);       
        echo json_encode($res);
        exit;

    } 


	    
     /*
      * 大转盘营销工具 轮盘活动添加/编辑
      * 传入参数customer_id
      * Author: 
      * 2017-11-01
      */
    function action_edit(){
        $customer_id       = $this->customer_id;
        $customer_id_en    = $this->customer_id_en;
        $theme             = $this->model_common->find_theme($customer_id);
		
		$slyder_id         = $_GET['slyder_id']?$_GET['slyder_id']:-1;
		$slyder_id         = (int)$slyder_id;
		
		if((!is_numeric($_GET['slyder_id']) && !empty($_GET['slyder_id'])))	json_out(array("errcode"=>40002,"errmsg"=>"slyder_id参数错误！"));
		
		//初始化
		$list['type']                       = 1;	//转盘抽奖限制 1.每人每天次数限制 2.每人每单次数限制
		$list['status']                     = 0;	//轮盘状态：1.启用、0.停用
		$list['title']                      = "";	//标题
		$list['begin_time']                 = "";	//活动开始时间
		$list['end_time']                   = "";	//活动结束时间
		$list['limit_order']                = "-1_-1";	//每人每单次数限制 -1为不限制 门槛存储方式为 (20_1,40_2,60_3)
		$list['limit_every_day']            = -1;	//每人每天次数限制 -1为不限制
		$list['award_expiry_date']          = "";	//奖品领取截止日期
		$list['auto_receive_rewards']       = 1;	//领取奖励设置 0.手动 1.自动
		$list['limit_of_participation']     = -1;	//参与人数限制 -1不限
		$list['display_list_of_winner']     = 1;	//显示中奖名单 0.不显示 1.显示
		$list['cumulative_frequency_type']  = 1;	//抽奖次数累计 1.当天可累计 2.当前活动累计 3.不可累计
		$list['is_fact_pay']                = 0;  	//订单实付款累计：1.开、0.关
		$limit_order_arr = explode(",",$list["limit_order"]);//每人每单次数限制 拆成数组
		
		if($slyder_id > 0){
			//编辑
			$action= "edit";
			$data['id']          = $slyder_id;
			$data['isvalid']     = true;
			$data['customer_id'] = $customer_id;
			$result = $this->model->action_select($data);
			if($result["errcode"] != 0)	json_out($result);
			$list   = $result["data"];
			
			if($list["limit_order"] != '-1'){	
				$limit_order_arr = explode(",",$list["limit_order"]);
			}
		}else{
			//新增
			$action= "add";
		}
		//var_dump($list);
        include ('view/slyder_adventures/action_edit.html');
    }	

	
	/*
      * 大转盘营销工具 轮盘活动保存
      * 传入参数customer_id
      * Author: 
      * 2017-11-01
      */
    function action_save(){
		$result["errcode"] = 0;
		$result["errmsg"]  = "success";
        $customer_id    = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
		$slyder_id      = $_POST['slyder_id']?$_POST['slyder_id']:-1;
		$slyder_id      = (int)$slyder_id;
		
        $type 						= $_POST['type']?$_POST['type']:1;										//转盘抽奖限制 1.每人每天次数限制 2.每人每单次数限制
		$status 					= $_POST['status']?$_POST['status']:0;									//轮盘状态：1.启用、0.停用
		$title						= $_POST['title']?$_POST['title']:"";									//标题
        $title						= mysql_escape_string($title);
		$begin_time					= $_POST['begin_time']?$_POST['begin_time']:"";							//活动开始时间
        $end_time					= $_POST['end_time']?$_POST['end_time']:"";								//活动结束时间
        $limit_every_day 			= $_POST['limit_every_day']?$_POST['limit_every_day']:-1;				//每人每天次数限制 -1为不限制
        $limit_money_arr			= $_POST['limit_money']?$_POST['limit_money']:"";						//每人每单次数限制 -1为不限制 门槛存储方式为 (20_1,40_2,60_3)
        $limit_times_arr			= $_POST['limit_times']?$_POST['limit_times']:"";						//每人每单次数限制 -1为不限制 门槛存储方式为 (20_1,40_2,60_3)
		$auto_receive_rewards 		= $_POST['auto_receive_rewards']?$_POST['auto_receive_rewards']:false;		//领取奖励设置 0.手动 1.自动
        $award_expiry_date 			= $_POST['award_expiry_date']?$_POST['award_expiry_date']:"";			//奖品领取截止日期
        $limit_of_participation		= $_POST['limit_of_participation']?$_POST['limit_of_participation']:-1;	//参与人数限制 -1不限
        $display_list_of_winner 	= $_POST['display_list_of_winner']?$_POST['display_list_of_winner']:false;	//显示中奖名单 0.不显示 1.显示
        $cumulative_frequency_type 	= $_POST['cumulative_frequency_type']?$_POST['cumulative_frequency_type']:0;//抽奖次数累计 0.不可累计 1.累计
        $is_fact_pay			    = $_POST['is_fact_pay']?$_POST['is_fact_pay']:0;						//累计实付款金额
		if( $type == 2 ){
			foreach($limit_money_arr as $k=>$v){
				if( empty($limit_order) ){
					$limit_order = $v."_".$limit_times_arr[$k];
				}else{
					$limit_order = $limit_order.",".$v."_".$limit_times_arr[$k];
				}
			}
		}else{
			$limit_order = "-1_-1";
		}

		
		if($slyder_id > 0){
			//更新
			$condition = [];
			$condition['id'] = $slyder_id;
			$condition['customer_id'] = $customer_id;
			$value = array(
				'type'						=>$type,
				'title'						=>$title,
				'status'					=>$status,
				'begin_time'				=>$begin_time,
				'end_time'					=>$end_time,
				'limit_every_day'			=>$limit_every_day,
				'limit_order'				=>$limit_order,
				'auto_receive_rewards'		=>$auto_receive_rewards,
				'award_expiry_date'			=>$award_expiry_date,
				'limit_of_participation'	=>$limit_of_participation,
				'display_list_of_winner'	=>$display_list_of_winner,
				'cumulative_frequency_type'	=>$cumulative_frequency_type,
				'is_fact_pay'               =>$is_fact_pay,
			);
			$res = $this->model->action_update($condition,$value);
			if($res["errcode"] != 0)	json_out(array("errcode"=>40004,"errmsg"=>"更新失败！"));
		}else{
			//新增
			$value = array(
				'type'						=>$type,
				'title'						=>$title,
				'status'					=>$status,
				'isvalid'					=>true,
				'customer_id'				=>$customer_id,
				'create_time'				=>date("Y-m-d H:i:s",time()),
				'begin_time'				=>$begin_time,
				'end_time'					=>$end_time,
				'limit_every_day'			=>$limit_every_day,
				'limit_order'				=>$limit_order,
				'auto_receive_rewards'		=>$auto_receive_rewards,
				'award_expiry_date'			=>$award_expiry_date,
				'limit_of_participation'	=>$limit_of_participation,
				'display_list_of_winner'	=>$display_list_of_winner,
				'cumulative_frequency_type'	=>$cumulative_frequency_type,
				'is_fact_pay'               =>$is_fact_pay,
			);
			$res = $this->model->action_insert($value);
			if($res["errcode"] != 0)	json_out(array("errcode"=>40004,"errmsg"=>"新增失败！"));
			$result["slyder_id"]= $res["slyder_id"];
		}
			
        json_out($result);
    }

     /*
      * 大转盘营销工具 轮盘活动奖品编辑
      * 传入参数customer_id
      * Author: 
      * 2017-11-01
      */
    function award_edit(){
		$customer_id        = $this->customer_id;
        $customer_id_en     = $this->customer_id_en;
        $theme              = $this->model_common->find_theme($customer_id);
		
		$slyder_id          = $_GET['slyder_id']?$_GET['slyder_id']:-1;
		$slyder_id          = (int)$slyder_id;
		if(!is_numeric($_GET['slyder_id']) || $slyder_id<0){
			json_out(array("errcode"=>40002,"errmsg"=>"slyder_id参数错误！"));
		}
		
				
		//活动状态
		$status               = 0;
		$param['id']          = $slyder_id;
		$param['isvalid']     = true;
		$param['customer_id'] = $customer_id;
		$result = $this->model->action_select($param);
		if($result["errcode"] == 0){ $status = $result["data"]["status"]; }
		
		//初始化
		$list  = array();
		$list[0]["num"]          = 0;		//奖品数量
		$list[0]["img"]          = "";		//奖品图片
		$list[0]["name"]         = "";		//奖品名称
		$list[0]["probability"]  = 0;		//中奖概率
		$list[0]["award_type"]   = 1;		//1.优惠券 2.商品
		$list[0]["coupon_id"]    = -1;		//如果奖品类型为优惠券需关联的优惠券编号
		$list[0]["award_level"]  = 1;		//奖品等级 1~16级
		$list[0]["num_limit_day"]= -1;		//每日限制发奖量
		$list[0]["express_price"]= 0;		//运费
		
		//奖项信息
		$data["isvalid"]    = true;
		$data["slyder_id"]  = $slyder_id;
		$result = $this->model->award_select($data);
		if($result["errcode"] == 0){
			$list  = array();
			$list  = $result["data"];
		}

		//活动已启用，但是没有添加奖项
		if($status > 1 && empty($result["data"])){
			$list  = array();
		}
		
		//var_dump($list);
        include ('view/slyder_adventures/award_edit.html');
    }
	
	 /*
      * 大转盘营销工具 轮盘活动奖品保存
      * 传入参数customer_id
      * Author: 
      * 2017-11-01
      */
    function award_save(){
		$customer_id      = $this->customer_id;
        $customer_id_en   = $this->customer_id_en;
		if(empty($_POST['slyder_id']) || $_POST['slyder_id'] < 0){
			json_out(array("errcode"=>40002,"errmsg"=>"slyder_id参数错误！"));
		}else{
			$slyder_id    = $_POST['slyder_id']?$_POST['slyder_id']:-1;
			$slyder_id    = (int)$slyder_id;
		}
		$dataStr = $_POST['dataStr']?$_POST['dataStr']:"";	//所有奖励表单数据
		if(empty($dataStr))	json_out(array("errcode"=>40002,"errmsg"=>"dataStr参数错误，请填写好奖项信息！"));
		$dataArr = json_decode ( $dataStr, true );
		$result  = $this->model->award_save_all($customer_id,$slyder_id,$dataArr);

		json_out($result);
    }
	
	/*
      * 大转盘营销工具 轮盘活动奖品删除
      * 传入参数customer_id
      * Author: 
      * 2017-11-01
      */
    function award_del(){
		$customer_id      = $this->customer_id;
        $customer_id_en   = $this->customer_id_en;
		if(empty($_POST['award_id']) || $_POST['award_id'] < 0){
			json_out(array("errcode"=>40002,"errmsg"=>"award_id参数错误！"));
		}else{
			$award_id    = $_POST['award_id']?$_POST['award_id']:-1;
			$award_id    = (int)$award_id;
		}
		if(empty($_POST['slyder_id']) || $_POST['slyder_id'] < 0){
			json_out(array("errcode"=>40002,"errmsg"=>"slyder_id参数错误！"));
		}else{
			$slyder_id    = $_POST['slyder_id']?$_POST['slyder_id']:-1;
			$slyder_id    = (int)$slyder_id;
		}
		$value["isvalid"]= false;
		$condition["id"] = $award_id;
		$condition["slyder_id"] = $slyder_id;
		$result  = $this->model->award_update($condition,$value);

		json_out($result);
    }
	
	
	public function save_pic(){
        require_once(ROOT_DIR.'mp/lib/image.php');
        $customer_id = $this->customer_id;
		//$img_size    = 800;
        //图片上传
        if($_FILES['imgFile']['tmp_name']){
            $image = new image();
            $file_path = $image->upload_image ($_FILES['imgFile'],$customer_id,'slyder_adventures');
        }
        if($file_path){
            $file_path='/resources/'.$file_path;
            json_out(array('errcode'=>0,'errmsg'=>$file_path));
        }else{
            json_out(array('errcode'=>40004,'errmsg'=>'上传失败！'));
        }
    }
	
	
	function select_num($n){
		$result = "";
		switch($n){
			case 1:
				$result = "一";
			break;
			case 2:
				$result = "二";
			break;
			case 3:
				$result = "三";
			break;
			case 4:
				$result = "四";
			break;
			case 5:
				$result = "五";
			break;
			case 6:
				$result = "六";
			break;
			case 7:
				$result = "七";
			break;
			case 8:
				$result = "八";
			break;
			case 9:
				$result = "九";
			break;
			case 10:
				$result = "十";
			break;
			case 11:
				$result = "十一";
			break;
			case 12:
				$result = "十二";
			break;
			case 13:
				$result = "十三";
			break;
			case 14:
				$result = "十四";
			break;
			case 15:
				$result = "十五";
			break;
			case 16:
				$result = "十六";
			break;
			case 17:
				$result = "十七";
			break;
			case 18:
				$result = "十八";
			break;
			case 19:
				$result = "十九";
			break;
			case 20:
				$result = "二十";
			break;
			default:
				$result = "未知";
			break;
		}
		return $result;
	}

     /*
      * 大转盘营销工具 查看获奖名单
      * 传入参数customer_id
      * Author: zhangqiusong
      * 2017-11-06
      */
    function name_list(){
        $customer_id            = $this->customer_id;
        //查看主题颜色
        $theme                  = $this->model_common->find_theme($customer_id);		
		//查看主题颜色 End
        $data['slyder_id']      = $_GET['slyder_id'];
        $data['title']          = $_GET['title']?$_GET['title']:"";//活动名称
        $pageNum                = $_GET['pagenum']?$_GET['pagenum']:1;//当前页
        $data['pageNum']        = $pageNum;//当前页
        $data['customer_id']    = $customer_id;
        $data['weixin_name']    = $_GET['weixin_name']?$_GET['weixin_name']:"";//用户微信名
        $data['user_id']        = $_GET['user_id']?$_GET['user_id']:"";//用户ID
        $data['name']           = $_GET['name']?$_GET['name']:"";//用户姓名
        $data['phone']          = $_GET['phone']?$_GET['phone']:"";//用户电话
        $data['status']         = $_GET['status']=="0"?"0":$_GET['status'];//状态
        $data['award_id']       = ">-1";//获奖名单
        //查询出对应的活动名称
        $data3['id']     =$data['slyder_id'];
        $data3['customer_id']   =$data['customer_id'];
        $result   = $this->model->action_select($data3);
        $title                  =$result['data']['title'];

        $res      = $this->model->name_list_select($data);
        $data2          = $res['activity_arr'];
        $pageCount      = $res['pageCount'];
        include ('view/slyder_adventures/name_list.html');
    }   

     /*
      * 大转盘营销工具 查看无奖名单
      * 传入参数customer_id
      * Author: zhangqiusong
      * 2017-11-06
      */
      function notwin_list(){
        $customer_id            = $this->customer_id;
        //查看主题颜色
        $data['slyder_id']      = $_GET['slyder_id'];
        $data['title']          = $_GET['title']?$_GET['title']:"";//活动名称
        $theme                  = $this->model_common->find_theme($customer_id);
        $pageNum                = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $data['pageNum']        = $pageNum;//当前页
        $data['customer_id']    = $customer_id;
        $data['weixin_name']    = $_REQUEST['weixin_name']?$_REQUEST['weixin_name']:"";//用户微信名
        $data['user_id']        = $_REQUEST['user_id']?$_REQUEST['user_id']:"";//用户ID
        $data['name']           = $_REQUEST['name']?$_REQUEST['name']:"";//用户姓名
        $data['phone']          = $_REQUEST['phone']?$_REQUEST['phone']:"";//用户电话
        $data['award_id']       = "=-1";//无奖名单
        //查询出对应的活动名称
        $data3['id']     =$data['slyder_id'];
        $data3['customer_id']   =$data['customer_id'];
        $result   = $this->model->action_select($data3);
        $title                  =$result['data']['title'];

        $res            =$this->model->name_list_select($data);
        $data2      = $res['activity_arr'];
        // var_dump($data2);
        $pageCount = $res['pageCount'];
        include ('view/slyder_adventures/notwin_list.html');
      }	 
	  
	 /*
      * 大转盘营销工具 查看优惠券列表
      * 传入参数customer_id
      * Author: liquanhui
      * 2017-11-06
      */
      function coupon_list(){
		$customer_id        = $this->customer_id;
        $customer_id_en     = $this->customer_id_en;
        $theme              = $this->model_common->find_theme($customer_id);
		$pageNum            = $_GET["pageNum"]?$_GET["pageNum"]:1;	//第几页
		$pageCount          = 1;	//总页数
		$list     		    = array();
		$condition          = [];
		/** 搜索条件 start **/
		$search_id          = $_GET["search_id"]?$_GET["search_id"]:"";					//查询编号
		$search_title       = $_GET["search_title"]?$_GET["search_title"]:"";			//查询标题
		$search_class_type  = $_GET["search_class_type"]?$_GET["search_class_type"]:0;	//查询优惠券类型
		if($search_id > 0){
			$condition["id"] = $search_id;
		}
		if(!empty($search_title)){
			$condition["title"] = $search_title;
		}
		if($search_class_type > 0){
			$condition["class_type"] = $search_class_type;
		}
		/** 搜索条件 end **/
		$condition["isvalid"]     = true;
		$condition["customer_id"] = $customer_id;
		$result = $this->model->coupon_sel($pageNum,$condition);
		if($result["errcode"] == 0){
			$pageCount = $result["pageCount"];
			$list      = $result["couponList"];
		}
        include ('view/slyder_adventures/coupon_list.html');
      }
	  
	 /*
      * 大转盘营销工具 查看产品列表
      * 传入参数customer_id
      * Author: liquanhui
      * 2017-11-06
      */
      function product_list(){
		$customer_id        = $this->customer_id;
        $customer_id_en     = $this->customer_id_en;
        $theme              = $this->model_common->find_theme($customer_id);
		$pageNum            = $_GET["pageNum"]?$_GET["pageNum"]:1;	//第几页
		$pageCount          = 1;	//总页数
		$list     		    = array();
		$condition          = [];
		/** 搜索条件 start **/
		$search_id          = $_GET["search_id"]?$_GET["search_id"]:"";		//查询编号
		$search_name        = $_GET["search_name"]?$_GET["search_name"]:"";	//查询标题
		$search_type        = $_GET["search_type"]?$_GET["search_type"]:"";	//查询产品类型
		if($search_id > 0){
			$condition["id"] = $search_id;
		}
		if(!empty($search_name)){
			$condition["name"] = $search_name;
		}
		if(!empty($search_type)){
			$condition["type_ids"] = $search_type;
		}
		/** 搜索条件 end **/
		$condition["customer_id"] = $customer_id;
		$result = $this->model->select_products($pageNum,$condition);
		if($result["errcode"] == 0){
			$pageCount = $result["pageCount"];
			$list      = $result["productList"];
		}

		//查询分类start
		$type_list = $this->model->select_product_type($customer_id,$search_type);
		//查询分类end
		

        include ('view/slyder_adventures/product_list.html');
      }

	 /*
      * 大转盘营销工具 用户统计查看明细列表
      * 传入参数customer_id
      * Author: zhangqiusong
      * 2017-11-24
      */
	 function action_user_statis(){
        $customer_id            = $this->customer_id;
        //查看主题颜色
        $theme                  = $this->model_common->find_theme($customer_id);
        $pageNum                = $_GET['pagenum']?$_GET['pagenum']:1;//当前页
        $data['pageNum']        = $pageNum;//当前页
        $data['customer_id']    = $this->customer_id;  
        $data['slyder_id']      = $_GET['slyder_id']?$_GET['slyder_id']:"";
        $data['user_id']        = $_GET['user_id']?$_GET['user_id']:"";
        $data['title']          = $_GET['title']?$_GET['title']:"";
        $data['award_level']    = $_GET['award_level']?$_GET['award_level']:"";
        $data['create_time']    = $_GET['create_time']?$_GET['create_time']:"";
        $data['create_time_end']= $_GET['create_time']?$_GET['create_time_end']:"";
        $data['name']           = $_GET['name']?$_GET['name']:"";
        $data['phone']          = $_GET['phone']?$_GET['phone']:"";
        $data['status']         = $_GET['status']=='0'?"0":$_GET['status'];
        $res            =$this->model->action_user_statis_select($data);
        $data2      = $res['activity_arr'];
        $user_arr   = $res['user_arr'];
        $pageCount = $res['pageCount'];
        // var_dump($data2);
        include('view/slyder_adventures/action_user_statis.html');

	 }
	
	 /*
      * 大转盘营销工具 活动统计列表
      * 传入参数customer_id
      * Author: zhangqiusong
      * 2017-11-08
      */
	 function statis_action_list(){
        $customer_id            = $this->customer_id;
        //查看主题颜色
        $theme                  = $this->model_common->find_theme($customer_id);
        $pageNum                = $_GET['pagenum']?$_GET['pagenum']:1;//当前页
        $data['pageNum']        = $pageNum;//当前页
        $data['customer_id']    = $this->customer_id;  
        $data['id']             = $_GET['id']?$_GET['id']:"";
        $data['title']          = $_GET['title']?$_GET['title']:"";
        $data['begin_time']     = $_GET['begin_time']?$_GET['begin_time']:"";
        $data['end_time']       = $_GET['end_time']?$_GET['end_time']:"";
        $data['status']         = $_GET['status']=='0'?"0":$_GET['status'];
        $res            =$this->model->statis_action_list($data);
        $data2      = $res['activity_arr'];
        $pageCount = $res['pageCount'];
        // var_dump($data2);
        include('view/slyder_adventures/statis_action_list.html');

	 }

	 /*
      * 大转盘营销工具 用户明细列表
      * 传入参数customer_id
      * Author: zhangqiusong
      * 2017-11-08
      */
	 function statis_action_user_list(){
        $customer_id            = $this->customer_id;
        //查看主题颜色
        $theme                  = $this->model_common->find_theme($customer_id);
        $pageNum                = $_GET['pagenum']?$_GET['pagenum']:1;//当前页
        $jump_page              = $_GET['jump_page']?:2;//用于判断用户从哪个界面跳转
        $data['pageNum']        = $pageNum;//当前页
        $data['customer_id']    = $this->customer_id;
        $data['slyder_id']      = $_GET['slyder_id'];
        $data['user_statistics']= $_GET['user_statistics']?:0;
        $data['weixin_name']    = $_GET['weixin_name']?:"";
        $data['user_id']        = $_GET['user_id']?:"";
        $data['title']          = $_GET['title'];
        $data['createtime']     = $_GET['createtime']?:"";
        $data['createtime_end'] = $_GET['createtime_end']?:"";
        $res              =$this->model->statis_action_user_list($data);
        $data2                  =$res['activity_arr'];
        $pageCount              =$res['pageCount'];
        include('view/slyder_adventures/statis_action_user_list.html');
	 }
	 
	 
	 
     /*
      * 大转盘营销工具 基础设置
      * 传入参数customer_id
      * Author: zpd
      * 2017-11-01
      */
    function basic_setting(){
        $customer_id       = $this->customer_id;
        $customer_id_en    = $this->customer_id_en;
        $theme             = $this->model_common->find_theme($customer_id);
		
	
		//查看基础设置
		$data['customer_id'] = $customer_id;
		$result = $this->model->basic_setting_select($data);
				
		//var_dump($result);
		if($result['errcode'] == 0){
			$is_open                  = $result['data']['is_open'];
			$is_display_person_center = $result['data']['is_display_person_center'];
			$is_display_my_records    = $result['data']['is_display_my_records'];
			$description              = $result['data']['description'];			
			$base_id                  = $result['data']['id'];			
		}else{
			$is_open                  = false;
			$is_display_person_center = false;
			$is_display_my_records    = false;
			$description              = '';				
			$base_id                  = -1;				
		}

		//查看基础设置 End
		
		//var_dump($list);
        include ('view/slyder_adventures/basic_setting.html');
    }	 
	 

	 /*
      * 大转盘营销工具 基础设置保存
      * 传入参数customer_id
      * Author: zpd
      * 2017-11-13
      */
    function basic_setting_save(){
		$customer_id      = $this->customer_id;
        $customer_id_en   = $this->customer_id_en;
		if(empty($_POST['id']) || !is_numeric($_POST['id'])){
			json_out(array("errcode"=>40902,"errmsg"=>"参数错误！"));
		}else{
			$base_id    = $_POST['id']?$_POST['id']:-1;
		}
		
		//查看基础设置
		$c['customer_id'] = $customer_id;
		$result_c = $this->model->basic_setting_select($c);
		$id = $result_c['data']['id'];
		if($id!=$base_id)	$base_id = $id;
		
			$data['is_open']                  = $_POST['is_open']?$_POST['is_open']:false;	
			$data['is_display_person_center'] = $_POST['is_display_person_center']?$_POST['is_display_person_center']:false;	
			$data['is_display_my_records']    = $_POST['is_display_my_records']?$_POST['is_display_my_records']:false;	
			$data['description']              = $_POST['introduce']?$_POST['introduce']:"";	
			
		if($base_id>0){				
			$condition['customer_id']	= $customer_id;
				
			$result  = $this->model->basic_setting_update($condition,$data);
		}else{
			$data['customer_id']              = $customer_id;
			
			$result  = $this->model->basic_setting_insert($data);			
		}

		json_out($result);
    }

	 
     /*
      * 大转盘营销工具 中奖订单管理
      * Author: zpd
      * 2017-11-06
      */
    function reward_order_list(){
        $customer_id      = $this->customer_id;
		$customer_id_en   = $this->customer_id_en;
				
        //查看主题颜色
        $theme                  = $this->model_common->find_theme($customer_id);
		//查看主题颜色 End

        //自动刷新
		$isauto = 0;
		if(!empty($_GET["isauto"])){
			$isauto = (int)$_GET['isauto'];
		}
		//自动刷新 End
		
		//搜索条件
		$condition['orders.customer_id']	= $customer_id;		
		
		if(!empty($_GET["search_begintime"])){ 
			$condition['search_begintime']  = $_GET['search_begintime'];		//下单时间开始
		}
		if(!empty($_GET["search_endtime"])){ 
			$condition['search_endtime']    = $_GET['search_endtime'];		    //下单时间结束
		}		
		if(!empty($_GET["pay_begintime"])){ 
			$condition['pay_begintime']  = $_GET['pay_begintime'];		   //订单支付时间开始
		}
		if(!empty($_GET["pay_endtime"])){ 
			$condition['pay_endtime']    = $_GET['pay_endtime'];		   //订单支付时间结束
		}
		

		if(!empty($_GET["search_batchcode"])){ 
			$condition['orders.batchcode']    = $_GET['search_batchcode'];		   //订单号
		}				
		if(!empty($_GET["search_product_name"])){ 
			$condition_ext['orders.product_name']  = $_GET['search_product_name'];  //产品名称
		}
		if(!empty((int)$_GET["search_class"])){ 
			$condition['orders.status']         = (int)$_GET['search_class'];		        //订单状态
		}
		
		$condition_ext['search_name']       = $_GET['search_name']?$_GET['search_name']:"";		        //搜索姓名
		$condition_ext['search_name_type']  = $_GET['search_name_type']?$_GET['search_name_type']:"1";	//搜索姓名类型
		if(!empty((int)$_GET["search_phone"])){ 
			$condition['address.phone']      = (int)$_GET['search_phone'];		    //搜索手机号
		}		
		$condition_ext['search_paystyle']   = $_GET['search_paystyle']?$_GET['search_paystyle']:"";		//支付方式
									
		
		//搜索条件 End
		
		//分页  start
		$pagenum = 1;
		if(!empty($_GET["pagenum"])){
		   $pagenum = $_GET["pagenum"];
		}
		$pagesize = 20;
		if(!empty($_GET["pagesize"])){
		   $pagesize = $_GET["pagesize"];
		}
		$start = ($pagenum-1) * $pagesize;
		$end = $pagesize;		

		$wcount = 0;
		$page   = 0;		
		$order_num = $this->model->order_lists_num($condition,$condition_ext);
		$wcount    = $order_num['data']['num'];
		$page      = ceil($wcount/$end);
		//分页  End

		//查询订单列表
		$result  = $this->model->order_lists_select($condition,$pagenum,$pagesize,$condition_ext);	
		//查询订单列表  End		
		
		//查询快递列表
		$express = array();
		$condition_express['customer_id']  = $customer_id;
		$expresses_company  = $this->model->order_expresses_company_select($condition_express);	
		if($expresses_company['errcode'] == 0){
			$express = $expresses_company['expresses_company'];
		}
		//查询快递列表  End

		//订单状态判断条件
		$send_status_array = array(2,3);  //已发货状态
		$send_button       = array(0,1);  //未发货状态
		//订单状态判断条件  End
		
		//查询平台是否开启虚拟发货 (大转盘暂不定制)
		// $query_virtual = "select open_virtual_cust from weixin_commonshops where customer_id = ".$customer_id;
		// $open_virtual_cust = 1;
		// $result_virtual = _mysql_query($query_virtual) or die("query_virtual Query error : ".mysql_error());
		// if($row_virtual = mysql_fetch_object($result_virtual)){
		// 	$open_virtual_cust = $row_virtual -> open_virtual_cust;
		// }

		//var_dump($result);
		//var_dump($order_num);
		include ('view/slyder_adventures/reward_order.html');
	}
	 
	

     /*
      * 大转盘营销工具 中奖订单管理-修改发货地址
      * Author: zpd
      * 2017-11-06
      */
    function reward_order_changeAdd(){
		$result["errcode"] = 0;
		$result["errmsg"]  = "success";
		
        $customer_id      = $this->customer_id;
		$customer_id_en   = $this->customer_id_en;

		$order_id    = $_POST['order_id']?$_POST['order_id']:-1;      //订单编号
		if(empty($order_id) || !is_numeric($order_id) || $order_id<0){
			json_out(array("errcode"=>41002,"errmsg"=>"参数错误！"));
		}		
		
		$condition['customer_id'] = $customer_id; 
		$condition['order_id']    = $order_id; 
		
		$data['name']          = $_POST['addressName']?$_POST['addressName']:"";	//姓名
		$data['phone']         = $_POST['addressPhone']?$_POST['addressPhone']:"";	//地址
		$data['location_p']    = $_POST['addressP']?$_POST['addressP']:"";	    //地址-省
		$data['location_c']    = $_POST['addressC']?$_POST['addressC']:"";	    //地址-市
		$data['location_a']    = $_POST['addressA']?$_POST['addressA']:"";	    //地址-区
		$data['address']       = $_POST['addressAdd']?$_POST['addressAdd']:"";	//地址-详情

		if(empty($data))	json_out(array("errcode"=>41003,"errmsg"=>"参数错误，请填写收货地址信息！"));
		
		//查询订单列表
		$result  = $this->model->order_address_update($condition,$data);	
		//查询订单列表  End		
		json_out($result);
		
	}	
	
	
		
     /*
      * 大转盘营销工具 中奖订单管理-修改发货地址
      * Author: zpd
      * 2017-11-06
      */
    function reward_order_send(){
		$result["errcode"] = 0;
		$result["errmsg"]  = "success";
		
        $customer_id      = $this->customer_id;
		$customer_id_en   = $this->customer_id_en;

		$order_id    = $_POST['order_id']?$_POST['order_id']:-1;      //订单编号
		if(empty($order_id) || !is_numeric($order_id) || $order_id<0){
			json_out(array("errcode"=>41102,"errmsg"=>"参数错误！"));
		}	
		
		$express_id   = $_POST['expressID']?$_POST['expressID']:-1;      //快递方式编号	
		if(empty($express_id) || !is_numeric($express_id) || $express_id<0){
			json_out(array("errcode"=>41103,"errmsg"=>"快递方式错误！"));
		}

		$batchcode      = $_POST["batchcode"]?$_POST['batchcode']:"";          //订单号
		$express_remark = $_POST["expressRemark"]?$_POST['expressRemark']:"";  //发货备注
		$express_num    = $_POST["expressNum"]?$_POST['expressNum']:"";        //快递单号
		

		//查询订单状态
		$data_status['customer_id'] = $customer_id;
		$data_status['order_id']    = $order_id;	
		$result_status  = $this->model->order_status_select($data_status);	
		//var_dump($result_status);
		//return;
		if($result_status['errcode'] != 0) json_out($result_status);
		//查询订单状态  End	

		//判断订单状态
		$status   = $result_status['status'];
		$user_id  = $result_status['user_id'];
		$pro_name = $result_status['product_name'];
		
		if($status != 1)   json_out(array("errcode"=>41104,"errmsg"=>"订单状态不正确，请检查订单"));	
		//判断订单状态 End
		

		/* 查询OpenID */
		$order_fromuser = "";
		$data_user['customer_id'] = $customer_id;
		$data_user['user_id']     = $user_id;	
		$result_user  = $this->model->user_openID_select($data_user);	
		//var_dump($result_user);
		//return;
		if($result_user['errcode'] != 0) json_out($result_user);
		$order_fromuser  = $result_user['open_id'];
		/* 查询OpenID End */

		/* 查询快递 */
		$condition_express['customer_id'] = $customer_id;
		$condition_express['express_id']  = $express_id;
		
		$expressname = '';
		$expresses_company  = $this->model->order_expresses_company_select($condition_express);	
		if($expresses_company['errcode'] != 0) json_out($result_userresult_user);
		$expressname = $expresses_company['expresses_company'][0]['expresses_name'];
		//var_dump($expresses_company);
		//return;		
		/* 查询快递 End */		
		
		
		/* 自动收货时间 */
		$auto_cus_time = 7;
		$return_time  = $this->model->shop_auto_cus_time_select($customer_id);	
		if($return_time['errcode'] != 0) json_out($return_time);
		$auto_cus_time = $return_time['auto_cus_time'];
		if ($return_time['auto_cus_time'] == 0){
			$auto_cus_time = 7;
		}
		//var_dump($auto_cus_time);
		//return;		
		/* 自动收货时间 End */

		/* 日志 */
		$log_username = $_SESSION['curr_login'];
		$data_log['order_id']       = $order_id;
		$data_log['operation']      = 4;
		$data_log['descript']       = "平台发货[物流：".$expressname.",单号：".$express_num."]";
		$data_log['operation_user'] = $log_username;
		$data_log['isvalid']        = true;
		$return_log  = $this->model->order_log_insert($data_log);	
		/* 日志 end */

		/* 修改订单状态 */
		$condition_order['id']     = $order_id;
		
		$data_order['status']      = 2;
		$data_order['send_status'] = 1;
		$return_order  = $this->model->order_status_update($condition_order,$data_order);	
		if($return_order['errcode'] != 0) json_out($return_order);		
		/* 修改订单状态 end */
		
		
		/*插入发货记录*/
		$result_add  = $this->model->order_address_select($order_id);
		//var_dump($result_add);
		//return;
		if($result_add['errcode'] != 0) json_out($result_add);	
		$adress    = $result_add['adress'];
		$data_delivery['name']          = $adress['name'];
		$data_delivery['phone']         = $adress['phone'];
		$data_delivery['location_p']    = $adress['location_p'];
		$data_delivery['location_c']    = $adress['location_c'];
		$data_delivery['location_a']    = $adress['location_a'];
		$data_delivery['address']       = $adress['address'];	
		$data_delivery['order_id']      = $order_id;
		$data_delivery['express_num']   = $express_num;
		$data_delivery['express_id']    = $express_id;
		$data_delivery['remark']        = $express_remark;
		$return_delivery  = $this->model->order_delivery_insert($data_delivery);
		if($return_delivery['errcode'] != 0) json_out($return_delivery);	
		$delivery_id = $return_delivery['delivery_id'];
		/*插入发货记录 end  */
		
		/*更新自动确认收货时间*/
		$condition_de['id'] = $delivery_id;
		$return_delivery_ar  = $this->model->order_delivery_auto_receivetime_update($condition_de,$auto_cus_time);
		if($return_delivery_ar['errcode'] != 0) json_out($return_delivery_ar);	
		/*更新自动确认收货时间 End*/
		

		/* 发送消息 */
		$content = "亲，您有一笔大转盘奖品订单【已发货】\n\n奖品：".$pro_name."\n时间：".date( "Y-m-d H:i:s")."\n快递：".$expressname."";
		if(!empty($express_remark)){
			$content=$content."\n备注：".$express_remark;
		}
		$content=$content."\n\n<a href='https://m.kuaidi100.com/result.jsp?nu=".$express_num."'>【查看物流进度】</a>\n<a href='".Protocol. $_SERVER['HTTP_HOST']."/mshop/web/index.php?m=slyder_adventures&a=order_details&customer_id=" . $customer_id . "&batchcode=".$batchcode."'>【查看订单详情】</a>";
		
			
		$wx_msg['openid']      = $order_fromuser;
		$wx_msg['content']     = mysql_escape_string($content);
		$wx_msg['customer_id'] = $customer_id;
		$wx_msg['user_id']     = $user_id;
		$return_msg  = $this->model->weixin_msg_insert($wx_msg);	
		if($return_msg['errcode'] != 0) json_out($return_msg);		
		/* 发送消息 */
		
		$result['time'] = date( "Y-m-d H:i:s");		
		json_out($result);
		
	}		
	
	/**
	 * [导入订单]
	 * @author liupeixin
	 * @date   2019-01-19
	 * @return [array]     [是否导入成功]
	 */
	public function import_excel(){
		$result = array();

        //是否存在文件
        if (!is_uploaded_file($_FILES["excelfile"]["tmp_name"])){
			$result["errcode"] = 40001;
	        $result["errmsg"]  = "请重新导入xls文件";
        } else {
        	$result = $this->model->import_excel($_FILES, $this->customer_id);
        }
        json_out($result);
	}
	
}

?>