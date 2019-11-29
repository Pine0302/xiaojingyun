<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
date_default_timezone_set('PRC');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../weixinpl/common/utility_shop.php');

$resultArr = array(); //用于JSON返回的结果
$success="操作成功";
$terror="操作失败";
$op = $configutil->splash_new($_POST["op"]);
//$op = $configutil->splash_new($_POST["op"]);
//$op = $configutil->splash_new($_GET["op"]);
$E_SQL="";
$shopmessage= new shopMessage_Utlity();
if($op == 1000){ //更新自动收货时间(实例不做访问)
	$days = $configutil->splash_new($_GET["days"]);
	$is_delay = $configutil->splash_new($_GET["is_delay"]);
	if(empty($days) || $days <= 0){
		$days = 3; //默认延后3天
	}
	if(!empty($batchcode) && !empty($days)){
		if($is_delay == 1){
			$query = "update weixin_commonshop_orders set auto_receivetime = DATE_ADD(auto_receivetime, INTERVAL ".$days." DAY ),is_delay = 2 where isvalid = true and sendstatus = 1 and batchcode = '".$batchcode."'";
		}else{
			$query = "update weixin_commonshop_orders set auto_receivetime = DATE_ADD(auto_receivetime, INTERVAL ".$days." DAY ) where isvalid = true and sendstatus = 1 and batchcode = '".$batchcode."'";
		}
		_mysql_query($query);
		
		$query = "select auto_receivetime from weixin_commonshop_orders where isvalid = true and sendstatus = 1 and batchcode = '".$batchcode."'";
		$result = _mysql_query($query);
		$receivetime = mysql_result($result,0,0);
		$resultArr["receivetime"] = $receivetime;
		
		$query = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) 
			values('".$batchcode."',6,'".$roletypeStr."更新的订单的自动收货日期为".$receivetime."','".$username."',now(),1)";
		_mysql_query($query);
		
		if($is_delay == 1){
			$query = "select weixin_fromuser from weixin_users where id  = (select user_id from weixin_commonshop_orders where isvalid = true and batchcode = '".$batchcode."' limit 0,1)";
			$result = _mysql_query($query);
			$fromuser = mysql_result($result,0,0);
			$content = "编号：".$batchcode.",商家已处理了您的延迟收货申请，当前自动收货时间为".$receivetime;
			$shopmessage->SendMessage($content,$fromuser,$customer_id);
		}
	}
}else if($op==1){//添加产品属性
	$custid=$configutil->splash_new($_POST["custid"]);
	$sx_type=$configutil->splash_new($_POST["sx_type"]);
	$sx_name=$configutil->splash_new($_POST["sx_name"]);
	$sx_introduce=$configutil->splash_new($_POST["sx_introduce"]);
	$add_sx_SQL="insert into slb_sx(sx_type,sx_name,sx_introduce,c_createtime,c_isvalid,custid) 
			values('".$sx_type."','".$sx_name."','".$sx_introduce."',now(),1,'".$custid."')";
	_mysql_query($add_sx_SQL);	
	if($sx_type==-1){
		$se_sx_SQL="select id from slb_sx where sx_type='".$sx_type."' and sx_name='".$sx_name."'  and sx_introduce='".$sx_introduce."'";
		$sx_id=0;
		$se_sx_R = _mysql_query($se_sx_SQL) or die('Query failed: ' . mysql_error());
			while ($se_sx_row = mysql_fetch_object($se_sx_R)) {
			$sx_id =$se_sx_row->id;
		}
		if($sx_id>0){
			$add_tyle_SQL="insert into slb_type(p_type,type_name,c_createtime,c_isvalid,custid) 
			values('".$sx_id."','".$sx_name."',now(),1,'".$custid."')";
			_mysql_query($add_tyle_SQL);	
		}
	}
}else if($op==2){//获取属性类型
	$success= array();
	$custid=$configutil->splash_new($_POST["custid"]);
	$get_sx_SQL="select id,sx_name,sx_introduce from slb_sx where sx_type=-1 and c_isvalid=1 and custid='".$custid."'";
		$result = _mysql_query($get_sx_SQL) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$map= array();	
			$map["id"]=$row->id;
			$map["sx_name"]=$row->sx_name;	
			$map["sx_introduce"]=$row->sx_introduce;
			array_push($success,$map);			
		}	
}else if($op==3){//获取单位属性
	$success= array();
	$custid=$configutil->splash_new($_POST["custid"]);
	$get_sx_SQL="select id,sx_name,sx_introduce from slb_sx where sx_type=0 and c_isvalid=1 and custid='".$custid."'";
		$result = _mysql_query($get_sx_SQL) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$map= array();	
			$map["id"]=$row->id;
			$map["sx_name"]=$row->sx_name;	
			$map["sx_introduce"]=$row->sx_introduce;
			array_push($success,$map);			
		}	
}else if($op==4){//属性修改编辑
	$ID=$configutil->splash_new($_POST["ID"]);
	$sx_type=$configutil->splash_new($_POST["sx_type"]);
	$sx_name=$configutil->splash_new($_POST["sx_name"]);
	$sx_introduce=$configutil->splash_new($_POST["sx_introduce"]);
	$up_sx_SQL=" update slb_sx set sx_name='".$sx_name."',sx_introduce='".$sx_introduce."' where id='".$ID."'";
	_mysql_query($up_sx_SQL);	
	if($sx_type==-1){
		$up_tyle_SQL=" update slb_type set type_name='".$sx_name."' where p_type='".$ID."'";
		_mysql_query($up_tyle_SQL);
	}
}else if($op==5){//删除属性	
	
	$map= array();
	$ID=$configutil->splash_new($_POST["ID"]);
	$sx_type=$configutil->splash_new($_POST["sx_type"]);
	$sx_id=0;
	if($sx_type==-1){
		$se_type_SQL="select id from slb_type where p_type='".$ID."'  and c_isvalid = 1";
		$result = _mysql_query($se_type_SQL) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$sx_id=$row->id;
		}
		if($sx_id>0){
			$map["id"]=0;
			$map["msg"]="请先删除类型配置中对应的类型";
			
		}else{
			$map["id"]=1;
			$map["msg"]="删除成功";
			$up_sx_SQL=" update slb_sx set c_isvalid=0 where id='".$ID."'";
			_mysql_query($up_sx_SQL);	
		}
	}else{
		$map["id"]=1;
		$map["msg"]="删除成功";
		$up_sx_SQL=" update slb_sx set c_isvalid=0 where id='".$ID."'";
		_mysql_query($up_sx_SQL);	
	}
	$success= array();
	array_push($success,$map);	

}else if($op==6){//获取商品属性
	$success= array();
	$sx_type=$configutil->splash_new($_POST["sx_type"]);
	$get_sx_SQL="select id,sx_name,sx_introduce from slb_sx where sx_type='".$sx_type."' and c_isvalid=1 ";
		$result = _mysql_query($get_sx_SQL) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$map= array();	
			$map["id"]=$row->id;
			$map["sx_name"]=$row->sx_name;	
			$map["sx_introduce"]=$row->sx_introduce;
			array_push($success,$map);			
		}	
}else if($op==7){//检验属性名字是否被占用
	$success= array();
	$sx_type=$configutil->splash_new($_POST["sx_type"]);
	$sx_name=$configutil->splash_new($_POST["sx_name"]);
	$custid=$configutil->splash_new($_POST["custid"]);
	$get_sx_C_SQL="select id,sx_name from slb_sx where sx_type='".$sx_type."' and c_isvalid=1 and sx_name='".$sx_name."' and custid='".$custid."' limit 0,1";
		$result = _mysql_query($get_sx_C_SQL) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$map= array();	
			$sx_id=$row->id;
			$sx_name=$row->sx_name;
			$map["id"]=$row->id;
			$map["sx_name"]=$row->sx_name;	
			if($sx_id>0){
				$map["msg"]="已被使用;ID:".$sx_id.",名称:".$sx_name;
			}
			array_push($success,$map);			
		}
		
}else if($op==8){//重新生成type
	$ID=$configutil->splash_new($_POST["ID"]);
	$sx_name=$configutil->splash_new($_POST["sx_name"]);
	$custid=$configutil->splash_new($_POST["custid"]);
	$se_tyle_SQL="select id from slb_type where p_type='".$ID."' and custid='".$custid."' and c_isvalid=0 ";
	$result = _mysql_query($se_tyle_SQL);
	$id = mysql_result($result,0,0);
	if($id>0){
		$up_type_SQL=" update slb_type set c_isvalid=1 where id='".$id."'";
		_mysql_query($up_type_SQL);	
	}else{
		$add_tyle_SQL="insert into slb_type(p_type,type_name,c_createtime,c_isvalid,custid) values('".$ID."','".$sx_name."',now(),1,'".$custid."')";
		_mysql_query($add_tyle_SQL);	
	}
	
	
	$success="重新生成type;成功！";
}else if($op==10){//添加产品，编辑
	$p_id=$configutil->splash_new($_POST["p_id"]);
	$psx_id=$configutil->splash_new($_POST["psx_id"]);
	$p_name=$configutil->splash_new($_POST["p_name"]);
	$p_type=$configutil->splash_new($_POST["p_type"]);
	$p_sx_id=$configutil->splash_new($_POST["p_sx_id"]);
	$p_price=$configutil->splash_new($_POST["p_price"]);
	$p_unit_id=$configutil->splash_new($_POST["p_unit_id"]);
	$p_url=$configutil->splash_new($_POST["p_url"]);
	$custid=$configutil->splash_new($_POST["custid"]);
	$p_introduce=$configutil->splash_new($_POST["p_introduce"]);
	$p_unit="";
	$p_sx_name="";
	$p_sx_introduce="";
	$UNIT_SQL="select sx_name from slb_sx where sx_type=0 and id='".$p_unit_id."'";
	$UNIT_R = _mysql_query($UNIT_SQL);
	$p_unit = mysql_result($UNIT_R,0,0);
	if($p_id>0){
	$U_P_SQL=" update slb_product set p_name='".$p_name."',p_price='".$p_price."',p_unit='".$p_unit."',p_unit_id='".$p_unit_id."',p_url='".$p_url."',p_introduce='".$p_introduce."' where id='".$p_id."'";	
	_mysql_query($U_P_SQL);
	}else{	
	$add_P_SQL="insert into slb_product(p_name,p_type,p_price,p_unit,p_unit_id,p_url,c_createtime,c_isvalid,p_introduce,custid) 
			values('".$p_name."','".$p_type."','".$p_price."','".$p_unit."','".$p_unit_id."','".$p_url."',now(),1,'".$p_introduce."','".$custid."')";
	_mysql_query($add_P_SQL);
	
	}
	$p_sx_SQL="select id,sx_name,sx_introduce from slb_sx where sx_type>0 and id='".$p_sx_id."'";
	$p_sx_R = _mysql_query($p_sx_SQL) or die('Query failed: ' . mysql_error());
		while ($p_sx_row = mysql_fetch_object($p_sx_R)) {
			$p_sx_id =$p_sx_row->id;
			$p_sx_name =$p_sx_row->sx_name;
			$p_sx_introduce =$p_sx_row->sx_introduce;
		}
	$p_id_SQL="select id from slb_product where p_name='".$p_name."' and  p_type='".$p_type."' and  p_price='".$p_price."' and  p_unit='".$p_unit."' and  p_url='".$p_url."' and  p_introduce='".$p_introduce."' and  custid='".$custid."'";
	$p_id__R = _mysql_query($p_id_SQL) or die('Query failed: ' . mysql_error());
	while ($p_id__row = mysql_fetch_object($p_id__R)) {
		$p_id =$p_id__row->id;
	}
	if($psx_id>0){
	$U_P_SX_SQL=" update slb_p_sx set p_id='".$p_id."',sx_id='".$sx_id."',sx_name='".$sx_name."',sx_introduce='".$sx_introduce."' where id='".$psx_id."'";		
	}else{
	$add_P_SX_SQL="insert into slb_p_sx (p_id,sx_id,sx_name,sx_introduce)values('".$p_id."','".$p_sx_id."','".$p_sx_name."','".$p_sx_introduce."')";
	_mysql_query($add_P_SX_SQL);
	}
}else if($op==11){//商品上架
	$ID=$configutil->splash_new($_POST["ID"]);
	$up_sx_SQL=" update slb_product set p_status=1  where id='".$ID."'";
	_mysql_query($up_sx_SQL);	
}else if($op==12){//商品下架
	$ID=$configutil->splash_new($_POST["ID"]);
	$up_sx_SQL=" update slb_product set p_status=0  where id='".$ID."'";
	_mysql_query($up_sx_SQL);	
}else if($op==13){//商品删除
	$ID=$configutil->splash_new($_POST["ID"]);
	$up_sx_SQL=" update slb_product set c_isvalid=0  where id='".$ID."' and p_status=0 ";
	_mysql_query($up_sx_SQL);	
}else if($op==31){//订单完成
	$ID=$configutil->splash_new($_POST["ID"]);
	$login_name=$configutil->splash_new($_POST["login_name"]);
	$o_code=$configutil->splash_new($_POST["o_code"]);
	$up_o_SQL=" update slb_order set o_state=2,o_time=now(),o_login_name='".$login_name."',o_code='".$o_code."'  where id='".$ID."' and o_state=1 and c_isvalid=1 ";
	_mysql_query($up_o_SQL);	
	$query = "select weixin_fromuser from weixin_users where id  = ( select userid from slb_order where id='".$ID."' )";
	$result = _mysql_query($query);
	$fromuser = mysql_result($result,0,0);
	$query = "select custid from slb_order where id='".$ID."' ";
	$result = _mysql_query($query);
	$custid = mysql_result($result,0,0);
	$query = "select addit1,o_batchcode,o_totale_price from slb_order where id='".$ID."' ";
	$result = _mysql_query($query);
	$addit1 = mysql_result($result,0,0);
	$o_batchcode = mysql_result($result,0,1);
	$o_totale_price = mysql_result($result,0,2);
	//您的 设备号xxxx 充值,金额100元,成功
	$content = "编号：".$o_batchcode.",";
	if($addit1!="" && !empty($addit1) ){
		$content=$content." 设备号:".$addit1 ."";
	}
	$content=$content."充值金额:".$o_totale_price."元,充值成功！";
	$shopmessage->SendMessage($content,$fromuser,$custid);
}else if($op==100){//类型自定义被配置
	$ID=$configutil->splash_new($_POST["ID"]);
	//$sx_type=$configutil->splash_new($_POST["sx_type"]);
	$title=$configutil->splash_new($_POST["title"]);	
	$code=$configutil->splash_new($_POST["code"]);	
	$addit1=$configutil->splash_new($_POST["addit1"]);
	$addit1_name=$configutil->splash_new($_POST["addit1_name"]);
	$addit1_introduce=$configutil->splash_new($_POST["addit1_introduce"]);
	$addit1_patrn=$configutil->splash_new($_POST["addit1_patrn"]);
	$addit2=$configutil->splash_new($_POST["addit2"]);
	$addit2_name=$configutil->splash_new($_POST["addit2_name"]);
	$addit2_introduce=$configutil->splash_new($_POST["addit2_introduce"]);
	$addit2_patrn=$configutil->splash_new($_POST["addit2_patrn"]);
	$up_type_SQL=" update slb_type set addit1='".$addit1."',addit1_name='".$addit1_name."',addit1_introduce='".$addit1_introduce."',addit2='".$addit2."',addit2_name='".$addit2_name."',addit2_introduce='".$addit2_introduce."',title='".$title."',code='".$code."',addit1_patrn='".$addit1_patrn."',addit2_patrn='".$addit2_patrn."' where id='".$ID."'";
	_mysql_query($up_type_SQL);	
}else if($op==101){//类型自定义删除
	$ID=$configutil->splash_new($_POST["ID"]);
	$up_type_SQL=" update slb_type set c_isvalid=0 where id='".$ID."'";
	_mysql_query($up_type_SQL);	
}
$error = mysql_error();
$terror=$terror.$E_SQL;
//echo $error."<br/>";
mysql_close($link);

$resultArr["result"] = empty($error) ? 1 : 0;
$resultArr["msg"] = empty($error) ? $success : $terror;
//$resultArr["msg"]=$op;
echo json_encode($resultArr);
?>