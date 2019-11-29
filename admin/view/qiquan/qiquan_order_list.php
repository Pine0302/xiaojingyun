<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>期权交易－订单列表</title>
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
		#caozuo a img {
            width: 18px;
            height: 18px;
            vertical-align: baseline;
            text-align:center;
        }



	</style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
	    <!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
				<?php $keyContent = '订单管理'; ?>
                <?php include 'qiquan_switching.php'; ?>
			</div>
		    <!--订单列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=qiquan&a=qiquan_order_list">
						<input type="hidden" id="m" name="m" value="qiquan">
						<input type="hidden" id="a" name="a" value="qiquan_order_list">
						<ul class="WSY_search_q">
							<li>单号：<input type="text" name="batchcode" id="batchcode" class="form_input" value="<?php echo $_GET['batchcode']?$_GET['batchcode']:"";?>" /></li>
							<li>手机：<input type="text" name="phone" id="phone" class="form_input" value="<?php echo $_GET['phone']?$_GET['phone']:"";?>"/></li>
							<li>用户ID：<input type="text" name="user_id" id="user_id" class="form_input" value="<?php echo $_GET['user_id']?$_GET['user_id']:"";?>"/></li>
							<li>股票代码： <input type="text" name="stock_code" id="stock_code" class="form_input" value="<?php echo $_GET['stock_code']?$_GET['stock_code']:"";?>"/> </li>
							<li>名义本金： <input type="text" name="capital" id="capital" class="form_input" value="<?php echo $_GET['capital']?$_GET['capital']:"";?>" /> </li>
							<li>状态：
		                        <select id="status" name="status" style="height:26px;" >
		                        	<option value="-1">全部</option>
	                        		<option value="0">待支付</option>
	                          		<option value="1">委托中</option>
	                        		<option value="3">待行权</option>
	                        		<option value="5">行权成功</option>
	                        		<option value="2">取消委托</option>
	                        		<option value="6">行权失败</option>
	                        		<option value="8">失效订单</option>
		                        </select>
							</li>
						</ul>
						<ul class="WSY_search_q">
							<li>时间：
								<select id="search_time_type" name="search_time_type" style="height:26px;">
		                        	<option value="1">下单时间</option>
		                        	<option value="2">委托时间</option>
		                        	<option value="3">认购时间</option>
		                        	<option value="4">结算时间</option>
		                        	<option value="5">取消时间</option>
		                        </select>
								<input class="form_input" type="text" id="start_time" name="start_time" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" value="<?php echo $_GET['start_time']?$_GET['start_time']:"";?>" style="min-width:120px" />
								至：
								<input class="form_input" type="text" id="end_time" name="end_time" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" value="<?php echo $_GET['end_time']?$_GET['end_time']:"";?>" style="min-width:120px"/>
							</li>
							<li><input type="submit" class="WSY-skin-bg form-btn search" onclick="return check_time()" value="搜索"></li>
							<li><input style="height: 24px;border-radius: 3px;" type="button" class="WSY-skin-bg form-btn search reset" value="重置"></li>
						</ul> 
					</form>

		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="4%" nowrap="nowrap" align="center">序号</th>
							<th width="15%" nowrap="nowrap" align="center">订单信息</th>
							<th width="7%" nowrap="nowrap" align="center">标的</th>
							<th width="7%" nowrap="nowrap" align="center">名义本金</th>
							<th width="6%" nowrap="nowrap" align="center">权利金（元）</th>
							<th width="6%" nowrap="nowrap" align="center">认购价</th>
							<th width="8%" nowrap="nowrap" align="center">行权开始时间</th>
							<th width="8%" nowrap="nowrap" align="center">行权到期时间</th>
							<th width="8%" nowrap="nowrap" align="center">结算信息</th>
							<th width="12%" nowrap="nowrap" align="center">支付信息</th>
							<th width="8%" nowrap="nowrap" align="center">订单状态</th>
							<th width="17%" nowrap="nowrap" align="center">时间</th>
							<th width="3%" nowrap="nowrap" align="center">操作</th>
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $key => $row) { ?>
								<tr>
									<td style="text-align:center;"><?php echo $row['id']?></td>
									<td style="text-align:center;">订单号：<a target="_blank" href="/mshop/admin/index.php?m=qiquan&a=qiquan_order_details&id=<?=$row['id']?>" style="color:rgb(0, 0, 255);"><?=$row['batchcode']?></a><br/>用户ID：<?=$row['user_id']?>（<?=$row['weixin_name']?>）<br/>手机号：<?php echo $phone = empty($row['phone'])? '--' :$row['phone'];?></td>
									<td style="text-align:center;"><?=$row['stock_code']?><br/><?=$row['name']?></td>
									<td style="text-align:center;"><?=$row['capital']?>元<br/><?=$row['exercise_cycle']?><br/><?=$row['offer_percent']?>%</td>
									<td style="text-align:center;"><?=$row['droit_price']?></td>
									<td style="text-align:center;">
										<?php 
											if($row['status'] < 3 ||$row['status'] == 8 )
											{
												echo '— —';
											}else
											{
												echo $row['exercise_stock_price'];
											}
										?></td>
									<td style="text-align:center;">
										<?php 
											if($row['status'] < 3 || $row['status'] == 8)
											{
												echo '— —';
											}else
											{
												echo $row['begin_time'];
											}
										?>
									</td>
									<td style="text-align:center;">
										<?php 
											if($row['status'] < 3 || $row['status'] == 8)
											{
												echo '— —';
											}else
											{
												echo $row['exercise_deadline'];
											}
										?>
									</td>
									<td style="text-align:center;">
										<?php 
											if($row['status'] == 5)
											{
												echo $row['close_stock_price']."<br/>";
												echo $row['profit_percent']."%<br/>";
												echo $row['settle_gain']."元";
											}else
											{
												echo '— —';
											}
										?>
									</td>
									<td style="text-align:center;">
										<?php
											if($row['status'] > 0 && $row['paystatus'] > 0 )
											{
												echo $row['paystyle']."<br/>";
												if(!empty($row['alipay_trade_no']) && $row['alipay_trade_no'] != 0 )
												{
													echo $row['alipay_trade_no']."<br/>";
												}
												
												echo $row['pay_time'];
											}else
											{
												echo '— —';
											}
										?>
									</td>
									<td style="text-align:center;">
										<?php
											if($row['status'] == 0 && $row['paystatus'] == 0){
												echo '待支付';
											}
											elseif($row['status'] == 1)
											{
												echo '委托中';
											}
											elseif($row['status'] == 2 )
											{
												echo '取消委托';
											}
											elseif ($row['status'] == 3)
											{
												echo '待行权';
											}
											elseif($row['status'] == 4)
											{
												echo '待结算';
											}
											elseif($row['status'] == 5  )
											{
												if($row['manual_exercise'] == 0 )
												{
													echo '已行权（自动）';
												}elseif ($row['manual_exercise'] == 1 )
												{
													echo '已行权（手动）';
												}
											}
											elseif($row['status'] == 6)
											{
												echo '行权失败';
											}
											elseif($row['status'] == 8 )
											{
												echo '失效订单';
											}

										?>
									</td>
									<td style="text-align:center;">
										下单时间：<?=$row['createtime']?><br/>
										<?php 
											if( $row['status'] >=3 && $row['status'] <= 6 )
											{
												echo '认购日期：'.$row['gain_droit_time'].'<br/>';
											}
											if( $row['status'] == 5)
											{
												echo '结算时间：'.$row['settle_time'].'<br/>';
											}
											if( $row['status'] == 2 )
											{
												echo '取消时间：'.$row['cancel_time'].'<br/>';
											}
											if( $row['status'] == 8 )
											{
												echo '失效原因：'.$row['invalid_reason'].'<br/>';
											}
										 ?>
									</td>
									<td id="caozuo">
                                        <?php if($row['is_blacklist'] == 0){?>
                                            <a onclick="liststatus(0,1,'<?=$row['user_id'];?>')">
                                                    <img src="/weixinpl/common/images_V6.0/operating_icon/icon08.png" align="absmiddle" alt="拉黑该用户" title="拉黑该用户"></a>
                                        <?php }else{?>
                                            <a onclick="liststatus(1,0,'<?=$row['user_id'];?>')">
                                                    <img src="/weixinpl/common/images_V6.0/operating_icon/icon07.png" align="absmiddle" alt="取消拉黑该用户" title="取消拉黑该用户"></a>
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
		    <!--订单管理代码结束-->
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	<!--内容框架结束-->
</body>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script><!--添加时间插件-->
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript">

var batchcode = $("#batchcode").val();//订单号
var phone = $("#phone").val();//电话号码
var user_id = $("#user_id").val();//用户ID
var stock_code =$("#stock_code").val();//股票代码
var capital = $("#capital").val();//名义本金
var status = <?php echo is_numeric(trim($_GET['status']))? $_GET['status'] : "''";?>;
var search_time_type = <?php echo $_GET['search_time_type']?$_GET['search_time_type']:"''";?>;

var start_time = $("#start_time").val();//开始时间
var end_time = $("#end_time").val();//结束时间
var param = "";
if(batchcode!=""){
	param += "&batchcode="+batchcode;
}
if(phone!=""){
	param += "&phone="+phone;
}
if(user_id!=""){
	param += "&user_id="+user_id;
}
if(stock_code!=""){
	param += "&stock_code="+stock_code;
}
if(capital!=""){
	param += "&capital="+capital;
}

if(status != '' )
{
	param += "&status="+status;

}

if(search_time_type!=""){
	param += "&search_time_type="+search_time_type;
}
if(start_time!=""){
	param += "&start_time="+start_time;
}
if(end_time!=""){
	param += "&end_time="+end_time;
}
//分页 start
var pagenum = <?php echo $pageNum ?>;//当前页
var count =<?php echo $pageCount ?>;//总页数	
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
	var url="/mshop/admin/index.php?m=qiquan&a=qiquan_order_list&pagenum="+p+param;	
	location.href = url;
   }
});

if(status != '')
{
	$('#status').find('option').each(function(){
		if($(this).val() == status)
		{
			$(this).attr('selected','selected');
		}
	});
}
if(search_time_type != '')
{
	$('#search_time_type').find('option').each(function(){
		if($(this).val() == search_time_type)
		{
			$(this).attr('selected','selected');
		}
	});
}



function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=qiquan&a=qiquan_order_list&pagenum="+a+param;

		location.href = url;
	}
}
//分页 end


//限制输入数字START
function num(obj){
obj.value = obj.value.replace(/[^\d]/g,""); //清除"数字"和"."以外的字符
}
//限制输入数字END


function check_time()
{
	var start_time = $('#start_time').val();
	var end_time = $('#end_time').val();
	var start_Time = Date.parse(new Date(start_time));
	var end_Time = Date.parse(new Date(end_time));
	if(start_Time > end_Time )
	{
		alert('开始时间不能大于结束时间');
		return false;
	}else
	{
		return true;
	}
}
//重置按钮
$(".reset").click(function(){
	var url="/mshop/admin/index.php?m=qiquan&a=qiquan_order_list";
	location.href = url;
});


function liststatus(is_blacklist,status,user_id){
	if(is_blacklist == 0){
		if(confirm('是否确认拉入黑名单') == false) return false;
	}else{
		if(confirm('是否确认取消黑名单') == false) return false;
	}
    var url = '/mshop/admin/index.php?m=qiquan&a=blacklist&customer_id=' + '<?php echo $customer_id_en?>';
    $.ajax({
        type: "POST",
        url: url,
        data: {
            'user_id': user_id,
            'status' : status
        },
        dataType: "json",
        success: function(res) {
            console.log(res);
            if (res.errcode == 0) {
                window.location.reload();
            } else {
                alert(res.errmsg);
                //alert(object.attr('style'));
            }
        }
    })
}
</script>
</html>