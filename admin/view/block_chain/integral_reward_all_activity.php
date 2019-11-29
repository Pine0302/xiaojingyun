<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>区块链积分奖励－活动管理</title>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
	<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>

	<style type="text/css">
		.form-btn{width:auto!important;padding:0 10px!important;cursor:pointer;color:#fff;border:0!important;}
		.form-add-btn{display:inline-block;line-height:24px;border-radius:3px;}
		.table-btn{color:#fff;border:0;cursor:pointer;border-radius:3px;height:24px;padding:0 10px;font-size:12px;}
		.div_item{float:left;padding:10px;font-size:14px;}
		.div_item label{margin-left:5px;font-size:14px;}
		.div_item input{border:1px solid #ccc; border-radius: 2px;}
		.layui-layer-content button{float: left;margin-top: 56px;margin-bottom: 19px;width: 80px;height: 30px;}
		.xubox_title{background: none!important;}
		.xubox_title em{left: 0!important;text-align: center!important;width: 100%!important;}
		.WSY_previous{width: 110px!important;}
		.caozuo{height: 24px;border-radius: 3px;}
	</style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
	    <!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
				<?php $keyContent = '活动管理'; ?>
                <?php include_once('reward_header.php'); ?>
			</div>
		    <!--订单列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form style="display:block" method="get" action="/mshop/admin/index.php?m=block_chain&a=integral_reward_all_activity">
						<input type="hidden" id="m" name="m" value="block_chain">
						<input type="hidden" id="a" name="a" value="integral_reward_all_activity">
						<ul class="WSY_search_q">
							<li>活动产品名称：<input type="text" name="product_name" id="product_name" class="form_input" value="<?php echo $_GET['product_name']?$_GET['product_name']:"";?>" /></li>
							<li>活动状态：
								<select id="status" name="status">

									<option <?php echo $_GET['status']==-1?'selected=selected':'';?> value="-1">全部</option>
									<option <?php echo $_GET['status']==1?'selected=selected':'';?> value="1">未开始</option>
									<option <?php echo $_GET['status']==2?'selected=selected':'';?> value="2">进行中</option>
									<option <?php echo $_GET['status']==3?'selected=selected':'';?> value="3">已结束</option>
									<option <?php echo isset($_GET['status']) && $_GET['status']==0?'selected=selected':'';?> value="0">已删除</option>
								</select>
							</li>		 			
							<li><input type="submit" class="WSY-skin-bg form-btn" style="color:#fff;" value="搜索"></li>
							<li><input type="button" class="WSY-skin-bg form-btn" style="color:#fff;" value="导出" onclick="export_excel()"></li>

							<li><input type="button" class="WSY-skin-bg form-btn" style="color:#fff;" value="删除兑换" onclick="operate_activity('del_many')">
							</li>
						</ul> 
					</form>
		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="4%" nowrap="nowrap" align="center"><input type="checkbox" id="allselects" name="allselects"></th>
							<th width="4%" nowrap="nowrap" align="center">活动编码</th>
							<th width="8%" nowrap="nowrap" align="center">活动产品名称</th>
							<th width="4%" nowrap="nowrap" align="center">活动状态</th>
							<th width="5%" nowrap="nowrap" align="center">发行总数量</th>
							<th width="5%" nowrap="nowrap" align="center">产品金额</th>
							<th width="15%" nowrap="nowrap" align="center">活动时间</th>
							<th width="5%" nowrap="nowrap" align="center">已兑换数量</th>
							<th width="5%" nowrap="nowrap" align="center">已兑换零钱（元）</th>
							<th width="6%" nowrap="nowrap" align="center">已扣除区块链积分</th>
							<th width="5%" nowrap="nowrap" align="center">创建时间</th>
							<th width="8%" nowrap="nowrap" align="center">操作</th>
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $row){ ?>
								<tr>
									<td style="text-align:center;"><input type="checkbox" class="del_this[]" value="<?php echo $row['id'];?>" stat = "<?php echo $row['status'] == '已删除'?0:1;?>"></td>
									<td style="text-align:center;"><?php echo $row['id'];?></td>
									<td style="text-align:center;"><?php echo $row['product_name'];?></td>
									<td style="text-align:center;"><?php echo $row['status'];?></td>
									<td style="text-align:center;"><?php echo $row['product_num'];?></td>
									<td style="text-align:center;"><?php echo $row['product_price'];?></td>
									<td style="text-align:center;"><?php echo $row['begin_time'];?>至<?php echo $row['end_time'];?></td>
									<td style="text-align:center;"><?php echo $row['exchange_num'];?></td>
									<td style="text-align:center;"><?php echo $row['exchange_money'];?></td>
									<td style="text-align:center;"><?php echo $row['exchange_jf'];?></td>
									<td style="text-align:center;"><?php echo $row['createtime'];?></td>
									<td style="text-align:center;">
									<?php if($row['status'] != '已删除'){ ?>
										<button type="button" class="WSY-skin-bg form-btn caozuo" onclick="operate_activity('del_one',<?php echo $row['id'];?>)">删除</button>
									<?php }?>
										<button type="button" class="WSY-skin-bg form-btn caozuo" onclick="location.href='/mshop/admin/index.php?m=block_chain&a=integral_reward_exchange_log&bonus_id=<?php echo $row['bonus_id'];?>&activity_id=<?php echo $row['id'];?>'">兑换日志</button>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
		        <!--翻页开始-->
		        <div class="WSY_page"></div>
		        <!--翻页结束-->
		    </div>
		</div>
		<!-- <div style="width:100%;height:20px;"></div> -->
	</div>
	<!--内容框架结束-->
</body>
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript">

var product_name = $("#product_name").val();//订单号
	status       = $("#status").val();//用户姓名

var param = "";
if(product_name!=""){
	param += "&product_name="+product_name;
}
param += "&status="+status;
//分页 start
var pagenum = <?php echo $pagenum ?>;//当前页
var count =<?php echo $pageCount ?>;//总页数	
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
	var url="/mshop/admin/index.php?m=block_chain&a=integral_reward_all_activity&pagenum="+p+param;	
	location.href = url;
   }
});



function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=block_chain&a=integral_reward_all_activity&pagenum="+a+param;

		location.href = url;
	}
}
//分页 end

//导出
function export_excel(){
	var url='/weixin/plat/app/index.php/Excel/block_chain_all_activity_excel/customer_id/'+'<?php echo $customer_id;?>';
    	product_name = $('#product_name').val();
    	status       = $('#status').val();
	if( product_name != ''){
		url += '/product_name/'+product_name;
	}
	if( status != '' ){
		url += '/status/'+status;
	}
	document.location = url;
}
//删除方法
function operate_activity(op,id=''){
	var remark = "";
	remark = "删除产品兑换活动后不可恢复，继续吗";
	layer.confirm(remark, {
		title:'警告',
		btn: ['确认','取消']
	}, function(confirm){
		layer.close(confirm);	
		if(op == 'del_one')
		{
			$.ajax({
				url:'/mshop/admin/index.php?m=block_chain&a=integral_reward_del',
				type:'post',
				dataType:'json',
				data:{
					op:op,
					activity_id:id,
				},
				async:false,
				success:function(res){
					if(res.errcode == 0)
					{
						alert(res.errmsg);
						location.reload();
					}
					else
					{
						alert(res.errmsg);
					}
				},
			});
		}
		else if(op == 'del_many')
		{
			var id_arr = [];
			$(".tbody-main :checkbox").each(function(){
				if($(this).attr('checked') && $(this).attr('stat') == 1)
				{
					id_arr.push($(this).val());
				}
			});
			if(id_arr != '')
			{
				$.ajax({
				url:'/mshop/admin/index.php?m=block_chain&a=integral_reward_del',
				type:'post',
				dataType:'json',
				data:{
					op:op,
					activity_id:id_arr,
				},
				async:false,
				success:function(res){
					console.log(res);
					if(res.errcode == 0)
					{
						alert(res.errmsg);
						location.reload();
					}
					else
					{
						alert(res.errmsg);
					}
				},
			});
			}else
			{
				alert('请选择删除哪个活动');
			}
		}
	}, function(){

	});
}
// 当页全选
$("#allselects").click(function(){    
    if ($(this).is(':checked')){    
        $(".tbody-main :checkbox").prop("checked","checked");   
    }else{    
        $(".tbody-main :checkbox").attr("checked",false); 
    }    
});
</script>
</html>