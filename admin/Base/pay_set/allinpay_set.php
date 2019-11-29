<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=4;//头部文件0支付方式，1微信支付,2支付宝,3财务通,4通联支付

$query = "select id,appkey,pwd,vendor_id,version FROM allinpays where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());

$allinpay_id=-1;
$appkey="";
$pwd="";
$vendor_id="";
$version=1;
while ($row = mysql_fetch_object($result)) {
	$allinpay_id = $row->id;
	$appkey = $row->appkey;
	$pwd = $row->pwd;
	$vendor_id = $row->vendor_id;
	$version = $row->version;
	break;
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/pay_set/allinpay_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>


<title>通联支付</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php"); 
		?>
		<form action="save_allinpay_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="allinpay_id" id="allinpay_id" value="<?php echo $allinpay_id; ?>" />
			<div class="WSY_remind_main">
				<dl class="WSY_remind_dl02"> 
					<dt>通联支付配置：</dt>
					<dd style="margin-left:10px;line-height: 24px;">
						 <a href="http://aihmc.allinpay.com" style="color:#0088cc;font-size:15px;" target=_blank>通联商家账户查询</a>
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>版本类型：</dt>
					<dd>
						 <select class="version" name="version" onchange="selPayType(this.value);">
							<option value=1 <?php if($version==1){?> selected <?php } ?>>老版本</option>
							<option value=2 <?php if($version==2){?> selected <?php } ?>>新版本</option>
						 </select>		 		
					</dd>
				</dl>	
				<dl class="WSY_remind_dl02"> 
					<dt>商户号：</dt>
					<dd>						
						<input class="text_input" id="vendor_id" type="text" name="vendor_id" value="<?php echo $vendor_id; ?>">
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"  id="div_key"> 
					<dt>商户KEY：</dt>
					<dd>						
						<input class="text_input" id="appkey" type="text" name="appkey" value="<?php echo $appkey; ?>">
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
<script type="text/javascript" src="../../Common/js/Base/pay_set/allinpay_set.js"></script>
<script>
selPayType(<?php echo $version; ?>);
</script>
</body>
</html>