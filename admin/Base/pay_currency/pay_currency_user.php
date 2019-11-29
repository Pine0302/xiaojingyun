<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=7;//头部文件0支付方式，1微信支付,2支付宝,3财务通,4通联支付
$currency_head = 2;



//分页---start
$pagenum = 1;
$pagesize = 20;
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * $pagesize;
$end = $pagesize;
//分页---end

//循环商家所有的粉丝
$query = "SELECT id,name,weixin_name FROM weixin_users WHERE isvalid=TRUE AND customer_id=".$customer_id;
$query1 = $query."  GROUP BY id DESC order by id desc limit ".$start.",".$end;

//假如带查询
$user_id = isset($_GET['promoter'])?$_GET['promoter']:'';
if(!empty($user_id)){
	$query1=$query." and id=".$user_id." order by createtime desc limit ".$start.",".$end;
	$query = $query." AND id=".$user_id;
}

//计算商家所有的粉丝量
$result = _mysql_query($query) or die('Query failed2: ' . mysql_error());
$rcount_q = mysql_num_rows($result);
$page=ceil($rcount_q/$end); 
 /* 输出数量结束 */

function cut_num($menber,$places){
	$places = $places+1;
	$num = substr(sprintf("%.".$places."f", $menber),0,-1); 
	return $num;	
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>充值记录</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/weixinpl/back_newshops/Common/js/layer/layer.js"></script>

<style>
/*.white1{background-color: #fff;
border-bottom: solid 2px #06a7e1;}*/
table th{color: #FFF;line-height: 30px;text-align: center;font-size: 12px; }
table td{height: 40px;line-height: 20px;font-size: 12px;color: #323232;padding: 0px 1em;text-align: center;border: 1px solid #D8D8D8; }
.display{display:none}
.count{
	width: 200px;
	height:30px;
	margin-left: 40px;
	margin-top: 40px;
	float: left;
}

.count span{
	font-size: 18px;
	color: #68af27;
	font-weight: bold;
}
</style>

</head>

<body id="bod" style="min-height: 580px;">
	<!--内容框架-->
	<div class="WSY_content" style="height: 100%;">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			
				<?php
			//include("../../../../weixinpl/back_newshops/Base/pay_currency/pay_head.php"); 
			// include("../../../../weixinpl/back_newshops/Base/pay_currency/currency_head.php");
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/pay_currency/currency_head.php");
			?> 
		
			<!--列表头部切换结束-->
<!--门店列表开始-->
  <div  class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list" style="margin-bottom:0px;">

	<form action="" >

      	<div style="margin-left:40px;margin-top:0px;">
      		<span>会员编号：</span>
      		<input type="text" name="promoter" id="promoter_num" value="<?php echo $user_id;?>" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)">
			<input type="submit" class="my_search" id="my_search" value="搜索">
			<ul class="WSY_righticon">
				<li style="margin-top: 20px;margin-right: 60px;"><a href="javascript:history.go(-1);">返回</a></li>
			</ul>
		</div>

	</form>
	<!-- <div class="count">商城累计消费：<span id="number"><?php echo $money?></span>  币</div>
	<div class="count">购物币累计充值：<span id="number" style="color:#c22439;font-size18px;font-weight:blod;"><?php echo $imoney?></span>  币</div> -->
             <br class="WSY_clearfloat";>
        </div> 
        <!--列表按钮开始-->
		
        <!--表格开始-->
		<div class="WSY_data" id="type1" style="margin-left: 1.5%;">
		
		<table class="WSY_t2"  width="95%"  style="border: 1px solid #D8D8D8;border-collapse: collapse;">
			<thead class="WSY_table_header">
				<tr style="border:none">
					<th width="4%">会员编号</th>
					<th width="6%">姓名(微信名)</th>		
					<th width="6%"><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>余额</th>
					<th width="6%">最后变动时间</th>
					<th width="6%">查看明细</th>
					<th width="8%">充值</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				$user_id     = -1;
				$user_name 	 = "";
				$weixin_name = "";
				
				$result = _mysql_query($query1) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$user_id 		= $row->id;
					$user_name 		= $row->name;
					$weixin_name 	= $row->weixin_name;
					$currency    =  0;
					$createtime = '<span style="color:#c22439;font-weight:blod;font-size:14px;">尚未充值</span>';
					$query_cu = "SELECT currency,createtime FROM weixin_commonshop_user_currency WHERE isvalid=true AND user_id=".$user_id." LIMIT 1";
					$result_cu= _mysql_query($query_cu) or die('Query failed2: ' . mysql_error());
					while( $row_cu = mysql_fetch_object($result_cu) ){
						$currency 	= $row_cu->currency;
						$createtime = $row_cu->createtime;
					}
					if($currency==NULL){
						$currency=0;
					}
					if($createtime==NULL){
						$createtime = '<span style="color:#c22439;font-weight:blod;font-size:14px;">尚未充值</span>';
					}

			?>
				<tr style="border:1px solid #D8D8D8">
					<td><a href="pay_currency_log.php?customer_id=<?php echo $customer_id_en;?>&promoter=<?php echo $user_id;?>" style="cursor:pointer;color:#06a7e1;"><?php echo $user_id;?></a></td>
					<td><?php echo $user_name;?>(<?php echo $weixin_name?>)</td>
					<td><?php echo cut_num($currency,2);?></td>
					<td><?php echo $createtime;?></td>
					<td><a href="pay_currency_log.php?customer_id=<?php echo $customer_id_en;?>&promoter=<?php echo $user_id;?>" style="cursor:pointer;color:#06a7e1;">点击查看明细</a></td>
					<td><a onclick="set_currency(<?php echo $user_id;?>)" style="cursor:pointer;color:#06a7e1;">充值</a></td>
				</tr>
			<?PHP }?> 
			
			</tbody>
			
			</table>
			
			<!--翻页开始-->
			<div class="WSY_page">
				
			</div>
			<!--翻页结束-->
		</div>
		<script src="../../../js/fenye/jquery.page1.js"></script>
		<script type="text/javascript">
		 var pagenum = <?php echo $pagenum ?>;
		  var count =<?php echo $page ?>;//总页数
			//pageCount：总页数
			//current：当前页
			var user_id = $("#promoter_num").val();
			var card_id = $("#card_member_id").val();


			
			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
				 document.location= "pay_currency_user.php?pagenum="+p;
			   }
			});

		  var page = <?php echo $page ?>;
		  
		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());
			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
			document.location= "pay_currency_user.php?pagenum="+a;
			}
		  }	
		</script>

	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">
<!--<script src="../../js/fenye/jquery.page.js"></script>-->
<script src="/wsy_pub/admin/static/js/sms_verification.js"></script>
<script>

function clearNoNum(obj)
{
//先把非数字的都替换掉，除了数字和.
obj.value = obj.value.replace(/[^\d]/g,"");
}

    //短信验证+充值按钮
    function set_currency(user_id){
        var param_arr = [user_id];
        sms_check("currency_recharge","sms_continue",param_arr);
        var log_content = "购物币充值短信验证";
        $.ajax({
            type: "post",
            url: "/wsy_pub/admin/index.php?m=security_sms&a=sys_log_insert",
            data: {'sys_calss': 'shop_system_currency_recharge','sys_content':log_content},
            dataType: "json",
            success: function (res) {
                console.log('succrss');
            },
            error: function (e) {
                console.log('操作日志插入失败')
            }
        });
    }

    function sms_continue(user_id){
        location.href="/weixinpl/back_newshops/Base/pay_currency/set_currency.php?customer_id=<?php echo $customer_id;?>&user_id="+user_id;
    }
</script>

<?php 

mysql_close($link);
?>

</body>
</html>
