<?php

//ini_set('display_errors','On');
//error_reporting(E_ALL);

header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$from_page = $configutil->splash_new($_GET["from_page"]);
$batchcode = $configutil->splash_new($_GET["batchcode"]);
$pay_batchcode = $configutil->splash_new($_GET["pay_batchcode"]);
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php'); 

$totalprice=0;
$status = 0;
$paystatus= 1;
$transaction_id = "";
$payother_trade_no= "";//代付商户订单号
$sendstatus = 0;
$query="select status,paystatus,sendstatus,return_status,payother_trade_no,paystyle  from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."' group by batchcode";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result)) {
	$paystatus = $row->paystatus;
	$return_status = $row->return_status;
	$status = $row->status;
	$payother_trade_no = $row->payother_trade_no;
	$sendstatus = $row->sendstatus;
	$paystyle = $row->paystyle;
}
//echo $paystyle;
$out_trade_no = $pay_batchcode;
if($paystyle == "找人代付"){
	$out_trade_no = $payother_trade_no;
}
$price				= 0;
$weipay = "select real_pay_price,transaction_id from system_order_pay_log where  pay_batchcode='".$pay_batchcode."'";
$result_weipay = _mysql_query($weipay) or die('Query_weipay failed: ' . mysql_error());
while ($row_result_weipay = mysql_fetch_object($result_weipay)) {
	$transaction_id = $row_result_weipay->transaction_id;
	$price = $row_result_weipay->real_pay_price;
}

$totalprice		= 0;
$score			= 0;
$sql = "select sum(origin_price) as totalprice,sum(needScore) as score  from weixin_commonshop_order_prices where isvalid=true and pay_batchcode='".$pay_batchcode."'";
//echo $sql;
$result = _mysql_query($sql) or die('paySql failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$totalprice = $row->totalprice;
	$score 		= $row->score;
}

$paystatus_str="未付款";
if($paystatus==1){
   $paystatus_str="已付款";
}

$return_status_str="未退款";
if($return_status==1){
   $return_status_str = "退款中";
}
$batchcode_totalprice = 0;
$sql_changeprice = "select totalprice from weixin_commonshop_changeprices where status=1 and isvalid=1 and batchcode='".$batchcode."' order by id desc limit 1";
$result_cp = _mysql_query($sql_changeprice) or die('Query sql_changeprice failed: ' . mysql_error());
if ($row_cp = mysql_fetch_object($result_cp)) {
	$batchcode_totalprice = $row_cp->totalprice;
}else{
//查询订单价格表中的记录
	$sql_price = "select price from weixin_commonshop_order_prices where isvalid=true and batchcode='".$batchcode."'";
	$result_price = _mysql_query($sql_price) or die('Query sql_price failed: ' . mysql_error());
	if ($row_price = mysql_fetch_object($result_price)) {
		//获取订单的真实价格（可能是折扣总价）
		$batchcode_totalprice = $row_price->price;
	}
}
$refund= 0;
$query5="select sum(refund) as refund from weixin_commonshop_refunds where isvalid=true and batchcode='".$batchcode."'";
$result5 = _mysql_query($query5) or die('Query failed: ' . mysql_error());
while ($row5 = mysql_fetch_object($result5)) {
   $refund = $row5->refund;
}
if(!$refund){
	$query5="select sum(refund) as refund from weixin_commonshop_refunds where isvalid=true and batchcode='".$pay_batchcode."'";
	$result5 = _mysql_query($query5) or die('Query failed: ' . mysql_error());
	while ($row5 = mysql_fetch_object($result5)) {
		$refund = $row5->refund;
	}
}


$check_refund_status = 0; //检查他是否可以退款 0:可以退款 1:不可退款
if($refund >= $price){
	$check_refund_status = 1;
}

$refundable = $price;
if(!empty($payother_trade_no)){		//如果是代付.则用代付订单号
	$batchcode = $payother_trade_no;
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
<style>
.WSY_remind_dl01 dd, .WSY_remind_dl02 dt{
	width:115px;
	height:20px;
}
.WSY_remind_dl01 dd, .WSY_remind_dl02 dd{
	line-height:20px;
}
</style>
<body>
	<!--内容框架-->
<div class="WSY_content">
	<!--列表内容大框-->
	<div class="WSY_columnbox">
		<!--列表头部切换开始-->
		<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a class="white1">通联分期支付详情</a>
			</div>
		</div><br />
		<!--列表头部切换结束-->
		<li style="margin-right: 60px;"><a href="javascript:history.go(-1);" class="WSY_button" style="margin-top: 0;width: 60px;height: 28px;vertical-align: middle;line-height: 28px;">返回</a></li>

		
		<div class="WSY_remind_main" style="min-height:250px;">
			<dl class="WSY_remind_dl02" style="margin-top:40px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">编号：</dt>
				<dd>
					<span class="input"><?php echo $pay_batchcode; ?></span>
				</dd>
			</dl>			
			<!-- <dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
					<dt style="line-height:20px;" class="WSY_left">通联分期交易单号：</dt>
					<dd><span class="input">
						<?php echo $transaction_id; ?>
					</span></dd>
				</dl>		 -->	
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">总费用：</dt>
				<dd><span class="input">
					<?php  echo number_format($totalprice,2).$currency_text; ?>
				</span></dd>
			</dl>
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">该订单费用：</dt>
				<dd><span class="input">
					<?php  echo number_format($batchcode_totalprice,2).$currency_text; ?>
				</span></dd>
			</dl>
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">交易状态：</dt>
				<dd><span class="input">
					<?php  echo $paystatus_str; ?>
				</span></dd>
			</dl>
			
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">关联订单：</dt>
				<?php
				$Pbatchcode 	= -1;
				$CouponPrice 	= 0;
				$pay_currency 	= 0;
				$card_discount 	= 1;
				$query = "select batchcode,CouponPrice,pay_currency,card_discount from weixin_commonshop_order_prices where isvalid=true and pay_batchcode='".$pay_batchcode."'";
				$result = _mysql_query($query) or die('Query_weipay failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$Pbatchcode 	= $row->batchcode;
					$CouponPrice 	= $row->CouponPrice;
					$pay_currency 	= $row->pay_currency;
					$card_discount 	= $row->card_discount;
                    
                    if($paystyle == "找人代付"){
                        $pay_currency = 0;
                    }
                    $sql = "SELECT yundian_id,yundian_self from weixin_commonshop_orders where isvalid =true and batchcode='{$batchcode}'";
                    $res = _mysql_query($sql) or die('Query_batchcode failed: ' . mysql_error());
                    $re= mysql_fetch_assoc($res);
                    ?>
                    <dd>
					<span class="input">
                        <?php if($re['yundian_self'] == 0 ){ ?>
                            <a href="order.php?customer_id=<?php echo $customer_id_en ?>&search_batchcode=<?php echo $batchcode ?>&from_page=<?php echo $from_page ?>">
								<?php  echo $batchcode; ?>
							</a>
                        <?php }else{ ?>
                            <a href="/mshop/admin/index.php?m=yundian&a=yundian_order_list&type=1&status=0&batchcode=<?php echo $batchcode; ?>">
								<?php  echo $batchcode; ?>
							</a>
                        <?php } ?>
					</span>
                    </dd>
			<?php
				if( $card_discount > 0 && $card_discount < 1 ){
			?>
				<dt style="line-height:20px;" class="WSY_left"></dt>
				<dd>
					<span class="input">
						会员卡折扣：<?php  echo ($card_discount*100).'%'; ?>
					</span>
				</dd>
			<?php
				}
			?>
			<?php
				if( $CouponPrice > 0 ){
			?>
				<dt style="line-height:20px;" class="WSY_left"></dt>
				<dd>
					<span class="input">
						优惠券金额：<?php  echo $CouponPrice; ?>
					</span>
				</dd>
			<?php
				}
			?>
			<?php
				if( $pay_currency > 0 ){
			?>
				<dt style="line-height:20px;" class="WSY_left"></dt>
				<dd>
					<span class="input">
						<?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>数量：<?php  echo $pay_currency; ?>
					</span>
				</dd>
			<?php
				}
			?>
				<dt style="line-height:20px;" class="WSY_left"></dt>
				<?php
				}
				?>
			</dl>
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">通联分期支付：</dt>
				<dd><span class="input">
					<?php  echo $price; ?>
				</span></dd>
			</dl>
			<?php
			if( $score > 0 ){
			?>			
			<dl class="WSY_remind_dl02" style="margin-top:10px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">积分支付：</dt>
				<dd><span class="input">
					<?php  echo $score; ?>
				</span></dd>
			</dl>
			<?php
			}
			?>
			
			<?php if($refund>0){ ?>
				<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
					<label>该通联分期交易单已退款金额：</label>
					<span class="input">
						<?php echo number_format($refund,2); ?>
					</span>
				</dl>
				<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
					<label></label>
					<span class="input">
					   <?php
						$query5="select refund,createtime from weixin_commonshop_refunds where isvalid=true and batchcode='".$batchcode."'";
						$result5 = _mysql_query($query5) or die('Query failed: ' . mysql_error());
						while ($row5 = mysql_fetch_object($result5)) {
							$refund 	= $row5->refund;
							$createtime = $row5->createtime;
							$refundable = $refundable - $refund;
							
						?> 
						<div style="height:20px;">退款金额：<?php echo $refund; ?> &nbsp;&nbsp;&nbsp;退款时间：<?php echo $createtime; ?></div>
						<?php } ?>
					</span>
				</dl>
			<?php } ?>
			<!-- <?php if($paystatus==1 and $status<1 and ($sendstatus == 3 || $sendstatus == 5) and $check_refund_status==0){ //只有申请退款或退货的才能显示退款操作 
			?>
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">退款状态：</dt>
				<span class="input">
					<?php if($return_status > 0){ ?>
					<?php if($return_status_str==0){ ?>
						&nbsp;&nbsp;<a href="javascript:showReturn();" style="color:blue" >退款</a><span style="color:red">(请到支付宝支付设置上传退款证书)</span>
					<?php } ?>
						
				</span>
			</dl>
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;height:40px" class="WSY_left">该订单可退金额：</dt>
				<span class="input"><?php echo $refundable; ?></span>
			</dl>
			<dl class="WSY_remind_dl02" id="div_return" style="display:none;margin-top:20px;margin-left:100px;">
				<dt style="line-height:40px;" class="WSY_left"><span style="font-size:14px;">退款金额：</span></dt>
				<dd style="line-height:40px;">
					<span><input class="not_agent_tip" value="0" name="return_money" id="return_money" style="width:50px;height:24px;text-align:center;border:solid 1px #ccc;border-radius:2px;" type="text">元(不超过订单金额）</span>
				</dd>	
				<dd class="WSY_bottonli con-button" style="float:left;margin-left:80px;"> <input id="subReturn" value="退款"  type="button" onclick="subReturn();" /></dd>	
			</dl>	
			<?php  }else{  ?>
			<span style="color:red;" >请先审核退款或退货申请</span>
			<?php } 
			} ?> -->
			
		</div> 
		<div style="width:100%;height:20px;">
		</div>		
	</div>
</div>

<?php mysql_close($link);?>	

<script>
function showReturn(){
   document.getElementById("div_return").style.display="block";
}
var totalprice = <?php echo $price; ?>;
var refundable = <?php echo $refundable; ?>;
function subReturn(){
	var paystyle = "<?php echo $paystyle; ?>";
	var this_batchcode_totalprice = "<?php echo number_format($batchcode_totalprice,2,'.',''); ?>";
	$('#subReturn').attr("disabled",true);
    var return_money = document.getElementById("return_money").value;
	
	if(parseFloat(return_money)>parseFloat(this_batchcode_totalprice)){
		alert("退款金额大于该笔订单金额");
		$('#subReturn').attr("disabled",false);
		return;
	}
   
   if(return_money>refundable){
	  alert("退款金额大于可退金额");
	  $('#subReturn').attr("disabled",false);
      return;
   }
   if(paystyle=="兴业银行公众号支付"){
	   document.location = "/weixinpl/common_shop/jiushop/refund_xypay.php?customer_id=<?php echo $customer_id_en; ?>&pay_batchcode=<?php echo $pay_batchcode; ?>&total_fee=<?php echo $price; ?>&batchcode=<?php echo $batchcode?>&out_trade_no=<?php echo $out_trade_no; ?>&refund_fee="+return_money+"&transaction_id=<?php echo $transaction_id;?>";
   }else {
	   document.location = "../../../../wsy_pay/web/alipay_rsa/wappay/refund.php?customer_id=<?php echo $customer_id_en; ?>&pay_batchcode=<?php echo $pay_batchcode; ?>&total_fee=<?php echo $price; ?>&batchcode=<?php echo $batchcode?>&WIDout_trade_no=<?php echo $out_trade_no; ?>&WIDrefund_amount=" + return_money + "&WIDtrade_no=<?php echo $transaction_id;?>&industry_type=shop";
   }
}
</script>
<script type="text/javascript" src="/weixinpl/common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="/weixinpl/common/js_V6.0/content.js"></script>
</body>
</html>