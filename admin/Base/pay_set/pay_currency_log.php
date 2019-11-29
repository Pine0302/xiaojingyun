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
$currency_head = 1;


$endtime 	= $_GET['endtime'];
$begintime 	= $_GET['begintime'];
if($endtime){
	$where = "and c.createtime < '{$endtime}'";
}
if($begintime){
	$where .= "and c.createtime > '{$begintime}'";
}


//分页---start
$pagenum = 1;
$pagesize = 20;
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * $pagesize;
$end = $pagesize;
//分页---end

$query = "SELECT c.id,c.user_id,u.name,u.weixin_name,c.cost_currency,c.after_currency,c.batchcode,c.type,c.class,c.remark,c.createtime FROM weixin_users u RIGHT JOIN weixin_commonshop_currency_log c ON u.id=c.user_id WHERE c.status=1 and c.isvalid=true {$where} and c.customer_id=".$customer_id;
// $res = _mysql_query($query);
$query1 = $query." order by createtime desc limit ".$start.",".$end;


$user_id = isset($_GET['promoter'])?$_GET['promoter']:'';

if(!empty($user_id)){
	$query1=$query." and user_id=".$user_id." order by createtime desc limit ".$start.",".$end;
	$query = $query." AND user_id=".$user_id;
}

$result = _mysql_query($query) or die('Query failed2: ' . mysql_error());
$rcount_q = mysql_num_rows($result);
$page=ceil($rcount_q/$end); 
 /* 输出数量结束 */
//出账:
//$query = "SELECT sum(cost_currency) as total_money FROM weixin_commonshop_currency_log WHERE status=1 and class=1  and customer_id=".$customer_id;
$query = "SELECT sum(cost_currency) as total_money FROM weixin_commonshop_currency_log WHERE status=1 and type=0  and customer_id=".$customer_id;
$result = _mysql_query($query);
while($row=mysql_fetch_object($result)){
	$money = round($row->total_money,2);
}
//入账:
//$query = "SELECT sum(cost_currency) as total_money FROM weixin_commonshop_currency_log WHERE status=1 and (class=0 or class=3) and customer_id=".$customer_id;
$query = "SELECT sum(cost_currency) as total_money FROM weixin_commonshop_currency_log WHERE status=1 and type=1  and customer_id=".$customer_id;
$result = _mysql_query($query);
while($row=mysql_fetch_object($result)){
	$imoney = round($row->total_money,2);
}
$imoney = number_format($imoney, 2,'.','');
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
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<style>
/*.white1{background-color: #fff;
border-bottom: solid 2px #06a7e1;}*/
table th{color: #FFF;line-height: 30px;text-align: center;font-size: 12px; }
table td{height: 40px;line-height: 20px;font-size: 12px;color: #323232;padding: 0px 1em;text-align: center;border: 1px solid #D8D8D8; }
.display{display:none}
.count{
	_width: 200px;
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

<style>
    /*<!-- 导出字段 -->*/
    .floatbox{position: fixed;top: 270px;left: 40%;padding: 15px;background-color: #dddddd;display: none;}
    .floatbox .tishitext{margin-bottom: 4px;}
    .floatbox .checkboxsdiv{border: 1px solid #888888;padding: 8px;width: 200px;background-color: #ffffff;}
    .checkboxsdiv input,.quanbuxuan input{display: inline-block;}
    .checkboxsdiv p,.quanbuxuan p{display: inline-block;white-space: nowrap;overflow: hidden;max-width: 181px;margin-left: 5px;}
    .floatbox .floatinputs{width: 60px;height: 27px;border-radius: 6px;background-color: #2eade8;cursor: pointer;color: #ffffff;display: inline-block;margin-top: 15px;margin-left: 16px;margin-right: 10px;}
    .floatbox .floatinputc{width: 60px;height: 27px;color: #ffffff;background-color: #aaaaaa;cursor: pointer;border-radius: 6px;display: inline-block;margin-top: 15px;}
    .quanbuxuan{display: inline-block;padding: 5px 0 0 10px;vertical-align: middle;margin-top: 15px;}
    .subdivb{display: inline-block;vertical-align: middle;}
    /*<!-- 导出字段 End -->*/
</style>

</head>

<body id="bod" style="min-height: 580px;">
	<!--内容框架-->
	<div class="WSY_content" style="height: 100%;">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			
				<?php
			//include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php"); 
			include("../../../../weixinpl/back_newshops/Base/pay_set/currency_head.php");
			?> 
		
			<!--列表头部切换结束-->
<!--门店列表开始-->
  <div  class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list" style="margin-bottom:0px;">

	<form action="" >

      	<div style="margin-left:40px;margin-top:0px;">
      		<div class="WSY_position1" style="float:left">
      			<ul>		
      				<li class="WSY_position_date tate001"  style='margin-right: 20px'>
      					<p>时间：<input class="date_picker" type="text" name="begintime" id="begintime" value="<?php echo $begintime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'#F{$dp.$D(\'endtime\')}'});"></p>
      					<p style="margin-left:0px;">&nbsp;&nbsp;-&nbsp;&nbsp;<input class="date_picker" type="text" name="endtime" id="endtime" value="<?php echo $endtime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'begintime\')}'});"></p>
      				</li>				
      			</ul>
      		</div>
      		<span>会员编号：</span>
      		<input type="text" name="promoter" id="promoter_num" value="<?php echo $user_id;?>" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;"  autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)"> 
			<input type="submit" class="my_search" id="my_search" value="提交">
			<input type="button" class="my_search" id="my_excel" style='background: #f0ad4e' value="导出数据">
			<ul class="WSY_righticon">
				<li style="margin-top: 20px;margin-right: 60px;"><a href="javascript:history.go(-1);">返回</a></li>
			</ul>
		</div>

	</form>
	<div class="count">累计出账：<span id="number"><?php echo $money?></span>  币</div>
	<div class="count">累计入账：<span id="number" style="color:#c22439;font-size18px;font-weight:blod;width:350px;"><?php echo $imoney?></span>  币</div>
             <br class="WSY_clearfloat";>
        </div> 
        <!--列表按钮开始-->
		
        <!--表格开始-->
		<div class="WSY_data" id="type1" style="margin-left: 1.5%;">
		
		<table class="WSY_t2"  width="95%"  style="border: 1px solid #D8D8D8;border-collapse: collapse;">
			<thead class="WSY_table_header">
				<tr style="border:none">
					<th width="2%" >ID</th>
					<th width="4%" >会员编号</th>
					<th width="6%">姓名</th>
					<th width="3%">充值/消费</th>
					<th width="4%">变动金额</th>			
					<th width="4%">变动后余额</th>
					<th width="5%">订单编号</th>
					<th width="8%">时间</th>
					<th width="8%">备注</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				$result = _mysql_query($query1) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$id 			= $row->id;
					$c_user_id 		= $row->user_id;
					$user_name 		= $row->name;
					$weixin_name 	= $row->weixin_name;
					$createtime 	= $row->createtime;
					$cost_currency  = $row->cost_currency;
					$after_currency = $row->after_currency;
					$batchcode 		= $row->batchcode;
					$type 			= $row->type;
					if($type==1){
						$pay = '<span style="color:#c22439;font-weight:blod;font-size:14px;">进账</span>';
					}elseif($type==0){
						$pay = '<span style="color:#68af27;font-weight:blod;font-size:14px;" font-size>出账</span>';
					}
					$class   		= $row->class;
					$remark 		= str_replace("<br />","",$row->remark);

			?>
				<tr style="border:1px solid #D8D8D8">
					<td><?php echo $id;?></td>
					<td><a href="pay_currency_log.php?promoter=<?php echo $c_user_id;?>" style="color:#06a7e1;"><?php echo $c_user_id;?></a></td>
					<td><?php echo $user_name;?>（<?php echo $weixin_name;?>）</td>
					<td><?php echo $pay;?></td>
					<td><?php echo cut_num($cost_currency,2);?></td>
					<td><?php echo cut_num($after_currency,2);?></td>
					<td><?php echo $batchcode;?></td>
					<td><?php echo $createtime;?></td>
					<td><?php echo $remark;?></td>
				</tr>
			<?PHP }?> 
			
			</tbody>
			
			</table>
			
			<!--翻页开始-->
			<div class="WSY_page">
				
			</div>
			<!--翻页结束-->
		</div>
		<div class="floatbox">
		    <p class="tishitext">导出字段选择</p>
		    <div class="checkboxsdiv">
		        <div><input type="checkbox" checked name="excel_field" value="ID"><p>ID</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="会员编号"><p>会员编号</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="姓名"><p>姓名</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="微信名"><p>微信名</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="充值|消费"><p>充值/消费</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="变动金额"><p>变动金额</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="变动后余额"><p>变动后余额</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="订单编号"><p>订单编号</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="时间"><p>时间</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="备注"><p>备注</p></div>
		    </div>
		    <div class="quanbuxuan">
		    	<input type="checkbox" id="allselects" checked="checked" value="全选"><p>全选</p>
		    </div>
		    <div class="subdivb">
		    	<input type="submit" class="floatinputs" value="确定">
		    	<input type="submit" class="floatinputc" value="取消">
		    </div>
		</div> 
		<script src="../../../js/fenye/jquery.page1.js"></script>
		<script type="text/javascript">
		 var pagenum = <?php echo $pagenum ?>;
		  var count =<?php echo $page ?>;//总页数
			//pageCount：总页数
			//current：当前页
			var user_id = "<?php echo $user_id;?>";
			var card_id = $("#card_member_id").val();
            var begintime = "<?php echo $begintime;?>";
            var endtime   = "<?php echo $endtime;?>"; 

			
			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
				 document.location= "pay_currency_log.php?pagenum="+p+"&begintime="+begintime+"&endtime="+endtime+"&promoter="+user_id;
			   }
			});

		  var page = <?php echo $page ?>;
		  
		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());
			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
			document.location= "pay_currency_log.php?pagenum="+a+"&begintime="+begintime+"&endtime="+endtime+"&promoter="+user_id;
			}
		  }	
		</script>

	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">
<!--<script src="../../js/fenye/jquery.page.js"></script>-->
<script>
	// 显示导出界面
	$("#my_excel").click(function(){
	    $(".floatbox").toggle();
	});

	// 导出取消
	$(".floatinputc").click(function(){
	    $(".floatbox").hide();
	}); 

	// 全选
	$("#allselects").click(function(){    
	    if(this.checked){    
	        $(".checkboxsdiv :checkbox").attr("checked", true);   
	    }else{    
	        $(".checkboxsdiv :checkbox").attr("checked", false); 
	    }    
	});

	// 导出操作
	$(".floatinputs").click(function(){
		var promoter_num = $('#promoter_num').val()
		var customer_id = <?php echo $customer_id ?>;
		var begintime = $('#begintime').val();
		var endtime = $('#endtime').val();
		
		if(!begintime)	begintime = 0;
		if(!endtime)	endtime = 0;
		var text = [];
		$('input[name="excel_field"]:checked').each(function(){ 
			text.push($(this).val()); 
		}); 
		var url='/weixin/plat/app/index.php/Excel/currency_excel/text/'+text+'/customer_id/'+customer_id+'/begintime/'+begintime+'/endtime/'+endtime+'/promoter_num/'+promoter_num;
		window.location = url
	    $(".floatbox").hide();
	});

 //   var money = <?php echo $money?>;
 //   magic_number(money);
	// function magic_number(value) { 
	//   var num = $("#number"); 
	//   num.animate({count: value}, { 
	//     duration: 1000, 
	//     step: function() { 
	//       num.text(String(parseInt(this.count))); 
	//     } 
	//   }); 
	// };





	// function update() { 
	//   $.getJSON("currency_data.php?jsonp=?", function(data) { 
	//     magic_number(data.n); 
	//   }); 
	// }; 
	// update(); 

function clearNoNum(obj)
{
//先把非数字的都替换掉，除了数字和.
obj.value = obj.value.replace(/[^\d]/g,"");
}
</script>

<?php 

mysql_close($link);
?>

</body>
</html>
