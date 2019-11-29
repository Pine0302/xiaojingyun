<?php

header("Content-type: text/html; charset=utf-8"); 
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

$p_name="";
if(!empty($_GET["p_name"])){
	$p_name = $configutil->splash_new($_GET["p_name"]);
}
$p_type="";
if(!empty($_GET["p_type"])){
	$p_type = $configutil->splash_new($_GET["p_type"]);
}
$begintime="";
if(!empty($_GET["begintime"])){
	$begintime = $configutil->splash_new($_GET["begintime"]);
}
$endtime="";
if(!empty($_GET["endtime"])){
	$endtime = $configutil->splash_new($_GET["endtime"]);
}

?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/welfare/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/utility.js" charset="utf-8"></script>
<script type="text/javascript" src="../../../common/js/jquery.blockUI.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<style> 
table#WSY_t2 td {
	border: 1px solid #d8d8d8; 
     padding: 0 1em 0; 
    text-align: center;
}
tr {
    line-height: 22px;
}
</style>
<title>基金明细</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body> 
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			// include("../../../../weixinpl/back_newshops/Mode/slb_mall/basic_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/slb_mall/basic_head.php");
			?>
			<!--列表头部切换结束-->
			<div class="WSY_remind_main">
				<form class="search" id="search_form" style="margin-left:18px; margin-top: 18px;">
					<div class="WSY_list" style="margin-top: 18px;">
						<a style="margin-left: 30px;">类型名称：	
						<input type="text" class="input "  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px"   id="p_name" name="p_name" value="<?php echo $p_name; ?>"/>
						</a>
						
						<input type="button" class="search_btn" onclick="searchForm();" style="width:80px" value="搜 索"> 
					</div>     
				</form>	  
				<table width="97%" class="WSY_table" id="WSY_t2">
					<thead class="WSY_table_header">
						<th width="10%">类型编码</th>
						<th width="10%">类型名称</th>
						<th width="10%">类型标题</th>
						<th width="15%">card是否开启</th>
						<th width="15%">card名称</th>
						<th width="10%">ZDY是否开启</th> 
						<th width="15%">ZDY名称</th> 
						<th width="5%">完成码开启</th> 
						<th width="10%">操作</th> 
					</thead>
					<tbody>
					   <?php 
					   $curr_login=$_SESSION['curr_login']; 
					   $pagenum = 1;

						if(!empty($_GET["pagenum"])){
						   $pagenum = $configutil->splash_new($_GET["pagenum"]);
						}

						$start = ($pagenum-1) * 20;
						$end = 20;				
						
						$query = 'SELECT id,p_type,type_name,addit1,addit1_name,addit1_introduce,addit1_patrn,addit2,addit2_name,addit2_introduce,addit2_patrn,title,code FROM slb_type where c_isvalid=true and custid='.$customer_id;				              
						if($begintime!=""){
						   $query = $query." and UNIX_TIMESTAMP(slb_product.c_createtime)>".strtotime($begintime);
						 }
						 if($endtime!=""){
						   $query = $query." and UNIX_TIMESTAMP(slb_product.c_createtime)<".strtotime($endtime);
						 } 
						 if($p_name!=""){
						   $query = $query." and type_name like '%" .$p_name. "%'";
						 }
						 if($p_type>0){
						   $query = $query." and p_type=".$p_type;
						 }
						 $result = _mysql_query($query);
						 $rcount_q2 = mysql_num_rows($result);
						 $query1=$query.' order by id desc limit '.$start.','.$end;
						 
					   $result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
					   
					   while ($row = mysql_fetch_object($result1)) {
							$id=$row->id;
							$p_type=$row->p_type;
							$type_name=$row->type_name;
							$addit1=$row->addit1;
							$addit1_name=$row->addit1_name;
							$addit1_introduce=$row->addit1_introduce;
							$addit1_patrn=$row->addit1_patrn;
							$addit2=$row->addit2;
							$addit2_name=$row->addit2_name;
							$addit2_introduce=$row->addit2_introduce;
							$addit2_patrn=$row->addit2_patrn;
							$title=$row->title;
							$code=$row->code;
							
					   ?>
						<tr>
						   <td style="text-align: center;"><?php echo $p_type; ?></td>
						   <td style="text-align: center;"><?php echo $type_name; ?></td>
						   <td style="text-align: center;" class="title"><?php echo $title; ?></td>
						   <td style="text-align: center;" class="addit1"><?php  if($addit1==0){ echo "关闭";}else{ echo "开启";}  ?></td>
						   <td style="text-align: center;" class="addit1_name"><?php echo $addit1_name; ?></td>
						   <td style="text-align: center;" class="addit2"><?php  if($addit2==0){ echo "关闭";}else{ echo "开启";}  ?></td>
						   <td style="text-align: center;" class="addit2_name"><?php echo $addit2_name; ?></td>
						   <td style="text-align: center;" class="code"><?php  if($code==0){ echo "关闭";}else{ echo "开启";}  ?></td>
						   <td>			   
							<a  onclick="Payload(this,'<?php echo $id; ?>',3)" class="wsy_preview ka" title="商品编辑"><img style="width: 18px;height: 18px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon05.png" /></a>
							<a  onclick="delete_type(this,'<?php echo $id; ?>')" class="wsy_preview ka" title="商品删除"><img style="width: 18px;height: 18px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon04.png" /></a>
						   </td>
						</tr>
						 <tr class="ksto" style="display: none">
						  <td colspan="9" >
							<div class="kstd" style="padding: 0px 50px 0px 50px;border: 2px solid green;line-height: 30px;border-radius:5px;height: 170px;">
							<div style="float: left;width:95%;margin-top:5px;height: 160px;">
							<h1><a style="font-size: 30px;">设置自定义字段</a></h1>
							<p style="margin-top: 10px;">
							<a>产品类型标题：</a><input  type="text" value="<?php echo $title; ?>" class="title" style="line-height: 20px;border: 1px solid grey;border-radius:5px;margin-right: 50px;display: inline-block; "  />
							<a style="margin-left: 30px;">完成码是否开启：</a><select style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px" class="code">
							<option value="0" <?php if($code==0){ echo "selected='selected'";} ?>>关闭</option>
							<option value="1" <?php if($code==1){ echo "selected='selected'";} ?>>开启</option>
							<select>
							<a style="margin-left: 30px;">正则格式(可以复制该格式)：/^(\w){6,20}$/</a>
							</p>
							<p style="margin-top: 10px;"><a>card是否开启：</a><select style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px" class="addit1">
							<option value="0" <?php if($addit1==0){ echo "selected='selected'";} ?>>关闭</option>
							<option value="1" <?php if($addit1==1){ echo "selected='selected'";} ?>>开启</option>
							<select>
							<a style="margin-left: 30px;">card名称：</a><input  type="text" value="<?php echo $addit1_name; ?>" class="addit1_name" style="line-height: 20px;border: 1px solid grey;border-radius:5px;display: inline-block; "  />
							<a style="margin-left: 30px;">card正则：</a><input  type="text" value="<?php echo $addit1_patrn; ?>" class="addit1_patrn" style="line-height: 20px;border: 1px solid grey;border-radius:5px;display: inline-block; "  />
							<a style="margin-left: 30px;">card描述：</a><input  type="text" value="<?php echo $addit1_introduce; ?>" class="addit1_introduce" style="line-height: 20px;border: 1px solid grey;border-radius:5px;display: inline-block;width:25%; "  />
							</p>
							<p style="margin-top: 10px;"><a>ZDY是否开启：</a><select style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px" class="addit2">
							<option value="0" <?php if($addit2==0){ echo "selected='selected'";} ?>>关闭</option>
							<option value="1" <?php if($addit2==1){ echo "selected='selected'";} ?>>开启</option>
							<select>
							<a style="margin-left: 30px;">ZDY名称：</a><input  type="text" value="<?php echo $addit2_name; ?>" class="addit2_name" style="line-height: 20px;border: 1px solid grey;border-radius:5px;display: inline-block; "  />
							<a style="margin-left: 30px;">ZDY正则：</a><input  type="text" value="<?php echo $addit2_patrn; ?>" class="addit2_patrn" style="line-height: 20px;border: 1px solid grey;border-radius:5px;display: inline-block; "  />
							<a style="margin-left: 30px;">ZDY描述：</a><input  type="text" value="<?php echo $addit2_introduce; ?>" class="addit2_introduce" style="line-height: 20px;border: 1px solid grey;border-radius:5px;display: inline-block;width:25%; "  />
							</p>
							</div>	
							<a style="float: right; left: 80%;margin-top:70px"><img onclick="save_type(this,'<?php echo $id; ?>')" style="width: 30px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon23.png"/></a>
							</div>
						  </td>
						  </tr>
					   <?php } ?>
					    
					
					</tbody>					
				</table>
				<div class="blank20"></div>
				<div id="turn_page"></div>
				<!--翻页开始-->
				<div class="WSY_page">
        	
				</div>
				<!--翻页结束-->
			</div>
		</div>
	</div>

	
<script src="../../../js/fenye/jquery.page1.js"></script>
<script>
var pagenum = <?php echo $pagenum ?>;
 var rcount_q2 = <?php echo $rcount_q2 ?>;
 var end = <?php echo $end ?>;
 /* var user_id = <?php echo $user_id ?>; */
 var count =Math.ceil(rcount_q2/end);//总页数

  	//pageCount：总页数
	//current：当前页
	
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		 var p_name = document.getElementById("p_name").value;
		 document.location= "set_type.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=5&Itype=5&pagenum="+p+"&p_name="+p_name;
	   }
    });

  var pagenum = <?php echo $pagenum ?>;
   var page = count;
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	var p=a;
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		 var p_name = document.getElementById("p_name").value;
		 document.location= "set_type.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=5&Itype=5&pagenum="+p+"&p_name="+p_name;
	}
  }
function searchForm(){
	var p_name = document.getElementById("p_name").value;
	document.location= "set_type.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=5&Itype=5&pagenum=1&p_name="+p_name;
}
function Payload(obj,ID,type){
	var XID=$(obj).parent().parent();
	var XIDO=XID.parent();
	if(XIDO.children("tr").eq(XID.index()+1).css("display")=="none"){
		$(".ksto").css({"display":"none"});
		XIDO.children("tr").eq(XID.index()+1).css({"display":"table-row"});
		if(type==0){
			$(".kstd").css({"display":"none"});
		}else{
			XIDO.children("tr").eq(XID.index()+1).children("td").children(".kstd").css({"display":" block"});
		}
		
	}else if(XIDO.children("tr").eq(XID.index()+1).css("display")=="table-row"){
		XIDO.children("tr").eq(XID.index()+1).css({"display":"none"});
	}
}
function save_type(obj,ID){
	var XID=$(obj).parent().parent();
	var XID1=XID.parent().parent();
	var XIDO=XID1.parent();
	var XID2=XIDO.children("tr").eq(XID1.index()-1);
	var title=XID.children("div").eq(0).children("p").eq(0).children(".title").val();
	var code=XID.children("div").eq(0).children("p").eq(0).children(".code").val();
	var addit1=XID.children("div").eq(0).children("p").eq(1).children(".addit1").val();
	var addit1_name=XID.children("div").eq(0).children("p").eq(1).children(".addit1_name").val();
	var addit1_introduce=XID.children("div").eq(0).children("p").eq(1).children(".addit1_introduce").val();
	var addit1_patrn=XID.children("div").eq(0).children("p").eq(1).children(".addit1_patrn").val();
	var addit2=XID.children("div").eq(0).children("p").eq(2).children(".addit2").val();
	var addit2_name=XID.children("div").eq(0).children("p").eq(2).children(".addit2_name").val();
	var addit2_introduce=XID.children("div").eq(0).children("p").eq(2).children(".addit2_introduce").val();
	var addit2_patrn=XID.children("div").eq(0).children("p").eq(2).children(".addit2_patrn").val();
	//---------------
	var addit1_str="";
	if(addit1==0){
		addit1_str="关闭";
	}else if(addit1==1){
		addit1_str="开启";
	}
	var addit2_str="";
	if(addit2==0){
		addit2_str="关闭";
	}else if(addit2==1){
		addit2_str="开启";
	}
	var code_str="";
	if(code==0){
		code_str="关闭";
	}else if(code==1){
		code_str="开启";
	}
	
	$.ajax({
        type: "post",
        url: "ajax_mall.php",
		dataType: "json",
		//begintime:begintime,endtime:endtime,
        data: {op: 100,addit1:addit1,addit1_name:addit1_name,addit1_introduce:addit1_introduce,addit2:addit2,addit2_name:addit2_name,addit2_introduce:addit2_introduce,ID:ID,title:title,code:code,addit1_patrn:addit1_patrn,addit2_patrn:addit2_patrn},
        success: function (date) {
			alert(date.msg); 
			if(date.result==1){
				$(".ksto").css({"display":"none"});
				XID2.children(".addit1").html(addit1_str);
				XID2.children(".addit1_name").html(addit1_name);
				XID2.children(".addit2").html(addit2_str);
				XID2.children(".addit2_name").html(addit2_name);
				XID2.children(".title").html(title);
				XID2.children(".code").html(code_str);
			}
        }
    });	
}
function delete_type(obj,ID){
	var XID=$(obj).parent().parent();
	var XIDO=$(obj).parent();
	var curr_login='<?php echo $curr_login; ?>';
	if(curr_login!="car"){//设置删除权限账号
		alert("你没有权限删除");
		return;
	}
	$.ajax({
        type: "post",
        url: "ajax_mall.php",
		dataType: "json",
		//begintime:begintime,endtime:endtime,
        data: {op: 101,ID:ID},
        success: function (date) {
			alert(date.msg); 
			if(date.result==1){
				XIDO.children("tr").eq(XID.index()+1).detach();
				XID.detach();
			}
        }
    });	
}
</script>

<?php mysql_close($link);?>	

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>