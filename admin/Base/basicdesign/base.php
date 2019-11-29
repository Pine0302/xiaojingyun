<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
/* 权限控制文件 start 所有渠道开关写在该文件里 */
include_once('../../../../mshop/admin/view/plug_link_selector/access_control.php');
/* 权限控制文件 end */
$head=0;//头部文件0商城资料，1分享设置,2购物设计	
//悬浮小窗口
$sql = "SELECT ChatFloat_img FROM weixin_widget where customer_id=".$customer_id;
$res = _mysql_query($sql);
while($row=mysql_fetch_object($res)){
	$ChatFloat_img = $row->ChatFloat_img;
	//echo $ChatFloat_img."=========";
	if($ChatFloat_img==''){
		$is_check='checked';
	}else{
		$is_check='';
	}
}

$query = "select id,name,introduce,shop_card_id,is_applymoney_minmoney,is_applymoney_notallowed,need_online,online_type,online_qq,online_custom,supply_chat,is_applymoney_startdate,is_applymoney_enddate  
	,advisory_telephone,advisory_flag,full_vpscore,is_applymoney_weekdate,is_nav from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
	// echo $query;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$shop_card_id           = -1; 
$name                   = "";
$introduce              = "";
$need_online            =  1; //是否开启在线客服
$shop_id                = -1; //是否存在商城
$online_type            = "";
$online_qq              = ""; 
$online_custom          = ""; //自定义链接
$supply_chat         	= ""; //平台小能客服接待组
$is_applymoney_startdate= ""; //提现开始时间
$is_applymoney_enddate  = ""; //提现结束时间
$advisory_telephone     = "";
$advisory_flag          =  0;
$is_applymoney_minmoney =  0; //最低提现金额
$full_vpscore           =  0; //消费满多少vp值才能提现
$is_applymoney_weekdate = -1; //提现可设置按每周几提现 0：周日；1-6；周一-周六

while ($row = mysql_fetch_object($result)) {
	$shop_id                   = $row->id;
	$shop_card_id              = $row->shop_card_id;
	$name                      = $row->name;
	$introduce                 = $row->introduce;
	$is_applymoney_minmoney    = $row->is_applymoney_minmoney;
	$is_applymoney_notallowed  = $row->is_applymoney_notallowed;
	$need_online               = $row->need_online;
	$online_type               = $row->online_type;
	$online_qq  			   = $row->online_qq;
	$online_custom  		   = $row->online_custom;
	$supply_chat  		   	   = $row->supply_chat;
	$is_applymoney_startdate   = $row->is_applymoney_startdate;
	$is_applymoney_enddate     = $row->is_applymoney_enddate;
	$advisory_telephone        = $row->advisory_telephone;
	$advisory_flag             = $row->advisory_flag;
	$is_applymoney_weekdate    = $row->is_applymoney_weekdate;
	$full_vpscore              = $row->full_vpscore;
}

$is_nav = 0;	//商城导航条
$logo = '';	//商城LOGO
$query = "SELECT is_nav,logo FROM weixin_commonshops_extend WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
$result= _mysql_query($query) or die('Query failed 64: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$is_nav = $row->is_nav;
	$logo = $row->logo;
}	
	
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/base_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../Common/js/Base/basicdesign/layer.min.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>

<title>商城资料</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/basicdesign/basic_head.php");
			// include("../../../../weixinpl/back_newshops/Base/basicdesign/basic_head.php"); 
		?>		
	<form action="save_base.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="saveFrom" name="saveFrom">
		<input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
		<div class="WSY_remind_main">
			<dl class="WSY_remind_dl02"> 
				<dt>微商城名称：</dt>
				<dd>
					<input type="text" name="name" value="<?php echo $name; ?>" maxlength="30" notnull="">
					&nbsp;&nbsp;<input type=button style="padding:0px 5px;line-height:24px;height:24px;border:solid 1px #ddd;border-radius:2px;cursor:pointer" value="二维码" onclick="showMediaMap('<?php echo $customer_id_en; ?>','<?php echo QRURL."?qrtype=2&customer_id=".$customer_id; ?>');" />
					不能包含特殊字符和数字
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>简介：</dt>
				<dd>
					<textarea name="introduce" onpropertychange="if(value.length>128) value=value.substr(0,128)" class="WSY_text_box_a" ><?php echo $introduce; ?></textarea>
				</dd>
				<dt>注意：</dt><div style="color:red;display: inline-block;line-height: 26px;">商城简介中不能包含回车和特殊字符，否则会导致不能分享等其他问题</div>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>会员卡设置：</dt>
				<dd>
					<select class="type" name="shop_card_id_select" id="shop_card_id_select" onchange="change_shop_card_id(this)" <?php if($shop_card_id>0){ ?> disabled <?php } ?>>
						<option value="-1">请选择</option>
						<?php 
						   $query="select id,name from weixin_cards where isvalid=true and customer_id=".$customer_id;
						   $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
						   while ($row = mysql_fetch_object($result)) {
							   $tid = $row->id;
							   $tname = $row->name;
						?>  
							 <option value="<?php echo $tid; ?>"  <?php if($shop_card_id==$tid){ ?>selected<?php } ?>><?php echo $tname; ?></option>
						<?php } ?>
						<input type="hidden" name="shop_card_id" id="shop_card_id" style="width:50px;" value="<?php echo $shop_card_id; ?>" />
					</select>
					<span class="tips">(积分会返回在该会员卡上)</span>
				</dd>
			</dl>
			<!-- <dl class="WSY_remind_dl02" id="distr_type_div_applymoney" <?php if($is_applymoney==0) echo "style='display:none'"; ?>> 
				<dt>会员卡提现条件：</dt>
				<dd>
					
				<div class="distr_type_div" style="height:64px;">
				<i><span class="fleft">提现开始日期：</span><input class="distr_input"  type="text" name="is_applymoney_startdate" id="is_applymoney_startdate" value="<?php echo $is_applymoney_startdate;?>" maxlength='2' 
						onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
				</i>				
				<i><span class="fleft">提现结束日期：</span><input class="distr_input"  type="text" name="is_applymoney_enddate" id="is_applymoney_enddate" value="<?php echo $is_applymoney_enddate;?>" maxlength='2' 
						onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
				</i>
				<i>每周
				<select class="type" name="is_applymoney_weekdate" id="is_applymoney_weekdate" onchange="change_weekdate(this)">
						<option value="-1" <?php if(-1 == $is_applymoney_weekdate){?>selected<?php }?>>无</option>
						<option value="0"  <?php if( 0 == $is_applymoney_weekdate){?>selected<?php }?>>星期日</option>
						<option value="1"  <?php if( 1 == $is_applymoney_weekdate){?>selected<?php }?>>星期一</option>
						<option value="2"  <?php if( 2 == $is_applymoney_weekdate){?>selected<?php }?>>星期二</option>
						<option value="3"  <?php if( 3 == $is_applymoney_weekdate){?>selected<?php }?>>星期三</option>
						<option value="4"  <?php if( 4 == $is_applymoney_weekdate){?>selected<?php }?>>星期四</option> 
						<option value="5"  <?php if( 5 == $is_applymoney_weekdate){?>selected<?php }?>>星期五</option>
						<option value="6"  <?php if( 6 == $is_applymoney_weekdate){?>selected<?php }?>>星期六</option>
				</select>
				提现 
				</i>
				<br/>
				<i><span class="fleft">最低提现金额：</span><input class="distr_input"  type="text" value="<?php echo $is_applymoney_minmoney?>" id="is_applymoney_minmoney" name="is_applymoney_minmoney" maxlength='5' onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
				</i>
				<i><span class="fleft">不能提现金额：</span><input class="distr_input"  type="text" value="<?php echo $is_applymoney_notallowed?>" id="is_applymoney_notallowed" name="is_applymoney_notallowed" maxlength='15' onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
				</i>
				<i><span class="fleft">消费累计</span><input class="distr_input" style="width:45px;height:20px;border-radius:2px;text-align:center;"  type="text" value="<?php echo $full_vpscore?>" id="full_vpscore" name="full_vpscore" maxlength='6' onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">&nbsp;&nbsp;vp值
				</i>
				</div>		
				</dd>
			</dl> -->
			
			<dl class="WSY_remind_dl02"> 
				<dt>是否开启在线客服：</dt>
				<dd>
					<?php if($need_online==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_need_online(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_need_online(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_need_online(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_need_online(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<div class="distr_type_div" id="distr_type_div" <?php if($need_online==0){ ?>style="display:none"<?php }?>>
							<i><input type="radio" class="distr_type"  <?php if($online_type==1){ ?>checked<?php } ?> value="1" name="online_type">在线客服</i>
							<i><input type="radio" class="distr_type" <?php if($online_type==2){ ?>checked<?php } ?> value="2" name="online_type"><span style="float:left">QQ客服</span><input class="distr_input" type="text" value="<?php echo $online_qq ?>" name="online_qq" ></i>
							<i><input type="radio" class="distr_type"  <?php if($online_type==3){ ?>checked<?php } ?> value="3" name="online_type" >多客服(只支持微信，不支持任何链接形式)</i>
							<i><input type="radio" class="distr_type" style="margin-left:10px;" <?php if($online_type==4){ ?>checked<?php } ?> value="4" name="online_type" ><span style="float:left">自定义链接</span><input class="" type="text" style="width:200px;margin-left:5px;" value="<?php echo $online_custom ?>" name="online_custom" ></i>
							<i><input type="radio" class="distr_type" <?php if($online_type==5){ ?>checked<?php } ?> value="5" name="online_type"><span style="float:left">小能客服接待组</span><input class="distr_input" type="text" value="<?php echo $supply_chat ?>" name="supply_chat" ></i>
					</div>
					<input type="hidden" name="need_online" id="need_online" value="<?php echo $need_online; ?>" />
				</dd>
			</dl>
			<!-- <dl class="WSY_remind_dl02">
				<dt>是否使用默认按钮：</dt>
				
				<dd style="margin-top:6px;"><input type="checkbox" name="auto_img" id="is_check"  <?php if($is_check) echo 'checked="checked"';?>></dd>
			</dl>
			<dl class="WSY_member">
					<dt style="margin-left:51px;">自定义在线客服悬浮窗口按钮：</dt>
						<div class="add_content_con_r" >
						<?php if($ChatFloat_img!=""){?>
						<img src="<?php echo '../../../'.$ChatFloat_img; ?>" id="img_v" style="width:100px;	height:100px;margin-left:70px;" /><br/>
						<p style="text-align:center;"> (尺寸建议：129*141像素）</p><br/>
						<div class="uploader white">
							<input type="text" class="filename" readonly/>
                            <input type="button" name="ChatFloat_img" class="button" value="上传..."/>
							<input size="17" name="ChatFloat_img" id="upfile" type=file value="">
							
							<input type=hidden value="<?php echo '../../../'.$ChatFloat_img ?>" name="ChatFloat_img" id="imgurl1_up" />
                        </div>
			  
						<?php }else{ 
						$ad_imgurl1="../../../back_lubricant/pic/ad1.png";
						?>
							<img src="../../../common_shop/jiushop/images/MobileChatFloat.png" id="img_v" style="width:100px;height:100px;margin-left:70px;" /><br/>
							<p style="text-align:center;"> (尺寸建议：129*141像素）</p><br/>
							<div class="uploader white">
                            <input type="text" class="filename" readonly/>
                            <input type="button" name="ChatFloat_img" class="button" value="上传..."/>
							<input size="17" name="ChatFloat_img" id="upfile" type=file value="">
							<input type=hidden value="<?php echo '../../../'.$ChatFloat_img ?>" name="ChatFloat_img" id="imgurl1" />
                        </div>
				
						<?php } ?>
				
						
						</div>
				</dl> -->
			<dl class="WSY_remind_dl02"> 
				<dt>是否开启咨询电话：</dt>
				<dd>
					<?php if($advisory_flag==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_advisory_telephone(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_advisory_telephone(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_advisory_telephone(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_advisory_telephone(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<div class="distr_type_div" id="advisory_telephone" <?php if($advisory_flag==0){ ?>style="display:none"<?php }?>>
							<input class="distr_input" style="width:80%" type="text" value="<?php echo $advisory_telephone ?>" name="advisory_telephone" ></i>
					<script>
					$(function(){
						if($("#advisory_telephone :input").val()==0){
							$("#advisory_telephone :input").val("");
						}
					});
					</script>
					</div>
					<input type="hidden" name="advisory_flag" id="advisory_flag" value="<?php echo $advisory_flag; ?>" />
				</dd>
			</dl>
			
			<dl class="WSY_remind_dl02"> 
				<dt>商城导航条：</dt>
				<dd>
					<?php if($is_nav==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="is_nav(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="is_nav(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="is_nav(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="is_nav(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_nav" id="is_nav" value="<?php echo $is_nav; ?>" />
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>商城LOGO：</dt>
				<div class="WSY_memberimg">
						<?php if($logo!=""){?>
                        <img src="<?php echo $logo; ?>" style="width:80px;height:80px;">
						<?php }else{ ?>	
						<img src=	"../../Common/images/Base/personal_center/gift.png" style="width:126px;height:120px;">
						<?php } ?>
                        <span>(尺寸要求：宽度256，高度256）</span>
                        <!--上传文件代码开始-->
                        <div class="uploader white">
                            <input type="text" class="filename" readonly/>
                            <input type="button" name="file" class="button" value="上传..."/>
							<input size="17" name="upfile" id="upfile" type=file value="<?php echo $logo ?>">
							<input type=hidden value="<?php echo $logo ?>" name="logo" id="logo" /> 
                        </div>
                        <!--上传文件代码结束-->
                    </div>
			</dl>
		</div>
		
	</form>
	<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="return saveData(this);" style="cursor:pointer;">
		</div>
	</div>
</div> 
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script>
function saveData(){
	var advisory_telephone=$("#advisory_telephone :input").val();
	var advisory_flag=$("#advisory_flag").val();
	if(advisory_telephone=="" && advisory_flag==0){
		$("#advisory_telephone :input").val(0);
	}else if( !chkPhoneNumber(advisory_telephone) && !chk400(advisory_telephone) && !chkTelephone(advisory_telephone) && advisory_flag==1){
		alert('请输入正确的电话号码!');
		return;
	}
	
	var full_vpscore = $('#full_vpscore').val();
	if( full_vpscore == "" ){
		alert('vp值不可为空');
		$('#full_vpscore').focus();
		return false;
	}
	
	/*var is_check = $("#is_check").is(':checked');//悬浮小窗口 默认按钮
	
	console.log(is_check);
	if(!is_check){
		var imgurl1_up = $('#imgurl1_up').val(); //上传图片
		var upfile   = $("#upfile").val();//悬浮小窗口 上传文件
		if(typeof(imgurl1_up) == "undefined" && upfile==""){
			alert("请上传自定义悬浮图片");
			return false;
		}
	}*/
	
	
	
	document.getElementById("saveFrom").submit();	
	return true ;
}
function set_need_online(obj){
	$("#need_online").val(obj);
	if(obj==0){
		$("#distr_type_div").hide();
	}else{
		$("#distr_type_div").show();
	}
}
function set_advisory_telephone(obj){
	$("#advisory_flag").val(obj);
	if(obj==0){
		$("#advisory_telephone :input").val(0);
		$("#advisory_telephone").hide();
	}else{
		$("#advisory_telephone :input").val("");
		$("#advisory_telephone").show();
	}
}
function is_nav(obj){
	$("#is_nav").val(obj);
}
function change_shop_card_id(e){ 
	var option_val= $(e).val();
	$('#shop_card_id').attr("value",option_val);
}


var i;
// function showMediaMap(customer_id,url){
// 	alert('调用这个');
// 	i = $.layer({
// 		type : 2,
// 		shadeClose: true,
// 		offset : ['10px' , '80px'],
// 		time : 0,
// 		iframe : {
// 			//src : '../common_shop/jiushop/forward.php?type=2&customer_id='+customer_id+'&product_id='+product_id
// 			src:url
// 		},
// 		title : "商城首页二维码(扫码即可以购买)",
// 		//fix : true,
// 		zIndex : 2,
// 		border : [5 , 0.3 , '#437799', true],
// 		area : ['500px','500px'],
// 		closeBtn : [0,true],
// 		success : function(){ //层加载成功后进行的回调
// 			//layer.shift('right-bottom',1000); //浏览器右下角弹出
// 		},
// 		end : function(){ //层彻底关闭后执行的回调
// 			/*$.layer({
// 				type : 2,
// 				offset : ['100px', ''],
// 				iframe : {
// 					src : 'http://sentsin.com/about/'
// 				},	
// 				area : ['960px','500px']
// 			})*/
// 		}
// 	});
// }
var i;
function showMediaMap(customer_id,url){
	i = $.layer({
		type : 2,
		shadeClose: true,
		offset : ['10px' , '80px'],
		time : 0,
		iframe : {
			//src : '../common_shop/jiushop/forward.php?type=2&customer_id='+customer_id+'&product_id='+product_id
			src:url
		},
		title : "商城首页二维码(扫码即可以购买)",
		//fix : true,
		zIndex : 2,
		border : [5 , 0.3 , '#437799', true],
		area : ['500px','500px'],
		closeBtn : [0,true],
		success : function(){ //层加载成功后进行的回调
			//layer.shift('right-bottom',1000); //浏览器右下角弹出
		},
		end : function(){ //层彻底关闭后执行的回调
			/*$.layer({
				type : 2,
				offset : ['100px', ''],
				iframe : {
					src : 'http://sentsin.com/about/'
				},	
				area : ['960px','500px']
			})*/
		}
	});
}
</script>
</body>
</html>