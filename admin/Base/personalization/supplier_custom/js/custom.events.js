$(function(){
    custom_event_type1=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_1").html(),//手机内容模板
            ctrl=$("#type_ctrl_1").html();//控制内容模板
            data.dom_ctrl=ctrldom;
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
			var detail_name="";
			var detail_value="";
			for(var i=0;i<data.content.dataset.length;i++)
            {
                title 			+= data.content.dataset[i].title+"|";
				pic	  			+= data.content.dataset[i].pic+"|";
				select_value	+= data.content.dataset[i].select_value+"|";
				detail_value	+= data.content.dataset[i].detail_value+"|";
				detail_name	  	+= data.content.dataset[i].detail_name+"|";
            }
			if(data.content.bg_color==""){
				data.content.bg_color="#ff0000";
			}
			update_mod1(customer_id,supplier_id,data.id,data.content.css_type,data.content.placeholder,title,pic,select_value,detail_value,detail_name,data.content.bg_color,data.content.padding);
			
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
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].detail_value="";
            data.content.dataset[index].select_value=$(this).val();
            reRender();
            changeProductType($(this).val(),index);
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
			for(var i=0;i<data.content.dataset.length;i++)
            {
				
                title 			+= data.content.dataset[i].title+"|";
				pic	  			+= data.content.dataset[i].pic+"|";
				select_value	+= data.content.dataset[i].select_value+"|";
				detail_value	+= data.content.dataset[i].detail_value+"|";
				detail_name	  	+= data.content.dataset[i].detail_name+"|";
            }
			
			//console.log(data);
			//console.log(select_value);
			//console.log(detail_value);
			update_mod2(customer_id,supplier_id,data.id,data.content.css_type,title,pic,select_value,detail_value,detail_name,data.content.margin,data.content.padding);  //链接，图片
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
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].detail_value="";
            data.content.dataset[index].select_value=$(this).val();
            reRender();
            changeProductType($(this).val(),index);
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
                    detail_name:""
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
            }
			//console.log(color);
			update_mod3(customer_id,supplier_id,data.id,data.content.pro_title_show,title,pic,select_value,detail_value,detail_name,color,data.content.padding);
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
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].detail_value="";
            data.content.dataset[index].select_value=$(this).val();
            reRender();
            changeProductType($(this).val(),index);
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
                    detail_name:""
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
			for(var i=0;i<data.content.dataset.length;i++)
            {
                title 			+= data.content.dataset[i].title+"|";
				pic	  			+= data.content.dataset[i].pic+"|";
				select_value	+= data.content.dataset[i].select_value+"|";
				detail_value	+= data.content.dataset[i].detail_value+"|";
				detail_name	  	+= data.content.dataset[i].detail_name+"|";
            }
			update_mod4(customer_id,supplier_id,data.id,data.content.css_type,title,pic,select_value,detail_value,detail_name,data.content.padding); 
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
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].detail_value="";
            data.content.dataset[index].select_value=$(this).val();
            reRender();
            changeProductType($(this).val(),index);
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

        //自定义链接
        $ctrl.find("input[name='customlink']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].link=$(this).val();
        });
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
			update_mod5(customer_id,supplier_id,data.id,data.content.css_type,data.content.pro_numshow,data.content.pro_title_show,data.content.pro_title_twoline,data.content.show_sale,title,imgurl,data.content.dataset[0].select_value,data.content.padding);  //还有cat_id
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
			}else if(data.content.pro_numshow > 20){
				_alert("数量不宜大于20");
				data.content.pro_numshow=20;
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
            }
			
			update_mod6(customer_id,supplier_id,data.id,data.content.pro_title_show,title,pic,select_value,detail_value,detail_name,data.content.foot_position,color,data.content.padding);   //还有foot_position
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
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].detail_value="";
            data.content.dataset[index].select_value=$(this).val();
            reRender();
            changeProductType($(this).val(),index);
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
                _this.find('div').css('backgroundColor', '#' + hex);
                var index=_this.parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].color='#' + hex;
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
                    detail_name:""
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
			for(var i=0;i<data.content.dataset.length;i++)
            {
                title 			+= data.content.dataset[i].title+"|";
				pic	  			+= data.content.dataset[i].pic+"|";
				select_value	+= data.content.dataset[i].select_value+"|";
				detail_value	+= data.content.dataset[i].detail_value+"|";
				detail_name	  	+= data.content.dataset[i].detail_name+"|";
            }
			update_mod7(customer_id,supplier_id,data.id,title,pic,select_value,detail_value,detail_name,data.content.padding);
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
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].detail_value="";
            data.content.dataset[index].select_value=$(this).val();
            reRender();
            changeProductType($(this).val(),index);
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
			for(var i=0;i<data.content.dataset.length;i++)
            {
                title 			+= data.content.dataset[i].title+"|";
				pic	  			+= data.content.dataset[i].pic+"|";
				select_value	+= data.content.dataset[i].select_value+"|";
				detail_value	+= data.content.dataset[i].detail_value+"|";
				detail_name	  	+= data.content.dataset[i].detail_name+"|";
            }
			update_mod8(customer_id,supplier_id,data.id,data.content.css_type,title,pic,select_value,detail_value,detail_name,data.content.padding); 
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
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].detail_value="";
            data.content.dataset[index].select_value=$(this).val();
            reRender();
            changeProductType($(this).val(),index);
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
            for(var i=0;i<data.content.dataset.length;i++)
            {
                title           += data.content.dataset[i].title+"|";
                pic             += data.content.dataset[i].pic+"|";
                select_value    += data.content.dataset[i].select_value+"|";
                detail_value    += data.content.dataset[i].detail_value+"|";
                detail_name     += data.content.dataset[i].detail_name+"|";
            }
			update_mod9(customer_id,supplier_id,data.id,data.content.css_type,title,pic,select_value,detail_value,detail_name,data.content.padding); 
            
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
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].detail_value="";
            data.content.dataset[index].select_value=$(this).val();
            reRender();
            changeProductType($(this).val(),index);
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
			
			update_mod10(customer_id,supplier_id,data.id,data.content.video_link,data.content.padding); 
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
		//	console.log(result3);
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
     }
});
function update_mod1(customer_id,supplier_id,diy_tem_contid,css_type,placeholder,title,imgurl,select_value,detail_value,detail_name,search_color,mod_padding){  //搜索更新模板

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"placeholder" : placeholder,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"search_color" : search_color,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod2(customer_id,supplier_id,diy_tem_contid,css_type,title,imgurl,select_value,detail_value,detail_name,mod_img_padding,mod_padding){  //图片广告更新模板

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_img_padding" : mod_img_padding,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result);
			}
		
		});
}
function update_mod3(customer_id,supplier_id,diy_tem_contid,pro_title_show,title,imgurl,select_value,detail_value,detail_name,color,mod_padding){  //分类图标更新模板

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_tem_contid" : diy_tem_contid,"pro_title_show" : pro_title_show,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"color" : color,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod4(customer_id,supplier_id,diy_tem_contid,css_type,title,imgurl,select_value,detail_value,detail_name,mod_padding){  //橱窗三图
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}

function update_mod5(customer_id,supplier_id,diy_tem_contid,css_type,pro_numshow,pro_title_show,pro_title_twoline,show_sale,title,imgurl,foreign_id,mod_padding){  //分类产品更新模板

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"pro_numshow" : pro_numshow,"pro_title_show" : pro_title_show,"pro_title_twoline" : pro_title_twoline,"show_sale" : show_sale,"foreign_id" : foreign_id,"title" : title,"imgurl" : imgurl,"select_value" : foreign_id,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod6(customer_id,supplier_id,diy_tem_contid,pro_title_show,title,imgurl,select_value,detail_value,detail_name,foot_position,color,mod_padding){  //底部菜单	
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_tem_contid" : diy_tem_contid,"pro_title_show" : pro_title_show,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"foot_position" : foot_position,"color" : color,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod7(customer_id,supplier_id,diy_tem_contid,title,imgurl,select_value,detail_value,detail_name,mod_padding){  //分割线
	
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod8(customer_id,supplier_id,diy_tem_contid,css_type,title,imgurl,select_value,detail_value,detail_name,mod_padding){  //橱窗四图
	
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}

function update_mod9(customer_id,supplier_id,diy_tem_contid,css_type,title,imgurl,select_value,detail_value,detail_name,mod_padding){  //橱窗二图
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"title" : title,"imgurl" : imgurl,"select_value" : select_value,"detail_value" : detail_value,"detail_name" : detail_name,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}

function update_mod10(customer_id,supplier_id,diy_tem_contid,video_link,mod_padding){  //视频添加
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_tem_contid" : diy_tem_contid,"video_link" : video_link,"mod_padding" : mod_padding},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function _alert(content){
    layer.alert(content,{shade: 0,shift:0,time:1500});
}

function showSelectProduct(data){	//显示产品选择框
	for( var i=0; i<data.content.dataset.length; i++ ){	
		if( data.content.dataset[i].select_value != '' && data.content.dataset[i].select_value != undefined && data.content.dataset[i].select_value != -1 ){
			selv=data.content.dataset[i].select_value.split("_");
			product_type_linktype=selv[1];
			product_type_val=selv[0];
			if( product_type_linktype==1 ){
				changeProductType(data.content.dataset[i].select_value,i,data.content.dataset[i].detail_value);
			}
		}
	}
}
