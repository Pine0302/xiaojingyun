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


class model_activity{
	var $db;

	function __construct()
	{
        $this->db = DB::getInstance();
    }


    /*
	 * zhoumuwang
	 * 编辑时读取活动数据
    */
    function read($data = array())
    {
    	$id        = $data['act_id'];
    	
    	if(empty($id)) return array('errcode' => 400,'errmsg'=>'缺少参数act_id');

    	$sql       = "select act_id,act_name,act_type,is_commission,start_time,end_time,ext_info,status,auto_start from ".WSY_SHOP.".integral_activity where act_id = '$id'";
    	$result    = $this->db->getRow ($sql);
		//var_dump($result);
		if ($result)
		{
			$result['ext_info'] ?  $result['ext_info'] = json_decode($result['ext_info']) : '';

			$res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result);
		}
		else
		{
			$res = array('errcode' => 400,'errmsg'=>'获取失败');
		}

    	return $res;
    }

     /*
     * zhoumuwang
     * 添加签到活动
     * 添加签到活动时候，先读取签到活动默认时间配置
    */
    function sign_add($data = array())
    {
        extract ( $data );
        $sql = "select id,sign_json from ".WSY_SHOP.".integral_setting where cust_id = '$cust_id' limit 1";
        $res = $this->db->getRow($sql);
        if($res['id'] > 0)
        {
        	$json = json_decode($res['sign_json'],true);
        	unset($json['continuity']);
        	$res1['ext_info'] = $json;
        	return array('errcode' => 0,'errmsg'=>'获取成功','data'=>$res1);
        }

        return array('errcode' => 400,'errmsg'=>'请先设置默认签到配置！');
    }

     /*
	 * zhoumuwang
	 * 活动列表
    */
    function activity_index($data = array())
    {
    	extract ( $data );
//var_dump($data);
    	if(!isset($cust_id)) return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');

    	$contidion = 1;
    	$now 	   = time();

    	isset($count) ? $count : $count = 20;
    	isset($page)  ? $page  = $page*$count : $page = 0;
    	if(isset($act_name)   && !empty($act_name))     $contidion .= " and act_name like '%".$act_name."%'";
    	if(isset($start_time) && !empty($start_time))   $contidion .= " and UNIX_TIMESTAMP(start_time) >='".strtotime($start_time)."'";
    	if(isset($end_time)   && !empty($end_time))     $contidion .= " and UNIX_TIMESTAMP(end_time) <='".strtotime($end_time)."'";
    	if(isset($add_time)   && !empty($add_time))
    	{
    		$today_sta  = strtotime($add_time);
    		$today_end  = $today_sta+(24*3600);
    		$contidion .= " and UNIX_TIMESTAMP(add_time) <='".$today_end."' and UNIX_TIMESTAMP(add_time)>='".$today_sta."'";
    	}
    	if(isset($act_type)   )     					$contidion .= " and act_type ='".$act_type."'";
    	if(isset($status) && $status != ''&& $status != -1) $contidion .= " and status ='".$status."'";
    	$contidion .= " and cust_id = $cust_id and isvalid = 1";
    	$sql        = "select act_id,act_name,add_time,status,start_time,end_time,auto_start from ".WSY_SHOP.".integral_activity where $contidion  order by act_id desc limit $page,$count";
    	$sql_total  = "select count(act_id) as total from ".WSY_SHOP.".integral_activity where $contidion";

    	$all        = $this->db->getRow($sql_total);
    	$result     = $this->db->getAll ($sql);
		foreach($result as $k => $val){
			if($val['status'] == 0){
				$result[$k]['status'] = "未启动";
			}elseif($val['status'] == 1){
				if(strtotime($val['start_time']) <= $now){
				    if(strtotime($val['end_time']) < $now){
                        $result[$k]['status'] = "结束";
                    }else{
                        $result[$k]['status'] = "进行中";
                    }
				}else{
					$result[$k]['status'] = "已启用";
				}
			}elseif($val['status'] == 2){
				$result[$k]['status'] = "结束";
			}elseif($val['status'] == 3){
				$result[$k]['status'] = "手动终止";
			}
		}

		$list_num = count($result);


    	if(count($result) <= 0) return array('errcode' => 400,'errmsg'=>'获取失败');
    	$result['total']  = $all['total'];
    	$result['page']   = $page;
    	$result['count']  = $count;
    	$result['list_num']  = $list_num;


    	$res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result);

    	return $res;

    }


    /*
	 * zhoumuwang
	 * 定时任务接口
	 * 过期的活动状态改为已结束
    */
    function crontab_activity()
    {
    	$now       = time();
    	$sql 	   = "select end_time,act_id from ".WSY_SHOP.".integral_activity where status = 1";//已启用的活动，定时任务改为已结束
    	$res 	   = $this->db->getAll($sql);
    	$contidion = 1;
    	$act_id    = array();

    	foreach ($res as $k => $v)
    	{
    		$overtime = strtotime($v['end_time']);
    		if($overtime < $now)
    		{
    			$act_id[] = $v['act_id'];
    		}
    	}

    	if(count($act_id) > 0)
    	{
    		$act = implode(',',$act_id);
    		$sql = "update ".WSY_SHOP.".integral_activity set status = 2 where act_id in('$act')";
    		$res = $this->db->query($sql);
    	}

    	return true;
    }

	/*
	* 活动保存操作
	* 参数：start_time-开始时间、end_time-结束时间、act_name-活动名称、status-是否发布、cust_id-商家ID、ext_info-配置json、act_type-活动类型 0购买产品送积分 1签到送积分 2兑换扣积分'、act_id-活动id、op-操作del-删除 release发布 end结束 conserve更新保存
	* $Author: liuzhongxuan $
	* 2017-08-24  $
	*/
	/*活动操作*/
	function saveactivity($parm=array()){
		$where = '';
		//var_dump($parm);
		//拼装更新数据
		if($parm['act_id']>0){
            $log_data['remark'] = '修改活动'.$parm['act_id'].'设置';//描述
			$fun = 'update';
			//基础数据
			$where = "act_id={$parm['act_id']}";
			$data = array();
			$data['act_id'] = $parm['act_id'];
				//删除
			if($parm['op']=='del'){
				$sql = "select status from ".WSY_SHOP.".integral_activity where act_id='{$parm['act_id']}'";
				$status = $this->db->getOne($sql);
				if($status == 1){
					json_out(array('errcode' => 600,'errmsg'=>'发布中活动不能删除'));
				}
				$data['isvalid'] = false;

				$log_data['remark'] = '删除id:'.$parm['act_id'].'活动';//描述

				//发布
			}elseif($parm['op']=='release'){
				if($parm['act_type'] == 1){
					$res = $this->checktime($parm['act_id'],$parm['act_type'],$parm['cust_id']);//检验发布时间是否冲突
					if($res['errcode'] == 600){
						json_out($res);
					}
				}
				$data['auto_start'] = 2;
				$data['status'] = 1;
				//结束
			}elseif($parm['op']=='end'){
				$data['status'] = 3;
				//保存
			}elseif($parm['op']=='conserve'){
				$sql = "select status from ".WSY_SHOP.".integral_activity where act_id={$parm['act_id']}";
				$status = $this->db->getOne($sql);
				if($status == 1){
					json_out(array('errcode' => 600,'errmsg'=>'发布中活动不能编辑'));
				}
				$data['act_name'] 	= $parm['act_name'];
				$data['act_type'] 	= $parm['act_type'];
				$data['only_type'] 	= $parm['only_type'];
				$data['start_time'] = $parm['start_time'];
				$data['end_time'] 	= $parm['end_time'];
				$data['ext_info'] 	= $parm['ext_info'];
				$data['auto_start'] = $parm['auto_start'];
				//自动发布
				if($parm['auto_start'])	
				{
					$data['status'] = 1;
				}
				if($parm['act_type'] != 1){
					$data['is_commission'] = $parm['is_commission'];
				}
			}
		}else{
            $log_data['remark'] = '添加活动';//描述
			$fun = 'insert';

			//其他数据
			$data = array(
			'cust_id'=>$parm['cust_id'],
			'act_name'=>$parm['act_name'],
			'act_type'=>$parm['act_type'],
			'only_type'=>$parm['only_type'],
			'start_time'=>$parm['start_time'],
			'end_time'=>$parm['end_time'],
			'ext_info'=>$parm['ext_info'],
			'add_time'=>$parm['add_time'],
			'isvalid'=>1,

			);
			//自动发布

//			$data['end_time'] = date('Y-m-d H:i:s',(24*3600+strtotime($data['end_time'])-1));
			$data['end_time'] = date('Y-m-d H:i:s',strtotime($data['end_time']));

			$data['auto_start'] = $parm['auto_start'];
			if($parm['auto_start'])	
			{
				if($parm['act_type'] == 1)
				{
					$res = $this->checktime2($parm['act_type'],$parm['cust_id'],$data['start_time'],$data['end_time']);//检验发布时间是否冲突
					if($res['errcode'] == 600)
					{
						json_out($res);
					}
				}
				$data['status'] = 1;
			}
			if($parm['act_type'] != 1){
				$data['is_commission'] = $parm['is_commission'];
			}

		}
		//执行语句
		//var_dump($data);
		$result = $this->db->autoExecute(WSY_SHOP.'.integral_activity', $data, $fun,$where) ;
		if($fun == 'insert'){
			$act_id = $this->db->insert_id();
		}else{
			$act_id = $parm['act_id'];
		}
		$data = array(
				'operation' => $fun,
				'act_id' => $act_id,
				'end_time'=>$data['end_time'],
			);
	//	var_dump($result);
		if ($result) {
				$res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$data);
                //插入操作日志
                $log_data['cust_id'] = $parm['cust_id']; //商家id
                $log_data['admin_name'] = $_SESSION['curr_login'];//操作人名称
                $log_data['add_time'] = date('Y-m-d H:i:s',time());//操作时间
                $this->insert_admin_log($log_data);

			} else {
				$res = array('errcode' => 400,'errmsg'=>'获取失败');
			}
		return $res;
	}
	/*
	* 校验活动时间
	* 参数：start_time-开始时间、end_time-结束时间、act_name-活动名称、status-是否发布、cust_id-商家ID、ext_info-配置json、act_type-活动类型 0购买产品送积分 1签到送积分 		  2兑换扣积分'、act_id-活动id、op-操作del-删除 release发布 end结束 conserve更新保存
	* $Author: liuzhongxuan $
	* 2017-08-24  $
	*/
	/*校验活动时间*/
	function checktime($act_id,$act_type,$cust_id){
		$sql1       = "select start_time,end_time from ".WSY_SHOP.".integral_activity where act_id = '$act_id'";
    	$row    = $this->db->getRow ($sql1);
		$start_time = $row['start_time'];//开始时间
		$end_time = $row['end_time'];//结束时间

		//当前操作活动的时间与所有发布中的时间进行对比校验（数据库查询）
		$sql       = "select act_id from ".WSY_SHOP.".integral_activity where ('$start_time' BETWEEN start_time AND end_time OR '$end_time' BETWEEN start_time AND end_time OR start_time BETWEEN '$start_time' AND '$end_time' OR end_time BETWEEN '$start_time' AND '$end_time')  and act_type = '$act_type' and isvalid = 1 and cust_id = '$cust_id' and status = 1";

    	$result    = $this->db->getOne ($sql);
		if($result){
			return array('errcode'=>600,'errmsg'=>'您选择活动时间段已占用，请选择其他时间段！');
		}else{
			return array('errcode'=>0,'errmsg'=>'成功！');
		}
	}

	function checktime2($act_type,$cust_id,$start_time,$end_time)
	{
		//当前操作活动的时间与所有发布中的时间进行对比校验（数据库查询）
		$sql       = "select act_id from ".WSY_SHOP.".integral_activity where ('$start_time' BETWEEN start_time AND end_time OR '$end_time' BETWEEN start_time AND end_time OR start_time BETWEEN '$start_time' AND '$end_time' OR end_time BETWEEN '$start_time' AND '$end_time')  and act_type = '$act_type' and isvalid = 1 and cust_id = '$cust_id' and status = 1";

    	$result    = $this->db->getOne ($sql);
		if($result){
			return array('errcode'=>600,'errmsg'=>'您选择活动时间段已占用，请选择其他时间段！');
		}else{
			return array('errcode'=>0,'errmsg'=>'成功！');
		}
	}

	/*
	 * 获取添加活动积分产品列表
	 * $Author: wuhaoliang $
	 * 2017-08-24  $
	 */
	function m_get_integral_product_list($data=array()){
		//获取参数并分解
		$cust_id    = $data['cust_id'];
		$page       = $data['page'];
		$page_size  = $data['page_size'];
		$act_id   	= $data['act_id'];
		//$search_key = json_decode($data['search_key'],true);


		$search_name    = $data['search_key']['product_name'];
		$search_pid     = $data['search_key']['product_id'];
		$search_type_id = $data['search_key']['type_id'];
		//拼接查询语句
		$where = ' isp.cust_id="'.$cust_id.'" and isp.isvalid = 1 and wcp.isvalid =1 and isp.pros_id = -1 and isp.type= 0 ';

		if(!empty($search_name)){
			$where .= " and wcp.name LIKE '%".$search_name."%' ";
		}
		if(!empty($search_pid)){
			$where .= " and wcp.id LIKE '%".$search_pid."%' ";
		}
		if($search_type_id !=-1 && is_numeric($search_type_id))
		{
			$supple_sql = "SELECT id FROM weixin_commonshop_types WHERE top_id = '{$search_type_id}' AND isvalid = true"; //查询所有子分类
			$all_supple = $this->db->getAll($supple_sql);
			$Content_id = '';
			foreach ($all_supple as $key => $value) 
			{
				if ($key > 0) 
				{
					$Content_id .= "  OR wcp.type_ids LIKE '%,{$value["id"]},%'";
				}
				else
				{
					$Content_id = "  OR (wcp.type_ids LIKE '%,{$value["id"]},%'";
				}
				
			}
			if ($Content_id != '') 
			{
				$Content_id .= ")";
			}
			
			$where .= " and wcp.type_ids LIKE '%,".$search_type_id.",%' ".$Content_id;
		}


		/*$count_sql = "select count(isp.id) as sum from  ".WSY_SHOP.".integral_setting_product isp inner join  weixin_commonshop_products wcp on isp.product_id = wcp.id
			where ".$where." and
			wcp.id not in (select iap.product_id from ".WSY_SHOP.".integral_activity_product iap  inner join ".WSY_SHOP.".integral_activity ia on iap.act_id = ia.act_id  where iap.isvalid = 1 and ia.isvalid = 1 and iap.cust_id =".$cust_id." and ia.cust_id = ".$cust_id." and ia.act_type = 2 and (ia.status = 1 or ia.status = 0)) and
			wcp.id not in (select product_id from ".WSY_SHOP.".integral_activity_product where act_id=".$act_id." and cust_id=".$cust_id." and isvalid = 1)";

		$all_num     = $this->db->getAll($count_sql)[0]['sum'];
		//var_dump($all_num);
		$count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

		$offset = ($page-1)*$page_size;
		$limit  = ' limit '.$offset.','.$page_size;*/

		//活动列表
		/*$sql = "select isp.id,isp.product_id,wcp.default_imgurl,wcp.name,wcp.type_ids,wcp.orgin_price,wcp.now_price,wcp.storenum from ".WSY_SHOP.".integral_setting_product isp inner join weixin_commonshop_products wcp on isp.product_id = wcp.id where ".$where." and
			wcp.id not in (select iap.product_id from ".WSY_SHOP.".integral_activity_product iap  inner join ".WSY_SHOP.".integral_activity ia on iap.act_id = ia.act_id  where iap.isvalid = 1 and ia.isvalid = 1 and iap.cust_id =".$cust_id." and ia.cust_id = ".$cust_id." and ia.act_type = 2 and (ia.status = 1 or ia.status = 0))
			and wcp.id not in (select product_id from ".WSY_SHOP.".integral_activity_product where act_id=".$act_id." and cust_id=".$cust_id." and isvalid = 1)
			order by isp.add_time desc ".$limit;*/

		$sql = "select act_id,act_name,act_type,is_commission,start_time,end_time,ext_info,status,auto_start from ".WSY_SHOP.".integral_activity where act_id = '$act_id'";
    	$result    = $this->db->getRow ($sql);

		$start_time = $result['start_time'];
		$end_time 	= $result['end_time'];

		//规则：支持同一个时间不同活动进行，但不支持该时间内有已经开始的活动的产品
		//1.已经添加的产品不能要
		//2.赠送活动的产品不能要
		//3.所有跨入该活动开始时间，结束时间的不能要
		$field_count = 'SELECT count(wcp.id) as sum';
		$field_select = "
			SELECT DISTINCT(isp.product_id),isp.id,wcp.default_imgurl,wcp.name,wcp.type_ids,wcp.orgin_price,wcp.now_price,wcp.storenum";
		$from =	" FROM ".WSY_SHOP.".integral_setting_product isp
			INNER JOIN weixin_commonshop_products wcp ON isp.product_id = wcp.id
			WHERE ".$where." AND isp.product_id NOT IN
			(
			SELECT pro.product_id FROM  ".WSY_SHOP.".integral_activity_product pro
			LEFT JOIN ".WSY_SHOP.".integral_activity ia  ON ia.act_id=pro.act_id
			AND ia.`status`<=1 AND ia.`isvalid`=1 AND pro.isvalid = 1
			where
			(ia.start_time<'{$start_time}' AND ia.end_time>'{$start_time}' )
			OR (ia.start_time<'{$end_time}' AND ia.end_time>'{$end_time}')
			OR (ia.start_time>'{$start_time}' AND ia.end_time<'{$end_time}')
			OR (ia.start_time<'{$start_time}' AND ia.end_time>'{$end_time}')
			OR ia.start_time = '{$start_time}'
			OR ia.start_time = '{$end_time}'
			OR ia.end_time = '{$start_time}'
			OR ia.end_time = '{$end_time}'
			OR ia.start_time = '{$start_time}'
			OR ia.start_time = '{$end_time}'
			OR ia.end_time = '{$start_time}'
			OR ia.end_time = '{$end_time}'
			AND
			pro.cust_id='{$cust_id}'

			) AND isp.product_id NOT IN (
				SELECT iep.product_id from ".WSY_SHOP.".integral_exchange_product iep
				LEFT JOIN ".WSY_SHOP.".integral_activity ia  ON ia.act_id=iep.act_id
				AND  ia.`isvalid`=1
				where
				iep.cust_id='{$cust_id}'
				AND  ia.status <= 1
				and iep.isvalid = 1
			) AND isp.product_id NOT IN (
				SELECT product_id from ".WSY_SHOP.".integral_activity_product where act_id='{$act_id}' and cust_id='{$cust_id} 'and isvalid = 1
			) 
		";
		$query_count = $field_count.$from;
		$all_num     = $this->db->getAll($query_count)[0]['sum'];
		//var_dump($all_num);
		$count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

		$offset = ($page-1)*$page_size;
		$limit  = ' limit '.$offset.','.$page_size;

		$from .= " GROUP BY isp.product_id order by isp.add_time desc";
		$query_select = $field_select.$from.$limit;
		//echo $query_select;

		$list = $this->db->getAll($query_select);
		$list_num = count($list);
		//查询类型名称
		$list = $this->get_type_name($list);
		//var_dump($list);
		return array("errcode"=>0,'errmsg'=>"数据获取成功",'datas'=>array('list'=>$list,'page_count'=>$count,'total'=>$all_num,'list_num'=>$list_num));
	}


	/*
	 * 添加门店积分产品时 获取 门店产品列表
	 * $Author: wuhaoliang $
	 * 2017-11-07  $
	 */
	function m_get_store_product_list($data=array()){
		//获取参数并分解
		$cust_id    = $data['cust_id'];
		$page       = $data['page'];
		$page_size  = $data['page_size'];
		//$search_key = json_decode($data['search_key'],true);

		$search_name    = $data['search_key']['product_name'];
		$search_pid     = $data['search_key']['product_id'];
		$search_type_id = $data['search_key']['type_id'];
		//拼接查询语句
		$where = " op.customer_id= '".$cust_id."'  and wcp.isvalid =1 and op.isvalid = 1 and op.store_status='up' ";

		if(!empty($search_name)){
			$where .= " and wcp.name LIKE '%".$search_name."%' ";
		}
		if(!empty($search_pid)){
			$where .= " and wcp.id LIKE '%".$search_pid."%' ";
		}
		if($search_type_id !=-1 && is_numeric($search_type_id)){
			$where .= " and wcp.type_ids LIKE '%,".$search_type_id.",%' ";
		}
		$groupby = ' group by op.product_id ';

		$count_sql  = 'select count(product_id) as sum from (SELECT op.product_id from '.WSY_DH.'.orderingretail_product op inner join '.WSY_PROD.'.weixin_commonshop_products wcp  on op.product_id = wcp.id left join '.WSY_DH.'.orderingretail_proxy_product opp on opp.product_id = op.product_id where '.$where.' and op.product_id not in (SELECT product_id FROM '.WSY_SHOP.'.integral_setting_product WHERE pros_id=-1 AND type=1 AND isvalid=1) '.$groupby.' ) as a ';
		
		
		$all_num    = $this->db->getAll($count_sql)[0]['sum'];
		$count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

		$offset  = ($page-1)*$page_size;
		$limit   = ' limit '.$offset.','.$page_size;
		$orderby = ' order by op.product_id desc ';
		$sql = 'SELECT  op.product_id as id,op.product_id,wcp.default_imgurl,wcp.name,wcp.type_ids,wcp.orgin_price,wcp.now_price,sum(opp.store_count) as storenum
				from '.WSY_DH.'.orderingretail_product op 
				inner join  '.WSY_PROD.'.weixin_commonshop_products wcp on op.product_id = wcp.id
				left  join  '.WSY_DH.'.orderingretail_proxy_product opp on opp.product_id = op.product_id
				where '.$where.' and op.product_id not in
					(SELECT product_id FROM '.WSY_SHOP.'.integral_setting_product WHERE pros_id=-1 AND type=1 AND isvalid=1) 
				'.$groupby.$orderby.$limit;
		$list = $this->db->getAll($sql);
		$list_num = count($list);
		//查询类型名称
		$list = $this->get_type_name($list);
		//var_dump($list);
		return array("errcode"=>0,'errmsg'=>"数据获取成功",'datas'=>array('list'=>$list,'page_count'=>$count,'total'=>$all_num,'list_num'=>$list_num,'sql'=>$count_sql));

	}


	/*
	 * 获取 已添加的 常量积分产品
	 * $Author: wuhaoliang $
	 * 2017-09-02  $
	 */
	function get_integral_product($data=array()){
		//获取参数并分解
		$cust_id    = $data['cust_id'];
		$page       = $data['page'];
		$page_size  = $data['page_size'];
		$integral_type  = $data['integral_type'];
		//$search_key = json_decode($data['search_key'],true);

		$search_name    = $data['search_key']['product_name'];
		$search_pid     = $data['search_key']['product_id'];
		$search_type_id = $data['search_key']['type_id'];
		//var_dump($search_key);
		//拼接查询语句and wcp.isvalid =1
		$where = ' isp.cust_id="'.$cust_id.'" and isp.isvalid = 1  and isp.pros_id = -1 and isp.type="'.$integral_type.'"';

		if(!empty($search_name)){
			$where .= " and wcp.name LIKE '%".$search_name."%' ";
		}

		//精确搜索
		if(!empty($search_pid)){
			$where .= " and wcp.id ='".$search_pid."'";
		}

		//模糊搜索
//		if(!empty($search_pid)){
//			$where .= " and wcp.id LIKE '%".$search_pid."%' ";
//		}

		if($search_type_id>0){
			$parent_id	=-1;
			$top_id		=-1;
			$level	= 0;
			$type_SQL="select parent_id,level,top_id from weixin_commonshop_types where id='".$search_type_id."'";
			$type_result = _mysql_query($type_SQL) or die('Query failed: ' . mysql_error());
			while ($type_row = mysql_fetch_object($type_result)) {
				$parent_id	= $type_row->parent_id;
				$level		= $type_row->level;
				$top_id		= $type_row->top_id;
			 }
			 $where .=" and wcp.type_ids like '%,".$search_type_id.",%'";
			 if($parent_id>0){
				$type_ID_SQL="select id from weixin_commonshop_types where top_id='".$top_id."' and level>".$level." and gflag like '%,".$search_type_id.",%'" ;
				$type_ID_result = _mysql_query($type_ID_SQL) or die('Query failed: ' . mysql_error());
				while ($type_row = mysql_fetch_object($type_ID_result)) {
					$type_id=$type_row->id;
					$Str=$Str."or wcp.type_ids like '%,".$type_id.",%'";
				 }

			 }else{
			 	$type_ID_SQL="select id from weixin_commonshop_types where top_id='".$search_type_id."' and level>".$level ;
				$type_ID_result = _mysql_query($type_ID_SQL) or die('Query failed: ' . mysql_error());
				while ($type_row = mysql_fetch_object($type_ID_result)) {
					$type_id=$type_row->id;
					$where .=" or wcp.type_ids like '%,".$type_id.",%'";
				 }
			 }
			
			$where .="  And isp.isvalid = true GROUP BY isp.product_id " ;
			
		}


		$count_sql  = 'select count(isp.id) as sum from '.WSY_SHOP.'.integral_setting_product isp inner join weixin_commonshop_products wcp on isp.product_id = wcp.id where '.$where;
		
		$all_num    = $this->db->getAll($count_sql)[0]['sum'];

		$count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

		$offset  = ($page-1)*$page_size;
		$limit   = ' limit '.$offset.','.$page_size;
		$orderby = ' order by isp.id desc ';
		$sql = 'select isp.id,isp.product_id,isp.mode,isp.consume_integral,isp.recommend_integral,isp.consume_type,isp.recommend_type,wcp.default_imgurl,wcp.name,wcp.type_ids,wcp.orgin_price,wcp.now_price,wcp.storenum from '.WSY_SHOP.'.integral_setting_product isp inner join weixin_commonshop_products wcp on isp.product_id = wcp.id where '.$where.$orderby.$limit;
		
		$list = $this->db->getAll($sql);
		$list_num = count($list);
		//查询类型名称
		$list = $this->get_type_name($list);

		$sql_base = "SELECT basic_json, store_json FROM ".WSY_SHOP.".integral_setting WHERE cust_id='{$cust_id}'";
		$basic_setting  		= $this->db->getRow($sql_base);
		$basic_setting_shop  	= json_decode($basic_setting['basic_json'], true);
		$basic_setting_store  	= json_decode($basic_setting['store_json'], true);

		foreach ($list as $key => $one) {
			if( $one['mode'] == 0 ){
				if( $integral_type == 0){
					$one['consume_integral']   = $basic_setting_shop['gift_set_value'];
					$one['recommend_integral'] = $basic_setting_shop['gift_set_value'];
					$one['consume_type']       = $basic_setting_shop['gift_set_type']+1;
					$one['recommend_type']     = $basic_setting_shop['gift_set_type']+1;
				}else{
					$one['consume_integral']   = $basic_setting_store['gift_set_value'];
					$one['consume_type']       = $basic_setting_store['gift_set_type'];
				}
				
			}
			if($one['consume_type'] == 1){
				$list[$key]['true_consume_integral'] = $one['now_price']*$one['consume_integral']/100;
			}else{
				$list[$key]['true_consume_integral'] = $one['consume_integral'];
			}

			if($one['recommend_type'] == 1){
				$list[$key]['true_recommend_integral'] = $one['now_price']*$one['recommend_integral']/100;
			}else{
				$list[$key]['true_recommend_integral'] = $one['recommend_integral'];
			}
		}
		//var_dump($list);
        
        //查询门店积分开关
		$sql_set = 'select store_json from '.WSY_SHOP.'.integral_setting where cust_id ="'.$cust_id.'"';

		$store_json =$this->db->getOne($sql_set);
        
        $store_arr = json_decode($store_json,TRUE);
        
        if(!empty($store_arr['join_product'])){
            $join_product = $store_arr['join_product'];
        }else{
            $join_product = 1;
        }
        $diyname = _get_diyname($data['cust_id']);
		return array("errcode"=>0,'errmsg'=>"数据获取成功",'datas'=>array('list'=>$list,'page_count'=>$count,'total'=>$all_num,'join_product'=>$join_product,'list_num'=>$list_num,'shop_integral_name'=>$diyname['shop_integral_name'],'store_integral_name'=>$diyname['store_integral_name'],'custom_name'=>$diyname['custom_name']));

	}
	/*
	 * 获取添加兑换产品列表，除去活动积分产品
	 * $Author: wuhaoliang $
	 * 2017-08-24  $
	 */
	function m_get_product_except_inte($data=array()){
		//获取参数并分解
		$cust_id    = $data['cust_id'];
		$page       = $data['page'];
		$page_size  = $data['page_size'];
		$act_id     = $data['act_id'];
		//$search_key = json_decode($data['search_key'],true);


		$search_name    = $data['search_key']['product_name'];
		$search_pid     = $data['search_key']['product_id'];
		$search_type_id = $data['search_key']['type_id'];

		//拼接查询语句
		$where = ' wcp.customer_id="'.$cust_id.'" and wcp.isvalid = 1 and wcp.isout =0 ';
		if(!empty($search_name)){
			$where .= " and wcp.name LIKE '%".$search_name."%' ";
		}
		if(!empty($search_pid)){
			$where .= " and wcp.id LIKE '%".$search_pid."%' ";
		}
		if($search_type_id !=-1 && is_numeric($search_type_id))
		{
			$supple_sql = "SELECT id FROM weixin_commonshop_types WHERE top_id = '{$search_type_id}' AND isvalid = true"; //查询所有子分类
			$all_supple = $this->db->getAll($supple_sql);
			$Content_id = '';
			foreach ($all_supple as $key => $value) 
			{
				if ($key > 0) 
				{
					$Content_id .= "  OR wcp.type_ids LIKE '%,{$value["id"]},%'";
				}
				else
				{
					$Content_id = "  OR (wcp.type_ids LIKE '%,{$value["id"]},%'";
				}
				
			}
			if ($Content_id != '') 
			{
				$Content_id .= ")";
			}
			
			$where .= " and wcp.type_ids LIKE '%,".$search_type_id.",%' ".$Content_id;
		}

		/*$count_sql = "select count(1) as sum
					  from weixin_commonshop_products wcp
					  where ".$where." and
					  wcp.id not in (select iap.product_id from ".WSY_SHOP.".integral_activity_product iap  inner join ".WSY_SHOP.".integral_activity ia on iap.act_id = ia.act_id  where iap.isvalid = 1 and ia.isvalid = 1 and iap.cust_id =".$cust_id." and ia.cust_id = ".$cust_id." and ia.act_type = 0 and (ia.status = 1 or ia.status = 0)) and
					  wcp.id not in (select product_id from ".WSY_SHOP.".integral_exchange_product where act_id=".$act_id." and cust_id=".$cust_id." and isvalid = 1) ";

	    $all_num  = $this->db->getAll($count_sql)[0]['sum'];

	    $count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

        $offset = ($page-1)*$page_size;
		$limit  = ' limit '.$offset.','.$page_size;

		//活动列表
		$sql = "select wcp.id as product_id,wcp.default_imgurl,wcp.name,wcp.type_ids,wcp.orgin_price,wcp.now_price,wcp.storenum,wcp.sell_count
				from weixin_commonshop_products wcp where ".$where." and
				wcp.id not in (select iap.product_id from ".WSY_SHOP.".integral_activity_product iap  inner join ".WSY_SHOP.".integral_activity ia on iap.act_id = ia.act_id  where iap.isvalid = 1 and ia.isvalid = 1 and iap.cust_id =".$cust_id." and ia.cust_id = ".$cust_id." and ia.act_type = 0 and (ia.status = 1 or ia.status = 0)) and
				wcp.id not in (select product_id from ".WSY_SHOP.".integral_exchange_product where act_id=".$act_id." and cust_id=".$cust_id." and isvalid = 1)
				order by wcp.sell_count desc,wcp.id desc ".$limit;
				*/


		$sql = "select act_id,act_name,act_type,is_commission,start_time,end_time,ext_info,status,auto_start from ".WSY_SHOP.".integral_activity where act_id = '{$act_id}'";
    	$result    = $this->db->getRow ($sql);

		$start_time = $result['start_time'];
		$end_time 	= $result['end_time'];


		//规则：支持同一个时间不同活动进行，但不支持该时间内有已经开始的活动的产品
		//1.已经添加的产品不能要
		//2.兑换活动的产品不能要
		//3.所有跨入该活动开始时间，结束时间的不能要
		$field_count = 'SELECT count(wcp.id) as sum ';
		$field_select = "
			SELECT distinct(wcp.id) AS product_id, wcp.id,wcp.default_imgurl,wcp.name,wcp.type_ids,wcp.orgin_price,wcp.now_price,wcp.storenum ";
		//第一个not in 排除 自身活动时间段内已经存在的兑换产品
		//第二个not in 排除 自身活动时间段内积分活动产品
		//第三个not in 排除 自身活动已经添加的产品
		$from =	" from weixin_commonshop_products wcp 
			WHERE ".$where." AND wcp.id NOT IN
			(
			SELECT pro.product_id FROM  ".WSY_SHOP.".integral_exchange_product pro
			LEFT JOIN ".WSY_SHOP.".integral_activity ia  ON ia.act_id=pro.act_id
			AND ia.`status`<=1 AND ia.`isvalid`=1 AND  pro.isvalid = 1
			where
			(ia.start_time<'{$start_time}' AND ia.end_time>'{$start_time}' )
			OR (ia.start_time<'{$end_time}' AND ia.end_time>'{$end_time}')
			OR (ia.start_time>'{$start_time}' AND ia.end_time<'{$end_time}')
			OR (ia.start_time<'{$start_time}' AND ia.end_time>'{$end_time}')
			OR ia.start_time = '{$start_time}'
			OR ia.start_time = '{$end_time}'
			OR ia.end_time = '{$start_time}'
			OR ia.end_time = '{$end_time}'
			OR ia.start_time = '{$start_time}'
			OR ia.start_time = '{$end_time}'
			OR ia.end_time = '{$start_time}'
			OR ia.end_time = '{$end_time}'
			AND
			pro.cust_id='{$cust_id}'

			) AND wcp.id  NOT IN (
				SELECT iep.product_id from ".WSY_SHOP.".integral_activity_product iep
				LEFT JOIN ".WSY_SHOP.".integral_activity ia  ON ia.act_id=iep.act_id
				AND  ia.`isvalid`=1
				where
				iep.cust_id='{$cust_id}'
				AND  ia.status <= 1
				AND  iep.isvalid = 1
			) AND wcp.id  NOT IN (
				SELECT product_id from ".WSY_SHOP.".integral_exchange_product where act_id='{$act_id}' and cust_id='{$cust_id}' and isvalid = 1
			) 
		";

		$query_count = $field_count.$from;
		$all_num     = $this->db->getAll($query_count)[0]['sum'];
		$count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

		$offset = ($page-1)*$page_size;
		$limit  = ' limit '.$offset.','.$page_size;

		$from .= " group by wcp.id order by wcp.id desc";
		$query_select = $field_select.$from.$limit;
		//var_dump($query_select);
		$list = $this->db->getAll($query_select);
		$list_num = count($list);
		//查询类型名称
		$list = $this->get_type_name($list);

	    //var_dump(array('list'=>$list,'page_count'=>$count));
		return array("errcode"=>0,'errmsg'=>"数据获取成功",'datas'=>array('list'=>$list,'page_count'=>$count,'total'=>$all_num,'list_num'=>$list_num));

	}

	/*
	 * 获取活动产品列表，积分与兑换活动通用
	 * $Author: wuhaoliang $
	 * 2017-08-24  $
	 */
	function m_get_activity_product($data=array()){
		//获取参数并分解
		$cust_id    = $data['cust_id'];
		$act_id     = $data['act_id'];
		$page 		= $data['page'];
        $page_size  = $data['page_size'];

		//查询该活动类型并判断是否存在
		$sql_act = 'select act_type from '.WSY_SHOP.'.integral_activity where act_id ="'.$act_id.'" and cust_id ="'.$cust_id.'" and isvalid = 1 and (act_type = 0 or act_type = 2)';

		$this_act_type =$this->db->getOne($sql_act)['act_type'];

		if($this_act_type == 0){
			$sql_table = 'integral_activity_product';
		}else if($this_act_type == 2){
			$sql_table = 'integral_exchange_product';
		}else{
			return array('errcode'=>505,'errmsg'=>'活动id有误，或者已经被删除');
		}

		$count_sql = 'select count(1) as sum
					  from  '.WSY_SHOP.'.'.$sql_table.' actp inner join weixin_commonshop_products wcp on actp.product_id = wcp.id where actp.act_id = "'.$act_id.'" and actp.cust_id = "'.$cust_id.'" and actp.isvalid = 1 and wcp.isvalid = 1';

	    $all_num  = $this->db->getAll($count_sql)[0]['sum'];

	    $count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}
		if($page < 1 || empty($page)){
            $page = 1;
        }

        $offset = ($page-1)*$page_size;
		$limit  = ' limit '.$offset.','.$page_size;

		$sql_prod = ' select actp.*,wcp.name as product_name,wcp.default_imgurl,wcp.type_ids,wcp.orgin_price,wcp.now_price,wcp.storenum from  '.WSY_SHOP.'.'.$sql_table.' actp inner join weixin_commonshop_products wcp on actp.product_id = wcp.id where actp.act_id = "'.$act_id.'" and actp.cust_id = "'.$cust_id.'" and actp.isvalid = 1 and wcp.isvalid = 1  order by actp.add_time desc '.$limit;

		$list = $this->db->getAll($sql_prod);
		$list_num = count($list);
		//查询类型名称
		$list = $this->get_type_name($list);
		//var_dump($list);
		$sql="select only_type from ".WSY_SHOP.".integral_activity where act_id='{$act_id}' and cust_id='{$cust_id}' and isvalid=1";
		$ret=$this->db->getRow($sql);
		return array("errcode"=>0,'errmsg'=>"数据获取成功",'datas'=>array('list'=>$list,'page_count'=>$count,'total'=>$all_num,'list_num'=>$list_num,'only_type'=>$ret['only_type']));
	}


	/*
	 * 添加常量积分产品，
	 * $Author: wuhaoliang $
	 * 2017-08-25  $
	 */
	function m_add_integral_product($data=array()){
		//获取并分解数据
        $product_id = $data['p_id'];
        $cust_id    = $data['cust_id'];
        $integral_type    	= $data['integral_type'];
        $consume_integral   = $data['consume_integral'];
        $consume_type       = $data['consume_type'];
        $recommend_integral = $data['recommend_integral'];
        $recommend_type     = $data['recommend_type'];
        if(empty($product_id)){
        	//返回码需统一
        	return array('errcode'=>505,'errmsg'=>'缺失产品id，或传输格式不对');
        }
        //var_dump($product_id);
        $product_sql = 'select name from weixin_commonshop_products where id="'.$product_id.'" and customer_id="'.$cust_id.'" and isvalid =1';
		//echo $product_sql;
        $product_name  = $this->db->getOne($product_sql);
        if(empty($product_name)){
        	//返回码需统一
        	return array('errcode'=>505,'errmsg'=>'产品id有误');
        }
        $ins_product  = array();

        $ins_product['product_id'] = $product_id;
        $ins_product['cust_id']    = $cust_id;
        $ins_product['mode']       = 0;
        $ins_product['isvalid']    = 1;
        $ins_product['add_time']   = date("Y-m-d H:i:s");
        $ins_product['is_commission']      = 0;
        $ins_product['product_name']       = $product_name;
        $ins_product['consume_integral']   = $consume_integral;
        $ins_product['recommend_integral'] = $recommend_integral;
        $ins_product['consume_type']       = $consume_type;
        $ins_product['recommend_type']     = $recommend_type;
        $ins_product['type']			   = $integral_type;

        $log_data['cust_id'] = $cust_id; //商家id
        $log_data['admin_name'] = $_SESSION['curr_login'];//操作人名称
        $log_data['add_time'] = date('Y-m-d H:i:s',time());//操作时间

        $uni_condition = array('product_id'=>$product_id,'cust_id'=>$cust_id,'pros_id'=>-1,'type'=>$integral_type);
        $check_id  = $this->check_unique_data(WSY_SHOP.'.integral_setting_product',$uni_condition);
        //插入product表
        if(empty($check_id)){
        	$ins_product['pros_id']	= -1;
        	$data = $this->db->autoExecute(WSY_SHOP.'.integral_setting_product',$ins_product, 'insert');
            $log_data['remark']		= '添加常量积分产品'.$product_id;//描述
            $log_data['data_json'] 	= '';//操作前的数据

        }else{
        	$data = $this->db->autoExecute(WSY_SHOP.'.integral_setting_product',$ins_product, 'update','id = "'.$check_id.'"');
            $log_data['remark'] = '修改常量积分产品'.$product_id;//描述
            $log_data['data_json'] = '';//操作前的数据
        }
        //插入操作日志
        $this->insert_admin_log($log_data);
        if( $integral_type == 0 ){          //商城才加
        	//插入product_index表
	        $index_condi = array('cust_id'=>$cust_id,'act_id'=>-1,'pid'=>$product_id,'type'=>0);
	        $index_id  = $this->check_unique_data(WSY_SHOP.'.integral_product_index',$index_condi);
	        if(empty($index_id)){
	        	$index_condi['createtime'] = date('Y-m-d H:i:s');
	        	$data = $this->db->autoExecute(WSY_SHOP.'.integral_product_index',$index_condi, 'insert');
	        }
        }

        $pros_sql  = 'select * from weixin_commonshop_product_prices where customer_id ="'.$cust_id.'" and product_id ="'.$product_id.'"';
        $pros_list = $this->db->getAll($pros_sql);
        $pros_list = $this->get_pro_name($pros_list);
        //var_dump($pros_list['0']);

        foreach ($pros_list as $key => $one_pro) {
    		$ins_product['pros_id']   = $one_pro['proids'];
        	$ins_product['pros_name'] = $one_pro['pros_name'];
        	$ins_product['type'] 	  = $integral_type;
        	//需判断是否存在该活动产品
        	$uni_condition['pros_id'] = "'".$one_pro['proids']."'";
        	$check_id  = $this->check_unique_data(WSY_SHOP.'.integral_setting_product',$uni_condition);
        	if(empty($check_id)){
        		$data = $this->db->autoExecute(WSY_SHOP.'.integral_setting_product',$ins_product, 'insert');
        	}else{
        		$data = $this->db->autoExecute(WSY_SHOP.'.integral_setting_product',$ins_product, 'update','id ="'.$check_id.'"');
        	}

        }

        //var_dump(array('errcode'=>0,'errmsg'=>'保存成功'));
        return array('errcode'=>0,'errmsg'=>'保存成功');
	}

	/*
	 * 删除常量积分产品，
	 * $Author: wuhaoliang $
	 * 2017-09-04  $
	 */
	function m_del_integral_product($data=array()){
		//获取并分解数据
        $product_id 	= $data['p_id'];
        $cust_id   		= $data['cust_id'];
        $integral_type 	= $data['integral_type'];

        if( $integral_type == 0 ){		//商城才删
        	$del_sql = 'delete from '.WSY_SHOP.'.integral_product_index where cust_id ="'.$cust_id.'" and pid = "'.$product_id.'" and type = 0';
        	$this->db->query($del_sql) ;
        }
       
        $product_sql = 'select product_name from '.WSY_SHOP.'.integral_setting_product where type="'.$integral_type.'" and product_id="'.$product_id.'" and cust_id="'.$cust_id.'" and isvalid = 1';
		//echo $product_sql;
        $product_name  = $this->db->getOne($product_sql);
        if(empty($product_name)){
        	//返回码需统一
        	return array('errcode'=>601,'errmsg'=>'该产品产品已经删除');
        }

        $update_data['isvalid'] = 0;

        $data = $this->db->autoExecute(WSY_SHOP.'.integral_setting_product',$update_data, 'update','product_id ="'.$product_id.'"');
        //var_dump($data);
        return array('errcode'=>0,'errmsg'=>'删除成功');
	}

	/*
	 * 添加活动积分产品
	 * $Author: wuhaoliang $
	 * 2017-08-25  $
	 */
	function m_add_activity_integral_product($data=array()){
		//获取并分解数据
        $product_id = $data['p_id'];
        $cust_id    = $data['cust_id'];
        $act_id     = $data['act_id'];
        $consume_integral   = $data['consume_integral'];
        $recommend_integral = $data['recommend_integral'];
        $integral_type      = $data['integral_type'];
        if(empty($product_id)){
        	//返回码需统一
        	return array('errcode'=>505,'errmsg'=>'缺失产品id，或传输格式不对');
        }
        $product_sql = 'select product_name from '.WSY_SHOP.'.integral_setting_product isp where isp.product_id="'.$product_id.'" and isp.cust_id="'.$cust_id.'" and isp.type = 0 and isp.isvalid =1 and isp.product_id not in (select iap.product_id from '.WSY_SHOP.'.integral_activity_product iap  inner join '.WSY_SHOP.'.integral_activity ia on iap.act_id = ia.act_id  where iap.isvalid = 1 and ia.isvalid = 1 and iap.cust_id ="'.$cust_id.'" and ia.cust_id = "'.$cust_id.'" and ia.act_type = 2 and (ia.status = 1 or ia.status = 0))';
        $product_name  = $this->db->getOne($product_sql);
        if(empty($product_name)){
        	//返回码需统一
        	return array('errcode'=>505,'errmsg'=>'产品id'.$product_id.'有误');

        }
        $act_sql  = 'select act_name from '.WSY_SHOP.'.integral_activity where act_id="'.$act_id.'" and cust_id="'.$cust_id.'" and act_type=0 and isvalid =1';
        $act_name = $this->db->getOne($act_sql);
        if(empty($act_name)){
        	//返回码需统一
        	//var_dump('ciciciciciciciccici');
        	return array('errcode'=>506,'errmsg'=>'活动id'.$act_id.'有误');

        }
        $ins_product  = array();

        $ins_product['product_id'] = $product_id;
        $ins_product['cust_id']    = $cust_id;
        $ins_product['act_id']     = $act_id;
        $ins_product['isvalid']    = 1;

        $ins_product['product_name']       = $product_name;
        $ins_product['consume_integral']   = $consume_integral;
        $ins_product['recommend_integral'] = $recommend_integral;
        $ins_product['consume_type'] 	   = $integral_type;
        $ins_product['recommend_type']     = $integral_type;

        $log_data['cust_id'] = $cust_id; //商家id
        $log_data['admin_name'] = $_SESSION['curr_login'];//操作人名称
        $log_data['add_time'] = date('Y-m-d H:i:s',time());//操作时间

        //需判断是否存在该活动产品
        $uni_condition = array('product_id'=>$product_id,'cust_id'=>$cust_id,'isvalid'=>1,'act_id'=>$act_id);
        $check_id  = $this->check_unique_data(WSY_SHOP.'.integral_activity_product',$uni_condition);
        if(empty($check_id)){
			$ins_product['add_time']   = date("Y-m-d H:i:s");
			$ins_product['is_edit']   = 0;			//积分活动产品   在发布后添加只能修改一次  所以插入的时候is_edit设置为0 ，修改的时候设置为1
        	$data = $this->db->autoExecute(WSY_SHOP.'.integral_activity_product',$ins_product,'insert');
            $log_data['remark'] = '添加活动积分产品'.$product_id;//描述
            $log_data['data_json'] = '';//操作前的数据
        }else{
			$ins_product['is_edit']   = 1;
        	$data = $this->db->autoExecute(WSY_SHOP.'.integral_activity_product',$ins_product,'update','id ="'.$check_id.'"');
            $log_data['remark'] = '修改活动积分产品'.$product_id;//描述
            $log_data['data_json'] = '';//操作前的数据
        }

        //插入product_index表
        $index_condi = array('cust_id'=>$cust_id,'act_id'=>$act_id,'pid'=>$product_id,'type'=>1);
        $index_id  = $this->check_unique_data(WSY_SHOP.'.integral_product_index',$index_condi);
        if(empty($index_id)){
        	$index_condi['createtime'] = date('Y-m-d H:i:s');
        	$data = $this->db->autoExecute(WSY_SHOP.'.integral_product_index',$index_condi, 'insert');
        }

        //添加操作日志
        $this->insert_admin_log($log_data);

        //var_dump(array('errcode'=>0,'errmsg'=>'保存成功'));
        return array('errcode'=>0,'errmsg'=>'保存成功');

	}

	/*
	 * 删除活动积分产品
	 * $Author: wuhaoliang $
	 * 2017-08-25  $
	 */

	function m_del_activity_product($data=array()){
		//获取并分解数据
        $product_id = $data['p_id'];
        $cust_id    = $data['cust_id'];
        $act_id     = $data['act_id'];

        //查询该活动类型并判断是否存在
		$sql_act = 'select act_type from '.WSY_SHOP.'.integral_activity where act_id ="'.$act_id.'" and cust_id ="'.$cust_id.'" and isvalid = 1 and (act_type = 0 or act_type = 2)';

		$this_act_type =$this->db->getOne($sql_act)['act_type'];

		if($this_act_type == 0){
			$sql_table = ''.WSY_SHOP.'.integral_activity_product';
		}else if($this_act_type == 2){
			$sql_table = ''.WSY_SHOP.'.integral_exchange_product';
		}else{
			return array('errcode'=>505,'errmsg'=>'活动id有误，或者已经被删除');
		}

		$condition['cust_id']= $cust_id;
		$condition['isvalid']= 1;
		$condition['act_id'] = $act_id ;
		$condition['product_id']= $product_id;
		$res_id = $this->check_unique_data($sql_table,$condition);
		if(empty($res_id)){
			return array('errcode'=>505,'errmsg'=>'该活动产品已经被删除');
		}else{
			$update_data['isvalid']= 0;
			$data = $this->db->autoExecute($sql_table,$update_data,'update','id = "'.$res_id.'"');

			$del_sql = 'delete from '.WSY_SHOP.'.integral_product_index where cust_id ="'.$cust_id.'" and pid = "'.$product_id.'" and act_id = "'.$act_id.'" and type = 1';
        	$this->db->query($del_sql) ;

		}

	

		return array('errcode'=>0,'errmsg'=>'删除成功');

	}



	/*
	 * 添加兑换活动产品
	 * $Author: wuhaoliang $
	 * 2017-08-25  $
	 */
	function m_add_integral_exchange_product($data=array()){
		//获取并分解数据
        $product_id = $data['p_id'];
        $cust_id    = $data['cust_id'];
        $act_id     = $data['act_id'];
        $integral   = $data['integral'];
		$store_integral   = $data['store_integral'];
        $money      = $data['money'];
        $stock      = $data['stock'];

       	$product_sql = "select wcp.name
				from weixin_commonshop_products wcp
				where wcp.customer_id='".$cust_id."' and wcp.isvalid = 1 and isout = 0
				and wcp.id ='".$product_id."' and wcp.id not in (select iap.product_id from ".WSY_SHOP.".integral_activity_product iap  inner join ".WSY_SHOP.".integral_activity ia on iap.act_id = ia.act_id  where iap.isvalid = 1 and ia.isvalid = 1 and iap.cust_id ='".$cust_id."' and ia.cust_id = '".$cust_id."' and ia.act_type = 0 and (ia.status = 1 or ia.status = 0))";

		$product_name = $this->db->getOne($product_sql);
		//var_dump($product_name);
		if(empty($product_name)){
        	//返回码需统一
        	return array('errcode'=>505,'errmsg'=>'产品id有误');
        }
        $act_sql  = 'select act_name from '.WSY_SHOP.'.integral_activity where act_id="'.$act_id.'" and cust_id="'.$cust_id.'" and act_type=2 and isvalid =1';
        $act_name = $this->db->getOne($act_sql);
        if(empty($act_name)){
        	//返回码需统一
        	//var_dump('ciciciciciciciccici');
        	return array('errcode'=>506,'errmsg'=>'活动id有误');

        }

        $ins_product  = array();

        $ins_product['product_id'] = $product_id;
        $ins_product['cust_id']    = $cust_id;
        $ins_product['act_id']     = $act_id;
        $ins_product['isvalid']    = 1;
        $ins_product['product_name'] = $product_name;
        $ins_product['integral']   = $integral;
        $ins_product['store_integral']   = $store_integral;
        $ins_product['money']      = $money;
        $ins_product['stock']      = $stock;

        //var_dump($ins_product);
        $log_data['cust_id'] = $cust_id; //商家id
        $log_data['admin_name'] = $_SESSION['curr_login'];//操作人名称
        $log_data['add_time'] = date('Y-m-d H:i:s',time());//操作时间

        //需判断是否存在该活动产品
 		$uni_condition = array('product_id'=>$product_id,'cust_id'=>$cust_id,'isvalid'=>1,'act_id'=>$act_id);
        $check_id  = $this->check_unique_data(WSY_SHOP.'.integral_exchange_product',$uni_condition);
        if(empty($check_id)){
			$ins_product['add_time']   = date("Y-m-d H:i:s");
			$ins_product['is_edit']   = 0;			//兑换活动产品   在发布后添加只能修改一次  所以插入的时候is_edit设置为0 ，修改的时候设置为1
       		$data = $this->db->autoExecute(WSY_SHOP.'.integral_exchange_product',$ins_product,'insert');
            $log_data['remark'] = '添加兑换活动产品'.$product_id;//描述
            $log_data['data_json'] = '';//操作前的数据
       	}else{
			$ins_product['is_edit']   = 1;
       		$data = $this->db->autoExecute(WSY_SHOP.'.integral_exchange_product',$ins_product,'update','id="'.$check_id.'"');
            $log_data['remark'] = '修改兑换活动产品'.$product_id;//描述
            $log_data['data_json'] = '';//操作前的数据
       	}

       	//插入product_index表
        $index_condi = array('cust_id'=>$cust_id,'act_id'=>$act_id,'pid'=>$product_id,'type'=>2);
        $index_id  = $this->check_unique_data(WSY_SHOP.'.integral_product_index',$index_condi);
        if(empty($index_id)){
        	$index_condi['createtime'] = date('Y-m-d H:i:s');
        	$data = $this->db->autoExecute(WSY_SHOP.'.integral_product_index',$index_condi, 'insert');
        }
        //添加操作日志
        $this->insert_admin_log($log_data);
        //var_dump(array('errcode'=>0,'errmsg'=>'保存成功'));
        return array('errcode'=>0,'errmsg'=>'保存成功');

	}


	/*
	 * 检查单一表中是否存在旧数据,用于插入前检测
	 * 用法：$tables 数据表 $condition 条件array()
	 * 返回：存在则返回id  不存在则返回 -1
	 * $Author: wuhaoliang $
	 * 2017-08-25  $
	 */
	function check_unique_data($tables,$condition){
		$where = '';

		foreach( $condition as $k => $v ){
			if( $k == 'auth_users' ){
				$where .= ' AND '.$v;
				continue;
			}
			$where .= ' AND '.$k.' = "'.$v.'"';
		}
		$where = substr($where,4);
		$where =' where '.$where;

		$sql = ' select id from '.$tables.$where;

		$id  = $this->db->getOne($sql);

		return $id;
	}


	/*
	 * 获取所有产品类型
	 * $Author: wuhaoliang $
	 * 2017-09-04  $
	 */
	function get_all_product_type($data=array()){

		$customer_id = $data['cust_id'];
		$first_type  = array();
		$first_type_id_str='';

		$type_query='SELECT id,name,parent_id From weixin_commonshop_types where isvalid = 1 AND is_shelves =1 AND parent_id = -1 AND customer_id="'.$customer_id.'"';


		$type_result = $this->db->getALL($type_query);
		foreach ($type_result as $key_one => $one_first) {
			$first_type_id_str .=$one_first['id'].',';
			$list['first_type'][] = array('name'=>$one_first['name'],'id'=>$one_first['id']);
		}


		if($first_type_id_str!= ''){
			$first_type_id_str= substr($first_type_id_str,0,strlen($first_type_id_str)-1);

			$sec_type_query   ='SELECT id,name,parent_id From weixin_commonshop_types where isvalid = 1 AND is_shelves =1 AND parent_id in ("'.$first_type_id_str.'") AND customer_id="'.$customer_id.'"';

			$sec_type_result = $this->db->getALL($sec_type_query);
			foreach ($sec_type_result as $key_two => $one_sec) {
				foreach ($list['first_type'] as $key_f => $one_f) {
					if($one_sec['parent_id'] == $one_f['id']){
						$list['first_type'][$key_f]['son'][]=array('id'=>$one_sec['id'],'name'=>$one_sec['name']);
						$list['first_type'][$key_f]['son_str'].=$one_sec['id'].',';
					}
				}
			}
		}
		return $list;
	}


	/*
	 * 获取产品的分类名称字符串
	 * 用法：查询产品时，获取type_ids字段，并将查询结果直接作为参数传入
	 * 返回：array(0=>array('type_name'=>'/*****'));
	 * $Author: wuhaoliang $
	 * 2017-08-24  $
	 */
	function get_type_name($list){
		foreach ($list as $key => $one) {
			$temp_type='';
			$type_ids = $one['type_ids'];
			$type_ids = trim($type_ids,",");
			if(strstr($type_ids, ',')){
				$type_arr = explode(",",$type_ids);
			}else{
				if ($type_ids != '') {
					$type_arr = array($type_ids);
				}
			}
			foreach($type_arr as $val_id){
				$sql_type   = "select name from weixin_commonshop_types where id='".$val_id."'";
				$temp_type .='/';
				$temp_type .= $this->db->getOne($sql_type);
			}
			$list[$key]['type_name']=$temp_type;
		}
		return $list;
	}

	/*
	 * 获取产品的属性名称字符串
	 * 用法：查询产品时，获取proids字段，并将查询结果直接作为参数传入
	 * 返回：array(0=>array('pros_name'=>'/*****'));
	 * $Author: wuhaoliang $
	 * 2017-08-24  $
	 */
	function get_pro_name($list){
		foreach ($list as $key => $one) {
			$temp_type= '';
			$pro_ids = $one['proids'];
			if(!empty($pro_ids )){
				if(strpos($pro_ids,'_') === false ){
					$sql_type   = "select name from weixin_commonshop_pros where id='".$pro_ids."'";
					$temp_type .= $this->db->getOne($sql_type);

				}else{
					$pro_ids = str_replace('_',',',$pro_ids);

					$sql_type   = "select name from weixin_commonshop_pros where id in('".$pro_ids."')";

					$type_list  = $this->db->getALL($sql_type);
					foreach ($type_list as $key_2 => $one_p) {
						$temp_type .= $one_p['name'].'-';
					}

					$temp_type = substr($temp_type,0,-1);

				}
			}

			$list[$key]['pros_name']=$temp_type;

		}
		return $list;
	}

	/*
	 * 获取单个产品的属性名称字符串
	 * 用法：查询产品时，获取proids字段，并将查询结果直接作为参数传入
	 * 返回：array(0=>array('pros_name'=>'/*****'));
	 * $Author: liuzhongxuan $
	 * 2017-08-24  $
	 */
	/*
	 * 
	 *
	 */

	function get_one_integral_product($data=array()){

		$product_id    		= $data['product_id'];
		$cust_id    		= $data['cust_id'];
		$integral_type      = $data['integral_type'];
		$gift_set_type   	= 0;
		$gift_set_value   	= 0;

		$sql       = "select basic_json,store_json from ".WSY_SHOP.".integral_setting where cust_id = '$cust_id'";
    	$result    = $this->db->getRow ($sql);
    	if($integral_type == 1){
    		$result['basic_json'] = json_decode($result['store_json'],TRUE);
			$gift_set_type 		  = $result['basic_json']['gift_set_type'];//全局赠送设置（类型：1、按比例，2按固定积分）
			$gift_set_value 	  = $result['basic_json']['gift_set_value'];//全局赠送设置（值：类型1为比例，类型2为积分）
    	}else{
    		$result['basic_json'] = json_decode($result['basic_json'],TRUE);
			$gift_set_type 		  = $result['basic_json']['gift_set_type'];//全局赠送设置（类型：1、按比例，2按固定积分）
			$gift_set_value 	  = $result['basic_json']['gift_set_value'];//全局赠送设置（值：类型1为比例，类型2为积分）
    	}
		

		$pro_arr = array();
		$que = "select pros_id from ".WSY_SHOP.".integral_setting_product where product_id='$product_id' and cust_id = '$cust_id' and isvalid = true and type ='".$integral_type."'";
		$pros_id_arr = $this->db->getAll($que);
		//var_dump($que);
			foreach ($pros_id_arr as $key => $one ) {
			//var_dump($one['pros_id']);
			if($one['pros_id'] != ''){
				if($one['pros_id'] == -1){
				$sql = "select isp.id,isp.product_name,isp.pros_name,isp.mode,isp.consume_integral,isp.recommend_integral,isp.consume_type,isp.recommend_type,wcp.default_imgurl,wcp.orgin_price,wcp.now_price,wcp.for_price,wcp.unit,wcp.weight,wcp.storenum,wcp.type_ids from ".WSY_SHOP.".integral_setting_product isp inner join weixin_commonshop_products wcp on isp.product_id = wcp.id where isp.product_id = '$product_id' and isp.type ='".$integral_type."' and isp.isvalid = 1 and wcp.isvalid = 1 and isp.pros_id = '{$one['pros_id']}'";

					$list = $this->db->getRow($sql);
					$pro_arr['main']['id'] 						= $list['id'];//'积分产品id',
					$pro_arr['main']['product_name'] 			= $list['product_name'];//'产品名称（冗余字段）',
					$pro_arr['main']['pros_name'] 				= $list['pros_name'];//'属性名称',
					$pro_arr['main']['mode'] 					= $list['mode'];//'模式：0全局 1自定义',
					$pro_arr['main']['consume_integral'] 		= $list['consume_integral'];//'消费积分(如果有百分号表示比例，否则表示数字)'
					$pro_arr['main']['recommend_integral'] 		= $list['recommend_integral'];//'推荐积分(如果有百分号表示比例，否则表示数字)',
					$pro_arr['main']['consume_type'] 			= $list['consume_type'];//'赠送类型：1比例2固定值',
					$pro_arr['main']['recommend_type'] 			= $list['recommend_type'];//'推荐类型：1比例2固定值',
					$pro_arr['main']['default_imgurl'] 			= $list['default_imgurl'];//产品图片
					$pro_arr['main']['orgin_price'] 			= $list['orgin_price'];//原价
					$pro_arr['main']['now_price'] 				= $list['now_price'];//现价
					$pro_arr['main']['for_price'] 				= $list['for_price'];//成本
					$pro_arr['main']['unit'] 					= $list['unit'];//单位
					$pro_arr['main']['weight'] 					= $list['weight'];//重量
					$pro_arr['main']['storenum'] 				= $list['storenum'];//库存
					$list['gift_set_type'] 						= $gift_set_type;//全局赠送设置（类型：1、按比例，2按固定积分）
					$list['gift_set_value'] 					= $gift_set_value;//全局赠送设置（值：类型1为比例，类型2为积分）
					//extract($list, EXTR_SKIP);

					//查询类型名称
					$type_data[0] = $list;
					$list = $this->get_type_name($type_data);
					$pro_arr['main'] = $list[0];
					//var_dump($pro_arr);
					//var_dump($list);
					//echo "////";
				}else{
					$query="select isp.id,isp.product_name,isp.pros_name,isp.consume_integral,isp.recommend_integral,isp.consume_type,isp.recommend_type,pp.proids,pp.orgin_price,pp.now_price,pp.storenum,pp.unit,pp.weight,pp.for_price,cp.name from ".WSY_SHOP.".integral_setting_product isp inner join weixin_commonshop_product_prices pp on isp.pros_id = pp.proids left join weixin_commonshop_pros cp on pp.proids=cp.id where isp.product_id='$product_id' and isp.isvalid=1 and isp.pros_id = '{$one['pros_id']}' and isp.type ='".$integral_type."' and pp.product_id = '$product_id' and pp.proids = '{$one['pros_id']}' and isp.cust_id='$cust_id'";
					$list2 = $this->db->getROW($query);
					$pro_arr['sub'][$key]['id'] 					= $list2['id'];//'积分产品id',
					$pro_arr['sub'][$key]['product_name'] 			= $list2['product_name'];//'产品名称（冗余字段）',
					$pro_arr['sub'][$key]['pros_name'] 				= $list2['pros_name'];//'属性名称',

					$pro_arr['sub'][$key]['mode'] 					= $list2['mode'];//'模式：0全局 1自定义',
					$pro_arr['sub'][$key]['consume_integral'] 		= $list2['consume_integral'];//'消费积分(如果有百分号表示比例，否则表示数字)'
					$pro_arr['sub'][$key]['recommend_integral'] 	= $list2['recommend_integral'];//'推荐积分(如果有百分号表示比例，否则表示数字)',
					$pro_arr['sub'][$key]['consume_type'] 			= $list2['consume_type'];//'赠送类型：1比例2固定值',
					$pro_arr['sub'][$key]['recommend_type'] 		= $list2['recommend_type'];//'推荐类型：1比例2固定值',
					$pro_arr['sub'][$key]['default_imgurl'] 		= $list2['default_imgurl'];//产品图片
					$pro_arr['sub'][$key]['orgin_price'] 			= $list2['orgin_price'];//原价
					$pro_arr['sub'][$key]['now_price'] 				= $list2['now_price'];//现价
					$pro_arr['sub'][$key]['for_price'] 				= $list2['for_price'];//成本
					$pro_arr['sub'][$key]['unit'] 					= $list2['unit'];//单位
					$pro_arr['sub'][$key]['weight'] 				= $list2['weight'];//重量
					$pro_arr['sub'][$key]['storenum'] 				= $list2['storenum'];//库存
					//var_dump($one['pros_id']);
					//var_dump($query);
					//var_dump($pro_arr);
				}
			}
		}
		//var_dump($pro_arr);

		$res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$pro_arr);

    	return $res;

	}


	/*
	* 积分产品保存操作
	* 参数：id：'积分id'；
			is_commission：'是否参与分佣：0不参加 1参加'；
			mode：'模式：0全局 1自定义'；
			consume_integral：'消费积分(如果有百分号表示比例，否则表示数字)'；
			recommend_integral：'推荐积分(如果有百分号表示比例，否则表示数字)'；
			consume_type：'赠送类型：1比例2固定值'；
			recommend_type：'推荐类型：1比例2固定值'；
	* $Author: liuzhongxuan $
	* 2017-08-24  $
	*/

	function saveintegralproduct($parm=array()){
		$where = "";
		$data = array();
		$sql = "";
		//拼装更新数据
		foreach ($parm['save_arr'] as $key => $value )
		{
			$data['mode'] = $value['mode'];
			if($data['mode'] == 0){
				$data['consume_type'] 	= $parm['gift_set_type'];
				$data['recommend_type'] = $parm['gift_set_type'];
			}else{
				$data['consume_type']	= $value['consume_type'];
				$data['recommend_type'] = $value['recommend_type'];
			}
			$data['consume_integral']	= $value['consume_integral'];
			$data['recommend_integral'] = $value['recommend_integral'];
			if ($value['id'] != '') 
			{
				$sql .= "UPDATE ".WSY_SHOP.".integral_setting_product SET mode='{$data["mode"]}', consume_type='{$data["consume_type"]}', recommend_type='{$data["recommend_type"]}', consume_integral='{$data["consume_integral"]}', recommend_integral='{$data["recommend_integral"]}' WHERE id = '{$value['id']}';";
			}
		}
		$this->db->tran_begin(); //开启事物
		try
		{
			$this->db->query($sql);
			$return = true;
		}
		catch(Exception $e)
		{
			$return = false;
			$this->db->tran_rollback(); //回滚
		}
		$this->db->tran_commit(); //提交
		return $return;
	}

    /*
	 * 获取用户积分明细
	 * $Author: djy $
	 * 2017-08-28  $
    */
    function integral_log($data = array())
    {
    	$cust_id        = $data['cust_id'];
    	$user_id        = $data['user_id'];
    	$type        = $data['type'];//积分明细类型：0购物 1签到 2关注 3绑定手机号',
        $count        = $data['count'];
        $page        = $data['page'];
        if($page < 1){
            $page = 1;
        }
        $start_time     = $data['start_time'];
        $end_time     = $data['end_time'];
    	if(empty($user_id)) return array('errcode' => 400,'errmsg'=>'缺少参数user_id');

        $where = " user_id = $user_id and cust_id='$cust_id' ";

        if(isset($type)){
			$where .= " AND type=".$type;
		}

        if(isset($start_time) && !empty($start_time)){
            $where .= " and UNIX_TIMESTAMP(add_time) >='".strtotime($start_time)."'";
        }
    	if(isset($end_time) && !empty($start_time)){
            $where .= " and UNIX_TIMESTAMP(add_time) <='".strtotime($end_time)."'";
        }

        $sql       = "select log_id,number,add_time from ".WSY_SHOP.".integral_log where $where";
    	$sql_total  = "select count(log_id) as total from ".WSY_SHOP.".integral_log where $where";

    	$all        = $this->db->getRow($sql_total);

        $sql .= " ORDER BY add_time DESC ";
		if( $page != '' && $count != '' ){
			$sql .= " LIMIT ".($page-1)*$count.",".$count;
		}else{
            $sql .= " LIMIT 0,20";
        }

    	$result    = $this->db->getAll ($sql);


        if($result != false){
            $res1['data'] = $result;
        }else{
            $res1['data'] = [];
        }
        $res1['total'] = $all['total'];
        $res1['page']  = $page;
		$res1['list_num']  = count($result);
    	$res1   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$res1) : $res = array('errcode' => 400,'errmsg'=>'获取失败');
    	return $res;
    }




    /*
	 * 获取用户积分明细
	 * $Author: djy $
	 * 2017-08-28  $
    */
    function integral_sign_log_sum($data = array())
    {
    	$cust_id        = $data['cust_id'];
    	$user_id        = $data['user_id'];
    	$type        = $data['type'];//积分明细类型：0购物 1签到 2关注 3绑定手机号',
        $start_time     = $data['start_time'];
        $end_time     = $data['end_time'];
    	if(empty($user_id)) return array('errcode' => 400,'errmsg'=>'缺少参数user_id');

        $sign_count = 0;
        $sign_numbers = 0;
        $sql       = "select count(log_id) as sign_count,sum(number) as sign_numbers from ".WSY_SHOP.".integral_log where user_id = '$user_id' and cust_id='$cust_id' ";

        if(isset($type)){
			$sql .= " AND type='".$type."'";
		}

        if(isset($start_time) && !empty($start_time)){
            $sql .= " and UNIX_TIMESTAMP(add_time) >='".strtotime($start_time)."'";
        }
    	if(isset($end_time) && !empty($start_time)){
            $sql .= " and UNIX_TIMESTAMP(add_time) <='".strtotime($end_time)."'";
        }

    	$result    = $this->db->getROW($sql);
    	$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取失败');
    	return $res;
    }

	/*
	* 积分设置保存
	* $Author: djy $
	* 2017-08-28  修改：2018-1-6$
	*/
	/*活动操作*/
	function save_integral_setting($parm=array()){

        $cust_id        = $parm['cust_id'];

        $sql  = 'select id,shop_onoff,reward_onoff,basic_json,aftersale_onoff from '.WSY_SHOP.'.integral_setting where cust_id = "'.$cust_id.'"';
		$data = $this->db->getRow($sql);
        $id = $data['id'];
        $data_json['shop_onoff'] = $data['shop_onoff'];
        $data_json['reward_onoff'] = $data['reward_onoff'];
        $data_json['aftersale_onoff'] = $data['aftersale_onoff'];
        $data_json['basic_json'] = $data['basic_json'];

         $log_data['cust_id'] = $cust_id; //商家id
         $log_data['admin_name'] = $_SESSION['curr_login'];//操作人名称
         $log_data['add_time'] = date('Y-m-d H:i:s',time());//操作时间
         $diyname = _get_diyname($cust_id);

		if($id)
		{
			$basic_arr=json_decode($parm['basic_json'],true);
			$cleartime=strtotime($basic_arr['clear_integral_time']);
			$day=$basic_arr['clear_integral_notice']['time1']['ahead_days'];
			$notice_time=$cleartime-$day*24*60*60;
			$notice_time=date("Y-m-d",$notice_time);
			$notice_time=$notice_time.substr($basic_arr['clear_integral_notice']['time1']['notice_time'],10,18);
			$basic_arr['clear_integral_notice']['time1']['notice_time']=$notice_time;
			$parm['basic_json']=json_encode($basic_arr, JSON_UNESCAPED_UNICODE);
			$result = $this->db->autoExecute(WSY_SHOP.'.integral_setting', $parm, 'update',"id = '$id'") ;
            //$log_data['remark'] = '更新积分配置';//描述
            $log_data['remark'] = '更新'.$diyname['shop_integral_name'].'配置';//描述
            $log_data['data_json'] = json_encode($data_json, JSON_UNESCAPED_UNICODE);//操作前的数据
            $this->insert_admin_log($log_data);

             //更新赠送积分奖励，默认全局比例的产品修改 报障19435 
            $a = $parm['basic_json'];
            $b = json_decode($a, true);
            $gift_set_type  = $b['gift_set_type'];  //赠送方式 0比例 1固定
            $gift_set_value = $b['gift_set_value']; //赠送方式数量或者比例
            $consume_type = $gift_set_type+1;
            $sql = "update ".WSY_SHOP.".integral_setting_product set consume_integral=".$gift_set_value.",recommend_integral=".$gift_set_value." where mode=0 and cust_id=".$cust_id." and isvalid=1 and consume_type=".$consume_type."";
            $this->db->query($sql);
		}
		else
		{
			$result = $this->db->autoExecute(WSY_SHOP.'.integral_setting',$parm, 'insert') ;
            //$log_data['remark'] = '积分配置';//描述
            $log_data['remark'] = $diyname['shop_integral_name'].'配置';//描述
            $log_data['data_json'] = '';//操作前的数据
            $this->insert_admin_log($log_data);
		}

		return $result;
	}

    /*
	 * 获取积分设置数据
	 * $Author: djy $
	 * 2017-08-28  $
    */
    function integral_setting_details($data = array())
    {
    	$cust_id        = $data['cust_id'];
    	if(empty($cust_id)) return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');

    	$sql       = "select basic_json,shop_onoff,reward_onoff,aftersale_onoff,store_json,store_onoff from ".WSY_SHOP.".integral_setting where cust_id = '$cust_id'";
    	$result    = $this->db->getRow ($sql);
    	$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取失败');
    	return $res;
    }

    /*
	 * 获取用户积分
	 * $Author: wuhaoliang $
	 * 2017-08-28  $
     */
    function get_user_integral($data = array()){

    	$cust_id =  $data['cust_id'];
    	$user_id =  $data['user_id'];

    	$sql = 'select user_id,integral,frost_integral from  moneybag_t where user_id ="'.$user_id.'" and isvalid = 1 and customer_id = "'.$cust_id.'"';

    	$result  = $this->db->getALL($sql)[0];

    	return $result;
    }

     /*
	 * 获取用户积分明细列表
	 * $Author: wuhaoliang $
	 * 2017-08-28  $
     */
    function m_get_user_integral($data = array()){

    	$cust_id =  $data['cust_id'];
    	$user_id =  $data['user_id'];
    	$type    =  $data['type'];
    	$month    =  $data['month'];
    	$page    =  $data['page'];
    	$count    =  $data['count'];

        $sql = "select log_id,type,number,add_time,order_id,remark from ".WSY_SHOP.".integral_log where type not in (9,10,12) and user_id = '$user_id' and cust_id='$cust_id' ";

        if(!empty($type)){
            if($type == 1){
                $sql .= " AND number>0";
            }elseif($type == 2){
                $sql .= " AND number<0";
            }

		}

        if(!empty($month)){
            $sql .= " AND month(add_time) =$month";
		}

        $sql .= " ORDER BY add_time desc";
		if( $page != '' && $count != '' ){
			$sql .= " LIMIT ".($page-1)*$count.",".$count;
		}else{
            $sql .= " LIMIT 0,20";
        }

    	$result  = $this->db->getALL($sql);
        foreach($result as $key=>$val){
            $result[$key]['add_time'] = date('Y/m/d',strtotime($val['add_time']));
            $pos = strpos($result[$key]['remark'], "推荐:返还"); //判断是否是自己的订单，不是的话，不可以查看订单详情 报障19577
            if ($pos === false) {
			   $result[$key]['you_or_me'] = 1;
			} else {
			   $result[$key]['you_or_me'] = 0;
			}
        }

        $ji_sql = "select integral from moneybag_t where customer_id = '$cust_id' and user_id = '$user_id' and isvalid = 1 limit 1";

        $ji_res  = $this->db->getOne($ji_sql);
        $res1['user_integral'] = $ji_res;
        $res1['integral_log'] = $result;

        $result != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$res1) : $res = array('errcode' => 600,'errmsg'=>'获取失败');

    	return $res;
    }

     /*获取用户积分统计
	 *参数：cust_id：商家id ;user_name:用户名称；user_id：用户id count：条数；page：页数
	 * liuzhongxuan  $
	 * 2017-08-28  $
    */
    function integral_stat_user_list($data = array())
    {
    	require_once($_SERVER['DOCUMENT_ROOT']."/weixinpl/php-emoji/emoji.php");
    	extract ( $data );

    	if(!isset($cust_id)) return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');

    	//$count     = 20;
    	//$contidion = 1;
    	$contidion = " isu.cust_id = '$cust_id' and wu.isvalid = 1 ";

    	isset($count) ? $count : $count = 20;
    	isset($page)  ? $page  = $page*$count : $page = 0;
    	if(isset($search_user_name))   $contidion .= " and wu.weixin_name like '%".$search_user_name."%'";
    	if(isset($search_start_time)) $contidion .= " and UNIX_TIMESTAMP(isu.update_time) >='".strtotime($search_start_time)."'";
    	if(isset($search_end_time))   $contidion .= " and UNIX_TIMESTAMP(isu.update_time) <='".strtotime($search_end_time)."'";
    	if(!empty($search_user_id))   $contidion .= " and isu.user_id like '%".$search_user_id."%'";

    	$sql        = "select isu.id,isu.user_id,t.integral as balance,t.store_integral as store_balance,isu.sign_score,isu.input,isu.output,isu.clear_num,isu.clear_sum,wu.weixin_headimgurl,wu.weixin_name from ".WSY_SHOP.".integral_stat_user isu left join weixin_users wu on isu.user_id=wu.id left join moneybag_t as t on t.user_id=isu.user_id and t.isvalid = 1 where $contidion  order by isu.id desc limit $page,$count";

    	$sql_total  = "select count(isu.id) as total from ".WSY_SHOP.".integral_stat_user isu left join weixin_users wu on isu.user_id=wu.id where $contidion";
		//var_dump($sql);
    	$contidion .= " and t.isvalid=1 ";
		$sql        = "select isu.id,isu.user_id,t.integral as balance,t.store_integral as store_balance,isu.sign_score,isu.input,isu.output,isu.store_input,isu.store_output,isu.clear_num,isu.clear_sum,wu.weixin_headimgurl,wu.weixin_name from ".WSY_SHOP.".integral_stat_user isu left join weixin_users wu on isu.user_id=wu.id left join moneybag_t as t on t.user_id=isu.user_id and t.isvalid = 1 where t.customer_id=".$cust_id." and wu.customer_id=".$cust_id." and $contidion  order by isu.id desc limit $page,$count";
		//由于有些客户用户不同商家ID对应同样的用户ID问题，添加t.customer_id=".$cust_id." and wu.customer_id=".$cust_id." 报障ID 19165
    	$all        = $this->db->getRow($sql_total);
    	$result     = $this->db->getAll ($sql);
		$list_num = count($result);
		foreach ($result as $key => $value) {
			$value['weixin_name'] = emoji_empty($value['weixin_name']);//去掉表情包
			$result[$key] = $value;
		}

    	$result['total'] = $all['total'];
    	$result['page']  = $page;
		$result['list_num']  = $list_num;

    	$result != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 600,'errmsg'=>'获取失败');

    	return $res;

    }


    /*
	 * 后台获取单个用户积分明细
	 * $Author: wuhaoliang $
	 * 2017-09-05    (改)2017-11-1 $
    */
    function m_user_integral_log($data=array()){
    	//拆分数据
    	$cust_id        = $data['cust_id'];
    	$user_id        = $data['user_id'];
		$integral_type  = $data['integral_type'];     //积分明细类型：-1为全部 0为商城积分  1为门店积分
    	$type       	= $data['type'];               //明细类型：-1为全部 1为收入  2为支出  3签到收入
        $start_time     = $data['start_time'];
        $end_time       = $data['end_time'];
        $page    	    = $data['page'];
        $page_size      = $data['page_size'];

        $where = "where wil.user_id = '".$user_id."' and wil.cust_id = '".$cust_id."' ";

		switch ($integral_type)
		{
			case '-1':
				if($type == -1) $where .= ' ';
				if($type == 1) $where .= 'and wil.number >= 0';
				if($type == 2) $where .= 'and wil.number < 0';
				if($type == 3) $where .= 'and wil.type=1';
				break;
			case '0':
				if($type == -1) $where .= ' and wil.type not in (1,9,10,11)';
				if($type == 1) $where .= ' and wil.number >= 0 and wil.type not in (1,9,10,11)';
				if($type == 2) $where .= ' and wil.number < 0 and wil.type not in (1,9,10,11)';
				if($type == 3) $where .= ' and wil.type=1';
				break;
			case '1':
				if($type == -1) $where .= ' and wil.type in (9,10,12)';
				if($type == 1) $where .= 'and wil.number >= 0 and wil.type in (9,10,12)';
				if($type == 2) $where .= 'and wil.number < 0 and wil.type in (9,10,12)';
				if($type == 3) $where .= 'and wil.type=1';
				break;
			default:
				break;
		}

        if(!empty($start_time)){
            $where .= " and UNIX_TIMESTAMP(add_time) >='".strtotime($start_time)."'";
        }
    	if(!empty($end_time)){
            $where .= " and UNIX_TIMESTAMP(add_time) <='".strtotime($end_time)."'";
        }

        $count_sql = 'select count(distinct wil.log_id) as sum from '.WSY_SHOP.'.integral_log  wil left join weixin_commonshop_order_prices wcop on wil.order_id = wcop.batchcode '.$where;

        $all_num  = $this->db->getAll($count_sql)[0]['sum'];

		if($page < 1 || empty($page)){
			$page = 1;
		}else{
			$page = $page + 1;
		}
	    $count   = ceil($all_num/$page_size);
		if($page > $count){
			$page = $count;
		}

		//防止total=0的时候limit值为负数
		$limit = '';
		if(!empty($all_num)) {
			$offset = ($page - 1) * $page_size;
			$limit = ' limit ' . $offset . ',' . $page_size;
		}

		$sele_sql = 'select distinct wil.*,wcop.price from '.WSY_SHOP.'.integral_log  wil left join weixin_commonshop_order_prices wcop on wil.order_id = wcop.batchcode '.$where.' order by log_id desc '.$limit;
		//var_dump($sele_sql);
		$list     = $this->db->getAll($sele_sql);

		$list_num = count($list);

		return array("errcode"=>0,'errmsg'=>"数据获取成功",'datas'=>array('list'=>$list,'page_count'=>$count,'total'=>$all_num,'list_num'=>$list_num));

    }

    /*
	 * 积分活动统计
	 * $Author: djy $
	 * 2017-08-28  $
    */
    function integral_activity_statistics($data = array())
    {
    	$cust_id        = $data['cust_id'];
        $search_ptype = $data['search_ptype'];
        $count        = $data['count'];
        $page        = $data['page'];
        if($page < 1){
            $page = 1;
        }
    	if(empty($cust_id)) return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');

        $where = " isp.cust_id = '$cust_id'";


        if(!empty($data['search_pid']))
        {
            $where .= " and wcp.id ='".$data['search_pid']."'";
        }
        if(!empty($data['search_pname']))
        {
            $where .= " AND wcp.name like '%".$data['search_pname']."%'";
        }

        if( $search_ptype > 0 ){
			$typeson_id=array();
			/* 查找该分类的所有子分类 start */
			$sqltype = "SELECT id FROM weixin_commonshop_types WHERE customer_id='$cust_id' AND isvalid=true AND is_shelves=1 AND LOCATE(',".$search_ptype.",', gflag)>0 ";
            $product_types = $this->db->getAll($sqltype);
            foreach($product_types as $key => $value ){
                $child_id = $value['id'];

				$typeson_id[] = $child_id;
            }
			/* 查找该分类的所有子分类 end */

 			if(empty($typeson_id)){
				$typeson_id = $search_ptype;
			}else{
				array_push($typeson_id,$search_ptype);
				$typeson_id = implode(',',$typeson_id);
			}

			$where .= " and (";
			$typeson_id_arr = explode(",",$typeson_id);
			$typeson_id_count = count($typeson_id_arr);
			for( $j=0; $j<$typeson_id_count; $j++ ){
				$o_typeid = $typeson_id_arr[$j];
				if( $j == 0 ){
					$where .= "( LOCATE(',".$o_typeid.",', type_ids)>0)";
				}else{
					$where .= " or (LOCATE(',".$o_typeid.",', type_ids)>0)";
				}
			}
			$where .= ")";
		}

        if(!empty($data['search_actid']))
        {
            $where .= " and ia.act_id ='".$data['search_actid']."'";
        }
        if(!empty($data['search_actname']))
        {
            $where .= " AND act_name like '%".$data['search_actname']."%'";
        }
        if(!empty($data['search_actstatus']))
        {
            $where .= " and ia.status ='".$data['search_actstatus']."'";
        }
        if(!empty($data['search_acttype']))
        {
            $where .= " and act_type ='".$data['search_acttype']."'";
        }

        $sql = "select isp.act_id,act_name,act_type,status,default_imgurl,wcp.name as pname,wcp.id as pid,type_ids,now_price,sales_volume,number,price from ".WSY_SHOP.".integral_stat_product isp
                        inner join ".WSY_SHOP.".integral_activity ia on isp.act_id = ia.act_id
                        inner join weixin_commonshop_products wcp on isp.product_id = wcp.id
                        where $where ";
        $sql_total  = "select count(isp.id) as total from ".WSY_SHOP.".integral_stat_product isp
                        inner join ".WSY_SHOP.".integral_activity ia on isp.act_id = ia.act_id
                        inner join weixin_commonshop_products wcp on isp.product_id = wcp.id
                        where $where ";
        $all        = $this->db->getRow($sql_total);

        $sql .= " ORDER BY update_time DESC ";
		if( $page != '' && $count != '' ){
			$sql .= " LIMIT ".($page-1)*$count.",".$count;
		}else{
            $sql .= " LIMIT 0,20";
        }
        // var_dump($sql);
    	$result    = $this->db->getAll ($sql);

        foreach ( $result as $k => $v ) {
			$type_name = '';
			$type_ids = trim($v['type_ids'],',');
			if( $type_ids != '' ){
				$query_type = "SELECT name FROM weixin_commonshop_types WHERE customer_id='".$cust_id."' AND isvalid=true AND id IN (".$type_ids.")";
                $result_type = $this->db->getAll($query_type);
                foreach ( $result_type as $k2 => $v2 ) {
                    $type_name .= $v2['name']."/";
                }
				$type_name = substr($type_name,0,-1);

			}
			$result[$k]['type_name'] = $type_name;
		}
        
        $list_num = count($result);
        $result['list_num'] = $list_num;
        $result['total'] = $all['total'];
    	$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取失败');
    	return $res;
    }

     /*
	 * 获取积分兑换列表
	 * $Author: djy $
	 * 2017-08-29  $
     */
    function integral_exchange_product($data = array()){

    	$cust_id =  $data['cust_id'];
    	$orderby =  $data['orderby'];
    	$search_ptype =  $data['search_ptype'];
    	$page =  $data['page'];
        $search_name    = $data['search_name'];

        if(empty($page)){
        	
            $page = 0;
        }
    	$count =  $data['count'];
		$now    = date('Y-m-d H:i:s',time());

        $where = " iep.cust_id=$cust_id and iep.isvalid=1 and ia.status=1 and ia.isvalid=1 and ia.start_time<'".$now."' and ia.end_time>'".$now."' and stock>0 and wcp.isout=0 ";

        if(!empty($search_name)){
			$where .= " and wcp.name LIKE '%".$search_name."%' ";
		}

        if( $search_ptype > 0 ){
			$typeson_id=array();
			/* 查找该分类的所有子分类 start */
			$sqltype = "SELECT id FROM weixin_commonshop_types WHERE customer_id='$cust_id' AND isvalid=true AND is_shelves=1 AND LOCATE(',".$search_ptype.",', gflag)>0 ";
            $product_types = $this->db->getAll($sqltype);
            foreach($product_types as $key => $value ){
                $child_id = $value['id'];

				$typeson_id[] = $child_id;
            }
			/* 查找该分类的所有子分类 end */

 			if(empty($typeson_id)){
				$typeson_id = $search_ptype;
			}else{
				array_push($typeson_id,$search_ptype);
				$typeson_id = implode(',',$typeson_id);
			}

			$where .= " and (";
			$typeson_id_arr = explode(",",$typeson_id);
			$typeson_id_count = count($typeson_id_arr);
			for( $j=0; $j<$typeson_id_count; $j++ ){
				$o_typeid = $typeson_id_arr[$j];
				if( $j == 0 ){
					$where .= "( LOCATE(',".$o_typeid.",', type_ids)>0)";
				}else{
					$where .= " or (LOCATE(',".$o_typeid.",', type_ids)>0)";
				}
			}
			$where .= ")";
		}

        $sql = "select iep.product_id,integral,store_integral,money,stock,end_time,sales_volume,default_imgurl,wcp.name as pname,wcp.now_price,ia.only_type from ".WSY_SHOP.".integral_exchange_product iep
                inner join ".WSY_SHOP.".integral_activity ia on iep.act_id = ia.act_id
                left join ".WSY_SHOP.".integral_stat_product isp on iep.product_id = isp.product_id and iep.act_id = isp.act_id
                inner join ".WSY_PROD.".weixin_commonshop_products wcp on iep.product_id = wcp.id
                where $where  ";
        $sql_total  = "select count(iep.id) as total from ".WSY_SHOP.".integral_exchange_product iep
                inner join ".WSY_SHOP.".integral_activity ia on iep.act_id = ia.act_id
                left join ".WSY_SHOP.".integral_stat_product isp on iep.product_id = isp.product_id and iep.act_id = isp.act_id
                inner join ".WSY_PROD.".weixin_commonshop_products wcp on iep.product_id = wcp.id
                where $where  ";
	//	echo $sql;
    	$all        = $this->db->getRow($sql_total);

        if($orderby['type']=='sale'){
            $sql .= " ORDER BY sales_volume ".$orderby['value'].",iep.id ".$orderby['value']."";
        }elseif($orderby['type']=='integral'){
            $sql .= " ORDER BY integral ".$orderby['value'].",iep.id ".$orderby['value']."";
        }elseif($orderby['type']=='time'){
            $sql .= " ORDER BY iep.add_time ".$orderby['value'].",iep.id ".$orderby['value']."";
        }else{
            $sql .= " ORDER BY iep.add_time DESC,iep.id DESC ";
        }

		if( $page != '' && $count != '' ){
			$sql .= " LIMIT ".$page*$count.",".$count;
		}else{
            $sql .= " LIMIT 0,16";
        }


    	$result  = $this->db->getALL($sql);
        //将时间中的 - 改为 / 苹果不兼容 - 
		foreach ($result as $key => $one) {
			$result[$key]['end_time'] = str_replace('-','/',$one['end_time']);
		}
        $list_num = count($result);
        $result['total'] = $all['total'];
        $result['list_num'] = "$list_num";

        //查询配置
		$sql = "SELECT  basic_json,shop_onoff,store_json,store_onoff FROM ".WSY_SHOP.".integral_setting WHERE cust_id='{$cust_id}'";
		$cust_setting = $this->db->getRow($sql);

		$result['shop_onoff'] 	= !empty($cust_setting['shop_onoff'])?   $cust_setting['shop_onoff']  : 0;
		$result['store_onoff']	= !empty($cust_setting['store_onoff'])?  $cust_setting['store_onoff'] : 0;	

		$shop_set 	= json_decode($cust_setting['basic_json'],TRUE);
		$store_set 	= json_decode($cust_setting['store_json'],TRUE);


		$result['shop_int_name'] 	= !empty($shop_set['integral_name'])?   $shop_set['integral_name']  : '商城积分';
		$result['store_int_name'] 	= !empty($store_set['integral_name'])? 	$store_set['integral_name'] : '门店积分';

        $result != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 600,'errmsg'=>'获取失败');

    	return $res;
    }

     /*
	 * 管理员操作日志
	 * $Author: djy $
	 * 2017-09-1  $

     $data['cust_id'] 商家id
     $data['admin_name'] 操作人名称
     $data['remark'] 描述
     $data['add_time'] 操作时间
     $data['data_json'] 操作前的数据

     */
    function insert_admin_log($data = array()){

        $result = $this->db->autoExecute(WSY_SHOP.'.admin_log',$data, 'insert') ;

    }

     /*
	 * 读取操作日志
	 * $Author: djy $
	 * 2017-09-1  $
     */
    function read_admin_log($data = array())
    {
    	$cust_id        = $data['cust_id'];
    	$keyword        = $data['keyword'];
        $count        = $data['count'];
        $page        = $data['page'];
        if($page < 1){
            $page = 1;
        }
        $start_time     = $data['start_time'];
        $end_time     = $data['end_time'];
    	if(empty($cust_id)) return array('errcode' => 400,'errmsg'=>'缺少参数user_id');

        $where = " cust_id=$cust_id ";

        if(!empty($keyword)){
			$where .= " and (remark LIKE '%".$keyword."%' or admin_name LIKE '%".$keyword."%')  ";
		}

        if(!empty($start_time)){
            $start_time = date('Y-m-d', strtotime($start_time)) . '00:00:00';
            $where .= " and UNIX_TIMESTAMP(add_time) >='".strtotime($start_time)."'";
        }
    	if(!empty($end_time)){
            $end_time = date('Y-m-d', strtotime($end_time)) . '23:59:59';
            $where .= " and UNIX_TIMESTAMP(add_time) <='".strtotime($end_time)."'";
        }

        $sql       = "select log_id,admin_name,remark,add_time from ".WSY_SHOP.".admin_log where $where";
    	$sql_total  = "select count(log_id) as total from ".WSY_SHOP.".admin_log where $where";

    	$all        = $this->db->getRow($sql_total);

        $sql .= " ORDER BY add_time DESC ";
		if( $page != '' && $count != '' ){
			$sql .= " LIMIT ".($page-1)*$count.",".$count;
		}else{
            $sql .= " LIMIT 0,20";
        }
    	$result    = $this->db->getAll ($sql);
        $result['total'] = $all['total'];
    	$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取失败');
    	return $res;
    }
	/*
	* 校验自动发布时间
	* 参数：start_time-开始时间、end_time-结束时间、act_name-活动名称、status-是否发布、cust_id-商家ID、ext_info-配置json、act_type-活动类型 0购买产品送积分 1签到送积分 		  2兑换扣积分'、act_id-活动id、op-操作del-删除 release发布 end结束 conserve更新保存
	* $Author: liuzhongxuan $
	* 2017-08-24  $
	*/
	/*校验活动时间*/
	function checktime_auto($parm){

		$start_time 	= $parm['start_time'];//开始时间
		$end_time 		= $parm['end_time'];//结束时间
		$act_type	 	= $parm['act_type'];//活动类型
		$cust_id	 	= $parm['cust_id'];//活动类型

		//当前操作活动的时间与所有发布中的时间进行对比校验（数据库查询）
		$sql       = "select act_id from ".WSY_SHOP.".integral_activity where ('$start_time' BETWEEN start_time AND end_time OR '$end_time' BETWEEN start_time AND end_time OR start_time BETWEEN '$start_time' AND '$end_time' OR end_time BETWEEN '$start_time' AND '$end_time')  and act_type = '$act_type' and isvalid = 1 and cust_id = '$cust_id' and status = 1";

    	$result    = $this->db->getOne ($sql);
		if($result){
			return array('errcode'=>600,'errmsg'=>'您选择活动时间段已占用，请选择其他时间段！');
		}else{
			return array('errcode'=>0,'errmsg'=>'成功！');
		}
	}
    
    /*
	 * 获取积分设置数据
	 * $Author: djy $
	 * 2017-11-02  $
    */
    function store_setting_details($data = array())
    {
    	$cust_id        = $data['cust_id'];
    	if(empty($cust_id)) return array('errcode' => 400,'errmsg'=>'缺少参数cust_id');

    	$sql       = "select store_onoff,afstore_onoff,store_json from ".WSY_SHOP.".integral_setting where cust_id = '$cust_id'";
    	$result    = $this->db->getRow ($sql);
    	$result   != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result) : $res = array('errcode' => 400,'errmsg'=>'获取失败');
    	return $res;
    }

	/*
	* 门店积分设置保存
	* $Author: djy $
	* 2017-11-02  修改：2018-1-6 hjw$
	*/
	/*活动操作*/
	function save_store_setting($parm=array()){

        $cust_id        = $parm['cust_id'];

        $sql  = 'select id,store_onoff,afstore_onoff,store_json from '.WSY_SHOP.'.integral_setting where cust_id ="'.$cust_id.'"';
		$data = $this->db->getRow($sql);
        $id = $data['id'];
        $data_json['store_onoff'] = $data['store_onoff'];
        $data_json['afstore_onoff'] = $data['afstore_onoff'];
        $data_json['store_json'] = $data['store_json'];

         $log_data['cust_id'] = $cust_id; //商家id
         $log_data['admin_name'] = $_SESSION['curr_login'];//操作人名称
         $log_data['add_time'] = date('Y-m-d H:i:s',time());//操作时间
         $diyname = _get_diyname($cust_id);

		if($id)
		{
			$store_arr=json_decode($parm['store_json'],true);
			$cleartime=strtotime($store_arr['clear_integral_time']);
			$day=$store_arr['clear_integral_notice']['time1']['ahead_days'];
			$notice_time=$cleartime-$day*24*60*60;
			$notice_time=date("Y-m-d",$notice_time);
			$notice_time=$notice_time.substr($store_arr['clear_integral_notice']['time1']['notice_time'],10,18);
			$store_arr['clear_integral_notice']['time1']['notice_time']=$notice_time;
			$parm['store_json']=json_encode($store_arr, JSON_UNESCAPED_UNICODE);
			$result = $this->db->autoExecute(WSY_SHOP.'.integral_setting', $parm, 'update',"id = '$id'") ;
            $remark = "";
            $parm_arr = json_decode($parm['store_json'],TRUE);
            $data_arr = json_decode($data_json['store_json'],TRUE);
            if($parm['store_onoff']!=$data_json['store_onoff']){
                //$remark .= "修改门店积分开关；";
                $remark .= "修改".$diyname['store_integral_name']."开关；";
            }
            if($parm['afstore_onoff']!=$data_json['afstore_onoff']){
                //$remark .= "修改门店积分售后开关；";
                $remark .= "修改".$diyname['store_integral_name']."售后开关；";
            }            
            if($parm_arr['integral_name']!=$data_arr['integral_name']){
                //$remark .= "修改门店积分名称；";
                $remark .= "修改".$diyname['store_integral_name']."名称；";
            }
            if($parm_arr['gift_set_type']!=$data_arr['gift_set_type']){
                //$remark .= "修改赠送门店积分类型；";
                $remark .= "修改赠送".$diyname['store_integral_name']."类型；";
            }
            if($parm_arr['gift_set_value']!=$data_arr['gift_set_value']){
                //$remark .= "修改赠送门店积分的值；";
                $remark .= "修改赠送".$diyname['store_integral_name']."的值；";
            }
            if($parm_arr['join_product']!=$data_arr['join_product']){
                $remark .= "修改参与产品的类型；";
            }
            if($parm_arr['clear_integral_time']!=$data_arr['clear_integral_time']){
                $remark .= "修改清除积分时间；";
            }
            if($parm_arr['clear_integral_notice']!=$data_arr['clear_integral_notice']){
                $remark .= "修改清除积分通知时间；";
            }
            
            $log_data['remark'] = $remark;//描述
            $log_data['data_json'] = json_encode($data_json, JSON_UNESCAPED_UNICODE);//操作前的数据
            $this->insert_admin_log($log_data);
		}
		else
		{
			$result = $this->db->autoExecute(WSY_SHOP.'.integral_setting',$parm, 'insert') ;
            //$log_data['remark'] = '门店积分配置';//描述
            $log_data['remark'] = $diyname['store_integral_name'].'配置';//描述
            $log_data['data_json'] = '';//操作前的数据
            $this->insert_admin_log($log_data);
		}

		return $result;
	}  

     /*
	 * 获取用户积分明细列表
	 * $Author: djy $
	 * 2017-11-03  $
     */
    function get_user_store_integral_detail($data = array()){

    	$cust_id =  $data['cust_id'];
    	$user_id =  $data['user_id'];
    	$type    =  $data['type'];
    	$month    =  $data['month'];
    	$page    =  $data['page'];
    	$count    =  $data['count'];

        $sql = "select log_id,type,number,add_time,order_id,remark from ".WSY_SHOP.".integral_log where type in (9,10,12) and user_id = '$user_id'and cust_id='$cust_id' ";

        if(!empty($type)){
            if($type == 1){
                $sql .= " AND number>0";
            }elseif($type == 2){
                $sql .= " AND number<0";
            }

		}

        if(!empty($month)){
            $sql .= " AND month(add_time) =$month";
		}

        $sql .= " ORDER BY add_time desc";
		if( $page != '' && $count != '' ){
			$sql .= " LIMIT ".($page-1)*$count.",".$count;
		}else{
            $sql .= " LIMIT 0,20";
        }
    	$result  = $this->db->getALL($sql);
        foreach($result as $key=>$val){
            $result[$key]['add_time'] = date('Y/m/d',strtotime($val['add_time']));
        }

        $ji_sql = "select store_integral from moneybag_t where customer_id = '$cust_id' and user_id = '$user_id' and isvalid = 1 limit 1";

        $ji_res  = $this->db->getOne($ji_sql);
        $res1['user_integral'] = $ji_res;
        $res1['integral_log'] = $result;

        //$result != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$res1) : $res = array('errcode' => 600,'errmsg'=>'获取失败');
        $res = array('errcode' => 0,'errmsg'=>'获取成功','data'=>$res1);

    	return $res;
    } 
    /**
     * 获取积分转换记录
     * $Author:  hjw$
     * $date:2018-1-3$ 
     */
    function integral_transformation_log($data = array()){
        $customer_id          = $data['customer_id'];
        $search_user_id       = $data['search_user_id'];
        $search_user_name     = $data['search_user_name'];
        $count                = $data['count'];
        $page                 = $data['page'];
        $search_start_time    = $data['search_start_time'];
        $search_end_time      = $data['search_end_time'];
        $type                 = $data['type']; //转换记录类型 -1默认为全部 1商城积分 2门店积分
        if(empty($customer_id) || -1 == $customer_id){
            return array('errcode' => 400,'errmsg'=>'缺少参数customer_id');	
        }
        $where = " itl.customer_id = '$customer_id' ";
        if(!empty($search_user_id)){
            $where              .= " AND itl.user_id = '$search_user_id' ";
            $search_user_name    = '';
        }       
        if(!empty($search_user_name))      $where    .= " AND itl.user_name LIKE '%".$search_user_name."%' ";
        if(empty($count) || $count < 0)    $count     = 20;
        if($page <= 1 || empty($page)){ 
            $current = 0;//从第几条数据开始查询
            $page = 1; 
        }else{
            $current = ($page-1)*$count; 
        }
        if(!empty($search_start_time))     $where    .= " AND itl.createtime >= '{$search_start_time}'";
        if(!empty($search_end_time))       $where    .= " AND itl.createtime <= '{$search_end_time}'";
        if($type != -1 && is_numeric($type)){
		    $where .= " AND itl.type = '$type'"; 
        }
        $sql = "SELECT itl.id,itl.user_id,itl.user_name,itl.before_num,itl.change_num,itl.after_num,itl.batchcode,itl.type,itl.change_object,itl.object_num,itl.createtime,itl.phone FROM ".WSY_SHOP.".integral_transformation_log itl  WHERE $where ORDER BY itl.createtime desc LIMIT $current,$count";

        $sql_total = "SELECT COUNT(itl.customer_id) AS total FROM ".WSY_SHOP.".integral_transformation_log itl WHERE $where";
        $all                    = $this->db->getRow($sql_total);
        $result                 = $this->db->getAll($sql);
        $list_num               = count($result);
        $result['total']        = $all['total'];
        $result['page']         = $page;
        $result['page_count']   = ceil($all['total']/$count);
        $result['list_num']   	= $list_num;
        $result != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','datas'=>$result) : $res = array('errcode' => 600,'errmsg'=>'获取失败');
        return $res;
	}

	/*
    * 获取商城和门店积分开关设置
    * $Author: liusongheng $
    * 2018-1-3  $
    */
    function get_shop_stroe_integral_onoff_setting($data = array()){
    	if (empty($data['customer_id'])) {
    		return array('errcode' => 100,'errmsg'=>'缺少参数customer_id');	
    	}
    	$cust_id=$data['customer_id'];
    	$sql = "select shop_onoff,store_onoff from ".WSY_SHOP.".integral_setting where cust_id='{$cust_id}' ";
    	$result = $this->db->getRow($sql);
    	if (!empty($result)) {
    		return array('errcode' => 0,'errmsg'=>'获取成功','data'=>$result);	
    	}else{
			return array('errcode' => 100,'errmsg'=>'获取失败');	
    	}
    }

    /*
    * 获取积分转换设置
    * $Author: liusongheng $
    * 2018-1-3  $
    */
   function get_shopmall_integral_transform_setting($data = array()){
   		$customer_id=$data['customer_id'];
   		if (empty($customer_id)) {
    		return array('errcode' => 100,'errmsg'=>'缺少参数customer_id');	
    	}
    	$sql = "select on_off as turn_on_off,trans_min,trans_cient,trans_rule,remark from ".WSY_SHOP.".integral_transformation_setting where customer_id='{$customer_id}' and type='{$data["type"]}' ";
    	$result = $this->db->getRow($sql);
    	if (empty($result)) {
    		$createtime=date('Y-m-d h:i:s',time());
    		$sql="insert into ".WSY_SHOP.".integral_transformation_setting(customer_id,type,on_off,trans_min,trans_cient,trans_rule,remark,createtime) value('{$customer_id}','{$data["type"]}','0','-1','-1','1',' ','{$createtime}')";
    		$res = $this->db->query($sql);
    		if ($res == true) {
    			 $sql = "select on_off as turn_on_off,trans_min,trans_cient,trans_rule,remark from ".WSY_SHOP.".integral_transformation_setting where customer_id='{$customer_id}' and type='{$data["type"]}' ";
    			 $result = $this->db->getRow($sql);
    			 return $result;
    		}
    	}
    	return $result;
   }


   /*
    * 检查积分转换设置数据
    * $Author: liusongheng $
    * 2018-1-3  $
    */
   function check_shopmall_integral_transform_setting($data = array()){
   		if (empty($data['customer_id'])){
   			return array('errcode' => 100,'errmsg'=>'缺少参数customer_id');	
   		}
		if (empty($data['trans_min'])) {$data['trans_min']=-1;}
		if (empty($data['trans_cient'])) {$data['trans_cient']=-1;}
		if (empty($data['trans_rule'])) {$data['trans_rule']=-1;}
   		$arr=array(1,2);
   		if (!in_array($data['type'], $arr)){
   			return array('errcode' => 100,'errmsg'=>'参数type错误');	
   		}
   		$arr=array(0,1);
   		if(!in_array($data['on_off'], $arr)){
   			return array('errcode' => 100,'errmsg'=>'参数on_off错误');
   		}
   		if ($data['trans_min'] != -1) {
   			 if (floor($data['trans_min']) != $data['trans_min'] || $data['trans_min'] <= 0) {
   			 	return array('errcode' => 100,'errmsg'=>'参数trans_min错误');
   			 }
   		}
   		if ($data['trans_cient'] != -1) {
   			 if ($data['trans_cient']%10 != 0) {
   			 	 return array('errcode' => 100,'errmsg'=>'参数trans_cient错误');
   			 }
   		}
   		if ($data['trans_rule'] != 1) {
   			 if (floor($data['trans_rule']) != $data['trans_rule'] || $data['trans_rule'] <= 0) {
   			 	return array('errcode' => 100,'errmsg'=>'参数trans_rule错误');
   			 }
   		}
   		$data['createtime']=date('Y-m-d h:i:s',time());
   		return array('errcode' => 0,'errmsg'=>'参数无误','data'=>$data);
   }

   /*
    * 保存积分转换设置
    * $Author: liusongheng $
    * $ret //转换前的设置
    * 2018-1-3  $
    */
   function save_shopmall_integral_transform_setting($data = array()){
   		$customer_id=$data['customer_id'];
   		$type=$data['type'];
   		$sql_pre="select on_off,trans_min,trans_cient,trans_rule,remark from ".WSY_SHOP.".integral_transformation_setting where customer_id = '{$customer_id}' and type='{$type}'";
    	$ret = $this->db->getRow($sql_pre);
   		// $sql = "update ".WSY_SHOP.".integral_transformation_setting set on_off = '{$on_off}',trans_min='{$trans_min}',trans_cient='{$trans_cient}',trans_rule='{$trans_rule}',remark='{$remark}' where customer_id = '{$customer_id}' and type='{$type}'";
    	// $res = $this->db->query($sql);
    	// 保存设置到数据库
    	 $result = $this->db->autoExecute(WSY_SHOP.'.integral_transformation_setting', $data, 'update',"customer_id = '{$customer_id}' and type='{$type}'");
    	 if (!$result) {
    	 	 return false;
    	 }
    	//保存操作日志
    	$log_data['cust_id']=$customer_id;
    	$log_data['admin_name']=$_SESSION['curr_login'];
    	$log_data['add_time']=date("Y-m-d H:i:s",time());
    	$ret['remark'] = mysql_real_escape_string($ret['remark']);
    	$log_data['data_json']=json_encode($ret);
    	$diyname = _get_diyname($customer_id);
    	if ($type==1) {
    		$log_data['remark']='修改'.$diyname['shop_integral_name'].'转换购物币设置';
    	}elseif ($type==2) {
    		$log_data['remark']='修改'.$diyname['store_integral_name'].'转换购物币设置';
    	}
    	$this->insert_admin_log($log_data);
    	return true;
   }
   
}//类结束
