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

$head=7;

$query = "select id from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$shop_id = $row->id;
} 

$custom_skin = -1;//商城前端自定义皮肤颜色，1：橘红色，2：红色，3：蓝色，4：绿色，5：黑色，6：紫色
$query = "select custom_skin from weixin_commonshops_extend where isvalid=true and customer_id=".$customer_id." and shop_id=".$shop_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$custom_skin = $row->custom_skin;
}
$skin="";
switch($custom_skin){
	case 1:
		$skin = "orange";
	break;
	case 2:
		$skin = "red";
	break;
	case 3:
		$skin = "blue";
	break;
	case 4:
		$skin = "green";
	break;
	case 5:
		$skin = "black";
	break;
	case 6:
		$skin = "purple";
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>皮肤设置</title>
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
.orange {width:60px;height:20px;background:#fe7c24 ;}
.picture img{width:250px;margin-top:-345px;float:left;margin-left: 100px;}
.cond{margin-top: -15px;}
.WSY_list{width:100%}
</style>
<script>
 function submitV(a){
	 document.getElementById("upform").submit();	
 }	
 $(document).ready(function() {
				$(".cond").change(function() {
					var $selectedvalue = $("input[name='sendstyle2']:checked").val();
					if ($selectedvalue == 1) {
						$('.picture').html('<img src=./images/orange.jpg>');
					} else if($selectedvalue == 2) {
						$('.picture').html('<img src=./images/red.jpg>');
					}else if($selectedvalue == 3) {
						$('.picture').html('<img src=./images/blue.jpg>');
					}else if($selectedvalue == 4) {
						$('.picture').html('<img src=./images/green.jpg>');
					}else if($selectedvalue == 5) {
						$('.picture').html('<img src=./images/black.jpg>');
					}else if($selectedvalue == 6) {
						$('.picture').html('<img src=./images/purple.jpg>');
					}
				});
			});
		</script>
</script>
</head>
	
<body>
<form id="upform" action="save_setting.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">
   <input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
	<div class="WSY_content">
		<div class="WSY_columnbox">
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/personalization/basic_head.php"); 
		?>		
		<div class="WSY_data">
              <div class="WSY_list" id="WSY_list" style="min-height: 500px;">
				
				<div class="WSY_member " style="height:auto;">
				<form id="frm_producttype" class="" method="post" enctype="multipart/form-data">
					
						<label style="float:left;"><div class="cond"><input type="radio" name="sendstyle2" <?php if($custom_skin==1){ ?>checked=true<?php } ?> value=1><div class="orange" style="margin-left: 20px;"></div><span style="font-size:15px;">橙色</span></div>
						</label><!-- 风格1 -->
						
						<label style="float:left;"><div class="cond"><input type="radio" name="sendstyle2" <?php if($custom_skin==2){ ?>checked=true<?php } ?> value=2><div class="red" style="margin-left: 20px;"></div><span style="font-size:15px;">红色</span></div>
						</label><!-- 风格2 -->
						
						<label style="float:left;"><div class="cond"><input type="radio" name="sendstyle2" <?php if($custom_skin==3){ ?>checked=true<?php } ?> value=3><div class="blue" style="margin-left: 20px;"></div><span style="font-size:15px;">蓝色</span></div>
						</label><!-- 风格3 -->
						
						<label style="float:left;"><div class="cond"><input type="radio" name="sendstyle2" <?php if($custom_skin==4){ ?>checked=true<?php } ?> value=4><div class="green" style="margin-left: 20px;"></div><span style="font-size:15px;">绿色</span></div>
						</label><!-- 风格4 -->
						
						<label style="float:left;"><div class="cond"><input type="radio" name="sendstyle2" <?php if($custom_skin==5){ ?>checked=true<?php } ?> value=5><div class="black" style="margin-left: 20px;"></div><span style="font-size:15px;">黑色</span></div>
						</label><!-- 风格5 -->
						
						<label style="float:left;"><div class="cond"><input type="radio" name="sendstyle2" <?php if($custom_skin==6){ ?>checked=true<?php } ?> value=6><div class="purple " style="margin-left: 20px;"></div><span style="font-size:15px;">紫色</span></div>
						</label><!-- 风格6 -->
						
						
					    <div style="clear:both"></div>
							   
				  </form>
				</div>
				<div style="width:30%;margin-left:10%;">
					<div class="picture" style="height:auto;">
						<img src="./images/<?php echo $skin; ?>.jpg">
					</div>
				</div><!-- 预览 -->	
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