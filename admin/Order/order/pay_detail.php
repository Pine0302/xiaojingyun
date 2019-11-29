<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');  

$pay_batchcode = $configutil->splash_new($_GET["pay_batchcode"]);
$from_page = $configutil->splash_new($_GET["from_page"]);
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php'); 

$payClass			= "钱包零钱支付";
$callBackBatchcode	= -1;
$price				= 0;
$paySql = "select callBackBatchcode,payClass,price from paycallback_t where isvalid=true and pay_batchcode='".$pay_batchcode."' limit 0,1";
$result_paySql = _mysql_query($paySql) or die('paySql failed: ' . mysql_error());
while ($row_paySql = mysql_fetch_object($result_paySql)) {
	$payClass 			= $row_paySql->payClass;
	$price 				= $row_paySql->price;
	$callBackBatchcode 	= $row_paySql->callBackBatchcode;
}
if($callBackBatchcode<0){
	$callBackBatchcode = "暂无";
}
switch($payClass){
	case 1:
		$payClass_srt	= "购物币支付";			   
		break;
	case 2:
		$payClass_srt	= "会员卡余额支付";			   
		break;
	case 3:
		$payClass_srt	= "钱包零钱支付";			   
		break;
	case 4:
		$payClass_srt	= "微信支付";			   
		break;
	case 5:
		$payClass_srt	= "支付宝支付";			   
		break;
	case 6:
		$payClass_srt	= "易宝支付";			   
		break;
	case 7:
		$payClass_srt	= "积分支付";			   
		break;
	case 8:
		$payClass_srt	= "京东支付";
		break;
	default:
		$payClass_srt	= "订单支付";			   
}

$score			= 0;
$totalprice		= 0;
$paystatus		= 0;
$paystatus_str	= "已付款";
$sql = "select sum(origin_price) as totalprice,sum(needScore) as score,paystatus from weixin_commonshop_order_prices where pay_batchcode='".$pay_batchcode."'";
$result = _mysql_query($sql) or die('paySql failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$totalprice = $row->totalprice;
	$score 		= $row->score;
	$paystatus 		= $row->paystatus;
}
if(!$paystatus){
	$paystatus_str	= "未付款";
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
				<a class="white1"><?php echo $payClass_srt; ?>详情</a> 
			</div>
		</div><br />
		<!--列表头部切换结束-->
			<li style="margin-right: 60px;"><a href="javascript:history.go(-1);" class="WSY_button" style="margin-top: 0;width: 60px;height: 28px;vertical-align: middle;line-height: 28px;">返回</a></li>
					
		<div class="WSY_remind_main" style="min-height:250px;">
			<dl class="WSY_remind_dl02" style="margin-top:40px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">
					编号：
				</dt>
				<dd>
					<span class="input"><?php echo $pay_batchcode; ?></span>
				</dd>
			</dl>			
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">支付交易单号：</dt>
				<dd>
					<span class="input">
						<?php echo $callBackBatchcode; ?>
					</span>
				</dd>
			</dl>			
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">总费用：</dt>
				<dd>
					<span class="input">
						<?php  echo number_format($totalprice,2).$currency_text; ?>
					</span>
				</dd>
			</dl>
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">交易状态：</dt>
				<dd>
					<span class="input">
						<?php  echo $paystatus_str; ?>
					</span>
				</dd>
			</dl>
			
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">关联订单：</dt>
                <?php
                $batchcode	 	= -1;
                $CouponPrice 	= 0;
                $pay_currency 	= 0;
                $card_discount 	= 1;
                $query = "select batchcode,CouponPrice,pay_currency,card_discount from weixin_commonshop_order_prices where isvalid=true and pay_batchcode='".$pay_batchcode."'";
                $result = _mysql_query($query) or die('Query_weipay failed: ' . mysql_error());
                while ($row = mysql_fetch_object($result)) {
                    $batchcode 		= $row->batchcode;
                    $CouponPrice 	= $row->CouponPrice;
                    $pay_currency 	= $row->pay_currency;
                    $card_discount 	= $row->card_discount;

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
			<?php if( $payClass == 5 ){ ?>
			<dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;"> 
				<dt style="line-height:20px;" class="WSY_left">支付宝支付：</dt>
				<dd><span class="input">
					<?php  echo round($price,2); ?>
				</span></dd>
			</dl>
			<?php } ?>
			
		</div> 
		<div style="width:100%;height:20px;">
		</div>		
	</div>
</div>

<?php mysql_close($link);?>	
<script type="text/javascript" src="/weixinpl/common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="/weixinpl/common/js_V6.0/content.js"></script>
</body>
</html>