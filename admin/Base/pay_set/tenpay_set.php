<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=3;//头部文件0支付方式，1微信支付,2支付宝,3财务通,4通联支付

$query = "select id,bussinessid,bussinesskey,type FROM tenpays where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());

$bussinessid = "";
$bussinesskey="";
$tenpay_id=-1;
$type = 1;
while ($row = mysql_fetch_object($result)) {
	$tenpay_id = $row->id;
	$bussinessid = $row->bussinessid;
	$bussinesskey = $row->bussinesskey;
	$type = $row->type;
	break;
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/pay_set/tenpay_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>


<title>财务通</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php"); 
		?>
		<form action="save_tenpay_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
		<input type=hidden name="tenpay_id" id="tenpay_id" value="<?php echo $tenpay_id; ?>" />
			<div class="WSY_remind_main">				
				<dl class="WSY_remind_dl02"> 
					<dt>财付通商户号：</dt>
					<dd>						
						<input class="text_input" value="<?php echo $bussinessid ?>" name="bussinessid" id="bussinessid">
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"  id="div_key"> 
					<dt>财付通密钥：</dt>
					<dd>						
						<input class="text_input" value="<?php if(!empty($bussinesskey)){echo substr_replace($bussinesskey,'*****',2,8);} ?>" name="bussinesskey" id="bussinesskey">
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>接口服务类型：</dt>
					<dd>
						 <select class="type" name="type">
							<option value=1 <?php if($type==1){?> selected <?php } ?>>即时到账交易</option>
							<option value=2 <?php if($type==2){?> selected <?php } ?>>中介担保交易</option>
						 </select>		 		
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
<script type="text/javascript" src="../../Common/js/Base/pay_set/tenpay_set.js"></script>

</body>
</html>