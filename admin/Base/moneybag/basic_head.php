<style  type="text/css">
body{
	margin:0;
	font-family:'微软雅黑','Times New Roman', Times, serif;
	}
.WSY_columnbox{overflow: auto}
.navi_head{
	height:38px;
    background:#F4F4F4;
    border-bottom:1px solid #D8D8D8;
}
.navi_body{
	height:50px;
	cursor: pointer;
	transition:height ease 0.5s;
}
.headbox li{float: left;width:150px;
	text-align:center;
	font-weight:bold;
	color:#FFF;
	font-size:14px;
	vertical-align:top;}
.headbox li p{height: 38px;line-height: 38px}
.headbox li .navi_title{
	font-size:15px;
	line-height:38px;
	margin-top:0;
    color:#646464;
    font-weight:normal;
}
.navbox{background:#06a7e1;display: none;position: absolute;border-bottom:2px solid #06A7E1;z-index: 10;}
.navbox li{float: none;height: 38px;line-height: 38px;background:#FFFFFF;color:#646464;font-weight:normal;}
.navbox li:hover{background:#EBEBEB;}
.navbox li:hover a{border:none;}
.clear{clear:both;}
.navi_title:hover{background:#FFFFFF;}

</style>
<div class="navi_body">
	<div class="navi_head">
		<ul class="headbox">
		<li>
			<p class="navi_title">零钱设置</p>	
			<ul class="navbox WSY-skin-bd">
					<a href="/weixinpl/back_newshops/Base/moneybag/payset.php?customer_id=<?php echo $customer_id_en; ?>"><li>支付设置</li></a>
					<a href="/weixinpl/back_newshops/Base/moneybag/moneybag.php?customer_id=<?php echo $customer_id_en; ?>"><li>提现设置</li></a>
					<a href="/mshop/admin/index.php?m=currency&a=money_conversion&customer_id=<?php echo $customer_id_en; ?>"><li>零钱转购物币</li></a>
                    <a href="/weixinpl/back_newshops/Base/moneybag/money_to_account.php?customer_id=<?php echo $customer_id_en; ?>"><li>零钱转货款</li></a>
                    <a href="/mshop/admin/index.php?m=change_of_change&a=money_bag_change_setting&customer_id=<?php echo $customer_id_en; ?>"><li>零钱转赠</li></a>
                    <a href="/mshop/admin/index.php?m=change_of_change&a=money_bag_change_block_chain_setting&customer_id=<?php echo $customer_id_en; ?>"><li>零钱转区块链积分</li></a>
			</ul>
		</li>

		<li>
			<p class="navi_title">待提现列表</p>	
			<ul class="navbox WSY-skin-bd">
					<a href="cash_being.php?customer_id=<?php echo $customer_id_en; ?>"><li>待提现列表</li></a>	
			</ul>
		</li>

		<li>
			<p class="navi_title">零钱后台充值</p>	
			<ul class="navbox WSY-skin-bd">
					<a href="shop_set.php?customer_id=<?php echo $customer_id_en; ?>"><li>零钱后台充值</li></a>	
			</ul>
		</li>

		<li>
			<p class="navi_title">零钱日志</p>	
			<ul class="navbox WSY-skin-bd">
					<a href="recharge_log.php?customer_id=<?php echo $customer_id_en; ?>"><li>零钱日志</li></a>
<!--					<a href="https://admin.weisanyun.cn/mshop/admin/index.php?m=currency&a=conversion_log&customer_id=--><?php //echo $customer_id; ?><!--"><li>转换日志</li></a>	-->
			</ul>
		</li>
		<div class="clear"></div>
		</ul>
	</div>
</div>
<script type="text/javascript">
	$(".headbox li").hover(function(){
			$(this).find(".navbox").stop().slideDown(300)
	},function(){
			$(this).find(".navbox").stop().slideUp(300)
	})
</script>
