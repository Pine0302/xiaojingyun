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
$head=3;


$data = "";
$query = "SELECT data_t FROM pcshop_head_service where customer_id=$customer_id AND isvalid=true";
$result= _mysql_query($query)or die('Query Error 52: '.mysql_error()." Error query :".$query);
while($row = mysql_fetch_object($result)){
	$data = $row->data_t;
}

$count_num = 6;
if($data){
	$data = json_decode($data,true);
	$count_num = count($data);
}

//活动页模板
$template_link = [];	
$query_template = "SELECT id,name FROM pcshop_diy_template WHERE customer_id=".$customer_id." AND supply_id=-1 AND isvalid=true AND custom_type=3 order by id desc";
$result_template = _mysql_query($query_template) or die('Query_template failed:'.mysql_error());
while( $row_template = mysql_fetch_object($result_template) ){
	$template_id 	 = $row_template -> id;		//模板id
	$template_name 	 = $row_template -> name;	//模板名称
	$template_link[] = $template_id."_".$template_name;
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>PC商城头部导航设置</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/personal_center/personal_center.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>
<style type="text/css">
* ul li{margin-left: 64px;}
.WSY_memberimg{display: inline;}
.upimg ul li{float: left;width:280px;margin-top: 10px;display: none;}
.upimg{width:830px;height: 500px;float: left;margin-top:25px;}
.nav_img img{width: 700px;}
.nav_img{margin-top: 50px;margin-left: 50px;float: left;}
.WSY_list{_width:50%;}
.choose_a {
    position: absolute;
    width: 420px;
    height: 29px;
    margin-top: 135px;
    margin-left: 196px;
}
.choosed{border: 2px solid red;}
.choose{cursor: pointer;}
.nav {
    width: 600px;
    height: 36px;
	margin-left:74px;
}
.nav_c{
	width:262px;
	font-size:14px;
    line-height: 34px;
	float:left;
}
.data-info{
	overflow: auto;
    height: 440px;
}
.data-right{
	float: left;
	width: 750px;
	height: 500px;
	margin-top: 20px;
	margin-left: 20px;
	_overflow: auto;
	border:1px solid #ccc;
}
.title_nav{
	width: 90%;
    height: 40px;
    margin-left: 5%;
    margin-top: 10px;
    border-bottom: 1px dotted #ccc;
}
.WSY-skin-bg{
	color: #fff;
}
.no-choose{
	margin-left: 64px;
    float: left;
    width: 80px;
    height: 25px;
    background-color: #ccc;
    line-height: 25px;
    text-align: center;
    cursor: pointer;
    margin-top: 5px;
}
input {
    width: 176px;
    border: 1px solid #AAAAAA;
    height: 22px;
    border-radius: 3px;
	padding-left: 5px;
	margin-left: 3px;
}
select {
	height: 24px;
}
dl.WSY_remind_dl02 {
    float: none;
}
.link_detail{
	display:none;
}
</style>
<script>
function comfirm(){
	$('#config_form').submit();
}

function is_check(a){
	$('#is_check_a').val(a);
}
function is_check_b(a){
	$('#is_check_b').val(a);
}


function submitV(a){
	document.getElementById("upform").submit();	
}

$(function(){
	$(".choose_a").addClass('choosed');
	$(".head_a").show('slow');
	$(".nav").hide();
	var show_num = <?php echo $count_num?>;
	for(var i=0;i<show_num;i++){
		console.log(show_num);
		$(".nav").eq(i).show();
	}
})

function show_nav(){
	$(".nav").hide();
	var show_num = $("#nav_num").val();
	for(var i=0;i<show_num;i++){
		$(".nav").eq(i).show();
	}
}
function choose_class(i){
	if(i==1){
		$(".choose_a").addClass('choosed');
		$(".head_a").show();
	}
}
function change_link(obj){
	console.log($(obj).val()+"==");
	if($(obj).val()==6){
		$(obj).parent().find('.link_detail').show();
	}else{
		$(obj).parent().find('.link_detail').hide();
	}
}


	
</script>
</head>

<body>
<form id="upform" action="save_head.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">
	<div class="WSY_content">
		<div class="WSY_columnbox">
		<?php include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/PcShop/Base/basic_head.php"); ?>		
			<div class="WSY_data">
			<div class="choose choose_a" onclick="choose_class(1);"></div>
			<div class="nav_img">
				<img src="../../../Common/images/PcShop/Base/HeadSet/head.png" alt="">
			</div>
			
			<div class="data-right">
			<div class="title_nav">
				<ul>
					<li class="no-choose WSY-skin-bg" >上部分</li>
				</ul>
			</div>
			<div class="data-info">
			<!-- A 区域设置开始 -->
			<div class="WSY_list head_a" id="WSY_list" style="min-height: 500px; display:none;">												
				<div class="WSY_remind_main" style="width:200px;">	

					<dl class="WSY_remind_dl02" style="width:280px; margin-bottom: 20px;">
						<dt style="margin-left:52px;">导航标题数量：</dt>
						<select name="nav_num" id="nav_num" onclick="show_nav();">
							<option value="1" <?php if($count_num==1){?>selected<?php }?>>1个</option>
							<option value="2" <?php if($count_num==2){?>selected<?php }?>>2个</option>
							<option value="3" <?php if($count_num==3){?>selected<?php }?>>3个</option>
							<option value="4" <?php if($count_num==4){?>selected<?php }?>>4个</option>
							<option value="5" <?php if($count_num==5){?>selected<?php }?>>5个</option>
							<option value="6" <?php if($count_num==6){?>selected<?php }?>>6个</option>
						</select>
					</dl>
					
					<?php for($i=0;$i<6;$i++){?>
					<div class="nav">
						<div class="nav_c">标题<?php echo $i+1;?>：<input type="text" name="nav_title[]" maxlength="5" placeholder="不超过五个字" value="<?php echo $data[$i]['title']?>"/></div>
						<div class="nav_c">
							链接：
							<select name="nav_link[]" onchange="change_link(this);">	
								<option value="-1" >----- 请选择 -----</option>
								<option value="1" <?php if($data[$i]['link']==1){?>selected<?php }?>>首页</option>
								<option value="2" <?php if($data[$i]['link']==2){?>selected<?php }?>>大礼包专区</option>
								<option value="3" <?php if($data[$i]['link']==3){?>selected<?php }?>>积分商城</option>
								<option value="4" <?php if($data[$i]['link']==4){?>selected<?php }?>>限时抢购</option>									
								<option value="5" <?php if($data[$i]['link']==5){?>selected<?php }?>>我的商城</option>
								<option value="6" <?php if($data[$i]['link']==6){?>selected<?php }?>>活动专场</option>
							</select>
							<select class="link_detail" name="link_detail[]" <?php if($data[$i]['link']==6){?>style="display:inline-block"<?php }?>>	
								<option value="-1" >----- 请选择 -----</option>
								<?php foreach($template_link as $key=>$val){
										 $val = explode('_',$val);
										 $tem_id = $val[0];
										 $tem_name = $val[1];
								?>
								<option value="<?php echo $tem_id?>" <?php if($data[$i]['link_detail']==$tem_id){?>selected<?php }?>><?php echo $tem_name?></option>
								<?php }?>
							</select>
						</div>
					</div>
					<?php }?>
					
				</div>
				</div>
	
			</div>
			<!-- A 区域设置结束 -->
			</div>

			</div>
			<div style="width:100%;height:20px;"></div>
			<div class="WSY_text_input01" style="width:300px;">
				<div class="WSY_text_input" style="padding-bottom:100px;"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
				<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
			</div>	
		</div>
	</div>
					
</form>	
<script type="text/javascript" src="../../../Common/js/Base/mall_setting/ToolTip.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
	</body>
</html>