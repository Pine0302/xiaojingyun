<?php
header("Content-type: text/html; charset=utf-8"); //test  sda

require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");
$head=6;//头部文件  0基本设置,1提现记录,2代理商管理
$user_id=-1;

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

$query2="select supply_money from weixin_commonshop_applysupplys where status=1 and isvalid=true and user_id=".$user_id;	
$supply_money = 0;
$result2 = _mysql_query($query2) or die('Query failed: 1' . mysql_error());
while ($row2 = mysql_fetch_object($result2)) {
	$supply_money = $row2->supply_money;
	$supply_money = round($supply_money,2);
}

 $search_batchcode="";
if(!empty($_POST["search_batchcode"])){
   $search_batchcode = $configutil->splash_new($_POST["search_batchcode"]);
}
 $begintime="";//开始时间
if(!empty($_POST["begintime"])){
   $begintime = $configutil->splash_new($_POST["begintime"]);
}
$endtime="";//结束时间
if(!empty($_POST["endtime"])){
   $endtime = $configutil->splash_new($_POST["endtime"]);
}
$finance_type="";//类型
if(!empty($_POST["finance_type"])){
   $finance_type = $configutil->splash_new($_POST["finance_type"]);
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
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scgys' and c.id=cf.column_id";
$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_supplierstr=1;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>供应商-账目明细</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/supplier/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/utility.js" charset="utf-8"></script>
<script type="text/javascript" src="../../../common/js/jquery.blockUI.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<style>
/*<!-- 导出字段 -->*/
.floatbox{position: fixed;top: 270px;left: 40%;padding: 15px;background-color: #dddddd;display: none;}
.floatbox .tishitext{margin-bottom: 4px;}
.floatbox .checkboxsdiv{border: 1px solid #888888;padding: 8px;width: 200px;background-color: #ffffff;}
.checkboxsdiv input,.quanbuxuan input{display: inline-block;}
.checkboxsdiv p,.quanbuxuan p{display: inline-block;white-space: nowrap;overflow: hidden;max-width: 181px;margin-left: 5px;}
.floatbox .floatinputs{width: 60px;height: 27px;border-radius: 6px;background-color: #2eade8;cursor: pointer;color: #ffffff;display: inline-block;margin-top: 15px;margin-left: 16px;margin-right: 10px;}
.floatbox .floatinputc{width: 60px;height: 27px;color: #ffffff;background-color: #aaaaaa;cursor: pointer;border-radius: 6px;display: inline-block;margin-top: 15px;}
.quanbuxuan{display: inline-block;padding: 5px 0 0 10px;vertical-align: middle;margin-top: -5px;}
.subdivb{display: inline-block;vertical-align: middle;}
/*<!-- 导出字段 End -->*/
</style>
</head>

<body>

<title>账目明细</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>  
	<!--内容框架-->
	<div class="WSY_content"> 
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			include("../../../../weixinpl/back_newshops/Mode/supplier/basic_head.php"); 
			?>
			<!--列表头部切换结束-->
			<div class="WSY_remind_main"> 
				<div class="search">
					姓名：<span style="font-weight:bold;font-size:18px;"><?php echo $username; ?></span>&nbsp;&nbsp;&nbsp; 手机号：<span style="font-weight:bold;font-size:18px;"><?php echo $phone; ?></span>&nbsp;&nbsp;&nbsp;
					<?php if($istype==3){?>
					账目余额：<span style="font-weight:bold;font-size:22px;color:red"><?php echo $supply_money; ?>元</span>
					<?php }?>
					<li style="margin: 0 40px 0 0;float:right;"><a href="javascript:history.go(-1);" class="WSY_button" style="margin-top: 0;width: 60px;height: 28px;vertical-align: middle;line-height: 28px;">返回</a></li>
					
				</div> 
				
				<form class="search" id="search_form" method="post" action="supplycost_detail.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id; ?>&istype=<?php echo $istype;?>">
					<div  class="search" id="search_form">
						订单号：<input type=text name="search_batchcode" id="search_batchcode" value="<?php echo $search_batchcode; ?>" style="width:220px;" />
                        <select data-am-selected="{btnWidth: 100,btnSize: 'xs'}" id="finance_type" name="finance_type" data-selected="finance_type">		  
                          <option  value="0" >类型</option>
                          <option  value="5" <?php if($finance_type==5){echo "selected = 'selected'";} ?> >入账</option>
                          <option  value="6" <?php if($finance_type==6){echo "selected = 'selected'";} ?>>出账</option>
                        </select>
					    <span class="WSY_position1" style="display:inline-block;vertical-align:bottom;">
			                <ul>		
				                <li class="WSY_position_date tate001" > 
					                <p>订单生成时间：<input class="date_picker" type="text" name="begintime" id="begintime" value="<?php echo $begintime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'#F{$dp.$D(\'endtime\')}'});"></p>
					                <p style="margin-left:0px;">&nbsp;&nbsp;-&nbsp;&nbsp;<input class="date_picker" type="text" name="endtime" id="endtime" value="<?php echo $endtime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'begintime\')}'});"></p>
				                </li>				
			                </ul>
		                </span>
						<input type="submit" class="search_btn"  value="搜 索">
						<input type="button" class="search_btn" id="my_excel" value="订单导出" >
					</div>
				</form>
				<table width="97%" class="WSY_table" id="WSY_t1">
					<thead class="WSY_table_header">
						<th width="10%">ID</th>
						<th width="20%">订单号</th>
						<th width="10%">账目记录</th>					
						<th width="15%">每次结算余额</th> 
						<th width="15%">订单生成时间</th> 
						<th width="15%">订单结算时间</th> 
						<th width="13%">消费说明</th> 
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
								case 3:
								$query = "select id,batchcode,price,detail,type,createtime,after_inventory,after_getmoney,withdrawal_id from weixin_commonshop_agentfee_records where  isvalid=true and  user_id=".$user_id;
								break;
						}
						// $query = "select id,batchcode,price,detail,type,createtime,after_inventory,after_getmoney from weixin_commonshop_agentfee_records where isvalid=true and  user_id=".$user_id;
						if(!empty($search_batchcode)){						   
							$query = $query." and batchcode like '%".$search_batchcode."%'";
							//echo $query;
						 }
						 //查询出入账
						 if ($finance_type!=0) {
						 	$query = $query." and type=".$finance_type;
						 }else{
						 	$query = $query." and type in(5,6)";
						 }

						 //查询出入账End
						 //查询订单生成时间
						     if(!empty($begintime)){
						     $begintime = strtotime($begintime);						   
						    	$query = $query." and UNIX_TIMESTAMP(createtime) > ".$begintime;
						     }
						     if(!empty($endtime)){	
						     $endtime = strtotime($endtime);					   
						    	$query = $query." and UNIX_TIMESTAMP(createtime) < ".$endtime;
						     }
						     //echo $query;exit();
						//查询订单生成时间End			
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
							$detail =$row->detail;
							$type =$row->type;
							$createtime =$row->createtime;
							$after_inventory =$row->after_inventory;
							$after_getmoney =$row->after_getmoney;
							$withdrawal_id =$row->withdrawal_id;
							
							$query2="select serial_number,remark,confirmtime from weixin_commonshop_withdrawals where isvalid=1 and user_type=1 and id=".$withdrawal_id;
							//查询订单结束时间
						         if(!empty($begintime)){	
						         // $starttime = strtotime($begintime);					   
						    	    $query2 = $query2." and UNIX_TIMESTAMP(confirmtime) > ".$begintime;
						         }
						         if(!empty($endtime)){
						         // $overtime = strtotime($endtime);							   
						        	$query2 = $query2." and UNIX_TIMESTAMP(confirmtime) < ".$endtime;
						         }
						        // echo $query2;exit();
						    //订单结束时间End
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
								case 3:
									switch($type){
										case 5:
											$price = $price.'元';	//每次进账的金额
											$after_getmoney = $after_getmoney.'元';	//每次结算的库存余额
										break;
										case 6:
											$price = $price.'元';	//每次提现驳回的金额
											$after_getmoney = $after_getmoney.'元';	//每次结算的库存余额
										break;
									}
								break;
							}
								
						?>
							<tr>
							   <td><?php echo $keyid; ?></td>
							   <td><?php echo $batchcode; ?></td>
							   <td><?php echo round($price,2); ?></td>
							   <td><?php echo $after_getmoney; ?></td>
							   <td><?php echo $createtime; ?></td>
							   <td><?php echo $confirmtime; ?></td>
							   <td><?php echo $detail.'</br>';
									// if(!empty($confirmtime)){echo '确认时间:'.$confirmtime.'</br>';}
									if(!empty($remark)){echo '提现备注:'.$remark.'</br>';}
							   ?>							   
							   </td>						   
							</tr>					
						
					   <?php } ?>
						
					</tbody>					
				</table>
			<!-- 导出字段选择 -->
			<div class="floatbox">
				<p class="tishitext">导出字段选择</p>
				<div class="checkboxsdiv">
					<div><input type="checkbox" name="excel_field" checked  value="r.id"><p>id</p></div>
					<div><input type="checkbox" name="excel_field" checked  value="r.batchcode"><p>订单号</p></div>
					<div><input type="checkbox" name="excel_field" checked  value="r.price"><p>账目记录</p></div>
					<div><input type="checkbox" name="excel_field" checked  value="r.after_getmoney"><p>每次结算余额</p></div>
					<div><input type="checkbox" name="excel_field" checked  value="r.createtime"><p>订单生成时间</p></div>
					<div><input type="checkbox" name="excel_field" checked value="w.confirmtime"><p>订单结算时间</p></div>
					<div><input type="checkbox" name="excel_field" checked  value="r.detail"><p>消费说明</p></div>
				</div>
				<div class="quanbuxuan">
					<input type="checkbox" id="allselects" checked="checked" value="全选"><p>全选</p>
				</div>
				<input type="submit" class="floatinputs" value="确定">
				<input type="submit" class="floatinputc" value="取消">
			</div>	
			<!-- 导出字段选择 End -->	
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

	var url="supplycost_detail.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id; ?>&istype=<?php echo $istype;?>&search_batchcode="+search_batchcode;
	console.log(url);
	document.location=url;
} */

 var istype = <?php echo $istype ?>;
 var pagenum = <?php echo $pagenum ?>;
 var rcount_q2 = <?php echo $rcount_q2 ?>;
 var end = <?php echo $end ?>;
 var user_id = <?php echo $user_id ?>;
 var customer_id = '<?php echo $customer_id ?>';
 var count =Math.ceil(rcount_q2/end);//总页数
 
  	//pageCount：总页数
	//current：当前页

	 $(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		 var search_batchcode = document.getElementById("search_batchcode").value;
		 document.location= "supplycost_detail.php?customer_id="+customer_id+"&pagenum="+p+"&user_id="+user_id+"&istype="+istype+"&search_batchcode="+search_batchcode;
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
		 document.location= "supplycost_detail.php?customer_id="+customer_id+"&pagenum="+a+"&user_id="+user_id+"&istype="+istype+"&search_batchcode="+search_batchcode;
	}
  }
  		// 全选
$("#allselects").click(function(){    
	if(this.checked){    
	    $(".checkboxsdiv :checkbox").attr("checked", true);   
    }else{    
	    $(".checkboxsdiv :checkbox").attr("checked", false); 
    }    
}); 
$("#my_excel").click(function(){
	$(".floatbox").toggle();
});
$(".floatinputc").click(function(){
	$(".floatbox").hide();
});	
//订单导出
$(".floatinputs").click(function(){
  var fields =[]; 
  var text = [];
  $('input[name="excel_field"]:checked').each(function(){ 
    fields.push($(this).val()); 
    text.push($(this).next().text()); 
  }); 
  var begintime = $("#begintime").val();
  var endtime = $("#endtime").val();
   var finance_type= $("#finance_type").val();
  //var checkValue=$("#finance_type").val();
  var batchcode= $("#search_batchcode").val();
  var proofTechnique = '1';
  if(begintime==""){
    begintime = 0;
  }
  if(endtime==""){
    endtime = 0;
  }
  var url='/weixin/plat/app/index.php/ExcelSupply/bill_excel/supply_id/'+user_id+'/begintime/'+begintime+'/endtime/'+endtime+'/finance_type/'+finance_type+'/fields/'+fields+'/text/'+text+'/proofTechnique/'+proofTechnique+'/batchcode/'+batchcode;
  //console.log(url);
  document.location=url;
  $(".floatbox").hide();	
});	
//订单导出 End
</script>
<?php mysql_close($link);?>	
 
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body></html>