<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

//$customer_id =$configutil->splash_new($_POST["customer_id"]);  //前面引入的文件中已经获取了
$id =$configutil->splash_new($_POST["id"]);//switch选择器
$type=$configutil->splash_new($_POST["type"]);//时间类型1：月2：季度3：年4：星期5：本田
$CONtype=$configutil->splash_new($_POST["CONtype"]);//订单状态
$SOtype=$configutil->splash_new($_POST["SOtype"]);//在同switch选择器下的辅助选择器
$tcount=0;

switch($id){
		//case 2:
		//echo json_encode($tcount);
		//return;
		//break;
		//select DATE_FORMAT(create_time,'%Y%u') weeks,count(caseid) count from tc_case group by weeks;  
		//select DATE_FORMAT(create_time,'%Y%m%d') days,count(caseid) count from tc_case group by days;  
		//select DATE_FORMAT(create_time,'%Y%m') months,count(caseid) count from tc_case group by months; 
	case 1:
		//已售罄的商品数
			$begintime="";
			$endtime ="";
			if(empty($_POST["begintime"])){
				$begintime = date("Y-m-d");
			}else{
				$begintime = $configutil->splash_new($_POST["begintime"]);
			}
				$condition="";
			if($CONtype==1){
				$condition=" and status=1";
			}else if($CONtype==2){
				$condition=" and status=0";
			}else if($CONtype==3){
				$condition=" and paystatus=1 and status!=-1";
			}else if($CONtype==4){
				$condition=" and paystatus=0";
			}else if($CONtype==5){
				$condition=" and (sendstatus=1 or sendstatus=2)";
			}else if($CONtype==6){
				$condition=" and sendstatus=0";
			}else if($CONtype==7){
				$condition=" and sendstatus=3";
			}else if($CONtype==8){
				$condition=" and status=-1";
			}else{
				$condition="";
			}
			$cond="";
			$grup="";
		//=================================(算时间段)=================================
			if($type==1){
				$cond="%Y-%m-%d";
				$grup="days";
				$a_time = strtotime($begintime);
				$begintime = date('Y-m-01', $a_time);
				$a_time = strtotime($begintime);
				$endtime = date("Y-m-d",strtotime('+1 Month',$a_time));
				$tcount_2=array();
				$flag=0;
				for($i=0;$i<32;$i++){
					$k=$i;
					$cd='+'.$k.' day';
					$b_time = strtotime($cd,$a_time );
					$Qtime=date('Y-m-d',$b_time);
					if($flag<1){
						array_push($tcount_2,$Qtime);
						if($b_time==strtotime($endtime)){
							$flag=1;
						}
					}
				}
			}
			if($type==2){
				$cond="%Y-%m";
				$grup="months";
				$tcount=array();
				$a_time = strtotime($begintime);
				$begintime=date('Y-01-01', $a_time);
				$b_time1 = strtotime('-3 Month',strtotime($begintime));
				$a_time1 = strtotime($begintime);
				$a_time2 = strtotime('+3 Month',strtotime($begintime));
				$a_time3 = strtotime('+6 Month',strtotime($begintime));
				$a_time4 = strtotime('+9 Month',strtotime($begintime));
				$a_time5 = strtotime('+1 Year',strtotime($begintime));
				$endtime = date("Y-m-d",$a_time5);
				if($a_time>=$b_time1 &&$a_time< $a_time1 ){
					$begintime=date('Y-m-01', $b_time1);
					$endtime=date('Y-m-01', $a_time1);
				}
				if($a_time>=$a_time1 &&$a_time< $a_time2){
					$begintime=date('Y-m-01', $a_time1);
					$endtime=date('Y-m-01', $a_time2);
				}
				if($a_time>=$a_time2 &&$a_time< $a_time3){
					$begintime=date('Y-m-01', $a_time2);
					$endtime=date('Y-m-01', $a_time3);
				}
				if($a_time>=$a_time3 &&$a_time< $a_time4){
					$begintime=date('Y-m-01', $a_time3);
					$endtime=date('Y-m-01', $a_time4);
				}
				if($a_time>=$a_time4 &&$a_time< $a_time5){
					$begintime=date('Y-m-01', $a_time4);
					$endtime=date('Y-m-01', $a_time5);
				}
				$tcount_2=array();
				$flag=0;
				for($i=0;$i<4;$i++){
				$k=$i;
				$cd='+'.$k.' Month';
				$b_time = strtotime($cd,strtotime($begintime));
				$Qtime=date('Y-m-d',$b_time);
				if($flag<1){
					array_push($tcount_2,$Qtime);
					if($b_time==strtotime($endtime)){
						$flag=1;
						}
					}
				}
			}
			if($type==3){
				$cond="%Y-%m";
				$grup="months";
				$tcount=array();
				$a_time = strtotime($begintime);
				$begintime=date('Y-01-01', $a_time);
				$a_time5 = strtotime('+1 Year',strtotime($begintime));
				$endtime = date("Y-m-d",$a_time5);
				$tcount_2=array();
				$flag=0;
				for($i=0;$i<13;$i++){
				$k=$i;
				$cd='+'.$k.' Month';
				$b_time = strtotime($cd,strtotime($begintime) );
				$Qtime=date('Y-m-01',$b_time);
				if($flag<1){
					array_push($tcount_2,$Qtime);
					if($b_time==strtotime($endtime)){
						$flag=1;
						}
					}
				}
			}
			
			if($type==4){
				$tcount=array();
				$cond="%Y-%m-%d";
				$grup="days";
				//$begintime = date("Y-m-d");
				$a_time = strtotime($begintime);
				$Wday = date("w",$a_time);//总天数
				$a_time = strtotime('-'.$Wday.' Day',$a_time);
				$a_time = strtotime('+1 Day',$a_time);
				$begintime=date('Y-m-d', $a_time);
				$a_time5 = strtotime('+7 Day',strtotime($begintime));
				$endtime = date("Y-m-d",$a_time5);
				$tcount_2=array();
				$flag=0;
				for($i=0;$i<8;$i++){
				$k=$i;
				$cd='+'.$k.' Day';
				$b_time = strtotime($cd,strtotime($begintime) );
				$Qtime=date('Y-m-d',$b_time);
				if($flag<1){
					array_push($tcount_2,$Qtime);
					if($b_time==strtotime($endtime)){
						$flag=1;
						}
					}
				}
			}
			if($type==5){
				$a_time = strtotime($begintime);
				$cond="%Y-%m-%d";
				$grup="days";
				$endtime = date("Y-m-d",strtotime('+1 Day',$a_time));
				$tcount_2=array();
				array_push($tcount_2,$begintime,$endtime);
			}
		//=================================(SQL段)====================================
		$tcount=array();	
		if($SOtype==1){
				// $query="select DATE_FORMAT(paytime,'".$cond."') ".$grup.",sum(totalprice) as tcount1  from weixin_commonshop_orders where customer_id=".$customer_id." and return_status != 8 and isvalid=1 ".$condition."   and UNIX_TIMESTAMP(paytime)>=".strtotime($begintime)." and UNIX_TIMESTAMP(paytime)<".strtotime($endtime)."  group by ".$grup."";
				$query = "select DATE_FORMAT(paytime, '{$cond}') {$grup},
				 			   IFNULL(sum(price),0) - IFNULL(sum(ExpressPrice),0) as tcount1,
				 			   sum(pay_currency) as tpay_currency
				 		  from weixin_commonshop_order_prices
				 		 where isvalid= 1
				 		   ".$condition."
				 		   and batchcode IN(
				 				select batchcode
				 				  from weixin_commonshop_orders
								 where customer_id= '".$customer_id."'
				 				   and isvalid= 1
				 				   and sendstatus!=4
				 				   and sendstatus!=6
				 				   ".$condition."
				 				   and paytime>= '".$begintime."'
				 				   and paytime< '".$endtime."' group by batchcode)
				 		 group by {$grup}";

				//$query = "select DATE_FORMAT(paytime, '%Y-%m-%d') days,sum(NoExpPrice) as tcount1,sum(pay_currency) as tpay_currency from weixin_commonshop_order_prices where customer_id=".$customer_id." and isvalid=1 and sendstatus!=4 and sendstatus!=6 ".$condition." and paytime>='".$begintime."' and paytime<'".$endtime."' group by days";

				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$tcount1=0;
				$tpay_currency=0;
				$price=0;
				$ki=0;
				$query09=$query;
				while ($row = mysql_fetch_object($result)) {
					$map=array();
					$Ttime= $row->$grup;
					for($i=$ki;$i<(count($tcount_2)-1);$i++){
						$map=array();
						if(strtotime($tcount_2[$i])<strtotime($Ttime)){
							array_push($map,$tcount_2[$i],0);
							array_push($tcount,$map);
						}else if(strtotime($tcount_2[$i])==strtotime($Ttime)){
							$Ttime=$tcount_2[$i];
							$ki=$i+1;
							break;
						}
						
					}
					$tcount1 = $row->tcount1;
					if($tcount1==null  || $tcount1=="" ){
						$tcount1=0;
					}
					
					$tpay_currency = $row->tpay_currency;
					if($tpay_currency==null  || $tpay_currency=="" ){
						$tpay_currency=0;
					}
					
					$tcount1 = $tcount1 - $tpay_currency;
					
					array_push($map,$Ttime,$tcount1);
					array_push($tcount,$map);
				}
	
				for($i=count($tcount);$i<(count($tcount_2)-1);$i++){
						$map=array();
							array_push($map,$tcount_2[$i],0);
							array_push($tcount,$map);	
					}
				// $query= "SELECT sum(totalprice) as tcount1  FROM weixin_commonshop_orders  where customer_id=".$customer_id." and  isvalid=1 ".$condition." ";
				// $query = "select IFNULL(sum(price),0) - IFNULL(sum(ExpressPrice),0) as tcount1,
				//  			   sum(pay_currency) as tpay_currency
				//  		  from weixin_commonshop_order_prices
				//  		 where isvalid= 1
				//  		   ".$condition."
				//  		   and batchcode IN(
				// 				select batchcode
				//  				  from weixin_commonshop_orders
				//  				 where customer_id= '".$customer_id."'
				//  				   and isvalid= 1
				//  				   and sendstatus!=4
				//  				   and sendstatus!=6
				//  				   ".$condition." group by batchcode)";	

				// //$query = "select sum(NoExpPrice) as tcount1,sum(pay_currency) as tpay_currency from weixin_commonshop_order_prices where customer_id=".$customer_id." and isvalid=1 and sendstatus!=4 and sendstatus!=6 ".$condition;

				// 	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				// 	while ($row = mysql_fetch_object($result)) {
				// 		$tcount1 = $row->tcount1;
				// 		if($tcount1==null  || $tcount1=="" ){
				// 			$tcount1=0;
				// 		}
						
				// 		$tpay_currency = $row->tpay_currency;
				// 		if($tpay_currency==null  || $tpay_currency=="" ){
				// 			$tpay_currency=0;
				// 		}
						
				// 		$tcount1 = $tcount1 - $tpay_currency;
						
				// 		array_push($tcount,$tcount1);
				// 	}
				//array_push($tcount,$query09);
				
			}
			if($SOtype==2){
				$query= "SELECT DATE_FORMAT(createtime,'".$cond."') ".$grup.",count(distinct batchcode) as tcount1  FROM weixin_commonshop_orders  where customer_id=".$customer_id."  and  isvalid=1 ".$condition."   and UNIX_TIMESTAMP(createtime)>=".strtotime($begintime)." and UNIX_TIMESTAMP(createtime)<".strtotime($endtime)."  group by ".$grup."";	
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$tcount1=0;
				$price=0;
				$ki=0;
				//array_push($tcount,$query);
				while ($row = mysql_fetch_object($result)) {
					$map=array();
					$Ttime= $row->$grup;
					for($i=$ki;$i<(count($tcount_2)-1);$i++){
						$map=array();
						if(strtotime($tcount_2[$i])<strtotime($Ttime)){
							array_push($map,$tcount_2[$i],0);
							array_push($tcount,$map);
						}else if(strtotime($tcount_2[$i])==strtotime($Ttime)){
							$Ttime=$tcount_2[$i];
							$ki=$i+1;
							break;
						}
						
					}
					$tcount1 = $row->tcount1;
					if($tcount1==null  || $tcount1=="" ){
						$tcount1=0;
					}
					array_push($map,$Ttime,$tcount1+$price);
					array_push($tcount,$map);
				}
	
				for($i=count($tcount);$i<(count($tcount_2)-1);$i++){
						$map=array();
							array_push($map,$tcount_2[$i],0);
							array_push($tcount,$map);	
					}
				
				
			}
		if($SOtype==8 ){
			//$query= "SELECT DATE_FORMAT(o.paytime,'".$cond."') ".$grup.",sum( o.totalprice) as tcount1  FROM weixin_commonshop_orders o join weixin_commonshop_order_express_prices p on o.batchcode=p.batchcode where o.customer_id=".$customer_id." and  o.isvalid=1 ".$condition."   and UNIX_TIMESTAMP(o.paytime)>=".strtotime($begintime)." and UNIX_TIMESTAMP(o.paytime)<".strtotime($endtime)."  group by ".$grup."";
			$query = "select DATE_FORMAT(paytime, '{$cond}') {$grup},
			 				   sum(price) as tcount1,
			 				   sum(pay_currency) as tpay_currency
			 			  from weixin_commonshop_order_prices
			 			 where isvalid= 1
			 			   ".$condition."
			 			   and batchcode IN(
			 					select batchcode
			 					  from weixin_commonshop_orders
			 					 where customer_id= '".$customer_id."'
		 	 					   and isvalid= 1
			 					   and sendstatus!=4
			 					   and sendstatus!=6
			 					   ".$condition."
			 					   and paytime>= '".$begintime."'
			 					   and paytime< '".$endtime."' group by batchcode)
			 			 group by {$grup}";

			//$query = "select DATE_FORMAT(paytime, '%Y-%m-%d') days,sum(price) as tcount1,sum(pay_currency) as tpay_currency from weixin_commonshop_order_prices where customer_id=".$customer_id." and isvalid=1 and sendstatus!=4 and sendstatus!=6 ".$condition." and paytime>='".$begintime."' and paytime<'".$endtime."'";

			$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$tcount1=0;
				$price=0;
				$ki=0;
				//array_push($tcount,$query);
				while ($row = mysql_fetch_object($result)) {
					$map=array();
					$Ttime= $row->$grup;
					for($i=$ki;$i<(count($tcount_2)-1);$i++){
						$map=array();
						if(strtotime($tcount_2[$i])<strtotime($Ttime)){
							array_push($map,$tcount_2[$i],0);
							array_push($tcount,$map);
						}else if(strtotime($tcount_2[$i])==strtotime($Ttime)){
							$Ttime=$tcount_2[$i];
							$ki=$i+1;
							break;
						}
						
					}

					$tcount1 = $row->tcount1;
					if($tcount1==null  || $tcount1=="" ){
						$tcount1=0;
					}
					
					$tpay_currency = $row->tpay_currency;
					if($tpay_currency==null  || $tpay_currency=="" ){
						$tpay_currency=0;
					}
					
					$tcount1 = $tcount1 - $tpay_currency;
					
					/*$batchcode_arr = array();
					$query_batchcode = "SELECT DISTINCT batchcode FROM weixin_commonshop_orders where customer_id=".$customer_id." and isvalid=1 ".$condition." and UNIX_TIMESTAMP(paytime)>=".strtotime($Ttime)." and UNIX_TIMESTAMP(paytime)<".(strtotime($Ttime)+86400);
					$result_batchcode = _mysql_query($query_batchcode) or die('Query_batchcode failed:'.mysql_error());
					while( $row_batchcode = mysql_fetch_object($result_batchcode) ){
						$batchcode = $row_batchcode->batchcode;
						
						array_push($batchcode_arr,$batchcode);
					}*/
					
					/*$batchcode_str = implode(',',$batchcode_arr);*/
					
					/*$price = 0;	//运费
					if( !empty($batchcode_arr) ){
						$query_exp = "SELECT SUM(price) AS exp_price FROM weixin_commonshop_order_express_prices WHERE isvalid=true AND (";
						
						foreach( $batchcode_arr as $val ){
							$query_exp .= " batchcode='".$val."' OR";
						}
						
						$query_exp = substr($query_exp,0,-2);
						$query_exp .= ')';
						
						$result_exp = _mysql_query($query_exp) or die('Query_exp failed:'.mysql_error());
						while( $row_exp = mysql_fetch_object($result_exp) ){
							$price = $row_exp->exp_price;
						}
					}*/
					
					
					array_push($map,$Ttime,$tcount1);
					array_push($tcount,$map);
				}
	
				for($i=count($tcount);$i<(count($tcount_2)-1);$i++){
						$map=array();
							array_push($map,$tcount_2[$i],0);
							array_push($tcount,$map);	
				}
				/*$query= "SELECT ( sum(p.price)) as price  FROM weixin_commonshop_orders o join weixin_commonshop_order_express_prices p on o.batchcode=p.batchcode where o.customer_id=".$customer_id." and  o.isvalid=1 ".$condition." ";	
					$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
						$price = $row->price;
						if($price==null  || $price=="" ){
							$price=0;
						}

					}
				$query= "SELECT sum(totalprice) as tcount1  FROM weixin_commonshop_orders  where customer_id=".$customer_id." and  isvalid=1 ".$condition." ";	
					$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
						$tcount1 = $row->tcount1;
						if($tcount1==null  || $tcount1=="" ){
							$tcount1=0;
						}
						
					}*/
				$query = "select sum(price) as tcount1,
				 			   sum(pay_currency) as tpay_currency
				 		  from weixin_commonshop_order_prices
				 		 where isvalid= 1
				 		   ".$condition."
				 		   and batchcode IN(
				 				select batchcode
				 				  from weixin_commonshop_orders
				 				 where customer_id= '".$customer_id."'
				 				   and isvalid= 1
				 				   and sendstatus!=4
				 				   and sendstatus!=6
				 				   ".$condition." group by batchcode)";			

				//$query = 'select sum(price) as tcount1,sum(pay_currency) as tpay_currency from weixin_commonshop_order_prices where customer_id='.$customer_id.' and isvalid=1 and sendstatus!=4 and sendstatus!=6 '.$condition;
								   	
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$tcount1 = $row->tcount1;
					if($tcount1==null  || $tcount1=="" ){
						$tcount1=0;
					}
					
					$tpay_currency = $row->tpay_currency;
					if($tpay_currency==null  || $tpay_currency=="" ){
						$tpay_currency=0;
					}
					
					$tcount1 = $tcount1 - $tpay_currency;
					
					array_push($tcount,$tcount1);
				}
							   
				
				
		}

		if($SOtype==3){//某个产品销售
			$tcount=array();
			$PPID=$configutil->splash_new($_POST["PPID"]);
			$query= "SELECT DATE_FORMAT(o.paytime,'".$cond."') ".$grup.",sum(rcount) as tcount1,pid,sum(totalprice) as tcount2 FROM weixin_commonshop_orders  where pid=".$PPID." and customer_id=".$customer_id." and  isvalid=1 ".$condition."   and UNIX_TIMESTAMP(createtime)>=".strtotime($begintime)." and UNIX_TIMESTAMP(createtime)<".strtotime($endtime)."  group by ".$grup."";
			$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$tcount1=0;
				$price=0;
				$ki=0;
				//array_push($tcount,$query);
				while ($row = mysql_fetch_object($result)) {
					$map=array();
					$Ttime= $row->$grup;
					for($i=$ki;$i<(count($tcount_2)-1);$i++){
						$map=array();
						if(strtotime($tcount_2[$i])<strtotime($Ttime)){
							array_push($map,$tcount_2[$i],0,0,0);
							array_push($tcount,$map);
						}else if(strtotime($tcount_2[$i])==strtotime($Ttime)){
							$Ttime=$tcount_2[$i];
							$ki=$i+1;
							break;
						}
						
					}
					$tcount1 = $row->tcount1;
					$pid=$row->pid;
					$tcount2 = $row->tcount2;
					if($tcount1==null  || $tcount1=="" ){
						$tcount1=0;
					}
					if($tcount2==null  || $tcount2=="" ){
						$tcount2=0.00;
					}
					array_push($map,$Ttime,$tcount1,$pid,$tcount2);
					array_push($tcount,$map);
				}
	
				for($i=count($tcount);$i<(count($tcount_2)-1);$i++){
						$map=array();
							array_push($map,$tcount_2[$i],0);
							array_push($tcount,$map);	
				}				
		}
			
			
		
		
		if($SOtype==4){//各产品销售
				$query= "SELECT sum(rcount) as tcount1,pid,sum(totalprice) as tcount2 FROM weixin_commonshop_orders  where  customer_id=".$customer_id."  and isvalid=1 ".$condition."   and UNIX_TIMESTAMP(createtime)>=".strtotime($begintime)." and UNIX_TIMESTAMP(createtime)<".strtotime($endtime)." group by pid";		
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$tcount=array();
				while ($row = mysql_fetch_object($result)) {
					$tcount1 = $row->tcount1;
					$pid=$row->pid;
					$tcount2 = $row->tcount2;
					$query1= "select name from weixin_commonshop_products where id='".$pid."'";
					$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
					$name="";
					while ($row1 = mysql_fetch_object($result1)) {
						$name=$row1->name;
					}
					if($tcount1==null  || $tcount1=="" ){
						$tcount1=0;
					}
					if($tcount2==null  || $tcount2=="" ){
						$tcount2=0.00;
					}
					$map=array();
					array_push($map,$name,$tcount1,$pid,$tcount2);
					array_push($tcount,$map);
				}
			}
		if($SOtype==5){//推官员  新增推广员   现有推官员
			
			$query1= "SELECT count(id) as tcount1 FROM promoters  where  customer_id=".$customer_id." and  isvalid=1  and  status=1 and 			UNIX_TIMESTAMP(createtime)<".strtotime($begintime);
			$result = _mysql_query($query1) or die('Query failed: ' . mysql_error());
			$$tcountT=0;
			while ($row = mysql_fetch_object($result)) {
					$tcountT = $row->tcount1;
				if($tcountT==null  || $tcountT=="" ){
						$tcountT=0;
				}
				$p_time = date("Y-m-d");
				if(strtotime($p_time)<strtotime($tcount_2[$i])){
					$tcountT=0;
				}
			}
			$query= "SELECT DATE_FORMAT(createtime,'".$cond."') ".$grup.",count(id) as tcount1 FROM promoters  where  customer_id=".$customer_id." and  isvalid=1  and  status=1  and UNIX_TIMESTAMP(createtime)>=".strtotime($begintime)." and UNIX_TIMESTAMP(createtime)<".strtotime($endtime)."  group by ".$grup."";
			$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$tcount1=0;
				$price=0;
				$ki=0;
			$tcount=array();
				//array_push($tcount,$query);
				while ($row = mysql_fetch_object($result)) {
					$map=array();
					$Ttime= $row->$grup;
					for($i=$ki;$i<(count($tcount_2)-1);$i++){
						$map=array();
						if(strtotime($tcount_2[$i])<strtotime($Ttime)){
							array_push($map,$tcount_2[$i],0,$tcountT);
							array_push($tcount,$map);
						}else if(strtotime($tcount_2[$i])==strtotime($Ttime)){
							$Ttime=$tcount_2[$i];
							$ki=$i+1;
							break;
						}
						
					}
					$tcount1 = $row->tcount1;
					if($tcount1==null  || $tcount1=="" ){
						$tcount1=0;
					}
					array_push($map,$Ttime,$tcount1,$tcountT);
					array_push($tcount,$map);
					$tcountT=$tcountT+$tcount1 ;
				}
	
				for($i=count($tcount);$i<(count($tcount_2)-1);$i++){
						$map=array();
							array_push($map,$tcount_2[$i],0,$tcountT);
							array_push($tcount,$map);	
				}				
			$query2= "SELECT count(id) as tcount1 FROM promoters  where  customer_id=".$customer_id." and  isvalid=1  and  status=1 ";
			$result = _mysql_query($query2) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$tcount1 = $row->tcount1;
					if($tcount1==null  || $tcount1=="" ){
						$tcount1=0;
					}

					array_push($tcount,$tcount1);
				}		
		}
		if($SOtype==6){//总粉丝  新增粉丝  粉丝且关注的
            //上一天
            $lasttime = date('Y-m-d',(strtotime($tcount_2[0]) - 3600*24));


            //原始粉丝
            $query = "SELECT distinct count(DISTINCT a.orginid) as tcount 
                      FROM weixin_users u 
                      LEFT JOIN weixin_attusers a on a.orginid=u.weixin_fromuser 
                      WHERE u.customer_id='".$customer_id."' AND a.customer_id='".$customer_id."' AND u.isvalid=1 AND a.status>0 AND u.createtime>'1971-01-01' AND UNIX_TIMESTAMP(u.createtime)<".strtotime($begintime);
            // var_dump($query);
                      // var_dump($begintime);
            $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
            $row = mysql_fetch_object($result);
            $tcount1 = $row->tcount;//原始粉丝数


            $query = "SELECT LEFT(u.createtime,10) AS createtime,a.status,COUNT(u.id) tcount
                        FROM weixin_users u 
                        LEFT JOIN weixin_attusers a ON a.orginid=u.weixin_fromuser  
                        WHERE u.customer_id=$customer_id AND u.isvalid=1 AND a.status>0 AND u.createtime>='".$begintime."' AND u.createtime<'".$endtime."' 
                        GROUP BY a.status,createtime ORDER BY createtime asc,a.status asc";
                        // var_dump($query);
            $result = _mysql_query($query) or die('Query failed: ' . mysql_error());

			$i = 0;
			while ($row = mysql_fetch_object($result)) {
				$data[] = array(
					'status' => $row -> status,//状态
					'count' => $row -> tcount,//新增粉丝数
					'createtime' => $row -> createtime//日期
					);
			}
			//print_r($data);
			for($i=0;$i<(count($tcount_2)-1);$i++) {
                $ccount = 0;
				foreach ($data as $k => $v) {
					if ($v['createtime'] == $tcount_2[$i]) {
						if ($v['status'] > 0) {
							$ccount = $ccount + $v['count'];
						} elseif ($v['status'] == 0) {
							$ccount = $ccount - $v['count'];
						}
					}
					//echo "createtime =".$createtime."   tcount_2[$i]=".$tcount_2[$i]."<br>";
				}
				$arr=array();
				if ($tcount_2[$i] > date("Y-m-d")){
					array_push($arr,$tcount_2[$i],0,0,0);
				}else{
					array_push($arr,$tcount_2[$i],$ccount,$tcount1,$tcount1+$ccount);
				}

                array_push($tcount,$arr);
                $tcount1 = $tcount1+$ccount;
            }

            array_push($tcount,$tcount1);
            array_push($tcount,$tcount1);


		}


		if($SOtype==7){//标签销售额
			if($_POST["ADtype"] == -1){
				$condition = "";
			}else{
				$ADtype = $configutil->splash_new($_POST["ADtype"]);
				$condition = " and from_type='".$ADtype."'";
			}
			
            $query1 = "SELECT DATE_FORMAT(statisticaltime,'".$cond."') ".$grup.",SUM(total_money) as total_money FROM weixin_advertise_data_statistics  where customer_id=".$customer_id." and  isvalid=1 ".$condition."   and UNIX_TIMESTAMP(statisticaltime)>='".strtotime($begintime)."' and UNIX_TIMESTAMP(statisticaltime)<'".strtotime($endtime)."' group by ".$grup."";	
			$result1 = _mysql_query($query1) or die('Query failed101: ' . mysql_error());
			$total_money=0;
			$ki=0;
			//array_push($tcount,$query);
			while ($row1 = mysql_fetch_object($result1)) {
				$map=array();
				$Ttime= $row1->$grup;
				for($i=$ki;$i<(count($tcount_2)-1);$i++){
					$map=array();
					if(strtotime($tcount_2[$i])<strtotime($Ttime)){
						array_push($map,$tcount_2[$i],0);
						array_push($tcount,$map);
					}else if(strtotime($tcount_2[$i])==strtotime($Ttime)){
						$Ttime=$tcount_2[$i];
						$ki=$i+1;
						break;
					}
					
				}
				$total_money = $row1->total_money;
				if($total_money==null  || $total_money=="" ){
					$total_money=0;
				}
				array_push($map,$Ttime,$total_money);
				array_push($tcount,$map);
			}

			for($i=count($tcount);$i<(count($tcount_2)-1);$i++){
					$map=array();
						array_push($map,$tcount_2[$i],0);
						array_push($tcount,$map);	
			}

			$all_total_money = 0;
			$query2 = "SELECT SUM(total_money) all_total_money FROM weixin_advertise_tag_statistics  where customer_id=".$customer_id." and  isvalid=1 ".$condition;
			$result2 = _mysql_query($query2) or die('Query failed102: ' . mysql_error());
			while ($row2 = mysql_fetch_object($result2)) {
				if ($row2->all_total_money != null){
					$all_total_money = $row2->all_total_money;
				}				
			}
			array_push($tcount,$all_total_money);
		}
		//==================================(返回区)==================================
		array_push($tcount,$begintime);
		array_push($tcount,$endtime);

		echo json_encode($tcount);
		return;
		break;

}
?>




