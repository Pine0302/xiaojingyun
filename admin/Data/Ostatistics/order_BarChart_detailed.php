<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
   $customer_id = $configutil->splash_new($_GET["customer_id"]);
   $customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");


$pagenum = 1;
$pagesize = 20;
$begintime="";
$endtime ="";
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * $pagesize;
$end = $pagesize;


$begintime="";
$endtime ="";
$province ="";
$city ="";
$area ="";
$search_status=-1;
$PID="";
if(!empty($_GET["search_status"])){
   $search_status=$configutil->splash_new($_GET["search_status"]);
}
$sql="SELECT  o.totalprice,o.batchcode,o.paystyle,o.paystatus,o.sendstatus,o.createtime from weixin_commonshop_order_addresses a inner join
weixin_commonshop_orders o on a.batchcode = o.batchcode where o.isvalid = true and o.customer_id =".$customer_id;

$sql_sum="SELECT  sum(o.totalprice) as totalprices from weixin_commonshop_order_addresses a inner join
weixin_commonshop_orders o on a.batchcode = o.batchcode where o.isvalid = true and o.customer_id =".$customer_id;

$query_count="select count(distinct o.batchcode) as tcount from weixin_commonshop_order_addresses a inner join
weixin_commonshop_orders o on a.batchcode = o.batchcode where o.isvalid = true and o.customer_id =".$customer_id;

$query="";
if(!empty($_GET["begintime"])){
   $begintime = $configutil->splash_new($_GET["begintime"]);
   if (strlen($begintime) > 11) {
   	   $begintime = substr($configutil->splash_new($_GET["begintime"]), 0, -3);
   }
   if(!empty($_GET["paytime"])){
	  $query = $query." and UNIX_TIMESTAMP(o.paytime)>=".strtotime($begintime);
   }else{
	  $query = $query." and UNIX_TIMESTAMP(o.createtime)>=".strtotime($begintime);
   }

}

if(!empty($_GET["endtime"])){
   $endtime = $configutil->splash_new($_GET["endtime"]);
   if (strlen($endtime) > 11) {
   	   $endtime = substr($configutil->splash_new($_GET["endtime"]), 0, -3);
   }
   if(!empty($_GET["paytime"])){
	   $query = $query." and UNIX_TIMESTAMP(o.paytime)<".strtotime($endtime);
   }else{
	     $query = $query." and UNIX_TIMESTAMP(o.createtime)<".strtotime($endtime);
   }
}

if(!empty($_GET["PID"])){
	$PID= $configutil->splash_new($_GET["PID"]);
		$query = $query." and o.pid=".$PID;
}
switch($search_status){
	case 1:
	//已确认
		$query = $query." and o.status=1";
		break;
	case 2:
	//未确认
		$query = $query." and o.status=0";
		break;
	case 3:
	//未确认
		$query = $query." and o.paystatus=1 and o.status!=-1 ";
		break;
	case 4:
	//未确认
		$query = $query." and o.paystatus=0";
		break;
	case 5:
	//已发货
		$query = $query." and (o.sendstatus=1 or o.sendstatus=2)";
		break;
	case 6:
	//未确认
		$query = $query." and o.sendstatus=0";
		break;
	case 7:
	//已退货
		$query = $query." and o.sendstatus=3";
		break;
	case 8:
	//已取消
		$query = $query." and o.status=-1";
		break;
}
if(!empty($_GET["province"])){
   $province = $configutil->splash_new($_GET["province"]);
   $query = $query." and a.location_p='".$province."'";
   if(!empty($_GET["city"])){
	   $city = $configutil->splash_new($_GET["city"]);
	   $query = $query." and a.location_c='".$city."'";
	   if(!empty($_GET["area"])){
		   $area = $configutil->splash_new($_GET["area"]);
		   $query = $query." and a.location_a='".$area."'";
	   }
   }
}
$sql = $sql.$query;
$sql = $sql." order by o.id desc"." limit ".$start.",".$end;
$sql_sum = $sql_sum.$query;
$result = _mysql_query($sql_sum) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$totalprices=$row->totalprices;
	$totalprices =round($totalprices,2);

}
$query_count = $query_count.$query;
$result = _mysql_query($query_count) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$tcount=$row->tcount;

}
$page=ceil($tcount/$end);

?>
<!DOCTYPE html>

<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>

</head>

<body>

<style type="text/css">
table th{color: #FFF;line-height: 30px;text-align: center;font-size: 12px; }
table td{height: 40px;line-height: 20px;font-size: 12px;color: #323232;padding: 0px 1em;text-align: center;border: 1px solid #D8D8D8; }
.display{display:none}

</style>


<script language="javascript">

$(document).ready(shop_obj.orders_init);
</script>
<body id="bod" style="min-height: 580px;">
	<!--内容框架-->
	<div class="WSY_content" style="height: 100%;">

		<!--列表内容大框-->
		<div class="WSY_columnbox" style="height: 98%;">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="typf white1" onclick="ClickType(this)">区域订单总计列表</a>
				</div>
			</div>
			<!--列表头部切换结束-->

  <div  class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list">
        	<div class="WSY_left" style="background-image:url('');width:95%;margin-top: 1px;padding:0px">
			<div >
			<span id="searchtype">
			<a style="margin-top: 0px;display:  inline-block;">
			<input id="PID" style="display:none" value="<?php echo $PID; ?>"/>
			时间
			<span class="WSY_generalize_dl08" >
			<span id="searchtype3">
			<input type="text" class="input Wdate" style="border: 1px solid #CFCBCB;height: 23px;margin-bottom: 5px;border-radius: 3px;" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="begintime" name="AccTime_A" value="<?php echo $begintime; ?>" maxlength="21" id="K_1389249066532" />
			-
			</span>
			<input type="text" class="input  Wdate"  style="border: 1px solid #CFCBCB;height: 23px;margin-bottom: 5px;border-radius: 3px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="endtime" name="AccTime_B" value="<?php echo $endtime; ?>" maxlength="20" id="K_1389249066580" />
			</span>
			</a>
			<a style="width:1%;margin-top: 0px;display:  inline-block;"></a>
			<a  style="margin-top: 0px;display:  inline-block;width:10%">
			省:&nbsp;<select name="province" id="province"style="width:70%;border: 1px solid #CFCBCB;height: 26px;border-radius: 3px;" ></select>
			</a>
			<a style="width:1%;margin-top: 0px;display:  inline-block;"></a>
			<a style="margin-top: 0px;display:  inline-block;width:10%">
			市:&nbsp;<select name="city" id="city" style="width:70%;border: 1px solid #CFCBCB;height: 26px;border-radius: 3px;"></select>
			</a>
			<a style="width:1%;margin-top: 0px;display:  inline-block;"></a>
			<a style="margin-top: 0px;display:  inline-block;width:10%">
			区:&nbsp;<select name="area" id="area" style="width:70%;border: 1px solid #CFCBCB;height: 26px;border-radius: 3px;"></select>
			</a>
			<script src="../../Common/js/Data/js/region_select.js"></script>
			<script type="text/javascript">
			new PCAS('province', 'city', 'area', '<?php echo $province;?>', '<?php echo $city;?>', '<?php echo $area;?>');
			</script>
			<a style="width:1%;margin-top: 0px;display:  inline-block;"></a>
			<a style="margin-top: 0px;display:  inline-block;width:16%">
			订单状态：
			<select name="search_status" id="search_status" style="width:60%;border: 1px solid #CFCBCB;height: 26px;border-radius: 3px;">
			<option value="-1">--请选择--</option>
			<option value="1" <?php if($search_status==1){ ?>selected <?php } ?>>已确认</option>
			<option value="2" <?php if($search_status==2){ ?>selected <?php } ?>>待确认</option>
			<option value="3" <?php if($search_status==3){ ?>selected <?php } ?>>已支付</option>
			<option value="4" <?php if($search_status==4){ ?>selected <?php } ?>>未支付</option>
			<option value="5" <?php if($search_status==5){ ?>selected <?php } ?>>已发货</option>
			<option value="6" <?php if($search_status==6){ ?>selected <?php } ?>>未发货</option>
			<option value="7" <?php if($search_status==7){ ?>selected <?php } ?>>申请退货</option>
			<option value="8" <?php if($search_status==8){ ?>selected <?php } ?>>已取消</option>
			</select>
			</a>
			<a style="width:1%;margin-top: 0px;display:  inline-block;"></a>
			</span>

			<span id="searchtype4">
			<a   style="width:10%;display:  inline-block;padding:0px" class="WSY_bottonliss">
            <input  type="button" class="search_btn" onclick="search_bar();" value="搜 索" style="width:80%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >
            </a>
			</span>
			</div>

          </div>
		<div class="WSY_data" id="type1" style="margin-left: 1.5%;margin-top: 10px;">

		<table class="WSY_t2"  width="97%"  style="border: 1px solid #D8D8D8;border-collapse: collapse;">
			<thead class="WSY_table_header">

				<tr style="border:1px solid #06A7E1">
					<th width="8%" nowrap="nowrap">订单号</th>
					<th width="8%" nowrap="nowrap">金额</th>
					<th width="8%" nowrap="nowrap">支付方式</th>
					<th width="12%" nowrap="nowrap">支付状态</th>
					<th width="8%" nowrap="nowrap">订单状态</th>
					<th width="8%" nowrap="nowrap">下单时间</th>
					<!-- <td width="8%" nowrap="nowrap">类型</td>
					<td width="8%" nowrap="nowrap">确认时间</td>
					<td width="8%" nowrap="nowrap">红包金额</td>
					<td width="12%" nowrap="nowrap">备注</td> -->
				</tr>
			</thead>
			<tbody>
			<?PHP


				//echo $sql;
				$totalprice_ye=0;
				$result1 = _mysql_query($sql) or die('Query failed1: ' . mysql_error());
					while ($row = mysql_fetch_object($result1)) {
						$totalprice=$row->totalprice;
						$totalprice_ye=$totalprice_ye+$totalprice;
						$batchcode=$row->batchcode;
						$sendstatus=$row->sendstatus;
						$paystyle=$row->paystyle;
						$paystatus=$row->paystatus;
						$createtime=$row->createtime;
						$paystatus_str="未支付";
						if($paystatus==1){
							$paystatus_str="已支付";
						}

						$sendstatusstr="未发货";
					switch($sendstatus){
					   case 1:
					       $sendstatusstr="已发货";
					       break;
					   case 2:
					       $sendstatusstr="顾客已收货";
						   break;
					   case 3:
					       $sendstatusstr="顾客已退货";
						   break;
						case 4:
					       $sendstatusstr="退货已确认";
						   break;
						case 5:
					       $sendstatusstr="顾客申请退款";
						   break;
						case 6:
					       $sendstatusstr="退款完成";
						   break;
					}

			?>
				<tr>
					<td><?php echo $batchcode;?></td>
					<td><?php echo $totalprice;?></td>
					<td><?php echo $paystyle;?></td>
					<td><?php echo $paystatus_str;?></td>
					<td><?php echo $sendstatusstr;?></td>
					<td><?php echo $createtime;?></td>
					<!-- <td><?php echo $type_name;?></td>
					<td><?php echo $createtime;?></td>
					<td><?php echo $red_money;?></td>
					<td><?php echo $remark;?></td> -->
				</tr>
			<?PHP }
				$totalprice_ye =round($totalprice_ye,2);
			?>
				 <tr>
				 <td>
					总共金额:<span style="color:red;font-size:22px;"><?php echo $totalprices;?></span>元
					</td>
					<td colspan="4">
					</td>
					<td>
					当前页总金额:<span style="color:red;font-size:22px;"><?php echo $totalprice_ye;?></span>元
					</td>
				</tr>
			</tbody>

		</table>
		<script>
		var PageURL="";
		$(function(){
				var begintime = document.getElementById("begintime").value;
				var endtime = document.getElementById("endtime").value;
				var PID = document.getElementById("PID").value;
				var province = document.getElementById("province").value;
				var city = document.getElementById("city").value;
				var area = document.getElementById("area").value;
				var search_status = document.getElementById("search_status").value;
				 PageURL="customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&search_status="+search_status;

				if(province !=""){
					PageURL=PageURL+'&province='+province;
				}
				if(city !=""){
					PageURL=PageURL+'&city='+city;
				}
				if(area !=""){
					PageURL=PageURL+'&area='+area;
				}
				if(begintime !=""){
					PageURL=PageURL+'&begintime='+begintime;
				}
				if(endtime !=""){
					PageURL=PageURL+'&endtime='+endtime;
				}
				if(PID !=""){
					PageURL=PageURL+'&PID='+PID;
				}



		});
		</script>
		<!--翻页开始-->
		<div class="WSY_page"></div>
		<script src="../../../js/fenye/jquery.page1.js"></script>
		<script type="text/javascript">
		  var pagenum = <?php echo $pagenum ?>;
		  var count =<?php echo $page ?>;//总页数
			//pageCount：总页数
			//current：当前页

			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
				 document.location= "order_BarChart_detailed.php?pagenum="+p+"&"+PageURL;
			   }
			});

		  var page = <?php echo $page ?>;

		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());
			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
			document.location= "order_BarChart_detailed.php?pagenum="+a+"&"+PageURL;
			}
		  }
		</script>
		<!--翻页结束-->
		</div>
	</div>
	</div>

</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">
<script src="../../../js/fenye/jquery.page.js"></script>
<script>

function search_bar(){
	var begintime = document.getElementById("begintime").value;
	var endtime = document.getElementById("endtime").value;
	var province = document.getElementById("province").value;
	var city = document.getElementById("city").value;
	var area = document.getElementById("area").value;
	var search_status = document.getElementById("search_status").value;
	var url="order_BarChart_detailed.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&search_status="+search_status;

 	if(province !=""){
		url=url+'&province='+province;
	}
	if(city !=""){
		url=url+'&city='+city;
	}
	if(area !=""){
		url=url+'&area='+area;
	}
	if(begintime !=""){
		url=url+'&begintime='+begintime;
	}
	if(endtime !=""){
		url=url+'&endtime='+endtime;
	}
	document.location=url;
}
</script>

<?php

mysql_close($link);
?>
</body></html>