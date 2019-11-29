<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=9;//头部文件0支付方式，1微信支付,2支付宝,3财务通,4通联支付,5PayPal支付,8易宝支付

$query = "select id,customernumber,secret,private_pem,public_pem FROM jdpay where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());

while ($row = mysql_fetch_object($result)) {
	$jdpay_id = $row->id;
	$jdpay_customernumber = $row->customernumber;
	$jdpay_secret = $row->secret;
	$private_pem = $row->private_pem;
	$public_pem = $row->public_pem;
	break;
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/pay_set/paypal_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>


<title>京东支付</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php"); 
		?>
		<form action="save_jdpay_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="jdpay_id" id="jdpay_id" value="<?php echo $jdpay_id; ?>" />
			<div class="WSY_remind_main">				
				<dl class="WSY_remind_dl02"> 
					<dt>商户号：</dt>
					<dd>						
						<input class="text_input" value="<?php if(!empty($jdpay_customernumber )){echo substr_replace($jdpay_customernumber ,"*********************",2,20);}?>" name="jdpay_customernumber" id="jdpay_customernumber">
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>密钥：</dt>
					<dd>						
						<input class="text_input" value="<?php if(!empty($jdpay_secret)){echo substr_replace($jdpay_secret,"*********************",2,20);} ?>" name="jdpay_secret" id="jdpay_secret">
					</dd>
				</dl>
			</div>
			<div  class="WSY_commonbox02" id="div_refund">
				<div class="WSY_common02">
					<a>支付需要以下证书</a><!--每个设置项标题-->
				</div>
				<ul class="WSY_commonbox">
					<li>公钥证书</li>
					<!--上传文件代码开始-->
						<div class="uploader white" id="WSY_commondiv">
							<input type="text" class="filename"  value="<?php if($public_pem!=""){echo $public_pem;}else{echo "请选择文件...";} ?>" readonly/>
							<input type="button" name="file" class="button" value="上传..."/>
							<input type="file" name="public_pem" size="30"/>
							<input type=hidden name="public_pem_v" value="<?php echo $public_pem; ?>" />
						</div>
						<!--上传文件代码结束-->
					<span><?php echo '更新时间:'.$public_pem; ?></span>
				</ul>
				<ul class="WSY_commonbox">
					<li>私钥证书</li>
					<!--上传文件代码开始-->
						<div class="uploader white" id="WSY_commondiv">
							<input type="text" class="filename"  value="<?php if($private_pem!=""){echo $private_pem;}else{echo "请选择文件...";} ?>" readonly/>
							<input type="button" name="file" class="button" value="上传..."/>
							<input type="file" name="private_pem" size="30"/>
							<input type=hidden name="private_pem_v" value="<?php echo $private_pem; ?>" /> 
						</div> 
						<!--上传文件代码结束-->
					<span><?php echo '更新时间:'.$private_pem; ?></span>
				</ul>
			</div>			
		</form>
		<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;">
		</div>
	</div>
</div> 
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../Common/js/Base/pay_set/jdpay_set.js"></script>

</body>
</html>