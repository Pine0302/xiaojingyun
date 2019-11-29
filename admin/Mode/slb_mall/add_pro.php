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
//头部文件  0基本设置,1基金明细
$INDEX = $configutil->splash_new($_GET["INDEX"]);
$Itype = $configutil->splash_new($_GET["Itype"]);

$ID=0;
if(!empty($_GET["ID"])){
	$ID = $configutil->splash_new($_GET["ID"]);
}
$p_name="";
$p_type =-10;
$p_price="";
$p_unit="";
$default_imgurl="";
$p_unit_id=0;
$p_introduce="";
$psx_id=0;
$p_sx_id=0;
$p_sx_name="";
$p_sx_introduce="";
if(!empty($ID) && $ID>0 ){
	

$S_P_SQL="select id,p_name,p_type,p_price,p_unit,p_url,p_unit_id,c_createtime,p_introduce from slb_product where id='".$ID."' and c_isvalid=1";
$S_P_SX_SQL="select id,sx_id,sx_name,sx_introduce from slb_p_sx where p_id='".$ID."'";
$S_P_R = _mysql_query($S_P_SQL) or die('Query failed1: ' . mysql_error());

while ($S_P_row = mysql_fetch_object($S_P_R)) {
	$ID = $S_P_row->id;
	$p_name = $S_P_row->p_name;
	$p_type = $S_P_row->p_type;
	$p_price = $S_P_row->p_price;
	$p_unit = $S_P_row->p_unit;
	$default_imgurl = $S_P_row->p_url;
	$p_unit_id = $S_P_row->p_unit_id;
	$c_createtime = $S_P_row->c_createtime;
	$p_introduce = $S_P_row->p_introduce;
}



$S_P_SX_R = _mysql_query($S_P_SX_SQL) or die('Query failed1: ' . mysql_error());
while ($S_P_SX_row = mysql_fetch_object($S_P_SX_R)) {
	$psx_id = $S_P_SX_row->id;
	$p_sx_id = $S_P_SX_row->sx_id;
	$p_sx_name = $S_P_SX_row->sx_name;
	$p_sx_introduce = $S_P_SX_row->sx_introduce;

}
}

$p_introduce_arr= array();
$p_introduce_arr = explode("&", $p_introduce);
for($i=count($p_introduce_arr);$i<8;$i++){
	array_push($p_introduce_arr,"");	
}
$S_SX_1_SQL="select id,sx_type,sx_name,sx_introduce from slb_sx where sx_type=-1 and c_isvalid=1 and custid='".$customer_id."'";
$S_SX_1_R = _mysql_query($S_SX_1_SQL) or die('Query failed1: ' . mysql_error());
$S_SX_0_SQL="select id,sx_type,sx_name,sx_introduce from slb_sx where sx_type=0 and c_isvalid=1 and custid='".$customer_id."'";
$S_SX_0_R = _mysql_query($S_SX_0_SQL) or die('Query failed1: ' . mysql_error());
$k=0;
?>  
<!doctype html>
<html><head><meta charset="utf-8">
<title>基本设置</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/welfare/set.css">
<style>
.folatA{display: inline-block;width:100px;text-align: right;}
.folatINPUT{border:1px solid #D7D7D7;line-height:20px;border-radius: 2px;width: 70%;height:20px}
.fl{float:left;width:220px;margin-right:10px;height:100px;margin-bottom:5px;overflow:hidden; resize:none;}
</style>
</head>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/slb_mall/basic_head.php");
			?>
			<!--列表头部切换结束-->
				<div style="float:left;width:35%">
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;margin-left: 50px;" class="WSY_left">商品背景图：</dt>
					<br/>
					<dd>
					<div style="margin-left: 50px;">
					<?php include("../../../../weixinpl/back_newshops/Mode/slb_mall/upload.php"); ?>
					</div> 
					</dd>
					</dl>
				</div> 
				<div style="float:left;width:60%;margin-bottom: 30px;">
				<div style="float:left;width:60%;padding:20px 20px 20px 5px;border:1px solid #D7D7D7;border-radius: 10px;">
				<a class="folatA">商品名称：</a><input id="p_name" class="folatINPUT" value="<?php echo $p_name; ?>"/><br/><br/>
				<a class="folatA">商品类型：</a>
				<?php if($ID>0){?>
				
				<?php 
				while ($S_SX_1_row = mysql_fetch_object($S_SX_1_R)) {
					$k=$k+1;
					if($p_type==-10 && $k==1){
						$p_type=mysql_result($S_SX_1_R,0,0);
					}
					$sx_1_id=$S_SX_1_row->id;
					$sx_1_name=$S_SX_1_row->sx_name;
					 if($sx_1_id==$p_type){
					?>
					<input id="p_type" class="folatINPUT" value="<?php echo $sx_1_name; ?>" readonly="readonly"/>
					<?php }} ?>	
				<?php }else{?>
				<select id="p_type" onchange="change_type()" class="folatINPUT">
				<?php 	while ($S_SX_1_row = mysql_fetch_object($S_SX_1_R)) {
					$k=$k+1;
					if($p_type==-10 && $k==1){
						$p_type=mysql_result($S_SX_1_R,0,0);
					}
					$sx_1_id=$S_SX_1_row->id;
					$sx_1_name=$S_SX_1_row->sx_name;
					?>
					<option value="<?php echo $sx_1_id; ?>" <?php if($sx_1_id==$p_type){ echo "selected='selected'"; } ?>><?php echo $sx_1_name; ?></option>
					<?php }?>
					</select>
				 <?php }?>
				<br/><br/>
				<a class="folatA">商品属性：</a><select id="p_sx_id" class="folatINPUT">
				<?php
					$S_SX_2_SQL="select id,sx_type,sx_name,sx_introduce from slb_sx where sx_type='".$p_type."' and c_isvalid=1 and custid='".$customer_id."'";
					$S_SX_2_R = _mysql_query($S_SX_2_SQL) or die('Query failed1: ' . mysql_error());
					while ($S_SX_2_row = mysql_fetch_object($S_SX_2_R)) {
					$sx_2_id=$S_SX_2_row->id;
					$sx_2_name=$S_SX_2_row->sx_name;
					?>
					<option value="<?php echo $sx_2_id; ?>" <?php if($sx_2_id==$p_sx_id){ echo "selected='selected'"; } ?>><?php echo $sx_2_name; ?></option>
				<?php }?>
				</select><br/><br/>
				<a class="folatA">商品价格：</a><input id="p_price" class="folatINPUT" value="<?php echo $p_price; ?>"/><br/><br/>
				<a class="folatA">商品单位：</a><select id="p_unit_id" class="folatINPUT">
				<?php while ($S_SX_0_row = mysql_fetch_object($S_SX_0_R)) {
					$sx_0_id=$S_SX_0_row->id;
					$sx_0_name=$S_SX_0_row->sx_name;
					?>
					<option value="<?php echo $sx_0_id; ?>" <?php if($sx_0_id==$p_unit_id){ echo "selected='selected'"; } ?>><?php echo $sx_0_name; ?></option>
				<?php }?>
				</select><br/><br/>
				<a class="folatA" style="float:left">商品描述：</a>
                <div style="float:left;width:500px;">
                    <textarea id="p_introduce_1" class="folatINPUT fl" value="<?php echo $p_introduce_arr[0]; ?>"></textarea>
                    <textarea id="p_introduce_2" class="folatINPUT fl" value="<?php echo $p_introduce_arr[1]; ?>"></textarea>
                    <textarea id="p_introduce_3" class="folatINPUT fl" value="<?php echo $p_introduce_arr[2]; ?>"></textarea>
                    <textarea id="p_introduce_4" class="folatINPUT fl" value="<?php echo $p_introduce_arr[3]; ?>"></textarea>
                    <textarea id="p_introduce_5" class="folatINPUT fl" value="<?php echo $p_introduce_arr[4]; ?>"></textarea>
                    <textarea id="p_introduce_6" class="folatINPUT fl" value="<?php echo $p_introduce_arr[5]; ?>"></textarea>
                    <textarea id="p_introduce_7" class="folatINPUT fl" value="<?php echo $p_introduce_arr[6]; ?>"></textarea>
                    <textarea id="p_introduce_8" class="folatINPUT fl" value="<?php echo $p_introduce_arr[7]; ?>"></textarea>
                    <textarea id="p_introduce_8" class="folatINPUT fl" style="display:none" value=""/>
                </div>
                <div style="clear:both">
					<div class="WSY_text_input"><button class="WSY_button" onclick="subBase();" style="margin-right: 40%;">提交保存</button><br class="WSY_clearfloat"></div>
				</div>
				</div> 
		</div>
	</div>
<?php mysql_close($link);?>	
<script> 
 function subBase(){
	var p_id=<?php echo $ID; ?>;
	var psx_id=<?php echo $psx_id; ?>;
	var p_name=$("#p_name").val();
	var p_type=$("#p_type").val();
	var p_sx_id=$("#p_sx_id").val();
	var p_price=$("#p_price").val();
	var p_unit_id=$("#p_unit_id").val();
	var p_url=$("#default_imgurl").val();
	var custid=<?php echo $customer_id; ?>;
	var p_introduce_1=$("#p_introduce_1").val();
	var p_introduce_2=$("#p_introduce_2").val();
	var p_introduce_3=$("#p_introduce_3").val();
	var p_introduce_4=$("#p_introduce_4").val();
	var p_introduce_5=$("#p_introduce_5").val();
	var p_introduce_6=$("#p_introduce_6").val();
	var p_introduce_7=$("#p_introduce_7").val();
	var p_introduce_8=$("#p_introduce_8").val();
	var p_introduce=p_introduce_1;
	if(p_introduce_2!=null || p_introduce_2!=""){
		if(p_introduce==""){
			alert("描述清楚第一条填写");
			return;
		}
		p_introduce=p_introduce+"&"+p_introduce_2;
	}
	if(p_introduce_3!=null || p_introduce_3!=""){
		if(p_introduce==""){
			alert("描述清楚第三条前填写");
			return;
		}
		p_introduce=p_introduce+"&"+p_introduce_3;
	}
	if(p_introduce_4!=null || p_introduce_4!=""){
		if(p_introduce==""){
			alert("描述清楚第四条前填写");
			return;
		}
		p_introduce=p_introduce+"&"+p_introduce_4;
	}
	if(p_introduce_5!=null || p_introduce_5!=""){
		if(p_introduce==""){
			alert("描述清楚第五条前填写");
			return;
		}
		p_introduce=p_introduce+"&"+p_introduce_5;
	}
	if(p_introduce_6!=null || p_introduce_6!=""){
		if(p_introduce==""){
			alert("描述清楚第六条前填写");
			return;
		}
		p_introduce=p_introduce+"&"+p_introduce_6;
	}
	if(p_introduce_7!=null || p_introduce_7!=""){
		if(p_introduce==""){
			alert("描述清楚第七条前填写");
			return;
		}
		p_introduce=p_introduce+"&"+p_introduce_7;
	}
	if(p_introduce_8!=null || p_introduce_8!=""){
		if(p_introduce==""){
			alert("描述清楚第八条前填写");
			return;
		}
		p_introduce=p_introduce+"&"+p_introduce_8;
	}
	if(p_name==null || p_name==""){
		alert("商品名称不能为空");
		return;
	}else{
		$.ajax({
        type: "post",
        url: "ajax_mall.php",
		dataType: "json",
		//begintime:begintime,endtime:endtime,
        data: {op:10,p_id:p_id,p_name:p_name,p_type:p_type,p_sx_id:p_sx_id,p_price:p_price,p_unit_id:p_unit_id,p_introduce:p_introduce,p_url:p_url,p_url:p_url,custid:custid,psx_id:psx_id},
			success: function (date) {
					alert(date.msg);
					location.href = "product.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=2&Itype=2";
			}
		});
	}
 }
 function change_type(){
	 var sx_type=$("#p_type").val();
	 $.ajax({
        type: "post",
        url: "ajax_mall.php",
		dataType: "json",
		//begintime:begintime,endtime:endtime,
        data: {op:6,sx_type:sx_type},
        success: function (date) {
			var len=date.msg.length;
				var str="";
				for(var i=0;i<len;i++){
					str+="<option value='"+date.msg[i].id+"'>"+date.msg[i].sx_name+"</option>";	
				}
				if(str==""){
					str="<option value=''></option>";
				}				
				$("#p_sx_id").html(str);		

        }
    });
	
  }
</script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>