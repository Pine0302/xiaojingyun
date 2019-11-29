<?php
header("Content-type: text/html; charset=utf-8"); 
//ini_set('display_errors','On');
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=0;//头部文件  0基本设置,1基金明细
$query = "select isOpenPublicWelfare from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());

$isOpenPublicWelfare=0;	//是否开启公益基金

while ($row = mysql_fetch_object($result)) {
	$isOpenPublicWelfare=$row->isOpenPublicWelfare; 
}
$query = "select valuepercent from weixin_commonshop_publicwelfare where isvalid=true and  customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$valuepercent = ""; //分配到公益基金的比例
$welfare_images=""; //公益基金背景图片
while ($row = mysql_fetch_object($result)) {
	$valuepercent=$row->valuepercent; 
}
$query2="select backimg from weixin_commonshop_publicwelfare where isvalid=true and  customer_id=".$customer_id;
$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
while ($row2 = mysql_fetch_object($result2)) {
	$welfare_images=$row2->backimg;
}
?>  
<!doctype html>
<html><head><meta charset="utf-8">
<title>基本设置</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/welfare/set.css">

</head>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			// include("../../../../weixinpl/back_newshops/Mode/welfare/basic_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/welfare/basic_head.php");
			?>
			<!--列表头部切换结束-->
						
			<form action="save_welfare.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
				<div class="WSY_remind_main">
					<dl class="WSY_remind_dl02"> 
						<dt style="line-height:20px;font-weight:normal;margin-left:28px" class="WSY_left">开启公益基金：</dt>
						<dd>
							<?php if($isOpenPublicWelfare==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_sendstatus(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_sendstatus(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
			 					<li onclick="change_sendstatus(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_sendstatus(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
							<span style="float: left;margin: -16px 210px;color: #888;"></span>
						</dd>						
						<input type="hidden" name="isOpenPublicWelfare" id="isOpenPublicWelfare" value="<?php echo $isOpenPublicWelfare; ?>" />
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;margin-left: 14px;" class="WSY_left">公益基金分配率：</dt>
						<dd>
							<input type="text" name="valuepercent" id="valuepercent" style="width:50px;background:#fefefe;" value="<?php echo $valuepercent; ?>" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)"/>（0~1）						
						</dd>
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;margin-left: 84px;" class="WSY_left">背景：</dt>
					<dd>
						<div class="WSY_memberimg">
							<?php if($welfare_images!=""){?>
							<img src="<?php echo $welfare_images; ?>" style="width:64px;height:100px;">
							<?php }else{ ?>
							<img src="../../../pic/uniqlo.png" style="width:64px;height:100px;">
							<?php } ?>
							<span>(图片尺寸：640px*1000px）</span>
							<!--上传文件代码开始-->
							<div class="uploader white">
								<input type="text" class="filename" readonly/>
								<input type="button" name="file" class="button" value="上传..."/>
								<input size="17" name="new_welfare_images" id="new_welfare_images" type=file value="<?php echo $welfare_images ?>">
								<input type=hidden value="<?php echo $welfare_images ?>" name="welfare_images" id="welfare_images" /> 
							</div>
							<!--上传文件代码结束-->
						</div>
					</dd>
					</dl>
					<div class="WSY_text_input"><input type="submit" class="WSY_button" value="提交保存" onclick="return subBase();"><br class="WSY_clearfloat"></div>
				</div> 
			</form>
		</div>
	</div>
<?php mysql_close($link);?>	
<script> 
function change_sendstatus(obj){ 
	$("#isOpenPublicWelfare").val(obj);
}

 function subBase(){
	
	isOpenPublicWelfare=document.getElementById("isOpenPublicWelfare").value;
	valuepercent=document.getElementById("valuepercent").value;
	new_welfare_images=document.getElementById("new_welfare_images").value;
	welfare_images=document.getElementById("welfare_images").value;
		 if(valuepercent==""){
			 alert('请输入公益基金分配率！');
			 return false;
		 }else if(isNaN(valuepercent)){
			 alert('公益基金分配率必须为数字！');
			 return false;
	     }else if(valuepercent>1||valuepercent<=0){
			 alert('公益基金分配率必须为0~1之间，且不能为0！');
			 return false;
		 }else if(welfare_images==""&&new_welfare_images==""){  
			 alert('请选择公益基金背景图片！');
			 return false;			 
		 }	 
		return true;	
 }
function clearNoNum(obj)
{
//先把非数字的都替换掉，除了数字和.
obj.value = obj.value.replace(/[^\d.]/g,"");
//必须保证第一个为数字而不是.
obj.value = obj.value.replace(/^\./g,"");
//保证只有出现一个.而没有多个.
obj.value = obj.value.replace(/\.{2,}/g,".");
//保证.只出现一次，而不能出现两次以上
obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
}
</script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>