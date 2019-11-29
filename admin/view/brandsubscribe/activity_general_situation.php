<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>品牌订阅－活动概况</title>
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
				<?php 
					$head = 1;
					include("brandsubscribe_head.php");
				?>
			</div>
		    <!--活动概况列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=brandsubscribe&a=activity_list" onsubmit="return tojudge()">
						<input type="hidden" id="m" name="m" value="brandsubscribe">
						<input type="hidden" id="a" name="a" value="activity_list">
						<ul class="WSY_search_q">
							<li>活动编码：<input type="text" name="activity_id" id="activity_id" value="<?php if($param['activity_id']!=-1){echo $param['activity_id'];}?>" class="form_input" oninput="num(this);"></li>
							<li>活动名称：<input type="text" name="activity_name" id="activity_name" value="<?php if($param['activity_name']!=""){echo $param['activity_name'];}?>" class="form_input"></li>
							<li>时间：
								<input class="form_input" type="text" id="starttime" name="starttime" value="<?php if($param['starttime']!=-1){echo $param['starttime'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px" />
								 至：
								<input class="form_input" type="text" id="endtime" name="endtime" value="<?php if($param['endtime']!=-1){echo $param['endtime'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px"/>
							</li>
							<li>活动状态：
								<select name="activity_status" id="activity_status">
									<option value="-1">--请选择--</option>
									<option value="1" <?php if($param['activity_status']==1){?>selected<?php }?>>待发布</option>	
									<option value="2" <?php if($param['activity_status']==2){?>selected<?php }?>>已发布</option>
									<option value="inprocessing" <?php if($param['activity_status']=='inprocessing'){?>selected<?php }?>>进行中</option>
									<option value="3" <?php if($param['activity_status']==3){?>selected<?php }?>>结束</option>	
									<option value="4" <?php if($param['activity_status']==4){?>selected<?php }?>>手动结束</option>
								</select>
							</li>
							<li><input type="submit" class="WSY-skin-bg form-btn"  value="搜索" ></li>
						</ul> 
					</form>

		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="12%" nowrap="nowrap" align="center">活动编码</th>
							<th width="12%" nowrap="nowrap" align="center">活动名称</th>
							<th width="28%" nowrap="nowrap" align="center">活动时间</th>
							<th width="12%" nowrap="nowrap" align="center">活动状态</th>
							<th width="12%" nowrap="nowrap" align="center">产品数量</th>
							<th width="12%" nowrap="nowrap" align="center">用户数量</th>
							<th width="12%" nowrap="nowrap" align="center">操作</th>
						</thead>
						<tbody class="tbody-main">
							<?php
							foreach ($data as $key => $row) {						
								$status = $row['status'] ;
								switch($status){
									case '1':
										$status_str = '待发布';
										break;
									case '2':
										if(strtotime($row['start_time']) < time() && ((strtotime($row['end_time']) > time() && $row['time_type'] == 1 ) || $row['time_type'] == 2 )) {
											if ($param['activity_status'] == 2) {
												$row = [];
											}else{
												$status_str = '进行中';
											}
										} else if((strtotime($row['start_time']) > time() && strtotime($row['end_time']) > time() && $row['time_type'] == 1) || (strtotime($row['start_time']) > time() &&  $row['time_type'] == 2)) {
											$status_str = '已发布';
										} else {
											$status_str = '结束';
										}
										break;
									case "3":
										$status_str = "结束";
										break;
									case "4":
										$status_str = "手动结束";
										break;
									default:
										$status_str = "未知状态";
								}
								if ($row) {
								?>
								<tr>
									<td style="text-align:center;"><?php echo $row['id']?></td>
									<td style="text-align:center;"><?php echo htmlspecialchars($row['name'])?></td>
									<td style="text-align:center;"><?php echo date('Y-m-d H:i',strtotime($row['start_time']))?> - 
										<?php if($row['time_type'] == 1) {
												echo date('Y-m-d H:i',strtotime($row['end_time']));
											}else if($row['time_type'] == 2){
												echo '永久';
											}
											?></td>
									<td style="text-align:center;"><?php echo $status_str;?></td>
									<td style="text-align:center;"><?php echo $row['productnum'];?></td>
									<td style="text-align:center;"><?php echo $row['usernum'];?></td>
									<td style="text-align:center;">
										<button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['id']?>',1);">查看</button>
									</td>
								</tr>
							
							<?php }}?>							
						</tbody>
						
					</table>
				</div>
		        <!--翻页开始-->
		        <div class="WSY_page">
		        	
		        </div>
		        <!--翻页结束-->
		    </div>
		    <!--活动概况列表代码结束-->
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	<!--内容框架结束-->
</body>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script><!--添加时间插件-->
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript">

var activity_id = $("#activity_id").val();
var activity_name = $("#activity_name").val();
var starttime = $("#starttime").val();
var endtime = $("#endtime").val();
var activity_status = $("#activity_status").val();
var param = "";
if(activity_id!=""){	
	param += "&activity_id="+parseInt(activity_id);
}
if(activity_name!=""){
	param += "&activity_name="+activity_name;
}
if(starttime!=""){
	param += "&starttime="+starttime;
}
if(endtime!=""){
	param += "&endtime="+endtime;
}
if(activity_status!=-1){
	param += "&activity_status="+activity_status;
}


<!-- 分页 start -->
var pagenum = <?php echo $pageNum ?>;//当前页
var count =<?php if (!$row) {echo 0;} else echo $pageCount; ?>;//总页数	
//pageCount：总页数brandsubscribe
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
	var url="/mshop/admin/index.php?m=brandsubscribe&a=activity_list&pagenum="+p+param;	

	location.href = url;
   }
});

function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=brandsubscribe&a=activity_list&pagenum="+a+param;	
		location.href = url;
	}
}
<!-- 分页 end -->

function tojudge() {
var starttime = $("#starttime").val();
var endtime = $("#endtime").val();
	var temp = CheckDateTime(starttime);
	if(!temp) {
		layer.alert('请输入正确时间');
		return false;
	}
	var temp = CheckDateTime(endtime);
	if(!temp) {
		layer.alert('请输入正确时间');
		return false;
	}
	return true;
}

//函数名：CheckDateTime  
//功能介绍：检查是否为日期时间  
function CheckDateTime(str){  
var reg = new RegExp(/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/);
if(str=='')return true; 
if (!reg.test(str)){  
return false;  
}  
return true;  
}  

function jump_url(activity_id,type){
	//type : 1-查看，编辑
	var url = "";
	if(type==1){
		url = "/mshop/admin/index.php?m=brandsubscribe&a=activity_details_list&activity_id="+activity_id;
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