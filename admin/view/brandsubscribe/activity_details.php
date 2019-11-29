<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>品牌订阅－活动明细</title>
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
					<a class="white1">活动明细</a>
				</div>
			</div>
		    <!--活动明细列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=brandsubscribe&a=activity_details_list">
						<input type="hidden" id="m" name="m" value="brandsubscribe">
						<input type="hidden" id="a" name="a" value="activity_details_list">
						<ul class="WSY_search_q">
							<input type="hidden" name="activity_id" id="activity_id" value="<?php echo $param['activity_id'];?>">
							<li>用户名：<input type="text" name="user_name" id="user_name" value="<?php if($param['user_name']!=""){echo $param['user_name'];}?>" class="form_input"></li>
							<li>用户名ID：<input type="text" name="user_id" id="user_id" value="<?php if($param['user_id']!=-1){echo $param['user_id'];}?>" class="form_input" oninput="num(this);"></li>
							<li><input type="submit" class="WSY-skin-bg form-btn"  value="搜索" ></li>
						</ul> 
					</form>

		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="16%" nowrap="nowrap" align="center">用户ID</th>
							<th width="20%" nowrap="nowrap" align="center">用户名</th>
							<th width="16%" nowrap="nowrap" align="center">已订阅产品数</th>
							<th width="16%" nowrap="nowrap" align="center">订阅中</th>
							<th width="16%" nowrap="nowrap" align="center">已失效</th>
							<th width="16%" nowrap="nowrap" align="center">操作</th>
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $key => $row) { 								?>
								<tr>
									<td style="text-align:center;"><?php echo $row['user_id'];?></td>
									<td style="text-align:center;"><?php echo $row['name'];?></td>
									<td style="text-align:center;"><?php echo $row['products_subscriber_num'];?></td>
									<td style="text-align:center;"><?php echo $row['insubscription'];?></td>
									<td style="text-align:center;"><?php echo $row['be_continued'];?></td>
									<td style="text-align:center;">
										<button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['user_id']?>',1);">查看</button>
									</td>
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
		    <!--活动明细列表代码结束-->
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	<!--内容框架结束-->
</body>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script><!--添加时间插件-->
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript">

var activity_id = $("#activity_id").val();
var user_name = $("#user_name").val();
var user_id = $("#user_id").val();
var param = "";
if(user_name!=""){
	param += "&user_name="+user_name;
}
if(user_id!=""){	
	param += "&user_id="+parseInt(user_id);
}

<!-- 分页 start -->
var pagenum = <?php echo $pageNum ?>;//当前页
var count =<?php echo $pageCount ?>;//总页数	
//pageCount：总页数brandsubscribe
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
	var url="/mshop/admin/index.php?m=brandsubscribe&a=activity_details_list&activity_id="+activity_id+"&pagenum="+p+param;	

	location.href = url;
   }
});

function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=brandsubscribe&a=activity_details_list&activity_id="+activity_id+"&pagenum="+a+param;	
		location.href = url;
	}
}
<!-- 分页 end -->

function jump_url(user_id,type){
	//type : 1-查看，编辑
	var url = "";
	if(type==1){
		url = "/mshop/admin/index.php?m=brandsubscribe&a=user_product_list&activity_id="+activity_id+"&user_id="+user_id;
	}
	location.href = url;
}

//限制输入数字START
function num(obj){
obj.value = obj.value.replace(/[^\d]/g,""); //清除"数字"和"."以外的字符
}
//限制输入数字END
</script>	
</html>