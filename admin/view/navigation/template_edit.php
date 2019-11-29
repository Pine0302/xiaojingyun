<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>编辑模板</title>
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="/weixinpl/css/inside.css" media="all">
<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/inside.js"></script>
<script>
	
</script>


</head>
<style>
.WSY_stylebox a{display:block;text-align:center;font-size:16px;font-family:'微软雅黑';line-height:22px;background-color:#f2f2f2;color:#323232}
a.hover-blue:hover{background-color:#06a7e1;}
a.cur{background-color:#06a7e1;}
</style>
<body>

	<div class="WSY_content">
		<div class="WSY_columnbox">
				<div class="WSY_data">
					<div class="WSY_list" id="WSY_list" >

						<ul class="WSY_righticon">
	                        <li><a href="javascript:;" onclick="javascript :history.back(-1);">返回</a></li>
	                    </ul>
	                    <br class="WSY_clearfloat">
	                    <div>
							<dl class="WSY_bulkbox"> 
					        	<dt>模板名称：</dt>
								<dd><input type="text" value="<?php echo $template['name']; ?>" name="name"  placeholder="60个字以内"></dd>		
					        </dl>
				        </div>
				        <div>
                            <?php if($template['is_shelve'] == 1){ ?>
							<dl class="WSY_bulkbox">
					        	<dt>放置位置：</dt>
					        	<dt><?php if($template['position']==1) echo "固定右侧悬浮"; else echo "固定左侧悬浮"; ?></dt>
					        </dl>
                            <?php }else{ ?>
                            <dl class="WSY_bulkbox">
                                <dt>放置位置：</dt>
                                <span><input type="radio" value="1" name="position" <?php if($template['position']== 1){ echo 'checked'; }?>>固定右侧悬浮</span>
                                <span><input type="radio" value="2" name="position" <?php if($template['position']!= 1){ echo 'checked'; }?>>固定左侧悬浮</span>
                            </dl>
                            <?php } ?>
				        </div>
				        <div class="WSY_stylebox" id="skin">
				        <?php if($template['style']==1){ ?>
							<a class="common_template " type="1"  href="javascript:;">
									<div class="item" type="1">
										<div class="img">
											<img src="/weixinpl/back_newshops/Base/personalization/images/daohang7.jpg">
										</div>
										<div class="title" id="bian">风格1(建议图标尺寸:160px*160px)</div>
									</div>
							</a>
						<?php } ?>
						<?php if($template['style']==2){ ?>
							<a class="common_template " type="2"  href="javascript:;">
									<div class="item" type="2">
										<div class="img">
											<img src="/weixinpl/back_newshops/Base/personalization/images/daohang2.jpg">
										</div>
										<div class="title">风格2(建议图标尺寸:160px*160px)</div>
									</div>
							</a>
						<?php } ?>
						<?php if($template['style']==3){ ?>
							<a class="common_template " type="3"  href="javascript:;">
									<div class="item" type="3">
										<div class="img">
											<img src="/weixinpl/back_newshops/Base/personalization/images/daohang3.jpg">
										</div>
										<div class="title">风格3(建议图标尺寸:120px*160px)</div>
									</div>
							</a>
						<?php } ?>
						<?php if($template['style']==4){ ?>
							<a class="common_template " type="4"  href="javascript:;">
									<div class="item" type="4">
										<div class="img">
											<img src="/weixinpl/back_newshops/Base/personalization/images/daohang4.jpg">
										</div>
										<div class="title">风格4(建议图标尺寸:320px*120px)</div>
									</div>
							</a>
						<?php } ?>
						<?php if($template['style']==5){ ?>
							<a class="common_template " type="5"  href="javascript:;">
									<div class="item" type="5">
										<div class="img">
											<img src="/weixinpl/back_newshops/Base/personalization/images/daohang5.jpg">
										</div>
										<div class="title">风格5(建议图标尺寸:160px*160px)</div>
									</div>
							</a>
						<?php } ?>
						<?php if($template['style']==6){ ?>
							<a class="common_template " type="6"  href="javascript:;">
									<div class="item" type="6">
										<div class="img">
											<img src="/weixinpl/back_newshops/Base/personalization/images/daohang6.jpg">
										</div>
										<div class="title">风格6(建议图标尺寸:160px*160px)</div>
									</div>
							</a>
						<?php } ?>
						</div>
					</div>
				</div>
				<div class="WSY_text_input01"><div class="WSY_text_input"><input type="submit" class="WSY_button" value="保存" id="submit"></div></div>
				<div style="width:100%;height:20px;"></div>
		</div>
	</div>
<script type="text/javascript" src="/weixinpl/common/js_V6.0/content.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script>
	$(function(){
		$("#submit").click(function(){
			var name=$("input[name='name']").val();
			var customer_id_en = '<?php echo $customer_id_en; ?>';
            var position=$("input[name='position']:checked").val();
            if(typeof(position) == "undefined" || position == null )
			{
				var position = <?php echo $template['position']; ?>;
			}
			if(name.length==0){
				layer.alert("请输入模板名称");
			}
			else if(name.length>60){
				layer.alert("模板名称必须60个字内");
			}
			else{
				var id=<?php echo $id; ?>;
				$.post("/mshop/admin/index.php?m=navigation&a=template_edit",{id:id,name:name,position:position},function(result){
				   if(result.errcode==0){
				   	layer.alert(result.errmsg,function(){
//				   		history.go(0);
                        window.location.href='/mshop/admin/index.php?m=navigation&a=template_list&customer_id='+customer_id_en;
				   	});
				   }else{
				   	layer.alert(result.errmsg);
				   }
				},'json');
			}
		})
	});
</script>
</body>
</html>