<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
/* 参数获取 */
$shop_id =$configutil->splash_new($_POST["shop_id"]);
$member_template_type = 1;
if(!empty($_POST["member_template_type"])){
   $member_template_type=$_POST["member_template_type"];
}
$OpenBillboard = 0;
if(!empty($_POST["OpenBillboard"])){
	$OpenBillboard = $configutil->splash_new($_POST["OpenBillboard"]); //是否开启龙虎榜
}
$is_qr_code = 1;
$is_qr_code = $configutil->splash_new($_POST["is_qr_code"]); //是否开启个人中心二维码海报

$is_open_privilege = 1;
$is_open_privilege = $configutil->splash_new($_POST["is_open_privilege"]); //是否在个人中心开启我的特权，0关1开

$is_open_extension_agent = 0;
$is_open_extension_agent = $configutil->splash_new($_POST["is_open_extension_agent"]); //是否在个人中心提示粉丝您还未成为推广员，0关1开

$is_open_inviter = 1;
$is_open_inviter = $configutil->splash_new($_POST["is_open_inviter"]); //是否在个人中心显示邀请人,0关，1开

$is_indication_range = 0;
$is_indication_range = $configutil->splash_new($_POST["is_indication_range"]); //邀请人显示范围,0全部显示，1推广员显示

$is_my_commission = 0;
if(!empty($_POST["is_my_commission"])){
   (int)$is_my_commission = $configutil->splash_new($_POST["is_my_commission"]);	//是否开启我的佣金
}
$isOpenreward = 0;
if(!empty($_POST["isOpenreward"])){
	(int)$isOpenreward = $configutil->splash_new($_POST["isOpenreward"]);	//是否开启累积佣金
}
$is_open_promoter_ranking = 0;
if(!empty($_POST["is_open_promoter_ranking"])){
	$is_open_promoter_ranking = $configutil->splash_new($_POST["is_open_promoter_ranking"]);	//是否开启推广员排行榜
}
$is_shareholder_bonus_reward = 0;
if(!empty($_POST["is_shareholder_bonus_reward"])){
	$is_shareholder_bonus_reward = $configutil->splash_new($_POST["is_shareholder_bonus_reward"]);	//是否显示店铺奖励报表
}

$giftcode_onoff = 0;
if(!empty($_POST["giftcode_onoff"])){
	$giftcode_onoff = $configutil->splash_new($_POST["giftcode_onoff"]);	//是否显示赠送码
}
$travel_card_onoff = 0; 
if(!empty($_POST["travel_card_onoff"])){
	(int)$travel_card_onoff = $configutil->splash_new($_POST["travel_card_onoff"]);	//是否显示赠送码
}


$giftcode_identitylimit = -2;
if(!empty($_POST["giftcode_identitylimit"])){
	if(!empty($giftcode_onoff)){					//2017.12.23
		$giftcode_identitylimit = implode(',', $_POST["giftcode_identitylimit"]);	//赠送码-用户等级
	}
}
$is_open_wechat_card = 0;
if(!empty($_POST["is_open_wechat_card"])){
	$is_open_wechat_card = $configutil->splash_new($_POST["is_open_wechat_card"]);	//是否显示赠送码
}

$show_promoter_card = 0;
$show_promoter_card = $configutil->splash_new($_POST["show_promoter_card"]); //是否在个人中心开启推广员名片，0关1开


// 会员卡编号
$show_card_id = -1;
if(!empty($_POST["show_card_id"])){
	$show_card_id = $configutil->splash_new($_POST["show_card_id"]);
}
// 会员卡显示内容
$card_show = 1;
if(!empty($_POST["card_show"])){
	$card_show = $configutil->splash_new($_POST["card_show"]);
}

/*我的特权显示方式*/
$upgrade_mode = 0;
if(!empty($_POST["upgrade_mode"])){//产品列表模板选择
   $upgrade_mode = $configutil->splash_new($_POST["upgrade_mode"]);
}

/*weixin_commonshops_extend是否存在记录start*/
$wce_id = -1;
$query = "select id from weixin_commonshops_extend where isvalid=true and shop_id=".$shop_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$wce_id = $row->id;
}
/*weixin_commonshops_extend是否存在记录end*/

//推广员名片初始化 start
	if ($show_promoter_card == 1) {
		//查询是否初始化了名片
		$user_card_setting_sql = "select id from weixin_commonshop_user_contact_setting where customer_id='".$customer_id."' and isvalid=true";
		$user_card_setting     = _mysql_query($user_card_setting_sql);
		while ($user_card_setting_res = mysql_fetch_object($user_card_setting)) {
			$user_card_setting_id = $user_card_setting_res->id;
		}

		//若没有数据，则初始化数据
		if (empty($user_card_setting_id)) {
			$insert_card_set_sql = "insert into weixin_commonshop_user_contact_setting (customer_id,pass_level,jump_url,name_onoff,level_onoff,address_onoff,weixin_onoff,qq_onoff,phone_onoff,tip_onoff,introduce_onoff,follow_onoff,isvalid,jump_title,jump_linktype) values ('{$customer_id}','-1_1_2_3_4_5','/addons/index.php/micro_broadcast/User/index?customer_id={$customer_id}',1,1,1,1,1,1,1,1,1,true,'首页','-2-1-首页')";
			_mysql_query($insert_card_set_sql) or die('insert_card_set_sql failed: ' . mysql_error());
		}
	}
//推广员名片初始化 end

 if($shop_id>0){
	 if($wce_id>0){
		$sql="update weixin_commonshops_extend set upgrade_mode=".$upgrade_mode.",
		is_open_wechat_card='".$is_open_wechat_card."'where customer_id=".$customer_id." and isvalid=true and shop_id=".$shop_id;
	 }else{
		$sql="insert into weixin_commonshops_extend(shop_id,createtime,isvalid,customer_id,is_Pinformation,is_stockOut,is_division,is_promoter,upgrade_mode,is_open_wechat_card) values(".$shop_id.",now(),true,".$customer_id.",0,0,0,0,".$upgrade_mode.",".$is_open_wechat_card.")";
	 }

	_mysql_query($sql)or die(' Query failed1: ' . mysql_error());

 }
/*我的特权显示方式*/







$template_type_bg=$configutil->splash_new($_POST["template_type_bg"]);
$template_head_bg='';
//echo $_FILES['new_template_type_bg']['name'];

	if($template_type_bg==1){
/* _file_put_contents('hello.txt','**********'.$_FILES['new_define_share_image']['tmp_name']);
file_put_contents('hello2.txt','-------'.$_FILES['new_template_type_bg']['name']); */
	if(!empty($_FILES['new_template_type_bg']['name'])){
		$rand1=rand(0,9);
		$rand2=rand(0,9);
		$rand3=rand(0,9);
		$filename=date("Ymdhis").$rand1.$rand2.$rand3;
		$filetype=substr($_FILES['new_template_type_bg']['name'], strrpos($_FILES['new_template_type_bg']['name'], "."),strlen($_FILES['new_template_type_bg']['name'])-strrpos($_FILES['new_template_type_bg']['name'], "."));
		$filetype=strtolower($filetype);
		if(($filetype!='.jpg')&&($filetype!='.png')&&($filetype!='.gif')){
				echo "<script>alert('文件类型或地址错误');</script>";
				//echo "<script>history.back(-1);</script>";
				exit ;
			}
		$filename=$filename.$filetype;
		$savedir='../../../../'.Base_Upload.'Base/personalization/personal_center/';
		_file_put_contents('hello3.txt',$davedir.'++++'.$filename);
		if(!is_dir($savedir)){
			mkdir($savedir,0777,true);
		}
		 $savefile=$savedir.$filename;
		if (!_move_uploaded_file($_FILES['new_template_type_bg']['tmp_name'], $savefile)){
			echo "<script>文件上传成功！</script>";
			//echo "<script>history.back(-1);</script>";
			exit;
		}
		$save_destination = str_replace("../","",$savefile);
		// $template_head_bg = "/weixinpl/".$save_destination;
		$template_head_bg = "/mshop/".$save_destination;

	}else{
	$template_head_bg=$_POST['now_template_type_bg'];
	}
}

 if($shop_id>0){
	$sql="update weixin_commonshops set is_qr_code=".$is_qr_code.",is_open_privilege=".$is_open_privilege.",show_card_id=".$show_card_id.",card_show=".$card_show.",is_open_extension_agent=".$is_open_extension_agent.",is_open_inviter=".$is_open_inviter.",is_indication_range=".$is_indication_range.",member_template_type=".$member_template_type.",	is_my_commission=".$is_my_commission.",  isOpenreward=".$isOpenreward.",	openbillboard=".$OpenBillboard.",template_head_bg='".$template_head_bg."',is_open_promoter_ranking=".$is_open_promoter_ranking.",is_shareholder_bonus_reward=".$is_shareholder_bonus_reward.",giftcode_onoff=".$giftcode_onoff.",giftcode_identitylimit='".$giftcode_identitylimit."' ,show_promoter_card=".$show_promoter_card." where id=".$shop_id;
	fwrite($f, "====sql===".$sql."\r\n");

	_mysql_query($sql)or die(' Query failed1: ' . mysql_error());


	$row_travel_id = 0;
	$query_setting = "select id from ".WSY_O2O.".travel_card_setting where isvalid=true and customer_id=".$customer_id;
	$result_travel = _mysql_query($query_setting) or die('Query failed: ' . mysql_error());
	while ($row_travel = mysql_fetch_object($result_travel)) {
		$row_travel_id = $row_travel->id;
	}
	if(!empty($row_travel_id)){
		$sql_tavel_card="update ".WSY_O2O.".travel_card_setting set is_open=".$travel_card_onoff." where  isvalid=true and customer_id=".$customer_id;
		fwrite($f, "====sql===".$sql_tavel_card."\r\n");
	}else{
		$sql_tavel_card="insert into ".WSY_O2O.".travel_card_setting (customer_id,is_open,isvalid) values(".$customer_id.",".$travel_card_onoff.",true) ";
		fwrite($f, "====sql===".$sql_tavel_card."\r\n");
	}
	_mysql_query($sql_tavel_card)or die(' Query failed1: ' . mysql_error());
 }
 // echo $sql;exit;
$error = mysql_error();
mysql_close($link);
echo $error;
echo "<script>location.href='personal_center.php?customer_id=".$customer_id_en."';</script>"
?>