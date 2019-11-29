<style type="text/css">
.WSY_column_header1{height:38px;background-color:#f4f4f4;border-bottom:solid 1px #d8d8d8;border-radius:2px 2px 0 0;margin-bottom:4px;}
.WSY_column_header1 .WSY_columnnav1 ul{font-size:0;}
.WSY_column_header1 .WSY_columnnav1 ul li{box-sizing:border-box;}
.WSY_column_header1 .WSY_columnnav1 ul>li{display:inline-block;position:relative;width:150px;text-align:center;height:100%;line-height:38px;}
.WSY_column_header1 .WSY_columnnav1 ul>li:hover{}
.WSY_column_header1 .WSY_columnnav1 ul>li ol{position:absolute;top:37px;background-color:#fff;display:none;width:100%;border-bottom: 2px solid #06A7E1;padding:0;z-index:9999;}
.WSY_column_header1 .WSY_columnnav1 a{display:block;width:100%;box-sizing:border-box;color:#646464;font-size:14px;}
.WSY_column_header1 .WSY_columnnav1 ul>li ol li a:hover{background-color:#f2f2f2;}
.active{background-color:#fff;border-bottom: 2px solid #06A7E1;}
</style>
<div class="WSY_column_header1">
	<div class="WSY_columnnav1">
		<ul>			
			<li value="0"><a href="/mshop/admin/index.php?m=brandsubscribe&a=activity_management">活动管理</a></li>
			<!-- <li value="1"><a href="/mshop/admin/index.php?m=exchange&a=ex_exchange">添加活动</a></li>			 -->
			<li value="1"><a href="/mshop/admin/index.php?m=brandsubscribe&a=activity_list">活动概况</a></li>			
		</ul>
	</div>
</div>
<script>
var head = <?php echo $head; ?>;
// alert(head);
$(".WSY_columnnav1 ul>li").eq(head).addClass('active WSY-skin-bd');

$(".WSY_columnnav1 ul>li").hover(function(){		
		$(this).addClass('active WSY-skin-bd');
		$(this).find("ol").stop().slideDown(300)
},function(){
		if($(this).val()!=head){
			$(this).removeClass('active WSY-skin-bd');
		}
		
		$(this).find("ol").stop().slideUp(300)
})
</script>