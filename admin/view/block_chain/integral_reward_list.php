<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>区块链积分奖励－奖金池</title>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
	<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>

	<style type="text/css">
		.form-btn{width:auto!important;padding:0 10px!important;cursor:pointer;color:#fff!important;border:0!important;}
		.form-add-btn{display:inline-block;line-height:24px;border-radius:3px;}
		.table-btn{color:#fff;border:0;cursor:pointer;border-radius:3px;height:24px;padding:0 10px;font-size:12px;}


		.div_item{float:left;padding:10px;font-size:14px;}
		.div_item label{margin-left:5px;font-size:14px;}
		.div_item input{border:1px solid #ccc; border-radius: 2px;}
		.layui-layer-content button{float: left;margin-top: 56px;margin-bottom: 19px;width: 80px;height: 30px;}
		.xubox_title{background: none!important;}
		.xubox_title em{left: 0!important;text-align: center!important;width: 100%!important;}
		.WSY_previous{width: 110px!important;}
		.temp_data{margin-left: 20px;padding-left:10px;width: 700px;height:30px;line-height: 30px;border-radius: 5px;border: 1px solid #ccc;font-size: 16px;}
		.temp_data li{float: left;margin-right: 10px;}
		.caozuo{height: 24px;border-radius: 3px;}
	</style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
	    <!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
				<?php $keyContent = '奖金池'; ?>
                <?php include_once('reward_header.php'); ?>
			</div>
		    <!--订单列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=block_chain&a=integral_reward_list">
						<input type="hidden" id="m" name="m" value="block_chain">
						<input type="hidden" id="a" name="a" value="integral_reward_list">
						<ul class="WSY_search_q">
							<li>年份：<input class="date_picker" type="text" name="year" id="year" onclick="WdatePicker({dateFmt:'yyyy'});" value="<?php echo $_GET['year']; ?>"></li>
							<li>月份：<input class="date_picker" type="text" name="month" id="month" onclick="WdatePicker({dateFmt:'MM'});" value="<?php echo $_GET['month']; ?>"></li>					
							<li><input type="submit" class="WSY-skin-bg form-btn search" value="搜索"></li>
							<li><input type="button" class="WSY-skin-bg form-btn" value="导出" onclick="export_excel()"></li>
						</ul> 
					</form>
					<ul class="temp_data">
						<li>
							时间：<?php echo date('Y-m-d'); ?>
						</li>
						<li>
							流通发行总量：<?php echo $user_block_chain!=NULL?$user_block_chain:0; ?>
						</li>
						<li>
							奖金池总额：￥<?php echo $total_bonus_money!=NULL?$total_bonus_money:0; ?>
						</li>
						<li>
							已发放总量：<?php echo number_format($total_exchange_jf!=NULL?$total_exchange_jf:0,4); ?>
						</li>
					</ul>
		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="6%" nowrap="nowrap" align="center">编号</th>
							<th width="8%" nowrap="nowrap" align="center">年月</th>
							<th width="7%" nowrap="nowrap" align="center">流通发行量</th>
							<th width="7%" nowrap="nowrap" align="center">奖金池</th>
							<th width="6%" nowrap="nowrap" align="center">价值</th>
							<th width="6%" nowrap="nowrap" align="center">已兑换零钱金额（元）</th>
							<th width="10%" nowrap="nowrap" align="center">操作</th>
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $row){ ?>
								<tr>
									<td style="text-align:center;"><?php echo $row['id'];?></td>
									<td style="text-align:center;"><?php echo $row['year_months'];?></td>
									<td style="text-align:center;"><?php echo $row['reward_money'];?></td>
									<td style="text-align:center;"><?php echo $row['total_money'];?></td>
									<td style="text-align:center;"><?php echo $row['value_money'];?></td>
									<td style="text-align:center;"><?php echo number_format($row['exchange_money'],2);?></td>
									<td style="text-align:center;">
										<button class="WSY-skin-bg form-btn caozuo" style="margin-right: 10px;" onclick="location.href='/mshop/admin/index.php?m=block_chain&a=integral_reward_activity&customer_id=<?php echo $customer_id_en;?>&bonus_id=<?php echo $row['id'];?>';">活动管理</button>
										<button class="WSY-skin-bg form-btn caozuo" onclick="location.href='/mshop/admin/index.php?m=block_chain&a=integral_reward_exchange_log&customer_id=<?php echo $customer_id_en;?>&bonus_id=<?php echo $row['id'];?>';">兑换日志</button>
									</td>
								</tr>
							<?php } ?>
						</tbody>
						
					</table>
				</div>
		        <!--翻页开始-->
		        <div class="WSY_page"></div>
		        <!--翻页结束-->
		    </div>
		</div>
		<!-- <div style="width:100%;height:20px;"></div> -->
	</div>
	<!--内容框架结束-->
</body>
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script>
<script type="text/javascript">

var year  = $("#year").val();//年份
	month = $("#month").val();//月份
	param = "";
if(year!=""){
	param += "&year="+year;
}
if(month!=""){
	param += "&month="+month;
}


//分页 start
var pagenum = <?php echo $pagenum ?>;//当前页
var count =<?php echo $pageCount ?>;//总页数	
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
	var url="/mshop/admin/index.php?m=block_chain&a=integral_reward_list&pagenum="+p+param;	
	location.href = url;
   }
});



function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=block_chain&a=integral_reward_list&pagenum="+a+param;

		location.href = url;
	}
}
//分页 end

//导出
function export_excel(){
	var url='/weixin/plat/app/index.php/Excel/block_chain_reward_excel/customer_id/'+'<?php echo $customer_id;?>';
		year  = $("#year").val();//年份
		month = $("#month").val();//月份
	if( year != ''){
		url += '/year/'+year;
	}
	if( month != '' ){
		url += '/month/'+month;
	}

	document.location = url;
}
</script>
</html>