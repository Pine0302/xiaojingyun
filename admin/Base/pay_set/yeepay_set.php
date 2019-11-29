<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=8;//头部文件0支付方式，1微信支付,2支付宝,3财务通,4通联支付,5PayPal支付,8易宝支付

$query = "select id,customernumber,secret FROM yeepay where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());

$mail='';
while ($row = mysql_fetch_object($result)) {
	$yeepay_id= $row->id;
	$yeepay_customernumber=$row->customernumber;
	$yeepay_secret=$row->secret;
	break;
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/pay_set/paypal_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>


<title>易宝支付</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php"); 
		?>
		<form action="save_yeepay_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="yeepay_id" id="yeepay_id" value="<?php echo $yeepay_id; ?>" />
			<div class="WSY_remind_main">				
				<dl class="WSY_remind_dl02"> 
					<dt>商户号：</dt>
					<dd>						
						<input class="text_input" value="<?php echo $yeepay_customernumber ?>" name="yeepay_customernumber" id="yeepay_customernumber">
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>密钥：</dt>
					<dd>						
						<input class="text_input" value="<?php echo $yeepay_secret ?>" name="yeepay_secret" id="yeepay_secret">
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
<script type="text/javascript" src="../../Common/js/Base/pay_set/yeepay_set.js"></script>

</body>
</html>