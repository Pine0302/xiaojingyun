<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
$head=5; /*关于头部文件的定位*/
require('../../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");
require('../../../../../weixinpl/auth_user.php');

$exp_user_id=-1;
if(!empty($_GET["exp_user_id"])){
    $exp_user_id = $configutil->splash_new($_GET["exp_user_id"]);
}
$query ="select isOpenPublicWelfare from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $isOpenPublicWelfare = $row->isOpenPublicWelfare;
	}
$query = 'SELECT id,appid,appsecret,access_token FROM weixin_menus where isvalid=true and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
$access_token="";
while ($row = mysql_fetch_object($result)) {
	$key_id =  $row->id ;
	$appid =  $row->appid ;
	$appsecret = $row->appsecret;
	$access_token = $row->access_token;
	break;
}
//新增客户
$new_customer_count =0;
//今日销售
$today_totalprice=0;
//新增订单
$new_order_count =0;
//新增推广员
$new_qr_count =0;
$nowtime = time();
$year = date('Y',$nowtime);
$month = date('m',$nowtime);
$day = date('d',$nowtime);

$cur_date = date('Y-m-d');
$cur_date_begin = $cur_date." 00:00:00";
$cur_date_end = $cur_date." 23:59:59";

// $query="select count(distinct batchcode) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$query="select count(distinct batchcode) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_order_count = $row->new_order_count;
   break;
}
// $query="select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=".$customer_id." and year(paytime)=".$year." and month(paytime)=".$month." and day(paytime)=".$day;
// 
$query="select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=".$customer_id." and paytime>='".$cur_date_begin."' and paytime<='".$cur_date_end."'";

$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $today_totalprice = $row->today_totalprice;
   break;
}
$today_totalprice = round($today_totalprice,2);
// $query="select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$query="select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=".$customer_id." and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_customer_count = $row->new_customer_count;
   break;
}
// $query="select count(1) as new_qr_count from promoters where isvalid=true and status=1 and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$query="select count(1) as new_qr_count from promoters where isvalid=true and status=1 and customer_id=".$customer_id." and createtime<='".$cur_date_begin."' and createtime>='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_qr_count = $row->new_qr_count;
   break;
}
$search_user_id=-1;
if(!empty($_GET["search_user_id"])){
   $search_user_id = $configutil->splash_new($_GET["search_user_id"]);
}
$search_name="";
if(!empty($_GET["search_name"])){
    $search_name = $configutil->splash_new($_GET["search_name"]);
}
if(!empty($_POST["search_name"])){
    $search_name = $configutil->splash_new($_POST["search_name"]);
}
$search_phone="";
if(!empty($_GET["search_phone"])){
    $search_phone = $configutil->splash_new($_GET["search_phone"]);
}
if(!empty($_POST["search_phone"])){
    $search_phone = $configutil->splash_new($_POST["search_phone"]) ;
}
$search_name_type=1;	//1为搜索微信名称 2为搜索收货名称
if(!empty($_GET["search_name_type"])){		
    $search_name_type = $configutil->splash_new($_GET["search_name_type"]);
}
if(!empty($_POST["search_name_type"])){
    $search_name_type = $configutil->splash_new($_POST["search_name_type"]);
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
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/m-style.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link href="../../../../back_commonshop/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/style.css" rel="stylesheet" type="text/css">
<link href="../../../../back_commonshop/css/operamasks-ui.css" rel="stylesheet" type="text/css"> 
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/icon.css" media="all">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../../../back_commonshop/js/global.js"></script>
</head>
<body>

<style type="text/css">body, html{background:url(images/main-bg.jpg) left top fixed no-repeat;}</style>


       <!--列表内容大框开始-->
	<div class="WSY_columnbox">
		<!--列表头部切换开始-->
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/personalization/basic_head.php");
		?>	
		<div class="WSY_data" id="home">		
        <!--列表头部切换结束-->
		   <?php
		   $sql_stock = "select stock_remind from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
		   $res_stock = _mysql_query($sql_stock) or die('Query failed: ' . mysql_error());
		   while ($row_sql_stock = mysql_fetch_object($res_stock)) {
				$stock_remind = $row_sql_stock->stock_remind;
			}
  		    $stock_mun=0;
			$stock_pidarr="";
			$query_stock1="select id from weixin_commonshop_products where isvalid=true and storenum<".$stock_remind." and isout=0 and customer_id=".$customer_id;
			$result_stock1 = _mysql_query($query_stock1) or die('Query failed: ' . mysql_error());
			$stock_mun1 = mysql_num_rows($result_stock1);
			while ($row_stock1 = mysql_fetch_object($result_stock1)) {
				$stock_pid1 = $row_stock1->id;
				if(!empty($stock_pidarr)){
					$stock_pidarr=$stock_pidarr."_".$stock_pid1;
				}else{
					$stock_pidarr=$stock_pid1;
				}
			}
			$query_stock2="select id,propertyids,storenum from weixin_commonshop_products where isvalid=true and isout=0 and storenum>".$stock_remind." and customer_id=".$customer_id;
			$result_stock2 = _mysql_query($query_stock2) or die('Query failed: ' . mysql_error());
			$stock_mun2=0;
			while ($row_stock2 = mysql_fetch_object($result_stock2)) {
				$stock_pid = $row_stock2->id;			
				$stock_storenum = $row_stock2->storenum;			
				$stock_propertyids = $row_stock2->propertyids;			
				if(!empty($stock_propertyids)){
					$query_stock3="SELECT * FROM weixin_commonshop_product_prices WHERE storenum<".$stock_remind." and product_id='".$stock_pid."' limit 0,1";
				   //echo  $query_stock3;
					$result_stock3 = _mysql_query($query_stock3) or die('Query failed: ' . 	mysql_error());
					$result_stock3_mun1 = mysql_num_rows($result_stock3);
					while ($row_stock3 = mysql_fetch_object($result_stock3)) {
						$stock_pid2 = $row_stock3->product_id;
					}
					if($result_stock3_mun1 !=0){
						$stock_mun2=$stock_mun2 + 1;
						if(!empty($stock_pidarr)){
							$stock_pidarr=$stock_pidarr."_".$stock_pid2;
						}else{
							$stock_pidarr=$stock_pid2;
						}
					}				   
				}
			}
			$stock_mun=$stock_mun1+$stock_mun2; 
		   ?>
		</div>
	<script type="text/javascript">
		var skin_index_init=function(){
			$('#shop_skin_index .menu .nav a.category').click(function(){
				if($('#category').height()>$(window).height()){
					$('html, body, #cover_layer').css({
						height:$('#category').height(),
						width:$(window).width(),
						overflow:'hidden'
					});
				}else{
					$('#category, #cover_layer').css('height', $(window).height());
					$('html, body').css({
						height:$(window).height(),
						overflow:'hidden'
					});
				}
				$('#cover_layer').show();
				$('#category').animate({left:'0%'}, 500);
				$('#shop_page_contents').animate({margin:'0 -70% 0 70%'}, 500);
				window.scrollTo(0);
				return false;
			});
		}
</script>	
<div id="iframe_page">
	<div class="iframe_content">
		<!--<link href="../../../../css/shop.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../../../js/shop.js"></script>
		<link href="css/operamasks-ui.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="js/operamasks-ui.min.js"></script>-->
		<script type="text/javascript" src="../../../../js/tis.js"></script>
              <div class="WSY_list" id="order" >
			    <div class="WSY_left" ><a></a>
                    
					</div>
			<ul class="WSY_righticon">
				<li>
					<a href="add_distributor_article.php?customer_id=<?php echo $customer_id_en; ?>">添加文章</a>
				</li>
			</ul>
			<br class="WSY_clearfloat">
			<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
				<thead  class="WSY_table_header">
						<th width="8%" nowrap="nowrap">id</th>
						<th width="30%" nowrap="nowrap">文章名称</th>	
						<th width="20%" nowrap="nowrap">链接产品</th>
						<th width="34%" nowrap="nowrap">地址</th>					
						<th width="8%" nowrap="nowrap">操作</th>							
				</thead>
				<tbody>
				<?php
					$query="select * from weixin_commonshop_distributor_article where customer_id=$customer_id and isvalid=1";
					$re=_mysql_query($query);
					while($row=mysql_fetch_object($re)){
						$id=$row->id;
						$title=$row->title;
						$p_id=$row->p_id;
						
						$query2="select name from weixin_commonshop_products where id=$p_id";
						$re2=_mysql_query($query2);
						while($row2=mysql_fetch_object($re2)){
						$p_name=$row2->name;
						break;
						}
						?>
					<tr>
					   <td><?php echo $id ?></td>
					   <td><?php echo $title ?></td>
					   <td><?php echo $p_name ?></td>
					   <td style="word-wrap: break-word;"><?php echo Protocol."$_SERVER[HTTP_HOST]/weixinpl/common_shop/jiushop/mb_distributor_article.php?customer_id=".$customer_id_en."&article_id=$id" ?></td>
					   <td>
					   <p><a  style="cursor:pointer" href="add_distributor_article.php?key_id=<?php echo $id ?>">编辑</a></p>					
					   <p><a  style="cursor:pointer" onclick="del_article(event)" href="del_distributor_article.php?key_id=<?php echo $id ?>">删除</a></p>		
					   </td>
					</tr>	
					<?php	
					}			
				?>				
				</tbody>
			</table>
			<div class="blank20"></div>
			<div id="turn_page"></div>
		</div>	
	</div>
</div>
</div>
<?php 
mysql_close($link);
?>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/fenye/fenye.css" media="all">
<script>
function del_article(e){
	if(!confirm("您确定要删除此文章？")){
		e.preventDefault();
		return false;
	}
}
</script>
</body></html>