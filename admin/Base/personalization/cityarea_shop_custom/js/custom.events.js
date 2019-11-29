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
                _alert('提示信息：请输入以//或https://开头正确的URL！');
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
    custom_event_type2=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_2").html(),//手机内容模板
            ctrl=$("#type_ctrl_2").html();//控制内容模板
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
            custom_event_type2($ctrl,data);
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
			
			// console.log(data);
			//console.log(select_value);
			//console.log(detail_value);
			update_mod2(customer_id,data.id,data.content.css_type,title,pic,select_value,detail_value,detail_name,data.content.margin,data.content.padding,sel_link_type,link);  //链接，图片
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
        //为非滚动图片时的上下距离
        $ctrl.find("#slider-i").slider({
             min:0,
             max:20,
             step:1,
            animate: "fast",
            value:data.content.margin,
            slide:function(event,ui){
                 $conitem.find(".members_imgad ul li").css("margin-bottom",ui.value);
               $ctrl.find(".j-ctrl-showheight-i").text(ui.value+"px");
             },
            stop:function(event,ui){
                 data.content.margin=parseInt(ui.value);
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
        //改变链接-分类选择
        $ctrl.find("select[name='type_id_4']").change(function(){
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
                _alert('提示信息：请输入以//或https://开头正确的URL！');
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
                    title:"",
                    color:"#000",
                    pic:"images/img1.jpg",
                    foreign_id:'',
                    detail_id:'',
                    select_value:"",
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
        custom_event_type3=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_3").html(),//手机内容模板
            ctrl=$("#type_ctrl_3").html();//控制内容模板
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
            custom_event_type3($ctrl,data);
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
                title 			+= data.content.dataset[i].title+"|";
				pic	  			+= data.content.dataset[i].pic+"|";
				select_value	+= data.content.dataset[i].select_value+"|";
				detail_value	+= data.content.dataset[i].detail_value+"|";
				detail_name	  	+= data.content.dataset[i].detail_name+"|";
				if(!(data.content.dataset[i].color)){
					data.content.dataset[i].color="#000000";
				}
				color	  		+= data.content.dataset[i].color+"|";
				sel_link_type	+= data.content.dataset[i].sel_link_type+"|";
				link	  		+= data.content.dataset[i].link+"|";
            }
			//console.log(color);
			update_mod3(customer_id,data.id,data.content.pro_title_show,title,pic,select_value,detail_value,detail_name,color,data.content.padding,sel_link_type,link);
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
        //改变链接-分类选择
        $ctrl.find("select[name='type_id_4']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].select_value=$(this).val();
            console.log(data.content.dataset)
            reRender();
        });
        $ctrl.find(".ex").click(function(){
            var index=$(this).parents().parents().parents().parents().index();
            if (data.content.dataset[index].select_value == '') {
                data.content.dataset[index].select_value = 'ex_2';
            }
            console.log(data.content.dataset)
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
                _alert('提示信息：请输入以//或https://开头正确的URL！');
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
        //是否显示标题
        $ctrl.find("input[name='pro_title_show']").change(function(){
            data.content.pro_title_show=$(this).val();
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
                    title:"",
                    color:"#000",
                    pic:"images/icon01.png",
                    foreign_id:'',
                    detail_id:'',
                    select_value:"",
                    detail_value:'',
                    detail_name:"",
					sel_link_type:1
                };
            if(data.content.dataset.length<5){
                data.content.dataset.push(newdata);
                reRender();
            }
            else{
             _alert("分类图标一行不能超过5个");
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

    custom_event_type4=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_4").html(),//手机内容模板
            ctrl=$("#type_ctrl_4").html();//控制内容模板
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
            custom_event_type4($ctrl,data);
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
			update_mod4(customer_id,data.id,data.content.css_type,title,pic,select_value,detail_value,detail_name,data.content.padding,sel_link_type,link); 
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
        //改变链接-分类选择
        $ctrl.find("select[name='type_id_4']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].select_value=$(this).val();
            console.log(data.content.dataset)
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
                _alert('提示信息：请输入以//或https://开头正确的URL！');
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
        })
    };
    custom_event_type5=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_5").html(),//手机内容模板
            ctrl=$("#type_ctrl_5").html();//控制内容模板
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
            custom_event_type5($ctrl,data);
			var title="|";
			var imgurl="|";
			update_mod5(customer_id,data.id,data.content.css_type,data.content.pro_numshow,data.content.pro_title_show,data.content.pro_title_twoline,data.content.show_sale,title,imgurl,data.content.dataset[0].select_value,data.content.padding);  //还有cat_id
        }
        //布局方式
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();
            reRender();
        });
        //是否显示标题
        $ctrl.find("input[name='pro_title_show']").change(function(){
            data.content.pro_title_show=$(this).val();
            reRender();
        });
        //商品数量
        $ctrl.find("input[name='pro_numshow']").change(function(){
            data.content.pro_numshow=$(this).val();
			if(!(/^(\+|-)?\d+$/.test( data.content.pro_numshow )) || data.content.pro_numshow < 0 ){
				_alert("输入数量有误，请重新输入");
				data.content.pro_numshow=2;
			}
            reRender();
        });
        //选择分类
        $ctrl.find("select[name='type_id_2']").change(function(){
            data.content.dataset[0].select_value=$(this).val();
            reRender();
        });
         //两行显示
        $ctrl.find("input[name='pro_title_twoline']").click(function(){
            if($(this).attr("checked")=="checked"){
                 $(this).removeAttr("checked");
                 data.content.pro_title_twoline=0;
            }else{
                 $(this).attr("checked","checked");
                data.content.pro_title_twoline=1;
            }
            reRender();
        });
        //是否显示销量
        $ctrl.find("input[name='show_sale']").change(function(){
            data.content.show_sale=$(this).val();
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
    };
    custom_event_type6=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_6").html(),//手机内容模板
            ctrl=$("#type_ctrl_6").html();//控制内容模板
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
            custom_event_type6($ctrl,data);
			
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
                title 			+= data.content.dataset[i].title+"|";
				pic	  			+= data.content.dataset[i].pic+"|";
				select_value	+= data.content.dataset[i].select_value+"|";
				detail_value	+= data.content.dataset[i].detail_value+"|";
				detail_name	  	+= data.content.dataset[i].detail_name+"|";
				if(!(data.content.dataset[i].color)){
					data.content.dataset[i].color="#000000";
				}
				color	  		+= data.content.dataset[i].color+"|";
				sel_link_type	+= data.content.dataset[i].sel_link_type+"|";
				link	  		+= data.content.dataset[i].link+"|";
            }
			
			update_mod6(customer_id,data.id,data.content.pro_title_show,title,pic,select_value,detail_value,detail_name,data.content.foot_position,color,data.content.padding,data.content.bg_color,sel_link_type,link);   //还有foot_position
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
        //改变链接-分类选择
        $ctrl.find("select[name='type_id_4']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].select_value=$(this).val();
            console.log(data.content.dataset)
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
                _alert('提示信息：请输入以//或https://开头正确的URL！');
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
        })
        //是否显示标题
        $ctrl.find("input[name='pro_title_show']").change(function(){
            data.content.pro_title_show=$(this).val();
            reRender();
        });
        //是否固定位置
        $ctrl.find("input[name='foot_position']").change(function(){
            data.content.foot_position=$(this).val();
            reRender();
        });
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
                    title:"",
                    color:"#000",
                    pic:"images/i1.jpg",
                    foreign_id:'',
                    detail_id:'',
                    select_value:"",
                    detail_value:'',
                    detail_name:"",
					sel_link_type:1
                };
            if(data.content.dataset.length<5){
                data.content.dataset.push(newdata);
                reRender();
            }
            else{
             _alert('底部菜单一行不要超过五个');
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
                _alert('提示信息：请输入以//或https://开头正确的URL！');
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
     //橱窗四图
         custom_event_type8=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_8").html(),//手机内容模板
            ctrl=$("#type_ctrl_8").html();//控制内容模板
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
            custom_event_type8($ctrl,data);
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
			update_mod8(customer_id,data.id,data.content.css_type,title,pic,select_value,detail_value,detail_name,data.content.padding,sel_link_type,link); 
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
        //改变链接-分类选择
        $ctrl.find("select[name='type_id_4']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].select_value=$(this).val();
            console.log(data.content.dataset)
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
                _alert('提示信息：请输入以//或https://开头正确的URL！');
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
    };
     //橱窗二图
         custom_event_type9=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_9").html(),//手机内容模板
            ctrl=$("#type_ctrl_9").html();//控制内容模板
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
            custom_event_type9($ctrl,data);
            var title="";
            var pic="";
            var select_value="";
            var detail_name="";
            var detail_value="";
			var sel_link_type="";
			var link="";
            for(var i=0;i<data.content.dataset.length;i++)
            {
                title           += data.content.dataset[i].title+"|";
                pic             += data.content.dataset[i].pic+"|";
                select_value    += data.content.dataset[i].select_value+"|";
                detail_value    += data.content.dataset[i].detail_value+"|";
                detail_name     += data.content.dataset[i].detail_name+"|";
				sel_link_type	+= data.content.dataset[i].sel_link_type+"|";
				link	  		+= data.content.dataset[i].link+"|";
            }
			update_mod9(customer_id,data.id,data.content.css_type,title,pic,select_value,detail_value,detail_name,data.content.padding,sel_link_type,link); 
            
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
        //改变链接-分类选择
        $ctrl.find("select[name='type_id_4']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].select_value=$(this).val();
            console.log(data.content.dataset)
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
                _alert('提示信息：请输入以//或https://开头正确的URL！');
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
    };
    custom_event_type10=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_10").html(),//手机内容模板
            ctrl=$("#type_ctrl_10").html();//控制内容模板
            data.dom_ctrl=ctrldom;

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
            custom_event_type10($ctrl,data);
			
			update_mod10(customer_id,data.id,data.content.video_link,data.content.padding); 
        }
        //主标题
        $ctrl.find("input[name='video_link']").change(function() {
            var val = $(this).val();
            data.content.video_link = val;
			//检测视频地址  
			result = data.content.video_link.indexOf("http");
			result2 = data.content.video_link.indexOf("<iframe");
			result3 = data.content.video_link.indexOf("swf");
            result4 = data.content.video_link.indexOf("</iframe>");
			// console.log(result3);
			if(result!=0 || result2==0 || result3>0|| result4>0){
				_alert("请输入正确的视频地址");
				data.content.video_link="";
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
     };
	 custom_event_type11=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_11").html(),//手机内容模板
            ctrl=$("#type_ctrl_11").html();//控制内容模板
            data.dom_ctrl=ctrldom;

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
            custom_event_type11($ctrl,data);
			
			var title="";
			var pic="";
			for(var i=0;i<data.content.dataset.length;i++)
            {
				title 	+= data.content.dataset[i].title+"|";
				pic		+= data.content.dataset[i].pic+"|";
            }
			
			update_mod11(customer_id,data.id,data.content.css_type,data.content.bg_color,data.content.color,pic,title,data.content.placeholder,data.content.padding); 
        }
		//改变显示方式
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();
            reRender();
        });
		//主标题
        $ctrl.find("input[name='placeholder']").change(function() {
            var val = $(this).val();
            $conitem.find(".search-input").attr('placeholder',val);
            data.content.placeholder = val;
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
		//背景颜色改变
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
                data.content.color='#' + hex;                    
                }
                if(cl=="bg_color"){
                 _this.find('div').css('backgroundColor', '#' + hex);
                 data.content.bg_color='#' + hex;                      
                }
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
		//删除
        $ctrl.find(".j-del").click(function(){
            if(data.content.dataset.length>0){
                var index=$(this).parents("li.ctrl-item-list-li").index();
                data.content.dataset[index].pic='';
                reRender();
            }
        });
     };
	 custom_event_type12=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_12").html(),//手机内容模板
            ctrl=$("#type_ctrl_12").html();//控制内容模板
            data.dom_ctrl=ctrldom;
			//省市选择
			ctrl_address(data.areaData,"location_p"+data.id,"location_c"+data.id,"",data.content.location_p,data.content.city_name,'');
			
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
            custom_event_type12($ctrl,data);
			var title="";
			var pic="";
			var select_value="";
			var detail_name="";
			var detail_value="";
			var start_time="";
			var end_time="";
			var sel_link_type="";
			var link="";
			
			for(var i=0;i<data.content.dataset.length;i++)
            {
				
                title 			+= data.content.dataset[i].title+"|";
				pic	  			+= data.content.dataset[i].pic+"|";
				select_value	+= data.content.dataset[i].select_value+"|";
				detail_value	+= data.content.dataset[i].detail_value+"|";
				detail_name	  	+= data.content.dataset[i].detail_name+"|";
				start_time	  	+= data.content.dataset[i].start_time+"|";
				end_time	  	+= data.content.dataset[i].end_time+"|";
				sel_link_type	+= data.content.dataset[i].sel_link_type+"|";
				link	  		+= data.content.dataset[i].link+"|";
            }
			
			update_mod12(customer_id,data.id,data.content.css_type,title,pic,select_value,detail_value,detail_name,data.content.margin,data.content.padding,data.content.city_name,start_time,end_time,data.content.location_p,sel_link_type,link);  //链接，图片
        }
		
        //改变显示方式
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();
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
        //为非滚动图片时的上下距离
        $ctrl.find("#slider-i").slider({
             min:0,
             max:20,
             step:1,
            animate: "fast",
            value:data.content.margin,
            slide:function(event,ui){
                 $conitem.find(".members_imgad ul li").css("margin-bottom",ui.value);
               $ctrl.find(".j-ctrl-showheight-i").text(ui.value+"px");
             },
            stop:function(event,ui){
                 data.content.margin=parseInt(ui.value);
				 reRender();
             }
         });
		 //改变省
        $ctrl.find("select[name='location_p']").change(function(){
            data.content.location_p=$(this).val();
			var num=data.areaData.length;//获取数组长度
			for(var i=0;i<num;i++){			
				if(data.areaData[i].LevelType==2&&data.areaData[i].MergerName.match(data.content.location_p)){//LevelType==2代表市
					data.content.city_name=data.areaData[i].name;
				}
			}
			reRender();
        });
		 //改变城市
        $ctrl.find("select[name='city_name']").change(function(){
            data.content.city_name=$(this).val();
			reRender();
			ctrl_address(data.areaData,"location_p"+data.id,"location_c"+data.id,"",data.content.location_p,data.content.city_name,'');
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
                _alert('提示信息：请输入以//或https://开头正确的URL！');
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
			$("#frm_img"+index).submit();
        });
		//改变时间
        $ctrl.find("input[name='start_time']").focus(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
			var endtime=$dp.$('endtime'+index);
			WdatePicker({onpicked:function(){
				// endtime.focus();
				data.content.dataset[index].start_time=$(this).val();
				reRender();
				},oncleared:function(){
					data.content.dataset[index].start_time=$(this).val();
					reRender();
				},readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'endtime'+index+'\')}'
			})
        });
		
		//改变时间
        $ctrl.find("input[name='end_time']").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
			var starttime=$dp.$('starttime'+index);
			WdatePicker({onpicked:function(){
				// endtime.focus();
				data.content.dataset[index].end_time=$(this).val();
				reRender();
				},oncleared:function(){
					data.content.dataset[index].end_time=$(this).val();
					reRender();
				},readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'starttime'+index+'\')}'
			})
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
					title:"",
					color:"#000",
					pic:"images/img5.jpg",
					foreign_id:'',
					detail_id:'',
					select_value:"",
					detail_value:'',
					detail_name:"",
					start_time:"",
					end_time:"",
					sel_link_type:1
				};
			if(data.content.dataset.length<5){
				data.content.dataset.push(newdata);
				reRender();
			}
			else{
				_alert('请不要超过五张');
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
			var title="";
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
			update_mod13(customer_id,data.id,title,select_value,detail_value,detail_name,data.content.css_type,data.content.rolling_direction,data.content.rolling_speed,data.content.show_time_limit,data.content.padding,sel_link_type,link);
        }
		//改变显示方式
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();
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
        $ctrl.find("input[name='title']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            var title_val=$(this).val();
			var title_len = title_val.length;
			if(data.content.rolling_direction == 1 && title_len>27){
				_alert("提示信息：公告内容不能超过27个字！");
				return;
			}
			var patrn=/[`#$^&*"{}\/[\]]/im;  
			if(patrn.test(title_val)){
				_alert("提示信息：您输入的数据含有非法字符！");	
				return;
			}
			data.content.dataset[index].title=title_val;
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
			var reg=/^(()http:\/\/|https:\/\/|HTTP:\/\/|HTTPS:\/\/).*$/;
            if(!reg.test($(this).val())){
                _alert('提示信息：请输入以//或https://开头正确的URL！');
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
                    title:"",
                    color:"#000",
                    pic:"",
                    foreign_id:'',
                    detail_id:'',
                    select_value:"",
                    detail_value:'',
                    detail_name:"",
					sel_link_type:1
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
	//头部引导页
	custom_event_type14=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_14").html();//手机内容模板
            ctrl=$("#type_ctrl_14").html();//控制内容模板
            data.dom_ctrl=ctrldom;
			
		//重新渲染数据
        var reRender=function(){
			update_mod14(customer_id,data.id,data.content.padding);
        }
		
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

    custom_event_type15=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_15").html(),//手机内容模板
            ctrl=$("#type_ctrl_15").html();//控制内容模板
            data.dom_ctrl=ctrldom;
            
            showSelectProduct(data);    //显示产品选择框
            
        //重新渲染数据
        var reRender=function(){
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type15($ctrl,data);
            //console.log(data.content.bg_color); 
            var title="";
            var pic="";
            var select_value="";
            var detail_name="";
            var detail_value="";
            var sel_link_type="";
            var link="";
            // console.log(data.content.dataset[0].pic)
            for(var i=0;i<data.content.dataset.length;i++)
            {
                title           += data.content.dataset[i].title+"|";
                pic             += data.content.dataset[i].pic+"|";
                select_value    += data.content.dataset[i].select_value+"|";
                detail_value    += data.content.dataset[i].detail_value+"|";
                detail_name     += data.content.dataset[i].detail_name+"|";
                sel_link_type   += data.content.dataset[i].sel_link_type+"|";
                link            += data.content.dataset[i].link+"|";
            }
            if(data.content.bg_color==""){
                data.content.bg_color="#ff0000";
            }
            update_mod15(customer_id,data.id,data.content.color,data.content.bg_color,data.content.placeholder,pic,title,data.content.padding,data.content.pro_title_show)
        }
        //背景颜色改变
        $ctrl.find('#text_color').ColorPicker({
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
                $ctrl.find('.search-input').css('color', '#' + hex);
                $conitem.find('.con_display').css('color', '#' + hex);
                data.content.color='#' + hex;
            }
        });

        $ctrl.find('#bg_color').ColorPicker({
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
                $ctrl.find('.bg_color div').css('backgroundColor', '#' + hex);
                $conitem.find('.con_display').css('backgroundColor', '#' + hex);
                data.content.bg_color='#' + hex;
            }
        });
        //主标题
        $ctrl.find("input[name='placeholder']").change(function() {
            var val = $(this).val();
            $conitem.find(".search-input").attr('text',val);
            data.content.placeholder = val;
           // console.log(data.content.placeholder);
            reRender();
        }); 

        //图片样式
        $ctrl.find("input[name='pro_title_show']").change(function(){
            data.content.pro_title_show=$(this).val();
            reRender();
        });

        $ctrl.find("#slider").slider({
            min:0,
            max:50,
            step:1,
            animate: "fast",
            value:data.content.padding,
            slide:function(event,ui){
                console.log(ui.value)
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
                _alert('提示信息：请输入以//或https://开头正确的URL！');
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

    // 联系电话
    custom_event_type20=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_20").html(),//手机内容模板
            ctrl=$("#type_ctrl_20").html();//控制内容模板
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
            custom_event_type20($ctrl,data);
            var title="";
            var pic="";
            var select_value="";
            var detail_name="";
            var detail_value="";
            var sel_link_type="";
            var link="";
            for(var i=0;i<data.content.dataset.length;i++)
            {
                title           += data.content.dataset[i].title+"|";
                pic             += data.content.dataset[i].pic+"|";
                select_value    += data.content.dataset[i].select_value+"|";
                detail_value    += data.content.dataset[i].detail_value+"|";
                detail_name     += data.content.dataset[i].detail_name+"|";
                sel_link_type   += data.content.dataset[i].sel_link_type+"|";
                link            += data.content.dataset[i].link+"|";
            }
            update_mod20(customer_id,data.id,title,pic,select_value,detail_value,detail_name,data.content.padding,sel_link_type,link);
        }
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

    // 定位地址
    custom_event_type21=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_21").html(),//手机内容模板
            ctrl=$("#type_ctrl_21").html();//控制内容模板
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
            custom_event_type21($ctrl,data);
            var title="";
            var pic="";
            var select_value="";
            var detail_name="";
            var detail_value="";
            var sel_link_type="";
            var link="";
            for(var i=0;i<data.content.dataset.length;i++)
            {
                title           += data.content.dataset[i].title+"|";
                pic             += data.content.dataset[i].pic+"|";
                select_value    += data.content.dataset[i].select_value+"|";
                detail_value    += data.content.dataset[i].detail_value+"|";
                detail_name     += data.content.dataset[i].detail_name+"|";
                sel_link_type   += data.content.dataset[i].sel_link_type+"|";
                link            += data.content.dataset[i].link+"|";
            }
            update_mod21(customer_id,data.id,title,pic,select_value,detail_value,detail_name,data.content.padding,sel_link_type,link);
        }
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

    // 店铺顶部信息
    custom_event_type22=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_22").html(),//手机内容模板
            ctrl=$("#type_ctrl_22").html();//控制内容模板
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
            custom_event_type22($ctrl,data);
            var title="";
            var pic="";
            var select_value="";
            var detail_name="";
            var detail_value="";
            var sel_link_type="";
            var link="";
            for(var i=0;i<data.content.dataset.length;i++)
            {
                title           += data.content.dataset[i].title+"|";
                pic             += data.content.dataset[i].pic+"|";
                select_value    += data.content.dataset[i].select_value+"|";
                detail_value    += data.content.dataset[i].detail_value+"|";
                detail_name     += data.content.dataset[i].detail_name+"|";
                sel_link_type   += data.content.dataset[i].sel_link_type+"|";
                link            += data.content.dataset[i].link+"|";
            }
            update_mod22(customer_id,data.id,title,pic,select_value,detail_value,detail_name,data.content.padding,sel_link_type,link);
        }
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

function update_mod5(customer_id,diy_tem_contid,css_type,pro_numshow,pro_title_show,pro_title_twoline,show_sale,title,imgurl,foreign_id,mod_padding){  //分类产品更新模板

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"pro_numshow" : pro_numshow,"pro_title_show" : pro_title_show,"pro_title_twoline" : pro_title_twoline,"show_sale" : show_sale,"foreign_id" : foreign_id,"title" : title,"imgurl" : imgurl,"select_value" : foreign_id,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod6(customer_id,diy_tem_contid,pro_title_show,title,imgurl,select_value,detail_value,detail_name,foot_position,color,mod_padding,bg_color,sel_link_type,link){  //底部菜单	
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"pro_title_show" : pro_title_show,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"foot_position" : foot_position,"color" : color,"mod_padding" : mod_padding,"search_color" : bg_color,"sel_link_type" : sel_link_type,"link" : link},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod7(customer_id,diy_tem_contid,title,imgurl,select_value,detail_value,detail_name,mod_padding,sel_link_type,link){  //分割线
	
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod8(customer_id,diy_tem_contid,css_type,title,imgurl,select_value,detail_value,detail_name,mod_padding,sel_link_type,link){  //橱窗四图
	
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

function update_mod9(customer_id,diy_tem_contid,css_type,title,imgurl,select_value,detail_value,detail_name,mod_padding,sel_link_type,link){  //橱窗二图
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

function update_mod10(customer_id,diy_tem_contid,video_link,mod_padding){  //视频添加
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"video_link" : video_link,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}

function update_mod11(customer_id,diy_tem_contid,css_type,bg_color,color,imgurl,title,placeholder,mod_padding){  //LBS定位
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"css_type" : css_type,"diy_tem_contid" : diy_tem_contid,"search_color" : bg_color,"color" : color,"imgurl" : imgurl,"title" : title,"placeholder" : placeholder,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}

function update_mod12(customer_id,diy_tem_contid,css_type,title,imgurl,select_value,detail_value,detail_name,mod_img_padding,mod_padding,city_name,start_time,end_time,province,sel_link_type,link){  //LBS城市广告

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_img_padding" : mod_img_padding,"mod_padding" : mod_padding,"city_name" : city_name,"start_time" : start_time,"end_time" : end_time,"province" : province,"sel_link_type" : sel_link_type,"link" : link},
			dataType: "json",		
			success : function(result) {
			//	console.log(result);
			}
		
		});
}

function update_mod13(customer_id,diy_tem_contid,title,select_value,detail_value,detail_name,css_type,rolling_direction,rolling_speed,show_time_limit,mod_padding,sel_link_type,link){  //滚动公告栏

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"css_type" : css_type,"rolling_direction" : rolling_direction,"rolling_speed" : rolling_speed,"show_time_limit" : show_time_limit,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
				
			}
		
		});
}

function update_mod14(customer_id,diy_tem_contid,mod_padding){  //头部引导页

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			
			}
		
		});
}

function update_mod15(customer_id,diy_tem_contid,color,search_color,placeholder,imgurl,title,mod_padding,pro_title_show){  //搜索更新模板
    var op="update_mod";
    $.ajax({  
            type : "POST",  
            url : "save_model.php",
            data : {
                "model_type" : 15,
                "op" : op,
                "customer_id" : customer_id,
                "diy_tem_contid" : diy_tem_contid,
                "color" : color,
                "search_color" : search_color,
                "placeholder" : placeholder,
                "mod_padding" : mod_padding,
                "title" : title,
                "pro_title_show" : pro_title_show,
                "imgurl" : imgurl
            },
            dataType: "json",       
            success : function(result) {
                alert(result)
            //  console.log(result.msg);
            }
        
        });
}

// 联系电话
function update_mod20(customer_id,diy_tem_contid,title,imgurl,select_value,detail_value,detail_name,mod_padding,sel_link_type,link){
    
    var op="update_mod";
    $.ajax({  
            type : "POST",  
            url : "save_model.php",
            data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link},
            dataType: "json",       
            success : function(result) {
            //  console.log(result.msg);
            }
        
        });
}

// 定位地址
function update_mod21(customer_id,diy_tem_contid,title,imgurl,select_value,detail_value,detail_name,mod_padding,sel_link_type,link){  
    
    var op="update_mod";
    $.ajax({  
            type : "POST",  
            url : "save_model.php",
            data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link},
            dataType: "json",       
            success : function(result) {
            //  console.log(result.msg);
            }
        
        });
}

// 店铺顶部信息
function update_mod22(customer_id,diy_tem_contid,title,imgurl,select_value,detail_value,detail_name,mod_padding,sel_link_type,link){  
    
    var op="update_mod";
    $.ajax({  
            type : "POST",  
            url : "save_model.php",
            data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_padding" : mod_padding,"sel_link_type" : sel_link_type,"link" : link},
            dataType: "json",       
            success : function(result) {
            //  console.log(result.msg);
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
