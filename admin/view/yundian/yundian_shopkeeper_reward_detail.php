<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>云店奖励－店主收入明细</title>
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

		/*<!-- 导出字段 -->*/
		.floatbox{position: absolute;top: 270px;left: 40%;padding: 15px;background-color: #dddddd;display: none;}
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
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
	    <!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
                <div class="WSY_columnnav">
					<?php if($param['pay_style'] != 0) {?>
					<a class="white1 WSY-skin-bg" style="color: white;" />云店店主自营产品收入明细</a>
					<a class="white1" href="/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_reward_detail&user_id=<?php echo($param['user_id']);?>&pay_style=0" />云店店主提成收益明细</a>
					<?php } else {?>
					<a class="white1" href="/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_reward_detail&user_id=<?php echo($param['user_id']);?>&pay_style=1" />云店店主自营产品收入明细</a>
					<a class="white1 WSY-skin-bg" style="color: white;" />云店店主提成收益明细</a>
					<?php }?>    	
                    
                </div>   				
			</div>
		    <!--店主列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_reward_detail">
						<input type="hidden" id="m" name="m" value="yundian">
						<input type="hidden" id="a" name="a" value="yundian_shopkeeper_reward_detail">
						<ul class="WSY_search_q">
							<li>用户ID：<input type="text" name="from_id" id="from_id" value="<?php if($param['from_id']!=-1){echo $param['from_id'];}?>" class="form_input" oninput="num(this);"/></li>
							<li>用户昵称：<input type="text" name="name" id="name" value="<?php if($param['name']!=-1){echo $param['name'];}?>" class="form_input" /></li>
							<li>订单号：<input type="text" name="batchcode" id="batchcode" value="<?php if($param['batchcode']!=-1){echo $param['batchcode'];}?>" class="form_input" oninput="num(this);"/></li>
							<li>查询时间范围：
								<input class="form_input" type="text" id="start_time" name="start_time" value="<?php if($param['start_time']!=-1){echo $param['start_time'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px" />
								至：
								<input class="form_input" type="text" id="end_time" name="end_time" value="<?php if($param['end_time']!=-1){echo $param['end_time'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px"/>
							</li>

							<li><input type="submit" class="WSY-skin-bg form-btn"  value="搜索" ></li>
						</ul> 
						<input type="hidden" name="user_id" value="<?php echo($param['user_id']);?>">
						<input type="hidden" name="pay_style" value="<?php echo($param['pay_style']);?>">
					</form>

		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="10%" nowrap="nowrap"align="center">序号</th>
							<th width="10%" nowrap="nowrap"align="center">用户昵称</th>
							<th width="10%" nowrap="nowrap"align="center">用户ID</th>
							<th width="10%" nowrap="nowrap"align="center">手机号码</th>
							<th width="10%" nowrap="nowrap"align="center">订单号</th>
							<th width="10%" nowrap="nowrap"align="center">订单金额</th>
							<?php if($param['pay_style'] == 1) { ?>
							<th width="10%" nowrap="nowrap"align="center">产品数量</th>
							<th width="10%" nowrap="nowrap"align="center">平台抽成</th>
							<th width="10%" nowrap="nowrap"align="center">店主收入</th>
							<?php } else { ?>
							<th width="10%" nowrap="nowrap"align="center">总返利金额</th>
							<th width="10%" nowrap="nowrap"align="center">店主提成金额</th>
							<?php }?>
							<th width="10%" nowrap="nowrap"align="center">订单状态</th>
							
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $key => $row) {
							if($row['sendstatus'] == 2) {
								$order_status = '已结算';
							} else {
								$order_status = '未结算';
							} ?>
								<tr>
									<td style="text-align:center;"><?php echo $row['id']?></td>
									<td style="text-align:center;"><?php echo $row['name']?></td>
									<td style="text-align:center;"><?php echo $row['from_id']?></td>
									<td style="text-align:center;"><?php echo $row['phone']?></td>
									<?php if($param['pay_style'] == 1) { ?>
									<td style="text-align:center;"><a href="/mshop/admin/index?m=yundian&a=yundian_order_list&batchcode=<?php echo $row['batchcode']?>&type=1"><?php echo $row['batchcode']?></a></td>
									<?php } else { ?>
									<td style="text-align:center;"><a href="/mshop/admin/index?m=yundian&a=yundian_order_list&batchcode=<?php echo $row['batchcode']?>&type=0"><?php echo $row['batchcode']?></a></td>
									<?php } ?>		
									<td style="text-align:center;"><?php echo $row['totalprice']?></td>
									<?php if($param['pay_style'] == 1) { ?>
										<td style="text-align:center;"><?php echo $row['rcount']?></td>
										<td style="text-align:center;"><?php echo $row['reward']?></td>
									<?php } else { ?>
										<td style="text-align:center;"><?php echo $row['reward_money']?></td>
									<?php } ?>		
									<td style="text-align:center;"><?php echo $row['money']?></td>
									<td style="text-align:center;"><?php echo $order_status?></td>
									
								</tr>
							
							<?php }?>					
						</tbody>
						
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
	<!--内容框架结束-->
</body>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script><!--添加时间插件-->
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript">

var from_id = $("#from_id").val();
var name = $("#name").val();
var batchcode = $("#batchcode").val();
var start_time = $("#start_time").val();
var end_time = $("#end_time").val();
var param = "";

if(from_id!=""){
	param += "&from_id="+from_id;
}
if(name!=""){
	param += "&name="+name;
}
if(batchcode!=""){
	param += "&batchcode="+batchcode;
}
if(start_time!=""){
	param += "&start_time="+start_time;
}
if(end_time!=""){
	param += "&end_time="+end_time;
}
param += "&user_id="+<?php echo($param['user_id']);?>;
param += "&pay_style="+<?php echo($param['pay_style']);?>;

<!-- 分页 start -->
var pagenum = <?php echo $pageNum ?>;//当前页
var count =<?php echo $pageCount ?>;//总页数	
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
	var url="/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_reward_detail&pagenum="+p+param;	
	location.href = url;
   }
});

function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_reward_detail&pagenum="+a+param;	
		location.href = url;
	}
}
<!-- 分页 end -->
//限制输入数字START
function num(obj){
obj.value = obj.value.replace(/[^\d]/g,""); //清除"数字"和"."以外的字符
}
//限制输入数字END

</script>	
</html>