$(function(){

	$("#mul_unsale").click(function(){
		var ckIds = $("input[name='pro_ids']:checked");
		if(ckIds.length == 0){
			alert("请先勾选要下架的商品！");
			return;
		}
		if(confirm("是否确定将选中的"+ckIds.length+"个商品下架？")){
			var idsStr = "";
			ckIds.each(function(i,n){
				//console.log(" i : "+i+" n.value : "+n.value);
				if(i > 0){
					idsStr += ",";
				}
				idsStr = idsStr + n.value;
			});
			var url = "sale.php?customer_id="+customer_id_en+"&keyid="+idsStr+"&op=unsale_m";
			location.href=url;
		}
	});
	$(".mul_property").click(function(){
		var action = $(this).data("action");
		var ckIds = $("input[name='pro_ids']:checked");
		if(ckIds.length == 0){
			if(action=="add"){
				layer.alert("请选择需要批量添加属性的记录！");
				return;
			}else{
				layer.alert("请选择需要批量删除属性的记录！");
				return;
			}			
		}
		var html = "";
		html += '<div class="div_item"><input id="pro_out" type="checkbox" name="ckp_props" value="1"><label for="check_out">下架</label></div>';
		html += '<div class="div_item"><input id="pro_new" type="checkbox" name="ckp_props" value="1"><label for="check_out">新品</label></div>';
		html += '<div class="div_item"><input id="pro_hot" type="checkbox" name="ckp_props" value="1"><label for="check_out">热卖</label></div>';
		html += '<div class="div_item"><input id="pro_issnapup" type="checkbox" name="ckp_props" value="1"><label for="check_out">抢购</label></div>';
		html += '<div class="div_item"><input id="pro_vp" type="checkbox" name="ckp_props" value="1"><label for="check_out">vp产品</label></div>';
		html += '<div class="div_item"><input id="pro_virtual" type="checkbox" name="ckp_props" value="1"><label for="check_out">虚拟产品</label></div>';
		html += '<div class="div_item"><input id="pro_currency" type="checkbox" name="ckp_props" value="1"><label for="check_out">购物币产品</label></div>';
		html += '<div class="div_item"><input id="pro_guess" type="checkbox" name="ckp_props" value="1"><label for="check_out">猜您喜欢产品</label></div>';
		html += '<div class="div_item"><input id="pro_freeshipping" type="checkbox" name="ckp_props" value="1"><label for="check_out">包邮</label></div>';
		html += '<div class="div_item"><input id="pro_score" type="checkbox" name="ckp_props" value="1"><label for="check_out">兑换专区</label></div>';
		html += '<div class="div_item"><input id="pro_limit" type="checkbox" name="ckp_props" value="1"><label for="check_out">限购</label></div>';
		html += '<div class="div_item"><input id="pro_extend" type="checkbox" name="ckp_props" value="1"><label for="check_out">首次推广奖励</label></div>';
		html += '<div class="div_item"><input id="pro_tax" type="checkbox" name="ckp_props" value="1"><label for="check_out">税收产品</label></div>';
		html += '<div class="div_item"><input id="pro_mini_mshop" type="checkbox" name="ckp_props" value="1"><label for="check_out">微信小程序</label></div>';
		
		if(action=="add"){

			html += '<div class="div_item" id="div_currency_text" style="display:none;width:90%;">返佣购物币：<input id="currency_text_input" style="height: 20px;" type="text" name="currency"/></div>';
			html += "<div class='div_item' id='issnapup_start' style='display:none;width:90%;'>抢购开始时间：<input id='issnapup_start_input' style='height: 20px;' type='text' name='issnapup_start' onclick='WdatePicker({dateFmt:"+'"yyyy-MM-dd HH:mm"'+",minDate:"+'"2015-10-25 10:00"'+",maxDate:"+'"2018-10-25 21:30"'+"});'/></div>";
			html += "<div class='div_item' id='issnapup_end' style='display:none;width:90%;'>抢购结束时间：<input id='issnapup_end_input' style='height: 20px;' type='text' name='issnapup_end' onclick='WdatePicker({dateFmt:"+'"yyyy-MM-dd HH:mm"'+",minDate:"+'"2015-10-25 10:00"'+",maxDate:"+'"2018-10-25 21:30"'+"});'/></div>";
			html += '<div class="div_item" id="div_vp_text" style="display:none;width:90%;">VP：<input id="vp_text_input" style="height: 20px;" type="text" name="vp"/></div>';
			html += '<div class="div_item" id="div_limit_text" style="display:none;width:90%;">限购数量：<input id="limit_text_input" style="height: 20px;" type="text" name="limit"/></div>';
			html += '<div class="div_item" id="div_extend_text" style="display:none;width:90%;">首次推广奖励金额：<input id="extend_text_input" style="height: 20px;" type="text" name="extend_money"/></div>';
			html += tax_html;
		}
		html += '<div style="clear:both;">';
		html += '<button style="float:none;margin-top:0;margin-left:95px;" action='+action+' onclick="saveLabel(this)">确定</button>';
		html += '<button style="float:none;margin-top:0;margin-left: 20px;" onclick="colse_layerOpen()">取消</button>';
		html += '</div>';
		if(action=="add"){
			var tips = "批量添加标签";
		}else{
			var tips = "批量删除标签";
		}
		
		//自定页
			layer.open({
			  type: 1,
			  title:tips,
			  skin: 'layui-layer-demo', //样式类名
			  closeBtn: 0, //不显示关闭按钮
			  shift: 2,
			  shadeClose: true, //开启遮罩关闭
			  content: html
			});
			if(action=="add"){
				$('#pro_issnapup').click(function(){
					if($('#pro_issnapup').prop("checked")==true){
						$('#issnapup_start').show();
						$('#issnapup_end').show();
					}else{
						$('#issnapup_start').hide();
						$('#issnapup_end').hide();
					}			
				});	
				$('#pro_vp').click(function(){
					if($('#pro_vp').prop("checked")==true){
						$('#div_vp_text').show();
					}else{
						$('#div_vp_text').hide();
					}			
				});	
				$('#pro_limit').click(function(){
					if($('#pro_limit').prop("checked")==true){
						$('#div_limit_text').show();
					}else{
						$('#div_limit_text').hide();
					}			
				});
				$('#pro_currency').click(function(){
					if($('#pro_currency').prop("checked")==true){
						$('#div_currency_text').show();
					}else{
						$('#div_currency_text').hide();
					}			
				});
				$('#pro_extend').click(function(){
					if($('#pro_extend').prop("checked")==true){
						$('#div_extend_text').show();
					}else{
						$('#div_extend_text').hide();
					}			
				});
				$('#pro_tax').click(function(){
					if($('#pro_tax').prop("checked")==true){
						$('#div_tax_text').show();
					}else{
						$('#div_tax_text').hide();
					}	
				})
			
			}	
	});
	$("#ck_all").click(function(){
		$("input[name='pro_ids']").attr("checked",this.checked);
	});
	$("input[name='pro_ids']").click(function(){
		if(!this.checked){
			$("#ck_all").attr("checked",this.checked);
		}
	});
});
