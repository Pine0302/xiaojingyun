<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=6;//头部文件0支付方式，1微信支付,2支付宝,3财务通,4通联支付,6银联支付

$query = "select id,merchant_no,terminal_no,merchant_key from weixin_china_unionpays where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$merchant_no  = "";
$terminal_no  = "";
$merchant_key = "";
$unionpay_id  = -1;
while ($row = mysql_fetch_object($result)) {
	$merchant_no  = $row->merchant_no;
	$unionpay_id  = $row->id;
	$terminal_no  = $row->terminal_no;
	$merchant_key = $row->merchant_key;
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/pay_set/alipay_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>


<title>银联支付</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php");  
		?>
		<form action="save_unionpay_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="unionpay_id" value="<?php echo $unionpay_id ?>" />
			<div class="WSY_remind_main">
				<!--<dl class="WSY_remind_dl02"> 
					<dt>教程：</dt>
					<dd class="course_dd ">						
						<span style="font-size:14px;line-height:24px;;color:#6b6969">开启支付后，商城等b2c功能将有支付功能</span>
						 <a  href="https://b.alipay.com/order/productDetail.htm?productId=2013080604609688" style="color:red"  target="_blank">
							<span  class="red">立即申请</span>
						</a>
						<a  href="../../../alipay/alipay.doc"  style="color:red"  target="_blank">
							<span  class="red">申请步骤</span>
						</a>
				</dl>-->
				<dl class="WSY_remind_dl02"> 
					<dt>商户号：</dt>
					<dd>						
						<input type="text" name="merchant_no" value="<?php echo $merchant_no; ?>">
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>终端号：</dt>
					<dd>						
						<input type="text" value="<?php  if(!empty($terminal_no)){echo substr_replace($terminal_no,"*****",2,5);} ?>" name="terminal_no" id="terminal_no" data-password >
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>商户密钥KEY：</dt>
					<dd>						
						<input type="text" value="<?php if(!empty($merchant_key)){echo substr_replace($merchant_key,"***************",3,20);} ?>" name="merchant_key" id="merchant_key" data-password>
					</dd>
				</dl>
			</div> 
		</form>
		<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;">
		</div>
	</div>
</div> 
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../Common/js/Base/pay_set/unionpay_set.js"></script>
</body>
</html>