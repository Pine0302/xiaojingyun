<html>
<head>
<meta charset="utf-8">
<title>区块链积分－发放</title>
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
<link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="/weixinpl/common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="/weixinpl/common/utility.js"></script>


<style type="text/css">

.button1 {
float: none;
margin-top: 30px;
margin-bottom: 30px;
margin-left:  60px;
}
.button2 {
float: none;
margin-top: 30px;
margin-bottom: 30px;
margin-left:  5px;

}
dt{
	float: left;
}
input[type=radio]{margin-right: 5px; }
input[type=text]{width: 200px;height: 25px;border: 1px solid #ccc;margin-top: 2px;padding-left: 5px;}

</style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
		<!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
				<?php $keyContent = '区块链积分发放'; ?>
				<?php include_once('header.php'); ?>
			</div>
			<!--积分发放设置列表代码开始-->
				<div class="WSY_remind_main">
					<div class="divfloat">
						<input type="hidden" name="id" id="id" value="<?php echo $id?>">
						<dl class="WSY_remind_dl02"  >
							<dt>区块链积分发放：</dt>
							<dd>
								<?php if($on_off == 1){ ?> 
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="change_is_block(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="change_is_block(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
								<?php }else { ?> 
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="change_is_block(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="change_is_block(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>						
								<?php } ?> 
							</dd>
							<input type="hidden" name="on_off" id="on_off" value="<?php echo $on_off; ?>" />
						</dl>
						<!-- <dl class="WSY_remind_dl02 block_chain_gene"  >
							<dt>同步区块链系统用户基因：</dt>
							<dd>
								<?php if($block_chain_gene == 1){ ?> 
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="deletePromoter(0)" class="WSY_bot" id="WSY_bot" style="left: 0px;"></li>
									<span onclick="deletePromoter(1)" class="WSY_bot2" id="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>
								<?php }else { ?> 
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="deletePromoter(0)" class="WSY_bot" id="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="deletePromoter(1)" class="WSY_bot2" id="WSY_bot2" style="display: block; left: 30px;"></span> 
								</ul>						
								<?php } ?> 
							</dd>
							<input type="hidden" name="block_chain_gene" id="block_chain_gene" value="<?php echo $block_chain_gene; ?>" />
						</dl> -->
						<dl class="WSY_remind_dl02"  >
						<dt>发放账号：</dt>
						<dd>
						<input type ="text"  name="appid" id="appid" maxlength="100" value="<?php echo $appid;?>" style="width:200px;height:25px;border:1px solid #ccc;margin-top:2px;" <?php if($on_off==0){echo 'disabled="disabled"';}?> />
						<img src="/weixinpl/Base/common/images/pay_set/icon.jpg" onMouseOver="toolTip('账号与密钥请在区块链后台的第三方管理获取对应参数')" onMouseOut="toolTip()" style="margin-right:5px" />
						</dd>	
						</dl>
						<dl class="WSY_remind_dl02"  >
						<dt>发放密钥：</dt>
						<dd>
						<input type ="text" name="appsecret" id="appsecret" maxlength="100" value="<?php echo $screet;?>" style="width:200px;height:25px;border:1px solid #ccc;margin-top:2px;" <?php if($on_off==0){echo 'disabled="disabled"';}?> />
						</dd>
						</dl>
						<dl class="WSY_remind_dl02"  >
							<dt>域名地址：</dt>
							<dd>
								<input type ="text" name="url" id="url" maxlength="100" value="<?php echo $url;?>" style="width:200px;height:25px;border:1px solid #ccc;margin-top:2px;" <?php if($on_off==0){echo 'disabled="disabled"';}?> />
								<span style="color: blue; cursor:pointer" onclick="curl_request()" >&nbsp;&nbsp;&nbsp;测试</span>
							</dd>
						</dl>
						<dl class="WSY_remind_dl02"  >
							<dt>发放设置：</dt>
							<dd style="font-size: 14px;padding-bottom: 10px;">
								产品现价/商家订单实付*比例（%）
								<input type="text" name="block_chain_bfb" id="block_chain_bfb" <?php if($on_off==0){echo 'disabled="disabled"';}?> value="<?php echo $block_chain_bfb!=NULL?$block_chain_bfb:'0.0000';?>">
							</dd>
<!-- 							<dd style="font-size: 14px;padding-bottom: 10px;margin-left: 80px;">
								<input type ="radio" name="block_chain_type" <?php// echo $block_chain_type==2?'checked="checked"':''; ?> <?php// if($on_off==0){echo 'disabled="disabled"';}?> value="2" />固定金额（￥）
								<input type="text" name="block_chain_money" id="block_chain_money" <?php// if($on_off==0){echo 'disabled="disabled"';}?> value="<?php// echo $block_chain_money;?>">
							</dd> -->
						</dl>


						<dl class="WSY_remind_dl02" id="name" >
						<dt>显示名称：</dt>
						<dd>
						<input type ="text" name="name" id="name1" maxlength="100" value="<?php echo $name;?>" style="width:200px;height:25px;border:1px solid #ccc;margin-top:2px; " <?php if($on_off==0){echo 'disabled="disabled"';}?> />
						</dd>
						</dl>

                        <dl class="WSY_remind_dl02"  id="valid_day"> 
						<dt>有 效 期 ：</dt>
						<dd>
						<input type ="text" maxlength="100" name="valid_day" id="valid_day1" value="<?php echo $valid_day?>" style="width:200px;height:25px;border:1px solid #ccc;margin-top:2px;" <?php if($on_off==0){echo 'disabled="disabled"';}?> />天
						</dd>
						</dl>
						
					</div>
				</div>
            <div class="submit_div" >
				<input type="button" class="WSY_button button1" name="submit" value="提交"  style="cursor:pointer;">
				<input type="button" class="WSY_button button2" value="取消" onclick="javascript:history.go(-1);"/>
			</div>
        </div>		
    </div>	 					

</body>
<script type="text/javascript" src="/weixinpl/common/js/ToolTip.js"></script>  <!-- 提示框 -->
<script type="text/javascript" src="/weixinpl/common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="/weixinpl/common/js_V6.0/content.js"></script>
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script>
function change_is_block(obj){
	$("#on_off").val(obj);//区块链积分是否开启发放
	if(obj==0){

		$("input:text").attr("disabled",true);
		$("input:radio").attr("disabled",true); 
	}else{

		$("input:text").attr("disabled",false);
		$("input:radio").attr("disabled",false); 
	}
}


$("input[name='submit']").click(function(){

    var name              = $("#name1").val();
        valid_day         = $("#valid_day1").val();
        on_off            = $("#on_off").val();
        url               = $("#url").val();
        appsecret         = $("#appsecret").val();
        appid             = $("#appid").val();
        id                = $("#id").val();
        block_chain_bfb   = $("#block_chain_bfb").val();
        // block_chain_money = $("#block_chain_money").val();
        block_chain_type  = $('input[name="block_chain_type"]:checked').val();
        block_chain_gene  = $("#block_chain_gene").val();

    if(appid ==""){
    	alert('发放账号不能为空');
    }else if(appsecret ==""){
    	alert('发放密钥不能为空');
    }else if(url ==""){
    	alert('域名地址不能为空');
    }else if(name ==""){
    	alert('显示名称不能为空');
    }else if(block_chain_type==""){
    	alert('发放设置类型不能为空');
    }else if(block_chain_bfb == '' && block_chain_type == 1){
    	alert('发放设置百分比不能为空');
  //   }
  //   else if(block_chain_money == '' && block_chain_type == 2){
		// alert('发放设置固定金额不能为空');
    }else if(valid_day > 99999){
    	alert('有效期不能大于99999');
    }else{
	    $.ajax({
	        url:'index.php?m=block_chain&a=integral_grant_update',
	        type:"POST",
	        data:{ 'id':id,'name':name,'valid_day':valid_day,'on_off':on_off,'url':url,'appsecret':appsecret,'appid':appid,'block_chain_bfb':block_chain_bfb,'block_chain_type':block_chain_type,'block_chain_gene':block_chain_gene},
	        dataType:"json",
	        success:function(data){
	        	console.log(data) 
	        	alert('保存成功');
	            window.location.href = 'index.php?m=block_chain&a=integral_grant';
	        }
	    });
	}
});

function curl_request(){
	var appid     = $('#appid').val();
	var appsecret = $('#appsecret').val();
	var on_off    = $("#on_off").val();
	var url       = $("#url").val();

	if(on_off == 0){
		alert('请先开启积分发放');

	}else{

	if(appid == ""){
  		alert('区块链账户不能为空');
    }else if(appsecret == ""){
   		alert('密钥不能为空');
   	}else if(url ==""){
   		alert('域名地址不能为空');
   	}

	 $.ajax({
            type: "post",
            url: "index.php?m=block_chain&a=check_integral",
            data: {
                'appid':appid,
                'appsecret':appsecret,
                'url':url
            },
            dataType: "json",
            success: function(res) {
                if(res.access_token !=null){
					alert('对接成功');
                }else{
                	alert('对接失败，请检查信息是否正确？');
                }	
            },
        });
	}
}	

//强制更改基因开关
function deletePromoter(num){
	var content = "开启同步区块链系统用户基因开关会自动开启强制绑定手机号开关！确定开启吗？";
	var _index  = "WSY_bot";
	if (num == 0) 
	{
		content = "关闭同步区块链系统用户基因开关，请自行前往商城设置关闭强制绑定手机号开关！确定关闭吗？"; 
		_index  = 'WSY_bot2';
	}
	layer.confirm(content, {
		btn: ['确定','取消']
	}, function(confirm){
		layer.close(confirm);
		$("#block_chain_gene").val(num);
	}, function(){
		document.getElementById(_index).click();
		layer.msg('已取消', {
			time: 4000,
			btn: ['确认'],
			icon:1
		});
	});
}
</script>
</html>
