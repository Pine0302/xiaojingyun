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
$head=2;
$query = "select id, is_showbottom_menu,is_pic,openbillboard,is_showdiscuss,isshowdiscount,nowprice_title,detail_template_type,list_type,footmenu_type from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$shop_id = $row->id;
	$is_showbottom_menu = $row->is_showbottom_menu;
	$is_pic = $row->is_pic;
	$is_showdiscuss = $row->is_showdiscuss;
	$isshowdiscount = $row->isshowdiscount;
	$detail_template_type = $row->detail_template_type;
	$nowprice_title = $row->nowprice_title;
	$list_type = $row->list_type;
	$footmenu_type = $row->footmenu_type;
} 
/* 个人中心和产品显示vp值 */
$isvp_switch = 0;
$query_vp = "select isvp_switch from weixin_commonshop_vp_bases where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result_vp = _mysql_query($query_vp) or die('Query failed: ' . mysql_error());
while ($row_vp = mysql_fetch_object($result_vp)) {
	$isvp_switch = $row_vp->isvp_switch;
}


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>商城购物设置</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/personal_center/personal_center.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/mall_setting/setting.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>
<style type="text/css">
.WSY_remind_main dl ul{z-index: 0}
</style>
<script>
function comfirm(){
	$('#config_form').submit();
}	
function change_is_showbottom_menu(a){
	$('#is_showbottom_menu').val(a);
}
function change_is_pic(a){
	$('#is_pic').val(a);
}	
function change_is_showdiscuss(a){
	$('#is_showdiscuss').val(a);
}
/*function change_isOpenSales(a){
	$('#isOpenSales').val(a);
}*/
function change_isshowdiscount(a){
	$('#isshowdiscount').val(a);
}
function change_isvp_switch(a){
	$('#isvp_switch').val(a);
}
function change_isshowsuspensionmenu(a){
	$('#isshow_suspensionmenu').val(a);
}
 function submitV(a){
	 document.getElementById("upform").submit();	
 }
	
</script>
</head>
	
<body>
<form id="upform" action="save_setting.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">
   <input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
	<div class="WSY_content">
		<div class="WSY_columnbox">

		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/personalization/basic_head.php");
		?>		
		<div class="WSY_data">

              <div class="WSY_list" id="WSY_list" style="min-height: 500px;">
					
				
				
				<div class="WSY_remind_main">	
					
					
					<dl class="WSY_remind_dl02">
					<dt>首页开启底部菜单:</dt>
						 <dd>
							<?php if($is_showbottom_menu==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_showbottom_menu(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_showbottom_menu(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_showbottom_menu(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_showbottom_menu(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="is_showbottom_menu" id="is_showbottom_menu" value="<?php echo $is_showbottom_menu; ?>" />
					</dl>
					
					<dl class="WSY_remind_dl02">
					<dt>开启图片评论:</dt>
						 <dd>
							<?php if($is_pic==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_pic(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_pic(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_pic(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_pic(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="is_pic" id="is_pic" value="<?php echo $is_pic; ?>" />
					</dl>
					<dl class="WSY_remind_dl02">
					<dt>vp值开关:</dt>
						 <dd>
							<?php if($isvp_switch==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_isvp_switch(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_isvp_switch(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_isvp_switch(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_isvp_switch(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<img id="vp_set" src="../../../Common/images/Base/help.png">
						<input type="hidden" name="isvp_switch" id="isvp_switch" value="<?php echo $isvp_switch; ?>" />
					</dl>
					<div class="WSY_clearfloat"></div>
					<dl class="WSY_remind_dl02">
					<dt>橱窗显示评论:</dt>
						 <dd>
							<?php if($is_showdiscuss==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_showdiscuss(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_showdiscuss(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_showdiscuss(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_showdiscuss(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>	
						</dd>
						<input type="hidden" name="is_showdiscuss" id="is_showdiscuss" value="<?php echo $is_showdiscuss; ?>" />
					</dl>
					<!--<dl class="WSY_remind_dl02">
					<dt>显示产品销量:</dt>
						 <dd>
							<?php //if($isOpenSales==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_isOpenSales(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_isOpenSales(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php //}else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_isOpenSales(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_isOpenSales(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php// } ?>
						</dd>
						<input type="hidden" name="isOpenSales" id="isOpenSales" value="<?php echo $isOpenSales; ?>" />
					</dl>-->
					<dl class="WSY_remind_dl02">
					<dt>显示产品折扣:</dt>
						 <dd>
							<?php if($isshowdiscount==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_isshowdiscount(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_isshowdiscount(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_isshowdiscount(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_isshowdiscount(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="isshowdiscount" id="isshowdiscount" value="<?php echo $isshowdiscount; ?>" />
					</dl>
					<div class="WSY_clearfloat"></div>
				</div>	
				
				<div class="WSY_member " style=" margin-bottom: 40px; ">
				<dd class="d1">"现价"自定义为</dd>
				<dd><input type="text" placeholder="现价" style="width: 80px;" name="nowprice_title" id="nowprice_title" value="<?php echo $nowprice_title;?>"/></dd><dd class="d1">（不填则默认显示"现价"）</dd>
					
				</div>
				<!--
				<div class="WSY_member model" style="height:auto;">
					<div class=" left" >
						<dt>详情页面模板</dt>
					</div>		
					<div class=" center" >
						 <dd>&nbsp;&nbsp;产品图片尺寸要求:</dd>
							    <dd>400*400</dd>
							    <dd>640*400</dd>
								<dd>640*400</dd>
								<dd>640*400</dd>
								<dd>640*586</dd>
								<dd>640*640</dd>
					</div>
					
						<label style="float:left;"><div class="con"><input type="radio" name="detail_template_type" <?php if($detail_template_type==1){ ?>checked=true<?php } ?> value=1>橱窗1</div> <img src='../../../Common/images/Base/mall_setting/big_detail1.jpg' style="margin: 0 auto;" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/big_detail1.jpg>')" onMouseOut="toolTip()" >
						</label>
						<label style="float:left;"><div class="con"><input type="radio" name="detail_template_type" <?php if($detail_template_type==2){ ?>checked<?php } ?>  value=2>橱窗2</div> <img src='../../../Common/images/Base/mall_setting/big_detail2.jpg' style="margin: 0 auto;" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/big_detail2.jpg>')" onMouseOut="toolTip()" ></label>
						 
						 <label style="float:left;"><div class="con"><input type="radio" name="detail_template_type" <?php if($detail_template_type==3){ ?>checked<?php } ?>  value=3>橱窗3</div> <img src='../../../Common/images/Base/mall_setting/big_detail3.jpg' style="margin: 0 auto;"onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/big_detail3.jpg>')" onMouseOut="toolTip()" ></label>
						<label style="float:left;"><div class="con"><input type="radio" name="detail_template_type" <?php if($detail_template_type==4){ ?>checked<?php } ?>  value=4>橱窗4 </div><img src='../../../Common/images/Base/mall_setting/big_detail4.jpg' style="margin: 0 auto;" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/big_detail4.jpg>')" onMouseOut="toolTip()" ></label>
						  
						 <label style="float:left;"><div class="con"><input type="radio" name="detail_template_type" <?php if($detail_template_type==5){ ?>checked<?php } ?>  value=5>橱窗5</div> <img src='../../../Common/images/Base/mall_setting/big_detail5.jpg' style="margin: 0 auto;"onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/big_detail5.jpg>')" onMouseOut="toolTip()"  ></label>
						  
						<label style="float:left;"><div class="con"><input type="radio" name="detail_template_type" <?php if($detail_template_type==6){ ?>checked<?php } ?>  value=6>橱窗6</div> <img src='../../../Common/images/Base/mall_setting/big_detail6.jpg' style="margin: 0 auto;" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/big_detail6.jpg>')" onMouseOut="toolTip()"></label>
						  
	 
						  <div style="clear: both;"></div>			   
				  
				</div>	
				-->
				<div class="WSY_member  box_right" style="height:auto;">
					<div class=" left" >
						<dt>产品列表模板</dt>
					</div>		
				<form id="frm_producttype" class="" method="post" enctype="multipart/form-data">
			<?php 
				/* $sql="select list_type,footmenu_type from weixin_commonshops where customer_id=".$customer_id;
				$result = _mysql_query($sql) or die('Query failed_sql_name: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$list_type = $row->list_type; 
					$footmenu_type=$row->footmenu_type;
				} */
			?>
					
						<label style="float:left;"><div class="con"><input type="radio" name="sendstyle2" <?php if($list_type==1){ ?>checked=true<?php } ?> value=1>模板1</div> <img src='../../../Common/images/Base/mall_setting/type_style1.png' style="margin: 0 auto;" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/type_style1.png>')" onMouseOut="toolTip()" >
						</label>
						<label style="float:left;"><div class="con"><input type="radio" name="sendstyle2" <?php if($list_type==2){ ?>checked<?php } ?>  value=2>模板2</div> <img src='../../../Common/images/Base/mall_setting/type_style2.png' style="margin: 0 auto;" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/type_style2.png>')" onMouseOut="toolTip()" ></label>
						 
						 <label style="float:left;"><div class="con"><input type="radio" name="sendstyle2" <?php if($list_type==3){ ?>checked<?php } ?>  value=3>模板3</div> <img src='../../../Common/images/Base/mall_setting/type_style3.png' style="margin: 0 auto;"onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/type_style3.png>')" onMouseOut="toolTip()" ></label>
						<label style="float:left;"><div class="con"><input type="radio" name="sendstyle2" <?php if($list_type==4){ ?>checked<?php } ?>  value=4>模板4 </div><img src='../../../Common/images/Base/mall_setting/type_style4.png' style="margin: 0 auto;" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/type_style4.png>')" onMouseOut="toolTip()" ></label>
						
						<!--
						<label style="float:left;"><div class="con" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/grey.png>')" onMouseOut="toolTip()" ><input type="radio" name="footmenu_type" <?php if($footmenu_type==1){ ?>checked<?php } ?>  value=1>灰色风格底部菜单</div></label>
						<label style="float:left;"><div class="con" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/black.png>')" onMouseOut="toolTip()" ><input type="radio" name="footmenu_type" <?php if($footmenu_type==2){ ?>checked<?php } ?>  value=2>黑色风格底部菜单</div> </label>
						<label style="float:left;"><div class="con" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/white.png>')" onMouseOut="toolTip()" ><input type="radio" name="footmenu_type" <?php if($footmenu_type==3){ ?>checked=true<?php } ?> value=3>白色风格底部菜单</div></label>
						</label>
						<label style="float:left;"><div class="con" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/usual.png>')" onMouseOut="toolTip()" ><input type="radio" name="footmenu_type" <?php if($footmenu_type==4){ ?>checked<?php } ?>  value=4>经典风格底部菜单</div></label>
					<label style="float:left;"><div class="con" onMouseOver="toolTip('<img src=../../../Common/images/Base/mall_setting/nav_5.png>')" onMouseOut="toolTip()" ><input type="radio" name="footmenu_type" <?php if($footmenu_type==5){ ?>checked=true<?php } ?> value=5>底部菜单5</div></label>
					</label>
						  <label style="float:left;"><div class="con"  ><input type="radio" name="footmenu_type" <?php if($footmenu_type==0){ ?>checked<?php } ?>  value=0>不需要底部菜单</div></label>
						-->
						   <div style="clear:both"></div>
							   
				  </form>
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
<script type="text/javascript" src="../../../../common/js/layer/V2_1/layer.js"></script>
<script>
layer.config({
    extend: '/extend/layer.ext.js'
}); 

$('#vp_set').on('click', function(){
	
	layer.tips('开通后,个人中心和产品列表显示vp值','#vp_set');
});
</script>
	</body>
</html>