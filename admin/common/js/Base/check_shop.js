if(shop_id<1){
	layer.confirm('您还没设置商城基本设置', {
		btn: ['跳转'] //按钮
	},function(index){
		location.href='/weixinpl/back_newshops/Base/basicdesign/base.php?customer_id='+customer_id;
		layer.close(index);
	});
}