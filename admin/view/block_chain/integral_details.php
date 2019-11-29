<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>区块链－积分明细</title>
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
		.details{width: 96%;margin-left:20px;border-radius:5px;line-height:30px;height:30px;padding:5px;}
		.details li{float: left;text-align:center;font-size: 14px;background: #f3f3f3;padding:0px 20px;}
		.details li:first-child{border-top-left-radius:5px;border-bottom-left-radius:5px;}
		.details li:last-child{border-top-right-radius:5px;border-bottom-right-radius:5px;}

	</style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
	    <!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
				<?php $keyContent = '区块链积分明细'; ?>
                <?php include_once('header.php'); ?>
			</div>
		    <!--订单列表代码开始-->
		    <div class="WSY_data">
		    	<div class="WSY_agentsbox">
					<form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=block_chain&a=integral_details">
						<input type="hidden" id="m" name="m" value="block_chain">
						<input type="hidden" id="a" name="a" value="integral_details">
						<ul class="WSY_search_q">
							<li>订单号：<input type="text" name="batchcode" id="batchcode" class="form_input" value="<?php echo $_GET['batchcode']?$_GET['batchcode']:"";?>" /></li>
							<li>用户名：<input type="text" name="user_name" id="user_name" class="form_input" value="<?php echo $_GET['user_name']?$_GET['user_name']:"";?>"/></li>
							<li>用户编码：<input type="text" name="user_id" id="user_id" class="form_input" value="<?php echo $_GET['user_id']?$_GET['user_id']:"";?>"/></li>
							<li>领取状态：<select name="status" id="status">
									<option <?php echo $_GET['status'] == -1?"selected='selected'":'';?> value="-1">请选择</option>
									<option <?php echo isset($_GET['status']) && $_GET['status'] == 0?"selected='selected'":'';?> value="0">待领取</option>
									<option <?php echo $_GET['status'] == 1?"selected='selected'":'';?> value="1">已领取</option>
									<!-- <option value="2">领取中</option> -->
								</select>
							</li>							
							<li><input type="submit" class="WSY-skin-bg form-btn search" value="搜索"></li>
							<li><input type="button" class="WSY-skin-bg form-btn explore" onclick="export_excel_email()" value="导出邮箱"></li>
							<li><input type="button" class="WSY-skin-bg form-btn explore" onclick="export_excel_local()" value="导出本地"></li>
						</ul>
					</form>
					<ul class="details"><li>已发放：<?=$reward_total?></li><li>待领取：<?=$reward_off?></li><li>已领取：<?=$reward_on?></li></ul>
		            <table width="97%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
							<th width="10%" nowrap="nowrap" align="center">用户信息</th>
							<th width="10%" nowrap="nowrap" align="center">订单号</th>
							<th width="7%" nowrap="nowrap" align="center">下单时间</th>
							<th width="7%" nowrap="nowrap" align="center">领取状态</th>
							<th width="6%" nowrap="nowrap" align="center">数量</th>
							<th width="10%" nowrap="nowrap" align="center">领取时间</th>
						</thead>
						<tbody class="tbody-main">
							<?php foreach ($data as $row){ ?>
								<tr>
									<td style="text-align:center;">（<?=$row['weixin_name']?>），<?=$row['user_id']?></td>
									<td style="text-align:center;"><?=$row['batchcode']?></td>
									<td style="text-align:center;"><?=$row['createtime']?></td>
									<td style="text-align:center;">
										<?php
											if($row['status']==0||$row['status'] == 2){
												echo '待领取';
											}
											elseif($row['status'] == 1)
											{
												echo '已领取';
											}
										?>
									</td>
									<td style="text-align:center;"><?php echo $row['reward']?></td>
									<td style="text-align:center;"><?php if($row['status'] ==1){echo $row['score_receivetime']; }?></td>
								</tr>
							<?php } ?>
						</tbody>
						
					</table>
				</div>

				
		        <!--翻页开始-->
		        <div class="WSY_page">
		        	
		        </div>
		        <!--翻页结束-->
		    </div>
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	<!--内容框架结束-->
</body>
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script>
    layer.config({
        extend: '/extend/layer.ext.js'
    });
</script>
<script type="text/javascript">

var batchcode = $("#batchcode").val();//订单号
var user_name = $("#user_name").val();//用户姓名
var user_id   = $("#user_id").val();//用户ID
var status    = $("#status").val();//类型

var param = "";
if(batchcode!=""){
	param += "&batchcode="+batchcode;
}
if(user_name!=""){
	param += "&user_name="+user_name;
}
if(user_id!=""){
	param += "&user_id="+user_id;
}
if(status!=""){
	param += "&status="+status;
	
}

//分页 start
var pagenum = <?php echo $pagenum ?>;//当前页
var count =<?php echo $pageCount ?>;//总页数	
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
	var url="/mshop/admin/index.php?m=block_chain&a=integral_details&pagenum="+p+param;	
	location.href = url;
   }
});



function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a>count) || isNaN(a)){
		layer.alert('没有下一页了');
		return false;
	}else{
		var url="/mshop/admin/index.php?m=block_chain&a=integral_details&pagenum="+a+param;

		location.href = url;
	}
}
//分页 end

//导出
function export_excel_email(){
    var name ="block_chain_integral_details_excel";
    var emails = '';
    var op     = 'iscount';
    var str = "";
    var user_id = $('#user_id').val();
    var batchcode = $('#batchcode').val();
    var user_name = $('#user_name').val();
    var status = $('#status').val();
    var customer_id = <?=$customer_id?>;

    var _obj = {user_id:user_id,batchcode:batchcode,user_name:user_name,customer_id:customer_id,status:status};
    var obj = JSON.stringify(_obj);
    $.ajax({
    	type:'post', 
    	async:false, 
    	url:'/weixinpl/common/explore/jiaoben.php',
    	data:{fields:str,
    		function_name:name,
    		param_json:obj,
    		customer_id:customer_id,
    		op:op,
    	},
        success:function(data)
        {
            var res = JSON.parse(data);
            var eamil_arr     = res.emails.split('#*#');
            var eamil_address = "";
            var type          = 2;
            var op            = 'add_email';
            var tips          = "导出数据已打包发送到您的邮箱，请注意查收";
            if(res.errcode == 10003)
            {
                layer.alert(res.errmsg);
                return;
            }
            else
            {
                type = 2;
                tips = "请留意您的邮箱，导出完成后会发到你的邮箱上！";
                layer.prompt({title: '请输入您邮箱地址',value:eamil_address, formType: 0}, function(email, prompt){
                    layer.close(prompt);
                    if (checkEmail(email)){
                        emails = email;
	                        $.ajax({
	                        	type:'post',
	                        	 async:false, 
	                        	 url:'/weixinpl/common/explore/jiaoben.php', 
	                        	 data:{fields:str,
	                        	 	function_name:name,
	                        	 	param_json:obj,
	                        	 	customer_id:customer_id,
	                        	 email:emails,
	                        	 op:op,
	                        	 type:type,
	                        	},
	                        success:function(data){
	                            var res           = JSON.parse(data);
	                            console.log(res);
	                            if(res.status == 2)
	                            {
	                                layer.alert(res.msg);
	                                return;
	                            }
	                            $.post('/weixinpl/common/explore/jiaoben.php',{'debug':1},function(da){},'json');
	                            layer.alert(tips);
	                        }
	                    });
                    }
                    else
                    {
                        layer.alert("邮箱地址填写有误，请填写正确的邮箱地址");
                        return;
                    }
                })
            }


        }
    });
}
/*校验邮箱地址*/
function checkEmail(str){
    var re= /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    return re.test(str);
}


//本地导出
function export_excel_local(){
	var url='/weixin/plat/app/index.php/Excel/block_chain_integral_details_excel/customer_id/'+'<?php echo $customer_id;?>';
	var user_id   = $('#user_id').val();
    var batchcode = $('#batchcode').val();
    var user_name = $('#user_name').val();
    var status    = $('#status').val();
	if( user_name != ''){
		url += '/user_name/'+user_name;
	}
	if( user_id != '' ){
		url += '/user_id/'+user_id;
	}
	if( batchcode != '' ){
		url += '/batchcode/'+batchcode;
	}
	if( status != '' ){
		url += '/status/'+status;
	}

	document.location = url;
}
</script>
</html>