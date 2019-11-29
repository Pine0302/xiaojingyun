<div class="WSY_column_header">
	<div class="WSY_columnnav">
		<input type="text" value="<?php echo $INDEX; ?>" id="index" style="display:none"/>
		<a >订单明细</a>
		<a >销售统计</a>
		<a >商品明细</a>		
		<a >属性设置</a>
		<a >商品添加</a>
		<a style="float:right">辅助设置</a>		
        <span class="WSY_preview1" style="background-color:red;border-color:red;margin-top:9px;">此功能已停止维护更新，如有疑问，请咨询相关客服</span>	
	</div>
</div>
<script>
$(function(){
	 var index=$("#index").val();
	 if(index==null || index==""){
		 index=0;
	 }
	 $(".kst").removeClass("white1");
	 $(".WSY_columnnav a").eq(index).addClass("white1");
});
$('.WSY_columnnav a').click(function(){
    //比如点击了第2个span，怎么写输出它在.banner_pagediv下处于第几个span?
    var index = $(this).index();
    $(".kst").removeClass("white1");
    $(this).addClass("white1");
    if(index==1){
    	location.href = "order.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=0&Itype=0";
    }
    if(index==2){
    	location.href = "statistics.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=1&Itype=1";
    }
    if(index==3){
    	location.href = "product.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=2&Itype=2";
    }
	 if(index==4){
    	location.href = "add_sx.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=3&Itype=3";
    }
	if(index==5){
    	location.href = "add_pro.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=4&Itype=4";
    }
	if(index==6){
    	location.href = "set_type.php?customer_id=<?php echo $customer_id_en; ?>&INDEX=5&Itype=5";
    }

});
</script>
