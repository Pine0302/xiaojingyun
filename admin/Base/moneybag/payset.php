<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=0;//头部文件0商城资料，1分享设置,2购物设计

$isopen_poundage           = 0;     //是否开启零钱收续费
$isOpen_massage            = 0;     //是否开启支付密码短信充值开关

$query = "SELECT isopen_poundage,poundage_percentage,isOpen_massage FROM moneybag_rule where customer_id=".$customer_id." and isvalid=true LIMIT 1";
$result= _mysql_query($query);
while($row=mysql_fetch_object($result)){
	$isopen_poundage	= $row->isopen_poundage;
	$poundage_percentage	= $row->poundage_percentage;
	$isOpen_massage 	= $row->isOpen_massage;
	}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/base_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../Common/js/Base/basicdesign/layer.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>

<title>支付设置</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>
.distr_type_div i{margin-top:7px;}
.WSY_remind_dl02 .distr_type_div {height:35px;}
.cash_name{float:left;line-height:25px;margin-right:}
.cash_coefficient_dd{display:inline-block;margin-right:10px;margin-top:5px;}
.xuxiandiv{border: 2px dashed #999999;margin: 23px 23px 40px 23px;position: relative;padding-bottom: 20px;}
.shezhidiv{position: absolute;top: -18px;left: 32px;border: 2px solid #cccccc;background-color: #ffffff;padding: 6px 20px;}
.is_fee_input *{vertical-align: middle;}
.mb5{margin-bottom:5px;}
</style>
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/moneybag/basic_head.php"); 
		?>		
	<form action="save_moneybag_rule2.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="saveFrom" name="saveFrom">
		<input type="hidden" name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
		<div class="WSY_remind_main">
			<dl class="WSY_remind_dl02"> 
				<dt>零钱支付收取手续费：</dt>
				<dd>
					<?php if( $isopen_poundage == 1 ){ ?> 
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li class="WSY_bot"  onclick="set_isopen_poundage(0)" style="left: 0px;"></li>
							<span class="WSY_bot2" onclick="set_isopen_poundage(1)" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?> 
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li class="WSY_bot" onclick="set_isopen_poundage(0)" style="display: none; left: 30px;"></li>
							<span class="WSY_bot2" onclick="set_isopen_poundage(1)" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?> 
					<input type="hidden" name="isopen_poundage" id="isopen_poundage" value="<?php echo $isopen_poundage; ?>" />
					<input type="hidden" name="old_isopen_poundage" id="old_isopen_poundage" value="<?php echo $isopen_poundage; ?>" />	
					<input type="hidden" name="old_poundage_percentage" id="old_poundage_percentage" value="<?php echo $poundage_percentage; ?>" />	
				</dd>
				<dd style="margin-left:15px;float:left;">
					<a href="poundage_set_log.php?customer_id=<?php echo $customer_id_en?>&setting_id=1"><img style="width:20px;" title="查看修改日志" src="../../Common/images/Base/basicdesign/icon-log.png"></a>
				</dd>
			</dl>
			<dl class="WSY_remind_dl02" id="poundage_percentage_box" style="display:<?php if( $isopen_poundage == 1 ){ echo "block";}else{ echo "none";}?>"> 
				<dt>手续费比例：</dt>
				<dd>
					<input type="text" style="width:150px;" name="poundage_percentage" id="poundage_percentage" value="<?php echo $poundage_percentage;?>">%
				</dd>
			</dl>		
			<dl class="WSY_remind_dl02"> 
				<dt>支付密码短信重置：</dt>
				<dd>
					<?php if($isOpen_massage==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="isOpen_massage(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="isOpen_massage(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="isOpen_massage(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="isOpen_massage(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="isOpen_massage" id="isOpen_massage" value="<?php echo $isOpen_massage; ?>" />	
				</dd>
			</dl>			
		</div>
		</form>
			<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="return saveData(this);" style="cursor:pointer;">
		</div>
	</div>
</div> 
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script>
function saveData(){
	var isopen_poundage 	= $("#isopen_poundage").val();
	var poundage_percentage = $("#poundage_percentage").val();

		if( isopen_poundage == 1 ){
		if( poundage_percentage < 0 ){
			alert('支付手续费不能小于0');
			return;
		}else if( poundage_percentage > 100 ){
			alert('支付手续费不能大于1');
			return;
		}else if( poundage_percentage == '' ){
			alert('请输入支付手续费比例');
			return;
		}else if( isNaN(poundage_percentage) ){
			alert('支付手续费比例输入不合法');
			return;
		}
	}
		document.getElementById("saveFrom").submit();	
	return true ;
}

	function isOpen_massage(obj){
	$("#isOpen_massage").val(obj);
}
	function set_isopen_poundage(obj){
	$("#isopen_poundage").val(obj);
	if(obj==0){
		document.getElementById("poundage_percentage_box").style.display="none";
	}else{
		document.getElementById("poundage_percentage_box").style.display="";
	}
}
function hide(obj){//手续费、返购物币开关只能开一个动画
	if(obj==1){
		var action = "#is_fee_div .WSY_bot";
	}else{
		var action = "#is_curr_div .WSY_bot";
	}
		$(action).animate({left : '30px'});
		$(action).parent().find(".WSY_bot2").animate({left : '30px'});
		$(action).hide();
		$(action).parent().find(".WSY_bot2").show();
		$(action).parent().find("p").animate({margin : '0 0 0 13px'}, 500);
		
		$(action).parent().find("p").html('关');
		$(action).parent().css({backgroundColor : '#cbd2d8'});
		$(action).parent().find("p").css({color : '#7f8a97'});

}
</script>
</body>
</html>