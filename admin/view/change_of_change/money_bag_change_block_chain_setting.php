<html>
<head>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
	<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/jquery-2.1.0.min.js"></script>
	<title>零钱转区块链积分</title>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<style>
		input[type=text],textarea{border:1px solid #ccc;}
		input[type=text]{width: 100px;height: 20px;font-size: 14px;padding-left: 3px;}
		input[type=radio]{margin-left: 30px;}
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
						<dt>开启零钱转换：</dt>
						<dd>
							<?php if($block_onoff==1 && $block_setting_onoff == 1){ ?>
								<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="set_block_onoff(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="set_block_onoff(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>																
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="set_block_onoff(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="set_block_onoff(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>						
							<?php } ?>
							<input type="hidden" id="block_onoff" value="<?php echo $block_onoff?$block_onoff:'0'; ?>" />	
						</dd>
						<dt>最低转换价格：</dt>
						<dd><input type="text" onkeyup="decimal(this)" id="min_money" value="<?php echo $min_money?$min_money:'-1';?>">（每次转出设置的金额不能低于该设置值，-1表示不限）</dd>
						<dt>转赠系数：</dt>
						<dd>
							<input type="radio" name="typed" <?php echo $type==-1||$type==NULL?'checked="checked"':''; ?> value="-1">不限
							<input type="radio" name="typed" <?php echo $type==1?'checked="checked"':''; ?> value="1">按整10
							<input type="radio" name="typed" <?php echo $type==2?'checked="checked"':''; ?> value="2">按整100
							<input type="radio" name="typed" <?php echo $type==3?'checked="checked"':''; ?> value="3">按整1000
						</dd>
						<dt>转换比例：</dt>
						<dd>零钱：区块链积分=1： <input type="text" value="<?php echo $proportion;?>" name="proportion" id="proportion" onkeyup="decimal(this,4)">（只能输入大于的0正数，保留小数点后4位）</dd>
						<dt>转换规则：</dt>
						<dd><textarea onkeyup="clearstring(this)" id="remark" rows="10" cols="50"><?php echo $remark?$remark:'';?></textarea>（限制10万字符内）</dd>
						<input type="hidden" id="op" value="<?php echo $id ==NULL?'insert':'update'; ?>">
					</dl>
				</div>
				<div class="clear"><button type="button" class="WSY_button">保存</button></div>
			</form>
			<div style="height:40px;"></div>
		</div>
	</div>
<script type="text/javascript">
$(function(){
	var block_setting_onoff = <?php echo $block_setting_onoff?$block_setting_onoff:0;?>;//区块链发放开关
	var block_onoff = <?php echo $block_onoff?$block_onoff:0;?>;
	if(block_setting_onoff == 0)
	{
		$('input').attr('disabled','disabled');
		$('textarea').attr('disabled','disabled');
		return;
	}
	if(block_onoff == 0)
	{
		$('input').attr('disabled','disabled');
		$('textarea').attr('disabled','disabled');
	}
});
//改变开关值
function set_block_onoff(obj){
	if(obj == 0)
	{
		$('input').attr('disabled','disabled');
		$('textarea').attr('disabled','disabled');
	}
	else
	{
		$('input').attr('disabled',false);
		$('textarea').attr('disabled',false);
	}
	$("#block_onoff").val(obj);
}

//参数len表示可保留的小数点位数，len为2，只保留两位小数
function decimal(t,len=2){
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
//保存
$('.WSY_button').click(function(){
	var block_onoff = $('#block_onoff').val();
	var min_money = $('#min_money').val();
	var remark = $('#remark').val();
	var op     = $('#op').val();
	var proportion = $('#proportion').val();
	var type   = $('input[name="typed"]:checked').val();
	if(min_money == '')
	{
		alert('最低转换价格不能为空');
		return;
	}
	if(proportion == '')
	{
		alert('转换比例不能为空');
		return;
	}
	if(proportion <= 0 )
	{
		alert('最低转换价格不能小于0');
		return;
	}

	if(remark == '')
	{
		alert('转换规则不能为空');
		return;
	}
	if(remark.length>100000)
	{
		alert('转赠说明不能超过100000个字符');
	}
	$.ajax({
		type:'post',
		url:'/mshop/admin/index.php?m=change_of_change&a=save_change_block_chian_setting',
		async:false, 
		dataType:'json',
		data:{
			block_onoff:block_onoff,
			min_money:min_money,
			remark:remark,
			proportion:proportion,
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
//开按钮的点击事件
$(document).on('click','.WSY_bot',function(){
	var block_setting_onoff = <?php echo $block_setting_onoff;?>;//区块链发放开关
	if(block_setting_onoff == 1)
	{
		$(this).animate({left : '30px'});
		$(this).parent().find(".WSY_bot2").animate({left : '30px'});
		$(this).hide();
		$(this).parent().find(".WSY_bot2").show();
		$(this).parent().find("p").animate({margin : '0 0 0 13px'}, 500);
		
		$(this).parent().find("p").html('关');
		$(this).parent().css({backgroundColor : '#cbd2d8'});
		$(this).parent().find("p").css({color : '#7f8a97'});
	}else
	{
		alert('区块链发放设置已关闭，请打开后再设置');
	}
});

//关按钮的点击事件
$(document).on('click','.WSY_bot2',function(){
	$(this).parent().find(".WSY_bot").animate({left : '0px'});
	$(this).animate({left : '0px'});
	$(this).parent().find(".WSY_bot").show();
	$(this).hide();
	$(this).parent().find("p").animate({margin : '0 0 0 27px'}, 500);

	$(this).parent().find("p").html('开');
	$(this).parent().css({backgroundColor : '#ff7170'});
	$(this).parent().find("p").css({color : '#fff'});
});
</script>
</body>
</html>