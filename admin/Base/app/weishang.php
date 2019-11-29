<?php
header("Content-type: text/html; charset=utf-8"); //test
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
require('../../../../weixinpl/common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");

$channel_id = 0;
$admin_id = 0;
$safePhone = "";
$query  = "select adminuser_id,safePhone from customers where isvalid = true and id = ".$customer_id;
$result = _mysql_query($query) or die("L15 query error : ".mysql_error());
$admin_id = mysql_result($result,0,0);
$safePhone = mysql_result($result,0,1);
$parent_id = $admin_id;
while($channel_id != 5 ){
	$admin_id = $parent_id;
	$query = "select parent_id , channel_level_id  from adminusers where id = ".$admin_id;
	$result = _mysql_query($query) or die("L19 query error : ".mysql_error());
	$parent_id = mysql_result($result,0,0);
	$channel_id = mysql_result($result,0,1);
}

$op = $configutil->splash_new($_GET["op"]);
if($op == "updateinfo"){
	$weixin_name = "";
	$weixin_logo = "";
	$foreign_id = 0;
	$query ="select id, weixin_name from weixin_baseinfos where isvalid = true and customer_id = ".$customer_id;
	$result = _mysql_query($query) or die("L19 query error : ".mysql_error());
	$foreign_id = mysql_result($result,0,0);
	$weixin_name = mysql_result($result,0,1);
	$query = "select imgurl from images where isvalid = true and foreign_id = ".$foreign_id." and type = 2 ";
	$result = _mysql_query($query) or die("L37 query error : ".mysql_error());
	$weixin_logo = mysql_result($result,0,0);
	
	$query ="update appusers set nickname = '".$weixin_name."' , logo = 'logos/".$weixin_logo."' where username = '".$safePhone."' and isvalid = true and customerid = '".$customer_id."'";
	_mysql_query($query) or die("L41 query error : ".mysql_error());
}else if($op == "unbind"){
	$bind_appid = $configutil->splash_new($_GET["bind_appid"]);
	$query = "update appandcus set isvalid = false where customerid = ".$customer_id." and appid = ".$bind_appid." and isvalid = true";
	_mysql_query($query) or die("L45 query error : ".mysql_error());
}



$query = "select applogo , downloadQRcode from oem_infos where isvalid = true and  adminuser_id = ".$admin_id;
$result = _mysql_query($query) or die("L26 query error : ".mysql_error());
$applogo = mysql_result($result,0,0);
$downloadQRcode = mysql_result($result,0,1);

$bind_appid = 0;
$createtime = "";
$query = "select appid,createtime from appandcus where isvalid = true and customerid = ".$customer_id; 
$result = _mysql_query($query) or die("L38 query error : ".mysql_error());
if($row = mysql_fetch_object($result)){
	$bind_appid = $row->appid;
	$createtime = $row->createtime;
}

/*
if($bind_appid == 0){
	$query="select app.id  ,app.createtime , app.nickname , app.username , app.logo ,app.customerid  from appusers app , customers cus where app.username = cus.safePhone and app.isvalid = true and cus.id = ".$customer_id;
}else{
	$query="select app.id  ,app.createtime , app.nickname , app.username , app.logo,app.customerid  from appusers app where app.id = ".$bind_appid;
}
*/

$appid = 0;

$nickname = "";
$username = "";
$logo = "";
$app_customerid = 0;

if($bind_appid > 0){
	$query="select app.id  ,app.createtime , app.nickname , app.username , app.logo,app.customerid  from appusers app where app.isvalid = true and app.id = ".$bind_appid;
	$result = _mysql_query($query) or die("L51 query error : ".mysql_error());
	if($row = mysql_fetch_object($result)){
		$appid = $row->id;
		$nickname = $row->nickname;
		$username = $row->username;
		$logo = $row->logo;
		$app_customerid = $row->customerid;
	}
}




?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>微商APP</title>
<link rel="stylesheet" type="text/css" href="/weixin/plat/Public/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/app/weishang.css">
<link rel="stylesheet" type="text/css" href="/weixin/plat/Public/css_V6.0/content<?php echo $theme; ?>.css"><!--内容CSS配色·蓝色-->

<script type="text/javascript" src="/weixin/plat/Public/js_V6.0/assets/js/jquery.min.js"></script>

<!--日期插件JS-->
<link href="/weixin/plat/Public/css_V6.0/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="/weixin/plat/Public/js_V6.0/jquery.ui.core.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/js_V6.0/jquery.ui.datepicker.js"></script>
<style type="text/css">
.password{
  width: 235px;
  height: 20px;
  background: url(images/weishang/input-bj01.png) no-repeat;
  padding: 5px 0px;
  padding-left: 5px;
  line-height: 20px;
  margin-right: 5px;
}

</style>
</head>

<body>
	<!--内容框架开始-->
	<div class="WSY_content">

		<!--列表内容大框开始-->
		<div class="WSY_columnbox" style="min-height:600px">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">操作</a>
					<a href="messenge.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>">信息推送记录</a>
					<a href="notes_setting.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>">app下载引导设置</a>
				</div>
			</div>
			<!--列表头部切换结束-->
        <div class="WSY_data">
        	<div class="WSY_operation">
                <dl class="WSY_operation_dl">
                    <dd class="optn01"><img src="<?php echo CHANNEL_HOST."/".$applogo;?>" alt="app" width="112" height="112"><span>APP</span></dd>
                    <dd class="optn02"><img src="<?php echo CHANNEL_HOST."/".$downloadQRcode;?>" alt="app" width="150" height="150"><span>二维码</span></dd>
					<?php if($appid > 0 || $bind_appid > 0){?>
					<?php if($customer_id != $app_customerid ){ //如APP的customer_id和当前商家的customer_id不一样，表示为绑定的商家?>
				   <dd class="optn03" style="display:block">已绑定</dd>
					<?php }else{ ?>
                    <dd class="optn04" style="display:block">已注册</dd>
					<?php }
					}					?>
                </dl>
				<?php if($appid == 0 && $bind_appid == 0){?>
                <div class="WSY_operation_btn"><input type="button" onclick="openReg()" value="注　册"></div>
				<?php } ?> 
				<?php if($appid > 0 || $bind_appid > 0){ ?>
                <div class="WSY_operation_div">
                	<a><img src="<?php echo empty($logo) ? '../../../images/Base/app/head-img.png' : CHANNEL_HOST."/".$logo; ?>" alt="头像" width="100px" height="100px"></a>
                    <dl>
                        <dd><span>账号：</span><input type="text" id="txt_username" value="<?php echo $username ; ?>" readonly="readonly"></dd>
                        <dd><span>昵称：</span><input type="text" id="txt_nickname" value="<?php echo $nickname ; ?>" readonly="readonly"></dd>
                        <dd><span>时间：</span><input type="text" id="txt_createtime" value="<?php echo $createtime ; ?>" readonly="readonly"></dd>
                    </dl>
                </div>
                <div class="WSY_text_input01 WSY_text_input07">
					<?php if($customer_id != $app_customerid ){ //如APP的customer_id和当前商家的customer_id不一样，表示为绑定的商家?>
                	<div class="WSY_text_input WSY_buttondw"><button class="WSY_button" onclick="openUnbind()" type="button">解 绑</button>
                    	<div class="WSY_solution" id="div_unbind">
                        	<h1>账号解绑<i onclick="closeUnbind()"></i></h1>
                            <p class="WSY_prompt"><a>账号解绑先完成安全验证！</a></p>
                            <ul class="WSY_solution_ul">
                            	<li>
								<a>手机号码：</a>
								<?php if(empty($safePhone)){ ?>
								<a style="color:red" href="/weixin/plat/app/index.php/IndexV2/my_account/C_id/<?php echo passport_encrypt((string)$customer_id) ?>">点击绑定</a>
								<?php }else{ ?>
								<a><?php echo $safePhone ;?></a>
								<button type="button" id="yanzhengma" onclick="GetCheckCode()" style="margin-left:10px">获取验证码</button>
								<?php }?>
								
                                <li><a class="sltn_a">验证码：</a><input type="text" id="validcode"></li>
                            </ul>
                            <div class="WSY_solution_btn"><input type="button" value="确 定" onclick="doUnbind()"></div>
                        </div>
                    </div>
					<?php }else{ //如果不为绑定的商家则可以更新头像等数据 ?>
                    <div class="WSY_text_input"><button class="WSY_button" onclick="updateInfo();">更 新</button></div>
					<?php } ?>
                </div>
				<?php } ?>
            </div>
            <div class="WSY_operation_right" id="div_reg" style="display:none">
            	<h3>USER REGISTER 用户注册</h3>
                <form method="post" action="save_weishang.php?customer_id=<?php echo $customer_id_en ?>&op=reg" id="regForm">
                	<dl class="WSY_operation_red">
                    	<dd><span>手机号码</span>
						<?php if(empty($safePhone)){ ?>
						<span class="boundA"><a href="/weixin/plat/app/index.php/IndexV2/my_account/C_id/<?php echo passport_encrypt((string)$customer_id) ?>">点击绑定</a></span>
						<?php }else{ ?>
						<input type="text" id="safePhone" name="safePhone" readonly="readonly" value="<?php echo $safePhone;?>">
						<?php }?>
						</dd>
						
                        <dd><span>设置密码</span><input type="password" class="password" id="regPass" name="regPass" value=""><i>*长度为6~16位字符</i></dd>
                        <dd class="red_btn"><input type="button" onclick="doReg()" value="马上注册"></dd>
                    </dl>
                </form>
            </div>
		</div>
	</div>
</div>
<script type="text/javascript" src="/weixin/plat/Public/js_V6.0/content.js"></script>
</body>
</html>
<script type="text/javascript">
	function openReg(){
		$("#div_reg").show();
	}
	function openUnbind(){
		$("#div_unbind").show();
	}
	function updateInfo(){
		location.href='weishang.php?customer_id=<?php echo $customer_id_en ?>&op=updateinfo';
	}
	function closeUnbind(){
		$("#div_unbind").hide();
	}
	function doUnbind(){
		var phone = '<?php echo $safePhone;?>';
		var validcode = $("#validcode").val();
		if(phone == "" ){
			alert("请先绑定手机号！");
			return;
		}
		if(validcode == "" ){
			alert("请输入验证码！");
			return;
		}
		$.ajax({ 
			url: "../../../bind_safe_phone.php?action=doit&customer_id=<?php echo $customer_id_en ?>",  
			type: "POST",  
			dataType: "json",
			data:  { 'yzm': validcode },
			success: function(result) {
				debugger;
				if(result.code == 2022){
					alert(result.msg);
				}else if(result.code == 2202){
					alert(result.msg);
				}else if(result.code == 1){
					alert(result.msg);
					//alert("可以进行更换");
					//document.getElementById("frmBind").submit(); 
					location.href='weishang.php?customer_id=<?php echo $customer_id_en ?>&op=unbind&bind_appid=<?php echo $bind_appid; ?>';
				}
			}
		});
	}
	function doReg(){
		var pass = $("#regPass").val();
		if(pass == "" || pass.length < 6 || pass.length > 16){
			alert("请输入正确的密码!");
			return;
		}
		$("#regForm").submit();
	}
	
var mcode=Math.round(Math.random()*1000000);
function GetCheckCode(){
	var c_mobile = '<?php echo $safePhone;?>';
	if(!(/^1[0-9][0-9]\d{4,8}$/.test(c_mobile))){ 
		alert("不是完整的11位手机号或者正确的手机号前七位"); 
		document.mobileform.phone.focus(); 
		return false; 
	}else{	
		$.get("../../../user_sendmessage.php?phone="+c_mobile,function(data){
		
		  alert("验证信息会发送到"+c_mobile);
		  var btn = document.getElementById("yanzhengma");
		  test.init(btn);
		});
	
		
	var test = {
		   node:null,
		   count:60,
		   start:function(){
			  //console.log(this.count);
			  if(this.count > 0){
				 this.node.innerHTML = this.count--;
				 var _this = this;
				 setTimeout(function(){
					_this.start();
				 },1000);
			  }else{
				 this.node.removeAttribute("disabled");
				 this.node.innerHTML = "再次发送";
				 this.count = 60;
			  }
		   },
		   //初始化
		   init:function(node){
			  this.node = node;
			  this.node.setAttribute("disabled",true);
			  this.start();
		   }
		};
	}
}

</script>
<?php 

mysql_close($link);
?>
