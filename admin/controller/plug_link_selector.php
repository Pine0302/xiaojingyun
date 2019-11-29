<?php

/*
版权信息:  秘密信息
功能描述：商城新链接选择
开 发 者：黄泽钦
开发日期： 2017-08-29
重要说明：无
 */
class control_plug_link_selector extends control_base
{
	var $model;

    function __construct()
	{
		parent::__construct();
		require_once('model/plug_link_selector.php');
		$this->model = new model_plug_link_selector();
		
		parent::check_login();

        $data['data']=file_get_contents('php://input', true);
		//$data = $_REQUEST['data'];
		$this->parmdata  = json_decode($data['data'],true);

    }

	function selector_list(){

		$customer_id   = $this->customer_id;
		$customer_id_en = $this->customer_id_en;

        require_once(ROOT_DIR."wsy_user/public/weishi_common.php");
        $weishi_common = new weishi_common($customer_id);

		$is_activity   = 0;
		if(!empty($_GET['is_activity'])){
			$is_activity = $_GET['is_activity'];		//活动橱窗特有获取链接
		}
		$theme = $this->model->get_theme($customer_id);
		if($theme == null || $theme == "undefined" || $theme == ""){
			$theme = "blue";
		}

        //判断是否绑定微视
        $check_ws = $weishi_common->check_ws();
        if ($check_ws) {
            $is_band_weishi = 1;
        } else {
            $is_band_weishi = 0;
        }
		
		/* 权限控制文件 start 所有渠道开关写在该文件里 */
		require_once('view/plug_link_selector/access_control.php');
		/* 权限控制文件 end */
		
		include ('view/plug_link_selector/selector_list.html');
		mysql_close($link);
		
	}
	
	function defaultset_common_function(){
		$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
		mysql_select_db(DB_NAME) or die('Could not select database');
		_mysql_query("SET NAMES UTF8");
		$customer_id   = $this->customer_id;
		$op			   = $_POST['op'];
		$pagenum	   = $_POST['pagenum']?$_POST['pagenum']:1;
		$search_val	   = $_POST['search_val']?$_POST['search_val']:'';
		$search_type   = $_POST['search_type'];						//产品分类ID
		
		if($search_type == '-1'){
			$search_type = "";
		}
		
		$pagesize      = 20;
		$limit		   = " limit ".($pagenum-1)*$pagesize.','.$pagesize;
		
		$condition['customer_id'] = $customer_id;
		switch($op){
			case 'o2o_one_category':				//收银O2O一级分类
				$result = $this->model->select_o2o_one_category($condition,$limit);
				break;
			case 'o2o_category_list':				//收银O2O分类详情
				$condition['parent_id'] = $_POST['parent_id'];
				$condition['class'] = $_POST['class'];
				$result = $this->model->select_o2o_category_list($condition,$limit);
				break;
			case 'o2o_category_next':			//收银O2O二级分类
				$condition['parent_id'] = $_POST['parent_id'];
				$result = $this->model->select_o2o_category_next($condition,$limit);
				break;
			case 'o2o_category_next_list':			//收银O2O二级分类详情
				$condition['parent_id'] 	 = $_POST['parent_id'];
				$condition['parent_id_next'] = $_POST['parent_id_next'];
				$result = $this->model->select_o2o_category_next_list($condition,$limit);
				break;
			case 'photo_text_message':				//图文消息
				$result = $this->model->select_photo_text_message($condition,$limit,$search_val);
				break;
			case 'wei_singlepage':
				$result = $this->model->select_wei_singlepage($condition,$limit,$search_val);
				break;
			case 'diy_template':
				$result = $this->model->select_diy_template($condition,$limit,$search_val);
				break;
			case 'product_type_arr':
				//获取选择框链接
				require_once($_SERVER['DOCUMENT_ROOT']."/weixinpl/common/utility_common.php");
				$shopLink = new shopLink_Utlity($customer_id);
				$link_arr = $shopLink->getSelectLink(array(3), 1);	//3：产品分类
				$result = $link_arr['type_arr'];
				break;
			case 'all_product':
				$result = $this->model->select_all_product($condition,$limit,$search_val,$search_type);
				break;
			case 'card_member':
				$result = $this->model->select_card_member($condition,$limit);
				break;
			case 'product_type':
				$result = $this->model->select_product_type($condition,$limit,$search_val);
				break;
			case 'cityarea_cater':
				$result = $this->model->select_cityarea_shop($condition,$limit,2);
				break;
			case 'cityarea_ktv':
				$result = $this->model->select_cityarea_shop($condition,$limit,30);
				break;
			case 'cityarea_hotel':
				$result = $this->model->select_cityarea_shop($condition,$limit,60);
				break;
			case 'cityarea_store':
				$result = $this->model->select_cityarea_shop($condition,$limit,20);
				break;
			case 'shoptype_name':	//查询线下商城  商家分类
				$result = $this->model->select_shoptype_name($condition,$limit);
				break;
			case 'select_classify_list':	//查询线下商城-商家分类-详细信息
				$condition['parent_id'] = $_POST['parent_id'];
				$result = $this->model->select_classify_list($condition,$limit,$pagenum);
				break;
			case 'product_renew':								//续费活动
				$result = $this->model->select_renew_product($condition,$limit,$search_val,$search_type);
				break;
			case 'renew_activity_list':							//续费活动列表
				$result = $this->model->select_renew_activity($condition,$limit,$search_val);
				break;
			case 'product_restricte_time':						//限购活动
				$result = $this->model->select_product_restricte_time($condition,$limit,$search_val,$search_type);
				break;
			case 'limit_activity_list':							//限时活动列表
				$result = $this->model->select_limit_activity($condition,$limit,$search_val,$search_type);
				break;
			case 'package_list':								//大礼包
				$result = $this->model->select_package_list($condition,$limit,$search_val);
				break;
			case 'product_privilege':							//特权活动
			case 'product_new':									//新品活动
			case 'product_hot':									//热卖活动
			case 'product_vp':									//vp活动
				$result = $this->model->select_all_product($condition,$limit,$search_val,$search_type,$op);
				break;
			case 'integral_product':							//积分活动
				$type			   = $_POST['type'];
				$result = $this->model->select_integral_product($condition,$limit,$search_val,$search_type,$type);
				break;
			case 'intergral_activity_list':						//积分活动列表
				$type			   = $_POST['type'];
				$result = $this->model->select_intergral_activity($condition,$limit,$search_val,$type);
				break;
			case 'send_package':								//赠送活动
				$result = $this->model->select_send_package($condition,$limit,$search_val);
				break;
			case 'exchange_activity_list':						//商品满赠活动列表
				$result = $this->model->select_exchange_activity($condition,$limit,$search_val);
				break;
			case 'pink_activity':								//拼团活动
				$result = $this->model->select_pink_activity($condition,$limit,$search_val);
				break;
			case 'pink_activity_list':							//拼团活动列表
				$result = $this->model->select_all_pink_activity($condition,$limit,$search_val);
				break;
			case 'pink_activity_product':						//拼团活动产品详情
				$result = $this->model->select_pink_activity_product($condition,$limit,$search_val,$search_type);
				break;	
			case 'cr_activity':									//众筹活动
				$result = $this->model->select_cr_activity($condition,$limit,$search_val);
				break;
			case 'cr_activity_product':							//众筹活动产品详情
				$result = $this->model->select_cr_activity_product($condition,$limit,$search_val,$search_type);
				break;
			case 'bargain_activity':							//砍价活动
				$result = $this->model->select_bargain_activity($condition,$limit,$search_val);
				break;
			case 'bargain_activity_product':					//砍价活动产品详情
				$result = $this->model->select_bargain_activity_product($condition,$limit,$search_val,$search_type);
				break;
            case 'cooperative_shop':                            //合作商列表
                $result = $this->model->select_cooperative_shop($condition, $limit,$search_val,$search_type);
                break;
            case 'coupon_list':                                 //优惠券列表
                $result = $this->model->select_coupon_list($condition, $limit,$search_val,$search_type);
                break;
			case 'package_send_activity':
				$result = $this->model->select_send_package_list($condition,$limit,$search_val);
				break;
			case 'select_yiren_list':
                $condition['parent_id'] = $_POST['parent_id'];
				$result = $this->model->select_yiren_list($condition,$limit,$search_val);
				break;
            case 'select_yiren_list2':
            	$condition['parent_id'] = $_POST['parent_id'];
                $result = $this->model->select_yiren_list2($condition,$limit,$search_val);
                break;
            case 'select_yiren_list3':
                $result = $this->model->select_yiren_list3($condition,$limit,$search_val);
                break;
            case "brandsubscribe":
                $result = $this->model->select_brandsubscribe_list($condition,$limit);
		}
		json_out($result);
	}
	
	function common_activity_product(){
		$selector_id = $_POST['selector_id'];
		$selector_title = $_POST['selector_title'];
		
		$temp = explode('-', $selector_id);            //数据组成		分类+ID+标题
		$temp_id_num = count($temp) - 2;
		
		$customer_id   = $this->customer_id;
		$show_num	   = $_POST['show_num']?$_POST['show_num']:1;
		$limit		   = " limit 0,".$show_num;
		$res = array();
		
		$condition['customer_id'] = $customer_id;
		//转换标题中的 - 
		foreach ( $temp as $k => $v )
		{
			$temp[$k] = str_replace('&henggan&', '-', $temp[$k]);
		}
		switch ($temp[1]) {
			case '2': 
				switch($temp[2]){
					case '11':
						switch($temp[3]){
							case '1':	//续费活动
								$result = $this->model->common_activity_product($condition,$limit,$temp[$temp_id_num],1);
								$title  = "续费活动>>".$selector_title;
								break;
							case '2':	//限时活动
								$result = $this->model->common_activity_product($condition,$limit,$temp[$temp_id_num],2);
								$title  = "限时活动>>".$selector_title;
								break;
							case '13':  //商品满赠活动
								$result = $this->model->common_activity_product($condition,$limit,$temp[$temp_id_num],3);
								$title  = "商品满赠活动>>".$selector_title;
								break;
						}
						break;
					case '12':
						switch($temp[3]){
							case '1':	//积分活动
								$result = $this->model->common_activity_product($condition,$limit,$temp[$temp_id_num],4);
								$title  = "积分活动>>".$selector_title;
								break;
							case '3':	//拼团活动
								$result = $this->model->common_activity_product($condition,$limit,$temp[$temp_id_num],5);
								$title  = "拼团活动>>".$selector_title;
								break;
							case '4':	//众筹活动
								$result = $this->model->common_activity_product($condition,$limit,$temp[$temp_id_num],6);
								$title  = "众筹活动>>".$selector_title;
								break;
							case '5':	//砍价活动
								$result = $this->model->common_activity_product($condition,$limit,$temp[$temp_id_num],7);
								$title  = "砍价活动>>".$selector_title;
								break;
							case '6':	//礼包满赠活动
								$result = $this->model->common_activity_product($condition,$limit,$temp[$temp_id_num],8);
								$title  = "礼包满赠活动>>".$selector_title;
								break;
						}
						break;
				}
				break;
		}
		$res['title']  = $title;
		$res['result'] = $result; 
		json_out($res);
	}
	 
}
