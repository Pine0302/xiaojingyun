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
$head=2;

$is_check_a = 0;
$is_check_b = 0;
$query = "SELECT type,is_check FROM pcshop_foot WHERE isvalid=true AND customer_id=$customer_id LIMIT 2";
$result= _mysql_query($query)or die('Query Error 15: '.mysql_error()." Error query :".$query);
while( $row = mysql_fetch_object($result) ){
	if($row->type==1){
		$is_check_a = $row->is_check;
	}elseif($row->type==2){
		$is_check_b = $row->is_check;
	}
	
}

//a
$a_image 	 = "";//图片
//b
$b_image 	 = "";
//c
$c_image 	 = "";
//d
$d_image 	 = "";
$count_num   = 0;
$query = "SELECT type,image FROM pcshop_foot_support WHERE customer_id=$customer_id";
$result= _mysql_query($query)or die('Query Error 21: '.mysql_error()." Error query :".$query);
while( $row = mysql_fetch_assoc($result)){
	if( $row['type'] == 1){
		$a_image = $row['image'];
	}
	if( $row['type'] == 2){
		$b_image = $row['image'];
	}
	if( $row['type'] == 3){
		$c_image = $row['image'];
	}
	if( $row['type'] == 4){
		$d_image = $row['image'];
	}
}

$query = "SELECT count(id) as count_num FROM pcshop_foot_support WHERE isvalid=true AND customer_id=$customer_id";
$result= _mysql_query($query)or die('Query Error 47: '.mysql_error()." Error query :".$query);
while( $row = mysql_fetch_object($result)){
	$count_num = $row->count_num;
}

$copyright = "";
$query = "SELECT data_t FROM pcshop_foot_service where customer_id=$customer_id AND isvalid=true AND type=2";
$result= _mysql_query($query)or die('Query Error 52: '.mysql_error()." Error query :".$query);
while($row = mysql_fetch_object($result)){
	$copyright = $row->data_t;
}
//echo $query.'</br>';


$telephone = "";
$identification = "";
$data = "";
$query = "SELECT data_t FROM pcshop_foot_service where customer_id=$customer_id AND isvalid=true AND type=3";
$result= _mysql_query($query)or die('Query Error 52: '.mysql_error()." Error query :".$query);
while($row = mysql_fetch_object($result)){
	$data = $row->data_t;
}
//echo $query.'</br>';
if($data){
	$data 			= json_decode($data,true);
	$telephone 		= $data['telephone'];
	$identification = $data['identification'];
}
// echo "电话：".$telephone.'</br>';
// echo "标识：".$identification.'</br>';

// echo "注册：".$copyright.'</br>';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>PC商城底部-A-区设置</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/personal_center/personal_center.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>
<style type="text/css">
* ul li{margin-left: 64px;}
.WSY_memberimg{display: inline;}
.upimg ul li{float: left;width:280px;margin-top: 10px;}
.upimg{width:830px;height: 500px;float: left;margin-top:25px;}
.nav_img img{width: 700px;}
.nav_img{margin-top: 50px;margin-left: 50px;float: left;}
.WSY_list{_width:50%;}
.choose_a{
	position: absolute;
	width: 700px;
	height: 70px;
    margin-top: 55px;
    margin-left: 50px;
   
}
.choose_b{
	position: absolute;
	width: 700px;
    height: 100px;
    
    margin-left: 50px;
    margin-top: 127px;
}
.choose_c{
	position: absolute;
	width: 700px;
    height: 80px;
    position: absolute;
    
    margin-left: 50px;
    margin-top: 229px;
}
.choosed{border: 2px solid red;}
.choose{cursor: pointer;}
.links ul li{margin-top: 2px;}
.links input{width: 250px;border:1px solid #ccc;border-radius: 3px;height: 15px;}
.title{border:1px solid #ccc;border-radius: 3px;height:15px;}
.footer_c img{width: 20px;height: 20px;margin-left: 15px;}
.footer_c{float: left;margin-left: 50px;margin-top: 50px;height: 500px;display: none;width:580px;}
.fc_left{float: left;}.
.fc_title{}
.fc_right{float: right;}
.fc_left ul li{margin-top: 2px;}
.copyright{
	position: relative;
    float: left;
    margin-top: 10px;
    height: 20px;
    margin-left: 16px;
}
.copyright input{
	border: 1px solid #ccc;
    width: 400px;
    border-radius: 3px;
    height: 18px;
}
.telephone{
	position: relative;
    float: left;
    margin-top: 10px;
    height: 20px;
    margin-left: 16px;
}
.telephone input{
	border: 1px solid #ccc;
    width: 200px;
    border-radius: 3px;
    height: 18px;
}
.data-left{
	float: left;
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
.data-info{
	overflow: auto;
    height: 440px;
}
.title-list{
	padding:4px 6px 4px 6px;
	background-color: #ccc;
	color: black;
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
	$(".footer_a").show('slow');
	// var show_num = "<?php echo $count_num?>";
	// switch(show_num) {
	// 	case "1":
	// 		var tis = "265*100";
	// 		break;
	// 	case "2":
	// 		var tis = "265*100";
	// 		break;
	// 	case "3":
	// 		var tis = "265*100";
	// 		break;
	// 	case "4":
	// 		var tis = "265*100";
	// 		break;
	// }
	var tis = "1920*140";
	var content = "配图建议（长*宽）尺寸为："+tis+"像素";
	$("#content_tis").html(content);
	// for(var i=0;i<show_num;i++){
	// 	$(".upimg ul li").eq(i).show();
	// }

	//2为B区input框架提示语


})

function show_img(){
	// $(".upimg ul li").hide();
	// var show_num = $("#img_num").val();
	// switch(show_num) {
	// 	case "1":
	// 		var tis = "265*100";
	// 		break;
	// 	case "2":
	// 		var tis = "265*100";
	// 		break;
	// 	case "3":
	// 		var tis = "265*100";
	// 		break;
	// 	case "4":
	// 		var tis = "265*100";
	// 		break;
	// }
	var tis = "1920*140";
	var content = "配图建议（长*高）尺寸为："+tis+"像素";
	$("#content_tis").html(content);
	for(var i=0;i<show_num;i++){
		$(".upimg ul li").eq(i).show();
	}
}
function choose_class(i){
	$(".no-choose").each(function(){
		$(this).removeClass('WSY-skin-bg');
	});
	$(".no-choose").eq(i-1).addClass('WSY-skin-bg');
	if(i==1){
		$(".choose_a").addClass('choosed');
		$(".choose_b").removeClass('choosed');
		$(".choose_c").removeClass('choosed');
		$(".footer_a").show();
		$(".footer_b").hide();
		$(".footer_c").hide();
	}
	if(i==2){
		$(".choose_a").removeClass('choosed');
		$(".choose_b").addClass('choosed');
		$(".choose_c").removeClass('choosed');
		$(".footer_a").hide();
		$(".footer_b").show();
		$(".footer_c").hide();
	}
	if(i==3){
		$(".choose_a").removeClass('choosed');
		$(".choose_b").removeClass('choosed');
		$(".choose_c").addClass('choosed');
		$(".footer_a").hide();
		$(".footer_b").hide();
		$(".footer_c").show();
	}
}

function add_fc_links(){

	//console.log();
	if($(".fc_left ul li").length>=16){
		alert("C区链接最多只能添加17个！");
		return false;
	}

	var content='<li><span>标题：</span><input type="text" class="fc_title" name="fc_title[]" style="width:150px;height:18px;border:1px solid #ccc; border-radius:3px;"><span>->链接：</span><input type="text" class="fc_links" name="fc_links[]" style="width:200px;height:18px;border:1px solid #ccc; border-radius:3px;"></li>';

	$(".fc_left ul").append(content);
}

function delete_fc_links(){
	if($(".fc_left ul li").length<=1){
		return false;
	}else{
		$(".fc_left ul li:last").remove();
	}
}

function checked_title_b(obj){

	if($(obj).attr('checked')){
		$(obj).val(1);
	}else{
		$(obj).val(0);
	}
}

function choose(i,obj){
	$(".no-choose").each(function(){
		$(this).removeClass('WSY-skin-bg');
	});
	$(obj).addClass('WSY-skin-bg');

	if(i==1){
		$(".choose_a").addClass('choosed');
		$(".choose_b").removeClass('choosed');
		$(".choose_c").removeClass('choosed');
		$(".footer_a").show();
		$(".footer_b").hide();
		$(".footer_c").hide();
	}
	if(i==2){
		$(".choose_a").removeClass('choosed');
		$(".choose_b").addClass('choosed');
		$(".choose_c").removeClass('choosed');
		$(".footer_a").hide();
		$(".footer_b").show();
		$(".footer_c").hide();
	}
	if(i==3){
		$(".choose_a").removeClass('choosed');
		$(".choose_b").removeClass('choosed');
		$(".choose_c").addClass('choosed');
		$(".footer_a").hide();
		$(".footer_b").hide();
		$(".footer_c").show();
	}

}

	
</script>
</head>

<body>
<form id="upform" action="save_foot.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">
	<div class="WSY_content">
		<div class="WSY_columnbox">
		<?php include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/PcShop/Base/basic_head.php"); ?>		
			<div class="WSY_data">
				<div class="data-left">
					<div class="choose choose_a" onclick="choose_class(1);"></div>
					<div class="choose choose_b" onclick="choose_class(2);"></div>
					<div class="chosse choose_c" onclick="choose_class(3);"></div>
					<div class="nav_img">
						<img src="../../../Common/images/PcShop/Base/Footset/footer_nav.jpg" alt="">
					</div>
				</div>
				<div class="data-right">
					<div class="title_nav">
						<!-- <a class="title-list">上部分</span>
						<a class="title-list">中间部分</span>
						<a class="title-list">底部部分</span> -->
						<ul>
							<li class="no-choose WSY-skin-bg" onclick="choose(1,this);">上部分</li>
							<li class="no-choose" onclick="choose(2,this)";>中间部分</li>
							<li class="no-choose" onclick="choose(3,this)";>底部部分</li>
						</ul>
					</div>
					<div class="data-info">
					<!-- A 区域设置开始 -->
		            <div class="WSY_list footer_a" id="WSY_list" style="min-height: 500px; display:none;">												
						<div class="WSY_remind_main" style="width:200px;">	
							<!--开关按钮-->
							<dl class="WSY_remind_dl02">
								<dt style="margin-left:28px;">开启A区显示:</dt>
								<dd>
									<?php if($is_check_a==1){ ?>
										<ul style="background-color: rgb(255, 113, 112);">
											<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
											<li onclick="is_check(0)" class="WSY_bot" style="left: 0px;"></li>
											<span onclick="is_check(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
										</ul>
									<?php }else{ ?>
										<ul style="background-color: rgb(203, 210, 216);">
											<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
											<li onclick="is_check(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
											<span onclick="is_check(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
										</ul>
									<?php } ?>
								</dd>
								<input type="hidden" name="is_check_a" id="is_check_a" value="<?php echo $is_check_a; ?>" />
							</dl>
							<!--开关按钮-->

							<!-- <dl class="WSY_remind_dl02" style="width:210px;">
								<dt style="margin-left:52px;">配图数量:</dt>
								<select name="img_num" id="img_num" onclick="show_img();">
									<option value="0" disabled>请选择</option>
									<option value="1" <?php if($count_num==1){?>selected<?php }?>>---- 1张 ----</option>
									<option value="2" <?php if($count_num==2){?>selected<?php }?>>---- 2张 ----</option>
									<option value="3" <?php if($count_num==3){?>selected<?php }?>>---- 3张 ----</option>
									<option value="4" <?php if($count_num==4){?>selected<?php }?>>---- 4张 ----</option>
								</select>
							</dl> -->
							
							<div class="upimg">
							<dt style="margin-left:100px;">配图: <span id="content_tis"></span></dt>
								<ul style="margin-top:20px;margin-left:35px">
									<li>
										<!--配图 1-->
										<div class="WSY_memberimg" id="" style="position:relative;">
											<span style="position:absolute;left:20px;">(配图1)</span>
											<?php if($a_image!=""){?>
											<img src="<?php echo $a_image; ?>" style="width:80px;height:80px;">
											<?php }else{ ?>
											<img src="../../../../../shop/Public/Default/Home/images/footer1.png" style="width:126px;height:120px;">
											<?php } ?>
											
											<!--上传文件代码开始-->
											<div class="uploader white">
												<input type="text" class="filename" readonly/>
												<input type="button" name="file" class="button" value="上传..."/>
												<input size="17" name="image[]" id="a_image" type=file value="<?php echo $a_image ?>">
												<input type=hidden value="<?php echo $a_image ?>" name="a_image" id="a_image" /> 
											</div>
											<!--上传文件代码结束-->
										</div>
										<!--配图 1-->
									</li>
									<!--<li>
										
										<div class="WSY_memberimg" id="" style="position:relative;">
											<span style="position:absolute;left:20px;">(配图2)</span>
											<?php if($b_image!=""){?>
											<img src="<?php echo $b_image; ?>" style="width:80px;height:80px;">
											<?php }else{ ?>
											<img src="../../../../../shop/Public/Default/Home/images/footer2.png" style="width:126px;height:120px;">
											<?php } ?>
											
											
											<div class="uploader white">
												<input type="text" class="filename" readonly/>
												<input type="button" name="file" class="button" value="上传..."/>
												<input size="17" name="image[]" id="b_image" type=file value="<?php echo $b_image ?>">
												<input type=hidden value="<?php echo $b_image ?>" name="b_image" id="b_image" /> 
											</div>
											
										</div>
										
									</li>
									<li>
										
										<div class="WSY_memberimg" id="" style="position:relative;">
											<span style="position:absolute;left:20px;">(配图3)</span>
											<?php if($c_image!=""){?>
											<img src="<?php echo $c_image; ?>" style="width:80px;height:80px;">
											<?php }else{ ?>
											<img src="../../../../../shop/Public/Default/Home/images/footer3.png" style="width:126px;height:120px;">
											<?php } ?>
											
											
											<div class="uploader white">
												<input type="text" class="filename" readonly/>
												<input type="button" name="file" class="button" value="上传..."/>
												<input size="17" name="image[]" id="c_image" type=file value="<?php echo $c_image ?>">
												<input type=hidden value="<?php echo $c_image ?>" name="c_image" id="c_image" /> 
											</div>
											
										</div>
										
									</li>
									<li>
										
										<div class="WSY_memberimg" id="" style="position:relative;">
											<span style="position:absolute;left:20px;">(配图4)</span>
											<?php if($d_image!=""){?>
											<img src="<?php echo $d_image; ?>" style="width:80px;height:80px;">
											<?php }else{ ?>
											<img src="../../../../../shop/Public/Default/Home/images/footer4.png" style="width:126px;height:120px;">
											<?php } ?>
											
											
											<div class="uploader white">
												<input type="text" class="filename" readonly/>
												<input type="button" name="file" class="button" value="上传..."/>
												<input size="17" name="image[]" id="d_image" type=file value="<?php echo $d_image ?>">
												<input type=hidden value="<?php echo $d_image ?>" name="d_image" id="d_image" /> 
											</div>
											
										</div>
										
									</li>-->
								</ul>
							</div>

							<!-- 告诉save文件，这个是处理A区域的表单-->
							<!-- <input type="hidden" value="1" name="op_type"> -->

						</div>
			
					</div>
					<!-- A 区域设置结束 -->
					
					<!-- B 区域设置结束 -->
					<div class="footer_b" style="margin-top:20px;height:500px;float:left;display:none;" >
						<div class="WSY_list" id="WSY_list">												
						<div class="WSY_remind_main">	
							<!--开关按钮-->
							<dl class="WSY_remind_dl02">
								<dt style="margin-left:28px;">开启B区显示:</dt>
								<dd>
									<?php if($is_check_b==1){ ?>
										<ul style="background-color: rgb(255, 113, 112);">
											<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
											<li onclick="is_check_b(0)" class="WSY_bot" style="left: 0px;"></li>
											<span onclick="is_check_b(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
										</ul>
									<?php }else{ ?>
										<ul style="background-color: rgb(203, 210, 216);">
											<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
											<li onclick="is_check_b(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
											<span onclick="is_check_b(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
										</ul>
									<?php } ?>
								</dd>
								<input type="hidden" name="is_check_b" id="is_check_b" value="<?php echo $is_check_b; ?>" />
							</dl>
						</div>
						</div>
						<ul>
						<?php 
							$pfs_id = -1;
							$query = "SELECT id FROM pcshop_foot_service WHERE customer_id=$customer_id AND isvalid=true AND type=0";
							$result= _mysql_query($query)or die('Query Error 421: '.mysql_error()." Error query :".$query);
							while($info = mysql_fetch_object($result)){
								$id = $info->id;
							}
							if($id>0){

							
						?>
												
							<?php
								$query = "SELECT data_t FROM pcshop_foot_service where customer_id=$customer_id AND isvalid=true AND type=0";
								$result= _mysql_query($query)or die('Query Error 52: '.mysql_error()." Error query :".$query);
								while($row = mysql_fetch_object($result)){
									$data_t = $row->data_t;
									$data   = json_decode($data_t,true);
								}	
								// echo '<pre>';			
								// print_r($data);
								// echo '</pre>';			
								for($i=0;$i<count($data);$i++){
							?>
							<li>
								<input type="checkbox" name="fb_check_<?php echo $i;?>" id="fb_check" onclick="checked_title_b(this);" value="<?php if($data[$i]['fb_check']=="" ||$data[$i]['fb_check']==NULL){echo 0;}else{echo $data[$i]['fb_check'];}?>" <?php if($data[$i]['fb_check']==1){echo 'checked';}?> >
								<span>大标题<?php echo $i+1;?>：</span><input type="text" class="title" value="<?php echo $data[$i]['title'];?>" name="fb_name[]">
								<div class="links">
									<ul>
										<?php 
											for($j=0;$j<count( $data[$i]['fb'] );$j++){

										?>
										<li><span>小标题：</span><input type="text" class="fb_title" value="<?php echo $data[$i]['fb'][$j]['title'];?>" name="fb_title<?php echo $i+1;?>[]" placeholder="例：免费注册"><span>->链接：</span><input type="text" placeholder="以‘//’开头，如：//www.xxx.com" value="<?php echo $data[$i]['fb'][$j]['link'];?>" class="fb_links" name="fb_links<?php echo $i+1;?>[]" ></li>
										<?php }?>
									</ul>
								</div>
							</li>
							<?php 
								}
							}else{
							?>
							<?php 
								//$i=0;
								for($i=0;$i<5;$i++){
							?>
							<li>
								<input type="checkbox" name="fb_check_<?php echo $i;?>" onclick="checked_title_b(this);" value="0">
								<span>大标题<?php echo $i+1;?>：</span><input type="text" class="title" name="fb_name[]">
								<div class="links">
									<ul>
										<?php
											for($j=0;$j<5;$j++){

										?>
										<li><span>小标题：</span><input type="text" class="fb_title" name="fb_title<?php echo $j+1;?>[]" placeholder="例：免费注册"><span>->链接：</span><input type="text" class="fb_links" placeholder="以‘//’开头，如：//www.xxx.com" name="fb_links<?php echo $j+1;?>[]" ></li>
										<?php }?>

									</ul>
								</div>
							</li>
							<?php }?>

							
							<?php }?>
						</ul>
						<input type="hidden" id="is_fb_check" name="fb_check_arr" value="">
					</div>
					<!-- B 区域设置结束 -->

					<!-- C 区域设置结束 -->
					<div class="footer_c">
						<div class="fc_left">
						<ul>
							<?php
								$pfs_id = -1;
								$query = "SELECT id FROM pcshop_foot_service WHERE customer_id=$customer_id AND isvalid=true AND type=0";
								$result= _mysql_query($query)or die('Query Error 421: '.mysql_error()." Error query :".$query);
								while($info = mysql_fetch_object($result)){
									$id = $info->id;
								}
								if($id > 0){

								$query = "SELECT data_t FROM pcshop_foot_service where customer_id=$customer_id AND isvalid=true AND type=1";
								$result= _mysql_query($query)or die('Query Error 52: '.mysql_error()." Error query :".$query);
								while($row = mysql_fetch_object($result)){
									$data_t = $row->data_t;
									$data   = json_decode($data_t,true);
								}							
								for($i=0;$i<count($data);$i++){
							?>
							
							

							<li><span>标题：</span><input type="text" class="fc_title" value="<?php echo $data[$i]['title']?>" name="fc_title[]" style="width:150px;height:18px;border:1px solid #ccc; border-radius:3px;"><span>->链接：</span><input type="text" class="fc_links" placeholder="例：//www.xxx.com" value="<?php echo $data[$i]['link']?>" name="fc_links[]" style="width:200px;height:18px;border:1px solid #ccc; border-radius:3px;"></li>

							<?php }
							}else{
							?>
							<li><span>标题：</span><input type="text" class="fc_title" value="" name="fc_title[]" style="width:150px;height:18px;border:1px solid #ccc; border-radius:3px;"><span>->链接：</span><input type="text"  placeholder="例：//www.xxx.com" class="fc_links" value="" name="fc_links[]" style="width:200px;height:18px;border:1px solid #ccc; border-radius:3px;"></li>
							<?php }?>
						</ul>
						</div>

						<div class="fc_right">
							<a style="cursor:pointer;" onclick="add_fc_links();"><img src="../../../Common/images/PcShop/Base/Footset/add.png" alt=""></a>
							<a style="cursor:pointer;" onclick="delete_fc_links();"><img src="../../../Common/images/PcShop/Base/Footset/delete.png" alt=""></a>
						</div>

						<div class="copyright">
							版权标签信息：<input type="text" value="<?php echo $copyright;?>" name="copyright" placeholder="copyright © 20013-2017 ****公司 ***编号 出版许可" >
						</div>
						<div class="telephone">
							举报或其他：<input type="text" value="<?php echo $identification;?>" name="identification" placeholder="例：违法举报电话" >
							电话/其他->：<input type="text" value="<?php echo $telephone;?>" name="telephone" placeholder="8888-88888888" >
						</div>
						
					</div>
					<!-- C 区域设置结束 -->
					</div>
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