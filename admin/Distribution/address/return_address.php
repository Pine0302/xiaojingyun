<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=0;//头部文件0商城资料，1分享设置,2购物设计
$customer_id=$customer_id;
$save_msg="";
$Q_type=$configutil->splash_new($_GET["Q_type"]);
$Q_flag=0;
$id=0;
$location_p = ""; 
$location_c="";
$location_a = "";
$address=""; //详细地址
$zipcode=""; //邮编
$name="";//联系人
$phone="";//电话
$tel=""; //...
$comment=""; //备注
$save_query	="00000222000";
if($Q_type==1){
	$customer_id=$configutil->splash_new($_GET["customer_id"]);
	$id=$configutil->splash_new($_POST["id"]);
	$location_p=$configutil->splash_new($_POST["province"]);
	$location_c=$configutil->splash_new($_POST["city"]);
	$location_a=$configutil->splash_new($_POST["area"]);
	$address=$configutil->splash_new($_POST["address"]);
	$zipcode=$configutil->splash_new($_POST["zipcode"]);
	$name=$configutil->splash_new($_POST["name"]);
	$phone=$configutil->splash_new($_POST["phone"]);
	$tel=$configutil->splash_new($_POST["tel"]);
	$comment=$configutil->splash_new($_POST["comment"]);
	//echo "id=".$id.";location_p=".$location_p."===";
	
	if($id>0){
		$save_query="update weixin_commonshop_returnaddress set 
					location_p='".$location_p."',
					location_c='".$location_c."',
					location_a='".$location_a."',
					address='".$address."',
					zipcode='".$zipcode."',
					name='".$name."',
					phone='".$phone."',
					tel='".$tel."',
					comment='".$comment."'
					where id='".$id."'
					";
	}else{
		$save_query="insert into weixin_commonshop_returnaddress(
					location_p,location_c,location_a,address,zipcode,
					name,phone,tel,comment,customer_id,supplier_id
					) values(
					'".$location_p."','".$location_c."','".$location_a."','".$address."','".$zipcode."',
					'".$name."','".$phone."','".$tel."','".$comment."','".$customer_id."',-1
					)";
	}
	echo strlen($phone+$tel);
	if($location_p!="" && strlen($location_p)>0
	&& $location_c!="" && strlen($location_c)>0
	&& $location_a!="" && strlen($location_a)>0
		){
			if($address!="" && strlen($address)>0){
				if($name!="" && strlen($name)>0){
					if(strlen($phone+$tel+"")>1 ){
						$result = _mysql_query($save_query) or die('Query failed: ' . mysql_error());
						if($result){
							$Q_flag=1;
							$save_msg="保存成功";
						}else{
							$save_msg="保存失败";
						}
					}else{
						$save_msg="电话必须填写一个";
					}
				}else{
					$save_msg="姓名不能为空";
				}
			}else{
				$save_msg="详细地址不能为空";
			}
		
	}else{
		$save_msg="省 市 区不完整";
	}
	
}
$query = "select id,location_p,location_c,location_a,address,zipcode,name,phone,tel,comment
	from weixin_commonshop_returnaddress where isvalid=true and customer_id=".$customer_id." and supplier_id=-1 limit 0,1";
// if($Q_type==""){
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$id= $row->id;
		$location_p = $row->location_p;
		$location_c = $row->location_c;
		$location_a= $row->location_a;
		$introduce = $row->introduce;
		$address = $row->address;
		$zipcode=$row->zipcode;
		$name = $row->name;
		$phone = $row->phone;
		$tel = $row->tel;
		$comment = $row->comment;
	}
// }

	
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/base_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>


<title>商城资料</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>
.select_address{
	width:20%;min-width:120px;border: 1px solid #ccc;height: 26px;border-radius: 3px;
}
.input_address{
	width:20%;min-width:120px;border: 1px solid #ccc;height: 26px;border-radius: 3px;
}
</style>
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a >退货地址</a>
			</div>
		</div>
		<script>
		var head = <?php echo $head; ?>;
		$(".WSY_columnnav").find("a").eq(head).addClass('white1');
		</script>	
	<form action="return_address.php?customer_id=<?php echo $customer_id; ?>&Q_type=1" enctype="multipart/form-data" method="post" id="saveFrom" name="saveFrom">
		<input type=hidden name="id" id="id" value="<?php echo $id; ?>" />
		<input type=hidden  id="save_msg" value="<?php echo $save_msg; ?>" />
		<!--<h2 style="padding-left: 150px;font-size: 20px;color: red;">以下填写，务必全部(电话二选一)填写且正确。<br/>
			同意退货申请，会直接把以下信息发送给客户!</h2>-->
		<div class="WSY_list">
			<li class="WSY_left"><a style="font-size:16px;color:red">以下填写，务必全部(电话二选一)填写且正确。同意退货申请，会直接把以下信息发送给客户!</a></li>
        </div>
		<div class="WSY_remind_main">
			<div style="display:block;overflow:hidden;margin-left:124px;">
			<dl class="WSY_remind_dl02 dlboxa"> 
				<dt>省：</dt>
				<dd>
					<select name="province" id="province" class="select_address"></select>
				</dd>
			</dl>
			<dl class="WSY_remind_dl02 dlboxa"> 
				<dt>市：</dt>
				<dd>
					<select name="city" id="city" class="select_address"></select>
				</dd>
			</dl>
			<dl class="WSY_remind_dl02 dlboxa"> 
				<dt>区：</dt>
				<dd>
					<select name="area" id="area" class="select_address"></select>
				</dd>
			</dl>
			</div>
			<style>
			.WSY_remind_main .dlboxa{display:block;float:left;margin-top:0px !important;}
			.dlboxa dt{width:27px;}
			.dlboxa dt,.dlboxa dd{display:block;float:left;}
			</style>
			<script src="../../../common/region_select.js"></script>
			<script type="text/javascript">
				new PCAS('province', 'city', 'area', '<?php echo $location_p;?>', '<?php echo $location_c;?>', '<?php echo $location_a;?>');
			</script>
			<dl class="WSY_remind_dl02"> 
				<dt>详细地址：</dt>
				<dd>
					<input type="text" name="address" style="width:20%;min-width:120px;border: 1px solid #ccc;height: 26px;border-radius: 3px;" value="<?php echo $address; ?>" maxlength="30" notnull="">
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>邮编：</dt>
				<dd>
					<input type="text" name="zipcode" style="width:20%;min-width:120px;border: 1px solid #ccc;height: 26px;border-radius: 3px;" value="<?php echo $zipcode; ?>" maxlength="30" notnull="">
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>姓名：</dt>
				<dd>
					<input type="text" name="name" style="width:20%;min-width:120px;border: 1px solid #ccc;height: 26px;border-radius: 3px;" value="<?php echo $name; ?>" maxlength="30" notnull="">
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>联系电话（手机）：</dt>
				<dd>
					<input type="text" name="phone" id="phone" style="width:20%;min-width:120px;border: 1px solid #ccc;height: 26px;border-radius: 3px;" value="<?php echo $phone; ?>" maxlength="30" notnull="">
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>联系电话（座机）：</dt>
				<dd>
					<input type="text" name="tel" id="tel" style="width:20%;min-width:120px;border: 1px solid #ccc;height: 26px;border-radius: 3px;" value="<?php echo $tel; ?>" maxlength="30" notnull="">
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>备注：</dt>
				<dd>
					<textarea name="comment" onpropertychange="if(value.length>128) value=value.substr(0,128)" class="WSY_text_box_a" ><?php echo $comment; ?></textarea>
				</dd>
			</dl>
			
		</div>
		
	</form>
	<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="return saveData(this);" style="cursor:pointer;">
		</div>
	</div>
</div> 

<script>
//电话号码
function chkPhoneNumber(tel)
{	
	var phoneReg = /^1[3|4|5|7|8][0-9]\d{8}$|^(09)\d{8}$/;
	if(!phoneReg.test(tel)){
		return false;
	}
	return true;
}
//400和800电话
function chk400(tel)
{
	var phoneReg = /^[48]00\d{7}$/;
	if(!phoneReg.test(tel)){
		return false;
	}
	return true;
}
//区号加座机电话
function chkTelephone(tel)
{
	var phoneReg = /^((0\d{2,3}))(\d{7,8})?$/;
	if(!phoneReg.test(tel)){
		return false;
	}
	return true;
}
function saveData(){
	var tel = $("#tel").val();
	var phone = $("#phone").val();
	if(!chkPhoneNumber(phone) && phone != ''){
		alert("请输入正确的手机号码！");
		return;
	}
	if(!chk400(tel) && !chkTelephone(tel) && tel != ''){
		alert("请输入正确的座机号码！");
		return;
	}
	document.getElementById("saveFrom").submit();	
}
$(function(){
	var save_msg=$("#save_msg").val();
	if(save_msg==""){
		
	}else{
		alert(save_msg);
	}
});
$(".WSY_remind_dl02").hover(function(){
    var $this=$(this);
    $("dt,dt span",$this).addClass("WSY_t3")
},function(){
    var $this=$(this);
    $("dt,dt span",$this).removeClass("WSY_t3")
})
</script>
</body>
</html>