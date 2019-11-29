<?php
header("Content-type: text/html; charset=utf-8"); //ini_set('display_errors','on');
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');

$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

$batchcode="";
if(!empty($_GET["batchcode"])){
	$batchcode = $configutil->splash_new($_GET["batchcode"]);
}
$_class = -1;
if(!empty($_GET["class"])){
	$_class = $configutil->splash_new($_GET["class"]);
}
$is_comfirm = false;//判断订单是否已完成

 if($_class>0){
	switch($_class){
		case 1://商城订单
			$sql = "SELECT id FROM weixin_commonshop_orders WHERE isvalid=true AND paystatus=true AND status=1 AND batchcode='".$batchcode."' LIMIT 1";
		break;
		case 2:
			$sql = "SELECT id FROM package_order_t WHERE isvalid=true AND paystatus=true AND (status=1 OR status=4) AND batchcode='".$batchcode."' LIMIT 1";
		break;
	}
	
	$result = _mysql_query($sql) or die('SQL failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$is_comfirm = empty($row->id)?false:true;
	}
	 
 }

$repay_id = -1;
$sql_repay = "SELECT id FROM weixin_order_commission_repay_log WHERE isvalid=true AND status=1 AND batchcode='".$batchcode."' LIMIT 1";
$result_repay = _mysql_query($sql_repay) or die('sql_repay failed: ' . mysql_error()); 
while ($row_repay = mysql_fetch_object($result_repay)) { 
	$repay_id = $row_repay->id;
}
//$query_reward_global = "select Glob_commission from now_pay_commission where isvalid=true and custi1d=' limit 0,1";
//$result_reward_global = _mysql_query($query_reward_global) or sqlErr($logger->debug(mysql_error()),14041); 

//errlog4php($logger->info("Some text that will be discarded"));
?>

<!doctype html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>返佣说明 订单号:<?php echo $batchcode;?></title>
<style>
.operation-btn{padding: 3px 5px;background-color: #06a7e1;color: #fff;border-radius: 2px;cursor:pointer;}
</style>
</head>
<body> 
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">返佣说明</a>
				</div>
			</div>
			<!--列表头部切换结束-->
			<div class="WSY_remind_main">
				<form class="search" id="search_form" method="post" action="cash.php?customer_id=AzBVZ1UzVGk=">
					<div class="WSY_list" style="margin-top: 18px;">
						<li class="WSY_left"><a>订单号：<?php echo $batchcode; ?></a></li>		
						<ul class="WSY_righticon">
							<li><a style="margin-right:40px;" href="javascript:history.go(-1);"><td valign="bottom" align="right">返回</td></a></li>
							<?php if( $repay_id > 0 ){?>
							<li><a style="margin-right:40px;" href="order_repay_log.php?batchcode=<?php echo $batchcode; ?>"><td valign="bottom" align="right">查看操作记录</td></a></li>
							<?php	} ?>							
						</ul>
					</div>     
				</form>
  
				<table width="97%" class="WSY_table" id="WSY_t1">
					<thead class="WSY_table_header">
						<th width="20%" nowrap="nowrap">推广员信息</th>
						<th width="10%" nowrap="nowrap">佣金状态</th>
						<th width="10%" nowrap="nowrap">佣金/积分</th>
						<th width="10%" nowrap="nowrap">奖励说明</th>
						<th width="20%" nowrap="nowrap">返佣时间</th>
						<th width="30%" nowrap="nowrap">备注</th>
						<?php if($is_comfirm){ ?>
						<th width="10%" nowrap="nowrap">操作</th>
						<?php } ?>
					</thead>
					<tbody>
					<?php 
						
						$query  = "select id_new,user_id,reward,card_member_id,createtime,paytype,remark,type,commission_type,commission_score,class from weixin_commonshop_order_promoters where isvalid=true and batchcode='".$batchcode."'";

						 $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
						$total_money = 0;
						$total_score = 0;
						 while ($row = mysql_fetch_object($result)) {
							$id_new = $row->id_new;
							$user_id = $row->user_id;
							$class = $row->class;
							$sql="select isAgent,is_consume from promoters where isvalid=true and user_id=".$user_id." limit 1";
							$result3 = _mysql_query($sql) or die('Query failed: ' . mysql_error());
							while ($row3 = mysql_fetch_object($result3)) {
								$isAgent = $row3->isAgent;
								$is_consume = $row3->is_consume;	//判断 0:不是初级店铺奖励 1:初级店铺奖励
							}
							$sql="select is_team,is_shareholder from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
							$result3 = _mysql_query($sql) or die('Query failed: ' . mysql_error());
							while ($row3 = mysql_fetch_object($result3)) {
								$is_team = $row3->is_team;					//是否开启团队奖励
								$is_shareholder = $row3->is_shareholder;	//是否开启股东分红奖励
								break;
							}
							$query4="select a_name,b_name,c_name,d_name from weixin_commonshop_shareholder where isvalid=true and customer_id=".$customer_id." limit 0,1";
							$result4 = _mysql_query($query4);
							while($row4 = mysql_fetch_object($result4)){
								$a_name=$row4->a_name;
								$b_name=$row4->b_name;
								$c_name=$row4->c_name;
								$d_name=$row4->d_name;
							}
							
							$consume_name ="";
							if($is_team==1 && $is_shareholder==0){
								if($is_consume>0){
									$consume_name = "(初级店铺奖励)";
								}
							}else if($is_shareholder==1){
								switch($is_consume){
									case 1: $consume_name = "(店铺奖励-".$d_name.")"; break;
									case 2: $consume_name = "(店铺奖励-".$c_name.")"; break;
									case 3: $consume_name = "(店铺奖励-".$b_name.")"; break;
									case 4: $consume_name = "(店铺奖励-".$a_name.")"; break;
								}
							}
								
							
							switch($isAgent){
								case 0:
								$user_name="推广员";
								break;
								case 1:
								$user_name="代理商";
								break;
								case 2:
								$user_name="顶级推广员";
								break;
								case 3:
								$user_name="合作商";
								break;
								case 4:
								$user_name="技师";
								break;
								case 5:
								$user_name="区代";
								break;
								case 6:
								$user_name="市代";
								break;
								case 7:
								$user_name="省代";
								break;
							}
							
							$commission_type = $row->commission_type;
							$reward = $row->reward;
							$commission_score = $row->commission_score;
							
							if($commission_type == 1){
								$reward_momey  = $reward;
								$total_money = $total_money+$reward;
							}elseif($commission_type == 2){
								$reward_momey = $commission_score;
								$total_score = $total_score+$commission_score;
							}
							
							
							$card_member_id = $row->card_member_id;
							$createtime = $row->createtime;
							$paytype = $row->paytype;
							$remark = $row->remark;
							$type = $row->type;//团队奖励
							switch($type){
								case 0:
								$type_name="分销奖励";
								break;
								case 1:
								$type_name="区域奖励";
								break;
								case 2:
								if($is_shareholder==1){
									$type_name="店铺奖励";
								}else if($is_shareholder==0){
									$type_name="初级店铺奖励";
								}
								break;
								case 3:		
								$type_name="分销奖励 ";								
								break;
								case 4:
								$type_name="区域奖励";	
								break;
								case 5:
								$type_name="店铺奖励";	
								break;
								case 6:		
								$type_name="商圈金融分销奖励 ";								
								break;
								case 7:
								$type_name="商圈金融团队奖励";	
								break;
								case 8:
								$type_name="商圈金融店铺奖励";	
								break;
								case 9:
								$type_name="绩效奖励";
								break;
								case 10:
								$type_name= (defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币')."奖励";	
								break;								
								case 11:
								$type_name="社区代理奖励";
								break;								
								case 12:
								$type_name="F2C代发奖励";	
								break;								
								case 13:
								$type_name="F2C货款";	
								break;
								case 14:
								$type_name="F2C代发扣除";	
								break;	
								case 16:
                                $type_name="招商奖励";
								break;
								case 15:
								$type_name="F2C运费分担";	
								break;
								case 17:
								$type_name="F2C推荐奖励";
								break;
								case 25:
                                $type_name="云店奖励(云店店主抽成)";
								break;
								case 26:
								$type_name="云店奖励(云店自营产品)";
								break;
								case 27:
								$type_name="云店奖励(平台对云店的抽成)";
								break;
								case 28:
								$type_name="店铺奖励";
								break;
                                case 30:
                                $type_name="订货商的高级奖励";
                                break;
                                case 32:
                                $type_name="区块链奖励";
                                break;
							}
							$sql="select customer_red_id from weixin_red_log where isvalid=true and deal_id='".$batchcode."'";
							$result3 = _mysql_query($sql) or die('Query failed: ' . mysql_error());
							while ($row3 = mysql_fetch_object($result3)) {
								$customer_red_id = $row3->customer_red_id;
							}
							if($paytype == 0){
								$paytpyestr= "已支付";
							}else if($paytype == 1){
								//$paytpyestr= "<span style='color:red'>已到会员卡</span>";
								$paytpyestr= "<span style='color:red'>已到账</span>";
							}else if($paytype == 2){
								$paytpyestr= "已退货";
								$remark = "(撤销)".$remark;
							}else if($paytype == 4){
								$paytpyestr= "已退款";
								$remark = "(撤销)".$remark;
							}else if($paytype == 5){
								$paytpyestr= "已过期";
							}else if($paytype == 3){
								$paytpyestr= "<span style='color:red'>已发红包</span>"; 
							}else if($paytype == 6){
								$paytpyestr= "<span style='color:red'>已抵扣</span>"; 
							}
							
							$weixin_name="";
							$name="";
							$sql = "select name,weixin_name from weixin_users where isvalid=true and id=".$user_id." and customer_id=".$customer_id." limit 0,1";
							$result2 = _mysql_query($sql) or die('Query failed: ' . mysql_error()); 
							while ($row2 = mysql_fetch_object($result2)) { 
								$name = $row2->name; 
								$weixin_name = $row2->weixin_name;  
							} 
							 
							
							if($type==9){
								$weixin_name='';
								$user_name	='';
								$consume_name='';
								$user_id 	='-';
								$card_member_id='-';
							}
							
					?> 
						<tr>
							<td>
								<a href="../../Users/promoter/promoter.php?search_user_id=<?php echo $user_id; ?>&customer_id=<?php echo $customer_id_en; ?>"><?php echo $weixin_name; ?>(<?php echo $user_id; ?>)</a>
								</br><?php echo $user_name;?>
								</br><?php echo $consume_name;?>						   
							</td>
							<td class="td_<?php echo $id_new; ?>">
								<?php echo $paytpyestr; ?>
								<?php if($paytype==3){?>
								</br>(<?php echo $customer_red_id?>)
								<?php } ?>							
							</td>
							
							<td><?php echo round($reward_momey,2); ?></td>
							
							<td><?php echo $type_name; ?></td>
							<td><?php echo $createtime; ?></td>
							<td><?php echo $remark; ?></td>
							<?php if($is_comfirm){ ?>
							<td class="td_<?php echo $id_new; ?>">
								<?php if( $paytype == 0 ){ ?>
								<span class="operation-btn" onclick="againRecorded(<?php echo $id_new; ?>,<?php echo $type; ?>)">重新入账</span>
								<?php }else{ ?>
								<span><?php echo $paytpyestr; ?></span>
								<?php } ?>
							</td>
							<?php } ?>
						</tr>
					   <?php } ?>
						<tr>
							<td colspan="3">
								总返佣：<span style="font-size:16px;color:red;"><?php echo round($total_money,2); ?>元
							</td>
							<?php if($is_comfirm){ ?>
							<td colspan="4">
							<?php }
							else{?>
							<td colspan="3">
							<?php } ?>
								总返佣积分：<span style="font-size:16px;color:red;"><?php echo round($total_score,2); ?>
							</td>
						</tr>
					</tbody>					
				</table>
				<div class="blank20"></div>
				<div id="turn_page"></div>
				<!--翻页开始-->
				<div class="WSY_page">
        	
				</div>
				<!--翻页结束-->
			</div>
		</div>
	</div>
<script>
	function againRecorded(id_new,type){
		$.ajax({  
			type: 'POST',  
			url: 'order.class.php?customer_id=<?php echo $customer_id_en; ?>', 
			data:{
				op			: 'again_comfirm',	
				batchcode	: '<?php echo $batchcode; ?>',		
				id_new		: id_new,	
				type		: type,	
				class		: <?php echo $_class; ?>	
			},
			dataType: 'json',  
			success: function(data){
				$('.td_'+id_new).html('<span style="color:red;">'+data.msg+'</span>');
				alert(data.msg);
			}
		});
	}
</script>

<?php mysql_close($link);?>	


</body>
</html>