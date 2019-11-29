<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=2;

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

//假如带查询
$user_id = isset($_GET['promoter'])?$_GET['promoter']:'';
$search_name = addslashes(isset($_GET['search_name'])?$_GET['search_name']:'');

if(!empty($user_id) || !empty($search_name)){
	if( !empty($user_id) ){
		$query = $query." AND id=".$user_id;
	}
	if( !empty($search_name) ){
		if( $search_name == '%' ){
			$query = $query." AND (name like '%!%%' escape '!' or weixin_name like '%!%%' escape '!')";
		}else{
			$query = $query." AND (name like '%".$search_name."%' or weixin_name like '%".$search_name."%')";
		}
		
	}
	$query1 = $query." order by createtime desc limit ".$start.",".$end;	
}else{
	$query1 = $query." order by id desc limit ".$start.",".$end;
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
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="/weixinpl/css/inside.css" media="all">
<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/weixinpl/common/utility.js"></script>
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
			//include("../../../../weixinpl/back_newshops/Base/moneybag/pay_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/moneybag/basic_head.php");
			?> 
		
			<!--列表头部切换结束-->
<!--门店列表开始-->
  <div  class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list" style="margin-bottom:0px;">

	<form action="" >

      	<div style="margin-left:40px;margin-top:0px;">
      		<span>推广员编号：</span>
      		<input type="text" name="promoter" id="promoter_num" value="<?php echo $user_id;?>" onkeyup="clearNoNum(this,1);" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;" oninput="this.value=this.value.replace(/[^0-9]+/g,'')">
			<span>姓名：</span>
      		<input type="text" name="search_name" id="search_name" value="<?php echo $search_name;?>" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;">
			<input type="submit" class="my_search" id="my_search" value="搜索">
		</div>

	</form>

             <br class="WSY_clearfloat";>
        </div> 
        <!--列表按钮开始-->
		
        <!--表格开始-->
		<div class="WSY_data" id="type1" style="margin-left: 1.5%;">
		
		<table class="WSY_t2"  width="95%"  style="border: 1px solid #D8D8D8;border-collapse: collapse;">
			<thead class="WSY_table_header">
				<tr style="border:none">
					<th width="4%" >会员编号</th>
					<th width="6%">姓名(微信名)</th>		
					<th width="6%">零钱余额</th>
					<th width="6%">最后变动时间</th>
					<th width="6%">明细</th>
					<th width="8%">充值</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			    $user_id 		= -1;
				$user_name 		= "";
				$weixin_name	= "";
				
				
				$result = _mysql_query($query1) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$user_id 		= $row->id;
					$user_name 		= $row->name;
					$weixin_name 	= $row->weixin_name;
					$user_name 		= $user_name."(".$weixin_name.")";
					/* 查询个人零钱总额 */
					$balance 		=  0;
					$createtime 	= '<span style="color:#c22439;font-weight:blod;font-size:14px;">尚未初始化</span>';
					$query_cu = "SELECT createtime,balance FROM moneybag_t WHERE isvalid=TRUE AND customer_id=".$customer_id." and user_id=".$user_id." limit 0,1";
					$result_cu = _mysql_query($query_cu) or die('Query failed: ' . mysql_error());
					while ($row_cu = mysql_fetch_object($result_cu)) {
						$createtime 	= $row_cu->createtime;
						$balance 	 	= round($row_cu->balance,2);
					}
					

			?>
				<tr style="border:1px solid #D8D8D8">
					<td><?php echo $user_id;?></td>
					<td><?php echo $user_name;?></td>
					<td><?php echo $balance;?></td>
					<td><?php echo $createtime;?></td>
					<td><a href="user_detail.php?customer_id=<?php echo $customer_id;?>&user_id=<?php echo $user_id;?>"style="cursor:pointer;color:#06a7e1;">查看钱包明细</a></td>
					<td><a onclick="set_balance(<?php echo $user_id;?>)" style="cursor:pointer;color:#06a7e1;">充值</a></td>
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
			var search_name = $("#search_name").val();
			var card_id = $("#card_member_id").val();
			
			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
					var url = "shop_set.php?pagenum="+p;
					if(user_id != ''){
						url += "&user_id="+user_id;
					}
					if(search_name != ''){
						url += "&search_name="+search_name;
					}
					 document.location= url;
			   }
			});

		  var page = <?php echo $page ?>;
		  
		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());
			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
				var url = "shop_set.php?pagenum="+a;
				if(user_id != ''){
					url += "&user_id="+user_id;
				}
				if(search_name != ''){
					url += "&search_name="+search_name;
				}
				document.location= url;
			}
		  }	
		  function reg(obj){
			 obj.value = obj.value.replace(/\'/,"");
		  }
		</script>

	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">
<!--<script src="../../js/fenye/jquery.page.js"></script>-->
<script src="/wsy_pub/admin/static/js/sms_verification.js"></script>
<script>
    function set_balance(user_id){
        //判断是否开启短信验证
        var param_arr = [user_id];
        sms_check("moneybag_recharge","sms_continue",param_arr);
        //插入操作日志
        var log_content = "零钱充值短信验证";
        $.ajax({
            type: "post",
            url: "/wsy_pub/admin/index.php?m=security_sms&a=sys_log_insert",
            data: {'sys_calss': 'shop_system_moneybag_background_recharge','sys_content':log_content},
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
        location.href="/mshop/admin/Base/moneybag/set_balance.php?customer_id=<?php echo $customer_id;?>&user_id="+user_id;
    }

</script>

<?php 

mysql_close($link);
?>

</body>
</html>
