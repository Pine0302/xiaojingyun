<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');		
require('../../../../weixinpl/proxy_info.php');
require('../../../../weixinpl/auth_user.php');
_mysql_query("SET NAMES UTF8");		
$head=0;

$pidt_id 	   = -1;
$is_phone 	   = 1;		//是否显示电话号码：0不显示，1显示
$is_qq 		   = 1;		//是否显示qq：0不显示，1显示
$is_weixin 	   = 1;		//是否显示微信号：0不显示，1显示
$is_weixincode = 1;		//是否显示微信二维码：0不显示，1显示
$query_pidt = "select id,is_phone,is_qq,is_weixin,is_weixincode from personal_info_display_t where isvalid=true and customer_id=".$customer_id." limit 1";
$result_pidt = _mysql_query($query_pidt) or die('query_pidt failed:'.mysql_error());
while($row_pidt = mysql_fetch_object($result_pidt)){
	$pidt_id 	   = $row_pidt->id;
	$is_phone 	   = $row_pidt->is_phone;
	$is_qq 		   = $row_pidt->is_qq;
	$is_weixin 	   = $row_pidt->is_weixin;
	$is_weixincode = $row_pidt->is_weixincode;
}
if($pidt_id<0){		//初次使用自动插入一条数据
	$query_insert = "insert into personal_info_display_t(customer_id,is_phone,is_qq,is_weixin,is_weixincode,isvalid,createtime) values(".$customer_id.",1,1,1,1,true,now())";
	_mysql_query($query_insert) or die('$query_insert failed:'.mysql_error());
	$pidt_id = mysql_insert_id();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>团队个人信息显示开关</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/personal_center/personal_center.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/mall_setting/setting.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<script>

function change_is_phone(a){
	$('#is_phone').val(a);
}
function change_is_qq(a){	
	$('#is_qq').val(a);
}
function change_is_weixin(a){	
	$('#is_weixin').val(a);
}
function change_is_weixincode(a){	
	$('#is_weixincode').val(a);
}

 function submitV(a){
	 document.getElementById("upform").submit();	
 }	
</script>
</head>
	
<body>
<form id="upform" action="save_personal_info_display.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="pidt_id" value="<?php echo $pidt_id;?>" />
	<div class="WSY_content">
		<div class="WSY_columnbox">

		<?php
			// include("../../../../weixinpl/back_newshops/Diy/personal_info_display/head.php");
			include($_SERVER['DOCUMENT_ROOT'].'/mshop/admin/Diy/personal_info_display/head.php'); 
		?>		
		<div class="WSY_data">
				<div class="WSY_remind_main" style="width:600px;margin-left:5%;">	
					<dl class="WSY_remind_dl02">
					<dt>显示电话号码：</dt>
						 <dd>
							<?php if($is_phone==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_phone(0)" class="WSY_bot" id="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_phone(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_phone(0)" class="WSY_bot"  id="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_phone(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>			
					</dl>
					
					<dl class="WSY_remind_dl02">
					<dt>显示QQ号：</dt>
						 <dd>
							<?php if($is_qq==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_qq(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_qq(1)" class="WSY_bot2" id="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_qq(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_qq(1)" class="WSY_bot2" id="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						
					</dl>
					
					<dl class="WSY_remind_dl02">
					<dt>显示微信号：</dt>
						 <dd>
							<?php if($is_weixin==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_weixin(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_weixin(1)" class="WSY_bot2" id="WSY_bot3" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_weixin(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_weixin(1)" class="WSY_bot2" id="WSY_bot3" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						
					</dl>
					
					<dl class="WSY_remind_dl02">
					<dt>显示微信二维码：</dt>
						 <dd>
							<?php if($is_weixincode==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_weixincode(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_weixincode(1)" class="WSY_bot2" id="WSY_bot4" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_weixincode(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_weixincode(1)" class="WSY_bot2" id="WSY_bot4" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						
					</dl>
									
					<input type="hidden" name="is_phone" id="is_phone" value="<?php echo $is_phone; ?>" />
					<input type="hidden" name="is_qq" id="is_qq" value="<?php echo $is_qq; ?>" />
					<input type="hidden" name="is_weixin" id="is_weixin" value="<?php echo $is_weixin; ?>" />
					<input type="hidden" name="is_weixincode" id="is_weixincode" value="<?php echo $is_weixincode; ?>" />
					<div style="clear:both"></div>
				</div>
				</form>
				<div class="WSY_text_input01" style="margin-left: 22%;">
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
					<!--<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>-->
				</div>			
			
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	</div>
</form>	
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>