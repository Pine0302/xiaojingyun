<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require('../../../../weixinpl/proxy_info.php');
require('../../../../weixinpl/auth_user.php');

$op = "";
if(!empty($_GET["op"])){
   $op = $_GET["op"];
}

$pagenum = 1;
if(!empty($_GET["pagenum"])){
   $pagenum = $_GET["pagenum"];
}

$type = -1;
if(!empty($_GET["type"])){
   $type = $_GET["type"];
}

$nowtime = time();
$year = date('Y',$nowtime);
$month = date('m',$nowtime);
$day = date('d',$nowtime);



$search_msg="";
if($_GET["search_msg"]!=""){
	$search_msg = $configutil->splash_new($_GET["search_msg"]);
}
$appid=-1;
if(!empty($_GET["appid"])){
	$appid = $configutil->splash_new($_GET["appid"]);
}

$msgtype=-1;
if($_GET["msgtype"]!=""){
	$msgtype = $configutil->splash_new($_GET["msgtype"]);
}


$pagenum = 1;
if(!empty($_GET["pagenum"])){
	$pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * 20;
$end = 20;
$query="select m.id,m.appid,m.content,m.type,m.msgtype,m.msgid,m.createtime from appusers u inner join appmsg m where u.isvalid=true and u.id=m.appid and u.customerid=".$customer_id;
$result_count = _mysql_query($query) or die('Query failed: ' . mysql_error());
$rcount_all = mysql_num_rows($result_count);  //总消息数
$query.=" and m.msgtype=0";  
$result_count = _mysql_query($query) or die('Query failed: ' . mysql_error());
$rcount_sys = mysql_num_rows($result_count);   //系统消息数
?>

<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<style>
.WSY_page .WSY_page_search{height:26px;}
.WSY_list .WSY_left{padding-left:0px;}
.WSY_righticon {
    margin-right: 30px;
}
.WSY_input01 input[type="text"] {
    padding-left: 5px;
}
</style>
</head>

<body>
	<!--内容框架-->
	<div class="WSY_content">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a href="weishang.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>">操作</a>
					<a class="white1">信息推送记录</a>
				</div>
			</div>
			<!--列表头部切换结束-->
			<!--门店列表开始-->
			<div class="WSY_data">
				<!--列表按钮开始-->
				<div class="WSY_list" id="WSY_list">
						
					<ul class="WSY_righticon">
						<li><a name="add_store" id="add_store"href="messenge.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>">返回</a></li>
						
					</ul>
					<br class="WSY_clearfloat";>
				</div>
				<!--列表按钮开始-->
			<!--表格开始-->
			<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
				<thead class="WSY_table_header">
					<tr>
						<th width="5%">ID</th>
						<th width="10%">发送者</th>
						<th width="5%">角色</th>
						<th width="20%">内容</th>
						<th width="15%">编号</th>
						<th width="8%">类型</th> 
						<th width="8%">消息类型</th>	
						<th width="11%">创建时间</th>
						<th width="9%">接收人数</th>
						<th width="9%">已读人数</th>
						<th width="9%">未读人数</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$query1="select m.id,m.appid,m.content,m.type,m.msgtype,m.msgid,m.createtime from appusers u inner join appmsg m where u.isvalid=true and u.id=m.appid and u.customerid=".$customer_id;
					if($search_msg!=""){
						$query1=$query1." and m.content like '%".$search_msg."%'";	
					}
					if($msgtype!=-1){
						$query1=$query1." and m.msgtype=".$msgtype;
					}
					if($appid!=-1){
						$query1=$query1." and m.appid=".$appid;
					}
					$result_count = _mysql_query($query1) or die('Query failed: ' . mysql_error());
					$rcount_q2 = mysql_num_rows($result_count);   
					$query1=$query1.' limit '.$start.','.$end;
					$result = _mysql_query($query1) or die('Query failed1: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
						$msg_id = $row->id;
						$msg_appid = $row->appid;
						$msg_content = $row->content;
						$msg_type = $row->type;
						$msg_msgtype = $row->msgtype;
						$msg_msgid = $row->msgid;
						$msg_createtime = $row->createtime;
						
						$query2="select id,appid from appmsgr where msgid='".$msg_msgid."' and isvalid=true";
						$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());   
						$appids="";
						$pcount = 0;
						while ($row2 = mysql_fetch_object($result2)) {
							$t_appid = $row2->appid;
							if(empty($appids)){
								$appids = $t_appid;
							}else{
								$appids = $appids.",".$t_appid;
							}
							$pcount++;
						}
						$no_read_msg = 0;
						$read_msg = 0;
						if(!empty($user_ids)){
							$query2="select type,count(1) as ncount from appmsgr where isvalid=true  and type=0 and appid in (".$appids.")";
							$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());   
							while ($row2 = mysql_fetch_object($result2)) {  
								$no_read_msg = $row2->ncount;
								break;
							}
							$query2="select type,count(1) as ncount from appmsgr where isvalid=true and type=1 and appid in (".$appids.")";
							$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());   
							while ($row2 = mysql_fetch_object($result2)) {  
								$read_msg = $row2->ncount;
								break;
							} 
						}						
						?>
						<tr>
							<td><?php echo $msg_id; ?></td>  
							<td><?php echo $msg_appid; ?></td>
							<td><?php echo $msg_type; ?></td>
							<td><?php echo $msg_content; ?></td>
							<td><?php echo $msg_msgid; ?></td>
							<td><?php
								if($msg_type==1){
									echo "图文";
								}elseif($msg_type==2){
									echo "图片";
								}elseif($msg_type==3){
									echo "语音";
								}elseif($msg_type==4){
									echo "视频";
								}else{
									echo "文本";
								}?>
							</td>
							<td><?php 
								if($msg_msgtype==1){
									echo "个人消息";
								}else{
									echo "系统消息";
								}?>
							</td>
							<td><?php echo $msg_createtime; ?></td>
							<td align="center"><a href="read_person.php?card_id=<?php echo $shop_card_id; ?>&card_shop_id=<?php echo $shop_id; ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" ><?php echo $pcount; ?></a></td>
							<td align="center"><a href="read_person.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&search_paystatus=0&search_shop_id=<?php echo $shop_id; ?>"><?php echo $no_read_msg; ?></a></td>
							<td align="center"><a href="read_person.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&search_paystatus=1&search_shop_id=<?php echo $shop_id; ?>"><?php echo $read_msg; ?></a></td>
						</tr>					
					<?php }?>
				</tbody>
			</table>
        <!--表格结束-->
        <!--翻页开始-->
		
      <div class="WSY_page">

      </div>
  <!--翻页结束--></div> <!--门店列表结束-->
	</div>
	<div style="width:100%;height:20px;"></div>
</div>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript" background="#ffffff"> 
var pagenum = <?php echo $pagenum ?>;
var rcount_q2 = <?php echo $rcount_q2 ?>;
var end = <?php echo $end ?>;
var count =Math.ceil(rcount_q2/end);//总页数
var page = count;
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
		var search_type = document.getElementById("search_type").value; 
		var search_person = document.getElementById("search_person").value; 
		var search_msg = document.getElementById("search_msg").value; 
		document.location= "messenge.php?pagenum="+p+"&msgtype="+search_type+"&appid="+search_person+"&search_msg="+search_msg;
	}
});
function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());  
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		var search_type = document.getElementById("search_type").value; 
		var search_person = document.getElementById("search_person").value; 
		var search_msg = document.getElementById("search_msg").value; 
		document.location= "messenge.php?pagenum="+p+"&msgtype="+search_type+"&appid="+search_person+"&search_msg="+search_msg;
	}
  }
function searchForm(){
    	var search_type = document.getElementById("search_type").value; 
		var search_person = document.getElementById("search_person").value; 
		var search_msg = document.getElementById("search_msg").value; 
		document.location= "messenge.php?pagenum=1&msgtype="+search_type+"&appid="+search_person+"&search_msg="+search_msg;
}
</script>
</body>
</html>
