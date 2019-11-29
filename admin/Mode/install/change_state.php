<?php

header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
require('../../../../weixinpl/common/utility_shop.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
$type = $configutil->splash_new($_POST["type"]);
$resultArr = array();
if($type == "engineer"){
	$op = $configutil->splash_new($_POST["op"]);
	$dataId = $configutil->splash_new($_POST["dataId"]);
	$reason = $configutil->splash_new($_POST["reason"]);
	if($op == 0 || $op == 1){ //0拒绝 ,1暂停
		$query = "select user_id from weixin_install_engineer where isvalid = true and id = ".$dataId;
		$result = _mysql_query($query) or die("L19 query error : ".mysql_error());
		$user_id = mysql_result($result,0,0);
		
		$query = "update weixin_install_engineer set status = -1,status_remark = '".$reason."' where isvalid = true and customer_id = ".$customer_id." and id = ".$dataId;
		_mysql_query($query) or die("L26 query error : ".mysql_error());
		
		
		//查找推广员编号
		$query = "select id,parent_id from promoters where isvalid = true and customer_id = ".$customer_id." and user_id = ".$user_id;
		$result = _mysql_query($query) or die("L28 query error : ".mysql_error());
		$promoter_id = mysql_result($result,0,0);
		$parent_id = mysql_result($result,0,1);
		
		//查找weixin_qrs 编号
		$query = "select id from weixin_qrs where isvalid = true and customer_id = ".$customer_id." and qr_info_id = 
		(select id from weixin_qr_infos where isvalid = true and customer_id = ".$customer_id." and foreign_id = ".$user_id." limit 0,1)";
		$result = _mysql_query($query) or die("L35 query error : ".mysql_error());
		$qr_id = mysql_result($result,0,0);
		
		  $sql="update weixin_qrs set status=-1,reason='".$reason."' where id=".$qr_id;
		  _mysql_query($sql)or die("L39 query error : ".mysql_error());
		  
		  $sql="update promoters set status=-1,isAgent=0 where user_id=".$user_id." and isvalid=true and customer_id=".$customer_id;
		  _mysql_query($sql)or die("L42 query error : ".mysql_error());
		  
		  //减少上级的推广员数
		  $sql="update promoters set promoter_count=promoter_count-1 where isvalid=true and user_id=".$parent_id;
		  _mysql_query($sql)or die("L46 query error : ".mysql_error());
		
	}else if($op == 2){  //审核通过后如不是推广员则自动成为推广员
		$query = "update weixin_install_engineer set status = 1 where isvalid = true and customer_id = ".$customer_id." and id = ".$dataId;
		_mysql_query($query) or die("L20 query error : ".mysql_error());
		
		$query = "select user_id from weixin_install_engineer  where isvalid = true and  id = ".$dataId;
		$result = _mysql_query($query) or die("L24 query error : ".mysql_error());
		$user_id = mysql_result($result,0,0);
		
		
		//自动添加推广员
	 $curr_user_id = $user_id;
	 $qr_info_id =-1;
	 $query="select id,scene_id from weixin_qr_infos where type=1 and isvalid=true and customer_id=".$customer_id." and foreign_id=".$curr_user_id;
	 $result = _mysql_query($query) or die('Query failed6: ' . mysql_error());  
	 
	 while ($row = mysql_fetch_object($result)) {
		$scene_id = $row->scene_id;
		$qr_info_id=$row->id;
	 }
	 fwrite($f, "===query====".$query."\r\n"); 
	 if($qr_info_id<0){
		$query="select max(scene_id) as scene_id from weixin_qr_infos where isvalid=true and customer_id=".$customer_id;
		$result = _mysql_query($query) or die('Query failed7: ' . mysql_error());  
		$scene_id=1;
		while ($row = mysql_fetch_object($result)) {
			$scene_id = $row->scene_id;
			break;
		}
		$scene_id++;
		$sql="insert into weixin_qr_infos(foreign_id,type,scene_id,isvalid,customer_id) values(".$curr_user_id.",1,".$scene_id.",true,".$customer_id.")";
		_mysql_query($sql);
		$qr_info_id = mysql_insert_id();
	 }
	 fwrite($f, "===qr_info_id====".$qr_info_id."\r\n"); 
	 $query="select id,ticket,status from weixin_qrs where customer_id=".$customer_id." and isvalid=true and type=1 and qr_info_id=".$qr_info_id;
	 $qr_id=-1;
	 $status = 0;
	 $result = _mysql_query($query) or die('Query failed8: ' . mysql_error());  
	 while ($row = mysql_fetch_object($result)) {
		 $qr_id = $row->id;
		 $status= $row->status;
		 break;
	 }
	 
	 $parent_id=-1;
	  $query="select parent_id from weixin_users where isvalid=true and id=".$curr_user_id." limit 0,1";
	  $result = _mysql_query($query) or die('Query failed9: ' . mysql_error());
	   while ($row = mysql_fetch_object($result)) {
		   $parent_id = $row->parent_id;
	   }
	 
	 if($qr_id<0){
		 $action_name ="QR_LIMIT_SCENE";
		 $query="insert into weixin_qrs(action_name,expire_seconds,qr_info_id,customer_id,isvalid,createtime,type,status) values('".$action_name."',-1,".$qr_info_id.",".$customer_id.",true,now(),1,1)";
		 _mysql_query($query);
		 $qr_id = mysql_insert_id();
	 }else{
		 $query="update weixin_qrs set status=1 where id=".$qr_id;
		 _mysql_query($query);
		 
		 $query="update promoters set status=1,parent_id=".$parent_id." where user_id=".$curr_user_id;
		 _mysql_query($query);
		  //增加上级的推广员数
		$sql="update promoters set promoter_count=promoter_count+1 where isvalid=true and user_id=".$parent_id;
		_mysql_query($query);
	 }
	 
	   $query="select id,pwd,customer_id from promoters where  isvalid=true  and user_id=".$curr_user_id;
	   $result = _mysql_query($query) or die('Query failed10: ' . mysql_error());
	   $pwd = "";
	   $before_customer_id=-1;
	   $promoter_id = -1;
	   while ($row = mysql_fetch_object($result)) {
		   $promoter_id = $row->id;
		   $pwd=$row->pwd;
		   $before_customer_id = $row->customer_id;
	   }
		$generation=1;
		   if($parent_id>0){
			   $query="select generation from promoters where isvalid=true  and user_id=".$parent_id;
			   $result = _mysql_query($query) or die('Query failed10: ' . mysql_error());
			   while ($row = mysql_fetch_object($result)) {
					$generation = $row->generation;
			   }
			   $generation=$generation+1;
		   }
	   if($promoter_id<0){
		   $pwd="888888"; 
		   $sql ="insert into promoters(user_id,pwd,isvalid,customer_id,parent_id,createtime,status,generation) values(".$curr_user_id.",'888888',true,".$customer_id.",".$parent_id.",now(),1,".$generation.")";
		   _mysql_query($sql);
		   $error=mysql_error();
		   //echo $error;
		   //增加推广员数量
		   $query="update promoters set promoter_count= promoter_count+1 where isvalid=true and status=1 and user_id=".$parent_id;
		   _mysql_query($query);
	   }else{
		   $sql="update promoters set parent_id=".$parent_id.",status=1 where id=".$promoter_id;
		   _mysql_query($sql);
	   }
	   //自动添加推广员 end
		
		
		
		
	}else if($op == 3){ //删除技师
		$query = "update weixin_install_engineer set isvalid = false where isvalid = true and customer_id = ".$customer_id." and id = ".$dataId;
		_mysql_query($query) or die("L24 query error : ".mysql_error());
		
		$query = "select user_id from weixin_install_engineer where isvalid = true and id = ".$dataId;
		$result = _mysql_query($query) or die("L19 query error : ".mysql_error());
		$user_id = mysql_result($result,0,0);
		
		
		//查找推广员编号
		$query = "select id,parent_id from promoters where isvalid = true and customer_id = ".$customer_id." and user_id = ".$user_id;
		$result = _mysql_query($query) or die("L154 query error : ".mysql_error());
		$promoter_id = mysql_result($result,0,0);
		$parent_id = mysql_result($result,0,1);
		
		//查找weixin_qrs 编号
		$query = "select id from weixin_qrs where isvalid = true and customer_id = ".$customer_id." and qr_info_id = 
		(select id from weixin_qr_infos where isvalid = true and customer_id = ".$customer_id." and foreign_id = ".$user_id." limit 0,1)";
		$result = _mysql_query($query) or die("L161 query error : ".mysql_error());
		$qr_id = mysql_result($result,0,0);
		
		  $sql="update weixin_qrs set isvalid=false where id=".$qr_id;
		  _mysql_query($sql)or die("L165 query error : ".mysql_error());
		  
		  $sql="update promoters set isvalid = false where user_id=".$user_id." and customer_id=".$customer_id;
		  _mysql_query($sql)or die("L168 query error : ".mysql_error());
		  
	}else if($op == 4){ //扣除技师积分
		$score = $configutil->splash_new($_POST["score"]);
		$comment = $configutil->splash_new($_POST["comment"]);
		
		$query = "insert into weixin_install_score (totalscore,createtime,isvalid,scoretype,comment,engineer_id) values('-".$score."',now(),1,2,'".$comment."','".$dataId."')";
		_mysql_query($query) or die("L118 query error  : ".mysql_error());
		
		$query = "update weixin_install_engineer set totalscore = totalscore - ".$score." where isvalid = true and id = ".$dataId;
		_mysql_query($query) or die("L121 query error  : ".mysql_error());
		
	}
	$resultArr["result"] = 1;
	$resultArr["msg"] = "操作成功！";
}else if($type == "order"){
	$op = $configutil->splash_new($_POST["op"]);
	if($op == 1){ //指派订单
		$dataId = $configutil->splash_new($_POST["dataId"]);
		$eng_id = $configutil->splash_new($_POST["eng_id"]);
		$res_num = $configutil->splash_new($_POST["res_num"]);
		// res_num ,eng_id 查找正在进行中的记录，有的话不能再派单
		$query = "select id,status from weixin_install_reservation_engineer where isvalid = true and reservation_num = '".$res_num."' and engineer_id = ".$eng_id." and status !=2 ";
		$result = _mysql_query($query) or die("L36 query error : ".mysql_error());
		if($row = mysql_fetch_object($result)){
			$status = $row->status;
			$statusStr = "进行中";
			if($status == 0){
				$statusStr = "等待技师接单...";
			}else if ($status == 3){
				$statusStr = "已安装完成";
			}
			$resultArr["result"] = -1;
			$resultArr["msg"] = "该订单已有指派记录，并且状态为：".$statusStr;
		}else{
			$query = "insert into weixin_install_reservation_engineer(reservation_num,engineer_id,status,createtime,isvalid) 
			values('".$res_num."','".$eng_id."',0,now(),1)";
			_mysql_query($query) or die("L149 query error : ".mysql_error());
			
			$query = "update weixin_install_reservation  set status = 1 where isvalid = true and status = 0 and id = ".$dataId." and customer_id = ".$customer_id;
			_mysql_query($query) or die("L152 query error : ".mysql_error());
			
			$resultArr["result"] = 1;
			$resultArr["msg"] = "指派成功，等待技师接单中...";
			
			//指派安装技师后推送消息给技师
			$query="select weixin_fromuser from weixin_users where isvalid=true and id=(select user_id from weixin_install_engineer where isvalid = true and  id = '".$eng_id."' limit 0,1)";
			$result = _mysql_query($query) or die('L159 Query failed: ' . mysql_error());
			$weixin_fromuser="";
			while ($row = mysql_fetch_object($result)) {
				$weixin_fromuser = $row->weixin_fromuser;
				break;
			}
			
			$content = "已有订单指派您进行安装[编号:".$res_num."],请进入个人中心接收订单！";
			$shopmessage= new shopMessage_Utlity();
			$shopmessage->SendMessage($content,$weixin_fromuser,$customer_id);
			
		}
	}else if($op == 2){ //回访评分
		$dataId = $configutil->splash_new($_POST["dataId"]);
		$score = $configutil->splash_new($_POST["score"]);
		$reservation_num = $configutil->splash_new($_POST["reservation_num"]);
		$remark = $configutil->splash_new($_POST["remark"]);
		
		if(empty($dataId) || empty($score) || empty($reservation_num) ){
			$resultArr["result"] = -1;
			$resultArr["msg"] = "缺少参数.";
		}else{
			
			$query = "select weight from weixin_install_settings where isvalid = true and customer_id = ".$customer_id;
			$result = _mysql_query($query) or die("L70 query error : ".mysql_error());
			$weight = mysql_result($result,0,0);
			
			$query = "select score1 from weixin_install_score where isvalid = true and reservation_num = '".$reservation_num."' and scoretype = 0";
			$result = _mysql_query($query) or die("L74 query error : ".mysql_error());
			$score1 = mysql_result($result,0,0);
			
			// 总分 [百分制]；  权重 score1    1 - 权重 score2 
			$totalscore = (($score1 * $weight) +  ( (1 - $weight) *$score)) * 100 / 5;
			//echo "totalscore : ".$totalscore;
			
			$query="update weixin_install_score set score2 = ".$score." ,score2remark = '".$remark."' ,score2time = now(),totalscore = ".$totalscore." where isvalid = true and scoretype = 0 and reservation_num = '".$reservation_num."'";
			_mysql_query($query) or die("L78 query error : ".mysql_error());
			
			
			$query = "update weixin_install_reservation  set status = 4 where isvalid = true and id = ".$dataId." and customer_id = ".$customer_id;
			_mysql_query($query) or die("L81 query error : ".mysql_error());
			
			
			$resultArr["result"] = 1;
			$resultArr["msg"] = "已回访完成！";
		}
	}else if($op == 3){ //删除订单
		$dataId = $configutil->splash_new($_POST["dataId"]);
		if(empty($dataId)){
			$resultArr["result"] = -1;
			$resultArr["msg"] = "缺少参数dataId.";
		}else{
			$query = "update weixin_install_reservation_engineer set isvalid = false where isvalid = true 
				and reservation_num = (select reservation_num from weixin_install_reservation where isvalid = true and id = '".$dataId."') ";
			_mysql_query($query) or die("L87 query error : ".mysql_error());
			
			$query = "update weixin_install_reservation set isvalid = false where isvalid = true and customer_id = ".$customer_id." and id = ".$dataId;
			_mysql_query($query) or die("L90 query error : ".mysql_error());
			
			$resultArr["result"] = 1;
			$resultArr["msg"] = "删除成功！";
		}
	}else if($op == 4){ //确认完成
		$dataId = $configutil->splash_new($_POST["dataId"]);
		$reservation_num = $configutil->splash_new($_POST["reservation_num"]);
		if(empty($dataId) || empty($reservation_num)){
			$resultArr["result"] = -1;
			$resultArr["msg"] = "缺少参数!";
		}else{
			
			
			$hasError = false;
			$msg = "";
			
			$query = "select r.engineer_id,e.user_id from weixin_install_reservation_engineer r
				inner join weixin_install_engineer e on r.engineer_id = e.id where r.isvalid = true and r.status = 3 and r.reservation_num = '".$reservation_num."'";
			$result = _mysql_query($query) or die("L114 query error : ".mysql_error());
			$engineer_id = mysql_result($result,0,0);
			$user_id = mysql_result($result,0,1);
			
			if(!empty($engineer_id) && !empty($user_id)){
				_mysql_query('START TRANSACTION');
				
				// 1 统计积分
				$query = "select totalscore from weixin_install_score where isvalid = true and reservation_num = '".$reservation_num."' and scoretype = 0";
				$result = _mysql_query($query) or die("L120 query error : ".mysql_error());
				$score = mysql_result($result,0,0);
				
				$query = "update weixin_install_engineer set totalscore = totalscore+".$score." where isvalid = true and id =".$engineer_id." and customer_id = ".$customer_id;
				_mysql_query($query); //or die("L151 query error : ".mysql_error());
				$error = mysql_error();
				if(!empty($error)){
					$msg = "L132 : ".$error;
					$hasError = true;
				}
				
				
				// 2 向会员卡添加安装费用
				//查找当前订单的安装费
				$query = "select install_cost from weixin_install_reservation where isvalid = true and customer_id = ".$customer_id." and id = ".$dataId;
				$result = _mysql_query($query) or die("L131 query error : ".mysql_error());
				$install_cost = mysql_result($result,0,0);
				
				$perscore = $score / 100.00;
				
				$install_cost = $install_cost * $perscore;
				
				//商家指定的会员卡
				$query_card = "select shop_card_id from weixin_commonshops where isvalid = true and customer_id = ".$customer_id;
				$result_card = _mysql_query($query_card) or die("L136 query error : ".mysql_error());
				$shop_card_id = mysql_result($result_card,0,0);
				if($shop_card_id > 0){
					//找到技师所对应的会员卡号和剩余积分
					$query_member = "select m.id,c.remain_consume from weixin_card_members m
						inner join weixin_card_member_consumes c on m.id = c.card_member_id where m.isvalid = true and  m.card_id = ".$shop_card_id." and m.user_id = ".$user_id." ";
					$result_member = _mysql_query($query_member) or die("L142 query error : ".mysql_error());
					$card_member_id = mysql_result($result_member,0,0);
					$remain_consume = mysql_result($result_member,0,1);
					
					if($card_member_id > 0){
						//添加会员卡明细记录
						$query_insert = "insert into weixin_card_recharge_records(card_member_id,before_cost,after_cost,cost,createtime,remark,new_record,recharge_type,isvalid)
							values('".$card_member_id."','".$remain_consume."','".($remain_consume+$install_cost)."','".$install_cost."',now(),'技师产品安装费',1,1,1);";
						_mysql_query($query_insert);// or die("L148 query error : ".mysql_error());
						$error = mysql_error();
						if(!empty($error)){
							$msg = "L160 : ".$error;
							$hasError = true;
						}
						//更新会员卡余额
						$query_update = "update weixin_card_member_consumes set remain_consume = remain_consume+".$install_cost." where isvalid = true and card_member_id=".$card_member_id;
						_mysql_query($query_update); // or die("L158 query error : ".mysql_error());
						$error = mysql_error();
						if(!empty($error)){
							$msg = "L168 : ".$error;
							$hasError = true;
						}
						
						$query = "select weixin_fromuser from weixin_users where isvalid = true and id = ".$user_id." limit 0,1";
						$result = _mysql_query($query) or die("L277 query error : ".mysql_error());
						$eng_fromuser = mysql_result($result,0,0);
						
						$content = "恭喜您，您于".date( "Y-m-d H:i:s")." 获得安装费 ￥".$install_cost."元";
						$shopmessage= new shopMessage_Utlity();
						$shopmessage->SendMessage($content,$eng_fromuser,$customer_id);
						
					}
					
				}
				
				// 3 安装费返佣 三级
				$query = "select reward1,reward2,reward3,reward_account from weixin_install_settings where isvalid = true and customer_id = ".$customer_id;
				$result = _mysql_query($query) or die("L156 query error : ".mysql_error());
				$reward1 = mysql_result($result,0,0);
				$reward2 = mysql_result($result,0,1);
				$reward3 = mysql_result($result,0,2);
				$reward_account = mysql_result($result,0,3);
				
				if($reward_account > 0){
					$query = "select parent_id from weixin_users where isvalid = true and customer_id = ".$customer_id." and id = ".$user_id." limit 0,1";
					$result = _mysql_query($query) or die("Ll62 query error : ".mysql_error());
					$parent_id = mysql_result($result,0,0);
					$level = 1;
					while($level<=3 && !empty($parent_id) && $parent_id > 0){
						
						$reward_money = 0;
						$remark = "";
						if($level == 1){
							$reward_money = $reward_account * $reward1;
							$remark = "技师产品安装费,普通返佣";
						}else if($level == 2){
							$reward_money = $reward_account * $reward2;
							$remark = "技师产品安装费,黑铁返佣";
						}else{
							$reward_money = $reward_account * $reward3;
							$remark = "技师产品安装费,青铜返佣";
						}
						
						if($reward_money > 0){
							//找到技师所对应的会员卡号和剩余积分
							$query_member = "select m.id,c.remain_consume from weixin_card_members m
								inner join weixin_card_member_consumes c on m.id = c.card_member_id where m.isvalid = true and  m.card_id = ".$shop_card_id." and m.user_id = ".$parent_id." ";
							$result_member = _mysql_query($query_member) or die("L183 query error : ".mysql_error());
							$card_member_id = mysql_result($result_member,0,0);
							$remain_consume = mysql_result($result_member,0,1);
							
							//添加会员卡明细记录
							$query_insert = "insert into weixin_card_recharge_records(card_member_id,before_cost,after_cost,cost,createtime,remark,new_record,recharge_type,isvalid)
								values('".$card_member_id."','".$remain_consume."','".($remain_consume+$reward_money)."','".$reward_money."',now(),'".$remark."',1,2,1);";
							_mysql_query($query_insert); //or die("L201 query error : ".mysql_error())
							$error = mysql_error();
							if(!empty($error)){
								$msg = "添加会员卡明细记录异常：".$remark.",会员卡：".$card_member_id.",佣金:".$reward_money."L212 : ".$error;
								$hasError = true;
							}
							
							//更新会员卡余额
							$query_update = "update weixin_card_member_consumes set remain_consume = remain_consume+".$reward_money." where isvalid = true and card_member_id=".$card_member_id;
							_mysql_query($query_update); // or die("L209 query error : ".mysql_error())
							$error = mysql_error();
							if(!empty($error)){
								$msg = "更新会员卡余额异常：".$remark.",会员卡：".$card_member_id.",佣金:".$reward_money."  L221 : ".$error;
								$hasError = true;
							}
							
							$query = "select weixin_fromuser from weixin_users where isvalid = true and id = ".$parent_id." limit 0,1";
							$result = _mysql_query($query) or die("L344 query error : ".mysql_error());
							$eng_fromuser = mysql_result($result,0,0);
							
							$content = "恭喜您，您于".date( "Y-m-d H:i:s")." 获得".$remark." ￥".$reward_money."元";
							$shopmessage= new shopMessage_Utlity();
							$shopmessage->SendMessage($content,$eng_fromuser,$customer_id);
						}
						
						//重新查找 parent_id
						$query = "select parent_id from weixin_users where isvalid = true and customer_id = ".$customer_id." and id = ".$parent_id." limit 0,1";
						$result = _mysql_query($query) or die("L217 query error : ".mysql_error());
						$parent_id = mysql_result($result,0,0);
						
						$level++;
					}
				
				}
				
				// 4 修改订单状态
				$query = "update weixin_install_reservation set status = 5 where isvalid = true and id = ".$dataId;
				_mysql_query($query);// or die("L225 query error : ".mysql_error())
				$error = mysql_error();
				if(!empty($error)){
					$msg = "修改订单状态异常  L238 : ".$error;
					$hasError = true;
				}
				
				
				if($hasError == true){
					$resultArr["result"] = -1;
					$resultArr["msg"] = $msg;
					_mysql_query('ROLLBACK ');
				}else{
					$resultArr["result"] = 1;
					$resultArr["msg"] = "订单确认成功！";
					_mysql_query('COMMIT ');
				}
			
			}else{
				$resultArr["result"] = 1;
				$resultArr["msg"] = "订单已确认！";
			}
		}
	}else if($op == 5){ //回访后订单扣分
		$score = $configutil->splash_new($_POST["score"]);
		$comment = $configutil->splash_new($_POST["comment"]);
		$engineer_id = $configutil->splash_new($_POST["engineer_id"]);
		$reservation_num = $configutil->splash_new($_POST["reservation_num"]);
		
		$query = "insert into weixin_install_score (reservation_num,totalscore,createtime,isvalid,scoretype,comment,engineer_id) values('".$reservation_num."','-".$score."',now(),1,2,'".$comment."','".$engineer_id."')";
		_mysql_query($query) or die("L367 query error  : ".mysql_error());
		
		$query = "update weixin_install_engineer set totalscore = totalscore - ".$score." where isvalid = true and id = ".$engineer_id;
		_mysql_query($query) or die("L370 query error  : ".mysql_error());
		
		$resultArr["result"] = 1;
		$resultArr["msg"] = "扣分成功！";
	}
}else if($type == "article"){
	$op = $configutil->splash_new($_POST["del"]);
	$article_id = $configutil->splash_new($_POST["article_id"]);
	if(!empty($article_id) && $article_id >0){
		$query = "update weixin_install_article set isvalid = false where id = ".$article_id." and customer_id = ".$customer_id;
		_mysql_query($query) or die("L428 query error  : ".mysql_error());
	}
	$resultArr["result"] = 1;
	$resultArr["msg"] = "删除成功！";
}
echo json_encode($resultArr);

mysql_close($link);
?>