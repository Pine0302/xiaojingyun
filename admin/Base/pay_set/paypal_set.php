<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=5;//头部文件0支付方式，1微信支付,2支付宝,3财务通,4通联支付,5PayPal支付

$query = "select id,mail FROM paypalpay where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());

$mail='';
while ($row = mysql_fetch_object($result)) {
	$paypalpay_id = $row->id;
	$mail=$row->mail;
	break;
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/pay_set/paypal_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>


<title>PayPal支付</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php"); 
		?>
		<form action="save_paypal_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="paypalpay_id" id="paypalpay_id" value="<?php echo $paypalpay_id; ?>" />
			<div class="WSY_remind_main">				
				<dl class="WSY_remind_dl02"> 
					<dt>PayPal账户：</dt>
					<dd>						
						<input class="text_input" value="<?php echo $mail ?>" name="mail" id="mail" maxlength='255'>
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
<script type="text/javascript" src="../../Common/js/Base/pay_set/paypal_set.js"></script>

</body>
</html>