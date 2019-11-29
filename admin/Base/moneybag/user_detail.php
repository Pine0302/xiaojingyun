<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');


//分页---start
$pagenum = 1;
$pagesize = 20;
$user_id = $configutil->splash_new($_GET["user_id"]);
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * $pagesize;
$end = $pagesize;
//分页---end

$query = "SELECT id,money,after_money,type,batchcode,pay_style,remark,createtime,operation_user FROM moneybag_log WHERE isvalid=true AND user_id = $user_id";
$query1 = $query." order by createtime desc,id desc limit ".$start.",".$end;
//echo $query;



$result = _mysql_query($query) or die('Query failed2: ' . mysql_error());
$rcount_q = mysql_num_rows($result);
$page=ceil($rcount_q/$end); 
 /* 输出数量结束 */

//是否子账号
$is_auth_user = "no";
if ( !empty($_SESSION['is_auth_user']) ){
	$is_auth_user = $_SESSION['is_auth_user'];
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
			$head=0;
			
			// $detail_type = 1;//头部导航判断是否显示个人零钱明细
			
			include("../../../../weixinpl/back_newshops/Base/moneybag/user_head.php");
			?> 
		
			<!--列表头部切换结束-->
<!--门店列表开始-->
  <div  class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list" style="margin-bottom:0px;">
			<ul class="WSY_righticon">
				<li style="margin: 10px 60px 20px 0;"><a href="javascript:history.go(-1);">返回</a></li>
			</ul>
	<!-- <form action="" >

      	<div style="margin-left:40px;margin-top:0px;">
      		<span>推广员编号：</span>
      		<input type="text" name="promoter" id="promoter_num" value="<?php echo $user_id;?>" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;">
			<input type="submit" class="my_search" id="my_search" value="搜索">
		</div>

	</form> -->
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
					<th width="4%" >ID</th>
					<th width="6%">进出金额</th>		
					<th width="4%">进账/出账</th>
                    <th width="6%">变动后金额</th>
					<th width="6%">订单号</th>
					<th width="4%">资金动向</th>
					<th width="6%">时间</th>
					<th width="8%">备注</th>
				<?php
					if ( $is_auth_user == 'no' ){	//非子账号显示操作人
				?>
					<th width="5%">操作人</th>
				<?php
					}
				?>
				</tr>
			</thead>
			<tbody>
			<?php 
				$result = _mysql_query($query1) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$id 			= $row->id;
					$money 			= $row->money;
					$type 			= $row->type;
                    $after_money 			= $row->after_money;
					if( $type == 0 ){
						$type = '<span style="color:#c22439;font-weight:blod;font-size:14px;">进账</span>';
					}elseif( $type == 1 ){
						$type = '<span style="color:#68af27;font-weight:blod;font-size:14px;">支出</span>';
					}
					$batchcode 		= $row->batchcode;
					$pay_style 		= $row->pay_style;
					switch ($pay_style) {
						case '0':
							$pay_style = "商城消费";
							break;
						case '1':
							$pay_style = "返佣";
							break;
						case '2':
							$pay_style = "消费返现";
							break;
						case '3':
							$pay_style = "大礼包";
							break;
						case '4':
							$pay_style = "商家后台充值";
							break;
						case '5':
							$pay_style = "提现";
							break;
						case '6':
							$pay_style = "绩效奖励";
							break;
						case '7':
							$pay_style = "会员奖金";
							break;

                        case '10':
                            $pay_style = "收银O2O";
                            break;
                        case '13':
                            $pay_style = "零钱支付手续费";
                            break;
                        case '17':
                            $pay_style = "订货系统分仓派单结算";
                            break;
                        case '18':
                            $pay_style = "订货系统推荐奖励";
                            break;
                        case '19':
                            $pay_style = "订货系统提成奖励";
                            break;
                        case '21':
                            $pay_style = "订货系统充值扣款";
                            break;
                        case '22':
                            $pay_style = "订货系统充值退款";
                            break;
                        case '23':
                            $pay_style = "订货系统充值返点 ";
                            break;
                        case '24':
                            $pay_style = "订货系统返点奖励";
                            break;
                        case '25':
                            $pay_style = "订货系统进货转上级";
                            break;
                        case '27':
                            $pay_style = "订货系统货款充值";
                            break;
                        case '28':
                            $pay_style = "订货系统进货";
                            break;
                        case '32':
                            $pay_style = "订货系统零钱转货款";
                            break;
                        case '33':
                            $pay_style = "订货系统充值返差";
                            break;
                        case '34':
                            $pay_style = "订货系统伯乐平级奖";
                            break;
                        case '35':
                            $pay_style = "订货系统伯乐越级奖";
                            break;
                        case '40':
                            $pay_style = "订货系统货款平级奖励";
                            break;
                        case '41':
                            $pay_style = "订货系统货款越级奖励";
                            break;
                        case '48':
                            $pay_style = "音王ktv打赏";
                            break;
						default:
							# code...
							break;
					}
					$remark 	 	= str_replace("<br />","",$row->remark);
					$createtime 	= $row->createtime;
					$operation_user = $row->operation_user;
					if ( empty($operation_user) ){
						$operation_user = '商家平台';
					}

			?>
				<tr style="border:1px solid #D8D8D8">
					<td><?php echo $id;?></td>
					<td><?php echo $money;?></td>
					<td><?php echo $type;?></td>
                    <td><?php echo $after_money;?></td>
					<td><?php echo $batchcode;?></td>
					<td><?php echo $pay_style;?></td>
					<td><?php echo $createtime;?></td>
					<td><?php echo $remark;?></td>
				<?php
					if ( $is_auth_user == 'no' ){	//非子账号显示操作人
				?>
					<td><?php echo $operation_user;?></td>
				<?php
					}
				?>
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
			var user_id = "<?php echo $user_id;?>";



			
			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
				 document.location= "user_detail.php?pagenum="+p+"&user_id="+user_id;
			   }
			});

		  var page = <?php echo $page ?>;
		  
		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());
			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
			document.location= "user_detail.php?pagenum="+a+"&user_id="+user_id;
			}
		  }	
		</script>

	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">
<!--<script src="../../js/fenye/jquery.page.js"></script>-->
<script>


</script>

<?php 

mysql_close($link);
?>

</body>
</html>
