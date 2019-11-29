<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");
$new_baseurl ="//".$http_host; //新UI图片显示
$user_id = $configutil->splash_new($_GET["user_id"]);
$brand="select wcbs.user_id as user_id,wcbs.brand_logo as brand_logo,wcbs.brand_supply_name as brand_supply_name,wcbs.brand_tel as brand_tel,wcbs.brand_intro as brand_intro,wcbs.brand_name as brand_name,wcbs.brand_address as brand_address,wcbs.brand_business_license as brand_business_license,wcbs.addition as addition,wcbs.brand_status as brand_status,wcbs.reason as reason,wcbs.brand_opentime as brand_opentime,wcbs.creatime as creatime,wu.name as name,wu.weixin_name as weixin_name from weixin_commonshop_brand_supplys wcbs inner join weixin_users wu on wcbs.isvalid=true and wcbs.customer_id=".$customer_id." and wcbs.user_id=".$user_id."";
$result=_mysql_query($brand) or die ('brand faild' .mysql_error());
while($row=mysql_fetch_object($result)){
	$user_id=$row->user_id;
	$brand_name=$row->brand_name;
	$brand_address=$row->brand_address;
	$brand_tel=$row->brand_tel;
	$brand_status=$row->brand_status;
	$reason=$row->reason;
	$creatime=$row->creatime;   
	$name=$row->name;
	$brand_intro=$row->brand_intro;
	$brand_logo=$row->brand_logo;
	$brand_supply_name=$row->brand_supply_name;
	$brand_business_license=$row->brand_business_license;
	$weixin_name=$row->weixin_name;//微信名称
	$username= $name."(".$weixin_name.")";
}

$is_kefu	= 1;//是否开启在线客服
$kefu_type		= 1;//客服类型
$supply_qq		= "";//客服类型
$xiaoneng		= "";//客服类型
$supply_id		= -1;//客服类型
$brand_kefu="select supply_id,is_kefu,kefu_type,supply_qq,siteid,xiaoneng from weixin_commonshop_supply_kefu where isvalid=true and customer_id=".$customer_id." and supply_id=".$user_id; 
$result=_mysql_query($brand_kefu) or die ('brand_kefu faild' .mysql_error());
while($row=mysql_fetch_object($result)){
	$supply_id 		= $row->supply_id;
	$is_kefu 		= $row->is_kefu;
	$kefu_type 		= $row->kefu_type;
	$supply_qq 		= $row->supply_qq;
	$siteid 		= $row->siteid;
	$xiaoneng 		= $row->xiaoneng;
}	
mysql_close($link);
 
?>
<html>
<head>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/css2.css" media="all">
<link href="../../../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../js/tis.js"></script>

<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style type="text/css">
a:hover{text-decoration: none;}   
.button_blue{cursor: pointer;margin-left: 10px;font-size: 14px;line-height: 30px;background-color: #06a7e1;padding-left: 15px;padding-right: 15px;border-radius: 3px 3px 3px 3px;margin-top:20px;color: #fff;}
.button_blue:hover{background:#0e98c9;}
.name{  margin-top: 10px;height: 30px;line-height: 30px;font-size: 13px;text-align: left;font-weight: bolder;margin-left: 19px;}
.button_box{width: 296px;display: block;text-align: right;}
.button_box .WSY_button{border-radius:2px;border:none;}
.WSY_remind_dl02 input {
    width: 400px;
    height: 24px;
    border: 1px solid #dddddd;
    border-radius: 2px;
    padding-left: 5px;
}
.WSY_remind_dl02 dd ul {
	float: left;
    overflow: hidden;
    background-color: #cbd2d8;
    width: 50px;
    height: 20px;
    border-radius: 300px;
    position: relative;
}
.WSY_remind_dl02 dd ul p {
    position: absolute;
    font-size: 12px;
    font-family: "Arial";
    line-height: 20px;
}
.WSY_remind_dl02 dd ul li {
    width: 16px;
    height: 16px;
    border-radius: 300px;
    background: #fff;
    position: absolute;
    z-index: 999;
    margin-left: 2px;
    margin-top: 2px;
    cursor: pointer;
}
.WSY_remind_dl02 dd ul span {
    width: 16px;
    height: 16px;
    border-radius: 300px;
    background: #fff;
    position: absolute;
    margin-left: 2px;
    margin-top: 2px;
    cursor: pointer;
}
.kf_type_div i {
    display: block;
    float: left;
    margin-right: 10px;
    height: 20px;
}
.kf_type {
    margin-right: 2px;
    margin-top: 6px;
    display: block;
    float: left;
}
.kf_input {
    width: 150px;
    height: 24px;
    border: solid 1px #ccc;
    border-radius: 2px;
    margin-left: 2px;
}
.WSY_remind_dl01, .WSY_remind_dl02, WSY_remind_dl03{margin:10px 0px;}
.textcss{width:400px;height:200px;border:1px solid #dddddd;}
</style>
</head>

<body> 
    <div class="WSY_content">
		<div class="WSY_columnbox">
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a  class="white1">品牌合作商申请资料</a>
				</div>
			</div>  
			<form action="savebrand_supply2.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&supply_id=<?php echo $supply_id; ?>" enctype="multipart/form-data" id="brand_supply" method="post">
				<div id="products" class="r_con_wrap">
					<div style="margin-top:20px">
						
						<dl class="WSY_remind_dl02"> 
							<dt>合作商ID：</dt>
							<dd>						
								<input type="text" name="user_id" id="user_id"  value="<?php echo $user_id?>" readonly>	
							</dd>
						</dl>
						<dl class="WSY_remind_dl02"> 
							<dt>公司名称：</dt>
							<dd>						
								<input type="text" name="brand_name" id="brand_name"  value="<?php echo $brand_name;?>"> 	
							</dd>
						</dl>
						<dl class="WSY_remind_dl02"> 
							<dt>品牌合作商名称：</dt>
							<dd>						
								<input type="text" name="brand_supply_name" id="brand_supply_name"  value="<?php echo $brand_supply_name;?>"> 	
							</dd>
						</dl>						
						<dl class="WSY_remind_dl02"> 
							<dt>店铺电话：</dt>
							<dd>						
								<input type="text" name="brand_tel" id="brand_tel"  value="<?php echo $brand_tel;?>" > 	
							</dd>
						</dl>
						<dl class="WSY_remind_dl02"> 
							<dt>是否开启在线客服：</dt>
							<dd>
								<?php if($is_kefu==1){ ?>
									<ul style="background-color: rgb(255, 113, 112);">
										<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
										<li onclick="set_need_kefu(0)" class="WSY_bot" style="display: list-item;left: 0px;"></li>
										<span onclick="set_need_kefu(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
									</ul>
								<?php }else{ ?>
									<ul style="background-color: rgb(203, 210, 216);">
										<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
										<li onclick="set_need_kefu(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
										<span onclick="set_need_kefu(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
									</ul>						
								<?php } ?>
								<div class="kf_type_div" id="kf_type_div" <?php if($is_kefu==0){ ?>style="display:none"<?php }?>>
										<i><input type="radio" class="kf_type" style="height:15px;width:15px;" <?php if($kefu_type==1){ ?>checked<?php } ?> value="1" name="kefu_type"><span style="float:left">QQ客服</span><input class="kf_input" style="width:230px;height:20px;" type="text" value="<?php echo $supply_qq ?>" name="supply_qq" ></i></i>
										<i><input type="radio" class="kf_type" style="height:15px;width:15px;" <?php if($kefu_type==2){ ?>checked<?php } ?> value="2" name="kefu_type"><span style="float:left">小能企业号</span><input class="kf_input" style="width:50px;height:20px;" type="text" value="<?php echo $siteid ?>" name="siteid" ></i>
										<i><span style="float:left">小能客服接待组</span><input class="kf_input" style="width:230px;height:20px;" type="text" value="<?php echo $xiaoneng ?>" name="xiaoneng" ></i>
								</div>
										<input type="hidden" name="is_kefu" id="is_kefu" value="<?php echo $is_kefu; ?>" />
							</dd>
						</dl>
						<dl class="WSY_remind_dl02"> 
							<dt>所在地区：</dt>
							<dd>						
								<input type="text" name="brand_address" id="brand_address"  value="<?php echo $brand_address;?>"> 	
							</dd>
						</dl>
						<dl class="WSY_remind_dl02"> 
							<dt>品牌图标：</dt>
							<dd>						
								<img src="<?php echo $brand_logo;?>" style="max-width:200px;" onMouseOver="toolTip('<img src=<?php echo $new_baseurl.$brand_logo;?>>')" onMouseOut="toolTip()">
							</dd>
						</dl>
						<dl class="WSY_remind_dl02"> 
							<dt>营业执照：</dt>
							<dd>						
								<img src="<?php echo $brand_business_license;?>" style="max-width:200px;" onMouseOver="toolTip('<img src=<?php echo $new_baseurl.$brand_business_license;?>>')" onMouseOut="toolTip()">
							</dd>
						</dl>
						<dl class="WSY_remind_dl02"> 
							<dt>店铺介绍：</dt>
							<dd>						
								<textarea name="brand_intro" id="brand_intro" style="width:400px;height:200px;border:1px solid #dddddd;" maxlength="200"><?php echo $brand_intro;?></textarea> <span style="color:#FF0B0B;">（最多输入200个字符）</span>	
							</dd>
						</dl>
						
						
						
					</div>
					<span class="button_box">
						<input type=button class="WSY_button"  value="提交" onclick="submitV();"  style="float:none"/>
						&nbsp;	
						<input type=button class="WSY_button"  value="取消" onclick="document.location='brand_supply.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>';" style="float:none" />
					</span>
				</div> 

			</form>

		</div>
	</div>
</body>
<script>
	function submitV(){
		$("#brand_supply").submit();
	}
	
	function set_need_kefu(obj){
	 $("#is_kefu").val(obj);
	//console.log('obj='+obj);
	if(obj==0){
		$("#kf_type_div").hide();
	}else{
		$("#kf_type_div").show();
	}
}
	// --------显示控制开关效果
	$(function(){
	$(".WSY_bot").click(function(){
		$(this).animate({left : '30px'});
		$(this).parent().find(".WSY_bot2").animate({left : '30px'});
		$(this).hide();
		$(this).parent().find(".WSY_bot2").show();
		$(this).parent().find("p").animate({margin : '0 0 0 13px'}, 500);
		
		$(this).parent().find("p").html('关');
		$(this).parent().css({backgroundColor : '#cbd2d8'});
		$(this).parent().find("p").css({color : '#7f8a97'});
		})
		
	$(".WSY_bot2").click(function(){
		$(this).parent().find(".WSY_bot").animate({left : '0px'});
		$(this).animate({left : '0px'});
		$(this).parent().find(".WSY_bot").show();
		$(this).hide();
		$(this).parent().find("p").animate({margin : '0 0 0 27px'}, 500);
		
		$(this).parent().find("p").html('开');
		$(this).parent().css({backgroundColor : '#ff7170'});
		$(this).parent().find("p").css({color : '#fff'});
		})
	})

</script>
<script language="javascript" src="../../Common/js/Mode/supplier/ToolTip.js"></script>
</html>

