<?php

header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=1;//头部文件  0基本设置,1基金明细

$query ="select isOpenPublicWelfare from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$isOpenPublicWelfare = $row->isOpenPublicWelfare;
}
$search_name="";
if(!empty($_GET["search_name"])){
	$search_name = $configutil->splash_new($_GET["search_name"]);
}
$publicwelfare=0;
$query ="select publicwelfare from weixin_commonshop_publicwelfare where isvalid=true and customer_id=".$customer_id;
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $publicwelfare = $row->publicwelfare;
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
			<?php
			// include("../../../../weixinpl/back_newshops/Mode/welfare/basic_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/welfare/basic_head.php");
			?>
			<!--列表头部切换结束-->
			<div class="WSY_remind_main">
				<dl class="WSY_remind_dl02" style="margin-left: 36px;">
					<dt style="line-height:28px;" class="WSY_left">公益基金总额：</dt>
					<dd>
						<span style="padding-left:10px;color:red;font-size:24px;font-weight:bold">￥<?php echo $publicwelfare;?></span>
					</dd>
				</dl>
				<form class="search" id="search_form" style="margin-left:18px; margin-top: 18px;">
					<div class="WSY_list" style="margin-top: 18px;">
						<li class="WSY_left"><a>捐助时间：</a></li>					
						<span class="WSY_generalize_dl08" >
							&nbsp;&nbsp;<span id="searchtype3" class="display">
								<input type="text" class="input Wdate" style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="begintime" name="AccTime_A" value="<?php echo $begintime; ?>" maxlength="21" id="K_1389249066532" />
								-
							</span>
								<input type="text" class="input  Wdate"  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="endtime" name="AccTime_B" value="<?php echo $endtime; ?>" maxlength="20" id="K_1389249066580" />
						</span>&nbsp;  
						<input type="button" class="search_btn" onclick="searchForm();" value="搜 索"> 
					</div>     
				</form>	  
				<table width="97%" class="WSY_table" id="WSY_t1">
					<thead class="WSY_table_header">
						<th width="20%">名称</th>
						<th width="20%">订单号</th>
						<th width="20%">捐助时间</th>
						<th width="20%">个人累计捐助金额</th> 
						<th width="20%">单次捐助金额</th> 
					</thead>
					<tbody>
					   <?php 
					   
					   $pagenum = 1;

						if(!empty($_GET["pagenum"])){
						   $pagenum = $configutil->splash_new($_GET["pagenum"]);
						}

						$start = ($pagenum-1) * 20;
						$end = 20;				
						
						$query = 'SELECT user_id,createtime,before_score,add_score,batchcode FROM weixin_commonshop_publicwelfare_log where isvalid=true and customer_id='.$customer_id;				              
						if($begintime!=""){
						   $query = $query." and UNIX_TIMESTAMP(weixin_commonshop_publicwelfare_log.createtime)>".strtotime($begintime);
						 }
						 if($endtime!=""){
						   $query = $query." and UNIX_TIMESTAMP(weixin_commonshop_publicwelfare_log.createtime)<".strtotime($endtime);
						 } 
						 $result = _mysql_query($query);
						 $rcount_q2 = mysql_num_rows($result);
						 $query1=$query.' order by id desc limit '.$start.','.$end;
						 
					   $result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
					   
					   while ($row = mysql_fetch_object($result1)) {
						   $welfare_user_id = $row->user_id;
						   $createtime = $row->createtime;
						   $before_score = $row->before_score;
						   $add_score = $row->add_score;
						   $batchcode = $row->batchcode;
						   $total_score=$before_score+$add_score;
							$query2= "select name,weixin_name from weixin_users where isvalid=true and id=".$welfare_user_id." limit 0,1"; 
							$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
							$weixin_name="";
							$username="";
							while ($row1 = mysql_fetch_object($result2)) {
								$username=$row1->name;
								$weixin_name = $row1->weixin_name;
								$username = $username."(".$weixin_name.")";
							}	
							if($username==""){
								$username="未知";
							}	
					   ?>
						<tr>
						   <td><?php echo $username; ?></td>
						   <td><a href="../../Order/order/order.php?&search_batchcode=<?php echo $batchcode; ?>"><?php echo $batchcode; ?></a></td>
						   <td><?php echo $createtime; ?></td>
						   <td><?php echo $total_score; ?></td>
						   <td><?php echo $add_score; ?></td>
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
		 document.location= "welfare_log.php?pagenum="+p+"&begintime="+begintime+"&endtime="+endtime;
	   }
    });

  var pagenum = <?php echo $pagenum ?>;
   var page = count;
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		 var begintime = document.getElementById("begintime").value;
		 var endtime = document.getElementById("endtime").value;
		 document.location= "welfare_log.php?pagenum="+p+"&begintime="+begintime+"&endtime="+endtime;
	}
  }
function searchForm(){
	var begintime = document.getElementById("begintime").value;
    var endtime = document.getElementById("endtime").value;
	document.location= "welfare_log.php?pagenum=1&begintime="+begintime+"&endtime="+endtime;
}

</script>

<?php mysql_close($link);?>	

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>