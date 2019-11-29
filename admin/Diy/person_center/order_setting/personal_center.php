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
$member_template_type = 0;
$pageindex= 1 ;//头部文件1个人中心设置

$upgrade_mode = 1;
$query = "select upgrade_mode,is_open_wechat_card from weixin_commonshops_extend where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$upgrade_mode = $row->upgrade_mode;
	$is_open_wechat_card = $row->is_open_wechat_card;//个人中心是否开启微信卡券 0关 1开
}
//echo  $upgrade_mode;

$show_card_id 	= -1;
$card_show 		= 1;
$show_promoter_card = 1;
$query = "select id, member_template_type,is_my_commission,openbillboard,template_head_bg,is_qr_code,isOpenreward,is_open_privilege,is_open_extension_agent,is_open_inviter,is_indication_range,is_open_promoter_ranking,show_card_id,card_show,is_shareholder_bonus_reward,giftcode_onoff,giftcode_identitylimit,show_promoter_card from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$shop_id 				   	= $row->id;
	$member_template_type 	   	= $row->member_template_type;
	$is_my_commission 		   	= $row->is_my_commission;
	$OpenBillboard 			   	= $row->openbillboard;
	$template_head_bg 		   	= $row->template_head_bg;
	$is_qr_code 			   	= $row->is_qr_code;//个人中心二维码海报开关
	$isOpenreward 			   	= $row->isOpenreward;
	$template_type_bg 		   	= $template_head_bg?1:0;
	$is_open_privilege         	= $row->is_open_privilege;//是否在个人中心开启我的特权，0关1开
	$is_open_extension_agent   	= $row->is_open_extension_agent;//是否在个人中心提示粉丝您还未成为推广员，0关1开
	$is_open_inviter           	= $row->is_open_inviter;//是否在个人中心显示邀请人,0关，1开
	$is_indication_range       	= $row->is_indication_range;//邀请人显示范围,0全部显示，1推广员显示
	$is_open_promoter_ranking  	= $row->is_open_promoter_ranking;//是否开启推广员排行榜，1开，0关
	$show_card_id  				= $row->show_card_id;//会员卡编号
	$card_show  				= $row->card_show;//会员卡显示内容
	$is_shareholder_bonus_reward= $row->is_shareholder_bonus_reward;//是否显示店铺奖励报表
	$giftcode_onoff             = $row->giftcode_onoff;//赠送码显示开关
	$giftcode_identitylimit     = $row->giftcode_identitylimit;//赠送码特权显示
	$show_promoter_card         = $row->show_promoter_card;//推广员显示
}//echo $member_template_type;

if(!empty($giftcode_identitylimit)){
	$giftcode_identitylimit = explode(',',$giftcode_identitylimit);
}else{
	$giftcode_identitylimit = array("-2");
	}


$wd_query1="select count(1) as wdcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='微店模式' and c.id=cf.column_id";
$wd_result1 = _mysql_query($wd_query1) or die('Query failed: ' . mysql_error());  
$is_scwd=0; //是否开通了微店模式 0不开通 1开通
$wdcount=0; 
while ($row = mysql_fetch_object($wd_result1)) {
   $wdcount = $row->wdcount;
   break;
}
if($wdcount>0){	
   $is_scwd=1;
}

//判断渠道是否开启股东分红功能---start
$is_disrcount 	= 0;
$is_OpenShareholder = 0;
$query = "SELECT count(1) AS is_disrcount FROM customer_funs cf INNER JOIN columns c WHERE c.isvalid=true AND cf.isvalid=true AND cf.customer_id=".$customer_id." AND c.sys_name='商城股东分红奖励' AND c.id=cf.column_id";
$result = _mysql_query($query) or die('W228 is_OpenShareholder Query failed: ' . mysql_error());  
while ( $row = mysql_fetch_object($result) ) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if( $is_disrcount > 0 ){
	$is_OpenShareholder = 1;
}
//判断渠道是否开启股东分红功能---end

$is_shareholder = 0;	//是否开启股东分红奖励
$query = "SELECT is_shareholder FROM weixin_commonshops WHERE isvalid=true AND customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed'.mysql_error());
$row = mysql_fetch_assoc($result);
$is_shareholder = $row['is_shareholder'];

if( $is_OpenShareholder == 1 && $is_shareholder == 1 ){
	$query = "SELECT a_name,b_name,c_name,d_name FROM weixin_commonshop_shareholder WHERE customer_id=".$customer_id." AND isvalid=true";
	$result = _mysql_query($query) or die('Query failed:'.mysql_error());
	$shareholder_name = mysql_fetch_assoc($result);
}


//echo $is_scwd;
$dp_query1="select count(1) as dpcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='单品模式' and c.id=cf.column_id";
$is_scdp=0; //是否开通了微店模式 0不开通 1开通
$dpcount=0;
$dp_result1 = _mysql_query($dp_query1) or die('Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($dp_result1)) {
   $dpcount = $row->dpcount;
   break;
}
if($dpcount>0){			
   $is_scdp=1;
}
//echo $is_scdp;

// 获取会员卡列表数据
$sql="select id,name from weixin_cards where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($sql) or die(mysql_error());
if($result){
	while($row = mysql_fetch_assoc($result)){
		$card_list[]=$row;
	}
}

//判断渠道是否开启微信卡券功能---start
$is_wercount 	= 0;
$is_OpenWechatCard = 0;
$query = "SELECT count(1) AS is_wercount FROM customer_funs cf INNER JOIN columns c WHERE c.isvalid=true AND cf.isvalid=true AND cf.customer_id=".$customer_id." AND c.sys_name='微信卡券' AND c.id=cf.column_id";
$result = _mysql_query($query) or die('W228 is_OpenShareholder Query failed: ' . mysql_error());  
while ( $row = mysql_fetch_object($result) ) {
	$is_wercount = $row->is_wercount;
	break;
}
if( $is_wercount > 0 ){
	$is_OpenWechatCard = 1;
}
//判断渠道是否开启微信卡券功能---end


/* 查看旅游卡渠道开关 start */
	$is_travelcard = 0;
	$query="select count(1) as is_travelcard from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='旅游卡'";
	$result = _mysql_query($query) or die('L274 is_travelcard Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
	   $is_travelcard = $row->is_travelcard;
	}	
	if( $is_travelcard ==1){
		$query_travel = "select is_open,id from ".WSY_O2O.".travel_card_setting where isvalid=true and customer_id=".$customer_id." limit 0,1";
		$result_travel = _mysql_query($query_travel) or die('Query failed: ' . mysql_error());
		while ($row_travel = mysql_fetch_object($result_travel)) {
			$travel_card_onoff				   	= $row_travel->is_open;
		}
	}
/* 查看旅游卡渠道开关 end */

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>个人中心设置</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/personal_center/personal_center.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>
<script>
function comfirm(){
	$('#config_form').submit();
}
function change_OpenBillboard(a){
	$('#OpenBillboard').val(a);
}
function change_is_qr_code(a){
	$('#is_qr_code').val(a);
}
function change_is_my_commission(a){
	$('#is_my_commission').val(a);
}
function change_isOpenreward(a){
	$('#isOpenreward').val(a);
}
function change_is_open_privilege(a){
	$('#is_open_privilege').val(a);
}
function change_is_open_promoter_ranking(a){
	$('#is_open_promoter_ranking').val(a);
}
function change_is_shareholder_bonus_reward(a){
	$('#is_shareholder_bonus_reward').val(a);
}
function change_is_open_extension_agent(a){
	$('#is_open_extension_agent').val(a);
}
function giftcode_onoff(obj){
	$('#giftcode_onoff').val(obj);
	hide(obj);
}
function change_is_open_wechat_card(a){
	$('#is_open_wechat_card').val(a);
}
function travel_card_onoff(obj){
	$('#travel_card_onoff').val(obj);

}
function show_promoter_card1(a){
	$('#show_promoter_card').val(a);
	//开关按钮
		if (a == '1') {
			var html = '<ul style="background-color: rgb(255, 113, 112);"><p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">关</p><li onclick="show_promoter_card1(0)" class="WSY_bot" style="left: 0px;"></li><span onclick="show_promoter_card1(1)" class="WSY_bot2" style="display: none; left: 0px;"></span></ul><input type="hidden" name="show_promoter_card" id="show_promoter_card" value="<?php echo $show_promoter_card; ?>" /> ';
			$(a).parent().parent().html('').html(html);
			$('#showCard').attr('value',0);
		}else{
			var html = '<ul style="background-color: rgb(203, 210, 216);"><p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">开</p><li onclick="show_promoter_card1(0)" class="WSY_bot" style="display: none; left: 30px;"></li><span onclick="show_promoter_card1(1)" class="WSY_bot2" style="display: block; left: 30px;"></span></ul><input type="hidden" name="show_promoter_card" id="show_promoter_card" value="<?php echo $show_promoter_card; ?>" />';
			$(a).parent().parent().html('').html(html);
			$('#showCard').attr('value',1);
		}
	
	
}

function hide(obj){
	if(obj == 0){
		$("#isShow_identitylimit").fadeOut();
	}else{
		$("#isShow_identitylimit").fadeIn();
	}
}
function change_is_open_inviter(a){
	if (a==1) {
		$('#IndicationRange').css("display","block");
	}else{
		$('#IndicationRange').css("display","none");
	}
	$('#is_open_inviter').val(a);
}
 function submitV(a){
	 var giftcode_onoff = $('input[name=giftcode_onoff]').val();
	 var $giftcode_identitylimit = $('.giftcode_identitylimit:checked');
	 var is_open_wechat_card = $('input[name=is_open_wechat_card]').val();	
	 if(giftcode_onoff == 1 && ($giftcode_identitylimit == undefined || $giftcode_identitylimit.length == 0)){
		 alert('请选择用户等级！');
		 return;
	 }
	 document.getElementById("upform").submit();	
 }
$(function(){
	$(".template_type_bg").change(
		function() {
			var selectedvalue = $("input[name='template_type_bg']:checked").val();
			console.log(selectedvalue);
			
			if (selectedvalue == 1) {
			 $("#define_template_head_bg_div").show();
			}
			else {
			 $("#define_template_head_bg_div").hide();
			}
			});
});		
</script>
</head>

<body>
<form id="upform" action="save_personal_center.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">
	<div class="WSY_content">
		<div class="WSY_columnbox">

		<?php
			include("../head.php"); 
		?>		
		<div class="WSY_data">

              <div class="WSY_list" id="WSY_list" style="min-height: 500px;">
								
				
				<div class="WSY_remind_main">	

					<dl class="WSY_remind_dl02">
					<dt>开启我的佣金:</dt>
						 <dd>
							<?php if($is_my_commission==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_my_commission(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_my_commission(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_is_my_commission(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_my_commission(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="is_my_commission" id="is_my_commission" value="<?php echo $is_my_commission; ?>" />
					</dl>
					
					<dl class="WSY_remind_dl02">
						<dt>开启累积佣金:</dt>
						<dd>
							<?php if($isOpenreward==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="change_isOpenreward(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="change_isOpenreward(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="change_isOpenreward(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="change_isOpenreward(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>
							<?php } ?>
						</dd>
						<input type="hidden" name="isOpenreward" id="isOpenreward" value="<?php echo $isOpenreward; ?>" />
					</dl>

					<dl class="WSY_remind_dl02">
					<dt>开启龙虎榜:</dt>
						 <dd>
							<?php if($OpenBillboard==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_OpenBillboard(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_OpenBillboard(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_OpenBillboard(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_OpenBillboard(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="OpenBillboard" id="OpenBillboard" value="<?php echo $OpenBillboard; ?>" />
					</dl>
					<dl class="WSY_remind_dl02">
					<dt>开启二维码海报:</dt>
						 <dd>
							<?php if($is_qr_code==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_qr_code(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_qr_code(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_is_qr_code(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_qr_code(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="is_qr_code" id="is_qr_code" value="<?php echo $is_qr_code; ?>" />
					</dl>


				</div>	
				<div class="WSY_remind_main">	
					<dl class="WSY_remind_dl02">
					    <dt>"我的特权"显示:</dt>
						<dd>
							<?php if($is_open_privilege==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="change_is_open_privilege(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="change_is_open_privilege(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="change_is_open_privilege(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="change_is_open_privilege(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>
							<?php } ?>
						</dd>
						<input type="hidden" name="is_open_privilege" id="is_open_privilege" value="<?php echo $is_open_privilege; ?>" />
					</dl>
					<dl class="WSY_remind_dl02">
					    <dt>"推广员排行榜"显示:</dt>
						<dd>
							<?php if($is_open_promoter_ranking==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="change_is_open_promoter_ranking(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="change_is_open_promoter_ranking(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="change_is_open_promoter_ranking(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="change_is_open_promoter_ranking(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>
							<?php } ?>
						</dd>
						<input type="hidden" name="is_open_promoter_ranking" id="is_open_promoter_ranking" value="<?php echo $is_open_promoter_ranking; ?>" />
					</dl>
					<dl class="WSY_remind_dl02">
					    <dt>开启我的销售报表:</dt>
						<dd>
							<?php if($is_shareholder_bonus_reward==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="change_is_shareholder_bonus_reward(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="change_is_shareholder_bonus_reward(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="change_is_shareholder_bonus_reward(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="change_is_shareholder_bonus_reward(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>
							<?php } ?>
						</dd>
						<input type="hidden" name="is_shareholder_bonus_reward" id="is_shareholder_bonus_reward" value="<?php echo $is_shareholder_bonus_reward; ?>" />
					</dl>
				</div>
				
                <div class="WSY_remind_main">
				   	<dl class="WSY_remind_dl02" >
						<dt>赠送码显示:</dt>
						<dd>
							<?php if($giftcode_onoff==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="giftcode_onoff(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="giftcode_onoff(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="giftcode_onoff(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="giftcode_onoff(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>
							<?php } ?>
						</dd>
						<input type="hidden" name="giftcode_onoff" id="giftcode_onoff" value="<?php echo $giftcode_onoff; ?>" />
					</dl>

					<?php if($is_OpenWechatCard==1){ ?>
					<dl class="WSY_remind_dl02">
					    <dt>"微信卡券"显示:</dt>
						<dd>
							<?php if($is_open_wechat_card==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="change_is_open_wechat_card(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="change_is_open_wechat_card(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="change_is_open_wechat_card(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="change_is_open_wechat_card(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>
							<?php } ?>
						</dd>
						<input type="hidden" name="is_open_wechat_card" id="is_open_wechat_card" value="<?php echo $is_open_wechat_card; ?>" />
					</dl>
					<?php } ?>
					<?php if($is_travelcard==1){ ?>
					<dl class="WSY_remind_dl02" >
						<dt>我的旅游卡显示:</dt>
						<dd>
							<?php if($travel_card_onoff==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="travel_card_onoff(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="travel_card_onoff(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="travel_card_onoff(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="travel_card_onoff(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>
							<?php } ?>
						</dd>
						<input type="hidden" name="travel_card_onoff" id="travel_card_onoff" value="<?php echo $travel_card_onoff; ?>" />
					</dl>
					<?php } ?>

					<dl class="WSY_remind_dl02" style="width:300px;">
						<dt>推广员名片:</dt>
						<dd>
							 <?php if($show_promoter_card==1){ ?> 
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="show_promoter_card1(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="show_promoter_card1(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
							 <?php }else{ ?> 
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="show_promoter_card1(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="show_promoter_card1(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>
							<?php } ?> 
						</dd>
						<input type="hidden" name="show_promoter_card" id="show_promoter_card" value="<?php echo $show_promoter_card; ?>" /> 
						<img style="width:15px;position: absolute;margin-left: 5px;margin-top:2px; font-color: red;" id="img" src="/mshop\admin\Common\images\Base/help.png" >
						  <a href="#" style="color:#1c58d5;font-size:14px;margin-left: 25px;" onclick="showCard('<?php echo $show_promoter_card; ?>')" id="showCard" >设置名片规则</a>
					</dl>
					
	
                </div>
               
                <div class="WSY_remind_main" id="isShow_identitylimit"  style="<?php if($giftcode_onoff==0){echo "display:none;";}?>">			   
					
						<dl class="WSY_remind_dl02" style="width:1000px;">
						<dt>赠送码特权:</dt>
						<dd style="margin-top: 4px;">
							<input type="checkbox" class="giftcode_identitylimit" id="giftcode_identitylimit"  name="giftcode_identitylimit[]" value="-1" <?php  if(in_array('-1',$giftcode_identitylimit) or in_array('-2',$giftcode_identitylimit)){echo 'checked';}?> /><label for="giftcode_identitylimit">粉丝</label>
			                <input type="checkbox" class="giftcode_identitylimit" id="giftcode_identitylimit0" name="giftcode_identitylimit[]" value="0" <?php  if(in_array('0',$giftcode_identitylimit) or in_array('-2',$giftcode_identitylimit)){echo 'checked';}?> /><label for="giftcode_identitylimit0">推广员</label>
							<?php
				            if( !empty($shareholder_name) ){
			                ?>
							<input type="checkbox" class="giftcode_identitylimit" id="giftcode_identitylimit1" name="giftcode_identitylimit[]"  value="1" <?php  if(in_array('1',$giftcode_identitylimit) or in_array('-2',$giftcode_identitylimit)){echo 'checked';}?> /><label for="giftcode_identitylimit1"><?php echo $shareholder_name['d_name'];?></label>
			                <input type="checkbox" class="giftcode_identitylimit" id="giftcode_identitylimit2" name="giftcode_identitylimit[]"  value="2" <?php  if(in_array('2',$giftcode_identitylimit) or in_array('-2',$giftcode_identitylimit)){echo 'checked';}?> /><label for="giftcode_identitylimit2"><?php echo $shareholder_name['c_name'];?></label>
							<input type="checkbox" class="giftcode_identitylimit" id="giftcode_identitylimit3" name="giftcode_identitylimit[]"  value="3" <?php  if(in_array('3',$giftcode_identitylimit) or in_array('-2',$giftcode_identitylimit)){echo 'checked';}?> /><label for="giftcode_identitylimit3"><?php echo $shareholder_name['b_name'];?></label>
			                <input type="checkbox" class="giftcode_identitylimit" id="giftcode_identitylimit4" name="giftcode_identitylimit[]"  value="4" <?php  if(in_array('4',$giftcode_identitylimit) or in_array('-2',$giftcode_identitylimit)){echo 'checked';}?> /><label for="giftcode_identitylimit4"><?php echo $shareholder_name['a_name'];?></label>
							<?php }?>
						</dd>
						</dl>
					
				</div>		
						
				<div class="WSY_remind_main" style="height:120px!important;">

					<dl class="WSY_remind_dl02" style="float:none!important;width:400px;">
						<dt>个人中心提示粉丝:"您还没有成为推广员":</dt>
						<dd>
							<?php if($is_open_extension_agent==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="change_is_open_extension_agent(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="change_is_open_extension_agent(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="change_is_open_extension_agent(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="change_is_open_extension_agent(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>
							<?php } ?>
						</dd>
						<input type="hidden" name="is_open_extension_agent" id="is_open_extension_agent" value="<?php echo $is_open_extension_agent; ?>" />
					</dl>
					<dl class="WSY_remind_dl02" style="width:500px;">
						<dt>邀请人显示:</dt>
						<dd>
							<?php if($is_open_inviter==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="change_is_open_inviter(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="change_is_open_inviter(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="change_is_open_inviter(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="change_is_open_inviter(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>
							<?php } ?>
						</dd>
						<div id="IndicationRange"  style="<?php if($is_open_inviter==0){echo "display:none;";}?>" >
						<dd >
							<input type="radio" name="is_indication_range" radiogroup="is_indication_range" id="is_indication_range" <?php if( $is_indication_range == 0 ){?>checked<?php }?> value=0 style="margin-left:10px;"><span>所有人都显示</span>
							<input type="radio" name="is_indication_range" radiogroup="is_indication_range" id="is_indication_range" <?php if( $is_indication_range == 1 ){?>checked<?php }?> value=1 style="margin-left:10px;"><span>推广员显示</span>
						</dd>
						</div>
						<input type="hidden" name="is_open_inviter" id="is_open_inviter" value="<?php echo $is_open_inviter; ?>" />
					</dl>
					<dl class="WSY_remind_dl02" style="width:100%;">
						<dt>选择会员卡显示内容:</dt>
						<dd>
							<select name="show_card_id" onchange='change_card(this)'>
								<option value="-1">全部会员卡</option>
								<?php foreach ($card_list as $key => $value) { ?>
								<option value="<?php echo $value['id'] ?>" <?php if($show_card_id==$value['id']){ ?>selected<?php } ?> ><?php echo $value['name'] ?></option>
								<?php } ?>
							</select>
						</dd>
						<text id='card_show_all'>
							<input type="radio" name="card_show" id="card_show_1" <?php if( $card_show == 1 ){?>checked<?php }?> value=1><label for="card_show_1">全部积分</label>
						</text>
						<text id='card_show_one'>
							<input type="radio" name="card_show" id="card_show_2" <?php if( $card_show == 2 ){?>checked<?php }?> value=2><label for="card_show_2">积分</label>
							<input type="radio" name="card_show" id="card_show_3" <?php if( $card_show == 3 ){?>checked<?php }?> value=3><label for="card_show_3">余额</label>
						</text>
					</dl>
				</div>
				<div class="WSY_member " style="height:auto;">				
					<dl class="WSY_remind_dl02" style="width:500px !important;"> 
						<dt  style="line-height:20px;font-weight:normal;margin-left:28px;" >特权显示模式 ：</dt>

							<div style="width:90px;">
								<input type="radio" name="upgrade_mode" id="upgrade_mode_one" <?php if( $upgrade_mode == 1 ){?>checked<?php }?> value=1><span style="float:left;margin-left:10px;">列表模式</span>
								<img src='../../../Common/images/Base/personal_center/mode1.jpg' style="margin-left:5px;width:80px;margin-top:10px;" onMouseOver="toolTip('<img src=../../../Common/images/Base/personal_center/mode1.jpg>')" onMouseOut="toolTip()">
							</div>
							<div style="width:90px;">
								<input type="radio" name="upgrade_mode" id="upgrade_mode_two" <?php if( $upgrade_mode == 2 ){?>checked<?php }?> value=2><span style="float:left;margin-left:10px;">单一模式</span>
								<img src='../../../Common/images/Base/personal_center/mode2.jpg' style="margin-left:5px;width:80px;margin-top:10px;" onMouseOver="toolTip('<img src=../../../Common/images/Base/personal_center/mode2.jpg>')" onMouseOut="toolTip()">
							</div>
						
					</dl>			
				<div style="clear:both"></div>
				</div>

				<div class="WSY_member input WSY_remind_main" style='height: 65px;'>
				<dt style=" position: absolute; left: 51px;width: 70px; ">背景图：</dt>
				
					<dt style=" float: none;margin-bottom: 0; "><input type="radio" value=0 <?php if($template_type_bg==0){ ?>checked<?php } ?> name="template_type_bg" class="template_type_bg" style="margin-top: 9px;">默认</dt>
					<dt style=" float: none; "><input type="radio" value=1 <?php if($template_type_bg==1){ ?>checked<?php } ?> name="template_type_bg" class="template_type_bg" style="margin-top: 9px;">自定义</dt>
					<div style="position: relative;bottom: 59.5px;left: 25px;">
						<label style="float:left;text-align:center;margin-right: 10px;margin-left: 50px;">  图片尺寸要求:</label>							
						<label style="float:left;text-align:center;margin-left: 10px;">750*368</label>
								
						<div style="clear: both;"></div>
					</div>			
				</div>

				<div class="WSY_member input">	
					<div class="WSY_memberimg" id="define_template_head_bg_div" <?php if(!$template_type_bg) echo "style='display:none'"; ?>>
						<?php if($template_head_bg!=""){?>
						<img src="<?php echo $template_head_bg; ?>" style="width:80px;height:80px;">
						<?php }else{ ?>
						<img src="../../../Common/images/Base/personal_center/gift.png" style="width:126px;height:120px;">
						<?php } ?>
						
						<!--上传文件代码开始-->
						<div class="uploader white">
							<input type="text" class="filename" readonly/>
							<input type="button" name="file" class="button" value="上传..."/>
							<input size="17" name="new_template_type_bg" id="new_template_type_bg" type=file value="<?php echo $template_head_bg ?>">
							<input type=hidden value="<?php echo $template_head_bg ?>" name="now_template_type_bg" id="now_template_type_bg" /> 
						</div>
						<!--上传文件代码结束-->
					</div>
					 <input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
				</div>
	
	
				<div class="WSY_text_input01">
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
				</div>			
			</div>
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	</div>
</form>	
<script type="text/javascript" src="../../../Common/js/Base/mall_setting/ToolTip.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
	</body>
</html>

<script>
	var customer_id_en = '<?php echo $customer_id_en; ?>';
	change_card('','<?php echo $show_card_id ?>');
	$("[name='card_show'][value="+<?php echo $card_show ?>+"]").prop('checked','true');
	function change_card(obj,card_id){
		var card_id = card_id||$(obj).val();
		if( card_id == -1 ){
			$('#card_show_all').show()
			$('#card_show_one').hide()
			$("[name='card_show'][value='1']").prop('checked','true');
		}else{
			$('#card_show_one').show()
			$('#card_show_all').hide()
			$("[name='card_show'][value='2']").prop('checked','true');
		}
	}

	$('#img').on('mouseenter', function(){
		layer.tips('提示：名片规则是整个微商城通用。','#img',{
			area: '215px',
			time: 0
		});
	});

	$('#img').on('mouseleave', function(){
		layer.tips('提示：名片规则是整个微商城通用。','#img',{
			area: '215px',
			time: 1
		});
	});

//名片规则	
function showCard(show_promoter_card){
	if (show_promoter_card==0) {
		layer.alert("请开启推广员名片开关");
	}else{
	layer.open({
		  type: 2,
		  area: ['1400px', '770px'],
		  fixed: false, //不固定
		  maxmin: true,
		  resize:true,
		  title: '名片规则',
		  content: '/mshop/admin/index.php?m=promoter_card&a=get_card_setting&customer_id='+customer_id_en,
	});
  }
}

</script>	
