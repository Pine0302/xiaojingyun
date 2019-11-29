<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>基本设置</title>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
	<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
	<script type="text/javascript" src="/weixinpl/common/utility.js"></script>
</head>
<style>
.table{width:640px;table-layout: fixed;empty-cells: show;border-collapse: collapse;margin-left: 18px;margin-top: 30px;}
.table tr{font-size: 12px;color: #323232;font-family: Helvetica,"Microsoft YaHei", Arial, Helvetica, sans-serif;}
.table th{color: #fff;line-height: 30px;}
.table td{border: 1px solid #d8d8d8;padding: 0 1em 0;text-overflow: ellipsis;overflow: hidden;text-align: left !important;}

.WSY_t6 input,.WSY_t7 input{border: 1px solid #ccc;border-radius: 2px;height: 24px;margin: 0 2px 0 0;padding-left: 5px;}
.WSY_t6 input{width: 135px;float: left;}
.WSY_t7 input{width: 105px;}

</style>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<?php $keyContent = '基本设置'; ?>
                <?php include_once('reward_header.php'); ?>
			</div>

			<!--列表头部切换结束-->
			<div class="WSY_remind_main">
				<dl class="WSY_remind_dl02" style="margin-top:40px;">
					<dt style="line-height:20px;" class="WSY_left">区块链积分奖励：</dt>
					<dd>
						<?php if($setting['on_off']==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
							<li onclick="change_on_off(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="change_on_off(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>
						<?php }elseif($setting['on_off']==0 ||$setting['on_off'] == NULL ){ ?>
						<ul style="background-color: rgb(203, 210, 216);">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
							<li onclick="change_on_off(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="change_on_off(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>
						<?php } ?>
					</dd>
					<input type="hidden" id="on_off" value="<?php echo $setting['on_off']==NULL?0:$setting['on_off']; ?>"/>
					<input type="hidden" id="op" value="<?php echo $setting['on_off']==NULL?'insert':'update'; ?>" />
				</dl>
				<table width="33%" class="WSY_table" id="WSY_t1" style="min-width: 640px;">
		          <thead class="WSY_table_header">
		            <th width="33%" class="WSY_table_little">区块链积分奖金池抽取比例</th>
		          </thead>
		          <tr>
		            <td class="WSY_t6"><input <?php if($setting['on_off'] == 0||$setting['on_off']==NULL){ ?>disabled="disabled"<?php } ?> name="proportion" id="proportion" value="<?php echo number_format($setting['proportion'],4);?>" onkeyup="clearNoNum(this)" /><span style="font-size: 14px;">(0~1)“区域奖励”、“招商奖励”、“店铺奖励”、“绩效奖励”、“云店奖励”和“区块链积分奖励”的分配比例之和为1，则推广员无推广奖励</span></td>
		          </tr>
		        </table>

				<?php include_once("../../wsy_rebate/admin/Reward/public/public_pro.php"); ?>
				<div class="WSY_text_input"><button class="WSY_button subimt_save" >提交保存</button><br class="WSY_clearfloat"></div>
			</div>
		</div>
	</div>
</body>
<script>

//开按钮的点击事件
$(document).on('click','.WSY_bot',function(){
	$(this).animate({left : '30px'});
	$(this).parent().find(".WSY_bot2").animate({left : '30px'});
	$(this).hide();
	$(this).parent().find(".WSY_bot2").show();
	$(this).parent().find("p").animate({margin : '0 0 0 13px'}, 500);
	
	$(this).parent().find("p").html('关');
	$(this).parent().css({backgroundColor : '#cbd2d8'});
	$(this).parent().find("p").css({color : '#7f8a97'});
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

function change_on_off(obj)
{
	if(obj == 0)
	{
		$('#proportion').attr('disabled',true);
	}
	else if(obj == 1)
	{
		$('#proportion').attr('disabled',false);
	}
	$('#on_off').val(obj);
}

function clearNoNum(obj)
{
	//先把非数字的都替换掉，除了数字和.
	obj.value = obj.value.replace(/[^0-9.]/g,"");
	//必须保证第一个为数字而不是.
	obj.value = obj.value.replace(/^\./g,"");
	//保证.只出现一次，而不能出现两次以上
	obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
}

//保存
$(document).on('click','.subimt_save',function(){
	var proportion  = $('#proportion').val();
	var on_off      = $('#on_off').val();
	var op          = $('#op').val();
	var rule        = /^[0-1]([.]{1}[0-9]{1,4})?$/;
	if(on_off==1 && proportion=='')
	{
		alert('金额比例不能为空');
		return;
	}
	if(!rule.test(proportion))
	{
		alert('金额比例只能在0-1之间，小数点后面不能超过4位');
		return;
	}
	if(proportion < 0 || proportion > 1 )
	{
		alert('金额比例只能在0-1之间，小数点后面不能超过4位');
		return;
	}
	var attrach         = $('#attrach_investment').val();
	var shareholder_all = $('#shareholder').val();
	var globals   		= $("#globalbonus").val();
	var team_all 		= $('#team').val();
	var yundian_reward  = $('#yundian_reward').val();
	if(attrach == undefined)
	{
		attrach = 0
	}
	if(shareholder_all == undefined)
	{
		shareholder_all = 0
	}
	if(team_all == undefined)
	{
		team_all = 0
	}
	if(globals == undefined)
	{
		globals = 0
	}
	if(yundian_reward == undefined)
	{
		yundian_reward = 0
	}

	var all = parseFloat(shareholder_all)*100 + parseFloat(team_all)*100+ parseFloat(globals)*100+ parseFloat(attrach)*100+parseFloat(yundian_reward)*100+parseFloat(proportion)*100;
	if(all>100){
		alert('各级推广比例之和不得大于1');
		$('#proportion').val("");
		return;
	}
	// console.log(shareholder_all,team_all,globals,attrach,yundian_reward,proportion,all);
	// return ;




	$.ajax({
		type:'post',
		url:'/mshop/admin/index.php?m=block_chain&a=integral_reward_setting_update',
		async:false, 
		dataType:'json',
		data:{
			on_off:on_off,
			proportion:proportion,
			op:op
		},
		success:function(res){
			//console.log(res);
			if(res.errcode == 0)
			{
				location.reload();
			}
			if(res.errcode == 400)
			{
				alert(res.errmsg);
			}
		},
	})

});
</script>

</html>