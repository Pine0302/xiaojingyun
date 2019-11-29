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

$maintenance_id = -1;
$is_maintain    = 0;		//是否开启维护：0不维护，1维护
$begintime      = "";
$endtime        = "";
$information    = "";
$query = "select id,is_maintain,begintime,endtime,information from weixin_maintenance_info where isvalid=true and customer_id=".$customer_id." limit 1";
$result = _mysql_query($query) or die('query failed:'.mysql_error());
while($row = mysql_fetch_object($result)){
	$maintenance_id = $row->id;
	$is_maintain 	= $row->is_maintain;
	$begintime 		= $row->begintime;
	$endtime 	    = $row->endtime;
	$information 	= $row->information;
}
if($maintenance_id<0){		//初次使用自动插入一条数据
	$query_insert = "insert into weixin_maintenance_info(customer_id,isvalid,createtime) values(".$customer_id.",true,now())";
	_mysql_query($query_insert) or die('$query_insert failed:'.mysql_error());
	$maintenance_id = mysql_insert_id();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>维护信息编辑</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/personal_center/personal_center.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/mall_setting/setting.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script>

function change_is_maintain(a){
	$('#is_maintain').val(a);
}

 function submitV(a){
		var begintime = document.getElementById("begintime").value;
		var endtime = document.getElementById("endtime").value;
		if(begintime>endtime){
			alert('维护结束时间不能在开始时间之前!');
			return;
		}
		else if(begintime==endtime){
			alert('维护结束时间不能与开始时间一样!');
			return;
		}	 
	 
	 document.getElementById("upform").submit();	
 }	
</script>
</head>
	
<body>
<form id="upform" action="maintenance_info_save.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="maintenance_id" value="<?php echo $maintenance_id;?>" />
	<div class="WSY_content">
		<div class="WSY_columnbox">

		<?php
			// include("../../../../weixinpl/back_newshops/Diy/maintenance_info/head.php"); 
			include($_SERVER['DOCUMENT_ROOT'].'/mshop/admin/Diy/maintenance_info/head.php'); 
		?>		
		<div class="WSY_data">
				<div class="WSY_remind_main" style="width:600px;height: 18px;">	
					<dl class="WSY_remind_dl02" style="float: none;">
					<dt style="font-size: 12px;">是否开启维护：</dt>
						 <dd style="margin-left: 15px;">
							<?php if($is_maintain==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_maintain(0)" class="WSY_bot" id="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_maintain(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_maintain(0)" class="WSY_bot"  id="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_maintain(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>			
					</dl>
				</div>
					
					<dl class="WSY_member">
						<dt>开始日期：</dt>
						<dd><input class="login-input-username" type="text" id="begintime" name="begintime" value="<?php echo $begintime ?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'2013-10-25 10:00'});"  /></dd>
					</dl>
					
					<dl class="WSY_member">
						<dt>截止日期：</dt>
						<dd><input  class="login-input-username" type="text" id="endtime" name="endtime" value="<?php echo $endtime ?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'2013-10-25 10:00'});"  /></dd>
					</dl>
					
					<dl class="WSY_member">
						<dt>介绍</dt>
						<dd >
						<textarea id="editor1" name="information"><?php echo $information ?></textarea>
						</dd>
						<div class="clear"></div>
					</dl>
									
					<input type="hidden" name="is_maintain" id="is_maintain" value="<?php echo $is_maintain; ?>" />
					
					<div style="clear:both"></div>
				
				<div class="WSY_text_input01" style="margin-left: 22%;">
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
					<!--<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>-->
				</div>			
			
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	</div>
</form>	
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>

<!--配置ckeditor和ckfinder-->
<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<!--编辑器多图片上传引入开始-->
<script type="text/javascript" src="../../../../weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/handlers.js"></script>
<!--编辑器多图片上传引入结束-->
<script>
CKEDITOR.replace( 'editor1',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '/weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '/weixin/plat/Public/ckfinder/ckfinder.html?Type=Images',
filebrowserFlashBrowseUrl : '/weixin/plat/Public/ckfinder/ckfinder.html?Type=Flash',
filebrowserUploadUrl : '/weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '/weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '/weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});

 function changeOauth(v){
   if(v==0){
      document.getElementById("span_oauth").style.display="none";
      document.getElementById("span_ip").style.display="block";
      //document.getElementById("fen").style.display="block";
      document.getElementById("fen_is").style.display="none";
   }else{
      document.getElementById("span_ip").style.display="none";
     // document.getElementById("fen").style.display="none";
      document.getElementById("span_oauth").style.display="block";
      document.getElementById("fen_is").style.display="block";
   }
}
changeOauth(<?php echo $is_oauth; ?>);
</script>
</body>
</html>