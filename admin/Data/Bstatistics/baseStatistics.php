<?php 
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
require('../../../../weixinpl/back_init.php');
_mysql_query("SET NAMES UTF8");
/*$customer_id = $configutil->splash_new($_GET["customer_id"]);
$customer_id = passport_decrypt($customer_id); */ //引入文件中已获取
$name =$_SESSION['username'];
if(!empty($_SESSION['curr_login'])){
$name =$_SESSION['curr_login'];
$query="SELECT name from weixin_commonshops where isvalid=true and customer_id =".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$shopname=$row->name;
	break;
}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>数据统计</title>
<link href="../../Common/css/Data/css/statistics.css" rel="stylesheet" type="text/css">	
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script src="../../Common/js/Data/js/echarts/echarts.js"></script>
<script type="text/javascript" src="../../Common/js/Data/js/ichartjs/ichart.1.2.min.js"></script>
<script type="text/javascript" src="../../Common/js/Data/js/baseStatistics.js"></script>
<style>
.ds{display:none}
.display{display:none}
#mtype2 .statistics_ul03 dt a{background:url(../../Common/images/Data/qushiicon/qushi_icon_21.png) no-repeat left center;}
#mtype3 .statistics_ul03 dt a{background:url(../../Common/images/Data/qushiicon/qushi_icon_10.png) no-repeat left center;}
#mtype4 .statistics_ul03 dt a{background:url(../../Common/images/Data/qushiicon/qushi_icon_05.png) no-repeat left center;}
#mtype5 .statistics_ul03 dt a{background:url(../../Common/images/Data/qushiicon/qushi_icon_17.png) no-repeat left center;}
#mtype6 .statistics_ul03 dt a{background:url(../../Common/images/Data/qushiicon/qushi_icon_03.png) no-repeat left center;}
#mtype7 .statistics_ul03 dt a{background:url(../../Common/images/Data/qushiicon/qushi_icon_14.png) no-repeat left center;}


@keyframes myfirst
{
0%   {background:red; left:200px; top:-100px;}

100% {background:red; left:0px; top:0px;}
}

@-moz-keyframes myfirst /* Firefox */
{
0%   {background:red; left:200px; top:-100px;}

100% {background:red; left:0px; top:0px;}
}

@-webkit-keyframes myfirst /* Safari and Chrome */
{
0%   {background:red; left:200px; top:-100px;}

100% {background:red; left:0px; top:0px;}
}


</style>
</head>

<body>
<div class="WSY_columnbox">
	<div class="WSY_column_header">
    	<div class="WSY_columnnav">
          	<!--<a class="CH white1" onclick="choose(this)">基础数据统计</a>-->
			<a class="CH first" onclick="choose(this)">销售统计</a>
			<a class="CH" onclick="choose(this)">各产品销售统计</a>
			<a class="CH" onclick="choose(this)">推广员统计</a>
			<a class="CH" onclick="choose(this)">粉丝统计</a>
			<a class="CH" onclick="choose(this)">单日销售统计</a>
			<!-- <a class="CH" onclick="choose(this)">投放标签销售统计</a> -->
        </div>
    </div>
	<!--描点开始-->
	<div id="MD" style="float:left;position:fixed;top: 15px;right: 49px;width:10%;background-color: #EFEFEF;padding-bottom:10px;border-radius:2px;border:solid 1px #e4e4e4;">
		<ul>
			<li style="margin-top:10px">
			<input  type="button" class="search_btn" onclick="tracingPointon(this)"  value="搜索"  style="width:80%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
			</li>
			<li style="margin-top:10px">
			<a href="#mtype2">
			<input  type="button" class="search_btn tracingPoint"  value="销售情况"  style="width:80%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
			</a>
			</li>
			<li style="margin-top:10px">
			<a href="#mtype3">
			<input  type="button" class="search_btn tracingPoint"  value="订单统计"  style="width:80%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
			</a>
			</li>
			</li>
			<li style="margin-top:10px">
			<a href="#mtype4">
			<input  type="button" class="search_btn tracingPoint"  value="产品销售情况"  style="width:80%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
			</a>
			</li>
			<li style="margin-top:10px">
			<a href="#mtype5">
			<input  type="button" class="search_btn tracingPoint"  value="推广员统计"  style="width:80%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
			</a>
			</li>
			<li style="margin-top:10px">
			<a href="#mtype6">
			<input  type="button" class="search_btn tracingPoint"  value="产品销售统计"  style="width:80%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
			</a>
			</li>
			<li style="margin-top:10px">
			<a href="#mtype7">
			<input  type="button" class="search_btn tracingPoint"  value="单日报表"  style="width:80%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >
			</a>
			</li>
			<!-- <li style="margin-top:10px">
			<a href="#mtype8">
			<input  type="button" class="search_btn tracingPoint"  value="标签销售额"  style="width:80%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >
			</a>
			</li>
			<li style="margin-top:10px">
			<a href="#mtype9">
			<input  type="button" class="search_btn tracingPoint"  value="标签订单量,占比"  style="width:80%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >
			</a>
			</li> -->
			<li style="margin-top:10px">
			<input  type="button" class="search_btn tracingPoint"  onclick="tracingPointoff(this)" value="收拢"  style="width:80%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
			</li>
		</ul>
	</div>
	<script>
	function tracingPointoff(obj){
		$(".tracingPoint").addClass("ds");
	}
	function tracingPointon(obj){
		$(".tracingPoint").removeClass("ds");
	}
	$(function(){
		$(".CH").removeClass("white1");
		$(".first").addClass("white1");
		$("#MD").addClass("ds");
		$(".statistics_div02").addClass("ds");
		$("#mtype2").removeClass("ds");
		$("#mtype3").removeClass("ds");
	});
	function choose(obj){
		var Cname=$(obj).html();
		if(Cname=="基础数据统计"){
			$(".CH").removeClass("white1");
			$("#MD").removeClass("ds");
			$(obj).addClass("white1");
			$(".statistics_div02").removeClass("ds");
		}
		if(Cname=="销售统计"){
			$(".CH").removeClass("white1");
			$(obj).addClass("white1");
			$("#MD").addClass("ds");
			$(".statistics_div02").addClass("ds");
			$("#mtype2").removeClass("ds");
			$("#mtype3").removeClass("ds");
		}
		if(Cname=="各产品销售统计"){
			$(".CH").removeClass("white1");
			$(obj).addClass("white1");
			$("#MD").addClass("ds");
			$(".statistics_div02").addClass("ds");
			$("#mtype4").removeClass("ds");
		}
		if(Cname=="推广员统计"){
			$(".CH").removeClass("white1");
			$(obj).addClass("white1");
			$("#MD").addClass("ds");
			$(".statistics_div02").addClass("ds");
			$("#mtype5").removeClass("ds");
		}
		if(Cname=="粉丝统计"){
			$(".CH").removeClass("white1");
			$(obj).addClass("white1");
			$("#MD").addClass("ds");
			$(".statistics_div02").addClass("ds");
			$("#mtype6").removeClass("ds");
		}
		if(Cname=="单日销售统计"){
			$(".CH").removeClass("white1");
			$(obj).addClass("white1");
			$("#MD").addClass("ds");
			$(".statistics_div02").addClass("ds");
			$("#mtype7").removeClass("ds");
		}
		// if(Cname=="投放标签销售统计"){
		// 	$(".CH").removeClass("white1");
		// 	$(obj).addClass("white1");
		// 	$("#MD").addClass("ds");
		// 	$(".statistics_div02").addClass("ds");
		// 	$("#mtype8").removeClass("ds");
		// 	$("#mtype9").removeClass("ds");
		// 	total_order_num();
		// 	container_charts();
		// 	yes_total_order_num();
		// 	rejection_total_order_num();
		// 	yes_rejection_total_order_num();
		// 	rejection_charts();
		// 	consumption_cake_charts();
		// 	rejection_cake_charts();
		// }
	}
	</script>
	<!--描点关闭-->
	

	
    <div class="statisticsbox">
		
		<!--销售统计（日期）开始统计-->
		<div  id="mtype2" class="statistics_div02"  style="min-width:1180px">
            <dl class="statistics_ul03">
                <dt><a>销售统计</a></dt>
<!--==================================================(开始设置)===================================================-->
	
				
			<div class="WSY_search_q" style="display:block;" >
               <li style="width:24%;">
				时间：&nbsp;
				<a>
				<input type="text" class="Wdate" style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});"  id="TSbegintime" name="AccTime_S" value="<?php echo $begintime;		 ?>" >
				</a>-<a>
				<input type="text" class="Wdate" readonly="readonly"  style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="TSendtime" name="AccTime_E" value="<?php echo $endtime; ?>" >
				</a>
				</li>
				<li style="width:16%;">
				邮费类型：&nbsp;
				<a>
				<select id="SYF" style="width: 50%;border: 1px solid #CFCBCB;height: 28px;margin-bottom: 5px;border-radius: 3px;">
				<option value="1">--默认--</option>
				<option value="8">包含邮费</option>
				<option value="1">不包含邮费</option>
				</select>
				</a>
				</li>
				<li style="width:16%;">
				时间类型：&nbsp;
				<a>
				<select id="STYPE" style="width: 50%;border: 1px solid #CFCBCB;height: 28px;margin-bottom: 5px;border-radius: 3px;">
				<option value="4">--默认--</option>
				<option value="4">按星期</option>
				<option value="1">按月份</option>
				<option value="2">按季度</option>
				<option value="3">按年份</option>
				
				
				</select>
				</a>
				</li>
				<li style="width:16%;"  class="WSY_bottonliss">
				<input id="search_bar" type="button"  onclick="search_SQ(this)" value="搜 索"  style="width:40%;border-radius: 3px;height:25px;color:#fff;cursor: pointer;">	
				<input  type="button"  onclick="search_SQ(this)" value="刷新数据"  style="width:40%;margin-left: 9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer;" >
				</li>			
          </div>
				<div id="TTsale">
		 
				</div>	
				<div style="margin-top:20px;margin-left: 100px">
				<div id='canvasDiv' style="float:left;width:800px;height:400px"></div>
				
				<div style="float:left;width:20%" class="WSY_bottonliss">
					<ul>
					<li style="margin-top:10px"  class="WSY_bottonliss"><input  type="button" class="search_btn" onclick="search_SQ(this)" value="转曲线图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					<li style="margin-top:10px"  class="WSY_bottonliss"><input  type="button" class="search_btn" onclick="search_SQ(this)" value="转柱形图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					<li style="margin-top:10px"  class="WSY_bottonliss"><input  type="button" class="search_btn" onclick="search_SQ(this)" value="列表查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					<li style="margin-top:10px"  class="WSY_bottonliss"><input  type="button" class="search_btn TS" onclick="search_SQ(this)" value="详细查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
				<!--	<li style="margin-top:10px"  class="WSY_bottonliss"><input  type="button" class="search_btn" onclick="search_SQ(this)" value="导出订单"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					<li style="margin-top:10px"  class="WSY_bottonliss"><input  type="button" class="search_btn" onClick="search_SQ(this)" value="导出飞豆"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>-->
					</ul>
				</div>
				<div style="clear: both;height:20px"></div>	  
				</div>   
<!--=============================================(设置结束)=============================================-->
            </dl>
        </div>
		<!--销售统计（日期）结束统计-->
		
		
		
		
		
		<!--订单统计开始统计-->
		<div  id="mtype3" class="statistics_div02"  style="min-width:1180px">
            <dl class="statistics_ul03">
                <dt><a>订单统计</a></dt>
 <!--==================================================(开始设置)===================================================-->
	
				
			<div class="WSY_search_q" style="display:block;" >
               <li style="width:24%;">
				时间：&nbsp;
				<a>
				<input type="text" class="Wdate" style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});"  id="TObegintime" name="AccTime_S" value="<?php echo $begintime;	?>" >
				</a>-<a>
				<input type="text" class="Wdate" readonly="readonly"  style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="TOendtime" name="AccTime_E" value="<?php echo $endtime; ?>" >
				</a>
				</li>
				<li style="width:16%;"> 
				订单状态：
				<select name="search_status" id="status_O" style="width:60%;border: 1px solid #CFCBCB;height: 26px;border-radius: 3px;">
					<option value="-1">--请选择--</option>
					<option value="1" <?php if($search_status==1){ ?>selected <?php } ?>>已确认</option>
					<option value="2" <?php if($search_status==2){ ?>selected <?php } ?>>待确认</option>
					<option value="3" <?php if($search_status==3){ ?>selected <?php } ?>>已支付</option>
					<option value="4" <?php if($search_status==4){ ?>selected <?php } ?>>未支付</option>
					<option value="5" <?php if($search_status==5){ ?>selected <?php } ?>>已发货</option>
					<option value="6" <?php if($search_status==6){ ?>selected <?php } ?>>未发货</option>
					<!--<option value="7" <?php if($search_status==7){ ?>selected <?php } ?>>申请退货</option>-->
					<option value="8" <?php if($search_status==8){ ?>selected <?php } ?>>已取消</option>			
				</select>
				</li>
				<li style="width:16%;">
				时间类型：&nbsp;
				<a>
				<select id="OSTYPE" style="width: 50%;border: 1px solid #CFCBCB;height: 28px;margin-bottom: 5px;border-radius: 3px;">
				<option value="1">--默认--</option>
				<option value="4">按星期</option>
				<option value="1">按月份</option>
				<option value="2">按季度</option>
				<option value="3">按年份</option>
				
				
				</select>
				</a>
				</li>
				<li style="width:16%;" class="WSY_bottonliss">
				<input id="Osearch_bar" type="button"  onclick="search_SO(this)" value="搜 索"  style="width:40%;border-radius: 3px;height:25px;color:#fff;cursor: pointer;" >	
				<input  type="button"  onclick="search_SO(this)" value="刷新数据"  style="width:40%;margin-left: 9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >
				</li>			
          </div>
				<div id="TOsale">
		 
				</div>	
				<div style="margin-top:20px;margin-left: 100px">
				<div id='OcanvasDiv' style="float:left;width:800px;height:400px"></div>
				
				<div style="float:left;width:20%" class="WSY_bottonliss">
					<ul>
					<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SO(this)" value="转曲线图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SO(this)" value="转柱形图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SO(this)" value="列表查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					<li style="margin-top:10px"><input  type="button" class="search_btn TO" onclick="search_SO(this)" value="详细查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SO(this)" value="导出订单"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					<li style="margin-top:10px"><input  type="button" class="search_btn" onClick="search_SO(this)" value="导出飞豆"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					</ul>
				</div>
				<div style="clear: both;height:20px"></div>	  
				</div>   
<!--=============================================(设置结束)=============================================-->
            </dl>
        </div>
		<!--订单统计结束统计-->
		
		
		
		
		
		<!--商品销售统计开始统计-->
		<div  id="mtype4" class="statistics_div02"  style="min-width:1180px">
            <dl class="statistics_ul03">
                <dt><a>产品销售统计</a></dt>
		 <!--==================================================(开始设置)===================================================-->
		
					
				<div class="WSY_search_q" style="display:block;" >
				   <li style="width:24%;">
					时间：&nbsp;
					<a>
					<input type="text" class="Wdate" style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});"  id="TPbegintime" name="AccTime_S" value="<?php echo $begintime;		 ?>" >
					</a>-<a>
					<input type="text" class="Wdate" readonly="readonly"  style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="TPendtime" name="AccTime_E" value="<?php echo $endtime; ?>" >
					</a>
					</li>
					<li style="width:16%;"> 
					订单状态：
					<select name="search_status" id="status_P" style="width:60%;border: 1px solid #CFCBCB;height: 26px;border-radius: 3px;">
						<option value="-1">--请选择--</option>
						<option value="1" <?php if($search_status==1){ ?>selected <?php } ?>>已确认</option>
						<option value="2" <?php if($search_status==2){ ?>selected <?php } ?>>待确认</option>
						<option value="3" <?php if($search_status==3){ ?>selected <?php } ?>>已支付</option>
						<option value="4" <?php if($search_status==4){ ?>selected <?php } ?>>未支付</option>
						<option value="5" <?php if($search_status==5){ ?>selected <?php } ?>>已发货</option>
						<option value="6" <?php if($search_status==6){ ?>selected <?php } ?>>未发货</option>
						<!--<option value="7" <?php if($search_status==7){ ?>selected <?php } ?>>申请退货</option>-->
						<option value="8" <?php if($search_status==8){ ?>selected <?php } ?>>已取消</option>			
					</select>
					</li>
					<li style="width:16%;">
					时间类型：&nbsp;
					<a>
					<select id="PSTYPE" style="width: 50%;border: 1px solid #CFCBCB;height: 28px;margin-bottom: 5px;border-radius: 3px;">
					<option value="1">--默认--</option>
					<option value="4">按星期</option>
					<option value="1">按月份</option>
					<option value="2">按季度</option>
					<option value="3">按年份</option>
					
					
					</select>
					</a>
					</li>
					<li style="width:16%;" class="WSY_bottonliss">
					<input  type="button"  onclick="search_SP(this)" value="搜 索"  style="width:40%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
					<input  type="button"  onclick="search_SP(this)" value="刷新数据"  style="width:40%;margin-left: 9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >
					</li>			
			  </div>
					<div id="TPsale">
			 
					</div>	
					<div style="margin-top:20px;margin-left: 100px">
					<div id='PcanvasDiv' style="float:left;width:800px;height:400px"></div>
					
					<div style="float:left;width:20%" class="WSY_bottonliss">
						<ul>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SP(this)" value="转曲线图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SP(this)" value="转柱形图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SP(this)" value="转销售图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SP(this)" value="转数量图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SP(this)" value="列表查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SP(this)" value="详细查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SP(this)" value="导出订单"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer;  " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onClick="search_SP(this)" value="导出飞豆"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						</ul>
					</div>
					<div style="clear: both;height:20px"></div>	  
					</div>   
	<!--=============================================(设置结束)=============================================-->
            </dl>
        </div>
		<!--商品销售统计结束统计-->
		
		
		
		
		
		
		<!--推广员统计开始统计-->
		<div  id="mtype5" class="statistics_div02"  style="min-width:1180px">
            <dl class="statistics_ul03">
                <dt><a>推广员统计</a></dt>
 		 <!--==================================================(开始设置)===================================================-->
		
					
				<div class="WSY_search_q" style="display:block;" >
				   <li style="width:24%;">
					时间：&nbsp;
					<a>
					<input type="text" class="Wdate" style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});"  id="TGbegintime" name="AccTime_S" value="<?php echo $begintime;		 ?>" >
					</a>-<a>
					<input type="text" class="Wdate" readonly="readonly"  style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="TGendtime" name="AccTime_E" value="<?php echo $endtime; ?>" >
					</a>
					</li>
				
					<li style="width:16%;">
					时间类型：&nbsp;
					<a>
					<select id="GTYPE" style="width: 50%;border: 1px solid #CFCBCB;height: 28px;margin-bottom: 5px;border-radius: 3px;">
					<option value="4">--默认--</option>
					<option value="4">按星期</option>
					<option value="1">按月份</option>
					<option value="2">按季度</option>
					<option value="3">按年份</option>
					
					
					</select>
					</a>
					</li>
					<li style="width:16%;" class="WSY_bottonliss">
					<input  type="button"  onclick="search_SG(this)" value="搜 索"  style="width:40%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
					<input  type="button"  onclick="search_SG(this)" value="刷新数据"  style="width:40%;margin-left: 9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >
					</li>			
			  </div>
					<div id="TGsale">
			 
					</div>	
					<div style="margin-top:20px;margin-left: 100px">
					<div id='GcanvasDiv' style="float:left;width:800px;height:400px"></div>
					
					<div style="float:left;width:20%" class="WSY_bottonliss">
						<ul>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SG(this)" value="转曲线图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SG(this)" value="转柱形图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					<!--	<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SG(this)" value="列表查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SG(this)" value="详细查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>-->
						</ul>
					</div>
					<div style="clear: both;height:20px"></div>	  
					</div>   
	<!--=============================================(设置结束)=============================================-->

            </dl>
        </div>
		<!--推广员统计结束统计-->
		
		
		
		
		
		<!--粉丝统计开始统计-->
		<div  id="mtype6" class="statistics_div02"  style="min-width:1180px">
            <dl class="statistics_ul03">
                <dt><a>粉丝统计</a></dt>
		 <!--==================================================(开始设置)===================================================-->
		
					
				<div class="WSY_search_q" style="display:block;" >
				   <li style="width:24%;">
					时间：&nbsp;
					<a>
					<input type="text" class="Wdate" style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});"  id="TFbegintime" name="AccTime_S" value="<?php echo $begintime;		 ?>" >
					</a>-<a>
					<input type="text" class="Wdate" readonly="readonly"  style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="TFendtime" name="AccTime_E" value="<?php echo $endtime; ?>" >
					</a>
					</li>
					<li style="width:16%;">
					时间类型：&nbsp;
					<a>
					<select id="FSTYPE" style="width: 50%;border: 1px solid #CFCBCB;height: 28px;margin-bottom: 5px;border-radius: 3px;">
					<option value="4">--默认--</option>
					<option value="4">按星期</option>
					<option value="1">按月份</option>
					<option value="2">按季度</option>
					<option value="3">按年份</option>
					
					
					</select>
					</a>
					</li>
					<li style="width:16%;" class="WSY_bottonliss">
					<input  type="button"  onclick="search_SF(this)" value="搜 索"  style="width:40%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
					<input  type="button"  onclick="search_SF(this)" value="刷新数据"  style="width:40%;margin-left: 9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >
					</li>			
			  </div>
					<div id="TFsale">
			 
					</div>	
					<div style="margin-top:20px;margin-left: 100px">
					<div id='FcanvasDiv' style="float:left;width:800px;height:400px"></div>
					
					<div style="float:left;width:20%" class="WSY_bottonliss">
						<ul>
						<!--<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SF(this)" value="转曲线图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SF(this)" value="转柱形图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SF(this)" value="列表查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<!--<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SF(this)" value="详细查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>-->
						</ul>
					</div>
					<div style="clear: both;height:20px"></div>	  
					</div>   
	<!--=============================================(设置结束)=============================================-->

            </dl>
        </div>
		<!--粉丝统计结束统计-->
		
		
		
		
		<!--单日报表开始统计-->
		<div id="mtype7" class="statistics_div02" style="min-width:1180px">
            <dl class="statistics_ul03">
                <dt><a>单日报表</a></dt>
		 <!--==================================================(开始设置)===================================================-->
		
					
				<div class="WSY_search_q" style="display:block;" >
				   <li style="width:24%;">
					时间：&nbsp;
					<a>
					<input type="text" class="Wdate" style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});"  id="TDbegintime" name="AccTime_S" value="<?php echo $begintime;		 ?>" >
					</a>-<a>
					<input type="text" class="Wdate" readonly="readonly"  style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="TDendtime" name="AccTime_E" value="<?php echo $endtime; ?>" >
					</a>
					</li>
					<li style="width:25%;">
					搜索类型：&nbsp;
					<a>
					<select id="search_SOtype" style="width: 50%;border: 1px solid #CFCBCB;height: 28px;margin-bottom: 5px;border-radius: 3px;">
					<option value="1">--默认--</option>
					<option value="1">产品销售-订单统计</option>
					<option value="2">推广员-粉丝统计</option>

	
					
					
					</select>
					</a>
					</li>
					
					<li style="width:16%;" class="WSY_bottonliss">
					<input  type="button"  onclick="search_SD(this)" value="搜 索"  style="width:40%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
					<input  type="button"  onclick="search_SD(this)" value="刷新数据"  style="width:40%;margin-left: 9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >
					</li>			
			  </div>
					<div id="TDsale">
			 
					</div>	
					<div style="margin-top:20px;margin-left: 100px">
					<div id='DcanvasDiv' style="float:left;width:800px;height:400px"></div>
					
					<div style="float:left;width:20%" class="WSY_bottonliss uli" >
						<ul>
					<!--	<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SD(this)" value="转曲线图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SD(this)" value="转柱形图"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>-->
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SD(this)" value="列表查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
					<!--<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SD(this)" value="详细查看"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_SD(this)" value="导出订单"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onClick="search_SD(this)" value="导出飞豆"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>-->
						</ul>
					</div>
					<div style="clear: both;height:20px"></div>	  
					</div>   
	<!--=============================================(设置结束)=============================================-->

            </dl>
        </div>
		<!--单日报表结束统计-->
		
		






        
		
		
    </div>
</div>
<input type=hidden id="customer_id" value="<?php echo $customer_id_en?>">
<input type=hidden id="NPScustomer_id" value="<?php echo $customer_id ?>">
<input type=hidden id="PScustomer_id" value="<?php echo passport_decrypt($customer_id)?>">
<input type=hidden id="shopname" value="<?php echo $shopname ?>">
<input type="hidden" id="currency-unit" value="<?php echo OOF_T ?>">

</body>
</html>
