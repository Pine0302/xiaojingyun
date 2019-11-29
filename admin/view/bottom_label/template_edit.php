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
					        	<dt><?php if($template['position']==2) echo "固定不随页面移动"; else echo "随页面移动而移动"; ?></dt>
					        </dl>
                            <?php }else{ ?>
                            <dl class="WSY_bulkbox">
                                <dt>放置位置：</dt>
                                <span><input type="radio" value="2" name="position" <?php if($template['position']== 2){ echo 'checked'; }?>>固定不随页面移动</span>
                                <span><input type="radio" value="1" name="position" <?php if($template['position']== 1){ echo 'checked'; }?>>随页面移动而移动</span>
                            </dl>
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
				$.post("/mshop/admin/index.php?m=bottom_label&a=template_edit",{id:id,name:name,position:position},function(result){
				   if(result.errcode==0){
				   	layer.alert(result.errmsg,function(){
//				   		history.go(0);
                        window.location.href='/mshop/admin/index.php?m=bottom_label&a=template_list&customer_id='+customer_id_en;
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