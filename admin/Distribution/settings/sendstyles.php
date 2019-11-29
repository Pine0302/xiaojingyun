<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');

$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

//查询订货系统门店开关
if($_POST['check_isopen_shop']){
	$data = array();
	$isopen_shop=0;//订货系统门店模式开关
	$query = "select isopen_shop from orderingretail_shop_setting where customer_id=".$customer_id." limit 1"; 
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$isopen_shop=  $row->isopen_shop;		
	}
	$data['isopen_shop'] = $isopen_shop;
	echo json_encode($data);die();
}

$head  = 0;	//0:配送方式；1:送货时间

$query = "select sendstyle_express,sendstyle_pickup,open_virtual_cust,open_virtual_supplier, open_virtual_proxy,regional_detection,is_kuaidi,appkey,appsecret,appcode from weixin_commonshops where isvalid=true and customer_id=".$customer_id; 
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$sendstyle_express=0;	//配送方式快递开关
$sendstyle_pickup=0;	//配送方式自提开关
$open_virtual_cust = 1; //开启平台虚拟发货
$open_virtual_supplier = 1; //开启供应商虚拟发货
$open_virtual_proxy = 1; //开启代理商虚拟发货
$regional_detection = 1; //开启地区检测
$is_kuaidi = 0; //快递查询方式：0免费查询，1付费查询 默认0
while ($row = mysql_fetch_object($result)) {
	$sendstyle_express=  $row->sendstyle_express;		
	$sendstyle_pickup=$row->sendstyle_pickup;
	$open_virtual_cust = $row->open_virtual_cust;
	$open_virtual_supplier = $row->open_virtual_supplier;
	$open_virtual_proxy = $row->open_virtual_proxy;
	$regional_detection = $row->regional_detection;
	
	$is_kuaidi = $row->is_kuaidi;
	$AppKey = $row->appkey;
	$AppSecret = $row->appsecret;
	$AppCode = $row->appcode;
}
?>
<html> 
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Distribution/d-style.css">
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>

<title>配送设置</title> 

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="div_new_content">
<form action="save_sendstyles.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
    <div class="WSY_content">
		<div class="WSY_columnbox">
			<div class="WSY_column_header">
				<?php include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Distribution/settings/basic_head.php"); ?>
			</div>
			<div class="WSY_data">
				<div class="WSY_remind_main">
					<dl class="WSY_remind_dl02">
					<dt>快递：</dt>
						 <dd>
							<?php if($sendstyle_express==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_express(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_express(1)" class="WSY_bot2" style="display: none; left: 30px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_express(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_express(1)" class="WSY_bot2" style="display: block; left: 0px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="sendstyle_express" id="sendstyle_express" value="<?php echo $sendstyle_express; ?>" />
					</dl>	
					<dl class="WSY_remind_dl02"> 
					<dt>自提：</dt> 
						 <dd>
							<?php if($sendstyle_pickup==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p> 
								<li onclick="change_pickup(0)" class=" change_pickup" style="left: 0px;"></li>
								<span onclick="change_pickup(1)" class=" change_pickup2"  style="display: none; left: 0px;"></span>
							</ul>  
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_pickup(0)" class=" change_pickup" style="display: none; left: 30px;"></li>
								<span onclick="change_pickup(1)" class=" change_pickup2"  style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="sendstyle_pickup" id="sendstyle_pickup" value="<?php echo $sendstyle_pickup; ?>" />
					</dl>
					<dl class="WSY_remind_dl02"> 
					<dt>平台和代理商虚拟发货：</dt> 
						 <dd>
							<?php if($open_virtual_cust == 1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p> 
								<li onclick="change_virtual(0,1)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_virtual(1,1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>  
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_virtual(0,1)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_virtual(1,1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="open_virtual_cust" id="open_virtual_cust" value="<?php echo $open_virtual_cust; ?>" />
					</dl>
					<dl class="WSY_remind_dl02"> 
					<dt>合作商虚拟发货：</dt> 
						 <dd>
							<?php if($open_virtual_supplier == 1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p> 
								<li onclick="change_virtual(0,2)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_virtual(1,2)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>  
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_virtual(0,2)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_virtual(1,2)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="open_virtual_supplier" id="open_virtual_supplier" value="<?php echo $open_virtual_supplier; ?>" />
					</dl>
					<dl class="WSY_remind_dl02"> 
					<dt>地区检测：</dt> 
						 <dd>
							<?php if($regional_detection ==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p> 
								<li onclick="change_virtual(0,4)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_virtual(1,4)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>  
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_virtual(0,4)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_virtual(1,4)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="regional_detection" id="regional_detection" value="<?php echo $regional_detection; ?>" />
					</dl>
					<!--  代理商暂无物流方式选择，先注释
					<dl class="WSY_remind_dl02"> 
					<dt>代理商虚拟发货：</dt> 
						 <dd>
							<?php if($open_virtual_proxy == 1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p> 
								<li onclick="change_virtual(0,3)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_virtual(1,3)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>  
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
								<li onclick="change_virtual(0,3)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_virtual(1,3)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<input type="hidden" name="open_virtual_proxy" id="open_virtual_proxy" value="<?php echo $open_virtual_proxy; ?>" />
					</dl>
					-->
					<dl class="WSY_remind_dl02"> 
					<dt>快递查询：</dt>
						<span onclick="kuaidi(0);">
							<input type="radio" name="is_kuaidi" id="mianfei" value="0" <?php if($is_kuaidi == 0){ echo 'checked';} ?> />
							<label for="mianfei">免费查询跳转(快递100)</label>
						</span>

						<span onclick="kuaidi(2);">
							<input type="radio" name="is_kuaidi" id="mianfei_ex" value="2" <?php if($is_kuaidi == 2){ echo 'checked';} ?> />
							<label for="mianfei_ex">免费查询接口(快递100)</label>
						</span>

						<span onclick="kuaidi(1);">
							<input type="radio" name="is_kuaidi" id="fufei" value="1" <?php if($is_kuaidi == 1){ echo 'checked';} ?> />
							<label for="fufei">付费查询接口(阿里云)</label>
						</span>
					</dl>

	                <div class="WSY_remind_main" style="margin-left:80px">
						<dl class="WSY_remind_dl02 fufei">
		                    <dt>AppKey：</dt>
		                    <dd>
		                        <input type="text" name="AppKey" value='<?php echo $AppKey ?>' onkeyup="StrCheck(this);" onblur="StrCheck(this)" style="width:20%;min-width:240px;border: 1px solid #ccc;height: 26px;border-radius: 3px;">
		                    </dd>
		                </dl>
		    
		                <dl class="WSY_remind_dl02 fufei">
		                    <dt>AppSecret：</dt>
		                    <dd>
		                        <input type="text" name="AppSecret" value='<?php echo $AppSecret ?>' onkeyup="StrCheck(this);" onblur="StrCheck(this)" style="width:20%;min-width:240px;border: 1px solid #ccc;height: 26px;border-radius: 3px;">
		                    </dd>
		                </dl>

		                <dl class="WSY_remind_dl02 fufei">
		                    <dt>AppCode：</dt>
		                    <dd>
		                        <input type="text" name="AppCode" value='<?php echo $AppCode ?>' onkeyup="StrCheck(this);" onblur="StrCheck(this)" style="width:20%;min-width:240px;border: 1px solid #ccc;height: 26px;border-radius: 3px;">
		                    </dd>
		                </dl>

						<dl class="WSY_remind_dl02 fufei">
		                    <dt>温馨提示：</dt>
		                    </br></br><dt>1、进入购买链接，联系涪擎售前客服；</dt>
		                    </br></br><dt>2、发送优惠口令：32 ，享受优惠折扣；</dt>
		                    </br></br><dt>【点击这里可以跳转】</dt>
		                    <dd>
		                        <a href="https://market.aliyun.com/products/56928004/cmapi021863.html?spm=5176.730005.productlist.d_cmapi021863.79ba3524E5zncu&innerSource=search_%E5%BF%AB%E9%80%92#sku=yuncode1586300000" target="_blank">https://market.aliyun.com</a>
		                    </dd>
		                </dl>
					</div>
				</div>	
				<div class="WSY_text_input01">
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;height: 32px;"/></div>
				</div>
			</div>
		</div>
	</div>
 </form>
 <script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
 <script type="text/javascript" src="../../../common/js_V6.0/content.js"></script> 
<div style="width:100%;height:20px;">
</div>  
</div>	
</body>
<?php mysql_close($link);?>	
<script>
function change_virtual(obj,tag){
	switch(tag){
		case 1 : 
			$("#open_virtual_cust").val(obj);
		break;
		case 2 : 
			$("#open_virtual_supplier").val(obj);
		break;
		case 3 : 
			$("#open_virtual_proxy").val(obj);
		break;
		case 4 : 
			$("#regional_detection").val(obj);
		break;
	}
	
}
function change_pickup(obj){
	$("#sendstyle_pickup").val(obj);
}
function change_express(obj){
	$("#sendstyle_express").val(obj);
}
 function submitV(a){
	 var a = $('#sendstyle_express').val();
	 var b = $('#sendstyle_pickup').val();
	 var c = a+b;
	 if(c==0){
		 alert('请至少保留一种配送方式！');
		 return;
	 }

    var is_kuaidi = $("input[name='is_kuaidi']:checked").val();
	var AppKey    = $("[name='AppKey']").val().trim();
    var AppSecret = $("[name='AppSecret']").val().trim();
    var AppCode   = $("[name='AppCode']").val().trim();

    console.log(is_kuaidi);
    if (is_kuaidi == 1) {
    	if( !AppKey ){
        alert('请输入AppKey!');
	        return false;
	    }

	    if( !AppSecret ){
	        alert('请输入AppSecret!');
	        return false;
	    }

	    if( !AppCode ){
	        alert('请输入AppCode!');
	        return false;
	    }
    }

    document.getElementById("upform").submit();
 } 

 	// --------显示控制开关效果
	$(function(){
		var isopen_shop = "<?php echo $isopen_shop; ?>";
		$(".change_pickup").click(function(){
			$(this).animate({left : '30px'});
			$(this).parent().find(".change_pickup2").animate({left : '30px'});
			$(this).hide();
			$(this).parent().find(".change_pickup2").show();
			$(this).parent().find("p").animate({margin : '0 0 0 13px'}, 500);
			
			$(this).parent().find("p").html('关');
			$(this).parent().css({backgroundColor : '#cbd2d8'});
			$(this).parent().find("p").css({color : '#7f8a97'});
			})
			
		$(".change_pickup2").click(function(){
			var thiss = $(this);
		    $.ajax({
		        type: "POST",
		        url : "sendstyles.php",
		        data: {'check_isopen_shop':1},
		        dataType : "json",
		        success : function (data){
					if(data.isopen_shop==1){
	                    $("#sendstyle_pickup").val(0);
						alert('已开启门店，两者不能同时开启');
						return false;
					}else{
						thiss.parent().find(".change_pickup").animate({left : '0px'});
						thiss.animate({left : '0px'});
						thiss.parent().find(".change_pickup").show();
						thiss.hide();
						thiss.parent().find("p").animate({margin : '0 0 0 27px'}, 500);
						thiss.parent().find("p").html('开');
						thiss.parent().css({backgroundColor : '#ff7170'});
						thiss.parent().find("p").css({color : '#fff'});
					}
				}
	        });
		})

		var is_kuaidi = <?php echo $is_kuaidi ?>;
		kuaidi(is_kuaidi)
	});	

	function kuaidi(i) {
		if ( i == 1 ){
			$('.fufei').css('display','block');
		} else {
			$('.fufei').css('display','none');
		}
	}

	/*只能输入英文数字*/
    function StrCheck(obj) {
        obj.value = $.trim(obj.value);
        obj.value = obj.value.replace(/[^A-Za-z0-9]/g,"");
    }
</script>

</html>