<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>云店奖励－操作日志</title>
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
				<?php $keyContent = '操作日志'; ?>
                <?php include 'cloud_shop_switching.php'; ?>
			</div>
		    <!--店主列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=yundian&a=yundian_setting_log">
						<input type="hidden" id="m" name="m" value="yundian">
						<input type="hidden" id="a" name="a" value="yundian_setting_log">
						<ul class="WSY_search_q">
							<li>操作描述：<input type="text" name="word" id="word" value="<?php if($param['word']!=""){echo $param['word'];}?>" class="form_input"></li>
							<li>时间搜索：
								<input class="form_input" type="text" id="start_time" name="start_time" value="<?php if($param['start_time']!=-1){echo $param['start_time'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px" />
								至
								<input class="form_input" type="text" id="end_time" name="end_time" value="<?php if($param['end_time']!=-1){echo $param['end_time'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px"/>
							</li>

							<li><input type="submit" class="WSY-skin-bg form-btn"  value="搜索" ></li>
						</ul> 
					</form>

		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="5%" align="center">序号</th>
							<th width="10%" align="center">操作人</th>
							<th width="15%" align="center">操作</th>
							<th width="25%" align="center">操作描述</th>
							<th width="12.5%" align="center">操作时间</th>
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $key => $row) { ?>
								<tr>
									<td style="text-align:center;"><?php echo $row['id']?></td>
									<td style="text-align:center;"><?php echo $row['operationuser']?></td>
									<td style="text-align:center;"><?php echo $row['title']?></td>
									<td style="text-align:center;"><?php echo $row['remark']?></td>
									<td style="text-align:center;"><?php echo date('Y-m-d H:i',strtotime($row['createtime']))?></td>

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

var word = $("#word").val();
var start_time = $("#start_time").val();
var end_time = $("#end_time").val();
var param = "";

if(word!=""){
	param += "&word="+word;
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
	var url="/mshop/admin/index.php?m=yundian&a=yundian_setting_log&pagenum="+p+param;
	location.href = url;
   }
});

function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=yundian&a=yundian_setting_log&pagenum="+a+param;
		location.href = url;
	}
}
<!-- 分页 end -->

//导出处理END
</script>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">
</html>