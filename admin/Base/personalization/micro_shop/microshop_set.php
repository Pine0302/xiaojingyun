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
$detail_template_type = 0;
$head=3;

$is_microshop		= 0; //是否开启微店
$isOpenMicroshopAd	= 0; //是否开启微店广告图
$microshop_adimg	= "";//微店广告图
$foreign_id			= -1; //固定链接ID
$detail_id			= -1; //产品ID
$link_type			= "";//链接类型
$id					= -1;
$is_microshopData	= 0;//微店数据统计
$is_mandatoryAD	= 0;//微店强制显示广告开关
$adimg 				= array();
$pc_adimg_info      = array();
$detail_id_array 	= array();
$foreign_id_array	= array();
$query="select id,isOpenMicroshopAd,microshop_adimg,foreign_id,detail_id,link_type,is_microshopData,is_microshop,is_mandatoryAD,pc_shop_adimg from weixin_commonshop_customer_microshop where customer_id=".$customer_id." limit 1";
$result=_mysql_query($query) or die ('query faild' .mysql_error());
while($row=mysql_fetch_object($result)){
	$isOpenMicroshopAd	= $row->isOpenMicroshopAd;
	$microshop_adimg	= $row->microshop_adimg;
	$foreign_id			= $row->foreign_id;
	$detail_id			= $row->detail_id;
	$link_type			= $row->link_type;  
	$id					= $row->id;
	$is_microshopData	= $row->is_microshopData;
	$is_microshop		= $row->is_microshop;
	$is_mandatoryAD		= $row->is_mandatoryAD;
	$pc_shop_adimg      = $row->pc_shop_adimg;
}
$adimg 				= explode("|",$microshop_adimg);
$detail_id_array 	= explode("|",$detail_id);
$foreign_id_array 	= explode("|",$foreign_id);
$link_type_array 	= explode("|",$link_type);

//pc端广告图把图片，将json格式变成数组

$pc_adimg_info =  json_decode($pc_shop_adimg,true);

foreach ($pc_adimg_info as $k => $v){
$pc_foreign_id_array[]	= $pc_adimg_info[$k]['foreign_id'];
$pc_adimg[] = $pc_adimg_info[$k]['microshop_adimg'];
$pc_detail_id_array[]	= $pc_adimg_info[$k]['detail_id'];
$pc_link_type_array[]	= $pc_adimg_info[$k]['link_type'];
}

//分类链接
$typearr=[];
$tuwenarr=[];
$query="select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $pt_id 		= $row->id;
   $pt_name 	= $row->name;
   $typearr[] 	= $pt_id."_".$pt_name;
}

//查询开启权限的开关等信息
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

$wce_id 						= -1;//extend记录ID
$microshop_open_permissions_code 	= "";//勾选允许开启微店权限
$microshop_give_identity 		= "";//勾选开启微店赠送身份
$query = "select id,microshop_open_permissions_code,microshop_give_identity from weixin_commonshops_extend  where isvalid=true and shop_id=".$keyid;
$result = _mysql_query($query) or die('W21 Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$wce_id 		               = $row -> id;
	$microshop_open_permissions_code = $row -> microshop_open_permissions_code;
	$microshop_give_identity        = $row -> microshop_give_identity;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>微店设置</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/personal_center/personal_center.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/mall_setting/setting.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>
<script>

function change_is_microshop(a){
	$('#is_microshop').val(a);
}
function change_isOpenMicroshopAd(a){
	$('#isOpenMicroshopAd').val(a);
}
function change_is_microshopData(a){
	$('#is_microshopData').val(a);
}
function change_is_mandatoryAD(a){
	$('#is_mandatoryAD').val(a);
}

 function submitV(a){
	 document.getElementById("upform").submit();	
 }
$(document).ready(function(){ 
	setselect();
	<?php if($PC_SHOP){ ?>
	pc_setselect();
	<?php } ?>
});	
</script>
</head>
	
<body>
<form id="upform" action="save_microshop.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">

	<div class="WSY_content">
		<div class="WSY_columnbox">

		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/personalization/basic_head.php");
		?>		
		<div class="WSY_data">

              <div class="WSY_list" id="WSY_list" style="min-height: 500px;">
				<div class="WSY_remind_main" style="height:400px;">	
					<dl class="WSY_remind_dl02">
					<dt>开启微店:</dt>
						 <dd>
							<?php if($is_microshop==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_microshop(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_microshop(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_microshop(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_microshop(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>					
					</dl>
					
					<dl class="WSY_remind_dl02">
					<dt>开启微店广告图:</dt>
						 <dd>
							<?php if($isOpenMicroshopAd==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_isOpenMicroshopAd(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_isOpenMicroshopAd(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_isOpenMicroshopAd(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_isOpenMicroshopAd(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						
					</dl>
					
					<dl class="WSY_remind_dl02">
					<dt>开启微店数据统计:</dt>
						 <dd>
							<?php if($is_microshopData==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_microshopData(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_microshopData(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_microshopData(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_microshopData(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>					
					</dl>
					
					<dl class="WSY_remind_dl02">
					<dt>微店强制显示广告开关:</dt>
						 <dd>
							<?php if($is_mandatoryAD==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_mandatoryAD(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_mandatoryAD(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_mandatoryAD(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_mandatoryAD(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>					
					</dl>
					
					<dl class="WSY_remind_dl02" style="margin-top: 12px;width: 100% !important;">
					<dt style="padding-top:5px;">勾选允许开启微店权限:</dt>
						<div style="padding: 10px;margin-top:30px;">
						<span style="margin-left:42px;">推广员身份：</span>
							<?php
							$exp_name_3 = ""; //3*3等级推广员自定义名称 
							$level 		= 1; 
							$i   		= 0;
							if( $is_ncomission ){
								$query_commisions="select exp_name,level from ".WSY_SHOP.".weixin_commonshop_commisions where isvalid=true and customer_id=".$customer_id." order by level asc";
								$result_commisions = _mysql_query($query_commisions) or die('w94 Query failed: ' . mysql_error());
								
								while ($row = mysql_fetch_object($result_commisions)) {
									$exp_name_3 = $row->exp_name;
									$level 		= $row->level;
									$i++;
							?>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" class="check P" <?php if( strstr($microshop_open_permissions_code,"P_".$level )){?> checked <?php } ?> name="P_<?php echo $level?>">
									<?php echo $exp_name_3 ?>
								</label>
							</span>
							<?php 
									
								}
							}else{
							?>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" <?php if( strstr($microshop_open_permissions_code,"P_1" ) ){?> checked <?php } ?> name="P_1" >
									<?php echo $exp_name ?>
								</label>
							</span>
						<?php 
							}
						?>
						<input type=hidden name="P_num" id="P_num" value="<?php echo $i; ?>" />
						</div>						
						<?php 
							if( $is_shareholder ){
						?>
						<div style="padding: 10px;">
						<span style="margin-left:55px;">股东身份：</span>
						<?php						
								$QUERY_BASE = "SELECT a_name,b_name,c_name,d_name from weixin_commonshop_shareholder WHERE isvalid = true and customer_id = ".$customer_id." limit 0,1";
								$RESULT_BASE = _mysql_query($QUERY_BASE) or die (" Wrong_1 : QUERY ERROR : ".mysql_error());
								$a_name 		 = "白金";	
								$b_name			 = "黄金";
								$c_name			 = "白银";
								$d_name			 = "青铜";
								while ($row = mysql_fetch_object($RESULT_BASE)) {
									$a_name = empty($row->a_name)?"白金":$row->a_name;
									$b_name = empty($row->b_name)?"黄金":$row->b_name;
									$c_name = empty($row->c_name)?"白银":$row->c_name;
									$d_name = empty($row->d_name)?"青铜":$row->d_name;
								}
							?>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" class="check G" <?php if( strstr($microshop_open_permissions_code,"G_1" ) ){?> checked <?php } ?> name="G_1" >
									<?php echo $d_name; ?>
								</label>
							</span>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" class="check G" <?php if( strstr($microshop_open_permissions_code,"G_2" ) ){?> checked <?php } ?> name="G_2" >
									<?php echo $c_name; ?>
								</label>
							</span>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" class="check G" <?php if( strstr($microshop_open_permissions_code,"G_3" ) ){?> checked <?php } ?> name="G_3" >
									<?php echo $b_name; ?>
								</label>
							</span>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" class="check G" <?php if( strstr($microshop_open_permissions_code,"G_4" ) ){?> checked <?php } ?> name="G_4" >
									<?php echo $a_name; ?>
								</label>
							</span>
							</div>
							<?php
							}
							?>
							
							<?php 
								if( $is_team ){
							?>
							<div style="padding: 10px;">
							<span style="margin-left:27px;">区域商身份：</span>
							<?php
								$QUERY_BASE = "SELECT p_customer,c_customer,a_customer,is_diy_area,diy_customer from ".WSY_SHOP.".weixin_commonshop_team WHERE isvalid = true and customer_id = ".$customer_id." limit 0,1";
								$RESULT_BASE = _mysql_query($QUERY_BASE) or die (" Wrong_1 : QUERY ERROR : ".mysql_error());
								$p_customer		= "省代";	
								$c_customer		= "市代";
								$a_customer		= "区代";
								$is_diy_area	= 0;//开启自定义区域
								$diy_customer	= "";
								while ($row = mysql_fetch_object($RESULT_BASE)) {
									$p_customer = empty($row->p_customer)?"省代":$row->p_customer;
									$c_customer = empty($row->c_customer)?"市代":$row->c_customer;
									$a_customer = empty($row->a_customer)?"区代":$row->a_customer;
									$is_diy_area = $row->is_diy_area;
									$diy_customer = empty($row->diy_customer)?"自定义区域":$row->diy_customer;
								}
							?>
							<input type=hidden name="is_diy_area" id="is_diy_area" value="<?php echo $is_diy_area; ?>" />
							<span class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" class="check Q" <?php if( strstr($microshop_open_permissions_code,"Q_1" ) ){?> checked <?php } ?>  name="Q_1" >
									<?php echo $p_customer; ?>
								</label>
							</span>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" class="check Q" <?php if( strstr($microshop_open_permissions_code,"Q_2" ) ){?> checked <?php } ?> name="Q_2" >
									<?php echo $c_customer; ?>
								</label>
							</span>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" class="check Q" <?php if( strstr($microshop_open_permissions_code,"Q_3" ) ){?> checked <?php } ?> name="Q_3" >
									<?php echo $a_customer; ?>
								</label>
							</span>
							<?php
							if( $is_diy_area ){
							?>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="checkbox" class="check Q" <?php if( strstr($microshop_open_permissions_code,"Q_4" ) ){?> checked <?php } ?> name="Q_4" >
									<?php echo $diy_customer; ?>
								</label>
							</span>
							<?php 
							}
							?>
							</div>
							<?php
							}
							?>						
					</dl>
					<dl class="WSY_remind_dl02" style="margin-top: 12px;width: 100% !important;">
					<dt style="padding-top:5px;">勾选开启微店赠送身份:</dt>
						<div style="padding: 10px;margin-top:30px;">
						<span style="margin-left:42px;">推广员身份：</span>
							<?php
							$exp_name_3 = ""; //3*3等级推广员自定义名称 
							$level 		= 1; 
							$i   		= 0;
							if( $is_ncomission ){
								$query_commisions="select exp_name,level from ".WSY_SHOP.".weixin_commonshop_commisions where isvalid=true and customer_id=".$customer_id." order by level asc";
								$result_commisions = _mysql_query($query_commisions) or die('w94 Query failed: ' . mysql_error());
								
								while ($row = mysql_fetch_object($result_commisions)) {
									$exp_name_3 = $row->exp_name;
									$level 		= $row->level;
									$i++;
							?>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="radio" class="giveIden" <?php if( $microshop_give_identity == "P_".$level  ){?> checked <?php } ?> name="G_P_<?php echo $level?>" code="P_<?php echo $level?>">
									<?php echo $exp_name_3 ?>
								</label>
							</span>
							<?php 
									
								}
							}else{
							?>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="radio" class="giveIden" <?php if( $microshop_give_identity == "P_1"  ){?> checked <?php } ?> name="G_P_1" code="P_1">
									<?php echo $exp_name ?>
								</label>
							</span>
						<?php 
							}
						?>
						</div>
						<?php 
							if( $is_shareholder ){
						?>
						<div style="padding: 10px;">
						<span style="margin-left:55px;">股东身份：</span>
						<?php						
								$QUERY_BASE = "SELECT a_name,b_name,c_name,d_name from weixin_commonshop_shareholder WHERE isvalid = true and customer_id = ".$customer_id." limit 0,1";
								$RESULT_BASE = _mysql_query($QUERY_BASE) or die (" Wrong_1 : QUERY ERROR : ".mysql_error());
								$a_name 		 = "白金";	
								$b_name			 = "黄金";
								$c_name			 = "白银";
								$d_name			 = "青铜";
								while ($row = mysql_fetch_object($RESULT_BASE)) {
									$a_name = empty($row->a_name)?"白金":$row->a_name;
									$b_name = empty($row->b_name)?"黄金":$row->b_name;
									$c_name = empty($row->c_name)?"白银":$row->c_name;
									$d_name = empty($row->d_name)?"青铜":$row->d_name;
								}
							?>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="radio" class="giveIden" <?php if( $microshop_give_identity == "G_1"  ){?> checked <?php } ?> name="G_G_1" code="G_1">
									<?php echo $d_name; ?>
								</label>
							</span>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="radio" class="giveIden" <?php if( $microshop_give_identity == "G_2"  ){?> checked <?php } ?> name="G_G_2" code="G_2">
									<?php echo $c_name; ?>
								</label>
							</span>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="radio" class="giveIden" <?php if( $microshop_give_identity == "G_3"  ){?> checked <?php } ?> name="G_G_3" code="G_3">
									<?php echo $b_name; ?>
								</label>
							</span>
							<span class="WSY_remind_labelbox">
								<label>
									<input type="radio" class="giveIden" <?php if( $microshop_give_identity == "G_4" ){?> checked <?php } ?> name="G_G_4" code="G_4">
									<?php echo $a_name; ?>
								</label>
							</span>
							</div>	
							<?php
							}
							?>
												

                   </dl>
					<dl class="" id="" style="height:300px;position:absolute;top:400px;" > 
					<dt style="line-height:20px;" >广告图图片1：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:90px;width:200px;">
							<?php 
								$temp = array();
								$temp = explode('-',$foreign_id_array[0]);
								$selector_title = $temp[count($temp)-1];
							?>
							<input type="text" name="selector_title1" id="selector_title" value="<?php echo $selector_title; ?>"  disabled />
							<button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
							<input type=hidden name="selector_id1" id="selector_id" value="<?php echo $foreign_id_array[0]; ?>" />
							
							<select  style="display:none;" id="foreign_id1" name="foreign_id1" onchange="getproduct(this.options[this.options.selectedIndex].value,1)">

							<optgroup label="---------------产品分类---------------"></optgroup>
							<option value="0">---请选择---</option>
							<?php 
								for($i=0;$i<count($typearr);$i++){
									$typestr=explode("_",$typearr[$i]);
							?>	  
								<option value="<?php echo $typestr[0];?>_1"><?php echo $typestr[1];?></option>
							<?php 	
								}
							?>
							
							</select>
							<div class="pro_select1"  id="pro_select1" style="display:none;">
								<select id="detail_id1" name="detail_id1" style="width:160px;margin-top:5px;float:left;display:none;">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>
						
						
						
					
					</dl>
					<iframe id="frm_typeimg" src="microshop_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $adimg[0]; ?>&id=<?php echo $id; ?>&num=1" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:472px;left:60px;"></iframe>
							<dl class="" id="" style="height:300px;position:absolute;top:400px;left:430px;" > 
					<dt style="line-height:20px;" >广告图图片2：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:90px;width:200px;">
							<?php 
								$temp = array();
								$temp = explode('-',$foreign_id_array[1]);
								$selector_title = $temp[count($temp)-1];
							?>
							<input type="text" name="selector_title2" id="selector_title" value="<?php echo $selector_title; ?>"  disabled />
							<button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
							<input type=hidden name="selector_id2" id="selector_id" value="<?php echo $foreign_id_array[1]; ?>" />
							<select style="display:none;"  id="foreign_id2" name="foreign_id2" onchange="getproduct(this.options[this.options.selectedIndex].value,2)">

							<optgroup label="---------------产品分类---------------"></optgroup>
							<option value="0">---请选择---</option>
							<?php 
								for($i=0;$i<count($typearr);$i++){
									$typestr=explode("_",$typearr[$i]);
							?>	  
								<option value="<?php echo $typestr[0];?>_1"><?php echo $typestr[1];?></option>
							<?php 	
								}
							?>
							
							</select>
							<div class="pro_select2"  id="pro_select2" style="display:none;">
								<select id="detail_id2" name="detail_id2" style="width:160px;margin-top:5px;float:left;display:none;">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>
						
						
						
					
					</dl>
					<iframe id="frm_typeimg" src="microshop_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $adimg[1]; ?>&id=<?php echo $id; ?>&num=2" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width: 290px;height: 250px;border: solid 1px #d0d0d0;position: absolute;top: 472px;left: 451px;"></iframe>
							<dl class="" id="" style="height:300px;position:absolute;top:729px;" > 
					<dt style="line-height:20px;" >广告图图片3：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:90px;width:200px;">
							<?php 
								$temp = array();
								$temp = explode('-',$foreign_id_array[2]);
								$selector_title = $temp[count($temp)-1];
							?>
							<input type="text" name="selector_title3" id="selector_title" value="<?php echo $selector_title; ?>"  disabled />
							<button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
							<input type=hidden name="selector_id3" id="selector_id" value="<?php echo $foreign_id_array[2]; ?>" />
							
							<select style="display:none;" id="foreign_id3" name="foreign_id3" onchange="getproduct(this.options[this.options.selectedIndex].value,3)">

							<optgroup label="---------------产品分类---------------"></optgroup>
							<option value="0">---请选择---</option>
							<?php 
								for($i=0;$i<count($typearr);$i++){
									$typestr=explode("_",$typearr[$i]);
							?>	  
								<option value="<?php echo $typestr[0];?>_1"><?php echo $typestr[1];?></option>
							<?php 	
								}
							?>
							
							</select>
							<div class="pro_select3"  id="pro_select3" style="display:none;">
								<select id="detail_id3" name="detail_id3" style="width:160px;margin-top:5px;float:left;display:none;">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>
						
						
						
					
					</dl>
					<iframe id="frm_typeimg" src="microshop_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $adimg[2]; ?>&id=<?php echo $id; ?>&num=3" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:797px;left:60px;"></iframe>
							<dl class="" id="" style="height:300px;position:absolute;top:729px;left: 430px;" > 
					<dt style="line-height:20px;" >广告图图片4：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:90px;width:200px;">
							<?php 
								$temp = array();
								$temp = explode('-',$foreign_id_array[3]);
								$selector_title = $temp[count($temp)-1];
							?>
							<input type="text" name="selector_title4" id="selector_title" value="<?php echo $selector_title; ?>"  disabled />
							<button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
							<input type=hidden name="selector_id4" id="selector_id" value="<?php echo $foreign_id_array[3]; ?>" />
							
							<select style="display:none;"  id="foreign_id4" name="foreign_id4" onchange="getproduct(this.options[this.options.selectedIndex].value,4)">

							<optgroup label="---------------产品分类---------------"></optgroup>
							<option value="0">---请选择---</option>
							<?php 
								for($i=0;$i<count($typearr);$i++){
									$typestr=explode("_",$typearr[$i]);
							?>	  
								<option value="<?php echo $typestr[0];?>_1"><?php echo $typestr[1];?></option>
							<?php 	
								}
							?>
							
							</select>
							<div class="pro_select4"  id="pro_select4" style="display:none;">
								<select id="detail_id4" name="detail_id4" style="width:160px;margin-top:5px;float:left;display:none;">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>
						
						
						
					
					</dl>
					<iframe id="frm_typeimg" src="microshop_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $adimg[3]; ?>&id=<?php echo $id; ?>&num=4" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:797px;left: 451px;"></iframe>






							<dl class="" id="" style="height:300px;position:absolute;top:1054px;" > 
					<dt style="line-height:20px;" >广告图图片5：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:90px;width:200px;">
							<?php 
								$temp = array();
								$temp = explode('-',$foreign_id_array[4]);
								$selector_title = $temp[count($temp)-1];
							?>
							<input type="text" name="selector_title5" id="selector_title" value="<?php echo $selector_title; ?>"  disabled />
							<button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
							<input type=hidden name="selector_id5" id="selector_id" value="<?php echo $foreign_id_array[4]; ?>" />
							<select style="display:none;"  id="foreign_id5" name="foreign_id5" onchange="getproduct(this.options[this.options.selectedIndex].value,5)">

							<optgroup label="---------------产品分类---------------"></optgroup>
							<option value="0">---请选择---</option>
							<?php 
								for($i=0;$i<count($typearr);$i++){
									$typestr=explode("_",$typearr[$i]);
							?>	  
								<option value="<?php echo $typestr[0];?>_1"><?php echo $typestr[1];?></option>
							<?php 	
								}
							?>
							
							</select>
							<div class="pro_select5"  id="pro_select5" style="display:none;">
								<select id="detail_id5" name="detail_id5" style="width:160px;margin-top:5px;float:left;display:none;">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>
						
						
						
					</dl>

					<iframe id="frm_typeimg" src="microshop_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $adimg[4]; ?>&id=<?php echo $id; ?>&num=5" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:1124px;left:60px;"></iframe>
                    

                <!--pc端开始 -->

                
					<?php if($PC_SHOP){ ?>
                   	<dl class="" id="" style="height:300px;position:absolute;top:400px;left:845px;" > 
					<dt style="line-height:20px;" >pc端广告图图片1：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:125px;">
						
							<select id="pc_foreign_id1" name="pc_foreign_id1" onchange="pc_getproduct(this.options[this.options.selectedIndex].value,1)">

							<optgroup label="---------------产品分类---------------"></optgroup>
							<option value="0">---请选择---</option>
							<?php 
								for($i=0;$i<count($typearr);$i++){
									$typestr=explode("_",$typearr[$i]);
							?>	  
								<option value="<?php echo $typestr[0];?>_1"><?php echo $typestr[1];?></option>
							<?php 	
								}
							?>
							
							</select>
							<div class="pc_pro_select1"  id="pc_pro_select1" style="display:block;">
								<select id="pc_detail_id1" name="pc_detail_id1" style="width:160px;margin-top:5px;float:left;">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>
					</dl>
					<iframe id="frm_typeimg" src="pc_microshop_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $pc_adimg[0]; ?>&id=<?php echo $id; ?>&num=1" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:472px;left:860px;"></iframe>



                   <dl class="" id="" style="height:300px;position:absolute;top:400px;left:1245px;" > 
					<dt style="line-height:20px;" >pc端广告图图片2：</dt>			
						<div style="height:78px;display:block;position:absolute;top:2px;left:125px;">
							<select id="pc_foreign_id2" name="pc_foreign_id2" onchange="pc_getproduct(this.options[this.options.selectedIndex].value,2)">

							<optgroup label="---------------产品分类---------------"></optgroup>
							<option value="0">---请选择---</option>
							<?php 
								for($i=0;$i<count($typearr);$i++){
									$typestr=explode("_",$typearr[$i]);
							?>	  
								<option value="<?php echo $typestr[0];?>_1"><?php echo $typestr[1];?></option>
							<?php 	
								}
							?>
							
							</select>
							<div class="pc_pro_select2"  id="pc_pro_select2" style="display:block;">
								<select id="pc_detail_id2" name="pc_detail_id2" style="width:160px;margin-top:5px;float:left;">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>
					</dl>
					<iframe id="frm_typeimg" src="pc_microshop_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $pc_adimg[1]; ?>&id=<?php echo $id; ?>&num=2" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width: 290px;height: 250px;border: solid 1px #d0d0d0;position: absolute;top: 472px;left: 1260px;"></iframe>



				<dl class="" id="" style="height:300px;position:absolute;top:729px;left:845px;" > 
					<dt style="line-height:20px;" >pc端广告图图片3：</dt>
						<div style="height:78px;display:block;position:absolute;top:2px;left:125px;">
						
							<select id="pc_foreign_id3" name="pc_foreign_id3" onchange="pc_getproduct(this.options[this.options.selectedIndex].value,3)">

							<optgroup label="---------------产品分类---------------"></optgroup>
							<option value="0">---请选择---</option>
							<?php 
								for($i=0;$i<count($typearr);$i++){
									$typestr=explode("_",$typearr[$i]);
							?>	  
								<option value="<?php echo $typestr[0];?>_1"><?php echo $typestr[1];?></option>
							<?php 	
								}
							?>
							
							</select>
							<div class="pc_pro_select3"  id="pc_pro_select3" style="display:block;">
								<select id="pc_detail_id3" name="pc_detail_id3" style="width:160px;margin-top:5px;float:left;">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>		
					</dl>
					<iframe id="frm_typeimg" src="pc_microshop_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $pc_adimg[2]; ?>&id=<?php echo $id; ?>&num=3" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:797px;left:860px;"></iframe>


                   	<dl class="" id="" style="height:300px;position:absolute;top:729px;left:1245px;" > 
					<dt style="line-height:20px;" >pc端广告图图片4：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:125px;">
						
							<select id="pc_foreign_id4" name="pc_foreign_id4" onchange="pc_getproduct(this.options[this.options.selectedIndex].value,4)">

							<optgroup label="---------------产品分类---------------"></optgroup>
							<option value="0">---请选择---</option>
							<?php 
								for($i=0;$i<count($typearr);$i++){
									$typestr=explode("_",$typearr[$i]);
							?>	  
								<option value="<?php echo $typestr[0];?>_1"><?php echo $typestr[1];?></option>
							<?php 	
								}
							?>
							
							</select>
							<div class="pc_pro_select4"  id="pc_pro_select4" style="display:block;">
								<select id="pc_detail_id4" name="pc_detail_id4" style="width:160px;margin-top:5px;float:left;">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>	
					</dl>
					<iframe id="frm_typeimg" src="pc_microshop_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $pc_adimg[3]; ?>&id=<?php echo $id; ?>&num=4" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:797px;left: 1260px;"></iframe>

                    
                    <dl class="" id="" style="height:300px;position:absolute;top:1054px;left:845px;" > 
					<dt style="line-height:20px;" >pc端广告图图片5：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:125px;">
						
							<select id="pc_foreign_id5" name="pc_foreign_id5" onchange="pc_getproduct(this.options[this.options.selectedIndex].value,5)">

							<optgroup label="---------------产品分类---------------"></optgroup>
							<option value="0">---请选择---</option>
							<?php 
								for($i=0;$i<count($typearr);$i++){
									$typestr=explode("_",$typearr[$i]);
							?>	  
								<option value="<?php echo $typestr[0];?>_1"><?php echo $typestr[1];?></option>
							<?php 	
								}
							?>
							
							</select>
							<div class="pc_pro_select5"  id="pc_pro_select5" style="display:block;">
								<select id="pc_detail_id5" name="pc_detail_id5" style="width:160px;margin-top:5px;float:left;">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>
						
						
						
					</dl>
								<iframe id="frm_typeimg" src="pc_microshop_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $pc_adimg[4]; ?>&id=<?php echo $id; ?>&num=5" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:1124px;left:860px;"></iframe>

                    <!--PC端结束-->
                    <?php } ?>
           
                    
                   


					<input type="hidden" name="isOpenMicroshopAd" id="isOpenMicroshopAd" value="<?php echo $isOpenMicroshopAd; ?>" />
					<input type="hidden" name="id" id="id" value="<?php echo $id;?>">
					<input type="hidden" name="is_microshopData" id="is_microshopData" value="<?php echo $is_microshopData; ?>" />
					<input type="hidden" name="is_mandatoryAD" id="is_mandatoryAD" value="<?php echo $is_mandatoryAD; ?>" />
					<input type="hidden" name="is_microshop" id="is_microshop" value="<?php echo $is_microshop; ?>" />
					<input type="hidden" name="adimg1" id="adimg1" value="<?php echo $adimg[0] ; ?>" />
					<input type="hidden" name="adimg2" id="adimg2" value="<?php echo $adimg[1] ; ?>" />
					<input type="hidden" name="adimg3" id="adimg3" value="<?php echo $adimg[2] ; ?>" />
					<input type="hidden" name="adimg4" id="adimg4" value="<?php echo $adimg[3] ; ?>" />
					<input type="hidden" name="adimg5" id="adimg5" value="<?php echo $adimg[4] ; ?>" />
					<!--pc端 -->
					<input type="hidden" name="pc_adimg1" id="pc_adimg1" value="<?php echo $pc_adimg[0] ; ?>" />
					<input type="hidden" name="pc_adimg2" id="pc_adimg2" value="<?php echo $pc_adimg[1] ; ?>" />
					<input type="hidden" name="pc_adimg3" id="pc_adimg3" value="<?php echo $pc_adimg[2] ; ?>" />
					<input type="hidden" name="pc_adimg4" id="pc_adimg4" value="<?php echo $pc_adimg[3] ; ?>" />
					<input type="hidden" name="pc_adimg5" id="pc_adimg5" value="<?php echo $pc_adimg[4] ; ?>" />
					<!--pc端 -->
					
					<input type=hidden name="keyid" id="keyid" value="<?php echo $keyid; ?>" />
					<input type=hidden name="wce_id" id="wce_id" value="<?php echo $wce_id; ?>" />
					<input type=hidden name="is_ncomission" id="is_ncomission" value="<?php echo $is_ncomission; ?>" />
					<input type=hidden name="is_shareholder" id="is_shareholder" value="<?php echo $is_shareholder; ?>" />
					<input type=hidden name="is_team" id="is_team" value="<?php echo $is_team; ?>" />
					<input type=hidden name="isOpenAgent" id="isOpenAgent" value="<?php echo $isOpenAgent; ?>" />
					<input type=hidden name="isOpenSupply" id="isOpenSupply" value="<?php echo $isOpenSupply; ?>" />
					<div style="clear:both"></div>
							   
				  </form>
				</div>

				<input type=hidden name="microshop_give_identity" id="microshop_give_identity" value="<?php echo $microshop_give_identity; ?>" />
					<div style="clear:both"></div>
				<div class="WSY_text_input01" style="margin-left: 44%;margin-top:1005px;">
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
				</div>			
			</div>
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	</div>
</form>	
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<script charset="utf-8" src="../../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script>
function getproduct(typeid,num){
//	console.log(typeid);
	var typearr= new Array(); 
	typearr=typeid.split("_");	
	if(typearr[1]==1){			
		url='get_product_list.php?callback=jsonpCallback_get_product_list&type_id='+typearr[0]+'&num='+num;
		 $.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_get_product_list'
		});		
		$("#pro_select1").css("display","block");
	}
	
}
function pc_getproduct(typeid,num){
//	console.log(typeid);
	var typearr= new Array(); 
	typearr=typeid.split("_");	
	if(typearr[1]==1){			
		url='get_product_list.php?callback=pc_jsonpCallback_get_product_list&type_id='+typearr[0]+'&num='+num;
		 $.jsonp({
			url:url,
			callbackParameter: 'pc_jsonpCallback_get_product_list'
		});		
		$("#pc_pro_select1").css("display","block");
	}
	
}
var detail_id=-1;	
function jsonpCallback_get_product_list(results){
		var len = results.length;
		console.log(results);
		var sel_pro1 = document.getElementById("detail_id1");
		var sel_pro2 = document.getElementById("detail_id2");
		var sel_pro3 = document.getElementById("detail_id3");	
		var sel_pro4 = document.getElementById("detail_id4");
		var sel_pro5 = document.getElementById("detail_id5");
		if(results[2].num==1){
		var new_option1 = new Option("---请选择一个产品---",-1);
		sel_pro1.options.length=0;
		sel_pro1.options.add(new_option1);
		}else if(results[2].num==2){
			sel_pro2.options.length=0;	
			var new_option2 = new Option("---请选择一个产品---",-1);
			sel_pro2.options.add(new_option2);
		}else if(results[2].num==3){
			sel_pro3.options.length=0;
			var new_option3 = new Option("---请选择一个产品---",-1);
			sel_pro3.options.add(new_option3);
		}else if(results[2].num==4){
			sel_pro4.options.length=0;
			var new_option4 = new Option("---请选择一个产品---",-1);
			sel_pro4.options.add(new_option4);
		}else if(results[2].num==5){
			sel_pro5.options.length=0;
			var new_option5 = new Option("---请选择一个产品---",-1);
			sel_pro5.options.add(new_option5);
		}	
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		console.log(results[i].num);
		if(results[2].num==1){
		var new_option1 = new Option(pname,pid);
		sel_pro1.options.add(new_option1);
		if(pid==detail_id1){
			new_option1.selected=true;
		}
		}else if(results[2].num==2){
			var new_option2 = new Option(pname,pid);
			sel_pro2.options.add(new_option2);
			if(pid==detail_id2){
				new_option2.selected=true;
			}
		}else if(results[2].num==3){
		var new_option3 = new Option(pname,pid);
			sel_pro3.options.add(new_option3);
			if(pid==detail_id3){
				new_option3.selected=true;
			}
		}else if(results[2].num==4){
			var new_option4 = new Option(pname,pid);
			sel_pro4.options.add(new_option4);
			if(pid==detail_id4){
				new_option4.selected=true;
			}
		}else{
			var new_option5 = new Option(pname,pid);
			sel_pro5.options.add(new_option5);
			if(pid==detail_id5){
				new_option5.selected=true;
			}
		}		
	}
}
var foreign_id=-1;
var detail_id=-1;
var type_linktype=-1;
function setselect(){
	
	var foreign_id1='<?php echo $foreign_id_array[0]?>';
	var foreign_id2='<?php echo $foreign_id_array[1]?>';
	var foreign_id3='<?php echo $foreign_id_array[2]?>';
	var foreign_id4='<?php echo $foreign_id_array[3]?>';
	var foreign_id5='<?php echo $foreign_id_array[4]?>';
	var detail_id1='<?php echo $detail_id_array[0]?>';
	var detail_id2='<?php echo $detail_id_array[1]?>';
	var detail_id3='<?php echo $detail_id_array[2]?>';
	var detail_id4='<?php echo $detail_id_array[3]?>';
	var detail_id5='<?php echo $detail_id_array[4]?>';

	var type_linktype1='<?php echo $link_type_array[0]?>';
	var type_linktype2='<?php echo $link_type_array[1]?>';
	var type_linktype3='<?php echo $link_type_array[2]?>';
	var type_linktype4='<?php echo $link_type_array[3]?>';
	var type_linktype5='<?php echo $link_type_array[4]?>';
	var sobj1= document.getElementById("foreign_id1");
	var options1 = sobj1.options;
	var sobj2= document.getElementById("foreign_id2");
	var options2 = sobj2.options;
	var sobj3= document.getElementById("foreign_id3");
	var options3 = sobj3.options;
	var sobj4= document.getElementById("foreign_id4");
	var options4 = sobj4.options;
	var sobj5= document.getElementById("foreign_id5");
	var options5 = sobj5.options;
	
	for(var j=0;j<options1.length;j++){
		document.getElementById("pro_select1").style.display="block";
		var ov = options1[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==foreign_id1 && ovtype==type_linktype1){
			if(type_linktype1==1){
				var dd =options1[j].selected;
				options1[j].selected ="selected";
				if(foreign_id1>0){
					//产品分类才显示出 选择产品，图文不需要
					document.getElementById("pro_select1").style.display="block";
				}
				if(detail_id1>0){
					console.log(foreign_id1);
					changeProductType21(foreign_id1,detail_id1); 
				}else{
					changeProductType21(foreign_id1,-1); 
				}
			}else{
			  options1[j].selected ="selected";
			 
			}
			break;
		}	
	}
	for(var j=0;j<options2.length;j++){
		document.getElementById("pro_select2").style.display="block";
		var ov = options2[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==foreign_id2 && ovtype==type_linktype2){
			if(type_linktype2==1){
				var dd =options2[j].selected;
				options2[j].selected ="selected";
				if(foreign_id2>0){
					//产品分类才显示出 选择产品，图文不需要
					document.getElementById("pro_select2").style.display="block";
				}
				if(detail_id2>0){
					console.log(foreign_id2);
					changeProductType22(foreign_id2,detail_id2); 
				}else{
					changeProductType22(foreign_id,-1); 
				}
			}else{
			  options2[j].selected ="selected";
			 
			}
			break;
		}	
	}
	for(var j=0;j<options3.length;j++){
		document.getElementById("pro_select3").style.display="block";
		var ov = options3[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==foreign_id3 && ovtype==type_linktype3){
			if(type_linktype3==1){
				var dd =options3[j].selected;
				options3[j].selected ="selected";
				if(foreign_id3>0){
					//产品分类才显示出 选择产品，图文不需要
					document.getElementById("pro_select3").style.display="block";
				}
				if(detail_id3>0){
					console.log(foreign_id3);
					changeProductType23(foreign_id3,detail_id3); 
				}else{
					changeProductType23(foreign_id,-1); 
				}
			}else{
			  options3[j].selected ="selected";
			 
			}
			break;
		}	
	}for(var j=0;j<options4.length;j++){
		document.getElementById("pro_select4").style.display="block";
		var ov = options4[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==foreign_id4 && ovtype==type_linktype4){
			if(type_linktype4==1){
				var dd =options4[j].selected;
				options4[j].selected ="selected";
				if(foreign_id4>0){
					//产品分类才显示出 选择产品，图文不需要
					document.getElementById("pro_select4").style.display="block";
				}
				if(detail_id4>0){
					console.log(foreign_id4);
					changeProductType24(foreign_id4,detail_id4); 
				}else{
					changeProductType24(foreign_id4,-1); 
				}
			}else{
			  options4[j].selected ="selected";
			 
			}
			break;
		}	
	}for(var j=0;j<options5.length;j++){
		document.getElementById("pro_select5").style.display="block";
		var ov = options5[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==foreign_id5 && ovtype==type_linktype5){
			if(type_linktype5==1){
				var dd =options5[j].selected;
				options5[j].selected ="selected";
				if(foreign_id5>0){
					//产品分类才显示出 选择产品，图文不需要
					document.getElementById("pro_select5").style.display="block";
				}
				if(detail_id5>0){
					console.log(foreign_id5);
					changeProductType25(foreign_id5,detail_id5); 
				}else{
					changeProductType25(foreign_id5,-1); 
				}
			}else{
			  options5[j].selected ="selected";
			 
			}
			break;
		}	
	}
}




function changeProductType21(pro_typeid,d_id){   //执行edit时候

	 p_detail_id1 = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=jsonpCallback_get_product_list21&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list21'
	});
	
 // }
}function changeProductType22(pro_typeid,d_id){   //执行edit时候

	 p_detail_id2 = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=jsonpCallback_get_product_list22&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list22'
	});
	
 // }
}function changeProductType23(pro_typeid,d_id){   //执行edit时候

	 p_detail_id3 = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=jsonpCallback_get_product_list23&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list23'
	});
	
 // }
}function changeProductType24(pro_typeid,d_id){   //执行edit时候

	 p_detail_id4 = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=jsonpCallback_get_product_list24&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list24'
	});
	
 // }
}function changeProductType25(pro_typeid,d_id){   //执行edit时候

	 p_detail_id5 = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=jsonpCallback_get_product_list25&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list25'
	});
	
 // }
}
function jsonpCallback_get_product_list21(results){
	var len = results.length;
	var sel_pro1 = document.getElementById("detail_id1");
	sel_pro1.options.length=0;
	var new_option1 = new Option("---请选择一个产品---",-1);
	sel_pro1.options.add(new_option1);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option1 = new Option(pname,pid);
		sel_pro1.options.add(new_option1);
		if(pid==p_detail_id1){
			new_option1.selected=true;
		}
	}   
}
function jsonpCallback_get_product_list22(results){
	var len = results.length;
	var sel_pro2 = document.getElementById("detail_id2");
	sel_pro2.options.length=0;
	var new_option2 = new Option("---请选择一个产品---",-1);
	sel_pro2.options.add(new_option2);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option2 = new Option(pname,pid);
		sel_pro2.options.add(new_option2);
		if(pid==p_detail_id2){
			new_option2.selected=true;
		}
	}   
}
function jsonpCallback_get_product_list23(results){
	var len = results.length;
	var sel_pro3 = document.getElementById("detail_id3");
	sel_pro3.options.length=0;
	var new_option3 = new Option("---请选择一个产品---",-1);
	sel_pro3.options.add(new_option3);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option3 = new Option(pname,pid);
		sel_pro3.options.add(new_option3);
		if(pid==p_detail_id3){
			new_option3.selected=true;
		}
	}   
}
function jsonpCallback_get_product_list24(results){
	var len = results.length;
	var sel_pro4 = document.getElementById("detail_id4");
	sel_pro4.options.length=0;
	var new_option4 = new Option("---请选择一个产品---",-1);
	sel_pro4.options.add(new_option4);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option4 = new Option(pname,pid);
		sel_pro4.options.add(new_option4);
		if(pid==p_detail_id4){
			new_option4.selected=true;
		}
	}   
}
function jsonpCallback_get_product_list25(results){
	var len = results.length;
	var sel_pro5 = document.getElementById("detail_id5");
	sel_pro5.options.length=0;
	var new_option5 = new Option("---请选择一个产品---",-1);
	sel_pro5.options.add(new_option5);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option5 = new Option(pname,pid);
		sel_pro5.options.add(new_option5);	
		if(pid==p_detail_id5){
			new_option5.selected=true;
		}
	}   
}





function setTypeImg(imgurl,obj,type){
	if(type==0){
	if(obj==0){
		$("#adimg1").val(imgurl);
		}
	if(obj==1){
		$("#adimg2").val(imgurl);
		}
	if(obj==2){
		$("#adimg3").val(imgurl);
		}
	if(obj==3){
		$("#adimg4").val(imgurl);
		}
	if(obj==4){
		$("#adimg5").val(imgurl);
		}	
	}else{
	if(type==1){
	if(obj==0){
		$("#pc_adimg1").val(imgurl);
		}
	if(obj==1){
		$("#pc_adimg2").val(imgurl);
		}
	if(obj==2){
		$("#pc_adimg3").val(imgurl);
		}
	if(obj==3){
		$("#pc_adimg4").val(imgurl);
		}
	if(obj==4){
		$("#pc_adimg5").val(imgurl);
		}	

	}
	}
	
}

$('.giveIden').click(function(){
	var go_on = $(this).attr('checked');
	$('.giveIden').attr('checked',false);
	if(go_on == "checked"){
		$(this).attr('checked',true);
		$('#microshop_give_identity').val($(this).attr('code'));
	}else{
		$(this).attr('checked',false);
		$('#microshop_give_identity').val('');
	}
	
});
$('.check').click(function(){
	var go_on = $(this).attr('checked');
	if(go_on == "checked"){
		var type = $(this).attr('name').substr(0,1);//类型
		var level = $(this).attr('name').substr(2);//级别
		$("."+type).each(function(){
			var c_level = $(this).attr('name').substr(2);//遍历当前对象级别
			if( c_level>level ){
				$(this).attr('checked',true);
			}
		});
	}
	
});
</script>
<?php if($PC_SHOP){ ?>
<script>
var pc_detail_id=-1;
function pc_jsonpCallback_get_product_list(results){
		var len = results.length;
		console.log(results);
		var sel_pro1 = document.getElementById("pc_detail_id1");
		var sel_pro2 = document.getElementById("pc_detail_id2");
		var sel_pro3 = document.getElementById("pc_detail_id3");	
		var sel_pro4 = document.getElementById("pc_detail_id4");
		var sel_pro5 = document.getElementById("pc_detail_id5");
		if(results[2].num==1){
		var new_option1 = new Option("---请选择一个产品---",-1);
		sel_pro1.options.length=0;
		sel_pro1.options.add(new_option1);
		}else if(results[2].num==2){
			sel_pro2.options.length=0;	
			var new_option2 = new Option("---请选择一个产品---",-1);
			sel_pro2.options.add(new_option2);
		}else if(results[2].num==3){
			sel_pro3.options.length=0;
			var new_option3 = new Option("---请选择一个产品---",-1);
			sel_pro3.options.add(new_option3);
		}else if(results[2].num==4){
			sel_pro4.options.length=0;
			var new_option4 = new Option("---请选择一个产品---",-1);
			sel_pro4.options.add(new_option4);
		}else if(results[2].num==5){
			sel_pro5.options.length=0;
			var new_option5 = new Option("---请选择一个产品---",-1);
			sel_pro5.options.add(new_option5);
		}	
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		console.log(results[i].num);
		if(results[2].num==1){
		var new_option1 = new Option(pname,pid);
		sel_pro1.options.add(new_option1);
		if(pid==pc_detail_id1){
			new_option1.selected=true;
		}
		}else if(results[2].num==2){
			var new_option2 = new Option(pname,pid);
			sel_pro2.options.add(new_option2);
			if(pid==pc_detail_id2){
				new_option2.selected=true;
			}
		}else if(results[2].num==3){
		var new_option3 = new Option(pname,pid);
			sel_pro3.options.add(new_option3);
			if(pid==pc_detail_id3){
				new_option3.selected=true;
			}
		}else if(results[2].num==4){
			var new_option4 = new Option(pname,pid);
			sel_pro4.options.add(new_option4);
			if(pid==pc_detail_id4){
				new_option4.selected=true;
			}
		}else{
			var new_option5 = new Option(pname,pid);
			sel_pro5.options.add(new_option5);
			if(pid==pc_detail_id5){
				new_option5.selected=true;
			}
		}		
	}
}

var pc_foreign_id=-1;
var pc_detail_id=-1;
var pc_type_linktype=-1;
function pc_setselect(){
	
	var pc_foreign_id1='<?php echo $pc_foreign_id_array[0]?>';
	var pc_foreign_id2='<?php echo $pc_foreign_id_array[1]?>';
	var pc_foreign_id3='<?php echo $pc_foreign_id_array[2]?>';
	var pc_foreign_id4='<?php echo $pc_foreign_id_array[3]?>';
	var pc_foreign_id5='<?php echo $pc_foreign_id_array[4]?>';
	var pc_detail_id1='<?php echo $pc_detail_id_array[0]?>';
	var pc_detail_id2='<?php echo $pc_detail_id_array[1]?>';
	var pc_detail_id3='<?php echo $pc_detail_id_array[2]?>';
	var pc_detail_id4='<?php echo $pc_detail_id_array[3]?>';
	var pc_detail_id5='<?php echo $pc_detail_id_array[4]?>';

	var pc_type_linktype1='<?php echo $pc_link_type_array[0]?>';
	var pc_type_linktype2='<?php echo $pc_link_type_array[1]?>';
	var pc_type_linktype3='<?php echo $pc_link_type_array[2]?>';
	var pc_type_linktype4='<?php echo $pc_link_type_array[3]?>';
	var pc_type_linktype5='<?php echo $pc_link_type_array[4]?>';
	var sobj1= document.getElementById("pc_foreign_id1");
	var options1 = sobj1.options;
	var sobj2= document.getElementById("pc_foreign_id2");
	var options2 = sobj2.options;
	var sobj3= document.getElementById("pc_foreign_id3");
	var options3 = sobj3.options;
	var sobj4= document.getElementById("pc_foreign_id4");
	var options4 = sobj4.options;
	var sobj5= document.getElementById("pc_foreign_id5");
	var options5 = sobj5.options;
	
	for(var j=0;j<options1.length;j++){
		document.getElementById("pc_pro_select1").style.display="block";
		var ov = options1[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==pc_foreign_id1 && ovtype==pc_type_linktype1){
			if(pc_type_linktype1==1){
				var dd =options1[j].selected;
				options1[j].selected ="selected";
				if(pc_foreign_id1>0){
					//产品分类才显示出 选择产品，图文不需要
					document.getElementById("pc_pro_select1").style.display="block";
				}
				if(pc_detail_id1>0){
					console.log(pc_foreign_id1);
					pc_changeProductType21(pc_foreign_id1,pc_detail_id1); 
				}else{
					pc_changeProductType21(pc_foreign_id1,-1); 
				}
			}else{
			  options1[j].selected ="selected";
			 
			}
			break;
		}	
	}
	for(var j=0;j<options2.length;j++){
		document.getElementById("pc_pro_select2").style.display="block";
		var ov = options2[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==pc_foreign_id2 && ovtype==pc_type_linktype2){
			if(pc_type_linktype2==1){
				var dd =options2[j].selected;
				options2[j].selected ="selected";
				if(pc_foreign_id2>0){
					//产品分类才显示出 选择产品，图文不需要
					document.getElementById("pc_pro_select2").style.display="block";
				}
				if(pc_detail_id2>0){
					console.log(pc_foreign_id2);
					pc_changeProductType22(pc_foreign_id2,pc_detail_id2); 
				}else{
					pc_changeProductType22(pc_foreign_id,-1); 
				}
			}else{
			  options2[j].selected ="selected";
			 
			}
			break;
		}	
	}
	for(var j=0;j<options3.length;j++){
		document.getElementById("pc_pro_select3").style.display="block";
		var ov = options3[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==pc_foreign_id3 && ovtype==pc_type_linktype3){
			if(pc_type_linktype3==1){
				var dd =options3[j].selected;
				options3[j].selected ="selected";
				if(foreign_id3>0){
					//产品分类才显示出 选择产品，图文不需要
					document.getElementById("pc_pro_select3").style.display="block";
				}
				if(pc_detail_id3>0){
					console.log(pc_foreign_id3);
					pc_changeProductType23(pc_foreign_id3,pc_detail_id3); 
				}else{
					pc_changeProductType23(pc_foreign_id,-1); 
				}
			}else{
			  options3[j].selected ="selected";
			 
			}
			break;
		}	
	}for(var j=0;j<options4.length;j++){
		document.getElementById("pc_pro_select4").style.display="block";
		var ov = options4[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==pc_foreign_id4 && ovtype==pc_type_linktype4){
			if(pc_type_linktype4==1){
				var dd =options4[j].selected;
				options4[j].selected ="selected";
				if(pc_foreign_id4>0){
					//产品分类才显示出 选择产品，图文不需要
					document.getElementById("pc_pro_select4").style.display="block";
				}
				if(pc_detail_id4>0){
					console.log(pc_foreign_id4);
					pc_changeProductType24(pc_foreign_id4,pc_detail_id4); 
				}else{
					pc_changeProductType24(pc_foreign_id4,-1); 
				}
			}else{
			  options4[j].selected ="selected";
			 
			}
			break;
		}	
	}for(var j=0;j<options5.length;j++){
		document.getElementById("pc_pro_select5").style.display="block";
		var ov = options5[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==pc_foreign_id5 && ovtype==pc_type_linktype5){
			if(pc_type_linktype5==1){
				var dd =options5[j].selected;
				options5[j].selected ="selected";
				if(pc_foreign_id5>0){
					//产品分类才显示出 选择产品，图文不需要
					document.getElementById("pc_pro_select5").style.display="block";
				}
				if(pc_detail_id5>0){
					console.log(pc_foreign_id5);
					pc_changeProductType25(pc_foreign_id5,pc_detail_id5); 
				}else{
					pc_changeProductType25(pc_foreign_id5,-1); 
				}
			}else{
			  options5[j].selected ="selected";
			 
			}
			break;
		}	
	}
}
function pc_changeProductType21(pro_typeid,d_id){   //执行edit时候

	 pc_p_detail_id1 = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=pc_jsonpCallback_get_product_list21&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'pc_jsonpCallback_get_product_list21'
	});
	
 // }
}function pc_changeProductType22(pro_typeid,d_id){   //执行edit时候

	 pc_p_detail_id2 = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=pc_jsonpCallback_get_product_list22&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'pc_jsonpCallback_get_product_list22'
	});
	
 // }
}function pc_changeProductType23(pro_typeid,d_id){   //执行edit时候

	 pc_p_detail_id3 = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=pc_jsonpCallback_get_product_list23&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'pc_jsonpCallback_get_product_list23'
	});
	
 // }
}function pc_changeProductType24(pro_typeid,d_id){   //执行edit时候

	 pc_p_detail_id4 = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=pc_jsonpCallback_get_product_list24&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'pc_jsonpCallback_get_product_list24'
	});
	
 // }
}function pc_changeProductType25(pro_typeid,d_id){   //执行edit时候

	 pc_p_detail_id5 = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=pc_jsonpCallback_get_product_list25&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'pc_jsonpCallback_get_product_list25'
	});
	
 // }
}

function pc_jsonpCallback_get_product_list21(results){
	var len = results.length;
	var sel_pro1 = document.getElementById("pc_detail_id1");
	sel_pro1.options.length=0;
	var new_option1 = new Option("---请选择一个产品---",-1);
	sel_pro1.options.add(new_option1);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option1 = new Option(pname,pid);
		sel_pro1.options.add(new_option1);
		if(pid==pc_p_detail_id1){
			new_option1.selected=true;
		}
	}   
}
function pc_jsonpCallback_get_product_list22(results){
	var len = results.length;
	var sel_pro2 = document.getElementById("pc_detail_id2");
	sel_pro2.options.length=0;
	var new_option2 = new Option("---请选择一个产品---",-1);
	sel_pro2.options.add(new_option2);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option2 = new Option(pname,pid);
		sel_pro2.options.add(new_option2);
		if(pid==pc_p_detail_id2){
			new_option2.selected=true;
		}
	}   
}
function pc_jsonpCallback_get_product_list23(results){
	var len = results.length;
	var sel_pro3 = document.getElementById("pc_detail_id3");
	sel_pro3.options.length=0;
	var new_option3 = new Option("---请选择一个产品---",-1);
	sel_pro3.options.add(new_option3);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option3 = new Option(pname,pid);
		sel_pro3.options.add(new_option3);
		if(pid==pc_p_detail_id3){
			new_option3.selected=true;
		}
	}   
}
function pc_jsonpCallback_get_product_list24(results){
	var len = results.length;
	var sel_pro4 = document.getElementById("pc_detail_id4");
	sel_pro4.options.length=0;
	var new_option4 = new Option("---请选择一个产品---",-1);
	sel_pro4.options.add(new_option4);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option4 = new Option(pname,pid);
		sel_pro4.options.add(new_option4);
		if(pid==pc_p_detail_id4){
			new_option4.selected=true;
		}
	}   
}
function pc_jsonpCallback_get_product_list25(results){
	var len = results.length;
	var sel_pro5 = document.getElementById("pc_detail_id5");
	sel_pro5.options.length=0;
	var new_option5 = new Option("---请选择一个产品---",-1);
	sel_pro5.options.add(new_option5);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option5 = new Option(pname,pid);
		sel_pro5.options.add(new_option5);	
		if(pid==pc_p_detail_id5){
			new_option5.selected=true;
		}
	}   
}
</script>
<?php } ?>
<!-- 新选择链接 -->
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script>
   var that;//标签选择
   var customer_id_en = '<?php echo $customer_id_en; ?>';
   
	//选择优惠劵
	function showSelector(obj){
		that = obj;
        var selector_id = $(obj).parent().find('#selector_id').val();
		layer.open({
			  type: 2,
			  area: ['1500px', '720px'],
			  fixed: false, //不固定
			  maxmin: true,
			  resize:true,
			  title: '选择链接页面',
			  content: '/mshop/admin/index.php?m=plug_link_selector&a=selector_list&customer_id='+customer_id_en+'&selector_id='+selector_id,
		});
	}
	//选择链接回调函数
	//[int] selector_id 链接组成ID [string] selector_title 链接名称
	function showSelectorCallback(selector_id,selector_title){
		console.log(selector_id);
		console.log(selector_title);
		$(that).parent().find("#selector_title").val(selector_title);
		$(that).parent().find("#selector_id").val(selector_id);
	}

</script>
	</body>
</html>