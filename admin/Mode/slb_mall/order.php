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
$p_batchcode="";
if(!empty($_GET["p_batchcode"])){
	$p_batchcode = $configutil->splash_new($_GET["p_batchcode"]);
}
$card="";
if(!empty($_GET["card"])){
	$card = $configutil->splash_new($_GET["card"]);
}
$addit="";
if(!empty($_GET["addit"])){
	$addit = $configutil->splash_new($_GET["addit"]);
}
$PX="";
if(!empty($_GET["PX"])){
	$PX = $configutil->splash_new($_GET["PX"]);
}
$PXl=1;
if(!empty($_GET["PXl"])){
	$PXl = $configutil->splash_new($_GET["PXl"]);
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
    padding:0px; ;
}
tr {
    line-height: 22px;
}
table { table-layout:fixed; word-break: break-all; word-wrap: break-word; }  
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
				<form class="search" id="search_form" style="margin-left:18px; margin-top: 18px 0px 5px 0px;">
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
						<a style="margin-left: 30px;">订单号：	
						<input type="text" class="input "  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px"   id="p_batchcode" name="p_batchcode" value="<?php echo $p_batchcode; ?>"/>
						</a>
						<a style="margin-left: 30px;">card识别码：	
						<input type="text" class="input "  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px"   id="card" name="card" value="<?php echo $card; ?>"/>
						</a>
						<a style="margin-left: 30px;">ZDY识别码	：	
						<input type="text" class="input "  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px"   id="addit" name="addit" value="<?php echo $addit; ?>"/>
						</a>
							
					</div>  
					<div class="WSY_list" style="margin-top: 5px;">
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
						<input type="button" class="search_btn" onclick="exportRecord(1);" style="width:80px" value="导出订单"> 
						<select onchange="search_PX()" style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px;margin-left: 1%;" id="PX" name="PX">
						<option value="-10">我要排序</option>
						<option value="1" <?php if($PX==1){ echo "selected='selected'";}?>>下单时间（升）</option>
						<option value="2" <?php if($PX==2){ echo "selected='selected'";}?>>下单时间（降）</option>
						<option value="5" <?php if($PX==5){ echo "selected='selected'";}?>>完成时间（升）</option>
						<option value="6" <?php if($PX==6){ echo "selected='selected'";}?>>完成时间（降）</option>
						</select>
						<select onchange="search_PXl()" style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;width:100px;margin-left: 1%;" id="PXl" name="PXl">
						<option value="1" <?php if($PXl==1){ echo "selected='selected'";}?>>已支付</option>
						<option value="3" <?php if($PXl==3){ echo "selected='selected'";}?>>已完成</option>
						<option value="2" <?php if($PXl==2){ echo "selected='selected'";}?>>未支付</option>
						</select>
					</div> 					
				</form>	 
				<div >
				<table width="97%" class="WSY_table" id="WSY_t1">
					<thead class="WSY_table_header">
						<th width="10%">订单号</th>						
						<th width="6%">微信名称</th>
						<th width="5%">商品类型</th>
						<th width="10%">card识别码</th> 
						<th width="10%">ZDY识别码</th> 
						<th width="6%">商品名称</th> 
						<th width="5%">单价(数量)</th> 
						<th width="5%">商品总价</th> 
						<th width="5%">下单时间</th> 
						<th width="5%">完成时间</th>				
						<th width="4%">订单状态</th>
						<th width="8%">完成秘卡</th> 
						<th width="6%">完成账号</th> 
						<th width="5%">操作</th> 
					</thead>
					<tbody>
					   <?php 
						$is_auth_user= $_SESSION['is_auth_user'];
						$curr_login=$_SESSION['curr_login']; 

					   $pagenum = 1;

						if(!empty($_GET["pagenum"])){
						   $pagenum = $configutil->splash_new($_GET["pagenum"]);
						}

						$start = ($pagenum-1) * 20;
						$end = 20;	
						
						$total_SQL="select sum(o_totale_price) from slb_order where c_isvalid=true and o_state>0 and custid=".$customer_id;
						$total_R = _mysql_query($total_SQL);
						$total = mysql_result($total_R,0,0);
						$total	=sprintf("%.2f",$total);
						$query = 'SELECT id,userid,weixin_name,p_type,addit1,addit2,p_id,p_name,p_price,p_mun,o_totale_price,c_createtime,c_isvalid,custid,o_state,o_batchcode,o_time,o_login_name,o_code FROM slb_order where c_isvalid=true and custid='.$customer_id;				              
						if($begintime!=""){
						   $query = $query." and UNIX_TIMESTAMP(weixin_commonshop_publicwelfare_log.createtime)>".strtotime($begintime);
						 }
						 if($endtime!=""){
						   $query = $query." and UNIX_TIMESTAMP(weixin_commonshop_publicwelfare_log.createtime)<".strtotime($endtime);
						 } 
						 if($p_name!=""){
						   $query = $query." and p_name like '%" .$p_name. "%'";
						 }
						  if($p_batchcode!=""){
						   $query = $query." and o_batchcode like '%".$p_batchcode. "%'";
						 }
						  if($card!=""){
						   $query = $query." and addit1 like '%" .$card. "%'";
						 }
						  if($addit!=""){
						   $query = $query." and addit2 like '%" .$addit. "%'";
						 }
						 if($p_type>0){
						   $query = $query." and p_type=".$p_type;
						 }
						 if($PXl==1){
						   $query = $query." and o_state = 1 ";
						 }
						 if($PXl==2){
						   $query = $query." and o_state = 0 ";
						 }
						 if($PXl==3){
						   $query = $query." and o_state = 2 ";
						 }
						 
						 $result = _mysql_query($query);
						 $rcount_q2 = mysql_num_rows($result);
						
						 if($PX>0){
							 if($PX==1){
								$query1=$query.' order by c_createtime  '; 
							 }
							 if($PX==2){
								$query1=$query.' order by c_createtime desc '; 
							 }
							 if($PX==3){
								$query1=$query.' order by o_state  '; 
							 }
							 if($PX==4){
								$query1=$query.' order by o_state desc '; 
							 }
							 if($PX==5){
								$query1=$query.' order by o_time '; 
							 }
							 if($PX==6){
								$query1=$query.' order by o_time desc '; 
							 }
						 }else{
							 $query1=$query.' order by id desc ';
						 }
						  $query1=$query1.' limit '.$start.','.$end;
						$YZF=0; 
						$YWF=0;
						$YBF=0;
					   $result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
					   while ($row = mysql_fetch_object($result1)) {
							$id = $row->id;
							$userid = $row->userid;
							$weixin_name = $row->weixin_name;
							$p_type = $row->p_type;
							$p_type_name_SQL="select sx_name from slb_sx where sx_type=-1 and id='".$p_type."'";
							$p_type_name_R = _mysql_query($p_type_name_SQL);
							$p_type_name = mysql_result($p_type_name_R,0,0);
							$codeI_SQL="select code from slb_type where c_isvalid=1 and p_type='".$p_type."'";
							$codeI_R = _mysql_query($codeI_SQL);
							$codeI = mysql_result($codeI_R,0,0);
							$addit1 = $row->addit1;
							$addit2 = $row->addit2;
							$p_id = $row->p_id;
							$p_name = $row->p_name;
							$p_price = $row->p_price;
							$p_mun = $row->p_mun;
							$o_totale_price = $row->o_totale_price;
							$c_createtime = $row->c_createtime;
							$custid = $row->custid;
							$o_state = $row->o_state;
							$o_state_name="";
							if($o_state==0){
								$o_state_name="未支付";
								$YWF=$YWF+$o_totale_price;
								$YBF=$YBF+$o_totale_price;
							}else if($o_state==1){
								$o_state_name="已支付";
								$YZF=$YZF+$o_totale_price;
								$YBF=$YBF+$o_totale_price;
							}else{
								$o_state_name="已完成";
								$YZF=$YZF+$o_totale_price;
								$YBF=$YBF+$o_totale_price;
							}
							$o_batchcode = $row->o_batchcode;
							$o_time = $row->o_time;
							$o_login_name = $row->o_login_name;
							$o_code = $row->o_code;
							if($codeI==0){
								$o_code="关闭";
							}
								 
					   ?>
						<tr>
						   <td><?php echo $o_batchcode; ?></td>
						   <td><?php echo $weixin_name; ?></td>
						   <td><?php echo $p_type_name; ?></td>
						   <td><?php echo $addit1; ?></td>
						   <td><?php echo $addit2; ?></td>
						   <td><?php echo $p_name; ?></td>
						   <td><?php echo $p_price; ?>(<?php echo $p_mun; ?>)</td>
						   <td><?php echo $o_totale_price; ?></td>
						   <td><?php echo $c_createtime; ?></td>
						   <td class="o_time"><?php echo $o_time; ?></td>
						   <td class="state"><?php echo $o_state_name; ?></td>
						   <td class="code">
						   <?php if($codeI==0  && $o_state>0){?>
						   关闭
						   <?php }else if($codeI==1 && $o_state==1 ){?>
						   <input class="code" value="<?php echo $o_code; ?>" style="line-height: 25px;border: 1px solid red; width: 95%;border-radius: 5px;"/>
						   <?php }else if($codeI==1 && $o_state==2 ){?>
						   <?php echo $o_code; ?>
						   <?php }?>
						   </td>
						   <td class="login_name"><?php echo $o_login_name; ?></td>
						   <td>
						    <?php if($o_state==1){?>
						   <a  onclick="Payload(this,'<?php echo $id; ?>','<?php echo $curr_login; ?>','<?php echo $codeI; ?>')" class="wsy_preview ka" title="完成确认"><img style="width: 18px;height: 18px;cursor: pointer;" src="../../../common/images_V6.0/operating_icon/icon23.png" /></a>
						     <?php }?>
						   </td>


						</tr>
					   <?php } 
					   $YZF=sprintf("%.2f",$YZF);
					   $YWF=sprintf("%.2f",$YWF);
					   $YBF=sprintf("%.2f",$YBF);
					   
					   ?>
					    <tr>
						<td colspan="14">
						<div>
						<a style="display: inline-block;width: 24.5%;">本页支付销售额：<?php echo $YZF;?></a>
						<a style="display: inline-block;width: 24.5%;">本页未付销售额：<?php echo $YWF;?></a>
						<a style="display: inline-block;width: 24.5%;">本页订单销售额：<?php echo $YBF;?></a>
						<a style="display: inline-block;width: 24.5%;">总销售额：<?php echo $total;?></a>
						</div>
						</td>

						</tr>
					
					</tbody>					
				</table>
				</div>
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
<script src="../../../common/js/floatBox.js"></script>
<!-- 温馨提士弹窗 -->
<link rel="stylesheet" type="text/css" href="/weixinpl/common/kindly_reminder/kindly_reminder.css">
<script src="/weixinpl/common/kindly_reminder/kindly_reminder.js"></script>
<script type="text/javascript">
	kindly_reminder(); 
</script>
<!-- 温馨提士弹窗结束 -->
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
		 var p_batchcode = document.getElementById("p_batchcode").value;
		var card = document.getElementById("card").value;
		var addit = document.getElementById("addit").value;
		var PX = document.getElementById("PX").value;
		var PXl = document.getElementById("PXl").value;
		 document.location= "order.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=0&Itype=0&pagenum="+p+"&begintime="+begintime+"&endtime="+endtime+"&p_name="+p_name+"&p_type="+p_type+"&p_batchcode="+p_batchcode+"&card="+card+"&addit="+addit+"&PXl="+PXl+"&PX="+PX;
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
		 var p_batchcode = document.getElementById("p_batchcode").value;
			var card = document.getElementById("card").value;
			var addit = document.getElementById("addit").value;
			var PX = document.getElementById("PX").value;
			var PXl = document.getElementById("PXl").value;
		 document.location= "order.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=0&Itype=0&pagenum="+p+"&begintime="+begintime+"&endtime="+endtime+"&p_name="+p_name+"&p_type="+p_type+"&p_batchcode="+p_batchcode+"&card="+card+"&addit="+addit+"&PXl="+PXl+"&PX="+PX;
	}
  }
function searchForm(){
	var begintime = document.getElementById("begintime").value;
    var endtime = document.getElementById("endtime").value;
	var p_name = document.getElementById("p_name").value;
	var p_type = document.getElementById("p_type").value;
	var p_batchcode = document.getElementById("p_batchcode").value;
	var card = document.getElementById("card").value;
	var addit = document.getElementById("addit").value;
	var PX = document.getElementById("PX").value;
	var PXl = document.getElementById("PXl").value;
	document.location= "order.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=0&Itype=0&pagenum=1&begintime="+begintime+"&endtime="+endtime+"&p_name="+p_name+"&p_type="+p_type+"&p_batchcode="+p_batchcode+"&card="+card+"&addit="+addit+"&PXl="+PXl+"&PX="+PX;
}
function search_PX(){
	var begintime = document.getElementById("begintime").value;
    var endtime = document.getElementById("endtime").value;
	var p_name = document.getElementById("p_name").value;
	var p_type = document.getElementById("p_type").value;
	var p_batchcode = document.getElementById("p_batchcode").value;
	var card = document.getElementById("card").value;
	var addit = document.getElementById("addit").value;
	var PX = document.getElementById("PX").value;
	var PXl = document.getElementById("PXl").value;
	document.location= "order.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=0&Itype=0&pagenum=1&begintime="+begintime+"&endtime="+endtime+"&p_name="+p_name+"&p_type="+p_type+"&p_batchcode="+p_batchcode+"&card="+card+"&addit="+addit+"&PX="+PX+"&PXl="+PXl;
}
function search_PXl(){
	var begintime = document.getElementById("begintime").value;
    var endtime = document.getElementById("endtime").value;
	var p_name = document.getElementById("p_name").value;
	var p_type = document.getElementById("p_type").value;
	var p_batchcode = document.getElementById("p_batchcode").value;
	var card = document.getElementById("card").value;
	var addit = document.getElementById("addit").value;
	var PX = document.getElementById("PX").value;
	var PXl = document.getElementById("PXl").value;
	document.location= "order.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=0&Itype=0&pagenum=1&begintime="+begintime+"&endtime="+endtime+"&p_name="+p_name+"&p_type="+p_type+"&p_batchcode="+p_batchcode+"&card="+card+"&addit="+addit+"&PXl="+PXl+"&PX="+PX;
}
function exportRecord(num){
	switch(num){
		case 1: //订单导出
		  var name ="slb_order_excel";
		  break;	
	}

	/*导出自行安装订单筛选框*/
	var excelArray = [
						["id","id"],
						["o_batchcode","订单号"],
						["weixin_name","微信名称"],
						["p_type_name","商品类型"],
						["addit1","ZDY1识别码"],
						["addit2","ZDY2识别码"],
						["p_name","商品名称"],
						["p_price","单价(数量)"],
						["o_totale_price","商品总价"],
						["c_createtime","下单时间"],
						["o_time","完成时间"],
						["o_state_name","订单状态"],
						["o_code","完成秘卡"],
						["o_login_name","完成账号"]
					 ];
	exportBox(excelArray);
	$(".floatbox").show();

	$(".floatinputs").click(function(){
		var str="";
		$("input[name='excel_field[]']:checkbox").each(function(){ 
            if($(this).is(':checked')){
                str += $(this).val()+","
            }
        })
        str = str.substring(0,str.length-1);
		
		var begintime = document.getElementById("begintime").value;
	    var endtime = document.getElementById("endtime").value;
		var p_name = document.getElementById("p_name").value;
		var p_type = document.getElementById("p_type").value;
		var p_batchcode = document.getElementById("p_batchcode").value;
		var card = document.getElementById("card").value;
		var addit = document.getElementById("addit").value;
		var PX = document.getElementById("PX").value;
		var url="/weixin/plat/app/index.php/Excel/slb_order_excel/customer_id/<?php echo $customer_id; ?>";
		if(p_name!= ""){
			url = url +"/p_name/"+p_name;
		}
		if(p_type!= ""){
			url = url +"/p_type/"+p_type;
		}
		if(p_batchcode!= ""){
			url = url +"/p_batchcode/"+p_batchcode;
		}
		if(card!= ""){
			url = url +"/card/"+card;
		}
		if(addit!= ""){
			url = url +"/addit/"+addit;
		}
		if(PX!= ""){
			url = url +"/PX/"+PX;
		}
		if(begintime!= ""){
			url = url + "/begintime/" + begintime;
		}
		if(endtime!= ""){
			url = url + "/endtime/" + endtime;
		}
		if(str != ""){
			url = url + "/excel_fields/" + str;
		}
		url = url + "/";
		console.log(url);
		location.href = url;
		$(".floatbox").hide();
		$(".floatbox").remove();
	});
	
	//console.log(url);
	//alert(url);
	document.location=url; 
}
function Payload(obj,ID,login_name,codeI){
	var XID=$(obj).parent().parent();
	var code=XID.children(".code").children(".code").val();
	var date1 = getNowFormatDate();
	var login_name='<?php echo $curr_login ?>';
	if(codeI>0 && (code=='' ||code==null)){
		alert("请填写完成秘钥");
		return;
	}
	$.ajax({
        type: "post",
        url: "ajax_mall.php",
		dataType: "json",
		//begintime:begintime,endtime:endtime,
        data: {op: 31,ID:ID,o_code:code,login_name:login_name},
        success: function (date) {
			alert(date.msg); 
			if(date.result==1){	
				XID.children(".o_time").html(date1);
				XID.children(".state").html("已完成");
				XID.children(".code").html(code);
				XID.children(".login_name").html(login_name);
				$(obj).detach();
			}		
        }
    });
}
function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
            + " " + date.getHours() + seperator2 + date.getMinutes()
            + seperator2 + date.getSeconds();
    return currentdate;
}


</script>

<?php mysql_close($link);?>	

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>