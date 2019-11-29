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
$p_type=-10;
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
table#WSY_t1 td {
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
			<?php include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/slb_mall/basic_head.php"); ?>
	<div id="add_sx" style="position:absolute;right: 15%;top: 72px;" onmouseover="isOut=false" onmouseoout="isOut=true">
	<input type='button' value="添加属性" onclick="add_X_sxdiv()" class="search_btn" style="width:80px;cursor: pointer;"/>
	</div>		
<script>
 function add_X_sxdiv(){
	 var str='<div style="width: 70%;padding: 5% 0 5% 0;margin: auto;">'+
			'<p><a class="WSY_preview1" style="width: 42%;margin-left:0;margin-right:2%;display: inline-block;text-align: center;line-height: 30px;font-size: 18px;cursor: pointer;" onclick="add_N_S()">添加产品属性</a><a class="WSY_preview1" style="width: 42%;display: inline-block;text-align: center;line-height: 30px;font-size: 18px;margin-left:0;cursor: pointer;" onclick="add_N_L()">添加属性类型</a></p><br/>'+
			'<a>属性类型：</a><select class="input"  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:150px;" id="sx_type" value="50"><option>00000000</option></select> <br/>'+
			'<a>属性名称：</a><input type="text" class="input" onblur="check_name()"  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:150px;" id="sx_name" value=""><br/>'+
			'<a>描述：</a><br/>'+
			'<textarea rows="3" cols="" style="width: 400px;height: 160px;border: 1px solid #CFCBCB;resize: none;" id="sx_introduce"></textarea><br/>'+
			'<br/><input type="button" value="添加" onclick="add_save()" class="search_btn" style="width:80px;cursor: pointer;margin-left: 40%;"/></div>';
	   $('#add_sx').html("").animate({'width':'600px','height':'400px'},500).css({'background-color':'rgb( 251, 251, 251 )','border':'1px solid #CFCBCB','border-radius':'5px'}).append(str);
		add_N_S();
	   }
  function add_N_sxdiv(){
	  $('#add_sx').html("").animate({'width':'30px','height':'20px'},500).css({'border-radius':'2px','border':'0px'}).append('<input type="button" value="添加属性" onclick="add_X_sxdiv()" class="search_btn" style="width:80px;cursor: pointer;"/>');
  }
  function add_N_L(){
	  var XID=$('#add_sx').children("div").eq(0);
	  XID.children("a").eq(0).html("类型类别：");
	  XID.children("a").eq(1).html("类型名称："); 
	  XID.children("select").eq(0).html('<option value="-1">属性类型</option><option value="0">单位属性</option>');
  }
   function add_N_S(){
	  var XID=$('#add_sx').children("div").eq(0);
	  XID.children("a").eq(0).html("属性类型：");
	  XID.children("a").eq(1).html("属性名称："); 
	  var custid=<?php echo $customer_id; ?>;
	  	$.ajax({
			type: "post",
			url: "ajax_mall.php",
			dataType: "json",
			//begintime:begintime,endtime:endtime,
			data: {op: 2,custid:custid},
			success: function (date) {
				var len=date.msg.length;
				var str="";
				for(var i=0;i<len;i++){
					str+="<option value='"+date.msg[i].id+"'>"+date.msg[i].sx_name+"</option>";	
				}	
				XID.children("select").eq(0).html(str);				
			}
		});
  }
  
	function check_name(){
		var sx_type=$("#sx_type").val();
		var sx_name=$("#sx_name").val();
		var custid=<?php echo $customer_id; ?>;
		$.ajax({
			type: "post",
			url: "ajax_mall.php",
			dataType: "json",
			//begintime:begintime,endtime:endtime,
			data: {op: 7,sx_type:sx_type,sx_name:sx_name,custid:custid},
			success: function (date) {
				if(date.msg[0].id>0){
				alert(date.msg[0].msg); 
				$("#sx_name").val("")
				return true;
				}else{
					return false;
				}
			}
		});
	}
   function add_save(){
	   if(check_name()){
		    return;
	   }
	 
	  var sx_type=$("#sx_type").val();
	  var sx_name=$("#sx_name").val();
	  var custid=<?php echo $customer_id; ?>;
	  var sx_introduce=$("#sx_introduce").val();
	  if(sx_name=="" || sx_name==null){
		  alert("名称不能空");
		  return;
	 }else{
	  
	  $.ajax({
        type: "post",
        url: "ajax_mall.php",
		dataType: "json",
		//begintime:begintime,endtime:endtime,
        data: {op: 1,sx_type:sx_type,sx_name:sx_name,sx_introduce:sx_introduce,custid:custid},
        success: function (date) {
			alert(date.msg); 
			if(date.result==1){
				location.href = "add_sx.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=3&Itype=3";
				setTimeout(add_N_sxdiv(),"3000");
			}
        }
    });
	}
  }
   function save_sx(obj){
	  var XID=$(obj).parent().parent();
	  var ID=XID.children("div").eq(0).children(".id").val();
	  var sx_type=XID.children("div").eq(0).children(".sx_type").val();
	  var sx_name=XID.children("div").eq(0).children(".sx_name").val();
	  var sx_introduce=XID.children("div").eq(1).children(".sx_introduce").val();
	  var custid=<?php echo $customer_id; ?>;
	  if(sx_name=="" || sx_name==null){
		  alert("名称不能空");
		  return;
	 }else{
	  $.ajax({
        type: "post",
        url: "ajax_mall.php",
		dataType: "json",
		//begintime:begintime,endtime:endtime,
        data: {op: 4,ID:ID,sx_name:sx_name,sx_introduce:sx_introduce,sx_type:sx_type,custid:custid},
        success: function (date) {
			if(date.result==1){
				$(".ksto").css({'display':'none'});
				var XID1=XID.parent().parent();
				var XIDO=XID1.parent();
				XIDO.children("tr").eq(XID1.index()-1).children("td").eq(1).html(sx_name);
				XIDO.children("tr").eq(XID1.index()-1).children("td").eq(2).html(sx_introduce);
			}else{
				alert(date.msg); 
			}
        }
    });
	 }
  }
  function delect_sx(obj,ID,sx_type){
	   var XID=$(obj).parent().parent();
	   var XIDO=$(obj).parent();
	  $.ajax({
        type: "post",
        url: "ajax_mall.php",
		dataType: "json",
		//begintime:begintime,endtime:endtime,
        data: {op: 5,ID:ID,sx_type:sx_type},
        success: function (date) {
			if(date.result==1){
				alert(date.msg[0].msg); 
				if(date.msg[0].id==1){
					XIDO.children("tr").eq(XID.index()+1).detach();
					XID.detach();
				}
			}else{
				alert(date.msg); 
			}
        }
    });
  }
function  cinsert_type(obj,ID,sx_name){
	 var custid=<?php echo $customer_id; ?>;
	$.ajax({
        type: "post",
        url: "ajax_mall.php",
		dataType: "json",
		//begintime:begintime,endtime:endtime,
        data: {op: 8,ID:ID,sx_name:sx_name,custid:custid},
        success: function (date) {
			alert(date.msg); 
			if(date.result==1){	
				$(obj).detach();
			}		
        }
    });
}
  
$(function(){ 
	$(document).bind("click",function(e){ 
	if($("#add_sx").css("height")>'350px'){
		var target = $(e.target); 
		var ID=	target.attr('id');
		if(ID!='add_sx'){
			var XID=target.parent()
			if(XID.attr('id')!='add_sx'){
				var XID=target.parent().parent();
				if(XID.attr('id')!='add_sx'){
					var XID=target.parent().parent().parent();
					if(XID.attr('id')!='add_sx'){
						add_N_sxdiv();
					}
				}
			}	
		}
		}
	}); 		
}); 
</script>	
			
			<!--列表头部切换结束-->
			<div class="WSY_remind_main">
				<form class="search" id="search_form" style="margin-left:18px; margin-top: 18px;">
					<div class="WSY_list" style="margin-top: 18px;">
						<a style="margin-left: 30px;">商品名称：	
						<input type="text" class="input "  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px"   id="p_name" name="p_name" value="<?php echo $p_name; ?>"/>
						</a>
						<a style="margin-left: 30px;">商品类型：	
						<select style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px" id="p_type" name="p_type">
						<option value="-10">全部</option>
						<option value="-1">类型属性</option>
						<option value="-2">单位属性</option>
						<?php
							$S_SX_1_SQL="select id,sx_type,sx_name,sx_introduce from slb_sx where sx_type=-1 and c_isvalid=1 and custid='".$customer_id."' ";
							$S_SX_1_R = _mysql_query($S_SX_1_SQL) or die('Query failed1: ' . mysql_error());
							while ($S_SX_2_row = mysql_fetch_object($S_SX_1_R)) {
							$sx_2_id=$S_SX_2_row->id;
							$sx_2_name=$S_SX_2_row->sx_name;
							?>
							<option value="<?php echo $sx_2_id; ?>" <?php if($sx_2_id==$p_type){ echo "selected='selected'"; } ?>><?php echo $sx_2_name; ?></option>
						<?php }?>
						</select>
						</a>
					
						<input type="button" class="search_btn" onclick="searchForm();" style="width:80px" value="搜 索"> 
					</div>     
				</form>	  
				<table width="97%" class="WSY_table" id="WSY_t1">
					<thead class="WSY_table_header">
						<th width="20%">属性类型</th>
						<th width="20%">属性名称</th>
						<th width="40%">属性详细</th>
						<th width="20%">操作</th> 
					</thead>
					<tbody>
					   <?php 
					   $pagenum = 1;

						if(!empty($_GET["pagenum"])){
						   $pagenum = $configutil->splash_new($_GET["pagenum"]);
						}

						$start = ($pagenum-1) * 20;
						$end = 20;				
						
						$query = "SELECT id,sx_type,sx_name,sx_introduce FROM slb_sx where c_isvalid=true and custid='".$customer_id."'";				              
						if($begintime!=""){
						   $query = $query." and UNIX_TIMESTAMP(c_createtime)>".strtotime($begintime);
						 }
						 if($endtime!=""){
						   $query = $query." and UNIX_TIMESTAMP(c_createtime)<".strtotime($endtime);
						 } 
						 if($p_name!=""){
						   $query = $query." and sx_name like '%" .$p_name. "%'";
						 }
						 if($p_type>0){
						   $query = $query." and sx_type=".$p_type;
						 }
						if($p_type==-1){
						   $query = $query." and sx_type=".$p_type;
						 }
						 if($p_type==-2){
						   $query = $query." and sx_type=0";
						 }
						$result = _mysql_query($query);
						$rcount_q2 = mysql_num_rows($result);
						
						$query1=$query.' order by sx_type  limit '.$start.','.$end;	 
						$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
					   
					   while ($row = mysql_fetch_object($result1)) {
						   $id = $row->id;
						   $sx_type = $row->sx_type;
						   $sx_type_name="";
						   $slb_type_state=0;
						   if($sx_type==-1){
							   $sx_type_name="属性类型";
							   $slb_type_state_SQL="select id FROM slb_type where c_isvalid=true and p_type='".$id."'";
							   $slb_type_state_result = _mysql_query($slb_type_state_SQL) or die('Query failed: ' . mysql_error());
								while ($slb_type_state_result_row = mysql_fetch_object($slb_type_state_result)) {
									$slb_type_state = $slb_type_state_result_row->id;
								}
						   }else if($sx_type==0){
							   $sx_type_name="单位属性";
						   }else if($sx_type>0){
							   $type_name_SQL="select sx_name FROM slb_sx where c_isvalid=true and id='".$sx_type."'";
							   $type_name_result = _mysql_query($type_name_SQL) or die('Query failed: ' . mysql_error());
								while ($type_name_row = mysql_fetch_object($type_name_result)) {
									$sx_type_name = $type_name_row->sx_name;
								}
						   }
						   
						   $sx_name = $row->sx_name;
						   $sx_introduce = $row->sx_introduce;
					   ?>
						<tr>
						   <td><?php echo $sx_type_name; ?>(ID:<?php echo $id; ?>)</td>
						   <td><?php echo $sx_name; ?></td>
						   <td><?php echo $sx_introduce; ?></td>
						   <td>
						   	<a  onclick="Payload(this,'<?php echo $id; ?>',1)" class="wsy_preview ka" title="编辑"><img style="width: 18px;height: 18px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon05.png" /></a>
							<a  onclick="delect_sx(this,'<?php echo $id; ?>','<?php echo $sx_type; ?>')" class="wsy_preview ka" title="删除"><img style="width: 18px;height: 18px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon04.png" /></a>
							<?php  if($slb_type_state==0 && $sx_type==-1){ ?>
							<a  onclick="cinsert_type(this,'<?php echo $id; ?>','<?php echo $sx_name; ?>')" class="wsy_preview ka" title="重新生成type"><img style="width: 18px;height: 18px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon04.png" /></a>
							<?php  } ?>
						   </td>
						</tr>
						   <tr class="ksto" style="display: none">
						  <td colspan="4" >
							<div class="kstd" style="padding: 0px 50px 0px 50px;border: 2px solid green;line-height: 30px;border-radius:5px;height: 170px;">
							<div style="float: left;width:25%;margin-top:5px">
							<input  type="text" value="<?php echo $id; ?>" class="id" style="display: none"/>
							<input  type="text" value="<?php echo $sx_type; ?>" class="sx_type" style="display: none"/>
							<a>属性类型：</a><input  type="text" value="<?php echo $sx_type_name; ?>" class="sx_type_name" style="line-height: 20px;border: 1px solid grey;border-radius:5px;margin-right: 50px;display: inline-block; "  readonly='readonly' class ="width160 "  /><br/>
							<a>属性名称：</a><input  type="text" value="<?php echo $sx_name; ?>" class="sx_name" style="line-height: 20px;border: 1px solid grey;border-radius:5px;display: inline-block;" />
							</div>
							<a style="float: left;margin-top:5px">描述：</a>
							<div style="float: left;width:40%;margin-top:5px">
							
							<textarea rows="3" cols="" style="width: 400px;height: 160px;border: 1px solid #CFCBCB;resize: none;" class="sx_introduce"><?php echo $sx_introduce; ?></textarea>
							</div>		
							<a style="float: right; left: 80%;margin-top:70px"><img onclick="save_sx(this)" style="width: 30px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon23.png"/></a>
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
		// var begintime = document.getElementById("begintime").value;
		// var endtime = document.getElementById("endtime").value;
		 var p_name = document.getElementById("p_name").value;
			var p_type = document.getElementById("p_type").value;
		 document.location= "add_sx.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=3&Itype=3&pagenum="+p+"&p_name="+p_name+"&p_type="+p_type;
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
		 //var begintime = document.getElementById("begintime").value;
		// var endtime = document.getElementById("endtime").value;
		 var p_name = document.getElementById("p_name").value;
	var p_type = document.getElementById("p_type").value;
		 document.location= "add_sx.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=3&Itype=3&pagenum="+p+"&p_name="+p_name+"&p_type="+p_type;
	}
  }
function searchForm(){
	//var begintime = document.getElementById("begintime").value;
    //var endtime = document.getElementById("endtime").value;
	var p_name = document.getElementById("p_name").value;
	var p_type = document.getElementById("p_type").value;
	document.location= "add_sx.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=3&Itype=3&pagenum=1&p_name="+p_name+"&p_type="+p_type;
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

</script>

<?php mysql_close($link);?>	

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>