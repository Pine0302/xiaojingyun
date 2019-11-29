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
						<a style="margin-left: 30px;">商品名称：	
						<input type="text" class="input "  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px"   id="p_name" name="p_name" value="<?php echo $p_name; ?>"/>
						</a>
						<a style="margin-left: 30px;">商品类型：	
						<select style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px" id="p_type" name="p_type">
						<option value="-10">全部</option>
						<?php
							$S_SX_1_SQL="select id,sx_type,sx_name,sx_introduce from slb_sx where sx_type=-1 and c_isvalid=1 and custid='".$customer_id."'";
							$S_SX_1_R = _mysql_query($S_SX_1_SQL) or die('Query failed1: ' . mysql_error());
							while ($S_SX_2_row = mysql_fetch_object($S_SX_1_R)) {
							$sx_2_id=$S_SX_2_row->id;
							$sx_2_name=$S_SX_2_row->sx_name;
							?>
							<option value="<?php echo $sx_2_id; ?>" <?php if($sx_2_id==$p_type){ echo "selected='selected'"; } ?>><?php echo $sx_2_name; ?></option>
						<?php }?>
						</select>
						</a>
						<a  style="margin-left: 30px;">创建时间：				
						<span class="WSY_generalize_dl08" >
							<span id="searchtype3" class="display">
								<input type="text" class="input Wdate" style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="begintime" name="AccTime_A" value="<?php echo $begintime; ?>" maxlength="21" id="K_1389249066532" />
								-
							</span>
								<input type="text" class="input  Wdate"  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="endtime" name="AccTime_B" value="<?php echo $endtime; ?>" maxlength="20" id="K_1389249066580" />
						</span>
						</a>
						<input type="button" class="search_btn" onclick="searchForm();" style="width:80px" value="搜 索"> 
					</div>     
				</form>	  
				<table width="97%" class="WSY_table" id="WSY_t2">
					<thead class="WSY_table_header">
						<th width="8%">商品名称（ID）</th>
						<th width="8%">商品UI图</th>
						<th width="8%">商品类型</th>
						<th width="8%">属性名称</th>
						<th width="5%">商品价格</th> 
						<th width="15%">创建时间</th> 
						<th width="5%">商品状态</th> 
						<th width="35%">商品描述</th> 
						<th width="8%">操作</th> 
					</thead>
					<tbody>
					   <?php 
					   
					   $pagenum = 1;

						if(!empty($_GET["pagenum"])){
						   $pagenum = $configutil->splash_new($_GET["pagenum"]);
						}

						$start = ($pagenum-1) * 20;
						$end = 20;				
						
						$query = 'SELECT id,p_name,p_type,p_price,p_unit,p_unit_id,p_url,c_createtime,p_introduce,p_status FROM slb_product where c_isvalid=true and custid='.$customer_id;				              
						if($begintime!=""){
						   $query = $query." and UNIX_TIMESTAMP(slb_product.c_createtime)>".strtotime($begintime);
						 }
						 if($endtime!=""){
						   $query = $query." and UNIX_TIMESTAMP(slb_product.c_createtime)<".strtotime($endtime);
						 } 
						 if($p_name!=""){
						   $query = $query." and p_name like '%" .$p_name. "%'";
						 }
						 if($p_type>0){
						   $query = $query." and p_type=".$p_type;
						 }
						 $result = _mysql_query($query);
						 $rcount_q2 = mysql_num_rows($result);
						 $query1=$query.' order by id desc limit '.$start.','.$end;
						 
					   $result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
					   
					   while ($row = mysql_fetch_object($result1)) {
						   $id = $row->id;
						   $p_name = $row->p_name;
						   $p_type = $row->p_type;
						   $p_type_name_SQL="select sx_name from slb_sx where sx_type=-1 and id='".$p_type."'";
							$p_type_name_R = _mysql_query($p_type_name_SQL);
							$p_type_name = mysql_result($p_type_name_R,0,0);
						   $p_price = $row->p_price;
						   $p_unit = $row->p_unit;
						   $p_unit_id = $row->p_unit_id;
						   $p_url = $row->p_url;
						   $c_createtime = $row->c_createtime;
						   $p_introduce = $row->p_introduce;
						   $p_status = $row->p_status;
						   $p_sx_name_SQL="select sx_name,sx_id from slb_p_sx where  p_id='".$id."' limit 0,1";
						   $p_sx_name_R = _mysql_query($p_sx_name_SQL);
							$p_sx_name = mysql_result($p_sx_name_R,0,0);
							$p_sx_id = mysql_result($p_sx_name_R,0,1);
							$p_introduce_t ="";
							$p_introduce_arr= array();
							$p_introduce_arr = explode("&", $p_introduce);
							for($i=0;$i<count($p_introduce_arr);$i++){
								if($p_introduce_arr[$i]!=null || $p_introduce_arr[$i]!='' ){
									if($p_introduce_t==""){
										$p_introduce_t=	$p_introduce_arr[$i];
									}else{
										$p_introduce_t=	$p_introduce_t.",".$p_introduce_arr[$i];
									}
									
								}
								
							}
							
					   ?>
						<tr>
						   <td style="text-align: center;"><?php echo $p_name; ?>(<?php echo $id; ?>)</td>
						   <td style="text-align: center;"><img alt="商品背景图" src="<?php echo $p_url; ?>" style='width:70px;height:50px'></td>
						   <td style="text-align: center;"><?php echo $p_type_name; ?></td>
						   <td style="text-align: center;"><?php echo $p_sx_name; ?></td>
						   <td style="text-align: center;"><?php echo $p_price; ?><br/><?php echo $p_unit; ?></td>
						   <td style="text-align: center;"><?php echo $c_createtime; ?></td>
							<td class="kot" style="text-align: center;"><?php if($p_status==0){ ?>保存<?php }?><?php if($p_status==1){ ?>出售<?php }?></td>
						   <td style="text-align: center;"><?php echo $p_introduce_t; ?></td>
						   <td>
						   <?php if($p_status==0){ ?>
						    <a  onclick="Payload(this,'<?php echo $id; ?>',1)" class="wsy_preview ka" title="上架出售"><img style="width: 18px;height: 18px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon32.png" /></a>
							<?php }?>
							<?php if($p_status==1){ ?>
							<a  onclick="Payload(this,'<?php echo $id; ?>',2)" class="wsy_preview ka" title="下架保存"><img style="width: 18px;height: 18px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon33.png" /></a>
							<?php }?>
							<?php if($p_status==0){ ?>
							<a  onclick="Payload(this,'<?php echo $id; ?>',3)" class="wsy_preview ka" title="商品编辑"><img style="width: 18px;height: 18px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon05.png" /></a>
							<a  onclick="Payload(this,'<?php echo $id; ?>',4)" class="wsy_preview ka" title="商品删除"><img style="width: 18px;height: 18px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon04.png" /></a>
							<?php }?>
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
		 var begintime = document.getElementById("begintime").value;
		 var endtime = document.getElementById("endtime").value;
		 var p_name = document.getElementById("p_name").value;
		 var p_type = document.getElementById("p_type").value;
		 document.location= "product.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=2&Itype=2&pagenum="+p+"&begintime="+begintime+"&endtime="+endtime+"&p_name="+p_name+"&p_type="+p_type;
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
		 var begintime = document.getElementById("begintime").value;
		 var endtime = document.getElementById("endtime").value;
		 var p_name = document.getElementById("p_name").value;
		 var p_type = document.getElementById("p_type").value;
		 document.location= "product.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=2&Itype=2&pagenum="+p+"&begintime="+begintime+"&endtime="+endtime+"&p_name="+p_name+"&p_type="+p_type;
	}
  }
function searchForm(){
	var begintime = document.getElementById("begintime").value;
    var endtime = document.getElementById("endtime").value;
	var p_name = document.getElementById("p_name").value;
	var p_type = document.getElementById("p_type").value;
	document.location= "product.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=2&Itype=2&pagenum=1&begintime="+begintime+"&endtime="+endtime+"&p_name="+p_name+"&p_type="+p_type;
}
function  Payload(obj,ID,type){
	var XID=$(obj).parent().parent();
	//XIDO.children("tr").eq(XID.index()+1).detach();
	var op=0;
	if(type==1){
		if(XID.children(".kot").html()=="出售"){
			alert("该商品已经处于上架状态");
			return;
		}
		op=11;
	}else if(type==2){
		if(XID.children(".kot").html()=="保存"){
			alert("该商品已经处于下架状态");
			return;
		}
		op=12;
		
	}else if(type==3){
		location.href = "add_pro.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=4&Itype=4&ID="+ID+"";
		return;
		
	}else if(type==4){
		 if (!confirm("确认要删除？")) {
            window.event.returnValue = false;
        }
		if(XID.children(".kot").html()=="出售"){
			alert("该商品已经处于上架状态;不能删除");
			return;
		}
		op=13;
	}
	//kot
	$.ajax({
        type: "post",
        url: "ajax_mall.php",
		dataType: "json",
		//begintime:begintime,endtime:endtime,
        data: {op:op,ID:ID},
        success: function (date) {
			if(date.result==1){
				location.href = "product.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=2&Itype=2";			
			}else{
				alert(date.msg); 
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