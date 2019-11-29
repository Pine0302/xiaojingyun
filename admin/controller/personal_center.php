<?php


class control_personal_center extends control_base
{
	var $model;
	var $model_common;

	function __construct()
	{
        parent::__construct();
		//登录校验
		parent::check_login();
		//登录校验 End

		require_once('model/personal_center.php');
		$this->model = new model_personal_center();

		require_once('model/common.php');
		$this->model_common = new model_common();

    }

	/*
     * 个人中心自定义模板列表
     * 作者：lqh
	*/
    function diy_template_list()
    {
    	$customer_id    = $this->customer_id;
    	$customer_id_en = $this->customer_id_en;
		$pagenum        = $_GET["pagenum"];         //当前页数

		$data = array(
			'customer_id'	=>	$customer_id,
			'page'			=>	$pagenum,
		);
		$result_list = $this->model->select_personal_center_diy_template($data);

		//查看主题颜色
		$theme = $this->model_common->find_theme($customer_id);

		include('view/personal_center/personal_center_control.htm');

    }

	/*
     * 上架模板
     * 作者：lqh
	*/
	function on_carriage(){
		$diy_temid     = $_POST["diy_temid"];         //模板编号

		 if($diy_temid <= 0 || empty($diy_temid))
        {
            json_out(array('errcode' => 40002,'errmsg'=>'diy_id参数错误！'));
        }

		//先下架所有模板
		$value["is_used"]          = false;
		$condition["is_used"]      = true;
		$condition["customer_id"]  = $this->customer_id;
		$result = $this->model->update_personal_center_diy_template($condition,$value);

		//再上架当前模板
		unset($condition["is_used"]);
		$condition["id"]  = $diy_temid;
		$value["is_used"] = true;
		$result = $this->model->update_personal_center_diy_template($condition,$value);

		//插入日志
		$this->model->insert_log($this->customer_id,"上架模板，ID:".$diy_temid);

		json_out($result);
	}

	/*
     * 下架模板
     * 作者：lqh
	*/
	function under_carriage(){
		$diy_temid     = $_POST["diy_temid"];         //模板编号

		if($diy_temid <= 0 || empty($diy_temid))
        {
            json_out(array('errcode' => 40002,'errmsg'=>'diy_id参数错误！'));
        }

		$condition["id"]  = $diy_temid;
		$condition["customer_id"]  = $this->customer_id;
		$value["is_used"] = false;
		$result = $this->model->update_personal_center_diy_template($condition,$value);

		//插入日志
		$this->model->insert_log($this->customer_id,"下架模板，ID:".$diy_temid);

		json_out($result);
	}

	/*
     * 删除模板
     * 作者：lqh
	*/
	function del_template(){
		$diy_temid     = $_POST["diy_temid"];         //模板编号

		if($diy_temid <= 0 || empty($diy_temid))
        {
            json_out(array('errcode' => 40002,'errmsg'=>'diy_id参数错误！'));
        }

		$condition["id"]  = $diy_temid;
		$condition["customer_id"]  = $this->customer_id;
		$value["isvalid"] = false;
		$result = $this->model->update_personal_center_diy_template($condition,$value);

		//插入日志
		$this->model->insert_log($this->customer_id,"删除模板，ID:".$diy_temid);

		json_out($result);
	}

	/*
     * 修改模板名字
     * 作者：lqh
	*/
	function change_template_name(){
		$diy_temid= $_POST["diy_temid"];    //模板编号
		$name     = $_POST["name"];         //模板名字

		if(empty($name))
        {
            json_out(array('errcode' => 40002,'errmsg'=>'名字不能为空！'));
        }

		$condition["id"]  = $diy_temid;
		$condition["customer_id"]  = $this->customer_id;
		$value["name"] = $name;
		$result = $this->model->update_personal_center_diy_template($condition,$value);

		//插入日志
		$this->model->insert_log($this->customer_id,"更改模板名字，ID:".$diy_temid);

		json_out($result);
	}


	/*
     * 保存模板设置
     * 作者：lqh
	*/
	function save_template(){
		$diy_temid    = $_POST["diy_temid"];    //模板编号
		$name         = $_POST["name"];         //模板名字
		$bgcolor      = $_POST["bgcolor"];      //背景颜色
		$content      = $_POST["content"];      //模块顺序
		$customer_id  = $this->customer_id;

		if($diy_temid <= 0 || empty($diy_temid))
        {
            json_out(array('errcode' => 40002,'errmsg'=>'diy_id参数错误！'));
        }

		$condition["id"]  = $diy_temid;
		$condition["customer_id"]  = $customer_id;
		$value = array(
			"name"			=> $name,
			"bgcolor"		=> $bgcolor,
			"content"		=> $content,
		);
		$result = $this->model->update_personal_center_diy_template($condition,$value);

		//插入日志
		$this->model->insert_log($customer_id,"更新模板内容，ID:".$diy_temid);

		json_out($result);
	}

	/*
     * 编辑模板
     * 作者：lqh
	*/
	function edit_personal_center(){
		$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
		mysql_select_db(DB_NAME) or die('Could not select database');
		_mysql_query("SET NAMES UTF8");
		require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/common/utility.php');

		$customer_id    = $this->customer_id;
		$customer_id_en = $this->customer_id_en;
		$diy_temid      = (int)$_GET["diy_temid"];    //模板编号
		$template_id	= (int)$_GET['template_id'];  //模板样式编号

		$customarr[] = "";
		$name        = "自定义模板";
		$bgcolor     = "";
		$default_img = "";
		$action      = "";

		if($diy_temid > 0){
			$action = "edit";
			$template_arr   = $this->model->find_personal_center_diy_template($diy_temid,$customer_id);
			$result_content = $this->model->select_template_content($template_arr['content'],$customer_id);

			foreach($result_content AS $k=>$v){
				$content_arr = json_decode($v["content"],true);
				$customarr[$k]['type']	=	$v["type"];				//模板类型
				$customarr[$k]['diy_tem_contid'] = $v["diy_tem_contid"];
				if(isset($content_arr['title'])){
					$customarr[$k]['title'] = $content_arr['title'];
				}
				if(isset($content_arr['imgurl'])){
					$customarr[$k]['imgurl'] = $content_arr['imgurl'];
				}
				if(isset($content_arr['icon_pic'])){
					$customarr[$k]['icon_pic'] = $content_arr['icon_pic'];
				}
				if(isset($content_arr['color1'])){
					$customarr[$k]['color1'] = $content_arr['color1'];
				}
				if(isset($content_arr['data_num'])){
					$customarr[$k]['data_num'] = $content_arr['data_num'];
				}
				if(isset($content_arr['li_title'])){
					$customarr[$k]['li_title'] = $content_arr['li_title'];
				}
				if(isset($content_arr['foreign_id'])){				//固定链接ID
					$customarr[$k]['foreign_id'] = $content_arr['foreign_id'];
				}
				if(isset($content_arr['detail_id'])){				//产品ID
					$customarr[$k]['detail_id'] = $content_arr['detail_id'];
				}
				if(isset($content_arr['mod_padding'])){				//模块间间距
					$customarr[$k]['mod_padding'] = $content_arr['mod_padding'];
				}
				if(isset($content_arr['mod_img_padding'])){			//模块内间距
					$customarr[$k]['mod_img_padding'] = $content_arr['mod_img_padding'];
				}
				if(isset($content_arr['css_type'])){				//样式类型
					$customarr[$k]['css_type'] = $content_arr['css_type'];
				}
				if(isset($content_arr['pro_title_show'])){			//显示产品名字
					$customarr[$k]['pro_title_show'] = $content_arr['pro_title_show'];
				}
				if(isset($content_arr['pro_title_twoline'])){		//产品显示两行名字
					$customarr[$k]['pro_title_twoline'] = $content_arr['pro_title_twoline'];
				}
				if(isset($content_arr['pro_numshow'])){				//分类产品显示的数量
					$customarr[$k]['pro_numshow'] = $content_arr['pro_numshow'];
				}
				if(isset($content_arr['foot_position'])){			//底部菜单是否固定
					$customarr[$k]['foot_position'] = $content_arr['foot_position'];
				}
				if(isset($content_arr['placeholder'])){				//搜索框提示语
					$customarr[$k]['placeholder'] = $content_arr['placeholder'];
				}
				if(isset($content_arr['show_sale'])){				//显示销量
					$customarr[$k]['show_sale'] = $content_arr['show_sale'];
				}
				if(isset($content_arr['link_type'])){				//链接类型
					$customarr[$k]['link_type'] = $content_arr['link_type'];
				}
				if(isset($content_arr['select_value'])){			//固定链接名字
					$customarr[$k]['select_value'] = $content_arr['select_value'];
				}
				if(isset($content_arr['detail_value'])){			//产品的ID
					$customarr[$k]['detail_value'] = $content_arr['detail_value'];
				}
				if(isset($content_arr['detail_name'])){				//产品名字
					$customarr[$k]['detail_name'] = $content_arr['detail_name'];
				}
				if(isset($content_arr['bg_color'])){				//搜索框背景颜色
					$customarr[$k]['bg_color'] = $content_arr['bg_color'];
				}
				if(isset($content_arr['color'])){					//文字颜色
					$customarr[$k]['color'] = $content_arr['color'];
				}
				if(isset($content_arr['video_link'])){				//视频链接
					$customarr[$k]['video_link'] = $content_arr['video_link'];
				}
				if(isset($content_arr['rolling_direction'])){		//滚动方向
					$customarr[$k]['rolling_direction'] = $content_arr['rolling_direction'];
				}
				if(isset($content_arr['rolling_speed'])){			//滚动速度
					$customarr[$k]['rolling_speed'] = $content_arr['rolling_speed'];
				}
				if(isset($content_arr['show_time_limit'])){			//显示时间限制
					$customarr[$k]['show_time_limit'] = $content_arr['show_time_limit'];
				}
				if(isset($content_arr['city_name'])){				//城市广告绑定的城市
					$customarr[$k]['city_name'] = $content_arr['city_name'];
				}
				if(isset($content_arr['start_time'])){				//展示开始时间
					$customarr[$k]['start_time'] = $content_arr['start_time'];
				}
				if(isset($content_arr['end_time'])){				//展示结束时间
					$customarr[$k]['end_time'] = $content_arr['end_time'];
				}
				if(isset($content_arr['province'])){				//城市广告绑定的省份
					$customarr[$k]['province'] = $content_arr['province'];
				}
				if(isset($content_arr['sel_link_type'])){			//链接类型
					$customarr[$k]['sel_link_type'] = $content_arr['sel_link_type'];
				}
				if(isset($content_arr['link'])){					//链接
					$customarr[$k]['link'] = $content_arr['link'];
				}
				if(isset($content_arr['shop_type'])){				//商城类型
					$customarr[$k]['shop_type'] = $content_arr['shop_type'];
				}
				if(isset($content_arr['sort_type'])){				//排序类型
					$customarr[$k]['sort_type'] = $content_arr['sort_type'];
				}
				if(isset($content_arr['divide_type'])){				//划分类型
					$customarr[$k]['divide_type'] = $content_arr['divide_type'];
				}
				if(isset($content_arr['css_show'])){				//订单显示 样式选择是否显示
					$customarr[$k]['css_show'] = $content_arr['css_show'];
				}
				if(isset($content_arr['rs_member_id'])){			//数据显示 红积分会员卡选择
					$customarr[$k]['rs_member_id'] = $content_arr['rs_member_id'];
				}
			}
			$name        = $template_arr["name"];
			$bgcolor     = $template_arr["bgcolor"];
			$default_img = $template_arr["default_img"];
		}else{
			//新增模板
			$action = "add";
			$value = array(
				"customer_id"	=> $customer_id,
				"is_used"		=> false,
				"isvalid"		=> true,
				"createtime"	=> date("Y-m-d H:i:s",time()),
				"name"			=> "自定义模板",
				"bgcolor"		=> $bgcolor,
				"content"		=> "-1",
				"default_img"	=> $default_img,
			);
			$result = $this->model->insert_personal_center_diy_template($value);

			//插入日志
			$this->model->insert_log($customer_id,"新增自定义模板，ID:".$result);
			$diy_temid=$result;
			
			//添加模板样式内容
			
			if($template_id > 0){
				$res = $this->build_personal_center_template($result,$template_id);
				if($res['errcode'] > 0){
					json_out(array('errcode' => 6655,'errmsg'=>'添加模板内容失败'));
				}else{
					$condition["id"]  = $diy_temid;
					$condition["customer_id"]  = $customer_id;
					$value = array(
						"content"		=> $res['contid_arr'],
					);
					$result = $this->model->update_personal_center_diy_template($condition,$value);

					//插入日志
					$this->model->insert_log($customer_id,"更新模板内容，ID:".$diy_temid);
				}
				header("Location:/mshop/admin/index.php?m=personal_center&a=edit_personal_center&customer_id=".$customer_id_en."&diy_temid=".$diy_temid);
			}
		}



		/**获取链接内容 START**/
		// 获取礼包列表
		$sql = "SELECT package_name,id from package_list_t where customer_id='{$customer_id}' and isvalid=true ";
		$result = _mysql_query($sql) or die('Query failed1: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$package_id  =	$row->id;
			$package_name =	$row->package_name;
			$package_lists[] =	$package_id."_".$package_name;
		}


		//图文信息
		$imginfoLst = new ArrayList();
		$query = 'SELECT id,title FROM weixin_subscribes where isvalid=true and parent_id=-1 and is_message=0 and customer_id='.$customer_id;
		$result = _mysql_query($query) or die('Query failed2: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			  $sub_id =  $row->id ;
			  $title = $row->title;

			  $pstr = $sub_id."_".$title;
			  $imginfo[]=$sub_id."_".$title;
			  $imginfoLst->add($pstr);
		}

		$imginfosize = $imginfoLst->size();


		//优惠券 start
		$couponLst = new ArrayList();
		//只有普通优惠券
		$query = 'select id,is_open,title,NeedMoney,CanGetNum,Days,DaysType,class_type,user_scene from weixin_commonshop_coupons where isvalid=true and is_open=1  and customer_id='.$customer_id;
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			  $coupon_id =  $row->id ;
			  $title = $row->title;

			  $cstr = $coupon_id."_".$title;
			  $couponinfo[] = $coupon_id."_".$title;
			  $couponLst->add($cstr);
		}
		$couponsize = $couponLst->size();


		///城市商圈，渠道开关
		$is_cityarea=0;
		$is_cityarea_count=0;
		$query="select count(1) as is_cityarea_count from customer_funs cf inner join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and (c.sys_name='商圈-美食' or c.sys_name='商圈-外卖' or c.sys_name='商圈-金融保险' or c.sys_name='商圈-酒店' or c.sys_name='商圈-ktv' or c.sys_name='商圈-线下商城' or c.sys_name='商圈-金融管理' or c.sys_name='商圈-教练服务')";
		$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
		   $is_cityarea_count = $row->is_cityarea_count;
		}
		if($is_cityarea_count>0){
		   $is_cityarea=1;
		}

		$is_cityarea_caterer = 0;
		$is_cityarea_ktv     = 0;
		$is_cityarea_hotel   = 0;
		$is_cityarea_shop    = 0;
		$is_cityarea_finance = 0;
		$is_cityarea_coach = 0;
		if($is_cityarea){
			//城市商圈（美食），渠道开关
			$query="select count(1) as is_cityarea_caterer from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-美食'";
			$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $is_cityarea_caterer = $row->is_cityarea_caterer;
			}

			if($is_cityarea_caterer){
				//店铺数据
				$cityareaCatererLst = new ArrayList();
				$query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=2 and customer_id=".$customer_id;
				$result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
				while($supply_row = mysql_fetch_object($result)){
					$cityarea_id = $supply_row -> id;
					$cityarea_shop_name = $supply_row -> shop_name;

					$pstr = $cityarea_id."_".$cityarea_shop_name;
					$cityfood[]=$cityarea_id."_".$cityarea_shop_name;
					$cityareaCatererLst->add($pstr);
				}
				$cityareaCaterersize = $cityareaCatererLst->size();
				//店铺数据 End
				$cityarea_industry[]="2_美食_0";
			}
			//城市商圈（美食），渠道开关 End

			//城市商圈（KTV），渠道开关
			$query="select count(1) as is_cityarea_ktv from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-ktv'";
			$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $is_cityarea_ktv = $row->is_cityarea_ktv;
			}

			if($is_cityarea_ktv){
				//店铺数据
				$cityareaKTVLst = new ArrayList();
				$query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=30 and customer_id=".$customer_id;
				$result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
				while($supply_row = mysql_fetch_object($result)){
					$cityarea_id = $supply_row -> id;
					$cityarea_shop_name = $supply_row -> shop_name;

					$pstr = $cityarea_id."_".$cityarea_shop_name;
					$cityktv[] = $cityarea_id."_".$cityarea_shop_name;
					$cityareaKTVLst->add($pstr);
				}
				$cityareaKTVsize = $cityareaKTVLst->size();
				//店铺数据 End
				$cityarea_industry[]="2_KTV_1";
			}
			//城市商圈（KTV），渠道开关 End
			//
		// echo  $is_cityarea_ktv."===<br>";
		// var_dump($cityktv);
		// die;

			//城市商圈（酒店），渠道开关
			$query="select count(1) as is_cityarea_hotel from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-酒店'";
			$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $is_cityarea_hotel = $row->is_cityarea_hotel;
			}

			if($is_cityarea_hotel){
				//店铺数据
				$cityareaHotelLst = new ArrayList();
				$query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=60 and customer_id=".$customer_id;
				$result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
				while($supply_row = mysql_fetch_object($result)){
					$cityarea_id = $supply_row -> id;
					$cityarea_shop_name = $supply_row -> shop_name;

					$pstr = $cityarea_id."_".$cityarea_shop_name;
					$cityhotel[] = $cityarea_id."_".$cityarea_shop_name;
					$cityareaHotelLst->add($pstr);
				}
				$cityareaHotelsize = $cityareaHotelLst->size();
				//店铺数据 End
				$cityarea_industry[]="2_酒店_2";
			}
			//城市商圈（酒店），渠道开关 End

			//城市商圈（线下商城），渠道开关
			$query="select count(1) as is_cityarea_shop from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-线下商城'";
			$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $is_cityarea_shop = $row->is_cityarea_shop;
			}

			if($is_cityarea_shop){
				//店铺数据
				$cityareaShopLst = new ArrayList();
				$query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=20 and customer_id=".$customer_id;
				$result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
				while($supply_row = mysql_fetch_object($result)){
					$cityarea_id = $supply_row -> id;
					$cityarea_shop_name = $supply_row -> shop_name;

					$pstr = $cityarea_id."_".$cityarea_shop_name;
					$cityshop[] = $cityarea_id."_".$cityarea_shop_name;
					$cityareaShopLst->add($pstr);
				}
				$cityareaShopsize = $cityareaShopLst->size();
				//店铺数据 End
				$cityarea_industry[]="2_线下商城-首页_3";
				$cityarea_industry[]="2_线下商城-商家列表_4";
			}
			//城市商圈（线下商城），渠道开关 End

			//城市商圈（金融管理），渠道开关
			$query="select count(1) as is_cityarea_finance from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-金融管理'";
			$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $is_cityarea_finance = $row->is_cityarea_finance;
			}

			if($is_cityarea_finance){
				//城市商圈（金融）
				$cityarea_industry[]="2_金融-贷款_5";
				$cityarea_industry[]="2_金融-信用卡_6";
				$cityarea_industry[]="2_金融-保险_7";
				//城市商圈（金融） End
				$cityarea_industry[]="2_艺人服务_9";
			}

			//城市商圈（教练服务），渠道开关
			$query="select count(1) as is_cityarea_coach from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-教练服务'";
			$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			   $is_cityarea_coach = $row->is_cityarea_coach;
			}
			//城市商圈（教练服务），渠道开关 End

			if($is_cityarea_coach){
				$cityarea_industry[]="2_教练系统服务_8";
			}


		}


		//品牌供应商店铺
		$brandarr=[];//品牌供应商数据
		$isOpenBrandSupply=0;//是否开启品牌供应商
		$user_id=0;//供应商ID
		$is_coupon=0;//是否开启优惠券
		$brand_supply_name="";//供应商名称
		$check_brand="select isOpenBrandSupply,is_coupon from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
		$check_brand_result=_mysql_query($check_brand) or die ('check_brand faild ' .mysql_error());
		while($row=mysql_fetch_object($check_brand_result)){
			$isOpenBrandSupply=$row->isOpenBrandSupply;
			$is_coupon=$row->is_coupon;
		}
		if($isOpenBrandSupply){//开启品牌供应商就查询品牌供应商店铺信息
			$brand="select user_id,brand_supply_name from weixin_commonshop_brand_supplys where isvalid=true and brand_status=1 and customer_id=".$customer_id."";
			$brand_result=_mysql_query($brand) or die ('brand faild' .mysql_error());
			while($row=mysql_fetch_object($brand_result)){
				$user_id=$row->user_id;
				$brand_supply_name=$row->brand_supply_name;
				$brandarr[]=$user_id."_".$brand_supply_name;

			}

		}


		$is_f2c = 0;
		/* 查看f2c系统渠道开关 create by hzq */
		$query="select count(1) as is_f2c from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='F2C系统'";
		$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
		   $is_f2c = $row->is_f2c;
		}
		/* 查看f2c系统渠道开关 end */

		$is_ticket = 0;
		/* 查看票务系统渠道开关 start */
		$query="select count(1) as is_ticket from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='票务系统'";
		$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
		   $is_ticket = $row->is_ticket;
		}
		/* 查看票务系统渠道开关 end */
		/* 查看旅游卡渠道开关 start */
			$is_travelcard = 0;
			$query="select count(1) as is_travelcard from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='旅游卡'";
			$result = _mysql_query($query) or die('L274 is_travelcard Query failed: ' . mysql_error());  
			while ($row = mysql_fetch_object($result)) {
			   $is_travelcard = $row->is_travelcard;
			}	
		/* 查看旅游卡渠道开关 end */

		$fixedlink[]="-1_---------------请选择---------------";
		$fixedlink[]="-16_首页";
		$fixedlink[]="-6_全部产品";
		$fixedlink[]="-2_新品上市";
		$fixedlink[]="-3_热卖产品";
		$fixedlink[]="-4_购物车";
		$fixedlink[]="-8_个人中心";
		$fixedlink[]="-18_我的订单";
		$fixedlink[]="-9_我的微店";
		$fixedlink[]="-7_产品分类页1";
		$fixedlink[]="-17_产品分类页2";
		$fixedlink[]="-37_产品分类页3";
		$fixedlink[]="-47_产品分类页4";
		$fixedlink[]="-33_区域批发商列表";
		//$fixedlink[]="-5_限时抢购";
		$fixedlink[]="-10_商城在线客服";
		$fixedlink[]="-11_礼包列表";
		$fixedlink[]="-12_VP产品";
		$fixedlink[]="-15_积分专区";
		$fixedlink[]="-20_人气团列表";
		$fixedlink[]="-21_续费专区";
		$fixedlink[]="-22_电商直播";
		$fixedlink[]="-23_语音直播";
		if($is_ticket){
			$fixedlink[]="-24_票务特价机票";
			$fixedlink[]="-25_票务特价火车票";
		}
		if($is_f2c>0){
		$fixedlink[]="-26_F2C系统中心";
		}
		$fixedlink[]="-27_订货系统登录";
		$fixedlink[]="-28_订货系统申请";
		$fixedlink[]="-29_订货系统中心";
		$fixedlink[]="-100_门店申请";
		$fixedlink[]="-101_门店商城模式";
		$fixedlink[]="-102_门店店铺模式";
		$fixedlink[]="-19_拼团商品专区1";
		$fixedlink[]="-30_拼团商品专区2";
		$fixedlink[]="-31_拼团商品专区3";
		$fixedlink[]="-34_积分签到";
		$fixedlink[]="-35_积分商城";
		$fixedlink[]="-95_砍价活动";
		$fixedlink[]="-96_众筹新版";


		/* 线下商城产品分类 */
		$cityarea_shop_protype_arr = array();
		$query = "select id,name from weixin_cityarea_shop_types where is_shelves=1 and isvalid=true and customer_id=".$customer_id." order by asort desc";
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$st_id = $row->id;
			$st_name = $row->name;
			$shop_protype_str = $st_id."_".$st_name;
			$cityarea_shop_protype_arr[] = $shop_protype_str;
		}
		/* 线下商城产品分类 */

		/* 线下商城店铺分类 */
		$cityarea_shop_type_arr = array();
		$query = "select id,shoptype_name from weixin_cityarea_shop_shoptypes where customer_id=".$customer_id." and isvalid=true order by sort desc,id desc";
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$pt_id = $row->id;
			$pt_name = $row->shoptype_name;
			$shop_type_str = $pt_id."_".$pt_name;
			$cityarea_shop_type_arr[] = $shop_type_str;
		}
		/* 线下商城店铺分类 */

		/* 线下商城店铺列表 */
		$cityarea_shop_arr = array();
		$query = "select id,shop_name from weixin_cityarea_supply where customer_id=".$customer_id." and (types=20 or (types=21 and is_freeze = 0)) and isvalid=true";
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$cs_id = $row->id;
			$cs_name = $row->shop_name;
			$shop_str = $cs_id."_".$cs_name;
			$cityarea_shop_arr[] = $shop_str;
		}
		/* 线下商城店铺列表 */


		/*获取省、市*/
		$areaData		 = [];
		$area_id 		 = -1;
		$area_name 		 = '';
		$area_MergerName = '';
		$area_LevelType  = 1;
		$n = 0;
		$query_address = "select ID,name,MergerName,LevelType,ParentId from address where ID != 100000 and LevelType != 3 order by ID desc";
		$result_address = _mysql_query($query_address) or die('Query_address failed:'.mysql_error());
		while($row_address = mysql_fetch_object($result_address)){
			$area_id 		 = $row_address->ID;
			$area_name 		 = $row_address->name;
			$area_MergerName = $row_address->MergerName;
			$area_LevelType  = $row_address->LevelType;
			$area_ParentId 	 = $row_address->ParentId;

			$areaData[$n]['id'] 		= $area_id;
			$areaData[$n]['name'] 	  	= $area_name;
			$areaData[$n]['MergerName'] = $area_MergerName;
			$areaData[$n]['LevelType']  = $area_LevelType;
			$areaData[$n]['parentId']   = $area_ParentId;

			$n++;
		}
		//var_dump($areaData);
		/*获取省、市*/


		//微视直播房间
		$room_link = array();
		$query_weishi = "select r.id,r.title from weixin_os_room r inner join weixin_os_anchor a on r.anchor_id=a.id where r.isvalid=true and a.isvalid=true and a.customer_id=".$customer_id;
		$result_weishi = _mysql_query($query_weishi) or die('query_weishi failed:'.mysql_error());
		while( $row_weishi = mysql_fetch_object($result_weishi) ){
			$room_id 	     = $row_weishi -> id;		//模板id
			$room_title 	 = $row_weishi -> title;	//模板名称
			$room_link[] = $room_id."_".$room_title;
		}

		/*
		 * 查询所有已开启红积分的会员卡
		 * by whl ---18/3/14
		 */
		$red_score_member_link = array();
		$has_red_score_member  = -1;
		$query_card = "select wc.id,wc.name from weixin_cards wc INNER JOIN weixin_commonshop_red_score rs on wc.customer_id=rs.customer_id  where wc.isvalid=true and wc.is_red_score=1 and rs.is_red_score_open=1 and rs.customer_id=".$customer_id;
		$result_card = _mysql_query($query_card) or die('result_card failed: ' . mysql_error() .'<br /> query：'.$query_card);
		while($row = mysql_fetch_object($result_card)){
			$card_id  		    = $row->id;
			$red_name  		    = $row->name;
			$red_str            = $card_id.'_'.$red_name;
			$red_score_member_link[] = $red_str;
			$has_red_score_member = 1;
		}


		require_once($_SERVER['DOCUMENT_ROOT']."/weixinpl/common/utility_common.php");
		$shopLink = new shopLink_Utlity($customer_id);
		$link_arr = $shopLink->getSelectLink(array(3), 1);	//3：产品分类
		$type_arr = $link_arr['type_arr'];
		/**获取链接内容 END**/




		//print_r($customarr);
		//查看主题颜色
		$theme = $this->model_common->find_theme($customer_id);
		include('view/personal_center/personal_center.htm');
		mysql_close($link);
	}

	/*
     * 保存模块内容
     * 作者：hzq
	*/
	function save_template_content(){
		$DBlink = mysql_connect(DB_HOST,DB_USER,DB_PWD);
		mysql_select_db(DB_NAME) or die('Could not select database');
		_mysql_query("SET NAMES UTF8");

		require_once($_SERVER['DOCUMENT_ROOT'].'/mshop/web/model/integral.php');
		$model_integral = new model_integral();

		$diy_temid			=-1; //保存diy_template表的ID
		$diy_tem_contid		=-1; //模块的ID
		$type				=-1; //模块类型
		$title				=""; //文字标题
		$imgurl				=""; //图片地址
		$icon_pic			=""; //图片地址
		$link_str			="#";
		$color				="#333|#333|"; //文字颜色
		$color1				="#888|#888|"; //数字颜色
		$search_color		=""; //搜素栏背景颜色
		$foreign_id			=-1; //图文消息之类ID
		$detail_id			=-1; //商品ID
		$video_link			=""; //视频链接
		$mod_padding		=0; //模块间距
		$mod_img_padding	=0; //模块内图片间距
		$css_type			=""; //样式ID
		$pro_title_show		=0; // 是否显示产品标题
		$pro_title_twoline	=0; // 是否两行显示产品
		$pro_numshow		=2; // 显示产品数量
		$foot_position		=1; // 底部菜单样式，1-固定在底部，2-随页面移动
		$placeholder		=""; // 搜索框提示语
		$show_sale			=0;  // 是否显示销量
		$content			=-1; // diy_template表保存的模块顺序
		$type_id_2			=-1;
		$select_value		="";
		$detail_value		="";
		$detail_name		="";
		$name				="";
		$bgcolor			="";
		$op					=""; //ajax操作
		$link_type			=1;  //分类连接类型 ,1 为固定连接，2为分类，3为图文，4为城市商圈
		$rolling_direction	=""; //滚动公告栏滚动方向
		$rolling_speed		=""; //滚动公告栏滚动速度
		$show_time_limit	=""; //滚动公告栏每条公告显示时间
		$city_name			=""; //城市名
		$start_time			=""; //展示开始时间
		$end_time			=""; //展示结束时间
		$sel_link_type		=""; //链接类型，1：选择的链接，2：填写的链接
		$shop_type		    =0;  //商城类型: 0.线上商城 1.线下商城
		$sort_type			=0;  //排序类型: 0.按用户距商家距离从近到远排序 1.按商家销量从多到少排序
		$divide_type		=0;  //划分类型: 0.按产品分类 1.按店铺
		$data_num			= 4; //数据显示 样式1默认显示2个
		$rs_member_id		=""; //数据显示 红积分会员卡id  -1表示全部
		$customer_id 		= $this->customer_id;
		$customer_id_en     = $this->customer_id_en;

		if($_POST["diy_temid"]){
			$diy_temid	=	$_POST["diy_temid"];
		}
		if($_POST["diy_tem_contid"]){
			$diy_tem_contid	=	$_POST["diy_tem_contid"];
		}
		if($_POST["type"]){
			$type	=	$_POST["type"];
		}
		if($_POST["title"]){
			$title	=	$_POST["title"];
		}
		if($_POST["imgurl"]){
			$imgurl	=	$_POST["imgurl"];
			$imgurl=str_replace($new_baseurl,'',$imgurl);
		}
		if($_POST["icon_pic"]){
			$icon_pic	=	$_POST["icon_pic"];
			$icon_pic=str_replace($new_baseurl,'',$icon_pic);
		}
		if($_POST["select_value"]){
			$select_value	=	$_POST["select_value"];
		}
		if($_POST["detail_value"]){
			$detail_value	=	$_POST["detail_value"];
		}
		if($_POST["detail_name"]){
			$detail_name	=	$_POST["detail_name"];
		}
		if($_POST["data_num"]){
			$data_num	=	$_POST["data_num"];
		}
		if($_POST["rs_member_id"]){
			$rs_member_id = $_POST["rs_member_id"];
		}
		if($_POST["color"]){
			$color	=	$_POST["color"];
		}
		if($_POST["color1"]){
			$color1	=	$_POST["color1"];
		}
		if($_POST["search_color"]){
			$search_color	=	$_POST["search_color"];
		}
		if($_POST["foreign_id"]){
			$foreign_id	=	$_POST["foreign_id"];
		}

		if($_POST["detail_id"]){
			$detail_id	=	$_POST["detail_id"];
		}
		if($_POST["video_link"]){
			$video_link	=	$_POST["video_link"];
		}
		if($_POST["mod_padding"]){
			$mod_padding	=	$_POST["mod_padding"];
		}
		if($_POST["mod_img_padding"]){
			$mod_img_padding	=	$_POST["mod_img_padding"];
		}
		if($_POST["css_type"]){
			$css_type	=	$_POST["css_type"];
		}
		if(isset($_POST["pro_title_show"])){
			$pro_title_show	=	$_POST["pro_title_show"];
		}
		if(isset($_POST["pro_title_twoline"])){
			$pro_title_twoline	=	$_POST["pro_title_twoline"];
		}
		if($_POST["pro_numshow"]){
			$pro_numshow	=	$_POST["pro_numshow"];
		}
		if($_POST["foot_position"]){
			$foot_position	=	$_POST["foot_position"];
		}
		if($_POST["placeholder"]){
			$placeholder	=	$_POST["placeholder"];
		}
		if(isset($_POST["show_sale"])){
			$show_sale	=	$_POST["show_sale"];
		}
		if($_POST["content"]){
			$content	=	$_POST["content"];
			$content	=   ",".$content;
		}
		if($_POST["name"]){
			$name	=	$_POST["name"];
		}
		if($_POST["bgcolor"]){
			$bgcolor	=	$_POST["bgcolor"];
		}
		if($_POST["rolling_direction"]){
			$rolling_direction	=	$_POST["rolling_direction"];
		}
		if($_POST["rolling_speed"]){
			$rolling_speed	=	$_POST["rolling_speed"];
		}
		if($_POST["show_time_limit"]){
			$show_time_limit	=	$_POST["show_time_limit"];
		}
		if($_POST["city_name"]){
			$city_name	=	$_POST["city_name"];
		}
		if($_POST["start_time"]){
			$start_time	=	$_POST["start_time"];
		}
		if($_POST["end_time"]){
			$end_time	=	$_POST["end_time"];
		}
		if($_POST["province"]){
			$province	=	$_POST["province"];
		}
		if($_POST["sel_link_type"]){
			$sel_link_type	=	$_POST["sel_link_type"];
		}
		if($_POST["link"]){
			$link_str	=	$_POST["link"];
		}
		if($_POST["op"]){
			$op	=	$_POST["op"];
		}

		if($_POST["select_package_value"]){
			$select_package_value	=	$_POST["select_package_value"];
		}

		if($_POST["shop_type"]){
			$shop_type	=	$_POST["shop_type"];
		}

		if($_POST["sort_type"]){
			$sort_type	=	$_POST["sort_type"];
		}

		if($_POST["divide_type"]){
			$divide_type	=	$_POST["divide_type"];
		}
		if($_POST["li_title"]){
			$li_title	=	$_POST["li_title"];
		}
		if($_POST["css_show"]){
			$css_show	=	$_POST["css_show"];
		}

		/** 链接 start **/

		//sz_zpq
		$query_bargain="select id from ".wsy_shop.".kj_activity where isvalid=true and activity_status=2 and customer_id=".$customer_id." ORDER BY id desc limit 1";
		$result_bargain = _mysql_query($query_bargain) or die('Query_bargain failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result_bargain)) {
			$bargain_id = $row->id;
		}
		$query_crowdfund="select id from ".wsy_shop.".cr_activity where isvalid=true and status=2 and customer_id=".$customer_id." ORDER BY create_time desc limit 1";
		$result_crowdfund = _mysql_query($query_crowdfund) or die('Query_crowdfund failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result_crowdfund)) {
			$crowdfund_id = $row->id;
		}
		//sz_zpq

		//处理图片链接
		if($foreign_id>0)
		{
			$select_value=$foreign_id;
			$foreignarr=explode("_",$foreign_id);
			$foreign_id=$foreignarr[0];
			//线下商城如果选择的是'1_16'，即为选择全部，$foreign_id取-1
			if ($foreignarr[1] == 16){
				$foreign_id = -1;
			}
		}

		$selectarr[]="";
		$detailvaluearr[]="";
		$detailnamearr[]="";
		$sel_link_type_arr[]="";

		if($select_value){ //创建连接
			//$type_id_2	=	$configutil->splash_new($_POST["type_id_2"]);
			$link1[]="";
			$selectarr=explode("|",$select_value);
			$packagearr=explode("|",$select_package_value);
			$detailvaluearr=explode("|",$detail_value);
			$sel_link_type_arr=explode("|",$sel_link_type);
			$link_arr=explode("|",$link_str);

			for($i=0;$i<count($selectarr)-1;$i++){
				$link1[$i] = 'javascript:';
				if( $sel_link_type_arr[$i] == 1 ){

					if($selectarr[$i]>=0){
						$typestrarr= explode("_",$selectarr[$i]);
						$type_id_2 = $typestrarr[0];
						$link_type=$typestrarr[1];
						$type_id_3=$typestrarr[2];

						if($link_type==1){
							$product_detail_id_2 = $detailvaluearr[$i];
							if($product_detail_id_2>0){
								$link1[$i]="/weixinpl/mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_2;
							}else{
								$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_2;
								$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
								$typename="";
								while ($row3 = mysql_fetch_object($result3)) {
								   $typename = $row3->name;
								}
								$tcount = 0;	//子分类数量
								$query_type = "SELECT count(1) as tcount FROM weixin_commonshop_types WHERE customer_id=".$customer_id." AND parent_id=".$type_id_2." AND is_shelves=1 AND isvalid=true";
								$result_type = _mysql_query($query_type) or die('Query_type failed:'.mysql_error());
								while( $row_type = mysql_fetch_object($result_type) ){
									$tcount = $row_type -> tcount;
								}
								if( $tcount > 0 ){
									$link1[$i]="/weixinpl/mshop/proclass.php?customer_id=".$customer_id_en."&tid=".$type_id_2;
								}else{
									$link1[$i]="/weixinpl/mshop/list.php?customer_id=".$customer_id_en."&tid=".$type_id_2;
								}
							}
						}else if($link_type==2){
						   //图文
							$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_2;
							$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
							while ($row = mysql_fetch_object($result)) {
							   $website_url = $row->website_url;
							}
							$pos = strpos($website_url,"?");
							$pos2 = strpos($website_url,"single_id");
							if( $pos2 > 0 ){	//微官网单页链接
								$website_url = $website_url."&C_id=".$customer_id_en;
							} else {
								// if($pos>0){
								//    $website_url = $website_url."&customer_id=".$customer_id_en;
								// }else{
								//    $website_url = $website_url."?customer_id=".$customer_id_en;
								// }
							}
							$link1[$i] = $website_url;
						}else if($link_type==3){
						   //城市商圈-美食
							$link1[$i] = "/o2o/web/city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_2;
						}else if($link_type==4){
						   //商圈行业列表
							switch($type_id_3){
								case 0:
									$link1[$i] = "/o2o/web/city_area/cater/index.php?customer_id=".$customer_id_en;
									break;
								case 1:
									$link1[$i] = "/o2o/web/city_area/ktv/index.php?customer_id=".$customer_id_en;
									break;
								case 2:
									$link1[$i] = "/o2o/web/city_area/hotel/index.php?customer_id=".$customer_id_en;
									break;
								case 3:
									$link1[$i] = "/o2o/web/city_area/shop/index.php?customer_id=".$customer_id_en;
									break;
								case 4:
									$link1[$i] = "/o2o/web/city_area/shop/shop_list.php?customer_id=".$customer_id_en;
									break;
								case 5:
									$link1[$i] = "/o2o/web/city_area/finance2/loan/loanList.php?customer_id=".$customer_id_en;
									break;
								case 6:
									$link1[$i] = "/o2o/web/city_area/finance2/credit/index.php?customer_id=".$customer_id_en;
									break;
								case 7:
									$link1[$i] = "/o2o/web/city_area/finance2/insurance/insurance_list.php?customer_id=".$customer_id_en;
									break;
								case 8:
									$link1[$i] = "/addons/index.php/coach/Index/coach_index?customer_id=".$customer_id_en;
									break;
								case 9:
									$link1[$i] = "/weixinpl/yiren/front/web/index.html?customer_id_en=".$customer_id_en;
									break;
							}
						}else if($link_type==5){
						   //品牌供应商
							$link1[$i] = "/weixinpl/mshop/my_store/my_store.php?customer_id=".$customer_id_en."&supplier_id=".$type_id_2;
						}else if($link_type==6){
						   //城市商圈-ktv
							$link1[$i] = "/o2o/web/city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_2;
						}else if($link_type==7){
						   //城市商圈-酒店
							$link1[$i] = "/o2o/web/city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_2;
						}else if($link_type==8){
						   //城市商圈-线下商城
							$link1[$i] = "/o2o/web/city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_2;
						}else if($link_type==60){
						   //优惠券
							if($type_id_2==0 or empty($type_id_2)){
								$link1[$i] = "/weixinpl/mshop/coupons_center.php?customer_id=".$customer_id_en;
							}else{
								$link1[$i] = "/weixinpl/mshop/coupons_center.php?customer_id=".$customer_id_en."&cp_id=".$type_id_2;
							}
						}else if($link_type==9){
						   //微视直播系统
							$link1[$i] = "/weixin/plat/app/index.php/Mshopzhibo/show_room/customer_id/".$customer_id."/room_id/".$type_id_2;
						}else if($link_type==10){
						   //已启用的模板
							$link1[$i] = "index.php?customer_id=".$customer_id_en."&diy_template_id=".$type_id_2;
						}


					}else{
						switch($selectarr[$i]){
							case -6:
								$link1[$i]="/weixinpl/mshop/list.php?customer_id=".$customer_id_en;
								break;
							case -2:
								$link1[$i]="/weixinpl/mshop/list.php?isnew=1&customer_id=".$customer_id_en;
								break;
							case -3:
								$link1[$i]="/weixinpl/mshop/list.php?ishot=1&customer_id=".$customer_id_en;
								break;
							case -4:
								$link1[$i]="/weixinpl/mshop/order_cart.php?customer_id=".$customer_id_en;
								break;
							case -7:
								$link1[$i]="/weixinpl/mshop/class_page.php?customer_id=".$customer_id_en;
								break;
							case -8:
								$link1[$i]="/weixinpl/mshop/personal_center.php?customer_id=".$customer_id_en;
								break;
							case -9:
								$link1[$i]="/weixinpl/mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
								break;
							case -5:
								$link1[$i]="/weixinpl/mshop/snap_up.php?customer_id=".$customer_id_en;
								break;
							case -33:
								$link1[$i]="/weixinpl/mshop/wholesalers_list.php?customer_id=".$customer_id_en;
								break;
							case -10:
								$link1[$i]="/weixinpl/online/show_online.php?customer_id=".$customer_id_en;
								break;
							case -11:
								$typestrarr= explode("_",$selectarr[$i]);
								$type_id_2 = $typestrarr[0];
								$link_type=$typestrarr[1];
								if($link_type){
									$link1[$i]="/weixinpl/mshop/product_detail_gift.php?package_id={$link_type}";
								}else{
									$link1[$i]="/weixinpl/mshop/package_list.php?customer_id=".$customer_id_en;
								}
								break;
							case -12:
								$link1[$i]="/weixinpl/mshop/list.php?isvp=1&customer_id=".$customer_id_en;
								break;
							case -15:
								$link1[$i]="/weixinpl/mshop/list.php?isscore=1&customer_id=".$customer_id_en;
								break;
							case -16:
								$link1[$i]="/weixinpl/common_shop/jiushop/index.php?customer_id=".$customer_id_en;
								break;
							case -17:
								$link1[$i]="/weixinpl/mshop/proclass.php?customer_id=".$customer_id_en;
								break;
							case -18:
								$link1[$i]="/weixinpl/mshop/orderlist.php?customer_id=".$customer_id_en;
								break;
							case -19:
								$link1[$i]="/market/web/collageActivities/product_list_view.php?customer_id=".$customer_id_en."&op=ordinary";
								break;
							case -20:
								$link1[$i]="/market/web/collageActivities/product_list_view.php?customer_id=".$customer_id_en."&op=popularity";
								break;
							case -21:
								$link1[$i]="/market/web/promoter_renew/index.php?customer_id=".$customer_id_en;
								break;
							case -22:
								$link1[$i]="/addons/index.php/micro_broadcast/user/index?customer_id=".$customer_id_en;
								break;
							case -23:
								$link1[$i]="/addons/index.php/voice_online/Index/index?customer_id=".$customer_id_en;
								break;
							case -24:
								$link1[$i]="/weixinpl/ticke_check.php?type=flight";
								break;
							case -25:
								$link1[$i]="/weixinpl/ticke_check.php?type=train";
								break;
							case -26:
								$link1[$i]="/addons/index.php/f2c/index/personal_center?customer_id=".$customer_id_en;
								break;
							case -27:
								$link1[$i]="/addons/index.php/ordering_retail/Proxy/proxy_login?customer_id=".$customer_id_en;
								break;
							case -28:
								$link1[$i]="/addons/index.php/ordering_retail/Proxy/proxy_apply?customer_id=".$customer_id_en;
								break;
							case -29:
								$link1[$i]="/addons/index.php/ordering_retail/Proxy/personal_center.html?customer_id=".$customer_id_en;
								break;
							case -30:
								$link1[$i]="/market/web/collageActivities/product_list_view.php?op=ordinary2&customer_id=".$customer_id_en;
								break;
							case -31:
								$link1[$i]="/market/web/collageActivities/product_list_view.php?op=ordinary3&customer_id=".$customer_id_en;
								break;
							case -34:
								$link1[$i]=$model_integral->integral_sign_url.$customer_id_en;
								break;
							case -35:
								$link1[$i]=$model_integral->integral_shop_url.$customer_id_en;
								break;
							case -37:
								$link1[$i]="/weixinpl/mshop/class_page3.php?customer_id=".$customer_id_en;
								break;
							case -47:
								$link1[$i]="/weixinpl/mshop/class_page4.php?customer_id=".$customer_id_en;
								break;
							case -95:
								$link1[$i]="/market/web/haggling/web/index.html?customer_id_en=".$customer_id_en."&activity_id=".$bargain_id;
								break;
							case -96:
								//$link1[$i]="/weixinpl/sustain/front/web/index.html?customer_id_en=".$customer_id_en."&activity_id=".$crowdfund_id;
								$link1[$i]="/weixinpl/sustain/back/index.php/Workroom_admin/crowdfund/index_list.html?customer_id_en=".$customer_id_en;
								break;
							case -100:
								$link1[$i]="/addons/index.php/ordering_retail/Proxy/apply_shop.html?customer_id=".$customer_id_en;
								break;
							case -101:
								$link1[$i]="/addons/index.php/ordering_retail/Shop/nearby_shop.html?customer_id=".$customer_id_en;
								break;
							case -102:
								$link1[$i]="/addons/index.php/ordering_retail/Shop/shop_list.html?customer_id=".$customer_id_en;
								break;
							default:
								$link1[$i]="javascript:";
								break;
						}
					}
				} else {
					if( $link_arr[$i] == '' ){
						$link1[$i] = "javascript:";
					} else {
						$link1[$i] = $link_arr[$i];
					}
				}
				$link=implode("|",$link1);


			}

		}

		/** 链接 end **/


		$temp = array();
		switch($op){
			case "add_mod":		//添加模板
		/*		$temp = array(
					'title' 			=> $title,
					'imgurl' 			=> $imgurl,
					'foreign_id' 		=> $foreign_id,
					'detail_id' 		=> $detail_id,
					'video_link' 		=> $video_link,
					'mod_padding' 		=> $mod_padding,
					'mod_img_padding' 	=> $mod_img_padding,
					'css_type' 			=> $css_type,
					'pro_title_show' 	=> $pro_title_show,
					'pro_title_twoline' => $pro_title_twoline,
					'pro_numshow' 		=> $pro_numshow,
					'foot_position' 	=> $foot_position,
					'placeholder' 		=> $placeholder,
					'show_sale' 		=> $show_sale,
					'link_type' 		=> $link_type,
					'rolling_direction' => $rolling_direction,
					'rolling_speed' 	=> $rolling_speed,
					'show_time_limit' 	=> $show_time_limit,
					'city_name' 		=> $city_name,
					'start_time' 		=> $start_time,
					'end_time' 			=> $end_time,
					'province' 			=> $province,
					'sel_link_type' 	=> $sel_link_type,
					'shop_type' 		=> $shop_type,
					'sort_type' 		=> $sort_type,
					'divide_type' 		=> $divide_type,
					'select_value' 		=> $select_value,
				);

		*/
				switch($type){
					case '7':			//分割线
						$temp = array(
							'title'	 	  		=> $title,
							'mod_padding'		=> $mod_padding,
							'imgurl'	 		=> $imgurl,
							'select_value'	 	=> $select_value,
							//'detail_value'	 	=> $detail_value,
							//'detail_name'	 	=> $detail_name,
							'link'				=> $link,
							'sel_link_type'		=> $sel_link_type,
						);
						break;
					case '13':			//滚动公告栏
						$temp = array(
							'title'	 	  		=> $title,
							'mod_padding'		=> $mod_padding,
							'icon_pic'	 		=> $icon_pic,
							'rolling_speed'	 	=> $rolling_speed,
							'rolling_direction'	=> $rolling_direction,
							'show_time_limit'	=> $show_time_limit,
							'link'				=> $link,
							'sel_link_type'		=> $sel_link_type,
						);
						break;
					case '17':			//个人中心头部
						$temp = array(
							'title'	 	  => $title,
							'mod_padding' => $mod_padding,
							'icon_pic'	  => $icon_pic,
							'css_type'	  => $css_type,
						);
						break;
					case '21':			//订单显示
						$temp = array(
							'mod_padding' => $mod_padding,
							'li_title'	  => $li_title,
							'select_value'=> '商城订单',
							'imgurl'	  => $imgurl,
							'icon_pic'	  => $icon_pic,
							'title'		  => $title,
							'foreign_id'  => $foreign_id,
							'css_type'	  => $css_type,
							'css_show'	  => 1,
						);
						break;
					case '22':			//数据显示
						$temp = array(
							'title'		    => $title,
							'imgurl'	    => $imgurl,
							'select_value'  => $select_value,
							'sel_link_type' => $sel_link_type,
							'css_type'	    => $css_type,
							'color'		    => $color,
							'color1'	    => $color1,
							'mod_padding'   => $mod_padding,
							'data_num'		=> $data_num,
							'rs_member_id'  => $rs_member_id,
						);
						break;
					case '23':			//功能模块
						$temp = array(
							'title'		    => $title,
							'imgurl'	    => $imgurl,
							'select_value'  => $select_value,
							'sel_link_type' => $sel_link_type,
							'css_type'	    => $css_type,
							'color'		    => $color,
							'mod_padding'   => $mod_padding,
							'data_num'		=> $data_num,
						);
						break;
					case '24':			//功能模块
						$temp = array(
							'title'		    => $title,
							'imgurl'	    => $imgurl,
							'select_value'  => $select_value,
							'color'		    => $color,
							'mod_padding'   => $mod_padding,
							'css_show'	  	=> 1,
						);
						break;
				}

				$value = array(
					'diy_temid'			=> $diy_temid,
					'diy_tem_contid'	=> $diy_tem_contid,
					'type'				=> $type,
					'createtime'		=> date('Y-m-d H:i:s',time()),
					'isvalid'			=> true,
					'customer_id'		=> $customer_id,
					'content'			=> json_encode($temp,JSON_UNESCAPED_UNICODE),
				);
				$return_msg = $this->model->insert_personal_center_diy_template_content($value);
				json_out($return_msg);
				break;
			case "update_mod":		//更新模板内容
				//查询当前contain_id属于的模块编号
				$result = $this->model->select_content_type($customer_id,$diy_tem_contid);
				switch($result['type']){
					case '7':			//分割线
						$temp = array(
							'title'	 	  		=> $title,
							'mod_padding'		=> $mod_padding,
							'imgurl'	 		=> $imgurl,
							'select_value'	 	=> $select_value,
							//'detail_value'	 	=> $detail_value,
							//'detail_name'	 	=> $detail_name,
							'link'				=> $link,
							'sel_link_type'		=> $sel_link_type,
						);
						break;
					case '13':			//滚动公告栏
						$temp = array(
							'title'	 	  		=> $title,
							'mod_padding'		=> $mod_padding,
							'icon_pic'	 		=> $icon_pic,
							'select_value'	 	=> $select_value,
							'detail_value'	 	=> $detail_value,
							'detail_name'	 	=> $detail_name,
							'rolling_speed'	 	=> $rolling_speed,
							'rolling_direction'	=> $rolling_direction,
							'show_time_limit'	=> $show_time_limit,
							'link'				=> $link,
							'sel_link_type'		=> $sel_link_type,
						);
						break;
					case '17':			//个人中心头部
						$temp = array(
							'title'		  => $title,
							'mod_padding' => $mod_padding,
							'icon_pic'	  => $icon_pic,
							'css_type'	  => $css_type,
						);
						break;
					case '21':		//订单显示
						$temp = array(
							'mod_padding' => $mod_padding,
							'li_title'	  => $li_title,
							'select_value'=> $select_value,
							'imgurl'	  => $imgurl,
							'icon_pic'	  => $icon_pic,
							'title'		  => $title,
							'foreign_id'  => $foreign_id,
							'css_type'	  => $css_type,
							'css_show'	  => $css_show,
						);
						break;
					case '22':		//数据显示
						$temp = array(
							'css_type'	  	=> $css_type,
							'color'		  	=> $color,
							'color1'	  	=> $color1,
							'mod_padding' 	=> $mod_padding,
							'title'		    => $title,
							'imgurl'	    => $imgurl,
							'select_value'  => $select_value,
							'sel_link_type' => $sel_link_type,
							'data_num'		=> $data_num,
							'rs_member_id'  => $rs_member_id,
						);
						break;
					case '23':		//功能模块
						$temp = array(
							'css_type'	  	=> $css_type,
							'color'		  	=> $color,
							'color1'	  	=> $color1,
							'mod_padding' 	=> $mod_padding,
							'title'		    => $title,
							'imgurl'	    => $imgurl,
							'select_value'  => $select_value,
							'sel_link_type' => $sel_link_type,
							'data_num'		=> $data_num,
						);
						break;
					case '24':			//功能模块
						$temp = array(
							'title'		    => $title,
							'imgurl'	    => $imgurl,
							'select_value'  => $select_value,
							'color'		    => $color,
							'mod_padding'   => $mod_padding,
							'css_show'	  	=> $css_show,
						);
						break;
				}
				$condition['isvalid'] = true;
				$condition['diy_tem_contid'] = $diy_tem_contid;
				$condition['customer_id'] = $customer_id;

				$value['content'] = json_encode($temp,JSON_UNESCAPED_UNICODE);
                var_dump($value);
				$return_msg = $this->model->update_personal_center_diy_template_content($value,$condition);
				json_out($return_msg);
				break;
			case "del_mod":			//删除模板内容
				$condition['isvalid'] = true;
				$condition['diy_tem_contid'] = $diy_tem_contid;
				$condition['customer_id'] = $customer_id;

				$value['isvalid'] = false;
				$return_msg = $this->model->update_personal_center_diy_template_content($value,$condition);
				json_out($return_msg);
				break;
		}
		mysql_close($DBlink);
	}

	function select_diy_function(){
		$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
		mysql_select_db(DB_NAME) or die('Could not select database');
		_mysql_query("SET NAMES UTF8");
		require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/common/utility.php');

		$customer_id = $this->customer_id;
		$query_custom = "select id,subscribe_id,need_score,imgurl from weixin_commonshop_subscribes where isvalid=true  and customer_id=".$customer_id." ORDER BY id desc";
		$result_custom = _mysql_query($query_custom) or die('Query failed912: ' . mysql_error());

			//自定义功能
		$imgurl ='';
		$arr = array();
		while ($row_c = mysql_fetch_object($result_custom)) {
			$temp = array();

			$cs_id = $row_c->id;
			$subscribe_id = $row_c->subscribe_id;
			$need_score = $row_c->need_score;
			$imgurl =$row_c->imgurl;
			if($imgurl == '' ){

				$imgurl='/weixinpl/mshop/images/info_image/function.png';
			}
			$imgurl = $new_baseurl.$imgurl;
			$query = "SELECT id,title,website_url,coupon_id FROM weixin_subscribes where  id=".$subscribe_id;
			$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
			$website_url="";
			$title="";
			while ($row = mysql_fetch_object($result)) {
				$website_url = $row->website_url;
				$title = $row->title;
				$coupon_id = $row->coupon_id;
			}
			if($coupon_id>0){
				continue;
			}
			$pos = strpos($website_url,"?");
			if($pos>0){
				$website_url = $website_url."&C_id=".$customer_id."&fromuser=".$weixin_fromuser;
			}else{
				$website_url = $website_url."?C_id=".$customer_id."&fromuser=".$weixin_fromuser;
			}
			$mppos= strstr($title,"{weixin_title}");
			if(!empty($mppos)){
				$title = str_replace("{weixin_title}",$weixin_name,$title);
			}
				$mppos= strstr($title,"{weixin_parent_title}");
			if(!empty($mppos) and $parent_id>0){
				$query="select weixin_name from weixin_users where  isvalid=true and id=".$parent_id." limit 0,1";
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$parent_weixin_name="";
				while ($row = mysql_fetch_object($result)) {
					$parent_weixin_name = $row->weixin_name;
				}
					$title = str_replace("{weixin_parent_title}",$parent_weixin_name,$title);
			}
			array_push($temp,$title,$imgurl);
			array_push($arr,$temp);
		}
		mysql_close($link);
		json_out($arr);
	}
	
	//新建模板选择页
	public function choose_personal_center_template(){
		include('view/personal_center/personal_center_control.htm');
	}
	
	//生成模板
	public function build_personal_center_template($diy_temid,$template_id){
		$return_msg['errcode'] = 0;
		$return_msg['errmsg']  = 'success';
		$customer_id = $this->customer_id;
		$contid_arr = "";
		switch($template_id){
			case '1':					//模板一
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '17','{\"title\":\"个人中心头部|\",\"mod_padding\":0,\"icon_pic\":\"\",\"css_type\":\"1\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '22','{\"css_type\":\"2\",\"color\":\"#333|#333|\",\"color1\":\"#888|#888|\",\"mod_padding\":0,\"title\":\"我的零钱|消费总额|\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|\",\"select_value\":\"零钱|消费总额|\",\"sel_link_type\":\"1|1|\",\"data_num\":\"2\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '24','{\"title\":\"商城订单|收银O2O|到店付|\",\"imgurl\":\"/weixinpl/mshop/images/info_image/s_order.png|/weixinpl/mshop/images/info_image/s_order.png|/weixinpl/mshop/images/info_image/s_order.png|\",\"select_value\":\"商城订单|线下收银订单|到店付订单|\",\"color\":\"#333|#333|#333|\",\"mod_padding\":\"10\",\"css_show\":null}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '23','{\"css_type\":\"2\",\"color\":\"#333|#333|#333|#333|#333|\",\"color1\":\"#888|#888|\",\"mod_padding\":\"10\",\"title\":\"收货地址|我的资产|我的团队|我的特权|我的二维码|我的名片|\",\"imgurl\":\"/weixinpl/mshop/images/info_image/wode_fahuodizhi.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/data-icon.png|/weixinpl/mshop/images/info_image/wode_tuandui.png|/weixinpl/mshop/images/info_image/wode_quanxian.png|/weixinpl/mshop/images/info_image/wode_qrcode.png|/weixinpl/mshop/images/icon-bs-card.png|\",\"select_value\":\"收货地址|我的资产|我的团队|我的特权|二维码|我的名片|\",\"sel_link_type\":\"1|1|1|1|1|1|\",\"data_num\":\"4\"}')";
				break;
			case '2':					//模板二
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '17','{\"title\":\"个人中心头部|\",\"mod_padding\":0,\"icon_pic\":\"\",\"css_type\":\"4\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '13','{\"title\":\"滚动公告栏|\",\"mod_padding\":0,\"icon_pic\":\"\",\"rolling_speed\":\"10\",\"rolling_direction\":\"1\",\"show_time_limit\":\"1\",\"link\":\"javascript:\",\"sel_link_type\":\"1|\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '22','{\"css_type\":\"1\",\"color\":\"#333|#333|#333|#333|\",\"color1\":\"#888|#888|#888|#888|\",\"mod_padding\":0,\"title\":\"零钱|积分|购物币|消费总额|\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|\",\"select_value\":\"零钱|会员卡积分|购物币|消费总额|\",\"sel_link_type\":\"1|1|1|1|\",\"data_num\":\"4\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '21','{\"mod_padding\":\"10\",\"li_title\":\"商城订单\",\"select_value\":\"商城订单\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png|\",\"icon_pic\":\"/weixinpl/mshop/images/info_image/s_order.png\",\"title\":\"待付款|待发货|待收货|待评价|售后中|\",\"foreign_id\":\"商城订单\",\"css_type\":\"1\",\"css_show\":\"1\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '23','{\"css_type\":\"1\",\"color\":\"#333|#333|#333|#333|\",\"color1\":\"#888|#888|\",\"mod_padding\":\"10\",\"title\":\"大礼包|VP产品|积分专区|续费专区|\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon2.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon1.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon3.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon4.png|\",\"select_value\":\"大礼包|VP产品|积分专区|续费专区|\",\"sel_link_type\":\"1|1|1|1|\",\"data_num\":\"4\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '23','{\"css_type\":\"1\",\"color\":\"#333|#333|#333|#333|#333|#333|#333|#333|\",\"color1\":\"#888|#888|\",\"mod_padding\":\"10\",\"title\":\"我的资产|我的特权|我的团队|累积收益|收货地址|我的微店|我的店铺|二维码|我的名片|\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/data-icon.png|/weixinpl/mshop/images/info_image/wode_quanxian.png|/weixinpl/mshop/images/info_image/wode_tuandui.png|/weixinpl/mshop/images/info_image/wode_shouyi.png|/weixinpl/mshop/images/info_image/wode_fahuodizhi.png|/weixinpl/mshop/images/info_image/wode_weidian.png|/weixinpl/mshop/images/info_image/wode_dianfu.png|/weixinpl/mshop/images/info_image/wode_qrcode.png|/weixinpl/mshop/images/icon-bs-card.png|\",\"select_value\":\"我的资产|我的特权|我的团队|累积收益|收货地址|我的微店|我的店铺|二维码|我的名片|\",\"sel_link_type\":\"1|1|1|1|1|1|1|1|1|\",\"data_num\":\"3\"}')";
				break;
			case '3':					//模板三
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '17','{\"title\":\"个人中心头部|\",\"mod_padding\":0,\"icon_pic\":\"\",\"css_type\":\"2\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '22','{\"css_type\":\"1\",\"color\":\"#333|#333|#333|\",\"color1\":\"#888|#888|#888|\",\"mod_padding\":0,\"title\":\"购物币|积分|消费总额|\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|\",\"select_value\":\"购物币|会员卡积分|消费总额|\",\"sel_link_type\":\"1|1|1|\",\"data_num\":\"3\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '21','{\"mod_padding\":\"10\",\"li_title\":\"商城订单\",\"select_value\":\"商城订单\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png|\",\"icon_pic\":\"/weixinpl/mshop/images/info_image/s_order.png\",\"title\":\"待付款|待发货|待收货|待评价|售后中|\",\"foreign_id\":\"商城订单\",\"css_type\":\"1\",\"css_show\":\"1\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '23','{\"css_type\":\"1\",\"color\":\"#333|#333|#333|#333|\",\"color1\":\"#888|#888|\",\"mod_padding\":\"10\",\"title\":\"我的资产|我的特权|我的团队|累积收益|\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/data-icon.png|/weixinpl/mshop/images/info_image/wode_quanxian.png|/weixinpl/mshop/images/info_image/wode_tuandui.png|/weixinpl/mshop/images/info_image/wode_shouyi.png|\",\"select_value\":\"我的资产|我的特权|我的团队|累积收益|\",\"sel_link_type\":\"1|1|1|1|\",\"data_num\":\"4\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '7','{\"title\":\"分割线|\",\"mod_padding\":0,\"imgurl\":\"/mshop/admin/Base/personalization/personal_center/images/temp0.jpg|\",\"select_value\":\"1_我的资产|\",\"link\":\"javascript:\",\"sel_link_type\":\"1|\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '23','{\"css_type\":\"1\",\"color\":\"#333|#333|#333|#333|#333|#333|#333|#333|\",\"color1\":\"#888|#888|\",\"mod_padding\":0,\"title\":\"大礼包|VP产品|积分专区|续费专区|电商直播|语音直播|拼团专区|限时抢购|我的名片|\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon2.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon1.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon3.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon4.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon7.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon8.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon5.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon6.png|/weixinpl/mshop/images/icon-bs-card.png|\",\"select_value\":\"大礼包|VP产品|积分专区|续费专区|电商直播|语音直播|拼团专区|限时抢购|我的名片|\",\"sel_link_type\":\"1|1|1|1|1|1|1|1|1|\",\"data_num\":\"4\"}')";
				break;
			case '4':					//模板四
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '17','{\"title\":\"个人中心头部|\",\"mod_padding\":0,\"icon_pic\":\"\",\"css_type\":\"1\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '22','{\"css_type\":\"1\",\"color\":\"#333|#333|#333|\",\"color1\":\"#888|#888|#888|\",\"mod_padding\":0,\"title\":\"购物币|积分|消费总额|\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png|\",\"select_value\":\"购物币|会员卡积分|消费总额|\",\"sel_link_type\":\"1|1|1|\",\"data_num\":\"3\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '21','{\"mod_padding\":\"10\",\"li_title\":\"商城订单\",\"select_value\":\"商城订单\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png|/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png|\",\"icon_pic\":\"/weixinpl/mshop/images/info_image/s_order.png\",\"title\":\"待付款|待发货|待收货|待评价|售后中|\",\"foreign_id\":\"商城订单\",\"css_type\":\"1\",\"css_show\":\"1\"}')";
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '23','{\"css_type\":\"1\",\"color\":\"#333|#333|#333|#333|#333|#333|#333|#333|#333|#333|\",\"color1\":\"#888|#888|\",\"mod_padding\":0,\"title\":\"我的资产|我的特权|我的导师|我的团队|累积收益|收货地址|二维码|我的微店|签到|会员卡券|我的名片|\",\"imgurl\":\"/weixinpl/back_newshops/Base/personalization/personal_center/images/data-icon.png|/weixinpl/mshop/images/info_image/wode_quanxian.png|/weixinpl/mshop/images/info_image/wode_sprite_03.png|/weixinpl/mshop/images/info_image/wode_tuandui.png|/weixinpl/mshop/images/info_image/wode_shouyi.png|/weixinpl/mshop/images/info_image/wode_fahuodizhi.png|/weixinpl/mshop/images/info_image/wode_qrcode.png|/weixinpl/mshop/images/info_image/wode_weidian.png|/weixinpl/mshop/images/info_image/wode_sprite_09.png|/weixinpl/mshop/images/info_image/vipcard.png|/weixinpl/mshop/images/icon-bs-card.png|\",\"select_value\":\"我的资产|我的特权|我的导师|我的团队|累积收益|收货地址|二维码|我的微店|签到|微信卡券|我的名片|\",\"sel_link_type\":\"1|1|1|1|1|1|1|1|1|1|1|\",\"data_num\":\"4\"}')";
				break;
			case '5':					//空白模板
				$sql[] = "INSERT INTO ".WSY_SHOP.".personal_center_diy_template_content (diy_temid, diy_tem_contid, createtime, isvalid, customer_id, type, content) VALUES ('".$diy_temid."', '".date('YmdHis',time()).rand(100,999)."', '".date('Y-m-d H:i:s',time())."',true,'".$customer_id."', '17','{\"title\":\"个人中心头部|\",\"mod_padding\":0,\"icon_pic\":\"\",\"css_type\":\"1\"}')";
				break;
			default:
				break;
		}
		if(!empty($sql)){
			foreach($sql as $v){
				$res = $this->model->insert_template_content($v);
				if($res['errcode'] > 0){
					$return_msg['errcode'] = $res['errcode'];
					$return_msg['errmsg']  = $res['errmsg'];
					break;
				}else{
					$contid_arr .= $res['diy_tem_contid'].',';
				}
			}
			$return_msg['contid_arr'] = $contid_arr;
		}
		return $return_msg;
	}
}
