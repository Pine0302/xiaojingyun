<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require_once '../../../../weixinpl/common/excel/phpExcelReader/Excel/reader.php';

$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('utf-8');
$log_username = $_SESSION['username'];
require('../../../../weixinpl/common/utility_shop.php');  //商城方法
$shopmessage = new shopMessage_Utlity();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!is_uploaded_file($_FILES["excelfile"]["tmp_name"]))
	//是否存在文件
	{
	}else{

	    $Import_TmpFile = $_FILES['excelfile']['tmp_name'];
        $data->read($Import_TmpFile);

		$order_way = $_POST['order_way'];	//订单来源，1：导出记录，2：导出飞豆

		$k = 0;
		for($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
		
			if ( $order_way == 1 ){
				$batchcode      = $data -> sheets[0]['cells'][$i][1];
				$order_time     = $data -> sheets[0]['cells'][$i][2];
				$is_sign        = $data -> sheets[0]['cells'][$i][3];
			} 
			else if ( $order_way == 2 )
			{
				$batchcode      = $data -> sheets[0]['cells'][$i][1];
				$order_time     = $data -> sheets[0]['cells'][$i][2];
				$is_sign        = $data -> sheets[0]['cells'][$i][3];
			}
			//去掉特殊符号
			$batchcode      = str_replace("'",'',$batchcode);
			$batchcode      = str_replace("＇",'',$batchcode);
			$order_time     = str_replace("'",'',$order_time);
			$order_time     = str_replace("＇",'',$order_time);
			$is_sign     	= str_replace("'",'',$is_sign);
			$is_sign     	= str_replace("＇",'',$is_sign);

			$is_sign == "确认" ? $is_sign = 1 : $is_sign = 2;

			$datas[$k]['batchcode']  = $batchcode;
			$datas[$k]['order_time'] = $order_time;
			$datas[$k]['is_sign']    = $is_sign;

			$k++;
		}	

		foreach($datas as $k=>$v)
		{
			$is_sign    = $v['is_sign'];
			$batchcode  = $v['batchcode'];
			$paystatus  = 1;
			$sendstatus = 2;
			$status     = '';
			if($is_sign == 2)
			{
				$paystatus  = 0;
				$sendstatus = 1;
				$status = ",status=-1"; 
			}
			$confirm_receivetime = '';
			if($sendstatus == 2)
			{
				$time   = date('Y-m-d H:i:s',time());
				$confirm_receivetime = ",confirm_receivetime ='".$time."'";
			}

			$order_sel  = "select id from weixin_commonshop_orders where is_pay_on_delivery = 1 and is_sign=0 and batchcode='".$batchcode."' and customer_id=".$customer_id;
			$order_rest = _mysql_query($order_sel);
			if($order_rest == false || $batchcode == '')
			{
				continue;
			}	
			$query_sign = "update weixin_commonshop_orders set is_sign=$is_sign,paystatus=$paystatus,sendstatus=$sendstatus $confirm_receivetime $status where batchcode='".$batchcode."' and customer_id=".$customer_id;
			echo $query_sign;
			_mysql_query($query_sign);  


			//修改佣金paytype=0
			$query_promotes = "update weixin_commonshop_order_promoters set paytype = 0 where batchcode='".$batchcode."'";
			_mysql_query($query_promotes);

			//发送拒绝签收消息
			if($is_sign == 2)
			{

				$sel_promotes = "select user_id,remark,level_name from weixin_commonshop_order_promoters where batchcode='".$batchcode."' and paytype =0";
				$result_pro   = _mysql_query($sel_promotes) or die('Query_apply failed: ' . mysql_error());
				while($row 	  = mysql_fetch_object($result_pro))
				{
					$user_id  = $row->user_id;
					$usermon  = $row->remark;
					$level    = $row->level_name;
					$userid[] = $user_id;	
					$level_name[]    = $level;
					if(mb_strpos($usermon,'元') == false)
					{
						$usermon_str = $usermon.'元';
					}
					$usermoney[]     = $usermon_str;
				}	

				foreach($userid as $k=>$v)
				{
					/*读取顾客资料开始*/
					$query_user = "SELECT
										weixin_name,
										province,
										city,
										sex
									FROM
										weixin_users
									WHERE
										customer_id = ".$customer_id."
									AND id = ".$v."
									AND isvalid = TRUE
									LIMIT 0,1";
					$result_user  = _mysql_query($query_user) or errorResult($result,14007);
					$consume_name = "佚名";	//微信名
					$province     = "";		//省份
					$city         = "";		//城市
					$sexstr 	  = "保密";	//性别
					$sex		  = 0;		//性别
					while ($row = mysql_fetch_object($result_user)) {
					  $consume_name = $row->weixin_name;
					  $province     = $row->province;
					  $city 		= $row->city;
					  $sex 			= $row->sex;
					}
					$consume_name = mysql_real_escape_string($consume_name);
					switch($sex){
						case 1:
							$sexstr = "男";
							break;
						case 2:
							$sexstr = "女";
							break;
						default:
							$sexstr = "保密";
					}
					/*读取顾客资料结束*/

					$open     = $shopmessage->query_openid($customer_id,$v);
					$openid   = $open['openid'];

					$content_head2 = "亲，您流失了".$usermoney[$k]."的佣金"."\r\n";
					$content = $content_head2.
								"来源：【货到付款订单拒支付】\n".
								"身份：【".$level_name[$k]."】\n".

								"顾客：".$consume_name."\n".
								"定位：".$province.$city."\n".
								"性别：".$sexstr."\n".
								"时间：".date( "Y-m-d H:i:s")."";
					$send_object[$k]["content"]	= $content;
					$send_object[$k]["openid"]	= $openid;			

				}

				//发送消息
				$is_commission_message  = 1;//佣金消息提示开关，0关，1开
				$is_commission_scope    = 0;//佣金消息提示范围，0所有人，1推广员提示
				$send_len = count( $send_object );
				for ($i = 0; $i < $send_len; $i++) {
					$send_con 		= $send_object[$i]["content"];
					$send_openid	= $send_object[$i]["openid"];
					$query_is_extension_status   = $shopmessage->query_is_extension_status($send_openid);//获取is_extension_status
	                $is_extension_status       = $query_is_extension_status["is_extension_status"];

					if ($is_commission_message==1) {
	                    if ($is_commission_scope==0) {
	                         //发送消息
	                        $shopmessage->SendMessage($send_con, $send_openid, $customer_id);
	                     }else{
	                        if ($is_extension_status>0) {
	                            //发送消息
	                            $shopmessage->SendMessage($send_con, $send_openid, $customer_id);
	                         }
	                     }
	                }
				}

			}	

			//插入日志
			$query_log = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) values('".$batchcode."',31,'平台确认签收订单','".$log_username."',now(),1)";
			_mysql_query($query_log);

			//$json["status"] = 0;
			//$json["line"] = 41;
			//$is_sign == 1 ? $json["msg"] = "编号：".$batchcode."，签收成功" : $json["msg"] = "编号：".$batchcode."，拒绝签收成功";
		}	

	}		
	
}
	


$error =mysql_error();
 mysql_close($link);
 //echo $error;
 //echo $parent_id;
echo "<script>location.href='order.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
?>