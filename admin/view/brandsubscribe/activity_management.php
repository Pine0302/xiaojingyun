<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>品牌订阅－活动管理</title>
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
                <?php 
					$head = 0;
					include("brandsubscribe_head.php");
				?>
			</div>
		    <!--店主列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=brandsubscribe&a=activity_management" onsubmit="return tojudge()">
						<input type="hidden" id="m" name="m" value="brandsubscribe">
						<input type="hidden" id="a" name="a" value="activity_management">
						<ul class="WSY_search_q">
							<li>活动编码：<input type="text" name="id" id="id" value="<?php if($param['id']!=-1){echo $param['id'];}?>" class="form_input" oninput="num(this);"/></li>
							<li>活动名称：<input type="text" name="name" id="name" value="<?php if($param['name']!=""){echo $param['name'];}?>" class="form_input"></li>
							<li>时间：
								<input class="form_input" type="text" id="start_time" name="start_time" value="<?php if($param['start_time']!=-1){echo $param['start_time'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px" />
								至：
								<input class="form_input" type="text" id="end_time" name="end_time" value="<?php if($param['end_time']!=-1){echo $param['end_time'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px"/>
							</li>							
							<li>活动状态：
		                        <select id="status" name="status">
		                        	<option value =''>-请选择-</option>
		                        	<option value ='1'>待发布</option>
		                        	<option value ='2'>已发布</option>
		                        	<option value ='ongoing'>进行中</option>
		                        	<option value ='3'>结束</option>
		                        	<option value ='4'>手动结束</option>
		                        </select>
							</li>

							<li><input type="submit" class="WSY-skin-bg form-btn"  value="搜索" ></li>
							<li><a class="WSY-skin-bg form-btn form-add-btn" onclick="activity_deal('',1);">添加活动</a></li>
<!-- 							<li><a class="WSY-skin-bg form-btn form-add-btn" onclick="show_div_export();">导出列表</a></li> -->
						</ul> 
					</form>

		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="10%" nowrap="nowrap"align="center">活动编码</th>
							<th width="15%" nowrap="nowrap"align="center">活动名称</th>
							<th width="20%" nowrap="nowrap"align="center">活动时间</th>
							<th width="15%" nowrap="nowrap"align="center">关联礼包</th>
							<th width="10%" nowrap="nowrap"align="center">状态</th>
							<th width="30%" nowrap="nowrap"align="center">操作</th>
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $key => $row) {
								$break = 0;
								switch ($row['status']) {
									case '1':
										$status = '待发布';
										break;
									case '2':
										if(((strtotime($row['start_time']) < time() && strtotime($row['end_time']) > time() && $row['time_type'] == 1 ) || (strtotime($row['start_time']) < time() && $row['time_type'] == 2))) {
											if($param['status'] == 2) {
												$break = 1;
											}
											$status = '进行中';
										} else if((strtotime($row['start_time']) > time() && strtotime($row['end_time']) > time() && $row['time_type'] == 1) || (strtotime($row['start_time']) > time() &&  $row['time_type'] == 2)) {
											$status = '已发布';
										} else {
											$status = '结束';
										}
										break;
									case '3':
										$status = '结束';
										break;
									case '4':
										$status = '手动结束';
										break;																													
									default:
										$status = '未知状态';
										break;
								}

								if($break == 0) { ?>
								<tr>
									<td style="text-align:center;"><?php echo $row['id']; ?></td>
									<td style="text-align:center;"><?php echo htmlspecialchars($row['name']); ?></td>
									<?php if($row['time_type'] == 1) { ?>
									<td style="text-align:center;"><?php echo $row['start_time']; ?>-<?php echo $row['end_time']; ?></td>
									<?php } else if($row['time_type'] == 2) { ?>
									<td style="text-align:center;"><?php echo $row['start_time']; ?>-永久</td>
									<?php } ?>
									<td style="text-align:center;"><?php echo $row['package_name']; ?></td>
									<td style="text-align:center;"><?php echo $status; ?></td>
									<td style="text-align:center;">
										<?php if($status == '待发布') { ?>
											<button class="table-btn WSY-skin-bg" onclick="activity_deal('<?php echo $row['id']?>',0,this,'<?php echo $row['time_type']?>','<?php echo $row['end_time']?>');">发布</button>
											<button class="table-btn WSY-skin-bg" onclick="activity_deal('<?php echo $row['id']?>',1,this);">编辑</button>
											<button class="table-btn WSY-skin-bg" onclick="activity_deal('<?php echo $row['id']?>',2,this);">删除</button>											
										<?php } else if($status == '进行中' || $status == '已发布') { ?>
											<button class="table-btn WSY-skin-bg" onclick="activity_deal('<?php echo $row['id']?>',4,this);">查看</button>
											<button class="table-btn WSY-skin-bg" onclick="activity_deal('<?php echo $row['id']?>',5,this);">终止</button>
										<?php } else if($status == '结束' || $status == '手动结束') { ?>
											<button class="table-btn WSY-skin-bg" onclick="activity_deal('<?php echo $row['id']?>',4,this);">查看</button>
											<button class="table-btn WSY-skin-bg" onclick="activity_deal('<?php echo $row['id']?>',2,this);">删除</button>
										<?php } ?>
											<button class="table-btn WSY-skin-bg" onclick="activity_deal('<?php echo $row['id']?>',3,this);">关联产品</button>
										
									</td>
								</tr>
							
							<?php } }?>					
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

var id = $("#id").val();
var act_name = $("#name").val();
	$("status").val(sessionStorage.value);
var status = $("#status").val();
var start_time = $("#start_time").val();
var end_time = $("#end_time").val();
var param = "";
if(id!=""){	
	param += "&id="+parseInt(id);
}
if(act_name!=""){
	param += "&name="+act_name;
}
if(status!=""){
	param += "&status="+status;
}
if(start_time!=""){
	param += "&start_time="+start_time;
}
if(end_time!=""){
	param += "&end_time="+end_time;
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
	var url="/mshop/admin/index.php?m=brandsubscribe&a=activity_management&pagenum="+p+param;	
	location.href = url;
   }
});

function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('请输入正确页数');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=brandsubscribe&a=activity_management&pagenum="+a+param;	
		location.href = url;
	}
}
<!-- 分页 end -->
function tojudge() {
var start_time = $("#start_time").val();
var end_time = $("#end_time").val();
	var temp = CheckDateTime(start_time);
	if(!temp) {
		layer.alert('请输入正确时间');
		return false;
	}
	var temp = CheckDateTime(end_time);
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


function activity_deal(id,type,obj,time_type=2,end_time=''){
	//type：0发布1编辑/添加2删除3关联4查看5终止
	var that = $(obj);
	var url = "";
	var remark = "";
	switch(type){
		case 0:

			if(time_type == 1) { //不是永久 活动
				var end_time_c = Date.parse(new Date(end_time)); //计算结束时间 时间戳
				end_time_c = end_time_c / 1000;
				var time_now = ((new Date()).valueOf())/1000;
				if(time_now >= end_time_c) {
					remark = "设置的活动时间已过！";
					layer.confirm(remark, {
						title:'警告',
						btn: ['前往编辑','取消']
					}, function(confirm){
						layer.close(confirm);
						activity_deal(id,1,obj);
					},function(){

					});
				} else {
					remark = "确定要发布活动吗?";
					activity_deal_ajax(remark,id,type,that);					
				}

			} else if(time_type == 2) { //永久活动
				remark = "确定要发布活动吗?";
				activity_deal_ajax(remark,id,type,that);
			}


		break;
		case 1:

			if(id != ''){
				url = "/mshop/admin/index.php?m=brandsubscribe&a=activity_edit&activity_id="+id;
			} else {
				url = "/mshop/admin/index.php?m=brandsubscribe&a=activity_edit";
			}
			location.href = url;

		break;
		case 2:

			remark = "删除活动后不可恢复，继续吗?";
			activity_deal_ajax(remark,id,type,that);

		break;
		case 3:

			url = "/mshop/admin/index.php?m=brandsubscribe&a=related_products_list&activity_id="+id+"&pagenum=1";
			location.href = url;

		break;
		case 4:

			url = "/mshop/admin/index.php?m=brandsubscribe&a=activity_list&activity_id="+id;
			location.href = url;

		break;	
		case 5:

			remark = "确定要终止活动吗?";
			activity_deal_ajax(remark,id,type,that);

		break;							
		default :

		break;
	}
	
}

function activity_deal_ajax(remark,id,type,that) {
	url = '/mshop/admin/index.php?m=brandsubscribe&a=activity_deal';
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
			async:false,
			success: function(res){		
				console.log(res);
				if( res.errcode == 0 ){
					layer.alert(res.errmsg);
					var html;
					if(type == 0) {		
						var timestamp = ((new Date()).valueOf())/1000;
						var start_time = Date.parse(new Date(res.data['start_time']));
						start_time = start_time / 1000;
						var end_time = Date.parse(new Date(res.data['end_time']));
						end_time = end_time / 1000;									
						if(start_time < timestamp && ((end_time > timestamp && res.data['time_type'] == 1 ) || res.data['time_type'] == 2 )) {
							html = '进行中';
						} else {
							html = '已发布';
						}						
						that.parent('td').prev('td').html(html);
						html = '<button class="table-btn WSY-skin-bg" onclick="activity_deal('+id+',4,this);">查看</button>\n';
						html += '<button class="table-btn WSY-skin-bg" onclick="activity_deal('+id+',5,this);">终止</button>\n';
						html += '<button class="table-btn WSY-skin-bg" onclick="activity_deal('+id+',3,this);">关联产品</button>';
						that.parent('td').html(html);
					} else if(type == 2) {
						that.parents('tr').remove();
					} else if(type ==5) {
						that.parent('td').prev('td').html('手动结束');						
						html = '<button class="table-btn WSY-skin-bg" onclick="activity_deal('+id+',4,this);">查看</button>\n';
						html += '<button class="table-btn WSY-skin-bg" onclick="activity_deal('+id+',2,this);">删除</button>\n';
						html += '<button class="table-btn WSY-skin-bg" onclick="activity_deal('+id+',3,this);">关联产品</button>';
						that.parent('td').html(html);
					}
				}else{
					layer.alert(res.errmsg);
				}
			}
		});
	}, function(){

	});
}


//限制输入数字START
function num(obj){
obj.value = obj.value.replace(/[^\d]/g,""); //清除"数字"和"."以外的字符
}
//限制输入数字END
//保存SELECT数据SATRT
var se = document.getElementById('status');
se.onchange = function(){
    sessionStorage.value = this.value;
    sessionStorage.index = this.selectedIndex;
    // console.log( sessionStorage.index +";"+ sessionStorage.value );
}
window.onload = function(){
    // alert( sessionStorage.index +";"+ sessionStorage.value );
    se.options[ sessionStorage.index ].selected = true;
}
//保存SELECT数据END
</script>
</html>