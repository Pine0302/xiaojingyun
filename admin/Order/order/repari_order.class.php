<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');   //配置
require('../../../../weixinpl/customer_id_decrypt.php');   //解密参数
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require('../../../../weixinpl/common/utility_shop.php');  //商城方法
$shopmessage= new shopMessage_Utlity(); 

/********参数部分********/
 $batchcode 	= trim($_POST["batchcode"]);		//订单号
 $customer_id 	= $_POST["customer_id"];	//商家ID
 $op 			= $_POST["op"];				//方法名
/********参数部分********/

switch($op){
	case 'GetSupply_Money';
	
		 $query="select status,user_id,exp_user_id,sendstatus,supply_id,sum(totalprice) as totalprice,agent_id,agentcont_type,paystyle,pname from weixin_commonshop_orders where batchcode='".$batchcode."' and isvalid=true";
		$result = _mysql_query($query) or die('Query Failed '.mysql_error());
		$status =  0; 		  //
		$agentcont_type =  0; //0:推广员结算路线 1:代理商结算路线
		$parent_id 		= -1;
		$sendstatus 	=  0;
		$supply_id		= -1; //供应商user_id
		$agent_id 		= -1; //代理商user_id
		$by_user_id 	= -1; //购买者user_id
		$by_user_id = -1; //购买者user_id
		while ($row = mysql_fetch_object($result)) {
			$status 		= $row->status;
			$by_user_id 	= $row->user_id;
			$sendstatus 	= $row->sendstatus;
			$parent_id 		= $row->exp_user_id;
			$supply_id 		= $row->supply_id;
			$totalprice 	= $row->totalprice;
			$agent_id 		= $row->agent_id;
			$agentcont_type = $row->agentcont_type;
			$paystyle 		= $row->paystyle;

		}
		
		$rnum = 0;
		 $query2 = "select count(1) as count from  weixin_commonshop_orders where batchcode='".$batchcode."' and isvalid=true";
		$result2 = _mysql_query($query2) or die('Query Failed1 '.mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
			$rnum = $row2->count;
		}		
		if(empty($rnum)){
			echo '该订单不存在，请检查订单号！';
			return;
		}
		if($status == 0){
			echo '该订单未确认过，不能处理，请在后台重新确认订单！';
			return;
		}
		if($supply_id >0){
			
			$ccount = -1;
			$query2="select count(1) as ccount from weixin_commonshop_applysupplys where status=1 and isvalid=true and user_id=".$supply_id;
			$result2 = _mysql_query($query2) or die('Query failed2: ' . mysql_error());		
			while ($row2 = mysql_fetch_object($result2)) {
				$ccount = $row2->ccount;			
			}
			if($ccount == 0){
				echo '异常，该供应商申请表不存在该ID:'.$supply_id ;
				return;
			}
			$query = "select id from weixin_commonshop_agentfee_records where batchcode='".$batchcode."' and user_id=".$supply_id." and isvalid=true ";
			$id = -1;
			$result = _mysql_query($query)or die('Query Failed1:'.mysql_error());
			while ($row = mysql_fetch_object($result)) {
				$id = $row->id;
			}
			if($id>0){  //已经入账，无需处理
				echo '此订单已经入账，无需处理！';				
			}else{	
				//供应商结算方式 start
				$Supply_Money = 0;
				
					$query5="select price from weixin_commonshop_order_express_prices where batchcode='".$batchcode."' and isvalid=true limit 0,1";
					$result5 = _mysql_query($query5) or die('Query failed3: ' . mysql_error());
					$express_price = 0;
					$f_totalprice = 0;
					while ($row5 = mysql_fetch_object($result5)) {
						$express_price = $row5->price;
						break;
					}
					$shopmessage->GetSupply_Money($batchcode,$totalprice,$express_price,$customer_id,$agent_id,$supply_id);
					
					//插入日志
					$sql = "insert into repair_log(isvalid,createtime,key_parameter,type,result,remark)values(true,now(),'".$batchcode."',1,1,'供应商订单重新入账')";
					_mysql_query($sql)or die('Query failed4: ' . mysql_error());
					
					echo '此订单处理成功';
			}
			//供应商结算方式 end

		}else{
			echo '此订单不是供应商订单！';
		}		
		
		
			
	break;
}


?>