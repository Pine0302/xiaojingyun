<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>彩铃订单</title>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
<!-- 	<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css"> -->
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
	<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/mshop/admin/Common/js/layer-v3.1.1/layer.js"></script>
<!-- 	<script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script> -->
<!-- <script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script> -->

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
					$head = 2;
					include("cailing_head.php");
				?>
			</div>
		    <!--订单列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=cailing&a=order_list_management" >
						<input type="hidden" id="m" name="m" value="cailing">
						<input type="hidden" id="a" name="a" value="order_list_management">
						<ul class="WSY_search_q">
							<li>订单号：
								<input type="text" name="batchcode" id="batchcode" value="<?php if($param['batchcode']!=-1){echo $param['batchcode'];}?>" class="form_input" oninput="num(this);"/>
							</li>
							<li>手机号码：
								<input type="text" name="use_phone" id="use_phone" value="<?php if($param['use_phone']!=-1){echo $param['use_phone'];}?>" class="form_input" oninput="num(this);"/>
							</li>
							<li>支付类型：
		                        <select id="paystyle" name="paystyle">
		                        	<option value =''>全部</option>
		                        	<option value ='零钱'>零钱支付</option>
		                        	<option value ='微信'>微信支付</option>
		                        	<option value ='后台'>后台支付</option>
		                        	<option value ='优惠抵扣'>优惠抵扣</option>
		                        	<option value ='提单不支付'>提单不支付</option>		                        	
		                        </select>
							</li>							
							<li>状态筛选：
		                        <select id="status" name="status">
		                        	<option value =''>全部</option>
		                        	<option value ='1'>待付款</option>
		                        	<option value ='2'>待完成</option>
		                        	<option value ='3'>已完成</option>
		                        	<option value ='4'>已退款</option>
		                        </select>
							</li>

							<li><input type="submit" class="WSY-skin-bg form-btn"  value="搜索" ></li>
							<li><a class="WSY-skin-bg form-btn form-add-btn" onclick="show_div_export();">导出列表</a></li>
						</ul> 
					</form>

		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="9.5%" nowrap="nowrap"align="center">订单号</th>
							<th width="9.5%" nowrap="nowrap"align="center">下单时间</th>
							<th width="10.5%" nowrap="nowrap"align="center">订购彩铃</th>
							<th width="5.5%" nowrap="nowrap"align="center">订购价格</th>
							<th width="8.5%" nowrap="nowrap"align="center">用户名</th>
							<th width="8.5%" nowrap="nowrap"align="center">用户编号</th>
							<th width="8.5%" nowrap="nowrap"align="center">手机号码</th>
							<th width="5.5%" nowrap="nowrap"align="center">订单状态</th>
							<th width="8.5%" nowrap="nowrap"align="center">支付时间</th>
							<th width="7.5%" nowrap="nowrap"align="center">支付类型</th>							
							<th width="18%" nowrap="nowrap"align="center">操作</th>														
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $key => $row) {
								$time_now = date('Y-m-d H:i:s');
								if($row['paystatus'] == 0 && $row['status'] != -1 && $row['status'] != 4 && $time_now<=$row['recoverytime']) {
									$status = '待付款';
								}else if($row['paystatus'] == 1 && $row['status'] == 2) {
									$status = '待完成';
								}else if($row['paystatus'] == 1 && $row['status'] == 1) {
									$status = '已完成';
								}else if($row['status'] == 3) {
									$status = '已退款';
								}else if($row['status'] == -1){
									$status = '取消订单';
								}else if($row['status'] == 4 || $time_now>$row['recoverytime']){
									$status = '订单失效';
								}else {
									$status = '未知状态';
								}

								 ?>
								<tr>
									<td style="text-align:center;"><?php echo $row['batchcode']; ?></td>
									<td style="text-align:center;"><?php echo $row['createtime']; ?></td>
									<td style="text-align:center;"><?php echo $row['colortone_name']; ?></td>
									<td style="text-align:center;"><?php echo $row['money']; ?></td>
									<td style="text-align:center;"><?php echo $row['weixin_name']; ?></td>
									<td style="text-align:center;"><?php echo $row['user_id']; ?></td>
									<td style="text-align:center;"><?php echo $row['use_phone']; ?></td>
									<td style="text-align:center;"><?php echo $status; ?></td>
									<td style="text-align:center;"><?php echo $row['paytime']; ?></td>
									<?php if($status == '待付款') { ?>
									<td style="text-align:center;">提单不支付</td>	
									<?php } else { ?>
									<td style="text-align:center;"><?php echo $row['paystyle']; ?></td>	
									<?php } ?>
									<td style="text-align:center;">
										<?php if($status == '待付款' ) { ?>
											<button class="table-btn WSY-skin-bg" onclick="order_deal('<?php echo $row['id']?>',0,this,'<?php echo $row['batchcode']?>');">确认支付</button>
										<?php } else if($status == '待完成' ) { ?>
											<button class="table-btn WSY-skin-bg" onclick="order_deal('<?php echo $row['id']?>',1,this,'<?php echo $row['batchcode']?>');">确认完成</button>
											<button class="table-btn WSY-skin-bg" onclick="order_deal('<?php echo $row['id']?>',2,this,'<?php echo $row['batchcode']?>');">退款</button>
										<?php } else if($status == '已完成' || $status == '已退款' || $status == '取消订单' || $status == '订单失效' ) { ?>
											<button class="table-btn WSY-skin-bg" onclick="order_deal('<?php echo $row['id']?>',3,this,'<?php echo $row['batchcode']?>');">删除</button>
										<?php } ?>
											<button class="table-btn WSY-skin-bg" onclick="order_deal('<?php echo $row['id']?>',4,this,'<?php echo $row['batchcode']?>');">详情</button>
											<button class="table-btn WSY-skin-bg" onclick="order_deal('<?php echo $row['id']?>',5,this,'<?php echo $row['batchcode']?>');">备注</button>
										
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
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="batchcode"><p>订单号</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="createtime"><p>下单时间</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="colortone_name"><p>订购彩铃</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="money"><p>订购价格</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="weixin_name"><p>用户名</p></div>					
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="user_id"><p>用户编号</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="use_phone"><p>手机号码</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="status"><p>订单状态</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="paytime"><p>支付时间</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="paystyle"><p>支付类型</p></div>
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
		        <div class="WSY_page" style="width:98.3% ">
		        	
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
// layer.config({
//     extend: '/extend/layer.ext.js'
// });
	$("#status").val(sessionStorage.value);
	$("#paystyle").val(sessionStorage.value2);    
var customer_id = '<?php echo $param['customer_id']; ?>';
var customer_id_en = '<?php echo $customer_id_en; ?>';
var batchcode = $("#batchcode").val();
var status = $("#status").val();
var use_phone = $("#use_phone").val();
var paystyle = $("#paystyle").val();
var param = "";
if(batchcode!=""){	
	param += "&batchcode="+parseInt(batchcode);
}
if(use_phone!=""){
	param += "&use_phone="+use_phone;
}
if(paystyle!=""){
	param += "&paystyle="+paystyle;
}
if(status!=""){
	param += "&status="+status;
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
	var url="/mshop/admin/index.php?m=cailing&a=order_list_management&pagenum="+p+param;	
	location.href = url;
   }
});

function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('请输入正确页数');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=cailing&a=order_list_management&pagenum="+a+param;	
		location.href = url;
	}
}
<!-- 分页 end -->

function order_deal(id,type,obj,batchcode){
	//type：0确认支付1确认完成2退款3删除4详情5备注
	var that = $(obj);
	var url = "";
	var remark = "";
	switch(type){
		case 0:

			remark = "确定要确认支付吗？";
			order_deal_ajax_c(remark,id,type,that,batchcode);

		break;
		case 1:

			remark = "确定要完成订单吗？";
			order_deal_ajax_c(remark,id,type,that,batchcode);

		break;
		case 2:

			remark = "确定要退款吗？请输入备注。";
			order_deal_ajax_a(remark,id,type,that,batchcode);

		break;
		case 3:

			remark = "删除订单后不可恢复，继续吗？";
			order_deal_ajax_c(remark,id,type,that,batchcode);

		break;
		case 4:

			url = "/mshop/admin/index.php?m=cailing&a=order_details&batchcode="+batchcode+"&customer_id="+customer_id_en;
			location.href = url;

		break;	
		case 5:

			remark = "请输入备注！";
			order_deal_ajax_b(remark,id,type,that,batchcode);

		break;							
		default :

		break;
	}
	
}


function order_deal_ajax_c(remark,id,type,that,batchcode) {
	url = '/mshop/admin/index.php?m=cailing&a=order_deal';
	layer.confirm(remark, {
		title:'警告',
		btn: ['确认','取消']
	}, function(confirm){
		layer.close(confirm);	
		  var content = '';
			$.ajax({
				url: url,
				dataType: 'json',
				type: 'post',
				data: {
					id:id,
					type:type,
					customer_id:customer_id,
					batchcode:batchcode,
					content:content,
				},
				async:false,
				success: function(res){	//type：0确认支付1确认完成2退款3删除4详情5备注
					console.log(res);
						var html;
						if(type == 0) {
							that.parent('td').prev('td').html('后台支付');
							that.parent('td').prev('td').prev('td').html(res.data.paytime);
							that.parent('td').prev('td').prev('td').prev('td').html('待完成');
							html = '<button class="table-btn WSY-skin-bg" onclick="order_deal(\''+id+'\',1,this,\''+batchcode+'\');">确认完成</button>\n';
							html += '<button class="table-btn WSY-skin-bg" onclick="order_deal(\''+id+'\',2,this,\''+batchcode+'\');">退款</button>\n';
							html += '<button class="table-btn WSY-skin-bg" onclick="order_deal(\''+id+'\',4,this,\''+batchcode+'\');">详情</button>\n';
							html += '<button class="table-btn WSY-skin-bg" onclick="order_deal(\''+id+'\',5,this,\''+batchcode+'\');">备注</button>';
							that.parent('td').html(html);
						} else if(type ==1) {
							that.parent('td').prev('td').prev('td').prev('td').html('已完成');
							html = '<button class="table-btn WSY-skin-bg" onclick="order_deal(\''+id+'\',3,this,\''+batchcode+'\');">删除</button>\n';
							html += '<button class="table-btn WSY-skin-bg" onclick="order_deal(\''+id+'\',4,this,\''+batchcode+'\');">详情</button>\n';
							html += '<button class="table-btn WSY-skin-bg" onclick="order_deal(\''+id+'\',5,this,\''+batchcode+'\');">备注</button>';
							that.parent('td').html(html);
						} else if(type == 3) {
							that.parents('tr').remove();
						}
					layer.alert(res.errmsg);

				}
			});
	}, function(){

	});
}


function order_deal_ajax_b(remark,id,type,that,batchcode) {
	$("textarea").attr("maxlength",10);
		url = '/mshop/admin/index.php?m=cailing&a=order_deal';
		layer.prompt({
		formType: 2,
		value: '',
		maxlength:200,
		title: remark,
		area: ['350px', '200px'],
		  success: function(layero, index){
		  	layero.find(".layui-layer-input").attr('placeholder','请输入备注，不能超过200字');
		    layero.find(".layui-layer-input").attr('maxlength',200);
		  }
		}, function(value, index, elem){
		  var content = value;
			if(content.length > 200) {
				layer.alert('不能超过200字');
			} else {
			  layer.close(index);
				$.ajax({
					url: url,
					dataType: 'json',
					type: 'post',
					data: {
						id:id,
						type:type,
						customer_id:customer_id,
						batchcode:batchcode,
						content:content,
					},
					async:false,
					success: function(res){	//type：0确认支付1确认完成2退款3删除4详情5备注
						console.log(res);

						layer.alert(res.errmsg);

					}
				});
			}	  
		});
}

function order_deal_ajax_a(remark,id,type,that,batchcode) {
		url = '/mshop/admin/index.php?m=cailing&a=order_deal';
		layer.prompt({
		formType: 2,
		value: '',
		maxlength:200,
		title: remark,
		area: ['350px', '200px'], //自定义文本域宽高
		success: function(layero, index){
			layero.find(".layui-layer-input").attr('placeholder','请输入备注，不能超过200字');
		    layero.find(".layui-layer-input").attr('maxlength',200);
		  },
		yes: function(index, layero){
			var content = layero.find(".layui-layer-input").val();
			if(content.length > 200) {
				layer.alert('不能超过200字');
			} else {
				layer.close(index);	
				$.ajax({
					url: url,
					dataType: 'json',
					type: 'post',
					data: {
						id:id,
						type:type,
						customer_id:customer_id,
						batchcode:batchcode,
						content:content,
					},
					async:false,
					success: function(res){	//type：0确认支付1确认完成2退款3删除4详情5备注
						console.log(res);

						var html;
						if(type ==2) {
							that.parent('td').prev('td').prev('td').prev('td').html('已退款');
							html = '<button class="table-btn WSY-skin-bg" onclick="order_deal(\''+id+'\',3,this,\''+batchcode+'\');">删除</button>\n';
							html += '<button class="table-btn WSY-skin-bg" onclick="order_deal(\''+id+'\',4,this,\''+batchcode+'\');">详情</button>\n';
							html += '<button class="table-btn WSY-skin-bg" onclick="order_deal(\''+id+'\',5,this,\''+batchcode+'\');">备注</button>';
							that.parent('td').html(html);
						}

						layer.alert(res.errmsg);

					}
				});				
			}

		}
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
var se2 = document.getElementById('paystyle');
se2.onchange = function(){
    sessionStorage.value2 = this.value;
    sessionStorage.index2 = this.selectedIndex;
    // console.log( sessionStorage2.index +";"+ sessionStorage.value );
}
window.onload = function(){
    //alert( sessionStorage.index +";"+ sessionStorage.value );
    se.options[ sessionStorage.index ].selected = true;
    se2.options[ sessionStorage.index2 ].selected = true;

}
//保存SELECT数据END

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

		var search_batchcode = document.getElementById("batchcode").value;
        var search_use_phone = document.getElementById("use_phone").value;
        var search_paystyle = document.getElementById("paystyle").value;
        var search_status = document.getElementById("status").value;

		if(search_batchcode == ""){
		search_batchcode=-1;
		}
		if(search_use_phone == ""){
		search_use_phone=-1;
		}
		if(search_paystyle == ""){
		search_paystyle=-1;
		}
		if(search_status == ""){
		search_status=-1;
		}

		var url_base='/weixin/plat/app/index.php/Excel/color_bell_order_list_excel/customer_id/<?php echo $param['customer_id']; ?>/search_batchcode/'+search_batchcode+'/search_paystyle/'+search_paystyle+'/search_use_phone/'+search_use_phone+'/search_status/'+search_status+'/fields/'+str;

			url = url_base;
			console.log(url);
			$.ajax({type:'GET', async:false, url:url,
				success:function(data){
					window.location.href = url;
				}
			});
	}	
//导出处理END


</script>
</html>