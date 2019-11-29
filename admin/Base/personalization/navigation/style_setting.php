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

$head 	= '1';
$type 	= '-1';
$id 	= -1;
$query = "SELECT id,type from navigation_style_setting_t where customer_id=".$customer_id." and isvalid= true limit 1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());;
while ($row = mysql_fetch_object($result)){
	$type 	= $row->type;
	$id 	= $row->id;
}


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>导航样式设置</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/personal_center/personal_center.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/mall_setting/setting.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>
<script>
	
</script>


</head>
<style>
.WSY_stylebox a{display:block;text-align:center;font-size:16px;font-family:'微软雅黑';line-height:22px;background-color:#f2f2f2;color:#323232}
a.hover-blue:hover{background-color:#06a7e1;}
a.cur{background-color:#06a7e1;}
</style>
<body>

	<div class="WSY_content">
		<div class="WSY_columnbox">
		<?php
			include("../../../../../weixinpl/back_newshops/Base/personalization/navigation/head.php"); 
		?>		
				<div class="WSY_data">
					<div class="WSY_list" id="WSY_list" >

						<ul class="WSY_righticon">
	                        <li><a href="../home_decoration/defaultset.php?customer_id=<?php echo $customer_id_en; ?>">返回</a></li>
	                    </ul>
	                    <br class="WSY_clearfloat">

						<div class="WSY_stylebox" id="skin">
							<a class="common_template <?php if($type==1){?>cur<?php } ?>" type="1" onclick="confirm('您确定要选择此种风格吗？')?location.href='./navigation_style_edit.php?type=1&customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>':''" href="javascript:;">
									<div class="item" type="1" title="点击选择导航风格">
										<div class="img">
											<img src="../images/daohang7.jpg">
										</div>
										<div class="title" id="bian">风格1(建议图标尺寸:160px*160px)</div>
									</div>
							</a>
							<a class="common_template <?php if($type==2){?>cur<?php } ?>" type="2" onclick="confirm('您确定要选择此种风格吗？')?location.href='./navigation_style_edit.php?type=2&customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>':''" href="javascript:;">
									<div class="item" type="2" title="点击选择导航风格">
										<div class="img">
											<img src="../images/daohang2.jpg">
										</div>
										<div class="title">风格2(建议图标尺寸:160px*160px)</div>
									</div>
							</a>
							<a class="common_template <?php if($type==3){?>cur<?php } ?>" type="3" onclick="confirm('您确定要选择此种风格吗？')?location.href='./navigation_style_edit.php?type=3&customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>':''" href="javascript:;">
									<div class="item" type="3" title="点击选择导航风格">
										<div class="img">
											<img src="../images/daohang3.jpg">
										</div>
										<div class="title">风格3(建议图标尺寸:120px*160px)</div>
									</div>
							</a>
							<a class="common_template <?php if($type==4){?>cur<?php } ?>" type="4" onclick="confirm('您确定要选择此种风格吗？')?location.href='./navigation_style_edit.php?type=4&customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>':''" href="javascript:;">
									<div class="item" type="4" title="点击选择导航风格">
										<div class="img">
											<img src="../images/daohang4.jpg">
										</div>
										<div class="title">风格4(建议图标尺寸:320px*120px)</div>
									</div>
							</a>
							<a class="common_template <?php if($type==5){?>cur<?php } ?>" type="5" onclick="confirm('您确定要选择此种风格吗？')?location.href='./navigation_style_edit.php?type=5&customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>':''" href="javascript:;">
									<div class="item" type="5" title="点击选择导航风格">
										<div class="img">
											<img src="../images/daohang5.jpg">
										</div>
										<div class="title">风格5(建议图标尺寸:160px*160px)</div>
									</div>
							</a>
							<a class="common_template <?php if($type==6){?>cur<?php } ?>" type="6" onclick="confirm('您确定要选择此种风格吗？')?location.href='./navigation_style_edit.php?type=6&customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>':''" href="javascript:;">
									<div class="item" type="6" title="点击选择导航风格">
										<div class="img">
											<img src="../images/daohang6.jpg">
										</div>
										<div class="title">风格6(建议图标尺寸:160px*160px)</div>
									</div>
							</a>
						</div>
					</div>
				</div>
		</div>
	</div>
<script type="text/javascript" src="../../../Common/js/Base/mall_setting/ToolTip.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../../../common/js/layer/V2_1/layer.js"></script>
<script>
	$(function(){
		$(".common_template").hover(function(){
			$(this).addClass("hover-blue");
		},function(){
			$(this).removeClass("hover-blue");
		})
	});
</script>
</body>
</html>