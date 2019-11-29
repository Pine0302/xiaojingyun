<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>活动管理－队列活动</title>
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
				<!-- <div class="WSY_columnnav">
					<a class="white1">满赠活动</a>
				</div> -->
				<?php 
					$head = 0;
					include("queue_head.php");
				?>
			</div>
		    <!--产品管理代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=m=queue&a=queue_activity">
						<input type="hidden" id="m" name="m" value="queue">
						<input type="hidden" id="a" name="a" value="queue_activity">
						<ul class="WSY_search_q">
							<li>活动编码：<input type="text" name="activity_id" id="activity_id" value="<?php if($param['activity_id']!=-1){echo $param['activity_id'];}?>" class="form_input" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" onchange="clearNoNum(this)" ></li>
							<li>活动名称：<input type="text" name="activity_name" id="activity_name" value="<?php if($param['activity_name']!=""){echo $param['activity_name'];}?>" class="form_input"></li>
							<li>活动时间：
								<input class="form_input" type="text" id="starttime" name="starttime" value="<?php if($param['starttime']!=-1){echo $param['starttime'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px" />
								 至：
								<input class="form_input" type="text" id="endtime" name="endtime" value="<?php if($param['endtime']!=-1){echo $param['endtime'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px"/>
							</li>
							<li>创建时间：
								<input class="form_input" type="text" id="createtime_start" name="createtime_start" value="<?php if($param['createtime_start']!=-1){echo $param['createtime_start'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px" />
								 至：
								<input class="form_input" type="text" id="createtime_end" name="createtime_end" value="<?php if($param['createtime_end']!=-1){echo $param['createtime_end'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px"/>
							</li>
							<li>活动状态：
								<select name="isout" id="isout">
									<option value="-1">全部</option>
									<option value="3" <?php if($param['isout']==3){?>selected<?php }?>>待启用</option>	
									<option value="1" <?php if($param['isout']==1){?>selected<?php }?>>已启用</option>
									<option value="2" <?php if($param['isout']==2){?>selected<?php }?>>已终止</option>
								</select>
							</li>
							<li><input type="submit" class="WSY-skin-bg form-btn"  value="搜索" ></li>
							<li><a class="WSY-skin-bg form-btn form-add-btn" onclick="jump_url(0,3);">创建活动</a></li>
						</ul> 
					</form>

		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="6%" nowrap="nowrap"align="center">活动编码</th>
							<th width="15%" nowrap="nowrap"align="center">活动名称</th>
							<th width="8%" nowrap="nowrap"align="center">产品总数</th>
							<th width="8%" nowrap="nowrap"align="center">参与用户总数</th>
							<th width="8%" nowrap="nowrap"align="center">发放总额</th>
							<th width="10%" nowrap="nowrap"align="center">创建时间</th>
							<th width="20%" nowrap="nowrap"align="center">活动时间</th>
							<th width="10%" nowrap="nowrap"align="center">状态</th>
							<th width="20%" nowrap="nowrap"align="center">操作</th>
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $key => $row) { 								
								$isout = $row['isout'] ;
								switch($isout){
									case "0":
										$status_str = "待启用";
										break;
									case "1":
										$status_str = "已启用";
										break;
									case "2":
										$status_str = "已终止";
										break;
									default:
										$status_str = "未知状态";
								}
								?>
								<tr>
									<td style="text-align:center;"><?php echo $row['id']?></td>
									<td style="text-align:center;"><?php echo $row['name']?></td>
									<td style="text-align:center;"><?php echo $row['count']?></td>
									<td style="text-align:center;"><?php echo $row['people']?></td>
									<td style="text-align:center;">￥<?php echo $row['bonus']?></td>
									<td style="text-align:center;"><?php echo $row['createtime']?></td>
									<td style="text-align:center;"><?php echo date('Y-m-d H:i',strtotime($row['start_time']))?> - <?php echo date('Y-m-d H:i',strtotime($row['end_time']))?></td>
									<td style="text-align:center;"><?php echo $status_str;?></td>
									<td style="text-align:center;">
										<?php if($isout==0){?>
										<button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['id']?>',1);">编辑</button>
										<?php }?>
										<?php if($isout!=0){?>
										<button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['id']?>',1);">查看</button>
										<?php }?>
										<?php if($isout!=2){?>
										<button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['id']?>',2);">产品</button>
										<?php }?>
										<?php if($isout==0){?>
										<button class="table-btn WSY-skin-bg" onclick="operate_activity('<?php echo $row['id']?>',1);">启用</button>
										<?php }?>
										<?php if($isout==1){?>
										<button class="table-btn WSY-skin-bg" onclick="operate_activity('<?php echo $row['id']?>',2);">终止</button>
										<?php }?>
										<?php if($isout!=1){?>
										<button class="table-btn WSY-skin-bg" onclick="operate_activity('<?php echo $row['id']?>',3);">删除</button>
										<?php }?>
										<?php if($isout!=0){?>
										<button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['id']?>',4);">明细</button>
										<?php }?>
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
		    <!--产品管理代码结束-->
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
var isout = $("#isout").val();
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
if(isout!=-1){
	param += "&isout="+isout;
}


<!-- 分页 start -->
var pagenum = <?php echo $pageNum ?>;//当前页
var count =<?php echo $pageCount ?>;//总页数	
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
	var url="/mshop/admin/index.php?m=queue&a=queue_activity&pagenum="+p+param;	

	location.href = url;
   }
});

function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=queue&a=queue_activity&pagenum="+a+param;	
		location.href = url;
	}
}
<!-- 分页 end -->

function operate_activity(id,type){
	//type：1启用 2删除 3终止
	var remark = "";
	var url = '/mshop/admin/index.php?m=queue&a=queue_exec';
	if(type==1){
		remark = "启用活动后不可恢复，继续吗";
	}else if(type==2){
		remark = "终止活动后不可恢复，继续吗";
	}else if(type==3){
		remark = "删除活动后不可恢复，继续吗";
	}

	layer.confirm(remark, {
		title:'警告',
		btn: ['确认','取消']
	}, function(confirm){
		layer.close(confirm);	
		
		$.ajax({
			url: url,
			dataType: 'json',
			type: 'post',
			data: {
				id:id,
				type:type
			},
			success: function(res){		
				console.log(res);
				if( res.errcode == 1 ){
					alert(res.errmsg);
					document.location.reload();
					//window.location = "/mshop/admin/index?m=exchange&a=exchange_activity_list";
				}else{
					alert(res.errmsg);
				}
			}
		});
	}, function(){
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});
}

function jump_url(id,type){
	//type : 1-查看，编辑  2-关联产品  3-添加活动
	var url = "";
	if(type==1){
		url = "/mshop/admin/index.php?m=queue&a=queue_ck&id="+id;
	}else if(type==2){
		url = "/mshop/admin/index.php?m=queue&a=queue_shop&id="+id;
	}else if(type==3){
		url = "/mshop/admin/index.php?m=queue&a=queue_ck";
	}else if(type==4){
		url = "/mshop/admin/index.php?m=queue&a=queue_count&isout=3&activity_id="+id;
	}
	location.href = url;
}

/*只能输入数字*/
function clearNoNum(obj){
	obj.value = obj.value.replace(/[^\d]/g,""); //清除"数字"以外的字符
	obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字而不是
}

</script>	
</html>