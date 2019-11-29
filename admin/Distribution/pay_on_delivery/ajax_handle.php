<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require_once('../../../../weixinpl/function_model/collageActivities.php');
require_once('../../../../weixinpl/common/utility_shop.php');
$collageActivities = new collageActivities($customer_id);
$shopMessage_Utlity = new shopMessage_Utlity();

$op = $configutil->splash_new($_POST['op']);

switch( $op ){
	case 'edit_product_recommendation':	//修改团推荐产品状态
		$status = $configutil->splash_new($_POST['status']);
		$query = "UPDATE collage_activities_product_recommendation_set_t SET is_open=".$status." WHERE customer_id=".$customer_id." AND isvalid=true";
		_mysql_query($query) or die('Query failed:'.mysql_error());
		
		die(true);
	break;
	
	case 'save_product_recommendation':	//保存团推荐设置
		$data['recommendation_id'] 	= $configutil->splash_new($_POST['recommendation_id']);
		$data['pattern'] 			= $configutil->splash_new($_POST['pattern']);
		if( $data['pattern'] == 1 ){
			$data['system_set_id'] 	= $configutil->splash_new($_POST['system_set_id']);
			$data['num'] 			= $configutil->splash_new($_POST['num']);
			$data['type'] 			= $configutil->splash_new($_POST['type']);
			$data['style'] 			= $configutil->splash_new($_POST['style']);
			$data['sort'] 			= $configutil->splash_new($_POST['sort']);
		}
		$system_set_id = $collageActivities -> addProductRecommendationSet($data);
		
		die(json_encode($system_set_id));
	break;
	
	case 'get_all_active_product':	//获取所有活动关联产品
		$limitstart 	= $configutil->splash_new($_POST['limitstart']);
		$limitend 		= $configutil->splash_new($_POST['limitend']);
		$search_pid 	= $configutil->splash_new($_POST['search_pid']);
		$search_pname 	= $configutil->splash_new($_POST['search_pname']);
		$search_aid 	= $configutil->splash_new($_POST['search_aid']);
		$search_aname 	= $configutil->splash_new($_POST['search_aname']);
		$search_atype 	= $configutil->splash_new($_POST['search_atype']);
		$pid_str 		= $configutil->splash_new($_POST['pid_str']);
		$is_count 		= $configutil->splash_new($_POST['is_count']);
		
		$data['count'] = 0;
		$data['product'] = [];
		$query_product = "SELECT
								cgpt.activitie_id,
								cgpt.pid,
								cgpt.price,
								cgpt.number AS pnumber,
								cat.name AS aname,
								cat.type,
								cat.start_time,
								cat.end_time,
								cat.group_size,
								cat.number AS anumber,
								wcp.name AS pname
					FROM collage_group_products_t AS cgpt 
					LEFT JOIN collage_activities_t AS cat ON cgpt.activitie_id=cat.id 
					LEFT JOIN weixin_commonshop_products AS wcp ON cgpt.pid=wcp.id 
					WHERE cgpt.isvalid=true AND cgpt.status=1 AND cat.isvalid=true AND wcp.isvalid=true AND wcp.isout=false AND wcp.customer_id=".$customer_id." ";
		if( $pid_str != '' ){
			$query_product .= " AND cgpt.pid not in(".$pid_str.") ";
		}
		if( $search_pid != '' ){
			//$query_product .= " AND cgpt.pid like '%".$search_pid."%'";
			$query_product .= " AND cgpt.pid like".$search_pid." ";
		}
		if( $search_pname != '' ){
			$query_product .= " AND wcp.name like '%".$search_pname."%' ";
		}
		if( $search_aid != '' ){
			$query_product .= " AND cgpt.activitie_id=".$search_aid." ";
		}
		if( $search_aname != '' ){
			$query_product .= " AND cat.name like '%".$search_aname."%' ";
		}
		if( $search_atype > 0 ){
			$query_product .= " AND cat.type=".$search_atype." ";
		}
		if( $is_count ){
			$result = _mysql_query($query_product) or die('Query_count failed:'.mysql_error());
			$data['count'] = mysql_num_rows($result);
		}
		$query_product .= " ORDER BY activitie_id ASC LIMIT ".$limitstart.",".$limitend;
		$result_product = _mysql_query($query_product) or die('Query_product failed:'.mysql_error());
		while( $row_product = mysql_fetch_assoc($result_product) ){
			$row_product['aname'] = htmlspecialchars($row_product['aname']);
			$data['product'][] = $row_product;
		}
		
		die(json_encode($data));
	break;
	
	case 'get_recommendation_product':	//获取团推荐关联产品
		$limitstart = $configutil->splash_new($_POST['limitstart']);
		$limitend 	= $configutil->splash_new($_POST['limitend']);
		$pid_str 	= $configutil->splash_new($_POST['pid_str']);
		$is_count 	= $configutil->splash_new($_POST['is_count']);
		
		$filed = " carpt.id,carpt.is_out,cgpt.pid,cgpt.price,cgpt.createtime,cgpt.stock,cat.name AS aname,cat.type,cat.start_time,cat.end_time,cat.group_size,cat.number,wcp.name AS pname,wcp.sell_count,wcp.storenum,wcp.isnew,wcp.ishot,wcp.type_ids,wcp.default_imgurl,wcp.is_virtual,wcp.is_free_shipping,wcp.is_currency ";
		
		$filed_count = " count(1) AS pcount ";
		
		$condition = array('cgpt.isvalid'=>true,'cgpt.status'=>1,'cat.isvalid'=>true,'wcp.isvalid'=>true,'carpt.isvalid'=>true);
		
		$data['count'] = 0;
		$data['product'] = [];
		if( !empty($pid_str) ){
			$condition['carpt.pid'] = "IN (".$pid_str.")";
		}
		if( $is_count ){
			$data['count'] = $collageActivities -> get_recommendation_product($condition,$filed_count)['data'][0]['pcount'];
		}
		if( $limitstart != '' && $limitend != '' ){
			$condition['limit'] = " LIMIT ".$limitstart.",".$limitend;
		}
		$data['product'] = $collageActivities -> get_recommendation_product($condition,$filed)['data'];
		// var_dump($data['product'][0]);
		
		die(json_encode($data));
	break;
	
	case 'add_product_relation':	//团推荐自定义模式添加产品
		$recommendation_id 	= $configutil->splash_new($_POST['recommendation_id']);
		$pid_arr 			= $_POST['pid_arr'];
		$activitie_id_arr 	= $_POST['activitie_id_arr'];
		$pid_arr_num 		= count($pid_arr);
		
		$query_ins = "INSERT INTO collage_activities_recommendation_product_t(recommendation_id,createtime,isvalid,pid,is_out,activitie_id) VALUES ";
		$query_ins_v = "";
		for( $i = 0; $i < $pid_arr_num; $i++ ){
			$query_ins_v .= " (".$recommendation_id.",now(),true,".$pid_arr[$i].",false,".$activitie_id_arr[$i]."),";
		}
		$query_ins_v = substr($query_ins_v,0,-1);
		$query_ins .= $query_ins_v;
		_mysql_query($query_ins) or die('Query_ins failed:'.mysql_error());
		
		die(json_encode(mysql_insert_id()));
	break;
	
	case 'del_recommendation_product':	//删除团推荐产品
		$recommendation_id 	= $configutil->splash_new($_POST['recommendation_id']);
		$pid 				= $configutil->splash_new($_POST['pid']);
		
		$query = "DELETE FROM collage_activities_recommendation_product_t WHERE pid=".$pid." AND recommendation_id=".$recommendation_id;
		_mysql_query($query) or die('Query failed:'.mysql_error());
		
		die(true);
	break;
	
	case 'edit_recommendation_product_status':	//团推荐产品发布与下架
		$recommendation_id 	= $configutil->splash_new($_POST['recommendation_id']);
		$pid 				= $configutil->splash_new($_POST['pid']);
		$status 			= $configutil->splash_new($_POST['status']);
		
		$query = "UPDATE collage_activities_recommendation_product_t SET is_out=".$status." WHERE pid=".$pid." AND recommendation_id=".$recommendation_id;
		_mysql_query($query) or die('Query failed:'.mysql_error());
		
		die(true);
	break;
	
	case 'explain'://修改拼团说明发布状态
		$keyid    = $configutil->splash_new($_POST["keyid"]);
		$status   = $configutil->splash_new($_POST["status"]);
		$before_statu = 1 == $status?2:1;
		$query = "update collage_activities_explain_t set status=".$before_statu." where isvalid=true and customer_id=".$customer_id." and id=".$keyid;
		$error = 0;
		_mysql_query($query) or die('W21 Query failed: ' . mysql_error());
		$error = mysql_error();
		if($error>0){
			$data['code'] = "10001";
			$data['tips'] = "修改失败";
		}else{
			$data['code'] = "1";
			$data['before_statu'] = $before_statu;
			$data['tips'] = "修改成功";
		}
		echo json_encode($data);;
	break;
	
	case 'save_recommendation_activity'://修改拼团说明发布状态
		$keyid     = $configutil->splash_new($_POST["keyid"]);
		$num       = $configutil->splash_new($_POST["num"]);
		$type      = $configutil->splash_new($_POST["type"]);
		$sort_type = $configutil->splash_new($_POST["sort_type"]);
		$sort      = $configutil->splash_new($_POST["sort"]);
		
		$addArray = array('num'=>$num,'type'=>$type,'sort_type'=>$sort_type,'sort'=>$sort);
		
		$result = $collageActivities->addGroupRecommendation($customer_id,$addArray,$keyid);
		
		echo json_encode($result);
	break;
	
	case 'save_activity_explanation'://修改拼团说明
		$keyid     = $configutil->splash_new($_POST["keyid"]);
		$content   = $configutil->splash_new($_POST["content"]);
		
		$result = $collageActivities->addExplain($customer_id,$content,$keyid);
		
		echo json_encode($result);
	break;
	
	case 'end_pro'://修改活动产品状态
		$keyid     = $configutil->splash_new($_POST["keyid"]);
		$pid   = $configutil->splash_new($_POST["pid"]);
		
		$query = "UPDATE collage_group_products_t SET status=2 WHERE isvalid=true AND pid=".$pid." AND activitie_id=".$keyid;
		_mysql_query($query) or die('Query failed:'.mysql_error());
		$result = mysql_affected_rows();
		$data['code'] = "10001";
		$data['tips'] = "修改失败";
		if( $result > 0 ){
			$data['code'] = "1";
			$data['tips'] = "修改成功";
		}
		echo json_encode($data);
	break;
	
	case 'rand_lottery';
		$keyid = $configutil->splash_new($_POST["keyid"]);
		$pid   = $configutil->splash_new($_POST["pid"]);
		$draw_num = $configutil->splash_new($_POST["draw_num"]);
		
		$filed = 'ot.id AS group_id,wu.name,wu.weixin_name,ot.total_price,ot.join_num,ot.createtime,ot.success_num,ot.status AS group_status,at.start_time,at.end_time';
		$condition = array(
			'at.isvalid'=>true,
			'ot.status'=>4,
			'ot.isvalid'=>true,
			'ot.pid'=>$pid,
			'ot.activitie_id'=>$keyid,
			'LIMIT'=>$draw_num,
		);
		$data = $collageActivities->select_raffle_list($condition,$filed);
		
		echo json_encode($data);
	break;
	
	case 'release':	//活动发布
		$id = $configutil->splash_new($_POST['id']);
		$query = "UPDATE collage_activities_t SET status=2 WHERE customer_id=".$customer_id." AND isvalid=true AND id=".$id;
		_mysql_query($query) or die('Query failed:'.mysql_error());
		$result = mysql_affected_rows();
		die(json_encode($result));
	break;
	
	case 'stop':	//活动终止
		$id = $configutil->splash_new($_POST['id']);
		$type = $configutil->splash_new($_POST['type']);
		$query = "UPDATE collage_activities_t SET status=3 WHERE customer_id=".$customer_id." AND isvalid=true AND id=".$id;
		_mysql_query($query) or die('Query failed:'.mysql_error());
		$result = mysql_affected_rows();
		
		if( $type == 2 ){//抽奖团类型，产品进入待抽奖状态
			$query_product = "UPDATE collage_group_products_t SET status=3 WHERE  activitie_id=".$id." AND isvalid=true";
		}else{
			$query_product = "UPDATE collage_group_products_t SET status=2 WHERE  activitie_id=".$id." AND isvalid=true";
			
		}
		_mysql_query($query_product) or die('Query_product failed:'.mysql_error());
		
		die(json_encode($result));
	break;
	
	case 'get_all_product':	//活动添加产品获取产品
		$search_pid = $configutil->splash_new($_POST['search_pid']);
		$search_pname = $configutil->splash_new($_POST['search_pname']);
		$search_supply_id = $configutil->splash_new($_POST['search_supply_id']);
		$search_ptype = $configutil->splash_new($_POST['search_ptype']);
		$search_pfrom = $configutil->splash_new($_POST['search_pfrom']);
		$search_ptag = $configutil->splash_new($_POST['search_ptag']);
		$pid_str = $configutil->splash_new($_POST['pid_str']);
		$del_pid_str = $configutil->splash_new($_POST['del_pid_str']);
		$limitstart = $configutil->splash_new($_POST['limitstart']);
		$limitend = $configutil->splash_new($_POST['limitend']);
		$is_count = $configutil->splash_new($_POST['is_count']);
		
		$data['count'] = 0;
		$data['product'] = [];
		$query_ing = "SELECT cgpt.pid FROM collage_group_products_t AS cgpt LEFT JOIN collage_activities_t AS cat ON cgpt.activitie_id=cat.id WHERE cgpt.isvalid=true AND cat.isvalid=true AND cat.status!=4 AND cat.status!=3";
		if( $del_pid_str != '' ){
			$query_ing .= " AND cgpt.pid NOT IN (".$del_pid_str.")";
		}

		$result_ing = _mysql_query($query_ing) or die('Query_child failed:'.mysql_error());
		while( $row_ing = mysql_fetch_assoc($result_ing) ) {
			$ing_pid[] = $row_ing['pid'];
		}

		//删除已经添加的商品
		$query_check = "select pid from pay_on_delivery_products_t where customer_id=".$customer_id." AND isvalid=true";
		$check_result = _mysql_query($query_check) or die('Query failed:'.mysql_error());
		while( $check_row = mysql_fetch_assoc($check_result) ){
			$ing_pid[] = $check_row['pid'];
		}

		$ing_pids = implode(',',$ing_pid);

		$query = "SELECT
						id,
						name,
						type_ids,
						orgin_price,
						now_price,
						storenum,
						sell_count,
						default_imgurl,
						isnew,
						ishot,
						is_virtual,
						is_free_shipping,
						issnapup,
						is_currency,
						createtime
					FROM weixin_commonshop_products
					WHERE customer_id=".$customer_id." AND isvalid=true AND isout=false ";

		//zhou			
		if($ing_pids != '')
		{
			$query .= " AND id NOT IN (".$ing_pids.")";
		}
		if( $search_pid > 0 ){
			$query .= " AND id like '%".$search_pid."%'";
			//$query .= " AND id=".$search_pid;
		}
		if( !empty($search_pname) ){
			$query .= " AND name like '%".$search_pname."%'";
		}
		if( $search_supply_id > 0 ){
			$query .= " AND is_supply_id=".$search_supply_id;
		}
		if( $search_ptype > 0 ){
			$typeson_id=array();
			/* 查找该分类的所有子分类 start */
			$query_child = "SELECT id FROM weixin_commonshop_types WHERE customer_id=".$customer_id." AND isvalid=true AND is_shelves=1 AND LOCATE(',".$search_ptype.",', gflag)>0 ";
			$result_child = _mysql_query($query_child) or die('Query_child failed:'.mysql_error());
			while( $row_child = mysql_fetch_object($result_child) ){
				$child_id = $row_child -> id;
				
				$typeson_id[] = $child_id;
			}
			/* 查找该分类的所有子分类 end */
			
			if(empty($typeson_id)){
				$typeson_id = $search_ptype; 
			}else{
				array_push($typeson_id,$search_ptype);
				$typeson_id = implode(',',$typeson_id);
			}
			
			$query .= " and (";
			$typeson_id_arr = explode(",",$typeson_id);
			$typeson_id_count = count($typeson_id_arr);
			for( $j=0; $j<$typeson_id_count; $j++ ){
				$o_typeid = $typeson_id_arr[$j];
				if( $j == 0 ){
					$query .= "( LOCATE(',".$o_typeid.",', type_ids)>0)";
				}else{
					$query .= " or (LOCATE(',".$o_typeid.",', type_ids)>0)";
				}
			}
			$query .= ")";
		}
		if( $search_pfrom == 1 ){
			$query .= " AND is_supply_id=-1";
		}
		if( $search_pfrom == 2 ){
			$query .= " AND is_supply_id>0";
		}
		switch( $search_ptag ){
			case 1:
				$query .= " AND ishot=1";
			break;
			case 2:
				$query .= " AND isnew=1";
			break;
			case 3:
				$query .= " AND is_free_shipping=1";
			break;
			case 4:
				$query .= " AND is_virtual=1";
			break;
			case 5:
				$query .= " AND is_currency=1";
			break;
		}
		if( $pid_str != '' ){
			$query .= " AND id NOT IN(".$pid_str.")";
		}
		if( $is_count ){
			$result = _mysql_query($query) or die('Query count failed:'.mysql_error());
			$count = mysql_num_rows($result);
			$data['count'] = $count;
		}
		$query .= " ORDER BY asort_value DESC,id DESC ";
		if( $limitstart != '' && $limitend != '' ){
			$query .= " LIMIT ".$limitstart.",".$limitend;
		}
		$result = _mysql_query($query) or die('Query failed:'.mysql_error());
		while( $row = mysql_fetch_assoc($result) ){
			$type_name = '';
			$type_ids = trim($row['type_ids'],',');
			if( $type_ids != '' ){
				$query_type = "SELECT name FROM weixin_commonshop_types WHERE customer_id=".$customer_id." AND isvalid=true AND id IN (".$type_ids.")";
				$result_type = _mysql_query($query_type) or die('Query_type failed:'.mysql_error());
				while( $row_type = mysql_fetch_object($result_type) ){
					$type_name .= $row_type->name."/";
				}
				$type_name = substr($type_name,0,-1);
			}

			//删除已经添加的商品
			$query_check = "select pid from pay_on_delivery_products_t where customer_id=".$customer_id." AND isvalid=true";
			$check_result = _mysql_query($query_check) or die('Query failed:'.mysql_error());
			while( $check_row = mysql_fetch_assoc($check_result) ){
				$check_pid = $check_row['pid'];
				if($row['id'] == $check_pid){
					unset($row);
				}
			}
			if(!empty($row)) {
				$row['type_name'] = $type_name;
				$data['product'][] = $row;
			}
		}
		die(json_encode($data));
	break;
	
	case 'select_add_activitie_product':	//获取活动页添加产品的信息
		$willAddproductId = $configutil->splash_new($_POST['willAddproductId']);
		
		$data = [];
		$query = "SELECT id,name,orgin_price,now_price,cost_price,for_price FROM weixin_commonshop_products WHERE id IN(".$willAddproductId.")";
		$result = _mysql_query($query) or die('Query failed:'.mysql_error());
		while( $row = mysql_fetch_assoc($result) ){
			$data[] = $row;
		}
		die(json_encode($data));
	break;
	
	case 'save_activity_info':	//保存活动信息
		$data['activity']['id']				= $configutil->splash_new($_POST['keyid']);
		$data['activity']['name']			= $configutil->splash_new($_POST['name']);
		$data['activity']['type']			= $configutil->splash_new($_POST['type']);
		$data['activity']['luck_draw_num'] 	= $configutil->splash_new($_POST['luck_draw_num']);
		$data['activity']['start_time'] 	= $configutil->splash_new($_POST['start_time']);
		$data['activity']['end_time']		= $configutil->splash_new($_POST['end_time']);
		$data['activity']['group_size']		= $configutil->splash_new($_POST['group_size']);
		$data['activity']['user_level'] 	= $configutil->splash_new($_POST['user_level']);
		$data['activity']['number'] 		= $configutil->splash_new($_POST['number']);
		$data['product_info'] 				= $_POST['product_info'];
		$data['delPidStr'] 					= $configutil->splash_new($_POST['delPidStr']);
		$data['addPidArr'] 					= $_POST['addPidArr'];
		if( $data['activity']['id'] == -1 ){
			$data['activity']['customer_id'] = $customer_id;
			$data['activity']['isvalid'] = true;
			$data['activity']['status'] = 1;
			$data['activity']['createtime'] = 'now()';
		}
		
		$result = $collageActivities -> change_group_products_t($data,$data['activity']['id']);
		die(json_encode($result));
	break;
	
	case 'submit_lottery':
		$data = array();
		$data['code'] = 0;
		$data['msg'] = '';
		$keyid = $configutil->splash_new($_POST['keyid']);
		$lottery = $configutil->splash_new($_POST['lottery']);
		$pid = $configutil->splash_new($_POST['pid']);
		$lottery = json_decode($lottery,true);
		$str = '';
		foreach( $lottery as $key => $val ){
			$str .= ','.$val;
		}
		$str = trim($str,',');
		/*中奖团订单改变状态*/
		$condition = array(
				'activitie_id'=>$keyid,	
				'customer_id'=>$customer_id,		
				'pid'=>$pid,		
				'isvalid'=>true,
				'id'=>" in (".$str.")"
					);
		$value = array(
					'status'=>'3',
					'is_win'=>'1'
					);
		_mysql_query("BEGIN");
		$result1 = $collageActivities ->update_group_order($condition,$value);
		$result4 = $collageActivities ->update_crew_order(array('group_id'=>" in (".$str.")",'status'=>2),array('status'=>5));
		
		//中奖团的订单改为有效的商城订单
		$condition = array(
					'group_id_in' => $str,
					'ccot.status' => 5,
					'ccot.isvalid' => true
		);
		$filed = " ccot.batchcode,ccot.user_id,wu.weixin_fromuser,ccopmt.pname ";
		$success_batchcode_info = $collageActivities -> get_pay_batchcode_info($condition,$filed)['data'];
		$success_batchcode_arr = [];
		$success_user_id_arr = [];
		$weixin_fromuser = [];
		$product_name = [];
		foreach( $success_batchcode_info as $k => $v ){
			$success_batchcode_arr[] = $v['batchcode'];
			$success_user_id_arr[] = $v['user_id'];
			$weixin_fromuser[$v['user_id']] = $v['weixin_fromuser'];
			$product_name[] = $v['pname'];
		}
		
		$query_qr = "SELECT is_QR,supply_id FROM weixin_commonshop_orders WHERE batchcode='".$success_batchcode_arr[0]."' LIMIT 1";
		$result_qr = _mysql_query($query_qr) or die('Query_qr failed:'.mysql_error());
		$result_order = mysql_fetch_assoc($result_qr);
		$is_QR = $result_order['is_QR'];	//是否二维码核销产品订单
		$supply_id = $result_order['supply_id'];	//供应商id
		
		//获取供应商openid
		$supply_openid = '';
		if ( $supply_id > 0 ) {
			$query_supply = "SELECT weixin_fromuser FROM weixin_users WHERE id=".$supply_id." AND isvalid=true";
			$result_supply = _mysql_query($query_supply) or die('Query_supply failed:'.mysql_error());
			while ( $row_supply = mysql_fetch_object($result_supply) ) {
				$supply_openid = $row_supply -> weixin_fromuser;
			}
		}
		
		$success_batchcode_str = implode(',',$success_batchcode_arr);
		$query_isvalid = "UPDATE weixin_commonshop_orders SET is_collageActivities=1 WHERE batchcode IN (".$success_batchcode_str.")";
		_mysql_query($query_isvalid) or die('Query_isvalid failed:'.mysql_error());
		
		foreach( $success_batchcode_arr as $k => $v ){
			$user_id = $success_user_id_arr[$k];
			$fromuser = $weixin_fromuser[$user_id];
			
			//推送抽奖成功消息
			$lottery_msg = "亲，您参加的 ".$product_name[$k]." 抽奖团  中奖啦\r\n".
								"时间：".date("Y-m-d H:i:s")."";
			$shopMessage_Utlity -> SendMessage($lottery_msg,$fromuser,$customer_id);
			
			//如果是二维码核销产品则自动发货
			if( $is_QR == 1 ){
				$shopMessage_Utlity -> GetQR($v,$fromuser,$customer_id);
				
				$descript = "商家已发货";
				$query_log = "INSERT INTO weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) values('".$v."',4,'".$descript."','".$fromuser."',now(),1)";
				_mysql_query($query_log);
				
				//更改订单状态
				$query_order_up = "UPDATE weixin_commonshop_orders SET sendstatus=1 WHERE customer_id=".$customer_id." AND batchcode='".$v."' AND isvalid=true";
				_mysql_query($query_order_up);
				
				$query_orderp_up = "UPDATE weixin_commonshop_order_prices SET sendstatus=1 WHERE customer_id=".$customer_id." AND batchcode='".$v."' AND isvalid=true";
				_mysql_query($query_orderp_up);
			}
		}
		
		if ( $supply_id > 0 ) {
			$content = "亲，".$product_name[0]." 抽奖团 拼团成功，请及时发货\r\n".
							"时间：".date( "Y-m-d H:i:s")."";
			$shopMessage_Utlity -> SendMessage($content,$supply_openid,$customer_id);
		}
		
		/*未中奖团订单改变状态*/
		$condition = array(
				'activitie_id'=>$keyid,	
				'customer_id'=>$customer_id,		
				'isvalid'=>true,
				'pid'=>$pid,
				'status'=>4,
				'id'=>" not in (".$str.")"
					);
		$value = array(
					'status'=>'2',
					'is_win'=>'2'
					);
		
		$result2 = $collageActivities ->update_group_order($condition,$value);
		
		//获取失败团的id
		$group_id_arr = [];
		$query_group_id = "SELECT id FROM collage_group_order_t WHERE status=2 AND pid=".$pid." AND isvalid=true AND activitie_id=".$keyid." AND customer_id=".$customer_id;
		$result_group_id = _mysql_query($query_group_id) or die('Query_group_id failed:'.mysql_error());
		while( $row_group_id = mysql_fetch_object($result_group_id) ){
			$group_id_arr[] = $row_group_id -> id;
		}
		if( !empty($group_id_arr) ){
			$group_id_str = implode(',',$group_id_arr);
			
			$result5 = $collageActivities ->update_crew_order(array('group_id'=>" in (".$group_id_str.")",'activitie_id'=>$keyid,'isvalid'=>true,'customer_id'=>$customer_id,'status'=>2),array('status'=>3));
			$result6 = $collageActivities ->update_crew_order(array('group_id'=>" in (".$group_id_str.")",'activitie_id'=>$keyid,'isvalid'=>true,'customer_id'=>$customer_id,'status'=>1),array('status'=>4)); 
			$result7 = $collageActivities ->update_crew_order(array('group_id'=>" in (".$group_id_str.")",'activitie_id'=>$keyid,'isvalid'=>true,'customer_id'=>$customer_id,'status'=>5),array('status'=>3)); 
		}
			
		
		/*更改产品状态*/
		$query = "UPDATE collage_group_products_t SET status=4 WHERE activitie_id=".$keyid." AND isvalid=true AND pid=".$pid."";
		$result3 = _mysql_query($query) or die('Query failed:'.mysql_error());
		
		if( $result1['code'] == 0 && $result2['code'] == 0 && $result4['code'] == 0 && $result3 ){
			$sql = "SELECT COUNT(1) AS ccount FROM collage_group_order_t WHERE status=4 AND isvalid=true AND activitie_id=".$keyid." LIMIT 1";
			$result = _mysql_query($query) or die('Query failed:'.mysql_error());
			$ccount = mysql_fetch_assoc($result);
			if($ccount['ccount'] == 0){
				$sql = "UPDATE collage_activities_t SET status=3 WHERE isvalid=true AND activitie_id=".$keyid."";
				_mysql_query($query) or die('Query failed:'.mysql_error());
			}
			
			//重新统计拼团成功和拼团失败的数量
			//成功团数
			$scount = 0;
			$query_success = "SELECT COUNT(1) AS scount FROM collage_group_order_t WHERE status=3 AND isvalid=true AND activitie_id=".$keyid." AND pid=".$pid;
			$result_success = _mysql_query($query_success) or die('Query_success failed:'.mysql_error());
			while( $row_success = mysql_fetch_object($result_success) ){
				$scount = $row_success -> scount;
			}
			//失败团数
			$fcount = 0;
			$query_fail = "SELECT COUNT(1) AS fcount FROM collage_group_order_t WHERE status=2 AND isvalid=true AND activitie_id=".$keyid." AND pid=".$pid;
			$result_fail = _mysql_query($query_fail) or die('Query_fail failed:'.mysql_error());
			while( $row_fail = mysql_fetch_object($result_fail) ){
				$fcount = $row_fail -> fcount;
			}
			
			$query_product = "UPDATE collage_group_products_t SET total_success=".$scount.",total_fail=".$fcount." WHERE activitie_id=".$keyid." AND isvalid=true AND pid=".$pid;
			_mysql_query($query_product) or die('Query_product failed:'.mysql_error());
			
			_mysql_query("COMMIT");
			
			$data['msg'] = '操作成功';
		}else{
			_mysql_query("ROLLBACK");
			$data['msg'] = '操作失败';
		}
		echo json_encode($data);
	break;
	
	case 'get_group_mes':
		$keyid = $configutil->splash_new($_POST['keyid']);
		$pid = $configutil->splash_new($_POST['pid']);
		$page = $configutil->splash_new($_POST['page']);
		$start = ($page - 1) * 10;
		$filed = "ot.type,ot.id AS group_id,ot.head_id,ot.success_num,ot.price,ot.join_num,ot.total_price,ot.status AS group_status,ot.pid,ot.createtime,wu.name,wu.weixin_name,wu.weixin_headimgurl,mp.name as pname,mp.id as pid";	
		$condition = array('at.isvalid'=>true,'ot.isvalid'=>true,'ot.activitie_id'=>$keyid,'ot.pid'=>$pid,'ot.status'=>'4','LIMIT'=>' LIMIT '.$start.',10');
		$list = $collageActivities->get_group_order($condition,$filed);
		echo json_encode($list);
	break;
	
	case 'changeGroupStatus':		
		$is_open   = $configutil->splash_new($_POST["is_open"]);
		$keyid     = $configutil->splash_new($_POST["keyid"]);
		
		$addArray = array('is_open'=>$is_open);
		
		$result = $collageActivities->addGroupRecommendation($customer_id,$addArray,$keyid);
		
		echo json_encode($result);
	break;
}
?>