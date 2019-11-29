<?php
header("Content-type: text/html; charset=utf-8");
require($_SERVER['DOCUMENT_ROOT'].'/weixinpl/config.php');
require(LocalBaseURL.'/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require(LocalBaseURL.'/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require(LocalBaseURL.'/proxy_info.php');

_mysql_query("SET NAMES UTF8");
$head=1;

//分页---start
$pagenum = 1;
$pagesize = 20;
$begintime="";
$endtime ="";
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * $pagesize;
$end = $pagesize;




$name 				= '';	//姓名
$weixin_name 		= '';	//微信名
$account 			= '';	//绑定的号码
$balance 			= 0;	//钱包余额
$getmoney 			= 0;	//申请提现金额
$cash_type 			= -1;	//提现类型 1:支付宝,2:财付通,3:银行账户,4:微信零钱
$status 			= 0;	//提现状态 0：未审核 1：已同意提现 2：审核未通过
$status_str			= '';	//提现状态 0：未审核 1：已同意提现 2：审核未通过
$createtime 		= '';	//申请提现金额
$remark 			= '';	//备注
$surplus_type 		=  0;	//0:全额提现 1：直接扣取 2：返购物币 3：扣手续费和返购物币
$callback_fee 		=  0;	//手续费比例
$callback_fee_flxed =  0;	//固定手续费金额
$callback_currency 	=  0;	//返购物币比例
$give_currency	 	=  0;	//赠送购物币比例

// weixin_cash_being_log 钱包零钱提现记录日志表
$query = "SELECT id,getmoney,cash_type,status,createtime,remark,user_id,batchcode,remain_money,surplus_type,callback_fee,callback_fee_flxed,callback_currency,processing_time,give_currency,return_result,person_information,is_mini_mshop FROM weixin_cash_being_log WHERE isvalid=true AND customer_id=$customer_id";

//--------------------------------------------------
//日期条件--开始时间
$begintime = "";
if( !empty($_GET['AccTime_E']) ){  //结算/发放 时间
	$begintime = $_GET['AccTime_E'];
	$query = $query." and UNIX_TIMESTAMP(createtime)>=".strtotime($begintime);
}
//日期条件--结束时间
$endtime = "";
if( !empty($_GET['AccTime_B']) ){   //结算/发放 End
	$endtime = $_GET['AccTime_B'];
	$query = $query." and UNIX_TIMESTAMP(createtime)<=".strtotime($endtime);
}
//编号查询
if( !empty($_GET["promoter"]) ){
	$user_id = $configutil->splash_new($_GET["promoter"]);
	$query = $query." and user_id=".$user_id;
}
//订单号搜索
$batchcode = -1;
if( !empty($_GET['b']) ){   //结算/发放 End
	$batchcode = $configutil->splash_new($_GET["b"]);
	$query = $query." and batchcode='".$batchcode."'";
}
//账户类型搜索
$search_cashtype = "";
if( $_REQUEST['search_cashtype'] != "" ){
	$search_cashtype = $_GET['search_cashtype'];
	$query = $query." and cash_type=".$search_cashtype;
}
//状态搜索
$search_status = "";
if( $_REQUEST['search_status'] != "" ){
	$search_status = $_GET['search_status'];
	$query = $query." and status=".$search_status;
}

$result = _mysql_query($query) or die('Query failed21: ' . mysql_error());
$rcount_q = mysql_num_rows($result);
$page=ceil($rcount_q/$end);
 /* 输出数量结束 */

$query = $query." ORDER BY  id desc LIMIT ".$start.",".$end;

//--小数截取2位方法
function cut_num($menber,$places){
	$places = $places+1;
	$num = substr(sprintf("%.".$places."f", $menber),0,-1);
	return $num;
}

$pay_type = "IPSpay";
// pay_config 第三方支付公共配置表, 其中 gathering 表示 收款项目
$sql = "SELECT gathering From pay_config WHERE customer_id='".$customer_id."' AND pay_type='".$pay_type."' AND isvalid=true limit 1";
$result = _mysql_query($sql) or die(mysql_error().$sql);
if($result){
    $arr = mysql_fetch_row($result);
    $gathering = $arr[0];
}
$gathering = unserialize($gathering);
foreach ($gathering as $key => $value) {
	$gathering_name_list[] 		= $value['pro_name'];
	$gathering_begintime_list[] 	= $value['begintime'];
	$gathering_endtime_list[] 	= $value['endtime'];
}
$gathering_name 		= implode(',', $gathering_name_list);
$gathering_begintime 	= implode(',', $gathering_begintime_list);
$gathering_endtime 		= implode(',', $gathering_endtime_list);


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>待提现记录</title>
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="/weixinpl/css/inside.css" media="all">
<link rel="stylesheet" href="/weixinpl/back_newshops/Order/order/percent/jquery.percentageloader.0.2.css">
<script type="text/javascript" src="/weixinpl/back_newshops/Common/js/layer/jquery.min.js"></script>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script>
<script type="text/javascript" src="/weixinpl/back_newshops/Common/js/layer/layer.js"></script>
<script src="/weixinpl/back_newshops/Common/js/Data/js/echarts/echarts.js"></script>
<script type="text/javascript" src="/weixinpl/back_newshops/Common/js/Data/js/ichartjs/ichart.1.2.min.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/inside.js"></script>

<script type="text/javascript" src="./batch_operate.js"></script>
<script src="/weixinpl/back_newshops/Order/order/percent/jquery.percentageloader.0.2.js"></script>

<link type="text/css" rel="stylesheet" rev="stylesheet" href="./batch_operate.css" media="all">

    <style>

table th{color: #FFF;line-height: 30px;text-align: center;font-size: 12px; }
table td{height: 40px;line-height: 20px;font-size: 12px;color: #323232;padding: 0px 1em;text-align: center;border: 1px solid #D8D8D8; }
.display{display:none}
table td img{width: 20px;height: 20px;margin-left: 5px;}
.mlt12{margin-left: 15px;margin-top: 22px;}
.WSY_position_date select {
    width: 130px;
    height: 24px;
    background: #fefefe;
    border: 1px solid #ccc;
    color: #333;
    border-radius: 2px;
    padding-left: 5px;
}

#topLoader {
	width: 256px;
	height: 256px;
	margin-bottom: 32px;
	position:absolute;width:400px; left:50%; top:50%; margin-left:-200px; height:auto; z-index:100; padding:1px;
}
#per_container {
	width: 500px;
	padding: 10px;
	margin-left: auto;
	margin-right: auto;
}
#BgDiv{background-color:#e3e3e3; position:absolute; z-index:99; left:0; top:0; display:none; width:100%; height:1000px;opacity:0.5;filter: alpha(opacity=50);-moz-opacity: 0.5;}
.layer-ul span{display:inline-block;min-width:80px;}
</style>

    <script>
        var security_sms_key = false;       //定义短信验证全局变量
    </script>
</head>

<body id="bod" style="min-height: 580px;">
<div id="BgDiv"></div>
<div id="per_container">
	<div style="display:none" id="topLoader"></div>
</div>

	<!--内容框架-->
	<div class="WSY_content" style="height: 100%;">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->

				<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/moneybag/basic_head.php");
			?>

			<!--列表头部切换结束-->
<!--门店列表开始-->
  <div  class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list">

	<form action="" >

      	<div style="margin-left:40px;margin-top:0px;">
      		<span style="margin-left:10px;">会员编号：</span>
      		<input type="text" name="promoter" id="promoter_num" maxlength="15" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" value="<?php echo $user_id;?>" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;">
      	<!-- 	<span style="margin-left:20px;">会员卡编号：</span>
      		<input type="text" name="card_num" id="card_member_id" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;"> -->
			<div class="WSY_position1" style="float:left">
				<ul>
					<li class="WSY_position_date tate001" >
						<p>时间：<input class="date_picker" type="text" name="AccTime_E" id="begintime" value="<?php echo $begintime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});"></p>
						<p style="margin-left:0px;">&nbsp;&nbsp;-&nbsp;&nbsp;<input class="date_picker" type="text" name="AccTime_B" id="endtime" value="<?php echo $endtime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});"></p>
					</li>


					<li class="WSY_position_date tate001 mlt12" >账户类型：
						<select name="search_cashtype"  id="search_cashtype">
							<option value="">--全部--</option>
							<option class="WSY_position_date input" value="0" <?php if($search_cashtype=="0"){ ?>selected <?php } ?>>微信零钱</option>
							<option class="WSY_position_date input" value="1" <?php if($search_cashtype=="1"){ ?>selected <?php } ?>>支付宝</option>
							<option class="WSY_position_date input" value="2" <?php if($search_cashtype=="2"){ ?>selected <?php } ?>>财付通</option>
							<option class="WSY_position_date input" value="3" <?php if($search_cashtype=="3"){ ?>selected <?php } ?>>银行账户</option>
							<option class="WSY_position_date input" value="4" <?php if($search_cashtype=="4"){ ?>selected <?php } ?>>环迅账户</option>
						</select>
					</li>

					<li class="WSY_position_date tate001 mlt12" >状态：
						<select name="search_status"  id="search_status">
							<option value="">--全部--</option>
							<option class="WSY_position_date input" value="0" <?php if($search_status=="0"){ ?>selected <?php } ?>>未审核</option>
							<option class="WSY_position_date input" value="1" <?php if($search_status=="1"){ ?>selected <?php } ?>>已同意提现</option>
							<option class="WSY_position_date input" value="2" <?php if($search_status=="2"){ ?>selected <?php } ?>>驳回</option>
						</select>
					</li>

<!--					<li class="WSY_position_date tate001 mlt12" >批量通过-环迅收款项目：-->
<!--						<select name="batch_op_name"  id="batch_op_name">-->
<!--							<option value=""></option>-->
<!--							--><?php //for($i=0;$i<count($gathering_name_list);$i++){?>
<!--							<option class="WSY_position_date input" data-begin="--><?php //echo $gathering_begintime_list[$i];?><!--" data-end="--><?php //echo $gathering_endtime_list[$i];?><!--" value="">--><?php //echo $gathering_name_list[$i];?><!--</option>-->
<!--							--><?php //}?>
<!--						</select>-->
<!--					</li>-->
				</ul>
			</div>

		<input type="submit" id="my_search" value="搜索">
		<input type="button" style="width:100px" id="my_search" value="数据导出" onclick="exportRecord(1)">
		<input class="my_search" type="button" style="text-align: center;margin-left: 10px;width:100px" onclick="exportRecord(2);" value="导出邮箱">
		<input type="button" style="width:100px" class="lot_operate" id="" value="批量通过" onclick="batchPass('<?php echo HostUrl ?>','<?php echo $gathering_name ?>','<?php echo $gathering_begintime ?>','<?php echo $gathering_endtime ?>')">
		<input type="button" style="width:100px" class="lot_operate" id="" value="批量驳回" onclick="batchReject()">

		</div>



	</form>

             <br class="WSY_clearfloat";>
        </div>
        <!--列表按钮开始-->

        <!--表格开始-->
		<div class="WSY_data" id="type1" style="margin-left: 1.5%;">

		<table class="WSY_t2"  width="97%"  style="border: 1px solid #D8D8D8;border-collapse: collapse;">
			<thead class="WSY_table_header">
				<tr style="border:none">
                    <th width="3%">
                        <input type="checkbox" name="all_checkbox" onclick="change_box()" class="all_checkbox" >

                    </th>

					<th width="2%" >ID</th>
					<th width="4%" >编号</th>
					<th width="6%">姓名</th>
					<th width="6%">绑定账号/手机</th>
					<th width="5%">钱包余额</th>
					<th width="5%">申请金额</th>
					<th width="5%">到帐金额</th>
					<th width="5%">手续费</th>
					<th width="5%">返送<?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?></th>
					<th width="5%">赠送<?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?></th>
					<th width="5%">账户类型</th>
					<th width="4%">状态</th>
					<th width="8%">提现申请时间</th>
					<th width="8%">提现审核时间</th>
					<th width="10%">备注信息</th>
					<th width="10%">提现结果</th>
					<th width="8%">操作</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$result= _mysql_query($query) or die('Query failed 170: ' . mysql_error());
				while($row=mysql_fetch_object($result)){
					$id 				= $row->id;
					$getmoney 			= $row->getmoney;
					$cash_type 			= $row->cash_type;
					$user_id 			= $row->user_id;
					$batchcode 			= $row->batchcode;
					$surplus_type 		= $row->surplus_type;         // 0:全额提现 1：直接扣取 2：返购物币 3：扣手续费和返购物币
					$remain_money 		= $row->remain_money;
					$callback_fee 		= $row->callback_fee;         // 手续费比例
					$callback_fee_flxed = $row->callback_fee_flxed;  // 固定手续费金额
					$callback_currency 	= $row->callback_currency;   // 返购物币比例
					$give_currency	 	= $row->give_currency;       // 赠送购物币比例
                    $is_mini_mshop	 	= $row->is_mini_mshop;       // 是否在微信小程序提现
					$return_result      = json_decode($row->return_result,true);
					$person_information = json_decode($row->person_information,true);

					$query2 = "SELECT name,weixin_name FROM weixin_users WHERE isvalid=true AND id=$user_id LIMIT 1";
					$result2= _mysql_query($query2) or die('Query failed 180: ' . mysql_error());
					while( $row2 = mysql_fetch_object($result2) ){
						$name 			= $row2->name;                 // 自定义名
						$weixin_name 	= $row2->weixin_name;         // 昵称
					}
					// system_user_t 系统用户信息表, 其中 account 为 账号（手机号）
					$query3 = "SELECT account FROM system_user_t WHERE isvalid=true AND user_id=$user_id LIMIT 1";
					$result3= _mysql_query($query3) or die('Query failed 186: ' . mysql_error());
					while( $row3 = mysql_fetch_object($result3) ){
						$account = $row3->account;
					}
					// moneybag_t 商城用户钱包余额表，其中 balance 为 钱包总余额
					$query4 = "SELECT balance FROM moneybag_t WHERE isvalid=true AND user_id=$user_id AND customer_id=$customer_id LIMIT 1";
					$result4= _mysql_query($query4) or die('Query failed 186: ' . mysql_error());
					while( $row3 = mysql_fetch_object($result4) ){
						$balance = $row3->balance;
					}


					switch ($cash_type) {
						case '0':
							$cash_type = "微信零钱";
						break;
						case '1':
							$cash_type = "支付宝";
						break;
						case '2':
							$cash_type = "财付通";
						break;
						case '3':
							$cash_type = "银行账户";
						break;
						case '4':
							$cash_type = "环迅账户";
						break;
						default :
							$cash_type = "未知";
						break;

					}
					if($account == ''){
						$account = '<span style="color:#c22439;font-weight:blod;font-size:14px;">尚未绑定</span>';
					}

					$status 		= $row->status;
					switch ($status) {
						case '0':
							$status_str = '<span style="color:#c22439;font-weight:blod;font-size:14px;">未审核</span>';
						break;

						case '1':
							$status_str = '<span style="color:#06a7e1;font-weight:blod;font-size:14px;">已同意提现</span>';
						break;

						case '2':
							$status_str = '<span style="color:#68af27;font-weight:blod;font-size:14px;">驳回</span>';
						break;

					}
					//提交申请时间
					$createtime 	 = $row->createtime;
					//提交审核时间
					$processing_time = $row->processing_time;
					//状态备注
					$remark 		 = $row->remark;
                // moneybag_account 钱包提现账号表
				$query2 = "SELECT real_name,phone,bind_account,bind_band,bind_bang_address FROM moneybag_account WHERE isvalid=true AND user_id=$user_id";
				switch ($cash_type) {
					case '微信零钱':
						$sql2 = $query2." AND type = 1 LIMIT 1";
						break;
					case '支付宝':
						$sql2 = $query2." AND type = 2 LIMIT 1";
						break;
					case '财付通':
						$sql2 = $query2." AND type = 3 LIMIT 1";
						break;
					case '银行账户':
						$sql2 = $query2." AND type = 4 LIMIT 1";
						break;
					case '环迅账户':
						$sql2 = $query2." AND type = 5 LIMIT 1";
						break;
				}
				$res2 = _mysql_query($sql2) or die('Query failed22: ' . mysql_error());
				while( $row=mysql_fetch_object($res2) ){
					$real_name 			= $row->real_name;
					$phone 				= $row->phone;
					$bind_account 		= $row->bind_account;
					$bind_band 			= $row->bind_band;
					$bind_bang_address 	= $row->bind_bang_address;
				}

				$commission_fee 	 = 0;	//手续费
				$commission_currency = 0;	//购物币
				//手续费/返购物币
				if( $surplus_type == 0 ){	//全额提现
					$commission_fee 	 = 0;
					$commission_currency = 0;
				} else if( $surplus_type == 1 ){	//直接扣取
					if( $callback_fee > 0 ){
						$commission_fee = $getmoney*$callback_fee/100;
					} else if( $callback_fee_flxed > 0 ){
						$commission_fee = $callback_fee_flxed;
					}

				} else if( $surplus_type == 2 ){	//返购物币
					$commission_currency = $getmoney*$callback_currency/100;
				} else if( $surplus_type == 3 ){	//扣手续费和返购物币
					if( $callback_fee > 0 ){
						$commission_fee = $getmoney*$callback_fee/100;
					} else if( $callback_fee_flxed > 0 ){
						$commission_fee = $callback_fee_flxed;
					}

					$commission_currency = $getmoney*$callback_currency/100;
				}

				//因为数据精度问题$commission_fee，$commission_currency去掉原有的round()，见报障19148 ，为了保持与实际发放时同步，这里不进行精度取舍，也可参考WeChat_ToPay.php里面的发放代码
				$real_cash = round((($getmoney-$commission_fee-$commission_currency)*100)/100,2);

				if( $real_cash < 0 ){
					$real_cash = 0;
				}

				$user_give_currency  = round($getmoney*$give_currency/100,2); 	//赠送购物币

                //组建数组赋值于checkbox的c_data属性上
                $c_data                             = array();
                $c_data['id']                       = $id;
                $c_data['cash_type']               = $cash_type;
//                $c_data['user_id']                 = $user_id;
                $c_data['batchcode']               = $batchcode;
                $c_data['real_cash']               = $real_cash;
                $c_data['bind_account']            = $bind_account;
                $c_data['person_bind_account']    = $person_information['bind_account'];
                $c_data['person_real_name']       = addslashes($person_information['real_name']);
                $c_data['gathering_name']         = $gathering_name;
                $c_data['gathering_begintime']   = $gathering_begintime;
                $c_data['gathering_endtime']     = $gathering_endtime;
                $c_data['gathering_begintime']   = $gathering_begintime;
                    //搜索框传值
                $c_data['AccTime_E']              = $begintime;
                $c_data['AccTime_B']              = $endtime;
                $c_data['pagenum']                = $pagenum;
                $c_data['search_cashtype']       = $search_cashtype;
                $c_data['search_status']         = $search_status;
                $c_data_json = json_encode($c_data); //将数组json格式化，方便传参


			?>
				<tr style="border:1px solid #D8D8D8" class="tr<?php echo $id;?> tr" id="tr<?php echo $batchcode;?>">
                    <td>
                        <input type="checkbox" name="input_checkbox" class="checkbox" c_data='<?php echo $c_data_json; ?>'  <?php if($status != 0) echo 'disabled="disabled" '; ?> />

                    </td>

					<td><?php echo $id;?></td>
					<td><?php echo $user_id;?></td>
					<td><?php echo $person_information['real_name'];?></td>
					<td>
						<?php
							if($cash_type == "微信零钱"){
								echo "手机号：".$person_information['phone'];
							}elseif($cash_type == "支付宝"){
								echo "手机号：".$person_information['phone']."</br>";
								echo "支付宝：".$person_information['bind_account'];
							}elseif($cash_type == "财付通"){
								echo "手机号：".$person_information['phone']."</br>";
								echo "财付通：".$person_information['bind_account'];
							}elseif($cash_type == "银行账户"){
								echo "银行账号：".$person_information['bind_account']."</br>";
								echo "所属银行：".$person_information['bind_band']."</br>";
								echo "所属支行：".$person_information['bind_bang_address'];
							}elseif($cash_type == "环迅账户"){
								echo "环迅账号".$person_information['bind_account'];
							}
						?>
					</td>
					<td><a style="color: #06a7e1;" href="user_detail.php?customer_id=<?php echo $customer_id_en;?>&user_id=<?php echo $user_id;?>"><?php echo cut_num($balance,2);?></a></td>
					<td><?php echo cut_num($getmoney,2);?></td>
					<td><?php echo cut_num($real_cash,2);?></td>
					<td><?php echo cut_num($commission_fee,2);?></td>
					<td><?php echo cut_num($commission_currency,2);?></td>
					<td><?php echo cut_num($user_give_currency,2);?></td>
					<td><?php echo $cash_type;?></td>
					<td class="str<?php echo $id;?>"><?php echo $status_str;?></td>
					<td><?php echo $createtime;?></td>
					<td class="processing_time_<?php echo $id;?>"><?php echo $processing_time;?></td>
					<td><?php echo $remark;?></td>
					<td><?php if($return_result['return_code']=="SUCCESS"&&$return_result['result_code']=="SUCCESS"){
							echo "零钱提现成功";
						}else if($return_result==null){
							echo " ";

							}else{
								echo "零钱提现出错:".$return_result['err_code_des'];
								}?></td>
					<td class="images">
					<?php if( $status == 0 ){ ?>
						<?php if($cash_type == '微信零钱'){?>
							<a onclick="toPay(this,<?php echo $user_id;?>,<?php echo $id;?>,<?php echo $batchcode;?>,<?php echo $real_cash;?>);" title="确定打款">
								<img src="/weixinpl/common/images_V6.0/operating_icon/icon23.png" class="<?php echo $id;?>">
							</a>
						<?php }else if($cash_type == '环迅账户'){?>
							<!--     -->
							<a onclick="promptBox('<?php echo cut_num($real_cash,2); ?>','<?php echo $person_information['bind_account']; ?>','<?php echo $person_information['real_name']; ?>','<?php echo $gathering_name ?>','<?php echo $gathering_begintime ?>','<?php echo $gathering_endtime ?>',<?php echo $id; ?>,'<?php echo $batchcode;?>')" title="确定打款">
								<img src="/weixinpl/common/images_V6.0/operating_icon/icon23.png" class="<?php echo $id;?>">
							</a>
						<?php }else{?>
							<a onclick="Athor_pay('<?php echo $batchcode;?>',<?php echo $real_cash;?>,<?php echo $id;?>,'<?php echo $person_information['bind_account'];?>');" title="确定打款">
								<img src="/weixinpl/common/images_V6.0/operating_icon/icon23.png" class="<?php echo $id;?>">
							</a>
						<?php }?>
						<a title="驳回申请" class="B_hui" onclick="false_type(<?php echo $id;?>);">
							<img src="/weixinpl/common/images_V6.0/operating_icon/icon25.png" class="<?php echo $id;?>">
						</a>
					<?php }?>
					<a title="删除申请" onclick="delete_type(<?php echo $id;?>);">
							<img src="/weixinpl/common/images_V6.0/operating_icon/icon04.png">
						</a>

					<!--<a title="提现详情" href="./cash_detail.php?customer_id=<?php echo $customer_id_en;?>&b=<?php echo $batchcode;?>">
							<img src="../../../common/images_V6.0/operating_icon/icon44.png">
						</a>-->
					</td>
				</tr>
			<?PHP }?>

			</tbody>

			</table>

			<!--翻页开始-->
			<div class="WSY_page">

			</div>
			<!--翻页结束-->
		</div>
		<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
	  	<script src="/weixinpl/common/js/floatBox.js"></script>
	  	<script src="/wsy_pub/admin/static/js/sms_verification.js"></script>
		<script type="text/javascript">
			var customer_id_en = '<?php echo $customer_id_en ?>';
			var customer_id = '<?php echo $customer_id ?>';
			var pagenum = <?php echo $pagenum ?>;
			var count =<?php echo $page ?>;//总页数
			//pageCount：总页数
			//current：当前页
			var user_id = $("#promoter_num").val();
			var card_id = $("#card_member_id").val();
			var AccTime_E = $("#begintime").val();
			var AccTime_B = $("#endtime").val();
			var search_cashtype = $("#search_cashtype").val();
			var search_status = $("#search_status").val();


			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
				 document.location= "cash_being.php?customer_id="+customer_id_en+"&pagenum="+p+"&promoter="+user_id+"&AccTime_E="+AccTime_E+"&AccTime_B="+AccTime_B+"&search_cashtype="+search_cashtype+"&search_status="+search_status;
			   }
			});

		  var page = <?php echo $page ?>;

		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());

			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
			document.location= "cash_being.php?customer_id="+customer_id_en+"&pagenum="+a+"&promoter="+user_id+"&AccTime_E="+AccTime_E+"&AccTime_B="+AccTime_B+"&search_cashtype="+search_cashtype+"&search_status="+search_status;
			}
		  }
		</script>
		<script type="text/javascript">

		function false_type(id){

			var tis_str = prompt("请输入驳回理由（30字符内）","余额不够");
			if(tis_str){
				if( tis_str.length > 30 ){
					alert('请控制字数在30个字符以内');
					return;
				}
				var kid = id;
				var type = 'false_type';
				$.ajax({
					url:'save_cash_type.php',
					dataType:'json',
					type:'post',
					data:{
							id:kid,
							type:type,
							tis:tis_str
					},
					success:function(data){
						if(data.status==400){
								// document.location="cash_being.php?customer_id=<?php echo $customer_id_en;?>";
								//history.go(0);
								$("."+id).hide();
								$(".str"+id).html('<span style="color:#68af27;font-weight:blod;font-size:14px;">驳回</span>');
								$(".processing_time_"+id).html(data.datetime);
						}else{
							alert("未知错误");
						}
					}
				});
			}

		}
		function delete_type(id){
			var kid = id;
			var type = 'delete_type';
			var is_pay = confirm("您确定删除吗？");
			if( is_pay==false){
				return false;
			}
			$.ajax({
				url:'save_cash_type.php',
				dataType:'json',
				type:'post',
				data:{
						id:kid,
						type:type
				},
				success:function(data){
					if(data==400){
							//document.location="cash_being.php?customer_id=<?php echo $customer_id_en;?>";
							//history.go(0);
							$("."+id).hide();
							$(".tr"+id).remove();

					}else{
						alert("未知错误");
					}
				}
			});
		}

		function promptBox(money,account,name,select,begin,end,id,batchcode){
			var select 	= select.split(',');
			var begin 	= begin.split(',');
			var end 	= end.split(',');

			var str = '<ul class="layer-ul"><li><span>提现金额：</span> ￥'+money+'</li>';
				str += '<li><span>提现账户号：</span> '+account+'</li>';
				str += '<li><span>提现名称：</span> '+name+'</li>';
				str += '<li><span>收款项目：</span> <select style="width:100px;" id="project">';
				for (var i = 0; i < select.length; i++) {
					str += '<option data-begin="'+begin[i]+'" data-end="'+end[i]+'">'+select[i]+'</option>';
				}
				str += '</select></li>';
				str += '</ul>';
			//弹窗
			layer.open({
				title:'确认提示',
				content:str,
				btn:['确认','取消'],
				yes:function(index){
					submitTrue(id,account,money,name,batchcode);
					layer.close(index);
				},
				btn2:function(index){
					layer.close(index);
				}
			});
			// var select 	= select.split(',');
			// var begin 	= begin.split(',');
			// var end 	= end.split(',');
			// console.log(select.length,begin)
			// var str = '<div class="promptBox"><h3>确认提示</h3><ul>';
			// 	str += '<li>提现金额： ￥'+money+'</li>';
			// 	str += '<li>提现账户号： '+account+'</li>';
			// 	str += '<li>提现名称： '+name+'</li>';
			// 	str += '<li>收款项目： <select style="width:100px;" id="project">';
			// 	for (var i = 0; i < select.length; i++) {
			// 		str += '<option data-begin="'+begin[i]+'" data-end="'+end[i]+'">'+select[i]+'</option>';
			// 	}
			// 	// for(var i in begin){
			// 	// }
			// 	str += '';
			// 	str += '</select></li>';
			// 	str += '</ul><p><input type="button" class="WSY_button" value="确认" style="cursor:pointer;" onclick="submitTrue('+id+','+account+','+money+',\''+name+'\');">';
			// 	str += '<input type="button" class="WSY_button" value="取消" style="cursor:pointer;" onclick="offpromptBox();"></p></div>';
			// $('body').append(str);
		}

		function tab(date1,date2){
		    var oDate1 = new Date(date1);
		    var oDate2 = new Date(date2);
		    if(oDate1.getTime() >= oDate2.getTime()){
		        return 1;
		    } else {
		        return 2;
		    }
		}

		function submitTrue(id,account,money,name,batchcode){
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
					},
					btn2:function(index){
						layer.close(index);
					}
				});
				// txt = '您的收款项目已过期,请重新添加!'
				// var str = '<div class="promptBox1"><p>提示</p><div>';
				// str += ''+txt+'';
				// str += '</div><p><a href="//<?php echo HostUrl ?>/wsy_pay/admin/pay_set/hxpay_set.php"><input type="button" class="WSY_button" value="去添加" style="cursor:pointer;" ></a>';
				// str += '<input type="button" class="WSY_button" value="取消" style="cursor:pointer;" onclick="offpromptBox();"></p></div>';
				// $('body').append(str);
			}else{
				$('.promptBox').remove();
				$.ajax({
					url:'<?php echo BaseURL;?>/mshop/ips_operation.php',
					dataType:'json',
					type:'post',
					data:{
						batchcode:batchcode,
						price:money,
						account:account,
						name:name,
						id:id,
						type:'user',
						op:2
					},
					success:function(data){
						console.log(data.code==0)
						if(data.code==0){
							$.ajax({
								url:'save_pay.php',
								dataType:'json',
								type:'post',
								data:{
									batchcode:batchcode,
									customer_id:customer_id
								},
								success:function(data){
									var data = eval(data);
									if(data['status']=='401'){
										$("."+id).hide();
										$(".str"+id).html('<span style="color:#06a7e1;font-weight:blod;font-size:14px;">已同意提现</span>');
										$(".processing_time_"+id).html(data.datetime);
                                        //插入操作日志
                                        var log_content = 'ID'+id+'，提现审核通过；';
                                        $.ajax({
                                            type: "post",
                                            url: "/wsy_pub/admin/index.php?m=security_sms&a=sys_log_insert",
                                            data: {'sys_calss': 'shop_system_moneybag','sys_content':log_content},
                                            dataType: "json",
                                            success: function (res) {
                                                console.log('succrss');
                                            },
                                            error: function (e) {
                                                console.log('操作日志插入失败')
                                            }
                                        });
									}else{
										alert("未知错误");
									}
								}
							});
						}else{
							alert(data.msg);
							location.href=location;
						}
					}
				});
			}
		}
		function offpromptBox(){
			$('.promptBox,.promptBox1').remove();
		}

		function IPS_pay(batchcode,money,id,account){
			var batchcode = batchcode;
			//var user_id = "<?php echo $user_id;?>";
			var customer_id = "<?php echo $customer_id_en;?>";
			var is_pay = confirm("确定打款"+money+"元？");
			if( is_pay==false){
				return false;
			}
			$.ajax({
				url:'<?php echo BaseURL;?>/mshop/ips_operation.php',
				dataType:'json',
				type:'post',
				data:{
						batchcode:batchcode,
						price:money,
						account:account,
						op:2
				},
				success:function(data){
					if(data.code==0){
						$.ajax({
							url:'save_pay.php',
							dataType:'json',
							type:'post',
							data:{
								batchcode:batchcode,
								customer_id:customer_id
							},
							success:function(data){
								var data = eval(data);
								if(data['status']=='401'){
									$("."+id).hide();
									$(".str"+id).html('<span style="color:#06a7e1;font-weight:blod;font-size:14px;">已同意提现</span>');
									$(".processing_time_"+id).html(data.datetime);
								}else{
									alert("未知错误");
								}
							}
						});
					}else{
						alert('请求失败')
					}
				}
			});

		}

        function Athor_pay(batchcode,money,id,account){
		    var param = [batchcode,money,id,account];
		    sms_check("moneybag_withdraw","go_Athor_pay",param);
        }
		function go_Athor_pay(batchcode,money,id,account){
			var batchcode = batchcode;
			//var user_id = "<?php echo $user_id;?>";
			var customer_id = "<?php echo $customer_id_en;?>";
			var is_pay = confirm("确定打款"+money+"元？");
			if( is_pay==false){
				return false;
			}
            $.ajax({
                url:'save_pay.php',
                dataType:'json',
                type:'post',
                data:{
                    batchcode:batchcode,
                    //user_id:user_id,
                    customer_id:customer_id
                },
                success:function(data){
                    var data = eval(data);
                    if(data['status']==401){
                        // alert(data['msg']);
                        // //document.location="cash_being.php?customer_id=<?php echo $customer_id_en;?>";
                        // history.go(0);
                        $("."+id).hide();
                        $(".str"+id).html('<span style="color:#06a7e1;font-weight:blod;font-size:14px;">已同意提现</span>');
                        $(".processing_time_"+id).html(data.datetime);
                    }else{
                        alert("未知错误");
                    }
                }
            });
		}
		var lock = 0; //事件锁 防止多次提交
		var _inot = '';
		function toPay(obj,user_id,id,batchcode,money){
			if(lock == 1) return;
			lock = 1;
            var param = [user_id,id,batchcode,money];
            sms_check("moneybag_withdraw","go_toPay",param);
            _inot = obj;
        }
        function go_toPay(user_id,id,batchcode,money){
			var AccTime_E = $("#begintime").val();
			var AccTime_B = $("#endtime").val();
			var pagenum = "<?php echo $pagenum;?>";
			var search_cashtype = "<?php echo $search_cashtype;?>";
			var search_status = "<?php echo $search_status;?>";
			var user_id = $("#promoter_num").val();

			var url = "<?php echo BaseURL;?>mshop/WeChatPay/WeChat_ToPay.php?customer_id=<?php echo $customer_id_en;?>&uid="+user_id+"&kid="+id+"&b="+batchcode+"&AccTime_E="+AccTime_E+"&AccTime_B="+AccTime_B+"&pagenum="+pagenum+"&search_cashtype="+search_cashtype+"&search_status="+search_status;

			console.log(url);

			var is_pay = confirm("确定打款"+money+"元？");
			if(is_pay==false){
				lock = 0;
				return false;
			}else{
                $.ajax({
					url: url,
					dataType: 'html',
					data: {},
					type: 'get',    
					async:true,   
					success:function (res){   
						
					},
					error:function(res){
						console.log(res);
					},
				});
				alert('确认打款：'+money+"元成功，请稍后刷新查看提现结果");
				$(_inot).hide();
				$(_inot).siblings('.B_hui').hide();
				$('.str'+id).find('span').text('已同意提现').css({'color':'#06a7e1'});   
                //window.location.href=url;
			}

		}

</script>

<script>
		  var glo_add;
		  var glo_per;//完成百份比
		  var obj_json;
		  var topLoaderRunning;
		  var $topLoader;
		  $(function() {
			  inti_per();
		  });

		  function inti_per(){
			  glo_add = 0.0;
			  glo_per = 0.0;
			  $topLoader = $("#topLoader").percentageLoader({
				  width: 256, height: 256, controllable: true, progress: glo_add, onProgressUpdate: function (val) {
					  this.setValue(Math.round(val * 100.0) + '%初始化中，请勿刷新和关闭页面！');
				  }
			  });
			  topLoaderRunning = false;
		  }

		  function ShowDIV(thisObjID) {
			  $("#BgDiv").css({ display: "block", height: $(document).height() });
			  var yscroll = document.documentElement.scrollTop;
			  $("#" + thisObjID).css("top", "100px");
			  $("#" + thisObjID).css("display", "block");
			  document.documentElement.scrollTop = 0;
		  }

		  function closeDiv(thisObjID) {
			  $("#BgDiv").css("display", "none");
			  $("#" + thisObjID).css("display", "none");
		  }
		  
		  //零钱提现日志导出功能
		  function exportRecord(opt){
			var excelArray = [
			  ["id","ID"],
			  ["user_id","编号"],
			  ["real_name","姓名"],
			  ["balance","钱包余额"],
			  ["getmoney","申请提现金额"],
			  ["real_cash","到账金额"],
			  ["commission_fee","手续费"],
			  ["commission_currency","返送购物币"],
			  ["user_give_currency","赠送购物币"],
			  ["cash_type_str","账户类型"],
			  ["bind_account","绑定的账号"],
			  ["phone","绑定的手机号码"],
			  ["bind_band","绑定的银行"],
			  ["bind_bang_address","绑定银行所属支行"],
			  ["status_str","状态"],
			  ["createtime","提现申请时间"],
			  ["processing_time","提现审核时间"],
			  ["remark","备注信息"],
			  ["return_code_str","提现结果"],
		  	];  

			  var user_id = $("#promoter_num").val();
			  var AccTime_E = $("#begintime").val();
			  var AccTime_B = $("#endtime").val();
			  var search_cashtype = $("#search_cashtype").val();
			  var search_status = $("#search_status").val();

			  exportBox(excelArray);
			  $(".floatbox").show();

			  $(".floatinputs").click(function() {
			  	var str = "";
				var excludes = '';
				$("input[name='excel_field[]']:checkbox").each(function () {
				  if ($(this).is(':checked')) {
					  str += $(this).val() + ","
				  }else{
				  	excludes += $(this).val()+ ',';
				  }
				});
				str = str.substring(0, str.length - 1);

				if (str == "") {
				  str = 0;
				}
			  	if(opt == 1)
			  	{
				  var url = '/weixin/plat/app/index.php/Excel/cash_being_excel/customer_id/<?php echo $customer_id; ?>';
				  if (AccTime_E != "") {
					  url = url + '/AccTime_E/' + AccTime_E;
				  }
				  if (AccTime_B != "") {
					  url = url + '/AccTime_B/' + AccTime_B;
				  }
				  if (user_id != "") {
					  url = url + '/user_id/' + user_id;
				  }
				  if (search_cashtype != "") {
					  url = url + '/search_cashtype/' + search_cashtype;
				  }
				  if (search_status != "") {
					  url = url + '/search_status/' + search_status;
				  }

				  url = url +  '/excel_fields/' + str;
				  console.log(url);

//				  var oFunc = function () {
////					  console.log(url);
//					  $.ajax({type:'GET', async:false, url:url,
//						  success:function(data){
//							  if(data != null){
//								  closeDiv('topLoader');
//							  }else{ }
//							  console.log(data);
//						  }
//					  });
//				  };


				  inti_per();
				  ShowDIV('topLoader');
				  if (topLoaderRunning) {
					  return;
				  }
				  topLoaderRunning = true;

				  glo_add = 0.0;
				  $topLoader.percentageLoader({progress: glo_add});
				  $topLoader.percentageLoader({value: ('导出中，请勿刷新和关闭页面！')});

				  setTimeout(function(){
//					  $.ajax({type:'GET', async:false, url:url,
//						  success:function(){
//							  closeDiv('topLoader');
//						  }
//					  });
					  document.location = url;
					  closeDiv('topLoader');
				  		$(".floatbox").hide();
				  		$(".floatbox").remove();
				  },2000);
				}
				else
				{
					var _condition = {'AccTime_E':AccTime_E,
								'AccTime_B':AccTime_B,
								'user_id':user_id,
								'search_status':search_status,
								'search_cashtype':search_cashtype,
								'customer_id':customer_id,
								'excludes':excludes
							};
				    var condition = JSON.stringify(_condition);
				    var name = 'cash_being_excel';
				    var op   = 'iscount';
				    $.ajax({
				    	type:'post',
				    	async:false, 
				    	url:'/weixinpl/common/explore/jiaoben.php',
				    	data:{fields:str,
				    		function_name:name,
				    		param_json:condition,
				    		customer_id:customer_id,
				    		op:op,
				    	},
				        success:function(data)
				        {
				            var res = JSON.parse(data);
				            var eamil_arr     = res.emails.split('#*#');
				            var eamil_address = "";
				            var type          = 2;
				            var op            = 'add_email';
				            var tips          = "导出数据已打包发送到您的邮箱，请注意查收";
				            if(res.errcode == 10003)
				            {
				                layer.alert(res.errmsg);
				                return;
				            }
				            else
				            {
				                type = 2;
				                tips = "请留意您的邮箱，导出完成后会发到你的邮箱上！";
				                layer.prompt({title: '请输入您邮箱地址',value:eamil_address, formType: 0}, function(email, prompt){
				                    layer.close(prompt);
				                    if (checkEmail(email)){
				                        emails = email;
					                        $.ajax({
					                        	type:'post',
					                        	 async:false, 
					                        	 url:'/weixinpl/common/explore/jiaoben.php', 
					                        	 data:{fields:str,
					                        	 	function_name:name,
					                        	 	param_json:condition,
					                        	 	customer_id:customer_id,
													email:emails,
													op:op,
													type:type,
					                        	},
					                        success:function(data){
					                            var res           = JSON.parse(data);
					                            console.log(res);
					                            if(res.status == 2)
					                            {
					                                layer.alert(res.msg);
					                                return;
					                            }
					                            $.post('/weixinpl/common/explore/jiaoben.php',{'debug':1},function(da){},'json');
					                            layer.alert(tips);
					                            $(".floatbox").hide();
					                        }
					                    });
				                    }
				                    else
				                    {
				                        layer.alert("邮箱地址填写有误，请填写正确的邮箱地址");
				                        return;
				                    }
				                })
				            }


				        }
				    });
				}
			});
		}
		/*校验邮箱地址*/
		function checkEmail(str){
		    var re= /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
		    return re.test(str);
		}
		//点击复选框，判定是否全选
	    $('.checkboxsdiv input').click(function(){
	    	var num = 0;
	    	$("input[name='excel_field[]']").each(function(){
		        if($(this).attr("checked")){
		        	num++;
		        }
	    	});
	    	if( num >= 18 )
	    	{
	    		$('#allselects').attr('checked', true);
	    	}
	    	else
	    	{
	    		$('#allselects').attr('checked',false);
	    	}
	    })
	    // 全选
	    $("#allselects").click(function(){
	        if(this.checked){
	            $(".checkboxsdiv :checkbox").attr("checked", true);
	        }else{
	            $(".checkboxsdiv :checkbox").attr("checked",false);
	        }
	    }); 
</script>

	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="/weixinpl/css/fenye/fenye.css" media="all">


<?php

mysql_close($link);
?>

</body>
</html>
