<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>品牌订阅－用户订阅明细</title>
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

	</style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
	    <!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">用户订阅明细</a>
				</div>
			</div>
		    <!--用户订阅明细列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=brandsubscribe&a=user_product_list">
						<input type="hidden" id="m" name="m" value="brandsubscribe">
						<input type="hidden" id="a" name="a" value="user_product_list">
						<ul class="WSY_search_q">
							<input type="hidden" name="activity_id" id="activity_id" value="<?php echo $param['activity_id'];?>">
							<input type="hidden" name="user_id" id="user_id" value="<?php echo $param['user_id'];?>">
							<li>产品名：<input type="text" name="product_name" id="product_name" value="<?php if($param['product_name']!=""){echo $param['product_name'];}?>" class="form_input"></li>
							<li>产品ID：<input type="text" name="product_id" id="product_id" value="<?php if($param['product_id']!=-1){echo $param['product_id'];}?>" class="form_input" oninput="num(this);"></li>
							<li>订阅状态：
								<select name="product_status" id="product_status">
									<option value="-1">--全部--</option>
									<option value="1" <?php if($param['product_status']==1){?>selected<?php }?>>订阅中</option>
									<option value="2" <?php if($param['product_status']==2){?>selected<?php }?>>已失效</option>	
									<option value="3" <?php if($param['product_status']==3){?>selected<?php }?>>已结束</option>
								</select>
							</li>
							<li><input type="submit" class="WSY-skin-bg form-btn"  value="搜索" ></li>
						</ul> 
					</form>

		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="10%" nowrap="nowrap" align="center">产品ID</th>
							<th width="10%" nowrap="nowrap" align="center">产品名称</th>
							<th width="10%" nowrap="nowrap" align="center">产品分类</th>
							<th width="10%" nowrap="nowrap" align="center">关联礼包</th>
							<th width="10%" nowrap="nowrap" align="center">礼包时限</th>
							<th width="10%" nowrap="nowrap" align="center">限购数量</th>
							<th width="10%" nowrap="nowrap" align="center">复购价格</th>
							<th width="10%" nowrap="nowrap" align="center">订阅状态</th>
							<th width="10%" nowrap="nowrap" align="center">剩余数量</th>
							<th width="10%" nowrap="nowrap" align="center">剩余天数</th>
						</thead>
						<tbody class="tbody-main">
							<?php
							foreach ($data as $key => $row) {						
								$aut_end_time = $row['aut_end_time'];
								$bsa_end_time = $row['bsa_end_time'];
								$time_type = $row['time_type'];
								$status = $row['status'];
								$num_left = $row['num_left'];
								if ($time_type == 1) {
									if (strtotime($bsa_end_time) < time() || $status == 3 || $status == 4 ) {
										$status_str = '已结束';
									} else if (strtotime($aut_end_time) < time() || $num_left == 0) {
										$status_str = '已失效';
									} else {
										$status_str = '订阅中';
									}
								} else {
									if ($status == 3 || $status == 4) {
										$status_str = '已结束';
									} else if (strtotime($aut_end_time) < time() || $num_left == 0) {
										$status_str = '已失效';
									} else {
										$status_str = '订阅中';
									}
								}
								?>
								<tr>
									<td style="text-align:center;"><?php echo $row['product_id'];?></td>
									<td style="text-align:center;"><?php echo $row['name'];?></td>
									<td style="text-align:center;"><?php echo $row['typename'];?>
									<td style="text-align:center;"><?php echo $row['package_name'];?></td>
									<td style="text-align:center;"><?php echo $row['time_limit'];?></td>
									<td style="text-align:center;"><?php if ($row['total_limit_num'] == -1) {echo '不限';} else {echo $row['total_limit_num'];}?></td>
									<td style="text-align:center;"><?php echo $row['activity_price'];?></td>
									<td style="text-align:center;"><?php echo $status_str;?></td>
									<td style="text-align:center;"><?php if ($row['total_limit_num'] == -1) {echo '不限';} else {echo $row['num_left'];}?></td>
									<td style="text-align:center;"><?php echo $row['day_left'];?></td>
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
		    <!--用户订阅明细列表代码结束-->
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	<!--内容框架结束-->
</body>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script><!--添加时间插件-->
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript">

var product_name = $("#product_name").val();
var product_id = $("#product_id").val();
var product_status = $("#product_status").val();
var param = "";
if(product_id!=""){	
	param += "&product_id="+parseInt(product_id);
}
if(product_name!=""){
	param += "&product_name="+product_name;
}
if(product_status!=-1){
	param += "&product_status="+product_status;
}


<!-- 分页 start -->
var pagenum = <?php echo $pageNum ?>;//当前页
var count =<?php echo $pageCount; ?>;//总页数	
//pageCount：总页数brandsubscribe
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
	var url="/mshop/admin/index.php?m=brandsubscribe&a=user_product_list&pagenum="+p+param;	

	location.href = url;
   }
});

function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=brandsubscribe&a=user_product_list&pagenum="+a+param;	
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