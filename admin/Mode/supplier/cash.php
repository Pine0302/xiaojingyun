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

if(!empty($_REQUEST["search_batchcode"])){
   $search_batchcode = $configutil->splash_new($_REQUEST["search_batchcode"]);
}

if(isset($_REQUEST["search_account_type"])){
   $search_account_type = $configutil->splash_new($_REQUEST["search_account_type"]);
}

if(isset($_REQUEST["search_status"])){
   $search_status = $configutil->splash_new($_REQUEST["search_status"]);
}

if(isset($_REQUEST["foreign_id1"])){
   $time_type = $configutil->splash_new($_REQUEST["foreign_id1"]);
}
if(isset($_REQUEST["begintime"])){
   $begin_time = $configutil->splash_new($_REQUEST["begintime"]);
}
if(isset($_REQUEST["endtime"])){
   $end_time = $configutil->splash_new($_REQUEST["endtime"]);
}

$pay_type = "IPSpay";
$query = "SELECT gathering From pay_config WHERE customer_id='".$customer_id."' AND pay_type='".$pay_type."' AND isvalid=true limit 1";
$result = _mysql_query($query) or die(mysql_error().$sql);
if($result){
    $arr = mysql_fetch_row($result);
    $gathering = $arr[0];
}
$gathering = unserialize($gathering);
foreach ($gathering as $key => $value) {
	$gathering_name[] 		= $value['pro_name'];
	$gathering_begintime[] 	= $value['begintime'];
	$gathering_endtime[] 	= $value['endtime'];
}
$gathering_name 		= implode(',', $gathering_name);
$gathering_begintime 	= implode(',', $gathering_begintime);
$gathering_endtime 		= implode(',', $gathering_endtime);
// var_dump($gathering);

?>
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>供应商-提现记录</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/supplier/set.css">
<script type="text/javascript" src="layer/jquery.min.js"></script>
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/utility.js" charset="utf-8"></script>
<script type="text/javascript" src="../../../common/js/jquery.blockUI.js?time=<?php echo time() ?>"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="layer/layer.js"></script>
<style>
tr {
    line-height: 22px;
}

.date_picker{
    width: 130px;
    height: 24px !important;
    background: #fefefe;
    border: 1px solid #ccc;
    color: #333;
    border-radius: 2px;
    padding-left: 5px;
}

/*<!-- 导出字段 -->*/
.floatbox{position: fixed;top: 270px;left: 40%;padding: 15px;background-color: #dddddd;display: none;}
.floatbox .tishitext{margin-bottom: 4px;}
.floatbox .checkboxsdiv{border: 1px solid #888888;padding: 8px;width: 200px;background-color: #ffffff;}
.checkboxsdiv input,.quanbuxuan input{display: inline-block;}
.checkboxsdiv p,.quanbuxuan p{display: inline-block;white-space: nowrap;overflow: hidden;max-width: 181px;margin-left: 5px;}
.floatbox .floatinputs{width: 60px;height: 27px;border-radius: 6px;background-color: #2eade8;cursor: pointer;color: #ffffff;display: inline-block;margin-top: 15px;margin-left: 16px;margin-right: 10px;}
.floatbox .floatinputc{width: 60px;height: 27px;color: #ffffff;background-color: #aaaaaa;cursor: pointer;border-radius: 6px;display: inline-block;margin-top: 15px;}
.quanbuxuan{display: inline-block;padding: 5px 0 0 10px;vertical-align: middle;margin-top: 15px;}
.subdivb{display: inline-block;vertical-align: middle;}
/*<!-- 导出字段 End -->*/
.prompt-ul li span{min-width:90px;display:inline-block;}
.tc{text-align: center !important;}
table#WSY_t1 td{
	word-wrap: break-word;
    word-break: break-all; 
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
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/supplier/basic_head.php");
			?>
			<!--列表头部切换结束-->
			<div class="WSY_remind_main">
				<form class="search" id="search_form" method="post" action="cash.php?customer_id=<?php echo $customer_id_en; ?>">
					<div class="WSY_list" style="margin-top: 18px;">
						时间：
						<select id="foreign_id1" name="foreign_id1" onchange="selectTime()">
                          <option value="choiceTime" <?php if($time_type=="choiceTime"){?>selected<?php }?>>请选择时间</option>

                          <option value="indentCreate" <?php if($time_type=="indentCreate"){?>selected<?php }?>>订单申请时间</option>
                          <option value="indentEnd" <?php if($time_type=="indentEnd"){?>selected<?php }?>>订单确认时间</option>
                        </select>
                        <span class="WSY_position1" <?php if($time_type!="choiceTime"&& $time_type!=""){?>style="display:inline-block;vertical-align:bottom;"<?php }else{?>style="display:none;vertical-align:bottom;"<?php }?>>
						<input class="date_picker" type="text" name="begintime" id="begintime" value="<?php echo $begin_time ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'#F{$dp.$D(\'endtime\')}'});">&nbsp;&nbsp;-&nbsp;&nbsp;
						<input class="date_picker" type="text" name="endtime" id="endtime" value="<?php echo $end_time ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'begintime\')}'});">
						</span>
						订单号：
						<input style="margin-top:5px" type=text name="search_batchcode" id="search_batchcode" value="<?php echo $search_batchcode; ?>"/>
						<!-- 0:微信；1:支付宝;2：财付通 3:银行 -->
						账户类型:
						<select name="search_account_type" id='search_account_type'>
							<option value="-1">所有</option>
							<option <?php if($search_account_type=='0') { ?>selected <?php } ?> value="0">微信零钱</option>
							<option <?php if($search_account_type==1) { ?>selected <?php } ?> value="1">支付宝</option>
							<option <?php if($search_account_type==2) { ?>selected <?php } ?> value="2">财付通</option>
							<option <?php if($search_account_type==3) { ?>selected <?php } ?> value="3">银行</option>
							<option <?php if($search_account_type==4) { ?>selected <?php } ?> value="4">环迅账户</option>
						</select>
						<!-- 1申请   2成功    3驳回 -->
						申请状态:
						<select name="search_status" id='search_status' style='width: auto'>
							<option value="0">所有</option>
							<option <?php if($search_status==1) { ?>selected <?php } ?> value="1">申请</option>
							<option <?php if($search_status==2) { ?>selected <?php } ?> value="2">成功</option>
							<option <?php if($search_status==3) { ?>selected <?php } ?> value="3">驳回</option>
						</select>
						<input style="margin-top:5px" type="submit" class="search_btn" value="搜 索">
						<input style="margin-top:5px" type="button" class="search_btn" id="my_excel" value="导出记录">
					</div>
				</form>
				<table width="97%" class="WSY_table" id="WSY_t1">
					<thead class="WSY_table_header">
						<th width="5%">ID</th>
						<th width="11%">订单号</th>
						<th width="14%">交易流水号</th>
						<th width="8%">账户类型</th>
						<th width="17%">账户</th>
						<th width="10%">合作商名称</th>
						<th width="8%">申请金额</th>
						<th width="8%">申请时间</th>
						<th width="5%">申请状态</th>
						<th width="8%">确认时间</th>
						<th width="8%">备注</th>
						<th width="8%">操作</th>
					</thead>
					<tbody>
						<?php
						$query="SELECT distinct wd.id,wd.user_id,wd.serial_number,wd.money,wd.remark,wd.status,wd.createtime,wd.confirmtime,wd.person_information,agc.batchcode from weixin_commonshop_withdrawals wd INNER JOIN weixin_card_members wcm ON wd.user_id=wcm.user_id inner join weixin_commonshop_agentfee_records agc where agc.withdrawal_id = wd.id and wd.isvalid=1 and wd.type=1 and wd.user_type=2 and wcm.isvalid=1 and wd.customer_id= ".$customer_id;
						// var_dump($search_batchcode,$search_status,$search_account_type);
						if($search_batchcode!=''){
							$query = $query." and agc.batchcode like '%".$search_batchcode."%'";
						}
						if($search_status!=''&&$search_status!=0){
							$query = $query." and wd.status = {$search_status} ";
						}
						if($search_account_type!=''&&$search_account_type!=-1){
							$query = $query." and wcm.account_type = {$search_account_type} ";
						}

						//根据时间筛选
						 if($time_type=="indentCreate"){//根据订单生成时间筛选
						 	if($begin_time!=""){
						 		$query .= " and wd.createtime>='".$begin_time."'";
						 	}
						 	if($end_time!=""){
						 		$query .= " and wd.createtime<='".$end_time."'";
						 	}
						 }else if($time_type="indentEnd"){//根据订单确认时间筛选
					 		if($begin_time!=""){
					 			$query .= " and wd.confirmtime>='".$begin_time."'";
						 	}
						 	if($end_time!=""){
						 		$query .= " and wd.confirmtime<='".$end_time."'";
						 	}
						 }
						  /* 输出数量开始 */
						 $query2 = $query.' group by id order by id';
						  //echo $query2;
						 $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
						 $rcount_q2 = mysql_num_rows($result2);
						 /* 输出数量结束 */
						 $query = $query." order by id desc"." limit ".$start.",".$end;


						$account = "";
						$account_type="";
						$email="";
						$bank_open="";
						$name="";
						$bank_name="";
						$userphone ="";
						$bank_address="";
						$bank_type=1;	//1:对公账号 2:对私账号
					//echo $query;
						 $result = _mysql_query($query) or die('Query failed: ' . mysql_error());

						 while ($row = mysql_fetch_object($result)) {

							$keyid = $row->id;
							$user_id =$row->user_id;
							$money = $row->money;
							$money = round($money, 2);
							$status = $row->status;
							$createtime = $row->createtime;
							$confirmtime = $row->confirmtime;

							$serial_number=$row->serial_number;
							$remark = $row->remark;
							$batchcode = $row->batchcode;
							$person_information = $row->person_information;

							// 查询供应商名称
							$query2= "SELECT shopName from weixin_commonshop_applysupplys where isvalid=true and user_id=".$user_id." limit 0,1";
							$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
							while ($row2 = mysql_fetch_object($result2)) {
								$shopName		=	$row2->shopName;
							}
							if(trim($shopName)==NULL){
								$query2= "SELECT name from weixin_users where isvalid=true and id=".$user_id." and customer_id=".$customer_id." limit 0,1";
								$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
								while ($row2 = mysql_fetch_object($result2)) {
									$shopName		=	$row2->name;
								}
							}
							$shopName = trim($shopName)==NULL?'暂未设定':'-'.$shopName;
							if( !empty( $person_information ) ){
								$person_information = json_decode($person_information,true);
								$username = $person_information['name'].'('.$person_information['weixin_name'].')';
								$account = $person_information['account'];
								$account_type = $person_information['account_type'];
								$email = $person_information['email'];
								$bank_open = $person_information['bank_open'];
								$name = $person_information['member_name'];
								$bank_name = $person_information['bank_name'];
								$bank_type = $person_information['bank_type'];
								$bank_address = $person_information['bank_address'];
								$userphone = $person_information['phone'];

								// if ($person_information['weixin_name'] == NULL) {
								// 	$que = "select name from weixin_card_members where user_id =".$user_id." and name!='' and phone = ".$userphone." limit 1";
								// 	$res = _mysql_query($que) or die('Query failed: ' . mysql_error());
							 // 		while ($arr = mysql_fetch_object($res)) {
								// 		$username = $person_information['name'].'('.$arr->name.')';
							 // 		}
								// }

								//查找账户和支付宝
								if($search_account_type!=''&&$search_account_type!=-1){
									if( $account_type != $search_account_type ){
										continue;
									}
								}
							}else{
								//查询用户名
								$query2= "select name,phone,weixin_name from weixin_users where isvalid=true and id=".$user_id." and customer_id=".$customer_id." limit 0,1";
								$username="";
								$userphone ="";
								$weixin_name = "";
								$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
								while ($row2 = mysql_fetch_object($result2)) {
									$username=$row2->name;
									//$userphone = $row2->phone;
									$weixin_name = $row2->weixin_name;
									$username = $username."(".$weixin_name.")";
									// break;
								}
								$query2="select account_type,email,account,bank_open,bank_name,name,bank_address,phone,bank_type from weixin_card_members where isvalid=true and user_id=".$user_id;
								if($search_account_type!=''&&$search_account_type!=-1){
									$query2 = $query2." and account_type = {$search_account_type} ";
								}
								$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
								while ($row2 = mysql_fetch_object($result2)) {
									$account= $row2->account;
									$account_type =$row2->account_type;
									$email =$row2->email;
									$bank_open = $row2->bank_open;
									$name = $row2->name;
									$bank_name = $row2->bank_name;
									$bank_type = $row2->bank_type;
									$bank_address = $row2->bank_address;
									$userphone = $row2->phone;
								}
							}


							$statusstr="待审核";
							switch($status){
							   case 1:
								 $statusstr="待审核";
								 break;
							   case 2:
								 $statusstr="通过";
								 break;
							   case 3:
								 $statusstr="已驳回";
								 break;
							}



							$bank_type_str="";
							switch($bank_type){
								case 1:
								   $bank_type_str="对公账号";
								   break;
								case 2:
								   $bank_type_str="对私账号";
								   break;
							}
							if( $account_type == 4 ){
								switch($bank_type){
									case 3:
									   $ips_type_str="个人";
									   break;
									case 4:
									   $ips_str = $ips_type_str = "企业";
									   break;
								}
							}
							$account_type_str="";
							switch($account_type){
								case 0:
								   $account_type_str="微信零钱";
								   break;
								case 1:
								   $account_type_str="支付宝";
								   break;
								case 2:
								   $account_type_str="财付通";
								   break;
								case 3:
								   $account_type_str="银行账户";
								   break;
								case 4:
								   $account_type_str="环迅账户";
								   break;
							}

					   ?>
						<tr>
							<td><?php echo $keyid; ?></td>
							<td><?php echo $batchcode; ?></td>
							<td><span id="span_serial_number_<?php echo $keyid;?>"><?php echo $serial_number; ?></span></td>
							<td><?php echo $account_type_str; ?></td>
							<td>
							   <?php if($account_type!=4){ ?>

								<?php echo $username; ?></a><br/>
							   	<?php echo $userphone; ?><br/>
							   <?php } ?>
							   <?php if($account_type==3){ ?>
							   银行账户:<?php echo $account; ?><br/>
							   开户银行：<?php echo '('.$bank_type_str.')'.$bank_open.'('.$bank_address.')'; ?><br/>
							   开户姓名：<?php echo $bank_name; ?><br/>
							   <?php }elseif($account_type==4){ ?>
							   <?php echo $ips_type_str; ?>账户:<?php echo $account; ?><br/>
							   <?php echo $ips_str ?>名称：<?php echo $name; ?><br/>
							   <?php }elseif( $account_type==1 || $account_type==2 ){ ?>
							   账户:<?php echo $email; ?><br/>
							   名称：<?php echo $name; ?>
							   <?php }else{ ?>
							   名称：<?php echo $name; ?>
							   <?php }?>
							</td>
							<td class='tc'><span><?php echo $user_id; ?><?php echo $shopName; ?></span></td>
						    <td><a href="supplycost_detail.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id; ?>&istype=3&search_batchcode=<?php echo $batchcode; ?>"><?php echo $money; ?>元</a></td>
						    <td><?php echo $createtime; ?></td>
						    <td><span id="span_status_<?php echo $keyid;?>"><?php echo $statusstr; ?></span></td>
						    <td><span id="span_confirmtime_<?php echo $keyid;?>"><?php echo $confirmtime; ?></span></td>
						    <td><span id="span_remark_<?php echo $keyid;?>"><?php echo $remark; ?></span></td>

							<td>
							<span id="span_op_<?php echo $keyid;?>">
							<?php if($status==1 && $account_type==0){?>
								<a  class="btn1" href="javascript:toPay(<?php echo $user_id;?>,<?php echo $batchcode; ?>,<?php echo $money; ?>,<?php echo $keyid;?>);"  title="通过">
									<img src="../../../common/images_V6.0/operating_icon/icon23.png" align="absmiddle"/>
								</a>
								<a  class="btn1" href="javascript:showReason(3,<?php echo $keyid; ?>,<?php echo $user_id; ?>,<?php echo $money; ?>,<?php echo $batchcode; ?>);"  title="驳回">
									<img src="../../../common/images_V6.0/operating_icon/icon03.png" align="absmiddle"/>
								</a>
							<?php }elseif($status==1){?>
								<?php if( $account_type == 4 ){ ?>
								<a  class="btn1" href="javascript:promptBox('<?php echo $money; ?>','<?php echo $account; ?>','<?php echo $name; ?>','<?php echo $gathering_name ?>','<?php echo $gathering_begintime ?>','<?php echo $gathering_endtime ?>',<?php echo $keyid; ?>);"  title="通过">
									<img src="../../../common/images_V6.0/operating_icon/icon23.png" align="absmiddle"/>
								</a>
								<?php }else{ ?>
								<a  class="btn1" href="javascript:sub_status(2,<?php echo $keyid; ?>,<?php echo $user_id; ?>);"  title="通过">
								<img src="../../../common/images_V6.0/operating_icon/icon23.png" align="absmiddle"/>
								</a>
								<?php } ?>
								<a  class="btn1" href="javascript:showReason(3,<?php echo $keyid; ?>,<?php echo $user_id; ?>,<?php echo $money; ?>,<?php echo $batchcode; ?>);"  title="驳回">
								<img src="../../../common/images_V6.0/operating_icon/icon03.png" align="absmiddle"/>
								</a>
							<?php }?>
							</span>

						   </td>
						</tr>
					   <?php } ?>


					</tbody>
				</table>

				<!-- 导出字段选择 -->
			<div class="floatbox">
				<p class="tishitext">导出字段选择</p>
				<div class="checkboxsdiv">
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="user_id"><p>用户ID</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="batchcode"><p>订单号</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="serial_number"><p>交易流水号</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="account_type_str"><p>账户类型</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="username"><p>姓名</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="weixin_name"><p>微信名</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="userphone"><p>账户电话</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="email"><p>账户</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="name"><p>账户名称</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="money"><p>申请金额</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="createtime"><p>申请时间</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="status"><p>申请状态</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="confirmtime"><p>确认时间</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="remark"><p>备注</p></div>
				</div>
				<div class="quanbuxuan">
					<input type="checkbox" id="allselects" checked="checked" value="全选"><p>全选</p>
				</div>
				<div class="subdivb">
					<input type="submit" class="floatinputs" value="确定">
					<input type="submit" class="floatinputc" value="取消">
				</div>
			</div>
			<!-- 导出字段选择 End -->

				<div class="blank20"></div>
				<div id="turn_page"></div>
				<!--翻页开始-->
				<div class="WSY_page">

				</div>
				<!--翻页结束-->
			</div>
		</div>
	</div>


<script src="../../../js/fenye/jquery.page1.js"></script>
<script>
 var pagenum = <?php echo $pagenum ?>;
 var rcount_q2 = <?php echo $rcount_q2 ?>;
 var end = <?php echo $end ?>;
/*var user_id = <?php echo $user_id ?>;*/
 var count =Math.ceil(rcount_q2/end);//总页数
 var customer_id = <?php echo $customer_id ?>;

  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
        	var search_account_type = $('#search_account_type option:selected').val();
        	var search_status = $('#search_status option:selected').val();
		 	var search_batchcode = document.getElementById("search_batchcode").value;
		 	var time_type = $('#foreign_id1').val();
		 	var begintime = $('#begintime').val();
		 	var endtime = $('#endtime').val();
		 	document.location= "cash.php?pagenum="+p+"&search_batchcode="+search_batchcode+"&search_status="+search_status+"&search_account_type="+search_account_type+"&foreign_id1="+time_type+"&begintime="+begintime+"&endtime="+endtime;
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
		 document.location= "cash.php?pagenum="+a+"&search_batchcode="+search_batchcode;
	}
  }

  function sub_status(status,id,user_id){	//确认提交申请提现
    if(status==2){
		//var serial_number=prompt("请输入交易流水账号","");
		var str = '<div>请输入交易流水账号<br/><input type="text" id="trade_no" style="width:100%;padding:5px;border:solid 1px #ddd;"/></div>'
		layer.open({
			title:'提示',
			content:str,
			btn:['确认','取消'],
			yes:function(index){
				var serial_number = $('#trade_no').val();
				if(serial_number == ''){
					layer.msg('交易流水账号不能为空');
				}else{
					url='save_cash.php?callback=jsonpCallback_save_shop_applycash&user_id='+user_id+'&status='+status+"&keyid="+id+"&pagenum="+pagenum+"&serial_number="+serial_number;
					 // console.log(url);
					$.jsonp({
						url:url,
						callbackParameter: 'jsonpCallback_save_shop_applycash'
					});
					layer.close(index);
				}
			},btn2:function(index){
				layer.close(index);
			}
		});
	}
 //    if(serial_number==""){
	//   alert("不能为空");
	//   return;
	// }
	// if(serial_number){
	//  url='save_cash.php?callback=jsonpCallback_save_shop_applycash&user_id='+user_id+'&status='+status+"&keyid="+id+"&pagenum="+pagenum+"&serial_number="+serial_number;
	// 	 // console.log(url);
	// 	 $.jsonp({
	// 		url:url,
	// 		callbackParameter: 'jsonpCallback_save_shop_applycash'
	// 	});
	// }
  }
   function jsonpCallback_save_shop_applycash(results){
	 var keyid = results[0].keyid;
	 var remark = results[0].remark;
	 var serial_number = results[0].serial_number;
	 var confirmtime = results[0].confirmtime;
      document.getElementById("span_status_"+keyid).innerHTML="<span id='span_status_"+keyid+"'>确认</span>";
	   document.getElementById("span_remark_"+keyid).innerHTML="<span id='span_remark_"+keyid+"'>"+remark+"</span>";
      document.getElementById("span_confirmtime_"+keyid).innerHTML="<span id='span_confirmtime_"+keyid+"'>"+confirmtime+"</span>";
      document.getElementById("span_serial_number_"+keyid).innerHTML="<span id='span_serial_number_"+keyid+"'>"+serial_number+"</span>";
      document.getElementById("span_op_"+keyid).innerHTML="<span id='span_op_"+keyid+"'></span>";
  }

  function showReason(status,id,user_id,money,batchcode){	//驳回提现申请
   if(status==3){
	var str=prompt("请输入驳回/暂停理由","您的余额不足以提现，请联系客服");
	}
    if(str)
    {
	   url='save_cash.php?callback=jsonpCallback_showReason&user_id='+user_id+'&status='+status+"&keyid="+id+"&money="+money+"&batchcode="+batchcode+"&pagenum="+pagenum+"&reason="+str;
		 $.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_showReason'
		});
    }
  }
  function jsonpCallback_showReason(results){
	 var keyid = results[0].keyid;
	 var remark = results[0].remark;
	 var confirmtime = results[0].confirmtime;
	 document.getElementById("span_status_"+keyid).innerHTML="<span id='span_status_"+keyid+"'>已驳回</span>";
      document.getElementById("span_remark_"+keyid).innerHTML="<span id='span_remark_"+keyid+"'>"+remark+"</span>";
	  document.getElementById("span_confirmtime_"+keyid).innerHTML="<span id='span_confirmtime_"+keyid+"'>"+confirmtime+"</span>";
	  document.getElementById("span_op_"+keyid).innerHTML="<span id='span_op_"+keyid+"'></span>";
  }
  function toPay(user_id,batchcode,money,id){
  			var lock=false;//默认未锁定
			var url = "../../../mshop/WeChatPay/WeChat_ToPay_supply.php?customer_id=<?php echo $customer_id_en;?>&uid="+user_id+"&kid="+id+"&b="+batchcode+"&p="+pagenum;

			layer.open({
				title:'提示',
				content:'确定打款'+money+'元？',
				btn:['确认','取消'],
				yes:function(index){
					if(!lock){
						lock = true;//锁住
						window.location.href=url;
						layer.close(index);
					}
				},btn2:function(index){
					layer.close(index);
				}
			});

		}
</script>
<script>
$("#my_excel").click(function(){
	$(".floatbox").toggle();
});
	$(".floatinputc").click(function(){
			$(".floatbox").hide();
		});
	$(".floatinputs").click(function(){
        var str="";
        $("input[name='excel_field[]']:checkbox").each(function(){
            if($(this).attr("checked")){
                str += $(this).val()+","
            }
        })
        //alert(str);
        str = str.substring(0,str.length-1);
        //alert(str);
        $(".floatbox").hide();
        export_excel(str);
        //alert(str);
	});
// 全选
$("#allselects").click(function(){
    if(this.checked){
        $(".checkboxsdiv :checkbox").attr("checked", true);
    }else{
        $(".checkboxsdiv :checkbox").attr("checked", false);
    }
});

function selectTime(){
	 var checkValue=$("#foreign_id1").val();
	 if (checkValue!='choiceTime') {
	 	$(".WSY_position1").css("display","inline-block");
	 }else{
	 	$(".WSY_position1").css("display","none");
	 }
}

function export_excel(arr){
	var search_batchcode = $('#search_batchcode').val();//搜索订单\
	var search_account_type = $('#search_account_type option:selected').val();//账户类型
	var search_status = $('#search_status option:selected').val();//申请状态
	var time_type  =  $('#foreign_id1').val();//时间类型：订单确认时间，订单生成时间
	var begin_time = $('#begintime').val();//开始时间
	var end_time   = $('#endtime').val();//结束时间
	if(search_batchcode==""){
		search_batchcode = 0;
	}
	if(begin_time==""){
		begin_time = -1;
	}
	if(end_time==""){
		end_time = -1;
	}
	var url="/weixin/plat/app/index.php/Excel/commonshop_excel_supplier_cash/customer_id/"+customer_id+"/search_batchcode/"+search_batchcode+"/search_status/"+search_status+"/search_account_type/"+search_account_type+"/time_type/"+time_type+"/begin_time/"+begin_time+"/end_time/"+end_time+"/fields/"+arr;
	console.log(url)
	document.location = url;

}

function promptBox(money,account,name,select,begin,end,id){
	var select 	= select.split(',');
	var begin 	= begin.split(',');
	var end 	= end.split(',');
	var str = '<ul class="prompt-ul">';
		str += '<li><span>提现金额：</span> ￥'+money+'</li>';
		str += '<li><span>提现账户号：</span> '+account+'</li>';
		str += '<li><span>提现名称：</span> '+name+'</li>';
		str += '<li><span>收款项目：</span> <select style="width:100px;" id="project">';
		for(var i in select){
			str += '<option data-begin="'+begin[i]+'" data-end="'+end[i]+'">'+select[i]+'</option>';
		}
		str += '</select></li></ul>';

	layer.open({
		title:'确认提示',
		content:str,
		btn:['确认','取消'],
		yes:function(index){
			submitTrue(id,account,money,name);
			layer.close(index);
		},btn2:function(index){
			layer.close(index);
		}
	});
}

function tab(date1,date2){
    var oDate1 = new Date(date1);
    var oDate2 = new Date(date2);
    if(oDate1.getTime() > oDate2.getTime()){
        return 1;
    } else {
        return 2;
    }
}

function submitTrue(id,account,money,name){
	var batchcode = '<?php echo time(); ?>';
	var begin = $('#project option:selected').data('begin');
	var name = $('#project option:selected').text();
	var end = $('#project option:selected').data('end');
	var now = '<?php echo date("Y-m-d") ?>';
	if( tab(now,begin)==2 || tab(end,now)==2 ){

		layer.open({
			title:'提示',
			content:'您的收款项目已过期,请重新添加!',
			btn:['去添加','取消'],
			yes:function(index){
				window.location.href = '//<?php echo HostUrl ?>/wsy_pay/admin/pay_set/hxpay_set.php';
				layer.close(index);
			},btn2:function(index){
				layer.close(index);
			}
		});
	}else{
		$('.promptBox').remove();
		$.ajax({
			url:'../../../mshop/ips_operation.php',
			dataType:'json',
			type:'post',
			data:{
				batchcode:batchcode,
				price:money,
				account:account,
				name:name,
				id:id,
				op:2
			},
			success:function(data){
				if(data.code==0){
					var serial_number = data.serial_number
					url='save_cash.php?callback=jsonpCallback_save_shop_applycash&status=2&keyid='+id+'&serial_number='+serial_number;
					$.jsonp({
						url:url,
						callbackParameter: 'jsonpCallback_save_shop_applycash'
					});
				}else{
					alert(data.msg)
					location.href=location;
				}
			}
		});
	}
}
function offpromptBox(){
	$('.promptBox,.promptBox1').remove();
}
</script>

<?php mysql_close($link);?>

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>