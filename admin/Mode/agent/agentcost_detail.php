<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=1;//头部文件  0基本设置,1提现记录,2代理商管理

$user_id=-1;
$begintime="";
$endtime ="";
if(!empty($_GET["user_id"])){
    $user_id = $configutil->splash_new($_GET["user_id"]);
}

$istype=1;

if(!empty($_GET["istype"])){
    $istype = $configutil->splash_new($_GET["istype"]);		//1:库存记录;2:进账记录
}

$query = 'SELECT id,appid,appsecret,access_token FROM weixin_menus where isvalid=true and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
$access_token="";
while ($row = mysql_fetch_object($result)) {
	$keyid =  $row->id ;
	$appid =  $row->appid ;
	$appsecret = $row->appsecret;
	$access_token = $row->access_token;
	break;
}

//新增客户
$new_customer_count =0;
//今日销售
$today_totalprice=0;
//新增订单
$new_order_count =0;
//新增推广员
$new_qr_count =0;

$nowtime = time();
$year = date('Y',$nowtime);
$month = date('m',$nowtime);
$day = date('d',$nowtime);

$query="select count(1) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_order_count = $row->new_order_count;
   break;
}

$query="select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $today_totalprice = $row->today_totalprice;
   break;
}
$today_totalprice = round($today_totalprice,2);

$query="select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_customer_count = $row->new_customer_count;
   break;
}

$query="select count(1) as new_qr_count from promoters where isvalid=true and status=1 and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_qr_count = $row->new_qr_count;
   break;
}

$query2= "select name,weixin_name,phone from weixin_users where isvalid=true and id=".$user_id." and customer_id=".$customer_id." limit 0,1"; 
$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
$username="";
$userphone="";
while ($row2 = mysql_fetch_object($result2)) {
	$username=$row2->name;
	$phone=$row2->phone;
	$weixin_name = $row2->weixin_name;
	$username = $username."(".$weixin_name.")";
	break;
}

$query2="select agent_inventory,agent_getmoney from promoters where  status=1 and isvalid=true and user_id=".$user_id;
$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
$agent_getmoney = 0;
$agent_inventory = 0;
while ($row2 = mysql_fetch_object($result2)) {
	$agent_inventory = $row2->agent_inventory;//代理库存余额
	$agent_getmoney = $row2->agent_getmoney;//代理得到的金额
	break;
}
 $search_batchcode="";
if(!empty($_POST["search_batchcode"])){
   $search_batchcode = $configutil->splash_new($_POST["search_batchcode"]);
}
if(!empty($_POST["AccTime_A"])){
	$begintime = $configutil->splash_new($_POST["AccTime_A"]);
}
if(!empty($_POST["AccTime_B"])){
	$endtime = $configutil->splash_new($_POST["AccTime_B"]);
}

$is_distribution=0;//渠道取消代理商功能
//代理模式,分销商城的功能项是 266
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scdl' and c.id=cf.column_id";
$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_distribution=1;
}
$is_supplierstr=0;//渠道取消供应商功能
//供应商模式,渠道开通与不开通
$sp_query="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scgys' and c.id=cf.column_id";
$sp_result = _mysql_query($sp_query) or die('Query failed: ' . mysql_error());  
$sp_count= mysql_num_rows($sp_result);
if($sp_count>0){
   $is_supplierstr=1;
}
?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/agent/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/utility.js" charset="utf-8"></script>
<script type="text/javascript" src="../../../common/js/jquery.blockUI.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>

<style> 

tr {
    line-height: 22px;
}
</style>
<title>提现记录</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>  
	<!--内容框架-->
	<div class="WSY_content"> 
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			include("../../../../weixinpl/back_newshops/Mode/agent/basic_head.php"); 
			?>
			<!--列表头部切换结束-->
			<div class="WSY_remind_main"> 
				<div class="search">
					姓名：<span style="font-weight:bold;font-size:18px;"><?php echo $username; ?></span>&nbsp;&nbsp;&nbsp; 手机号：<span style="font-weight:bold;font-size:18px;"><?php echo $phone; ?></span>&nbsp;&nbsp;&nbsp;
					<?php if($istype==1){?>
					库存余额：<span style="font-weight:bold;font-size:22px;color:red"><?php echo $agent_inventory; ?>元</span>
					<?php }else{?>
					进账余额：<span style="font-weight:bold;font-size:22px;color:red"><?php echo $agent_getmoney; ?>元</span>
					 <?php }?>
					<li style="margin: 0 40px 0 0;float:right;"><a href="javascript:history.go(-1);" class="WSY_button" style="margin-top: 0;width: 60px;height: 28px;vertical-align: middle;line-height: 28px;">返回</a></li>

				</div> 
				<form class="search" id="search_form" method="post" action="agentcost_detail.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id; ?>&istype=<?php echo $istype;?>">
					<div  class="search" id="search_form">
						订单号：<input type=text name="search_batchcode" id="search_batchcode" value="<?php echo $search_batchcode; ?>" style="width:220px;" />&nbsp;&nbsp;	    
						<?php if($istype!=1){?>  
					    时间：
							<span class="WSY_generalize_dl08" >
								<span id="searchtype3" class="display">
									<input type="text" class="input Wdate" style="border: 1px solid #CFCBCB;height: 30px;margin-bottom: 5px;border-radius: 5px;" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="begintime" name="AccTime_A" value="<?php echo $begintime; ?>" maxlength="21" id="K_1389249066532" />
									-
								</span>
									<input type="text" class="input  Wdate"  style="border: 1px solid #CFCBCB;height: 30px;margin-bottom: 5px;border-radius: 5px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="endtime" name="AccTime_B" value="<?php echo $endtime; ?>" maxlength="20" id="K_1389249066580" />
							</span>&nbsp;  
						<?php } ?> 
						<input type="submit" class="search_btn"  value="搜 索">
					</div>	
				</form>
				<table width="97%" class="WSY_table" id="WSY_t1">
					<thead class="WSY_table_header">
						<th width="7%">ID</th>
						<th width="13%">订单号</th>
						<?php if($istype==1){?>
						<th width="20%">库存记录</th>
						<?php }else{?>
						<th width="20%">进账记录</th> 
						<?php }?>
						<?php if($istype!=1){?>
						<th width="8%">成本</th> 
						<th width="8%">利润</th> 						
						<th width="10%">每次结算余额</th> 
						<th width="10%">时间</th> 
						<th width="24%">消费说明</th> 
						<?php }else{?>
						<th width="18%">每次结算余额</th> 
						<th width="18%">时间</th> 
						<th width="24%">消费说明</th> 
						<?php } ?>
					</thead>
					<tbody>
					   <?php 
						$pagenum = 1;

						if(!empty($_GET["pagenum"])){
						$pagenum = $configutil->splash_new($_GET["pagenum"]);
						}
						$pagesize=20;
						if(!empty($_GET["pagesize"])){
						$pagesize = $configutil->splash_new($_GET["pagesize"]);
						}
						if(!empty($_POST["pagesize"])){
						$pagesize = $configutil->splash_new($_POST["pagesize"]);
						}			
						
						$start = ($pagenum-1) * $pagesize;
						$end = $pagesize;

						switch($istype){
								case 1:
								$query = "select id,batchcode,price,detail,type,createtime,after_inventory,after_getmoney,withdrawal_id from weixin_commonshop_agentfee_records where (type=1 or type=3) and isvalid=true and  user_id=".$user_id;
								break; 
								case 2:
								$query = "select id,batchcode,price,detail,type,createtime,after_inventory,after_getmoney,withdrawal_id from weixin_commonshop_agentfee_records where (type=2 or type=4) and isvalid=true and  user_id=".$user_id;
								break;
							}
						// $query = "select id,batchcode,price,detail,type,createtime,after_inventory,after_getmoney from weixin_commonshop_agentfee_records where isvalid=true and  user_id=".$user_id;
						if(!empty($search_batchcode)){
						   
							$query = $query." and batchcode like '%".$search_batchcode."%'";
						 }
						 if(!empty($begintime)){				   
							$query = $query."  and UNIX_TIMESTAMP(createtime)>=".strtotime($begintime);
						 }
						 if(!empty($endtime)){			   
							$query = $query."  and UNIX_TIMESTAMP(createtime)<=".strtotime($endtime);
						 }
						 
						   /* 输出数量开始 */
						 $query2 = $query.' group by id order by id';
						 $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
						 $rcount_q2 = mysql_num_rows($result2);
						 /* 输出数量结束 */
						 $query = $query." order by id desc limit ".$start.",".$end;
						 $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
						 $keyid = -1;
						 $batchcode ="";
						 $price =0;
						 
						 $detail ="";
						 $in_money = "";
						 $out_money = "";
						 $createtime = "";
						 $total_in_money = 0;
						 $total_out_money = 0;
						 $after_inventory = 0;
						 $after_getmoney = 0;
						 $withdrawal_id = -1;
						 while ($row = mysql_fetch_object($result)) {
							$keyid = $row->id;
							$batchcode =$row->batchcode;
							$price =$row->price;
							$price1 =0;
							$price2 =0;
							$price3 =0;
							$query1 = "select price from weixin_commonshop_agentfee_records where (type=1 or type=3) and isvalid=true and batchcode=".$batchcode." and  user_id=".$user_id;
							//echo $query1;
							$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
							while ($row1 = mysql_fetch_object($result1)) {
								$price1 =$row1->price;
								$price3 =$price3+$price1;
							
							} 
							
							$price2=$price+$price3;
							$price3=abs($price3);
							$detail =$row->detail;
							$type =$row->type;
							$createtime =$row->createtime;
							$after_inventory =$row->after_inventory;
							$after_getmoney =$row->after_getmoney;
							
							$withdrawal_id =$row->withdrawal_id; 
							
							$query2="select serial_number,remark,confirmtime from weixin_commonshop_withdrawals where isvalid=1 and user_type=1 and id=".$withdrawal_id;
						//	echo $query2;
							$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
							$confirmtime ="";
							$serial_number ="";
							$remark ="";
							while ($row2 = mysql_fetch_object($result2)) {
								$confirmtime = $row2->confirmtime;
								$serial_number=$row2->serial_number;
								$remark = $row2->remark;
							}
							switch($istype){
								case 1:
									switch($type){
										case 1:
											$total_out_money = $total_out_money + $price;	//出库
											$after_inventory = "库存余额:".$after_inventory.'元';	//每次结算的库存余额
											$out_money = $price."元";break;
										case 3:
											$after_inventory = "库存余额:".$after_inventory.'元';	//每次结算的库存余额
											$out_money =  $price."元";break;							
									}
								break;
								case 2:
									switch($type){
										case 2:	
											$total_in_money = $total_in_money + $price;		//收入
											$after_getmoney = "余额:".$after_getmoney.'元';	//每次结算的库存余额
											$in_money = $price."元";
											break;
										case 4:	
											$total_in_money = $total_in_money + $price;		//收入
											$after_getmoney = "余额:".$after_getmoney.'元';	//每次结算的库存余额
											$in_money = $price."元";
											break;
											
									 }
								break;
							}
							
					   ?>
						<tr>
						
						   <td><?php echo $keyid; ?></td>
						   <td><?php echo $batchcode; ?></td>
						   <?php if($istype==1){?>
								<td><?php echo $out_money; ?></td>
								<td><?php echo $after_inventory; ?></td> 
							<?php }else{?>
								<td><?php echo $in_money.'</br>';
								if(!empty($serial_number)){echo '(交易流水号:'.$serial_number.')</br>';}
								?>
								</td>
								<td><?php echo $price3; ?></td>
								<td><?php echo $price2; ?></td>
								<td><?php echo $after_getmoney; ?></td>
							<?php }?>
						   <td><?php echo $createtime; ?></td>
						   <td><?php echo $detail.'</br>';
								if(!empty($confirmtime)){echo '确认时间:'.$confirmtime.'</br>';}
								if(!empty($remark)){echo '提现备注:'.$remark.'</br>';}
						   ?>
						   
						   </td>

						   
						</tr>				
						
					   <?php } ?>
						
					</tbody>					
				</table>
				<!--翻页开始-->
				<div class="WSY_page">
        	
				</div>
				<!--翻页结束-->
			</div>
		</div>
	</div>

<script src="../../../js/fenye/jquery.page1.js"></script>

<script>
/* function search_form(){
	var search_batchcode = document.getElementById("search_batchcode").value;
	<?php if($istype!=1){?>
		var begintime = document.getElementById("begintime").value;	
		var endtime = document.getElementById("endtime").value;
	<?php }?>
	var url="agentcost_detail.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id; ?>&istype=<?php echo $istype;?>&search_batchcode="+search_batchcode;
	console.log(url);
	<?php if($istype!=1){?>
		if(begintime !=""){
			url=url+'&begintime='+begintime;
		}
		if(endtime !=""){
			url=url+'&endtime='+endtime;
		}
	<?php }?>
		document.location=url;
} */
 var istype = <?php echo $istype ?>;
 var pagenum = <?php echo $pagenum ?>;
 var rcount_q2 = <?php echo $rcount_q2 ?>;
 var end = <?php echo $end ?>;
 var user_id = <?php echo $user_id ?>;
 var customer_id = '<?php echo $customer_id_en ?>';
 var count =Math.ceil(rcount_q2/end);//总页数
 
  	//pageCount：总页数
	//current：当前页
/* 	 $(".tcdPageCode").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		 var search_batchcode = document.getElementById("search_batchcode").value;
		 document.location= "agentcost_detail.php?customer_id="+customer_id+"&pagenum="+p+"&user_id="+user_id+"&istype="+istype+"&search_batchcode="+search_batchcode;
	   }
    }); */ 
 $(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		 var search_batchcode = document.getElementById("search_batchcode").value;
		 document.location= "agentcost_detail.php?customer_id="+customer_id+"&pagenum="+p+"&user_id="+user_id+"&istype="+istype+"&search_batchcode="+search_batchcode;
	   }
    });

  var pagenum = <?php echo $pagenum ?>;
   var page = count;
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
	var search_batchcode = document.getElementById("search_batchcode").value;
		 document.location= "agentcost_detail.php?customer_id="+customer_id+"&pagenum="+a+"&user_id="+user_id+"&istype="+istype+"&search_batchcode="+search_batchcode;
	}
  }
</script>

<?php mysql_close($link);?>	
 
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>