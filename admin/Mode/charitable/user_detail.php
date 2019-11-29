<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../weixinpl/proxy_info.php');
$pagenum   = 1; //第几页
$pagesize  = 20;
$begintime = "";
$endtime   = "";
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * $pagesize;
$end   = $pagesize;
$user_id = 313242;
if(!empty($_GET["user_id"])){
   $user_id = $configutil->splash_new($_GET["user_id"]);
}
if(!empty($_GET["search_batchcode"])){
   $search_batchcode = $configutil->splash_new($_GET["search_batchcode"]);
}

if(!empty($_GET["begintime"])){
   $begintime = $configutil->splash_new($_GET["begintime"]);
}
if(!empty($_GET["endtime"])){
   $endtime = $configutil->splash_new($_GET["endtime"]);
}

$name        	   = "匿名";//用户名字
$weixin_name 	   = "匿名";//用户微信名字
$username    	   = "匿名";//用户微信名字
$charitable        = "0";//用户慈善分
$weixin_headimgurl = "";//用户头像
$phone             = "";//用户头像
$query = "select name,weixin_name,charitable,phone from weixin_users where isvalid=true and id=". $user_id ." and customer_id=".$customer_id." limit 0,1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());				   
while ($row = mysql_fetch_object($result)) {
	$name        = $row->name;
	$weixin_name = $row->weixin_name;
	$charitable  = $row->charitable;
	$phone       = $row->phone;
	$username    = $name ."(".$weixin_name.")";
}
/*输出数据语句*/
$query = "select batchcode,reward,createtime,paytype,charitable from charitable_log_t where user_id=". $user_id ." and isvalid=true and customer_id=".$customer_id;
/*输出数据语句*/

/*统计数据数量*/
$query_num ="select count(1) as wcount from charitable_log_t where isvalid=true and customer_id=".$customer_id." and  user_id=". $user_id;
/*统计数据数量*/
$sql = "";
 if(!empty($search_batchcode)){			   
	$sql .= " and batchcode like '%".$search_batchcode."%'";
}
if(!empty($begintime)){	
// print_r($begintime);	   
	$sql .= " and UNIX_TIMESTAMP(createtime)>".strtotime($begintime);
}
if(!empty($endtime)){
// print_r($endtime);			   
	$sql .= " and UNIX_TIMESTAMP(createtime)<".strtotime($endtime);
}
/*运行统计数据数量*/
$query_num .= $sql;
$result_num = _mysql_query($query_num) or die('Query_num failed: ' . mysql_error());
$wcount     = 0;//数据数量
$page       = 0;//分页数
while ($row_num = mysql_fetch_object($result_num)) {
	$wcount =  $row_num->wcount ;
}			
$page=ceil($wcount/$end);
/*运行统计数据数量*/
$query .=  $sql." GROUP BY batchcode ORDER BY id DESC limit ".$start.",".$end; 
?>
<!DOCTYPE html>
<!-- saved from url=(0047)//www.ptweixin.com/member/?m=shop&a=orders -->
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">	
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
</head>

<body>
<div id="WSY_content">
	<div class="WSY_columnbox" style="min-height: 300px;">
		<div  class="WSY_data">
			<div id="WSY_list" class="WSY_list">
				<div class="WSY_left" style="background: none; float:none;">
					姓名：<span style="font-weight:bold"><?php echo $username; ?></span>&nbsp;&nbsp;&nbsp; 手机号：<span style="font-weight:bold"><?php echo $phone; ?></span>&nbsp;&nbsp;&nbsp;
					到账慈善分：<span style="font-weight:bold;font-size:22px;color:red"><?php echo $charitable; ?></span>
				</div>
				<li style="margin: 0 40px 0 0;float:right;"><a href="javascript:history.go(-1);" class="WSY_button" style="margin-top: 0;width: 60px;height: 28px;vertical-align: middle;line-height: 28px;">返回</a></li>

				<div class="WSY_position_text" style="padding-left: 20px;">
					<a>
						订单号：
						<input type="text" name="search_batchcode" id="search_batchcode" value="<?php echo $search_batchcode; ?>">
					</a>
					<a>
						捐助时间：
						<span id="searchtype3" class="display">
							<input type="text" class="input Wdate" style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="begintime" name="AccTime_A" value="<?php echo $begintime; ?>" maxlength="21" id="K_1389249066532" />
							-
							<input type="text" class="input  Wdate"  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="endtime" name="AccTime_B" value="<?php echo $endtime; ?>" maxlength="20" id="K_1389249066580" />
						</span>
					</a>
					<input type="button" class="search_btn" onclick="searchForm();" value="搜 索"> 
				</div>
				<?php if(!empty($begintime)){
					
						$query = "select IFNULL(sum(charitable),0) as month_charitables from charitable_log_t where isvalid=true and paytype in (0,1) and  user_id=".$user_id." and UNIX_TIMESTAMP(createtime)>".strtotime($begintime)." and customer_id=".$customer_id;
						if(!empty($endtime)){			   
							$query .= " and UNIX_TIMESTAMP(createtime)<".strtotime($endtime);
						}else{
							$endtime = "现在";
						}
						$month_charitables = 0;
						$result=_mysql_query($query) or die('L122 '.mysql_error());
						while($row=mysql_fetch_object($result)){
							$month_charitables = $row -> month_charitables;
						}
				?>
				
				<div class="WSY_left" style="background: none; float:none;">
					<?php echo $begintime."~".$endtime ?>新增慈善分:<span style="font-weight:bold"><?php echo $month_charitables; ?></span>
				</div>
				<?php
				} ?>
			</div>
		<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			<thead class="WSY_table_header">
				<tr>
					<th width="10%" nowrap="nowrap">订单号</th>
					<!-- <th width="10%" nowrap="nowrap">金额</th> -->
					<th width="10%" nowrap="nowrap">捐赠金额</th>
					<th width="10%" nowrap="nowrap">慈善积分</th>
					<th width="10%" nowrap="nowrap">状态</th>
					<th width="10%" nowrap="nowrap">时间</th>
				</tr>
			</thead>
			<tbody>
			   <?php 
				/* $totalprice 		 = 0; */
				$batchcode  = -1;
				$createtime = ""; //确认完成时间
				$reward     = 0;	//订单慈善分		
				$paytype    = -1;//订单慈善分订单状态		
				$charitable = 0;//订单慈善分
				//echo $query;
				$result = _mysql_query($query) or die('Query failed2: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					/* $totalprice 		 = $row -> totalprice; */
					$batchcode  = $row -> batchcode;
					$createtime = $row -> createtime;
					$reward     = $row -> reward;
					$paytype    = $row -> paytype;
					$charitable = $row -> charitable;
					switch($paytype){
						case -1:  
							$paytype_str = "未支付";
							break;
						case 0:  
							$paytype_str = "已支付";
							break;
						case 1:  
							$paytype_str = "已到账";
							break;
						case 2:  
							$paytype_str = "已退货";
							break;
						case 3:  
							$paytype_str = "已退款";
							break;
					}
									
			   ?>
                <tr>
				   <td align="center"><?php echo $batchcode; ?></td>
				   <td align="center"><?php echo $reward; ?></td>
				   <td align="center"><?php echo $charitable; ?></td>
				   <td align="center"><?php echo $paytype_str; ?></td>
				   <td align="center"><?php echo $createtime; ?></td>
                </tr>				
			   <?php } ?>
			</tbody>
		</table>
		<div class="blank20"></div>
		<div id="turn_page"></div>
		</div>	
			<!--翻页开始-->
        <div class="WSY_page">
        	
        </div>
        <!--翻页结束-->
	</div>
</div>
	


<?php 

mysql_close($link);
?>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script>
var user_id     = "<?php echo $user_id;?>";
var customer_id = "<?php echo $customer_id_en;?>";
var pagenum     = <?php echo $pagenum ?>;
var count       = <?php echo $page ?>;//总页数	

</script>
<script type="text/javascript" src="../../Common/js/Mode/charitable/user_detail.js"></script>
</body></html>