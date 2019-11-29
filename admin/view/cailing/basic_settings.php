<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>彩铃订购－基本设置</title>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
	<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
	<script type="text/javascript" src="/weixinpl/common/js_V6.0/content.js"></script>
	    <style>
        i{display: inline-block;}
		.page-main{background-color:#ff8430;padding:0 10px;}
		.page-main .page-top{text-align:right;font-size:0;padding:15px 0;}
		.page-main .page-top>a{font-size:13px;color:#fff;padding:0 9px 0 9px;border-right:solid 1px #e6e6e6;cursor:default}
		.page-main .page-top>a:last-child{border-right:0;}
		.page-center{display:flex;justify-content:space-between;align-items:center;}
		.page-center .head-img{width:96px;height:96px;border-radius:50%;border:solid 1px #d2d2d2;overflow:hidden;}
		.page-center .head-img img{width: 100%;}
		.page-center .text-box{width:calc(100% - 96px);box-sizing:border-box;padding-left:15px;}
		.page-center .text-box .name{font-size:18px;color:#fff;}
		.page-center .text-box .name .label{font-size:9px;color:#fff;border-radius:15px;background:-webkit-linear-gradient(left,#727790,#42475d);background: linear-gradient(to right,#727790,#42475d);padding:2px 5px;line-height:1;margin:-2px 0 0 5px;display:inline-block;vertical-align:middle;}
		.page-center .text-box .weixin{font-size:13px;color:#fff;margin:5px 0 15px 0;}
		.page-center .text-box .group{display:flex;align-items:flex-start;font-size:12px;margin-top:5px;}
		.page-center .text-box .group>label{color:#fff;white-space:nowrap;}
		.page-center .text-box .group>div{color:#fff;word-break:break-all;}
		.page-center .text-box .group .brief{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden;}
		.page-center .text-box .group .address{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
		.control-main{display:flex;justify-content:space-between;align-items:flex-end;margin-top:10px;padding-bottom:15px;border-bottom:dashed 1px #e1e1e1;}
		.control-main .left{width:96px;box-sizing:border-box;padding:0 5px;}
		.control-main .left>button{color:#fff;border:solid 1px #fff;background:none;width:100%;height:30px;line-height:1;border-radius:4px;font-size:14px;}
		.control-main .right{width:calc(100% - 96px);display:flex;justify-content:center;align-items:center;box-sizing:border-box;padding-left:15px;}
		.control-main .right .list{width:33.3333%;text-align:center;box-sizing:border-box;border-right:solid 1px #e1e1e1;}
		.control-main .right .list:last-child{border-right:0;}
		.order-tips{display:flex;flex-wrap:wrap;padding:5px 0 15px 0;}
		.order-tips span{width:60px;background-color:#f8f8f8;line-height:23px;border-radius:3px;text-align:center;font-size:12px;color:#888;margin-right:calc(33.3333% - 80px);margin-top:10px;}
		.order-tips span:nth-child(4n){margin-right:0;}
		<?php if($res['card_show_but'] == 0 ) { $displayType = 'none';} else { $displayType = 'block';}?>
		.display{display: <?php echo $displayType; ?>; }
		.radio-main{font-size:0;}
		.radio-main .radio-list{display:inline-block;vertical-align:middle;}
		.radio-main .radio-list>*{display:inline-block;vertical-align:middle;}
		.radio-main .radio-list input{margin-top:2px;}
		.radio-main .radio-list label{margin:0 10px 0 5px;}
    </style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
	    <!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
               <!--  <div class="WSY_columnnav">
                    <a class="white1">基本设置</a>
                </div>   -->
                <?php 
					$head = 0;
					include("cailing_head.php");
				?>
            </div>

		<div class="WSY_remind_main">
            <dl class="WSY_bulkdl">
                <dt>手机短信验证</dt>
				<dd>
	                <?php if($res['phone_check_but']==1){ ?>
	                <ul class="switch-radio" style="background-color: rgb(255, 113, 112);margin-top:2px;">
	                    <p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
	                    <li onclick="phone_check_but(0)" class="WSY_bot" style="left: 0px;"></li>
	                    <span onclick="phone_check_but(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
	                </ul>
	                <?php }else{ ?>
	                <ul class="switch-radio" style="background-color: rgb(203, 210, 216);margin-top:2px;">
	                    <p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
	                    <li onclick="phone_check_but(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
	                    <span onclick="phone_check_but(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
	                </ul>
	                <?php } ?>
				</dd>
				<input type="hidden" name="phone_check_but" id="phone_check_but" value="<?php echo $res['phone_check_but']; ?>" />
            </dl>
            <dl class="WSY_bulkdl">
                <dt>推广员名片</dt>
                <dd>
	                <?php if($res['card_show_but']==1){ ?>
	                <ul class="switch-radio" style="background-color: rgb(255, 113, 112);margin-top:2px;">
	                    <p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
	                    <li onclick="card_show_but(0)" class="WSY_bot" id="WSY_bot" style="left: 0px;"></li>
	                    <span onclick="card_show_but(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
	                </ul>
	                <?php }else{ ?>
	                <ul class="switch-radio" style="background-color: rgb(203, 210, 216);margin-top:2px;">
	                    <p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
	                    <li onclick="card_show_but(0)" class="WSY_bot" id="WSY_bot" style="display: none; left: 30px;"></li>
	                    <span onclick="card_show_but(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
	                </ul>
	                <?php } ?>
	                <dd style="margin-top:5px;">
	                	<img style="width:15px;vertical-align:middle;" id="hint" src="/mshop/admin/Common/images/Base/help.png">
	                	<a href="#" style="display:inline-block;vertical-align:middle;" onClick="showCard('<?php echo $res['card_show_but']; ?>')">设置名片规则</a>
	                </dd>
                </dd>
                <input type="hidden" name="card_show_but" id="card_show_but" value="<?php echo $res['card_show_but']; ?>" />
            </dl>
           
			<dl class="WSY_bulkdl display" style="margin-top:24px;"> 
				<dt>名片模板</dt>
				<div class="page-main" style="float:left;width:500px;height:100%;overflow:hidden;">
					<div class="page-top">
						<a>编辑</a>
						<a>预览</a>
					</div>
					<div class="page-center">
						<div class="head-img"><img  src="./static/images/default.jpg"></div>
						<div class="text-box">
							<p class="name">欧阳啦啦<span class="label">型男</span></p>
							<p class="weixin">微信：Luo-xia@en</p>
							<div class="group">
								<label>简介：</label>
								<div class="brief">跟着我 左手右手 一个慢动作，右手左手慢动作重播, 你有没... </div>
							</div>
							<div class="group">
								<label>地址：</label>
								<div class="address">广东省深圳市龙岗区上塘龙塘新...</div>
							</div>
						</div>
					</div>
					<div class="control-main">
						<div class="left"><button type="button" class="skin-bg">+ 关注</button></div>
						<div class="right">
							<div class="list" ><img src="./static/images/icon-qq.png" width="20"></div>
							<div class="list" ><img src="./static/images/icon-weixin.png" width="26"></div>
							<div class="list" ><img src="./static/images/icon-tel.png" width="21"></div>
						</div>
					</div>
					<div class="order-tips">
						<span>推广员</span>
						<span>店铺</span>
						<span>区代</span>
						<span>合作商</span>
						<span>代理商</span>
						<span>VIP</span>
						<span>大鱼会员</span>
						<span>经销商</span>
					</div>
				</div>
			</dl>
			<dl class="WSY_bulkdl display" style="margin-top:24px;"> 
				<dt>名片位置</dt>
				<dd class="radio-main">
                    <span class="radio-list">
                        <input type="radio" class="fl" id="card_1" value="1" name="card_position" <?php if ($res['card_position']==1) {
                        ?>checked="checked" <?php } ?> ><label for="card_1">放顶部</label>
                    </span>
                    <span class="radio-list">
                        <input type="radio" class="fl" id="card_2" value="0" name="card_position" <?php if ($res['card_position']==0) {
                        ?>checked="checked" <?php } ?> ><label for="card_2">放底部</label>
                    </span>
				</dd>
			</dl>	
		    

		</div>
		<div class="submit_div" style="float:left;padding-left:30px;margin-left:auto;margin-right:auto;margin: 20px 10px 20px 300px;">
			<input type="submit" class="WSY_button" value="保存" onclick="return saveData(this);" />
		</div>

	</div>
	</div>
	<!--内容框架结束-->
</body>
<script type="text/javascript">

function phone_check_but(obj){
	$("#phone_check_but").val(obj);
}

function card_show_but(obj){
	$("#card_show_but").val(obj);
	var phone_check_but = $('#phone_check_but').val();	
	var card_show_but = $('#card_show_but').val();
	if ( card_show_but == 0 ) {
		$('.display').hide();
	} else {
		$('.display').show();
	}	
}

$('#hint').on('mouseenter', function(){
	layer.tips('提示：名片规则是整个微商城通用。','#hint',{
		area: '215px',
		time: 0
	});
});

$('#hint').on('mouseleave', function(){
	layer.tips('提示：名片规则是整个微商城通用。','#hint',{
		area: '215px',
		time: 1
	});
});

function saveData(obj){
	var phone_check_but = $('#phone_check_but').val();
	var card_show_but = $('#card_show_but').val();
	var card_position = $('input:radio[checked=checked]').val();
	if( phone_check_but != 0 && phone_check_but != 1 ){
		layer.alert('保存失败！');
		return false;
	}
	if( card_show_but != 0 && card_show_but != 1 ){
		layer.alert('保存失败！');
		return false;
	}
	if( card_position != 0 && card_position != 1 ){
		layer.alert('保存失败！');
		return false;
	}
	$.ajax({
		url:'/mshop/admin/index.php?m=cailing&a=setting_save',
		dataType: 'json',
		type:'POST',
		data:{
			'phone_check_but':phone_check_but,
			'card_show_but':card_show_but,
			'card_position':card_position
		},
		success:function(res){
			layer.alert(res.errmsg);
		} 
	});
}

function showCard(card_show_but){
    if ($('#WSY_bot').is(':hidden')){
        layer.alert("请开启推广员名片开关");	
	}else{
	layer.open({
		  type: 2,
		  area: ['1400px', '720px'],
		  fixed: false, //不固定
		  maxmin: true,
		  resize:true,
		  title: '名片规则',
		  content: '/mshop/admin/index.php?m=promoter_card&a=get_card_setting',
	});
}
}
</script>
</html>