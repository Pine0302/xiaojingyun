<?php
// header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
require('../../../../weixinpl/function_model/public_function.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$shop_id=-1;
$shop_id =$configutil->splash_new($_POST["shop_id"]);

$name                      = $configutil->splash_new($_POST["name"]);
$introduce                 = $configutil->splash_new($_POST["introduce"]);


if ($_POST["shop_card_id_select"]){
	$shop_card_id          = $configutil->splash_new($_POST["shop_card_id_select"]);
}else{
	$shop_card_id          = $configutil->splash_new($_POST["shop_card_id"]);
}


$need_online              	= $configutil->splash_new($_POST["need_online"]);
$online_type              	= $configutil->splash_new($_POST["online_type"]);
$online_qq                 	= $configutil->splash_new($_POST["online_qq"]);
$online_custom          	= $configutil->splash_new($_POST["online_custom"]);
$supply_chat        	 	= $configutil->splash_new($_POST["supply_chat"]);
$advisory_telephone        	= $configutil->splash_new($_POST["advisory_telephone"]);
//echo $advisory_telephone;
$advisory_flag            	= $configutil->splash_new($_POST["advisory_flag"]);
$is_nav 				  	= $configutil->splash_new($_POST["is_nav"]);
$logo 				  	= $configutil->splash_new($_POST["logo"]);
if($shop_id>0){
	$sql = " update weixin_commonshops set name = '".$name
		."', introduce ='".$introduce
		."', shop_card_id = ".$shop_card_id
		." , need_online = ".$need_online
		.", online_type = '".$online_type
		."', online_qq = '".$online_qq
		."', online_custom = '".$online_custom
		."', supply_chat = '".$supply_chat
		."', advisory_telephone='".$advisory_telephone."'"
		.", advisory_flag=".$advisory_flag
		." where id = ".$shop_id." and customer_id=".$customer_id;

		$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());

}else{
	/* $sql = " insert into weixin_commonshops(name,introduce,shop_card_id,is_applymoney,is_applymoney_minmoney,need_online)
		values('".$name."','".$introduce."',".$shop_card_id.",".$is_applymoney.",'".$is_applymoney_minmoney."',".$need_online.")"  ; */


	$sql="insert into weixin_commonshops(customer_id,isvalid,name,email,createtime,need_express,need_email,template_id,issell,reward_type,sell_discount,init_reward,need_customermessage,need_online,online_type,online_qq,online_custom,supply_chat,isprint,detail_template_type,sell_detail,auto_confirmtime,is_attent,attent_url,member_template_type,distr_type,is_showbottom_menu,auto_upgrade_money,is_autoupgrade,is_needlogin,shop_card_id,is_showdiscuss,is_showshare_info,per_share_score,introduce,gz_url,auto_upgrade_money_2,nopostage_money,shop_url,exp_name,reward_level,exp_mem_name,watertype,exp_pic_text1,exp_pic_text2,exp_pic_text3,is_pic,staff_imgurl,open_agents,define_share_image,auto_cus_time,isOpenAgent,isOpenSupply,promoter_bg_imgurl,parent_ps,template_head_bg,is_dis_model,issell_model,parent_class,parent_pid,stock_remind,is_godefault,isOpenInstall,is_identity,per_identity_num,is_cost_limit,per_cost_limit,is_weight_limit,per_weight_limit,is_number_limit,per_number_limit,isOpenPublicWelfare,is_bottom_support,bottom_support_imgurl,bottom_support_cont,isAgreement,is_team,openbillboard,nowprice_title,shopping_status,is_my_commission,isOpenSales,list_type,pro_card_level,is_cashback,cashback_perday,qrsell_orderothers,is_shareholder,CouponId,is_coupon,sendstyle_express,sendstyle_pickup,is_cardfavourable,advisory_telephone,advisory_flag)";


	$sql=$sql." values(".$customer_id.",true,'".$name."','',now(),0,0,1,0,1,0,1,0,".$need_online.",1,'$online_qq','$online_custom','$supply_chat',0,1,'',30,0,'',1,2,1,-1,0,1,".$shop_card_id.",1,0,0,'".$introduce."','',0,0,'','推广员',3,'我的会员_一度会员_二度会员_三度会员',1,'消费变成投资 人人都是老板','长按此图片识别图中二维码搞定','奖励送不停,别人消费你还有奖励',0,'',0,'',7,0,0,'','','',0,0,-1,-1,0,0,0,0,0,0,0,0,0,0,0,0,0,'','',0,0,0,'',0,0,1,1,0,0,0,'',0,-1,0,1,0,0,'".$advisory_telephone."',$advisory_flag)";

	$shop_id = _mysql_query($sql) or die('Query failed 58: ' . mysql_error());

	$query = "SELECT id FROM weixin_commonshops WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
	$result= _mysql_query($query)or die('Query failed 140: ' . mysql_error());
	while($row = mysql_fetch_object($result)){
		$shop_id = $row->id;
	}
}
$ex_id = -1;
$query = "SELECT id FROM weixin_commonshops_extend WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
$result= _mysql_query($query)or die('Query failed 140: ' . mysql_error());
while($row = mysql_fetch_object($result)){
	$ex_id = $row->id;
}


//echo $sql;die;
$commonshop_subscribe_id = $configutil->splash_new($_POST["keyid"]);
$uptypes=array('image/jpg', //上传文件类型列表
'image/jpeg',
'image/png',
'image/pjpeg',
'image/gif',
'image/bmp',
'image/x-png');	
$max_file_size=1000000; //上传文件大小限制, 单位BYTE
$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
$destination_folder='../../../'.Base_Upload.'Base/personalization/personal_center/'; //上传文件路径

//$watermark=1; //是否附加水印(1为加水印,0为不加水印);
//$watertype=1; //水印类型(1为文字,2为图片)
//$waterposition=2; //水印位置(1为左下角,2为右下角,3为左上角,4为右上角,5为居中);
//$waterstring="www.tt365.org"; //水印字符串
//$waterimg="xplore.gif"; //水印图片
$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
$imgpreviewsize=1/1; //缩略图比例

$destination = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!is_uploaded_file($_FILES["upfile"]["tmp_name"]))
	//是否存在文件
	{
		
		if($commonshop_subscribe_id<=0){
			//echo "<font color='red'>文件不存在！</font>";
			//exit;
			//2018/1/22 没有上传图片，就以原图片路径保存
			// $destination = $configutil->splash_new($_POST["logo"]); //变量命名错误，导致没保存
			$save_destination = $configutil->splash_new($_POST["logo"]);
		}else{
			//2018/1/22 没有上传图片，就以原图片路径保存
			// $destination = $configutil->splash_new($_POST["logo"]); //变量命名错误，导致没保存
			$save_destination = $configutil->splash_new($_POST["logo"]);

		}
	}else{
		
		$file = $_FILES["upfile"];
		if($max_file_size < $file["size"])
		//检查文件大小
		{
			echo "<font color='red'>文件太大！</font>";
			exit;
		}
		if(!in_array($file["type"], $uptypes))
		//检查文件类型
		{
		  echo "<font color='red'>不能上传此类型文件！</font>";
		  exit;
		}
		if(!file_exists($destination_folder))
		  mkdir($destination_folder,0777,true);
	  
		  $filename=$file["tmp_name"];
		 
		  $image_size = getimagesize($filename);

		  $pinfo=pathinfo($file["name"]);

		  $ftype=$pinfo["extension"];
		  $destination = $destination_folder.time().".".$ftype;
		  
		  $overwrite=true;
		  if (file_exists($destination) && $overwrite != true)
		  {
			 echo "<font color='red'>同名文件已经存在了！</a>";
			 exit;
		   }
		  if(!_move_uploaded_file ($filename, $destination))
		  {
			 echo "<font color='red'>移动文件出错！</a>";
			 exit;
		  }
		 
		   $pinfo=pathinfo($destination);
			
		  $fname=$pinfo["basename"];	
		  $save_destination = str_replace("../","",$destination);
//		  $save_destination = "/weixinpl/".$save_destination;
		  $save_destination = "/mshop/".$save_destination;
	
	}
  }



if($ex_id < 0){

	$data = array(
			'shop_id'=>$shop_id,
			'isvalid'=>1,
			'createtime'=>'now()',
			'customer_id'=>$customer_id,
			'custom_skin'=>1,
			'is_nav'=>$is_nav,
			'logo'=>$logo

		);
	$table = 'weixin_commonshops_extend';
	insert($data,$table);



	//$sql2 = "INSERT INTO weixin_commonshops_extend(shop_id,createtime,isvalid,customer_id,is_Pinformation,is_stockOut,permanent_code,is_division,recovery_time,custom_skin,is_promoter,is_product_shuf,is_qrMessage,is_memberBuyMessage,is_buyContentMessage,is_orderCommissionMessage,is_orderActivist,is_nav) VALUES($shop_id,now(),true,$customer_id,FALSE,FALSE,'',FALSE,'',1,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE)";
}else{

	$sql2 = "UPDATE weixin_commonshops_extend SET is_nav = $is_nav,logo='".$save_destination."' WHERE isvalid = true AND customer_id=$customer_id";
	
	$result2= _mysql_query($sql2) or die('Query failed 144: ' . mysql_error());
}

mysql_close($link);

echo "<script>location.href='base.php?customer_id=".$customer_id_en."';</script>"

?>