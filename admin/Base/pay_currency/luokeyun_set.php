<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

$currency_head = 3;
$sql  = "SELECT is_open,luokeyun_key FROM currency_luokeyun_set WHERE isvalid=true and customer_id=".$customer_id;
$res  = _mysql_query($sql);
$is_open 		= 0;
$key 		= '';
while ($row = mysql_fetch_object($res) ){
	$is_open = $row->is_open;
	$key 	 = $row->luokeyun_key;
}


?>
<html>
<head>
<style type="text/css">
.WSY_remind_main{overflow:hidden;}
.divfloat{display:block;float:left;width:250px;}
.submit_div{float:left;margin-left:10%;}
</style>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/pay_set/pay_switch.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>

<style type="text/css">
	dd{
		float: left;
	}
	dt{
		float: left;
	}
	dl{
		float: left;
		margin-left: 20px;
	}
	#set_currency{
		width:500px;
	}

</style>
<title>提现至洛克云平台</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			//include("../../../../weixinpl/back_newshops/Base/pay_currency/pay_head.php"); 
			include("../../../../weixinpl/back_newshops/Base/pay_currency/currency_head.php"); 
		?>
		<form action="save_luokeyun.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
			<div class="WSY_remind_main">
				<div class="divfloat">
				
				<dl class="WSY_remind_dl02" id="set_currency" > 
					<dt><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>提现至洛克云平台：</dt>
					<dd>
						<?php if($is_open==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="change_is_open(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_is_open(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="change_is_open(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_is_open(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
						<?php } ?>
					</dd>
					<input type="hidden" name="is_open" id="is_open" value="<?php echo $is_open; ?>" />
				</dl>

				<dl class="WSY_remind_dl02" id="set_currency" > 
					<dt style="line-height: 30px;">洛克云对接密钥：</dt>
					<dd>
						<input type="text" id="key" name="key" value="<?php echo $key; ?>" style="width:300px;height:30px;border:1px solid #ccc;border-radius: 3px;">
					</dd>
				</dl>

				</div>
				
				
			</div> 
		</form>
		<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;">
			<input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);"/>
		</div>
	</div>
</div> 
<script type="text/javascript" src="../../Common/js/Base/basicdesign/ToolTip.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script>
function change_is_open(obj){
	$("#is_open").val(obj);//购物币是否参与分佣
}
function submitV(){
	$('#upform').submit();
}
</script>
</body>
</html>