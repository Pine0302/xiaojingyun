<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>云店奖励－店主列表</title>
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
				<?php $keyContent = '店主列表'; ?>
                <?php include 'cloud_shop_switching.php'; ?>
			</div>
		    <!--店主列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_list">
						<input type="hidden" id="m" name="m" value="yundian">
						<input type="hidden" id="a" name="a" value="yundian_shopkeeper_list">
						<ul class="WSY_search_q">
							<li>用户ID：<input type="text" name="user_id" id="user_id" value="<?php if($param['user_id']!=-1){echo $param['user_id'];}?>" class="form_input" oninput="num(this);"/></li>
							<li>昵称：<input type="text" name="name" id="name" value="<?php if($param['name']!=""){echo $param['name'];}?>" class="form_input"></li>
							<li>店铺名称：<input type="text" name="store_name" id="store_name" value="<?php if($param['store_name']!=""){echo $param['store_name'];}?>" class="form_input" onkeyup="clearTSZF(this) " onafterpaste="clearTSZF(this) "></li>
							<li>用户身份：
		                        <select id="tequan_id" name="tequan_id">
		                          <?php 
		                          echo "<option value =''>全部</option>";
		                            foreach ($res2 as $key => $value) {
		                                echo "<option value ='{$value["id"]}'>{$value["name"]}</option>";
		                            }
		                          ?>
		                        </select>
							</li>
							<li>时间搜索：
								<input class="form_input" type="text" id="verify_time" name="verify_time" value="<?php if($param['verify_time']!=-1){echo $param['verify_time'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px" />
								至：
								<input class="form_input" type="text" id="expire_time" name="expire_time" value="<?php if($param['expire_time']!=-1){echo $param['expire_time'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px"/>
							</li>

							<li><input type="submit" class="WSY-skin-bg form-btn"  value="搜索" ></li>
							<li><a class="WSY-skin-bg form-btn form-add-btn" onclick="show_div_export();">导出列表</a></li>
						</ul> 
					</form>

		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="10%" nowrap="nowrap"align="center">用户ID</th>
							<th width="10%" nowrap="nowrap"align="center">昵称</th>
							<th width="10%" nowrap="nowrap"align="center">店主身份</th>
							<th width="10%" nowrap="nowrap"align="center">店铺名称</th>
							<th width="10%" nowrap="nowrap"align="center">手机号码</th>
							<th width="10%" nowrap="nowrap"align="center">成为店主时间</th>
							<th width="10%" nowrap="nowrap"align="center">到期时间</th>
							<th width="10%" nowrap="nowrap"align="center">店主提成收益</th>
							<th width="10%" nowrap="nowrap"align="center">店主自营收益</th>
							<th width="6%" nowrap="nowrap"align="center">自营商品上架数量</th>
							<th width="7%" nowrap="nowrap"align="center">总订单数量</th>
							<th width="8%" nowrap="nowrap"align="center">操作</th>
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $key => $row) { ?>
								<tr>
									<td style="text-align:center;"><?php echo $row['user_id']?></td>
									<td style="text-align:center;"><?php echo $row['name']?></td>
									<td style="text-align:center;"><?php echo $row['identity_name']?></td>
									<?php if($row['store_name'] ==''){$row['store_name'] =$row['name'];}?>
									<td style="text-align:center;"><?php echo $row['store_name']?></td>
									<td style="text-align:center;"><?php echo $row['phone']?></td>
									<td style="text-align:center;"><?php echo date('Y-m-d H:i',strtotime($row['verify_time']))?></td>
									<td style="text-align:center;"><?php echo date('Y-m-d H:i',strtotime($row['expire_time']))?></td>
									<td style="text-align:center;"><a target="_blank" href="/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_reward_detail&user_id=<?php echo($row['user_id']);?>&pay_style=0"><?php echo $row['profit_keeper']?></a></td>
									<td style="text-align:center;"><a target="_blank" href="/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_reward_detail&user_id=<?php echo($row['user_id']);?>&pay_style=1"><?php echo $row['profit_self']?></a></td>
									<td style="text-align:center;"><a target="_blank" href="/mshop/admin/index.php?m=yundian&a=shopkeeper_order_list&user_id=<?php echo($row['user_id']);?>&type=2"><?php echo $row['isup']?></a></td>
									<td style="text-align:center;"><a target="_blank" href="/mshop/admin/index.php?m=yundian&a=yundian_order_list&yun_user_id=<?php echo($row['user_id']);?>&type=0"><?php echo $row['order_sum']?></a></td>
									<td style="text-align:center;">
										<button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['user_id']?>',1);">编辑</button>
										<button class="table-btn WSY-skin-bg" onclick="operate_activity('<?php echo $row['user_id']?>');">删除</button>
									</td>
								</tr>
							
							<?php }?>					
						</tbody>
						
					</table>
				</div>

				<!-- 导出字段选择 -->
			<div class="floatbox">
				<p class="tishitext">导出字段选择</p>
				<div class="checkboxsdiv">
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="user_id"><p>用户ID</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="name"><p>昵称</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="tequan_id"><p>店主身份</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="store_name"><p>店铺名称</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="phone"><p>手机号码</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="verify_time"><p>成为店主时间</p></div>					
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="expire_time"><p>到期时间</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="profit_keeper"><p>店主提成收益</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="profit_self"><p>店主自营收益</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="product_count"><p>自营商品上架数量</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="order_count"><p>总订单数量</p></div>
				</div>
				<div class="quanbuxuan">
					<input type="checkbox" id="allselects" checked="checked" value="全选"><p>全选</p>
				</div>
				<div class="subdivb">
					<input type="submit" class="floatinputs" value="确定">
					<input type="submit" class="floatinputc" value="取消">
				</div>
			</div>
			<!-- 导出字段选择 End -->
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

var user_id = $("#user_id").val();
var name = $("#name").val();
	$("#tequan_id").val(sessionStorage.value);
var store_name = $("#store_name").val();
var tequan_id = $("#tequan_id").val();
var verify_time = $("#verify_time").val();
var expire_time = $("#expire_time").val();
var param = "";
if(user_id!=""){	
	param += "&user_id="+parseInt(user_id);
}
if(name!=""){
	param += "&name="+name;
}
if(store_name!=""){
	param += "&store_name="+store_name;
}
if(tequan_id!=""){
	param += "&tequan_id="+tequan_id;
}
if(verify_time!=""){
	param += "&verify_time="+verify_time;
}
if(expire_time!=""){
	param += "&expire_time="+expire_time;
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
	var url="/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_list&pagenum="+p+param;	
	location.href = url;
   }
});

function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_list&pagenum="+a+param;	
		location.href = url;
	}
}
<!-- 分页 end -->

function operate_activity(user_id,type){
	//type：1发布 2删除 3终止
	var url = "";
	var remark = "";
	url = '/mshop/admin/index.php?m=yundian&a=del_yundian_shopkeepers';
	remark = "删除店主后不可恢复，继续吗";

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
				user_id:user_id,
			},
			success: function(res){		
				console.log(res);
				if( res.errcode == 1 ){
					alert(res.errmsg);
					document.location.reload();
					//window.location = "/mshop/admin/index?m=yundian&a=yundian_shopkeeper_list";
				}else{
					alert(res.errmsg);
				}
			}
		});
	}, function(){

	});

	
}

function jump_url(user_id,type){
	//type : 1-查看，编辑
	var url = "";
	if(type==1){
		url = "/mshop/admin/index.php?m=yundian&a=edit_yundian_shopkeepers&user_id="+user_id;
	}
	location.href = url;
}

//导出处理START
	function show_div_export(){
	  $(".floatbox").toggle();
	}


	$(".floatinputc").click(function(){
			$(".floatbox").hide();
		});
	$(".floatinputs").click(function(){
		var str="";
		$("input[name='excel_field[]']:checkbox").each(function(){
			if($(this).attr("checked")){
				str += $(this).val()+","
			}
		})
		// alert(str);
		// return;
		str = str.substring(0,str.length-1);
		$(".floatbox").hide();
		//alert(str);
		exportRecord(str);

	});

	// 全选
	$("#allselects").click(function(){
		if(this.checked){
			$(".checkboxsdiv :checkbox").attr("checked", true);
		}else{
			$(".checkboxsdiv :checkbox").attr("checked",false);
		}
	});	


	//导出方法
	function exportRecord(str){

		var search_user_id = document.getElementById("user_id").value;
        var search_name = document.getElementById("name").value;
        var search_tequan_id = document.getElementById("tequan_id").value;
        var search_apply_time = document.getElementById("verify_time").value;
        var search_expire_time = document.getElementById("expire_time").value;

		if(search_user_id == ""){
		search_user_id=-1;
		}
		if(search_name == ""){
		search_name=-1;
		}
		if(search_tequan_id == ""){
		search_tequan_id=-1;
		}
		if(search_apply_time == ""){
		search_apply_time=-1;
		}
		if(search_expire_time == ""){
		search_expire_time=-1;
		}

		var url_base='/weixin/plat/app/index.php/Excel/yundian_shopkeeper_list_excel/customer_id/<?php echo $param['customer_id']; ?>/search_user_id/'+search_user_id+'/search_tequan_id/'+search_tequan_id+'/search_name/'+search_name+'/fields/'+str;

			url_base=url_base+'/search_apply_time/'+search_apply_time;
			url_base=url_base+'/search_expire_time/'+search_expire_time;

			url = url_base;
			console.log(url);
			$.ajax({type:'GET', async:false, url:url,
				success:function(data){
					window.location.href = url;
				}
			});
	}	
//导出处理END
//限制输入数字START
function num(obj){
obj.value = obj.value.replace(/[^\d]/g,""); //清除"数字"和"."以外的字符
}
//限制输入数字END
//保存SELECT数据SATRT
var se = document.getElementById('tequan_id');
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
//* 过滤特殊字符 */
function clearTSZF(obj){
	obj.value = stripscript(obj.value);
}
function stripscript(s) 
{ 
	var pattern = new RegExp("[`~!%@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]");
	var rs = ""; 
	for (var i = 0; i < s.length; i++) { 
		rs = rs+s.substr(i, 1).replace(pattern, ''); 
	} 
	return rs; 
}
</script>
</html>