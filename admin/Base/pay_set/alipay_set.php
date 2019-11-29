<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=2;//头部文件0支付方式，1微信支付,2支付宝,3财务通,4通联支付

$query = "select id,account,pid,akey from alipays where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$account = "";
$pid="";
$akey="";
$alipay_id = -1;
while ($row = mysql_fetch_object($result)) {
	$account = $row->account;
	$alipay_id = $row->id;
	$akey = $row->akey;
	$pid = $row->pid;
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/pay_set/alipay_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>


<title>支付宝</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php");  
		?>
		<form action="save_alipay_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="alipay_id" value="<?php echo $alipay_id ?>" />
			<div class="WSY_remind_main">
				<dl class="WSY_remind_dl02"> 
					<dt>教程：</dt>
					<dd class="course_dd ">						
						<span style="font-size:14px;line-height:24px;;color:#6b6969">开启支付后，商城等b2c功能将有支付功能</span>
						 <a  href="https://b.alipay.com/order/productDetail.htm?productId=2013080604609688" style="color:red"  target="_blank">
							<span  class="red">立即申请</span>
						</a>
						<a  href="../../../alipay/alipay.doc"  style="color:red"  target="_blank">
							<span  class="red">申请步骤</span>
						</a>
						<a  href="../../../alipay/alipay.pdf"  style="color:red"  target="_blank">
							<span  class="red">WEB支付宝申请步骤</span>
						</a>
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>支付宝账户：</dt>
					<dd>						
						<input type="text" name="account" value="<?php echo $account; ?>">
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>支付宝PID：</dt>
					<dd>						
						<input type="text" value="<?php  if(!empty($pid)){echo substr_replace($pid,"***************",2,10);} ?>" name="pid" id="pid" data-password >
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>支付宝KEY：</dt>
					<dd>						
						<input type="text" value="<?php if(!empty($akey)){echo substr_replace($akey,"***************",2,10);} ?>" name="akey" id="akey" data-password>
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
<script type="text/javascript" src="../../Common/js/Base/pay_set/alipay_set.js"></script>
</body>
</html>