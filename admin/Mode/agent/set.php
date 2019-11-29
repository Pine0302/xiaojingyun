<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=0;//头部文件  0基本设置,1提现记录,2代理商管理

//供应商模式,渠道开通与不开通
$is_supplierstr=0;//渠道取消供应商功能
$sp_count=0;//渠道取消供应商功能
$sp_query="select count(1) as sp_count from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城供应商模式' and c.id=cf.column_id";
$sp_result = _mysql_query($sp_query) or die('W_is_supplier Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($sp_result)) {
   $sp_count = $row->sp_count;
   break;
}
if($sp_count>0){
   $is_supplierstr=1;
}

//代理商信息
$sendstatus=0;
$is_export_order=0;
$query = "select id,agent_price,agent_detail,not_agent_tip,sendstatus,is_showdiscount,is_export_order from weixin_commonshop_agents where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$agent_price=""; 	
$agent_detail="";
$is_showdiscount=0;
$not_agent_tip ="对不起,你仍未成为代理商！";
while ($row = mysql_fetch_object($result)) {
    $agent_price=$row->agent_price;
	$agent_detail=$row->agent_detail;
	$not_agent_tip=$row->not_agent_tip;
	$sendstatus=$row->sendstatus; //代理商发货开关
	$is_showdiscount=$row->is_showdiscount; //是否在代理商申请页面显示折扣
	$is_export_order=$row->is_export_order; //是否开启订单导出
	
}
$pricearr = explode(",",$agent_price);
$len =  count($pricearr);
$diy_num = $len;

$query = "select isOpenAgent from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$isOpenAgent = 0; 
while ($row = mysql_fetch_object($result)) {
	$isOpenAgent=$row->isOpenAgent;//是否在个人中心开启代理商申请
}
?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/agent/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<!--编辑器多图片上传引入开始--->
<script type="text/javascript" src="/weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/handlers.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>
<!--编辑器多图片上传引入结束--->
<title>基本设置</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			// include("../../../../weixinpl/back_newshops/Mode/agent/basic_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/agent/basic_head.php");
			?>
			<!--列表头部切换结束-->
			<form action="save_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
				<div class="WSY_remind_main">
					<dl class="WSY_remind_dl02" style="margin-top:40px;"> 
						<dt style="line-height:20px;" class="WSY_left">客户端个人中心开启代理商申请入口：</dt>
						<dd>
							<?php if($isOpenAgent==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_isOpenAgent(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_isOpenAgent(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_isOpenAgent(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_isOpenAgent(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
						</dd>						
						<input type="hidden" name="isOpenAgent" id="isOpenAgent" value="<?php echo $isOpenAgent; ?>" />
					</dl>				
					<dl class="WSY_remind_dl02" style="margin-top:24px;<?php if($is_supplierstr>0){?>display:none;<?php } ?>"> 
						<dt style="line-height:20px;" class="WSY_left">开启代理商发货：</dt>
						<dd>
							<?php if($sendstatus==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_sendstatus(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_sendstatus(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_sendstatus(0)" <?php if($is_supplierstr!=1){?>class="WSY_bot"<?php }?> style="display: none; left: 30px;"></li>
								<span onclick="change_sendstatus(1)" <?php if($is_supplierstr!=1){?>class="WSY_bot2"<?php }?> style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
							<span style="float: left;margin: -16px 210px;color: #888;">已开启代理商发货的就不要开启合作商，以免造成数据混乱！</span>
						</dd>						
						<input type="hidden" name="sendstatus" id="sendstatus" value="<?php echo $sendstatus; ?>" />
					</dl>
					<dl class="WSY_remind_dl02" style="margin-top:24px;"> 
						<dt style="line-height:20px;" class="WSY_left">客户端显示申请代理商折扣：</dt>
						<dd>
							<?php if($is_showdiscount==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_is_showdiscount(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_showdiscount(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_showdiscount(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_showdiscount(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>							
						</dd>						
						<input type="hidden" name="is_showdiscount" id="is_showdiscount" value="<?php echo $is_showdiscount; ?>" />
					</dl>
					<dl class="WSY_remind_dl02" style="margin-top:24px;"> 
						<dt style="line-height:20px;" class="WSY_left">代理商订单导出：</dt>
						<dd>
							<?php if($is_export_order==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_isExportOrder(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_isExportOrder(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_isExportOrder(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_isExportOrder(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>							
						</dd>						
						<input type="hidden" name="isExportOrder" id="isExportOrder" value="<?php echo $is_export_order; ?>" />
					</dl>					
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;" class="WSY_left">非代理商提示信息：</dt>
						<dd>
							<input type="text" class="not_agent_tip" name="not_agent_tip" value="<?php echo $not_agent_tip; ?>" type="text" placeholder="对不起,你仍未成为代理商">
						</dd>
					</dl>
					<!--列表按钮开始-->
					<div class="WSY_list" style="margin-top: 18px;">
						<li class="WSY_left"><a>代理商分配设置</a></li>
						<li class="WSY_addto"><a href="javascript:diy_add(1);">添加代理商级别</a></li>
					</div>
					<!--列表按钮结束-->
        
					<!--表格开始-->
					<table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="25%">代理商级别</th>
							<th width="25%">价钱</th>
							<th width="25%">代理折扣</th>
							<th width="25%">操作</th> 
						</thead>
						<?php
					   $is_1= 0;
					   $is_2= 0;
					   $is_3 = 0;
					   
					   for($i=0;$i<$len;$i++){
						  
						   $varr= $pricearr[$i];
						   if(empty($varr)){
							  continue;
						   }
						   
						   $vlst = explode("_",$varr);

						   
						   $type = $vlst[0];
						   if(empty($vlst[1])){
								switch($i){
									case 0:
										$exp_name = '一级代理';
									break;
									case 1:
										$exp_name = '二级代理';
									break;
									case 2:
										$exp_name = '三级代理';
									break;
									case 3:
										$exp_name = '四级代理';
									break;
									case 4:
										$exp_name = '五级代理';
									break;
									case 5:
										$exp_name = '六级代理';
									break;
									case 6:
										$exp_name = '七级代理';
									break;
									case 7:
										$exp_name = '八级代理';
									break;
									
								}
							}else{
								$exp_name = $vlst[1];
							}
						   $name = $vlst[1];
						   
						   $value = $vlst[2];
						   $discount = $vlst[3];
						
						   switch((int)$type){
							  case 1:
								 ?>
								  <tr id="diy_item_<?php echo $i+1; ?>">
									  <td class="WSY_t5"><input type=text name="singletext" id="singletext_<?php echo $diy_num; ?>" value="<?php echo $exp_name; ?>" placeholder="请输入代理商级别名称"/></td>
									  <td class="WSY_t5"><input type=text name="singletext_con" id="singletext_con<?php echo $diy_num; ?>" value="<?php echo $value; ?>" placeholder="请输入价钱" onkeyup="value=value.replace(/[^\d.]/g,'')"/><span><?php echo OOF_T ?></span></td> 
									  <td class="WSY_t5"><input type=text name="singletext_dc" id="singletext_dc<?php echo $diy_num; ?>" value="<?php echo $discount; ?>" placeholder="请输入代理折扣" onkeyup="clearNoNum(this,4);"/><span>%</span></td>
									  <td class="WSY_t4"><a href="javascript:diy_del(<?php echo $i+1; ?>,1);"><img src="../../../common/images_V6.0/operating_icon/icon04.png" align="absmiddle" alt="删除"></a>&nbsp;</td>
								   </tr>
						<?php    $is_1 = 1; 
								 break;
							  case 2:
								  ?>						  
								  <tr id="diy_item_<?php echo $i+1; ?>">
									  <td class="WSY_t5">日期选择</td>
									  <td class="WSY_t5"><input type=text name="singledate" id="singledate_<?php echo $diy_num; ?>" value="<?php echo $name; ?>" placeholder="请输入字段名" /></td>
									  <td class="WSY_t5"><input type=text name="singledate_con" id="singledate_con<?php echo $diy_num; ?>" value="<?php echo $value; ?>" placeholder="请输入初始内容" /></td>
									  <td class="WSY_t4"><a href="javascript:diy_del(<?php echo $i+1; ?>,2);">删除</a>&nbsp;<a href="javascript:diy_add(2);">添加</a></td>
								   </tr>
						<?php    $is_2  =2;
								 break;
							   case 3:
							   ?>
							   <tr id="diy_item_<?php echo $i+1; ?>">
								  <td class="WSY_t5">下拉选择</td>
								  <td class="WSY_t5"><input type=text name="singleselect" id="singleselect_<?php echo $diy_num; ?>" value="<?php echo $name; ?>" placeholder="自定义下拉框" /></td>
								  <td class="WSY_t5"><input type=text name="singleselect_con" id="singleselect_con<?php echo $diy_num; ?>" value="<?php echo $value; ?>" placeholder="选择1|选择2" /></td>
								  <td class="WSY_t4"><a href="javascript:diy_del(<?php echo $i+1; ?>,3);">删除</a>&nbsp;<a href="javascript:diy_add(3);">添加</a></td>
							   </tr>
								 
						<?php   $is_3 = 3;
								break;
						}
					   } 
					   
						  if(empty($is_1)){ 
							 $diy_num++;
							
						  ?>
								   <tr id="diy_item_<?php echo $diy_num; ?>">
									  <td class="WSY_t5"><input type=text name="singletext" value="" id="singletext_<?php echo $diy_num; ?>" placeholder="请输入代理商级别名称"   /></td>
									  <td class="WSY_t5"><input type=text name="singletext_con" value="" id="singletext_con<?php echo $diy_num; ?>" placeholder="请输入价钱"   onkeyup="value=value.replace(/[^\d.]/g,'')"/><span><?php echo OOF_T ?></span></td>
									  <td class="WSY_t5"><input type=text name="singletext_dc" value="" id="singletext_dc<?php echo $diy_num; ?>" placeholder="请输入代理折扣"   onkeyup="clearNoNum(this,4);"/><span>%</span></td>
									  <td class="WSY_t4"><a href="javascript:diy_del(<?php echo $i+1; ?>,1);"><img src="../../../common/images_V6.0/operating_icon/icon04.png" align="absmiddle" alt="删除"></a>&nbsp;</td>
								   </tr>
						   
						  <?php
						  }
					   ?>
					   <!--
						  <?php if(empty($is_2)){ 
							  $diy_num++;
						  ?>
								   <tr id="diy_item_<?php echo $diy_num; ?>">
									  <td class="WSY_t5">日期选择</td>
									  <td class="WSY_t5"><input type=text name="singledate" value="" id="singledate_<?php echo $diy_num; ?>" placeholder="请输入字段名"   /></td>
									  <td class="WSY_t5"><input type=text name="singledate_con" value="" id="singledate_con<?php echo $diy_num; ?>" placeholder="请输入初始内容"  /></td>
									  <td class="WSY_t4"><a href="javascript:diy_del(<?php echo $diy_num; ?>,2);">删除</a>&nbsp;<a href="javascript:diy_add(2);">添加</a></td>
								   </tr>
						   
						  <?php
						  }
					   ?>
					   
						<?php if(empty($is_3)){ 
							  $diy_num++;
						?>
								   <tr id="diy_item_<?php echo $diy_num; ?>">
									  <td class="WSY_t5">下拉选择</td>
									  <td class="WSY_t5"><input type=text name="singleselect" id="singleselect_<?php echo $diy_num; ?>" value="" placeholder="自定义下拉框" /></td>
									  <td class="WSY_t5"><input type=text name="singleselect_con" id="singleselect_con<?php echo $diy_num; ?>" value="" placeholder="选择1|选择2" /></td>
									  <td class="WSY_t4"><a href="javascript:diy_del(<?php echo $diy_num; ?>,3);">删除</a>&nbsp;<a href="javascript:diy_add(3);">添加</a></td>
								   </tr>
						   
						  <?php
						  }
					   ?>
						-->
						
					</table>
					<div class="clear"></div>
					<!--表格结束-->
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;" class="WSY_left">代理商协议：</dt>											
					</dl>	
					<textarea id="editor1"   name="agent_detail"><?php echo $agent_detail;?></textarea>			
					<input type=hidden name="agent_price" id="agent_price" value="<?php echo $agent_price ?>" />
					<div class="WSY_text_input"><input type="submit" class="WSY_button" value="提交保存"  onclick="return subBase();"><br class="WSY_clearfloat"></div>
				</div> 
			</form>
		</div>
	</div>
<?php mysql_close($link);?>	
<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<script>
function change_sendstatus(obj){ 
	$("#sendstatus").val(obj);
}
function change_is_showdiscount(obj){ 
	$("#is_showdiscount").val(obj);
}

function change_isOpenAgent(obj){
	$("#isOpenAgent").val(obj);
}

function change_isExportOrder(obj){
	$("#isExportOrder").val(obj);
}

CKEDITOR.replace( 'editor1',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Images',
filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Flash',
filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});


var diy_num = <?php echo $diy_num; ?>;
function diy_add(dtype){
    
   var str = "";
   diy_num++;
   switch(dtype){
      case 1:
	      str = str + "<tr id=\"diy_item_"+diy_num+"\">"
				+ "<td class=\"WSY_t5\"><input type=text name=\"singletext\" id=\"singletext_"+diy_num+"\" value=\"\" placeholder=\"请输入代理商级别名称\"  /></td>"
				+ "<td class=\"WSY_t5\"><input type=text name=\"singletext_con\" id=\"singletext_con"+diy_num+"\" value=\"\" placeholder=\"请输入价钱\" onkeyup=\"value=value.replace(\/[^\\d.]\/g,\'\')\"/><span><?php echo OOF_T ?></span></td>"
				+ "<td class=\"WSY_t5\"><input type=text name=\"singletext_dc\" id=\"singletext_dc"+diy_num+"\" value=\"\" placeholder=\"请输入代理折扣\" onkeyup=\"clearNoNum(this,4);\"/><span>%</span></td>"
				+ "<td class=\"WSY_t4\"><a href=\"javascript:diy_del("+diy_num+",1);\"><img src=\"../../../common/images_V6.0/operating_icon/icon04.png\" align=\"absmiddle\" alt=\"删除\"></a>&nbsp;</td>"
				+ "</tr>";
	     break;
	  case 2:
	     str = str + "<tr id=\"diy_item_"+diy_num+"\">"
	            + "<td class=\"WSY_t5\">日期选择</td>"
				+ "<td class=\"WSY_t5\"><input type=text name=\"singledate\" id=\"singledate_"+diy_num+"\" value=\"\" placeholder=\"请输入字段名\" /></td>"
				+ "<td class=\"WSY_t5\"><input type=text name=\"singledate_con\" id=\"singledate_con"+diy_num+"\" value=\"\" placeholder=\"请输入初始内容\" /></td>"
				+ "<td class=\"WSY_t4\"><a href=\"javascript:diy_del("+diy_num+",2);\">删除</a>&nbsp;<a href=\"javascript:diy_add(2,"+diy_num+");\">添加</a></td>"
				+ "</tr>";
	     break; 
	  case 3:
	     str = str + "<tr id=\"diy_item_"+diy_num+"\">"
	            + "<td class=\"WSY_t5\">下拉选择</td>"
				+ "<td class=\"WSY_t5\"><input type=text name=\"singleselect\" id=\"singleselect_"+diy_num+"\" value=\"\" placeholder=\"自定义下拉框\" /></td>"
				+ "<td class=\"WSY_t5\"><input type=text name=\"singleselect_con\" id=\"singleselect_con"+diy_num+"\" value=\"\" placeholder=\"选择1|选择2\" /></td>"
				+ "<td class=\"WSY_t4\"><a href=\"javascript:diy_del("+diy_num+",3);\">删除</a>&nbsp;<a href=\"javascript:diy_add(3,"+diy_num+");\">添加</a></td>"
				+ "</td>";
	     break;
   }
   $("#WSY_t1").append(str);
}

function diy_del(num,type){
	if(confirm("数据删除后不能恢复哦！确认要删除吗？")){
		document.getElementById("diy_item_"+num).style.display="none";
		document.getElementById("diy_item_"+num).innerHTML="";
	} 
}

 function subBase(){
	var str = "";
	var singletext = document.getElementsByName("singletext");
	var singletext_con = document.getElementsByName("singletext_con");
	var singletext_dc = document.getElementsByName("singletext_dc");

	var len = singletext.length;

	for(i=0;i<len;i++){
	  
	    var v = singletext[i].value;
		var con = parseFloat(singletext_con[i].value).toFixed(2);
		var dc = singletext_dc[i].value;
		if(v==""){
			alert('请输入代理商级别名称');
		   return false;
		}
		if(con==""){
			alert('请输入价钱!');
		   return false;
		}
		if(con<=0){
			alert('价钱必须要大于0!');
		   return false;
		}
		if(dc==""){
			alert('请输入代理商折扣!');
		   return false;
		}
		
		str = str +"1_"+v+"_"+con+"_"+dc+",";
		
	}
	
	if(str!=""){
	   str = str.substring(0,str.length-1);
	}
	document.getElementById("agent_price").value=str;
	//document.getElementById("upform").submit();
 }
</script>

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>