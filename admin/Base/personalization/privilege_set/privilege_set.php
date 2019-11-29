<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');		
require('../../../../../weixinpl/proxy_info.php');
require('../../../../../weixinpl/auth_user.php');
_mysql_query("SET NAMES UTF8");		

$head=9;

$query = "select id from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$shop_id = $row->id;
}

$upgrade_mode = 1;
$query = "select upgrade_mode from weixin_commonshops_extend where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$upgrade_mode = $row->upgrade_mode;
} 


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>特权设置</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/personal_center/personal_center.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/mall_setting/setting.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>

<style>
label{width:100%}
.black{width:60px;height:20px;background:#1c1f20;}
.red{width:60px;height:20px;background:#ec2935;}
.blue{width:60px;height:20px;background:#5d9cec;}
.green{width:60px;height:20px;background:#34ad8f;}
.purple {width:60px;height:20px;background:#987cc9 ;}
.orange {width:60px;height:20px;background:#f93c42 ;}
.picture img{width:250px;margin-top:-345px;float:left;margin-left: 100px;}
.cond{margin-top: -15px;}
.WSY_list{width:100%}
.WSY_member input[type="radio"] {
     display: inline-block;
     float: none;
}
</style>
<script>
 function submitV(a){
	 document.getElementById("upform").submit();	
 }	
</script>
</head>
	
<body>
<form id="upform" action="save_privilege.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">
   <input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
	<div class="WSY_content">
		<div class="WSY_columnbox">
		<?php
			include("../../../../../weixinpl/back_newshops/Base/personalization/basic_head.php"); 
		?>		
		<div class="WSY_data">
              <div class="WSY_list" id="WSY_list" style="min-height: 500px;">
				
				<div class="WSY_member " style="height:auto;">
				<form id="frm_producttype" class="" method="post" enctype="multipart/form-data">
					
					<dl class="WSY_remind_dl02" style="width:500px !important;"> 
						<dt  style="line-height:20px;font-weight:normal;margin-left:28px;" >特权显示模式 ：</dt>
							<label for="upgrade_mode_one" style="margin: 0 5px;"><input type="radio" name="upgrade_mode" id="upgrade_mode_one" value="1" <?php if( $upgrade_mode == 1 ){?>checked<?php }?> style="margin-right: 5px;"><span>模式一</span></label>
							<label for="upgrade_mode_two" style="margin: 0 5px;"><input type="radio" name="upgrade_mode" id="upgrade_mode_two" value="2" <?php if( $upgrade_mode == 2 ){?>checked<?php }?> style="margin-right: 5px;"><span>模式二</span></label>
						
					</dl>
						
				<div style="clear:both"></div>
							   
				  </form>
				</div>
				<div class="WSY_text_input01"  style="margin-left:20%;">
					<div class="WSY_text_input" style=""><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
					<div class="WSY_text_input" style=""><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
				</div>				
			</div>
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	</div>
</form>	
<script type="text/javascript" src="../../../Common/js/Base/mall_setting/ToolTip.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../../../common/js/layer/V2_1/layer.js"></script>
</body>
</html>