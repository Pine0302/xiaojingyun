<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../../weixinpl/proxy_info.php');
$head=0;//头部文件
	$material_id = -1;
	$query = "select id,pc_logo from weixin_commonshop_material where isvalid=true and customer_id=".$customer_id."";
	$result = _mysql_query($query) or die('L19 Query failed: ' . mysql_error());
	if ($row = mysql_fetch_object($result)) {	
		$material_id = $row->id;	
		$pc_logo = $row->pc_logo;	
	}else{
		$shop_id = -1;
		$query = "select id from weixin_commonshops where isvalid=true and customer_id=".$customer_id;	
		$result = _mysql_query($query) or die('L12 Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {	
			$shop_id = $row->id;
			break;		
		}
		$sql = "insert into weixin_commonshop_material(shop_id,pc_logo,customer_id,isvalid,createtime)values(".$shop_id.",'',".$customer_id.",true,now())";
		_mysql_query($sql)or die('L26 Query failed: ' . mysql_error());
	}


//查询客服设置
$is_open = 0;
$choose_type = -1;
$qq_link = "";
$custom_link = "";
$bear_link = "";
$query = "SELECT is_open,choose_type,qq_link,custom_link,bear_link FROM customer_server_center WHERE isvalid=true AND customer_id=$customer_id";
$result= _mysql_query($query)or die('L32 Query failed: ' . mysql_error());
while( $row = mysql_fetch_object($result)){
	$is_open 		= $row->is_open;
	$choose_type 	= $row->choose_type;
	$qq_link 		= $row->qq_link;
	$custom_link 	= $row->custom_link;
	$bear_link 		= $row->bear_link;

}

	
	
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/basicdesign/base_set.css">
<script type="text/javascript" src="../../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../Common/js/Base/basicdesign/layer.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../../common/utility.js"></script>

<title>PC商城基本资料</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			// include("../../../../../weixinpl/back_newshops/PcShop/Base/basicdesign/basic_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/PcShop/Base/basicdesign/basic_head.php");
		?>		
	<form action="save_base.php?customer_id=<?php echo $customer_id_en; ?>&material_id=<?php echo $material_id;?>" enctype="multipart/form-data" method="post" id="saveFrom" name="saveFrom">
		<div class="WSY_remind_main">
			<dl class="WSY_remind_dl02"> 
				<dt>商城LOGO</dt>
				<dl class="WSY_member">			
					<div>										
						<dd class="spa">
							<img src="<?php echo $pc_logo; ?>" id="img_v1" style="width:220px;" /><br/>
							<input style="width:208;border:1 solid #9a9999; font-size:9pt; background-color:#ffffff; height:18;margin-top: 5px;margin-bottom: 5px;" size="17" name="upfile" id="upfile" class="upfile" type=file value=""> (图片尺寸：宽200*高80 格式：透明底，png）
							<input type=hidden value="<?php echo $pc_logo; ?>" name="imgurl" id="imgurl" /> 

						</dd>	
						
					</div>
				</dl>
			</dl>

			<dl class="WSY_remind_dl02"> 
				<dt>是否开启在线客服：</dt>
				<dd>
					<?php if($is_open==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_pc_online(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_pc_online(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_pc_online(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_pc_online(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<div class="distr_type_div" id="distr_type_div" <?php if($is_open==0){ ?>style="display:none"<?php }?>>
							<!-- <i><input type="radio" class="distr_type"  <?php if($choose_type==1){ ?>checked<?php } ?> value="1" name="online_type">在线客服</i> -->
							<i>
								<input type="radio" class="distr_type" id="qq" <?php if($choose_type==2){ ?>checked<?php } ?> value="2" name="online_type">
								<span style="float:left">QQ客服</span>
								<input class="distr_input" type="text" value="<?php echo $qq_link ?>" name="qq_link" >
							</i>
							<i>
								<input type="radio" class="distr_type" id="custom" placeholder="如:'//www.xx.com'" style="margin-left:10px;" <?php if($choose_type==3){ ?>checked<?php } ?> value="3" name="online_type" >
								<span style="float:left">自定义链接</span>
								<input class="" type="text" style="width:200px;margin-left:5px;" value="<?php echo $custom_link ?>" name="custom_link" ></i>
							<i>
								<input type="radio" class="distr_type" id="bear" <?php if($choose_type==4){ ?>checked<?php } ?> value="4" name="online_type">
								<span style="float:left">小能客服接待组</span>
								<input class="distr_input" type="text" value="<?php echo $bear_link ?>" name="bear_link" ></i>
					</div>
					<input type="hidden" name="need_online" id="need_online" value="<?php echo $is_open; ?>" />
				</dd>
			</dl>
			
		</div>
		
	</form>
	<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="return saveData(this);" style="cursor:pointer;">
		</div>
	</div>
</div> 
<script type="text/javascript" src="../../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<script>
function saveData(){

	if($("#need_online").val()==1){
		
		if($("input[id='qq']").is(':checked')==true){
			if($("input[name='qq_link']").val()==""){
				alert('QQ号码必须填');
				return false;
			}			
		}
		if($("input[id='custom']").is(':checked')==true){
			if($("input[name='custom_link']").val()==""){
				alert('自定义链接必须填');
				return false;
			}
		}
		if($("input[id='bear']").is(':checked')==true){
			if($("input[name='bear_link']").val()==""){
				alert('小能客服必须填');
				return false;
			}
		}
	}
	

	document.getElementById("saveFrom").submit();	
}
function set_pc_online(o){
	$("#need_online").val(o);
	if(o==1){
		$("#distr_type_div").show('slow');
	}else{
		$("#distr_type_div").hide('slow');
	}
}

</script>
</body>
</html>