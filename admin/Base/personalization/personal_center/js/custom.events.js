$(function(){
    custom_event_type1=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_1").html(),//手机内容模板
            ctrl=$("#type_ctrl_1").html();//控制内容模板
            data.dom_ctrl=ctrldom;
			
			showSelectProduct(data);	//显示产品选择框
			
        //重新渲染数据
        var reRender=function(){
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type1($ctrl,data);
			//console.log(data.content.bg_color); 
			var title="";
			var pic="";
            var select_value="";
			var select_package_value="";
			var detail_name="";
			var detail_value="";
			var sel_link_type="";
			var link="";
			for(var i=0;i<data.content.dataset.length;i++)
            {
                title                   += data.content.dataset[i].title+"|";
				pic                     += data.content.dataset[i].pic+"|";
                select_value            += data.content.dataset[i].select_value+"|";
				detail_value	        += data.content.dataset[i].detail_value+"|";
				detail_name	  	        += data.content.dataset[i].detail_name+"|";
				sel_link_type	        += data.content.dataset[i].sel_link_type+"|";
				link	  		        += data.content.dataset[i].link+"|";
            }
			if(data.content.bg_color==""){
				data.content.bg_color="#ff0000";
			}
			update_mod1(customer_id,data.id,data.content.css_type,data.content.placeholder,title,pic,select_value,detail_value,detail_name,data.content.bg_color,data.content.padding,sel_link_type,link);
			
        }
        //改变显示方式
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();
            reRender();
        });
        //背景颜色改变
        $ctrl.find('.colorSelector').ColorPicker({
            onShow: function (colpkr) {
                $(colpkr).fadeIn(400);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(400);
                reRender();
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                $ctrl.find('.colorSelector div').css('backgroundColor', '#' + hex);
                $conitem.find('.con_display').css('backgroundColor', '#' + hex);
                data.content.bg_color='#' + hex;
            }
        });
        //主标题
        $ctrl.find("input[name='placeholder']").change(function() {
            var val = $(this).val();
            $conitem.find(".search-input").attr('placeholder',val);
            data.content.placeholder = val;
           // console.log(data.content.placeholder);
            reRender();
        }); 
         $ctrl.find("#slider").slider({
            min:0,
            max:50,
            step:1,
            animate: "fast",
            value:data.content.padding,
            slide:function(event,ui){
                $conitem.find(".con_display").css("padding-top",ui.value);
                $conitem.find(".con_display").css("padding-bottom",ui.value);
                $ctrl.find(".j-ctrl-showheight2").text(ui.value+"px");
            },
            stop:function(event,ui){
                data.content.padding=parseInt(ui.value);
				reRender();
            }
        });
		//搜索链接
		$ctrl.find(".search-input-btn").click(function(){
			var index = $(this).parents("li.ctrl-item-list-li").index();
			var search_val = $('#search_input_'+index).val();
			if( search_val == '' ){
				return false;
			}
			var type_id_2 = $(this).parent().find('#type_id_2');
			var options = $(this).parent().find('#type_id_2 option');
			options.each(function(i){
				if( options.eq(i).text() == search_val ){
					options.eq(i).attr('selected','selected');
					type_id_2.change();
					return false;
				}
			});
		});
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].detail_value="";
            data.content.dataset[index].select_value=$(this).val();
            var product_type = $(this).parent().find('select[name=product_type_2]');
            var room = $(this).parent().find('select[name=room_link]');
            if($(this).val() == -40){
                var product_type_val = product_type.val();
                product_type.show();
                data.content.dataset[index].select_value = product_type_val;
                changeProductType(data.content.dataset[index].select_value,index);
                // return;
            }else if($(this).val() == 'weishi'){
                var room_val = room.val();
                room.show();
                data.content.dataset[index].select_value = room_val;
            }else{
                product_type.hide();
                room.hide();
            }
            reRender();
        });

        //改变链接-礼包
        $ctrl.find("select[name='type_id_3']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].select_value=$(this).val();
            reRender();
        });
		//链接类型
		$ctrl.find(".sel_link_type").change(function(){
			var index=$(this).parents("li.ctrl-item-list-li").index();
			data.content.dataset[index].sel_link_type = $(this).val();
			data.content.dataset[index].link = $('#custom_link_'+index).val();
			reRender();
		});
		$ctrl.find("input[name='custom_link']").change(function(){
			var index=$(this).parents("li.ctrl-item-list-li").index();
			var reg=/^(http:\/\/|https:\/\/|HTTP:\/\/|HTTPS:\/\/).*$/; 
            if(!reg.test($(this).val())){
                _alert('提示信息：请输入以http://或https://开头正确的URL！');
			} else {
				data.content.dataset[index].link = $(this).val();
				reRender();
				_alert('提示信息：有效URL链接！');
			}
		});
		$ctrl.find("select[name='room_link']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
			data.content.dataset[index].detail_value = '';
			data.content.dataset[index].select_value = $(this).val();
            reRender();
        });
		$ctrl.find("select[name='product_type_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
			data.content.dataset[index].detail_value = '';
			data.content.dataset[index].select_value = $(this).val();
            reRender();
			changeProductType(data.content.dataset[index].select_value,index);
        });
         $ctrl.find("select[name='product_detail_id_2']").change(function(){
             var index=$(this).parents("li.ctrl-item-list-li").index();
             data.content.dataset[index].detail_value=$(this).val();
             data.content.dataset[index].detail_name=$(this).find("option:selected").text();
             reRender();
         });
         //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].pic=$(this).val();
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
             var index=$(this).parents("li.ctrl-item-list-li").index();
            // console.log($("#frm_img"+index));
                 $("#frm_img"+index).submit();
        });
        //改变标题
        $ctrl.find("input[name='title']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].title=$(this).val();
            var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
            if(patrn.test(data.content.dataset[index].title)){
                _alert('提示信息：您输入的数据含有非法字符！');
                data.content.dataset[index].title="";
            }
            reRender();
        });
    };
   
        

    custom_event_type7=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_7").html(),//手机内容模板
            ctrl=$("#type_ctrl_7").html();//控制内容模板
            data.dom_ctrl=ctrldom;
			
			showSelectProduct(data);	//显示产品选择框

        //重新渲染数据
        var reRender=function(){
            for(var i=0;i<data.content.dataset.length;i++)
            {
                data.content.dataset[i].mod_sort=i+1;
            }
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type7($ctrl,data);
			var title="";
			var pic="";
			var select_value="";
			var detail_name="";
			var detail_value="";
			var sel_link_type="";
			var link="";
			for(var i=0;i<data.content.dataset.length;i++)
            {
                title 			+= data.content.dataset[i].title+"|";
				pic	  			+= data.content.dataset[i].pic+"|";
				select_value	+= data.content.dataset[i].select_value+"|";
				detail_value	+= data.content.dataset[i].detail_value+"|";
				detail_name	  	+= data.content.dataset[i].detail_name+"|";
				sel_link_type	+= data.content.dataset[i].sel_link_type+"|";
				link	  		+= data.content.dataset[i].link+"|";
            }
            console.log(sel_link_type);
			update_mod7(customer_id,data.id,title,pic,select_value,detail_value,detail_name,data.content.padding,sel_link_type,link);
        }
        //改变标题
        $ctrl.find("input[name='title']").change(function(){
            data.content.dataset[0].title=$(this).val();
			var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
			if(patrn.test(data.content.dataset[0].title)){
				_alert("提示信息：您输入的数据含有非法字符！");	
				data.content.dataset[0].title="";
			}
            reRender();
        });
		//搜索链接
		$ctrl.find(".search-input-btn").click(function(){
			var index = $(this).parents("li.ctrl-item-list-li").index();
			var search_val = $('#search_input_'+index).val();
			if( search_val == '' ){
				return false;
			}
			var type_id_2 = $(this).parent().find('#type_id_2');
			var options = $(this).parent().find('#type_id_2 option');
			options.each(function(i){
				if( options.eq(i).text() == search_val ){
					options.eq(i).attr('selected','selected');
					type_id_2.change();
					return false;
				}
			});
		});
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].detail_value="";
            data.content.dataset[index].select_value=$(this).val();
			var product_type = $(this).parent().find('select[name=product_type_2]');
			var room = $(this).parent().find('select[name=room_link]');
			if($(this).val() == -40){
				var product_type_val = product_type.val();
				product_type.show();
				data.content.dataset[index].select_value = product_type_val;
				changeProductType(data.content.dataset[index].select_value,index);
				// return;
			}else if($(this).val() == 'weishi'){
				var room_val = room.val();
				room.show();
				data.content.dataset[index].select_value = room_val;
			}else{
				product_type.hide();
				room.hide();
			}
            reRender();
        });
        //改变链接-礼包
        $ctrl.find("select[name='type_id_3']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].select_value=$(this).val();
            reRender();
        });
		//链接类型
		$ctrl.find(".sel_link_type").change(function(){
			var index=$(this).parents("li.ctrl-item-list-li").index();
			data.content.dataset[index].sel_link_type = $(this).val();
			data.content.dataset[index].link = $('#custom_link_'+index).val();
			reRender();
		});
		$ctrl.find("input[name='custom_link']").change(function(){
			var index=$(this).parents("li.ctrl-item-list-li").index();
			var reg=/^(http:\/\/|https:\/\/|HTTP:\/\/|HTTPS:\/\/).*$/;
            if(!reg.test($(this).val())){
                _alert('提示信息：请输入以http://或https://开头正确的URL！');
			} else {
				data.content.dataset[index].link = $(this).val();
				reRender();
				_alert('提示信息：有效URL链接！');
			}
		});
		$ctrl.find("select[name='room_link']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
			data.content.dataset[index].detail_value = '';
			data.content.dataset[index].select_value = $(this).val();
            reRender();
        });
		$ctrl.find("select[name='product_type_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
			data.content.dataset[index].detail_value = '';
			data.content.dataset[index].select_value = $(this).val();
            reRender();
			changeProductType(data.content.dataset[index].select_value,index);
        });
         $ctrl.find("select[name='product_detail_id_2']").change(function(){
             var index=$(this).parents("li.ctrl-item-list-li").index();
             data.content.dataset[index].detail_value=$(this).val();
             data.content.dataset[index].detail_name=$(this).find("option:selected").text();
             reRender();
         });
         //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].pic=$(this).val();
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
             var index=$(this).parents("li.ctrl-item-list-li").index();
            // console.log($("#frm_img"+index));
                 $("#frm_img"+index).submit();
        });
        // 模块上下间距
        $ctrl.find("#slider").slider({
            min:0,
            max:50,
            step:1,
            animate: "fast",
            value:data.content.padding,
            slide:function(event,ui){
                $conitem.find(".con_display").css("padding-top",ui.value);
                $conitem.find(".con_display").css("padding-bottom",ui.value);
                $ctrl.find(".j-ctrl-showheight2").text(ui.value+"px");
            },
            stop:function(event,ui){
                data.content.padding=parseInt(ui.value);
                reRender();
            }
        });
     }
     

	 custom_event_type13=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_13").html(),//手机内容模板
            ctrl=$("#type_ctrl_13").html();//控制内容模板
            data.dom_ctrl=ctrldom;
			
			showSelectProduct(data);	//显示产品选择框
		//滚动方向
		/*if(data.content.rolling_direction == 1){
			$('#marquee'+data.id).kxbdSuperMarquee({
				isMarquee:true,
				direction:'left',
				isEqual:false,
				scrollDelay:data.content.rolling_speed
			});
		}else{
			$('#marquee'+data.id).kxbdSuperMarquee({
				direction:'up',
				isEqual:false,
				distance:40,
				time:data.content.show_time_limit
			});
		}*/
        //重新渲染数据
        var reRender=function(){
            for(var i=0;i<data.content.dataset.length;i++)
            {
                data.content.dataset[i].mod_sort=i+1;
            }
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type13($ctrl,data);
			var title = "";
            var select_value="";
			var detail_name="";
			var detail_value="";
			var sel_link_type="";
			var link="";
			for(var i=0;i<data.content.dataset.length;i++)
            {
                title 			+= data.content.dataset[i].title+"|";
				select_value	+= data.content.dataset[i].select_value+"|";
				detail_value	+= data.content.dataset[i].detail_value+"|";
				detail_name	  	+= data.content.dataset[i].detail_name+"|";	
				sel_link_type	+= data.content.dataset[i].sel_link_type+"|";
				link	  		+= data.content.dataset[i].link+"|";
				
            }
			if(data.content.rolling_direction == 1){
				$('#marquee'+data.id).kxbdSuperMarquee({
					isMarquee:true,
					direction:'left',
					isEqual:false,
					scrollDelay:data.content.rolling_speed
				});
			}else{
				$('#marquee'+data.id).kxbdSuperMarquee({
					direction:'up',
					isEqual:false,
					distance:40,
					time:data.content.show_time_limit
				});
			}
			// console.log(data.content.rolling_direction);
			// console.log(data.content.rolling_speed);
			// console.log(data.content.show_time_limit);
			update_mod13(customer_id,data.id,title,select_value,detail_value,detail_name,data.content.css_type,data.content.rolling_direction,data.content.rolling_speed,data.content.show_time_limit,data.content.padding,sel_link_type,link,data.content.icon_pic);
        }
		//改变显示方式
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();
            reRender();
        });
         //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
            var index=$(this).parents('li.up-icon').index()
            if(index==-1){
                data.content.icon_pic=$(this).val();
            }else{
                data.content.dataset[index].pic=$(this).val();
            }
            
            reRender();
        });
        
        var IME_status = false;  //输入法编辑器状态  true 开始  false 结束
        //字体数量监控
        $ctrl.find("input[name='title']").on('keyup', function(){
            console.log(IME_status)
            if (IME_status) {
                return;
            }
            
            var index = $(this).parents("li.ctrl-item-list-li").index();    //索引
            var textLength = $(this).val().length;
            if(textLength<51){
                data.content.dataset[index].text_length = textLength;
            var textContent = $(this).val();
            console.log(textContent);
            
            if (data.content.dataset[index].title == textContent) {
                return;
            } else {
                data.content.dataset[index].title = textContent;
            }
            
            reRender();
            $ctrl.find("input[name='title']").eq(index).val('').focus().val(textContent);
            }else{
                alert("不能超过50个字符！")
                $ctrl.find("input[name='title']").eq(index).val('').focus().val(data.content.dataset[index].title);
            }
                            
        }).on('compositionstart', function(){
            IME_status = true;
        }).on('compositionend', function(){
            IME_status = false;
        });
        
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
            var index=$(this).parents('li.up-icon').index();
            if(index==-1){
                $("#frm_img10").submit();
            }else{
                $("#frm_img"+index).submit();
            }
                
        });
		// 模块上下间距
        $ctrl.find("#slider").slider({
            min:0,
            max:50,
            step:1,
            animate: "fast",
            value:data.content.padding,
            slide:function(event,ui){
                $conitem.find(".con_display").css("padding-top",ui.value);
                $conitem.find(".con_display").css("padding-bottom",ui.value);
                $ctrl.find(".j-ctrl-showheight2").text(ui.value+"px");
            },
            stop:function(event,ui){
                data.content.padding=parseInt(ui.value);
				reRender();
            }
        });
		//改变滚动方向
        $ctrl.find("input[name='rolling_direction']").change(function(){
            data.content.rolling_direction=$(this).val();
            reRender();
        });
		// 改变滚动速度
        $ctrl.find("#slider_speed").slider({
            min:1,
            max:20,
            step:1,
            animate: "fast",
            value:data.content.rolling_speed,
            slide:function(event,ui){
                $ctrl.find(".j-ctrl-showheight2-speed").text(ui.value+'（数值越大速度越慢）');
            },
            stop:function(event,ui){
                data.content.rolling_speed=parseInt(ui.value);
				reRender();
            }
        });
		//改变每条公告显示时间
        $ctrl.find("input[name='show_time_limit']").change(function(){
			//验证用户输入内容
			//先把非数字的都替换掉，除了数字和.
			this.value = this.value.replace(/[^\d.]/g,"");
			//必须保证第一个为数字而不是.
			this.value = this.value.replace(/^\./g,"");
			//保证只有出现一个.而没有多个.
			this.value = this.value.replace(/\.{2,}/g,".");
			//保证.只出现一次，而不能出现两次以上
			this.value = this.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
			//只能输入两个小数
			this.value = this.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); 
			
			if( $(this).val() <= 0 || $(this).val() == '' ){
				_alert("提示信息：显示时间必须大于零！");
				$(this).val(1);
				return;
			}
            data.content.show_time_limit=$(this).val();
            reRender();
        });
        //改变公告
        /*$ctrl.find("input[name='title']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            var title_val=$(this).val();
			var title_len = title_val.length;
			if(data.content.rolling_direction == 1 && title_len>50){
				_alert("提示信息：公告内容不能超过50个字！");
				return;
			}
			var patrn=/[`#$^&*"{}\/[\]]/im;  
			if(patrn.test(title_val)){
				_alert("提示信息：您输入的数据含有非法字符！");	
				return;
			}
			data.content.dataset[index].title=title_val;
            reRender();
        });*/
		//搜索链接
		$ctrl.find(".search-input-btn").click(function(){
			var index = $(this).parents("li.ctrl-item-list-li").index();
			var search_val = $('#search_input_'+index).val();
			if( search_val == '' ){
				return false;
			}
			var type_id_2 = $(this).parent().find('#type_id_2');
			var options = $(this).parent().find('#type_id_2 option');
			options.each(function(i){
				if( options.eq(i).text() == search_val ){
					options.eq(i).attr('selected','selected');
					type_id_2.change();
					return false;
				}
			});
		});
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].detail_value="";
            data.content.dataset[index].select_value=$(this).val();
			var product_type = $(this).parent().find('select[name=product_type_2]');
			var room = $(this).parent().find('select[name=room_link]');
			if($(this).val() == -40){
				var product_type_val = product_type.val();
				product_type.show();
				data.content.dataset[index].select_value = product_type_val;
				changeProductType(data.content.dataset[index].select_value,index);
				// return;
			}else if($(this).val() == 'weishi'){
				var room_val = room.val();
				room.show();
				data.content.dataset[index].select_value = room_val;
			}else{
				product_type.hide();
				room.hide();
			}
            reRender();
        });
        //改变链接-礼包
        $ctrl.find("select[name='type_id_3']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].select_value=$(this).val();
            reRender();
        });
		//链接类型
		$ctrl.find(".sel_link_type").change(function(){
			var index=$(this).parents("li.ctrl-item-list-li").index();
			data.content.dataset[index].sel_link_type = $(this).val();
			data.content.dataset[index].link = $('#custom_link_'+index).val();
			reRender();
		});
		$ctrl.find("input[name='custom_link']").change(function(){
			var index=$(this).parents("li.ctrl-item-list-li").index();
			var reg=/^(http:\/\/|https:\/\/|HTTP:\/\/|HTTPS:\/\/).*$/;
            if(!reg.test($(this).val())){
                _alert('提示信息：请输入以http://或https://开头正确的URL！');
			} else {
				data.content.dataset[index].link = $(this).val();
				reRender();
				_alert('提示信息：有效URL链接！');
			}
		});
		$ctrl.find("select[name='room_link']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
			data.content.dataset[index].detail_value = '';
			data.content.dataset[index].select_value = $(this).val();
            reRender();
        });
		$ctrl.find("select[name='product_type_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
			data.content.dataset[index].detail_value = '';
			data.content.dataset[index].select_value = $(this).val();
            reRender();
			changeProductType(data.content.dataset[index].select_value,index);
        });
         $ctrl.find("select[name='product_detail_id_2']").change(function(){
             var index=$(this).parents("li.ctrl-item-list-li").index();
             data.content.dataset[index].detail_value=$(this).val();
             data.content.dataset[index].detail_name=$(this).find("option:selected").text();
             reRender();
         });

        //上移
        $ctrl.find(".j-moveup").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();

            if(index==0) return;//第一个导航不可再向上移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset.slice(index,index+1)[0];
            data.content.dataset.splice(index,1);
            data.content.dataset.splice(index-1,0,tmpdata);

            reRender();//更新视图
        });

        //下移
        $ctrl.find(".j-movedown").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index(),
                len=data.content.dataset.length;

            if(index==len-1) return;//最后一个导航不可再向下移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset.slice(index,index+1)[0];
            data.content.dataset.splice(index,1);
            data.content.dataset.splice(index+1,0,tmpdata);
            reRender();//更新视图
        });

        //添加
        $ctrl.find(".ctrl-item-list-add").click(function(){
            var new_sort=data.content.dataset.length+1;
            var newdata={
                    mod_sort:new_sort,
                    link:"",
                    title:"滚动公告栏",
                    color:"#000",
                    pic:"",
                    foreign_id:'',
                    detail_id:'',
                    select_value:"",
                    detail_value:'',
                    detail_name:"",
					sel_link_type:1,
                    text_length:5,
                };
            if(data.content.dataset.length<20){
                data.content.dataset.push(newdata);
                reRender();
            }
            else{
             _alert("公告不能超过20条！");
            }
        });

        //删除
        $ctrl.find(".j-del").click(function(){
            if(data.content.dataset.length>1){
                var index=$(this).parents("li.ctrl-item-list-li").index();
                data.content.dataset.splice(index,1);
                reRender();
            }
            else{
             _alert('再删就没拉');
            }
        });
    };
	
    //个人中心头部
	custom_event_type17=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_17").html();//手机内容模板
            ctrl=$("#type_ctrl_17").html();//控制内容模板
            data.dom_ctrl=ctrldom;

		//重新渲染数据
        var reRender=function(){
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            if(!(/^(\+|-)?\d+$/.test( data.content.pro_numshow )) || data.content.pro_numshow < 0){
                data.content.pro_numshow=2;
            }
            custom_event_type17($ctrl,data);
            var title		 = "";
			for(var i=0;i<data.content.dataset.length;i++)
            {
                title           += data.content.dataset[i].title+"|";
            }
			update_mod17(customer_id,data.id,data.content.padding,title,data.content.css_type,data.content.icon_pic);
        }
        
       //改变显示方式
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();
            reRender();
        });
        //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
			var index=$(this).parents('li.up-icon').index()
            if(index==-1){
                data.content.icon_pic=$(this).val();
            }else{
                data.content.dataset[index].pic=$(this).val();
            }
            
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
            var index=$(this).parents('li.up-icon').index();
            if(index==-1){
                $("#frm_img10").submit();
            }else{
                $("#frm_img"+index).submit();
            }
                
        });
        //删除图片
        $ctrl.find(".head-delete").click(function(){ 
            data.content.icon_pic = '';
            reRender();
        })

		// 模块上下间距
        $ctrl.find("#slider").slider({
            min:0,
            max:50,
            step:1,
            animate: "fast",
            value:data.content.padding,
            slide:function(event,ui){
                $conitem.find(".con_display").css("padding-top",ui.value);
                $conitem.find(".con_display").css("padding-bottom",ui.value);
                $ctrl.find(".j-ctrl-showheight2").text(ui.value+"px");
            },
            stop:function(event,ui){
                data.content.padding=parseInt(ui.value);
				reRender();
            }
        });
	}
	
	//订单显示
	custom_event_type21=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_21").html(),//手机内容模板
            ctrl=$("#type_ctrl_21").html();//控制内容模板
            data.dom_ctrl=ctrldom;

        //重新渲染数据
        var reRender=function(){
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            if(!(/^(\+|-)?\d+$/.test( data.content.pro_numshow )) || data.content.pro_numshow < 0){
                data.content.pro_numshow=2;
            }
            custom_event_type21($ctrl,data);
            var title="";
            var imgurl="";
            for(var i=0;i<data.content.dataset.length;i++)
            {
                title           += data.content.dataset[i].title+"|";
                imgurl             += data.content.dataset[i].pic+"|";
            }
			data.content.select_value = data.content.select_value;
            update_mod21(customer_id,data.id,data.content.css_type,title,imgurl,data.content.select_value,data.content.padding,data.content.li_title,data.content.icon_pic,data.content.css_show);   //还有cat_id
        } 
        //样式选择
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();
            reRender();
            console.log(data.content.select_value);
        });
       
        //选择分类
        $ctrl.find("select[name='type_id_2']").change(function(){
            data.content.select_value=$(this).val();
			
			data.content.li_title=$(this).val();
			switch($(this).val()){
				case '商城订单':
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case '大礼包订单':
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case '订餐订单':
				//	data.content.icon_pic = "/weixinpl/mshop/images/o2o/caterer.png";
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case '外卖订单':
				//	data.content.icon_pic = "/weixinpl/mshop/images/o2o/caterer_delivery.png";
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case 'KTV订单':
				//	data.content.icon_pic = "/weixinpl/mshop/images/o2o/ktv.png";
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case '酒店订单':
				//	data.content.icon_pic = "/weixinpl/mshop/images/o2o/hotel.png";
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case '线下商城-自提订单':
				//	data.content.icon_pic = "/weixinpl/mshop/images/o2o/cityshop_take.png";
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case '线下商城-配送订单':
				//	data.content.icon_pic = "/weixinpl/mshop/images/o2o/cityshop_delivery.png";
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";	
					break;
				case '线下商城-社区订单':
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case '金融订单':
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
					break;
				case '教练服务订单':
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case '线下收银订单':
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case '拼团订单':
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case '票务订单':
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";
                    break;
				case '到店付订单':
				//	data.content.icon_pic = "/weixinpl/mshop/images/o2o/pay.png";
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";	
                    break;
				case '艺人订单':
				//	data.content.icon_pic = "/weixinpl/mshop/images/o2o/pay.png";
					data.content.icon_pic = "/weixinpl/mshop/images/info_image/s_order.png";	
                    break;
			}
		//	if(data.content.css_type == 2){
				switch(data.content.select_value){
					case '商城订单':
					case '线下商城-配送订单':
						data.content.dataset = data.content.dataset0;
                       
						break;
					case '订餐订单':
						data.content.dataset = data.content.dataset1;
                        
						break;
					case '外卖订单':
					case '线下商城-社区订单':
						data.content.dataset = data.content.dataset2;
                        
						break;
					case 'KTV订单':
					case '酒店订单':
						data.content.dataset = data.content.dataset3;
                        
						break;
					case '线下商城-自提订单':
						data.content.dataset = data.content.dataset4;
                        
						break;
					case '金融订单':
						data.content.dataset = data.content.dataset5;
                        
						break;
					case '教练服务订单':
						data.content.dataset = data.content.dataset6;
                        
						break;
					case '线下收银订单':
						data.content.dataset = data.content.dataset7;
                        
						break;
					case '到店付订单':
						data.content.dataset = data.content.dataset8;
                        
						break;
					case '拼团订单':
						data.content.dataset = data.content.dataset9;
                        break;
					case '大礼包订单':
                        data.content.dataset = data.content.dataset9;
                        break;
					case '票务订单':
						data.content.dataset = data.content.dataset9;
						break;
					case '艺人订单':
						data.content.dataset = data.content.dataset10;
                        
						break;
				} 
		//	}
            reRender();
        });
        
         //选择分类
        $ctrl.find("input[name='title1']").change(function(){
            data.content.li_title=$(this).val();
            reRender();
        });
        
          //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
            var index=$(this).parents('li.up-icon').index()
            if(index==-1){
                data.content.icon_pic=$(this).val();
            }else{
                data.content.dataset[index].pic=$(this).val();
            }
            
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
            var index=$(this).parents('li.up-icon').index();
            if(index==-1){
                $("#frm_img10").submit();
            }else{
                $("#frm_img"+index).submit();
            }
                
        });



        // 模块上下间距
        $ctrl.find("#slider").slider({
            min:0,
            max:50,
            step:1,
            animate: "fast",
            value:data.content.padding,
            slide:function(event,ui){
                $conitem.find(".con_display").css("padding-top",ui.value);
                $conitem.find(".con_display").css("padding-bottom",ui.value);
                $ctrl.find(".j-ctrl-showheight2").text(ui.value+"px");
            },
            stop:function(event,ui){
                data.content.padding=parseInt(ui.value);
                reRender();
            }
        });
    };
	
	//数据显示
    custom_event_type22=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_22").html(),//手机内容模板
            ctrl=$("#type_ctrl_22").html();//控制内容模板
            data.dom_ctrl=ctrldom;
            
            showSelectProduct(data);    //显示产品选择框

        //重新渲染数据
        var reRender=function(){
			console.log(data.content.dataset);
            for(var i=0;i<data.content.dataset.length;i++)
            {
                data.content.dataset[i].mod_sort=i+1;
            }
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            console.log(data.content.data_num);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type22($ctrl,data);
            var title="";
            var pic="";
            var select_value="";
            var detail_name="";
            var detail_value="";
            var sel_link_type="";
            var link="";
            var color="";
            var color1="";
            var rs_member_id="";
            for(var i=0;i<data.content.dataset.length;i++)
            {
                
                title           += data.content.dataset[i].title+"|";
                pic             += data.content.dataset[i].pic+"|";
                select_value    += data.content.dataset[i].select_value+"|";
                detail_value    += data.content.dataset[i].detail_value+"|";
                detail_name     += data.content.dataset[i].detail_name+"|";
                sel_link_type   += data.content.dataset[i].sel_link_type+"|";
                link            += data.content.dataset[i].link+"|";
                color           += data.content.dataset[i].color+"|";
                color1          += data.content.dataset[i].color1+"|";
                rs_member_id    += data.content.dataset[i].member_id+"|";
            }
            
            // console.log(data);
            //console.log(select_value);
            //console.log(detail_value);
            update_mod22(customer_id,data.id,data.content.css_type,title,pic,select_value,detail_value,detail_name,data.content.margin,data.content.padding,sel_link_type,link,data.content.data_num,color,color1,data.content.dataset1,data.content.dataset2,rs_member_id);  //链接，图片
        }
        //改变标题
        $ctrl.find("input[name='title']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].title=$(this).val();
            var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
            if(patrn.test(data.content.dataset[index].title)){
                _alert("提示信息：您输入的数据含有非法字符！");   
                data.content.dataset[index].title="";
            }
			console.log(1);
            reRender();
        });
        //改变显示方式
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();
			
            if($(this).val()==1){
                data.content.dataset2=data.content.dataset;
                data.content.dataset=data.content.dataset1;
            }else if($(this).val()==2){
                data.content.dataset1=data.content.dataset;
                data.content.dataset=data.content.dataset2;
            }
            reRender();
        });
		
		//选择分类
        $ctrl.find("select[name='type_id_2']").change(function(){
			var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].select_value=$(this).val();
			data.content.dataset[index].title=$(this).val();
            
             if( $(this).val() == '积分单元总量' || $(this).val() == '单元转换率' || $(this).val() == '红积分总量' ){
                $(".member_cart_"+index).show(); 
                data.content.dataset[index].member_id=-1;
                if( $(this).val() == '单元转换率' ){
                    var temp_arr_22 =data.red_score_member_link[0].split("_");
                    data.content.dataset[index].member_id=temp_arr_22[0];
                }
            }else{
                data.content.dataset[index].member_id='';
                $(".member_cart_"+index).hide(); 
            }
            reRender();
        });

        //红积分会员卡选择
        $ctrl.find("select[name='member_cart_type']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].member_id=$(this).val();
            reRender();
           
        });
        

        $ctrl.find("input[name='data_num']").change(function(){
            var length=data.content.dataset.length;
            var num=$(this).val();
            data.content.data_num=num;
            if(length<num){
                for(var i=0;i<num-length;i++){
                    var new_sort=length+i+1;
                    var newdata={
                                mod_sort:new_sort,
                                link:"",
                                title:"零钱",
                                color:"#333",
                                color1:"#888",
                                pic:"/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png",
                                foreign_id:'',
                                detail_id:'',
                                select_value:"零钱",
                                detail_value:'',
                                detail_name:"",
                                sel_link_type:1
                            };
                    data.content.dataset.push(newdata);
                }
   
            }else{
                data.content.dataset.splice(num)
            }
            reRender();
        });
        // 模块上下间距
        $ctrl.find("#slider").slider({
            min:0,
            max:50,
            step:1,
            animate: "fast",
            value:data.content.padding,
            slide:function(event,ui){
                $conitem.find(".con_display").css("padding-top",ui.value);
                $conitem.find(".con_display").css("padding-bottom",ui.value);
                $ctrl.find(".j-ctrl-showheight2").text(ui.value+"px");
            },
            stop:function(event,ui){
                data.content.padding=parseInt(ui.value);
                reRender();
            }
        });
        
        
         //字体颜色改变
        $ctrl.find('.colorSelector').ColorPicker({
            onShow: function (colpkr) {
                 _this=$(this);
                $(colpkr).fadeIn(400);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(400);
                reRender();
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                var cl=_this.find("div").attr("color");
                // console.log(cl);
                if(cl=="color"){
                _this.find('div').css('backgroundColor', '#' + hex);
                var index=_this.parents("li.ctrl-item-list-li").index();
                data.content.dataset[index].color='#' + hex;                    
                }
                if(cl=="bg_color"){
                 _this.find('div').css('backgroundColor', '#' + hex);
                 data.content.bg_color='#' + hex;                      
                }
            }
        });
         //数字颜色改变
        $ctrl.find('.colorSelector1').ColorPicker({
            onShow: function (colpkr) {
                 _this=$(this);
                $(colpkr).fadeIn(400);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(400);
                reRender();
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                var cl=_this.find("div").attr("color");
                // console.log(cl);
                if(cl=="color"){
                _this.find('div').css('backgroundColor', '#' + hex);
                var index=_this.parents("li.ctrl-item-list-li").index();
                data.content.dataset[index].color1='#' + hex;                    
                }
                if(cl=="bg_color"){
                 _this.find('div').css('backgroundColor', '#' + hex);
                 data.content.bg_color1='#' + hex;                      
                }
            }
        });
         //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].pic=$(this).val();
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
             var index=$(this).parents("li.ctrl-item-list-li").index();
            // console.log($("#frm_img"+index));
                 $("#frm_img"+index).submit();
        })

        //上移
        $ctrl.find(".j-moveup").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();

            if(index==0) return;//第一个导航不可再向上移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset.slice(index,index+1)[0];
            data.content.dataset.splice(index,1);
            data.content.dataset.splice(index-1,0,tmpdata);

            reRender();//更新视图
        });

        //下移
        $ctrl.find(".j-movedown").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index(),
                len=data.content.dataset.length;

            if(index==len-1) return;//最后一个导航不可再向下移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset.slice(index,index+1)[0];
            data.content.dataset.splice(index,1);
            data.content.dataset.splice(index+1,0,tmpdata);
            reRender();//更新视图
        });

        //添加
        $ctrl.find(".ctrl-item-list-add").click(function(){
            var new_sort=data.content.dataset.length+1;
            var newdata={
                    mod_sort:new_sort,
                    link:"",
                    title:"零钱",
                    color:"#333",
                    color1:"#888",
                    pic:"/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png",
                    foreign_id:'',
                    detail_id:'',
                    select_value:"零钱",
                    detail_value:'',
                    detail_name:"",
                    sel_link_type:1
                };
            if(data.content.dataset.length<6){
                data.content.dataset.push(newdata);
                reRender();
            }
            else{
             _alert('请不要超过六张');
            }
        });
        //删除
        $ctrl.find(".j-del").click(function(){
            if(data.content.dataset.length>1){
                var index=$(this).parents("li.ctrl-item-list-li").index();
                data.content.dataset.splice(index,1);
                reRender();
            }
            else{
             _alert('再删就没拉');
            }
        });
    };
     
	 //功能模块
	 custom_event_type23=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_23").html(),//手机内容模板
            ctrl=$("#type_ctrl_23").html();//控制内容模板
            data.dom_ctrl=ctrldom;
            
            showSelectProduct(data);    //显示产品选择框
            
        //重新渲染数据
        var reRender=function(){
            for(var i=0;i<data.content.dataset.length;i++)
            {
                data.content.dataset[i].mod_sort=i+1;
            }
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type23($ctrl,data);
            var title="";
            var pic="";
            var select_value="";
            var detail_name="";
            var detail_value="";
            var color="";
            var sel_link_type="";
            var link="";
            for(var i=0;i<data.content.dataset.length;i++)
            {
                title           += data.content.dataset[i].title+"|";
                pic             += data.content.dataset[i].pic+"|";
                select_value    += data.content.dataset[i].select_value+"|";
                detail_value    += data.content.dataset[i].detail_value+"|";
                detail_name     += data.content.dataset[i].detail_name+"|";
                if(!(data.content.dataset[i].color)){
                    data.content.dataset[i].color="#000000";
                }
                color           += data.content.dataset[i].color+"|";
                sel_link_type   += data.content.dataset[i].sel_link_type+"|";
                link            += data.content.dataset[i].link+"|";
            }

            //console.log(color);
            update_mod23(customer_id,data.id,data.content.pro_title_show,title,pic,select_value,detail_value,detail_name,color,data.content.padding,sel_link_type,link,data.content.data_num,data.content.css_type);
        }
        //改变标题
        $ctrl.find("input[name='title']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].title=$(this).val();
            var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
            if(patrn.test(data.content.dataset[index].title)){
                _alert("提示信息：您输入的数据含有非法字符！");   
                data.content.dataset[index].title="";
            }
            reRender();
        });

        //改变显示方式
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();         
            reRender();
        });

        //改变每行个数
        $ctrl.find("input[name='data_num']").change(function(){
            data.content.data_num=$(this).val();         
            reRender();
        });

        // 模块上下间距
        $ctrl.find("#slider").slider({
            min:0,
            max:50,
            step:1,
            animate: "fast",
            value:data.content.padding,
            slide:function(event,ui){
                $conitem.find(".con_display").css("padding-top",ui.value);
                $conitem.find(".con_display").css("padding-bottom",ui.value);
                $ctrl.find(".j-ctrl-showheight2").text(ui.value+"px");
            },
            stop:function(event,ui){
                data.content.padding=parseInt(ui.value);
                reRender();
            }
        });
        
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
			data.content.dataset[index].select_value=$(this).val();
			
			/* 自定义功能 start */
			var temp_pic = '';						
			var temp_select_val = $(this).val();
			$.ajax({
				url:'/mshop/admin/index.php?m=personal_center&a=select_diy_function',
				data:{'customer_id':customer_id},
				type:'GET',
				async:false, 
				dataType: "json",		
				success : function(result) {
					$.each(result, function(idx, obj) {
						if(temp_select_val == obj[0]){
							temp_pic = obj[1];
						}
					});
				}
				
			});
			data.content.dataset[index].pic = temp_pic;
			/* 自定义功能 end */
            
			switch($(this).val()){
				case '我的资产':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_qianbao.png";
					break;
				case '我的特权':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_quanxian.png";
					break;
				case '我的团队':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_tuandui.png";
					break;
				case '累积收益':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_shouyi.png";
					break;
				case '收货地址':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_fahuodizhi.png";
					break;
				case '二维码':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_qrcode.png";
					break;
				case '我的微店':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_weidian.png";
					break;
				case '社区微店':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/wode_sprite.png";
					break;
				case '奖励报表':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_jiangli.png";
					break;
				case 'F2C店':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/f2c.png";
					break;
				case '我的赠送':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_zengsong.png";
					break;
				case '授权证书':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/authorization.png";
					break;
                case '赠送码':
                    data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/icon-zengsongma.png";
                    break;
				case '我的佣金':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/commission.png";
					break;
				case '我的慈善':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/charity.png";
					break;
				case '我的店铺':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_dianfu.png";
					break;
				case '店铺龙虎榜':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/dragon.png";
					break;
				case '修改邀请人':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/superior.png";
					break;
				case '推广排行榜':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/promoter_rank.png";
					break;
				case '头部引导':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/headguide.png";
					break;
				case '外卖配送':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/o_waimai.png";
					break;
				case '订货系统':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_sprite_06.png";
					break;
				case '我的导师':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_sprite_03.png";
					break;
				case '签到':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_sprite_09.png";
					break;
				case '安装预约':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/function.png";
					break;
				case '大礼包':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon2.png";
					break;
				case 'VP产品':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon1.png";
					break;
				case '积分专区':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon3.png";
					break;
				case '续费专区':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon4.png";
					break;
				case '电商直播':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon7.png";
					break;
				case '语音直播':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon8.png";
					break;
				case '拼团专区':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon5.png";
					break;
				case '限时抢购':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon6.png";
					break;
				case '特权专区':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon10.png";
					break;
				case '大转盘':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_zhuanpan.png";
					break;
				case '中奖纪录':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/wode_jilu.png";
					break;
                case '我的发布':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon10.png";
					break;
                case '社区动态':
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png";
					break;
				case '微信卡券':
					data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/vipcard.png";
					break;
				case '我的旅游卡':
                    data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/travel_card.png";
                    break;
                case '旅游卡办卡首页':
                    data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/travel_card.png";
                    break;
                case '云店':
                    data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/cloudshop.png";
                    break;
                case '持仓':
                    data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/chicang.png";
                    break;
                case '我的自选':
                    data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/chicang.png";
                    break;
                case '帮助中心':
                    data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/chicang.png";
                    break;
         
                case '我的名片':
                    data.content.dataset[index].pic = "/weixinpl/mshop/images/icon-bs-card.png";
                    break;
                case '我的彩铃':
                    data.content.dataset[index].pic = "/weixinpl/mshop/images/cl_logo.png";
                    break;
                case '我的订阅':
                    data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/mydeal.png";
                    break;
				default:
					data.content.dataset[index].pic = "/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png";
					break;
			}
			data.content.dataset[index].title=$(this).val();
			
            reRender();
        });
        
        
         //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].pic=$(this).val();
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
             var index=$(this).parents("li.ctrl-item-list-li").index();
            // console.log($("#frm_img"+index));
                 $("#frm_img"+index).submit();
        })
        //颜色改变
        $ctrl.find('.colorSelector').ColorPicker({
            onShow: function (colpkr) {
                 _this=$(this);
                $(colpkr).fadeIn(400);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(400);
                reRender();
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                _this.find('div').css('backgroundColor', '#' + hex);
                var index=_this.parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].color='#' + hex;
            }
        });

        //上移
        $ctrl.find(".j-moveup").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();

            if(index==0) return;//第一个导航不可再向上移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset.slice(index,index+1)[0];
            data.content.dataset.splice(index,1);
            data.content.dataset.splice(index-1,0,tmpdata);

            reRender();//更新视图
        });

        //下移
        $ctrl.find(".j-movedown").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index(),
                len=data.content.dataset.length;

            if(index==len-1) return;//最后一个导航不可再向下移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset.slice(index,index+1)[0];
            data.content.dataset.splice(index,1);
            data.content.dataset.splice(index+1,0,tmpdata);
            reRender();//更新视图
        });

        //添加
        $ctrl.find(".ctrl-item-list-add").click(function(){
            var new_sort=data.content.dataset.length+1;
            var newdata={
                    mod_sort:new_sort,
                    link:"",
                    title:"我的资产",
                    color:"#333",
                    pic:"/weixinpl/back_newshops/Base/personalization/personal_center/images/data-icon.png",
                    foreign_id:'',
                    detail_id:'',
                    select_value:"我的资产",
                    detail_value:'',
                    detail_name:"",
                    sel_link_type:1
                };
          
                data.content.dataset.push(newdata);
                reRender();
            
            
        });

        //删除
        $ctrl.find(".j-del").click(function(){
            if(data.content.dataset.length>1){
                var index=$(this).parents("li.ctrl-item-list-li").index();
                data.content.dataset.splice(index,1);
                reRender();
            }
            else{
             _alert('再删就没拉');
            }
        });
    };


    //功能模块
     custom_event_type24=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_24").html(),//手机内容模板
            ctrl=$("#type_ctrl_24").html();//控制内容模板
            data.dom_ctrl=ctrldom;
            
            showSelectProduct(data);    //显示产品选择框
            
        //重新渲染数据
        var reRender=function(){
            for(var i=0;i<data.content.dataset.length;i++)
            {
                data.content.dataset[i].mod_sort=i+1;
            }
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type24($ctrl,data);
            var title="";
            var pic="";
            var select_value="";
            var detail_name="";
            var detail_value="";
            var color="";
            var sel_link_type="";
            var link="";
            for(var i=0;i<data.content.dataset.length;i++)
            {
                title           += data.content.dataset[i].title+"|";
                pic             += data.content.dataset[i].pic+"|";
                select_value    += data.content.dataset[i].select_value+"|";
                detail_value    += data.content.dataset[i].detail_value+"|";
                detail_name     += data.content.dataset[i].detail_name+"|";
                if(!(data.content.dataset[i].color)){
                    data.content.dataset[i].color="#000000";
                }
                color           += data.content.dataset[i].color+"|";
                sel_link_type   += data.content.dataset[i].sel_link_type+"|";
                link            += data.content.dataset[i].link+"|";
            }

            //console.log(color);
            update_mod24(customer_id,data.id,data.content.pro_title_show,title,pic,select_value,detail_value,detail_name,color,data.content.padding,sel_link_type,link,data.content.data_num,data.content.css_type);
        }
        //改变标题
        $ctrl.find("input[name='title']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].title=$(this).val();
            var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
            if(patrn.test(data.content.dataset[index].title)){
                _alert("提示信息：您输入的数据含有非法字符！");   
                data.content.dataset[index].title="";
            }
            reRender();
        });

        //改变显示方式
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();         
            reRender();
        });

        //改变每行个数
        $ctrl.find("input[name='data_num']").change(function(){
            data.content.data_num=$(this).val();         
            reRender();
        });

        // 模块上下间距
        $ctrl.find("#slider").slider({
            min:0,
            max:50,
            step:1,
            animate: "fast",
            value:data.content.padding,
            slide:function(event,ui){
                $conitem.find(".con_display").css("padding-top",ui.value);
                $conitem.find(".con_display").css("padding-bottom",ui.value);
                $ctrl.find(".j-ctrl-showheight2").text(ui.value+"px");
            },
            stop:function(event,ui){
                data.content.padding=parseInt(ui.value);
                reRender();
            }
        });
        
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].select_value=$(this).val();

            data.content.dataset[index].pic = "/weixinpl/mshop/images/info_image/s_order.png";
            data.content.dataset[index].title=$(this).val();
            
            reRender();
        });
        
        
         //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].pic=$(this).val();
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
             var index=$(this).parents("li.ctrl-item-list-li").index();
            // console.log($("#frm_img"+index));
                 $("#frm_img"+index).submit();
        })
        //颜色改变
        $ctrl.find('.colorSelector').ColorPicker({
            onShow: function (colpkr) {
                 _this=$(this);
                $(colpkr).fadeIn(400);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(400);
                reRender();
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                _this.find('div').css('backgroundColor', '#' + hex);
                var index=_this.parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].color='#' + hex;
            }
        });

        //上移
        $ctrl.find(".j-moveup").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();

            if(index==0) return;//第一个导航不可再向上移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset.slice(index,index+1)[0];
            data.content.dataset.splice(index,1);
            data.content.dataset.splice(index-1,0,tmpdata);

            reRender();//更新视图
        });

        //下移
        $ctrl.find(".j-movedown").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index(),
                len=data.content.dataset.length;

            if(index==len-1) return;//最后一个导航不可再向下移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset.slice(index,index+1)[0];
            data.content.dataset.splice(index,1);
            data.content.dataset.splice(index+1,0,tmpdata);
            reRender();//更新视图
        });

        //添加
        $ctrl.find(".ctrl-item-list-add").click(function(){
            var new_sort=data.content.dataset.length+1;
            var newdata={
                    mod_sort:new_sort,
                    link:"",
                    title:"商城订单",
                    color:"#333",
                    pic:"/weixinpl/mshop/images/info_image/s_order.png",
                    foreign_id:'',
                    detail_id:'',
                    select_value:"商城订单",
                    detail_value:'',
                    detail_name:"",
                    sel_link_type:1
                };
          
                data.content.dataset.push(newdata);
                reRender();
            
            
        });

        //删除
        $ctrl.find(".j-del").click(function(){
            if(data.content.dataset.length>1){
                var index=$(this).parents("li.ctrl-item-list-li").index();
                data.content.dataset.splice(index,1);
                reRender();
            }
            else{
             _alert('再删就没拉');
            }
        });
    };
    
    

});
function update_mod1(customer_id,diy_tem_contid,css_type,placeholder,title,imgurl,select_value,detail_value,detail_name,search_color,mod_padding,sel_link_type,link){  //搜索更新模板

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"placeholder" : placeholder,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"search_color" : search_color,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod2(customer_id,diy_tem_contid,css_type,title,imgurl,select_value,detail_value,detail_name,mod_img_padding,mod_padding,sel_link_type,link){  //图片广告更新模板

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_img_padding" : mod_img_padding,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link},
			dataType: "json",		
			success : function(result) {
			//	console.log(result);
			}
		
		});
}
function update_mod3(customer_id,diy_tem_contid,pro_title_show,title,imgurl,select_value,detail_value,detail_name,color,mod_padding,sel_link_type,link){  //分类图标更新模板

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"pro_title_show" : pro_title_show,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"color" : color,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod4(customer_id,diy_tem_contid,css_type,title,imgurl,select_value,detail_value,detail_name,mod_padding,sel_link_type,link){  //橱窗三图
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}

function update_mod5(customer_id,diy_tem_contid,css_type,pro_numshow,pro_title_show,pro_title_twoline,show_sale,title,imgurl,foreign_id,mod_padding,shop_type,divide_type){  //分类产品更新模板

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"pro_numshow" : pro_numshow,"pro_title_show" : pro_title_show,"pro_title_twoline" : pro_title_twoline,"show_sale" : show_sale,"foreign_id" : foreign_id,"title" : title,"imgurl" : imgurl,"select_value" : foreign_id,"mod_padding" : mod_padding,"shop_type" : shop_type,"divide_type" : divide_type},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod21(customer_id,diy_tem_contid,css_type,title,imgurl,foreign_id,mod_padding,li_title,icon_pic,css_show){  //订单显示更新模板

    var op="update_mod";
    $.ajax({  
            type : "POST",  
            url : "/mshop/admin/index.php?m=personal_center&a=save_template_content",
            data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"foreign_id" : foreign_id,"title" : title,"imgurl" : imgurl,"select_value" : foreign_id,"mod_padding" : mod_padding,'li_title':li_title,'icon_pic':icon_pic,'css_show':css_show},
            dataType: "json",       
            success : function(result) {
            //  console.log(result.msg);
            }
        
        });
}
function update_mod22(customer_id,diy_tem_contid,css_type,title,imgurl,select_value,detail_value,detail_name,mod_img_padding,mod_padding,sel_link_type,link,data_num,color,color1,dataset1,dataset2,rs_member_id){  //图片广告更新模板

    var op="update_mod";
    $.ajax({  
            type : "POST",  
            url : "/mshop/admin/index.php?m=personal_center&a=save_template_content",
            data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_img_padding" : mod_img_padding,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link,'data_num':data_num,'color':color,'color1':color1,'dataset1':dataset1,'dataset2':dataset2,'rs_member_id':rs_member_id},
            dataType: "json",       
            success : function(result) {
            //  console.log(result);
            }
        
        });
}
function update_mod23(customer_id,diy_tem_contid,pro_title_show,title,imgurl,select_value,detail_value,detail_name,color,mod_padding,sel_link_type,link,data_num,css_type){  //分类图标更新模板

    var op="update_mod";
    $.ajax({  
            type : "POST",  
            url : "/mshop/admin/index.php?m=personal_center&a=save_template_content",
            data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"pro_title_show" : pro_title_show,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"color" : color,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link,'data_num':data_num,'css_type':css_type},
            dataType: "json",       
            success : function(result) {
            //  console.log(result.msg);
            }
        
        });
}

function update_mod24(customer_id,diy_tem_contid,pro_title_show,title,imgurl,select_value,detail_value,detail_name,color,mod_padding,sel_link_type,link,data_num,css_type){  //分类图标更新模板

    var op="update_mod";
    $.ajax({  
            type : "POST",  
            url : "/mshop/admin/index.php?m=personal_center&a=save_template_content",
            data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"color" : color,"mod_padding" : mod_padding,'css_type':css_type},
            dataType: "json",       
            success : function(result) {
            //  console.log(result.msg);
            }
        
        });
}

function update_mod7(customer_id,diy_tem_contid,title,imgurl,select_value,detail_value,detail_name,mod_padding,sel_link_type,link){  //分割线
	
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "/mshop/admin/index.php?m=personal_center&a=save_template_content",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}



function update_mod13(customer_id,diy_tem_contid,title,select_value,detail_value,detail_name,css_type,rolling_direction,rolling_speed,show_time_limit,mod_padding,sel_link_type,link,icon_pic){  //滚动公告栏

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "/mshop/admin/index.php?m=personal_center&a=save_template_content",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"css_type" : css_type,"rolling_direction" : rolling_direction,"rolling_speed" : rolling_speed,"show_time_limit" : show_time_limit,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link,"icon_pic" : icon_pic},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
				
			}
		
		});
}


function update_mod17(customer_id,diy_tem_contid,mod_padding,title,css_type,icon_pic){  //个人中心头部

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "/mshop/admin/index.php?m=personal_center&a=save_template_content",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"mod_padding" : mod_padding,"css_type":css_type,"title" : title,"icon_pic" : icon_pic},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			
			}
		
		});
}


function _alert(content){
    layer.alert(content,{shade: 0,shift:0});
}

function showSelectProduct(data){	//显示产品选择框
	if( data.type == 1 && ( data.content.css_type == 1 || data.content.css_type == 4 ) ){	//搜索栏样式1和样式4不需要链接
		return;
	}

	for( var i=0; i<data.content.dataset.length; i++ ){	
		if( data.content.dataset[i].select_value != '' && data.content.dataset[i].select_value != undefined && data.content.dataset[i].select_value != -1 ){
			selv=data.content.dataset[i].select_value.split("_");
			product_type_linktype=selv[1];
			product_type_val=selv[0];
			
			if( product_type_linktype==1 && product_type_val>0 ){
				changeProductType(data.content.dataset[i].select_value,i,data.content.dataset[i].detail_value);
			}
		}
	}
}

