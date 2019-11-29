<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');		
require('../../../../weixinpl/proxy_info.php');
require('../../../../weixinpl/auth_user.php');
_mysql_query("SET NAMES UTF8");		
$head=0;

$keyid = -1;

if(!empty($_GET['keyid'])){
	$keyid = $configutil->splash_new($_GET["keyid"]);
}

$is_open       	  = 1;//是否上架：1：上架；0：下架 默认上架
$sort          	  = 0;//排序
$title         	  = "";//分类标题
$top_img       	  = "";//上侧广告图
$popular     	  = "";//热门推荐
$right_img     	  = "";//右侧广告图
$top_img_arr 	  = array();//顶部广告图数组
$right_img_arr 	  = array();//右侧广告图数组
$categorie_id  	  = "";//该类别的产品分类ID
$ad_pro_id     	  = "";//图片广告产品ID
$abbreviation_img = "";//分类缩略图
$query = "SELECT is_open,title,sort,top_img,right_img,categorie_id,ad_pro_id,abbreviation_img,popular FROM pcshop_home_categories where customer_id=".$customer_id." AND isvalid=true AND id=".$keyid;
$result = _mysql_query($query)or die('Query Error 52: '.mysql_error()." Error query :".$query);
while($row = mysql_fetch_object($result)){
	$is_open      	  = $row->is_open;
	$sort      	      = $row->sort;
	$title        	  = $row->title;
	$top_img      	  = $row->top_img;
	$popular   	 	  = $row->popular;
	$right_img   	  = $row->right_img;
	$categorie_id	  = $row->categorie_id;
	$ad_pro_id    	  = $row->ad_pro_id;
	$abbreviation_img = $row->abbreviation_img;
}

/*查询一级分类ID组成数组*/
$categorie_id = json_decode($categorie_id,true);
$type_array = array();
foreach($categorie_id as $k => $v){
	$type_array[] = $v['id'];
}
/*分离右侧广告图*/
$right_img_arr = explode("|",$right_img);

/*分离右侧广告图*/
$top_img_arr = explode("|",$top_img);

/*分离广告图的产品ID*/
$ad_pro_id = explode("|",$ad_pro_id);
$foreign_id = array();
$detail_id  = array();
foreach($ad_pro_id as $key => $value){
	$arr = explode("_",$value);
	$foreign_id[] = $arr[0];
	$detail_id[]  = $arr[1];
}

$query = "select id, name from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=-1 and customer_id=".$customer_id;

$type_arr = array();
$ctype_arr = array();
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {	//一级分类
	$pt_id = $row->id;
	$pt_name = $row->name;
	$type_str = $pt_id."_".$pt_name;
	$type_arr[] = $type_str;
	
	$query_child = "select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id." and parent_id=".$pt_id;
	$result_child = _mysql_query($query_child) or die("Query child failed:".mysql_error());
	while($row_child = mysql_fetch_object($result_child)){	//二级分类
		$pc_id = $row_child->id;
		$pc_name = $row_child->name;
		$ctype_str = $pc_id.'_'.$pc_name;
		$ctype_arr[$pt_id][] = $ctype_str;
		
		$query_child3 = "select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id." and parent_id=".$pc_id;
		$result_child3 = _mysql_query($query_child3) or die("Query child failed3:".mysql_error());
		while($row_child3 = mysql_fetch_object($result_child3)){	//三级分类
			$pc_id3 = $row_child3->id;
			$pc_name3 = $row_child3->name;
			$ctype_str = $pc_id3.'_'.$pc_name3;
			$ctype_arr[$pc_id][] = $ctype_str;
			
			$query_child4 = "select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id." and parent_id=".$pc_id3;
			$result_child4 = _mysql_query($query_child4) or die("Query child failed4:".mysql_error());
			while($row_child4 = mysql_fetch_object($result_child4)){	//四级分类
				$pc_id4 = $row_child4->id;
				$pc_name4 = $row_child4->name;
				$ctype_str = $pc_id4.'_'.$pc_name4;
				$ctype_arr[$pc_id3][] = $ctype_str;
			}
		}
	}		
}

//热门推荐
$popular = json_decode($popular,true);
$popular_array = array();
foreach($popular as $k => $v){
	$popular_array[] = $k;
}

//一级分类菜单

$sortCondition="";
$sort_str ="";
$querySort = 'SELECT sort_str FROM weixin_commonshop_type_sort where customer_id='.$customer_id;
$resultSort = _mysql_query($querySort) or die('Query failed: ' . mysql_error());

while ($row = mysql_fetch_object($resultSort)) {
    $sort_str = $row->sort_str;
    break;
}

if(strlen($sort_str)>0){
	$sortCondition = 'order by field(id'.$sort_str.')';
}

$sql_type = "select id,name from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." AND is_shelves = 1 and parent_id=-1  ".$sortCondition;
$result_type = _mysql_query($sql_type)or die('Query Error 53: '.mysql_error()." Error query :".$sql_type);

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>PC商城首页分类</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/personal_center/personal_center.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script>
 function submitV(a){
	 var title = $('#title').val();
	 var sort  = $('#sort').val();
	 if( title == '' ){
		 alert("分类标题不能为空！");
		 return;
	 }
	 
	 
	 document.getElementById("upform").submit();	
 }

 $(function(){
	choose_class(1);
	
	function getObjectURL(file) {
		var url = null ; 
		if (window.createObjectURL!=undefined) {
			url = window.createObjectURL(file) ;
		} else if (window.URL!=undefined) {
			url = window.URL.createObjectURL(file) ;
		} else if (window.webkitURL!=undefined) {
			url = window.webkitURL.createObjectURL(file) ;
		}
			return url ;
		}
		$(".upfile").change(function(){
			var obj = $(this).parent('.uploader').siblings('img');
			var objUrl;
			if(navigator.userAgent.indexOf("MSIE")>0){
			objUrl = this.value;
		}else
			objUrl = getObjectURL(this.files[0]);
			$(obj).attr("src",objUrl);
		});
		setselect();
})
 
 function is_open(a){
	$('#is_open').val(a);
}
 
 function choose_class(i){
	 $(".no-choose").each(function(){
		$(this).removeClass('choose-title');
	});
	
	if(i==1){
		$(".no-choose").eq(i-1).addClass('choose-title');
		$(".choose_a").addClass('choosed');
		$(".choose_b").removeClass('choosed');
		$(".choose_c").removeClass('choosed');
		$(".choose_d").removeClass('choosed');
		$(".WSY_text_input").css('margin-top','0');
		$("#top").show();
		$("#right").hide();
		$("#hot").hide();
		$("#type").hide();
	}
	if(i==2){
		$(".no-choose").eq(i).addClass('choose-title');
		$(".choose_a").removeClass('choosed');
		$(".choose_b").addClass('choosed');
		$(".choose_c").removeClass('choosed');
		$(".choose_d").removeClass('choosed');
		$(".WSY_text_input").css('margin-top','0');
		$("#top").hide();
		$("#right").hide();
		$("#hot").hide();
		$("#type").show();
	}
	if(i==3){
		$(".no-choose").eq(i).addClass('choose-title');
		$(".choose_a").removeClass('choosed');
		$(".choose_b").removeClass('choosed');
		$(".choose_d").removeClass('choosed');
		$(".choose_c").addClass('choosed');
		$(".WSY_text_input").css('margin-top','120px');
		$("#top").hide();
		$("#right").show();
		$("#type").hide();
		$("#hot").hide();
	}
	if(i==4){
		$(".no-choose").eq(i-3).addClass('choose-title');
		$(".choose_a").removeClass('choosed');
		$(".choose_b").removeClass('choosed');
		$(".choose_c").removeClass('choosed');
		$(".choose_d").addClass('choosed');
		$(".WSY_text_input").css('margin-top','120px');
		$("#top").hide();
		$("#right").hide();
		$("#type").hide();
		$("#hot").show();
	}
}
function choose(i,obj){
	$(".no-choose").each(function(){
		$(this).removeClass('choose-title');
	});
	$(obj).addClass('choose-title');

	if(i==1){
		$(".choose_a").addClass('choosed');
		$(".choose_b").removeClass('choosed');
		$(".choose_c").removeClass('choosed');
		$(".choose_d").removeClass('choosed');
		$("#top").show();
		$("#right").hide();
		$("#type").hide();
		$("#hot").hide();
	}
	if(i==2){
		$(".choose_a").removeClass('choosed');
		$(".choose_b").removeClass('choosed');
		$(".choose_c").removeClass('choosed');
		$(".choose_d").addClass('choosed');
		$("#top").hide();
		$("#right").hide();
		$("#hot").show();
		$("#type").hide();
	}
	if(i==3){
		$(".choose_a").removeClass('choosed');
		$(".choose_b").addClass('choosed');
		$(".choose_d").removeClass('choosed');
		$(".choose_c").removeClass('choosed');
		$("#top").hide();
		$("#type").show();
		$("#right").hide();
		$("#hot").hide();
	}
	if(i==4){
		$(".choose_a").removeClass('choosed');
		$(".choose_b").removeClass('choosed');
		$(".choose_c").addClass('choosed');
		$(".choose_d").removeClass('choosed');
		$("#top").hide();
		$("#hot").hide();
		$("#type").hide();
		$("#right").show();
	}

}

</script>
<style>
.checkboxclass{margin-left: 15px;margin-top: 15px;float:left}
* ul li{margin-left: 64px;}
.WSY_memberimg{display: inline;}
.upimg ul li{float: left;width:280px;margin-top: 10px;}
.upimg{width:830px;height: 500px;float: left;margin-top:25px;}
.nav_img img{width: 720px;height: auto;}
.nav_img{margin-top: 50px;margin-left: 50px;float: left;}
.choosed {border: 2px solid red;}
.choose {cursor: pointer;}
.data-left {float: left;}
.data-right {float: left;width: 750px;height: 500px;margin-top: 20px;margin-left: 20px;overflow: auto;border: 1px solid #ccc;}
.choose_a {position: absolute;width: 431px;height: 39px;margin-top: 55px;margin-left: 181px;}
.choose_b {position: absolute;width: 572px;height: 251px;margin-top: 96px;margin-left: 50px;}
.choose_c {position: absolute;width: 142px;height: 292px;margin-top: 52px;margin-left: 626px;}
.choose_d {position: absolute;width: 116px;height: 78px;margin-top: 55px;margin-left: 50px;}
.no-choose {margin-left: 64px;float: left;width: 80px;height: 25px;background-color: #ccc;line-height: 25px;text-align: center;cursor: pointer;margin-top: 5px;}
.choose-title {background-color: #06a7e1 !important;color: #fff;}
.title_nav {width: 90%;height: 40px;margin-left: 5%;margin-top: 10px;border-bottom: 1px dotted #ccc;}
</style>
</head>

<body>
<form id="upform" action="save_category.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">
	<input type=hidden name="keyid" value="<?php echo $keyid; ?>">
	<div class="WSY_content">
		<div class="WSY_columnbox">
		<?php
			include("../../../../weixinpl/back_newshops/PcShop/Base/basic_head.php"); 
			?>
			<div class="WSY_data">
				<div class="choose choose_a" onclick="choose_class(1);"></div>
				<div class="choose choose_b" onclick="choose_class(2);"></div>
				<div class="chosse choose_c" onclick="choose_class(3);"></div>
				<!--<div class="chosse choose_d" onclick="choose_class(4);"></div>-->

				<div class="nav_img data-left">
					<img src="../../Common/images/PcShop/categorie/20170106155704.png" alt="">
				</div>
				
				
	            <div class="WSY_list" id="WSY_list" style="min-height: 500px;">												
					<div class="WSY_remind_main" style="width:200px;">	
					
						<div class="data-right">
							<div class="title_nav">
								<ul>
									<li class="no-choose choose-title" onclick="choose(1,this);">顶端广告图</li>
									<li style="display:none;" class="no-choose" onclick="choose(2,this)";>热门推荐</li>
									<li class="no-choose" onclick="choose(3,this)";>一级分类</li>
									<li class="no-choose" onclick="choose(4,this)";>右侧广告图</li>
								</ul>
							</div>
							<div class="data-info">
							<!--开关按钮-->
							<dl class="WSY_remind_dl02" style="width: 100%;">
								<dt style="margin-left:28px;">分类上架:</dt>
								<dd>
									<?php if($is_open==1){ ?>
										<ul style="background-color: rgb(255, 113, 112);">
											<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
											<li onclick="is_open(0)" class="WSY_bot" style="left: 0px;"></li>
											<span onclick="is_open(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
										</ul>
									<?php }else{ ?>
										<ul style="background-color: rgb(203, 210, 216);">
											<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
											<li onclick="is_open(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
											<span onclick="is_open(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
										</ul>
									<?php } ?>
								</dd>
								<input type="hidden" name="is_open" id="is_open" value="<?php echo $is_open; ?>" />
							</dl>
							<!--开关按钮-->
							
							<dl class="WSY_member" style="display: inline-flex;">
								<dt style="margin-left: 25px;margin-top: 6px;">分类标题：</dt>
								<dd><input type="text" value="<?php echo $title; ?>" name="title" id="title"></dd>
							</dl>
							
							<dl class="WSY_member" style="display: inline-flex;">
								<dt style="margin-left: 25px;margin-top: 6px;">排序：</dt>
								<dd><input type="text" value="<?php echo $sort; ?>" name="sort" id="sort"></dd>
							</dl>
								
							<!-- 分类缩略图 -->
							<div class="upimg" id="abbreviation_img" style="height:auto;">
								<dt style="margin-left:100px;">分类缩略图: <span id="content_tis"></span></dt>
								<ul style="margin-top:20px;margin-left:35px">
									<li>
										<span id="content_tis" class="">建议（长*宽）尺寸为：18*18像素</span>	
										<div class="WSY_memberimg" id="" style="position:relative;background-color:#FF6405;">
											<img src="<?php if(empty($abbreviation_img)){echo "../../Common/images/PcShop/categorie/001.png"; }else{echo $abbreviation_img;} ?>" style="width:39px;height:33px;">
											
											<!--上传文件代码开始-->
											<div class="uploader white">
												<input type="text" class="filename" readonly/>
												<input type="button" name="file" class="button" value="上传..."/>
												<input size="17" name="image7" id="abbreviation_img" class="upfile" type=file value="<?php echo $abbreviation_img ?>">
												<input type=hidden value="<?php echo $abbreviation_img ?>" name="img7" id="abbreviation_image" /> 
											</div>
											<!--上传文件代码结束-->
										</div>
									</li>
								</ul>
							</div>
							<!-- 分类缩略图 -->
		
							<!-- 热门推荐 -->
							<!--<div id="hot" class="upimg" style="display:none;">
								<dl class="WSY_member" style="display: inline-flex;">
									<dt style="margin-left: 25px;margin-top: 6px;">热门推荐一：</dt>
										<select name="search_type_id1" id="search_type_id1" class="hot">
										<option value="">--请选择--</option>
										<?php 
											$parent_id = -1;
											$parent_name = ''; // 顶级分类
											$query = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=-1 AND is_shelves=1";
											$result= _mysql_query($query)or die('Query failed 145: ' . mysql_error());
											while( $row = mysql_fetch_object($result) ){
												$parent_id = $row->id;
												$parent_name = $row->name;								
										?>
										<option value="<?php echo $parent_id;?>" <?php if($popular_array[0] == $parent_id){ echo 'selected';}?> ><?php echo $parent_name;?></option>
											<?php 
												$ch_id2 = -1;
												$ch_name2 = '';// 第二级分类
												$query_c2 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$parent_id AND is_shelves=1";
												$result_c2= _mysql_query($query_c2)or die('Query failed 145: ' . mysql_error());
												while( $row_c2 = mysql_fetch_object($result_c2) ){
													$ch_id2 = $row_c2->id;
													$ch_name2 = $row_c2->name;
													if($ch_id2 != -1){
												
											?>
												<option value="<?php echo $ch_id2;?>" <?php if($popular_array[0] == $ch_id2){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name2;?></option>
													<?php 
														$ch_id3 = -1;
														$ch_name3 = '';// 第三级分类
														$query_c3 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$ch_id2 AND is_shelves=1";
														$result_c3= _mysql_query($query_c3)or die('Query failed 167: ' . mysql_error());
														while( $row_c3 = mysql_fetch_object($result_c3) ){
															$ch_id3 = $row_c3->id;
															$ch_name3 = $row_c3->name;
													?>
													<option value="<?php echo $ch_id3;?>"  <?php if($popular_array[0] == $ch_id3){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name3;?></option>
														<?php 
															$ch_id4 = -1;
															$ch_name4 = '';// 第四级分类
															$query_c4 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$ch_id3 AND is_shelves=1";
															$result_c4= _mysql_query($query_c4)or die('Query failed 167: ' . mysql_error());
															while( $row_c4 = mysql_fetch_object($result_c4) ){
																$ch_id4 = $row_c4->id;
																$ch_name4 = $row_c4->name;
														?>
														<option value="<?php echo $ch_id4;?>"  <?php if($popular_array[0] == $ch_id4){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name4;?></option>
														<?php }?>
													<?php }?>
												<?php }}?>
										<?php }?>
									</select>
								</dl>
								<dl class="WSY_member" style="display: inline-flex;">
									<dt style="margin-left: 25px;margin-top: 6px;">热门推荐二：</dt>
										<select name="search_type_id2" id="search_type_id2" class="hot">
										<option value="">--请选择--</option>
										<?php 
											$parent_id = -1;
											$parent_name = ''; // 顶级分类
											$query = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=-1 AND is_shelves=1";
											$result= _mysql_query($query)or die('Query failed 145: ' . mysql_error());
											while( $row = mysql_fetch_object($result) ){
												$parent_id = $row->id;
												$parent_name = $row->name;								
										?>
										<option value="<?php echo $parent_id;?>" <?php if($popular_array[1] == $parent_id){ echo 'selected';}?> ><?php echo $parent_name;?></option>
											<?php 
												$ch_id2 = -1;
												$ch_name2 = '';// 第二级分类
												$query_c2 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$parent_id AND is_shelves=1";
												$result_c2= _mysql_query($query_c2)or die('Query failed 145: ' . mysql_error());
												while( $row_c2 = mysql_fetch_object($result_c2) ){
													$ch_id2 = $row_c2->id;
													$ch_name2 = $row_c2->name;
													if($ch_id2 != -1){
												
											?>
												<option value="<?php echo $ch_id2;?>" <?php if($popular_array[1] == $ch_id2){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name2;?></option>
													<?php 
														$ch_id3 = -1;
														$ch_name3 = '';// 第三级分类
														$query_c3 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$ch_id2 AND is_shelves=1";
														$result_c3= _mysql_query($query_c3)or die('Query failed 167: ' . mysql_error());
														while( $row_c3 = mysql_fetch_object($result_c3) ){
															$ch_id3 = $row_c3->id;
															$ch_name3 = $row_c3->name;
													?>
													<option value="<?php echo $ch_id3;?>"  <?php if($popular_array[1] == $ch_id3){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name3;?></option>
														<?php 
															$ch_id4 = -1;
															$ch_name4 = '';// 第四级分类
															$query_c4 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$ch_id3 AND is_shelves=1";
															$result_c4= _mysql_query($query_c4)or die('Query failed 167: ' . mysql_error());
															while( $row_c4 = mysql_fetch_object($result_c4) ){
																$ch_id4 = $row_c4->id;
																$ch_name4 = $row_c4->name;
														?>
														<option value="<?php echo $ch_id4;?>"  <?php if($popular_array[1] == $ch_id4){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name4;?></option>
														<?php }?>
													<?php }?>
												<?php }}?>
										<?php }?>
									</select>
								</dl>
								<dl class="WSY_member" style="display: inline-flex;">
									<dt style="margin-left: 25px;margin-top: 6px;">热门推荐三：</dt>
										<select name="search_type_id3" id="search_type_id3" class="hot">
										<option value="">--请选择--</option>
										<?php 
											$parent_id = -1;
											$parent_name = ''; // 顶级分类
											$query = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=-1 AND is_shelves=1";
											$result= _mysql_query($query)or die('Query failed 145: ' . mysql_error());
											while( $row = mysql_fetch_object($result) ){
												$parent_id = $row->id;
												$parent_name = $row->name;								
										?>
										<option value="<?php echo $parent_id;?>" <?php if($popular_array[2] == $parent_id){ echo 'selected';}?> ><?php echo $parent_name;?></option>
											<?php 
												$ch_id2 = -1;
												$ch_name2 = '';// 第二级分类
												$query_c2 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$parent_id AND is_shelves=1";
												$result_c2= _mysql_query($query_c2)or die('Query failed 145: ' . mysql_error());
												while( $row_c2 = mysql_fetch_object($result_c2) ){
													$ch_id2 = $row_c2->id;
													$ch_name2 = $row_c2->name;
													if($ch_id2 != -1){
												
											?>
												<option value="<?php echo $ch_id2;?>" <?php if($popular_array[2] == $ch_id2){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name2;?></option>
													<?php 
														$ch_id3 = -1;
														$ch_name3 = '';// 第三级分类
														$query_c3 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$ch_id2 AND is_shelves=1";
														$result_c3= _mysql_query($query_c3)or die('Query failed 167: ' . mysql_error());
														while( $row_c3 = mysql_fetch_object($result_c3) ){
															$ch_id3 = $row_c3->id;
															$ch_name3 = $row_c3->name;
													?>
													<option value="<?php echo $ch_id3;?>"  <?php if($popular_array[2] == $ch_id3){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name3;?></option>
														<?php 
															$ch_id4 = -1;
															$ch_name4 = '';// 第四级分类
															$query_c4 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$ch_id3 AND is_shelves=1";
															$result_c4= _mysql_query($query_c4)or die('Query failed 167: ' . mysql_error());
															while( $row_c4 = mysql_fetch_object($result_c4) ){
																$ch_id4 = $row_c4->id;
																$ch_name4 = $row_c4->name;
														?>
														<option value="<?php echo $ch_id4;?>"  <?php if($popular_array[2] == $ch_id4){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name4;?></option>
														<?php }?>
													<?php }?>
												<?php }}?>
										<?php }?>
									</select>
								</dl>
								<dl class="WSY_member" style="display: inline-flex;">
									<dt style="margin-left: 25px;margin-top: 6px;">热门推荐四：</dt>
										<select name="search_type_id4" id="search_type_id4" class="hot">
										<option value="">--请选择--</option>
										<?php 
											$parent_id = -1;
											$parent_name = ''; // 顶级分类
											$query = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=-1 AND is_shelves=1";
											$result= _mysql_query($query)or die('Query failed 145: ' . mysql_error());
											while( $row = mysql_fetch_object($result) ){
												$parent_id = $row->id;
												$parent_name = $row->name;								
										?>
										<option value="<?php echo $parent_id;?>" <?php if($popular_array[3] == $parent_id){ echo 'selected';}?> ><?php echo $parent_name;?></option>
											<?php 
												$ch_id2 = -1;
												$ch_name2 = '';// 第二级分类
												$query_c2 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$parent_id AND is_shelves=1";
												$result_c2= _mysql_query($query_c2)or die('Query failed 145: ' . mysql_error());
												while( $row_c2 = mysql_fetch_object($result_c2) ){
													$ch_id2 = $row_c2->id;
													$ch_name2 = $row_c2->name;
													if($ch_id2 != -1){
												
											?>
												<option value="<?php echo $ch_id2;?>" <?php if($popular_array[3] == $ch_id2){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name2;?></option>
													<?php 
														$ch_id3 = -1;
														$ch_name3 = '';// 第三级分类
														$query_c3 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$ch_id2 AND is_shelves=1";
														$result_c3= _mysql_query($query_c3)or die('Query failed 167: ' . mysql_error());
														while( $row_c3 = mysql_fetch_object($result_c3) ){
															$ch_id3 = $row_c3->id;
															$ch_name3 = $row_c3->name;
													?>
													<option value="<?php echo $ch_id3;?>"  <?php if($popular_array[3] == $ch_id3){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name3;?></option>
														<?php 
															$ch_id4 = -1;
															$ch_name4 = '';// 第四级分类
															$query_c4 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$ch_id3 AND is_shelves=1";
															$result_c4= _mysql_query($query_c4)or die('Query failed 167: ' . mysql_error());
															while( $row_c4 = mysql_fetch_object($result_c4) ){
																$ch_id4 = $row_c4->id;
																$ch_name4 = $row_c4->name;
														?>
														<option value="<?php echo $ch_id4;?>"  <?php if($popular_array[3] == $ch_id4){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name4;?></option>
														<?php }?>
													<?php }?>
												<?php }}?>
										<?php }?>
									</select>
								</dl>
								<dl class="WSY_member" style="display: inline-flex;">
									<dt style="margin-left: 25px;margin-top: 6px;">热门推荐五：</dt>
										<select name="search_type_id5" id="search_type_id5" class="hot">
										<option value="">--请选择--</option>
										<?php 
											$parent_id = -1;
											$parent_name = ''; // 顶级分类
											$query = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=-1 AND is_shelves=1";
											$result= _mysql_query($query)or die('Query failed 145: ' . mysql_error());
											while( $row = mysql_fetch_object($result) ){
												$parent_id = $row->id;
												$parent_name = $row->name;								
										?>
										<option value="<?php echo $parent_id;?>" <?php if($popular_array[4] == $parent_id){ echo 'selected';}?> ><?php echo $parent_name;?></option>
											<?php 
												$ch_id2 = -1;
												$ch_name2 = '';// 第二级分类
												$query_c2 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$parent_id AND is_shelves=1";
												$result_c2= _mysql_query($query_c2)or die('Query failed 145: ' . mysql_error());
												while( $row_c2 = mysql_fetch_object($result_c2) ){
													$ch_id2 = $row_c2->id;
													$ch_name2 = $row_c2->name;
													if($ch_id2 != -1){
												
											?>
												<option value="<?php echo $ch_id2;?>" <?php if($popular_array[4] == $ch_id2){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name2;?></option>
													<?php 
														$ch_id3 = -1;
														$ch_name3 = '';// 第三级分类
														$query_c3 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$ch_id2 AND is_shelves=1";
														$result_c3= _mysql_query($query_c3)or die('Query failed 167: ' . mysql_error());
														while( $row_c3 = mysql_fetch_object($result_c3) ){
															$ch_id3 = $row_c3->id;
															$ch_name3 = $row_c3->name;
													?>
													<option value="<?php echo $ch_id3;?>"  <?php if($popular_array[4] == $ch_id3){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name3;?></option>
														<?php 
															$ch_id4 = -1;
															$ch_name4 = '';// 第四级分类
															$query_c4 = "SELECT id,name FROM weixin_commonshop_types WHERE isvalid=true AND customer_id=$customer_id AND parent_id=$ch_id3 AND is_shelves=1";
															$result_c4= _mysql_query($query_c4)or die('Query failed 167: ' . mysql_error());
															while( $row_c4 = mysql_fetch_object($result_c4) ){
																$ch_id4 = $row_c4->id;
																$ch_name4 = $row_c4->name;
														?>
														<option value="<?php echo $ch_id4;?>"  <?php if($popular_array[4] == $ch_id4){ echo 'selected';}?>><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;'.$ch_name4;?></option>
														<?php }?>
													<?php }?>
												<?php }}?>
										<?php }?>
									</select>
								</dl>
							</div>-->
							<!-- 热门推荐 -->
								
							<!-- 顶部广告图 -->
							<div class="upimg" id="top" style="display:none;">
								<dt style="margin-left:100px;">顶部广告图: <span id="content_tis"></span></dt>
								<ul style="margin-top:20px;margin-left:35px">
									<li>
										<span id="content_tis" class="">左侧广告图 建议（长*宽）尺寸为：241*65像素</span>
										<div style="margin-top:10px;">
										
											<select id="foreign_id1" name="foreign_id1" onchange="getproduct(this.options[this.options.selectedIndex].value,1)">

											<optgroup label="---------------产品分类---------------"></optgroup>
											<option value="">------------选择分类------------</option>
											<?php
												for( $i = 0;$i < count($type_arr);$i++ ){
													$typearr = explode("_",$type_arr[$i]);
													$pt_id 	 = $typearr[0];
													$pt_name = $typearr[1];
											 ?>
											<option value="<?php echo $pt_id; ?>_1"><?php echo $pt_name; ?></option>
											<?php
												for( $ii = 0; $ii < count($ctype_arr[$pt_id]);$ii++ ){
													$typearr2 = explode("_",$ctype_arr[$pt_id][$ii]);
													$pc_id 	  = $typearr2[0];
													$pc_name  = $typearr2[1];
											?>
											<option value="<?php echo $pc_id; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name; ?></option>
											<?php
												for( $iii = 0; $iii < count($ctype_arr[$pc_id]);$iii++ ){
													$typearr3 = explode("_",$ctype_arr[$pc_id][$iii]);
													$pc_id3   = $typearr3[0];
													$pc_name3 = $typearr3[1];
											?>
											<option value="<?php echo $pc_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name3; ?></option>
											<?php
												for( $iiii = 0;$iiii < count($ctype_arr[$pc_id3]);$iiii++ ){
													$typearr4 = explode("_",$ctype_arr[$pc_id3][$iiii]);
													$pc_id4   = $typearr4[0];
													$pc_name4 = $typearr4[1];
											?>
											<option value="<?php echo $pc_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name4; ?></option>
											<?php
												}
												}
												}
											} 
											?>


											</select>
											<div class="pro_select1"  id="pro_select1" style="display:block;">
												<select id="detail_id1" name="detail_id1" style="width:160px;margin-top:5px;float:left;">
													
												</select>
											</div>
										</div>	
										<div class="WSY_memberimg" id="" style="position:relative;">
											<img src="<?php if(empty($top_img_arr[0])){echo "../../Common/images/PcShop/categorie/top1.png"; }else{echo $top_img_arr[0];} ?>" style="width:132px;height:36px;">
											
											<!--上传文件代码开始-->
											<div class="uploader white">
												<input type="text" class="filename" readonly/>
												<input type="button" name="file" class="button" value="上传..."/>
												<input size="17" name="image1" id="top_img1" class="upfile" type=file value="<?php echo $top_img_arr[0] ?>">
												<input type=hidden value="<?php echo $top_img_arr[0] ?>" name="img1" id="top_image1" /> 
											</div>
											<!--上传文件代码结束-->
										</div>
									</li>
								</ul>
								<ul style="margin-top:20px;margin-left:35px">
									<li>
										<span id="content_tis" class="">中间广告图 建议（长*宽）尺寸为：241*65像素</span>
										<div style="margin-top:10px;">
										
											<select id="foreign_id2" name="foreign_id2" onchange="getproduct(this.options[this.options.selectedIndex].value,2)">

											<optgroup label="---------------产品分类---------------"></optgroup>
											<option value="">------------选择分类------------</option>
											<?php
												for( $i = 0;$i < count($type_arr);$i++ ){
													$typearr = explode("_",$type_arr[$i]);
													$pt_id 	 = $typearr[0];
													$pt_name = $typearr[1];
											 ?>
											<option value="<?php echo $pt_id; ?>_1"><?php echo $pt_name; ?></option>
											<?php
												for( $ii = 0; $ii < count($ctype_arr[$pt_id]);$ii++ ){
													$typearr2 = explode("_",$ctype_arr[$pt_id][$ii]);
													$pc_id 	  = $typearr2[0];
													$pc_name  = $typearr2[1];
											?>
											<option value="<?php echo $pc_id; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name; ?></option>
											<?php
												for( $iii = 0; $iii < count($ctype_arr[$pc_id]);$iii++ ){
													$typearr3 = explode("_",$ctype_arr[$pc_id][$iii]);
													$pc_id3   = $typearr3[0];
													$pc_name3 = $typearr3[1];
											?>
											<option value="<?php echo $pc_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name3; ?></option>
											<?php
												for( $iiii = 0;$iiii < count($ctype_arr[$pc_id3]);$iiii++ ){
													$typearr4 = explode("_",$ctype_arr[$pc_id3][$iiii]);
													$pc_id4   = $typearr4[0];
													$pc_name4 = $typearr4[1];
											?>
											<option value="<?php echo $pc_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name4; ?></option>
											<?php
												}
												}
												}
											} 
											?>


											</select>
											<div class="pro_select2"  id="pro_select2" style="display:block;">
												<select id="detail_id2" name="detail_id2" style="width:160px;margin-top:5px;float:left;">
													
												</select>
											</div>
										</div>	
										<div class="WSY_memberimg" id="" style="position:relative;">
											<img src="<?php if(empty($top_img_arr[1])){echo "../../Common/images/PcShop/categorie/top2.png"; }else{echo $top_img_arr[1];} ?>" style="width:132px;height:36px;">
											
											<!--上传文件代码开始-->
											<div class="uploader white">
												<input type="text" class="filename" readonly/>
												<input type="button" name="file" class="button" value="上传..."/>
												<input size="17" name="image2" id="top_img2" class="upfile" type=file value="<?php echo $top_img_arr[1] ?>">
												<input type=hidden value="<?php echo $top_img_arr[1] ?>" name="img2" id="top_image2" /> 
											</div>
											<!--上传文件代码结束-->
										</div>
									</li>
								</ul>
								<ul style="margin-top:20px;margin-left:35px">
									<li>
										<span id="content_tis" class="">右侧广告图 建议（长*宽）尺寸为：241*65像素</span>
										<div style="margin-top:10px;">
										
											<select id="foreign_id3" name="foreign_id3" onchange="getproduct(this.options[this.options.selectedIndex].value,3)">

											<optgroup label="---------------产品分类---------------"></optgroup>
											<option value="">------------选择分类------------</option>
											<?php
												for( $i = 0;$i < count($type_arr);$i++ ){
													$typearr = explode("_",$type_arr[$i]);
													$pt_id 	 = $typearr[0];
													$pt_name = $typearr[1];
											 ?>
											<option value="<?php echo $pt_id; ?>_1"><?php echo $pt_name; ?></option>
											<?php
												for( $ii = 0; $ii < count($ctype_arr[$pt_id]);$ii++ ){
													$typearr2 = explode("_",$ctype_arr[$pt_id][$ii]);
													$pc_id 	  = $typearr2[0];
													$pc_name  = $typearr2[1];
											?>
											<option value="<?php echo $pc_id; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name; ?></option>
											<?php
												for( $iii = 0; $iii < count($ctype_arr[$pc_id]);$iii++ ){
													$typearr3 = explode("_",$ctype_arr[$pc_id][$iii]);
													$pc_id3   = $typearr3[0];
													$pc_name3 = $typearr3[1];
											?>
											<option value="<?php echo $pc_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name3; ?></option>
											<?php
												for( $iiii = 0;$iiii < count($ctype_arr[$pc_id3]);$iiii++ ){
													$typearr4 = explode("_",$ctype_arr[$pc_id3][$iiii]);
													$pc_id4   = $typearr4[0];
													$pc_name4 = $typearr4[1];
											?>
											<option value="<?php echo $pc_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name4; ?></option>
											<?php
												}
												}
												}
											} 
											?>


											</select>
											<div class="pro_select3"  id="pro_select3" style="display:block;">
												<select id="detail_id3" name="detail_id3" style="width:160px;margin-top:5px;float:left;">
													
												</select>
											</div>
										</div>	
										<div class="WSY_memberimg" id="" style="position:relative;">
											<img src="<?php if(empty($top_img_arr[2])){echo "../../Common/images/PcShop/categorie/top3.png"; }else{echo $top_img_arr[2];} ?>" style="width:132px;height:36px;">
											
											<!--上传文件代码开始-->
											<div class="uploader white">
												<input type="text" class="filename" readonly/>
												<input type="button" name="file" class="button" value="上传..."/>
												<input size="17" name="image3" id="top_img3" class="upfile" type=file value="<?php echo $top_img_arr[2] ?>">
												<input type=hidden value="<?php echo $top_img_arr[2] ?>" name="img3" id="top_image3" /> 
											</div>
											<!--上传文件代码结束-->
										</div>
									</li>
								</ul>
							</div>
							<!-- 顶部广告图 -->
							
							
							<!-- 右侧广告图 -->
							<div class="upimg" id="right"  style="display:none;">
								<dt style="margin-left:100px;">右侧广告图: <span id="content_tis"></span></dt>
								<ul style="margin-top:20px;margin-left:35px">
									<li>
										<span id="content_tis" class="">(上层广告图)建议（长*宽）尺寸为：219*149像素</span>
										<div style="margin-top:10px;">
										
											<select id="foreign_id4" name="foreign_id4" onchange="getproduct(this.options[this.options.selectedIndex].value,4)">

											<optgroup label="---------------产品分类---------------"></optgroup>
											<option value="">------------选择分类------------</option>
											<?php
												for( $i = 0;$i < count($type_arr);$i++ ){
													$typearr = explode("_",$type_arr[$i]);
													$pt_id 	 = $typearr[0];
													$pt_name = $typearr[1];
											 ?>
											<option value="<?php echo $pt_id; ?>_1"><?php echo $pt_name; ?></option>
											<?php
												for( $ii = 0; $ii < count($ctype_arr[$pt_id]);$ii++ ){
													$typearr2 = explode("_",$ctype_arr[$pt_id][$ii]);
													$pc_id 	  = $typearr2[0];
													$pc_name  = $typearr2[1];
											?>
											<option value="<?php echo $pc_id; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name; ?></option>
											<?php
												for( $iii = 0; $iii < count($ctype_arr[$pc_id]);$iii++ ){
													$typearr3 = explode("_",$ctype_arr[$pc_id][$iii]);
													$pc_id3   = $typearr3[0];
													$pc_name3 = $typearr3[1];
											?>
											<option value="<?php echo $pc_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name3; ?></option>
											<?php
												for( $iiii = 0;$iiii < count($ctype_arr[$pc_id3]);$iiii++ ){
													$typearr4 = explode("_",$ctype_arr[$pc_id3][$iiii]);
													$pc_id4   = $typearr4[0];
													$pc_name4 = $typearr4[1];
											?>
											<option value="<?php echo $pc_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name4; ?></option>
											<?php
												}
												}
												}
											} 
											?>


											</select>
											<div class="pro_select4"  id="pro_select4" style="display:block;">
												<select id="detail_id4" name="detail_id4" style="width:160px;margin-top:5px;float:left;height:19px;">
													
												</select>
											</div>
										</div>
										<div class="WSY_memberimg" id="" style="position:relative;">
											
											<img src="<?php if(empty($right_img_arr[0])){echo "../../Common/images/PcShop/categorie/right1.png"; }else{echo $right_img_arr[0];} ?>" style="width:219px;height:149px;">
											
											<!--上传文件代码开始-->
											<div class="uploader white">
												<input type="text" class="filename" readonly/>
												<input type="button" name="file" class="button" value="上传..."/>
												<input size="17" name="image4" id="right4" type=file class="upfile" value="<?php echo $right_img_arr[0]; ?>">
												<input type=hidden value="<?php echo $right_img_arr[0]; ?>" name="img4" id="right_img4" /> 
											</div>
											<!--上传文件代码结束-->
										</div>
									</li>
								</ul>	
								
								<ul style="margin-top:20px;margin-left:35px">
									<li>
										<span id="content_tis" class="">(中层广告图)建议（长*宽）尺寸为：219*149像素</span>
										<div style="margin-top:10px;">
										
											<select id="foreign_id5" name="foreign_id5" onchange="getproduct(this.options[this.options.selectedIndex].value,5)">

											<optgroup label="---------------产品分类---------------"></optgroup>
											<option value="">------------选择分类------------</option>
											<?php
												for( $i = 0;$i < count($type_arr);$i++ ){
													$typearr = explode("_",$type_arr[$i]);
													$pt_id 	 = $typearr[0];
													$pt_name = $typearr[1];
											 ?>
											<option value="<?php echo $pt_id; ?>_1"><?php echo $pt_name; ?></option>
											<?php
												for( $ii = 0; $ii < count($ctype_arr[$pt_id]);$ii++ ){
													$typearr2 = explode("_",$ctype_arr[$pt_id][$ii]);
													$pc_id 	  = $typearr2[0];
													$pc_name  = $typearr2[1];
											?>
											<option value="<?php echo $pc_id; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name; ?></option>
											<?php
												for( $iii = 0; $iii < count($ctype_arr[$pc_id]);$iii++ ){
													$typearr3 = explode("_",$ctype_arr[$pc_id][$iii]);
													$pc_id3   = $typearr3[0];
													$pc_name3 = $typearr3[1];
											?>
											<option value="<?php echo $pc_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name3; ?></option>
											<?php
												for( $iiii = 0;$iiii < count($ctype_arr[$pc_id3]);$iiii++ ){
													$typearr4 = explode("_",$ctype_arr[$pc_id3][$iiii]);
													$pc_id4   = $typearr4[0];
													$pc_name4 = $typearr4[1];
											?>
											<option value="<?php echo $pc_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name4; ?></option>
											<?php
												}
												}
												}
											} 
											?>


											</select>
											<div class="pro_select5"  id="pro_select5" style="display:block;">
												<select id="detail_id5" name="detail_id5" style="width:160px;margin-top:5px;float:left;height:19px;">
													
												</select>
											</div>
										</div>
										<div class="WSY_memberimg" id="" style="position:relative;">
											<img src="<?php if(empty($right_img_arr[1])){echo "../../Common/images/PcShop/categorie/right2.jpg"; }else{echo $right_img_arr[1];} ?>" style="width:219px;height:149px;">
											
											<!--上传文件代码开始-->
											<div class="uploader white">
												<input type="text" class="filename" readonly/>
												<input type="button" name="file" class="button" value="上传..."/>
												<input size="17" name="image5" id="right2" type=file class="upfile" value="<?php echo $right_img_arr[1]; ?>">
												<input type=hidden value="<?php echo $right_img_arr[1]; ?>" name="img5" id="right_img2" /> 
											</div>
											<!--上传文件代码结束-->
										</div>
									</li>
								</ul>	
								
								<ul style="margin-top:20px;margin-left:35px">
									<li>
										<span id="content_tis" class="">(下层广告图)建议（长*宽）尺寸为：219*149像素</span>
										<div style="margin-top:10px;">
										
											<select id="foreign_id6" name="foreign_id6" onchange="getproduct(this.options[this.options.selectedIndex].value,6)">

											<optgroup label="---------------产品分类---------------"></optgroup>
											<option value="">------------选择分类------------</option>
											<?php
												for( $i = 0;$i < count($type_arr);$i++ ){
													$typearr = explode("_",$type_arr[$i]);
													$pt_id 	 = $typearr[0];
													$pt_name = $typearr[1];
											 ?>
											<option value="<?php echo $pt_id; ?>_1"><?php echo $pt_name; ?></option>
											<?php
												for( $ii = 0; $ii < count($ctype_arr[$pt_id]);$ii++ ){
													$typearr2 = explode("_",$ctype_arr[$pt_id][$ii]);
													$pc_id 	  = $typearr2[0];
													$pc_name  = $typearr2[1];
											?>
											<option value="<?php echo $pc_id; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name; ?></option>
											<?php
												for( $iii = 0; $iii < count($ctype_arr[$pc_id]);$iii++ ){
													$typearr3 = explode("_",$ctype_arr[$pc_id][$iii]);
													$pc_id3   = $typearr3[0];
													$pc_name3 = $typearr3[1];
											?>
											<option value="<?php echo $pc_id3; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name3; ?></option>
											<?php
												for( $iiii = 0;$iiii < count($ctype_arr[$pc_id3]);$iiii++ ){
													$typearr4 = explode("_",$ctype_arr[$pc_id3][$iiii]);
													$pc_id4   = $typearr4[0];
													$pc_name4 = $typearr4[1];
											?>
											<option value="<?php echo $pc_id4; ?>_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $pc_name4; ?></option>
											<?php
												}
												}
												}
											} 
											?>


											</select>
											<div class="pro_select6"  id="pro_select6" style="display:block;">
												<select id="detail_id6" name="detail_id6" style="width:160px;margin-top:5px;float:left;height:19px;">
													
												</select>
											</div>
										</div>
										<div class="WSY_memberimg" id="" style="position:relative;">
											<img src="<?php if(empty($right_img_arr[2])){echo "../../Common/images/PcShop/categorie/right3.png"; }else{echo $right_img_arr[2];} ?>" style="width:219px;height:149px;">
											
											<!--上传文件代码开始-->
											<div class="uploader white">
												<input type="text" class="filename" readonly/>
												<input type="button" name="file" class="button" value="上传..."/>
												<input size="17" name="image6" id="right3" type=file class="upfile" value="<?php echo $right_img_arr[2]; ?>">
												<input type=hidden value="<?php echo $right_img_arr[2]; ?>" name="img6" id="right_img3" /> 
											</div>
											<!--上传文件代码结束-->
										</div>
									</li>
								</ul>
							</div>
							<!-- 右侧广告图 -->
						

							<!-- 勾选一级分类 -->		
							<div class="upimg" id="type" style="padding:50px;width: 400px;">
								<div style="height: 37px;">
									<span style="font-size: 16px;">勾选一级分类：</span>
									
								</div>
								<div style="height: 37px;">
									
									<span style="color:red;font-size: 16px;">(注意：一级分类展示窗口空间有限，建议勾选分类控制在10个内)</span>
								</div>
								<div style="margin-left: 50px;">
									<?php 
										$type_id   = -1;//类型ID
										$type_name = '';//类型名字
										while($row_type = mysql_fetch_object($result_type)){
											$type_id   = $row_type->id;
											$type_name = $row_type->name;
											
									?>
										<div class="checkboxclass">
											<input type="checkbox" name="categorie_id[]" <?php if(in_array($type_id,$type_array)){echo "checked";} ?>  value="<?php echo $type_id; ?>"/><span><?php echo $type_name; ?></span>
										</div>
									<?php } ?>
								</div>
							</div>
							<!-- 勾选一级分类 -->
						
							</div>
						</div>
					</div>
					<div class="WSY_text_input01" style="width:300px;">
						<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
						<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
					</div>			
				</div>
			</div>
			<div style="width:100%;height:20px;"></div>
		</div>
	</div>
</form>	
<script type="text/javascript" src="../../Common/js/Base/mall_setting/ToolTip.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script>
function setselect(){
	var foreign_id1='<?php echo $foreign_id[0]?>';
	var foreign_id2='<?php echo $foreign_id[1]?>';
	var foreign_id3='<?php echo $foreign_id[2]?>';
	var foreign_id4='<?php echo $foreign_id[3]?>';
	var foreign_id5='<?php echo $foreign_id[4]?>';
	var foreign_id6='<?php echo $foreign_id[5]?>';

	var detail_id1='<?php echo $detail_id[0]?>';
	var detail_id2='<?php echo $detail_id[1]?>';
	var detail_id3='<?php echo $detail_id[2]?>';
	var detail_id4='<?php echo $detail_id[3]?>';
	var detail_id5='<?php echo $detail_id[4]?>';
	var detail_id6='<?php echo $detail_id[5]?>';


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
	var sobj6= document.getElementById("foreign_id6");
	var options6 = sobj6.options;
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
		if(ov==foreign_id1){
			var dd =options1[j].selected;
			options1[j].selected ="selected";
			if(foreign_id1>0){
				//产品分类才显示出 选择产品，图文不需要
				document.getElementById("pro_select1").style.display="block";
			}
			if(ov==foreign_id1){
				options1[j].selected ="selected";
			}
		
			
			if(detail_id1>0){
				changeProductType21(foreign_id1,detail_id1); 
			}else{
				changeProductType21(foreign_id1,-1); 
			}
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
		if(ov==foreign_id2){
			var dd =options2[j].selected;
			options2[j].selected ="selected";
			if(foreign_id2>0){
				//产品分类才显示出 选择产品，图文不需要
				document.getElementById("pro_select2").style.display="block";
			}
			if(detail_id2>0){
				changeProductType22(foreign_id2,detail_id2); 
			}else{
				changeProductType22(foreign_id2,-1); 
			}
			if(ov==foreign_id2){
				options2[j].selected ="selected";
			}
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
		if(ov==foreign_id3){
			var dd =options3[j].selected;
			options3[j].selected ="selected";
			if(foreign_id3>0){
				//产品分类才显示出 选择产品，图文不需要
				document.getElementById("pro_select3").style.display="block";
			}
			if(detail_id3>0){
				changeProductType23(foreign_id3,detail_id3); 
			}else{
				changeProductType23(foreign_id3,-1); 
			}
			if(ov==foreign_id3){
				options3[j].selected ="selected";
			}
		}
	}

	for(var j=0;j<options4.length;j++){
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
		if(ov==foreign_id4){
			var dd =options4[j].selected;
			options4[j].selected ="selected";
			if(foreign_id4>0){
				//产品分类才显示出 选择产品，图文不需要
				document.getElementById("pro_select4").style.display="block";
			}

			if(detail_id4>0){
				changeProductType24(foreign_id4,detail_id4); 
			}else{
				changeProductType24(foreign_id4,-1); 
			}
			if(ov==foreign_id4){
				options4[j].selected ="selected";
			}
		}
	}	
	for(var j=0;j<options5.length;j++){
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
		if(ov==foreign_id5){
			var dd =options5[j].selected;
			options5[j].selected ="selected";
			if(foreign_id5>0){
				//产品分类才显示出 选择产品，图文不需要
				document.getElementById("pro_select5").style.display="block";
			}
			if(detail_id5>0){
				changeProductType25(foreign_id5,detail_id5); 
			}else{
				changeProductType25(foreign_id5,-1); 
			}
			if(ov==foreign_id5){
				options5[j].selected ="selected";
			}
		}
	}	
	for(var j=0;j<options6.length;j++){
		document.getElementById("pro_select6").style.display="block";
		var ov = options6[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==foreign_id6){
			var dd =options6[j].selected;
			options6[j].selected ="selected";
			if(foreign_id6>0){
				//产品分类才显示出 选择产品，图文不需要
				document.getElementById("pro_select6").style.display="block";
			}
			if(detail_id6>0){
				changeProductType26(foreign_id6,detail_id6); 
			}else{
				changeProductType26(foreign_id6,-1); 
			}
			if(ov==foreign_id6){
				options6[j].selected ="selected";
			}
		}
	}
}


function getproduct(typeid,num){
//	console.log(typeid);
	//alert(typeid);
	var typearr= new Array(); 
	typearr=typeid.split("_");
	if(typearr[1]==1){//表示是产品分类			
		url='get_product_list1.php?callback=jsonpCallback_get_product_list&type_id='+typearr[0]+'&num='+num;
		 $.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_get_product_list'
		});		
		$("#pro_select"+num).css("display","block");
	}else{		
		$("#pro_select"+num).hide();
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
		var sel_pro6 = document.getElementById("detail_id6");
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
		}else if(results[2].num==6){
			sel_pro6.options.length=0;
			var new_option6 = new Option("---请选择一个产品---",-1);
			sel_pro6.options.add(new_option6);
		}
		if(len==3){
			switch(results[2].num){
				case 1:
					var new_option11 = new Option("---无相关产品信息---",-2);
					sel_pro1.options.add(new_option11);
					break;
				case 2:
					var new_option12 = new Option("---无相关产品信息---",-2);
					sel_pro2.options.add(new_option12);
					break;
				case 3:
					var new_option13 = new Option("---无相关产品信息---",-2);
					sel_pro3.options.add(new_option13);
					break;
				case 4:
					var new_option14 = new Option("---无相关产品信息---",-2);
					sel_pro4.options.add(new_option14);
					break;
				case 5:
					var new_option15 = new Option("---无相关产品信息---",-2);
					sel_pro5.options.add(new_option15);
					break;
				case 6:
					var new_option16 = new Option("---无相关产品信息---",-2);
					sel_pro6.options.add(new_option16);
					break;
			}
		}	
		for(i=3;i<len;i++){
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
			}else if(results[2].num==5){
				var new_option5 = new Option(pname,pid);
				sel_pro5.options.add(new_option5);
				if(pid==detail_id5){
					new_option5.selected=true;
				}
			}else if(results[2].num==6){
				var new_option6 = new Option(pname,pid);
				sel_pro6.options.add(new_option6);
				if(pid==detail_id6){
					new_option6.selected=true;
				}
			}	
		}


	}

function changeProductType21(pro_typeid,d_id){   //执行edit时候

	 p_detail_id1 = d_id;
	 //是产品分类
	 url='get_product_list1.php?callback=jsonpCallback_get_product_list21&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list21'
	});
	
 // }
}

function changeProductType22(pro_typeid,d_id){   //执行edit时候

	 p_detail_id2 = d_id;
	 //是产品分类
	 url='get_product_list1.php?callback=jsonpCallback_get_product_list22&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list22'
	});
	
 // }
}

function changeProductType23(pro_typeid,d_id){   //执行edit时候

	 p_detail_id3 = d_id;
	 //是产品分类
	 url='get_product_list1.php?callback=jsonpCallback_get_product_list23&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list23'
	});
	
 // }
}

function changeProductType24(pro_typeid,d_id){   //执行edit时候

	 p_detail_id4 = d_id;
	 //是产品分类
	 url='get_product_list1.php?callback=jsonpCallback_get_product_list24&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list24'
	});
	
 // }
}

function changeProductType25(pro_typeid,d_id){   //执行edit时候

	 p_detail_id5 = d_id;
	 //是产品分类
	 url='get_product_list1.php?callback=jsonpCallback_get_product_list25&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list25'
	});
	
 // }
}

function changeProductType26(pro_typeid,d_id){   //执行edit时候

	 p_detail_id6 = d_id;
	 //是产品分类
	 url='get_product_list1.php?callback=jsonpCallback_get_product_list26&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list26'
	});
	
 // }
}

function jsonpCallback_get_product_list21(results){
	var len = results.length;
	//alert("哈哈"+len);
	var sel_pro1 = document.getElementById("detail_id1");
	sel_pro1.options.length=0;
	var new_option1 = new Option("---请选择一个产品---",-1);
	sel_pro1.options.add(new_option1);
	for(i=3;i<len;i++){
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
	for(i=3;i<len;i++){
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
	for(i=3;i<len;i++){
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
	for(i=3;i<len;i++){
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
	for(i=3;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option5 = new Option(pname,pid);
		sel_pro5.options.add(new_option5);
		if(pid==p_detail_id5){
			new_option5.selected=true;
		}
	}   
}

function jsonpCallback_get_product_list26(results){
	var len = results.length;
	var sel_pro6 = document.getElementById("detail_id6");
	sel_pro6.options.length=0;
	var new_option6 = new Option("---请选择一个产品---",-1);
	sel_pro6.options.add(new_option6);
	for(i=3;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option6 = new Option(pname,pid);
		sel_pro6.options.add(new_option6);
		if(pid==p_detail_id6){
			new_option6.selected=true;
		}
	}   
}


</script>
</body>
</html>