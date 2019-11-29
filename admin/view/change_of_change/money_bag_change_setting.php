<html>
<head>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
	<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/jquery-2.1.0.min.js"></script>
	<title>零钱转赠</title>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<style>
		input[type=text],textarea{border:1px solid #ccc;}
		input[type=text]{width: 100px;height: 20px;font-size: 14px;padding-left: 3px;}
		input[type=checkbox]{margin:0px 10px;}
		.WSY_remind_dl02 dd,dt{margin-bottom:30px;}
		.WSY_remind_dl02 dd {display: block;margin-right: 10px;line-height: 20px;}
		.WSY_button {float: none;margin-left: 600px;}
	</style>
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/moneybag/basic_head.php"); 
		?>		
	<form action="" method="post" id="saveFrom">
		<div class="WSY_remind_main">
			<dl class="WSY_remind_dl02"> 
				<dt>零钱转赠：</dt>
				<dd>
					<?php if($transfer_onoff==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="set_transfer_onoff(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="set_transfer_onoff(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="set_transfer_onoff(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="set_transfer_onoff(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" id="transfer_onoff" value="<?php echo $transfer_onoff?$transfer_onoff:'0'; ?>" />	
				</dd>
				<dt>转赠条件：</dt>
				<dd>最低留存金额￥ <input type="text" onkeyup="decimal(this)" id="min_money" value="<?php echo $min_money?$min_money:'-1';?>">（意思是零钱余额不能低于该设置值，-1表示不限制）</dd>
				<dt>转赠方式：</dt>
				<dd><input type="checkbox"  class="typed" <?php echo $type[0] == 1?"checked='checked' value='1'":"value='0'";?>>一对多扫码转赠 <input type="checkbox" class="typed" <?php echo $type[1] == 1?"checked='checked' value='1'":"value='0'";?>> 一对一扫码转赠 <input type="checkbox" class="typed" <?php echo $type[2] == 1?"checked='checked' value='1'":"value='0'";?>> 输入对方ID或手机号转赠</dd>
				<dt>转赠说明：</dt>
				<dd><textarea onkeyup="clearstring(this)" id="remark" rows="10" cols="50"><?php echo $remark?$remark:'';?></textarea>（限制10万字符内）</dd>
				<input type="hidden" id="op" value="<?php echo $id ==NULL?'insert':'update'; ?>">
			</dl>
		</div>
		<div class="clear"><button type="button" class="WSY_button">保存</button></div>

	<div style="height:40px;"></div>	
<script type="text/javascript" src="/weixinpl/common/js_V6.0/content.js"></script>
<script>

//改变开关值
function set_transfer_onoff(obj){
	if(obj == 0)
	{
		$('input').attr('disabled','disabled');
		$('textarea').attr('disabled','disabled');
	}else
	{
		$('input').attr('disabled',false);
		$('textarea').attr('disabled',false);
	}
	$("#transfer_onoff").val(obj);
}

//参数len表示可保留的小数点位数，len为2，只保留两位小数
function decimal(t){
	var len = 2;
    var num = t.value;
    var reg = /^[\+\-]?\d*?\.?\d*?$/ ;
    var arr =  num.split('.'),ARR = [];
    if(reg.test(num)){
        if(arr[0] != ''){
            var integer = arr[0];
            if(arr.length > 1){
                for(var i = 1;i<arr.length;i++){
                    ARR.push(arr[i]);
                }
                var txt = ARR.join().substring(0,len);
                t.value = integer+'.'+txt ;
            }else{
                t.value = integer;
            }
        }
    }else{
        isNaN(parseFloat(num)) ? t.value ='' : t.value = parseFloat(num);
    }
}

//限制文本不能输引号
function clearstring(obj)
{
	obj.value = obj.value.replace(/[\']/g,'');
	obj.value = obj.value.replace(/[\"]/g,'');
	obj.value = obj.value.replace(/[\“]/g,'');
	obj.value = obj.value.replace(/[\”]/g,'');
	obj.value = obj.value.replace(/[\’]/g,'');
	obj.value = obj.value.replace(/[\‘]/g,'');
}
//转赠方式按钮值
$('.typed').click(function(){
	if($(this).val() == 1)
	{
		$(this).val(0);
		return;
	}
	if($(this).val() == 0)
	{
		$(this).val(1);
		return;
	}

});
//保存
$('.WSY_button').click(function(){
	var transfer_onoff = $('#transfer_onoff').val();
	var min_money = $('#min_money').val();
	var remark = $('#remark').val();
	var op     = $('#op').val();
	if(min_money == '')
	{
		alert('最低留存金额不能为空');
		return;
	}
	if(remark == '')
	{
		remark = '零钱转赠是直接把自己余额账户转赠给对方零钱账户，无法退款，请谨慎操作！';
	}
	if(remark.length>100000)
	{
		alert('转赠说明不能超过100000个字符');
		return;
	}
	var type = [];
	$('.typed').each(function(){
		type.push($(this).val());
	});
	if(transfer_onoff == 1 && type[0] == 0 && type[1] == 0 && type[2] == 0)
	{
		alert('开启零钱转赠必须勾选其中一个转赠方式');
		return;
	}
	$.ajax({
		type:'post',
		url:'/mshop/admin/index.php?m=change_of_change&a=save_money_bag_change_setting',
		async:false, 
		dataType:'json',
		data:{
			transfer_onoff:transfer_onoff,
			min_money:min_money,
			remark:remark,
			type: type,
			op:op
		},
		success:function(res){
			console.log(res);
			if(res.errcode == 0)
			{
				location.reload();
			}
			else
			{
				alert(res.errmsg);
			}
		},
		error:function()
		{

		}
	});
});
$(function(){
	var transfer_onoff = <?php echo $transfer_onoff?$transfer_onoff:'0';?>;
	if(transfer_onoff == 0)
	{
		$('input').attr('disabled','disabled');
		$('textarea').attr('disabled','disabled');
	}
});
</script>
</body>
</html>