<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);  //解密
require('../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../weixinpl/proxy_info.php');
$head= 0 ;//头部文件0礼包

$query = "select id,reward_level,is_ncomission,exp_name,is_shareholder,is_team,isOpenAgent,isOpenSupply from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$keyid		 	= -1;
$reward_level 	= 3;
$is_ncomission 	= 0;
$exp_name		= "推广员";
$is_shareholder	= 0;//是否在个人中心开启代理商申请
$is_team		= 0;//是否开启区域奖励
$isOpenAgent	= 0;//是否在个人中心开启代理商申请
$isOpenSupply	= 0;//是否在个人中心开启供应商申请
$result = _mysql_query($query) or die('W21 Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$reward_level 	= $row -> reward_level;
	$keyid		  	= $row -> id;
	$is_ncomission	= $row -> is_ncomission;
	$exp_name		= $row -> exp_name;
	$is_shareholder	= $row -> is_shareholder;
	$is_team		= $row -> is_team;
	$isOpenAgent	= $row -> isOpenAgent;
	$isOpenSupply	= $row -> isOpenSupply;
	
}
$permanent_code = "";
$query = "select permanent_code from weixin_commonshops_extend  where shop_id=".$keyid;
$result = _mysql_query($query) or die('W21 Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$permanent_code 	= $row -> permanent_code;
}
/* echo "++".$permanent_code = substr($permanent_code,0,-1);
$permanentarr 	= explode(",", $permanent_code);
echo "permanent=".$permanent		= count($permanentarr); */
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>二维码生成条件</title>
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script charset="utf-8" src="../../common/js/jquery.jsonp-2.2.0.js"></script>
</head>
<style>
.WSY_remind_labelbox {
	overflow: hidden;
	margin-bottom: 10px;
}
</style>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<?php
				include("../../../weixinpl/back_newshops/Diy/head.php"); 
			?>
			<form id="upform" class="upform" method="post" action="save_qr_code.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data">
			<input type=hidden name="keyid" id="keyid" value="<?php echo $keyid; ?>" />
			<input type=hidden name="is_ncomission" id="is_ncomission" value="<?php echo $is_ncomission; ?>" />
			<input type=hidden name="is_shareholder" id="is_shareholder" value="<?php echo $is_shareholder; ?>" />
			<input type=hidden name="is_team" id="is_team" value="<?php echo $is_team; ?>" />
			<input type=hidden name="isOpenAgent" id="isOpenAgent" value="<?php echo $isOpenAgent; ?>" />
			<input type=hidden name="isOpenSupply" id="isOpenSupply" value="<?php echo $isOpenSupply; ?>" />
			
				<div class="WSY_data WSY_remind_main">
					<dl class="WSY_remind_dl02"> 
						<dt>推广员永久二维码：<span style="color:red;">(不勾选则为临时二维码,时效30天)</span></dt>
						<dd style="overflow:hidden;margin-top:5px;">
							<?php
							$exp_name_3 = ""; //3*3等级推广员自定义名称 
							$level 		= 1; 
							$i			= 0;
							if( $is_ncomission ){
								$query_commisions="select exp_name,level from weixin_commonshop_commisions where isvalid=true and customer_id=".$customer_id." and level<=".$reward_level;
								$result_commisions = _mysql_query($query_commisions) or die('w94 Query failed: ' . mysql_error());
								
								while ($row = mysql_fetch_object($result_commisions)) {
									$i++;
									$exp_name_3 = $row->exp_name;
									$level 		= $row->level;
							?>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"P_".$level ) ){?> checked <?php } ?> name="P_<?php echo $level?>" >
									<?php echo $exp_name_3 ?>
								</label>
							</div>
							<?php 
									
								}
							}else{
							?>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"P_1" ) ){?> checked <?php } ?> name="P_1" >
									<?php echo $exp_name ?>
								</label>
							</div>
						<?php 
							}
						if( $isOpenAgent ){
						?>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"D_1" ) ){?> checked <?php } ?> name="D_1" >
									代理商
								</label>
							</div>
						<?php
						}
						if( $isOpenSupply ){
						?>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"Y_1" ) ){?> checked <?php } ?> name="Y_1" >
									供应商
								</label>
							</div>
							<?php
						}
							if( $is_shareholder ){
								$QUERY_BASE = "SELECT a_name,b_name,c_name,d_name from weixin_commonshop_shareholder WHERE isvalid = true and customer_id = ".$customer_id." limit 0,1";
								$RESULT_BASE = _mysql_query($QUERY_BASE) or die (" Wrong_1 : QUERY ERROR : ".mysql_error());
								$a_name 		 = "白金";	
								$b_name			 = "黄金";
								$c_name			 = "白银";
								$d_name			 = "青铜";
								while ($row = mysql_fetch_object($RESULT_BASE)) {
									$a_name = $row->a_name;
									$b_name = $row->b_name;
									$c_name = $row->c_name;
									$d_name = $row->d_name;
								}
							?>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"G_1" ) ){?> checked <?php } ?> name="G_1" >
									<?php echo $a_name; ?>
								</label>
							</div>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"G_2" ) ){?> checked <?php } ?> name="G_2" >
									<?php echo $b_name; ?>
								</label>
							</div>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"G_3" ) ){?> checked <?php } ?> name="G_3" >
									<?php echo $c_name; ?>
								</label>
							</div>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"G_4" ) ){?> checked <?php } ?> name="G_4" >
									<?php echo $d_name; ?>
								</label>
							</div>
							<?php
							}
							if( $is_team ){
								$QUERY_BASE = "SELECT p_customer,c_customer,a_customer,is_diy_area,diy_customer from weixin_commonshop_team WHERE isvalid = true and customer_id = ".$customer_id." limit 0,1";
								$RESULT_BASE = _mysql_query($QUERY_BASE) or die (" Wrong_1 : QUERY ERROR : ".mysql_error());
								$p_customer		= "省代";	
								$c_customer		= "市代";
								$a_customer		= "区代";
								$is_diy_area	= 0;//开启自定义区域
								$diy_customer	= "";
								while ($row = mysql_fetch_object($RESULT_BASE)) {
									$p_customer = $row->p_customer;
									$c_customer = $row->c_customer;
									$a_customer = $row->a_customer;
									$is_diy_area = $row->is_diy_area;
									$diy_customer = $row->diy_customer;
								}
							?>
							<input type=hidden name="is_diy_area" id="is_diy_area" value="<?php echo $is_diy_area; ?>" />
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"Q_1" ) ){?> checked <?php } ?>  name="Q_1" >
									<?php echo $p_customer; ?>
								</label>
							</div>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"Q_2" ) ){?> checked <?php } ?> name="Q_2" >
									<?php echo $c_customer; ?>
								</label>
							</div>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"Q_3" ) ){?> checked <?php } ?> name="Q_3" >
									<?php echo $a_customer; ?>
								</label>
							</div>
							<?php
							if( $is_diy_area ){
							?>
							<div class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($permanent_code,"Q_4" ) ){?> checked <?php } ?> name="Q_4" >
									<?php echo $diy_customer; ?>
								</label>
							</div>
							<?php 
							}
							}
							?>
						</dd>
						<input type=hidden name="P_num" id="P_num" value="<?php echo $i; ?>" />
					</dl>
			
				</div>	
				<div class="WSY_text_input01">
					<div class="WSY_text_input">
						<button class="WSY_button" id="btnSave" type="button" onclick="saveBase()">提交保存</button>
					</div>
				</div>
			</form>
		</div>		
	</div>
<script>
function saveBase(){
	document.getElementById("upform").submit();
}
</script>
<script type="text/javascript" src="../../common/js_V6.0/content.js"></script>
</body>
</html>
