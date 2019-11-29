<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');		
require('../../../../weixinpl/proxy_info.php');
//require('../../../../auth_user.php');
_mysql_query("SET NAMES UTF8");		

$head=5;
$adimg            = array();
$detail_id_array  = array();
$foreign_id_array = array();
$link_type_array  = array();
$ids 			  = array();
$query = "select id,brand_adimg,brand_ad_foreign_id,brand_ad_detail_id,brand_linktype,types from weixin_commonshop_supply_album where isvalid=true and supply_id=-1 and customer_id=".$customer_id." order by id desc limit 5";
$result = _mysql_query($query) or die("Query failed : ".mysql_error());
while($row = mysql_fetch_object($result)){
	$types = $row->types;
	switch($types){
		case 1:
			$ids[0]   				= $row->id;
			$adimg[0]           = $row->brand_adimg;
			$foreign_id_array[0]= $row->brand_ad_foreign_id;
			$detail_id_array[0] = $row->brand_ad_detail_id;
			$link_type_array[0] = $row->brand_linktype;
			break;
		case 2:
			$ids[1]   			    = $row->id;
			$adimg[1]           = $row->brand_adimg;
			$foreign_id_array[1]= $row->brand_ad_foreign_id;
			$detail_id_array[1] = $row->brand_ad_detail_id;
			$link_type_array[1] = $row->brand_linktype;
			break;
		case 3:
			$ids[2]   			    = $row->id;
			$adimg[2]           = $row->brand_adimg;
			$foreign_id_array[2]= $row->brand_ad_foreign_id;
			$detail_id_array[2] = $row->brand_ad_detail_id;
			$link_type_array[2] = $row->brand_linktype;
			break;
		case 4:
			$ids[3] 			    = $row->id;
			$adimg[3]           = $row->brand_adimg;
			$foreign_id_array[3]= $row->brand_ad_foreign_id;
			$detail_id_array[3] = $row->brand_ad_detail_id;
			$link_type_array[3] = $row->brand_linktype;
			break;
		case 5:
			$ids[4]  			    = $row->id;
			$adimg[4]           = $row->brand_adimg;
			$foreign_id_array[4]= $row->brand_ad_foreign_id;
			$detail_id_array[4] = $row->brand_ad_detail_id;
			$link_type_array[4] = $row->brand_linktype;
			break;
	}
}
 
//分类链接
/*
$typearr=[];
$tuwenarr=[];
$query="select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $pt_id = $row->id;
   $pt_name = $row->name;
   $typearr[] = $pt_id."_".$pt_name;
}
*/

$query = "select id, name from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=-1 and customer_id=".$customer_id;

if( $sort_str ){
	$query .= ' order by field(id'.$sort_str.')';  
}
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

//图文信息
$query = 'SELECT id,title FROM weixin_subscribes where isvalid=true and parent_id=-1 and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	  $sub_id =  $row->id ;
	  $title = $row->title;
	  $tuwenarr[] = $sub_id."_".$title;
}

$fixedarr[]="-1_---------------请选择---------------";
$fixedarr[]="-6_全部产品";
$fixedarr[]="-2_新品上市";
$fixedarr[]="-3_热卖产品";
$fixedarr[]="-4_购物车";
$fixedarr[]="-8_个人中心";
$fixedarr[]="-7_产品分类页";
$fixedarr[]="-5_限时抢购";
$fixedarr[]="-10_商城在线客服";
$fixedarr[]="-11_礼包列表";
$fixedarr[]="-12_VP产品";

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>合作商-幻灯片管理</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/personal_center/personal_center.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/mall_setting/setting.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
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
});	
</script>
</head>
	
<body>
<form id="upform" action="save_album.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="id1" value="<?php echo $ids[0] ;?>">
	<input type="hidden" name="id2" value="<?php echo $ids[1] ;?>">
	<input type="hidden" name="id3" value="<?php echo $ids[2] ;?>">
	<input type="hidden" name="id4" value="<?php echo $ids[3] ;?>">
	<input type="hidden" name="id5" value="<?php echo $ids[4] ;?>">

	<div class="WSY_content">
		<div class="WSY_columnbox">

		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/supplier/basic_head.php"); 
		?>		
		<div class="WSY_data">

              <div class="WSY_list" id="WSY_list" style="min-height: 500px;">
				<div class="WSY_remind_main" style="height:400px;">		

					<dl class="" id="" style="height:300px;position:absolute;top:115px;" > 
					<dt style="line-height:20px;" >广告图图片1：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:90px;">
						
							<select id="foreign_id1" name="foreign_id1" onchange="getproduct(this.options[this.options.selectedIndex].value,1)">
							<?php 
								for($i=0;$i<count($fixedarr);$i++){
									$fixedstr=explode("_",$fixedarr[$i]);
							?>	  
								<option value="<?php echo $fixedstr[0];?>" ><?php echo $fixedstr[1];?></option>
							<?php 	
								}
							?>

							<optgroup label="---------------产品分类---------------"></optgroup>
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
							
							<optgroup label="---------------图文消息---------------"></optgroup>
							<?php 
								for($i=0;$i<count($tuwenarr);$i++){
									$tuwenstr=explode("_",$tuwenarr[$i]);
							?>	  
								<option value="<?php echo $tuwenstr[0];?>_2" ><?php echo $tuwenstr[1];?></option>
							<?php 	
								}
							?>

							</select>
							<div class="pro_select1"  id="pro_select1" style="display:block;">
								<select id="detail_id1" name="detail_id1" style="width:160px;margin-top:5px;float:left;">
									
								</select>
							</div>
							<!-- </span> -->
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>							
					</dl>
					<iframe id="frm_typeimg" src="supplier_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $adimg[0]; ?>&id=<?php echo $id; ?>&num=1" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:187px;left:60px;">
								
					</iframe>
					
					<dl class="" id="" style="height:300px;position:absolute;top:115px;left:460px;" > 
					<dt style="line-height:20px;" >广告图图片2：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:90px;">
						
							<select id="foreign_id2" name="foreign_id2" onchange="getproduct(this.options[this.options.selectedIndex].value,2)">
							<?php 
								for($i=0;$i<count($fixedarr);$i++){
									$fixedstr=explode("_",$fixedarr[$i]);
							?>	  
								<option value="<?php echo $fixedstr[0];?>" ><?php echo $fixedstr[1];?></option>
							<?php 	
								}
							?>

							<optgroup label="---------------产品分类---------------"></optgroup>
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
							<optgroup label="---------------图文消息---------------"></optgroup>
							<?php 
								for($i=0;$i<count($tuwenarr);$i++){
									$tuwenstr=explode("_",$tuwenarr[$i]);
							?>	  
								<option value="<?php echo $tuwenstr[0];?>_2" ><?php echo $tuwenstr[1];?></option>
							<?php 	
								}
							?>
							</select>
							<div class="pro_select2"  id="pro_select2" style="display:block;">
								<select id="detail_id2" name="detail_id2" style="width:160px;margin-top:5px;float:left;">
									
								</select>
							</div>
							<!-- </span> -->
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>						
					
					</dl>
					<iframe id="frm_typeimg" src="supplier_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $adimg[1]; ?>&id=<?php echo $id; ?>&num=2" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width: 290px;height: 250px;border: solid 1px #d0d0d0;position: absolute;top: 187px;left: 481px;"></iframe>
					

					
					<dl class="" id="" style="height:300px;position:absolute;top:444px;" > 
					<dt style="line-height:20px;" >广告图图片3：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:90px;">
						
							<select id="foreign_id3" name="foreign_id3" onchange="getproduct(this.options[this.options.selectedIndex].value,3)">
							<?php 
								for($i=0;$i<count($fixedarr);$i++){
									$fixedstr=explode("_",$fixedarr[$i]);
							?>	  
								<option value="<?php echo $fixedstr[0];?>" ><?php echo $fixedstr[1];?></option>
							<?php 	
								}
							?>

							<optgroup label="---------------产品分类---------------"></optgroup>
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
							<optgroup label="---------------图文消息---------------"></optgroup>
							<?php 
								for($i=0;$i<count($tuwenarr);$i++){
									$tuwenstr=explode("_",$tuwenarr[$i]);
							?>	  
								<option value="<?php echo $tuwenstr[0];?>_2" ><?php echo $tuwenstr[1];?></option>
							<?php 	
								}
							?>
							</select>
							<div class="pro_select3"  id="pro_select3" style="display:block;">
								<select id="detail_id3" name="detail_id3" style="width:160px;margin-top:5px;float:left;">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>										
					
					</dl>
					<iframe id="frm_typeimg" src="supplier_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $adimg[2]; ?>&id=<?php echo $id; ?>&num=3" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:512px;left:60px;"></iframe>


					<dl class="" id="" style="height:300px;position:absolute;top:444px;left: 460px;" > 
					<dt style="line-height:20px;" >广告图图片4：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:90px;">
						
							<select id="foreign_id4" name="foreign_id4" onchange="getproduct(this.options[this.options.selectedIndex].value,4)">
							<?php 
								for($i=0;$i<count($fixedarr);$i++){
									$fixedstr=explode("_",$fixedarr[$i]);
							?>	  
								<option value="<?php echo $fixedstr[0];?>" ><?php echo $fixedstr[1];?></option>
							<?php 	
								}
							?>

							<optgroup label="---------------产品分类---------------"></optgroup>
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
							<optgroup label="---------------图文消息---------------"></optgroup>
							<?php 
								for($i=0;$i<count($tuwenarr);$i++){
									$tuwenstr=explode("_",$tuwenarr[$i]);
							?>	  
								<option value="<?php echo $tuwenstr[0];?>_2" ><?php echo $tuwenstr[1];?></option>
							<?php 	
								}
							?>
							</select>
							<div class="pro_select4"  id="pro_select4" style="display:block;">
								<select id="detail_id4" name="detail_id4" style="width:160px;margin-top:5px;float:left;">
									
								</select>
							</div>
							<!-- </span> -->
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>						
					
					</dl>
					<iframe id="frm_typeimg" src="supplier_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $adimg[3]; ?>&id=<?php echo $id; ?>&num=4" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:512px;left: 481px;"></iframe>
					

					
					<dl class="" id="" style="height:300px;position:absolute;top:769px;" > 
					<dt style="line-height:20px;" >广告图图片5：</dt>
						
						<div style="height:78px;display:block;position:absolute;top:2px;left:90px;">
						
							<select id="foreign_id5" name="foreign_id5" onchange="getproduct(this.options[this.options.selectedIndex].value,5)">
							<?php 
								for($i=0;$i<count($fixedarr);$i++){
									$fixedstr=explode("_",$fixedarr[$i]);
							?>	  
								<option value="<?php echo $fixedstr[0];?>" ><?php echo $fixedstr[1];?></option>
							<?php 	
								}
							?>

							<optgroup label="---------------产品分类---------------"></optgroup>
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
							<optgroup label="---------------图文消息---------------"></optgroup>
							<?php 
								for($i=0;$i<count($tuwenarr);$i++){
									$tuwenstr=explode("_",$tuwenarr[$i]);
							?>	  
								<option value="<?php echo $tuwenstr[0];?>_2" ><?php echo $tuwenstr[1];?></option>
							<?php 	
								}
							?>
							</select>
							<div class="pro_select5"  id="pro_select5" style="display:block;">
								<select id="detail_id5" name="detail_id5" style="width:160px;margin-top:5px;float:left;">
									
								</select>
							</div>
							<!-- </span> -->
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						</div>						
					
					</dl>
					<iframe id="frm_typeimg" src="supplier_adimg.php?customer_id=<?php echo $customer_id_en; ?>&microshop_adimg=<?php echo $adimg[4]; ?>&id=<?php echo $id; ?>&num=5" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:838px;left:60px;"></iframe>

				
					<!-- <input type="hidden" name="isOpenMicroshopAd" id="isOpenMicroshopAd" value="<?php echo $isOpenMicroshopAd; ?>" /> -->
					<input type="hidden" name="id" id="id" value="<?php echo $id;?>">
					<!-- <input type="hidden" name="is_microshopData" id="is_microshopData" value="<?php echo $is_microshopData; ?>" />
					<input type="hidden" name="is_mandatoryAD" id="is_mandatoryAD" value="<?php echo $is_mandatoryAD; ?>" />
					<input type="hidden" name="is_microshop" id="is_microshop" value="<?php echo $is_microshop; ?>" /> -->
					<input type="hidden" name="adimg1" id="adimg1" value="<?php echo $adimg[0] ; ?>" />
					<input type="hidden" name="adimg2" id="adimg2" value="<?php echo $adimg[1] ; ?>" />
					<input type="hidden" name="adimg3" id="adimg3" value="<?php echo $adimg[2] ; ?>" />
					<input type="hidden" name="adimg4" id="adimg4" value="<?php echo $adimg[3] ; ?>" />
					<input type="hidden" name="adimg5" id="adimg5" value="<?php echo $adimg[4] ; ?>" />
					<div style="clear:both"></div>
					<!-- </div></div></div></div></div> -->
							   
				  <!-- </form> -->
				</div>

				<div class="WSY_text_input01" style="margin-left: 44%;margin-top:639px;">
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
				</div>	

			</div>
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	</div>
</form>	
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script>
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
	//alert(foreign_id2);	
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

	if(type_linktype1 > 0){		
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
		if(type_linktype1==2){
			$("#pro_select1").hide();
		}

	}else if(foreign_id1 < 0){
		//alert(foreign_id1);
		for(var j=0;j<11;j++){  //每添加一个链接， J需要+1
			var ov = options1[j].value;			
			if(ov==foreign_id1){
				options1[j].selected ="selected";
			}
			
		}
		//alert(foreign_id1);	
		$("#pro_select1").hide();
	}else{
		options1[0].selected ="selected";					
		$("#pro_select1").hide();
	}
	
	
	if(type_linktype2 > 0){
		
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
						changeProductType22(foreign_id2,-1); 
					}
				}else{
				  options2[j].selected ="selected";
				 
				}
				break;
			}	
		}
		if(type_linktype2==2){
			$("#pro_select2").hide();
		}

	}else if(foreign_id2 < 0){
		
		for(var j=0;j<11;j++){  //每添加一个链接， J需要+1
			var ov = options2[j].value;			
			if(ov==foreign_id2){
				options2[j].selected ="selected";
			}
			
		}
		$("#pro_select2").hide();	
	}else{
		
		options2[0].selected ="selected";					
		$("#pro_select2").hide();
	}

	if(type_linktype3 > 0){
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
						changeProductType23(foreign_id3,-1); 
					}
				}else{
				  options3[j].selected ="selected";
				 
				}
				break;
			}	
			//debugger;
		}
		if(type_linktype3==2){
			$("#pro_select3").hide();
		}
	}else if(foreign_id3 < 0){
		for(var j=0;j<11;j++){  //每添加一个链接， J需要+1
			var ov = options3[j].value;			
			if(ov==foreign_id3){
				options3[j].selected ="selected";
			}
			$("#pro_select3").hide();
		}	
	}else{
		options3[0].selected ="selected";					
		$("#pro_select3").hide();
	}

	if(type_linktype4 > 0){
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
		}
		if(type_linktype4==2){
			$("#pro_select4").hide();
		}
	}else if(foreign_id4 < 0){
		for(var j=0;j<11;j++){  //每添加一个链接， J需要+1
			var ov = options4[j].value;			
			if(ov==foreign_id4){
				options4[j].selected ="selected";
			}
			$("#pro_select4").hide();
		}	
	}else{
		options4[0].selected ="selected";					
		$("#pro_select4").hide();
	}

	if(type_linktype5 > 0){
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
		if(type_linktype5==2){
			$("#pro_select5").hide();
		}
	}else if(foreign_id5 < 0){
		for(var j=0;j<11;j++){  //每添加一个链接， J需要+1
			var ov = options5[j].value;			
			if(ov==foreign_id5){
				options5[j].selected ="selected";
			}
			$("#pro_select5").hide();
		}	
	}else{
		options5[0].selected ="selected";					
		$("#pro_select5").hide();
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
}function changeProductType22(pro_typeid,d_id){   //执行edit时候

	 p_detail_id2 = d_id;
	 //是产品分类
	 url='get_product_list1.php?callback=jsonpCallback_get_product_list22&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list22'
	});
	
 // }
}function changeProductType23(pro_typeid,d_id){   //执行edit时候

	 p_detail_id3 = d_id;
	 //是产品分类
	 url='get_product_list1.php?callback=jsonpCallback_get_product_list23&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list23'
	});
	
 // }
}function changeProductType24(pro_typeid,d_id){   //执行edit时候

	 p_detail_id4 = d_id;
	 //是产品分类
	 url='get_product_list1.php?callback=jsonpCallback_get_product_list24&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list24'
	});
	
 // }
}function changeProductType25(pro_typeid,d_id){   //执行edit时候

	 p_detail_id5 = d_id;
	 //是产品分类
	 url='get_product_list1.php?callback=jsonpCallback_get_product_list25&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list25'
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


function setTypeImg(imgurl,obj){
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
	
}
</script>
	</body>
</html>