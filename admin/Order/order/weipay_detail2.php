<?php

header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$from_page = $configutil->splash_new($_GET["from_page"]);
$batchcode = $configutil->splash_new($_GET["batchcode"]);

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

$sign_type="";
$service_version="";
$sign ="";
$sign_key_index=1;
$trade_mode = 1;
$trade_state=0;
$pay_info="";
$partner="";
$bank_type="";
$bank_billno="";
$total_fee=0;
$fee_type=0;
$notify_id="";
$transaction_id="";
$out_trade_no="";
$attach="";
$time_end="";
$transport_fee=0;
$product_fee=0;
$discount=0;
$buyer_alias="";
$sendstatus = 0;


$weipay_id=-1;
$query ="SELECT id,sign_type,service_version,sign,sign_key_index,trade_mode,trade_state,pay_info,partner,bank_type,bank_billno,total_fee,fee_type,notify_id,transaction_id,out_trade_no,attach,time_end,transport_fee,product_fee,discount,buyer_alias,sendstatus from weixin_weipay_notifys where isvalid=true and attach='1' and out_trade_no='".$batchcode."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $weipay_id = $row->id;
	$sign_type=$row->sign_type;
	$service_version=$row->service_version;
	$sign =$row->sign;;
	$sign_key_index=$row->sign_key_index;
	$trade_mode = $row->trade_mode;
	$trade_state=$row->trade_state;
	$pay_info=$row->pay_info;
	$partner=$row->partner;
	$bank_type=$row->bank_type;
	$bank_billno=$row->bank_billno;
	$total_fee=$row->total_fee;
	$fee_type=$row->fee_type;
	$notify_id=$row->notify_id;
	$transaction_id=$row->transaction_id;
	$out_trade_no=$row->out_trade_no;
	$attach=$row->attach;
	$time_end=$row->time_end;
	$transport_fee=$row->transport_fee;
	$product_fee=$row->product_fee;
	$discount=$row->discount;
	$buyer_alias=$row->buyer_alias;
	$sendstatus = $row->sendstatus;
}
if($weipay_id<0){
   echo "<script>alert('未支付成功！');window.history.go(-1);</script>";
   return;
}

$trade_mode_str="";
if($trade_mode==1){
  $trade_mode_str="即时到账";
}
$trade_state_str="";
if($trade_state==0){
   $trade_state_str="支付成功";
}

$sendstatus_str="未确认发货接口";
if($sendstatus==1){
   $sendstatus_str="已确认发货接口";
}

//货币符号
$currency_text = '元';
$sql="select * from weixin_currency_symbol_set where customer_id=$customer_id";
$res=_mysql_query($sql);
while ($row = mysql_fetch_object($res) ){
    $currency_text = $row->currency_text;
}
?>
<!doctype html>
<html><head><meta charset="utf-8">
<title>订单管理</title>
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
<script charset="utf-8" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>

</head>
<script>
 function submitV(){
    document.getElementById("keywordFrm").submit();
 }
</script>
<body>
	<!--内容框架-->
<div class="WSY_content">
	<!--列表内容大框-->
	<div class="WSY_columnbox">
		<!--列表头部切换开始-->
		<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a class="white1">微信支付详情</a>
			</div>
		</div><br />
		<!--列表头部切换结束-->


		<div class="WSY_remind_main" style="min-height:250px;">

			<dl class="WSY_remind_dl02" style="margin-top:40px;margin-left:100px;">
				<dt style="line-height:20px;" class="WSY_left">
				交易单号：
				<span class="input"><?php echo $transaction_id; ?></span>
				</dt>
			</dl>

			<dl class="WSY_remind_dl02" style="margin-top:40px;margin-left:100px;">
				<dt style="line-height:20px;" class="WSY_left">
				商户号：
				<span class="input"><?php echo $partner; ?></span>
				</dt>
			</dl>

			<dl class="WSY_remind_dl02" style="margin-top:40px;margin-left:100px;">
				<dt style="line-height:20px;" class="WSY_left">
				付款银行：
				<span class="input"><?php echo $bank_type; ?></span>
				</dt>
			</dl>

			<dl class="WSY_remind_dl02" style="margin-top:40px;margin-left:100px;">
				<dt style="line-height:20px;" class="WSY_left">
				总费用：
				<span class="input"><?php echo number_format($total_fee,2).$currency_text; ?></span>
				</dt>
			</dl>

			<dl class="WSY_remind_dl02" style="margin-top:40px;margin-left:100px;">
				<dt style="line-height:20px;" class="WSY_left">
				币种：
				<span class="input"><?php echo $fee_type; ?></span>
				</dt>
			</dl>

			<dl class="WSY_remind_dl02" style="margin-top:40px;margin-left:100px;">
				<dt style="line-height:20px;" class="WSY_left">
				交易模式：
				<span class="input"><?php echo $trade_mode_str; ?></span>
				</dt>
			</dl>

			<dl class="WSY_remind_dl02" style="margin-top:40px;margin-left:100px;">
				<dt style="line-height:20px;" class="WSY_left">
				交易状态：
				<span class="input"><?php echo $trade_state_str; ?></span>
				</dt>
			</dl>

			<dl class="WSY_remind_dl02" style="margin-top:40px;margin-left:100px;">
				<dt style="line-height:20px;" class="WSY_left">
				发货接口状态：
				<span class="input">
				<?php echo $sendstatus_str;
				if($sendstatus==0){ ?>
					&nbsp;&nbsp;<a href="order_deliver.php?customer_id=<?php echo $customer_id_en; ?>&batchcode=<?php echo $batchcode; ?>" style="color:blue;display: inline-block;" >确认发货接口</a>
				<?php } ?>
				</span>
				</dt>
			</dl>

		</div>
	<div style="width:100%;height:20px;"></div>
	</div>
</div>

<?php mysql_close($link);?>


</body>
</html>