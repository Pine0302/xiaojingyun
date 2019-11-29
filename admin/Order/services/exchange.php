<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/auth_user.php');
require('../../../../weixinpl/proxy_info.php');
$head=1;//头部文件

_mysql_query("SET NAMES UTF8");

$op="";
if(!empty($_GET["op"])){
   $op=$configutil->splash_new($_GET["op"]);
   $keyid=$configutil->splash_new($_GET["keyid"]);
   if($op=="del"){
	   $sql="update weixin_commonshop_product_evaluations set isvalid=false where id=".$keyid;
	   _mysql_query($sql);
   }else if($op=="status"){
       $status= $configutil->splash_new($_GET["status"]);
	   $sql="update weixin_commonshop_product_evaluations set status=".$status." where id=".$keyid;
	   _mysql_query($sql);
   }
}
$new_baseurl = BaseURL."back_commonshop/";

$pid=-1;
if(!empty($_GET["pid"])){
   $pid=$configutil->splash_new($_GET["pid"]);
}

$search_status = -1;
$search_level = -1;
if($_GET["search_status"]!=""){
	$search_status = $configutil->splash_new($_GET["search_status"]);	 
}
if($_GET["search_level"]!=""){
	$search_level = $configutil->splash_new($_GET["search_level"]);	 
}
$keyword = "";
if(!empty($_GET["keyword"])){
   $keyword = $configutil->splash_new($_GET["keyword"]);
}

$is_distribution=0;//渠道取消代理商功能
//代理模式,分销商城的功能项是 266
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scdl' and c.id=cf.column_id";
$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_distribution=1;
}
$is_supplierstr=0;//渠道取消供应商功能
//供应商模式,渠道开通与不开通
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scgys' and c.id=cf.column_id";
$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_supplierstr=1;
}
		
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>产品管理－售后管理</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/contentblue.css"><!--内容CSS配色·蓝色-->
<link rel="stylesheet" type="text/css" href="../../Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/jscolor.js"></script><!--拾色器js-->
</head>

<body>
<!--内容框架开始-->
<div class="WSY_content" id="WSY_content_height">

       <!--列表内容大框开始-->
	<div class="WSY_columnbox">	
		<?php
			include("../../../../weixinpl/back_newshops/Order/services/head.php"); 
		?>
    <!--产品管理代码开始-->
		<div class="WSY_data">
			<div class="WSY_agentsbox">
				<form class="search" style="display:block" method="get" action="services.php?customer_id=<?php echo $customer_id_en; ?>">
					<div class="WSY_search_q">
						<li>换货申请列表&nbsp&nbsp&nbsp&nbsp&nbsp <input type="text" name="keyword" value="请输入买家手机号或者订单号/退货编号进行搜索" class="form_input" size="15" onfocus="if(this.value==defaultValue) {this.value='<?php echo $keyword; ?>';this.type='text'}" onblur="if(!value) {value=defaultValue; this.type='text';} " style="width:265px; color:#ccc"></li>	
						<li class="WSY_bottonliss"><input type="submit" class="search_btn" value="查询"></li>
						
						<li>时间筛选：
							<span class="om-calendar om-widget om-state-default">
								<input type="text" class="input" id="begintime" name="AccTime_S" value="" maxlength="20" id="K_1389249066532">
								<span class="om-calendar-trigger"></span></span>-<span class="om-calendar om-widget om-state-default">
								<input type="text" class="input" id="endtime" name="AccTime_E" value="2015-11-22 10:56" maxlength="20" id="K_1389249066580">
								<span class="om-calendar-trigger"></span>
							</span>
						</li>
						
						<li class="WSY_bottonliss aright" ><input type="button" style="width:100px" id="mul_unsale" value="批量删除"></li>
					</div>
				</form> 

				<table width="97%" class="WSY_table" id="WSY_t1">
				  <thead class="WSY_table_header">
						<th width="3%"><input id="ck_all"  type="checkbox"></th>
						<th width="5%" nowrap="nowrap"align="center">ID</th>
						<th width="7%" nowrap="nowrap"align="center">商品</th>
						<th width="10%" nowrap="nowrap"align="center">申请退货金额</th>
						<th width="12%" nowrap="nowrap"align="center">买家信息</th>
						<th width="6%" nowrap="nowrap"align="center">退货状态</th>
						<th width="10%" nowrap="nowrap"align="center" class="last">操作</th>
				  </thead>
				  
				  <?php
					$pagenum = 1;

					if(!empty($_GET["pagenum"])){
					   $pagenum = $configutil->splash_new($_GET["pagenum"]);
					}

					$start = ($pagenum-1) * 20;
					$end = 20;  
					
					
					$query_page = 'SELECT count(1) as wcount FROM weixin_commonshop_product_evaluations where isvalid=true and customer_id = '.$customer_id;
					$query2="select id,user_id,status,discuss,level,createtime,discussimg,type,batchcode,product_id,reply_id from weixin_commonshop_product_evaluations where isvalid=true and customer_id=". $customer_id;
					/* 关键字搜索 */
					$query_cond = "";
					if(!empty($keyword)){
						$query_cond .= " and discuss like '%".$keyword."%'";
					}
					if($search_status >= 0){
						$query_cond .= " and status = ".$search_status;
					}
					if($search_level >= 0){
						$query_cond .= " and level =".$search_level;
					}
					if($pid > 0){
						$query_cond .= ' and product_id='.$pid;
					}
					$query_page = $query_page.$query_cond;
					$result_page = _mysql_query($query_page) or die('Query_page failed: ' . mysql_error());
					$wcount =0;
					$page=0;
					while ($row_page = mysql_fetch_object($result_page)) {
						$wcount =  $row_page->wcount ;
					}			
					$page=ceil($wcount/$end);	
					
					$query2 = $query2.$query_cond; 
					$query2=$query2." order by batchcode desc,id asc limit ".$start.",".$end;
					$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
					
					while ($row2 = mysql_fetch_object($result2)) {
					   $d_id = $row2->id;
					   $user_id = $row2->user_id;
					   $level = $row2->level; 
					   $discuss = $row2->discuss;
					   $createtime = $row2->createtime;
					   $status = $row2->status;
					   $discussimg = $row2->discussimg;
					   $type = $row2->type;
					   $batchcode = $row2->batchcode;
					   $product_id = $row2->product_id;
					   $reply_id = $row2->reply_id;
					   $img_array = explode(",", $discussimg); 
					   $statusname="无效";
					   $typename="商家回复";
					   switch($type){ 
						  case 1:
							  $typename="用户评论";
							 break;
						  case 2:
							 $typename="追加评论";
					   }		   
					   if($status==1){
						  $statusname="有效";
					   }

						$levelname="";
						switch($level){
							case 1:
								$levelname="好评";
								break;
							case 2: 
								$levelname="中评";
								break;
							case 3:
								$levelname="差评";
								break;
					   } 
					   //产品 名称
					   $query_pn="select name from weixin_commonshop_products where customer_id=".$customer_id." and id=".$product_id." limit 0,1";
					   $result_pn = _mysql_query($query_pn) or die('Query_pn failed: ' . mysql_error());
					   $porductName="";
					   while ($row_pn = mysql_fetch_object($result_pn)) {
						  $porductName=$row_pn -> name;
					   }		   
					   //产品 名称End
					   //购买用户 名称
					   $query3="select name,weixin_headimgurl,weixin_name from weixin_users where isvalid=true and id=".$user_id." limit 0,1";
					   $result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
					   $username="";
					   $headimgurl="";
					   while ($row3 = mysql_fetch_object($result3)) {
						  $username=$row3->name;
						  $headimgurl = $row3->weixin_headimgurl;
						  $weixin_name=$row3->weixin_name;
						  break;
					   }
					   if(empty($username)){
						  $username = $weixin_name;
					   }
					   //购买用户 名称End 
					?>
				  
					<tr id="WSY_q1" >
						<td><input type="checkbox" name="pro_ids" value="<?php echo $p_id; ?>"></td>
						<td align="center"><?php echo $d_id; ?></td> 
						<td align="center"><?php echo $username; ?></td>
						<td align="center"><?php echo $batchcode; ?></td>
						<td align="center"><?php echo $porductName; ?></td>
						<td align="center"><?php echo $levelname; ?></td>
						
						<td><a href="addcardshop.php?keyid=<?php echo $shop_id ?>&card_id=<?php echo $shop_card_id ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" style="cursor:pointer;" title="编辑">
								<img src="../../../common/images_V6.0/operating_icon/icon05.png" style="height:20px;width:20px"></a>
							<a href="exchange.php?shop_id=<?php echo $shop_id ?>&op=del&card_id=<?php echo $shop_card_id; ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" onclick="if(!confirm(&#39;删除后不可恢复，继续吗？&#39;)){return false};" title="删除">
								<img src="../../../common/images_V6.0/operating_icon/icon04.png" style="height:20px;width:20px"></a></td>					
					</tr>
					<?php } ?>
				</table>
			</div>
			<!--翻页开始-->
			<div class="WSY_page">
				
			</div>
			<!--翻页结束-->
		</div>
    <!--产品管理代码结束-->
	</div>

	<div style="width:100%;height:20px;"></div>
</div>
<?php 

mysql_close($link);
?>
<!--内容框架结束-->
<script src="../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript">
pagenum = '<?php echo $pagenum; ?>';
rcount_q = <?php echo $wcount;?>; 
end = <?php echo $end ?>;
count =Math.ceil(rcount_q/end);//总页数
  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
			var search_status = $("#search_status").val();
			var search_level = $("#search_level").val();
		 document.location= "discuss.php?pagenum="+p+"&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pid=<?php echo $pid; ?>&search_status="+search_status+"&search_level="+search_level;
	   }
    });

  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val()); 
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		var search_status = $("#search_status").val();
		var search_level = $("#search_level").val();
	document.location= "discuss.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pid=<?php echo $pid; ?>&pagenum="+a+"&search_status="+search_status+"&search_level="+search_level;
	}
  }
  
</script>	

</body>
</html>